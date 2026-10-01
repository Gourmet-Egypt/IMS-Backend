<?php

namespace App\Services\Commit\Strategies;

use App\Services\Commit\CommitContext;
use App\Services\Commit\Contracts\EmailPolicy;
use App\Services\Commit\Email\SupplierEmailPolicy;

/**
 * Supplier purchase order (POType 0/1). Committed exactly as a transfer-in —
 * same fields, validation, and endpoint routing (isClosed decides both the
 * outcome and which endpoint is called, see TransferInStrategy) — the only
 * difference being that the counterparty is the PO's SupplierID rather than
 * an OtherStore.
 */
class SupplierStrategy extends TransferInStrategy
{
    public function emailPolicy(): EmailPolicy
    {
        return new SupplierEmailPolicy();
    }

    public function buildPayload(CommitContext $ctx): array
    {
        $payload = parent::buildPayload($ctx);
        $payload['Order']['SupplierID'] = (int) $ctx->order->SupplierID;

        return $payload;
    }
}
