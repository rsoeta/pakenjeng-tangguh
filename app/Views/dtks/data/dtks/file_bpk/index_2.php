<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<!-- <script src="<?php base_url('/assets/dist/js/webcam.min.js'); ?>"></script> -->

<script async src="<?= base_url('/assets/dist/js/capture.js'); ?>"></script>


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
            <?php if ($role <= 2) {  ?>
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
                                <!-- <div class="row">
                                    <div class="col-12">
                                        <label class="col-8" for="">Import Data</label>
                                        <div class="input-group">
                                            <input type="file" name="file" id="file" class="form-control">
                                            <button type="submit" id="form_data" onclick="return confirmSubmit()" class="btn btn-outline-secondary">Upload File</button>
                                        </div>
                                        <div class="col-4">
                                        </div>
                                    </div>
                                </div> -->
                                <div class="row">
                                    <div class="col-12 col-sm-9">
                                        <label class="col-8" for="">Import Data</label>
                                        <div class="col-12 col-sm-12 input-group">
                                            <input type="file" name="file" class="form-control form-control float-right" required accept=".xls, .xlsx">
                                            <button type="submit" name="submit" class="btn btn-success btn float-right" onclick="return confirmSubmit()">
                                                <i class="fa fa-upload"></i> Upload
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <label class="col-12" for="">Reload Data</label>
                                        <div class="col-12 col-sm-12 input-group">
                                            <button type="button" class="btn btn-info btn  float-right" data-toggle="modal" onclick="reload_table()">
                                                <i class="fa fa-sync-alt"></i> Reload
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </ol>
                    </div><!-- /.col -->
                </div>
            <?php } ?>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= ($role > 2) ? 'active' : ''; ?>" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">TABEL VERIVALI</button>
                </li>
                <li class="nav-item" role="presentation" <?= ($role > 2) ? 'hidden' : ''; ?>>
                    <button class="nav-link <?= ($role <= 2) ? 'active' : ''; ?>" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">HASIL VERIVALI</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade <?= ($role > 2) ? 'show active' : ''; ?>" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row my-2">
                        <div class="col">
                            <div class="row">
                                <div class="col-sm-3 col-6 mb-1">
                                    <select <?php if ($role >= 3) {
                                                echo 'disabled="disabled"';
                                            } ?> class="form-control form-control-sm" name="" id="datadesa">
                                        <option value="">[ Semua Desa ]</option>
                                        <?php foreach ($desKels as $row) { ?>
                                            <option <?= $kode_desa == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-6 mb-1">
                                    <select <?php if ($role > 3) {
                                                echo 'disabled="disabled"';
                                            } ?> class="form-control form-control-sm" name="" id="datarw">
                                        <option value="">[ Semua No. RW ]</option>
                                        <?php foreach ($datarw as $row) { ?>
                                            <option <?php if ($level == $row['no_rw']) {
                                                        echo 'selected';
                                                    } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-6 mb-1">
                                    <select class="form-control form-control-sm" name="" id="datart">
                                        <option value="">[ Semua No. RT ]</option>

                                    </select>
                                </div>
                                <div class="col-sm-3 col-6 mb-1">
                                    <select class="form-control form-control-sm" name="" id="dataBansos">
                                        <option value="">[ Semua Bansos ]</option>
                                        <?php foreach ($Bansos as $row) { ?>
                                            <option value="<?= $row['dbj_id']; ?>"><?= $row['dbj_nama_bansos']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-6 mb-1" hidden>
                                    <select class="form-control form-control-sm" name="" id="dataStatus">
                                        <option value="0" selected>0</option>
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
                <div class="tab-pane fade <?= ($role <= 2) ? 'show active' : ''; ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row my-2">
                        <div class="col">
                            <div class="row">
                                <div class="col-sm-3 col-6 mb-1">
                                    <select <?php if ($role >= 3) {
                                                echo 'disabled="disabled"';
                                            } ?> class="form-control form-control-sm" name="" id="datadesa2">
                                        <option value="">[ Semua Desa ]</option>
                                        <?php foreach ($desKels as $row) { ?>
                                            <option <?= $kode_desa == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-6 mb-1">
                                    <select <?php if ($role > 3) {
                                                echo 'disabled="disabled"';
                                            } ?> class="form-control form-control-sm" name="" id="datarw2">
                                        <option value="">[ Semua No. RW ]</option>
                                        <?php foreach ($datarw as $row) { ?>
                                            <option <?php if ($level == $row['no_rw']) {
                                                        echo 'selected';
                                                    } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-6 mb-1">
                                    <select class="form-control form-control-sm" name="" id="dataStatusPm">
                                        <option value="">[ Semua Status ]</option>
                                        <?php foreach ($dataStatus2 as $row) { ?>
                                            <option value="<?= $row['id_status']; ?>"><?= $row['jenis_status']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-6 mb-1">
                                    <select class="form-control form-control-sm" name="" id="dataBansos2">
                                        <option value="">[ Semua Bansos ]</option>
                                        <?php foreach ($Bansos as $row) { ?>
                                            <option value="<?= $row['dbj_id']; ?>"><?= $row['dbj_nama_bansos']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 col-6 mb-1" hidden>
                                    <select class="form-control form-control-sm" name="" id="dataStatus2">
                                        <option value="1" selected>1</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <table id="example" class="table table-sm table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
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

        var table = $('#example').DataTable({
                'order': [],
                'fixedHeader': true,
                'searching': true,
                'paging': true,
                'responsive': true,
                'compact': true,
                'processing': true,
                'serverSide': true,
                'dom': "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'<'float-md-right ml-2'B>f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                'ajax': {
                    'url': '<?= site_url('tabelGeo2'); ?>',
                    'type': 'POST',
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
                    },
                    "dataSrc": function(response) {
                        $('input[name=csrf_test_name]').val(response.csrf_test_name);
                        return response.data;
                    }
                },
                'buttons': ['csv', {
                    'text': '<i class="fa fa-id-badge fa-fw" aria-hidden="true"></i>',
                    'action': function(e, dt, node) {

                        $(dt.table().node()).toggleClass('cards');
                        $('.fa', node).toggleClass(['fa-table', 'fa-id-badge']);

                        dt.draw('page');
                    },
                    'className': 'btn-sm',
                    'attr': {
                        'title': 'Change views',
                    }
                }],
                'select': 'single',
                'columns': [{
                        'orderable': true,
                        'data': null,
                        'className': 'text-center',
                        'render': function(data, type, full, meta) {
                            if (type === 'display') {
                                var token = (Math.random() * 3 * 1e38).toString(16);
                                data = '<i class="fa fa-user fa-fw"></i>';
                                data = '<img src="https://www.gravatar.com/avatar/' + token + '.png?d=robohash" class="avatar border rounded-circle">';
                            }

                            return data;
                        }
                    },
                    {
                        'data': 'vg_nama_lengkap'
                    },
                    {
                        'data': 'vg_nik'
                    },
                    {
                        'data': 'vg_nkk'
                    },
                    {
                        'data': 'vg_alamat',
                        // 'class': 'text-right'
                    },
                    {
                        'data': 'namaDesa',
                        // 'class': 'text-right'
                    },
                    {
                        'data': 'dbj_nama_bansos',
                    },
                    {
                        'data': 'sta_nama',
                    },
                    {
                        'data': 'null'
                    }
                ],
                'drawCallback': function(settings) {
                    var api = this.api();
                    var $table = $(api.table().node());

                    if ($table.hasClass('cards')) {

                        // Create an array of labels containing all table headers
                        var labels = [];
                        $('thead th', $table).each(function() {
                            labels.push($(this).text());
                        });

                        // Add data-label attribute to each cell
                        $('tbody tr', $table).each(function() {
                            $(this).find('td').each(function(column) {
                                $(this).attr('data-label', labels[column]);
                            });
                        });

                        var max = 0;
                        $('tbody tr', $table).each(function() {
                            max = Math.max($(this).height(), max);
                        }).height(max);

                    } else {
                        // Remove data-label attribute from each cell
                        $('tbody td', $table).each(function() {
                            $(this).removeAttr('data-label');
                        });

                        $('tbody tr', $table).each(function() {
                            $(this).height('auto');
                        });
                    }
                }
            })
            .on('select', function(e, dt, type, indexes) {
                var rowData = table.rows(indexes).data().toArray()
                $('#row-data').html(JSON.stringify(rowData));
            })
            .on('deselect', function() {
                $('#row-data').empty();
            })
            .on('order.dt search.dt', function() {
                table.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).on('click', 'tbody td', function() {
                var data = table.row($(this).parent()).data();
                $('#row-data').html(JSON.stringify(data));
            });


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
            "url": "<?= site_url('tabelGeo'); ?>",
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
            "url": "<?= site_url('tabelGeo2'); ?>",
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


    function edit_person(vg_id) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('editGeo') ?>",
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

    function edit_person2(vg_id) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('editGeo2') ?>",
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
</script>


<?= $this->endSection(); ?>