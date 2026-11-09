<x-layouts.app title="Rooster">
    <section aria-labelledby="lessons-heading">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Sportcentrum De Linde</p>
                <h1 id="lessons-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Rooster</h1>
                <p class="mt-2 text-slate-600">Kies een les om de details te bekijken.</p>
            </div>
            @can('create', \App\Models\Lesson::class)
                <a class="inline-flex w-fit items-center justify-center rounded-full bg-brand-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" href="{{ route('lessons.create') }}">+ Les aanmaken</a>
            @endcan
        </div>

        <form class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5" method="GET" action="{{ route('lessons.index') }}">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500" for="week">Week van</label>
                    <input class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="week" type="date" name="week" value="{{ $selectedWeek->toDateString() }}">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500" for="category">Categorie</label>
                    <select class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="category" name="category">
                        <option value="">Alle categorieën</option>
                        @foreach (['strength' => 'Kracht', 'cardio' => 'Cardio', 'relaxation' => 'Ontspanning'] as $value => $label)
                            <option value="{{ $value }}" @selected($filters['category'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500" for="instructor_id">Trainer</label>
                    <select class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="instructor_id" name="instructor_id">
                        <option value="">Alle trainers</option>
                        @foreach ($instructors as $instructor)
                            <option value="{{ $instructor->id }}" @selected((string) $filters['instructor_id'] === (string) $instructor->id)>{{ $instructor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500" for="sport">Sport of les</label>
                    <input class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="sport" type="search" name="sport" value="{{ $filters['sport'] }}" placeholder="Zoek op naam">
                </div>
                <div class="flex items-end gap-2">
                    <button class="flex-1 rounded-full bg-brand-600 px-4 py-2.5 text-sm font-extrabold text-white transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">Filteren</button>
                    <a class="rounded-full border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50" href="{{ route('lessons.index', ['week' => $selectedWeek->toDateString()]) }}">Wissen</a>
                </div>
            </div>
        </form>

        <div class="mb-3 flex items-center justify-between gap-3">
            <a class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-bold text-brand-700 transition hover:bg-brand-50" href="{{ $previousWeekUrl }}"><span aria-hidden="true">←</span> Vorige week</a>
            <p class="rounded-full bg-brand-50 px-4 py-2 text-center text-sm font-extrabold text-brand-800">{{ $selectedWeekLabel }}</p>
            <a class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-bold text-brand-700 transition hover:bg-brand-50" href="{{ $nextWeekUrl }}">Volgende week <span aria-hidden="true">→</span></a>
        </div>

        @if ($lessons->isEmpty())
            <div class="rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-12 text-center shadow-sm">
                <h2 class="text-lg font-extrabold text-brand-950">Geen lessen gevonden</h2>
                <p class="mt-2 text-slate-600">Pas de filters aan of kies een andere week.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-brand-950/5">
                <div class="overflow-x-auto">
                    <table class="min-w-[1050px] w-full table-fixed border-collapse text-left text-sm">
                        <thead>
                            <tr class="bg-brand-800 text-white">
                                <th class="w-20 px-3 py-3 text-center text-xs font-extrabold uppercase tracking-wider" scope="col">Tijd</th>
                                @foreach ($weekDays as $day)
                                    <th class="px-3 py-3 text-center" scope="col">
                                        <span class="block text-xs font-bold uppercase tracking-wider text-blue-100">{{ $day['date']->format('d') }} {{ $day['name'] }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timeSlots as $hour)
                                <tr>
                                    <th class="w-20 border-t border-slate-100 bg-slate-50 px-2 py-1 text-center align-top text-xs font-bold text-slate-500" scope="row">{{ sprintf('%02d:00', $hour) }}</th>
                                    @foreach ($weekDays as $day)
                                        @php
                                            $dayKey = $day['date']->format('Y-m-d');
                                            $lessonsAtHour = $lessonsByDay->get($dayKey, collect())->filter(fn ($lesson) => $lesson->starts_at->hour === $hour);
                                        @endphp
                                        <td class="h-10 border-l border-t border-slate-100 p-1 align-top">
                                            <div class="space-y-1">
                                                @foreach ($lessonsAtHour as $lesson)
                                                    @php
                                                        $lessonRegistration = $registrationsByLesson->get($lesson->id);
                                                        $categoryLabel = ['strength' => 'Kracht', 'cardio' => 'Cardio', 'relaxation' => 'Ontspanning'][$lesson->category] ?? $lesson->category;
                                                    @endphp
                                                    <a class="block rounded-lg border-l-4 border-brand-500 bg-brand-50 px-2 py-1.5 text-xs text-brand-950 transition hover:bg-brand-100 focus-visible:outline-2 focus-visible:outline-brand-600" href="{{ route('lessons.show', $lesson) }}">
                                                        <span class="block font-extrabold">{{ $lesson->starts_at->format('H:i') }} · {{ $lesson->sport }}</span>
                                                        <span class="mt-0.5 block text-brand-700">{{ $categoryLabel }} · {{ $lesson->enrolled_count }}/{{ $lesson->max_participants }}</span>
                                                        @if ($lessonRegistration !== null)
                                                            <span class="mt-0.5 block font-bold">Jouw plek: {{ $lessonRegistration->status === 'enrolled' ? 'ingeschreven' : 'wachtlijst' }}</span>
                                                        @endif
                                                    </a>
                                                @endforeach
                                            </div>
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
