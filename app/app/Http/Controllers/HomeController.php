<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonRegistration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $now = now();
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->addDays(4)->endOfDay();
        $todayStart = $now->copy()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();
        $dayNames = ['maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag'];
        $weekDays = collect(range(0, 4))
            ->map(fn (int $offset): array => [
                'date' => $weekStart->copy()->addDays($offset),
                'name' => $dayNames[$offset],
            ]);

        $lessons = Lesson::query()
            ->with('instructor')
            ->withCount([
                'registrations as enrolled_count' => fn ($query) => $query->where('status', 'enrolled'),
            ])
            ->whereBetween('starts_at', [$weekStart, $weekEnd])
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();

        $stats = null;

        if ($request->user()?->role === 'admin') {
            $stats = [
                'participants' => LessonRegistration::query()
                    ->where('status', 'enrolled')
                    ->whereHas('lesson', fn ($query) => $query
                        ->where('starts_at', '>=', $todayStart)
                        ->where('starts_at', '<', $tomorrowStart))
                    ->count(),
                'cancellations' => LessonRegistration::query()
                    ->where('canceled_at', '>=', $todayStart)
                    ->where('canceled_at', '<', $tomorrowStart)
                    ->count(),
                'lessons' => Lesson::query()
                    ->where('starts_at', '>=', $todayStart)
                    ->where('starts_at', '<', $tomorrowStart)
                    ->count(),
            ];
        }

        $greeting = match (true) {
            $now->hour < 12 => 'Goedemorgen',
            $now->hour < 18 => 'Goedemiddag',
            default => 'Goedenavond',
        };

        return view('home', [
            'greeting' => $greeting,
            'weekDays' => $weekDays,
            'weekLessons' => $lessons->groupBy(fn (Lesson $lesson) => $lesson->starts_at->format('Y-m-d')),
            'weekRangeLabel' => $weekStart->format('d-m-Y').' t/m '.$weekEnd->format('d-m-Y'),
            'timeSlots' => range(6, 21),
            'stats' => $stats,
        ]);
    }
}
