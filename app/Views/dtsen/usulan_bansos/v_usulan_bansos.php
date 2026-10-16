<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .fab-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        font-size: 24px;
        cursor: pointer;
        z-index: 999;
        transition: all 0.25s ease;
    }

    .fab-btn:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    #btnReloadDraft,
    #btnReloadVerified {
        padding: 4px 10px;
    }

    /* Countdown Digit Styles */
    /* Divider tipis */
    .countdown-divider {
        width: 1px;
        height: 36px;
        background: rgba(255, 255, 255, 0.35);
        margin: 0 14px;
    }

    .countdown-header {
        margin-left: auto;
        display: flex;
        flex-direction: column;
        /* tengah secara vertical */
        justify-content: center;
        /* sedikit turun */
        transform: translateY(3px);
        text-align: right;
        align-items: flex-end;
        line-height: 1;
    }

    .countdown-label {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 1px;
    }

    /* Wrapper futuristik */
    .countdown-future {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Digit futuristik */
    .countdown-future .digit {
        font-size: clamp(18px, 4vw, 26px);
        font-family: "Orbitron", sans-serif;
        font-weight: 700;
        color: #ffffff;
        text-shadow:
            0 0 6px rgba(255, 255, 255, .4),
            0 0 12px rgba(0, 0, 0, .3);
        padding: 0 2px;
        transition: transform .15s ease-out;
    }

    .countdown-future .digit.animate {
        transform: scale(1.20);
    }

    /* Desktop: rapikan spacing supaya tidak menjauh dari kanan */
    @media (min-width: 992px) {
        .countdown-header {
            padding-right: 8px;
            /* lebih precise di desktop */
            transform: translateY(2px);
        }

        .countdown-divider {
            height: 32px;
        }

        .countdown-future {
            gap: 10px;
        }

        .countdown-label {
            font-size: 14px;
        }
    }

    /* Mobile tetap responsive */
    @media (max-width: 576px) {
        .countdown-header {
            transform: translateY(1px);
        }

        .countdown-future {
            gap: 4px;
        }

        .countdown-divider {
            display: none;
        }

        .countdown-label {
            font-size: 11px;
        }
    }

    @media (max-width: 500px) {
        .countdown-label {
            font-size: 11px;
        }

        .countdown-future {
            gap: 4px;
        }
    }

    /* 🚀 Mobile-First Offcanvas & Fluid Flexbox Styling */
    .offcanvas-akses {
        width: 100% !important;
        max-width: 100%;
    }

    @media (min-width: 768px) {
        .offcanvas-akses {
            width: 580px !important;
        }
    }

    .btn-touch-target {
        min-height: 44px;
        min-width: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        border-radius: 0.5rem;
        font-weight: 600;
    }

    .deadline-flex-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 0.85rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.6rem;
        margin-bottom: 0.75rem;
    }

    .deadline-flex-item {
        flex-grow: 1;
        flex-basis: 100%;
    }

    @media (min-width: 576px) {
        .deadline-flex-item {
            flex-basis: calc(33.333% - 0.75rem);
        }
    }

    /* =================================================================
       📱 MOBILE-FIRST STACKED CARD TABLE (SUPER COMPACT)
       ================================================================= */
    /* Pastikan di desktop text sejajar tengah (vertikal) */
    #tableUsulanBansosDraft td,
    #tableUsulanBansosVerified td {
        vertical-align: middle !important;
    }

    @media (max-width: 767.98px) {

        /* Sembunyikan header tabel */
        #tableUsulanBansosDraft thead,
        #tableUsulanBansosVerified thead {
            display: none !important;
        }

        /* Ubah baris (TR) menjadi Card padat */
        #tableUsulanBansosDraft tbody tr,
        #tableUsulanBansosVerified tbody tr {
            display: block;
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-left: 4px solid #0d6efd;
            border-radius: 0.5rem;
            margin-bottom: 0.6rem;
            padding: 0.6rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        #tableUsulanBansosVerified tbody tr {
            border-left-color: #198754;
        }

        /* Matikan display flex default pada sel agar bisa dikustomisasi */
        #tableUsulanBansosDraft tbody td,
        #tableUsulanBansosVerified tbody td {
            display: block;
            border: none !important;
            padding: 0 !important;
            width: 100% !important;
            text-align: left !important;
        }

        /* Sembunyikan label (data-label) palsu yang bikin tinggi */
        #tableUsulanBansosDraft tbody td::before,
        #tableUsulanBansosVerified tbody td::before {
            display: none !important;
        }

        /* 1. Baris Pertama (Atas): Nama & Badge Program */
        #tableUsulanBansosDraft tbody td:nth-child(3),
        #tableUsulanBansosVerified tbody td:nth-child(3) {
            margin-bottom: 0.4rem;
        }

        /* 2. Baris Kedua (Tengah): Kotak NIK & KK */
        #tableUsulanBansosDraft tbody td:nth-child(2),
        #tableUsulanBansosVerified tbody td:nth-child(2) {
            background: #f8f9fa;
            border: 1px solid #e9ecef !important;
            border-radius: 0.4rem;

            /* 🚀 PERBAIKAN: Sisi kanan dilebarkan 2.5rem agar jadi area aman Nomor Urut */
            padding: 0.35rem 2.5rem 0.35rem 0.5rem !important;
            margin-bottom: 0.5rem;
        }

        /* 3. Baris Ketiga (Bawah): Status & Info Tambahan */
        #tableUsulanBansosDraft tbody td:nth-child(4),
        #tableUsulanBansosVerified tbody td:nth-child(4),
        #tableUsulanBansosDraft tbody td:nth-child(5),
        #tableUsulanBansosVerified tbody td:nth-child(5),
        #tableUsulanBansosDraft tbody td:nth-child(6),
        #tableUsulanBansosVerified tbody td:nth-child(6),
        #tableUsulanBansosDraft tbody td:nth-child(7),
        #tableUsulanBansosVerified tbody td:nth-child(7) {
            display: inline-block;
            width: auto !important;
            font-size: 0.8rem;
            margin-right: 0.5rem;
            margin-bottom: 0.2rem;
        }

        /* 4. Baris Keempat (Paling Bawah): Tombol Aksi */
        #tableUsulanBansosDraft tbody td:last-child,
        #tableUsulanBansosVerified tbody td:last-child {
            display: block;
            border-top: 1px dashed #dee2e6 !important;
            padding-top: 0.5rem !important;
            margin-top: 0.3rem;
            text-align: right !important;
        }

        /* Posisikan Nomor Urut (Kolom 1) di pojok kanan atas mutlak */
        #tableUsulanBansosDraft tbody td:first-child,
        #tableUsulanBansosVerified tbody td:first-child {
            position: absolute;
            top: 0.6rem;
            right: 0.6rem;
            width: auto !important;
        }
    }
