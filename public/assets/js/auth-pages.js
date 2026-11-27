(function () {
    'use strict';

    var body = document.body;

    // Show/hide password fields cleanly across login, register, and reset-password
    var passwordTogglePairs = [
        { toggleId: 'togglePassword', inputId: 'password' },
        { toggleId: 'togglePassword2', inputId: 'password_confirm' },
        { toggleId: 'togglePasswordConfirm', inputId: 'password_confirm' }
    ];

    passwordTogglePairs.forEach(function (pair) {
        var toggleEl = document.getElementById(pair.toggleId);
        var inputEl = document.getElementById(pair.inputId);
        if (toggleEl && inputEl) {
            toggleEl.addEventListener('click', function () {
                inputEl.type = inputEl.type === 'password' ? 'text' : 'password';
                toggleEl.classList.toggle('fa-eye-slash');
            });
        }
    });

    // Dark-mode toggle (auth pages)
    document.addEventListener('DOMContentLoaded', function () {
        var toggleBtn = document.getElementById('toggleTheme');
        var theme = localStorage.getItem('theme');

        if (theme === 'dark') {
            body.classList.add('dark-mode');
            if (toggleBtn) {
                toggleBtn.innerHTML = '<i class="fas fa-sun"></i> Mode Terang';
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                    toggleBtn.innerHTML = '<i class="fas fa-sun"></i> Mode Terang';
                } else {
                    localStorage.setItem('theme', 'light');
                    toggleBtn.innerHTML = '<i class="fas fa-moon"></i> Mode Gelap';
                }
            });
        }

        // Auto-fade alerts
        var authBox = document.querySelector('[data-alert-fade]');
        var fadeDelay = authBox ? parseInt(authBox.getAttribute('data-alert-fade') || '3000', 10) : 3000;
        window.setTimeout(function () {
            document.querySelectorAll('.alert').forEach(function (el) {
                el.classList.add('auth-alert-fade-out');
                setTimeout(function () {
                    el.remove();
                }, 500);
            });
        }, fadeDelay);

        // Flashdata SweetAlert trigger (lupa-password)
        var flashEl = document.getElementById('authFlashSwal');
        if (flashEl && typeof Swal !== 'undefined') {
            var icon = flashEl.getAttribute('data-icon') || 'info';
            var title = flashEl.getAttribute('data-title') || '';
            var text = flashEl.getAttribute('data-text') || '';
            var redirectUrl = flashEl.getAttribute('data-redirect') || '';

            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                timer: 3000,
                showConfirmButton: false
            }).then(function () {
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            });
        }
    });

    // PWA Service Worker registration
    var swUrl = body ? body.getAttribute('data-sw-url') : '';
    if (swUrl && 'serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register(swUrl).catch(function (err) {
                console.warn('[PWA] SW registration warning:', err);
            });
        });
    }
})();
