<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.index') }}" class="text-xs font-bold text-purple-600 hover:text-purple-800">
                        &larr; Kembali ke Panel Admin
                    </a>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1 flex items-center gap-2">
                    <span>Detail Pengguna: {{ $user->name }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">User</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Email: {{ $user->email }} &bull; Bergabung sejak {{ $user->created_at->translatedFormat('d F Y') }}</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Ganti Sandi Button -->
                <button 
                    type="button" 
                    @click="openPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-xs transition flex items-center gap-1.5"
                >
                    <span>🔑 Ganti Sandi</span>
                </button>

                <!-- Kirim Link Reset Email Form -->
                <form 
                    method="POST" 
                    action="{{ route('admin.users.send-reset-link', $user->id) }}" 
                    class="inline"
                    onsubmit="return confirm('Kirim email tautan reset kata sandi ke {{ $user->email }}?')"
                >
                    @csrf
                    <button 
                        type="submit" 
                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-teal-600 hover:bg-teal-700 text-white shadow-xs transition flex items-center gap-1.5"
                    >
                        <span>✉️ Kirim Link Reset Email</span>
                    </button>
                </form>

                <!-- Hapus Akun Form -->
                @if($user->id !== auth()->id() && !$user->isAdmin() && !in_array($user->email, ['alqad.ri2505@gmail.com', 'alqadri2505@gmail.com']))
                    <form 
                        method="POST" 
                        action="{{ route('admin.users.destroy', $user->id) }}" 
                        class="inline"
                        onsubmit="return confirm('⚠️ PERINGATAN HAPUS AKUN:\nApakah Anda yakin ingin menghapus akun pengguna {{ addslashes($user->name) }} ({{ $user->email }})?\n\nSeluruh data transaksi, kategori, anggaran, dan catatan utang pengguna ini akan DIHAPUS PERMANEN dari sistem!')"
                    >
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition flex items-center gap-1.5"
                        >
                            <span>🗑️ Hapus Akun</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div x-data="adminUserManagement()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Summary Cards for this User -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Pemasukan -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pemasukan</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-600 mt-3">
                    Rp {{ number_format($userTotalIncome, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Seluruh akumulasi uang masuk</p>
            </div>

            <!-- Pengeluaran -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengeluaran</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-black text-rose-600 mt-3">
                    Rp {{ number_format($userTotalExpense, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Seluruh akumulasi uang keluar</p>
            </div>

            <!-- Saldo -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Saldo Bersih</span>
                    <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-black {{ $userBalance >= 0 ? 'text-teal-600' : 'text-rose-600' }} mt-3">
                    Rp {{ number_format($userBalance, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Pemasukan dikurangi pengeluaran</p>
            </div>

            <!-- Utang User -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggungan Utang</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="text-2xl font-black {{ $userTotalUnpaidDebt > 0 ? 'text-rose-600' : 'text-slate-800' }} mt-3">
                    Rp {{ number_format($userTotalUnpaidDebt, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-400 mt-1">
                    {{ $userDebts->where('status', '!=', 'paid')->count() }} utang belum lunas
                </p>
            </div>
        </div>

        <!-- Section: Rincian Saldo Rekening & Dompet User -->
        <x-account-balances 
            :accounts="$userAccountBalances" 
            title="Saldo Rekening Bank & Dompet Digital Pengguna"
            subtitle="Rincian saldo riil, akumulasi uang masuk, dan pengeluaran pada akun milik {{ $user->name }}."
            :showFilterLink="false"
            context="admin"
        />

        <!-- Section: Charts & Budgetinku of this User -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Chart -->
            <div class="lg:col-span-6 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-bold text-slate-800 text-base">Grafik Pengeluaran per Kategori</h2>
                        <p class="text-xs text-slate-400">Komposisi pos pengeluaran akun ini</p>
                    </div>
                </div>

                @if(count($chartLabels) > 0)
                    <div class="relative flex-1 flex items-center justify-center min-h-[260px] py-4">
                        <canvas id="userExpenseChart" class="max-h-[260px]"></canvas>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center p-8 text-slate-400 min-h-[220px]">
                        <p class="text-sm font-semibold text-slate-600">User ini belum memiliki riwayat pengeluaran.</p>
                    </div>
                @endif
            </div>

            <!-- Right: Budgetinku Status of this User -->
            <div class="lg:col-span-6 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-xs flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-bold text-slate-800 text-base">Status "Budgetinku" User</h2>
                        <p class="text-xs text-slate-400">Limit anggaran bulan berjalan yang dipasang oleh user</p>
                    </div>
                </div>

                @if($budgets->count() > 0)
                    <div class="space-y-3.5 flex-1">
                        @foreach($budgets as $b)
                            <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/70">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-xs text-slate-800">
                                        {{ $b->category ? $b->category->name : 'Total Pengeluaran Bulanan' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                        @if($b->status === 'danger') bg-rose-100 text-rose-800
                                        @elseif($b->status === 'warning') bg-amber-100 text-amber-800
                                        @else bg-emerald-100 text-emerald-800 @endif">
                                        {{ $b->status_label }}
                                    </span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden mb-1.5">
                                    <div class="h-full rounded-full
                                        @if($b->status === 'danger') bg-rose-500
                                        @elseif($b->status === 'warning') bg-amber-500
                                        @else bg-emerald-500 @endif"
                                        style="width: {{ min($b->percentage, 100) }}%">
                                    </div>
                                </div>
                                <div class="flex justify-between text-[11px] text-slate-500">
                                    <span>Terpakai: Rp {{ number_format($b->spent_amount, 0, ',', '.') }}</span>
                                    <span>Limit: Rp {{ number_format($b->limit_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center p-8 text-slate-400 min-h-[220px]">
                        <p class="text-sm font-semibold text-slate-600">User belum mengatur limit Budgetinku untuk bulan ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section: Full Itemized Transaction History (Read-Only) -->
        <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="font-bold text-slate-900 text-base">Riwayat Transaksi Pengguna (Semua)</h2>
                <p class="text-xs text-slate-500">Menampilkan seluruh mutasi pencatatan milik user (termasuk input dari form publik)</p>
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
                            <th class="py-3 px-5 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-5 font-medium text-slate-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($trx->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    @if($trx->type === 'income')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            Pemasukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            Pengeluaran
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap font-medium text-slate-800">
                                    {{ $trx->category->name }}
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $trx->payment_method_badge_class }}">
                                        {{ $trx->payment_method_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 font-black whitespace-nowrap {{ $trx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $trx->type === 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-slate-600 text-xs">
                                    {{ $trx->notes ?: '-' }}
                                </td>
                                <td class="py-3.5 px-5 text-right text-xs text-slate-400 whitespace-nowrap">
                                    <span class="text-emerald-600 font-semibold">Tercatat</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">
                                    Belum ada transaksi yang tercatat untuk user ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Ganti Kata Sandi Pengguna -->
        <div 
            x-show="passwordModal" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.away="passwordModal = false"
                class="bg-white dark:bg-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 relative text-left"
            >
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 flex items-center justify-center text-lg">
                            🔑
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-slate-900 dark:text-white">Atur / Ganti Kata Sandi</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui kata sandi akun pengguna</p>
                        </div>
                    </div>
                    <button @click="passwordModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg">&times;</button>
                </div>

                <div class="my-4 p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200/80 dark:border-slate-700/80">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Akun Pengguna:</div>
                    <div class="font-bold text-slate-900 dark:text-white text-sm mt-0.5" x-text="selectedUser.name"></div>
                    <div class="text-xs text-slate-500 font-mono" x-text="selectedUser.email"></div>
                </div>

                <form :action="'{{ url('admin/users') }}/' + selectedUser.id + '/password'" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Kata Sandi Baru
                            </label>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="newPassword = generateRandomPassword()" 
                                    class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 inline-flex items-center gap-1"
                                >
                                    <span>🎲 Acak Sandi</span>
                                </button>
                                <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                                <button 
                                    type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:text-purple-500 inline-flex items-center gap-1"
                                    x-text="showPassword ? '🙈 Sembunyikan' : '👁️ Intip / Lihat'"
                                >
                                </button>
                            </div>
                        </div>

                        <div class="relative">
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                x-model="newPassword" 
                                required 
                                minlength="6"
                                placeholder="Masukkan kata sandi baru"
                                class="w-full px-4 py-3 pr-24 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white font-mono text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 transition"
                            >
                            <button 
                                type="button" 
                                @click="copyPassword()" 
                                class="absolute right-2 top-2 px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 text-xs font-bold transition flex items-center gap-1"
                            >
                                <span x-text="copied ? '✅ Disalin' : '📋 Salin'"></span>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                            💡 Anda dapat mengintip dan menyalin kata sandi ini sekarang untuk langsung diberikan kepada pengguna.
                        </p>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-700">
                        <button 
                            type="button" 
                            @click="passwordModal = false" 
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-600/20 transition flex items-center gap-1.5"
                        >
                            <span>💾 Simpan Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function adminUserManagement() {
            return {
                passwordModal: false,
                selectedUser: { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}', email: '{{ addslashes($user->email) }}' },
                newPassword: '',
                showPassword: true,
                copied: false,
                openPasswordModal(id, name, email) {
                    this.selectedUser = { id, name, email };
                    this.newPassword = this.generateRandomPassword();
                    this.showPassword = true;
                    this.copied = false;
                    this.passwordModal = true;
                },
                generateRandomPassword() {
                    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
                    let pwd = '';
                    for (let i = 0; i < 10; i++) {
                        pwd += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    return pwd;
                },
                copyPassword() {
                    if (!this.newPassword) return;
                    navigator.clipboard.writeText(this.newPassword);
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2500);
                }
            };
        }

        @if(count($chartLabels) > 0)
        const ctx = document.getElementById('userExpenseChart').getContext('2d');
        const isDarkInitial = document.documentElement.classList.contains('dark');
        const userChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: {!! json_encode($chartColors) !!},
                    borderWidth: 2,
                    borderColor: isDarkInitial ? '#1e293b' : '#ffffff',
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
                            padding: 12,
                            font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" }
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

        window.addEventListener('theme-changed', function(e) {
            const isDark = e.detail && e.detail.theme === 'dark';
            if (userChart) {
                userChart.data.datasets[0].borderColor = isDark ? '#1e293b' : '#ffffff';
                if (userChart.options.plugins && userChart.options.plugins.legend) {
                    userChart.options.plugins.legend.labels.color = isDark ? '#cbd5e1' : '#475569';
                }
                userChart.update();
            }
        });
        @endif
    </script>
    @endpush
</x-app-layout>
