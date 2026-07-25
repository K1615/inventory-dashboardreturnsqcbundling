# Authentication Turnover Report

Date: 2026-07-25
Specification: `.codex/LARAVEL-ERP-AUTHENTICATION-MASTER-SPEC.md`
Disposition: **NOT READY FOR DEVELOPER TURNOVER**

## 1. Executive status

All implementation phases and application-level stability checks were
executed. Authentication, route protection, CSRF/session behavior, actor
integrity, ERP regressions, clean installation, build, migration safety, and
HTTP smoke checks pass.

The strict master-specification gate cannot be declared ready because:

1. The supplied project has no `.git` metadata. An authoritative `git status`,
   diff, ignored-secret check, and commit review are therefore impossible.
2. The configured repository-wide Pint check exits 1 for 22 legacy files that
   are outside the authentication change. Auth-touched PHP files pass Pint.

These are turnover-governance blockers, not observed authentication runtime
failures. They must be resolved before changing this disposition.

## 2. Architecture and security model

- Laravel 13.19 uses its built-in `web` session guard and Eloquent `users`
  provider.
- Sessions use the database driver and existing `sessions` table.
- `GET /login` and `POST /login` are guest-only.
- `POST /logout` is authenticated and CSRF-protected; no GET logout exists.
- Every ERP page, JSON read, and mutation is inside one `auth` route group.
- Login regenerates the session; logout invalidates it and regenerates CSRF.
- Credential errors are generic. A normalized email/IP limiter allows five
  attempts before temporary throttling.
- Same-origin fetch calls send session credentials and JSON acceptance, attach
  CSRF when appropriate, and route expired sessions back to login.
- Browser-supplied actor labels are ignored. Human audit fields derive from
  `$request->user()->name`; `Auto-Reorder System` is the sole intentional
  system principal.

## 3. ERP behavior verification matrix

| Area | Guest | Authenticated | Mutation/identity evidence |
|---|---|---|---|
| Dashboard, returns, bundling | Redirect to login | HTTP 200 | Inspection, RMA, return, bundle, PO, and limits tests pass |
| Inventory items/requests | Redirect/401 JSON | HTTP 200 | Requestor and reviewer come from session user |
| Stock movement | Redirect/401 JSON | HTTP 200 | Create/status workflows pass; actor spoof rejected |
| Warehouse transfer | Redirect/401 JSON | HTTP 200 | Single, batch, approval workflows pass; session requester |
| Alerts and state JSON | Redirect/401 JSON | HTTP 200/JSON | Protected read endpoints and expiry handling pass |
| Logout | No GET route | POST redirects to login | Session invalidation and CSRF tests pass |

The complete 31-route audit is in `docs/ROUTE-SECURITY-MATRIX.md`.

## 4. Concrete verification evidence

Executed with PHP 8.3.32 unless stated otherwise:

| Gate | Result |
|---|---|
| `php artisan optimize:clear` | Pass |
| `php artisan migrate:status` | Pass; all 8 migrations ran |
| `php artisan migrate --pretend` | Pass; nothing pending |
| `php artisan route:list -v --except-vendor` | Pass; 31 routes, all ERP routes use `auth` |
| `php artisan test` | Pass; 66 tests, 232 assertions |
| Pint on all auth-touched PHP files | Pass |
| `npm run build` | Pass; Vite 8.1.5 |
| `composer check-platform-reqs` | Pass |
| `composer validate --strict --no-check-publish` | Pass |
| Prohibited identity/debug/logout pattern search | Pass; 0 hits |
| Full configured `php vendor/bin/pint --test` | **Fail; 22 legacy files** |
| `git status --short` | **Unavailable; not a Git repository** |

No `migrate:fresh`, `db:wipe`, reset, rollback, truncate, or destructive
database command was run against the repository database.

## 5. Clean-install and runtime evidence

An isolated copy under `C:\tmp\erp-auth-clean-install` was used, leaving the
repository database untouched:

1. Composer install from the lock file passed.
2. npm install passed with 0 reported vulnerabilities.
3. Application key generation passed.
4. Fresh SQLite migration created all 8 migrations.
5. Full seeding created 24 items and 1 user.
6. Re-running the initial-user seeder preserved the existing user.
7. Vite production build passed.
8. All 66 tests and 232 assertions passed.
9. A real HTTP session smoke test returned 200 for login and all five main ERP
   modules after authentication, then redirected to `/login` after POST logout.

The active development database was changed only by the requested
non-destructive initial-user seeding. It retains the existing 24 items and
sessions and now has one development account.

## 6. Files added

- Authentication backend:
  `app/Http/Controllers/Auth/AuthenticatedSessionController.php`,
  `app/Http/Requests/Auth/LoginRequest.php`
- Provisioning:
  `config/initial-admin.php`,
  `database/seeders/InitialAdminUserSeeder.php`
- Views:
  `resources/views/auth/login.blade.php`,
  `resources/views/layouts/guest.blade.php`,
  `resources/views/partials/auth-fetch.blade.php`,
  `resources/views/partials/authenticated-user.blade.php`
