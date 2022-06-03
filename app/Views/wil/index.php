<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper mt-2" style="min-height: 2009.89px;">
    <!-- Content Header (Page header) -->


    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card card-primary">
            <div class="card-header">
                <h5 class="card-title"><?= $title; ?></h5>
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
                <div class="row my-2">
                    <div class="col">
                        <?php
                        $user = session()->get('role_id');
                        $desa_id = session()->get('kode_desa');
                        $ops = session()->get('level');
                        ?>
                        <div class="row">
                            <div class="col-sm-1 col-3 mb-1">
                                <label for="namaProv" class="form-label">
                                    Provinsi
                                </label>
                            </div>
                            <div class="col-sm-2 col-9">
                                <select class="form-control form-control-sm" name="namaProv" id="namaProv">
                                    <option value="">-Pilih-</option>
                                    <?php foreach ($provinsi as $row) : ?>
                                        <option value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-1 col-3 mb-1">
                                <label for="namaKab" class="form-label">
                                    Kab/Kota
                                </label>
                            </div>
                            <div class="col-sm-2 col-9">
                                <select class="form-control form-control-sm" name="namaKab" id="namaKab">
                                    <option value="">-Kosong-</option>
                                </select>
                            </div>
                            <div class="col-sm-1 col-3 mb-1">
                                <label for="namaKec" class="form-label">
                                    Kecamatan
                                </label>
                            </div>
                            <div class="col-sm-2 col-9">
                                <select class="form-control form-control-sm" name="namaKec" id="namaKec">
                                    <option value="">-Kosong-</option>
                                </select>
                            </div>
                            <div class="col-sm-1 col-3 mb-1">
                                <label for="namaDesa" class="form-label">
                                    Desa/Kel.
                                </label>
                            </div>
                            <div class="col-sm-2 col-9">
                                <select class="form-control form-control-sm" name="namaDesa" id="namaDesa">
                                    <option value="">-Kosong-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="tabel_data" class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Provinsi</th>
                            <th>Nama Kab/Kota</th>
                            <th>Nama Kecamatan</th>
                            <th>Nama Desa/Kelurahan</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="viewmodal" style="display: none;"></div>
