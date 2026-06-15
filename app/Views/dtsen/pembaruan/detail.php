<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<?php
// Tangkap Role ID User yang sedang login
$roleId = session()->get('role_id') ?? 0;
?>
<?php if ($roleId == 6): ?>
    <style>
        /* Sembunyikan tombol-tombol aksi simpan/edit */
        form button[type="submit"],
        form .btn-primary:not(.btnEditAnggota),
        form .btn-success,
        form .btn-danger,
        form .btn-warning,
        #btnGetLocation,
        #btnApply {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Kunci semua input secara bedah HANYA yang ada di dalam <form>.
            // Ini membebaskan DataTables (yang tidak di dalam form) agar tombolnya bisa diklik normal!
            function lockAllInputs() {
                document.querySelectorAll('form input, form select, form textarea').forEach(function(el) {
                    el.disabled = true;
                    el.style.backgroundColor = '#f8f9fa'; // Beri efek abu-abu tanda terkunci
                });
                // Matikan event klik pada checkbox dan radio button
                document.querySelectorAll('form input[type="radio"], form input[type="checkbox"]').forEach(function(el) {
                    el.style.pointerEvents = 'none';
                });
            }

            // Jalankan penguncian saat halaman dimuat
            lockAllInputs();

            // 2. Kunci otomatis saat modal terbuka atau tab dipindah
            $(document).on('shown.bs.modal', '#modalAnggota', lockAllInputs);
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', lockAllInputs);

            // 3. Jaga-jaga: Lawan script bawaan yang mencoba membuka kunci (misal: saat toggle Status Hamil)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === "disabled") {
                        if (mutation.target.disabled === false && mutation.target.closest('form')) {
                            mutation.target.disabled = true; // Paksa kunci kembali!
                        }
                    }
                });
            });

            // Pantau semua form di dalam halaman
            document.querySelectorAll('form').forEach(function(form) {
                observer.observe(form, {
                    attributes: true,
                    subtree: true
                });
            });
        });
    </script>
<?php endif; ?>

<div class="content-wrapper mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">👨‍👩‍👧 Pembaruan Data Keluarga</h4>

        <a href="<?= ($roleId == 6) ? base_url('sensus-ekonomi') : base_url('dtsen-se') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> <?= ($roleId == 6) ? 'Kembali ke Pencarian' : 'Kembali ke Daftar Keluarga' ?>
        </a>
    </div>

    <?php if ($roleId == 6): ?>
        <div class="alert alert-warning shadow-sm border-0 mb-3 py-2 px-3" style="font-size: 0.95rem;">
            <i class="fas fa-eye me-2"></i> <b>Mode Lihat (Read-Only)</b> — Anda masuk sebagai Petugas Sensus Ekonomi 2026. Seluruh data dikunci dan tidak dapat diubah.
        </div>
    <?php endif; ?>

    <section class="content">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="fw-bold mb-0">🗂️ Detail Pembaruan Data Keluarga</h4>

                    <div class="d-flex justify-content-end align-items-center flex-wrap gap-1 mt-2">

                        <?php
                        // Pastikan huruf kecil semua agar tidak case-sensitive
                        $usulanStatus = strtolower(trim($usulan['status'] ?? ''));
                        $isReady = (int) ($is_submitted_ready ?? 0);
                        ?>

                        <?php if (!$usulanStatus || $sumber === 'utama'): ?>
                            <span class="badge bg-secondary px-2 py-1 small">Belum Ada Pembaruan</span>

                        <?php elseif ($usulanStatus === 'draft'): ?>
                            <?php if ($isReady === 1): ?>
                                <span class="badge bg-info text-dark px-2 py-1 small">Submitted</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark px-2 py-1 small">Draft</span>
                            <?php endif; ?>

                        <?php elseif ($usulanStatus === 'submitted'): ?>
                            <span class="badge bg-info text-dark px-2 py-1 small">Submitted</span>

                        <?php elseif ($usulanStatus === 'verified' || $usulanStatus === 'diverifikasi'): ?>
                            <span class="badge bg-primary px-2 py-1 small">Verified</span>

                        <?php else: ?>
                            <span class="badge bg-secondary px-2 py-1 small">Belum Ada Pembaruan</span>
                        <?php endif; ?>

                        <?php if (!empty($kategori_desil)): ?>
                            <span class="badge 
                            <?php
                            if ($kategori_desil <= 3) echo 'bg-success';
                            elseif ($kategori_desil <= 5) echo 'bg-warning text-dark';
                            else echo 'bg-danger';
                            ?>
                            px-2 py-1 small">
                                Desil <span class="badge bg-light text-dark"><?= $kategori_desil ?></span>
                            </span>
                        <?php endif; ?>

                        <?php if ($user['role_id'] <= 3): ?>
                            <button id="btnApply"
                                class="btn btn-outline-dark btn-sm shadow-sm px-3 py-1"
                                data-usulan-id="<?= esc($usulan['id'] ?? $payload['id'] ?? '') ?>">
                                <i class="fas fa-check-circle"></i> Terapkan Data
                            </button>
                        <?php endif; ?>

                    </div>

                </div>
            </div>

            <ul class="nav nav-tabs" id="pembaruanTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabKeluarga" role="tab">Data Keluarga</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-anggota" role="tab">Anggota</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabRumah" role="tab">Rumah</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabAset" role="tab">Aset</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabFoto" role="tab">Foto & Geotag</a></li>
            </ul>


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

            <?php if ($roleId == 6): ?>

                <style>
                    .read-only-mode button[type="submit"],
                    .read-only-mode button[type="button"],
                    .read-only-mode .btn-primary,
                    .read-only-mode .btn-success,
                    .read-only-mode .btn-danger,
                    .read-only-mode .btn-warning {
                        display: none !important;
                    }

                    /* Tampilkan kembali tombol navigasi tab jika tidak sengaja tersembunyi */
                    .nav-tabs .nav-link {
                        display: block !important;
                    }
                </style>
            <?php endif; ?>

        </div>
    </section>
</div>

<script>
    window.baseUrl = "<?= rtrim(base_url(), '/') ?>";
    const isTambahMode = "<?= $sumber === 'baru' ? 'true' : 'false' ?>";
    const payload = <?= json_encode($payload ?? []) ?>;

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

    document.querySelector('form')?.addEventListener('submit', function() {
        document.querySelectorAll('.rupiah').forEach(function(el) {
            el.value = el.value.replace(/\./g, '').replace(/,/g, '');
        });
    });
</script>

<script src="/assets/vendor/browser-image-compression.js"></script>

<script src="<?= base_url('assets/js/pembaruan_keluarga.js'); ?>"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.toLowerCase();
        if (hash === '#tab-anggota' || hash === '#tabanggota') {
            const interval = setInterval(() => {
                const tabTrigger = document.querySelector('[href="#tab-anggota"], [data-bs-target="#tab-anggota"]');
                if (tabTrigger) {
                    clearInterval(interval);
                    const tab = new bootstrap.Tab(tabTrigger);
                    tab.show();

                    const targetSection = document.querySelector('#tab-anggota');
                    if (targetSection) {
                        setTimeout(() => {
                            targetSection.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 400);
                    }
                    history.replaceState(null, null, ' ');
                }
            }, 300);
        }
    });
</script>

<?= $this->endSection(); ?>