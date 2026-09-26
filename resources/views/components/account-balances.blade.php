@props([
    'accounts' => [],
    'title' => 'Saldo per Rekening Bank & Dompet Digital',
    'subtitle' => 'Pantau ketersediaan saldo, total uang masuk, dan pengeluaran pada masing-masing rekening dan dompet digital.',
    'showFilterLink' => true,
    'context' => 'user', // 'user' or 'admin'
])

@php
    $totalBalance = array_sum(array_column($accounts, 'balance'));
    $totalIncome = array_sum(array_column($accounts, 'income'));
    $totalExpense = array_sum(array_column($accounts, 'expense'));
    $activeCount = count(array_filter($accounts, fn($a) => $a['has_activity'] || $a['balance'] != 0));
    $totalAccounts = count($accounts);
@endphp

<div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6" id="account-balances-section">
    <!-- Header with Aggregates & Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-slate-100">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">{{ $title }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Filter Tabs (Vanilla JS) -->
            <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-bold text-slate-600">
                <button 
                    type="button" 
                    onclick="filterAccountCards('all', this)"
                    class="account-tab-btn px-3.5 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition"
                >
                    Semua ({{ $totalAccounts }})
                </button>
                <button 
                    type="button" 
                    onclick="filterAccountCards('active', this)"
                    class="account-tab-btn px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition"
                >
                    Ada Saldo / Mutasi ({{ $activeCount }})
                </button>
            </div>

            <!-- Total Aggregated Pill -->
            <div class="px-4 py-2 rounded-xl bg-slate-50 border border-slate-200/80 text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Seluruh Rekening</span>
                <span class="text-sm font-black {{ $totalBalance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    Rp {{ number_format($totalBalance, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="accountsGrid">
        @forelse($accounts as $key => $acc)
            <div 
                class="account-card group relative p-5 rounded-2xl border transition duration-200 flex flex-col justify-between
                    {{ $acc['has_activity'] ? 'bg-white border-slate-200/90 hover:border-emerald-500/50 hover:shadow-md' : 'bg-slate-50/60 border-dashed border-slate-200 opacity-75 hover:opacity-100' }}"
                data-has-activity="{{ $acc['has_activity'] || $acc['balance'] != 0 ? '1' : '0' }}"
            >
                <!-- Top Brand & Type Bar -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-xs" style="background-color: {{ $acc['color'] }};"></span>
                            <span class="font-black text-sm text-slate-900 group-hover:text-emerald-700 transition">
                                {{ $acc['label'] }}
                            </span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $acc['type'] }}
                        </span>
                    </div>

                    @if(!empty($acc['desc']))
                        <p class="text-[11px] text-slate-500 font-medium mb-2">{{ $acc['desc'] }}</p>
                    @endif

                    <!-- Saldo Saat Ini -->
                    <div class="mt-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Saldo Tersedia</span>
                        <div class="text-xl font-black tracking-tight {{ $acc['balance'] > 0 ? 'text-emerald-600' : ($acc['balance'] < 0 ? 'text-rose-600' : 'text-slate-800') }}">
                            Rp {{ number_format($acc['balance'], 0, ',', '.') }}
                        </div>
                    </div>

                    <!-- Masuk & Keluar Breakdown -->
                    <div class="mt-3 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-emerald-50/90 border border-emerald-200 rounded-xl p-2">
                            <span class="text-[10px] font-bold text-emerald-800 block uppercase tracking-wider">Masuk</span>
                            <span class="font-bold text-emerald-900 text-[11px] truncate block" title="Rp {{ number_format($acc['income'], 0, ',', '.') }}">
                                +{{ number_format($acc['income'], 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="bg-rose-50/90 border border-rose-200 rounded-xl p-2">
                            <span class="text-[10px] font-bold text-rose-800 block uppercase tracking-wider">Keluar</span>
                            <span class="font-bold text-rose-900 text-[11px] truncate block" title="Rp {{ number_format($acc['expense'], 0, ',', '.') }}">
                                -{{ number_format($acc['expense'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Action & Count -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-600 font-bold">
                        {{ $acc['count'] }} Transaksi
                    </span>
                    @if($showFilterLink && $context === 'user')
                        <a 
                            href="{{ route('transactions.index', ['payment_method' => $acc['key']]) }}" 
                            class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1 transition shadow-2xs"
                            title="Filter riwayat transaksi {{ $acc['label'] }}"
                        >
                            <span>Lihat Mutasi</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $acc['has_activity'] ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                            {{ $acc['has_activity'] ? 'Aktif' : 'Nihil' }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-8 text-center text-slate-400 text-sm">
                Belum ada data rekening atau dompet digital.
            </div>
        @endforelse
    </div>
</div>

<script>
    function filterAccountCards(mode, button) {
        const buttons = document.querySelectorAll('.account-tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
            btn.classList.add('text-slate-500');
        });
        button.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
        button.classList.remove('text-slate-500');

        const cards = document.querySelectorAll('.account-card');
        cards.forEach(card => {
            const hasActivity = card.getAttribute('data-has-activity') === '1';
            if (mode === 'all') {
                card.style.display = '';
            } else if (mode === 'active') {
                card.style.display = hasActivity ? '' : 'none';
            }
        });
    }
</script>
