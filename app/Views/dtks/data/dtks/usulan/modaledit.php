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


<!-- Modal -->
<?= form_open_multipart('', ['class' => 'formsimpan']) ?>
<?= csrf_field(); ?>
<div class="modal fade" id="modaledit" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modaleditLabel" aria-hidden="true">
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
                                        <label class="col-4 col-sm-4 col-form-label" for="nokk">No. KK</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="number" name="nokk" id="nokk" class="form-control form-control-sm" value="<?= $nokk; ?>" autocomplete="on">
                                            <div class="invalid-feedback errornokk"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="nik">NIK</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="number" name="nik" id="nik" class="form-control form-control-sm" value="<?= $du_nik; ?>" autocomplete="off" <?= $user >= 4 ? ' readonly="readonly"' : ''; ?>>
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
                                        <label class="col-4 col-sm-4 col-form-label" for="tempat_lahir">Tempat Lahir</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control form-control-sm" value="<?= $tempat_lahir; ?>">
                                            <div class="invalid-feedback errortempat_lahir"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="tanggal_lahir">Tanggal Lahir</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control form-control-sm" value="<?= $tanggal_lahir; ?>">
                                            <div class="invalid-feedback errortanggal_lahir"></div>
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
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="jenis_pekerjaan">Pekerjaan</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="jenis_pekerjaan" name="jenis_pekerjaan" class="form-select form-select-sm">
                                                <option value="">-- Pilih Jenis Pekerjaan --</option>
                                                <?php foreach ($pekerjaan as $row) { ?>
                                                    <option <?php if ($jenis_pekerjaan == $row['pk_id']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['pk_id'] ?>"> <?= $row['pk_nama']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errorjenis_pekerjaan"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-6">
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
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="jenis_pendidikan">Pendidikan</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="jenis_pendidikan" name="jenis_pendidikan" class="form-select form-select-sm">
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
                                            <select id="status_kawin" name="status_kawin" class="form-select form-select-sm">
                                                <option value="">-- Pilih Status Perkawinan --</option>
                                                <?php foreach ($statusKawin as $row) { ?>
                                                    <option <?= $status_kawin == $row['idStatus'] ? 'selected' : ''; ?> value="<?= $row['idStatus'] ?>"> <?= $row['StatusKawin']; ?></option>
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
                                                    <option <?= $datarw == $row['no_rw'] ? 'selected' : ''; ?> value="<?= $row['no_rw'] ?>"> <?= $row['no_rw']; ?>
                                                    </option>
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
                                                    <option <?= $datart == $row['no_rt'] ? 'selected' : ''; ?> value="<?= $row['no_rt'] ?>"> <?= $row['no_rt']; ?>
                                                    </option>
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
                                <div class="col-12">
                                    <?php if ($user < 4) : ?>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-center">
                                                <label for="du_proses" class="form-check-label mr-2">PADAN </label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input larger proses" type="checkbox" id="du_proses" name="du_proses" <?= $du_proses == '1' ? 'checked' : ''; ?> value="1" />
                                                </div>
                                                <div class="invalid-feedback errordu_proses"></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="modal-footer mt-3 justify-content-between">
                                        <input type="datetime-local" name="updated_at" id="" value="<?= date('Y-m-d H:i:s'); ?>" hidden>
                                        <button type="submit" class="btn btn-block btn-warning btnSimpan">Update</button>
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
                                        <hr>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk0">Apakah memiliki tempat berteduh tetap sehari-hari?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk0" name="sk0" <?= $sk0 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk0', 'label0')" />
                                                <label for="sk0" id="label0" class="form-check-label"><?= $sk0 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk5">Apakah kepala keluarga atau pengurus keluarga masih bekerja?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk5" name="sk5" <?= $sk5 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk5', 'label5')" />
                                                <label for="sk5" id="label5" class="form-check-label"><?= $sk5 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk6">Apakah pengeluaran pangan lebih besar (>70%) dari total pengeluaran?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk6" name="sk6" <?= $sk6 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk6', 'label6')" />
                                                <label for="sk6" id="label6" class="form-check-label"><?= $sk6 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk7">Apakah tempat tinggal sebagian besar berlantai tanah dan/atau plesteran?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk7" name="sk7" <?= $sk7 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk7', 'label7')" />
                                                <label for="sk7" id="label7" class="form-check-label"><?= $sk7 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk8">Apakah tempat tinggal memiliki fasilitas buang air kecil / besar sendiri?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk8" name="sk8" <?= $sk8 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk8', 'label8')" />
                                                <label for="sk8" id="label8" class="form-check-label"><?= $sk8 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk9">Apakah target survey tinggal bersama anggota keluarga yang lain?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk9" name="sk9" <?= $sk9 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk9', 'label9')" />
                                                <label for="sk9" id="label9" class="form-check-label"><?= $sk9 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk1">Apakah pernah khawatir atau pernah tidak makan dalam setahun terakhir?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk1" name="sk1" <?= $sk1 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk1', 'label1')" />
                                                <label for="sk1" id="label1" class="form-check-label"><?= $sk1 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk2">Apakah ada pengeluaran untuk pakaian selama 1 (satu) tahun terakhir?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk2" name="sk2" <?= $sk2 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk2', 'label2')" />
                                                <label for="sk2" id="label2" class="form-check-label"><?= $sk2 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk3">Apakah tempat tinggal sebagian besar berdinding bambu / kawat / kayu?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk3" name="sk3" <?= $sk3 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk3', 'label3')" />
                                                <label for="sk3" id="label3" class="form-check-label"><?= $sk3 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-10 col-form-label" for="sk4">Apakah sumber penerangan berasal dari listrik PLN 450 watt atau bukan listrik?</label>
                                            <div class="col-2">
                                                <input class="form-check-input" type="checkbox" id="sk4" name="sk4" <?= $sk4 == '1' ? 'checked' : ''; ?> value="1" onchange="tampilkanLabel('sk4', 'label4')" />
                                                <label for="sk4" id="label4" class="form-check-label"><?= $sk4 == '1' ? 'Ya' : 'Tidak'; ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="step-4" class="">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row nopadding">
                                        <p class="col-12 col-form-label">CPM : <em><?= $nama . ' - ' . $alamat . ' RT ' . $datart . ' RW ' . $datarw; ?></em></p>
                                    </div>
                                    <hr>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-3 col-form-label" for="databansos">Program</label>
                                        <div class="col-8 col-sm-9">
                                            <select id="databansos" name="databansos" class="form-select form-select-sm">
                                                <option value="">-- Pilih Program --</option>
                                                <?php foreach ($bansos as $row) { ?>
                                                    <option <?php if ($databansos == $row['dbj_id']) {
                                                                echo 'selected';
                                                            } ?> value="<?= $row['dbj_id'] ?>"> <?= $row['dbj_nama_bansos']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errordatabansos"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group row nopadding" id="disabil_status_div">
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
                                                    <option <?= $disabil_jenis == $row['dj_id'] ? 'selected' : ''; ?> value="<?= $row['dj_id'] ?>"> <?= $row['dj_keterangan']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding du_so_id_div" style="display: none;">
                                        <label class="col-4 col-sm-4 col-form-label" for="du_so_id">Status</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="du_so_id" name="du_so_id" class="form-select form-select-sm">
                                                <option value="">-- Status Orangtua --</option>
                                                <?php foreach ($sta_ortu as $row) { ?>
                                                    <option <?= $du_so_id == $row['so_id'] ? 'selected' : ''; ?> value="<?= $row['so_id'] ?>"> <?= ucwords(strtolower($row['so_desk'])); ?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="invalid-feedback errordu_so_id"></div>
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
                                            </div v>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" id="chk-TidakHamil" name="status_hamil" <?= $status_hamil == 2 ? 'checked' : ''; ?> value="2" />
                                                <label for="chk-TidakHamil" class="form-check-label"> Tidak </label>
                                            </div v>
                                            <div class="invalid-feedback errorstatus_hamil"></div>
                                            </di v>
                                        </div>
                                        <div class="form-group row nopadding" id="tgl_hamil_div" style="display: none;">
                                            <label class="col-4 col-sm-2 col-form-label" for="tgl_hamil">Tanggal</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="date" name="tgl_hamil" id="tgl_hamil" class="form-control form-control-sm" value="<?= $tgl_hamil; ?>">
                                                <div class="invalid-feedback errortgl_hamil"></div>
                                            </div v>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="du_kate">Kel. Adat Terpencil</label>
                                        <div class="col-8 col-sm-8">
                                            <select id="du_kate" name="du_kate" class="form-select form-select-sm">
                                                <option <?= $du_kate == 0 ? ' selected' : ''; ?> value="0">Tidak</option>
                                                <option <?= $du_kate == 1 ? ' selected' : ''; ?> value="1">Ya</option>
                                            </select>
                                            <div class="invalid-feedback errordu_kate"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 du_nasu_div">
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="du_nasu">Nama Suku</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" name="du_nasu" id="du_nasu" class="form-control form-control-sm" value="<?= $du_nasu; ?>">
                                            <div class="invalid-feedback errordu_nasu"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-12 mt-2">
                                    <label class="label-center mt-2">Dokumen</label>
                                    <div class="form-group row nopadding">
                                        <div class="col-6 col-sm-6 mb-2">
                                            <a download="<?= $du_foto_identitas; ?>" href="<?= usulan_foto($du_foto_identitas, 'foto_identitas'); ?>">
                                                <img class="img-preview-id foto-dokumen" src="<?= usulan_foto($du_foto_identitas, 'foto_identitas'); ?>">
                                            </a>
                                            <br>
                                            <label for="du_foto_identitas">Foto KTP/KK/KIA/AKL</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-address-card"></i></span>
                                                </div>
                                                <input type="file" class="form-control form-control-sm" spellcheck="false" name="du_foto_identitas" id="du_foto_identitas" onchange="previewImgId()" accept="image/*" capture />
                                            </div>
                                        </div>
                                        <div class="invalid-feedback errordu_foto_identitas"></div>
                                        <div class="col-6 col-sm-6 mb-2">
                                            <a download="<?= $du_foto_rumah; ?>" href="<?= usulan_foto($du_foto_rumah, 'foto_rumah'); ?>">
                                                <img class="img-preview-rmh foto-dokumen" src="<?= usulan_foto($du_foto_rumah, 'foto_rumah'); ?>">
                                            </a>
                                            <br>
                                            <label for="du_foto_rumah">Foto Rumah</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-home"></i></span>
                                                </div>
                                                <input type="file" class="form-control form-control-sm" spellcheck="false" name="du_foto_rumah" id="du_foto_rumah" onchange="previewImgRmh()" accept="image/*" capture />
                                            </div>
                                        </div>
                                        <div class="invalid-feedback errordu_foto_rumah"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-12 mt-2">
                                    <label class="label-center mt-2">Koordinat</label>
                                    <div class="form-group row nopadding">

                                        <div class="col-sm-5 col-5">
                                            <input type="text" class="form-control form-control-sm mb-2" placeholder="Lat" spellcheck="false" id="du_latitude" name="du_latitude" value="<?= $du_latitude; ?>" readonly required>
                                            <div class="invalid-feedback errordu_latitude"></div>
                                        </div>
                                        <div class="col-sm-5 col-5">
                                            <input type="text" class="form-control form-control-sm mb-2" placeholder="Long" spellcheck="false" id="du_longitude" name="du_longitude" value="<?= $du_longitude; ?>" readonly required>
                                            <div class="invalid-feedback errordu_longitude"></div>
                                        </div>
                                        <div class="col-sm-2 col-2" hidden>
                                            <button type="button" class="btn btn-outline-primary" onclick="getLocation()"><i class="fas fa-map-marked-alt"></i></button>
                                        </div>
                                        <div class="col-sm-2 col-2">
                                            <button type="button" class="btn btn-outline-primary" onclick="copyText()"><i class="fas fa-map-marked-alt"></i></button>
                                        </div>
                                        <!-- Elemen untuk menampilkan pesan sementara -->
                                        <div id="temporary-message"><span id="message-content"></span></div>
                                    </div>
                                </div>
                                <?php if ($user < 4) : ?>
                                    <div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 d-flex justify-content-center">
                                                <label for="du_proses" class="form-check-label mr-2">PADAN </label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input larger proses" type="checkbox" id="du_proses" name="du_proses" <?= $du_proses == '1' ? 'checked' : ''; ?> value="1" />
                                                </div>
                                                <div class="invalid-feedback errordu_proses"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
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

        // start dropdown kel adat terpencil
        // Tangkap perubahan pada dropdown
        $('#du_kate').change(function() {
            if ($(this).val() === '0') {
                // Jika nilainya 0, hapus nilai Nama Suku
                $('#du_nasu').val('');
            }
        });
        // end dropdown kel adat terpencil

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

                        if (response.error.jenis_pendidikan) {
                            $('#jenis_pendidikan').addClass('is-invalid');
                            $('.errorjenis_pendidikan').html(response.error.jenis_pendidikan);
                        } else {
                            $('#jenis_pendidikan').removeClass('is-invalid');
                            $('.errorjenis_pendidikan').html('');
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
                    url: "<?= base_url('action'); ?>",
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

        $('#du_usia').val(getAge);

        if ($("#du_usia").val() < 18) {
            $('.du_so_id_div').show();
        } else {
            $('.du_so_id_div').hide();
        }

        $(".proses").change(function() {
            // Memeriksa apakah checkbox pertama atau kedua dicentang
            if ($(this).is(':checked')) {
                // Mencari semua checkbox dengan name dan value yang sama
                $('[name="du_proses"][value="1"]').prop('checked', true);
            } else {
                // Jika checkbox saat ini di-uncheck, maka uncheck juga checkbox yang lain
                $('[name="du_proses"][value="1"]').prop('checked', false);
            }
        });

        var dropdown = document.getElementById("du_kate");
        var input = document.getElementById("du_nasu");

        if (dropdown.value === "1") {
            input.readOnly = false;
        } else {
            input.readOnly = true;
        }

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

    var dropdown = document.getElementById("du_kate");
    var input = document.getElementById("du_nasu");

    dropdown.addEventListener("change", function() {
        if (dropdown.value === "1") {
            input.readOnly = false;
        } else {
            input.readOnly = true;
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

    function copyText() {
        var text1 = document.getElementById("du_latitude").value;
        var text2 = document.getElementById("du_longitude").value;

        var textToCopy = text1 + ", " + text2; // Menggabungkan teks dari kedua textbox

        navigator.clipboard.writeText(textToCopy).then(function() {
            // Menampilkan pesan sementara
            var temporaryMessage = document.getElementById('temporary-message');
            temporaryMessage.textContent = "Tikor: " + textToCopy + " disalin ke clipboard";
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