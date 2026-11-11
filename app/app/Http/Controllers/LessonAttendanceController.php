<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLessonAttendanceRequest;
use App\Models\Lesson;
use App\Models\LessonRegistration;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class LessonAttendanceController extends Controller
{
    public function show(Lesson $lesson): View
    {
        Gate::authorize('viewParticipants', $lesson);

        /** @var Collection<int, LessonRegistration> $registrations */
        $registrations = $lesson->registrations()
            ->with('user:id,name')
            ->where('status', 'enrolled')
            ->get()
            ->sortBy(fn (LessonRegistration $registration) => $registration->user->name, SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return view('lessons.participants', [
            'lesson' => $lesson,
            'registrations' => $registrations,
            'lessonHasEnded' => $lesson->starts_at->copy()->addMinutes($lesson->duration_minutes)->lessThanOrEqualTo(now()),
        ]);
    }

    public function update(UpdateLessonAttendanceRequest $request, Lesson $lesson): RedirectResponse
    {
        $entries = $request->validated('attendance');

        DB::transaction(function () use ($entries, $lesson): void {
            $lockedLesson = Lesson::query()
                ->whereKey($lesson->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            Gate::authorize('manageAttendance', $lockedLesson);

            $registrationIds = collect($entries)->pluck('registration_id');
            $registrations = $lockedLesson->registrations()
                ->whereIn('id', $registrationIds)
                ->where('status', 'enrolled')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($registrations->count() !== $registrationIds->unique()->count()) {
                throw ValidationException::withMessages([
                    'attendance' => 'Een of meer inschrijvingen zijn gewijzigd. Vernieuw de pagina en probeer het opnieuw.',
                ]);
            }

            foreach ($entries as $entry) {
                $registration = $registrations->get($entry['registration_id']);
                $registration->attendance = $entry['status'];
                $registration->save();
            }
        }, attempts: 3);

        return redirect()
            ->route('lessons.participants', $lesson)
            ->with('status', 'De aanwezigheid is bijgewerkt.');
    }
}
