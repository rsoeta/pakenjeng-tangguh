<?= $this->extend('templates/index') ?>

<?= $this->section('content') ?>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="mb-0 text-primary fw-bold"><i class="fas fa-chart-pie me-2"></i> Statistik PBI-JKN</h4>
                    <p class="text-muted">Proporsi dan visualisasi data kepesertaan secara real-time.</p>
                </div>
            </div>

            <div class="row">
                <!-- 🥧 KANVAS 1: STATUS KEPESERTAAN -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-top-primary h-100">
                        <div class="card-header bg-white pb-0 border-0">
                            <h6 class="fw-bold text-center">Proporsi Status Kepesertaan</h6>
                        </div>
                        <div class="card-body">
                            <!-- 🚀 Hapus max-width, gunakan width 100% dan height statis -->
                            <div style="position: relative; width: 100%; height: 350px;">
                                <canvas id="canvasStatus"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🍩 KANVAS 2: ALASAN NON-AKTIF -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm border-top-danger h-100">
                        <div class="card-header bg-white pb-0 border-0">
                            <h6 class="fw-bold text-center">Proporsi Alasan Non-Aktif</h6>
                        </div>
                        <div class="card-body">
                            <!-- 🚀 Hapus max-width, gunakan width 100% dan height statis -->
                            <div style="position: relative; width: 100%; height: 350px;">
                                <canvas id="canvasAlasan"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🚀 Panggil CDN Chart.js Utama -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- 🚀 Panggil CDN Plugin DataLabels (Untuk menampilkan teks di dalam irisan Pie) -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
    $(document).ready(function() {

        Swal.fire({
            title: 'Memuat Grafik...',
            allowOutsideClick: false,
            customClass: {
                popup: 'swal-sm'
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= site_url('pbi/statistik/get_data') ?>',
            type: 'POST',
            success: function(res) {
                Swal.close();
                if (res.status) {
                    renderChartStatus(res.chartStatus);
                    renderChartAlasan(res.chartAlasan);
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data statistik.', 'error');
            }
        });

        // 🥧 GRAFIK 1: STATUS KEPESERTAAN
        function renderChartStatus(dataAPI) {
            const ctx = document.getElementById('canvasStatus').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: dataAPI.labels,
                    datasets: [{
                        data: dataAPI.data,
                        backgroundColor: dataAPI.colors,
                        borderWidth: 1,
                        hoverOffset: 4
                    }]
                },
                // Aktifkan plugin datalabels khusus untuk grafik ini
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // 🔥 Tambahkan baris ini
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 14
                            },
                            formatter: (value) => {
                                return value;
                            } // Tampilkan jumlah data murni
                        }
                    }
                }
            });
        }

        // 🍩 GRAFIK 2: ALASAN NON-AKTIF (SUDAH DI-UPGRADE)
        function renderChartAlasan(dataAPI) {
            const ctx = document.getElementById('canvasAlasan').getContext('2d');

            // 1. Hitung Total Data Keseluruhan untuk mencari persentase
            let totalData = dataAPI.data.reduce((a, b) => parseInt(a) + parseInt(b), 0);

            // 2. Modifikasi Label agar otomatis mengandung Persentase (Contoh: "Meninggal: 30%")
            let customLabels = dataAPI.labels.map((label, index) => {
                let val = dataAPI.data[index];
                let percentage = totalData > 0 ? Math.round((val / totalData) * 100) : 0;
                return `${label}: ${percentage}%`;
            });

            const colors = [
                '#fd7e14', '#ffc107', '#6f42c1', '#0dcaf0',
                '#dc3545', '#6c757d', '#20c997', '#0d6efd'
            ];

            new Chart(ctx, {
                type: 'pie', // Diubah jadi pie penuh agar mirip referensi gambar kedua
                data: {
                    labels: customLabels, // Gunakan label yang sudah disisipi persentase
                    datasets: [{
                        data: dataAPI.data,
                        backgroundColor: colors,
                        borderWidth: 1,
                        hoverOffset: 4
                    }]
                },
                // Aktifkan plugin datalabels
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // 🔥 Tambahkan baris ini
                    layout: {
                        padding: 10
                    },
                    plugins: {
                        legend: {
                            position: 'right', // Geser legenda ke sebelah kanan
                            labels: {
                                font: {
                                    size: 11
                                },
                                boxWidth: 15 // Perkecil kotak warna legenda agar rapi
                            }
                        },
                        datalabels: {
                            color: '#ffffff', // Warna teks putih di dalam irisan
                            font: {
                                weight: 'bold',
                                size: 13
                            },
                            formatter: function(value, context) {
                                // Hitung total dulu
                                let total = context.chart._metasets[context.datasetIndex].total;
                                let percentage = (value / total) * 100;
                                // 🔥 Sembunyikan angka jika irisan kurang dari 3% agar tidak tumpang tindih
                                return percentage > 3 ? value : '';
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    // Tampilan saat kursor diarahkan ke irisan
                                    let value = context.raw || 0;
                                    return ` Jumlah: ${value} Jiwa`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>