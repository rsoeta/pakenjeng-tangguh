<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Autentikasi | <?= nameApp(); ?></title>

  <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets'); ?>/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url('assets'); ?>/dist/css/adminlte.min.css">

  <style>
    body {
      background: #f4f6f9 !important;
      font-family: 'Ubuntu', sans-serif !important;
    }

    /* Memastikan wrapper tidak mentok ke kiri-kanan pada mobile */
    .lockscreen-wrapper {
      width: 90%;
      max-width: 400px;
      margin: 0 auto;
      padding-top: 10%;
    }

    .glass-card {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      /* padding responsive */
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
      border-top: 5px solid #007bff;
    }

    .lock-icon {
      font-size: 3rem;
      color: #007bff;
      margin-bottom: 1rem;
    }

    /* Penyesuaian padding untuk desktop agar tetap proporsional */
    @media (min-width: 576px) {
      .glass-card {
        padding: 2.5rem;
      }
    }
  </style>
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
        <a href="javascript:window.history.go(-1);" class="btn btn-link btn-sm text-secondary mt-2">
          <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
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
</body>

</html>