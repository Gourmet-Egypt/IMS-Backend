<?php

namespace App\Http\Requests\Dashboard\User;

use App\Enums\UserRolesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function rules()
    {
        return [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'store_id' => 'required|integer|exists:Store,ID',
            'user_number' => 'required|string|max:255',
            'role' => ['required', 'string', 'max:191', Rule::enum(UserRolesEnum::class)],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'store_id.required' => 'Store ID is required.',
            'store_id.exists' => 'The selected store does not exist.',
            'role.required' => 'Role is required.',
            'role.in' => 'Invalid role selected.',
        ];
    }
}
