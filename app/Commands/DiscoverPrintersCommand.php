<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DiscoverPrintersCommand extends Command
{
    protected $signature = 'printer:discover
                            {--range=192.168.1.1-192.168.1.254 : IP range to scan}
                            {--port=9100 : Port to check}
                            {--timeout=1 : Connection timeout in seconds}';

    protected $description = 'Discover network printers on the local network';

    public function handle(): int
    {
        $range = $this->option('range');
        $port = (int) $this->option('port');
        $timeout = (int) $this->option('timeout');

        $this->info("Scanning for printers on port {$port}...");
        $this->newLine();

        list($start, $end) = explode('-', $range);
        $startOctets = explode('.', $start);
        $endOctets = explode('.', $end);

        $startNum = (int) $startOctets[3];
        $endNum = (int) $endOctets[3];
        $subnet = "{$startOctets[0]}.{$startOctets[1]}.{$startOctets[2]}";

        $found = [];
        $bar = $this->output->createProgressBar($endNum - $startNum + 1);
        $bar->start();

        for ($i = $startNum; $i <= $endNum; $i++) {
            $ip = "{$subnet}.{$i}";

            $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);

            if ($socket) {
                fclose($socket);
                $found[] = ['IP' => $ip, 'Port' => $port, 'Status' => '✓ Available'];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (empty($found)) {
            $this->warn("No printers found on port {$port}");
            return 0;
        }

        $this->info("Found ".count($found)." printer(s):");
        $this->newLine();
        $this->table(['IP Address', 'Port', 'Status'], $found);

        $this->newLine();
        $this->info("Add these to your config/printing.php file");

        return 0;
    }

    // ============================================
    // 10. ADVANCED PRINTER SERVICE ADDITIONS
    // ============================================

    // Add these methods to PurchaseOrderPrintService class:

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
}
