<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="modaltambahrt" tabindex="-1" aria-labelledby="modaltambahrtLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaltambahrtLabel">Form Add Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open('wilayah/simpandatart', ['class' => 'formsave']) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">No. Dusun</label>
                    <input type="text" name="no_dusun" id="no_dusun" class="form-control form-control-sm" required>
                    <label for="">No. RW</label>
                    <input type="text" name="no_rw" id="no_rw" class="form-control form-control-sm" required>
                    <label for="">No. RT</label>
                    <input type="text" name="no_rt" id="no_rt" class="form-control form-control-sm" required>
                    <label for="">Nama RT</label>
                    <input type="text" name="nama_rt" id="nama_rt" class="form-control form-control-sm" required>
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