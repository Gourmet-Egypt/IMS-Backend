<?php

namespace App\Services\Commit\Strategies;

use App\Services\Commit\CommitContext;
use App\Services\Commit\Contracts\CommitValidator;
use App\Services\Commit\Contracts\EmailPolicy;
use App\Services\Commit\Contracts\TransactionTypeStrategy;
use App\Services\Commit\Email\SupplierEmailPolicy;
use App\Services\Commit\Validators\PurchaseOrderValidator;

/**
 * PO (POType 0, and HQ 1). `isClosed` in the request picks the endpoint:
 *   isClosed = 1 -> /api/commit-order (closes; status becomes 2)
 *   isClosed = 0 -> /api/partial-po   (stays open; status becomes 1)
 */
class SupplierStrategy implements TransactionTypeStrategy
{
    public function endpoint(CommitContext $ctx): string
    {
        return $this->isClosing($ctx) ? '/api/commit-order' : '/api/partial-po';
    }

    public function validator(): CommitValidator
    {
        return new PurchaseOrderValidator();
    }

    public function emailPolicy(): EmailPolicy
    {
        return new SupplierEmailPolicy();
    }

    public function buildPayload(CommitContext $ctx): array
    {
        if ($this->isClosing($ctx)) {
            return ['Order' => $ctx->basePayload()];
        }

        return ['Order' => $ctx->basePayload() + ['isClosed' => 0]];
    }

    private function isClosing(CommitContext $ctx): bool
    {
        return (int) $ctx->request->input('isClosed', 0) === 1;
    }
}
