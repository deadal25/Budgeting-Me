<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Selamat Datang, {{ Auth::user()->name }}!</span>
                    <span class="text-xl">👋</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Ringkasan keuangan dan batas pengeluaran bulan {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}.
                </p>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                @if($hasUnpaidDebt)
                    <button 
                        onclick="openDebtReminderModal()" 
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 transition shadow-xs"
                    >
                        <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span>Pengingat Utang ({{ $unpaidDebtsCount }})</span>
                    </button>
                @endif
                <button 
                    onclick="openAddModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white shadow-md shadow-emerald-500/20 transition active:scale-95"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Transaksi
                </button>
                <a 
                    href="{{ route('debts.index') }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 shadow-xs transition"
                >
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Catatan Utang
                </a>
                <a 
                    href="{{ route('budgets.index') }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 shadow-xs transition"
                >
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    Atur Budgetinku
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Admin Mode Switcher Banner (if admin) -->
        @if(Auth::user()->isAdmin())
            <div class="rounded-3xl p-5 bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 text-white shadow-lg border border-purple-500/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/25 border border-purple-400/40 text-purple-200 flex items-center justify-center text-2xl shrink-0 shadow-inner">
                        👑
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-black text-base text-white">Mode Keuangan Pribadi Administrator</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-500 text-white shadow-xs">Admin</span>
                        </div>
                        <p class="text-xs text-purple-200/90 mt-1 leading-relaxed">
                            Anda sedang berada di mode pencatatan keuangan pribadi Anda sendiri. Semua fitur budget, transaksi, dan utang bekerja khusus untuk Anda.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('admin.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-black bg-white text-purple-950 hover:bg-purple-50 shadow-md transition flex items-center gap-2">
                        <span>🛡️ Buka Panel Pemantauan Seluruh Pengguna</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @endif

        <!-- 1. KOTAK RINGKASAN UTAMA: PEMASUKAN, PENGELUARAN, TOTAL UANG, TOTAL UTANG -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Kotak 1: Pemasukan Bulan Ini -->
            <div class="rounded-3xl bg-white border border-slate-200/90 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Pemasukan Bulan Ini</span>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-emerald-600">Rp {{ number_format($monthIncome, 0, ',', '.') }}</div>
                    <p class="text-[11px] text-slate-400 mt-1 font-medium">Total uang masuk bulan berjalan</p>
                </div>
            </div>

            <!-- Kotak 2: Pengeluaran Bulan Ini -->
            <div class="rounded-3xl bg-white border border-slate-200/90 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Pengeluaran Bulan Ini</span>
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-rose-600">Rp {{ number_format($monthExpense, 0, ',', '.') }}</div>
                    <p class="text-[11px] text-slate-400 mt-1 font-medium">Total uang keluar bulan berjalan</p>
                </div>
            </div>

            <!-- Kotak 3: Total Uang (Saldo Kumulatif) -->
            <div class="rounded-3xl bg-white border border-slate-200/90 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Total Uang (Saldo)</span>
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black {{ $allTimeBalance >= 0 ? 'text-indigo-600' : 'text-rose-600' }}">
                        Rp {{ number_format($allTimeBalance, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1 font-medium">Saldo riil seluruh akun aktif</p>
                </div>
            </div>

            <!-- Kotak 4: Total Utang Saya (Dengan Pesan Bayar Saat Gajian) -->
            <div class="rounded-3xl bg-white border-2 {{ $totalUnpaidDebt > 0 ? 'border-rose-300 ring-2 ring-rose-50 shadow-md' : 'border-slate-200/90 shadow-xs' }} p-5 transition flex flex-col justify-between relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between gap-1 mb-1">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-rose-600">Total Utang Saya</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-gradient-to-r from-amber-400 to-amber-500 text-amber-950 shadow-2xs animate-pulse shrink-0">
                            💰 Bayar Saat Gajian!
                        </span>
                    </div>
                    <div class="mt-2.5">
                        <div class="text-2xl font-black {{ $totalUnpaidDebt > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            Rp {{ number_format($totalUnpaidDebt, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] font-bold {{ $totalUnpaidDebt > 0 ? 'text-amber-800' : 'text-slate-400' }} mt-1">
                            @if($totalUnpaidDebt > 0)
                                {{ $unpaidDebtsCount }} utang aktif &bull; Bayar saat gajian!
                            @else
                                Tidak ada tanggungan utang 🎉
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <button type="button" onclick="openDashboardAddDebtModal()" class="font-extrabold text-xs text-rose-600 hover:text-rose-800 transition">
                        + Catat Utang
                    </button>
                    <a href="{{ route('debts.index') }}" class="font-extrabold text-xs text-slate-700 hover:text-slate-900 flex items-center gap-1 transition">
                        <span>Rincian</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. KATEGORI BUDGETING & GRAFIK PENGELUARAN (LANGSUNG DIBAWAH KOTAK 4 RINGKASAN DI BAGIAN PALING ATAS) -->
        <div class="space-y-6">
            <!-- Budget Alert Banner if set -->
            @if($overallBudget)
                <div class="rounded-3xl p-5 sm:p-6 shadow-xs border transition
                    @if($overallBudget->status === 'danger')
                        bg-rose-50 border-rose-200 text-rose-900
                    @elseif($overallBudget->status === 'warning')
                        bg-amber-50 border-amber-200 text-amber-900
                    @else
                        bg-emerald-50 border-emerald-200 text-emerald-900
                    @endif
                ">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xs
                                @if($overallBudget->status === 'danger') bg-rose-500 text-white
                                @elseif($overallBudget->status === 'warning') bg-amber-500 text-white
                                @else bg-emerald-500 text-white @endif">
                                @if($overallBudget->status === 'danger')
                                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @elseif($overallBudget->status === 'warning')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="font-black text-base sm:text-lg">Budgetinku — Batas Pengeluaran Bulan Ini</h2>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black
                                        @if($overallBudget->status === 'danger') bg-rose-200 text-rose-800 border border-rose-300
                                        @elseif($overallBudget->status === 'warning') bg-amber-200 text-amber-800 border border-amber-300
                                        @else bg-emerald-200 text-emerald-800 border border-emerald-300 @endif">
                                        {{ $overallBudget->status_label }}
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm mt-1 opacity-90 leading-relaxed">
                                    @if($overallBudget->status === 'danger')
                                        <strong>Limit Terlampaui!</strong> Total pengeluaran telah melebihi batas anggaran sebesar <strong>Rp {{ number_format(abs($overallBudget->remaining_amount), 0, ',', '.') }}</strong>.
                                    @elseif($overallBudget->status === 'warning')
                                        <strong>Hati-hati jangan boros!</strong> Anda telah menggunakan <strong>{{ $overallBudget->percentage }}%</strong> dari anggaran. Sisa: <strong>Rp {{ number_format($overallBudget->remaining_amount, 0, ',', '.') }}</strong>.
                                    @else
                                        Pengeluaran terkendali dengan baik. <strong>Sisa anggaran bulan ini: Rp {{ number_format($overallBudget->remaining_amount, 0, ',', '.') }}</strong>.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 self-end md:self-center shrink-0">
                            <a href="{{ route('budgets.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-black bg-white text-slate-900 hover:bg-slate-50 border border-slate-200 shadow-xs transition">
                                Atur Budgetinku &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="flex justify-between text-xs font-bold mb-1.5 opacity-80">
                            <span>Terpakai: Rp {{ number_format($overallBudget->spent_amount, 0, ',', '.') }}</span>
                            <span>Limit: Rp {{ number_format($overallBudget->limit_amount, 0, ',', '.') }} ({{ $overallBudget->percentage }}%)</span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-slate-200/80 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500
                                @if($overallBudget->status === 'danger') bg-rose-600
                                @elseif($overallBudget->status === 'warning') bg-amber-500
                                @else bg-emerald-500 @endif"
                                style="width: {{ min($overallBudget->percentage, 100) }}%">
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-3xl p-5 bg-gradient-to-r from-teal-50 to-emerald-50 border border-teal-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-2xl bg-teal-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-black text-teal-950 text-sm sm:text-base">Belum Menentukan Limit Anggaran "Budgetinku"</h2>
                            <p class="text-xs text-teal-800">Tentukan batas pengeluaran bulan ini agar sistem memberi notifikasi dan lencana peringatan saat mendekati batas boros.</p>
                        </div>
                    </div>
                    <a href="{{ route('budgets.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-black bg-teal-600 hover:bg-teal-500 text-white shadow-xs shrink-0 transition">
                        + Pasang Limit Anggaran
                    </a>
                </div>
            @endif

            <!-- Grid 2 Kolom: Kiri Grafik Distribusi, Kanan Kategori Budgeting -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Kolom Kiri: Grafik Distribusi Pengeluaran -->
                <div class="lg:col-span-6 bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="font-black text-slate-900 text-base">Grafik Pengeluaran per Kategori</h2>
                            <p class="text-xs text-slate-500 font-medium">Distribusi pengeluaran berdasarkan kategori bulan ini</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-black bg-slate-100 text-slate-700 border border-slate-200">
                            {{ count($chartLabels) }} Kategori
                        </span>
                    </div>

                    @if(count($chartLabels) > 0)
                        <div class="relative flex-1 flex items-center justify-center min-h-[260px] py-4">
                            <canvas id="categoryExpenseChart" class="max-h-[260px]"></canvas>
                        </div>
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-8 text-slate-400 min-h-[220px]">
                            <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                            <p class="text-sm font-bold text-slate-700">Belum ada data pengeluaran bulan ini</p>
                            <p class="text-xs text-slate-400 mt-1">Catat transaksi pengeluaran untuk melihat grafik kategori.</p>
                        </div>
                    @endif
                </div>

                <!-- Kolom Kanan: Kategori Budgeting ("Budgetinku") -->
                <div class="lg:col-span-6 bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xs flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                        <div>
                            <h2 class="font-black text-slate-900 text-base">Kategori Budgeting ("Budgetinku")</h2>
                            <p class="text-xs text-slate-500 font-medium">Batas pengeluaran per kategori yang aktif</p>
                        </div>
                        <a href="{{ route('budgets.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-black bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition">
                            Atur Semua Limit &rarr;
                        </a>
                    </div>

                    @if($categoryBudgets->count() > 0)
                        <div class="space-y-3.5 flex-1">
                            @foreach($categoryBudgets as $cBudget)
                                <div class="p-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/70 hover:bg-white transition shadow-2xs">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3.5 h-3.5 rounded-full shrink-0 shadow-xs" style="background-color: {{ $cBudget->category->color ?? '#10B981' }}"></div>
                                            <span class="font-extrabold text-xs text-slate-900">{{ $cBudget->category->name }}</span>
                                        </div>
                                        <span class="text-xs font-black px-2 py-0.5 rounded-md
                                            @if($cBudget->status === 'danger') bg-rose-100 text-rose-800
                                            @elseif($cBudget->status === 'warning') bg-amber-100 text-amber-800
                                            @else bg-emerald-100 text-emerald-800 @endif">
                                            {{ $cBudget->status_label }}
                                        </span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden mb-1.5">
                                        <div class="h-full rounded-full transition-all duration-500
                                            @if($cBudget->status === 'danger') bg-rose-500
                                            @elseif($cBudget->status === 'warning') bg-amber-500
                                            @else bg-emerald-500 @endif"
                                            style="width: {{ min($cBudget->percentage, 100) }}%">
                                        </div>
                                    </div>
                                    <div class="flex justify-between text-[11px] font-semibold text-slate-600">
                                        <span>Terpakai: Rp {{ number_format($cBudget->spent_amount, 0, ',', '.') }}</span>
                                        <span>Limit: Rp {{ number_format($cBudget->limit_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-8 text-slate-400 min-h-[220px]">
                            <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-sm font-bold text-slate-700">Belum ada limit spesifik per kategori</p>
                            <p class="text-xs text-slate-400 mt-1">Anda bisa memasang batas untuk Makanan, Hiburan, dll.</p>
                            <a href="{{ route('budgets.index') }}" class="mt-3 px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white transition">
                                + Tambah Limit Kategori
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. KOTAK KHUSUS CATATAN UTANG SAYA (DENGAN PESAN BAYAR SAAT GAJIAN) -->
        @if($totalUnpaidDebt > 0)
            <div class="rounded-3xl bg-gradient-to-br from-white via-rose-50/30 to-amber-50/20 border-2 border-rose-300 shadow-md p-6 sm:p-7 transition relative overflow-hidden">
                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-3 max-w-2xl">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Rincian Utang Perlu Dilunasi
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-amber-400 to-amber-500 text-amber-950 shadow-xs animate-pulse">
                                💰 Pesan: Bayar Saat Gajian!
                            </span>
                        </div>

                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Utang Aktif Saat Ini</div>
                            <div class="text-3xl font-black text-rose-600 mt-1">
                                Rp {{ number_format($totalUnpaidDebt, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-900 text-xs sm:text-sm">
                            <span class="text-xl shrink-0">🔔</span>
                            <div class="leading-relaxed">
                                <strong class="font-bold text-amber-950">Jangan lupa bayar utang jika ada saat gajian!</strong>
                                <p class="text-xs text-amber-900/90 mt-0.5">
                                    Saat menerima gaji bulanan atau pemasukan baru, segera sisihkan untuk pelunasan utang agar cicilan tidak menumpuk.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row lg:flex-col gap-2.5 shrink-0 lg:w-72">
                        <button 
                            type="button"
                            onclick="openDashboardAddDebtModal()" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl font-bold text-xs sm:text-sm bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 text-white shadow-md shadow-rose-500/20 transition active:scale-95"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Catat Utang Baru
                        </button>
                        <a 
                            href="{{ route('debts.index') }}" 
                            class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-2xl font-bold text-xs bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 shadow-xs transition"
                        >
                            <span>Kelola Semua Catatan Utang</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Preview Daftar Utang -->
                <div class="mt-6 pt-5 border-t border-rose-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Utang Menunggu Pembayaran ({{ $unpaidDebts->count() }})</span>
                        <a href="{{ route('debts.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Lihat Semua &rarr;</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($unpaidDebts->take(6) as $debt)
                            <div class="p-3.5 rounded-2xl bg-white border border-rose-100 shadow-xs hover:border-rose-300 transition flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-bold text-xs text-slate-900 truncate max-w-[150px]">{{ $debt->title }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $debt->creditor ? 'Pemberi: ' . $debt->creditor : 'Utang' }}
                                    </div>
                                    <div class="font-black text-xs text-rose-600 mt-1">
                                        {{ $debt->formatted_remaining_amount }}
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <button 
                                        type="button" 
                                        onclick='openDashboardPayDebtModal(@json($debt))' 
                                        class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold shadow-xs transition flex items-center gap-1"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Bayar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- 4. SECTION: SALDO REKENING BANK & DOMPET DIGITAL (4 kategori untuk user biasa, lengkap untuk admin) -->
        <x-account-balances 
            :accounts="$accountBalances" 
            :title="$isAdmin ? 'Saldo Rekening Bank & Dompet Digital Platform' : 'Saldo Rekening & Dompet Digital Saya'"
            :subtitle="$isAdmin ? 'Rincian saldo seluruh rekening bank dan e-wallet secara lengkap.' : 'Rincian total saldo dibagi dalam 4 kelompok: E-Wallet, M-Banking, Cash/Tunai, dan Rekening Tabungan.'"
            :showFilterLink="true"
            context="user"
        />

        <!-- Section: Recent Transactions (Latest 10) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-base sm:text-lg">Transaksi Terbaru</h2>
                    <p class="text-xs text-slate-500">10 transaksi terakhir yang tercatat di akun Anda (termasuk dari form publik)</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('transactions.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center gap-1.5">
                        <span>Lihat Semua Riwayat</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-5">Tanggal</th>
                            <th class="py-3 px-5">Jenis</th>
                            <th class="py-3 px-5">Kategori</th>
                            <th class="py-3 px-5">Sumber Dana</th>
                            <th class="py-3 px-5">Nominal</th>
                            <th class="py-3 px-5">Catatan</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($recentTransactions as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-5 font-medium text-slate-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    @if($item->type === 'income')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                            Pemasukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                            Pengeluaran
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $item->category->color ?? '#64748B' }}"></span>
                                        <span class="font-semibold text-slate-800">{{ $item->category->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $item->payment_method_badge_class }}">
                                        {{ $item->payment_method_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 font-black whitespace-nowrap {{ $item->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $item->type === 'income' ? '+' : '-' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-slate-600 text-xs max-w-xs truncate">
                                    {{ $item->notes ?: '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    @if($item->isLockedFor(auth()->user()))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Pemasukan bawaan akun demo dilindungi dan tidak dapat diedit atau dihapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Terkunci (Demo)</span>
                                        </span>
                                    @else
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('transactions.edit', $item->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('transactions.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">
                                    <p class="text-sm font-semibold">Belum ada transaksi</p>
                                    <p class="text-xs mt-1">Mulai catat transaksi pertama Anda dengan menekan tombol Tambah Transaksi di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Add Transaction Modal -->
    <div id="addModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-900">Tambah Transaksi Baru</h3>
                <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('transactions.store') }}" class="mt-6 space-y-4">
                @csrf

                <!-- Type Selector -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="income" class="peer sr-only modal-type-radio" onchange="filterModalCategories('income')">
                            <div class="py-2.5 px-4 rounded-xl border border-slate-200 bg-slate-50 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 text-slate-600 text-center text-xs font-bold transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                Pemasukan
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="expense" checked class="peer sr-only modal-type-radio" onchange="filterModalCategories('expense')">
                            <div class="py-2.5 px-4 rounded-xl border border-slate-200 bg-slate-50 peer-checked:bg-rose-50 peer-checked:border-rose-500 peer-checked:text-rose-700 text-slate-600 text-center text-xs font-bold transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                Pengeluaran
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Category & Payment Method in 2 Cols -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Category -->
                    <div>
                        <label for="modal_category_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Kategori</label>
                        <select id="modal_category_id" name="category_id" required class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-type="{{ $category->type }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method / Sumber Dana -->
                    <div>
                        <label for="modal_payment_method" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Sumber / Asal Dana</label>
                        <select id="modal_payment_method" name="payment_method" required class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach($paymentMethods as $key => $method)
                                <option value="{{ $key }}" {{ old('payment_method', 'Cash/Tunai') === $key ? 'selected' : '' }}>
                                    {{ $method['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Amount -->
                <div>
                    <label for="modal_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Jumlah (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                        <input type="number" id="modal_amount" name="amount" required min="1" step="1" placeholder="50.000" class="w-full ps-10 rounded-xl border-slate-200 text-sm font-bold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Date -->
                <div>
                    <label for="modal_date" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input type="date" id="modal_date" name="date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <!-- Notes -->
                <div>
                    <label for="modal_notes" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Catatan (Opsional)</label>
                    <input type="text" id="modal_notes" name="notes" placeholder="Makan siang, bensin, dll." class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- POPUP PENGINGAT UTANG SAAT GAJIAN -->
    @if($hasUnpaidDebt)
    <div id="debtReminderModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border-2 border-rose-300 animate-in fade-in zoom-in duration-200">
            <!-- Header with close button -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-rose-500 text-white flex items-center justify-center shadow-md shadow-rose-500/20 shrink-0">
                        <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800">
                            🔔 Pengingat Finansial
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mt-1">Jangan Lupa Bayar Utang!</h3>
                    </div>
                </div>
                <button onclick="closeDebtReminderModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body Message -->
            <div class="mt-5 space-y-4">
                <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-rose-50 border border-amber-200">
                    <div class="text-xs font-bold text-amber-950 flex items-center gap-1.5">
                        <span>💰</span>
                        <span class="uppercase tracking-wider">Pesan Penting: Bayar Saat Gajian!</span>
                    </div>
                    <p class="text-xs text-amber-900 mt-1.5 leading-relaxed">
                        Hai <strong>{{ Auth::user()->name }}</strong>, jangan lupa bayar utang jika ada saat menerima gaji atau uang masuk. Sisihkan dana sekarang agar kewajiban tidak menumpuk!
                    </p>
                </div>

                <!-- Total Amount Highlight -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Total Utang Aktif Anda:</div>
                        <div class="text-2xl font-black text-rose-600">Rp {{ number_format($totalUnpaidDebt, 0, ',', '.') }}</div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                        {{ $unpaidDebtsCount }} Utang Belum Lunas
                    </span>
                </div>

                <!-- List preview -->
                <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    @foreach($unpaidDebts as $debt)
                        <div class="p-3 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-800">{{ $debt->title }}</span>
                                <span class="text-slate-400 text-[11px] block">{{ $debt->creditor ? 'Pemberi: ' . $debt->creditor : 'Utang' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-rose-600">{{ $debt->formatted_remaining_amount }}</span>
                                @if($debt->pay_on_salary)
                                    <span class="text-[10px] text-amber-600 font-semibold block">Bayar saat gajian</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <button 
                    type="button" 
                    onclick="closeDebtReminderModal()" 
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition text-center"
                >
                    Saya Mengerti / Nanti Saja
                </button>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a 
                        href="{{ route('debts.index') }}" 
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 text-white text-xs font-bold shadow-md shadow-rose-500/20 transition text-center"
                    >
                        Kelola & Bayar Sekarang &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- QUICK ADD DEBT MODAL ON DASHBOARD -->
    <div id="dashboardAddDebtModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Catat Utang Baru</h3>
                    <p class="text-xs text-slate-500">Masukkan detail utang atau pinjaman yang perlu dilunasi.</p>
                </div>
                <button onclick="closeDashboardAddDebtModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('debts.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="dash_add_title" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama / Keperluan Utang <span class="text-rose-500">*</span></label>
                    <input type="text" id="dash_add_title" name="title" required placeholder="Contoh: Utang Teman, Cicilan Laptop, Pinjaman Bank" class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="dash_add_creditor" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pemberi Pinjaman</label>
                        <input type="text" id="dash_add_creditor" name="creditor" placeholder="Contoh: Budi, SeaBank, dll." class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                    <div>
                        <label for="dash_add_due_date" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Jatuh Tempo (Opsional)</label>
                        <input type="date" id="dash_add_due_date" name="due_date" class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="dash_add_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Total Nominal Utang (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                            <input type="number" id="dash_add_amount" name="amount" required min="1" step="1" placeholder="500.000" class="w-full ps-10 rounded-xl border-slate-200 text-sm font-bold focus:border-rose-500 focus:ring-rose-500">
                        </div>
                    </div>
                    <div>
                        <label for="dash_add_paid_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Sudah Dicicil (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                            <input type="number" id="dash_add_paid_amount" name="paid_amount" value="0" min="0" step="1" class="w-full ps-10 rounded-xl border-slate-200 text-sm font-medium focus:border-rose-500 focus:ring-rose-500">
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="pay_on_salary" value="1" checked class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="text-xs font-bold text-amber-950">Bayar saat gajian!</span>
                            <p class="text-[11px] text-amber-800">Tandai utang ini agar otomatis muncul sebagai prioritas pelunasan saat menerima gaji.</p>
                        </div>
                    </label>
                </div>

                <div>
                    <label for="dash_add_notes" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Catatan Tambahan (Opsional)</label>
                    <textarea id="dash_add_notes" name="notes" rows="2" placeholder="Catatan nomor rekening atau perjanjian..." class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeDashboardAddDebtModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 text-white text-xs font-bold shadow-md shadow-rose-500/20 transition">
                        Simpan Catatan Utang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- QUICK PAY DEBT MODAL ON DASHBOARD -->
    <div id="dashboardPayDebtModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Bayar / Cicil Utang</h3>
                    <p id="dashPayModalSubtitle" class="text-xs text-slate-500 mt-0.5">Catat pembayaran angsuran utang Anda.</p>
                </div>
                <button onclick="closeDashboardPayDebtModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="dashPayDebtForm" method="POST" action="" class="mt-6 space-y-4">
                @csrf

                <!-- Quick Info Box -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Sisa Tagihan Saat Ini:</div>
                        <div id="dashPayModalRemainingText" class="text-xl font-black text-rose-600">Rp 0</div>
                    </div>
                    <button 
                        type="button" 
                        onclick="setDashboardFullPayAmount()" 
                        class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold transition"
                    >
                        Lunasi Penuh
                    </button>
                </div>

                <!-- Payment Amount -->
                <div>
                    <label for="dash_pay_payment_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Jumlah Pembayaran (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                        <input type="number" id="dash_pay_payment_amount" name="payment_amount" required min="1" step="1" placeholder="100.000" class="w-full ps-10 rounded-xl border-slate-200 text-sm font-bold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Payment Method / Bank / E-Wallet -->
                <div>
                    <label for="dash_pay_payment_method" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Dibayar Dari Rekening / Sumber Dana <span class="text-rose-500">*</span></label>
                    <select id="dash_pay_payment_method" name="payment_method" required class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach($paymentMethods as $key => $method)
                            <option value="{{ $key }}">
                                {{ $method['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label for="dash_pay_payment_date" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal Pembayaran</label>
                    <input type="date" id="dash_pay_payment_date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <!-- Synchronize with Transactions Checkbox -->
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="create_transaction" value="1" checked class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-xs font-bold text-emerald-950">Catat Otomatis ke Riwayat Pengeluaran</span>
                            <p class="text-[11px] text-emerald-800">Menambahkan transaksi pengeluaran kategori <em>"Bayar Utang/Cicilan"</em> dan mengurangi saldo rekening Anda.</p>
                        </div>
                    </label>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeDashboardPayDebtModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition">
                        Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script for Charts and Modals -->
    @push('scripts')
    <script>
        // Modal helpers for Transactions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            filterModalCategories('expense');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function filterModalCategories(type) {
            const select = document.getElementById('modal_category_id');
            const options = select.querySelectorAll('option');
            let firstSelected = false;

            options.forEach(opt => {
                if (opt.getAttribute('data-type') === type) {
                    opt.style.display = '';
                    if (!firstSelected) {
                        opt.selected = true;
                        firstSelected = true;
                    }
                } else {
                    opt.style.display = 'none';
                    if (opt.selected) opt.selected = false;
                }
            });
        }

        // Debt Modals & Reminder Popup
        let dashboardPayingDebt = null;

        function openDebtReminderModal() {
            const modal = document.getElementById('debtReminderModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeDebtReminderModal() {
            const modal = document.getElementById('debtReminderModal');
            if (modal) modal.classList.add('hidden');
            try {
                sessionStorage.setItem('debtReminderDismissed', '1');
            } catch(e) {}
        }

        function openDashboardAddDebtModal() {
            document.getElementById('dashboardAddDebtModal').classList.remove('hidden');
        }
        function closeDashboardAddDebtModal() {
            document.getElementById('dashboardAddDebtModal').classList.add('hidden');
        }

        function openDashboardPayDebtModal(debt) {
            dashboardPayingDebt = debt;
            const form = document.getElementById('dashPayDebtForm');
            form.action = `/debts/${debt.id}/pay`;
            
            document.getElementById('dashPayModalSubtitle').textContent = `Membayar: ${debt.title} (Kepada: ${debt.creditor || '-'})`;
            
            const remaining = Math.max(0, debt.amount - debt.paid_amount);
            document.getElementById('dashPayModalRemainingText').textContent = 'Rp ' + remaining.toLocaleString('id-ID');
            document.getElementById('dash_pay_payment_amount').value = remaining > 0 ? remaining : '';
            document.getElementById('dash_pay_payment_amount').max = remaining;

            document.getElementById('dashboardPayDebtModal').classList.remove('hidden');
        }
        function closeDashboardPayDebtModal() {
            document.getElementById('dashboardPayDebtModal').classList.add('hidden');
            dashboardPayingDebt = null;
        }

        function setDashboardFullPayAmount() {
            if (dashboardPayingDebt) {
                const remaining = Math.max(0, dashboardPayingDebt.amount - dashboardPayingDebt.paid_amount);
                document.getElementById('dash_pay_payment_amount').value = remaining;
            }
        }

        // Auto trigger popup on page load if user has unpaid debts and hasn't dismissed yet this session
        document.addEventListener('DOMContentLoaded', function() {
            @if($hasUnpaidDebt)
                try {
                    const dismissed = sessionStorage.getItem('debtReminderDismissed');
                    if (!dismissed) {
                        setTimeout(() => {
                            openDebtReminderModal();
                        }, 500);
                    }
                } catch(e) {
                    openDebtReminderModal();
                }
            @endif
        });

        // Render Chart.js if data exists
        @if(count($chartLabels) > 0)
        const ctx = document.getElementById('categoryExpenseChart').getContext('2d');
        const isDarkInitial = document.documentElement.classList.contains('dark');
        const categoryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: {!! json_encode($chartColors) !!},
                    borderWidth: 2,
                    borderColor: isDarkInitial ? '#1e293b' : '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: isDarkInitial ? '#cbd5e1' : '#475569',
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                size: 11,
                                family: "'Plus Jakarta Sans', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let val = context.raw || 0;
                                return ' ' + context.label + ': Rp ' + val.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Dynamic update on theme toggle
        window.addEventListener('theme-changed', function(e) {
            const isDark = e.detail && e.detail.theme === 'dark';
            if (categoryChart) {
                categoryChart.data.datasets[0].borderColor = isDark ? '#1e293b' : '#ffffff';
                if (categoryChart.options.plugins && categoryChart.options.plugins.legend) {
                    categoryChart.options.plugins.legend.labels.color = isDark ? '#cbd5e1' : '#475569';
                }
                categoryChart.update();
            }
        });
        @endif
    </script>
    @endpush
</x-app-layout>
