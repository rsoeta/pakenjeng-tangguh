<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>



<div class="content-wrapper mt-1">

    <!-- Main content -->
    <section class="content">
        <div class="col-sm-6 col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-crosshairs"></i> Geotagging
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <input type="text" class="form-control" placeholder="latitude" spellcheck="false" data-ms-editor="true" id="latitude">
                            <!-- /input-group -->
                        </div>
                        <!-- /.col-lg-6 -->
                        <div class="col-6">
                            <input type="text" class="form-control" placeholder="longitude" spellcheck="false" data-ms-editor="true" id="longitude">
                            <!-- /input-group -->
                        </div>
                        <!-- /.col-lg-6 -->
                    </div>
                    <div class="d-grid gap-2 d-flex justify-content-end mt-2">
                        <button class="btn btn-danger me-2" type="button" onclick="location.reload()">Reset</button>
                        <button class="btn btn-primary" type="button" id="get-location">GetLocation</button>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
    </section>
</div>
<!-- /.container-fluid -->
<div class="viewmodal" style="display: none;"></div>
<script>
    // tambahkan event listener pada button get location
    $("#get-location").click(() => {
        // untuk memeriksa jika browser tidak support maka akan muncul alert
        if (!navigator.geolocation)
            return alert("Geolocation is not supported.");

        // jika browser support maka fungsi ini akan dijalankan
        navigator.geolocation.getCurrentPosition((position) => {
            // tambahkan callback untuk menampilkan latitude dan longitude
            // $("#latitude").html(`Latitude: ${position.coords.latitude}`);
            // $("#longitude").html(`Longitude: ${position.coords.longitude}`);
            $("#latitude").val(`${position.coords.latitude}`);
            $("#longitude").val(`${position.coords.longitude}`);
        });
    });
</script>

<?= $this->endSection(); ?>