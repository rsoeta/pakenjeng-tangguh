<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card text-center">
                <div class="card-header bg-success shadow">
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
                <div class="container-fluid">
                    <div class="row">
                        <?php if (session()->getFlashdata('message')) { ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert" style="text-align: center;">
                                <?= session()->getFlashdata('message') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php } ?>
                        <div class="col-12">
                            <ol class="breadcrumb float-right">
                                <div class="row">
                                    <div class="btn-group">
                                        <?php if ($role < 3) { ?>
                                            <form action="<?= site_url('importPbi') ?>" method="post" enctype="multipart/form-data">
                                                <?= csrf_field(); ?>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="col-12 input-group">
                                                            <input type="file" name="file" class="form-control form-control float-right" required accept=".xls, .xlsx">
                                                            <button type="submit" name="submit" class="btn btn-info float-right" onclick="return confirmSubmit()">
                                                                <i class="fa fa-cloud-upload-alt"></i> Upload
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        <?php } ?>
                                        <button href="#" type="button" class="btn btn-sm btn-primary float-right tombolTambah"><i class="fa fa-plus"></i> Tambah Data</button>
                                        <a href="/exportExcel" type="button" class="btn btn-sm btn-success float-right"><i class="fa fa-file-excel"></i> Export Excel</a>
                                    </div>
                                </div>
                            </ol>
                        </div><!-- /.col -->
                    </div>
                </div><!-- /.row -->
            <?php } ?>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">TABEL VERIVALI</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">HASIL VERIVALI</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="row my-2">
                        <div class="col">
                            <div class="row">
                                <div class="col-sm-6 col-12 mb-1">
                                    <select <?php if ($role >= 3) {
                                                echo 'disabled="disabled"';
                                            } ?> class="form-control form-control-sm" name="" id="datadesa">
                                        <option value="">[ Nama Desa Kosong ]</option>
                                        <?php foreach ($desKels as $row) { ?>
                                            <option <?= $kode_desa == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-12 mb-1">
                                    <select <?php if ($role == 4) {
                                                echo 'disabled="disabled"';
                                            } ?> class="form-control form-control-sm" name="" id="datarw">
                                        <option value="">[ No. RW Kosong ]</option>
                                        <?php foreach ($datarw as $row) { ?>
                                            <option <?php if ($level == $row['no_rw']) {
                                                        echo 'selected';
                                                    } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-12 mb-1">
                                    <select class="form-control form-control-sm" name="" id="datart">
                                        <option value="">[ No. RT Kosong ]</option>

                                    </select>
                                </div>
                                <div class="col-sm-6 col-12 mb-1">
                                    <select class="form-control form-control-sm" name="" id="datastatus">
                                        <option value="">[ Status Kosong ]</option>
                                        <option value="1" selected>AKTIF</option>
                                        <?php foreach ($status as $row) { ?>
                                            <option value="<?= $row['id_status']; ?>"><?= $row['jenis_status']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php if ($role <= 3) { ?>
                                    <?php $val = 0 ?>
                                    <div class="col-sm-6 col-12" hidden>
                                        <select class="form-control form-control-sm" name="" id="dataVvPbi">
                                            <option value="<?= $val; ?>" selected>[ Ket. Verivali Kosong ]</option>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <table id="tabel_data" class="table table-sm table-striped compact" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NOKA</th>
                                    <th>PSNOKA</th>
                                    <th>Alamat</th>
                                    <th>No KK</th>
                                    <th>NIK SIAK</th>
                                    <th>Tempat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Status Kawin</th>
                                    <th>Nama Ibu</th>
                                    <th>CekLap</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row my-2">
                        <div class="col">
                            <div class="row">
                                <div class="col-sm-6 col-12 mb-1">
                                    <select <?php if ($role >= 3) {
                                                echo 'disabled="disabled"';
                                            } ?> class="form-control form-control-sm" name="" id="datadesaverivali">
                                        <option value="">[ Nama Desa Kosong ]</option>
                                        <?php foreach ($desKels as $row) { ?>
                                            <option <?= $kode_desa == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-12 mb-1">
                                    <select <?php if ($role == 4) {
                                                echo 'disabled="disabled"';
                                            } ?> class="form-control form-control-sm" name="" id="datarwverivali">
                                        <option value="">[ No. RW Kosong ]</option>
                                        <?php foreach ($datarw as $row) { ?>
                                            <option <?php if ($level == $row['no_rw']) {
                                                        echo 'selected';
                                                    } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="<?= $role <= 3 ? 'col-sm-4' : 'col-sm-6'; ?> col-12 mb-1">
                                    <select class="form-control form-control-sm" name="" id="datartverivali">
                                        <option value="">[ No. RT Kosong ]</option>
                                    </select>
                                </div>
                                <div class="<?= $role <= 3 ? 'col-sm-4' : 'col-sm-6'; ?> col-12 mb-1">
                                    <select class="form-control form-control-sm" name="" id="datastatusverivali">
                                        <option value="" selected>[ Status Kosong ]</option>
                                        <?php foreach ($status as $row) { ?>
                                            <option value="<?= $row['id_status']; ?>"><?= $row['jenis_status']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php if ($role <= 3) { ?>
                                    <div class="<?= $role <= 3 ? 'col-sm-4' : 'col-sm-6'; ?> col-12 mb-1">
                                        <select class="form-control form-control-sm" name="" id="dataVvPbiverivali">
                                            <?php foreach ($verivali_pbi as $row) { ?>
                                                <option value="<?= $row['vp_id']; ?>"><?= $row['vp_keterangan']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <table id="tabel_data_verivali" class="table table-sm table-striped compact" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>NOKA</th>
                                    <th>PSNOKA</th>
                                    <th>Alamat</th>
                                    <th>No KK</th>
                                    <th>NIK SIAK</th>
                                    <th>NIK SIKS</th>
                                    <th>Tempat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Status Kawin</th>
                                    <th>Nama Ibu</th>
                                    <th>CekLap</th>
                                    <th>Ket. Verval</th>
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
    });

    table = $('#tabel_data').DataTable({
        'order': [],
        'fixedHeader': true,
        'searching': true,
        'paging': true,
        'rowReorder': {
            selector: 'td:nth-child(2)'
        },
        'responsive': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('/tabel_pbi'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.datadesa = $('#datadesa').val();
                data.datarw = $('#datarw').val();
                data.datart = $('#datart').val();
                data.datastatus = $('#datastatus').val();
                data.dataVvPbi = $('#dataVvPbi').val();
            },
            "dataSrc": function(response) {
                $('input[name=csrf_test_name]').val(response.csrf_test_name);
                return response.data;
            }
        },

        "columnDefs": [{
            "targets": [0],
            "orderable": false
        }]
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
    $('#datastatus').change(function() {
        table.draw();
    });
    $('#dataVvPbi').change(function() {
        table.draw();
    });

    table2 = $('#tabel_data_verivali').DataTable({
        'order': [],
        'fixedHeader': true,
        'searching': true,
        'paging': true,
        'rowReorder': {
            selector: 'td:nth-child(2)'
        },
        'responsive': true,
        'compact': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('/tabel_pbi_verivali'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.datadesaverivali = $('#datadesaverivali').val();
                data.datarwverivali = $('#datarwverivali').val();
                data.datartverivali = $('#datartverivali').val();
                data.datastatusverivali = $('#datastatusverivali').val();
                data.dataVvPbiverivali = $('#dataVvPbiverivali').val();
            },
            "dataSrc": function(response) {
                $('input[name=csrf_test_name]').val(response.csrf_test_name);
                return response.data;
            }
        },

        "columnDefs": [{
            "targets": [0],
            "orderable": false
        }]
    });


    $('#datadesaverivali').change(function() {
        table2.draw();
    });
    $('#datarwverivali').change(function() {
        table2.draw();
    });
    $('#datartverivali').change(function() {
        table2.draw();
    });
    $('#datastatusverivali').change(function() {
        table2.draw();
    });
    $('#dataVvPbiverivali').change(function() {
        table2.draw();
    });


    function save() {
        $('#btnSave').text('saving...'); //change button text
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('person/ajax_add') ?>";
        } else {
            url = "<?php echo site_url('ajax_update') ?>";
        }

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(data) {

                if (data.status) //if success close modal and reload ajax table
                {
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
                        title: 'Data berhasil di update!',
                    });
                    // $('#modal_form').modal('hide');
                    // reload_table();
                    window.location.reload();

                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled', false); //set button enable 


            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled', false); //set button enable 

            }
        });
    }

    function edit_person(id) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('editpbi') ?>",
            data: {
                id: id
            },
            dataType: "JSON",
            success: function(response) {
                if (response.sukses) {
                    $('.viewmodal').html(response.sukses).show();
                    $('#modaledit').modal('show');
                }

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    function confirmSubmit() {
        // pop up comfirm to submit send data
        {
            var agree = confirm("Hati-Hati!!\nData sebelumnya akan terhapus. \nApakah Anda yakin akan mengimport data ini?");
            if (agree)
                return true;
            else
                return false;
        }
    }

    $(document).on('click', '#deleteBtn', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        // alert(id);
        // $('.editIndividu').modal('show');
        tanya = confirm(`HAPUS DATA "${nama}"?`);
        if (tanya == true) {
            $.ajax({
                type: "post",
                url: "<?= base_url('dltPbi'); ?>",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    if (response.informasi) {
                        alert(response.informasi);
                    } else if (response.sukses) {
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
                }
            });
        }

    });
</script>

<?= $this->endSection(); ?>