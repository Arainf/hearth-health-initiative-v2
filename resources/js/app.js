import './bootstrap';
import Alpine from 'alpinejs';
import $ from "jquery";

window.$ = window.jQuery = $;
window.Alpine = Alpine;

/* ===============================
   THEME HANDLER
   =============================== */
const root = document.documentElement;
const storedTheme = localStorage.getItem('theme');

// Set initial theme
function setInitialTheme() {
    // If user has explicitly set a preference, use it
    if (storedTheme) {
        root.setAttribute('data-theme', storedTheme);
    } 
    // Otherwise, use system preference
    else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        root.setAttribute('data-theme', 'dark');
    } else {
        root.setAttribute('data-theme', 'light');
    }
}

// Toggle between light and dark theme
window.toggleTheme = function() {
    const currentTheme = root.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    root.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
};

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', () => {
    setInitialTheme();
    
    // Watch for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('theme')) {
            const newTheme = e.matches ? 'dark' : 'light';
            root.setAttribute('data-theme', newTheme);
        }
    });
});

Alpine.start();

