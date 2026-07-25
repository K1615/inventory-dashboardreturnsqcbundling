# Authentication Rollback and Recovery

Rollback should be planned, backed up, and tested in an isolated environment.
Never use `migrate:fresh` or `db:wipe`.

## Authentication files added

- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Requests/Auth/LoginRequest.php`
- `config/initial-admin.php`
- `database/seeders/InitialAdminUserSeeder.php`
- `resources/views/auth/login.blade.php`
- `resources/views/layouts/guest.blade.php`
- `resources/views/partials/auth-fetch.blade.php`
- `resources/views/partials/authenticated-user.blade.php`
- Authentication and turnover feature tests
- Documentation under `docs/` and the authentication ADR

## Existing files modified

- `.env.example`, `README.md`, `CHANGELOG.md`
- `bootstrap/app.php`, `routes/web.php`
- `database/seeders/DatabaseSeeder.php`
- Four ERP controllers and active Blade layouts/views
- `tests/Feature/ExampleTest.php`

No authentication migration was added. The existing Laravel users/sessions
migration already provided the required tables. The only new environment keys
are `INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_EMAIL`, and
`INITIAL_ADMIN_PASSWORD`.

## Backup SQLite

Stop write traffic, confirm the configured database path, and copy the file to
a timestamped backup outside the repository. For the default path in
PowerShell:

```powershell
$source = Resolve-Path database/database.sqlite
$backup = Join-Path (Split-Path $source) ("database-auth-backup-{0}.sqlite" -f (Get-Date -Format "yyyyMMdd-HHmmss"))
Copy-Item -LiteralPath $source -Destination $backup
Get-FileHash -LiteralPath $source
Get-FileHash -LiteralPath $backup
```

Do not assume a production path; verify `DB_DATABASE` without publishing
credentials.

## Safe code rollback

Prefer reverting the authentication release through reviewed version-control
changes. A manual rollback must:

1. Put the application in an approved maintenance window.
2. Restore the pre-authentication versions of routes, controllers, and views.
3. Remove only the added authentication files listed above.
4. Remove the initial-user config and environment keys if no longer used.
5. Run `composer dump-autoload` and `php artisan optimize:clear`.
6. Run the pre-rollback test/build suite and inspect `route:list`.

Removing `auth` middleware makes ERP data public again and is not a safe
production operating state. If authentication itself is failing, prefer
repairing configuration, sessions, or the initial account rather than
disabling route protection.

## Data consequences

- Existing ERP inventory, quantities, requests, movements, alerts, purchase
  orders, and audit rows are not changed by code rollback.
- Users and sessions created while authentication was active are
  authentication data. Do not delete them unless retention and rollback plans
  explicitly authorize it.
- Actor string values written after authentication are valid audit snapshots
  and should remain.
- No authentication-only migration exists to roll back.

If a later release adds authentication migrations, roll back only their
specific batch in an isolated rehearsal first. Never roll back the original
users migration on a populated database because its `down()` removes users,
password-reset tokens, and sessions.

## Restore

1. Stop application writes.
2. Preserve the suspect database separately for investigation.
3. Verify the backup checksum and timestamp.
4. Copy the verified backup to the configured SQLite path.
5. Restore correct filesystem ownership and write permissions.
6. Run `php artisan migrate:status` and read-only smoke checks.
7. Bring the application online and verify login, each module, and logout.

The SQLite copy is the recovery boundary; no restore command should recreate
the database schema or discard ERP history.
