<?= $this->extend('template/index') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header bg-white">

            <h5 class="mb-0">
                Penentuan Kemiskinan
            </h5>

        </div>

        <div class="card-body">

            <!-- FILTER -->

            <div class="row mb-3">

                <div class="col-md-2">
                    <select id="filter_rw" class="form-control">
                        <option value="all">Semua RW</option>
                        <?php for ($i = 1; $i <= 20; $i++): ?>
                            <option value="<?= $i ?>">RW <?= $i ?></option>
                        <?php endfor ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <select id="filter_rt" class="form-control">
                        <option value="all">Semua RT</option>
                        <?php for ($i = 1; $i <= 20; $i++): ?>
                            <option value="<?= $i ?>">RT <?= $i ?></option>
                        <?php endfor ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <select id="filter_desil" class="form-control">
                        <option value="all">Semua Desil</option>
                        <option value="1">Desil 1</option>
                        <option value="2">Desil 2</option>
                        <option value="3">Desil 3</option>
                        <option value="4">Desil 4</option>
                        <option value="5">Desil 5</option>
                        <option value="none">Tanpa Desil</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button id="btnFilter" class="btn btn-primary">
                        Filter
                    </button>
                </div>

            </div>

            <!-- TABLE -->

            <div class="table-responsive">

                <table id="tableKK" class="table table-bordered table-striped table-hover">

                    <thead class="table-light">

                        <tr>

                            <th width="40">No</th>
                            <th>No KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Alamat</th>
                            <th width="60">RW</th>
                            <th width="60">RT</th>
                            <th width="80">Desil</th>
                            <th width="180">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php $no = 1;
                        foreach ($keluarga as $row): ?>

                            <tr>

                                <td><?= $no++ ?></td>

                                <td><?= esc($row['no_kk']) ?></td>

                                <td><?= esc($row['kepala_keluarga']) ?></td>

                                <td><?= esc($row['alamat']) ?></td>

                                <td><?= esc($row['rw']) ?></td>

                                <td><?= esc($row['rt']) ?></td>

                                <td>

                                    <?php if ($row['kategori_desil']): ?>

                                        <span class="badge bg-info">
                                            Desil <?= $row['kategori_desil'] ?>
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">
                                            -
                                        </span>

                                    <?php endif ?>

                                </td>

                                <td>

                                    <button
                                        class="btn btn-sm btn-danger btn-miskin"
                                        data-id="<?= $row['id_kk'] ?>">
                                        Miskin
                                    </button>

                                    <button
                                        class="btn btn-sm btn-success btn-tidak-miskin"
                                        data-id="<?= $row['id_kk'] ?>">
                                        Tidak Miskin
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

<!-- MODAL -->

<div class="modal fade" id="modalKemiskinan">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <form id="formKemiskinan">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Penentuan Status Kemiskinan
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <input type="hidden" id="kk_id" name="kk_id">

                    <input type="hidden" id="status" name="status">

                    <div id="container-alasan"></div>

                    <hr>

                    <div class="mb-3">

                        <label class="form-label">
                            Catatan Petugas
                        </label>

                        <textarea
                            name="catatan"
                            id="catatan"
                            class="form-control"
                            rows="3"></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Tutup
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?= $this->endSection() ?>

<script>
    function loadAlasan(status) {
        $.get('/dtsen/kemiskinan/alasan', {
            status: status
        }, function(res) {

            let html = '';

            Object.keys(res).forEach(function(kategori) {

                html += `
            <div class="mb-3">
            <h6 class="fw-bold text-primary">${kategori}</h6>
            `;

                res[kategori].forEach(function(row) {

                    html += `
                <div class="form-check">
                    <input class="form-check-input alasan"
                           type="checkbox"
                           value="${row.id}">
                    <label class="form-check-label">
                        ${row.label}
                    </label>
                </div>
                `;

                });

                html += '</div>';

            });

            $('#container-alasan').html(html);

        });
    }

    $('.btn-miskin').click(function() {

        let kk = $(this).data('id');

        $('#kk_id').val(kk);
        $('#status').val('miskin');

        loadAlasan('miskin');

        $('#modalKemiskinan').modal('show');

    });

    $('.btn-tidak-miskin').click(function() {

        let kk = $(this).data('id');

        $('#kk_id').val(kk);
        $('#status').val('tidak_miskin');

        loadAlasan('tidak_miskin');

        $('#modalKemiskinan').modal('show');

    });

    $('#formKemiskinan').submit(function(e) {

        e.preventDefault();

        let alasan = [];

        $('.alasan:checked').each(function() {

            alasan.push($(this).val());

        });

        if (alasan.length === 0) {

            Swal.fire({
                icon: 'warning',
                title: 'Pilih minimal satu alasan'
            });

            return;

        }

        $.post('/dtsen/kemiskinan/simpan', {

            kk_id: $('#kk_id').val(),
            status: $('#status').val(),
            alasan: alasan,
            catatan: $('#catatan').val()

        }, function(res) {

            if (res.success) {

                Swal.fire({
                    icon: 'success',
                    title: 'Data berhasil disimpan'
                }).then(() => {
                    location.reload();
                });

            }

        });

    });
</script>