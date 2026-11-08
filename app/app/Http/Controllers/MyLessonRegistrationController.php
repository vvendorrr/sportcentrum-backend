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
}
