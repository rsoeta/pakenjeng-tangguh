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
                <?php echo form_open('updateAnomali2', ['class' => 'formupdate']) ?>
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
                            <label class="col-4 col-sm-4 col-form-label" for="va_nik">NIK</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_nik" id="va_nik" class="form-control form-control-sm" value="<?= set_value('va_nik', $va_nik); ?>" <?= $role > 3 ? 'readonly' : ''; ?>>
                                <div class="invalid-feedback errornik"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="va_nama">Nama</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_nama" id="va_nama" class="form-control form-control-sm" value="<?= set_value('va_nama', $va_nama); ?>">
                                <div class="invalid-feedback errorva_nama"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="va_nkk">No. KK</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_nkk" id="va_nkk" class="form-control form-control-sm" value="<?= set_value('va_nkk', $va_nkk); ?>">
                                <div class="invalid-feedback errorva_nkk"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label for="va_jk" class="col-4 col-sm-4 col-form-label">Jenis Kelamin</label>
                            <div class="col-8 col-sm-8">
                                <select id="va_jk" name="va_jk" class="form-select form-select-sm">
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($jenisKelamin as $row) { ?>
                                        <option <?php if ($va_jk == $row['IdJenKel']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['IdJenKel'] ?>"> <?php echo $row['NamaJenKel']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorva_jk"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="va_tmp_lhr">Tempat Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_tmp_lhr" id="va_tmp_lhr" class="form-control form-control-sm" value="<?= set_value('va_tmp_lhr', $va_tmp_lhr); ?>">
                                <div class="invalid-feedback errorva_tmp_lhr"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="va_tgl_lhr">Tanggal Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="date" name="va_tgl_lhr" id="va_tgl_lhr" class="form-control form-control-sm" value="<?= set_value('va_tgl_lhr', $va_tgl_lhr); ?>">
                                <div class="invalid-feedback errorva_tgl_lhr"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="form-group row nopadding" <?= $role > 0 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="va_prov">Provinsi</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_prov" id="va_prov" class="form-control form-control-sm" value="<?= set_value('va_prov', '32'); ?>">
                                <div class="invalid-feedback errorva_prov"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?= $role > 0 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="va_kab">Kabupaten</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_kab" id="va_kab" class="form-control form-control-sm" value="<?= set_value('va_kab', '32.05'); ?>">
                                <div class="invalid-feedback errorva_kab"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?= $role > 0 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="va_kec">Kecamatan</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_kec" id="va_kec" class="form-control form-control-sm" value="<?= set_value('va_kec', '32.05.33'); ?>">
                                <div class="invalid-feedback errorva_kec"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?= $role > 0 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="va_desa">Desa</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_desa" id="va_desa" class="form-control form-control-sm" value="<?= set_value('va_desa', $kode_desa); ?>">
                                <div class="invalid-feedback errorva_desa"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="va_alamat">Alamat</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_alamat" id="va_alamat" class="form-control form-control-sm" value="<?= set_value('va_alamat', $va_alamat); ?>">
                                <div class="invalid-feedback errorva_alamat"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="va_rw">No. RW</label>
                            <div class="col-8 col-sm-8">
                                <select id="va_rw" name="va_rw" class="form-select form-select-sm">
                                    <option value="">-- Pilih RW --</option>
                                    <?php foreach ($datarw as $row) { ?>
                                        <option <?= ($va_rw == $row['no_rw']) ? 'selected' : ''; ?> value="<?= $row['no_rw'] ?>"> <?php echo $row['no_rw']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorva_rw"></div>
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
                            <label class="col-4 col-sm-4 col-form-label" for="va_ibu">Nama Ibu</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="va_ibu" id="va_ibu" class="form-control form-control-sm" value="<?= set_value('va_ibu', $va_ibu); ?>">
                                <div class="invalid-feedback errorva_ibu"></div>
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

                        if (response.error.va_nik) {
                            $('#va_nik').addClass('is-invalid');
                            $('.errorva_nik').html(response.error.va_nik);
                        } else {
                            $('#va_nik').removeClass('is-invalid');
                            $('.errorva_nik').html('');
                        }

                        if (response.error.va_nkk) {
                            $('#va_nkk').addClass('is-invalid');
                            $('.errorva_nkk').html(response.error.va_nkk);
                        } else {
                            $('#va_nkk').removeClass('is-invalid');
                            $('.errorva_nkk').html('');
                        }

                        if (response.error.va_nama) {
                            $('#va_nama').addClass('is-invalid');
                            $('.errorva_nama').html(response.error.va_nama);
                        } else {
                            $('#va_nama').removeClass('is-invalid');
                            $('.errorva_nama').html('');
                        }

                        if (response.error.va_tmp_lahir) {
                            $('#va_tmp_lahir').addClass('is-invalid');
                            $('.errorva_tmp_lahir').html(response.error.va_tmp_lahir);
                        } else {
                            $('#va_tmp_lahir').removeClass('is-invalid');
                            $('.errorva_tmp_lahir').html('');
                        }

                        if (response.error.va_tgl_lahir) {
                            $('#va_tgl_lahir').addClass('is-invalid');
                            $('.errorva_tgl_lahir').html(response.error.va_tgl_lahir);
                        } else {
                            $('#va_tgl_lahir').removeClass('is-invalid');
                            $('.errorva_tgl_lahir').html('');
                        }

                        if (response.error.va_jenkel_id) {
                            $('#va_jenkel_id').addClass('is-invalid');
                            $('.errorva_jenkel_id').html(response.error.va_jenkel_id);
                        } else {
                            $('#va_jenkel_id').removeClass('is-invalid');
                            $('.errorva_jenkel_id').html('');
                        }

                        if (response.error.va_pekerjaan) {
                            $('#va_pekerjaan').addClass('is-invalid');
                            $('.errorva_pekerjaan').html(response.error.va_pekerjaan);
                        } else {
                            $('#va_pekerjaan').removeClass('is-invalid');
                            $('.errorva_pekerjaan').html('');
                        }

                        if (response.error.va_rw) {
                            $('#va_rw').addClass('is-invalid');
                            $('.errorva_rw').html(response.error.va_rw);
                        } else {
                            $('#va_rw').removeClass('is-invalid');
                            $('.errorva_rw').html('');
                        }

                        if (response.error.va_rt) {
                            $('#va_rt').addClass('is-invalid');
                            $('.errorva_rt').html(response.error.va_rt);
                        } else {
                            $('#va_rt').removeClass('is-invalid');
                            $('.errorva_rt').html('');
                        }

                        if (response.error.va_alamat) {
                            $('#va_alamat').addClass('is-invalid');
                            $('.errorva_alamat').html(response.error.va_alamat);
                        } else {
                            $('#va_alamat').removeClass('is-invalid');
                            $('.errorva_alamat').html('');
                        }

                        if (response.error.va_status) {
                            $('#va_status').addClass('is-invalid');
                            $('.errorva_status').html(response.error.va_status);
                        } else {
                            $('#va_status').removeClass('is-invalid');
                            $('.errorva_status').html('');
                        }

                        if (response.error.va_ibu_kandung) {
                            $('#va_ibu_kandung').addClass('is-invalid');
                            $('.errorva_ibu_kandung').html(response.error.va_ibu_kandung);
                        } else {
                            $('#va_ibu_kandung').removeClass('is-invalid');
                            $('.errorva_ibu_kandung').html('');
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