<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonIndexRequest;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class LessonController extends Controller
{
    public function index(LessonIndexRequest $request): View
    {
        $filters = $request->validated();
        $weekStart = Carbon::parse($filters['week'] ?? now()->toDateString())->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(4)->endOfDay();
        $sportSearch = trim($filters['sport'] ?? '');

        $query = Lesson::query()
            ->with('instructor')
            ->withCount([
                'registrations as enrolled_count' => fn($query) => $query->where('status', 'enrolled'),
            ])
            ->whereBetween('starts_at', [$weekStart, $weekEnd]);

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['instructor_id'])) {
            $query->where('instructor_id', $filters['instructor_id']);
        }

        if ($sportSearch !== '') {
            $query->where('sport', 'like', '%' . $sportSearch . '%');
        }

        $lessons = $query
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();

        $user = $request->user();
        $registrationsByLesson = $user === null
            ? collect()
            : $user->lessonRegistrations()
                ->whereIn('status', ['enrolled', 'waitlisted'])
                ->whereIn('lesson_id', $lessons->modelKeys())
                ->get(['lesson_id', 'status'])
                ->keyBy('lesson_id');

        $dayNames = ['Ma', 'Di', 'Wo', 'Do', 'Vr'];
        $weekDays = collect(range(0, 4))
            ->map(fn(int $offset): array => [
                'date' => $weekStart->copy()->addDays($offset),
                'name' => $dayNames[$offset],
            ]);
        $preservedFilters = array_filter([
            'category' => $filters['category'] ?? null,
            'instructor_id' => $filters['instructor_id'] ?? null,
            'sport' => $sportSearch !== '' ? $sportSearch : null,
        ], fn($value) => $value !== null && $value !== '');

        return view('lessons.index', [
            'lessons' => $lessons,
            'lessonsByDay' => $lessons->groupBy(fn(Lesson $lesson) => $lesson->starts_at->format('Y-m-d')),
            'registrationsByLesson' => $registrationsByLesson,
            'weekDays' => $weekDays,
            'timeSlots' => range(6, 19),
            'selectedWeek' => $weekStart,
            'selectedWeekLabel' => $weekStart->format('d-m-Y') . ' t/m ' . $weekEnd->format('d-m-Y'),
            'filters' => [
                'category' => $filters['category'] ?? '',
                'instructor_id' => $filters['instructor_id'] ?? '',
                'sport' => $sportSearch,
            ],
            'instructors' => User::query()->where('role', 'admin')->orderBy('name')->get(['id', 'name']),
            'previousWeekUrl' => route('lessons.index', [...$preservedFilters, 'week' => $weekStart->copy()->subWeek()->toDateString()]),
            'nextWeekUrl' => route('lessons.index', [...$preservedFilters, 'week' => $weekStart->copy()->addWeek()->toDateString()]),
        ]);
    }

    public function show(Request $request, Lesson $lesson): View
    {
        $lesson->load('instructor')->loadCount([
            'registrations as enrolled_count' => fn($query) => $query->where('status', 'enrolled'),
        ]);

        $registration = $request->user()?->lessonRegistrations()
            ->where('lesson_id', $lesson->getKey())
            ->whereIn('status', ['enrolled', 'waitlisted'])
            ->first();

        $canManageAttendance = $request->user()?->role === 'admin';

        $participants = $canManageAttendance
            ? $lesson->registrations()
                ->with('user')
                ->where('status', 'enrolled')
                ->orderBy('registered_at')
                ->get()
            : collect();

        return view('lessons.show', [
            'lesson' => $lesson,
            'registration' => $registration,

            'participants' => $participants,
            'canManageAttendance' => $canManageAttendance,
            'attendanceIsOpen' => now()->greaterThanOrEqualTo(
                $lesson->starts_at->copy()
                    ->addMinutes($lesson->duration_minutes)
            ),
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
