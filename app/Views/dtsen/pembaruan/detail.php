<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<!-- 🚀 OBAT ANTI-CRASH JS: JQUERY WAJIB DILOAD PALING ATAS -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<?php
// Tangkap Data & Role dengan Aman agar PHP tidak Crash!
$roleId = session()->get('role_id') ?? 0;
$isEditableUser = ($roleId <= 4);

// Amankan semua variabel yang dibutuhkan oleh Offcanvas
$safeIdKk = $id_kk ?? $payload['id_kk'] ?? $usulan['id_kk'] ?? '';
$safeNoKk = $kkData['no_kk'] ?? $payload['perumahan']['no_kk'] ?? $perumahan['no_kk'] ?? '';
$safeNama = $kkData['kepala_keluarga'] ?? $payload['perumahan']['kepala_keluarga'] ?? $perumahan['kepala_keluarga'] ?? '';
$safeAlamat = $kkData['alamat'] ?? $payload['perumahan']['alamat'] ?? $perumahan['alamat'] ?? '';
$safeDesil = $kategori_desil ?? $payload['kategori_desil'] ?? '';
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

                        <?php elseif ($usulanStatus === 'draft' || $usulanStatus === 'submitted'): ?>
                            <!-- 🚀 SMART BADGE: Tampilan dikendalikan penuh oleh kelengkapan isi data -->
                            <?php if ($isReady === 1): ?>
                                <span class="badge bg-info text-dark px-2 py-1 small">Submitted</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark px-2 py-1 small">Draft</span>
                            <?php endif; ?>

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

                        <!-- 🚀 TAMBAHKAN CLASS btnOpenDesil -->
                        <button type="button" class="btn btn-primary btn-sm shadow-sm px-3 py-1 fw-bold btnOpenDesil" data-bs-toggle="offcanvas" data-bs-target="#offcanvasChartDesil">
                            <i class="fas fa-chart-line me-1"></i> Grafik Desil
                        </button>

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
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabRumah" role="tab">Rumah</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabFoto" role="tab">Foto</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabAset" role="tab">Aset</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-anggota" role="tab">Anggota</a></li>
            </ul>


            <div class="tab-content p-3">
                <div class="tab-pane fade show active" id="tabKeluarga" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_keluarga'); ?>
                </div>
                <div class="tab-pane fade" id="tabRumah" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_rumah'); ?>
                </div>
                <div class="tab-pane fade" id="tabFoto" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_foto'); ?>
                </div>
                <div class="tab-pane fade" id="tabAset" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_aset'); ?>
                </div>
                <div class="tab-pane fade" id="tab-anggota" role="tabpanel">
                    <?= $this->include('dtsen/pembaruan/tab_anggota'); ?>
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

<!-- ============================================================== -->
<!-- 🚀 ZONA MERDEKA: SEMUA OFFCANVAS & MODAL HARUS DI SINI -->
<!-- ============================================================== -->

<div class="offcanvas offcanvas-end shadow-lg" id="offcanvasChartDesil" aria-labelledby="offcanvasChartDesilLabel" style="width: 800px; max-width: 100vw;">
    <div class="offcanvas-header bg-primary text-white d-flex flex-column align-items-start pb-3" style="border-bottom: 3px solid #ffc107;">
        <div class="d-flex justify-content-between w-100 align-items-center mb-2">
            <div class="d-flex align-items-center">
                <img src="<?= function_exists('logoApp') ? logoApp() : base_url('assets/img/logo.png') ?>" alt="Logo" class="p-1 me-2" style="width: 45px; height: 45px; object-fit: contain;" onerror="this.outerHTML='<i class=\'fas fa-chart-pie fa-2x me-2 text-warning\'></i>'">
                <div>
                    <h5 class="offcanvas-title fw-bold mb-0" id="offcanvasChartDesilLabel" style="line-height: 1.2;">Riwayat Desil Keluarga</h5>
                    <small class="text-white-50" style="font-size: 0.75rem;"><?= function_exists('titleApp') ? titleApp() : 'SINDEN' ?></small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="mt-2 small w-100 bg-white bg-opacity-10 p-2 rounded">
            <div class="mb-1"><i class="fas fa-id-card me-2 text-warning"></i> No. KK: <strong class="text-black"><?= esc($safeNoKk) ?></strong></div>
            <div><i class="fas fa-user-circle me-2 text-warning"></i> Kepala Keluarga: <strong class="text-black"><?= esc($safeNama) ?></strong></div>
        </div>
    </div>
    <div class="offcanvas-body p-4 flex-grow-1">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3 pb-3 border-bottom">
            <?php if ($isEditableUser && $roleId <= 3): ?>
                <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold btnHistoricalDesil"><i class="fas fa-history me-1"></i> Tambah Snapshot</button>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold btnUpdateDesil" data-id="<?= esc($safeIdKk) ?>" data-nokk="<?= esc($safeNoKk) ?>" data-nama="<?= esc($safeNama) ?>" data-alamat="<?= esc($safeAlamat) ?>" data-desil="<?= esc($safeDesil) ?>"><i class="fas fa-hand-holding-heart me-1"></i> Update Desil</button>
                <button type="button" id="btnSyncDesil" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold"><i class="fas fa-sync-alt me-1"></i> Sync</button>
            <?php endif; ?>
        </div>
        <div id="desilChart" style="min-height:350px;"></div>
        <div id="desilTrendInfo" class="mt-3 small text-muted"></div>
    </div>
