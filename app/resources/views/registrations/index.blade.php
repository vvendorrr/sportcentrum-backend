<x-layouts.app title="My registrations">
    <h1 class="mb-6 text-3xl font-bold">My upcoming registrations</h1>

    @forelse ($registrations as $registration)
        <article class="mb-4 rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="text-xl font-semibold">{{ $registration->lesson->sport }}</h2>
            <p>{{ $registration->lesson->starts_at->format('Y-m-d H:i') }} UTC · {{ $registration->lesson->duration_minutes }} minutes</p>
            <p>Instructor: {{ $registration->lesson->instructor->name }}</p>
            <p>Status: <strong>{{ $registration->status }}</strong></p>

            @if (now()->lessThanOrEqualTo($registration->lesson->starts_at->copy()->subHours(2)))
                <form class="mt-3" method="POST" action="{{ route('registrations.cancel', $registration) }}">
                    @csrf
                    @method('DELETE')
                    <button class="rounded bg-red-700 px-4 py-2 text-white" type="submit">Cancel registration</button>
                </form>
            @else
                <p class="mt-3">The cancellation deadline has passed.</p>
            @endif
        </article>
    @empty
        <p>You have no future lesson registrations.</p>
    @endforelse
</x-layouts.app>
