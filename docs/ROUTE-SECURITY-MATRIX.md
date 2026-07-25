# Route Security Matrix

Generated and audited from `php artisan route:list -v` for the authentication
turnover release.

All ERP routes use Laravel's `web` stack plus `auth`. Guest Blade navigation
receives a 302 to `/login`; a request that accepts JSON receives HTTP 401.

| Method | URI | Name | Action | Middleware | Guest behavior | Authenticated behavior |
|---|---|---|---|---|---|---|
| GET | `/` | `inventory.dashboard` | `InventorySubmoduleController@index` | web, auth | Login redirect | Dashboard/Returns/Bundling page |
| GET | `/alerts` | `inventory.alerts` | `InventorySubmoduleController@alertsPage` | web, auth | Login redirect | Alerts page |
| GET | `/items` | `inventory.index` | `InventoryController@index` | web, auth | Login redirect | Inventory page |
| GET | `/movement` | `stock-movements.index` | `StockMovementController@index` | web, auth | Login redirect | Stock movement page |
| GET | `/warehouse` | `warehouse.layout` | `WarehouseLayoutController@index` | web, auth | Login redirect | Warehouse page |
| GET | `/inventory/api/state` | — | `InventorySubmoduleController@getState` | web, auth | 401 JSON when requested by fetch | ERP state JSON |
| GET | `/inventory/api/alerts-summary` | `alerts.summary` | `InventorySubmoduleController@alertsSummary` | web, auth | 401 JSON | Alert summary JSON |
| GET | `/api/stock-movements/data` | `stock-movements.data` | `StockMovementController@getDashboardData` | web, auth | 401 JSON | Movement JSON |
| GET | `/warehouse-layout/data` | `warehouse.layout.data` | `WarehouseLayoutController@getData` | web, auth | 401 JSON | Warehouse JSON |
| POST | `/api/requests` | `api.requests.store` | `InventoryController@storeRequest` | web, auth | 401 JSON | Submit inventory request |
| POST | `/api/requests/{id}/resolve` | `api.requests.resolve` | `InventoryController@resolveRequest` | web, auth | 401 JSON | Approve/void inventory request |
| POST | `/api/stock-movements` | `stock-movements.store` | `StockMovementController@store` | web, auth | 401 JSON | Submit movement |
| PATCH | `/api/stock-movements/{txId}/status` | `stock-movements.update-status` | `StockMovementController@updateStatus` | web, auth | 401 JSON | Approve/void movement |
| POST | `/inventory/api/inspection` | — | `InventorySubmoduleController@submitInspection` | web, auth | 401 JSON | Submit QC inspection |
| POST | `/inventory/api/rma` | — | `InventorySubmoduleController@submitRma` | web, auth | 401 JSON | Submit RMA |
| POST | `/inventory/api/resolve-return` | — | `InventorySubmoduleController@resolveReturn` | web, auth | 401 JSON | Approve/void QC/RMA |
| POST | `/inventory/api/bundle` | — | `InventorySubmoduleController@submitBundle` | web, auth | 401 JSON | Submit bundle |
| POST | `/inventory/api/resolve-bundle` | — | `InventorySubmoduleController@resolveBundle` | web, auth | 401 JSON | Approve/void bundle |
| POST | `/inventory/api/limits/{id}` | — | `InventorySubmoduleController@updateItemLimits` | web, auth | 401 JSON | Change item limits |
| POST | `/inventory/api/auto-reorder/{id}` | — | `InventorySubmoduleController@toggleAutoReorder` | web, auth | 401 JSON | Toggle auto-reorder |
| POST | `/inventory/api/submit-po` | — | `InventorySubmoduleController@submitPO` | web, auth | 401 JSON | Submit manual PO |
| POST | `/inventory/api/draft/{id}/submit` | — | `InventorySubmoduleController@submitDraft` | web, auth | 401 JSON | Submit draft |
| POST | `/inventory/api/draft/{id}/discard` | — | `InventorySubmoduleController@discardDraft` | web, auth | 401 JSON | Discard draft |
| POST | `/inventory/api/pipeline/{id}` | — | `InventorySubmoduleController@processPipeline` | web, auth | 401 JSON | Approve/void PO |
| POST | `/inventory/api/pipeline/{id}/receive` | — | `InventorySubmoduleController@markReceived` | web, auth | 401 JSON | Receive PO |
| POST | `/warehouse-layout/request` | `warehouse.layout.request` | `WarehouseLayoutController@storeRequest` | web, auth | 401 JSON | Submit transfer |
| POST | `/warehouse-layout/batch-request` | `warehouse.layout.batch-request` | `WarehouseLayoutController@storeBatchRequest` | web, auth | 401 JSON | Submit batch transfer |
| POST | `/warehouse-layout/process/{id}` | `warehouse.layout.process` | `WarehouseLayoutController@processApproval` | web, auth | 401 JSON | Approve/void transfer |
| GET | `/login` | `login` | `AuthenticatedSessionController@create` | web, guest | Login page | Redirect to `/` |
| POST | `/login` | `login.store` | `AuthenticatedSessionController@store` | web, guest | Credential processing | Redirect to `/` |
| POST | `/logout` | `logout` | `AuthenticatedSessionController@destroy` | web, auth | Login redirect | Log out and redirect |

Framework routes outside ERP scope:

| Method | URI | Name | Protection and purpose |
|---|---|---|---|
| GET | `/up` | — | Public framework health endpoint; no ERP data |
| GET | `/storage/{path}` | `storage.local` | Laravel private local-disk handler requires a valid relative signature (403/404 otherwise) |
| PUT | `/storage/{path}` | `storage.local.upload` | Laravel upload handler requires `upload=1` and a valid relative signature (403/404 otherwise) |

There is no GET logout and no GET mutation route. CSRF remains active through
the web middleware group for every ERP mutation.
