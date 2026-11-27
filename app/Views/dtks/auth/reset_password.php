<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

<button id="toggleTheme" class="btn btn-sm" title="Ganti Mode">
    <i class="fas fa-moon"></i> Mode Gelap
</button>

<div class="reset-box auth-reset" data-alert-fade="3000">
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
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
        </div>

        <div class="mb-2 position-relative">
            <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Ulangi password" required>
            <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
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

<?= $this->endSection(); ?>
