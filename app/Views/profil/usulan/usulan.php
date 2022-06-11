<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- DataTales Example -->
            <div class="card shadow mb-4">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title" style="text-align: center;">Daftar Data Terpadu Kesejahteraan Sosial</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <a href="" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
                        <table id="example2" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>--</th>
                                    <th>No</th>
                                    <th>Nama Kepala RUTA</th>
                                    <th>Alamat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($dtks as $row) : ?>
                                    <tr>
                                        <td>
                                            <a href="/dtks/pages/detail/<?= $row['id']; ?>">
                                                <i class="fas fa-align-left"></i>
                                            </a>
                                        </td>
                                        <td scope=" row"><?= $i; ?>
                                        </td>
                                        <td><?= $row['nama_krt']; ?></td>
                                        <td><?= $row['alamat']; ?></td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
    </section>
</div>
<!-- /.container-fluid -->

<?= $this->endSection(); ?>