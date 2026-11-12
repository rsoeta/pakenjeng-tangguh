<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        color: #495057;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .card-custom {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }

    /* Animasi Lock */
    .lock-icon {
        font-size: 5rem;
        color: #6c757d;
        margin-bottom: 1rem;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #198754;
        box-shadow: 0 0 0 rgba(25, 135, 84, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
        }
        70% {
            box-shadow: 0 0 0 7px rgba(25, 135, 84, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
        }
    }

    /* Soft Background Accents untuk KPI Cards */
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.12) !important;
    }
    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.12) !important;
    }
    .bg-soft-warning {
        background-color: rgba(255, 193, 7, 0.18) !important;
    }
    .bg-soft-danger {
        background-color: rgba(220, 53, 69, 0.12) !important;
    }
    .bg-soft-secondary {
        background-color: rgba(108, 117, 125, 0.12) !important;
    }
</style>

<?php
// VARIABEL PENENTU STATUS AKSES PENTRI
$isPentri = ($user_login['role_id'] == 4);
$statusAkses = $akses['status'] ?? 'none';
$bolehAkses = (!$isPentri || $statusAkses === 'approved');
?>

<div class="content-wrapper mt-0">
    <div class="content-header pb-2">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1 fw-bold text-primary"><i class="fas fa-shield-alt me-2"></i>Rekapitulasi Perlinsos</h3>
                <small class="text-muted">Entri cepat data KPM Perlinsos Kemensos</small>
            </div>
            <div>
                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                    <i class="fas fa-user-check me-1"></i> Petugas: <?= $user_login['nama'] ?>
                </span>
            </div>
        </div>
    </div>

    <section class="content">

        <!-- =========================================================
             🚧 GERBANG VALIDASI (HANYA MUNCUL JIKA PENTRI BELUM DIAKUI)
             ========================================================= -->
        <?php if ($isPentri && !$bolehAkses): ?>

            <div class="row justify-content-center mt-4">
                <div class="col-lg-6 col-md-8">
                    <div class="card card-custom text-center border-top border-4 border-<?= ($statusAkses == 'rejected') ? 'danger' : (($statusAkses == 'pending') ? 'warning' : 'secondary') ?>">
                        <div class="card-body py-5 px-4">

                            <?php if ($statusAkses === 'pending'): ?>
                                <!-- TAMPILAN PENDING -->
                                <i class="fas fa-hourglass-half lock-icon text-warning"></i>
                                <h4 class="fw-bold">Menunggu Verifikasi Admin</h4>
                                <p class="text-muted">Bukti screenshot Anda sedang diperiksa oleh Admin/Kades. Modul ini akan otomatis terbuka setelah disetujui.</p>
                                <button class="btn btn-outline-secondary rounded-pill mt-3" onclick="location.reload()"><i class="fas fa-sync-alt me-1"></i> Muat Ulang Halaman</button>

                            <?php else: ?>
                                <!-- TAMPILAN UPLOAD BUKTI (BELUM ADA / DITOLAK) -->
                                <i class="fas fa-lock lock-icon text-<?= ($statusAkses == 'rejected') ? 'danger' : 'secondary' ?>"></i>
                                <h4 class="fw-bold">Akses Modul Terkunci</h4>
                                <p class="text-muted mb-2">Sebagai langkah validasi, Anda diwajibkan mengunggah <b>Screenshot Halaman Depan Perlinsos</b> untuk membuktikan bahwa Anda telah mendaftarkan KPM ke portal Kemensos.</p>

                                <?php if ($statusAkses === 'rejected'): ?>
                                    <div class="alert alert-danger mx-auto mt-3 text-start shadow-sm" style="max-width: 400px;">
                                        <strong><i class="fas fa-times-circle me-1"></i> Ditolak Admin!</strong><br>
                                        Alasan: <i>"<?= $akses['catatan'] ?>"</i><br>
                                        <small>Silakan unggah bukti yang benar.</small>
                                    </div>
                                <?php endif; ?>

                                <form id="formUploadBukti" class="mt-4" enctype="multipart/form-data">
                                    <div class="mb-3 text-start mx-auto" style="max-width: 400px;">
                                        <label class="form-label fw-bold">Unggah Bukti Screenshot (JPG/PNG)</label>
                                        <input class="form-control" type="file" id="bukti_screenshot" name="bukti_screenshot" accept=".jpg, .jpeg, .png" required>

                                        <!-- 🚀 WADAH PREVIEW GAMBAR (Awalnya Disembunyikan) -->
                                        <div class="mt-3 text-center d-none" id="previewContainer">
                                            <p class="small text-muted mb-1">Pratinjau Gambar:</p>
                                            <img id="previewImage" src="" alt="Preview Bukti" class="img-fluid rounded shadow-sm border" style="max-height: 250px; object-fit: contain;">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill shadow-sm" id="btnUploadBukti">
                                        <i class="fas fa-cloud-upload-alt me-1"></i> Kirim Bukti Akses
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- =========================================================
             🔓 MODUL UTAMA (JIKA ADMIN ATAU PENTRI APPROVED)
             ========================================================= -->

            <!-- 🔑 NAVIGASI TABS (HANYA UNTUK ADMIN) -->
            <?php if (!$isPentri): ?>
                <ul class="nav nav-tabs fw-bold mb-3" id="perlinsosTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="rekap-tab" data-bs-toggle="tab" data-bs-target="#tabRekap" type="button" role="tab"><i class="fas fa-clipboard-list me-1"></i> Modul Rekapitulasi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-primary" id="diagram-tab" data-bs-toggle="tab" data-bs-target="#tabDiagram" type="button" role="tab"><i class="fas fa-chart-pie me-1"></i> Rekapitulasi Realtime</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-danger" id="verifikasi-tab" data-bs-toggle="tab" data-bs-target="#tabVerifikasi" type="button" role="tab"><i class="fas fa-user-check me-1"></i> Verifikasi Akses Pentri</button>
                    </li>
                </ul>
            <?php endif; ?>

            <div class="tab-content" id="myTabContent">

                <!-- 🎯 TAB 1: MODUL REKAPITULASI (ISI ASLI PERLINSOS) -->
                <div class="tab-pane fade show active" id="tabRekap" role="tabpanel">

                    <!-- BARIS 1: FORM & GRAFIK -->
                    <div class="row g-3 mb-3">
                        <!-- KOLOM KIRI (FORM) -->
                        <div class="col-lg-4">
                            <div class="card card-custom h-100 border-top border-4 border-primary">
                                <div class="card-header bg-white border-0 pt-3 pb-0">
                                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-keyboard me-1"></i> Form Rekapitulasi</h6>
                                </div>
                                <div class="card-body">
                                    <form id="formPerlinsos">
                                        <input type="hidden" id="id_kk" name="id_kk">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Cari NIK / No. KK</label>
                                            <select class="form-select w-100" id="nik_peserta" name="nik_peserta" required>
                                                <option value="">Ketik NIK / No. KK...</option>
                                            </select>
                                            <div class="form-text text-info small mt-2"><i class="fas fa-info-circle"></i> Pastikan KPM sudah berhasil didaftarkan di aplikasi Perlinsos terlebih dahulu.</div>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm mt-2" id="btnSimpan">
                                            <i class="fas fa-save me-1"></i> Simpan Rekapitulasi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN (GRAFIK) -->
                        <div class="col-lg-8">
                            <div class="card card-custom h-100 border-top border-4 border-warning">
                                <div class="card-header bg-white border-0 pt-3 pb-0">
                                    <h6 class="fw-bold text-warning mb-0"><i class="fas fa-trophy me-1"></i> Leaderboard Petugas</h6>
                                </div>
                                <div class="card-body px-2">
                                    <div id="chartCapaian" style="min-height: 280px; width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BARIS 2: TABEL (FULL WIDTH) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-custom">
                                <div class="card-header bg-white border-bottom pt-3 pb-2 d-flex align-items-center">
                                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-history me-1"></i> Riwayat Input Terakhir</h6>
                                    <button id="btnReload" class="btn btn-sm btn-outline-secondary rounded-pill ms-auto">
                                        <i class="fas fa-sync-alt"></i> Segarkan
                                    </button>
                                </div>
                                <div class="card-body">

                                    <!-- 🎛️ FILTER BAR -->
                                    <div class="row g-2 mb-3">
                                        <?php if (!$isPentri): ?>
                                            <div class="col-md-3">
                                                <select class="form-select form-select-sm" id="filterPetugas">
                                                    <option value="">Semua Petugas</option>
                                                </select>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-auto">
                                            <select class="form-select form-select-sm" id="filterStatus">
                                                <option value="">Semua Status</option>
                                                <option value="belum_verifikasi">Belum Verifikasi</option>
                                                <option value="terverifikasi">Terverifikasi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-auto">
                                            <select class="form-select form-select-sm" id="filterRW">
                                                <option value="">Semua RW</option>
                                            </select>
                                        </div>
                                        <div class="col-md-auto">
                                            <select class="form-select form-select-sm" id="filterRT" disabled>
                                                <option value="">Semua RT</option>
                                            </select>
                                        </div>
                                        <div class="col-md-auto ms-auto">
                                            <button class="btn btn-outline-secondary btn-sm" id="btnResetFilter" title="Reset Filter">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- 📊 TABEL -->
                                    <table id="tablePerlinsos" class="table table-striped table-hover nowrap w-100">
                                        <thead class="bg-light text-center">
                                            <tr>
                                                <th class="text-center" style="width: 45px;">No.</th>
                                                <th>No. KK</th>
                                                <th>Kepala Keluarga</th>
                                                <th>NIK KPM</th>
                                                <th>Nama KPM</th>
                                                <th>Alamat Lengkap</th>
                                                <?php if (!$isPentri): ?>
                                                    <th>Petugas</th>
                                                <?php endif; ?>
                                                <th>Kategori Desil</th>
                                                <th>Waktu Rekap</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 🎯 TAB BARU: REKAPITULASI REALTIME (DIAGRAM) -->
                <?php if (!$isPentri): ?>
                    <div class="tab-pane fade" id="tabDiagram" role="tabpanel">

                        <!-- HEADER BAR & STATUS REALTIME -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 p-3 bg-white rounded-3 shadow-sm border">
                            <div class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                                <span class="badge bg-soft-success text-success border border-success rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="pulse-dot"></span>
                                    <span class="fw-bold">Sinkronisasi Realtime</span>
                                </span>
                                <span class="small text-muted" id="lastUpdatedSummary"><i class="fas fa-clock me-1"></i> Terakhir diperbarui: -</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" id="btnRefreshDiagram">
                                    <i class="fas fa-sync-alt me-1"></i> Segarkan Data
                                </button>
                            </div>
                        </div>

                        <!-- 5 KARTU REKAPITULASI UTAMA (KPI WIDGET) -->
                        <div class="row g-3 mb-4">
                            <!-- 1. TOTAL TARGET -->
                            <div class="col-xl col-md-4 col-sm-6">
                                <div class="card card-custom h-100 border-start border-4 border-secondary shadow-sm bg-white">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-muted small fw-bold text-uppercase">Total Target</span>
                                            <div class="rounded-circle bg-soft-secondary p-2 text-secondary d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                                <i class="fas fa-bullseye fa-sm"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-dark mb-0" id="cardTotalTarget">0</h3>
                                        <small class="text-muted">Target KK</small>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. TOTAL BELUM TERCAPAI -->
                            <div class="col-xl col-md-4 col-sm-6">
                                <div class="card card-custom h-100 border-start border-4 border-danger shadow-sm bg-white">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-danger small fw-bold text-uppercase">Belum Tercapai</span>
                                            <div class="rounded-circle bg-soft-danger p-2 text-danger d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                                <i class="fas fa-times-circle fa-sm"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-danger mb-0" id="cardBelumTercapai">0</h3>
                                        <small class="text-muted" id="persenBelumTercapai">0% sisa target</small>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. TOTAL CAPAIAN -->
                            <div class="col-xl col-md-4 col-sm-6">
                                <div class="card card-custom h-100 border-start border-4 border-primary shadow-sm bg-white">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-primary small fw-bold text-uppercase">Total Capaian</span>
                                            <div class="rounded-circle bg-soft-primary p-2 text-primary d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                                <i class="fas fa-chart-line fa-sm"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-primary mb-0" id="cardTotalCapaian">0</h3>
                                        <small class="text-muted fw-bold" id="cardPersenCapaian">0% tercapai</small>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. TOTAL BELUM VERIFIKASI -->
                            <div class="col-xl col-md-6 col-sm-6">
                                <div class="card card-custom h-100 border-start border-4 border-warning shadow-sm bg-white">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-warning small fw-bold text-uppercase">Belum Verifikasi</span>
                                            <div class="rounded-circle bg-soft-warning p-2 text-warning d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                                <i class="fas fa-clock fa-sm"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-warning mb-0" id="cardBelumVerifikasi">0</h3>
                                        <small class="text-muted">Menunggu Operator</small>
                                    </div>
                                </div>
                            </div>

                            <!-- 5. TOTAL TERVERIFIKASI -->
                            <div class="col-xl col-md-6 col-sm-6">
                                <div class="card card-custom h-100 border-start border-4 border-success shadow-sm bg-white">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="text-success small fw-bold text-uppercase">Terverifikasi</span>
                                            <div class="rounded-circle bg-soft-success p-2 text-success d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                                <i class="fas fa-check-double fa-sm"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-success mb-0" id="cardTerverifikasi">0</h3>
                                        <small class="text-muted">Tersinkron SIKS-NG</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BARIS DIAGRAM: DONUT CAPAIAN & KOMPARASI RW -->
                        <div class="row g-3 mb-4">
                            <!-- DIAGRAM 1: DONUT CHART KOMPOSISI CAPAIAN -->
                            <div class="col-lg-5">
                                <div class="card card-custom h-100 border-top border-4 border-primary">
                                    <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                                        <h6 class="fw-bold text-primary mb-0"><i class="fas fa-chart-pie me-1"></i> Proporsi Rekapitulasi</h6>
                                        <span class="badge bg-primary rounded-pill" id="badgeTotalKpm">0 KPM</span>
                                    </div>
                                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                        <div id="chartDonutSummary" style="min-height: 320px; width: 100%;"></div>
                                        <div class="small text-muted mt-2 text-center">
                                            <i class="fas fa-info-circle me-1"></i><b>Total Capaian</b> = Belum Verifikasi + Terverifikasi
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DIAGRAM 2: STACKED BAR PER WILAYAH (RW) -->
                            <div class="col-lg-7">
                                <div class="card card-custom h-100 border-top border-4 border-info">
                                    <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                                        <h6 class="fw-bold text-info mb-0"><i class="fas fa-chart-bar me-1"></i> Progres per Wilayah (RW)</h6>
                                        <small class="text-muted">Status Komparatif Antar-RW</small>
                                    </div>
                                    <div class="card-body px-2">
                                        <div id="chartRwSummary" style="min-height: 320px; width: 100%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BARIS TABEL RINCIAN PER RW -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-custom border-top border-4 border-secondary">
                                    <div class="card-header bg-white border-bottom pt-3 pb-2 d-flex align-items-center">
                                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-table me-1"></i> Rincian Capaian Tiap RW</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped align-middle mb-0 text-center" id="tableRwSummary">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Wilayah</th>
                                                        <th>Target (KK)</th>
                                                        <th>Total Capaian</th>
                                                        <th>Belum Verifikasi</th>
                                                        <th>Terverifikasi</th>
                                                        <th>Belum Tercapai</th>
                                                        <th style="min-width: 150px;">Progres (%)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyRwSummary">
                                                    <tr>
                                                        <td colspan="7" class="text-muted py-3">Memuat data...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>

                <!-- 🎯 TAB 2: VERIFIKASI AKSES (HANYA ADMIN) -->
                <?php if (!$isPentri): ?>
                    <div class="tab-pane fade" id="tabVerifikasi" role="tabpanel">
                        <div class="card card-custom border-top border-4 border-danger">
                            <div class="card-header bg-white border-bottom pt-3 pb-2 d-flex align-items-center">
                                <h6 class="fw-bold text-danger mb-0"><i class="fas fa-user-lock me-1"></i> Daftar Tunggu Verifikasi Akses Pentri</h6>
                                <button id="btnReloadAkses" class="btn btn-sm btn-outline-secondary rounded-pill ms-auto">
                                    <i class="fas fa-sync-alt"></i> Segarkan Tabel
                                </button>
                            </div>
                            <div class="card-body">
                                <table id="tableAkses" class="table table-striped table-hover nowrap w-100">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Waktu Upload</th>
                                            <th>Petugas</th>
                                            <th>Bukti Screenshot</th>
                                            <th>Status Akses</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- AKHIR TAB 2 -->

            </div>
        <?php endif; ?>

    </section>
