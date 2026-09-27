<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Panel Administrator</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                        Manajemen Pengguna & Monitoring
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola akun pengguna, ganti kata sandi, kirim reset email, dan pantau ringkasan keuangan.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium">Administrator Utama: <strong class="text-purple-700">{{ auth()->user()->email }}</strong></span>
            </div>
        </div>
    </x-slot>

    <div x-data="adminUserManagement()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Admin Personal Finances Quick Access Banner -->
        <div class="rounded-3xl p-5 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 text-white shadow-lg border border-emerald-500/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/25 border border-emerald-400/40 text-emerald-200 flex items-center justify-center text-2xl shrink-0 shadow-inner">
                    💼
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-black text-base text-white">Kelola Keuangan & Budget Pribadi Anda</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-white shadow-xs">Akun Admin</span>
                    </div>
                    <p class="text-xs text-emerald-200/90 mt-1 leading-relaxed">
                        Perlu mencatat pengeluaran, pemasukan, utang, atau budget pribadi Anda sendiri? Anda dapat beralih ke dashboard keuangan pribadi kapan saja.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl text-xs font-black bg-white text-emerald-950 hover:bg-emerald-50 shadow-md transition flex items-center gap-2">
                    <span>📊 Buka Dashboard Keuangan Pribadi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Notice Banner -->
        <div class="rounded-2xl p-4 bg-purple-50 border border-purple-200 text-purple-900 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <span class="font-bold text-xs sm:text-sm">Fitur Manajemen Pengguna Aktif</span>
                    <p class="text-[11px] text-purple-700">Administrator memiliki hak akses untuk mengubah kata sandi, melihat kata sandi baru yang ditetapkan, mengirimkan link reset ke email pengguna, serta menghapus akun yang tidak lagi aktif.</p>
                </div>
            </div>
        </div>

        <!-- Registration Access Code Section (Rotates Weekly) -->
        @include('admin.partials.registration-code-card')

        <!-- Global Platform Aggregate Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Stat 1 -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pengguna</span>
                <div class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalUsers) }}</div>
                <p class="text-[10px] text-slate-400 mt-0.5">Akun user terdaftar</p>
            </div>

            <!-- Stat 2 -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
                <div class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalTransactions) }}</div>
                <p class="text-[10px] text-slate-400 mt-0.5">Termasuk dari form publik</p>
            </div>

            <!-- Stat 3 -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Pemasukan Platform</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalPlatformIncome, 0, ',', '.') }}</div>
                <p class="text-[10px] text-slate-400 mt-0.5">Akumulasi seluruh user</p>
            </div>

            <!-- Stat 4 -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Pengeluaran Platform</span>
                <div class="text-2xl font-black text-rose-600 mt-1">Rp {{ number_format($totalPlatformExpense, 0, ',', '.') }}</div>
                <p class="text-[10px] text-slate-400 mt-0.5">Akumulasi seluruh user</p>
            </div>

            <!-- Stat 5 -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <span class="text-[11px] font-bold text-teal-600 uppercase tracking-wider">Saldo Bersih Platform</span>
                <div class="text-2xl font-black {{ $totalPlatformBalance >= 0 ? 'text-teal-600' : 'text-rose-600' }} mt-1">
                    Rp {{ number_format($totalPlatformBalance, 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-slate-400 mt-0.5">Net cashflow sistem</p>
            </div>
        </div>

        <!-- Section: Distribusi Saldo Platform per Rekening Bank & Dompet -->
        <x-account-balances 
            :accounts="$platformAccountBalances" 
            title="Total Peredaran Saldo Platform per Rekening & Dompet"
            subtitle="Ringkasan total saldo, akumulasi pemasukan, dan pengeluaran seluruh pengguna sistem berdasarkan metode pembayaran."
            :showFilterLink="false"
            context="admin"
        />

        <!-- Users Table Card -->
        <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-lg">Daftar Pengguna Terdaftar</h2>
                    <p class="text-xs text-slate-500">Klik tombol "Lihat Detail" untuk memantau rincian keuangan setiap pengguna.</p>
                </div>

                <!-- Search Input -->
                <form method="GET" action="{{ route('admin.index') }}" class="flex items-center gap-2">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari nama atau email..." 
                            class="text-xs rounded-xl border-slate-200 ps-9 pe-4 py-2 w-64 focus:border-purple-500 focus:ring-purple-500"
                        >
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition">
                        Cari
                    </button>
                    @if($search)
                        <a href="{{ route('admin.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-5">Pengguna</th>
                            <th class="py-3.5 px-5">Terdaftar Sejak</th>
                            <th class="py-3.5 px-5 text-center">Jumlah Transaksi</th>
                            <th class="py-3.5 px-5">Total Pemasukan</th>
                            <th class="py-3.5 px-5">Total Pengeluaran</th>
                            <th class="py-3.5 px-5">Saldo Saat Ini</th>
                            <th class="py-3.5 px-5 text-right">Kelola & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($users as $usr)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($usr->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $usr->name }}</div>
                                            <div class="text-xs text-slate-500 font-mono">{{ $usr->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-xs text-slate-600 whitespace-nowrap">
                                    {{ $usr->created_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                        {{ $usr->transactions_count }} Data
                                    </span>
                                </td>
                                <td class="py-4 px-5 font-bold text-emerald-600 whitespace-nowrap">
                                    Rp {{ number_format($usr->total_income, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 font-bold text-rose-600 whitespace-nowrap">
                                    Rp {{ number_format($usr->total_expense, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 font-black whitespace-nowrap {{ $usr->balance >= 0 ? 'text-teal-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($usr->balance, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <!-- Lihat Detail Keuangan -->
                                        <a 
                                            href="{{ route('admin.users.show', $usr->id) }}" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 transition"
                                            title="Lihat Detail Transaksi & Budget Pengguna"
                                        >
                                            <span>👁️ Detail</span>
                                        </a>

                                        <!-- Ganti Sandi (Modal) -->
                                        <button 
                                            type="button" 
                                            @click="openPasswordModal({{ $usr->id }}, '{{ addslashes($usr->name) }}', '{{ addslashes($usr->email) }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition shadow-2xs"
                                            title="Atur / Ganti Kata Sandi Pengguna"
                                        >
                                            <span>🔑 Sandi</span>
                                        </button>

                                        <!-- Kirim Link Reset Email -->
                                        <form 
                                            method="POST" 
                                            action="{{ route('admin.users.send-reset-link', $usr->id) }}" 
                                            class="inline"
                                            onsubmit="return confirm('Kirim tautan reset kata sandi ke email {{ $usr->email }}?')"
                                        >
                                            @csrf
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 transition shadow-2xs"
                                                title="Kirim Link Reset ke Email Pengguna"
                                            >
                                                <span>✉️ Email</span>
                                            </button>
                                        </form>

                                        <!-- Hapus Pengguna (Protected) -->
                                        @if($usr->id !== auth()->id() && !$usr->isAdmin() && !in_array($usr->email, ['alqad.ri2505@gmail.com', 'alqadri2505@gmail.com']))
                                            <form 
                                                method="POST" 
                                                action="{{ route('admin.users.destroy', $usr->id) }}" 
                                                class="inline"
                                                onsubmit="return confirm('⚠️ PERINGATAN HAPUS AKUN:\nApakah Anda yakin ingin menghapus akun pengguna {{ addslashes($usr->name) }} ({{ $usr->email }})?\n\nSeluruh data transaksi, kategori, anggaran, dan catatan utang pengguna ini akan DIHAPUS PERMANEN dari sistem!')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition shadow-2xs"
                                                    title="Hapus Akun Pengguna"
                                                >
                                                    <span>🗑️ Hapus</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    Tidak ada pengguna yang sesuai dengan pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
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

    <script>
        function adminUserManagement() {
            return {
                passwordModal: false,
                selectedUser: { id: null, name: '', email: '' },
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
    </script>
</x-app-layout>
