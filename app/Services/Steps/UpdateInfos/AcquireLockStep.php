<?php

namespace App\Services\Steps\UpdateInfos;

use Illuminate\Support\Facades\Cache;

class AcquireLockStep
{
    public function handle($payload, \Closure $next)
    {
        $cacheKey = "po_entry_update_{$payload->purchaseOrderEntry->ID}";

        $lock = Cache::lock($cacheKey, 300);

        if (!$lock->get()) {
            return response()->json([
                'status' => 409,
                'message' => 'This entry is already being updated. Please wait.',
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
