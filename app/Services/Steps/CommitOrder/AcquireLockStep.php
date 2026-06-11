<?php

namespace App\Services\Steps\CommitOrder;

use App\Traits\Responses;
use Illuminate\Support\Facades\Cache;

class AcquireLockStep
{
    use Responses;

    public function handle($payload, \Closure $next)
    {
        $cacheKey = "po_commit_{$payload->purchaseOrder->ID}";

        $lock = Cache::lock($cacheKey, 300);

        if (!$lock->get()) {
            return response()->json([
                'status' => 409,
                'message' => 'This order is already being processed. Please wait.',
            ], 409);
        }

        $payload->lock = $lock;

        try {
            return $next($payload);
        } finally {
            optional($payload->lock)->release();
        }
    }
}
