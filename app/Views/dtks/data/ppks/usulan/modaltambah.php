<?php
$user = session()->get('role_id');
$nik = session()->get('nik');
$jabatan = session()->get('level');
$desa_id = session()->get('kode_desa');
?>


<style>
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
<div class="container">
    <div class="modal" id="modaltambah" aria-labelledby="modaltambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-primary">
                    <img src="<?= logoApp(); ?>" alt="<?= nameApp(); ?> Logo" class="brand-image" style="width:30px; margin-right: auto">
                    <h5 class="modal-title" id="modaltambahLabel"><?= $title; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="smartwizard">
                        <ul>
                            <li><a href="#step-1">Pendataan<br /><small>Data Individu</small></a></li>
                            <!-- <li><a href="#step-2">Step 2<br /><small>Personal Info</small></a></li> -->
                            <!-- <li><a href="#step-4">Step 3<br /><small>Pengusulan Bansos</small></a></li> -->
                        </ul>
                        <div>
                            <div id="step-1">
                                <div class="row">
                                    <div class="form-group row nopadding mb-2">
                                        <label class="col-4 col-sm-2 col-form-label" for="dataCari">Cari Data</label>
                                        <div class="col-8 col-sm-10">
                                            <select name="dataCari" id="dataCari" class="form-control form-control-sm select2" style="width: 100%;">
                                                <option value='0'>-- Pilih --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-form-label" for="ppks_kategori_id">Kategori</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="ppks_kategori_id" name="ppks_kategori_id" class="form-select form-select-sm">
                                                    <option value="">-- Pilih --</option>
                                                    <?php foreach ($ppks_kategori as $row) { ?>
                                                        <option value="<?= $row['pk_id'] ?>"> <?php echo strtoupper($row['pk_nama_kategori']); ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errorppks_kategori_id"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-form-label" for="ppks_nokk">No. KK</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="number" name="ppks_nokk" id="ppks_nokk" class="form-control form-control-sm" autocomplete="on" autofocus>
                                                <div class="invalid-feedback errorppks_nokk"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="alamat">Alamat</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback erroralamat"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="kelurahan">Desa/Kel.</label>
                                            <div class="col-8 col-sm-8">
                                                <select <?= $user >= 3 ? 'disabled' : ''; ?> id="kelurahan" name="kelurahan" class="form-select form-select-sm">
                                                    <option value="">-- Pilih Desa / Kel. --</option>
                                                    <?php foreach ($desa as $row) { ?>
                                                        <option <?= $desa_id == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errorkelurahan"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="datarw">No. RW</label>
                                            <div class="col-8 col-sm-8">
                                                <select <?= $user >= 4 ? 'disabled' : ''; ?> id="datarw" name="datarw" class="form-select form-select-sm">
                                                    <option value="">-- Pilih --</option>
                                                    <?php foreach ($datarw as $row) { ?>
                                                        <option <?= $jabatan == $row['no_rw'] ? 'selected' : ''; ?> value="<?= $row['no_rw']; ?>"> <?php echo $row['no_rw']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errordatarw"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="datart">No. RT</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="datart" name="datart" class="form-select form-select-sm">
                                                    <option value="">[ Kosong ]</option>
                                                    <?php foreach ($datart as $row) { ?>
                                                        <option value="<?= $row['no_rt'] ?>"> <?php echo $row['no_rt']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errordatart"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="ppks_nama">Nama Lengkap</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="text" name="ppks_nama" id="ppks_nama" class="form-control form-control-sm">
                                                <div class="invalid-feedback errorppks_nama"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">

                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="ppks_nik">NIK</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="number" name="ppks_nik" id="ppks_nik" class="form-control form-control-sm" autocomplete="off">
                                                <div class="invalid-feedback errorppks_nik"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="ppks_tempat_lahir">Tempat Lahir</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="text" name="ppks_tempat_lahir" id="ppks_tempat_lahir" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback errorppks_tempat_lahir"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="ppks_tgl_lahir">Tanggal Lahir</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="date" name="ppks_tgl_lahir" id="ppks_tgl_lahir" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback errorppks_tgl_lahir"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="ppks_jenis_kelamin">Jenis Kelamin</label>
                                            <div class="col-8 col-sm-8">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="chk-Lk" name="ppks_jenis_kelamin" value="1" />
                                                    <label for="chk-Lk" class="form-check-label"> Laki-Laki </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="chk-Pr" name="ppks_jenis_kelamin" value="2" />
                                                    <label for="chk-Pr" class="form-check-label"> Perempuan </label>
                                                </div>
                                                <div class="invalid-feedback errorppks_jenis_kelamin"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="ppks_no_telp">No. Telp/HP</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="text" name="ppks_no_telp" id="ppks_no_telp" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback errorppks_no_telp"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="ppks_status_keberadaan">Status Keberadaan</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="ppks_status_keberadaan" name="ppks_status_keberadaan" class="form-select form-select-sm">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="1">PANTI</option>
                                                    <option value="2">MASYARAKAT</option>
                                                </select>
                                                <div class="invalid-feedback errorppks_status_keberadaan"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="ppks_status_panti">Status Panti</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="ppks_status_panti" name="ppks_status_panti" class="form-select form-select-sm">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="1">BUTUH</option>
                                                    <option value="2">TIDAK</option>
                                                </select>
                                                <div class="invalid-feedback errorppks_status_panti"></div>
                                            </div>
                                        </div>
                                        <input type="datetime-local" name="updated_at" id="" value="<?= date('Y-m-d H:i:s'); ?>" hidden>

                                    </div>
                                </div>
                                <div class="row">
                                    <!-- <div class="col-sm-12 col-12 mt-2"> -->
                                    <!-- <label class="label-center mt-2">Dokumen</label> -->
                                    <!-- <div class="form-group row nopadding"> -->
                                    <div class="col-6 col-sm-6 mb-2">
                                        <img class="img-preview-rmh" src="<?= ppks_foto(null, ''); ?>" style="width: 30px; height: 40px; border-radius: 2px;">
                                        <br>
                                        <label for="ppks_foto">Foto KPM</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-home"></i></span>
                                            </div>
                                            <input type="file" class="form-control form-control-sm" spellcheck="false" name="ppks_foto" id="ppks_foto" onchange="previewImgRmh()" accept="image/*" capture required />
                                        </div>
                                    </div>
                                    <div class="invalid-feedback errorppks_foto"></div>
                                    <!-- </div> -->
                                    <!-- <div class="form-group row nopadding"> -->
                                    <div class="col-6 col-sm-6 mb-2">
                                        <img class="img-preview-rmh foto-dokumen" src="https://assets.pikiran-rakyat.com/crop/0x0:0x0/x/photo/2020/10/01/4026253578.jpg" style="height: 40px; border-radius: 2px;">
                                        <!-- <img class="img-preview-rmh" src="<?= ppks_foto(null, ''); ?>" style="width: 30px; height: 40px; border-radius: 2px;"> -->
                                        <br>
                                        <label for="databansos">Status Bantuan</label>
                                        <div class="input-group">
                                            <select id="databansos" name="databansos" class="form-select form-select-sm">
                                                <option value="">-- Pilih --</option>
                                                <?php foreach ($bansos as $row) { ?>
                                                    <option value="<?= $row['dbj_id'] ?>"> <?php echo $row['dbj_nama_bansos']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback errordatabansos"></div>
                                    <!-- </div> -->
                                    <!-- </div> -->
                                </div>
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
                                    <button type="submit" class="btn btn-block btn-primary btnSimpan" id="btnSimpan">Simpan</button>
                                </div>
                            </div>
                            <!-- <div id="step-2">
                            <div class="row">
                                <div class="col-md-6"> <input type="text" class="form-control" placeholder="Address" required> </div>
                                <div class="col-md-6"> <input type="text" class="form-control" placeholder="City" required> </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6"> <input type="text" class="form-control" placeholder="State" required> </div>
                                <div class="col-md-6"> <input type="text" class="form-control" placeholder="Country" required> </div>
                            </div>
                            </div> -->

                            <!-- <div id="step-4" class="">
                                
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<script>
    $('#dataCari').on('change', (event) => {
        // console.log(event.target.value);
        getData(event.target.value).then(data => {
            $('#ppks_nokk').val(data.nokk);
            $('#kelurahan').val(data.kelurahan);
            $('#datarw').val(data.rw);
            $('#datart').val(data.rt);
            $('#alamat').val(data.alamat);
            $('#ppks_nama').val(data.nama);
            $('#ppks_nik').val(data.du_nik);
            $('#ppks_tempat_lahir').val(data.tempat_lahir);
            $('#ppks_tgl_lahir').val(data.tanggal_lahir);
            if (data.jenis_kelamin == '1') {
                $('#chk-Lk').prop('checked', true);
            }
            if (data.jenis_kelamin == '2') {
                $('#chk-Pr').prop('checked', true);
                $("#status_hamil_div").show();
                // $('#tgl_hamil_div').show();
            }
            if (data.hamil_status == '1') {
                $('#chk-YaHamil').prop('checked', true);
                $('#tgl_hamil_div').show();
                $('#tgl_hamil').val(data.tgl_hamil);
            } else {
                $('#chk-TidakHamil').prop('checked', true);
                $('#tgl_hamil_div').hide();
                $('#tgl_hamil').val('');
            }
            $('#jenis_pekerjaan').val(data.jenis_pekerjaan);
            $('#status_kawin').val(data.status_kawin);
            $('#shdk').val(data.shdk);
            $('#databansos').val(data.program_bansos);
            $('#ibu_kandung').val(data.ibu_kandung);
            if (data.disabil_status == '1') {
                $('#chk-Yes').prop('checked', true);
                $('#disabil_jenis_div').show();
                $('#disabil_jenis').val(data.disabil_jenis);
            } else {
                $('#chk-No').prop('checked', true);
                $('#disabil_jenis_div').hide();
            }
            $('#chk-Yes').val(data.disabil_status);
            $('#disabil_jenis').val(data.disabil_kode);
            $('#du_usia').val(getAge);
            if ($('#du_usia').val() < 18) {
                $('#sta_ortu_div').show();
            } else {
                $('#sta_ortu_div').hide();
            }
        });
    });

    async function getData(id) {
        let response = await fetch('/api_usulan/' + id);
        let data = await response.json();

        return data;
    }

    $(document).ready(function() {
        $('#dataCari').select2({
            dropdownParent: $('#modaltambah'),
            ajax: {
                url: "<?php echo base_url('get_data_penduduk'); ?>",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                    };
                },
                processResults: function(response) {
                    return {
                        results: response.data
                    };
                },
                cache: true
            }
        });

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
                url: "<?= site_url('/tmbUsulPpks'); ?>",
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
                    $('.btnSimpan').html('Simpan');
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
                            table.draw();
                            tabel_padan.draw();
                        }

                        $('#modaltambah').modal('hide');
                        table.draw();
                        tabel_padan.draw();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });

        $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'dots',
            autoAdjustHeight: true,
            transitionEffect: 'fade',
            showStepURLhash: false,
        });

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
            $("#disabil_jenis_div").show();
        } else {
            $("#disabil_jenis_div").hide();
        };

        if ($("#chk-No").is(":checked")) {
            $("#disabil_status_div").hide();
        } else {
            $("#disabil_status_div").show();
        };

        if ($("#du_usia").val() < 18) {
            $('#du_so_id_div').show();
        } else {
            $('#du_so_id_div').hide();
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            // z.innerHTML = "Geolokasi Tidak Didukung oleh Browser Ini";
            alert("Geolokasi Tidak Didukung oleh Browser Ini");
        }

        $('#tanggal_lahir').change(function() {
            var dob = new Date(document.getElementById('tanggal_lahir').value);
            var today = new Date();
            var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
            document.getElementById('du_usia').value = age;
        });

    });

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

    function getAge() {
        var dob = new Date(document.getElementById('tanggal_lahir').value);
        var today = new Date();
        var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
        // var du_usia = document.getElementById('du_usia').value;

        return age;
    }

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