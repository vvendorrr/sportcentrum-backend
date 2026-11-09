<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'voornaam',
            'last_name' => 'achternaam',
            'phone_number' => 'telefoonnummer',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Vul je voornaam in.',
            'first_name.string' => 'Je voornaam moet tekst zijn.',
            'first_name.max' => 'Je voornaam mag maximaal 255 tekens bevatten.',
            'last_name.required' => 'Vul je achternaam in.',
            'last_name.string' => 'Je achternaam moet tekst zijn.',
            'last_name.max' => 'Je achternaam mag maximaal 255 tekens bevatten.',
            'phone_number.string' => 'Je telefoonnummer moet tekst zijn.',
            'phone_number.max' => 'Je telefoonnummer mag maximaal 30 tekens bevatten.',
        ];
    }
}
