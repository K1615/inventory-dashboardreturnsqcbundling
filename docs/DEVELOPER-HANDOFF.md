# Developer Handoff

## Project snapshot

- Framework: Laravel 13.19.0
- PHP: 8.3 or newer
- Database: SQLite
- Sessions: database-backed
- Frontend: Blade, Tailwind CSS, vanilla JavaScript `fetch()`
- Authentication: built-in Laravel `web` session guard and Eloquent users

Read these first:

1. `docs/AUTHENTICATION.md`
2. `docs/ROUTE-SECURITY-MATRIX.md`
3. `docs/IDENTITY-MAPPING.md`
4. `routes/web.php`
5. `app/Http/Requests/Auth/LoginRequest.php`
6. `tests/Feature/AuthenticationTest.php`
7. `tests/Feature/IdentityIntegrityTest.php`

## Authentication files

Backend and configuration:

- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Requests/Auth/LoginRequest.php`
- `bootstrap/app.php`
- `config/auth.php`
- `config/session.php`
- `config/initial-admin.php`
- `routes/web.php`

User provisioning and database:

- `app/Models/User.php`
- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/seeders/InitialAdminUserSeeder.php`
- `database/seeders/DatabaseSeeder.php`
- `.env.example`

Views and browser behavior:

- `resources/views/auth/login.blade.php`
- `resources/views/layouts/guest.blade.php`
- `resources/views/partials/authenticated-user.blade.php`
- `resources/views/partials/auth-fetch.blade.php`
- The four active layouts and five ERP views that include those partials or
  display authenticated actor identity

Tests:

- `tests/Feature/AuthenticationTest.php`
- `tests/Feature/RouteProtectionTest.php`
- `tests/Feature/SessionSecurityTest.php`
- `tests/Feature/IdentityIntegrityTest.php`
- `tests/Feature/InitialAdminUserSeederTest.php`
- `tests/Feature/ErpModuleRegressionTest.php`

Documentation:

- `README.md`
- `CHANGELOG.md`
- `docs/AUTHENTICATION.md`
- `docs/AUTHENTICATION-IMPLEMENTATION-CHECKLIST.md`
- `docs/AUTHENTICATION-ROLLBACK.md`
- `docs/IDENTITY-MAPPING.md`
- `docs/ROUTE-SECURITY-MATRIX.md`
- `docs/adr/001-session-authentication.md`
- This handoff

## Change impact

| Area | Why and dependency | Risk if changed incorrectly | Future RBAC point |
|---|---|---|---|
| Auth controller/request | Login, logout, validation, throttle, session lifecycle | Fixation, enumeration, logout/session failure | Add account-active checks and login monitoring |
| `routes/web.php` | Single protection boundary for every ERP route | One misplaced route can expose data/mutations | Add policy/permission middleware inside auth |
| `bootstrap/app.php` | JSON errors for all fetch paths | Expired sessions become redirects/HTML parse errors | Keep normal `expectsJson()` behavior |
| Auth fetch partial | Same-origin cookies, CSRF, 401/419 redirect | Mutations can lose CSRF or UI can mis-handle expiry | No authorization logic belongs here |
| Authenticated-user partial | Escaped identity and POST logout | GET logout or missing CSRF weakens session safety | Role labels may be displayed only after server RBAC |
| Four controllers | Replace spoofable actor values | Audit impersonation if request actor fields return | Switch name strings to reviewed user FKs |
| Initial-user seeder | Controlled first account | Duplicate/overwritten account or exposed secret | Replace with account administration later |
| Tests | Stability and spoofing gates | Regressions can ship undetected | Add policy and maker-checker cases |

No inventory quantity formula, status name, alert threshold, PO rule, returns
rule, bundling recipe rule, transfer calculation, or approval-only CRUD rule was
intentionally redesigned. Validation and actor sources were the only controller
behavior changes. Regression tests exercise each major workflow.

## Key decisions

- Built-in session authentication matches Blade and same-origin fetch with the
  least architectural disruption.
- Public registration is inappropriate for controlled ERP accounts.
- RBAC was deferred; every authenticated user currently has every module.
- Existing string actor columns were retained to avoid destructive historical
  data migration.
