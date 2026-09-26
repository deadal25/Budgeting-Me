import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Theme Controller (Mode Terang & Mode Gelap)
window.initTheme = function() {
    const saved = localStorage.getItem('theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    // Default to light if nothing saved, or respect saved preference
    const isDark = saved === 'dark' || (!saved && prefersDark);
    
    if (isDark) {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
    } else {
        document.documentElement.classList.add('light');
        document.documentElement.classList.remove('dark');
    }
    
    updateThemeToggleUI(isDark);
};

window.toggleTheme = function() {
    const isDark = document.documentElement.classList.contains('dark');
    const nextTheme = isDark ? 'light' : 'dark';
    
    if (nextTheme === 'dark') {
        document.documentElement.classList.remove('light');
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
        localStorage.setItem('theme', 'light');
    }
    
    updateThemeToggleUI(nextTheme === 'dark');
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: nextTheme } }));
};

function updateThemeToggleUI(isDark) {
    document.querySelectorAll('.theme-toggle-dark-icon').forEach(el => {
        if (isDark) {
            el.classList.add('hidden');
        } else {
            el.classList.remove('hidden');
        }
    });
    document.querySelectorAll('.theme-toggle-light-icon').forEach(el => {
        if (isDark) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });
    document.querySelectorAll('.theme-toggle-text').forEach(el => {
        el.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
    });
}

// Initialize on DOM load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.initTheme();
    });
} else {
    window.initTheme();
}

Alpine.start();
