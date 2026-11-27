<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>
<!-- captha -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<button id="toggleTheme" class="btn btn-sm">
    <i class="fas fa-moon"></i> Mode Gelap
</button>

<div class="login-box auth-login" data-alert-fade="5000">
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
    <?php $flashMessage = session()->getFlashdata('message'); ?>
    <?php if (is_array($flashMessage) && !empty($flashMessage['text'])): ?>
        <?php
        $alertClass = match ($flashMessage['type'] ?? 'warning') {
            'success' => 'alert-success',
            'error'   => 'alert-danger',
            default   => 'alert-warning',
        };
        ?>
        <div class="alert <?= $alertClass; ?> text-center py-2 small" role="alert">
            <?= esc($flashMessage['text']); ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="post">
        <?= csrf_field() ?>
        <div class="form-floating mb-2">
            <input type="email" class="form-control" placeholder=" " id="email" name="email" value="<?= set_value('email'); ?>" required>
            <label for="email">Email</label>
        </div>
        <div class="form-floating mb-2 position-relative password-field">
            <input type="password" class="form-control" placeholder=" " id="password" name="password" required>
            <label for="password">Password</label>
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
        </div>

        <!-- <div class="g-recaptcha" data-sitekey="6LctvBomAAAAAGjg0x7rNMuW9c5BOZfP-ev4E6b5"></div> -->
        <div class="mb-2">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAADhc27Fwr9L8O_Sb" data-theme="light" data-size="flexible"></div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Masuk</button>
        <a href="lupa-password" class="btn btn-sm btn-danger w-100 mt-2">Lupa Password</a>
    </form>

    <div class="auth-divider"><span>atau</span></div>

    <a href="<?= base_url('auth/google'); ?>" class="btn btn-google w-100" id="btnGoogleLogin">
        <svg class="google-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path fill="#EA4335" d="M12 10.2v3.9h5.5c-.24 1.25-.96 2.31-2.04 3.02l3.3 2.56c1.92-1.77 3.04-4.38 3.04-7.48 0-.72-.06-1.41-.19-2.08H12z"/>
            <path fill="#34A853" d="M12 22c2.7 0 4.96-.9 6.62-2.42l-3.3-2.56c-.91.61-2.08.98-3.32.98-2.55 0-4.71-1.72-5.48-4.04l-3.41 2.63C4.76 19.86 8.12 22 12 22z"/>
            <path fill="#4A90E2" d="M6.52 13.96A5.98 5.98 0 0 1 6.2 12c0-.68.12-1.34.32-1.96L3.11 7.41A10 10 0 0 0 2 12c0 1.61.39 3.13 1.11 4.59l3.41-2.63z"/>
            <path fill="#FBBC05" d="M12 6c1.47 0 2.79.51 3.83 1.5l2.87-2.87C16.95 3 14.7 2 12 2 8.12 2 4.76 4.14 3.11 7.41l3.41 2.63C7.29 7.72 9.45 6 12 6z"/>
        </svg>
        <span>Masuk dengan Google</span>
    </a>
    <div class="google-whitelist-note">Khusus email terdaftar pada akun SINDEN</div>

    <p class="mt-2 mb-0 text-center">
        <a href="/register" class="text-center small">Register a new membership</a>
    </p>
</div>

<footer>
    Powered by Pemerintah Desa Pasirlangu, Kec. Pakenjeng, Kab. Garut.
    <br>
    Mendukung implementasi Data Tunggal Sosial dan Ekonomi Nasional (DTSEN).
</footer>

<?= $this->endSection(); ?>
