<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<?php
$user = session()->get('role_id');
isset($user_login['lp_kode']) ? $desa_id = $user_login['lp_kode'] : $desa_id = session()->get('kode_desa');
$ops = session()->get('jabatan');
$level = session()->get('level');
?>
<div class="content-wrapper mt-3">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs justify-content-center" id="custom-tabs-five-tab" role="tablist">
                                <li class="pt-2 px-3">
                                    <h3 class="card-title"><?= $title; ?></h3>
                                </li>
                            </ul>
                            <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link <?= $user >= 3 ? 'active' : ''; ?>" id="custom-tabs-five-overlay-tab" data-toggle="pill" href="#custom-tabs-five-overlay" role="tab" aria-controls="custom-tabs-five-overlay" aria-selected="false">Data Verivali</a>
                                </li>
                                <?php if ($user <= 3) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= $user <= 2 ? 'active' : ''; ?>" id="custom-tabs-five-overlay-dark-tab" data-toggle="pill" href="#custom-tabs-five-overlay-dark" role="tab" aria-controls="custom-tabs-five-overlay-dark" aria-selected="false">Progres Verivali</a>
                                    </li>
                                <?php } ?>
                                <?php if ($user <= 2) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-five-normal-tab" data-toggle="pill" href="#custom-tabs-five-normal" role="tab" aria-controls="custom-tabs-five-normal" aria-selected="true">Succes Verivali</a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="card-body">
                            <?php if ($user <= 4) { ?>
                                <div class="tab-content" id="custom-tabs-five-tabContent">
                                    <div class="tab-pane fade <?= $user >= 3 ? 'active show' : ''; ?>" id="custom-tabs-five-overlay" role="tabpanel" aria-labelledby="custom-tabs-five-overlay-tab">
                                        <div class="row my-2">
                                            <div class="col">
                                                <div class="row">
                                                    <div class="col-sm-1 col-2 mb-1">
                                                        <label for="datadesa" class="form-label">
                                                            Desa
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2 col-4">
                                                        <select <?php if ($user >= 3) {
                                                                    echo 'disabled="disabled"';
                                                                } ?> class="form-control form-control-sm" name="" id="datadesa">
                                                            <option value="">-Pilih-</option>
                                                            <?php foreach ($desKels as $row) { ?>
                                                                <option <?php if ($desa_id == $row['id']) {
                                                                            echo 'selected';
                                                                        } ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-1 col-2 mb-1">
                                                        <label for="datarw" class="form-label">
                                                            RW
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2 col-4">
                                                        <select <?php if ($user >= 4) {
                                                                    echo 'disabled="disabled"';
                                                                } ?> class="form-control form-control-sm" name="" id="datarw">
                                                            <option value="">-Pilih-</option>
                                                            <?php foreach ($datarw as $row) { ?>
                                                                <option <?php if ($ops == $row['no_rw']) {
                                                                            echo 'selected';
                                                                        } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-1 col-2 mb-1">
                                                        <label for="datart" class="form-label">
                                                            RT
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2 col-4">
                                                        <select class="form-control form-control-sm" name="" id="datart">
                                                            <option value="">-Pilih-</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-1 col-2 mb-1">
                                                        <label for="datashdk" class="form-label">
                                                            SHDK
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-2 col-4">
                                                        <select class="form-control form-control-sm" name="" id="datashdk">
                                                            <option value="">-Pilih-</option>
                                                            <?php foreach ($datashdk as $row) { ?>
                                                                <option value="<?= $row['id']; ?>"><?= $row['jenis_shdk']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row my-2">
                                            <table id="tabel_data" class="table table-hover table-sm compact" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Foto</th>
                                                        <th>ID DTKS</th>
                                                        <th>NIK</th>
                                                        <th>Nama</th>
                                                        <th>No. KK</th>
                                                        <th>Alamat</th>
                                                        <th>RT</th>
                                                        <th>RW</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php if ($user <= 3) { ?>
                                        <div class="tab-pane fade <?= $user <= 2 ? 'active show' : ''; ?>" id="custom-tabs-five-overlay-dark" role="tabpanel" aria-labelledby="custom-tabs-five-overlay-dark-tab">
                                            <div class="overlay-wrapper">
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="row mb-2">
                                                            <div class="col">
                                                                <button type="button" class="btn btn-info btn-sm  float-right" data-toggle="modal" onclick="reload_table()">
                                                                    <i class="fa fa-sync-alt"></i> Reload
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-1 col-2 mb-1">
                                                                <label for="datadesa1" class="form-label">
                                                                    Desa
                                                                </label>
                                                            </div>
                                                            <div class="col-sm-2 col-4">
                                                                <select <?php if ($user >= 3) {
                                                                            echo 'disabled="disabled"';
                                                                        } ?> class="form-control form-control-sm" name="" id="datadesa1">
                                                                    <option value="">-Pilih-</option>
                                                                    <?php foreach ($desKels as $row) { ?>
                                                                        <option <?php if ($desa_id == $row['id']) {
                                                                                    echo 'selected';
                                                                                } ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-1 col-2 mb-1">
                                                                <label for="datarw1" class="form-label">
                                                                    RW
                                                                </label>
                                                            </div>
                                                            <div class="col-sm-2 col-4">
                                                                <select <?php if ($user >= 4) {
                                                                            echo 'disabled="disabled"';
                                                                        } ?> class="form-control form-control-sm" name="" id="datarw1">
                                                                    <option value="">-Pilih-</option>
                                                                    <?php foreach ($datarw as $row) { ?>
                                                                        <option <?php if ($ops == $row['no_rw']) {
                                                                                    echo 'selected';
                                                                                } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-1 col-2 mb-1">
                                                                <label for="datart1" class="form-label">
                                                                    RT
                                                                </label>
                                                            </div>
                                                            <div class="col-sm-2 col-4">
                                                                <select class="form-control form-control-sm" name="" id="datart1">
                                                                    <option value="">-Pilih-</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-1 col-2 mb-1">
                                                                <label for="data_status1" class="form-label">
                                                                    Status
                                                                </label>
                                                            </div>
                                                            <div class="col-sm-2 col-4">
                                                                <select class="form-control form-control-sm" name="" id="data_status1">
                                                                    <option value="">-Pilih-</option>
                                                                    <?php foreach ($status as $row) {  ?>
                                                                        <option value="<?= $row['id_status']; ?>"><?= $row['jenis_status']; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row my-2">
                                                    <table id="tabel_data1" class="table table-hover table-sm compact" style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Foto</th>
                                                                <th>ID DTKS</th>
                                                                <th>Nama</th>
                                                                <th>NIK</th>
                                                                <th>No. KK</th>
                                                                <th>Desa</th>
                                                                <th>Status</th>
                                                                <th>Tanggal Kejadian</th>
                                                                <th>No. Registrasi Kejadian</th>
                                                                <th>Ket</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($user <= 2) { ?>
                                        <div class="tab-pane fade" id="custom-tabs-five-normal" role="tabpanel" aria-labelledby="custom-tabs-five-normal-tab">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="row mb-2">
                                                        <div class="col">
                                                            <button type="button" class="btn btn-info btn-sm float-right" data-toggle="modal" onclick="reload_table()">
                                                                <i class="fa fa-sync-alt"></i> Reload
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-1 col-2 mb-1">
                                                            <label for="datadesa2" class="form-label">
                                                                Desa
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-2 col-4">
                                                            <select <?php if ($user >= 3) {
                                                                        echo 'disabled="disabled"';
                                                                    } ?> class="form-control form-control-sm" name="" id="datadesa2">
                                                                <option value="">-Pilih-</option>
                                                                <?php foreach ($desKels as $row) { ?>
                                                                    <option <?php if ($desa_id == $row['id']) {
                                                                                echo 'selected';
                                                                            } ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-1 col-2 mb-1">
                                                            <label for="datarw2" class="form-label">
                                                                RW
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-2 col-4">
                                                            <select <?php if ($user >= 4) {
                                                                        echo 'disabled="disabled"';
                                                                    } ?> class="form-control form-control-sm" name="" id="datarw2">
                                                                <option value="">-Pilih-</option>
                                                                <?php foreach ($datarw as $row) { ?>
                                                                    <option <?php if ($ops == $row['no_rw']) {
                                                                                echo 'selected';
                                                                            } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-1 col-2 mb-1">
                                                            <label for="datart2" class="form-label">
                                                                RT
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-2 col-4">
                                                            <select class="form-control form-control-sm" name="" id="datart2">
                                                                <option value="">-Pilih-</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-1 col-2 mb-1">
                                                            <label for="data_status2" class="form-label">
                                                                Status
                                                            </label>
                                                        </div>
                                                        <div class="col-sm-2 col-4">
                                                            <select class="form-control form-control-sm" name="" id="data_status2">
                                                                <option value="">-Pilih-</option>
                                                                <?php foreach ($status as $row) {  ?>
                                                                    <option value="<?= $row['id_status']; ?>"><?= $row['jenis_status']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <table id="tabel_data2" class="table table-hover table-sm compact" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Foto</th>
                                                            <th>ID DTKS</th>
                                                            <th>Nama</th>
                                                            <th>NIK</th>
                                                            <th>No. KK</th>
                                                            <th>Desa</th>
                                                            <th>Status</th>
                                                            <th>Tanggal Kejadian</th>
                                                            <th>No. Registrasi Kejadian</th>
                                                            <th>Ket</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.container-fluid -->
<div class="viewmodal" style="display: none;"></div>
<script>
    $(document).ready(function() {

        // $('body').addClass('sidebar-collapse');

        $('#datarw').change(function() {
            var desa = $('#datadesa').val();
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

        $('#datarw1').change(function() {
            var desa = $('#datadesa1').val();
            var no_rw = $('#datarw1').val();
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
                        $('#datart1').html(html);
                    }
                });
            } else {
                $('#datart1').val('');
            }
        });

        $('#datarw2').change(function() {
            var desa = $('#datadesa2').val();
            var no_rw = $('#datarw2').val();
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
                        $('#datart2').html(html);
                    }
                });
            } else {
                $('#datart2').val('');
            }
        });
    });

    table = $('#tabel_data').DataTable({
        'order': [],
        'fixedHeader': true,
        'searching': true,
        'paging': true,
        'responsive': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('tabVerivaliBnba'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.datadesa = $('#datadesa').val();
                data.datarw = $('#datarw').val();
                data.datart = $('#datart').val();
                data.datashdk = $('#datashdk').val();
            },
            "dataSrc": function(response) {
                $('input[name=csrf_test_name]').val(response.csrf_test_name);
                return response.data;
            }
        },
        'columnDefs': [{
            'targets': [1, 2, 3, 5],
            'visible': false
        }],
    });


    $('#datadesa').change(function() {
        table.draw();
    });
    $('#datarw').change(function() {
        table.draw();
    });
    $('#datart').change(function() {
        table.draw();
    });
    $('#datashdk').change(function() {
        table.draw();
    });


    function edit_person(id_data) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('editBnba') ?>",
            data: {
                id_data: id_data
            },
            dataType: "JSON",
            success: function(response) {
                if (response.sukses) {
                    $('.viewmodal').html(response.sukses).show();
                    $('#modaledit').on('shown.bs.modal', function(event) {
                        $('#id_dtks').focus();
                    });
                    $('#modaledit').modal('show');
                }

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    table1 = $('#tabel_data1').DataTable({
        'order': [],
        'fixedHeader': true,
        'searching': true,
        'paging': true,
        'responsive': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('tabVerivaliBnba1'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.datadesa1 = $('#datadesa1').val();
                data.datarw1 = $('#datarw1').val();
                data.datart1 = $('#datart1').val();
                data.data_status1 = $('#data_status1').val();
            },
            "dataSrc": function(response) {
                $('input[name=csrf_test_name]').val(response.csrf_test_name);
                return response.data;
            }
        },
        'columnDefs': [{
            'targets': [1, 2],
            'visible': false
        }],
    });

    $('#datadesa1').change(function() {
        table1.draw();
    });
    $('#datarw1').change(function() {
        table1.draw();
    });
    $('#datart1').change(function() {
        table1.draw();
    });
    $('#data_status1').change(function() {
        table1.draw();
    });

    function edit_person1(id_data) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('editBnba1') ?>",
            data: {
                id_data: id_data
            },
            dataType: "JSON",
            success: function(response) {
                if (response.sukses) {
                    $('.viewmodal').html(response.sukses).show();
                    $('#modalUpdate').on('shown.bs.modal', function(event) {
                        $('#id_dtks').focus();
                    });
                    $('#modalUpdate').modal('show');
                }

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    $(document).on('click', '#ChangeNext', function(event) {
        // var statusid = $(this).attr('data-id');
        // alert(statusid);

        // if (confirm("Are you sure changing status?")) {
        event.preventDefault();
        var statusid = $(this).attr('data-id');
        $.ajax({
            url: 'lockBnba',
            method: 'POST',
            data: {
                statusid: statusid
            },
            dataType: 'json',
            success: function(response) {
                if (response.sukses) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1000,
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
                    // alert(response.sukses);
                    table.draw();
                    table1.draw();
                    table2.draw();
                    // table1.ajax.reload(null, false); //reload datatable ajax 
                }
                //WHERE IT WORKED
                // table1.DataTable().ajax.reload();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
        // } else {
        //     return false;
        // }
    });


    table2 = $('#tabel_data2').DataTable({
        'order': [],
        'fixedHeader': true,
        'searching': true,
        'paging': true,
        'responsive': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('tabVerivaliBnba2'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.datadesa2 = $('#datadesa2').val();
                data.datarw2 = $('#datarw2').val();
                data.datart2 = $('#datart2').val();
                data.data_status2 = $('#data_status2').val();
            },
            "dataSrc": function(response) {
                $('input[name=csrf_test_name]').val(response.csrf_test_name);
                return response.data;
            }
        },
        'columnDefs': [{
            'targets': [1, 2],
            'visible': false
        }],
    });

    $('#datadesa2').change(function() {
        table2.draw();
    });
    $('#datarw2').change(function() {
        table2.draw();
    });
    $('#datart2').change(function() {
        table2.draw();
    });
    $('#data_status2').change(function() {
        table2.draw();
    });

    function edit_person1(id_data) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('editBnba1') ?>",
            data: {
                id_data: id_data
            },
            dataType: "JSON",
            success: function(response) {
                if (response.sukses) {
                    $('.viewmodal').html(response.sukses).show();
                    $('#modalUpdate').on('shown.bs.modal', function(event) {
                        $('#id_dtks').focus();
                    });
                    $('#modalUpdate').modal('show');
                }

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    $(document).on('click', '#ChangePrev', function(event) {
        // var statusid = $(this).attr('data-id');
        // alert(statusid);

        // if (confirm("Are you sure changing status?")) {
        event.preventDefault();
        var statusid = $(this).attr('data-id');
        $.ajax({
            url: 'unlockBnba',
            method: 'POST',
            data: {
                statusid: statusid
            },
            dataType: 'json',
            success: function(response) {
                if (response.sukses) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 500,
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
                    // alert(response.sukses);
                    table.draw();
                    table1.draw(); //reload datatable ajax
                    table2.draw();
                    // table1.ajax.reload(null, false); //reload datatable ajax 
                }
                //WHERE IT WORKED
                // table1.DataTable().ajax.reload();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
        // } else {
        //     return false;
        // }
    });


    function reload_table() {
        table.ajax.reload(null, false); //reload datatable ajax
        table1.ajax.reload(null, false); //reload datatable ajax 
        table2.ajax.reload(null, false); //reload datatable ajax
    }

    $('#datarw').ready(function() {
        var desa = $('#datadesa').val();
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

    $('#datarw1').ready(function() {
        var desa = $('#datadesa1').val();
        var no_rw = $('#datarw1').val();
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
                    $('#datart1').html(html);
                }
            });
        } else {
            $('#datart1').val('');
        }
    });

    $('#datarw2').ready(function() {
        var desa = $('#datadesa2').val();
        var no_rw = $('#datarw2').val();
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
                    $('#datart2').html(html);
                }
            });
        } else {
            $('#datart2').val('');
        }
    });
</script>

<?= $this->endSection(); ?>