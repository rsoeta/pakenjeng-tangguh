<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>


<!-- jQuery Library -->
<script src="<?= base_url(); ?>/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?= base_url(); ?>/assets/dist/js/jquery/3.6.0/jquery-3.6.0.min.js"></script>
<script src="<?= base_url(); ?>/assets/dist/js/jquery/datatables/1.10.19/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.9/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script><!-- Content Wrapper. Contains page content -->

<style>
    #bg-orange {
        background-color: orange;
    }
</style>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <h4 class="m-0"><?= strtoupper('rekap ' . $title); ?></b></h5>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div><!-- /.container-fluid -->
    <?php
    $user = session()->get('role_id');
    $nik = session()->get('nik');
    $jabatan = session()->get('opr_sch');
    $desa_id = session()->get('kode_desa');
    ?>
    <div class="container-fluid">
        <div class="card-body">
            <div class="row">
                <div class="col-6 col-sm-4">
                    <button type="button" class="btn btn-outline-success btn-block shadow" data-toggle="modal" onclick="reload_table()">
                        <i class="fa fa-sync-alt"></i> Reload
                    </button>
                </div>
                <div class="col-6 col-sm-4 mb-2">
                    <button type="button" class="btn btn-outline-primary btn-block shadow tombolTambah">
                        <i class="fa fa-plus"></i> Tambah Data
                    </button>
                </div>
                <div class="col-12 col-sm-4 mb-2" <?= $user > 3 ?  'hidden' :  ''; ?>>
                    <a href="/expKip" type="button" class="btn btn-outline-success shadow btn-block">
                        <i class="fa fa-file-excel"></i> Export Data
                    </a>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-3 col-6 mb-2">
                        <select <?php echo $user >= 3 ? 'readonly="readonly" tabindex="-1" aria-disabled="true"' : ''; ?> class="form-control form-control-sm" name="desa" id="desa">
                            <option value="">[ Desa Kosong ]</option>
                            <?php foreach ($desa as $row) { ?>
                                <option <?php if ($desa_id == $row['id']) {
                                            echo 'selected';
                                        } ?> value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-3 col-6 mb-2">
                        <select <?php echo $user > 2 ? 'readonly="readonly" tabindex="-1" aria-disabled="true"' : ''; ?> id="nama_sekolah" name="nama_sekolah" class="form-select form-select-sm">
                            <option value="">[ Sekolah Kosong ]</option>
                            <?php foreach ($nama_sekolah as $row) { ?>
                                <option <?php echo $jabatan == $row['opr_sch'] ? 'selected' : ''; ?> value="<?= $row['opr_sch']; ?>"><?= $row['opr_sch']; ?></option>
                            <?php } ?>
                        </select>
                        </select>
                    </div>
                    <div class="col-sm-3 col-6 mb-2">
                        <select class="form-control form-control-sm" name="jenjang" id="jenjang">
                            <option value="">[ Jenjang Kosong ]</option>
                            <?php foreach ($jenjang_sekolah as $row) { ?>
                                <option value="<?= $row['sj_id']; ?>"><?= $row['sj_nama']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-3 col-6 mb-2">
                        <select class="form-control form-control-sm" name="kelas" id="kelas">
                            <option value="">[ Kelas Kosong ]</option>
                            <?php foreach ($kelas_sekolah as $row) { ?>
                                <option value="<?= $row['dk_kelas']; ?>"><?= $row['dk_kelas']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <br>
                    <table class="table" id="tabel_data">
                        <thead class="text-success">
                            <tr>
                                <th>NO</th>
                                <th>KKS</th>
                                <th>KIP</th>
                                <th>NIK</th>
                                <th>Nama Siswa</th>
                                <th>Alamat</th>
                                <th>Jenjang Pendidikan Terakhir</th>
                                <th>Kelas Terakhir</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
            "url": "<?= site_url('tabel_kip'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.desa = $('#desa').val();
                data.nama_sekolah = $('#nama_sekolah').val();
                data.jenjang = $('#jenjang').val();
                data.kelas = $('#kelas').val();
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
    $('#nama_sekolah').change(function() {
        table.draw();
    });
    $('#jenjang').change(function() {
        table.draw();
    });
    $('#kelas').change(function() {
        table.draw();
    });

    $(document).on('click', '#deleteBtn', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        // alert(id);
        // $('.editIndividu').modal('show');
        tanya = confirm(`Hapus data ${nama}?`);
        if (tanya == true) {
            $.ajax({
                type: "post",
                url: "<?= base_url('dltKip'); ?>",
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


    function edit_person(dk_id) {
        //Ajax Load data from ajax
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('editKip') ?>",
            data: {
                dk_id: dk_id
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

        $('.tombolTambah').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= site_url('formTmbKip') ?>",
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
    });

    $(document).ready(function() {

        $('#datarw').change(function() {

            var no_rw = $('#datarw').val();

            var action = 'get_rt';

            if (no_rw != '') {
                $.ajax({
                    url: "<?php echo base_url('action'); ?>",
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