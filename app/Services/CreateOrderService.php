<?php

namespace App\Services;

use App\Enums\TransferRequestStatusEnum;
use App\Enums\TransferRequestTypeEnum;
use App\Http\Resources\App\TransferRequest\TransferRequestResource;
use App\Jobs\SyncPurchaseOrderJob;
use App\Models\TransferRequest;
use App\Services\Steps\CreateOrder\AcquireLockStep;
use App\Services\Steps\CreateOrder\BuildDataStep;
use App\Services\Steps\CreateOrder\CallApiStep;
use App\Services\Steps\CreateOrder\ValidateStep;
use App\Support\Pipeline;
use App\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreateOrderService
{
    use Responses;

    protected Pipeline $pipeline;

    public function __construct()
    {
        $this->pipeline = new Pipeline();
    }

    public function create(TransferRequest $transferRequest, Request $request): JsonResponse
    {
        $payload = (object) [
            'transferRequest' => $transferRequest,
            'request' => $request,
            'cashier' => null,
            'apiData' => [],
            'apiResponse' => null,
        ];

        // Step 1: Validate and build data
        $result = $this->pipeline
            ->send($payload)
            ->through([
                ValidateStep::class,
                BuildDataStep::class,
            ])
            ->thenReturn();

        if ($result instanceof JsonResponse) {
            return $result;
        }

        // Step 2: Acquire lock + Call API
        $apiResult = $this->pipeline
            ->send($result)
            ->through([
                AcquireLockStep::class,
                CallApiStep::class,
            ])
            ->thenReturn();

        if ($apiResult instanceof JsonResponse) {
            return $apiResult;
        }

        // Step 3: Handle response and update transfer request
        return $this->handleApiResponse($transferRequest, $apiResult->apiResponse);
    }

    private function handleApiResponse(TransferRequest $transferRequest, array $apiResponse): JsonResponse
    {
        if ($transferRequest->type === TransferRequestTypeEnum::TransferIN->value) {
            $purchaseOrderNumber = sprintf(
                '%05d_%05d_%s',
                $transferRequest->other_store_id,
                $transferRequest->store_id,
                $apiResponse['poNumber']
            );

            SyncPurchaseOrderJob::dispatch($transferRequest->id, $purchaseOrderNumber)
                ->delay(now()->addMinutes(3));

            $purchaseOrderId = null;
        } else {
            $purchaseOrderId = $apiResponse['id'] ?? null;
        }

        $transferRequest->update([
            'status' => TransferRequestStatusEnum::CLOSED,
            'purchase_order_id' => $purchaseOrderId,
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Transfer request status updated successfully. PO sync job dispatched.',
            data: new TransferRequestResource($transferRequest)
        );
    }
}
