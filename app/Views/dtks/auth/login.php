<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>
<!-- captha -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<!-- <script src="https://www.google.com/recaptcha/api.js"></script> -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<!-- render fonts poppins -->
<link href="<?= base_url('assets/font/Poppins/poppins.css'); ?>" rel="stylesheet">
<style>
    :root {
        --green: #2EC4B6;
        --green-dark: #25a99d;
        --gold: #FFBE0B;
        --muted: #6b7280;
        --bg: #f9fafa;
    }

    body,
    body.login-page {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(180deg, rgba(46, 196, 182, 0.45) 0%, var(--bg) 100%) !important;
        height: auto !important;
        min-height: 100vh !important;
        min-height: 100dvh !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 20px 14px;
        margin: 0;
        overflow-x: hidden;
        transition: background 0.4s, color 0.4s;
    }

    .login-box {
        width: 100% !important;
        max-width: 400px;
        margin: 0 auto !important;
        background: #fff;
        padding: 24px 24px;
        border-radius: 14px;
        border-top: 4px solid var(--green);
        box-shadow: 0 10px 30px rgba(46, 196, 182, 0.15), 0 4px 12px rgba(15, 23, 42, 0.06);
    }

    .login-logo {
        text-align: center;
        margin-bottom: 12px;
    }

    .login-logo img {
        width: 72px;
        height: 72px;
    }

    .title {
        text-align: center;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--green);
    }

    .subtitle {
        text-align: center;
        font-size: 12.5px;
        color: var(--muted);
        margin-bottom: 18px;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13.5px;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(46, 196, 182, 0.25);
        outline: none;
    }

    .login-box .form-floating > .form-control {
        height: 56px;
        padding: 1.25rem 0.75rem 0.35rem;
    }

    .login-box .form-floating > label {
        padding: 1rem 0.75rem;
        color: var(--muted);
        font-size: 13px;
    }

    .login-box .form-floating > .form-control:focus ~ label,
    .login-box .form-floating > .form-control:not(:placeholder-shown) ~ label {
        top: 0;
        height: auto;
        padding: 0 0.35rem;
        transform: translate(0.15rem, -50%) scale(0.85);
        opacity: 0.85;
        background-color: #fff;
    }

    .login-box .password-field > .form-control {
        padding-right: 2.5rem;
    }

    .login-box .password-field > label {
        padding-right: 2.5rem;
    }

    .login-box .password-toggle {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #aaa;
        font-size: 12px;
        z-index: 5;
    }

    .btn-primary {
        background: var(--green) !important;
        border: none !important;
        border-radius: 8px;
        padding: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #fff !important;
        transition: background 0.2s;
    }

    .btn-primary:hover {
        background: var(--green-dark) !important;
    }

    .btn-danger {
        background: #FF6B6B;
        border: none;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 13px;
        font-weight: 600;
    }

    .btn-danger:hover {
        background: #ff5252;
    }

    .login-box a:not(.btn) {
        color: var(--green);
        font-weight: 600;
        text-decoration: none;
    }

    .login-box a:not(.btn):hover {
        color: var(--green-dark);
        text-decoration: underline;
    }

    .auth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 14px 0 12px;
        color: var(--muted);
        font-size: 12px;
    }

    .auth-divider::before,
    .auth-divider::after {
        content: "";
        flex: 1;
        border-bottom: 1px solid #e5e7eb;
    }

    .auth-divider span {
        padding: 0 10px;
    }

    .btn-google {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #ffffff;
        color: #1f2937;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none !important;
        transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
    }

    .btn-google:hover,
    .btn-google:focus {
        background: #f9fafb;
        color: #111827;
        border-color: #9ca3af;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    }

    .google-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    .google-whitelist-note {
        text-align: center;
        font-size: 11px;
        color: var(--muted);
        margin-top: 6px;
    }

    .cf-turnstile {
        width: 100% !important;
        max-width: 100% !important;
        display: flex;
        justify-content: center;
    }

    .cf-turnstile > div,
    .cf-turnstile > div > div,
    .cf-turnstile iframe {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }

    #toggleTheme {
        position: fixed;
        top: 12px;
        right: 14px;
        z-index: 100;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(46, 196, 182, 0.4);
        color: #135d54;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 500;
        backdrop-filter: blur(6px);
    }

    #toggleTheme:hover {
        background: var(--green);
        color: #fff;
    }

    footer {
        width: 100%;
        max-width: 400px;
        text-align: center;
        font-size: 11px;
        color: var(--muted);
        margin: 18px auto 0 !important;
        line-height: 1.4;
    }

    /* 📱 Mobile Compact Mode */
    @media (max-width: 576px) {
        body,
        body.login-page {
            padding: 10px 10px;
        }

        .login-box {
            padding: 18px 14px;
            border-radius: 12px;
        }

        .login-logo {
            margin-bottom: 8px;
        }

        .login-logo img {
            width: 56px;
            height: 56px;
        }

        .subtitle {
            font-size: 11.5px;
            margin-bottom: 12px;
        }

        .form-control {
            padding: 8px 10px;
            font-size: 12.5px;
            border-radius: 7px;
        }

        .login-box .form-floating > .form-control {
            height: 54px;
            padding: 1.2rem 0.65rem 0.3rem;
        }

        .login-box .form-floating > label {
            padding: 0.95rem 0.65rem;
            font-size: 12.5px;
        }

        .login-box .password-field > .form-control {
            padding-right: 2.35rem;
        }

        .login-box .password-field > label {
            padding-right: 2.35rem;
        }

        .btn-primary {
            padding: 8px 10px;
            font-size: 13px;
        }

        .auth-divider {
            margin: 10px 0 9px;
            font-size: 11px;
        }

        .btn-google {
            padding: 8px 10px;
            font-size: 12.5px;
            gap: 8px;
        }

        .google-whitelist-note {
            font-size: 10.5px;
        }

        #toggleTheme {
            top: 8px;
            right: 10px;
            padding: 4px 10px;
            font-size: 11px;
        }

        footer {
            font-size: 10px;
            margin: 12px auto 0 !important;
        }
    }
