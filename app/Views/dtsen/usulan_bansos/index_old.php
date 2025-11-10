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
        box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.3);
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
        <div class="container-fluid">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="<?= base_url('/pages'); ?>">Home</a></li>
                <li class="breadcrumb-item active"><?= esc($title); ?></li>
            </ol>
        </div>
    </section>

    <section class="content">
        <?php
        $desa_id = $user_login['kode_desa'] ?? '';
        ?>
        <div class="card-body">

            <?php if (session()->get('message')) : ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?= session()->get('message'); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-hand-holding-heart"></i> Daftar Usulan Bansos Bulan Ini</h5>

                    <button id="btnTambahUsulan" class="fab-btn" data-toggle="tooltip" title="Tambah Usulan Bansos">
                        <i class="fas fa-hand-holding-heart"></i>
                    </button>
                </div>

                <div class="card-body">
                    <table id="tableUsulanBansos" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Program</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ⏬ Load modal form -->
<?= $this->include('dtsen/usulan_bansos/form_usulan_bansos') ?>

<!-- Tambahkan sebelum </body> -->
<script src="<?= base_url('assets/js/usulan_bansos.js'); ?>"></script>

<script>
    // ================================
    // 🔄 Inisialisasi DataTables Usulan Bansos (final stable)
    // ================================
    $(function() {
        // pastikan tidak duplikat
        if ($.fn.DataTable.isDataTable('#tableUsulanBansos')) {
            $('#tableUsulanBansos').DataTable().destroy();
        }

        const table = $('#tableUsulanBansos').DataTable({
            ajax: {
                url: '/usulan-bansos/data',
                type: 'GET',
                dataType: 'json',
                // fleksibel: terima baik {data:[...]} atau [...] langsung
                dataSrc: function(json) {
                    console.log("📦 Data diterima:", json);
                    if (Array.isArray(json)) return json;
                    if (json.data) return json.data;
                    return [];
                },
                error: function(xhr, error, thrown) {
                    console.error("❌ Gagal memuat data:", error, thrown, xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: 'Server tidak mengembalikan data yang valid.'
                    });
                }
            },
            deferRender: true,
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            processing: true,
            language: {
                emptyTable: "Belum ada data usulan bulan ini.",
                processing: "⏳ Memuat data...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "›",
                    previous: "‹"
                }
            },
            columns: [{
                    data: null,
                    title: 'No',
                    className: 'text-center',
                    orderable: false,
                    render: (data, type, row, meta) => meta.row + 1
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
                    className: 'text-center',
                    render: s => {
                        const badge = {
                            draft: 'secondary',
                            dikirim: 'info',
                            diverifikasi: 'warning',
                            disetujui: 'success',
                            ditolak: 'danger'
                        } [s] || 'secondary';
                        return `<span class="badge bg-${badge}">${(s || 'draft').toUpperCase()}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    title: 'Tanggal Dibuat',
                    className: 'text-center',
                    render: d => d ?
                        new Date(d).toLocaleString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '-'
                },
                {
                    data: 'created_by',
                    title: 'Dibuat Oleh',
                    className: 'text-center',
                    defaultContent: '-'
                },
                {
                    data: 'id',
                    title: 'Aksi',
                    className: 'text-center',
                    orderable: false,
                    render: id => `
                  <button class="btn btn-danger btn-sm btnHapusUsulan" data-id="${id}">
                      <i class="fas fa-trash"></i>
                  </button>
              `
                }
            ],
            order: [
                [5, 'desc']
            ]
        });

        // 🗑️ hapus data
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
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: xhr => {
                            Swal.fire('Error', 'Gagal menghapus data.', 'error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>