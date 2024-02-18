<?php
$user = session()->get('role_id');
$nik = session()->get('nik');
$jabatan = session()->get('jabatan');
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

    /* CSS untuk pesan sementara */
    #temporary-message {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 20px;
        border-radius: 5px;
        display: none;
        z-index: 9999;
        text-align: center;
        /* Teks berada di tengah */
        width: 50%;
        max-width: 300px;
        overflow: auto;
        /* Aktifkan overflow jika pesan melebihi ukuran maksimum */
    }
</style>

<link href="<?= base_url('assets/dist/css/smart_wizard.min.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?= base_url('assets/dist/css/smart_wizard_theme_dots.min.css'); ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?= base_url('assets/dist/js/jquery.smartWizard.min.js'); ?>"></script>

<!-- end modal dialog multi-step form wizard -->

<?= form_open_multipart('', ['class' => 'formsimpan']) ?>
<?= csrf_field(); ?>
<!-- Modal -->
<div class="modal fade" id="modalview" tabindex="-1" aria-labelledby="modalviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-info">
                <img src="<?= logoApp(); ?>" alt="<?= nameApp(); ?> Logo" class="brand-image" style="width:30px; margin-right: auto">
                <h5 class="modal-title" id="modalviewLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="smartwizard">
                    <ul>
                        <li><a href="#step-1">Step 1<br /><small>Data Individu</small></a></li>
                        <!-- <li><a href="#step-2">Step 2<br /><small>Personal Info</small></a></li>
                        <li><a href="#step-3">Step 3<br /><small>Payment Info</small></a></li> -->
                        <li><a href="#step-4">Step 2<br /><small>Survey Kriteria</small></a></li>
                    </ul>
                    <div>
                        <div id="step-1">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group row nopadding" hidden>
                                        <label class="col-4 col-sm-4 col-form-label" for="id">ID</label>
                                        <div class="col-8 col-sm-8">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="text" name="id" id="id" class="form-control form-control-sm" value="<?= $id; ?>" autofocus>
                                            <div class="invalid-feedback errorid"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="nokk">No. KK</label>
                                        <div class="col-8 col-sm-8">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="number" name="nokk" id="nokk" class="form-control form-control-sm" value="<?= $nokk; ?>" autocomplete="on">
                                            <div class="invalid-feedback errornokk"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="nik">NIK</label>
                                        <div class="col-8 col-sm-8">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="number" name="nik" id="nik" class="form-control form-control-sm" value="<?= $du_nik; ?>" autocomplete="off">
                                            <div class="invalid-feedback errornik"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="nama">Nama</label>
                                        <div class="col-8 col-sm-8">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="text" name="nama" id="nama" class="form-control form-control-sm" value="<?= $nama; ?>">
                                            <div class="invalid-feedback errornama"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="tempat_lahir">Tempat Lahir</label>
                                        <div class="col-8 col-sm-8">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="text" name="tempat_lahir" id="tempat_lahir" class="form-control form-control-sm" value="<?= $tempat_lahir; ?>">
                                            <div class="invalid-feedback errortempat_lahir"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="tanggal_lahir">Tanggal Lahir</label>
                                        <div class="col-8 col-sm-8">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control form-control-sm" value="<?= $tanggal_lahir; ?>">
                                            <div class="invalid-feedback errortanggal_lahir"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="ibu_kandung">Ibu Kandung</label>
                                        <div class="col-8 col-sm-8">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="text" name="ibu_kandung" id="ibu_kandung" class="form-control form-control-sm" value="<?= $ibu_kandung; ?>">
                                            <div class="invalid-feedback erroribu_kandung"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="jenis_kelamin">Jenis Kelamin</label>
                                        <div class="col-8 col-sm-8">
                                            <div class="form-check form-check-inline">
                                                <input <?= $user > 3 ? ' disabled="on"' : ''; ?> class="form-check-input" type="radio" id="chk-Lk" name="jenis_kelamin" <?= $jenis_kelamin == '1' ? 'checked' : ''; ?> value="1" />
                                                <label for="chk-Lk" class="form-check-label"> Laki-Laki </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input <?= $user > 3 ? ' disabled="on"' : ''; ?> class="form-check-input" type="radio" id="chk-Pr" name="jenis_kelamin" <?= $jenis_kelamin == '2' ? 'checked' : ''; ?> value="2" />
                                                <label for="chk-Pr" class="form-check-label"> Perempuan </label>
                                            </div>
                                            <div class="invalid-feedback errorjenis_kelamin"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding" id="status_hamil_div" style="display: none">
                                        <label class="col-4 col-sm-4 col-form-label" for="status_hamil">Status Hamil</label>
                                        <div class="col-8 col-sm-8">
                                            <div class="form-check form-check-inline">
                                                <input <?= $user > 3 ? ' disabled="on"' : ''; ?> class="form-check-input" type="radio" id="chk-YaHamil" name="status_hamil" <?= $status_hamil == 1 ? 'checked' : ''; ?> value="1" />
                                                <label for="chk-YaHamil" class="form-check-label"> Ya </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input <?= $user > 3 ? ' disabled="on"' : ''; ?> class="form-check-input" type="radio" id="chk-TidakHamil" name="status_hamil" <?= $status_hamil == 2 ? 'checked' : ''; ?> value="2" />
                                                <label for="chk-TidakHamil" class="form-check-label"> Tidak </label>
                                            </div>
                                            <div class="invalid-feedback errorstatus_hamil"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding" id="tgl_hamil_div" style="display: none;">
                                        <label class="col-4 col-sm-4 col-form-label" for="tgl_hamil">Tgl Mulai Hamil</label>
                                        <div class="col-8 col-sm-8">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="date" name="tgl_hamil" id="tgl_hamil" class="form-control form-control-sm" value="<?= $tgl_hamil; ?>">
                                            <div class="invalid-feedback errortgl_hamil"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="jenis_pekerjaan">Pekerjaan</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user > 3 ? ' disabled="on' : ''; ?> id="jenis_pekerjaan" name="jenis_pekerjaan" class="form-select form-select-sm">
                                                <option value="">-- Pilih Jenis Pekerjaan --</option>
                                                <?php foreach ($pekerjaan as $row) { ?>
                                                    <option <?php if ($jenis_pekerjaan == $row['pk_id']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['pk_id'] ?>"> <?php echo $row['pk_nama']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errorjenis_pekerjaan"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="shdk">SHDK</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user > 3 ? ' disabled="on' : ''; ?> id="shdk" name="shdk" class="form-select form-select-sm">
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
                                <div class="col-12 col-sm-6">
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="jenis_pendidikan">Pendidikan</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user > 3 ? ' disabled="on' : ''; ?> id="jenis_pendidikan" name="jenis_pendidikan" class="form-select form-select-sm">
                                                <option value="">-- Pilih Status Pendidikan --</option>
                                                <?php foreach ($pendidikan_kk as $row) { ?>
                                                    <option <?php if ($jenis_pendidikan == $row['pk_id']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['pk_id'] ?>"> <?= $row['pk_nama']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errorjenis_pendidikan"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="status_kawin">Status</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user > 3 ? ' disabled="on' : ''; ?> id="status_kawin" name="status_kawin" class="form-select form-select-sm">
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
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="kecamatan">Kecamatan</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="kecamatan" id="kecamatan" class="form-control form-control-sm" value="<?= Profil_Admin()['namaKec']; ?>" readonly>
                                            <div class="invalid-feedback errorkecamatan"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="kelurahan">Desa/Kelurahan</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user > 3 ? ' disabled="on' : ''; ?> id="kelurahan" name="kelurahan" class="form-select form-select-sm">
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
                                        <label class="col-4 col-sm-4 col-form-label" for="datarw">No. RW</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user > 3 ? ' disabled="on' : ''; ?> id="datarw" name="datarw" class="form-select form-select-sm">
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
                                            <select <?= $user > 3 ? ' disabled="on' : ''; ?> id="datart" name="datart" class="form-select form-select-sm">
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
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="<?= $alamat; ?>">
                                            <div class="invalid-feedback erroralamat"></div>
                                        </div>
                                    </div>

                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="databansos">Program</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user > 3 ? ' disabled="on' : ''; ?> id="databansos" name="databansos" class="form-select form-select-sm">
                                                <option value="">-- Pilih Program --</option>
                                                <?php foreach ($bansos as $row) { ?>
                                                    <option <?php if ($databansos == $row['dbj_id']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['dbj_id'] ?>"> <?php echo $row['dbj_nama_bansos']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errordatabansos"></div>
                                        </div>
                                    </div>

                                    <div class="form-group row nopadding">
                                        <label for="disabil_status" class="col-4 col-form-label">Disabilitas</label>
                                        <div class="col-8 col-sm-8">
                                            <div class="form-check form-check-inline">
                                                <input <?= $user > 3 ? ' disabled="on"' : ''; ?> class="form-check-input" type="radio" id="chk-Yes" name="disabil_status" <?= $disabil_status == 1 ? 'checked' : ''; ?> value="1" />
                                                <label for="chk-Yes" class="form-check-label"> Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input <?= $user > 3 ? ' disabled="on"' : ''; ?> class="form-check-input" type="radio" id="chk-No" name="disabil_status" <?= $disabil_status == 2 ? 'checked' : ''; ?> value="2" />
                                                <label for="chk-No" class="form-check-label"> Tidak</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding" id="disabil_jenis_div" style="display: none">
                                        <label for="disabil_jenis" class="col-4 col-form-label">Jenis Disabilitas</label>
                                        <div class="col-8 col-sm-8">
                                            <select <?= $user > 3 ? ' disabled="on' : ''; ?> id="disabil_jenis" name="disabil_jenis" class="form-select form-select-sm">
                                                <option value="">-- Pilih Jenis Disabilitas --</option>
                                                <?php foreach ($DisabilitasJenisModel as $row) { ?>
                                                    <option <?= $disabil_jenis == $row['dj_id'] ? 'selected' : ''; ?> value="<?= $row['dj_id'] ?>"> <?php echo $row['dj_keterangan']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-12 mt-2">
                                    <label class="label-center mt-2">Dokumen</label>
                                    <div class="form-group row nopadding">
                                        <div class="col-6 col-sm-6 mb-2">
                                            <a download="<?= $du_foto_identitas; ?>" href="<?= usulan_foto($du_foto_identitas, 'foto_identitas'); ?>">
                                                <img src="<?= usulan_foto($du_foto_identitas, 'foto_identitas'); ?>" class="foto-dokumen">
                                            </a>
                                            <br>
                                            <label for="du_foto_identitas">Foto KTP / KK / KIA / AKL</label>
                                            <div class="input-group" hidden>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-address-card"></i></span>
                                                </div>
                                                <input type="file" class="form-control" spellcheck="false" name="du_foto_identitas" id="du_foto_identitas" onchange="previewImgId()" accept="image/*" capture />
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-6 mb-2">
                                            <a download="<?= $du_foto_rumah; ?>" href="<?= usulan_foto($du_foto_rumah, 'foto_rumah'); ?>">
                                                <img src="<?= usulan_foto($du_foto_rumah, 'foto_rumah'); ?>" class="foto-dokumen">
                                            </a>
                                            <br>
                                            <label for="du_foto_rumah">Foto Rumah</label>
                                            <div class="input-group" hidden>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-home"></i></span>
                                                </div>
                                                <input type="file" class="form-control" spellcheck="false" name="du_foto_rumah" id="du_foto_rumah" onchange="previewImgRmh()" accept="image/*" capture />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-12 mt-2">
                                    <label class="label-center mt-2">Titik Koordinat</label>
                                    <div class="form-group row nopadding">
                                        <!-- <div class="col-sm-2 col-2">
                                <button type="button" class="btn btn-primary" onclick="getLocation()"><i class="fas fa-map-marker-alt"></i></button>
                            </div> -->
                                        <div class="col-sm-5 col-5">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="text" class="form-control mb-2" placeholder="Latitude" spellcheck="false" id="du_latitude" name="du_latitude" value="<?= $du_latitude; ?>" required>
                                            <div class="invalid-feedback errordu_latitude"></div>
                                        </div>
                                        <div class="col-sm-5 col-5">
                                            <input <?= $user > 3 ? ' readonly="on"' : ''; ?> type="text" class="form-control mb-2" placeholder="Longitude" spellcheck="false" id="du_longitude" name="du_longitude" value="<?= $du_longitude; ?>" required>
                                            <div class="invalid-feedback errordu_longitude"></div>
                                        </div>
                                        <div class="col-sm-2 col-2 nopadding">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="copyText()"><i class="fas fa-map-marked-alt"></i></button>
                                        </div>
                                        <!-- Elemen untuk menampilkan pesan sementara -->
                                        <div id="temporary-message"><span id="message-content"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="step-4" class="">
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
                                            <label class="col-10 col-form-label" for="sk0">Apakah memiliki tempat berteduh tetap sehari-hari?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk0" name="sk0" <?= $sk0 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk0" class="form-check-label"><?= $sk0 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk5">Apakah kepala keluarga atau pengurus keluarga masih bekerja?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk5" name="sk5" <?= $sk5 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk5" class="form-check-label"><?= $sk5 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk6">Apakah pengeluaran pangan lebih besar (>70%) dari total pengeluaran?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk6" name="sk6" <?= $sk6 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk6" class="form-check-label"><?= $sk6 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk7">Apakah tempat tinggal sebagian besar berlantai tanah dan/atau plesteran?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk7" name="sk7" <?= $sk7 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk7" class="form-check-label"><?= $sk7 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk8">Apakah tempat tinggal memiliki fasilitas buang air kecil / besar sendiri?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk8" name="sk8" <?= $sk8 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk8" class="form-check-label"><?= $sk8 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk9">Apakah target survey tinggal bersama anggota keluarga yang lain?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk9" name="sk9" <?= $sk9 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk9" class="form-check-label"><?= $sk9 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk1">Apakah pernah khawatir atau pernah tidak makan dalam setahun terakhir?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk1" name="sk1" <?= $sk1 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk1" class="form-check-label"><?= $sk1 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk2">Apakah ada pengeluaran untuk pakaian selama 1 (satu) tahun terakhir?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk2" name="sk2" <?= $sk2 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk2" class="form-check-label"><?= $sk2 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk3">Apakah tempat tinggal sebagian besar berdinding bambu / kawat / kayu?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk3" name="sk3" <?= $sk3 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk3" class="form-check-label"><?= $sk3 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk4">Apakah sumber penerangan berasal dari listrik PLN 450 watt atau bukan listrik?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk4" name="sk4" <?= $sk4 == '1' ? 'checked' : ''; ?> value="1" <?= $user > 3 ? ' onclick="return false"' : ''; ?> />
                                                <label for="sk4" class="form-check-label"><?= $sk4 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>

                                    </div>
                                    <div <?= $user > 3 ? ' hidden' : ''; ?>>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-center">
                                                <div class="form-check form-check-inline">
                                                    <input <?= $user > 3 ? ' readonly="on"' : ''; ?> class="form-check-input larger" type="checkbox" id="proses" name="du_proses" <?= $du_proses == '1' ? 'checked' : ''; ?> value="1" />
                                                </div>
                                                <label for="proses" class="form-check-label"> PADAN</label>
                                                <div class="invalid-feedback errordu_proses"></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer mt-3">
                                            <button type="submit" class="btn btn-info btn-block btnSimpan">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo form_close(); ?>

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

                        if (response.error.du_nasu) {
                            $('#du_nasu').addClass('is-invalid');
                            $('.errordu_nasu').html(response.error.du_nasu);
                        } else {
                            $('#du_nasu').removeClass('is-invalid');
                            $('.errordu_nasu').html('');
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

            function tampilkanLabel(checkboxId, labelId) {
                var checkbox = document.getElementById(checkboxId);
                var label = document.getElementById(labelId);

                if (checkbox.checked) {
                    label.innerHTML = " Ya";
                } else {
                    label.innerHTML = " Tidak";
                }
            }

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

        $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'dots',
            autoAdjustHeight: true,
            transitionEffect: 'fade',
            showStepURLhash: false,
        });

        // Cek status radio button saat halaman dimuat
        if ($("#chk-Pr").is(":checked")) {
            $("#status_hamil_div").show(); // Menggunakan metode show() untuk menampilkan div
        } else {
            $("#status_hamil_div").hide(); // Menggunakan metode hide() untuk menyembunyikan div
            $("#tgl_hamil_div").hide();
        }

        if ($("#chk-YaHamil").is(":checked")) {
            $("#tgl_hamil_div").show();
            $('#tgl_hamil_div').show().find(':input').attr('required', true);
        } else {
            $('#tgl_hamil_div').hide().find(':input').attr('required', false);
            $("#tgl_hamil_div").hide();
        }

        if ($("#chk-Yes").is(":checked")) {
            $("#disabil_jenis_div").show();
        } else {
            $("#disabil_jenis_div").hide();
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
                $("#tgl_hamil_div").show();
                $('#tgl_hamil_div').show().find(':input').attr('required', true);
            } else {
                $('#tgl_hamil_div').hide().find(':input').attr('required', false);
                $("#tgl_hamil_div").hide();
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

    function copyText() {
        var text1 = document.getElementById("du_latitude").value;
        var text2 = document.getElementById("du_longitude").value;

        var textToCopy = text1 + ", " + text2; // Menggabungkan teks dari kedua textbox

        navigator.clipboard.writeText(textToCopy).then(function() {
            // Menampilkan pesan sementara
            var temporaryMessage = document.getElementById('temporary-message');
            temporaryMessage.textContent = "Lokasi: " + textToCopy + " disalin ke clipboard";
            temporaryMessage.style.display = 'block';

            // Menghilangkan pesan sementara setelah 2 detik
            setTimeout(function() {
                temporaryMessage.style.display = 'none';
            }, 2000);
        }, function(err) {
            console.error('Gagal menyalin teks: ', err);
        });
    }
    // Menangani operasi paste
    document.addEventListener('paste', function(event) {
        var pasteData = (event.clipboardData || window.clipboardData).getData('text');
        // Di sini Anda dapat menangani data yang dipaste sesuai kebutuhan aplikasi Anda
        console.log("Teks yang ditempel:", pasteData);
    });
</script>