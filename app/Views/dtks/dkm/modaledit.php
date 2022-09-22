<!-- Modal -->
<style>
    .center {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<!-- Modal -->
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="modaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaleditLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= form_open_multipart('', ['class' => 'formupload']) ?>
                <?= csrf_field(); ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 col-12 col-sm-6">
                            <div class="center">
                                <input type="text" name="dd_id" id="dd_id" class="form-control form-control-sm" value="<?= $dd_id; ?>" style="display: none;">
                                <div class="col-3 col-sm-3">
                                    <a href="<?= dkm_foto_cpm('DKM_FP' . $dd_nik . '.jpg', 'foto-cpm') ?>" data-lightbox="dataCpm">
                                        <img src="<?= dkm_foto_cpm('DKM_FP' . $dd_nik . '.jpg', 'foto-cpm') ?>" style="width: 80px; border-radius: 5px;">
                                    </a>
                                    <figcaption><span>Foto Calon PM</span></figcaption>
                                </div>
                                <div class="col-3 col-sm-3">
                                    <a href="<?= dkm_foto_cpm('DKM_FH' . $dd_nik . '.jpg', 'foto-rumah-depan') ?>" data-lightbox="dataCpm">
                                        <img src="<?= dkm_foto_cpm('DKM_FH' . $dd_nik . '.jpg', 'foto-rumah-depan') ?>" style="width: 80px; border-radius: 5px;">
                                    </a>
                                    <figcaption><span>Foto Rumah Depan</span></figcaption>
                                </div>
                                <div class="col-3 col-sm-3">
                                    <a href="<?= dkm_foto_cpm('DKM_BH' . $dd_nik . '.jpg', 'foto-rumah-belakang') ?>" data-lightbox="dataCpm">
                                        <img src="<?= dkm_foto_cpm('DKM_BH' . $dd_nik . '.jpg', 'foto-rumah-belakang') ?>" style="width: 80px; border-radius: 5px;">
                                    </a>
                                    <figcaption><span>Foto Rumah Belakang</span></figcaption>
                                </div>
                                <div class="col-3 col-sm-3">
                                    <a href="<?= dkm_foto_cpm('DKM_KK' . $dd_nik . '.jpg', 'foto-kk') ?>" data-lightbox="dataCpm">
                                        <img src="<?= dkm_foto_cpm('DKM_KK' . $dd_nik . '.jpg', 'foto-kk') ?>" style="width: 80px; border-radius: 5px;">
                                    </a>
                                    <figcaption><span>Foto Kartu Keluarga</span></figcaption>
                                </div>
                            </div>
                        </div>
                        <!-- Data Identitas -->
                        <div class="col-md-6 col-12 col-sm-6">
                            <div class="form-group">
                                <div class="form-group row nopadding" hidden>
                                    <label class="col-4 col-sm-4 col-form-label" for="dd_id">ID</label>
                                    <div class="col-8 col-sm-8">
                                        <input type="text" name="dd_id" id="dd_id" class="form-control form-control-sm" value="<?= set_value('dd_id', $dd_id); ?>">
                                    </div>
                                </div>
                                <div class="form-group row nopadding">
                                    <label class="col-4 col-sm-4 col-form-label" for="dd_nkk">No. KK</label>
                                    <div class="col-8 col-sm-8">
                                        <input type="text" name="dd_nkk" id="dd_nkk" class="form-control form-control-sm" value="<?= set_value('dd_nkk', $dd_nkk); ?>">
                                        <div class="invalid-feedback errordd_nkk"></div>
                                    </div>
                                </div>
                                <div class="form-group row nopadding">
                                    <label class="col-4 col-sm-4 col-form-label" for="dd_nama">Nama</label>
                                    <div class="col-8 col-sm-8">
                                        <input type="text" name="dd_nama" id="dd_nama" class="form-control form-control-sm" value="<?= set_value('dd_nama', $dd_nama); ?>">
                                        <div class="invalid-feedback errordd_nama"></div>
                                    </div>
                                </div>
                                <div class="form-group row nopadding">
                                    <label class="col-4 col-sm-4 col-form-label" for="dd_nik">NIK</label>
                                    <div class="col-8 col-sm-8">
                                        <input type="text" name="dd_nik" id="dd_nik" class="form-control form-control-sm" value="<?= set_value('dd_nik', $dd_nik); ?>">
                                        <div class="invalid-feedback errordd_nik"></div>
                                    </div>
                                </div>

                                <div class="form-group row nopadding">
                                    <label class="col-4 col-sm-4 col-form-label" for="dd_alamat">Alamat</label>
                                    <div class="col-8 col-sm-8">
                                        <input type="text" name="dd_alamat" id="dd_alamat" class="form-control form-control-sm" value="<?= set_value('dd_alamat', $dd_alamat); ?>">
                                        <div class="invalid-feedback errordd_alamat"></div>
                                    </div>
                                </div>

                                <div class="form-group row nopadding">
                                    <label class="col-4 col-sm-4 col-form-label" for="dd_rt">No. RT</label>
                                    <div class="col-8 col-sm-8">
                                        <select id="dd_rt" name="dd_rt" class="form-select form-select-sm">
                                            <option value="">-- Pilih RT --</option>
                                            <?php foreach ($datart as $row) { ?>
                                                <option <?php if ($dd_rt == $row['no_rt']) {
                                                            echo 'selected';
                                                        } ?> value="<?= $row['no_rt'] ?>"> <?php echo $row['no_rt']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="invalid-feedback errordd_rt"></div>
                                    </div>
                                </div>
                                <div class="form-group row nopadding">
                                    <label class="col-4 col-sm-4 col-form-label" for="dd_rw">No. RW</label>
                                    <div class="col-8 col-sm-8">
                                        <select id="dd_rw" name="dd_rw" class="form-select form-select-sm">
                                            <option value="">-- Pilih RW --</option>
                                            <?php foreach ($datarw as $row) { ?>
                                                <option <?php if ($dd_rw == $row['no_rw']) {
                                                            echo 'selected';
                                                        } ?> value="<?= $row['no_rw'] ?>"> <?php echo $row['no_rw']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="invalid-feedback errordd_rw"></div>
                                    </div>
                                </div>

                                <div class="form-group row nopadding">
                                    <label class="col-4 col-sm-4 col-form-label" for="dd_desa">Desa/Kelurahan</label>
                                    <div class="col-8 col-sm-8">
                                        <select id="dd_desa" name="dd_desa" class="form-select form-select-sm">
                                            <option value="">-- Pilih --</option>
                                            <?php foreach ($datadesa as $row) { ?>
                                                <option <?php if ($dd_desa == $row['id']) {
                                                            echo 'selected';
                                                        } ?> value="<?= $row['id'] ?>"> <?php echo $row['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="invalid-feedback errordd_desa"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Data Adminduk -->
                        <div class="col-md-6 col-12 col-sm-6">
                            <label class="label-center mt-2">Kepemilikan Adminduk</label>
                            <div class="d-flex justify-content-center">
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_adminduk"><b>Adminduk</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_adminduk" id="dd_adminduk" value="<?= $dd_adminduk == 1 ? set_checkbox('dd_adminduk', $dd_adminduk) : ''; ?>" <?= isset($dd_adminduk) ? 'checked="checked"' : ''; ?>>
                                </div>
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_bpjs"><b>BPJS</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_bpjs" id="dd_bpjs" value="<?= $dd_bpjs == 1 ? set_checkbox('dd_bpjs', $dd_bpjs) : ''; ?>" <?= isset($dd_bpjs) ? 'checked="checked"' : ''; ?>>
                                </div>
                            </div>
                            <!-- Bantuan -->
                            <label class="label-center mt-2">Penerimaan Bantuan</label>
                            <div class="d-flex justify-content-center">
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_blt"><b>BLT</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_blt" id="dd_blt" value="<?= $dd_blt == 1 ? set_checkbox('dd_blt', $dd_blt) : ''; ?>" <?= isset($dd_blt) ? 'checked="checked"' : ''; ?>>
                                </div>
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_blt_dd"><b>BLT DD</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_blt_dd" id="dd_blt_dd" value="<?= $dd_blt_dd == 1 ? set_checkbox('dd_blt_dd', $dd_blt_dd) : ''; ?>" <?= isset($dd_blt_dd) ? 'checked="checked"' : ''; ?>>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_bpnt"><b>BPNT</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_bpnt" id="dd_bpnt" value="<?= $dd_bpnt == 1 ? set_checkbox('dd_bpnt', $dd_bpnt) : ''; ?>" <?= isset($dd_bpnt) ? 'checked="checked"' : ''; ?>>
                                </div>
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_pkh"><b>PKH</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_pkh" id="dd_pkh" value="<?= $dd_bpnt == 1 ? set_checkbox('dd_pkh', $dd_pkh) : ''; ?>" <?= isset($dd_pkh) ? 'checked="checked"' : ''; ?>>
                                </div>
                            </div>
                        </div>
                        <!-- Dokumentasi -->
                        <div class="col-md-6 col-12 col-sm-6">
                            <label class="label-center mt-2">Foto Dokumentasi</label>
                            <div class="form-group row nopadding">
                                <label class="col-3 col-sm-2 col-form-label mb-2" for="dd_foto_cpm">Foto CPM</label>
                                <div class="col-9 col-sm-4 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                                        </div>
                                        <input type="file" class="form-control form-control-sm" spellcheck="false" name="dd_foto_cpm" value="" accept="image/*" capture required />
                                    </div>
                                </div>
                                <div class="invalid-feedback errordd_foto_cpm"></div>
                                <label class="col-3 col-sm-1 col-form-label mb-2" for="dd_foto_rumah_depan">Rmh.Dpn</label>
                                <div class="col-9 col-sm-5 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-home"></i></span>
                                        </div>
                                        <input type="file" class="form-control form-control-sm" spellcheck="false" name="dd_foto_rumah_depan" value="" accept="image/*" capture required />
                                    </div>
                                </div>
                                <div class="invalid-feedback errordd_foto_rumah_depan"></div>
                                <label class="col-3 col-sm-2 col-form-label mb-2" for="dd_foto_rumah_belakang">Rmh.Blk</label>
                                <div class="col-9 col-sm-4 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-house-damage"></i></span>
                                        </div>
                                        <input type="file" class="form-control form-control-sm" spellcheck="false" name="dd_foto_rumah_belakang" value="" accept="image/*" capture required />
                                    </div>
                                </div>
                                <div class="invalid-feedback errordd_foto_rumah_belakang"></div>
                                <label class="col-3 col-sm-1 col-form-label mb-2" for="dd_foto_kk">Ft.KK</label>
                                <div class="col-9 col-sm-5 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-chalkboard-teacher"></i></span>
                                        </div>
                                        <input type="file" class="form-control form-control-sm" spellcheck="false" name="dd_foto_kk" value="" accept="image/*" capture required />
                                    </div>
                                </div>
                                <div class="invalid-feedback errordd_foto_kk"></div>
                            </div>
                        </div>
                        <!-- Koordinat -->
                        <div class="col-md-6 col-12 col-sm-6">
                            <label class="label-center mt-2">Titik Koordinat</label>
                            <div class="form-group row nopadding">
                                <div class="col-sm-2 col-2">
                                    <button type="button" class="btn btn-primary" onclick="getLocation()"><i class="fas fa-map-marker-alt"></i></button>
                                </div>
                                <div class="col-sm-5 col-5">
                                    <input type="text" class="form-control form-control-sm mb-2" placeholder="Latitude" spellcheck="false" id="latitude" name="dd_latitude" value="<?= set_value('dd_latitude', $dd_latitude); ?>" required>
                                    <div class="invalid-feedback errordd_latitude"></div>
                                </div>
                                <div class="col-sm-5 col-5">
                                    <input type="text" class="form-control form-control-sm mb-2" placeholder="Longitude" spellcheck="false" id="longitude" name="dd_longitude" value="<?= set_value('dd_longitude', $dd_longitude); ?>" required>
                                    <div class="invalid-feedback errordd_longitude"></div>
                                </div>
                            </div>
                        </div>
                        <!-- show image -->
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                            <button type="submit" class="btn btn-success btnUpdate float-right">Update</button>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.btnUpdate').click(function(e) {
            e.preventDefault();
            let form = $('.formupload')[0];
            let data = new FormData(form);
            $.ajax({
                type: "POST",
                url: '<?= site_url('/updateDkm') ?>',
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
                beforeSend: function() {
                    $('.btnUpdate').attr('disable', 'disabled');
                    $('.btnUpdate').html('<i class="fa fa-spin fa-spinner"></i>');
                },
                complete: function() {
                    $('.btnUpdate').removeAttr('disable');
                    $('.btnUpdate').html('Update');
                },
                success: function(response) {
                    if (response.error) {
                        if (response.error.dd_nik) {
                            $('#dd_nik').addClass('is-invalid');
                            $('.errordd_nik').html(response.error.dd_nik);
                        } else {
                            $('#dd_nik').removeClass('is-invalid');
                            $('.errordd_nik').html('');
                        }

                        if (response.error.dd_nkk) {
                            $('#dd_nkk').addClass('is-invalid');
                            $('.errordd_nkk').html(response.error.dd_nkk);
                        } else {
                            $('#dd_nkk').removeClass('is-invalid');
                            $('.errordd_nkk').html('');
                        }

                        if (response.error.dd_nama) {
                            $('#dd_nama').addClass('is-invalid');
                            $('.errordd_nama').html(response.error.dd_nama);
                        } else {
                            $('#dd_nama').removeClass('is-invalid');
                            $('.errordd_nama').html('');
                        }

                        if (response.error.dd_alamat) {
                            $('#dd_alamat').addClass('is-invalid');
                            $('.errordd_alamat').html(response.error.dd_alamat);
                        } else {
                            $('#dd_alamat').removeClass('is-invalid');
                            $('.errordd_alamat').html('');
                        }

                        if (response.error.dd_rt) {
                            $('#dd_rt').addClass('is-invalid');
                            $('.errordd_rt').html(response.error.dd_rt);
                        } else {
                            $('#dd_rt').removeClass('is-invalid');
                            $('.errordd_rt').html('');
                        }

                        if (response.error.dd_rw) {
                            $('#dd_rw').addClass('is-invalid');
                            $('.errordd_rw').html(response.error.dd_rw);
                        } else {
                            $('#dd_rw').removeClass('is-invalid');
                            $('.errordd_rw').html('');
                        }

                        if (response.error.dd_desa) {
                            $('#dd_desa').addClass('is-invalid');
                            $('.errordd_desa').html(response.error.dd_desa);
                        } else {
                            $('#dd_desa').removeClass('is-invalid');
                            $('.errordd_desa').html('');
                        }

                        if (response.error.dd_status) {
                            $('#dd_status').addClass('is-invalid');
                            $('.errordd_status').html(response.error.dd_status);
                        } else {
                            $('#dd_status').removeClass('is-invalid');
                            $('.errordd_status').html('');
                        }

                        if (response.error.dd_adminduk) {
                            $('#dd_adminduk').addClass('is-invalid');
                            $('.errordd_adminduk').html(response.error.dd_adminduk);
                        } else {
                            $('#dd_adminduk').removeClass('is-invalid');
                            $('.errordd_adminduk').html('');
                        }

                        if (response.error.dd_adminduk_foto) {
                            $('#dd_adminduk_foto').addClass('is-invalid');
                            $('.errordd_adminduk_foto').html(response.error.dd_adminduk_foto);
                        } else {
                            $('#dd_adminduk_foto').removeClass('is-invalid');
                            $('.errordd_adminduk_foto').html('');
                        }

                        if (response.error.dd_bpjs) {
                            $('#dd_bpjs').addClass('is-invalid');
                            $('.errordd_bpjs').html(response.error.dd_bpjs);
                        } else {
                            $('#dd_bpjs').removeClass('is-invalid');
                            $('.errordd_bpjs').html('');
                        }

                        if (response.error.dd_bpjs_foto) {
                            $('#dd_bpjs_foto').addClass('is-invalid');
                            $('.errordd_bpjs_foto').html(response.error.dd_bpjs_foto);
                        } else {
                            $('#dd_bpjs_foto').removeClass('is-invalid');
                            $('.errordd_bpjs_foto').html('');
                        }

                        if (response.error.dd_blt) {
                            $('#dd_blt').addClass('is-invalid');
                            $('.errordd_blt').html(response.error.dd_blt);
                        } else {
                            $('#dd_blt').removeClass('is-invalid');
                            $('.errordd_blt').html('');
                        }

                        if (response.error.dd_blt_dd) {
                            $('#dd_blt_dd').addClass('is-invalid');
                            $('.errordd_blt_dd').html(response.error.dd_blt_dd);
                        } else {
                            $('#dd_blt_dd').removeClass('is-invalid');
                            $('.errordd_blt_dd').html('');
                        }

                        if (response.error.dd_bpnt) {
                            $('#dd_bpnt').addClass('is-invalid');
                            $('.errordd_bpnt').html(response.error.dd_bpnt);
                        } else {
                            $('#dd_bpnt').removeClass('is-invalid');
                            $('.errordd_bpnt').html('');
                        }

                        if (response.error.dd_pkh) {
                            $('#dd_pkh').addClass('is-invalid');
                            $('.errordd_pkh').html(response.error.dd_pkh);
                        } else {
                            $('#dd_pkh').removeClass('is-invalid');
                            $('.errordd_pkh').html('');
                        }

                        if (response.error.dd_foto_cpm) {
                            $('#dd_foto_cpm').addClass('is-invalid');
                            $('.errordd_foto_cpm').html(response.error.dd_foto_cpm);
                        } else {
                            $('#dd_foto_cpm').removeClass('is-invalid');
                            $('.errordd_foto_cpm').html('');
                        }

                        if (response.error.dd_foto_rumah_depan) {
                            $('#dd_foto_rumah_depan').addClass('is-invalid');
                            $('.errordd_foto_rumah_depan').html(response.error.dd_foto_rumah_depan);
                        } else {
                            $('#dd_foto_rumah_depan').removeClass('is-invalid');
                            $('.errordd_foto_rumah_depan').html('');
                        }

                        if (response.error.dd_foto_rumah_belakang) {
                            $('#dd_foto_rumah_belakang').addClass('is-invalid');
                            $('.errordd_foto_rumah_belakang').html(response.error.dd_foto_rumah_belakang);
                        } else {
                            $('#dd_foto_rumah_belakang').removeClass('is-invalid');
                            $('.errordd_foto_rumah_belakang').html('');
                        }

                        if (response.error.dd_foto_kk) {
                            $('#dd_foto_kk').addClass('is-invalid');
                            $('.errordd_foto_kk').html(response.error.dd_foto_kk);
                        } else {
                            $('#dd_foto_kk').removeClass('is-invalid');
                            $('.errordd_foto_kk').html('');
                        }

                        if (response.error.dd_latitude) {
                            $('#dd_latitude').addClass('is-invalid');
                            $('.errordd_latitude').html(response.error.dd_latitude);
                        } else {
                            $('#dd_latitude').removeClass('is-invalid');
                            $('.errordd_latitude').html('');
                        }

                        if (response.error.dd_longitude) {
                            $('#dd_longitude').addClass('is-invalid');
                            $('.errordd_longitude').html(response.error.dd_longitude);
                        } else {
                            $('#dd_longitude').removeClass('is-invalid');
                            $('.errordd_longitude').html('');
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
                            location.reload(true);

                        }

                        $('#modaledit').modal('hide');

                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        })
    });

    let x = document.getElementById("latitude");
    let y = document.getElementById("longitude");
    let z = document.getElementById("z");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            // z.innerHTML = "Geolokasi Tidak Didukung oleh Browser Ini";
            alert("Geolokasi Tidak Didukung oleh Browser Ini");
        }
    }

    function showPosition(position) {
        $("#latitude").val(`${position.coords.latitude}`);
        $("#longitude").val(`${position.coords.longitude}`);
        // x.innerHTML = position.coords.latitude;
        // y.innerHTML = position.coords.longitude;
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert("Pengguna menolak permintaan geolokasi.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Informasi lokasi tidak tersedia.");
                break;
            case error.TIMEOUT:
                alert("Permintaan untuk menghitung waktu lokasi pengguna.");
                break;
            case error.UNKNOWN_ERROR:
                alert("Terjadi kesalahan yang tidak diketahui.");
                break;
        }
    }
</script>