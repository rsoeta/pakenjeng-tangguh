<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper mt-2" style="min-height: 2009.89px;">
    <!-- Content Header (Page header) -->


    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card card-primary">
            <div class="card-header">
                <h5 class="card-title">Data Wilayah - Dusun</h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-sm btn-primary tombolAdd">
                            <i class="fa fa-plus"></i> Add Data
                        </button>
                    </div>

                    <form action="/wilayah/index" method="post">
                        <?= csrf_field(); ?>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari Dusun" name="caridusun" autofocus value="<?= $cari; ?>">
                            <button class="btn btn-sm btn-primary" type="submit" name="tomboldusun">Search</button>
                        </div>
                </div>
                </form>

                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Dusun</th>
                            <th>Nama Dusun</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1 + (($noHalaman - 1) * 5);
                        foreach ($datadusun as $d) : ?>
                            <tr>
                                <td><?= $nomor++; ?></td>
                                <td><?= $d['no_dusun']; ?></td>
                                <td><?= $d['nama_dusun']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="hapus('<?= $d['id'] ?>','<?= $d['nama_dusun'] ?>')">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="float-center">
                    <?= $pager->links('dusun', 'paging_data'); ?>
                </div>
            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Data Wilayah - RW</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-sm btn-primary">
                            <i class="fa fa-plus"></i> Add Data
                        </button>
                    </div>
                </div>
                Start creating your amazing application!
            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card card-yellow">
            <div class="card-header">
                <h3 class="card-title">Data Wilayah - RT</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="col">
                    <div class="row">
                        <button type="button" class="btn btn-sm btn-primary">
                            <i class="fa fa-plus"></i> Add Data
                        </button>
                    </div>
                </div>
                Start creating your amazing application!
            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->
</div>

<div class="viewmodal" style="display: none;"></div>
<script>
    function hapus(id, nama_dusun) {
        Swal.fire({
            title: 'Hapus Dusun?',
            html: `Yakin akan menghapus data dusun <strong>${nama_dusun}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Tidak!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "<?= site_url('wilayah/hapus') ?>",
                    data: {
                        id_dusun: id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            window.location.reload();
                        }
                    }
                });
            }
        })
    }
    $(document).ready(function() {
        $('.tombolAdd').click(function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= site_url('wilayah/formTambahDusun') ?>",
                dataType: "json",
                success: function(response) {
                    if (response.data) {
                        $('.viewmodal').html(response.data).show();
                        $('#modaltambahdusun').on('shown.bs.modal', function(event) {
                            $('#no_dusun').focus();
                        });
                        $('#modaltambahdusun').modal('show');
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });
    });
</script>
<?= $this->endsection(); ?>