</div>

<div class="modal fade" id="modalHistoricalDesil" data-bs-focus="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Snapshot Historis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formHistoricalDesil">
                    <input type="hidden" name="id_kk" value="<?= esc($safeIdKk) ?>">
                    <!-- Tahun -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <select name="tahun" class="form-select" required>
                            <?php $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 2025; $year--): ?>
                                <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>><?= $year ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <!-- Triwulan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Periode Triwulan</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold text-primary">TW</span>
                            <input type="number" class="form-control" name="triwulan" id="inputTriwulan" step="0.1" min="1" max="4.9" placeholder="Contoh: 3 atau 3.1" required>
                        </div>
                    </div>
                    <!-- Desil -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Desil</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                <?php $warna = ($i == 0) ? 'secondary' : (($i <= 3) ? 'success' : (($i <= 5) ? 'warning' : 'danger')); ?>
                                <input type="radio" class="btn-check" name="desil" id="desil<?= $i ?>" value="<?= $i ?>" required>
                                <label class="btn btn-outline-<?= $warna ?> rounded-pill px-3" for="desil<?= $i ?>">Desil <?= $i ?></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" id="btnSaveHistorical" class="btn btn-primary">Simpan</button></div>
        </div>
    </div>
</div>
<!-- 🚀 PANGGIL MODAL DI LUAR TAB (Agar Bebas dari Jebakan Animasi/Duplikasi) -->
<?= $this->include('dtsen/pembaruan/modal_anggota') ?>
<?= $this->include('dtsen/se/modal_input_desil') ?>

<script src="/assets/vendor/browser-image-compression.js"></script>
<script src="<?= base_url('assets/js/pembaruan_keluarga.js'); ?>"></script>

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


    // 🚀 Render grafik saat Offcanvas terbuka
    document.getElementById('offcanvasChartDesil').addEventListener('shown.bs.offcanvas', function() {
        loadDesilChart();
    });

    // ==============================================================
    // 🚀 FUNGSI RE-RENDER GRAFIK (AUTO-UPDATE TANPA RELOAD PAGE)
    // ==============================================================
    window.reloadDesilChart = function() {
        if (desilChartInstance !== null) {
            desilChartInstance.destroy(); // Hancurkan canvas grafik lama
            desilChartInstance = null;
        }
        loadDesilChart(); // Tarik data baru dari database dan gambar ulang
    };

    // ==============================================================
    // 🚀 PENGENDALI MODAL (TIDAK MENUTUP OFFCANVAS)
    // ==============================================================
    $(document).on('click', '.btnUpdateDesil', function(e) {
        e.preventDefault();
        const btn = $(this);
        const modalDesil = $('#modalInputDesil');

        modalDesil.find('#modal_id_kk').val(btn.attr('data-id'));
        modalDesil.find('#modal_no_kk').val(btn.attr('data-nokk'));
        modalDesil.find('#modal_kepala_keluarga').val(btn.attr('data-nama'));
        modalDesil.find('#modal_alamat').val(btn.attr('data-alamat'));
        modalDesil.find('#kategori_desil').val(btn.attr('data-desil'));

        // LANGSUNG BUKA MODAL TANPA MENUTUP OFFCANVAS
        modalDesil.modal('show');
    });

    $(document).on('click', '.btnHistoricalDesil', function(e) {
        e.preventDefault();
        // LANGSUNG BUKA MODAL TANPA MENUTUP OFFCANVAS
        $('#modalHistoricalDesil').modal('show');
    });

    // ==============================================================
    // 🚀 UPDATE OTOMATIS SAAT TOMBOL SYNC DIKLIK
    // ==============================================================
    document.getElementById('btnSyncDesil')?.addEventListener('click', function() {
        const btn = this;
        const idKK = <?= (int)$id_kk ?>;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sync...';

        fetch("<?= base_url('pembaruan-keluarga/sync-desil') ?>/" + idKK, {
                method: "POST"
            })
            .then(res => res.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync';

                if (res.status === 'changed') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Desil Berubah',
                        html: `Dari <b>${res.from ?? '-'}</b> menjadi <b>${res.to}</b>`,
                        timer: 1800,
                        showConfirmButton: false
                    });

                    // PANGGIL FUNGSI RE-RENDER! (Menggantikan location.reload)
                    reloadDesilChart();
                } else if (res.status === 'unchanged') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tetap',
                        text: 'Desil tidak berubah.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message
                    });
                }
            });
    });
