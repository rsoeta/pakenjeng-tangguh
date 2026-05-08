<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-0">
    <?= $this->include('dtsen/se/layout_nav') ?>

    <section class="content">
        <div class="card shadow-sm border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <button id="btnReloadSubmitted" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-sync-alt"></i> Muat Ulang
                    </button>
                </div>

                <table id="tableSubmitted" class="table table-striped table-hover nowrap w-100">
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

        // ========================= 🟢 TABLE SUBMITTED =========================
        const tableSubmitted = $('#tableSubmitted').DataTable({
            ajax: {
                url: '/pembaruan-keluarga/data?submitted=1',
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
                    data: 'nama_kepala'
                },
                {
                    data: 'no_kk_target',
                    className: 'text-nowrap',
                    render: function(noKK, type, row) {
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
                    data: 'status',
                    render: () => `<span class="badge bg-info">SUBMITTED</span>`
                },
                {
                    data: 'updated_at',
                    render: d => d ? new Date(d).toLocaleString('id-ID') : '-'
                },
                {
                    data: 'created_by_name',
                    render: function(name, type, row) {

                        const nope = row.created_by_nope || '';

                        // Jika tidak ada nomor → tampilkan nama biasa
                        if (!nope) return name || '-';

                        // Normalisasi nomor → ubah ke 62xxx
                        let phone = nope.replace(/\D/g, ""); // buang spasi / tanda
                        if (phone.startsWith("0")) phone = "62" + phone.substring(1);
                        else if (!phone.startsWith("62")) phone = "62" + phone;

                        return `
                            <a href="https://wa.me/${phone}" 
                            class="text-success fw-bold" 
                            target="_blank">
                                <i class="fab fa-whatsapp"></i> ${name}
                            </a>
                        `;
                    }
                },

                // PERBAIKAN DI SINI
                {
                    data: 'id',
                    render: function(id, type, row, meta) {
                        return `
                    <a href="/pembaruan-keluarga/lanjutkan/${id}" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-eye"></i> Lihat
                    </a>
                    <button class="btn btn-danger btn-sm btnDeleteUsulan" data-id="${row.id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                `;
                    }
                }
            ],
            responsive: true,
            pageLength: 10
        });

    });
</script>

<?= $this->endSection(); ?>