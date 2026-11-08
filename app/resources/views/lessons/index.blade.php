<x-layouts.app title="Upcoming lessons">
    <h1>Upcoming lessons</h1>

    @forelse ($lessons as $lesson)
        <article>
            <h2>{{ $lesson->sport }}</h2>
            <p>{{ $lesson->starts_at->format('Y-m-d H:i') }} UTC · {{ $lesson->duration_minutes }} minutes</p>
            <p>Category: {{ ucfirst($lesson->category) }} · Instructor: {{ $lesson->instructor->name }}</p>
            <p>Enrolled: {{ $lesson->enrolled_count }} / {{ $lesson->max_participants }}</p>
            <p>Places remaining: {{ max(0, $lesson->max_participants - $lesson->enrolled_count) }}</p>

            @auth
                @if (auth()->user()->role === 'member')
                    @if ($registrationsByLesson->has($lesson->id))
                        <p>Your status: {{ $registrationsByLesson->get($lesson->id)->status }}</p>
                    @else
                        <form method="POST" action="{{ route('lessons.register', $lesson) }}">
                            @csrf
                            <button type="submit">Register or join the waitlist</button>
                        </form>
                    @endif
                @endif
            @endauth
        </article>
    @empty
        <p>There are no upcoming lessons.</p>
    @endforelse
</x-layouts.app>
