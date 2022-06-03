<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <h3 class="card-title" style="text-align: center;"><?= $title; ?></h3>
            <br><br>
            <?php if (session()->get('jabatan') == 0) { ?>
                <a href="" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add</a>
            <?php } ?>
            <table id="example2" class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>--</th>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($dtks as $row) : ?>
                        
                        <tr>
                            <td>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="window.location='/dtks/vv06/redaktirovat/<?= $row['idv']; ?>'">
                                    <i class="fas fa-align-left"></i>
                                </button>
                            </td>
                            <td scope=" row"><?= $i; ?>
                            </td>
                            <td><?= $row['nama']; ?></td>
                            <td><?= $row['alamat']; ?></td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- /.col -->
    </section>
</div>
<!-- /.container-fluid -->

<?= $this->endSection(); ?>