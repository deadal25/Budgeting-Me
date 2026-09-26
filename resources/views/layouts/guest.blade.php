<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Budgeting-Me') }} - Autentikasi</title>

        <!-- Inline Theme Script to Prevent FOUC -->
        <script>
            (function() {
                try {
                    const saved = localStorage.getItem('theme');
                    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (saved === 'dark' || (!saved && prefersDark)) {
                        document.documentElement.classList.add('dark');
                        document.documentElement.classList.remove('light');
                    } else {
                        document.documentElement.classList.add('light');
                        document.documentElement.classList.remove('dark');
                    }
                } catch(e) {}
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
    </head>
    <body class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col py-8 sm:py-12 px-4 sm:px-6 lg:px-8 selection:bg-emerald-500 selection:text-white relative overflow-x-hidden overflow-y-auto transition-colors duration-200">
        <!-- Top Right Theme Toggle (Fixed so it stays accessible when scrolling) -->
        <div class="fixed top-4 right-4 sm:top-5 sm:right-5 z-30">
            <x-theme-toggle :showLabel="true" />
        </div>

        <!-- Background Orbs (Fixed in background so they never block or cause scroll jumps) -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
            <div class="absolute top-10 left-1/2 -translate-x-1/2 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Main Auth Container (my-auto centers vertically on large screens, scrolls naturally on small screens) -->
        <div class="my-auto w-full relative z-10 py-4">
            <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
                <a href="/" class="inline-block hover:opacity-90 transition">
                    <x-application-logo class="justify-center" />
                </a>
            </div>

            <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="auth-card bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/80 backdrop-blur-xl py-8 px-6 shadow-2xl rounded-3xl sm:px-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
