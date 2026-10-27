<!DOCTYPE html>
<html lang="id" prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb#">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="author" content="Rian Sutarsa">
    <meta name="keywords" content="<?= esc(titleApp()); ?>, SINDEN, DTKS">
    <meta name="description" content="<?= esc(titleApp()); ?> — Sistem Informasi Data Ekonomi dan Sosial Desa Pasirlangu">

    <!-- Open Graph (Facebook, WhatsApp, dsb.) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url(); ?>">
    <meta property="og:title" content="<?= esc($title); ?> | <?= nameApp(); ?>">
    <meta property="og:description" content="<?= esc(titleApp()); ?>">
    <meta property="og:image" content="<?= base_url('assets/logo/sinden-og.png'); ?>">
    <meta property="og:image:secure_url" content="<?= base_url('assets/logo/sinden-og.png'); ?>">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Logo <?= nameApp(); ?>">
    <meta property="og:site_name" content="<?= nameApp(); ?>">
    <meta property="og:locale" content="id_ID">

    <!-- WhatsApp Preview Image Fallback -->
    <link rel="image_src" href="<?= base_url('assets/logo/sinden-og.png'); ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@sutarsarian">
    <meta name="twitter:creator" content="@sutarsarian">
    <meta name="twitter:title" content="<?= esc($title); ?> | <?= nameApp(); ?>">
    <meta name="twitter:description" content="<?= esc(titleApp()); ?>">
    <meta name="twitter:image" content="<?= base_url('assets/logo/sinden-og.png'); ?>">

    <!-- PWA & Mobile Web App Meta Tags -->
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
    <meta name="theme-color" content="#2ecc71">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?= nameApp(); ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/logo/apple-touch-icon.png'); ?>">

    <title><?= $title; ?> | <?= nameApp(); ?></title>


    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/fontawesome-free/css/all.min.css'); ?>">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css'); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/adminlte.min.css'); ?>">
    <!-- Bootstrap -->
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="<?= base_url('assets/dist/css/authstyle.css'); ?>">

    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/logo/icon-192.png'); ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/logo/icon-192.png'); ?>">

    <!-- sweetalert -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/sweetalert2/sweetalert2.min.css'); ?>">

    <!-- jQuery -->
    <script src="<?= base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets/dist/js/adminlte.min.js'); ?>"></script>
    <!-- Bootstrap -->
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="<?= base_url('assets/plugins/sweetalert2/sweetalert2.all.min.js'); ?>"></script>

</head>

<body class="hold-transition login-page dark-mode">

    <?= $this->renderSection('content'); ?>


    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?= base_url('sw.js'); ?>')
                    .catch(function(err) {
                        console.warn('[PWA] SW registration warning:', err);
                    });
            });
        }
    </script>
</body>

</html>