<x-layouts.app title="Lesrooster">
    <section aria-labelledby="lessons-heading">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Elke les een stap vooruit</p>
                <h1 id="lessons-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Het lesrooster</h1>
                <p class="mt-2 max-w-2xl text-slate-600">Ontdek het aanbod en kies een les die bij jouw energie past.</p>
            </div>
            @can('create', \App\Models\Lesson::class)
                <a class="inline-flex w-fit items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" href="{{ route('lessons.create') }}">+ Les aanmaken</a>
            @endcan
        </div>

        @if ($lessons->isEmpty())
            <div class="rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-14 text-center shadow-sm">
                <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-brand-50 text-2xl text-brand-700" aria-hidden="true">↗</span>
                <h2 class="mt-4 text-xl font-extrabold text-brand-950">Er zijn nog geen lessen ingepland</h2>
                <p class="mt-2 text-slate-600">Bekijk later opnieuw het rooster.</p>
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($lessons as $lesson)
                    @php
                        $isFull = $lesson->enrolled_count >= $lesson->max_participants;
                        $memberRegistration = $registrationsByLesson->get($lesson->id);
                        $categoryLabel = ['strength' => 'Kracht', 'cardio' => 'Cardio', 'relaxation' => 'Ontspanning'][$lesson->category] ?? $lesson->category;
                    @endphp
                    <article class="group flex flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-brand-200 hover:shadow-xl hover:shadow-brand-900/10">
                        <div class="flex items-start justify-between gap-4">
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700">{{ $categoryLabel }}</span>
                            <span @class([
                                'rounded-full px-3 py-1 text-xs font-extrabold',
                                'bg-emerald-50 text-emerald-700' => ! $isFull,
                                'bg-amber-50 text-amber-800' => $isFull,
                            ])>{{ $isFull ? 'Vol' : 'Plek beschikbaar' }}</span>
                        </div>

                        <h2 class="mt-5 text-2xl font-black tracking-tight text-brand-950"><a class="rounded focus-visible:outline-2 focus-visible:outline-brand-600" href="{{ route('lessons.show', $lesson) }}">{{ $lesson->sport }}</a></h2>
                        <p class="mt-2 text-sm font-semibold text-slate-500">{{ $lesson->starts_at->format('d-m-Y · H:i') }} UTC</p>

                        <dl class="mt-5 grid grid-cols-2 gap-3 rounded-2xl bg-slate-50 p-4 text-sm">
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Duur</dt>
                                <dd class="mt-1 font-bold text-slate-800">{{ $lesson->duration_minutes }} minuten</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Trainer</dt>
                                <dd class="mt-1 font-bold text-slate-800">{{ $lesson->instructor->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Bezetting</dt>
                                <dd class="mt-1 font-bold text-slate-800">{{ $lesson->enrolled_count }} / {{ $lesson->max_participants }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Vrije plekken</dt>
                                <dd class="mt-1 font-bold text-brand-700">{{ max(0, $lesson->max_participants - $lesson->enrolled_count) }}</dd>
                            </div>
                        </dl>

                        <div class="mt-auto pt-5">
                            @auth
                                @if (auth()->user()->role === 'member')
                                    @if ($memberRegistration !== null)
                                        <p class="rounded-xl {{ $memberRegistration->status === 'enrolled' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }} px-4 py-3 text-sm font-bold" role="status">
                                            {{ $memberRegistration->status === 'enrolled' ? 'Je bent ingeschreven' : 'Je staat op de wachtlijst' }}
                                        </p>
                                    @else
                                        <form method="POST" action="{{ route('lessons.register', $lesson) }}">
                                            @csrf
                                            <button class="w-full rounded-full bg-brand-600 px-5 py-3 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">
                                                {{ $isFull ? 'Op de wachtlijst zetten' : 'Inschrijven' }}
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @else
                                <a class="block w-full rounded-full border border-brand-200 px-5 py-3 text-center text-sm font-extrabold text-brand-700 transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" href="{{ route('login') }}">Log in om je in te schrijven</a>
                            @endauth
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
