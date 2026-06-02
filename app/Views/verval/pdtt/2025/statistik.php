<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <section class="content">


        <div class="container-fluid pt-3">
            <div class="card-tools mb-2" style="display: flex; justify-content: flex-end;">
                <a href="<?= base_url('pdtt/2025') ?>" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card shadow-sm border-top border-info">
                <div class="card-header bg-white">
                    <h3 class="card-title fw-bold mt-1">
                        <i class="fas fa-chart-bar text-info"></i> Capaian Kinerja Petugas Entri pada PDTT 2025
                    </h3>
                </div>
                <div class="card-body">
                    <div style="position: relative; max-height: 80vh; overflow-y: auto; overflow-x: hidden; border-radius: 4px;">
                        <div id="dynamicChartWrapper" style="position: relative; width: 100%;">
                            <canvas id="capaianChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Pastikan Chart.js sudah di-load di layout utama, jika belum, uncomment baris di bawah -->
<script src="<?= base_url('assets/plugins/chart.js/Chart.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>

<script>
    $(function() {
        // 1. Ambil Data JSON dari Controller
        var rawData = <?= $statistik; ?>;

        // 2. Siapkan Array untuk Chart
        var labels = [];
        var dataSelesai = [];
        var dataPending = [];

        // 3. Looping data untuk dimasukkan ke array
        rawData.forEach(function(item) {
            labels.push(item.nama);
            dataSelesai.push(item.selesai);
            dataPending.push(item.pending);
            // Set font global Chart.js ke Ubuntu
            Chart.defaults.global.defaultFontFamily = "'Ubuntu', 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif";
        });

        // 🚀 LOGIKA TINGGI DINAMIS (ANTI-BERHIMPITAN)
        var tinggiPerPetugas = 20; // Jatah tinggi 60px untuk masing-masing petugas
        var tinggiTotal = labels.length * tinggiPerPetugas;

        // Set batas minimal tinggi (misal jika petugasnya cuma 1 atau 2 orang, grafik tidak terlalu gepeng)
        if (tinggiTotal < 350) {
            tinggiTotal = 350;
        }

        // Suntikkan tinggi yang sudah dihitung ke dalam wrapper canvas
        $('#dynamicChartWrapper').css('height', tinggiTotal + 'px');

        // 4. Konfigurasi Chart.js
        var ctx = $('#capaianChart').get(0).getContext('2d');

        var chartData = {
            labels: labels,
            datasets: [{
                    label: 'Selesai / Verified',
                    backgroundColor: 'rgba(40, 167, 69, 0.9)', // Warna Hijau
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1,
                    data: dataSelesai
                },
                {
                    label: 'Pending / Draft',
                    backgroundColor: 'rgba(255, 193, 7, 0.9)', // Warna Kuning
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1,
                    data: dataPending
                }
            ]
        };

        var chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    stacked: true,
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    }
                }],
                yAxes: [{
                    stacked: true,
                    gridLines: {
                        display: false
                    },
                    categoryPercentage: 0.8, // Kembalikan ke ukuran ideal
                    barPercentage: 0.8 // Kembalikan ke ukuran ideal
                }]
            },
            legend: {
                display: true,
                position: 'top',
                labels: {
                    fontColor: '#333',
                    fontSize: 9,
                    boxWidth: 20
                }
            },
            plugins: {
                datalabels: {
                    color: '#ffffff',
                    font: {
                        family: "'Ubuntu', sans-serif", // 🚀 KUNCI: Set font angka ke Ubuntu
                        weight: 'bold',
                        size: 10
                    },
                    formatter: function(value, context) {
                        return value > 0 ? value : '';
                    }
                }
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    footer: function(tooltipItems, data) {
                        let total = 0;
                        tooltipItems.forEach(function(tooltipItem) {
                            total += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                        });
                        return 'Total Beban Tugas: ' + total;
                    }
                }
            }
        };

        // 5. Render Horizontal Bar Chart
        new Chart(ctx, {
            type: 'horizontalBar', // 🚀 KUNCI UTAMA: Ubah menjadi horizontalBar
            data: chartData,
            options: chartOptions
        });
    });
</script>

<?= $this->endSection(); ?>