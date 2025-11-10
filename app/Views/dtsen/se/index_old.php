<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<?php
$user = session()->get('role_id');
isset($user_login['lp_kode']) ? $desa_id = $user_login['lp_kode'] : $desa_id = session()->get('kode_desa');
$ops = session()->get('jabatan');
$level = session()->get('level');
$wilayah_tugas = session()->get('wilayah_tugas');
?>

<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<div class="content-wrapper mt-0">
    <div class="content-header">
        <h3 class="mb-2">📋 Data Keluarga DTSEN</h3>
        <small class="text-muted">Kelola data keluarga DTSEN dan input kategori desil</small>
    </div>

    <section class="content">
        <div class="card shadow-sm">
            <div class="card-body">

                <!-- 🔍 Filter Wilayah -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filterRW" class="form-label fw-bold">Filter No. RW</label>
                        <select id="filterRW" class="form-select">
                            <option value="">[ Semua RW ]</option>
                            <?php foreach ($dataRW as $rw): ?>
                                <option value="<?= $rw['rw'] ?>"><?= $rw['rw'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <p id="activeWilayah" class="text-muted small"></p>


                <table id="tableKeluarga" class="table table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>No.</th>
                            <th>No KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Alamat</th>
                            <th>RW/RT</th>
                            <th>Kategori Desil</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- modal input desil -->
<?= $this->include('dtsen/se/modal_input_desil'); ?>

<!-- JS DataTables -->
<script src="<?= base_url('assets/js/input_desil.js'); ?>"></script>

<!-- JS dtsen_se -->
<!-- <script src="<?= base_url('assets/js/dtsen_se.js'); ?>"></script> -->

<script>
    $(document).ready(function() {
        const wilayah = "<?= session()->get('wilayah_tugas') ?>";
        $("#activeWilayah").html(`Menampilkan data wilayah tugas: <strong>${wilayah}</strong>`);
    });

    $(document).ready(function() {
        const tableKeluarga = $('#tableKeluarga').DataTable({
            ajax: {
                url: '/dtsen-se/tabel_data',
                type: 'POST',
                data: d => d.filterRW = $('#filterRW').val(),
                dataSrc: 'data'
            },
            responsive: {
                details: {
                    type: 'column',
                    target: 0 // kolom pertama jadi tombol expand "+"
                }
            },
            columnDefs: [{
                    className: 'dtr-control',
                    orderable: false,
                    targets: 0
                },
                {
                    orderable: false,
                    searchable: false,
                    targets: 1
                } // kolom nomor
            ],
            order: [
                [2, 'asc']
            ], // urut default berdasarkan No KK
            columns: [{
                    data: null,
                    defaultContent: '',
                    className: 'text-center'
                },
                {
                    data: null,
                    title: 'No.',
                    className: 'text-center',
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: 'no_kk',
                    title: 'No. KK',
                    className: 'text-nowrap'
                },
                {
                    data: 'kepala_keluarga',
                    title: 'Kepala Keluarga',
                    className: 'text-capitalize'
                },
                {
                    data: 'alamat',
                    title: 'Alamat',
                    render: d => d ? d : '-'
                },
                {
                    data: null,
                    title: 'Wilayah',
                    render: r => {
                        const rw = r.rw ? `RW ${r.rw}` : '-';
                        const rt = r.rt ? `RT ${r.rt}` : '-';
                        return `<span class="badge bg-light text-dark border">${rw}</span> / <span class="badge bg-info text-dark">${rt}</span>`;
                    },
                    className: 'text-center'
                },
                {
                    data: 'kategori_desil',
                    title: 'Desil',
                    render: d => {
                        if (!d) return '<span class="badge bg-secondary">Belum</span>';
                        const desil = parseInt(d);
                        let warna = 'secondary';
                        if (desil <= 2) warna = 'danger';
                        else if (desil <= 4) warna = 'warning';
                        else warna = 'success';
                        return `<span class="badge bg-${warna}">${desil}</span>`;
                    }
                },
                {
                    data: null,
                    title: 'Aksi',
                    className: 'text-center text-nowrap',
                    render: row => {
                        const userRole = <?= session()->get('role_id') ?? 99 ?>;
                        const noKk = row.no_kk;
                        const idKk = row.id_kk;
                        const nama = row.kepala_keluarga;

                        let btns = `
                        <a href="/pembaruan-keluarga/detail/${idKk}" 
                           class="btn btn-sm btn-success me-1" 
                           title="Pembaruan Data Keluarga - ${nama}">
                           <i class="fas fa-users-cog"></i>
                        </a>
                    `;

                        if (userRole <= 3) {
                            btns += `
                            <button class="btn btn-sm btn-primary btnInputDesil"
                                data-id="${idKk}"
                                data-nama="${nama}"
                                data-nokk="${noKk}"
                                data-alamat="${row.alamat}"
                                data-desil="${row.kategori_desil ?? ''}">
                                <i class="fas fa-hand-holding-heart"></i>
                            </button>
                        `;
                        }

                        return btns || '<span class="text-muted"><i class="fas fa-lock"></i> Tidak Diizinkan</span>';
                    }
                }
            ],
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });

        // 🔄 Refresh data bila filter RW berubah
        $('#filterRW').on('change', () => tableKeluarga.ajax.reload());
    });

    $(document).ready(function() {
        // tombol filter
        $('#btnFilter').on('click', function() {
            table.ajax.reload();
        });

        // tombol reset
        $('#btnReset').on('click', function() {
            $('#filter_rw').val('');
            $('#filter_rt').val('');
            table.ajax.reload();
        });

        // buka modal input desil
        $(document).on('click', '.btnInputDesil', function() {
            const idKk = $(this).data('id');
            const nama = $(this).data('nama');
            const noKk = $(this).data('nokk');
            const alamat = $(this).data('alamat');
            const desil = $(this).data('desil');

            $('#modalInputDesil').modal('show');
            $('#id_kk').val(idKk);
            $('#no_kk').val(noKk);
            $('#nama_kepala').val(nama);
            $('#alamat').val(alamat);
            $('#kategori_desil').val(desil || '');

            const role = <?= session()->get('role_id') ?? 99 ?>;
            if (role > 3) {
                $('#kategori_desil').prop('disabled', true);
                $('#formInputDesil button[type="submit"]').prop('disabled', true).text('Tidak Diizinkan');
            } else {
                $('#kategori_desil').prop('disabled', false);
                $('#formInputDesil button[type="submit"]').prop('disabled', false).text('Simpan');
            }
        });

        // reload tabel setelah update desil
        $(document).on('desilUpdated', function() {
            table.ajax.reload(null, false);
        });
    });
</script>

<?= $this->endSection(); ?>