<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">

        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title" style="text-align: center;"><?= $title; ?></h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form class="form-horizontal">
                <div class="card-body">
                    <div class="form-group row">
                        <label for="id" class="col-sm-4 col-lg-2 col-form-label">ID</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="text" class="form-control" name="id" readonly placeholder="ID" value="<?= $dtks['id']; ?>">
                        </div>
                    </div>
                    <?php if (session()->get('jabatan') == 0) {  ?>
                        <div class="form-group row">
                            <label for="id_dtks" class="col-sm-4 col-lg-2 col-form-label">ID DTKS</label>
                            <div class="col-sm-8 col-lg-10">
                                <input type="numeric" class="form-control" name="id_dtks" placeholder="ID DTKS" value="<?= $dtks['id_dtks']; ?>">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group row">
                        <label for="nik" class="col-sm-4 col-lg-2 col-form-label">NIK</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="numeric" class="form-control" name="nik" placeholder="NIK" value="<?= $dtks['nik']; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nkk" class="col-sm-4 col-lg-2 col-form-label">No. KK</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="numeric" class="form-control" name="nkk" placeholder="No. KK" value="<?= $dtks['nkk']; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_krt" class="col-sm-4 col-lg-2 col-form-label">Nama Kepala Ruta</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="text" class="form-control" name="nama_krt" placeholder="Nama Kepala Rumah Tangga" value="<?= $dtks['nama_krt']; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tgl_lahir" class="col-sm-4 col-lg-2 col-form-label">Tanggal Lahir</label>
                        <div class="col-sm-8 col-lg-10 input-group date" data-target-input="nearest">
                            <input type="text" id="tanggal1" name="tanggal1" class="form-control datetimepicker-input" data-target="#tanggal1" spellcheck="false" data-ms-editor="true" value="<?= $dtks['tgl_lahir']; ?>">
                            <div class="input-group-append" data-target="#tanggal1" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-th"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="alamat" class="col-sm-4 col-lg-2 col-form-label">Alamat</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="text" class="form-control" name="alamat" placeholder="Alamat" value="<?= $dtks['alamat']; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rt" class="col-sm-4 col-lg-2 col-form-label">No. RT</label>
                        <div class="col-sm-8 col-lg-10">
                            <select class="custom-select">
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
                                <select class="custom-select">
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
                        <label for="rmh_depan" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Depan
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input type="file" class="custom-file-input" id="customFile" value="<?= $dtks['rmh_depan']; ?>">
                            <label class="custom-file-label" for="customFile">"<?= $dtks['rmh_depan']; ?>"</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_kiri" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kiri
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input type="file" class="custom-file-input" id="customFile" value="<?= $dtks['rmh_kiri']; ?>">
                            <label class="custom-file-label" for="customFile">"<?= $dtks['rmh_kiri']; ?>"</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_belakang" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Belakang
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input type="file" class="custom-file-input" id="customFile" value="<?= $dtks['rmh_belakang']; ?>">
                            <label class="custom-file-label" for="customFile">"<?= $dtks['rmh_belakang']; ?>"</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_kanan" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kanan
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input type="file" class="custom-file-input" id="customFile" value="<?= $dtks['rmh_kanan']; ?>">
                            <label class="custom-file-label" for="customFile">"<?= $dtks['rmh_kanan']; ?>"</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="peristiwa" class="col-sm-4 col-lg-2 col-form-label">Peristiwa</label>
                        <div class="col-sm-8 col-lg-10">
                            <select class="custom-select">
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
                            <input type="text" id="tanggal2" name="tanggal2" class="form-control datetimepicker-input" data-target="#tanggal2" spellcheck="false" data-ms-editor="true" value="<?= $dtks['tgl_peristiwa']; ?>">
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

    </section>
</div>

<?= $this->endSection(); ?>