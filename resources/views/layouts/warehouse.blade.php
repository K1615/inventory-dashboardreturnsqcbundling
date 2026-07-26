<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ERP Inventory Management System')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navyBlue: '#1E3A8A',
                        emeraldGreen: '#10B981',
                        emeraldHover: '#059669',
                        voidRed: '#EF4444',
                        voidHover: '#DC2626'
                    }
                }
            }
        }
    </script>
    @include('partials.authenticated-fetch')
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen">

    <!-- The Submodule Blade View gets injected right here -->
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
