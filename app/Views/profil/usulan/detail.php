<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Profil</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Profil KPM</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">

                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" src="<?= base_url(); ?>/assets/dist/img/user4-128x128.jpg" alt="User profile picture">
                            </div>

                            <h3 class="profile-username text-center"><?= $dtks['nama_krt']; ?></h3>

                            <p class="text-muted text-center"><?= $dtks['nik']; ?></p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Jumlah Keluarga</b> <a class="float-right"><?= $dtks['jml_kel']; ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Jumlah Tanggungan</b> <a class="float-right"><?= $dtks['jml_art']; ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Status</b> <a class="float-right"><?= $dtks['status']; ?></a>
                                </li>
                            </ul>
                            <?php if (session()->get('jabatan') == 0) {  ?>
                                <a href="/dtks/usulan/edit/<?= $dtks['id']; ?>" class="btn btn-success btn-block">
                                    <b>Edit</b>
                                </a>
                                <a href="/dtks/usulan/delete/<?= $dtks['id']; ?>" class="btn btn-danger btn-block" onclick="return confirm('anda akan menghapus data ini. apakah anda yakin?');">
                                    <b>Delete</b>
                                </a>
                            <?php } elseif (session()->get('jabatan') > 0) {   ?>
                                <a href="/dtks/usulan/edit/<?= $dtks['id']; ?>" class="btn btn-success btn-block">
                                    <b>Edit</b>
                                </a>
                            <?php } ?>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Deskripsi</a></li>
                                <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Detail</a></li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="activity">
                                    <!-- Post -->
                                    <div class="post">
                                        <div class="user-block">
                                            <img class="img-circle img-bordered-sm" src="<?= base_url(); ?>/assets/dist/img/user1-128x128.jpg" alt="user image">
                                            <span class="username">
                                                <a href="#"><?= $dtks['nama_krt']; ?></a>
                                                <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                                            </span>
                                            <span class="description">Di update pada - <?= $dtks['updated_at']; ?></span>
                                        </div>
                                        <!-- /.user-block -->
                                        <p>
                                            Lahir pada tanggal <?= $dtks['tgl_lahir']; ?> menjadi kepala keluarga dengan nomor <?= $dtks['nkk']; ?>. tinggal di <?= $dtks['alamat']; ?> dalam satu rumah terdapat <?= $dtks['jml_kel']; ?> Keluarga dan memiliki <?= $dtks['jml_art']; ?> anggota rumah tangga, dan status saat ini adalah <?= $dtks['peristiwa']; ?>
                                        </p>
                                    </div>
                                    <!-- /.post -->

                                    <!-- Post -->
                                    <div class="post">
                                        <div class="user-block">
                                            <a href="#">Dok. Rumah</a>
                                            <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                                        </div>
                                        <!-- /.user-block -->
                                        <div class="row mb-3">
                                            <div class="col-sm-6">
                                                <img class="img-fluid" src="<?= base_url(); ?>/img/dtks_usulan/<?= $dtks['rmh_depan']; ?>" alt="Photo">
                                                <p style="text-align: center;">Foto rumah tampak depan</p>
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-sm-6">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <img class="img-fluid mb-3" src="<?= base_url(); ?>/img/dtks_usulan/<?= $dtks['rmh_kanan']; ?>" alt="Photo">
                                                        <p style="text-align: center;">Foto rumah tampak kanan</p>
                                                        <img class="img-fluid mb-3" src="<?= base_url(); ?>/img/dtks_usulan/<?= $dtks['rmh_kiri']; ?>" alt="Photo">
                                                        <p style="text-align: center;">Foto rumah tampak kiri</p>
                                                    </div>
                                                    <!-- /.col -->
                                                    <div class="col-sm-6">
                                                        <img class="img-fluid" src="<?= base_url(); ?>/img/dtks_usulan/<?= $dtks['rmh_belakang']; ?>" alt="Photo">
                                                        <p style="text-align: center;">Foto rumah tampak belakang</p>
                                                    </div>
                                                    <!-- /.col -->
                                                </div>
                                                <!-- /.row -->
                                            </div>
                                            <!-- /.col -->
                                        </div>
                                        <!-- /.row -->
                                    </div>
                                    <!-- /.post -->
                                </div>
                                <div class="tab-pane" id="settings">
                                    <form class="form-horizontal">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label for="id" class="col-sm-4 col-lg-2 col-form-label" hidden>ID</label>
                                                <div class="col-sm-8 col-lg-10">
                                                    <input type="hidden" class="form-control" name="id" readonly placeholder="ID" value="<?= $dtks['id']; ?>">
                                                </div>
                                            </div>
                                            <?php if (session()->get('jabatan') == 0) {  ?>
                                                <div class="form-group row">
                                                    <label for="id_dtks" class="col-sm-4 col-lg-2 col-form-label">ID DTKS</label>
                                                    <div class="col-sm-8 col-lg-10">
                                                        <input type="numeric" class="form-control" name="id_dtks" placeholder="ID DTKS" value="<?= $dtks['id_dtks']; ?>" readonly>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group row">
                                                <label for="nik" class="col-sm-4 col-lg-2 col-form-label">NIK</label>
                                                <div class="col-sm-8 col-lg-10">
                                                    <input type="numeric" class="form-control" name="nik" placeholder="NIK" value="<?= $dtks['nik']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="nkk" class="col-sm-4 col-lg-2 col-form-label">No. KK</label>
                                                <div class="col-sm-8 col-lg-10">
                                                    <input type="numeric" class="form-control" name="nkk" placeholder="No. KK" value="<?= $dtks['nkk']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="nama_krt" class="col-sm-4 col-lg-2 col-form-label">Nama Kepala Ruta</label>
                                                <div class="col-sm-8 col-lg-10">
                                                    <input type="text" class="form-control" name="nama_krt" placeholder="Nama Kepala Rumah Tangga" value="<?= $dtks['nama_krt']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="tgl_lahir" class="col-sm-4 col-lg-2 col-form-label">Tanggal Lahir</label>
                                                <div class="col-sm-8 col-lg-10 input-group date" data-target-input="nearest">
                                                    <input type="text" id="tanggal1" name="tanggal1" class="form-control datetimepicker-input" data-target="#tanggal1" spellcheck="false" data-ms-editor="true" value="<?= $dtks['tgl_lahir']; ?>" readonly>
                                                    <div class="input-group-append" data-target="#tanggal1" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-th"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="alamat" class="col-sm-4 col-lg-2 col-form-label">Alamat</label>
                                                <div class="col-sm-8 col-lg-10">
                                                    <input type="text" class="form-control" name="alamat" placeholder="Alamat" value="<?= $dtks['alamat']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rt" class="col-sm-4 col-lg-2 col-form-label">No. RT</label>
                                                <div class="col-sm-8 col-lg-10">
                                                    <select class="custom-select" disabled>
                                                        <option value="<?= $dtks['rt']; ?>"><?= $dtks['rt']; ?></option>
                                                        <option>--Pilih No. RT--</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php if (session()->get('jabatan') == 0) {  ?>
                                                <div class="form-group row">
                                                    <label for="rw" class="col-sm-4 col-lg-2 col-form-label">No. RW</label>
                                                    <div class="col-sm-8 col-lg-10">
                                                        <select class="custom-select" disabled>
                                                            <option value="<?= $dtks['rw']; ?>"><?= $dtks['rw']; ?></option>
                                                            <option>--Pilih No. RW--</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } else if (session()->get('jabatan') > 0) { ?>

                                                <div class="form-group row">
                                                    <label for="rw" class="col-sm-4 col-lg-2 col-form-label">No. RW</label>
                                                    <div class="col-sm-8 col-lg-10">
                                                        <select class="custom-select" disabled>
                                                            <option value="<?= $dtks['rw']; ?>"><?= $dtks['rw']; ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group row">
                                                <label for="rmh_depan" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Depan</label>
                                                <div class="custom-file col-sm-8 col-lg-10">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="rmh_depan" name="rmh_depan" aria-describedby="inputGroupFileAddon01" onchange="preViewImgdepan()" disabled>
                                                        <label class="custom-file-label" for="rmh_depan">Pilih foto</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rmh_kiri" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kiri</label>
                                                <div class="custom-file col-sm-8 col-lg-10">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="rmh_kiri" name="rmh_kiri" aria-describedby="inputGroupFileAddon01" onchange="preViewImgkiri()" disabled>
                                                        <label class="custom-file-label" for="rmh_kiri">Pilih foto</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rmh_belakang" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Belakang</label>
                                                <div class="custom-file col-sm-8 col-lg-10">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="rmh_belakang" name="rmh_belakang" aria-describedby="inputGroupFileAddon01" onchange="preViewImgbelakang()" disabled>
                                                        <label class="custom-file-label" for="rmh_belakang">Pilih foto</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rmh_kanan" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kanan</label>
                                                <div class="custom-file col-sm-8 col-lg-10">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="rmh_kanan" name="rmh_kanan" aria-describedby="inputGroupFileAddon01" onchange="preViewImgkanan()" disabled>
                                                        <label class="custom-file-label" for="rmh_kanan">Pilih foto</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="jml_kel" class="col-sm-4 col-lg-2 col-form-label">Jml Keluarga</label>
                                                <div class="col-sm-8 col-lg-10">
                                                    <select name="jml_kel" class="custom-select" value="<?= old('jml_kel'); ?>" disabled>
                                                        <option>--Pilih Jml Keluarga--</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="jml_art" class="col-sm-4 col-lg-2 col-form-label">Jml. ART</label>
                                                <div class="col-sm-8 col-lg-10">
                                                    <select name="jml_art" class="custom-select" value="<?= old('jml_art'); ?>" disabled>
                                                        <option>--Pilih Jml. ART--</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                        <option value="13">13</option>
                                                        <option value="14">14</option>
                                                        <option value="15">15</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="peristiwa" class="col-sm-4 col-lg-2 col-form-label">Peristiwa</label>
                                                <div class="col-sm-8 col-lg-10">
                                                    <select class="custom-select" disabled>
                                                        <option value="<?= $dtks['peristiwa']; ?>"><?= $dtks['peristiwa']; ?></option>
                                                        <option>--Pilih Peristiwa--</option>
                                                        <option value="Aktif">Aktif</option>
                                                        <option value="Meninggal Dunia">Meninggal Dunia</option>
                                                        <option value="Pindah">Pindah</option>
                                                        <option value="Datang">Datang</option>
                                                        <option value="Tidak Ditemukan">Tidak Ditemukan</option>
                                                        <option value="Menolak">Menolak</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="tgl_peristiwa" class="col-sm-4 col-lg-2 col-form-label">Tanggal Peristiwa</label>
                                                <div class="col-sm-8 col-lg-10 input-group date" data-target-input="nearest">
                                                    <input type="text" id="tanggal2" name="tanggal2" class="form-control datetimepicker-input" data-target="#tanggal2" spellcheck="false" data-ms-editor="true" value="<?= $dtks['tgl_peristiwa']; ?>" readonly>
                                                    <div class="input-group-append" data-target="#tanggal2" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-th"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-lg-2"></label>
                                                <div class="col-sm-8 col-lg-10 input-group ">
                                                    <?php if (session()->get('jabatan') == 0) { ?>
                                                        <div class="col">
                                                            <a href="/dtks/usulan/delete/<?= $dtks['id']; ?>" class="btn btn-danger" onclick="return confirm('anda akan menghapus data ini. apakah anda yakin?');">
                                                                <i class=" fas fa-trash"></i>
                                                            </a>
                                                            <a href="/dtks/usulan/edit/<?= $dtks['id']; ?>" class="btn btn-success">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    <?php } else if (session()->get('jabatan') > 0) { ?>
                                                        <div class="col">
                                                            <a href="/dtks/usulan/edit/<?= $dtks['id']; ?>" class="btn btn-success">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <!-- /.card-footer -->
                                    </form>
                                    <hr>
                                    <a href="/dtks/usulan/tables">Kembali ke daftar DTKS</a>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>


<?= $this->endSection(); ?>