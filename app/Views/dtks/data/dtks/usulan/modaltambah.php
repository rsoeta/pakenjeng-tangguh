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
    <div class="modal fade" id="modaltambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                            <li><a href="#step-1">Step 1<br /><small>Data Individu</small></a></li>
                            <!-- <li><a href="#step-2">Step 2<br /><small>Personal Info</small></a></li> -->
                            <li><a href="#step-3">Step 2<br /><small>Survey Kriteria</small></a></li>
                            <li><a href="#step-4">Step 3<br /><small>Pengusulan Bansos</small></a></li>
                        </ul>
                        <div>
                            <div id="step-1">
                                <div class="row">
                                    <div class="form-group row nopadding mb-2">
                                        <label class="col-4 col-sm-2 col-form-label" for="dataCari">Cari Data</label>
                                        <div class="col-8 col-sm-10">
                                            <select name="dataCari" id="dataCari" class="form-control form-control-sm select2" style="width: 100%;">
                                                <option value='0'>-- Berdasarkan NIK --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-form-label" for="nokk">No. KK</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="number" name="nokk" id="nokk" class="form-control form-control-sm" autocomplete="on" autofocus>
                                                <div class="invalid-feedback errornokk"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="nik">NIK</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="number" name="nik" id="nik" class="form-control form-control-sm" autocomplete="off">
                                                <div class="invalid-feedback errornik"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="nama">Nama</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="text" name="nama" id="nama" class="form-control form-control-sm">
                                                <div class="invalid-feedback errornama"></div>
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
                                            <label class="col-4 col-sm-4 col-form-label" for="alamat">Alamat</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback erroralamat"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="jenis_kelamin">Jenis Kelamin</label>
                                            <div class="col-8 col-sm-8">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="chk-Lk" name="jenis_kelamin" value="1" />
                                                    <label for="chk-Lk" class="form-check-label"> Laki-Laki </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="chk-Pr" name="jenis_kelamin" value="2" />
                                                    <label for="chk-Pr" class="form-check-label"> Perempuan </label>
                                                </div>
                                                <div class="invalid-feedback errorjenis_kelamin"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding" id="status_hamil_div" style="display: none">
                                            <label class="col-4 col-sm-4 col-form-label" for="status_hamil">Status Hamil</label>
                                            <div class="col-8 col-sm-8">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="chk-YaHamil" name="status_hamil" value="1" />
                                                    <label for="chk-YaHamil" class="form-check-label"> Ya </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="chk-TidakHamil" name="status_hamil" value="2" />
                                                    <label for="chk-TidakHamil" class="form-check-label"> Tidak </label>
                                                </div>
                                                <div class="invalid-feedback errorstatus_hamil"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding" id="tgl_hamil_div" style="display: none;">
                                            <label class="col-4 col-sm-4 col-form-label" for="tgl_hamil">Tanggal</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="date" name="tgl_hamil" id="tgl_hamil" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback errortgl_hamil"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="tempat_lahir">Tempat Lahir</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback errortempat_lahir"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="tanggal_lahir">Tanggal Lahir</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback errortanggal_lahir"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="du_usia">Usia</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="number" name="du_usia" id="du_usia" class="form-control form-control-sm" value="" readonly>
                                                <div class="invalid-feedback errordu_usia"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="jenis_pendidikan">Pendidikan</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="jenis_pendidikan" name="jenis_pendidikan" class="form-select form-select-sm">
                                                    <option value="">-- Pilih Status Pendidikan --</option>
                                                    <?php foreach ($pendidikan_kk as $row) { ?>
                                                        <option value="<?= $row['pk_id'] ?>"> <?= $row['pk_nama']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errorjenis_pendidikan"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="jenis_pekerjaan">Pekerjaan</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="jenis_pekerjaan" name="jenis_pekerjaan" class="form-select form-select-sm">
                                                    <option value="">-- Pilih Jenis Pekerjaan --</option>
                                                    <?php foreach ($pekerjaan as $row) { ?>
                                                        <option value="<?= $row['pk_id'] ?>"> <?= $row['pk_nama']; ?></option>
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
                                                        <option value="<?= $row['idStatus'] ?>"> <?= $row['StatusKawin']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errorstatus_kawin"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="shdk">SHDK</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="shdk" name="shdk" class="form-select form-select-sm">
                                                    <option value="">-- Status Hubungan dalam Keluarga --</option>
                                                    <?php foreach ($shdk as $row) { ?>
                                                        <option value="<?= $row['id']; ?>"><?= $row['jenis_shdk']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errorShdk"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="ibu_kandung">Ibu Kandung</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="text" name="ibu_kandung" id="ibu_kandung" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback erroribu_kandung"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="datetime-local" name="updated_at" id="" value="<?= date('Y-m-d H:i:s'); ?>" hidden>
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
                                                <label class="col-10 col-form-label" for="sk0">Apakah memiliki tempat berteduh tetap sehari-hari?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk0" name="sk0" value="1" onchange="tampilkanLabel('sk0', 'label0')" />
                                                    <label for="sk0" id="label0" class="form-check-label"></label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-10 col-form-label" for="sk5">Apakah kepala keluarga atau pengurus keluarga masih bekerja?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk5" name="sk5" value="1" onchange="tampilkanLabel('sk5', 'label5')" />
                                                    <label for="sk5" id="label5" class="form-check-label"></label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-10 col-form-label" for="sk6">Apakah pengeluaran pangan lebih besar (>70%) dari total pengeluaran?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk6" name="sk6" value="1" onchange="tampilkanLabel('sk6', 'label6')" />
                                                    <label for="sk6" id="label6" class="form-check-label"></label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-10 col-form-label" for="sk7">Apakah tempat tinggal sebagian besar berlantai tanah dan/atau plesteran?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk7" name="sk7" value="1" onchange="tampilkanLabel('sk7', 'label7')" />
                                                    <label for="sk7" id="label7" class="form-check-label"></label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-10 col-form-label" for="sk8">Apakah tempat tinggal memiliki fasilitas buang air kecil / besar sendiri?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk8" name="sk8" value="1" onchange="tampilkanLabel('sk8', 'label8')" />
                                                    <label for="sk8" id="label8" class="form-check-label"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-10 col-form-label" for="sk9">Apakah target survey tinggal bersama anggota keluarga yang lain?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk9" name="sk9" value="1" onchange="tampilkanLabel('sk9', 'label9')" />
                                                    <label for="sk9" id="label9" class="form-check-label"></label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-10 col-form-label" for="sk1">Apakah pernah khawatir atau pernah tidak makan dalam setahun terakhir?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk1" name="sk1" value="1" onchange="tampilkanLabel('sk1', 'label1')" />
                                                    <label for="sk1" id="label1" class="form-check-label"></label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-10 col-form-label" for="sk2">Apakah ada pengeluaran untuk pakaian selama 1 (satu) tahun terakhir?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk2" name="sk2" value="1" onchange="tampilkanLabel('sk2', 'label2')" />
                                                    <label for="sk2" id="label2" class="form-check-label"></label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-10 col-form-label" for="sk3">Apakah tempat tinggal sebagian besar berdinding bambu / kawat / kayu?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk3" name="sk3" value="1" onchange="tampilkanLabel('sk3', 'label3')" />
                                                    <label for="sk3" id="label3" class="form-check-label"></label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-10 col-form-label" for="sk4">Apakah sumber penerangan berasal dari listrik PLN 450 watt atau bukan listrik?</label>
                                                <div class="col-2">
                                                    <input class="form-check-input" type="checkbox" id="sk4" name="sk4" value="1" onchange="tampilkanLabel('sk4', 'label4')" />
                                                    <label for="sk4" id="label4" class="form-check-label"></label>
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
                                            <label class="col-4 col-sm-4 col-form-label" for="databansos">Program</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="databansos" name="databansos" class="form-select form-select-sm">
                                                    <option value="">-- Pilih Program --</option>
                                                    <?php foreach ($bansos as $row) { ?>
                                                        <option value="<?= $row['dbj_id'] ?>"> <?= $row['dbj_nama_bansos']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errordatabansos"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-4">
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-form-label" for="disabil_status">Disabilitas</label>
                                            <div class="col-8 col-sm-8">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="chk-Yes" name="disabil_status" value="1" />
                                                    <label for="chk-Yes" class="form-check-label"> Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="chk-No" name="disabil_status" value="2" />
                                                    <label for="chk-No" class="form-check-label"> Tidak</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding" id="disabil_jenis_div" style="display: none">
                                            <label for="disabil_jenis" class="col-4 col-form-label">Jenis</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="disabil_jenis" name="disabil_jenis" class="form-select form-select-sm">
                                                    <option value="">-- Pilih Jenis Disabilitas --</option>
                                                    <?php foreach ($DisabilitasJenisModel as $row) { ?>
                                                        <option value="<?= $row['dj_id'] ?>"> <?php echo $row['dj_keterangan']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errordisabil_jenis"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4 du_so_id_div" style="display: none;">
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="du_so_id">Status</label>
                                            <div class="col-8 col-sm-8">
                                                <select id="du_so_id" name="du_so_id" class="form-select form-select-sm">
                                                    <option value="">-- Status Orangtua --</option>
                                                    <?php foreach ($sta_ortu as $row) { ?>
                                                        <option value="<?= $row['so_id'] ?>"> <?= ucwords(strtolower($row['so_desk'])); ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errordu_so_id"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-6 col-form-label" for="du_kate">Kel. Adat Terpencil</label>
                                            <div class="col-8 col-sm-6">
                                                <select id="du_kate" name="du_kate" class="form-select form-select-sm">
                                                    <option value="0">Tidak</option>
                                                    <option value="1">Ya</option>
                                                </select>
                                                <div class="invalid-feedback errordu_kate"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4 du_nasu_div">
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-4 col-form-label" for="du_nasu">Nama Suku</label>
                                            <div class="col-8 col-sm-8">
                                                <input type="text" name="du_nasu" id="du_nasu" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback errordu_nasu"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-12 mt-2">
                                        <label class="label-center mt-2">Dokumen</label>
                                        <div class="form-group row nopadding">
                                            <div class="col-4 mb-2">
                                                <img class="img-preview-id" src="<?= usulan_foto(null, 'foto_identitas'); ?>" style="width: 30px; height: 40px; border-radius: 2px;">
                                                <br>
                                                <label for="du_foto_identitas">Foto KTP/KK</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-address-card"></i></span>
                                                    </div>
                                                    <input type="file" class="form-control form-control-sm" spellcheck="false" name="du_foto_identitas" id="du_foto_identitas" onchange="previewImgId()" accept="image/*" capture="camera" capture required />
                                                </div>
                                            </div>
                                            <div class="invalid-feedback errordu_foto_identitas"></div>
                                            <div class="col-4 mb-2">
                                                <img class="img-preview-rmh" src="<?= usulan_foto(null, 'foto_rumah'); ?>" style="width: 30px; height: 40px; border-radius: 2px;">
                                                <br>
                                                <label for="du_foto_rumah">Foto Rumah Depan</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-home"></i></span>
                                                    </div>
                                                    <input type="file" class="form-control form-control-sm" spellcheck="false" name="du_foto_rumah" id="du_foto_rumah" onchange="previewImgRmh()" accept="image/*" capture="camera" capture required />
                                                </div>
                                            </div>
                                            <div class="invalid-feedback errordu_foto_rumah_dalam"></div>
                                            <div class="col-4 mb-2">
                                                <img class="img-preview-rmh-dlm" src="<?= usulan_foto(null, 'foto_rumah'); ?>" style="width: 30px; height: 40px; border-radius: 2px;">
                                                <br>
                                                <label for="du_foto_rumah_dalam">Foto Rumah Dalam</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-home"></i></span>
                                                    </div>
                                                    <input type="file" class="form-control form-control-sm" spellcheck="false" name="du_foto_rumah_dalam" id="du_foto_rumah_dalam" onchange="previewImgRmhDlm()" accept="image/*" capture="camera" capture required />
                                                </div>
                                            </div>
                                            <div class="invalid-feedback errordu_foto_rumah_dalam"></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-12 mt-2">
                                        <label class="label-center mt-2">Koordinat</label>
                                        <div class="form-group row nopadding">
                                            <div class="col-5 col-sm-4">
                                                <input type="text" class="form-control form-control-sm mb-2" placeholder="Latitude" spellcheck="false" id="du_latitude" name="du_latitude" readonly required>
                                                <div class="invalid-feedback errordu_latitude"></div>
                                            </div>
                                            <div class="col-5 col-sm-4">
                                                <input type="text" class="form-control form-control-sm mb-2" placeholder="Longitude" spellcheck="false" id="du_longitude" name="du_longitude" readonly required>
                                                <div class="invalid-feedback errordu_longitude"></div>
                                            </div>
                                            <div class="col-2 col-sm-4">
                                                <input type="text" class="form-control form-control-sm mb-2" placeholder="Accuracy" spellcheck="false" id="du_accuracy" name="du_accuracy" readonly required>
                                                <div class="invalid-feedback errordu_accuracy"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <div class="col-6 col-sm-6">
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-block" onclick="getLokasi()"><i class="fas fa-map-marker-alt"></i> Get</button>
                                            </div>
                                            <div class="col-6 col-sm-6">
                                                <button type="button" class="btn btn-outline-info btn-sm btn-block" onclick="openGoogleMaps()"><i class="fas fa-location-arrow"></i> Cek</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer mt-3 justify-content-between">
                                        <button type="submit" class="btn btn-block btn-success btnSimpan">Simpan</button>
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

        // Start script kordinat
        if (navigator.geolocation) {
            var options = {
                enableHighAccuracy: true, // Mengaktifkan akurasi tinggi
                timeout: 10000, // Batas waktu menunggu lokasi dalam milidetik
                maximumAge: 0 // Tidak menggunakan data cache
            };

            navigator.geolocation.watchPosition(showPosition, showError, options);
        } else {
            alert("Geolokasi Tidak Didukung oleh Browser Ini");
        }
        // End script kordinat

        $('#dataCari').select2({
            dropdownParent: $('#modaltambah'),
            ajax: {
                url: "<?= base_url('get_data_penduduk'); ?>",
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
                url: "<?= site_url('/tmbUsul'); ?>",
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

                        if (response.error.du_foto_rumah_dalam) {
                            $('#du_foto_rumah_dalam').addClass('is-invalid');
                            $('.errordu_foto_rumah_dalam').html(response.error.du_foto_rumah_dalam);
                        } else {
                            $('#du_foto_rumah_dalam').removeClass('is-invalid');
                            $('.errordu_foto_rumah_dalam').html('');
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

                        if (response.error.du_accuracy) {
                            $('#du_accuracy').addClass('is-invalid');
                            $('.errordu_accuracy').html(response.error.du_accuracy);
                        } else {
                            $('#du_accuracy').removeClass('is-invalid');
                            $('.errordu_accuracy').html('');
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

        $('#tanggal_lahir').change(function() {
            var dob = new Date(document.getElementById('tanggal_lahir').value);
            var today = new Date();
            var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
            document.getElementById('du_usia').value = age;
            // $("#du_usia").change(function() {
            let usia = parseInt($("#du_usia").val(), 10);

            if (usia < 18) {
                $("#du_so_id_div").show();
                $("#du_so_id").prop("required", true).addClass("is-invalid");
                // $(".errordu_so_id").html("Status Orangtua Harus di Pilih");
            } else {
                $("#du_so_id_div").hide();
                $("#du_so_id").val("").prop("required", false).removeClass("is-invalid");
            }
            // });
        });

        $("#du_so_id").prop("required", false).removeClass("is-invalid");
        $("#du_so_id").change(function() {
            if ($(this).val() !== "") {
                $(this).removeClass("is-invalid");
            } else {
                $(this).prop("required", true).addClass("is-invalid");
                $(".errordu_so_id").html("Status Orangtua Harus di Pilih");
            }
        });

        var dropdown = document.getElementById("du_kate");
        var input = document.getElementById("du_nasu");

        if (dropdown.value === "1") {
            input.readOnly = false;
        } else {
            input.readOnly = true;
        }

        // Awalnya, dropdown tidak wajib diisi
        // $("#disabil_jenis").prop("required", false).removeClass("is-invalid");
        $("#disabil_jenis").val("").prop("required", false).removeClass("is-invalid");
        // Mendengarkan perubahan pada radio button
        $("input[type='radio']").change(function() {
            if ($("#chk-Yes").val() === "1") {
                // Jika yang dipilih adalah "Ya", dropdown menjadi wajib diisi
                $("#disabil_jenis").prop("required", true).addClass("is-invalid");
                // $(".errordisabil_jenis").html("Jenis Disabilitas Harus di Pilih");
            } else {
                // } else if ($("#chk-No").val() === "2") {
                // Jika yang dipilih adalah "Tidak", dropdown tidak wajib diisi
                $("#disabil_jenis").val("").prop("required", false).removeClass("is-invalid");
            }
        });

        // $("#disabil_jenis").val("").prop("required", false).removeClass("is-invalid");

        // Mendengarkan perubahan pada dropdown
        $("#disabil_jenis").change(function() {
            // Jika dropdown memiliki nilai yang dipilih, hapus class "is-invalid"
            if ($("#disabil_jenis").val() !== "") {
                $("#disabil_jenis").removeClass("is-invalid");
            } else {
                $("#disabil_jenis").prop("required", true).addClass("is-invalid");
                $(".errordisabil_jenis").html("Jenis Disabilitas Harus di Pilih");
            }
        });

        $('#du_usia').val(getAge);
        if ($('#du_usia').val() < 18) {
            $('.du_so_id_div').show();
        } else {
            $('.du_so_id_div').hide();
        }
    });

    $('#dataCari').on('change', (event) => {
        // console.log(event.target.value);
        getData(event.target.value).then(data => {
            $('#nokk').val(data.nokk);
            $('#kelurahan').val(data.kelurahan);
            $('#datarw').val(data.rw);
            $('#datart').val(data.rt);
            $('#alamat').val(data.alamat);
            $('#nama').val(data.nama);
            $('#nik').val(data.du_nik);
            $('#tempat_lahir').val(data.tempat_lahir);
            $('#tanggal_lahir').val(data.tanggal_lahir);
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
            $('#jenis_pendidikan').val(data.du_pendidikan_id);
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
                $('.du_so_id_div').show();
            } else {
                $('.du_so_id_div').hide();
            }
        });
    });

    async function getData(id) {
        let response = await fetch('/api_usulan/' + id);
        let data = await response.json();

        return data;
    }

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

    function openGoogleMaps() {
        var latitude = document.getElementById("du_latitude").value;
        var longitude = document.getElementById("du_longitude").value;

        // Membangun URL untuk Google Maps dengan koordinat yang dimasukkan
        var googleMapsURL = "https://www.google.com/maps?q=" + latitude + "," + longitude;

        // Membuka Google Maps di tab atau jendela baru
        window.open(googleMapsURL, "_blank");
    }


    function getLokasi() {
        if (navigator.geolocation) {
            // Menentukan waktu kedaluwarsa 10 detik dan akurasi tinggi
            var options = {
                timeout: 10000,
                enableHighAccuracy: true,
                maximumAge: 0 // Menghindari penggunaan cache
            };
            navigator.geolocation.getCurrentPosition(showPosition, showError, options);
        } else {
            alert("Geolokasi Tidak Didukung oleh Browser Ini.");
        }
    }

    function showPosition(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        var accuracy = position.coords.accuracy;

        document.getElementById("du_latitude").value = latitude;
        document.getElementById("du_longitude").value = longitude;
        document.getElementById("du_accuracy").value = accuracy;
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