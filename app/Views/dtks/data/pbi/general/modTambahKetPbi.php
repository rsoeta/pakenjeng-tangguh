<?php
$user = session()->get('role_id');
$nik = session()->get('nik');
$jabatan = session()->get('level');
$desa_id = session()->get('kode_desa');
?>


<!-- Modal -->
<div class="modal fade" id="modaltambah" tabindex="-1" aria-labelledby="modaltambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaltambahLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('tmbKetVvPbi', ['class' => 'formsimpan']) ?>
            <div class="modal-body">
                <?= csrf_field(); ?>
                <div class="form-group row nopadding">
                    <label class="col-form-label" for="vp_keterangan">Jenis Keterangan :</label>
                    <div class="col">
                        <textarea type="text" name="vp_keterangan" id="vp_keterangan" class="form-control"></textarea>
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
                    if (response.error) {
                        if (response.error.vp_keterangan) {
                            $('#vp_keterangan').addClass('is-invalid');
                            $('.errorvp_keterangan').html(response.error.vp_keterangan);
                        } else {
                            $('#vp_keterangan').removeClass('is-invalid');
                            $('.errorvp_keterangan').html('');
                        }

                    } else {
                        if (response.sukses) {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
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
                            $('#modaltambah').modal('hide');
                            window.location.reload();

                        }

                        $('#modaltambah').modal('hide');
                        window.location.reload();

                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        })
    });

    $('#datarw').change(function() {
        var desa = $('#kelurahan').val();
        var no_rw = $('#datarw').val();
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
                    $('#datart').html(html);
                }
            });
        } else {
            $('#datart').val('');
        }
    });
</script>