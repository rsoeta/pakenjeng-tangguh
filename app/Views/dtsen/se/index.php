<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .nav-tabs-right {
        border-left: 1px solid #ddd;
        height: 100%;
        padding-left: 0;
    }

    .nav-tabs-right .nav-link {
        text-align: left;
        border-radius: 0;
        border-left: 3px solid transparent;
    }

    .nav-tabs-right .nav-link.active {
        background-color: #f8f9fa;
        border-left: 3px solid #007bff;
    }
</style>

<div class="content-wrapper mt-0">
    <div class="content-header">
        <h3 class="mb-2">👨‍👩‍👧‍👦 Data Keluarga DTSEN</h3>
        <small class="text-muted">Kelola data keluarga aktif dan draft pembaruan</small>
    </div>

    <section class="content">
        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom pb-0">
                <ul class="nav nav-tabs card-header-tabs" id="tabKeluarga" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tabDaftar-tab" data-bs-toggle="tab" data-bs-target="#tabDaftar" type="button" role="tab">
                            🔵 Daftar Keluarga
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tabDraft-tab" data-bs-toggle="tab" data-bs-target="#tabDraft" type="button" role="tab">
                            🟡 Draft
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tabSubmitted-tab" data-bs-toggle="tab"
                            data-bs-target="#tabSubmitted" type="button" role="tab">
                            🟢 Submitted
                        </button>
                    </li>
                    <!-- Admin Only -->
                    <?php if ($role_id <= 3): ?>
                        <li class="nav-item">
                            <button class="nav-link" id="tabArsip-tab" data-bs-toggle="tab" data-bs-target="#tabArsip" type="button" role="tab">
                                🔴 Arsip
                            </button>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>

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

                                        <!-- 🔐 AKSES -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Akses</label>
                                            <div class="form-control form-control-sm bg-light text-nowrap" id="filterAkses">
                                                Akses Desa Penuh
                                            </div>
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

                                        <!-- 📊 DESIL -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Desil</label>
                                            <select class="form-select form-select-sm" id="filterDesil">
                                                <option value="">Semua</option>
                                                <option value="belum">Belum</option>
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>">Desil <?= $i ?></option>
                                                <?php endfor; ?>
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
                                    <div class="btn-group w-100" role="group">
                                        <button id="btnReloadKeluarga" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-sync-alt"></i> Muat Ulang
                                        </button>
                                        <button id="btnTambahKeluarga" class="btn btn-primary btn-sm">
                                            <i class="fas fa-user-plus"></i> Tambah Keluarga
                                        </button>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- ===================== TAB 2: DRAFT PEMBARUAN ===================== -->
                <div class="tab-pane fade" id="tabDraft" role="tabpanel">
                    <div class="d-flex justify-content-end mb-2">
                        <button id="btnReloadDraft" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-sync-alt"></i> Muat Ulang
                        </button>
                    </div>

                    <table id="tableDraftKeluarga" class="table table-striped table-hover nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th></th>
                                <th>No.</th>
                                <th>Kepala Keluarga</th>
                                <th>No KK</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Petugas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- ===================== TAB 2: SUBMITTED PEMBARUAN ===================== -->
                <div class="tab-pane fade" id="tabSubmitted" role="tabpanel">
                    <div class="d-flex justify-content-end mb-2">
                        <button id="btnReloadSubmitted" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-sync-alt"></i> Muat Ulang
                        </button>
                    </div>

                    <table id="tableSubmitted" class="table table-striped table-hover nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th></th>
                                <th>No.</th>
                                <th>Kepala Keluarga</th>
                                <th>No KK</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Petugas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- ===================== TAB 4: ARSIP ===================== -->
                <div class="tab-pane fade" id="tabArsip" role="tabpanel">

                    <div class="card shadow-sm">
                        <div class="card-header bg-light border-bottom pb-0">
                            <!-- 🔥 TAB ATAS UNTUK ARSIP -->
                            <ul class="nav nav-tabs" id="arsipTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" id="arsipKeluarga-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#arsipKeluarga"
                                        type="button" role="tab">
                                        📁 Arsip Keluarga
                                    </button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link" id="arsipAnggota-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#arsipAnggota"
                                        type="button" role="tab">
                                        👤 Arsip Anggota
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body tab-content">

                            <!-- ===================== SUBTAB: ARSIP KELUARGA ===================== -->
                            <div class="tab-pane fade show active" id="arsipKeluarga" role="tabpanel">
                                <h5 class="fw-bold mb-3">📁 Arsip Keluarga</h5>

                                <table id="tableArsipKeluarga" class="table table-striped table-hover w-100">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>Kepala Keluarga</th>
                                            <th>No KK</th>
                                            <th>Alamat</th>
                                            <th>RW/RT</th>
                                            <th>Tanggal Hapus</th>
                                            <th>Alasan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <!-- ===================== SUBTAB: ARSIP ANGGOTA ===================== -->
                            <div class="tab-pane fade" id="arsipAnggota" role="tabpanel">
                                <h5 class="fw-bold mb-3">👤 Arsip Anggota Keluarga</h5>

                                <table id="tableArsipAnggota" class="table table-striped table-hover w-100">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No.</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Hubungan</th>
                                            <th>ID KK</th>
                                            <th>Tanggal Hapus</th>
                                            <th>Alasan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

