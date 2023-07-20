<!DOCTYPE html>
<html lang="id" xlmns:og="http://ogp.me/ns#">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Rian Sutarsa">
    <meta name="keywords" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Kecamatan Pakenjeng">
    <meta name="description" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Kecamatan Pakenjeng">

    <meta property="og:title" content="<?= nameApp(); ?> | <?= $title; ?>" />
    <meta property="og:description" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Kecamatan Pakenjeng" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= base_url(); ?>" />
    <meta property="og:image" content="<?= base_url('icon-dtks.png'); ?>" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@sutarsarian" />
    <meta name="twitter:creator" content="@sutarsarian" />
    <meta name="twitter:title" content="<?= nameApp(); ?> | <?= $title; ?>" />
    <meta name="twitter:description" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Kecamatan Pakenjeng" />
    <meta name="twitter:image" content="<?= base_url('icon-dtks.png'); ?>" />

    <title><?= $title; ?> | <?= nameApp(); ?></title>


    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets'); ?>/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets'); ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets'); ?>/dist/css/adminlte.min.css">
    <!-- Bootstrap -->
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="<?= base_url('assets/dist/css/authstyle.css'); ?>">

    <link rel="shortcut icon" type="image/x-icon/png" href="<?= base_url('icon-dtks.png'); ?>">

    <!-- jQuery -->
    <script src="<?= base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('assets/dist/js/adminlte.min.js'); ?>"></script>
    <!-- Bootstrap -->
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<body class="hold-transition login-page">

    <?= $this->renderSection('content'); ?>

</body>

</html>