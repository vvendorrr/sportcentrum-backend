<?php

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

it('loads a schedule with dozens of lessons without per-lesson queries', function () {
    $this->travelTo('2026-10-10 09:00:00');
    $members = User::factory()->count(200)->create();

    foreach (range(1, 30) as $index) {
        $lesson = Lesson::factory()->create([
            'starts_at' => '2026-10-12 '.sprintf('%02d', 7 + ($index % 10)).':00:00',
            'sport' => 'Spinning',
        ]);

        foreach ($members->take(10) as $member) {
            $lesson->registrations()->create([
                'user_id' => $member->id,
                'status' => 'enrolled',
                'registered_at' => now(),
            ]);
        }
    }

    $selectQueries = [];
    DB::listen(function (QueryExecuted $query) use (&$selectQueries): void {
        if (str_starts_with(strtolower(ltrim($query->sql)), 'select')) {
            $selectQueries[] = $query->sql;
        }
    });

    $startedAt = hrtime(true);

    $this->get(route('lessons.index', ['week' => '2026-10-12']))
        ->assertOk()
        ->assertSeeText('Spinning');

    $elapsedMilliseconds = (hrtime(true) - $startedAt) / 1_000_000;

    expect(count($selectQueries))->toBeLessThanOrEqual(10)
        ->and($elapsedMilliseconds)->toBeLessThan(1000);
});
