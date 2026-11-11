<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Vul een geldig e-mailadres in.',
            'password.required' => 'Vul je wachtwoord in.',
            'password.string' => 'Het wachtwoord moet tekst zijn.',
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            'email' => Str::lower(trim((string) $this->input('email'))),
            'password' => (string) $this->input('password'),
            'is_active' => true,
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            $inactiveUser = User::query()
                ->where('email', $credentials['email'])
                ->where('is_active', false)
                ->first();

            if ($inactiveUser !== null && Auth::validate([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ])) {
                RateLimiter::clear($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => 'Je account is uitgeschakeld. Neem contact op met een beheerder.',
                ]);
            }

            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => 'De combinatie van e-mailadres en wachtwoord is onjuist.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Te veel inlogpogingen. Probeer het over {$seconds} seconden opnieuw.",
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower(trim((string) $this->input('email'))).'|'.$this->ip(),
        );
    }
}
