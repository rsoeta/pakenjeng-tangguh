<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-0">
    <?= $this->include('dtsen/se/layout_nav') ?>

    <section class="content">
        <div class="card shadow-sm border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <button id="btnReloadDraft" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-sync-alt"></i> Muat Ulang
                    </button>
                </div>
                
                <table id="tableDraftKeluarga" class="table table-striped table-hover nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th></th>
                            <th>No.</th>
                            <th>Kepala Keluarga</th>
                            <th>No KK</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>

            </div>
    </section>
</div>

...

<script>
    $(document).ready(function() {

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
                    data: 'nama_kepala',
                    title: 'Kepala Keluarga'
                },
                {
                    data: 'no_kk_target',
                    title: 'No KK'
                },
                {
                    data: 'status',
                    title: 'Status',
                    render: s => `<span class="badge bg-secondary">${(s || 'draft').toUpperCase()}</span>`
                },
                {
                    data: 'updated_at',
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
                    render: function(id, type, row, meta) {
                        return `
                        <a href="/pembaruan-keluarga/lanjutkan/${id}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Lanjutkan
                        </a>
                        <button class="btn btn-danger btn-sm btnDeleteUsulan" data-id="${row.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                `;
                    }
                }
            ],
            responsive: true,
            pageLength: 10,
            createdRow: row => $(row).find('td').css('text-align', 'left'),
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
        });

        $('#btnReloadDraft').on('click', function() {
            tableDraft.ajax.reload(null, false);
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
            setTimeout(() => {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Muat Ulang');
            }, 800);
        });

    });
</script>

<?= $this->endSection(); ?>