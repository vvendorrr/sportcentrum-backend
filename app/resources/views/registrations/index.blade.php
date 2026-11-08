<x-layouts.app title="Mijn inschrijvingen">
    <h1>Mijn komende inschrijvingen</h1>

    @forelse ($registrations as $registration)
        <article>
            <h2>{{ $registration->lesson->sport }}</h2>
            <p>{{ $registration->lesson->starts_at->format('d-m-Y H:i') }} UTC · {{ $registration->lesson->duration_minutes }} minuten</p>
            <p>Trainer: {{ $registration->lesson->instructor->name }}</p>
            <p>Status: <strong>{{ $registration->status === 'enrolled' ? 'Ingeschreven' : 'Op de wachtlijst' }}</strong></p>

            @if (now()->lessThanOrEqualTo($registration->lesson->starts_at->copy()->subHours(2)))
                <form method="POST" action="{{ route('registrations.cancel', $registration) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Afmelden</button>
                </form>
            @else
                <p>De afmeldtermijn is verstreken.</p>
            @endif
        </article>
    @empty
        <p>Je hebt geen toekomstige lesinschrijvingen.</p>
    @endforelse
</x-layouts.app>
