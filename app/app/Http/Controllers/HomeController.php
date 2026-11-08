<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $lessons = Lesson::query()
            ->with('instructor')
            ->withCount([
                'registrations as enrolled_count' => fn ($query) => $query->where('status', 'enrolled'),
            ])
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->orderBy('id')
            ->limit(5)
            ->get();

        return view('home', ['lessons' => $lessons]);
    }
}
