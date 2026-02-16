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
        \Illuminate\Support\Facades\Log::info("Starting commit process for Purchase Order", [
            'purchase_order_id' => $purchaseOrder->ID,
            'po_number' => $purchaseOrder->PONumber,
            'po_type' => $purchaseOrder->POType,
            'store_id' => $purchaseOrder->StoreID,
            'other_store_id' => $purchaseOrder->OtherStoreID,
        ]);

        $payload = (object) [
            'purchaseOrder' => $purchaseOrder,
            'request' => $request,
            'cashier' => null,
            'poTypeEnum' => null,
            'orderData' => [],
            'apiResponse' => null,
        ];

        $result = $this->pipeline
            ->send($payload)
            ->through([
                ValidateStep::class,
                BuildOrderDataStep::class,
                CommitToApiStep::class,
            ])
            ->thenReturn();

        if ($result instanceof JsonResponse) {
            \Illuminate\Support\Facades\Log::warning("Commit process returned error response for Purchase Order #{$purchaseOrder->ID}");
            return $result;
        }

        \Illuminate\Support\Facades\Log::info("Commit to API successful, dispatching PurchaseOrderCommitted event for PO #{$purchaseOrder->ID}");

        PurchaseOrderCommitted::dispatch($purchaseOrder);

        $purchaseOrder->load(['condition', 'entries', 'entries.infos']);

        \Illuminate\Support\Facades\Log::info("Purchase Order committed successfully", [
            'purchase_order_id' => $purchaseOrder->ID,
            'po_number' => $purchaseOrder->PONumber,
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase Order Committed Successfully',
            data: new \App\Http\Resources\App\Offline\PurchaseOrderResource($purchaseOrder),
        );
    }
}
