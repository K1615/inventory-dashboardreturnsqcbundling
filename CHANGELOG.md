# Changelog

## 2026-07-25 — Session authentication and developer turnover

### Added

- Guest-only login page and POST login flow
- POST/CSRF logout with session invalidation
- Remember-me support and native login throttling
- Environment-driven, repeatable initial-user seeder
- Shared authenticated user/logout UI and AJAX expiry handling
- Authentication, route-security, identity-spoofing, CSRF, seeder, and ERP
  regression tests
- Authentication architecture, identity map, rollback plan, ADR, and developer
  handoff documentation

### Changed

- All ERP pages, data endpoints, and mutations now require `auth`
- All human actor fields now use the authenticated user's name
- AJAX requests negotiate JSON and redirect safely after 401/419
- Stock/warehouse mutation errors no longer expose raw exception messages
- Project setup instructions now use non-destructive migrations

### Security

- Session ID regeneration after login
- Generic invalid-credential errors
- Five-attempt email/IP login throttle
- Browser identity impersonation removed
- Fake account selectors and fake logout controls removed

### Configuration

Added placeholder-only `INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_EMAIL`, and
`INITIAL_ADMIN_PASSWORD` keys. Database sessions remain the default.

### Migrations

No new migration. Existing `users` and `sessions` tables are reused; ERP actor
columns remain strings.

### Upgrade

1. Back up the configured database.
2. Install dependencies and update `.env.example`-derived configuration.
3. Run `php artisan optimize:clear` and non-destructive `php artisan migrate`.
4. Set initial-user variables and run the dedicated seeder if no user exists.
5. Run tests, route audit, Pint, and the frontend build.

### Known limitations

- No RBAC or maker-checker separation
- No registration, password-reset UI, account UI, email verification, or 2FA
- Legacy actor names are strings rather than user foreign keys
- Login throttling is not a permanent account lockout
