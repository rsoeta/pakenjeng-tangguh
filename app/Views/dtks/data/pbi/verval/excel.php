<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js" integrity="sha512-3fMsI1vtU2e/tVxZORSEeuMhXnT9By80xlmXlsOku7hNwZSHJjwcOBpmy+uu+fyWwGCLkMvdVbHkeoXdAzBv+w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php
$role = session()->get('role_id');
$kode_desa = session()->get('kode_desa');
$ops = null;
$level = session()->get('level');
?>

<div class="content-wrapper mt-1">
    <section class="content">
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info">
                    <div class="row">
                        <div class="col">
                            <h5 class="m-0 font-weight-bold text-dark text-center"><?= $title; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" onclick="reload_table()">
                                        <i class="fa fa-sync-alt"></i> Reload
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a href="javascript: history.back()" type="button" class="btn btn-warning btn-block">
                                        <i class="fa fa-undo-alt"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
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
                                    <option value="">[ Keterangan Kosong ]</option>
                                    <?php foreach ($verivali_pbi as $row) { ?>
                                        <option value="<?= $row['vp_id']; ?>"><?= $row['vp_keterangan']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="container-fluid">
                                <div class="tengah">
                                    <table class="table table-hover table-head-fixed" id="tabexport">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Alamat</th>
                                                <th>No. KK</th>
                                                <th>NIK SIAK</th>
                                                <th>NIK SIKS</th>
                                                <th>Tanggal Lahir</th>
                                                <th>Nama Ibu</th>
                                                <th>Ket. Verval</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- End of Main Content -->
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

    table2 = $('#tabexport').DataTable({
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
        "pageLength": 1000,
        'dom': 'Bfrtip',
        'buttons': [{
            title: null,
            extend: 'excelHtml5',
            customizeData: function(data) {
                for (var i = 0; i < data.body.length; i++) {
                    for (var j = 0; j < data.body[i].length; j++) {
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            },
            orientation: 'landscape'
        }],
        "ajax": {
            "url": "<?= site_url('/tabexport'); ?>",
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
</script>

<?= $this->endSection(); ?>