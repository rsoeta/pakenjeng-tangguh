<?= $this->extend('dtks/auth/templates/index'); ?>
<?= $this->section('content'); ?>

<!-- render fonts poppins -->
<link href="<?= base_url('assets/font/Poppins/poppins.css'); ?>" rel="stylesheet">

<style>
    :root {
        --green: #2EC4B6;
        --gold: #FFBE0B;
        --muted: #6b7280;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(180deg, #f1fbfa 0%, #ffffff 50%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.4s, color 0.4s;
    }

    .card {
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        border: none;
    }

    .card-body {
        padding: 32px;
    }

    .text-center img {
        width: 90px;
        height: 90px;
    }

    .btn-danger {
        background: #FF6B6B;
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
    }

    .btn-danger:hover {
        background: #FF5252;
    }

    .text-center a {
        color: var(--green);
        font-weight: 600;
    }

    /* 🌙 Dark Mode */
    body.dark-mode {
        background: #121212 !important;
        color: #e0e0e0 !important;
    }

    body.dark-mode .card {
        background: #1e1e1e !important;
        color: #ddd !important;
    }

    body.dark-mode input {
        background: #2b2b2b !important;
        color: #eee !important;
        border-color: #444 !important;
    }

    body.dark-mode .btn-danger {
        background: var(--gold) !important;
        color: #222 !important;
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

<button id="toggleTheme" title="Ganti Mode">
    <i class="fas fa-moon"></i>
</button>

<div class="col-xl-4 col-lg-8 col-md-7">
    <div class="card" id="elemen">
        <div class="card-body text-center">
            <a href="<?= base_url(); ?>">
                <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="SINDEN Logo">
            </a>
            <h2 class="h5 text-gray-900 mt-3"><?= $title; ?></h2>

            <p class="small text-muted mb-4">Masukkan data akun Anda untuk mereset password.</p>

            <?php if (session()->get('success')) : ?>
                <div class="alert alert-success"><?= session()->get('success'); ?></div>
            <?php endif; ?>

            <?php if (isset($validation)) : ?>
                <div class="alert alert-warning"><?= $validation->listErrors(); ?></div>
            <?php endif; ?>

            <form action="/requestReset" method="POST">
                <?= csrf_field(); ?>

                <div class="form-group text-left">
                    <label for="fullname">Nama Lengkap</label>
                    <input type="text" class="form-control" name="fullname" placeholder="Masukan Nama Lengkap" value="<?= set_value('fullname'); ?>">
                </div>

                <div class="form-group text-left">
                    <label for="nik">NIK</label>
                    <input type="text" class="form-control" name="nik" placeholder="Masukan No. KTP/NIK" value="<?= set_value('nik'); ?>">
                </div>

                <div class="form-group text-left">
                    <label for="nope">No. HP</label>
                    <input type="text" class="form-control" name="nope" placeholder="Masukan No. Handphone" value="<?= set_value('nope'); ?>">
                </div>

                <div class="form-group text-left">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Masukan Email" value="<?= set_value('email'); ?>">
                </div>

                <button type="submit" class="btn btn-danger btn-block">Reset Password</button>

                <hr>
                <a class="small" href="<?= base_url('login'); ?>">Sudah punya akun? Masuk</a>
            </form>
        </div>
    </div>
</div>

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