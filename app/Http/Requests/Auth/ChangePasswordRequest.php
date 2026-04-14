<?php

namespace App\Http\Requests\Auth;

use App\Traits\Responses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
{
    use Responses;

    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => 'bail|required|string',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->has('old_password')) {
                if (!Hash::check($this->old_password, $this->user()->password)) {
                    $validator->errors()->add('old_password', $this->messages()['old_password.current_password']);
                } elseif ($this->old_password === $this->password) {
                    $validator->errors()->add('password', $this->messages()['password.different']);
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();
        $firstKey = $validator->errors()->keys()[0] ?? null;

        $status = ($firstKey === 'old_password') ? 400 : 422;

        throw new HttpResponseException(
            $this->error($status, $firstError, null)
        );
    }

    public function messages(): array
    {
        return [
            'old_password.required' => 'Old password is required.',
            'old_password.string' => 'Old password must be a valid string.',
            'old_password.current_password' => 'The provided old password is incorrect.',

            'password.required' => 'New password is required.',
            'password.string' => 'New password must be a valid string.',
            'password.min' => 'New password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.different' => 'New password must be different from old password.',
        ];
    }
}
