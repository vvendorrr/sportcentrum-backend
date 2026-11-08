<x-layouts.app title="Inloggen">
    <h1>Inloggen</h1>

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label for="email">E-mailadres</label>
        <input id="email" type="email" name="email" value="{{ is_scalar(old('email')) ? old('email') : '' }}" autocomplete="email" required>
        @error('email') <p>{{ $message }}</p> @enderror

        <label for="password">Wachtwoord</label>
        <input id="password" type="password" name="password" autocomplete="current-password" required>
        @error('password') <p>{{ $message }}</p> @enderror

        <label for="remember">
            <input id="remember" type="checkbox" name="remember" value="1">
            Onthoud mij
        </label>

        <button type="submit">Inloggen</button>
    </form>

    <p>Nog geen account? <a href="{{ route('register') }}">Account aanmaken</a></p>
</x-layouts.app>
