<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

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
                            🟢 Daftar Keluarga
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tabDraft-tab" data-bs-toggle="tab" data-bs-target="#tabDraft" type="button" role="tab">
                            🟡 Draft Pembaruan
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body tab-content">
                <!-- ===================== TAB 1: DAFTAR KELUARGA ===================== -->
                <div class="tab-pane fade show active" id="tabDaftar" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <label for="filterRW" class="form-label fw-bold mb-0">Filter RW:</label>
                            <select id="filterRW" class="form-select form-select-sm d-inline-block" style="width: 120px;">
                                <option value="">[ Semua ]</option>
                                <?php foreach ($dataRW as $rw): ?>
                                    <option value="<?= $rw['rw'] ?>"><?= $rw['rw'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button id="btnTambahKeluarga" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus"></i> Tambah Keluarga Baru
                        </button>
                    </div>

                    <table id="tableKeluarga" class="table table-striped table-hover nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th></th>
                                <th>No.</th>
                                <th>No KK</th>
                                <th>Kepala Keluarga</th>
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
                    <table id="tableDraftKeluarga" class="table table-striped table-hover nowrap w-100">
                        <thead class="text-center">
                            <tr>
                                <th></th>
                                <th>No.</th>
                                <th>No KK</th>
                                <th>Kepala Keluarga</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Petugas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
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

        // ========================= 🟢 TABLE DAFTAR KELUARGA =========================
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
                    data: 'no_kk'
                },
                {
                    data: 'kepala_keluarga',
                    className: 'text-capitalize'
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
        `
                }
            ],
            responsive: true,
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            createdRow: row => $(row).find('td').css('text-align', 'left'),
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
        });

        $('#filterRW').on('change', () => tableKeluarga.ajax.reload());
        $('#btnTambahKeluarga').on('click', () => window.location.href = '/pembaruan-keluarga/tambah');

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
                    data: 'no_kk_target',
                    title: 'No KK'
                },
                {
                    data: 'nama_kepala',
                    title: 'Kepala Keluarga'
                },
                {
                    data: 'status',
                    title: 'Status',
                    render: s => `<span class="badge bg-secondary">${(s || 'draft').toUpperCase()}</span>`
                },
                {
                    data: 'created_at',
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
                    render: id => `
                    <a href="/pembaruan-keluarga/lanjutkan/${id}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Lanjutkan
                    </a>
        `
                }
            ],
            responsive: true,
            pageLength: 10,
            createdRow: row => $(row).find('td').css('text-align', 'left'),
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
        });

    });
</script>

<?= $this->endSection(); ?>