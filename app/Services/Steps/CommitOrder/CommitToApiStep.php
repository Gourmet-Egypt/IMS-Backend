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

        $response = Http::withoutVerifying()
            ->timeout(30)
            ->asJson()
            ->post("http://".$server."/api/commit-order", $payload->orderData);

        if (!$response->successful()) {
            $responseData = $response->json();
            $errorMessage = $this->parseErrorMessage($responseData);

            return $this->error(
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: $errorMessage
            );
        }

        $payload->apiResponse = $response;

        return $next($payload);
    }

    protected function parseErrorMessage(array $responseData): string
    {
        $errorMessage = 'Failed to commit order';

        if (!isset($responseData['message'])) {
            return $errorMessage;
        }

        if (is_string($responseData['message'])) {
            preg_match('/"message":\s*"([^"]+)"/', $responseData['message'], $matches);
            $errorMessage = !empty($matches[1]) ? $matches[1] : $responseData['message'];
        } elseif (is_array($responseData['message'])) {
            $errorMessage = json_encode($responseData['message']);
        } else {
            $errorMessage = $responseData['message'];
        }

        if (strpos($errorMessage, ':') !== false) {
            $errorMessage = trim(substr($errorMessage, strpos($errorMessage, ':') + 1));
        }

        return $errorMessage;
    }
}
