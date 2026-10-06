<?php

namespace App\Services\Conditions;

use App\Enums\PurchaseOrderTypeEnum;
use App\Http\Resources\App\Offline\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Services\Commit\CommitException;
use App\Services\Commit\Contracts\CommitApiClient;
use App\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ConditionsService
{
    use Responses;

    public function __construct(
        private ConditionsStrategyFactory $strategies,
        private CommitApiClient $api,
    ) {}

    public function save(PurchaseOrder $order, Request $request): JsonResponse
    {
        try {
            $type = PurchaseOrderTypeEnum::tryFrom((int) $order->POType)
                ?? throw new CommitException('Invalid purchase order type', Response::HTTP_BAD_REQUEST);

            $strategy = $this->strategies->for($type);

            validator($request->all(), $strategy->rules(), $strategy->messages())->validate();

            $lock = Cache::lock("po_commit_{$order->ID}", 60);

            if (!$lock->get()) {
                throw new CommitException(
                    'This order is already being processed. Please wait.',
                    Response::HTTP_CONFLICT
                );
            }

            try {
                $this->guardNotClosed($order);

                $this->api->commit($strategy->endpoint(), $strategy->buildPayload($order, $request));
            } finally {
                $lock->release();
            }
        } catch (ValidationException $e) {
            return $this->error(
                status: Response::HTTP_UNPROCESSABLE_ENTITY,
                message: $e->validator->errors()->first()
            );
        } catch (CommitException $e) {
            return $this->error(status: $e->httpStatus, message: $e->getMessage());
        }

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Purchase Order Conditions Saved Successfully',
            data: new PurchaseOrderResource(
                $order->load(['condition', 'entries', 'entries.infos'])
            ),
        );
    }

    private function guardNotClosed(PurchaseOrder $order): void
    {
        if ((int) PurchaseOrder::whereKey($order->ID)->value('status') === 2) {
            throw new CommitException('Purchase order is already committed', Response::HTTP_CONFLICT);
        }
    }
}
