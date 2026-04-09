<?= $this->extend('templates/index') ?>
<?= $this->section('content') ?>

<style>
    .btn.disabled {
        pointer-events: none;
        opacity: 0.65;
    }
</style>
<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <?php
            $uri = service('uri');
            $segment = $uri->getSegment(3);
            ?>

            <div class="d-flex justify-content-between align-items-center mb-3">

                <!-- TITLE -->
                <h5 class="m-0">
                    <?= $title ?>
                </h5>

                <!-- NAV FLOW BUTTON -->
                <div class="btn-group" role="group">

                    <a href="<?= base_url('dtsen/kemiskinan/penentuan') ?>"
                        class="btn btn-sm <?= $segment == 'penentuan' ? 'btn-primary disabled' : 'btn-outline-primary' ?>">
                        <i class="fas fa-edit me-1"></i> Penentuan
                    </a>

                    <a href="<?= base_url('dtsen/kemiskinan/verifikasi') ?>"
                        class="btn btn-sm <?= $segment == 'verifikasi' ? 'btn-warning disabled' : 'btn-outline-warning' ?>">
                        <i class="fas fa-check-circle me-1"></i> Verifikasi
                    </a>

                    <a href="<?= base_url('dtsen/kemiskinan/final') ?>"
                        class="btn btn-sm <?= $segment == 'final' ? 'btn-success disabled' : 'btn-outline-success' ?>">
                        <i class="fas fa-database me-1"></i> Final
                    </a>

                </div>

            </div>
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