<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreTrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'naam',
            'email' => 'e-mailadres',
            'phone_number' => 'telefoonnummer',
            'password' => 'wachtwoord',
            'is_active' => 'accountstatus',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vul de naam van de trainer in.',
            'name.string' => 'De naam moet tekst zijn.',
            'name.max' => 'De naam mag maximaal 255 tekens bevatten.',
            'email.required' => 'Vul het e-mailadres van de trainer in.',
            'email.email' => 'Vul een geldig e-mailadres in.',
            'email.max' => 'Het e-mailadres mag maximaal 255 tekens bevatten.',
            'email.unique' => 'Er bestaat al een account met dit e-mailadres.',
            'phone_number.string' => 'Het telefoonnummer moet tekst zijn.',
            'phone_number.max' => 'Het telefoonnummer mag maximaal 30 tekens bevatten.',
            'password.required' => 'Vul een wachtwoord in.',
            'password.min' => 'Het wachtwoord moet minimaal 8 tekens bevatten.',
            'password.confirmed' => 'De wachtwoorden komen niet overeen.',
            'is_active.required' => 'Selecteer de accountstatus.',
            'is_active.boolean' => 'Selecteer een geldige accountstatus.',
        ];
    }
}
