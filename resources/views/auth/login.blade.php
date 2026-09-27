<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Masuk ke Akun Anda</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Akses dashboard, riwayat transaksi, dan limit Budgetinku</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Quick Credentials Selector -->
    <div class="mb-6 p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700/80 text-xs space-y-2">
        <button 
            type="button" 
            onclick="fillLogin('alqad.ri2505@gmail.com', 'password')"
            class="w-full py-2 px-3 rounded-xl bg-indigo-500/15 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 hover:bg-indigo-500/25 border border-indigo-500/30 font-bold text-xs transition flex items-center justify-center gap-2"
        >
            <span>👑 1-Klik Masuk Akun Admin (Alqadri)</span>
        </button>
        <button 
            type="button" 
            onclick="fillLogin('user@budgetingme.com', 'password')"
            class="w-full py-2 px-3 rounded-xl bg-emerald-500/15 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-500/25 border border-emerald-500/30 font-bold text-xs transition flex items-center justify-center gap-2"
        >
            <span>👤 1-Klik Isi Akun Demo (Budi Santoso)</span>
        </button>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

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
                autofocus 
                placeholder="nama@email.com"
                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Kata Sandi
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 hover:underline">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>
            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-xs">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-emerald-500 shadow-xs focus:ring-emerald-500" name="remember">
                <span class="ms-2 text-slate-600 dark:text-slate-400">Ingat saya</span>
            </label>
            <a href="/" class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                Isi form publik?
            </a>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-lg shadow-emerald-500/25 transition duration-150 text-sm"
            >
                Masuk Sekarang
            </button>
        </div>

        <div class="text-center pt-3 border-t border-slate-200 dark:border-slate-700/80">
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 underline">
                    Daftar Akun Baru
                </a>
            </p>
        </div>
    </form>

    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>
