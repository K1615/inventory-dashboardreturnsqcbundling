<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PC Parts Stock Manager')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { navyBlue: '#1E3A8A', emeraldGreen: '#10B981' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col md:flex-row">
    <aside class="w-full md:w-64 bg-navyBlue text-white flex flex-col justify-between md:sticky md:top-0 md:h-screen shadow-xl z-20 shrink-0">
        <div class="flex flex-col">
            <div class="p-6 border-b border-white/10">
                <h1 class="text-lg font-bold tracking-wide text-white">ERP Inventory</h1>
                <p class="text-[10px] text-blue-200 uppercase tracking-widest mt-0.5">Management System</p>
            </div>
            <nav class="p-4 flex flex-col gap-1.5 overflow-y-auto">
                <a href="#" class="px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all">Dashboard</a>
                <a href="#" class="px-4 py-2.5 text-xs font-bold text-white bg-white/10 border-l-4 border-emeraldGreen rounded-r-lg transition-all">Inventory Items</a>
                <a href="#" class="px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all">Stock Movements</a>
                <a href="#" class="px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all">Warehouse Layout</a>
                <a href="#" class="px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all">Alerts & Reorders</a>
                <a href="#" class="px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all">Returns & QC</a>
                <a href="#" class="px-4 py-2.5 text-xs font-semibold text-blue-100 rounded-lg hover:text-white hover:bg-white/10 transition-all">Product Bundling</a>
            </nav>
        </div>
        <div class="p-4 border-t border-white/10 bg-black/10 flex items-center justify-between text-sm font-semibold">
            <a href="#" class="hover:text-blue-200 transition-colors flex items-center gap-2 py-1 px-2 rounded hover:bg-white/5 text-xs">
                <span class="w-2 h-2 rounded-full bg-emeraldGreen"></span>Admin Panel
            </a>
            <button onclick="alert('Logging out...')" class="hover:text-red-300 text-white/80 transition-colors flex items-center gap-1 py-1 px-2 rounded hover:bg-white/5 text-xs">Logout</button>
        </div>
    </aside>
    <main class="flex-1 max-w-full p-6 space-y-8 overflow-y-auto h-screen">
        @yield('content')
    </main>
</body>
</html>