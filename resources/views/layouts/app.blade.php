<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-sans antialiased">
        <div class="app-shell pb-20 sm:pb-6">
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-white/40 bg-white/70 backdrop-blur-md shadow-sm">
                    <div class="max-w-lg mx-auto py-4 px-4 sm:px-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="max-w-lg mx-auto w-full px-4 sm:px-6 py-6">
                {{ $slot }}
            </main>

            @if (! ($hideBottomNav ?? false))
                <x-bottom-nav :active="$bottomNavActive ?? ''" />
            @endif
        </div>
    </body>
</html>
