# HealthTrack

A web-based patient information management system for the Barangay Health Center
of Mambog I, Bacoor, Cavite.

Built with Laravel, Livewire, Blade and Tailwind CSS, on PostgreSQL.

---

## What it does

| Role | Can do |
|------|--------|
| **Midwife** | Clinical records (diagnoses, lab values, doctor's notes, medical history, allergies), appointments, full patient access |
| **Health Worker** | Registers patients and maintains their details |
| **Patient** | Views their own records. Read-only. |

Accounts cannot be self-created. Staff accounts come from the seeder; patient
accounts are created by a health worker during registration.

---

## Requirements

- PHP 8.3 or newer, with the `pdo_sqlite` and `mbstring` extensions
  (add `pdo_pgsql` if you are using PostgreSQL)
- Composer
- Node.js 20 or newer
- PostgreSQL 14 or newer — **optional for local development**, see below

---

## Setup — the easy way (Windows)

Double-click **`Setup.bat`** in this folder. A window opens with four numbered
buttons; work down them in order.

| Button | What it does |
|---|---|
| 1. Install prerequisites | Downloads PHP, Composer and Node.js if they are missing. Also launches the PostgreSQL installer, if you picked PostgreSQL. |
| 2. Install dependencies | `composer install` and `npm install`. |
| 3. Set up database | Writes `.env`, creates the database, runs migrations and loads demo data. |
| 4. Start server | Starts the site and opens it in your browser. |

Everything it installs goes under your user profile, so no administrator rights
are needed (the Node.js and PostgreSQL installers may still prompt).

**Choosing a database.** PostgreSQL is selected by default — it is what the
study specifies and what this project targets. Button 1 installs it and opens
its setup wizard, which asks you to choose a superuser password. Remember that
password, type it into the Password box, then press button 3. It is written
only to your local `.env`.

SQLite is offered as a fallback. It needs nothing installed and is useful for
getting the site running quickly, but it is not the database this system is
meant to run on.

Every button is safe to press again. The only destructive control is
*Reset database*, which drops every table and reloads the demo data; it asks
first.

If something goes wrong, the log pane at the bottom shows exactly which command
failed. The same functions can be run from a terminal without the window:

```powershell
. .\tools\setup\lib.ps1
```

Then call e.g. `Get-Prerequisites` or `Install-Dependencies -Log { param($m) Write-Host $m }`.

---

## Setup — by hand

```bash
composer install
```

```bash
npm install
```

Copy the environment file and generate a key:

```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

`.env.example` ships configured for **SQLite**, so there is no database server
to install and no password to set. Create the (empty) database file:

```bash
touch database/database.sqlite
```

Then build the schema and load demo data:

```bash
php artisan migrate --seed
```

Finally, compile the stylesheet:

```bash
npm run build
```

### Using PostgreSQL instead

The study specifies PostgreSQL, and the app runs on it unchanged. Create the
database, then swap the commented block in `.env`:

```bash
createdb healthtrack
```

Comment out the two `DB_CONNECTION=sqlite` / `DB_DATABASE=` lines, uncomment the
`pgsql` block below them, set `DB_USERNAME` and `DB_PASSWORD`, and re-run
`php artisan migrate --seed`.

The test suite always runs on in-memory SQLite regardless — see `phpunit.xml`.

---

## Running it

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

That is all you need if you are not editing CSS, because `npm run build` has
already compiled the stylesheet. If you *are* editing `resources/css/app.css`
or any Blade file's classes, run Vite in a second terminal for live rebuilds:

```bash
npm run dev
```

---

## Demo accounts

All seeded accounts use the password `password`. **Never deploy with these.**

| Email | Role | Lands on |
|-------|------|----------|
| `midwife@healthtrack.test` | Midwife | `/midwife/dashboard` |
| `healthworker@healthtrack.test` | Health Worker | `/health-worker/dashboard` |
| `patient@healthtrack.test` | Patient | `/patient/dashboard` |

Login redirects by role on its own — there is no role picker. The destination
comes from `UserRole::homeRoute()`.

### What each role can do

| | Midwife | Health Worker | Patient |
|---|:---:|:---:|:---:|
| Own dashboard | yes | yes | yes |
| Patient list and search | yes | yes | — |
| Open any patient record | yes | yes | own only |
| Register a new patient | — | yes | — |
| Create a patient's portal account | yes | — | — |
| Add or delete clinical records | yes | read only | read only |
| Schedule and manage appointments | yes | — | read only |

"Clinical records" means all five types: diagnoses, lab values, doctor's notes,
medical history and allergies. The midwife is the only role that writes them;
this comes from the study's Figure 2.

Patient registration is health-worker-only, enforced by
`PatientPolicy::register()` as well as by the route group. To let midwives
register patients too, see the note on that method.

### How a patient gets a login

There is no public sign-up — `/register` returns 404, and that is deliberate.
Accounts are issued by staff, and the study's Level 1 DFD splits the work:

> "The Health Worker module is responsible for patient registration ... The
> midwife can also create patient accounts, which are stored in the Account
> Database."

So the two steps have different owners:

1. **A health worker registers the patient** — demographics only. The record is
   created with `user_id = null`. Most walk-in patients stop here and never
   need a login.
2. **A midwife grants portal access** — from the *Portal account* panel on the
   patient's record screen. They enter an email address; the account is created
   with role Patient and linked to the patient record.

**No member of staff ever knows a patient's password.** The account is created
with an unusable random string, and the patient sets a real password through
the "Forgot password" link. `PortalAccountTest` asserts that none of the
obvious guesses open the account.

The rule lives in `PatientPolicy::createAccount()`. A health worker sees "Only
the midwife can create one" instead of the button, and calling the Livewire
method directly returns 403.

### Comparing the three views

Rather than logging in and out repeatedly, open one account in a normal window
and the other two in private/incognito windows — sessions do not collide, so
all three sit side by side.

The access rules are worth seeing directly. Signed in as the patient, edit the
address bar to `/patients`: the answer is a 403, not a redirect to somewhere
friendlier. The same holds for a health worker visiting `/midwife/dashboard`.
Both are covered by `tests/Feature/RoleAccessTest.php`.

Emails (password resets, verification links) are written to
`storage/logs/laravel.log` in development rather than sent.

---

## Tests

```bash
php artisan test
```

Tests run against in-memory SQLite, so no database server is needed for them.

---

## Documentation

Start with [DOCS/01-how-it-works.md](DOCS/01-how-it-works.md).

| Document | Covers |
|----------|--------|
| [01-how-it-works.md](DOCS/01-how-it-works.md) | Architecture, folder map, how a request flows |
| [02-adding-a-page.md](DOCS/02-adding-a-page.md) | Adding a new page, step by step |
| [03-adding-a-record-type.md](DOCS/03-adding-a-record-type.md) | Adding a field or a whole new record type |
| [04-editing-common-things.md](DOCS/04-editing-common-things.md) | Styling, navigation, roles, permissions, validation |
| [05-conventions.md](DOCS/05-conventions.md) | House rules and the reasoning behind them |
| [06-troubleshooting.md](DOCS/06-troubleshooting.md) | Common errors and what they mean |

---

## Deployment notes

The study specifies AWS. A workable mapping:

- **RDS for PostgreSQL** — the database
- **Elastic Beanstalk or EC2** — the application
- **ACM + Application Load Balancer** — HTTPS
- **CloudWatch** — logs

Before going live: set `APP_DEBUG=false`, set a real `APP_KEY`, configure a
genuine `MAIL_MAILER`, and change every seeded password.
