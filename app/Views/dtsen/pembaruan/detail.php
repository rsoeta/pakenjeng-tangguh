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
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="fw-bold mb-0">🗂️ Detail Pembaruan Data Keluarga</h4>

                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-1 mt-2">

                        <!-- BADGE STATUS -->
                        <?php $status = $usulan['status'] ?? $sumber ?? ''; ?>

                        <?php if (!empty($usulan['status']) && $usulan['status'] == 'draft'): ?>
                            <span class="badge bg-warning text-dark px-2 py-1 small">Draft</span>

                        <?php elseif ($status == $sumber): ?>
                            <span class="badge bg-success px-2 py-1 small">Baru</span>

                        <?php elseif (!empty($usulan['status'])): ?>
                            <span class="badge bg-primary px-2 py-1 small">Tervalidasi</span>
                        <?php endif; ?>

                        <!-- BADGE DESIL -->
                        <?php if (!empty($kategori_desil)): ?>
                            <span class="badge 
            <?php
                            if ($kategori_desil <= 2) echo 'bg-danger';
                            elseif ($kategori_desil <= 4) echo 'bg-warning text-dark';
                            else echo 'bg-success';
            ?>
            px-2 py-1 small">
                                Desil <span class="badge bg-light text-dark"><?= $kategori_desil ?></span>
                            </span>
                        <?php endif; ?>

                        <!-- APPLY BUTTON -->
                        <?php if ($user['role_id'] <= 3): ?>
                            <button id="btnApply"
                                class="btn btn-danger btn-sm shadow-sm px-3 py-1"
                                data-usulan-id="<?= esc($usulan['id'] ?? $payload['id'] ?? '') ?>">
                                <i class="fas fa-check-circle"></i> Terapkan Data
                            </button>
                        <?php endif; ?>

                    </div>

                </div>
            </div>

            <!-- Nav Tabs -->
            <ul class="nav nav-tabs" id="pembaruanTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabKeluarga" role="tab">Data Keluarga</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-anggota" role="tab">Anggota</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabRumah" role="tab">Rumah</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabAset" role="tab">Aset</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabFoto" role="tab">Foto & Geotag</a></li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content p-3">
                <div class="tab-pane fade show active" id="tabKeluarga" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_keluarga'); ?>
                </div>
                <div class="tab-pane fade" id="tab-anggota" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_anggota'); ?>
                </div>
                <div class="tab-pane fade" id="tabRumah" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_rumah'); ?>
                </div>
                <div class="tab-pane fade" id="tabAset" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_aset'); ?>
                </div>
                <div class="tab-pane fade" id="tabFoto" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_foto'); ?>
                </div>
            </div>
        </div>
</div>

</section>
</div>

<!-- Variabel global -->
<script>
    window.baseUrl = "<?= rtrim(base_url(), '/') ?>";
    const isTambahMode = "<?= $sumber === 'baru' ? 'true' : 'false' ?>";
    const payload = <?= json_encode($payload ?? []) ?>;
    // console.log('🚀 Payload dari PHP:', payload);

    // Auto Uppercase
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('upper')) {
            e.target.value = e.target.value.toUpperCase();
        }
    });

    // Only numbers
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('onlynum')) {
            e.target.value = e.target.value.replace(/\D/g, '');
        }
    });

    // =========================
    // Validasi Angka 16 Digit
    // =========================
    document.querySelectorAll('.onlynum16').forEach(input => {

        // Saat mengetik — hanya angka
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, ''); // hapus non-digit

            if (this.value.length > 16) {
                this.value = this.value.slice(0, 16); // batasi 16 digit
            }
        });

        // Saat keluar dari input — cek panjang
        input.addEventListener('blur', function() {
            if (this.value.length !== 16) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });

    // Format ribuan untuk rupiah
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('rupiah')) {
            let value = e.target.value.replace(/\D/g, ''); // hanya angka
            e.target.value = new Intl.NumberFormat('id-ID').format(value);
        }
    });

    document.querySelector('form').addEventListener('submit', function() {
        document.querySelectorAll('.rupiah').forEach(function(el) {
            el.value = el.value.replace(/\./g, '').replace(/,/g, '');
        });
    });
</script>

<!-- Script utama -->
<script src="<?= base_url('assets/js/pembaruan_keluarga.js'); ?>"></script>

<!-- =======================================================
     🔁 Aktifkan Tab Anggota Otomatis Setelah Reload
     ======================================================= -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 🔹 Normalisasi hash ke lowercase
        const hash = window.location.hash.toLowerCase();
        if (hash === '#tab-anggota' || hash === '#tabanggota') {
            const interval = setInterval(() => {
                // Cari elemen trigger (baik href maupun data-bs-target)
                const tabTrigger = document.querySelector('[href="#tab-anggota"], [data-bs-target="#tab-anggota"]');
                if (tabTrigger) {
                    clearInterval(interval);

                    // Aktifkan tab via Bootstrap API
                    const tab = new bootstrap.Tab(tabTrigger);
                    tab.show();

                    // Scroll halus ke tab anggota
                    const targetSection = document.querySelector('#tab-anggota');
                    if (targetSection) {
                        setTimeout(() => {
                            targetSection.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 400);
                    }

                    // Hapus hash agar tidak trigger ulang
                    history.replaceState(null, null, ' ');
                }
            }, 300);
        }
    });
</script>

<?= $this->endSection(); ?>