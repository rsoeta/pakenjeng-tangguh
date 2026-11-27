<?= $this->extend('dtks/auth/templates/index'); ?>
<?= $this->section('content'); ?>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<button id="toggleTheme" class="btn btn-sm" title="Ganti Mode">
    <i class="fas fa-moon"></i> Mode Gelap
</button>

<div class="login-box auth-forgot" id="elemen" data-alert-fade="3000">
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

        <div class="form-floating mb-2 mb-compact">
            <input type="text" class="form-control" id="fullname" name="fullname" placeholder=" " value="<?= set_value('fullname'); ?>" required>
            <label for="fullname">Masukan Nama Lengkap</label>
        </div>

        <div class="form-floating mb-2 mb-compact">
            <input type="text" class="form-control" id="nik" name="nik" placeholder=" " value="<?= set_value('nik'); ?>" required>
            <label for="nik">Masukan No. KTP/NIK</label>
        </div>

        <div class="form-floating mb-2 mb-compact">
            <input type="text" class="form-control" id="nope" name="nope" placeholder=" " value="<?= set_value('nope'); ?>" required>
            <label for="nope">Masukan No. Handphone</label>
        </div>

        <div class="form-floating mb-2 mb-compact">
            <input type="email" class="form-control" id="email" name="email" placeholder=" " value="<?= set_value('email'); ?>" required>
            <label for="email">Masukan Email</label>
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

<?php $flashMessage = session()->getFlashdata('message'); ?>
<?php if (is_array($flashMessage) && !empty($flashMessage['text'])) : ?>
    <div id="authFlashSwal" hidden
        data-icon="<?= esc($flashMessage['type'] ?? 'info', 'attr'); ?>"
        data-title="<?= esc(($flashMessage['type'] ?? '') === 'success' ? 'Berhasil' : 'Gagal', 'attr'); ?>"
        data-text="<?= esc($flashMessage['text'], 'attr'); ?>"
        data-redirect="<?= ($flashMessage['type'] ?? '') === 'success' ? 'https://mail.google.com/' : ''; ?>">
    </div>
<?php endif; ?>

<?= $this->endSection(); ?>
