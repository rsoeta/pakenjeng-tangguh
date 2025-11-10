<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

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

    /* Dark mode */
    body.dark-mode {
        background: #121212 !important;
        color: #e0e0e0 !important;
    }

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

    #toggleTheme {
        position: absolute;
        top: 15px;
        right: 20px;
        background: transparent;
        border: none;
        color: var(--muted);
        cursor: pointer;
    }

    #toggleTheme:hover {
        color: var(--gold);
    }
</style>

<!-- <div class="text-right mt-5 mb-2"> -->
<button id="toggleTheme" title="Ganti Mode">
    <i class="fas fa-moon"></i>
</button>
<!-- </div> -->

<div class="login-box">
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
    <?php if (session()->get('message')): ?>
        <div class="alert alert-warning text-center" role="alert">
            <?= session()->get('message'); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($validation)): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?= $validation->listErrors(); ?>
        </div>
    <?php endif; ?>

    <form action="/register" method="post">
        <?= csrf_field(); ?>

        <div class="mb-3">
            <input type="text" class="form-control" name="fullname" placeholder="Nama Lengkap" value="<?= set_value('fullname'); ?>" required>
        </div>

        <div class="mb-3">
            <input type="number" class="form-control" name="nik" placeholder="Nomor KTP/NIK" value="<?= set_value('nik'); ?>" required>
        </div>

        <div class="mb-3">
            <input type="number" class="form-control" name="nope" placeholder="Nomor Handphone" value="<?= set_value('nope'); ?>" required>
        </div>

        <div class="mb-3">
            <input type="email" class="form-control" name="email" placeholder="Alamat Email" value="<?= set_value('email'); ?>" required>
        </div>

        <div class="mb-3">
            <select class="form-control" name="kelurahan" required>
                <option value="">-- Pilih Desa / Kelurahan --</option>
                <?php foreach ($desa as $row): ?>
                    <option value="<?= $row['id']; ?>" <?= set_select('kelurahan', $row['id']); ?>><?= $row['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <select class="form-control" name="no_rw" required>
                <option value="">-- Pilih RW --</option>
                <?php foreach ($datarw as $row): ?>
                    <option value="<?= $row['no_rw']; ?>" <?= set_select('no_rw', $row['no_rw']); ?>><?= $row['no_rw']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3 position-relative">
            <input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
            <i class="fas fa-eye" id="togglePassword" style="position:absolute; right:16px; top:14px; cursor:pointer; color:#aaa"></i>
        </div>

        <div class="mb-3 position-relative">
            <input type="password" class="form-control" placeholder="Ulangi Password" id="password_confirm" name="password_confirm" required>
            <i class="fas fa-eye" id="togglePassword2" style="position:absolute; right:16px; top:14px; cursor:pointer; color:#aaa"></i>
        </div>

        <button type="submit" class="btn btn-primary w-100">Daftar</button>
    </form>

    <p class="mt-3 mb-0 text-center">
        <a href="/login" class="text-center small">Sudah punya akun?</a>
    </p>
</div>

<footer>
    Dikembangkan oleh Pemerintah Desa Pasirlangu, Kecamatan Pakenjeng, Kabupaten Garut.<br>
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
            toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
        }

        toggleBtn.addEventListener("click", function() {
            body.classList.toggle("dark-mode");
            if (body.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                localStorage.setItem("theme", "light");
                toggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
            }
        });
    });
</script>

<?= $this->endSection(); ?>