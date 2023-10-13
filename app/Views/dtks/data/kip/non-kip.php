<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    #bg-orange {
        background-color: orange;
    }

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
        background-color: var(--purple);
        border: none;
        color: var(--primary);
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
<?php
$user = session()->get('role_id');
$nik = session()->get('nik');
$jabatan = session()->get('level');
$desa_id = session()->get('kode_desa');
?>

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

        <!-- <div class="container-fluid"> -->
        <div class="card-body">
            <div class="row">
                <div class="col-6 col-sm-4 mb-2">
                    <button type="button" class="btn add-button tombolTambah">
                        <span class="plus-icon"><i class="fas fa-user-plus fa-sm" style="color: white;"></i></span>
                        <!-- <i class="fa fa-plus"></i> Tambah Data -->
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
                    <div class="col-sm-2 col-12 mb-2">
                        <select <?php echo $user > 2 ? 'readonly="readonly" tabindex="-1" aria-disabled="true"' : ''; ?> id="nama_sekolah" name="nama_sekolah" class="form-select form-select-sm">
                            <option value="">[ Semua Sekolah ]</option>
                            <?php if ($nama_sekolah !== "") { ?>
                                <?php foreach ($nama_sekolah as $row) { ?>
                                    <option <?php echo $jabatan == $row['opr_sch'] ? 'selected' : ''; ?> value="<?= $row['opr_sch']; ?>"><?= $row['opr_sch']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        </select>
                    </div>
                    <div class="col-sm-2 col-6 mb-2">
                        <select <?= $user >= 4 ? 'disabled = "true"' : ''; ?> class="form-control form-control-sm" name="jenjang" id="jenjang">
                            <option value="">[ Semua Jenjang ]</option>
                            <?php foreach ($jenjang_sekolah as $row) { ?>
                                <option value="<?= $row['sj_id']; ?>"><?= $row['sj_nama']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-1 col-6 mb-2">
                        <select class="form-control form-control-sm" name="kelas" id="kelas">
                            <option value="">[ Semua Kelas ]</option>
                            <?php foreach ($kelas_sekolah as $row) { ?>
                                <option value="<?= $row['dk_kelas']; ?>"><?= $row['dk_kelas']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <br>
                    <table class="table" id="tabel_data" style="width: 100%;">
                        <thead class="text-white bg-gradient-purple">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th>KKS</th>
                                <th>Alamat</th>
                                <th>No. RT</th>
                                <th>No. RW</th>
                                <th>Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- </div> -->
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
            "url": "<?= site_url('tabel_nonkip'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.desa = $('#desa').val();
                data.no_rw = $('#rw').val();
                data.no_rt = $('#rt').val();
                // data.nama_sekolah = $('#nama_sekolah').val();
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
    $('#rw').change(function() {
        table.draw();
    });
    $('#rt').change(function() {
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

    $(document).on('click', '#deleteBtn', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        // alert(id);
        // $('.editIndividu').modal('show');
        tanya = confirm(`Hapus data ${nama}?`);
        if (tanya == true) {
            $.ajax({
                type: "post",
                url: "<?= base_url('dltNonKip'); ?>",
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
            url: "<?php echo site_url('editNonKip') ?>",
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
                url: "<?= site_url('formTmbNonKip') ?>",
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

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);

    function previewImgId() {
        const dk_foto_identitas = document.querySelector('#dk_foto_identitas');
        const imgPreview = document.querySelector('.img-preview-id');


        const fileIdentitas = new FileReader();
        fileIdentitas.readAsDataURL(dk_foto_identitas.files[0]);

        fileIdentitas.onload = function(e) {
            imgPreview.src = e.target.result;
        }
    }
</script>

<?= $this->endSection(); ?>