<?php

namespace App\Services\Commit\Strategies;

use App\Services\Commit\CommitContext;
use App\Services\Commit\Contracts\CommitValidator;
use App\Services\Commit\Contracts\EmailPolicy;
use App\Services\Commit\Contracts\TransactionTypeStrategy;
use App\Services\Commit\Email\SupplierEmailPolicy;
use App\Services\Commit\Validators\ReturnToSupplierValidator;

class ReturnToSupplierStrategy implements TransactionTypeStrategy
{
    public function endpoint(CommitContext $ctx): string
    {
        return '/api/commit-order';
    }

    public function validator(): CommitValidator
    {
        return new ReturnToSupplierValidator();
    }

    public function emailPolicy(): EmailPolicy
    {
        return new SupplierEmailPolicy();
    }

    public function buildPayload(CommitContext $ctx): array
    {
        return ['Order' => [
            'transactionType' => 'ReturnToSupplier',
            'SupplierID' => (int) $ctx->order->SupplierID,
        ] + $ctx->basePayload()];
    }
}
