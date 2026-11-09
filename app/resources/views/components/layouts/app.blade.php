@props(['title' => 'Sportcentrum De Linde', 'minimal' => false])

<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1769d2">
    <title>{{ $title }} · Sportcentrum De Linde</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen">
        @unless ($minimal)
            <aside class="z-20 flex w-full flex-col border-b border-brand-100 bg-white md:fixed md:inset-y-0 md:left-0 md:w-64 md:border-r md:border-b-0">
                <a class="flex items-center gap-3 border-b border-slate-100 px-5 py-5" href="{{ route('home') }}" aria-label="Sportcentrum De Linde - startpagina">
                    <span class="grid size-11 place-items-center rounded-2xl bg-brand-600 text-lg font-black text-white shadow-lg shadow-brand-600/20" aria-hidden="true">L</span>
                    <span class="leading-tight">
                        <span class="block text-xs font-bold uppercase tracking-[0.18em] text-brand-600">Sportcentrum</span>
                        <span class="block text-lg font-extrabold tracking-tight text-brand-950">De Linde</span>
                    </span>
                </a>

                <nav class="flex gap-1 overflow-x-auto px-3 py-3 md:flex-1 md:flex-col md:overflow-y-auto" aria-label="Hoofdnavigatie">
                    <a @class([
                        'flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition focus-visible:outline-2 focus-visible:outline-brand-600',
                        'bg-brand-50 text-brand-800' => request()->routeIs('home'),
                        'text-slate-600 hover:bg-brand-50 hover:text-brand-800' => ! request()->routeIs('home'),
                    ]) href="{{ route('home') }}">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z" /></svg>
                        Home
                    </a>

                    <a @class([
                        'flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition focus-visible:outline-2 focus-visible:outline-brand-600',
                        'bg-brand-50 text-brand-800' => request()->routeIs('lessons.*'),
                        'text-slate-600 hover:bg-brand-50 hover:text-brand-800' => ! request()->routeIs('lessons.*'),
                    ]) href="{{ route('lessons.index') }}">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" /><path stroke-linecap="round" d="M16 3v4M8 3v4M3 10h18" /></svg>
                        Rooster
                    </a>

                    @auth
                        @if (auth()->user()->role === 'member')
                            <a @class([
                                'flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition focus-visible:outline-2 focus-visible:outline-brand-600',
                                'bg-brand-50 text-brand-800' => request()->routeIs('registrations.*'),
                                'text-slate-600 hover:bg-brand-50 hover:text-brand-800' => ! request()->routeIs('registrations.*'),
                            ]) href="{{ route('registrations.index') }}">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                                Mijn inschrijvingen
                            </a>
                        @endif

                        @can('create', \App\Models\Lesson::class)
                            <p class="hidden px-4 pb-1 pt-5 text-xs font-extrabold uppercase tracking-[0.16em] text-slate-400 md:block">Beheer</p>
                            <a @class([
                                'flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition focus-visible:outline-2 focus-visible:outline-brand-600',
                                'bg-brand-50 text-brand-800' => request()->routeIs('lessons.create'),
                                'text-slate-600 hover:bg-brand-50 hover:text-brand-800' => ! request()->routeIs('lessons.create'),
                            ]) href="{{ route('lessons.create') }}">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                                Les aanmaken
                            </a>
                            <a @class([
                                'flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition focus-visible:outline-2 focus-visible:outline-brand-600',
                                'bg-brand-50 text-brand-800' => request()->routeIs('participants.*'),
                                'text-slate-600 hover:bg-brand-50 hover:text-brand-800' => ! request()->routeIs('participants.*'),
                            ]) href="{{ route('participants.index') }}">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m12-13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm3 4a4 4 0 0 1 3 4v2m-3-18a4 4 0 0 1 0 8" /></svg>
                                Deelnemers
                            </a>
                            <a @class([
                                'flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition focus-visible:outline-2 focus-visible:outline-brand-600',
                                'bg-brand-50 text-brand-800' => request()->routeIs('trainers.*'),
                                'text-slate-600 hover:bg-brand-50 hover:text-brand-800' => ! request()->routeIs('trainers.*'),
                            ]) href="{{ route('trainers.index') }}">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-8 7a8 8 0 0 1 16 0M19 8l1.5 1.5L23 7" /></svg>
                                Trainers
                            </a>
                        @endcan
                    @endauth

                    @guest
                        <div class="ml-1 flex shrink-0 items-center gap-2 md:hidden">
                            <a class="rounded-xl bg-brand-600 px-4 py-3 text-sm font-bold text-white" href="{{ route('login') }}">Inloggen</a>
                            <a class="rounded-xl border border-brand-200 px-4 py-3 text-sm font-bold text-brand-700" href="{{ route('register') }}">Account aanmaken</a>
                        </div>
                    @endguest
                </nav>

                <div class="hidden border-t border-slate-100 p-4 md:block">
                    @guest
                        <div class="flex flex-col gap-2">
                            <a class="rounded-xl bg-brand-600 px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-brand-700" href="{{ route('login') }}">Inloggen</a>
                            <a class="rounded-xl border border-brand-200 px-4 py-3 text-center text-sm font-bold text-brand-700 transition hover:bg-brand-50" href="{{ route('register') }}">Account aanmaken</a>
                        </div>
                    @endguest
                    @auth
                        <div class="flex items-center gap-3">
                            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-black text-brand-800" aria-hidden="true">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-extrabold text-slate-800">{{ auth()->user()->name }}</span>
                                <span class="block truncate text-xs text-slate-500">{{ auth()->user()->email }}</span>
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="rounded-lg p-2 text-slate-500 transition hover:bg-brand-50 hover:text-brand-700 focus-visible:outline-2 focus-visible:outline-brand-600" type="submit" aria-label="Uitloggen" title="Uitloggen">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5M15 12H3m9-9h6a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-6" /></svg>
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </aside>
        @endunless

        <div @class([
            'min-h-screen md:ml-64' => ! $minimal,
            'flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-brand-50 via-white to-sky-100 px-4 py-10' => $minimal,
        ])>
            @if ($minimal)
                <a class="mb-6 flex items-center gap-3 rounded-xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-600" href="{{ route('home') }}">
                    <span class="grid size-11 place-items-center rounded-2xl bg-brand-600 text-lg font-black text-white shadow-lg shadow-brand-600/20" aria-hidden="true">L</span>
                    <span class="leading-tight">
                        <span class="block text-xs font-bold uppercase tracking-[0.18em] text-brand-600">Sportcentrum</span>
                        <span class="block text-lg font-extrabold tracking-tight text-brand-950">De Linde</span>
                    </span>
                </a>
            @endif

            <main @class([
                'mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8' => ! $minimal,
                'w-full max-w-xl' => $minimal,
            ])>
                @if (session('status'))
                    <p class="mb-6 rounded-2xl border border-brand-100 bg-brand-50 px-5 py-4 text-sm font-semibold text-brand-900 shadow-sm" role="status">{{ session('status') }}</p>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800" role="alert">
                        <p class="font-bold">Controleer de ingevulde gegevens:</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>

            @unless ($minimal)
                <footer class="mt-12 border-t border-blue-100 bg-white">
                    <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-sm text-slate-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                        <p class="font-bold text-brand-900">Sportcentrum De Linde</p>
                        <p>Samen in beweging.</p>
                    </div>
                </footer>
            @endunless
        </div>
    </div>
</body>
</html>
