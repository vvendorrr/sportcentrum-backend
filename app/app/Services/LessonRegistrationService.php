<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LessonRegistrationService
{
    public function register(User $member, Lesson $lesson): LessonRegistration
    {
        return DB::transaction(function () use ($member, $lesson): LessonRegistration {
            $lockedLesson = Lesson::query()
                ->whereKey($lesson->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedLesson->starts_at->lessThanOrEqualTo(now())) {
                throw ValidationException::withMessages([
                    'lesson' => 'Je kunt je niet meer inschrijven voor een les die al is begonnen.',
                ]);
            }

            $registration = $lockedLesson->registrations()
                ->where('user_id', $member->getKey())
                ->lockForUpdate()
                ->first();

            if ($registration !== null && $registration->status !== 'cancelled') {
                return $registration;
            }

            $status = $lockedLesson->registrations()
                ->where('status', 'enrolled')
                ->count() < $lockedLesson->max_participants
                    ? 'enrolled'
                    : 'waitlisted';

            $attributes = [
                'status' => $status,
                'registered_at' => now(),
                'attendance' => null,
                'canceled_at' => null,
            ];

            if ($registration !== null) {
                $registration->update($attributes);

                return $registration->refresh();
            }

            return $lockedLesson->registrations()->create([
                ...$attributes,
                'user_id' => $member->getKey(),
            ]);
        }, attempts: 3);
    }

    public function cancel(User $member, LessonRegistration $registrationReference): LessonRegistration
    {
        return $this->cancelRegistration($registrationReference, $member, false);
    }

    public function cancelOnBehalf(User $actor, LessonRegistration $registrationReference): LessonRegistration
    {
        if ($actor->role !== 'admin') {
            throw new AuthorizationException;
        }

        return $this->cancelRegistration($registrationReference, null, true);
    }

    private function cancelRegistration(
        LessonRegistration $registrationReference,
        ?User $member,
        bool $onBehalf,
    ): LessonRegistration {
        return DB::transaction(function () use ($member, $onBehalf, $registrationReference): LessonRegistration {
            $lesson = Lesson::query()
                ->whereKey($registrationReference->lesson_id)
                ->lockForUpdate()
                ->firstOrFail();

            $registrationQuery = LessonRegistration::query()
                ->whereKey($registrationReference->getKey());

            if ($member !== null) {
                $registrationQuery->where('user_id', $member->getKey());
            }

            $registration = $registrationQuery->lockForUpdate()->firstOrFail();

            if ($registration->status === 'cancelled') {
                return $registration;
            }

            $now = now();

            if ($onBehalf && $now->greaterThanOrEqualTo($lesson->starts_at)) {
                throw ValidationException::withMessages([
                    'registration' => 'Een afmelding namens een lid kan alleen vóór aanvang van de les worden geregistreerd.',
                ]);
            }

            if (! $onBehalf && $now->greaterThan($lesson->starts_at->copy()->subHours(2))) {
                throw ValidationException::withMessages([
                    'registration' => 'Je kunt je tot uiterlijk twee uur voor aanvang van de les afmelden.',
                ]);
            }

            $wasEnrolled = $registration->status === 'enrolled';

            $registration->update([
                'status' => 'cancelled',
                'canceled_at' => $now,
            ]);

            if ($wasEnrolled) {
                $enrolledCount = $lesson->registrations()
                    ->where('status', 'enrolled')
                    ->count();

                if ($enrolledCount < $lesson->max_participants) {
                    $nextWaitlisted = $lesson->registrations()
                        ->where('status', 'waitlisted')
                        ->orderBy('registered_at')
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->first();

                    $nextWaitlisted?->update(['status' => 'enrolled']);
                }
            }

            return $registration->refresh();
        }, attempts: 3);
    }
}
