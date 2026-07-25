<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sign in') — {{ config('app.name', 'ERP Inventory') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navyBlue: '#1E3A8A',
                        emeraldGreen: '#10B981',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <main class="min-h-screen grid lg:grid-cols-2">
        <section class="hidden lg:flex bg-navyBlue text-white p-12 xl:p-20 flex-col justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-blue-200">ERP Inventory</p>
                <h1 class="mt-4 text-4xl font-bold leading-tight">Warehouse operations in one secure workspace.</h1>
                <p class="mt-5 max-w-lg text-blue-100 leading-relaxed">
                    Manage inventory, movements, returns, bundling, transfers, alerts, and reorders.
                </p>
            </div>
            <p class="text-sm text-blue-200">Authorized personnel only</p>
        </section>

        <section class="flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-navyBlue">ERP Inventory</p>
                    <h1 class="mt-2 text-2xl font-bold text-slate-900">Management System</h1>
                </div>

                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
