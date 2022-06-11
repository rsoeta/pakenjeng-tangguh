<!-- Modal -->
<div class="modal fade" id="modaltambahketerangan" tabindex="-1" aria-labelledby="modaltambahketeranganLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaltambahketeranganLabel">Form. Tambah Keterangan</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open('dtks/pages/simpandata', ['class' => 'formsimpan']); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Nama Keterangan</label>
                    <input type="text" name="jenisketerangan" id="jenisketerangan" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary float-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary tombolSimpan">Simpan</button>
            </div>
            <?= form_close(); ?>
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
                beforeSend: function(e) {
                    $('.tombolSimpan').prop('disabled', true);
                    $('.tombolSimpan').html('<i class="fa fa-spin fa-spinner"></i>');
                },

                success: function(response) {
                    if (response.sukses) {
                        Swal.fire(
                            'Berhasil!',
                            response.sukses,
                            'success'
                        ).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
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