<!-- 🔹 Modal Input Desil -->
<?= $this->include('dtsen/se/modal_input_desil'); ?>

<script src="<?= base_url('assets/js/input_desil.js'); ?>"></script>

<script>
    $(document).ready(function() {

        // =========================
        // 🔎 FILTER STATE (GLOBAL)
        // =========================
        const filterKeluarga = {
            rw: '',
            rt: '',
            status: '',
            desil: ''
        };

        function loadRW() {
            $.getJSON('/dtsen-se/list-rw', function(res) {
                const $rw = $('#filterRW');
                $rw.empty().append('<option value="">Semua RW</option>');

                res.data.forEach(rw => {
                    $rw.append(`<option value="${rw}">RW ${rw}</option>`);
                });
            });
        }

        loadRW();

        // ========================= 🔵 TABLE DAFTAR KELUARGA =========================
        const tableKeluarga = $('#tableKeluarga').DataTable({
            ajax: {
                url: '/dtsen-se/tabel_data',
                type: 'POST',
                data: function(d) {
                    d.filter = filterKeluarga;
                    console.log('📤 FILTER DIKIRIM:', d.filter);
                },
                dataSrc: 'data'
            },

            responsive: true,
            pageLength: 10,
            autoWidth: false,

            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
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
                    render: (d, t, r, m) => m.row + 1,
                    className: 'text-center'
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
                    render: function(noKK) {
                        if (!noKK) return '-';
                        return `
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold">${noKK}</span>
                        <button 
                            type="button"
                            class="btn btn-outline-secondary btn-xs btnCopyNoKK"
                            data-value="${noKK}"
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
                    className: 'text-center',
                    render: r => `
                <span class="badge bg-light text-dark border">RW ${r.rw}</span>
                <span class="mx-1">/</span>
                <span class="badge bg-info text-dark">RT ${r.rt}</span>
            `
                },

                // 🔹 Status
                {
                    data: 'usulan_status',
                    className: 'text-center',
                    render: function(status, type, row) {

                        if (!status) {
                            return `<span class="badge bg-secondary">Belum Ada Pembaruan</span>`;
                        }

                        if (status === 'draft') {
                            if (row.is_submitted_ready == 1) {
                                return `<span class="badge bg-info text-dark">Submitted</span>`;
                            }
                            return `<span class="badge bg-warning text-dark">Draft</span>`;
                        }

                        if (status === 'submitted') {
                            return `<span class="badge bg-info text-dark">Submitted</span>`;
                        }

                        if (status === 'verified' || status === 'diverifikasi') {
                            return `<span class="badge bg-primary">Verified</span>`;
                        }

                        return `<span class="badge bg-secondary">Belum Ada Pembaruan</span>`;
                    }
                },

                // 🔹 Desil
                {
                    data: 'kategori_desil',
                    className: 'text-center',
                    render: d => {
                        if (!d) return '<span class="badge bg-secondary">Belum</span>';
                        const n = parseInt(d);
                        const warna = n <= 3 ? 'success' : n <= 5 ? 'warning' : 'danger';
                        return `<span class="badge bg-${warna}">${n}</span>`;
                    }
                },

                // 🔹 Aksi
                {
                    data: null,
                    className: 'text-start text-nowrap',
                    orderable: false,
                    searchable: false,
                    render: row => `
                <a href="/pembaruan-keluarga/detail/${row.id_kk}" 
                   class="btn btn-outline-dark btn-sm me-1">
                    <i class="fas fa-users-cog"></i>
                </a>

                <button class="btn btn-outline-primary btn-sm btnInputDesil me-1"
                    data-id="${row.id_kk}"
                    data-nama="${row.kepala_keluarga}"
                    data-nokk="${row.no_kk}"
                    data-alamat="${row.alamat}"
                    data-desil="${row.kategori_desil ?? ''}">
                    <i class="fas fa-hand-holding-heart"></i>
                </button>

                <button class="btn btn-outline-danger btn-sm btnDeleteKeluarga"
                    data-id="${row.id_kk}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `
                }
            ]
        });

        // ========================= 🎛 FILTER HANDLER =========================
        function reloadTableKeluarga() {
            tableKeluarga.ajax.reload();
        }

        // Wilayah / Akses
        $('#filterWilayah').on('change', function() {
            filterKeluarga.wilayah = $(this).val() || null;
            reloadTableKeluarga();
        });

        // RW
        const $filterRW = $('#filterRW');
        const $filterRT = $('#filterRT');

        /**
         * ===============================
         * 🔁 FETCH RT BY RW
         * ===============================
         */
        $(document).on('change', '#filterRW', function() {
            const rw = $(this).val();

            filterKeluarga.rw = rw || '';
            filterKeluarga.rt = '';

            const $rt = $('#filterRT');
            $rt.prop('disabled', !rw)
                .empty()
                .append('<option value="">Semua RT</option>');

            if (!rw) {
                $('#tableKeluarga').DataTable().ajax.reload();
                return;
            }

            $.getJSON(`/dtsen-se/list-rt/${rw}`, function(res) {
                res.data.forEach(rt => {
                    $rt.append(`<option value="${rt}">RT ${rt}</option>`);
                });
            });

            $('#tableKeluarga').DataTable().ajax.reload();
        });

        $(document).on('change', '#filterRT', function() {
            filterKeluarga.rt = $(this).val() || '';
            $('#tableKeluarga').DataTable().ajax.reload();
        });

        // RT
        $('#filterRT').on('change', function() {
            filterKeluarga.rt = $(this).val() || null;
            reloadTableKeluarga();
        });

        // Status
        $('#filterStatus').on('change', function() {
            filterKeluarga.status = $(this).val() || null;
            reloadTableKeluarga();
        });

        // Desil
        $('#filterDesil').on('change', function() {
            filterKeluarga.desil = $(this).val() || null;
            reloadTableKeluarga();
        });

        $('.filter-keluarga').on('change', function() {
            const key = $(this).data('filter');
            filterKeluarga[key] = $(this).val();

            console.log('🧠 FILTER UPDATE:', filterKeluarga);

            $('#tableKeluarga').DataTable().ajax.reload();
        });


        // Reset
        $('#btnResetFilter').on('click', function() {

            filterKeluarga.rw = '';
            filterKeluarga.rt = '';
            filterKeluarga.status = '';
            filterKeluarga.desil = '';

            // reset UI
            $('.filter-keluarga').val('');

            $('#tableKeluarga').DataTable().ajax.reload();
        });

        function reloadTableKeluarga() {
            $('#tableKeluarga').DataTable().ajax.reload(null, false);
        }

        /* ======================================================
         * 🔎 FILTER HANDLER — TABLE KELUARGA
         * ====================================================== */

        // Helper: ambil nilai filter (null jika kosong)
        function getFilterValue(selector) {
            const v = $(selector).val();
            return (v === '' || v === null) ? null : v;
        }

        // Inject filter ke AJAX DataTables
        function applyTableKeluargaFilter() {
            tableKeluarga.ajax.reload();
        }

        // Bind auto-submit on change
        $(document).on('change', `
            #filterWilayah,
            #filterRW,
            #filterRT,
            #filterStatus,
            #filterDesil
        `, function() {
            applyTableKeluargaFilter();
        });

        // Reset filter
        $(document).on('click', '#btnResetFilter', function() {
            $('#filterWilayah').val('');
            $('#filterRW').val('');
            $('#filterRT').val('');
            $('#filterStatus').val('');
            $('#filterDesil').val('');

            applyTableKeluargaFilter();
        });

        $('#filterRW').on('change', () => tableKeluarga.ajax.reload());
        $('#btnTambahKeluarga').on('click', () => window.location.href = '/pembaruan-keluarga/tambah');
        $('#btnReloadKeluarga').on('click', () => {
            tableKeluarga.ajax.reload(null, false);
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
            setTimeout(() => {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Muat Ulang');
            }, 800);
        });

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


        // ========================= 🟡 TABLE DRAFT PEMBARUAN =========================
        const tableDraft = $('#tableDraftKeluarga').DataTable({
            ajax: {
                url: '/pembaruan-keluarga/data?status=draft',
                type: 'GET',
                dataType: 'json',
                dataSrc: json => json.data || []
            },
            columns: [{
                    data: null,
                    defaultContent: ''
                },
                {
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'nama_kepala',
                    title: 'Kepala Keluarga'
                },
                {
                    data: 'no_kk_target',
                    title: 'No KK'
                },
                {
                    data: 'status',
                    title: 'Status',
                    render: s => `<span class="badge bg-secondary">${(s || 'draft').toUpperCase()}</span>`
                },
                {
                    data: 'updated_at',
                    title: 'Tanggal Dibuat',
                    render: d => d ? new Date(d).toLocaleString('id-ID') : '-'
                },
                {
                    data: 'created_by_name',
                    title: 'Petugas',
                    defaultContent: '-'
                },
                {
                    data: 'id',
                    title: 'Aksi',
                    orderable: false,
                    render: function(id, type, row, meta) {
                        return `
                        <a href="/pembaruan-keluarga/lanjutkan/${id}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Lanjutkan
                        </a>
                        <button class="btn btn-danger btn-sm btnDeleteUsulan" data-id="${row.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                `;
                    }
                }
            ],
            responsive: true,
            pageLength: 10,
            createdRow: row => $(row).find('td').css('text-align', 'left'),
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
        });

        $('#btnReloadDraft').on('click', function() {
            tableDraft.ajax.reload(null, false);
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
            setTimeout(() => {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Muat Ulang');
            }, 800);
        });

        // ========================= 🟢 TABLE SUBMITTED =========================
        const tableSubmitted = $('#tableSubmitted').DataTable({
            ajax: {
                url: '/pembaruan-keluarga/data?submitted=1',
                type: 'GET',
                dataType: 'json',
                dataSrc: json => json.data || []
            },
            columns: [{
                    data: null,
                    defaultContent: ''
                },
                {
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'nama_kepala'
                },
                {
                    data: 'no_kk_target',
                    className: 'text-nowrap',
                    render: function(noKK, type, row) {
                        if (!noKK) return '-';

                        return `
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold">${noKK}</span>
                                <button 
                                    type="button"
                                    class="btn btn-outline-secondary btn-xs btnCopyNoKK"
                                    data-value="${noKK}"
                                    title="Salin No KK">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        `;
                    }
                },

                {
                    data: 'status',
                    render: () => `<span class="badge bg-info">SUBMITTED</span>`
                },
                {
                    data: 'updated_at',
                    render: d => d ? new Date(d).toLocaleString('id-ID') : '-'
                },
                {
                    data: 'created_by_name',
                    render: function(name, type, row) {

                        const nope = row.created_by_nope || '';

                        // Jika tidak ada nomor → tampilkan nama biasa
                        if (!nope) return name || '-';

                        // Normalisasi nomor → ubah ke 62xxx
                        let phone = nope.replace(/\D/g, ""); // buang spasi / tanda
                        if (phone.startsWith("0")) phone = "62" + phone.substring(1);
                        else if (!phone.startsWith("62")) phone = "62" + phone;

                        return `
                            <a href="https://wa.me/${phone}" 
                            class="text-success fw-bold" 
                            target="_blank">
                                <i class="fab fa-whatsapp"></i> ${name}
                            </a>
                        `;
                    }
                },

                // PERBAIKAN DI SINI
                {
                    data: 'id',
                    render: function(id, type, row, meta) {
                        return `
                    <a href="/pembaruan-keluarga/lanjutkan/${id}" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <button class="btn btn-danger btn-sm btnDeleteUsulan" data-id="${row.id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                `;
                    }
                }
            ],
            responsive: true,
            pageLength: 10
        });

        // ========================= 📋 COPY NO KK =========================
        $(document).on('click', '.btnCopyNoKK', function() {
            const value = $(this).data('value');

            if (!value) return;

            navigator.clipboard.writeText(value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin',
                    text: 'No. KK berhasil disalin ke clipboard',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top'
                });
            }).catch(() => {
                Swal.fire('Gagal', 'Tidak dapat menyalin No. KK', 'error');
            });
        });

        // ========================= 🔥 HAPUS USULAN KELUARGA =========================
        $(document).on('click', '.btnDeleteUsulan', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Hapus Usulan?',
                text: 'Data usulan beserta ART-nya akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus Permanen'
            }).then(res => {
                if (!res.isConfirmed) return;

                fetch('/pembaruan-keluarga/delete-keluarga', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id
                        })
                    })
                    .then(r => r.json())
                    .then(json => {
                        Swal.fire(json.status ? 'Berhasil' : 'Gagal', json.message, json.status ? 'success' : 'error');

                        // Reload kedua tabel
                        tableDraftKeluarga?.ajax?.reload(null, false);
                        tableSubmitted?.ajax?.reload(null, false);
                    });
            });
        });

        // 🔥 Reload submitted
        $('#btnReloadSubmitted').on('click', function() {
            tableSubmitted.ajax.reload(null, false);
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
            setTimeout(() => {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Muat Ulang');
            }, 800);
        });

        document.addEventListener("shown.bs.tab", function(event) {
            const target = event.target.getAttribute("data-bs-target");

            if (target === "#arsipKeluarga" && !arsipKeluargaLoaded) {
                loadArsipKeluarga();
                arsipKeluargaLoaded = true;
            }

            if (target === "#arsipAnggota" && !arsipAnggotaLoaded) {
                loadArsipAnggota();
                arsipAnggotaLoaded = true;
            }
        });

        // ========================= 🔴 TABLE ARSIP =========================

        $(document).on("click", ".nav-link", function() {
            const target = $(this).data("target");

            if (target === "#tableArsipKeluarga") showArsipKeluarga();
            if (target === "#tableArsipAnggota") showArsipAnggota();
        });

        let arsipKeluargaLoaded = false;
        let arsipAnggotaLoaded = false;

        function showArsipKeluarga() {
            const wrap = $('#arsipContent');
            wrap.html($('#tmplArsipKeluarga').html()); // tempelkan template

            loadArsipKeluarga(); // setelah tabel ada di DOM → baru inisialisasi DT
        }

        function showArsipAnggota() {
            const wrap = $('#arsipContent');
            wrap.html($('#tmplArsipAnggota').html());

            loadArsipAnggota();
        }

        let dtArsipKeluarga = null;
        let dtArsipAnggota = null;

        // =============== TABLE ARSIP KELUARGA =================
        function loadArsipKeluarga() {
            if (dtArsipKeluarga) {
                dtArsipKeluarga.ajax.reload(null, false);
                return;
            }

            dtArsipKeluarga = $('#tableArsipKeluarga').DataTable({
                ajax: {
                    url: '/dtsen-se/tabel_arsip',
                    type: 'POST',
                    dataSrc: 'data'
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1
                    },
                    {
                        data: 'kepala_keluarga'
                    },
                    {
                        data: 'no_kk'
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: null,
                        render: r => `RW ${r.rw} / RT ${r.rt}`
                    },
                    {
                        data: 'deleted_at'
                    },
                    {
                        data: 'delete_reason'
                    },
                    {
                        data: null,
                        render: row => `
                    <button class="btn btn-success btn-sm btnRestoreKK"
                        data-id="${row.id_kk}">
                        <i class="fas fa-undo"></i> Restore
                    </button>`
                    }
                ]
            });
        }

        // restore KK
        $(document).on('click', '.btnRestoreKK', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Pulihkan Keluarga?",
                text: "Data akan dikembalikan ke daftar keluarga.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, kembalikan!"
            }).then(result => {
                if (!result.isConfirmed) return;

                $.post('/dtsen-se/restore', {
                    id_kk: id
                }, function(res) {
                    if (res.status) {
                        Swal.fire("Berhasil!", res.message, "success");
                        loadArsipKeluarga(); // reload tanpa reinit
                    } else {
                        Swal.fire("Gagal!", res.message, "error");
                    }
                }, 'json');
            });
        });

        // =============== TABLE ARSIP ANGGOTA =================
        function loadArsipAnggota() {
            if (dtArsipAnggota) {
                dtArsipAnggota.ajax.reload(null, false);
                return;
            }

            dtArsipAnggota = $('#tableArsipAnggota').DataTable({
                ajax: {
                    url: '/dtsen-se/arsip-anggota',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1
                    },
                    {
                        data: 'nik'
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'jenis_shdk'
                    },
                    {
                        data: 'id_kk'
                    },
                    {
                        data: 'deleted_at'
                    },
                    {
                        data: 'delete_reason'
                    },
                    {
                        data: null,
                        render: row => `
                    <button class="btn btn-success btn-sm btnRestoreAnggota"
                        data-id="${row.id}"
                        data-source="${row.sumber}">
                        <i class="fas fa-undo"></i> Restore
                    </button>`
                    }
                ]
            });
        }

        // restore anggota
        $(document).on('click', '.btnRestoreAnggota', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Pulihkan Anggota?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, pulihkan!"
            }).then(result => {
                if (!result.isConfirmed) return;

                $.post('/dtsen-se/restore-art', {
                    id_art: id
                }, function(res) {
                    if (res.status) {
                        Swal.fire("Berhasil!", res.message, "success");
                        loadArsipAnggota();
                    } else {
                        Swal.fire("Gagal!", res.message, "error");
                    }
                }, 'json');
            });
        });

        // Saat user klik tab
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {

            const target = $(e.target).attr('data-bs-target'); // ex: #tabDraft

            let tab = 'daftar';
            if (target === '#tabDraft') tab = 'draft';
            if (target === '#tabSubmitted') tab = 'submitted';
            if (target === '#tabDaftar') tab = 'daftar';

            // 1. Update URL TANPA reload halaman
            history.replaceState(null, "", "?tab=" + tab);

            // 2. Reload DataTable sesuai tab aktif
            if (tab === 'daftar') {
                tableKeluarga.ajax.reload();
            }
            if (tab === 'draft') {
                tableDraft.ajax.reload();
            }
            if (tab === 'submitted') {
                tableSubmitted.ajax.reload();
            }
        });

        function activateTabFromURL() {
            const url = new URL(window.location.href);
            const tab = url.searchParams.get('tab') || 'daftar';

            // Peta tab terhadap ID tombol dan tab-pane
            const tabMap = {
                daftar: {
                    btn: '#tabDaftar-tab',
                    pane: '#tabDaftar',
                    table: tableKeluarga
                },
                draft: {
                    btn: '#tabDraft-tab',
                    pane: '#tabDraft',
                    table: tableDraft
                },
                submitted: {
                    btn: '#tabSubmitted-tab',
                    pane: '#tabSubmitted',
                    table: tableSubmitted
                },
            };

            const target = tabMap[tab] || tabMap.daftar;

            // Aktifkan tombol tab
            $(target.btn).tab('show');

            // Setelah pane ditampilkan, adjust kolom DataTables
            setTimeout(() => {
                if (target.table) {
                    target.table.columns.adjust().draw(false);
                }
            }, 100);
        }

        // Jalankan otomatis ketika halaman load
        activateTabFromURL();



    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get("tab");

        if (!tab) return;

        // Mapping parameter → id tab
        const tabMap = {
            "draft": "#tabDraft",
            "submitted": "#tabSubmitted"
        };

        const targetPane = tabMap[tab];
        if (!targetPane) return;

        // Cari tombol tab bootstrap-nya (bukan <a>, tapi <button>)
        const tabButton = document.querySelector(`button[data-bs-target="${targetPane}"]`);

        if (tabButton) {
            // Trigger Bootstrap tab show
            const tabInstance = new bootstrap.Tab(tabButton);
            tabInstance.show();

            // Scroll ke area tab (opsional)
            setTimeout(() => {
                tabButton.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
            }, 150);
        }
    });
</script>


<?= $this->endSection(); ?>