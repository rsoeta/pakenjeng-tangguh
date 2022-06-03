<!-- Modal -->

<!-- Modal -->
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="modaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaleditLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php
            $role = session()->get('role_id');
            ?>
            <div class="modal-body">
                <?php echo form_open('updatepbi', ['class' => 'formsimpan'])
                ?>
                <?= csrf_field(); ?>
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <div class="form-group row nopadding" hidden>
                            <label class="col-4 col-sm-4 col-form-label" for="id">ID Semesta</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="id" id="id" class="form-control form-control-sm" value="<?= set_value('id', $id); ?>">
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="datapisat">PISAT</label>
                            <div class="col-8 col-sm-8">
                                <select id="datapisat" name="datapisat" class="form-select form-select-sm">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($datapisat as $row) { ?>
                                        <option <?php if ($pisat == $row['id']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['id'] ?>"> <?php echo $row['jenis_pisat']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordatapisat"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?php echo $role >= 4 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="noka">NOKA</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="noka" id="noka" class="form-control form-control-sm" value="<?= set_value('noka', $noka); ?>">
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?php echo $role >= 4 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="ps_noka">PS NOKA</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="ps_noka" id="ps_noka" class="form-control form-control-sm" value="<?= set_value('ps_noka', $ps_noka); ?>">
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="kkno">No. KK</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="kkno" id="kkno" class="form-control form-control-sm" value="<?= set_value('kkno', $kkno); ?>">
                                <div class="invalid-feedback errorkkno"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="nama">Nama</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="nama" id="nama" class="form-control form-control-sm" value="<?= set_value('nama', $nama); ?>" <?= $role > 3 ? 'readonly' : ''; ?>>
                                <div class="invalid-feedback errornama"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="nik">NIK</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="nik" id="nik" class="form-control form-control-sm" value="<?= set_value('nik', $nik); ?>">
                                <div class="invalid-feedback errornik"></div>
                            </div>
                        </div>
                        <?php if ($role <= 3) { ?>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="nik_siks">NIK SIKS-NG</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nik_siks" id="nik_siks" class="form-control form-control-sm" value="<?= set_value('nik_siks', $nik_siks); ?>" <?= ($role == 1 || $role == 3) ? '' : 'readonly'; ?>>
                                    <div class="invalid-feedback errornik_siks"></div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group row nopadding">
                            <label for="jenkel" class="col-4 col-sm-4 col-form-label">Jenis Kelamin</label>
                            <div class="col-8 col-sm-8">
                                <select id="jenkel" name="jenkel" class="form-select form-select-sm">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($jenisKelamin as $row) { ?>
                                        <option <?php if ($jenkel == $row['IdJenKel']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['IdJenKel'] ?>"> <?php echo $row['NamaJenKel']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorjenis_kelamin"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="tmplhr">Tempat Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="tmplhr" id="tmplhr" class="form-control form-control-sm" value="<?= set_value('tmplhr', $tmplhr); ?>">
                                <div class="invalid-feedback errortmplhr"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="tgllhr">Tanggal Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="date" name="tgllhr" id="tgllhr" class="form-control form-control-sm" value="<?= set_value('tgllhr', $tgllhr); ?>">
                                <div class="invalid-feedback errortgllhr"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="status_kawin">Status</label>
                            <div class="col-8 col-sm-8">
                                <select id="status_kawin" name="status_kawin" class="form-select form-select-sm">
                                    <option value="">-- Pilih Status Perkawinan --</option>
                                    <?php foreach ($statusKawin as $row) { ?>
                                        <option <?php if ($kdstawin == $row['idStatus']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['idStatus'] ?>"> <?php echo $row['StatusKawin']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorstatus_kawin"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="kelas_rawat">Kelas Rawat</label>
                            <div class="col-8 col-sm-8">
                                <select id="kelas_rawat" name="kelas_rawat" class="form-select form-select-sm">
                                    <?php if ($kelas_rawat == 0) { ?>
                                        <option value="0" selected>-Pilih-</option>
                                        <option value="1">SATU</option>
                                        <option value="2">DUA</option>
                                        <option value="3">TIGA</option>
                                    <?php } elseif ($kelas_rawat == 1) {  ?>
                                        <option value="1" selected>SATU</option>
                                        <option value="2">DUA</option>
                                        <option value="3">TIGA</option>
                                    <?php } elseif ($kelas_rawat == 2) {  ?>
                                        <option value="1">SATU</option>
                                        <option value="2" selected>DUA</option>
                                        <option value="3">TIGA</option>
                                    <?php } else { ?>
                                        <option value="1">SATU</option>
                                        <option value="2">DUA</option>
                                        <option value="3" selected>TIGA</option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorkelas_rawat"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="kodepos">Kode Pos</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="kodepos" id="kodepos" class="form-control form-control-sm" value="<?= set_value('kodepos', $kodepos); ?>">
                                <div class="invalid-feedback errorkodepos"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="alamat">Alamat</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="<?= set_value('alamat', $alamat); ?>">
                                <div class="invalid-feedback erroralamat"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?php echo session()->get('role_id') > 3 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="rw">No. RW</label>
                            <div class="col-8 col-sm-8">
                                <select id="rw" name="rw" class="form-select form-select-sm">
                                    <option value="">-- Pilih RW --</option>
                                    <?php foreach ($datarw as $row) { ?>
                                        <option <?php if ($rw == $row['no_rw']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['no_rw'] ?>"> <?php echo $row['no_rw']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorrw"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="rt">No. RT</label>
                            <div class="col-8 col-sm-8">
                                <select id="rt" name="rt" class="form-select form-select-sm">
                                    <option value="">-Kosong-</option>
                                    <?php foreach ($datart as $row) { ?>
                                        <option <?php if ($rt == $row['no_rt']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['no_rt'] ?>"> <?php echo $row['no_rt']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorrt"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="nmayah">Nama Ayah</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="nmayah" id="nmayah" class="form-control form-control-sm" value="<?= set_value('nmayah', $nmayah); ?>">
                                <div class="invalid-feedback errornmayah"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="nikayah">NIK Ayah</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="nikayah" id="nikayah" class="form-control form-control-sm" value="<?= set_value('nikayah', $nikayah); ?>">
                                <div class="invalid-feedback errornikayah"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="nmibu">Nama Ibu</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="nmibu" id="nmibu" class="form-control form-control-sm" value="<?= set_value('nmibu', $nmibu); ?>">
                                <div class="invalid-feedback errornmibu"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="nikibu">NIK Ibu</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="nikibu" id="nikibu" class="form-control form-control-sm" value="<?= set_value('nikibu', $nikibu); ?>">
                                <div class="invalid-feedback errornikibu"></div>
                            </div>
                        </div>
                        <?php if ($role == 0) { ?>
                            <div class="form-group row nopadding" <?= $role > 2 ? 'hidden' : ''; ?>>
                                <label class="col-4 col-sm-4 col-form-label" for="ket_aktivasi">Ket Aktivasi</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="ket_aktivasi" id="ket_aktivasi" class="form-control form-control-sm" value="<?= set_value('ket_aktivasi', 'SK Mensos 92/HUK/2021'); ?>">
                                    <div class="invalid-feedback errorket_aktivasi"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding" <?= $role > 2 ? 'hidden' : ''; ?>>
                                <label class="col-4 col-sm-4 col-form-label" for="kdkepwil">Kode KepWil</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="kdkepwil" id="kdkepwil" class="form-control form-control-sm" value="<?= set_value('kdkepwil', '05'); ?>">
                                    <div class="invalid-feedback errorkdkepwil"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding" <?= $role > 2 ? 'hidden' : ''; ?>>
                                <label class="col-4 col-sm-4 col-form-label" for="kdkc">Kode Kecamatan</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="kdkc" id="kdkc" class="form-control form-control-sm" value="<?= set_value('kdkc', '1009'); ?>">
                                    <div class="invalid-feedback errorkdkc"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding" <?= $role > 2 ? 'hidden' : ''; ?>>
                                <label class="col-4 col-sm-4 col-form-label" for="nmkc">Nama Kec</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nmkc" id="nmkc" class="form-control form-control-sm" value="<?= set_value('nmkc', 'TASIKMALAYA 11'); ?>">
                                    <div class="invalid-feedback errornmkc"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding" <?= $role > 2 ? 'hidden' : ''; ?>>
                                <label class="col-4 col-sm-4 col-form-label" for="kdprov">Kode Prov</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="kdprov" id="kdprov" class="form-control form-control-sm" value="<?= set_value('kdprov', '11'); ?>">
                                    <div class="invalid-feedback errorkdprov"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding" <?= $role > 2 ? 'hidden' : ''; ?>>
                                <label class="col-4 col-sm-4 col-form-label" for="nmprov">Nama Prov</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nmprov" id="nmprov" class="form-control form-control-sm" value="<?= set_value('nmprov', 'JAWA BARAT'); ?>">
                                    <div class="invalid-feedback errornmprov"></div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group row nopadding">
                            <label for="status" class="col-4 col-sm-4 col-form-label">Status</label>
                            <div class="col-8 col-sm-8">
                                <select id="status" name="status" class="form-select form-select-sm">
                                    <option value="">-- Pilih Status --</option>
                                    <?php foreach ($status as $row) { ?>
                                        <option <?php if ($stat == $row['id_status']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['id_status'] ?>"> <?php echo $row['jenis_status']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorstatus"></div>
                            </div>
                        </div>
                        <?php if ($role <= 3) { ?>
                            <div class="form-group row nopadding">
                                <label for="verivali_pbi" class="col-4 col-sm-4 col-form-label">Ket. Verivali</label>
                                <div class="col-8 col-sm-8">
                                    <select id="verivali_pbi" name="verivali_pbi" class="form-select form-select-sm">
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($verivali_pbi as $row) { ?>
                                            <option <?php if ($vv_pbi == $row['vp_id']) {
                                                        echo 'selected';
                                                    } ?> value="<?= $row['vp_id'] ?>"> <?php echo $row['vp_keterangan']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="invalid-feedback errorvv_pbi"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="modal-footer mt-3">
                        <button type="submit" class="btn btn-primary btn-block btnSimpan">Update</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('.formsimpan').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "post",
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.tombolSave').prop('disable', 'disabled');
                    $('.tombolSave').html('<i class="fa fa-spin fa-spinner"></i>')
                },
                complete: function() {
                    $('.tombolsave').removeAttr('disable');
                    $('.tombolsave').html('Update');
                },
                success: function(response) {
                    if (response.error) {

                        if (response.error.nik) {
                            $('#nik').addClass('is-invalid');
                            $('.errornik').html(response.error.nik);
                        } else {
                            $('#nik').removeClass('is-invalid');
                            $('.errornik').html('');
                        }

                        if (response.error.kkno) {
                            $('#kkno').addClass('is-invalid');
                            $('.errorkkno').html(response.error.kkno);
                        } else {
                            $('#kkno').removeClass('is-invalid');
                            $('.errorkkno').html('');
                        }

                        if (response.error.nama) {
                            $('#nama').addClass('is-invalid');
                            $('.errornama').html(response.error.nama);
                        } else {
                            $('#nama').removeClass('is-invalid');
                            $('.errornama').html('');
                        }

                        if (response.error.tmplhr) {
                            $('#tmplhr').addClass('is-invalid');
                            $('.errortmplhr').html(response.error.tmplhr);
                        } else {
                            $('#tmplhr').removeClass('is-invalid');
                            $('.errortmplhr').html('');
                        }

                        if (response.error.tgllhr) {
                            $('#tgllhr').addClass('is-invalid');
                            $('.errortgllhr').html(response.error.tgllhr);
                        } else {
                            $('#tgllhr').removeClass('is-invalid');
                            $('.errortgllhr').html('');
                        }

                        if (response.error.status_kawin) {
                            $('#status_kawin').addClass('is-invalid');
                            $('.errorstatus_kawin').html(response.error.status_kawin);
                        } else {
                            $('#status_kawin').removeClass('is-invalid');
                            $('.errorstatus_kawin').html('');
                        }

                        if (response.error.kelas_rawat) {
                            $('#kelas_rawat').addClass('is-invalid');
                            $('.errorkelas_rawat').html(response.error.kelas_rawat);
                        } else {
                            $('#kelas_rawat').removeClass('is-invalid');
                            $('.errorkelas_rawat').html('');
                        }

                        if (response.error.kodepos) {
                            $('#kodepos').addClass('is-invalid');
                            $('.errorkodepos').html(response.error.kodepos);
                        } else {
                            $('#kodepos').removeClass('is-invalid');
                            $('.errorkodepos').html('');
                        }

                        if (response.error.rw) {
                            $('#rw').addClass('is-invalid');
                            $('.errorrw').html(response.error.rw);
                        } else {
                            $('#rw').removeClass('is-invalid');
                            $('.errorrw').html('');
                        }

                        if (response.error.rt) {
                            $('#rt').addClass('is-invalid');
                            $('.errorrt').html(response.error.rt);
                        } else {
                            $('#rt').removeClass('is-invalid');
                            $('.errorrt').html('');
                        }

                        if (response.error.alamat) {
                            $('#alamat').addClass('is-invalid');
                            $('.erroralamat').html(response.error.alamat);
                        } else {
                            $('#alamat').removeClass('is-invalid');
                            $('.erroralamat').html('');
                        }

                        if (response.error.status) {
                            $('#status').addClass('is-invalid');
                            $('.errorstatus').html(response.error.status);
                        } else {
                            $('#status').removeClass('is-invalid');
                            $('.errorstatus').html('');
                        }

                        if (response.error.datapisat) {
                            $('#datapisat').addClass('is-invalid');
                            $('.errordatapisat').html(response.error.datapisat);
                        } else {
                            $('#datapisat').removeClass('is-invalid');
                            $('.errordatapisat').html('');
                        }

                        if (response.error.nmayah) {
                            $('#nmayah').addClass('is-invalid');
                            $('.errornmayah').html(response.error.nmayah);
                        } else {
                            $('#nmayah').removeClass('is-invalid');
                            $('.errornmayah').html('');
                        }

                        if (response.error.nikayah) {
                            $('#nikayah').addClass('is-invalid');
                            $('.errornikayah').html(response.error.nikayah);
                        } else {
                            $('#nikayah').removeClass('is-invalid');
                            $('.errornikayah').html('');
                        }

                        if (response.error.nmibu) {
                            $('#nmibu').addClass('is-invalid');
                            $('.errornmibu').html(response.error.nmibu);
                        } else {
                            $('#nmibu').removeClass('is-invalid');
                            $('.errornmibu').html('');
                        }

                        if (response.error.nikibu) {
                            $('#nikibu').addClass('is-invalid');
                            $('.errornikibu').html(response.error.nikibu);
                        } else {
                            $('#nikibu').removeClass('is-invalid');
                            $('.errornikibu').html('');
                        }

                    } else {
                        if (response.sukses) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses!',
                                text: response.sukses
                            })

                            $('#modaledit').modal('hide');
                            // $('#tabel_data').DataTable().ajax.reload();
                            table.draw();
                            table2.draw();

                        }
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
            return false;
        });
        $('#rw').change(function() {
            var desa = $('#datadesa').val();
            var no_rw = $('#rw').val();
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
                        $('#rt').html(html);
                    }
                });
            } else {
                $('#rt').val('');
            }
        });

    });
</script>