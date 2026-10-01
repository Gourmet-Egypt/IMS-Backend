<?php

namespace App\Services\Steps\CreateOrder;

use App\Enums\TransferRequestTypeEnum;
use App\Services\CreateOrder\StrategyFactory;
use App\Traits\Responses;
use Illuminate\Http\Response;

class ValidateStepV2
{
    use Responses;

    public function __construct(private StrategyFactory $strategies) {}

    public function handle($payload, \Closure $next)
    {
        $transferRequest = $payload->transferRequest;

        if (!$transferRequest->items()->exists()) {
            return $this->error(status: Response::HTTP_NOT_ACCEPTABLE, message: 'No items were found', data: []);
        }

        $cashier = $payload->request->user()->cashier;

        if (!$cashier) {
            return $this->error(status: Response::HTTP_NOT_FOUND, message: 'Cashier not found');
        }

        $type = TransferRequestTypeEnum::from($transferRequest->type);
        $strategy = $this->strategies->for($type);

        if ($error = $strategy->validate($transferRequest)) {
            return $this->error(status: Response::HTTP_UNPROCESSABLE_ENTITY, message: $error);
        }

        $payload->cashier = $cashier;

        return $next($payload);
    }
}
