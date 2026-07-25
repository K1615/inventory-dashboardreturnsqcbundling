# Identity Mapping

This inventory distinguishes authenticated actors from business-party data and
legacy fixtures. Line references reflect the authentication turnover release.

| Field / occurrence | Module and storage | Browser supplied? | Backend authority | Status and future migration |
|---|---|---:|---|---|
| `sessions.user_id` — `database/migrations/0001_01_01_000000_create_users_table.php:32` | Framework session → `users.id` | No | Laravel session guard | Native user foreign key; retain |
| `requests.requestor` — `database/migrations/2026_07_12_061820_create_requests_table.php:13`; `InventoryController.php:34` | Inventory add/edit/delete request creator | Ignored if present | `$request->user()->name` | Spoof protected; add nullable `requestor_user_id` later |
| `requests.reviewer` — same migration line 14; `InventoryController.php:100` | Inventory approval/void reviewer | Ignored if present | `$request->user()->name` | Spoof protected; add `reviewer_user_id` later |
| `stock_movements.user` — `2026_07_21_164523_create_erp_dependent_tables.php:75`; `StockMovementController.php:80` | Stock movement creator | Ignored if present | `$request->user()->name` | Spoof protected; add `created_by_user_id` later |
| `stock_movement_requests.requester` — `2026_07_21_231018_create_stock_movement_requests_table.php:14`; `WarehouseLayoutController.php:93,130` | Single and batch transfer requester | Ignored if present | `$request->user()->name` | Spoof protected; add requester user FK later |
| `qc_inspections.op` — dependent-tables migration line 21; `InventorySubmoduleController.php:307` | QC inspection submitter | Ignored if present | `$request->user()->name` | Spoof protected; add operator user FK later |
| `rma_requests.op` — dependent-tables migration line 33; controller line 328 | RMA submitter | Ignored if present | `$request->user()->name` | Vendor remains separate business data |
| `returns_audit_logs.op` — dependent-tables migration line 45; controller line 386 | User resolving QC/RMA | Ignored if present | `$request->user()->name` | Spoof protected; historical label is `op` |
| `bundle_requests.requester` — dependent-tables migration line 56; controller line 405 | Bundle request creator | Ignored if present | `$request->user()->name` | Spoof protected; add requester user FK later |
| `bundle_requests.approver` — dependent-tables migration line 61; controller line 423 | Bundle approver/voider | Ignored if present | `$request->user()->name` | Spoof protected; add approver user FK later |
| `approval_requests.requester` — `2026_07_21_164654_create_alerts_and_handoffs_tables.php:32`; controller line 152 | Manual PO creator | Ignored if present | `$request->user()->name` | Human actor for manual orders |
| Same `approval_requests.requester` — `AutoReorderService.php:65` | Automated draft PO | No | Literal `Auto-Reorder System` | Intentional system principal; model separately in a future audit design |
| `shipment_handoffs.created_by` — alerts-and-handoffs migration line 65; controller line 274 | User receiving an ordered PO | Ignored if present | `$request->user()->name` | Spoof protected; add receiver user FK later |
| `system_logs.user` — dependent-tables migration line 13; controller lines 102,117,170,188,206,218,244,251,291,389 | Human business-action audit label | No actor payload is used | `$request->user()->name` | Immutable name snapshot; add nullable user FK without deleting snapshot |
| Same `system_logs.user` — `AutoReorderService.php:81` | Automated reorder audit | No | `Auto-Reorder System` | Intentional system actor |
| `stock_alerts.acknowledged_by` — alerts-and-handoffs migration line 21 | Future alert acknowledgement | Currently unused | No active route writes it | Deferred; must use authenticated identity when implemented |
| Authenticated navigation — `partials/authenticated-user.blade.php:3-4` | User name/email display | No | `auth()->user()` with Blade escaping | Read-only; not an authorization control |
| `InventorySystemSeeder.php:79-106` | Optional historical sample audit rows | Seeder fixture | Static historical labels | Not called by `DatabaseSeeder`; not a live account or actor selector |
| `InventoryStockMovementSeeder.php:35-45` | Optional historical movement rows | Seeder fixture | Static `Admin 1–3` labels | Not called by `DatabaseSeeder`; retain only as legacy demo history |

## Legitimate business-person and organization fields

These values do not represent the current authenticated actor and remain
browser-editable under their existing validation:

- `approval_requests.supplier`: external supplier.
- `rma_requests.vendor`: target manufacturer/vendor.
- shipment, warehouse, source, and destination names: logistics data.
- product/item names and customer-return source labels: inventory business
  data.

No active request field named `user`, `requester`, `requestor`, `operator`,
`reviewer`, `approver`, `created_by`, or `op` can override the logged-in actor.
`updated_by`, `processed_by`, `resolved_by`, `approved_by`, and `submitted_by`
do not exist in the active application schema or request handling.

## Safe foreign-key migration sequence

1. Add nullable actor user-ID columns without removing name columns.
2. Produce a reviewed mapping of normalized historical names to user IDs.
3. Backfill only unambiguous rows and report exceptions.
4. Write both ID and name snapshot for new activity.
5. Add foreign keys using a deletion strategy that preserves audit history.
6. Update reads and tests to prefer IDs while retaining historical names.
7. Do not remove legacy strings until audit and retention requirements approve
   it.
