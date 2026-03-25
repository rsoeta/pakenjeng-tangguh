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

                            <tbody>

                                <?php $no = 1;
                                foreach ($verifikasi as $row): ?>

                                    <tr data-id="<?= $row['id'] ?>">

                                        <td><?= $no++ ?></td>

                                        <td><?= esc($row['kepala_keluarga']) ?></td>
                                        <td>

                                            <span class="no-nik"><?= esc($row['nik']) ?></span>

                                            <button
                                                class="btn btn-xs btn-light btn-copy"
                                                data-nik="<?= $row['nik'] ?>"
                                                title="Salin">

                                                <i class="fas fa-copy"></i>

                                            </button>

                                        </td>
                                        <td>

                                            <span class="no-kk"><?= esc($row['no_kk']) ?></span>

                                            <button
                                                class="btn btn-xs btn-light btn-copy"
                                                data-kk="<?= $row['no_kk'] ?>"
                                                title="Salin">

                                                <i class="fas fa-copy"></i>

                                            </button>

                                        </td>
                                        <td><?= esc($row['rw']) ?></td>
                                        <td><?= esc($row['rt']) ?></td>
                                        <td>
                                            <?php if ($row['kategori_desil']): ?>
                                                <span class="badge bg-info">
                                                    Desil <?= $row['kategori_desil'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">-</span>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <?php if ($row['status_kemiskinan'] == 'miskin'): ?>
                                                <span class="badge bg-danger">Miskin</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Tidak Miskin</span>
                                            <?php endif ?>
                                        </td>
                                        <td><?= esc($row['petugas_entri']) ?></td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-info btn-detail"
                                                data-id="<?= $row['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                                Detail
                                            </button>
                                        </td>

                                    </tr>

                                <?php endforeach ?>

                            </tbody>

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
            pageLength: 25,
            order: [
                [0, 'asc']
            ],
            columnDefs: [{
                orderable: false,
                targets: 7
            }],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data"
            }
        });
    });

    let currentVerifikasiId = null;

    $(document).on('click', '.btn-detail', function() {

        let id = $(this).data('id');

        currentVerifikasiId = id;

        $('#detail-kemiskinan').html('<div class="text-center">Loading...</div>');

        $('#modalDetail').modal('show');

        $.get('/dtsen/kemiskinan/detail', {
            id: id
        }, function(res) {

            let html = '';

            html += `
            <h6>Status</h6>
            <p>
            <span class="badge ${res.status=='miskin'?'bg-danger':'bg-success'}">
            ${res.status=='miskin'?'Miskin':'Tidak Miskin'}
            </span>
            </p>
            `;

            Object.keys(res.alasan).forEach(function(kategori) {

                html += `<h6 class="mt-3">${kategori}</h6><ul>`;

                res.alasan[kategori].forEach(function(item) {
                    html += `<li>${item}</li>`;
                });

                html += '</ul>';

            });

            if (res.catatan) {

                html += `
                <hr>
                <h6>Catatan Petugas</h6>
                <p>${res.catatan}</p>
                `;

            }

            $('#detail-kemiskinan').html(html);

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