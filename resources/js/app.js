import.meta.glob([
    '../fonts/**',
]);


import './bootstrap';
import Alpine from 'alpinejs';
import $ from "jquery";
import select2 from "select2";

window.$ = window.jQuery = $;
select2($);

window.Alpine = Alpine;

/* ===============================
   SELECT2 GLOBAL INIT FUNCTION
================================ */

function initSelect2(scope = document) {
    $(scope).find('select.select2').each(function () {

        if (!$(this).hasClass("select2-hidden-accessible")) {

            $(this).select2({
                width: '100%',
                height: '100%'
            });

        }

    });
}

window.initSelect2 = initSelect2;

document.addEventListener('alpine:init', () => {

    Alpine.nextTick(() => {
        initSelect2();
    });

});


/* ===============================
   THEME HANDLER
================================ */

const root = document.documentElement;

function setInitialTheme() {
    const storedTheme = localStorage.getItem('theme');

    if (storedTheme) {
        root.setAttribute('data-theme', storedTheme);
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        root.setAttribute('data-theme', 'dark');
    } else {
        root.setAttribute('data-theme', 'light');
    }
}

window.toggleTheme = function () {
    const currentTheme = root.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    root.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
};

/* ===============================
   DOM READY
================================ */

document.addEventListener('DOMContentLoaded', () => {
    setInitialTheme();
    initSelect2();

    window.matchMedia('(prefers-color-scheme: dark)')
        .addEventListener('change', e => {
            if (!localStorage.getItem('theme')) {
                root.setAttribute('data-theme', e.matches ? 'dark' : 'light');
            }
        });
});

/* ===============================
   ALPINE INTEGRATION
================================ */

document.addEventListener('alpine:initialized', () => {
    initSelect2();
});

Alpine.start();
