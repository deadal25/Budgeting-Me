<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Budgeting-Me') }} - Catatan Keuangan Pribadi Cerdas</title>

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
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Alpine.js & Tailwind -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen flex flex-col">
        <div class="flex-1 flex flex-col">
            @include('layouts.navigation')

            <!-- Global Toast / Alert Notifications -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 shadow-sm flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="font-medium text-sm">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="text-emerald-600 hover:text-emerald-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('new_password_info'))
                    <div x-data="{ copied: false }" class="mb-4 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 dark:bg-amber-950/40 dark:border-amber-600/50 p-4.5 text-amber-950 dark:text-amber-100 shadow-md">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl shrink-0 shadow-inner">
                                    🔑
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 dark:bg-amber-800 dark:text-amber-100">
                                            Informasi Kata Sandi Pengguna
                                        </span>
                                    </div>
                                    <p class="text-xs text-amber-900 dark:text-amber-200 mt-1">
                                        Kata sandi baru untuk <strong>{{ session('new_password_info')['user_name'] }}</strong> (<span class="font-mono">{{ session('new_password_info')['email'] }}</span>):
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="font-mono font-black text-base px-3.5 py-1.5 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-600 rounded-xl text-slate-900 dark:text-amber-300 select-all shadow-inner tracking-wider">
                                    {{ session('new_password_info')['password'] }}
                                </span>
                                <button 
                                    type="button" 
                                    @click="navigator.clipboard.writeText('{{ session('new_password_info')['password'] }}'); copied = true; setTimeout(() => copied = false, 2500)" 
                                    class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm"
                                >
                                    <span x-text="copied ? '✅ Tersalin!' : '📋 Salin Sandi'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-rose-800 shadow-sm flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <p class="font-medium text-sm">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="text-rose-600 hover:text-rose-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 rounded-xl bg-amber-50 border border-amber-200 p-4 text-amber-800 shadow-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-500 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <span class="font-bold text-sm">Harap periksa kesalahan berikut:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1 text-amber-700 ps-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/80 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/70 dark:border-slate-800 shadow-xs">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1 pb-12">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-500 dark:text-slate-400 mt-auto">
                <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">Budgeting-Me</span>
                        <span>&copy; {{ date('Y') }} Hak Cipta Dilindungi.</span>
                    </div>
                    <div class="flex items-center gap-4 text-slate-400 dark:text-slate-500">
                        <span>Aplikasi Pencatatan Keuangan Pribadi Siap Produksi</span>
                    </div>
                </div>
            </footer>
        </div>

        @stack('scripts')
    </body>
</html>
