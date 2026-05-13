<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $title; ?></h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalImport">
                        <i class="fas fa-file-excel mr-1"></i> Impor dari Google Sheets (Excel)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success'); ?></div>
            <?php endif; ?>

            <div class="card card-dark">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="tableMasterKKS">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama Penerima</th>
                                    <th>No. KKS</th>
                                    <th>Wilayah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($list as $row) : ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $row['nik']; ?></td>
                                        <td class="font-weight-bold text-uppercase"><?= $row['nama_penerima']; ?></td>
                                        <td class="text-primary"><?= $row['no_kks'] ?: '-'; ?></td>
                                        <td><?= $row['alamat']; ?> RT <?= $row['rt']; ?> / RW <?= $row['rw']; ?></td>
                                        <td>
                                            <?php
                                            // Mengamankan string (menghapus spasi berlebih dan menjadikan huruf kecil untuk pengecekan)
                                            $status_kks_cek = strtolower(trim($row['status_kks']));

                                            // Menentukan warna badge
                                            if ($status_kks_cek == 'aktif') {
                                                $badge_class = 'badge-success'; // Hijau
                                            } elseif ($status_kks_cek == 'non aktif' || $status_kks_cek == 'non-aktif') {
                                                $badge_class = 'badge-danger';  // Merah
                                            } else {
                                                $badge_class = 'badge-secondary'; // Abu-abu (untuk status lain/kosong)
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class; ?>"><?= $row['status_kks']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-upload mr-2"></i> Impor Data KKS</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="<?= base_url('master-kks/import'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="alert alert-warning small">
                        <i class="fas fa-info-circle mr-1"></i> <strong>Instruksi:</strong> Simpan file Google Sheets Anda sebagai format <strong>.xlsx</strong> terlebih dahulu. Pastikan urutan kolom sesuai dengan struktur aslinya.
                    </div>
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">Mulai Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableMasterKKS').DataTable({
            "responsive": true,
            "pageLength": 10,
            "language": {
                "search": "Cari Nama/NIK:",
                "emptyTable": "Belum ada data. Silakan impor file Excel menggunakan tombol di atas.",
                "zeroRecords": "Data NIK/Nama yang dicari tidak ditemukan."
            }
        });
    });
</script>

<?= $this->endSection(); ?>