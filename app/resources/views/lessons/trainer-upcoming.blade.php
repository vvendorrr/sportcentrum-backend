<x-layouts.app title="Mijn komende lessen">
    <section aria-labelledby="trainer-lessons-heading">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Trainer</p>
            <h1 id="trainer-lessons-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Mijn komende lessen</h1>
            <p class="mt-2 text-slate-600">Bekijk de actuele deelnemerslijst en groepsgrootte van je lessen.</p>
        </div>

        @if ($lessons->isEmpty())
            <div class="rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-14 text-center shadow-sm">
                <h2 class="text-xl font-extrabold text-brand-950">Je hebt geen komende lessen</h2>
                <p class="mt-2 text-slate-600">Lessen die aan jou zijn toegewezen verschijnen hier.</p>
            </div>
        @else
            <div class="grid gap-5 lg:grid-cols-2">
                @foreach ($lessons as $lesson)
                    @php($enrolled = $lesson->registrations->where('status', 'enrolled'))
                    @php($waitlisted = $lesson->registrations->where('status', 'waitlisted'))
                    <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-brand-600">{{ $lesson->starts_at->copy()->setTimezone(config('sportcentrum.schedule_timezone'))->format('d-m-Y · H:i') }}</p>
                                <h2 class="mt-1 text-2xl font-black tracking-tight text-brand-950">{{ $lesson->sport }}</h2>
                            </div>
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-bold text-brand-700">{{ $lesson->duration_minutes }} minuten</span>
                        </div>

                        <dl class="mt-5 grid grid-cols-2 gap-3 rounded-2xl bg-slate-50 p-4 text-sm">
                            <div>
                                <dt class="font-semibold text-slate-500">Ingeschreven</dt>
                                <dd class="mt-1 font-extrabold text-brand-950">{{ $lesson->enrolled_count }} / {{ $lesson->max_participants }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-500">Wachtlijst</dt>
                                <dd class="mt-1 font-extrabold text-brand-950">{{ $lesson->waitlisted_count }}</dd>
                            </div>
                        </dl>

                        <p class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-950">Alleen leden met een bevestigde inschrijving mogen deelnemen. Laat deelnemers zonder geldige inschrijving of met alleen een wachtlijststatus niet aansluiten; verwijs hen naar de beheerder. Overschrijd de lescapaciteit niet.</p>

                        <section class="mt-5" aria-labelledby="enrolled-{{ $lesson->id }}">
                            <h3 id="enrolled-{{ $lesson->id }}" class="font-extrabold text-brand-950">Bevestigde deelnemers</h3>
                            @if ($enrolled->isEmpty())
                                <p class="mt-2 text-sm text-slate-500">Er zijn nog geen bevestigde deelnemers.</p>
                            @else
                                <ul class="mt-2 space-y-2 text-sm text-slate-700">
                                    @foreach ($enrolled as $registration)
                                        <li>{{ $registration->user->name }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </section>

                        <section class="mt-5" aria-labelledby="waitlisted-{{ $lesson->id }}">
                            <h3 id="waitlisted-{{ $lesson->id }}" class="font-extrabold text-brand-950">Wachtlijstdeelnemers</h3>
                            @if ($waitlisted->isEmpty())
                                <p class="mt-2 text-sm text-slate-500">Er staan geen deelnemers op de wachtlijst.</p>
                            @else
                                <ul class="mt-2 space-y-2 text-sm text-slate-700">
                                    @foreach ($waitlisted as $registration)
                                        <li class="flex items-center justify-between gap-3">
                                            <span>{{ $registration->user->name }}</span>
                                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800">Wachtlijst</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </section>

                        <a class="mt-6 inline-flex rounded-full border border-brand-200 px-4 py-2 text-sm font-bold text-brand-700 transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" href="{{ route('lessons.participants', $lesson) }}">Deelnemers en aanwezigheid beheren</a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
