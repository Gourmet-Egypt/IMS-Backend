<?php

namespace App\Services\Steps\Print;

use Illuminate\Support\Facades\File;

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

        } catch (\Exception $e) {
            throw $e;
        }

        return $next($payload);
    }
}
