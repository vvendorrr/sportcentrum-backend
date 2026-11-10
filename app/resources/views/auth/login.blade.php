<x-layouts.app title="Inloggen" :minimal="true">
    <section class="mx-auto max-w-md overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-brand-950/10" aria-labelledby="login-heading">
        <div class="bg-gradient-to-r from-brand-800 to-brand-600 px-7 py-8 text-white sm:px-9">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-100">Fijn dat je er bent</p>
            <h1 id="login-heading" class="mt-2 text-3xl font-black tracking-tight">Inloggen</h1>
            <p class="mt-2 text-sm text-blue-100">Log in en bekijk jouw lessen.</p>
        </div>

        <form class="space-y-5 p-7 sm:p-9" method="POST" action="{{ route('login.store') }}">
            @csrf

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700" for="email">E-mailadres</label>
                <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="email" type="email" name="email" value="{{ is_scalar(old('email')) ? old('email') : '' }}" autocomplete="email" placeholder="jij@voorbeeld.nl" required>
                @error('email') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700" for="password">Wachtwoord</label>
                <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="password" type="password" name="password" autocomplete="current-password" placeholder="Je wachtwoord" required>
                @error('password') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-3 text-sm font-semibold text-slate-600" for="remember">
                <input class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" id="remember" type="checkbox" name="remember" value="1">
                Onthoud mij
            </label>

            <button class="w-full rounded-full bg-brand-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">Inloggen</button>

            <p class="text-center text-sm text-slate-600">Nog geen account? <a class="font-bold text-brand-700 underline decoration-brand-200 decoration-2 underline-offset-4 hover:text-brand-900" href="{{ route('register') }}">Account aanmaken</a></p>
        </form>
    </section>
</x-layouts.app>
