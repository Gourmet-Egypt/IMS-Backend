<?php

namespace App\Services\Steps\CommitOrder;

use Illuminate\Support\Facades\DB;

class BuildOrderDataStep
{
    public function handle($payload, \Closure $next)
    {
        $storeId = DB::table('Configuration')->select('StoreID')->value('StoreID');

        $baseData = [
            "ID" => $payload->purchaseOrder->ID,
            "transactionType" => $payload->poTypeEnum,
            "StoreID" => $storeId,
            "CashierID" => (int) $payload->cashier->ID
        ];

        $orderSpecific = match ($payload->purchaseOrder->POType) {
            '3' => [
                "VehicleType" => (string) $payload->request->input('VehicleTypeID'),
                "Vehicle_tempOut" => $payload->request->input('Vehicle_tempOut'),
                "DeliveryPermitNumber" => $payload->request->input('DeliveryPermitNumber'),
                "Notes" => $payload->request->input('Notes', ''),
                "seal_number" => $payload->request->input('seal_number'),
            ],
            '2' => [
                "Vehicle_tempIN" => $payload->request->input('Vehicle_tempIN'),
            ],
            default => [],
        };

        $payload->orderData = [
            "Order" => array_merge($baseData, $orderSpecific),
        ];

        return $next($payload);
    }
}
