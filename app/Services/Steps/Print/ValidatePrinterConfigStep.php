<?php

namespace App\Services\Steps\Print;

class ValidatePrinterConfigStep
{
    public function handle($payload, \Closure $next)
    {
        if (!config('printing.enabled')) {
            $payload->skipPrint = true;
            return $next($payload);
        }

        $printerConfig = config('printing.printers');

        if (!$printerConfig) {
            $payload->skipPrint = true;
            return $next($payload);
        }

        $payload->printerConfig = $printerConfig;
        $payload->skipPrint = false;

        return $next($payload);
    }
}
