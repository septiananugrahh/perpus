<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
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
        
        $this->upgradePlainPassword();

        $user = \App\Models\User::where('email', $this->email)->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Cek plain text atau hash
        $passwordCorrect = ($this->password === $user->password) || \Illuminate\Support\Facades\Hash::check($this->password, $user->password);

        if (!$passwordCorrect) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }
    
    /**
 * Jika password di database masih plain text dan cocok dengan input,
 * langsung ubah menjadi hash sebelum Auth::attempt() dijalankan.
 */
protected function upgradePlainPassword(): void
{
    $user = User::where('email', $this->string('email'))->first();

    if (! $user || ! is_string($user->password) || $user->password === '') {
        return;
    }

    // Kalau sudah hash (bcrypt/argon), biarkan Auth::attempt() yang memeriksa
    if (Hash::info($user->password)['algoName'] !== 'unknown') {
        return;
    }

    // Masih plain text: bandingkan langsung, lalu simpan sebagai hash
    if (hash_equals($user->password, (string) $this->input('password'))) {
        $user->forceFill(['password' => $this->input('password')])->save();
    }
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
