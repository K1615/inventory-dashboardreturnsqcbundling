# Authentication Architecture and Operations

## System overview

The ERP uses Laravel's built-in, stateful session authentication:

- Guard: `web`
- Guard driver: `session`
- User provider: Eloquent `App\Models\User`
- Session driver: `database`
- Login: `GET /login` and `POST /login`, both under `guest`
- Logout: `POST /logout`, under `auth` and web CSRF middleware
- Protected routes: one `auth` group containing every ERP page, data endpoint,
  and mutation endpoint
- Guest page behavior: redirect to `/login`
- Guest JSON behavior: HTTP 401 with JSON
- Successful login: redirect to the originally intended URL or `/`
- Remember me: Laravel's standard remember-token cookie flow

Public registration, password reset, email verification, two-factor
authentication, account administration, roles, and permissions are outside the
implemented scope.

## Login flow

1. The user opens `/login`.
2. `guest` middleware permits access and redirects authenticated users to `/`.
3. `LoginRequest` trims and lowercases the email and validates email, password,
   and optional remember-me input.
4. The native rate limiter allows five failed attempts per normalized email and
   IP before returning a throttling validation error.
5. `Auth::attempt()` checks the Eloquent user provider. Failures always use
   Laravel's generic credential message.
6. The session ID is regenerated after success.
7. `redirect()->intended()` returns the user to the protected URL originally
   requested, or the dashboard.
8. `auth` middleware protects every subsequent ERP request.

Passwords are never placed in old input, application logs, system audit logs,
or documentation.

## Logout flow

1. The user submits the sidebar's POST logout form.
2. Web middleware validates its CSRF token.
3. Laravel logs the user out of the `web` guard.
4. The session is invalidated.
5. A new CSRF token is generated.
6. The browser is redirected to `/login`.

There is no GET logout route.

## AJAX authentication behavior

All API-like browser routes remain in `routes/web.php`. They require the same
session cookie and CSRF protection as the Blade pages; they are not stateless
APIs.

Each active authenticated layout includes `partials/auth-fetch.blade.php`. The
wrapper:

- preserves same-origin credentials;
- adds `Accept: application/json`;
- supplies `X-CSRF-TOKEN` to same-origin non-read requests when the caller did
  not already provide it;
- preserves successful response handling;
- redirects to `/login` on HTTP 401, HTTP 419, or a redirected login response.

Blade layouts expose the token through a `csrf-token` meta element, while
existing mutations that already set `X-CSRF-TOKEN` continue to do so. This
prevents expired sessions or invalid CSRF state from becoming an HTML-to-JSON
parse error in existing JavaScript.

## Authenticated identity

The source of human actor identity is always `$request->user()->name`.
Controllers use it for:

- inventory requestors and reviewers;
- stock-movement creators;
- transfer requesters;
- returns/QC and RMA operators and resolvers;
- bundle requesters and approvers;
- manual purchase-order requesters and decision logs;
- shipment receivers;
- system log entries for authenticated actions.

`Auto-Reorder System` is an intentional non-human actor used only by background
stock evaluation. Supplier, vendor, manufacturer, and warehouse fields are
business data and are not replaced with the logged-in user.

Existing ERP tables store actor names as strings. They were retained to avoid a
destructive cross-module migration. A future migration should add nullable user
foreign keys, backfill by a reviewed and case-normalized mapping, preserve the
legacy name snapshot, deploy dual writes, verify historical coverage, and only
then make foreign keys authoritative.

See `docs/IDENTITY-MAPPING.md` for the complete field inventory.

## Security decisions

- Manual session authentication fits the existing server-rendered Blade and
  same-origin fetch architecture without a frontend rewrite.
- Public registration was excluded because ERP accounts require controlled
  provisioning.
- Logout is POST to prevent cross-site and crawler-triggered logout.
- Session regeneration blocks session fixation.
- Generic credential errors prevent account enumeration.
- Login throttling limits repeated guessing without implementing a permanent
  account lockout.
- Browser-submitted actor values are ignored to prevent audit impersonation.
- Full RBAC was deferred so this change protects identity without inventing
  unapproved business authorization rules.

## Initial user operations

Set the three `INITIAL_ADMIN_*` variables and run:

```bash
php artisan db:seed --class=Database\\Seeders\\InitialAdminUserSeeder
```

The seeder validates strength, hashes the password, normalizes the email, and
uses `firstOrCreate`. Re-running it does not create a duplicate or replace an
existing password.

An alternative for an authorized operator is Tinker:

```bash
php artisan tinker
```

Then create a user with a securely supplied password using
`App\Models\User::create(...)`. Do not paste a production password into shell
history, source code, chat, tickets, or documentation.

To reset a password, load the intended user in Tinker, assign a newly supplied
plain value to `password` (the model's hashed cast hashes it), save, and delete
that user's rows from `sessions` so existing sessions end.

There is no disabled-account column. To disable access operationally, rotate
the password to an unavailable random value and remove that user's session
rows. Add a reviewed `is_active` field and authentication check in a future
account-administration phase.

## Session maintenance

Inspect protected routes:

```bash
php artisan route:list -v
```

Clear configuration and view caches after environment changes:

```bash
php artisan optimize:clear
```

Expired database sessions may be removed with a targeted query based on
`last_activity` and `SESSION_LIFETIME`; take a database backup first. Never
truncate the sessions table during an incident unless signing out every user is
the intended response.

## Error handling and recovery

- Invalid credentials: generic validation error; verify the email is normalized
  and reset the password through an authorized process.
- Throttled login: wait for the displayed interval or clear only the confirmed
  rate-limit key during an approved support action.
- Missing sessions table: run `php artisan migrate:status`, then non-destructive
  `php artisan migrate`.
- Database unavailable: verify the configured SQLite path and permissions;
  preserve the file and review `storage/logs/laravel.log`.
- Deleted authenticated user: the provider cannot restore the user and the
  protected request returns to login.
- Invalid CSRF: the shared fetch wrapper redirects to login; refresh the login
  page to obtain a new session/token.
- Duplicate email: the unique users-table constraint and initial-user seeder
  preserve the existing account.
- Corrupted or removed session: the next protected request is unauthenticated;
  log in again.

Production must use `APP_DEBUG=false`, HTTPS, and
`SESSION_SECURE_COOKIE=true`. Never log passwords, remember tokens, session IDs,
CSRF tokens, full cookies, or application keys.

## Environment example audit

`.env.example` contains placeholder-only initial-user keys and explicitly
documents the database session driver, lifetime, encryption, cookie path/domain,
secure, HTTP-only, SameSite, partitioned, and table settings. Names match
`config/initial-admin.php` and `config/session.php`. Guard/provider variables are
not required because the application intentionally uses Laravel's `web` and
Eloquent defaults. Other omitted database/session environment variables are
optional driver overrides with configuration defaults; none is required for
the documented SQLite setup. The real `.env` was not copied into documentation
or the isolated installation.

## Audit logging

Business mutations continue to write `SystemLog` and module audit records using
the authenticated actor where those schemas support it. The system does not
add login-success or logout business audit rows. Login failures are handled by
Laravel validation and rate limiting and do not log credential values.

Some multi-record ERP operations predate authentication and do not consistently
use database transactions. Authentication does not worsen this behavior, but a
future reliability phase should review purchase-order receiving, returns,
bundling decrements, warehouse transfers, and inventory approval mutations for
atomic transactions.
