<!-- Modal -->
<div class="modal fade" id="modaltambahdusun" tabindex="-1" aria-labelledby="modaltambahdusunLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaltambahdusunLabel">Form Add Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open('wilayah/simpandataDusun', ['class' => 'formsave']) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">No. Dusun</label>
                    <input type="text" name="no_dusun" id="no_dusun" class="form-control form-control-sm" required>
                    <label for="">Nama Dusun</label>
                    <input type="text" name="nama_dusun" id="nama_dusun" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary tombolSave">Save</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.formsave').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "post",
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function(e) {
                    $('.tombolSave').prop('disabled', true);
                    $('.tombolSave').html('<i class="fa fa-spin fa-spinner"></i>')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Berhasil!',
                            response.success,
                            'success'
                        ).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });

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