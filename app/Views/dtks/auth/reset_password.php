<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

<!-- Script & Fonts -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<link href="<?= base_url('assets/font/Poppins/poppins.css'); ?>" rel="stylesheet">

<style>
    :root {
        --green: #2EC4B6;
        --gold: #FFBE0B;
        --muted: #6b7280;
        --bg: #f7fafc;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(180deg, #f1fbfa 0%, #ffffff 50%);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .reset-box {
        max-width: 420px;
        margin: auto;
        background: #fff;
        padding: 32px 28px;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
    }

    .reset-logo {
        text-align: center;
        margin-bottom: 16px;
    }

    .reset-logo img {
        width: 90px;
        height: 90px;
    }

    .title {
        text-align: center;
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--green);
    }

    .subtitle {
        text-align: center;
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 24px;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 14px;
    }

    .btn-success {
        background: var(--green);
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
    }

    .btn-danger {
        background: #FF6B6B;
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
    }

    footer {
        text-align: center;
        font-size: 12px;
        color: var(--muted);
        margin-top: 32px;
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
    }

    body.dark-mode .btn-success {
        background: #2EC4B6 !important;
    }

    body.dark-mode a {
        color: #FFBE0B !important;
    }
</style>

<div class="text-right mb-2">
    <button id="toggleTheme" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-moon"></i> Mode Gelap
    </button>
</div>

<div class="reset-box">
    <div class="reset-logo">
        <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="SINDEN Logo">
    </div>
    <div class="title">Reset Password</div>
    <div class="subtitle">Silakan masukkan password baru Anda</div>

    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert text-center <?= session()->getFlashdata('message')['type'] === 'success' ? 'alert-success' : 'alert-danger' ?>">
            <?= session()->getFlashdata('message')['text']; ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('reset-password'); ?>" method="POST">
        <?= csrf_field(); ?>
        <input type="hidden" name="token" value="<?= esc($token); ?>">

        <div class="mb-3 position-relative">
            <input type="password" class="form-control" name="password" id="password" placeholder="Password baru" required>
            <i class="fas fa-eye" id="togglePassword" style="position:absolute; right:16px; top:14px; cursor:pointer; color:#aaa"></i>
        </div>

        <div class="mb-3 position-relative">
            <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Ulangi password" required>
            <i class="fas fa-eye" id="togglePasswordConfirm" style="position:absolute; right:16px; top:14px; cursor:pointer; color:#aaa"></i>
        </div>

        <button type="submit" class="btn btn-success w-100">Reset Password</button>
        <a href="<?= base_url('login'); ?>" class="btn btn-sm btn-danger w-100 mt-2">Kembali ke Login</a>
    </form>
</div>

<footer>
    Dikembangkan oleh Pemerintah Desa Pasirlangu, Kecamatan Pakenjeng, Kabupaten Garut.
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