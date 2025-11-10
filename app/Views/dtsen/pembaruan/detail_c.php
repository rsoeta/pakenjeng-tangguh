<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">👨‍👩‍👧 Pembaruan Data Keluarga</h4>

        <a href="<?= base_url('dtsen-se') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Keluarga
        </a>
    </div>


    <section class="content">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0">🗂️ Detail Pembaruan Data Keluarga</h4>
                    <?php $status = $usulan['status'] ?? $sumber ?? ''; ?>
                    <?php if (!empty($usulan['status']) && $usulan['status'] == 'draft'): ?>
                        <span class="badge bg-warning text-dark">Draft Pembaruan</span>
                    <?php elseif ($status == $sumber): ?>
                        <span class="badge bg-success">Baru</span>
                    <?php elseif (empty($usulan['status'])): ?>
                        <span class="badge bg-primary">Terverifikasi</span>
                    <?php endif; ?>
                </div>
                <!-- Badge Kategori Desil sebelah kanan -->
                <div class="mb-3 text-end">
                    <?php if (!empty($kategori_desil)) : ?>
                        <button type="button" class="btn 
                <?php if ($kategori_desil <= 2) echo 'btn-danger';
                        elseif ($kategori_desil <= 4) echo 'btn-warning';
                        else echo 'btn-success'; ?>">
                            Desil <span class="badge bg-secondary"><?= $kategori_desil ?></span>
                        </button>
                    <?php endif ?>
                </div>
                <ul class="nav nav-tabs" id="pembaruanTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabKeluarga">Data Keluarga</a></li>
                    <li class="nav-item" data-bs-target="#tab-anggota"><a class="nav-link" data-bs-toggle="tab" href="#tabAnggota">Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabRumah">Rumah</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabAset">Aset</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabFoto">Foto & Geotag</a></li>
                </ul>

                <div class="tab-content p-3">
                    <div class="tab-pane fade show active" id="tabKeluarga">
                        <?= $this->include('dtsen/pembaruan/tab_keluarga'); ?>
                    </div>
                    <div class="tab-pane fade" id="tabAnggota">
                        <?= $this->include('dtsen/pembaruan/tab_anggota'); ?>
                    </div>
                    <div class="tab-pane fade" id="tabRumah">
                        <?= $this->include('dtsen/pembaruan/tab_rumah'); ?>
                    </div>
                    <div class="tab-pane fade" id="tabAset">
                        <?= $this->include('dtsen/pembaruan/tab_aset'); ?>
                    </div>
                    <div class="tab-pane fade" id="tabFoto">
                        <?= $this->include('dtsen/pembaruan/tab_foto'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($user['role_id'] <= 3): ?>
            <div class="text-end">
                <button id="btnApply"
                    class="btn btn-success btn-lg shadow-sm mt-3"
                    data-usulan-id="<?= esc($usulan['id'] ?? $payload['id'] ?? '') ?>">
                    <i class="fas fa-check-circle"></i> Terapkan Data ke Database Utama
                </button>
            <?php endif; ?>
            </div>
    </section>
</div>

<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('assets/js/pembaruan_keluarga.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Jalankan hanya kalau hash = #tab-anggota
        if (window.location.hash === '#tab-anggota') {
            // Tunggu sampai Bootstrap sudah siap
            const interval = setInterval(() => {
                const tabTrigger = document.querySelector('[data-bs-target="#tab-anggota"]');
                if (tabTrigger) {
                    clearInterval(interval);

                    // Aktifkan tab secara manual
                    const tab = new bootstrap.Tab(tabTrigger);
                    tab.show();

                    // Scroll halus ke tabel anggota
                    const tabContent = document.querySelector('#tab-anggota');
                    if (tabContent) {
                        setTimeout(() => {
                            tabContent.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 300);
                    }

                    // Bersihkan hash supaya tidak trigger ulang
                    history.replaceState(null, null, ' ');
                }
            }, 200);
        }
    });
</script>

<script>
    const isTambahMode = "<?= $sumber === 'baru' ? 'true' : 'false' ?>";
    // nilai baseUrl di-render langsung oleh PHP
    window.baseUrl = "<?= rtrim(base_url(), '/') ?>";
</script>

<script>
    const payload = <?= json_encode($payload ?? []) ?>;
    console.log('🚀 Payload dari PHP:', payload);

    const baseUrl = "<?= base_url() ?>";
</script>
<!-- JS Pembaruan Keluarga -->
<script src="<?= base_url('assets/js/pembaruan_keluarga.js'); ?>"></script>


<?= $this->endSection(); ?>