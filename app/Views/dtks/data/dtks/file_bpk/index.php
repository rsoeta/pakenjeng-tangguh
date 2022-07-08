<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<!-- <script src="<?php base_url('/assets/dist/js/webcam.min.js'); ?>"></script> -->

<!-- <script async src="<?= base_url('/assets/dist/js/capture.js'); ?>"></script> -->

<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card text-center">
                <div class="card-header bg-warning shadow">
                    <strong><?= $title; ?></strong>
                </div>
            </div>
            <?php
            $role = session()->get('role_id');
            $kode_desa = session()->get('kode_desa');
            $ops = null;
            $level = session()->get('level');
            ?>
            <?php if ($role <= 3) {  ?>
                <div class="row">
                    <div class="col-12">
                        <?php if (session()->getFlashdata('message')) { ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert" style="text-align: center;">
                                <?= session()->getFlashdata('message') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php } ?>
                        <ol class="float-right">
                            <form action="<?= site_url('imporVerivaliGeo') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field(); ?>
                                <div class="row">
                                    <div class="col-12 col-sm-5" <?= ($role > 2) ? 'hidden' : ''; ?>>
                                        <div class="col-12 input-group">
                                            <input type="file" name="file" class="form-control form-control float-right" required accept=".xls, .xlsx">
                                            <button type="submit" name="submit" class="btn btn-success btn float-right" onclick="return confirmSubmit()">
                                                <i class="fa fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </ol>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-sm-6 col-12">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="diagram-tab" data-bs-toggle="tab" data-bs-target="#diagram" type="button" role="tab" aria-controls="diagram" aria-selected="true">DIAGRAM</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="false">TAB VERIVALI</button>
                        </li>
                        <li class="nav-item" role="presentation" <?= ($role > 3) ? 'hidden' : ''; ?>>
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">PROSES VERIVALI</button>
                        </li>
                    </ul>
                </div>
                <?php if ($role > 1) { ?>

                <?php } ?>
            </div>
            <!-- create breadcrump -->
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="diagram" role="tabpanel" aria-labelledby="diagram-tab">
                    <div class="row my-2">
                        <div class="col-sm-6 col-12">
                            <canvas id="capaian-chart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row my-2">
                        <div class="col">
                            <div class="row">
                                <div class="col-sm-2 col-6 mb-1">
                                    <select <?= ($role > 2) ? 'disabled' : ''; ?> class="form-control form-control-sm" name="datadesa" id="datadesa">
                                        <option value="">[ Semua Desa ]</option>
                                        <?php foreach ($desKels as $row) { ?>
                                            <option <?= $kode_desa == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-2 col-6 mb-1">
                                    <select <?= ($role > 3) ? 'readonly' : ''; ?> class="form-control form-control-sm" name="" id="datarw">
                                        <option value="">[ Semua No. RW ]</option>
                                        <?php foreach ($datarw as $row) { ?>
                                            <option <?php if ($level == $row['no_rw']) {
                                                        echo 'selected';
                                                    } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-2 col-6 mb-1">
                                    <select class="form-control form-control-sm" name="" id="datart">
                                        <option value="">[ Semua No. RT ]</option>

                                    </select>
                                </div>
                                <div class="col-sm-2 col-6 mb-1">
                                    <select class="form-control form-control-sm" name="" id="dataBansos">
                                        <option value="">[ Semua Bansos ]</option>
                                        <?php foreach ($Bansos as $row) { ?>
                                            <option value="<?= $row['dbj_id']; ?>"><?= $row['dbj_nama_bansos']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-2 col-6 mb-1">
                                    <select class="form-control form-control-sm" id="dataIndikasi" name="indikasi[]">
                                        <option value="">[ Semua Kondisi ]</option>
                                        <?php foreach ($indikasiTemuan as $row) { ?>
                                            <option value="<?= $row['tkt_num']; ?>"><?= $row['tkt_ket']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-2 col-6 mb-1">
                                    <select class="form-control form-control-sm" name="" id="dataStatus">
                                        <option value="0" selected>Gagal</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <table id="tabel_data" class="table table-sm compact display" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NAMA</th>
                                    <th>NIK</th>
                                    <th>NO. KK</th>
                                    <th>ALAMAT</th>
                                    <th>NO. DESA</th>
                                    <th>JENIS BANSOS</th>
                                    <th>STATUS</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade <?= ($role > 2) ? 'hidden' : ''; ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row my-2">
                        <div class="col-12">
                            <form action="<?= site_url('/exportDataPdtt') ?>" method="post">
                                <?= csrf_field(); ?>
                                <div class="row mb-2">
                                    <div class="col-12 text-right">
                                        <button type="button" class="btn btn-info" data-toggle="modal" onclick="reload_table()">
                                            <i class="fa fa-sync-alt"></i> Reload
                                        </button>
                                        <!-- </div> -->
                                        <button type="submit" name="" id="exportDataPdtt" class="btn btn-success" <?= ($role > 3) ? 'hidden' : ''; ?>>
                                            <i class="fa fa-file-excel"></i> Export Data
                                        </button>
                                        <!-- </div> -->
                                        <a href="/exportBaPdtt" type="submit" name="exportBaPdtt" id="exportBaPdtt" class="btn btn-danger" <?= ($role > 3) ? 'hidden' : ''; ?>>
                                            <i class="fa fa-clipboard-check"></i> Export B.A
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2 col-6 mb-1">
                                        <select <?= ($role >= 3) ? 'disabled="disabled"' : ''; ?> class="form-control form-control-sm" name="datadesa2" id="datadesa2">
                                            <option value="">[ Semua Desa ]</option>
                                            <?php foreach ($desKels as $row) { ?>
                                                <option <?= $kode_desa == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 col-6 mb-1">
                                        <select <?php if ($role > 3) {
                                                    echo 'disabled="disabled"';
                                                } ?> class="form-control form-control-sm" name="datarw2" id="datarw2">
                                            <option value="">[ Semua No. RW ]</option>
                                            <?php foreach ($datarw as $row) { ?>
                                                <option <?php if ($level == $row['no_rw']) {
                                                            echo 'selected';
                                                        } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 col-6 mb-1">
                                        <select class="form-control form-control-sm" name="dataStatusPm" id="dataStatusPm">
                                            <option value="">[ Semua Status ]</option>
                                            <?php foreach ($dataStatus2 as $row) { ?>
                                                <option value="<?= $row['id_status']; ?>"><?= $row['jenis_status']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 col-6 mb-1">
                                        <select class="form-control form-control-sm" name="dataBansos2" id="dataBansos2">
                                            <option value="">[ Semua Bansos ]</option>
                                            <?php foreach ($Bansos as $row) { ?>
                                                <option value="<?= $row['dbj_id']; ?>"><?= $row['dbj_nama_bansos']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 col-6 mb-1">
                                        <select class="form-control form-control-sm" name="dataIndikasi2" id="dataIndikasi2">
                                            <option value="">[ Semua Kondisi ]</option>
                                            <?php foreach ($indikasiTemuan as $row) { ?>
                                                <option value="<?= $row['tkt_num']; ?>"><?= $row['tkt_ket']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 col-6 mb-1">
                                        <select class="form-control form-control-sm" name="dataStatus2" id="dataStatus2">
                                            <option value="0">Gagal</option>
                                            <option value="1" selected>Proses</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <table id="tabel_data2" class="table table-sm compact display" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NAMA</th>
                                    <th>NIK</th>
                                    <th>NO. KK</th>
                                    <th>ALAMAT</th>
                                    <th>NO. DESA</th>
                                    <th>JENIS BANSOS</th>
                                    <th>STATUS</th>
                                    <th>DOKUMENTASI</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
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
        $('.tombolTambah').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= site_url('tmbData') ?>",
                dataType: "json",
                success: function(response) {
                    $('.viewmodal').html(response.data).show();
                    $('#modaltambah').modal('show');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });

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
        $('#datarwverivali').change(function() {
            var desa = $('#datadesaverivali').val();
            var no_rw = $('#datarwverivali').val();
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
                        $('#datartverivali').html(html);
                    }
                });
            } else {
                $('#datartverivali').val('');
            }
        });

        $('.select2').select2();
    });

    table = $('#tabel_data').DataTable({
        'order': [],
        // 'fixedHeader': true,
        'searching': true,
        'paging': true,
        // 'rowReorder': {
        //     selector: 'td:nth-child(2)'
        // },
        'responsive': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('/tabelGeo'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.datadesa = $('#datadesa').val();
                data.datarw = $('#datarw').val();
                data.datart = $('#datart').val();
                data.dataBansos = $('#dataBansos').val();
                data.dataIndikasi = $('#dataIndikasi').val();
                data.dataStatus = $('#dataStatus').val();
            },
            "dataSrc": function(response) {
                $('input[name=csrf_test_name]').val(response.csrf_test_name);
                return response.data;
            }
        },

        "columnDefs": [{
                "targets": [0],
                "orderable": false,
            },
            {
                "targets": [7],
                "visible": false,
            }
        ]
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
    $('#dataBansos').change(function() {
        table.draw();
    });
    $('#dataIndikasi').change(function() {
        table.draw();
    });
    $('#dataStatus').change(function() {
        table.draw();
    });

    table2 = $('#tabel_data2').DataTable({
        'order': [],
        'fixedHeader': true,
        'searching': true,
        'paging': true,
        'responsive': true,
        'compact': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('/tabelGeo2'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.datadesa2 = $('#datadesa2').val();
                data.datarw2 = $('#datarw2').val();
                data.dataStatusPm = $('#dataStatusPm').val();
                data.dataBansos2 = $('#dataBansos2').val();
                data.dataStatus2 = $('#dataStatus2').val();
                data.dataIndikasi2 = $('#dataIndikasi2').val();
            },
            "dataSrc": function(response) {
                $('input[name=csrf_test_name]').val(response.csrf_test_name);
                return response.data;
            }
        },

        "columnDefs": [{
                "targets": [0],
                "orderable": false
            },
            {
                "targets": [7],
                "visible": false,
            }
        ]
    });


    $('#datadesa2').change(function() {
        table2.draw();
    });
    $('#datarw2').change(function() {
        table2.draw();
    });
    $('#dataStatusPm').change(function() {
        table2.draw();
    });
    $('#dataBansos2').change(function() {
        table2.draw();
    });
    $('#dataStatus2').change(function() {
        table2.draw();
    });
    $('#dataIndikasi2').change(function() {
        table2.draw();
    });


    function edit_person(vg_id) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('/editGeo') ?>",
            data: {
                vg_id: vg_id
            },
            dataType: "JSON",
            success: function(response) {
                if (response.sukses) {
                    $('.viewmodal').html(response.sukses).show();
                    $('#modalEdit').modal('show');
                }

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    function reload_table() {
        table.ajax.reload(null, false); //reload datatable ajax 
        table2.ajax.reload(null, false); //reload datatable ajax 
    }

    // function alert MyFunction with checkbox
    function confirmSubmit() {
        // pop up comfirm to submit send data
        {
            var agree = confirm("Data sebelumnya akan terhapus. \nApakah Anda yakin akan mengimport data ini?");
            if (agree)
                return true;
            else
                return false;
        }
    }

    $(function() {
        $('#exportBaPdtt').click(function() {
            var $elt = $('#datadesa2').removeAttr('disabled', '');
            setTimeout(function() {
                $elt.attr('disabled', true);
            }, 500);

        });
    });

    $(function() {
        $('#exportDataPdtt').click(function() {
            var $elt = $('#datadesa2').removeAttr('disabled', '');
            setTimeout(function() {
                $elt.attr('disabled', true);
            }, 500);

        });
    });

    $(function() {

        /*------------------------------------------
        --------------------------------------------
        Get the Pie Chart Canvas 
        --------------------------------------------
        --------------------------------------------*/
        var cData = JSON.parse(`<?= isset($dataCapaian) ? $dataCapaian : ''; ?>`);
        var ctx = $("#capaian-chart");

        /*------------------------------------------
        --------------------------------------------
        Pie Chart Data 
        --------------------------------------------
        --------------------------------------------*/
        var data = {
            labels: cData.label,
            datasets: [{
                label: "Jumlah Capaian",
                data: cData.dataCapaian,
                backgroundColor: [
                    "#DEB887",
                    "#A9A9A9",
                    "#DC143C",
                    "#F4A460",
                    "#2E8B57",
                    "#1D7A46",
                    "#CDA776",
                ],
                borderColor: [
                    "#CDA776",
                    "#989898",
                    "#CB252B",
                    "#E39371",
                    "#1D7A46",
                    "#F4A460",
                    "#CDA776",
                ],
                borderWidth: [1, 1, 1, 1, 1, 1, 1]
            }]
        };

        var options = {
            responsive: true,
            // scales: {
            //     r: {
            //         pointLabels: {
            //             display: true,
            //             centerPointLabels: true,
            //             font: {
            //                 size: 18
            //             }
            //         }
            //     }
            // },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Capaian Verivali PDTT'
                }
            }
            // responsive: true,
            // title: {
            //     display: true,
            //     position: "bottom",
            //     text: "Capaian Verivali PDTT",
            //     fontSize: 18,
            //     fontColor: "#111"
            // },
            // legend: {
            //     display: true,
            //     position: "top",
            //     labels: {
            //         fontColor: "#333",
            //         fontSize: 16
            //     }
            // }
        };

        /*------------------------------------------
        --------------------------------------------
        create Pie Chart class object
        --------------------------------------------
        --------------------------------------------*/
        var chart1 = new Chart(ctx, {
            type: "pie",
            data: data,
            options: options
        });

    });
</script>

<?= $this->endSection(); ?>