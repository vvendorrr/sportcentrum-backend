<x-layouts.app :title="$title">
    <section aria-labelledby="directory-heading">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Sportcentrum De Linde</p>
            <h1 id="directory-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">{{ $title }}</h1>
        </div>

        <div class="grid gap-5 lg:grid-cols-[minmax(15rem,0.7fr)_minmax(0,2fr)]">
            <aside class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm" aria-label="{{ $listHeading }}">
                <h2 class="border-b border-slate-100 px-5 py-4 text-sm font-extrabold uppercase tracking-wide text-slate-500">{{ $listHeading }}</h2>
                @if ($people->isEmpty())
                    <p class="px-5 py-6 text-sm text-slate-500">Er zijn nog geen {{ strtolower($title) }} beschikbaar.</p>
                @else
                    <nav class="max-h-[34rem] overflow-y-auto p-2" aria-label="{{ $listHeading }}">
                        @foreach ($people as $person)
                            <a @class([
                                'mb-1 flex items-center gap-3 rounded-2xl px-3 py-3 transition last:mb-0 focus-visible:outline-2 focus-visible:outline-brand-600',
                                'bg-brand-50 text-brand-950' => $selectedPerson?->is($person),
                                'text-slate-700 hover:bg-slate-50' => ! $selectedPerson?->is($person),
                            ]) href="{{ route($directoryType.'.index', [$selectionParameter => $person->id]) }}" @if ($selectedPerson?->is($person)) aria-current="page" @endif>
                                <span class="grid size-10 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-black text-brand-800" aria-hidden="true">{{ str($person->name)->substr(0, 1)->upper() }}</span>
                                <span class="min-w-0 flex-1 truncate text-sm font-bold">{{ $person->name }}</span>
                                @if ($directoryType === 'trainers')
                                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-slate-500">{{ $person->upcoming_lessons_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                @endif
            </aside>

            @if ($selectedPerson)
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <header class="flex flex-wrap items-center gap-4 border-b border-slate-100 px-5 py-5 sm:px-7">
                        <span class="grid size-12 shrink-0 place-items-center rounded-full bg-brand-100 text-lg font-black text-brand-800" aria-hidden="true">{{ str($selectedPerson->name)->substr(0, 1)->upper() }}</span>
                        <h2 class="min-w-0 flex-1 truncate text-xl font-extrabold text-brand-950">{{ $selectedPerson->name }}</h2>
                        @if ($directoryType === 'trainers')
                            <span class="text-sm font-semibold text-slate-500">Trainer</span>
                        @endif
                    </header>

                    <div class="grid gap-5 p-4 sm:p-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(16rem,0.9fr)]">
                        <section class="min-h-72 rounded-3xl border border-slate-200 p-4 sm:p-5" aria-labelledby="directory-items-heading">
                            <h3 id="directory-items-heading" class="mb-4 text-sm font-extrabold text-slate-500">{{ $itemsHeading }}</h3>
                            @if ($items->isEmpty())
                                <p class="rounded-2xl bg-slate-50 px-4 py-5 text-sm text-slate-500">{{ $emptyItemsMessage }}</p>
                            @else
                                <ul class="divide-y divide-slate-100">
                                    @foreach ($items as $item)
                                        @php($lesson = $directoryType === 'participants' ? $item->lesson : $item)
                                        <li>
                                            <a class="flex items-center justify-between gap-4 rounded-xl px-3 py-3 transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-brand-600" href="{{ route('lessons.show', $lesson) }}">
                                                <span class="min-w-0">
                                                    <span class="block truncate text-sm font-bold text-brand-950">{{ $lesson->sport }}</span>
                                                    <span class="mt-1 block text-xs font-semibold text-slate-500">
                                                        {{ $lesson->starts_at->format('d-m-Y · H:i') }}
                                                        @if ($directoryType === 'participants' && $item->status === 'waitlisted')
                                                            · Wachtlijst
                                                        @endif
                                                    </span>
                                                </span>
                                                <span class="shrink-0 text-slate-400" aria-hidden="true">→</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </section>

                        <section class="rounded-3xl border border-slate-200 p-4 sm:p-5" aria-labelledby="directory-information-heading">
                            <h3 id="directory-information-heading" class="mb-5 text-lg font-extrabold text-brand-950">Informatie</h3>
                            <dl class="space-y-5">
                                <div>
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Naam</dt>
                                    <dd class="mt-1 break-words text-sm font-bold text-slate-800">{{ $selectedPerson->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">E-mailadres</dt>
                                    <dd class="mt-1 break-words text-sm font-bold text-slate-800"><a class="rounded hover:text-brand-700 focus-visible:outline-2 focus-visible:outline-brand-600" href="mailto:{{ $selectedPerson->email }}">{{ $selectedPerson->email }}</a></dd>
                                </div>
                            </dl>
                        </section>
                    </div>
                </article>
            @else
                <div class="grid min-h-64 place-items-center rounded-3xl border border-dashed border-brand-200 bg-white p-8 text-center">
                    <p class="text-sm font-semibold text-slate-500">Er zijn nog geen gegevens om weer te geven.</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
