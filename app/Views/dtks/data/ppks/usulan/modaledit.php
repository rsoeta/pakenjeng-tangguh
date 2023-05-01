<?php
$user = session()->get('role_id');
$nik = session()->get('nik');
$jabatan = session()->get('level');
$desa_id = session()->get('kode_desa');
?>

<style>
    input.larger {
        width:
            150px;
        height:
            15px;
    }

    .foto-dokumen {
        width: 60px;
        border-radius: 2px;
        /* style="width: 30px; height: 40px; border-radius: 2px; */
    }

    /* start modal dialog multi-step form wizard  */
    body {
        background-color:
            #eee
    }

    .form-control:focus {
        color:
            #495057;
        background-color:
            #fff;
        border-color:
            #80bdff;
        outline:
            0;
        box-shadow:
            0 0 0 0rem rgba(0,
                123,
                255,
                .25)
    }

    .btn-secondary:focus {
        box-shadow:
            0 0 0 0rem rgba(108,
                117,
                125,
                .5)
    }

    .close:focus {
        box-shadow:
            0 0 0 0rem rgba(108,
                117,
                125,
                .5)
    }

    .mt-200 {
        margin-top:
            200px
    }
</style>

<link href="<?= base_url('assets/dist/css/smart_wizard.min.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?= base_url('assets/dist/css/smart_wizard_theme_dots.min.css'); ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?= base_url('assets/dist/js/jquery.smartWizard.min.js'); ?>"></script>
<!-- end modal dialog multi-step form wizard -->


