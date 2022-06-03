<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card text-center">
                <div class="card-header">
                    <strong><?= $title; ?></strong>
                </div>
            </div>
            <?php
            $user = session()->get('role_id');
            $desa_id = session()->get('kode_desa');
            $ops = session()->get('level');
            ?>
            <div class="row my-2">
                <div class="col">
                    <div class="row">
                        <div class="col-sm-1 col-3 mb-1">
                            <label for="desa" class="form-label">
                                Desa
                            </label>
                        </div>
                        <div class="col-sm-2 col-9">
                            <select <?php if ($user >= 2) {
                                        echo 'disabled="disabled"';
                                    } ?> class="form-control form-control-sm" name="" id="desa">
                                <option value="">-Pilih-</option>
                                <?php foreach ($desKels as $row) { ?>
                                    <option <?php if ($desa_id == $row['id']) {
                                                echo 'selected';
                                            } ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-1 col-3 mb-1">
                            <label for="rw" class="form-label">
                                RW
                            </label>
                        </div>
                        <div class="col-sm-2 col-9">
                            <select <?php if ($user == 3) {
                                        echo 'disabled="disabled"';
                                    } ?> class="form-control form-control-sm" name="" id="rw">
                                <option value="">-Pilih-</option>
                                <?php foreach ($datarw as $row) { ?>
                                    <option <?php if ($ops == $row['rw']) {
                                                echo 'selected';
                                            } ?> value="<?php echo $row['rw']; ?>"><?php echo $row['rw']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-1 col-3 mb-1">
                            <label for="rt" class="form-label">
                                RT
                            </label>
                        </div>
                        <div class="col-sm-2 col-9">
                            <select class="form-control form-control-sm" name="" id="rt">
                                <option value="">-Pilih-</option>
                                <?php foreach ($datart as $row) { ?>
                                    <option value="<?= $row['rt']; ?>"><?= $row['rt']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-1 col-3 mb-1">
                            <label for="keterangan" class="form-label">
                                Keterangan
                            </label>
                        </div>
                        <div class="col-sm-2 col-9">
                            <select class="form-control form-control-sm" name="" id="keterangan">
                                <option value="">-Pilih-</option>
                                <?php foreach ($keterangan as $row) { ?>
                                    <option value="<?= $row['id_ketvv']; ?>"><?= $row['jenis_keterangan']; ?></option>
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
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>NIK</th>
                        <th>No. KK</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Indikasi Masalah</th>
                        <th>Keterangan</th>
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
    table = $('#tabel_data').DataTable({
        'order': [],
        'fixedHeader': true,
        'searching': true,
        'paging': true,
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        'responsive': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('verivali09/tabel_data'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.desa = $('#desa').val();
                data.rw = $('#rw').val();
                data.rt = $('#rt').val();
                data.keterangan = $('#keterangan').val();
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


    $('#desa').change(function() {
        table.draw();
    });
    $('#rw').change(function() {
        table.draw();
    });
    $('#rt').change(function() {
        table.draw();
    });
    $('#keterangan').change(function() {
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

    function edit_person(idv) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('editVerivali') ?>",
            data: {
                idv: idv
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

    $(document).ready(function() {

        // $('body').addClass('sidebar-collapse');

        $('#rw').change(function() {

            var no_rw = $('#rw').val();

            var action = 'get_rt';

            if (no_rw != '') {
                $.ajax({
                    url: "<?php echo base_url('individu/get_rt'); ?>",
                    method: "POST",
                    data: {
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
</script>

<?= $this->endSection(); ?>