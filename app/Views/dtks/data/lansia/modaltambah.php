<!-- Modal -->
<div class="modal fade" id="modaltambah" tabindex="-1" aria-labelledby="modaltambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaltambahLabel">Form Tambah Data</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('dtks/simpandata', ['class' => 'formdata']) ?>
            <?= csrf_field(); ?>
            <div class="modal-body">
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="FOTO_DIRI">Foto Identitas</label>
                    <div class="col-8">
                        <input type="text" name="FOTO_DIRI" id="FOTO_DIRI" class="form-control form-control-sm">
                        <div class="invalid-feedback errorFOTO_DIRI">
                        </div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="NAMA">Nama Lengkap</label>
                    <div class="col-8">
                        <input type="text" name="NAMA" id="NAMA" class="form-control form-control-sm">
                        <div class="invalid-feedback errorNAMA">
                        </div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="NIK">N.I.K</label>
                    <div class="col-8">
                        <input type="hidden" class="txt_csrfname" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                        <input type="text" name="NIK" id="NIK" class="form-control form-control-sm">
                        <div class="invalid-feedback errorNIK">
                        </div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="NO_KK">No. KK</label>
                    <div class="col-8">
                        <input type="text" name="NO_KK" id="NO_KK" class="form-control form-control-sm">
                        <div class="invalid-feedback errorNO_KK">
                        </div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="TEMPAT_LAHIR">Tempat Lahir</label>
                    <div class="col-8">
                        <input type="text" name="TEMPAT_LAHIR" id="TEMPAT_LAHIR" class="form-control form-control-sm">
                        <div class="invalid-feedback errorTEMPAT_LAHIR">
                        </div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="TANGGAL_LAHIR">Tanggal Lahir</label>
                    <div class="col-8">
                        <input type="date" name="TANGGAL_LAHIR" id="TANGGAL_LAHIR" class="form-control form-control-sm">
                        <div class="invalid-feedback errorTANGGAL_LAHIR">
                        </div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="ALAMAT">Alamat</label>
                    <div class="col-8">
                        <input type="text" name="ALAMAT" id="ALAMAT" class="form-control form-control-sm">
                        <div class="invalid-feedback errorALAMAT">
                        </div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="RW">No. RW</label>
                    <div class="col-8">
                        <select name="RW" id="RW" class="form-control form-control-sm">
                            <option value="">-Pilih-</option>
                            <?php foreach ($rws as $row) { ?>
                                <option value="<?= $row['no_rw']; ?>"><?= $row['no_rw']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorRW"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="RT">No. RT</label>
                    <div class="col-8">
                        <select name="RT" id="RT" class="form-control form-control-sm">
                            <option value="">-Pilih-</option>
                        </select>
                        <div class="invalid-feedback errorRT"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-form-label" for="FOTO_KK">Foto KK</label>
                    <div class="col-8">
                        <input type="text" name="FOTO_KK" id="FOTO_KK" class="form-control form-control-sm">
                        <div class="invalid-feedback errorFOTO_KK">
                        </div>
                    </div>
                </div>
                <?php if (session()->get('level') == 2) { ?>
                    <div class="form-group row nopadding">
                        <label class="col-4 col-form-label" for="Created_by">Created_by</label>
                        <div class="col-8">
                            <select name="Created_by" id="Created_by" class="form-control form-control-sm">
                                <option value="">-Pilih-</option>
                                <?php foreach ($operator as $row) { ?>
                                    <option <?php if (session()->get('nik') == $row['nik']) {
                                                echo 'selected';
                                            } ?> value="<?= $row['nik']; ?>"><?= strtoupper($row['fullname']); ?></option>
                                <?php } ?>
                            </select>
                            <div class="invalid-feedback errorRT"></div>
                        </div>
                    </div>
                <?php } ?>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary tombolSave">Save</button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        $('#RW').change(function() {

            var no_rw = $('#RW').val();

            var action = 'get_rt';

            if (no_rw != '') {
                $.ajax({
                    url: "<?php echo base_url('/dtks/janda/action'); ?>",
                    method: "POST",
                    data: {
                        no_rw: no_rw,
                        action: action
                    },
                    dataType: "JSON",
                    success: function(data) {
                        var html = '<option value="">-Pilih-</option>';

                        for (var count = 0; count < data.length; count++) {

                            html += '<option value="' + data[count].id_rt + '">' + data[count].id_rt + '</option>';

                        }

                        $('#RT').html(html);
                    }
                });
            } else {
                $('#RT').val('');
            }
        });
    });

    $(document).ready(function() {
        $('.formdhkp').submit(function(e) {
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
                    $('.tombolsave').html('Simpan');
                },
                success: function(response) {
                    if (response.error) {
                        if (response.error.nop) {
                            $('#nop').addClass('is-invalid');
                            $('.errorNop').html(response.error.nop);
                        } else {
                            $('#nop').removeClass('is-invalid');
                            $('.errorNop').html('');
                        }

                        if (response.error) {
                            $('#nama_wp').addClass('is-invalid');
                            $('.errornama_wp').html(response.error.nama_wp);
                        } else {
                            $('#nama_wp').removeClass('is-invalid');
                            $('.errornama_wp').html('');
                        }

                        if (response.error) {
                            $('#alamat_wp').addClass('is-invalid');
                            $('.erroralamat_wp').html(response.error.alamat_wp);
                        } else {
                            $('#alamat_wp').removeClass('is-invalid');
                            $('.erroralamat_wp').html('');
                        }

                        if (response.error) {
                            $('#alamat_op').addClass('is-invalid');
                            $('.erroralamat_op').html(response.error.alamat_op);
                        } else {
                            $('#alamat_op').removeClass('is-invalid');
                            $('.erroralamat_op').html('');
                        }

                        if (response.error) {
                            $('#bumi').addClass('is-invalid');
                            $('.errorbumi').html(response.error.bumi);
                        } else {
                            $('#bumi').removeClass('is-invalid');
                            $('.errorbumi').html('');
                        }

                        if (response.error) {
                            $('#bgn').addClass('is-invalid');
                            $('.errorbgn').html(response.error.bgn);
                        } else {
                            $('#bgn').removeClass('is-invalid');
                            $('.errorbgn').html('');
                        }

                        if (response.error) {
                            $('#pajak').addClass('is-invalid');
                            $('.errorpajak').html(response.error.pajak);
                        } else {
                            $('#pajak').removeClass('is-invalid');
                            $('.errorpajak').html('');
                        }

                        if (response.error) {
                            $('#nama_ktp').addClass('is-invalid');
                            $('.errornama_ktp').html(response.error.nama_ktp);
                        } else {
                            $('#nama_ktp').removeClass('is-invalid');
                            $('.errornama_ktp').html('');
                        }

                        if (response.error) {
                            $('#dusun').addClass('is-invalid');
                            $('.errordusun').html(response.error.dusun);
                        } else {
                            $('#dusun').removeClass('is-invalid');
                            $('.errordusun').html('');
                        }

                        if (response.error) {
                            $('#rw').addClass('is-invalid');
                            $('.errorrw').html(response.error.rw);
                        } else {
                            $('#rw').removeClass('is-invalid');
                            $('.errorrw').html('');
                        }

                        if (response.error) {
                            $('#rt').addClass('is-invalid');
                            $('.errorrt').html(response.error.rt);
                        } else {
                            $('#rt').removeClass('is-invalid');
                            $('.errorrt').html('');
                        }

                        if (response.error) {
                            $('#ket').addClass('is-invalid');
                            $('.errorket').html(response.error.ket);
                        } else {
                            $('#ket').removeClass('is-invalid');
                            $('.errorket').html('');
                        }
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.sukses
                        })

                        $('#modaltambah').modal('hide');
                        dhkp21();
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
            return false;
        });
    });

    $(document).ready(function() {
        // Initialize
        $("#nop").autocomplete({

            source: function(request, response) {

                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash

                // Fetch data
                $.ajax({
                    url: "<?= site_url('pbb/dhkp21/getUsers') ?>",
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: request.term,
                        [csrfName]: csrfHash // CSRF Token
                    },
                    success: function(data) {
                        // Update CSRF Token
                        $('.txt_csrfname').val(data.token);

                        response(data.data);
                    }
                });
            },
            select: function(event, ui) {
                // Set selection
                $('#nop').val(ui.item.label); // display the selected text
                $('#nama_wp').val(ui.item.value); // save selected id to input
                $('#alamat_wp').val(ui.item.value2); // save selected id to input
                return false;
            },
            focus: function(event, ui) {
                $("#nop").val(ui.item.label);
                $("#nama_wp").val(ui.item.value);
                $("#alamat_wp").val(ui.item.value2);
                return false;
            },
        });
    });
</script>