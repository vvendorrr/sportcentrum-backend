<x-layouts.app title="Traineraccount aanmaken">
    <section class="mx-auto max-w-3xl" aria-labelledby="create-trainer-heading">
        <div class="mb-7">
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Trainers</p>
            <h1 id="create-trainer-heading" class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Traineraccount aanmaken</h1>
            <p class="mt-2 text-slate-600">Maak een nieuw traineraccount aan. De trainer kan na aanmaak direct inloggen.</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <form class="grid gap-4 sm:grid-cols-2" method="POST" action="{{ route('trainers.store') }}">
                @csrf

                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-bold text-slate-700" for="trainer-name">Naam</label>
                    <input
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                        id="trainer-name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        required
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
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
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
                        value="{{ old('phone_number') }}"
                        autocomplete="tel"
                        @error('phone_number') aria-invalid="true" aria-describedby="trainer-phone-error" @enderror
                    >
                    @error('phone_number')
                        <p class="mt-2 text-sm font-semibold text-red-700" id="trainer-phone-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700" for="trainer-password">Wachtwoord</label>
                    <input
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                        id="trainer-password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        @error('password') aria-invalid="true" aria-describedby="trainer-password-error" @enderror
                    >
                    @error('password')
                        <p class="mt-2 text-sm font-semibold text-red-700" id="trainer-password-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700" for="trainer-password-confirmation">Wachtwoord bevestigen</label>
                    <input
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                        id="trainer-password-confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700" for="trainer-status">Accountstatus</label>
                    <select
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                        id="trainer-status"
                        name="is_active"
                        @error('is_active') aria-invalid="true" aria-describedby="trainer-status-error" @enderror
                    >
                        @php($trainerStatus = old('is_active', '1'))
                        <option value="1" @selected($trainerStatus === '1')>Actief</option>
                        <option value="0" @selected($trainerStatus === '0')>Inactief</option>
                    </select>
                    @error('is_active')
                        <p class="mt-2 text-sm font-semibold text-red-700" id="trainer-status-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-end gap-3 sm:col-span-2 sm:justify-end">
                    <a class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-extrabold text-slate-700 transition hover:border-slate-400 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" href="{{ route('trainers.index') }}">
                        Annuleren
                    </a>
                    <button class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">
                        Traineraccount aanmaken
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-layouts.app>
