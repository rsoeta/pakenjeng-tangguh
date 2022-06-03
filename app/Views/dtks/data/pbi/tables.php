<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <h3 class="card-title text-center"><strong>Daftar Data Terpadu Kesejahteraan Sosial</strong></h3>
                </div>
            </div>
            <?php if (session()->get('jabatan') == 0) { ?>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <a href="" class="btn btn-primary btn-block"><i class="fas fa-plus"></i> Add</a>
                    </div>
                </div>
            <?php } ?>
            <table id="example2" class="table table-hover table-sm mt-2">
                <thead>
                    <tr>
                        <th>--</th>
                        <th>No</th>
                        <th>Nama Kepala RUTA</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <!-- /.col -->
    </section>
</div>
<!-- /.container-fluid -->
<script>
    function dataverivali06() {
        $.ajax({
            type: "post",
            url: "<?= site_url('Vv06/tabel_data'); ?>",
            dataType: "json",
            success: function(response) {
                $('.viewdata').html(response.data);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }
    $(document).ready(function() {
        dataverivali06();
    });
</script>

<?= $this->endSection(); ?>