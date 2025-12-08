<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthenticatedSessionController extends Controller
{
    use Responses;

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();

        if ($user instanceof JsonResponse) {
            return $user;
        }


        $user->tokens()->delete();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        $guard = $request->input('guard', 'web');
        $resourceClass = $request->getResourceClass($guard);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Login Successful',
            data: [
                'token' => $token,
                'user' => new $resourceClass($user),
                'guard' => $guard,
            ]
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {

        $request->user()->currentAccessToken()->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Logout Successful',
            data: []
        );
    }
}
