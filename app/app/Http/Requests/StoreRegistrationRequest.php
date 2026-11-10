<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        if (is_string($email)) {
            $this->merge(['email' => Str::lower(trim($email))]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vul je naam in.',
            'name.string' => 'Je naam moet tekst zijn.',
            'name.max' => 'Je naam mag maximaal 255 tekens bevatten.',
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Vul een geldig e-mailadres in.',
            'email.max' => 'Je e-mailadres mag maximaal 255 tekens bevatten.',
            'email.unique' => 'Er bestaat al een account met dit e-mailadres.',
            'phone_number.required' => 'Vul je telefoonnummer in.',
            'phone_number.string' => 'Je telefoonnummer moet tekst zijn.',
            'phone_number.max' => 'Je telefoonnummer mag maximaal 30 tekens bevatten.',
            'password.required' => 'Vul een wachtwoord in.',
            'password.min' => 'Je wachtwoord moet minimaal 8 tekens bevatten.',
            'password.confirmed' => 'De wachtwoorden komen niet overeen.',
        ];
    }
}
