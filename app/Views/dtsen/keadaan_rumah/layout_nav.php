<div class="content-header pt-1 pb-1">
    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
        <div>
            <h3 class="m-0 fw-bold text-dark"><i class="fas fa-home text-primary me-2"></i><?= esc($title) ?></h3>
            <small class="text-muted">Klasifikasi Fisik Rumah &amp; Status Kepemilikan Tempat Tinggal (DTSEN)</small>
        </div>

        <div class="btn-group shadow-sm" role="group">
            <a href="<?= base_url('dtsen-se') ?>" class="btn btn-sm btn-outline-secondary bg-white" title="Kembali ke Data Keluarga">
                <i class="fas fa-arrow-left me-1"></i> Data Keluarga
            </a>

            <a href="<?= base_url('dtsen/keadaan-rumah') ?>"
                class="btn btn-sm <?= ($segment ?? '') === 'tabel' ? 'btn-primary active text-white fw-bold' : 'btn-outline-primary bg-white' ?>">
                <i class="fas fa-table me-1"></i> Data Tabel
            </a>

            <a href="<?= base_url('dtsen/keadaan-rumah/grafik') ?>"
                class="btn btn-sm <?= ($segment ?? '') === 'grafik' ? 'btn-primary active text-white fw-bold' : 'btn-outline-primary bg-white' ?>">
                <i class="fas fa-chart-pie me-1"></i> Pie Graph
            </a>
        </div>
    </div>
</div>