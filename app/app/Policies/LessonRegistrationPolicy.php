<?php

namespace App\Policies;

use App\Models\LessonRegistration;
use App\Models\User;

class LessonRegistrationPolicy
{
    public function cancelOnBehalf(User $actor, LessonRegistration $registration): bool
    {
        return $actor->role === 'admin'
            && $registration->user?->role === 'member'
            && in_array($registration->status, ['enrolled', 'waitlisted'], true);
    }
}
