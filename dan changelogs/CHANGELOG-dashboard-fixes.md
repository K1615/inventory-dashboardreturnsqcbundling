# Changelog — Dashboard fixes

File changed: `resources/views/inventory/submodule.blade.php`

## 1. Fixed swapped Product Categories / Warehouse Locations charts

**Problem:** The "Product Categories" card and "Warehouse Locations" card had
their `<canvas>` element IDs crossed, so each card was rendering the other
card's data.

**Fix:** Swapped the canvas IDs back to match their card titles.
- "Product Categories" card → `<canvas id="dashCategoryChart">` (was `dashWarehouseChart`)
- "Warehouse Locations" card → `<canvas id="dashWarehouseChart">` (was `dashCategoryChart`)

No JS logic changed — `dashCatChart` and `dashWhChart` were already building
the correct data, they were just being drawn into the wrong card.

## 2. Fixed Critical Alerts widget not reflecting real thresholds

**Problem:** The Dashboard's "Low Stock Items" / "Out of Stock Items" counts
(`updateDashCalculatedGauges()`) were computed with a hardcoded rule
(`qty === 0` → out of stock, `qty <= 5` → low stock), completely independent
of each item's actual `minLimit`/`maxLimit`, so it never matched Alerts & Reorders.

**Fix (corrected):** `updateDashCalculatedGauges()` now runs the exact same
live calculation the Alerts & Reorders summary cards use (`alertsRenderCards()`
in `alerts-reorders.blade.php`) — comparing each item's `qty` against its own
`minLimit` straight from `appState.inventory`. This keeps the two views in
sync automatically.

Note: an earlier version of this fix pulled counts from the `stockAlerts`
DB table instead. That table is only refreshed when the `stock:check-levels`
artisan command runs, so it can sit stale/empty and disagree with the live
Alerts & Reorders numbers — that approach was reverted in favor of the live
calculation above.

## 3. Added an Overstock tile to the Critical Alerts widget

The widget previously only showed Low Stock and Out of Stock counts. Added a
third tile, `#dash-alert-over`, computed the same live way (item's `qty` vs
`maxLimit`), matching the "Overstock" figure already shown on the Alerts &
Reorders page.

## 4. Fixed missing nav notification badge on other submodules

**Problem:** `#nav-alerts-badge` (the little red count next to "Alerts &
Reorders" in the sidebar) only ever got populated inside
`submodule.blade.php`. Inventory Items, Stock Movements, and Warehouse Layout
are separate routes/views — the badge markup existed there too, but nothing
ever filled it in, so it just stayed hidden on those pages.

**Fix:** Added a small dedicated endpoint,
`GET /inventory/api/alerts-summary` (`InventorySubmoduleController::alertsSummary()`),
that computes the same live Low Stock / Out of Stock counts used everywhere
else (qty vs `minLimit`, no dependency on the `stockAlerts` table). Each of
the three other layout files (`layouts/inventoryItems.blade.php`,
`layouts/stockMovement.blade.php`, `layouts/warehouse.blade.php`) now fetches
that endpoint on load and fills in `#nav-alerts-badge` — same red/amber and
show/hide behavior as on the Dashboard.

## 5. Fixed nav badge going stale/missing on Dashboard, Returns & QC, Alerts & Reorders, Product Bundling

**Problem:** `renderNavAlertsBadge()` in `submodule.blade.php` (which drives
these four tabs) read from `appState.stockAlerts` — the same stale DB table
identified in fix #2. Once the other three pages were switched to a live
calculation (fix #4), the mismatch became obvious: those pages showed correct
counts while these four showed none.

**Fix:** `renderNavAlertsBadge()` now uses the same live calculation as
everywhere else (qty vs `minLimit`, from `appState.inventory`), so all seven
pages agree.

File changed: `resources/views/inventory/submodule.blade.php`

