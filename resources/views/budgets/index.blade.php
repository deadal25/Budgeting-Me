<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Fitur "Budgetinku"</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">Limit Anggaran</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Atur batas maksimal pengeluaran bulanan dan pantau peringatan agar tidak boros.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <button 
                    onclick="openBudgetModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-gradient-to-r from-teal-600 to-emerald-500 hover:from-teal-500 hover:to-emerald-400 text-white shadow-md shadow-teal-500/20 transition active:scale-95"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Pasang Limit Anggaran
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Month and Year Selector -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="font-bold text-slate-900 text-sm sm:text-base">
                        Periode: {{ \Carbon\Carbon::create()->month($selectedMonth)->locale('id')->isoFormat('MMMM') }} {{ $selectedYear }}
                    </h2>
                    <p class="text-xs text-slate-500">Pilih periode bulan dan tahun untuk melihat dan mengatur anggaran.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('budgets.index') }}" class="flex items-center gap-2">
                <select name="month" class="text-xs rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="text-xs rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500">
                    @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                    Lihat
                </button>
            </form>
        </div>

        <!-- Section 1: Overall Monthly Spending Limit -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span>1. Batas Maksimal Pengeluaran Keseluruhan (Total)</span>
                </h2>
            </div>

            @if($overallBudget)
                <div class="bg-white border rounded-3xl p-6 sm:p-8 shadow-xs transition
                    @if($overallBudget->status === 'danger') border-rose-300 ring-2 ring-rose-100
                    @elseif($overallBudget->status === 'warning') border-amber-300 ring-2 ring-amber-100
                    @else border-emerald-200 @endif
                ">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Batas Anggaran Total</span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                                    @if($overallBudget->status === 'danger') bg-rose-100 text-rose-800 border border-rose-300
                                    @elseif($overallBudget->status === 'warning') bg-amber-100 text-amber-800 border border-amber-300
                                    @else bg-emerald-100 text-emerald-800 border border-emerald-300 @endif">
                                    {{ $overallBudget->status_label }}
                                </span>
                            </div>
                            <div class="text-3xl font-black text-slate-900">
                                Rp {{ number_format($overallBudget->limit_amount, 0, ',', '.') }}
                            </div>
                            <p class="text-xs text-slate-500">
                                @if($overallBudget->status === 'danger')
                                    <span class="text-rose-600 font-bold">🚨 Pengeluaran telah melebihi limit sebesar Rp {{ number_format(abs($overallBudget->remaining_amount), 0, ',', '.') }}!</span>
                                @elseif($overallBudget->status === 'warning')
                                    <span class="text-amber-600 font-bold">⚠️ Hati-hati jangan boros! Anggaran tersisa Rp {{ number_format($overallBudget->remaining_amount, 0, ',', '.') }}.</span>
                                @else
                                    <span class="text-emerald-600 font-bold">✓ Sisa anggaran bulan ini: Rp {{ number_format($overallBudget->remaining_amount, 0, ',', '.') }}.</span>
                                @endif
                            </p>
                        </div>

                        <!-- Stats Box -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase">Pengeluaran Riil</span>
                                <div class="text-base font-black text-slate-800 mt-0.5">Rp {{ number_format($overallBudget->spent_amount, 0, ',', '.') }}</div>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase">Sisa Anggaran</span>
                                <div class="text-base font-black {{ $overallBudget->remaining_amount >= 0 ? 'text-teal-600' : 'text-rose-600' }} mt-0.5">
                                    Rp {{ number_format($overallBudget->remaining_amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-span-2 sm:col-span-1 p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase">Persentase</span>
                                <div class="text-base font-black
                                    @if($overallBudget->status === 'danger') text-rose-600
                                    @elseif($overallBudget->status === 'warning') text-amber-600
                                    @else text-emerald-600 @endif mt-0.5">
                                    {{ $overallBudget->percentage }}%
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 self-start lg:self-center">
                            @if($overallBudget->isLockedFor(auth()->user()))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs" title="Data anggaran bawaan akun demo dilindungi dan tidak dapat diedit atau dihapus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>Terkunci (Demo)</span>
                                </span>
                            @else
                                <button 
                                    onclick="openEditBudgetModal({{ $overallBudget->id }}, {{ (int)$overallBudget->limit_amount }}, 'Batas Pengeluaran Total')" 
                                    class="px-3.5 py-2 rounded-xl text-xs font-bold border border-slate-200 text-slate-700 hover:bg-slate-50 transition"
                                >
                                    Ubah Limit
                                </button>
                                <form method="POST" action="{{ route('budgets.destroy', $overallBudget->id) }}" onsubmit="return confirm('Hapus batas anggaran ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Visual Progress Bar -->
                    <div class="mt-6 pt-5 border-t border-slate-100">
                        <div class="flex justify-between text-xs font-semibold mb-2">
                            <span class="text-slate-500">Progress Penggunaan Anggaran</span>
                            <span class="font-bold
                                @if($overallBudget->status === 'danger') text-rose-600
                                @elseif($overallBudget->status === 'warning') text-amber-600
                                @else text-emerald-600 @endif">
                                {{ $overallBudget->percentage }}% dari limit Rp {{ number_format($overallBudget->limit_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="w-full h-3.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500
                                @if($overallBudget->status === 'danger') bg-rose-500
                                @elseif($overallBudget->status === 'warning') bg-amber-500
                                @else bg-emerald-500 @endif"
                                style="width: {{ min($overallBudget->percentage, 100) }}%">
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white border border-slate-200/80 rounded-3xl p-8 text-center shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Batas Pengeluaran Total Bulan Ini</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        Pasang batas maksimal pengeluaran bulanan Anda agar sistem Budgeting-Me dapat memberikan indikator lencana dan peringatan boros secara real-time.
                    </p>
                    <button 
                        onclick="openBudgetModal(null)" 
                        class="mt-4 px-4 py-2 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-500 text-white shadow-xs transition"
                    >
                        + Pasang Limit Total Bulan Ini
                    </button>
                </div>
            @endif
        </div>

        <!-- Section 2: Category Specific Limits -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <span>2. Batas Anggaran Spesifik per Kategori</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                            {{ $categoryBudgets->count() }} Terdaftar
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500">Kendalikan pengeluaran pos-pos spesifik seperti Makanan, Hiburan, dll.</p>
                </div>
                <button 
                    onclick="openBudgetModal('category')" 
                    class="text-xs font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1"
                >
                    + Tambah Limit Kategori
                </button>
            </div>

            @if($categoryBudgets->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($categoryBudgets as $cBudget)
                        <div class="bg-white border rounded-2xl p-5 shadow-xs transition hover:shadow-md
                            @if($cBudget->status === 'danger') border-rose-200
                            @elseif($cBudget->status === 'warning') border-amber-200
                            @else border-slate-200/80 @endif
                        ">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $cBudget->category->color ?? '#0D9488' }}"></span>
                                    <h3 class="font-bold text-slate-800 text-sm">{{ $cBudget->category->name }}</h3>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    @if($cBudget->status === 'danger') bg-rose-100 text-rose-800
                                    @elseif($cBudget->status === 'warning') bg-amber-100 text-amber-800
                                    @else bg-emerald-100 text-emerald-800 @endif">
                                    {{ $cBudget->status_label }}
                                </span>
                            </div>

                            <div class="flex items-baseline justify-between mb-2">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">Terpakai</span>
                                    <div class="font-black text-slate-800 text-sm">
                                        Rp {{ number_format($cBudget->spent_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">Limit</span>
                                    <div class="font-black text-slate-600 text-sm">
                                        Rp {{ number_format($cBudget->limit_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden mb-3">
                                <div class="h-full rounded-full transition-all duration-500
                                    @if($cBudget->status === 'danger') bg-rose-500
                                    @elseif($cBudget->status === 'warning') bg-amber-500
                                    @else bg-emerald-500 @endif"
                                    style="width: {{ min($cBudget->percentage, 100) }}%">
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                                <span class="text-slate-500 text-[11px]">
                                    Sisa: <strong class="{{ $cBudget->remaining_amount >= 0 ? 'text-teal-600' : 'text-rose-600' }}">Rp {{ number_format($cBudget->remaining_amount, 0, ',', '.') }}</strong>
                                </span>
                                @if($cBudget->isLockedFor(auth()->user()))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Data anggaran bawaan akun demo dilindungi dan tidak dapat diedit atau dihapus">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span>Terkunci</span>
                                    </span>
                                @else
                                    <div class="flex items-center gap-1.5">
                                        <button 
                                            onclick="openEditBudgetModal({{ $cBudget->id }}, {{ (int)$cBudget->limit_amount }}, '{{ $cBudget->category->name }}')" 
                                            class="p-1 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                            title="Ubah Limit"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('budgets.destroy', $cBudget->id) }}" onsubmit="return confirm('Hapus batas kategori ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-md text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 text-center shadow-xs">
                    <p class="text-xs text-slate-500">Belum ada limit spesifik per kategori untuk bulan ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal: Pasang / Buat Limit Anggaran Baru -->
    <div id="budgetModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-900">Pasang Limit "Budgetinku"</h3>
                <button onclick="closeBudgetModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('budgets.store') }}" class="mt-6 space-y-4">
                @csrf

                <!-- Category Target -->
                <div>
                    <label for="budget_category_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Target Batas Pengeluaran
                    </label>
                    <select id="budget_category_id" name="category_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                        <option value="">-- Total Seluruh Pengeluaran Bulanan --</option>
                        @foreach($expenseCategories as $cat)
                            <option value="{{ $cat->id }}">Spesifik Kategori: {{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Pilih "Total Seluruh Pengeluaran" untuk limit global bulanan.</p>
                </div>

                <!-- Month & Year -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="budget_month" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Bulan</label>
                        <select id="budget_month" name="month" class="w-full rounded-xl border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="budget_year" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tahun</label>
                        <select id="budget_year" name="year" class="w-full rounded-xl border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                            @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Limit Amount -->
                <div>
                    <label for="limit_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Batas Maksimal (Nominal Limit Rp)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                        <input 
                            type="number" 
                            id="limit_amount" 
                            name="limit_amount" 
                            required 
                            min="1000" 
                            step="1000" 
                            placeholder="Contoh: 3000000" 
                            class="w-full ps-10 rounded-xl border-slate-200 text-sm font-bold focus:border-teal-500 focus:ring-teal-500"
                        >
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeBudgetModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-md shadow-teal-500/20 transition">
                        Simpan Limit Anggaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Nominal Limit -->
    <div id="editBudgetModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-black text-slate-900">Ubah Batas Anggaran</h3>
                <button onclick="closeEditBudgetModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editBudgetForm" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label id="editBudgetName" class="block text-xs font-bold text-slate-600 mb-1.5">Batas Anggaran</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                        <input 
                            type="number" 
                            id="edit_limit_amount" 
                            name="limit_amount" 
                            required 
                            min="1000" 
                            step="1000" 
                            class="w-full ps-10 rounded-xl border-slate-200 text-sm font-bold focus:border-teal-500 focus:ring-teal-500"
                        >
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeEditBudgetModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition">
                        Perbarui Limit
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openBudgetModal(pref) {
            document.getElementById('budgetModal').classList.remove('hidden');
            if (pref === 'category') {
                const select = document.getElementById('budget_category_id');
                if (select.options.length > 1) {
                    select.selectedIndex = 1;
                }
            } else if (pref === null) {
                document.getElementById('budget_category_id').value = '';
            }
        }
        function closeBudgetModal() {
            document.getElementById('budgetModal').classList.add('hidden');
        }

        function openEditBudgetModal(id, currentAmount, name) {
            document.getElementById('editBudgetForm').action = '/budgets/' + id;
            document.getElementById('editBudgetName').textContent = name;
            document.getElementById('edit_limit_amount').value = currentAmount;
            document.getElementById('editBudgetModal').classList.remove('hidden');
        }
        function closeEditBudgetModal() {
            document.getElementById('editBudgetModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
