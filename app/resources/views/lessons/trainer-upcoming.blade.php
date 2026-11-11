<x-layouts.app title="Mijn komende lessen">
    <section aria-labelledby="trainer-lessons-heading">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Trainer</p>
            <h1 id="trainer-lessons-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Mijn komende lessen</h1>
            <p class="mt-2 text-slate-600">Bekijk de groepsgrootte en wachtlijst van je lessen. De volledige deelnemerslijst vind je via de knop per les.</p>
        </div>

        @if ($lessons->isEmpty())
            <div class="rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-14 text-center shadow-sm">
                <h2 class="text-xl font-extrabold text-brand-950">Je hebt geen komende lessen</h2>
                <p class="mt-2 text-slate-600">Lessen die aan jou zijn toegewezen verschijnen hier.</p>
            </div>
        @else
            <p class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-950">Alleen leden met een bevestigde inschrijving mogen deelnemen. Laat deelnemers zonder geldige inschrijving of met alleen een wachtlijststatus niet aansluiten; verwijs hen naar de beheerder. Overschrijd de lescapaciteit niet.</p>

            <div class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <caption class="sr-only">Mijn komende lessen</caption>
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-4 font-bold" scope="col">Datum en tijd</th>
                            <th class="px-5 py-4 font-bold" scope="col">Sport</th>
                            <th class="px-5 py-4 font-bold" scope="col">Duur</th>
                            <th class="px-5 py-4 font-bold" scope="col">Ingeschreven</th>
                            <th class="px-5 py-4 font-bold" scope="col">Wachtlijst</th>
                            <th class="px-5 py-4 font-bold" scope="col"><span class="sr-only">Acties</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($lessons as $lesson)
                            <tr class="hover:bg-brand-50/40">
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-700">{{ $lesson->starts_at->copy()->setTimezone(config('sportcentrum.schedule_timezone'))->format('d-m-Y · H:i') }}</td>
                                <td class="px-5 py-4 font-bold text-brand-950">{{ $lesson->sport }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-700">{{ $lesson->duration_minutes }} minuten</td>
                                <td class="whitespace-nowrap px-5 py-4 tabular-nums font-extrabold text-brand-950">{{ $lesson->enrolled_count }} / {{ $lesson->max_participants }}</td>
                                <td class="whitespace-nowrap px-5 py-4 tabular-nums font-extrabold text-brand-950">{{ $lesson->waitlisted_count }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a class="inline-flex rounded-full border border-brand-200 px-4 py-2 text-sm font-bold text-brand-700 transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" href="{{ route('lessons.participants', $lesson) }}">Deelnemers en aanwezigheid beheren</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.app>
