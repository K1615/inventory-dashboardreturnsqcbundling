{{-- resources/views/inventory/submodule.blade.php --}}
@extends('layouts.app')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Dependencies -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Unified Configuration & Styling -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navyBlue: '#1E3A8A',     
                        emeraldGreen: '#10B981', 
                        white: '#FFFFFF',        
                        lightBg: '#F3F4F6'       
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-navy: #1E3A8A;
            --bg-emerald: #10B981;
        }
        .chart-grid-line { stroke: #f3f4f6; stroke-width: 1; stroke-dasharray: 4,4; }
        .chart-line-in { stroke: #1E3A8A; stroke-width: 3; fill: none; stroke-linecap: round; }
        .chart-line-out { stroke: #10B981; stroke-width: 3; fill: none; stroke-linecap: round; }
        
        /* Hide scrollbar for clean internal panels */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
@endsection

@section('content')
<div class="bg-gray-50 text-gray-800 font-sans flex flex-col md:flex-row min-h-screen overflow-hidden absolute inset-0 w-full">

    <!-- Unified Left Sidebar Navigation Layout -->
    <aside class="w-full md:w-64 bg-navyBlue text-white flex flex-col justify-between md:sticky md:top-0 md:h-screen shadow-xl z-20 shrink-0">
        <div class="flex flex-col h-full">
            <!-- Branding -->
            <div class="p-6 border-b border-white/10 shrink-0">
                <h1 class="text-lg font-bold tracking-wide text-white">ERP Inventory</h1>
                <p class="text-[10px] text-blue-200 uppercase tracking-widest mt-0.5">Management System</p>
            </div>
            
            <!-- Sidebar Navigation Links -->
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
                
                <!-- Alerts & Reorders -->
                <a href="{{ route('inventory.dashboard', ['tab' => 'alerts']) }}" 
                onclick="if(typeof handleJsNav === 'function') handleJsNav(event, 'alerts')" id="nav-alerts" 
                class="nav-item px-4 py-2.5 text-xs transition-all flex items-center justify-between {{ $currentRoute === 'inventory.dashboard' && $currentTab === 'alerts' ? $activeClass : $inactiveClass }}">
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

        <!-- User Profile Actions -->
        <div class="p-4 border-t border-white/10 bg-black/10 flex items-center justify-between text-sm font-semibold shrink-0">
            <a href="#" class="hover:text-blue-200 transition-colors flex items-center gap-2 py-1 px-2 rounded hover:bg-white/5 text-xs">
                <span class="w-2 h-2 rounded-full bg-emeraldGreen"></span>
                Admin Panel
            </a>
            <button onclick="alert('Logging out...')" class="hover:text-red-300 text-white/80 transition-colors flex items-center gap-1 py-1 px-2 rounded hover:bg-white/5 text-xs">
                Logout
            </button>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="flex-1 h-screen overflow-y-auto relative w-full">

        <!-- ========================================== -->
        <!-- VIEW 1: DASHBOARD MODULE                   -->
        <!-- ========================================== -->
        <section id="view-dashboard" class="app-view p-6 lg:p-8 max-w-7xl mx-auto w-full space-y-8 overflow-x-hidden">
            <div class="border-b border-gray-200 pb-4 flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Inventory Dashboard</h2>
                    <p class="text-sm text-gray-500">Overview of product distributions, system activities, and quick access links.</p>
                </div>
            </div>

            <!-- Dashboard ROW 1: Inventory Flow Line Graph -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                            <span>📦 Inventory Flow Trends</span>
                        </h3>
                        <p class="text-xs text-gray-400">Comparing inbound deliveries vs outbound shipments over the last 6 weeks</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 text-xs font-medium">
                            <span class="flex items-center gap-1"><span class="w-3 h-1 bg-navyBlue rounded-full inline-block"></span> Inbound</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-1 bg-emeraldGreen rounded-full inline-block"></span> Outbound</span>
                        </div>
                        <button onclick="expandDashboardPanel('flowGraph')" class="text-[11px] font-bold text-white bg-navyBlue hover:bg-blue-800 px-3 py-1.5 rounded transition-colors">
                            Expand Chart
                        </button>
                    </div>
                </div>

                <div class="w-full relative min-h-[220px] bg-gray-50/50 rounded-lg p-2 border border-gray-100">
                    <canvas id="dashFlowChart"></canvas>
                </div>
            </div>

            <!-- Dashboard ROW 2: Pie Graphs -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pie Graph 1: Component Categories -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Warehouse Locations</h3>
                            <p class="text-xs text-gray-400">Inventory split by component type</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="alert('Module in development')" class="text-[11px] font-semibold text-gray-600 hover:text-navyBlue bg-gray-100 px-2 py-1 rounded transition-colors">View Items</button>
                            <button onclick="expandDashboardPanel('categories')" class="text-[11px] font-bold text-white bg-navyBlue hover:bg-blue-800 px-2.5 py-1 rounded transition-colors">Expand</button>
                        </div>
                    </div>
                    
                    <div class="relative h-48 w-full py-2">
                        <canvas id="dashWarehouseChart"></canvas>
                    </div>
                </div>

                <!-- Pie Graph 2: Warehouse Placements -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Product Categories</h3>
                            <p class="text-xs text-gray-400">Inventory split by storage site</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="alert('Module in development')" class="text-[11px] font-semibold text-gray-600 hover:text-navyBlue bg-gray-100 px-2 py-1 rounded transition-colors">View Maps</button>
                            <button onclick="expandDashboardPanel('warehouses')" class="text-[11px] font-bold text-white bg-navyBlue hover:bg-blue-800 px-2.5 py-1 rounded transition-colors">Expand</button>
                        </div>
                    </div>

                    <div class="relative h-48 w-full py-2">
                        <canvas id="dashCategoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Dashboard ROW 3: System Logs & Stock Alerts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                <!-- System Log Entries Table -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">System Log Entries</h3>
                            <p class="text-xs text-gray-400">Detailed timeline of user actions and inventory adjustments</p>
                        </div>
                        <button onclick="expandDashboardPanel('systemLogs')" class="text-xs text-blue-600 hover:text-blue-800 font-bold bg-blue-50 px-3 py-1.5 rounded-md transition-colors">Expand Full Log</button>
                    </div>
                    
                    <div class="overflow-x-auto flex-1 max-h-[260px] overflow-y-auto border border-gray-100 rounded-lg">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-gray-100 text-gray-600 font-bold sticky top-0 uppercase tracking-wide">
                                <tr>
                                    <th class="p-3">User</th>
                                    <th class="p-3">Action Details</th>
                                    <th class="p-3 text-right">Date &amp; Time</th>
                                </tr>
                            </thead>
                            <tbody id="dashLogTableBody" class="divide-y divide-gray-100 text-gray-700 font-medium">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Dedicated Critical Alerts Center -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="border-b border-gray-100 pb-3 mb-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Critical Alerts</h3>
                        <p class="text-xs text-gray-400">Immediate inventory warnings based on unified database</p>
                    </div>

                    <div class="flex flex-col gap-5 flex-1 justify-center">
                        <div class="cursor-pointer bg-amber-50 hover:bg-amber-100 border border-amber-200 p-5 rounded-xl flex items-center justify-between transition-colors shadow-sm">
                            <div>
                                <div id="dash-alert-low" class="text-2xl font-bold text-amber-700">0</div>
                                <div class="text-xs text-amber-600 font-bold uppercase tracking-wide mt-1">Low Stock Items</div>
                            </div>
                            <span class="text-amber-500 font-bold text-3xl">⚠️</span>
                        </div>

                        <div class="cursor-pointer bg-red-50 hover:bg-red-100 border border-red-200 p-5 rounded-xl flex items-center justify-between transition-colors shadow-sm">
                            <div>
                                <div id="dash-alert-out" class="text-2xl font-bold text-red-700">0</div>
                                <div class="text-xs text-red-600 font-bold uppercase tracking-wide mt-1">Out of Stock Items</div>
                            </div>
                            <span class="text-red-500 font-bold text-3xl">🚨</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard ROW 4: Operations Control Desk -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 pb-3 mb-5">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Operations Control Desk</h3>
                    <p class="text-xs text-gray-400">Quick redirection routes to all major system workflows</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-navyBlue uppercase tracking-widest block">Master Catalog</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Inventory Items</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Add new products, update pricing models, and monitor catalog listings.</p>
                        </div>
                        <button onclick="window.location.href='{{ route('inventory.index') }}'" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-navyBlue hover:text-navyBlue text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            Open Catalog &rarr;
                        </button>
                    </div>
                    
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-emeraldGreen uppercase tracking-widest block">Logistics</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Stock Movements</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Record inbound deliveries, process shipments, and manage internal transfers.</p>
                        </div>
                        <button onclick="window.location.href='{{ route('stock-movements.index') }}'" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-emeraldGreen hover:text-emeraldGreen text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            Track Movements &rarr;
                        </button>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-navyBlue uppercase tracking-widest block">Facilities</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Warehouse Layout</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Locate items on the floorplan map and optimize physical storage space zones.</p>
                        </div>
                        <button onclick="window.location.href='{{ route('warehouse.layout') }}'" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-navyBlue hover:text-navyBlue text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            View Floorplans &rarr;
                        </button>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest block">Procurement</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Alerts & Reorders</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Review critical low-stock metrics and generate automated vendor purchase orders.</p>
                        </div>
                        <button onclick="handleJsNav(event, 'alerts')" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-amber-600 hover:text-amber-700 text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            Manage Orders &rarr;
                        </button>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-emeraldGreen uppercase tracking-widest block">Quality Assurance</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Returns & QC Hub</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Handle customer return requests (RMAs) and verify defective parts testing.</p>
                        </div>
                        <button onclick="handleJsNav(event, 'returns')" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-emeraldGreen hover:text-emeraldGreen text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            Process Returns &rarr;
                        </button>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-navyBlue uppercase tracking-widest block">Sales Packaging</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Product Bundling</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Combine individual components into complete PC builds or promotional sets.</p>
                        </div>
                        <button onclick="handleJsNav(event, 'bundling')" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-navyBlue hover:text-navyBlue text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            Configure Bundles &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dashboard ROW 5: Directory Overview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 bg-gray-50/70 flex flex-col md:flex-row items-center gap-4 justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Products Directory Overview</h3>
                            <button onclick="alert('Module in development')" class="text-[11px] font-bold text-white bg-emeraldGreen hover:bg-emerald-600 px-3 py-1.5 rounded-lg shadow-sm transition-colors flex items-center gap-1">
                                Full Page &rarr;
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Live lookup of inventory using text and category filters</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                        <input type="text" id="dashDirSearch" oninput="runDashDirectoryFiltering()" placeholder="Search components..." class="w-full sm:w-56 px-3 py-2 text-xs bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-navyBlue shadow-sm" />
                        <select id="dashDirCategory" onchange="runDashDirectoryFiltering()" class="w-full sm:w-40 px-3 py-2 text-xs bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-navyBlue shadow-sm">
                            <option value="All">All Categories</option>
                            <option value="CPU">CPUs</option>
                            <option value="GPU">Graphics Cards</option>
                            <option value="RAM">Memory (RAM)</option>
                            <option value="Storage">Storage Drives</option>
                            <option value="Power Supply">Power Supplies</option>
                            <option value="Motherboard">Motherboards</option>
                            <option value="Case">Cases</option>
                            <option value="Cooler">Coolers</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[350px] overflow-y-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-100/80 border-b border-gray-200 text-gray-600 font-bold uppercase sticky top-0">
                                <th class="p-3.5">Part ID</th>
                                <th class="p-3.5">Component Details</th>
                                <th class="p-3.5">Category</th>
                                <th class="p-3.5 text-right">Available Stock</th>
                                <th class="p-3.5 text-right">Unit Price</th>
                                <th class="p-3.5 text-center">Status Badge</th>
                            </tr>
                        </thead>
                        <tbody id="dashDirectoryTableBody" class="divide-y divide-gray-100 text-gray-700 font-medium">
                            <!-- Injected -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>


        <!-- ========================================== -->
        <!-- VIEW 2: RETURNS & QC MODULE                -->
        <!-- ========================================== -->
        <section id="view-returns" class="app-view hidden p-6 lg:p-8 max-w-7xl mx-auto w-full space-y-8 overflow-x-hidden">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-bold text-navyBlue">Quality Control & Return Operations</h2>
                    <p class="text-sm text-gray-500 mt-1">Monitor return frequencies, manage localized product evaluations, and process manufacturer returns.</p>
                </div>
            </div>

            <!-- Returns Performance Graph -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-navyBlue">Return Frequency Overview</h3>
                    <p class="text-xs text-gray-500">6-Month historical comparison: Internal Warehouse/User Returns vs. Manufacturer (RMA) Returns.</p>
                </div>
                <div class="h-64 w-full relative">
                    <canvas id="returnsChart"></canvas>
                </div>
            </div>

            <!-- Returns SECTION 1: Dual Ticketing Systems (Forms) -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <!-- Ticket System A: Internal QC -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col">
                    <div class="border-b border-gray-100 pb-3 mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-navyBlue">1. QC Inspection Ticketing</h3>
                        <span class="bg-blue-50 text-navyBlue text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">Internal Routing</span>
                    </div>
                    <form id="formInspection" onsubmit="handleInspectionSubmit(event)" class="space-y-4 flex-1">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Select Item Under Inspection</label>
                            <select id="insItem" required class="w-full border border-gray-300 rounded-md p-2 text-sm focus:border-navyBlue outline-none bg-white">
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Source / Origin</label>
                                <select id="insSource" required class="w-full border border-gray-300 rounded-md p-2 text-sm focus:border-navyBlue outline-none bg-white">
                                    <option value="Customer Aftersales">Customer Aftersales Return</option>
                                    <option value="Warehouse Transfer">Warehouse Transfer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Operator</label>
                                <select id="insOp" required class="w-full border border-gray-300 rounded-md p-2 text-sm focus:border-navyBlue outline-none bg-white">
                                    <option value="admin1">admin1</option>
                                    <option value="admin2">admin2</option>
                                    <option value="admin3">admin3</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Validation Outcome</label>
                            <select id="insOutcome" required class="w-full border border-gray-300 rounded-md p-2 text-sm focus:border-navyBlue outline-none bg-white">
                                <option value="Good - Clear for Restock">Good - Clear for Restock (Adds to Inventory)</option>
                                <option value="Good - Open Box Release">Good - Open Box Release (Adds to Inventory)</option>
                                <option value="Damaged - Move to Quarantine">Damaged - Move to Quarantine Zone</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-navyBlue text-white font-bold py-2.5 rounded-md hover:bg-blue-900 transition-colors mt-2 text-sm">
                            Submit to Inspection Approvals
                        </button>
                    </form>
                </div>

                <!-- Ticket System B: Manufacturer Returns (RMA) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col">
                    <div class="border-b border-gray-100 pb-3 mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-navyBlue">2. Manufacturer Return (RMA) Ticketing</h3>
                        <span class="bg-emeraldGreen/10 text-emeraldGreen text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">External Vendor</span>
                    </div>
                    <form id="formRMA" onsubmit="handleRmaSubmit(event)" class="space-y-4 flex-1">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Select Quarantined Item</label>
                            <select id="rmaItem" required class="w-full border border-gray-300 rounded-md p-2 text-sm focus:border-navyBlue outline-none bg-white">
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Primary Reasons for Return</label>
                            <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 border border-gray-200 rounded-md">
                                <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                    <input type="checkbox" value="Dead on Arrival (DOA)" class="rma-reason w-3.5 h-3.5 text-navyBlue"> Dead on Arrival (DOA)
                                </label>
                                <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                    <input type="checkbox" value="Physical Defect" class="rma-reason w-3.5 h-3.5 text-navyBlue"> Physical Defect
                                </label>
                                <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                    <input type="checkbox" value="Missing Retail Box" class="rma-reason w-3.5 h-3.5 text-navyBlue"> Missing Retail Box
                                </label>
                                <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                                    <input type="checkbox" value="Failed QC Bench Test" class="rma-reason w-3.5 h-3.5 text-navyBlue"> Failed QC Bench Test
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Target Manufacturer</label>
                                <input type="text" id="rmaVendor" required placeholder="e.g. ASUS, Corsair" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:border-navyBlue outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Operator</label>
                                <select id="rmaOp" required class="w-full border border-gray-300 rounded-md p-2 text-sm focus:border-navyBlue outline-none bg-white">
                                    <option value="admin1">admin1</option>
                                    <option value="admin2">admin2</option>
                                    <option value="admin3">admin3</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-navyBlue text-white font-bold py-2.5 rounded-md hover:bg-blue-900 transition-colors mt-2 text-sm">
                            Submit to RMA Approvals
                        </button>
                    </form>
                </div>
            </div>

            <!-- Returns SECTION 2: Approval Queues -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-[350px]">
                    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center rounded-t-xl">
                        <div>
                            <h3 class="font-bold text-navyBlue text-sm">Pending Inspection Approvals</h3>
                            <p class="text-[10px] text-gray-500">Awaiting management authorization for internal routing.</p>
                        </div>
                        <span class="bg-navyBlue text-white text-xs font-bold px-2 py-1 rounded-full" id="countIns">0</span>
                    </div>
                    <div class="overflow-y-auto flex-1 p-0">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-white sticky top-0 shadow-sm z-10">
                                <tr class="text-[10px] text-gray-400 uppercase tracking-wide">
                                    <th class="px-4 py-2 font-semibold">Details</th>
                                    <th class="px-4 py-2 font-semibold">Proposed Action</th>
                                    <th class="px-4 py-2 font-semibold text-center">Decision</th>
                                </tr>
                            </thead>
                            <tbody id="tableApproveIns" class="divide-y divide-gray-100"></tbody>
                        </table>
                        <div id="emptyApproveIns" class="hidden p-6 text-center text-gray-400 text-xs">No pending inspection requests.</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-[350px]">
                    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center rounded-t-xl">
                        <div>
                            <h3 class="font-bold text-navyBlue text-sm">Pending RMA Approvals</h3>
                            <p class="text-[10px] text-gray-500">Awaiting management authorization to return to manufacturer.</p>
                        </div>
                        <span class="bg-navyBlue text-white text-xs font-bold px-2 py-1 rounded-full" id="countRma">0</span>
                    </div>
                    <div class="overflow-y-auto flex-1 p-0">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-white sticky top-0 shadow-sm z-10">
                                <tr class="text-[10px] text-gray-400 uppercase tracking-wide">
                                    <th class="px-4 py-2 font-semibold">Details</th>
                                    <th class="px-4 py-2 font-semibold">Return Reasons</th>
                                    <th class="px-4 py-2 font-semibold text-center">Decision</th>
                                </tr>
                            </thead>
                            <tbody id="tableApproveRma" class="divide-y divide-gray-100"></tbody>
                        </table>
                        <div id="emptyApproveRma" class="hidden p-6 text-center text-gray-400 text-xs">No pending RMA requests.</div>
                    </div>
                </div>
            </div>

            <!-- Returns SECTION 3: Finalized Audit Log -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
                <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="font-bold text-navyBlue text-lg">Centralized Audit Log</h3>
                        <p class="text-xs text-gray-500">Historical archive of localized material lifecycle mutations and status overrides.</p>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <input type="text" id="searchReturnsLog" onkeyup="filterReturnsLogs()" placeholder="Search product or operator..." class="px-3 py-2 border border-gray-300 rounded-md text-sm w-full md:w-56 focus:border-navyBlue outline-none">
                        <select id="filterLogStream" onchange="filterReturnsLogs()" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-navyBlue outline-none bg-white">
                            <option value="All">All Streams</option>
                            <option value="Inspection">Inspection</option>
                            <option value="RMA">Manufacturer RMA</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-navyBlue text-white">
                            <tr class="text-xs uppercase tracking-wide">
                                <th class="px-6 py-4 font-semibold">Log Timestamp</th>
                                <th class="px-6 py-4 font-semibold">Operator</th>
                                <th class="px-6 py-4 font-semibold">Stream</th>
                                <th class="px-6 py-4 font-semibold">Product Name & Request Info</th>
                                <th class="px-6 py-4 font-semibold text-right">Final Outcome</th>
                            </tr>
                        </thead>
                        <tbody id="tableReturnsAudit" class="divide-y divide-gray-100"></tbody>
                    </table>
                    <div id="emptyReturnsAudit" class="hidden p-8 text-center text-gray-400 text-sm">No records match your filters.</div>
                </div>
            </div>
        </section>


        <!-- ========================================== -->
        <!-- VIEW 3: PRODUCT BUNDLING MODULE            -->
        <!-- ========================================== -->
        <section id="view-bundling" class="app-view hidden p-6 lg:p-8 max-w-7xl mx-auto w-full overflow-x-hidden">
            <header class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-navyBlue">Build & Assembly Station</h2>
                    <p class="text-sm text-gray-500 mt-1">Create custom builds or select pre-built packages to request for approval.</p>
                </div>
                
                <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm flex items-center gap-3">
                    <span class="text-sm font-bold text-navyBlue">Current User:</span>
                    <select id="userSelector" class="border border-gray-300 rounded text-sm px-2 py-1 focus:outline-none focus:border-emeraldGreen bg-white">
                        <option value="Admin 1">Admin 1</option>
                        <option value="Admin 2">Admin 2</option>
                        <option value="Admin 3">Admin 3</option>
                    </select>
                </div>
            </header>

            <!-- Bundling Top Section: Custom Builder & Presets -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Left Side: Custom PC Builder -->
                <div id="customBuilderSection" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-navyBlue">Custom PC Builder</h3>
                        <p class="text-sm text-gray-500">Select individual parts to build a custom computer.</p>
                    </div>
                    
                    <form id="customBuilderForm" class="flex-1 space-y-4">
                        <div id="customSelectsContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
                    </form>

                    <div class="mt-6 pt-4 border-t border-gray-100 flex gap-2">
                        <button id="clearCustomBtn" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-md transition-colors text-sm">
                            Clear Form
                        </button>
                        <button onclick="handleReviewCustomBuild(event)" id="reviewCustomBtn" class="flex-[2] bg-emeraldGreen hover:bg-green-600 text-white font-bold py-3 px-4 rounded-md transition-colors shadow-sm">
                            Review Custom Build
                        </button>
                    </div>
                </div>

                <!-- Right Side: Pre-Built Packages -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-navyBlue">Pre-Built Packages</h3>
                        <p class="text-sm text-gray-500">Quickly select preset computer builds for approval.</p>
                    </div>
                    
                    <div id="presetsContainer" class="flex-1 space-y-4 overflow-y-auto pr-2 max-h-96">
                        <!-- Preset Cards generated dynamically -->
                    </div>
                </div>
            </div>

            <!-- Bundling Middle Section: Audit Log & Approvals -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Left Side: Audit & Activity Log -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col">
                    <h3 class="text-lg font-bold text-navyBlue mb-1">Audit & Activity Log</h3>
                    <p class="text-sm text-gray-500 mb-4">History of processed build requests.</p>
                    
                    <div class="overflow-y-auto flex-1 max-h-64 border border-gray-100 rounded">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="sticky top-0 bg-gray-50">
                                <tr class="text-gray-600 border-b border-gray-200">
                                    <th class="py-2 px-3 font-semibold">Date/Time</th>
                                    <th class="py-2 px-3 font-semibold">Details</th>
                                    <th class="py-2 px-3 font-semibold">Req / Appr</th>
                                    <th class="py-2 px-3 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody id="bundlingAuditTableBody" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Side: Request/Approval Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col border-t-4 border-t-emeraldGreen">
                    <h3 class="text-lg font-bold text-navyBlue mb-1">Pending Requests (Approvals)</h3>
                    <p class="text-sm text-gray-500 mb-4">Review and approve requests to deduct stock.</p>
                    
                    <div class="overflow-y-auto flex-1 max-h-64 border border-gray-100 rounded">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="sticky top-0 bg-gray-50">
                                <tr class="text-gray-600 border-b border-gray-200">
                                    <th class="py-2 px-3 font-semibold">Requester</th>
                                    <th class="py-2 px-3 font-semibold">Date/Time</th>
                                    <th class="py-2 px-3 font-semibold">Details</th>
                                    <th class="py-2 px-3 font-semibold text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="bundlingApprovalTableBody" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bundling Bottom Section: Inventory Table Context -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
                    <h3 class="text-lg font-bold text-navyBlue">Live Parts Inventory</h3>
                    
                    <div class="flex gap-3">
                        <select id="bundleCategoryFilter" class="border border-gray-300 rounded-md text-sm px-3 py-2 focus:outline-none focus:border-navyBlue bg-white">
                            <option value="All">All Categories</option>
                            <option value="CPU">CPU</option>
                            <option value="GPU">GPU</option>
                            <option value="Motherboard">Motherboard</option>
                            <option value="RAM">RAM</option>
                            <option value="Storage">Storage</option>
                            <option value="Power Supply">Power Supply</option>
                            <option value="Case">Case</option>
                            <option value="Cooler">Cooler</option>
                        </select>
                        <input type="text" id="bundleSearchInput" placeholder="Search parts..." class="border border-gray-300 rounded-md text-sm px-3 py-2 focus:outline-none focus:border-navyBlue w-48">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="sticky top-0">
                            <tr class="bg-navyBlue text-white">
                                <th class="py-3 px-4 rounded-tl-md">Part ID</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4 rounded-tr-md text-right">Available Stock</th>
                            </tr>
                        </thead>
                        <tbody id="bundlingInventoryTableBody" class="divide-y divide-gray-100 text-gray-700"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        @include('inventory.alerts-reorders')

    <!-- ========================================== -->
    <!-- GLOBAL MODALS OVERLAYS                     -->
    <!-- ========================================== -->

    <!-- Dashboard: Logic Expansion Viewer Modal -->
    <div id="dashExpansionModal" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center z-[60] p-4 transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] flex flex-col overflow-hidden border border-gray-100 transform scale-95 transition-transform duration-200">
            <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 id="dashModalDisplayTitle" class="text-sm font-bold text-navyBlue uppercase tracking-wider">Detailed Panel View</h3>
                <button onclick="closeDashModalWindow()" class="text-gray-400 hover:text-gray-600 font-bold text-xl leading-none">&times;</button>
            </div>
            <div id="dashModalDisplayContent" class="p-6 overflow-y-auto flex-1 text-xs"></div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button onclick="closeDashModalWindow()" class="px-5 py-2.5 bg-navyBlue text-white text-xs font-bold rounded-lg hover:bg-blue-800 transition-colors">Close Window</button>
            </div>
        </div>
    </div>

    <!-- Bundling: Preset Confirmation Modal -->
    <div id="presetModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden">
            <div class="bg-navyBlue px-6 py-4 flex justify-between items-center">
                <h3 id="modalPresetTitle" class="text-lg font-bold text-white">Preset Details</h3>
                <button class="closeModalBtn text-gray-300 hover:text-white transition-colors" data-target="presetModal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <p id="modalPresetDesc" class="text-sm text-gray-500 mb-4"></p>
                <div class="bg-gray-50 rounded border border-gray-200 p-4 mb-6">
                    <h4 class="font-bold text-gray-700 text-sm mb-2 border-b pb-1">Included Parts:</h4>
                    <ul id="modalPresetPartsList" class="space-y-2 text-sm text-gray-600"></ul>
                </div>
                <div class="flex items-center justify-between mb-6">
                    <span class="text-sm font-semibold text-gray-700">Can currently build:</span>
                    <span id="modalPresetMaxBuild" class="text-2xl font-bold text-navyBlue">0</span>
                </div>
                <div class="flex gap-3">
                    <button class="closeModalBtn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-md transition-colors text-sm" data-target="presetModal">Cancel</button>
                    <button id="customizePresetBtn" class="flex-1 bg-navyBlue hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-md transition-colors text-sm">Customize</button>
                    <button id="confirmPresetPurchaseBtn" class="flex-[1.5] bg-emeraldGreen hover:bg-green-600 text-white font-bold py-3 px-4 rounded-md transition-colors text-sm">Submit Request</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bundling: Custom Build Confirmation Modal -->
    <div id="customConfirmModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden">
            <div class="bg-navyBlue px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Review Custom Build</h3>
                <button class="closeModalBtn text-gray-300 hover:text-white transition-colors" data-target="customConfirmModal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-500 mb-4">Please review the parts selected before submitting the request.</p>
                <div class="bg-gray-50 rounded border border-gray-200 p-4 mb-6">
                    <h4 class="font-bold text-gray-700 text-sm mb-2 border-b pb-1">Selected Parts:</h4>
                    <ul id="customConfirmPartsList" class="space-y-2 text-sm text-gray-600"></ul>
                </div>
                <div class="flex gap-3 mt-6">
                    <button class="closeModalBtn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-md transition-colors text-sm" data-target="customConfirmModal">Cancel</button>
                    <button id="placeCustomOrderBtn" class="flex-1 bg-emeraldGreen hover:bg-green-600 text-white font-bold py-3 px-4 rounded-md transition-colors text-sm">Submit Request</button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- UNIFIED APPLICATION JAVASCRIPT LOGIC       -->
<!-- ========================================== -->
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken };
    
    // Core Application State injected from Controller
    let appState = @json($initialData);
    
    // Exact mapping to the database seeder IDs
    const bundlingPresets = [
        { id: 'preset1', name: 'High-End Intel Gaming', desc: 'Top tier 1440p/4K gaming performance.', recipe: ['P001', 'P003', 'P009', 'P005', 'P007', 'P011', 'P013', 'P015'] },
        { id: 'preset2', name: 'AMD Value Powerhouse', desc: 'Excellent price-to-performance ratio for gaming and work.', recipe: ['P002', 'P004', 'P010', 'P006', 'P008', 'P012', 'P014'] },
        { id: 'preset3', name: 'Budget Starter PC', desc: 'Affordable entry-level 1080p gaming and productivity build.', recipe: ['P016', 'P017', 'P018', 'P019', 'P008', 'P020', 'P013'] }
    ];

    let bundlingSelectedPresetId = null;
    let bundlingPendingCustomIds = [];

    // Core Sync Function
    async function syncState() {
        try {
            const res = await fetch('/inventory/api/state');
            appState = await res.json();
            refreshAllUI();
        } catch (error) {
            console.error("Error syncing state:", error);
        }
    }

    function routeTo(viewName) {
        document.querySelectorAll('.app-view').forEach(v => v.classList.add('hidden'));
        document.querySelectorAll('.nav-item').forEach(n => { 
            n.classList.remove('bg-white/10', 'border-emeraldGreen', 'text-white', 'font-bold'); 
            n.classList.add('text-blue-100', 'border-transparent', 'font-semibold'); 
        });
        
        document.getElementById(`view-${viewName}`).classList.remove('hidden');
        
        const activeNav = document.getElementById(`nav-${viewName}`);
        activeNav.classList.remove('text-blue-100', 'border-transparent', 'font-semibold'); 
        activeNav.classList.add('bg-white/10', 'border-emeraldGreen', 'text-white', 'font-bold');
        
        refreshAllUI();
    }

    function refreshAllUI() {
        runDashDirectoryFiltering();
        updateDashCalculatedGauges();
        renderDashLogTables();
        renderReturnsDropdowns();
        renderReturnsPendingInspections();
        renderReturnsPendingRMAs();
        renderReturnsAuditLog();
        
        // Add this line right here:
        if (typeof updateReturnsChart === 'function') updateReturnsChart();

        renderDashboardCharts(); // <--- ADD THIS LINE HERE
        
        renderBundlingTable();
        renderBundlingCustomBuilder();
        renderBundlingPresets();
        renderBundlingApprovalTable();
        renderBundlingAuditTable();
        alertsRenderAll();
        renderNavAlertsBadge();
    }

    // Lights up a red count badge on the "Alerts & Reorders" nav item
    // whenever there's an active (unacknowledged) or acknowledged-but-
    // unresolved stock alert, so a low/out-of-stock situation is visible
    // from anywhere in the app, not just when you're on that tab.
    function renderNavAlertsBadge() {
        const badge = document.getElementById('nav-alerts-badge');
        const openAlerts = (appState.stockAlerts || []).filter(a => a.status === 'active' || a.status === 'acknowledged');

        if (openAlerts.length === 0) {
            badge.classList.add('hidden');
            badge.textContent = '';
            return;
        }

        const hasCritical = openAlerts.some(a => a.severity === 'critical');
        badge.textContent = openAlerts.length;
        badge.classList.remove('hidden');
        badge.classList.toggle('bg-red-500', hasCritical);
        badge.classList.toggle('bg-amber-500', !hasCritical);
    }

    // ==========================================
    // DASHBOARD LOGIC
    // ==========================================
    let dashCatChart = null;
    let dashWhChart = null;
    let dashFlowChart = null;

    function renderDashboardCharts() {
        const inventory = appState.inventory || [];
        
        // 1. Tally up Categories
        const catData = {};
        // 2. Tally up Warehouses (Default to 'Main Hub' if undefined)
        const whData = {};

        inventory.forEach(item => {
            const qty = parseInt(item.qty) || 0;
            if (qty > 0) {
                const cat = item.category || 'Uncategorized';
                const wh = item.warehouse || 'Main Hub';
                
                catData[cat] = (catData[cat] || 0) + qty;
                whData[wh] = (whData[wh] || 0) + qty;
            }
        });

        // --- Category Pie Chart ---
        if (dashCatChart) dashCatChart.destroy();
        dashCatChart = new Chart(document.getElementById('dashCategoryChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(catData),
                datasets: [{
                    data: Object.values(catData),
                    backgroundColor: ['#1E3A8A', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#3B82F6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } } },
                cutout: '60%'
            }
        });

        // --- Warehouse Pie Chart ---
        if (dashWhChart) dashWhChart.destroy();
        dashWhChart = new Chart(document.getElementById('dashWarehouseChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(whData),
                datasets: [{
                    data: Object.values(whData),
                    backgroundColor: ['#1E3A8A', '#10B981', '#6B7280'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } } },
                cutout: '60%'
            }
        });

        // --- Inventory Flow Chart (Live Data Integration) ---
        if (dashFlowChart) dashFlowChart.destroy();
        
        const dynamicDates = [];
        const inboundCounts = [0, 0, 0, 0, 0, 0];
        const outboundCounts = [0, 0, 0, 0, 0, 0];
        
        // 1. Generate dates starting from TODAY going forward 5 days (6 days total)
        for (let i = 0; i <= 5; i++) {
            const d = new Date();
            d.setDate(d.getDate() + i);
            dynamicDates.push(d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        }

        // 2. FETCH THE LIVE DATA DIRECTLY FROM THE ENDPOINT
        fetch("{{ route('stock-movements.data') }}")
            .then(res => res.json())
            .then(data => {
                const movements = data.movements || [];
                
                movements.forEach(movement => {
                    // Only count 'Approved' transactions on the dashboard chart
                    if (movement.status !== 'Approved') return;

                    const rawDate = movement.date || movement.created_at;
                    const movementDateStr = new Date(rawDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    
                    const dateIndex = dynamicDates.indexOf(movementDateStr);
                    
                    if (dateIndex !== -1) {
                        const type = (movement.type || '').toLowerCase();
                        const qty = parseInt(movement.qty) || 0;

                        if (type === 'stock-in' || type === 'product return') {
                            inboundCounts[dateIndex] += qty;
                        } else if (type === 'stock-out' || type === 'warehouse transfer') {
                            outboundCounts[dateIndex] += qty;
                        }
                    }
                });

                // 3. Render the chart INSIDE the fetch block so it waits for the data
                dashFlowChart = new Chart(document.getElementById('dashFlowChart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: dynamicDates,
                        datasets: [
                            { label: 'Inbound', data: inboundCounts, borderColor: '#1E3A8A', tension: 0.3, borderWidth: 3 },
                            { label: 'Outbound', data: outboundCounts, borderColor: '#10B981', tension: 0.3, borderWidth: 3 }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { 
                            y: { display: true, beginAtZero: true, suggestedMax: 10, ticks: { stepSize: 2 } }, 
                            x: { grid: { display: false } } 
                        }
                    }
                });
            })
            .catch(err => console.error("Chart Fetch Error:", err));
    }

    function runDashDirectoryFiltering() {
        const query = document.getElementById('dashDirSearch').value.toLowerCase().trim();
        const cat = document.getElementById('dashDirCategory').value;
        const filtered = (appState.inventory || []).filter(item => {
            const ms = (item.name || '').toLowerCase().includes(query) || (item.id || '').toLowerCase().includes(query);
            const mc = (cat === "All") || (item.category === cat);
            return ms && mc;
        });
        
        const tbody = document.getElementById('dashDirectoryTableBody');
        tbody.innerHTML = '';
        
        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-gray-400 font-medium bg-gray-50/50">No components matched search parameters.</td></tr>`;
            return;
        }
        
        filtered.forEach(item => {
            // FIX: Point to item.qty instead of item.stock
            const qty = parseInt(item.qty) || 0;
            
            let badge = qty === 0 
                ? `<span class="bg-red-50 text-red-700 border border-red-100 px-2 py-1 rounded-md text-[10px] font-bold">Out of Stock</span>` 
                : (qty <= 5 
                    ? `<span class="bg-amber-50 text-amber-700 border border-amber-100 px-2 py-1 rounded-md text-[10px] font-bold">Low Stock</span>` 
                    : `<span class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-1 rounded-md text-[10px] font-bold">Healthy Stock</span>`);
            
            let sc = qty === 0 ? 'text-red-600' : (qty <= 5 ? 'text-amber-600' : 'text-gray-700');
            
            tbody.insertAdjacentHTML('beforeend', `
                <tr class="hover:bg-gray-50/80 transition-colors border-b border-gray-50">
                    <td class="p-3.5 font-bold font-mono text-gray-400">${item.id}</td>
                    <td class="p-3.5 font-semibold text-gray-900">${item.name}</td>
                    <td class="p-3.5"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded font-bold text-[10px]">${item.category}</span></td>
                    <td class="p-3.5 text-right font-bold ${sc}">${qty}</td>
                    <td class="p-3.5 text-right font-semibold text-gray-500">$${parseFloat(item.price || 0).toFixed(2)}</td>
                    <td class="p-3.5 text-center">${badge}</td>
                </tr>`);
        });
    }

    function renderDashLogTables() {
        const tbody = document.getElementById('dashLogTableBody'); 
        tbody.innerHTML = '';
        
        appState.systemLogs.slice(0, 10).forEach(log => {
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-3 font-bold text-navyBlue">${log.user}</td>
                    <td class="p-3 font-medium text-gray-700">${log.action}</td>
                    <td class="p-3 text-right font-mono text-gray-400 text-[11px] whitespace-nowrap">${log.timestamp}</td>
                </tr>`;
        });
    }

    function updateDashCalculatedGauges() {
        let low = 0, out = 0;
        (appState.inventory || []).forEach(item => { 
            // FIX: Point to item.qty instead of item.stock
            const qty = parseInt(item.qty) || 0;
            if (qty === 0) out++; 
            else if (qty <= 5) low++; 
        });
        document.getElementById('dash-alert-low').innerText = low; 
        document.getElementById('dash-alert-out').innerText = out;
    }

    window.expandDashboardPanel = function(panelType) {
        const modal = document.getElementById('dashExpansionModal');
        const title = document.getElementById('dashModalDisplayTitle');
        const content = document.getElementById('dashModalDisplayContent');
        content.innerHTML = '';
        
        const inventory = appState.inventory || [];
        
        if(panelType === 'flowGraph') {
            title.innerText = "Inventory Flow Analysis";
            
            // Generate the exact same forward-facing dates as the line chart
            const dynamicDates = [];
            const inboundCounts = [0, 0, 0, 0, 0, 0];
            const outboundCounts = [0, 0, 0, 0, 0, 0];
            
            for (let i = 0; i <= 5; i++) {
                const d = new Date();
                d.setDate(d.getDate() + i);
                dynamicDates.push(d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
            }

            // Set a temporary loading state while we fetch the live data
            content.innerHTML = `<div class="p-4 text-center text-gray-500 font-medium">Loading ledger data...</div>`;

            fetch("{{ route('stock-movements.data') }}")
                .then(res => res.json())
                .then(data => {
                    const movements = data.movements || [];
                    
                    movements.forEach(movement => {
                        if (movement.status !== 'Approved') return;

                        const rawDate = movement.date || movement.created_at;
                        const movementDateStr = new Date(rawDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        const dateIndex = dynamicDates.indexOf(movementDateStr);
                        
                        if (dateIndex !== -1) {
                            const type = (movement.type || '').toLowerCase();
                            const qty = parseInt(movement.qty) || 0;

                            if (type === 'stock-in' || type === 'product return') {
                                inboundCounts[dateIndex] += qty;
                            } else if (type === 'stock-out' || type === 'warehouse transfer') {
                                outboundCounts[dateIndex] += qty;
                            }
                        }
                    });

                    // Build the HTML rows using the live arrays we just populated
                    let rowsHtml = '';
                    dynamicDates.forEach((dateStr, index) => {
                        rowsHtml += `<div class="flex justify-between border-b border-gray-100 pb-1.5 pt-1.5 last:border-0 last:pb-0">
                            <span>${dateStr}</span> 
                            <span class="text-navyBlue font-bold">${inboundCounts[index]} Units</span> 
                            <span class="text-emeraldGreen font-bold">${outboundCounts[index]} Units</span>
                        </div>`;
                    });

                    // Inject the final HTML into the modal
                    content.innerHTML = `
                        <div class="space-y-4">
                            <p class="text-gray-500 leading-relaxed">The warehouse asset efficiency calculations show inbound deliveries and outbound fulfillment over the next 6 days:</p>
                            <div class="bg-white border rounded-lg p-4">
                                <h4 class="font-bold text-gray-800 mb-2 uppercase text-[10px] tracking-wide">Daily Ledger Details</h4>
                                <div class="space-y-2 text-gray-600 font-medium">
                                    <div class="flex justify-between border-b pb-2 text-gray-400 text-[10px] uppercase"><span>Date</span> <span>Units Inbound</span> <span>Units Outbound</span></div>
                                    ${rowsHtml}
                                </div>
                            </div>
                        </div>`;
                })
                .catch(err => console.error("Modal Fetch Error:", err));

        } else if(panelType === 'warehouses') {
            title.innerText = "Category Distribution Analysis";
            
            // Calculate live category stats based on qty
            const catData = {};
            let total = 0;
            inventory.forEach(item => {
                const qty = parseInt(item.qty) || 0;
                if (qty > 0) {
                    const cat = item.category || 'Uncategorized';
                    catData[cat] = (catData[cat] || 0) + qty;
                    total += qty;
                }
            });
            
            let rowsHtml = '';
            if (total === 0) {
                rowsHtml = `<div class="py-2.5 text-gray-400 text-center">No stock available</div>`;
            } else {
                Object.entries(catData).sort((a, b) => b[1] - a[1]).forEach(([cat, qty]) => {
                    const pct = ((qty / total) * 100).toFixed(1);
                    rowsHtml += `<div class="flex justify-between py-2.5 border-b border-gray-100 last:border-0"><span>${cat}</span><span class="font-bold text-navyBlue">${pct}% (${qty} units)</span></div>`;
                });
            }

            content.innerHTML = `
                <div class="space-y-3">
                    <p class="text-gray-500 leading-relaxed">Live breakdown of inventory by hardware categories:</p>
                    <div class="bg-white border rounded-lg p-4 font-medium">
                        ${rowsHtml}
                    </div>
                </div>`;

        } else if(panelType === 'categories') {
            title.innerText = "Storage Placement Overview";
            
            // Calculate live warehouse stats based on qty
            const whData = {};
            let total = 0;
            inventory.forEach(item => {
                const qty = parseInt(item.qty) || 0;
                if (qty > 0) {
                    const wh = item.warehouse || 'Main Hub';
                    whData[wh] = (whData[wh] || 0) + qty;
                    total += qty;
                }
            });

            let rowsHtml = '';
            if (total === 0) {
                rowsHtml = `<div class="py-2.5 text-gray-400 text-center">No stock available</div>`;
            } else {
                Object.entries(whData).sort((a, b) => b[1] - a[1]).forEach(([wh, qty]) => {
                    const pct = ((qty / total) * 100).toFixed(1);
                    rowsHtml += `<div class="flex justify-between py-2.5 border-b border-gray-100 last:border-0"><span>${wh}</span><span class="font-bold text-emeraldGreen">${pct}% (${qty} units)</span></div>`;
                });
            }

            content.innerHTML = `
                <div class="space-y-3">
                    <p class="text-gray-500 leading-relaxed">Live breakdown of component placements per storage facility:</p>
                    <div class="bg-white border rounded-lg p-4 font-medium">
                        ${rowsHtml}
                    </div>
                </div>`;

        } else if(panelType === 'systemLogs') {
            // (System logs were already pulling from the live DB, so this part stays the same)
            title.innerText = "System Log Audit Archive";
            let logsHtml = `
                <div class="bg-white border rounded-lg overflow-hidden">
                    <div class="p-3 bg-gray-100 font-bold border-b text-gray-500 grid grid-cols-5 uppercase text-[10px]">
                        <span>User</span><span class="col-span-3">Action Completed</span><span class="text-right">Timestamp</span>
                    </div>
                    <div class="divide-y divide-gray-100">`;
            
            (appState.systemLogs || []).forEach(log => {
                logsHtml += `
                    <div class="p-3 grid grid-cols-5 items-center font-medium text-gray-700 hover:bg-gray-50">
                        <span class="font-bold text-navyBlue">${log.user}</span>
                        <span class="col-span-3 text-gray-600">${log.action}</span>
                        <span class="font-mono text-gray-400 text-[11px] text-right">${log.timestamp}</span>
                    </div>`;
            });
            logsHtml += `</div></div>`;
            content.innerHTML = logsHtml;
        }
        
        modal.classList.remove('hidden'); 
        modal.classList.add('flex');
    }

    window.closeDashModalWindow = function() {
        document.getElementById('dashExpansionModal').classList.add('hidden');
        document.getElementById('dashExpansionModal').classList.remove('flex');
    }

    // ==========================================
    // RETURNS & QC LOGIC
    // ==========================================
    let returnsChartInstance = null;

    function initReturnsChart() {
        const ctx = document.getElementById('returnsChart').getContext('2d');
        
        // Setup empty chart shell
        returnsChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [], 
                datasets: [
                    { label: 'Internal Inspections', data: [], backgroundColor: '#1E3A8A', borderRadius: 4 },
                    { label: 'Manufacturer Returns (RMA)', data: [], backgroundColor: '#10B981', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { font: { size: 12, family: "'Inter', sans-serif" }, color: '#374151' } } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { color: '#6B7280' } },
                    x: { grid: { display: false }, ticks: { color: '#6B7280' } }
                }
            }
        });
        
        updateReturnsChart();
    }

    // New function to calculate dynamic data from Audit Logs
    function updateReturnsChart() {
        if (!returnsChartInstance) return;
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        const labels = [];
        const insData = [0, 0, 0, 0, 0, 0];
        const rmaData = [0, 0, 0, 0, 0, 0];
        
        // Generate the last 6 months based on the current date
        const d = new Date();
        for (let i = 5; i >= 0; i--) {
            const pastDate = new Date(d.getFullYear(), d.getMonth() - i, 1);
            labels.push(monthNames[pastDate.getMonth()]);
        }

        // Tally data from the audit log
        (appState.returnsAudit || []).forEach(log => {
            const logDate = new Date(log.time);
            const diffMonths = (d.getFullYear() - logDate.getFullYear()) * 12 + d.getMonth() - logDate.getMonth();
            
            if (diffMonths >= 0 && diffMonths <= 5) {
                const index = 5 - diffMonths;
                if (log.stream === 'Inspection') insData[index]++;
                if (log.stream === 'RMA') rmaData[index]++;
            }
        });

        // Apply data and re-render
        returnsChartInstance.data.labels = labels;
        returnsChartInstance.data.datasets[0].data = insData;
        returnsChartInstance.data.datasets[1].data = rmaData;
        returnsChartInstance.update();
    }

    function renderReturnsDropdowns() {
        const insSelect = document.getElementById('insItem'); 
        const rmaSelect = document.getElementById('rmaItem');
        
        let opts = '<option value="">-- Choose Item from Inventory --</option>';
        appState.inventory.forEach(item => {
            opts += `<option value="${item.id}">${item.id} - ${item.name} (${item.category})</option>`;
        });
        
        insSelect.innerHTML = opts; 
        rmaSelect.innerHTML = opts;
    }

    window.handleInspectionSubmit = async function(e) {
        e.preventDefault();
        const payload = { 
            itemId: document.getElementById('insItem').value, 
            source: document.getElementById('insSource').value, 
            op: document.getElementById('insOp').value, 
            outcome: document.getElementById('insOutcome').value 
        };
        const res = await fetch('/inventory/api/inspection', { method: 'POST', headers, body: JSON.stringify(payload) });
        appState = await res.json();
        document.getElementById('formInspection').reset(); 
        refreshAllUI();
    }

    window.handleRmaSubmit = async function(e) {
        e.preventDefault();
        const checkboxes = document.querySelectorAll('.rma-reason:checked');
        if(checkboxes.length === 0) return alert("Select at least one reason.");
        
        const payload = { 
            itemId: document.getElementById('rmaItem').value, 
            vendor: document.getElementById('rmaVendor').value, 
            op: document.getElementById('rmaOp').value, 
            reasons: Array.from(checkboxes).map(cb => cb.value).join(', ') 
        };
        const res = await fetch('/inventory/api/rma', { method: 'POST', headers, body: JSON.stringify(payload) });
        appState = await res.json();
        document.getElementById('formRMA').reset(); 
        refreshAllUI();
    }

    function renderReturnsPendingInspections() {
        const tbody = document.getElementById('tableApproveIns'); 
        tbody.innerHTML = '';
        const empty = document.getElementById('emptyApproveIns');
        
        document.getElementById('countIns').innerText = appState.inspections.length;
        if (appState.inspections.length === 0) { empty.classList.remove('hidden'); return; }
        
        empty.classList.add('hidden');
        appState.inspections.forEach(req => {
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 border-b border-gray-100">
                        <div class="font-bold text-navyBlue text-xs">${req.product}</div>
                        <div class="text-[10px] text-gray-500 mt-0.5">Op: ${req.op} | ${req.date}</div>
                        <div class="text-[10px] text-gray-400">Src: ${req.source}</div>
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100">
                        <span class="inline-block bg-blue-50 text-navyBlue text-[10px] font-bold px-2 py-1 rounded">${req.action}</span>
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 text-center">
                        <select onchange="resolveReturn('Inspection', '${req.id}', this.value)" class="border border-gray-300 rounded px-1.5 py-1 text-[11px] font-semibold focus:border-emeraldGreen outline-none bg-white cursor-pointer">
                            <option value="" selected disabled>Action</option>
                            <option value="Approved">Approve</option>
                            <option value="Voided">Void</option>
                        </select>
                    </td>
                </tr>`;
        });
    }

    function renderReturnsPendingRMAs() {
        const tbody = document.getElementById('tableApproveRma'); 
        tbody.innerHTML = '';
        const empty = document.getElementById('emptyApproveRma');
        
        document.getElementById('countRma').innerText = appState.rmas.length;
        if (appState.rmas.length === 0) { empty.classList.remove('hidden'); return; }
        
        empty.classList.add('hidden');
        appState.rmas.forEach(req => {
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 border-b border-gray-100">
                        <div class="font-bold text-navyBlue text-xs">${req.product}</div>
                        <div class="text-[10px] text-gray-500 mt-0.5">Op: ${req.op} | ${req.date}</div>
                        <div class="text-[10px] text-gray-400">Vendor: ${req.vendor}</div>
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100">
                        <div class="text-xs text-gray-700 max-w-[150px] whitespace-normal leading-tight">${req.reasons}</div>
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 text-center">
                        <select onchange="resolveReturn('RMA', '${req.id}', this.value)" class="border border-gray-300 rounded px-1.5 py-1 text-[11px] font-semibold focus:border-emeraldGreen outline-none bg-white cursor-pointer">
                            <option value="" selected disabled>Action</option>
                            <option value="Approved">Approve</option>
                            <option value="Voided">Void</option>
                        </select>
                    </td>
                </tr>`;
        });
    }

    window.resolveReturn = async function(type, id, decision) {
        if(!decision) return;
        const res = await fetch('/inventory/api/resolve-return', { method: 'POST', headers, body: JSON.stringify({type, id, decision}) });
        appState = await res.json(); 
        refreshAllUI();
    }

    function renderReturnsAuditLog(data = appState.returnsAudit) {
        const tbody = document.getElementById('tableReturnsAudit'); 
        tbody.innerHTML = '';
        const empty = document.getElementById('emptyReturnsAudit');
        
        if (data.length === 0) { empty.classList.remove('hidden'); return; }
        
        empty.classList.add('hidden');
        data.forEach(log => {
            let bc = "bg-gray-100 text-gray-500 border-gray-200 line-through";
            if(log.statusType === 'success') bc = "bg-green-50 text-emeraldGreen border-emeraldGreen";
            if(log.statusType === 'danger') bc = "bg-red-50 text-red-600 border-red-300";
            if(log.statusType === 'neutral') bc = "bg-orange-50 text-orange-600 border-orange-300";
            
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100">
                    <td class="px-6 py-3 text-xs text-gray-500 font-mono">${log.time}</td>
                    <td class="px-6 py-3 font-bold text-gray-700 text-sm">${log.op}</td>
                    <td class="px-6 py-3"><span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">${log.stream}</span></td>
                    <td class="px-6 py-3 text-sm text-navyBlue font-semibold">${log.info}</td>
                    <td class="px-6 py-3 text-right"><span class="px-2.5 py-1 text-[11px] font-bold border rounded-md ${bc}">${log.outcome}</span></td>
                </tr>`;
        });
    }

    window.filterReturnsLogs = function() {
        const term = document.getElementById('searchReturnsLog').value.toLowerCase();
        const stream = document.getElementById('filterLogStream').value;
        const filtered = appState.returnsAudit.filter(log => {
            const matchTerm = log.info.toLowerCase().includes(term) || log.op.toLowerCase().includes(term);
            const matchStream = stream === "All" || log.stream === stream;
            return matchTerm && matchStream;
        });
        renderReturnsAuditLog(filtered);
    }

    // ==========================================
    // PRODUCT BUNDLING LOGIC
    // ==========================================
    function renderBundlingTable() {
        const term = document.getElementById('bundleSearchInput').value.toLowerCase();
        const cat = document.getElementById('bundleCategoryFilter').value;
        const tbody = document.getElementById('bundlingInventoryTableBody'); 
        tbody.innerHTML = '';
        
        const filtered = appState.inventory.filter(i => (i.name.toLowerCase().includes(term) || i.id.toLowerCase().includes(term)) && (cat === 'All' || i.category === cat));
        
        if (filtered.length === 0) { 
            tbody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-gray-500">No parts found.</td></tr>`; 
            return; 
        }
        
        filtered.forEach(item => {
            // FIX: Point to item.qty
            const sc = item.qty <= 0 ? 'text-red-500 font-bold' : 'text-gray-700 font-medium';
            tbody.insertAdjacentHTML('beforeend', `
                <tr class="hover:bg-gray-50 border-b border-gray-100 transition-colors">
                    <td class="py-3 px-4 font-mono text-xs text-navyBlue">${item.id}</td>
                    <td class="py-3 px-4"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">${item.category}</span></td>
                    <td class="py-3 px-4 font-medium text-gray-800">${item.name}</td>
                    <td class="py-3 px-4 text-right ${sc}">${item.qty}</td>
                </tr>`);
        });
    }

    function renderBundlingCustomBuilder() {
        const container = document.getElementById('customSelectsContainer'); 
        const currentSelections = {};
        
        // Save current selections to prevent resetting if the page refreshes
        document.querySelectorAll('.custom-part-select').forEach(s => { 
            currentSelections[s.dataset.category] = s.value; 
        });
        
        container.innerHTML = '';
        
        // Map the UI labels (what the user sees) to the actual database categories
        const categoryMap = [
            { label: 'CPU', dbValue: 'Processor' },
            { label: 'GPU', dbValue: 'Graphics Card' },
            { label: 'Motherboard', dbValue: 'Motherboard' },
            { label: 'RAM', dbValue: 'Memory' },
            { label: 'Storage', dbValue: 'Storage' },
            { label: 'Power Supply', dbValue: 'Power Supply' },
            { label: 'Case', dbValue: 'Case' }, // Assuming these match your DB
            { label: 'Cooler', dbValue: 'Cooler' } // Assuming these match your DB
        ];
        
        categoryMap.forEach(mapping => {
            let opts = `<option value="">-- Choose ${mapping.label} --</option>`;
            
            // Filter using the exact database value
            const availableParts = appState.inventory.filter(i => {
                const dbCategory = (i.category || '').trim().toLowerCase();
                const targetCategory = mapping.dbValue.toLowerCase();
                
                return dbCategory === targetCategory && i.qty > 0;
            });

            availableParts.forEach(i => {
                // Keep the selection if it was already chosen
                const sel = currentSelections[mapping.dbValue] === i.id ? 'selected' : '';
                opts += `<option value="${i.id}" ${sel}>${i.name} (Stock: ${i.qty})</option>`;
            });
            
            // Note: data-category is set to mapping.dbValue so the Pre-built Package "Customize" button can find it!
            container.insertAdjacentHTML('beforeend', `
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">${mapping.label}</label>
                    <select data-category="${mapping.dbValue}" class="custom-part-select border border-gray-300 rounded-md text-sm px-3 py-2 bg-gray-50 focus:border-navyBlue focus:ring-1 focus:ring-navyBlue outline-none">${opts}</select>
                </div>`);
        });
    }

    function renderBundlingPresets() {
        const container = document.getElementById('presetsContainer'); 
        container.innerHTML = '';
        
        bundlingPresets.forEach(p => {
            let maxBuild = Infinity;
            p.recipe.forEach(id => {
                const item = appState.inventory.find(i => i.id === id);
                // FIX: Calculate based on item.qty
                if (item && item.qty < maxBuild) maxBuild = item.qty;
                if (!item) maxBuild = 0;
            });
            
            if (maxBuild === Infinity) maxBuild = 0;
            const bc = maxBuild <= 0 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700';
            
            container.insertAdjacentHTML('beforeend', `
                <div class="border border-gray-200 rounded-md p-4 hover:border-navyBlue transition-colors flex justify-between items-center bg-gray-50">
                    <div>
                        <h4 class="font-bold text-navyBlue">${p.name}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">${p.desc}</p>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded text-xs font-bold ${bc}">${maxBuild} can be built</span>
                    </div>
                    <button onclick="openBundlePresetModal('${p.id}')" class="px-4 py-2 border-2 border-navyBlue text-navyBlue hover:bg-navyBlue hover:text-white rounded text-sm font-bold transition-colors shrink-0 ml-4">
                        View Options
                    </button>
                </div>`);
        });
    }

    window.openBundlePresetModal = function(pid) {
        const p = bundlingPresets.find(i => i.id === pid);
        bundlingSelectedPresetId = pid;
        let maxB = Infinity;
        
        document.getElementById('modalPresetPartsList').innerHTML = '';
        p.recipe.forEach(id => {
            const part = appState.inventory.find(i => i.id === id);
            if (part) {
                // FIX: Calculate against part.qty
                if (part.qty < maxB) maxB = part.qty;
                const c = part.qty > 0 ? 'text-emeraldGreen' : 'text-red-500';
                document.getElementById('modalPresetPartsList').insertAdjacentHTML('beforeend', `
                    <li class="flex justify-between items-center border-b border-gray-200 border-dashed pb-1 last:border-0 last:pb-0">
                        <span class="text-gray-700"><span class="text-xs font-bold text-gray-400 mr-2">${part.category}</span> ${part.name}</span>
                        <span class="${c} font-bold text-xs">${part.qty} in stock</span>
                    </li>`);
            }
        });
        
        maxB = maxB === Infinity ? 0 : maxB;
        document.getElementById('modalPresetTitle').innerText = p.name;
        document.getElementById('modalPresetDesc').innerText = p.desc;
        document.getElementById('modalPresetMaxBuild').innerText = maxB;
        
        const btn = document.getElementById('confirmPresetPurchaseBtn');
        if(maxB <= 0) {
            btn.disabled = true; 
            btn.innerText = "Out of Stock"; 
            btn.classList.replace('bg-emeraldGreen', 'bg-gray-400');
        } else {
            btn.disabled = false; 
            btn.innerText = "Submit Request"; 
            btn.classList.replace('bg-gray-400', 'bg-emeraldGreen');
        }
        
        document.getElementById('presetModal').classList.remove('hidden'); 
        document.getElementById('presetModal').classList.add('flex');
    };

    document.getElementById('confirmPresetPurchaseBtn').addEventListener('click', async () => {
        const p = bundlingPresets.find(i => i.id === bundlingSelectedPresetId);
        const payload = { 
            requester: document.getElementById('userSelector').value, 
            type: 'Pre-built', 
            details: p.name, 
            recipe: p.recipe 
        };
        const res = await fetch('/inventory/api/bundle', { method: 'POST', headers, body: JSON.stringify(payload) });
        appState = await res.json(); 
        
        document.getElementById('presetModal').classList.add('hidden'); 
        document.getElementById('presetModal').classList.remove('flex');
        refreshAllUI();
    });

    document.getElementById('customizePresetBtn').addEventListener('click', () => {
        const p = bundlingPresets.find(i => i.id === bundlingSelectedPresetId);
        document.querySelectorAll('.custom-part-select').forEach(s => s.value = "");
        
        p.recipe.forEach(id => {
            const part = appState.inventory.find(i => i.id === id);
            // FIX: Ensure part.qty > 0 before selecting
            if (part && part.qty > 0) {
                const sel = document.querySelector(`.custom-part-select[data-category="${part.category}"]`);
                if (sel) sel.value = id;
            }
        });
        
        document.getElementById('presetModal').classList.add('hidden'); 
        document.getElementById('presetModal').classList.remove('flex');
        
        const cSec = document.getElementById('customBuilderSection');
        cSec.classList.add('ring-2', 'ring-emeraldGreen');
        setTimeout(() => cSec.classList.remove('ring-2', 'ring-emeraldGreen'), 1500);
    });

    window.handleReviewCustomBuild = function(e) {
        e.preventDefault();
        bundlingPendingCustomIds = [];
        document.querySelectorAll('.custom-part-select').forEach(s => { 
            if (s.value) bundlingPendingCustomIds.push(s.value); 
        });
        
        if (bundlingPendingCustomIds.length === 0) return alert("Select at least one part for your custom build.");
        
        const list = document.getElementById('customConfirmPartsList'); 
        list.innerHTML = '';
        
        bundlingPendingCustomIds.forEach(id => {
            const p = appState.inventory.find(i => i.id === id);
            list.insertAdjacentHTML('beforeend', `
                <li class="flex justify-between items-center border-b border-gray-200 border-dashed pb-1 last:border-0 last:pb-0">
                    <span class="text-gray-700"><span class="text-xs font-bold text-gray-400 mr-2">${p.category}</span> ${p.name}</span>
                </li>`);
        });
        
        document.getElementById('customConfirmModal').classList.remove('hidden'); 
        document.getElementById('customConfirmModal').classList.add('flex');
    };

    document.getElementById('placeCustomOrderBtn').addEventListener('click', async () => {
        const payload = { 
            requester: document.getElementById('userSelector').value, 
            type: 'Custom Build', 
            details: `Assembly (${bundlingPendingCustomIds.length} parts)`, 
            recipe: bundlingPendingCustomIds 
        };
        const res = await fetch('/inventory/api/bundle', { method: 'POST', headers, body: JSON.stringify(payload) });
        appState = await res.json();
        
        document.getElementById('customConfirmModal').classList.add('hidden'); 
        document.getElementById('customConfirmModal').classList.remove('flex');
        document.querySelectorAll('.custom-part-select').forEach(s => s.value = "");
        refreshAllUI();
    });

    function renderBundlingApprovalTable() {
        const tbody = document.getElementById('bundlingApprovalTableBody'); 
        tbody.innerHTML = '';
        
        if (appState.bundlePending.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-gray-400 italic">No pending requests.</td></tr>`; 
            return;
        }
        
        appState.bundlePending.forEach(req => {
            const outOfStockParts = (req.recipe || []).filter(partId => {
                const part = appState.inventory.find(i => i.id === partId);
                // FIX: Check against part.qty
                return !part || part.qty <= 0;
            });
            const blocked = outOfStockParts.length > 0;

            const approveBtn = blocked
                ? `<button disabled title="Out of stock — cannot approve" class="text-xs bg-gray-300 text-gray-500 font-bold py-1 px-2 rounded cursor-not-allowed">Approve</button>`
                : `<button onclick="resolveBundle('${req.id}', 'Approved')" class="text-xs bg-emeraldGreen hover:bg-green-600 text-white font-bold py-1 px-2 rounded transition-colors">Approve</button>`;

            tbody.insertAdjacentHTML('beforeend', `
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="py-2 px-3 text-gray-800 font-medium">${req.requester}</td>
                    <td class="py-2 px-3 text-gray-500 text-xs">${req.requestDate}</td>
                    <td class="py-2 px-3 text-navyBlue font-medium">
                        ${req.type}: ${req.details}
                        ${blocked ? `<span class="block mt-0.5 text-[10px] font-bold text-red-600">⚠ Out of stock — cannot approve</span>` : ''}
                    </td>
                    <td class="py-2 px-3 text-center">
                        <div class="flex gap-2 justify-center">
                            ${approveBtn}
                            <button onclick="resolveBundle('${req.id}', 'Voided')" class="text-xs bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded transition-colors">Void</button>
                        </div>
                    </td>
                </tr>`);
        });
    }

    // ==========================================
    // INITIALIZATION & EVENT LISTENERS
    // ==========================================
    document.querySelectorAll('.closeModalBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById(btn.dataset.target).classList.add('hidden');
            document.getElementById(btn.dataset.target).classList.remove('flex');
        });
    });
    
    document.getElementById('bundleSearchInput').addEventListener('input', renderBundlingTable);
    document.getElementById('bundleCategoryFilter').addEventListener('change', renderBundlingTable);
    
    document.getElementById('clearCustomBtn').addEventListener('click', (e) => { 
        e.preventDefault(); 
        document.querySelectorAll('.custom-part-select').forEach(s => s.value = ""); 
    });

    window.addEventListener('DOMContentLoaded', () => {
        initReturnsChart();
        refreshAllUI();
    });

    // 1. Missing API caller to approve/void requests
    window.resolveBundle = async function(id, decision) {
        // Grab the current acting user from the top right dropdown
        const approver = document.getElementById('userSelector').value;
        
        try {
            const res = await fetch('/inventory/api/resolve-bundle', { 
                method: 'POST', 
                headers, 
                body: JSON.stringify({ id, decision, approver }) 
            });
            
            const data = await res.json();
            
            if (data.success === false) {
                alert(data.message);
                return;
            }
            
            appState = data; 
            refreshAllUI();
        } catch (error) {
            console.error("Failed to resolve bundle:", error);
        }
    }

    // 2. Missing function to draw the Audit Log table
    window.renderBundlingAuditTable = function() {
        const tbody = document.getElementById('bundlingAuditTableBody'); 
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        if (!appState.bundleAudit || appState.bundleAudit.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-gray-400 italic">No audit history found.</td></tr>`; 
            return;
        }
        
        appState.bundleAudit.forEach(req => {
            let statusStyle = req.status === 'Approved' ? 'text-emeraldGreen' : 'text-red-500 line-through';
            
            tbody.insertAdjacentHTML('beforeend', `
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="py-2 px-3 text-gray-500 text-xs">${req.actionDate}</td>
                    <td class="py-2 px-3 text-navyBlue font-medium">${req.type}: ${req.details}</td>
                    <td class="py-2 px-3 text-gray-800 text-xs">Req: ${req.requester}<br>Appr: ${req.approver}</td>
                    <td class="py-2 px-3 font-bold ${statusStyle}">${req.status}</td>
                </tr>`);
        });
    }

    // 1. Intercept clicks if we are already inside the SPA dashboard
    function handleJsNav(event, tabName) {
        event.preventDefault();
        
        // Cleanly update the URL in the browser without reloading
        window.history.pushState({}, '', '/?tab=' + tabName);
        
        // Fire your existing function to change the UI
        routeTo(tabName);
    }

    // 2. Automatically load the module if you arrive from another page (like Inventory Items)
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        // If there is a tab parameter, load it immediately
        if (tab && typeof routeTo === 'function') {
            routeTo(tab);
        }
    });
</script> 
@endsection