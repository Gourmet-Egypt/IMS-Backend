<?php

namespace App\Services\Steps\CreateOrder;

use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class CallApiStep
{
    use Responses;

    public function handle($payload, \Closure $next)
    {
        $server = config('database.connections.sqlsrv.host');

        $response = Http::withoutVerifying()
            ->asJson()
            ->post("http://{$server}/api/create-order", $payload->apiData);

        if ($response->failed()) {
            return $this->error(
                status: $response->status() ?? Response::HTTP_INTERNAL_SERVER_ERROR,
                message: $response->json('message') ?? 'Failed to create order'
            );
        }

        $payload->apiResponse = $response->json();

        return $next($payload);
    }
}
