<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<?php
// 🚀 Tangkap Role ID User
$roleId = session()->get('role_id') ?? ($user['role_id'] ?? 99);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body {
        background: linear-gradient(135deg, #f8fbff, #e0f7fa);
        font-family: 'Poppins', sans-serif;
    }

    .hero-banner {
        background: linear-gradient(90deg, #4facfe, #00f2fe);
        color: #fff;
        padding: 2rem;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15);
    }

    .hero-banner h2 {
        font-weight: 700;
        margin-bottom: .5rem;
    }

    .hero-banner p {
        margin: 0;
        opacity: 0.9;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.2rem;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
    }

    .stat-icon {
        font-size: 1.8rem;
        margin-bottom: .5rem;
    }

    /* ✨ Gaya khusus untuk Card Menu Prioritas Dinamis */
    .priority-card {
        border-top: 4px solid #4facfe;
        background: linear-gradient(to bottom, #ffffff, #f8fbff);
    }

    .priority-card:hover {
        border-top: 4px solid #00f2fe;
    }

    .chart-box {
        background: white;
        border-radius: 15px;
        margin-top: 2rem;
        padding: 1.5rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .hero-banner {
            padding: 1.5rem;
            font-size: .9rem;
        }
    }
</style>

<div class="content-wrapper mt-1 p-4">
    <div class="hero-banner">
        <h2>✨ Assalamualaikum, Selamat <?= Salam(); ?>,</h2>
        <h2><?= ucwords(strtolower(session()->get('fullname'))); ?></h2>

        <?php if ($roleId == 6): ?>
            <small>Terima kasih telah bergabung sebagai Petugas Sensus Ekonomi 2026.</small>
        <?php else: ?>
            <small>Terima kasih telah berkontribusi dalam memperbarui data kesejahteraan masyarakat desa.</small>
        <?php endif; ?>
    </div>

    <?php if ($roleId == 6): ?>

        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px;">
                    <i class="fas fa-bullhorn me-2"></i> Selamat datang Petugas Sensus Ekonomi 2026. Silakan klik menu di bawah untuk mulai melakukan pencocokan data berdasarkan Nomor KK warga.
                </div>
            </div>
        </div>

        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 300px)); justify-content: center;">
            <div class="stat-card" style="border-top: 4px solid #00f2fe;" onclick="window.location='<?= base_url('sensus-ekonomi') ?>'">
                <div class="stat-icon text-primary"><i class="fas fa-search-location"></i></div>
                <h6 class="fw-bold">Validasi Data Keluarga</h6>
                <h3><?= number_format($totalKK ?? 0, 0, ',', '.'); ?> <small class="text-muted" style="font-size: 1rem;">KK</small></h3>
                <div class="mt-2 text-primary small">
                    Mulai Pencarian No. KK <i class="fas fa-arrow-circle-right ms-1"></i>
                </div>
            </div>
        </div>

    <?php else: ?>

        <?php if (isset($total_anomali_tugas) && $total_anomali_tugas > 0): ?>
            <div class="alert alert-warning shadow-sm my-2 border-0" style="border-radius: 12px;">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning-dark"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1 text-dark">Tugas Baru: Perbaikan Data Anomali</h5>
                        <p class="mb-0 text-dark">
                            Terdapat <strong><?= $total_anomali_tugas ?> KPM</strong> di wilayah Anda yang mengalami ketidakpadanan data Dukcapil.
                            Silakan lakukan koordinasi, lengkapi data, dan unggah Foto KK terbaru.
                        </p>
                    </div>
                    <div class="ms-3">
                        <a href="<?= base_url('verval/anomali') ?>" class="btn btn-warning fw-bold px-4 shadow-sm text-dark" style="border-radius: 8px;">
                            <i class="fas fa-edit"></i> Tindak Lanjuti
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($menu_pemulihan) && $total_masalah > 0): ?>
            <div class="alert alert-danger shadow-sm my-2 border-0">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="<?= esc($menu_pemulihan['tm_icon']) ?> fa-3x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">Aksi Diperlukan: Pemulihan Data Wilayah</h5>
                        <p class="mb-0">
                            Ditemukan <strong><?= $total_masalah ?> keluarga</strong> dengan format RT/RW tidak standar (kosong atau kurang dari 3 digit).
                            Hal ini menyebabkan data tidak muncul di dashboard petugas.
                        </p>
                    </div>
                    <div class="ms-3">
                        <a href="<?= base_url($menu_pemulihan['tm_url']) ?>" class="btn btn-light fw-bold px-4">
                            Perbaiki Sekarang
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($menu_prioritas)): ?>
            <?php
            // ✨ HITUNG JUMLAH MENU
            $jmlMenu = count($menu_prioritas);

            // ✨ LOGIKA KELAS DINAMIS (TANPA WHITESPACE DI DESKTOP)
            if ($jmlMenu == 1) {
                $colClass = 'col-12'; // 1 Menu: Full layar HP & Desktop
            } elseif ($jmlMenu == 2) {
                $colClass = 'col-6';  // 2 Menu: Dibagi 2 rata di HP & Desktop (50:50)
            } else {
                $colClass = 'col-6 col-md-4'; // >= 3 Menu: Di HP bagi 2, di Desktop maksimal 3 sejajar
            }
            ?>

            <h5 class="mt-4 mb-2 fw-bold text-secondary"><i class="fas fa-bolt text-warning"></i> Menu Prioritas & Akses Cepat</h5>

            <div class="row mt-0">
                <?php foreach ($menu_prioritas as $mp): ?>
                    <?php
                    // ✨ LOGIKA PENGGABUNGAN NAMA: "Parent Child"
                    $judulMenu = !empty($mp['parent_nama'])
                        ? $mp['parent_nama'] . ' ' . $mp['tm_nama']
                        : $mp['tm_nama'];
                    ?>
                    <div class="<?= $colClass ?> mb-3">
                        <div class="stat-card priority-card shadow-sm h-100" onclick="window.location='<?= base_url($mp['tm_url']) ?>'">
                            <div class="stat-icon text-info"><i class="<?= esc($mp['tm_icon']) ?>"></i></div>
                            <h6 class="fw-bold mt-2 text-dark"><?= esc(strtoupper($judulMenu)) ?></h6>
                            <small class="text-muted d-block mt-1">Buka Modul</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card" onclick="window.location='/dtsen-se'">
                <div class="stat-icon text-primary"><i class="fas fa-users"></i></div>
                <h6>Daftar Keluarga</h6>
                <h3><?= number_format($totalKK ?? 0, 0, ',', '.'); ?></h3>
            </div>

            <div class="stat-card" onclick="window.location='/usulan-bansos'">
                <div class="stat-icon text-success"><i class="fas fa-hand-holding-heart"></i></div>
                <h6>Usulan Bansos Bulan Ini</h6>
                <h3><?= number_format($totalUsulan ?? 0, 0, ',', '.'); ?></h3>
            </div>

            <div class="stat-card" onclick="window.location='/pembaruan-keluarga/draft'">
                <div class="stat-icon text-warning"><i class="fas fa-clipboard-list"></i></div>
                <h6>Draft Pembaruan</h6>
                <h3><?= number_format($totalDraft ?? 0, 0, ',', '.'); ?></h3>
            </div>

            <div class="stat-card" onclick="window.location='/pembaruan-keluarga/submitted'">
                <div class="stat-icon text-success"><i class="fas fa-file-upload"></i></div>
                <h6>Submitted Pembaruan</h6>
                <h3><?= number_format($totalSubmitted ?? 0, 0, ',', '.'); ?></h3>
            </div>
        </div>

    <?php endif; ?>

    <div class="chart-box">
        <h5 class="mb-3"><i class="fas fa-chart-bar text-info"></i> Jumlah Keluarga Berdasarkan Kategori Desil</h5>
        <canvas id="chartDesil" height="100"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ✨ Notifikasi SweetAlert saat login sukses
        <?php if (session()->getFlashdata('login_success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Selamat datang kembali!',
                html: '<b><?= ucwords(strtolower(session()->get('fullname'))); ?></b>',
                timer: 2500,
                showConfirmButton: false
            });
        <?php endif; ?>

        // 🚀 Render Chart hanya jika BUKAN role 6 (karena elemen canvas tidak dirender untuk role 6)
        // Ambil data chart dari PHP
        const desilLabels = [
            <?php foreach ($dataDesil ?? [] as $d): ?> '<?= $d->kategori_desil; ?>', <?php endforeach; ?>
        ];
        const desilData = [
            <?php foreach ($dataDesil ?? [] as $d): ?> <?= $d->jumlah; ?>, <?php endforeach; ?>
        ];

        const chartCanvas = document.getElementById('chartDesil');
        if (chartCanvas) {
            new Chart(chartCanvas, {
                type: 'bar',
                data: {
                    labels: desilLabels,
                    datasets: [{
                        label: 'Jumlah Keluarga',
                        data: desilData,
                        backgroundColor: 'rgba(63, 81, 181, 0.4)',
                        borderColor: 'rgba(63, 81, 181, 1)',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#3f51b5',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>

<?= $this->endSection(); ?>