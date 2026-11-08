<x-layouts.app title="Account aanmaken">
    <h1>Account aanmaken</h1>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <label for="name">Naam</label>
        <input id="name" type="text" name="name" value="{{ is_scalar(old('name')) ? old('name') : '' }}" autocomplete="name" required>
        @error('name') <p>{{ $message }}</p> @enderror

        <label for="email">E-mailadres</label>
        <input id="email" type="email" name="email" value="{{ is_scalar(old('email')) ? old('email') : '' }}" autocomplete="email" required>
        @error('email') <p>{{ $message }}</p> @enderror

        <label for="phone_number">Telefoonnummer</label>
        <input id="phone_number" type="tel" name="phone_number" value="{{ is_scalar(old('phone_number')) ? old('phone_number') : '' }}" autocomplete="tel" required>
        @error('phone_number') <p>{{ $message }}</p> @enderror

        <label for="password">Wachtwoord</label>
        <input id="password" type="password" name="password" autocomplete="new-password" required>
        @error('password') <p>{{ $message }}</p> @enderror

        <label for="password_confirmation">Bevestig wachtwoord</label>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>

        <button type="submit">Account aanmaken</button>
    </form>

    <p>Heb je al een account? <a href="{{ route('login') }}">Inloggen</a></p>
</x-layouts.app>