<!-- Modal -->
<?= form_open_multipart('', ['class' => 'formsimpan']) ?>
<?= csrf_field(); ?>
<div class="modal fade" id="modaledit" tabindex="-1" role="dialog" aria-labelledby="modaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-warning">
                <img src="<?= logoApp(); ?>" alt="<?= nameApp(); ?> Logo" class="brand-image" style="width:30px; margin-right: auto">
                <h5 class="modal-title" id="exampleModalLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="smartwizard">
                    <ul>
                        <li><a href="#step-1">Pendataan<br /><small>Data Individu</small></a></li>
                        <!-- <!-- <li><a href="#step-2">Step 2<br /><small>Personal Info</small></a></li> -->
                        <!-- <li><a href="#step-3">Step 3<br /><small>Survey Kriteria</small></a></li> -->
                        <!-- <li><a href="#step-4">Step 2<br /><small>Pengusulan Bansos</small></a></li> -->
                    </ul>
                    <div>
                        <div id="step-1">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group row nopadding" hidden>
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_id">ID</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="ppks_id" id="ppks_id" class="form-control form-control-sm" value="<?= $ppks_id; ?>" autofocus>
                                            <div class="invalid-feedback errorppks_id"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_nik">NIK</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="number" name="ppks_nik" id="ppks_nik" class="form-control form-control-sm" value="<?= $ppks_nik; ?>" autocomplete="off">
                                            <div class="invalid-feedback errorppks_nik"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_nama">Nama Lengkap</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="ppks_nama" id="ppks_nama" class="form-control form-control-sm" value="<?= $ppks_nama; ?>">
                                            <div class="invalid-feedback errorppks_nama"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_nokk">No. KK</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="number" name="ppks_nokk" id="ppks_nokk" class="form-control form-control-sm" value="<?= $ppks_nokk; ?>" autocomplete="on">
                                            <div class="invalid-feedback errorppks_nokk"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_tempat_lahir">Tempat Lahir</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="ppks_tempat_lahir" id="ppks_tempat_lahir" class="form-control form-control-sm" value="<?= $ppks_tempat_lahir; ?>">
                                            <div class="invalid-feedback errorppks_tempat_lahir"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_tgl_lahir">Tanggal Lahir</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="date" name="ppks_tgl_lahir" id="ppks_tgl_lahir" class="form-control form-control-sm" value="<?= $ppks_tgl_lahir; ?>">
                                            <div class="invalid-feedback errorppks_tgl_lahir"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding" style="display: none;">
                                        <label class="col-4 col-sm-4 col-form-label" for="du_usia">Usia</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="number" name="du_usia" id="du_usia" class="form-control form-control-sm" value="" readonly>
                                            <div class="invalid-feedback errorusia"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_no_telp">No. Telp/HP</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="ppks_no_telp" id="ppks_no_telp" class="form-control form-control-sm" value="<?= $ppks_no_telp; ?>">
                                            <div class="invalid-feedback errorppks_no_telp"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_jenis_kelamin">Jenis Kelamin</label>
                                        <div class="col-8 col-sm-8">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="chk-Lk" name="ppks_jenis_kelamin" <?= $ppks_jenis_kelamin == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="chk-Lk" class="form-check-label"> Laki-Laki </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="chk-Pr" name="ppks_jenis_kelamin" <?= $ppks_jenis_kelamin == '2' ? 'checked' : ''; ?> value="2" />
                                                <label for="chk-Pr" class="form-check-label"> Perempuan </label>
                                            </div>
                                            <div class="invalid-feedback errorppks_jenis_kelamin"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_status_keberadaan">Status Keberadaan</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="ppks_status_keberadaan" name="ppks_status_keberadaan" class="form-select form-select-sm">
                                                <option value="">-- Pilih --</option>
                                                <option <?= $ppks_status_keberadaan == 1 ? 'selected' : ''; ?> value="1">PANTI</option>
                                                <option <?= $ppks_status_keberadaan == 2 ? 'selected' : ''; ?> value="2">MASYARAKAT</option>
                                            </select>
                                            <div class="invalid-feedback errorppks_status_keberadaan"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ppks_status_panti">Status Panti</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="ppks_status_panti" name="ppks_status_panti" class="form-select form-select-sm">
                                                <option value="">-- Pilih --</option>
                                                <option <?= $ppks_status_panti == 1 ? 'selected' : ''; ?> value="1">BUTUH</option>
                                                <option <?= $ppks_status_panti == 2 ? 'selected' : ''; ?> value="2">TIDAK</option>
                                            </select>
                                            <div class="invalid-feedback errorppks_status_panti"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="kelurahan">Desa/Kel.</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user >= 3 ? 'disabled' : ''; ?> id="kelurahan" name="kelurahan" class="form-select form-select-sm">
                                                <option value="">-- Pilih Desa / Kel. --</option>
                                                <?php foreach ($desa as $row) { ?>
                                                    <option <?= $desa_id == $row['id'] ? ' selected' : ''; ?> value="<?= $row['id'] ?>"> <?= $row['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errorkelurahan"></div>
                                        </div>
                                    </div>

                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="datarw">No. RW</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user >= 4 ? 'disabled' : ''; ?> id="datarw" name="datarw" class="form-select form-select-sm">
                                                <option value="">-- Pilih RW --</option>
                                                <?php foreach ($rw as $row) { ?>
                                                    <option <?= $datarw == $row['no_rw'] ? 'selected' : ''; ?> value="<?= $row['no_rw'] ?>"> <?= $row['no_rw']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errordatarw"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="datart">No. RT</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="datart" name="datart" class="form-select form-select-sm">
                                                <option value="">-- Pilih RT --</option>
                                                <?php foreach ($rt as $row) { ?>
                                                    <option <?php if ($datart == $row['no_rt']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['no_rt'] ?>"> <?php echo $row['no_rt']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errordatart"></div>
                                        </div>
                                    </div>

                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="alamat">Alamat</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="<?= $alamat; ?>">
                                            <div class="invalid-feedback erroralamat"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- <div class="col-sm-12 col-12 mt-2"> -->
                                <!-- <label class="label-center mt-2">Dokumen</label> -->
                                <!-- <div class="form-group row nopadding"> -->
                                <div class="col-6 col-sm-6 mb-2">
                                    <a download="<?= $ppks_foto; ?>" href="<?= ppks_foto($ppks_foto, ''); ?>">
                                        <img class="img-preview-rmh foto-dokumen" src="<?= ppks_foto($ppks_foto, ''); ?>">
                                    </a>
                                    <br>
                                    <label for="ppks_foto">Foto KPM</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-home"></i></span>
                                        </div>
                                        <input type="file" class="form-control form-control-sm" spellcheck="false" name="ppks_foto" id="ppks_foto" onchange="previewImgRmh()" accept="image/*" capture />
                                    </div>
                                </div>
                                <div class="invalid-feedback errorppks_foto"></div>
                                <div class="col-6 col-sm-6 mb-2">
                                    <!-- <div class="form-group row nopadding"> -->
                                    <img class="img-preview-rmh foto-dokumen" src="https://assets.pikiran-rakyat.com/crop/0x0:0x0/x/photo/2020/10/01/4026253578.jpg" style="height: 80px; width: auto;">
                                    <br>
                                    <label for="databansos">Status Program</label>
                                    <div class="input-group">
                                        <select id="databansos" name="databansos" class="form-select form-select-sm">
                                            <option value="">-- Pilih --</option>
                                            <?php foreach ($bansos as $row) { ?>
                                                <option <?php if ($databansos == $row['dbj_id']) {
                                                            echo 'selected';
                                                        } ?> value="<?= $row['dbj_id'] ?>"> <?php echo $row['dbj_nama_bansos']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <div class="invalid-feedback errordatabansos"></div>
                                    </div>
                                    <!-- </div> -->
                                </div>
                                <!-- </div> -->
                                <!-- </div> -->
                                <div class="col-sm-12 col-12 mt-2">
                                    <label class="label-center mt-2">Koordinat</label>
                                    <div class="form-group row nopadding">

                                        <div class="col-sm-6 col-6">
                                            <input type="text" class="form-control form-control-sm mb-2" placeholder="Lat" spellcheck="false" id="latitude" name="du_latitude" readonly required>
                                            <div class="invalid-feedback errordu_latitude"></div>
                                        </div>
                                        <div class="col-sm-6 col-6">
                                            <input type="text" class="form-control form-control-sm mb-2" placeholder="Long" spellcheck="false" id="longitude" name="du_longitude" readonly required>
                                            <div class="invalid-feedback errordu_longitude"></div>
                                        </div>
                                        <div class="col-sm-1 col-1" hidden>
                                            <button type="button" class="btn btn-outline-primary" onclick="getLocation()"><i class="fas fa-map-marked-alt"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer mt-3 justify-content-between">
                                    <input type="datetime-local" name="ppks_updated_at" id="" value="<?= date('Y-m-d H:i:s'); ?>" hidden>
                                    <button type="submit" class="btn btn-block btn-warning btnSimpan">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= form_close(); ?>

<script>
    $(document).ready(function() {

        $('.btnSimpan').click(function(e) {
            e.preventDefault();
            let $kelurahan = $('#kelurahan').removeAttr('disabled', '');
            let $datarw = $('#datarw').removeAttr('disabled', '');
            setTimeout(function() {
                $kelurahan.attr('disabled', true);
                $datarw.attr('disabled', true);
            }, 500);
            let form = $('.formsimpan')[0];
            let data = new FormData(form);
            $.ajax({
                type: "POST",
                url: '<?= base_url('/updatePpks') ?>',
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
                beforeSend: function() {
                    $('.btnSimpan').attr('disable', 'disabled');
                    $('.btnSimpan').html('<i class="fa fa-spin fa-spinner"></i>');
                },
                complete: function() {
                    $('.btnSimpan').removeAttr('disable');
                    $('.btnSimpan').html('Update');
                },
                success: function(response) {
                    if (response.error) {

                        if (response.error.ppks_nik) {
                            $('#ppks_nik').addClass('is-invalid');
                            $('.errorppks_nik').html(response.error.ppks_nik);
                        } else {
                            $('#ppks_nik').removeClass('is-invalid');
                            $('.errorppks_nik').html('');
                        }

                        if (response.error.ppks_nokk) {
                            $('#ppks_nokk').addClass('is-invalid');
                            $('.errorppks_nokk').html(response.error.ppks_nokk);
                        } else {
                            $('#ppks_nokk').removeClass('is-invalid');
                            $('.errorppks_nokk').html('');
                        }

                        if (response.error.ppks_nama) {
                            $('#ppks_nama').addClass('is-invalid');
                            $('.errorppks_nama').html(response.error.ppks_nama);
                        } else {
                            $('#ppks_nama').removeClass('is-invalid');
                            $('.errorppks_nama').html('');
                        }

                        if (response.error.ppks_tempat_lahir) {
                            $('#ppks_tempat_lahir').addClass('is-invalid');
                            $('.errorppks_tempat_lahir').html(response.error.ppks_tempat_lahir);
                        } else {
                            $('#ppks_tempat_lahir').removeClass('is-invalid');
                            $('.errorppks_tempat_lahir').html('');
                        }

                        if (response.error.ppks_tgl_lahir) {
                            $('#ppks_tgl_lahir').addClass('is-invalid');
                            $('.errorppks_tgl_lahir').html(response.error.ppks_tgl_lahir);
                        } else {
                            $('#ppks_tgl_lahir').removeClass('is-invalid');
                            $('.errorppks_tgl_lahir').html('');
                        }

                        if (response.error.ppks_jenis_kelamin) {
                            $('#ppks_jenis_kelamin').addClass('is-invalid');
                            $('.errorppks_jenis_kelamin').html(response.error.ppks_jenis_kelamin);
                        } else {
                            $('#ppks_jenis_kelamin').removeClass('is-invalid');
                            $('.errorppks_jenis_kelamin').html('');
                        }

                        if (response.error.ppks_kategori_id) {
                            $('#ppks_kategori_id').addClass('is-invalid');
                            $('.errorppks_kategori_id').html(response.error.ppks_kategori_id);
                        } else {
                            $('#ppks_kategori_id').removeClass('is-invalid');
                            $('.errorppks_kategori_id').html('');
                        }

                        if (response.error.ppks_no_telp) {
                            $('#ppks_no_telp').addClass('is-invalid');
                            $('.errorppks_no_telp').html(response.error.ppks_no_telp);
                        } else {
                            $('#ppks_no_telp').removeClass('is-invalid');
                            $('.errorppks_no_telp').html('');
                        }

                        if (response.error.kelurahan) {
                            $('#kelurahan').addClass('is-invalid');
                            $('.errorkelurahan').html(response.error.kelurahan);
                        } else {
                            $('#kelurahan').removeClass('is-invalid');
                            $('.errorkelurahan').html('');
                        }

                        if (response.error.datarw) {
                            $('#datarw').addClass('is-invalid');
                            $('.errordatarw').html(response.error.datarw);
                        } else {
                            $('#datarw').removeClass('is-invalid');
                            $('.errordatarw').html('');
                        }

                        if (response.error.datart) {
                            $('#datart').addClass('is-invalid');
                            $('.errordatart').html(response.error.datart);
                        } else {
                            $('#datart').removeClass('is-invalid');
                            $('.errordatart').html('');
                        }

                        if (response.error.alamat) {
                            $('#alamat').addClass('is-invalid');
                            $('.erroralamat').html(response.error.alamat);
                        } else {
                            $('#alamat').removeClass('is-invalid');
                            $('.erroralamat').html('');
                        }

                        if (response.error.ppks_status_keberadaan) {
                            $('#ppks_status_keberadaan').addClass('is-invalid');
                            $('.errorppks_status_keberadaan').html(response.error.ppks_status_keberadaan);
                        } else {
                            $('#ppks_status_keberadaan').removeClass('is-invalid');
                            $('.errorppks_status_keberadaan').html('');
                        }

                        if (response.error.databansos) {
                            $('#databansos').addClass('is-invalid');
                            $('.errordatabansos').html(response.error.databansos);
                        } else {
                            $('#databansos').removeClass('is-invalid');
                            $('.errordatabansos').html('');
                        }

                        if (response.error.ppks_status_panti) {
                            $('#ppks_status_panti').addClass('is-invalid');
                            $('.errorppks_status_panti').html(response.error.ppks_status_panti);
                        } else {
                            $('#ppks_status_panti').removeClass('is-invalid');
                            $('.errorppks_status_panti').html('');
                        }

                        if (response.error.ppks_foto) {
                            $('#ppks_foto').addClass('is-invalid');
                            $('.errorppks_foto').html(response.error.ppks_foto);
                        } else {
                            $('#ppks_foto').removeClass('is-invalid');
                            $('.errorppks_foto').html('');
                        }

                        if (response.error.du_latitude) {
                            $('#du_latitude').addClass('is-invalid');
                            $('.errordu_latitude').html(response.error.du_latitude);
                        } else {
                            $('#du_latitude').removeClass('is-invalid');
                            $('.errordu_latitude').html('');
                        }

                        if (response.error.du_longitude) {
                            $('#du_longitude').addClass('is-invalid');
                            $('.errordu_longitude').html(response.error.du_longitude);
                        } else {
                            $('#du_longitude').removeClass('is-invalid');
                            $('.errordu_longitude').html('');
                        }

                    } else {
                        if (response.sukses) {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })

                            Toast.fire({
                                icon: 'success',
                                title: response.sukses,
                            });
                            // window.location.reload();
                            $('#modaledit').modal('hide');
                            table.draw();
                            tabel_padan.draw();
                        }
                        $('#modaledit').modal('hide');
                        table.draw();
                        tabel_padan.draw();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }

            });
        })

        $('#datarw').change(function() {
            var desa = $('#kelurahan').val();
            var no_rw = $('#datarw').val();
            var action = 'get_rt';
            if (no_rw != '') {
                $.ajax({
                    url: "<?php echo base_url('action'); ?>",
                    method: "POST",
                    data: {
                        desa: desa,
                        no_rw: no_rw,
                        action: action
                    },
                    dataType: "JSON",
                    success: function(data) {
                        var html = '<option value="">-Pilih-</option>';
                        for (var count = 0; count < data.length; count++) {
                            html += '<option value="' + data[count].no_rt + '">' + data[count].no_rt + '</option>';
                        }
                        $('#datart').html(html);
                    }
                });
            } else {
                $('#datart').val('');
            }
        });

        $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'dots',
            autoAdjustHeight: true,
            transitionEffect: 'fade',
            showStepURLhash: false,
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            // z.innerHTML = "Geolokasi Tidak Didukung oleh Browser Ini";
            alert("Geolokasi Tidak Didukung oleh Browser Ini");
        }

        if ($("#chk-Pr").is(":checked")) {
            $("#status_hamil_div").show();
        } else {
            $("#status_hamil_div").hide();
            $("#tgl_hamil_div").hide();
        };

        if ($("#chk-YaHamil").is(":checked")) {
            // $("#tgl_hamil_div").show();
            $('#tgl_hamil_div').show().find(':input').attr('required', true);
        } else {
            $('#tgl_hamil_div').hide().find(':input').attr('required', false);
            // $("#tgl_hamil_div").hide();
        };

        if ($("#chk-Yes").is(":checked")) {
            $("#disabil_status_div").show();
            $("#disabil_jenis_div").show();
            // } else {
            //     $("#disabil_jenis_div").hide();
        };

        // if ($("#chk-No").is(":checked")) {
        //     $("#disabil_status_div").hide();
        // } else {
        //     $("#disabil_status_div").show();
        // };

        // $('#du_usia').val(getAge);
        // if ($('#du_usia').val() < 18) {
        //     $('#du_so_id_div').show();
        // } else {
        //     $('#du_so_id_div').hide();
        // }

    });

    $(function() {
        $("input[name='jenis_kelamin']").click(function() {
            if ($("#chk-Pr").is(":checked")) {
                $("#status_hamil_div").show();
            } else {
                $("#status_hamil_div").hide();
                $("#tgl_hamil_div").hide();
            }
        });

        $("input[name='status_hamil']").click(function() {
            if ($("#chk-YaHamil").is(":checked")) {
                // $("#tgl_hamil_div").show();
                $('#tgl_hamil_div').show().find(':input').attr('required', true);
            } else {
                $('#tgl_hamil_div').hide().find(':input').attr('required', false);
                // $("#tgl_hamil_div").hide();
            }
        });

        $("input[name='disabil_status']").click(function() {
            if ($("#chk-Yes").is(":checked")) {
                $("#disabil_jenis_div").show();
            } else {
                $("#disabil_jenis_div").hide();
            }
        });

    });

    // function getAge() {
    //     var dob = new Date(document.getElementById('tanggal_lahir').value);
    //     var today = new Date();
    //     var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
    //     // var du_usia = document.getElementById('du_usia').value;

    //     return age;
    // }

    var x = document.getElementById("latitude");
    var y = document.getElementById("longitude");
    var z = document.getElementById("z");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            // z.innerHTML = "Geolokasi Tidak Didukung oleh Browser Ini";
            alert("Geolokasi Tidak Didukung oleh Browser Ini");
        }
    }

    function showPosition(position) {
        $("#latitude").val(`${position.coords.latitude}`);
        $("#longitude").val(`${position.coords.longitude}`);
        // x.innerHTML = position.coords.latitude;
        // y.innerHTML = position.coords.longitude;
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert("Pengguna menolak permintaan geolokasi.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Informasi lokasi tidak tersedia.");
                break;
            case error.TIMEOUT:
                alert("Permintaan untuk menghitung waktu lokasi pengguna.");
                break;
            case error.UNKNOWN_ERROR:
                alert("Terjadi kesalahan yang tidak diketahui.");
                break;
        }
    }
</script>