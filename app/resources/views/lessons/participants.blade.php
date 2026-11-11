<x-layouts.app :title="'Deelnemers · '.$lesson->sport">
    <section class="mx-auto max-w-4xl" aria-labelledby="participants-heading">
        <a class="mb-6 inline-flex items-center gap-2 text-sm font-bold text-brand-700 transition hover:text-brand-900" href="{{ route('lessons.show', $lesson) }}"><span aria-hidden="true">←</span> Terug naar de les</a>

        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-brand-950/5">
            <header class="bg-gradient-to-r from-brand-950 via-brand-800 to-brand-600 px-6 py-8 text-white sm:px-9 sm:py-10">
                <p class="text-sm font-bold uppercase tracking-wide text-blue-100">{{ $lesson->starts_at->format('d-m-Y · H:i') }} · {{ $lesson->duration_minutes }} minuten</p>
                <h1 id="participants-heading" class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">{{ $lesson->sport }}</h1>
                <p class="mt-2 text-blue-100">Deelnemers en aanwezigheid</p>
            </header>

            <div class="p-6 sm:p-9">
                @if ($registrations->isEmpty())
                    <p class="rounded-2xl bg-slate-50 px-4 py-5 text-sm text-slate-500">Er zijn geen ingeschreven deelnemers voor deze les.</p>
                @elseif ($lessonHasEnded)
                    <form method="POST" action="{{ route('lessons.attendance.update', $lesson) }}">
                        @csrf
                        @method('PATCH')

                        <ul class="divide-y divide-slate-100">
                            @foreach ($registrations as $registration)
                                <li class="grid gap-3 py-4 sm:grid-cols-[1fr_14rem] sm:items-center">
                                    <span class="font-bold text-brand-950">{{ $registration->user->name }}</span>
                                    <input type="hidden" name="attendance[{{ $loop->index }}][registration_id]" value="{{ $registration->id }}">
                                    <label class="sr-only" for="attendance-{{ $registration->id }}">Aanwezigheid van {{ $registration->user->name }}</label>
                                    <select id="attendance-{{ $registration->id }}" name="attendance[{{ $loop->index }}][status]" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200">
                                        <option value="" @selected($registration->attendance === null)>Nog niet geregistreerd</option>
                                        <option value="attended" @selected($registration->attendance === 'attended')>Aanwezig</option>
                                        <option value="absent" @selected($registration->attendance === 'absent')>Afwezig</option>
                                    </select>
                                </li>
                            @endforeach
                        </ul>

                        @error('attendance')
                            <p class="mt-4 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                        @error('attendance.*.registration_id')
                            <p class="mt-4 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                        @error('attendance.*.status')
                            <p class="mt-4 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>
                        @enderror

                        <button class="mt-6 rounded-full bg-brand-600 px-6 py-3 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">Aanwezigheid opslaan</button>
                    </form>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($registrations as $registration)
                            <li class="py-4 font-bold text-brand-950">{{ $registration->user->name }}</li>
                        @endforeach
                    </ul>
                    <p class="mt-5 rounded-2xl bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">Aanwezigheid kan worden geregistreerd nadat de les is afgelopen.</p>
                @endif
            </div>
        </article>
    </section>
</x-layouts.app>
