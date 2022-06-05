<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <img src="<?= base_url('icon-dtks.png'); ?>" alt="Logo" style="height: 50%; width: 50%;">
        </div>
        <div class="card-body">
            <p class="login-box-msg">
                <?php if (session()->get('success')) : ?>
            <div class="alert alert-success text-center" role="alert">
                <?= session()->get('success'); ?>
            </div>
        <?php endif; ?>
        <?php if (session()->get('message')) : ?>
            <div class="alert alert-warning text-center" role="alert">
                <?= session()->get('message'); ?>
            </div>
        <?php endif; ?>
        </p>
        <br>
        <form action="/login" method="post">
            <div class="input-group mb-3">
                <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="<?= set_value('email'); ?>">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input type="password" class="form-control" placeholder="Password" id="password" name="password" value="<?= set_value('password'); ?>">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-eye"></i>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox small">
                    <input type="checkbox" class="custom-control-input" id="checkbox">
                    <label class="custom-control-label" for="checkbox"> Tampilkan kata sandi</label>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-8">
                    <div class="icheck-primary">
                        <input type="checkbox" id="remember">
                        <label for="remember">
                            Remember Me
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <hr>
        <div class="social-auth-links mt-2 mb-3">
            <a href="register" class="btn btn-sm btn-block btn-primary">
                <i class="fab fa-facebook mr-2"></i>
            </a>
            <a href="register" class="btn btn-sm btn-block btn-danger">
                <i class="fab fa-google-plus mr-2"></i>
            </a>
        </div>
        <!-- /.social-auth-links -->

        <hr>
        <p class="mb-0">
            <a href="/register" class="text-center small">Register a new membership</a>
        </p>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<script>
    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);

    // show hide password
    $(document).ready(function() {
        $('#checkbox').click(function() {
            if ($(this).is(':checked')) {
                $('#password').attr('type', 'text');
            } else {
                $('#password').attr('type', 'password');
            }
        });
    });
</script>

<?= $this->endSection(); ?>