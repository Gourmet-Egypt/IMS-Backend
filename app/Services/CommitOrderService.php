<?php

namespace App\Services;

use App\Events\PurchaseOrderCommitted;
use App\Models\PurchaseOrder;
use App\Services\Steps\CommitOrder\BuildOrderDataStep;
use App\Services\Steps\CommitOrder\CommitToApiStep;
use App\Services\Steps\CommitOrder\ValidateStep;
use App\Support\Pipeline;
use App\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CommitOrderService
{
    use Responses;

    protected Pipeline $pipeline;

    public function __construct()
    {
        $this->pipeline = new Pipeline();
    }

    public function commit(PurchaseOrder $purchaseOrder, Request $request): JsonResponse
    {
        $isPartial = $request->route()->getActionMethod() === 'partialCommitOrder';

        $result = DB::transaction(function () use ($purchaseOrder, $request, $isPartial) {
            // Lock the purchase order row to prevent race conditions
            $lockedPurchaseOrder = PurchaseOrder::where('ID', $purchaseOrder->ID)
                ->lockForUpdate()
                ->first();

            if (!$lockedPurchaseOrder) {
                return $this->error(
                    status: Response::HTTP_NOT_FOUND,
                    message: 'Purchase Order not found'
                );
            }

            // Idempotency check: prevent double commit
            // Status 2 = closed (fully committed)
            if (!$isPartial && $lockedPurchaseOrder->Status == 2) {
                return $this->error(
                    status: Response::HTTP_CONFLICT,
                    message: 'Purchase Order has already been committed'
                );
            }

            $payload = (object) [
                'purchaseOrder' => $lockedPurchaseOrder,
                'request' => $request,
                'cashier' => null,
                'poTypeEnum' => null,
                'orderData' => [],
                'apiResponse' => null,
                'isPartial' => $isPartial,
            ];

            return $this->pipeline
                ->send($payload)
                ->through([
                    ValidateStep::class,
                    BuildOrderDataStep::class,
                    CommitToApiStep::class,
                ])
                ->thenReturn();
        });

        // A step failed and returned a response — stop here.
        if ($result instanceof JsonResponse) {
            return $result;
        }

        // ---- Everything below runs ONLY after the transaction has committed ----

        PurchaseOrderCommitted::dispatch($purchaseOrder);

        $this->startQueueWorker();

        $purchaseOrder->refresh()->load(['condition', 'entries', 'entries.infos']);

        $message = $isPartial
            ? 'Purchase Order Partial Committed Successfully'
            : 'Purchase Order Committed Successfully';

        return $this->success(
            status: Response::HTTP_OK,
            message: $message,
            data: new \App\Http\Resources\App\Offline\PurchaseOrderResource($purchaseOrder),
        );
    }

    private function startQueueWorker(): void
    {
        $cwd = getcwd();
        chdir(base_path());

        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen('start /B php artisan queue:work --stop-when-empty', 'r'));
        } else {
            exec('php artisan queue:work --stop-when-empty > /dev/null 2>&1 &');
        }

        chdir($cwd);
    }
}
