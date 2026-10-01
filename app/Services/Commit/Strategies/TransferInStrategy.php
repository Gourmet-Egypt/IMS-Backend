<?php

namespace App\Services\Commit\Strategies;

use App\Services\Commit\CommitContext;
use App\Services\Commit\Contracts\CommitValidator;
use App\Services\Commit\Contracts\EmailPolicy;
use App\Services\Commit\Contracts\TransactionTypeStrategy;
use App\Services\Commit\Email\TransferInEmailPolicy;
use App\Services\Commit\Validators\TransferInValidator;

/**
 * TransferIN (POType 2, and HQ 4). One client-facing action; `isClosed` in the
 * request decides both the outcome and, since only one external endpoint
 * actually honors it, which endpoint is called:
 *   isClosed = 1 -> /api/commit-order        (closes; status becomes 2)
 *   isClosed = 0 -> /api/partial-transfer-in (stays open; status becomes 1)
 * Confirmed by direct testing: /api/commit-order always closes regardless of
 * `isClosed`, so isClosed=0 can only be honored via the partial endpoint.
 */
class TransferInStrategy implements TransactionTypeStrategy
{
    public function endpoint(CommitContext $ctx): string
    {
        return $this->isClosing($ctx) ? '/api/commit-order' : '/api/partial-transfer-in';
    }

    public function validator(): CommitValidator
    {
        return new TransferInValidator();
    }

    public function emailPolicy(): EmailPolicy
    {
        return new TransferInEmailPolicy();
    }

    public function buildPayload(CommitContext $ctx): array
    {
        $request = $ctx->request;

        if ($this->isClosing($ctx)) {
            return ['Order' => $ctx->basePayload() + [
                'isClosed'       => 1,
                'Vehicle_tempIN' => $request->input('Vehicle_tempIN', 0),
                'receiver_name'  => $ctx->cashier->Name ?? '',
            ]];
        }

        return ['Order' => $ctx->basePayload() + [
            'IsClosed'             => 0,
            'Vehicle_tempIN'       => (float) $request->input('vehicle_TempIN', $request->input('Vehicle_tempIN', 0)),
            'VehicleType'          => (string) $request->input('vehicleType', ''),
            'Vehicle_tempOut'      => (float) $request->input('vehicle_TempOut', 0),
            'DeliveryPermitNumber' => (string) $request->input('deliveryPermitNumber', ''),
            'Notes'                => (string) $request->input('notes', ''),
            'seal_number'          => (string) $request->input('seal_number', ''),
            'Driver_Name'          => (string) $request->input('driver_Name', ''),
            'Vehicle_Number'       => (string) $request->input('vehicle_Number', ''),
            'Goods_Type'           => (int) $request->input('goods_Type', 0),
        ]];
    }

    private function isClosing(CommitContext $ctx): bool
    {
        return (int) $ctx->request->input('isClosed', 0) === 1;
    }
}
