<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shadow-inner">
            🔑
        </div>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Lupa Kata Sandi?</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">
            Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan khusus agar Anda dapat membuat kata sandi baru.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Alamat Email Terdaftar
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

        <!-- Submit Button -->
        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full py-3 px-4 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-lg shadow-emerald-500/25 transition duration-150 text-sm flex items-center justify-center gap-2"
            >
                <span>✉️ Kirim Tautan Reset Kata Sandi</span>
            </button>
        </div>

        <div class="text-center pt-3 border-t border-slate-200 dark:border-slate-700/80">
            <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 hover:underline inline-flex items-center gap-1">
                <span>&larr; Kembali ke Halaman Masuk</span>
            </a>
        </div>
    </form>
</x-guest-layout>
