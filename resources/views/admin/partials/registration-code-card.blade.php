@if(isset($registrationCodeDetails) && $registrationCodeDetails)
    <div class="rounded-3xl p-6 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white shadow-xl border border-indigo-500/30 relative overflow-hidden" x-data="{ copied: false, showCustomForm: false }">
        <!-- Background subtle glow decoration -->
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-indigo-900/60 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600/30 border border-indigo-400/40 text-indigo-300 flex items-center justify-center text-xl shrink-0 shadow-inner">
                        🔐
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-black text-base sm:text-lg text-white tracking-tight">Kode Akses Registrasi User Baru</h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Aktif
                            </span>
                        </div>
                        <p class="text-xs text-indigo-200/80 mt-0.5">
                            Wajib dimasukkan oleh calon pengguna saat mendaftar akun baru di aplikasi.
                        </p>
                    </div>
                </div>

                <!-- Auto-rotation Badge -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-900/50 border border-indigo-700/50 text-indigo-200 text-xs font-semibold self-start sm:self-auto">
                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Rotasi Otomatis Setiap 7 Hari (1 Minggu)</span>
                </div>
            </div>

            <!-- Body: Code Display & Expiry Info -->
            <div class="mt-5 grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
                <!-- Large Code Hero Display -->
                <div class="lg:col-span-6 bg-black/40 border border-indigo-500/30 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-inner">
                    <div class="text-center sm:text-left">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-300 block mb-1">Kode 4 Karakter Aktif</span>
                        <div class="font-mono text-3xl sm:text-4xl font-black text-amber-300 tracking-[0.35em] drop-shadow-md select-all">
                            {{ $registrationCodeDetails['code'] }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Copy Button -->
                        <button 
                            type="button"
                            @click="navigator.clipboard.writeText('{{ $registrationCodeDetails['code'] }}'); copied = true; setTimeout(() => copied = false, 2500)"
                            class="px-4 py-2.5 rounded-xl text-xs font-black bg-indigo-600 hover:bg-indigo-500 text-white shadow-md transition flex items-center gap-2 active:scale-95"
                            :class="copied ? 'bg-emerald-600 hover:bg-emerald-500' : ''"
                            title="Salin Kode ke Clipboard"
                        >
                            <template x-if="!copied">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                    Salin Kode
                                </span>
                            </template>
                            <template x-if="copied">
                                <span class="flex items-center gap-1.5 text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Tersalin!
                                </span>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- Expiration Countdown & Details -->
                <div class="lg:col-span-6 space-y-2 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-indigo-950/60 border border-indigo-900/80">
                        <span class="text-indigo-300">Sisa Masa Berlaku:</span>
                        <span class="font-bold text-amber-300 bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">
                            ⏳ {{ $registrationCodeDetails['remaining_text'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-indigo-950/60 border border-indigo-900/80">
                        <span class="text-indigo-300">Jadwal Rotasi Berikutnya:</span>
                        <span class="font-semibold text-slate-200">
                            {{ $registrationCodeDetails['expires_at_formatted'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-indigo-950/60 border border-indigo-900/80">
                        <span class="text-indigo-300">Terakhir Diperbarui:</span>
                        <span class="font-semibold text-slate-200">
                            {{ $registrationCodeDetails['last_generated_at_formatted'] }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Notice note -->
            <div class="mt-4 p-3 rounded-xl bg-indigo-950/40 border border-indigo-900/40 text-[11px] text-indigo-200/90 leading-relaxed flex items-start gap-2">
                <svg class="w-4 h-4 text-indigo-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>
                    <strong>Keamanan Registrasi:</strong> Tanpa kode 4 huruf/angka ini, pengunjung luar tidak dapat membuat akun baru di form registrasi publik. Setiap 7 hari tepat, kode akan diacak ulang secara otomatis oleh sistem.
                </span>
            </div>

            <!-- Action Controls (Regenerate & Custom) -->
            <div class="mt-5 pt-4 border-t border-indigo-900/60 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Regenerate Random Code Button -->
                    <form method="POST" action="{{ route('admin.registration-code.regenerate') }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengacak kode baru sekarang? Kode sebelumnya akan segera kedaluwarsa dan tidak dapat digunakan lagi.')">
                        @csrf
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-700 hover:bg-indigo-600 text-white transition flex items-center gap-1.5 shadow-sm active:scale-95"
                        >
                            <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Acak Ulang Kode Sekarang (Random 4 Karakter)</span>
                        </button>
                    </form>

                    <!-- Toggle Custom Code Form Button -->
                    <button 
                        type="button" 
                        @click="showCustomForm = !showCustomForm" 
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/15 text-white border border-white/20 transition flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span x-text="showCustomForm ? 'Tutup Atur Manual' : 'Atur Kode Tertentu Manual'"></span>
                    </button>
                </div>
            </div>

            <!-- Custom Code Inline Form (Collapsible) -->
            <div x-show="showCustomForm" x-transition class="mt-4 pt-4 border-t border-indigo-900/60 bg-black/25 p-4 rounded-2xl">
                <form method="POST" action="{{ route('admin.registration-code.update') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    @csrf
                    <div class="flex-1">
                        <label for="custom_code" class="block text-xs font-bold text-indigo-200 mb-1">
                            Masukkan 4 Huruf / Angka Pilihan Anda:
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="custom_code" 
                                name="custom_code" 
                                maxlength="4" 
                                placeholder="Contoh: A9X2" 
                                required
                                class="w-full uppercase font-mono tracking-widest text-center text-sm font-bold bg-slate-900 border border-indigo-500/50 rounded-xl px-4 py-2 text-amber-300 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 placeholder:text-slate-500"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 4)"
                            >
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">
                            *Tepat 4 karakter alfanumerik. Masa berlaku 7 hari akan direset terhitung mulai hari ini.
                        </span>
                    </div>

                    <button 
                        type="submit" 
                        class="sm:mt-4 px-5 py-2.5 rounded-xl text-xs font-black bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-md transition flex items-center justify-center gap-1.5 self-end sm:self-auto"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Kode Manual</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif
