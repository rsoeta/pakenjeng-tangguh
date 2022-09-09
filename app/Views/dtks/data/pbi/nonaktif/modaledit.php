<!-- Modal -->

<!-- Modal -->
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="modaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaleditLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php
            $role = session()->get('role_id');
            ?>
            <div class="modal-body">
                <?php echo form_open('updateInactive', ['class' => 'formsimpan']); ?>
                <?= csrf_field(); ?>
                <div class="form-group row nopadding" hidden>
                    <label class="col-4 col-sm-4 col-form-label" for="dpn_id">ID</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="dpn_id" id="dpn_id" class="form-control form-control-sm" value="<?= $dpn_id; ?>">
                        <div class="invalid-feedback errordpn_id"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-12">
                        <hr>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-form-label" for="cariPbi">Cari Data KIS</label>
                            <div class="col-8">
                                <select name="cariPbi" id="cariPbi" class="form-select form-select-sm select2" style="width: 100%;">
                                    <option value='0'>-- Select --</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_nama_kis">Nama</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_nama_kis" id="dpn_nama_kis" class="form-control form-control-sm" value="<?= $dpn_nama_kis; ?>" <?= $role > 3 ? 'readonly' : ''; ?>>
                                <div class="invalid-feedback errordpn_nama_kis"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_noka_kis">NOKA</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_noka_kis" id="dpn_noka_kis" class="form-control form-control-sm" value="<?= $dpn_noka_kis; ?>">
                                <div class="invalid-feedback errordpn_noka_kis"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_nik_kis">NIKKA</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_nik_kis" id="dpn_nik_kis" class="form-control form-control-sm" value="<?= $dpn_nik_kis; ?>">
                                <div class="invalid-feedback errordpn_nik_kis"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_tmp_lhr_kis">Tempat Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_tmp_lhr_kis" id="dpn_tmp_lhr_kis" class="form-control form-control-sm" value="<?= $dpn_tmp_lhr_kis; ?>">
                                <div class="invalid-feedback errordpn_tmp_lhr_kis"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_tgl_lhr_kis">Tanggal Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="date" name="dpn_tgl_lhr_kis" id="dpn_tgl_lhr_kis" class="form-control form-control-sm" value="<?= $dpn_tgl_lhr_kis; ?>">
                                <div class="invalid-feedback errordpn_tgl_lhr_kis"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?php echo session()->get('role_id') > 3 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_rw_kis">No. RW</label>
                            <div class="col-8 col-sm-8">
                                <select id="dpn_rw_kis" name="dpn_rw_kis" class="form-select form-select-sm">
                                    <option value="">-- Pilih RW --</option>
                                    <?php foreach ($datarw as $row) { ?>
                                        <option <?= $dpn_rw_kis == $row['no_rw'] ? 'selected' : ''; ?> value="<?= $row['no_rw'] ?>"> <?= $row['no_rw']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordpn_rw_kis"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_rt_kis">No. RT</label>
                            <div class="col-8 col-sm-8">
                                <select id="dpn_rt_kis" name="dpn_rt_kis" class="form-select form-select-sm">
                                    <option value="">-Kosong-</option>
                                    <?php foreach ($datart as $row) { ?>
                                        <option <?= $dpn_rt_kis == $row['no_rt'] ? 'selected' : ''; ?> value="<?= $row['no_rt'] ?>"> <?= $row['no_rt']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordpn_rt_kis"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_alamat_kis">Alamat</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_alamat_kis" id="dpn_alamat_kis" class="form-control form-control-sm" value="<?= $dpn_alamat_kis; ?>">
                                <div class="invalid-feedback errordpn_alamat_kis"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <hr>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-form-label" for="cariPm">Cari Data PM</label>
                            <div class="col-8">
                                <select name="cariPm" id="cariPm" class="form-select form-select-sm select2" style="width: 100%;">
                                    <option value="0">-- Select --</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_nama_pm">Nama</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_nama_pm" id="dpn_nama_pm" class="form-control form-control-sm" value="<?= $dpn_nama_pm; ?>">
                                <div class="invalid-feedback errordpn_nama_pm"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_nik_pm">NIK PM</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_nik_pm" id="dpn_nik_pm" class="form-control form-control-sm" value="<?= $dpn_nik_pm; ?>">
                                <div class="invalid-feedback errordpn_nik_pm"></div>
                            </div>
                        </div>

                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_nkk_pm">No. KK</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_nkk_pm" id="dpn_nkk_pm" class="form-control form-control-sm" value="<?= $dpn_nkk_pm; ?>">
                                <div class="invalid-feedback errordpn_nkk_pm"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_tmp_lhr_pm">Tempat Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_tmp_lhr_pm" id="dpn_tmp_lhr_pm" class="form-control form-control-sm" value="<?= $dpn_tmp_lhr_pm; ?>">
                                <div class="invalid-feedback errordpn_tmp_lhr_pm"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_tgl_lhr_pm">Tanggal Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="date" name="dpn_tgl_lhr_pm" id="dpn_tgl_lhr_pm" class="form-control form-control-sm" value="<?= $dpn_tgl_lhr_pm; ?>">
                                <div class="invalid-feedback errordpn_tgl_lhr_pm"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?php echo session()->get('role_id') > 2 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_kode_desa">Nama Desa</label>
                            <div class="col-8 col-sm-8">
                                <select id="dpn_kode_desa" name="dpn_kode_desa" class="form-select form-select-sm">
                                    <option value="">-- Pilih Desa --</option>
                                    <?php foreach ($desKels as $row) { ?>
                                        <option <?= session()->get('kode_desa') == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id'] ?>" <?= $dpn_kode_desa; ?>> <?php echo $row['name']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordpn_kode_desa"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?php echo session()->get('role_id') > 3 ? 'hidden' : ''; ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_rw_pm">No. RW</label>
                            <div class="col-8 col-sm-8">
                                <select id="dpn_rw_pm" name="dpn_rw_pm" class="form-select form-select-sm">
                                    <option value="">-- Pilih RW --</option>
                                    <?php foreach ($datarw as $row) { ?>
                                        <option <?= $dpn_rw_pm == $row['no_rw'] ? 'selected' : ''; ?> value="<?= $row['no_rw'] ?>"> <?= $row['no_rw']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordpn_rw_pm"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_rt_pm">No. RT</label>
                            <div class="col-8 col-sm-8">
                                <select id="dpn_rt_pm" name="dpn_rt_pm" class="form-select form-select-sm">
                                    <option value="">-Kosong-</option>
                                    <?php foreach ($datart as $row) { ?>
                                        <option <?= $dpn_rt_pm == $row['no_rt'] ? 'selected' : ''; ?> value="<?= $row['no_rt'] ?>"> <?= $row['no_rt']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordpn_rt_pm"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dpn_alamat_pm">Alamat</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dpn_alamat_pm" id="dpn_alamat_pm" class="form-control form-control-sm" value="<?= $dpn_alamat_pm; ?>">
                                <div class="invalid-feedback errordpn_alamat_pm"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mt-3 justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-primary float-end btnSimpan"> Update</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#cariPm').select2({
            dropdownParent: $('#modaledit'),
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

        $('#cariPbi').select2({
            dropdownParent: $('#modaledit'),
            ajax: {
                url: "<?php echo site_url('get_data_pbi'); ?>",
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

        $('.formsimpan').submit(function(e) {
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

                        if (response.error.dpn_nama_kis) {
                            $('#dpn_nama_kis').addClass('is-invalid');
                            $('.errordpn_nama_kis').html(response.error.dpn_nama_kis);
                        } else {
                            $('#dpn_nama_kis').removeClass('is-invalid');
                            $('.errordpn_nama_kis').html('');
                        }

                        if (response.error.dpn_noka_kis) {
                            $('#dpn_noka_kis').addClass('is-invalid');
                            $('.errordpn_noka_kis').html(response.error.dpn_noka_kis);
                        } else {
                            $('#dpn_noka_kis').removeClass('is-invalid');
                            $('.errordpn_noka_kis').html('');
                        }

                        if (response.error.dpn_nik_kis) {
                            $('#dpn_nik_kis').addClass('is-invalid');
                            $('.errordpn_nik_kis').html(response.error.dpn_nik_kis);
                        } else {
                            $('#dpn_nik_kis').removeClass('is-invalid');
                            $('.errordpn_nik_kis').html('');
                        }

                        if (response.error.dpn_tmp_lhr_kis) {
                            $('#dpn_tmp_lhr_kis').addClass('is-invalid');
                            $('.errordpn_tmp_lhr_kis').html(response.error.dpn_tmp_lhr_kis);
                        } else {
                            $('#dpn_tmp_lhr_kis').removeClass('is-invalid');
                            $('.errordpn_tmp_lhr_kis').html('');
                        }

                        if (response.error.dpn_tgl_lhr_kis) {
                            $('#dpn_tgl_lhr_kis').addClass('is-invalid');
                            $('.errordpn_tgl_lhr_kis').html(response.error.dpn_tgl_lhr_kis);
                        } else {
                            $('#dpn_tgl_lhr_kis').removeClass('is-invalid');
                            $('.errordpn_tgl_lhr_kis').html('');
                        }

                        if (response.error.dpn_rw_kis) {
                            $('#dpn_rw_kis').addClass('is-invalid');
                            $('.errordpn_rw_kis').html(response.error.dpn_rw_kis);
                        } else {
                            $('#dpn_rw_kis').removeClass('is-invalid');
                            $('.errordpn_rw_kis').html('');
                        }

                        if (response.error.dpn_rt_kis) {
                            $('#dpn_rt_kis').addClass('is-invalid');
                            $('.errordpn_rt_kis').html(response.error.dpn_rt_kis);
                        } else {
                            $('#dpn_rt_kis').removeClass('is-invalid');
                            $('.errordpn_rt_kis').html('');
                        }

                        if (response.error.dpn_alamat_kis) {
                            $('#dpn_alamat_kis').addClass('is-invalid');
                            $('.errordpn_alamat_kis').html(response.error.dpn_alamat_kis);
                        } else {
                            $('#dpn_alamat_kis').removeClass('is-invalid');
                            $('.errordpn_alamat_kis').html('');
                        }

                        if (response.error.dpn_nik_pm) {
                            $('#dpn_nik_pm').addClass('is-invalid');
                            $('.errordpn_nik_pm').html(response.error.dpn_nik_pm);
                        } else {
                            $('#dpn_nik_pm').removeClass('is-invalid');
                            $('.errordpn_nik_pm').html('');
                        }

                        if (response.error.dpn_nama_pm) {
                            $('#dpn_nama_pm').addClass('is-invalid');
                            $('.errordpn_nama_pm').html(response.error.dpn_nama_pm);
                        } else {
                            $('#dpn_nama_pm').removeClass('is-invalid');
                            $('.errordpn_nama_pm').html('');
                        }

                        if (response.error.dpn_nkk_pm) {
                            $('#dpn_nkk_pm').addClass('is-invalid');
                            $('.errordpn_nkk_pm').html(response.error.dpn_nkk_pm);
                        } else {
                            $('#dpn_nkk_pm').removeClass('is-invalid');
                            $('.errordpn_nkk_pm').html('');
                        }

                        if (response.error.dpn_tmp_lhr_pm) {
                            $('#dpn_tmp_lhr_pm').addClass('is-invalid');
                            $('.errordpn_tmp_lhr_pm').html(response.error.dpn_tmp_lhr_pm);
                        } else {
                            $('#dpn_tmp_lhr_pm').removeClass('is-invalid');
                            $('.errordpn_tmp_lhr_pm').html('');
                        }

                        if (response.error.dpn_tgl_lhr_pm) {
                            $('#dpn_tgl_lhr_pm').addClass('is-invalid');
                            $('.errordpn_tgl_lhr_pm').html(response.error.dpn_tgl_lhr_pm);
                        } else {
                            $('#dpn_tgl_lhr_pm').removeClass('is-invalid');
                            $('.errordpn_tgl_lhr_pm').html('');
                        }

                        if (response.error.dpn_rw_pm) {
                            $('#dpn_rw_pm').addClass('is-invalid');
                            $('.errordpn_rw_pm').html(response.error.dpn_rw_pm);
                        } else {
                            $('#dpn_rw_pm').removeClass('is-invalid');
                            $('.errordpn_rw_pm').html('');
                        }

                        if (response.error.dpn_rt_pm) {
                            $('#dpn_rt_pm').addClass('is-invalid');
                            $('.errordpn_rt_pm').html(response.error.dpn_rt_pm);
                        } else {
                            $('#dpn_rt_pm').removeClass('is-invalid');
                            $('.errordpn_rt_pm').html('');
                        }

                        if (response.error.dpn_alamat_pm) {
                            $('#dpn_alamat_pm').addClass('is-invalid');
                            $('.errordpn_alamat_pm').html(response.error.dpn_alamat_pm);
                        } else {
                            $('#dpn_alamat_pm').removeClass('is-invalid');
                            $('.errordpn_alamat_pm').html('');
                        }

                    } else {
                        if (response.sukses) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses!',
                                text: response.sukses
                            })

                            $('#modaledit').modal('hide');
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

    $('#cariPm').on('change', (event) => {
        // console.log(event.target.value);
        getPm(event.target.value).then(data => {
            $('#dpn_nkk_pm').val(data.nokk);
            $('#dpn_kode_desa').val(data.kelurahan);
            $('#dpn_rw_pm').val(data.rw);
            $('#dpn_rt_pm').val(data.rt);
            $('#dpn_alamat_pm').val(data.alamat);
            $('#dpn_nama_pm').val(data.nama);
            $('#dpn_nik_pm').val(data.du_nik);
            $('#dpn_tmp_lhr_pm').val(data.tempat_lahir);
            $('#dpn_tgl_lhr_pm').val(data.tanggal_lahir);
        });
    });

    $('#cariPbi').on('change', (event) => {
        // console.log(dataPbi);
        getPbi(event.target.value).then(dataPbi => {
            $('#dpn_nama_kis').val(dataPbi.nama);
            $('#dpn_noka_kis').val(dataPbi.noka);
            $('#dpn_nik_kis').val(dataPbi.nik);
            $('#dpn_tmp_lhr_kis').val(dataPbi.tmplhr);
            $('#dpn_tgl_lhr_kis').val(dataPbi.tgllhr);
            $('#dpn_rw_kis').val(dataPbi.rw);
            $('#dpn_rt_kis').val(dataPbi.rt);
            $('#dpn_alamat_kis').val(dataPbi.alamat);
        });
    });

    async function getPm(id) {
        let response = await fetch('/api_usulan/' + id);
        let data = await response.json();

        return data;
    }

    async function getPbi(id) {
        let response = await fetch('/api_pbi/' + id);
        let dataPbi = await response.json();

        return dataPbi;
    }
</script>