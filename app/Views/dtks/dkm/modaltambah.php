<style>
    .center {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<!-- Modal -->
<div class="modal fade" id="modaltambah" aria-labelledby="modaltambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaltambahLabel"><i class="fa fa-crosshairs mr-1"></i> <?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open_multipart('', ['class' => 'formupload']) ?>
            <?= csrf_field(); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-12 mt-2">
                        <div class="form-group row nopadding">
                            <label class="col-3 col-sm-2 col-form-label" for="dataCari">Cari Data</label>
                            <div class="col-9 col-sm-4">
                                <select id="dataCari" class="form-control form-control-sm select2" style="width: 100%;">
                                    <option value='0'>-- Select --</option>
                                </select>
                            </div>
                            <label class="col-3 col-sm-1 col-form-label" for="dd_nkk">No.KK</label>
                            <div class="col-9 col-sm-5">
                                <input type="text" name="dd_nkk" id="dd_nkk" class="form-control form-control-sm" value="">
                                <div class="invalid-feedback errordd_nkk"></div>
                            </div>
                            <label class="col-3 col-sm-2 col-form-label" for="dd_nama">Nama</label>
                            <div class="col-9 col-sm-4">
                                <input type="text" name="dd_nama" id="dd_nama" class="form-control form-control-sm" value="">
                                <div class="invalid-feedback errordd_nama"></div>
                            </div>
                            <label class="col-3 col-sm-1 col-form-label" for="dd_nik">NIK</label>
                            <div class="col-9 col-sm-5">
                                <input type="text" name="dd_nik" id="dd_nik" class="form-control form-control-sm" value="">
                                <div class="invalid-feedback errordd_nik"></div>
                            </div>
                            <div hidden>
                                <label class="col-3 col-sm-2 col-form-label" for="dd_desa">Desa</label>
                                <div class="col-9 col-sm-4">
                                    <input type="text" name="dd_desa" id="dd_desa" class="form-control form-control-sm" value="">
                                    <div class="invalid-feedback errordd_desa"></div>
                                </div>
                            </div>
                            <label class="col-3 col-sm-2 col-form-label" for="dd_alamat">Alamat</label>
                            <div class="col-9 col-sm-4">
                                <input type="text" name="dd_alamat" id="dd_alamat" class="form-control form-control-sm" value="">
                                <div class="invalid-feedback errordd_alamat"></div>
                            </div>
                            <label class="col-3 col-sm-1 col-form-label" for="dd_rt">No. RT</label>
                            <div class="col-3 col-sm-2">
                                <input type="number" name="dd_rt" id="dd_rt" class="form-control form-control-sm" value="">
                                <div class="invalid-feedback errordd_rt"></div>
                            </div>
                            <label class="col-3 col-sm-1 col-form-label" for="dd_rw">No. RW</label>
                            <div class="col-3 col-sm-2">
                                <input type="number" name="dd_rw" id="dd_rw" class="form-control form-control-sm" value="">
                                <div class="invalid-feedback errordd_rw"></div>
                            </div>
                            <label class="label-center mt-2">Kepemilikan Adminduk</label>
                            <div class="d-flex justify-content-center">
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_adminduk"><b>Adminduk</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_adminduk" id="dd_adminduk">
                                </div>
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_bpjs"><b>BPJS</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_bpjs" id="dd_bpjs">
                                </div>
                            </div>
                            <label class="label-center mt-2">Penerimaan Bantuan</label>
                            <div class="d-flex justify-content-center">
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_blt"><b>BLT</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_blt" id="dd_blt" value="1" <?= isset($dd_blt) ? 'checked="checked"' : ''; ?>>
                                </div>
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_blt_dd"><b>BLT DD</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_blt_dd" id="dd_blt_dd" value="1" <?= isset($dd_blt_dd) ? 'checked="checked"' : ''; ?>>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_bpnt"><b>BPNT</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_bpnt" id="dd_bpnt" value="1" <?= isset($dd_bpnt) ? 'checked="checked"' : ''; ?>>
                                </div>
                                <label class="col-sm-2 col-3 form-check-label mr-3" for="dd_pkh"><b>PKH</b></label>
                                <div class="form-check col-sm-1 col-1">
                                    <input type="checkbox" class="form-control form-check-input" name="dd_pkh" id="dd_pkh" value="1" <?= isset($dd_pkh) ? 'checked="checked"' : ''; ?>>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-12 mt-2">
                            <label class="label-center mt-2">Foto Dokumentasi</label>
                            <div class="form-group row nopadding">
                                <label class="col-3 col-sm-2 col-form-label mb-2" for="dd_foto_cpm">Foto CPM</label>
                                <div class="col-9 col-sm-4 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                                        </div>
                                        <input type="file" class="form-control" spellcheck="false" name="dd_foto_cpm" accept="image/*" capture required />
                                    </div>
                                </div>
                                <div class="invalid-feedback errordd_foto_cpm"></div>
                                <label class="col-3 col-sm-1 col-form-label mb-2" for="dd_foto_rumah_depan">Rmh.Dpn</label>
                                <div class="col-9 col-sm-5 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-home"></i></span>
                                        </div>
                                        <input type="file" class="form-control" spellcheck="false" name="dd_foto_rumah_depan" accept="image/*" capture required />
                                    </div>
                                </div>
                                <div class="invalid-feedback errordd_foto_rumah_depan"></div>
                                <label class="col-3 col-sm-2 col-form-label mb-2" for="dd_foto_rumah_belakang">Rmh.Blk</label>
                                <div class="col-9 col-sm-4 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-house-damage"></i></span>
                                        </div>
                                        <input type="file" class="form-control" spellcheck="false" name="dd_foto_rumah_belakang" accept="image/*" capture required />
                                    </div>
                                </div>
                                <div class="invalid-feedback errordd_foto_rumah_belakang"></div>
                                <label class="col-3 col-sm-1 col-form-label mb-2" for="dd_foto_kk">Ft.KK</label>
                                <div class="col-9 col-sm-5 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-chalkboard-teacher"></i></span>
                                        </div>
                                        <input type="file" class="form-control" spellcheck="false" name="dd_foto_kk" accept="image/*" capture required />
                                    </div>
                                </div>
                                <div class="invalid-feedback errordd_foto_kk"></div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-12 mt-2">
                            <label class="label-center mt-2">Titik Koordinat</label>
                            <div class="form-group row nopadding">
                                <div class="col-sm-2 col-2">
                                    <button type="button" class="btn btn-primary" onclick="getLocation()"><i class="fas fa-map-marker-alt"></i></button>
                                </div>
                                <div class="col-sm-5 col-5">
                                    <input type="text" class="form-control mb-2" placeholder="Latitude" spellcheck="false" id="latitude" name="dd_latitude" required>
                                    <div class="invalid-feedback errordd_latitude"></div>
                                </div>
                                <div class="col-sm-5 col-5">
                                    <input type="text" class="form-control mb-2" placeholder="Longitude" spellcheck="false" id="longitude" name="dd_longitude" required>
                                    <div class="invalid-feedback errordd_longitude"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <!-- <button type="submit" class="btn btn-secondary">Cancel</button> -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                    <button type="submit" class="btn btn-success btnSimpan float-right">Submit</button>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<script>
    $('#dataCari').on('change', (event) => {
        // console.log(event.target.value);
        getData(event.target.value).then(data => {
            $('#dd_nkk').val(data.nokk);
            $('#dd_nama').val(data.nama);
            $('#dd_nik').val(data.du_nik);
            $('#dd_alamat').val(data.alamat);
            $('#dd_rw').val(data.rw);
            $('#dd_rt').val(data.rt);
            $('#dd_desa').val(data.kelurahan);
        });
    });

    async function getData(id) {
        let response = await fetch('/api_usulan/' + id);
        let data = await response.json();

        return data;
    }

    $(document).ready(function() {
        $('.btnSimpan').click(function(e) {
            e.preventDefault();

            let form = $('.formupload')[0];

            let data = new FormData(form);

            $.ajax({
                type: "POST",
                url: "<?= site_url('/simpanDkm'); ?>",
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
                beforeSend: function(e) {
                    $('.btnSimpan').prop('disable', 'disabled');
                    $('.btnSimpan').html('<i class="fa fa-spin fa-spinner"></i>')
                },
                complete: function(e) {
                    $('.btnSimpan').removeAttr('disabled');
                    $('.btnSimpan').html('Submit');
                },
                success: function(response) {
                    if (response.error) {

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

                        if (response.error.dd_nik) {
                            $('#dd_nik').addClass('is-invalid');
                            $('.errordd_nik').html(response.error.dd_nik);
                        } else {
                            $('#dd_nik').removeClass('is-invalid');
                            $('.errordd_nik').html('');
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
                        if (response.error.dd_adminduk_foto) {
                            $('#dd_adminduk_foto').addClass('is-invalid');
                            $('.errordd_adminduk_foto').html(response.error.dd_adminduk_foto);
                        } else {
                            $('#dd_adminduk_foto').removeClass('is-invalid');
                            $('.errordd_adminduk_foto').html('');
                        }
                        if (response.error.dd_bpjs_foto) {
                            $('#dd_bpjs_foto').addClass('is-invalid');
                            $('.errordd_bpjs_foto').html(response.error.dd_bpjs_foto);
                        } else {
                            $('#dd_bpjs_foto').removeClass('is-invalid');
                            $('.errordd_bpjs_foto').html('');
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

                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Sukses!',
                                text: response.sukses,
                                showConfirmButton: false,
                                timer: 2000
                            })

                            $('#modaltambah').modal('hide');
                            // $('#tabel_data').DataTable().ajax.reload();
                            table.draw();

                        }
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });

        $('#rw').change(function() {
            var desa = $('#datadesa').val();
            var no_rw = $('#rw').val();
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
                        $('#rt').html(html);
                    }
                });
            } else {
                $('#rt').val('');
            }
        });

        $('#dataCari').select2({
            dropdownParent: $('#modaltambah'),
            ajax: {
                url: "<?php echo base_url('get_data_penduduk'); ?>",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                    };
                },
                processResults: function(response) {
                    return {
                        results: response.data
                    };
                },
                cache: true
            }
        });
    });

    var x = document.getElementById("latitude");
    var y = document.getElementById("longitude");
    var z = document.getElementById("z");

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