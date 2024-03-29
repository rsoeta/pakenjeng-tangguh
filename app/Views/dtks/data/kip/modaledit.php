<?php
$user = session()->get('role_id');
$nik = session()->get('nik');
// $jabatan = session()->get('opr_sch');
$desa_id = session()->get('kode_desa');
$kec_id = '32.05.33';
?>


<!-- Modal -->
<div class="modal fade" id="modaledit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--purple);">
                <img src="<?= logoApp(); ?>" alt="<?= nameApp(); ?> Logo" class="brand-image" style="width:30px; margin-right: auto">
                <h5 class="modal-title" id="exampleModalLabel" style="color: white;"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open_multipart('', ['class' => 'formsimpan']) ?>
            <div class="modal-body">
                <?= csrf_field(); ?>
                <div class="row">
                    <!-- ID -->
                    <div class="form-group row nopadding" hidden>
                        <label class="col-4 col-sm-4 col-form-label" for="dk_id">dk_id</label>
                        <div class="col-8 col-sm-8">
                            <input type="text" name="dk_id" dk_id="dk_id" class="form-control form-control-sm" value="<?= set_value('dk_id', $dk_id); ?>">
                            <div class="invalid-feedback errordk_id"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- NO. KIP -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_kip">No. KIP</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_kip" id="dk_kip" class="form-control form-control-sm" style="text-transform:uppercase" autocomplete="off" value="<?= set_value('dk_kip', $dk_kip); ?>">
                                <div class="invalid-feedback errordk_kip"></div>
                            </div>
                        </div>
                        <!-- NISN -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nisn">NISN</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_nisn" id="dk_nisn" class="form-control form-control-sm" style="text-transform:uppercase" autocomplete="off" value="<?= set_value('dk_nisn', $dk_nisn); ?>">
                                <div class="invalid-feedback errordk_nisn"></div>
                            </div>
                        </div>
                        <!-- NO. KK -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nkk">No. KK</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_nkk" id="dk_nkk" class="form-control form-control-sm" style="text-transform:uppercase" autocomplete="off" value="<?= set_value('dk_nkk', $dk_nkk); ?>">
                                <div class="invalid-feedback errordk_nkk"></div>
                            </div>
                        </div>
                        <!-- NIK -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nik">NIK</label>
                            <div class="col-8 col-sm-8">
                                <input type="number" name="dk_nik" id="dk_nik" class="form-control form-control-sm" autocomplete="off" value="<?= set_value('dk_nik', $dk_nik); ?>">
                                <div class="invalid-feedback errordk_nik"></div>
                            </div>
                        </div>
                        <!-- Nama Siswa -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nama_siswa">Nama Siswa</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_nama_siswa" id="dk_nama_siswa" class="form-control form-control-sm" style="text-transform:uppercase" value="<?= set_value('dk_nama_siswa', $dk_nama_siswa); ?>">
                                <div class="invalid-feedback errordk_nama_siswa"></div>
                            </div>
                        </div>
                        <!-- Jenis Kelamin -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_jenkel">Jenis Kelamin</label>
                            <div class="col-8 col-sm-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dk_jenkel" id="dk_jenkel1" value="1" <?= $dk_jenkel == 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="dk_jenkel1">
                                        LAKI-LAKI
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="dk_jenkel" id="dk_jenkel2" value="2" <?= $dk_jenkel == 2 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="dk_jenkel2">
                                        PEREMPUAN
                                    </label>
                                </div>
                                <div class="invalid-feedback errordk_jenkel"></div>
                            </div>
                        </div>
                        <!-- Tempat Lahir -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_tmp_lahir">Tempat Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_tmp_lahir" id="dk_tmp_lahir" class="form-control form-control-sm" style="text-transform:uppercase" value="<?= set_value('dk_tmp_lahir', $dk_tmp_lahir); ?>">
                                <div class="invalid-feedback errordk_tmp_lahir"></div>
                            </div>
                        </div>
                        <!-- Tanggal Lahir -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_tgl_lahir">Tgl Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="date" name="dk_tgl_lahir" id="dk_tgl_lahir" class="form-control form-control-sm" value="<?= set_value('dk_tgl_lahir', $dk_tgl_lahir); ?>">
                                <div class="invalid-feedback errordk_tgl_lahir"></div>
                            </div>
                        </div>
                        <!-- Desa/Kelurahan -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_desa">Desa/Kelurahan</label>
                            <div class="col-8 col-sm-8">
                                <select <?php echo $user >= 4  ? 'readonly="readonly" tabindex="-1" aria-disabled="true"' : '' ?> id="dk_desa" name="dk_desa" class="form-select form-select-sm">
                                    <option value="">-- Pilih Desa / Kelurahan --</option>
                                    <?php foreach ($desa as $row) { ?>
                                        <option <?php echo $desa_id == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordk_desa"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Alamat -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_alamat">Alamat</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_alamat" id="dk_alamat" class="form-control form-control-sm" style="text-transform:uppercase;" value="<?= set_value('dk_alamat', $dk_alamat); ?>">
                                <div class="invalid-feedback errordk_alamat"></div>
                            </div>
                        </div>
                        <!-- No. RT/RW -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_rt">No.RT</label>
                            <div class="col-3 col-sm-3">
                                <input type="number" name="dk_rt" id="dk_rt" class="form-control form-control-sm" style="text-transform:uppercase;" value="<?= set_value('dk_rt', $dk_rt); ?>">
                                <div class="invalid-feedback errordk_rt"></div>
                            </div>
                            <label class="col-2 col-sm-2 col-form-label" for="dk_rw">No.RW</label>
                            <div class="col-3 col-sm-3">
                                <input <?= $user >= 4 ? 'disabled = "true"' : ''; ?> type="number" name="dk_rw" id="dk_rw" class="form-control form-control-sm" style="text-transform:uppercase;" value="<?= set_value('dk_rw', $dk_rw); ?>">
                                <div class="invalid-feedback errordk_rw"></div>
                            </div>
                        </div>
                        <!-- Nama Ibu -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nama_ibu">Nama Ibu</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_nama_ibu" id="dk_nama_ibu" class="form-control form-control-sm" style="text-transform:uppercase" value="<?= set_value('dk_nama_ibu', $dk_nama_ibu); ?>">
                                <div class="invalid-feedback errordk_nama_ibu"></div>
                            </div>
                        </div>
                        <!-- Nama Ayah -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nama_ayah">Nama Ayah</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_nama_ayah" id="dk_nama_ayah" class="form-control form-control-sm" style="text-transform:uppercase" value="<?= set_value('dk_nama_ayah', $dk_nama_ayah); ?>">
                                <div class="invalid-feedback errordk_nama_ayah"></div>
                            </div>
                        </div>
                        <!-- Nama Sekolah -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_nama_sekolah">Nama Sekolah</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="dk_nama_sekolah" id="dk_nama_sekolah" class="form-control form-control-sm" value="<?= set_value('dk_nama_sekolah', $dk_nama_sekolah); ?>">
                                <div class="invalid-feedback errordk_nama_sekolah"></div>
                            </div>
                        </div>
                        <!-- Kelas -->
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="dk_kelas">Kelas</label>
                            <div class="col-8 col-sm-8">
                                <input type="number" name="dk_kelas" id="dk_kelas" class="form-control form-control-sm" value="<?= set_value('dk_kelas', $dk_kelas); ?>">
                            </div>
                        </div>
                        <!-- Dokumen -->
                        <div class="col-sm-12 col-12 mt-2">
                            <label class="label-center mt-2">Foto KK</label>
                            <div class="form-group row nopadding">
                                <div class="col-10 mt-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-address-card"></i></span>
                                        </div>
                                        <input type="file" class="form-control form-control-sm" spellcheck="false" name="dk_foto_identitas" id="dk_foto_identitas" onchange="previewImgId()" accept="image/*" capture />
                                    </div>
                                </div>
                                <div class="col-2">
                                    <a download="<?= $dk_foto_identitas; ?>" href="<?= kip_foto($dk_foto_identitas, 'nonkip_foto_kk'); ?>">
                                        <img class="img-preview-id" src="<?= kip_foto($dk_foto_identitas, 'nonkip_foto_kk'); ?>" style="height: 30px; width: 40px; border-radius: 2px;">
                                    </a>
                                    <br>
                                    <p for="dk_foto_identitas">Preview</p>
                                    <div class="invalid-feedback errordk_foto_identitas"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="card-body row">
                    <div class="col-6">
                        <button type="button" class="btn btn-secondary btn-block" data-bs-dismiss="modal">Tutup</button>
                    </div>
                    <div class="col-6">
                        <input type="datetime-local" name="updated_at" id="" value="<?= date('Y-m-d H:i:s'); ?>" hidden>
                        <button type="submit" class="btn btn-primary btn-block btnSimpan">Update</button>
                    </div>
                </div>
            </div>
            <!-- </form> -->
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.btnSimpan').click(function(e) {
            e.preventDefault();
            let $kelurahan = $('#dk_desa').removeAttr('disabled', '');
            let $datarw = $('#dk_rw').removeAttr('disabled', '');
            setTimeout(function() {
                $kelurahan.attr('disabled', true);
                $datarw.attr('disabled', true);
            }, 500);
            let form = $('.formsimpan')[0];
            let data = new FormData(form);
            $.ajax({
                type: "POST",
                url: "<?= site_url('/upNonKip'); ?>",
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
                beforeSend: function() {
                    $('.btnSimpan').attr('disable', 'disabled');
                    $('.btnSimpan').html('<i class="fa fa-spin fa-spinner"></i>');
                },
                complete: function() {
                    $('.btnSimpan').removeAttr('disable');
                    $('.btnSimpan').html('Update');
                },
                success: function(response) {
                    if (response.error) {
                        if (response.error.dk_nisn) {
                            $('#dk_nisn').addClass('is-invalid');
                            $('.errordk_nisn').html(response.error.dk_nisn);
                        } else {
                            $('#dk_nisn').removeClass('is-invalid');
                            $('.errordk_nisn').html('');
                        }

                        if (response.error.dk_nkk) {
                            $('#dk_nkk').addClass('is-invalid');
                            $('.errordk_nkk').html(response.error.dk_nkk);
                        } else {
                            $('#dk_nkk').removeClass('is-invalid');
                            $('.errordk_nkk').html('');
                        }

                        if (response.error.dk_kip) {
                            $('#dk_kip').addClass('is-invalid');
                            $('.errordk_kip').html(response.error.dk_kip);
                        } else {
                            $('#dk_kip').removeClass('is-invalid');
                            $('.errordk_kip').html('');
                        }

                        if (response.error.dk_nik) {
                            $('#dk_nik').addClass('is-invalid');
                            $('.errordk_nik').html(response.error.dk_nik);
                        } else {
                            $('#dk_nik').removeClass('is-invalid');
                            $('.errordk_nik').html('');
                        }

                        if (response.error.dk_nama_siswa) {
                            $('#dk_nama_siswa').addClass('is-invalid');
                            $('.errordk_nama_siswa').html(response.error.dk_nama_siswa);
                        } else {
                            $('#dk_nama_siswa').removeClass('is-invalid');
                            $('.errordk_nama_siswa').html('');
                        }

                        if (response.error.dk_jenkel) {
                            $('#dk_jenkel').addClass('is-invalid');
                            $('.errordk_jenkel').html(response.error.dk_jenkel);
                        } else {
                            $('#dk_jenkel').removeClass('is-invalid');
                            $('.errordk_jenkel').html('');
                        }

                        if (response.error.dk_tmp_lahir) {
                            $('#dk_tmp_lahir').addClass('is-invalid');
                            $('.errordk_tmp_lahir').html(response.error.dk_tmp_lahir);
                        } else {
                            $('#dk_tmp_lahir').removeClass('is-invalid');
                            $('.errordk_tmp_lahir').html('');
                        }

                        if (response.error.dk_tgl_lahir) {
                            $('#dk_tgl_lahir').addClass('is-invalid');
                            $('.errordk_tgl_lahir').html(response.error.dk_tgl_lahir);
                        } else {
                            $('#dk_tgl_lahir').removeClass('is-invalid');
                            $('.errordk_tgl_lahir').html('');
                        }

                        if (response.error.dk_alamat) {
                            $('#dk_alamat').addClass('is-invalid');
                            $('.errordk_alamat').html(response.error.dk_alamat);
                        } else {
                            $('#dk_alamat').removeClass('is-invalid');
                            $('.errordk_alamat').html('');
                        }

                        if (response.error.dk_rt) {
                            $('#dk_rt').addClass('is-invalid');
                            $('.errordk_rt').html(response.error.dk_rt);
                        } else {
                            $('#dk_rt').removeClass('is-invalid');
                            $('.errordk_rt').html('');
                        }

                        if (response.error.dk_rw) {
                            $('#dk_rw').addClass('is-invalid');
                            $('.errordk_rw').html(response.error.dk_rw);
                        } else {
                            $('#dk_rw').removeClass('is-invalid');
                            $('.errordk_rw').html('');
                        }

                        if (response.error.dk_desa) {
                            $('#dk_desa').addClass('is-invalid');
                            $('.errordk_desa').html(response.error.dk_desa);
                        } else {
                            $('#dk_desa').removeClass('is-invalid');
                            $('.errordk_desa').html('');
                        }

                        if (response.error.dk_nama_sekolah) {
                            $('#dk_nama_sekolah').addClass('is-invalid');
                            $('.errordk_nama_sekolah').html(response.error.dk_nama_sekolah);
                        } else {
                            $('#dk_nama_sekolah').removeClass('is-invalid');
                            $('.errordk_nama_sekolah').html('');
                        }

                        if (response.error.dk_nama_ibu) {
                            $('#dk_nama_ibu').addClass('is-invalid');
                            $('.errordk_nama_ibu').html(response.error.dk_nama_ibu);
                        } else {
                            $('#dk_nama_ibu').removeClass('is-invalid');
                            $('.errordk_nama_ibu').html('');
                        }

                        if (response.error.dk_nama_ayah) {
                            $('#dk_nama_ayah').addClass('is-invalid');
                            $('.errordk_nama_ayah').html(response.error.dk_nama_ayah);
                        } else {
                            $('#dk_nama_ayah').removeClass('is-invalid');
                            $('.errordk_nama_ayah').html('');
                        }

                        if (response.error.dk_kelas) {
                            $('#dk_kelas').addClass('is-invalid');
                            $('.errordk_kelas').html(response.error.dk_kelas);
                        } else {
                            $('#dk_kelas').removeClass('is-invalid');
                            $('.errordk_kelas').html('');
                        }

                        if (response.error.dk_foto_identitas) {
                            $('#dk_foto_identitas').addClass('is-invalid');
                            $('.errordk_foto_identitas').html(response.error.dk_foto_identitas);
                        } else {
                            $('#dk_foto_identitas').removeClass('is-invalid');
                            $('.errordk_foto_identitas').html('');
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
                            $('#modaledit').modal('hide');
                            table.draw();
                        }
                        $('#modaledit').modal('hide');
                        table.draw();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
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
    });
</script>