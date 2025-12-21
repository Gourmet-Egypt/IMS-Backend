<?php

namespace App\Http\Controllers;

use App\Services\PrinterService;
use Illuminate\Support\Facades\DB;

class PrinterController extends Controller
{
    protected PrinterService $printerService;

    public function __construct(PrinterService $printerService)
    {
        $this->printerService = $printerService;
    }

    /**
     * List all configured printers
     */
    public function index()
    {
        $printers = config('printing.printers', []);
        $default = config('printing.default');

        return response()->json([
            'printers' => $printers,
            'default' => $default,
            'enabled' => config('printing.enabled'),
        ]);
    }

    /**
     * Test printer connection
     */
    public function test(int $storeId)
    {
        $result = $this->printerService->testConnection($storeId);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Get printer status
     */
    public function status(int $storeId)
    {
        $config = $this->printerService->getPrinterConfig($storeId);

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'No printer configured',
            ], 404);
        }

        $connectionTest = $this->printerService->testConnection($storeId);

        $status = [
            'printer' => $config,
            'connection' => $connectionTest,
        ];

        // Add SNMP status if available
        if ($config['type'] === 'network' && isset($config['ip'])) {
            $snmpStatus = $this->printerService->getPrinterStatus($config['ip']);
            if ($snmpStatus) {
                $status['device_status'] = $snmpStatus;
            }
        }

        return response()->json($status);
    }

    /**
     * Get print queue status
     */
    public function queueStatus()
    {
        $pending = DB::table('jobs')
            ->where('queue', 'printing')
            ->count();

        $failed = DB::table('failed_jobs')
            ->where('queue', 'printing')
            ->orderBy('failed_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'pending' => $pending,
            'failed' => $failed->count(),
            'recent_failures' => $failed,
        ]);
    }
}
