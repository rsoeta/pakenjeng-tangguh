<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<!-- <div class="text-right mt-5 mb-2"> -->
<button id="toggleTheme" class="btn btn-sm" title="Ganti Mode">
    <i class="fas fa-moon"></i> Mode Gelap
</button>
<!-- </div> -->

<div class="login-box auth-register" data-alert-fade="3000">
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

        <div class="form-floating mb-2 mb-compact">
            <input type="text" class="form-control form-control-sm" id="fullname" name="fullname" placeholder=" " value="<?= set_value('fullname'); ?>" required>
            <label for="fullname">Nama Lengkap</label>
        </div>

        <div class="form-floating mb-2 mb-compact">
            <input type="number" class="form-control form-control-sm" id="nik" name="nik" placeholder=" " value="<?= set_value('nik'); ?>" required>
            <label for="nik">Nomor KTP/NIK</label>
        </div>

        <div class="form-floating mb-2 mb-compact">
            <input type="number" class="form-control form-control-sm" id="nope" name="nope" placeholder=" " value="<?= set_value('nope'); ?>" required>
            <label for="nope">Nomor Handphone</label>
        </div>

        <div class="form-floating mb-2 mb-compact">
            <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder=" " value="<?= set_value('email'); ?>" required>
            <label for="email">Alamat Email</label>
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

        <div class="form-floating mb-2 mb-compact position-relative password-field">
            <input type="password" class="form-control form-control-sm" placeholder=" " id="password" name="password" required>
            <label for="password">Password</label>
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
        </div>

        <div class="form-floating mb-2 mb-compact position-relative password-field">
            <input type="password" class="form-control form-control-sm" placeholder=" " id="password_confirm" name="password_confirm" required>
            <label for="password_confirm">Ulangi Password</label>
            <i class="fas fa-eye password-toggle" id="togglePassword2"></i>
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

<?= $this->endSection(); ?>
