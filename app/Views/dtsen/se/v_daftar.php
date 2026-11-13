<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-0">
    <?= $this->include('dtsen/se/layout_nav') ?>

    <section class="content">
        <div class="card shadow-sm border-danger">
            <div class="card-body tab-content">
                <!-- ===================== TAB 1: DAFTAR KELUARGA ===================== -->
                <div class="tab-pane fade show active" id="tabDaftar" role="tabpanel">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body py-3">

                            <!-- ================= HEADER: FILTER + ACTION ================= -->
                            <div class="row g-2 align-items-end">

                                <!-- ================= FILTER BAR ================= -->
                                <div class="col-lg">
                                    <div class="row g-2 align-items-end" id="filterBarKeluarga">

                                        <!-- 👤 PETUGAS ENTRI (WilayahFilterTrait) -->
                                        <?php
                                        $isPetugas   = ((int) ($role_id ?? 0)) >= 4;
                                        $myId        = session()->get('id');
                                        $myName      = session()->get('fullname') ?? 'Saya';
                                        $petugasList = $petugas_list ?? [];
                                        ?>
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Petugas Entri</label>
                                            <select class="form-select form-select-sm" id="filterPetugas"
                                                <?= $isPetugas ? 'disabled title="Data dikunci sesuai wilayah tugas Anda"' : 'title="Pilih petugas untuk melihat data sesuai wilayah tugasnya"' ?>>
                                                <?php if ($isPetugas): ?>
                                                    <option value="<?= esc($myId) ?>" selected><?= esc(ucwords(strtolower($myName))) ?></option>
                                                <?php else: ?>
                                                    <option value="">Semua Petugas</option>
                                                    <?php foreach ($petugasList as $p): ?>
                                                        <option value="<?= esc($p['id']) ?>" title="<?= esc($p['wilayah_tugas'] ?: ('RW ' . ($p['level'] ?: '-'))) ?>">
                                                            <?= esc(ucwords(strtolower($p['fullname']))) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <!-- RW -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Wilayah (RW)</label>
                                            <select class="form-select form-select-sm" id="filterRW">
                                                <option value="">Semua RW</option>
                                            </select>
                                        </div>

                                        <!-- RT -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">RT</label>
                                            <select class="form-select form-select-sm" id="filterRT" disabled>
                                                <option value="">Semua RT</option>
                                            </select>
                                        </div>

                                        <!-- 📌 STATUS -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Status</label>
                                            <select class="form-select form-select-sm" id="filterStatus">
                                                <option value="">Semua Status</option>
                                                <option value="none">Belum Ada Pembaruan</option>
                                                <option value="draft">Draft</option>
                                                <option value="submitted">Submitted</option>
                                                <option value="verified">Verified</option>
                                            </select>
                                        </div>

                                        <!-- 🛡️ PERLINSOS -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Perlinsos</label>
                                            <select class="form-select form-select-sm" id="filterPerlinsos">
                                                <option value="">Semua</option>
                                                <option value="terverifikasi">Terverifikasi</option>
                                                <option value="belum_verifikasi">Belum Verifikasi</option>
                                                <option value="none">Belum Di-entri</option>
                                            </select>
                                        </div>

                                        <!-- 📊 DESIL -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Desil</label>
                                            <select class="form-select form-select-sm" id="filterDesil">
                                                <option value="">Semua</option>
                                                <option value="belum">Belum</option>
                                                <?php for ($i = 0; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>">Desil <?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>

                                        <!-- 🎁 BANSOS -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Bansos</label>
                                            <select class="form-select form-select-sm" id="filterBansos">
                                                <option value="">Semua</option>
                                                <option value="PKH">PKH</option>
                                                <option value="SEMBAKO">SEMBAKO</option>
                                                <option value="PKH + SEMBAKO">PKH + SEMBAKO</option>
                                                <option value="none">Tidak Ada</option>
                                            </select>
                                        </div>

                                        <!-- 🔄 RESET -->
                                        <div class="col-auto">
                                            <button class="btn btn-outline-secondary btn-sm" id="btnResetFilter">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                <!-- ================= ACTION BUTTON ================= -->
                                <div class="col-lg-auto ms-lg-auto">
                                    <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center">
                                        <!-- batasi akses hanya untuk role_id <4 -->
                                        <?php if ($role_id < 4): ?>
                                            <button id="btnSyncGlobal"
                                                class="btn btn-outline-success btn-sm shadow-sm"
                                                title="Sinkronisasi seluruh desil nasional dengan histori SINDEN berdasarkan periode rilis resmi">
                                                <i class="fas fa-cloud-download-alt me-1"></i> Sync Desil Nasional
                                            </button>
                                        <?php endif; ?>
                                        <div class="btn-group" role="group">
                                            <button id="btnReloadKeluarga" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-sync-alt"></i> Muat Ulang
                                            </button>
                                            <button id="btnTambahKeluarga" class="btn btn-primary btn-sm">
                                                <i class="fas fa-user-plus"></i> Tambah Keluarga
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- ================= END HEADER ================= -->

                        </div>
                    </div>

                    <table id="tableKeluarga" class="table table-striped table-hover nowrap w-100">
                        <thead class="text-light bg-primary text-center">
                            <tr>
                                <th></th>
                                <th>No.</th>
                                <th>Kepala Keluarga</th>
                                <th>No KK</th>
                                <th>Alamat</th>
                                <th>Wilayah</th>
                                <th>Status</th> <!-- 🆕 -->
                                <th>Desil</th>
                                <th>Bansos</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ============================================================== -->
<!-- 🚀 ZONA MERDEKA: OFFCANVAS RIWAYAT DESIL & MODAL -->
<!-- ============================================================== -->
<?php
$roleId = (int) ($role_id ?? session()->get('role_id') ?? 0);
$isEditableUser = ($roleId <= 4);
?>
<div class="offcanvas offcanvas-end shadow-lg" id="offcanvasChartDesil" aria-labelledby="offcanvasChartDesilLabel" style="width: 800px; max-width: 100vw;">

    <!-- 🚀 1. HEADER: Logo, Judul, & Identitas KK -->
    <div class="offcanvas-header bg-primary text-white d-flex flex-column align-items-start pb-3" style="border-bottom: 3px solid #ffc107;">
        <div class="d-flex justify-content-between w-100 align-items-center mb-2">
            <div class="d-flex align-items-center">
                <img src="<?= function_exists('logoApp') ? logoApp() : base_url('assets/img/logo.png') ?>" alt="Logo" class="p-1 me-2" style="width: 45px; height: 45px; object-fit: contain;" onerror="this.outerHTML='<i class=\'fas fa-chart-pie fa-2x me-2 text-warning\'></i>'">
                <div>
                    <h5 class="offcanvas-title fw-bold mb-0" id="offcanvasChartDesilLabel" style="line-height: 1.2;">Riwayat Desil Keluarga</h5>
                    <small class="text-white-50" style="font-size: 0.75rem;"><?= function_exists('titleApp') ? titleApp() : 'SINDEN' ?></small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="mt-2 small w-100 bg-white bg-opacity-10 p-2 rounded">
            <div class="mb-1">
                <i class="fas fa-id-card me-2 text-warning"></i> No. KK:
                <strong id="offcanvasNoKk" class="text-black copy-text-direct" style="cursor: pointer;" title="Klik untuk menyalin" data-value="">
                    -
                </strong>
            </div>
            <div><i class="fas fa-user-circle me-2 text-warning"></i> Kepala Keluarga: <strong id="offcanvasNamaKepalaKeluarga" class="text-black">-</strong></div>
        </div>
    </div>

    <!-- 🚀 2. BODY: Tombol Aksi & Render Grafik -->
    <div class="offcanvas-body p-4 flex-grow-1">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3 pb-3 border-bottom">
            <?php if ($isEditableUser && $roleId <= 3): ?>
                <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold btnHistoricalDesil"><i class="fas fa-history me-1"></i> Tambah Snapshot</button>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold btnUpdateDesil" data-id="" data-nokk="" data-nama="" data-alamat="" data-desil=""><i class="fas fa-hand-holding-heart me-1"></i> Update Desil</button>
                <button type="button" id="btnSyncDesil" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold"><i class="fas fa-sync-alt me-1"></i> Sync</button>
            <?php endif; ?>
        </div>
        <div id="desilChart" style="min-height:350px;"></div>
        <div id="desilTrendInfo" class="mt-3 small text-muted"></div>
    </div>

    <!-- 🚀 3. FOOTER: Informasi Hak Cipta & Modul -->
    <div class="offcanvas-footer mt-auto bg-light border-top p-3 text-center">
        <h6 class="fw-bold text-primary mb-1" style="font-size: 0.9rem;">
            <i class="fas fa-shield-alt me-1"></i> <?= nameApp(); ?> - <?= titleApp(); ?>
        </h6>
        <div class="text-muted" style="font-size: 0.75rem;">
            &copy; <?= date('Y') ?> Pemerintah Desa Pasirlangu / Badan Pusat Statistik. Hak Cipta Dilindungi.<br>
            <span class="fst-italic">Modul Analisis Desil & Kesejahteraan Sosial</span>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistoricalDesil" data-bs-focus="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Snapshot Historis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formHistoricalDesil">
                    <input type="hidden" name="id_kk" id="historical_id_kk" value="">
                    <!-- Tahun -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <select name="tahun" class="form-select" required>
                            <?php $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 2025; $year--): ?>
                                <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>><?= $year ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <!-- Triwulan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Periode Triwulan</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold text-primary">TW</span>
                            <input type="number" class="form-control" name="triwulan" id="inputTriwulan" step="0.1" min="1" max="4.9" value="<?= ceil(date('n') / 3) ?>" placeholder="Contoh: 3 atau 3.1" required>
                        </div>
                        <small class="text-muted fst-italic">Digunakan untuk penandaan pada grafik (misal: 1, 2, atau 3.1).</small>
                    </div>
                    <!-- Desil -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Desil</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                <?php $warna = ($i == 0) ? 'secondary' : (($i <= 3) ? 'success' : (($i <= 5) ? 'warning' : 'danger')); ?>
                                <input type="radio" class="btn-check" name="desil" id="desil<?= $i ?>" value="<?= $i ?>" required>
                                <label class="btn btn-outline-<?= $warna ?> rounded-pill px-3" for="desil<?= $i ?>">Desil <?= $i ?></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" id="btnSaveHistorical" class="btn btn-primary">Simpan</button></div>
        </div>
    </div>
