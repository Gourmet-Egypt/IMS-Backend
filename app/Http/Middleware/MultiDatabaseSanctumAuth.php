<?php

namespace App\Http\Middleware;

use App\Models\AdminToken;
use App\Models\UserToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MultiDatabaseSanctumAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $accessToken = UserToken::findToken($token);


        if (!$accessToken) {
            $accessToken = AdminToken::findToken($token);
        }


        if ($accessToken && $accessToken->tokenable) {

            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token expired.',
                ], 401);
            }


            $request->setUserResolver(function () use ($accessToken) {
                return $accessToken->tokenable;
            });


            $accessToken->forceFill(['last_used_at' => now()])->save();

            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.',
        ], 401);
    }
}
