<!-- Modal -->

<!-- Modal -->
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="modaleditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaleditLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo form_open('dtks/updatebnba', ['class' => 'formsimpan'])
                ?>
                <?= csrf_field(); ?>
                <div class="form-group row nopadding" hidden>
                    <label class="col-4 col-sm-4 col-form-label" for="id_dtks">ID Semesta</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="id_dtks" id="id_dtks" class="form-control form-control-sm" value="<?= set_value('id_dtks', $id_dtks); ?>">
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="province_id">Provinsi</label>
                    <div class="col-8 col-sm-8">
                        <select id="province_id" name="province_id" class="form-select form-select-sm">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($dataprov as $row) { ?>
                                <option <?php if ($province_id == $row['id']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorprovince_id"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="regency_id">Kabupaten/Kota</label>
                    <div class="col-8 col-sm-8">
                        <select id="regency_id" name="regency_id" class="form-select form-select-sm">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($datakab as $row) { ?>
                                <option <?php if ($regency_id == $row['id']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorregency_id"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="district_id">Kecamatan</label>
                    <div class="col-8 col-sm-8">
                        <select id="district_id" name="district_id" class="form-select form-select-sm">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($datakec as $row) { ?>
                                <option <?php if ($district_id == $row['id']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errordistrict_id"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="village_id">Desa/Kelurahan</label>
                    <div class="col-8 col-sm-8">
                        <select id="village_id" name="village_id" class="form-select form-select-sm">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($datadesa as $row) { ?>
                                <option <?php if ($village_id == $row['id']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorvillage_id"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="alamat">Alamat</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="<?= set_value('alamat', $alamat); ?>">
                        <div class="invalid-feedback erroralamat"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="dusun">Dusun</label>
                    <div class="col-8 col-sm-8">
                        <select id="dusun" name="dusun" class="form-select form-select-sm">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($datadusun as $row) { ?>
                                <option <?php if ($dusun == $row['nama_dusun']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['nama_dusun'] ?>"> <?php echo $row['nama_dusun']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errordusun"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="no_rw">No. RW</label>
                    <div class="col-8 col-sm-8">
                        <select id="no_rw" name="no_rw" class="form-select form-select-sm">
                            <option value="">-- Pilih RW --</option>
                            <?php foreach ($datarw as $row) { ?>
                                <option <?php if ($no_rw == $row['no_rw']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['no_rw'] ?>"> <?php echo $row['no_rw']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorrw"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="no_rt">No. RT</label>
                    <div class="col-8 col-sm-8">
                        <select id="no_rt" name="no_rt" class="form-select form-select-sm">
                            <option value="">-- Pilih RT --</option>
                            <?php foreach ($datart as $row) { ?>
                                <option <?php if ($no_rt == $row['no_rt']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['no_rt'] ?>"> <?php echo $row['no_rt']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorrt"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="nomor_kk">No. KK</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="nomor_kk" id="nomor_kk" class="form-control form-control-sm" value="<?= set_value('nomor_kk', $nomor_kk); ?>">
                        <div class="invalid-feedback errornomor_kk"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="nomor_nik">NIK</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="nomor_nik" id="nomor_nik" class="form-control form-control-sm" value="<?= set_value('nomor_nik', $nomor_nik); ?>">
                        <div class="invalid-feedback errornomor_nik"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="nama">Nama</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="nama" id="nama" class="form-control form-control-sm" value="<?= set_value('nama', $nama); ?>">
                        <div class="invalid-feedback errornama"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="tempat_lahir">Tempat Lahir</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control form-control-sm" value="<?= set_value('tempat_lahir', $tempat_lahir); ?>">
                        <div class="invalid-feedback errortempat_lahir"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="tanggal_lahir">Tanggal Lahir</label>
                    <div class="col-8 col-sm-8">
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control form-control-sm" value="<?= set_value('tanggal_lahir', $tanggal_lahir); ?>">
                        <div class="invalid-feedback errortanggal_lahir"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label for="jenis_kelamin" class="col-4 col-sm-4 col-form-label">Jenis Kelamin</label>
                    <div class="col-8 col-sm-8">
                        <select id="jenis_kelamin" name="jenis_kelamin" class="form-select form-select-sm">
                            <option value="">-- Pilih --</option>
                            <?php foreach ($jenisKelamin as $row) { ?>
                                <option <?php if ($jenis_kelamin == $row['IdJenKel']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['IdJenKel'] ?>"> <?php echo $row['NamaJenKel']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorjenis_kelamin"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="nama_ibu_kandung">Nama Ibu Kandung</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="nama_ibu_kandung" id="nama_ibu_kandung" class="form-control form-control-sm" value="<?= set_value('nama_ibu_kandung', $nama_ibu_kandung); ?>">
                        <div class="invalid-feedback errornama_ibu_kandung"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label for="hubungan_keluarga" class="col-4 col-sm-4 col-form-label">SHDK</label>
                    <div class="col-8 col-sm-8">
                        <select id="hubungan_keluarga" name="hubungan_keluarga" class="form-select form-select-sm">
                            <?php foreach ($datashdk as $row) { ?>
                                <option <?php if ($hubungan_keluarga == $row['id']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['id'] ?>"> <?= $row['jenis_shdk']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorhubungan_keluarga"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label for="status" class="col-4 col-sm-4 col-form-label">Status</label>
                    <div class="col-8 col-sm-8">
                        <select id="status" name="status" class="form-select form-select-sm">
                            <option value="0" <?= $status == 0 ? 'selected' : ""; ?>>Non-Aktif</option>
                            <option value="1" <?= $status == 1 ? 'selected' : ""; ?>>Aktif</option>
                        </select>
                        <div class="invalid-feedback errorketerangan"></div>
                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <button type="submit" class="btn btn-primary btn-block btnSimpan">Update</button>
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

                        if (response.error.province_id) {
                            $('#province_id').addClass('is-invalid');
                            $('.errorprovince_id').html(response.error.province_id);
                        } else {
                            $('#province_id').removeClass('is-invalid');
                            $('.errorprovince_id').html('');
                        }

                        if (response.error.regency_id) {
                            $('#regency_id').addClass('is-invalid');
                            $('.errorregency_id').html(response.error.regency_id);
                        } else {
                            $('#regency_id').removeClass('is-invalid');
                            $('.errorregency_id').html('');
                        }

                        if (response.error.district_id) {
                            $('#district_id').addClass('is-invalid');
                            $('.errordistrict_id').html(response.error.district_id);
                        } else {
                            $('#district_id').removeClass('is-invalid');
                            $('.errordistrict_id').html('');
                        }

                        if (response.error.village_id) {
                            $('#village_id').addClass('is-invalid');
                            $('.errorvillage_id').html(response.error.village_id);
                        } else {
                            $('#village_id').removeClass('is-invalid');
                            $('.errorvillage_id').html('');
                        }

                        if (response.error.alamat) {
                            $('#alamat').addClass('is-invalid');
                            $('.erroralamat').html(response.error.alamat);
                        } else {
                            $('#alamat').removeClass('is-invalid');
                            $('.erroralamat').html('');
                        }

                        if (response.error.dusun) {
                            $('#dusun').addClass('is-invalid');
                            $('.errordusun').html(response.error.dusun);
                        } else {
                            $('#dusun').removeClass('is-invalid');
                            $('.errordusun').html('');
                        }

                        if (response.error.no_rw) {
                            $('#no_rw').addClass('is-invalid');
                            $('.errorno_rw').html(response.error.no_rw);
                        } else {
                            $('#no_rw').removeClass('is-invalid');
                            $('.errorno_rw').html('');
                        }

                        if (response.error.no_rt) {
                            $('#no_rt').addClass('is-invalid');
                            $('.errorno_rt').html(response.error.no_rt);
                        } else {
                            $('#no_rt').removeClass('is-invalid');
                            $('.errorno_rt').html('');
                        }

                        if (response.error.nomor_kk) {
                            $('#nomor_kk').addClass('is-invalid');
                            $('.errornomor_kk').html(response.error.nomor_kk);
                        } else {
                            $('#nomor_kk').removeClass('is-invalid');
                            $('.errornomor_kk').html('');
                        }

                        if (response.error.nomor_nik) {
                            $('#nomor_nik').addClass('is-invalid');
                            $('.errornomor_nik').html(response.error.nomor_nik);
                        } else {
                            $('#nomor_nik').removeClass('is-invalid');
                            $('.errornomor_nik').html('');
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

                        if (response.error.nama_ibu_kandung) {
                            $('#nama_ibu_kandung').addClass('is-invalid');
                            $('.errornama_ibu_kandung').html(response.error.nama_ibu_kandung);
                        } else {
                            $('#nama_ibu_kandung').removeClass('is-invalid');
                            $('.errornama_ibu_kandung').html('');
                        }

                        if (response.error.hubungan_keluarga) {
                            $('#hubungan_keluarga').addClass('is-invalid');
                            $('.errorhubungan_keluarga').html(response.error.hubungan_keluarga);
                        } else {
                            $('#hubungan_keluarga').removeClass('is-invalid');
                            $('.errorhubungan_keluarga').html('');
                        }

                        if (response.error.status) {
                            $('#status').addClass('is-invalid');
                            $('.errorstatus').html(response.error.status);
                        } else {
                            $('#status').removeClass('is-invalid');
                            $('.errorstatus').html('');
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

                        }
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
            return false;
        });
    });
</script>