<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string')]
    public string $email = ''; 

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginField = filter_var($this->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        $value = $this->email;

        // Jika user login pakai No HP, kita pastikan formatnya +62 (sesuai database)
        if ($loginField === 'phone') {
            // Jika input user mulai dengan '0', ganti jadi '+62'
            // Jika input user mulai dengan '8', tambah '+62'
            if (str_starts_with($value, '0')) {
                $value = '+62' . substr($value, 1);
            } elseif (str_starts_with($value, '8')) {
                $value = '+62' . $value;
            }
        }

        // Coba Login
        if (! Auth::attempt([$loginField => $value, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}