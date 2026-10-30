<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

<!-- Script & Fonts -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
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

    .reset-box {
        width: 100% !important;
        max-width: 400px;
        margin: 0 auto !important;
        background: #fff;
        padding: 24px 24px;
        border-radius: 14px;
        border-top: 4px solid var(--green);
        box-shadow: 0 10px 30px rgba(46, 196, 182, 0.15), 0 4px 12px rgba(15, 23, 42, 0.06);
    }

    .reset-logo {
        text-align: center;
        margin-bottom: 10px;
    }

    .reset-logo img {
        width: 68px;
        height: 68px;
    }

    .title {
        text-align: center;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 4px;
        color: var(--green);
    }

    .subtitle {
        text-align: center;
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 16px;
    }

    .form-control {
        border-radius: 8px;
        padding: 9px 12px;
        font-size: 13px;
        border: 1px solid #d1d5db;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(46, 196, 182, 0.25);
        outline: none;
    }

    .btn-success {
        background: var(--green) !important;
        border: none !important;
        border-radius: 8px;
        padding: 10px;
        font-size: 14px;
        font-weight: 600;
        color: #fff !important;
        transition: background 0.2s;
    }

    .btn-success:hover {
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

    .cf-turnstile {
        width: 100% !important;
        max-width: 100% !important;
        display: flex;
        justify-content: center;
        overflow: hidden;
    }

    .cf-turnstile iframe {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
    }

    footer {
        width: 100%;
        max-width: 400px;
        text-align: center;
        font-size: 11px;
        color: var(--muted);
        margin: 16px auto 0 !important;
        line-height: 1.4;
    }

    /* Dark mode */
    body.dark-mode {
        background: #121212 !important;
        color: #e0e0e0 !important;
    }

    body.dark-mode .reset-box {
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

    body.dark-mode .form-control:focus {
        border-color: var(--green) !important;
    }

    body.dark-mode .btn-success {
        background: #2EC4B6 !important;
    }

    body.dark-mode .reset-box a:not(.btn) {
        color: #FFBE0B !important;
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

    body.dark-mode #toggleTheme {
        background: #1e1e1e;
        color: #FFBE0B;
        border-color: #444;
    }

    /* 📱 Mobile Compact Mode */
    @media (max-width: 576px) {
        body,
        body.login-page {
            padding: 10px 10px;
        }

        .reset-box {
            padding: 16px 14px;
            border-radius: 12px;
        }

        .reset-logo {
            margin-bottom: 6px;
        }

        .reset-logo img {
            width: 52px;
            height: 52px;
        }

        .title {
            font-size: 16px;
            margin-bottom: 2px;
        }

        .subtitle {
            font-size: 11px;
            margin-bottom: 10px;
        }

        .form-control {
            padding: 7px 10px;
            font-size: 12px;
            border-radius: 7px;
        }

        .btn-success {
            padding: 8px 10px;
            font-size: 13px;
        }

        #toggleTheme {
            top: 8px;
            right: 10px;
            padding: 4px 10px;
            font-size: 11px;
        }

        footer {
            font-size: 10px;
            margin: 10px auto 0 !important;
        }
    }
</style>

<button id="toggleTheme" class="btn btn-sm" title="Ganti Mode">
    <i class="fas fa-moon"></i> Mode Gelap
</button>

<div class="reset-box">
    <div class="reset-logo">
        <a href="<?= base_url(); ?>">
            <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="SINDEN Logo">
        </a>
    </div>
    <div class="title">Reset Password</div>
    <div class="subtitle">Silakan masukkan password baru Anda</div>

    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert text-center py-2 small <?= session()->getFlashdata('message')['type'] === 'success' ? 'alert-success' : 'alert-danger' ?>">
            <?= session()->getFlashdata('message')['text']; ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('reset-password'); ?>" method="POST">
        <?= csrf_field(); ?>
        <input type="hidden" name="token" value="<?= esc($token); ?>">

        <div class="mb-2 position-relative">
            <input type="password" class="form-control" name="password" id="password" placeholder="Password baru" required>
            <i class="fas fa-eye" id="togglePassword" style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#aaa; font-size:12px;"></i>
        </div>

        <div class="mb-2 position-relative">
            <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Ulangi password" required>
            <i class="fas fa-eye" id="togglePasswordConfirm" style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#aaa; font-size:12px;"></i>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-1">Reset Password</button>
        <a href="<?= base_url('login'); ?>" class="btn btn-sm btn-danger w-100 mt-2">Kembali ke Login</a>
    </form>
</div>

<footer>
    Powered by Pemerintah Desa Pasirlangu, Kec. Pakenjeng, Kab. Garut.
    <br>
    Mendukung implementasi Data Tunggal Sosial dan Ekonomi Nasional (DTSEN).
</footer>

<script>
    // Toggle Password Visibility
    document.getElementById("togglePassword").addEventListener("click", function() {
        const pwd = document.getElementById("password");
        pwd.type = pwd.type === "password" ? "text" : "password";
        this.classList.toggle("fa-eye-slash");
    });

    document.getElementById("togglePasswordConfirm").addEventListener("click", function() {
        const pwd2 = document.getElementById("password_confirm");
        pwd2.type = pwd2.type === "password" ? "text" : "password";
        this.classList.toggle("fa-eye-slash");
    });

    // Auto fade alert
    window.setTimeout(function() {
        document.querySelectorAll(".alert").forEach(el => {
            el.style.transition = "opacity 0.5s";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 500);
        });
    }, 3000);

    // Dark Mode Toggle
    document.addEventListener("DOMContentLoaded", function() {
        const body = document.body;
        const toggleBtn = document.getElementById("toggleTheme");
        const theme = localStorage.getItem("theme");

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
