<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>


<div class="content-wrapper mt-1">
    <section class="content-header">
        <div class="container-fluid">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="<?= base_url('dtks/pages'); ?>">Home</a></li>
                <li class="breadcrumb-item active"><?= $title; ?></li>
            </ol>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <?php
        $user = session()->get('role_id');
        $nik = session()->get('nik');
        $jabatan = session()->get('level');
        $desa_id = $user_login['kode_desa'];
        // echo deadline_usulan();
        ?>
        <div class="card-body">
            <?php if (session()->get('message')) : ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?= session()->get('message'); ?>
                </div>
            <?php endif; ?>
            <?= form_open('dtks/usulan22/export', ['target' => 'blank']); ?>
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="row">
                        <div class="col-6 col-sm-3 mb-2" <?= $user != 3 ?  'hidden' :  ''; ?>>
                            <a href="exportBa" type="submit" name="btnExpBA" class="btn btn-danger btn-block" id="exportBA">
                                <i class="fa fa-clipboard-check"></i> Export B.A
                            </a>
                        </div>
                        <div class="col-6 col-sm-3 mb-2" <?= $user > 3 ?  'hidden' :  ''; ?>>
                            <button type="submit" name="btnExpData" class="btn btn-success btn-block" id="exportExcel">
                                <i class="fa fa-file-excel"></i> Export Data
                            </button>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <button type="button" class="btn btn-info btn-block" data-toggle="modal" onclick="reload_table()">
                                <i class="fa fa-sync-alt"></i> Reload
                            </button>
                        </div>
                        <div class="col-6 col-sm-3 mb-2">
                            <button type="button" class="btn btn-primary btn-block tombolTambah">
                                <i class="fa fa-plus"></i> Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-2 col-6 mb-2">
                    <select <?= $user >= 3 ? 'disabled' : ''; ?> class="form-control form-control-sm" name="desa" id="desa">
                        <option value="">[ Semua Desa ]</option>
                        <?php foreach ($desa as $row) { ?>
                            <option <?= $desa_id == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-2 col-3 mb-2">
                    <select <?php if ($user >= 4) {
                                echo 'disabled = "true"';
                            } ?> class="form-control form-control-sm" name="rw" id="rw">
                        <option value="">[ Semua RW ]</option>
                        <?php foreach ($datarw as $row) { ?>
                            <option <?php if ($jabatan == $row['no_rw']) {
                                        echo 'selected';
                                    } ?> value="<?= $row['no_rw']; ?>"><?= $row['no_rw']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-2 col-3 mb-2">
                    <select class="form-control form-control-sm" name="rt" id="rt">
                        <option value="">[ Semua RT ]</option>
                    </select>
                </div>
                <div class="col-sm-2 col-6 mb-2">
                    <select class="form-control form-control-sm" name="bansos" id="bansos">
                        <option value="">[ Semua Bansos ]</option>
                        <?php foreach ($bansos as $row) { ?>
                            <option value="<?= $row['dbj_id']; ?>"><?= $row['dbj_ket_bansos']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-2 col-3 mb-2">
                    <select class="form-control form-control-sm" name="data_tahun" id="data_tahun">
                        <option value="">[ Semua Tahun ]</option>
                        <?php
                        $mulai = 2021;
                        $tahun = date('Y');
                        for ($i = $mulai; $i < $mulai + 3; $i++) { ?>
                            <option value='<?= $i; ?>' <?php if ($i == $tahun) {
                                                            echo ' selected';
                                                        } ?>><?= $i; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-2 col-3 mb-2">
                    <select class="form-control form-control-sm" name="data_bulan" id="data_bulan">
                        <option value="">[ Semua Bulan ]</option>
                        <?php

                        $bulan = [1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        $bulanIni = date('n');
                        for ($i = 0; $i < 12; $i++) {
                            $AmbilNamaBulan = strtotime(sprintf('%d months', $i));
                            $LabelBulan     = $bulan[date('n', $AmbilNamaBulan)];
                            $ValueBulan     = date('n', $AmbilNamaBulan);
                            // if ($ValueBulan < $i) continue;
                        ?>
                            <option value="<?php echo $ValueBulan; ?>" <?php if ($bulanIni == $ValueBulan) {
                                                                            echo ' selected';
                                                                        } ?>><?php echo $LabelBulan; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?= form_close() ?>
            <div>
                <br>
                <table class="table" id="tabel_data">
                    <thead class=" text-primary">
                        <tr>
                            <th>NO</th>
                            <th>NAMA</th>
                            <th>NO. KK</th>
                            <th>NIK</th>
                            <th>JENIS KELAMIN</th>
                            <th>ALAMAT</th>
                            <th>SHDK</th>
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
            "url": "<?= site_url('tabel_data'); ?>",
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
                data.data_tahun = $('#data_tahun').val();
                data.data_bulan = $('#data_bulan').val();
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
    $('#data_tahun').change(function() {
        table.draw();
    });
    $('#data_bulan').change(function() {
        table.draw();
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
                url: "<?= base_url('dltUsul'); ?>",
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

    function edit_person(id) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('editUsulan') ?>",
            data: {
                id: id
            },
            dataType: "JSON",
            success: function(response) {
                if (response.informasi) {
                    alert(response.informasi);

                } else if (response.sukses) {
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
                url: "<?= site_url('tambah') ?>",
                dataType: "json",
                success: function(response) {
                    $('.viewmodal').html(response.data).show();

                    // $('#modaltambah').modal('show');
                    // $('#modaltambah').modal('show', function() {
                    //     $($this).find('#nokk').focus();
                    // });
                    $('#modaltambah').on('shown.bs.modal', function(event) {
                        $('#nokk').focus();
                    });
                    $('#modaltambah').modal('show');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    // alert('Batas waktu untuk Tambah Data, Telah Habis!!');
                }
            });
        });


        $('#tabel_data');


        $('#rw').change(function() {
            var desa = $('#desa').val();
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

        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 5000);
    });

    function reload_table() {
        table.ajax.reload(null, false); //reload datatable ajax 
    }

    $('#rw').ready(function() {
        var desa = $('#desa').val();
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


    $(function() {
        $('#exportExcel').click(function() {
            var $elt = $('#desa').removeAttr('disabled', '');
            setTimeout(function() {
                $elt.attr('disabled', true);
            }, 500);

        });
    });

    $(function() {
        $('#exportBA').click(function() {
            // $('#desa').removeAttr('disabled', '');
            // window.location.reload();
            // $("#desa").attr('disabled', 'true');
            var $elt = $('#desa').removeAttr('disabled', '');
            setTimeout(function() {
                $elt.attr('disabled', true);
            }, 500);

        });
    });
</script>

<?= $this->endSection(); ?>