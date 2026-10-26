<?= $this->extend('templates/index'); ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><i class="fas fa-file-medical-alt text-primary mr-2"></i>Asesmen Lapad Ruhama / SLRT</h1>
                    <small class="text-muted">Instrumen Penanganan Pemerlu Pelayanan Kesejahteraan Sosial (PPKS) Kabupaten Garut</small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Assessment</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-clipboard-list"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Asesmen Tercatat</span>
                            <span class="info-box-number text-lg"><?= number_format($totalAsesmen); ?> <small>Berkas</small></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Asesmen Bulan Ini</span>
                            <span class="info-box-number text-lg"><?= number_format($bulanIni); ?> <small>Kasus</small></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-print"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Berkas Siap Cetak</span>
                            <span class="info-box-number text-lg"><?= number_format($siapCetak); ?> <small>Dokumen</small></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Table -->
            <div class="card card-primary card-outline shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-table mr-1"></i> Data Asesmen PMKS Lapad Ruhama
                    </h3>
                    <div class="card-tools ml-auto">
                        <a href="<?= base_url('assessment/tambah'); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Buat Asesmen Baru
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="btnReload">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableAssessment" class="table table-bordered table-striped table-hover text-sm w-100">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th width="4%">No</th>
                                    <th width="15%">No. Asesmen / Tanggal</th>
                                    <th width="18%">No. KK / Lokasi</th>
                                    <th width="18%">Sasaran (Pasien / PMKS)</th>
                                    <th width="20%">Permasalahan & Rekomendasi</th>
                                    <th width="10%">Fasilitator</th>
                                    <th width="7%">Status</th>
                                    <th width="8%">Aksi</th>
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

