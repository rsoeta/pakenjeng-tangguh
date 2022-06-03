<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <section class="content">
        <div class="card-header py-3 text-center">
            <div class="row">
                <h3 class="m-0 font-weight-bold text-primary"><?= $title; ?></h3>
            </div>
        </div>
        <div class="card-body">
            <?php
            $user = session()->get('level');
            $nik = session()->get('nik');
            $jabatan = session()->get('jabatan');
            $desa_id = session()->get('kode_desa');
            ?>
            <div class="row">
                <div class="col-6 col-sm-4 mb-2">
                    <button type="button" class="btn btn-outline-warning btn-block shadow" data-toggle="modal" onclick="reload_table()">
                        <i class="fa fa-sync-alt"> Reload</i>
                    </button>
                </div>
                <div class="col-6 col-sm-4 mb-2">
                    <button type="button" class="btn btn-outline-primary btn-block shadow tombolTambah">
                        <i class="fa fa-plus"> Tambah Data</i>
                    </button>
                </div>
                <div class="col-12 col-sm-4 mb-2" <?= $user != 1 ?  'hidden' :  ''; ?>>
                    <a href="/dtks/usulan21/excel" type="button" class="btn btn-outline-success shadow btn-block">
                        <i class="fa fa-file-excel"> Export Data</i>
                    </a>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-3 col-6 mb-1">
                    <select <?php if ($user >= 2) {
                                echo 'disabled="disabled"';
                            } ?> class="form-control form-control-sm" name="desa" id="desa">
                        <option value="">-Pilih Desa-</option>
                        <?php foreach ($desa as $row) { ?>
                            <option <?php if ($desa_id == $row['id']) {
                                        echo 'selected';
                                    } ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-3 col-6 mb-1">
                    <select <?php if ($user >= 3) {
                                echo 'disabled="disabled"';
                            } ?> class="form-control form-control-sm" name="rw" id="rw">
                        <option value="">-Pilih RW-</option>
                        <?php foreach ($rw as $row) { ?>
                            <option <?php if ($jabatan == $row['no_rw']) {
                                        echo 'selected';
                                    } ?> value="<?= $row['no_rw']; ?>"><?= $row['no_rw']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-3 col-6 mb-1">
                    <select class="form-control form-control-sm" name="rt" id="rt">
                        <option value="">-Pilih RT-</option>
                        <?php foreach ($datart as $row) { ?>
                            <option value="<?= $row['id_rt']; ?>"><?= $row['id_rt']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-3 col-6 mb-1">
                    <select class="form-control form-control-sm" name="bansos" id="bansos">
                        <option value="">-Pilih Program-</option>
                        <?php foreach ($bansos as $row) { ?>
                            <option value="<?= $row['Id']; ?>"><?= $row['NamaBansos']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <br>
                <table class="table" id="tabel_data">
                    <thead class="text-black">
                        <tr>
                            <th>NO</th>
                            <th>NAMA</th>
                            <th>NO. KK</th>
                            <th>NIK</th>
                            <th>JENIS KELAMIN</th>
                            <th>TEMPAT LAHIR</th>
                            <th>TANGGAL LAHIR</th>
                            <th>IBU KANDUNG</th>
                            <th>JENIS PEKERJAAN</th>
                            <th>STATUS PERKAWINAN</th>
                            <th>ALAMAT</th>
                            <th>RT</th>
                            <th>RW</th>
                            <th>DESA</th>
                            <th>KECAMATAN</th>
                            <th>SHDK</th>
                            <th>FOTO RUMAH</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<!-- /.container-fluid -->
<div class="viewmodal" style="display: none;"></div>
<script>
    var save_method; //for save method string
    var table;

    table = $('#tabel_data').DataTable({
        'order': [],
        'fixedHeader': true,
        'searching': true,
        'paging': true,
        'responsive': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('dtks/tab_usul'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.desa = $('#desa').val();
                data.rw = $('#rw').val();
                data.rt = $('#rt').val();
                data.bansos = $('#bansos').val();
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
    $('#bansos').change(function() {
        table.draw();
    });

    $(document).on('click', '#deleteBtn', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        // alert(id);
        // $('.editIndividu').modal('show');
        tanya = confirm(`Yakin Anda akan Menghapus Data ${nama}?`);
        if (tanya == true) {
            $.ajax({
                type: "post",
                url: "<?= base_url('dtks/dltUsul'); ?>",
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
                        // window.location.reload();
                        table.draw();

                    }
                }
            });
        }

    });


    function edit_person(id) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('dtks/editUsulan') ?>",
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

    $(document).ready(function() {

        // $('body').addClass('sidebar-collapse');

        $('.tombolTambah').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= site_url() ?>/dtks/usulan21/formtambah",
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
        $('#tabel_data');
    });

    function reload_table() {
        table.ajax.reload(null, false); //reload datatable ajax 
    }

    $(document).ready(function() {

        $('#rw').change(function() {

            var no_rw = $('#rw').val();

            var action = 'get_rt';

            if (no_rw != '') {
                $.ajax({
                    url: "<?php echo base_url('dtks/action'); ?>",
                    method: "POST",
                    data: {
                        no_rw: no_rw,
                        action: action
                    },
                    dataType: "JSON",
                    success: function(data) {
                        var html = '<option value="">-Pilih-</option>';

                        for (var count = 0; count < data.length; count++) {

                            html += '<option value="' + data[count].id_rt + '">' + data[count].id_rt + '</option>';

                        }

                        $('#rt').html(html);
                    }
                });
            } else {
                $('#rt').val('');
            }
        });
    });

    $(document).ready(function() {

        $('#datarw').change(function() {

            var no_rw = $('#datarw').val();

            var action = 'get_rt';

            if (no_rw != '') {
                $.ajax({
                    url: "<?php echo base_url('dtks/action'); ?>",
                    method: "POST",
                    data: {
                        no_rw: no_rw,
                        action: action
                    },
                    dataType: "JSON",
                    success: function(data) {
                        var html = '<option value="">-Pilih-</option>';

                        for (var count = 0; count < data.length; count++) {

                            html += '<option value="' + data[count].id_rt + '">' + data[count].id_rt + '</option>';

                        }

                        $('#datart').html(html);
                    }
                });
            } else {
                $('#datart').val('');
            }
        });
    });

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);
</script>

<?= $this->endSection(); ?>