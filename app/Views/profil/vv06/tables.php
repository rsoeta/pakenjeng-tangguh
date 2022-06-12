<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <h3 class="card-title text-center" style="text-align: center;"><?= $title; ?></h3>
            <br><br>

            <?php if (session()->get('jabatan') == 0) { ?>
                <a href="" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
                <br><br>
                <table id="example2" class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>--</th>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>RT</th>
                            <th>RW</th>
                            <th>Status</th>
                            <th>Updated at</th>
                            <th>Keterangan</th>

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
                                <td scope="row"><?= $i; ?></td>
                                <td><?= $row['nik']; ?></td>
                                <td><?= $row['nama']; ?></td>
                                <td><?= $row['alamat']; ?></td>
                                <td><?= $row['rt']; ?></td>
                                <td><?= $row['rw']; ?></td>
                                <td><?php echo $row['jenis_status']; ?></td>
                                <td><?php echo $row['updated_at']; ?></td>
                                <td>
                                    <?php
                                    if ($row['id_ketvv'] == 1) { ?>
                                        <span class="badge badge-danger"><?php echo $row['jenis_keterangan']; ?></span>
                                    <?php } else if ($row['id_ketvv'] == 2) { ?>
                                        <span class="badge badge-warning"><?php echo $row['jenis_keterangan']; ?></span>
                                    <?php } else if ($row['id_ketvv'] == 3) { ?>
                                        <span class="badge badge-success"><?php echo $row['jenis_keterangan']; ?></span>
                                    <?php } else if ($row['id_ketvv'] == 4 || 5 || 7) { ?>
                                        <span class="badge badge-dark"><?php echo $row['jenis_keterangan']; ?></span>
                                    <?php } else { ?>
                                        <span class="badge badge-info"><?php echo $row['jenis_keterangan']; ?></span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php } else if (session()->get('jabatan') >= 1) { ?>

            <table id="example2" class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>--</th>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>RT</th>
                        <th>Updated at</th>
                        <th>Keterangan</th>

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
                            <td scope="row"><?= $i; ?></td>
                            <td><?= $row['nama']; ?></td>
                            <td><?= $row['alamat']; ?></td>
                            <td><?= $row['rt']; ?></td>
                            <td><?php echo $row['updated_at']; ?></td>
                            <td>
                                <?php
                                if ($row['id_ketvv'] == 1) { ?>
                                    <span class="badge badge-danger"><?php echo $row['jenis_keterangan']; ?></span>
                                <?php } else if ($row['id_ketvv'] == 2) { ?>
                                    <span class="badge badge-warning"><?php echo $row['jenis_keterangan']; ?></span>
                                <?php } else if ($row['id_ketvv'] == 3) { ?>
                                    <span class="badge badge-success"><?php echo $row['jenis_keterangan']; ?></span>
                                <?php } else if ($row['id_ketvv'] == 4 || 5 || 7) { ?>
                                    <span class="badge badge-dark"><?php echo $row['jenis_keterangan']; ?></span>
                                <?php } else { ?>
                                    <span class="badge badge-info"><?php echo $row['jenis_keterangan']; ?></span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php } ?>
        </div>
        <!-- /.col -->
    </section>
</div>
<!-- /.container-fluid -->



<?= $this->endSection(); ?>