<!-- Modal Detail Asesmen -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalDetailLabel"><i class="fas fa-info-circle mr-2"></i>Rincian Berkas Asesmen</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="detailContent">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-info"></i>
                    <p class="mt-2 text-muted">Memuat rincian asesmen...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                <a href="#" id="modalBtnWord" class="btn btn-success btn-sm"><i class="fas fa-file-word mr-1"></i> Unduh Word</a>
                <a href="#" id="modalBtnCetak" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-print mr-1"></i> Cetak Berkas</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#tableAssessment').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "<?= base_url('assessment/datatable'); ?>",
            type: "POST"
        },
        columns: [
            { data: 'no', className: 'text-center align-middle', orderable: false },
            { data: 'nomor_asesmen', className: 'align-middle' },
            { data: 'kk_info', className: 'align-middle' },
            { data: 'sasaran', className: 'align-middle' },
            { data: 'permasalahan', className: 'align-middle' },
            { data: 'petugas', className: 'align-middle text-center' },
            { data: 'status', className: 'align-middle text-center' },
            { data: 'action', className: 'align-middle text-center', orderable: false }
        ],
        language: {
            processing: '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...',
            zeroRecords: 'Belum ada berkas asesmen yang dibuat',
            emptyTable: 'Tidak ada data asesmen',
            search: 'Pencarian:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_ s.d. _END_ dari _TOTAL_ berkas',
            infoEmpty: 'Menampilkan 0 berkas',
            paginate: {
                first: 'Awal',
                last: 'Akhir',
                next: 'Lanjut',
                previous: 'Kembali'
            }
        },
        order: [[1, 'desc']]
    });

    $('#btnReload').on('click', function() {
        table.ajax.reload(null, false);
    });

    // Detail Button Handler
    $(document).on('click', '.btn-detail', function() {
        var id = $(this).data('id');
        $('#modalBtnCetak').attr('href', '<?= base_url('assessment/cetak'); ?>/' + id);
        $('#modalBtnWord').attr('href', '<?= base_url('assessment/export-word'); ?>/' + id);
        $('#modalDetail').modal('show');
        $('#detailContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-info"></i><p class="mt-2 text-muted">Memuat rincian data...</p></div>');

        $.ajax({
            url: '<?= base_url('assessment/detail'); ?>/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status && res.data) {
                    var d = res.data;
                    var html = '<div class="p-3">';
                    html += '<div class="row">';
                    html += '<div class="col-md-6 mb-2"><strong>No. Asesmen:</strong> ' + d.nomor_asesmen + '</div>';
                    html += '<div class="col-md-6 mb-2"><strong>Tanggal:</strong> ' + d.tanggal_asesmen + '</div>';
                    html += '<div class="col-md-6 mb-2"><strong>No. KK:</strong> ' + d.no_kk + '</div>';
                    html += '<div class="col-md-6 mb-2"><strong>Kepala Keluarga:</strong> ' + (d.kepala_desa_nama ? d.nama_sasaran : '-') + '</div>';
                    html += '<div class="col-md-6 mb-2"><strong>Sasaran Pasien:</strong> <span class="badge badge-primary text-sm">' + d.nama_sasaran + '</span> (NIK: ' + d.nik_sasaran + ')</div>';
                    html += '<div class="col-md-6 mb-2"><strong>Pembuat Pernyataan:</strong> ' + d.nama_pembuat_pernyataan + ' (NIK: ' + d.nik_pembuat_pernyataan + ')</div>';
                    html += '<div class="col-12 mb-2"><strong>Permasalahan:</strong><div class="alert alert-warning p-2 mt-1 mb-0">' + (d.riwayat_penyakit || '-') + '<br>' + (d.permasalahan_tambahan || '') + '</div></div>';
                    html += '<div class="col-12 mb-2"><strong>Permohonan Bantuan:</strong><div class="alert alert-info p-2 mt-1 mb-0">' + (d.permohonan_bantuan || '-') + '</div></div>';
                    html += '<div class="col-md-6 mb-2"><strong>Fasilitator:</strong> ' + d.fasilitator_nama + '</div>';
                    html += '<div class="col-md-6 mb-2"><strong>Ketua Puskesos:</strong> ' + (d.ketua_puskesos_nama || '-') + '</div>';
                    html += '<div class="col-md-6 mb-2"><strong>Kepala Desa:</strong> ' + (d.kepala_desa_nama || '-') + '</div>';
                    html += '<div class="col-md-6 mb-2"><strong>Supervisor SLRT:</strong> ' + (d.supervisor_nama || '-') + ' (' + (d.supervisor_niat || '-') + ')</div>';
                    html += '</div>';

                    // 4 Pilar Foto Dokumentasi
                    html += '<hr><h6 class="font-weight-bold mb-3"><i class="fas fa-camera mr-1"></i> Dokumentasi Lapangan (4 Pilar Foto):</h6>';
                    html += '<div class="row text-center">';

                    var fPasien = d.foto_pasien ? ('<?= base_url(); ?>/' + d.foto_pasien) : null;
                    var fDepan  = d.foto_depan ? ('<?= base_url(); ?>/' + d.foto_depan) : (d.kk_foto_depan ? ('<?= base_url(); ?>/' + d.kk_foto_depan) : null);
                    var fDalam  = d.foto_dalam ? ('<?= base_url(); ?>/' + d.foto_dalam) : (d.kk_foto_dalam ? ('<?= base_url(); ?>/' + d.kk_foto_dalam) : null);
                    var fMck    = d.foto_kamar_mandi ? ('<?= base_url(); ?>/' + d.foto_kamar_mandi) : (d.rt_foto_mck ? ('<?= base_url(); ?>/' + d.rt_foto_mck) : null);

                    var listFoto = [
                        { label: '1. Pasien / Sasaran', url: fPasien },
                        { label: '2. Rumah Depan', url: fDepan },
                        { label: '3. Rumah Dalam', url: fDalam },
                        { label: '4. MCK / Sanitasi', url: fMck }
                    ];

                    listFoto.forEach(function(f) {
                        html += '<div class="col-6 col-md-3 mb-2">';
                        html += '<div class="border rounded p-1 bg-light">';
                        html += '<small class="font-weight-bold d-block mb-1 text-truncate">' + f.label + '</small>';
                        if (f.url) {
                            html += '<a href="' + f.url + '" target="_blank"><img src="' + f.url + '" class="img-fluid rounded border" style="height: 100px; object-fit: cover; width: 100%;"></a>';
                        } else {
                            html += '<div class="text-muted d-flex align-items-center justify-content-center border rounded bg-white" style="height: 100px;"><small class="font-italic">Tidak Ada</small></div>';
                        }
                        html += '</div></div>';
                    });
                    html += '</div>';

                    html += '</div>';
                    $('#detailContent').html(html);
                } else {
                    $('#detailContent').html('<div class="alert alert-danger m-3">Gagal memuat rincian asesmen.</div>');
                }
            },
            error: function() {
                $('#detailContent').html('<div class="alert alert-danger m-3">Terjadi kesalahan pada server.</div>');
            }
        });
    });

    // Delete Button Handler
    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        var no = $(this).data('no');

        Swal.fire({
            title: 'Hapus Asesmen?',
            text: 'Berkas ' + no + ' akan dihapus dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('assessment/hapus'); ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            Swal.fire('Terhapus!', res.message, 'success');
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                    }
                });
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

