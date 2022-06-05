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
                        <div class="col-12">
                            <div class="p-3">
                                <div class="card-header text-center">
                                    <img src="<?= base_url('icon-dtks.png'); ?>" alt="Logo" style="height: 50%; width: 50%;">
                                </div>
                                <div class="text-center">
                                    <h4 class="text-gray-900"><?= $title; ?></h4>
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
                                    <div class="form-group my-1 mt-5">
                                        <input type="text" class="form-control form-control-sm form-control-user" name="fullname" aria-describedby="emailHelp" placeholder="Masukan Nama Lengkap" value="<?= set_value('fullname'); ?>" autocomplete="off">
                                    </div>
                                    <div class="form-group my-1">
                                        <div class="row">
                                            <div class="col-6">
                                                <input type="numeric" class="form-control form-control-sm form-control-user" name="nik" aria-describedby="emailHelp" placeholder="Masukan No. KTP/NIK" value="<?= set_value('nik'); ?>" autocomplete="off">
                                            </div>
                                            <div class="col-6">
                                                <input type="numeric" class="form-control form-control-sm form-control-user" name="nope" aria-describedby="emailHelp" placeholder="Masukan No. Handphone" value="<?= set_value('nope'); ?>" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group my-1">
                                        <input type="email" class="form-control form-control-sm form-control-user" name="email" aria-describedby="emailHelp" placeholder="Masukan Email" value="<?= set_value('email'); ?>" autocomplete="off">
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
                                        <input type="text" class="form-control form-control-sm form-control-user" name="opr_sch" aria-describedby="opr_sch" placeholder="Masukan Nama Sekolah" value="<?= set_value('opr_sch'); ?>" autocomplete="off">
                                    </div>
                                    <div class="form-group my-1">
                                        <div class="row">
                                            <div class="col-6">
                                                <input type="password" class="form-control form-control-sm form-control-user" name="password" placeholder="Password" id="password1" value="<?= set_value('password'); ?>">
                                            </div>
                                            <div class="col-6">
                                                <input type="password" class="form-control form-control-sm form-control-user" name="password_confirm" placeholder="Ulangi Password" id="password2" value="<?= set_value('password_confirm'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group my-1">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="checkbox">
                                            <label class="custom-control-label" for="checkbox"> Tampilkan kata sandi</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success btn-sm btn-block" style="font-weight:bold">
                                            Daftar
                                        </button>
                                    </div>
                                    <hr>
                                </form>
                                <div class="text-center">
                                    <a class="small" href="/login" style="color: black; font-weight:bold">Already have an Account!</a>
                                </div>
                                </d3v>
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

    $(document).ready(function() {
        $('#checkbox').click(function() {
            if ($(this).is(':checked')) {
                $('#password1').attr('type', 'text');
                $('#password2').attr('type', 'text');
            } else {
                $('#password1').attr('type', 'password');
                $('#password2').attr('type', 'password');
            }
        });
    });
</script>
<?= $this->endSection(); ?>