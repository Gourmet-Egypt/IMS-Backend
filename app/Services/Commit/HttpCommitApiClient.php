<?php

namespace App\Services\Commit;

use App\Services\Commit\Contracts\CommitApiClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

/**
 * Talks to the legacy commit API on the SQL Server host. Single POST, bounded
 * timeout, and NO retry — the commit is not idempotent, so a retry could
 * double-commit. Failures surface as CommitException with an HTTP status.
 */
class HttpCommitApiClient implements CommitApiClient
{
    public function commit(string $endpoint, array $payload): array
    {
        $server = config('database.connections.sqlsrv.host');

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->asJson()
                ->post("http://{$server}{$endpoint}", $payload);
        } catch (ConnectionException $e) {
            throw new CommitException(
                'Could not reach the commit service: ' . $e->getMessage(),
                Response::HTTP_BAD_GATEWAY
            );
        }

        if (!$response->successful()) {
            throw new CommitException(
                $this->parseError($response->json() ?? []),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response->json() ?? [];
    }

    /**
     * Ported from the legacy CommitToApiStep::parseErrorMessage.
     */
    protected function parseError(?array $responseData): string
    {
        $errorMessage = 'Failed to commit order';

        if (!$responseData || !isset($responseData['message'])) {
            return $errorMessage;
        }

        if (is_string($responseData['message'])) {
            preg_match('/"message":\s*"([^"]+)"/', $responseData['message'], $matches);
            $errorMessage = !empty($matches[1]) ? $matches[1] : $responseData['message'];
        } elseif (is_array($responseData['message'])) {
            $errorMessage = json_encode($responseData['message'], JSON_INVALID_UTF8_SUBSTITUTE);
        } else {
            $errorMessage = (string) $responseData['message'];
        }

        if (strpos($errorMessage, ':') !== false) {
            $errorMessage = trim(substr($errorMessage, strpos($errorMessage, ':') + 1));
        }

        return mb_convert_encoding($errorMessage, 'UTF-8', 'UTF-8');
    }
}
