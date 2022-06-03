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
                        <div class="col-md-2 col-12">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#modalAdd">
                                <i class="fa fa-plus fa-sm"></i> Tambah User
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <br>
                    <div class="tengah">
                        <table id="tabelUser" class="table table-hover table-head-fixed compact">
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
                                        <td><?= $row['user_image']; ?></td>
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
                                            <button type="button" class="btn" onclick="view('<?= $row['id']; ?>')">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn" onclick="hapus('<?= $row['id']; ?>','<?= $row['fullname']; ?>')">
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
</script>

<?= $this->endSection(); ?>