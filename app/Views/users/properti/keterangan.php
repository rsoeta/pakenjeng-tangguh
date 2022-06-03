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
                <button href="" class="btn btn-primary tombolTambah"><i class="fas fa-plus-circle"></i> Add</button>
            <?php } ?>
            <br><br>
            <form method="post" action="/dtks/pages/keterangan">
                <?= csrf_field(); ?>
                <div class="input-group mb-3 col-12 col-sm-6 float-right">
                    <input type="text" class="form-control" placeholder="Cari Keterangan" name="cariketerangan" value="<?= $cari; ?>" aria-label="Cari Keterangan" aria-describedby="button-addon2">
                    <button class="btn btn-primary" type="submit" name="tombolcari" id="button-addon2">Cari</button>
                </div>
            </form>

            <table id="" class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Keterangan</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1 + (($nohalaman - 1) * 5); ?>
                    <?php foreach ($keterangan as $row) : ?>
                        <tr>
                            <td scope=" row"><?= $i; ?></td>
                            <td><?= $row['jenis_keterangan']; ?></td>
                            <td>
                                <button type="button" class="btn btn-danger" onclick="hapus('<?= $row['id_ketvv']; ?>','<?= $row['jenis_keterangan']; ?>')">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <button type="button" class="btn btn-info" title="Edit Keterangan" onclick="edit('<?= $row['id_ketvv'] ?>')">
                                    <i class="fa fa-pencil-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                <?php echo $pager->links('paging_data', 'paging_data'); ?>
            </div>
        </div>
        <!-- /.col -->
    </section>
</div>
<!-- /.container-fluid -->

<div class="viewmodal" style="display: none;"></div>
<script>
    function hapus(id, jenis) {
        Swal.fire({
            title: 'Hapus Keterangan',
            html: `Yakin hapus Jenis Keterangan <strong>"${jenis}"</strong> ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Tidak!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "<?= site_url('dtks/pages/hapus'); ?>",
                    data: {
                        idket: id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.sukses) {
                            window.location.reload();
                        }

                    }
                });
            }
        })
    }

    function edit(id) {
        $.ajax({
            type: "post",
            url: "<?= site_url('dtks/pages/formEdit') ?>",
            data: {
                idket: id
            },
            dataType: "json",
            success: function(response) {
                if (response.data) {
                    $('.viewmodal').html(response.data).show();
                    $('#modalformedit').on('shown.bs.modal', function(event) {
                        $('#jenis_keterangan').focus();
                    });
                    $('#modalformedit').modal('show');
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }

    $(document).ready(function() {
        $('.tombolTambah').click(function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= site_url('dtks/pages/formTambahKeterangan'); ?>",
                dataType: "json",
                success: function(response) {
                    if (response.data) {
                        $('.viewmodal').html(response.data).show();
                        $('#modaltambahketerangan').on('shown.bs.modal', function(event) {
                            $('#jenisketerangan').focus();
                        });
                        $('#modaltambahketerangan').modal('show');
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });
    });
</script>

<?= $this->endSection(); ?>