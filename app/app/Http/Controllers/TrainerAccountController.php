<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTrainerLessonsRequest;
use App\Http\Requests\StoreTrainerRequest;
use App\Http\Requests\UpdateTrainerRequest;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class TrainerAccountController extends Controller
{
    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('trainers.create');
    }

    public function store(StoreTrainerRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        $trainer = User::query()->create([
            'name' => trim($attributes['name']),
            'email' => $attributes['email'],
            'phone_number' => $attributes['phone_number'] ?? null,
            'password_hash' => $attributes['password'],
            'role' => 'admin',
            'is_active' => $attributes['is_active'],
        ]);

        return redirect()
            ->route('trainers.index', ['trainer' => $trainer->getKey()])
            ->with('status', 'Het traineraccount is aangemaakt.');
    }

    public function update(UpdateTrainerRequest $request, User $trainer): RedirectResponse
    {
        $attributes = $request->validated();

        $trainer->fill([
            'name' => trim($attributes['name']),
            'email' => $attributes['email'],
            'phone_number' => $attributes['phone_number'] ?? null,
            'is_active' => $attributes['is_active'],
        ])->save();

        return redirect()
            ->route('trainers.index', ['trainer' => $trainer->getKey()])
            ->with('status', 'Het traineraccount is bijgewerkt.');
    }

    public function assignLessons(AssignTrainerLessonsRequest $request, User $trainer): RedirectResponse
    {
        Lesson::query()
            ->whereIn('id', $request->validated('lessons'))
            ->where('starts_at', '>', now())
            ->update(['instructor_id' => $trainer->getKey()]);

        return redirect()
            ->route('trainers.index', ['trainer' => $trainer->getKey()])
            ->with('status', 'De geselecteerde lessen zijn toegewezen aan deze trainer.');
    }

    public function detachLesson(User $trainer, Lesson $lesson): RedirectResponse
    {
        Gate::authorize('assignLessons', $trainer);

        if ($lesson->instructor_id !== $trainer->getKey() || $lesson->starts_at->isPast()) {
            abort(404);
        }

        $lesson->update(['instructor_id' => null]);

        return redirect()
            ->route('trainers.index', ['trainer' => $trainer->getKey()])
            ->with('status', 'De toewijzing van deze les is opgeheven.');
    }
}
