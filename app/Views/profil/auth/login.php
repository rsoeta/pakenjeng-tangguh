<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-4 col-lg-8 col-md-7 col-xs-12">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <!-- <div class="col-lg-6 d-none d-lg-block bg-login-image"></div> -->
                        <div class="col-lg-12">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900">- Opr NewDTKS -</h1>
                                    <h2 class="h4 text-gray-900">Login</h2>
                                    <hr>
                                    <!-- pesan validasi sukses register -->
                                    <?php
                                    if (session()->getFlashdata('info')) { ?>
                                        <div class="alert alert-success" role="alert">
                                            <?=
                                            session()->getFlashdata('info');
                                            ?>
                                        </div>
                                    <?php } ?>

                                    <!-- pesan validasi sukses register -->
                                    <?php
                                    if (session()->getFlashdata('pesan')) { ?>
                                        <div class="alert alert-warning" role="alert">
                                            <?=
                                            session()->getFlashdata('pesan');
                                            ?>
                                        </div>
                                    <?php } ?>

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
                                </div>
                                <?= form_open('/dtks/auth/cek_login'); ?>
                                <?= csrf_field(); ?>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter Email Address...">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck">
                                        <label class="custom-control-label" for="customCheck">Remember
                                            Me</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Login
                                </button>
                                <hr>
                                <a href="" class="btn btn-google btn-user btn-block">
                                    <i class="fab fa-google fa-fw"></i> Login with Google
                                </a>
                                <?= form_close(); ?>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="/dtks/auth/register">Create an Account!</a>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="/">Back to Home</a>
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