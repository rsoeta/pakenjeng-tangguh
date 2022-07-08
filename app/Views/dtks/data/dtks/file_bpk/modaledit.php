<!-- Modal -->
<div class="modal fade" id="modalEdit" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel"><i class="fa fa-crosshairs"></i> <?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php
            $role = session()->get('role_id');
            $kode_desa = session()->get('kode_desa');
            ?>
            <?= form_open_multipart('', ['class' => 'formupload']) ?>
            <?= csrf_field(); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group row nopadding">
                            <label for="" class="col-5 col-sm-2 col-form-label">Indikasi Temuan</label>
                            <div class="col-7 col-sm-4">
                                <select name="" id="" disabled>
                                    <?php foreach ($indikasiTemuan as $row) { ?>
                                        <option class="form-control form-control-sm" <?= ($row['tkt_num'] == $vg_source) ? 'selected' : ''; ?> value="<?= $row['tkt_num']; ?>"><?= $row['tkt_ket']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label for="" class="col-5 col-sm-1 col-form-label">Bansos</label>
                            <div class="col-7 col-sm-5">
                                <input type="text" class="form-control form-control-sm" value="<?= $jenisBansosSatu; ?> <?= $jenisBansosDua; ?>" disabled>
                            </div>
                        </div>
                        <!-- <input type="file" name="foto" id="foto" class="form-control form-control-sm" hidden> -->
                        <div class="form-group row nopadding">
                            <input type="hidden" name="vg_id" id="vg_id" class="form-control form-control-sm" value="<?= set_value('vg_id', $vg_id); ?>">
                            <label class="col-5 col-sm-2 col-form-label" for="vg_nik">NIK</label>
                            <div class="col-7 col-sm-4">
                                <input type="text" name="vg_nik" id="vg_nik" class="form-control form-control-sm" value="<?= set_value('vg_nik', $vg_nik); ?>" readonly>
                                <div class="invalid-feedback errornik"></div>
                            </div>
                            <label class="col-5 col-sm-1 col-form-label" for="vg_nama_lengkap">Nama</label>
                            <div class="col-7 col-sm-5">
                                <input type="text" name="vg_nama_lengkap" id="vg_nama_lengkap" class="form-control form-control-sm" value="<?= set_value('vg_nama_lengkap', $vg_nama_lengkap); ?>" readonly>
                                <div class="invalid-feedback errorvg_nama_lengkap"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-sm-12 col-12 mt-2">
                        <div class="form-group row nopadding">
                            <label class="col-3 col-sm-2 col-form-label" for="vg_nama_ktp">Nama KTP</label>
                            <div class="col-9 col-sm-4">
                                <input type="text" name="vg_nama_ktp" id="vg_nama_ktp" class="form-control form-control-sm" value="<?= set_value('vg_nama_ktp', $vg_nama_ktp); ?>">
                                <div class="invalid-feedback errorvg_nama_ktp"></div>
                            </div>
                            <label class="col-3 col-sm-2 col-form-label" for="vg_nik_ktp">NIK KTP</label>
                            <div class="col-9 col-sm-4">
                                <input type="text" name="vg_nik_ktp" id="vg_nik_ktp" class="form-control form-control-sm" value="<?= set_value('vg_nik_ktp', $vg_nik_ktp); ?>">
                                <div class="invalid-feedback errorvg_nik_ktp"></div>
                            </div>
                            <label class="col-3 col-sm-2 col-form-label" for="vg_alamat">Alamat</label>
                            <div class="col-9 col-sm-4">
                                <input type="text" name="vg_alamat" id="vg_alamat" class="form-control form-control-sm" value="<?= set_value('vg_alamat', $vg_alamat); ?>">
                                <div class="invalid-feedback errorvg_alamat"></div>
                            </div>
                            <label class="col-3 col-sm-1 col-form-label" for="vg_rt">No. RT</label>
                            <div class="col-3 col-sm-2">
                                <input type="number" name="vg_rt" id="vg_rt" class="form-control form-control-sm" value="<?= set_value('vg_rt', $vg_rt); ?>">
                                <div class="invalid-feedback errorvg_rt"></div>
                            </div>
                            <label class="col-3 col-sm-1 col-form-label" for="vg_rw">No. RW</label>
                            <div class="col-3 col-sm-2">
                                <input type="number" name="vg_rw" id="vg_rw" class="form-control form-control-sm" value="<?= set_value('vg_rw', $vg_rw); ?>">
                                <div class="invalid-feedback errorvg_rw"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding">
                            <label for="vg_ds_id" class="col-3 col-sm-2 col-form-label">Status PM</label>
                            <div class="col-9 col-sm-4">
                                <select id="vg_ds_id" name="vg_ds_id" class="form-select form-select-sm">
                                    <?php foreach ($statusDtks as $row) { ?>
                                        <option <?= ($row['id_status'] == $vg_ds_id) ? 'selected' : ''; ?> value="<?= $row['id_status']; ?>"> <?php echo $row['jenis_status']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorvg_ds_id"></div>
                            </div>
                            <label for="vg_status" class="col-3 col-sm-1 col-form-label">Verivali</label>
                            <div class="col-9 col-sm-5">
                                <select id="vg_status" name="vg_status" class="form-select form-select-sm">
                                    <?php foreach ($status as $row) { ?>
                                        <option <?= ($row['sta_id'] == $vg_sta_id) ? 'selected' : ''; ?> value="<?= $row['sta_id']; ?>"> <?= $row['sta_nama']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorvg_status"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-12 mt-2">
                        <label class="label-center mt-2">Foto Dokumentasi</label>
                        <div class="form-group row nopadding">
                            <label class="col-3 col-sm-2 col-form-label mb-2" for="image_fp">PM</label>
                            <div class="col-9 col-sm-4 mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>
                                    <input type="file" class="form-control" spellcheck="false" value="<?= set_value('image_fp', $vg_fp); ?>" name="image_fp" accept="image/*" capture required />
                                </div>
                            </div>
                            <div class="invalid-feedback errorimage_fp"></div>
                            <label class="col-3 col-sm-1 col-form-label mb-2" for="image_fr">Rumah</label>
                            <div class="col-9 col-sm-5 mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-home"></i></span>
                                    </div>
                                    <input type="file" class="form-control" spellcheck="false" value="<?= set_value('image_fr', $vg_fr); ?>" name="image_fr" accept="image/*" capture required />
                                </div>
                            </div>
                            <div class="invalid-feedback errorimage_fr"></div>
                            <label class="col-3 col-sm-2 col-form-label mb-2" for="vg_fktp">KTP</label>
                            <div class="col-9 col-sm-4 mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-home"></i></span>
                                    </div>
                                    <input type="file" class="form-control" spellcheck="false" value="<?= set_value('vg_fktp', $vg_fr); ?>" name="vg_fktp" accept="image/*" capture required />
                                </div>
                            </div>
                            <div class="invalid-feedback errorvg_fktp"></div>
                            <label class="col-3 col-sm-1 col-form-label mb-2" for="vg_fkk">KK</label>
                            <div class="col-9 col-sm-5 mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-home"></i></span>
                                    </div>
                                    <input type="file" class="form-control" spellcheck="false" value="<?= set_value('vg_fkk', $vg_fr); ?>" name="vg_fkk" accept="image/*" capture required />
                                </div>
                            </div>
                            <div class="invalid-feedback errorvg_fkk"></div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-12 mt-2">
                        <div class="form-group row nopadding">
                            <div class="col-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-control form-check-input" name="vg_terbukti" id="exampleCheck1" value="1" <?= isset($vg_terbukti) ? 'checked="checked"' : ''; ?>>
                                    <label class="form-check-label" for="exampleCheck1"><b>Terbukti</b></label>
                                </div>
                            </div>
                            <div class="col-9">
                                <label for="vg_alasan">| Alasan</label>
                                <textarea name="vg_alasan" cols="10" rows="2" class="form-control" id="vg_alasan" placeholder="Tuliskan sedikitnya keterangan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-12 mt-2">
                        <label class="label-center mt-2">Titik Koordinat</label>
                        <div class="form-group row nopadding">
                            <div class="col-sm-2 col-12"></div>
                            <div class="col-sm-5 col-6">
                                <input type="text" class="form-control mb-2" placeholder="Latitude" value="<?= set_value('vg_lat', $vg_lat); ?>" spellcheck="false" id="latitude" name="vg_lat" required>
                                <div class="invalid-feedback errorivg_lat"></div>
                            </div>
                            <div class="col-sm-5 col-6">
                                <input type="text" class="form-control mb-2" placeholder="Longitude" value="<?= set_value('vg_lang', $vg_lang); ?>" spellcheck="false" id="longitude" name="vg_lang" required>
                                <div class="invalid-feedback errorivg_lang"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-primary" onclick="getLocation()">Get Coordinate</button>
                <button type="submit" class="btn btn-success btnSimpan float-right">Submit</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.btnSimpan').click(function(e) {
            e.preventDefault();

            let form = $('.formupload')[0];

            let data = new FormData(form);

            $.ajax({
                type: "post",
                url: "<?= site_url('updateGeo'); ?>",
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


                        if (response.error.vg_rw) {
                            $('#vg_rw').addClass('is-invalid');
                            $('.errorvg_rw').html(response.error.vg_rw);
                        } else {
                            $('#vg_rw').removeClass('is-invalid');
                            $('.errorvg_rw').html('');
                        }

                        if (response.error.vg_rt) {
                            $('#vg_rt').addClass('is-invalid');
                            $('.errorvg_rt').html(response.error.vg_rt);
                        } else {
                            $('#vg_rt').removeClass('is-invalid');
                            $('.errorvg_rt').html('');
                        }

                        if (response.error.vg_alamat) {
                            $('#vg_alamat').addClass('is-invalid');
                            $('.errorvg_alamat').html(response.error.vg_alamat);
                        } else {
                            $('#vg_alamat').removeClass('is-invalid');
                            $('.errorvg_alamat').html('');
                        }
                        if (response.error.image_fp) {
                            $('#image_fp').addClass('is-invalid');
                            $('.errorimage_fp').html(response.error.image_fp);
                        } else {
                            $('#image_fp').removeClass('is-invalid');
                            $('.errorimage_fp').html('');
                        }
                        if (response.error.image_fr) {
                            $('#image_fr').addClass('is-invalid');
                            $('.errorimage_fr').html(response.error.image_fr);
                        } else {
                            $('#image_fr').removeClass('is-invalid');
                            $('.errorimage_fr').html('');
                        }
                        if (response.error.vg_lat) {
                            $('#vg_lat').addClass('is-invalid');
                            $('.errorvg_lat').html(response.error.vg_lat);
                        } else {
                            $('#vg_lat').removeClass('is-invalid');
                            $('.errorvg_lat').html('');
                        }
                        if (response.error.vg_lang) {
                            $('#vg_lang').addClass('is-invalid');
                            $('.errorvg_lang').html(response.error.vg_lang);
                        } else {
                            $('#vg_lang').removeClass('is-invalid');
                            $('.errorvg_lang').html('');
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

                            $('#modalEdit').modal('hide');
                            // $('#tabel_data').DataTable().ajax.reload();
                            table.draw();
                            table2.draw();

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

    });

    var x = document.getElementById("latitude");
    var y = document.getElementById("longitude");
    var z = document.getElementById("z");


    // var cameras = new Array(); //create empty array to later insert available devices
    // navigator.mediaDevices.enumerateDevices() // get the available devices found in the machine
    //     .then(function(devices) {
    //         devices.forEach(function(device) {
    //             var i = 0;
    //             if (device.kind === "videoinput") { //filter video devices only
    //                 cameras[i] = device.deviceId; // save the camera id's in the camera array
    //                 i++;
    //             }
    //         });
    //     })

    // Webcam.set('constraints', { //set the constraints and initialize camera device (0 or 1 for back and front - varies which is which depending on device)
    //     width: 1920,
    //     height: 1080,
    //     sourceId: cameras[1],
    //     deviceId: {
    //         exact: deviceId
    //     }
    // });

    // var constraints = {
    //     audio: true,
    //     video: {
    //         facingMode: 'environment'
    //     }
    // };


    // Webcam.set('constraints', {
    //     width: 1280,
    //     height: 720,
    //     // facingMode: 'environment',
    //     facingMode: {
    //         exact: 'environment'
    //     },
    //     // width: 320,
    //     // height: 240,
    //     // dest_width: 640,
    //     // dest_height: 480,
    //     image_format: 'jpeg',
    //     jpeg_quality: 100,
    //     force_flash: false,
    //     flip_horiz: false,
    //     // enable_flash: true,
    //     fps: 45
    // });


    // Webcam.set({
    //     width: 320,
    //     height: 240,
    //     // facingMode: {
    //     //     exact: 'environment'
    //     // }
    // });

    // Webcam.attach('#my_camera');

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

    // var shutter = new Audio();
    // shutter.autoplay = false;
    // shutter.src = navigator.userAgent.match(/Firefox/) ? 'shutter.ogg' : 'shutter.mp3';

    // function take_snapshot() {

    //     shutter.play();

    //     // take snapshot and get image data
    //     Webcam.snap(function(data_uri) {
    //         // display results in page
    //         // document.getElementById('results').innerHTML =
    //         //     '<img src="' + data_uri + '"/>';
    //         $(".image-tag1").val(data_uri);
    //         document.getElementById('result_fp').innerHTML = '<img src="' + data_uri + '"/>';
    //     });
    // }

    // function take_snapshot2() {

    //     // shutter.play();

    //     // take snapshot and get image data
    //     Webcam.snap(function(data_uri) {
    //         // display results in page
    //         // document.getElementById('results').innerHTML =
    //         //     '<img src="' + data_uri + '"/>';
    //         $(".image-tag2").val(data_uri);
    //         document.getElementById('result_fr').innerHTML = '<img src="' + data_uri + '"/>';
    //     });
    // }
</script>