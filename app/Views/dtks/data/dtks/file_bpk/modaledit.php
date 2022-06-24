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
            <div class="modal-body">
                <?= form_open_multipart('', ['class' => 'formupload']) ?>
                <?= csrf_field(); ?>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group row nopadding">
                            <label for="vg_dbj_id1" class="col-2 col-form-label">Bansos</label>
                            <div class="col-4">
                                <select id="vg_dbj_id1" name="vg_dbj_id1" class="form-select form-select-sm" disabled>
                                    <?php foreach ($Bansos as $row) { ?>
                                        <option <?php if ($vg_dbj_id1 == $row['dbj_id']) {
                                                    echo 'selected';
                                                } ?> value="<?= $row['dbj_id'] ?>"> <?php echo $row['dbj_nama_bansos']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorvg_dbj_id1"></div>
                            </div>
                            <input type="hidden" name="vg_id" id="vg_id" class="form-control form-control-sm" value="<?= set_value('vg_id', $vg_id); ?>">
                            <label class="col-2 col-form-label" for="vg_nik">NIK</label>
                            <div class="col-4">
                                <input type="text" name="vg_nik" id="vg_nik" class="form-control form-control-sm" value="<?= set_value('vg_nik', $vg_nik); ?>" <?= $role > 2 ? 'readonly' : ''; ?>>
                                <div class="invalid-feedback errornik"></div>
                            </div>
                        </div>
                        <!-- <input type="file" name="foto" id="foto" class="form-control form-control-sm" hidden> -->
                        <div class="form-group row nopadding">
                            <label class="col-2 col-form-label" for="vg_nama_lengkap">Nama</label>
                            <div class="col-10">
                                <input type="text" name="vg_nama_lengkap" id="vg_nama_lengkap" class="form-control form-control-sm" value="<?= set_value('vg_nama_lengkap', $vg_nama_lengkap); ?>" <?= $role > 2 ? 'readonly' : ''; ?>>
                                <div class="invalid-feedback errorvg_nama_lengkap"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-sm-6 col-12">
                        <div class="form-group row nopadding">
                            <label class="col-3 col-sm-4 col-form-label" for="vg_alamat">Alamat</label>
                            <div class="col-9 col-sm-8">
                                <input type="text" name="vg_alamat" id="vg_alamat" class="form-control form-control-sm" value="<?= set_value('vg_alamat', $vg_alamat); ?>">
                                <div class="invalid-feedback errorvg_alamat"></div>
                            </div>
                        </div>

                        <div class="form-group row nopadding">
                            <label class="col-3 col-sm-4 col-form-label" for="vg_rt">No. RT</label>
                            <div class="col-3 col-sm-8">
                                <input type="number" name="vg_rt" id="vg_rt" class="form-control form-control-sm" value="<?= set_value('vg_rt', $vg_rt); ?>">
                                <div class="invalid-feedback errorvg_rt"></div>
                            </div>
                            <label class="col-3 col-sm-4 col-form-label" for="vg_rw">No. RW</label>
                            <div class="col-3 col-sm-8">
                                <input type="number" name="vg_rw" id="vg_rw" class="form-control form-control-sm" value="<?= set_value('vg_rw', $vg_rw); ?>">
                                <div class="invalid-feedback errorvg_rw"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?= ($role > 2) ? 'hidden' : ''; ?>>
                            <label for="vg_ds_id" class="col-4 col-sm-4 col-form-label">Status PM</label>
                            <div class="col-8 col-sm-8">
                                <select id="vg_ds_id" name="vg_ds_id" class="form-select form-select-sm">
                                    <?php foreach ($statusDtks as $row) { ?>
                                        <option <?= ($row['id_status'] == $vg_ds_id) ? 'selected' : ''; ?> value="<?= $row['id_status']; ?>"> <?php echo $row['jenis_status']; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback errorvg_ds_id"></div>
                            </div>
                        </div>
                        <div class="form-group row nopadding" <?= ($role > 2) ? 'hidden' : ''; ?>>
                            <label for="vg_status" class="col-4 col-sm-4 col-form-label">Ket. Verivali</label>
                            <div class="col-8 col-sm-8">
                                <select id="vg_status" name="vg_status" class="form-select form-select-sm">
                                    <option selected value="1"> Proses</option>
                                </select>
                                <div class="invalid-feedback errorvg_status"></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-sm-6 col-12">
                        <div class="row">
                            <div class="col-6">
                                <!-- <label for="image_fr">Foto Rumah</label> -->
                                <div id="result_fr" hidden></div>
                                <input type="hidden" name="image_fr" class="image-tag2">
                                <div class="invalid-feedback errorimage_fr"></div>
                            </div>
                            <div class="col-6">
                                <!-- <label for="image_fp">Foto PM</label> -->
                                <div id="result_fp" hidden></div>
                                <input type="hidden" name="image_fp" class="image-tag1">
                                <div class="invalid-feedback errorimage_fp"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="my_camera"></div>
                                <p id="z"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <input type="hidden" class="form-control form-control-sm mb-2" placeholder="Latitude" spellcheck="false" data-ms-editor="true" id="latitude" name="vg_lat">
                                <button class="btn btn-sm btn-block btn-info" type="button" onclick="take_snapshot2();getLocation();"><i class="fa fa-home"></i> Foto Rumah</button>
                            </div>
                            <div class="col-6">
                                <input type="hidden" class="form-control form-control-sm mb-2" placeholder="Longitude" spellcheck="false" data-ms-editor="true" id="longitude" name="vg_lang">
                                <button class="btn btn-sm btn-block btn-primary" type="button" onclick="take_snapshot()"><i class="fa fa-user"></i> Foto PM</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mt-3">
                        <button type="submit" class="btn btn-success btn-block btnSimpan">Submit</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
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
    Webcam.set('constraints', {
        // width: 1280,
        // height: 720,
        deviceId: {
            exact: deviceId
        },
        // facingMode: 'environment',
        // width: 320,
        // height: 240,
        dest_width: 640,
        dest_height: 480,
        image_format: 'jpeg',
        jpeg_quality: 100,
        force_flash: false,
        flip_horiz: false,
        enable_flash: true,
        fps: 45
    });
    Webcam.attach('#my_camera');

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

    var shutter = new Audio();
    shutter.autoplay = false;
    shutter.src = navigator.userAgent.match(/Firefox/) ? 'shutter.ogg' : 'shutter.mp3';

    function take_snapshot() {

        shutter.play();

        // take snapshot and get image data
        Webcam.snap(function(data_uri) {
            // display results in page
            // document.getElementById('results').innerHTML =
            //     '<img src="' + data_uri + '"/>';
            $(".image-tag1").val(data_uri);
            document.getElementById('result_fp').innerHTML = '<img src="' + data_uri + '"/>';
        });
    }

    function take_snapshot2() {

        // shutter.play();

        // take snapshot and get image data
        Webcam.snap(function(data_uri) {
            // display results in page
            // document.getElementById('results').innerHTML =
            //     '<img src="' + data_uri + '"/>';
            $(".image-tag2").val(data_uri);
            document.getElementById('result_fr').innerHTML = '<img src="' + data_uri + '"/>';
        });
    }
</script>