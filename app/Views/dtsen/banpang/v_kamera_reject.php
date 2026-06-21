<?= $this->extend('templates/index') ?>

<?= $this->section('content') ?>
<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-2">
                <div class="col-sm-6">
                    <h5 class="m-0 font-weight-bold text-dark">
                        <a href="<?= base_url('banpang/reject') ?>" class="btn btn-sm btn-secondary mr-3 shadow-sm rounded-circle">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <?= $title; ?>
                    </h5>
                    <small class="text-muted">Arahkan kamera dengan jelas dan terang.</small>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid mb-5">


            <div class="card shadow-sm border-left-primary mb-3">
                <div class="card-body p-3">
                    <h6 class="font-weight-bold text-dark mb-1"><?= esc($kpm['nama']) ?></h6>
                    <div class="text-sm text-muted">
                        <i class="fas fa-id-card mr-1"></i> NIK: <?= esc($kpm['nik']) ?> <br>
                        <i class="fas fa-box mr-1"></i> No PBP: <span class="font-weight-bold text-danger"><?= esc($kpm['no_pbp']) ?></span><br>
                        <i class="fas fa-map-marker-alt mr-1"></i> <?= esc($kpm['alamat_pbp']) ?>
                    </div>
                    <?php if (!empty($kpm['notes'])): ?>
                        <div class="alert alert-warning p-2 mt-2 mb-0 text-xs">
                            <strong>Catatan Reject:</strong> <?= esc($kpm['notes']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <form id="formDokumentasi" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $kpm['id'] ?>">

                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white p-2 border-bottom-0 text-center">
                        <span class="font-weight-bold text-dark"><i class="fas fa-id-badge text-info mr-1"></i> 1. Foto KTP Asli</span>
                    </div>
                    <div class="card-body p-3 text-center">
                        <img id="previewKtp" src="" class="img-fluid rounded mb-3 d-none border border-info" style="max-height: 250px; object-fit: contain;">

                        <input type="file" name="foto_ktp_sinden" id="inputKtp" class="d-none" accept="image/*" capture="environment" required>

                        <button type="button" class="btn btn-lg btn-block btn-outline-info font-weight-bold p-3" onclick="$('#inputKtp').click()">
                            <i class="fas fa-camera fa-2x d-block mb-1"></i> Ambil Foto KTP
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white p-2 border-bottom-0 text-center">
                        <span class="font-weight-bold text-dark"><i class="fas fa-user-check text-success mr-1"></i> 2. Swafoto (KPM + Beras)</span>
                    </div>
                    <div class="card-body p-3 text-center">
                        <img id="previewPbp" src="" class="img-fluid rounded mb-3 d-none border border-success" style="max-height: 250px; object-fit: contain;">

                        <input type="file" name="foto_pbp_sinden" id="inputPbp" class="d-none" accept="image/*" capture="environment" required>

                        <button type="button" class="btn btn-lg btn-block btn-outline-success font-weight-bold p-3" onclick="$('#inputPbp').click()">
                            <i class="fas fa-camera fa-2x d-block mb-1"></i> Ambil Swafoto PBP
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block shadow font-weight-bold p-3 mb-4 rounded-pill" id="btnSimpan">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> Simpan & Kirim
                </button>
            </form>

        </div>
    </section>
</div>

<script>
    $(document).ready(function() {

        // 1. Logika Live Preview Foto KTP
        $('#inputKtp').change(function() {
            let file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewKtp').attr('src', e.target.result).removeClass('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        // 2. Logika Live Preview Swafoto PBP
        $('#inputPbp').change(function() {
            let file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewPbp').attr('src', e.target.result).removeClass('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        // 3. Logika Upload AJAX & Notifikasi SweetAlert2
        $('#formDokumentasi').on('submit', function(e) {
            e.preventDefault();

            // Validasi mandiri apakah kedua foto sudah diisi
            if ($('#inputKtp')[0].files.length === 0 || $('#inputPbp')[0].files.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Lengkap!',
                    text: 'Mohon ambil Foto KTP dan Swafoto PBP terlebih dahulu.',
                    customClass: {
                        popup: 'swal2-sm'
                    }
                });
                return;
            }

            let formData = new FormData(this);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            let btn = $('#btnSimpan');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengunggah...');

            Swal.fire({
                title: 'Mengunggah Data...',
                text: 'Sedang memproses gambar dan mengirim notifikasi WhatsApp. Mohon tunggu.',
                allowOutsideClick: false,
                customClass: {
                    popup: 'swal2-sm'
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('banpang/reject/simpanDokumentasi') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terkirim!',
                            text: res.message,
                            customClass: {
                                popup: 'swal2-sm'
                            },
                            confirmButtonText: 'Kembali ke Daftar'
                        }).then(() => {
                            window.location.href = '<?= base_url('banpang/reject') ?>';
                        });
                    } else {
                        btn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt mr-2"></i> Simpan & Kirim');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message,
                            customClass: {
                                popup: 'swal2-sm'
                            }
                        });
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt mr-2"></i> Simpan & Kirim');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gangguan Jaringan',
                        text: 'Gagal terhubung ke server SINDEN.',
                        customClass: {
                            popup: 'swal2-sm'
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>