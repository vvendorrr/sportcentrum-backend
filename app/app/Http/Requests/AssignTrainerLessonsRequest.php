<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTrainerLessonsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('assignLessons', $this->route('trainer')) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'lessons' => ['required', 'array', 'min:1'],
            'lessons.*' => [
                'required',
                'integer',
                Rule::exists('lessons', 'id')
                    ->where(fn ($query) => $query->where('starts_at', '>', now())),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lessons.required' => 'Selecteer minimaal één les.',
            'lessons.array' => 'Selecteer minimaal één les.',
            'lessons.min' => 'Selecteer minimaal één les.',
            'lessons.*.required' => 'Selecteer minimaal één les.',
            'lessons.*.integer' => 'Selecteer geldige lessen.',
            'lessons.*.exists' => 'Een van de geselecteerde lessen bestaat niet (meer) of ligt in het verleden.',
        ];
    }
}
