<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">

        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><?= $title; ?></h3>
                <br>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="/dtks/usulan/update/<?= $dtks['id']; ?>" method="post" class="form-horizontal">
                <?= csrf_field(); ?>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="id" class="col-sm-4 col-lg-2 col-form-label" hidden>ID</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="text" class="form-control" name="id" readonly placeholder="ID" value="<?= (old('id')) ? old('id') : $dtks['id'] ?>" hidden>
                        </div>
                    </div>
                    <?php if (session()->get('jabatan') == 0) {  ?>
                        <div class="form-group row">
                            <label for="id_dtks" class="col-sm-4 col-lg-2 col-form-label">ID DTKS</label>
                            <div class="col-sm-8 col-lg-10">
                                <input type="numeric" class="form-control" name="id_dtks" placeholder="ID DTKS" value="<?= (old('id_dtks')) ? old('id_dtks') : $dtks['id_dtks'] ?>">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group row">
                        <label for="nik" class="col-sm-4 col-lg-2 col-form-label">NIK</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="numeric" class="form-control <?= ($validation->hasError('nik')) ? 'is-invalid' : ''; ?>" name="nik" placeholder="NIK" value="<?= (old('nik')) ? old('nik') : $dtks['nik'] ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('nik'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nkk" class="col-sm-4 col-lg-2 col-form-label">No. KK</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="numeric" class="form-control <?= ($validation->hasError('nkk')) ? 'is-invalid' : ''; ?>" name="nkk" placeholder="No. KK" value="<?= (old('nkk')) ? old('nkk') : $dtks['nkk'] ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('nkk'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_krt" class="col-sm-4 col-lg-2 col-form-label">Nama Kepala Ruta</label>
                        <div class="col-sm-8 col-lg-10">
                            <input type="text" class="form-control <?= ($validation->hasError('nama_krt')) ? 'is-invalid' : ''; ?>" name="nama_krt" placeholder="Nama Kepala Rumah Tangga" value="<?= (old('nama_krt')) ? old('nama_krt') : $dtks['nama_krt'] ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('nama_krt'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tgl_lahir" class="col-sm-4 col-lg-2 col-form-label">Tanggal Lahir</label>
                        <div class="col-sm-8 col-lg-10 input-group date">
                            <input type="text" id="tanggal1" name="tgl_lahir" class="form-control datetimepicker-input <?= ($validation->hasError('tgl_lahir')) ? 'is-invalid' : ''; ?>" data-target="#tanggal1" spellcheck="false" data-ms-editor="true" value="<?= (old('tgl_lahir')) ? old('tgl_lahir') : $dtks['tgl_lahir'] ?>">
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
                            <input type="text" class="form-control <?= ($validation->hasError('alamat')) ? 'is-invalid' : ''; ?>" name="alamat" placeholder="Alamat" value="<?= (old('alamat')) ? old('alamat') : $dtks['alamat'] ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('alamat'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rt" class="col-sm-4 col-lg-2 col-form-label">No. RT</label>
                        <div class="col-sm-8 col-lg-10">
                            <select name="rt" class="custom-select <?= ($validation->hasError('rt')) ? 'is-invalid' : ''; ?>">
                                <option value="<?= (old('rt')) ? old('rt') : $dtks['rt'] ?>"><?= (old('rt')) ? old('rt') : $dtks['rt'] ?></option>
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
                                <select name="rw" class="custom-select <?= ($validation->hasError('rw')) ? 'is-invalid' : ''; ?>" value="">
                                    <option value="<?= (old('rw')) ? old('rw') : $dtks['rw'] ?>"><?= (old('rw')) ? old('rw') : $dtks['rw'] ?></option>
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
                                <select name="rw" class="custom-select <?= ($validation->hasError('rw')) ? 'is-invalid' : ''; ?>" value="">
                                    <option value="<?= (old('rw')) ? old('rw') : $dtks['rw'] ?>"><?= (old('rw')) ? old('rw') : $dtks['rw'] ?></option>
                                    <option value="<?= session()->get('jabatan'); ?>"><?= session()->get('jabatan'); ?></option>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('rw'); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group row">
                        <label for="rmh_depan" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Depan</label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input <?= ($validation->hasError('rmh_depan')) ? 'is-invalid' : ''; ?>" id="rmh_depan" name="rmh_depan" aria-describedby="inputGroupFileAddon01" onchange="preViewImgdepan()">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('rmh_depan'); ?>
                                </div>
                                <label class="custom-file-label" for="rmh_depan">Pilih foto</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_kiri" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kiri</label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input <?= ($validation->hasError('rmh_kiri')) ? 'is-invalid' : ''; ?>" id="rmh_kiri" name="rmh_kiri" aria-describedby="inputGroupFileAddon01" onchange="preViewImgkiri()">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('rmh_kiri'); ?>
                                </div>
                                <label class="custom-file-label" for="rmh_kiri">Pilih foto</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_belakang" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Belakang</label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input <?= ($validation->hasError('rmh_belakang')) ? 'is-invalid' : ''; ?>" id="rmh_belakang" name="rmh_belakang" aria-describedby="inputGroupFileAddon01" onchange="preViewImgbelakang()">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('rmh_belakang'); ?>
                                </div>
                                <label class="custom-file-label" for="rmh_belakang">Pilih foto</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_kanan" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kanan</label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input <?= ($validation->hasError('rmh_kanan')) ? 'is-invalid' : ''; ?>" id="rmh_kanan" name="rmh_kanan" aria-describedby="inputGroupFileAddon01" onchange="preViewImgkanan()">
                                <div class="invalid-feedback">
                                    <?= $validation->getError('rmh_kanan'); ?>
                                </div>
                                <label class="custom-file-label" for="rmh_kanan">Pilih foto</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="jml_kel" class="col-sm-4 col-lg-2 col-form-label">Jml Keluarga</label>
                        <div class="col-sm-8 col-lg-10">
                            <select name="jml_kel" class="custom-select <?= ($validation->hasError('jml_kel')) ? 'is-invalid' : ''; ?>" value="<?= old('jml_kel'); ?>">
                                <option>--Pilih Jml Keluarga--</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                            <div class="invalid-feedback">
                                <?= $validation->getError('jml_kel'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="jml_art" class="col-sm-4 col-lg-2 col-form-label">Jml. ART</label>
                        <div class="col-sm-8 col-lg-10">
                            <select name="jml_art" class="custom-select <?= ($validation->hasError('jml_art')) ? 'is-invalid' : ''; ?>" value="<?= old('jml_art'); ?>">
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
                            <div class="invalid-feedback">
                                <?= $validation->getError('jml_art'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="peristiwa" class="col-sm-4 col-lg-2 col-form-label">Peristiwa</label>
                        <div class="col-sm-8 col-lg-10">
                            <select name="peristiwa" class="custom-select <?= ($validation->hasError('peristiwa')) ? 'is-invalid' : ''; ?>" value="">
                                <option value="<?= (old('peristiwa')) ? old('peristiwa') : $dtks['peristiwa'] ?>"><?= (old('peristiwa')) ? old('peristiwa') : $dtks['peristiwa'] ?></option>
                                <option value="">--Pilih Peristiwa--</option>
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
                            <input type="text" id="tanggal2" name="tgl_peristiwa" class="form-control datetimepicker-input <?= ($validation->hasError('tgl_peristiwa')) ? 'is-invalid' : ''; ?>" data-target="#tanggal2" spellcheck="false" data-ms-editor="true" value="<?= (old('tgl_peristiwa')) ? old('tgl_peristiwa') : $dtks['tgl_peristiwa'] ?>">
                            <div class="input-group-append" data-target="#tanggal2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-th"></i></div>
                            </div>
                            <div class="invalid-feedback">
                                <?= $validation->getError('tgl_peristiwa'); ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <a href="/dtks/usulan/detail/<?= $dtks['id']; ?>" class="btn btn-secondary">Back</a>
                        <button type="submit" class="btn btn-success float-right">Update</button>
                    </div>
                    <!-- /.card-footer -->
            </form>
        </div>

    </section>
</div>

<?= $this->endSection(); ?>