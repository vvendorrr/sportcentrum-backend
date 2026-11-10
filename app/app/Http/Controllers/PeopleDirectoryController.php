<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PeopleDirectoryController extends Controller
{
    public function participants(Request $request): View
    {
        Gate::authorize('create', Lesson::class);

        $participants = User::query()
            ->where('role', 'member')
            ->orderBy('name')
            ->get();
        $selectedPerson = $participants->firstWhere('id', $request->integer('participant'))
            ?? $participants->first();
        $items = $selectedPerson?->lessonRegistrations()
            ->with('lesson')
            ->whereIn('status', ['enrolled', 'waitlisted'])
            ->orderBy(
                Lesson::query()
                    ->select('starts_at')
                    ->whereColumn('lessons.id', 'lesson_registrations.lesson_id'),
            )
            ->get() ?? collect();

        return view('directory.index', [
            'directoryType' => 'participants',
            'title' => 'Deelnemers',
            'people' => $participants,
            'selectedPerson' => $selectedPerson,
            'items' => $items,
            'selectionParameter' => 'participant',
            'listHeading' => 'Alle deelnemers',
            'itemsHeading' => 'Inschrijvingen',
            'emptyItemsMessage' => 'Deze deelnemer heeft nog geen inschrijvingen.',
            'missedLessonsCount' => $selectedPerson
                ? $selectedPerson->lessonRegistrations()
                    ->where('attendance', 'absent')
                    ->count()
                : 0,

        ]);
    }

    public function trainers(Request $request): View
    {
        Gate::authorize('create', Lesson::class);

        $trainers = User::query()
            ->where('role', 'admin')
            ->withCount([
                'taughtLessons as upcoming_lessons_count' => fn($query) => $query->where('starts_at', '>', now()),
            ])
            ->orderBy('name')
            ->get();
        $selectedPerson = $trainers->firstWhere('id', $request->integer('trainer'))
            ?? $trainers->first();
        $items = $selectedPerson?->taughtLessons()
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->get() ?? collect();

        return view('directory.index', [
            'directoryType' => 'trainers',
            'title' => 'Trainers',
            'people' => $trainers,
            'selectedPerson' => $selectedPerson,
            'items' => $items,
            'selectionParameter' => 'trainer',
            'listHeading' => 'Alle trainers',
            'itemsHeading' => 'Aankomende lessen',
            'emptyItemsMessage' => 'Deze trainer heeft geen aankomende lessen.',
        ]);
    }
}
