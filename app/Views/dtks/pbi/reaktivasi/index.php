<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .summary-card {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .summary-card:hover {
        transform: translateY(-3px);
    }

    .summary-card.active {
        border-width: 2px;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, .25);
    }
</style>

<div class="content-wrapper mt-1">
    <section class="content pt-2">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title mb-0">Reaktivasi PBI</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3" id="summaryCards">
                        <div class="col-md col-4 mb-2">
                            <div class="card border-secondary shadow-sm summary-card" data-status="draft">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Draft</h6>
                                    <h4 class="counter" id="sumDraft">0</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md col-4 mb-2">
                            <div class="card border-warning shadow-sm summary-card" data-status="diajukan">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Diajukan</h6>
                                    <h4 class="counter" id="sumDiajukan">0</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md col-4 mb-2">
                            <div class="card border-info shadow-sm summary-card" data-status="diverifikasi">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Diverifikasi</h6>
                                    <h4 class="counter" id="sumVerifikasi">0</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md col-4 mb-2">
                            <div class="card border-success shadow-sm summary-card" data-status="disetujui">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Disetujui</h6>
                                    <h4 class="counter" id="sumSetujui">0</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md col-4 mb-2">
                            <div class="card border-danger shadow-sm summary-card" data-status="ditolak">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Ditolak</h6>
                                    <h4 class="counter" id="sumTolak">0</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md col-4 mb-2">
                            <div class="card border-primary shadow-sm summary-card" data-status="diajukan_siks">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Diajukan SIKS</h6>
                                    <h4 class="counter" id="sumSiks">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div class="row g-2 align-items-end mb-3">

                            <div class="col-md-2">
                                <label class="form-label small mb-1">Status</label>
                                <select id="filterStatus" class="form-control form-control-sm">
                                    <option value="">Semua Status</option>
                                    <option value="0">Draft</option>
                                    <option value="1">Diajukan</option>
                                    <option value="2">Diverifikasi</option>
                                    <option value="3">Disetujui</option>
                                    <option value="4">Ditolak</option>
                                    <option value="5">Diajukan SIKS</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small mb-1">RW</label>
                                <select id="filterRw" class="form-control form-control-sm"></select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small mb-1">RT</label>
                                <select id="filterRt" class="form-control form-control-sm"></select>
                            </div>

                            <div class="col-md-2">
                                <button id="btnResetFilter" class="btn btn-sm btn-outline-secondary w-100">
                                    Reset Filter
                                </button>
                            </div>

                            <!-- Upload Excel -->
                            <?php if (session('role_id') < 4) : ?>
                                <div class="col-md-4 col-12">
                                    <label class="form-label small mb-1">Upload Data Verivali</label>
                                    <form id="formUploadExcel" enctype="multipart/form-data" class="d-flex gap-2">
                                        <input type="file"
                                            name="file_excel"
                                            accept=".xls,.xlsx"
                                            class="form-control form-control-sm"
                                            required>

                                        <button type="submit"
                                            class="btn btn-primary btn-sm">
                                            Upload
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                        <table id="tabelReaktivasi" class="table table-sm table-hover table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>No KK</th>
                                    <th>Desil</th>
                                    <th>Status</th>
                                    <th>RW</th>
                                    <th>RT</th>
                                    <th>ALAMAT</th>
                                    <th>Reaktivasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="modalAjukan">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formAjukan" enctype="multipart/form-data">

                <div class="modal-header">
                    <h5 class="modal-title">Ajukan Reaktivasi</h5>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="nik" id="nik">

                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" id="nama" class="form-control" readonly>
                    </div>

                    <div class="mb-2">
                        <label>Alasan</label>
                        <textarea name="alasan" class="form-control" required></textarea>
                    </div>

                    <div class="mb-2">
                        <label>Upload Surat Faskes</label>
                        <input type="file" name="surat_faskes" id="fileInput" class="form-control" required>
                    </div>

                    <div id="filePreview" class="mt-2"></div>

                    <div class="progress mt-3 d-none" id="uploadProgressWrapper">
                        <div class="progress-bar" id="uploadProgress" role="progressbar" style="width:0%">0%</div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        Simpan & Ajukan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalVerifikasi" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shield-check text-success me-2"></i>
                    Verifikasi Reaktivasi PBI
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="verifikasiContent"></div>
            </div>

            <div class="modal-footer justify-content-between">

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>

                    <div id="verifikasiActionButtons"></div>

                    <button id="btnKirimSiks" class="btn btn-primary d-none">
                        <i class="bi bi-send"></i> Kirim ke SIKS
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const ROLE_ID = <?= (int) session('role_id') ?>;

    let table = null;
    let compressedFile = null;

    let filters = {
        rw: null,
        rt: null,
        kemensos_status: null, // dari dropdown
        workflow_status: null // dari summaryCards
    };

    let currentData = null;

    /* =========================================================
       FIELD ITEM TEMPLATE
    ========================================================= */
    function fieldItem(label, value, id) {
        return `
        <div class="col-md-6 mb-3">
            <label class="form-label text-muted small mb-1">${label}</label>
            <div class="input-group input-group-sm">
                <input type="text" 
                       class="form-control" 
                       value="${value ?? '-'}" 
                       readonly 
                       id="${id}">
                <button class="btn btn-outline-secondary btn-copy"
                        data-copy="${id}">
                        <i class="bi bi-clipboard"></i>
                </button>
            </div>
        </div>
    `;
    }

    /* =========================================================
       DROPDOWN STATUS
    ========================================================= */
    loadStatusDropdown();

    function loadStatusDropdown() {

        $.get('/pbi/reaktivasi/dropdown-status', function(res) {

            const select = $('#filterStatus');

            select.html('<option value="">Semua Status</option>');

            res.forEach(function(status) {
                select.append(`<option value="${status}">${status}</option>`);
            });

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
                    loadSummary();
                    return;
                }

                res[selectedRw].forEach(function(rt) {
                    rtSelect.append(`<option value="${rt}">RT ${rt}</option>`);
                });

                table.ajax.reload();
                loadSummary();
            });

            rtSelect.off('change').on('change', function() {
                filters.rt = $(this).val() || null;
                table.ajax.reload();
                loadSummary();
            });

        });
    }

    /* =========================================================
       STATUS BADGE
    ========================================================= */
    function getStatusBadge(status) {

        const map = {
            0: 'secondary',
            1: 'warning',
            2: 'info',
            3: 'success',
            4: 'danger',
            5: 'primary'
        };

        const label = {
            0: 'Draft',
            1: 'Diajukan',
            2: 'Diverifikasi',
            3: 'Disetujui',
            4: 'Ditolak',
            5: 'Diajukan SIKS'
        };

        return `<span class="badge bg-${map[status] || 'dark'}">
                ${label[status] || 'Unknown'}
            </span>`;
    }

    /* =========================================================
         RENDER ACTION BUTTONS
    ========================================================= */
    function renderActionButtons(data) {

        let html = '';

        // STATUS 1 → DIAJUKAN
        if (data.status_pengajuan == 1) {
            html += `
            <button id="btnVerifikasi" class="btn btn-primary">
                <i class="bi bi-check2-square me-1"></i> Verifikasi
            </button>
        `;
        }

        // STATUS 2 → DIVERIFIKASI
        if (data.status_pengajuan == 2) {
            html += `
            <button id="btnSetujui" class="btn btn-success me-2">
                <i class="bi bi-send-check me-1"></i> Setujui
            </button>
            <button id="btnTolak" class="btn btn-danger">
                <i class="bi bi-x-circle me-1"></i> Tolak
            </button>
        `;
        }

        return html;
    }

    /* =========================================================
       INIT DOCUMENT
    ========================================================= */
    $(document).ready(function() {

        loadRwRtDropdown();

        table = $('#tabelReaktivasi').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            searching: true,
            order: [
                [0, 'asc']
            ],
            ajax: {
                url: '/pbi/reaktivasi/tabel',
                type: 'POST',
                data: function(d) {
                    d.rw = filters.rw;
                    d.rt = filters.rt;
                    d.kemensos_status = filters.kemensos_status;
                    d.workflow_status = filters.workflow_status;
                }
            },
            columns: [{
                    data: 'nama'
                },
                {
                    data: 'nik'
                },
                {
                    data: 'no_kk'
                },
                {
                    data: 'desil_nasional'
                },
                {
                    data: 'status'
                },
                {
                    data: 'rw'
                },
                {
                    data: 'rt'
                },
                {
                    data: 'alamat'
                },
                {
                    data: 'status_pengajuan',
                    render: function(data) {
                        if (data === null) {
                            return getStatusBadge(0);
                        }
                        return getStatusBadge(parseInt(data));
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {

                        // Belum pernah diajukan
                        if (row.reaktivasi_id === null) {
                            return `
                                <button class="btn btn-sm btn-primary btn-ajukan"
                                    data-nik="${row.nik}"
                                    data-nama="${row.nama}"
                                    data-rw="${row.rw}"
                                    data-rt="${row.rt}">
                                    Ajukan
                                </button>
                            `;
                        }

                        // Sudah ada reaktivasi → tampil tombol detail
                        return `
                            <button class="btn btn-sm btn-info btn-verifikasi"
                                data-id="${row.reaktivasi_id}">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                        `;
                    }
                }
            ]
        });

        /* ==========================
           FILTER STATUS
        =========================== */

        $('#filterStatus').on('change', function() {
            filters.kemensos_status = $(this).val() || null;
            table.ajax.reload(null, false);
        });

        $('#btnResetFilter').on('click', function() {
            filters = {
                rw: null,
                rt: null,
                status: null
            };
            $('#filterStatus, #filterRw, #filterRt').val('');
            $('.summary-card').removeClass('active');
            table.ajax.reload();
            loadSummary();
        });

        /* ==========================
           AJUKAN
        =========================== */

        $(document).on('click', '.btn-ajukan', function() {
            $('#nik').val($(this).data('nik'));
            $('#nama').val($(this).data('nama'));
            $('#modalAjukan').modal('show');
        });

        /* ==========================
           VERIFIKASI
        =========================== */
        $(document).on('click', '.btn-verifikasi', function() {

            const id = $(this).data('id');

            $.get('/pbi/reaktivasi/detail/' + id, function(res) {

                currentData = res;

                let html = `
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        Informasi Petugas Desa
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label text-muted small mb-1">Nama Petugas</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control"
                                        value="${res.nama_petugas_login ?? '-'}"
                                        readonly id="copy_petugas">
                                    <button class="btn btn-outline-secondary btn-copy"
                                            data-copy="copy_petugas">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted small mb-1">Kecamatan Bertugas</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control"
                                        value="${res.nama_kecamatan_login ?? '-'}"
                                        readonly id="copy_kec">
                                    <button class="btn btn-outline-secondary btn-copy"
                                            data-copy="copy_kec">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted small mb-1">Desa Bertugas</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control"
                                        value="${res.nama_desa_login ?? '-'}"
                                        readonly id="copy_desa">
                                    <button class="btn btn-outline-secondary btn-copy"
                                            data-copy="copy_desa">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        Identitas KPM Calon Reaktivasi
                    </div>
                    <div class="card-body row nopadding">
                        ${fieldItem('Nama KPM', res.nama.toUpperCase(), 'copy_nama')}
                        ${fieldItem('NIK', res.nik, 'copy_nik')}
                        ${fieldItem('Nomor Kartu JKN', res.noka_jkn, 'copy_noka')}
                        ${fieldItem('Alamat', res.alamat.toUpperCase() ?? '-', 'copy_alamat')}
                        ${fieldItem('RT', res.rt, 'copy_rt')}
                        ${fieldItem('RW', res.rw, 'copy_rw')}
                        ${fieldItem('Desa/Kelurahan', res.nama_desa.toUpperCase(), 'copy_desakpm')}
                        ${fieldItem('Kecamatan', res.nama_kecamatan.toUpperCase(), 'copy_keckpm')}
                        
                        <div class="col-12">
                            <label class="form-label text-muted small mb-1">Alasan</label>
                            <textarea class="form-control form-control-sm" 
                                    rows="2" 
                                    readonly 
                                    id="copy_alasan">${res.alasan ?? '-'}</textarea>
                            <button class="btn btn-outline-secondary btn-sm mt-1 btn-copy"
                                    data-copy="copy_alasan">
                                <i class="bi bi-clipboard"></i> Salin
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        Surat Rekomendasi Faskes
                    </div>
                    <div class="card-body">
                        <iframe src="/uploads/pbi/${res.surat_faskes}" 
                                style="width:100%;height:400px;border-radius:6px;"></iframe>

                        <div class="mt-3">
                            <a href="/uploads/pbi/${res.surat_faskes}" 
                            target="_blank" 
                            class="btn btn-primary btn-sm">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
                `;

                $('#verifikasiContent').html(html);
                // 🔥 INI BAGIAN PENTING
                $('#verifikasiActionButtons').html(renderActionButtons(res));
                $('#modalVerifikasi').modal('show');

                const status = res.status_pengajuan;

                $('#btnSetujui').addClass('d-none');
                $('#btnTolak').addClass('d-none');
                $('#btnKirimSiks').addClass('d-none');

                if (status == 2) {
                    // Diverifikasi → bisa setujui/tolak
                    $('#btnSetujui').removeClass('d-none');
                    $('#btnTolak').removeClass('d-none');
                }

                if (status == 3) {
                    // Sudah disetujui → boleh kirim ke SIKS
                    $('#btnKirimSiks').removeClass('d-none');
                }

                if (status == 5) {
                    // Sudah dikirim ke SIKS
                    $('#btnKirimSiks')
                        .removeClass('btn-primary')
                        .addClass('btn-success')
                        .prop('disabled', true)
                        .text('Sudah Dikirim ke SIKS');
                }
            });
        });


        /* ==========================
           SETUJUI
        =========================== */
        $(document).on('click', '#btnSetujui', function() {

            if (!currentData) return;

            Swal.fire({
                title: 'Setujui Pengajuan?',
                text: 'Data akan ditandai Disetujui & siap kirim ke SIKS',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.post('/pbi/reaktivasi/setujui/' + currentData.id, function(res) {

                    if (res.success) {

                        Swal.fire('Berhasil', 'Data Disetujui', 'success');

                        $('#modalVerifikasi').modal('hide');
                        table.ajax.reload(null, false);
                        loadSummary();

                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }

                });

            });

        });

        /* ==========================
           TOLAK
        =========================== */
        $(document).on('click', '#btnTolak', function() {

            if (!currentData) return;

            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: 'Status akan berubah menjadi Ditolak',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.post('/pbi/reaktivasi/tolak/' + currentData.id, function(res) {

                    if (res.success) {

                        Swal.fire('Berhasil', 'Data Ditolak', 'success');

                        $('#modalVerifikasi').modal('hide');
                        table.ajax.reload(null, false);
                        loadSummary();

                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }

                });

            });

        });

        /* ==========================
           GOOGLE FORM
        =========================== */
        $(document).on('click', '#btnGoogleForm', function() {

            if (!currentData) {
                Swal.fire('Error', 'Data belum dimuat', 'error');
                return;
            }

            const formId = '1FAIpQLSd3xyxi2ekvZ4tBhrx5Lm1Kor7uPnSuZGFV7OULlul1XtKmGQ';

            const baseUrl = `https://docs.google.com/forms/d/e/${formId}/viewform?usp=pp_url`;

            const params = new URLSearchParams({
                'entry.1356288758': currentData.nama_petugas_login.trim(),
                'entry.1136367332': currentData.nama_kecamatan_login.trim(),
                'entry.1395221663': currentData.nama_desa_login.trim(),
                'entry.27482709': currentData.nama.toUpperCase().trim(),
                'entry.185500325': currentData.nik.trim(),
                'entry.1646181118': currentData.noka_jkn.trim(),
                'entry.277064263': currentData.alamat.toUpperCase().trim(),
                'entry.1011754031': currentData.rt.trim(),
                'entry.540247442': currentData.rw.trim(),
                'entry.312532285': currentData.nama_desa.trim(),
                'entry.669273881': currentData.nama_kecamatan.trim()
            });

            window.open(baseUrl + '&' + params.toString(), '_blank');
        });

        /* ==========================
              VERIFIKASI
        =========================== */
        $(document).on('click', '#btnVerifikasi', function() {

            Swal.fire({
                title: 'Verifikasi Data?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Verifikasi'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.post('/pbi/reaktivasi/verifikasi/' + currentData.id, function(res) {

                    if (res.success) {
                        Swal.fire('Berhasil', 'Status menjadi Diverifikasi', 'success');
                        $('#modalVerifikasi').modal('hide');
                        table.ajax.reload(null, false);
                        loadSummary();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }

                });

            });

        });

        /* ==========================
           SUMMARY
        =========================== */

        function loadSummary() {

            $.ajax({
                url: '/pbi/reaktivasi/summary',
                type: 'GET',
                data: filters,
                success: function(res) {

                    animateCounter($('#sumDraft'), res.draft || 0);
                    animateCounter($('#sumDiajukan'), res.diajukan || 0);
                    animateCounter($('#sumVerifikasi'), res.diverifikasi || 0);
                    animateCounter($('#sumSetujui'), res.disetujui || 0);
                    animateCounter($('#sumTolak'), res.ditolak || 0);
                    animateCounter($('#sumSiks'), res.diajukan_siks || 0);

                }
            });
        }

        /* =========================================
           SUMMARY CARDS CLICK HANDLER
        ========================================= */

        $('#summaryCards').on('click', '.summary-card', function() {

            const selected = $(this).data('status');
            // draft | diajukan | diverifikasi | dst

            if (filters.workflow_status === selected) {
                filters.workflow_status = null;
                $('.summary-card').removeClass('active');
            } else {
                filters.workflow_status = selected;
                $('.summary-card').removeClass('active');
                $(this).addClass('active');
            }

            table.ajax.reload(null, false);
        });

        table.on('draw', loadSummary);
        loadSummary();

        /* =========================================================
        COPY TO CLIPBOARD
        ========================================================= */
        $(document).on('click', '.btn-copy', function() {

            const targetId = $(this).data('copy');
            const input = document.getElementById(targetId);

            if (!input) return;

            input.select();
            input.setSelectionRange(0, 99999);

            navigator.clipboard.writeText(input.value);

            Swal.fire({
                icon: 'success',
                title: 'Disalin',
                text: 'Data berhasil disalin ke clipboard',
                timer: 1200,
                showConfirmButton: false
            });
        });


        /* =========================================================
           FORM AJUKAN - SUBMIT HANDLER
        ========================================================= */
        let isSubmitting = false;
        $('#formAjukan').submit(function(e) {
            e.preventDefault();
            if (isSubmitting) return;
            isSubmitting = true;
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Uploading...');
            const formData = new FormData(this);
            if (compressedFile) {
                formData.set('surat_faskes', compressedFile);
            }
            // 🔥 SHOW PROGRESS STATE DI SINI 
            Swal.fire({
                title: 'Mengunggah & Mengompresi...',
                html: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $('#uploadProgressWrapper').removeClass('d-none');
            $.ajax({
                url: '/pbi/reaktivasi/ajukan',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    let xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            let percent = Math.round((evt.loaded / evt.total) * 100);
                            $('#uploadProgress').css('width', percent + '%').text(percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(res) {
                    isSubmitting = false;
                    btn.prop('disabled', false).text('Simpan & Ajukan');
                    if (res.success) {
                        $('#modalAjukan').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire('Berhasil', 'Pengajuan dikirim', 'success');
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                    $('#uploadProgressWrapper').addClass('d-none');
                    $('#uploadProgress').css('width', '0%').text('0%');
                },
                error: function() {
                    isSubmitting = false;
                    btn.prop('disabled', false).text('Simpan & Ajukan');
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        });

        /* =========================================================
              KIRIM SIKS
        ========================================================= */
        $('#btnKirimSiks').on('click', function() {

            Swal.fire({
                title: 'Kirim ke SIKS?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $.post('/pbi/reaktivasi/kirim-siks/' + currentData.id, function(res) {

                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#modalVerifikasi').modal('hide');
                        $('#tabelReaktivasi').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }

                });

            });
        });

        /* =========================================================
           UPLOAD EXCEL
        ========================================================= */
        $('#formUploadExcel').submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: '/pbi/reaktivasi/upload-excel',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message + ' (' + res.total + ' data)', 'success');
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }
            });
        });
    });

    /* =========================================================
       COUNTER ANIMATION
    ========================================================= */

    function animateCounter(element, target) {

        const duration = 600;
        const startTime = performance.now();

        function update(currentTime) {

            const progress = Math.min((currentTime - startTime) / duration, 1);
            const value = Math.floor(progress * target);

            element.text(value);

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.text(target);
            }
        }

        requestAnimationFrame(update);
    }
</script>

<?= $this->endSection(); ?>