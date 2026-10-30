<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

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
        padding: 16px 14px;
        margin: 0;
        overflow-x: hidden;
        transition: background 0.4s, color 0.4s;
    }

    .login-box {
        width: 100% !important;
        max-width: 400px;
        margin: 0 auto !important;
        background: #fff;
        padding: 20px 22px;
        border-radius: 14px;
        border-top: 4px solid var(--green);
        box-shadow: 0 10px 30px rgba(46, 196, 182, 0.15), 0 4px 12px rgba(15, 23, 42, 0.06);
    }

    .login-logo {
        text-align: center;
        margin-bottom: 8px;
    }

    .login-logo img {
        width: 60px;
        height: 60px;
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
        margin-bottom: 12px;
    }

    .form-control {
        border-radius: 8px;
        padding: 7px 11px;
        font-size: 12.5px;
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
        padding: 9px;
        font-size: 13.5px;
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
        padding: 9px;
        font-weight: 600;
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

    footer {
        width: 100%;
        max-width: 400px;
        text-align: center;
        font-size: 11px;
        color: var(--muted);
        margin: 12px auto 0 !important;
        line-height: 1.4;
    }

    /* Dark mode */
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
        background: #2EC4B6 !important;
    }

    body.dark-mode .login-box a:not(.btn) {
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
            padding: 8px 10px;
        }

        .login-box {
            padding: 14px 14px;
            border-radius: 12px;
        }

        .login-logo {
            margin-bottom: 4px;
        }

        .login-logo img {
            width: 46px;
            height: 46px;
        }

        .subtitle {
            font-size: 11px;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 5px 9px;
            font-size: 11.5px;
            border-radius: 6px;
        }

        .mb-compact {
            margin-bottom: 6px !important;
        }

        .btn-primary {
            padding: 7px 10px;
            font-size: 12.5px;
        }

        #toggleTheme {
            top: 6px;
            right: 8px;
            padding: 3px 9px;
            font-size: 10.5px;
        }

        footer {
            font-size: 9.5px;
            margin: 8px auto 0 !important;
        }
    }
</style>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<!-- <div class="text-right mt-5 mb-2"> -->
<button id="toggleTheme" class="btn btn-sm" title="Ganti Mode">
    <i class="fas fa-moon"></i> Mode Gelap
</button>
<!-- </div> -->

<div class="login-box">
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
    <?php if (session()->get('message')): ?>
        <div class="alert alert-warning text-center py-2 small" role="alert">
            <?= session()->get('message'); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($validation)): ?>
        <div class="alert alert-danger text-center py-2 small" role="alert">
            <?= $validation->listErrors(); ?>
        </div>
    <?php endif; ?>

    <form action="/register" method="post">
        <?= csrf_field(); ?>

        <div class="mb-2 mb-compact">
            <input type="text" class="form-control form-control-sm" name="fullname" placeholder="Nama Lengkap" value="<?= set_value('fullname'); ?>" required>
        </div>

        <div class="mb-2 mb-compact">
            <input type="number" class="form-control form-control-sm" name="nik" placeholder="Nomor KTP/NIK" value="<?= set_value('nik'); ?>" required>
        </div>

        <div class="mb-2 mb-compact">
            <input type="number" class="form-control form-control-sm" name="nope" placeholder="Nomor Handphone" value="<?= set_value('nope'); ?>" required>
        </div>

        <div class="mb-2 mb-compact">
            <input type="email" class="form-control form-control-sm" name="email" placeholder="Alamat Email" value="<?= set_value('email'); ?>" required>
        </div>

        <div class="mb-2 mb-compact">
            <select class="form-control form-control-sm" name="kelurahan" required>
                <option value="">-- Pilih Desa / Kelurahan --</option>
                <?php foreach ($desa as $row): ?>
                    <option value="<?= $row['id']; ?>" <?= set_select('kelurahan', $row['id']); ?>><?= $row['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-2 mb-compact">
            <select class="form-control form-control-sm" name="no_rw" required>
                <option value="">-- Pilih RW --</option>
                <?php foreach ($datarw as $row): ?>
                    <option value="<?= $row['no_rw']; ?>" <?= set_select('no_rw', $row['no_rw']); ?>><?= $row['no_rw']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-2 mb-compact position-relative">
            <input type="password" class="form-control form-control-sm" placeholder="Password" id="password" name="password" required>
            <i class="fas fa-eye" id="togglePassword" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; color:#aaa; font-size:12px;"></i>
        </div>

        <div class="mb-2 mb-compact position-relative">
            <input type="password" class="form-control form-control-sm" placeholder="Ulangi Password" id="password_confirm" name="password_confirm" required>
            <i class="fas fa-eye" id="togglePassword2" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; color:#aaa; font-size:12px;"></i>
        </div>

        <div class="mb-2 mb-compact">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAADhc27Fwr9L8O_Sb" data-theme="light" data-size="flexible"></div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Daftar</button>
    </form>

    <p class="mt-2 mb-0 text-center">
        <a href="/login" class="text-center small">Sudah punya akun?</a>
    </p>
</div>

<footer>
    Powered by Pemerintah Desa Pasirlangu, Kec. Pakenjeng, Kab. Garut.<br>
    Mendukung implementasi Data Tunggal Sosial dan Ekonomi Nasional (DTSEN).
</footer>

<script>
    // show/hide password
    document.getElementById("togglePassword").addEventListener("click", function() {
        const input = document.getElementById("password");
        const type = input.type === "password" ? "text" : "password";
        input.type = type;
        this.classList.toggle("fa-eye-slash");
    });
    document.getElementById("togglePassword2").addEventListener("click", function() {
        const input = document.getElementById("password_confirm");
        const type = input.type === "password" ? "text" : "password";
        input.type = type;
        this.classList.toggle("fa-eye-slash");
    });

    // fade out alerts
    window.setTimeout(function() {
        document.querySelectorAll(".alert").forEach(el => {
            el.style.transition = "opacity 0.5s";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 500);
        });
    }, 3000);
</script>

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
</script>

<?= $this->endSection(); ?>
