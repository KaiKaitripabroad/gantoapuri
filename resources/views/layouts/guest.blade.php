<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased min-h-screen app-shell flex flex-col items-center justify-center p-4 sm:p-6 relative overflow-x-hidden">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-32 -right-24 h-72 w-72 rounded-full bg-brand-400/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-16 h-64 w-64 rounded-full bg-accent-400/25 blur-3xl"></div>
        </div>

        <div class="relative w-full max-w-md">
            <div class="mb-8 text-center">
                <a href="/" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/80 px-4 py-2 shadow-card backdrop-blur-sm border border-white/60">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-lg font-bold text-white">G</span>
                    <span class="font-semibold text-slate-800 tracking-tight">{{ config('app.name', 'GroupTask') }}</span>
                </a>
            </div>

            <div class="card-surface p-6 sm:p-8 shadow-lg">
                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-slate-500">グループでタスクとガントを共有</p>
        </div>
    </body>
</html>
