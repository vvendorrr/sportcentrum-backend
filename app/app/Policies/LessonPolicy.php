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

    public function viewOccupancy(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function viewUpcomingLessons(User $user): bool
    {
        return $user->role === 'trainer';
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
        return $user->role === 'admin'
            || ($user->role === 'trainer' && $lesson->instructor_id === $user->getKey());
    }

    public function manageAttendance(User $user, Lesson $lesson): bool
    {
        return ($user->role === 'admin'
                || ($user->role === 'trainer' && $lesson->instructor_id === $user->getKey()))
            && $lesson->starts_at->copy()->addMinutes($lesson->duration_minutes)->lessThanOrEqualTo(now());
    }

    public function register(User $user, Lesson $lesson): bool
    {
        return $user->role === 'member' && $user->is_active;
    }
}
