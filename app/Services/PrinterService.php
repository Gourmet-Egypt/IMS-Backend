<?php

namespace App\Services;

use App\Jobs\PrintDocumentJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    /**
     * Queue a print job
     */
    public function queuePrint(string $pdfPath, int $storeId, int $copies = 1): bool
    {
        if (!config('printing.enabled')) {
            Log::info('Auto-print disabled, skipping print job');
            return false;
        }

        $printerConfig = $this->getPrinterConfig($storeId);

        if (!$printerConfig) {
            Log::warning("No printer configured for store {$storeId}");
            return false;
        }

        PrintDocumentJob::dispatch($pdfPath, $storeId, $copies)
            ->onQueue('printing');

        Log::info("Print job queued for store {$storeId}", [
            'pdf' => $pdfPath,
            'printer' => $printerConfig['name'],
        ]);

        return true;
    }

    /**
     * Get printer configuration for a store
     */
    public function getPrinterConfig(int $storeId): ?array
    {
        $printers = config('printing.printers', []);

        // Try to get store-specific printer
        if (isset($printers[$storeId]) && $printers[$storeId]['enabled']) {
            return $printers[$storeId];
        }

        // Fall back to default printer
        $default = config('printing.default');
        return $default['enabled'] ? $default : null;
    }

    /**
     * Execute print to network printer
     */
    public function printPdf(string $pdfPath, array $printerConfig, int $copies = 1): bool
    {
        if (!File::exists($pdfPath)) {
            throw new \Exception("PDF file not found: {$pdfPath}");
        }

        $type = $printerConfig['type'] ?? 'local';

        switch ($type) {
            case 'network':
                return $this->printToNetworkPrinter($pdfPath, $printerConfig, $copies);
            case 'local':
                return $this->printToLocalPrinter($pdfPath, $printerConfig, $copies);
            case 'file':
                return $this->printToFile($pdfPath, $printerConfig);
            default:
                throw new \Exception("Unknown printer type: {$type}");
        }
    }

    /**
     * Print to network printer via raw socket connection
     */
    protected function printToNetworkPrinter(string $pdfPath, array $config, int $copies): bool
    {
        $ip = $config['ip'];
        $port = $config['port'] ?? 9100;

        Log::info("Printing to network printer", [
            'ip' => $ip,
            'port' => $port,
            'file' => $pdfPath,
        ]);

        try {
            // Read PDF content
            $pdfContent = File::get($pdfPath);

            // Open socket connection to printer
            $socket = @fsockopen($ip, $port, $errno, $errstr, 10);

            if (!$socket) {
                throw new \Exception("Failed to connect to printer at {$ip}:{$port} - {$errstr} ({$errno})");
            }

            // Send PDF data for each copy
            for ($i = 0; $i < $copies; $i++) {
                $written = fwrite($socket, $pdfContent);

                if ($written === false) {
                    fclose($socket);
                    throw new \Exception("Failed to write to printer socket");
                }

                // Small delay between copies
                if ($i < $copies - 1) {
                    usleep(500000); // 0.5 seconds
                }
            }

            fclose($socket);

            Log::info("Successfully printed {$copies} copies to network printer {$ip}:{$port}");
            return true;

        } catch (\Exception $e) {
            Log::error("Network print failed: ".$e->getMessage(), [
                'ip' => $ip,
                'port' => $port,
                'pdf' => $pdfPath,
            ]);
            throw $e;
        }
    }

    /**
     * Print to local printer using system commands
     */
    protected function printToLocalPrinter(string $pdfPath, array $config, int $copies): bool
    {
        $printerName = $config['name'];
        $os = PHP_OS_FAMILY;

        if ($os === 'Windows') {
            return $this->printWindowsLocal($pdfPath, $printerName, $copies);
        } else {
            return $this->printLinuxLocal($pdfPath, $printerName, $copies);
        }
    }

    /**
     * Windows local printing
     */
    protected function printWindowsLocal(string $pdfPath, string $printer, int $copies): bool
    {
        // Try SumatraPDF first (best option)
        $sumatraPath = 'C:\Program Files\SumatraPDF\SumatraPDF.exe';
        if (file_exists($sumatraPath)) {
            $command = sprintf(
                '"%s" -print-to "%s" -silent -print-settings "noscale,%dx" "%s"',
                $sumatraPath,
                $printer,
                $copies,
                $pdfPath
            );
            exec($command, $output, $result);
            if ($result === 0) {
                return true;
            }
        }

        // Fallback to other methods...
        Log::warning("Windows local printing not fully implemented in this example");
        return false;
    }

    /**
     * Linux local printing
     */
    protected function printLinuxLocal(string $pdfPath, string $printer, int $copies): bool
    {
        $command = sprintf(
            'lp -d %s -n %d "%s"',
            escapeshellarg($printer),
            $copies,
            escapeshellarg($pdfPath)
        );

        exec($command, $output, $result);

        if ($result === 0) {
            Log::info("Linux print successful", ['printer' => $printer]);
            return true;
        }

        throw new \Exception("Linux print command failed with code {$result}");
    }

    /**
     * Print to file (for testing)
     */
    protected function printToFile(string $pdfPath, array $config): bool
    {
        $outputPath = storage_path('app/printed_documents/'.basename($pdfPath));
        File::ensureDirectoryExists(dirname($outputPath));
        File::copy($pdfPath, $outputPath);

        Log::info("PDF copied to file", ['output' => $outputPath]);
        return true;
    }

    /**
     * Test printer connection without printing
     */
    public function testConnection(int $storeId): array
    {
        $config = $this->getPrinterConfig($storeId);

        if (!$config) {
            return [
                'success' => false,
                'message' => 'No printer configured',
            ];
        }

        if ($config['type'] !== 'network') {
            return [
                'success' => true,
                'message' => 'Not a network printer',
                'type' => $config['type'],
            ];
        }

        $ip = $config['ip'];
        $port = $config['port'] ?? 9100;

        $socket = @fsockopen($ip, $port, $errno, $errstr, 5);

        if (!$socket) {
            return [
                'success' => false,
                'message' => "Connection failed: {$errstr} ({$errno})",
                'ip' => $ip,
                'port' => $port,
            ];
        }

        fclose($socket);

        return [
            'success' => true,
            'message' => 'Connection successful',
            'ip' => $ip,
            'port' => $port,
            'printer' => $config['name'],
        ];
    }

    /**
     * Get printer status via SNMP (if available)
     */
    public function getPrinterStatus(string $ip): ?array
    {
        // Requires PHP SNMP extension
        if (!extension_loaded('snmp')) {
            return null;
        }

        try {
            snmp_set_quick_print(true);
            snmp_set_oid_output_format(SNMP_OID_OUTPUT_FULL);

            // Standard printer MIB OIDs
            $status = @snmpget($ip, 'public', '1.3.6.1.2.1.25.3.5.1.1.1');
            $pages = @snmpget($ip, 'public', '1.3.6.1.2.1.43.10.2.1.4.1.1');

            return [
                'status' => $status ?: 'Unknown',
                'pages_printed' => $pages ?: 'Unknown',
            ];

        } catch (\Exception $e) {
            Log::warning("Failed to get printer status via SNMP", [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Clean up old temporary PDF files
     */
    public function cleanupOldFiles(): void
    {
        $tempPath = config('printing.temp_path');
        $hours = config('printing.cleanup_after_hours', 24);
        $cutoff = now()->subHours($hours);

        $files = File::files($tempPath);
        $deleted = 0;

        foreach ($files as $file) {
            if (File::lastModified($file) < $cutoff->timestamp) {
                File::delete($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            Log::info("Cleaned up {$deleted} old PDF files");
        }
    }
}
