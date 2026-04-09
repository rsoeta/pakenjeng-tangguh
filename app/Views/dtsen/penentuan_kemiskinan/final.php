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
                    <div class="table-responsive">
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
                                <select id="filterStatus" class="form-control form-control-sm">
                                    <option value="">Semua Status</option>
                                    <option value="miskin">Miskin</option>
                                    <option value="tidak_miskin">Tidak Miskin</option>
                                </select>
                            </div>

                        </div>
                        <table id="tableFinal" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kepala Keluarga</th>
                                    <th>NIK</th>
                                    <th>No KK</th>
                                    <th>RW</th>
                                    <th>RT</th>
                                    <th>Status</th>
                                    <th>Tanggal Verifikasi</th>
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

<script>
    let tableFinal;
    let dropdownData = {};

    $(document).ready(function() {

        tableFinal = $('#tableFinal').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            ajax: {
                url: '/dtsen/kemiskinan/final-data',
                data: function(d) {
                    d.rw = $('#filterRw').val();
                    d.rt = $('#filterRt').val();
                    d.status = $('#filterStatus').val();
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
                    data: 'nik',
                    className: 'text-nowrap text-start',
                    render: function(nik) {
                        if (!nik) return '-';
                        return `
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold">${nik}</span>
                        <button 
                            type="button"
                            class="btn btn-outline-secondary btn-xs btnCopyNIK"
                            data-value="${nik}"
                            title="Salin NIK">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `;
                    }
                },
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
                {
                    data: 'rw'
                },
                {
                    data: 'rt'
                },
                {
                    data: 'status_kemiskinan',
                    render: d => d === 'miskin' ?
                        `<span class="badge bg-danger">Miskin</span>` : `<span class="badge bg-success">Tidak Miskin</span>`
                },
                {
                    data: 'verified_at'
                }
            ]
        });

        loadRwRtDropdownFinal();

        $('#filterStatus').on('change', function() {
            tableFinal.ajax.reload(null, false);
        });

    });

    function loadRwRtDropdownFinal() {

        $.get('/dropdown-rwrt', function(res) {

            dropdownData = res;

            const rwSelect = $('#filterRw');
            const rtSelect = $('#filterRt');

            rwSelect.html('<option value="">Semua RW</option>');
            rtSelect.html('<option value="">Semua RT</option>');

            Object.keys(res).forEach(function(rw) {
                rwSelect.append(`<option value="${rw}">RW ${rw}</option>`);
            });

            rwSelect.on('change', function() {

                const rw = $(this).val();

                rtSelect.html('<option value="">Semua RT</option>');

                if (rw && dropdownData[rw]) {
                    dropdownData[rw].forEach(rt => {
                        rtSelect.append(`<option value="${rt}">RT ${rt}</option>`);
                    });
                }

                rtSelect.val('');
                tableFinal.ajax.reload(null, false);
            });

            rtSelect.on('change', function() {
                tableFinal.ajax.reload(null, false);
            });

        });
    }

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

    // ========================= 📋 COPY NIK =========================
    $(document).on('click', '.btnCopyNIK', function() {
        const value = $(this).data('value');

        if (!value) return;

        navigator.clipboard.writeText(value).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Tersalin',
                text: 'NIK berhasil disalin ke clipboard',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top'
            });
        }).catch(() => {
            Swal.fire('Gagal', 'Tidak dapat menyalin NIK', 'error');
        });
    });
</script>
<?= $this->endSection() ?>