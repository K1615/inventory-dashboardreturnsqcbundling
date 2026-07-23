@extends('layouts.warehouse')

@section('content')
<div class="min-h-screen flex flex-col md:flex-row w-full bg-gray-50 text-gray-800 font-sans">
    <!-- Meta Configuration Setup for Interfacing APIs inside layouts -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Left Navigation Sidebar -->
    <aside class="w-full md:w-64 bg-[#1E3A8A] text-white flex flex-col justify-between md:sticky md:top-0 md:h-screen shadow-xl z-20 shrink-0">
        <div class="flex flex-col">
            <div class="p-6 border-b border-white/10">
                <h1 class="text-lg font-bold tracking-wide text-white">ERP Inventory</h1>
                <p class="text-[10px] text-blue-200 uppercase tracking-widest mt-0.5">Management System</p>
            </div>
            
            @php
                // Get current route name
                $currentRoute = request()->route()->getName();
                // Get the requested tab from the URL, defaulting to 'dashboard'
                $currentTab = request()->query('tab', 'dashboard'); 
                
                // Define your active and inactive tailwind classes
                $activeClass = 'font-bold text-white bg-white/10 border-l-4 border-[#10B981] rounded-r-lg';
                $inactiveClass = 'font-semibold text-blue-100 border-l-4 border-transparent hover:text-white hover:bg-white/10 rounded-lg';
            @endphp

            <nav class="p-4 flex flex-col gap-1.5 overflow-y-auto flex-1">
                
                <!-- Dashboard -->
                <a href="{{ route('inventory.dashboard', ['tab' => 'dashboard']) }}" 
                onclick="if(typeof handleJsNav === 'function') handleJsNav(event, 'dashboard')" id="nav-dashboard" 
                class="nav-item px-4 py-2.5 text-xs transition-all {{ $currentRoute === 'inventory.dashboard' && $currentTab === 'dashboard' ? $activeClass : $inactiveClass }}">
                    Dashboard
                </a>
                
                <!-- Inventory Items -->
                <a href="{{ route('inventory.index') }}" 
                class="nav-item px-4 py-2.5 text-xs transition-all {{ $currentRoute === 'inventory.index' ? $activeClass : $inactiveClass }}">
                    Inventory Items
                </a>
                
                <!-- Stock Movements -->
                <a href="{{ route('stock-movements.index') }}" 
                class="nav-item px-4 py-2.5 text-xs transition-all {{ $currentRoute === 'stock-movements.index' ? $activeClass : $inactiveClass }}">
                    Stock Movements
                </a>
                
                <!-- Warehouse Layout -->
                <a href="{{ route('warehouse.layout') }}" 
                class="nav-item px-4 py-2.5 text-xs transition-all {{ $currentRoute === 'warehouse.layout' ? $activeClass : $inactiveClass }}">
                    Warehouse Layout
                </a>
                
                <!-- Alerts & Reorders (now its own page) -->
                <a href="{{ route('inventory.alerts') }}" id="nav-alerts" 
                class="nav-item px-4 py-2.5 text-xs transition-all flex items-center justify-between {{ $currentRoute === 'inventory.alerts' ? $activeClass : $inactiveClass }}">
                    <span>Alerts & Reorders</span>
                    <span id="nav-alerts-badge" class="hidden ml-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"></span>
                </a>
                
                <!-- Returns & QC -->
                <a href="{{ route('inventory.dashboard', ['tab' => 'returns']) }}" 
                onclick="if(typeof handleJsNav === 'function') handleJsNav(event, 'returns')" id="nav-returns" 
                class="nav-item px-4 py-2.5 text-xs transition-all {{ $currentRoute === 'inventory.dashboard' && $currentTab === 'returns' ? $activeClass : $inactiveClass }}">
                    Returns & QC
                </a>
                
                <!-- Product Bundling -->
                <a href="{{ route('inventory.dashboard', ['tab' => 'bundling']) }}" 
                onclick="if(typeof handleJsNav === 'function') handleJsNav(event, 'bundling')" id="nav-bundling" 
                class="nav-item px-4 py-2.5 text-xs transition-all {{ $currentRoute === 'inventory.dashboard' && $currentTab === 'bundling' ? $activeClass : $inactiveClass }}">
                    Product Bundling
                </a>
                
            </nav>
        </div>

        <div class="p-4 border-t border-white/10 bg-black/10 flex items-center justify-between text-sm font-semibold">
            <div class="flex items-center gap-2 text-xs text-blue-100">
                <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                <select id="currentUserSession" class="bg-transparent font-bold text-white focus:outline-none cursor-pointer">
                    <option value="Admin 1" class="text-gray-800">Admin 1</option>
                    <option value="Admin 2" class="text-gray-800">Admin 2</option>
                    <option value="Admin 3" class="text-gray-800">Admin 3</option>
                    <option value="Admin 4" class="text-gray-800">Admin 4</option>
                </select>
            </div>
            <button onclick="alert('Logging out...')" class="hover:text-red-300 text-white/80 transition-colors flex items-center gap-1 py-1 px-2 rounded hover:bg-white/5 text-xs">
                Logout
            </button>
        </div>
    </aside>

    <!-- Main Workspace Area -->
    <main class="flex-1 p-6 grid grid-cols-1 xl:grid-cols-4 gap-6 overflow-x-hidden">
        
        <!-- Left Side Control Column: Filters & Logs -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Quick Filters Card -->
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                    <h2 class="text-xs font-bold text-[#1E3A8A] uppercase tracking-wider">Quick Filters</h2>
                    <button onclick="resetFilters()" class="text-xs text-[#1E3A8A] hover:underline font-semibold">Clear All</button>
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Search Part Name</label>
                    <input type="text" id="searchInput" oninput="filterInventory()" placeholder="Type a part name..." class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-navyBlue/20 focus:border-[#1E3A8A] transition-all">
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Part Type</label>
                    <div class="flex flex-wrap gap-1" id="typeChips">
                        <button onclick="toggleChip('type', 'All')" id="type-all" class="chip text-[11px] px-2 py-1 rounded font-medium border bg-[#1E3A8A] text-white border-[#1E3A8A] transition-all">All</button>
                        <button onclick="toggleChip('type', 'Processor')" id="type-processor" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">CPU</button>
                        <button onclick="toggleChip('type', 'Graphics Card')" id="type-gpu" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">GPU</button>
                        <button onclick="toggleChip('type', 'Motherboard')" id="type-mobo" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">Motherboard</button>
                        <button onclick="toggleChip('type', 'Memory')" id="type-ram" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">RAM</button>
                        <button onclick="toggleChip('type', 'Storage')" id="type-storage" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">Storage</button>
                        <button onclick="toggleChip('type', 'Power Supply')" id="type-psu" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">Power Supply</button>
                        <button onclick="toggleChip('type', 'Case')" id="type-case" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">Case</button>
                        <button onclick="toggleChip('type', 'Cooler')" id="type-cooler" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">Cooler</button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Warehouse Location</label>
                    <div class="flex flex-col gap-1" id="warehouseChips">
                        <button onclick="toggleChip('warehouse', 'All')" id="wh-all" class="chip text-[11px] px-2 py-1 rounded font-medium border bg-[#1E3A8A] text-white border-[#1E3A8A] text-left transition-all">All Warehouses</button>
                        <!-- Dynamic Warehouse Chips will be injected here -->
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Storage Zone</label>
                    <div class="flex flex-wrap gap-1" id="zoneChips">
                        <button onclick="toggleChip('zone', 'All')" id="zone-all" class="chip text-[11px] px-2 py-1 rounded font-medium border bg-[#1E3A8A] text-white border-[#1E3A8A] transition-all">All Zones</button>
                        <!-- Dynamic Zone Chips will be injected here -->
                    </div>
                </div>
            </div>

            <!-- Audit History Log Panel -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-[#1E3A8A] text-xs tracking-wide uppercase">Audit / Movement Logs</h3>
                </div>
                <div class="p-2 max-h-[420px] overflow-y-auto font-mono text-[11px] divide-y divide-gray-100" id="transactionLogs">
                </div>
            </div>
        </div>

        <!-- Right Side Primary Columns: Graphs & Action Tables -->
        <div class="xl:col-span-3 space-y-6">
            
            <!-- CHARTS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 flex flex-col justify-between items-center h-72">
                    <div class="w-full flex justify-between items-center mb-2">
                        <h3 class="text-xs font-bold text-[#1E3A8A] uppercase tracking-wider">Warehouse Distribution</h3>
                        <button onclick="maximizeChart('pie')" class="text-[11px] text-blue-600 hover:underline font-medium">Expand Graph ↗</button>
                    </div>
                    <div class="w-full h-full relative flex justify-center items-center">
                        <canvas id="warehousePieChart" class="max-h-56"></canvas>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 flex flex-col justify-between h-72">
                    <div class="w-full flex justify-between items-center mb-2">
                        <h3 class="text-xs font-bold text-[#1E3A8A] uppercase tracking-wider">Transfers Velocity</h3>
                        <button onclick="maximizeChart('bar')" class="text-[11px] text-blue-600 hover:underline font-medium">Expand Graph ↗</button>
                    </div>
                    <div class="w-full h-full relative">
                        <canvas id="transBarGraph"></canvas>
                    </div>
                </div>
            </div>

            <!-- REQUESTS AND APPROVAL TABLE -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-amber-50/50 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <h3 class="font-bold text-amber-900 text-xs uppercase tracking-wide">Pending Stock Movement Requests</h3>
                    </div>
                    <span id="requestCount" class="text-[11px] bg-amber-600 text-white px-2.5 py-0.5 rounded-md font-bold">0 Active</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-wider font-bold border-b border-gray-200">
                                <th class="py-3 px-6">Requester</th>
                                <th class="py-3 px-6">Item Description</th>
                                <th class="py-3 px-6">Source</th>
                                <th class="py-3 px-6">Target</th>
                                <th class="py-3 px-6 text-center">Qty</th>
                                <th class="py-3 px-6 text-center">Planned Date</th>
                                <th class="py-3 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requestTableBody" class="text-xs text-gray-700 divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- INVENTORY LEVELS DISPLAY -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-[#1E3A8A] text-xs uppercase tracking-wide">Current Stock Levels</h3>
                    <div class="flex items-center gap-2">
                        <button onclick="openBatchModal()" class="text-xs font-bold bg-[#1E3A8A] hover:bg-blue-800 text-white px-3 py-1.5 rounded-lg shadow-sm transition-all">
                            + Create Batch Request
                        </button>
                        <span id="itemCount" class="text-[11px] bg-[#1E3A8A] text-white px-2.5 py-1 rounded-md font-bold">0 items found</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-wider font-bold border-b border-gray-200">
                                <th class="py-3 px-6">Item Description</th>
                                <th class="py-3 px-6">Warehouse</th>
                                <th class="py-3 px-6">Zone</th>
                                <th class="py-3 px-6 text-center">Available Stock</th>
                                <th class="py-3 px-6 text-center">Last Moved</th>
                                <th class="py-3 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryBody" class="text-xs text-gray-700 divide-y divide-gray-100">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- CHART MAXIMIZE MODAL -->
    <div id="chartMaximizeModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-md hidden flex-col justify-center items-center p-4 z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-3xl h-[65vh] flex flex-col justify-between shadow-2xl overflow-hidden">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-2">
                <h3 id="modalChartTitle" class="text-sm font-bold text-[#1E3A8A] uppercase tracking-wider">Expanded View</h3>
                <button onclick="closeChartModal()" class="text-gray-400 hover:text-gray-700 font-bold text-2xl">&times;</button>
            </div>
            <div class="flex-1 w-full h-full min-h-0 relative flex justify-center items-center">
                <canvas id="maximizedChartCanvas" class="max-w-full max-h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- WINDOW MODAL FORM (SINGLE MOVE) -->
    <div id="moveStockModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm hidden justify-end items-stretch z-50">
        <div class="w-full max-w-md bg-white shadow-2xl flex flex-col justify-between h-full">
            <div class="p-6 border-b border-gray-200 bg-[#1E3A8A] text-white flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider">Create Transfer Request</h3>
                </div>
                <button onclick="closeTransferModal()" class="text-white/70 hover:text-white text-xl font-bold">&times;</button>
            </div>
            <div class="p-6 flex-1 overflow-y-auto space-y-4">
                <input type="hidden" id="modalItemId">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Item Selected</label>
                    <input type="text" id="modalItemName" readonly class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-xs font-semibold text-gray-700 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Current Warehouse</label>
                        <input type="text" id="modalCurrentWh" readonly class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-xs text-gray-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-400 tracking-wider mb-1">Current Zone</label>
                        <input type="text" id="modalCurrentZone" readonly class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-xs text-gray-600 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1">Destination Warehouse</label>
                        <select id="modalDestWh" class="w-full border border-gray-200 bg-gray-50 rounded-lg p-2 text-xs focus:outline-none focus:border-[#1E3A8A]"></select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1">Destination Zone</label>
                        <select id="modalDestZone" class="w-full border border-gray-200 bg-gray-50 rounded-lg p-2 text-xs focus:outline-none focus:border-[#1E3A8A]">
                            <option value="Zone A">Zone A</option>
                            <option value="Zone B">Zone B</option>
                            <option value="Zone C">Zone C</option>
                            <option value="Zone D">Zone D</option>
                            <option value="Zone E">Zone E</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1">Quantity to Move</label>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                            <input type="number" id="modalDestQty" min="1" value="1" class="w-full p-2 text-xs text-center font-bold bg-transparent focus:outline-none">
                            <span id="modalMaxLabel" class="text-[10px] text-gray-400 font-medium pr-3 whitespace-nowrap"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-500 tracking-wider mb-1">Planned Date</label>
                        <input type="date" id="modalMoveDate" class="w-full border border-gray-200 bg-gray-50 rounded-lg p-2 text-xs focus:outline-none focus:border-[#1E3A8A]">
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex gap-3">
                <button onclick="closeTransferModal()" class="w-1/3 text-xs font-semibold py-2.5 text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-all">Cancel</button>
                <button onclick="submitTransferRequest()" class="w-2/3 text-xs font-bold py-2.5 text-white bg-[#10B981] hover:bg-[#059669] rounded-lg uppercase tracking-wider transition-all">Queue Request</button>
            </div>
        </div>
    </div>

    <!-- WINDOW MODAL FORM (BATCH MOVEMENTS) -->
    <div id="batchStockModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm hidden justify-center items-center p-4 z-50">
        <div class="w-full max-w-2xl bg-white shadow-2xl rounded-xl flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-gray-200 bg-[#1E3A8A] text-white flex justify-between items-center rounded-t-xl">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider">Create Batch Transfer Request</h3>
                    <p class="text-[11px] text-blue-200">Move multiple items out of a source warehouse together.</p>
                </div>
                <button onclick="closeBatchModal()" class="text-white hover:text-gray-200 text-xl font-bold">&times;</button>
            </div>
            <div class="p-6 flex-1 overflow-y-auto space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Source Warehouse</label>
                        <select id="batchSourceWh" onchange="loadBatchItems()" class="w-full border border-gray-200 bg-gray-50 rounded-lg p-2 text-xs focus:outline-none focus:border-[#1E3A8A]">
                            <option value="Main Warehouse">Main Warehouse</option>
                            <option value="North Branch">North Branch</option>
                            <option value="East Hub">East Hub</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Target Warehouse</label>
                        <select id="batchTargetWh" class="w-full border border-gray-200 bg-gray-50 rounded-lg p-2 text-xs focus:outline-none focus:border-[#1E3A8A]">
                            <option value="North Branch">North Branch</option>
                            <option value="Main Warehouse">Main Warehouse</option>
                            <option value="East Hub">East Hub</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Execution Date</label>
                        <input type="date" id="batchMoveDate" class="w-full border border-gray-200 bg-gray-50 rounded-lg p-2 text-xs focus:outline-none focus:border-[#1E3A8A]">
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 font-bold border-b border-gray-200 text-[10px] uppercase">
                                <th class="p-2.5 text-center w-12">Include</th>
                                <th class="p-2.5">Item Description</th>
                                <th class="p-2.5 text-center">Zone</th>
                                <th class="p-2.5 text-center">Available</th>
                                <th class="p-2.5 text-center w-28">Move Qty</th>
                                <th class="p-2.5">Target Zone</th>
                            </tr>
                        </thead>
                        <tbody id="batchItemsTableBody" class="divide-y divide-gray-100 text-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                <button onclick="closeBatchModal()" class="text-xs font-semibold px-4 py-2 text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-100">Cancel</button>
                <button onclick="submitBatchTransferRequest()" class="text-xs font-bold px-5 py-2 text-white bg-[#10B981] hover:bg-[#059669] rounded-lg uppercase tracking-wider shadow-sm">Queue Batch Request</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let inventoryData = [];
    let pendingRequests = [];
    let globalHistoryLogs = [];
    let globalWarehouses = [];
    let globalZones = [];

    let selectedTypeFilter = 'All';
    let selectedWarehouseFilter = 'All';
    let selectedZoneFilter = 'All';
    
    let pieChartInstance = null;
    let barGraphInstance = null;
    let maximizedChartInstance = null;

    const chipMapping = {
        type: { 
            'All': 'type-all', 
            'Processor': 'type-processor', 
            'Graphics Card': 'type-gpu', 
            'Motherboard': 'type-mobo',
            'Memory': 'type-ram',
            'Storage': 'type-storage', 
            'Power Supply': 'type-psu',
            'Case': 'type-case',
            'Cooler': 'type-cooler'
        },
        warehouse: { 'All': 'wh-all' }, // Cleared old hardcoded locations
        zone: { 'All': 'zone-all' }     // Cleared old hardcoded zones
    };

    // Global Fetch Wrapper incorporating CSRF Protection Headers
    async function apiFetch(url, options = {}) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        options.headers = {
            ...options.headers,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        };
        const response = await fetch(url, options);
        if (!response.ok) {
            const errData = await response.json();
            alert(errData.message || errData.error || 'Server error occurred.');
            throw new Error('API Error');
        }
        return response.json();
    }

    async function loadAllData() {
        try {
            const data = await apiFetch("{{ route('warehouse.layout.data') }}");
            
            inventoryData = data.inventory;
            pendingRequests = data.pendingRequests;
            globalHistoryLogs = data.historyLogs;
            
            // Assign the dynamically fetched layouts
            globalWarehouses = data.warehouses || [];
            globalZones = data.zones || [];
            
            // CRITICAL FIX: Actually execute the functions to draw the buttons and dropdowns
            renderDynamicFilters();
            populateModalDropdowns();
            
            filterInventory();
            renderRequestsTable();
            renderAuditLogs();
            refreshChartsData();
        } catch (e) { 
            console.error("Database connection failed", e); 
        }
    }

    function renderDynamicFilters() {
        const whContainer = document.getElementById('warehouseChips');
        const zoneContainer = document.getElementById('zoneChips');

        // Keep the 'All' buttons, remove old dynamically added ones
        whContainer.innerHTML = `<button onclick="toggleChip('warehouse', 'All')" id="wh-all" class="chip text-[11px] px-2 py-1 rounded font-medium border bg-[#1E3A8A] text-white border-[#1E3A8A] text-left transition-all">All Warehouses</button>`;
        zoneContainer.innerHTML = `<button onclick="toggleChip('zone', 'All')" id="zone-all" class="chip text-[11px] px-2 py-1 rounded font-medium border bg-[#1E3A8A] text-white border-[#1E3A8A] transition-all">All Zones</button>`;

        // Add dynamic Warehouse chips
        globalWarehouses.forEach((wh, index) => {
            const safeId = `wh-dyn-${index}`;
            chipMapping.warehouse[wh] = safeId; // Update chip mapping dynamically
            whContainer.innerHTML += `<button onclick="toggleChip('warehouse', '${wh}')" id="${safeId}" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 text-left transition-all">${wh}</button>`;
        });

        // Add dynamic Zone chips
        globalZones.forEach((zone, index) => {
            const safeId = `zone-dyn-${index}`;
            chipMapping.zone[zone] = safeId; // Update chip mapping dynamically
            zoneContainer.innerHTML += `<button onclick="toggleChip('zone', '${zone}')" id="${safeId}" class="chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">${zone}</button>`;
        });
        
        // Re-apply current active filters if they exist
        toggleChip('warehouse', selectedWarehouseFilter);
        toggleChip('zone', selectedZoneFilter);
    }

    function populateModalDropdowns() {
        // Find all dropdowns that need warehouse/zone data
        const destZoneSelect = document.getElementById('modalDestZone');
        const batchSourceWh = document.getElementById('batchSourceWh');
        const batchTargetWh = document.getElementById('batchTargetWh');

        if(destZoneSelect) destZoneSelect.innerHTML = globalZones.map(z => `<option value="${z}">${z}</option>`).join('');
        
        const whOptions = globalWarehouses.map(w => `<option value="${w}">${w}</option>`).join('');
        if(batchSourceWh) batchSourceWh.innerHTML = whOptions;
        if(batchTargetWh) batchTargetWh.innerHTML = whOptions;
    }

    function toggleChip(type, value) {
        if (type === 'type') selectedTypeFilter = value;
        if (type === 'warehouse') selectedWarehouseFilter = value;
        if (type === 'zone') selectedZoneFilter = value;

        Object.keys(chipMapping[type]).forEach(key => {
            const button = document.getElementById(chipMapping[type][key]);
            if (button) {
                if (key === value) {
                    button.className = "chip text-[11px] px-2 py-1 rounded font-medium border bg-[#1E3A8A] text-white border-[#1E3A8A] transition-all";
                } else {
                    button.className = "chip text-[11px] px-2 py-1 rounded font-medium border border-gray-200 bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all";
                }
            }
        });
        filterInventory();
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        toggleChip('type', 'All');
        toggleChip('warehouse', 'All');
        toggleChip('zone', 'All');
    }

    function filterInventory() {
        const searchVal = document.getElementById('searchInput').value.toLowerCase();
        const filtered = inventoryData.filter(item => {
            // Safety check to prevent JS crash if name is null
            const itemName = item.name ? item.name.toLowerCase() : '';
            const matchesSearch = itemName.includes(searchVal);
            
            // FIXED: Using item.category instead of item.type
            const matchesType = (selectedTypeFilter === 'All' || item.category === selectedTypeFilter);
            const matchesWh = (selectedWarehouseFilter === 'All' || item.warehouse === selectedWarehouseFilter);
            const matchesZone = (selectedZoneFilter === 'All' || item.zone === selectedZoneFilter);
            
            return matchesSearch && matchesType && matchesWh && matchesZone;
        });
        renderInventoryTable(filtered);
    }

    function formatDate(dateString) {
        if(!dateString) return 'N/A';
        const d = new Date(dateString);
        return `${String(d.getMonth()+1).padStart(2,'0')}/${String(d.getDate()).padStart(2,'0')}/${d.getFullYear()} ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
    }

    function renderRequestsTable() {
        const tbody = document.getElementById('requestTableBody');
        if (!tbody) return; // Safeguard if element is missing
        
        tbody.innerHTML = '';
        
        // Safeguard to ensure it reads the array length properly
        const count = Array.isArray(pendingRequests) ? pendingRequests.length : 0;
        const countElement = document.getElementById('requestCount');
        if (countElement) countElement.innerText = `${count} Active`;

        if (count === 0) return;

        pendingRequests.forEach(req => {
            const row = document.createElement('tr');
            row.className = "bg-amber-50/10 hover:bg-amber-50/30 transition-colors";
            
            // Added quotes around '${req.id}' and fallbacks (|| 'N/A') to prevent crashes
            row.innerHTML = `
                <td class="py-3 px-6 font-bold text-gray-600">${req.requester || 'System'}</td>
                <td class="py-3 px-6 font-semibold text-gray-900">${req.name || 'Unknown Item'}</td>
                <td class="py-3 px-6 text-[11px] text-gray-500"><strong>${req.from_wh || 'N/A'}</strong> <span class="text-gray-400">(${req.from_zone || 'N/A'})</span></td>
                <td class="py-3 px-6 text-[11px] text-gray-600"><strong>${req.to_wh || 'N/A'}</strong> <span class="text-gray-400">(${req.to_zone || 'N/A'})</span></td>
                <td class="py-3 px-6 text-center font-bold text-amber-700">${req.qty || 0} pcs</td>
                <td class="py-3 px-6 text-center font-mono text-gray-500">${req.planned_date || 'N/A'}</td>
                <td class="py-3 px-6 text-right">
                    <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-xs">
                        <button onclick="processApproval('${req.id}', 'approve')" class="px-2 py-1 text-[11px] font-bold text-[#10B981] hover:bg-[#10B981]/10 rounded">Approve</button>
                        <span class="text-gray-200 self-center">|</span>
                        <button onclick="processApproval('${req.id}', 'void')" class="px-2 py-1 text-[11px] font-bold text-[#EF4444] hover:bg-[#EF4444]/10 rounded">Void</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderInventoryTable(data) {
        const tbody = document.getElementById('inventoryBody');
        tbody.innerHTML = '';
        data.forEach(item => {
            const row = document.createElement('tr');
            row.className = `hover:bg-gray-50/80 transition-colors`;
            row.innerHTML = `
                <td class="py-3 px-6 font-semibold text-gray-900">${item.name}</td>
                <td class="py-3 px-6"><span class="px-2 py-0.5 bg-gray-100 border border-gray-200 rounded text-gray-600 font-medium">${item.warehouse}</span></td>
                <td class="py-3 px-6 text-gray-500 font-medium">${item.zone}</td>
                <td class="py-3 px-6 text-center font-bold text-navyBlue">${item.qty} pcs</td>
                <td class="py-3 px-6 text-center font-mono text-gray-400">${formatDate(item.last_moved)}</td>
                <td class="py-3 px-6 text-right">
                    <button onclick="openTransferModal('${item.id}')" class="text-xs font-semibold px-2.5 py-1 rounded-md bg-[#10B981] hover:bg-[#059669] text-white transition-all">Move Stock</button>
                </td>
            `;
            tbody.appendChild(row);
        });
        document.getElementById('itemCount').innerText = `${data.length} item(s) found`;
    }

    function renderAuditLogs() {
        const container = document.getElementById('transactionLogs');
        container.innerHTML = '';
        globalHistoryLogs.forEach(log => {
            const logRow = document.createElement('div');
            logRow.className = "p-3 flex flex-col gap-1 border-b border-gray-100 hover:bg-gray-50/60";
            
            let logBadge = log.type === "APPROVED" 
                ? `<span class="text-[#10B981] font-bold">[APPROVED]</span>` 
                : `<span class="text-[#EF4444] font-bold">[VOIDED]</span>`;

            logRow.innerHTML = `
                <div class="text-gray-700 text-xs">
                    ${logBadge} ${log.qty} pcs of <strong>${log.name}</strong>
                </div>
                <div class="text-gray-400 text-[10px] flex justify-between mt-0.5">
                    <span>${log.from_wh} → ${log.to_wh} (${log.zone})</span>
                    <span class="font-mono">${log.raw_date}</span>
                </div>
            `;
            container.appendChild(logRow);
        });
    }

    function openTransferModal(id) {
        const item = inventoryData.find(i => i.id === id);
        if (!item) return;

        document.getElementById('modalItemId').value = item.id;
        document.getElementById('modalItemName').value = item.name;
        document.getElementById('modalCurrentWh').value = item.warehouse;
        document.getElementById('modalCurrentZone').value = item.zone;
        document.getElementById('modalMaxLabel').innerText = `Max: ${item.qty} pcs`;
        document.getElementById('modalDestQty').max = item.qty;
        document.getElementById('modalDestQty').value = 1;
        document.getElementById('modalMoveDate').value = new Date().toISOString().split('T')[0];

        const destWhDropdown = document.getElementById('modalDestWh');
        destWhDropdown.innerHTML = '';
        
        // Use real database warehouses instead of the hardcoded array
        globalWarehouses.forEach(wh => {
            const option = document.createElement('option');
            option.value = wh;
            option.innerText = wh;
            if (wh === item.warehouse) option.disabled = true; // Can't move to same WH
            destWhDropdown.appendChild(option);
        });

        document.getElementById('moveStockModal').classList.replace('hidden', 'flex');
    }

    function closeTransferModal() {
        document.getElementById('moveStockModal').classList.replace('flex', 'hidden');
    }

    async function submitTransferRequest() {
        // REMOVED parseInt()
        const id = document.getElementById('modalItemId').value; 
        const sourceItem = inventoryData.find(i => i.id === id);
        const currentSelectedAdmin = document.getElementById('currentUserSession').value;
        const toWh = document.getElementById('modalDestWh').value;
        const toZone = document.getElementById('modalDestZone').value;
        const qty = parseInt(document.getElementById('modalDestQty').value);
        const moveDate = document.getElementById('modalMoveDate').value;

        if (!sourceItem || isNaN(qty) || qty <= 0 || qty > sourceItem.qty || !moveDate) {
            alert("Please check parameters.");
            return;
        }

        try {
            await apiFetch("{{ route('warehouse.layout.request') }}", {
                method: 'POST',
                body: JSON.stringify({
                    itemId: sourceItem.id,
                    requester: currentSelectedAdmin,
                    toWh: toWh,
                    toZone: toZone,
                    qty: qty,
                    date: moveDate
                })
            });
            closeTransferModal();
            loadAllData();
        } catch(e){}
    }

    function openBatchModal() {
        document.getElementById('batchMoveDate').value = new Date().toISOString().split('T')[0];
        loadBatchItems();
        document.getElementById('batchStockModal').classList.replace('hidden', 'flex');
    }

    function closeBatchModal() {
        document.getElementById('batchStockModal').classList.replace('flex', 'hidden');
    }

    function loadBatchItems() {
        const srcWh = document.getElementById('batchSourceWh').value;
        const tbody = document.getElementById('batchItemsTableBody');
        tbody.innerHTML = '';

        const matchingItems = inventoryData.filter(i => i.warehouse === srcWh);
        
        if (matchingItems.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-400">No parts found stored in this warehouse location.</td></tr>`;
            return;
        }

        matchingItems.forEach(item => {
            // Generate the dynamic zone options based on the global array
            const dynamicZoneOptions = globalZones.map(z => 
                `<option value="${z}" ${item.zone === z ? 'selected' : ''}>${z}</option>`
            ).join('');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="p-2 text-center"><input type="checkbox" value="${item.id}" class="batch-row-checkbox cursor-pointer"></td>
                <td class="p-2 font-medium text-gray-900">${item.name}</td>
                <td class="p-2 text-center text-gray-500">${item.zone}</td>
                <td class="p-2 text-center font-bold text-[#1E3A8A]">${item.qty} pcs</td>
                <td class="p-2 text-center"><input type="number" min="1" max="${item.qty}" value="1" id="batchQty-${item.id}" class="w-20 p-1 border border-gray-200 rounded text-center font-semibold"></td>
                <td class="p-2">
                    <select id="batchZone-${item.id}" class="border border-gray-200 rounded p-1 bg-gray-50 text-xs">
                        ${dynamicZoneOptions}
                    </select>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    async function submitBatchTransferRequest() {
        const srcWh = document.getElementById('batchSourceWh').value;
        const targetWh = document.getElementById('batchTargetWh').value;
        const moveDate = document.getElementById('batchMoveDate').value;
        const currentSelectedAdmin = document.getElementById('currentUserSession').value;

        if (srcWh === targetWh) {
            alert("Source and Target warehouses must be different locations.");
            return;
        }
        if (!moveDate) {
            alert("Please select a valid implementation date.");
            return;
        }

        const checkboxes = document.querySelectorAll('.batch-row-checkbox:checked');
        if (checkboxes.length === 0) {
            alert("Please choose at least one item.");
            return;
        }

        let packageItems = [];
        checkboxes.forEach(cb => {
            // REMOVED parseInt() 
            const itemId = cb.value; 
            const sourceItem = inventoryData.find(i => i.id === itemId);
            if (sourceItem) {
                const qty = parseInt(document.getElementById(`batchQty-${itemId}`).value);
                const toZone = document.getElementById(`batchZone-${itemId}`).value;
                if (qty > 0 && qty <= sourceItem.qty) {
                    packageItems.push({ id: itemId, qty: qty, toZone: toZone });
                }
            }
        });

        try {
            await apiFetch("{{ route('warehouse.layout.batch-request') }}", {
                method: 'POST',
                body: JSON.stringify({
                    srcWh: srcWh,
                    targetWh: targetWh,
                    date: moveDate,
                    requester: currentSelectedAdmin,
                    items: packageItems
                })
            });
            closeBatchModal();
            loadAllData();
        } catch(e){}
    }

    async function processApproval(requestId, status) {
        try {
            await apiFetch(`/warehouse-layout/process/${requestId}`, {
                method: 'POST',
                body: JSON.stringify({ status: status })
            });
            loadAllData();
        } catch(e){}
    }

    function maximizeChart(chartType) {
        const modal = document.getElementById('chartMaximizeModal');
        const modalTitle = document.getElementById('modalChartTitle');
        const maxCanvas = document.getElementById('maximizedChartCanvas');
        
        if (maximizedChartInstance) maximizedChartInstance.destroy();
        modal.classList.replace('hidden', 'flex');

        // Dynamically build the expanded chart totals
        const whTotals = {};
        globalWarehouses.forEach(wh => whTotals[wh] = 0);
        
        inventoryData.forEach(item => {
            if (whTotals[item.warehouse] !== undefined) {
                whTotals[item.warehouse] += item.qty;
            }
        });

        if (chartType === 'pie') {
            modalTitle.innerText = "Expanded Distribution Summary Profile";
            maximizedChartInstance = new Chart(maxCanvas, {
                type: 'pie',
                data: {
                    labels: Object.keys(whTotals),
                    datasets: [{ data: Object.values(whTotals), backgroundColor: ['#1E3A8A', '#10B981', '#3B82F6'] }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        } else {
            modalTitle.innerText = "Expanded Historic Activity Velocity";
            const barDataMap = {};
            globalHistoryLogs.filter(l => l.type === "APPROVED").forEach(t => {
                // Same fix as the inline chart — group by date only, not
                // the full timestamp, so this stays consistent with it.
                let dKey = (t.raw_date || "2026-07-11 00:00:00").slice(0, 10);
                barDataMap[dKey] = (barDataMap[dKey] || 0) + 1;
            });
            const sortedDates = Object.keys(barDataMap).sort();
            maximizedChartInstance = new Chart(maxCanvas, {
                type: 'bar',
                data: {
                    labels: sortedDates,
                    datasets: [{ label: 'Approved Actions', data: sortedDates.map(d => barDataMap[d]), backgroundColor: '#10B981' }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    }

    function closeChartModal() {
        document.getElementById('chartMaximizeModal').classList.replace('flex', 'hidden');
        if (maximizedChartInstance) maximizedChartInstance.destroy();
    }

    function refreshChartsData() {
        // Dynamically build the totals object based on actual warehouses
        const whTotals = {};
        globalWarehouses.forEach(wh => whTotals[wh] = 0);
        
        inventoryData.forEach(item => {
            if (whTotals[item.warehouse] !== undefined) {
                whTotals[item.warehouse] += item.qty;
            }
        });

        if (pieChartInstance) {
            pieChartInstance.data.datasets[0].data = Object.values(whTotals);
            pieChartInstance.update();
        } else {
            pieChartInstance = new Chart(document.getElementById('warehousePieChart'), {
                type: 'pie',
                data: {
                    labels: Object.keys(whTotals),
                    datasets: [{ data: Object.values(whTotals), backgroundColor: ['#1E3A8A', '#10B981', '#3B82F6'], borderWidth: 1 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 9 } } } } }
            });
        }

        const barDataMap = {};
        globalHistoryLogs.filter(l => l.type === "APPROVED").forEach(t => {
            // Group by date only (Y-m-d) — raw_date is a full 'Y-m-d H:i:s'
            // timestamp, and grouping by that meant almost every approved
            // transfer landed in its own bucket (nothing shares an exact
            // second), so the chart was really just one bar per transfer
            // instead of a daily volume trend.
            let dKey = (t.raw_date || "2026-07-11 00:00:00").slice(0, 10);
            barDataMap[dKey] = (barDataMap[dKey] || 0) + 1;
        });
        const sortedDates = Object.keys(barDataMap).sort();

        if (barGraphInstance) {
            barGraphInstance.data.labels = sortedDates;
            barGraphInstance.data.datasets[0].data = sortedDates.map(d => barDataMap[d]);
            barGraphInstance.update();
        } else {
            barGraphInstance = new Chart(document.getElementById('transBarGraph'), {
                type: 'bar',
                data: {
                    labels: sortedDates,
                    datasets: [{ data: sortedDates.map(d => barDataMap[d]), backgroundColor: '#10B981', borderRadius: 4 }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 9 } } }, x: { ticks: { font: { size: 9 } } } }, plugins: { legend: { display: false } } }
            });
        }
    }

    window.onload = () => {
        loadAllData();
    };
</script>
@endsection