<?= $this->extend('templates/index') ?>
<?= $this->section('content') ?>

<style>
    .form-check-label {
        cursor: pointer;
    }

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
                            <select id="filterRw" class="form-control form-control-sm"></select>
                        </div>

                        <div class="col-md-2">
                            <select id="filterRt" class="form-control form-control-sm"></select>
                        </div>

                        <div class="col-md-2">
                            <select id="filter_desil" class="form-control form-control-sm">
                                <option value="all">Semua Desil</option>
                                <option value="none">Tanpa Desil</option>
                                <option value="1">Desil 1</option>
                                <option value="2">Desil 2</option>
                                <option value="3">Desil 3</option>
                                <option value="4">Desil 4</option>
                                <option value="5">Desil 5</option>
                                <option value="6">Desil 6</option>
                                <option value="7">Desil 7</option>
                                <option value="8">Desil 8</option>
                                <option value="9">Desil 9</option>
                                <option value="10">Desil 10</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <button id="resetFilter" class="btn btn-light btn-sm">
                                Reset
                            </button>
                        </div>


                    </div>

                    <div class="table-responsive">
                        <table id="tableKK" class="table table-bordered table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Kepala Keluarga</th>
                                    <th>NIK</th>
                                    <th>No KK</th>
                                    <th>Alamat</th>
                                    <th width="60">RW</th>
                                    <th width="60">RT</th>
                                    <th width="80">Desil</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
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
                        type="submit"
                        class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let table;

    let filters = {
        rw: null,
        rt: null,
        desil: null
    };

    $(document).ready(function() {

        loadRwRtDropdown();

        table = $('#tableKK').DataTable({

            processing: true,
            serverSide: false,
            responsive: true,

            ajax: {
                url: '/dtsen/kemiskinan/datatable',
                dataSrc: 'data',
                data: function(d) {
                    d.rw = filters.rw;
                    d.rt = filters.rt;
                    d.desil = filters.desil;
                }
            },

            columns: [{
                    data: null
                }, // No
                {
                    data: 'kepala_keluarga'
                }, // Kepala Keluarga
                {
                    data: 'nik'
                }, // NIK
                {
                    data: 'no_kk'
                }, // No KK
                {
                    data: 'alamat'
                }, // Alamat
                {
                    data: 'rw'
                }, // RW
                {
                    data: 'rt'
                }, // RT
                {
                    data: 'kategori_desil'
                }, // Desil
                {
                    data: null
                } // Aksi
            ],

            columnDefs: [{
                    targets: 0,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    targets: 7,
                    render: function(data) {

                        if (!data) {
                            return '<span class="badge bg-secondary">-</span>';
                        }

                        return `<span class="badge bg-info">Desil ${data}</span>`;

                    }
                },

                {
                    targets: 8,
                    orderable: false,
                    render: function(data, type, row) {

                        return `
                <button class="btn btn-sm btn-danger btn-miskin"
                data-id="${row.id_kk}">
                <i class="fas fa-times"></i> Miskin
                </button>

                <button class="btn btn-sm btn-success btn-tidak-miskin"
                data-id="${row.id_kk}">
                <i class="fas fa-check"></i> Tidak Miskin
                </button>
                `;
                    }
                }
            ],

            pageLength: 10,

            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    next: "›",
                    previous: "‹"
                }
            }

        });
    });

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

                    let checkboxId = "alasan_" + row.id;

                    html += `
                    <div class="form-check mb-1">
                    <input class="form-check-input alasan"
                    type="checkbox"
                    value="${row.id}"
                    id="${checkboxId}">
                    <label class="form-check-label"
                    for="${checkboxId}">
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

    /* =========================================================
       DROPDOWN RW RT
    ========================================================= */
    function loadRwRtDropdown() {

        $.get('/dropdown-rwrt', function(res) {

            const rwSelect = $('#filterRw');
            const rtSelect = $('#filterRt');

            rwSelect.html('<option value="">Semua RW</option>');
            rtSelect.html('<option value="">Semua RT</option>');

            Object.keys(res).forEach(function(rw) {
                rwSelect.append(`<option value="${rw}">RW ${rw}</option>`);
            });

            rwSelect.off('change').on('change', function() {
                const selectedRw = $(this).val();
                filters.rw = selectedRw || null;
                rtSelect.html('<option value="">Semua RT</option>');
                if (!selectedRw || !res[selectedRw]) {
                    table.ajax.reload();
                    return;
                }
                res[selectedRw].forEach(function(rt) {
                    rtSelect.append(`<option value="${rt}">RT ${rt}</option>`);
                });
                table.ajax.reload();
            });

            rtSelect.off('change').on('change', function() {
                filters.rt = $(this).val() || null;
                table.ajax.reload();
            });
        });
    }

    $('#filter_desil').on('change', function() {
        filters.desil = $(this).val();
        table.ajax.reload();
    });

    $('#resetFilter').click(function() {

        filters = {
            rw: null,
            rt: null,
            desil: null
        };

        $('#filterRw').val('');
        $('#filterRt').val('');
        $('#filter_desil').val('all');

        table.ajax.reload();

    });

    let currentRow;

    $(document).on('click', '.btn-miskin', function() {
        currentRow = $(this).closest('tr');
        let kk = $(this).data('id');
        $('#kk_id').val(kk);
        $('#status').val('miskin');
        loadAlasan('miskin');
        $('#modalKemiskinan').modal('show');
    });

    $(document).on('click', '.btn-tidak-miskin', function() {
        currentRow = $(this).closest('tr');
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
                    title: 'Data berhasil disimpan',
                    timer: 1200,
                    showConfirmButton: false
                });

                setTimeout(function() {
                    location.reload();
                }, 1200);
            }
        });
    });
</script>

<?= $this->endSection() ?>