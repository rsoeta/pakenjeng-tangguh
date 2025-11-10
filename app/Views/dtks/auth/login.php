<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>
<!-- captha -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>

<!-- render fonts poppins -->
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

    .login-box {
        max-width: 420px;
        margin: auto;
        background: #fff;
        padding: 32px 28px;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
    }

    .login-logo {
        text-align: center;
        margin-bottom: 16px;
    }

    .login-logo img {
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

    .btn-primary {
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
    }

    body.dark-mode .btn-primary {
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

<div class="login-box">
    <!-- <a class="login-logo" href="<?= base_url(); ?>">
        <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="SINDEN Logo">
    </a> -->
    <div class="login-logo">
        <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="SINDEN Logo">
    </div>
    <div class="title">SINDEN</div>
    <div class="subtitle">Sistem Informasi Data Ekonomi dan Sosial Desa</div>

    <?php if (session()->get('success')): ?>
        <div class="alert alert-success text-center" role="alert">
            <?= session()->get('success'); ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('message') && is_array(session()->getFlashdata('message'))): ?>
        <div class="alert alert-warning text-center" role="alert">
            <?= session()->getFlashdata('message')['text']; ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="post">
        <div class="mb-3">
            <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="<?= set_value('email'); ?>" required>
        </div>
        <div class="mb-3 position-relative">
            <input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
            <i class="fas fa-eye" id="togglePassword" style="position:absolute; right:16px; top:14px; cursor:pointer; color:#aaa"></i>
        </div>

        <div class="g-recaptcha" data-sitekey="6LctvBomAAAAAGjg0x7rNMuW9c5BOZfP-ev4E6b5"></div>
        <div class="mb-3"></div>

        <button type="submit" class="btn btn-primary w-100">Masuk</button>
        <a href="lupa-password" class="btn btn-sm btn-danger w-100 mt-2">Lupa Password</a>
    </form>

    <p class="mt-3 mb-0 text-center">
        <a href="/register" class="text-center small">Register a new membership</a>
    </p>
</div>

<footer>
    Dikembangkan oleh Pemerintah Desa Pasirlangu, Kecamatan Pakenjeng, Kabupaten Garut.
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