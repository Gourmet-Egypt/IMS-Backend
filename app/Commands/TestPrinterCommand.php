<?php

namespace App\Commands;

use App\Services\PurchaseOrderPrintService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TestPrinterCommand extends Command
{
    protected $signature = 'printer:test
                            {store_id : Store ID to test printer for}
                            {--ip= : Override printer IP}
                            {--port=9100 : Override printer port}
                            {--copies=1 : Number of copies to print}';

    protected $description = 'Test network printer connection and print a test page';

    public function handle(PurchaseOrderPrintService $printerService): int
    {
        $storeId = (int) $this->argument('store_id');

        $this->info("Testing printer for Store ID: {$storeId}");
        $this->newLine();


        $config = $printerService->getPrinterConfig($storeId);

        if (!$config) {
            $this->error("No printer configured for store {$storeId}");
            return 1;
        }

        if ($this->option('ip')) {
            $config['ip'] = $this->option('ip');
        }
        if ($this->option('port')) {
            $config['port'] = $this->option('port');
        }

        $this->info("Printer Configuration:");
        $this->table(
            ['Setting', 'Value'],
            [
                ['Name', $config['name']],
                ['Type', $config['type']],
                ['IP', $config['ip'] ?? 'N/A'],
                ['Port', $config['port'] ?? 'N/A'],
                ['Enabled', $config['enabled'] ? 'Yes' : 'No'],
            ]
        );
        $this->newLine();

        // Test network connection
        if ($config['type'] === 'network') {
            $this->info("Testing network connection...");
            if (!$this->testConnection($config['ip'], $config['port'])) {
                return 1;
            }
        }

        // Generate test PDF
        $this->info("Generating test PDF...");
        $testPdfPath = $this->generateTestPdf();
        $this->info("Test PDF created at: {$testPdfPath}");
        $this->newLine();

        // Attempt to print
        $this->info("Sending print job...");

        try {
            $copies = (int) $this->option('copies');
            $printerService->printPdf($testPdfPath, $config, $copies);

            $this->info("✓ Print job sent successfully!");
            $this->info("Check your printer for output.");

            // Cleanup
            sleep(2);
            File::delete($testPdfPath);

            return 0;

        } catch (\Exception $e) {
            $this->error("✗ Print failed: ".$e->getMessage());
            File::delete($testPdfPath);
            return 1;
        }
    }

    protected function testConnection(string $ip, int $port): bool
    {
        $this->line("Attempting to connect to {$ip}:{$port}...");

        $socket = @fsockopen($ip, $port, $errno, $errstr, 5);

        if (!$socket) {
            $this->error("✗ Connection failed: {$errstr} ({$errno})");
            $this->newLine();
            $this->warn("Troubleshooting tips:");
            $this->line("1. Verify printer IP address is correct");
            $this->line("2. Check if printer is powered on");
            $this->line("3. Ensure printer is on the same network");
            $this->line("4. Check firewall settings");
            $this->line("5. Verify port 9100 is open on printer");
            return false;
        }

        fclose($socket);
        $this->info("✓ Connection successful!");
        $this->newLine();
        return true;
    }

    protected function generateTestPdf(): string
    {
        $tempPath = config('printing.temp_path');
        File::ensureDirectoryExists($tempPath);

        $filename = 'test_print_'.now()->format('YmdHis').'.pdf';
        $filepath = $tempPath.'/'.$filename;


        $html = view('pdf.test-page', [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'store_id' => $this->argument('store_id'),
        ])->render();


        $pdf = app(\App\Services\PurchaseOrderPdfService::class);

        // Simplified PDF generation
        file_put_contents($filepath, $this->createSimplePdf());

        return $filepath;
    }

    protected function createSimplePdf(): string
    {
        return "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj 4 0 obj<</Length 100>>stream\nBT /F1 24 Tf 100 700 Td (Test Print Page) Tj ET\nBT /F1 12 Tf 100 650 Td (Timestamp: ".now()->format('Y-m-d H:i:s').") Tj ET\nendstream\nendobj 5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj xref\n0 6\n0000000000 65535 f\n0000000009 00000 n\n0000000056 00000 n\n0000000115 00000 n\n0000000262 00000 n\n0000000410 00000 n\ntrailer<</Size 6/Root 1 0 R>>\nstartxref\n489\n%%EOF";
    }
}
