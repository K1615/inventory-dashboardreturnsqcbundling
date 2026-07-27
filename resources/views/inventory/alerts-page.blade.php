{{-- resources/views/inventory/alerts-page.blade.php --}}
{{-- Standalone Alerts & Reorders page, separated out of the tabbed dashboard SPA. --}}
@extends('layouts.warehouse')

@section('content')
<div class="min-h-screen flex flex-col md:flex-row w-full bg-gray-50 text-gray-800 font-sans">
    <!-- Meta Configuration Setup for Interfacing APIs inside layouts -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Left Navigation Sidebar -->
    <aside class="w-full md:w-64 bg-[#1E3A8A] text-white flex flex-col justify-between md:sticky md:top-0 md:h-screen shadow-xl z-20 shrink-0">
        <!-- Top Area: Branding & Hamburger Button -->
        <div class="flex items-center justify-between p-6 border-b border-white/10 shrink-0">
            <div>
                <h1 class="text-lg font-bold tracking-wide text-white">ERP Inventory</h1>
                <p class="text-[10px] text-blue-200 uppercase tracking-widest mt-0.5">Management System</p>
            </div>
            
            <!-- Hamburger Button (Visible only on mobile) -->
            <button id="mobileMenuBtn" class="md:hidden text-white focus:outline-none focus:ring-2 focus:ring-white/50 rounded p-1 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <!-- Hamburger Icon -->
                    <path id="hamburgerIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <!-- Close (X) Icon -->
                    <path id="closeIcon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

            @php
                $currentRoute = request()->route()->getName();
                $activeClass = 'font-bold text-white bg-white/10 border-l-4 border-[#10B981] rounded-r-lg';
                $inactiveClass = 'font-semibold text-blue-100 border-l-4 border-transparent hover:text-white hover:bg-white/10 rounded-lg';
            @endphp

            <!-- Collapsible Content: Navigation Links + Role Bar -->
        <div id="mobileMenu" class="hidden md:flex flex-col justify-between flex-1 overflow-hidden transition-all duration-300 ease-in-out">
            <nav class="p-4 flex flex-col gap-1.5 overflow-y-auto flex-1">
                
                <!-- PASTE YOUR EXISTING <a> TAGS HERE -->
                <!-- Dashboard -->
                <a href="{{ route('inventory.dashboard', ['tab' => 'dashboard']) }}"
                class="nav-item px-4 py-2.5 text-xs transition-all {{ $currentRoute === 'inventory.dashboard' ? $activeClass : $inactiveClass }}">
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
                class="nav-item px-4 py-2.5 text-xs transition-all {{ $currentRoute === 'inventory.dashboard' ? $inactiveClass : $inactiveClass }}">
                    Returns & QC
                </a>

                <!-- Product Bundling -->
                <a href="{{ route('inventory.dashboard', ['tab' => 'bundling']) }}"
                class="nav-item px-4 py-2.5 text-xs transition-all {{ $inactiveClass }}">
                    Product Bundling
                </a>
            </nav>

            <div class="mt-auto shrink-0">
                @include('partials.role-bar')
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 h-screen overflow-y-auto relative w-full bg-gray-50">
        @include('inventory.alerts-reorders')
    </div>
</div>

<script>
    // This page's own scoped state fetch — it no longer shares appState with
    // the dashboard tab or any other page. If stock changes elsewhere, this
    // page won't know until it's reloaded or synced (see syncAlertsState below).
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken };
    let appState = @json($initialData);

    // Alerts & Reorders doesn't call refreshAllUI() (that function belonged to
    // the old shared SPA and touched Dashboard/Returns/Bundling DOM nodes that
    // no longer exist on this page). Each mutation handler in alerts-reorders.blade.php
    // still tries to call it in a try/catch, so nothing breaks — it just silently
    // no-ops here.

    document.addEventListener('DOMContentLoaded', () => {
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const hamIcon = document.getElementById('hamburgerIcon');
        const closeIcon = document.getElementById('closeIcon');

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => {
                // Toggle menu visibility
                mobileMenu.classList.toggle('hidden');
                mobileMenu.classList.toggle('flex');
                
                // Toggle between Hamburger and 'X' icons
                hamIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        // The shared partial ships with class="hidden" because it was built to be
        // one of several toggled tabs. On its own page it should always be visible.
        const view = document.getElementById('view-alerts');
        if (view) view.classList.remove('hidden');
        alertsRenderAll();
    });
</script>
@endsection
