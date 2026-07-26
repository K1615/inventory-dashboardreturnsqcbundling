<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — {{ config('app.name', 'ERP Inventory Management System') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="antialiased bg-gray-50 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white border border-gray-200 rounded-xl shadow-sm p-8">
        <h1 class="text-xl font-bold text-gray-900 mb-1">Inventory ERP — Demo Login</h1>
        <p class="text-sm text-gray-500 mb-6">This is a simple demo login (no password). Enter a name and pick a role to continue.</p>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Your name (optional)</label>
                <input type="text" name="name" maxlength="60" placeholder="e.g. Jamie"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navyBlue focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-2">Log in as</label>
                <div class="grid grid-cols-1 gap-2">
                    <button type="submit" name="role" value="admin"
                        class="w-full text-left px-4 py-3 rounded-lg border-2 border-gray-200 hover:border-navyBlue hover:bg-navyBlue/5 transition-colors">
                        <span class="block font-bold text-gray-900">Admin</span>
                        <span class="block text-xs text-gray-500">Full access — approvals, config, user management</span>
                    </button>
                    <button type="submit" name="role" value="manager"
                        class="w-full text-left px-4 py-3 rounded-lg border-2 border-gray-200 hover:border-navyBlue hover:bg-navyBlue/5 transition-colors">
                        <span class="block font-bold text-gray-900">Manager</span>
                        <span class="block text-xs text-gray-500">Can approve/void, edit limits — no user management or bundling config</span>
                    </button>
                    <button type="submit" name="role" value="staff"
                        class="w-full text-left px-4 py-3 rounded-lg border-2 border-gray-200 hover:border-navyBlue hover:bg-navyBlue/5 transition-colors">
                        <span class="block font-bold text-gray-900">Staff</span>
                        <span class="block text-xs text-gray-500">View-only on approvals/config — can submit requests &amp; process QC</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

</body>
</html>