</style>
<style>
    body.dark-mode {
        background: #121212 !important;
        color: #e0e0e0 !important;
    }

    body.dark-mode .card,
    body.dark-mode .login-box {
        background: #1e1e1e !important;
        color: #ddd !important;
        border: 1px solid #333;
        border-top: 4px solid var(--green);
    }

    body.dark-mode .form-control {
        background: #2b2b2b !important;
        color: #eee !important;
        border-color: #444 !important;
    }

    body.dark-mode .login-box .form-floating > .form-control:focus ~ label,
    body.dark-mode .login-box .form-floating > .form-control:not(:placeholder-shown) ~ label {
        background-color: #2b2b2b;
    }

    body.dark-mode .form-control:focus {
        border-color: var(--green) !important;
    }

    body.dark-mode .btn-primary {
        background: #2EC4B6 !important;
    }

    body.dark-mode .login-box a:not(.btn) {
        color: #FFBE0B !important;
    }

    body.dark-mode #toggleTheme {
        background: #1e1e1e;
        color: #FFBE0B;
        border-color: #444;
    }

    body.dark-mode .auth-divider {
        color: #9ca3af;
    }

    body.dark-mode .auth-divider::before,
    body.dark-mode .auth-divider::after {
        border-color: #374151;
    }

    body.dark-mode .btn-google {
        background: #2b2b2b;
        color: #f3f4f6;
        border-color: #444;
    }

    body.dark-mode .btn-google:hover,
    body.dark-mode .btn-google:focus {
        background: #333;
        color: #ffffff;
        border-color: #6b7280;
    }

    body.dark-mode .google-whitelist-note {
        color: #9ca3af;
    }
</style>


<button id="toggleTheme" class="btn btn-sm">
    <i class="fas fa-moon"></i> Mode Gelap
</button>

