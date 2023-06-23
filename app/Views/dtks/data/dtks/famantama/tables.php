<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    canvas {
        max-width: 600px;
        margin: 20px auto;
    }

    .add-button {
        position: fixed;
        bottom: 6%;
        right: 4%;
        /* Nilai z-index yang tinggi */
        z-index: 5;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #007bff;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .plus-icon {
        font-weight: bold;
    }
</style>
<div class="content-wrapper mt-1">
    <section class="content-header">
        <div class="container-fluid">
            <ol class="breadcrumb float-right">
                <li class="breadcrumb-item"><a href="<?= base_url('/pages'); ?>">Home</a></li>
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
            <!-- start modal dialog multi-step form wizard -->
            <!-- <div class="container"> -->
            <!-- <div class="row d-flex justify-content-center"> <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"> Launch multistep Wizard </button> </div> Modal -->
            <!-- </div> -->

            <!-- end modal dialog multi-step form wizard -->
            <?php if (session()->get('message')) : ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?= session()->get('message'); ?>
                </div>
            <?php endif; ?>


            <div class="row">
                <div class="col-12">
                    <div class="card card-dark card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Pendataan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Diagram</a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Messages</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Settings</a>
                                </li> -->
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                <div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="row">
                                                <div class="col-6 col-sm-3 mb-2">
                                                    <!-- <button type="button" class="btn btn-success btn-block tombolTambah">
                                                        <i class="fa fa-plus"></i> Tambah Data
                                                    </button> -->
                                                    <button type="button" class="add-button tombolTambah">
                                                        <span class="plus-icon"><i class="fas fa-user-plus"></i></span>
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?= form_open('/exportFamantama', ['target' => 'blank']); ?>
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="row">
                                                <div class="col-6 col-sm-3 mb-2" <?= $user != 3 ?  'hidden' :  ''; ?>>
                                                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" onclick="reload_table()">
                                                        <i class="fa fa-sync-alt"></i> Reload
                                                    </button>
                                                </div>
                                                <div class="col-6 col-sm-3 mb-2" <?= $user > 3 ?  'hidden' :  ''; ?>>
                                                    <button type="submit" name="btnExpData" class="btn btn-success btn-block" id="exportExcel">
                                                        <i class="fa fa-file-excel"></i> Export Data
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
                                            <select <?= $user >= 4 ? 'disabled = "true"' : ''; ?> class="form-control form-control-sm" name="rw" id="rw">
                                                <option value="">[ Semua RW ]</option>
                                                <?php foreach ($datarw as $row) { ?>
                                                    <option <?= $jabatan == $row['no_rw'] ? 'selected' : ''; ?> value="<?= $row['no_rw']; ?>"><?= $row['no_rw']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 col-3 mb-2">
                                            <select class="form-control form-control-sm" name="rt" id="rt">
                                                <option value="">[ Semua RT ]</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 col-3 mb-2">
                                            <select class="form-control form-control-sm" name="data_tahun" id="data_tahun">
                                                <option value="">[ Semua Tahun ]</option>
                                                <?php
                                                $mulai = 2021;
                                                $tahun = date('Y');
                                                for ($i = $mulai; $i < $mulai + 3; $i++) { ?>
                                                    <option value='<?= $i; ?>' <?= $i == $tahun ? ' selected' : ''; ?>><?= $i; ?></option>
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
                                                    <option value="<?= $ValueBulan; ?>" <?= $bulanIni == $ValueBulan ? ' selected' : ''; ?>><?= $LabelBulan; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 col-6 mb-2">
                                            <select class="form-control form-control-sm" name="shdk" id="shdk">
                                                <option value="">[ SHDK ]</option>
                                                <?php foreach ($shdk as $row) { ?>
                                                    <option value="<?= $row['id']; ?>"><?= $row['jenis_shdk']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <?= form_close() ?>

                                    </div>
                                    <div>
                                        <br>
                                        <table class="table" id="tabel_data" style="width: 100%;">
                                            <thead class="text-dark">
                                                <tr>
                                                    <th>NO</th>
                                                    <th>NAMA</th>
                                                    <th>NIK</th>
                                                    <th>NO. KK</th>
                                                    <th>ALAMAT</th>
                                                    <th>NO. RT</th>
                                                    <th>NO. RW</th>
                                                    <th>SHDK</th>
                                                    <th>PEKERJAAN KEPALA KELUARGA</th>
                                                    <th>PENDATA</th>
                                                    <th>UPLOAD PADA</th>
                                                    <th>AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                    <div class="row">
                                        <h3><strong>Capaian Per/RT</strong></h3>
                                    </div>
                                    <!-- /.col -->
                                    <div class="row">
                                        <div class="col-12 col-sm-8 mb-4">
                                            <h5>Persentase</h5>
                                            <?php $no = 1 ?>
                                            <?php foreach ($chartData as $row) { ?>
                                                <div class="progress-group">
                                                    <span class="float-right"><b><?= number_format($row['jml_rkp'] / $totalFamantama * 100, 2, ',', '.'); ?>%</b></span>
                                                    <div class="progress" style="height: 20px;">
                                                        <?php $persentase = $row['jml_rkp'] / $totalFamantama * 100; ?>
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-dark" style="width: <?= number_format($persentase * 2, 2); ?>%">
                                                            <div style="text-align: left; font-size: smaller;"><?= $no; ?>. <?= ucfirst(strtolower($row['fd_rt'])); ?> / <?= $row['fd_rw']; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $no++ ?>
                                            <?php } ?>
                                        </div>

                                        <div class="col-12 col-sm-4">
                                            <h5>Tabel</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>NO.</th>
                                                            <th>DESA</th>
                                                            <th>RT</th>
                                                            <th>RW</th>
                                                            <th>JUMLAH</th>
                                                            <th>PERSENTASE</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $no = 1 ?>
                                                        <?php foreach ($chartData as $row) : ?>
                                                            <tr style="text-align: right;">
                                                                <td style="text-align: center;"><?= $no; ?></td>
                                                                <td style="text-align: left;"><?= $row['name']; ?></td>
                                                                <td style="text-align: left;"><?= $row['fd_rt']; ?></td>
                                                                <td style="text-align: left;"><?= $row['fd_rw']; ?></td>
                                                                <td style="text-align: right;"><?= number_format($row['jml_rkp'], '0', ',', '.'); ?></td>
                                                                <td style="text-align: right;"><?= number_format($row['jml_rkp'] / $totalFamantama * 100, 2, ',', '.'); ?>%</td>
                                                            </tr>
                                                            <?php $no++ ?>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="4" style="text-align: center;">TOTAL</th>
                                                            <th style="text-align: right;"><?= number_format($totalFamantama, '0', ',', '.'); ?></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <a href="/famantama" class="btn btn-sm btn-secondary float-right">Rincian</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                                    Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
                                    Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.container-fluid -->
<div class="viewmodal" style="display: none;"></div>
<script>
    // 'use strict';
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $('.displayNone').css('display', 'none');

    });

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
            "url": "<?= site_url('tbFamantama'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.desa = $('#desa').val();
                data.rw = $('#rw').val();
                data.rt = $('#rt').val();
                // data.kerja_famantama = $('#kerja_famantama').val();
                data.data_tahun = $('#data_tahun').val();
                data.data_bulan = $('#data_bulan').val();
                data.shdk = $('#shdk').val();
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
    // $('#kerja_famantama').change(function() {
    //     table.draw();
    // });
    $('#data_tahun').change(function() {
        table.draw();
    });
    $('#data_bulan').change(function() {
        table.draw();
    });
    $('#shdk').change(function() {
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
                type: "POST",
                url: "<?= site_url('/dltFamantama'); ?>",
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
            url: "<?php echo site_url('/editFamantama') ?>",
            data: {
                id: id
            },
            dataType: "JSON",
            success: function(response) {
                if (response.informasi) {
                    alert(response.informasi);

                } else if (response.sukses) {
                    $('.viewmodal').html(response.sukses).show();
                    $('#modaledit').on('shown.bs.modal', function(event) {
                        $('#nik').focus();
                    });
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

        $('#tabel_data');

        $('.tombolTambah').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= site_url('tambahFamantama') ?>",
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

    $('#rw01').ready(function() {
        var desa = $('#desa').val();
        var no_rw = $('#rw01').val();
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
                    $('#rt01').html(html);
                }
            });
        } else {
            $('#rt01').val('');
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

    function previewImgId() {
        const du_foto_identitas = document.querySelector('#du_foto_identitas');
        const imgPreview = document.querySelector('.img-preview-id');


        const fileIdentitas = new FileReader();
        fileIdentitas.readAsDataURL(du_foto_identitas.files[0]);

        fileIdentitas.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }

    function previewImgRmh() {
        const du_foto_rumah = document.querySelector('#du_foto_rumah');
        const imgPreview = document.querySelector('.img-preview-rmh');


        const fileIdentitas = new FileReader();
        fileIdentitas.readAsDataURL(du_foto_rumah.files[0]);

        fileIdentitas.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }
</script>

<?= $this->endSection(); ?>