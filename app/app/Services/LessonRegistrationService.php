<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Models\User;
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
                    'lesson' => 'You cannot register for a lesson that has already started.',
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
        return DB::transaction(function () use ($member, $registrationReference): LessonRegistration {
            $registrationId = $registrationReference->getKey();
            $lesson = Lesson::query()
                ->whereKey($registrationReference->lesson_id)
                ->lockForUpdate()
                ->firstOrFail();

            $registration = $member->lessonRegistrations()
                ->whereKey($registrationId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($registration->status === 'cancelled') {
                return $registration;
            }

            $now = now();
            $cancellationDeadline = $lesson->starts_at->copy()->subHours(2);

            if ($now->greaterThan($cancellationDeadline)) {
                throw ValidationException::withMessages([
                    'registration' => 'You can only cancel a lesson at least two hours before it starts.',
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
