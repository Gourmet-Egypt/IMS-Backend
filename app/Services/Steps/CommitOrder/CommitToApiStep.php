<?php

namespace App\Services\Steps\CommitOrder;

use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class CommitToApiStep
{
    use Responses;

    public function handle($payload, \Closure $next)
    {
        $server = config('database.connections.sqlsrv.host');
        $endpoint = $payload->isPartial ? '/api/partial-transfer-in' : '/api/commit-order';

        $response = Http::withoutVerifying()
            ->timeout(30)
            ->asJson()
            ->post("http://".$server.$endpoint, $payload->orderData);

        if (!$response->successful()) {
            $responseData = $response->json() ?? [];
            $errorMessage = $this->parseErrorMessage($responseData);

            return $this->error(
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: $errorMessage
            );
        }

        $payload->apiResponse = $response;

        return $next($payload);
    }

    protected function parseErrorMessage(?array $responseData): string
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

        // Ensure valid UTF-8
        $errorMessage = mb_convert_encoding($errorMessage, 'UTF-8', 'UTF-8');

        return $errorMessage;
    }
}
