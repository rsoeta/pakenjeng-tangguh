<!DOCTYPE html>
<html lang="id" xlmns:og="http://ogp.me/ns#">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <!-- <meta http-equiv="Content-Security-Policy" content="connect-src 'ws://localhost:8080';"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Rian Sutarsa">
    <meta name="keywords" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Kecamatan Pakenjeng">
    <meta name="description" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Kecamatan Pakenjeng">

    <meta property="og:title" content="<?= nameApp() . ' Kec. ' . ucwords(strtolower(Profil_Admin()['namaKec'])); ?> | <?= $title; ?>" />
    <meta property="og:description" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Kecamatan Pakenjeng" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= base_url(); ?>" />
    <meta property="og:image" content="<?= base_url('icon-dtks.png'); ?>" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@sutarsarian" />
    <meta name="twitter:creator" content="@sutarsarian" />
    <meta name="twitter:title" content="<?= nameApp() . ' Kec. ' . ucwords(strtolower(Profil_Admin()['namaKec'])); ?> | <?= $title; ?>" />
    <meta name="twitter:description" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Kecamatan Pakenjeng" />
    <meta name="twitter:image" content="<?= base_url('icon-dtks.png'); ?>" />

    <title><?= $title; ?> | <?= nameApp() . ' Kec. ' . ucwords(strtolower(Profil_Admin()['namaKec'])); ?></title>



    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/fontawesome-free/css/all.min.css'); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/adminlte.min.css'); ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/style.css'); ?>">
    <!-- <link rel="stylesheet" href="<?= base_url('assets/dist/css/chat.css'); ?>"> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.bootstrap5.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.9/css/fixedHeader.bootstrap.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="<?= base_url('landing-page/css/bootstrap.min.css'); ?>"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.0/css/fixedHeader.dataTables.min.css">

    <!-- select2 -->
    <link href="<?= base_url('assets/plugins/select2/css/select2.min.css'); ?>" rel="stylesheet" />

    <!-- fontawesome -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />
    <!-- sweetalert -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/sweetalert2/sweetalert2.min.css'); ?>">
    <!-- lightbox -->
    <link rel="stylesheet" href="<?= base_url('assets/lightbox/dist/css/lightbox.min.css'); ?>">

    <link rel="shortcut icon" type="image/x-icon/png" href="<?= base_url('icon-dtks.png'); ?>" />
    <!-- DataTables -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.0/js/dataTables.fixedHeader.min.js"></script>

    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="<?= base_url('assets/plugins/sweetalert2/sweetalert2.all.min.js'); ?>"></script>
    <script src="<?= base_url('assets/plugins/select2/js/select2.min.js'); ?>"></script>

</head>

<!-- <body class="hold-transition sidebar-mini"> -->

<body class="hold-transition sidebar-mini layout-navbar-fixed">
    <div class="wrapper">