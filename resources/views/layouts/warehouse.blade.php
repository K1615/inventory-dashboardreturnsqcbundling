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
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen">

    <!-- The Submodule Blade View gets injected right here -->
    @yield('content')

</body>
</html>