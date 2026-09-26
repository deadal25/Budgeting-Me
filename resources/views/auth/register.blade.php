<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Akun Baru</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mulai kelola keuangan pribadi Anda secara cerdas dan teratur</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Nama Lengkap
            </label>
            <input 
                id="name" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus 
                placeholder="Contoh: Budi Santoso"
                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
            >
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Alamat Email
            </label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                placeholder="nama@email.com"
                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Email ini juga bisa digunakan untuk form transaksi publik tanpa login.</p>
        </div>

        <!-- Kode Pendaftaran Resmi (4 Huruf / Angka) -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="registration_code" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Kode Pendaftaran (4 Karakter) <span class="text-rose-500">*</span>
                </label>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-700">Wajib Diisi</span>
            </div>
            <div class="relative">
                <input 
                    id="registration_code" 
                    type="text" 
                    name="registration_code" 
                    value="{{ old('registration_code') }}" 
                    required 
                    maxlength="4"
                    autocomplete="off"
                    placeholder="CONTOH: 7B8K"
                    class="w-full px-4 py-3 rounded-xl font-mono font-bold text-center tracking-widest text-base uppercase bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
                >
            </div>
            <x-input-error :messages="$errors->get('registration_code')" class="mt-1" />
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                Harap masukkan 4 karakter resmi dari Administrator. Kode berganti otomatis setiap minggu.
            </p>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Kata Sandi
            </label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Konfirmasi Kata Sandi
            </label>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="Ulangi kata sandi"
                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-lg shadow-emerald-500/25 transition duration-150 text-sm"
            >
                Daftar Akun Sekarang
            </button>
        </div>

        <div class="text-center pt-3 border-t border-slate-200 dark:border-slate-700/80">
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 underline">
                    Masuk di Sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
