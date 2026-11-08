<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Services\LessonRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LessonRegistrationController extends Controller
{
    public function store(Request $request, Lesson $lesson, LessonRegistrationService $registrations): RedirectResponse
    {
        Gate::authorize('register', $lesson);

        $registration = $registrations->register($request->user(), $lesson);
        $message = $registration->status === 'enrolled'
            ? 'You are enrolled in the lesson.'
            : 'The lesson is full. You have been added to the waitlist.';

        return redirect()
            ->route('lessons.index')
            ->with('status', $message);
    }

    public function destroy(
        Request $request,
        LessonRegistration $lessonRegistration,
        LessonRegistrationService $registrations,
    ): RedirectResponse {
        $registration = $request->user()->lessonRegistrations()
            ->whereKey($lessonRegistration->getKey())
            ->firstOrFail();

        $registrations->cancel($request->user(), $registration);

        return redirect()
            ->route('registrations.index')
            ->with('status', 'Your lesson registration has been cancelled.');
    }
}
