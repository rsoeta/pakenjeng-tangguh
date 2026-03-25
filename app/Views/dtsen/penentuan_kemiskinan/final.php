<?= $this->extend('templates/index') ?>
<?= $this->section('content') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Data Kemiskinan Final</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableFinal" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kepala Keluarga</th>
                                    <th>NIK</th>
                                    <th>No KK</th>
                                    <th>RW</th>
                                    <th>RT</th>
                                    <th>Status</th>
                                    <th>Tanggal Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($final as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($row['kepala_keluarga']) ?></td>
                                        <td><?= esc($row['nik']) ?></td>
                                        <td><?= esc($row['no_kk']) ?></td>
                                        <td><?= esc($row['rw']) ?></td>
                                        <td><?= esc($row['rt']) ?></td>
                                        <td>
                                            <?php if ($row['status_kemiskinan'] == 'miskin'): ?>
                                                <span class="badge bg-danger">Miskin</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Tidak Miskin</span>
                                            <?php endif ?>
                                        </td>
                                        <td><?= $row['verified_at'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>