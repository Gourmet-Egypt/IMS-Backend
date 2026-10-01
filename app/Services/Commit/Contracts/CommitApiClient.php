<?php

namespace App\Services\Commit\Contracts;

/**
 * Outbound client for the external (legacy) commit API — the system of record.
 * The HTTP implementation is built in a later step and bound to this interface
 * in a service provider.
 */
interface CommitApiClient
{
    /**
     * POST the built payload to the given external endpoint (the strategy
     * decides which). Returns the decoded body; throws CommitException on failure.
     */
    public function commit(string $endpoint, array $payload): array;
}
