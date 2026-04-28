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
                return $next($payload);
            }

            for ($i = 0; $i < $copies; $i++) {
                $written = fwrite($socket, $pdfContent);

                if ($written === false) {
                    fclose($socket);
                    return $next($payload);
                }

                if ($i < $copies - 1) {
                    usleep(500000); // 0.5 seconds delay between copies
                }
            }

            fclose($socket);

        } catch (\Exception $e) {
            // Silently skip printing if an error occurs
            return $next($payload);
        }

        return $next($payload);
    }
}
