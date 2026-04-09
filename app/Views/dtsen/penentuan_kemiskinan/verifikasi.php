<?= $this->extend('templates/index') ?>
<?= $this->section('content') ?>

<style>
    .btn.disabled {
        pointer-events: none;
        opacity: 0.65;
    }
</style>
<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <?php
            $uri = service('uri');
            $segment = $uri->getSegment(3);
            ?>

            <div class="d-flex justify-content-between align-items-center mb-3">

                <!-- TITLE -->
                <h5 class="m-0">
                    <?= $title ?>
                </h5>

                <!-- NAV FLOW BUTTON -->
                <div class="btn-group" role="group">

                    <a href="<?= base_url('dtsen/kemiskinan/penentuan') ?>"
                        class="btn btn-sm <?= $segment == 'penentuan' ? 'btn-primary disabled' : 'btn-outline-primary' ?>">
                        <i class="fas fa-edit me-1"></i> Penentuan
                    </a>

                    <a href="<?= base_url('dtsen/kemiskinan/verifikasi') ?>"
                        class="btn btn-sm <?= $segment == 'verifikasi' ? 'btn-warning disabled' : 'btn-outline-warning' ?>">
                        <i class="fas fa-check-circle me-1"></i> Verifikasi
                    </a>

                    <a href="<?= base_url('dtsen/kemiskinan/final') ?>"
                        class="btn btn-sm <?= $segment == 'final' ? 'btn-success disabled' : 'btn-outline-success' ?>">
                        <i class="fas fa-database me-1"></i> Final
                    </a>

                </div>

            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <div class="row mb-3">

                        <div class="col-md-2">
                            <select id="filterRw" class="form-control form-control-sm">
                                <option value="">Semua RW</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select id="filterRt" class="form-control form-control-sm">
                                <option value="">Semua RT</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select id="filterDesil" class="form-control form-control-sm">
                                <option value="">Semua Desil</option>
                                <option value="none">Tanpa Desil</option>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>">Desil <?= $i ?></option>
                                <?php endfor ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select id="filterStatus" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="miskin">Miskin</option>
                                <option value="tidak_miskin">Tidak Miskin</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <input type="text" id="filterPetugas" class="form-control form-control-sm" placeholder="Cari Petugas Entri">
                        </div>

                    </div>

                    <div class="table-responsive">

                        <table id="tableVerifikasi" class="table table-bordered table-striped table-hover">

                            <thead class="table-light">

                                <tr>
                                    <th width="40">No</th>
                                    <th>Kepala Keluarga</th>
                                    <th>NIK</th>
                                    <th>No KK</th>
                                    <th width="60">RW</th>
                                    <th width="60">RT</th>
                                    <th width="80">Desil</th>
                                    <th width="120">Status</th>
                                    <th>Petugas Entri</th>
                                    <th width="180">Aksi</th>
                                </tr>

                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetail">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Detail Penentuan Kemiskinan
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detail-kemiskinan"></div>
            </div>
            <div class="modal-footer d-flex justify-content-between w-100">
                <button class="btn btn-warning btn-rollback" id="btnRollbackDetail">
                    <i class="fas fa-undo"></i> Rollback
                </button>
                <?php if (session()->role_id < 4): ?>
                    <button
                        class="btn btn-danger btn-tolak"
                        id="btnTolakDetail">
                        <i class="fas fa-times"></i>
                        Tolak
                    </button>

                    <button
                        class="btn btn-success btn-validasi"
                        id="btnValidasiDetail">
                        <i class="fas fa-check"></i>
                        Validasi
                    </button>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<script>
    let tableVerifikasi;
    let dropdownData = {};

    let filters = {
        rw: null,
        rt: null,
        desil: null
    };

    $(document).ready(function() {

        tableVerifikasi = $('#tableVerifikasi').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/dtsen/kemiskinan/verifikasi-data',
                data: function(d) {
                    d.rw = $('#filterRw').val();
                    d.rt = $('#filterRt').val();
                    d.desil = $('#filterDesil').val();
                    d.status = $('#filterStatus').val();
                    d.petugas = $('#filterPetugas').val();
                },
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
                    data: 'nik'
                },
                {
                    data: 'no_kk'
                },
                {
                    data: 'rw'
                },
                {
                    data: 'rt'
                },
                {
                    data: 'kategori_desil',
                    render: d => d ? `<span class="badge bg-info">Desil ${d}</span>` : `<span class="badge bg-secondary">-</span>`
                },
                {
                    data: 'status_kemiskinan',
                    render: d => d === 'miskin' ?
                        `<span class="badge bg-danger">Miskin</span>` : `<span class="badge bg-success">Tidak Miskin</span>`
                },
                {
                    data: 'petugas_entri'
                },
                {
                    data: 'id',
                    render: id => `
                <button class="btn btn-sm btn-info btn-detail" data-id="${id}">
                    <i class="fas fa-eye"></i> Detail
                </button>
                <button class="btn btn-sm btn-warning btn-rollback" data-id="${id}">
                    <i class="fas fa-undo"></i> Rollback
                </button>
            `
                }
            ]
        });

        loadRwRtDropdown();

        $('#filterRw, #filterRt, #filterDesil, #filterStatus').on('change', function() {
            tableVerifikasi.ajax.reload();
        });

        $('#filterPetugas').on('keyup', function() {
            tableVerifikasi.ajax.reload();
        });

    });

    /* =========================================================
       DROPDOWN RW RT
    ========================================================= */
    function loadRwRtDropdown() {

        $.get('/dropdown-rwrt', function(res) {

            dropdownData = res; // 🔥 simpan global

            const rwSelect = $('#filterRw');
            const rtSelect = $('#filterRt');

            rwSelect.html('<option value="">Semua RW</option>');
            rtSelect.html('<option value="">Semua RT</option>');

            Object.keys(res).forEach(function(rw) {
                rwSelect.append(`<option value="${rw}">RW ${rw}</option>`);
            });

            // =============================
            // RW CHANGE
            // =============================
            rwSelect.off('change').on('change', function() {

                const selectedRw = $(this).val();

                rtSelect.html('<option value="">Semua RT</option>');

                if (selectedRw && dropdownData[selectedRw]) {
                    dropdownData[selectedRw].forEach(function(rt) {
                        rtSelect.append(`<option value="${rt}">RT ${rt}</option>`);
                    });
                }

                // reset RT
                rtSelect.val('');

                // reload table
                if (tableVerifikasi) {
                    tableVerifikasi.ajax.reload(null, false);
                }

            });

            // =============================
            // RT CHANGE
            // =============================
            rtSelect.off('change').on('change', function() {

                if (tableVerifikasi) {
                    tableVerifikasi.ajax.reload(null, false);
                }

            });

        });
    }

    let currentVerifikasiId = null;

    $(document).on('click', '.btn-detail', function() {

        let id = $(this).data('id');
        currentVerifikasiId = id;

        $('#btnRollbackDetail').data('id', id);
        $('#btnTolakDetail').data('id', id);
        $('#btnValidasiDetail').data('id', id);

        $('#detail-kemiskinan').html(`
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin"></i><br>
            Memuat data...
        </div>
    `);

        $('#modalDetail').modal('show');

        $.get('/dtsen/kemiskinan/detail', {
                id: id
            })
            .done(function(res) {

                let html = '';

                // ======================
                // IDENTITAS KK 🔥 BARU
                // ======================
                html += `
            <div class="mb-3">
                <h6>Identitas</h6>
                <table class="table table-sm">
                    <tr>
                        <td width="120"><b>Kepala</b></td>
                        <td>${res.kepala_keluarga}</td>
                    </tr>
                    <tr>
                        <td><b>NIK</b></td>
                        <td>
                            ${res.nik}
                            <button class="btn btn-xs btn-light btn-copy" data-nik="${res.nik}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td><b>No KK</b></td>
                        <td>
                            ${res.no_kk}
                            <button class="btn btn-xs btn-light btn-copy" data-kk="${res.no_kk}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        `;

                // ======================
                // STATUS
                // ======================
                html += `
            <h6>Status</h6>
            <p>
                <span class="badge ${res.status === 'miskin' ? 'bg-danger' : 'bg-success'}">
                    ${res.status === 'miskin' ? 'Miskin' : 'Tidak Miskin'}
                </span>
            </p>
        `;

                // ======================
                // URUTAN KATEGORI 🔥 FIX
                // ======================
                const urutanKategori = [
                    'Aset',
                    'Rumah',
                    'Keluarga',
                    'Pekerjaan',
                    'Ekonomi',
                    'Kesehatan'
                ];

                if (res.alasan && Object.keys(res.alasan).length > 0) {

                    urutanKategori.forEach(function(kategori) {

                        if (res.alasan[kategori]) {

                            html += `<h6 class="mt-3">${kategori}</h6><ul class="mb-2">`;

                            res.alasan[kategori].forEach(function(item) {
                                html += `<li>${item}</li>`;
                            });

                            html += '</ul>';

                        }

                    });

                } else {
                    html += `<p class="text-muted">Tidak ada data alasan</p>`;
                }

                // ======================
                // CATATAN
                // ======================
                if (res.catatan) {
                    html += `
                <hr>
                <h6>Catatan Petugas</h6>
                <p>${res.catatan}</p>
            `;
                }

                $('#detail-kemiskinan').html(html);

            })
            .fail(function() {

                $('#detail-kemiskinan').html(`
            <div class="text-center text-danger py-4">
                Gagal memuat data
            </div>
        `);

            });

    });

    $(document).on('click', '#btnValidasiDetail', function() {

        if (!currentVerifikasiId) return;

        Swal.fire({
            title: 'Validasi data ini?',
            icon: 'question',
            showCancelButton: true
        }).then((result) => {

            if (result.isConfirmed) {

                $.post('/dtsen/kemiskinan/validasi', {
                        id: currentVerifikasiId
                    },
                    function(res) {

                        if (res.success) {

                            $('#modalDetail').modal('hide');

                            location.reload();

                        }

                    });

            }

        });

    });

    $(document).on('click', '#btnTolakDetail', function() {

        if (!currentVerifikasiId) return;

        Swal.fire({
            title: 'Tolak data ini?',
            icon: 'warning',
            showCancelButton: true
        }).then((result) => {

            if (result.isConfirmed) {

                $.post('/dtsen/kemiskinan/tolak', {
                        id: currentVerifikasiId
                    },
                    function(res) {

                        if (res.success) {

                            $('#modalDetail').modal('hide');

                            location.reload();

                        }

                    });

            }

        });

    });

    $(document).on('click', '.btn-rollback', function() {

        let id = $(this).data('id');

        Swal.fire({
            title: 'Kembalikan Data?',
            text: 'Data akan dikembalikan ke Penentuan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kembalikan'
        }).then((result) => {

            if (result.isConfirmed) {

                $.post('/dtsen/kemiskinan/rollback', {
                    id: id
                }, function(res) {

                    if (res.success) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Data dikembalikan',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        // 🔥 CLOSE MODAL
                        $('#modalDetail').modal('hide');

                        // 🔥 RELOAD DATATABLE
                        $('#tableVerifikasi').DataTable().ajax.reload(null, false);

                    }

                });

            }

        });

    });

    $(document).on('click', '#btnRollbackDetail', function() {

        let id = $(this).data('id');

        Swal.fire({
            title: 'Kembalikan Data?',
            icon: 'warning',
            showCancelButton: true
        }).then((result) => {

            if (result.isConfirmed) {

                $.post('/dtsen/kemiskinan/rollback', {
                    id: id
                }, function(res) {

                    if (res.success) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Data dikembalikan',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        $('#modalDetail').modal('hide');
                        $('#tableVerifikasi').DataTable().ajax.reload(null, false);

                    }

                });

            }

        });

    });

    $(document).on('click', '.btn-copy', function() {

        let kk = $(this).data('kk');

        navigator.clipboard.writeText(kk);

        Swal.fire({
            icon: 'success',
            title: 'No KK disalin',
            timer: 800,
            showConfirmButton: false
        });

    });

    $(document).on('click', '.btn-copy[data-nik]', function() {

        let nik = $(this).data('nik');

        navigator.clipboard.writeText(nik);

        Swal.fire({
            icon: 'success',
            title: 'NIK disalin',
            timer: 800,
            showConfirmButton: false
        });

    });
</script>

<?= $this->endSection() ?>