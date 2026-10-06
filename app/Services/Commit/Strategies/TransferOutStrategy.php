<?php

namespace App\Services\Commit\Strategies;

use App\Services\Commit\CommitContext;
use App\Services\Commit\Contracts\CommitValidator;
use App\Services\Commit\Contracts\EmailPolicy;
use App\Services\Commit\Contracts\TransactionTypeStrategy;
use App\Services\Commit\Email\TransferOutEmailPolicy;
use App\Services\Commit\Validators\TransferOutValidator;

/**
 * TransferOut (POType 3, and HQ 5). One action, always hitting /api/commit-order
 * and always closing the order (no isClosed concept for this type).
 */
class TransferOutStrategy implements TransactionTypeStrategy
{
    public function endpoint(CommitContext $ctx): string
    {
        return '/api/commit-order';
    }

    public function validator(): CommitValidator
    {
        return new TransferOutValidator();
    }

    public function emailPolicy(): EmailPolicy
    {
        return new TransferOutEmailPolicy();
    }

    public function buildPayload(CommitContext $ctx): array
    {
        $request = $ctx->request;

        return ['Order' => $ctx->basePayload() + [
            'VehicleType'          => (string) $request->input('VehicleType', ''),
            'Vehicle_TempOut'      => $request->input('Vehicle_tempOut', 0),
            'DeliveryPermitNumber' => $request->input('DeliveryPermitNumber', ''),
            'Notes'                => $request->input('Notes', ''),
            'seal_number'          => $request->input('seal_number', ''),
            'Goods_type'           => (int) $request->input('goods_type', 0),
            'Driver_name'          => $request->input('driver_name', ''),
            'Vehicle_number'       => $request->input('vehicle_number', ''),
        ]];
    }
}
