<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0 fw-bold"><i class="fas fa-camera-retro text-primary mr-2"></i> <?= $title; ?></h4>
                    <p class="text-muted">Kelola penyaluran bansos melalui KKS secara efisien</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Bansos KKS</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">

        <div class="container-fluid">
            <div class="card filter-box mb-4 shadow-none border">
                <div class="card-body p-3">

                    <!-- BARIS 1: AREA FILTER DROPDOWN -->
                    <div class="row align-items-end g-2">
                        <!-- Filter RW -->
                        <div class="col-6 col-md-2">
                            <label class="filter-label small fw-bold text-muted mb-1">Wilayah RW</label>
                            <select id="filter_rw" class="form-control form-control-sm border bg-light rounded-pill px-3">
                                <option value="">Semua RW</option>
                            </select>
                        </div>

                        <!-- Filter RT -->
                        <div class="col-6 col-md-2">
                            <label class="filter-label small fw-bold text-muted mb-1">Wilayah RT</label>
                            <select id="filter_rt" class="form-control form-control-sm border bg-light rounded-pill px-3">
                                <option value="">Semua RT</option>
                            </select>
                        </div>

                        <!-- Filter Tahap Salur -->
                        <div class="col-6 col-md-3">
                            <label class="filter-label small fw-bold text-muted mb-1">Tahap Salur</label>
                            <select id="filter_tahap" class="form-control form-control-sm border bg-light rounded-pill px-3">
                                <option value="">Semua Tahap</option>
                                <?php
                                $currentYear = date('Y');
                                $currentTahapNum = ceil(date('n') / 3);
                                $defaultTahap = "Tahap {$currentTahapNum} Tahun {$currentYear}";

                                foreach ([$currentYear, $currentYear - 1] as $y) {
                                    $maxTahap = ($y == $currentYear) ? $currentTahapNum : 4;
                                    for ($t = $maxTahap; $t >= 1; $t--) {
                                        $val = "Tahap $t Tahun $y";
                                        $isSelected = ($val === $defaultTahap) ? 'selected' : '';
                                        echo "<option value=\"$val\" $isSelected>$val</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Filter Jenis Bansos -->
                        <div class="col-6 col-md-3">
                            <label class="filter-label small fw-bold text-muted mb-1">Jenis Bansos</label>
                            <select id="filter_jenis" class="form-control form-control-sm border bg-light rounded-pill px-3">
                                <option value="">Semua Bansos</option>
                                <option value="PKH">PKH</option>
                                <option value="SEMBAKO">SEMBAKO</option>
                                <option value="PKH + SEMBAKO">MIX (PKH + SEMBAKO)</option>
                            </select>
                        </div>

                        <!-- Filter Status Kunci -->
                        <div class="col-12 col-md-2">
                            <label class="filter-label small fw-bold text-muted mb-1">Status Kunci</label>
                            <select id="filter_locked" class="form-control form-control-sm border bg-light rounded-pill px-3">
                                <option value="">Semua Status</option>
                                <option value="1">🔒 Terkunci</option>
                                <option value="0">🔓 Terbuka</option>
                            </select>
                        </div>
                    </div>

                    <!-- BARIS 2: AREA TOMBOL AKSI (RATA KANAN) -->
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end align-items-center" style="gap: 8px;">
                            <button id="btn_filter" class="btn btn-sm btn-dark rounded-pill px-4 shadow-sm">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>

                            <?php if (session()->get('role_id') < 4): ?>
                                <button id="btn_export_excel" class="btn btn-sm btn-success rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-file-excel mr-1"></i> Export
                                </button>

                                <button type="button" class="btn btn-primary btn-sm shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalImportTugas">
                                    <i class="fas fa-upload mr-1"></i> Impor (SIKS-NG)
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card card-bansos shadow-sm">
            <div class="card-body p-3">
                <table class="table table-hover align-middle w-100" id="tableDokumentasi">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Foto KPM</th>
                            <th>Detail KPM & Bantuan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
</div>


<button class="btn btn-primary shadow btn-floating rounded-pill px-4 py-2" type="button"
    data-bs-toggle="offcanvas" data-bs-target="#offcanvasMaster">
    <i class="fas fa-plus-circle mr-1"></i> Tambah Baru
</button>

<div class="offcanvas offcanvas-end shadow" tabindex="-1" id="offcanvasMaster" style="width: 600px; max-width: 90vw;">
    <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title"><i class="fas fa-edit mr-2"></i> Form Dokumentasi Penyaluran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body bg-light p-0">
        <form id="formBansosKKS" enctype="multipart/form-data">
            <?= csrf_field(); ?>

            <input type="hidden" name="id" id="id_dokumentasi">

            <div class="p-3">
                <div class="card card-primary card-outline shadow-sm mb-3">
                    <div class="card-body">
                        <div class="form-group row align-items-center">
                            <label for="nik_search" class="col-4 col-form-label" style="font-size: 0.9rem;">Cari NIK / KKS</label>
                            <div class="col-8">
                                <select class="form-control" id="nik_search" name="nik_search" style="width: 100%;"></select>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Ketik NIK atau Nomor KKS</small>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="form-group row mb-2">
                            <label class="col-4 col-form-label small">Nama KPM</label>
                            <div class="col-8">
                                <input type="text" class="form-control bg-white form-control-sm" id="nama_kpm" name="nama_kpm" readonly placeholder="Otomatis...">
                                <input type="hidden" id="nik_kpm_hidden" name="nik_kpm">
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-4 col-form-label small">Nomor KKS</label>
                            <div class="col-8">
                                <input type="text" class="form-control bg-white form-control-sm text-primary font-weight-bold" id="no_kks" name="no_kks" readonly placeholder="Otomatis...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="form-group row mb-3">
                            <label class="col-4 col-form-label small">Jenis Bansos <span class="text-danger">*</span></label>
                            <div class="col-8">
                                <div class="btn-group btn-group-toggle btn-group-bansos d-flex" data-toggle="buttons">
                                    <label class="btn btn-sm flex-fill">
                                        <input type="radio" name="jenis_bansos" value="PKH" required> PKH
                                    </label>
                                    <label class="btn btn-sm flex-fill">
                                        <input type="radio" name="jenis_bansos" value="SEMBAKO"> SMBK
                                    </label>
                                    <label class="btn btn-sm flex-fill">
                                        <input type="radio" name="jenis_bansos" value="PKH + SEMBAKO"> MIX
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-4 col-form-label small">Tahap & Tahun <span class="text-danger">*</span></label>
                            <div class="col-8">
                                <div class="row">
                                    <div class="col-5 pr-1">
                                        <select class="form-control form-control-sm" name="tahun_salur" required>
                                            <option value="<?= date('Y') ?>"><?= date('Y') ?></option>
                                            <option value="<?= date('Y') - 1 ?>"><?= date('Y') - 1 ?></option>
                                        </select>
                                    </div>
                                    <div class="col-7 pl-1">
                                        <?php
                                        // 🚀 Menghitung tahap berdasarkan bulan berjalan (Kuartal 1-4)
                                        $tahapSekarang = ceil(date('n') / 3);
                                        ?>
                                        <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                            <label class="btn btn-xs flex-fill py-1 <?= ($tahapSekarang == 1) ? 'active' : '' ?>">
                                                <input type="radio" name="tahap_salur" value="Tahap 1" <?= ($tahapSekarang == 1) ? 'checked' : '' ?> required> T.1
                                            </label>
                                            <label class="btn btn-xs flex-fill py-1 <?= ($tahapSekarang == 2) ? 'active' : '' ?>">
                                                <input type="radio" name="tahap_salur" value="Tahap 2" <?= ($tahapSekarang == 2) ? 'checked' : '' ?> required> T.2
                                            </label>
                                            <label class="btn btn-xs flex-fill py-1 <?= ($tahapSekarang == 3) ? 'active' : '' ?>">
                                                <input type="radio" name="tahap_salur" value="Tahap 3" <?= ($tahapSekarang == 3) ? 'checked' : '' ?> required> T.3
                                            </label>
                                            <label class="btn btn-xs flex-fill py-1 <?= ($tahapSekarang == 4) ? 'active' : '' ?>">
                                                <input type="radio" name="tahap_salur" value="Tahap 4" <?= ($tahapSekarang == 4) ? 'checked' : '' ?> required> T.4
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label class="col-4 col-form-label small">Nominal (Rp) <span class="text-danger">*</span></label>
                            <div class="col-8">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control font-weight-bold text-success" id="nominal_cair" name="nominal_cair" placeholder="0" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="add_thousands"><strong>+000</strong></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <label class="col-4 col-form-label small">Status Salur <span class="text-danger">*</span></label>
                            <div class="col-8">
                                <select class="form-control form-control-sm" name="status_salur" id="status_salur" required>
                                    <option value="">-- Pilih (Kosongkan jika PR) --</option>
                                    <option value="Sukses Salur">Sukses Salur</option>
                                    <option value="Saldo Kosong">Saldo Kosong</option>
                                    <option value="KKS Rusak/Hilang">KKS Rusak/Hilang</option>
                                    <option value="KPM Meninggal">KPM Meninggal</option>
                                    <option value="Pindah/Tidak Ditemukan">Pindah</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="card card-outline card-success h-100">
                            <div class="card-body p-2 text-center">
                                <label class="small d-block mb-1">Foto KPM + KKS <span class="text-danger">*</span></label>
                                <img id="prev_kpm" src="<?= base_url('assets/img/no-image.svg'); ?>" class="img-fluid border rounded mb-2" style="aspect-ratio: 1/1; object-fit: cover;">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="foto_kpm_kks" id="foto_kpm_kks" accept="image/*">
                                    <label class="custom-file-label small" for="foto_kpm_kks">Upload...</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card card-outline card-success h-100">
                            <div class="card-body p-2 text-center">
                                <label class="small d-block mb-1">Foto Struk/Uang <span class="text-danger">*</span></label>
                                <img id="prev_bukti" src="<?= base_url('assets/img/no-image.svg'); ?>" class="img-fluid border rounded mb-2" style="aspect-ratio: 1/1; object-fit: cover;">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="foto_bukti_transaksi" id="foto_bukti_transaksi" accept="image/*">
                                    <label class="custom-file-label small" for="foto_bukti_transaksi">Upload...</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="lat" name="latitude">
                <input type="hidden" id="lng" name="longitude">
            </div>

            <div class="p-3 bg-white border-top sticky-bottom">
                <button type="submit" id="btnSimpan" class="btn btn-primary btn-block py-2 rounded-pill shadow">
                    <i class="fas fa-save mr-1"></i> Simpan Dokumentasi
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalImportTugas" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i> Impor PR / Tugas SIKS-NG</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <form action="<?= base_url('bansos-kks/importTugasExcel'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body bg-light">
                    <div class="alert alert-warning small py-2 border-warning shadow-sm">
                        <i class="fas fa-info-circle"></i> <b>Format Kolom Excel (Mulai Baris 2):</b><br>
                        A. NIK KPM &nbsp; | &nbsp; B. NAMA KPM &nbsp; | &nbsp; C. NOMOR KKS<br>
                        D. JENIS BANSOS &nbsp; | &nbsp; E. TAHAP SALUR &nbsp; | &nbsp; F. NOMINAL CAIR
                    </div>
                    <div class="form-group mb-0">
                        <label class="fw-bold small">Pilih File Excel (.xls / .xlsx)</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm"><i class="fas fa-upload mr-1"></i> Mulai Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // --- DATA TABLES ---
        var tableDokumentasi = $('#tableDokumentasi').DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            "ajax": {
                "url": "<?= base_url('bansos-kks/datatable') ?>",
                "type": "POST",
                "data": function(d) {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                    // 🚀 Kirim parameter filter ke Controller
                    d.filter_rw = $('#filter_rw').val();
                    d.filter_rt = $('#filter_rt').val();
                    d.filter_tahap = $('#filter_tahap').val();
                    d.filter_locked = $('#filter_locked').val();
                    d.filter_jenis = $('#filter_jenis').val();
                }
            },
            "columnDefs": [{
                    "className": "text-center align-middle",
                    "targets": [0, 1, 3]
                },
                {
                    "orderable": false,
                    "targets": [1, 3]
                }
            ]
        });

        // 🚀 Aktifkan Tombol Filter
        $('#btn_filter').click(function() {
            tableDokumentasi.ajax.reload();
        });

        // 🚀 FUNGSI EXPORT EXCEL MENGIKUTI FILTER
        $('#btn_export_excel').click(function() {
            var rw = $('#filter_rw').val();
            var rt = $('#filter_rt').val();
            var tahap = $('#filter_tahap').val();
            var locked = $('#filter_locked').val();
            var jenis = $('#filter_jenis').val();

            // Redirect ke function Controller dengan query parameter
            var exportUrl = "<?= base_url('bansos-kks/exportExcel') ?>?rw=" + rw + "&rt=" + rt + "&tahap=" + tahap + "&locked=" + locked + "&jenis=" + jenis;
            window.open(exportUrl, '_blank');
        });

        // ==========================================
        // 🌍 DYNAMIC FILTER CASCADING (RW -> RT)
        // ==========================================

        // 1. Load data RW secara otomatis saat halaman dibuka
        $.ajax({
            url: "<?= base_url('bansos-kks/get-rw') ?>",
            type: "GET",
            cache: false, // 🚀 KUNCI: Cegah browser menggunakan ingatan lama!
            dataType: "JSON",
            success: function(data) {
                $('#filter_rw').empty().append('<option value="">Semua RW</option>');
                $.each(data, function(index, value) {
                    $('#filter_rw').append('<option value="' + value + '">' + value + '</option>');
                });
            }
        });

        // 2. Load data RT secara dinamis berdasarkan RW yang dipilih
        $('#filter_rw').change(function() {
            var rwSelected = $(this).val();
            $('#filter_rt').empty().append('<option value="">Semua RT</option>');

            if (rwSelected !== "") {
                $.ajax({
                    url: "<?= base_url('bansos-kks/get-rt') ?>",
                    type: "GET",
                    cache: false, // 🚀 KUNCI: Cegah browser menggunakan ingatan lama!
                    data: {
                        rw: rwSelected
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $.each(data, function(index, value) {
                            $('#filter_rt').append('<option value="' + value + '">' + value + '</option>');
                        });
                    }
                });
            }
        });

        // --- SELECT2 (Dengan dropdownParent agar jalan di Offcanvas) ---
        $('#nik_search').select2({
            dropdownParent: $('#offcanvasMaster'), // 👈 KUNCI BIAR JALAN DI MODAL/OFFCANVAS
            ajax: {
                url: '<?= base_url('bansos-kks/cari-nik') ?>',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchTerm: params.term,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        $('#nik_search').on('select2:select', function(e) {
            var data = e.params.data;
            $('#nama_kpm').val(data.nama_kpm);
            $('#no_kks').val(data.no_kks);
            $('#nik_kpm_hidden').val(data.id);
        });

        // --- RUPIAH AUTO FORMAT ---
        $('#nominal_cair').on('keyup', function() {
            var val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(new Intl.NumberFormat('id-ID', {
                style: 'decimal'
            }).format(val));
        });

        $('#add_thousands').on('click', function() {
            let currentVal = $('#nominal_cair').val().replace(/[^0-9]/g, '');
            if (currentVal !== '') {
                let newVal = currentVal + '000';
                $('#nominal_cair').val(new Intl.NumberFormat('id-ID', {
                    style: 'decimal'
                }).format(newVal));
            } else {
                $('#nominal_cair').focus();
            }
        });

        // --- LIVE PREVIEW FOTO ---
        function readURL(input, targetID) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(targetID).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                $(input).next('.custom-file-label').html(input.files[0].name);
            }
        }
        $('#foto_kpm_kks').change(function() {
            readURL(this, '#prev_kpm');
        });
        $('#foto_bukti_transaksi').change(function() {
            readURL(this, '#prev_bukti');
        });

        // --- GEOTAGGING ---
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                $('#lat').val(position.coords.latitude);
                $('#lng').val(position.coords.longitude);
            });
        }

        // Pastikan variabel roleId sudah dideklarasikan di awal script JS
        var roleId = <?= session()->get('role_id') ?>;

        // ========================================================
        // 1. LOGIKA KLIK TOMBOL EDIT / LENGKAPI / LIHAT
        // ========================================================
        $('#tableDokumentasi').on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            var kelengkapan = $(this).data('kelengkapan'); // Menangkap chip pelacak dari PHP

            $.ajax({
                url: "<?= base_url('bansos-kks/edit-ajax') ?>/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(res) {
                    if (res.status === 'success') {
                        var d = res.data;

                        // Reset Form 
                        $('#formBansosKKS')[0].reset();

                        // Set Judul Berdasarkan Mode
                        if (roleId >= 4 && kelengkapan == 0) {
                            $('.offcanvas-title').html('<i class="fas fa-camera text-primary mr-2"></i> Lengkapi Dokumentasi');
                        } else if (roleId >= 4 && kelengkapan == 1) {
                            $('.offcanvas-title').html('<i class="fas fa-eye text-info mr-2"></i> Lihat Data (Read-Only)');
                        } else {
                            $('.offcanvas-title').html('<i class="fas fa-edit text-warning mr-2"></i> Edit Dokumentasi');
                        }

                        // Isi Hidden ID
                        if ($('#id_dokumentasi').length == 0) {
                            $('#formBansosKKS').append('<input type="hidden" name="id" id="id_dokumentasi">');
                        }
                        $('#id_dokumentasi').val(d.id);

                        // Isi Data Teks & Hidden
                        $('#nama_kpm').val(d.nama_kpm);
                        $('#no_kks').val(d.no_kks);
                        $('#nik_kpm_hidden').val(d.nik_kpm);
                        $('#lat').val(d.latitude);
                        $('#lng').val(d.longitude);

                        $('#nominal_cair').val(new Intl.NumberFormat('id-ID').format(d.nominal_cair));
                        $("select[name='status_salur']").val(d.status_salur);

                        // 🚀 PECAH STRING TAHAP DAN TAHUN
                        if (d.tahap_salur) {
                            var splitTahap = d.tahap_salur.split(' Tahun ');
                            var tahap = splitTahap[0];
                            var tahun = splitTahap[1];

                            $('.btn-group-toggle input[name="tahap_salur"]').parent().removeClass('active');
                            $("input[name='tahap_salur'][value='" + tahap + "']").prop('checked', true).parent().addClass('active');

                            if ($("select[name='tahun_salur'] option[value='" + tahun + "']").length === 0) {
                                $("select[name='tahun_salur']").append(new Option(tahun, tahun));
                            }
                            $("select[name='tahun_salur']").val(tahun);
                        }

                        // Set Radio Button Jenis Bansos
                        $('.btn-group-bansos input[name="jenis_bansos"]').parent().removeClass('active');
                        $("input[name='jenis_bansos'][value='" + d.jenis_bansos + "']").prop('checked', true).parent().addClass('active');

                        // Tampilkan Preview Foto
                        var prevKpmSrc = d.foto_kpm_kks ? "<?= base_url('uploads/bansos') ?>/" + d.foto_kpm_kks : "<?= base_url('assets/img/no-image.svg') ?>";
                        var prevBuktiSrc = d.foto_bukti_transaksi ? "<?= base_url('uploads/bansos') ?>/" + d.foto_bukti_transaksi : "<?= base_url('assets/img/no-image.svg') ?>";
                        $('#prev_kpm').attr('src', prevKpmSrc);
                        $('#prev_bukti').attr('src', prevBuktiSrc);

                        // SYNC SELECT2
                        var newOption = new Option("NIK: " + d.nik_kpm + " - " + d.nama_kpm, d.nik_kpm, true, true);
                        $('#nik_search').append(newOption).trigger('change');

                        // ========================================================
                        // 🚀 LOGIKA PENGGEMBOKAN SMART LOCK (TUGAS PENTRI)
                        // ========================================================
                        // 1. Reset semua gembokan dulu ke kondisi normal
                        $('#formBansosKKS').find('input, select, textarea, button[type="submit"]').prop('disabled', false).prop('readonly', false).removeClass('bg-light');
                        $('#btnSimpan').show();

                        if (roleId >= 4) {
                            if (kelengkapan == 0) {
                                // MODE PENTRI LENGKAPI TUGAS: Kunci identitas & bansos
                                $('#nama_kpm, #no_kks').prop('readonly', true).addClass('bg-light');
                                $('input[name="jenis_bansos"], input[name="tahap_salur"]').prop('disabled', true);
                                $('select[name="tahun_salur"]').prop('disabled', true).addClass('bg-light');
                                $('#nik_search').prop('disabled', true); // Kunci Select2 NIK
                            } else {
                                // MODE PENTRI LIHAT DATA: Kunci Semua!
                                $('#formBansosKKS').find('input, select, textarea').prop('disabled', true);
                                $('#btnSimpan').hide(); // Sembunyikan tombol simpan
                            }
                        }

                        // TAMPILKAN OFFCANVAS
                        var offcanvasEl = document.getElementById('offcanvasMaster');
                        var myOffcanvas = window.BS5.Offcanvas.getInstance(offcanvasEl);
                        if (!myOffcanvas) {
                            myOffcanvas = new window.BS5.Offcanvas(offcanvasEl);
                        }
                        myOffcanvas.show();
                    }
                }
            });
        });

        // ========================================================
        // 🚀 LOGIKA KLIK TOMBOL TAMBAH BARU
        // ========================================================
        $('[data-bs-target="#offcanvasMaster"]').on('click', function() {
            // Reset Form ke kondisi perawan
            $('#formBansosKKS')[0].reset();
            $('#id_dokumentasi').val('');
            $('.offcanvas-title').html('<i class="fas fa-plus-circle text-primary mr-2"></i> Tambah Data Baru');

            // Reset Select2 dan Preview Foto
            if ($('#nik_search').hasClass('select2-hidden-accessible')) {
                $('#nik_search').val(null).trigger('change');
            }
            $('#prev_kpm, #prev_bukti').attr('src', '<?= base_url("assets/img/no-image.svg") ?>');

            // Lepas semua gembokan form
            $('#formBansosKKS').find('input, select, textarea, button[type="submit"]').prop('disabled', false).prop('readonly', false).removeClass('bg-light');
            $('#btnSimpan').show();

            // 🚀 JIKA ADMIN: Boleh simpan tanpa foto dan tanpa status (sebagai PR)
            if (roleId <= 3) {
                $('#foto_kpm_kks, #foto_bukti_transaksi').prop('required', false);
                $('#status_salur').prop('required', false); // 🚀 Matikan required status salur
            } else {
                $('#foto_kpm_kks, #foto_bukti_transaksi').prop('required', true);
                $('#status_salur').prop('required', true);
            }
        });

        // ========================================================
        // 🚀 LOGIKA AJAX SUBMIT (Perbaikan ekstensi SVG)
        // ========================================================
        $('#formBansosKKS').on('submit', function(e) {
            e.preventDefault();

            var statusSalur = $("select[name='status_salur']").val();
            var isDraftAdmin = (roleId <= 3 && !statusSalur); // Deteksi mode Draft Admin

            if ($('#nik_kpm_hidden').val() === '') {
                Swal.fire('Peringatan', 'Pilih NIK KPM dahulu!', 'warning');
                return false;
            }
            if (!$("input[name='jenis_bansos']:checked").val()) {
                Swal.fire('Peringatan', 'Pilih Jenis Bansos!', 'warning');
                return false;
            }

            // 🚀 VALIDASI FOTO CERDAS (Perbaikan: Deteksi .svg)
            if (!isDraftAdmin) {
                var isFotoKpmKosong = ($('#foto_kpm_kks').val() === '' && $('#prev_kpm').attr('src').includes('no-image.svg'));
                var isFotoBuktiKosong = ($('#foto_bukti_transaksi').val() === '' && $('#prev_bukti').attr('src').includes('no-image.svg'));

                if (isFotoKpmKosong || isFotoBuktiKosong) {
                    Swal.fire('Peringatan', 'Kedua foto wajib diunggah untuk kelengkapan dokumentasi!', 'warning');
                    return false;
                }

                if ($('#nominal_cair').val() === '') {
                    Swal.fire('Peringatan', 'Nominal Cair wajib diisi!', 'warning');
                    return false;
                }
            }

            // Buka paksa field yang ter-disable agar value-nya terkirim ke Backend
            $(this).find(':disabled').prop('disabled', false);

            var formData = new FormData(this);
            var rawNominal = $('#nominal_cair').val().replace(/\./g, '');
            formData.set('nominal_cair', rawNominal);

            $.ajax({
                url: '<?= base_url('bansos-kks/simpan') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');
                },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Berhasil', res.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                        $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Dokumentasi');
                    }
                },
                error: function() {
                    Swal.fire('Error Sistem', 'Terjadi kesalahan pada server!', 'error');
                    $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Dokumentasi');
                }
            });
        });

        // ==========================================
        // 🗑️ LOGIKA KLIK TOMBOL HAPUS (DENGAN SWEETALERT)
        // ==========================================
        $('#tableDokumentasi').on('click', '.btn-delete', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data dokumentasi beserta foto fisiknya akan dihapus permanen dan tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                reverseButtons: true // Supaya tombol batal ada di kiri, hapus di kanan
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang menyapu bersih data dan foto.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "<?= base_url('bansos-kks/hapus') ?>",
                        type: "POST",
                        data: {
                            id: id,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>' // Proteksi CSRF
                        },
                        dataType: "JSON",
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Terhapus!', res.message, 'success');
                                // Reload tabel tanpa mereset halaman (tetap di page yang sama)
                                $('#tableDokumentasi').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error Sistem', xhr.responseText, 'error');
                        }
                    });
                }
            });
        });

        // 2. Reset Form saat klik "Tambah Baru" agar tidak terbawa data Edit
        $('[data-bs-target="#offcanvasMaster"]').click(function() {
            $('#formBansosKKS')[0].reset();
            $('#id_dokumentasi').val(''); // Kosongkan ID agar dianggap Simpan Baru
            $('.offcanvas-title').html('<i class="fas fa-plus-circle mr-2"></i> Tambah Dokumentasi Baru');
            $('#nik_search').val(null).trigger('change');
            $('#prev_kpm, #prev_bukti').attr('src', "<?= base_url('assets/images/image_not_available.jpg'); ?>");

            // 🚀 Reset Jenis Bansos (kosongkan)
            $('.btn-group-bansos .btn').removeClass('active');

            // 🚀 Reset Tahap Salur ke bulan berjalan saat ini secara dinamis
            $('.btn-group-toggle input[name="tahap_salur"]').parent().removeClass('active');
            var currentMonth = new Date().getMonth() + 1; // getMonth() mulai dari 0, jadi ditambah 1
            var currentTahap = Math.ceil(currentMonth / 3);
            $("input[name='tahap_salur'][value='Tahap " + currentTahap + "']").prop('checked', true).parent().addClass('active');

            // 🚀 Pastikan Dropdown Tahun juga kembali ke tahun ini
            $("select[name='tahun_salur']").val(new Date().getFullYear());
        });

        // ==========================================
        // 🔒 LOGIKA KLIK TOMBOL KUNCI/GEMBOK (TOGGLE LOCK)
        // ==========================================
        $('#tableDokumentasi').on('click', '.btn-toggle-lock', function() {
            var id = $(this).data('id');
            var currentStatus = $(this).data('status'); // 1 = saat ini terkunci, 0 = saat ini terbuka

            // 🚀 PERBAIKAN LOGIKA: Jika sekarang 0 (terbuka), maka kita akan menguncinya (isLocking = true)
            var isLocking = (currentStatus == 0);
            var targetStatus = isLocking ? 1 : 0; // Status yang akan dikirim ke database

            var titleText = isLocking ? 'Kunci Data?' : 'Buka Kunci Data?';
            var htmlText = isLocking ?
                'Data ini akan ditandai sebagai <b>Valid</b> dan tidak dapat diedit/dihapus oleh Operator/Pentri.' :
                'Kunci data akan dibuka, Operator/Pentri akan bisa mengedit data ini kembali.';
            var iconType = isLocking ? 'question' : 'warning';
            var btnText = isLocking ? '<i class="fas fa-lock"></i> Ya, Kunci!' : '<i class="fas fa-unlock"></i> Ya, Buka!';
            var btnColor = isLocking ? '#d33' : '#3085d6';

            Swal.fire({
                title: titleText,
                html: htmlText,
                icon: iconType,
                showCancelButton: true,
                confirmButtonColor: btnColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: btnText,
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "<?= base_url('bansos-kks/toggle-lock') ?>",
                        type: "POST",
                        data: {
                            id: id,
                            status: targetStatus, // 🚀 Kirim TARGET statusnya ke backend
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: "JSON",
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('#tableDokumentasi').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error Sistem', 'Terjadi kesalahan jaringan atau server.', 'error');
                        }
                    });
                }
            });
        });

        // ==========================================
        // 📋 FUNGSI COPY NIK & NO KK
        // ==========================================

        // Tombol Salin NIK
        $(document).on('click', '.btnCopyNik', function() {
            // 🚀 Ambil dari data-value (sesuai setting DataTables kita tadi)
            // Tambahkan fallback || $(this).data('nik') untuk berjaga-jaga jika ada tombol versi lama
            const nik = $(this).data('value') || $(this).data('nik');

            navigator.clipboard.writeText(nik)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'NIK Disalin!',
                        text: `NIK ${nik} berhasil disalin ke clipboard`,
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end' // 🚀 Pindah ke pojok kanan atas agar lebih estetik
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menyalin',
                        text: 'Clipboard tidak didukung oleh browser atau koneksi tidak aman (HTTPS/Localhost).',
                    });
                });
        });

        // Tombol Salin No. KK
        $(document).on('click', '.btnCopyNoKK', function() {
            // 🚀 Ambil dari data-value
            const noKK = $(this).data('value');

            navigator.clipboard.writeText(noKK)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'No. KKS Disalin!', // 🚀 Teks disesuaikan
                        text: `No. KKS ${noKK} berhasil disalin ke clipboard`, // 🚀 Teks disesuaikan
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menyalin',
                        text: 'Clipboard tidak didukung oleh browser atau koneksi tidak aman (HTTPS/Localhost).',
                    });
                });
        });

    });
</script>
<?= $this->endSection(); ?>