<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LessonController extends Controller
{
    public function index(Request $request): View
    {
        $lessons = Lesson::query()
            ->with('instructor')
            ->withCount([
                'registrations as enrolled_count' => fn ($query) => $query->where('status', 'enrolled'),
            ])
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();

        $registrationsByLesson = $request->user()->lessonRegistrations()
            ->whereIn('status', ['enrolled', 'waitlisted'])
            ->get(['lesson_id', 'status'])
            ->keyBy('lesson_id');

        return view('lessons.index', [
            'lessons' => $lessons,
            'registrationsByLesson' => $registrationsByLesson,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Lesson::class);

        return view('lessons.create', [
            'instructors' => User::query()
                ->where('role', 'admin')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(StoreLessonRequest $request): RedirectResponse
    {
        Lesson::query()->create($request->validated());

        return redirect()
            ->route('lessons.index')
            ->with('status', 'Lesson created.');
    }
}
