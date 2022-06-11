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
            <form action="/dtks/usulan/update/<?= $dtks['id']; ?>" method="post" class="form-horizontal">
                <div class="card-body">
                    <div class="form-group row">
                        <label for="id" class="col-sm-4 col-lg-2 col-form-label">ID</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="text" class="form-control <?= ($validation->hasError('id')) ? 'is-invalid' : ''; ?>" name="id" readonly placeholder="ID" value="<?= $dtks['id']; ?>">
                        </div>
                        <div class="invalid-feedback">
                            <?= $validation->getError('id'); ?>
                        </div>
                    </div>
                    <?php if (session()->get('jabatan') == 0) {  ?>
                        <div class="form-group row">
                            <label for="id_dtks" class="col-sm-4 col-lg-2 col-form-label">ID DTKS</label>
                            <div class="col-sm-8 col-lg-10">
                                <input type="numeric" class="form-control <?= ($validation->hasError('id_dtks')) ? 'is-invalid' : ''; ?>" name="id_dtks" placeholder="ID DTKS" value="<?= $dtks['id_dtks']; ?>">
                            </div>
                            <div class="invalid-feedback">
                                <?= $validation->getError('id_dtks'); ?>
                            </div>
                        </div>
                    <?php } else if (session()->get('jabatan') > 0) {  ?>
                        <div class="form-group row">
                            <label for="id_dtks" class="col-sm-4 col-lg-2 col-form-label">ID DTKS</label>
                            <div class="col-sm-8 col-lg-10">
                                <input type="numeric" class="form-control <?= ($validation->hasError('id_dtks')) ? 'is-invalid' : ''; ?>" name="id_dtks" readonly placeholder="ID DTKS" value="<?= $dtks['id_dtks']; ?>">
                            </div>
                            <div class="invalid-feedback">
                                <?= $validation->getError('id_dtks'); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group row">
                        <label for="nik" class="col-sm-4 col-lg-2 col-form-label">NIK</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="numeric" class="form-control <?= ($validation->hasError('nik')) ? 'is-invalid' : ''; ?>" name="nik" placeholder="NIK" value="<?= $dtks['nik']; ?>">
                        </div>
                        <div class="invalid-feedback">
                            <?= $validation->getError('nik'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nkk" class="col-sm-4 col-lg-2 col-form-label">No. KK</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="numeric" class="form-control <?= ($validation->hasError('nkk')) ? 'is-invalid' : ''; ?>" name="nkk" placeholder="No. KK" value="<?= $dtks['nkk']; ?>">
                        </div>
                        <div class="invalid-feedback">
                            <?= $validation->getError('nkk'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_krt" class="col-sm-4 col-lg-2 col-form-label">Nama Kepala Ruta</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="text" class="form-control <?= ($validation->hasError('nama_krt')) ? 'is-invalid' : ''; ?>" name="nama_krt" placeholder="Nama Kepala Rumah Tangga" value="<?= $dtks['nama_krt']; ?>">
                        </div>
                        <div class="invalid-feedback">
                            <?= $validation->getError('nama_krt'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tgl_lahir" class="col-sm-4 col-lg-2 col-form-label">Tanggal Lahir</label>
                        <div class="col-sm-8 col-lg-10 input-group date" data-target-input="nearest">
                            <input type="text" id="tanggal1" name="tgl_lahir" class="form-control datetimepicker-input <?= ($validation->hasError('tgl_lahir')) ? 'is-invalid' : ''; ?>" data-target="#tanggal1" spellcheck="false" data-ms-editor="true" value="<?= $dtks['tgl_lahir']; ?>">
                            <div class="input-group-append" data-target="#tanggal1" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-th"></i></div>
                            </div>
                            <div class="invalid-feedback">
                                <?= $validation->getError('tgl_lahir'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="alamat" class="col-sm-4 col-lg-2 col-form-label">Alamat</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="text" class="form-control <?= ($validation->hasError('alamat')) ? 'is-invalid' : ''; ?>" name="alamat" placeholder="Alamat" value="<?= $dtks['alamat']; ?>">
                        </div>
                        <div class="invalid-feedback">
                            <?= $validation->getError('alamat'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rt" class="col-sm-4 col-lg-2 col-form-label">No. RT</label>
                        <div class="col-sm-8 col-lg-10">
                            <select class="custom-select ">
                                <option class="<?= ($validation->hasError('rt')) ? 'is-invalid' : ''; ?>" value="<?= $dtks['rt']; ?>"><?= $dtks['rt']; ?></option>
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
                            <div class="invalid-feedback">
                                <?= $validation->getError('rt'); ?>
                            </div>
                        </div>
                    </div>
                    <?php if (session()->get('jabatan') == 0) {  ?>
                        <div class="form-group row">
                            <label for="rw" class="col-sm-4 col-lg-2 col-form-label">No. RW</label>
                            <div class="col-sm-8 col-lg-10">
                                <select class="custom-select ">
                                    <option class="<?= ($validation->hasError('rw')) ? 'is-invalid' : ''; ?>" value="<?= $dtks['rw']; ?>"><?= $dtks['rw']; ?></option>
                                    <option>--Pilih No. RW--</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('rw'); ?>
                                </div>
                            </div>
                        </div>
                    <?php } else if (session()->get('jabatan') > 0) { ?>
                        <div class="form-group row">
                            <label for="rw" class="col-sm-4 col-lg-2 col-form-label">No. RW</label>
                            <div class="col-sm-8 col-lg-10">
                                <select class="custom-select " disabled>
                                    <option class="<?= ($validation->hasError('rw')) ? 'is-invalid' : ''; ?>" value="<?= $dtks['rw']; ?>"><?= $dtks['rw']; ?></option>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('rw'); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group row">
                        <label for="rmh_depan" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Depan
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input name="rmh_depan" type="file" class="custom-file-input <?= ($validation->hasError('rmh_depan')) ? 'is-invalid' : ''; ?>" id="customFile" value="<?= $dtks['rmh_depan']; ?>">
                            <label name="rmh_depan" class="custom-file-label <?= ($validation->hasError('rmh_depan')) ? 'is-invalid' : ''; ?>" for="customFile">"<?= $dtks['rmh_depan']; ?>"</label>
                            <div class="invalid-feedback">
                                <?= $validation->getError('rmh_depan'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_kiri" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kiri
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input name="rmh_kiri" type="file" class="custom-file-input <?= ($validation->hasError('rmh_kiri')) ? 'is-invalid' : ''; ?>" id="customFile" value="<?= $dtks['rmh_kiri']; ?>">
                            <label name="rmh_kiri" class="custom-file-label <?= ($validation->hasError('rmh_kiri')) ? 'is-invalid' : ''; ?>" for="customFile">"<?= $dtks['rmh_kiri']; ?>"</label>
                            <div class="invalid-feedback">
                                <?= $validation->getError('rmh_kiri'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_belakang" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Belakang
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input name="rmh_belakang" type="file" class="custom-file-input <?= ($validation->hasError('rmh_belakang')) ? 'is-invalid' : ''; ?>" id="customFile" value="<?= $dtks['rmh_belakang']; ?>">
                            <label name="rmh_belakang" class="custom-file-label <?= ($validation->hasError('rmh_belakang')) ? 'is-invalid' : ''; ?>" for="customFile">"<?= $dtks['rmh_belakang']; ?>"</label>
                            <div class="invalid-feedback">
                                <?= $validation->getError('rmh_belakang'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_kanan" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kanan
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input name="rmh_kanan" type="file" class="custom-file-input <?= ($validation->hasError('rmh_kanan')) ? 'is-invalid' : ''; ?>" id="customFile" value="<?= $dtks['rmh_kanan']; ?>">
                            <label name="rmh_kanan" class="custom-file-label <?= ($validation->hasError('rmh_kanan')) ? 'is-invalid' : ''; ?>" for="customFile">"<?= $dtks['rmh_kanan']; ?>"</label>
                            <div class="invalid-feedback">
                                <?= $validation->getError('rmh_kanan'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="peristiwa" class="col-sm-4 col-lg-2 col-form-label">Peristiwa</label>
                        <div class="col-sm-8 col-lg-10">
                            <select class="custom-select ">
                                <option class="<?= ($validation->hasError('peristiwa')) ? 'is-invalid' : ''; ?>" value="<?= $dtks['peristiwa']; ?>"><?= $dtks['peristiwa']; ?></option>
                                <option>--Pilih Peristiwa--</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Meninggal Dunia">Meninggal Dunia</option>
                                <option value="Pindah">Pindah</option>
                                <option value="Datang">Datang</option>
                                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
                                <option value="Menolak">Menolak</option>
                            </select>
                            <div class="invalid-feedback">
                                <?= $validation->getError('peristiwa'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tgl_peristiwa" class="col-sm-4 col-lg-2 col-form-label">Tanggal Peristiwa</label>
                        <div class="col-sm-8 col-lg-10 input-group date" data-target-input="nearest">
                            <input type="text" id="tanggal2" name="tgl_peristiwa" class="form-control datetimepicker-input <?= ($validation->hasError('tgl_peristiwa')) ? 'is-invalid' : ''; ?>" data-target="#tanggal2" spellcheck="false" data-ms-editor="true" value="<?= $dtks['tgl_peristiwa']; ?>">
                            <div class="input-group-append" data-target="#tanggal2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-th"></i></div>
                            </div>
                            <div class="invalid-feedback">
                                <?= $validation->getError('tgl_peristiwa'); ?>
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
                                    <button type="submit" class="btn btn-success float-right"><i class="fas fa-edit"></i></button>
                                    <!-- <a href="/dtks/usulan/update/<?= $dtks['id']; ?>" class="btn btn-success">
                                        <i class="fas fa-edit"></i>
                                    </a> -->
                                </div>
                            <?php } else if (session()->get('jabatan') > 0) { ?>
                                <div class="col">
                                    <button type="submit" class="btn btn-success float-right"><i class="fas fa-edit"></i></button>
                                    <!-- <a href="/dtks/usulan/update/<?= $dtks['id']; ?>" class="btn btn-success">
                                        <i class="fas fa-edit"></i>
                                    </a> -->
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