</style>

<div class="content-wrapper mt-1">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="mb-0"><i class="fas fa-hand-holding-heart"></i> <?= esc($title); ?></h1>
            <ol class="breadcrumb float-right mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/pages'); ?>">Home</a></li>
                <li class="breadcrumb-item active"><?= esc($title); ?></li>
            </ol>
        </div>
    </section>

    <section class="content">
        <div class="card mt-2">
            <div class="card-header bg-primary text-white d-flex align-items-center flex-wrap gap-2 py-2">

                <h5 class="mb-0 fw-bold"><i class="fas fa-list-alt me-1"></i> Usulan Bantuan Sosial</h5>

                <div class="countdown-divider"></div>

                <div class="countdown-header text-end">
                    <div class="countdown-label">Sisa Waktu</div>
                    <div id="countdownWrap" class="countdown-future">
                        <span class="digit" id="cdDays">0</span>d
                        <span class="digit" id="cdHours">00</span>h
                        <span class="digit" id="cdMinutes">00</span>m
                        <span class="digit" id="cdSeconds">00</span>s
                    </div>
                </div>

                <?php if (($user_login['role_id'] ?? session()->get('role_id') ?? 99) <= 3): ?>
                    <button type="button"
                        class="btn btn-light btn-sm text-primary shadow-sm ms-2 btn-touch-target px-3"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasGeneralAkses"
                        aria-controls="offcanvasGeneralAkses"
                        title="Pengaturan Jadwal & Batas Waktu Akses">
                        <i class="fas fa-cog fa-spin-hover"></i>
                        <span class="d-none d-sm-inline">Pengaturan Akses</span>
                    </button>
                <?php endif; ?>

            </div>

            <div class="card-body">
                <button id="btnTambahUsulan" class="fab-btn" data-toggle="tooltip" title="Tambah Usulan Bansos">
                    <i class="fas fa-hand-holding-heart"></i>
                </button>

                <div class="row mb-3">
                    <!-- CSRF Token untuk AJAX -->
                    <input type="hidden"
                        id="csrfToken"
                        name="<?= csrf_token() ?>"
                        value="<?= csrf_hash() ?>" />

                    <div class="col-md-2 col-6">
                        <select id="filterBulan" class="form-control">
                            <option value="">-- Bulan --</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-6">
                        <select id="filterTahun" class="form-control">
                            <option value="">-- Tahun --</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-6">
                        <select id="filterProgram" class="form-control">
                            <option value="">-- Semua Program --</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-6">
                        <select id="filterCreatedBy" class="form-control">
                            <option value="">-- Semua Pengusul --</option>
                        </select>
                    </div>

                    <div class="col-md-1 col-6">
                        <select id="filterRW" class="form-control">
                            <option value="">-- RW --</option>
                        </select>
                    </div>

                    <div class="col-md-1 col-6">
                        <select id="filterRT" class="form-control">
                            <option value="">-- RT --</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <!-- <button id="btnApplyFilter" class="btn btn-primary w-100">Filter</button> -->
                        <button id="btnResetFilter" class="btn btn-secondary w-100">Reset</button>
                    </div>

                </div>

                <!-- 🔹 Tab Navigation -->
                <ul class="nav nav-tabs" id="usulanTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tabDraft-tab" data-bs-toggle="tab" data-bs-target="#tabDraft" type="button" role="tab">
                            🟡 Draft
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tabVerified-tab" data-bs-toggle="tab" data-bs-target="#tabVerified" type="button" role="tab">
                            ✅ Diverifikasi
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-3">
                    <!-- 🟡 Draft -->
                    <div class="tab-pane fade show active" id="tabDraft" role="tabpanel">
                        <div class="d-flex justify-content-end mb-2">
                            <button id="btnReloadDraft" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-sync-alt"></i> Reload
                            </button>
                        </div>

                        <table id="tableUsulanBansosDraft" class="table table-striped table-bordered w-100"></table>
                    </div>

                    <!-- ✅ Diverifikasi -->
                    <div class="tab-pane fade" id="tabVerified" role="tabpanel">
                        <div class="d-flex justify-content-end mb-2">
                            <button id="btnReloadVerified" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-sync-alt"></i> Reload
                            </button>
                        </div>
                        <table id="tableUsulanBansosVerified" class="table table-striped table-bordered w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('dtsen/usulan_bansos/form_usulan_bansos'); ?>

