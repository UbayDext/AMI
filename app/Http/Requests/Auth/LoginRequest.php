<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = \App\Models\User::where('email', $this->email)->first();

        if ($user) {
            if ($user->is_blocked) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    redirect()->route('login')->with('error', 'Account anda telah terblokir karena terlalu sering mencoba login')
                );
            }

            // Jika ganti bulan, reset attempts
            if ($user->last_failed_login_at && $user->last_failed_login_at->format('Y-m') !== now()->format('Y-m')) {
                $user->update([
                    'failed_login_attempts' => 0,
                    'last_failed_login_at' => null,
                ]);
            }
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            if ($user) {
                $user->increment('failed_login_attempts');
                $user->update(['last_failed_login_at' => now()]);

                if ($user->failed_login_attempts >= 3) {
                    $user->update(['is_blocked' => true]);
                    throw new \Illuminate\Http\Exceptions\HttpResponseException(
                        redirect()->route('login')->with('error', 'Account anda telah terblokir karena terlalu sering mencoba login')
                    );
                } else {
                    $remaining = 3 - $user->failed_login_attempts;
                    throw ValidationException::withMessages([
                        'email' => trans('auth.failed') . " (Sisa percobaan bulan ini: {$remaining} kali)",
                    ]);
                }
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if ($user && $user->failed_login_attempts > 0) {
            $user->update([
                'failed_login_attempts' => 0,
                'last_failed_login_at' => null,
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
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

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
