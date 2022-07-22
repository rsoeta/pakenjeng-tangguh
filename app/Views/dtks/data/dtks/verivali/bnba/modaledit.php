<!-- Modal -->
<?php
$level = session()->get('role_id');
?>
<!-- Modal -->
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="modaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="col-12 modal-title text-center" id="modaleditLabel"><?= $title; ?></h5>
            </div>
            <div class="modal-body">
                <?php echo form_open('updatebnba', ['class' => 'formsimpan']); ?>
                <?= csrf_field(); ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row nopadding" hidden>
                                <label class="col-4 col-sm-4 col-form-label" for="id_data">ID</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="id_data" id="id_data" class="form-control form-control-sm" value="<?= set_value('id_data', $db_id); ?>">
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="nomor_nik">NIK</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nomor_nik" id="nomor_nik" class="form-control form-control-sm" value="<?= set_value('nomor_nik', $nomor_nik); ?>" disabled>
                                    <div class="invalid-feedback errornomor_nik"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="nama">Nama</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nama" id="nama" class="form-control form-control-sm" value="<?= set_value('nama', $nama); ?>" disabled>
                                    <div class="invalid-feedback errornama"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="jenis_kelamin" class="col-4 col-sm-4 col-form-label">Jenis Kelamin</label>
                                <div class="col-8 col-sm-8">
                                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select form-select-sm" disabled>
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($jenisKelamin as $row) { ?>
                                            <option <?php if ($jenis_kelamin == $row['IdJenKel']) {
                                                        echo 'selected';
                                                    } ?> value="<?= $row['IdJenKel'] ?>"> <?php echo $row['NamaJenKel']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="invalid-feedback errorjenis_kelamin"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="nama_ibu_kandung">Nama Ibu</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nama_ibu_kandung" id="nama_ibu_kandung" class="form-control form-control-sm" value="<?= set_value('nama_ibu_kandung', $nama_ibu_kandung); ?>" disabled>
                                    <div class="invalid-feedback errornama_ibu_kandung"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="nomor_kk">No. KK</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nomor_kk" id="nomor_kk" class="form-control form-control-sm" value="<?= set_value('nomor_kk', $nomor_kk); ?>" disabled>
                                    <div class="invalid-feedback errornomor_kk"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="alamat">Alamat</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="<?= set_value('alamat', $alamat); ?>" disabled>
                                    <div class="invalid-feedback erroralamat"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="no_rt">No. RT</label>
                                <div class="col-8 col-sm-8">
                                    <select id="no_rt" name="no_rt" class="form-select form-select-sm" disabled>
                                        <option value="">-- Pilih RT --</option>
                                        <?php foreach ($datart as $row) { ?>
                                            <option <?php if ($no_rt == $row['no_rt']) {
                                                        echo 'selected';
                                                    } ?> value="<?= $row['no_rt'] ?>"> <?php echo $row['no_rt']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="invalid-feedback errorrt"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="no_rw">No. RW</label>
                                <div class="col-8 col-sm-8">
                                    <select id="no_rw" name="no_rw" class="form-select form-select-sm" disabled>
                                        <option value="">-- Pilih RW --</option>
                                        <?php foreach ($datarw as $row) { ?>
                                            <option <?php if ($no_rw == $row['no_rw']) {
                                                        echo 'selected';
                                                    } ?> value="<?= $row['no_rw'] ?>"> <?php echo $row['no_rw']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="invalid-feedback errorrw"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row nopadding">
                                <label for="status" class="col-4 col-sm-4 col-form-label">Status</label>
                                <div class="col-8 col-sm-8">
                                    <select id="status" name="status" class="form-select form-select-sm">
                                        <option value="">-- Pilih Status --</option>
                                        <?php foreach ($status as $row) { ?>
                                            <option <?php if (2 == $row['id_status']) {
                                                        echo 'selected';
                                                    } ?> value="<?= $row['id_status'] ?>"> <?= $row['jenis_status']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="invalid-feedback errorstatus"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="tanggal_kejadian" class="col-4 col-sm-4 col-form-label">Tgl Kejadian</label>
                                <div class="col-8 col-sm-8">
                                    <input type="date" name="tanggal_kejadian" id="tanggal_kejadian" class="form-control form-control-sm" value="<?= set_value('tanggal_kejadian', $tanggal_kejadian); ?>">
                                    <div class="invalid-feedback errortanggal_kejadian"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="no_registrasi_kejadian" class="col-4 col-sm-4 col-form-label">No.Reg Kejadian</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="no_registrasi_kejadian" id="no_registrasi_kejadian" class="form-control form-control-sm" value="<?= set_value('no_registrasi_kejadian', $no_registrasi_kejadian); ?>">
                                    <div class="invalid-feedback errorno_registrasi_kejadian"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- show image -->

                <div class="modal-footer justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
                    $('.btnsimpan').html('Update');
                },
                success: function(response) {
                    if (response.error) {

                        if (response.error.status) {
                            $('#status').addClass('is-invalid');
                            $('.errorstatus').html(response.error.status);
                        } else {
                            $('#status').removeClass('is-invalid');
                            $('.errorstatus').html('');
                        }
                        if (response.error.tanggal_kejadian) {
                            $('#tanggal_kejadian').addClass('is-invalid');
                            $('.errortanggal_kejadian').html(response.error.tanggal_kejadian);
                        } else {
                            $('#tanggal_kejadian').removeClass('is-invalid');
                            $('.errortanggal_kejadian').html('');
                        }
                        if (response.error.no_registrasi_kejadian) {
                            $('#no_registrasi_kejadian').addClass('is-invalid');
                            $('.errorno_registrasi_kejadian').html(response.error.no_registrasi_kejadian);
                        } else {
                            $('#no_registrasi_kejadian').removeClass('is-invalid');
                            $('.errorno_registrasi_kejadian').html('');
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
                            // window.location.reload();
                            table.draw();

                        }

                        $('#modaledit').modal('hide');
                        table.draw();
                        table1.draw();

                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        })
    });
</script>