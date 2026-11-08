<x-layouts.app title="Create lesson">
    <h1 class="mb-6 text-3xl font-bold">Create lesson</h1>

    <form class="max-w-xl space-y-4" method="POST" action="{{ route('lessons.store') }}">
        @csrf

        <label class="block">
            <span class="mb-1 block">Instructor</span>
            <select class="w-full rounded border border-slate-300 p-2" name="instructor_id" required>
                <option value="">Choose an instructor</option>
                @foreach ($instructors as $instructor)
                    <option value="{{ $instructor->id }}" @selected(is_scalar(old('instructor_id')) && (string) old('instructor_id') === (string) $instructor->id)>{{ $instructor->name }}</option>
                @endforeach
            </select>
            @error('instructor_id') <span class="text-red-700">{{ $message }}</span> @enderror
        </label>

        <label class="block">
            <span class="mb-1 block">Start time (UTC)</span>
            <input class="w-full rounded border border-slate-300 p-2" type="datetime-local" name="starts_at" value="{{ is_scalar(old('starts_at')) ? old('starts_at') : '' }}" required>
            @error('starts_at') <span class="text-red-700">{{ $message }}</span> @enderror
        </label>

        <label class="block">
            <span class="mb-1 block">Duration (minutes)</span>
            <input class="w-full rounded border border-slate-300 p-2" type="number" name="duration_minutes" min="1" max="65535" value="{{ is_scalar(old('duration_minutes')) ? old('duration_minutes') : '' }}" required>
            @error('duration_minutes') <span class="text-red-700">{{ $message }}</span> @enderror
        </label>

        <label class="block">
            <span class="mb-1 block">Category</span>
            <select class="w-full rounded border border-slate-300 p-2" name="category" required>
                <option value="">Choose a category</option>
                @foreach (['strength' => 'Strength', 'cardio' => 'Cardio', 'relaxation' => 'Relaxation'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('category') <span class="text-red-700">{{ $message }}</span> @enderror
        </label>

        <label class="block">
            <span class="mb-1 block">Sport</span>
            <input class="w-full rounded border border-slate-300 p-2" type="text" name="sport" maxlength="100" value="{{ is_scalar(old('sport')) ? old('sport') : '' }}" required>
            @error('sport') <span class="text-red-700">{{ $message }}</span> @enderror
        </label>

        <label class="block">
            <span class="mb-1 block">Maximum participants</span>
            <input class="w-full rounded border border-slate-300 p-2" type="number" name="max_participants" min="1" max="65535" value="{{ is_scalar(old('max_participants')) ? old('max_participants') : '' }}" required>
            @error('max_participants') <span class="text-red-700">{{ $message }}</span> @enderror
        </label>

        <button class="rounded bg-blue-700 px-4 py-2 text-white" type="submit">Create lesson</button>
    </form>
</x-layouts.app>
