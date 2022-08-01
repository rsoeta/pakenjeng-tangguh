<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dtks/pages'); ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?= $title; ?></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- DataTales Example -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="row">
                        <div class="col">
                            <!-- Button trigger modal -->
                            <h4 class="text-center"><?= $title; ?></h4>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-tool float-right" data-toggle="modal" data-target="#modalAdd">
                                    <i class="fa fa-plus fa-sm"></i> Tambah User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <br>
                    <div class="tengah">
                        <table id="tabelUser" class="table table-sm table-hover table-head-fixed compact" style="width: 100%;">
                            <thead class="text-primary">
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA LENGKAP</th>
                                    <th>NAMA DESA</th>
                                    <th>NO. RW</th>
                                    <th>NIK</th>
                                    <th>EMAIL</th>
                                    <th>LEVEL</th>
                                    <th>USER IMAGE</th>
                                    <th>DIBUAT PADA</th>
                                    <th>STATUS</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($users as $row) : ?>
                                    <tr>
                                        <td scope="row"><?= $i; ?></td>
                                        <td><?= $row['fullname']; ?></td>
                                        <td><?= $row['nama_desa']; ?></td>
                                        <td><?= $row['level']; ?></td>
                                        <td><?= $row['nik']; ?></td>
                                        <td><?= $row['email']; ?></td>
                                        <td>
                                            <?php if ($row['role_id'] == 1) {
                                                $badges = 'bg-danger';
                                            } elseif ($row['role_id'] == 2) {
                                                $badges = 'bg-primary';
                                            } elseif ($row['role_id'] == 3) {
                                                $badges = 'bg-success';
                                            } elseif ($row['role_id'] == 4) {
                                                $badges = 'bg-warning';
                                            } elseif ($row['role_id'] == 6) {
                                                $badges = 'bg-info';
                                            } else {
                                                $badges = 'bg-secondary';
                                            }
                                            ?>
                                            <?php foreach ($roles as $role) { ?>
                                                <?php if ($role['id_role'] == $row['role_id']) {
                                                    echo '<span class="badge ' . $badges . '">' . $role['nm_role'] . '</span>';
                                                } ?>
                                            <?php } ?>
                                        </td>
                                        <td><a href="<?= Foto_Profil($row['user_image'], 'profil'); ?>" data-lightbox="<?= $row['fullname']; ?>" data-title="<?= $row['fullname']; ?>"><img src="<?= Foto_Profil($row['user_image'], 'profil'); ?>" alt="" style="border: 2px solid #ddd; border-radius: 5px; padding: 1px; width: 30px;"></a></td>
                                        <td><?= $row['created_at']; ?></td>
                                        <td>
                                            <?php $status = $row['status'] ?>
                                            <?php if ($status == 1) { ?>
                                                <a href="/update_status/<?php echo $row['id']; ?>/<?php echo $row['status']; ?>" class="btn btn-warning btn-sm rounded-pill">Active</a>
                                                <!-- In these as we are creating an attribute and passing the values -->
                                            <?php } else { ?>
                                                <a href="/update_status/<?php echo $row['id']; ?>/<?php echo $row['status']; ?>" class="btn btn-dark btn-sm rounded-pill">Inactive</a>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="view('<?= $row['id']; ?>')">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapus('<?= $row['id']; ?>','<?= $row['fullname']; ?>')">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- End of Main Content -->

<div class="viewmodal" style="display: none;"></div>
<script>
    $(document).ready(function() {
        $('#tabelUser').DataTable({
            responsive: true
        });

        // $('body').addClass('sidebar-collapse');

        $('.tombolTambah').click(function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= base_url('user/formTambah'); ?>",
                dataType: "json",
                type: "post",
                data: {
                    aksi: 0
                },
                success: function(response) {
                    if (response.data) {
                        $('.viewmodal').html(response.data).show();
                        $('#modalTambahUser').on('shown.bs.modal', function(event) {
                            // do something...
                            $('#firstname').focus();
                        });
                        $('#modalTambahUser').modal('show');
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });

        lightbox.option({
            'resizeDuration': 110,
            'wrapAround': true,
            'disableScrolling': true,
            'fitImagesInViewport': true,
            'maxWidth': 800,
            'maxHeight': 800,
        })
    });

    function hapus(id, fullname) {
        tanya = confirm(`Anda yakin akan Menghapus ${fullname}?`);
        if (tanya == true) {
            $.ajax({
                type: "post",
                url: "<?= base_url('hapus'); ?>",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    if (response.sukses) {
                        window.location.reload();
                    }
                }
            });
        }
    }

    function view(id) {
        $.ajax({
            type: "post",
            url: "<?= base_url("formview"); ?>",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {
                if (response.sukses) {
                    $('.viewmodal').html(response.sukses).show();
                    $('#modalview').modal('show');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    $('document').ready(function() {
        var pwd1 = $("#password1");
        var pwd2 = $("#password2");
        $('#checkbox').click(function() {
            if (pwd1.attr('type') === "password" && pwd2.attr('type') === "password") {
                pwd1.attr('type', 'text') && pwd2.attr('type', 'text');
            } else {
                pwd1.attr('type', 'password') && pwd2.attr('type', 'password');
            }
        });

        if ($('#countdown').length) {
            start_countdown();
        }

        // #kecamatan disable false on submit
        $('#formTambahUser').click(function(e) {
            e.preventDefault();
            $('#kecamatan').prop('disabled', false);
            $('#mainform').submit();
        });
    });
</script>

<!-- Modal -->
<div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form. <?= $title1; ?></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form action="/user_tambah" method="POST" id="mainform">
                        <?= csrf_field(); ?>
                        <div class="form-group my-1">
                            <input type="text" class="form-control form-control form-control-user" name="fullname" aria-describedby="emailHelp" placeholder="Masukan Nama Lengkap" value="<?= set_value('fullname'); ?>">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group my-1">
                                    <input type="numeric" class="form-control form-control form-control-user" name="nik" aria-describedby="emailHelp" placeholder="Masukan No. KTP/NIK" value="<?= set_value('nik'); ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group my-1">
                                    <input type="numeric" class="form-control form-control form-control-user" name="nope" aria-describedby="emailHelp" placeholder="Masukan No. Handphone" value="<?= set_value('nope'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group my-1">
                            <input type="email" class="form-control form-control form-control-user" name="email" aria-describedby="emailHelp" placeholder="Masukan Email" value="<?= set_value('email'); ?>">
                        </div>
                        <div class="form-group my-1">
                            <select id="kecamatan" name="kecamatan" class="form-control form-control form-control-user" disabled="true">
                                <option value="">-- Pilih Kecamatan --</option>
                                <?php foreach ($kecamatan as $row) { ?>
                                    <option <?= $kode_kec == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id'] ?>" <?= set_select('kecamatan', $row['id']); ?>> <?php echo $row['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group my-1">
                            <select id="kelurahan" name="kelurahan" class="form-control form-control form-control-user">
                                <option value="">-- Pilih Desa / Kelurahan --</option>
                                <?php foreach ($desa as $row) { ?>
                                    <option value="<?= $row['id'] ?>" <?= set_select('kelurahan', $row['id']); ?>> <?php echo $row['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group my-1">
                            <select id="no_rw" name="no_rw" class="form-control form-control form-control-user">
                                <option value="">-- Pilih RW --</option>
                                <?php foreach ($datarw as $row) { ?>
                                    <option value="<?= $row['no_rw'] ?>" <?= set_select('no_rw', $row['no_rw']); ?>> <?php echo $row['no_rw']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group my-1">
                                    <input type="password" class="form-control form-control form-control-user" name="password" placeholder="Password" id="password1" value="<?= set_value('password'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-1">
                                    <input type="password" class="form-control form-control form-control-user" name="password_confirm" placeholder="Password confirm" id="password2" value="<?= set_value('password_confirm'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group my-1">
                            <div class="custom-control custom-checkbox small">
                                <input type="checkbox" class="custom-control-input" id="checkbox">
                                <label class="custom-control-label" for="checkbox"> Tampilkan kata sandi</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="formTambahUser" type="submit" class="btn btn-primary btn-block">
                                <?= $title1; ?>
                            </button>
                        </div>
                        <hr>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>