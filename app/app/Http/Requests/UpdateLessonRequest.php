<?php

namespace App\Http\Requests;

use App\Models\Lesson;

class UpdateLessonRequest extends LessonFormRequest
{
    public function authorize(): bool
    {
        $lesson = $this->route('lesson');

        return $lesson instanceof Lesson
            && ($this->user()?->can('update', $lesson) ?? false);
    }
}
