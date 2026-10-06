<?php

namespace App\Services;

use App\Enums\TransferRequestStatusEnum;
use App\Enums\TransferRequestTypeEnum;
use App\Http\Resources\App\TransferRequest\TransferRequestResource;
use App\Models\TransferRequest;
use App\Services\CreateOrder\StrategyFactory;
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

    public function __construct(private StrategyFactory $strategies)
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

        return $this->handleApiResponse($transferRequest, $apiResult->apiResponse);
    }

    private function handleApiResponse(TransferRequest $transferRequest, array $apiResponse): JsonResponse
    {
        $type = TransferRequestTypeEnum::from($transferRequest->type);
        $strategy = $this->strategies->for($type);

        $transferRequest->update([
            'status' => TransferRequestStatusEnum::CLOSED,
            'purchase_order_id' => $strategy->resolvePurchaseOrderId($transferRequest, $apiResponse),
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Transfer request status updated successfully.',
            data: new TransferRequestResource($transferRequest)
        );
    }
}
