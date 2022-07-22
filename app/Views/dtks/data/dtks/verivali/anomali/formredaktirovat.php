<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->

<div class="content-wrapper mt-1">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-sm-7">
                    <!-- general form elements -->
                    <!-- /.card -->
                    <!-- Horizontal Form -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <!-- <button type="button" class="btn btn-sm btn-secondary float-right" onclick="goBack()">
                                <i class="fa fa-backward"></i> Back
                            </button> -->
                            <h3 class="card-title"><?= $title; ?></h3>
                        </div>

                        <div class="card-body">
                            <div class="pesan" style="display: none;"></div>
                            <div class="form-group row nopadding" hidden>
                                <label class="col-4 col-sm-4 col-form-label" for="ids">ID Semesta</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="ids" id="ids" class="form-control form-control-sm" value="<?= $ids; ?>">
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="nik_perbaikan">NIK</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nik_perbaikan" id="nik_perbaikan" class="form-control form-control-sm" value="<?= $nik_perbaikan; ?>">
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="nkk">No. KK</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nkk" id="nkk" class="form-control form-control-sm" value="<?= $nkk; ?>">
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="nama">Nama</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="nama" id="nama" class="form-control form-control-sm" value="<?= $nama; ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="tmp_lahir">Tempat Lahir</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="tmp_lahir" id="tmp_lahir" class="form-control form-control-sm" value="<?= $tmp_lahir; ?>">
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="tgl_lahir">Tgl Lahir</label>
                                <div class="col-8 col-sm-8">
                                    <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control form-control-sm" value="<?= $tgl_lahir; ?>">
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="alamat">Alamat</label>
                                <div class="col-8 col-sm-8">
                                    <input type="text" name="alamat" id="alamat" class="form-control form-control-sm" value="<?= $alamat; ?>">
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="rt">No. RT</label>
                                <div class="col-8 col-sm-8">
                                    <select id="rt" name="rt" class="form-select form-select-sm" aria-label="Default select example">
                                        <option value="">-- Pilih RT --</option>
                                        <?php foreach ($datart as $row) { ?>
                                            <option value="<?= $row['rt'] ?>" <?php if ($rt == $row['rt']) echo 'selected'; ?>> <?php echo $row['rt']; ?></option>
                                        <?php } ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label class="col-4 col-sm-4 col-form-label" for="rw">No. RW</label>
                                <div class="col-8 col-sm-8">
                                    <select id="rw" name="rw" class="form-select form-select-sm" aria-label="Default select example">
                                        <option value="">-- Pilih RW --</option>
                                        <?php foreach ($datarw as $row) { ?>
                                            <option value="<?= $row['rw'] ?>" <?php if ($rw == $row['rw']) echo 'selected'; ?>> <?php echo $row['rw']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="status" class="col-4 col-sm-4 col-form-label">Status</label>
                                <div class="col-8 col-sm-8">
                                    <select id="status" name="status" class="form-select form-select-sm" aria-label="Default select example">
                                        <option value="">-- Pilih Status --</option>
                                        <?php foreach ($datastatus as $stat) { ?>
                                            <option value="<?= $stat['id_status'] ?>" <?php if ($status == $stat['id_status']) echo 'selected'; ?>> <?php echo $stat['jenis_status']; ?></option>
                                        <?php } ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="status" class="col-4 col-sm-4 col-form-label">Keterangan</label>
                                <div class="col-8 col-sm-8">
                                    <select id="keterangan" name="keterangan" class="form-select form-select-sm" aria-label="Default select example" disabled>
                                        <option value="">-- Pilih Keterangan --</option>
                                        <?php foreach ($dataketerangan as $ket) { ?>
                                            <option value="<?= $ket['id_ketvv']; ?>" <?php if ($keterangan == $ket['id_ketvv']) echo 'selected="selected"'; ?>> <?php echo $ket['jenis_keterangan']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php if (session()->get('role_id') <= 2) { ?>
                                <div class="form-group row nopadding">
                                    <label for="status" class="col-4 col-sm-4 col-form-label">Keterangan</label>
                                    <div class="col-8 col-sm-8">
                                        <select id="keterangan" name="keterangan" class="form-select form-select-sm" aria-label="Default select example">
                                            <option value="">-- Pilih Keterangan --</option>
                                            <?php foreach ($dataketerangan as $ket) { ?>
                                                <option value="<?= $ket['id_ketvv']; ?>" <?php if ($keterangan == $ket['id_ketvv']) echo 'selected="selected"'; ?>> <?php echo $ket['jenis_keterangan']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="modal-footer mt-3">
                                <button type="submit" class="btn btn-primary btn-block tombolSave">Update</button>
                            </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
</div>

<div class="viewmodal" style="display: none;"></div>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script>
    function goBack() {
        window.history.back();
    }

    $(document).ready(function() {

        $('.tombolSave').click(function(e) {
            e.preventDefault();

            let form = $('.formsimpan')[0];

            let data = new FormData(form);

            $.ajax({
                type: "post",
                url: "<?= site_url('dtks/vv06/updatedata'); ?>",
                data: data,
                dataType: "json",
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function(e) {
                    $('.tombolSave').html('<i class="fa fa-spin fa-spinner"></i>');
                    $('.tombolSave').prop('disabled', true);
                },
                complete: function() {
                    $('.tombolSave').html('Update');
                    $('.tombolSave').prop('disabled', true);
                },
                success: function(response) {
                    if (response.error) {
                        let dataError = response.error;
                        if (dataError.errorNik) {
                            $('.errorNik').html(dataError.errorNik).show();
                            $('#nik').addClass('is-invalid');
                        } else {
                            $('.errorNik').fadeOut();
                            $('#nik').removeClass('is-invalid');
                            $('#nik').addClass('is-valid');
                        }
                        if (dataError.errorNkk) {
                            $('.errorNkk').html(dataError.errorNkk).show();
                            $('#nkk').addClass('is-invalid');
                        } else {
                            $('.errorNkk').fadeOut();
                            $('#nkk').removeClass('is-invalid');
                            $('#nkk').addClass('is-valid');
                        }
                        if (dataError.errorNama) {
                            $('.errorNama').html(dataError.errorNama).show();
                            $('#nama').addClass('is-invalid');
                        } else {
                            $('.errorNama').fadeOut();
                            $('#nama').removeClass('is-invalid');
                            $('#nama').addClass('is-valid');
                        }
                        if (dataError.errorTgl_lahir) {
                            $('.errorTgl_lahir').html(dataError.errorTgl_lahir).show();
                            $('#tgl_lahir').addClass('is-invalid');
                        } else {
                            $('.errorTgl_lahir').fadeOut();
                            $('#tgl_lahir').removeClass('is-invalid');
                            $('#tgl_lahir').addClass('is-valid');
                        }
                        if (dataError.errorAlamat) {
                            $('.errorAlamat').html(dataError.errorAlamat).show();
                            $('#alamat').addClass('is-invalid');
                        } else {
                            $('.errorAlamat').fadeOut();
                            $('#alamat').removeClass('is-invalid');
                            $('#alamat').addClass('is-valid');
                        }
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            html: response.sukses,
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
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