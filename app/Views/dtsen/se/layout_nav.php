<div class="content-header pb-0">
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h3 class="m-0 fw-bold">👨‍👩‍👧‍👦 <?= $title ?></h3>
            <small class="text-muted">Manajemen Data SINDEN</small>
        </div>

        <div class="btn-group shadow-sm" role="group">

            <a href="<?= base_url('dtsen-se') ?>"
                class="btn btn-sm <?= $segment == 'daftar' ? 'btn-primary disabled' : 'btn-outline-primary bg-white' ?>">
                🔵 Daftar
            </a>

            <a href="<?= base_url('pembaruan-keluarga/draft') ?>"
                class="btn btn-sm <?= $segment == 'draft' ? 'btn-warning disabled' : 'btn-outline-warning bg-white' ?>">
                🟡 Draft
            </a>

            <a href="<?= base_url('pembaruan-keluarga/submitted') ?>"
                class="btn btn-sm <?= $segment == 'submitted' ? 'btn-info disabled' : 'btn-outline-info bg-white' ?>">
                🟢 Submitted
            </a>

            <a href="<?= base_url('pembaruan-keluarga/pemulihan') ?>"
                class="btn btn-sm <?= $segment == 'pemulihan' ? 'btn-danger disabled' : 'btn-outline-danger bg-white' ?>">
                🚨 Pemulihan
            </a>

            <?php if ($role_id <= 3): ?>
                <a href="<?= base_url('pembaruan-keluarga/arsip') ?>"
                    class="btn btn-sm <?= $segment == 'arsip' ? 'btn-dark disabled' : 'btn-outline-dark bg-white' ?>">
                    🔴 Arsip
                </a>
            <?php endif; ?>

        </div>
    </div>
</div>