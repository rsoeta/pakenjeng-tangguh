<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<style>
    /* Styling KPI Stat Cards */
    .stat-card-widget {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .stat-card-widget:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
    }
    .stat-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .chart-container-box {
        min-height: 340px;
        position: relative;
    }
    .table-bnba th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        background-color: #f8f9fa;
        vertical-align: middle !important;
    }
    .table-bnba td {
        font-size: 0.85rem;
        vertical-align: middle !important;
    }
    .filter-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 0.25rem;
    }
    .badge-purple {
        background-color: #6f42c1;
        color: #ffffff;
    }
    .preview-foto-modal {
        width: 100%;
        max-width: 250px;
        aspect-ratio: 3 / 4;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
        background-color: #f8f9fa;
        display: block;
        margin: 0 auto;
    }
    .preview-foto-modal:hover {
        opacity: 0.95;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    /* 🚀 Fix AdminLTE clearfix agar flexbox tidak mendorong tombol ke tengah */
    .card-header::after {
        display: none !important;
    }
</style>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 d-flex justify-content-between align-items-center">
                <div class="col-auto">
                    <h4 class="m-0 fw-bold fs-5 text-dark">
                        <i class="fas fa-chart-pie text-primary mr-2"></i> <?= esc($title); ?>
                    </h4>
                    <p class="text-muted small mb-0 d-none d-sm-block">
                        Rekapitulasi visual dan By Name By Address (BNBA) penyaluran bansos KKS
                    </p>
                </div>
                <div class="col-auto">
                    <ol class="breadcrumb bg-transparent p-0 m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('bansos/rekapitulasi'); ?>">Bansos</a></li>
                        <li class="breadcrumb-item active">Rekapitulasi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <!-- ======================================================= -->
            <!-- 🔍 SECTION: FILTER DATA -->
            <!-- ======================================================= -->
            <div class="card shadow-sm border mb-4 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-filter text-primary mr-2"></i> Filter Data Bantuan Sosial
                    </h6>
                </div>
                <div class="card-body p-3">
                    <!-- BARIS 1: AREA FILTER DROPDOWN -->
                    <div class="row align-items-end g-2">
                        <!-- 1. Filter Petugas Entri -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg">
                            <label class="filter-label">Petugas Entri</label>
                            <?php
                            $isPentri = (int)($role_id ?? 4) >= 4;
                            $myId = session()->get('id') ?? session()->get('id_user') ?? session()->get('user_id');
                            ?>
                            <select id="filter_petugas" class="form-control form-control-sm rounded-pill px-3 shadow-none border" <?= $isPentri ? 'disabled' : '' ?>>
                                <option value="">Semua Petugas</option>
                                <?php if (!empty($petugas_list)): ?>
                                    <?php foreach ($petugas_list as $p): ?>
                                        <option value="<?= $p['id'] ?>" <?= ($isPentri && $myId == $p['id']) ? 'selected' : '' ?>>
                                            <?= esc(ucwords(strtolower($p['fullname']))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- 2. Filter Wilayah RW -->
                        <div class="col-6 col-sm-6 col-md-4 col-lg">
                            <label class="filter-label">Wilayah RW</label>
                            <select id="filter_rw" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                                <option value="">Semua RW</option>
                            </select>
                        </div>

                        <!-- 3. Filter Wilayah RT -->
                        <div class="col-6 col-sm-6 col-md-4 col-lg">
                            <label class="filter-label">Wilayah RT</label>
                            <select id="filter_rt" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                                <option value="">Semua RT</option>
                            </select>
                        </div>

                        <!-- 4. Filter Tahap Salur -->
                        <div class="col-6 col-sm-6 col-md-6 col-lg">
                            <label class="filter-label">Tahap Salur</label>
                            <select id="filter_tahap" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                                <option value="">Semua Tahap</option>
                                <?php if (!empty($tahap_list)): ?>
                                    <?php foreach ($tahap_list as $tahap): ?>
                                        <option value="<?= esc($tahap) ?>">
                                            <?= esc($tahap) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- 5. Filter Jenis Bansos -->
                        <div class="col-6 col-sm-6 col-md-6 col-lg">
                            <label class="filter-label">Jenis Bansos</label>
                            <select id="filter_jenis" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                                <option value="">Semua</option>
                                <option value="PKH">PKH</option>
                                <option value="SEMBAKO">Sembako</option>
                                <option value="PKH + SEMBAKO">PKH + Sembako</option>
                            </select>
                        </div>
                    </div>

                    <!-- BARIS 2: AREA TOMBOL AKSI (SISI PALING KANAN) -->
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end align-items-center" style="gap: 8px;">
                            <button id="btn_reset" type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm" title="Reset Filter">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                            <button id="btn_filter" type="button" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <button id="btn_export_excel" type="button" class="btn btn-sm btn-success rounded-pill px-4 shadow-sm btn-export-excel">
                                <i class="fas fa-file-excel mr-1"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================= -->
            <!-- 📊 SECTION CARD DIATAS: DONUT CHART & KPI SUMMARY -->
            <!-- ======================================================= -->
            <div class="card shadow-sm border mb-4 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center mb-0">
                            <i class="fas fa-chart-pie text-success mr-2"></i> Rekapitulasi Realisasi & Komposisi Bantuan Sosial
                        </h6>
                        <span class="badge badge-light border text-muted small px-3 py-1 ms-auto ml-auto" id="labelActiveFilter" style="margin-left: auto !important;">
                            Semua Bansos
                        </span>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">

                    <!-- BARIS KPI RINGKASAN DATA -->
                    <div class="row g-2 mb-4">
                        <!-- Total KPM -->
                        <div class="col-6 col-md-4 col-xl">
                            <div class="card stat-card-widget shadow-sm p-3 bg-white h-100">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon-circle bg-light text-primary mr-3 shadow-sm">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Total KPM</span>
                                        <h4 class="fw-bold mb-0 text-dark" id="kpi_total_kpm">0</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sukses Salur -->
                        <div class="col-6 col-md-4 col-xl">
                            <div class="card stat-card-widget shadow-sm p-3 bg-white h-100 border-left border-success" style="border-left-width: 4px !important;">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon-circle bg-success bg-opacity-10 text-success mr-3 shadow-sm" style="background-color: rgba(40, 167, 69, 0.12);">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Sukses Salur</span>
                                        <div class="d-flex align-items-baseline" style="gap: 5px;">
                                            <h4 class="fw-bold mb-0 text-success" id="kpi_sukses_salur">0</h4>
                                            <small class="text-muted fw-bold" id="kpi_persen_sukses">(0%)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Belum Salur / Draft -->
                        <div class="col-6 col-md-4 col-xl">
                            <div class="card stat-card-widget shadow-sm p-3 bg-white h-100 border-left border-warning" style="border-left-width: 4px !important;">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon-circle text-warning mr-3 shadow-sm" style="background-color: rgba(255, 193, 7, 0.15);">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Belum Salur</span>
                                        <div class="d-flex align-items-baseline" style="gap: 5px;">
                                            <h4 class="fw-bold mb-0 text-warning" id="kpi_belum_salur">0</h4>
                                            <small class="text-muted fw-bold" id="kpi_persen_belum">(0%)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gagal Salur / Kendala -->
                        <div class="col-6 col-md-6 col-xl">
                            <div class="card stat-card-widget shadow-sm p-3 bg-white h-100 border-left border-danger" style="border-left-width: 4px !important;">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon-circle text-danger mr-3 shadow-sm" style="background-color: rgba(220, 53, 69, 0.12);">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Gagal / Kendala</span>
                                        <div class="d-flex align-items-baseline" style="gap: 5px;">
                                            <h4 class="fw-bold mb-0 text-danger" id="kpi_gagal_salur">0</h4>
                                            <small class="text-muted fw-bold" id="kpi_persen_gagal">(0%)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Dana Cair -->
                        <div class="col-12 col-md-6 col-xl">
                            <div class="card stat-card-widget shadow-sm p-3 bg-white h-100 border-left border-primary" style="border-left-width: 4px !important;">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon-circle text-primary mr-3 shadow-sm" style="background-color: rgba(0, 123, 255, 0.12);">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div>
                                        <span class="text-muted text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Dana Cair</span>
                                        <h5 class="fw-bold mb-0 text-primary" id="kpi_nominal_sukses">Rp 0</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BARIS DUA DONUT CHART: STATUS PENYALURAN & PROPORSI JENIS BANSOS -->
                    <div class="row g-3">
                        <!-- Donut 1: Status Realisasi Penyaluran -->
                        <div class="col-12 col-lg-6">
                            <div class="card border shadow-none rounded-3 h-100">
                                <div class="card-header bg-light py-2 border-bottom">
                                    <h6 class="m-0 fw-bold small text-dark">
                                        <i class="fas fa-chart-pie mr-1 text-primary"></i> Proporsi Status Penyaluran
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <div id="chartDonutStatus" class="chart-container-box"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Donut 2: Komposisi Jenis Bansos -->
                        <div class="col-12 col-lg-6">
                            <div class="card border shadow-none rounded-3 h-100">
                                <div class="card-header bg-light py-2 border-bottom">
                                    <h6 class="m-0 fw-bold small text-dark">
                                        <i class="fas fa-layer-group mr-1 text-info"></i> Komposisi Jenis Bansos
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <div id="chartDonutJenis" class="chart-container-box"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ======================================================= -->
            <!-- 📋 SECTION CARD BAWAH: TABEL BNBA -->
            <!-- ======================================================= -->
            <div class="card shadow-sm border mb-4 rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <h6 class="m-0 fw-bold text-dark d-flex align-items-center mb-0">
                                <i class="fas fa-table text-primary mr-2"></i> Rekapitulasi Data BNBA (By Name By Address)
                            </h6>
                            <small class="text-muted">Data penerima bantuan sosial terinci berdasarkan kriteria filter</small>
                        </div>
                        <div class="card-tools ms-auto ml-auto text-end" style="margin-left: auto !important;">
                            <button id="btn_export_excel" type="button" class="btn btn-sm btn-success rounded-pill px-4 shadow-sm btn-export-excel">
                                <i class="fas fa-file-excel mr-1"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table id="tableBnba" class="table table-hover table-bordered table-striped w-100 table-bnba">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 40px;">No</th>
                                    <th>NIK KPM</th>
                                    <th>Nama KPM</th>
                                    <th>No. KK</th>
                                    <th>Alamat / Wilayah</th>
                                    <th>No. KKS</th>
                                    <th class="text-center">Jenis Bansos</th>
                                    <th class="text-center">Tahap</th>
                                    <th class="text-end">Nominal (Rp)</th>
                                    <th class="text-center">Status</th>
                                    <th>Petugas Entri</th>
                                    <th class="text-center" style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- ======================================================= -->
<!-- 👁️ MODAL DETAIL BNBA -->
<!-- ======================================================= -->
<div class="modal fade" id="modalDetailBnba" tabindex="-1" aria-labelledby="modalDetailBnbaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="modalDetailBnbaLabel">
                    <i class="fas fa-id-card mr-2"></i> Detail Penerima Bantuan Sosial (BNBA)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="row g-3">
                    <!-- Sisi Kiri: Foto & Koordinat -->
                    <div class="col-12 col-md-5">
                        <!-- Switcher Program jika memiliki > 1 Bansos (1 KPM 2 Bansos) -->
                        <div id="containerProgramSelector" class="card border border-primary bg-light shadow-none mb-3" style="display: none;">
                            <div class="card-body p-2 text-center">
                                <label class="small fw-bold text-dark mb-1 d-block">
                                    <i class="fas fa-layer-group text-purple mr-1"></i> Program Bansos (Klik untuk foto):
                                </label>
                                <div class="btn-group w-100" role="group" id="btnGroupProgram"></div>
                            </div>
                        </div>

                        <div class="card border shadow-none mb-3">
                            <div class="card-header bg-light py-2 text-center d-flex justify-content-between align-items-center">
                                <span class="small fw-bold text-muted" id="labelFotoKpm">Foto KPM & KKS</span>
                                <span class="badge badge-light border text-muted" style="font-size: 0.7rem;">Portrait 3:4</span>
                            </div>
                            <div class="card-body p-2 text-center">
                                <a id="linkFotoKpm" href="#" target="_blank" title="Klik untuk memperbesar">
                                    <img id="imgFotoKpm" src="<?= base_url('assets/img/no-image.svg'); ?>" alt="Foto KPM" class="preview-foto-modal">
                                </a>
                            </div>
                        </div>

                        <div class="card border shadow-none mb-3">
                            <div class="card-header bg-light py-2 text-center d-flex justify-content-between align-items-center">
                                <span class="small fw-bold text-muted" id="labelFotoStruk">Foto Struk / Bukti Transaksi</span>
                                <span class="badge badge-light border text-muted" style="font-size: 0.7rem;">Portrait 3:4</span>
                            </div>
                            <div class="card-body p-2 text-center">
                                <a id="linkFotoStruk" href="#" target="_blank" title="Klik untuk memperbesar">
                                    <img id="imgFotoStruk" src="<?= base_url('assets/img/no-image.svg'); ?>" alt="Foto Struk" class="preview-foto-modal">
                                </a>
                            </div>
                        </div>

                        <!-- Geotagging Peta -->
                        <div id="boxLokasi" class="card border shadow-none mb-0">
                            <div class="card-body p-3 text-center">
                                <span class="small fw-bold text-muted d-block mb-1">Geotagging Lokasi Penyaluran</span>
                                <p class="small font-monospace mb-2" id="txtKoordinat">-</p>
                                <a id="btnBukaMaps" href="#" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-none w-100">
                                    <i class="fas fa-map-marked-alt mr-1"></i> Buka Google Maps
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Detail Informasi -->
                    <div class="col-12 col-md-7">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped border">
                                <tbody>
                                    <tr>
                                        <th style="width: 40%;">Nama Lengkap KPM</th>
                                        <td id="detail_nama_kpm" class="fw-bold text-primary">-</td>
                                    </tr>
                                    <tr>
                                        <th>NIK KPM</th>
                                        <td id="detail_nik_kpm" class="font-monospace">-</td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Kartu Keluarga</th>
                                        <td id="detail_no_kk" class="font-monospace">-</td>
                                    </tr>
                                    <tr>
                                        <th>Kepala Keluarga</th>
                                        <td id="detail_kepala_keluarga">-</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat Domisili</th>
                                        <td id="detail_alamat">-</td>
                                    </tr>
                                    <tr>
                                        <th>Wilayah RT / RW</th>
                                        <td id="detail_rt_rw">-</td>
                                    </tr>
                                    <tr>
                                        <th>Nomor KKS</th>
                                        <td id="detail_no_kks" class="font-monospace text-primary fw-bold">-</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Bantuan</th>
                                        <td>
                                            <div id="detail_jenis_bansos">-</div>
                                            <div id="boxRincianKomponen" class="mt-2" style="display: none;">
                                                <table class="table table-sm table-bordered mb-0 bg-white small">
                                                    <thead class="bg-light text-muted">
                                                        <tr>
                                                            <th class="py-1">Program</th>
                                                            <th class="py-1 text-end">Nominal</th>
                                                            <th class="py-1 text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbodyRincianKomponen"></tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tahap Salur</th>
                                        <td id="detail_tahap_salur">-</td>
                                    </tr>
                                    <tr>
                                        <th>Total Nominal Cair</th>
                                        <td id="detail_nominal_cair" class="fw-bold text-success">-</td>
                                    </tr>
                                    <tr>
                                        <th>Status Penyaluran</th>
                                        <td id="detail_status_salur">-</td>
                                    </tr>
                                    <tr>
                                        <th>Petugas Entri</th>
                                        <td id="detail_petugas">-</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Perekaman</th>
                                        <td id="detail_created_at" class="small text-muted">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- 📜 SCRIPTS: HIGHCHARTS & DATATABLES -->
<!-- ======================================================= -->
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
$(document).ready(function() {

    // Simpan instance chart secara global agar bisa di-destroy/update
    let chartStatusInstance = null;
    let chartJenisInstance = null;

    // =======================================================
    // 1️⃣ INISIALISASI DATATABLE BNBA (SERVER-SIDE)
    // =======================================================
    const tableBnba = $('#tableBnba').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Memuat data...',
            search: "Cari BNBA:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Tidak ada data BNBA yang cocok dengan kriteria filter",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "»",
                previous: "«"
            }
        },
        ajax: {
            url: "<?= base_url('bansos/datatable-bnba'); ?>",
            type: "POST",
            data: function(d) {
                d.filter_petugas = $('#filter_petugas').val();
                d.filter_rw      = $('#filter_rw').val();
                d.filter_rt      = $('#filter_rt').val();
                d.filter_tahap   = $('#filter_tahap').val();
                d.filter_jenis   = $('#filter_jenis').val();
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
            }
        },
        columnDefs: [
            { targets: [0, 6, 7, 9, 11], className: "text-center" },
            { targets: [8], className: "text-end" },
            { orderable: false, targets: [0, 11] }
        ]
    });

    // =======================================================
    // 2️⃣ FUNGSI LOAD DATA DIAGRAM DONUT & STATISTIK KPI
    // =======================================================
    function loadChartData() {
        const pPetugas = $('#filter_petugas').val();
        const pRw      = $('#filter_rw').val();
        const pRt      = $('#filter_rt').val();
        const pTahap   = $('#filter_tahap').val();
        const pJenis   = $('#filter_jenis').val();

        // Update Label Filter Aktif di Header
        let labelFilter = pJenis ? pJenis : 'Semua Bansos';
        if (pTahap) labelFilter += ' (' + pTahap + ')';
        if (pRw) labelFilter += ' - RW ' + pRw;
        $('#labelActiveFilter').text(labelFilter);

        $.ajax({
            url: "<?= base_url('bansos/chart-data'); ?>",
            type: "POST",
            data: {
                filter_petugas: pPetugas,
                filter_rw: pRw,
                filter_rt: pRt,
                filter_tahap: pTahap,
                filter_jenis: pJenis,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: "JSON",
            success: function(res) {
                if (res.status) {
                    // Update KPI Cards
                    $('#kpi_total_kpm').text(Number(res.total_kpm).toLocaleString('id-ID'));
                    $('#kpi_sukses_salur').text(Number(res.sukses_salur).toLocaleString('id-ID'));
                    $('#kpi_persen_sukses').text('(' + res.persen_sukses + '%)');
                    $('#kpi_belum_salur').text(Number(res.belum_salur).toLocaleString('id-ID'));
                    $('#kpi_persen_belum').text('(' + res.persen_belum + '%)');
                    $('#kpi_gagal_salur').text(Number(res.gagal_salur).toLocaleString('id-ID'));
                    $('#kpi_persen_gagal').text('(' + res.persen_gagal + '%)');
                    $('#kpi_nominal_sukses').text(res.nominal_sukses);

                    // Render Donut 1: Status Penyaluran
                    renderDonutStatus(res.donut_status, res.total_kpm);

                    // Render Donut 2: Komposisi Jenis Bansos
                    renderDonutJenis(res.donut_jenis, res.total_kpm);
                }
            },
            error: function() {
                console.error("Gagal memuat statistik chart bansos.");
            }
        });
    }

    // Helper Render Donut Chart Status
    function renderDonutStatus(seriesData, total) {
        if (chartStatusInstance) {
            chartStatusInstance.destroy();
        }

        if (total === 0) {
            $('#chartDonutStatus').html('<div class="d-flex h-100 justify-content-center align-items-center text-muted fw-bold"><i class="fas fa-box-open mr-2"></i> Tidak ada data untuk kriteria filter ini.</div>');
            return;
        }

        chartStatusInstance = Highcharts.chart('chartDonutStatus', {
            chart: {
                type: 'pie',
                backgroundColor: 'transparent',
                height: 320
            },
            title: {
                text: '<span style="font-size:13px; font-weight:bold;">Total KPM</span><br><span style="font-size:18px; font-weight:800; color:#007bff;">' + Number(total).toLocaleString('id-ID') + '</span>',
                align: 'center',
                verticalAlign: 'middle',
                y: 10,
                style: { textAlign: 'center' }
            },
            tooltip: {
                pointFormat: '<b>{point.y} KPM</b> ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    innerSize: '65%',
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br>{point.y} ({point.percentage:.1f}%)',
                        distance: 15,
                        style: {
                            fontSize: '11px',
                            fontWeight: 'normal',
                            textOutline: 'none'
                        }
                    },
                    showInLegend: true
                }
            },
            legend: {
                align: 'center',
                verticalAlign: 'bottom',
                layout: 'horizontal'
            },
            series: [{
                name: 'Jumlah KPM',
                data: seriesData
            }],
            credits: { enabled: false }
        });
    }

    // Helper Render Donut Chart Jenis
    function renderDonutJenis(seriesData, total) {
        if (chartJenisInstance) {
            chartJenisInstance.destroy();
        }

        if (total === 0) {
            $('#chartDonutJenis').html('<div class="d-flex h-100 justify-content-center align-items-center text-muted fw-bold"><i class="fas fa-box-open mr-2"></i> Tidak ada data untuk kriteria filter ini.</div>');
            return;
        }

        chartJenisInstance = Highcharts.chart('chartDonutJenis', {
            chart: {
                type: 'pie',
                backgroundColor: 'transparent',
                height: 320
            },
            title: {
                text: '<span style="font-size:13px; font-weight:bold;">Bantuan</span><br><span style="font-size:18px; font-weight:800; color:#28a745;">' + Number(total).toLocaleString('id-ID') + '</span>',
                align: 'center',
                verticalAlign: 'middle',
                y: 10,
                style: { textAlign: 'center' }
            },
            tooltip: {
                pointFormat: '<b>{point.y} KPM</b> ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    innerSize: '65%',
                    allowPointSelect: true,
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                if (this.name) {
                                    $('#filter_jenis').val(this.name).trigger('change');
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br>{point.y} ({point.percentage:.1f}%)',
                        distance: 15,
                        style: {
                            fontSize: '11px',
                            fontWeight: 'normal',
                            textOutline: 'none'
                        }
                    },
                    showInLegend: true
                }
            },
            legend: {
                align: 'center',
                verticalAlign: 'bottom',
                layout: 'horizontal'
            },
            series: [{
                name: 'Jumlah KPM',
                data: seriesData
            }],
            credits: { enabled: false }
        });
    }

    // =======================================================
    // 3️⃣ CASCADING DROPDOWN RW -> RT
    // =======================================================
    // Load RW saat pertama kali halaman terbuka
    $.ajax({
        url: "<?= base_url('bansos/get-rw'); ?>",
        type: "GET",
        dataType: "JSON",
        success: function(data) {
            $('#filter_rw').empty().append('<option value="">Semua RW</option>');
            $.each(data, function(idx, val) {
                $('#filter_rw').append('<option value="' + val + '">RW ' + val + '</option>');
            });
        }
    });

    // Event saat RW berubah -> ambil RT
    $('#filter_rw').on('change', function() {
        const rwSelected = $(this).val();
        $('#filter_rt').empty().append('<option value="">Semua RT</option>');

        if (rwSelected !== '') {
            $.ajax({
                url: "<?= base_url('bansos/get-rt'); ?>",
                type: "GET",
                data: { rw: rwSelected },
                dataType: "JSON",
                success: function(data) {
                    $.each(data, function(idx, val) {
                        $('#filter_rt').append('<option value="' + val + '">RT ' + val + '</option>');
                    });
                }
            });
        }
    });

    // =======================================================
    // 4️⃣ EVENT TRIGGER FILTER & RESET
    // =======================================================
    // Trigger saat tombol filter diklik atau dropdown berubah
    $('#btn_filter').on('click', function() {
        tableBnba.ajax.reload();
        loadChartData();
    });

    $('#filter_petugas, #filter_rw, #filter_rt, #filter_tahap, #filter_jenis').on('change', function() {
        tableBnba.ajax.reload();
        loadChartData();
    });

    // Reset filter
    $('#btn_reset').on('click', function() {
        if (!$('#filter_petugas').prop('disabled')) {
            $('#filter_petugas').val('');
        }
        $('#filter_rw').val('');
        $('#filter_rt').empty().append('<option value="">Semua RT</option>');
        $('#filter_tahap').val('');
        $('#filter_jenis').val('');

        tableBnba.ajax.reload();
        loadChartData();
    });

    // =======================================================
    // 5️⃣ EXPORT EXCEL MENGIKUTI FILTER
    // =======================================================
    $(document).on('click', '#btn_export_excel, .btn-export-excel', function() {
        const petugas = $('#filter_petugas').val();
        const rw      = $('#filter_rw').val();
        const rt      = $('#filter_rt').val();
        const tahap   = $('#filter_tahap').val();
        const jenis   = $('#filter_jenis').val();

        const exportUrl = "<?= base_url('bansos/export-excel'); ?>?petugas=" + encodeURIComponent(petugas) +
                          "&rw=" + encodeURIComponent(rw) +
                          "&rt=" + encodeURIComponent(rt) +
                          "&tahap=" + encodeURIComponent(tahap) +
                          "&jenis=" + encodeURIComponent(jenis);
        window.open(exportUrl, '_blank');
    });

    // =======================================================
    // 6️⃣ MODAL DETAIL BNBA
    // =======================================================
    $(document).on('click', '.btn-detail-bnba', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Memuat Detail...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "<?= base_url('bansos/detail-bnba'); ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(res) {
                Swal.close();
                if (res.status && res.data) {
                    const d = res.data;
                    const items = d.items || [];

                    $('#detail_nama_kpm').text(d.nama_kpm ? d.nama_kpm.toUpperCase() : '-');
                    $('#detail_nik_kpm').text(d.nik_kpm || '-');
                    $('#detail_no_kk').text(d.no_kk || '-');
                    $('#detail_kepala_keluarga').text(d.kepala_keluarga || '-');
                    $('#detail_alamat').text(d.alamat || '-');
                    $('#detail_rt_rw').text('RT ' + (d.rt_formatted || '-') + ' / RW ' + (d.rw_formatted || '-'));
                    $('#detail_no_kks').text(d.nomor_kks || '-');
                    $('#detail_tahap_salur').text(d.tahap_salur || '-');
                    $('#detail_nominal_cair').text(d.total_nominal_formatted || d.nominal_formatted || 'Rp 0');
                    $('#detail_petugas').text(d.petugas_nama || 'Admin / Sistem');
                    $('#detail_created_at').text(d.created_at || d.updated_at || '-');

                    // Jenis Bansos Badge Modal (bersih tanpa embel-embel sub-label)
                    let badgeJenisHtml = '';
                    const katJenis = d.jenis_bansos_kategori || d.jenis_bansos || '';
                    if (katJenis === 'PKH + SEMBAKO') {
                        badgeJenisHtml = '<span class="badge badge-purple px-2 py-1" style="background-color: #6f42c1; color: white;"><i class="fas fa-layer-group mr-1"></i> PKH + SEMBAKO</span>';
                    } else if (katJenis === 'PKH') {
                        badgeJenisHtml = '<span class="badge badge-primary px-2 py-1"><i class="fas fa-hand-holding-heart mr-1"></i> PKH</span>';
                    } else {
                        badgeJenisHtml = '<span class="badge badge-info px-2 py-1" style="background-color: #20c997; color: white;"><i class="fas fa-box-open mr-1"></i> SEMBAKO</span>';
                    }
                    $('#detail_jenis_bansos').html(badgeJenisHtml);

                    // Helper render foto dan rincian program tertentu (berorientasi portrait 3:4)
                    function renderItemDetail(item) {
                        $('#labelFotoKpm').text('Foto KPM & KKS (' + item.jenis_bansos + ')');
                        $('#labelFotoStruk').text('Foto Struk (' + item.jenis_bansos + ')');

                        $('#imgFotoKpm').attr('src', item.foto_kpm_url);
                        $('#linkFotoKpm').attr('href', item.foto_kpm_url);

                        $('#imgFotoStruk').attr('src', item.foto_struk_url);
                        $('#linkFotoStruk').attr('href', item.foto_struk_url);

                        // Geotagging
                        if (item.maps_url && item.latitude && item.longitude) {
                            $('#boxLokasi').show();
                            $('#txtKoordinat').text(item.latitude + ', ' + item.longitude);
                            $('#btnBukaMaps').attr('href', item.maps_url);
                        } else {
                            $('#boxLokasi').hide();
                        }

                        // Status per-item
                        const statusLower = (item.status_salur || '').toLowerCase();
                        let badgeStatus = '<span class="badge badge-warning text-dark">Belum Transaksi</span>';
                        if (statusLower === 'sukses salur') {
                            badgeStatus = '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Sukses Salur</span>';
                        } else if (item.status_salur) {
                            badgeStatus = '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> ' + item.status_salur + '</span>';
                        }
                        $('#detail_status_salur').html(badgeStatus);
                    }

                    // Tangani tampilan foto & rincian: 1 KPM 1 Bansos vs 1 KPM 2 Bansos
                    if (items.length > 1) {
                        $('#containerProgramSelector').show();
                        $('#btnGroupProgram').empty();
                        $('#boxRincianKomponen').show();
                        $('#tbodyRincianKomponen').empty();

                        items.forEach(function(it, idx) {
                            const activeClass = (idx === 0) ? 'btn-primary active text-white' : 'btn-outline-primary';
                            const btn = $('<button type="button" class="btn btn-sm btn-program-tab ' + activeClass + ' py-1 px-3 fw-bold"></button>')
                                .html('<i class="fas fa-layer-group mr-1"></i> ' + it.jenis_bansos)
                                .data('item-index', idx);
                            $('#btnGroupProgram').append(btn);

                            // Tambahkan baris di tabel rincian
                            const itStLow = (it.status_salur || '').toLowerCase();
                            let itStBadge = '<span class="badge badge-warning text-dark">Belum Transaksi</span>';
                            if (itStLow === 'sukses salur') {
                                itStBadge = '<span class="badge badge-success">Sukses Salur</span>';
                            } else if (it.status_salur) {
                                itStBadge = '<span class="badge badge-danger">' + it.status_salur + '</span>';
                            }

                            const tr = '<tr>' +
                                '<td class="py-1 fw-bold">' + it.jenis_bansos + '</td>' +
                                '<td class="py-1 text-end text-success fw-bold">' + it.nominal_formatted + '</td>' +
                                '<td class="py-1 text-center">' + itStBadge + '</td>' +
                            '</tr>';
                            $('#tbodyRincianKomponen').append(tr);
                        });

                        // Default tampilkan item pertama
                        renderItemDetail(items[0]);

                        // Event klik switcher program bansos
                        $('.btn-program-tab').off('click').on('click', function() {
                            $('.btn-program-tab').removeClass('btn-primary active text-white').addClass('btn-outline-primary');
                            $(this).removeClass('btn-outline-primary').addClass('btn-primary active text-white');
                            const selIdx = $(this).data('item-index');
                            renderItemDetail(items[selIdx]);
                        });
                    } else if (items.length === 1) {
                        $('#containerProgramSelector').hide();
                        $('#boxRincianKomponen').hide();
                        renderItemDetail(items[0]);
                    } else {
                        $('#containerProgramSelector').hide();
                        $('#boxRincianKomponen').hide();
                        $('#detail_status_salur').html('<span class="badge badge-warning text-dark">Belum Transaksi</span>');
                    }

                    $('#modalDetailBnba').modal('show');
                } else {
                    Swal.fire('Error', res.message || 'Gagal mengambil data detail.', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem saat mengambil data detail.', 'error');
            }
        });
    });

    // =======================================================
    // 7️⃣ COPY CLIPBOARD HANDLERS
    // =======================================================
    $(document).on('click', '.btnCopyNik', function() {
        const val = $(this).data('value');
        if (!val) return;
        navigator.clipboard.writeText(val).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'NIK Disalin!',
                text: `${val} berhasil disalin ke clipboard`,
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    });

    $(document).on('click', '.btnCopyNoKK', function() {
        const val = $(this).data('value');
        if (!val) return;
        navigator.clipboard.writeText(val).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Nomor Disalin!',
                text: `${val} berhasil disalin ke clipboard`,
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    });

    // =======================================================
    // 🚀 INITIAL LOAD
    // =======================================================
    loadChartData();
});
</script>

<?= $this->endSection(); ?>

