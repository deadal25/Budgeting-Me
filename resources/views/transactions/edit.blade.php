<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Transaksi</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui rincian transaksi keuangan Anda.</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                &larr; Kembali ke Riwayat
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('transactions.update', $transaction->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Type Selector -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Transaksi</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="income" class="peer sr-only" {{ old('type', $transaction->type) === 'income' ? 'checked' : '' }} onchange="filterCategories('income')">
                            <div class="py-3 px-4 rounded-xl border border-slate-200 bg-slate-50 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 text-slate-600 text-center text-sm font-bold transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                Pemasukan
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="expense" class="peer sr-only" {{ old('type', $transaction->type) === 'expense' ? 'checked' : '' }} onchange="filterCategories('expense')">
                            <div class="py-3 px-4 rounded-xl border border-slate-200 bg-slate-50 peer-checked:bg-rose-50 peer-checked:border-rose-500 peer-checked:text-rose-700 text-slate-600 text-center text-sm font-bold transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                Pengeluaran
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Category & Payment Method 2 Cols -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Kategori</label>
                        <select id="category_id" name="category_id" required class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach($categories as $category)
                                <option 
                                    value="{{ $category->id }}" 
                                    data-type="{{ $category->type }}"
                                    {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method / Sumber Dana -->
                    <div>
                        <label for="payment_method" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Sumber / Asal Dana</label>
                        <select id="payment_method" name="payment_method" required class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach($paymentMethods as $key => $method)
                                <option 
                                    value="{{ $key }}" 
                                    {{ old('payment_method', $transaction->payment_method ?? 'Cash') === $key ? 'selected' : '' }}
                                >
                                    {{ $method['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Jumlah (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-slate-400 text-sm font-bold">Rp</span>
                        <input 
                            type="number" 
                            id="amount" 
                            name="amount" 
                            required 
                            min="1" 
                            step="1" 
                            value="{{ old('amount', (int)$transaction->amount) }}" 
                            class="w-full ps-10 rounded-xl border-slate-200 text-sm font-bold focus:border-emerald-500 focus:ring-emerald-500"
                        >
                    </div>
                </div>

                <!-- Date -->
                <div>
                    <label for="date" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input 
                        type="date" 
                        id="date" 
                        name="date" 
                        required 
                        value="{{ old('date', $transaction->date->format('Y-m-d')) }}" 
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Catatan (Opsional)</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="3" 
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >{{ old('notes', $transaction->notes) }}</textarea>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function filterCategories(type) {
            const select = document.getElementById('category_id');
            const options = select.querySelectorAll('option');
            let hasSelected = false;

            options.forEach(opt => {
                if (opt.getAttribute('data-type') === type) {
                    opt.style.display = '';
                    if (opt.selected) hasSelected = true;
                } else {
                    opt.style.display = 'none';
                    if (opt.selected) opt.selected = false;
                }
            });

            if (!hasSelected) {
                const first = Array.from(options).find(o => o.getAttribute('data-type') === type);
                if (first) first.selected = true;
            }
        }

        // Initialize based on current type
        const currentType = document.querySelector('input[name="type"]:checked')?.value || 'expense';
        filterCategories(currentType);
    </script>
    @endpush
</x-app-layout>
