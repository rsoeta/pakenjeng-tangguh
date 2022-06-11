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
                        <?php if (session()->get('jabatan') < 7) { ?>
                            <a href="/dtks/usulan/create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></a>
                        <?php } ?>
                    </div>
                    <div class="card-header">
                        <h4 class="card-title" style="text-align: center;">Daftar Data Terpadu Kesejahteraan Sosial</h4>
                        <?php if (session()->getFlashdata('pesan')) : ?>
                            <div class="alert alert-success" role="alert">
                                <?= session()->getFlashdata('pesan');
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- /.card-header -->
                    <div class="card-body">

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
                                            <a href="/dtks/usulan/detail/<?= $row['id']; ?>">
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

<script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);
</script>

<?= $this->endSection(); ?>