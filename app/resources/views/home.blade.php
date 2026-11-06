<!DOCTYPE html>
<html lang="nl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Startpagina</title>
    </head>
    <body>
        <main>
            <h1 id="greeting">Welkom, {{ $username }}!</h1>
            <small>Welkom bij Sportcentrum. Kies een les en blijf in beweging.</small>

            <table>
                <caption>Voorbeeld van aankomende lessen</caption>
                <thead>
                    <tr>
                        <th scope="col">Les</th>
                        <th scope="col">Datum</th>
                        <th scope="col">Tijd</th>
                        <th scope="col">Categorie</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lessons as $lesson)
                        <tr>
                            <td>{{ $lesson['name'] }}</td>
                            <td>
                                <time datetime="{{ $lesson['date'] }}">
                                    {{ \Illuminate\Support\Carbon::parse($lesson['date'])->locale('nl')->translatedFormat('j F Y') }}
                                </time>
                            </td>
                            <td>{{ $lesson['time'] }}</td>
                            <td>{{ $lesson['category'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </main>

        <script>
            const hour = new Date().getHours();
            const timeOfDay = hour < 12
                ? 'Goedemorgen'
                : hour < 18
                    ? 'Goedemiddag'
                    : 'Goedenavond';
            const username = @json($username);

            document.getElementById('greeting').textContent = `${timeOfDay}, ${username}!`;
        </script>
    </body>
</html>
