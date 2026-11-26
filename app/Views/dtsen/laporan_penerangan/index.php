<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .kpi-card-penerangan {
        border-radius: 12px;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        cursor: pointer;
        border: 1px solid rgba(0, 0, 0, 0.07);
        user-select: none;
    }

    .kpi-card-penerangan:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08) !important;
    }

    .kpi-card-penerangan.active-filter {
        outline: 2.5px solid #0d6efd;
        box-shadow: 0 6px 16px rgba(13, 110, 253, 0.18) !important;
        background-color: #f8fbff !important;
    }

    .kpi-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .chart-box-penerangan {
        position: relative;
        height: 290px;
    }

    .filter-label-sm {
        font-size: 0.75rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 3px;
        display: block;
    }

    .insight-pill {
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .insight-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08) !important;
    }

    .table-wilayah-row:hover {
        background-color: #eef6ff !important;
        cursor: pointer;
    }

    .card-header::after {
        display: none !important;
    }
</style>

<div class="content-wrapper mt-1 px-2 px-md-3 pb-4">
    <!-- ========================================== -->
    <!-- HEADER & TOMBOL LAPORAN                   -->
    <!-- ========================================== -->
    <div class="content-header pb-2">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="m-0 fw-bold text-dark">
                    <i class="fas fa-bolt text-warning me-2"></i><?= esc($title) ?>
                </h4>
                <small class="text-muted">
                    Klasifikasi keluarga (Smart Merge Usulan Terbaru &amp; Master RT) &bull;
                    <span class="badge bg-light text-dark border"><?= esc($nama_desa_aktif) ?></span>
                    <span class="badge bg-primary text-white" id="badgeTotalTop">Memuat...</span>
                </small>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="<?= base_url('dtsen-se') ?>" class="btn btn-sm btn-outline-secondary bg-white">
                    <i class="fas fa-arrow-left me-1"></i> Data Keluarga
                </a>

                <button type="button" id="btnExportExcel" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Excel (.xlsx)
                </button>

                <div class="btn-group shadow-sm">
                    <button type="button" id="btnCetakRingkas" class="btn btn-sm btn-primary">
                        <i class="fas fa-print me-1"></i> Cetak Laporan Resmi
                    </button>
                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" data-toggle="dropdown" aria-expanded="false">
                        <span class="sr-only visually-hidden">Opsi Cetak</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" id="optCetakRingkas">
                                <i class="fas fa-file-alt text-primary me-2"></i> Ringkasan &amp; Rekap Wilayah Saja
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" id="optCetakLengkap">
                                <i class="fas fa-list-ol text-success me-2"></i> Lengkap dengan Lampiran BNBA
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PANEL FILTER MULTI-DIMENSI                -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <?php if ((int) $role_id < 3): ?>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label class="filter-label-sm">Desa / Kelurahan</label>
                        <select class="form-select form-select-sm filter-control" id="filter_desa">
                            <option value="all" <?= $default_desa === 'all' ? 'selected' : '' ?>>Semua Desa</option>
                            <?php foreach ($desa_list as $d): ?>
                                <option value="<?= esc($d['kode_desa']) ?>" <?= $default_desa === $d['kode_desa'] ? 'selected' : '' ?>>
                                    <?= esc($d['nama_desa_label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" id="filter_desa" value="<?= esc($default_desa) ?>">
                <?php endif; ?>

                <div class="col-6 col-md-2 col-lg-1">
                    <label class="filter-label-sm">RW</label>
                    <select class="form-select form-select-sm filter-control" id="filter_rw">
                        <option value="all">Semua</option>
                        <?php foreach ($rw_list as $rw): ?>
                            <option value="<?= esc($rw) ?>">RW <?= esc($rw) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-1">
                    <label class="filter-label-sm">RT</label>
                    <select class="form-select form-select-sm filter-control" id="filter_rt" disabled>
                        <option value="all">Semua</option>
                    </select>
                </div>

                <div class="col-12 col-md-5 col-lg-3">
                    <label class="filter-label-sm">Sumber Data (Smart Merge)</label>
                    <select class="form-select form-select-sm filter-control" id="filter_sumber_mode">
                        <option value="all" selected>Semua (Usulan Verified/Submitted/Draft + Master RT)</option>
                        <option value="verified">Hanya Diverifikasi / Submitted + Master RT</option>
                        <option value="draft_only">Khusus Usulan Draft (Belum Verifikasi)</option>
                        <option value="master_only">Hanya Master Rumah Tangga (dtsen_rt)</option>
                    </select>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <label class="filter-label-sm">Sumber Penerangan Utama</label>
                    <select class="form-select form-select-sm filter-control" id="filter_kategori">
                        <option value="all">Semua Kategori</option>
                        <?php foreach ($kategori_list as $kat): ?>
                            <option value="<?= esc($kat) ?>"><?= esc($kat) ?></option>
                        <?php endforeach; ?>
                        <option value="non_pln_atau_gelap">Non-PLN &amp; Bukan Listrik</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="filter-label-sm">Golongan Daya PLN</label>
                    <select class="form-select form-select-sm filter-control" id="filter_daya">
                        <option value="all">Semua Daya</option>
                        <option value="450 WATT">450 WATT (Subsidi)</option>
                        <option value="900 WATT">900 WATT (incl. &le; 900W)</option>
                        <option value="1.300 WATT">1.300 WATT</option>
                        <option value="2.200 WATT+">2.200 WATT+ (&gt; 900W / &ge; 2.200W)</option>
                        <option value="1300_plus">&ge; 1.300 WATT (1.300W &amp; 2.200W+)</option>
                        <option value="multi">Multi-Meteran (&gt; 1 Meter)</option>
                        <option value="kosong">PLN Meteran (Daya Kosong)</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-1">
                    <label class="filter-label-sm">Desil</label>
                    <select class="form-select form-select-sm filter-control" id="filter_desil">
                        <option value="all">Semua</option>
                        <option value="1_4">Desil 1–4</option>
                        <option value="5_10">Desil 5–10</option>
                        <option value="belum">Belum Ada / 0</option>
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>">Desil <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="filter-label-sm">Status Bansos</label>
                    <select class="form-select form-select-sm filter-control" id="filter_bansos">
                        <option value="all">Semua Status</option>
                        <option value="PENERIMA">Semua Penerima Bansos</option>
                        <option value="PKH">PKH Saja</option>
                        <option value="SEMBAKO">SEMBAKO Saja</option>
                        <option value="PKH + SEMBAKO">PKH + SEMBAKO</option>
                        <option value="NONE">Non-Bansos (-)</option>
                    </select>
                </div>

                <div class="col-auto">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetFilter" title="Reset Seluruh Filter">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 5 KARTU KPI INTERAKTIF                    -->
    <!-- ========================================== -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-4 col-lg">
            <div class="card kpi-card-penerangan shadow-sm bg-white h-100 p-2 btn-kpi-filter" data-kategori="Listrik PLN dengan meteran" style="border-left: 4px solid #198754 !important;" title="Klik untuk filter PLN dengan meteran">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon-box bg-success text-white me-2">
                        <i class="fas fa-plug"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small text-truncate">PLN dgn Meteran</div>
                        <div class="fs-5 fw-bold text-success" id="kpiPlnMeteran">0 <small class="fw-normal text-muted">(0%)</small></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg">
            <div class="card kpi-card-penerangan shadow-sm bg-white h-100 p-2 btn-kpi-filter" data-kategori="Listrik PLN tanpa meteran" style="border-left: 4px solid #ffc107 !important;" title="Klik untuk filter PLN tanpa meteran (nyalur)">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon-box bg-warning text-dark me-2">
                        <i class="fas fa-code-branch"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small text-truncate">PLN Tanpa Meteran</div>
                        <div class="fs-5 fw-bold" style="color: #997404;" id="kpiPlnTanpa">0 <small class="fw-normal text-muted">(0%)</small></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg">
            <div class="card kpi-card-penerangan shadow-sm bg-white h-100 p-2 btn-kpi-filter" data-kategori="Listrik non-PLN" style="border-left: 4px solid #0dcaf0 !important;" title="Klik untuk filter Listrik non-PLN">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon-box bg-info text-dark me-2">
                        <i class="fas fa-solar-panel"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small text-truncate">Listrik non-PLN</div>
                        <div class="fs-5 fw-bold" style="color: #055160;" id="kpiNonPln">0 <small class="fw-normal text-muted">(0%)</small></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-lg">
            <div class="card kpi-card-penerangan shadow-sm bg-white h-100 p-2 btn-kpi-filter" data-kategori="Bukan listrik" style="border-left: 4px solid #dc3545 !important;" title="Klik untuk filter Bukan listrik">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon-box bg-danger text-white me-2">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small text-truncate">Bukan Listrik</div>
                        <div class="fs-5 fw-bold text-danger" id="kpiBukanListrik">0 <small class="fw-normal text-muted">(0%)</small></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg">
            <div class="card kpi-card-penerangan shadow-sm bg-white h-100 p-2 btn-kpi-filter" data-kategori="Belum Terdata" style="border-left: 4px solid #6c757d !important;" title="Klik untuk filter Belum Terdata">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon-box bg-secondary text-white me-2">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-muted small text-truncate">Belum Terdata</div>
                        <div class="fs-5 fw-bold text-secondary" id="kpiBelumTerdata">0 <small class="fw-normal text-muted">(0%)</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SOROTAN PRIORITAS OPERATOR                -->
    <!-- ========================================== -->
    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <div class="alert alert-warning border mb-0 py-2 px-3 d-flex justify-content-between align-items-center insight-pill" id="insightDesilTanpaMeteran" title="Klik untuk melihat daftar keluarga ini">
                <div class="small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Desil 1–4</strong> PLN Tanpa Meteran (Nyalur)
                </div>
                <span class="badge bg-warning text-dark fs-6" id="valInsightTanpaMeter">0 KK</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-danger border mb-0 py-2 px-3 d-flex justify-content-between align-items-center insight-pill" id="insightDesilBukanListrik" title="Klik untuk melihat daftar keluarga Desil 1-4 Non-PLN / Bukan Listrik">
                <div class="small">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>Desil 1–4</strong> Non-PLN / Bukan Listrik
                </div>
                <span class="badge bg-danger fs-6" id="valInsightGelap">0 KK</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-info border mb-0 py-2 px-3 d-flex justify-content-between align-items-center insight-pill" id="insightBansosDayaTinggi" title="Klik untuk melihat penerima Bansos daya >= 1.300W">
                <div class="small">
                    <i class="fas fa-bolt me-1"></i>
                    Penerima Bansos Daya &ge; 1.300 Watt
                </div>
                <span class="badge bg-info text-dark fs-6" id="valInsightBansosTinggi">0 KK</span>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- NAVIGASI TAB: RINGKASAN VS BNBA           -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white pb-0 pt-2 px-3 border-bottom">
            <ul class="nav nav-tabs card-header-tabs" id="tabLaporanPenerangan" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active fw-bold" id="nav-ringkasan-tab" data-bs-toggle="tab" data-toggle="tab" href="#paneRingkasan" role="tab">
                        <i class="fas fa-chart-pie text-primary me-1"></i> Ringkasan, Grafik &amp; Matriks Korelasi
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-bold" id="nav-bnba-tab" data-bs-toggle="tab" data-toggle="tab" href="#paneBnba" role="tab">
                        <i class="fas fa-table text-success me-1"></i> Daftar Keluarga Rinci (BNBA)
                        <span class="badge bg-success text-white ms-1" id="badgeCountBnba">0</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-3 tab-content">
            <!-- TAB 1: RINGKASAN, GRAFIK, WILAYAH & KORELASI -->
            <div class="tab-pane fade show active" id="paneRingkasan" role="tabpanel">
                <!-- Baris Grafik -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-6">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-uppercase text-dark"><i class="fas fa-chart-pie text-success me-1"></i> Proporsi Sumber Penerangan Utama</span>
                                <span class="badge bg-secondary" id="badgeTerdataInfo">0% Terdata</span>
                            </div>
                            <div class="card-body">
                                <div class="chart-box-penerangan">
                                    <canvas id="chartKategoriPenerangan"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-uppercase text-dark"><i class="fas fa-tachometer-alt text-primary me-1"></i> Rincian Golongan Daya (PLN dgn Meteran)</span>
                                <span class="badge bg-primary" id="badgeMultiMeterInfo">0 Multi-Meteran</span>
                            </div>
                            <div class="card-body">
                                <div class="chart-box-penerangan">
                                    <canvas id="chartDayaPln"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Korelasi Desil & Bansos -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-5">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <span class="fw-bold small text-uppercase text-dark"><i class="fas fa-layer-group text-warning me-1"></i> Korelasi Sumber Penerangan &times; Desil</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover mb-0 align-middle" id="tblKorelasiDesil">
                                        <thead class="table-light text-center small">
                                            <tr>
                                                <th class="text-start">Sumber Penerangan</th>
                                                <th>Desil 1–4</th>
                                                <th>Desil 5–10</th>
                                                <th>Belum / 0</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="fw-bold table-secondary text-end"></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <span class="fw-bold small text-uppercase text-dark"><i class="fas fa-hands-helping text-info me-1"></i> Korelasi Sumber Penerangan &times; Penerima Bansos</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover mb-0 align-middle" id="tblKorelasiBansos">
                                        <thead class="table-light text-center small">
                                            <tr>
                                                <th class="text-start">Sumber Penerangan</th>
                                                <th>PKH</th>
                                                <th>SEMBAKO</th>
                                                <th>PKH + SEMBAKO</th>
                                                <th>Total Penerima</th>
                                                <th>Non-Bansos</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot class="fw-bold table-secondary text-end"></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Rekapitulasi Per Wilayah -->
                <div class="card border">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <span class="fw-bold small text-uppercase text-dark">
                            <i class="fas fa-map-marked-alt text-danger me-1"></i> Rekapitulasi per Wilayah (<span id="lblWilayahLevel">RW</span>)
                        </span>
                        <small class="text-muted">Klik baris wilayah untuk langsung memfilter daftar keluarga</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover mb-0 align-middle" id="tblRekapWilayah">
                                <thead class="table-dark text-center small">
                                    <tr>
                                        <th style="width: 45px;">No</th>
                                        <th class="text-start">Wilayah</th>
                                        <th>Total KK</th>
                                        <th>PLN Meteran</th>
                                        <th>PLN Tanpa Meteran</th>
                                        <th>Non-PLN</th>
                                        <th>Bukan Listrik</th>
                                        <th>Belum Terdata</th>
                                        <th>450W</th>
                                        <th>900W</th>
                                        <th>&ge; 1.300W</th>
                                        <th>% Terdata</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="fw-bold table-secondary"></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: DAFTAR KELUARGA RINCI (BNBA) -->
            <div class="tab-pane fade" id="paneBnba" role="tabpanel">
                <div class="table-responsive">
                    <table id="tableLaporanPenerangan" class="table table-striped table-hover align-middle w-100">
                        <thead class="bg-primary text-white text-center small text-uppercase">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Kepala Keluarga</th>
                                <th>No. KK</th>
                                <th>Alamat</th>
                                <th>Wilayah</th>
                                <th>Sumber Penerangan</th>
                                <th>Daya &amp; Meteran PLN</th>
                                <th>Desil</th>
                                <th>Bansos Aktif</th>
                                <th>Sumber Data</th>
                                <th style="width: 65px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const csrfName = '<?= csrf_token() ?>';
        let csrfHash   = '<?= csrf_hash() ?>';
        let includeAnalyticsFlag = 1;
        let currentWilayahLevel  = 'RW';
        let chartKategori = null;
        let chartDaya     = null;

        // ==========================================
        // 🛡️ SENSOR NO. KK & SALIN CLIPBOARD
        // ==========================================
        function maskNumberJS(number) {
            if (!number || number === '-') return '-';
            const numStr = String(number).trim();
            if (numStr.length <= 8) return escHtml(numStr);
            const masked = numStr.substring(0, 8) + '*'.repeat(numStr.length - 8);
            return `<span class="fw-bold text-primary" style="cursor:pointer;"
                      onmouseenter="this.innerText='${escHtml(numStr)}'"
                      onmouseleave="this.innerText='${escHtml(masked)}'"
                      ontouchstart="this.innerText='${escHtml(numStr)}'"
                      ontouchend="this.innerText='${escHtml(masked)}'"
                      title="Arahkan kursor / tahan untuk melihat utuh">${escHtml(masked)}</span>`;
        }

        function escHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, function(m) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
            });
        }

        function fmtNum(n) {
            return Number(n || 0).toLocaleString('id-ID');
        }

        $(document).on('click', '.btnCopyNoKK', function(e) {
            e.preventDefault();
            const val = $(this).data('value');
            if (!val) return;
            navigator.clipboard.writeText(String(val)).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin',
                    text: 'No. KK ' + val + ' berhasil disalin',
                    timer: 1300,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        });

        // ==========================================
        // 📊 RENDER GRAFIK CHART.JS
        // ==========================================
        function renderCharts(analytics) {
            const sum = analytics.summary;
            const ctxKat = document.getElementById('chartKategoriPenerangan').getContext('2d');
            if (chartKategori) chartKategori.destroy();

            chartKategori = new Chart(ctxKat, {
                type: 'doughnut',
                data: {
                    labels: [
                        'PLN dengan Meteran',
                        'PLN Tanpa Meteran',
                        'Listrik non-PLN',
                        'Bukan Listrik',
                        'Belum Terdata'
                    ],
                    datasets: [{
                        data: [
                            sum.pln_meteran.count,
                            sum.pln_tanpa.count,
                            sum.non_pln.count,
                            sum.bukan_listrik.count,
                            sum.belum_terdata.count
                        ],
                        backgroundColor: ['#198754', '#ffc107', '#0dcaf0', '#dc3545', '#adb5bd'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { boxWidth: 14, font: { size: 11 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const val = Number(ctx.raw || 0);
                                    const tot = sum.total || 1;
                                    const pct = ((val / tot) * 100).toFixed(1);
                                    return `${ctx.label}: ${fmtNum(val)} KK (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });

            const dayaArr = analytics.daya_breakdown || [];
            const ctxDaya = document.getElementById('chartDayaPln').getContext('2d');
            if (chartDaya) chartDaya.destroy();

            chartDaya = new Chart(ctxDaya, {
                type: 'bar',
                data: {
                    labels: dayaArr.map(d => d.label),
                    datasets: [{
                        label: 'Jumlah Keluarga (KK)',
                        data: dayaArr.map(d => d.count),
                        backgroundColor: ['#0d6efd', '#20c997', '#fd7e14', '#6f42c1', '#adb5bd'],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const item = dayaArr[ctx.dataIndex] || { count: 0, percent: 0 };
                                    return `${fmtNum(item.count)} KK (${item.percent}% dari PLN Meteran)`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // ==========================================
        // 🧮 RENDER KPI & TABEL RINGKASAN ANALITIK
        // ==========================================
        function updateAnalyticsUI(analytics) {
            if (!analytics || !analytics.summary) return;
            const s = analytics.summary;
            currentWilayahLevel = analytics.wilayah_level || 'RW';

            $('#badgeTotalTop').text(`Total: ${fmtNum(s.total)} KK`);
            $('#badgeCountBnba').text(fmtNum(s.total));
            $('#badgeTerdataInfo').text(`${s.pct_terdata}% Terdata (${fmtNum(s.total_terdata)} KK)`);
            $('#badgeMultiMeterInfo').text(`${fmtNum(analytics.multi_meter_cnt)} KK Multi-Meteran`);
            $('#lblWilayahLevel').text(currentWilayahLevel);

            $('#kpiPlnMeteran').html(`${fmtNum(s.pln_meteran.count)} <small class="fw-normal text-muted">(${s.pln_meteran.percent}%)</small>`);
            $('#kpiPlnTanpa').html(`${fmtNum(s.pln_tanpa.count)} <small class="fw-normal text-muted">(${s.pln_tanpa.percent}%)</small>`);
            $('#kpiNonPln').html(`${fmtNum(s.non_pln.count)} <small class="fw-normal text-muted">(${s.non_pln.percent}%)</small>`);
            $('#kpiBukanListrik').html(`${fmtNum(s.bukan_listrik.count)} <small class="fw-normal text-muted">(${s.bukan_listrik.percent}%)</small>`);
            $('#kpiBelumTerdata').html(`${fmtNum(s.belum_terdata.count)} <small class="fw-normal text-muted">(${s.belum_terdata.percent}%)</small>`);

            // Insights
            const ins = analytics.insights || {};
            $('#valInsightTanpaMeter').text(`${fmtNum(ins.desil14_tanpa_meteran || 0)} KK`);
            $('#valInsightGelap').text(`${fmtNum(ins.desil14_non_pln_gelap || 0)} KK`);
            $('#valInsightBansosTinggi').text(`${fmtNum(ins.bansos_daya_1300_plus || 0)} KK`);

            // Highlight KPI card sesuai filter kategori
            const activeKat = $('#filter_kategori').val();
            $('.btn-kpi-filter').removeClass('active-filter');
            if (activeKat && activeKat !== 'all') {
                $(`.btn-kpi-filter[data-kategori="${activeKat}"]`).addClass('active-filter');
            }

            // Render Korelasi Desil
            const $desilBody = $('#tblKorelasiDesil tbody').empty();
            const $desilFoot = $('#tblKorelasiDesil tfoot').empty();
            let td = [0, 0, 0, 0];
            Object.entries(analytics.korelasi_desil || {}).forEach(([kat, row]) => {
                const d14 = Number(row['Desil 1–4'] || 0);
                const d510 = Number(row['Desil 5–10'] || 0);
                const d0 = Number(row['Belum Ada / Desil 0'] || 0);
                const tot = Number(row['Total'] || 0);
                td[0] += d14; td[1] += d510; td[2] += d0; td[3] += tot;
                $desilBody.append(`
                    <tr>
                        <td class="small fw-semibold">${escHtml(kat)}</td>
                        <td class="text-end">${fmtNum(d14)}</td>
                        <td class="text-end">${fmtNum(d510)}</td>
                        <td class="text-end">${fmtNum(d0)}</td>
                        <td class="text-end fw-bold">${fmtNum(tot)}</td>
                    </tr>
                `);
            });
            $desilFoot.html(`
                <tr>
                    <td class="text-start">TOTAL</td>
                    <td>${fmtNum(td[0])}</td>
                    <td>${fmtNum(td[1])}</td>
                    <td>${fmtNum(td[2])}</td>
                    <td>${fmtNum(td[3])}</td>
                </tr>
            `);

            // Render Korelasi Bansos
            const $banBody = $('#tblKorelasiBansos tbody').empty();
            const $banFoot = $('#tblKorelasiBansos tfoot').empty();
            let tb = [0, 0, 0, 0, 0, 0];
            Object.entries(analytics.korelasi_bansos || {}).forEach(([kat, row]) => {
                const c = [
                    Number(row['PKH'] || 0),
                    Number(row['SEMBAKO'] || 0),
                    Number(row['PKH + SEMBAKO'] || 0),
                    Number(row['Total Penerima'] || 0),
                    Number(row['Non-Bansos'] || 0),
                    Number(row['Total'] || 0)
                ];
                c.forEach((v, idx) => tb[idx] += v);
                $banBody.append(`
                    <tr>
                        <td class="small fw-semibold">${escHtml(kat)}</td>
                        <td class="text-end">${fmtNum(c[0])}</td>
                        <td class="text-end">${fmtNum(c[1])}</td>
                        <td class="text-end">${fmtNum(c[2])}</td>
                        <td class="text-end fw-bold text-primary">${fmtNum(c[3])}</td>
                        <td class="text-end">${fmtNum(c[4])}</td>
                        <td class="text-end fw-bold">${fmtNum(c[5])}</td>
                    </tr>
                `);
            });
            $banFoot.html(`
                <tr>
                    <td class="text-start">TOTAL</td>
                    <td>${fmtNum(tb[0])}</td>
                    <td>${fmtNum(tb[1])}</td>
                    <td>${fmtNum(tb[2])}</td>
                    <td class="text-primary">${fmtNum(tb[3])}</td>
                    <td>${fmtNum(tb[4])}</td>
                    <td>${fmtNum(tb[5])}</td>
                </tr>
            `);

            // Render Rekap Wilayah
            const $wilBody = $('#tblRekapWilayah tbody').empty();
            const $wilFoot = $('#tblRekapWilayah tfoot').empty();
            let tw = [0, 0, 0, 0, 0, 0, 0, 0, 0];
            const wilRows = analytics.wilayah_rekap || [];
            if (wilRows.length === 0) {
                $wilBody.append('<tr><td colspan="12" class="text-center text-muted py-3">Tidak ada data untuk filter saat ini.</td></tr>');
            } else {
                wilRows.forEach((w, idx) => {
                    const nums = [
                        Number(w.total || 0),
                        Number(w.pln_meteran || 0),
                        Number(w.pln_tanpa || 0),
                        Number(w.non_pln || 0),
                        Number(w.bukan_listrik || 0),
                        Number(w.belum_terdata || 0),
                        Number(w.daya_450 || 0),
                        Number(w.daya_900 || 0),
                        Number(w.daya_1300_plus || 0)
                    ];
                    nums.forEach((v, i) => tw[i] += v);
                    $wilBody.append(`
                        <tr class="table-wilayah-row" data-key="${escHtml(w.key)}" data-level="${escHtml(currentWilayahLevel)}">
                            <td class="text-center">${idx + 1}</td>
                            <td class="fw-semibold text-primary">${escHtml(w.label)}</td>
                            <td class="text-end fw-bold">${fmtNum(nums[0])}</td>
                            <td class="text-end text-success fw-semibold">${fmtNum(nums[1])}</td>
                            <td class="text-end fw-semibold" style="color:#997404;">${fmtNum(nums[2])}</td>
                            <td class="text-end fw-semibold" style="color:#055160;">${fmtNum(nums[3])}</td>
                            <td class="text-end text-danger fw-semibold">${fmtNum(nums[4])}</td>
                            <td class="text-end text-muted">${fmtNum(nums[5])}</td>
                            <td class="text-end">${fmtNum(nums[6])}</td>
                            <td class="text-end">${fmtNum(nums[7])}</td>
                            <td class="text-end">${fmtNum(nums[8])}</td>
                            <td class="text-end"><span class="badge ${w.pct_terdata >= 80 ? 'bg-success text-white' : (w.pct_terdata >= 40 ? 'bg-warning text-dark' : 'bg-secondary text-white')}">${w.pct_terdata}%</span></td>
                        </tr>
                    `);
                });
            }

            const totalTerdataWil = tw[0] - tw[5];
            const pctTotalTerdata = tw[0] > 0 ? ((totalTerdataWil / tw[0]) * 100).toFixed(1) : '0.0';
            $wilFoot.html(`
                <tr>
                    <td colspan="2" class="text-start">TOTAL KESELURUHAN</td>
                    <td class="text-end">${fmtNum(tw[0])}</td>
                    <td class="text-end">${fmtNum(tw[1])}</td>
                    <td class="text-end">${fmtNum(tw[2])}</td>
                    <td class="text-end">${fmtNum(tw[3])}</td>
                    <td class="text-end">${fmtNum(tw[4])}</td>
                    <td class="text-end">${fmtNum(tw[5])}</td>
                    <td class="text-end">${fmtNum(tw[6])}</td>
                    <td class="text-end">${fmtNum(tw[7])}</td>
                    <td class="text-end">${fmtNum(tw[8])}</td>
                    <td class="text-end">${pctTotalTerdata}%</td>
                </tr>
            `);

            renderCharts(analytics);
        }

        // ==========================================
        // 📋 INISIALISASI DATATABLES BNBA
        // ==========================================
        function badgeKategoriHtml(kat) {
            switch (kat) {
                case 'Listrik PLN dengan meteran':
                    return '<span class="badge bg-success"><i class="fas fa-plug me-1"></i> PLN dgn Meteran</span>';
                case 'Listrik PLN tanpa meteran':
                    return '<span class="badge bg-warning text-dark"><i class="fas fa-code-branch me-1"></i> PLN Tanpa Meteran</span>';
                case 'Listrik non-PLN':
                    return '<span class="badge bg-info text-dark"><i class="fas fa-solar-panel me-1"></i> Non-PLN</span>';
                case 'Bukan listrik':
                    return '<span class="badge bg-danger"><i class="fas fa-fire me-1"></i> Bukan Listrik</span>';
                default:
                    return '<span class="badge bg-secondary"><i class="fas fa-question-circle me-1"></i> Belum Terdata</span>';
            }
        }

        function badgeSumberHtml(src) {
            if (src === 'Usulan Verified') return '<span class="badge bg-success text-white">Verified</span>';
            if (src === 'Usulan Submitted') return '<span class="badge bg-info text-dark">Submitted</span>';
            if (src === 'Usulan Draft') return '<span class="badge bg-warning text-dark">Draft</span>';
            if (src === 'Master RT') return '<span class="badge bg-primary text-white">Master RT</span>';
            return '<span class="badge bg-light text-muted border">-</span>';
        }

        const table = $('#tableLaporanPenerangan').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            deferRender: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[4, 'asc']],
            language: {
                emptyTable: 'Tidak ada data yang tersedia pada tabel ini',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ keluarga',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 keluarga',
                infoFiltered: '(disaring dari _MAX_ total keluarga)',
                lengthMenu: 'Tampilkan _MENU_ baris',
                loadingRecords: 'Memuat...',
                processing: 'Memproses data...',
                search: 'Cari:',
                zeroRecords: 'Tidak ditemukan data keluarga yang cocok',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            },
            ajax: {
                url: '<?= base_url('dtsen/laporan-penerangan/datatable') ?>',
                type: 'POST',
                data: function(d) {
                    d[csrfName]            = csrfHash;
                    d.filter_desa          = $('#filter_desa').val();
                    d.filter_rw            = $('#filter_rw').val();
                    d.filter_rt            = $('#filter_rt').val();
                    d.filter_sumber_mode   = $('#filter_sumber_mode').val();
                    d.filter_kategori      = $('#filter_kategori').val();
                    d.filter_daya          = $('#filter_daya').val();
                    d.filter_desil         = $('#filter_desil').val();
                    d.filter_bansos        = $('#filter_bansos').val();
                    d.include_analytics    = includeAnalyticsFlag;
                },
                dataSrc: function(json) {
                    if (json && json.analytics) {
                        updateAnalyticsUI(json.analytics);
                        includeAnalyticsFlag = 0; // Paging/search selanjutnya tidak perlu hitung ulang grafik
                    }
                    return (json && json.data) ? json.data : [];
                }
            },
            columns: [
                { data: 'no', orderable: false, className: 'text-center' },
                {
                    data: 'kepala_keluarga',
                    render: function(data, type, row) {
                        return `<div class="fw-bold text-dark">${escHtml(data)}</div>` +
                               (row.nama_desa && row.nama_desa !== '-' ? `<small class="text-muted">${escHtml(row.nama_desa)}</small>` : '');
                    }
                },
                {
                    data: 'no_kk',
                    render: function(data) {
                        if (!data || data === '-') return '-';
                        return `${maskNumberJS(data)}
                                <button type="button" class="btn btn-xs btn-link p-0 ms-1 text-secondary btnCopyNoKK" data-value="${escHtml(data)}" title="Salin No. KK">
                                    <i class="far fa-copy"></i>
                                </button>`;
                    }
                },
                {
                    data: 'alamat',
                    render: d => `<small>${escHtml(d || '-')}</small>`
                },
                {
                    data: 'rw',
                    className: 'text-center text-nowrap',
                    render: (d, t, row) => `RW ${escHtml(row.rw)} / RT ${escHtml(row.rt)}`
                },
                {
                    data: 'sumber_penerangan',
                    className: 'text-center',
                    render: d => badgeKategoriHtml(d)
                },
                {
                    data: 'daya_raw',
                    render: function(d, t, row) {
                        if (row.sumber_penerangan !== 'Listrik PLN dengan meteran') {
                            return '<span class="text-muted small">-</span>';
                        }
                        let html = `<div class="fw-bold small">${escHtml(d || 'Belum Diisi')}</div>`;
                        if (Number(row.jumlah_meteran) > 1) {
                            html += `<span class="badge bg-primary text-white">${row.jumlah_meteran} Meteran</span> `;
                        }
                        if (row.nomor_pelanggan) {
                            html += `<div class="text-muted" style="font-size: 0.72rem;">IDPEL: ${escHtml(row.nomor_pelanggan)}</div>`;
                        }
                        return html;
                    }
                },
                {
                    data: 'desil',
                    className: 'text-center',
                    render: function(d) {
                        if (d === null || d === undefined || d === '') return '<span class="badge bg-light text-muted border">-</span>';
                        const num = Number(d);
                        const cls = (num >= 1 && num <= 4) ? 'bg-danger' : (num >= 5 ? 'bg-secondary' : 'bg-dark');
                        return `<span class="badge ${cls}">Desil ${num}</span>`;
                    }
                },
                {
                    data: 'bansos',
                    className: 'text-center',
                    render: function(d, t, row) {
                        if (!d || d === '-') return '<span class="text-muted small">-</span>';
                        const cls = d.includes('+') ? 'bg-purple text-white' : (d === 'PKH' ? 'bg-primary' : 'bg-info text-dark');
                        const bgInline = d.includes('+') ? 'style="background-color:#6f42c1;"' : '';
                        return `<span class="badge ${cls}" ${bgInline} title="${escHtml(row.bansos_tahap || '')}">${escHtml(d)}</span>`;
                    }
                },
                {
                    data: 'sumber_data',
                    className: 'text-center',
                    render: d => badgeSumberHtml(d)
                },
                {
                    data: 'id_kk_encoded',
                    orderable: false,
                    className: 'text-center',
                    render: function(enc) {
                        return `<a href="<?= base_url('pembaruan-keluarga/detail') ?>/${enc}" target="_blank" class="btn btn-xs btn-outline-primary" title="Buka Detail Keluarga / Tab Rumah">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>`;
                    }
                }
            ]
        });

        function reloadWithAnalytics() {
            includeAnalyticsFlag = 1;
            table.ajax.reload();
        }

        function switchToBnbaTab() {
            const el = document.getElementById('nav-bnba-tab');
            if (!el) return;
            if (window.BS5 && window.BS5.Tab) {
                try {
                    window.BS5.Tab.getOrCreateInstance(el).show();
                    return;
                } catch (e) {}
            }
            $(el).tab('show');
        }

        // Event Filter Dropdown
        $('#filter_desa').on('change', function() {
            const desa = $(this).val();
            const $rw = $('#filter_rw');
            const $rt = $('#filter_rt');
            $rw.empty().append('<option value="all">Semua</option>');
            $rt.empty().append('<option value="all">Semua</option>').prop('disabled', true);

            $.getJSON('<?= base_url('dtsen/laporan-penerangan/rw-by-desa') ?>', { desa: desa }, function(list) {
                (list || []).forEach(rw => $rw.append(`<option value="${escHtml(rw)}">RW ${escHtml(rw)}</option>`));
                reloadWithAnalytics();
            });
        });

        $('#filter_rw').on('change', function() {
            const rw = $(this).val();
            const desa = $('#filter_desa').val();
            const $rt = $('#filter_rt');
            $rt.empty().append('<option value="all">Semua</option>');

            if (rw && rw !== 'all') {
                $.getJSON('<?= base_url('dtsen/laporan-penerangan/rt-by-rw') ?>/' + encodeURIComponent(rw), { desa: desa }, function(list) {
                    if (list && list.length > 0) {
                        list.forEach(rt => $rt.append(`<option value="${escHtml(rt)}">RT ${escHtml(rt)}</option>`));
                        $rt.prop('disabled', false);
                    } else {
                        $rt.prop('disabled', true);
                    }
                    reloadWithAnalytics();
                });
            } else {
                $rt.prop('disabled', true);
                reloadWithAnalytics();
            }
        });

        $('#filter_rt, #filter_sumber_mode, #filter_kategori, #filter_daya, #filter_desil, #filter_bansos').on('change', reloadWithAnalytics);

        // Klik Kartu KPI -> Toggle Filter Kategori
        $('.btn-kpi-filter').on('click', function() {
            const kat = $(this).data('kategori');
            const cur = $('#filter_kategori').val();
            $('#filter_kategori').val(cur === kat ? 'all' : kat);
            reloadWithAnalytics();
        });

        // Klik Insight Sorotan -> Filter & pindah ke Tab BNBA
        $('#insightDesilTanpaMeteran').on('click', function() {
            $('#filter_desil').val('1_4');
            $('#filter_kategori').val('Listrik PLN tanpa meteran');
            $('#filter_daya').val('all');
            switchToBnbaTab();
            reloadWithAnalytics();
        });

        $('#insightDesilBukanListrik').on('click', function() {
            $('#filter_desil').val('1_4');
            $('#filter_kategori').val('non_pln_atau_gelap');
            $('#filter_daya').val('all');
            switchToBnbaTab();
            reloadWithAnalytics();
        });

        $('#insightBansosDayaTinggi').on('click', function() {
            $('#filter_bansos').val('PENERIMA');
            $('#filter_kategori').val('Listrik PLN dengan meteran');
            $('#filter_daya').val('1300_plus');
            switchToBnbaTab();
            reloadWithAnalytics();
        });

        // Klik baris Rekap Wilayah -> Drill down
        $(document).on('click', '.table-wilayah-row', function() {
            const level = $(this).data('level');
            const key   = String($(this).data('key') || '');
            if (!key || key === '-') return;

            if (level === 'Desa') {
                $('#filter_desa').val(key).trigger('change');
            } else if (level === 'RW') {
                $('#filter_rw').val(key).trigger('change');
            } else if (level === 'RT') {
                $('#filter_rt').val(key).trigger('change');
                switchToBnbaTab();
            }
        });

        // Reset Filter
        $('#btnResetFilter').on('click', function() {
            <?php if ((int) $role_id < 3): ?>
                $('#filter_desa').val('<?= esc($default_desa) ?>');
            <?php endif; ?>
            $('#filter_rw').val('all');
            $('#filter_rt').empty().append('<option value="all">Semua</option>').prop('disabled', true);
            $('#filter_sumber_mode').val('all');
            $('#filter_kategori').val('all');
            $('#filter_daya').val('all');
            $('#filter_desil').val('all');
            $('#filter_bansos').val('all');
            table.search('');
            reloadWithAnalytics();
        });

        // Penyesuaian FixedHeader saat ganti tab
        $('button[data-bs-toggle="tab"], button[data-toggle="tab"]').on('shown.bs.tab', function() {
            table.columns.adjust();
            if (table.fixedHeader && typeof table.fixedHeader.adjust === 'function') {
                table.fixedHeader.adjust();
            }
        });

        // Bangun Query Params untuk Export & Cetak
        function buildQueryParams(extra = {}) {
            const params = new URLSearchParams({
                filter_desa:        $('#filter_desa').val() || '',
                filter_rw:          $('#filter_rw').val() || 'all',
                filter_rt:          $('#filter_rt').val() || 'all',
                filter_sumber_mode: $('#filter_sumber_mode').val() || 'all',
                filter_kategori:    $('#filter_kategori').val() || 'all',
                filter_daya:        $('#filter_daya').val() || 'all',
                filter_desil:       $('#filter_desil').val() || 'all',
                filter_bansos:      $('#filter_bansos').val() || 'all',
                search:             table.search() || ''
            });
            Object.entries(extra).forEach(([k, v]) => params.set(k, v));
            return params.toString();
        }

        $('#btnExportExcel').on('click', function() {
            window.location.href = '<?= base_url('dtsen/laporan-penerangan/export-excel') ?>?' + buildQueryParams();
        });

        function openPrintWindow(withLampiran) {
            const url = '<?= base_url('dtsen/laporan-penerangan/cetak') ?>?' + buildQueryParams({ lampiran: withLampiran ? '1' : '0' });
            window.open(url, '_blank');
        }

        $('#btnCetakRingkas, #optCetakRingkas').on('click', function(e) {
            e.preventDefault();
            openPrintWindow(false);
        });

        $('#optCetakLengkap').on('click', function(e) {
            e.preventDefault();
            openPrintWindow(true);
        });
    });
</script>

<?= $this->endSection(); ?>
