<x-layouts.app title="Accountinstellingen">
    @php($securityHasErrors = $errors->hasAny(['email', 'current_password', 'new_password', 'password']))
    @php($passwordHasErrors = $errors->hasAny(['current_password', 'new_password']))
    @php($securityActive = $securityHasErrors || request()->query('tab') === 'security')

    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Jouw account</p>
        <h1 class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Accountinstellingen</h1>
        <p class="mt-2 text-slate-600">Beheer je profielgegevens en de beveiliging van je account.</p>
    </div>

    <nav class="mb-6 flex flex-wrap gap-3" aria-label="Accountinstellingen">
        <button
            @class([
                'rounded-full border px-5 py-2.5 text-sm font-extrabold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600',
                'border-brand-600 bg-brand-600 text-white' => ! $securityActive,
                'border-slate-300 text-slate-700 hover:border-brand-200 hover:text-brand-800' => $securityActive,
            ])
            data-account-section="account-info"
            aria-controls="account-info"
            @if (! $securityActive) aria-current="page" @endif
        >
            Account info
        </button>
        <button
            @class([
                'rounded-full border px-5 py-2.5 text-sm font-extrabold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600',
                'border-brand-600 bg-brand-600 text-white' => $securityActive,
                'border-slate-300 text-slate-700 hover:border-brand-200 hover:text-brand-800' => ! $securityActive,
            ])
            data-account-section="account-security"
            aria-controls="account-security"
            @if ($securityActive) aria-current="page" @endif
        >
            Account beveiliging
        </button>
    </nav>

    <section id="account-info" aria-labelledby="account-info-heading" @if ($securityActive) hidden @endif>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 id="account-info-heading" class="text-xl font-black tracking-tight text-brand-950">Account info</h2>

            <form class="mt-6" method="POST" action="{{ route('account.update') }}">
                @csrf
                @method('PATCH')

                <h3 class="text-sm font-bold uppercase tracking-[0.14em] text-slate-500">Mijn profiel</h3>

                <div class="mt-4 flex flex-wrap items-start gap-5">
                    <img src="{{ asset('images/profile-placeholder.svg') }}" alt="Profielfoto" width="80" height="80" class="rounded-full object-cover">
                    <div class="flex flex-1 flex-wrap gap-4">
                        <div class="min-w-40 flex-1">
                            <label class="mb-2 block text-sm font-bold text-slate-700" for="first-name">Voornaam</label>
                            <input
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                                id="first-name"
                                name="first_name"
                                type="text"
                                value="{{ old('first_name', $account['first_name']) }}"
                                @error('first_name') aria-invalid="true" aria-describedby="first-name-error" @enderror
                            >
                            @error('first_name')
                                <p class="mt-2 text-sm font-semibold text-red-700" id="first-name-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="min-w-40 flex-1">
                            <label class="mb-2 block text-sm font-bold text-slate-700" for="last-name">Achternaam</label>
                            <input
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                                id="last-name"
                                name="last_name"
                                type="text"
                                value="{{ old('last_name', $account['last_name']) }}"
                                @error('last_name') aria-invalid="true" aria-describedby="last-name-error" @enderror
                            >
                            @error('last_name')
                                <p class="mt-2 text-sm font-semibold text-red-700" id="last-name-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="min-w-40 flex-1">
                            <label class="mb-2 block text-sm font-bold text-slate-700" for="phone-number">Telefoonnummer</label>
                            <input
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                                id="phone-number"
                                name="phone_number"
                                type="tel"
                                value="{{ old('phone_number', $account['phone_number']) }}"
                                @error('phone_number') aria-invalid="true" aria-describedby="phone-number-error" @enderror
                            >
                            @error('phone_number')
                                <p class="mt-2 text-sm font-semibold text-red-700" id="phone-number-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
                    <button class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">
                        Wijzigingen opslaan
                    </button>
                    <button class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-extrabold text-slate-700 transition hover:border-slate-400 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="reset">
                        Wijzigingen vergeten
                    </button>
                </div>
            </form>

            <section class="mt-8 border-t border-slate-100 pt-6" aria-labelledby="registered-lessons-heading">
                <h3 id="registered-lessons-heading" class="text-sm font-bold uppercase tracking-[0.14em] text-slate-500">Ingeschreven lessen</h3>

                @forelse ($registeredLessons as $lesson)
                    <article class="mt-4 rounded-2xl bg-slate-50 p-4 text-sm">
                        <h4 class="text-base font-extrabold text-brand-950">{{ $lesson['sport'] }}</h4>
                        <dl class="mt-2 flex flex-wrap gap-x-6 gap-y-1">
                            <div class="flex gap-2">
                                <dt class="font-semibold text-slate-500">Categorie</dt>
                                <dd class="font-bold text-slate-800">{{ $lesson['category'] }}</dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="font-semibold text-slate-500">Datum</dt>
                                <dd class="font-bold text-slate-800">
                                    <time datetime="{{ $lesson['starts_at'] }}">
                                        {{ \Illuminate\Support\Carbon::parse($lesson['starts_at'])->locale('nl')->translatedFormat('j F Y') }}
                                    </time>
                                </dd>
                            </div>
                            <div class="flex gap-2">
                                <dt class="font-semibold text-slate-500">Tijd</dt>
                                <dd class="font-bold text-slate-800">{{ \Illuminate\Support\Carbon::parse($lesson['starts_at'])->format('H:i') }}</dd>
                            </div>
                        </dl>
                    </article>
                @empty
                    <p class="mt-4 text-sm text-slate-600">Je bent voor geen komende lessen aangemeld.</p>
                @endforelse
            </section>
        </div>
    </section>

    <section id="account-security" aria-labelledby="account-security-heading" @if (! $securityActive) hidden @endif>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 id="account-security-heading" class="text-xl font-black tracking-tight text-brand-950">Account beveiliging</h2>

            <form class="mt-6" method="POST" action="{{ route('account.email.update') }}">
                @csrf
                @method('PATCH')

                <label class="mb-2 block text-sm font-bold text-slate-700" for="email">E-mailadres</label>
                <input
                    class="w-full max-w-md rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $account['email']) }}"
                    autocomplete="email"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                >
                @error('email')
                    <p class="mt-2 text-sm font-semibold text-red-700" id="email-error" role="alert">{{ $message }}</p>
                @enderror

                <button class="mt-5 rounded-full border border-red-200 px-5 py-2.5 text-sm font-extrabold text-red-700 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600" id="open-password-settings" type="button">
                    Wachtwoord wijzigen
                </button>

                <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
                    <button class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">
                        Wijzigingen opslaan
                    </button>
                    <button class="rounded-full border border-red-200 px-5 py-2.5 text-sm font-extrabold text-red-700 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600" type="reset">
                        Wijzigingen vergeten
                    </button>
                </div>
            </form>

            <form
                id="password-settings"
                class="mt-6 max-w-md space-y-4 border-t border-slate-100 pt-6"
                method="POST"
                action="{{ route('account.password.update') }}"
                @if (! $passwordHasErrors) hidden @endif
            >
                @csrf
                @method('PATCH')

                <h3 class="text-sm font-bold uppercase tracking-[0.14em] text-slate-500">Wachtwoord wijzigen</h3>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700" for="current-password">Huidig wachtwoord</label>
                    <input
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                        id="current-password"
                        name="current_password"
                        type="password"
                        autocomplete="current-password"
                        required
                        @error('current_password') aria-invalid="true" aria-describedby="current-password-error" @enderror
                    >
                    @error('current_password')
                        <p class="mt-2 text-sm font-semibold text-red-700" id="current-password-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700" for="new-password">Nieuw wachtwoord</label>
                    <input
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                        id="new-password"
                        name="new_password"
                        type="password"
                        autocomplete="new-password"
                        required
                        @error('new_password') aria-invalid="true" aria-describedby="new-password-error" @enderror
                    >
                    @error('new_password')
                        <p class="mt-2 text-sm font-semibold text-red-700" id="new-password-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700" for="new-password-confirmation">Nieuw wachtwoord bevestigen</label>
                    <input
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100"
                        id="new-password-confirmation"
                        name="new_password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="flex flex-wrap gap-3 pt-1">
                    <button class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="submit">
                        Wachtwoord opslaan
                    </button>
                    <button class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-extrabold text-slate-700 transition hover:border-slate-400 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" id="close-password-settings" type="button">
                        Sluiten
                    </button>
                </div>
            </form>

            <section class="mt-8 rounded-2xl border border-red-200 bg-red-50 p-5" aria-labelledby="delete-account-heading">
                <h3 id="delete-account-heading" class="text-lg font-extrabold text-red-900">Account verwijderen</h3>
                <p class="mt-2 text-sm text-red-800">
                    Je account en lesinschrijvingen worden definitief verwijderd. Lessen waarvoor je trainer bent blijven bestaan zonder trainer.
                </p>

                <form class="mt-4 max-w-md space-y-3" method="POST" action="{{ route('account.destroy') }}" onsubmit="return confirm('Weet je zeker dat je je account definitief wilt verwijderen?')">
                    @csrf
                    @method('DELETE')

                    <label class="block text-sm font-bold text-red-950" for="delete-account-password">Huidig wachtwoord</label>
                    <input
                        class="w-full rounded-xl border border-red-200 bg-white px-4 py-3 text-sm text-slate-900 focus-visible:outline-2 focus-visible:outline-red-600"
                        id="delete-account-password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        @if ($errors->has('password')) aria-describedby="delete-account-password-error" @endif
                        @if ($errors->has('password')) autofocus @endif
                    >
                    @error('password')
                        <p class="text-sm font-semibold text-red-800" id="delete-account-password-error" role="alert">{{ $message }}</p>
                    @enderror

                    <button class="rounded-full bg-red-700 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-red-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700" type="submit">
                        Account definitief verwijderen
                    </button>
                </form>
            </section>
        </div>
    </section>

    <script>
        const accountSectionButtons = document.querySelectorAll('[data-account-section]');

        accountSectionButtons.forEach((button) => {
            button.addEventListener('click', () => {
                accountSectionButtons.forEach((sectionButton) => {
                    const isSelected = sectionButton === button;
                    document.getElementById(sectionButton.dataset.accountSection).hidden = !isSelected;

                    if (isSelected) {
                        sectionButton.setAttribute('aria-current', 'page');
                        sectionButton.classList.add('border-brand-600', 'bg-brand-600', 'text-white');
                        sectionButton.classList.remove('border-slate-300', 'text-slate-700');
                    } else {
                        sectionButton.removeAttribute('aria-current');
                        sectionButton.classList.remove('border-brand-600', 'bg-brand-600', 'text-white');
                        sectionButton.classList.add('border-slate-300', 'text-slate-700');
                    }
                });
            });
        });

        const passwordSettings = document.getElementById('password-settings');
        const openPasswordSettingsButton = document.getElementById('open-password-settings');
        const closePasswordSettingsButton = document.getElementById('close-password-settings');

        openPasswordSettingsButton.addEventListener('click', () => {
            passwordSettings.hidden = false;
            document.getElementById('current-password').focus();
        });

        closePasswordSettingsButton.addEventListener('click', () => {
            passwordSettings.hidden = true;
            openPasswordSettingsButton.focus();
        });
    </script>
</x-layouts.app>