</script>

<script>
    let desilChartInstance = null;

    function loadDesilChart() {

        console.log("LOAD DESIL CHART DIPANGGIL");

        const chartEl = document.querySelector("#desilChart");
        if (!chartEl) {
            console.log("desilChart element tidak ditemukan");
            return;
        }

        if (desilChartInstance !== null) return;

        const idKK = <?= (int)$id_kk ?>;

        // 🚀 UBAH BARIS INI (URL Dinamis):
        fetch("<?= base_url($roleId == 6 ? 'sensus-ekonomi/desil-history' : 'pembaruan-keluarga/desil-history') ?>/" + idKK)
            .then(res => res.json())
            .then(res => {

                console.log("Response:", res);

                if (res.status !== 'success' || res.data.length === 0) {
                    chartEl.innerHTML = '<div class="text-center text-muted py-5">Belum ada histori desil.</div>';
                    return;
                }

                const data = res.data;
                const categories = data.map(d => d.periode);
                const values = data.map(d => d.desil);

                const options = {
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    series: [{
                        name: 'Desil',
                        data: values
                    }],
                    xaxis: {
                        categories: categories
                    },
                    yaxis: {
                        // 🚀 PERBAIKAN: Ubah min jadi 0 dan tickAmount jadi 10
                        min: 0,
                        max: 10,
                        tickAmount: 10
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 6,
                        hover: {
                            size: 8
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "Desil " + val;
                            }
                        }
                    },
                    colors: ['#0d6efd'],
                    grid: {
                        borderColor: '#e9ecef'
                    }
                };

                desilChartInstance = new ApexCharts(chartEl, options);
                desilChartInstance.render();
            });
    }

    // 🚀 Sabuk Pengaman: Perbaiki scrolling halaman jika modal bertumpuk ditutup
    document.addEventListener('hidden.bs.modal', function(event) {
        if (document.querySelectorAll('.modal.show').length > 0) {
            document.body.classList.add('modal-open');
        }
    });

    document.addEventListener('shown.bs.tab', function(event) {
        if (event.target.getAttribute('data-bs-target') === '#tabRumah') {

            $('#rumah_provinsi, #rumah_regency, #rumah_district, #rumah_village').select2({
                width: '100%'
            });

        }
    });

    document.getElementById('btnSyncDesil')?.addEventListener('click', function() {

        const btn = this;
        const idKK = <?= (int)$id_kk ?>;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sync...';

        fetch("<?= base_url('pembaruan-keluarga/sync-desil') ?>/" + idKK, {
                method: "POST",
                credentials: "same-origin"
            })
            .then(res => res.json())
            .then(res => {

                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync';

                // ✅ SUCCESS - ADA PERUBAHAN
                if (res.status === 'changed') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Desil Berubah',
                        html: `Dari <b>${res.from ?? '-'}</b> menjadi <b>${res.to}</b><br><small>${res.periode}</small>`,
                        showConfirmButton: false,
                        timer: 1800,
                        timerProgressBar: true
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 1800);
                }

                // ℹ️ TIDAK BERUBAH
                else if (res.status === 'unchanged') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Perubahan',
                        text: 'Desil tetap sama.',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                }

                // ❌ ERROR DARI SERVER
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync';

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal melakukan sinkronisasi.',
                    showConfirmButton: false,
                    timer: 2000
                });
            });

    });
</script>

<?= $this->endSection(); ?>