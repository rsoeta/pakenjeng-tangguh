<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper mt-1">
    <section class="content pt-2">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title mb-0">Reaktivasi PBI</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelReaktivasi" class="table table-sm table-hover table-striped w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Mendesak</th>
                                    <th>Tanggal Draft</th>
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

<script>
    const ROLE_ID = <?= (int) session('role_id') ?>;

    function getStatusBadge(status) {
        const badgeClassMap = {
            0: 'secondary',
            1: 'warning',
            2: 'info',
            3: 'success',
            4: 'danger',
            5: 'primary'
        };

        const statusLabelMap = {
            0: 'Draft',
            1: 'Diajukan',
            2: 'Diverifikasi',
            3: 'Disetujui',
            4: 'Ditolak',
            5: 'Diajukan SIKS',
            6: 'Disetujui Kab',
            7: 'Ditolak Kab'
        };

        const badgeClass = badgeClassMap[status] || 'dark';
        const label = statusLabelMap[status] || 'Tidak diketahui';
        return `<span class="badge badge-${badgeClass}">${label}</span>`;
    }

    function getActionButtons(row) {
        const status = Number(row.status_pengajuan);
        const id = row.id;
        let buttons = '';

        if (ROLE_ID === 4 && status === 0) {
            buttons += `<button type="button" class="btn btn-xs btn-warning btn-action" data-url="/pbi/reaktivasi/submit/${id}" data-confirm="Ajukan data ini?">Ajukan</button>`;
        }

        if (ROLE_ID === 3 && status === 1) {
            buttons += `<button type="button" class="btn btn-xs btn-info btn-action" data-url="/pbi/reaktivasi/verify/${id}" data-confirm="Verifikasi data ini?">Verifikasi</button>`;
        }

        if (ROLE_ID === 3 && status === 2) {
            buttons += `<button type="button" class="btn btn-xs btn-success btn-action mr-1" data-url="/pbi/reaktivasi/approve/${id}" data-confirm="Setujui data ini?">Setujui</button>`;
            buttons += `<button type="button" class="btn btn-xs btn-danger btn-action" data-url="/pbi/reaktivasi/reject/${id}" data-confirm="Tolak data ini?">Tolak</button>`;
        }

        if (ROLE_ID === 3 && status === 3) {
            buttons += `<button type="button" class="btn btn-xs btn-primary btn-action" data-url="/pbi/reaktivasi/kirimSiks/${id}" data-confirm="Kirim data ke SIKS?">Kirim SIKS</button>`;
        }

        return buttons || '-';
    }

    $(function() {
        const table = $('#tabelReaktivasi').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '/pbi/reaktivasi/tabel',
                type: 'POST'
            },
            columns: [{
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'nik'
                },
                {
                    data: 'nama_snapshot'
                },
                {
                    data: 'status_pengajuan',
                    render: function(data) {
                        return getStatusBadge(Number(data));
                    }
                },
                {
                    data: 'kondisi_mendesak',
                    render: function(data) {
                        if (Number(data) === 1) {
                            return '<span class="badge badge-danger">Mendesak</span>';
                        }

                        return '<span class="badge badge-light">Normal</span>';
                    }
                },
                {
                    data: 'tanggal_draft',
                    defaultContent: '-'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return getActionButtons(row);
                    }
                }
            ]
        });

        $('#tabelReaktivasi').on('click', '.btn-action', function() {
            const url = $(this).data('url');
            const confirmText = $(this).data('confirm') || 'Lanjutkan proses?';

            Swal.fire({
                title: 'Konfirmasi',
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message || 'Aksi berhasil.', 'success');
                            table.ajax.reload(null, false);
                            return;
                        }

                        Swal.fire('Gagal', response.message || 'Proses gagal.', 'error');
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection(); ?>
