{{-- resources/views/inventory/alerts-reorders.blade.php --}}
{{-- Alerts & Reorders submodule: extracted from submodule.blade.php --}}

        <section id="view-alerts" class="app-view hidden p-6 lg:p-8 max-w-7xl mx-auto w-full space-y-6 overflow-x-hidden">
            <div class="border-b border-gray-200 pb-4">
                <h2 class="text-2xl font-bold text-gray-900">Alerts & Reorders</h2>
                <p class="text-sm text-gray-500">Stock thresholds, shortage alerts, and the purchase order approval pipeline.</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div onclick="alertsApplyQuickFilter('Out of Stock')" class="bg-white p-5 rounded-xl border-2 border-transparent hover:border-red-500 cursor-pointer shadow-sm flex items-center justify-between transition group">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 group-hover:text-red-500">Out of Stock</p>
                        <h3 class="text-2xl font-bold text-navyBlue mt-1" id="alerts-out-count">0</h3>
                    </div>
                    <div class="p-3 bg-red-50 text-red-500 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                </div>
                <div onclick="alertsApplyQuickFilter('Low Stock')" class="bg-white p-5 rounded-xl border-2 border-transparent hover:border-amber-500 cursor-pointer shadow-sm flex items-center justify-between transition group">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 group-hover:text-amber-500">Low Stock</p>
                        <h3 class="text-2xl font-bold text-navyBlue mt-1" id="alerts-low-count">0</h3>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-500 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                </div>
                <div onclick="alertsApplyQuickFilter('Overstock')" class="bg-white p-5 rounded-xl border-2 border-transparent hover:border-amber-500 cursor-pointer shadow-sm flex items-center justify-between transition group">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 group-hover:text-amber-500">Overstock</p>
                        <h3 class="text-2xl font-bold text-navyBlue mt-1" id="alerts-over-count">0</h3>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-500 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
                </div>
            </div>

            <!-- Active Stock Alerts -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-xs font-bold text-navyBlue uppercase tracking-wider">Active Stock Alerts</h3>
                    <span class="text-[10px] bg-navyBlue text-white px-2 py-0.5 rounded font-bold" id="alerts-active-count">0 Active</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold text-[10px]">
                                <th class="py-3 px-4">Item</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4 text-center">Severity</th>
                                <th class="py-3 px-4 text-center">Qty / Threshold</th>
                            </tr>
                        </thead>
                        <tbody id="alerts-table-body" class="divide-y divide-gray-100 text-gray-700">
                            <tr><td colspan="4" class="py-6 text-center text-gray-400 italic">No active alerts.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Approvals Pipeline Board -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-xs font-bold text-navyBlue uppercase tracking-wider">Order Approvals Pipeline</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] bg-amber-500 text-white px-2 py-0.5 rounded font-bold hidden" id="alerts-draft-count">0 Auto-Drafts Awaiting Review</span>
                        <span class="text-[10px] bg-navyBlue text-white px-2 py-0.5 rounded font-bold" id="alerts-pipeline-count">0 Active</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase font-semibold text-[10px]">
                                <th class="py-3 px-4">Date / Time</th>
                                <th class="py-3 px-4">Requested By</th>
                                <th class="py-3 px-4">Order Details</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="alerts-pipeline-body" class="divide-y divide-gray-100 text-gray-700">
                            <tr><td colspan="5" class="py-6 text-center text-gray-400 italic">No orders currently pending review.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Search / Filter Strip -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col lg:flex-row gap-4 items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto flex-1">
                    <label for="alerts-search-input" class="sr-only">Search item name or ID</label>
                    <input type="text" id="alerts-search-input" placeholder="Search item name or ID..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navyBlue text-sm w-full sm:w-80" oninput="alertsRenderTable()">
                    <label for="alerts-status-filter" class="sr-only">Filter by status</label>
                    <select id="alerts-status-filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-navyBlue text-sm bg-white" onchange="alertsRenderTable()">
                        <option value="all">All Statuses</option>
                        <option value="Normal">Normal Stock</option>
                        <option value="Low Stock">Low Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                        <option value="Overstock">Overstock</option>
                    </select>
                </div>
            </div>

            <!-- Master Inventory Thresholds Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-200 text-navyBlue text-xs uppercase font-semibold">
                                <th class="py-4 px-4">Item Details</th>
                                <th class="py-4 px-2 text-center">Quantity</th>
                                <th class="py-4 px-3 text-center w-28">Min Limit</th>
                                <th class="py-4 px-3 text-center w-28">Max Limit</th>
                                <th class="py-4 px-4 text-center">Status</th>
                                <th class="py-4 px-3 text-center w-24">Auto-Reorder</th>
                                <th class="py-4 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody id="alerts-inventory-table-body" class="divide-y divide-gray-100 text-sm text-gray-700"></tbody>
                    </table>
                </div>
                <div id="alerts-no-results" class="hidden p-8 text-center text-gray-400">No records found.</div>
                <div id="alerts-table-toggle-wrap" class="hidden border-t border-gray-100 px-4 py-3 text-center">
                    <button type="button" id="alerts-table-toggle-btn" onclick="alertsToggleTableExpanded()" class="text-xs font-semibold text-navyBlue hover:underline"></button>
                </div>
            </div>
        </section>

    <!-- Alerts & Reorders: Purchase Order Modal -->
    <div id="alertsPoModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center z-[60] p-4">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
            <div class="bg-navyBlue text-white px-6 py-4 flex justify-between items-center">
                <h4 class="font-bold text-xs tracking-wide uppercase">New Order Form</h4>
                <button onclick="alertsClosePOModal()" class="text-white hover:text-gray-300 text-2xl font-semibold leading-none">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" id="alertsModalItemId">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Target Product Line</span>
                    <p id="alertsModalItemName" class="font-semibold text-gray-800 text-xs bg-gray-50 p-2.5 rounded border border-gray-200"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="alertsModalQty" class="block text-xs font-bold text-gray-500 mb-1">Order Quantity</label>
                        <input type="number" id="alertsModalQty" value="10" min="1" class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs focus:ring-1 focus:ring-navyBlue focus:outline-none">
                    </div>
                    <div>
                        <label for="alertsModalSupplier" class="block text-xs font-bold text-gray-500 mb-1">Supplier</label>
                        <select id="alertsModalSupplier" class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs bg-white focus:ring-1 focus:ring-navyBlue focus:outline-none">
                            <option value="Global Logistics">Global Logistics</option>
                            <option value="Nexus Distribution">Nexus Distribution</option>
                            <option value="Apex Supplies">Apex Supplies</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="alertsModalWarehouse" class="block text-xs font-bold text-gray-500 mb-1">Destination Warehouse</label>
                    <select id="alertsModalWarehouse" class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs bg-white focus:ring-1 focus:ring-navyBlue focus:outline-none">
                        <option value="Alpha Warehouse">Alpha Warehouse</option>
                        <option value="Beta Hub Facility">Beta Hub Facility</option>
                    </select>
                </div>
                <div class="bg-blue-50 text-navyBlue p-3 rounded text-xs leading-normal border border-blue-100">
                    <strong>Notice:</strong> This order requires approval in the <strong>Order Approvals Pipeline</strong> before it can be marked received.
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button onclick="alertsClosePOModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">Cancel</button>
                    <button onclick="alertsSubmitPOForm()" class="px-4 py-2 bg-emeraldGreen text-white rounded-lg text-xs font-semibold shadow hover:bg-emeraldGreen/90 transition">Submit to Pipeline</button>
                </div>
            </div>
        </div>
    </div>

