<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateTrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('trainer')) ?? false;
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('trainer'))],
            'phone_number' => ['nullable', 'string', 'max:30'],
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
            'is_active.required' => 'Selecteer de accountstatus.',
            'is_active.boolean' => 'Selecteer een geldige accountstatus.',
        ];
    }
}