</div>

<script>
    // ==========================================
    // 🛡️ FUNGSI BANTUAN: SENSOR DATA SENSITIF
    // ==========================================
    function maskNumberJS(number) {
        if (!number || number === '-' || number === 'NOKKS') return number || '-';
        let numStr = number.toString().trim();
        if (numStr.length <= 8) return numStr;
        let masked = numStr.substring(0, 8) + '*'.repeat(numStr.length - 8);
        return `<span class="fw-bold" style="cursor:pointer;" 
                  onmouseenter="this.innerText='${numStr}'" 
                  onmouseleave="this.innerText='${masked}'" 
                  ontouchstart="this.innerText='${numStr}'" 
                  ontouchend="this.innerText='${masked}'" 
                  title="Arahkan kursor untuk melihat utuh">${masked}</span>`;
    }

    $(document).ready(function() {

        // VARIABEL BANTUAN JS (DARI PHP)
        const isPentri = <?= $isPentri ? 'true' : 'false' ?>;
        const bolehAkses = <?= $bolehAkses ? 'true' : 'false' ?>;
        const roleId = <?= $user_login['role_id'] ?>;

        // ==========================================================
        // 📸 JS GATEKEEPER: PREVIEW GAMBAR SEBELUM UPLOAD
        // ==========================================================
        $('#bukti_screenshot').on('change', function(event) {
            const file = event.target.files[0];
            const previewContainer = $('#previewContainer');
            const previewImage = $('#previewImage');

            if (file) {
                // Validasi ukuran di sisi klien (Opsional, max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Terlalu Besar', 'Ukuran gambar maksimal 2MB', 'warning');
                    $(this).val('');
                    previewContainer.addClass('d-none');
                    return;
                }

                // Baca dan tampilkan gambar
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.attr('src', e.target.result);
                    previewContainer.removeClass('d-none'); // Munculkan wadah
                }
                reader.readAsDataURL(file);
            } else {
                // Sembunyikan jika file dibatalkan
                previewImage.attr('src', '');
                previewContainer.addClass('d-none');
            }
        });

        // ==========================================================
        // 🔐 JS GATEKEEPER: UPLOAD BUKTI (UNTUK PENTRI)
        // ==========================================================
        $('#formUploadBukti').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            let btn = $('#btnUploadBukti');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Mengunggah...');

            $.ajax({
                url: '/perlinsos/upload-bukti',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Berhasil!', res.message, 'success').then(() => {
                            location.reload(); // Refresh layar agar masuk state 'pending'
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1"></i> Kirim Bukti Akses');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1"></i> Kirim Bukti Akses');
                }
            });
        });

        // ==========================================================
        // 🔓 JS GATEKEEPER: TABEL ADMIN VERIFIKASI (ROLE <= 3)
        // ==========================================================
        if (!isPentri && bolehAkses) {
            const tableAkses = $('#tableAkses').DataTable({
                ajax: {
                    url: '/perlinsos/data-akses',
                    type: 'POST',
                    dataSrc: 'data'
                },
                responsive: true,
                autoWidth: false, // 🚀 FIX 1: Matikan autoWidth agar tidak kacau di tab tersembunyi
                columnDefs: [{
                        targets: '_all',
                        defaultContent: '-'
                    } // 🚀 FIX 2: Sabuk pengaman mutlak untuk data kosong
                ],
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1,
                        // className: 'text-center'
                    },
                    {
                        data: 'created_at',
                        // className: 'text-center'
                    },
                    {
                        data: 'fullname',
                        className: 'text-start text-capitalize fw-bold'
                    },
                    {
                        data: 'file_bukti',
                        // className: 'text-center',
                        render: function(img) {
                            return `<button class="btn btn-sm btn-info btnLihatBukti" data-img="/uploads/perlinsos/${img}"><i class="fas fa-image me-1"></i> Lihat Bukti</button>`;
                        }
                    },
                    {
                        data: 'status',
                        // className: 'text-center',
                        render: function(s) {
                            if (s === 'approved') return '<span class="badge bg-success">Disetujui</span>';
                            if (s === 'rejected') return '<span class="badge bg-danger">Ditolak</span>';
                            return '<span class="badge bg-warning text-dark">Menunggu</span>';
                        }
                    },
                    {
                        data: 'id',
                        // className: 'text-center',
                        render: function(id, type, row) {
                            if (row.status === 'approved') {
                                return `<button class="btn btn-sm btn-danger btnUbahStatus" data-id="${id}" data-action="rejected" title="Cabut Akses"><i class="fas fa-times"></i> Cabut</button>`;
                            }
                            return `
                            <button class="btn btn-sm btn-success btnUbahStatus me-1" data-id="${id}" data-action="approved" title="Setujui"><i class="fas fa-check"></i> Setujui</button>
                            <button class="btn btn-sm btn-danger btnUbahStatus" data-id="${id}" data-action="rejected" title="Tolak"><i class="fas fa-times"></i> Tolak</button>
                        `;
                        }
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                }
            });

            // 🚀 FIX 3: Kalkulasi ulang lebar DataTables saat Tab diklik (Wajib untuk Mobile)
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust().responsive.recalc();
            });

            $('#btnReloadAkses').click(() => tableAkses.ajax.reload());

            // Lihat Gambar Bukti pakai SweetAlert (Lebih elegan dan ANTI-OVERFLOW)
            $(document).on('click', '.btnLihatBukti', function() {
                let img = $(this).data('img');
                Swal.fire({
                    title: 'Bukti Screenshot',
                    // 🚀 KUNCI SAKTI: Gunakan parameter html, lalu batasi tinggi maksimal gambar ke 70% dari tinggi layar (70vh)
                    html: `<img src="${img}" alt="Screenshot" style="max-height: 70vh; width: auto; max-width: 100%; object-fit: contain; border-radius: 8px; border: 1px solid #ddd;">`,
                    width: '800px', // Lebar modal diperbesar
                    confirmButtonText: '<i class="fas fa-times me-1"></i> Tutup',
                    confirmButtonColor: '#6c757d',
                    customClass: {
                        popup: 'rounded-4' // Opsional: membuat sudut modal lebih halus
                    }
                });
            });

            // Eksekusi Setuju/Tolak Akses
            $(document).on('click', '.btnUbahStatus', function() {
                let id = $(this).data('id');
                let action = $(this).data('action');
                let actionText = (action === 'approved') ? 'Menyetujui' : 'Menolak';

                let promptHtml = '';
                if (action === 'rejected') {
                    promptHtml = `<p>Berikan alasan penolakan agar Pentri bisa memperbaikinya:</p><textarea id="alasanTolak" class="form-control" rows="2" placeholder="Wajib diisi jika menolak"></textarea>`;
                }

                Swal.fire({
                    title: `Yakin ingin ${actionText} akses ini?`,
                    html: promptHtml,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Proses!',
                    preConfirm: () => {
                        let catatan = '';
                        if (action === 'rejected') {
                            catatan = document.getElementById('alasanTolak').value;
                            if (!catatan) Swal.showValidationMessage('Alasan penolakan wajib diisi!');
                        }
                        return catatan;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/perlinsos/verify-akses', {
                            id: id,
                            status: action,
                            catatan: result.value
                        }, function(res) {
                            if (res.success) {
                                Swal.fire('Berhasil!', res.message, 'success');
                                tableAkses.ajax.reload(null, false);
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        }, 'json');
                    }
                });
            });
        }

        // ==========================================================
        // 📊 JS MODAL UTAMA (JIKA APPROVED / ADMIN)
        // ==========================================================
        if (bolehAkses) {

            // 1️⃣ INITIALIZE SELECT2 (AJAX PENCARIAN)
            $('#nik_peserta').select2({
                placeholder: "Ketik NIK / No. KK...",
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: '/perlinsos/search-art',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            $('#nik_peserta').on('select2:select', function(e) {
                $('#id_kk').val(e.params.data.id_kk);
            });

            $('#nik_peserta').on('select2:clear', function(e) {
                $('#id_kk').val('');
            });

            // 2️⃣ INITIALIZE DATATABLES
            const tablePerlinsos = $('#tablePerlinsos').DataTable({
                ajax: {
                    url: '/perlinsos/data',
                    type: 'POST',
                    data: function(d) {
                        d.petugas = $('#filterPetugas').val() || '';
                        d.status  = $('#filterStatus').val() || '';
                        d.rw      = $('#filterRW').val() || '';
                        d.rt      = $('#filterRT').val() || '';
                    },
                    dataSrc: 'data'
                },
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                order: [], // 🚀 Pertahankan urutan data dari server (created_at DESC)

                columnDefs: [{
                    targets: '_all',
                    defaultContent: '-'
                }],

                // 🚀 PERBAIKAN 2A: Gunakan drawCallback agar nomor selalu dihitung ulang dari 1 sesuai halaman aktif
                drawCallback: function(settings) {
                    let api = this.api();
                    let startIndex = api.context[0]._iDisplayStart;
                    api.column(0, {
                        page: 'current'
                    }).nodes().each(function(cell, i) {
                        // Bungkus dengan span agar aman dari ikon toggle panah DataTables
                        cell.innerHTML = '<span class="fw-bold text-secondary">' + (startIndex + i + 1) + '</span>';
                    });
                },

                columns: [
                    // 🚀 0. Nomor Urut
                    {
                        data: null,
                        className: 'text-center align-middle',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            let no = meta.row + meta.settings._iDisplayStart + 1;
                            return '<span class="fw-bold text-secondary">' + no + '</span>';
                        }
                    },
                    // 🚀 1. No. KK (Dipindahkan sebelum Kepala Keluarga)
                    {
                        data: 'no_kk',
                        className: 'text-nowrap',
                        render: function(data, type) {
                            if (!data) return '-';
                            if (type === 'filter' || type === 'sort') return data;
                            return `
                        <div class="d-flex gap-2">
                            <span>${maskNumberJS(data)}</span>
                            <button type="button" class="btn btn-outline-secondary btn-xs btnCopyGlobal" data-value="${data}" title="Salin"><i class="fas fa-copy"></i></button>
                        </div>`;
                        }
                    },
                    // 🚀 2. Kepala Keluarga
                    {
                        data: 'kepala_keluarga',
                        className: 'text-start text-capitalize'
                    },
                    // 🚀 3. NIK KPM
                    {
                        data: 'nik',
                        className: 'text-nowrap',
                        render: function(data, type) {
                            if (!data) return '-';
                            if (type === 'filter' || type === 'sort') return data;
                            let maskedData = maskNumberJS(data).replace('class="fw-bold"', 'class="fw-bold text-primary"');
                            return `
                        <div class="d-flex gap-2">
                            <span>${maskedData}</span>
                            <button type="button" class="btn btn-outline-secondary btn-xs btnCopyGlobal" data-value="${data}" title="Salin"><i class="fas fa-copy"></i></button>
                        </div>`;
                        }
                    },
                    // 🚀 4. Nama KPM
                    {
                        data: 'nama_kpm',
                        className: 'text-start text-capitalize'
                    },
                    // 🚀 5. Alamat Lengkap
                    {
                        data: null,
                        className: 'text-start',
                        render: function(row) {
                            let alamat = row.alamat ? row.alamat + ', ' : '';
                            let rt = row.rt ? 'RT ' + row.rt : '';
                            let rw = row.rw ? ' / RW ' + row.rw : '';
                            return (alamat + rt + rw) || '-';
                        }
                    },
                    // 🚀 6. Petugas (Hanya Admin / Operator)
                    ...(roleId <= 3 ? [{
                        data: 'petugas',
                        className: 'text-start text-uppercase',
                        render: d => d ? d.toUpperCase() : '-'
                    }] : []),
                    // 🚀 7. Kategori Desil (Disimpan sebelum Waktu Rekap)
                    {
                        data: 'kategori_desil',
                        className: 'text-center align-middle',
                        render: function(data) {
                            if (!data || data === '0' || data === 0 || data === '') {
                                return '<span class="badge bg-secondary px-2 py-1">-</span>';
                            }
                            let val = parseInt(data);
                            let bgClass = 'bg-danger';
                            if (val <= 3) bgClass = 'bg-success';
                            else if (val <= 5) bgClass = 'bg-warning text-dark';

                            return `<span class="badge ${bgClass} px-2 py-1">Desil ${val}</span>`;
                        }
                    },
                    // 🚀 8. Waktu Rekap
                    {
                        data: 'created_at',
                        className: 'text-center',
                        render: d => d ? d.substring(0, 16) : '-'
                    },
                    // 🚀 9. Status
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function(s) {
                            if (s === 'tersinkron') {
                                return '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Terverifikasi</span>';
                            }
                            return '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Belum Verifikasi</span>';
                        }
                    },
                    // 🚀 10. Aksi
                    {
                        data: 'id',
                        orderable: false,
                        className: 'text-center',
                        render: function(id, type, row) {
                            let status = row.status || 'draft'; // Anggap 'draft' jika kosong

                            // 🔒 Jika sudah divalidasi, tampilkan gembok untuk SEMUA ROLE
                            if (status === 'tersinkron') {
                                return `<button class="btn btn-outline-secondary btn-sm rounded-circle shadow-sm" disabled title="Terkunci (Telah Sinkron dengan SIKS-NG)"><i class="fas fa-lock"></i></button>`;
                            }

                            let btn = '';

                            // 🚀 Jika Operator (Role <= 3), munculkan tombol Validasi
                            if (roleId <= 3) {
                                btn += `<button class="btn btn-outline-success btn-sm rounded-circle shadow-sm btnVerifyData me-1" data-id="${id}" title="Validasi (Tandai Sudah Sinkron)"><i class="fas fa-check-double"></i></button>`;
                            }

                            // 🗑️ Tombol Hapus (Milik PENTRI & Operator selama belum digembok)
                            btn += `<button class="btn btn-outline-danger btn-sm rounded-circle shadow-sm btnHapus" data-id="${id}" title="Hapus Data"><i class="fas fa-trash"></i></button>`;

                            return btn;
                        }
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                }
            });

            $('#btnReload').on('click', function() {
                tablePerlinsos.ajax.reload(null, false);
                loadChart();
                if (typeof loadSummaryDiagram === 'function') loadSummaryDiagram();
            });

            // 3️⃣ LOGIKA FILTER
            $.getJSON('/perlinsos/filter-options', function(res) {
                if (res.petugas) res.petugas.forEach(p => $('#filterPetugas').append(`<option value="${p.nik}">${p.fullname}</option>`));
                if (res.rw) res.rw.forEach(r => $('#filterRW').append(`<option value="${r.rw}">RW ${r.rw}</option>`));
            });

            $('#filterPetugas, #filterRT, #filterStatus').on('change', () => tablePerlinsos.ajax.reload(null, false));

            $('#filterRW').on('change', function() {
                const rw = $(this).val();
                const $rt = $('#filterRT');
                $rt.prop('disabled', !rw).html('<option value="">Semua RT</option>');
                tablePerlinsos.ajax.reload(null, false);

                if (rw) {
                    $.getJSON('/perlinsos/rt-by-rw/' + rw, function(res) {
                        if (res.rt) res.rt.forEach(rt => $rt.append(`<option value="${rt}">RT ${rt}</option>`));
                    });
                }
            });

            $('#btnResetFilter').on('click', function() {
                $('#filterPetugas, #filterRW, #filterStatus').val('');
                $('#filterRT').html('<option value="">Semua RT</option>').prop('disabled', true);
                tablePerlinsos.ajax.reload(null, false);
            });

            // 4️⃣ INITIALIZE APEXCHARTS (STACKED BAR DENGAN PERSENTASE + RESPONSIVE MOBILE)
            var chartOptions = {
                series: [],
                chart: {
                    type: 'bar',
                    height: 380, // Sedikit ditambah untuk ruang desktop
                    stacked: true,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false, // Default Desktop: Vertikal
                        columnWidth: '45%',
                        borderRadius: 3,
                        dataLabels: {
                            position: 'center'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        if (val === 0) return '';

                        let totalTarget = 0;
                        if (opts.w && opts.w.globals && opts.w.globals.series) {
                            for (let s = 0; s < opts.w.globals.series.length; s++) {
                                totalTarget += (opts.w.globals.series[s][opts.dataPointIndex] || 0);
                            }
                        }
                        let persen = totalTarget > 0 ? Math.round((val / totalTarget) * 100) : 0;

                        return val + ' (' + persen + '%)';
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 'bold',
                        colors: ['#ffffff'] // Tetap putih
                    },
                    // 🚀 PERBAIKAN 1: Tambahkan efek bayangan tebal agar teks tidak menyatu dengan background putih
                    dropShadow: {
                        enabled: true,
                        top: 1,
                        left: 1,
                        blur: 2,
                        color: '#000000',
                        opacity: 0.85
                    }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: [],
                    labels: {
                        rotate: -45,
                        rotateAlways: false,
                        style: {
                            fontSize: '11px',
                            fontWeight: 600
                        }
                    }
                },
                colors: ['#dc3545', '#ffc107', '#198754'], // Merah (Belum Tercapai), Kuning (Belum Verifikasi), Hijau (Terverifikasi)
                fill: {
                    opacity: 1
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " KPM"
                        }
                    }
                },
                // 🚀 KUNCI SAKTI: OVERRIDE KHUSUS UNTUK LAYAR MOBILE
                responsive: [{
                    breakpoint: 768, // Aktif ketika lebar layar 768px ke bawah
                    options: {
                        chart: {
                            // Tinggikan kanvas agar belasan nama petugas tidak saling tindih
                            height: 600
                        },
                        plotOptions: {
                            bar: {
                                horizontal: true, // Berubah jadi batang menyamping
                                barHeight: '70%', // Ketebalan batang proporsional
                                borderRadius: 3
                            }
                        },
                        xaxis: {
                            // Sembunyikan label sumbu X bawah di HP agar lebih lega
                            labels: {
                                show: false
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    fontWeight: 600
                                }
                            }
                        },
                        dataLabels: {
                            style: {
                                fontSize: '10px' // Perkecil sedikit font agar muat di batang
                            }
                        }
                    }
                }]
            };

            var chart = new ApexCharts(document.querySelector("#chartCapaian"), chartOptions);
            chart.render();

            function loadChart() {
                $.getJSON('/perlinsos/chart-capaian', function(response) {
                    chart.updateSeries(response.series);
                    chart.updateOptions({
                        xaxis: {
                            categories: response.categories
                        }
                    });
                });
            }
            loadChart();

            // 5️⃣ LOGIKA SIMPAN & HAPUS
            $('#formPerlinsos').on('submit', function(e) {
                e.preventDefault();
                let id_kk = $('#id_kk').val();
                let nik = $('#nik_peserta').val();

                if (!id_kk || !nik) {
                    Swal.fire('Peringatan', 'Silakan pilih KPM terlebih dahulu!', 'warning');
                    return;
                }

                let btn = $('#btnSimpan');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: '/perlinsos/save',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Rekapitulasi');
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#nik_peserta').val(null).trigger('change');
                            $('#id_kk').val('');
                            tablePerlinsos.ajax.reload(null, false);
                            loadChart();
                            if (typeof loadSummaryDiagram === 'function') loadSummaryDiagram();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    }
                });
            });

            $(document).on('click', '.btnHapus', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Rekapitulasi?',
                    text: "Data yang dihapus dari SINDEN tidak memengaruhi data di web Perlinsos.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/perlinsos/delete/' + id, function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                tablePerlinsos.ajax.reload(null, false);
                                loadChart();
                                if (typeof loadSummaryDiagram === 'function') loadSummaryDiagram();
                            }
                        }, 'json');
                    }
                });
            });

            // 6️⃣ LOGIKA COPY
            $(document).on('click', '.btnCopyGlobal', function() {
                const value = $(this).data('value');
                if (!value) return;
                navigator.clipboard.writeText(value).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersalin',
                        text: 'Data ' + value + ' berhasil disalin ke clipboard',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top'
                    });
                });
            });

            // 🚀 7️⃣ LOGIKA VALIDASI SINKRONISASI (KHUSUS OPERATOR)
            $(document).on('click', '.btnVerifyData', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Validasi Sinkronisasi?',
                    text: "Tindakan ini akan mengunci data secara permanen sebagai tanda bahwa data SINDEN dan SIKS-NG sudah sinkron.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check-double me-1"></i> Ya, Validasi!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('/perlinsos/verify-data/' + id, function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Tervalidasi!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                tablePerlinsos.ajax.reload(null, false);
                                loadChart(); // Opsional jika status validasi memengaruhi chart
                                if (typeof loadSummaryDiagram === 'function') loadSummaryDiagram();
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        }, 'json');
                    }
                });
            });

            // ==========================================================
            // 📊 8️⃣ JS TAB DIAGRAM: REKAPITULASI REALTIME (KHUSUS ADMIN)
            // ==========================================================
            if (!isPentri) {
                // 1. Donut Chart - Komposisi Capaian Keseluruhan
                var donutOptions = {
                    series: [0, 0, 0],
                    labels: ['Belum Tercapai', 'Belum Verifikasi', 'Terverifikasi'],
                    chart: {
                        type: 'donut',
                        height: 320
                    },
                    colors: ['#dc3545', '#ffc107', '#198754'],
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center'
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#ffffff']
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '13px',
                                        fontWeight: 600
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '18px',
                                        fontWeight: 'bold',
                                        formatter: val => val + ' KPM'
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total Capaian',
                                        fontSize: '12px',
                                        fontWeight: 600,
                                        color: '#0d6efd',
                                        formatter: function(w) {
                                            let s = w.globals.seriesTotals;
                                            let capaian = (s[1] || 0) + (s[2] || 0);
                                            let target = (s[0] || 0) + capaian;
                                            let persen = target > 0 ? Math.round((capaian / target) * 100) : 0;
                                            return capaian + ' (' + persen + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return Math.round(val) + '%';
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: val => val + ' KPM'
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: { height: 280 },
                            legend: { position: 'bottom' }
                        }
                    }]
                };

                var chartDonut = new ApexCharts(document.querySelector("#chartDonutSummary"), donutOptions);
                chartDonut.render();

                // 2. Stacked Bar Chart - Progres per RW
                var rwBarOptions = {
                    series: [],
                    chart: {
                        type: 'bar',
                        height: 320,
                        stacked: true,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '45%',
                            borderRadius: 3
                        }
                    },
                    xaxis: {
                        categories: [],
                        labels: {
                            style: { fontSize: '11px', fontWeight: 600 }
                        }
                    },
                    colors: ['#dc3545', '#ffc107', '#198754'],
                    legend: { position: 'top' },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val > 0 ? val : '';
                        },
                        style: { fontSize: '10px', colors: ['#ffffff'] },
                        dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: 0.8 }
                    },
                    tooltip: {
                        y: { formatter: val => val + ' KK/KPM' }
                    },
                    responsive: [{
                        breakpoint: 768,
                        options: {
                            chart: { height: 420 },
                            plotOptions: {
                                bar: { horizontal: true, barHeight: '65%' }
                            }
                        }
                    }]
                };

                var chartRw = new ApexCharts(document.querySelector("#chartRwSummary"), rwBarOptions);
                chartRw.render();

                // 3. Fungsi Ambil & Perbarui Data Diagram Realtime
                window.loadSummaryDiagram = function() {
                    let $btn = $('#btnRefreshDiagram');
                    $btn.prop('disabled', true).find('i').addClass('fa-spin');

                    $.getJSON('/perlinsos/summary-capaian', function(res) {
                        $btn.prop('disabled', false).find('i').removeClass('fa-spin');

                        // Update Kartu Angka KPI
                        $('#cardTotalTarget').text(res.total_target.toLocaleString('id-ID'));
                        $('#cardBelumTercapai').text(res.total_belum_tercapai.toLocaleString('id-ID'));
                        $('#cardTotalCapaian').text(res.total_capaian.toLocaleString('id-ID'));
                        $('#cardBelumVerifikasi').text(res.total_belum_verifikasi.toLocaleString('id-ID'));
                        $('#cardTerverifikasi').text(res.total_terverifikasi.toLocaleString('id-ID'));

                        let persenBelum = res.total_target > 0 ? Math.round((res.total_belum_tercapai / res.total_target) * 100) : 0;
                        $('#persenBelumTercapai').text(persenBelum + '% sisa target');
                        $('#cardPersenCapaian').text(res.persen_capaian + '% tercapai');
                        $('#badgeTotalKpm').text(res.total_capaian.toLocaleString('id-ID') + ' KPM');
                        $('#lastUpdatedSummary').html('<i class="fas fa-clock me-1"></i> Terakhir diperbarui: ' + res.timestamp);

                        // Update Donut Chart
                        chartDonut.updateSeries(res.donut.series);

                        // Update RW Chart
                        chartRw.updateOptions({
                            xaxis: { categories: res.rw_chart.categories }
                        });
                        chartRw.updateSeries(res.rw_chart.series);

                        // Update Tabel Breakdown RW
                        let tbody = '';
                        if (res.rw_details && res.rw_details.length > 0) {
                            res.rw_details.forEach(item => {
                                let badgeColor = item.persen >= 100 ? 'bg-success' : (item.persen >= 50 ? 'bg-primary' : 'bg-warning text-dark');
                                tbody += `<tr>
                                    <td class="fw-bold">${item.rw}</td>
                                    <td>${item.target}</td>
                                    <td class="fw-bold text-primary">${item.capaian}</td>
                                    <td class="text-warning fw-bold">${item.belum_verifikasi}</td>
                                    <td class="text-success fw-bold">${item.terverifikasi}</td>
                                    <td class="text-danger fw-bold">${item.belum_tercapai}</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; min-width: 60px;">
                                                <div class="progress-bar ${item.persen >= 100 ? 'bg-success' : 'bg-primary'}" role="progressbar" style="width: ${Math.min(100, item.persen)}%"></div>
                                            </div>
                                            <span class="badge ${badgeColor}">${item.persen}%</span>
                                        </div>
                                    </td>
                                </tr>`;
                            });
                        } else {
                            tbody = '<tr><td colspan="7" class="text-muted py-3">Belum ada data rincian RW.</td></tr>';
                        }
                        $('#tbodyRwSummary').html(tbody);
                    }).fail(function() {
                        $btn.prop('disabled', false).find('i').removeClass('fa-spin');
                    });
                };

                // Trigger ketika tab diagram dibuka
                $('#diagram-tab').on('shown.bs.tab', function() {
                    loadSummaryDiagram();
                    setTimeout(() => {
                        window.dispatchEvent(new Event('resize'));
                    }, 150);
                });

                // Tombol segarkan manual
                $('#btnRefreshDiagram').on('click', function() {
                    loadSummaryDiagram();
                });
            }
        }
    });
</script>

<?= $this->endSection(); ?>