<?php

namespace App\Services\Commit;

use App\Enums\PurchaseOrderTypeEnum;
use App\Http\Resources\App\Offline\PurchaseOrderResource;
use App\Jobs\Commit\GeneratePdfJob;
use App\Jobs\Commit\PrintJob;
use App\Jobs\Commit\SendEmailsJob;
use App\Models\PurchaseOrder;
use App\Services\Commit\Strategies\ReturnToSupplierStrategy;
use App\Services\Commit\Contracts\CommitApiClient;
use App\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * The commit server. Orchestrates the rebuilt commit flow using a strategy
 * resolved by the factory, and a client for the external system of record.
 * There is a single commit action per order — for types that carry an
 * `isClosed` field (TransferIn/Supplier), that field alone decides whether
 * the order ends up partially committed or fully closed; there is no
 * separate "partial" mode or endpoint.
 */
class CommitService
{
    use Responses;

    public function __construct(
        private StrategyFactory $strategies,
        private CommitApiClient $api,
    ) {}

    public function commit(PurchaseOrder $order, Request $request): JsonResponse
    {
        try {
            $ctx = $this->prepare($order, $request);   // resolve strategy + validate
            $this->commitUnderLock($ctx);               // guard -> build -> gateway
        } catch (ValidationException $e) {
            return $this->error(
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
                message: $e->validator->errors()->first()
            );
        } catch (CommitException $e) {
            return $this->error(status: $e->httpStatus, message: $e->getMessage());
        }

        $this->dispatchPostCommit($order->refresh());

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase Order Committed Successfully',
            data: new PurchaseOrderResource(
                $order->load(['condition', 'entries', 'entries.infos'])
            ),
        );
    }

    /**
     * Resolve the strategy, run type-agnostic preflight guards, then validate
     * the request against the chosen strategy's rules.
     */
    private function prepare(PurchaseOrder $order, Request $request): CommitContext
    {
        $cashier = $request->user()?->cashier
            ?? throw new CommitException('Cashier not found', Response::HTTP_NOT_FOUND);

        $type = PurchaseOrderTypeEnum::tryFrom((int) $order->POType)
            ?? throw new CommitException('Invalid purchase order type', Response::HTTP_BAD_REQUEST);

        $strategy = $request->input('transactionType') === 'ReturnToSupplier'
            ? app(ReturnToSupplierStrategy::class)
            : $this->strategies->for($type);

        $ctx = new CommitContext(
            order: $order,
            request: $request,
            cashier: $cashier,
            storeId: (int) DB::table('Configuration')->value('StoreID'),
            apiType: $type->apiTransactionType(),
            strategy: $strategy,
        );

        // Validate for its guard effect (throws ValidationException on failure).
        // Payloads read the raw request, so the validated array isn't retained.
        $validator = $strategy->validator();
        validator(
            $request->all(),
            $validator->rules(),
            $validator->messages()
        )->validate();

        return $ctx;
    }

    /**
     * The single concurrency window: acquire the lock, re-read status to reject
     * an already-committed order, build the payload, and call the gateway.
     */
    private function commitUnderLock(CommitContext $ctx): void
    {
        $lock = Cache::lock("po_commit_{$ctx->order->ID}", 60);

        if (!$lock->get()) {
            throw new CommitException(
                'This order is already being processed. Please wait.',
                Response::HTTP_CONFLICT
            );
        }

        try {
            $this->guardNotAlreadyCommitted($ctx);

            $this->api->commit(
                $ctx->strategy->endpoint($ctx),
                $ctx->strategy->buildPayload($ctx)
            );
        } finally {
            $lock->release();
        }
    }

    /**
     * Idempotency guard. The external API owns `status` (0 open, 1 partial,
     * 2 closed); we only read it. A closed order is never re-committed.
     */
    private function guardNotAlreadyCommitted(CommitContext $ctx): void
    {
        $status = (int) PurchaseOrder::whereKey($ctx->order->ID)->value('status');

        if ($status === 2) {
            throw new CommitException(
                'Purchase order is already committed',
                Response::HTTP_CONFLICT
            );
        }
    }

    /**
     * The post-commit saga: PDF -> email -> print, in order. The chain stops
     * if a link fails (so no email/print without a PDF); the failure is logged.
     */
    private function dispatchPostCommit(PurchaseOrder $order): void
    {
        Bus::chain([
            new GeneratePdfJob($order),
            new SendEmailsJob($order),
            new PrintJob($order),
        ])->catch(function (\Throwable $e) use ($order) {
            Log::error("PO {$order->ID} post-commit chain failed", ['error' => $e->getMessage()]);
        })->dispatch();

        $this->drainQueue();
    }

    /**
     * Kick a one-shot background worker to drain the queued chain immediately.
     * Production has no persistent worker, so we spawn `queue:work --stop-when-empty`
     * per request (mirrors the legacy flow). Each chained job enqueues the next
     * before it finishes, so --stop-when-empty drains the full chain in order.
     */
    private function drainQueue(): void
    {
        $cwd = getcwd();
        chdir(base_path());

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                pclose(popen('start /B php artisan queue:work --stop-when-empty', 'r'));
            } else {
                exec('php artisan queue:work --stop-when-empty > /dev/null 2>&1 &');
            }
        } finally {
            chdir($cwd);
        }
    }
}