- Browser actor inputs were removed/ignored because hidden or selected identity
  is not trustworthy.
- API-like browser endpoints remain under web middleware so sessions and CSRF
  protect them.

## Extension points

- Roles/permissions: introduce explicit models or a reviewed package, seed
  stable keys, then authorize routes and controller actions server-side.
- Policies/gates: bind them to Item, request, movement, transfer, bundle, and PO
  actions rather than navigation visibility.
- Maker-checker: compare immutable creator user IDs against resolver user IDs;
  names are insufficient.
- Password reset: add verified delivery, signed/expiring tokens, routes, and
  enumeration-safe responses.
- User administration: add active state, controlled creation, password rotation,
  session revocation, and auditable changes.
- Actor FKs: follow the dual-write/backfill sequence in
  `docs/IDENTITY-MAPPING.md`.
- Module access: add middleware/policies and tests before hiding navigation.
- Login monitoring: listen to Laravel login, failed, lockout, and logout events
  without recording credentials, tokens, cookies, or session IDs.

## Known limitations

- Turnover blocker: this supplied copy has no Git metadata, so an authoritative
  status/diff/ignored-secret review cannot be completed.
- Turnover blocker: repository-wide Pint reports 22 legacy files outside the
  authentication change; the auth-scoped Pint gate passes.
- Medium: every authenticated account has all ERP module/action access.
- Medium: actor identity is stored as a mutable name snapshot, not a user FK.
- Medium: no explicit disabled-account flag or user-administration UI.
- Low: no password-reset UI, email verification, 2FA, or login activity UI.
- Low: protection against repeated guessing is temporary rate limiting, not a
  permanent lockout.
- Existing reliability debt: several pre-auth multi-record business mutations
  are not consistently wrapped in database transactions.

## Operations

Create the initial user:

```bash
php artisan db:seed --class=Database\\Seeders\\InitialAdminUserSeeder
```

Reset a password: use Tinker, identify the user by normalized email, assign a
new securely supplied password, save, and revoke that user's sessions. Do not
put the password in source code or shared command history.

Disable a user: until an active-state field exists, rotate the password to an
unavailable random value and remove the user's session rows. Deletion is not
preferred because audit/history requirements must be reviewed first.

Inspect and test:

```bash
php artisan optimize:clear
php artisan migrate:status
php artisan route:list -v
php artisan test
php vendor/bin/pint --test
npm run build
```

The repository-wide Pint command is intentionally listed as a gate, but it
will remain non-zero until the legacy files named in
`docs/AUTHENTICATION-TURNOVER-REPORT.md` are formatted in a separately reviewed
change.

Troubleshoot login in this order: environment/cache, users row, password hash,
sessions table, SQLite permissions, session cookie/HTTPS configuration, route
middleware, and application logs.

## Do-not-break rules

- Do not reintroduce client-selected actor identity.
- Do not create a GET logout route.
- Do not remove CSRF protection.
- Do not move session routes to stateless API middleware without redesign.
- Do not expose credentials in seeders, docs, logs, screenshots, or commits.
- Do not implement roles only in the UI.
- Do not trust hidden inputs as identity or authorization.
- Do not use route visibility as the only access-control mechanism.
- Do not replace actor strings with user IDs without a safe data migration.
- Keep the 66 authentication/regression tests and the route audit passing.

## Recommended commit breakdown

No commits were created. A clean review can split the work into:

1. Authentication backend, configuration, seeder, and routes
2. Login interface, shared session handling, and user/logout UI
3. Authenticated identity integration and controller validation
4. Authentication, spoofing, session, and ERP regression tests
5. Documentation and turnover materials

## Deferred RBAC sequence

1. Define stable Role model and seeds.
2. Define Permission model and stable action keys.
3. Add user-role assignment with administrative controls.
4. Implement policies and gates.
5. Enforce module access server-side.
6. Enforce action-level permissions.
7. Implement maker-checker separation using user IDs.
8. Add nullable user foreign keys to actor-bearing tables.
9. Backfill reviewed historical audit mappings.
10. Add denial, escalation, maker-checker, and migration tests.
