<?php

namespace App\Services;

use App\Http\Resources\App\Offline\PurchaseOrderEntryResource;
use App\Models\PurchaseOrderEntry;
use App\Services\Steps\UpdateInfos\AcquireLockStep;
use App\Services\Steps\UpdateInfos\BuildDataStep;
use App\Services\Steps\UpdateInfos\CallApiStep;
use App\Services\Steps\UpdateInfos\ValidateStep;
use App\Support\Pipeline;
use App\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UpdateInfosService
{
    use Responses;

    protected Pipeline $pipeline;

    public function __construct()
    {
        $this->pipeline = new Pipeline();
    }

    public function update(PurchaseOrderEntry $purchaseOrderEntry, array $validated): JsonResponse
    {
        $payload = (object) [
            'purchaseOrderEntry' => $purchaseOrderEntry,
            'validated' => $validated,
            'poTypeEnum' => null,
            'storeId' => null,
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

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase order entry updated successfully',
            data: new PurchaseOrderEntryResource(
                $purchaseOrderEntry->load(['infos', 'itemById'])
            ),
        );
    }
}
