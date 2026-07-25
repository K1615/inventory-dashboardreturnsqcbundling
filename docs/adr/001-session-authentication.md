# ADR 001: Laravel Session Authentication

## Status

Accepted — 2026-07-25

## Context

The ERP is a server-rendered Laravel Blade application whose JavaScript uses
same-origin `fetch()` calls under web middleware. It already has Laravel's
default User model, users table, web guard, CSRF middleware, and
database-backed sessions. It needs login, logout, route protection, and trusted
actor identity without registration, RBAC, or a frontend rewrite.

## Decision

Use Laravel's built-in manual session authentication with a dedicated session
controller and login form request. Protect every ERP route with `auth`, keep
login under `guest`, retain API-like browser routes under web middleware, and
derive all human actor names from the authenticated session.

## Alternatives considered

- **Laravel Breeze:** good minimal starter, but would introduce scaffolding and
  asset conventions beyond the small flow this established UI requires.
- **Laravel Jetstream:** includes teams/profile/security features outside scope
  and would be disproportionately invasive.
- **Laravel Fortify:** headless auth primitives are useful for broader auth
  feature sets, but unnecessary for login/logout only.
- **Token authentication:** appropriate for independent API clients, not for
  same-origin Blade pages that already require CSRF-protected browser sessions.

## Consequences

- Authentication is small, conventional, and reviewable.
- Browser operations keep Laravel CSRF and session semantics.
- All authenticated users currently access all modules.
- Actor identity remains stored in legacy name strings.
- Account administration and password reset require future work.

## Security implications

Sessions regenerate after login and invalidate after POST/CSRF logout. Generic
credential errors and native throttling reduce enumeration and guessing.
Client-selected actor identity is rejected. Production requires HTTPS, secure
cookies, debug disabled, controlled account provisioning, and database/session
backups.

## Future migration path

Add account-status controls, roles, permissions, policies, maker-checker rules,
and user foreign-key audit fields incrementally. If external API clients are
introduced, design a separate token-authenticated API boundary rather than
moving existing web routes without redesign.
