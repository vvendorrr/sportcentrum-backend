<x-layouts.app title="Accountinstellingen">
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.16em] text-brand-600">Jouw account</p>
        <h1 class="mt-1 text-3xl font-black tracking-tight text-brand-950 sm:text-4xl">Accountinstellingen</h1>
        <p class="mt-2 text-slate-600">Beheer je profielgegevens en de beveiliging van je account.</p>
    </div>

    <nav class="mb-6 flex flex-wrap gap-3" aria-label="Accountinstellingen">
        <button
            @class([
                'rounded-full border px-5 py-2.5 text-sm font-extrabold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600',
                'border-brand-600 bg-brand-600 text-white' => ! $errors->has('password'),
                'border-slate-300 text-slate-700 hover:border-brand-200 hover:text-brand-800' => $errors->has('password'),
            ])
            data-account-section="account-info"
            aria-controls="account-info"
            @if (! $errors->has('password')) aria-current="page" @endif
        >
            Account info
        </button>
        <button
            @class([
                'rounded-full border px-5 py-2.5 text-sm font-extrabold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600',
                'border-brand-600 bg-brand-600 text-white' => $errors->has('password'),
                'border-slate-300 text-slate-700 hover:border-brand-200 hover:text-brand-800' => ! $errors->has('password'),
            ])
            data-account-section="account-security"
            aria-controls="account-security"
            @if ($errors->has('password')) aria-current="page" @endif
        >
            Account beveiliging
        </button>
    </nav>

    <section id="account-info" aria-labelledby="account-info-heading" @if ($errors->has('password')) hidden @endif>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 id="account-info-heading" class="text-xl font-black tracking-tight text-brand-950">Account info</h2>

            <form class="mt-6">
                <h3 class="text-sm font-bold uppercase tracking-[0.14em] text-slate-500">Mijn profiel</h3>

                <div class="mt-4 flex flex-wrap items-center gap-5">
                    <img src="{{ asset('images/profile-placeholder.svg') }}" alt="Profielfoto" width="80" height="80" class="rounded-full object-cover">
                    <div class="flex flex-1 flex-wrap gap-4">
                        <div class="min-w-40 flex-1">
                            <label class="mb-2 block text-sm font-bold text-slate-700" for="first-name">Voornaam</label>
                            <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="first-name" name="first_name" type="text" value="{{ $account['first_name'] }}">
                        </div>
                        <div class="min-w-40 flex-1">
                            <label class="mb-2 block text-sm font-bold text-slate-700" for="last-name">Achternaam</label>
                            <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="last-name" name="last_name" type="text" value="{{ $account['last_name'] }}">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
                    <button class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="button">
                        Wijzigingen opslaan
                    </button>
                    <button class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-extrabold text-slate-700 transition hover:border-slate-400 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="reset">
                        Wijzigingen vergeten
                    </button>
                </div>
            </form>

            <section class="mt-8 border-t border-slate-100 pt-6" aria-labelledby="recent-lessons-heading">
                <h3 id="recent-lessons-heading" class="text-sm font-bold uppercase tracking-[0.14em] text-slate-500">Laatst gevolgde lessen</h3>

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

    <section id="account-security" aria-labelledby="account-security-heading" @if (! $errors->has('password')) hidden @endif>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <form>
                <h2 id="account-security-heading" class="text-xl font-black tracking-tight text-brand-950">Account beveiliging</h2>

                <div id="account-settings" class="mt-6">
                    <label class="mb-2 block text-sm font-bold text-slate-700" for="email">E-mailadres</label>
                    <input class="w-full max-w-md rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="email" name="email" type="email" value="{{ $account['email'] }}" autocomplete="email">

                    <button class="mt-5 rounded-full border border-red-200 px-5 py-2.5 text-sm font-extrabold text-red-700 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600" id="open-password-settings" type="button">
                        Wachtwoord wijzigen
                    </button>
                </div>

                <div id="password-settings" class="mt-6 max-w-md space-y-4" hidden>
                    <h3 class="text-sm font-bold uppercase tracking-[0.14em] text-slate-500">Wachtwoord wijzigen</h3>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700" for="current-password">Huidig wachtwoord</label>
                        <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="current-password" name="current_password" type="password" autocomplete="current-password">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700" for="new-password">Nieuw wachtwoord</label>
                        <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="new-password" name="new_password" type="password" autocomplete="new-password">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700" for="new-password-confirmation">Nieuw wachtwoord bevestigen</label>
                        <input class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-4 focus:ring-brand-100" id="new-password-confirmation" name="new_password_confirmation" type="password" autocomplete="new-password">
                    </div>

                    <div class="flex flex-wrap gap-3 pt-1">
                        <button class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="button">
                            Wachtwoord opslaan
                        </button>
                        <button class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-extrabold text-slate-700 transition hover:border-slate-400 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" id="close-password-settings" type="button">
                            Sluiten
                        </button>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
                    <button class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600" type="button">
                        Wijzigingen opslaan
                    </button>
                    <button class="rounded-full border border-red-200 px-5 py-2.5 text-sm font-extrabold text-red-700 transition hover:bg-red-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600" type="reset">
                        Wijzigingen vergeten
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

        const accountSettings = document.getElementById('account-settings');
        const passwordSettings = document.getElementById('password-settings');
        const openPasswordSettingsButton = document.getElementById('open-password-settings');
        const closePasswordSettingsButton = document.getElementById('close-password-settings');

        openPasswordSettingsButton.addEventListener('click', () => {
            accountSettings.hidden = true;
            passwordSettings.hidden = false;
            document.getElementById('current-password').focus();
        });

        closePasswordSettingsButton.addEventListener('click', () => {
            passwordSettings.hidden = true;
            accountSettings.hidden = false;
            openPasswordSettingsButton.focus();
        });

        const currentPasswordInput = document.getElementById('current-password');
        const newPasswordInput = document.getElementById('new-password');
        const newPasswordConfirmationInput = document.getElementById('new-password-confirmation');
        const savePasswordButton = passwordSettings.querySelector('button[type="button"]');

        const updatePasswordRequirements = () => {
            currentPasswordInput.required = true;
            newPasswordInput.required = true;
            newPasswordConfirmationInput.required = true;
            newPasswordConfirmationInput.setCustomValidity(
                newPasswordConfirmationInput.value !== '' && newPasswordConfirmationInput.value !== newPasswordInput.value
                    ? 'De nieuwe wachtwoorden komen niet overeen.'
                    : ''
            );
        };

        newPasswordInput.addEventListener('input', updatePasswordRequirements);
        newPasswordConfirmationInput.addEventListener('input', updatePasswordRequirements);
        savePasswordButton.addEventListener('click', () => {
            updatePasswordRequirements();
            currentPasswordInput.reportValidity();
            newPasswordInput.reportValidity();
            newPasswordConfirmationInput.reportValidity();
        });
    </script>
</x-layouts.app>
