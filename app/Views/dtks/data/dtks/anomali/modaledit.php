<!-- Modal -->
<div class="modal fade" id="modalEdit" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php
            $role = session()->get('role_id');
            $kode_desa = session()->get('kode_desa');
            ?>
            <div class="modal-body">
                <?php echo form_open('updateAnomali', ['class' => 'formupdate']) ?>
                <?= csrf_field(); ?>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group row nopadding">
                            <label for="va_nama_anomali" class="col-4 col-sm-2 col-form-label">Ket. Anomali</label>
                            <div class="col-8 col-sm-6">
                                <select id="va_nama_anomali" name="va_nama_anomali" class="form-select form-select-sm" disabled>
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($keterangan as $row) { ?>
                                        <option <?php if ($va_nama_anomali == $row['ano_id']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['ano_id'] ?>"> <?php echo $row['ano_nama']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorva_nama_anomali"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-sm-6 col-12">
                        <div class="form-group row nopadding" hidden>
                            <label class="col-4 col-sm-4 col-form-label" for="va_id">ID Semesta</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_id" id="va_id" class="form-control form-control-sm" value="<?= set_value('va_id', $va_id); ?>">
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="db_nik">NIK</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_nik" id="db_nik" class="form-control form-control-sm" value="<?= set_value('db_nik', $db_nik); ?>" <?= $role > 3 ? 'readonly' : ''; ?>>
                                <div class="invalid-feedback errornik"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="db_nama">Nama</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_nama" id="db_nama" class="form-control form-control-sm" value="<?= set_value('db_nama', $db_nama); ?>">
                                <div class="invalid-feedback errordb_nama"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="db_nkk">No. KK</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_nkk" id="db_nkk" class="form-control form-control-sm" value="<?= set_value('db_nkk', $db_nkk); ?>">
                                <div class="invalid-feedback errordb_nkk"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label for="db_jenkel_id" class="col-4 col-sm-4 col-form-label">Jenis Kelamin</label>
                            <div class="col-8 col-sm-8">
                                <select id="db_jenkel_id" name="db_jenkel_id" class="form-select form-select-sm">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($jenisKelamin as $row) { ?>
                                        <option <?php if ($db_jenkel_id == $row['IdJenKel']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['IdJenKel'] ?>"> <?php echo $row['NamaJenKel']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordb_jenkel_id"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="db_tmp_lahir">Tempat Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_tmp_lahir" id="db_tmp_lahir" class="form-control form-control-sm" value="<?= set_value('db_tmp_lahir', $db_tmp_lahir); ?>">
                                <div class="invalid-feedback errordb_tmp_lahir"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="db_tgl_lahir">Tanggal Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="date" name="db_tgl_lahir" id="db_tgl_lahir" class="form-control form-control-sm" value="<?= set_value('db_tgl_lahir', $db_tgl_lahir); ?>">
                                <div class="invalid-feedback errordb_tgl_lahir"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="form-group row nopadding" <?= $role > 0 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="db_province">Provinsi</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_province" id="db_province" class="form-control form-control-sm" value="<?= set_value('db_province', '32'); ?>">
                                <div class="invalid-feedback errordb_province"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?= $role > 0 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="db_regency">Kabupaten</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_regency" id="db_regency" class="form-control form-control-sm" value="<?= set_value('db_regency', '32.05'); ?>">
                                <div class="invalid-feedback errordb_regency"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?= $role > 0 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="db_district">Kecamatan</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_district" id="db_district" class="form-control form-control-sm" value="<?= set_value('db_district', '32.05.33'); ?>">
                                <div class="invalid-feedback errordb_district"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?= $role > 0 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="db_village">Desa</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_village" id="db_village" class="form-control form-control-sm" value="<?= set_value('db_village', $kode_desa); ?>">
                                <div class="invalid-feedback errordb_village"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="db_alamat">Alamat</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_alamat" id="db_alamat" class="form-control form-control-sm" value="<?= set_value('db_alamat', $db_alamat); ?>">
                                <div class="invalid-feedback errordb_alamat"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="db_rw">No. RW</label>
                            <div class="col-8 col-sm-8">
                                <select id="db_rw" name="db_rw" class="form-select form-select-sm">
                                    <option value="">-- Pilih RW --</option>
                                    <?php foreach ($datarw as $row) { ?>
                                        <option <?= ($db_rw == $row['no_rw']) ? 'selected' : ''; ?> value="<?= $row['no_rw'] ?>"> <?php echo $row['no_rw']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordb_rw"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="db_rt">No. RT</label>
                            <div class="col-8 col-sm-8">
                                <select id="db_rt" name="db_rt" class="form-select form-select-sm">
                                    <option value="">-Kosong-</option>
                                    <?php foreach ($datart as $row) { ?>
                                        <option <?php if ($db_rt == $row['no_rt']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['no_rt'] ?>"> <?php echo $row['no_rt']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordb_rt"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="va_pekerjaan">Pekerjaan</label>
                            <div class="col-8 col-sm-8">
                                <select id="va_pekerjaan" name="va_pekerjaan" class="form-select form-select-sm">
                                    <option value="">-Kosong-</option>
                                    <?php foreach ($jenisPekerjaan as $row) { ?>
                                        <option <?php if ($va_pekerjaan == $row['pk_id']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['pk_id'] ?>"> <?php echo $row['pk_nama']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorva_pekerjaan"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="db_ibu_kandung">Nama Ibu</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="db_ibu_kandung" id="db_ibu_kandung" class="form-control form-control-sm" value="<?= set_value('db_ibu_kandung', $db_ibu_kandung); ?>">
                                <div class="invalid-feedback errordb_ibu_kandung"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?= ($role > 3) ? 'style="display: none;"' : ''; ?>>
                            <label for="va_status" class="col-4 col-sm-4 col-form-label">Ket. Verivali</label>
                            <div class="col-8 col-sm-8">
                                <select id="va_status" name="va_status" class="form-select form-select-sm">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($status as $row) { ?>
                                        <option selected value="<?= ($role > 3) ? 1 : $row['sta_id'] ?>"> <?php echo $row['sta_nama']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorva_status"></div>
                            </div>
                        </div>
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

        $('.formupdate').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "post",
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.btnSimpan').prop('disable', 'disabled');
                    $('.btnSimpan').html('<i class="fa fa-spin fa-spinner"></i>')
                },
                complete: function() {
                    $('.btnSimpan').removeAttr('disable');
                    $('.btnSimpan').html('Update');
                },
                success: function(response) {
                    if (response.error) {

                        if (response.error.db_nik) {
                            $('#db_nik').addClass('is-invalid');
                            $('.errordb_nik').html(response.error.db_nik);
                        } else {
                            $('#db_nik').removeClass('is-invalid');
                            $('.errordb_nik').html('');
                        }

                        if (response.error.db_nkk) {
                            $('#db_nkk').addClass('is-invalid');
                            $('.errordb_nkk').html(response.error.db_nkk);
                        } else {
                            $('#db_nkk').removeClass('is-invalid');
                            $('.errordb_nkk').html('');
                        }

                        if (response.error.db_nama) {
                            $('#db_nama').addClass('is-invalid');
                            $('.errordb_nama').html(response.error.db_nama);
                        } else {
                            $('#db_nama').removeClass('is-invalid');
                            $('.errordb_nama').html('');
                        }

                        if (response.error.db_tmp_lahir) {
                            $('#db_tmp_lahir').addClass('is-invalid');
                            $('.errordb_tmp_lahir').html(response.error.db_tmp_lahir);
                        } else {
                            $('#db_tmp_lahir').removeClass('is-invalid');
                            $('.errordb_tmp_lahir').html('');
                        }

                        if (response.error.db_tgl_lahir) {
                            $('#db_tgl_lahir').addClass('is-invalid');
                            $('.errordb_tgl_lahir').html(response.error.db_tgl_lahir);
                        } else {
                            $('#db_tgl_lahir').removeClass('is-invalid');
                            $('.errordb_tgl_lahir').html('');
                        }

                        if (response.error.db_jenkel_id) {
                            $('#db_jenkel_id').addClass('is-invalid');
                            $('.errordb_jenkel_id').html(response.error.db_jenkel_id);
                        } else {
                            $('#db_jenkel_id').removeClass('is-invalid');
                            $('.errordb_jenkel_id').html('');
                        }

                        if (response.error.va_pekerjaan) {
                            $('#va_pekerjaan').addClass('is-invalid');
                            $('.errorva_pekerjaan').html(response.error.va_pekerjaan);
                        } else {
                            $('#va_pekerjaan').removeClass('is-invalid');
                            $('.errorva_pekerjaan').html('');
                        }

                        if (response.error.db_rw) {
                            $('#db_rw').addClass('is-invalid');
                            $('.errordb_rw').html(response.error.db_rw);
                        } else {
                            $('#db_rw').removeClass('is-invalid');
                            $('.errordb_rw').html('');
                        }

                        if (response.error.db_rt) {
                            $('#db_rt').addClass('is-invalid');
                            $('.errordb_rt').html(response.error.db_rt);
                        } else {
                            $('#db_rt').removeClass('is-invalid');
                            $('.errordb_rt').html('');
                        }

                        if (response.error.db_alamat) {
                            $('#db_alamat').addClass('is-invalid');
                            $('.errordb_alamat').html(response.error.db_alamat);
                        } else {
                            $('#db_alamat').removeClass('is-invalid');
                            $('.errordb_alamat').html('');
                        }

                        if (response.error.va_status) {
                            $('#va_status').addClass('is-invalid');
                            $('.errorva_status').html(response.error.va_status);
                        } else {
                            $('#va_status').removeClass('is-invalid');
                            $('.errorva_status').html('');
                        }

                        if (response.error.db_ibu_kandung) {
                            $('#db_ibu_kandung').addClass('is-invalid');
                            $('.errordb_ibu_kandung').html(response.error.db_ibu_kandung);
                        } else {
                            $('#db_ibu_kandung').removeClass('is-invalid');
                            $('.errordb_ibu_kandung').html('');
                        }

                    } else {
                        if (response.sukses) {

                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Sukses!',
                                text: response.sukses,
                                showConfirmButton: false,
                                timer: 2000
                            })

                            $('#modalEdit').modal('hide');
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