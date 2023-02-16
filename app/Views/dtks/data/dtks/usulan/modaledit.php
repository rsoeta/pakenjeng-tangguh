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
                        <li><a href="#step-1">Step 1<br /><small>Data Individu</small></a></li>
                        <!-- <!-- <li><a href="#step-2">Step 2<br /><small>Personal Info</small></a></li> -->
                        <li><a href="#step-3">Step 3<br /><small>Survey Kriteria</small></a></li>
                        <li><a href="#step-4">Step 2<br /><small>Pengusulan Bansos</small></a></li>
                    </ul>
                    <div>
                        <div id="step-1">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group row nopadding" hidden>
                                        <label class="col-4 col-sm-4 col-form-label" for="id">ID</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="id" id="id" class="form-control form-control-sm" value="<?= $id; ?>" autofocus>
                                            <div class="invalid-feedback errorid"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="nik">NIK</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="number" name="nik" id="nik" class="form-control form-control-sm" value="<?= $du_nik; ?>" autocomplete="off" <?= $user > 3 ? 'readonly="on"' : ''; ?>>
                                            <div class="invalid-feedback errornik"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="nama">Nama</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="nama" id="nama" class="form-control form-control-sm" value="<?= $nama; ?>">
                                            <div class="invalid-feedback errornama"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="nokk">No. KK</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="number" name="nokk" id="nokk" class="form-control form-control-sm" value="<?= $nokk; ?>" autocomplete="on">
                                            <div class="invalid-feedback errornokk"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="tempat_lahir">Tempat Lahir</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control form-control-sm" value="<?= $tempat_lahir; ?>">
                                            <div class="invalid-feedback errortempat_lahir"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="tanggal_lahir">tanggal lahir</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control form-control-sm" value="<?= $tanggal_lahir; ?>">
                                            <div class="invalid-feedback errortanggal_lahir"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ibu_kandung">Ibu Kandung</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="ibu_kandung" id="ibu_kandung" class="form-control form-control-sm" value="<?= $ibu_kandung; ?>">
                                            <div class="invalid-feedback erroribu_kandung"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="jenis_kelamin">Jenis Kelamin</label>
                                        <div class="col-8 col-sm-8">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="chk-Lk" name="jenis_kelamin" <?= $jenis_kelamin == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="chk-Lk" class="form-check-label"> Laki-Laki </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="chk-Pr" name="jenis_kelamin" <?= $jenis_kelamin == '2' ? 'checked' : ''; ?> value="2" />
                                                <label for="chk-Pr" class="form-check-label"> Perempuan </label>
                                            </div>
                                            <div class="invalid-feedback errorjenis_kelamin"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="jenis_pekerjaan">Pekerjaan</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="jenis_pekerjaan" name="jenis_pekerjaan" class="form-select form-select-sm">
                                                <option value="">-- Pilih Jenis Pekerjaan --</option>
                                                <?php foreach ($pekerjaan as $row) { ?>
                                                    <option <?php if ($jenis_pekerjaan == $row['idPekerjaan']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['idPekerjaan'] ?>"> <?php echo $row['JenisPekerjaan']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errorjenis_pekerjaan"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="status_kawin">Status</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="status_kawin" name="status_kawin" class="form-select form-select-sm">
                                                <option value="">-- Pilih Status Perkawinan --</option>
                                                <?php foreach ($statusKawin as $row) { ?>
                                                    <option <?php if ($status_kawin == $row['idStatus']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['idStatus'] ?>"> <?php echo $row['StatusKawin']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errorstatus_kawin"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding" <?php if ($user != 1) {
                                                                                echo 'hidden';
                                                                            } ?>>
                                        <label class="col-4 col-sm-4 col-form-label" for="kelurahan">Desa/Kelurahan</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="kelurahan" name="kelurahan" class="form-select form-select-sm">
                                                <option value="">-- Pilih Desa / Kelurahan --</option>
                                                <?php foreach ($desa as $row) { ?>
                                                    <option <?php if ($desa_id == $row['id']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errorkelurahan"></div>
                                        </div>
                                    </div>

                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="alamat">Alamat</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="<?= $alamat; ?>">
                                            <div class="invalid-feedback erroralamat"></div>
                                        </div>
                                    </div>

                                    <div class="form-group row nopadding" <?php if ($user > 3) {
                                                                                echo 'hidden';
                                                                            } ?>>
                                        <label class="col-4 col-sm-4 col-form-label" for="datarw">No. RW</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="datarw" name="datarw" class="form-select form-select-sm">
                                                <option value="">-- Pilih RW --</option>
                                                <?php foreach ($rw as $row) { ?>
                                                    <option <?php if ($datarw == $row['no_rw']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['no_rw'] ?>"> <?php echo $row['no_rw']; ?></option>
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
                                        <label class="col-4 col-sm-4 col-form-label" for="shdk">SHDK</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="shdk" name="shdk" class="form-select form-select-sm">
                                                <option value="">-- Status Hubungan dalam Keluarga --</option>
                                                <?php foreach ($shdk as $row) { ?>
                                                    <option <?php if ($stahub == $row['id']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['id']; ?>"><?= $row['jenis_shdk']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errorShdk"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="step-3" class="">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <b><em>*Keterangan :</em></b><br />
                                        <input type="checkbox" onclick="return false" checked /> = Ya
                                        <br />
                                        <input type="checkbox" onclick="return false" /> = Tidak
                                    </div>
                                    <hr>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk0">Apakah memiliki tempat tinggal sendiri?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk0" name="sk0" <?= $sk0 == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="sk0" class="form-check-label"> Ya</label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk1">Apakah pernah khawatir tidak makan dalam setahun terakhir?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk1" name="sk1" <?= $sk1 == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="sk1" class="form-check-label"> Ya</label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk2">Apakah ada pengeluaran untuk pakaian selama 1 (satu) tahun terakhir?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk2" name="sk2" <?= $sk2 == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="sk2" class="form-check-label"> Ya</label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk3">Apakah tempat tinggal sebagian besar berdinding bambu, kawat, papan, kayu, terpal, kardus, tembok tanpa plester, rumbia, atau seng?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk3" name="sk3" <?= $sk3 == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="sk3" class="form-check-label"> Ya</label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk4">Apakah sumber penerangan berasal dari listrik 450 watt?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk4" name="sk4" <?= $sk4 == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="sk4" class="form-check-label"> Ya</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk5">Apakah kepala keluarga atau pengurus keluarga masih bekerja?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk5" name="sk5" <?= $sk5 == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="sk5" class="form-check-label"> Ya</label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk6">Apakah pengeluaran kebutuhan makan lebih besar dari setengah total pengeluaran?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk6" name="sk6" <?= $sk6 == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="sk6" class="form-check-label"> Ya</label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk7">Apakah tempat tinggal sebagian besar berlantai tanah dan/atau plesteran?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk7" name="sk7" <?= $sk7 == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="sk7" class="form-check-label"> Ya</label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk8">Apakah tempat tinggal terdapat jamban sendiri?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk8" name="sk8" <?= $sk8 == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="sk8" class="form-check-label"> Ya</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div id="step-4" class="">
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-3 col-form-label" for="databansos">Program</label>
                                        <div class="col-8 col-sm-9">
                                            <select id="databansos" name="databansos" class="form-select form-select-sm">
                                                <option value="">-- Pilih Program --</option>
                                                <?php foreach ($bansos as $row) { ?>
                                                    <option <?php if ($databansos == $row['dbj_id']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['dbj_id'] ?>"> <?php echo $row['dbj_ket_bansos']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errordatabansos"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group row nopadding" id="disabil_status_div" style="display: none">
                                        <label for="disabil_status" class="col-4 col-sm-3 col-form-label">Disabilitas</label>
                                        <div class="col-8 col-sm-9">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="chk-Yes" name="disabil_status" <?= $disabil_status == 1 ? 'checked' : ''; ?> value="1" />
                                                <label for="chk-Yes" class="form-check-label"> Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="chk-No" name="disabil_status" <?= $disabil_status == 2 ? 'checked' : ''; ?> value="2" />
                                                <label for="chk-No" class="form-check-label"> Tidak</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding" id="disabil_jenis_div" style="display: none">
                                        <label for="disabil_jenis" class="col-4 col-sm-3 col-form-label">Jenis</label>
                                        <div class="col-8 col-sm-9">
                                            <select id="disabil_jenis" name="disabil_jenis" class="form-select form-select-sm">
                                                <option value="">-- Pilih Jenis Disabilitas --</option>
                                                <?php foreach ($DisabilitasJenisModel as $row) { ?>
                                                    <option <?= $disabil_jenis == $row['dj_id'] ? 'selected' : ''; ?> value="<?= $row['dj_id'] ?>"> <?php echo $row['dj_keterangan']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group row nopadding" id="status_hamil_div" style="display: none">
                                        <label class="col-4 col-sm-3 col-form-label" for="status_hamil">Hamil</label>
                                        <div class="col-8 col-sm-9">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="chk-YaHamil" name="status_hamil" <?= $status_hamil == 1 ? 'checked' : ''; ?> value="1" />
                                                <label for="chk-YaHamil" class="form-check-label"> Ya </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="chk-TidakHamil" name="status_hamil" <?= $status_hamil == 2 ? 'checked' : ''; ?> value="2" />
                                                <label for="chk-TidakHamil" class="form-check-label"> Tidak </label>
                                            </div>
                                            <div class="invalid-feedback errorstatus_hamil"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding" id="tgl_hamil_div" style="display: none;">
                                        <label class="col-4 col-sm-2 col-form-label" for="tgl_hamil">Tgl</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="date" name="tgl_hamil" id="tgl_hamil" class="form-control form-control-sm" value="<?= $tgl_hamil; ?>">
                                            <div class="invalid-feedback errortgl_hamil"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-12 mt-2">
                                    <label class="label-center mt-2">Dokumen</label>
                                    <div class="form-group row nopadding">
                                        <div class="col-6 col-sm-6 mb-2">
                                            <a download="<?= $du_foto_identitas; ?>" href="<?= usulan_foto($du_foto_identitas, 'foto_identitas'); ?>">
                                                <img class="img-preview-id" src="<?= usulan_foto($du_foto_identitas, 'foto_identitas'); ?>" style="width: 30px; height: 40px; border-radius: 2px;">
                                            </a>
                                            <br>
                                            <label for="du_foto_identitas">Foto KTP/KK/KIA/AKL</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-address-card"></i></span>
                                                </div>
                                                <input type="file" class="form-control" spellcheck="false" name="du_foto_identitas" id="du_foto_identitas" onchange="previewImgId()" accept="image/*" capture />
                                            </div>
                                        </div>
                                        <div class="invalid-feedback errordu_foto_identitas"></div>
                                        <div class="col-6 col-sm-6 mb-2">
                                            <a download="<?= $du_foto_rumah; ?>" href="<?= usulan_foto($du_foto_rumah, 'foto_rumah'); ?>">
                                                <img class="img-preview-rmh" src="<?= usulan_foto($du_foto_rumah, 'foto_rumah'); ?>" style="width: 30px; height: 40px; border-radius: 2px;">
                                            </a>
                                            <br>
                                            <label for="du_foto_rumah">Foto Rumah</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-home"></i></span>
                                                </div>
                                                <input type="file" class="form-control" spellcheck="false" name="du_foto_rumah" id="du_foto_rumah" onchange="previewImgRmh()" accept="image/*" capture />
                                            </div>
                                        </div>
                                        <div class="invalid-feedback errordu_foto_rumah"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-12 mt-2">
                                    <label class="label-center mt-2">Titik Koordinat</label>
                                    <div class="form-group row nopadding">
                                        <div class="col-sm-2 col-2">
                                            <button type="button" class="btn btn-primary" onclick="getLocation()"><i class="fas fa-map-marker-alt"></i></button>
                                        </div>
                                        <div class="col-sm-5 col-5">
                                            <input type="text" class="form-control mb-2" placeholder="Latitude" spellcheck="false" id="latitude" name="du_latitude" value="<?= $du_latitude; ?>" readonly required>
                                            <div class="invalid-feedback errordu_latitude"></div>
                                        </div>
                                        <div class="col-sm-5 col-5">
                                            <input type="text" class="form-control mb-2" placeholder="Longitude" spellcheck="false" id="longitude" name="du_longitude" value="<?= $du_longitude; ?>" readonly required>
                                            <div class="invalid-feedback errordu_longitude"></div>
                                        </div>
                                    </div>
                                </div>
                                <div <?= $user > 3 ? ' hidden' : ''; ?>>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input larger" type="checkbox" id="proses" name="du_proses" <?= $du_proses == '1' ? 'checked' : ''; ?> value="1" />
                                            </div>
                                            <label for="proses" class="form-check-label">PADAN </label>
                                            <div class="invalid-feedback errordu_proses"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer mt-3 justify-content-between">
                                    <input type="datetime-local" name="updated_at" id="" value="<?= date('Y-m-d H:i:s'); ?>" hidden>
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
            let form = $('.formsimpan')[0];
            let data = new FormData(form);
            $.ajax({
                type: "POST",
                url: '<?= base_url('/updateUsulan') ?>',
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

                        if (response.error.shdk) {
                            $('#shdk').addClass('is-invalid');
                            $('.errorShdk').html(response.error.shdk);
                        } else {
                            $('#shdk').removeClass('is-invalid');
                            $('.errorShdk').html('');
                        }

                        if (response.error.nik) {
                            $('#nik').addClass('is-invalid');
                            $('.errornik').html(response.error.nik);
                        } else {
                            $('#nik').removeClass('is-invalid');
                            $('.errornik').html('');
                        }

                        if (response.error.nokk) {
                            $('#nokk').addClass('is-invalid');
                            $('.errornokk').html(response.error.nokk);
                        } else {
                            $('#nokk').removeClass('is-invalid');
                            $('.errornokk').html('');
                        }

                        if (response.error.nama) {
                            $('#nama').addClass('is-invalid');
                            $('.errornama').html(response.error.nama);
                        } else {
                            $('#nama').removeClass('is-invalid');
                            $('.errornama').html('');
                        }

                        if (response.error.tempat_lahir) {
                            $('#tempat_lahir').addClass('is-invalid');
                            $('.errortempat_lahir').html(response.error.tempat_lahir);
                        } else {
                            $('#tempat_lahir').removeClass('is-invalid');
                            $('.errortempat_lahir').html('');
                        }

                        if (response.error.tanggal_lahir) {
                            $('#tanggal_lahir').addClass('is-invalid');
                            $('.errortanggal_lahir').html(response.error.tanggal_lahir);
                        } else {
                            $('#tanggal_lahir').removeClass('is-invalid');
                            $('.errortanggal_lahir').html('');
                        }

                        if (response.error.jenis_kelamin) {
                            $('#jenis_kelamin').addClass('is-invalid');
                            $('.errorjenis_kelamin').html(response.error.jenis_kelamin);
                        } else {
                            $('#jenis_kelamin').removeClass('is-invalid');
                            $('.errorjenis_kelamin').html('');
                        }

                        if (response.error.jenis_pekerjaan) {
                            $('#jenis_pekerjaan').addClass('is-invalid');
                            $('.errorjenis_pekerjaan').html(response.error.jenis_pekerjaan);
                        } else {
                            $('#jenis_pekerjaan').removeClass('is-invalid');
                            $('.errorjenis_pekerjaan').html('');
                        }

                        if (response.error.status_kawin) {
                            $('#status_kawin').addClass('is-invalid');
                            $('.errorstatus_kawin').html(response.error.status_kawin);
                        } else {
                            $('#status_kawin').removeClass('is-invalid');
                            $('.errorstatus_kawin').html('');
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

                        if (response.error.ibu_kandung) {
                            $('#ibu_kandung').addClass('is-invalid');
                            $('.erroribu_kandung').html(response.error.ibu_kandung);
                        } else {
                            $('#ibu_kandung').removeClass('is-invalid');
                            $('.erroribu_kandung').html('');
                        }

                        if (response.error.databansos) {
                            $('#databansos').addClass('is-invalid');
                            $('.errordatabansos').html(response.error.databansos);
                        } else {
                            $('#databansos').removeClass('is-invalid');
                            $('.errordatabansos').html('');
                        }

                        if (response.error.disabil_status) {
                            $('#databansos').addClass('is-invalid');
                            $('.errordatabansos').html(response.error.databansos);
                        } else {
                            $('#databansos').removeClass('is-invalid');
                            $('.errordatabansos').html('');
                        }

                        if (response.error.du_foto_identitas) {
                            $('#du_foto_identitas').addClass('is-invalid');
                            $('.errordu_foto_identitas').html(response.error.du_foto_identitas);
                        } else {
                            $('#du_foto_identitas').removeClass('is-invalid');
                            $('.errordu_foto_identitas').html('');
                        }

                        if (response.error.du_foto_rumah) {
                            $('#du_foto_rumah').addClass('is-invalid');
                            $('.errordu_foto_rumah').html(response.error.du_foto_rumah);
                        } else {
                            $('#du_foto_rumah').removeClass('is-invalid');
                            $('.errordu_foto_rumah').html('');
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