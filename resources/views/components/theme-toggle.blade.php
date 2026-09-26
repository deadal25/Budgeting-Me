@props(['showLabel' => false, 'class' => ''])

<button 
    type="button" 
    onclick="window.toggleTheme()" 
    aria-label="Ganti Tema Terang atau Gelap"
    class="theme-toggle-btn inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 cursor-pointer border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-xs {{ $class }}"
    title="Ganti Tema (Terang / Gelap)"
>
    <!-- Moon Icon (shown in Light mode) -->
    <svg class="theme-toggle-dark-icon w-4 h-4 text-slate-600 dark:text-slate-300 transition-transform duration-300 hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>

    <!-- Sun Icon (shown in Dark mode) -->
    <svg class="theme-toggle-light-icon w-4 h-4 text-amber-400 hidden transition-transform duration-300 hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>

    @if($showLabel)
        <span class="theme-toggle-text text-xs font-semibold">Mode Gelap</span>
    @endif
</button>
