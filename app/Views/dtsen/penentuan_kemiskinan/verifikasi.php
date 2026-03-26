<?= $this->extend('templates/index') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Verifikasi Kemiskinan</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
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
    $(document).ready(function() {

        $('#tableVerifikasi').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            ajax: {
                url: '/dtsen/kemiskinan/verifikasi-data',
                dataSrc: 'data'
            },
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data"
            },
            columns: [{
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
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
    });

    let currentVerifikasiId = null;

    $(document).on('click', '.btn-detail', function() {

        let id = $(this).data('id');
        currentVerifikasiId = id;

        // 🔥 Inject ID ke tombol modal (PENTING)
        $('#btnRollbackDetail').data('id', id);
        $('#btnTolakDetail').data('id', id);
        $('#btnValidasiDetail').data('id', id);

        // 🔥 Loading state
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
                // ALASAN
                // ======================
                if (res.alasan && Object.keys(res.alasan).length > 0) {

                    Object.keys(res.alasan).forEach(function(kategori) {

                        html += `<h6 class="mt-3">${kategori}</h6><ul class="mb-2">`;

                        res.alasan[kategori].forEach(function(item) {
                            html += `<li>${item}</li>`;
                        });

                        html += '</ul>';

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

                // ======================
                // RENDER
                // ======================
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