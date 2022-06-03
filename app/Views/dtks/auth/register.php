<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-8 col-md-7">
            <div class="card o-hidden border-0 shadow-lg" id="elemen">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <!-- <div class="col-lg-6 d-none d-lg-block bg-login-image"></div> -->
                        <div class="col">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900">-- Opr NewDTKS --</h1>
                                    <h2 class="h4 text-gray-900"><?= $title; ?></h2>
                                </div>
                                <hr>
                                <!-- pesan validasi error -->
                                <?php if (session()->get('success')) : ?>
                                    <div class="col-12 mb-2" style="background-color: darkorange; border-radius: 3px; padding: 10px;">
                                        <div class="alert alert-success text-success" role="alert">
                                            <?= session()->get('success'); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (session()->get('message')) : ?>
                                    <div class="col-12 mb-2" style="background-color: darkorange; border-radius: 3px; padding: 10px;">
                                        <div class="alert alert-success text-danger" role="alert">
                                            <?= session()->get('message'); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($validation)) : ?>
                                    <div class="col-12 mb-2" style="background-color: darkorange; border-radius: 3px; padding: 10px;">
                                        <div class="col">
                                            <div class="container">
                                                <?= $validation->listErrors(); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <form action="/register" method="POST">
                                    <?= csrf_field(); ?>
                                    <div class="form-group my-1">
                                        <input type="text" class="form-control form-control-sm form-control-user" name="fullname" aria-describedby="emailHelp" placeholder="Masukan Nama Lengkap" value="<?= set_value('fullname'); ?>">
                                    </div>
                                    <div class="form-group my-1">
                                        <input type="numeric" class="form-control form-control-sm form-control-user" name="nik" aria-describedby="emailHelp" placeholder="Masukan No. KTP/NIK" value="<?= set_value('nik'); ?>">
                                    </div>
                                    <div class="form-group my-1">
                                        <input type="numeric" class="form-control form-control-sm form-control-user" name="nope" aria-describedby="emailHelp" placeholder="Masukan No. Handphone" value="<?= set_value('nope'); ?>">
                                    </div>
                                    <div class="form-group my-1">
                                        <input type="email" class="form-control form-control-sm form-control-user" name="email" aria-describedby="emailHelp" placeholder="Masukan Email" value="<?= set_value('email'); ?>">
                                    </div>
                                    <div class="form-group my-1">
                                        <select id="kelurahan" name="kelurahan" class="form-control form-control-sm form-control-user">
                                            <option value="">-- Pilih Desa / Kelurahan --</option>
                                            <?php foreach ($desa as $row) { ?>
                                                <option value="<?= $row['id'] ?>" <?= set_select('kelurahan', $row['id']); ?>> <?php echo $row['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group my-1">
                                        <select id="no_rw" name="no_rw" class="form-control form-control-sm form-control-user">
                                            <option value="">-- Pilih RW --</option>
                                            <?php foreach ($datarw as $row) { ?>
                                                <option value="<?= $row['no_rw'] ?>" <?= set_select('no_rw', $row['no_rw']); ?>> <?php echo $row['no_rw']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group my-1">
                                        <input type="password" class="form-control form-control-sm form-control-user" name="password" placeholder="Password" value="<?= set_value('password'); ?>">
                                    </div>
                                    <div class="form-group my-1">
                                        <input type="password" class="form-control form-control-sm form-control-user" name="password_confirm" placeholder="Password confirm" value="<?= set_value('password_confirm'); ?>">
                                    </div>
                                    <div class="form-group my-1">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="customCheck">
                                            <label class="custom-control-label" for="customCheck">Remember
                                                Me</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        Sign Up
                                    </button>
                                    <hr>
                                    <a href="" class="btn btn-google btn-user btn-block">
                                        <i class="fab fa-google fa-fw"></i> Sign up with Google
                                    </a>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="/dtks/auth/login">Already have an Account!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);
</script>

<?= $this->endSection(); ?>