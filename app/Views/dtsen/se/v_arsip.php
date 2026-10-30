<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    /* =========================================================================
       🎨 STYLING PROFESIONAL WORKSPACE ARSIP TERPADU SINDEN
       ========================================================================= */
    .arsip-stat-card {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        transition: all 0.22s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        background: #fff;
    }

    .arsip-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08) !important;
    }

    .arsip-stat-card.active-card {
        border: 2px solid #0d6efd;
        background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
    }

    .arsip-stat-card.active-card-kk {
        border: 2px solid #dc3545;
        background: linear-gradient(135deg, #fff8f8 0%, #ffefef 100%);
    }

    .arsip-stat-card.active-card-art {
        border: 2px solid #0dcaf0;
        background: linear-gradient(135deg, #f4fcff 0%, #e6f9ff 100%);
    }

    .arsip-stat-card.active-card-group {
        border: 2px solid #198754;
        background: linear-gradient(135deg, #f4fff8 0%, #e6f9ef 100%);
    }

    .arsip-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Segmented Mode Pills */
    .arsip-mode-pills .nav-link {
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.86rem;
        padding: 0.48rem 0.95rem;
        color: #495057;
        border: 1px solid #dee2e6;
        background: #fff;
        margin-right: 0.4rem;
        transition: all 0.18s ease;
    }

    .arsip-mode-pills .nav-link:hover {
        background: #f8f9fa;
    }

    .arsip-mode-pills .nav-link.active {
        background: #212529;
        color: #fff;
        border-color: #212529;
        box-shadow: 0 3px 8px rgba(33, 37, 41, 0.2);
    }

    /* Sticky Bulk Action Bar */
    #bulkActionBar {
        position: sticky;
        top: 64px;
        z-index: 1020;
        border-radius: 12px;
        background: linear-gradient(90deg, #1e293b 0%, #0f172a 100%);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.25);
        transition: all 0.25s ease;
    }

    /* Table Enhancements */
    #tableArsipTerpadu th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        vertical-align: middle;
        white-space: nowrap;
    }

    #tableArsipTerpadu td {
        vertical-align: middle;
        font-size: 0.875rem;
    }

    #tableArsipTerpadu tbody tr.row-selected {
        background-color: rgba(25, 135, 84, 0.08) !important;
    }

    /* Inline Family Drawer (Child Row) */
    .family-drawer-box {
        background: #f8fafc;
        border-left: 4px solid #0d6efd;
        border-radius: 8px;
        padding: 14px 16px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.72rem;
        line-height: 1.2;
        border-radius: 0.25rem;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>

<div class="content-wrapper mt-0">
    <?= $this->include('dtsen/se/layout_nav') ?>

    <section class="content">
        <!-- ===================== 1. INTERACTIVE SUMMARY CARDS ===================== -->
        <div class="row g-3 mb-3">
            <!-- Semua Arsip -->
            <div class="col-6 col-lg-3">
                <div class="arsip-stat-card shadow-sm p-3 active-card" id="cardFilterAll" data-mode="all" title="Klik untuk menampilkan Semua Arsip (Keluarga & Penduduk)">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Semua Data Arsip</div>
                            <h3 class="fw-bold mb-0 mt-1 text-primary" id="statTotalAll">0</h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Gabungan KK & Penduduk</small>
                        </div>
                        <div class="arsip-stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Arsip Keluarga (KK) -->
            <div class="col-6 col-lg-3">
                <div class="arsip-stat-card shadow-sm p-3" id="cardFilterKK" data-mode="keluarga" title="Klik untuk memfilter khusus Arsip Keluarga (KK)">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Arsip Keluarga (KK)</div>
                            <h3 class="fw-bold mb-0 mt-1 text-danger" id="statTotalKK">0</h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Kartu Keluarga Terarsip</small>
                        </div>
                        <div class="arsip-stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Arsip Penduduk (ART) -->
            <div class="col-6 col-lg-3">
                <div class="arsip-stat-card shadow-sm p-3" id="cardFilterArt" data-mode="penduduk" title="Klik untuk memfilter khusus Arsip Penduduk / Anggota">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Arsip Penduduk</div>
                            <h3 class="fw-bold mb-0 mt-1 text-info" id="statTotalArt">0</h3>
                            <small class="text-muted" style="font-size: 0.75rem;" id="statArtBreakdown">Master: 0 | Usulan: 0</small>
                        </div>
                        <div class="arsip-stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grup Keluarga Terdampak -->
            <div class="col-6 col-lg-3">
                <div class="arsip-stat-card shadow-sm p-3" id="cardFilterBoth" data-mode="sekeluarga_arsip" title="Klik untuk memfilter data yang KK beserta Anggotanya sama-sama terarsip">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">Keluarga Terdampak</div>
                            <h3 class="fw-bold mb-0 mt-1 text-success" id="statTotalGroups">0</h3>
                            <small class="text-muted" style="font-size: 0.75rem;">Total KK Unik Memiliki Arsip</small>
                        </div>
                        <div class="arsip-stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================== 2. MAIN WORKSPACE CARD ===================== -->
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <!-- Segmented View Switcher (Instant Client-Side Filter, No Tab Reload!) -->
                    <ul class="nav arsip-mode-pills flex-wrap gap-1" id="arsipModePills" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" data-mode="all">
                                <i class="fas fa-th-list me-1"></i> Semua Arsip
                                <span class="badge bg-secondary ms-1" id="pillCountAll">0</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" data-mode="keluarga">
                                <i class="fas fa-folder-open text-danger me-1"></i> Arsip Keluarga (KK)
                                <span class="badge bg-danger ms-1" id="pillCountKK">0</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" data-mode="penduduk">
                                <i class="fas fa-user text-info me-1"></i> Arsip Penduduk (ART)
                                <span class="badge bg-info text-dark ms-1" id="pillCountArt">0</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" data-mode="sekeluarga_arsip" title="Menampilkan KK dan Penduduk yang KK induknya juga sedang terarsip">
                                <i class="fas fa-people-arrows text-warning me-1"></i> KK & Anggota Terarsip
                            </button>
                        </li>
                    </ul>

                    <!-- Top Right Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" id="btnSelectAllFiltered" title="Pilih seluruh data yang tampil sesuai filter saat ini">
                            <i class="fas fa-check-double me-1"></i> Pilih Semua Filter (<span id="filteredTotalCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm shadow-sm" id="btnExportExcelArsip" title="Unduh data arsip ke Excel">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm" id="btnReloadArsip" title="Muat ulang data dari server">
                            <i class="fas fa-sync-alt me-1"></i> Muat Ulang
                        </button>
                    </div>
                </div>

                <!-- Smart Filter Row -->
                <div class="row g-2 align-items-end mt-2 pt-2 border-top">
                    <!-- Filter RW -->
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-muted mb-1">Wilayah (RW)</label>
                        <select class="form-select form-select-sm" id="filterArsipRW">
                            <option value="">Semua RW</option>
                        </select>
                    </div>

                    <!-- Filter RT -->
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-muted mb-1">RT</label>
                        <select class="form-select form-select-sm" id="filterArsipRT" disabled>
                            <option value="">Semua RT</option>
                        </select>
                    </div>

                    <!-- Filter Kategori Alasan -->
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">Kategori Alasan Hapus</label>
                        <select class="form-select form-select-sm" id="filterArsipAlasan">
                            <option value="">Semua Alasan</option>
                            <option value="meninggal">🕊️ Meninggal Dunia</option>
                            <option value="pindah">🚚 Pindah Domisili / Pisah KK</option>
                            <option value="duplikat">📋 Data Ganda / Duplikat</option>
                            <option value="usulan">🔄 Ditimpa Usulan Pembaruan</option>
                            <option value="pemeriksaan">🔍 Pemeriksaan / Anomali Data</option>
                            <option value="lainnya">📌 Alasan Lainnya</option>
                        </select>
                    </div>

                    <!-- Filter Sumber / Status KK -->
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1">Sumber & Status KK</label>
                        <select class="form-select form-select-sm" id="filterArsipSumber">
                            <option value="">Semua Sumber & Status</option>
                            <option value="kk">📁 Hanya Master KK</option>
                            <option value="utama">👤 Penduduk - Master ART</option>
                            <option value="usulan">📝 Penduduk - Draft Usulan</option>
                            <option value="kk_aktif">🟢 Penduduk (KK Induk Masih Aktif)</option>
                            <option value="kk_terarsip">🔴 Penduduk (KK Induk Ikut Terarsip)</option>
                            <option value="nik_unik">✅ Penduduk (NIK Belum Ada di KK Aktif)</option>
                        </select>
                    </div>

                    <!-- Reset Filter -->
                    <div class="col-12 col-md-2 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="btnResetFilterArsip">
                            <i class="fas fa-eraser me-1"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- ===================== 3. STICKY BULK ACTION BAR ===================== -->
                <div id="bulkActionBar" class="p-3 mb-3 d-none">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="badge bg-success p-2 rounded-circle">
                                <i class="fas fa-check-double"></i>
                            </span>
                            <div>
                                <div class="fw-bold">
                                    <span id="selectedTotalCount">0</span> Data Arsip Dipilih
                                    <span class="badge bg-danger ms-1"><span id="selectedKkCount">0</span> Keluarga</span>
                                    <span class="badge bg-info text-dark ms-1"><span id="selectedArtCount">0</span> Penduduk</span>
                                </div>
                                <div class="form-check form-switch mt-1 mb-0">
                                    <input class="form-check-input cursor-pointer" type="checkbox" id="chkBulkIncludeRelated" checked>
                                    <label class="form-check-label small text-light opacity-90 cursor-pointer" for="chkBulkIncludeRelated">
                                        Pulihkan utuh sekeluarga (otomatis aktifkan KK induk & pulihkan anggota keluarga terkait)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-light" id="btnClearSelection">
                                <i class="fas fa-times me-1"></i> Batal Pilih
                            </button>
                            <button type="button" class="btn btn-sm btn-success fw-bold shadow" id="btnExecuteBulkRestore">
                                <i class="fas fa-undo-alt me-1"></i> Restore Terpilih (<span class="selectedTotalBadge">0</span>)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Banner Petunjuk Interaktif -->
                <div class="alert alert-light border border-secondary border-opacity-25 py-2 px-3 mb-3 small d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <i class="fas fa-bolt text-warning me-1"></i>
                        <strong>Mode Interaktif Terpadu:</strong> Anda dapat langsung melakukan <b>Restore Penduduk</b> maupun <b>Restore 1 Keluarga (KK + Anggota)</b> dari baris mana saja tanpa perlu berpindah tab, atau centang beberapa baris sekaligus untuk <b>Restore Massal (Bulk)</b>.
                    </div>
                </div>

                <!-- ===================== 4. UNIFIED INTERACTIVE DATATABLE ===================== -->
                <div class="table-responsive">
                    <table id="tableArsipTerpadu" class="table table-striped table-hover table-bordered w-100">
                        <thead class="table-dark text-center">
                            <tr>
                                <th width="3%" class="text-center">
                                    <input type="checkbox" id="checkAllPage" class="form-check-input m-0 cursor-pointer" style="width: 1.15rem; height: 1.15rem;" title="Pilih Semua di Halaman Ini">
                                </th>
                                <th width="4%">No.</th>
                                <th width="13%">Tipe & Kategori</th>
                                <th width="22%">Identitas (Nama & NIK / No. KK)</th>
                                <th width="22%">Keluarga Terkait & Anggota</th>
                                <th width="13%">Wilayah & Alamat</th>
                                <th width="11%">Waktu & Alasan Hapus</th>
                                <th width="12%">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ===================== 5. MODAL INTERAKTIF: KELOLA & PULIHKAN 1 KELUARGA ===================== -->
<div class="modal fade" id="modalDetailKeluargaArsip" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-users-cog me-2 text-info"></i> Kelola & Pulihkan Keluarga (KK & Anggota)
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="modalKeluargaBody">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <div>Memuat rincian keluarga dan anggota...</div>
                </div>
            </div>
            <div class="modal-footer bg-light justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex gap-2" id="modalKeluargaFooterActions"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // =========================================================================
        // 📦 STATE MANAGEMENT WORKSPACE ARSIP TERPADU
        // =========================================================================
        let allArsipData = [];
        let itemMapByUid = new Map();
        let selectedUids = new Set();

        const filterState = {
            mode: 'all', // 'all' | 'keluarga' | 'penduduk' | 'sekeluarga_arsip'
            rw: '',
            rt: '',
            alasan: '',
            sumber: ''
        };

        // =========================================================================
        // 🛡️ FUNGSI BANTUAN: SENSOR NIK / NO KK & FORMATTER
        // =========================================================================
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function maskNumberJS(number) {
            if (!number || number === '-' || number === 'NOKKS') return number || '-';
            let numStr = number.toString().trim();
            if (numStr.length <= 8) return escapeHtml(numStr);
            let masked = numStr.substring(0, 8) + '*'.repeat(numStr.length - 8);
            let safeFull = escapeHtml(numStr);
            let safeMasked = escapeHtml(masked);

            return `<span class="fw-bold text-primary cursor-pointer font-monospace" 
                      onmouseenter="this.innerText='${safeFull}'" 
                      onmouseleave="this.innerText='${safeMasked}'" 
                      ontouchstart="this.innerText='${safeFull}'" 
                      ontouchend="this.innerText='${safeMasked}'" 
                      title="Arahkan kursor untuk melihat lengkap">${safeMasked}</span>`;
        }

        function formatReasonBadge(cat) {
            const map = {
                meninggal: '<span class="badge bg-dark"><i class="fas fa-cross me-1"></i>Meninggal</span>',
                pindah: '<span class="badge bg-warning text-dark"><i class="fas fa-truck-moving me-1"></i>Pindah/Pisah</span>',
                duplikat: '<span class="badge bg-danger"><i class="fas fa-clone me-1"></i>Duplikat/Ganda</span>',
                usulan: '<span class="badge bg-info text-dark"><i class="fas fa-sync-alt me-1"></i>Timpaan Usulan</span>',
                pemeriksaan: '<span class="badge bg-primary"><i class="fas fa-search me-1"></i>Pemeriksaan</span>',
                lainnya: '<span class="badge bg-secondary"><i class="fas fa-tag me-1"></i>Lainnya</span>'
            };
            return map[cat] || map.lainnya;
        }

        function formatDateTimeID(dtStr) {
            if (!dtStr || dtStr === '0000-00-00 00:00:00') return '-';
            const d = new Date(dtStr.replace(' ', 'T'));
            if (isNaN(d.getTime())) return escapeHtml(dtStr);
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }) + ' ' + d.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // =========================================================================
        // 🔎 CUSTOM FILTER ENGINE DATATABLES (INSTANT 0ms)
        // =========================================================================
        $.fn.dataTable.ext.search.push(function(settings, searchData, index, rowData) {
            if (settings.nTable.id !== 'tableArsipTerpadu' || !rowData) return true;

            // 1. Filter Mode (Semua / Keluarga / Penduduk / KK & Anggota Terarsip)
            if (filterState.mode === 'keluarga' && rowData.record_type !== 'keluarga') {
                return false;
            }
            if (filterState.mode === 'penduduk' && rowData.record_type !== 'penduduk') {
                return false;
            }
            if (filterState.mode === 'sekeluarga_arsip') {
                if (rowData.record_type === 'keluarga' && (rowData.deleted_art_count || 0) === 0) {
                    return false;
                }
                if (rowData.record_type === 'penduduk' && !rowData.kk_is_deleted) {
                    return false;
                }
            }

            // 2. Filter Wilayah RW
            if (filterState.rw && filterState.rw !== '') {
                const targetRw = filterState.rw.padStart(3, '0');
                const rowRw = String(rowData.rw || '').padStart(3, '0');
                if (rowRw !== targetRw) return false;
            }

            // 3. Filter Wilayah RT
            if (filterState.rt && filterState.rt !== '') {
                const targetRt = filterState.rt.padStart(3, '0');
                const rowRt = String(rowData.rt || '').padStart(3, '0');
                if (rowRt !== targetRt) return false;
            }

            // 4. Filter Kategori Alasan
            if (filterState.alasan && filterState.alasan !== '') {
                if (rowData.reason_category !== filterState.alasan) return false;
            }

            // 5. Filter Sumber & Status KK
            if (filterState.sumber && filterState.sumber !== '') {
                const s = filterState.sumber;
                if (s === 'kk' && rowData.sumber !== 'kk') return false;
                if (s === 'utama' && rowData.sumber !== 'utama') return false;
                if (s === 'usulan' && rowData.sumber !== 'usulan') return false;
                if (s === 'kk_aktif' && (rowData.record_type !== 'penduduk' || rowData.kk_is_deleted)) return false;
                if (s === 'kk_terarsip' && (rowData.record_type !== 'penduduk' || !rowData.kk_is_deleted)) return false;
                if (s === 'nik_unik' && (rowData.record_type !== 'penduduk' || rowData.active_same_nik)) return false;
            }

            return true;
        });

        // =========================================================================
        // 📊 INISIALISASI DATATABLES ARSIP TERPADU
        // =========================================================================
        const dtArsip = $('#tableArsipTerpadu').DataTable({
            data: [],
            responsive: false,
            pageLength: 15,
            lengthMenu: [
                [10, 15, 25, 50, 100, -1],
                [10, 15, 25, 50, 100, "Semua"]
            ],
            autoWidth: false,
            order: [
                [6, 'desc']
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                searchPlaceholder: 'Cari Nama, NIK, No. KK, Alamat...'
            },
            dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6"f>>rt<"row align-items-center mt-2"<"col-md-6"i><"col-md-6"p>>',
            buttons: [{
                extend: 'excelHtml5',
                className: 'd-none btn-excel-arsip-hidden',
                title: 'Data_Arsip_Keluarga_dan_Penduduk_SINDEN',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6]
                }
            }],
            createdRow: function(row, data) {
                $(row).attr('data-uid', data.uid);
                if (selectedUids.has(data.uid)) {
                    $(row).addClass('row-selected');
                }
            },
            drawCallback: function() {
                const api = this.api();
                const info = api.page.info();

                // Update nomor urut
                api.column(1, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = info.start + i + 1;
                });

                // Update counter hasil filter pada tombol "Pilih Semua Filter"
                const filteredCount = api.rows({
                    filter: 'applied'
                }).count();
                $('#filteredTotalCount').text(filteredCount);

                // Sinkronisasi checkbox pada halaman aktif
                $('.row-arsip-checkbox').each(function() {
                    const uid = $(this).val();
                    const checked = selectedUids.has(uid);
                    $(this).prop('checked', checked);
                    $(this).closest('tr').toggleClass('row-selected', checked);
                });

                syncHeaderCheckbox();
            },
            columns: [
                // 0: Checkbox Bulk
                {
                    data: 'uid',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(uid) {
                        const checked = selectedUids.has(uid) ? 'checked' : '';
                        return `<input type="checkbox" class="form-check-input row-arsip-checkbox m-0 cursor-pointer" value="${uid}" style="width: 1.15rem; height: 1.15rem;" ${checked}>`;
                    }
                },
                // 1: No
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center fw-semibold text-muted',
                    defaultContent: ''
                },
                // 2: Tipe & Kategori
                {
                    data: 'record_type',
                    className: 'text-start',
                    render: function(type, renderType, row) {
                        if (renderType === 'filter' || renderType === 'sort') {
                            return `${type} ${row.sumber} ${row.jenis_shdk || ''}`;
                        }

                        if (type === 'keluarga') {
                            return `
                                <div class="d-flex flex-column gap-1 align-items-start">
                                    <span class="badge bg-danger"><i class="fas fa-home me-1"></i>Keluarga (KK)</span>
                                    <span class="badge bg-light text-dark border">Master KK</span>
                                </div>
                            `;
                        }

                        const sumberBadge = row.sumber === 'usulan' ?
                            '<span class="badge bg-warning text-dark"><i class="fas fa-file-signature me-1"></i>Draft Usulan</span>' :
                            '<span class="badge bg-secondary"><i class="fas fa-database me-1"></i>Master ART</span>';

                        return `
                            <div class="d-flex flex-column gap-1 align-items-start">
                                <span class="badge bg-info text-dark"><i class="fas fa-user me-1"></i>Penduduk</span>
                                ${sumberBadge}
                                <span class="badge bg-light text-dark border">${escapeHtml(row.jenis_shdk || '-')}</span>
                            </div>
                        `;
                    }
                },
                // 3: Identitas (Nama & NIK / No. KK)
                {
                    data: 'nama',
                    className: 'text-start',
                    render: function(nama, renderType, row) {
                        if (renderType === 'filter') {
                            return `${row.nama} ${row.nik} ${row.no_kk} ${row.search_blob || ''}`;
                        }
                        if (renderType === 'sort') {
                            return row.nama || '';
                        }

                        if (row.record_type === 'keluarga') {
                            const noKK = row.no_kk || '-';
                            return `
                                <div class="fw-bold text-dark">${escapeHtml(row.kepala_keluarga || '-')}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <small class="text-muted">No. KK:</small>
                                    ${maskNumberJS(noKK)}
                                    ${noKK !== '-' ? `<button type="button" class="btn btn-outline-secondary btn-xs btnCopyClipboard" data-value="${escapeHtml(noKK)}" data-label="No. KK" title="Salin No. KK"><i class="fas fa-copy"></i></button>` : ''}
                                </div>
                            `;
                        }

                        const nik = row.nik || '-';
                        let warningDup = '';
                        if (row.active_same_nik) {
                            warningDup = `<div class="mt-1"><span class="badge bg-warning text-dark" title="NIK ini sudah memiliki data aktif di KK yang sama (biasanya karena pembaruan usulan)"><i class="fas fa-exclamation-triangle me-1"></i>NIK Sudah Aktif di KK</span></div>`;
                        }

                        return `
                            <div class="fw-bold text-dark">${escapeHtml(row.nama || '-')}</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <small class="text-muted">NIK:</small>
                                ${maskNumberJS(nik)}
                                ${nik !== '-' ? `<button type="button" class="btn btn-outline-secondary btn-xs btnCopyClipboard" data-value="${escapeHtml(nik)}" data-label="NIK" title="Salin NIK"><i class="fas fa-copy"></i></button>` : ''}
                            </div>
                            ${warningDup}
                        `;
                    }
                },
                // 4: Keluarga Terkait & Anggota (Cross-Interactive!)
                {
                    data: 'no_kk',
                    className: 'text-start',
                    render: function(noKK, renderType, row) {
                        if (renderType === 'filter' || renderType === 'sort') {
                            return `${row.kepala_keluarga || ''} ${row.no_kk || ''}`;
                        }

                        const kkStatusBadge = row.kk_is_deleted ?
                            '<span class="badge bg-danger"><i class="fas fa-archive me-1"></i>KK Terarsip</span>' :
                            '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>KK Aktif</span>';

                        const activeCnt = row.active_art_count || 0;
                        const delCnt = row.deleted_art_count || 0;

                        const memberBtn = row.id_kk > 0 ? `
                            <button type="button" class="btn btn-outline-primary btn-xs mt-1 btnToggleFamilyDrawer" data-idkk="${row.id_kk}" title="Klik untuk melihat & me-restore anggota keluarga ini secara langsung tanpa pindah tab">
                                <i class="fas fa-users me-1"></i> Anggota: <b>${activeCnt}</b> Aktif | <b class="text-danger">${delCnt}</b> Arsip
                                <i class="fas fa-chevron-down ms-1"></i>
                            </button>
                        ` : '';

                        if (row.record_type === 'keluarga') {
                            return `
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    ${kkStatusBadge}
                                </div>
                                ${memberBtn}
                            `;
                        }

                        return `
                            <div class="small fw-bold text-dark">KK: ${escapeHtml(row.kepala_keluarga || '-')}</div>
                            <div class="d-flex align-items-center gap-1 small text-muted">
                                <span>${maskNumberJS(noKK)}</span>
                                ${noKK && noKK !== '-' ? `<button type="button" class="btn btn-outline-secondary btn-xs btnCopyClipboard" data-value="${escapeHtml(noKK)}" data-label="No. KK" title="Salin No. KK"><i class="fas fa-copy"></i></button>` : ''}
                            </div>
                            <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                ${kkStatusBadge}
                            </div>
                            ${memberBtn}
                        `;
                    }
                },
                // 5: Wilayah & Alamat
                {
                    data: 'alamat',
                    className: 'text-start',
                    render: function(alamat, renderType, row) {
                        if (renderType === 'filter' || renderType === 'sort') {
                            return `RW ${row.rw} RT ${row.rt} ${alamat || ''}`;
                        }
                        return `
                            <div class="mb-1">
                                <span class="badge bg-light text-dark border">RW ${escapeHtml(row.rw || '-')}</span>
                                <span class="badge bg-info text-dark">RT ${escapeHtml(row.rt || '-')}</span>
                            </div>
                            <div class="small text-muted text-wrap" style="max-width: 180px;">${escapeHtml(alamat || '-')}</div>
                        `;
                    }
                },
                // 6: Waktu & Alasan Hapus
                {
                    data: 'deleted_at',
                    className: 'text-start',
                    render: function(deletedAt, renderType, row) {
                        if (renderType === 'sort') {
                            return deletedAt || '';
                        }
                        if (renderType === 'filter') {
                            return `${deletedAt || ''} ${row.delete_reason || ''} ${row.reason_category || ''}`;
                        }

                        return `
                            <div class="small fw-semibold text-dark text-nowrap">
                                <i class="far fa-clock text-muted me-1"></i>${formatDateTimeID(deletedAt)}
                            </div>
                            <div class="my-1">${formatReasonBadge(row.reason_category)}</div>
                            <div class="small text-muted text-wrap" style="max-width: 200px;">${escapeHtml(row.delete_reason || '-')}</div>
                        `;
                    }
                },
                // 7: Aksi Cepat (Dual Action: Restore Individu & Restore 1 Keluarga!)
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center text-nowrap',
                    render: function(row) {
                        if (row.record_type === 'keluarga') {
                            const hasArchivedArt = (row.deleted_art_count || 0) > 0;
                            const btnRestoreKK = `
                                <button type="button" class="btn btn-success btn-sm w-100 mb-1 shadow-sm btnRestoreKKRow"
                                    data-uid="${row.uid}"
                                    data-idkk="${row.id_kk}"
                                    data-nama="${escapeHtml(row.kepala_keluarga)}"
                                    data-nokk="${escapeHtml(row.no_kk)}"
                                    data-delart="${row.deleted_art_count || 0}"
                                    title="Pulihkan Keluarga (KK) ini">
                                    <i class="fas fa-undo me-1"></i> ${hasArchivedArt ? 'Restore KK / Sekeluarga' : 'Restore Keluarga'}
                                </button>
                            `;

                            const btnManageArt = `
                                <button type="button" class="btn btn-outline-primary btn-xs w-100 btnOpenModalKeluarga"
                                    data-idkk="${row.id_kk}"
                                    title="Lihat & pilih penduduk/anggota keluarga tanpa pindah tab">
                                    <i class="fas fa-user-check me-1"></i> Pilih / Restore Penduduk
                                </button>
                            `;

                            return `<div class="d-flex flex-column">${btnRestoreKK}${btnManageArt}</div>`;
                        }

                        // Baris Penduduk (Anggota): Bisa langsung Restore Penduduk ATAU Restore 1 Keluarga!
                        const btnRestoreArt = `
                            <button type="button" class="btn btn-success btn-sm w-100 mb-1 shadow-sm btnRestoreArtRow"
                                data-uid="${row.uid}"
                                data-id="${row.id}"
                                data-sumber="${row.sumber}"
                                data-idkk="${row.id_kk}"
                                data-nama="${escapeHtml(row.nama)}"
                                data-nik="${escapeHtml(row.nik)}"
                                data-kepala="${escapeHtml(row.kepala_keluarga)}"
                                data-nokk="${escapeHtml(row.no_kk)}"
                                data-kkdeleted="${row.kk_is_deleted ? '1' : '0'}"
                                title="Pulihkan penduduk ini">
                                <i class="fas fa-user-check me-1"></i> Restore Penduduk
                            </button>
                        `;

                        const showSekeluargaBtn = row.id_kk > 0 && (row.kk_is_deleted || (row.deleted_art_count || 0) > 1);
                        const btnRestoreFamily = showSekeluargaBtn ? `
                            <button type="button" class="btn btn-outline-danger btn-xs w-100 btnRestoreSekeluargaRow"
                                data-idkk="${row.id_kk}"
                                data-kepala="${escapeHtml(row.kepala_keluarga)}"
                                data-nokk="${escapeHtml(row.no_kk)}"
                                data-kkdeleted="${row.kk_is_deleted ? '1' : '0'}"
                                data-delart="${row.deleted_art_count || 0}"
                                title="Pulihkan 1 Keluarga penuh (KK + seluruh anggota keluarga terarsip) langsung dari sini">
                                <i class="fas fa-users me-1"></i> Restore 1 Keluarga
                            </button>
                        ` : (row.id_kk > 0 ? `
                            <button type="button" class="btn btn-outline-secondary btn-xs w-100 btnOpenModalKeluarga"
                                data-idkk="${row.id_kk}"
                                title="Lihat detail keluarga & anggota lainnya">
                                <i class="fas fa-users-cog me-1"></i> Detail Keluarga
                            </button>
                        ` : '');

                        return `<div class="d-flex flex-column">${btnRestoreArt}${btnRestoreFamily}</div>`;
                    }
                }
            ]
        });

        // =========================================================================
        // 🔄 LOAD DATA ARSIP TERPADU DARI SERVER
        // =========================================================================
        function loadArsipTerpadu(preservePage = false) {
            $.ajax({
                url: '<?= base_url('dtsen-se/arsip-terpadu') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (!res.status) {
                        Swal.fire('Gagal Memuat', res.message || 'Terjadi kesalahan saat memuat data arsip.', 'error');
                        return;
                    }

                    allArsipData = res.data || [];
                    itemMapByUid.clear();
                    allArsipData.forEach(item => {
                        itemMapByUid.set(item.uid, item);
                    });

                    // Bersihkan UID terpilih yang sudah tidak ada di arsip (sudah di-restore)
                    Array.from(selectedUids).forEach(uid => {
                        if (!itemMapByUid.has(uid)) {
                            selectedUids.delete(uid);
                        }
                    });

                    // Update Summary Cards & Pill Badges
                    const sum = res.summary || {};
                    $('#statTotalAll, #pillCountAll').text(sum.total_all || 0);
                    $('#statTotalKK, #pillCountKK').text(sum.total_kk || 0);
                    $('#statTotalArt, #pillCountArt').text(sum.total_art || 0);
                    $('#statArtBreakdown').text(`Master: ${sum.total_art_utama || 0} | Usulan: ${sum.total_art_usulan || 0}`);
                    $('#statTotalGroups').text(sum.total_kk_groups || 0);

                    // Populate Dropdown RW jika belum terisi
                    const currentRw = $('#filterArsipRW').val();
                    const $rw = $('#filterArsipRW');
                    $rw.html('<option value="">Semua RW</option>');
                    (res.rw_list || []).forEach(rw => {
                        $rw.append(`<option value="${rw}" ${rw === currentRw ? 'selected' : ''}>RW ${rw}</option>`);
                    });

                    // Masukkan data ke DataTables
                    dtArsip.clear().rows.add(allArsipData).draw(preservePage ? false : true);
                    updateBulkSelectionUI();
                },
                error: function() {
                    Swal.fire('Error Server', 'Gagal menghubungi server saat memuat data arsip.', 'error');
                }
            });
        }

        loadArsipTerpadu();

        // =========================================================================
        // 🎛️ INTERAKSI MODE PILLS, SUMMARY CARDS, & FILTER BAR
        // =========================================================================
        function setArsipMode(mode) {
            filterState.mode = mode;

            // Update active class on pills
            $('#arsipModePills .nav-link').removeClass('active');
            $(`#arsipModePills .nav-link[data-mode="${mode}"]`).addClass('active');

            // Update active class on summary cards
            $('.arsip-stat-card').removeClass('active-card active-card-kk active-card-art active-card-group');
            if (mode === 'all') $('#cardFilterAll').addClass('active-card');
            else if (mode === 'keluarga') $('#cardFilterKK').addClass('active-card-kk');
            else if (mode === 'penduduk') $('#cardFilterArt').addClass('active-card-art');
            else if (mode === 'sekeluarga_arsip') $('#cardFilterBoth').addClass('active-card-group');

            dtArsip.draw();
        }

        $(document).on('click', '#arsipModePills .nav-link, .arsip-stat-card', function() {
            const mode = $(this).data('mode');
            if (mode) setArsipMode(mode);
        });

        // Filter RW -> Load RT
        $('#filterArsipRW').on('change', function() {
            const rw = $(this).val();
            filterState.rw = rw || '';
            filterState.rt = '';

            const $rt = $('#filterArsipRT');
            $rt.prop('disabled', !rw).html('<option value="">Semua RT</option>');

            if (rw) {
                // Ambil daftar RT unik dari data arsip pada RW tersebut
                const rtSet = new Set();
                allArsipData.forEach(item => {
                    if (String(item.rw).padStart(3, '0') === String(rw).padStart(3, '0') && item.rt && item.rt !== '-') {
                        rtSet.add(String(item.rt).padStart(3, '0'));
                    }
                });
                Array.from(rtSet).sort().forEach(rt => {
                    $rt.append(`<option value="${rt}">RT ${rt}</option>`);
                });
            }

            dtArsip.draw();
        });

        $('#filterArsipRT, #filterArsipAlasan, #filterArsipSumber').on('change', function() {
            filterState.rt = $('#filterArsipRT').val() || '';
            filterState.alasan = $('#filterArsipAlasan').val() || '';
            filterState.sumber = $('#filterArsipSumber').val() || '';
            dtArsip.draw();
        });

        $('#btnResetFilterArsip').on('click', function() {
            $('#filterArsipRW, #filterArsipAlasan, #filterArsipSumber').val('');
            $('#filterArsipRT').html('<option value="">Semua RT</option>').prop('disabled', true);
            filterState.rw = '';
            filterState.rt = '';
            filterState.alasan = '';
            filterState.sumber = '';
            setArsipMode('all');
        });

        $('#btnReloadArsip').on('click', function() {
            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Memuat...');
            loadArsipTerpadu(true);
            setTimeout(() => {
                $btn.prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i> Muat Ulang');
            }, 600);
        });

        $('#btnExportExcelArsip').on('click', function() {
            dtArsip.button('.btn-excel-arsip-hidden').trigger();
        });

        // =========================================================================
        // ☑️ MULTI-SELECT & BULK ACTION BAR MANAGEMENT
        // =========================================================================
        function syncHeaderCheckbox() {
            const $visibleChecks = $('.row-arsip-checkbox');
            const totalVisible = $visibleChecks.length;
            const totalChecked = $visibleChecks.filter(':checked').length;

            const $checkAll = $('#checkAllPage');
            if (totalVisible > 0 && totalVisible === totalChecked) {
                $checkAll.prop('checked', true).prop('indeterminate', false);
            } else if (totalChecked > 0) {
                $checkAll.prop('checked', false).prop('indeterminate', true);
            } else {
                $checkAll.prop('checked', false).prop('indeterminate', false);
            }
        }

        function updateBulkSelectionUI() {
            let kkCount = 0;
            let artCount = 0;

            selectedUids.forEach(uid => {
                const item = itemMapByUid.get(uid);
                if (item) {
                    if (item.record_type === 'keluarga') kkCount++;
                    else artCount++;
                } else if (uid.startsWith('kk_')) {
                    kkCount++;
                } else {
                    artCount++;
                }
            });

            const total = kkCount + artCount;
            $('#selectedTotalCount, .selectedTotalBadge').text(total);
            $('#selectedKkCount').text(kkCount);
            $('#selectedArtCount').text(artCount);

            if (total > 0) {
                $('#bulkActionBar').removeClass('d-none');
            } else {
                $('#bulkActionBar').addClass('d-none');
            }

            syncHeaderCheckbox();
        }

        // Checkbox per baris
        $(document).on('change', '.row-arsip-checkbox', function() {
            const uid = String($(this).val());
            const isChecked = $(this).is(':checked');
            if (isChecked) {
                selectedUids.add(uid);
            } else {
                selectedUids.delete(uid);
            }
            $(this).closest('tr').toggleClass('row-selected', isChecked);
            updateBulkSelectionUI();
        });

        // Klik di sel pertama untuk toggle checkbox
        $('#tableArsipTerpadu tbody').on('click', 'td:first-child', function(e) {
            if (!$(e.target).is(':checkbox')) {
                const $chk = $(this).find('.row-arsip-checkbox');
                if ($chk.length) {
                    $chk.prop('checked', !$chk.is(':checked')).trigger('change');
                }
            }
        });

        // Pilih semua di halaman aktif
        $('#checkAllPage').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-arsip-checkbox').each(function() {
                const uid = String($(this).val());
                $(this).prop('checked', isChecked);
                $(this).closest('tr').toggleClass('row-selected', isChecked);
                if (isChecked) selectedUids.add(uid);
                else selectedUids.delete(uid);
            });
            updateBulkSelectionUI();
        });

        // Pilih seluruh baris sesuai filter saat ini (lintas halaman)
        $('#btnSelectAllFiltered').on('click', function() {
            const filteredRows = dtArsip.rows({
                filter: 'applied'
            }).data().toArray();
            if (filteredRows.length === 0) return;

            filteredRows.forEach(r => {
                if (r.uid) selectedUids.add(r.uid);
            });
            dtArsip.draw(false);
            updateBulkSelectionUI();
        });

        // Batal Pilih
        $('#btnClearSelection').on('click', function() {
            selectedUids.clear();
            $('.row-arsip-checkbox').prop('checked', false);
            $('#tableArsipTerpadu tbody tr').removeClass('row-selected');
            updateBulkSelectionUI();
        });

        // =========================================================================
        // 👨‍👩‍👧 INLINE EXPANDABLE DRAWER & MODAL DETAIL 1 KELUARGA
        // =========================================================================
        function buildFamilyDrawerHtml(res) {
            const kk = res.kk || {};
            const activeMembers = res.active_members || [];
            const archivedMembers = res.archived_members || [];

            const kkBadge = kk.kk_is_deleted ?
                `<span class="badge bg-danger"><i class="fas fa-archive me-1"></i>KK Terarsip (${escapeHtml(kk.delete_reason || '-')})</span>` :
                `<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>KK Aktif</span>`;

            let archivedRowsHtml = '';
            if (archivedMembers.length === 0) {
                archivedRowsHtml = `<tr><td colspan="6" class="text-center text-muted py-2">Tidak ada anggota keluarga yang terarsip pada KK ini.</td></tr>`;
            } else {
                archivedMembers.forEach((m, idx) => {
                    const srcBadge = m.sumber === 'usulan' ?
                        '<span class="badge bg-warning text-dark">Draft Usulan</span>' :
                        '<span class="badge bg-secondary">Master ART</span>';
                    const dupWarn = m.active_same_nik ?
                        '<span class="badge bg-warning text-dark ms-1" title="NIK sudah aktif di KK ini">NIK Aktif</span>' : '';

                    archivedRowsHtml += `
                        <tr>
                            <td class="text-center">${idx + 1}</td>
                            <td class="fw-bold">${escapeHtml(m.nama)}${dupWarn}</td>
                            <td>${maskNumberJS(m.nik)}</td>
                            <td><span class="badge bg-light text-dark border">${escapeHtml(m.jenis_shdk)}</span> ${srcBadge}</td>
                            <td class="small text-muted">${escapeHtml(m.delete_reason)} <br><span class="text-secondary" style="font-size:0.72rem;">${formatDateTimeID(m.deleted_at)}</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-success btn-xs btnRestoreArtRow"
                                    data-uid="${m.uid}"
                                    data-id="${m.id}"
                                    data-sumber="${m.sumber}"
                                    data-idkk="${kk.id_kk}"
                                    data-nama="${escapeHtml(m.nama)}"
                                    data-nik="${escapeHtml(m.nik)}"
                                    data-kepala="${escapeHtml(kk.kepala_keluarga)}"
                                    data-nokk="${escapeHtml(kk.no_kk)}"
                                    data-kkdeleted="${kk.kk_is_deleted ? '1' : '0'}">
                                    <i class="fas fa-undo me-1"></i> Restore Penduduk
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            let activeBadgesHtml = activeMembers.length === 0 ?
                '<span class="text-muted small">Belum ada anggota aktif.</span>' :
                activeMembers.map(a => `<span class="badge bg-white text-dark border me-1 mb-1 p-1 px-2"><i class="fas fa-user-check text-success me-1"></i><b>${escapeHtml(a.nama)}</b> <small class="text-muted">(${escapeHtml(a.jenis_shdk)})</small></span>`).join('');

            return `
                <div class="family-drawer-box">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2 pb-2 border-bottom">
                        <div>
                            <span class="fw-bold text-dark"><i class="fas fa-home text-primary me-1"></i> KK: ${escapeHtml(kk.kepala_keluarga || '-')}</span>
                            <span class="mx-2 text-muted">|</span>
                            <span class="small text-muted">No. KK: ${maskNumberJS(kk.no_kk)}</span>
                            <span class="mx-2 text-muted">|</span>
                            ${kkBadge}
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            ${kk.kk_is_deleted ? `
                                <button type="button" class="btn btn-outline-success btn-xs btnRestoreKKOnly"
                                    data-idkk="${kk.id_kk}"
                                    data-nama="${escapeHtml(kk.kepala_keluarga)}"
                                    data-nokk="${escapeHtml(kk.no_kk)}">
                                    <i class="fas fa-home me-1"></i> Restore KK Saja
                                </button>
                            ` : ''}
                            ${(kk.kk_is_deleted || archivedMembers.length > 0) ? `
                                <button type="button" class="btn btn-primary btn-xs btnRestoreSekeluargaRow"
                                    data-idkk="${kk.id_kk}"
                                    data-kepala="${escapeHtml(kk.kepala_keluarga)}"
                                    data-nokk="${escapeHtml(kk.no_kk)}"
                                    data-kkdeleted="${kk.kk_is_deleted ? '1' : '0'}"
                                    data-delart="${archivedMembers.length}">
                                    <i class="fas fa-users me-1"></i> Restore 1 Keluarga Utuh (KK + ${archivedMembers.length} Anggota)
                                </button>
                            ` : ''}
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="small fw-bold text-secondary mb-1"><i class="fas fa-check-circle text-success me-1"></i> Anggota Aktif Saat Ini (${activeMembers.length}):</div>
                        <div>${activeBadgesHtml}</div>
                    </div>

                    <div>
                        <div class="small fw-bold text-danger mb-1"><i class="fas fa-archive me-1"></i> Daftar Anggota Terarsip di Keluarga Ini (${archivedMembers.length}):</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered bg-white mb-0">
                                <thead class="table-light text-center small">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Nama Anggota</th>
                                        <th width="20%">NIK</th>
                                        <th width="18%">Hubungan & Sumber</th>
                                        <th width="20%">Alasan & Waktu Hapus</th>
                                        <th width="12%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>${archivedRowsHtml}</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        // Toggle Inline Child Row Drawer
        $(document).on('click', '.btnToggleFamilyDrawer', function() {
            const $btn = $(this);
            const idKk = $btn.data('idkk');
            const tr = $btn.closest('tr');
            const row = dtArsip.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                $btn.find('.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                return;
            }

            $btn.find('.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            row.child('<div class="p-3 text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Memuat daftar anggota keluarga...</div>').show();
            tr.addClass('shown');

            $.getJSON('<?= base_url('dtsen-se/arsip-keluarga-detail') ?>', {
                id_kk: idKk
            }, function(res) {
                if (res.status) {
                    row.child(buildFamilyDrawerHtml(res)).show();
                } else {
                    row.child(`<div class="alert alert-danger m-2 py-2 small">${escapeHtml(res.message || 'Gagal memuat detail keluarga.')}</div>`).show();
                }
            });
        });

        // Buka Modal Interaktif Kelola Keluarga
        $(document).on('click', '.btnOpenModalKeluarga', function() {
            const idKk = $(this).data('idkk');
            const $modal = $('#modalDetailKeluargaArsip');
            const $body = $('#modalKeluargaBody');
            const $footer = $('#modalKeluargaFooterActions');

            $body.html('<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><div>Memuat rincian keluarga dan anggota...</div></div>');
            $footer.empty();
            $modal.modal('show');

            $.getJSON('<?= base_url('dtsen-se/arsip-keluarga-detail') ?>', {
                id_kk: idKk
            }, function(res) {
                if (!res.status) {
                    $body.html(`<div class="alert alert-danger">${escapeHtml(res.message || 'Data keluarga tidak ditemukan.')}</div>`);
                    return;
                }
                $body.html(buildFamilyDrawerHtml(res));

                const kk = res.kk || {};
                const archivedCnt = (res.archived_members || []).length;
                if (kk.kk_is_deleted || archivedCnt > 0) {
                    $footer.html(`
                        <button type="button" class="btn btn-success btn-sm fw-bold btnRestoreSekeluargaRow"
                            data-idkk="${kk.id_kk}"
                            data-kepala="${escapeHtml(kk.kepala_keluarga)}"
                            data-nokk="${escapeHtml(kk.no_kk)}"
                            data-kkdeleted="${kk.kk_is_deleted ? '1' : '0'}"
                            data-delart="${archivedCnt}">
                            <i class="fas fa-undo-alt me-1"></i> Pulihkan 1 Keluarga Utuh (KK + ${archivedCnt} Anggota)
                        </button>
                    `);
                }
            });
        });

        // =========================================================================
        // ♻️ AKSI 1: RESTORE PENDUDUK (INDIVIDU) + OPSI AKTIFKAN KK INDUK
        // =========================================================================
        $(document).on('click', '.btnRestoreArtRow', function() {
            const id = $(this).data('id');
            const uid = String($(this).data('uid') || '');
            const sumber = $(this).data('sumber') || 'utama';
            const idKk = $(this).data('idkk') || 0;
            const nama = $(this).data('nama') || 'Penduduk';
            const nik = $(this).data('nik') || '-';
            const kepala = $(this).data('kepala') || '-';
            const noKk = $(this).data('nokk') || '-';
            const kkDeleted = String($(this).data('kkdeleted')) === '1';

            let htmlContent = `
                <div class="text-start small">
                    <div class="p-2 bg-light rounded border mb-2">
                        <div><b>Nama Penduduk:</b> ${escapeHtml(nama)}</div>
                        <div><b>NIK:</b> ${escapeHtml(nik)}</div>
                        <div><b>Keluarga (KK):</b> ${escapeHtml(kepala)} (${escapeHtml(noKk)})</div>
                    </div>
            `;

            if (kkDeleted) {
                htmlContent += `
                    <div class="alert alert-warning py-2 px-3 mb-2 small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <b>Perhatian:</b> Kartu Keluarga induk (<b>${escapeHtml(kepala)}</b>) saat ini juga berstatus <b>Terarsip</b>.
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="swalChkRestoreParentKK" checked>
                            <label class="form-check-label fw-bold text-dark" for="swalChkRestoreParentKK">
                                Sekaligus aktifkan kembali Kartu Keluarga (KK) induknya
                            </label>
                        </div>
                    </div>
                `;
            }
            htmlContent += `</div>`;

            Swal.fire({
                title: 'Pulihkan Data Penduduk?',
                html: htmlContent,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-undo me-1"></i> Ya, Pulihkan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const chk = document.getElementById('swalChkRestoreParentKK');
                    return {
                        restore_kk: chk ? (chk.checked ? 1 : 0) : 0
                    };
                }
            }).then(result => {
                if (!result.isConfirmed) return;

                $.post('<?= base_url('dtsen-se/restore-art') ?>', {
                    id_art: id,
                    sumber: sumber,
                    id_kk: idKk,
                    restore_kk: result.value.restore_kk
                }, function(res) {
                    if (res.status) {
                        if (uid) selectedUids.delete(uid);
                        $('#modalDetailKeluargaArsip').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dipulihkan!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        });
                        loadArsipTerpadu(true);
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal memulihkan data penduduk.', 'error');
                    }
                }, 'json');
            });
        });

        // =========================================================================
        // ♻️ AKSI 2: RESTORE KELUARGA (KK) / SEKELUARGA PENUH DARI BARIS MANA SAJA
        // =========================================================================
        $(document).on('click', '.btnRestoreKKRow', function() {
            const idKk = $(this).data('idkk');
            const uid = String($(this).data('uid') || '');
            const nama = $(this).data('nama') || 'Keluarga';
            const noKk = $(this).data('nokk') || '-';
            const delArt = parseInt($(this).data('delart') || 0, 10);

            let htmlContent = `
                <div class="text-start small">
                    <div class="p-2 bg-light rounded border mb-2">
                        <div><b>Kepala Keluarga:</b> ${escapeHtml(nama)}</div>
                        <div><b>No. KK:</b> ${escapeHtml(noKk)}</div>
                    </div>
            `;

            if (delArt > 0) {
                htmlContent += `
                    <div class="alert alert-info py-2 px-3 mb-0 small">
                        <i class="fas fa-users me-1"></i>
                        Keluarga ini juga memiliki <b>${delArt} anggota keluarga yang terarsip</b>.
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="swalChkIncludeMembers" checked>
                            <label class="form-check-label fw-bold text-dark" for="swalChkIncludeMembers">
                                Sekaligus pulihkan seluruh (${delArt}) anggota keluarga yang terarsip
                            </label>
                        </div>
                    </div>
                `;
            } else {
                htmlContent += `<p class="mb-0 text-muted">Data keluarga akan dikembalikan ke Daftar Keluarga Aktif.</p>`;
            }
            htmlContent += `</div>`;

            Swal.fire({
                title: 'Pulihkan Keluarga?',
                html: htmlContent,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-undo me-1"></i> Ya, Pulihkan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const chk = document.getElementById('swalChkIncludeMembers');
                    return {
                        include_members: chk ? (chk.checked ? 1 : 0) : 0
                    };
                }
            }).then(result => {
                if (!result.isConfirmed) return;

                $.post('<?= base_url('dtsen-se/restore') ?>', {
                    id_kk: idKk,
                    include_members: result.value.include_members
                }, function(res) {
                    if (res.status) {
                        if (uid) selectedUids.delete(uid);
                        $('#modalDetailKeluargaArsip').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dipulihkan!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        });
                        loadArsipTerpadu(true);
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal memulihkan data keluarga.', 'error');
                    }
                }, 'json');
            });
        });

        // Restore KK Saja (dari dalam drawer/modal)
        $(document).on('click', '.btnRestoreKKOnly', function() {
            const idKk = $(this).data('idkk');
            $.post('<?= base_url('dtsen-se/restore') ?>', {
                id_kk: idKk,
                include_members: 0
            }, function(res) {
                if (res.status) {
                    $('#modalDetailKeluargaArsip').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1600,
                        showConfirmButton: false
                    });
                    loadArsipTerpadu(true);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }, 'json');
        });

        // Restore 1 Keluarga Penuh (bisa dipicu langsung dari baris Penduduk maupun baris Keluarga!)
        $(document).on('click', '.btnRestoreSekeluargaRow', function() {
            const idKk = $(this).data('idkk');
            const kepala = $(this).data('kepala') || 'Keluarga';
            const noKk = $(this).data('nokk') || '-';
            const delArt = parseInt($(this).data('delart') || 0, 10);

            Swal.fire({
                title: 'Pulihkan 1 Keluarga Utuh?',
                html: `
                    <div class="text-start small">
                        <div class="p-2 bg-light rounded border mb-2">
                            <div><b>Kepala Keluarga:</b> ${escapeHtml(kepala)}</div>
                            <div><b>No. KK:</b> ${escapeHtml(noKk)}</div>
                        </div>
                        <p class="mb-0">Sistem akan mengaktifkan kembali <b>Kartu Keluarga (KK)</b> beserta <b>seluruh anggota keluarga (${delArt} orang)</b> yang sedang terarsip dalam satu langkah.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-users me-1"></i> Ya, Pulihkan Sekeluarga!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (!result.isConfirmed) return;

                $.post('<?= base_url('dtsen-se/restore-sekeluarga') ?>', {
                    id_kk: idKk,
                    mode: 'full'
                }, function(res) {
                    if (res.status) {
                        $('#modalDetailKeluargaArsip').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sekeluarga Dipulihkan!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadArsipTerpadu(true);
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal memulihkan keluarga.', 'error');
                    }
                }, 'json');
            });
        });

        // =========================================================================
        // ⚡ AKSI 3: BULK RESTORE (PULIHKAN MASSAL)
        // =========================================================================
        $('#btnExecuteBulkRestore').on('click', function() {
            const uids = Array.from(selectedUids);
            if (uids.length === 0) {
                Swal.fire('Perhatian', 'Pilih minimal satu data arsip yang ingin dipulihkan.', 'warning');
                return;
            }

            const includeRelated = $('#chkBulkIncludeRelated').is(':checked') ? 1 : 0;
            const kkCount = $('#selectedKkCount').text();
            const artCount = $('#selectedArtCount').text();

            // Siapkan payload items lengkap
            const payloadItems = uids.map(uid => {
                const obj = itemMapByUid.get(uid);
                if (obj) {
                    return {
                        uid: obj.uid,
                        record_type: obj.record_type,
                        sumber: obj.sumber,
                        id: obj.id,
                        id_kk: obj.id_kk
                    };
                }
                return uid;
            });

            Swal.fire({
                title: `Restore ${uids.length} Data Terpilih?`,
                html: `
                    <div class="text-start small">
                        <div class="p-2 bg-light rounded border mb-2">
                            <div><b>Rincian Pilihan:</b> <span class="badge bg-danger">${kkCount} Keluarga</span> <span class="badge bg-info text-dark">${artCount} Penduduk</span></div>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="swalBulkIncludeRelated" ${includeRelated ? 'checked' : ''}>
                            <label class="form-check-label fw-bold text-dark" for="swalBulkIncludeRelated">
                                Pulihkan utuh sekeluarga (otomatis aktifkan KK induk & pulihkan anggota keluarga terkait)
                            </label>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `<i class="fas fa-undo-alt me-1"></i> Ya, Restore Semua (${uids.length})`,
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const chk = document.getElementById('swalBulkIncludeRelated');
                    return {
                        include_related: chk ? (chk.checked ? 1 : 0) : 0
                    };
                }
            }).then(result => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Memproses Pemulihan Massal...',
                    text: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: '<?= base_url('dtsen-se/restore-bulk') ?>',
                    type: 'POST',
                    data: {
                        items: JSON.stringify(payloadItems),
                        include_related: result.value.include_related
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            selectedUids.clear();
                            updateBulkSelectionUI();
                            Swal.fire({
                                icon: 'success',
                                title: 'Restore Massal Berhasil!',
                                text: res.message,
                                timer: 2200,
                                showConfirmButton: false
                            });
                            loadArsipTerpadu(true);
                        } else {
                            Swal.fire('Gagal', res.message || 'Gagal melakukan pemulihan massal.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error Server', 'Terjadi kesalahan saat memproses pemulihan massal.', 'error');
                    }
                });
            });
        });

        // =========================================================================
        // 📋 COPY CLIPBOARD (NIK & NO KK)
        // =========================================================================
        $(document).on('click', '.btnCopyClipboard', function() {
            const value = $(this).attr('data-value');
            const label = $(this).attr('data-label') || 'Nomor';
            if (!value) return;

            navigator.clipboard.writeText(value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: `${label} ${value} Tersalin`,
                    timer: 1400,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        });
    });
</script>

<?= $this->endSection(); ?>
