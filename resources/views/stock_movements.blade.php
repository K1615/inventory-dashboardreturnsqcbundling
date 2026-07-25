@extends('layouts.stockMovement')

@section('content')
<div class="flex-grow w-full flex flex-col min-w-0">
    <main class="p-6 space-y-6 max-w-7xl w-full mx-auto">

        <!-- TOP METRICS & CHART VISUALIZATION -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- SUMMARY STATS CARDS -->
            <div class="lg:col-span-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Available Stock</p>
                        <h3 id="statTotalStock" class="text-3xl font-bold text-navy mt-1">0</h3>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">Combined count across all parts catalog items</div>
                </div>
                
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Activity Management</p>
                        <h3 id="statTotalLogs" class="text-3xl font-bold text-emeraldAccent mt-1">0</h3>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <button onclick="openTransactionModal()" class="w-full bg-navy text-white font-medium py-2 px-4 rounded-lg text-xs hover:opacity-95 transition shadow-sm text-center">
                            + Create New Entry
                        </button>
                    </div>
                </div>
            </div>

            <!-- TRANSACTION TREND CHART -->
            <div class="lg:col-span-2 bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col">
                <div class="mb-2">
                    <h3 class="text-sm font-bold text-navy">Transaction Timeline Rate</h3>
                    <p class="text-xs text-gray-500">Number of total recorded transaction movements from date to date</p>
                </div>
                <div class="flex-grow relative h-40">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>

        </div>

        <!-- SECTION 1: ACTIVE TRANSACTION REQUESTS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-navy">Transaction Requests</h2>
                    <p class="text-xs text-gray-500">Active or unverified order movements awaiting completion status</p>
                </div>

                <!-- FILTER CONTROLS FOR REQUESTS -->
                <div class="flex flex-wrap items-center gap-2">
                    <select 
                        id="requestTypeFilter" 
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-navy bg-white"
                        onchange="filterRequestTable()"
                    >
                        <option value="All">All Types</option>
                        <option value="Stock-In">Stock-In</option>
                        <option value="Stock-Out">Stock-Out</option>
                        <option value="Warehouse Transfer">Warehouse Transfer</option>
                        <option value="Product Return">Product Return</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="p-3">Date</th>
                            <th class="p-3">ID</th>
                            <th class="p-3">Item Name</th>
                            <th class="p-3">Movement Type</th>
                            <th class="p-3 text-center">Qty</th>
                            <th class="p-3">Reason / Note</th>
                            <th class="p-3">Authorized By</th>
                            <th class="p-3">Change Status</th>
                        </tr>
                    </thead>
                    <tbody id="requestTableBody" class="divide-y divide-gray-100 text-gray-700">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 2: FINALIZED TRANSACTION LOGS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-navy">Finalized Transaction History</h2>
                    <p class="text-xs text-gray-500">Completed tracking archives of all approved or voided stock updates</p>
                </div>
                
                <!-- FILTER CONTROLS FOR LOGS -->
                <div class="flex flex-wrap items-center gap-2">
                    <select 
                        id="logStatusFilter" 
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-navy bg-white"
                        onchange="filterHistoryTable()"
                    >
                        <option value="All">All Outcomes</option>
                        <option value="Approved">Approved</option>
                        <option value="Voided">Voided</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="p-3">Date Completed</th>
                            <th class="p-3">ID</th>
                            <th class="p-3">Item Name</th>
                            <th class="p-3">Movement Type</th>
                            <th class="p-3 text-center">Qty</th>
                            <th class="p-3">Reason / Note</th>
                            <th class="p-3">Authorized By</th>
                            <th class="p-3">Outcome Status</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody" class="divide-y divide-gray-100 text-gray-700">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECTION 3: CURRENT INVENTORY PARTS TABLE -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-navy">Parts Stock Catalog</h2>
                    <p class="text-xs text-gray-500">Live inventory warehouse counts and item listings</p>
                </div>
                
                <!-- SEARCH/FILTER BARS -->
                <div class="flex flex-wrap items-center gap-2">
                    <input 
                        type="text" 
                        id="partSearch" 
                        placeholder="Search product name..." 
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-navy w-44"
                        oninput="filterInventoryTable()"
                    >
                    <select 
                        id="partCategory" 
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-navy bg-white"
                        onchange="filterInventoryTable()"
                    >
                        <option value="All">All Categories</option>
                        <option value="CPU">CPU</option>
                        <option value="GPU">GPU</option>
                        <option value="RAM">RAM</option>
                        <option value="Storage">Storage</option>
                        <option value="Motherboard">Motherboard</option>
                        <option value="PSU">PSU</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="p-3">Part ID</th>
                            <th class="p-3">Item Name</th>
                            <th class="p-3">Category</th>
                            <th class="p-3 text-center">Available Stock</th>
                            <th class="p-3">Warranty Expiration</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody" class="divide-y divide-gray-100 text-gray-700">
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- POPUP WINDOW MODAL FORM FRAME -->
<div id="transactionModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-lg mx-4 overflow-hidden transform scale-95 transition-transform duration-300" id="modalContainer">
        
        <div class="bg-navy p-4 text-white flex justify-between items-center">
            <h3 class="font-bold text-sm tracking-wide">Create New Transaction Entry</h3>
            <button onclick="closeTransactionModal()" class="text-gray-300 hover:text-white font-bold text-lg">&times;</button>
        </div>

        <form id="movementForm" onsubmit="submitTransactionForm(event)" class="p-6 space-y-4 text-xs">
            
            <div class="grid grid-cols-2 gap-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                <div>
                    <label class="block font-semibold text-gray-500 mb-0.5">Transaction ID</label>
                    <input type="text" id="formTxId" readonly class="w-full bg-transparent font-mono font-bold text-gray-800 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-gray-500 mb-0.5">Date</label>
                    <input type="date" id="formDate" required class="w-full bg-white border border-gray-200 rounded px-1.5 py-0.5 text-gray-800 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-gray-600 mb-1">Select Item Name</label>
                <select id="formItem" required class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-navy focus:outline-none">
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Movement Type</label>
                    <select id="formType" required onchange="toggleWarehouseFields()" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-navy focus:outline-none">
                        <option value="Stock-In">Stock-In (Delivery)</option>
                        <option value="Stock-Out">Stock-Out (Sale)</option>
                        <option value="Warehouse Transfer">Warehouse Transfer</option>
                        <option value="Product Return">Product Return</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Log Created By</label>
                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 font-semibold text-gray-700">
                        {{ auth()->user()->name }}
                    </div>
                </div>
            </div>

            <div id="warehouseRouteRow" class="grid grid-cols-2 gap-4 hidden bg-blue-50/50 p-3 rounded-lg border border-blue-100">
                <div>
                    <label class="block font-semibold text-blue-900 mb-1">From Warehouse</label>
                    <select id="formSrcWh" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 bg-white">
                        <option value="Main Warehouse A">Main Warehouse A</option>
                        <option value="Branch Location B">Branch Location B</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-blue-900 mb-1">To Warehouse</label>
                    <select id="formDestWh" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 bg-white">
                        <option value="Branch Location B">Branch Location B</option>
                        <option value="Main Warehouse A">Main Warehouse A</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-1">
                    <label class="block font-semibold text-gray-600 mb-1">Quantity</label>
                    <input type="number" id="formQty" min="1" value="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-navy focus:outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold text-gray-600 mb-1">Reason / Note</label>
                    <input type="text" id="formNote" placeholder="e.g. Regular restock, order fulfillment" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-navy focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeTransactionModal()" class="px-4 py-2 bg-gray-100 text-gray-600 font-medium rounded-lg hover:bg-gray-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-navy text-white font-medium rounded-lg hover:opacity-90 transition">Save Log</button>
            </div>
        </form>
    </div>
