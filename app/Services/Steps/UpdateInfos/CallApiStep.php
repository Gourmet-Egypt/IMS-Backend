<?php

namespace App\Services\Steps\UpdateInfos;

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
            ->post("http://{$server}/api/update-order-details", $payload->apiData);

        if (!$response->successful()) {
            return $this->error(
                status: Response::HTTP_INTERNAL_SERVER_ERROR,
                message: $response->body()
            );
        }

        $payload->apiResponse = $response;

        return $next($payload);
    }
}
