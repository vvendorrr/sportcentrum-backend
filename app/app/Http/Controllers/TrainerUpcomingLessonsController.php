<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TrainerUpcomingLessonsController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewUpcomingLessons', Lesson::class);

        $lessons = Lesson::query()
            ->where('instructor_id', $request->user()->getKey())
            ->where('starts_at', '>', now())
            ->with([
                'registrations' => fn ($query) => $query
                    ->with('user:id,name')
                    ->whereIn('status', ['enrolled', 'waitlisted'])
                    ->orderBy('registered_at')
                    ->orderBy('id'),
            ])
            ->withCount([
                'registrations as enrolled_count' => fn ($query) => $query->where('status', 'enrolled'),
                'registrations as waitlisted_count' => fn ($query) => $query->where('status', 'waitlisted'),
            ])
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();

        return view('lessons.trainer-upcoming', ['lessons' => $lessons]);
    }
}