</div>

<!-- SCRIPT DEJECTIONS USING REAL TIME ENDPOINTS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let inventory = [];
    let globalTransactions = [];
    let chartInstance = null;

    window.onload = function() {
        fetchDashboardData();
    };

    function fetchDashboardData() {
        fetch("{{ route('stock-movements.data') }}")
            .then(res => res.json())
            .then(data => {
                inventory = data.parts;
                globalTransactions = data.movements;
                refreshAllViews();
                initChartVisualization();
            })
            .catch(err => console.error("Error retrieving dataset:", err));
    }

    function refreshAllViews() {
        filterRequestTable();
        filterHistoryTable();
        filterInventoryTable();
        populateFormDropdowns();
        updateCalculatedMetrics();
    }

    function filterRequestTable() {
        const selectedType = document.getElementById('requestTypeFilter').value;
        const tbody = document.getElementById('requestTableBody');
        tbody.innerHTML = '';

        const filtered = globalTransactions.filter(t => {
            return t.status === 'Pending' && (selectedType === 'All' || t.type === selectedType);
        });

        if(filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="p-4 text-center text-gray-400 italic">No pending requests found.</td></tr>`;
            return;
        }

        filtered.forEach(log => {
            const product = inventory.find(p => p.id === log.part_id) || { name: "Unknown Item" };
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 border-b border-gray-100';
            tr.innerHTML = `
                <td class="p-3 font-medium text-gray-500 whitespace-nowrap">${log.date}</td>
                <td class="p-3 font-mono font-bold text-gray-900">${log.tx_id}</td>
                <td class="p-3 font-medium text-gray-800">${product.name}</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-100">${log.type}</span></td>
                <td class="p-3 text-center font-bold">${log.qty}</td>
                <td class="p-3 text-gray-500 max-w-xs truncate" title="${log.note}">${log.note}</td>
                <td class="p-3 text-gray-600 font-medium">${log.user}</td>
                <td class="p-3">
                    <select onchange="updateTransactionStatus('${log.tx_id}', this.value)" class="border border-gray-300 rounded px-2 py-1 bg-white text-xs font-medium focus:outline-none focus:ring-1 focus:ring-navy">
                        <option value="Pending" selected>Pending</option>
                        <option value="Approved">Approve</option>
                        <option value="Voided">Void/Deny</option>
                    </select>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function filterHistoryTable() {
        const statusFilter = document.getElementById('logStatusFilter').value;
        const tbody = document.getElementById('historyTableBody');
        tbody.innerHTML = '';

        const filtered = globalTransactions.filter(t => {
            return t.status !== 'Pending' && (statusFilter === 'All' || t.status === statusFilter);
        });

        if(filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="p-4 text-center text-gray-400 italic">No finalized logs recorded.</td></tr>`;
            return;
        }

        filtered.forEach(log => {
            const product = inventory.find(p => p.id === log.part_id) || { name: "Unknown Item" };
            let outcomeClass = log.status === 'Approved' ? 'bg-emerald-50 text-emerald-700 font-bold' : 'bg-gray-100 text-gray-500 line-through';

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 border-b border-gray-100';
            tr.innerHTML = `
                <td class="p-3 font-medium text-gray-500 whitespace-nowrap">${log.date}</td>
                <td class="p-3 font-mono font-bold text-gray-900">${log.tx_id}</td>
                <td class="p-3 font-medium text-gray-800">${product.name}</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">${log.type}</span></td>
                <td class="p-3 text-center font-bold">${log.qty}</td>
                <td class="p-3 text-gray-500 max-w-xs truncate" title="${log.note}">${log.note}</td>
                <td class="p-3 text-gray-600 font-medium">${log.user}</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded text-[10px] uppercase tracking-wider ${outcomeClass}">${log.status}</span></td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderInventoryTable(data) {
        const tbody = document.getElementById('inventoryTableBody');
        tbody.innerHTML = '';
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 border-b border-gray-100';
            tr.innerHTML = `
                <td class="p-3 font-mono font-semibold text-gray-500">${item.id}</td>
                <td class="p-3 font-medium text-gray-900">${item.name}</td>
                <td class="p-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full font-semibold">${item.category}</span></td>
                <td class="p-3 text-center font-bold text-gray-900">${item.stock}</td>
                <td class="p-3 text-gray-500">${item.exp_date || 'N/A'}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function updateTransactionStatus(txId, newStatus) {
        if (newStatus === 'Pending') return;

        fetch(`/api/stock-movements/${txId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchDashboardData();
            } else {
                alert(data.message);
                fetchDashboardData(); 
            }
        })
        .catch(err => {
            // DEBUG: Catch any hidden Javascript or Network errors
            console.error("Error mutating status update:", err);
            alert("A JavaScript error occurred. Check the console (F12)."); 
        });
    }

    function filterInventoryTable() {
        const query = document.getElementById('partSearch').value.toLowerCase();
        const category = document.getElementById('partCategory').value;

        const filtered = inventory.filter(item => {
            const queryMatch = item.name.toLowerCase().includes(query) || item.id.toLowerCase().includes(query);
            const catMatch = category === 'All' || item.category === category;
            return queryMatch && catMatch;
        });
        renderInventoryTable(filtered);
    }

    function updateCalculatedMetrics() {
        const sumStock = inventory.reduce((acc, curr) => acc + curr.stock, 0);
        document.getElementById('statTotalStock').textContent = sumStock;
        document.getElementById('statTotalLogs').textContent = globalTransactions.length;
    }

    function populateFormDropdowns() {
        const dropdown = document.getElementById('formItem');
        dropdown.innerHTML = '';
        inventory.forEach((item) => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = `${item.name} (In Stock: ${item.stock})`;
            dropdown.appendChild(opt);
        });
    }

    function toggleWarehouseFields() {
        const type = document.getElementById('formType').value;
        const routeRow = document.getElementById('warehouseRouteRow');
        if (type === 'Warehouse Transfer') {
            routeRow.classList.remove('hidden');
        } else {
            routeRow.classList.add('hidden');
        }
    }

    function openTransactionModal() {
        const modal = document.getElementById('transactionModal');
        const container = document.getElementById('modalContainer');
        
        document.getElementById('formTxId').value = "TX-" + Math.floor(10000 + Math.random() * 90000);
        document.getElementById('formDate').value = new Date().toISOString().split('T')[0];
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            container.classList.remove('scale-95');
        }, 50);
    }

    function closeTransactionModal() {
        const modal = document.getElementById('transactionModal');
        const container = document.getElementById('modalContainer');
        
        modal.classList.add('opacity-0');
        container.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('movementForm').reset();
            toggleWarehouseFields();
        }, 300);
    }

    function submitTransactionForm(e) {
        e.preventDefault();

        const txId = document.getElementById('formTxId').value;
        const date = document.getElementById('formDate').value;
        const partId = document.getElementById('formItem').value;
        const type = document.getElementById('formType').value;
        const qty = parseInt(document.getElementById('formQty').value);
        let note = document.getElementById('formNote').value;

        if (type === 'Warehouse Transfer') {
            const src = document.getElementById('formSrcWh').value;
            const dest = document.getElementById('formDestWh').value;
            note = `[${src} ➔ ${dest}] ${note}`;
        }

        fetch("{{ route('stock-movements.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tx_id: txId,
                date: date,
                part_id: partId,
                type: type,
                qty: qty,
                note: note
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchDashboardData();
                closeTransactionModal();
            } else {
                alert("Submission failed!");
            }
        })
        .catch(err => console.error("Error creating entry:", err));
    }

    function getAggregatedChartData() {
        const sortedHistory = [...globalTransactions].sort((a, b) => new Date(a.date) - new Date(b.date));
        const dateMap = {};
        
        sortedHistory.forEach(log => {
            dateMap[log.date] = (dateMap[log.date] || 0) + 1;
        });

        return {
            labels: Object.keys(dateMap),
            counts: Object.values(dateMap)
        };
    }

    function initChartVisualization() {
        const ctx = document.getElementById('activityChart').getContext('2d');
        const dataMetrics = getAggregatedChartData();

        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataMetrics.labels,
                datasets: [{
                    label: 'Transactions Tracked',
                    data: dataMetrics.counts,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    borderWidth: 2,
                    tension: 0.2,
                    fill: true,
                    pointBackgroundColor: '#1E3A8A'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#9CA3AF', font: { size: 10 } },
                        grid: { color: '#E5E7EB' }
                    },
                    x: {
                        ticks: { color: '#9CA3AF', font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }
</script>
@endsection
