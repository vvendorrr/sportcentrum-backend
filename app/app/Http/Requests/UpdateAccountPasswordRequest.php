<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateAccountPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password:web'],
            'new_password' => ['required', 'string', Password::min(8), 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Vul je huidige wachtwoord in.',
            'current_password.current_password' => 'Het opgegeven wachtwoord is onjuist.',
            'new_password.required' => 'Vul een nieuw wachtwoord in.',
            'new_password.min' => 'Je nieuwe wachtwoord moet minimaal 8 tekens bevatten.',
            'new_password.confirmed' => 'De nieuwe wachtwoorden komen niet overeen.',
        ];
    }
}
