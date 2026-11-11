<?php

namespace App\Http\Requests;

use App\Models\Lesson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLessonAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lesson = $this->route('lesson');

        return $lesson instanceof Lesson
            && ($this->user()?->can('manageAttendance', $lesson) ?? false);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $lesson = $this->route('lesson');

        return [
            'attendance' => ['required', 'array', 'min:1'],
            'attendance.*' => ['required', 'array'],
            'attendance.*.registration_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('lesson_registrations', 'id')
                    ->where(fn ($query) => $query
                        ->where('lesson_id', $lesson->getKey())
                        ->where('status', 'enrolled')),
            ],
            'attendance.*.status' => ['present', 'nullable', Rule::in(['attended', 'absent'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'attendance.required' => 'Er zijn geen aanwezigheidsgegevens ontvangen.',
            'attendance.array' => 'De aanwezigheidsgegevens zijn ongeldig.',
            'attendance.min' => 'Er zijn geen deelnemers om bij te werken.',
            'attendance.*.registration_id.exists' => 'Een inschrijving hoort niet bij deze les of is niet actief.',
            'attendance.*.status.in' => 'Kies aanwezig, afwezig of geen registratie.',
        ];
    }
}
