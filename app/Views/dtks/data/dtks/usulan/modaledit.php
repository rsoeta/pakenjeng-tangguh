<?php
$user = session()->get('role_id');
$nik = session()->get('nik');
$jabatan = session()->get('jabatan');
$desa_id = session()->get('kode_desa');
?>


<!-- Modal -->
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="modaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaleditLabel">Form. Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <?php echo form_open('updateUsulan', ['class' => 'formsimpan']) ?>
                <?= csrf_field(); ?>
                <div class="row">
                    <div class="col-12 col-sm-6">

                        <div class="form-group row nopadding" hidden>
                            <label class="col-4 col-sm-4 col-form-label" for="id">ID</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="id" id="id" class="form-control form-control-sm" value="<?= $id; ?>">
                                <div class="invalid-feedback errorid"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="nokk">No. KK</label>
                            <div class="col-8 col-sm-8">
                                <input type="number" name="nokk" id="nokk" class="form-control form-control-sm" value="<?= $nokk; ?>" autocomplete="on">
                                <div class="invalid-feedback errornokk"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?php if ($user != 1) {
                                                                    echo 'hidden';
                                                                } ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="kelurahan">Desa/Kelurahan</label>
                            <div class="col-8 col-sm-8">
                                <select id="kelurahan" name="kelurahan" class="form-select form-select-sm">
                                    <option value="">-- Pilih Desa / Kelurahan --</option>
                                    <?php foreach ($desa as $row) { ?>
                                        <option <?php if ($desa_id == $row['id']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorkelurahan"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?php if ($user > 3) {
                                                                    echo 'hidden';
                                                                } ?>>
                            <label class="col-4 col-sm-4 col-form-label" for="datarw">No. RW</label>
                            <div class="col-8 col-sm-8">
                                <select id="datarw" name="datarw" class="form-select form-select-sm">
                                    <option value="">-- Pilih RW --</option>
                                    <?php foreach ($rw as $row) { ?>
                                        <option <?php if ($datarw == $row['no_rw']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['no_rw'] ?>"> <?php echo $row['no_rw']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordatarw"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="datart">No. RT</label>
                            <div class="col-8 col-sm-8">
                                <select id="datart" name="datart" class="form-select form-select-sm">
                                    <option value="">-- Pilih RT --</option>
                                    <?php foreach ($rt as $row) { ?>
                                        <option <?php if ($datart == $row['no_rt']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['no_rt'] ?>"> <?php echo $row['no_rt']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordatart"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="alamat">Alamat</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="<?= $alamat; ?>">
                                <div class="invalid-feedback erroralamat"></div>
                            </div>
                        </div>

                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="nama">Nama</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="nama" id="nama" class="form-control form-control-sm" value="<?= $nama; ?>">
                                <div class="invalid-feedback errornama"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="nik">NIK</label>
                            <div class="col-8 col-sm-8">
                                <input type="number" name="nik" id="nik" class="form-control form-control-sm" value="<?= $du_nik; ?>" autocomplete="off">
                                <div class="invalid-feedback errornik"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="jenis_kelamin">Jenis Kelamin</label>
                            <div class="col-8 col-sm-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="chk-Lk" name="jenis_kelamin" <?= $jenis_kelamin == '1' ? 'checked' : ''; ?> value="1" />
                                    <label for="chk-Lk" class="form-check-label"> Laki-Laki </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="chk-Pr" name="jenis_kelamin" <?= $jenis_kelamin == '2' ? 'checked' : ''; ?> value="2" />
                                    <label for="chk-Pr" class="form-check-label"> Perempuan </label>
                                </div>
                                <div class="invalid-feedback errorjenis_kelamin"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" id="status_hamil_div" style="display: none">
                            <label class="col-4 col-sm-4 col-form-label" for="status_hamil">Status Hamil</label>
                            <div class="col-8 col-sm-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="chk-YaHamil" name="status_hamil" <?= $status_hamil == 1 ? 'checked' : ''; ?> value="1" />
                                    <label for="chk-YaHamil" class="form-check-label"> Ya </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="chk-TidakHamil" name="status_hamil" <?= $status_hamil == 2 ? 'checked' : ''; ?> value="2" />
                                    <label for="chk-TidakHamil" class="form-check-label"> Tidak </label>
                                </div>
                                <div class="invalid-feedback errorstatus_hamil"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" id="tgl_hamil_div" style="display: none;">
                            <label class="col-4 col-sm-4 col-form-label" for="tgl_hamil">Tgl Mulai Hamil</label>
                            <div class="col-8 col-sm-8">
                                <input type="date" name="tgl_hamil" id="tgl_hamil" class="form-control form-control-sm" value="<?= $tgl_hamil; ?>">
                                <div class="invalid-feedback errortgl_hamil"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="tempat_lahir">Tempat Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control form-control-sm" value="<?= $tempat_lahir; ?>">
                                <div class="invalid-feedback errortempat_lahir"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="tanggal_lahir">Tgl Lahir</label>
                            <div class="col-8 col-sm-8">
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control form-control-sm" value="<?= $tanggal_lahir; ?>">
                                <div class="invalid-feedback errortanggal_lahir"></div>
                            </div>
                        </div>

                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="jenis_pekerjaan">Pekerjaan</label>
                            <div class="col-8 col-sm-8">
                                <select id="jenis_pekerjaan" name="jenis_pekerjaan" class="form-select form-select-sm">
                                    <option value="">-- Pilih Jenis Pekerjaan --</option>
                                    <?php foreach ($pekerjaan as $row) { ?>
                                        <option <?php if ($jenis_pekerjaan == $row['idPekerjaan']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['idPekerjaan'] ?>"> <?php echo $row['JenisPekerjaan']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorjenis_pekerjaan"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="status_kawin">Status</label>
                            <div class="col-8 col-sm-8">
                                <select id="status_kawin" name="status_kawin" class="form-select form-select-sm">
                                    <option value="">-- Pilih Status Perkawinan --</option>
                                    <?php foreach ($statusKawin as $row) { ?>
                                        <option <?php if ($status_kawin == $row['idStatus']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['idStatus'] ?>"> <?php echo $row['StatusKawin']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorstatus_kawin"></div>
                            </div>
                        </div>

                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="shdk">SHDK</label>
                            <div class="col-8 col-sm-8">
                                <select id="shdk" name="shdk" class="form-select form-select-sm">
                                    <option value="">-- Status Hubungan dalam Keluarga --</option>
                                    <?php foreach ($shdk as $row) { ?>
                                        <option <?php if ($stahub == $row['id']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['id']; ?>"><?= $row['jenis_shdk']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorShdk"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="databansos">Program</label>
                            <div class="col-8 col-sm-8">
                                <select id="databansos" name="databansos" class="form-select form-select-sm">
                                    <option value="">-- Pilih Program --</option>
                                    <?php foreach ($bansos as $row) { ?>
                                        <option <?php if ($databansos == $row['dbj_id']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['dbj_id'] ?>"> <?php echo $row['dbj_ket_bansos']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errordatabansos"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label class="col-4 col-sm-4 col-form-label" for="ibu_kandung">Ibu Kandung</label>
                            <div class="col-8 col-sm-8">
                                <input type="text" name="ibu_kandung" id="ibu_kandung" class="form-control form-control-sm" value="<?= $ibu_kandung; ?>">
                                <div class="invalid-feedback erroribu_kandung"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label for="disabil_status" class="col-4 col-form-label">Status Disabilitas</label>
                            <div class="col-8 col-sm-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="chk-Yes" name="disabil_status" <?= $disabil_status == 1 ? 'checked' : ''; ?> value="1" />
                                    <label for="chk-Yes" class="form-check-label"> Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="chk-No" name="disabil_status" <?= $disabil_status == 2 ? 'checked' : ''; ?> value="2" />
                                    <label for="chk-No" class="form-check-label"> Tidak</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" id="disabil_jenis_div" style="display: none">
                            <label for="disabil_jenis" class="col-4 col-form-label">Jenis Disabilitas</label>
                            <div class="col-8 col-sm-8">
                                <select id="disabil_jenis" name="disabil_jenis" class="form-select form-select-sm">
                                    <option value="">-- Pilih Jenis Disabilitas --</option>
                                    <?php foreach ($DisabilitasJenisModel as $row) { ?>
                                        <option <?= $disabil_jenis == $row['dj_id'] ? 'selected' : ''; ?> value="<?= $row['dj_id'] ?>"> <?php echo $row['dj_keterangan']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <button type="submit" class="btn btn-warning btn-block btnsimpan">Update</button>
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
                url: '<?= base_url('/updateUsulan') ?>',
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

                        if (response.error.shdk) {
                            $('#shdk').addClass('is-invalid');
                            $('.errorShdk').html(response.error.shdk);
                        } else {
                            $('#shdk').removeClass('is-invalid');
                            $('.errorShdk').html('');
                        }

                        if (response.error.nik) {
                            $('#nik').addClass('is-invalid');
                            $('.errornik').html(response.error.nik);
                        } else {
                            $('#nik').removeClass('is-invalid');
                            $('.errornik').html('');
                        }

                        if (response.error.nokk) {
                            $('#nokk').addClass('is-invalid');
                            $('.errornokk').html(response.error.nokk);
                        } else {
                            $('#nokk').removeClass('is-invalid');
                            $('.errornokk').html('');
                        }

                        if (response.error.nama) {
                            $('#nama').addClass('is-invalid');
                            $('.errornama').html(response.error.nama);
                        } else {
                            $('#nama').removeClass('is-invalid');
                            $('.errornama').html('');
                        }

                        if (response.error.tempat_lahir) {
                            $('#tempat_lahir').addClass('is-invalid');
                            $('.errortempat_lahir').html(response.error.tempat_lahir);
                        } else {
                            $('#tempat_lahir').removeClass('is-invalid');
                            $('.errortempat_lahir').html('');
                        }

                        if (response.error.tanggal_lahir) {
                            $('#tanggal_lahir').addClass('is-invalid');
                            $('.errortanggal_lahir').html(response.error.tanggal_lahir);
                        } else {
                            $('#tanggal_lahir').removeClass('is-invalid');
                            $('.errortanggal_lahir').html('');
                        }

                        if (response.error.jenis_kelamin) {
                            $('#jenis_kelamin').addClass('is-invalid');
                            $('.errorjenis_kelamin').html(response.error.jenis_kelamin);
                        } else {
                            $('#jenis_kelamin').removeClass('is-invalid');
                            $('.errorjenis_kelamin').html('');
                        }

                        if (response.error.jenis_pekerjaan) {
                            $('#jenis_pekerjaan').addClass('is-invalid');
                            $('.errorjenis_pekerjaan').html(response.error.jenis_pekerjaan);
                        } else {
                            $('#jenis_pekerjaan').removeClass('is-invalid');
                            $('.errorjenis_pekerjaan').html('');
                        }

                        if (response.error.status_kawin) {
                            $('#status_kawin').addClass('is-invalid');
                            $('.errorstatus_kawin').html(response.error.status_kawin);
                        } else {
                            $('#status_kawin').removeClass('is-invalid');
                            $('.errorstatus_kawin').html('');
                        }

                        if (response.error.kelurahan) {
                            $('#kelurahan').addClass('is-invalid');
                            $('.errorkelurahan').html(response.error.kelurahan);
                        } else {
                            $('#kelurahan').removeClass('is-invalid');
                            $('.errorkelurahan').html('');
                        }

                        if (response.error.datarw) {
                            $('#datarw').addClass('is-invalid');
                            $('.errordatarw').html(response.error.datarw);
                        } else {
                            $('#datarw').removeClass('is-invalid');
                            $('.errordatarw').html('');
                        }

                        if (response.error.datart) {
                            $('#datart').addClass('is-invalid');
                            $('.errordatart').html(response.error.datart);
                        } else {
                            $('#datart').removeClass('is-invalid');
                            $('.errordatart').html('');
                        }

                        if (response.error.alamat) {
                            $('#alamat').addClass('is-invalid');
                            $('.erroralamat').html(response.error.alamat);
                        } else {
                            $('#alamat').removeClass('is-invalid');
                            $('.erroralamat').html('');
                        }

                        if (response.error.ibu_kandung) {
                            $('#ibu_kandung').addClass('is-invalid');
                            $('.erroribu_kandung').html(response.error.ibu_kandung);
                        } else {
                            $('#ibu_kandung').removeClass('is-invalid');
                            $('.erroribu_kandung').html('');
                        }

                        if (response.error.databansos) {
                            $('#databansos').addClass('is-invalid');
                            $('.errordatabansos').html(response.error.databansos);
                        } else {
                            $('#databansos').removeClass('is-invalid');
                            $('.errordatabansos').html('');
                        }

                        if (response.error.disabil_status) {
                            $('#databansos').addClass('is-invalid');
                            $('.errordatabansos').html(response.error.databansos);
                        } else {
                            $('#databansos').removeClass('is-invalid');
                            $('.errordatabansos').html('');
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

                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        })

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

    $(function() {
        $("input[name='jenis_kelamin']").click(function() {
            if ($("#chk-Pr").is(":checked")) {
                $("#status_hamil_div").show();
            } else {
                $("#status_hamil_div").hide();
                $("#tgl_hamil_div").hide();
            }
        });

        $("input[name='status_hamil']").click(function() {
            if ($("#chk-YaHamil").is(":checked")) {
                // $("#tgl_hamil_div").show();
                $('#tgl_hamil_div').show().find(':input').attr('required', true);
            } else {
                $('#tgl_hamil_div').hide().find(':input').attr('required', false);
                // $("#tgl_hamil_div").hide();
            }
        });

        $("input[name='disabil_status']").click(function() {
            if ($("#chk-Yes").is(":checked")) {
                $("#disabil_jenis_div").show();
            } else {
                $("#disabil_jenis_div").hide();
            }
        });

    });
</script>