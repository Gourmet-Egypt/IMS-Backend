<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRolesEnum;
use App\Models\Cashier;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    use Responses;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required' , Rules\Password::defaults()],
            'user_number' => ['required', 'unique:' . User::class],
            'store_id' => ['required', 'exists:App\Models\Store,ID'],
            'role' => ['required', 'string',
                Rule::in(array_column(UserRolesEnum::cases(), 'value')),
            ],

        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name must not exceed 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'email.max' => 'The email must not exceed 255 characters.',

            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',

            'user_number.required' => 'The user_number field is required.',
            'user_number.unique' => 'This user_number is already registered.',


            'store_id.required' => 'The store field is required.',
            'store_id.exists' => 'The selected store does not exist.',

            'role.required' => 'The role field is required.',
            'role.string' => 'The role must be a valid string.',
            'role.exists' => 'The selected role is invalid.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {

        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            $this->error(
                status: 401 ,
                message: $firstError,
            )
        );
    }
}
