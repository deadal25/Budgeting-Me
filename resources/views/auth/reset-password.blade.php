<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-2xl shadow-inner">
            🛡️
        </div>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Atur Ulang Kata Sandi</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">
            Buat kata sandi baru yang kuat dan aman untuk akun BudgetingMe Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Alamat Email
            </label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email', $request->email) }}" 
                required 
                autofocus 
                autocomplete="username"
                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Kata Sandi Baru
                </label>
                <button 
                    type="button" 
                    onclick="togglePasswordVisibility('password', this)"
                    class="text-xs text-slate-500 dark:text-slate-400 hover:text-emerald-500 transition"
                >
                    👁️ Lihat
                </button>
            </div>
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
            <div class="flex items-center justify-between mb-1.5">
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                    Konfirmasi Kata Sandi Baru
                </label>
                <button 
                    type="button" 
                    onclick="togglePasswordVisibility('password_confirmation', this)"
                    class="text-xs text-slate-500 dark:text-slate-400 hover:text-emerald-500 transition"
                >
                    👁️ Lihat
                </button>
            </div>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="Ulangi kata sandi baru"
                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-lg shadow-emerald-500/25 transition duration-150 text-sm flex items-center justify-center gap-2"
            >
                <span>💾 Simpan & Perbarui Kata Sandi</span>
            </button>
        </div>

        <div class="text-center pt-3 border-t border-slate-200 dark:border-slate-700/80">
            <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 hover:underline inline-flex items-center gap-1">
                <span>&larr; Kembali ke Halaman Masuk</span>
            </a>
        </div>
    </form>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerText = '🙈 Sembunyikan';
            } else {
                input.type = 'password';
                btn.innerText = '👁️ Lihat';
            }
        }
    </script>
</x-guest-layout>
