<x-layouts.app title="My registrations">
    <h1>My upcoming registrations</h1>

    @forelse ($registrations as $registration)
        <article>
            <h2>{{ $registration->lesson->sport }}</h2>
            <p>{{ $registration->lesson->starts_at->format('Y-m-d H:i') }} UTC · {{ $registration->lesson->duration_minutes }} minutes</p>
            <p>Instructor: {{ $registration->lesson->instructor->name }}</p>
            <p>Status: <strong>{{ $registration->status }}</strong></p>

            @if (now()->lessThanOrEqualTo($registration->lesson->starts_at->copy()->subHours(2)))
                <form method="POST" action="{{ route('registrations.cancel', $registration) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Cancel registration</button>
                </form>
            @else
                <p>The cancellation deadline has passed.</p>
            @endif
        </article>
    @empty
        <p>You have no future lesson registrations.</p>
    @endforelse
</x-layouts.app>
