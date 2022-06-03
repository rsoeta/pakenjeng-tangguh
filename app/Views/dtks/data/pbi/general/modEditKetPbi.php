<?php
$user = session()->get('role_id');
$nik = session()->get('nik');
$jabatan = session()->get('level');
$desa_id = session()->get('kode_desa');
?>


<!-- Modal -->
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="modaleditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaleditLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('updKetVvPbi', ['class' => 'formsimpan']) ?>
            <div class="modal-body">
                <?= csrf_field(); ?>
                <div class="form-group row nopadding" hidden>
                    <label class="col-form-label" for="vp_id">Jenis Keterangan :</label>
                    <div class="col">
                        <input type="text" name="vp_id" id="vp_id" class="form-control" value="<?= set_value('vp_id', $vp_id); ?>">
                        <div class="invalid-feedback errorvp_id"></div>
                    </div>
                </div>
                <div class="form-group row nopadding">
                    <label class="col-form-label" for="vp_keterangan">Jenis Keterangan :</label>
                    <div class="col">
                        <textarea type="text" name="vp_keterangan" id="vp_keterangan" class="form-control"><?= $vp_keterangan; ?></textarea>
                        <div class="invalid-feedback errorvp_keterangan"></div>
                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <button type="submit" class="btn btn-primary btn-block btnsimpan">Save</button>
                </div>
                <!-- </form> -->
                <?php echo form_close();
                ?>
            </div>
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
                    $('.btnsimpan').html('Simpan');
                },
                success: function(response) {
                    if (response.sukses) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500,
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
                        $('#modaledit').modal('hide');
                        window.location.reload();

                    }

                    $('#modaledit').modal('hide');
                    window.location.reload();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        })
    });
</script>