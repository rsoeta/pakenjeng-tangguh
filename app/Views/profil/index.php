<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?= $title; ?></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h5 style="justify-content: center; text-align: center;" class="card-title">Profil User</h5>
                        </div>
                        <div class="card-body box-profile">

                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" src="<?= Foto_Profil($user_login['user_image'], 'profil'); ?>" alt="<?= $user_login['fullname']; ?> profile picture">
                            </div>

                            <h3 class="profile-username text-center"><?= ucwords(strtolower($user_login['fullname'])); ?></h3>

                            <p class="text-muted text-center"><?= ($user_login['nm_role']); ?></p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item"><i class="fas fa-id-card"></i> NIK
                                    <b class="float-right"><?= $user_login['nik']; ?></b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-envelope"></i> Email
                                    <b class="float-right"><?= $user_login['email']; ?></b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-phone"></i> No. HP
                                    <b class="float-right"><?= $user_login['nope']; ?></b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-clock"></i> Waktu Pendaftaran
                                    <b class="float-right"><?= $user_login['created_at']; ?></b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-landmark mr-1"></i> Lembaga
                                    <?php if (session()->get('role_id') == 1) : ?>
                                        <b class="float-right"> <?= isset($user_login['lk_nama']) ? $user_login['lk_nama'] : '' ?>
                                            <?=
                                            ucwords(strtolower(isset($user_login['nama_kab']) ? $user_login['nama_kab'] : '')); ?>
                                        </b>
                                    <?php elseif (session()->get('role_id') == 2) :  ?>
                                        <b class="float-right"> <?= isset($user_login['lk_nama']) ? $user_login['lk_nama'] : '' ?>
                                            <?=
                                            ucwords(strtolower(isset($nama_pemerintah) ? $nama_pemerintah : '')); ?>
                                        </b>
                                    <?php elseif (session()->get('role_id') >= 3) :   ?>
                                        <b class="float-right"> <?= isset($user_login['lk_nama']) ? $user_login['lk_nama'] : '' ?>
                                            <?=
                                            ucwords(strtolower(isset($user_login['nama_desa']) ? $user_login['nama_desa'] : '')); ?>
                                        </b>
                                    <?php endif; ?>
                                </li>

                                <li class="list-group-item"><i class="fas fa-user mr-1"></i> Nama Pimpinan
                                    <b class="float-right">
                                        <?= ucwords(strtolower(isset($user_login['lp_kepala']) ? $user_login['lp_kepala'] : '')); ?>
                                    </b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-map-marker-alt mr-1"></i> Sekretariat
                                    <b class="float-right"><?= isset($user_login['lp_sekretariat']) ? $user_login['lp_sekretariat'] : ''; ?></b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-envelope mr-1"></i> Email Lembaga
                                    <b class="float-right"><?= isset($user_login['lp_email']) ? $user_login['lp_email'] : ''; ?></b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-archive mr-1"></i> Kode Pos
                                    <b class="float-right"><?= isset($user_login['lp_kode_pos']) ? $user_login['lp_kode_pos'] : ''; ?></b>
                                <li class="list-group-item"><i class="fas fa-image mr-1"></i> Logo Kabupaten
                                    <img class="img-fluid float-right" src=" <?= base_url() ?>/landing-page/images/logo-garut.png" alt="Logo Kab. Garut" style="width: 100px;">
                                </li>
                            </ul>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <div class="col-md-9 col-sm-6">
                    <div class="card card-primary card-outline card-tabs">
                        <div class="card-header p-0 pt-1 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true"><strong><i class="far fa-user"></i> Personal</strong></a>
                                </li>
                                <?php if ($user_login['role_id'] <= 3): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false"><strong><i class="fas fa-landmark"></i> Lembaga</strong></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-three-wa-tab" data-toggle="pill"
                                            href="#custom-tabs-three-wa" role="tab">
                                            <strong><i class="fab fa-whatsapp"></i> WhatsApp</strong>
                                        </a>
                                    </li>
                                    <!-- Migration Tool (Admin) -->
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-three-migrate-tool" data-toggle="pill"
                                            href="#custom-tabs-three-migrate" role="tab">
                                            <strong><i class="fas fa-database"></i> Migration Tool</strong>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-dismissible alert-success" id="personalMsg" style="display: none;"></div>
                            <div class="alert alert-dismissible alert-success" id="lembagaMsg" style="display: none;"></div>
                            <div class="tab-content" id="custom-tabs-three-tabContent">
                                <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                                    <div class="col-12 col-md-4 col-lg-4 col-4">
                                        <form id="personal_form" method="POST" enctype="multipart/form-data">
                                            <!-- Profile Image -->
                                            <ul class="list-group list-group-unbordered mb-3">
                                                <li class="list-group-item" style="display: none;">
                                                    <b>ID Personal</b>
                                                    <?= form_input(['name' => 'id_user', 'class' => 'form-control', 'id' => 'id_user', 'value' => isset($user_login) ? set_value('id_user', $user_login['id_user']) : '', 'spellcheck' => 'false']); ?>
                                                </li>
                                                <li class="list-group-item mt-3">
                                                    <b><i class="fas fa-user mr-1"></i> Nama Lengkap</b>
                                                    <?= form_input(['name' => 'fullname', 'class' => 'form-control', 'id' => 'fullname', 'value' => isset($user_login) ? set_value('fullname', strtoupper($user_login['fullname'])) : '', 'spellcheck' => 'false', 'readonly' => 'true']); ?>
                                                </li>
                                                <li class="list-group-item mt-3">
                                                    <b><i class="fas fa-id-card mr-1"></i> NIK</b>
                                                    <?= form_input(['name' => 'nik', 'class' => 'form-control', 'id' => 'nik', 'value' => isset($user_login) ? set_value('nik', $user_login['nik']) : '', 'readonly' => 'true']); ?>
                                                </li>
                                                <li class="list-group-item mt-3">
                                                    <b><i class="fas fa-envelope mr-1"></i> Email </b>
                                                    <?= form_input(['name' => 'email', 'class' => 'form-control', 'id' => 'email', 'value' => isset($user_login) ? set_value('email', $user_login['email']) : '', 'readonly' => 'true']); ?>
                                                </li>
                                                <li class="list-group-item mt-3">
                                                    <b><i class="fas fa-phone mr-1"></i> No. HP </b>
                                                    <?= form_input(['name' => 'nope', 'class' => 'form-control', 'id' => 'nope', 'value' => isset($user_login) ? set_value('nope', $user_login['nope']) : '']); ?>
                                                </li>
                                                <li class="list-group-item mt-3">
                                                    <b><i class="fas fa-landmark mr-1"></i> Lembaga </b>
                                                    <select name="user_lembaga_id" id="user_lembaga_id" class="form-control" disabled>
                                                        <?php foreach ($lembaga as $row) { ?>
                                                            <option value="<?= $row['lk_id']; ?>" <?= $user_login['role_id'] == $row['lk_id'] ? 'selected' : ''; ?>><?= $row['lk_nama']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </li>
                                                <li class="list-group-item mt-3">
                                                    <b><i class="fas fa-building mr-1"></i> Nama <?= $user_role; ?> </b>
                                                    <select name="nama_pemerintah" id="nama_pemerintah" class="form-control" disabled>
                                                        <?php foreach ($getAjax as $row) { ?>
                                                            <?php if (session()->get('role_id') == 1) : ?>
                                                                <option value="<?= $row['kode_kab']; ?>" <?= $user_login['kode_kab'] == $row['kode_kab'] ? 'selected' : ''; ?>><?= ucwords(strtolower($row['nama_kab'])); ?></option>
                                                            <?php elseif (session()->get('role_id') == 2) : ?>
                                                                <option value="<?= $row['kode_kec']; ?>" <?= $user_login['kode_kec'] == $row['kode_kec'] ? 'selected' : ''; ?>><?= ucwords(strtolower($row['nama_kec'])); ?></option>
                                                            <?php elseif (session()->get('role_id') >= 3) : ?>
                                                                <option value="<?= $row['kode_desa']; ?>" <?= $user_login['kode_desa'] == $row['kode_desa'] ? 'selected' : ''; ?>><?= ucwords(strtolower($row['nama_desa'])); ?></option>
                                                            <?php endif; ?>
                                                        <?php } ?>
                                                    </select>
                                                </li>
                                                <li class="list-group-item mt-3">
                                                    <b><i class="fas fa-image mr-1"></i> Ubah Foto Profil</b>
                                                    <div class="custom-file">
                                                        <?= form_upload(['name' => 'fp_user', 'id' => 'fp_user', 'class' => 'form-control']); ?>
                                                        <!-- <label class="custom-file-label" for="fp_user">Choose file</label> -->
                                                    </div>
                                                </li>
                                            </ul>

                                            <button type="button" id="personalUpdate" class="btn btn-success btn-block">Update</button>
                                            <!-- /.card-body -->
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab" <?= $user_login['role_id'] > 3 ? 'hidden' : ''; ?>>
                                    <div class="col-12 col-md-4 col-lg-4 col-4">
                                        <form id="lembaga_form" method="POST" enctype="multipart/form-data">

                                            <?= form_input(['type' => 'hidden', 'name' => 'lp_id', 'class' => 'form-control', 'id' => 'lp_id', 'value' => isset($user_login['lp_id']) ? set_value('lp_id', $user_login['lp_id']) : '']); ?>
                                            <!-- /.card-header -->
                                            <strong><i class="fas fa-landmark mr-1"></i> Lembaga</strong>
                                            <select name="user_lembaga_id" id="user_lembaga_id" class="form-control" disabled>
                                                <?php foreach ($lembaga as $row) { ?>
                                                    <option <?= $row['lk_id'] == $user_login['role_id'] ? 'selected' : '' ?> value="<?= $row['lk_id']; ?>"><?= $row['lk_nama']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <hr>

                                            <strong><i class="fas fa-user mr-1"></i> Nama Pimpinan</strong>
                                            <?= form_input(['name' => 'lp_kepala', 'class' => 'form-control', 'id' => 'lp_kepala', 'value' => isset($user_login['lp_kepala']) ? set_value('lp_kepala', strtoupper($user_login['lp_kepala'])) : '']); ?>
                                            <hr>

                                            <strong><i class="fas fa-address-book mr-1"></i> NIP</strong>
                                            <?= form_input(['name' => 'lp_nip', 'class' => 'form-control', 'id' => 'lp_nip', 'value' => isset($user_login['lp_nip']) ? set_value('lp_nip', $user_login['lp_nip']) : '']); ?>
                                            <hr>

                                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Sekretariat</strong>
                                            <?= form_textarea(['name' => 'lp_sekretariat', 'class' => 'form-control', 'rows' => '3', 'spellcheck' => 'false', 'id' => 'lp_sekretariat', 'value' => isset($user_login['lp_sekretariat']) ? set_value('lp_sekretariat', $user_login['lp_sekretariat']) : '']); ?>
                                            <hr>

                                            <strong><i class="fas fa-envelope mr-1"></i> Email Lembaga</strong>
                                            <?= form_input(['name' => 'lp_email', 'class' => 'form-control', 'id' => 'lp_email', 'value' => isset($user_login['lp_email']) ? set_value('lp_email', $user_login['lp_email']) : '']); ?>
                                            <hr>

                                            <strong><i class="fas fa-archive mr-1"></i> Kode Pos</strong>
                                            <?= form_input(['name' => 'lp_kode_pos', 'class' => 'form-control', 'id' => 'lp_kode_pos', 'value' => isset($user_login['lp_kode_pos']) ? set_value('lp_kode_pos', $user_login['lp_kode_pos']) : '']); ?>
                                            <hr>

                                            <strong><i class="fas fa-image mr-1"></i> Logo</strong>
                                            <hr>

                                            <?= form_input(['type' => 'hidden', 'name' => 'id_user', 'class' => 'form-control', 'id' => 'id_user', 'value' => isset($user_login) ? set_value('id_user', $user_login['id_user']) : '']); ?>

                                            <?php if (isset($user_login['lp_id'])) : ?>
                                                <button type="button" id="lembagaUpdate" class="btn btn-success btn-block">Update</button>
                                            <?php else : ?>
                                                <button type="button" id="lembagaSubmit" class="btn btn-success btn-block">Submit</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-three-wa" role="tabpanel">
                                    <?= $this->include('profil/wa_settings'); ?>
                                </div>
                                <!-- Migration Tool (Admin) -->
                                <?php if ($user_login['role_id'] <= 3): ?>
                                    <div class="tab-pane fade" id="custom-tabs-three-migrate" role="tabpanel">
                                        <?= $this->include('profil/migration_tool'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </div>

        <!-- /.container-fluid -->

        <!-- /.container-fluid -->
    </section>
</div>
<!-- End of Main Content -->

<script type="text/javascript">
    $(document).ready(function() {
        $('#personalSubmit').submit(function(event) {
            event.preventDefault();
            var fullname = $('#fullname').val();
            var nik = $('#nik').val();
            var email = $('#email').val();
            var no_hape = $('#no_hape').val();

            if (fullname != '' && nik != '' && email != '' && no_hape != '') {
                $.ajax({
                    type: "POST",
                    url: "/add_user",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        // alert (data)
                        $("#personalMsg").show();
                        $("#personalMsg").html('Data berhasil diupdate.');
                        setTimeout(function() {
                            $("#personalMsg").hide();
                        }, 2000);
                        setTimeout(function() {
                            location.reload();
                        }, 2010);
                    }
                });
            } else {
                alert('isian Form. Profil tidak lengkap!');
            }
        });

        $("#personalUpdate").click(function(event) {
            //     alert('test');
            // });
            event.preventDefault();
            var form_data = new FormData($('#personal_form')[0]);
            var id_user = $('#id_user').val();
            var fullname = $('#fullname').val();
            var nik = $('#nik').val();
            var email = $('#email').val();
            var nope = $('#nope').val();
            var user_lembaga_id = $('#user_lembaga_id').val();
            var fp_user = $('#fp_user').val();

            if (fullname != '' || nik != '' || email != '' || nope != '' || user_lembaga_id != '') {
                $.ajax({
                    type: "POST",
                    url: '<?= site_url('update_user'); ?>',
                    dataType: 'json',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // alert(res);
                        if (res) {
                            $("#personalMsg").show();
                            $("#personalMsg").html('Data berhasil diupdate.');
                            setTimeout(function() {
                                $("#personalMsg").hide();
                            }, 2000);
                            setTimeout(function() {
                                location.reload();
                            }, 2010);
                            // alert (res)
                        }
                    }
                });
            } else {
                alert('Isi Profil dengan lengkap!');
            }
        });

        $("#lembagaUpdate").click(function(event) {
            //     alert('test');
            // });
            event.preventDefault();
            var form_data = new FormData($('#lembaga_form')[0]);
            var id_user = $('#id_user').val();
            var user_lembaga_id = $('#user_lembaga_id').val();
            var lp_kepala = $('#lp_kepala').val();
            var lp_sekretariat = $('#lp_sekretariat').val();
            var lp_kode_pos = $('#lp_kode_pos').val();
            var lp_email = $('#lp_email').val();

            if (user_lembaga_id != '' || lp_kepala != '' || lp_sekretariat != '' || lp_kode_pos != '') {
                $.ajax({
                    type: "POST",
                    url: '<?= site_url('update_lembaga'); ?>',
                    dataType: 'json',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // alert(res);
                        if (res) {
                            $("#lembagaMsg").show();
                            $("#lembagaMsg").html('Data berhasil diupdate.');
                            setTimeout(function() {
                                $("#lembagaMsg").hide();
                            }, 2000);
                            setTimeout(function() {
                                location.reload();
                            }, 2010);
                            // alert (res)
                        }
                    }
                });
            } else {
                alert('Isi Profil dengan lengkap!');
            }
        });
        $("#lembagaSubmit").click(function(event) {
            //     alert('test');
            // });
            event.preventDefault();
            var form_data = new FormData($('#lembaga_form')[0]);
            var id_user = $('#id_user').val();
            var user_lembaga_id = $('#user_lembaga_id').val();
            var lp_kepala = $('#lp_kepala').val();
            var lp_sekretariat = $('#lp_sekretariat').val();
            var lp_kode_pos = $('#lp_kode_pos').val();
            var lp_email = $('#lp_email').val();

            if (user_lembaga_id != '' || lp_kepala != '' || lp_sekretariat != '' || lp_kode_pos != '') {
                $.ajax({
                    type: "POST",
                    url: '<?= site_url('submit_lembaga'); ?>',
                    dataType: 'json',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // alert(res);
                        if (res) {
                            $("#lembagaMsg").show();
                            $("#lembagaMsg").html('Data berhasil diupdate.');
                            setTimeout(function() {
                                $("#lembagaMsg").hide();
                            }, 2000);
                            setTimeout(function() {
                                location.reload();
                            }, 2010);
                            // alert (res)
                        }
                    }
                });
            } else {
                alert('Isi Profil dengan lengkap!');
            }
        });

        $(function() {
            $('#personalUpdate').click(function() {
                // $('#desa').removeAttr('disabled', '');
                // window.location.reload();
                // $("#desa").attr('disabled', 'true');
                var $elt = $('#user_lembaga_id').removeAttr('disabled', '');
                setTimeout(function() {
                    $elt.attr('disabled', true);
                }, 500);

            });
        });

    });
</script>
<?= $this->endSection(); ?>