<x-layouts.app title="Komende lessen">
    <h1>Komende lessen</h1>

    @forelse ($lessons as $lesson)
        <article>
            <h2>{{ $lesson->sport }}</h2>
            <p>{{ $lesson->starts_at->format('d-m-Y H:i') }} UTC · {{ $lesson->duration_minutes }} minuten</p>
            <p>Categorie: {{ ['strength' => 'Kracht', 'cardio' => 'Cardio', 'relaxation' => 'Ontspanning'][$lesson->category] ?? $lesson->category }} · Trainer: {{ $lesson->instructor->name }}</p>
            <p>Ingeschreven: {{ $lesson->enrolled_count }} / {{ $lesson->max_participants }}</p>
            <p>Beschikbare plekken: {{ max(0, $lesson->max_participants - $lesson->enrolled_count) }}</p>

            @auth
                @if (auth()->user()->role === 'member')
                    @if ($registrationsByLesson->has($lesson->id))
                        <p>Jouw status: {{ $registrationsByLesson->get($lesson->id)->status === 'enrolled' ? 'Ingeschreven' : 'Op de wachtlijst' }}</p>
                    @else
                        <form method="POST" action="{{ route('lessons.register', $lesson) }}">
                            @csrf
                            <button type="submit">
                                {{ $lesson->enrolled_count < $lesson->max_participants ? 'Inschrijven' : 'Op de wachtlijst zetten' }}
                            </button>
                        </form>
                    @endif
                @endif
            @endauth
        </article>
    @empty
        <p>Er zijn geen komende lessen.</p>
    @endforelse
</x-layouts.app>
