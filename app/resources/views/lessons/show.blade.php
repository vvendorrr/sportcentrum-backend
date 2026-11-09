<x-layouts.app :title="$lesson->sport">
    <section class="mx-auto max-w-4xl" aria-labelledby="lesson-heading">
        <a class="mb-6 inline-flex items-center gap-2 text-sm font-bold text-brand-700 transition hover:text-brand-900" href="{{ route('lessons.index') }}"><span aria-hidden="true">←</span> Terug naar het rooster</a>

        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-brand-950/5">
            <div class="bg-gradient-to-r from-brand-950 via-brand-800 to-brand-600 px-6 py-8 text-white sm:px-9 sm:py-10">
                <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-100">{{ ['strength' => 'Kracht', 'cardio' => 'Cardio', 'relaxation' => 'Ontspanning'][$lesson->category] ?? $lesson->category }}</span>
                <h1 id="lesson-heading" class="mt-4 text-3xl font-black tracking-tight sm:text-4xl">{{ $lesson->sport }}</h1>
                <p class="mt-2 text-blue-100">Lesdetails en beschikbare plekken</p>
            </div>

            <div class="grid gap-8 p-6 sm:p-9 md:grid-cols-[1.2fr_0.8fr]">
                <dl class="grid gap-5 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Begint</dt>
                        <dd class="mt-2 text-lg font-extrabold text-brand-950">{{ $lesson->starts_at->format('d-m-Y H:i') }} UTC</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Eindigt</dt>
                        <dd class="mt-2 text-lg font-extrabold text-brand-950">{{ $lesson->starts_at->copy()->addMinutes($lesson->duration_minutes)->format('d-m-Y H:i') }} UTC</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Duur</dt>
                        <dd class="mt-2 text-lg font-extrabold text-brand-950">{{ $lesson->duration_minutes }} minuten</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Trainer</dt>
                        <dd class="mt-2 text-lg font-extrabold text-brand-950">{{ $lesson->instructor->name }}</dd>
                    </div>
                </dl>

                <aside class="rounded-3xl border border-brand-100 bg-brand-50 p-5 sm:p-6" aria-label="Lesbezetting">
                    <p class="text-sm font-bold uppercase tracking-wide text-brand-700">Deelnemers</p>
                    <p class="mt-3 text-4xl font-black tracking-tight text-brand-950">{{ $lesson->enrolled_count }} <span class="text-xl text-slate-400">/ {{ $lesson->max_participants }}</span></p>
                    <p class="mt-1 text-sm font-semibold text-slate-600">{{ max(0, $lesson->max_participants - $lesson->enrolled_count) }} vrije plekken</p>

                    @auth
                        @if (auth()->user()->role === 'member')
                            @if ($registration !== null)
                                <p class="mt-5 rounded-xl {{ $registration->status === 'enrolled' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }} px-4 py-3 text-sm font-bold" role="status">
                                    {{ $registration->status === 'enrolled' ? 'Je bent ingeschreven voor deze les.' : 'Je staat op de wachtlijst voor deze les.' }}
                                </p>
                            @else
                                <form class="mt-5" method="POST" action="{{ route('lessons.register', $lesson) }}">
                                    @csrf
                                    <button class="w-full rounded-full bg-brand-600 px-5 py-3 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">
                                        {{ $lesson->enrolled_count < $lesson->max_participants ? 'Inschrijven' : 'Op de wachtlijst zetten' }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    @else
                        <a class="mt-5 block rounded-full border border-brand-200 bg-white px-5 py-3 text-center text-sm font-bold text-brand-700 transition hover:bg-brand-100" href="{{ route('login') }}">Log in om je in te schrijven</a>
                    @endauth
                </aside>
            </div>

            @can('update', $lesson)
                <div class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-9">
                    <p class="text-sm text-slate-500">Beheer deze les en de bijbehorende inschrijvingen.</p>
                    <div class="flex flex-wrap gap-3">
                        <a class="inline-flex items-center justify-center rounded-full border border-brand-200 bg-white px-5 py-3 text-sm font-bold text-brand-700 transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-brand-600" href="{{ route('lessons.edit', $lesson) }}">Les bewerken</a>
                        <form method="POST" action="{{ route('lessons.destroy', $lesson) }}" onsubmit="return confirm('Weet je zeker dat je deze les en alle inschrijvingen wilt verwijderen?')">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex items-center justify-center rounded-full border border-red-200 bg-white px-5 py-3 text-sm font-bold text-red-700 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-red-600" type="submit">Les verwijderen</button>
                        </form>
                    </div>
                </div>
            @endcan
        </article>
    </section>
</x-layouts.app>
