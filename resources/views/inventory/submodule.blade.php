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
            <nav class="p-4 flex flex-col gap-1.5 overflow-y-auto flex-1">
                <a href="#" onclick="routeTo('dashboard')" id="nav-dashboard" class="nav-item px-4 py-2.5 text-xs font-bold text-white bg-white/10 border-l-4 border-emeraldGreen rounded-r-lg transition-all">
                    Dashboard
                </a>
                <a href="#" onclick="alert('Module in development')" class="px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all">
                    Inventory Items
                </a>
                <a href="#" onclick="alert('Module in development')" class="px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all">
                    Stock Movements
                </a>
                <a href="#" onclick="alert('Module in development')" class="px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all">
                    Warehouse Layout
                </a>
                <a href="#" onclick="routeTo('alerts')" id="nav-alerts" class="nav-item px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all border-l-4 border-transparent flex items-center justify-between">
                    <span>Alerts & Reorders</span>
                    <span id="nav-alerts-badge" class="hidden ml-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none"></span>
                </a>
                <a href="#" onclick="routeTo('returns')" id="nav-returns" class="nav-item px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all border-l-4 border-transparent">
                    Returns & QC
                </a>
                <a href="#" onclick="routeTo('bundling')" id="nav-bundling" class="nav-item px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all border-l-4 border-transparent">
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

                <div class="w-full flex items-center justify-center relative min-h-[160px] bg-gray-50/50 rounded-lg p-2 border border-gray-100">
                    <svg viewBox="0 0 500 160" class="w-full h-full">
                        <line x1="20" y1="20" x2="480" y2="20" class="chart-grid-line" />
                        <line x1="20" y1="70" x2="480" y2="70" class="chart-grid-line" />
                        <line x1="20" y1="120" x2="480" y2="120" class="chart-grid-line" />
                        <path d="M 40,110 L 120,60 L 200,90 L 280,30 L 360,80 L 460,25" class="chart-line-in" />
                        <path d="M 40,130 L 120,90 L 200,70 L 280,50 L 360,35 L 460,45" class="chart-line-out" />
                        <text x="40" y="145" fill="#9ca3af" font-size="9" text-anchor="middle">Wk 1</text>
                        <text x="120" y="145" fill="#9ca3af" font-size="9" text-anchor="middle">Wk 2</text>
                        <text x="200" y="145" fill="#9ca3af" font-size="9" text-anchor="middle">Wk 3</text>
                        <text x="280" y="145" fill="#9ca3af" font-size="9" text-anchor="middle">Wk 4</text>
                        <text x="360" y="145" fill="#9ca3af" font-size="9" text-anchor="middle">Wk 5</text>
                        <text x="460" y="145" fill="#9ca3af" font-size="9" text-anchor="middle">Wk 6</text>
                    </svg>
                </div>
            </div>

            <!-- Dashboard ROW 2: Pie Graphs -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pie Graph 1: Component Categories -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Product Categories</h3>
                            <p class="text-xs text-gray-400">Inventory split by component type</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="alert('Module in development')" class="text-[11px] font-semibold text-gray-600 hover:text-navyBlue bg-gray-100 px-2 py-1 rounded transition-colors">View Items</button>
                            <button onclick="expandDashboardPanel('categories')" class="text-[11px] font-bold text-white bg-navyBlue hover:bg-blue-800 px-2.5 py-1 rounded transition-colors">Expand</button>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 py-4">
                        <svg class="w-32 h-32 transform -rotate-90 rounded-full border border-gray-100" viewBox="0 0 32 32">
                            <circle cx="16" cy="16" r="16" fill="transparent" stroke="#1E3A8A" stroke-width="32" stroke-dasharray="35 100" />
                            <circle cx="16" cy="16" r="16" fill="transparent" stroke="#10B981" stroke-width="32" stroke-dasharray="25 100" stroke-dashoffset="-35" />
                            <circle cx="16" cy="16" r="16" fill="transparent" stroke="#F59E0B" stroke-width="32" stroke-dasharray="20 100" stroke-dashoffset="-60" />
                            <circle cx="16" cy="16" r="16" fill="transparent" stroke="#EF4444" stroke-width="32" stroke-dasharray="20 100" stroke-dashoffset="-80" />
                        </svg>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs font-medium text-gray-600">
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-navyBlue rounded-xs inline-block"></span> CPUs (35%)</div>
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-emeraldGreen rounded-xs inline-block"></span> GPUs (25%)</div>
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-amber-500 rounded-xs inline-block"></span> RAM (20%)</div>
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-red-500 rounded-xs inline-block"></span> Storage (20%)</div>
                        </div>
                    </div>
                </div>

                <!-- Pie Graph 2: Warehouse Placements -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Warehouse Locations</h3>
                            <p class="text-xs text-gray-400">Inventory split by storage site</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="alert('Module in development')" class="text-[11px] font-semibold text-gray-600 hover:text-navyBlue bg-gray-100 px-2 py-1 rounded transition-colors">View Maps</button>
                            <button onclick="expandDashboardPanel('warehouses')" class="text-[11px] font-bold text-white bg-navyBlue hover:bg-blue-800 px-2.5 py-1 rounded transition-colors">Expand</button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 py-4">
                        <svg class="w-32 h-32 transform -rotate-90 rounded-full border border-gray-100" viewBox="0 0 32 32">
                            <circle cx="16" cy="16" r="16" fill="transparent" stroke="#1E3A8A" stroke-width="32" stroke-dasharray="50 100" />
                            <circle cx="16" cy="16" r="16" fill="transparent" stroke="#10B981" stroke-width="32" stroke-dasharray="30 100" stroke-dashoffset="-50" />
                            <circle cx="16" cy="16" r="16" fill="transparent" stroke="#6B7280" stroke-width="32" stroke-dasharray="20 100" stroke-dashoffset="-80" />
                        </svg>
                        <div class="grid grid-cols-1 gap-y-1.5 text-xs font-medium text-gray-600">
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-navyBlue rounded-xs inline-block"></span> Warehouse A - Main Hub (50%)</div>
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-emeraldGreen rounded-xs inline-block"></span> Warehouse B - Logistics (30%)</div>
                            <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-gray-500 rounded-xs inline-block"></span> Warehouse C - Overflow (20%)</div>
                        </div>
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
                        <button onclick="alert('Module in development')" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-navyBlue hover:text-navyBlue text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            Open Catalog &rarr;
                        </button>
                    </div>
                    
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-emeraldGreen uppercase tracking-widest block">Logistics</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Stock Movements</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Record inbound deliveries, process shipments, and manage internal transfers.</p>
                        </div>
                        <button onclick="alert('Module in development')" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-emeraldGreen hover:text-emeraldGreen text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            Track Movements &rarr;
                        </button>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-navyBlue uppercase tracking-widest block">Facilities</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Warehouse Layout</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Locate items on the floorplan map and optimize physical storage space zones.</p>
                        </div>
                        <button onclick="alert('Module in development')" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-navyBlue hover:text-navyBlue text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            View Floorplans &rarr;
                        </button>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest block">Procurement</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Alerts & Reorders</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Review critical low-stock metrics and generate automated vendor purchase orders.</p>
                        </div>
                        <button onclick="routeTo('alerts')" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-amber-600 hover:text-amber-700 text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            Manage Orders &rarr;
                        </button>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-emeraldGreen uppercase tracking-widest block">Quality Assurance</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Returns & QC Hub</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Handle customer return requests (RMAs) and verify defective parts testing.</p>
                        </div>
                        <button onclick="routeTo('returns')" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-emeraldGreen hover:text-emeraldGreen text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                            Process Returns &rarr;
                        </button>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <span class="text-[10px] font-bold text-navyBlue uppercase tracking-widest block">Sales Packaging</span>
                            <h4 class="text-base font-bold text-gray-800 mt-1">Product Bundling</h4>
                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">Combine individual components into complete PC builds or promotional sets.</p>
                        </div>
                        <button onclick="routeTo('bundling')" class="mt-5 w-full text-center bg-white border border-gray-300 hover:border-navyBlue hover:text-navyBlue text-gray-700 text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
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
        <!-- VIEW 4: ALERTS & REORDERS MODULE           -->
        <!-- ========================================== -->
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
                <div onclick="alertsApplyQuickFilter('Overstock')" class="bg-white p-5 rounded-xl border-2 border-transparent hover:border-blue-500 cursor-pointer shadow-sm flex items-center justify-between transition group">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 group-hover:text-blue-500">Overstock</p>
                        <h3 class="text-2xl font-bold text-navyBlue mt-1" id="alerts-over-count">0</h3>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-500 rounded-lg"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
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
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="alerts-table-body" class="divide-y divide-gray-100 text-gray-700">
                            <tr><td colspan="6" class="py-6 text-center text-gray-400 italic">No active alerts.</td></tr>
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
            </div>
        </section>

    </div>

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
    function runDashDirectoryFiltering() {
        const query = document.getElementById('dashDirSearch').value.toLowerCase().trim();
        const cat = document.getElementById('dashDirCategory').value;
        const filtered = appState.inventory.filter(item => {
            const ms = item.name.toLowerCase().includes(query) || item.id.toLowerCase().includes(query);
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
            let badge = item.stock === 0 
                ? `<span class="bg-red-50 text-red-700 border border-red-100 px-2 py-1 rounded-md text-[10px] font-bold">Out of Stock</span>` 
                : (item.stock <= 5 
                    ? `<span class="bg-amber-50 text-amber-700 border border-amber-100 px-2 py-1 rounded-md text-[10px] font-bold">Low Stock</span>` 
                    : `<span class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-2 py-1 rounded-md text-[10px] font-bold">Healthy Stock</span>`);
            
            let sc = item.stock === 0 ? 'text-red-600' : (item.stock <= 5 ? 'text-amber-600' : 'text-gray-700');
            
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50/80 transition-colors border-b border-gray-50">
                    <td class="p-3.5 font-bold font-mono text-gray-400">${item.id}</td>
                    <td class="p-3.5 font-semibold text-gray-900">${item.name}</td>
                    <td class="p-3.5"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded font-bold text-[10px]">${item.category}</span></td>
                    <td class="p-3.5 text-right font-bold ${sc}">${item.stock}</td>
                    <td class="p-3.5 text-right font-semibold text-gray-500">$${parseFloat(item.price).toFixed(2)}</td>
                    <td class="p-3.5 text-center">${badge}</td>
                </tr>`;
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
        appState.inventory.forEach(item => { 
            if (item.stock === 0) out++; 
            else if (item.stock <= 5) low++; 
        });
        document.getElementById('dash-alert-low').innerText = low; 
        document.getElementById('dash-alert-out').innerText = out;
    }

    window.expandDashboardPanel = function(panelType) {
        const modal = document.getElementById('dashExpansionModal');
        const title = document.getElementById('dashModalDisplayTitle');
        const content = document.getElementById('dashModalDisplayContent');
        content.innerHTML = '';
        
        if(panelType === 'flowGraph') {
            title.innerText = "Inventory Flow Analysis";
            content.innerHTML = `
                <div class="space-y-4">
                    <p class="text-gray-500 leading-relaxed">The warehouse asset efficiency calculations show steady inbound deliveries matching outbound fulfillment over the past 6 weeks:</p>
                    <div class="bg-white border rounded-lg p-4">
                        <h4 class="font-bold text-gray-800 mb-2 uppercase text-[10px] tracking-wide">Weekly Ledger Details</h4>
                        <div class="space-y-2 text-gray-600 font-medium">
                            <div class="flex justify-between border-b pb-2 text-gray-400 text-[10px] uppercase"><span>Week</span> <span>Units Inbound</span> <span>Units Outbound</span></div>
                            <div class="flex justify-between border-b pb-1.5"><span>Week 1</span> <span class="text-navyBlue font-bold">140 Units</span> <span class="text-emeraldGreen font-bold">110 Units</span></div>
                            <div class="flex justify-between border-b pb-1.5"><span>Week 2</span> <span class="text-navyBlue font-bold">195 Units</span> <span class="text-emeraldGreen font-bold">150 Units</span></div>
                            <div class="flex justify-between border-b pb-1.5"><span>Week 3</span> <span class="text-navyBlue font-bold">220 Units</span> <span class="text-emeraldGreen font-bold">185 Units</span></div>
                            <div class="flex justify-between pb-1.5"><span>Week 4</span> <span class="text-navyBlue font-bold">310 Units</span> <span class="text-emeraldGreen font-bold">290 Units</span></div>
                        </div>
                    </div>
                </div>`;
        } else if(panelType === 'categories') {
            title.innerText = "Category Distribution Analysis";
            content.innerHTML = `
                <div class="space-y-3">
                    <p class="text-gray-500 leading-relaxed">Breakdown of inventory by hardware categories:</p>
                    <div class="bg-white border rounded-lg p-4 divide-y divide-gray-100 font-medium">
                        <div class="flex justify-between py-2.5"><span>CPUs (Processors)</span><span class="font-bold text-navyBlue">35%</span></div>
                        <div class="flex justify-between py-2.5"><span>GPUs (Graphics Cards)</span><span class="font-bold text-navyBlue">25%</span></div>
                        <div class="flex justify-between py-2.5"><span>RAM (Memory)</span><span class="font-bold text-navyBlue">20%</span></div>
                        <div class="flex justify-between py-2.5"><span>Storage (SSD/HDD)</span><span class="font-bold text-navyBlue">20%</span></div>
                    </div>
                </div>`;
        } else if(panelType === 'warehouses') {
            title.innerText = "Storage Placement Overview";
            content.innerHTML = `
                <div class="space-y-3">
                    <p class="text-gray-500 leading-relaxed">Breakdown of component placements per storage facility:</p>
                    <div class="bg-white border rounded-lg p-4 divide-y divide-gray-100 font-medium">
                        <div class="flex justify-between py-2.5"><span>Warehouse A (Main Hub)</span><span class="font-bold text-emeraldGreen">50% Placement Volume</span></div>
                        <div class="flex justify-between py-2.5"><span>Warehouse B (Logistics)</span><span class="font-bold text-emeraldGreen">30% Placement Volume</span></div>
                        <div class="flex justify-between py-2.5"><span>Warehouse C (Overflow)</span><span class="font-bold text-emeraldGreen">20% Placement Volume</span></div>
                    </div>
                </div>`;
        } else if(panelType === 'systemLogs') {
            title.innerText = "System Log Audit Archive";
            let logsHtml = `
                <div class="bg-white border rounded-lg overflow-hidden">
                    <div class="p-3 bg-gray-100 font-bold border-b text-gray-500 grid grid-cols-5 uppercase text-[10px]">
                        <span>User</span><span class="col-span-3">Action Completed</span><span class="text-right">Timestamp</span>
                    </div>
                    <div class="divide-y divide-gray-100">`;
            
            appState.systemLogs.forEach(log => {
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
    function initReturnsChart() {
        const ctx = document.getElementById('returnsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [
                    { label: 'Internal Inspections', data: [45, 52, 38, 65, 48, 59], backgroundColor: '#1E3A8A', borderRadius: 4 },
                    { label: 'Manufacturer Returns (RMA)', data: [12, 18, 14, 22, 16, 25], backgroundColor: '#10B981', borderRadius: 4 }
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
            const sc = item.stock <= 0 ? 'text-red-500 font-bold' : 'text-gray-700 font-medium';
            tbody.insertAdjacentHTML('beforeend', `
                <tr class="hover:bg-gray-50 border-b border-gray-100 transition-colors">
                    <td class="py-3 px-4 font-mono text-xs text-navyBlue">${item.id}</td>
                    <td class="py-3 px-4"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">${item.category}</span></td>
                    <td class="py-3 px-4 font-medium text-gray-800">${item.name}</td>
                    <td class="py-3 px-4 text-right ${sc}">${item.stock}</td>
                </tr>`);
        });
    }

    function renderBundlingCustomBuilder() {
        const container = document.getElementById('customSelectsContainer'); 
        const currentSelections = {};
        document.querySelectorAll('.custom-part-select').forEach(s => { currentSelections[s.dataset.category] = s.value; });
        
        container.innerHTML = '';
        ['CPU', 'GPU', 'Motherboard', 'RAM', 'Storage', 'Power Supply', 'Case', 'Cooler'].forEach(cat => {
            let opts = `<option value="">-- Choose ${cat} --</option>`;
            appState.inventory.filter(i => i.category === cat && i.stock > 0).forEach(i => {
                const sel = currentSelections[cat] === i.id ? 'selected' : '';
                opts += `<option value="${i.id}" ${sel}>${i.name} (Stock: ${i.stock})</option>`;
            });
            container.insertAdjacentHTML('beforeend', `
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">${cat}</label>
                    <select data-category="${cat}" class="custom-part-select border border-gray-300 rounded-md text-sm px-3 py-2 bg-gray-50 focus:border-navyBlue focus:ring-1 focus:ring-navyBlue outline-none">${opts}</select>
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
                if (item && item.stock < maxBuild) maxBuild = item.stock;
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
                if (part.stock < maxB) maxB = part.stock;
                const c = part.stock > 0 ? 'text-emeraldGreen' : 'text-red-500';
                document.getElementById('modalPresetPartsList').insertAdjacentHTML('beforeend', `
                    <li class="flex justify-between items-center border-b border-gray-200 border-dashed pb-1 last:border-0 last:pb-0">
                        <span class="text-gray-700"><span class="text-xs font-bold text-gray-400 mr-2">${part.category}</span> ${part.name}</span>
                        <span class="${c} font-bold text-xs">${part.stock} in stock</span>
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
            if (part && part.stock > 0) {
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
                return !part || part.stock <= 0;
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

    window.resolveBundle = async function(id, decision) {
        const approver = document.getElementById('userSelector').value;
        const res = await fetch('/inventory/api/resolve-bundle', { method: 'POST', headers, body: JSON.stringify({id, decision, approver}) });

        if (!res.ok) {
            const err = await res.json();
            alert(err.message || 'Could not approve this request.');
            return;
        }

        appState = await res.json(); 
        refreshAllUI();
    }

    function renderBundlingAuditTable() {
        const tbody = document.getElementById('bundlingAuditTableBody'); 
        tbody.innerHTML = '';
        
        if (appState.bundleAudit.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-gray-400 italic">No activity logs yet.</td></tr>`; 
            return;
        }
        
        appState.bundleAudit.forEach(log => {
            const sc = log.status === 'Approved' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600';
            tbody.insertAdjacentHTML('beforeend', `
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="py-2 px-3 text-gray-500 text-xs">${log.actionDate}</td>
                    <td class="py-2 px-3 text-navyBlue font-medium text-xs truncate max-w-[120px]" title="${log.type}: ${log.details}">${log.type}: ${log.details}</td>
                    <td class="py-2 px-3 text-gray-600 text-xs">${log.requester} &rarr; <b>${log.approver}</b></td>
                    <td class="py-2 px-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider ${sc}">${log.status}</span>
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

    // ==========================================
    // ALERTS & REORDERS SUBMODULE
    // ==========================================
    let alertsStatusFilter = "all";

    function alertsGetStatus(item) {
        if (parseInt(item.stock) === 0) return "Out of Stock";
        if (parseInt(item.stock) < parseInt(item.minLimit)) return "Low Stock";
        if (item.maxLimit > 0 && parseInt(item.stock) > parseInt(item.maxLimit)) return "Overstock";
        return "Normal";
    }

    function alertsStatusBadge(status) {
        switch (status) {
            case "Out of Stock": return `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Out of Stock</span>`;
            case "Low Stock": return `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Low Stock</span>`;
            case "Overstock": return `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Overstock</span>`;
            default: return `<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Normal</span>`;
        }
    }

    window.alertsApplyQuickFilter = function(status) {
        alertsStatusFilter = status;
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
            const matchesSearch = item.name.toLowerCase().includes(search) || item.id.toLowerCase().includes(search);
            const matchesStatus = targetFilter === 'all' || status === targetFilter;
            return matchesSearch && matchesStatus;
        });

        const tbody = document.getElementById('alerts-inventory-table-body');
        const noResults = document.getElementById('alerts-no-results');
        if (!tbody) return;

        if (items.length === 0) {
            tbody.innerHTML = '';
            noResults.classList.remove('hidden');
            return;
        }
        noResults.classList.add('hidden');

        tbody.innerHTML = items.map(item => {
            const status = alertsGetStatus(item);
            return `
                <tr class="hover:bg-gray-50 border-b border-gray-100 transition">
                    <td class="py-3 px-4">
                        <div class="font-semibold text-gray-900">${item.name}</div>
                        <div class="text-xs text-gray-400">${item.category} | ${item.id}</div>
                    </td>
                    <td class="py-3 px-2 text-center font-bold text-gray-900">${item.stock}</td>
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
                        <button onclick="alertsOpenPOModal('${item.id}', '${item.name.replace(/'/g, "\\'")}')" class="px-2.5 py-1 text-xs font-semibold rounded bg-blue-50 text-navyBlue hover:bg-navyBlue hover:text-white border border-blue-200 transition">
                            Create PO
                        </button>
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
            const item = alert.inventoryItem;
            const statusStyle = alert.status === 'acknowledged' ? 'text-amber-600 font-semibold' : 'text-red-600 font-semibold';
            const actions = alert.status === 'active'
                ? `<button onclick="alertsAcknowledge(${alert.id})" class="px-2 py-0.5 bg-amber-500 text-white rounded font-bold hover:bg-amber-600 text-[11px]">Acknowledge</button>
                   <button onclick="alertsResolve(${alert.id})" class="px-2 py-0.5 bg-emeraldGreen text-white rounded font-bold hover:bg-emeraldGreen/90 text-[11px] ml-1">Resolve</button>`
                : `<button onclick="alertsResolve(${alert.id})" class="px-2 py-0.5 bg-emeraldGreen text-white rounded font-bold hover:bg-emeraldGreen/90 text-[11px]">Resolve</button>`;

            return `
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 font-semibold text-gray-900">${item ? item.name : alert.inventory_item_id} <span class="text-gray-400 font-normal">(${alert.inventory_item_id})</span></td>
                    <td class="py-3 px-4">${typeLabels[alert.type] || alert.type}</td>
                    <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${sevStyle[alert.severity] || 'bg-gray-100 text-gray-600'}">${alert.severity}</span></td>
                    <td class="py-3 px-4 text-center">${alert.current_qty} / ${alert.threshold_qty}</td>
                    <td class="py-3 px-4 text-center ${statusStyle}">${alert.status}</td>
                    <td class="py-3 px-4 text-right whitespace-nowrap">${actions}</td>
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
    }

    window.alertsUpdateLimit = async function(id, target, value) {
        const res = await fetch(`/inventory/api/limits/${id}`, { method: 'POST', headers, body: JSON.stringify({ target, value: parseInt(value) || 0 }) });
        appState = await res.json();
        refreshAllUI();
    }

    window.alertsToggleAutoReorder = async function(id, enabled) {
        const res = await fetch(`/inventory/api/auto-reorder/${id}`, { method: 'POST', headers, body: JSON.stringify({ enabled }) });
        appState = await res.json();
        refreshAllUI();
    }

    window.alertsAcknowledge = async function(id) {
        const res = await fetch(`/inventory/api/alerts/${id}/acknowledge`, { method: 'POST', headers });
        appState = await res.json();
        refreshAllUI();
    }

    window.alertsResolve = async function(id) {
        const res = await fetch(`/inventory/api/alerts/${id}/resolve`, { method: 'POST', headers });
        appState = await res.json();
        refreshAllUI();
    }

    window.alertsSubmitDraft = async function(id) {
        const res = await fetch(`/inventory/api/draft/${id}/submit`, { method: 'POST', headers });
        const data = await res.json();
        if (data.success === false) { alert(data.message || 'Operation failed'); return; }
        appState = data;
        refreshAllUI();
    }

    window.alertsDiscardDraft = async function(id) {
        const res = await fetch(`/inventory/api/draft/${id}/discard`, { method: 'POST', headers });
        const data = await res.json();
        if (data.success === false) { alert(data.message || 'Operation failed'); return; }
        appState = data;
        if (data.autoReorderTurnedOff) {
            alert("This draft was declined, and auto-reorder has been turned OFF for this item so it won't be redrafted automatically.");
        }
        refreshAllUI();
    }

    window.alertsProcessPipeline = async function(id, status) {
        const res = await fetch(`/inventory/api/pipeline/${id}`, { method: 'POST', headers, body: JSON.stringify({ status }) });
        const data = await res.json();
        if (data.success === false) { alert(data.message || 'Operation failed'); return; }
        appState = data;
        refreshAllUI();
    }

    window.alertsMarkReceived = async function(id) {
        const res = await fetch(`/inventory/api/pipeline/${id}/receive`, { method: 'POST', headers });
        const data = await res.json();
        if (data.success === false) { alert(data.message || 'Operation failed'); return; }
        appState = data;
        refreshAllUI();
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
        appState = await res.json();
        alertsClosePOModal();
        refreshAllUI();
    }
</script> 
@endsection