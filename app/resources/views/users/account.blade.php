<main>
    <h1>Accountgegevens</h1>

    <nav aria-label="Accountonderdelen">
        <button type="button" data-account-section="account-info" aria-controls="account-info" aria-current="page">
            Accountgegevens
        </button>
        <button type="button" data-account-section="registered-lessons" aria-controls="registered-lessons">
            Aangemelde lessen
        </button>
        <button type="button" data-account-section="edit-account" aria-controls="edit-account">
            Account bewerken
        </button>
    </nav>

    <section id="account-info" aria-labelledby="account-name-heading">
        <img src="{{ asset('images/profile-placeholder.svg') }}" alt="Profielfoto" width="80" height="80">
        <h2 id="account-name-heading">{{ $account['account_name'] }}</h2>
        <dl>
            <dt>E-mailadres</dt>
            <dd>{{ $account['email'] }}</dd>

            <dt>Voornaam</dt>
            <dd>{{ $account['first_name'] }}</dd>

            <dt>Achternaam</dt>
            <dd>{{ $account['last_name'] }}</dd>

            <dt>Geslacht</dt>
            <dd>{{ $account['gender'] }}</dd>

            <dt>Geboortedatum</dt>
            <dd>
                <time datetime="{{ $account['birth_date'] }}">
                    {{ \Illuminate\Support\Carbon::parse($account['birth_date'])->locale('nl')->translatedFormat('j F Y') }}
                </time>
            </dd>
        </dl>

        <section aria-labelledby="regular-lessons-heading">
            <h3 id="regular-lessons-heading">Vaste lessen</h3>

            @forelse ($regularLessons as $lesson)
                <article>
                    <h4>{{ $lesson['sport'] }}</h4>
                    <dl>
                        <dt>Categorie</dt>
                        <dd>{{ $lesson['category'] }}</dd>

                        <dt>Rooster</dt>
                        <dd>{{ $lesson['schedule'] }}</dd>

                        <dt>Tijd</dt>
                        <dd>{{ $lesson['time'] }}</dd>
                    </dl>
                </article>
            @empty
                <p>Je hebt geen vaste lessen.</p>
            @endforelse
        </section>
    </section>

    <section id="registered-lessons" aria-labelledby="registered-lessons-heading" hidden>
        <h2 id="registered-lessons-heading">Lessen waarvoor je bent aangemeld</h2>

        @forelse ($registeredLessons as $lesson)
            <article>
                <h3>{{ $lesson['sport'] }}</h3>
                <dl>
                    <dt>Categorie</dt>
                    <dd>{{ $lesson['category'] }}</dd>

                    <dt>Datum</dt>
                    <dd>
                        <time datetime="{{ $lesson['starts_at'] }}">
                            {{ \Illuminate\Support\Carbon::parse($lesson['starts_at'])->locale('nl')->translatedFormat('j F Y') }}
                        </time>
                    </dd>

                    <dt>Tijd</dt>
                    <dd>{{ \Illuminate\Support\Carbon::parse($lesson['starts_at'])->format('H:i') }}</dd>
                </dl>
            </article>
        @empty
            <p>Je bent voor geen lessen aangemeld.</p>
        @endforelse
    </section>

    <section id="edit-account" aria-labelledby="edit-account-heading" hidden>
        <h2 id="edit-account-heading">Account bewerken</h2>

        <form>
            <div id="account-settings">
                <label for="profile-picture">Profielfoto</label>
                <input id="profile-picture" name="profile_picture" type="file" accept="image/*">

                <label for="account-name">Gebruikersnaam</label>
                <input id="account-name" name="account_name" type="text" value="{{ $account['account_name'] }}">

                <label for="first-name">Voornaam</label>
                <input id="first-name" name="first_name" type="text" value="{{ $account['first_name'] }}">

                <label for="last-name">Achternaam</label>
                <input id="last-name" name="last_name" type="text" value="{{ $account['last_name'] }}">

                <label for="gender">Geslacht</label>
                <input id="gender" name="gender" type="text" value="{{ $account['gender'] }}">

                <label for="birth-date">Geboortedatum</label>
                <input id="birth-date" name="birth_date" type="date" value="{{ $account['birth_date'] }}">

                <label for="email">E-mailadres</label>
                <input id="email" name="email" type="email" value="{{ $account['email'] }}">

                <button id="open-password-settings" type="button">Wachtwoord wijzigen</button>
                <button type="button">Wijzigingen opslaan</button>
            </div>

            <section id="password-settings" aria-labelledby="password-settings-heading" hidden>
                <h3 id="password-settings-heading">Wachtwoord wijzigen</h3>

                <label for="current-password">Huidig wachtwoord</label>
                <input id="current-password" name="current_password" type="password" autocomplete="current-password">

                <label for="new-password">Nieuw wachtwoord</label>
                <input id="new-password" name="new_password" type="password" autocomplete="new-password">

                <label for="new-password-confirmation">Nieuw wachtwoord bevestigen</label>
                <input id="new-password-confirmation" name="new_password_confirmation" type="password" autocomplete="new-password">

                <button type="button">Wachtwoord opslaan</button>
                <button id="close-password-settings" type="button">Sluiten</button>
            </section>
        </form>
    </section>
</main>

<script>
    const accountSectionButtons = document.querySelectorAll('[data-account-section]');

    accountSectionButtons.forEach((button) => {
        button.addEventListener('click', () => {
            accountSectionButtons.forEach((sectionButton) => {
                const isSelected = sectionButton === button;
                document.getElementById(sectionButton.dataset.accountSection).hidden = !isSelected;

                if (isSelected) {
                    sectionButton.setAttribute('aria-current', 'page');
                } else {
                    sectionButton.removeAttribute('aria-current');
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
