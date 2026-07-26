# Laravel ERP Minimal Authentication Specification (Codex)

## Objective
Implement a minimal Laravel session-based login system for the existing ERP.

## Scope

### Implement ONLY
- Login page
- Logout
- Laravel session authentication
- Guest middleware
- Auth middleware
- One demo account
- Redirect guests to `/login`
- Redirect authenticated users to dashboard
- Protect all ERP routes

### Do NOT Implement
- Registration
- RBAC
- Roles
- Permissions
- Policies
- Password reset
- User management
- Profile editing
- Email verification

## Demo Account
Email: employee@gmail.com

Password: admin123

Requirements:
- Seeder using updateOrCreate()
- Hash::make()
- Auth::attempt()

## Phase 1 - Repository Analysis
Inspect:
- routes/web.php
- User model
- auth config
- controllers
- Blade layouts
- fetch() requests
- seeders

Search:
ACTING_USER, Warehouse Manager, Admin 1, userSelector, requester, reviewer, approver, created_by

Create an implementation map then continue automatically.

## Phase 2 - Authentication Design
Use:
- GET /login
- POST /login
- POST /logout
- guest middleware
- auth middleware
- intended redirect

## Phase 3 - Login Backend
Validate email/password.
Use Auth::attempt().
Regenerate session.
Use generic errors.
Use login throttling.

## Phase 4 - Login Page
Responsive Blade page.
Email, Password, Remember Me, CSRF.

## Phase 5 - Demo Seeder
Create one demo user.
Do not delete existing users.

## Phase 6 - Protect ERP
Protect every ERP page and mutation endpoint.

## Phase 7 - AJAX
Ensure fetch() continues working.
Handle expired sessions.

## Phase 8 - Identity
Replace fake identities with authenticated user where appropriate.
Remove fake login dropdowns.

## Phase 9 - Shared Layout
Display authenticated user.
POST logout form.

## Phase 10 - Controllers
Use authenticated user.
Preserve business rules.

## Phase 11 - Tests
Guest redirect.
Login.
Logout.
Protected routes.
Authenticated access.

## Phase 12 - Verification
Run:
- php artisan optimize:clear
- php artisan migrate:status
- php artisan route:list
- php artisan test

Never run migrate:fresh or db:wipe.

## Phase 13 - Fix Failures
Fix smallest responsible area.
Re-run tests.

## Phase 14 - Final Audit
Verify:
- Login works
- Logout works
- ERP routes protected
- Fake identities removed
- No destructive DB commands

## Final Report
Provide:
1. Summary
2. Files changed
3. Demo credentials
4. Route protection summary
5. Test results
6. Remaining limitations

## Working Rules
- Analyze first.
- Repository is source of truth.
- Continue automatically.
- Minimal changes only.
- No RBAC.
