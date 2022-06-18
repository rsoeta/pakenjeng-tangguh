<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            List Product
                            <a href="<?php echo base_url('product/create'); ?>" class="btn btn-primary float-right">Tambah</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Desa');
                                        echo form_dropdown('desa', $desas, $desa, ['class' => 'form-control', 'id' => 'desa']);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?php
                                        echo form_label('RW');
                                        echo form_dropdown('rw', $rws, $rw, ['class' => 'form-control', 'id' => 'rw']);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Keterangan');
                                        echo form_dropdown('keterangan', $categories, $keterangan, ['class' => 'form-control', 'id' => 'keterangan']);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Search');
                                        $form_keyword = [
                                            'type'  => 'text',
                                            'name'  => 'keyword',
                                            'id'    => 'keyword',
                                            'value' => $keyword,
                                            'class' => 'form-control',
                                            'placeholder' => 'Enter keyword ...'
                                        ];
                                        echo form_input($form_keyword);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hovered">
                                    <thead>
                                        <tr>
                                            <th>--</th>
                                            <th>No</th>
                                            <th>IDV</th>
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
                                        <?php $nomor = 0; ?>
                                        <?php foreach ($dtks as $key => $row) { ?>
                                            <tr>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="window.location='/dtks/vv06/redaktirovat/<?= $row['idv']; ?>'">
                                                        <i class="fas fa-align-left"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center"><?php echo ++$nomor; ?></td>
                                                <td><?php echo $row['idv']; ?></td>
                                                <td><?php echo $row['nik']; ?></td>
                                                <td><?php echo $row['nama']; ?></td>
                                                <td><?php echo $row['alamat']; ?></td>
                                                <td><?php echo $row['rt']; ?></td>
                                                <td><?php echo $row['rw']; ?></td>
                                                <td><?php echo $row['jenis_status']; ?></td>
                                                <td><?php echo $row['updated_at']; ?></td>
                                                <td><?php
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
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-3 float-right">
                                <div class="col-md-12">
                                    <?php echo $pager->links('paging_data', 'paging_data') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#desa").change(function() {
            filter();
        });
        $("#rw").change(function() {
            filter();
        });
        $("#keterangan").change(function() {
            filter();
        });
        $("#keyword").change(function() {
            filter();
        });
        // $("#keyword").keypress(function(event) {
        //     if (event.keyCode == 13) { // 13 adalah kode enter
        //         filter();
        //     }
        // });
        var filter = function() {
            var desa = $("#desa").val();
            var rw = $("#rw").val();
            var keterangan = $("#keterangan").val();
            var keyword = $("#keyword").val();
            window.location.replace("/dtks/vv06/table_dtks?desa=" + desa + "&rw=" + rw + "&keterangan=" + keterangan + "&keyword=" + keyword);
        }
    });
</script>

<!-- /.container-fluid -->

<?= $this->endSection(); ?>