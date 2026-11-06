<main>
    <h1>Traineraccounts beheren</h1>

    <label for="trainer-select">Kies een trainer</label>
    <select id="trainer-select" name="trainer">
        @foreach ($trainers as $trainerOption)
            <option value="{{ $trainerOption['id'] }}" @selected($trainerOption['id'] === $trainer['id'])>
                {{ $trainerOption['name'] }}
            </option>
        @endforeach
    </select>

    <section aria-labelledby="trainer-details-heading">
        <h2 id="trainer-details-heading">Trainergegevens</h2>
        <img src="{{ asset('images/profile-placeholder.svg') }}" alt="Profielfoto van trainer" width="80" height="80">

        <dl>
            <dt>Naam</dt>
            <dd>{{ $trainer['name'] }}</dd>

            <dt>E-mailadres</dt>
            <dd>{{ $trainer['email'] }}</dd>

            <dt>Telefoonnummer</dt>
            <dd>{{ $trainer['phone_number'] }}</dd>

            <dt>Accounttype</dt>
            <dd>{{ $trainer['role'] === 'admin' ? 'Beheerder' : 'Lid' }}</dd>

            <dt>Accountstatus</dt>
            <dd>{{ $trainer['is_active'] ? 'Actief' : 'Inactief' }}</dd>
        </dl>
    </section>

    <section aria-labelledby="edit-trainer-heading">
        <h2 id="edit-trainer-heading">Traineraccount aanpassen</h2>

        <form>
            <label for="trainer-name">Naam</label>
            <input id="trainer-name" name="name" type="text" value="{{ $trainer['name'] }}">

            <label for="trainer-email">E-mailadres</label>
            <input id="trainer-email" name="email" type="email" value="{{ $trainer['email'] }}">

            <label for="trainer-phone">Telefoonnummer</label>
            <input id="trainer-phone" name="phone_number" type="tel" value="{{ $trainer['phone_number'] }}">

            <label for="trainer-role">Accounttype</label>
            <select id="trainer-role" name="role">
                <option value="admin" @selected($trainer['role'] === 'admin')>Beheerder</option>
                <option value="member" @selected($trainer['role'] === 'member')>Lid</option>
            </select>

            <label for="trainer-status">Accountstatus</label>
            <select id="trainer-status" name="is_active">
                <option value="1" @selected($trainer['is_active'])>Actief</option>
                <option value="0" @selected(! $trainer['is_active'])>Inactief</option>
            </select>

            <button type="button">Wijzigingen opslaan</button>
        </form>
    </section>
</main>
