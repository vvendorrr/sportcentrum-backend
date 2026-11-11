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

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->role === 'admin';
    }

    public function viewParticipants(User $user, Lesson $lesson): bool
    {
        return $user->role === 'admin';
    }

    public function manageAttendance(User $user, Lesson $lesson): bool
    {
        return $user->role === 'admin'
            && $lesson->starts_at->copy()->addMinutes($lesson->duration_minutes)->lessThanOrEqualTo(now());
    }

    public function register(User $user, Lesson $lesson): bool
    {
        return $user->role === 'member' && $user->is_active;
    }
}
