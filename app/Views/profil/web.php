<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <?php if (isset($title)) { ?>
                        <h1><?= $title; ?></h1>
                    <?php } ?>
                </div>
                <div class="col-sm-6">
                    <!-- get breadcrumb from menu -->
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?= $title ?? ''; ?></li>
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
                                    <img class="img-fluid float-right" src=" <?= base_url('/landing-page/images/logo-garut.png') ?>" alt="Logo Kab. Garut" style="width: 100px;">
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
                            <?php if (session()->has('success')) : ?>
                                <div class="alert alert-success">
                                    <?= session('success') ?>
                                </div>
                            <?php endif; ?>

                            <div class="tab-content" id="custom-tabs-three-tabContent">
                                <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                                    <div class="col-12 col-md-4 col-lg-4 col-4">
                                        <form id="personal_form" method="POST" enctype="multipart/form-data">
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

                                                    <div class="text-center mt-2 mb-3">
                                                        <img id="preview_fp"
                                                            class="profile-user-img img-fluid img-circle shadow-sm"
                                                            src="<?= Foto_Profil($user_login['user_image'] ?? '', 'profil'); ?>"
                                                            alt="<?= $user_login['fullname'] ?? 'User'; ?> profile picture"
                                                            style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #adb5bd;">
                                                    </div>

                                                    <div class="custom-file">
                                                        <?= form_upload([
                                                            'name'   => 'fp_user',
                                                            'id'     => 'fp_user',
                                                            'class'  => 'form-control',
                                                            'accept' => 'image/*'
                                                        ]); ?>
                                                    </div>
                                                </li>
                                            </ul>

                                            <button type="button" id="personalUpdate" class="btn btn-success btn-block">Update</button>
                                            <div id="personalMsg" class="mt-2 text-center fw-bold text-success" style="display: none;"></div>
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
                                <div class="tab-pane fade" id="custom-tabs-three-menu" role="tabpanel" aria-labelledby="custom-tabs-three-menu-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold text-secondary mb-0"><i class="fas fa-list-alt text-primary"></i> Struktur Navigasi SINDEN</h5>
                                        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambahMenu">
                                            <i class="fas fa-plus-circle"></i> Tambah Menu Baru
                                        </button>
                                    </div>

                                    <div class="col-12 table-responsive p-0" style="border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                        <table class="table table-sm table-hover table-striped mb-0 align-middle" id="tableManagementMenu" style="width:100%;">
                                            <thead>
                                                <tr class="bg-dark text-white text-center" style="font-size: 0.9rem;">
                                                    <th style="width: 50px;">No</th>
                                                    <th style="width: 60px;">ID</th>
                                                    <th class="text-left">Nama Menu / Submenu</th>
                                                    <th>Class Code</th>
                                                    <th>Route URL</th>
                                                    <th>Icon Icon</th>
                                                    <th style="width: 70px;">Parent</th>
                                                    <th style="width: 70px;">Akses</th>
                                                    <th>Dashboard</th>
                                                    <th style="width: 80px;">Urutan</th>
                                                    <th>Status</th>
                                                    <th style="width: 60px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody style="font-size: 0.875rem;">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalTambahMenu" role="dialog" aria-labelledby="modalTambahMenuLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                                                <h5 class="modal-title fw-bold" id="modalTambahMenuLabel"><i class="fas fa-sliders-h"></i> Form Menu Baru</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form id="form_tambah_menu" method="POST">
                                                <div class="modal-body p-4">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6 form-group mb-3">
                                                            <label class="fw-bold small text-secondary">Nama Menu <span class="text-danger">*</span></label>
                                                            <input type="text" name="tm_nama" id="new_tm_nama" class="form-control form-control-sm" placeholder="Contoh: PDTT 2025" required spellcheck="false">
                                                        </div>
                                                        <div class="col-12 col-md-6 form-group mb-3">
                                                            <label class="fw-bold small text-secondary">Route URL Link <span class="text-danger">*</span></label>
                                                            <input type="text" name="tm_url" id="new_tm_url" class="form-control form-control-sm" placeholder="Contoh: pdtt/2025" required spellcheck="false">
                                                        </div>
                                                        <div class="col-12 col-md-6 form-group mb-3">
                                                            <label class="fw-bold small text-secondary">Icon Class (FontAwesome)</label>
                                                            <input type="text" name="tm_icon" id="new_tm_icon" class="form-control form-control-sm" placeholder="Contoh: fas fa-shield-alt" spellcheck="false">
                                                        </div>
                                                        <div class="col-12 col-md-6 form-group mb-3">
                                                            <label class="fw-bold small text-secondary">CSS Class Code</label>
                                                            <input type="text" name="tm_class" id="new_tm_class" class="form-control form-control-sm" placeholder="Kosongkan jika tidak ada" spellcheck="false">
                                                        </div>
                                                        <div class="col-12 col-md-6 form-group mb-3">
                                                            <label class="fw-bold small text-secondary">Induk Parent Menu</label>
                                                            <select name="tm_parent_id" id="new_tm_parent_id" class="form-control form-control-sm">
                                                                <option value="0">--- Menu Utama (No Parent) ---</option>
                                                                <?php foreach ($menu as $m): ?>
                                                                    <?php if ($m['tm_parent_id'] == 0): ?>
                                                                        <option value="<?= $m['tm_id'] ?>"><?= esc($m['tm_nama']) ?> [ID: <?= $m['tm_id'] ?>]</option>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-6 form-group mb-3">
                                                            <label class="fw-bold small text-secondary">Batas Grup Akses</label>
                                                            <select name="tm_grup_akses" id="new_tm_grup_akses" class="form-control form-control-sm">
                                                                <?php foreach ($statusRole as $role): ?>
                                                                    <option value="<?= $role['id_role'] ?>" <?= $role['id_role'] == 4 ? 'selected' : '' ?>><?= esc($role['nm_role']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-4 form-group mb-3">
                                                            <label class="fw-bold small text-secondary">Nomor Urutan</label>
                                                            <input type="number" name="tm_urutan" id="new_tm_urutan" class="form-control form-control-sm text-center" value="0" min="0">
                                                        </div>
                                                        <div class="col-12 col-md-4 form-group mb-3">
                                                            <label class="fw-bold small text-secondary">Dashboard Shortcut</label>
                                                            <select name="tm_is_dashboard" id="new_tm_is_dashboard" class="form-control form-control-sm">
                                                                <option value="0">Sembunyi</option>
                                                                <option value="1">Tampilkan</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md-4 form-group mb-3">
                                                            <label class="fw-bold small text-secondary">Status Sistem</label>
                                                            <select name="tm_status" id="new_tm_status" class="form-control form-control-sm">
                                                                <option value="1">Aktif</option>
                                                                <option value="0">Non-Aktif</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" data-dismiss="modal">Batal</button>
                                                    <button type="submit" id="btnSubmitMenu" class="btn btn-success btn-sm shadow-sm px-3"><i class="fas fa-save"></i> Simpan Menu</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="custom-tabs-three-general" role="tabpanel" aria-labelledby="cuxtom-tab-three-general-tab" <?= $user_login['role_id'] > 2 ? 'hidden' : ''; ?>>
                                    <!-- Usulan Akses -->
                                    <div class="card">
                                        <form id="updateForm" method="post" action="">
                                            <div class="card-header">
                                                <h5><strong>Usulan Akses</strong></h1>
                                            </div>
                                            <div class="card-body">
                                                <?php foreach ($deadline as $dd) { ?>
                                                    <div class="row">
                                                        <div class="col-4 col-sm-3">
                                                            <strong><i class="fa fa-user mr-1"></i> Hak-Akses</strong>
                                                            <select class="form-control" name="dd_role[]">
                                                                <?php foreach ($statusRole as $s) { ?>
                                                                    <option <?= $dd['dd_role'] == $s['id_role'] ? 'selected' : ''; ?> value="<?= $s['id_role']; ?>"><?= $s['nm_role']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-4 col-sm-3">
                                                            <input type="hidden" name="dd_id[]" id="" value="<?= $dd['dd_id']; ?>">
                                                            <strong><i class="fa fa-calendar-alt mr-1"></i> Start Date</strong>
                                                            <input type="datetime-local" name="dd_waktu_start[]" id="" class="form-control" value="<?= $dd['dd_waktu_start']; ?>">
                                                        </div>
                                                        <div class="col-4 col-sm-3">
                                                            <strong><i class="fa fa-calendar-alt mr-1"></i> End Date</strong>
                                                            <input type="datetime-local" name="dd_waktu_end[]" id="" class="form-control" value="<?= $dd['dd_waktu_end']; ?>">
                                                        </div>

                                                    </div>
                                                <?php } ?>
                                                <div class="row">
                                                    <div class="col-12 col-md-9 col-sm-9 text-right mt-2">
                                                        <button type="submit" class="btn btn-success btnGenUpdate"><i class="fa fa-check-double"></i> Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- General Akses -->
                                    <div class="card">
                                        <form id="updateFormGen" method="post" action="">
                                            <div class="card-header">
                                                <h5><strong>General Akses</strong></h1>
                                            </div>
                                            <div class="card-body">
                                                <?php foreach ($deadline_general as $dd) { ?>
                                                    <div class="row">
                                                        <div class="col-4 col-sm-3">
                                                            <strong><i class="fa fa-user mr-1"></i> Hak-Akses</strong>
                                                            <select class="form-control" name="dd_role[]">
                                                                <?php foreach ($statusRole as $s) { ?>
                                                                    <option <?= $dd['dd_role'] == $s['id_role'] ? 'selected' : ''; ?> value="<?= $s['id_role']; ?>"><?= $s['nm_role']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-4 col-sm-3">
                                                            <input type="hidden" name="dd_id[]" id="" value="<?= $dd['dd_id']; ?>">
                                                            <strong><i class="fa fa-calendar-alt mr-1"></i> Start Date</strong>
                                                            <input type="datetime-local" name="dd_waktu_start[]" id="" class="form-control" value="<?= $dd['dd_waktu_start']; ?>">
                                                        </div>
                                                        <div class="col-4 col-sm-3">
                                                            <strong><i class="fa fa-calendar-alt mr-1"></i> End Date</strong>
                                                            <input type="datetime-local" name="dd_waktu_end[]" id="" class="form-control" value="<?= $dd['dd_waktu_end']; ?>">
                                                        </div>

                                                    </div>
                                                <?php } ?>
                                                <div class="row">
                                                    <div class="col-12 col-md-9 col-sm-9 text-right mt-2">
                                                        <button type="submit" class="btn btn-warning btnGenUpdate"><i class="fa fa-check-circle"></i> Update</button>
                                                    </div>
                                                </div>
                                            </div>
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

        // =========================================================================
        // 🚀 LOAD DATA MENU (UI/UX UPGRADE VERSION)
        // =========================================================================
        function load_data_menu() {
            $.ajax({
                type: "post",
                url: "load_data_menu",
                dataType: "json",
                success: function(response) {
                    var html = '';

                    $.each(response, function(i, item) {
                        html += '<tr class="text-center">';
                        html += '<td class="align-middle text-secondary font-weight-bold">' + (i + 1) + '</td>';
                        html += '<td class="table_data_menu align-middle text-muted fw-bold small" data-column-name="tm_id" id="' + item.tm_id + '">' + item.tm_id + '</td>';

                        // 🚀 UX UPGRADE: Tampilkan nama menu berjejer dengan icon aslinya (Live Preview)
                        var iconPreview = item.tm_icon ? '<i class="' + item.tm_icon + ' text-primary mr-2" style="width:20px; display:inline-block; text-align:center;"></i>' : '<i class="fas fa-dot-circle text-muted mr-2"></i>';
                        html += '<td class="table_data_menu align-middle text-left" data-column-name="tm_nama" id="' + item.tm_id + '" contenteditable style="font-weight:600; color:#2c3e50;">' + iconPreview + item.tm_nama + '</td>';

                        // Kolom Class Code
                        var classVal = item.tm_class ? item.tm_class : '-';
                        html += '<td class="table_data_menu align-middle text-muted" data-column-name="tm_class" id="' + item.tm_id + '" contenteditable>' + classVal + '</td>';

                        // 🚀 UX UPGRADE: Badgify URL Link
                        html += '<td class="table_data_menu align-middle text-left" data-column-name="tm_url" id="' + item.tm_id + '" contenteditable><code class="text-danger bg-light px-2 py-1 rounded" style="font-size:0.8rem; border:1px solid #f1f1f1;">' + item.tm_url + '</code></td>';

                        // Kolom Icon Code
                        html += '<td class="table_data_menu align-middle text-left text-monospace font-weight-light" data-column-name="tm_icon" id="' + item.tm_id + '" contenteditable style="font-size:0.8rem; color:#7f8c8d;">' + (item.tm_icon ? item.tm_icon : '') + '</td>';
                        html += '<td class="table_data_menu align-middle font-weight-bold text-dark" data-column-name="tm_parent_id" id="' + item.tm_id + '" contenteditable>' + item.tm_parent_id + '</td>';
                        html += '<td class="table_data_menu align-middle font-weight-bold text-dark" data-column-name="tm_grup_akses" id="' + item.tm_id + '" contenteditable>' + item.tm_grup_akses + '</td>';

                        // Column Dashboard Switch
                        var chkDash = (item.tm_is_dashboard == '1') ? 'checked' : '';
                        html += '<td class="align-middle" data-column-name="tm_is_dashboard" id="' + item.tm_id + '"><input type="checkbox" class="toggle_checkbox_auto" data-toggle="toggle" data-on="<i class=\'fas fa-bolt\'></i> Tampil" data-off="Sembunyi" data-onstyle="info" data-offstyle="secondary" data-size="sm" ' + chkDash + ' value="1"></td>';

                        // Column Urutan
                        html += '<td class="table_data_menu align-middle font-weight-bold text-primary" data-column-name="tm_urutan" id="' + item.tm_id + '" contenteditable style="font-size:1rem;">' + item.tm_urutan + '</td>';

                        // Column Status Switch
                        var chkStat = (item.tm_status == '1') ? 'checked' : '';
                        html += '<td class="align-middle" data-column-name="tm_status" id="' + item.tm_id + '"><input type="checkbox" class="toggle_checkbox_auto" data-toggle="toggle" data-on="Aktif" data-off="Mati" data-onstyle="success" data-offstyle="danger" data-size="sm" ' + chkStat + ' value="1"></td>';

                        // Column Action Delete
                        html += '<td class="align-middle"><button type="button" name="btn_delete" class="btn btn-xs btn-outline-danger btn_delete py-1 px-2" style="border-radius:4px;"><i class="fa fa-trash-alt"></i></button></td>';
                        html += '</tr>';
                    });

                    $('#tableManagementMenu tbody').html(html);

                    // Render ulang komponen toggle bootstrap
                    if ($.fn.bootstrapToggle) {
                        $('#tableManagementMenu .toggle_checkbox_auto').bootstrapToggle();
                    }
                }
            });
        }
        load_data_menu();

        // =========================================================================
        // 🚀 ACTION SUBMIT: TAMBAH DATA MENU VIA MODAL (BUG-FREE & ASYNC)
        // =========================================================================
        $('#form_tambah_menu').on('submit', function(e) {
            e.preventDefault();

            var form_data = $(this).serialize();

            // Ubah status tombol loading
            var btn = $('#btnSubmitMenu');
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

            $.ajax({
                type: "POST",
                url: "insert_data_menu",
                data: form_data,
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalText);

                    if (res.status === 'success') {
                        // Tutup modal
                        $('#modalTambahMenu').modal('hide');
                        // Reset isi form inputan
                        $('#form_tambah_menu')[0].reset();

                        // Memicu Notifikasi Sukses SweetAlert2 Mobile-Friendly (320px)
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses! 🎉',
                            text: res.message,
                            width: '320px',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: {
                                title: 'fs-5',
                                content: 'fs-6'
                            }
                        });

                        // Muat ulang tabel navigasi secara realtime
                        load_data_menu();
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr, status, error) {
                    btn.prop('disabled', false).html(originalText);
                    console.error(xhr.responseText);
                    alert("Terjadi kesalahan sistem saat menyimpan menu!");
                }
            });
        });

        // UPDATE DATA MENU (Untuk In-Place Text Editing / ContentEditable)
        $(document).on('blur', '.table_data_menu[contenteditable]', function() {
            var id = $(this).attr('id');
            var table_column = $(this).attr('data-column-name');
            var value = $(this).text().trim();

            // Pengaman khusus untuk kolom link URL apabila dibungkus tag <code> oleh browser
            if (table_column === 'tm_url') {
                value = $(this).find('code').text().trim() || value;
            }

            $.ajax({
                type: "post",
                url: "update_data_menu",
                data: {
                    id: id,
                    table_column: table_column,
                    value: value
                },
                success: function(data) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Perubahan disimpan!'
                    });
                }
            });
        });

        // UPDATE DATA SWITCH TOGGLE (Dashboard & Status)
        $(document).on('change', '.toggle_checkbox_auto', function() {
            var td = $(this).closest('td');
            var id = td.attr('id');
            var table_column = td.attr('data-column-name');
            var value = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                type: "post",
                url: "update_data_menu",
                data: {
                    id: id,
                    table_column: table_column,
                    value: value
                },
                success: function(data) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Perubahan disimpan!'
                    });
                }
            });
        });

        // delete data menu
        $(document).on('click', '.btn_delete', function() {
            var tr = $(this).parents("tr");
            var id = tr.find("td:eq(1)").text(); // Ambil ID dari kolom ke-2

            // 🚀 Ganti confirm() standar dengan SweetAlert2 (Versi Mungil)
            Swal.fire({
                title: 'Hapus Data?',
                text: "Menu ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                width: '320px', // Perkecil ukuran untuk kenyamanan layar mobile
                customClass: {
                    title: 'fs-5',
                    content: 'fs-6'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "delete_data_menu",
                        data: {
                            id: id
                        },
                        success: function(data) {
                            // Toast Notifikasi Sukses
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
                            });

                            Toast.fire({
                                icon: 'success',
                                title: 'Data berhasil dihapus'
                            });

                            load_data_menu();
                        }
                    });
                }
            });
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

        // ==========================================
        // 🖼️ LOGIKA PREVIEW GAMBAR (REAL-TIME)
        // ==========================================
        $('#fp_user').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Mengganti src hasil helper dengan data URL file baru
                    $('#preview_fp').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        $("#personalUpdate").click(function(event) {
            event.preventDefault();

            // 1. Tarik Data dari Form
            var form_data = new FormData($('#personal_form')[0]);

            // 2. 🛡️ SUNTIKKAN CSRF TOKEN (Solusi 403 Forbidden)
            form_data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            // 3. Masukkan nilai dropdown Lembaga secara manual karena dia 'disabled'
            form_data.append('user_lembaga_id', $('#user_lembaga_id').val());

            // 4. Ambil nilai untuk validasi frontend
            var fullname = $('#fullname').val().trim();
            var nik = $('#nik').val().trim();
            var email = $('#email').val().trim();
            var nope = $('#nope').val().trim();

            // 5. Validasi: Wajib isi semua (menggunakan AND '&&' bukan OR '||')
            if (fullname !== '' && nik !== '' && email !== '' && nope !== '') {

                // Ubah status tombol menjadi loading
                var btn = $(this);
                var originalText = btn.html();
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

                $.ajax({
                    type: "POST",
                    url: '<?= site_url('update_web_admin'); ?>',
                    dataType: 'json',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res) {
                            $("#personalMsg").html('Data berhasil diupdate!').show();

                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            btn.prop('disabled', false).html(originalText);
                            alert("Gagal mengupdate data.");
                        }
                    },
                    error: function(xhr, status, error) {
                        // Jika masih terjadi error (misal 500), tombol akan menyala kembali
                        btn.prop('disabled', false).html(originalText);
                        console.error("Error Response: ", xhr.responseText);
                        alert("Terjadi kesalahan sistem! Coba lagi.");
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
            const form_submit = new FormData($('#submit_form')[0]);
            const dd_waktu_start1 = $('#dd_waktu_start1').val();
            const dd_waktu_end1 = $('#dd_waktu_end1').val();
            const dd_role1 = $('#dd_role1').val();
            // alert(form_data);

            if (dd_waktu_start1 != null || dd_waktu_end1 != null || dd_role1 != null) {
                $.ajax({
                    type: "POST",
                    url: '<?= site_url('submit_web_general'); ?>',
                    dataType: 'json',
                    data: form_submit,
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

        $('#updateForm').on('submit', function(e) {
            e.preventDefault(); // Mencegah formulir dikirimkan secara default

            var formData = $(this).serialize(); // Mengambil data formulir

            $.ajax({
                url: '/updateBatch', // Ganti dengan URL yang sesuai dengan controller dan method Anda
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Tanggapan dari server setelah proses update berhasil
                    // console.log(response);
                    // Tampilkan SweetAlert sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: 'Update deadline usulan berhasil!',
                    });
                },
                error: function(xhr, status, error) {
                    // Penanganan kesalahan jika terjadi
                    console.error(xhr.responseText);
                }
            });
        });

        $('#updateFormGen').on('submit', function(e) {
            e.preventDefault(); // Mencegah formulir dikirimkan secara default

            var formData = $(this).serialize(); // Mengambil data formulir

            $.ajax({
                url: '/updateBatchGen', // Ganti dengan URL yang sesuai dengan controller dan method Anda
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Tanggapan dari server setelah proses update berhasil
                    // console.log(response);
                    // Tampilkan SweetAlert sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: 'Update deadline general berhasil!',
                    });
                },
                error: function(xhr, status, error) {
                    // Penanganan kesalahan jika terjadi
                    console.error(xhr.responseText);
                }
            });
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

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?= $this->endSection(); ?>