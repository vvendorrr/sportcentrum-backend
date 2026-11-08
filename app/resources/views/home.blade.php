<x-layouts.app title="Startpagina">
    <section aria-labelledby="welkom-heading">
        <h1 id="welkom-heading">Welkom bij Sportcentrum De Linde</h1>
        <p>Bekijk de komende groepslessen en kies een les die bij je past.</p>
        <p><a href="{{ route('lessons.index') }}">Bekijk het lesrooster</a></p>
    </section>

    <section aria-labelledby="komende-lessen-heading">
        <h2 id="komende-lessen-heading">Komende lessen</h2>

        @if ($lessons->isEmpty())
            <p>Er zijn geen komende lessen.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th scope="col">Starttijd (UTC)</th>
                        <th scope="col">Sport</th>
                        <th scope="col">Categorie</th>
                        <th scope="col">Trainer</th>
                        <th scope="col">Ingeschreven</th>
                        <th scope="col">Beschikbare plekken</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lessons as $lesson)
                        <tr>
                            <td>{{ $lesson->starts_at->format('d-m-Y H:i') }}</td>
                            <td>{{ $lesson->sport }}</td>
                            <td>{{ ['strength' => 'Kracht', 'cardio' => 'Cardio', 'relaxation' => 'Ontspanning'][$lesson->category] ?? $lesson->category }}</td>
                            <td>{{ $lesson->instructor->name }}</td>
                            <td>{{ $lesson->enrolled_count }} / {{ $lesson->max_participants }}</td>
                            <td>{{ max(0, $lesson->max_participants - $lesson->enrolled_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
</x-layouts.app>
