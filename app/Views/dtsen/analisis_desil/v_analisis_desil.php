<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .card-metric {
        border-radius: 12px;
        transition: transform 0.2s;
    }

    .card-metric:hover {
        transform: translateY(-5px);
    }

    .pulse-danger {
        animation: pulse-animation 2s infinite;
    }

    @keyframes pulse-animation {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
</style>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0 fw-bold"><i class="fas fa-chart-line text-primary mr-2"></i> <?= $title; ?></h4>
            <p class="text-muted">Analisis Pergerakan Desil SIKS-NG & Prediksi Kelayakan Bansos</p>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <!-- 🎛️ FILTER PANEL DINAMIS -->
            <div class="card shadow-sm border-top-primary mb-4">
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <label class="small fw-bold">RW</label>
                            <select id="filter_rw" class="form-control select2">
                                <option value="">-- Semua --</option>
                                <?php for ($i = 1; $i <= 15; $i++): ?>
                                    <option value="<?= $i ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="small fw-bold">RT</label>
                            <select id="filter_rt" class="form-control select2">
                                <option value="">-- Semua --</option>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="small fw-bold">Bandingkan Dari <span class="text-danger">*</span></label>
                            <select id="periode_awal" class="form-control select2">
                                <?php foreach ($periodes as $p): ?>
                                    <option value="<?= $p['periode_label'] ?>"><?= $p['periode_label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="small fw-bold">Bandingkan Ke <span class="text-danger">*</span></label>
                            <select id="periode_akhir" class="form-control select2">
                                <?php foreach ($periodes as $p): ?>
                                    <option value="<?= $p['periode_label'] ?>"><?= $p['periode_label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🃏 CARD SUMMARY PIVOT -->
            <div class="row mb-2">
                <div class="col-md-3 col-6 mb-3">
                    <div class="card card-metric bg-primary text-white shadow">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1"><i class="fas fa-users mr-1"></i> Total KK</h6>
                            <h3 class="font-weight-bold mb-0" id="sum-total">0</h3>
                            <small>Populasi Wilayah</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card card-metric bg-danger text-white shadow">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1"><i class="fas fa-arrow-up mr-1"></i> Naik Desil</h6>
                            <h3 class="font-weight-bold mb-0"><span id="sum-naik">0</span> <span class="fs-6 fw-normal" id="pct-naik">(0%)</span></h3>
                            <small>Status Sejahtera Naik</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card card-metric bg-success text-white shadow">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1"><i class="fas fa-arrow-down mr-1"></i> Turun Desil</h6>
                            <h3 class="font-weight-bold mb-0"><span id="sum-turun">0</span> <span class="fs-6 fw-normal" id="pct-turun">(0%)</span></h3>
                            <small>Prioritas Usulan</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <div class="card card-metric bg-secondary text-white shadow">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1"><i class="fas fa-equals mr-1"></i> Tetap</h6>
                            <h3 class="font-weight-bold mb-0"><span id="sum-tetap">0</span> <span class="fs-6 fw-normal" id="pct-tetap">(0%)</span></h3>
                            <small>Tidak Berubah</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-12 mb-3">
                    <div class="card card-metric bg-dark text-white shadow border border-danger">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-1 text-danger"><i class="fas fa-radiation-alt mr-1"></i> POTENSI CORET</h6>
                            <h3 class="font-weight-bold mb-0 text-danger" id="sum-coret">0 <small class="fs-6 text-white">KK</small></h3>
                            <small>Penerima Aktif Melampaui Desil 4</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📋 TABEL DETAIL BNBA -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold mt-1" style="font-size: 1rem;">
                        <i class="fas fa-table text-primary mr-1"></i> Detail BNBA Perubahan Desil
                    </h3>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="tableDesil">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kepala Keluarga</th>
                                    <th>Alamat</th>
                                    <th class="text-center">Awal</th>
                                    <th class="text-center">Akhir</th>
                                    <th>Status Desil</th>
                                    <th>Bansos Aktif</th>
                                    <th>Prediksi Sistem</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        // 🚀 Inisialisasi DataTable & AJAX Intercept
        var table = $('#tableDesil').DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            // Menambahkan 'l' ke dalam kolom agar bersanding dengan tombol Export dan Search
            "dom": '<"row align-items-center"<"col-md-4"l><"col-md-4 text-center"B><"col-md-4"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Export Excel',
                className: 'btn btn-success btn-sm mb-3 shadow-sm text-white',
                exportOptions: {
                    format: {
                        body: function(data, row, column, node) {
                            // 🚀 Cek jika data mengandung tag HTML
                            if (typeof data === 'string') {
                                // 1. Ubah tag <br> menjadi karakter newline (\n) bawaan Excel
                                let cleanData = data.replace(/<br\s*[\/]?>/gi, "\n");
                                // 2. Sapu bersih sisa tag HTML lainnya (seperti <b>, <small>, <span>, dll)
                                cleanData = cleanData.replace(/<[^>]*>?/gm, '');
                                // 3. Decode HTML entities jika ada (seperti &amp; menjadi &)
                                let txt = document.createElement("textarea");
                                txt.innerHTML = cleanData;
                                return txt.value.trim();
                            }
                            return data;
                        }
                    }
                }
            }],
            "ajax": {
                "url": "<?= base_url('analisis-desil/datatable') ?>",
                "type": "POST",
                "data": function(d) {
                    d.rw = $('#filter_rw').val();
                    d.rt = $('#filter_rt').val();
                    d.periode_awal = $('#periode_awal').val();
                    d.periode_akhir = $('#periode_akhir').val();
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                },
                // 🚀 Tangkap Data Summary yang dikirim Controller
                "dataSrc": function(json) {
                    if (json.summary) {
                        $('#sum-total').text(json.summary.total);

                        $('#sum-naik').text(json.summary.val_naik);
                        $('#pct-naik').text('(' + json.summary.naik + '%)');

                        $('#sum-turun').text(json.summary.val_turun);
                        $('#pct-turun').text('(' + json.summary.turun + '%)');

                        $('#sum-tetap').text(json.summary.val_tetap);
                        $('#pct-tetap').text('(' + json.summary.tetap + '%)');

                        $('#sum-coret').html(json.summary.potensi_coret + ' <small class="fs-6 text-white">KK</small>');
                    }
                    return json.data || [];
                },
                "error": function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Gagal memuat data dari database!',
                        customClass: {
                            popup: 'swal-sm'
                        }
                    });
                }
            },
            "columnDefs": [{
                "className": "text-center",
                "targets": [0, 3, 4]
            }]
        });

        // 🚀 Trigger Auto-Reload saat Dropdown Berubah
        $('#filter_rw, #filter_rt, #periode_awal, #periode_akhir').on('change', function() {
            table.ajax.reload();
        });
    });

    // 🚀 Fungsi Salin No. KK
    window.copyKK = function(nokk) {
        navigator.clipboard.writeText(nokk).then(function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'KK ' + nokk + ' disalin!',
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                    popup: 'swal-sm'
                }
            });
        }).catch(function(err) {
            console.error('Gagal menyalin text: ', err);
        });
    };
</script>

<?= $this->endSection(); ?>