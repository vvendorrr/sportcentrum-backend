@props(['title' => 'Sportcentrum de Linde'])

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body>
    <header>
        <nav aria-label="Main navigation">
            <a href="{{ route('home') }}">Sportcentrum de Linde</a>
            <a href="{{ route('lessons.index') }}">Lessons</a>
            @auth
                <a href="{{ route('registrations.index') }}">My registrations</a>
                @can('create', \App\Models\Lesson::class)
                    <a href="{{ route('lessons.create') }}">Create lesson</a>
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