<script>
    table = $('#tabel_data').DataTable({
        'order': [],
        'fixedHeader': true,
        'searching': true,
        'paging': true,
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        'responsive': true,
        'processing': true,
        'serverSide': true,
        "ajax": {
            "url": "<?= site_url('dtks/wil/tabel_data'); ?>",
            "type": "POST",
            "data": {
                "csrf_test_name": $('input[name=csrf_test_name]').val()
            },
            "data": function(data) {
                data.csrf_test_name = $('input[name=csrf_test_name]').val();
                data.namaProv = $('#namaProv').val();
                data.namaKab = $('#namaKab').val();
                data.namaKec = $('#namaKec').val();
                data.namaDesa = $('#namaDesa').val();
            },
            "dataSrc": function(response) {
                $('input[name=csrf_test_name]').val(response.csrf_test_name);
                return response.data;
            }
        },

        "columnDefs": [{
            "targets": [0],
            "orderable": false
        }]
    });


    $('#namaProv').change(function() {
        table.draw();
    });
    $('#namaKab').change(function() {
        table.draw();
    });
    $('#namaKec').change(function() {
        table.draw();
    });
    $('#namaDesa').change(function() {
        table.draw();
    });

    // when country dropdown changes
    $('#namaProv').change(function() {
        var province_id = $(this).val();

        if (province_id) {

            $.ajax({
                type: "POST",
                url: "<?= base_url('getKab') ?>",
                data: {
                    province_id: province_id
                },
                success: function(res) {
                    var data = JSON.parse(res);
                    if (res) {

                        $("#namaKab").empty();
                        $("#namaKab").append('<option>-Pilih Kab/Kota-</option>');
                        $.each(data, function(key, value) {
                            $("#namaKab").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    } else {
                        $("#namaKab").empty();
                    }
                }
            });
        } else {

            $("#namaKab").empty();
            $("#namaKec").empty();
            $("#namaDesa").empty();
        }
    });

    // when state dropdown changes
    $('#namaKab').on('change', function() {

        var regency_id = $(this).val();

        if (regency_id) {

            $.ajax({
                type: "POST",
                url: "<?= base_url('getKec') ?>",
                data: {
                    regency_id: regency_id
                },
                success: function(res) {
                    var data = JSON.parse(res);
                    if (res) {
                        $("#namaKec").empty();
                        $("#namaKec").append('<option>-Pilih Kecamatan-</option>');
                        $.each(data, function(key, value) {
                            $("#namaKec").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    } else {
                        $("#namaKec").empty();
                    }
                }
            });
        } else {

            $("#namaKec").empty();
            $("#namaDesa").empty();
        }
    });

    // when state dropdown changes
    $('#namaKec').on('change', function() {

        var district_id = $(this).val();

        if (district_id) {

            $.ajax({
                type: "POST",
                url: "<?= base_url('getDesa') ?>",
                data: {
                    district_id: district_id
                },
                success: function(res) {
                    var data = JSON.parse(res);
                    if (res) {
                        $("#namaDesa").empty();
                        $("#namaDesa").append('<option>-Pilih Desa/Kel-</option>');
                        $.each(data, function(key, value) {
                            $("#namaDesa").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    } else {
                        $("#namaDesa").empty();
                    }
                }
            });
        } else {

            $("#namaDesa").empty();
        }
    });
    // $(document).ready(function() {

    //     $('#namaProv').change(function() {

    //         var province_id = $('#namaProv').val();
    //         var action = 'get_kab';
    //         if (province_id != '') {
    //             $.ajax({
    //                 url: "",
    //                 method: "POST",
    //                 data: {
    //                     province_id: province_id,
    //                     action: action
    //                 },
    //                 dataType: "JSON",
    //                 success: function(data) {
    //                     var html = '<option value="">-Pilih-</option>';
    //                     for (var count = 0; count < data.length; count++) {
    //                         html += '<option value="' + data[count].regency_id + '">' + data[count].name + '</option>';
    //                     }
    //                     $('#namaKab').html(html);
    //                 }
    //             });
    //         } else {
    //             $('#namaKab').val('');
    //         }
    //         $('#namaKab').val('');
    //     });

    //     $('#namaKab').change(function() {
    //         var regency_id = $('#namaKab').val();
    //         var action = 'get_kec';
    //         if (regency_id != '') {
    //             $.ajax({
    //                 url: "",
    //                 method: "POST",
    //                 data: {
    //                     regency_id: regency_id,
    //                     action: action
    //                 },
    //                 dataType: "JSON",
    //                 success: function(data) {
    //                     var html = '<option value="">-Pilih-</option>';
    //                     for (var count = 0; count < data.length; count++) {
    //                         html += '<option value="' + data[count].district_id + '">' + data[count].name + '</option>';
    //                     }
    //                     $('#namaKec').html(html);
    //                 }
    //             });
    //         } else {
    //             $('#namaKec').val('');
    //         }
    //         $('#namaKec').val('');
    //     });

    //     $('#namaKec').change(function() {

    //         var district_id = $('#namaKec').val();

    //         var action = 'get_desa';

    //         if (district_id != '') {
    //             $.ajax({
    //                 url: "",
    //                 method: "POST",
    //                 data: {
    //                     district_id: district_id,
    //                     action: action
    //                 },
    //                 dataType: "JSON",
    //                 success: function(data) {
    //                     var html = '<option value="">-Pilih-</option>';

    //                     for (var count = 0; count < data.length; count++) {
    //                         html += '<option value="' + data[count].village_id + '">' + data[count].name + '</option>';
    //                     }

    //                     $('#namaDesa').html(html);
    //                 }
    //             });
    //         } else {
    //             $('#namaDesa').val('');
    //         }

    //     });

    // });
</script>
<?= $this->endsection(); ?>