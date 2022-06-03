<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper mt-2" style="min-height: 2009.89px;">
    <section class="content">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="/wilayah/datadusun" role="tab" aria-controls="nav-home" aria-selected="true">Home</a>
                <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="/wilayah/datarw" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</a>
                <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="/wilayah/datart" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">...</div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">...</div>
            <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div>
        </div>
        <!-- Main content -->
        <!-- Default box -->
        <?= $this->include('pbb/wilayah/datadusun'); ?>

    </section>
</div>

<?= $this->endsection(); ?>

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
                    url: "<?= site_url('pbb/wilayah/hapus') ?>",
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
</div>