- Tests:
  `AuthenticationTest.php`, `RouteProtectionTest.php`,
  `SessionSecurityTest.php`, `IdentityIntegrityTest.php`,
  `InitialAdminUserSeederTest.php`, `ErpModuleRegressionTest.php`
- Documentation:
  `CHANGELOG.md`, `docs/AUTHENTICATION.md`,
  `docs/AUTHENTICATION-IMPLEMENTATION-CHECKLIST.md`,
  `docs/AUTHENTICATION-ROLLBACK.md`, `docs/IDENTITY-MAPPING.md`,
  `docs/ROUTE-SECURITY-MATRIX.md`, `docs/DEVELOPER-HANDOFF.md`,
  `docs/adr/001-session-authentication.md`, and this report

## 7. Files modified

- Environment/setup: `.env.example`, `README.md`, `bootstrap/app.php`,
  `routes/web.php`
- Seeder registration: `database/seeders/DatabaseSeeder.php`
- ERP backend:
  `InventoryController.php`, `InventorySubmoduleController.php`,
  `StockMovementController.php`, `WarehouseLayoutController.php`
- Active Blade UI:
  `layouts/app.blade.php`, `layouts/inventoryItems.blade.php`,
  `layouts/stockMovement.blade.php`, `layouts/warehouse.blade.php`,
  `inventory.blade.php`, `stock_movements.blade.php`,
  `warehouse_layout.blade.php`, `inventory/alerts-page.blade.php`,
  `inventory/submodule.blade.php`
- Baseline expectation: `tests/Feature/ExampleTest.php`

No migration was added or modified for authentication.

## 8. Repository-wide formatting debt

The full Pint gate reports these untouched legacy files:

- `app/Console/Commands/CheckStockLevels.php`
- Models: `ApprovalRequestItem.php`, `AuditMovementLog.php`,
  `BundleRequest.php`, `InventoryRequest.php`, `Item.php`,
  `QcInspection.php`, `ReturnsAuditLog.php`, `RmaRequest.php`,
  `ShipmentHandoff.php`, `StockAlert.php`, `StockMovement.php`,
  `StockMovementRequest.php`, `SystemLog.php`
- `app/Services/AutoReorderService.php`
- Migrations:
  `2026_07_12_061820_create_requests_table.php`,
  `2026_07_21_164005_create_items_table.php`,
  `2026_07_21_164523_create_erp_dependent_tables.php`,
  `2026_07_21_164654_create_alerts_and_handoffs_tables.php`
- Seeders: `InventoryStockMovementSeeder.php`,
  `InventorySystemSeeder.php`, `ItemSeeder.php`

Run Pint on these in a separate formatting-only review, confirm the diff, then
rerun the complete gate. Do not mix semantic behavior changes into that pass.

## 9. Initial account and secret handling

The repository contains placeholders only. Production must provide
`INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_EMAIL`, and
`INITIAL_ADMIN_PASSWORD` through protected environment configuration, run the
dedicated seeder once, remove the password value, and rotate it at first use.

For local verification only, the active SQLite database received:

- Email: `admin@example.test`
- Password: `LocalERP!2026Auth`

This credential is development-only. Change it before sharing or running this
copy in any persistent environment. The idempotent seeder does not overwrite
an existing password.

## 10. Operations and setup

Required runtime: PHP 8.3+, Composer 2, Node satisfying Vite's supported range,
and writable SQLite/session paths.

Follow `README.md` for installation and `docs/AUTHENTICATION.md` for session,
cookie, provisioning, troubleshooting, and deployment configuration. For
production, use HTTPS and set `SESSION_SECURE_COOKIE=true`.

Run before release:

```text
php artisan optimize:clear
php artisan migrate:status
php artisan route:list -v
php artisan test
php vendor/bin/pint --test
npm run build
```

## 11. Rollback and recovery

`docs/AUTHENTICATION-ROLLBACK.md` documents file scope, SQLite backup,
application rollback, data consequences, and restore verification. Prefer a
reviewed version-control revert. Do not remove auth middleware merely to work
around an outage, and do not roll back the original users/sessions migration
on a populated database.

## 12. Next developer starting point

1. Obtain the authoritative Git checkout containing this source.
2. Compare this file inventory to `git status` and a full reviewed diff.
3. Verify `.env` and database files are ignored and no secrets are staged.
4. Apply the 22-file formatting-only cleanup and rerun global Pint.
5. Rerun the exact gates in section 10 under PHP 8.3+.
6. Repeat the HTTP smoke test in the target deployment environment.
7. Change the development account password or provision a new protected
   initial account.
8. Change this disposition only after both blockers and every gate pass.

## 13. Deferred RBAC

All authenticated users currently have the same ERP access. The next security
increment should add stable roles and permissions, server-side policies/gates,
maker-checker separation, account active state and administration, and
nullable actor user foreign keys with reviewed historical backfill. UI hiding
must never substitute for server authorization.
