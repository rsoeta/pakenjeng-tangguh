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
                                <?php if (session()->getFlashdata('message')) : ?>
                                    <div class="alert <?= session()->getFlashdata('message')['type'] === 'success' ? 'alert-success' : 'alert-danger' ?>">
                                        <?= session()->getFlashdata('message')['text'] ?>
                                    </div>
                                <?php endif; ?>
                                <form action="<?= base_url('reset-password') ?>" method="POST">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="token" value="<?= esc($token) ?>">
                                    <div class="form-group my-1">
                                        <div class="row">
                                            <div class="col-12">
                                                <label for="password">Password Baru:</label>
                                                <input type="password" class="form-control form-control-sm form-control-user" name="password" placeholder="Password" id="password" required>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <label for="password_confirm">Ulangi Password:</label>
                                                <input type="password" class="form-control form-control-sm form-control-user" name="password_confirm" placeholder="Ulangi Password" id="password_confirm" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group my-1">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="checkbox">
                                            <label class="custom-control-label" for="checkbox"> Tampilkan Password</label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success btn-sm btn-block" style="font-weight:bold">
                                            Reset Password!
                                        </button>
                                    </div>
                                    <hr>
                                </form>
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
                $('#password').attr('type', 'text');
                $('#password_confirm').attr('type', 'text');
            } else {
                $('#password').attr('type', 'password');
                $('#password_confirm').attr('type', 'password');
            }
        });
    });
</script>
<?= $this->endSection(); ?>