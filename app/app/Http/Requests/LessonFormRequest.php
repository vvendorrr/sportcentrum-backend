<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class LessonFormRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'instructor_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', 'trainer')
                    ->where('is_active', 1),
            ],
            'starts_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'between:1,65535'],
            'category' => ['required', Rule::in(['strength', 'cardio', 'relaxation'])],
            'sport' => ['required', 'string', 'max:100'],
            'max_participants' => ['required', 'integer', 'between:1,65535'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'instructor_id.required' => 'Selecteer een trainer.',
            'instructor_id.integer' => 'Selecteer een geldige trainer.',
            'instructor_id.exists' => 'De geselecteerde trainer bestaat niet of is niet actief.',
            'starts_at.required' => 'Vul de startdatum en -tijd in.',
            'starts_at.date' => 'Vul een geldige startdatum en -tijd in.',
            'starts_at.after' => 'De startdatum en -tijd moeten in de toekomst liggen.',
            'duration_minutes.required' => 'Vul de duur van de les in.',
            'duration_minutes.integer' => 'De duur moet een geheel aantal minuten zijn.',
            'duration_minutes.between' => 'De duur moet tussen 1 en 65.535 minuten liggen.',
            'category.required' => 'Selecteer een categorie.',
            'category.in' => 'Selecteer een geldige categorie.',
            'sport.required' => 'Vul de sport in.',
            'sport.string' => 'De sport moet tekst zijn.',
            'sport.max' => 'De naam van de sport mag maximaal 100 tekens bevatten.',
            'max_participants.required' => 'Vul de maximale capaciteit in.',
            'max_participants.integer' => 'De capaciteit moet een geheel getal zijn.',
            'max_participants.between' => 'De maximale capaciteit moet tussen 1 en 65.535 deelnemers liggen.',
        ];
    }
}
