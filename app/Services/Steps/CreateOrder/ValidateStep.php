<?php

namespace App\Services\Steps\CreateOrder;

use App\Traits\Responses;
use Illuminate\Http\Response;

class ValidateStep
{
    use Responses;

    public function handle($payload, \Closure $next)
    {
        $transferRequest = $payload->transferRequest;

        if (!$transferRequest->items()->exists()) {
            return $this->error(
                status: Response::HTTP_NOT_ACCEPTABLE,
                message: 'No items were found',
                data: []
            );
        }

        $cashier = $payload->request->user()->cashier;

        if (!$cashier) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Cashier not found'
            );
        }

        $payload->cashier = $cashier;

        return $next($payload);
    }
}
