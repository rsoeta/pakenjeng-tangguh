<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-8 col-md-7">
            <div class="card o-hidden border-0 shadow-lg mt-1">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <!-- <div class="col-lg-6 d-none d-lg-block bg-login-image"></div> -->
                        <div class="col-lg-12">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900">- Opr NewDTKS -</h1>
                                    <h2 class="h4 text-gray-900">Register</h2>
                                </div>
                                <hr>
                                <!-- pesan validasi error -->
                                <?php $errors = session()->getFlashdata('errors');
                                if (!empty($errors)) { ?>

                                    <div class="alert alert-danger" role="alert">
                                        <ul>
                                            <?php foreach ($errors as $error) : ?>
                                                <li><?= esc($error) ?></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>

                                <?php } ?>
                                <?= form_open('dtks/auth/save_register'); ?>
                                <?= csrf_field(); ?>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" name="fullname" aria-describedby="emailHelp" placeholder="Enter your Fullname...">
                                </div>
                                <div class="form-group">
                                    <input type="numeric" class="form-control form-control-user" name="nik" aria-describedby="emailHelp" placeholder="Enter your NIK...">
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" name="email" aria-describedby="emailHelp" placeholder="Enter an Email address...">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" name="password" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" name="pass_confirm" placeholder="Password confirm">
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck">
                                        <label class="custom-control-label" for="customCheck">Remember
                                            Me</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Sign Up
                                </button>
                                <hr>
                                <a href="" class="btn btn-google btn-user btn-block">
                                    <i class="fab fa-google fa-fw"></i> Sign up with Google
                                </a>
                                <?= form_close(); ?>
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