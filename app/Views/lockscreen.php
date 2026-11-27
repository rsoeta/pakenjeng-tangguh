<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Autentikasi | <?= nameApp(); ?></title>

  <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets'); ?>/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('assets'); ?>/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/css/lockscreen.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/lockscreen.css') ?: '1'; ?>">
</head>

<body class="hold-transition lockscreen">
  <div class="lockscreen-wrapper">
    <div class="lockscreen-logo">
      <a href="<?= base_url(); ?>"><b><?= nameApp(); ?></b></a>
    </div>

    <div class="glass-card text-center">
      <div class="lock-icon">
        <i class="fas fa-user-lock"></i>
      </div>

      <h5 class="font-weight-bold">Sesi Keamanan</h5>
      <p class="text-muted small">Sesi Anda telah berakhir demi keamanan data. Silakan masuk kembali untuk melanjutkan pekerjaan.</p>

      <div class="mt-4">
        <a href="<?= base_url('logout'); ?>" class="btn btn-primary btn-block shadow-sm">
          <i class="fas fa-sign-in-alt mr-2"></i> Login Kembali
        </a>
        <button type="button" id="btnLockBack" class="btn btn-link btn-sm text-secondary mt-2">
          <i class="fas fa-arrow-left mr-1"></i> Kembali
        </button>
      </div>
    </div>

    <div class="lockscreen-footer text-center mt-5">
      <small class="text-muted">
        Copyright &copy; 2021 - <?= date('Y') ?> | <b><?= nameApp(); ?></b><br>
        <?= titleApp(); ?>
      </small>
    </div>
  </div>

  <script src="<?= base_url('assets'); ?>/plugins/jquery/jquery.min.js"></script>
  <script src="<?= base_url('assets'); ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url('assets/js/landing-public.js'); ?>?v=<?= @filemtime(FCPATH . 'assets/js/landing-public.js') ?: '1'; ?>" defer></script>
</body>

</html>