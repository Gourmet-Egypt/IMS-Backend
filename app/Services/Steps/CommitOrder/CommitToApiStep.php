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

        \Illuminate\Support\Facades\Log::info("Committing order to API for Purchase Order #{$payload->purchaseOrder->ID}", [
            'purchase_order_id' => $payload->purchaseOrder->ID,
            'po_number' => $payload->purchaseOrder->PONumber,
            'api_endpoint' => "http://{$server}/api/commit-order",
            'order_data' => $payload->orderData,
        ]);

        $response = Http::withoutVerifying()
            ->timeout(30)
            ->asJson()
            ->post("http://".$server."/api/commit-order", $payload->orderData);

        if (!$response->successful()) {
            $responseData = $response->json();
            $errorMessage = $this->parseErrorMessage($responseData);

            \Illuminate\Support\Facades\Log::error("API commit failed for Purchase Order #{$payload->purchaseOrder->ID}", [
                'purchase_order_id' => $payload->purchaseOrder->ID,
                'po_number' => $payload->purchaseOrder->PONumber,
                'status_code' => $response->status(),
                'error_message' => $errorMessage,
                'response_data' => $responseData,
            ]);

            return $this->error(
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: $errorMessage
            );
        }

        \Illuminate\Support\Facades\Log::info("API commit successful for Purchase Order #{$payload->purchaseOrder->ID}", [
            'purchase_order_id' => $payload->purchaseOrder->ID,
            'po_number' => $payload->purchaseOrder->PONumber,
            'status_code' => $response->status(),
            'response_data' => $response->json(),
        ]);

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
