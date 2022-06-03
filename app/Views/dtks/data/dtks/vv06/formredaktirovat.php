<?= $this->extend('dtks/templates/index'); ?>

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

                            <?= form_open_multipart('', ['class' => 'formsimpan']) ?>
                            <?= csrf_field(); ?>
                            <?php if (session()->get('jabatan') == 0) {
                            ?>
                                <div class="form-group row nopadding">
                                    <label for="idv" class="col-4 col-sm-4 col-lg-2 col-form-label">No</label>
                                    <div class="col-8 col-sm-8 col-lg-10">
                                        <input type="text" class="form-control form-control-sm" id="idv" name="idv" readonly placeholder="ID" value="<?= $idv; ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group row nopadding">
                                    <label for="ids" class="col-4 col-sm-4 col-lg-2 col-form-label">IDS</label>
                                    <div class="col-8 col-sm-8 col-lg-10">
                                        <input type="text" class="form-control form-control-sm" id="ids" name="ids" readonly placeholder="ID" value="<?= $ids; ?>" readonly>
                                    </div>
                                </div>
                            <?php } else if (session()->get('jabatan') > 0) {
                            ?>
                                <div class="form-group row nopadding">
                                    <label for="idv" class="col-4 col-sm-4 col-lg-2 col-form-label" hidden>No</label>
                                    <div class="col-8 col-sm-8 col-lg-10">
                                        <input type="text" class="form-control form-control-sm" id="idv" name="idv" readonly placeholder="ID" value="<?= $idv; ?>" hidden>
                                    </div>
                                </div>
                                <div class="form-group row nopadding">
                                    <label for="ids" class="col-4 col-sm-4 col-lg-2 col-form-label" hidden>IDS</label>
                                    <div class="col-8 col-sm-8 col-lg-10">
                                        <input type="text" class="form-control form-control-sm" id="ids" name="ids" readonly placeholder="ID" value="<?= $ids; ?>" hidden>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="form-group row nopadding">
                                <label for="nik" class="col-4 col-sm-4 col-lg-2 col-form-label">NIK</label>
                                <div class="col-8 col-sm-8 col-lg-10">
                                    <input type="text" class="form-control form-control-sm" id="nik" name="nik" value="<?= $nik; ?>">
                                    <div class="errorNik" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="nkk" class="col-4 col-sm-4 col-lg-2 col-form-label">No. KK</label>
                                <div class="col-8 col-sm-8 col-lg-10">
                                    <input type="text" class="form-control form-control-sm" id="nkk" name="nkk" value="<?= $nkk; ?>">
                                    <div class="errorNkk" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="nama" class="col-4 col-sm-4 col-lg-2 col-form-label">Nama</label>
                                <div class="col-8 col-sm-8 col-lg-10">
                                    <input type="text" class="form-control form-control-sm" id="nama" name="nama" value="<?= $nama; ?>" readonly>
                                    <div class="errorNama" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="tgl_lahir" class="col-4 col-lg-2 col-form-label">Tgl Lahir</label>
                                <div class="col-8 col-lg-10 input-group date" data-target-input="nearest">
                                    <input type="date" class="form-control form-control-sm" id="tgl_lahir" name="tgl_lahir" value="<?= $tgl_lahir; ?>">
                                    <div class="errorTgl_lahir" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="alamat" class="col-4 col-lg-2 col-form-label">Alamat</label>
                                <div class="col-8 col-lg-10">
                                    <input type="text" class="form-control form-control-sm" id="alamat" name="alamat" value="<?= $alamat; ?>">
                                    <div class="errorAlamat" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="rt" class="col-4 col-lg-2 col-form-label">No. RT</label>
                                <div class="col-8 col-lg-10">
                                    <select class="custom-select form-control-sm" name="id_rt" id="id_rt">
                                        <?php
                                        foreach ($datart as $k) :
                                            if ($k['id_rt'] == $pilihrt) :
                                                echo "<option value=\"$k[id_rt]\" selected>$k[id_rt]</option>";
                                            else :
                                                echo "<option value=\"$k[id_rt]\">$k[id_rt]</option>";
                                            endif;
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="rw" class="col-4 col-lg-2 col-form-label">No. RW</label>
                                <div class="col-8 col-lg-10">
                                    <select class="custom-select form-control-sm" name="no_rw" id="no_rw">
                                        <?php
                                        foreach ($datarw as $i) :
                                            if ($i['no_rw'] == $pilihrw) :
                                                echo "<option value=\"$i[no_rw]\" selected>$i[no_rw]</option>";
                                            else :
                                                echo "<option value=\"$i[no_rw]\">$i[no_rw]</option>";
                                            endif;
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="status" class="col-4 col-lg-2 col-form-label">Status</label>
                                <div class="col-8 col-lg-10">
                                    <select class="custom-select form-control-sm" name="status" id="status">
                                        <?php
                                        foreach ($datastatus as $j) :
                                            if ($j['id_status'] == $status) :
                                                echo "<option value=\"$j[id_status]\" selected>$j[jenis_status]</option>";
                                            else :
                                                echo "<option value=\"$j[id_status]\">$j[jenis_status]</option>";
                                            endif;
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="ket_vv" class="col-4 col-lg-2 col-form-label">Ket Verivali</label>
                                <div class="col-8 col-lg-10">
                                    <select class="custom-select form-control-sm " name="ket_vv" id="ket_vv" disabled>
                                        <?php
                                        foreach ($dataketvv as $m) :
                                            if ($m['id_ketvv'] == $ket_vv) :
                                                echo "<option value=\"$m[id_ketvv]\" selected>$m[jenis_keterangan]</option>";
                                            else :
                                                echo "<option value=\"$m[id_ketvv]\">$m[jenis_keterangan]</option>";
                                            endif;
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row nopadding">
                                <label for="status" class="col-4 col-lg-2 col-form-label">Foto Ket.</label>
                                <div class="col-8 col-lg-10">
                                    <img src="<?= base_url(); ?>/img/vv06/<?php echo $nik; ?>.png" style="width: 100%;" class="img-thumnail">
                                    <div class="errorTgl_lahir" style="display: none;"></div>
                                </div>
                            </div>
                            <?php if (session()->get('jabatan') == 0) { ?>
                                <div class="form-group row nopadding">
                                    <label for="ket_vv" class="col-4 col-lg-2 col-form-label">Ket Verivali</label>
                                    <div class="col-8 col-lg-10">
                                        <select class="custom-select form-control-sm" name="ket_vv" id="ket_vv">
                                            <?php
                                            foreach ($dataketvv as $m) :
                                                if ($m['id_ketvv'] == $ket_vv) :
                                                    echo "<option value=\"$m[id_ketvv]\" selected>$m[jenis_keterangan]</option>";
                                                else :
                                                    echo "<option value=\"$m[id_ketvv]\">$m[jenis_keterangan]</option>";
                                                endif;
                                            endforeach;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                            <br>
                            <?php if (session()->get('jabatan') == 0) { ?>
                                <div class="form-group row">
                                    <label class="col-4 col-lg-2 col-form-label"></label>
                                    <div class="col-8 col-lg-10">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                        <button type="submit" class="btn btn-success float-right tombolSave">Update</button>
                                    <?php } else if (session()->get('jabatan') > 0) { ?>
                                        <button type="submit" class="btn btn-success float-right tombolSave">Update</button>
                                    <?php } ?>
                                    <br>
                                    <hr>
                                    </div>
                                </div>
                                <?= form_close(); ?>
                                <div class="form-group row">
                                    <div class="col-6 col-lg-6">
                                        <a type="button" class="btn btn-warning" href="/dtks/vv06">Kembali ke daftar VeriVali</a>
                                    </div>
                                    <div class="col-6 col-lg-6">
                                        <a type="button" class="btn btn-danger float-right" href="/dtks/vv06/invalid">Kembali ke daftar Invalid</a>
                                    </div>
                                </div>
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