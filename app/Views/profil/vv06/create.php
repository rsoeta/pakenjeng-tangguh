<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">

        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Form. Tambah Data</h3>
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
                    <?php } else if (session()->get('jabatan') > 0) {  ?>
                        <div class="form-group row">
                            <label for="id_dtks" class="col-sm-4 col-lg-2 col-form-label">ID DTKS</label>
                            <div class="col-sm-8 col-lg-10">
                                <input type="numeric" class="form-control" name="id_dtks" readonly placeholder="ID DTKS" value="<?= $dtks['id_dtks']; ?>">
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
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
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
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    <option>6</option>
                                    <option>7</option>
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
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_kiri" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kiri
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input type="file" class="custom-file-input" id="customFile" value="<?= $dtks['rmh_kiri']; ?>">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_belakang" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Belakang
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input type="file" class="custom-file-input" id="customFile" value="<?= $dtks['rmh_belakang']; ?>">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="rmh_kanan" class="col-sm-4 col-lg-2 col-form-label">Rumah Tmpk Kanan
                        </label>
                        <div class="custom-file col-sm-8 col-lg-10">
                            <input type="file" class="custom-file-input" id="customFile" value="<?= $dtks['rmh_kanan']; ?>">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="peristiwa" class="col-sm-4 col-lg-2 col-form-label">Peristiwa</label>
                        <div class="col-sm-8 col-lg-10">
                            <select class="custom-select">
                                <option value="<?= $dtks['peristiwa']; ?>"><?= $dtks['peristiwa']; ?></option>
                                <option>--Pilih Peristiwa--</option>
                                <option value="">Aktif</option>
                                <option value="MD">Meninggal Dunia</option>
                                <option value="PD">Pindah</option>
                                <option value="DD">Datang</option>
                                <option value="TT">Tidak Ditemukan</option>
                                <option value="MM">Menolak</option>
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
                    <div class="card-footer">
                        <button type="submit" class="btn btn-secondary">Back</button>
                        <button type="submit" class="btn btn-success float-right">Save</button>
                    </div>
                    <!-- /.card-footer -->
            </form>
        </div>

    </section>
</div>

<?= $this->endSection(); ?>