<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

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

<div class="content-wrapper p-4">
    <!-- Hero -->
    <div class="hero-banner">
        <h2>👋 Assalamualaikum, Selamat <?= Salam(); ?>,</h2>
        <h2><?= ucwords(strtolower(session()->get('fullname'))); ?></h2>
        <small>Terima kasih telah berkontribusi dalam memperbarui data kesejahteraan masyarakat desa.</small>
    </div>

    <!-- Kartu Statistik -->
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

        <div class="stat-card" onclick="window.location='/dtsen-se'">
            <div class="stat-icon text-warning"><i class="fas fa-clipboard-list"></i></div>
            <h6>Draft Pembaruan</h6>
            <h3><?= number_format($totalDraft ?? 0, 0, ',', '.'); ?></h3>
        </div>

        <div class="stat-card" onclick="window.location='/lain-lain'">
            <div class="stat-icon text-secondary"><i class="fas fa-ellipsis-h"></i></div>
            <h6>Lain-lain</h6>
            <h3>-</h3>
        </div>
    </div>

    <!-- Diagram -->
    <div class="chart-box">
        <h5 class="mb-3"><i class="fas fa-chart-bar text-info"></i> Jumlah Keluarga Berdasarkan Kategori Desil</h5>
        <canvas id="chartDesil" height="100"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 🔔 Notifikasi SweetAlert saat login sukses
        <?php if (session()->getFlashdata('login_success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Selamat datang kembali!',
                html: '<b><?= ucwords(strtolower(session()->get('fullname'))); ?></b>',
                timer: 2500,
                showConfirmButton: false
            });
        <?php endif; ?>

        // Ambil data chart dari PHP
        const desilLabels = [
            <?php foreach ($dataDesil as $d): ?> '<?= $d->kategori_desil; ?>', <?php endforeach; ?>
        ];
        const desilData = [
            <?php foreach ($dataDesil as $d): ?> <?= $d->jumlah; ?>, <?php endforeach; ?>
        ];

        new Chart(document.getElementById('chartDesil'), {
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
    });
</script>

<?= $this->endSection(); ?>