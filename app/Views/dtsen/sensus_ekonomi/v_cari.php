<?= $this->extend('templates/index') ?> <?= $this->section('content') ?>
<style>
    /* 🚀 Modifikasi SweetAlert2 agar lebih mungil dan nyaman di layar HP */
    .swal2-popup.swal2-sm {
        font-size: 0.85rem !important;
        padding: 1rem;
        width: 85% !important;
        max-width: 350px !important;
    }

    .swal2-title {
        font-size: 1.2rem !important;
    }
</style>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12 text-center">
                    <h2 class="m-0 font-weight-bold text-primary"><i class="fas fa-search-location mr-2"></i> Sensus Ekonomi 2026</h2>
                    <p class="text-muted">Pencarian Validasi Data Keluarga</p>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid pt-2">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">

                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-body p-4 text-center">
                            <h5 class="font-weight-bold mb-3">Masukkan Nomor KK</h5>
                            <form id="formCariKk">
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control form-control-lg" id="no_kk" name="no_kk" placeholder="Ketik 16 Digit No. KK..." required autofocus autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary btn-lg px-4" id="btnCari">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block text-left"><i class="fas fa-info-circle text-info"></i> Hanya mencari data yang berada di dalam wilayah kerja Anda.</small>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow border-success d-none mt-3" id="cardHasil">
                        <div class="card-header bg-success text-white p-2">
                            <h6 class="card-title m-0 font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Data Ditemukan</h6>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="35%" class="text-muted align-middle">No. KK</td>
                                    <td width="5%" class="align-middle">:</td>
                                    <td id="res_no_kk" class="font-weight-bold align-middle"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted align-middle">Kepala Keluarga</td>
                                    <td class="align-middle">:</td>
                                    <td id="res_kepala" class="font-weight-bold text-primary align-middle"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted align-top">Alamat</td>
                                    <td class="align-top">:</td>
                                    <td id="res_alamat" class="align-top text-sm"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted align-middle">Kategori Desil</td>
                                    <td class="align-middle">:</td>
                                    <td class="align-middle">
                                        <span id="res_desil" class="badge"></span>
                                    </td>
                                </tr>
                            </table>
                            <hr class="mt-2 mb-3">
                            <a href="#" id="btnLihatDetail" class="btn btn-block btn-success shadow-sm font-weight-bold">
                                Buka Detail Keluarga <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#formCariKk').submit(function(e) {
            e.preventDefault();

            let noKk = $('#no_kk').val();
            let btn = $('#btnCari');

            // Validasi minimal 16 digit agar rapi
            if (noKk.length < 16) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Valid',
                    text: 'Nomor KK harus 16 digit!',
                    customClass: {
                        popup: 'swal2-sm'
                    }
                });
                return;
            }

            // Tampilan Loading
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            $('#cardHasil').addClass('d-none'); // Sembunyikan hasil lama jika ada

            $.ajax({
                url: '<?= base_url('sensus-ekonomi/cariKk') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    no_kk: noKk,
                    // Pastikan CSRF Token CodeIgniter 4 disertakan agar aman
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    btn.prop('disabled', false).html('<i class="fas fa-search"></i>');

                    if (response.status === 'success') {
                        // 🚀 PERBAIKAN: Deklarasikan variabel 'd' dengan mengambil dari response.data
                        let d = response.data;

                        // Populate data ke dalam Card
                        $('#res_no_kk').text(d.no_kk);
                        $('#res_kepala').text(d.kepala_keluarga);
                        $('#res_alamat').text(d.alamat);
                        $('#btnLihatDetail').attr('href', d.link_detail);

                        // 🚀 LOGIKA PEWARNAAN BADGE DESIL
                        let desilText = d.kategori_desil || 'Belum Ditentukan';
                        let badgeClass = 'badge bg-secondary'; // Warna default abu-abu

                        // Ekstrak angka dari teks (misal "Desil 3" akan diambil angka 3-nya saja)
                        let angkaDesil = parseInt(desilText.replace(/\D/g, ''));

                        if (!isNaN(angkaDesil)) {
                            if (angkaDesil >= 1 && angkaDesil <= 4) {
                                badgeClass = 'badge bg-success'; // 1-4: Hijau
                            } else if (angkaDesil === 5) {
                                badgeClass = 'badge bg-warning text-dark'; // 5: Kuning
                            } else if (angkaDesil >= 6 && angkaDesil <= 10) {
                                badgeClass = 'badge bg-danger'; // 6-10: Merah
                            }
                        }

                        // Terapkan class warna dan teksnya ke dalam span
                        $('#res_desil').attr('class', badgeClass + ' px-2 py-1').text(desilText);

                        // Tampilkan Card Hasil dengan animasi transisi
                        $('#cardHasil').removeClass('d-none').hide().fadeIn('fast');

                    } else {
                        // Sembunyikan card jika sebelumnya terbuka, lalu tampilkan alert
                        $('#cardHasil').addClass('d-none');

                        // Alert jika KK tidak ada / di luar wilayah
                        Swal.fire({
                            icon: 'error',
                            title: 'Pencarian Gagal',
                            text: response.message,
                            customClass: {
                                popup: 'swal2-sm'
                            }
                        });
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-search"></i>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gangguan Jaringan',
                        text: 'Gagal terhubung ke server Sinden.',
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