<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Dashboard\User\StoreUserRequest;
use App\Http\Requests\Dashboard\User\UpdateUserRequest;
use App\Http\Resources\Dashboard\UserResource;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use Responses;

    public function index()
    {
        $users = User::on('sqlsrv_rms')->paginate(10);

        return $this->successPaginated(
            status: Response::HTTP_OK,
            message: 'Users retrieved successfully',
            data: UserResource::collection($users)
        );
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->only(['name', 'email', 'store_id', 'user_number', 'role']);

        $data['password'] = Hash::make('password');
        $data['security_level'] = $data['security_level'] ?? 4;

        $user = User::on('sqlsrv_rms')->create($data);

        return $this->success(
            status: Response::HTTP_CREATED,
            message: 'User created successfully',
            data: new UserResource($user)
        );
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::on('sqlsrv_rms')->find($id);

        if (!$user) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'User not found'
            );
        }

        return $this->success(
            status: Response::HTTP_OK,
            message: 'User retrieved successfully',
            data: new UserResource($user)
        );
    }

    /**
     * Update the specified user
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = User::on('sqlsrv_rms')->find($id);

        if (!$user) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'User not found'
            );
        }

        $updateData = $request->only(['name', 'email', 'store_id', 'user_number', 'role']);

        $updateData = array_filter($updateData, fn($value) => !is_null($value));

        $updateData['password'] = Hash::make('password');

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
    public function destroy($id)
    {
        $user = User::on('sqlsrv_rms')->find($id);

        if (!$user) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'User not found'
            );
        }
        $user->delete();

        return $this->success(
            status: Response::HTTP_OK,
            message: 'User deleted successfully',
            data: new UserResource($user)
        );
    }


}
