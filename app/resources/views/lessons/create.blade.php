<x-layouts.app title="Les aanmaken">
    <h1>Les aanmaken</h1>

    <form method="POST" action="{{ route('lessons.store') }}">
        @csrf

        <label for="instructor_id">Trainer</label>
        <select id="instructor_id" name="instructor_id" required>
            <option value="">Kies een trainer</option>
            @foreach ($instructors as $instructor)
                <option value="{{ $instructor->id }}" @selected(is_scalar(old('instructor_id')) && (string) old('instructor_id') === (string) $instructor->id)>{{ $instructor->name }}</option>
            @endforeach
        </select>
        @error('instructor_id') <p>{{ $message }}</p> @enderror

        <label for="starts_at">Startdatum en -tijd (UTC)</label>
        <input id="starts_at" type="datetime-local" name="starts_at" value="{{ is_scalar(old('starts_at')) ? old('starts_at') : '' }}" required>
        @error('starts_at') <p>{{ $message }}</p> @enderror

        <label for="duration_minutes">Duur (minuten)</label>
        <input id="duration_minutes" type="number" name="duration_minutes" min="1" max="65535" value="{{ is_scalar(old('duration_minutes')) ? old('duration_minutes') : '' }}" required>
        @error('duration_minutes') <p>{{ $message }}</p> @enderror

        <label for="category">Categorie</label>
        <select id="category" name="category" required>
            <option value="">Kies een categorie</option>
            @foreach (['strength' => 'Kracht', 'cardio' => 'Cardio', 'relaxation' => 'Ontspanning'] as $value => $label)
                <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('category') <p>{{ $message }}</p> @enderror

        <label for="sport">Sport</label>
        <input id="sport" type="text" name="sport" maxlength="100" value="{{ is_scalar(old('sport')) ? old('sport') : '' }}" required>
        @error('sport') <p>{{ $message }}</p> @enderror

        <label for="max_participants">Maximaal aantal deelnemers</label>
        <input id="max_participants" type="number" name="max_participants" min="1" max="65535" value="{{ is_scalar(old('max_participants')) ? old('max_participants') : '' }}" required>
        @error('max_participants') <p>{{ $message }}</p> @enderror

        <button type="submit">Les aanmaken</button>
    </form>
</x-layouts.app>
