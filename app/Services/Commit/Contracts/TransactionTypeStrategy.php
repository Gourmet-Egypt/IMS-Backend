<?php

namespace App\Services\Commit\Contracts;

use App\Services\Commit\CommitContext;

/**
 * One implementation per transaction type (TransferIn / TransferOut / Supplier).
 * The factory resolves the right one from the order's POType; the commit server
 * then talks only to this contract, never to a concrete type.
 */
interface TransactionTypeStrategy
{
    /**
     * The external API endpoint this commit resolves to. There is still a
     * single client-facing action per type — no separate partial route or
     * flag — but the endpoint itself may depend on the payload: only
     * /api/partial-transfer-in honors `isClosed: 0` (stays partial);
     * /api/commit-order always closes regardless of `isClosed`.
     */
    public function endpoint(CommitContext $ctx): string;

    /**
     * The validator carrying this type's field rules.
     */
    public function validator(): CommitValidator;

    /**
     * The per-type email behavior (recipients + perspective).
     */
    public function emailPolicy(): EmailPolicy;

    /**
     * Build the payload sent to the external commit API for this type.
     */
    public function buildPayload(CommitContext $ctx): array;
}
