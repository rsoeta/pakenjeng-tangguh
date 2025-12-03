<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .fab-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        font-size: 24px;
        cursor: pointer;
        z-index: 999;
        transition: all 0.25s ease;
    }

    .fab-btn:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }
</style>

<div class="content-wrapper mt-1">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-hand-holding-heart"></i> <?= esc($title); ?></h5>
            <ol class="breadcrumb float-right mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/pages'); ?>">Home</a></li>
                <li class="breadcrumb-item active"><?= esc($title); ?></li>
            </ol>
        </div>
    </section>

    <section class="content">
        <div class="card mt-2">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Usulan Bantuan Sosial</h5>
                <button id="btnTambahUsulan" class="fab-btn" data-toggle="tooltip" title="Tambah Usulan Bansos">
                    <i class="fas fa-hand-holding-heart"></i>
                </button>
            </div>

            <div class="card-body">
                <!-- 🔹 Tab Navigation -->
                <ul class="nav nav-tabs" id="usulanTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tabDraft-tab" data-bs-toggle="tab" data-bs-target="#tabDraft" type="button" role="tab">
                            🟡 Draft
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tabVerified-tab" data-bs-toggle="tab" data-bs-target="#tabVerified" type="button" role="tab">
                            ✅ Diverifikasi
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-3">
                    <!-- 🟡 Draft -->
                    <div class="tab-pane fade show active" id="tabDraft" role="tabpanel">
                        <table id="tableUsulanBansosDraft" class="table table-striped table-bordered w-100"></table>
                    </div>

                    <!-- ✅ Diverifikasi -->
                    <div class="tab-pane fade" id="tabVerified" role="tabpanel">
                        <table id="tableUsulanBansosVerified" class="table table-striped table-bordered w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->include('dtsen/usulan_bansos/form_usulan_bansos'); ?>

<!-- tambahkan script datatable datatables.config.js -->
<script src="<?= base_url('assets/js/datatables.config.js'); ?>"></script>

<script>
    $(function() {
        const userRole = <?= session()->get('role_id') ?? 99 ?>;

        // =====================================================
        // 🟡 TABLE DRAFT (untuk status draft)
        // =====================================================
        const tableDraft = $('#tableUsulanBansosDraft').DataTable({
            ajax: {
                url: '/usulan-bansos/data?status=draft',
                type: 'GET',
                dataType: 'json',
                dataSrc: json => json.data || []
            },
            columns: [{
                    data: null,
                    defaultContent: ''
                }, // kolom kontrol "+"
                {
                    data: null,
                    title: 'No',
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'nik',
                    title: 'NIK'
                },
                {
                    data: 'nama',
                    title: 'Nama'
                },
                {
                    data: 'dbj_nama_bansos',
                    title: 'Program',
                    defaultContent: '-'
                },
                {
                    data: 'status',
                    title: 'Status',
                    render: s => `<span class="badge bg-${
                {draft:'secondary',diverifikasi:'success'}[s] || 'secondary'
            }">${(s || 'draft').toUpperCase()}</span>`
                },
                {
                    data: 'created_at',
                    title: 'Tanggal Dibuat',
                    render: d => d ? new Date(d).toLocaleString('id-ID') : '-'
                },
                {
                    data: 'created_by_name',
                    title: 'Dibuat Oleh',
                    defaultContent: '-'
                },
                {
                    data: 'id',
                    title: 'Aksi',
                    orderable: false,
                    render: function(id, type, row) {
                        const role = <?= session()->get('role_id') ?? 99 ?>;
                        let btn = `
                    <button class="btn btn-danger btn-sm btnHapusUsulan" data-id="${id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                `;
                        if (role <= 3) {
                            btn += `
                        <button class="btn btn-success btn-sm btnVerifikasiUsulan ms-1" data-id="${id}">
                            <i class="fas fa-check-circle"></i> Verifikasi
                        </button>`;
                        }
                        return btn;
                    }
                }
            ],
            createdRow: row => $(row).find('td').css('text-align', 'left'),
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
        });

        // ==========================
        // 🔹 INIT TABLE VERIFIED
        // ==========================
        const tableVerified = $('#tableUsulanBansosVerified').DataTable({
            ajax: {
                url: '/usulan-bansos/data?status=diverifikasi',
                type: 'GET',
                dataType: 'json',
                dataSrc: json => json.data || []
            },
            columns: [{
                    data: null,
                    defaultContent: ''
                }, // kolom kontrol "+"
                {
                    data: null,
                    title: 'No',
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'nik',
                    title: 'NIK'
                },
                {
                    data: 'nama',
                    title: 'Nama'
                },
                {
                    data: 'dbj_nama_bansos',
                    title: 'Program',
                    defaultContent: '-'
                },
                {
                    data: 'status',
                    title: 'Status',
                    render: s => `<span class="badge bg-${
                {diverifikasi:'success',ditolak:'danger'}[s] || 'secondary'
            }">${(s || '').toUpperCase()}</span>`
                },
                {
                    data: 'updated_at',
                    title: 'Tanggal Diverifikasi',
                    render: d => d ? new Date(d).toLocaleString('id-ID') : '-'
                },
                {
                    data: 'updated_by_name',
                    title: 'Verifikator',
                    defaultContent: '-'
                }
            ],
            createdRow: row => $(row).find('td').css('text-align', 'left'),
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
        });

        // ==========================
        // 🗑️ DELETE HANDLER
        // ==========================
        $(document).on('click', '.btnHapusUsulan', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/usulan-bansos/delete/' + id,
                        type: 'DELETE',
                        dataType: 'json',
                        success: res => {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                                tableDraft.ajax.reload(null, false);
                            } else Swal.fire('Gagal', res.message, 'error');
                        },
                        error: xhr => Swal.fire('Error', 'Gagal menghapus data.', 'error')
                    });
                }
            });
        });

        // ==========================
        // ✅ VERIFIKASI HANDLER
        // ==========================
        $(document).on('click', '.btnVerifikasiUsulan', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Verifikasi Usulan',
                text: 'Apakah usulan ini ingin ditandai diverifikasi?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Verifikasi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/usulan-bansos/verifikasi/' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: res => {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                                tableDraft.ajax.reload(null, false);
                                tableVerified.ajax.reload(null, false);
                            } else Swal.fire('Gagal', res.message, 'error');
                        },
                        error: xhr => Swal.fire('Error', 'Tidak dapat memproses verifikasi.', 'error')
                    });
                }
            });
        });

        // ==========================
        // 🗑️ DELETE HANDLER
        // ==========================
        $(document).on('click', '.btnHapusUsulan', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/usulan-bansos/delete/' + id,
                        type: 'DELETE',
                        dataType: 'json',
                        success: res => {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                                tableDraft.ajax.reload(null, false);
                            } else Swal.fire('Gagal', res.message, 'error');
                        },
                        error: xhr => Swal.fire('Error', 'Gagal menghapus data.', 'error')
                    });
                }
            });
        });

        // ==========================
        // ✅ VERIFIKASI HANDLER
        // ==========================
        $(document).on('click', '.btnVerifikasiUsulan', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Verifikasi Usulan',
                text: 'Apakah usulan ini ingin ditandai diverifikasi?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Verifikasi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/usulan-bansos/verifikasi/' + id,
                        type: 'POST',
                        dataType: 'json',
                        success: res => {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                                tableDraft.ajax.reload(null, false);
                                tableVerified.ajax.reload(null, false);
                            } else Swal.fire('Gagal', res.message, 'error');
                        },
                        error: xhr => Swal.fire('Error', 'Tidak dapat memproses verifikasi.', 'error')
                    });
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>