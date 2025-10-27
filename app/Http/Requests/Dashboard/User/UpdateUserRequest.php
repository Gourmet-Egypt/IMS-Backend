<?php

namespace App\Http\Requests\Dashboard\User;

use App\Enums\UserRolesEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string|max:191',
            'email' => [
                'nullable',
                'email',
                'max:191',
                Rule::unique('users', 'email')->ignore($this->user->id)
            ],
            'store_id' => 'nullable|integer|exists:Store,ID',
            'user_number' => 'nullable|string|max:255',
            'role' => ['nullable', 'string', 'max:191', Rule::enum(UserRolesEnum::class)],
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'Name must be a valid string.',
            'name.max' => 'Name cannot exceed 191 characters.',

            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email cannot exceed 191 characters.',
            'email.unique' => 'This email is already taken.',


            'store_id.integer' => 'Store ID must be a valid number.',
            'store_id.exists' => 'The selected store does not exist.',

            'user_number.string' => 'User number must be a valid string.',
            'user_number.max' => 'User number cannot exceed 255 characters.',

            'role.string' => 'Role must be a valid string.',
            'role.max' => 'Role cannot exceed 191 characters.',
            'role.in' => 'Invalid role selected.',

            'security_level.integer' => 'Security level must be a valid number.',
            'security_level.min' => 'Security level must be at least 1.',
            'security_level.max' => 'Security level cannot exceed 10.',
        ];
    }
}
