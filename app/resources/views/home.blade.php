<x-layouts.app title="Home">
    <section aria-labelledby="welcome-heading">
        <h1 id="welcome-heading">Welcome to Sportcentrum de Linde</h1>
        <p>Browse upcoming group lessons and find one that suits you.</p>
        <p><a href="{{ route('lessons.index') }}">View the lesson schedule</a></p>
        @auth
            <p><a href="{{ route('registrations.index') }}">View my registrations</a></p>
        @endauth
    </section>

    <section aria-labelledby="upcoming-lessons-heading">
        <h2 id="upcoming-lessons-heading">Upcoming lessons</h2>

        @if ($lessons->isEmpty())
            <p>There are no upcoming lessons.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th scope="col">Start time (UTC)</th>
                        <th scope="col">Sport</th>
                        <th scope="col">Category</th>
                        <th scope="col">Instructor</th>
                        <th scope="col">Enrolled</th>
                        <th scope="col">Places remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lessons as $lesson)
                        <tr>
                            <td>{{ $lesson->starts_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $lesson->sport }}</td>
                            <td>{{ ucfirst($lesson->category) }}</td>
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
