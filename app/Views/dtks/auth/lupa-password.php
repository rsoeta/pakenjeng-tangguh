<?= $this->extend('dtks/auth/templates/index'); ?>
<?= $this->section('content'); ?>

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
        margin-bottom: 10px;
    }

    .login-logo img {
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

    .login-box a:not(.btn) {
        color: var(--green);
        font-weight: 600;
        text-decoration: none;
    }

    .login-box a:not(.btn):hover {
        color: var(--green-dark);
        text-decoration: underline;
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

    /* 🌙 Dark Mode */
    body.dark-mode {
        background: #121212 !important;
        color: #e0e0e0 !important;
    }

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

    body.dark-mode .form-control:focus {
        border-color: var(--green) !important;
    }

    body.dark-mode .btn-primary {
        background: var(--green) !important;
    }

    body.dark-mode .login-box a:not(.btn) {
        color: var(--gold) !important;
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
        color: var(--gold);
        border-color: #444;
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

    /* 📱 Mobile Compact Mode */
    @media (max-width: 576px) {
        body,
        body.login-page {
            padding: 10px 10px;
        }

        .login-box {
            padding: 16px 14px;
            border-radius: 12px;
        }

        .login-logo {
            margin-bottom: 6px;
        }

        .login-logo img {
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

        .mb-compact {
            margin-bottom: 8px !important;
        }

        .btn-primary {
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

<div class="login-box" id="elemen">
    <div class="login-logo">
        <a href="<?= base_url(); ?>">
            <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="SINDEN Logo">
        </a>
    </div>
    <div class="title"><?= $title; ?></div>
    <div class="subtitle">Masukkan data akun Anda untuk mereset password.</div>

    <?php if (session()->get('success')) : ?>
        <div class="alert alert-success text-center py-2 small"><?= session()->get('success'); ?></div>
    <?php endif; ?>

    <?php if (isset($validation)) : ?>
        <div class="alert alert-warning text-center py-2 small"><?= $validation->listErrors(); ?></div>
    <?php endif; ?>

    <form action="/requestReset" method="POST">
        <?= csrf_field(); ?>

        <div class="mb-2 mb-compact">
            <input type="text" class="form-control" name="fullname" placeholder="Masukan Nama Lengkap" value="<?= set_value('fullname'); ?>" required>
        </div>

        <div class="mb-2 mb-compact">
            <input type="text" class="form-control" name="nik" placeholder="Masukan No. KTP/NIK" value="<?= set_value('nik'); ?>" required>
        </div>

        <div class="mb-2 mb-compact">
            <input type="text" class="form-control" name="nope" placeholder="Masukan No. Handphone" value="<?= set_value('nope'); ?>" required>
        </div>

        <div class="mb-2 mb-compact">
            <input type="email" class="form-control" name="email" placeholder="Masukan Email" value="<?= set_value('email'); ?>" required>
        </div>

        <div class="mb-2 mb-compact">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAADhc27Fwr9L8O_Sb" data-theme="light" data-size="flexible"></div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Reset Password</button>

        <p class="mt-2 mb-0 text-center">
            <a class="small" href="<?= base_url('login'); ?>">Sudah punya akun? Masuk</a>
        </p>
    </form>
</div>

<footer>
    Powered by Pemerintah Desa Pasirlangu, Kec. Pakenjeng, Kab. Garut.<br>
    Mendukung implementasi Data Tunggal Sosial dan Ekonomi Nasional (DTSEN).
</footer>

<script>
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

    window.setTimeout(function() {
        document.querySelectorAll(".alert").forEach(el => {
            el.style.transition = "opacity 0.5s";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 500);
        });
    }, 3000);
</script>

<?php if (session()->getFlashdata('message')) : ?>
    <script>
        Swal.fire({
            icon: '<?= session()->getFlashdata('message')['type'] ?>',
            title: '<?= session()->getFlashdata('message')['type'] === 'success' ? 'Berhasil' : 'Gagal' ?>',
            text: '<?= session()->getFlashdata('message')['text'] ?>',
            timer: 3000,
            showConfirmButton: false
        }).then(() => {
            <?php if (session()->getFlashdata('message')['type'] === 'success') : ?>
                window.location.href = 'https://mail.google.com/';
            <?php endif; ?>
        });
    </script>
<?php endif; ?>

<?= $this->endSection(); ?>
