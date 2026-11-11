<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonRegistration;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $scheduleTimezone = config('sportcentrum.schedule_timezone');
        $now = now($scheduleTimezone);
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(4)->endOfDay();
        $todayStart = $now->copy()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();
        $dayNames = ['Ma', 'Di', 'Wo', 'Do', 'Vr'];
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
            ->whereBetween('starts_at', [$weekStart->copy()->utc(), $weekEnd->copy()->utc()])
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get();

        $stats = null;

        if ($request->user()?->role === 'admin') {
            $stats = [
                'participants' => LessonRegistration::query()
                    ->where('status', 'enrolled')
                    ->whereHas('lesson', fn ($query) => $query
                        ->where('starts_at', '>=', $todayStart->copy()->utc())
                        ->where('starts_at', '<', $tomorrowStart->copy()->utc()))
                    ->count(),
                'cancellations' => LessonRegistration::query()
                    ->where('canceled_at', '>=', $todayStart->copy()->utc())
                    ->where('canceled_at', '<', $tomorrowStart->copy()->utc())
                    ->count(),
                'lessons' => Lesson::query()
                    ->where('starts_at', '>=', $todayStart->copy()->utc())
                    ->where('starts_at', '<', $tomorrowStart->copy()->utc())
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
            'weekLessons' => $lessons->groupBy(fn (Lesson $lesson) => $lesson->starts_at->copy()->setTimezone($scheduleTimezone)->format('Y-m-d')),
            'weekRangeLabel' => $weekStart->format('d-m-Y').' t/m '.$weekEnd->format('d-m-Y'),
            'timeSlots' => range(6, 21),
            'stats' => $stats,
        ]);
    }
}
