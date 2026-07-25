# ERP Inventory Management System

Laravel ERP for inventory items, stock movements, warehouse transfers, returns
and QC, product bundling, stock alerts, and purchase-order reordering.

## Requirements

- PHP 8.3 or newer
- Composer 2
- Node.js `^20.19.0` or `>=22.12.0`
- npm
- SQLite (the configured and tested database)
- PHP extensions: Ctype, DOM, Fileinfo, Filter, Hash, Iconv, JSON, LibXML,
  OpenSSL, PDO SQLite, Session, SQLite3, Tokenizer, XML, and XMLWriter

The locked application versions are Laravel 13.19.0 and PHPUnit 12.5.31.

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

On PowerShell, replace the copy command with:

```powershell
Copy-Item .env.example .env
```

If `database/database.sqlite` does not exist, create an empty file before
running migrations:

```powershell
New-Item database/database.sqlite -ItemType File
```

Configure the first account in `.env` before seeding:

```env
INITIAL_ADMIN_NAME=
INITIAL_ADMIN_EMAIL=
INITIAL_ADMIN_PASSWORD=
```

Use a unique password of at least 12 characters containing upper- and
lowercase letters, a number, and a symbol. Then run:

```bash
php artisan db:seed
php artisan serve
```

Open `http://127.0.0.1:8000/login`. Public registration is intentionally not
available. The user menu in every ERP sidebar contains the secure logout form.

For an existing database, seed only the initial account:

```bash
php artisan db:seed --class=Database\\Seeders\\InitialAdminUserSeeder
```

The initial-user seeder never replaces an existing account's name or password.
In production, missing initial-user variables cause the seeder to fail clearly.
After the first account exists, the three variables may be removed from the
runtime environment.

## Database sessions

`SESSION_DRIVER=database` is the supported configuration. Migration
`0001_01_01_000000_create_users_table.php` creates the required `sessions`
table. Removing a session row signs that browser out on its next request.

For SQLite, ensure that the PHP process can read and write
`database/database.sqlite` and the containing `database` directory. If login
appears successful but does not persist:

1. Confirm `php artisan migrate:status` shows the users-table migration as ran.
2. Confirm `SESSION_DRIVER=database`.
3. Confirm the SQLite file is writable.
4. Run `php artisan optimize:clear` after environment changes.
5. Check `storage/logs/laravel.log` without copying cookies or session IDs.

## Verification

```bash
php artisan optimize:clear
php artisan migrate:status
php artisan route:list -v
php artisan test
./vendor/bin/pint --test
npm run build
```

On Windows, run Pint as:

```powershell
php vendor/bin/pint --test
```

Authentication architecture, operations, troubleshooting, and extension
guidance are in [docs/AUTHENTICATION.md](docs/AUTHENTICATION.md) and
[docs/DEVELOPER-HANDOFF.md](docs/DEVELOPER-HANDOFF.md).

## Safety

Never use `migrate:fresh` or `db:wipe` against an existing ERP database. Back
up the SQLite file before migrations, rollback, or operational recovery. Never
commit `.env`, credentials, session identifiers, or production database files.
