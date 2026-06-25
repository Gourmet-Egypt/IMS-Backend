<?php

namespace App\Services;

use App\Events\PurchaseOrderCommitted;
use App\Models\PurchaseOrder;
use App\Services\Steps\CommitOrder\AcquireLockStep;
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
            $lockedPurchaseOrder = PurchaseOrder::where('ID', $purchaseOrder->ID)
                ->lockForUpdate()
                ->first();

            if (!$lockedPurchaseOrder) {
                return $this->error(
                    status: Response::HTTP_NOT_FOUND,
                    message: 'Purchase Order not found'
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
                ])
                ->thenReturn();
        });

        if ($result instanceof JsonResponse) {
            return $result;
        }

        $apiResult = $this->pipeline
            ->send($result)
            ->through([
                AcquireLockStep::class,
                CommitToApiStep::class,
            ])
            ->thenReturn();

        if ($apiResult instanceof JsonResponse) {
            return $apiResult;
        }


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
