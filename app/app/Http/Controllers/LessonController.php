<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

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

        $user = $request->user();
        $registrationsByLesson = $user === null
            ? collect()
            : $user->lessonRegistrations()
                ->whereIn('status', ['enrolled', 'waitlisted'])
                ->get(['lesson_id', 'status'])
                ->keyBy('lesson_id');

        return view('lessons.index', [
            'lessons' => $lessons,
            'registrationsByLesson' => $registrationsByLesson,
        ]);
    }

    public function show(Request $request, Lesson $lesson): View
    {
        $lesson->load('instructor')->loadCount([
            'registrations as enrolled_count' => fn ($query) => $query->where('status', 'enrolled'),
        ]);

        $registration = $request->user()?->lessonRegistrations()
            ->where('lesson_id', $lesson->getKey())
            ->whereIn('status', ['enrolled', 'waitlisted'])
            ->first();

        return view('lessons.show', [
            'lesson' => $lesson,
            'registration' => $registration,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Lesson::class);

        return view('lessons.create', [
            'instructors' => $this->instructors(),
        ]);
    }

    public function store(StoreLessonRequest $request): RedirectResponse
    {
        $lesson = Lesson::query()->create($request->validated());

        return redirect()
            ->route('lessons.show', $lesson)
            ->with('status', 'Les aangemaakt.');
    }

    public function edit(Lesson $lesson): View
    {
        Gate::authorize('update', $lesson);

        return view('lessons.edit', [
            'lesson' => $lesson,
            'instructors' => $this->instructors(),
        ]);
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson): RedirectResponse
    {
        $attributes = $request->validated();

        DB::transaction(function () use ($lesson, $attributes): void {
            $lockedLesson = Lesson::query()
                ->whereKey($lesson->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $enrolledCount = $lockedLesson->registrations()
                ->where('status', 'enrolled')
                ->count();

            if ($attributes['max_participants'] < $enrolledCount) {
                throw ValidationException::withMessages([
                    'max_participants' => 'De capaciteit kan niet lager zijn dan het huidige aantal ingeschreven leden.',
                ]);
            }

            $lockedLesson->update($attributes);
        }, attempts: 3);

        return redirect()
            ->route('lessons.show', $lesson)
            ->with('status', 'De les is bijgewerkt.');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        Gate::authorize('delete', $lesson);

        DB::transaction(function () use ($lesson): void {
            Lesson::query()
                ->whereKey($lesson->getKey())
                ->lockForUpdate()
                ->firstOrFail()
                ->delete();
        }, attempts: 3);

        return redirect()
            ->route('lessons.index')
            ->with('status', 'De les en bijbehorende inschrijvingen zijn verwijderd.');
    }

    /** @return Collection<int, User> */
    private function instructors(): Collection
    {
        return User::query()
            ->where('role', 'admin')
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
