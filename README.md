# Sportcentrum-backend

Online lesrooster en inschrijfsysteem voor **Sportcentrum De Linde**. Leden bekijken het rooster, schrijven zich online in voor lessen (met automatische wachtlijst bij een volle les) en melden zich tijdig af; trainers bekijken deelnemers en aanwezigheid van hun eigen lessen, en beheerders beheren lessen, leden- en traineraccounts en de bezetting van het lesaanbod.

> De applicatiecode staat in de map [`app/`](app/) en is een Laravel + Blade project. De eisen (plan van eisen) en wireframes staan in [`docs/`](docs/).

## Tech stack

| Onderdeel | Keuze |
| --- | --- |
| Backend | [Laravel](https://laravel.com/docs) 13, PHP ≥ 8.3 |
| Views | Blade met één gedeelde layout (geen React/Vue/Inertia) |
| Styling | [Tailwind CSS](https://tailwindcss.com) 4 via [Vite](https://vitejs.dev) |
| Database | SQLite (default), schema via migrations, data via seeders |
| Authenticatie | Session-based, met de rollen `member` (lid), `trainer` en `admin` (beheerder) |
| Testen | [Pest](https://pestphp.com) feature tests |
| Code style | [Laravel Pint](https://laravel.com/docs/pint) |

## Repository layout

```
sportcentrum-backend/
├── app/                  # De Laravel-applicatie
│   ├── app/              # Controllers, models, policies, services
│   ├── database/         # Migrations, factories, seeders (SQLite)
│   ├── resources/views/  # Blade views (components/, lessons/, users/, ...)
│   ├── routes/web.php    # Alle HTTP-routes
│   └── tests/Feature/    # Pest feature tests
└── docs/                 # Plan van eisen + wireframes
```

## Installation

### 1. Prerequisites

Installeer **PHP ≥ 8.3**, **Composer** en **Node.js** (met npm).

**Windows** (PowerShell) — installeert PHP en Composer in één keer:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager] -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

**macOS** — makkelijkst via [Laravel Herd](https://herd.laravel.com), plus `brew install composer`. Zonder Herd:

```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

**Linux**:

```bash
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

**Node.js** — installeer de nieuwste LTS van [nodejs.org](https://nodejs.org) (of `nvm install --lts`, `winget install OpenJS.NodeJS.LTS`, `scoop install nodejs-lts`).

**Controleer** of alles werkt (in een **nieuw** terminalvenster):

```bash
php -v        # 8.3 of nieuwer
composer -V
node -v && npm -v
```

### 2. One-command setup (aanbevolen)

```bash
git clone https://github.com/vvendorrr/sportcentrum-backend.git
cd sportcentrum-backend/app
composer run setup
```

`composer run setup` regelt alles in één keer: PHP-dependencies installeren, `.env` aanmaken, app key genereren, migrations draaien, npm-dependencies installeren en de frontend-assets bouwen.

### 3. Manual setup (alternatief)

Voer deze stappen uit vanuit `app/`:

```bash
# 1. PHP-dependencies
composer install

# 2. Omgevingsbestand + app key
cp .env.example .env            # Windows PowerShell: Copy-Item .env.example .env
php artisan key:generate

# 3. Database (SQLite; er hoeft niets geconfigureerd te worden)
touch database/database.sqlite
php artisan migrate --seed

# 4. Frontend-dependencies + assets bouwen
npm install
npm run build
```

> **Windows-tip:** PowerShell kent `touch` niet; gebruik daar `ni database/database.sqlite` (New-Item). In Git Bash werkt `touch` gewoon. Standaard gebruikt het project SQLite (`DB_CONNECTION=sqlite` in `.env.example`); wil je MySQL gebruiken, pas dan de `DB_*`-waarden in `.env` aan.

### 4. Start de applicatie

**Ontwikkelen** (serveert de app en Vite tegelijk, met hot reload voor Blade/Tailwind):

```bash
composer run dev
```

**Productie-achtig** (alleen als je CSS/JS hebt gewijzigd en niet met dev-server werkt):

```bash
php artisan serve   # http://localhost:8000
npm run build
```

Open daarna **http://localhost:8000**. Krijg je een Vite-manifestfout of zien pagina's er onstyleerd uit? Draai dan eerst `npm run build` (of gewoon `composer run dev`).

### 5. Demo-accounts (seeded data)

Heb je het project via `composer run setup` opgezet? Draai daarna nog `php artisan db:seed` voor de demo-data. Bij de handmatige setup zit `--seed` al in de migrate-stap. In totaal maak je dan: twee benoemde beheerders, vier willekeurige beheerders, vier trainers, acht leden, lessen en lesinschrijvingen aan.

| Rol | E-mail | Wachtwoord |
| --- | --- | --- |
| Beheerder | `beheerder@example.com` | `password` |
| Beheerder | `r.dewit@example.com` | `password` |
| Trainer (willekeurig, via Trainers-overzicht) | — | `password` |
| Lid (willekeurig, via overzicht) | — | `password` |

Het wachtwoord van **alle** seeded accounts is `password`. Leden e-mailadressen zie je in de Deelnemers-pagina (als beheerder ingelogd).

## Testing & tooling

```bash
# Pest-testsuite (binnen app/)
vendor/bin/pest                    # of: php artisan test --compact

# Eén testbestand draaien
vendor/bin/pest tests/Feature/AccountControllerTest.php

# Code style (fixen, en check)
vendor/bin/pint --dirty
vendor/bin/pint --test

# Verse demo-data in de database
php artisan migrate:fresh --seed
```

## Implementation plan

Deze sectie houdt bij hoe ver de user stories uit [`docs/plan-van-eisen.md`](docs/plan-van-eisen.md) zijn ten opzichte van de huidige code.

| ID  | User story | Status | Waar in de code |
| --- | --- | :---: | --- |
| US-01 | Rooster bekijken (aantallen per les, geen namen) | ✅ Afgerond | `LessonController::index/show`, `resources/views/lessons/` |
| US-02 | Account aanmaken en inloggen | ✅ Afgerond | `RegistrationController`, `LoginController`, `resources/views/auth/` |
| US-03 | Inschrijven voor een les (`enrolled` / wachtlijst) | ✅ Afgerond | `LessonRegistrationController::store`, `Lesson`-model, policies |
| US-04 | Afmelden voor een les (tot 2 uur voor start, wachtlijst-promotie) | ✅ Afgerond | `LessonRegistrationController::destroy` |
| US-05 | Deelnemers en aanwezigheid beheren | ✅ Afgerond | `LessonAttendanceController`, `PeopleDirectoryController::participants` |
| US-06 | Lessen aanmaken en inplannen | ✅ Afgerond | `LessonController::create/edit/store/update/destroy` |
| US-07 | Accountgegevens beheren (profiel, e-mail, wachtwoord) | ✅ Afgerond | `AccountController::update/updateEmail/updatePassword` (`PATCH`-endpoints) |
| US-08 | Ledenaccounts uitschakelen | ✅ Afgerond | `MemberAccountController::deactivate`, `UserPolicy::deactivate`, `EnsureActiveUser` |
| US-09 | Traineraccounts beheren | ✅ Afgerond | `TrainerAccountController`, `StoreTrainerRequest`, `UpdateTrainerRequest` — trainer en beheerder zijn aparte rollen |
| US-10 | Eigen inschrijvingen bekijken | ✅ Afgerond | `MyLessonRegistrationController::index` |
| US-11 | Rooster filteren | ✅ Afgerond | `LessonIndexRequest`, `LessonController::index` |
| US-12 | Bezettingsoverzicht voor beheerder | ✅ Afgerond | `LessonOccupancyController`, `resources/views/lessons/occupancy.blade.php` |
| US-13 | Afmelding door medewerker registreren | ⬜ Besluit opdrachtgever nodig | Bevoegde rol en uitzondering op de afmeldtermijn zijn nog niet vastgesteld |
| US-14 | Komende lessen voor trainer | ✅ Afgerond | `TrainerUpcomingLessonsController`, `resources/views/lessons/trainer-upcoming.blade.php` |
| US-15 | Wachtlijststatus bekijken | ✅ Afgerond | `MyLessonRegistrationController::index`, `resources/views/registrations/index.blade.php` |
| US-16 | Rollen en bevoegdheden vaststellen | ⬜ Besluit opdrachtgever nodig | Baliemedewerker en bevoegdheden per rol moeten nog met de opdrachtgever worden afgestemd |
| US-17 | Onverwachte deelnemers afhandelen | ⬜ Besluit opdrachtgever nodig | Aansluiten zonder inschrijving en handelen bij volle lessen zijn nog niet vastgesteld |
| US-18 | Nederlandstalige, sportieve website | ⬜ Te valideren | Nederlandse teksten en blauw-witte vormgeving zijn aanwezig; definitieve goedkeuring van de opdrachtgever ontbreekt |

### Openstaande besluiten en validatie

- **US-13** — bevestig welke medewerkers namens leden mogen afmelden en of zij na de twee-uursgrens mogen afmelden.
- **US-16** — bevestig of een baliemedewerker een aparte rol is en leg de volledige bevoegdhedenmatrix voor trainers en beheerders vast.
- **US-17** — bepaal hoe deelnemers zonder inschrijving en situaties zonder beschikbare plek worden afgehandeld.
- **US-18** — laat de opdrachtgever de blauw-witte, Nederlandstalige vormgeving beoordelen.
- **US-12** — bepaal welke aantallen of grens een les als "weinig bezocht" markeren in het bezettingsoverzicht.
- **US-15** — bevestig dat leden bij wachtlijststatus of promotie alleen de website raadplegen; e-mail/sms-meldingen vallen buiten scope.
- **US-07 aanvullende velden** — de opdrachtgever moet nog bepalen of geslacht en geboortedatum worden bewaard; deze velden vallen buiten de uitgewerkte criteria voor US-07.

### Niet-functionele eisen (uit plan van eisen)

- Minimaal 10 feature tests met Pest (de suite telt nu 109 tests, vrijwel allemaal feature tests).
- Blade-views met één gedeelde layout, Form Requests met Nederlandstalige meldingen.
- Branches per user story, pull requests met review.

## Development conventions

- Volg de richtlijnen in [`app/AGENTS.md`](app/AGENTS.md) — gegenereerd met [Laravel Boost](https://laravel.com/docs/ai), dat ook AI-agent-tooling levert (`php artisan boost:install`).
- Run `vendor/bin/pint --dirty` vóór het committen van PHP-wijzigingen.
- Vraag review op je pull request en laat de volledige testsuite draaien.
