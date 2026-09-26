<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Riwayat Transaksi</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola, filter, dan cari seluruh data transaksi keuangan Anda.</p>
            </div>
            <div class="flex items-center gap-2.5">
                <!-- Export to PDF Button -->
                <a 
                    href="{{ route('transactions.export.pdf', request()->query()) }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 shadow-xs transition"
                >
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Ekspor PDF
                </a>
                <!-- Add Transaction Button -->
                <button 
                    onclick="openAddModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white shadow-md shadow-emerald-500/20 transition active:scale-95"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Transaksi
                </button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Filter & Search Toolbar -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Catatan / Kategori</label>
                        <div class="relative">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Ketik kata kunci..." 
                                class="w-full text-xs rounded-xl border-slate-200 ps-9 focus:border-emerald-500 focus:ring-emerald-500"
                            >
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Jenis</label>
                        <select name="type" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Semua Jenis</option>
                            <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kategori</label>
                        <select name="category_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->type === 'income' ? '[+]' : '[-]' }} {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method Filter -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Sumber Dana</label>
                        <select name="payment_method" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Semua Sumber</option>
                            @foreach($paymentMethods as $key => $method)
                                <option value="{{ $key }}" {{ request('payment_method') === $key ? 'selected' : '' }}>
                                    {{ $method['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month Filter -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bulan</label>
                        <select name="month" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="all">Semua Bulan</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month', 'all') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-slate-500">Urutkan:</span>
                            <select name="sort_by" class="text-xs rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 py-1 px-2.5">
                                <option value="date" {{ request('sort_by') === 'date' ? 'selected' : '' }}>Tanggal</option>
                                <option value="amount" {{ request('sort_by') === 'amount' ? 'selected' : '' }}>Nominal</option>
                            </select>
                        </div>
                        <label class="text-xs text-slate-500 flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="order" value="desc" {{ request('order', 'desc') === 'desc' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                            <span>Terbaru / Tertinggi</span>
                        </label>
                        <label class="text-xs text-slate-500 flex items-center gap-1.5 cursor-pointer ms-2">
                            <input type="radio" name="order" value="asc" {{ request('order') === 'asc' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                            <span>Terlama / Terendah</span>
                        </label>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('transactions.index') }}" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Reset Filter
                        </a>
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Filtered Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Total Pemasukan (Filter)</span>
                    <div class="text-xl font-black text-emerald-700 mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-rose-50/70 border border-rose-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Total Pengeluaran (Filter)</span>
                    <div class="text-xl font-black text-rose-700 mt-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-teal-50/70 border border-teal-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Selisih Bersih (Filter)</span>
                    <div class="text-xl font-black {{ $netBalance >= 0 ? 'text-teal-700' : 'text-rose-700' }} mt-1">
                        Rp {{ number_format($netBalance, 0, ',', '.') }}
                    </div>
                </div>
                <div class="w-8 h-8 rounded-lg bg-teal-500 text-white flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
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
                        @forelse($transactions as $item)
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item->payment_method_badge_class }}">
                                        {{ $item->payment_method_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 font-black whitespace-nowrap {{ $item->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $item->type === 'income' ? '+' : '-' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-slate-600 text-xs max-w-sm truncate">
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
                                            <a href="{{ route('transactions.edit', $item->id) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Edit Transaksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form method="POST" action="{{ route('transactions.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Data yang terhapus tidak dapat dikembalikan.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Transaksi">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="font-bold text-slate-600">Tidak ada transaksi ditemukan</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian atau tambahkan transaksi baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @endif
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
                            <input type="radio" name="type" value="income" class="peer sr-only" onchange="filterModalCategories('income')">
                            <div class="py-2.5 px-4 rounded-xl border border-slate-200 bg-slate-50 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 text-slate-600 text-center text-xs font-bold transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                Pemasukan
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="expense" checked class="peer sr-only" onchange="filterModalCategories('expense')">
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
                                <option value="{{ $key }}" {{ old('payment_method', 'Cash') === $key ? 'selected' : '' }}>
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

    @push('scripts')
    <script>
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
    </script>
    @endpush
</x-app-layout>
