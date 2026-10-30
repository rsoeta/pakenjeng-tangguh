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
    <?php if (session()->getFlashdata('message') && is_array(session()->getFlashdata('message'))): ?>
        <div class="alert alert-warning text-center py-2 small" role="alert">
            <?= session()->getFlashdata('message')['text']; ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="post">
        <?= csrf_field() ?>
        <div class="mb-2">
            <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="<?= set_value('email'); ?>" required>
        </div>
        <div class="mb-2 position-relative">
            <input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
            <i class="fas fa-eye" id="togglePassword" style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#aaa; font-size:12px;"></i>
        </div>

        <!-- <div class="g-recaptcha" data-sitekey="6LctvBomAAAAAGjg0x7rNMuW9c5BOZfP-ev4E6b5"></div> -->
        <div class="mb-2">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAADhc27Fwr9L8O_Sb" data-theme="light" data-size="flexible"></div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Masuk</button>
        <a href="lupa-password" class="btn btn-sm btn-danger w-100 mt-2">Lupa Password</a>
    </form>

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
    }, 3000);
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
