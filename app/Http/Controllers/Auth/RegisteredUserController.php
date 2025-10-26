<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Dashboard\UserResource;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class RegisteredUserController extends Controller
{
    use Responses ;
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_number' => $request->user_number ,
            'store_id' => $request->store_id,
            'role' => $request->role
        ]);

        event(new Registered($user));

        Auth::login($user);

        return $this->success(
            status: Response::HTTP_CREATED ,
            message: "User created successfully" ,
            data: New UserResource($user)
        );
    }
}
