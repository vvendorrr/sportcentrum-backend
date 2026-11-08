@props(['title' => 'Sportcentrum De Linde'])

<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body>
    <header>
        <nav aria-label="Hoofdnavigatie">
            <a href="{{ route('home') }}">Sportcentrum De Linde</a>
            <a href="{{ route('lessons.index') }}">Rooster</a>
            @auth
                <a href="{{ route('registrations.index') }}">Mijn inschrijvingen</a>
                @can('create', \App\Models\Lesson::class)
                    <a href="{{ route('lessons.create') }}">Les aanmaken</a>
                @endcan
            @endauth
        </nav>
    </header>

    @if (session('status'))
        <p role="status">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main>
        {{ $slot }}
    </main>
</body>
</html>
