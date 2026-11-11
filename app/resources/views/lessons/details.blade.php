@php
    $startsAt = $lesson->starts_at->copy()->setTimezone(config('sportcentrum.schedule_timezone'));
    $endsAt = $startsAt->copy()->addMinutes($lesson->duration_minutes);
    $memberCount = $lesson->registrations->where('status', 'enrolled')->count();
    $categoryLabels = [
        'strength' => 'Kracht',
        'cardio' => 'Cardio',
        'relaxation' => 'Ontspanning',
    ];
@endphp

<section aria-labelledby="lesson-details-heading">
    <h2 id="lesson-details-heading">Lesdetails</h2>

    <dl>
        <dt>Begintijd</dt>
        <dd>
            <time datetime="{{ $startsAt->toIso8601String() }}">
                {{ $startsAt->format('H:i') }}
            </time>
        </dd>

        <dt>Eindtijd</dt>
        <dd>
            <time datetime="{{ $endsAt->toIso8601String() }}">
                {{ $endsAt->format('H:i') }}
            </time>
        </dd>

        <dt>Categorie</dt>
        <dd>{{ $categoryLabels[$lesson->category] ?? $lesson->category }}</dd>

        <dt>Sport</dt>
        <dd>{{ $lesson->sport }}</dd>

        <dt>Deelnemers</dt>
        <dd>{{ $memberCount }} / {{ $lesson->max_participants }}</dd>
    </dl>

    <button type="button">{{ ($isRegistered ?? false) ? 'Afmelden' : 'Aanmelden' }}</button>
</section>