<?php if (($user_login['role_id'] ?? session()->get('role_id') ?? 99) <= 3): ?>
    <!-- 🚀 OFFCANVAS PENGATURAN GENERAL & USULAN AKSES -->
    <div class="offcanvas offcanvas-end offcanvas-akses shadow-lg" tabindex="-1" id="offcanvasGeneralAkses" aria-labelledby="offcanvasGeneralAksesLabel">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title fw-bold mb-0" id="offcanvasGeneralAksesLabel">
                <i class="fas fa-user-clock me-2"></i> Pengaturan Batas Waktu Akses
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body bg-white">
            <div class="alert alert-info small py-2 mb-3 border-info">
                <i class="fas fa-info-circle me-1"></i> Atur rentang waktu (<i>Start Date</i> & <i>End Date</i>) pembukaan akses penginputan untuk masing-masing tingkatan pengguna.
            </div>

            <!-- 1️⃣ CARD USULAN AKSES -->
            <div class="card shadow-sm border-primary mb-4">
                <form id="updateForm" method="post" action="">
                    <div class="card-header bg-light border-bottom py-2">
                        <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-hand-holding-heart me-1"></i> 1. Usulan Akses (Bansos)</h6>
                    </div>
                    <div class="card-body p-3">
                        <?php if (!empty($deadline) && is_array($deadline)): ?>
                            <?php foreach ($deadline as $dl) : ?>
                                <div class="deadline-flex-row">
                                    <div class="deadline-flex-item">
                                        <label class="small fw-bold text-secondary mb-1 d-block"><i class="fa fa-user me-1"></i> Hak-Akses</label>
                                        <select class="form-control form-control-sm" name="dd_role[]">
                                            <?php foreach ($statusRole as $s) : ?>
                                                <option <?= $dl['dd_role'] == $s['id_role'] ? 'selected' : ''; ?> value="<?= esc($s['id_role']); ?>">
                                                    <?= esc($s['nm_role']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="deadline-flex-item">
                                        <input type="hidden" name="dd_id[]" value="<?= esc($dl['dd_id']); ?>">
                                        <label class="small fw-bold text-secondary mb-1 d-block"><i class="fa fa-calendar-alt me-1"></i> Start Date</label>
                                        <input type="datetime-local" name="dd_waktu_start[]" class="form-control form-control-sm" value="<?= esc($dl['dd_waktu_start']); ?>" required>
                                    </div>
                                    <div class="deadline-flex-item">
                                        <label class="small fw-bold text-secondary mb-1 d-block"><i class="fa fa-calendar-alt me-1"></i> End Date</label>
                                        <input type="datetime-local" name="dd_waktu_end[]" class="form-control form-control-sm" value="<?= esc($dl['dd_waktu_end']); ?>" required>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Data jadwal usulan belum tersedia.</p>
                        <?php endif; ?>

                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-success btn-touch-target px-4 shadow-sm w-100 w-sm-auto">
                                <i class="fa fa-check-double"></i> Simpan Usulan Akses
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 2️⃣ CARD GENERAL AKSES -->
            <div class="card shadow-sm border-warning mb-3">
                <form id="updateFormGen" method="post" action="">
                    <div class="card-header bg-light border-bottom py-2">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-sliders-h me-1 text-warning"></i> 2. General Akses (PPKS / Lainnya)</h6>
                    </div>
                    <div class="card-body p-3">
                        <?php if (!empty($deadline_general) && is_array($deadline_general)): ?>
                            <?php foreach ($deadline_general as $dlg) : ?>
                                <div class="deadline-flex-row">
                                    <div class="deadline-flex-item">
                                        <label class="small fw-bold text-secondary mb-1 d-block"><i class="fa fa-user me-1"></i> Hak-Akses</label>
                                        <select class="form-control form-control-sm" name="dd_role[]">
                                            <?php foreach ($statusRole as $s) : ?>
                                                <option <?= $dlg['dd_role'] == $s['id_role'] ? 'selected' : ''; ?> value="<?= esc($s['id_role']); ?>">
                                                    <?= esc($s['nm_role']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="deadline-flex-item">
                                        <input type="hidden" name="dd_id[]" value="<?= esc($dlg['dd_id']); ?>">
                                        <label class="small fw-bold text-secondary mb-1 d-block"><i class="fa fa-calendar-alt me-1"></i> Start Date</label>
                                        <input type="datetime-local" name="dd_waktu_start[]" class="form-control form-control-sm" value="<?= esc($dlg['dd_waktu_start']); ?>" required>
                                    </div>
                                    <div class="deadline-flex-item">
                                        <label class="small fw-bold text-secondary mb-1 d-block"><i class="fa fa-calendar-alt me-1"></i> End Date</label>
                                        <input type="datetime-local" name="dd_waktu_end[]" class="form-control form-control-sm" value="<?= esc($dlg['dd_waktu_end']); ?>" required>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Data jadwal general belum tersedia.</p>
                        <?php endif; ?>

                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-warning text-dark btn-touch-target px-4 shadow-sm w-100 w-sm-auto">
                                <i class="fa fa-check-circle"></i> Simpan General Akses
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    window.userRole = <?= session()->get('role_id') ?? 99 ?>;
    window.userNik = "<?= session()->get('nik') ?>";
    window.userName = "<?= session()->get('fullname') ?>";

    // =========================================================================
    // 🚀 KONFIGURASI MODULAR SWEETALERT2 & SUBMIT DEADLINE OFFCANVAS
    // =========================================================================
    const SwalAutoSuccess = Swal.mixin({
        icon: 'success',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        width: '22rem',
        padding: '1.25rem',
        customClass: {
            popup: 'rounded-4 shadow',
            title: 'fs-5 fw-bold',
            htmlContainer: 'fs-6'
        }
    });

    function showSuccessDeadline(message) {
        SwalAutoSuccess.fire({
            title: 'Berhasil!',
            text: message
        });
    }

    $(document).ready(function() {
        // 1. Submit Form Usulan Akses
        $('#updateForm').on('submit', function(e) {
            e.preventDefault();
            const $btn = $(this).find('button[type="submit"]');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: '<?= base_url('updateBatch'); ?>',
                type: 'POST',
                data: $(this).serialize(),
                success: function() {
                    $btn.prop('disabled', false).html(origHtml);
                    showSuccessDeadline('Batas waktu Usulan Akses berhasil diperbarui!');

                    // Tutup offcanvas & muat ulang setelah 2 detik agar countdown langsung sinkron
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: 'Terjadi kesalahan saat memperbarui jadwal usulan.',
                        width: '22rem'
                    });
                    console.error(xhr.responseText);
                }
            });
        });

        // 2. Submit Form General Akses
        $('#updateFormGen').on('submit', function(e) {
            e.preventDefault();
            const $btn = $(this).find('button[type="submit"]');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: '<?= base_url('updateBatchGen'); ?>',
                type: 'POST',
                data: $(this).serialize(),
                success: function() {
                    $btn.prop('disabled', false).html(origHtml);
                    showSuccessDeadline('Batas waktu General Akses berhasil diperbarui!');
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: 'Terjadi kesalahan saat memperbarui jadwal general.',
                        width: '22rem'
                    });
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

<script src="<?= base_url('assets/js/usulan_bansos/usulan_bansos.deadline.js'); ?>"></script>
<script src="<?= base_url('assets/js/usulan_bansos/usulan_bansos.table.js'); ?>"></script>
<script src="<?= base_url('assets/js/usulan_bansos/usulan_bansos.form.js'); ?>"></script>

<?= $this->endSection(); ?>