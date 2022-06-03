<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card text-center">
                <div class="card-header">
                    <strong><?= $title; ?></strong>
                </div>
            </div>
            <?php
            $user = session()->get('role_id');
            $desa_id = session()->get('kode_desa');
            $ops = session()->get('level');
            ?>
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-block btn-outline-primary float-right mb-2 shadow tombolTambah">
                        <i class="fa fa-plus fa-sm"></i> Tambah Data
                    </button>
                    <table id="tabel_data" class="table table-hover compact">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Keterangan</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($verivali_pbi as $row) : ?>
                                <tr>
                                    <td scope="row"><?= $i; ?></td>
                                    <td><?= $row['vp_keterangan']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-group-sm" onclick="view('<?= $row['vp_id']; ?>')">
                                            <i class="fa fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn btn-group-sm" onclick="hapus('<?= $row['vp_id']; ?>','<?= $row['vp_keterangan']; ?>')">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.container-fluid -->

<div class="viewmodal" style="display: none;"></div>
<script>
    $(document).ready(function() {
        $('#tabel_data').DataTable({
            responsive: true,
            compact: true
        });

        // $('body').addClass('sidebar-collapse');

        $('.tombolTambah').click(function(e) {
            e.preventDefault();

            $.ajax({
                url: "<?= base_url('/formTambahKetVvPbi'); ?>",
                dataType: "json",
                type: "post",
                data: {
                    aksi: 0
                },
                success: function(response) {
                    if (response.data) {
                        $('.viewmodal').html(response.data).show();
                        $('#modaltambah').on('shown.bs.modal', function(event) {
                            // do something...
                        });
                        $('#modaltambah').modal('show');
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
        });
    });

    function hapus(vp_id, vp_keterangan) {
        tanya = confirm(`Hapus ${vp_keterangan}?`);
        if (tanya == true) {
            $.ajax({
                type: "post",
                url: "<?= base_url('/hapusKetVvPbi'); ?>",
                data: {
                    vp_id: vp_id
                },
                dataType: "json",
                success: function(response) {
                    if (response.sukses) {
                        window.location.reload();
                    }
                }
            });
        }
    }

    function view(vp_id) {
        $.ajax({
            type: "post",
            url: "<?= base_url("/viewKetVvPbi"); ?>",
            data: {
                vp_id: vp_id
            },
            dataType: "json",
            success: function(response) {
                if (response.sukses) {
                    $('.viewmodal').html(response.sukses).show();
                    $('#modaledit').modal('show');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }
</script>

<?= $this->endSection(); ?>