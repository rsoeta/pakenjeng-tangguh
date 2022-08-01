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
                    <!-- get breadcrumb from menu -->
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="<?= base_url(); ?>">Home</a></li>
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
                <div class="col-md-3 col-sm-12">
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
                                <li class="list-group-item"><i class="fas fa-id-card mr-1"></i> NIK
                                    <b class="float-right"><?= $user_login['nik']; ?></b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-envelope mr-1"></i> Email
                                    <b class="float-right"><?= $user_login['email']; ?></b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-phone mr-1"></i> No. HP
                                    <b class="float-right"><?= $user_login['nope']; ?></b>
                                </li>
                                <li class="list-group-item"><i class="fas fa-clock mr-1"></i> Waktu Pendaftaran
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
                <div class="col-md-9 col-sm-12">
                    <div class="card card-primary card-outline card-tabs">
                        <div class="card-header p-0 pt-1 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="false"><strong><i class="far fa-user mr-1"></i> Personal</strong></a>
                                </li>
                                <li class="nav-item" <?= $user_login['role_id'] > 3 ? 'hidden' : ''; ?>>
                                    <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false"><strong><i class="fas fa-landmark mr-1"></i> Lembaga</strong></a>
                                </li>
                                <!-- tab menu -->
                                <li class="nav-item" <?= $user_login['role_id'] > 2 ? 'hidden' : ''; ?>>
                                    <a class="nav-link" id="custom-tabs-three-menu-tab" data-toggle="pill" href="#custom-tabs-three-menu" role="tab" aria-controls="custom-tabs-three-menu" aria-selected="false"><strong><i class="fas fa-bars mr-1"></i> Menu</strong></a>
                                </li>
                                <li class="nav-item" <?= $user_login['role_id'] > 2 ? 'hidden' : ''; ?>>
                                    <a class="nav-link" id="custom-tabs-three-general-tab" data-toggle="pill" href="#custom-tabs-three-general" role="tab" aria-controls="custom-tabs-three-general" aria-selected="false"><strong><i class="fas fa-cog mr-1"></i> General</strong></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-three-shield-tab" data-toggle="pill" href="#custom-tabs-three-shield" role="tab" aria-controls="custom-tabs-three-shield" aria-selected="true"><strong><i class="fas fa-shield-alt mr-1"></i> Ubah Password</strong></a>
                                </li>
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
                                                    <?= form_input(['name' => 'fullname', 'class' => 'form-control', 'id' => 'fullname', 'value' => isset($user_login) ? set_value('fullname', strtoupper($user_login['fullname'])) : '', 'spellcheck' => 'false']); ?>
                                                </li>
                                                <li class="list-group-item mt-3">
                                                    <b><i class="fas fa-id-card mr-1"></i> NIK</b>
                                                    <?= form_input(['name' => 'nik', 'class' => 'form-control', 'id' => 'nik', 'value' => isset($user_login) ? set_value('nik', $user_login['nik']) : '']); ?>
                                                </li>
                                                <li class="list-group-item mt-3">
                                                    <b><i class="fas fa-envelope mr-1"></i> Email </b>
                                                    <?= form_input(['name' => 'email', 'class' => 'form-control', 'id' => 'email', 'value' => isset($user_login) ? set_value('email', $user_login['email']) : '']); ?>
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
                                                    <select name="nama_pemerintah" id="nama_pemerintah" class="form-control select2">
                                                        <?php foreach ($getKec as $row) { ?>
                                                            <option <?= ($row['id'] == $user_login['kode_kec']) ? 'selected' : ''; ?> value="<?= $row['id']; ?>"><?= ucwords(strtolower($row['name'])); ?></option>
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


                                            <?= form_input(['type' => 'hidden', 'name' => 'id_user', 'class' => 'form-control', 'id' => 'id_user', 'value' => isset($user_login) ? set_value('id_user', $user_login['id_user']) : '']); ?>

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

                                            <?php if (isset($user_login['lp_id'])) : ?>
                                                <button type="button" id="lembagaUpdate" class="btn btn-success btn-block">Update</button>
                                            <?php else : ?>
                                                <button type="button" id="lembagaSubmit" class="btn btn-success btn-block">Submit</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-three-shield" role="tabpanel" aria-labelledby="custom-tabs-three-shield-tab">
                                    <div class="col-12 col-md-4 col-lg-4 col-4">
                                        <form id="password_form" method="POST">
                                            <?= form_input(['type' => 'hidden', 'name' => 'id_user', 'class' => 'form-control', 'id' => 'id_user', 'value' => isset($user_login) ? set_value('id_user', $user_login['id_user']) : '']); ?>
                                            <?= form_input(['type' => 'hidden', 'name' => 'password_old', 'class' => 'form-control', 'id' => 'password_old']); ?>
                                            <strong><i class="fas fa-key mr-1"></i> Password Lama</strong>
                                            <?= form_password(['name' => 'password_old', 'class' => 'form-control', 'id' => 'password_old']); ?>
                                            <hr>
                                            <strong><i class="fas fa-lock mr-1"></i> Password Baru</strong>
                                            <?= form_password(['name' => 'password_new', 'class' => 'form-control', 'id' => 'password_new']); ?>
                                            <hr>
                                            <strong><i class="fas fa-lock mr-1"></i> Konfirmasi Password Baru</strong>
                                            <?= form_password(['name' => 'password_confirm', 'class' => 'form-control', 'id' => 'password_confirm']); ?>
                                            <hr>
                                            <button type="button" id="passwordSubmit" class="btn btn-success btn-block disabled">Submit</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-three-menu" role="tabpanel" aria-labelledby="cuxtom-tab-three-menu-tab">
                                    <div class="col-12 table">
                                        <table class="table table-sm table-striped compact" cellspacing="0" width="100%">
                                            <thead>
                                                <tr class="table-dark">
                                                    <td>No</td>
                                                    <td>ID</td>
                                                    <td>Nama Menu/Submenu</td>
                                                    <td>Class</td>
                                                    <td>Link</td>
                                                    <td>Icon</td>
                                                    <td colspan="2">Parent</td>
                                                    <td>Akses</td>
                                                    <td>Status</td>
                                                    <td>Action</td>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-three-general" role="tabpanel" aria-labelledby="cuxtom-tab-three-general-tab" <?= $user_login['role_id'] > 2 ? 'hidden' : ''; ?>>
                                    <div class="col-12 col-md-4 col-lg-4 col-4">
                                        <form id="general_form" method="POST">
                                            <input type="hidden" name="dd_id" id="dd_id" value="<?= isset($deadline) ? $deadline->dd_id : ''; ?>">
                                            <strong><i class="fa fa-calendar-alt mr-1"></i> Tanggal Deadline</strong>
                                            <input type="datetime-local" name="dd_waktu" id="dd_waktu" class="form-control" value="<?= isset($deadline) ? $deadline->dd_waktu : ''; ?>">
                                            <hr>
                                            <?php if (isset($deadline)) : ?>
                                                <button type="button" id="btnGenUpdate" class="btn btn-success btn-block">Update</button>
                                            <?php else : ?>
                                                <button type="button" id="btnGenSubmit" class="btn btn-success btn-block">Submit</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</div>
<!-- End of Main Content -->
<script type="text/javascript">
    $(document).ready(function() {

        function load_data_menu() {
            $.ajax({
                type: "post",
                url: "load_data_menu",
                dataType: "json",
                success: function(response) {
                    // make column editable
                    var html = '<tr>';
                    html += '<td id="" placeholder="No urut akan terisi otomatis"></td>';
                    html += '<td id="tm_id" placeholder="ID akan terisi otomatis"></td>';
                    html += '<td id="tm_nama" contenteditable placeholder="Isikan Nama menu/submenu"></td>';
                    html += '<td id="tm_class" contenteditable placeholder="Isikan class code"></td>';
                    html += '<td id="tm_url" contenteditable placeholder="Isikan link"></td>';
                    html += '<td id="tm_icon" contenteditable placeholder="Isikan kode icon code"></td>';
                    html += '<td colspan="2" id="tm_parent_id" contenteditable placeholder="Isikan ID parent"></td>';
                    html += '<td id="tm_grup_akses" contenteditable placeholder="Isikan akses"></td>';
                    html += '<td id="tm_status" contenteditable placeholder="Isikan status"></td>';
                    html += '<td><button type="button" name="btn_add" id="btn_add" class="btn btn-sm btn-outline-success btn-block"><i class="fa fa-check"></i></button></td>';
                    html += '</tr>';
                    // loop data
                    $.each(response, function(i, item) {
                        html += '<tr>';
                        html += '<td>' + (i + 1) + '</td>';
                        //  get id  
                        html += '<td class="table_data_menu" data-column-name="tm_id" id="' + item.tm_id + '" contenteditable>' + item.tm_id + '</td>';
                        html += '<td class="table_data_menu" data-column-name="tm_nama" id="' + item.tm_id + '" contenteditable>' + item.tm_nama + '</td>';
                        html += '<td class="table_data_menu" data-column-name="tm_class" id="' + item.tm_id + '" contenteditable>' + item.tm_class + '</td>';
                        html += '<td class="table_data_menu" data-column-name="tm_url" id="' + item.tm_id + '" contenteditable>' + item.tm_url + '</td>';
                        html += '<td class="table_data_menu" data-column-name="tm_icon" id="' + item.tm_id + '" contenteditable>' + item.tm_icon + '</td>';
                        html += '<td colspan="2" class="table_data_menu" data-column-name="tm_parent_id" id="' + item.tm_id + '" contenteditable>' + item.tm_parent_id + '</td>';

                        // show get_nama_menu to table
                        // html += get_nama_menu(item.tm_parent_id);


                        // html += namaParent.responseText;
                        html += '<td class="table_data_menu" data-column-name="tm_grup_akses" id="' + item.tm_id + '" contenteditable>' + item.tm_grup_akses + '</td>';
                        // get status with checkbox class data-toggle="toggle"
                        if (item.tm_status == '1') {
                            html += '<td class="table_data_menu" data-column-name="tm_status" id="' + item.tm_id + '" contenteditable><input type="checkbox" class="toggle_checkbox" data-toggle="toggle" data-on="Aktif" data-off="Tidak Aktif" data-onstyle="success" data-offstyle="danger" checked value="1"></td>';
                        } else {
                            html += '<td class="table_data_menu" data-column-name="tm_status" id="' + item.tm_id + '" contenteditable><input type="checkbox" class="toggle_checkbox" data-toggle="toggle" data-on="Aktif" data-off="Tidak Aktif" data-onstyle="success" data-offstyle="danger" value="0"></td>';
                        }
                        // html += '<td class="table_data_menu" data-column-name="tm_status" id="' + item.tm_id + '" contenteditable>' + item.tm_status + '</td>';
                        html += '<td><button type="button" name="btn_delete" class="btn btn-sm btn-outline-danger btn-block btn_delete"><i class="fa fa-trash-alt"></i></button></td>';
                        html += '</tr>';
                    });
                    // append to table
                    $('tbody').html(html);
                }
            });
        }
        load_data_menu(); // panggil fungsi load data menu

        // get item.tm_nama from item.tm_parent_id
        function get_nama_menu(id) {
            $.ajax({
                type: "post",
                url: "get_nama_menu",
                data: {
                    id: item.tm_parent_id
                },
                dataType: "json",
                success: function(response) {
                    // $('#tm_parent_id').val(response.tm_nama);
                    html += '<td class="table_data_menu" data-column-name="tm_parent_nama" id="' + item.tm_id + '" contenteditable>' + response.data + '</td>';
                }
            });
        }
        // get item.tm_nama from item.tm_parent_id


        // insert data menu
        $(document).on('click', '#btn_add', function() {
            var tm_nama = $('#tm_nama').text();
            var tm_class = $('#tm_class').text();
            var tm_url = $('#tm_url').text();
            var tm_icon = $('#tm_icon').text();
            var tm_parent_id = $('#tm_parent_id').text();
            var tm_grup_akses = $('#tm_grup_akses').text();
            var tm_status = $('#tm_status').text();

            // cek if fields is empty
            if (tm_nama == '' || tm_url == '' || tm_icon == '' || tm_parent_id == '' || tm_grup_akses == '' || tm_status == '') {
                alert('Isikan data dengan lengkap');
            } else {
                $.ajax({
                    type: "post",
                    url: "insert_data_menu",
                    data: {
                        tm_nama: tm_nama,
                        tm_class: tm_class,
                        tm_url: tm_url,
                        tm_icon: tm_icon,
                        tm_parent_id: tm_parent_id,
                        tm_grup_akses: tm_grup_akses,
                        tm_status: tm_status
                    },
                    success: function(data) {
                        // alert('Data berhasil ditambahkan');
                        load_data_menu();
                    }
                });
            }
        });

        // update data menu
        $(document).on('blur', '.table_data_menu', function() {
            var id = $(this).attr('id');
            var table_column = $(this).attr('data-column-name');
            // if value from input is empty, then set value from checkbox
            if ($(this).text() == '') {
                // get value from checkbox
                var valu = $(this).find('input[type="checkbox"]').prop('checked');
                // if checkbox is checked, then set value 1, else set value 0
                if (valu) {
                    var value = 1;
                } else {
                    var value = 0;
                }
            } else {
                var value = $(this).text();
            }
            // var value = $(this).text();
            // var value = $(this).text();
            // alert(id + ', ' + table_column + ', ' + value);
            $.ajax({
                type: "post",
                url: "update_data_menu",
                data: {
                    id: id,
                    table_column: table_column,
                    value: value
                },
                success: function(data) {
                    // alert('Data berhasil diupdate');
                    load_data_menu();
                }
            });
        });

        // delete data menu
        $(document).on('click', '.btn_delete', function() {
            // alert confitm delete
            var conf = confirm('Yakin ingin menghapus data ini?');
            if (conf) {
                var id = $(this).parents("tr").find("td:eq(1)").text();

                $.ajax({
                    type: "post",
                    url: "delete_data_menu",
                    data: {
                        id: id
                    },
                    success: function(data) {
                        //  sweet alert
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })

                        Toast.fire({
                            icon: 'success',
                            title: 'data berhasil dihapus',
                        });
                        // alert('Data berhasil dihapus');
                        load_data_menu();
                    }
                });
            }
        });

        $('#personalSubmit').submit(function(event) {
            event.preventDefault();
            var fullname = $('#fullname').val();
            var nik = $('#nik').val();
            var email = $('#email').val();
            var no_hape = $('#no_hape').val();

            if (fullname != '' && nik != '' && email != '' && no_hape != '') {
                $.ajax({
                    type: "POST",
                    url: "",
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
            var nama_pemerintah = $('#nama_pemerintah').val();
            var fp_user = $('#fp_user').val();

            if (fullname != '' || nik != '' || email != '' || nope != '' || user_lembaga_id != '') {
                $.ajax({
                    type: "POST",
                    url: '<?= site_url('update_web_admin'); ?>',
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
                    url: '<?= site_url('submit_web_lembaga'); ?>',
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
                    url: '<?= site_url('update_web_lembaga'); ?>',
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
                    url: '<?= site_url('submit_web_lembaga'); ?>',
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

        $("#btnGenSubmit").click(function(event) {
            //     alert('test');
            // });
            event.preventDefault();
            var form_data = new FormData($('#general_form')[0]);
            var dd_id = $('#dd_id').val();
            var dd_waktu = $('#dd_waktu').val();
            var dd_deskripsi = $('#dd_deskripsi').val();

            if (dd_waktu != '') {
                $.ajax({
                    type: "POST",
                    url: '<?= site_url('submit_web_general'); ?>',
                    dataType: 'json',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // alert(res);
                        if (res) {
                            $("#lembagaMsg").show();
                            $("#lembagaMsg").html('Data berhasil diinput.');
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
                alert('Isi dengan lengkap!');
            }
        });

        $("#btnGenUpdate").click(function(event) {
            //     alert('test');
            // });
            event.preventDefault();
            var form_data = new FormData($('#general_form')[0]);
            var dd_id = $('#dd_id').val();
            var dd_waktu = $('#dd_waktu').val();

            if (dd_waktu != '') {
                $.ajax({
                    type: "POST",
                    url: '<?= site_url('update_web_general'); ?>',
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
                alert('Isi dengan lengkap!');
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

        // select2
        $('.select2').select2();
    });
</script>
<?= $this->endSection(); ?>