<x-layouts.app title="Create lesson">
    <h1>Create lesson</h1>

    <form method="POST" action="{{ route('lessons.store') }}">
        @csrf

        <label for="instructor_id">Instructor</label>
        <select id="instructor_id" name="instructor_id" required>
            <option value="">Choose an instructor</option>
            @foreach ($instructors as $instructor)
                <option value="{{ $instructor->id }}" @selected(is_scalar(old('instructor_id')) && (string) old('instructor_id') === (string) $instructor->id)>{{ $instructor->name }}</option>
            @endforeach
        </select>
        @error('instructor_id') <p>{{ $message }}</p> @enderror

        <label for="starts_at">Start time (UTC)</label>
        <input id="starts_at" type="datetime-local" name="starts_at" value="{{ is_scalar(old('starts_at')) ? old('starts_at') : '' }}" required>
        @error('starts_at') <p>{{ $message }}</p> @enderror

        <label for="duration_minutes">Duration (minutes)</label>
        <input id="duration_minutes" type="number" name="duration_minutes" min="1" max="65535" value="{{ is_scalar(old('duration_minutes')) ? old('duration_minutes') : '' }}" required>
        @error('duration_minutes') <p>{{ $message }}</p> @enderror

        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">Choose a category</option>
            @foreach (['strength' => 'Strength', 'cardio' => 'Cardio', 'relaxation' => 'Relaxation'] as $value => $label)
                <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('category') <p>{{ $message }}</p> @enderror

        <label for="sport">Sport</label>
        <input id="sport" type="text" name="sport" maxlength="100" value="{{ is_scalar(old('sport')) ? old('sport') : '' }}" required>
        @error('sport') <p>{{ $message }}</p> @enderror

        <label for="max_participants">Maximum participants</label>
        <input id="max_participants" type="number" name="max_participants" min="1" max="65535" value="{{ is_scalar(old('max_participants')) ? old('max_participants') : '' }}" required>
        @error('max_participants') <p>{{ $message }}</p> @enderror

        <button type="submit">Create lesson</button>
    </form>
</x-layouts.app>
