<?php

namespace App\Services\Steps\Print;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PrintToNetworkStep
{
    public function handle($payload, \Closure $next)
    {
        if ($payload->skipPrint) {
            return $next($payload);
        }

        $ip = $payload->printerConfig['ip'];
        $port = $payload->printerConfig['port'] ?? 9100;
        $copies = $payload->copies;

        Log::info("Printing to network printer", [
            'ip' => $ip,
            'port' => $port,
            'file' => $payload->pdfPath,
        ]);

        try {
            $pdfContent = File::get($payload->pdfPath);

            $socket = @fsockopen($ip, $port, $errno, $errstr, 10);

            if (!$socket) {
                throw new \Exception("Failed to connect to printer at {$ip}:{$port} - {$errstr} ({$errno})");
            }

            for ($i = 0; $i < $copies; $i++) {
                $written = fwrite($socket, $pdfContent);

                if ($written === false) {
                    fclose($socket);
                    throw new \Exception("Failed to write to printer socket");
                }

                if ($i < $copies - 1) {
                    usleep(500000); // 0.5 seconds delay between copies
                }
            }

            fclose($socket);

            Log::info("Successfully printed {$copies} copies to network printer {$ip}:{$port} for PO #{$payload->purchaseOrder->PONumber}");

        } catch (\Exception $e) {
            Log::error("Network print failed: ".$e->getMessage(), [
                'ip' => $ip,
                'port' => $port,
                'pdf' => $payload->pdfPath,
                'po_number' => $payload->purchaseOrder->PONumber,
            ]);
            throw $e;
        }

        return $next($payload);
    }
}
