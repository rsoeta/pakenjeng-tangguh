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
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                        <!-- <div class="d-flex align-items-center">
                            <label for="filterRW" class="form-label fw-bold mb-0 me-2">Filter RW:</label>
                            <select id="filterRW" class="form-select form-select-sm" style="width: 120px;">
                                <option value="">[ Semua ]</option>
                                <?php foreach ($dataRW as $rw): ?>
                                    <option value="<?= $rw['rw'] ?>"><?= $rw['rw'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div> -->
                        <!-- buat div dibwah ini rata kanan -->
                        <div class="ms-auto d-flex gap-2 mb-2">
                            <!-- buat kedua tombol dibawah merapat dalam satu baris -->
                            <div class="btn-group" role="group">
                                <button id="btnReloadKeluarga" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-sync-alt"></i> Muat Ulang
                                </button>
                                <button id="btnTambahKeluarga" class="btn btn-primary btn-sm">
                                    <i class="fas fa-user-plus"></i> Tambah Keluarga Baru
                                </button>
                            </div>
                        </div>
                    </div>

                    <table id="tableKeluarga" class="table table-striped table-hover nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th></th>
                                <th>No.</th>
                                <th>Kepala Keluarga</th>
                                <th>No KK</th>
                                <th>Alamat</th>
                                <th>Wilayah</th>
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

        // ========================= 🔵 TABLE DAFTAR KELUARGA =========================
        const tableKeluarga = $('#tableKeluarga').DataTable({
            ajax: {
                url: '/dtsen-se/tabel_data',
                type: 'POST',
                data: d => d.filterRW = $('#filterRW').val(),
                dataSrc: 'data'
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
                    data: 'kepala_keluarga',
                    className: 'text-capitalize'
                },
                {
                    data: 'no_kk'
                },
                {
                    data: 'alamat',
                    render: d => d || '-'
                },
                {
                    data: null,
                    render: r => `<span class="badge bg-light text-dark border">RW ${r.rw}</span> / <span class="badge bg-info text-dark">RT ${r.rt}</span>`,
                    className: 'text-center'
                },
                {
                    data: 'kategori_desil',
                    render: d => {
                        if (!d) return '<span class="badge bg-secondary">Belum</span>';
                        const n = parseInt(d);
                        const warna = n <= 2 ? 'danger' : n <= 4 ? 'warning' : 'success';
                        return `<span class="badge bg-${warna}">${n}</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-nowrap',
                    render: row => `
                        <a href="/pembaruan-keluarga/detail/${row.id_kk}" class="btn btn-success btn-sm me-1" title="Pembaruan Keluarga">
                            <i class="fas fa-users-cog"></i>
                        </a>
                        <button class="btn btn-primary btn-sm btnInputDesil"
                            data-id="${row.id_kk}"
                            data-nama="${row.kepala_keluarga}"
                            data-nokk="${row.no_kk}"
                            data-alamat="${row.alamat}"
                            data-desil="${row.kategori_desil ?? ''}">
                            <i class="fas fa-hand-holding-heart"></i>
                        </button>
                         <!-- 🔥 Tambahkan tombol hapus -->
                        <button class="btn btn-danger btn-sm btnDeleteKeluarga" data-id="${row.id_kk}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `
                }
            ],
            responsive: true,
            pageLength: 10,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            createdRow: row => $(row).find('td').css('text-align', 'left'),
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
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
                    data: 'no_kk_target'
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