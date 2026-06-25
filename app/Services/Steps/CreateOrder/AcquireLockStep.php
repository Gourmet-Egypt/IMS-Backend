<?php

namespace App\Services\Steps\CreateOrder;

use Illuminate\Support\Facades\Cache;

class AcquireLockStep
{
    public function handle($payload, \Closure $next)
    {
        $cacheKey = "transfer_create_order_{$payload->transferRequest->id}";

        $lock = Cache::lock($cacheKey, 300);

        if (!$lock->get()) {
            return response()->json([
                'status' => 409,
                'message' => 'This transfer request is already being processed. Please wait.',
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
