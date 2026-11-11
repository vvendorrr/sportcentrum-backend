@props([
    'lesson' => null,
    'instructors',
    'action',
    'cancelUrl',
    'httpMethod' => 'POST',
    'submitLabel' => 'Les opslaan',
])

@php
    $oldStartsAt = old('starts_at', $lesson?->starts_at?->copy()->setTimezone(config('sportcentrum.schedule_timezone'))->format('Y-m-d\\TH:i'));
    if (old('starts_at') !== null && is_string($oldStartsAt)) {
        try {
            $oldStartsAt = \Carbon\Carbon::parse($oldStartsAt, 'UTC')
                ->setTimezone(config('sportcentrum.schedule_timezone'))
                ->format('Y-m-d\\TH:i');
        } catch (\Throwable) {
        }
    }
    $oldInstructor = old('instructor_id', $lesson?->instructor_id);
    $oldDuration = old('duration_minutes', $lesson?->duration_minutes);
    $oldCategory = old('category', $lesson?->category);
    $oldSport = old('sport', $lesson?->sport);
    $oldCapacity = old('max_participants', $lesson?->max_participants);
@endphp

<form class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-brand-950/5" method="POST" action="{{ $action }}">
    @csrf
    @if ($httpMethod !== 'POST')
        @method($httpMethod)
    @endif

    <div class="grid gap-6 p-6 sm:grid-cols-2 sm:p-8">
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700" for="instructor_id">Trainer</label>
            <select class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="instructor_id" name="instructor_id" required>
                <option value="">Kies een trainer</option>
                @foreach ($instructors as $instructor)
                    <option value="{{ $instructor->id }}" @selected(is_scalar($oldInstructor) && (string) $oldInstructor === (string) $instructor->id)>{{ $instructor->name }}</option>
                @endforeach
            </select>
            @error('instructor_id') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700" for="starts_at">Startdatum en -tijd (Nederlandse tijd)</label>
            <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="starts_at" type="datetime-local" name="starts_at" value="{{ is_scalar($oldStartsAt) ? $oldStartsAt : '' }}" required>
            @error('starts_at') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700" for="duration_minutes">Duur (minuten)</label>
            <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="duration_minutes" type="number" name="duration_minutes" min="1" max="65535" value="{{ is_scalar($oldDuration) ? $oldDuration : '' }}" required>
            @error('duration_minutes') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700" for="category">Categorie</label>
            <select class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="category" name="category" required>
                <option value="">Kies een categorie</option>
                @foreach (['strength' => 'Kracht', 'cardio' => 'Cardio', 'relaxation' => 'Ontspanning'] as $value => $label)
                    <option value="{{ $value }}" @selected($oldCategory === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('category') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-bold text-slate-700" for="sport">Sport of lesnaam</label>
            <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="sport" type="text" name="sport" maxlength="100" value="{{ is_scalar($oldSport) ? $oldSport : '' }}" required>
            @error('sport') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-bold text-slate-700" for="max_participants">Maximaal aantal deelnemers</label>
            <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100 sm:max-w-xs" id="max_participants" type="number" name="max_participants" min="1" max="65535" value="{{ is_scalar($oldCapacity) ? $oldCapacity : '' }}" required>
            @error('max_participants') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-6 py-5 sm:flex-row sm:justify-end sm:px-8">
        <a class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-brand-600" href="{{ $cancelUrl }}">Annuleren</a>
        <button class="inline-flex items-center justify-center rounded-full bg-brand-600 px-6 py-3 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">{{ $submitLabel }}</button>
    </div>
</form>
