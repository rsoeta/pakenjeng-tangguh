<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>DataTables</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">DataTables</li>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="row">
                        <div class="col-1">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAdd">
                                <i class="fa fa-plus fa-sm"></i>
                            </button>
                        </div>
                        <!-- <button class="btn btn-primary" data-toggle="modal" data-target="modalTambah">Add</button> -->
                        <h3 class="m-0 font-weight-bold text-primary"><?= $title; ?></h3>
                    </div>
                </div>
                <div class="card-body">
                    <br>
                    <div class="tengah">
                        <table class="table table-hover table-head-fixed" id="example2" width="100%" cellspacing="0">
                            <thead class=" text-primary">
                                <tr>
                                    <th>--</th>
                                    <th>NO</th>
                                    <th>NIK</th>
                                    <th>NAMA LENGKAP</th>
                                    <th>EMAIL</th>
                                    <th>STATUS</th>
                                    <th>LEVEL</th>
                                    <th>JABATAN</th>
                                    <th>USER IMAGE</th>
                                    <th>CREATED AT</th>
                                    <th>UPDATED AT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($users as $row) : ?>
                                    <tr>
                                        <td>--</td>
                                        <td scope="row"><?= $i; ?></td>
                                        <td><?= $row['nik']; ?></td>
                                        <td><?= strtoupper($row['fullname']); ?></td>
                                        <td><?= $row['email']; ?></td>
                                        <td><?= $row['status']; ?></td>
                                        <td><?= $row['level']; ?></td>
                                        <td><?= $row['jabatan']; ?></td>
                                        <td><?= $row['user_image']; ?></td>
                                        <td><?= $row['created_at']; ?></td>
                                        <td><?= $row['updated_at']; ?></td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php

                    ?>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

        <!-- /.container-fluid -->
    </section>
</div>
<!-- End of Main Content -->


<?= $this->endSection(); ?>

<!-- Modal -->
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    Add rows here
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>