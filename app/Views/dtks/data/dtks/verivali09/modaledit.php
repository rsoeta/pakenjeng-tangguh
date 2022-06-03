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
                <?php echo form_open('ajax_update', ['class' => 'formsimpan'])
                ?>
                <?= csrf_field(); ?>
                <div class="form-group row nopadding" hidden>
                    <label class="col-4 col-sm-4 col-form-label" for="idv">ID Semesta</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="idv" id="idv" class="form-control form-control-sm" value="<?= set_value('idv', $idv); ?>">
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="nik">NIK</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="nik" id="nik" class="form-control form-control-sm" value="<?= set_value('nik', $nik); ?>">
                        <div class="invalid-feedback errornik"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="nkk">No. KK</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="nkk" id="nkk" class="form-control form-control-sm" value="<?= set_value('nkk', $nkk); ?>">
                        <div class="invalid-feedback errornkk"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="nama">Nama</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="nama" id="nama" class="form-control form-control-sm" value="<?= set_value('nama', $nama); ?>" readonly>
                        <div class="invalid-feedback errornama"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="tmp_lahir">Tempat Lahir</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="tmp_lahir" id="tmp_lahir" class="form-control form-control-sm" value="<?= set_value('tmp_lahir', $tmp_lahir); ?>">
                        <div class="invalid-feedback errortmp_lahir"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="tgl_lahir">Tgl Lahir</label>
                    <div class="col-8 col-sm-8">
                        <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control form-control-sm" value="<?= set_value('tgl_lahir', $tgl_lahir); ?>">
                        <div class="invalid-feedback errortgl_lahir"></div>
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
                    <label class="col-4 col-sm-4 col-form-label" for="rw">No. RW</label>
                    <div class="col-8 col-sm-8">
                        <select id="rw" name="rw" class="form-select form-select-sm">
                            <option value="">-- Pilih RW --</option>
                            <?php foreach ($datarw as $row) { ?>
                                <option <?php if ($rw == $row['rw']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['rw'] ?>"> <?php echo $row['rw']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorrw"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-4 col-sm-4 col-form-label" for="rt">No. RT</label>
                    <div class="col-8 col-sm-8">
                        <select id="rt" name="rt" class="form-select form-select-sm">
                            <option value="">-- Pilih RT --</option>
                            <?php foreach ($datart as $row) { ?>
                                <option <?php if ($rt == $row['rt']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['rt'] ?>"> <?php echo $row['rt']; ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback errorrt"></div>
                    </div>
                </div>
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

                <div class="form-group row nopadding">
                    <label for="ket" class="col-4 col-sm-4 col-form-label">Keterangan</label>
                    <div class="col-8 col-sm-8">
                        <select id="ket" name="ket" class="form-select form-select-sm">
                            <?php foreach ($keterangan as $row) { ?>
                                <option <?php if (session()->get('role_id') > 2) {
                                            echo 'disabled';
                                        } ?> <?php if ($ket == $row['id_ketvv']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['id_ketvv'] ?>"> <?= $row['jenis_keterangan']; ?></option>
                            <?php } ?>
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

                        if (response.error.nik) {
                            $('#nik').addClass('is-invalid');
                            $('.errornik').html(response.error.nik);
                        } else {
                            $('#nik').removeClass('is-invalid');
                            $('.errornik').html('');
                        }

                        if (response.error.nkk) {
                            $('#nkk').addClass('is-invalid');
                            $('.errornkk').html(response.error.nkk);
                        } else {
                            $('#nkk').removeClass('is-invalid');
                            $('.errornkk').html('');
                        }

                        if (response.error.nama) {
                            $('#nama').addClass('is-invalid');
                            $('.errornama').html(response.error.nama);
                        } else {
                            $('#nama').removeClass('is-invalid');
                            $('.errornama').html('');
                        }

                        if (response.error.tmp_lahir) {
                            $('#tmp_lahir').addClass('is-invalid');
                            $('.errortmp_lahir').html(response.error.tmp_lahir);
                        } else {
                            $('#tmp_lahir').removeClass('is-invalid');
                            $('.errortmp_lahir').html('');
                        }

                        if (response.error.tgl_lahir) {
                            $('#tgl_lahir').addClass('is-invalid');
                            $('.errortgl_lahir').html(response.error.tgl_lahir);
                        } else {
                            $('#tgl_lahir').removeClass('is-invalid');
                            $('.errortgl_lahir').html('');
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

                    } else {
                        if (response.sukses) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses!',
                                text: response.sukses
                            })

                            $('#modaledit').modal('hide');
                            $('#tabel_data').DataTable().ajax.reload();
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