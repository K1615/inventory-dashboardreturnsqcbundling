@extends('layouts.inventoryItems')

@section('content')
    <!-- LEVEL 1: DYNAMIC PIE CHARTS -->
    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center">
            <h3 class="text-sm font-bold text-navyBlue mb-4 uppercase tracking-wider self-start">Stock by Category</h3>
            <div class="flex flex-col sm:flex-row items-center justify-around w-full gap-4">
                <div id="categoryPie" class="w-36 h-36 rounded-full shadow-inner transition-all duration-500" style="background: conic-gradient(#1E3A8A 0% 100%);"></div>
                <div id="categoryLegend" class="text-xs space-y-1 bg-gray-50 p-3 rounded border w-full sm:w-auto"></div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex flex-col items-center">
            <h3 class="text-sm font-bold text-navyBlue mb-4 uppercase tracking-wider self-start">Items in Each Warehouse</h3>
            <div class="flex flex-col sm:flex-row items-center justify-around w-full gap-4">
                <div id="warehousePie" class="w-36 h-36 rounded-full shadow-inner transition-all duration-500" style="background: conic-gradient(#10B981 0% 100%);"></div>
                <div id="warehouseLegend" class="text-xs space-y-1 bg-gray-50 p-3 rounded border w-full sm:w-auto"></div>
            </div>
        </div>
    </section>

    <!-- LEVEL 2: DUAL LOGS & CONTROLS -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-5 space-y-3">
            <div>
                <h2 class="text-base font-bold text-navyBlue">Action History Log</h2>
                <p class="text-xs text-gray-500">A permanent list of approved or voided changes.</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-[340px] overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-gray-50 z-10 shadow-sm">
                        <tr class="text-gray-500 text-[11px] uppercase font-bold border-b border-gray-200">
                            <th class="px-4 py-2.5">Date & Details</th>
                            <th class="px-4 py-2.5">Staff Info</th>
                            <th class="px-4 py-2.5 text-right">Outcome</th>
                        </tr>
                    </thead>
                    <tbody id="auditTableBody" class="divide-y divide-gray-100 text-xs text-gray-600"></tbody>
                </table>
                <div id="auditEmptyState" class="p-6 text-center text-gray-400 text-xs">No historical actions recorded yet.</div>
            </div>
        </div>
        <div class="lg:col-span-7 space-y-3">
            <div>
                <h2 class="text-base font-bold text-navyBlue">Approve Box Changes</h2>
                <p class="text-xs text-gray-500">Review detailed component change forms requested by the team.</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-[340px] overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-slate-100 z-10 shadow-sm">
                        <tr class="text-gray-600 text-[11px] uppercase font-bold border-b border-gray-200">
                            <th class="px-4 py-2.5">Request Details</th>
                            <th class="px-4 py-2.5">Proposed Updates & Specs Notes</th>
                            <th class="px-4 py-2.5 text-right">Review Action</th>
                        </tr>
                    </thead>
                    <tbody id="pendingTableBody" class="divide-y divide-gray-100 text-xs text-gray-700"></tbody>
                </table>
                <div id="pendingEmptyState" class="p-6 text-center text-gray-400 text-xs">Clear! No changes are currently waiting for review.</div>
            </div>
        </div>
    </section>

    <!-- LEVEL 3: MAIN PRODUCT GRIDS -->
    <section class="space-y-4">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-lg font-bold text-navyBlue">Current Parts List</h2>
                <p class="text-xs text-gray-500">View and manage all components verified in stock.</p>
            </div>
            <button onclick="openFormModal('add')" class="bg-emeraldGreen text-white font-semibold px-4 py-2 rounded-lg shadow hover:bg-emeraldGreen/90 transition text-sm self-start sm:self-auto">+ Add New Product</button>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Search Name</label>
                <input type="text" id="filterSearch" oninput="applyFilters()" placeholder="Type part name..." class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-navyBlue focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Category</label>
                <select id="filterCategory" onchange="applyFilters()" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm bg-white focus:ring-1 focus:ring-navyBlue focus:outline-none">
                    <option value="All">All Categories</option>
                    <option value="Processor">Processors</option>
                    <option value="Graphics Card">Graphics Cards</option>
                    <option value="Memory">Memory (RAM)</option>
                    <option value="Storage">Storage (SSD/HDD)</option>
                    <option value="Motherboard">Motherboards</option>
                    <option value="Power Supply">Power Supplies</option>
                    <option value="Case">Case</option>
                    <option value="Cooler">Cooler</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Location</label>
                <select id="filterWarehouse" onchange="applyFilters()" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm bg-white focus:ring-1 focus:ring-navyBlue focus:outline-none">
                    <option value="All">All Warehouses</option>
                    <option value="Warehouse A">Warehouse A</option>
                    <option value="Warehouse B">Warehouse B</option>
                    <option value="Warehouse C">Warehouse C</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status</label>
                <select id="filterStatus" onchange="applyFilters()" class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm bg-white focus:ring-1 focus:ring-navyBlue focus:outline-none">
                    <option value="All">All Statuses</option>
                    <option value="Active">Active</option>
                    <option value="Out-of-Stock">Out-of-Stock</option>
                    <option value="Discontinued">Discontinued</option>
                    <option value="Archived">Archived</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold border-b border-gray-200">
                            <th class="px-6 py-3.5">Item Details</th>
                            <th class="px-6 py-3.5">Category</th>
                            <th class="px-6 py-3.5">Warehouse Location</th>
                            <th class="px-6 py-3.5">Quantity</th>
                            <th class="px-6 py-3.5">Price</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody" class="divide-y divide-gray-100 text-sm text-gray-700"></tbody>
                </table>
            </div>
            <div id="emptyState" class="hidden p-8 text-center text-gray-500">No items found matching your filter options.</div>
        </div>
    </section>

    <!-- MODALS CONTAINER -->
    <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="bg-navyBlue text-white px-6 py-4 flex justify-between items-center">
                <h3 class="font-bold text-base">Product Specs Window</h3>
                <button onclick="closeViewModal()" class="text-gray-300 hover:text-white text-xl font-bold">&times;</button>
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="flex justify-between items-start border-b pb-2">
                    <div>
                        <span class="font-bold text-gray-400 text-xs uppercase block">Product Title</span> 
                        <span id="viewName" class="text-base font-bold text-gray-800"></span>
                        <span id="viewIdBadge" class="text-xs text-gray-400 block font-mono"></span>
                    </div>
                    <span id="viewStatusBadge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                </div>
                <div class="grid grid-cols-2 gap-4 border-b pb-2">
                    <div><span class="font-bold text-gray-400 text-xs uppercase block">Category</span> <span id="viewCategory"></span></div>
                    <div><span class="font-bold text-gray-400 text-xs uppercase block">Price Tag</span> $<span id="viewPrice"></span></div>
                </div>
                <div class="grid grid-cols-2 gap-4 border-b pb-2">
                    <div><span class="font-bold text-gray-400 text-xs uppercase block">Warehouse Location</span> <span id="viewWarehouse"></span></div>
                    <div><span class="font-bold text-gray-400 text-xs uppercase block">Shelf/Box Placement</span> <span id="viewLocation"></span></div>
                </div>
                <div class="border-b pb-2">
                    <span class="font-bold text-gray-400 text-xs uppercase block">Available Stock Count</span>
                    <span id="viewQty" class="text-base font-semibold"></span> units in storage
                </div>
                <div>
                    <span class="font-bold text-gray-400 text-xs uppercase block">Notes & Description</span>
                    <p id="viewDesc" class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded border border-gray-100 mt-1 leading-relaxed max-h-24 overflow-y-auto"></p>
                </div>
                <div class="pt-4 flex justify-between items-center border-t border-gray-100">
                    <button id="viewQuickEditBtn" class="text-xs font-semibold text-emeraldGreen hover:underline flex items-center gap-1">&#9998; Change information details</button>
                    <button onclick="closeViewModal()" class="px-4 py-1.5 bg-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-300">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="formModal" class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="bg-navyBlue text-white px-6 py-4 flex justify-between items-center">
                <h3 id="formModalTitle" class="font-bold text-base">Propose Changes Form</h3>
                <button onclick="closeFormModal()" class="text-gray-300 hover:text-white text-xl font-bold">&times;</button>
            </div>
            <form id="itemForm" onsubmit="handleFormSubmissionEvent(event)" class="p-6 space-y-4">
                <input type="hidden" id="formPartId">
                <input type="hidden" id="formSubmissionMode">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Part / Product Name</label>
                    <input type="text" id="formPartName" required placeholder="e.g., Intel Core i7 Processor" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-navyBlue focus:outline-none text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Product Category</label>
                        <select id="formPartCategory" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-navyBlue text-sm bg-white">
                            <option value="Processor">Processor</option>
                            <option value="Graphics Card">Graphics Card</option>
                            <option value="Memory">Memory</option>
                            <option value="Storage">Storage</option>
                            <option value="Motherboard">Motherboard</option>
                            <option value="Power Supply">Power Supply</option>
                            <option value="Case">Case</option>
                            <option value="Cooler">Cooler</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status Setting</label>
                        <select id="formPartStatus" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-navyBlue text-sm bg-white">
                            <option value="Active">Active</option>
                            <option value="Out-of-Stock">Out-of-Stock</option>
                            <option value="Discontinued">Discontinued</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Stock Amount</label>
                        <input type="number" id="formPartQty" min="0" required placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-navyBlue text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Price per Unit ($)</label>
                        <input type="number" id="formPartPrice" min="0" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-navyBlue text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Store Warehouse</label>
                        <select id="formPartWarehouse" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-navyBlue text-sm bg-white">
                            <option value="Warehouse A">Warehouse A</option>
                            <option value="Warehouse B">Warehouse B</option>
                            <option value="Warehouse C">Warehouse C</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Exact Box/Shelf Location Label</label>
                    <input type="text" id="formPartLocation" placeholder="e.g., Shelf A-12" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-navyBlue text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Product Description Notes</label>
                    <textarea id="formPartDesc" rows="3" placeholder="Enter specifications or compatibility notes..." class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-navyBlue text-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeFormModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-600 text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emeraldGreen text-white rounded text-sm font-semibold hover:bg-emeraldGreen/90 shadow">Send for Review</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="bg-red-600 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="font-bold text-base flex items-center gap-2"><span>⚠️</span> Propose Item Deletion</h3>
                <button onclick="closeDeleteModal()" class="text-red-200 hover:text-white text-xl font-bold">&times;</button>
            </div>
            <form id="deleteForm" onsubmit="handleDeleteSubmissionEvent(event)" class="p-6 space-y-4">
                <input type="hidden" id="deletePartId">
                <div class="bg-red-50 border border-red-100 p-3 rounded text-xs text-red-800">
                    You are requesting to remove <strong id="deleteTargetName"></strong> from active asset lists. This action must be reviewed by another administrator.
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Reason for Deletion</label>
                    <select id="deleteReason" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-red-500 text-sm bg-white">
                        <option value="" disabled selected>Select a core reason...</option>
                        <option value="Damaged Goods / Scrap">Damaged Goods / Scrap</option>
                        <option value="Discontinued Line">Discontinued Line</option>
                        <option value="Inventory Audit Adjustments">Inventory Audit Adjustments</option>
                        <option value="Data Entry Mistake">Data Entry Mistake</option>
                        <option value="Transferred Out of System">Transferred Out of System</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Detailed Context / Justification</label>
                    <textarea id="deleteNotes" rows="3" required placeholder="Provide a detailed explanation for auditors..." class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-red-500 text-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-600 text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm font-semibold hover:bg-red-700 shadow">Submit Deletion Request</button>
                </div>
            </form>
        </div>
    </div>

    <!-- HIDDEN DATA CONTEXT PORTS -->
    <div id="laravelItemsBridge" data-inventory="{{ json_encode($items) }}" class="hidden"></div>
    <div id="laravelPendingBridge" data-pending="{{ json_encode($pending) }}" class="hidden"></div>
    <div id="laravelHistoryBridge" data-history="{{ json_encode($history) }}" class="hidden"></div>

    <script>
        // Global CSRF Token config for safe Laravel requests
        const CSRF_TOKEN = "{{ csrf_token() }}";

        // Hydrate arrays from database query values
        let masterInventory = JSON.parse(document.getElementById("laravelItemsBridge").getAttribute("data-inventory"));
        
        // Remap Laravel DB models back to original frontend UI expected format
        let pendingRequests = JSON.parse(document.getElementById("laravelPendingBridge").getAttribute("data-pending")).map(req => ({
            requestId: req.id,
            timestamp: new Date(req.created_at).toLocaleDateString(),
            requestor: req.requestor,
            type: req.type,
            targetData: { ...req.proposed_data, id: req.target_item_id || 'NEW' }
        }));

        let auditLogs = JSON.parse(document.getElementById("laravelHistoryBridge").getAttribute("data-history")).map(log => ({
            date: new Date(log.updated_at).toLocaleDateString(),
            itemTitle: log.proposed_data.name,
            type: log.type,
            proposedBy: log.requestor,
            reviewedBy: log.reviewer,
            outcome: log.outcome
        }));

        window.onload = () => { refreshInterface(); };

        function refreshInterface() {
            applyFilters();
            renderPendingTable();
            renderAuditTable();
            calculateAndDrawPieCharts();
        }

        function renderInventoryTable(dataList) {
            const tableBody = document.getElementById("inventoryTableBody");
            tableBody.innerHTML = "";
            if (dataList.length === 0) {
                document.getElementById("emptyState").classList.remove("hidden");
                return;
            }
            document.getElementById("emptyState").classList.add("hidden");

            dataList.forEach(item => {
                const row = document.createElement("tr");
                row.className = "hover:bg-gray-50 transition-colors";
                row.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 data-name"></div>
                        <div class="text-[11px] text-gray-400 font-mono">ID: #${item.id}</div>
                    </td>
                    <td class="px-6 py-4"><span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-gray-700 border border-gray-200">${item.category}</span></td>
                    <td class="px-6 py-4">
                        <div class="text-xs font-semibold text-gray-800">${item.warehouse}</div>
                        <div class="text-xs text-gray-400 font-mono data-loc"></div>
                    </td>
                    <td class="px-6 py-4 font-bold ${parseInt(item.qty) === 0 ? 'text-red-500' : 'text-gray-700'}">${item.qty} pcs</td>
                    <td class="px-6 py-4 font-semibold">$${parseFloat(item.price).toFixed(2)}</td>
                    <td class="px-6 py-4">${getStatusBadgeMarkup(item.status)}</td>
                    <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                        <button class="text-xs font-semibold bg-gray-100 text-navyBlue hover:bg-gray-200 px-2 py-1 rounded view-btn">View</button>
                        <button class="text-xs font-semibold bg-emeraldGreen/10 text-emeraldGreen hover:bg-emeraldGreen/20 px-2 py-1 rounded edit-btn">Edit</button>
                        <button class="text-xs font-semibold bg-red-50 text-red-600 hover:bg-red-100 px-2 py-1 rounded del-btn">Delete</button>
                    </td>
                `;
                row.querySelector('.data-name').innerText = item.name;
                // Change item.location to item.zone
                row.querySelector('.data-loc').innerText = item.zone || 'Unassigned';
                row.querySelector('.view-btn').onclick = () => viewItemDetails(item.id);
                row.querySelector('.edit-btn').onclick = () => openFormModal('edit', item.id);
                row.querySelector('.del-btn').onclick = () => openDeleteModal(item.id);
                tableBody.appendChild(row);
            });
        }

        function applyFilters() {
            const searchVal = document.getElementById("filterSearch").value.toLowerCase().trim();
            const catVal = document.getElementById("filterCategory").value;
            const whVal = document.getElementById("filterWarehouse").value;
            const statVal = document.getElementById("filterStatus").value;

            const finalResults = masterInventory.filter(item => {
                const matchesSearch = item.name.toLowerCase().includes(searchVal) || (item.desc && item.desc.toLowerCase().includes(searchVal));
                const matchesCategory = (catVal === "All" || item.category === catVal);
                const matchesWarehouse = (whVal === "All" || item.warehouse === whVal);
                const matchesStatus = (statVal === "All" || item.status === statVal);
                return matchesSearch && matchesCategory && matchesWarehouse && matchesStatus;
            });
            renderInventoryTable(finalResults);
        }

        function viewItemDetails(id) {
            const item = masterInventory.find(i => i.id === id);
            if (!item) return;

            document.getElementById("viewName").innerText = item.name;
            document.getElementById("viewIdBadge").innerText = `Database Key Index: #${item.id}`;
            document.getElementById("viewCategory").innerText = item.category;
            document.getElementById("viewPrice").innerText = parseFloat(item.price).toFixed(2);
            document.getElementById("viewWarehouse").innerText = item.warehouse;
            // Change item.location to item.zone
            document.getElementById("viewLocation").innerText = item.zone || 'N/A';
            document.getElementById("viewQty").innerText = item.qty;
            document.getElementById("viewDesc").innerText = item.desc || "No description notes logged.";
            
            const badge = document.getElementById("viewStatusBadge");
            badge.className = "px-2.5 py-0.5 rounded-full text-xs font-semibold";
            if (item.status === 'Active') badge.classList.add('bg-emeraldGreen', 'text-white');
            else if (item.status === 'Out-of-Stock') badge.classList.add('bg-amber-500', 'text-white');
            else if (item.status === 'Discontinued') badge.classList.add('bg-red-500', 'text-white');
            else badge.classList.add('bg-gray-400', 'text-white');
            badge.innerText = item.status;

            document.getElementById("viewQuickEditBtn").onclick = () => { closeViewModal(); openFormModal('edit', item.id); };
            document.getElementById("viewModal").classList.remove("hidden");
        }

        function closeViewModal() { document.getElementById("viewModal").classList.add("hidden"); }

        function openFormModal(mode, targetId = null) {
            document.getElementById("itemForm").reset();
            document.getElementById("formSubmissionMode").value = mode;
            if (mode === 'add') {
                document.getElementById("formModalTitle").innerText = "Propose New Item Addition";
                document.getElementById("formPartId").value = "";
            } else if (mode === 'edit') {
                const target = masterInventory.find(i => i.id === targetId);
                if(!target) return;
                document.getElementById("formModalTitle").innerText = `Modify Info for Item #${target.id}`;
                document.getElementById("formPartId").value = target.id;
                document.getElementById("formPartName").value = target.name;
                document.getElementById("formPartCategory").value = target.category;
                document.getElementById("formPartStatus").value = target.status;
                document.getElementById("formPartQty").value = target.qty;
                document.getElementById("formPartPrice").value = target.price;
                document.getElementById("formPartWarehouse").value = target.warehouse;
                // Change target.location to target.zone
                document.getElementById("formPartLocation").value = target.zone || "";
                document.getElementById("formPartDesc").value = target.desc || "";
            }
            document.getElementById("formModal").classList.remove("hidden");
        }

        function closeFormModal() { document.getElementById("formModal").classList.add("hidden"); }
        
        function openDeleteModal(id) {
            document.getElementById("deleteForm").reset();
            const target = masterInventory.find(i => i.id === id);
            if (!target) return;
            document.getElementById("deletePartId").value = target.id;
            document.getElementById("deleteTargetName").innerText = `"${target.name}" (ID: #${target.id})`;
            document.getElementById("deleteModal").classList.remove("hidden");
        }
        
        function closeDeleteModal() { document.getElementById("deleteModal").classList.add("hidden"); }

        // Fetch API request handler targeting Laravel router backend
        function handleFormSubmissionEvent(e) {
            e.preventDefault();
            const mode = document.getElementById("formSubmissionMode").value;
            const idVal = document.getElementById("formPartId").value;
            
            const payload = {
                name: document.getElementById("formPartName").value,
                category: document.getElementById("formPartCategory").value,
                status: document.getElementById("formPartStatus").value,
                qty: parseInt(document.getElementById("formPartQty").value) || 0,
                price: parseFloat(document.getElementById("formPartPrice").value) || 0.00,
                warehouse: document.getElementById("formPartWarehouse").value,
                location: document.getElementById("formPartLocation").value,
                desc: document.getElementById("formPartDesc").value
            };

            fetch('/api/requests', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({
                    type: mode === 'add' ? 'ADD' : 'EDIT',
                    target_item_id: idVal ? idVal : null, // Removed parseInt()
                    proposed_data: payload
                })
            })
            .then(res => res.json())
            .then(data => { if(data.success) window.location.reload(); });

            closeFormModal();
        }

        function handleDeleteSubmissionEvent(e) {
            e.preventDefault();
            const targetId = document.getElementById("deletePartId").value; // Removed parseInt()
            const target = masterInventory.find(i => i.id === targetId);
            if (!target) return;

            fetch('/api/requests', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({
                    type: 'DELETE',
                    target_item_id: targetId,
                    proposed_data: { name: target.name, category: target.category, warehouse: target.warehouse, qty: target.qty, price: target.price, status: target.status, desc: `Reason: [${document.getElementById("deleteReason").value}] - ${document.getElementById("deleteNotes").value}` }
                })
            })
            .then(res => res.json())
            .then(data => { if(data.success) window.location.reload(); });

            closeDeleteModal();
        }

        function renderPendingTable() {
            const tableBody = document.getElementById("pendingTableBody");
            tableBody.innerHTML = "";
            if (pendingRequests.length === 0) {
                document.getElementById("pendingEmptyState").classList.remove("hidden");
                return;
            }
            document.getElementById("pendingEmptyState").classList.add("hidden");

            pendingRequests.forEach(req => {
                const row = document.createElement("tr");
                let badgeColor = req.type === 'DELETE' ? "bg-red-600 text-white" : req.type === 'ADD' ? "bg-emeraldGreen text-white" : "bg-navyBlue text-white";
                row.className = req.type === 'DELETE' ? "bg-red-50/30 hover:bg-red-50" : "bg-amber-50/40 hover:bg-amber-50";
                row.innerHTML = `
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="font-bold text-gray-800">${req.requestor}</div>
                        <div class="text-[10px] text-gray-400 font-mono">${req.timestamp}</div>
                        <span class="inline-block mt-1 px-1.5 py-0.5 text-[9px] font-bold ${badgeColor} rounded uppercase">${req.type}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-gray-900 part-title"></div>
                        <div class="text-[11px] text-gray-600 mt-0.5">${req.targetData.category} | ${req.targetData.warehouse} | ${req.targetData.qty} pcs</div>
                        <div class="text-[10px] italic text-gray-500 bg-white border border-gray-100 p-1.5 rounded mt-1 shadow-inner max-h-16 overflow-y-auto contextual-desc"></div>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <select class="text-xs px-2 py-1 border border-gray-300 rounded bg-white font-medium focus:outline-none select-act">
                            <option value="" disabled selected>Choose...</option>
                            <option value="approve" class="text-emeraldGreen font-semibold">Approve</option>
                            <option value="void" class="text-red-500 font-semibold">Void</option>
                        </select>
                    </td>
                `;

                row.querySelector('.part-title').innerText = `${req.targetData.name} (#${req.targetData.id})`;
                row.querySelector('.contextual-desc').innerText = `Context: ${req.targetData.desc || 'No added details.'}`;
                row.querySelector('.select-act').onchange = (e) => handleSelectDecision(e.target, req.requestId);
                tableBody.appendChild(row);
            });
        }

        function handleSelectDecision(select, requestId) {
            const decision = select.value;
            if (!decision) return;

            fetch(`/api/requests/${requestId}/resolve`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({
                    decision: decision
                })
            })
            .then(res => res.json())
            .then(data => { if(data.success) window.location.reload(); });
        }

        function renderAuditTable() {
            const tableBody = document.getElementById("auditTableBody");
            tableBody.innerHTML = "";
            if (auditLogs.length === 0) {
                document.getElementById("auditEmptyState").classList.remove("hidden");
                return;
            }
            document.getElementById("auditEmptyState").classList.add("hidden");

            auditLogs.forEach(log => {
                const row = document.createElement("tr");
                const color = log.outcome === 'APPROVED' ? 'text-emeraldGreen font-bold' : 'text-red-500 line-through';
                row.innerHTML = `
                    <td class="px-4 py-2.5">
                        <div class="text-gray-400 text-[10px]">${log.date}</div>
                        <div class="font-semibold text-gray-800 truncate max-w-[120px] audit-title"></div>
                        <div class="text-[10px] font-bold text-navyBlue uppercase">${log.type}</div>
                    </td>
                    <td class="px-4 py-2.5 text-gray-500 text-[11px]"><div>Req: ${log.proposedBy}</div><div>Rev: ${log.reviewedBy}</div></td>
                    <td class="px-4 py-2.5 text-right font-bold text-[11px] ${color}">${log.outcome}</td>
                `;
                row.querySelector('.audit-title').innerText = log.itemTitle;
                tableBody.appendChild(row);
            });
        }

        function calculateAndDrawPieCharts() {
            const colorPalettes = ['#1E3A8A', '#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];
            const categories = ["Processor", "Graphics Card", "Memory", "Storage", "Motherboard", "Power Supply"];
            let catSummary = {}; categories.forEach(c => catSummary[c] = 0);
            let totalCatQty = 0;
            
            masterInventory.forEach(item => {
                if (catSummary[item.category] !== undefined) { catSummary[item.category] += parseInt(item.qty); totalCatQty += parseInt(item.qty); }
            });

            const catPie = document.getElementById("categoryPie");
            const catLegend = document.getElementById("categoryLegend");
            catLegend.innerHTML = "";

            if (totalCatQty === 0) {
                catPie.style.background = `conic-gradient(#e5e7eb 0% 100%)`;
                catLegend.innerHTML = `<div class="text-gray-400">No active stock</div>`;
            } else {
                let currentAngle = 0; let conicStyles = [];
                categories.forEach((cat, idx) => {
                    const qty = catSummary[cat]; const percent = (qty / totalCatQty) * 100; const nextAngle = currentAngle + percent;
                    const color = colorPalettes[idx % colorPalettes.length];
                    if (percent > 0) conicStyles.push(`${color} ${currentAngle.toFixed(1)}% ${nextAngle.toFixed(1)}%`);
                    catLegend.innerHTML += `<div class="flex items-center space-x-2"><span class="w-2 h-2 rounded-full" style="background-color: ${color}"></span><span>${cat}s: <strong>${qty}</strong></span></div>`;
                    currentAngle = nextAngle;
                });
                catPie.style.background = `conic-gradient(${conicStyles.join(', ')})`;
            }

            const warehouses = ["Warehouse A", "Warehouse B", "Warehouse C"];
            let whSummary = {}; warehouses.forEach(w => whSummary[w] = 0);
            let totalWhQty = 0;

            masterInventory.forEach(item => {
                if (whSummary[item.warehouse] !== undefined) { whSummary[item.warehouse] += parseInt(item.qty); totalWhQty += parseInt(item.qty); }
            });

            const whPie = document.getElementById("warehousePie");
            const whLegend = document.getElementById("warehouseLegend");
            whLegend.innerHTML = "";

            if (totalWhQty === 0) {
                whPie.style.background = `conic-gradient(#e5e7eb 0% 100%)`;
                whLegend.innerHTML = `<div class="text-gray-400">No active stock</div>`;
            } else {
                let currentAngle = 0; let conicStyles = [];
                const whColors = ['#10B981', '#1E3A8A', '#F59E0B'];
                warehouses.forEach((wh, idx) => {
                    const qty = whSummary[wh]; const percent = (qty / totalWhQty) * 100; const nextAngle = currentAngle + percent;
                    const color = whColors[idx];
                    if (percent > 0) conicStyles.push(`${color} ${currentAngle.toFixed(1)}% ${nextAngle.toFixed(1)}%`);
                    whLegend.innerHTML += `<div class="flex items-center space-x-2"><span class="w-2 h-2 rounded-full" style="background-color: ${color}"></span><span>${wh}: <strong>${qty}</strong></span></div>`;
                    currentAngle = nextAngle;
                });
                whPie.style.background = `conic-gradient(${conicStyles.join(', ')})`;
            }
        }

        function getStatusBadgeMarkup(status) {
            switch (status) {
                case 'Active': return `<span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emeraldGreen text-white">Active</span>`;
                case 'Out-of-Stock': return `<span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-white">Out-of-Stock</span>`;
                case 'Discontinued': return `<span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-red-500 text-white">Discontinued</span>`;
                default: return `<span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-gray-400 text-white">Archived</span>`;
            }
        }
    </script>
@endsection
