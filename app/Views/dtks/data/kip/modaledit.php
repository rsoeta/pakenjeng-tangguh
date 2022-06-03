<?php
$user = session()->get('role_id');
$nik = session()->get('nik');
$jabatan = session()->get('opr_sch');
$desa_id = session()->get('kode_desa');
$kec_id = '32.05.33';
?>


<!-- Modal -->
<div class="modal fade" id="modaledit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaleditLabel">Form. Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <?php echo form_open('updateKip', ['class' => 'formsimpan']) ?>
            <div class="modal-body">
                <?= csrf_field(); ?>
                <div class="row">
                    <div class="form-group row nopadding" hidden>
                        <label class="col-4 col-sm-4 col-form-label" for="dk_id">dk_id</label>
                        <div class="col-8 col-sm-8">
                            <input type="text" name="dk_id" dk_id="dk_id" class="form-control form-control-sm" value="<?= $dk_id; ?>">
                            <div class="invalid-feedback errordk_id"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_kks">No. KKS</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_kks" id="dk_kks" class="form-control form-control-sm" style="text-transform:uppercase" autocomplete="off" value="<?= set_value('dk_kks', $dk_kks); ?>">
                                <div class="invalid-feedback errordk_kks"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_kip">No. KIP</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_kip" id="dk_kip" class="form-control form-control-sm" style="text-transform:uppercase" autocomplete="off" value="<?= set_value('dk_kip', $dk_kip); ?>">
                                <div class="invalid-feedback errordk_kip"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nik">NIK</label>
                            <div class="col-8 col-sm-8">
                                <input type="number" name="dk_nik" id="dk_nik" class="form-control form-control-sm" autocomplete="off" value="<?= set_value('dk_nik', $dk_nik); ?>">
                                <div class="invalid-feedback errordk_nik"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nama_siswa">Nama Siswa</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_nama_siswa" id="dk_nama_siswa" class="form-control form-control-sm" style="text-transform:uppercase" value="<?= set_value('dk_nama_siswa', $dk_nama_siswa); ?>">
                                <div class="invalid-feedback errordk_nama_siswa"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_jenkel">Jenis Kelamin</label>
                            <div class="col-8 col-sm-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dk_jenkel" id="dk_jenkel1" value="1" <?= $dk_jenkel == 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="dk_jenkel1">
                                        LAKI-LAKI
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dk_jenkel" id="dk_jenkel2" value="2" <?= $dk_jenkel == 2 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="dk_jenkel2">
                                        PEREMPUAN
                                    </label>
                                </div>
                                <div class="invalid-feedback errordk_jenkel"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_tmp_lahir">Tempat Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_tmp_lahir" id="dk_tmp_lahir" class="form-control form-control-sm" style="text-transform:uppercase" value="<?= set_value('dk_tmp_lahir', $dk_tmp_lahir); ?>">
                                <div class="invalid-feedback errordk_tmp_lahir"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_tgl_lahir">Tgl Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="date" name="dk_tgl_lahir" id="dk_tgl_lahir" class="form-control form-control-sm" value="<?= set_value('dk_tgl_lahir', $dk_tgl_lahir); ?>">
                                <div class="invalid-feedback errordk_tgl_lahir"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_alamat">Alamat</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_alamat" id="dk_alamat" class="form-control form-control-sm" style="text-transform:uppercase;" value="<?= set_value('dk_alamat', $dk_alamat); ?>">
                                <div class="invalid-feedback errordk_alamat"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_rt">No.RT</label>
                            <div class="col-3 col-sm-3">
                                <input type="number" name="dk_rt" id="dk_rt" class="form-control form-control-sm" style="text-transform:uppercase;" value="<?= set_value('dk_rt', $dk_rt); ?>">
                                <div class="invalid-feedback errordk_rt"></div>
                            </div>
                            <label class="col-2 col-sm-2 col-form-label" for="dk_rw">No.RW</label>
                            <div class="col-3 col-sm-3">
                                <input type="number" name="dk_rw" id="dk_rw" class="form-control form-control-sm" style="text-transform:uppercase;" value="<?= set_value('dk_rw', $dk_rw); ?>">
                                <div class="invalid-feedback errordk_rw"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_desa">Desa/Kelurahan</label>
                            <div class="col-8 col-sm-8">
                                <select <?php echo $user >= 4  ? 'readonly="readonly" tabindex="-1" aria-disabled="true"' : '' ?> id="dk_desa" name="dk_desa" class="form-select form-select-sm">
                                    <option value="">-- Pilih Desa / Kelurahan --</option>
                                    <?php foreach ($desa as $row) { ?>
                                        <option <?php echo $desa_id == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordk_desa"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_kecamatan">Kecamatan</label>
                            <div class="col-8 col-sm-8">
                                <select <?php echo $user >= 4  ? 'readonly="readonly" tabindex="-1" aria-disabled="true"' : '' ?> id="dk_kecamatan" name="dk_kecamatan" class="form-select form-select-sm">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <?php foreach ($kecamatan as $row) { ?>
                                        <option <?php if ($kec_id == $row['id']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordk_kecamatan"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nama_ibu">Nama Ibu</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_nama_ibu" id="dk_nama_ibu" class="form-control form-control-sm" style="text-transform:uppercase" value="<?= set_value('dk_nama_ibu', $dk_nama_ibu); ?>">
                                <div class="invalid-feedback errordk_nama_ibu"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nama_ayah">Nama Ayah</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_nama_ayah" id="dk_nama_ayah" class="form-control form-control-sm" style="text-transform:uppercase" value="<?= set_value('dk_nama_ayah', $dk_nama_ayah); ?>">
                                <div class="invalid-feedback errordk_nama_ayah"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nama_sekolah">Nama Sekolah</label>
                            <div class="col-8 col-sm-8">
                                <select <?php echo $user > 2 ? 'readonly="readonly" tabindex="-1" aria-disabled="true"' : ''; ?> id="dk_nama_sekolah" name="dk_nama_sekolah" class="form-select form-select-sm">
                                    <?php foreach ($nama_sekolah as $row) { ?>
                                        <option <?php echo $jabatan == $row['opr_sch'] ? 'selected' : ''; ?> value="<?= $row['opr_sch']; ?>"><?= $row['opr_sch']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordk_nama_sekolah"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_jenjang">Jenjang</label>
                            <div class="col-8 col-sm-8">
                                <select id="dk_jenjang" name="dk_jenjang" class="form-select form-select-sm">
                                    <?php foreach ($jenjang_sekolah as $row) { ?>
                                        <option <?php echo $dk_jenjang == $row['sj_id'] ? 'selected' : ''; ?> value="<?= $row['sj_id']; ?>"><?= $row['sj_nama']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordk_jenjang"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_kelas">Kelas</label>
                            <div class="col-8 col-sm-8">
                                <input type="number" name="dk_kelas" id="dk_kelas" class="form-control form-control-sm" value="<?= set_value('dk_kelas', $dk_kelas); ?>">
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_partisipasi">Partisipasi</label>
                            <div class="col-8 col-sm-8">
                                <select id="dk_partisipasi" name="dk_partisipasi" class="form-select form-select-sm">
                                    <option value="">[ Kosong ]</option>
                                    <?php foreach ($partisipasi_sekolah as $row) { ?>
                                        <option <?php echo $dk_partisipasi == $row['ps_id'] ? 'selected' : '' ?> value="<?= $row['ps_id']; ?>"><?= $row['ps_nama']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordk_partisipasi"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_created_by">Editor</label>
                            <div class="col-8 col-sm-8">
                                <select <?php echo $user > 2 ? 'readonly="readonly" tabindex="-1" aria-disabled="true"' : ''; ?> id="dk_created_by" name="dk_created_by" class="form-select form-select-sm">
                                    <option value="">[ Kosong ]</option>
                                    <?php foreach ($users as $row) { ?>
                                        <option <?php echo $nik == $row['nik'] ? 'selected' : ''; ?> value="<?= $row['nik']; ?>"><?= strtoupper($row['fullname']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordk_created_by"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="card-body row">
                    <div class="col-md-6 mt-1">
                        <button type="button" class="btn btn-secondary btn-block" data-bs-dismiss="modal">Close</button>
                    </div>
                    <div class="col-md-6 mt-1">
                        <button type="submit" class="btn btn-primary btn-block btnsimpan">Update</button>
                    </div>
                </div>
            </div>
            <!-- </form> -->
            <?php echo form_close();
            ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.formsimpan').submit(function(e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.btnsimpan').attr('disable', 'disabled');
                    $('.btnsimpan').html('<i class="fa fa-spin fa-spinner"></i>');
                },
                complete: function() {
                    $('.btnsimpan').removeAttr('disable');
                    $('.btnsimpan').html('Update');
                },
                success: function(response) {
                    if (response.error) {
                        if (response.error.dk_kks) {
                            $('#dk_kks').addClass('is-invalid');
                            $('.errordk_kks').html(response.error.dk_kks);
                        } else {
                            $('#dk_kks').removeClass('is-invalid');
                            $('.errordk_kks').html('');
                        }

                        if (response.error.dk_kip) {
                            $('#dk_kip').addClass('is-invalid');
                            $('.errordk_kip').html(response.error.dk_kip);
                        } else {
                            $('#dk_kip').removeClass('is-invalid');
                            $('.errordk_kip').html('');
                        }

                        if (response.error.dk_nik) {
                            $('#dk_nik').addClass('is-invalid');
                            $('.errordk_nik').html(response.error.dk_nik);
                        } else {
                            $('#dk_nik').removeClass('is-invalid');
                            $('.errordk_nik').html('');
                        }

                        if (response.error.dk_nama_siswa) {
                            $('#dk_nama_siswa').addClass('is-invalid');
                            $('.errordk_nama_siswa').html(response.error.dk_nama_siswa);
                        } else {
                            $('#dk_nama_siswa').removeClass('is-invalid');
                            $('.errordk_nama_siswa').html('');
                        }

                        if (response.error.dk_jenkel) {
                            $('#dk_jenkel').addClass('is-invalid');
                            $('.errordk_jenkel').html(response.error.dk_jenkel);
                        } else {
                            $('#dk_jenkel').removeClass('is-invalid');
                            $('.errordk_jenkel').html('');
                        }

                        if (response.error.dk_tmp_lahir) {
                            $('#dk_tmp_lahir').addClass('is-invalid');
                            $('.errordk_tmp_lahir').html(response.error.dk_tmp_lahir);
                        } else {
                            $('#dk_tmp_lahir').removeClass('is-invalid');
                            $('.errordk_tmp_lahir').html('');
                        }

                        if (response.error.dk_tgl_lahir) {
                            $('#dk_tgl_lahir').addClass('is-invalid');
                            $('.errordk_tgl_lahir').html(response.error.dk_tgl_lahir);
                        } else {
                            $('#dk_tgl_lahir').removeClass('is-invalid');
                            $('.errordk_tgl_lahir').html('');
                        }

                        if (response.error.dk_alamat) {
                            $('#dk_alamat').addClass('is-invalid');
                            $('.errordk_alamat').html(response.error.dk_alamat);
                        } else {
                            $('#dk_alamat').removeClass('is-invalid');
                            $('.errordk_alamat').html('');
                        }

                        if (response.error.dk_nama_ibu) {
                            $('#dk_nama_ibu').addClass('is-invalid');
                            $('.errordk_nama_ibu').html(response.error.dk_nama_ibu);
                        } else {
                            $('#dk_nama_ibu').removeClass('is-invalid');
                            $('.errordk_nama_ibu').html('');
                        }

                        if (response.error.dk_nama_ayah) {
                            $('#dk_nama_ayah').addClass('is-invalid');
                            $('.errordk_nama_ayah').html(response.error.dk_nama_ayah);
                        } else {
                            $('#dk_nama_ayah').removeClass('is-invalid');
                            $('.errordk_nama_ayah').html('');
                        }

                        if (response.error.dk_nama_sekolah) {
                            $('#dk_nama_sekolah').addClass('is-invalid');
                            $('.errordk_nama_sekolah').html(response.error.dk_nama_sekolah);
                        } else {
                            $('#dk_nama_sekolah').removeClass('is-invalid');
                            $('.errordk_nama_sekolah').html('');
                        }

                        if (response.error.dk_jenjang) {
                            $('#dk_jenjang').addClass('is-invalid');
                            $('.errordk_jenjang').html(response.error.dk_jenjang);
                        } else {
                            $('#dk_jenjang').removeClass('is-invalid');
                            $('.errordk_jenjang').html('');
                        }

                        if (response.error.dk_kelas) {
                            $('#dk_kelas').addClass('is-invalid');
                            $('.errordk_kelas').html(response.error.dk_kelas);
                        } else {
                            $('#dk_kelas').removeClass('is-invalid');
                            $('.errordk_kelas').html('');
                        }

                        if (response.error.dk_partisipasi) {
                            $('#dk_partisipasi').addClass('is-invalid');
                            $('.errordk_partisipasi').html(response.error.dk_partisipasi);
                        } else {
                            $('#dk_partisipasi').removeClass('is-invalid');
                            $('.errordk_partisipasi').html('');
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

                        $('#modaledit').modal('hide');
                        table.draw();

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
    });
</script>