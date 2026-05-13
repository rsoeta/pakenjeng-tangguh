<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    /* Styling untuk Radio Button Group agar tampak seperti Tab/Tombol Profesional */
    .btn-group-toggle .btn {
        border: 1px solid #ced4da;
        background-color: #fff;
        color: #495057;
        font-size: 0.85rem;
        transition: all 0.2s ease-in-out;
    }

    .btn-group-toggle .btn:hover {
        background-color: #f8f9fa;
    }

    .btn-group-toggle .btn.active {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: #fff !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Khusus untuk Jenis Bansos agar warna hijaunya beda */
    .btn-group-bansos .btn.active {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
</style>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0"><?= $title; ?></h4>
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
            <div class="row">
                <div class="col-lg-7 col-md-12 mb-3">
                    <div class="card card-primary card-outline h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-edit mr-1"></i> Form Dokumentasi Penyaluran</h3>
                        </div>
                        <form id="formBansosKKS" class="form-horizontal" enctype="multipart/form-data">
                            <?= csrf_field(); ?>
                            <div class="card-body">
                                <div class="form-group row align-items-center">
                                    <label for="nik_search" class="col-4 col-form-label" style="font-size: 0.9rem;">Cari NIK / KKS</label>
                                    <div class="col-8">
                                        <select class="form-control select2" id="nik_search" name="nik_search" style="width: 100%;"></select>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Ketik NIK atau Nomor KKS</small>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="form-group row align-items-center">
                                    <label class="col-4 col-form-label" style="font-size: 0.9rem;">Nama KPM</label>
                                    <div class="col-8">
                                        <input type="text" class="form-control bg-light form-control-sm" id="nama_kpm" name="nama_kpm" readonly placeholder="Otomatis...">
                                        <input type="hidden" id="nik_kpm_hidden" name="nik_kpm">
                                    </div>
                                </div>

                                <div class="form-group row align-items-center">
                                    <label class="col-4 col-form-label" style="font-size: 0.9rem;">Nomor KKS</label>
                                    <div class="col-8">
                                        <input type="text" class="form-control bg-light form-control-sm text-primary font-weight-bold" id="no_kks" name="no_kks" readonly placeholder="Otomatis...">
                                    </div>
                                </div>

                                <div class="form-group row align-items-center">
                                    <label class="col-4 col-form-label" style="font-size: 0.9rem;">Jenis Bansos <span class="text-danger">*</span></label>
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

                                <div class="form-group row">
                                    <label class="col-4 col-form-label pt-0" style="font-size: 0.9rem;">Tahap & Tahun <span class="text-danger">*</span></label>
                                    <div class="col-8">
                                        <div class="row">
                                            <div class="col-5 pr-1">
                                                <select class="form-control form-control-sm" name="tahun_salur" required>
                                                    <option value="<?= date('Y') ?>"><?= date('Y') ?></option>
                                                    <option value="<?= date('Y') - 1 ?>"><?= date('Y') - 1 ?></option>
                                                </select>
                                            </div>
                                            <div class="col-7 pl-1">
                                                <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                                    <label class="btn btn-xs flex-fill py-1">
                                                        <input type="radio" name="tahap_salur" value="Tahap 1"> T.1
                                                    </label>
                                                    <label class="btn btn-xs flex-fill py-1">
                                                        <input type="radio" name="tahap_salur" value="Tahap 2"> T.2
                                                    </label>
                                                    <label class="btn btn-xs flex-fill py-1">
                                                        <input type="radio" name="tahap_salur" value="Tahap 3"> T.3
                                                    </label>
                                                    <label class="btn btn-xs flex-fill py-1">
                                                        <input type="radio" name="tahap_salur" value="Tahap 4"> T.4
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row align-items-center">
                                    <label class="col-4 col-form-label" style="font-size: 0.9rem;">Nominal (Rp) <span class="text-danger">*</span></label>
                                    <div class="col-8">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control font-weight-bold text-success" id="nominal_cair" name="nominal_cair" placeholder="0" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="add_thousands"><strong>+000</strong></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row align-items-center">
                                    <label class="col-4 col-form-label" style="font-size: 0.9rem;">Status Salur <span class="text-danger">*</span></label>
                                    <div class="col-8">
                                        <select class="form-control form-control-sm" name="status_salur" required>
                                            <option value="Sukses Salur">Sukses Salur</option>
                                            <option value="Saldo Kosong">Saldo Kosong</option>
                                            <option value="KKS Rusak/Hilang">KKS Rusak/Hilang</option>
                                            <option value="KPM Meninggal">KPM Meninggal</option>
                                            <option value="Pindah/Tidak Ditemukan">Pindah</option>
                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" id="lat" name="latitude">
                                <input type="hidden" id="lng" name="longitude">
                            </div>

                        </form>
                    </div>
                </div>

                <div class="col-lg-5 col-md-12 mb-3">
                    <div class="card card-success card-outline h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-camera mr-1"></i> Bukti Visual</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>1. Foto KPM Memegang KKS <span class="text-danger">*</span></label>
                                <div class="text-center mt-2 mb-2">
                                    <img id="prev_kpm" src="<?= base_url('assets/images/image_not_available.jpg'); ?>" class="img-fluid img-thumbnail rounded shadow-sm" style="width: 100%; aspect-ratio: 4/3; object-fit: contain; background-color: #f1f3f5;">
                                </div>
                                <div class="custom-file mt-2">
                                    <input type="file" class="custom-file-input" name="foto_kpm_kks" id="foto_kpm_kks" accept="image/*" form="formBansosKKS">
                                    <label class="custom-file-label" for="foto_kpm_kks">Pilih Gambar / Buka Kamera...</label>
                                </div>
                            </div>

                            <hr class="my-4 border-success">

                            <div class="form-group">
                                <label>2. Foto Bukti Transaksi (Struk/Uang) <span class="text-danger">*</span></label>
                                <div class="text-center mt-2 mb-2">
                                    <img id="prev_bukti" src="<?= base_url('assets/images/image_not_available.jpg'); ?>" class="img-fluid img-thumbnail rounded shadow-sm" style="width: 100%; aspect-ratio: 4/3; object-fit: contain; background-color: #f1f3f5;">
                                </div>
                                <div class="custom-file mt-2">
                                    <input type="file" class="custom-file-input" name="foto_bukti_transaksi" id="foto_bukti_transaksi" accept="image/*" form="formBansosKKS">
                                    <label class="custom-file-label" for="foto_bukti_transaksi">Pilih Gambar / Buka Kamera...</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top">
                            <button type="submit" id="btnSimpan" form="formBansosKKS" class="btn btn-primary float-right">
                                <i class="fas fa-save mr-1"></i> Simpan Dokumentasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // 1. Inisialisasi Select2 NIK
        $('#nik_search').select2({
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
            $('#no_kks').val(data.no_kks); // 👈 Ini baris baru!
            $('#nik_kpm_hidden').val(data.id);
        });

        // 2. Auto-Format Rupiah
        $('#nominal_cair').on('keyup', function() {
            var val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(new Intl.NumberFormat('id-ID', {
                style: 'decimal'
            }).format(val));
        });

        // 3. Live Preview Foto & Update Label Custom File
        function readURL(input, targetID, labelID) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(targetID).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);

                // Ubah text label menjadi nama file yang dipilih
                var fileName = input.files[0].name;
                $(input).next('.custom-file-label').html(fileName);
            }
        }

        $('#foto_kpm_kks').change(function() {
            readURL(this, '#prev_kpm');
        });
        $('#foto_bukti_transaksi').change(function() {
            readURL(this, '#prev_bukti');
        });

        // 4. Tangkap Lokasi (Geotagging)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                $('#lat').val(position.coords.latitude);
                $('#lng').val(position.coords.longitude);
            });
        }

        // 5. Submit Form AJAX
        $('#formBansosKKS').on('submit', function(e) {
            e.preventDefault();

            // Validasi Manual: Pastikan NIK sudah dipilih
            if ($('#nik_kpm_hidden').val() === '') {
                Swal.fire('Peringatan', 'Silakan cari dan pilih NIK KPM terlebih dahulu!', 'warning');
                return false;
            }
            // Tambahkan ini di dalam submit handler sebelum proses AJAX
            if (!$("input[name='jenis_bansos']:checked").val()) {
                Swal.fire('Peringatan', 'Silakan pilih Jenis Bansos terlebih dahulu!', 'warning');
                return false;
            }

            // Validasi Manual: Pastikan Tahap Salur (Radio) sudah dipilih
            if (!$("input[name='tahap_salur']:checked").val()) {
                Swal.fire('Peringatan', 'Silakan pilih Tahap Salur (T.1 / T.2 / T.3 / T.4) terlebih dahulu!', 'warning');
                return false;
            }

            // Validasi Manual: Pastikan File Foto 1 sudah diisi
            if ($('#foto_kpm_kks').val() === '') {
                Swal.fire('Peringatan', 'Foto KPM memegang KKS wajib diunggah!', 'warning');
                return false;
            }

            // Validasi Manual: Pastikan File Foto 2 sudah diisi
            if ($('#foto_bukti_transaksi').val() === '') {
                Swal.fire('Peringatan', 'Foto Bukti Transaksi wajib diunggah!', 'warning');
                return false;
            }

            var formData = new FormData(this);

            // Hapus titik pada nominal sebelum kirim
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
                error: function(xhr, status, error) {
                    // Tangkap error jika server bermasalah
                    Swal.fire('Error Sistem', 'Terjadi kesalahan pada server. Cek console.', 'error');
                    console.error(xhr.responseText);
                    $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Dokumentasi');
                }
            });
        });

        // Fitur Tambah Tiga Digit 000
        $('#add_thousands').on('click', function() {
            let currentVal = $('#nominal_cair').val().replace(/[^0-9]/g, ''); // Ambil angka saja

            if (currentVal !== '') {
                let newVal = currentVal + '000'; // Tambahkan 000 di belakang

                // Format ulang ke Rupiah agar tampilan tetap rapi dengan titik
                $('#nominal_cair').val(new Intl.NumberFormat('id-ID', {
                    style: 'decimal'
                }).format(newVal));
            } else {
                // Jika masih kosong, beri angka 0 dulu atau biarkan
                $('#nominal_cair').focus();
            }
        });

    });
</script>

<?= $this->endSection(); ?>