<div class="login-box">
    <!-- <a class="login-logo" href="<?= base_url(); ?>">
        <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="SINDEN Logo">
    </a> -->
    <div class="login-logo">
        <a href="<?= base_url(); ?>">
            <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="SINDEN Logo">
        </a>
    </div>
    <!-- <div class="title">SINDEN</div> -->
    <div class="subtitle">Sistem Informasi Data Ekonomi dan Sosial Desa</div>

    <?php if (session()->get('success')): ?>
        <div class="alert alert-success text-center py-2 small" role="alert">
            <?= session()->get('success'); ?>
        </div>
    <?php endif; ?>
    <?php $flashMessage = session()->getFlashdata('message'); ?>
    <?php if (is_array($flashMessage) && !empty($flashMessage['text'])): ?>
        <?php
        $alertClass = match ($flashMessage['type'] ?? 'warning') {
            'success' => 'alert-success',
            'error'   => 'alert-danger',
            default   => 'alert-warning',
        };
        ?>
        <div class="alert <?= $alertClass; ?> text-center py-2 small" role="alert">
            <?= esc($flashMessage['text']); ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="post">
        <?= csrf_field() ?>
        <div class="form-floating mb-2">
            <input type="email" class="form-control" placeholder=" " id="email" name="email" value="<?= set_value('email'); ?>" required>
            <label for="email">Email</label>
        </div>
        <div class="form-floating mb-2 position-relative password-field">
            <input type="password" class="form-control" placeholder=" " id="password" name="password" required>
            <label for="password">Password</label>
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
        </div>

        <!-- <div class="g-recaptcha" data-sitekey="6LctvBomAAAAAGjg0x7rNMuW9c5BOZfP-ev4E6b5"></div> -->
        <div class="mb-2">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAADhc27Fwr9L8O_Sb" data-theme="light" data-size="flexible"></div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Masuk</button>
        <a href="lupa-password" class="btn btn-sm btn-danger w-100 mt-2">Lupa Password</a>
    </form>

    <div class="auth-divider"><span>atau</span></div>

    <a href="<?= base_url('auth/google'); ?>" class="btn btn-google w-100" id="btnGoogleLogin">
        <svg class="google-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.24 1.25-.96 2.31-2.04 3.02l3.3 2.56c1.92-1.77 3.04-4.38 3.04-7.48 0-.72-.06-1.41-.19-2.08H12z"/>
            <path fill="#34A853" d="M12 22c2.7 0 4.96-.9 6.62-2.42l-3.3-2.56c-.91.61-2.08.98-3.32.98-2.55 0-4.71-1.72-5.48-4.04l-3.41 2.63C4.76 19.86 8.12 22 12 22z"/>
            <path fill="#4A90E2" d="M6.52 13.96A5.98 5.98 0 0 1 6.2 12c0-.68.12-1.34.32-1.96L3.11 7.41A10 10 0 0 0 2 12c0 1.61.39 3.13 1.11 4.59l3.41-2.63z"/>
            <path fill="#FBBC05" d="M12 6c1.47 0 2.79.51 3.83 1.5l2.87-2.87C16.95 3 14.7 2 12 2 8.12 2 4.76 4.14 3.11 7.41l3.41 2.63C7.29 7.72 9.45 6 12 6z"/>
        </svg>
        <span>Masuk dengan Google</span>
    </a>
    <div class="google-whitelist-note">Khusus email terdaftar pada akun SINDEN</div>

    <p class="mt-2 mb-0 text-center">
        <a href="/register" class="text-center small">Register a new membership</a>
    </p>
</div>

<footer>
    Powered by Pemerintah Desa Pasirlangu, Kec. Pakenjeng, Kab. Garut.
    <br>
    Mendukung implementasi Data Tunggal Sosial dan Ekonomi Nasional (DTSEN).
</footer>

<script>
    document.getElementById("togglePassword").addEventListener("click", function() {
        const passwordInput = document.getElementById("password");
        const type = passwordInput.type === "password" ? "text" : "password";
        passwordInput.type = type;
        this.classList.toggle("fa-eye-slash");
    });

    window.setTimeout(function() {
        document.querySelectorAll(".alert").forEach(el => {
            el.style.transition = "opacity 0.5s";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 500);
        });
    }, 5000);
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const body = document.body;
        const toggleBtn = document.getElementById("toggleTheme");
        const theme = localStorage.getItem("theme");

        // apply saved theme
        if (theme === "dark") {
            body.classList.add("dark-mode");
            toggleBtn.innerHTML = '<i class="fas fa-sun"></i> Mode Terang';
        }

        toggleBtn.addEventListener("click", function() {
            body.classList.toggle("dark-mode");
            if (body.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                toggleBtn.innerHTML = '<i class="fas fa-sun"></i> Mode Terang';
            } else {
                localStorage.setItem("theme", "light");
                toggleBtn.innerHTML = '<i class="fas fa-moon"></i> Mode Gelap';
            }
        });
    });
</script>


<?= $this->endSection(); ?>
