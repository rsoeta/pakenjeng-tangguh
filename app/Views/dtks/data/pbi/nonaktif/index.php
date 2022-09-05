<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>



<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card text-center">
                <div class="card-header bg-secondary shadow">
                    <strong><?= $title; ?></strong>
                </div>
            </div>
            <?php
            $role = session()->get('role_id');
            $kode_desa = session()->get('kode_desa');
            $ops = null;
            $level = session()->get('level');
            ?>
            <?php if ($role <= 4) {  ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <ol class="breadcrumb float-right">
                                <div class="row">
                                    <div class="btn-group">
                                        <?php if ($role <= 3) { ?>
                                            <a href="#" type="button" class="btn btn-sm btn-success float-right"><i class="fa fa-file-excel"></i> Export Excel</a>
                                        <?php } ?>
                                        <button type="button" class="btn btn-sm btn-primary float-end tombolTambah"><i class="fa fa-plus"></i> Tambah data</button>
                                    </div>
                                </div>
                            </ol>
                        </div><!-- /.col -->
                    </div>
                </div><!-- /.row -->
            <?php } ?>
            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col-sm-4 col-12 mb-1">
                            <select <?php if ($role >= 3) {
                                        echo 'disabled="disabled"';
                                    } ?> class="form-control form-control-sm" name="" id="datadesa">
                                <option value="">[ Nama Desa Kosong ]</option>
                                <?php foreach ($desKels as $row) { ?>
                                    <option <?= $kode_desa == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-4 col-12 mb-1">
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
                        <div class="col-sm-4 col-12 mb-1">
                            <select class="form-control form-control-sm" name="" id="datart">
                                <option value="">[ No. RT Kosong ]</option>

                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <table id="tb_pbi_nonaktif" class="table table-bordered border-secondary table-hover table-sm compact" style="width: 100%;">
                    <thead class="table-secondary">
                        <tr>
                            <th colspan="6">DATA KIS</th>
                            <th colspan="6">DATA PM</th>
                        </tr>
                        <tr>
                            <th>NO</th>
                            <th>NAMA</th>
                            <th>PSNOKA</th>
                            <th>NIKKA</th>
                            <th>TTL</th>
                            <th>ALAMAT</th>
                            <th>NIK PM</th>
                            <th>NAMA</th>
                            <th>NO. KK</th>
                            <th>TTL</th>
                            <th>ALAMAT</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
    </section>
</div>
<!-- /.container-fluid -->
<div class="viewmodal" style="display: none;"></div>
<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');

        $('.tombolTambah').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= site_url('tmbNA') ?>",
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

    $(document).on('click', '#deleteBtn', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        // alert(id);
        // $('.editIndividu').modal('show');
        tanya = confirm(`HAPUS DATA "${nama}"?`);
        if (tanya == true) {
            $.ajax({
                type: "post",
                url: "<?= base_url('dltInactive'); ?>",
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

    table = $('#tb_pbi_nonaktif').DataTable({
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
            "url": "<?= base_url('tb_pbi_nonaktif'); ?>",
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
            url: "<?php echo site_url('editInactive') ?>",
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