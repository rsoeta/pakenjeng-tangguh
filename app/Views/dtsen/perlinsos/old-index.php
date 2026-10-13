<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<!-- Tambahkan CDN ApexCharts & Select2 jika belum ada di template utama -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        color: #495057;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .card-custom {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>

<div class="content-wrapper mt-0">
    <div class="content-header pb-2">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1 fw-bold text-primary"><i class="fas fa-shield-alt me-2"></i>Rekapitulasi Perlinsos</h3>
                <small class="text-muted">Entri cepat data KPM Perlinsos Kemensos</small>
            </div>
            <div>
                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                    <i class="fas fa-user-check me-1"></i> Petugas: <?= $user_login['nama'] ?>
                </span>
            </div>
        </div>
    </div>

    <section class="content">

        <!-- ===================== BARIS 1: FORM & GRAFIK ===================== -->
        <div class="row g-3 mb-3">

            <!-- KOLOM KIRI (FORM) -->
            <div class="col-lg-4">
                <div class="card card-custom h-100 border-top border-4 border-primary">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold text-primary mb-0"><i class="fas fa-keyboard me-1"></i> Form Rekapitulasi</h6>
                    </div>
                    <div class="card-body">
                        <form id="formPerlinsos">
                            <input type="hidden" id="id_kk" name="id_kk">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Cari NIK</label>
                                <select class="form-select w-100" id="nik_peserta" name="nik_peserta" required>
                                    <option value="">Ketik NIK atau Nama...</option>
                                </select>
                                <div class="form-text text-info small mt-2"><i class="fas fa-info-circle"></i> Pastikan KPM sudah berhasil didaftarkan di aplikasi Perlinsos terlebih dahulu.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm mt-2" id="btnSimpan">
                                <i class="fas fa-save me-1"></i> Simpan Rekapitulasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (GRAFIK) -->
            <div class="col-lg-8">
                <div class="card card-custom h-100 border-top border-4 border-warning">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold text-warning mb-0"><i class="fas fa-trophy me-1"></i> Leaderboard Petugas</h6>
                    </div>
                    <div class="card-body px-2">
                        <!-- Tinggi chart disesuaikan agar pas dengan tinggi form di sebelahnya -->
                        <div id="chartCapaian" style="min-height: 280px; width: 100%;"></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ===================== BARIS 2: TABEL (FULL WIDTH) ===================== -->
        <div class="row">
            <div class="col-12">
                <div class="card card-custom">
                    <div class="card-header bg-white border-bottom pt-3 pb-2 d-flex align-items-center">
                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-history me-1"></i> Riwayat Input Terakhir</h6>
                        <button id="btnReload" class="btn btn-sm btn-outline-secondary rounded-pill ms-auto">
                            <i class="fas fa-sync-alt"></i> Segarkan
                        </button>
                    </div>
                    <div class="card-body">

                        <!-- 🎛️ FILTER BAR -->
                        <div class="row g-2 mb-3">
                            <?php if ($user_login['role_id'] <= 3): ?>
                                <div class="col-md-4">
                                    <select class="form-select form-select-sm" id="filterPetugas">
                                        <option value="">Semua Petugas</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-auto">
                                <select class="form-select form-select-sm" id="filterRW">
                                    <option value="">Semua RW</option>
                                </select>
                            </div>
                            <div class="col-md-auto">
                                <select class="form-select form-select-sm" id="filterRT" disabled>
                                    <option value="">Semua RT</option>
                                </select>
                            </div>
                            <div class="col-md-auto ms-auto">
                                <button class="btn btn-outline-secondary btn-sm" id="btnResetFilter" title="Reset Filter">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </div>

                        <!-- 📊 TABEL -->
                        <table id="tablePerlinsos" class="table table-striped table-hover nowrap w-100">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Waktu Rekap</th>
                                    <th>No. KK</th>
                                    <th>Kepala Keluarga</th>
                                    <th>NIK KPM</th>
                                    <th>Nama KPM</th>
                                    <th>Alamat Lengkap</th> <!-- 🚀 KOLOM BARU -->
                                    <?php if ($user_login['role_id'] <= 3): ?>
                                        <th>Petugas</th>
                                    <?php endif; ?>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<script>
    $(document).ready(function() {

        // ==========================================
        // 🎛️ LOGIKA FILTER DROPDOWN
        // ==========================================
        // Load Opsi Filter Awal
        $.getJSON('/perlinsos/filter-options', function(res) {
            if (res.petugas) res.petugas.forEach(p => $('#filterPetugas').append(`<option value="${p.nik}">${p.fullname}</option>`));
            if (res.rw) res.rw.forEach(r => $('#filterRW').append(`<option value="${r.rw}">RW ${r.rw}</option>`));
        });

        $('#filterPetugas, #filterRT').on('change', function() {
            tablePerlinsos.ajax.reload(null, false);
        });

        $('#filterRW').on('change', function() {
            const rw = $(this).val();
            const $rt = $('#filterRT');
            $rt.prop('disabled', !rw).html('<option value="">Semua RT</option>');

            tablePerlinsos.ajax.reload(null, false);

            if (rw) {
                $.getJSON('/perlinsos/rt-by-rw/' + rw, function(res) {
                    if (res.rt) res.rt.forEach(rt => $rt.append(`<option value="${rt}">RT ${rt}</option>`));
                });
            }
        });

        $('#btnResetFilter').on('click', function() {
            $('#filterPetugas, #filterRW').val('');
            $('#filterRT').html('<option value="">Semua RT</option>').prop('disabled', true);
            tablePerlinsos.ajax.reload(null, false);
        });

        // ==========================================
        // 1️⃣ INITIALIZE SELECT2 (AJAX PENCARIAN)
        // ==========================================
        $('#nik_peserta').select2({
            placeholder: "Ketik NIK...",
            allowClear: true,
            ajax: {
                url: '/perlinsos/search-art',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        // Otomatis isi id_kk yang tersembunyi saat NIK dipilih
        $('#nik_peserta').on('select2:select', function(e) {
            var data = e.params.data;
            $('#id_kk').val(data.id_kk);
        });

        $('#nik_peserta').on('select2:clear', function(e) {
            $('#id_kk').val('');
        });

        // ==========================================
        // 🛡️ FUNGSI BANTUAN: SENSOR DATA SENSITIF
        // ==========================================
        function maskNumberJS(number) {
            if (!number || number === '-' || number === 'NOKKS') return number || '-';

            let numStr = number.toString().trim();
            if (numStr.length <= 8) return numStr;

            let masked = numStr.substring(0, 8) + '*'.repeat(numStr.length - 8);

            return `<span class="fw-bold" style="cursor:pointer;" 
                  onmouseenter="this.innerText='${numStr}'" 
                  onmouseleave="this.innerText='${masked}'" 
                  ontouchstart="this.innerText='${numStr}'" 
                  ontouchend="this.innerText='${masked}'" 
                  title="Arahkan kursor untuk melihat utuh">${masked}</span>`;
        }

        // ==========================================
        // 2️⃣ INITIALIZE DATATABLES
        // ==========================================
        const roleId = <?= $user_login['role_id'] ?>;

        const tablePerlinsos = $('#tablePerlinsos').DataTable({
            ajax: {
                url: '/perlinsos/data',
                type: 'POST',
                data: function(d) {
                    d.petugas = $('#filterPetugas').val() || '';
                    d.rw = $('#filterRW').val() || '';
                    d.rt = $('#filterRT').val() || '';
                },
                dataSrc: 'data'
            },
            responsive: true,
            pageLength: 10,
            columns: [{
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                    // className: 'text-center'
                },
                {
                    data: 'created_at',
                    // className: 'text-center',
                    render: d => d ? d.substring(0, 16) : '-'
                },
                // 🚀 KOLOM NO KK (SENSOR & COPY)
                {
                    data: 'no_kk',
                    // className: 'text-center text-nowrap',
                    render: function(data, type) {
                        if (!data) return '-';
                        if (type === 'filter' || type === 'sort') return data;

                        return `
                    <div class="d-flex gap-2">
                        <span>${maskNumberJS(data)}</span>
                        <button type="button" class="btn btn-outline-secondary btn-xs btnCopyGlobal" data-value="${data}" title="Salin No KK">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>`;
                    }
                },
                // 🚀 KOLOM KEPALA KELUARGA
                {
                    data: 'kepala_keluarga',
                    // className: 'text-start text-capitalize'
                },
                // 🚀 KOLOM NIK (SENSOR & COPY)
                {
                    data: 'nik',
                    // className: 'text-center text-nowrap',
                    render: function(data, type) {
                        if (!data) return '-';
                        if (type === 'filter' || type === 'sort') return data;

                        // Beri warna primary khusus NIK agar beda dengan KK
                        let maskedData = maskNumberJS(data).replace('class="fw-bold"', 'class="fw-bold text-primary"');

                        return `
                    <div class="d-flex gap-2">
                        <span>${maskedData}</span>
                        <button type="button" class="btn btn-outline-secondary btn-xs btnCopyGlobal" data-value="${data}" title="Salin NIK">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>`;
                    }
                },
                {
                    data: 'nama_kpm',
                    // className: 'text-start text-capitalize'
                },
                // 🚀 KOLOM ALAMAT LENGKAP (PENGGABUNGAN DATA)
                {
                    data: null,
                    className: 'text-start',
                    render: function(row) {
                        let alamat = row.alamat ? row.alamat + ', ' : '';
                        let rt = row.rt ? 'RT ' + row.rt : '';
                        let rw = row.rw ? ' / RW ' + row.rw : '';

                        return (alamat + rt + rw) || '-';
                    }
                },
                // Kolom petugas
                ...(roleId <= 3 ? [{
                    data: 'petugas',
                    // className: 'text-start'
                }] : []),
                {
                    data: 'id',
                    // className: 'text-center',
                    orderable: false,
                    render: function(id) {
                        return `
                        <button class="btn btn-outline-danger btn-sm rounded-circle shadow-sm btnHapus" data-id="${id}" title="Hapus Data">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    }
                }
            ],
            order: [
                [1, 'desc']
            ], // Urutkan berdasarkan waktu rekap terbaru
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });

        $('#btnReload').on('click', function() {
            tablePerlinsos.ajax.reload();
            loadChart(); // Segarkan chart juga
        });

        // ==========================================
        // 3️⃣ INITIALIZE APEXCHARTS (TARGET VS CAPAIAN)
        // ==========================================
        var chartOptions = {
            series: [], // Akan diisi dari AJAX
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false, // Ubah vertikal agar enak dilihat perbandingannya
                    columnWidth: '55%',
                    endingShape: 'rounded',
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#333']
                },
                offsetY: -20
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: []
            },
            colors: ['#0d6efd', '#ffc107'], // Biru (Tercapai), Kuning (Target)
            fill: {
                opacity: 1
            },
            legend: {
                position: 'top'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " KPM"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chartCapaian"), chartOptions);
        chart.render();

        function loadChart() {
            $.getJSON('/perlinsos/chart-capaian', function(response) {
                chart.updateSeries(response.series);
                chart.updateOptions({
                    xaxis: {
                        categories: response.categories
                    }
                });
            });
        }

        loadChart(); // Load pertama kali

        // ==========================================
        // 4️⃣ LOGIKA SIMPAN DATA
        // ==========================================
        $('#formPerlinsos').on('submit', function(e) {
            e.preventDefault();

            let id_kk = $('#id_kk').val();
            let nik = $('#nik_peserta').val();

            if (!id_kk || !nik) {
                Swal.fire('Peringatan', 'Silakan pilih KPM terlebih dahulu!', 'warning');
                return;
            }

            let btn = $('#btnSimpan');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: '/perlinsos/save',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Rekapitulasi');

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Bersihkan Form
                        $('#nik_peserta').val(null).trigger('change');
                        $('#id_kk').val('');

                        // Update Data (Tabel & Grafik)
                        tablePerlinsos.ajax.reload(null, false);
                        loadChart();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Rekapitulasi');
                    Swal.fire('Error', 'Terjadi kesalahan pada server!', 'error');
                }
            });
        });

        // ==========================================
        // 5️⃣ LOGIKA HAPUS DATA
        // ==========================================
        $(document).on('click', '.btnHapus', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Hapus Rekapitulasi?',
                text: "Data yang dihapus dari SINDEN tidak memengaruhi data di web Perlinsos Kemensos.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('/perlinsos/delete/' + id, function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            tablePerlinsos.ajax.reload(null, false);
                            loadChart();
                        } else {
                            Swal.fire('Gagal', 'Data gagal dihapus', 'error');
                        }
                    }, 'json');
                }
            });
        });

        // ==========================================================
        // 📋 FITUR SALIN TEKS GLOBAL (NO KK / NIK)
        // ==========================================================
        $(document).on('click', '.btnCopyGlobal', function() {
            const value = $(this).data('value');
            if (!value) return;

            navigator.clipboard.writeText(value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin',
                    text: 'Data ' + value + ' berhasil disalin ke clipboard',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top'
                });
            }).catch(() => {
                Swal.fire('Gagal', 'Tidak dapat menyalin teks', 'error');
            });
        });
    });
</script>

<?= $this->endSection(); ?>