<script>

    // ==========================================
    // ALERTS & REORDERS SUBMODULE
    // ==========================================
    let alertsStatusFilter = "all";
    const ALERTS_TABLE_COLLAPSED_LIMIT = 8;
    let alertsTableExpanded = false;

    window.alertsToggleTableExpanded = function() {
        alertsTableExpanded = !alertsTableExpanded;
        alertsRenderTable();
    }

    function alertsGetStatus(item) {
        // FIX: Look for item.qty instead of item.stock
        const qty = parseInt(item.qty) || 0; 
        const min = parseInt(item.minLimit) || 0;
        const max = parseInt(item.maxLimit) || 0;

        if (qty === 0) return "Out of Stock";
        if (min > 0 && qty < min) return "Low Stock";
        if (max > 0 && qty > max) return "Overstock";
        return "Normal";
    }

    function alertsStatusBadge(status) {
        switch (status) {
            case "Out of Stock": return `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Out of Stock</span>`;
            case "Low Stock": return `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Low Stock</span>`;
            case "Overstock": return `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Overstock</span>`;
            default: return `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Normal</span>`;
        }
    }

    window.alertsApplyQuickFilter = function(status) {
        alertsStatusFilter = status;
        alertsTableExpanded = false;
        document.getElementById('alerts-status-filter').value = status === 'all' ? 'all' : status;
        alertsRenderTable();
    }

    function alertsRenderCards() {
        const items = appState.inventory || [];
        let out = 0, low = 0, over = 0;
        items.forEach(item => {
            const s = alertsGetStatus(item);
            if (s === "Out of Stock") out++;
            else if (s === "Low Stock") low++;
            else if (s === "Overstock") over++;
        });
        document.getElementById('alerts-out-count').innerText = out;
        document.getElementById('alerts-low-count').innerText = low;
        document.getElementById('alerts-over-count').innerText = over;
    }

    function alertsRenderTable() {
        const search = (document.getElementById('alerts-search-input')?.value || '').toLowerCase();
        const dropdownFilter = document.getElementById('alerts-status-filter')?.value || 'all';
        const targetFilter = alertsStatusFilter !== 'all' ? alertsStatusFilter : dropdownFilter;

        const items = (appState.inventory || []).filter(item => {
            const status = alertsGetStatus(item);
            
            // FIX: Safely convert both name and id to strings to prevent crashes from old integer IDs or nulls!
            const safeName = item.name ? String(item.name).toLowerCase() : '';
            const safeId = item.id ? String(item.id).toLowerCase() : '';
            
            const matchesSearch = safeName.includes(search) || safeId.includes(search);
            const matchesStatus = targetFilter === 'all' || status === targetFilter;
            return matchesSearch && matchesStatus;
        });

        const tbody = document.getElementById('alerts-inventory-table-body');
        const noResults = document.getElementById('alerts-no-results');
        const toggleWrap = document.getElementById('alerts-table-toggle-wrap');
        const toggleBtn = document.getElementById('alerts-table-toggle-btn');
        if (!tbody) return;

        if (items.length === 0) {
            tbody.innerHTML = '';
            if (noResults) noResults.classList.remove('hidden');
            if (toggleWrap) toggleWrap.classList.add('hidden');
            return;
        }
        if (noResults) noResults.classList.add('hidden');

        const shouldCollapse = items.length > ALERTS_TABLE_COLLAPSED_LIMIT;
        const visibleItems = (shouldCollapse && !alertsTableExpanded)
            ? items.slice(0, ALERTS_TABLE_COLLAPSED_LIMIT)
            : items;

        if (toggleWrap && toggleBtn) {
            if (shouldCollapse) {
                toggleWrap.classList.remove('hidden');
                const hiddenCount = items.length - ALERTS_TABLE_COLLAPSED_LIMIT;
                toggleBtn.innerText = alertsTableExpanded
                    ? 'Show less'
                    : `Show ${hiddenCount} more item${hiddenCount > 1 ? 's' : ''} (${items.length} total)`;
            } else {
                toggleWrap.classList.add('hidden');
            }
        }

        tbody.innerHTML = visibleItems.map(item => {
            const status = alertsGetStatus(item);
            // Safely escape the name for the PO modal button
            const safeItemName = (item.name || '').replace(/'/g, "\\'");

            return `
                <tr class="hover:bg-gray-50 border-b border-gray-100 transition">
                    <td class="py-3 px-4">
                        <div class="font-semibold text-gray-900">${item.name}</div>
                        <div class="text-xs text-gray-400">${item.category} | ${item.id}</div>
                    </td>
                    <td class="py-3 px-2 text-center font-bold text-gray-900">${item.qty}</td>
                    <td class="py-3 px-3 text-center">
                        <label class="sr-only" for="alerts-min-${item.id}">Min limit for ${item.name}</label>
                        <input type="number" id="alerts-min-${item.id}" value="${item.minLimit}" min="0" onchange="alertsUpdateLimit('${item.id}', 'min', this.value)" class="w-16 border border-gray-300 rounded text-center px-1 py-0.5 text-xs">
                    </td>
                    <td class="py-3 px-3 text-center">
                        <label class="sr-only" for="alerts-max-${item.id}">Max limit for ${item.name}</label>
                        <input type="number" id="alerts-max-${item.id}" value="${item.maxLimit}" min="0" onchange="alertsUpdateLimit('${item.id}', 'max', this.value)" class="w-16 border border-gray-300 rounded text-center px-1 py-0.5 text-xs">
                    </td>
                    <td class="py-3 px-4 text-center">${alertsStatusBadge(status)}</td>
                    <td class="py-3 px-3 text-center">
                        <label class="relative inline-flex items-center cursor-pointer" title="Auto-create a draft PO when this item hits Low/Out of Stock">
                            <span class="sr-only">Auto-reorder for ${item.name}</span>
                            <input type="checkbox" class="sr-only peer" ${item.auto_reorder ? 'checked' : ''} onchange="alertsToggleAutoReorder('${item.id}', this.checked)">
                            <div class="w-9 h-5 bg-gray-200 peer-checked:bg-emeraldGreen rounded-full transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </label>
                    </td>
                    <td class="py-3 px-4 text-right">
                        ${status === 'Overstock'
                            ? `<span class="px-2.5 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed" title="This item is overstocked — purchase orders are disabled until stock drops below the max limit.">Overstocked</span>`
                            : `<button onclick="alertsOpenPOModal('${item.id}', '${safeItemName}')" class="px-2.5 py-1 text-xs font-semibold rounded bg-blue-50 text-navyBlue hover:bg-navyBlue hover:text-white border border-blue-200 transition">
                            Create PO
                        </button>`}
                    </td>
                </tr>`;
        }).join('');
    }

    function alertsRenderAlertsTable() {
        const tbody = document.getElementById('alerts-table-body');
        const counter = document.getElementById('alerts-active-count');
        if (!tbody) return;

        const alerts = appState.stockAlerts || [];
        counter.innerText = `${alerts.length} Active`;

        if (alerts.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-gray-400 italic">No active alerts.</td></tr>`;
            return;
        }

        const typeLabels = { out_of_stock: 'Out of Stock', low_stock: 'Low Stock', overstock: 'Overstock' };
        const sevStyle = { critical: 'bg-red-100 text-red-700', high: 'bg-amber-100 text-amber-700', medium: 'bg-blue-100 text-blue-700' };

        tbody.innerHTML = alerts.map(alert => {
            // FIX: Point to alert.item and alert.item_id to match the database payload
            const item = alert.item;

            return `
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 font-semibold text-gray-900">${item ? item.name : 'Deleted Record'} <span class="text-gray-400 font-normal">(ID: ${alert.item_id})</span></td>
                    <td class="py-3 px-4">${typeLabels[alert.type] || alert.type}</td>
                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${sevStyle[alert.severity] || 'bg-gray-100 text-gray-600'}">${alert.severity}</span></td>
                    <td class="py-3 px-4 text-center">${alert.current_qty} / ${alert.threshold_qty}</td>
                </tr>`;
        }).join('');
    }

    function alertsRenderPipelineTable() {
        const tbody = document.getElementById('alerts-pipeline-body');
        const counter = document.getElementById('alerts-pipeline-count');
        const draftCounter = document.getElementById('alerts-draft-count');
        if (!tbody) return;

        const requests = appState.approvalRequests || [];
        const pending = requests.filter(r => r.status === 'Pending');
        const drafts = requests.filter(r => r.status === 'Draft');

        counter.innerText = `${pending.length} Active`;
        if (drafts.length > 0) {
            draftCounter.innerText = `${drafts.length} Auto-Draft${drafts.length > 1 ? 's' : ''} Awaiting Review`;
            draftCounter.classList.remove('hidden');
        } else {
            draftCounter.classList.add('hidden');
        }

        if (requests.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="py-6 text-center text-gray-400 italic">No orders currently pending review.</td></tr>`;
            return;
        }

        tbody.innerHTML = requests.map(req => {
            let statusStyle = "text-amber-600 font-semibold";
            if (req.status === "Ordered") statusStyle = "text-blue-600 font-bold";
            if (req.status === "Received") statusStyle = "text-emeraldGreen font-bold";
            if (req.status === "Voided") statusStyle = "text-gray-400 line-through";
            if (req.status === "Draft") statusStyle = "text-amber-700 font-bold";

            let actionTray = `<span class="text-xs text-gray-400 italic">Completed</span>`;
            if (req.status === "Draft") {
                actionTray = `
                    <button onclick="alertsSubmitDraft(${req.reqId})" class="px-2 py-0.5 bg-navyBlue text-white rounded font-bold hover:bg-blue-800 text-[11px]">Submit to Pipeline</button>
                    <button onclick="alertsDiscardDraft(${req.reqId})" class="px-2 py-0.5 bg-red-500 text-white rounded font-bold hover:bg-red-600 text-[11px] ml-1">Discard</button>`;
            } else if (req.status === "Pending") {
                actionTray = `
                    <button onclick="alertsProcessPipeline(${req.reqId}, 'Approved')" class="px-2 py-0.5 bg-emeraldGreen text-white rounded font-bold hover:bg-emeraldGreen/90 text-[11px]">Approve</button>
                    <button onclick="alertsProcessPipeline(${req.reqId}, 'Voided')" class="px-2 py-0.5 bg-red-500 text-white rounded font-bold hover:bg-red-600 text-[11px] ml-1">Void</button>`;
            } else if (req.status === "Ordered") {
                actionTray = `<button onclick="alertsMarkReceived(${req.reqId})" class="px-2 py-0.5 bg-blue-600 text-white rounded font-bold hover:bg-blue-700 text-[11px]">Mark as Received</button>`;
            }

            return `
                <tr class="${req.status === 'Draft' ? 'hover:bg-amber-50/50 bg-amber-50/30' : 'hover:bg-gray-50/50'}">
                    <td class="py-3 px-4 text-gray-500">${req.timestamp}</td>
                    <td class="py-3 px-4 font-bold text-navyBlue">
                        ${req.requester}
                        ${req.source === 'auto' ? '<span class="ml-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 text-[9px] font-bold uppercase align-middle">Auto</span>' : ''}
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-medium text-gray-900">${req.details}</div>
                        <div class="text-[10px] text-gray-400">Supplier: ${req.supplier} | Route: ${req.warehouse}</div>
                    </td>
                    <td class="py-3 px-4 text-center ${statusStyle}">${req.status}</td>
                    <td class="py-3 px-4 text-right whitespace-nowrap">${actionTray}</td>
                </tr>`;
        }).join('');
    }

    function alertsRenderAll() {
        if (!document.getElementById('view-alerts')) return;
        alertsRenderCards();
        alertsRenderTable();
        alertsRenderAlertsTable();
        alertsRenderPipelineTable();
        alertsUpdateNavBadge();
    }

    // Keeps the sidebar's "Alerts & Reorders" badge in sync with this page's
    // own appState after every action here (submit PO, change a limit,
    // toggle auto-reorder, etc.), instead of only reflecting whatever count
    // was fetched once when the page first loaded.
    function alertsUpdateNavBadge() {
        const badge = document.getElementById('nav-alerts-badge');
        if (!badge) return;
        const activeAlerts = (appState.stockAlerts || []).filter(a => a.status === 'active');
        if (activeAlerts.length === 0) {
            badge.classList.add('hidden');
            badge.textContent = '';
            return;
        }
        badge.textContent = activeAlerts.length;
        badge.classList.remove('hidden');
        const hasCritical = activeAlerts.some(a => a.type === 'out_of_stock');
        badge.classList.toggle('bg-red-500', hasCritical);
        badge.classList.toggle('bg-amber-500', !hasCritical);
    }

    window.alertsUpdateLimit = async function(id, target, value) {
        const res = await fetch(`/inventory/api/limits/${id}`, { method: 'POST', headers, body: JSON.stringify({ target, value: parseInt(value) || 0 }) });
        appState = await res.json();
        alertsRenderAll(); // Force local redraw instantly
        try { refreshAllUI(); } catch(e) {}
    }

    window.alertsToggleAutoReorder = async function(id, enabled) {
        const res = await fetch(`/inventory/api/auto-reorder/${id}`, { method: 'POST', headers, body: JSON.stringify({ enabled }) });
        appState = await res.json();
        alertsRenderAll();
        try { refreshAllUI(); } catch(e) {}
    }

    window.alertsSubmitDraft = async function(id) {
        const res = await fetch(`/inventory/api/draft/${id}/submit`, { method: 'POST', headers });
        const data = await res.json();
        if (data.success === false) { alert(data.message || 'Operation failed'); return; }
        appState = data;
        alertsRenderAll();
        try { refreshAllUI(); } catch(e) {}
    }

    window.alertsDiscardDraft = async function(id) {
        const res = await fetch(`/inventory/api/draft/${id}/discard`, { method: 'POST', headers });
        const data = await res.json();
        if (data.success === false) { alert(data.message || 'Operation failed'); return; }
        appState = data;
        if (data.autoReorderTurnedOff) {
            alert("This draft was declined, and auto-reorder has been turned OFF for this item so it won't be redrafted automatically.");
        }
        alertsRenderAll();
        try { refreshAllUI(); } catch(e) {}
    }

    window.alertsProcessPipeline = async function(id, status) {
        const res = await fetch(`/inventory/api/pipeline/${id}`, { method: 'POST', headers, body: JSON.stringify({ status }) });
        const data = await res.json();
        if (data.success === false) { alert(data.message || 'Operation failed'); return; }
        appState = data;
        alertsRenderAll();
        try { refreshAllUI(); } catch(e) {}
    }

    window.alertsMarkReceived = async function(id) {
        const res = await fetch(`/inventory/api/pipeline/${id}/receive`, { method: 'POST', headers });
        const data = await res.json();
        if (data.success === false) { alert(data.message || 'Operation failed'); return; }
        appState = data;
        alertsRenderAll();
        try { refreshAllUI(); } catch(e) {}
    }

    window.alertsOpenPOModal = function(id, name) {
        document.getElementById('alertsModalItemId').value = id;
        document.getElementById('alertsModalItemName').innerText = name;
        document.getElementById('alertsPoModal').classList.remove('hidden');
        document.getElementById('alertsPoModal').classList.add('flex');
    }

    window.alertsClosePOModal = function() {
        document.getElementById('alertsPoModal').classList.add('hidden');
        document.getElementById('alertsPoModal').classList.remove('flex');
    }

    window.alertsSubmitPOForm = async function() {
        const id = document.getElementById('alertsModalItemId').value;
        const qty = parseInt(document.getElementById('alertsModalQty').value) || 0;
        const supplier = document.getElementById('alertsModalSupplier').value;
        const warehouse = document.getElementById('alertsModalWarehouse').value;
        const item = (appState.inventory || []).find(i => i.id === id);

        if (qty <= 0 || !item) { alert('Please enter a valid order quantity.'); return; }

        const payload = {
            details: `Purchase ${qty}x units of ${item.name}`,
            supplier,
            warehouse,
            itemsArray: [{ id: item.id, qty }],
        };

        const res = await fetch('/inventory/api/submit-po', { method: 'POST', headers, body: JSON.stringify(payload) });
        const data = await res.json();
        if (data.success === false) { alert(data.message || 'Operation failed'); return; }
        appState = data;
        alertsClosePOModal();
        alertsRenderAll();
        try { refreshAllUI(); } catch(e) {}
    }
</script>
