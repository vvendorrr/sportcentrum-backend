<?php

namespace App\Http\Requests;

use App\Models\Lesson;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Lesson::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'instructor_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'admin'),
            ],
            'starts_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'between:1,65535'],
            'category' => ['required', Rule::in(['strength', 'cardio', 'relaxation'])],
            'sport' => ['required', 'string', 'max:100'],
            'max_participants' => ['required', 'integer', 'between:1,65535'],
        ];
    }
}
