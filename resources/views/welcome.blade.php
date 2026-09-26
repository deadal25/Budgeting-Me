<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Budgeting-Me — Catat & Kelola Keuangan Pribadi dengan Mudah</title>

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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen selection:bg-emerald-500 selection:text-white transition-colors duration-200">
    <!-- Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors duration-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-500/25 group-hover:scale-105 transition duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 dark:from-emerald-400 dark:via-teal-300 dark:to-cyan-400 bg-clip-text text-transparent">Budgeting-Me</span>
                    <span class="text-[10px] tracking-wider uppercase font-semibold text-emerald-600 dark:text-emerald-400/80">Smart Financial App</span>
                </div>
            </a>

            <div class="flex items-center gap-2 sm:gap-4">
                <a href="#quick-form" class="hidden md:inline-flex items-center text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition">
                    Form Cepat
                </a>
                <a href="#fitur" class="hidden md:inline-flex items-center text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition">
                    Fitur
                </a>
                <a href="#demo-info" class="hidden md:inline-flex items-center text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition">
                    Akun Demo
                </a>

                <!-- Theme Switcher in Navbar -->
                <x-theme-toggle :showLabel="false" />

                @if (Route::has('login'))
                    <div class="flex items-center gap-2 sm:gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-500/25 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white shadow-md shadow-emerald-500/20 transition">
                                    Daftar Akun
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Hero & Public Form Section -->
    <main class="relative pt-28 pb-20 overflow-hidden landing-hero-bg">
        <!-- Background Glow Orbs -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 right-10 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Alert if public transaction success -->
            @if(session('public_success'))
                <div class="mb-8 max-w-4xl mx-auto p-4 rounded-2xl bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-500/40 text-emerald-900 dark:text-emerald-200 shadow-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-emerald-900 dark:text-emerald-100">Transaksi Berhasil Dicatat!</p>
                            <p class="text-xs sm:text-sm text-emerald-800 dark:text-emerald-300">{{ session('public_success') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shrink-0 ms-2">
                        Masuk Akun
                    </a>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Hero Text & Value Proposition -->
                <div class="lg:col-span-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-6">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Solusi Pencatatan Keuangan Terkini
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-slate-900 dark:text-white leading-tight">
                        Kendalikan Uang Anda, <br class="hidden sm:inline">
                        <span class="bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 dark:from-emerald-400 dark:via-teal-300 dark:to-cyan-400 bg-clip-text text-transparent">Wujudkan Impian</span>
                    </h1>

                    <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Pencatatan keuangan cepat tanpa ribet. Anda bahkan bisa langsung mencatat transaksi di halaman ini tanpa login terlebih dahulu — transaksi otomatis tersimpan ke akun Anda secara aman!
                    </p>

                    <!-- Feature Badges -->
                    <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-3 max-w-lg mx-auto lg:mx-0 text-left">
                        <div class="p-3 rounded-xl bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 shadow-xs backdrop-blur-xs">
                            <div class="text-emerald-600 dark:text-emerald-400 font-bold text-sm">⚡ Input Publik</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Catat instan tanpa login</div>
                        </div>
                        <div class="p-3 rounded-xl bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 shadow-xs backdrop-blur-xs">
                            <div class="text-teal-600 dark:text-teal-400 font-bold text-sm">🎯 Budgetinku</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Limit & alert boros</div>
                        </div>
                        <div class="p-3 rounded-xl bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 shadow-xs backdrop-blur-xs">
                            <div class="text-cyan-600 dark:text-cyan-400 font-bold text-sm">📄 Export PDF</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Laporan siap cetak</div>
                        </div>
                    </div>

                    <!-- Platform Counter -->
                    <div class="mt-8 flex items-center justify-center lg:justify-start gap-8 text-slate-600 dark:text-slate-300 border-t border-slate-200 dark:border-slate-800/80 pt-6">
                        <div>
                            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalUsers) }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pengguna Terdaftar</div>
                        </div>
                        <div class="w-px h-8 bg-slate-200 dark:bg-slate-800"></div>
                        <div>
                            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalTransactions) }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Transaksi</div>
                        </div>
                        <div class="w-px h-8 bg-slate-200 dark:bg-slate-800"></div>
                        <div>
                            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">100%</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Aman & Terverifikasi</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Public Transaction Form Card -->
                <div id="quick-form" class="lg:col-span-6">
                    <div class="rounded-3xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-2xl p-6 sm:p-8 backdrop-blur-xl relative overflow-hidden">
                        <!-- Top Accent Line -->
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-500"></div>

                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span>Form Transaksi Cepat</span>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30">Publik</span>
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    Ketik email terdaftar Anda untuk mencatat transaksi tanpa perlu login terlebih dahulu.
                                </p>
                            </div>
                        </div>

                        <form id="publicTransactionForm" method="POST" action="{{ route('public.transaction.store') }}" class="space-y-4">
                            @csrf

                            <!-- Email Field with Real-Time AJAX Check -->
                            <div>
                                <label for="public_email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Email Akun Terdaftar <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        type="email" 
                                        id="public_email" 
                                        name="email" 
                                        required
                                        value="{{ old('email') }}"
                                        placeholder="nama@email.com (contoh: user@budgetingme.com)"
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/90 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent text-sm transition"
                                    >
                                    <!-- Spinner indicator -->
                                    <div id="emailSpinner" class="hidden absolute right-3.5 top-3.5">
                                        <svg class="animate-spin h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Real-time Status Notification Box -->
                                <div id="emailNotice" class="mt-2 text-xs rounded-lg p-2.5 hidden transition">
                                    <div id="emailNoticeText" class="font-medium"></div>
                                </div>

                                @error('email')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Transaction Type (Pemasukan / Pengeluaran) -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Jenis Transaksi <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="type" value="income" class="peer sr-only" {{ old('type') == 'income' ? 'checked' : '' }}>
                                        <div class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/60 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-600/20 peer-checked:border-emerald-500 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-400 text-slate-700 dark:text-slate-300 text-center text-sm font-semibold transition flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                            Pemasukan
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="type" value="expense" class="peer sr-only" {{ old('type', 'expense') == 'expense' ? 'checked' : '' }}>
                                        <div class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/60 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-600/20 peer-checked:border-rose-500 peer-checked:text-rose-700 dark:peer-checked:text-rose-400 text-slate-700 dark:text-slate-300 text-center text-sm font-semibold transition flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                            Pengeluaran
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Category & Payment Method / Sumber Dana in 2 Cols -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Category Dropdown (Filtered Dynamically) -->
                                <div>
                                    <label for="category_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                        Kategori <span class="text-rose-500">*</span>
                                    </label>
                                    <select 
                                        id="category_id" 
                                        name="category_id" 
                                        required
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/90 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                    >
                                        <option value="" disabled selected>Pilih Kategori...</option>
                                        @foreach($categories as $category)
                                            <option 
                                                value="{{ $category->id }}" 
                                                data-type="{{ $category->type }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}
                                            >
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Payment Method / Sumber Dana -->
                                <div>
                                    <label for="public_payment_method" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                        Asal / Sumber Dana <span class="text-rose-500">*</span>
                                    </label>
                                    <select 
                                        id="public_payment_method" 
                                        name="payment_method" 
                                        required
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/90 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                    >
                                        @foreach($paymentMethods as $key => $method)
                                            <option value="{{ $key }}" {{ old('payment_method', 'Cash/Tunai') === $key ? 'selected' : '' }}>
                                                {{ $method['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Amount & Date in 2 Cols -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Amount (Nominal) -->
                                <div>
                                    <label for="public_amount" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                        Jumlah (Rp) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-3 text-slate-500 dark:text-slate-400 text-sm font-bold">Rp</span>
                                        <input 
                                            type="number" 
                                            id="public_amount" 
                                            name="amount" 
                                            required
                                            min="1"
                                            step="1"
                                            value="{{ old('amount') }}"
                                            placeholder="50.000"
                                            class="w-full ps-10 pe-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/90 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-semibold"
                                        >
                                    </div>
                                    @error('amount')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date -->
                                <div>
                                    <label for="public_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                        Tanggal Transaksi <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        type="date" 
                                        id="public_date" 
                                        name="date" 
                                        required
                                        value="{{ old('date', date('Y-m-d')) }}"
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/90 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                    >
                                    @error('date')
                                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes (Optional) -->
                            <div>
                                <label for="public_notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Catatan (Opsional)
                                </label>
                                <input 
                                    type="text" 
                                    id="public_notes" 
                                    name="notes" 
                                    value="{{ old('notes') }}" 
                                    placeholder="Contoh: Makan siang, bayar kost, belanja, dll."
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/90 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
                                >
                                @error('notes')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button (Disabled by default until valid email checked) -->
                            <div class="pt-2">
                                <button 
                                    type="submit" 
                                    id="submitBtn" 
                                    disabled
                                    class="w-full py-3.5 px-6 rounded-xl font-bold text-white bg-slate-400 dark:bg-slate-700 cursor-not-allowed transition duration-200 shadow-lg flex items-center justify-center gap-2 group"
                                >
                                    <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <span id="submitBtnText">Ketik Email Terdaftar Terlebih Dahulu</span>
                                </button>
                                <p class="text-center text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                                    Transaksi akan langsung masuk ke dashboard dan mempengaruhi saldo & Budgetinku akun Anda.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Demo Credentials Section -->
    <section id="demo-info" class="py-14 bg-slate-100/90 dark:bg-slate-950/70 border-y border-slate-200 dark:border-slate-800/80 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Akun Uji Coba (Demo) Siap Digunakan</h3>
                <p class="text-slate-600 dark:text-slate-400 text-xs sm:text-sm mt-1">Gunakan akun demo di bawah ini untuk mencoba input transaksi publik atau masuk ke dashboard user dan admin.</p>
            </div>

            <div class="max-w-md mx-auto">
                <!-- User Demo -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-md hover:border-emerald-500/50 transition">
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30">Akun Pengguna Demo</span>
                        <button onclick="fillDemoEmail('user@budgetingme.com')" class="text-xs text-emerald-600 dark:text-emerald-400 font-bold hover:underline">Gunakan di Form ↑</button>
                    </div>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">Nama:</span> <strong class="text-slate-900 dark:text-white">Budi Santoso</strong></div>
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">Email:</span> <code class="text-emerald-700 dark:text-emerald-300 font-mono text-xs">user@budgetingme.com</code></div>
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">Password:</span> <code class="text-emerald-700 dark:text-emerald-300 font-mono text-xs">password</code></div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 flex items-center gap-1">
                            Masuk Akun User &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-20 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider">Fitur Unggulan</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white mt-3">Segala Kemudahan Mengatur Finansial Pribadi</h2>
                <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base mt-2">Didesain untuk memudahkan Anda mencatat uang masuk dan keluar setiap hari secara konsisten.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 shadow-md hover:border-emerald-500/50 transition hover:-translate-y-1 duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Input Publik Real-Time</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                        Cukup ketik email Anda di halaman depan, sistem memvalidasi seketika lewat AJAX. Jika cocok, transaksi langsung tercatat tanpa repot masuk akun terlebih dahulu.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 shadow-md hover:border-teal-500/50 transition hover:-translate-y-1 duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-teal-100 dark:bg-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Fitur "Budgetinku" & Peringatan</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                        Atur batas maksimal pengeluaran bulanan Anda. Dapatkan notifikasi visual pintar: Hijau (aman), Kuning (70-99% hati-hati jangan boros), dan Merah (limit terlampaui).
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/80 shadow-md hover:border-cyan-500/50 transition hover:-translate-y-1 duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-100 dark:bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Laporan Lengkap & Ekspor PDF</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                        Filter transaksi berdasarkan tanggal, kategori, dan jenis nominal. Ekspor seluruh riwayat pengeluaran Anda ke dalam file PDF rapi siap cetak kapan saja.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 py-10 text-slate-500 dark:text-slate-400 text-xs transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-900 dark:text-white text-sm">Budgeting-Me</span>
                <span>&copy; {{ date('Y') }} Hak Cipta Dilindungi.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('login') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400">Masuk Akun</a>
                <a href="{{ route('register') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400">Daftar Baru</a>
                <a href="#quick-form" class="hover:text-emerald-600 dark:hover:text-emerald-400">Form Publik</a>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Real-Time AJAX Email Check and Dynamic Category Filtering -->
    <script>
        const emailInput = document.getElementById('public_email');
        const emailSpinner = document.getElementById('emailSpinner');
        const emailNotice = document.getElementById('emailNotice');
        const emailNoticeText = document.getElementById('emailNoticeText');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const categorySelect = document.getElementById('category_id');
        const typeRadios = document.querySelectorAll('input[name="type"]');

        let debounceTimer;

        // Auto fill demo helper
        function fillDemoEmail(email) {
            emailInput.value = email;
            checkEmailAvailability(email);
            window.location.hash = '#quick-form';
        }

        // Email validation listener
        emailInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const email = this.value.trim();
            if (!email || !validateEmailFormat(email)) {
                resetEmailState();
                return;
            }
            debounceTimer = setTimeout(() => {
                checkEmailAvailability(email);
            }, 400);
        });

        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && validateEmailFormat(email)) {
                checkEmailAvailability(email);
            }
        });

        function validateEmailFormat(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function resetEmailState() {
            emailNotice.classList.add('hidden');
            emailNotice.className = 'mt-2 text-xs rounded-lg p-2.5 hidden transition';
            submitBtn.disabled = true;
            submitBtn.className = "w-full py-3.5 px-6 rounded-xl font-bold text-white bg-slate-400 dark:bg-slate-700 cursor-not-allowed transition duration-200 shadow-lg flex items-center justify-center gap-2";
            submitBtnText.textContent = "Ketik Email Terdaftar Terlebih Dahulu";
        }

        async function checkEmailAvailability(email) {
            emailSpinner.classList.remove('hidden');

            try {
                const response = await fetch(`/check-email?email=${encodeURIComponent(email)}`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();

                emailSpinner.classList.add('hidden');
                emailNotice.classList.remove('hidden');

                if (data.registered) {
                    // Valid registered email
                    emailNotice.className = "mt-2 text-xs rounded-xl p-3 bg-emerald-50 dark:bg-emerald-950/90 border border-emerald-300 dark:border-emerald-500 text-emerald-800 dark:text-emerald-200 transition";
                    emailNoticeText.innerHTML = `<span class="font-bold">✓ Email terdaftar:</span> Transaksi ini akan otomatis tercatat di akun <strong>${data.name}</strong>.`;

                    // Enable submit button
                    submitBtn.disabled = false;
                    submitBtn.className = "w-full py-3.5 px-6 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 cursor-pointer shadow-lg shadow-emerald-500/25 transition duration-200 flex items-center justify-center gap-2 transform active:scale-98";
                    submitBtnText.textContent = "Simpan Transaksi Sekarang";
                } else {
                    // Not registered
                    emailNotice.className = "mt-2 text-xs rounded-xl p-3 bg-rose-50 dark:bg-rose-950/90 border border-rose-300 dark:border-rose-500 text-rose-800 dark:text-rose-200 transition";
                    emailNoticeText.innerHTML = `<span>✗ Email belum terdaftar. Periksa kembali penulisan email atau <a href="{{ route('register') }}" class="underline font-bold text-rose-700 dark:text-rose-100 hover:text-rose-900 dark:hover:text-white">daftar akun di sini</a>.</span>`;

                    // Disable submit button
                    submitBtn.disabled = true;
                    submitBtn.className = "w-full py-3.5 px-6 rounded-xl font-bold text-white bg-slate-400 dark:bg-slate-700 cursor-not-allowed transition duration-200 shadow-lg flex items-center justify-center gap-2";
                    submitBtnText.textContent = "Email Belum Terdaftar";
                }
            } catch (err) {
                emailSpinner.classList.add('hidden');
                console.error("Gagal memeriksa email:", err);
            }
        }

        // Dynamic category filter based on selected type (income/expense)
        function filterCategories() {
            const selectedType = document.querySelector('input[name="type"]:checked')?.value || 'expense';
            const options = categorySelect.querySelectorAll('option:not([disabled])');
            let firstVisible = null;

            options.forEach(opt => {
                if (opt.getAttribute('data-type') === selectedType) {
                    opt.style.display = '';
                    if (!firstVisible) firstVisible = opt;
                } else {
                    opt.style.display = 'none';
                    if (opt.selected) {
                        opt.selected = false;
                    }
                }
            });

            if (firstVisible && !categorySelect.value) {
                firstVisible.selected = true;
            }
        }

        typeRadios.forEach(radio => {
            radio.addEventListener('change', filterCategories);
        });

        // Initialize category filtering on page load
        filterCategories();

        // Check email on page load if old value exists
        if (emailInput.value && validateEmailFormat(emailInput.value)) {
            checkEmailAvailability(emailInput.value);
        }
    </script>
</body>
</html>
