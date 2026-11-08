<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function register(User $user, Lesson $lesson): bool
    {
        return $user->role === 'member';
    }
}
