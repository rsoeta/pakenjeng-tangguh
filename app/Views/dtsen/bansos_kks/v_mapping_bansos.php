<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<!-- 🌍 LEAFLET & MARKERCLUSTER CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<style>
    /* Styling KPI Stat Cards */
    .stat-card-widget {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    .stat-card-widget:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    }

    .stat-icon-circle {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
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

    .card-header::after {
        display: none !important;
    }

    /* 🗺️ Peta Satelit Container */
    #mapBansos {
        width: 100%;
        height: 620px;
        border-radius: 0 0 12px 12px;
        z-index: 1;
        background-color: #1a232c;
    }

    @media (max-width: 768px) {
        #mapBansos {
            height: 480px;
        }
    }

    /* 📍 Custom Pin Marker (DivIcon) */
    .custom-map-pin {
        position: relative;
        width: 34px;
        height: 42px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        filter: drop-shadow(0 3px 5px rgba(0, 0, 0, 0.45));
        transition: transform 0.15s ease;
    }

    .custom-map-pin:hover {
        transform: scale(1.15) translateY(-3px);
        z-index: 999 !important;
    }

    .pin-Tear {
        width: 32px;
        height: 32px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2.5px solid #ffffff;
        box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.2);
    }

    .pin-Tear i {
        transform: rotate(45deg);
        color: #ffffff;
        font-size: 13px;
    }

    .pin-source-dot {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        border: 1.5px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 7px;
        color: #fff;
        z-index: 2;
    }

    /* 🎨 Custom Cluster Styling agar kontras di atas peta satelit */
    .marker-cluster-small,
    .marker-cluster-medium,
    .marker-cluster-large {
        background-color: rgba(255, 255, 255, 0.85) !important;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.45);
    }

    .marker-cluster-small div {
        background-color: #007bff !important;
        color: #fff !important;
        font-weight: 800;
        font-size: 13px;
    }

    .marker-cluster-medium div {
        background-color: #6f42c1 !important;
        color: #fff !important;
        font-weight: 800;
        font-size: 13px;
    }

    .marker-cluster-large div {
        background-color: #dc3545 !important;
        color: #fff !important;
        font-weight: 800;
        font-size: 14px;
    }

    /* 🃏 Popup Card Styling */
    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        padding: 0 !important;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3) !important;
    }

    .leaflet-popup-content {
        margin: 0 !important;
        width: 290px !important;
    }

    .popup-kpm-header {
        padding: 10px 14px;
        color: #fff;
        font-weight: 700;
        font-size: 0.88rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .popup-kpm-body {
        padding: 12px 14px;
        background: #fff;
        font-size: 0.8rem;
    }

    .popup-thumb-34 {
        width: 72px;
        aspect-ratio: 3 / 4;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
        flex-shrink: 0;
    }

    /* 🧭 Floating Legend Box di atas Peta */
    .map-legend-overlay {
        position: absolute;
        bottom: 24px;
        left: 14px;
        z-index: 800;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(4px);
        border-radius: 10px;
        padding: 10px 14px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(0, 0, 0, 0.1);
        font-size: 0.78rem;
        max-width: 240px;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 7px;
        border: 1.5px solid #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .legend-boundary-line {
        width: 24px;
        height: 5px;
        border-radius: 3px;
        display: inline-block;
        margin-right: 7px;
        background: repeating-linear-gradient(90deg,
                #ea4335 0px,
                #ea4335 4px,
                #ffffff 4px,
                #ffffff 8px);
        border: 1px solid #ea4335;
        box-shadow: 0 1px 3px rgba(234, 67, 53, 0.45);
    }

    /* Foto Modal Portrait 3:4 */
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
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 d-flex justify-content-between align-items-center">
                <div class="col-auto">
                    <h4 class="m-0 fw-bold fs-5 text-dark">
                        <i class="fas fa-map-marked-alt text-primary mr-2"></i> <?= esc($title); ?>
                    </h4>
                    <p class="text-muted small mb-0 d-none d-sm-block">
                        Pemetaan spasial penerima bantuan sosial (PKH, Sembako, PKH + Sembako) di atas citra satelit desa
                    </p>
                </div>
                <div class="col-auto">
                    <ol class="breadcrumb bg-transparent p-0 m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('bansos/rekapitulasi'); ?>">Bansos</a></li>
                        <li class="breadcrumb-item active">Mapping</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <!-- ======================================================= -->
            <!-- 🔍 SECTION 1: FILTER & PENCARIAN TITIK KPM -->
            <!-- ======================================================= -->
            <div class="card shadow-sm border mb-3 rounded-3">
                <div class="card-header bg-white py-2.5 border-bottom">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-filter text-primary mr-2"></i> Filter Sebaran Peta Bansos
                        </h6>
                        <span class="badge badge-light border text-muted small px-3 py-1" id="labelActiveFilter">
                            Semua Bansos
                        </span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row align-items-end g-2">
                        <!-- 1. Filter Petugas Entri -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
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

                        <!-- 2. Wilayah RW -->
                        <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                            <label class="filter-label">Wilayah RW</label>
                            <select id="filter_rw" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                                <option value="">Semua RW</option>
                            </select>
                        </div>

                        <!-- 3. Wilayah RT -->
                        <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                            <label class="filter-label">Wilayah RT</label>
                            <select id="filter_rt" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                                <option value="">Semua RT</option>
                            </select>
                        </div>

                        <!-- 4. Tahap Salur -->
                        <div class="col-6 col-sm-6 col-md-4 col-lg-2">
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

                        <!-- 5. Jenis Bansos -->
                        <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                            <label class="filter-label">Jenis Bansos</label>
                            <select id="filter_jenis" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                                <option value="">Semua</option>
                                <option value="PKH">PKH</option>
                                <option value="SEMBAKO">Sembako</option>
                                <option value="PKH + SEMBAKO">PKH + Sembako</option>
                            </select>
                        </div>

                        <!-- 6. Status Salur -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <label class="filter-label">Status Penyaluran</label>
                            <select id="filter_status" class="form-control form-control-sm rounded-pill px-3 shadow-none border">
                                <option value="">Semua Status</option>
                                <option value="sukses">Sukses Salur</option>
                                <option value="belum">Belum Transaksi / Draft</option>
                                <option value="gagal">Gagal / Kendala</option>
                            </select>
                        </div>
                    </div>

                    <!-- BARIS 2: PENCARIAN CEPAT KPM DI PETA & TOMBOL AKSI -->
                    <div class="row mt-3 align-items-center g-2">
                        <div class="col-12 col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border rounded-pill-start px-3">
                                    <i class="fas fa-search-location text-primary mr-1"></i> Cari Rumah KPM:
                                </span>
                                <input type="text" id="inputCariKpmPeta" class="form-control border" placeholder="Ketik Nama KPM atau NIK untuk terbang ke titik rumah..." list="datalistKpmMapped">
                                <datalist id="datalistKpmMapped"></datalist>
                                <button class="btn btn-outline-primary px-3" type="button" id="btnFlyToKpm" title="Lompat ke Titik Rumah">
                                    <i class="fas fa-crosshairs mr-1"></i> Temukan
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 d-flex justify-content-end align-items-center flex-wrap" style="gap: 8px;">
                            <button id="btn_fit_bounds" type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm" title="Tampilkan Seluruh Titik Desa">
                                <i class="fas fa-expand-arrows-alt mr-1"></i> Fokus Sebaran Desa
                            </button>
                            <button id="btn_reset" type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm" title="Reset Filter">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                            <button id="btn_filter" type="button" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-sync-alt mr-1"></i> Muat Peta
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================= -->
            <!-- 📊 SECTION 2: MINI-KPI CAKUPAN KOORDINAT & KOMPOSISI -->
            <!-- ======================================================= -->
            <div class="row g-2 mb-3">
                <!-- Total KPM -->
                <div class="col-6 col-lg-3">
                    <div class="card stat-card-widget shadow-sm p-3 bg-white h-100">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon-circle bg-light text-primary mr-3 shadow-sm">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <span class="text-muted text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Total KPM</span>
                                <h4 class="fw-bold mb-0 text-dark" id="map_kpi_total">0</h4>
                                <small class="text-muted" style="font-size: 0.72rem;">1 KPM / KK per Tahap</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sudah Terpetakan (Ada Koordinat) -->
                <div class="col-6 col-lg-3">
                    <div class="card stat-card-widget shadow-sm p-3 bg-white h-100 border-left border-success" style="border-left-width: 4px !important;">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon-circle text-success mr-2 mr-sm-3 shadow-sm d-none d-sm-flex" style="background-color: rgba(40, 167, 69, 0.12);">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <span class="text-muted text-uppercase d-block text-truncate" style="font-size: 0.7rem; font-weight: 700;">Terpetakan di Satelit</span>
                                <div class="d-flex align-items-baseline flex-wrap" style="gap: 4px;">
                                    <h4 class="fw-bold mb-0 text-success" id="map_kpi_mapped">0</h4>
                                    <small class="text-muted fw-bold" id="map_kpi_persen_mapped">(0%)</small>
                                </div>
                                <div class="text-muted d-flex flex-wrap align-items-center" style="font-size: 0.7rem; gap: 4px;">
                                    <span title="Berdasarkan titik lokasi rumah warga"><i class="fas fa-home text-primary"></i> Rumah: <b id="map_kpi_src_rumah">0</b></span>
                                    <span class="d-none d-sm-inline">|</span>
                                    <span title="Berdasarkan titik lokasi saat dokumentasi penyaluran"><i class="fas fa-camera text-info"></i> Salur: <b id="map_kpi_src_bansos">0</b></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Belum Ada Koordinat -->
                <div class="col-6 col-lg-3">
                    <div class="card stat-card-widget shadow-sm p-3 bg-white h-100 border-left border-warning" style="border-left-width: 4px !important; cursor: pointer;" id="cardScrollUnmapped" title="Klik untuk melihat daftar warga yang belum memiliki titik lokasi">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon-circle text-warning mr-2 mr-sm-3 shadow-sm d-none d-sm-flex" style="background-color: rgba(255, 193, 7, 0.15);">
                                <i class="fas fa-map-marker-slash"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <span class="text-muted text-uppercase d-block text-truncate" style="font-size: 0.7rem; font-weight: 700;">Belum Terpetakan</span>
                                <h4 class="fw-bold mb-0 text-warning" id="map_kpi_unmapped">0</h4>
                                <small class="text-primary fw-semibold d-block text-truncate" style="font-size: 0.72rem;"><i class="fas fa-arrow-down mr-1"></i> Lihat Daftar Warga</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Komposisi Jenis Bansos -->
                <div class="col-6 col-lg-3">
                    <div class="card stat-card-widget shadow-sm p-3 bg-white h-100 border-left border-primary" style="border-left-width: 4px !important;">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon-circle text-purple mr-2 mr-sm-3 shadow-sm d-none d-sm-flex" style="background-color: rgba(111, 66, 193, 0.12); color: #6f42c1;">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <span class="text-muted text-uppercase d-block mb-1 text-truncate" style="font-size: 0.7rem; font-weight: 700;">Sebaran Program</span>
                                <div class="d-flex flex-wrap align-items-center" style="gap: 4px;">
                                    <span class="badge badge-primary px-2 py-1" style="font-size: 0.72rem;" title="PKH Murni">PKH: <b id="map_kpi_pkh">0</b></span>
                                    <span class="badge px-2 py-1" style="background-color:#20c997; color:#fff; font-size: 0.72rem;" title="Sembako Murni">Sembako: <b id="map_kpi_sembako">0</b></span>
                                    <span class="badge px-2 py-1" style="background-color:#6f42c1; color:#fff; font-size: 0.72rem;" title="PKH + Sembako">Kombo: <b id="map_kpi_kombo">0</b></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================= -->
            <!-- 🛰️ SECTION 3: PETA SATELIT DESA & SAKLAR WARNA PIN -->
            <!-- ======================================================= -->
            <div class="card shadow-sm border mb-4 rounded-3 overflow-hidden">
                <div class="card-header bg-white py-2.5 border-bottom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center w-100" style="gap: 10px;">
                        <div>
                            <h6 class="m-0 fw-bold text-dark d-flex align-items-center mb-0">
                                <i class="fas fa-satellite text-primary mr-2"></i> Peta Satelit Sebaran Penerima Bansos — Desa <?= esc(ucwords(strtolower($nama_desa ?? 'Pasirlangu'))); ?>
                            </h6>
                            <small class="text-muted">Menampilkan titik lokasi rumah warga penerima bantuan serta lokasi dokumentasi penyaluran di lapangan</small>
                        </div>

                        <!-- Saklar Batas Desa & Mode Warna Pin -->
                        <div class="d-flex flex-wrap align-items-center ms-auto ml-auto" style="gap: 8px; margin-left: auto !important;">
                            <button type="button" id="btnToggleBatasDesa" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold shadow-sm" title="Tampilkan / Sembunyikan Garis Batas Wilayah Desa">
                                <i class="fas fa-draw-polygon mr-1"></i> Batas Desa: ON
                            </button>

                            <span class="small fw-bold text-muted d-none d-sm-inline ml-1"><i class="fas fa-palette mr-1"></i> Warna Pin:</span>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Mode Warna Pin">
                                <button type="button" class="btn btn-primary active fw-bold px-3 mode-color-btn" data-mode="jenis">
                                    <i class="fas fa-layer-group mr-1"></i> Jenis Bansos
                                </button>
                                <button type="button" class="btn btn-outline-primary fw-bold px-3 mode-color-btn" data-mode="status">
                                    <i class="fas fa-check-double mr-1"></i> Status Salur
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 position-relative">
                    <!-- Elemen Peta Leaflet -->
                    <div id="mapBansos"></div>

                    <!-- Legenda Mengambang (Floating Legend) -->
                    <div class="map-legend-overlay" id="mapLegendBox">
                        <div class="fw-bold text-dark mb-1.5 border-bottom pb-1" id="legendTitle">
                            <i class="fas fa-info-circle text-primary mr-1"></i> Legenda Jenis Bansos
                        </div>
                        <div id="legendContent">
                            <div class="d-flex align-items-center mb-1">
                                <span class="legend-dot" style="background-color: #007bff;"></span>
                                <span><b>PKH</b> (Program Keluarga Harapan)</span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="legend-dot" style="background-color: #20c997;"></span>
                                <span><b>SEMBAKO</b> (BPNT)</span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <span class="legend-dot" style="background-color: #6f42c1;"></span>
                                <span><b>PKH + SEMBAKO</b> (2 Bantuan)</span>
                            </div>
                        </div>
                        <div class="border-top pt-1 mt-1 text-muted" style="font-size: 0.7rem;">
                            <div class="d-flex align-items-center mb-1 text-dark fw-semibold">
                                <span class="legend-boundary-line"></span>
                                <span>Batas Wilayah Desa <?= esc(ucwords(strtolower($nama_desa ?? 'Pasirlangu'))); ?></span>
                            </div>
                            <div><i class="fas fa-home text-primary mr-1"></i> Ikon Rumah = Lokasi Rumah Warga</div>
                            <div><i class="fas fa-camera text-info mr-1"></i> Ikon Kamera = Lokasi Dokumentasi Salur</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================= -->
            <!-- 📋 SECTION 4: DAFTAR KPM BELUM MEMILIKI KOORDINAT -->
            <!-- ======================================================= -->
            <div class="card shadow-sm border mb-4 rounded-3" id="sectionUnmappedKpm">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <h6 class="m-0 fw-bold text-dark d-flex align-items-center mb-0">
                                <i class="fas fa-exclamation-triangle text-warning mr-2"></i> Daftar KPM Belum Memiliki Titik Lokasi (<span id="badgeCountUnmapped">0</span> KPM)
                            </h6>
                            <small class="text-muted">Daftar keluarga penerima manfaat yang titik lokasi rumahnya maupun foto dokumentasi penyalurannya belum tercatat di peta</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 ms-auto ml-auto" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnmappedTable" aria-expanded="true" style="margin-left: auto !important;">
                            <i class="fas fa-chevron-down mr-1"></i> Tampilkan / Sembunyikan
                        </button>
                    </div>
                </div>
                <div id="collapseUnmappedTable" class="collapse show">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table id="tableUnmappedKpm" class="table table-sm table-hover table-bordered table-striped w-100 mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center" style="width: 45px;">No</th>
                                        <th>NIK KPM</th>
                                        <th>Nama KPM</th>
                                        <th>No. KK</th>
                                        <th>Wilayah RT / RW</th>
                                        <th class="text-center">Jenis Bansos</th>
                                        <th class="text-center">Tahap</th>
                                        <th class="text-end">Nominal</th>
                                        <th class="text-center">Status Salur</th>
                                        <th class="text-center" style="width: 140px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyUnmappedKpm"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- ======================================================= -->
<!-- 👁️ MODAL DETAIL BNBA (SAMA DENGAN HALAMAN REKAPITULASI) -->
<!-- ======================================================= -->
<div class="modal fade" id="modalDetailBnba" tabindex="-1" aria-labelledby="modalDetailBnbaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="modalDetailBnbaLabel">
                    <i class="fas fa-id-card mr-2"></i> Detail Penerima Bantuan Sosial
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
                                <span class="small fw-bold text-muted d-block mb-1" id="labelSumberKoordinatModal">Geotagging Lokasi</span>
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
                        <div class="card border shadow-none mt-3 mb-0">
                            <div class="card-header bg-light py-2">
                                <span class="small fw-bold text-muted">Foto Rumah Tampak Depan</span>
                            </div>
                            <div class="card-body p-2 text-center">
                                <a id="linkFotoRumahDepan" href="<?= base_url('data/usulan/foto_rumah/noimage.png'); ?>" target="_blank" rel="noopener" title="Klik untuk memperbesar">
                                    <img id="imgFotoRumahDepan" src="<?= base_url('data/usulan/foto_rumah/noimage.png'); ?>" alt="Foto rumah tampak depan" class="img-fluid rounded border" style="max-height: 320px; object-fit: contain;" onerror="this.onerror=null;this.src='<?= base_url('assets/img/no-image.svg'); ?>';">
                                </a>
                            </div>
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
<!-- 📜 SCRIPTS: LEAFLET & MARKERCLUSTER -->
<!-- ======================================================= -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
    $(document).ready(function() {

        // =======================================================
        // 1️⃣ INISIALISASI PETA SATELIT LEAFLET & LAYER SWITCHER
        // =======================================================
        const defaultLat = -7.4560;
        const defaultLng = 107.6450;

        // Layer 1: Esri World Imagery + Label Jalan CartoDB (Anti-Blank s/d Zoom 20)
        const esriSatTiles = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 20,
            maxNativeZoom: 17,
            attribution: 'Tiles &copy; Esri'
        });
        const cartoLabels = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
            maxZoom: 20,
            maxNativeZoom: 18,
            attribution: '&copy; CARTO'
        });
        const layerSatelitLabel = L.layerGroup([esriSatTiles, cartoLabels]);

        // Layer 2: Google Hybrid Satellite
        const layerGoogleHybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            maxNativeZoom: 19,
            attribution: '&copy; Google Maps'
        });

        // Layer 3: Satelit Esri Murni (Tanpa Label)
        const layerSatelitMurni = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 20,
            maxNativeZoom: 17,
            attribution: 'Tiles &copy; Esri'
        });

        // Layer 4: Peta Jalan OpenStreetMap
        const layerStreetOsm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            maxNativeZoom: 19,
            attribution: '&copy; OpenStreetMap'
        });

        const map = L.map('mapBansos', {
            center: [defaultLat, defaultLng],
            zoom: 14,
            layers: [layerSatelitLabel]
        });

        // Layer Group Batas Desa (Gaya Google Maps: Garis Merah + Putus-putus Putih)
        const layerBatasDesa = L.featureGroup().addTo(map);
        let batasDesaBounds = null;
        let isBatasDesaVisible = true;
        const activeKodeDesa = "<?= esc($kode_desa ?? '32.05.33.2006'); ?>";
        const activeNamaDesa = "<?= esc($nama_desa ?? 'PASIRLANGU'); ?>";

        const baseMaps = {
            "🛰️ Satelit + Nama Jalan (Esri)": layerSatelitLabel,
            "🌍 Satelit Resolusi Tinggi (Google Hybrid)": layerGoogleHybrid,
            "🗺️ Satelit Murni (Tanpa Label)": layerSatelitMurni,
            "🛣️ Peta Jalan (OpenStreetMap)": layerStreetOsm
        };
        const overlayMaps = {
            "🔴 Batas Wilayah Desa": layerBatasDesa
        };
        L.control.layers(baseMaps, overlayMaps, {
            position: 'topright'
        }).addTo(map);

        // Muat GeoJSON Batas Wilayah Desa
        $.getJSON("<?= base_url('assets/geojson/batas_desa_pakenjeng.json'); ?>", function(allVillages) {
            const vilData = allVillages[activeKodeDesa] || allVillages['32.05.33.2006'];
            if (vilData && vilData.geojson) {
                layerBatasDesa.clearLayers();

                // 1. Lapisan Luar (Outer Red Glow / Casing khas Google Maps)
                const outerRedLayer = L.geoJSON(vilData.geojson, {
                    style: {
                        color: '#ea4335',
                        weight: 5.5,
                        opacity: 0.92,
                        fillColor: '#ea4335',
                        fillOpacity: 0.04,
                        lineJoin: 'round',
                        lineCap: 'round'
                    },
                    interactive: false
                });

                // 2. Lapisan Dalam (Inner White-Red Dashed Line khas Google Maps)
                const innerDashLayer = L.geoJSON(vilData.geojson, {
                    style: {
                        color: '#ffffff',
                        weight: 2.2,
                        opacity: 0.95,
                        dashArray: '4, 6',
                        fillOpacity: 0,
                        lineJoin: 'round',
                        lineCap: 'butt'
                    },
                    interactive: false
                });

                layerBatasDesa.addLayer(outerRedLayer);
                layerBatasDesa.addLayer(innerDashLayer);

                batasDesaBounds = outerRedLayer.getBounds();
                if (batasDesaBounds && batasDesaBounds.isValid()) {
                    map.fitBounds(batasDesaBounds, {
                        padding: [25, 25]
                    });
                }
            }
        });

        // Saklar Tombol Batas Desa ON/OFF
        $('#btnToggleBatasDesa').on('click', function() {
            if (map.hasLayer(layerBatasDesa)) {
                map.removeLayer(layerBatasDesa);
                isBatasDesaVisible = false;
                $(this).removeClass('btn-danger').addClass('btn-outline-danger')
                    .html('<i class="fas fa-draw-polygon mr-1"></i> Batas Desa: OFF');
            } else {
                map.addLayer(layerBatasDesa);
                isBatasDesaVisible = true;
                $(this).removeClass('btn-outline-danger').addClass('btn-danger')
                    .html('<i class="fas fa-draw-polygon mr-1"></i> Batas Desa: ON');
            }
        });

        // Sinkronisasi jika di-toggle dari kontrol layer kanan atas Leaflet
        map.on('overlayadd', function(e) {
            if (e.layer === layerBatasDesa) {
                isBatasDesaVisible = true;
                $('#btnToggleBatasDesa').removeClass('btn-outline-danger').addClass('btn-danger')
                    .html('<i class="fas fa-draw-polygon mr-1"></i> Batas Desa: ON');
            }
        });
        map.on('overlayremove', function(e) {
            if (e.layer === layerBatasDesa) {
                isBatasDesaVisible = false;
                $('#btnToggleBatasDesa').removeClass('btn-danger').addClass('btn-outline-danger')
                    .html('<i class="fas fa-draw-polygon mr-1"></i> Batas Desa: OFF');
            }
        });

        // Marker Cluster Group (mengelompok menjadi angka saat zoom out & menyebar saat zoom in)
        const markerCluster = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            maxClusterRadius: 42,
            disableClusteringAtZoom: 19
        });
        map.addLayer(markerCluster);

        // State Global Peta
        let currentMarkersData = [];
        let markerObjectsMap = {}; // Simpan referensi marker berdasarkan id untuk fitur Fly-To
        let currentColorMode = 'jenis'; // 'jenis' | 'status'
        let tableUnmappedDT = null;

        // =======================================================
        // 2️⃣ GENERATOR CUSTOM PIN MARKER & POPUP HTML
        // =======================================================
        function getPinConfig(item, mode) {
            if (mode === 'status') {
                if (item.status_summary === 'sukses') {
                    return {
                        color: '#28a745',
                        icon: 'fa-check'
                    };
                } else if (item.status_summary === 'gagal') {
                    return {
                        color: '#dc3545',
                        icon: 'fa-times'
                    };
                } else {
                    return {
                        color: '#ffc107',
                        icon: 'fa-clock'
                    };
                }
            } else {
                // Default: Berdasarkan Jenis Bansos
                if (item.jenis_bansos === 'PKH + SEMBAKO') {
                    return {
                        color: '#6f42c1',
                        icon: 'fa-layer-group'
                    };
                } else if (item.jenis_bansos === 'PKH') {
                    return {
                        color: '#007bff',
                        icon: 'fa-hand-holding-heart'
                    };
                } else {
                    return {
                        color: '#20c997',
                        icon: 'fa-box-open'
                    };
                }
            }
        }

        function createCustomMarkerIcon(item, mode) {
            const cfg = getPinConfig(item, mode);
            const isRumah = (item.coord_source === 'rumah_kk');
            const sourceIcon = isRumah ? 'fa-home' : 'fa-camera';
            const sourceBg = isRumah ? '#0d6efd' : '#17a2b8';

            const html = `
            <div class="custom-map-pin">
                <div class="pin-Tear" style="background-color: ${cfg.color};">
                    <i class="fas ${cfg.icon}"></i>
                </div>
                <span class="pin-source-dot" style="background-color: ${sourceBg};" title="${item.coord_source_label}">
                    <i class="fas ${sourceIcon}"></i>
                </span>
            </div>
        `;

            return L.divIcon({
                html: html,
                className: '',
                iconSize: [34, 42],
                iconAnchor: [17, 40],
                popupAnchor: [0, -36]
            });
        }

        function buildPopupHtml(item) {
            const cfg = getPinConfig(item, currentColorMode);

            // Badge Jenis Bansos
            let badgeJenis = '';
            if (item.jenis_bansos === 'PKH + SEMBAKO') {
                badgeJenis = '<span class="badge px-2 py-1" style="background-color:#6f42c1; color:#fff;"><i class="fas fa-layer-group mr-1"></i> PKH + SEMBAKO</span>';
            } else if (item.jenis_bansos === 'PKH') {
                badgeJenis = '<span class="badge badge-primary px-2 py-1"><i class="fas fa-hand-holding-heart mr-1"></i> PKH</span>';
            } else {
                badgeJenis = '<span class="badge px-2 py-1" style="background-color:#20c997; color:#fff;"><i class="fas fa-box-open mr-1"></i> SEMBAKO</span>';
            }

            // Badge Status Salur
            let badgeStatus = '';
            if (item.status_summary === 'sukses') {
                badgeStatus = '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Sukses Salur</span>';
            } else if (item.status_summary === 'gagal') {
                badgeStatus = `<span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> ${item.status_label}</span>`;
            } else {
                badgeStatus = '<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Transaksi</span>';
            }

            // Badge Sumber Koordinat
            const badgeCoordSource = (item.coord_source === 'rumah_kk') ?
                '<span class="badge badge-light border text-primary" style="font-size:0.68rem;"><i class="fas fa-home mr-1"></i> Titik Lokasi Rumah Warga</span>' :
                '<span class="badge badge-light border text-info" style="font-size:0.68rem;"><i class="fas fa-camera mr-1"></i> Titik Lokasi Dokumentasi Salur</span>';

            const thumbSrc = item.foto_kpm_url ? item.foto_kpm_url : "<?= base_url('assets/img/no-image.svg'); ?>";
            const gmapsUrl = `https://www.google.com/maps?q=${item.latitude},${item.longitude}`;

            return `
            <div>
                <div class="popup-kpm-header" style="background-color: ${cfg.color};">
                    <span class="text-truncate" style="max-width: 210px;" title="${item.nama_kpm}">
                        <i class="fas fa-user-check mr-1"></i> ${item.nama_kpm}
                    </span>
                    <span class="badge bg-white text-dark" style="font-size: 0.7rem;">RT ${item.rt}/RW ${item.rw}</span>
                </div>
                <div class="popup-kpm-body">
                    <div class="d-flex mb-2" style="gap: 10px;">
                        <img src="${thumbSrc}" alt="Foto KPM" class="popup-thumb-34">
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="mb-1">${badgeJenis}</div>
                            <div class="mb-1">${badgeStatus}</div>
                            <div class="fw-bold text-success mt-1" style="font-size: 0.85rem;">${item.nominal_formatted}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">${item.tahap_salur}</div>
                        </div>
                    </div>

                    <div class="border-top pt-2 mt-1 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">NIK:</span>
                            <span class="font-monospace fw-bold">${item.nik_masked}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">No. KK:</span>
                            <span class="font-monospace">${item.no_kk_masked}</span>
                        </div>
                        <div class="text-muted text-truncate mb-1" title="${item.alamat}">
                            <i class="fas fa-map-pin mr-1 text-danger"></i> ${item.alamat}
                        </div>
                        <div class="mb-2">${badgeCoordSource}</div>
                    </div>

                    <div class="d-flex justify-content-between mt-2 pt-2 border-top" style="gap: 6px;">
                        <button type="button" class="btn btn-xs btn-primary btn-detail-bnba flex-grow-1 rounded-pill py-1 fw-bold" data-id="${item.id}">
                            <i class="fas fa-eye mr-1"></i> Detail Lengkap
                        </button>
                        <a href="${gmapsUrl}" target="_blank" class="btn btn-xs btn-outline-secondary rounded-pill py-1 px-2 text-dark" title="Buka Rute di Google Maps">
                            <i class="fas fa-directions text-primary"></i> Maps
                        </a>
                    </div>
                </div>
            </div>
        `;
        }

        // =======================================================
        // 3️⃣ RENDER MARKERS KE ATAS PETA SATELIT
        // =======================================================
        function renderMarkersOnMap(fitBounds = true) {
            markerCluster.clearLayers();
            markerObjectsMap = {};

            const bounds = [];

            currentMarkersData.forEach(function(item) {
                const lat = parseFloat(item.latitude);
                const lng = parseFloat(item.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                const icon = createCustomMarkerIcon(item, currentColorMode);
                const marker = L.marker([lat, lng], {
                    icon: icon
                });
                marker.bindPopup(buildPopupHtml(item), {
                    maxWidth: 300
                });

                markerCluster.addLayer(marker);
                markerObjectsMap[item.id] = {
                    marker: marker,
                    data: item
                };
                bounds.push([lat, lng]);
            });

            if (fitBounds) {
                const hasSubWilayahFilter = ($('#filter_rw').val() !== '' || $('#filter_rt').val() !== '');
                if (!hasSubWilayahFilter && batasDesaBounds && batasDesaBounds.isValid()) {
                    map.fitBounds(batasDesaBounds, {
                        padding: [25, 25]
                    });
                } else if (bounds.length > 0) {
                    map.fitBounds(bounds, {
                        padding: [45, 45],
                        maxZoom: 18
                    });
                } else if (batasDesaBounds && batasDesaBounds.isValid()) {
                    map.fitBounds(batasDesaBounds, {
                        padding: [25, 25]
                    });
                }
            }
        }

        // =======================================================
        // 4️⃣ UPDATE LEGENDA BERDASARKAN MODE WARNA PIN
        // =======================================================
        function updateLegendUI() {
            if (currentColorMode === 'status') {
                $('#legendTitle').html('<i class="fas fa-check-double text-primary mr-1"></i> Legenda Status Salur');
                $('#legendContent').html(`
                <div class="d-flex align-items-center mb-1">
                    <span class="legend-dot" style="background-color: #28a745;"></span>
                    <span><b>Sukses Salur</b></span>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <span class="legend-dot" style="background-color: #ffc107;"></span>
                    <span><b>Belum Transaksi / Draft</b></span>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <span class="legend-dot" style="background-color: #dc3545;"></span>
                    <span><b>Gagal Salur / Kendala</b></span>
                </div>
            `);
            } else {
                $('#legendTitle').html('<i class="fas fa-info-circle text-primary mr-1"></i> Legenda Jenis Bansos');
                $('#legendContent').html(`
                <div class="d-flex align-items-center mb-1">
                    <span class="legend-dot" style="background-color: #007bff;"></span>
                    <span><b>PKH</b> (Program Keluarga Harapan)</span>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <span class="legend-dot" style="background-color: #20c997;"></span>
                    <span><b>SEMBAKO</b> (BPNT)</span>
                </div>
                <div class="d-flex align-items-center mb-1">
                    <span class="legend-dot" style="background-color: #6f42c1;"></span>
                    <span><b>PKH + SEMBAKO</b> (2 Bantuan)</span>
                </div>
            `);
            }
        }

        // Event klik saklar mode warna pin
        $('.mode-color-btn').on('click', function() {
            $('.mode-color-btn').removeClass('btn-primary active').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
            currentColorMode = $(this).data('mode');
            updateLegendUI();
            renderMarkersOnMap(false);
        });

        // =======================================================
        // 5️⃣ LOAD DATA SEBARAN DARI SERVER (AJAX POST)
        // =======================================================
        function loadMappingData(fitBounds = true) {
            const pPetugas = $('#filter_petugas').val();
            const pRw = $('#filter_rw').val();
            const pRt = $('#filter_rt').val();
            const pTahap = $('#filter_tahap').val();
            const pJenis = $('#filter_jenis').val();
            const pStatus = $('#filter_status').val();

            let labelFilter = pJenis ? pJenis : 'Semua Bansos';
            if (pTahap) labelFilter += ' (' + pTahap + ')';
            if (pRw) labelFilter += ' - RW ' + pRw;
            $('#labelActiveFilter').text(labelFilter);

            $.ajax({
                url: "<?= base_url('bansos/mapping-data'); ?>",
                type: "POST",
                data: {
                    filter_petugas: pPetugas,
                    filter_rw: pRw,
                    filter_rt: pRt,
                    filter_tahap: pTahap,
                    filter_jenis: pJenis,
                    filter_status: pStatus,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: "JSON",
                success: function(res) {
                    if (res.status) {
                        const s = res.summary || {};

                        // Update KPI Cards
                        $('#map_kpi_total').text(Number(s.total_kpm || 0).toLocaleString('id-ID'));
                        $('#map_kpi_mapped').text(Number(s.total_mapped || 0).toLocaleString('id-ID'));
                        $('#map_kpi_persen_mapped').text('(' + (s.persen_mapped || 0) + '%)');
                        $('#map_kpi_src_rumah').text(Number(s.source_rumah_kk || 0).toLocaleString('id-ID'));
                        $('#map_kpi_src_bansos').text(Number(s.source_bansos_kks || 0).toLocaleString('id-ID'));
                        $('#map_kpi_unmapped').text(Number(s.total_unmapped || 0).toLocaleString('id-ID'));
                        $('#badgeCountUnmapped').text(Number(s.total_unmapped || 0).toLocaleString('id-ID'));

                        $('#map_kpi_pkh').text(Number(s.count_pkh || 0).toLocaleString('id-ID'));
                        $('#map_kpi_sembako').text(Number(s.count_sembako || 0).toLocaleString('id-ID'));
                        $('#map_kpi_kombo').text(Number(s.count_pkh_sembako || 0).toLocaleString('id-ID'));

                        // Simpan data marker & render ke peta
                        currentMarkersData = res.markers || [];
                        renderMarkersOnMap(fitBounds);

                        // Isi Datalist Pencarian Cepat KPM di Peta
                        const datalist = $('#datalistKpmMapped');
                        datalist.empty();
                        currentMarkersData.forEach(function(m) {
                            datalist.append(`<option value="${m.nama_kpm} (${m.nik_kpm})" data-id="${m.id}">RT ${m.rt}/RW ${m.rw} - ${m.jenis_bansos}</option>`);
                        });

                        // Render Tabel KPM Belum Memiliki Koordinat
                        renderUnmappedTable(res.unmapped || []);
                    }
                },
                error: function() {
                    console.error("Gagal memuat data mapping bansos.");
                }
            });
        }

        // =======================================================
        // 6️⃣ RENDER TABEL KPM BELUM MEMILIKI KOORDINAT
        // =======================================================
        function renderUnmappedTable(unmappedList) {
            if (tableUnmappedDT) {
                tableUnmappedDT.destroy();
            }

            const tbody = $('#tbodyUnmappedKpm');
            tbody.empty();

            unmappedList.forEach(function(item, idx) {
                let badgeJenis = '';
                if (item.jenis_bansos === 'PKH + SEMBAKO') {
                    badgeJenis = '<span class="badge px-2 py-1" style="background-color:#6f42c1; color:#fff;"><i class="fas fa-layer-group mr-1"></i> PKH + SEMBAKO</span>';
                } else if (item.jenis_bansos === 'PKH') {
                    badgeJenis = '<span class="badge badge-primary px-2 py-1"><i class="fas fa-hand-holding-heart mr-1"></i> PKH</span>';
                } else {
                    badgeJenis = '<span class="badge px-2 py-1" style="background-color:#20c997; color:#fff;"><i class="fas fa-box-open mr-1"></i> SEMBAKO</span>';
                }

                let badgeStatus = '';
                if (item.status_summary === 'sukses') {
                    badgeStatus = '<span class="badge badge-success px-2 py-1">Sukses Salur</span>';
                } else if (item.status_summary === 'gagal') {
                    badgeStatus = `<span class="badge badge-danger px-2 py-1">${item.status_label}</span>`;
                } else {
                    badgeStatus = '<span class="badge badge-warning text-dark px-2 py-1">Belum Transaksi</span>';
                }

                let btnKkHtml = '';
                if (item.id_kk_token) {
                    btnKkHtml = `<a href="<?= base_url('pembaruan-keluarga/detail'); ?>/${item.id_kk_token}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2 mr-1" title="Petakan Koordinat Rumah di Pembaruan Keluarga">
                    <i class="fas fa-home"></i> KK
                </a>`;
                }

                const tr = `
                <tr>
                    <td class="text-center">${idx + 1}</td>
                    <td class="font-monospace small fw-bold text-primary">${item.nik_masked}</td>
                    <td class="fw-bold text-dark">${item.nama_kpm}</td>
                    <td class="font-monospace small">${item.no_kk_masked}</td>
                    <td>
                        <span class="small">${item.alamat}</span><br>
                        <span class="badge badge-light border text-muted">RT ${item.rt} / RW ${item.rw}</span>
                    </td>
                    <td class="text-center">${badgeJenis}</td>
                    <td class="text-center"><span class="badge badge-secondary">${item.tahap_salur}</span></td>
                    <td class="text-end fw-bold text-success">${item.nominal_formatted}</td>
                    <td class="text-center">${badgeStatus}</td>
                    <td class="text-center">
                        ${btnKkHtml}
                        <button type="button" class="btn btn-xs btn-info btn-detail-bnba rounded-pill px-2" data-id="${item.id}" title="Lihat Detail BNBA">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>
            `;
                tbody.append(tr);
            });

            tableUnmappedDT = $('#tableUnmappedKpm').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                language: {
                    search: "Cari KPM:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ KPM belum terpetakan",
                    infoEmpty: "Semua KPM pada filter ini sudah memiliki titik koordinat!",
                    zeroRecords: "Tidak ada KPM yang belum terpetakan pada filter ini",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "»",
                        previous: "«"
                    }
                }
            });
        }

        // Klik kartu "Belum Ada Koordinat" -> scroll ke tabel di bawah
        $('#cardScrollUnmapped').on('click', function() {
            $('html, body').animate({
                scrollTop: $('#sectionUnmappedKpm').offset().top - 70
            }, 400);
        });

        // =======================================================
        // 7️⃣ FITUR CARI & TERBANG (FLY-TO) KE TITIK RUMAH KPM
        // =======================================================
        function flyToSearchedKpm() {
            const query = ($('#inputCariKpmPeta').val() || '').trim().toLowerCase();
            if (!query) return;

            let foundObj = null;
            for (const id in markerObjectsMap) {
                const item = markerObjectsMap[id].data;
                const matchStr = `${item.nama_kpm} (${item.nik_kpm})`.toLowerCase();
                if (matchStr === query || item.nama_kpm.toLowerCase().includes(query) || item.nik_kpm.toLowerCase().includes(query)) {
                    foundObj = markerObjectsMap[id];
                    break;
                }
            }

            if (foundObj) {
                markerCluster.zoomToShowLayer(foundObj.marker, function() {
                    map.setView(foundObj.marker.getLatLng(), 19, {
                        animate: true
                    });
                    foundObj.marker.openPopup();
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Titik Tidak Ditemukan',
                    text: 'KPM tersebut tidak ditemukan pada peta atau belum memiliki titik koordinat.',
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        }

        $('#btnFlyToKpm').on('click', flyToSearchedKpm);
        $('#inputCariKpmPeta').on('change keyup', function(e) {
            if (e.type === 'change' || e.key === 'Enter') {
                flyToSearchedKpm();
            }
        });

        // Tombol Fokus Sebaran Desa (Fit Bounds ke Batas Desa / Titik Sebaran)
        $('#btn_fit_bounds').on('click', function() {
            if (batasDesaBounds && batasDesaBounds.isValid()) {
                map.fitBounds(batasDesaBounds, {
                    padding: [25, 25]
                });
                return;
            }
            const bounds = currentMarkersData.map(m => [parseFloat(m.latitude), parseFloat(m.longitude)]).filter(c => !isNaN(c[0]) && !isNaN(c[1]));
            if (bounds.length > 0) {
                map.fitBounds(bounds, {
                    padding: [45, 45],
                    maxZoom: 18
                });
            }
        });

        // =======================================================
        // 8️⃣ CASCADING DROPDOWN RW -> RT & EVENT FILTER
        // =======================================================
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

        $('#filter_rw').on('change', function() {
            const rwSelected = $(this).val();
            $('#filter_rt').empty().append('<option value="">Semua RT</option>');

            if (rwSelected !== '') {
                $.ajax({
                    url: "<?= base_url('bansos/get-rt'); ?>",
                    type: "GET",
                    data: {
                        rw: rwSelected
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $.each(data, function(idx, val) {
                            $('#filter_rt').append('<option value="' + val + '">RT ' + val + '</option>');
                        });
                    }
                });
            }
        });

        $('#btn_filter').on('click', function() {
            loadMappingData(true);
        });

        $('#filter_petugas, #filter_rw, #filter_rt, #filter_tahap, #filter_jenis, #filter_status').on('change', function() {
            loadMappingData(true);
        });

        $('#btn_reset').on('click', function() {
            if (!$('#filter_petugas').prop('disabled')) {
                $('#filter_petugas').val('');
            }
            $('#filter_rw').val('');
            $('#filter_rt').empty().append('<option value="">Semua RT</option>');
            $('#filter_tahap').val('');
            $('#filter_jenis').val('');
            $('#filter_status').val('');
            $('#inputCariKpmPeta').val('');

            loadMappingData(true);
        });

        // =======================================================
        // 9️⃣ MODAL DETAIL BNBA (MENDUKUNG 1 KPM 2 BANSOS & PORTRAIT 3:4)
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
                        const fotoRumahDepanUrl = d.foto_depan_url || "<?= base_url('data/usulan/foto_rumah/noimage.png'); ?>";
                        $('#imgFotoRumahDepan').attr('src', fotoRumahDepanUrl);
                        $('#linkFotoRumahDepan').attr('href', fotoRumahDepanUrl);

                        // Jenis Bansos Badge Modal
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
                                $('#labelSumberKoordinatModal').text(item.coord_source_label || 'Geotagging Lokasi');
                                $('#txtKoordinat').text(item.latitude + ', ' + item.longitude);
                                $('#btnBukaMaps').attr('href', item.maps_url);
                            } else {
                                $('#boxLokasi').hide();
                            }

                            const statusLower = (item.status_salur || '').toLowerCase();
                            let badgeStatus = '<span class="badge badge-warning text-dark">Belum Transaksi</span>';
                            if (statusLower === 'sukses salur') {
                                badgeStatus = '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Sukses Salur</span>';
                            } else if (item.status_salur) {
                                badgeStatus = '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> ' + item.status_salur + '</span>';
                            }
                            $('#detail_status_salur').html(badgeStatus);
                        }

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

                            renderItemDetail(items[0]);

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
        // 🚀 INITIAL LOAD
        // =======================================================
        loadMappingData(true);
    });
</script>

<?= $this->endSection(); ?>