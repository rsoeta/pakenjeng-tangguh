<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card text-center">
                <div class="card-header bg-dark">
                    <strong><?= $title; ?></strong>
                </div>
            </div>
            <?php
            $user = session()->get('role_id');
            $desa_id = session()->get('kode_desa');
            $ops = session()->get('jabatan');
            $level = session()->get('level');
            ?>
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
            <table id="tabel_data" class="table table-hover table-sm compact">
                <thead>
                    <tr>
                        <th>No</th>
                        <?php if ($user <= 3) { ?>
                            <th>ID DTKS</th>
                        <?php } ?>
                        <th>Nama</th>
                        <th>No. KK</th>
                        <th>NIK</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
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
            "url": "<?= site_url('tabel_bnba'); ?>",
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
    $('#datashdk').change(function() {
        table.draw();
    });

    $(document).on('click', '#deleteBtn', function() {
        var id = $(this).data('id');
        // alert(id);
        // $('.editIndividu').modal('show');
        tanya = confirm("Hapus data ini?");
        if (tanya == true) {
            $.ajax({
                type: "post",
                url: "<?= base_url('individu/delete'); ?>",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
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
                        window.location.reload();
                    }
                }
            });
        }

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