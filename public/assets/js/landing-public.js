(function () {
    'use strict';

    // Dark-mode toggle (landing)
    var body = document.body;
    var toggleBtn = document.getElementById('toggleTheme');
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
    }
    if (toggleBtn) {
        if (body.classList.contains('dark-mode')) {
            toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
        }

        toggleBtn.addEventListener('click', function () {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
                toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                localStorage.setItem('theme', 'light');
                toggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
            }
        });
    }

    // Article Copy Link button
    var btnCopy = document.getElementById('btnCopyLink');
    var toast = document.getElementById('toastCopy');
    var toastTimeout = null;

    if (btnCopy) {
        btnCopy.addEventListener('click', async function () {
            var titleEl = document.querySelector('.article-title');
            var title = btnCopy.getAttribute('data-title') || (titleEl ? titleEl.innerText.trim() : '');
            var url = btnCopy.getAttribute('data-url') || window.location.href;
            var textToCopy = title ? title + '\n\n' + url : url;
            var success = false;

            if (navigator.clipboard && window.isSecureContext) {
                try {
                    await navigator.clipboard.writeText(textToCopy);
                    success = true;
                } catch (err) {
                    success = false;
                }
            }

            if (!success) {
                var tempInput = document.createElement('textarea');
                tempInput.value = textToCopy;
                tempInput.className = 'sr-copy-helper';
                document.body.appendChild(tempInput);
                tempInput.focus();
                tempInput.select();
                try {
                    success = document.execCommand('copy');
                } catch (err) {
                    success = false;
                }
                document.body.removeChild(tempInput);
            }

            var copyIcon = document.getElementById('copyIcon');
            var copyText = document.getElementById('copyText');
            if (copyIcon && copyText) {
                copyIcon.className = 'fas fa-check';
                copyText.textContent = 'Link Disalin!';
                btnCopy.classList.add('copied');

                if (toast) {
                    toast.classList.add('show');
                    if (toastTimeout) {
                        clearTimeout(toastTimeout);
                    }
                    toastTimeout = setTimeout(function () {
                        toast.classList.remove('show');
                    }, 2800);
                }

                setTimeout(function () {
                    copyIcon.className = 'fas fa-link';
                    copyText.textContent = 'Salin Link';
                    btnCopy.classList.remove('copied');
                }, 2500);
            }
        });
    }

    // Lockscreen back button
    var btnLockBack = document.getElementById('btnLockBack');
    if (btnLockBack) {
        btnLockBack.addEventListener('click', function () {
            window.history.go(-1);
        });
    }

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
