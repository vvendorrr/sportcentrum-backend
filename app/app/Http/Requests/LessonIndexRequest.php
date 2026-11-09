<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LessonIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'week' => ['nullable', 'date'],
            'category' => ['nullable', Rule::in(['strength', 'cardio', 'relaxation'])],
            'instructor_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'admin'),
            ],
            'sport' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'week.date' => 'Selecteer een geldige datum voor de week.',
            'category.in' => 'Selecteer een geldige categorie.',
            'instructor_id.integer' => 'Selecteer een geldige trainer.',
            'instructor_id.exists' => 'De geselecteerde trainer bestaat niet.',
            'sport.string' => 'De zoekterm moet tekst zijn.',
            'sport.max' => 'De zoekterm mag maximaal 100 tekens bevatten.',
        ];
    }
}
