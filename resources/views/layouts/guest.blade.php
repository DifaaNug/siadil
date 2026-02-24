<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="text-center mb-6">
                <h1 style="font-size: 42px; font-weight: bold; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.2); letter-spacing: 3px;">SIADIL</h1>
                <p style="color: rgba(255,255,255,0.9); font-size: 15px; margin-top: 8px; font-weight: 300;">Sistem Informasi Arsip Digital</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>

            <div class="mt-4 text-center">
                <p style="color: rgba(255,255,255,0.8); font-size: 13px;">© 2026 SIADIL</p>
            </div>
        </div>
    </body>
</html>
