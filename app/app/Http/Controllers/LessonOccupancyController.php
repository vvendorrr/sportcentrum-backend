<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class LessonOccupancyController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewOccupancy', Lesson::class);

        $lessons = Lesson::query()
            ->with('instructor:id,name')
            ->withCount([
                'registrations as enrolled_count' => fn ($query) => $query->where('status', 'enrolled'),
            ])
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();

        return view('lessons.occupancy', ['lessons' => $lessons]);
    }
}
