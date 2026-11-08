<x-layouts.app title="Upcoming lessons">
    <h1 class="mb-6 text-3xl font-bold">Upcoming lessons</h1>

    @forelse ($lessons as $lesson)
        <article class="mb-4 rounded-lg border border-slate-200 bg-white p-5">
            <h2 class="text-xl font-semibold">{{ $lesson->sport }}</h2>
            <p>{{ $lesson->starts_at->format('Y-m-d H:i') }} UTC · {{ $lesson->duration_minutes }} minutes</p>
            <p>Category: {{ ucfirst($lesson->category) }} · Instructor: {{ $lesson->instructor->name }}</p>
            <p>{{ $lesson->enrolled_count }} / {{ $lesson->max_participants }} enrolled</p>

            @if (auth()->user()->role === 'member')
                @if ($registrationsByLesson->has($lesson->id))
                    <p class="mt-3">Your status: {{ $registrationsByLesson->get($lesson->id)->status }}</p>
                @else
                    <form class="mt-3" method="POST" action="{{ route('lessons.register', $lesson) }}">
                        @csrf
                        <button class="rounded bg-blue-700 px-4 py-2 text-white" type="submit">Register</button>
                    </form>
                @endif
            @endif
        </article>
    @empty
        <p>There are no upcoming lessons.</p>
    @endforelse
</x-layouts.app>
