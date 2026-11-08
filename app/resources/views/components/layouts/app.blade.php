@props(['title' => 'Sportcentrum de Linde'])

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-5xl items-center gap-6 px-6 py-4" aria-label="Main navigation">
            <a class="font-semibold" href="{{ route('lessons.index') }}">Sportcentrum de Linde</a>
            <a href="{{ route('lessons.index') }}">Lessons</a>
            <a href="{{ route('registrations.index') }}">My registrations</a>
            @can('create', \App\Models\Lesson::class)
                <a href="{{ route('lessons.create') }}">Create lesson</a>
            @endcan
        </nav>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-8">
        @if (session('status'))
            <p class="mb-6 rounded bg-emerald-100 p-4 text-emerald-900" role="status">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded bg-red-100 p-4 text-red-900" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