</div>

<!-- 🚀 KUNCI SAKTI: Panggil modal dari file terpisah agar dirender ke halaman ini -->
<?= $this->include('dtsen/se/modal_input_desil') ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    $(document).ready(function() {

        // =========================
        // 🔎 FILTER STATE (GLOBAL)
        // =========================
        const desilHistoryCache = {};
        const filterKeluarga = {
            petugas: $('#filterPetugas').val() || '',
            rw: '',
            rt: '',
            status: '',
            perlinsos: '',
            desil: '',
            bansos: ''
        };

        // 🔐 Daftar RW mengikuti wilayah tugas (akun petugas / petugas yang dipilih admin)
        function loadRW() {
            $.getJSON('/dtsen-se/list-rw', {
                petugas: $('#filterPetugas').val() || ''
            }, function(res) {
                const $rw = $('#filterRW');
                $rw.empty().append('<option value="">Semua RW</option>');

                (res.data || []).forEach(rw => {
                    $rw.append(`<option value="${rw}">RW ${rw}</option>`);
                });
            });
        }

        loadRW();

        // ==========================================
        // 🛡️ FUNGSI BANTUAN: SENSOR DATA SENSITIF (JS)
        // ==========================================
        function maskNumberJS(number) {
            if (!number || number === '-' || number === 'NOKKS') return number || '-';

            let numStr = number.toString().trim();
            if (numStr.length <= 8) return numStr;

            let masked = numStr.substring(0, 8) + '*'.repeat(numStr.length - 8);

            return `<span class="fw-bold text-primary" style="cursor:pointer;" 
                      onmouseenter="this.innerText='${numStr}'" 
                      onmouseleave="this.innerText='${masked}'" 
                      ontouchstart="this.innerText='${numStr}'" 
                      ontouchend="this.innerText='${masked}'" 
                      title="Tahan/Arahkan kursor untuk melihat utuh">${masked}</span>`;
        }

        // ========================= 🔵 TABLE DAFTAR KELUARGA =========================
        const tableKeluarga = $('#tableKeluarga').DataTable({
            stateSave: false,

            ajax: {
                url: '/dtsen-se/tabel_data',
                type: 'POST', // 🚀 KEMBALIKAN KE POST SESUAI ROUTE CI4 JENDERAL
                cache: false,
                data: function(d) {
                    // Bungkus kembali ke dalam variabel filter agar rapi
                    d.filter = filterKeluarga;

                    console.log('📤 Minta Data (POST) dengan Filter:', d.filter);
                },
                dataSrc: function(json) {
                    const rows = (json && json.data) ? json.data : [];
                    rows.forEach(function(r) {
                        if (r.id_kk !== undefined) {
                            desilHistoryCache[String(r.id_kk)] = Array.isArray(r.desil_history) ? r.desil_history : [];
                        }
                    });
                    return rows;
                }
            },

            responsive: true,
            pageLength: 10,
            autoWidth: false,
            order: [], // 🚀 Jangan paksa sort di kolom No

            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },

            // 🚀 KUNCI NOMOR URUT AGAR TIDAK TERPENGARUH SORTING KOLOM LAIN
            drawCallback: function(settings) {
                var api = this.api();
                var info = api.page.info();
                api.column(1, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = formatRibuanID(info.start + i + 1);
                });
            },

            columns: [
                // 🔹 Responsive control
                {
                    data: null,
                    defaultContent: '',
                    className: 'control text-center',
                    orderable: false
                },

                // 🔹 No
                {
                    data: null,
                    defaultContent: '',
                    orderable: false,
                    searchable: false
                },

                // 🔹 Kepala Keluarga
                {
                    data: 'kepala_keluarga',
                    className: 'text-start text-capitalize'
                },

                // 🔹 No KK
                {
                    data: 'no_kk',
                    className: 'text-nowrap text-start',
                    // 🚀 TAMBAHKAN PARAMETER 'type' DI SINI
                    render: function(data, type, row) {

                        // 1. KUNCI SAKTINYA DI SINI MBAH! 
                        // Jika DataTables sedang mencari (filter) atau mengurutkan (sort), berikan angka aslinya.
                        if (type === 'filter' || type === 'sort') {
                            return data;
                        }

                        // 🚀 Panggil fungsi penyensoran
                        let maskedData = maskNumberJS(data);

                        return `
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold">${maskedData}</span>
                        
                        <button 
                            type="button"
                            class="btn btn-outline-secondary btn-xs btnCopyNoKK"
                            data-value="${data}" 
                            title="Salin No KK">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `;
                    }
                },

                // 🔹 Alamat
                {
                    data: 'alamat',
                    className: 'text-start',
                    render: d => d || '-'
                },

                // 🔹 Wilayah
                {
                    data: null,
                    className: 'text-start',
                    render: r => `
                <span class="badge bg-light text-dark border">RW ${r.rw}</span>
                <span class="mx-1">/</span>
                <span class="badge bg-info text-dark">RT ${r.rt}</span>
            `
                },

                // 🔹 Status
                {
                    data: 'usulan_status',
                    className: 'text-start text-nowrap',
                    render: function(status, type, row) {

                        // 1. Status Pembaruan
                        let pembaruanBadge = '<span class="badge bg-secondary">Belum Ada Pembaruan</span>';
                        let pembaruanText = 'Belum Ada Pembaruan';

                        if (status === 'draft' || status === 'submitted') {
                            if (row.is_submitted_ready == 1) {
                                pembaruanBadge = '<span class="badge bg-info text-dark">Submitted</span>';
                                pembaruanText = 'Submitted';
                            } else {
                                pembaruanBadge = '<span class="badge bg-warning text-dark">Draft</span>';
                                pembaruanText = 'Draft';
                            }
                        } else if (status === 'verified' || status === 'diverifikasi') {
                            pembaruanBadge = '<span class="badge bg-primary">Verified</span>';
                            pembaruanText = 'Verified';
                        }

                        // 2. Status Perlinsos
                        let perlinsosBadge = '<span class="text-muted fw-bold">-</span>';
                        let perlinsosText = '-';

                        if (row.perlinsos_status) {
                            if (row.perlinsos_status === 'tersinkron') {
                                perlinsosBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Terverifikasi</span>';
                                perlinsosText = 'Terverifikasi';
                            } else {
                                perlinsosBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Belum Verifikasi</span>';
                                perlinsosText = 'Belum Verifikasi';
                            }
                        }

                        if (type === 'filter' || type === 'sort') {
                            return 'Pembaruan: ' + pembaruanText + ' Perlinsos: ' + perlinsosText;
                        }

                        return `
                            <div class="d-flex flex-column" style="gap: 3px;">
                                <div>
                                    <span class="small text-muted fw-bold me-1">Pembaruan:</span>${pembaruanBadge}
                                </div>
                                <div>
                                    <span class="small text-muted fw-bold me-1">Perlinsos:</span>${perlinsosBadge}
                                </div>
                            </div>
                        `;
                    }
                },

                // 🔹 Desil
                {
                    data: 'kategori_desil',
                    className: 'text-start',
                    render: d => {
                        // 🚀 PERBAIKAN: Pastikan 0 tidak terdeteksi sebagai "Belum"
                        if (d === null || d === '' || d === undefined) {
                            return '<span class="badge bg-secondary">Belum</span>';
                        }

                        const n = parseInt(d);
                        // 🚀 PERBAIKAN: Beri warna khusus (abu-abu/biru) untuk desil 0
                        let warna = 'danger';
                        if (n === 0) warna = 'secondary';
                        else if (n <= 3) warna = 'success';
                        else if (n <= 5) warna = 'warning';

                        return `<span class="badge bg-${warna}">${n}</span>`;
                    }
                },

                // 🔹 Bansos (Dokumentasi Bansos Tahap Terakhir)
                {
                    data: 'bansos',
                    render: function(data, type, row) {
                        if (!data || data === '-' || data === '') {
                            return '<span class="text-muted">-</span>';
                        }

                        if (type === 'filter') {
                            return data + (row.bansos_tahap ? ' ' + row.bansos_tahap : '');
                        }

                        if (type === 'sort') {
                            return data;
                        }

                        let tahapTooltip = row.bansos_tahap ? ` title="${row.bansos_tahap}"` : '';
                        let badgeClass = 'bg-primary';

                        if (data === 'PKH') {
                            badgeClass = 'bg-success';
                        } else if (data === 'SEMBAKO') {
                            badgeClass = 'bg-info text-dark';
                        } else if (data.indexOf('+') !== -1 || data.indexOf(',') !== -1) {
                            badgeClass = 'bg-primary';
                        }

                        return `<span class="badge ${badgeClass} shadow-sm"${tahapTooltip}>${data}</span>`;
                    }
                },

                // 🔹 Aksi
                {
                    data: null,
                    className: 'text-start text-nowrap',
                    orderable: false,
                    searchable: false,
                    render: row => {

                        let btnInputDesil = '';

                        if (row.can_input_desil) {
                            btnInputDesil = `
                                <button type="button" class="btn btn-outline-primary btn-sm btnInputDesil me-1"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasChartDesil"
                                    data-id="${row.id_kk}"
                                    data-nama="${row.kepala_keluarga}"
                                    data-nokk="${row.no_kk}"
                                    data-alamat="${row.alamat}"
                                    data-desil="${row.kategori_desil ?? ''}"
                                    title="Riwayat & Update Desil">
                                    <i class="fas fa-balance-scale"></i>
                                </button>
                            `;
                        }

                        const detailId = row.id_kk_encoded || row.id_kk;
                        return `
                            <a href="/pembaruan-keluarga/detail/${detailId}" 
                            class="btn btn-outline-dark btn-sm me-1">
                                <i class="fas fa-users-cog"></i>
                            </a>

                            ${btnInputDesil}

                            <button class="btn btn-outline-danger btn-sm btnDeleteKeluarga"
                                data-id="${row.id_kk}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        `;
                    }
                },
                // 🔹 Kolom Pencarian Tersembunyi (Anggota Keluarga)
                {
                    data: 'anggota_search',
                    visible: false, // 🚀 Sembunyikan dari tampilan tabel utama
                    searchable: true, // 🚀 Tetap dilacak oleh fitur Search DataTables
                    className: 'never', // 🚀 DataTables Responsive: jangan pernah tampilkan di child row
                    defaultContent: '',
                    render: function(data, type) {
                        // Hanya berikan data saat DataTables melakukan filtering/searching
                        if (type === 'filter') {
                            return data || '';
                        }
                        return '';
                    }
                }
            ]
        });

        // ==============================================================
        // 🎛️ SMART FILTER HANDLER (ANTI-GAGAL / EVENT DELEGATION)
        // ==============================================================

        // 0. Petugas Entri berubah (khusus Admin/Desa) → reset RW/RT ke wilayah tugas petugas terpilih
        $(document).on('change', '#filterPetugas', function() {
            filterKeluarga.petugas = $(this).val() || '';
            filterKeluarga.rw = '';
            filterKeluarga.rt = '';

            $('#filterRW').val('');
            $('#filterRT').html('<option value="">Semua RT</option>').prop('disabled', true);

            loadRW();
            $('#tableKeluarga').DataTable().ajax.reload(null, true);
        });

        // 1. Dengarkan perubahan pada semua filter dari akar dokumen
        $(document).on('change', '#filterRW, #filterRT, #filterStatus, #filterPerlinsos, #filterDesil, #filterBansos', function() {
            // Update parameter filter
            filterKeluarga.petugas = $('#filterPetugas').val() || '';
            filterKeluarga.rw = $('#filterRW').val() || '';
            filterKeluarga.rt = $('#filterRT').val() || '';
            if (this.id === 'filterRW') filterKeluarga.rt = ''; // RT lama tidak berlaku untuk RW baru
            filterKeluarga.status = $('#filterStatus').val() || '';
            filterKeluarga.perlinsos = $('#filterPerlinsos').val() || '';
            filterKeluarga.desil = $('#filterDesil').val() || '';
            filterKeluarga.bansos = $('#filterBansos').val() || '';

            console.log('🎯 Minta Data:', filterKeluarga); // Pantau di Inspect Element -> Console

            // PAKSA reload langsung tembak ke instance tabel (Bypass masalah scope variabel)
            $('#tableKeluarga').DataTable().ajax.reload(null, false);
        });

        // 2. Logika khusus RW (Tarik data RT dari Server)
        $(document).on('change', '#filterRW', function() {
            const rw = $(this).val();
            const $rt = $('#filterRT');

            // Reset UI RT
            $rt.prop('disabled', !rw).html('<option value="">Semua RT</option>');

            if (rw) {
                $rt.append('<option value="" disabled>Memuat...</option>');
                $.getJSON('/dtsen-se/list-rt/' + encodeURIComponent(rw), {
                    petugas: $('#filterPetugas').val() || ''
                }, function(res) {
                    $rt.html('<option value="">Semua RT</option>');
                    if (res.data) {
                        res.data.forEach(rt => {
                            $rt.append(`<option value="${rt}">RT ${rt}</option>`);
                        });
                    }
                });
            }
        });

        // 3. Tombol Reset
        $(document).on('click', '#btnResetFilter', function() {
            // Reset UI
            $('#filterRW, #filterStatus, #filterPerlinsos, #filterDesil, #filterBansos').val('');
            $('#filterRT').html('<option value="">Semua RT</option>').prop('disabled', true);

            // Petugas Entri hanya bisa direset oleh Admin/Desa (untuk petugas: terkunci)
            const $petugas = $('#filterPetugas');
            if (!$petugas.prop('disabled') && $petugas.val() !== '') {
                $petugas.val('');
                loadRW();
            }

            // Reset Parameter
            filterKeluarga.petugas = $petugas.val() || '';
            filterKeluarga.rw = '';
            filterKeluarga.rt = '';
            filterKeluarga.status = '';
            filterKeluarga.perlinsos = '';
            filterKeluarga.desil = '';
            filterKeluarga.bansos = '';

            // Tembak Reload
            $('#tableKeluarga').DataTable().ajax.reload(null, false);
        });

        // 4. Tombol Muat Ulang & Tambah
        $(document).on('click', '#btnReloadKeluarga', function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');

            $('#tableKeluarga').DataTable().ajax.reload(null, false);

            setTimeout(() => {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Muat Ulang');
            }, 800);
        });

        $(document).on('click', '#btnTambahKeluarga', function() {
            window.location.href = '/pembaruan-keluarga/tambah';
        });

        // ==============================================================
        // 🚀 STATE & GRAFIK RIWAYAT DESIL (INSTAN DARI PRELOAD CACHE)
        // ==============================================================
        let currentOffcanvasKkId = null;
        let desilChartInstance = null;

        function renderDesilChartFromData(data) {
            const chartEl = document.querySelector("#desilChart");
            if (!chartEl) return;

            if (desilChartInstance !== null) {
                desilChartInstance.destroy();
                desilChartInstance = null;
            }

            if (!Array.isArray(data) || data.length === 0) {
                chartEl.innerHTML = '<div class="text-center text-muted py-5">Belum ada histori desil.</div>';
                return;
            }

            chartEl.innerHTML = '';
            const categories = data.map(d => d.periode);
            const values = data.map(d => Number(d.desil));

            const options = {
                chart: {
                    type: 'line',
                    height: 320,
                    animations: {
                        enabled: true,
                        speed: 250
                    },
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                    name: 'Desil',
                    data: values
                }],
                xaxis: {
                    categories: categories
                },
                yaxis: {
                    min: 0,
                    max: 10,
                    tickAmount: 10
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: 6,
                    hover: {
                        size: 8
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "Desil " + val;
                        }
                    }
                },
                colors: ['#0d6efd'],
                grid: {
                    borderColor: '#e9ecef'
                }
            };

            desilChartInstance = new ApexCharts(chartEl, options);
            desilChartInstance.render();
        }

        function loadDesilChart(idKK, forceNetwork = false) {
            const targetId = String(idKK || currentOffcanvasKkId || '');
            const chartEl = document.querySelector("#desilChart");
            if (!chartEl || !targetId) return;

            // 🚀 INSTAN: Gunakan data yang sudah di-preload bersama tabel jika tersedia dan tidak dipaksa tarik ulang
            if (!forceNetwork && Object.prototype.hasOwnProperty.call(desilHistoryCache, targetId)) {
                renderDesilChartFromData(desilHistoryCache[targetId]);
                return;
            }

            chartEl.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i>Memuat riwayat desil...</div>';

            fetch("<?= base_url($roleId == 6 ? 'sensus-ekonomi/desil-history' : 'pembaruan-keluarga/desil-history') ?>/" + targetId)
                .then(res => res.json())
                .then(res => {
                    const list = (res.status === 'success' && Array.isArray(res.data)) ? res.data : [];
                    desilHistoryCache[targetId] = list;
                    renderDesilChartFromData(list);
                })
                .catch(() => {
                    chartEl.innerHTML = '<div class="text-center text-danger py-5">Gagal memuat histori desil.</div>';
                });
        }

        window.reloadDesilChart = function(forceNetwork = true) {
            loadDesilChart(currentOffcanvasKkId, forceNetwork);
        };

        document.getElementById('offcanvasChartDesil')?.addEventListener('shown.bs.offcanvas', function() {
            if (desilChartInstance !== null) {
                window.dispatchEvent(new Event('resize'));
            } else if (currentOffcanvasKkId) {
                loadDesilChart(currentOffcanvasKkId, false);
            }
        });

        // ==============================================================
        // 🚀 EVENT: BUKA OFFCANVAS DESIL DARI TABEL DAFTAR KELUARGA
        // ==============================================================
        $(document).on('click', '.btnInputDesil', function() {
            const id = $(this).attr('data-id');
            const nama = $(this).attr('data-nama') || '-';
            const nokk = $(this).attr('data-nokk') || '-';
            const alamat = $(this).attr('data-alamat') || '-';
            const desil = $(this).attr('data-desil') ?? '';

            currentOffcanvasKkId = id;

            // Suntikkan identitas ke header Offcanvas
            $('#offcanvasNoKk').text(nokk).attr('data-value', nokk);
            $('#offcanvasNamaKepalaKeluarga').text(nama);

            // Suntikkan data ke tombol Update Desil & Form Historical di dalam Offcanvas
            $('.btnUpdateDesil').attr({
                'data-id': id,
                'data-nokk': nokk,
                'data-nama': nama,
                'data-alamat': alamat,
                'data-desil': desil
            });
            $('#historical_id_kk').val(id);

            // 🚀 Langsung render grafik dari cache memori (0ms, tanpa tunggu animasi selesai)
            loadDesilChart(id, false);
        });

        // ==============================================================
        // 🚀 EVENT: BUKA MODAL UPDATE DESIL DARI DALAM OFFCANVAS
        // ==============================================================
        $(document).on('click', '.btnUpdateDesil', function(e) {
            e.preventDefault();

            const btn = $(this);
            const id = btn.attr('data-id');
            const nama = btn.attr('data-nama');
            const nokk = btn.attr('data-nokk');
            const alamat = btn.attr('data-alamat');
            const desil = btn.attr('data-desil');

            const modal = $('#modalInputDesil');

            modal.find('#modal_id_kk').val(id);
            modal.find('#modal_no_kk').val(nokk);
            modal.find('#modal_kepala_keluarga').val(nama);
            modal.find('#modal_alamat').val(alamat);

            modal.find('input[name="kategori_desil"]').prop('checked', false);
            if (desil !== undefined && desil !== null && String(desil).trim() !== '') {
                modal.find('input[name="kategori_desil"][value="' + String(desil).trim() + '"]').prop('checked', true);
            }

            const d = new Date();
            const currentYear = d.getFullYear();
            const currentMonth = d.getMonth() + 1;
            const currentTw = Math.ceil(currentMonth / 3);

            modal.find('#tahun_berlaku').val(currentYear);
            modal.find('#triwulan_berlaku').val(currentTw);

            modal.modal('show');
        });

        // ==============================================================
        // 🚀 EVENT: BUKA & SIMPAN MODAL SNAPSHOT HISTORIS DESIL
        // ==============================================================
        $(document).on('click', '.btnHistoricalDesil', function(e) {
            e.preventDefault();
            $('#historical_id_kk').val(currentOffcanvasKkId);
            $('#modalHistoricalDesil').modal('show');
        });

        $('#btnSaveHistorical').on('click', function() {
            const form = $('#formHistoricalDesil');
            const btn = $(this);
            const originalText = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: '/pembaruan-keluarga/add-historical-desil',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html(originalText);
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                        $('#modalHistoricalDesil').modal('hide');
                        $('#formHistoricalDesil')[0].reset();
                        $('#historical_id_kk').val(currentOffcanvasKkId);

                        reloadDesilChart();
                        tableKeluarga.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal menyimpan snapshot historis.', 'error');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(originalText);
                    Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
                }
            });
        });

        // ==============================================================
        // 🚀 EVENT: SYNC DESIL PER KK DARI DALAM OFFCANVAS
        // ==============================================================
        $('#btnSyncDesil').on('click', function() {
            if (!currentOffcanvasKkId) return;

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sync...';

            fetch("<?= base_url('pembaruan-keluarga/sync-desil') ?>/" + currentOffcanvasKkId, {
                    method: "POST",
                    credentials: "same-origin"
                })
                .then(res => res.json())
                .then(res => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync';

                    if (res.status === 'changed') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Desil Berubah',
                            html: `Dari <b>${res.from ?? '-'}</b> menjadi <b>${res.to}</b><br><small>${res.periode ?? ''}</small>`,
                            timer: 1800,
                            showConfirmButton: false
                        });

                        $('.btnUpdateDesil').attr('data-desil', res.to ?? '');
                        reloadDesilChart();
                        tableKeluarga.ajax.reload(null, false);
                    } else if (res.status === 'unchanged') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Tetap',
                            text: 'Desil tidak berubah.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Terjadi kesalahan.'
                        });
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync';
                    Swal.fire('Error', 'Gagal melakukan sinkronisasi.', 'error');
                });
        });

        // ==============================================================
        // 🚀 EVENT: SUBMIT FORM UPDATE DESIL (VIA AJAX)
        // ==============================================================
        $('#formInputDesil').on('submit', function(e) {
            e.preventDefault(); // 🛑 CEGAH BROWSER PINDAH HALAMAN!

            const form = $(this);
            const btnSubmit = form.find('button[type="submit"]');
            const originalText = btnSubmit.html();
            const selectedDesil = form.find('input[name="kategori_desil"]:checked').val();

            // Ubah tombol jadi status loading agar tidak diklik dua kali
            btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(res) {
                    // Kembalikan kondisi tombol
                    btnSubmit.prop('disabled', false).html(originalText);

                    if (res.status === 'success') {
                        // Tampilkan notifikasi sukses elegan
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Kategori desil berhasil diperbarui.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Perbarui atribut data-desil pada tombol di dalam Offcanvas
                        if (selectedDesil !== undefined) {
                            $('.btnUpdateDesil').attr('data-desil', selectedDesil);
                        }

                        // Sembunyikan modal
                        $('#modalInputDesil').modal('hide');

                        // 🚀 REFRESH GRAFIK OFFCANVAS & TABEL TANPA RELOAD HALAMAN
                        reloadDesilChart();
                        tableKeluarga.ajax.reload(null, false);
                    } else {
                        // Jika ada validasi error dari server
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal memperbarui desil.'
                        });
                    }
                },
                error: function(xhr) {
                    // Kembalikan kondisi tombol jika server down/error 500
                    btnSubmit.prop('disabled', false).html(originalText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Gagal terhubung ke server atau terjadi kesalahan sistem.'
                    });
                }
            });
        });

        // 📋 Salin No. KK dari header Offcanvas
        $(document).on('click', '.copy-text-direct', function() {
            const textToCopy = $(this).attr('data-value');
            if (textToCopy && textToCopy.trim() !== '') {
                navigator.clipboard.writeText(textToCopy.trim()).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'success',
                        title: 'No. KK disalin!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            }
        });

        // 🚀 Sabuk Pengaman: Perbaiki scrolling & validasi triwulan desimal
        document.addEventListener('hidden.bs.modal', function() {
            if (document.querySelectorAll('.modal.show').length > 0) {
                document.body.classList.add('modal-open');
            }
        });

        $('#triwulan_berlaku').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
            let val = parseFloat(this.value);
            if (val > 4.9) {
                this.value = '4.9';
            }
        });

        // ... (Kode selanjutnya) ...
        // ========================= 🔥 HAPUS KELUARGA =========================
        $(document).on('click', '.btnDeleteKeluarga', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Hapus Keluarga?',
                html: `
            <p class="mb-2">Sebutkan alasan penghapusan:</p>
            <textarea id="alasanHapus" class="form-control" rows="3" placeholder="Wajib diisi"></textarea>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                preConfirm: () => {
                    const alasan = document.getElementById('alasanHapus').value.trim();
                    if (!alasan) {
                        Swal.showValidationMessage('Alasan wajib diisi!');
                    }
                    return alasan;
                }
            }).then(result => {
                if (!result.isConfirmed) return;

                let alasan = result.value;

                $.post('/dtsen-se/delete', {
                        id_kk: id,
                        alasan: alasan
                    },
                    function(res) {
                        if (res.status) {
                            Swal.fire('Berhasil', res.message, 'success');
                            tableKeluarga.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    }, 'json'
                );
            });
        });

        // ========================= 📋 COPY NO KK =========================
        $(document).on('click', '.btnCopyNoKK', function() {
            const value = $(this).data('value');

            if (!value) return;

            navigator.clipboard.writeText(value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin',
                    text: 'No. KK ' + value + ' berhasil disalin ke clipboard',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top'
                });
            }).catch(() => {
                Swal.fire('Gagal', 'Tidak dapat menyalin No. KK', 'error');
            });
        });

    });

    document.addEventListener("DOMContentLoaded", function() {

        const btn = document.getElementById("btnSyncGlobal");
        if (!btn) return;

        btn.addEventListener("click", function() {
            const currentYear = new Date().getFullYear();
            let yearOptions = '';
            for (let y = currentYear; y >= 2024; y--) {
                yearOptions += `<option value="${y}" ${y === currentYear ? 'selected' : ''}>${y}</option>`;
            }

            Swal.fire({
                title: 'Sync Desil Nasional',
                html: `
                    <div class="text-start small">
                        <div class="alert alert-info py-2 px-3 mb-3 small">
                            <i class="fas fa-info-circle me-1"></i>
                            Pilih <b>Tahun</b> dan <b>Triwulan Rilis Resmi Kemensos</b> yang akan dicatat sebagai histori untuk setiap keluarga.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small mb-1">Tahun Rilis Kemensos:</label>
                            <select id="swalSyncTahun" class="form-select form-select-sm">
                                ${yearOptions}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small mb-1">Periode Triwulan:</label>
                            <select id="swalSyncTriwulan" class="form-select form-select-sm">
                                <option value="1">Triwulan 1 (TW1)</option>
                                <option value="2">Triwulan 2 (TW2)</option>
                                <option value="3" selected>Triwulan 3 (TW3)</option>
                                <option value="4">Triwulan 4 (TW4)</option>
                            </select>
                            <input type="text" id="swalSyncTriwulanCustom" class="form-control form-control-sm mt-2 d-none" placeholder="Contoh: 3.1" inputmode="decimal">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" id="swalCustomTwCheck">
                                <label class="form-check-label text-muted" for="swalCustomTwCheck" style="font-size: 0.78rem;">
                                    Gunakan triwulan desimal/revisi (misal: 3.1)
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-warning py-2 px-3 mb-0 small">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <b>Peringatan:</b> Sistem akan membandingkan desil nasional seluruh KK dan mencatat perubahannya ke periode yang Anda tentukan.
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-sync-alt me-1"></i> Mulai Sinkronisasi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                didOpen: () => {
                    const check = document.getElementById('swalCustomTwCheck');
                    const select = document.getElementById('swalSyncTriwulan');
                    const customInput = document.getElementById('swalSyncTriwulanCustom');
                    if (check && select && customInput) {
                        check.addEventListener('change', function() {
                            if (this.checked) {
                                select.classList.add('d-none');
                                customInput.classList.remove('d-none');
                                customInput.focus();
                            } else {
                                select.classList.remove('d-none');
                                customInput.classList.add('d-none');
                            }
                        });
                    }
                },
                preConfirm: () => {
                    const tahun = document.getElementById('swalSyncTahun').value;
                    const isCustom = document.getElementById('swalCustomTwCheck').checked;
                    let triwulan = isCustom ?
                        document.getElementById('swalSyncTriwulanCustom').value.trim() :
                        document.getElementById('swalSyncTriwulan').value;

                    if (!tahun) {
                        Swal.showValidationMessage('Tahun harus dipilih.');
                        return false;
                    }
                    if (!triwulan || isNaN(triwulan) || parseFloat(triwulan) <= 0 || parseFloat(triwulan) > 4.9) {
                        Swal.showValidationMessage('Periode triwulan tidak valid (harus angka 1 - 4.9).');
                        return false;
                    }

                    return {
                        tahun: tahun,
                        triwulan: triwulan
                    };
                }
            }).then((result) => {

                if (!result.isConfirmed || !result.value) return;

                const {
                    tahun,
                    triwulan
                } = result.value;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sync berjalan...';

                const formData = new FormData();
                formData.append('tahun', tahun);
                formData.append('triwulan', triwulan);

                fetch('/pembaruan-keluarga/sync-desil-global', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {

                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-cloud-download-alt me-1"></i> Sync Desil Nasional';

                        if (res.status === 'cooldown') {

                            let seconds = res.remaining_seconds || 60;

                            Swal.fire({
                                icon: 'warning',
                                title: 'Cooldown Aktif',
                                html: `
                                    <div>
                                        Sinkronisasi hanya dapat dilakukan setiap 1 menit.<br><br>
                                        Silakan tunggu <b><span id="cooldownTimer">${seconds}</span></b> detik lagi.
                                    </div>
                                `,
                                showConfirmButton: false
                            });

                            const interval = setInterval(() => {

                                seconds--;

                                const timerEl = document.getElementById('cooldownTimer');
                                if (timerEl) timerEl.textContent = seconds;

                                if (seconds <= 0) {
                                    clearInterval(interval);
                                    Swal.close();
                                }

                            }, 1000);

                            return;
                        }

                        if (res.status === 'success') {

                            Swal.fire({
                                icon: 'success',
                                title: 'Sinkronisasi Selesai',
                                html: `
                                <div class="text-start small">
                                    <b>Periode Target:</b> ${res.periode}<br>
                                    <b>Total KK dicek:</b> ${res.total}<br>
                                    <b>Berubah & Dicatat:</b> <span class="text-danger fw-bold">${res.changed}</span><br>
                                    <b>Tidak berubah:</b> <span class="text-success">${res.unchanged}</span>
                                </div>
                                `
                            });

                            if ($.fn.DataTable.isDataTable('#tableKeluarga')) {
                                $('#tableKeluarga').DataTable().ajax.reload(null, false);
                            }

                        } else {

                            Swal.fire(
                                'Gagal',
                                res.message || 'Terjadi kesalahan.',
                                'error'
                            );
                        }

                    })
                    .catch(err => {

                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-cloud-download-alt me-1"></i> Sync Desil Nasional';

                        Swal.fire(
                            'Error',
                            'Gagal menghubungi server.',
                            'error'
                        );
                    });

            });

        });

    });

    // // ==============================================================
    // // 🎛️ SMART FILTER (API MURNI)
    // // ==============================================================

    // // Buat fungsi sapu jagat untuk membaca ulang semua nilai dropdown
    // function eksekusiFilter() {
    //     filterKeluarga.rw = $('#filterRW').val() || '';
    //     filterKeluarga.rt = $('#filterRT').val() || '';
    //     filterKeluarga.status = $('#filterStatus').val() || '';
    //     filterKeluarga.desil = $('#filterDesil').val() || '';

    //     console.log("🛠️ Memerintahkan Tabel Update...", filterKeluarga);

    //     // Perintah API Murni DataTables: reload dan kembalikan ke halaman 1
    //     tableKeluarga.ajax.reload(null, true);
    // }

    // // Tautkan fungsi di atas ke SEMUA dropdown filter
    // $('#filterRT, #filterStatus, #filterDesil').on('change', function() {
    //     eksekusiFilter();
    // });

    // // Khusus RW (Karena harus tarik data RT)
    // $('#filterRW').on('change', function() {
    //     const rw = $(this).val();
    //     const $rt = $('#filterRT');

    //     // Set state filter sementara dan reload
    //     filterKeluarga.rw = rw || '';
    //     filterKeluarga.rt = '';
    //     $rt.prop('disabled', !rw).html('<option value="">Semua RT</option>');

    //     eksekusiFilter();

    //     // Tarik data RT jika RW tidak kosong
    //     if (rw) {
    //         $rt.append('<option value="" disabled>Memuat...</option>');
    //         $.getJSON('/dtsen-se/list-rt/' + rw, function(res) {
    //             $rt.html('<option value="">Semua RT</option>');
    //             if (res.data) {
    //                 res.data.forEach(rt => {
    //                     $rt.append(`<option value="${rt}">RT ${rt}</option>`);
    //                 });
    //             }
    //         });
    //     }
    // });

    // // Tombol Reset
    // $('#btnResetFilter').on('click', function() {
    //     $('#filterRW, #filterStatus, #filterDesil').val('');
    //     $('#filterRT').html('<option value="">Semua RT</option>').prop('disabled', true);
    //     eksekusiFilter();
    // });
</script>

<?= $this->endSection(); ?>