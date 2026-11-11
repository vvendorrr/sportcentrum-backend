<x-layouts.app title="Bezettingsoverzicht">
    <section aria-labelledby="occupancy-heading">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Beheer</p>
            <h1 id="occupancy-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Bezettingsoverzicht</h1>
            <p class="mt-2 text-slate-600">Bekijk de inschrijvingen en beschikbare plekken voor komende lessen. Een bezetting onder {{ $lowOccupancyPercent }}% is gemarkeerd als laag.</p>
        </div>

        @if ($lessons->isEmpty())
            <div class="rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-14 text-center shadow-sm">
                <h2 class="text-xl font-extrabold text-brand-950">Er zijn geen komende lessen</h2>
                <p class="mt-2 text-slate-600">Nieuwe lessen verschijnen hier zodra ze zijn ingepland.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <caption class="sr-only">Bezetting van komende lessen</caption>
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4 font-bold" scope="col">Datum en tijd</th>
                            <th class="px-5 py-4 font-bold" scope="col">Sport</th>
                            <th class="px-5 py-4 font-bold" scope="col">Trainer</th>
                            <th class="px-5 py-4 font-bold" scope="col">Ingeschreven</th>
                            <th class="px-5 py-4 font-bold" scope="col">Capaciteit</th>
                            <th class="px-5 py-4 font-bold" scope="col">Vrije plekken</th>
                            <th class="px-5 py-4 font-bold" scope="col">Bezetting</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($lessons as $lesson)
                            @php($remainingPlaces = max(0, $lesson->max_participants - $lesson->enrolled_count))
                            <tr class="hover:bg-brand-50/40">
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-700">{{ $lesson->starts_at->copy()->setTimezone(config('sportcentrum.schedule_timezone'))->format('d-m-Y · H:i') }}</td>
                                <td class="px-5 py-4 font-bold text-brand-950">{{ $lesson->sport }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $lesson->instructor?->name ?? 'Niet toegewezen' }}</td>
                                <td class="px-5 py-4 tabular-nums text-slate-700">{{ $lesson->enrolled_count }}</td>
                                <td class="px-5 py-4 tabular-nums text-slate-700">{{ $lesson->max_participants }}</td>
                                <td class="px-5 py-4 tabular-nums text-slate-700">{{ $remainingPlaces }}</td>
                                <td class="px-5 py-4">
                                    @if ($remainingPlaces === 0)
                                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-extrabold text-red-700">Vol</span>
                                    @elseif ($lesson->enrolled_count * 100 < $lesson->max_participants * $lowOccupancyPercent)
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-extrabold text-amber-800">Lage bezetting</span>
                                    @else
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700">Beschikbaar</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.app>
