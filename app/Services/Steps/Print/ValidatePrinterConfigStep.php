<?php

namespace App\Services\Steps\Print;

use Illuminate\Support\Facades\Log;

class ValidatePrinterConfigStep
{
    public function handle($payload, \Closure $next)
    {
        if (!config('printing.enabled')) {
            Log::info("Auto-print disabled, skipping print for PO #{$payload->purchaseOrder->PONumber}");
            $payload->skipPrint = true;
            return $next($payload);
        }

        $printerConfig = config('printing.printers');

        if (!$printerConfig) {
            Log::warning("No printer configured for store");
            $payload->skipPrint = true;
            return $next($payload);
        }

        $payload->printerConfig = $printerConfig;
        $payload->skipPrint = false;

        return $next($payload);
    }
}
