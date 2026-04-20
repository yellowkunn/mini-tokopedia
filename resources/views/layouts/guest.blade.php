<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Toko Online')</title>
        
        <!-- Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Additional Styles -->
        @stack('styles')
    </head>
    <body class="bg-gray-100 min-h-screen flex flex-col">
            <div class="max-w-[1200px] mx-auto px-4 py-4 lg:py-6">
                {{ $slot }}
            </div>
    </body>
</html>
