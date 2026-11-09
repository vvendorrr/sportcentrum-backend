<x-layouts.app title="Mijn inschrijvingen">
    <section aria-labelledby="registrations-heading">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Jouw planning</p>
            <h1 id="registrations-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Mijn inschrijvingen</h1>
            <p class="mt-2 text-slate-600">Bekijk je komende lessen en je plek op de wachtlijst.</p>
        </div>

        @if ($registrations->isEmpty())
            <div class="rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-14 text-center shadow-sm">
                <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-brand-50 text-2xl font-black text-brand-700" aria-hidden="true">+</span>
                <h2 class="mt-4 text-xl font-extrabold text-brand-950">Je hebt nog geen komende inschrijvingen</h2>
                <p class="mt-2 text-slate-600">Kies een les uit het rooster om mee te doen.</p>
                <a class="mt-5 inline-flex rounded-full bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" href="{{ route('lessons.index') }}">Bekijk het rooster</a>
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($registrations as $registration)
                    @php($isEnrolled = $registration->status === 'enrolled')
                    <article class="flex flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-brand-200 hover:shadow-xl hover:shadow-brand-900/10">
                        <div class="flex items-start justify-between gap-3">
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700">{{ $registration->lesson->category === 'strength' ? 'Kracht' : ($registration->lesson->category === 'relaxation' ? 'Ontspanning' : 'Cardio') }}</span>
                            <span @class([
                                'rounded-full px-3 py-1 text-xs font-extrabold',
                                'bg-emerald-50 text-emerald-700' => $isEnrolled,
                                'bg-amber-50 text-amber-800' => ! $isEnrolled,
                            ])>{{ $isEnrolled ? 'Ingeschreven' : 'Op de wachtlijst' }}</span>
                        </div>

                        <h2 class="mt-5 text-2xl font-black tracking-tight text-brand-950"><a class="rounded focus-visible:outline-2 focus-visible:outline-brand-600" href="{{ route('lessons.show', $registration->lesson) }}">{{ $registration->lesson->sport }}</a></h2>
                        <p class="mt-2 text-sm font-semibold text-slate-500">{{ $registration->lesson->starts_at->format('d-m-Y · H:i') }} UTC</p>

                        <dl class="mt-5 space-y-3 rounded-2xl bg-slate-50 p-4 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="font-semibold text-slate-500">Trainer</dt>
                                <dd class="text-right font-bold text-slate-800">{{ $registration->lesson->instructor->name }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="font-semibold text-slate-500">Duur</dt>
                                <dd class="text-right font-bold text-slate-800">{{ $registration->lesson->duration_minutes }} minuten</dd>
                            </div>
                        </dl>

                        <div class="mt-auto pt-5">
                            @if (now()->lessThanOrEqualTo($registration->lesson->starts_at->copy()->subHours(2)))
                                <p class="mb-3 text-xs leading-5 text-slate-500">Afmelden kan tot twee uur voor aanvang.</p>
                                <form method="POST" action="{{ route('registrations.cancel', $registration) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full rounded-full border border-red-200 bg-white px-5 py-3 text-sm font-bold text-red-700 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600" type="submit">Afmelden</button>
                                </form>
                            @else
                                <p class="rounded-xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-600">De afmeldtermijn is verstreken.</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
