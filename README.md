# HealthTrack

HealthTrack is a Laravel, Blade, Livewire, and Tailwind CSS application for barangay patient information and medical history management.

## Main roles

- Patient: appointments, medical history, allergies, diagnoses, lab values, and doctor notes.
- Midwife: barangay-scoped dashboard, patient management, patient records, and inventory.
- Admin: reserved for setup and maintenance.

## Security rule

Every clinical and inventory table includes `barangay_id`. Midwife access must always be scoped to the authenticated midwife's `barangay_id`.

## Local setup

Install PHP 8.3+, Composer, Node.js, and MySQL. Make sure a local MySQL server is running and matches your `.env` database values. Then run:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
# ensure MySQL is running before this next command
php artisan migrate --seed
composer run dev
```

If your local MySQL server is not running on `127.0.0.1:3306`, update `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` before running migrations.

## Run locally

Start the Laravel development server with:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Then open:

```text
http://127.0.0.1:8000
```

Seeded users:

```text
admin@healthtrack.test / password
midwife@healthtrack.test / password
delacruz.juan@healthtrack.test / password (patient)
```

Additional patients are seeded with emails in the format `lastname.firstname@healthtrack.test` (spaces removed).

## AWS notes

Use RDS for MySQL, S3 for private medical uploads, SQS for queues, CloudWatch for logs, and ACM-managed HTTPS in front of the application.
