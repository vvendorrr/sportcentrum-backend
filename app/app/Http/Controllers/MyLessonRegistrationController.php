<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MyLessonRegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $registrations = $request->user()->lessonRegistrations()
            ->with(['lesson.instructor'])
            ->whereIn('status', ['enrolled', 'waitlisted'])
            ->whereHas('lesson', fn ($query) => $query->where('starts_at', '>', now()))
            ->orderBy(
                Lesson::query()
                    ->select('starts_at')
                    ->whereColumn('lessons.id', 'lesson_registrations.lesson_id'),
            )
            ->orderBy('lesson_registrations.id')
            ->get();

        return view('registrations.index', ['registrations' => $registrations]);
    }

public function attendance(
    Request $request,
    Lesson $lesson,
    LessonRegistration $lessonRegistration
): RedirectResponse {
    // Alleen bevoegde beheerders mogen aanwezigheid registreren.
    Gate::authorize('create', Lesson::class);

    // Controleer of de deelnemer daadwerkelijk ingeschreven is.
    abort_unless(
        $lesson->registrations()
            ->whereKey($lessonRegistration->getKey())
            ->where('status', 'enrolled')
            ->exists(),
        404
    );

    // Aanwezigheid pas registreren nadat de les afgelopen is.
    abort_if(
        now()->lt(
            $lesson->starts_at->copy()
                ->addMinutes($lesson->duration_minutes)
        ),
        422,
        'Aanwezigheid kan pas na afloop van de les worden geregistreerd.'
    );

    // Controleer de invoer.
    $validated = $request->validate([
        'attendance' => ['required', 'in:attended,absent'],
    ]);

    // Sla de aanwezigheid op.
    $lessonRegistration->update([
        'attendance' => $validated['attendance'],
    ]);

    return redirect()
        ->route('lessons.show', $lesson)
        ->with('status', 'Aanwezigheid opgeslagen.');
}

}
