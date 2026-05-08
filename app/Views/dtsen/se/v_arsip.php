<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-0">
    <?= $this->include('dtsen/se/layout_nav') ?>

    <section class="content">
        <div class="card shadow-sm border-danger">
            <!-- ===================== TAB 4: ARSIP ===================== -->

            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom pb-0">
                    <!-- 🔥 TAB ATAS UNTUK ARSIP -->
                    <ul class="nav nav-tabs" id="arsipTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="arsipKeluarga-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#arsipKeluarga"
                                type="button" role="tab">
                                📁 Arsip Keluarga
                            </button>
                        </li>

                        <li class="nav-item">
                            <button class="nav-link" id="arsipAnggota-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#arsipAnggota"
                                type="button" role="tab">
                                👤 Arsip Anggota
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content">

                    <!-- ===================== SUBTAB: ARSIP KELUARGA ===================== -->
                    <div class="tab-pane fade show active" id="arsipKeluarga" role="tabpanel">
                        <h5 class="fw-bold mb-3">📁 Arsip Keluarga</h5>

                        <table id="tableArsipKeluarga" class="table table-striped table-hover w-100">
                            <thead class="text-center">
                                <tr>
                                    <th>No.</th>
                                    <th>Kepala Keluarga</th>
                                    <th>No KK</th>
                                    <th>Alamat</th>
                                    <th>RW/RT</th>
                                    <th>Tanggal Hapus</th>
                                    <th>Alasan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- ===================== SUBTAB: ARSIP ANGGOTA ===================== -->
                    <div class="tab-pane fade" id="arsipAnggota" role="tabpanel">
                        <h5 class="fw-bold mb-3">👤 Arsip Anggota Keluarga</h5>

                        <table id="tableArsipAnggota" class="table table-striped table-hover w-100">
                            <thead class="text-center">
                                <tr>
                                    <th>No.</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Hubungan</th>
                                    <th>ID KK</th>
                                    <th>Tanggal Hapus</th>
                                    <th>Alasan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // =============== TABLE ARSIP KELUARGA =================
        let dtArsipKeluarga = $('#tableArsipKeluarga').DataTable({
            ajax: {
                url: '/dtsen-se/tabel_arsip',
                type: 'POST',
                dataSrc: 'data'
            },
            responsive: true,
            pageLength: 10,
            autoWidth: false,

            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },

            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'kepala_keluarga'
                },
                {
                    data: 'no_kk'
                },
                {
                    data: 'alamat'
                },
                {
                    data: null,
                    render: r => `RW ${r.rw} / RT ${r.rt}`
                },
                {
                    data: 'deleted_at'
                },
                {
                    data: 'delete_reason'
                },
                {
                    data: null,
                    render: row => `
                    <button class="btn btn-success btn-sm btnRestoreKK" data-id="${row.id_kk}">
                        <i class="fas fa-undo"></i> Restore
                    </button>`
                }
            ]
        });

        // Restore KK
        $(document).on('click', '.btnRestoreKK', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: "Pulihkan Keluarga?",
                text: "Data akan dikembalikan ke daftar keluarga.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, kembalikan!"
            }).then(result => {
                if (result.isConfirmed) {
                    $.post('/dtsen-se/restore', {
                        id_kk: id
                    }, function(res) {
                        if (res.status) {
                            Swal.fire("Berhasil!", res.message, "success");
                            dtArsipKeluarga.ajax.reload(null, false);
                        } else {
                            Swal.fire("Gagal!", res.message, "error");
                        }
                    }, 'json');
                }
            });
        });

        // =============== TABLE ARSIP ANGGOTA =================
        let dtArsipAnggota = null;

        // Inisialisasi tabel anggota HANYA saat tab-nya diklik agar tidak berat
        $('button[data-bs-target="#arsipAnggota"]').on('shown.bs.tab', function() {
            if (!dtArsipAnggota) {
                dtArsipAnggota = $('#tableArsipAnggota').DataTable({
                    ajax: {
                        url: '/dtsen-se/arsip-anggota',
                        type: 'GET',
                        dataSrc: 'data'
                    },
                    responsive: true,
                    pageLength: 10,
                    autoWidth: false,

                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                    },

                    columns: [{
                            data: null,
                            render: (d, t, r, m) => m.row + 1
                        },
                        {
                            data: 'nik'
                        },
                        {
                            data: 'nama'
                        },
                        {
                            data: 'jenis_shdk'
                        },
                        {
                            data: 'id_kk'
                        },
                        {
                            data: 'deleted_at'
                        },
                        {
                            data: 'delete_reason'
                        },
                        {
                            data: null,
                            render: row => `
                            <button class="btn btn-success btn-sm btnRestoreAnggota" data-id="${row.id}">
                                <i class="fas fa-undo"></i> Restore
                            </button>`
                        }
                    ]
                });
            }
        });

        // Restore Anggota
        $(document).on('click', '.btnRestoreAnggota', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: "Pulihkan Anggota?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, pulihkan!"
            }).then(result => {
                if (result.isConfirmed) {
                    $.post('/dtsen-se/restore-art', {
                        id_art: id
                    }, function(res) {
                        if (res.status) {
                            Swal.fire("Berhasil!", res.message, "success");
                            dtArsipAnggota.ajax.reload(null, false);
                        } else {
                            Swal.fire("Gagal!", res.message, "error");
                        }
                    }, 'json');
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>