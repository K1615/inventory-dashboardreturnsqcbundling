<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ERP Inventory Management System') }}</title>

    <!-- This will pull in the CSRF token, Tailwind CDN, Chart.js, and specific styles from submodule.blade.php -->
    @yield('head')
    @include('partials.authenticated-fetch')
</head>
<body class="antialiased bg-gray-50">
    
    <!-- This will pull in the entire dashboard layout (sidebar, main content, modals) -->
    @yield('content')

</body>
</html>
