<x-layouts.app title="Home">
    <section aria-labelledby="home-heading">
        <div class="mb-7 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                @auth
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Jouw sportcentrum</p>
                    <h1 id="home-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">{{ $greeting }}, {{ auth()->user()->name }}</h1>
                @else
                    <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Sportcentrum De Linde</p>
                    <h1 id="home-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Welkom bij De Linde</h1>
                @endauth
                <p class="mt-2 text-slate-600">Jouw overzicht voor de week van {{ $weekRangeLabel }}.</p>
            </div>
            <a class="inline-flex w-fit items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" href="{{ route('lessons.index') }}">Bekijk alle lessen <span class="ml-2" aria-hidden="true">→</span></a>
        </div>

        @if ($stats !== null)
            <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <article class="relative overflow-hidden rounded-3xl border border-blue-100 bg-white p-5 shadow-sm shadow-brand-950/5 sm:p-6">
                    <span class="absolute right-5 top-5 grid size-11 place-items-center rounded-2xl bg-brand-50 text-brand-700" aria-hidden="true">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m12-13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm3 4a4 4 0 0 1 3 4v2m-3-18a4 4 0 0 1 0 8" /></svg>
                    </span>
                    <p class="text-sm font-bold text-slate-500">Aantal deelnemers vandaag</p>
                    <p class="mt-3 text-4xl font-black tracking-tight text-brand-950">{{ $stats['participants'] }}</p>
                    <p class="mt-1 text-xs font-semibold text-slate-400">Ingeschreven voor lessen van vandaag</p>
                </article>

                <article class="relative overflow-hidden rounded-3xl border border-blue-100 bg-white p-5 shadow-sm shadow-brand-950/5 sm:p-6">
                    <span class="absolute right-5 top-5 grid size-11 place-items-center rounded-2xl bg-sky-50 text-sky-700" aria-hidden="true">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M7 4v6m10-6v6M5 12h14v7H5zM9 15h.01M13 15h.01" /></svg>
                    </span>
                    <p class="text-sm font-bold text-slate-500">Aantal afmeldingen vandaag</p>
                    <p class="mt-3 text-4xl font-black tracking-tight text-brand-950">{{ $stats['cancellations'] }}</p>
                    <p class="mt-1 text-xs font-semibold text-slate-400">Afmeldingen die vandaag zijn verwerkt</p>
                </article>

                <article class="relative overflow-hidden rounded-3xl border border-blue-100 bg-white p-5 shadow-sm shadow-brand-950/5 sm:col-span-2 sm:p-6 xl:col-span-1">
                    <span class="absolute right-5 top-5 grid size-11 place-items-center rounded-2xl bg-cyan-50 text-cyan-700" aria-hidden="true">
                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path stroke-linecap="round" d="M16 3v4M8 3v4M3 10h18"/></svg>
                    </span>
                    <p class="text-sm font-bold text-slate-500">Lessen vandaag</p>
                    <p class="mt-3 text-4xl font-black tracking-tight text-brand-950">{{ $stats['lessons'] }}</p>
                    <p class="mt-1 text-xs font-semibold text-slate-400">Groepslessen op het rooster</p>
                </article>
            </div>
        @endif
    </section>

    <section aria-labelledby="weekrooster-heading">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Maandag t/m vrijdag</p>
                <h2 id="weekrooster-heading" class="mt-1 text-2xl font-extrabold tracking-tight text-brand-950">Weekrooster</h2>
            </div>
            <p class="rounded-full bg-brand-50 px-4 py-2 text-sm font-bold text-brand-800">{{ $weekRangeLabel }}</p>
        </div>

        @if ($weekLessons->isEmpty())
            <div class="rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-12 text-center shadow-sm">
                <h3 class="text-lg font-bold text-brand-950">Er zijn deze week geen lessen ingepland</h3>
                <p class="mt-2 text-slate-600">Bekijk het volledige rooster voor meer lessen.</p>
                <a class="mt-5 inline-flex rounded-full bg-brand-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-brand-700" href="{{ route('lessons.index') }}">Naar het rooster</a>
            </div>
        @else
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-brand-950/5">
                <div class="overflow-x-auto">
                    <table class="min-w-[1050px] w-full table-fixed border-collapse text-left text-sm">
                        <thead>
                            <tr class="bg-brand-800 text-white">
                                <th class="w-20 px-3 py-3 text-center text-xs font-extrabold uppercase tracking-wider" scope="col">Tijd</th>
                                @foreach ($weekDays as $day)
                                    <th class="px-3 py-3 text-center" scope="col">
                                        <span class="block text-xs font-bold uppercase tracking-wider text-blue-100">{{ ucfirst($day['name']) }}</span>
                                        <span class="mt-1 block text-sm font-extrabold">{{ $day['date']->format('d-m') }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timeSlots as $hour)
                                <tr class="group">
                                    <th class="border-t border-slate-100 bg-slate-50 px-2 py-2 text-center align-top text-xs font-bold text-slate-500" scope="row">{{ sprintf('%02d:00', $hour) }}</th>
                                    @foreach ($weekDays as $day)
                                        @php
                                            $dayKey = $day['date']->format('Y-m-d');
                                            $lessonsAtHour = $weekLessons->get($dayKey, collect())->filter(fn ($lesson) => $lesson->starts_at->hour === $hour);
                                        @endphp
                                        <td class="h-10 border-l border-t border-slate-100 p-1 align-top transition group-hover:bg-blue-50/30">
                                            @foreach ($lessonsAtHour as $lesson)
                                                <a class="mb-1 block rounded-xl border-l-4 border-brand-500 bg-brand-50 px-3 py-2 text-xs text-brand-950 transition hover:bg-brand-100 focus-visible:outline-2 focus-visible:outline-brand-600" href="{{ route('lessons.show', $lesson) }}">
                                                    <span class="block font-extrabold">{{ $lesson->starts_at->format('H:i') }}–{{ $lesson->starts_at->copy()->addMinutes($lesson->duration_minutes)->format('H:i') }}</span>
                                                    <span class="mt-1 block truncate font-bold">{{ $lesson->sport }}</span>
                                                    <span class="mt-1 block text-brand-700">{{ $lesson->enrolled_count }} / {{ $lesson->max_participants }} deelnemers</span>
                                                </a>
                                            @endforeach
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>
