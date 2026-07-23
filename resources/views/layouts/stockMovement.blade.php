<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Inventory Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#1E3A8A',
                        emeraldAccent: '#10B981',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen flex flex-col md:flex-row">

    <!-- Left Sidebar Layout (Preserved from original) -->
    <aside class="w-full md:w-64 bg-navy text-white flex flex-col justify-between md:sticky md:top-0 md:h-screen shadow-xl z-20 shrink-0">
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
        <div class="p-4 border-t border-white/10 bg-black/10 flex items-center justify-between text-sm font-semibold">
            <a href="#" class="hover:text-blue-200 transition-colors flex items-center gap-2 py-1 px-2 rounded hover:bg-white/5 text-xs">
                <span class="w-2 h-2 rounded-full bg-emeraldAccent"></span>Admin Panel
            </a>
            <button onclick="alert('Logging out...')" class="hover:text-red-300 text-white/80 transition-colors flex items-center gap-1 py-1 px-2 rounded hover:bg-white/5 text-xs">Logout</button>
        </div>
    </aside>

    <!-- Content injected here -->
    @yield('content')

    <script>
        // Populates the "Alerts & Reorders" nav badge with live data, so it's
        // visible from this page too, not just when you're on that tab.
        fetch('{{ route("alerts.summary") }}')
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('nav-alerts-badge');
                if (!badge) return;
                if (!data.count) {
                    badge.classList.add('hidden');
                    badge.textContent = '';
                    return;
                }
                badge.textContent = data.count;
                badge.classList.remove('hidden');
                badge.classList.toggle('bg-red-500', data.hasCritical);
                badge.classList.toggle('bg-amber-500', !data.hasCritical);
            })
            .catch(() => {});
    </script>
</body>
</html>