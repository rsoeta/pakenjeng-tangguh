<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper mt-2" style="min-height: 2009.89px;">
    <!-- Main content -->
    <section class="content">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="dsn">Data Kepala Dusun</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="rt">Data Ketua RT</a>
            </li>
        </ul>
        <div class="card card-primary">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-sm btn-primary tombolAdd">
                            <i class="fa fa-plus"></i> Add Data
                        </button>
                    </div>

                    <form action="datarw" method="post">
                        <?= csrf_field(); ?>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari RW" name="carirw" autofocus>
                            <button class="btn btn-sm btn-primary" type="submit" name="tombolrw">Search</button>
                        </div>
                    </form>
                </div>
                <table class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Dusun</th>
                            <th>No RW</th>
                            <th>Nama RW</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1 + (($noHalaman - 1) * 10);
                        foreach ($datarw as $d) : ?>
                            <tr>
                                <td><?= $nomor++; ?></td>
                                <td><?= $d['id_dusun']; ?></td>
                                <td><?= $d['no_rw']; ?></td>
                                <td><?= $d['nama_rw']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="hapusrw('<?= $d['id'] ?>','<?= $d['nama_rw'] ?>')">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-1">
                    <?= $pager->links('paging', 'paging_data'); ?>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </section>
</div>

</section>
<!-- /.content -->

<div class="viewmodal" style="display: none;"></div>
<script>
    function hapusrw(id, nama_rw) {
        Swal.fire({
            title: 'Hapus data RW?',
            html: `Yakin akan menghapus data RW <strong>${nama_rw}</strong>?`,
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
                    url: "<?= site_url('dtks/wilayah/hapusrw') ?>",
                    data: {
                        id: id
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
                url: "<?= site_url('dtks/wilayah/formTambahrw') ?>",
                dataType: "json",
                success: function(response) {
                    if (response.data) {
                        $('.viewmodal').html(response.data).show();
                        $('#modaltambahrw').on('shown.bs.modal', function(event) {
                            $('#no_dusun').focus();
                        });
                        $('#modaltambahrw').modal('show');
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