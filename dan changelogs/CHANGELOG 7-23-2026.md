# Changelog — Alerts & Reorders

## 2026-07-23

### Separated into its own page
- Added standalone `/alerts` page — moved out of the tabbed dashboard SPA.
- New route (`inventory.alerts`), new controller method (`alertsPage()`), new view (`alerts-page.blade.php`).
- Updated all sidebar nav links across the app to point to the new page.
- Removed the old in-SPA alerts tab and its wiring from `submodule.blade.php`.

### Removed manual Acknowledge / Resolve
- Active Stock Alerts table is now read-only (Item / Type / Severity / Qty-Threshold only).
- Removed `alertsAcknowledge()` / `alertsResolve()` (JS), their controller methods, and their routes.
- Alert lifecycle (create → auto-resolve) is untouched on the backend.

### Fixed stale alerts
- Root cause: `stock:check-levels` wasn't triggered everywhere `qty` could change.
- Added the missing trigger to: Warehouse Layout transfers, Stock Movement approvals, Inventory ADD/EDIT/DELETE approvals.
- Alerts (including Overstock) now clear themselves immediately when the underlying condition is fixed — no manual click, no stale rows.

### Fixed the nav badge
- Badge now counts real `stock_alerts` rows (`status = active`) instead of a separate live low/out-only calculation.
- Overstock now counts toward the badge (previously excluded).
- Badge always matches the Active Stock Alerts table exactly.
- Red = an Out of Stock alert is active. Amber = Low Stock and/or Overstock only.
- Alerts page updates its own badge instantly from local state after any action, instead of only on page load.

**Database impact:** None — no migrations, seeders, or model changes.
**Features removed:** None — only the manual Acknowledge/Resolve buttons, which were redundant with existing auto-resolve logic.
