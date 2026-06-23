<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Dashboard\UserResource;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

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

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $hashedPassword = Hash::make($request->password);

        $user->update([
            'password' => $hashedPassword,
        ]);

        User::onSecondary()->where('user_number', $user->user_number)->update([
            'password' => $hashedPassword,
        ]);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'Password changed successfully.',
            data: new UserResource($user)
        );
    }
}
