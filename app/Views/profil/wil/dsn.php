<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper mt-2" style="min-height: 2009.89px;">

    <!-- Main content -->
    <section class="content">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="rw">Data Ketua RW</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="rt">Data Ketua RT</a>
            </li>
        </ul>
        <!-- Default box -->
        <div class="card card-primary">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-sm btn-primary tombolAdd">
                            <i class="fa fa-plus"></i> Add Data
                        </button>
                    </div>

                    <form action="datadusun" method="post">
                        <?= csrf_field(); ?>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari Dusun" name="caridusun" autofocus>
                            <button class="btn btn-sm btn-primary" type="submit" name="tomboldusun">Search</button>
                        </div>
                    </form>
                </div>

                <table class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Dusun</th>
                            <th>Nama Dusun</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1 + (($noHalaman - 1) * 10);
                        foreach ($datadusun as $d) : ?>
                            <tr>
                                <td><?= $nomor++; ?></td>
                                <td><?= $d['no_dusun']; ?></td>
                                <td><?= $d['nama_dusun']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="hapusDusun('<?= $d['id'] ?>','<?= $d['nama_dusun'] ?>')">
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

<div class="viewmodal" style="display: none;"></div>
<script>
    function hapusDusun(id, nama_dusun) {
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
                    url: "<?= site_url('dtks/wil/hapusdusun') ?>",
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
                url: "<?= site_url('dtks/wil/formtambahdusun') ?>",
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