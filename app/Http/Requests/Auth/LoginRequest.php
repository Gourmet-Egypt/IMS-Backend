<?php

namespace App\Http\Requests\Auth;

use App\Http\Resources\Dashboard\AdminResource;
use App\Http\Resources\Dashboard\UserResource;
use App\Models\Admin;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    use Responses;

    protected array $guardConfig = [
        'web' => [
            'model' => User::class,
            'resource' => UserResource::class,
        ],
        'admin' => [
            'model' => Admin::class,
            'resource' => AdminResource::class,
        ],
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'password' => 'required|string',
            'guard' => 'sometimes|string|in:'.implode(',', array_keys($this->guardConfig)),
        ];
    }

    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        $guard = $this->input('guard', 'web');
        $credentials = $this->only('email', 'password');

        if (!isset($this->guardConfig[$guard])) {
            RateLimiter::hit($this->throttleKey());
            return $this->error(401, 'Invalid guard specified.');
        }

        $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'user_number';

        $modelClass = $this->guardConfig[$guard]['model'];
        $user = $modelClass::where($loginField, $credentials['email'])->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            return $this->error(404, $this->messages()['email.exists']);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($this->throttleKey());
            return $this->error(401, $this->messages()['password.invalid']);
        }

        RateLimiter::clear($this->throttleKey());

        return $user; // Return the user directly
    }

    public function ensureIsNotRateLimited(): void
    {
        $maxAttempts = 5;
        $decayMinutes = 1;

        if (!RateLimiter::tooManyAttempts($this->throttleKey(), $maxAttempts)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(
            'login|'.Str::lower($this->input('email', '')).'|'.$this->ip()
        );
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email or username is required.',
            'email.exists' => 'The provided email or username does not exist.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.invalid' => 'The provided password is incorrect.',
            'guard.in' => 'Invalid guard type specified.',
        ];
    }

    public function getResourceClass(string $guard): string
    {
        return $this->guardConfig[$guard]['resource'] ?? UserResource::class;
    }

    public function getGuardConfig(): array
    {
        return $this->guardConfig;
    }
}
