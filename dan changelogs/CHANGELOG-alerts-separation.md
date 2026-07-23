# Changelog — Alerts & Reorders: Separated into its own page

**Scope of this change:** Alerts & Reorders only. Returns & QC and Product Bundling
are untouched and remain inside the tabbed dashboard SPA.

**Database impact:** None. No migrations, seeders, or models were touched. All
mutation endpoints (`updateItemLimits`, `toggleAutoReorder`, `acknowledgeAlert`,
`submitPO`, `markReceived`, etc.) are byte-for-byte unchanged — same queries,
same `SystemLog::create()` calls, same `Artisan::call('stock:check-levels')`
triggers.

**Features removed:** None. Every action from the old tab (summary cards,
active alerts table, PO approval pipeline, threshold table, PO modal,
acknowledge/resolve/approve/void/receive/draft actions) still works — it's
the same partial file, just rendered on its own page instead of inside the SPA.

---

## New files

### `resources/views/inventory/alerts-page.blade.php`
- Standalone page, `@extends('layouts.warehouse')`.
- Own sidebar nav (copied from the `warehouse_layout.blade.php` pattern).
  "Alerts & Reorders" is a real link (`route('inventory.alerts')`), highlighted
  active via `$currentRoute === 'inventory.alerts'`.
- Declares its own `csrfToken`, `headers`, and `appState = @json($initialData)`.
- `@include('inventory.alerts-reorders')` — reuses the existing partial unchanged.
- On `DOMContentLoaded`: strips the `hidden` class off `#view-alerts` (the
  partial was originally built assuming it's a toggled SPA tab) and calls
  `alertsRenderAll()`.

---

## Modified files

### `routes/web.php`
- Added: `Route::get('/alerts', [InventorySubmoduleController::class, 'alertsPage'])->name('inventory.alerts');`
  (placed next to the existing `warehouse.layout` route)

### `app/Http/Controllers/InventorySubmoduleController.php`
- Added `alertsPage()` method — returns
  `view('inventory.alerts-page', ['initialData' => $this->getAppData()])`.
- No other methods touched.

### `resources/views/inventory/submodule.blade.php`
- Removed `@include('inventory.alerts-reorders')` (replaced with a comment
  pointing to the new file).
- Sidebar "Alerts & Reorders" link: now a plain
  `href="{{ route('inventory.alerts') }}"`, dropped the
  `onclick="handleJsNav(...)"` SPA intercept.
- Dashboard "Manage Orders →" quick-link button: changed from
  `onclick="handleJsNav(event, 'alerts')"` to
  `onclick="window.location.href='{{ route('inventory.alerts') }}'"`.
- `refreshAllUI()`: removed the `alertsRenderAll()` call.
- `renderNavAlertsBadge()`: rewritten. Previously computed low/out counts
  locally from `appState.inventory`; now fetches
  `/inventory/api/alerts-summary` — the same lightweight endpoint the other
  already-separated pages use — so the Dashboard's badge can't disagree with
  the Alerts page or drift stale between them.

### `resources/views/layouts/inventoryItems.blade.php`
### `resources/views/layouts/stockMovement.blade.php`
### `resources/views/warehouse_layout.blade.php`
- "Alerts & Reorders" nav link in each: `href` changed from
  `route('inventory.dashboard', ['tab' => 'alerts'])` to
  `route('inventory.alerts')`; dropped `onclick="handleJsNav(...)"`;
  active-state check changed from
  `$currentRoute === 'inventory.dashboard' && $currentTab === 'alerts'`
  to `$currentRoute === 'inventory.alerts'`.

---

## Untouched
- `resources/views/inventory/alerts-reorders.blade.php` (the actual UI/logic partial)
- `resources/views/layouts/warehouse.blade.php` (its badge-fetch script already
  hit `alerts.summary`, so it needed no changes)
- Returns & QC tab and Product Bundling tab (still inside the SPA, still share
  `appState` with the Dashboard)
- All database migrations, seeders, and models

---

## Known tradeoff (not a bug, not new)

Alerts & Reorders now has its own independent `appState`, fetched once on
page load. If inventory changes elsewhere (e.g. a return is processed, or a
bundle build consumes stock), this page won't reflect it until the page is
reloaded — same staleness pattern already accepted for Inventory Items,
Stock Movements, and Warehouse Layout. The nav badge is the exception: it
polls `/inventory/api/alerts-summary` independently on every page, so it
stays accurate everywhere even without a full reload.

## Suggested test pass before merging

- Load `/alerts` directly (not via nav click) — confirm data renders.
- From `/alerts`: submit a PO, toggle auto-reorder, edit a min/max limit,
  acknowledge/resolve an alert, approve/void a pipeline order, mark an order
  received.
- Confirm the nav badge count matches on: Dashboard, Inventory Items, Stock
  Movements, Warehouse Layout, and the new Alerts page.
- Confirm the "Manage Orders →" card on the Dashboard navigates correctly.
