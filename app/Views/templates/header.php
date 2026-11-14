<!DOCTYPE html>
<html lang="id" prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb#">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0">

    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-value" content="<?= csrf_hash() ?>">

    <meta name="author" content="Rian Sutarsa">
    <meta name="keywords" content="<?= esc(titleApp()); ?>, SINDEN, DTKS, Bansos">
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
    <meta name="theme-color" content="#2EC4B6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?= nameApp(); ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/logo/apple-touch-icon.png'); ?>">

    <title><?= $title; ?> | <?= nameApp(); ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/plugins/fontawesome-free/css/all.min.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/adminlte.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/style.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css'); ?>">
    <!-- Ionicons -->
    <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/select2/css/select2.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css'); ?>">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/sweetalert2/sweetalert2.min.css'); ?>">

    <!-- Lightbox -->
    <link rel="stylesheet" href="<?= base_url('assets/lightbox/dist/css/lightbox.min.css'); ?>">

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/logo/icon-192.png'); ?>" />
    <link rel="icon" type="image/png" href="<?= base_url('assets/logo/icon-192.png'); ?>" />

    <style>
        /* 🚀 STYLING UI PREMIUM SINDEN */
        :root {
            --green: #2EC4B6;
        }

        .navbar-sinden {
            background: #2EC4B6 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .sidebar-sinden {
            background: linear-gradient(180deg, #135d54 0%, #2EC4B6 100%) !important;
        }

        body.dark-mode .sidebar-sinden {
            background: linear-gradient(180deg, #0b3d2e 0%, #154360 100%) !important;
        }

        /* 🚀 STYLING UI PREMIUM SINDEN */
        .content-wrapper {
            min-height: 100vh !important;
            background-color: #f4f6f9;
            padding-top: 1.5rem !important;
        }

        /* Tombol Melayang (Floating Action Button) */
        .btn-floating {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1050;
            transition: all 0.3s ease;
        }

        .btn-floating:hover {
            transform: scale(1.1);
        }

        /* Styling Radio Button Group (Style Mbah) */
        .btn-group-toggle .btn {
            border: 1px solid #ced4da;
            background-color: #fff;
            color: #495057;
            font-size: 0.85rem;
            transition: all 0.2s ease-in-out;
        }

        .btn-group-toggle .btn:hover {
            background-color: #f8f9fa;
        }

        .btn-group-toggle .btn.active {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: #fff !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-group-bansos .btn.active {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        /* Table & Filter Styling */
        .card-bansos {
            border-radius: 12px;
            border: none;
        }

        .filter-box {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .filter-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: block;
        }

        /* 📌 GLOBAL STICKY HEADER UNTUK SELURUH TABEL DI PROYEK SINDEN */
        table.table:not(.fixedHeader-floating) > thead,
        table.dataTable:not(.fixedHeader-floating) > thead,
        table.table:not(.fixedHeader-floating) > thead > tr:only-child > th,
        table.table:not(.fixedHeader-floating) > thead > tr:only-child > td,
        table.dataTable:not(.fixedHeader-floating) > thead > tr:only-child > th,
        table.dataTable:not(.fixedHeader-floating) > thead > tr:only-child > td {
            position: sticky;
            top: 0;
            z-index: 1015;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        /* Warna latar belakang default solid (tidak transparan) untuk semua <thead> */
        table.table > thead,
        table.dataTable > thead,
        table.table > thead > tr > th,
        table.table > thead > tr > td,
        table.dataTable > thead > tr > th,
        table.dataTable > thead > tr > td {
            background-color: #f8f9fa;
        }

        body.dark-mode table.table > thead,
        body.dark-mode table.dataTable > thead,
        body.dark-mode table.table > thead > tr > th,
        body.dark-mode table.table > thead > tr > td,
        body.dark-mode table.dataTable > thead > tr > th,
        body.dark-mode table.dataTable > thead > tr > td {
            background-color: #1e293b;
            color: #f1f5f9;
        }

        /* Pertahankan warna solid sesuai class Bootstrap / AdminLTE & ID Tabel */
        thead.bg-primary, thead tr.bg-primary, thead.bg-primary th, thead tr.bg-primary th,
        #tableKeluarga thead th {
            background-color: #0d6efd !important;
            color: #ffffff !important;
        }

        thead.bg-warning, thead tr.bg-warning, thead.bg-warning th, thead tr.bg-warning th,
        #tableDraftKeluarga thead th {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        thead.bg-info, thead tr.bg-info, thead.bg-info th, thead tr.bg-info th,
        #tableSubmitted thead th {
            background-color: #0dcaf0 !important;
            color: #212529 !important;
        }

        thead.bg-danger, thead tr.bg-danger, thead.bg-danger th, thead tr.bg-danger th,
        #tablePemulihan thead th {
            background-color: #dc3545 !important;
            color: #ffffff !important;
        }

        thead.bg-success, thead tr.bg-success, thead.bg-success th, thead tr.bg-success th {
            background-color: #198754 !important;
            color: #ffffff !important;
        }

        thead.bg-secondary, thead tr.bg-secondary, thead.bg-secondary th, thead tr.bg-secondary th {
            background-color: #6c757d !important;
            color: #ffffff !important;
        }

        thead.bg-dark, thead tr.bg-dark, thead.bg-dark th, thead tr.bg-dark th,
        thead.table-dark, thead tr.table-dark, thead.table-dark th, thead tr.table-dark th,
        #tableArsipTerpadu thead th {
            background-color: #212529 !important;
            color: #ffffff !important;
        }

        thead.bg-light, thead tr.bg-light, thead.bg-light th, thead tr.bg-light th,
        thead.table-light, thead tr.table-light, thead.table-light th, thead tr.table-light th,
        #tableKeadaanRumah thead th {
            background-color: #f8f9fa !important;
            color: #212529 !important;
        }

        thead.table-primary, thead tr.table-primary, thead.table-primary th, thead tr.table-primary th {
            background-color: #cfe2ff !important;
            color: #084298 !important;
        }

        thead.table-success, thead tr.table-success, thead.table-success th, thead tr.table-success th {
            background-color: #d1e7dd !important;
            color: #0f5132 !important;
        }

        thead.table-info, thead tr.table-info, thead.table-info th, thead tr.table-info th {
            background-color: #cff4fc !important;
            color: #055160 !important;
        }

        thead.table-warning, thead tr.table-warning, thead.table-warning th, thead tr.table-warning th {
            background-color: #fff3cd !important;
            color: #664d03 !important;
        }

        thead.table-danger, thead tr.table-danger, thead.table-danger th, thead tr.table-danger th {
            background-color: #f8d7da !important;
            color: #842029 !important;
        }

        thead.table-secondary, thead tr.table-secondary, thead.table-secondary th, thead tr.table-secondary th {
            background-color: #e2e3e5 !important;
            color: #41464b !important;
        }

        div.dtfh-floatingparent {
            top: 0 !important;
            z-index: 1018 !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }
    </style>



    <!-- ===================== -->
    <!-- JAVASCRIPT — URUTAN BENAR -->
    <!-- ===================== -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        if (typeof jQuery !== 'undefined') jQuery.migrateMute = true;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js"></script>

    <!-- AdminLTE (Bootstrap 4 bundle!) -->
    <script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <!-- Datatables -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
    <script>
        (function() {
            if (typeof jQuery === 'undefined') return;
            var $ = jQuery;

            // 1. Default global FixedHeader (menempel rapat di batas atas layar: headerOffset = 0) untuk seluruh DataTables
            if (typeof $.fn.dataTable !== 'undefined') {
                $.extend(true, $.fn.dataTable.defaults, {
                    fixedHeader: {
                        header: true,
                        headerOffset: 0
                    }
                });

                // Matikan window-level FixedHeader jika tabel berada di dalam modal/offcanvas atau memakai scrollY
                $(document).on('preInit.dt', function(e, settings) {
                    var $table = $(settings.nTable);
                    if ($table.closest('.modal, .offcanvas').length > 0 || (settings.oInit && settings.oInit.scrollY)) {
                        if (settings.oInit) settings.oInit.fixedHeader = false;
                    }
                });

                // Rekalkulasi FixedHeader saat tab/collapse Bootstrap dibuka atau ukuran layar berubah
                $(document).on('shown.bs.tab shown.bs.collapse', function() {
                    $.fn.dataTable.tables({ api: true }).each(function() {
                        if (this.fixedHeader && typeof this.fixedHeader.adjust === 'function') {
                            this.fixedHeader.adjust();
                        }
                    });
                });
            }

            // 2. Dukungan sticky <thead> untuk tabel HTML biasa & wrapper .table-responsive
            function updateStickyTables() {
                $('.table-responsive').each(function() {
                    var wrapper = this;
                    if ($(wrapper).closest('.modal, .offcanvas').length > 0) return;

                    var style = window.getComputedStyle(wrapper);
                    var hasMaxHeight = style.maxHeight && style.maxHeight !== 'none' && style.maxHeight !== '0px';
                    if (hasMaxHeight) return;

                    var needsHorizontalScroll = wrapper.scrollWidth > wrapper.clientWidth + 2;
                    if (!needsHorizontalScroll) {
                        wrapper.style.overflow = 'visible';
                        $(wrapper).find('> table > thead').css('transform', '');
                    } else {
                        wrapper.style.overflow = '';
                        $(wrapper).find('> table').each(function() {
                            // Lewati jika sudah ditangani oleh DataTables FixedHeader
                            if (typeof $.fn.DataTable !== 'undefined' && $.fn.DataTable.isDataTable(this)) {
                                try {
                                    var api = $(this).DataTable();
                                    if (api.fixedHeader && api.fixedHeader.enabled && api.fixedHeader.enabled()) {
                                        $(this).children('thead').css('transform', '');
                                        return;
                                    }
                                } catch (err) {}
                            }
                            var thead = $(this).children('thead')[0];
                            if (!thead) return;
                            var rect = this.getBoundingClientRect();
                            var theadHeight = thead.offsetHeight || 40;
                            if (rect.top < 0 && rect.bottom > theadHeight) {
                                var offset = Math.min(-rect.top, rect.height - theadHeight);
                                thead.style.transform = 'translate3d(0, ' + offset + 'px, 0)';
                            } else {
                                thead.style.transform = '';
                            }
                        });
                    }
                });
            }

            var rafSticky = null;
            function requestStickyUpdate() {
                if (rafSticky) return;
                rafSticky = window.requestAnimationFrame(function() {
                    rafSticky = null;
                    updateStickyTables();
                });
            }

            $(window).on('scroll resize load', requestStickyUpdate);
            $(document).ready(function() {
                updateStickyTables();
                setTimeout(updateStickyTables, 400);
            });
        })();
    </script>

    <!-- Datatables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Select2 -->
    <script src="<?= base_url('assets/plugins/select2/js/select2.full.min.js'); ?>"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- Moment -->
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>

</head>

<body class="hold-transition sidebar-mini layout-navbar-fixed">
    <div class="wrapper">