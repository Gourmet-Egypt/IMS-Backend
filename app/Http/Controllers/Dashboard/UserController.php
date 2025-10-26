<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\User\StoreUserRequest;
use App\Http\Requests\Dashboard\User\UpdateUserRequest;
use App\Http\Resources\Dashboard\UserResource;
use App\Models\Cashier;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use Responses ;

    public function index(Request $request)
    {
        $cashiers = Cashier::search($request->store_id)->get();

        return $this->success(
            status : Response::HTTP_OK,
            message : 'Cashier List',
            data    : UserResource::collection($cashiers),
        );
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->only(['name', 'email', 'store_id', 'user_number', 'role', 'security_level']);

        $data['password'] = Hash::make($request->password);
        $data['security_level'] = $data['security_level'] ?? 4;

        $user = User::create($data);

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'User created successfully',
            data: new UserResource($user)
        );
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return $this->success(
            status: Response::HTTP_OK,
            message: 'User retrieved successfully',
            data: new UserResource($user)
        );
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $updateData = $request->only(['name', 'email', 'store_id', 'user_number', 'role', 'security_level']);

        $updateData = array_filter($updateData, fn($value) => !is_null($value));

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return $this->success(
            status: Response::HTTP_OK,
            message: 'User updated successfully',
            data: new UserResource($user->fresh())
        );
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'User deleted successfully',
            data: new UserResource($user)
        );
    }


}
