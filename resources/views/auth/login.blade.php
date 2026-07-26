<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | {{ config('app.name', 'ERP Inventory Management System') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="min-h-screen grid lg:grid-cols-2">
        <section class="hidden lg:flex bg-[#1E3A8A] text-white p-12 xl:p-20 flex-col justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-blue-200">ERP Inventory</p>
                <h1 class="mt-5 max-w-xl text-4xl xl:text-5xl font-bold leading-tight">
                    Inventory operations in one secure workspace.
                </h1>
                <p class="mt-6 max-w-lg text-blue-100 leading-relaxed">
                    Sign in to manage inventory, warehouse movements, alerts, returns, quality control, and bundling.
                </p>
            </div>
            <p class="text-sm text-blue-200">ERP Inventory Management System</p>
        </section>

        <section class="flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-8">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-700">ERP Inventory</p>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/60 p-7 sm:p-9">
                    <h2 class="text-2xl font-bold text-slate-900">Welcome back</h2>
                    <p class="mt-2 text-sm text-slate-500">Enter your credentials to access the ERP dashboard.</p>

                    @if ($errors->any())
                        <div role="alert" class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                class="mt-2 w-full rounded-lg border border-slate-300 px-3.5 py-2.5 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                class="mt-2 w-full rounded-lg border border-slate-300 px-3.5 py-2.5 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                            >
                        </div>

                        <label class="flex items-center gap-2.5 text-sm text-slate-600">
                            <input
                                name="remember"
                                type="checkbox"
                                value="1"
                                @checked(old('remember'))
                                class="h-4 w-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600"
                            >
                            Remember me
                        </label>

                        <button type="submit" class="w-full rounded-lg bg-[#1E3A8A] px-4 py-2.5 font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                            Sign in
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
