{{-- resources/views/inventory/alerts-page.blade.php --}}
{{-- Standalone Alerts & Reorders page, separated out of the tabbed dashboard SPA. --}}
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
                $currentRoute = request()->route()->getName();
                $activeClass = 'font-bold text-white bg-white/10 border-l-4 border-[#10B981] rounded-r-lg';
                $inactiveClass = 'font-semibold text-blue-100 border-l-4 border-transparent hover:text-white hover:bg-white/10 rounded-lg';
            @endphp

            <nav class="p-4 flex flex-col gap-1.5 overflow-y-auto flex-1">

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
        </div>

        @include('partials.auth-footer')
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

    document.addEventListener('DOMContentLoaded', function () {
        // The shared partial ships with class="hidden" because it was built to be
        // one of several toggled tabs. On its own page it should always be visible.
        const view = document.getElementById('view-alerts');
        if (view) view.classList.remove('hidden');
        alertsRenderAll();
    });
</script>
@endsection
