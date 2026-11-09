<x-layouts.app title="Account aanmaken" :minimal="true">
    <section class="mx-auto max-w-xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-brand-950/10" aria-labelledby="register-heading">
        <div class="bg-gradient-to-r from-brand-800 to-brand-600 px-7 py-8 text-white sm:px-9">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-100">Word lid van De Linde</p>
            <h1 id="register-heading" class="mt-2 text-3xl font-black tracking-tight">Account aanmaken</h1>
            <p class="mt-2 text-sm text-blue-100">Maak een account aan en schrijf je in voor groepslessen.</p>
        </div>

        <form class="grid gap-5 p-7 sm:grid-cols-2 sm:p-9" method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700" for="name">Naam</label>
                <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="name" type="text" name="name" value="{{ is_scalar(old('name')) ? old('name') : '' }}" autocomplete="name" placeholder="Voor- en achternaam" required>
                @error('name') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700" for="email">E-mailadres</label>
                <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="email" type="email" name="email" value="{{ is_scalar(old('email')) ? old('email') : '' }}" autocomplete="email" placeholder="jij@voorbeeld.nl" required>
                @error('email') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700" for="phone_number">Telefoonnummer</label>
                <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="phone_number" type="tel" name="phone_number" value="{{ is_scalar(old('phone_number')) ? old('phone_number') : '' }}" autocomplete="tel" placeholder="06 12345678" required>
                @error('phone_number') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700" for="password">Wachtwoord</label>
                <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="password" type="password" name="password" autocomplete="new-password" placeholder="Minimaal 8 tekens" required>
                @error('password') <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700" for="password_confirmation">Bevestig wachtwoord</label>
                <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Herhaal je wachtwoord" required>
            </div>

            <div class="sm:col-span-2">
                <button class="w-full rounded-full bg-brand-600 px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">Account aanmaken</button>
                <p class="mt-5 text-center text-sm text-slate-600">Heb je al een account? <a class="font-bold text-brand-700 underline decoration-brand-200 decoration-2 underline-offset-4 hover:text-brand-900" href="{{ route('login') }}">Inloggen</a></p>
            </div>
        </form>
    </section>
</x-layouts.app>
