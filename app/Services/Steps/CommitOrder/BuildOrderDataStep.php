<?php

namespace App\Services\Steps\CommitOrder;

use App\Models\PurchaseOrderProcessStart;
use Illuminate\Support\Facades\DB;

class BuildOrderDataStep
{
    public function handle($payload, \Closure $next)
    {
        \Illuminate\Support\Facades\Log::info("Building order data for Purchase Order #{$payload->purchaseOrder->ID}");

        $storeId = DB::table('Configuration')->select('StoreID')->value('StoreID');

        // // Fetch start_date from process start table
        // $processStart = PurchaseOrderProcessStart::where('purchase_order_id', $payload->purchaseOrder->ID)->first();
        // $startDate = $processStart ? $processStart->start_date : null;

        $baseData = [
            "ID" => $payload->purchaseOrder->ID,
            "transactionType" => $payload->poTypeEnum,
            "StoreID" => $storeId,
            "CashierID" => (int) $payload->cashier->ID
        ];

        // // Add start_date if it exists
        // if ($startDate) {
        //     $baseData["start_date"] = $startDate->format('Y-m-d H:i:s');
        // }

        $orderSpecific = match ($payload->purchaseOrder->POType) {
            '3' => [
                "VehicleType" => (string) $payload->request->input('VehicleTypeID', ''),
                "Vehicle_tempOut" => $payload->request->input('Vehicle_tempOut', 0),
                "DeliveryPermitNumber" => $payload->request->input('DeliveryPermitNumber', ''),
                "Notes" => $payload->request->input('Notes', ''),
                "seal_number" => $payload->request->input('seal_number', ''),
                "driver_name" => $payload->request->input('driver_name', ''),
            ],
            '2' => [
                "Vehicle_tempIN" => $payload->request->input('Vehicle_tempIN', 0),
                "receiver_name" => $payload->request->input('receiver_name', ''),
            ],
            default => [],
        };

        $payload->orderData = [
            "Order" => array_merge($baseData, $orderSpecific),
        ];

        \Illuminate\Support\Facades\Log::info("Order data built successfully for Purchase Order #{$payload->purchaseOrder->ID}", [
            'purchase_order_id' => $payload->purchaseOrder->ID,
            'store_id' => $storeId,
            'cashier_id' => $payload->cashier->ID,
            'transaction_type' => $payload->poTypeEnum,
            // 'has_start_date' => $startDate !== null,
        ]);

        return $next($payload);
    }
}
