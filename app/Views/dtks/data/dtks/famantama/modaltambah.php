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
                            <li><a href="#step-1">Step 1<br /><small>Data Individu</small></a></li>
                            <!-- <li><a href="#step-2">Step 2<br /><small>Personal Info</small></a></li> -->
                            <li><a href="#step-3">Step 2<br /><small>Survey I</small></a></li>
                            <li><a href="#step-4">Step 3<br /><small>Survey II</small></a></li>
                        </ul>
                        <div>
                            <div id="step-1">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="form-group row nopadding nopadding mb-2">
                                            <label class="col-4 col-sm-2 col-form-label" for="dataCari">Cari Data</label>
                                            <div class="col-8 col-sm-10">
                                                <select name="dataCari" id="dataCari" class="form-control select2" style="width: 100%;">
                                                    <option value='0'>-- Pilih --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-2 col-form-label" for="fd_nkk">1. No. KK</label>
                                            <div class="col-8 col-sm-10">
                                                <input type="number" name="fd_nkk" id="fd_nkk" class="form-control form-control-sm" autocomplete="on" autofocus>
                                                <div class="invalid-feedback errorfd_nkk"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-2 col-form-label" for="fd_alamat">2. Alamat</label>
                                            <div class="col-8 col-sm-10">
                                                <input type="text" name="fd_alamat" id="fd_alamat" class="form-control form-control-sm" value="">
                                                <div class="invalid-feedback errorfd_alamat"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-2 col-form-label" for="fd_desa">3. Desa/Kel.</label>
                                            <div class="col-8 col-sm-10">
                                                <select <?= $user >= 3 ? 'disabled' : ''; ?> id="fd_desa" name="fd_desa" class="form-select form-select-sm">
                                                    <option value="">-- Pilih --</option>
                                                    <?php foreach ($desa as $row) { ?>
                                                        <option <?= $desa_id == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errorfd_desa"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-3 col-sm-2 col-form-label ml-5" for="datarw">No. RW</label>
                                            <div class="col-7 col-sm-9">
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
                                            <label class="col-3 col-sm-2 col-form-label ml-5" for="datart">No. RT</label>
                                            <div class="col-7 col-sm-9">
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
                                            <label class="col-4 col-sm-2 col-form-label" for="fd_nama_lengkap">4. Nama</label>
                                            <div class="col-8 col-sm-10">
                                                <input type="text" name="fd_nama_lengkap" id="fd_nama_lengkap" class="form-control form-control-sm">
                                                <div class="invalid-feedback errorfd_nama_lengkap"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-2 col-form-label" for="fd_nik">5. NIK</label>
                                            <div class="col-8 col-sm-10">
                                                <input type="number" name="fd_nik" id="fd_nik" class="form-control form-control-sm" autocomplete="off">
                                                <div class="invalid-feedback errorfd_nik"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row nopadding">
                                            <label class="col-4 col-sm-2 col-form-label" for="fd_shdk">6. SHDK</label>
                                            <div class="col-8 col-sm-10">
                                                <select id="fd_shdk" name="fd_shdk" class="form-select form-select-sm">
                                                    <option value="">-- Status Hubungan dalam Keluarga --</option>
                                                    <?php foreach ($shdk as $row) { ?>
                                                        <option value="<?= $row['tsf_id']; ?>"><?= $row['jenis_shdk']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="invalid-feedback errorfd_shdk"></div>
                                            </div>
                                        </div>
                                        <input type="datetime-local" name="updated_at" id="" value="<?= date('Y-m-d H:i:s'); ?>" hidden>
                                    </div>
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
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_sta_bangteti">7. Status Bangunan Tempat Tinggal Yang Ditempati</label>
                                                <div class="col-6">
                                                    <select name="fd_sta_bangteti" id="fd_sta_bangteti" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($sta_bangteti as $item) { ?>
                                                            <option value="<?= $item['tsb_id']; ?>"><?= $item['tsb_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_sta_bangteti"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_sta_lahteti">8. Status Lahan Tempat Tinggal Yang Ditempati</label>
                                                <div class="col-6">
                                                    <select name="fd_sta_lahteti" id="fd_sta_lahteti" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($sta_lahteti as $item) { ?>
                                                            <option value="<?= $item['tsl_id']; ?>"><?= $item['tsl_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_sta_lahteti"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_jenlai">9. Jenis Lantai</label>
                                                <div class="col-6">
                                                    <select name="fd_jenlai" id="fd_jenlai" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($jenlai as $item) { ?>
                                                            <option value="<?= $item['tjl_id']; ?>"><?= $item['tjl_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_jenlai"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_jendin">10. Jenis Dinding</label>
                                                <div class="col-6">
                                                    <select name="fd_jendin" id="fd_jendin" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($jendin as $item) { ?>
                                                            <option value="<?= $item['tjd_id']; ?>"><?= $item['tjd_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_jendin"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="kondisi_dinding">11. Kondisi Dinding</label>
                                                <div class="col-6">
                                                    <select name="kondisi_dinding" id="kondisi_dinding" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($kondisi as $item) { ?>
                                                            <option value="<?= $item['tk_id']; ?>"><?= $item['tk_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorkondisi_dinding"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_jentap">12. Jenis Atap</label>
                                                <div class="col-6">
                                                    <select name="fd_jentap" id="fd_jentap" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($jentap as $item) { ?>
                                                            <option value="<?= $item['tjt_id']; ?>"><?= $item['tjt_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_jentap"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="kondisi_atap">13. Kondisi Atap</label>
                                                <div class="col-6">
                                                    <select name="kondisi_atap" id="kondisi_atap" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($kondisi as $item) { ?>
                                                            <option value="<?= $item['tk_id']; ?>"><?= $item['tk_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorkondisi_atap"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_penghasilan">14. Penghasilan Rata-Rata/Bulan</label>
                                                <div class="col-6">
                                                    <select name="fd_penghasilan" id="fd_penghasilan" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($penghasilan as $item) { ?>
                                                            <option value="<?= $item['tph_id']; ?>"><?= $item['tph_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_penghasilan"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_pengeluaran">15. Pengeluaran Rata-Rata/Bulan</label>
                                                <div class="col-6">
                                                    <select name="fd_pengeluaran" id="fd_pengeluaran" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($pengeluaran as $item) { ?>
                                                            <option value="<?= $item['tpk_id']; ?>"><?= $item['tpk_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_pengeluaran"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_jml_tanggungan">16. Jumlah Tanggungan Keluarga</label>
                                                <div class="col-6">
                                                    <select name="fd_jml_tanggungan" id="fd_jml_tanggungan" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($jml_tanggungan as $item) { ?>
                                                            <option value="<?= $item['tjt_id']; ?>"><?= $item['tjt_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_jml_tanggungan"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="step-4" class="">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_roda_dua">17. Kepemilikan Kendaraan Roda 2</label>
                                                <div class="col-6">
                                                    <select name="fd_roda_dua" id="fd_roda_dua" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($roda_dua as $item) { ?>
                                                            <option value="<?= $item['trd_id']; ?>"><?= $item['trd_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_roda_dua"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_sumber_minum">18. Sumber Air Minum</label>
                                                <div class="col-6">
                                                    <select name="fd_sumber_minum" id="fd_sumber_minum" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($sumber_minum as $item) { ?>
                                                            <option value="<?= $item['tsm_id']; ?>"><?= $item['tsm_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_sumber_minum"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_cara_minum">19. Cara Memperoleh Air Minum</label>
                                                <div class="col-6">
                                                    <select name="fd_cara_minum" id="fd_cara_minum" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($cara_minum as $item) { ?>
                                                            <option value="<?= $item['tcm_id']; ?>"><?= $item['tcm_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_cara_minum"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_penerangan_utama">20. Sumber Penerangan Utama</label>
                                                <div class="col-6">
                                                    <select name="fd_penerangan_utama" id="fd_penerangan_utama" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($penerangan_utama as $item) { ?>
                                                            <option value="<?= $item['tpu_id']; ?>"><?= $item['tpu_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_penerangan_utama"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_daya_listrik">21. Daya Listrik Terpasang</label>
                                                <div class="col-6">
                                                    <select name="fd_daya_listrik" id="fd_daya_listrik" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($daya_listrik as $item) { ?>
                                                            <option value="<?= $item['tdl_id']; ?>"><?= $item['tdl_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_daya_listrik"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_bahan_masak">22. Bahan Bakar/Energi Utama Untuk Memasak</label>
                                                <div class="col-6">
                                                    <select name="fd_bahan_masak" id="fd_bahan_masak" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($bahan_masak as $item) { ?>
                                                            <option value="<?= $item['tbm_id']; ?>"><?= $item['tbm_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_bahan_masak"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_tempat_bab">23. Penggunaan Fasilitas Tempat Buang Air Besar</label>
                                                <div class="col-6">
                                                    <select name="fd_tempat_bab" id="fd_tempat_bab" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($tempat_bab as $item) { ?>
                                                            <option value="<?= $item['ttb_id']; ?>"><?= $item['ttb_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_tempat_bab"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_jenis_kloset">24. Jenis Kloset</label>
                                                <div class="col-6">
                                                    <select name="fd_jenis_kloset" id="fd_jenis_kloset" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($jenis_kloset as $item) { ?>
                                                            <option value="<?= $item['tjk_id']; ?>"><?= $item['tjk_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_jenis_kloset"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_tempat_tinja">25. Tempat Pembuangan Akhir Tinja</label>
                                                <div class="col-6">
                                                    <select name="fd_tempat_tinja" id="fd_tempat_tinja" class="form-select form-select-sm">
                                                        <option value="">-Pilih-</option>
                                                        <?php foreach ($tempat_tinja as $item) { ?>
                                                            <option value="<?= $item['ttt_id']; ?>"><?= $item['ttt_jenis']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_tempat_tinja"></div>
                                                </div>
                                            </div>
                                            <div class="form-group row nopadding">
                                                <label class="col-6 col-form-label" for="fd_pekerjaan_kk">26. Pekerjaan Kepala Keluarga</label>
                                                <div class="col-6">
                                                    <select id="fd_pekerjaan_kk" name="fd_pekerjaan_kk" class="form-select form-select-sm">
                                                        <option value="">-- Pilih Jenis Pekerjaan --</option>
                                                        <?php foreach ($pekerjaan as $row) { ?>
                                                            <option value="<?= $row['fpp_id'] ?>"> <?= $row['pk_nama']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <div class="invalid-feedback errorfd_pekerjaan_kk"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer mt-3 justify-content-between">
                                            <button type="submit" class="btn btn-block btn-primary btnSimpan" id="btnSimpan">Simpan</button>
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
    $('#dataCari').on('change', (event) => {
        // console.log(event.target.value);
        getData(event.target.value).then(data => {
            $('#fd_nkk').val(data.nokk);
            $('#fd_desa').val(data.kelurahan);
            $('#datarw').val(data.rw);
            $('#datart').val(data.rt);
            $('#fd_alamat').val(data.alamat);
            $('#fd_nama_lengkap').val(data.nama);
            $('#fd_nik').val(data.du_nik);
            $('#fd_pekerjaan_kk').val(data.jenis_pekerjaan);
            // $('#fd_shdk').val(data.shdk);
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
            let $kelurahan = $('#fd_desa').removeAttr('disabled', '');
            let $datarw = $('#datarw').removeAttr('disabled', '');
            setTimeout(function() {
                $kelurahan.attr('disabled', true);
                $datarw.attr('disabled', true);
            }, 500);
            let form = $('.formsimpan')[0];
            let data = new FormData(form);
            $.ajax({
                type: "POST",
                url: "<?= site_url('/tmbFamantama'); ?>",
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
                        if (response.error.fd_nama_lengkap) {
                            $('#fd_nama_lengkap').addClass('is-invalid');
                            $('.errorfd_nama_lengkap').html(response.error.fd_nama_lengkap);
                        } else {
                            $('#fd_nama_lengkap').removeClass('is-invalid');
                            $('.errorfd_nama_lengkap').html('');
                        }

                        if (response.error.fd_nik) {
                            $('#fd_nik').addClass('is-invalid');
                            $('.errorfd_nik').html(response.error.fd_nik);
                        } else {
                            $('#fd_nik').removeClass('is-invalid');
                            $('.errorfd_nik').html('');
                        }

                        if (response.error.fd_nkk) {
                            $('#fd_nkk').addClass('is-invalid');
                            $('.errorfd_nkk').html(response.error.fd_nkk);
                        } else {
                            $('#fd_nkk').removeClass('is-invalid');
                            $('.errorfd_nkk').html('');
                        }

                        if (response.error.fd_alamat) {
                            $('#fd_alamat').addClass('is-invalid');
                            $('.errorfd_alamat').html(response.error.fd_alamat);
                        } else {
                            $('#fd_alamat').removeClass('is-invalid');
                            $('.errorfd_alamat').html('');
                        }

                        if (response.error.datart) {
                            $('#datart').addClass('is-invalid');
                            $('.errordatart').html(response.error.datart);
                        } else {
                            $('#datart').removeClass('is-invalid');
                            $('.errordatart').html('');
                        }

                        if (response.error.datarw) {
                            $('#datarw').addClass('is-invalid');
                            $('.errordatarw').html(response.error.datarw);
                        } else {
                            $('#datarw').removeClass('is-invalid');
                            $('.errordatarw').html('');
                        }

                        if (response.error.fd_shdk) {
                            $('#fd_shdk').addClass('is-invalid');
                            $('.errorfd_shdk').html(response.error.fd_shdk);
                        } else {
                            $('#fd_shdk').removeClass('is-invalid');
                            $('.errorfd_shdk').html('');
                        }

                        if (response.error.fd_sta_bangteti) {
                            $('#fd_sta_bangteti').addClass('is-invalid');
                            $('.errorfd_sta_bangteti').html(response.error.fd_sta_bangteti);
                        } else {
                            $('#fd_sta_bangteti').removeClass('is-invalid');
                            $('.errorfd_sta_bangteti').html('');
                        }

                        if (response.error.fd_sta_lahteti) {
                            $('#fd_sta_lahteti').addClass('is-invalid');
                            $('.errorfd_sta_lahteti').html(response.error.fd_sta_lahteti);
                        } else {
                            $('#fd_sta_lahteti').removeClass('is-invalid');
                            $('.errorfd_sta_lahteti').html('');
                        }

                        if (response.error.fd_jenlai) {
                            $('#fd_jenlai').addClass('is-invalid');
                            $('.errorfd_jenlai').html(response.error.fd_jenlai);
                        } else {
                            $('#fd_jenlai').removeClass('is-invalid');
                            $('.errorfd_jenlai').html('');
                        }

                        if (response.error.fd_jendin) {
                            $('#fd_jendin').addClass('is-invalid');
                            $('.errorfd_jendin').html(response.error.fd_jendin);
                        } else {
                            $('#fd_jendin').removeClass('is-invalid');
                            $('.errorfd_jendin').html('');
                        }

                        if (response.error.kondisi_dinding) {
                            $('#kondisi_dinding').addClass('is-invalid');
                            $('.errorkondisi_dinding').html(response.error.kondisi_dinding);
                        } else {
                            $('#kondisi_dinding').removeClass('is-invalid');
                            $('.errorkondisi_dinding').html('');
                        }

                        if (response.error.fd_jentap) {
                            $('#fd_jentap').addClass('is-invalid');
                            $('.errorfd_jentap').html(response.error.fd_jentap);
                        } else {
                            $('#fd_jentap').removeClass('is-invalid');
                            $('.errorfd_jentap').html('');
                        }

                        if (response.error.kondisi_atap) {
                            $('#kondisi_atap').addClass('is-invalid');
                            $('.errorkondisi_atap').html(response.error.kondisi_atap);
                        } else {
                            $('#kondisi_atap').removeClass('is-invalid');
                            $('.errorkondisi_atap').html('');
                        }

                        if (response.error.fd_penghasilan) {
                            $('#fd_penghasilan').addClass('is-invalid');
                            $('.errorfd_penghasilan').html(response.error.fd_penghasilan);
                        } else {
                            $('#fd_penghasilan').removeClass('is-invalid');
                            $('.errorfd_penghasilan').html('');
                        }

                        if (response.error.fd_pengeluaran) {
                            $('#fd_pengeluaran').addClass('is-invalid');
                            $('.errorfd_pengeluaran').html(response.error.fd_pengeluaran);
                        } else {
                            $('#fd_pengeluaran').removeClass('is-invalid');
                            $('.errorfd_pengeluaran').html('');
                        }

                        if (response.error.fd_jml_tanggungan) {
                            $('#fd_jml_tanggungan').addClass('is-invalid');
                            $('.errorfd_jml_tanggungan').html(response.error.fd_jml_tanggungan);
                        } else {
                            $('#fd_jml_tanggungan').removeClass('is-invalid');
                            $('.errorfd_jml_tanggungan').html('');
                        }

                        if (response.error.fd_roda_dua) {
                            $('#fd_roda_dua').addClass('is-invalid');
                            $('.errorfd_roda_dua').html(response.error.fd_roda_dua);
                        } else {
                            $('#fd_roda_dua').removeClass('is-invalid');
                            $('.errorfd_roda_dua').html('');
                        }

                        if (response.error.fd_sumber_minum) {
                            $('#fd_sumber_minum').addClass('is-invalid');
                            $('.errorfd_sumber_minum').html(response.error.fd_sumber_minum);
                        } else {
                            $('#fd_sumber_minum').removeClass('is-invalid');
                            $('.errorfd_sumber_minum').html('');
                        }

                        if (response.error.fd_cara_minum) {
                            $('#fd_cara_minum').addClass('is-invalid');
                            $('.errorfd_cara_minum').html(response.error.fd_cara_minum);
                        } else {
                            $('#fd_cara_minum').removeClass('is-invalid');
                            $('.errorfd_cara_minum').html('');
                        }

                        if (response.error.fd_penerangan_utama) {
                            $('#fd_penerangan_utama').addClass('is-invalid');
                            $('.errorfd_penerangan_utama').html(response.error.fd_penerangan_utama);
                        } else {
                            $('#fd_penerangan_utama').removeClass('is-invalid');
                            $('.errorfd_penerangan_utama').html('');
                        }

                        if (response.error.fd_daya_listrik) {
                            $('#fd_daya_listrik').addClass('is-invalid');
                            $('.errorfd_daya_listrik').html(response.error.fd_daya_listrik);
                        } else {
                            $('#fd_daya_listrik').removeClass('is-invalid');
                            $('.errorfd_daya_listrik').html('');
                        }

                        if (response.error.fd_bahan_masak) {
                            $('#fd_bahan_masak').addClass('is-invalid');
                            $('.errorfd_bahan_masak').html(response.error.fd_bahan_masak);
                        } else {
                            $('#fd_bahan_masak').removeClass('is-invalid');
                            $('.errorfd_bahan_masak').html('');
                        }

                        if (response.error.fd_tempat_bab) {
                            $('#fd_tempat_bab').addClass('is-invalid');
                            $('.errorfd_tempat_bab').html(response.error.fd_tempat_bab);
                        } else {
                            $('#fd_tempat_bab').removeClass('is-invalid');
                            $('.errorfd_tempat_bab').html('');
                        }

                        if (response.error.fd_jenis_kloset) {
                            $('#fd_jenis_kloset').addClass('is-invalid');
                            $('.errorfd_jenis_kloset').html(response.error.fd_jenis_kloset);
                        } else {
                            $('#fd_jenis_kloset').removeClass('is-invalid');
                            $('.errorfd_jenis_kloset').html('');
                        }

                        if (response.error.fd_tempat_tinja) {
                            $('#fd_tempat_tinja').addClass('is-invalid');
                            $('.errorfd_tempat_tinja').html(response.error.fd_tempat_tinja);
                        } else {
                            $('#fd_tempat_tinja').removeClass('is-invalid');
                            $('.errorfd_tempat_tinja').html('');
                        }

                        if (response.error.fd_pekerjaan_kk) {
                            $('#fd_pekerjaan_kk').addClass('is-invalid');
                            $('.errorfd_pekerjaan_kk').html(response.error.fd_pekerjaan_kk);
                        } else {
                            $('#fd_pekerjaan_kk').removeClass('is-invalid');
                            $('.errorfd_pekerjaan_kk').html('');
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
                        }

                        $('#modaltambah').modal('hide');
                        table.draw();
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

        if ($('#du_usia').val() < 18) {
            $('.du_so_id_div').show();
        } else {
            $('.du_so_id_div').hide();
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

    function tampilkanLabel(checkboxId, labelId) {
        var checkbox = document.getElementById(checkboxId);
        var label = document.getElementById(labelId);

        if (checkbox.checked) {
            label.innerHTML = " Ya";
        } else {
            label.innerHTML = " Tidak";
        }
    }
</script>