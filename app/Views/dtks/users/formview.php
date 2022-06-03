<!-- Modal -->
<div class="modal fade" id="modalview" tabindex="-1" aria-labelledby="modalviewLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalviewLabel"><?= $modTtl; ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('updateDataUser', ['class' => 'formupdate']) ?>
            <?= csrf_field(); ?>
            <div class="modal-body">
                <div class="form-group row mb-1" style="display: none;">
                    <label class="col-4 col-sm-4 col-form-label" for="id">ID</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="id" id="id" class="form-control form-control-sm" value="<?= set_value('id', $id); ?>" readonly>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label class="col-4 col-sm-4 col-form-label" for="nik">No. KTP</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="nik" id="nik" class="form-control form-control-sm" value="<?= set_value('nik', $nik); ?>">
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label class="col-4 col-sm-4 col-form-label" for="fullname">Nama Lengkap</label>
                    <div class="col-8 col-sm-8">
                        <input type="text" name="fullname" id="fullname" class="form-control form-control-sm" value="<?= set_value('fullname', $fullname); ?>">
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label class="col-4 col-sm-4 col-form-label" for="email">Email</label>
                    <div class="col-8 col-sm-8">
                        <input type="email" name="email" id="email" class="form-control form-control-sm" value="<?= set_value('email', $email); ?>">
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="kode_desa" class="col-4 col-sm-4">Desa</label>
                    <div class="col-8 col-sm-8">
                        <select type="number" name="kode_desa" id="kode_desa" class="form-control form-control-sm" required>
                            <option value="">[ Kosong ]</option>
                            <?php foreach ($desKels as $row) : ?>
                                <option <?php if ($row['id'] == $kode_desa) echo 'selected="selected"'; ?> value="<?= $row['id']; ?>" <?= set_select('kode_desa', $row['id']); ?>><?= $row['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="role" class="col-4 col-sm-4">Role</label>
                    <div class="col-8 col-sm-8">
                        <select type="number" name="role" id="role" class="form-control form-control-sm" required>
                            <option value="">[ Kosong ]</option>
                            <?php foreach ($roles as $row) : ?>
                                <option <?php if ($row['id_role'] == $role_id) echo 'selected="selected"'; ?> value="<?= $row['id_role']; ?>" <?= set_select('role', $row['id_role']); ?>><?= $row['nm_role']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label for="no_rw" class="col-4 col-sm-4">No. RW</label>
                    <div class="col-8 col-sm-8">
                        <select type="text" name="no_rw" id="no_rw" class="form-control form-control-sm">
                            <option value="">[ Kosong ]</option>
                            <?php foreach ($datarw as $row) : ?>
                                <option <?php if ($row['no_rw'] == $level) echo 'selected="selected"'; ?> value="<?= $row['no_rw']; ?>" <?= set_select('no_rw', $row['no_rw']); ?>><?= $row['no_rw']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-1">
                    <label class="col-4 col-sm-4 col-form-label" for="status">Status</label>
                    <div class="col-8 col-sm-8">
                        <select name="status" id="status" class="form-control form-control-sm">
                            <option value="0" <?php if ($status == 0) echo "selected"; ?>>Non-Aktif</option>
                            <option value="1" <?php if ($status == 1) echo "selected"; ?>>Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary btn-block tombolSave">Update</button>
                </div>
                <?= form_close(); ?>
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
                        $('.tombolSave').prop('disable', 'disabled');
                        $('.tombolSave').html('<i class="fa fa-spin fa-spinner"></i>')
                    },
                    complete: function() {
                        $('.tombolsave').removeAttr('disable');
                        $('.tombolsave').html('Update');
                    },
                    success: function(response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000,
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
                        $('#modalview').modal('hide');
                        window.location.reload();
                        // $('#tabelUser').draw();

                    },
                    error: function(xhr, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
                return false;
            });
        });
    </script>

</div>