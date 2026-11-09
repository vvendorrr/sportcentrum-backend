<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonRegistration;
use App\Models\User;
use App\Support\DemoData;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    public function home(): View
    {
        $username = auth()->user()?->name;
        if ($username === null && $this->hasTable('users')) {
            $username = User::query()->value('name');
        }

        $lessons = $this->hasTable('lessons')
            ? Lesson::query()
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(3)
                ->get()
                ->map(fn (Lesson $lesson) => [
                    'name' => $lesson->sport,
                    'date' => $lesson->starts_at->toDateString(),
                    'time' => $lesson->starts_at->format('H:i'),
                    'category' => $this->categoryLabel($lesson->category),
                ])
                ->all()
            : [];

        return view('home', [
            'username' => $username ?: DemoData::username(),
            'lessons' => $lessons ?: DemoData::upcomingLessons(),
        ]);
    }

    public function lesson(): View
    {
        $lesson = $this->hasTable('lessons')
            ? Lesson::query()->with('registrations')->orderBy('starts_at')->first()
            : null;

        if ($lesson === null) {
            $lesson = Lesson::make(DemoData::lessonAttributes());
            $lesson->setRelation('registrations', collect(DemoData::lessonRegistrations()));
        }

        return view('lessons.details', ['lesson' => $lesson]);
    }

    public function account(): View
    {
        $user = auth()->user();
        if ($user === null && $this->hasTable('users')) {
            $user = User::query()->first();
        }

        if ($user !== null) {
            $nameParts = preg_split('/\s+/', trim($user->name), 2);
            $account = [
                'account_name' => strstr($user->email, '@', true) ?: $user->name,
                'email' => $user->email,
                'first_name' => $nameParts[0] ?? '',
                'last_name' => $nameParts[1] ?? '',
                'gender' => 'Niet opgegeven',
                'birth_date' => null,
            ];
        } else {
            $account = [
                'account_name' => '',
                'email' => '',
                'first_name' => '',
                'last_name' => '',
                'gender' => 'Niet opgegeven',
                'birth_date' => null,
            ];
        }

        $registeredLessons = $user !== null && $this->hasTable('lesson_registrations') && $this->hasTable('lessons')
            ? $user->lessonRegistrations()
                ->with('lesson')
                ->where('status', 'enrolled')
                ->whereHas('lesson', fn (Builder $query) => $query->where('starts_at', '>=', now()))
                ->get()
                ->map(fn (LessonRegistration $registration) => [
                    'sport' => $registration->lesson->sport,
                    'category' => $this->categoryLabel($registration->lesson->category),
                    'starts_at' => $registration->lesson->starts_at->toIso8601String(),
                ])
                ->all()
            : [];

        return view('users.account', [
            'account' => $account,
            'registeredLessons' => $registeredLessons,
            'regularLessons' => [],
        ]);
    }

    public function trainers(): View
    {
        $trainers = $this->hasTable('users') && $this->hasTable('lessons')
            ? User::query()
                ->whereHas('taughtLessons')
                ->orderBy('name')
                ->get()
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number ?? '',
                    'role' => $user->role,
                    'is_active' => (bool) $user->is_active,
                ])
                ->all()
            : [];

        $trainers = $trainers ?: DemoData::trainers();

        return view('users.admin-trainers', [
            'trainer' => $trainers[0],
            'trainers' => $trainers,
        ]);
    }

    private function hasTable(string $table): bool
    {
        $connection = DB::connection();

        if ($connection->getDriverName() === 'sqlite') {
            $databasePath = $connection->getDatabaseName();
            if ($databasePath !== ':memory:' && ! is_file($databasePath)) {
                return false;
            }
        }

        return Schema::connection($connection->getName())->hasTable($table);
    }

    private function categoryLabel(string $category): string
    {
        return [
            'strength' => 'Kracht',
            'cardio' => 'Cardio',
            'relaxation' => 'Ontspanning',
        ][$category] ?? $category;
    }
}
