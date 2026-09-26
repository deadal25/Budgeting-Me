<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Catatan Utang & Pinjaman</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">Kewajiban</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Catat dan pantau total utang Anda. Jangan lupa bayar tepat waktu saat gajian!
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a 
                    href="{{ route('transactions.export.pdf') }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-900/40 shadow-xs transition"
                    title="Unduh Laporan Keuangan & Catatan Utang PDF"
                >
                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Ekspor PDF
                </a>
                <button 
                    onclick="openAddDebtModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 text-white shadow-md shadow-rose-500/20 transition active:scale-95"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Catat Utang Baru
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Success Alert Notification -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-2.5 text-xs sm:text-sm font-semibold">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Special Gajian Reminder Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-500 via-rose-500 to-purple-600 p-6 sm:p-7 text-white shadow-lg shadow-rose-500/10">
            <div class="absolute -right-8 -bottom-8 w-44 h-44 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="space-y-1.5 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-black tracking-wide uppercase">
                        <span>🔔 Pengingat Gajian</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black tracking-tight">Bayar Saat Gajian! Jangan Lupa Bayar Utang</h2>
                    <p class="text-xs sm:text-sm text-white/90 leading-relaxed">
                        Saat menerima gaji bulanan atau pemasukan baru, prioritaskan langsung alokasi pelunasan utang agar tanggungan berkurang dan kondisi finansial Anda semakin sehat dan tenang.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <button 
                        onclick="openAddDebtModal()" 
                        class="px-4 py-2.5 rounded-xl text-xs font-bold bg-white text-slate-900 hover:bg-slate-50 shadow-md transition"
                    >
                        + Catat Utang
                    </button>
                    @if($totalUnpaidDebt > 0)
                        <a 
                            href="#tabel-utang" 
                            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-white/20 hover:bg-white/30 text-white backdrop-blur-md transition"
                        >
                            Lihat Utang Aktif ({{ $unpaidCount }})
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary Cards Grid (Kotak Ringkasan Utang) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Card 1: Total Utang Belum Lunas -->
            <div class="rounded-2xl bg-white border-2 border-rose-200/80 p-5 shadow-xs hover:shadow-md transition relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Sisa Utang Belum Lunas</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-rose-600">Rp {{ number_format($totalUnpaidDebt, 0, ',', '.') }}</div>
                    <div class="flex items-center gap-1.5 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-100 text-rose-800">
                            {{ $unpaidCount }} Utang Perlu Dilunasi
                        </span>
                        <span class="text-[11px] font-semibold text-amber-600">Bayar saat gajian!</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Sudah Dibayar -->
            <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Sudah Dibayar</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalPaidDebt, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-slate-400 mt-2">
                        {{ $paidCount }} utang telah lunas seluruhnya
                    </div>
                </div>
            </div>

            <!-- Card 3: Total Pinjaman -->
            <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Akumulasi Pinjaman</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-slate-800">Rp {{ number_format($totalAllDebt, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-slate-400 mt-2">
                        Total pokok dari seluruh catatan utang
                    </div>
                </div>
            </div>

            <!-- Card 4: Persentase Pelunasan -->
            @php
                $globalPercent = $totalAllDebt > 0 ? min(100, round(($totalPaidDebt / $totalAllDebt) * 100)) : 100;
            @endphp
            <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Progres Pelunasan</span>
                    <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-xs">
                        {{ $globalPercent }}%
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-black text-teal-600">{{ $globalPercent }}% Lunas</div>
                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden mt-3">
                        <div class="h-full rounded-full bg-teal-500 transition-all duration-500" style="width: {{ $globalPercent }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Table Section -->
        <div id="tabel-utang" class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-base sm:text-lg">Daftar Utang & Tanggungan</h2>
                    <p class="text-xs text-slate-500">Kelola pinjaman, cicilan, dan pencatatan pembayaran.</p>
                </div>

                <!-- Status Filter Tabs -->
                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                    <a href="{{ route('debts.index', ['status' => 'all']) }}" class="px-3 py-1.5 rounded-lg transition {{ $statusFilter === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                        Semua
                    </a>
                    <a href="{{ route('debts.index', ['status' => 'active']) }}" class="px-3 py-1.5 rounded-lg transition {{ $statusFilter === 'active' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                        Aktif / Belum Lunas
                    </a>
                    <a href="{{ route('debts.index', ['status' => 'partial']) }}" class="px-3 py-1.5 rounded-lg transition {{ $statusFilter === 'partial' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                        Dicicil
                    </a>
                    <a href="{{ route('debts.index', ['status' => 'paid']) }}" class="px-3 py-1.5 rounded-lg transition {{ $statusFilter === 'paid' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                        Lunas
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-5">Nama Utang & Pemberi Pinjaman</th>
                            <th class="py-3.5 px-5">Total Utang</th>
                            <th class="py-3.5 px-5">Sudah Dibayar</th>
                            <th class="py-3.5 px-5">Sisa Tagihan</th>
                            <th class="py-3.5 px-5">Jatuh Tempo & Tag</th>
                            <th class="py-3.5 px-5">Status</th>
                            <th class="py-3.5 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($debts as $debt)
                            <tr class="hover:bg-slate-50/60 transition {{ $debt->is_paid ? 'opacity-70 bg-slate-50/30' : '' }}">
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900">{{ $debt->title }}</div>
                                    <div class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                                        <span>Kepada: <strong>{{ $debt->creditor ?: '-' }}</strong></span>
                                        @if($debt->notes)
                                            <span>&bull;</span>
                                            <span class="truncate max-w-xs text-slate-400">{{ $debt->notes }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-5 font-bold text-slate-800 whitespace-nowrap">
                                    {{ $debt->formatted_amount }}
                                </td>
                                <td class="py-4 px-5 text-emerald-600 font-semibold whitespace-nowrap">
                                    {{ $debt->formatted_paid_amount }}
                                    <div class="w-24 h-1.5 rounded-full bg-slate-100 overflow-hidden mt-1.5">
                                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ $debt->percentage_paid }}%"></div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="font-black text-base {{ $debt->remaining_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ $debt->formatted_remaining_amount }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-xs">
                                    <div class="text-slate-600 font-medium">
                                        {{ $debt->formatted_due_date ?: 'Tidak ditentukan' }}
                                    </div>
                                    @if($debt->pay_on_salary)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 mt-1">
                                            💰 Bayar Saat Gajian
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $debt->status_badge }}">
                                        {{ $debt->status_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        @if(!$debt->is_paid)
                                            <button 
                                                type="button" 
                                                onclick='openPayDebtModal(@json($debt))'
                                                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-xs transition flex items-center gap-1"
                                                title="Bayar / Cicil"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                Bayar
                                            </button>
                                        @endif

                                        <button 
                                            type="button"
                                            onclick='openEditDebtModal(@json($debt))'
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                            title="Edit"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        <form method="POST" action="{{ route('debts.destroy', $debt->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan utang ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">Tidak ada catatan utang</p>
                                    <p class="text-xs text-slate-400 mt-1">Anda tidak memiliki tanggungan utang pada kategori ini.</p>
                                    <button onclick="openAddDebtModal()" class="mt-4 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                                        + Catat Utang Baru
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($debts->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $debts->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Utang -->
    <div id="addDebtModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Catat Utang Baru</h3>
                    <p class="text-xs text-slate-500">Masukkan detail utang atau pinjaman yang perlu dilunasi.</p>
                </div>
                <button onclick="closeAddDebtModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('debts.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="add_title" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama / Keperluan Utang <span class="text-rose-500">*</span></label>
                    <input type="text" id="add_title" name="title" required placeholder="Contoh: Utang Teman, Cicilan Laptop, Pinjaman Bank" class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="add_creditor" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pemberi Pinjaman</label>
                        <input type="text" id="add_creditor" name="creditor" placeholder="Contoh: Budi, Bank BRI, dll." class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                    <div>
                        <label for="add_due_date" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Jatuh Tempo (Opsional)</label>
                        <input type="date" id="add_due_date" name="due_date" class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="add_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Total Nominal Utang (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                            <input type="number" id="add_amount" name="amount" required min="1" step="1" placeholder="500.000" class="w-full ps-10 rounded-xl border-slate-200 text-sm font-bold focus:border-rose-500 focus:ring-rose-500">
                        </div>
                    </div>
                    <div>
                        <label for="add_paid_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Sudah Dicicil (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                            <input type="number" id="add_paid_amount" name="paid_amount" value="0" min="0" step="1" class="w-full ps-10 rounded-xl border-slate-200 text-sm font-medium focus:border-rose-500 focus:ring-rose-500">
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
                    <label for="add_notes" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                    <textarea id="add_notes" name="notes" rows="2" placeholder="Catatan nomor rekening atau perjanjian..." class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeAddDebtModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 text-white text-xs font-bold shadow-md shadow-rose-500/20 transition">
                        Simpan Catatan Utang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Bayar / Cicil Utang -->
    <div id="payDebtModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Bayar / Cicil Utang</h3>
                    <p id="payModalSubtitle" class="text-xs text-slate-500 mt-0.5">Catat pembayaran angsuran utang Anda.</p>
                </div>
                <button onclick="closePayDebtModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="payDebtForm" method="POST" action="" class="mt-6 space-y-4">
                @csrf

                <!-- Quick Info Box -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Sisa Tagihan Saat Ini:</div>
                        <div id="payModalRemainingText" class="text-xl font-black text-rose-600">Rp 0</div>
                    </div>
                    <button 
                        type="button" 
                        onclick="setFullPayAmount()" 
                        class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold transition"
                    >
                        Lunasi Penuh
                    </button>
                </div>

                <!-- Payment Amount -->
                <div>
                    <label for="pay_payment_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Jumlah Pembayaran (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                        <input type="number" id="pay_payment_amount" name="payment_amount" required min="1" step="1" placeholder="100.000" class="w-full ps-10 rounded-xl border-slate-200 text-sm font-bold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Payment Method / Bank / E-Wallet -->
                <div>
                    <label for="pay_payment_method" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Dibayar Dari Rekening / Sumber Dana <span class="text-rose-500">*</span></label>
                    <select id="pay_payment_method" name="payment_method" required class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach($paymentMethods as $key => $method)
                            <option value="{{ $key }}">
                                {{ $method['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Saldo rekening terkait akan otomatis berkurang jika Anda mencatat transaksi pengeluaran.</p>
                </div>

                <!-- Date -->
                <div>
                    <label for="pay_payment_date" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal Pembayaran</label>
                    <input type="date" id="pay_payment_date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
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

                <!-- Notes -->
                <div>
                    <label for="pay_notes" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Catatan Pembayaran (Opsional)</label>
                    <input type="text" id="pay_notes" name="notes" placeholder="Contoh: Cicilan ke-1, transfer lewat SeaBank, dll." class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closePayDebtModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition">
                        Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Utang -->
    <div id="editDebtModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-black text-slate-900">Edit Catatan Utang</h3>
                    <p class="text-xs text-slate-500">Perbarui rincian utang atau pinjaman.</p>
                </div>
                <button onclick="closeEditDebtModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editDebtForm" method="POST" action="" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="edit_title" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Utang <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_title" name="title" required class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit_creditor" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pemberi Pinjaman</label>
                        <input type="text" id="edit_creditor" name="creditor" class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                    <div>
                        <label for="edit_due_date" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Jatuh Tempo</label>
                        <input type="date" id="edit_due_date" name="due_date" class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Total Nominal Utang (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                            <input type="number" id="edit_amount" name="amount" required min="1" step="1" class="w-full ps-10 rounded-xl border-slate-200 text-sm font-bold focus:border-rose-500 focus:ring-rose-500">
                        </div>
                    </div>
                    <div>
                        <label for="edit_paid_amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Sudah Dicicil (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                            <input type="number" id="edit_paid_amount" name="paid_amount" min="0" step="1" class="w-full ps-10 rounded-xl border-slate-200 text-sm font-medium focus:border-rose-500 focus:ring-rose-500">
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" id="edit_pay_on_salary" name="pay_on_salary" value="1" class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="text-xs font-bold text-amber-950">Bayar saat gajian!</span>
                            <p class="text-[11px] text-amber-800">Tandai utang ini agar otomatis diprioritaskan saat menerima gajian.</p>
                        </div>
                    </label>
                </div>

                <div>
                    <label for="edit_notes" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Catatan Tambahan</label>
                    <textarea id="edit_notes" name="notes" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditDebtModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentPayingDebt = null;

        function openAddDebtModal() {
            document.getElementById('addDebtModal').classList.remove('hidden');
        }
        function closeAddDebtModal() {
            document.getElementById('addDebtModal').classList.add('hidden');
        }

        function openPayDebtModal(debt) {
            currentPayingDebt = debt;
            const form = document.getElementById('payDebtForm');
            form.action = `/debts/${debt.id}/pay`;
            
            document.getElementById('payModalSubtitle').textContent = `Membayar: ${debt.title} (Kepada: ${debt.creditor || '-'})`;
            
            const remaining = Math.max(0, debt.amount - debt.paid_amount);
            document.getElementById('payModalRemainingText').textContent = 'Rp ' + remaining.toLocaleString('id-ID');
            document.getElementById('pay_payment_amount').value = remaining > 0 ? remaining : '';
            document.getElementById('pay_payment_amount').max = remaining;

            document.getElementById('payDebtModal').classList.remove('hidden');
        }
        function closePayDebtModal() {
            document.getElementById('payDebtModal').classList.add('hidden');
            currentPayingDebt = null;
        }

        function setFullPayAmount() {
            if (currentPayingDebt) {
                const remaining = Math.max(0, currentPayingDebt.amount - currentPayingDebt.paid_amount);
                document.getElementById('pay_payment_amount').value = remaining;
            }
        }

        function openEditDebtModal(debt) {
            const form = document.getElementById('editDebtForm');
            form.action = `/debts/${debt.id}`;

            document.getElementById('edit_title').value = debt.title || '';
            document.getElementById('edit_creditor').value = debt.creditor || '';
            document.getElementById('edit_amount').value = debt.amount || 0;
            document.getElementById('edit_paid_amount').value = debt.paid_amount || 0;
            document.getElementById('edit_due_date').value = debt.due_date ? debt.due_date.substring(0, 10) : '';
            document.getElementById('edit_pay_on_salary').checked = Boolean(debt.pay_on_salary);
            document.getElementById('edit_notes').value = debt.notes || '';

            document.getElementById('editDebtModal').classList.remove('hidden');
        }
        function closeEditDebtModal() {
            document.getElementById('editDebtModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
