<?php
$roleId = $user['role_id'] ?? 99;
$editable = ($roleId <= 4);
$disabled = $editable ? '' : 'disabled';
?>

<div class="p-3">

    <!-- ============================= -->
    <!-- 🏠 CARD DATA KELUARGA -->
    <!-- ============================= -->

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <h6 class="fw-bold mb-3">Identitas Keluarga</h6>

            <form id="formDataKeluarga" class="needs-validation" novalidate>
                <input type="hidden" id="id_kk" name="id_kk" value="<?= $id_kk ?>">
                <input type="hidden" id="sumber" name="sumber" value="<?= $sumber ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="keluarga_no_kk" class="form-label fw-semibold">Nomor Kartu Keluarga</label>
                        <input type="text" class="form-control onlynum16"
                            id="keluarga_no_kk" name="no_kk"
                            value="<?= esc($perumahan['no_kk'] ?? '') ?>"
                            <?= $disabled ?>
                            maxlength="16" minlength="16">
                    </div>
                    <div class="col-md-6">
                        <label for="kepala_keluarga" class="form-label fw-semibold">Kepala Keluarga</label>
                        <input type="text" class="form-control upper"
                            id="kepala_keluarga" name="kepala_keluarga"
                            value="<?= esc($perumahan['kepala_keluarga'] ?? '') ?>"
                            <?= $disabled ?>>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alamat Domisili</label>
                    <div class="bg-light border rounded-3 p-3 small">
                        <div>
                            <strong>RW:</strong> <?= esc($perumahan['rw'] ?? '-') ?> |
                            <strong>RT:</strong> <?= esc($perumahan['rt'] ?? '-') ?> |
                            <?= esc($perumahan['alamat'] ?? '-') ?>
                        </div>
                    </div>
                </div>

                <?php if ($editable): ?>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning small mt-3">
                        <i class="fas fa-lock"></i>
                        Anda tidak memiliki hak untuk mengubah data keluarga ini.
                    </div>
                <?php endif; ?>

            </form>

        </div>
    </div>

    <!-- ============================= -->
    <!-- 📊 CARD GRAFIK RIWAYAT DESIL -->
    <!-- ============================= -->

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0">Grafik Riwayat Desil Keluarga</h5>
            <small class="text-muted">Monitoring perubahan kesejahteraan per triwulan</small>
        </div>

        <div class="d-flex align-items-center gap-2">

            <?php if ($editable): ?>
                <button type="button"
                    id="btnSyncDesil"
                    class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-sync-alt me-1"></i> Sync
                </button>
            <?php endif; ?>

        </div>

    </div>
    <div id="desilChart" style="min-height:320px;"></div>

    <div id="desilTrendInfo" class="mt-3 small text-muted"></div>

</div>

<!-- ============================= -->
<!-- 📊 APEXCHARTS SCRIPT -->
<!-- ============================= -->

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

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

        fetch("<?= base_url('pembaruan-keluarga/desil-history') ?>/" + idKK)
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
                        min: 1,
                        max: 10,
                        tickAmount: 9
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

    // Panggil langsung tanpa tunggu tab event
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(loadDesilChart, 300);
    });
    // Render saat tab keluarga aktif
    document.addEventListener('shown.bs.tab', function(event) {
        const targetId = event.target.getAttribute('data-bs-target');
        if (targetId === '#tabKeluarga') {
            loadDesilChart();
        }
    });

    // Jika tab keluarga sudah aktif saat load
    document.addEventListener("DOMContentLoaded", function() {
        const activeTab = document.querySelector('.nav-link.active');
        if (activeTab && activeTab.getAttribute('data-bs-target') === '#tabKeluarga') {
            loadDesilChart();
        }
    });

    document.addEventListener('shown.bs.tab', function(event) {
        if (event.target.getAttribute('data-bs-target') === '#tabRumah') {

            $('#rumah_provinsi, #rumah_regency, #rumah_district, #rumah_village').select2({
                width: '100%'
            });

        }
    });
</script>

<script>
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

                if (res.status === 'changed') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Desil Berubah',
                        html: `Dari <b>${res.from ?? '-'}</b> menjadi <b>${res.to}</b><br>${res.periode}`,
                    }).then(() => location.reload());
                } else if (res.status === 'unchanged') {
                    Swal.fire('Tidak Ada Perubahan', 'Desil tetap sama.', 'info');
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync';
                Swal.fire('Error', 'Gagal melakukan sinkronisasi.', 'error');
            });
    });
</script>