<x-layouts.app :title="$title">
    <section aria-labelledby="directory-heading">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Sportcentrum De Linde</p>
                <h1 id="directory-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">{{ $title }}</h1>
            </div>
            @if ($directoryType === 'trainers')
                <a
                    class="inline-flex size-12 items-center justify-center rounded-full bg-brand-600 text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600"
                    href="{{ route('trainers.create') }}"
                    aria-label="Traineraccount aanmaken"
                    title="Traineraccount aanmaken"
                >
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                </a>
            @endif
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
                                        <li class="flex items-center gap-1">
                                            <a class="min-w-0 flex-1 flex items-center justify-between gap-4 rounded-xl px-3 py-3 transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-brand-600" href="{{ route('lessons.show', $lesson) }}">
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
                                            @if ($directoryType === 'trainers')
                                                <form class="shrink-0 pr-2" method="POST" action="{{ route('trainers.lessons.detach', [$selectedPerson, $lesson]) }}" onsubmit="return confirm('Toewijzing van deze les opheffen? De les blijft bestaan zonder trainer.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-lg px-2.5 py-2 text-xs font-bold text-red-700 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600" type="submit">
                                                        Toewijzing opheffen
                                                    </button>
                                                </form>
                                            @endif
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
                                @if ($directoryType === 'participants')
                                    <div>
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Accountstatus</dt>
                                        <dd class="mt-1 text-sm font-bold {{ $selectedPerson->is_active ? 'text-emerald-700' : 'text-red-700' }}">{{ $selectedPerson->is_active ? 'Actief' : 'Uitgeschakeld' }}</dd>
                                    </div>
                                @endif
                            </dl>

                            @if ($directoryType === 'trainers')
                                <form class="mt-6 border-t border-slate-100 pt-5" method="POST" action="{{ route('trainers.lessons.assign', $selectedPerson) }}">
                                    @csrf

                                    <h4 class="text-sm font-bold uppercase tracking-[0.14em] text-slate-500">Lessen toewijzen</h4>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Vink aankomende lessen aan die deze trainer gaat geven. De huidige trainer van een les wordt vervangen.</p>

                                    @php($assignError = $errors->first('lessons') ?: $errors->first('lessons.*'))
                                    @if ($assignError !== '')
                                        <p class="mt-3 text-sm font-semibold text-red-700" role="alert">{{ $assignError }}</p>
                                    @endif

                                    @if ($assignableLessons->isEmpty())
                                        <p class="mt-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500">Er zijn momenteel geen aankomende lessen van andere trainers om toe te wijzen.</p>
                                    @else
                                        <div class="mt-3 max-h-64 space-y-2 overflow-y-auto pr-1">
                                            @foreach ($assignableLessons as $assignableLesson)
                                                <label class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                                                    <input class="mt-0.5 size-4 shrink-0 accent-brand-600" type="checkbox" name="lessons[]" value="{{ $assignableLesson->id }}">
                                                    <span class="min-w-0">
                                                        <span class="block truncate text-sm font-bold text-brand-950">{{ $assignableLesson->sport }}</span>
                                                        <span class="mt-0.5 block text-xs font-semibold text-slate-500">
                                                            {{ $assignableLesson->starts_at->format('d-m-Y · H:i') }} · Trainer: {{ $assignableLesson->instructor?->name ?? 'geen' }}
                                                        </span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <button class="mt-4 rounded-full bg-brand-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">
                                            Toewijzen
                                        </button>
                                    @endif
                                </form>
                            @endif

                            @if ($directoryType === 'participants' && $selectedPerson->is_active)
                                <form class="mt-6 border-t border-slate-100 pt-5" method="POST" action="{{ route('members.deactivate', $selectedPerson) }}" onsubmit="return confirm('Weet je zeker dat je dit ledenaccount wilt uitschakelen? De accountgegevens en inschrijvingen blijven bewaard.')">
                                    @csrf
                                    @method('PATCH')
                                    <p class="mb-3 text-xs leading-5 text-slate-500">Het lid kan na uitschakeling niet meer inloggen of zich inschrijven. Bestaande gegevens en inschrijvingen blijven bewaard.</p>
                                    <button class="w-full rounded-full border border-red-200 bg-white px-5 py-3 text-sm font-bold text-red-700 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600" type="submit">Ledenaccount uitschakelen</button>
                                </form>
                            @endif
                        </section>
                    </div>
                </article>

                @if ($directoryType === 'trainers')
                    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm lg:col-span-2" aria-labelledby="trainer-edit-heading">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-7">
                            <h2 id="trainer-edit-heading" class="text-lg font-extrabold text-brand-950">Traineraccount bewerken</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Wijzig de accountgegevens van {{ $selectedPerson->name }}. Een uitgeschakeld account kan niet meer inloggen.</p>
                        </div>

                        <form class="grid gap-4 p-4 sm:grid-cols-2 sm:p-6" method="POST" action="{{ route('trainers.update', $selectedPerson) }}">
                            @csrf
                            @method('PATCH')

                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-bold text-slate-700" for="trainer-name">Naam</label>
                                <input
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                                    id="trainer-name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $selectedPerson->name) }}"
                                    @error('name') aria-invalid="true" aria-describedby="trainer-name-error" @enderror
                                >
                                @error('name')
                                    <p class="mt-2 text-sm font-semibold text-red-700" id="trainer-name-error" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700" for="trainer-email">E-mailadres</label>
                                <input
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                                    id="trainer-email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $selectedPerson->email) }}"
                                    autocomplete="email"
                                    @error('email') aria-invalid="true" aria-describedby="trainer-email-error" @enderror
                                >
                                @error('email')
                                    <p class="mt-2 text-sm font-semibold text-red-700" id="trainer-email-error" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700" for="trainer-phone">Telefoonnummer</label>
                                <input
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                                    id="trainer-phone"
                                    name="phone_number"
                                    type="tel"
                                    value="{{ old('phone_number', $selectedPerson->phone_number) }}"
                                    @error('phone_number') aria-invalid="true" aria-describedby="trainer-phone-error" @enderror
                                >
                                @error('phone_number')
                                    <p class="mt-2 text-sm font-semibold text-red-700" id="trainer-phone-error" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-bold text-slate-700" for="trainer-status">Accountstatus</label>
                                <select
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                                    id="trainer-status"
                                    name="is_active"
                                    @error('is_active') aria-invalid="true" aria-describedby="trainer-status-error" @enderror
                                >
                                    @php($trainerStatus = old('is_active', $selectedPerson->is_active ? '1' : '0'))
                                    <option value="1" @selected($trainerStatus === '1')>Actief</option>
                                    <option value="0" @selected($trainerStatus === '0')>Inactief</option>
                                </select>
                                @error('is_active')
                                    <p class="mt-2 text-sm font-semibold text-red-700" id="trainer-status-error" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-end">
                                <button class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">
                                    Wijzigingen opslaan
                                </button>
                            </div>
                        </form>
                    </section>
                @endif
            @else
                <div class="grid min-h-64 place-items-center rounded-3xl border border-dashed border-brand-200 bg-white p-8 text-center">
                    <p class="text-sm font-semibold text-slate-500">Er zijn nog geen gegevens om weer te geven.</p>
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
