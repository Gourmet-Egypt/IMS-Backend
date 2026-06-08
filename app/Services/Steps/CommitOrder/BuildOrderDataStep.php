<?php

namespace App\Services\Steps\CommitOrder;

use App\Models\PurchaseOrderProcessStart;
use Illuminate\Support\Facades\DB;

class BuildOrderDataStep
{
    public function handle($payload, \Closure $next)
    {
        $storeId = DB::table('Configuration')->select('StoreID')->value('StoreID');

        if ($payload->isPartial) {
            $payload->orderData = [
                "Order" => [
                    "ID" => (int) $payload->purchaseOrder->ID,
                    "transactionType" => $payload->poTypeEnum,
                    "IsClosed" => (int) $payload->request->input('isClosed', 0),
                    "StoreID" => (int) $storeId,
                    "CashierID" => (int) $payload->cashier->ID,
                    "VehicleType" => (string) $payload->request->input('vehicleType', ''),
                    "Vehicle_tempOut" => (float) $payload->request->input('vehicle_TempOut', 0),
                    "Vehicle_tempIN" => (float) $payload->request->input('vehicle_TempIN', 0),
                    "DeliveryPermitNumber" => (string) $payload->request->input('deliveryPermitNumber', ''),
                    "Notes" => (string) $payload->request->input('notes', ''),
                    "seal_number" => (string) $payload->request->input('seal_number', ''),
                    "Driver_Name" => (string) $payload->request->input('driver_Name', ''),
                    "Vehicle_Number" => (string) $payload->request->input('vehicle_Number', ''),
                    "Goods_Type" => (int) $payload->request->input('goods_Type', 0),
                ],
            ];
        } else {
            $baseData = [
                "ID" => $payload->purchaseOrder->ID,
                "transactionType" => $payload->poTypeEnum,
                "StoreID" => $storeId,
                "CashierID" => (int) $payload->cashier->ID
            ];

            $orderSpecific = match ($payload->purchaseOrder->POType) {
                '3' => [
                    "VehicleType" => (string) $payload->request->input('VehicleType', ''),
                    "Vehicle_tempOut" => $payload->request->input('Vehicle_tempOut', 0),
                    "DeliveryPermitNumber" => $payload->request->input('DeliveryPermitNumber', ''),
                    "Notes" => $payload->request->input('Notes', ''),
                    "seal_number" => $payload->request->input('seal_number', ''),
                    "Goods_type" => (int) $payload->request->input('goods_type', 0),
                    "Driver_name" => $payload->request->input('driver_name', ''),
                    "Vehicle_number" => $payload->request->input('vehicle_number', ''),
                ],
                '2' => [
                    "isClosed" => (int) $payload->request->input('isClosed', 0),
                    "Vehicle_tempIN" => $payload->request->input('Vehicle_tempIN', 0),
                    "receiver_name" => $payload->cashier->Name ?? '',
                ],
                default => [],
            };

            $payload->orderData = [
                "Order" => array_merge($baseData, $orderSpecific),
            ];
        }

        return $next($payload);
    }
}
