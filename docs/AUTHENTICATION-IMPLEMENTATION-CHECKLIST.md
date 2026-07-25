# Authentication Implementation Checklist

This checklist tracks execution of `.codex/LARAVEL-ERP-AUTHENTICATION-MASTER-SPEC.md`.
The repository and command evidence are authoritative. A phase is marked complete
only after its applicable implementation and verification work has finished.

- [x] Phase 1 — Current-state verification
- [x] Phase 2 — Authentication design
- [x] Phase 3 — Build the login backend
- [x] Phase 4 — Build the login interface
- [x] Phase 5 — Create a secure initial user
- [x] Phase 6 — Protect the complete ERP
- [x] Phase 7 — Handle unauthenticated AJAX requests
- [x] Phase 8 — Replace fake user identity
- [x] Phase 9 — Shared authenticated user interface
- [x] Phase 10 — Protect controller actions
- [x] Phase 11 — Authentication tests
- [x] Phase 12 — Execute verification
- [x] Phase 13 — Fix failures
- [x] Phase 14 — Final audit
- [x] Phase 15 — Production stability and developer turnover gate executed;
  strict status is **NOT READY FOR DEVELOPER TURNOVER**

## Phase 1 evidence

- Laravel framework locked at 13.19.0; application requires PHP 8.3.
- Default `web` guard uses the Eloquent `users` provider.
- Database-backed sessions and the default `users` and `sessions` tables exist.
- All ERP pages, data endpoints, and mutations were public at baseline.
- The active SQLite database had 24 items, 3 sessions, and no users.
- All eight existing migrations were reported as ran.
- Baseline frontend build passed.
- Baseline tests could not start under the available PHP 8.2.12 runtime because
  PHPUnit 12 contains PHP 8.3 syntax. Runtime remediation is tracked for the
  verification gate.

## Phase 2 decisions

- Use Laravel's default `web` session guard and Eloquent user provider.
- Use named `GET /login`, `POST /login`, and `POST /logout` routes.
- Apply `guest` to login routes and `auth` to the complete ERP route group.
- Use a dedicated authentication controller and focused login form request.
- Use Laravel's native rate limiter, generic credential errors, session
  regeneration, intended redirects, and POST/CSRF logout.
- Use a dedicated guest layout and a shared authenticated-user/logout partial.
- Keep browser-session JSON endpoints under web middleware.
- Add one shared same-origin fetch wrapper for JSON acceptance and 401/419
  session-expiry handling.
- Keep existing actor columns as strings and populate them from the
  authenticated user's name; defer user foreign keys and RBAC.

## Phase 15 evidence and disposition

- PHP 8.3.32 verification passed: 66 tests, 232 assertions.
- The scoped formatter gate passed for every PHP file changed by this
  implementation.
- The Vite production build passed.
- Composer platform requirements and strict manifest validation passed.
- All eight migrations remain applied and `migrate --pretend` reported no
  pending work. No destructive database command was used.
- An isolated clean install passed dependency installation, fresh migration,
  full seeding, idempotent initial-user seeding, build, all 66 tests, and an
  HTTP login/module/logout smoke test.
- The security search found no live fake-user selector, client-authoritative
  actor field, GET logout, debug `console.log`, or prohibited hard-coded
  acting identity.
- The configured repository-wide Pint gate still reports 22 pre-existing,
  authentication-unrelated PHP files. They were not rewritten as part of this
  security change.
- The supplied source tree has no Git metadata, so `git status`, diff review,
  ignored-secret confirmation, and commit-level turnover cannot be performed.

The last two items are mandatory governance gates in the master specification.
The final disposition and recovery path are recorded in
`docs/AUTHENTICATION-TURNOVER-REPORT.md`.
