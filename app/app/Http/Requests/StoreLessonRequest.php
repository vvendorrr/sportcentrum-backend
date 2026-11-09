<?php

namespace App\Http\Requests;

use App\Models\Lesson;

class StoreLessonRequest extends LessonFormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Lesson::class) ?? false;
    }
}
