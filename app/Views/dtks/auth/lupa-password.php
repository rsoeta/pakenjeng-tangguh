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
                            <div class="p-2">
                                <div class="text-center">
                                    <div class="card-header text-center">
                                        <a href="<?= base_url(); ?>">
                                            <img src="<?= base_url('icon-dtks.png'); ?>" alt="Logo" style="height: 50%; width: 50%;">
                                        </a>
                                    </div>
                                    <h2 class="h5 text-gray-900"><?= $title; ?></h2>
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
                                <?php if (isset($validation)) : ?>
                                    <div class="col-12 mb-2" style="background-color: darkorange; border-radius: 3px; padding: 10px;">
                                        <div class="col">
                                            <div class="container">
                                                <?= $validation->listErrors(); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form action="/requestReset" method="POST">
                                    <?= csrf_field(); ?>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="fullname">Nama</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="text" class="form-control form-control-sm form-control-user" name="fullname" aria-describedby="emailHelp" placeholder="Masukan Nama Lengkap" value="<?= set_value('fullname'); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="nik">NIK</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="numeric" class="form-control form-control-sm form-control-user" name="nik" aria-describedby="emailHelp" placeholder="Masukan No. KTP/NIK" value="<?= set_value('nik'); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="nope">No. HP</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="numeric" class="form-control form-control-sm form-control-user" name="nope" aria-describedby="emailHelp" placeholder="Masukan No. Handphone" value="<?= set_value('nope'); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row nopadding">
                                        <label class="col-4 col-sm-4 col-form-label" for="email">Email</label>
                                        <div class="col-8 col-sm-8">
                                            <input type="email" class="form-control form-control-sm form-control-user" name="email" aria-describedby="emailHelp" placeholder="Masukan Email" value="<?= set_value('email'); ?>">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-block">
                                        Reset Password
                                    </button>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="<?= base_url('login'); ?>" style="color: black; font-weight:bold">Sudah punya Akun!</a>
                                    </div>
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

    $('document').ready(function() {
        var pwd1 = $("#password");
        var pwd2 = $("#password_confirm");
        $('#checkbox').click(function() {
            if (pwd1.attr('type') === "password" && pwd2.attr('type') === "password") {
                pwd1.attr('type', 'text') && pwd2.attr('type', 'text');
            } else {
                pwd1.attr('type', 'password') && pwd2.attr('type', 'password');
            }
        });

        if ($('#countdown').length) {
            start_countdown();
        }
    });
</script>

<?php if (session()->getFlashdata('message')) : ?>
    <script>
        Swal.fire({
            icon: '<?= session()->getFlashdata('message')['type'] ?>',
            title: '<?= session()->getFlashdata('message')['type'] === 'success' ? 'Berhasil' : 'Gagal' ?>',
            text: '<?= session()->getFlashdata('message')['text'] ?>',
            showConfirmButton: true,
            timer: 3000,
            width: '300px'
        }).then(() => {
            <?php if (session()->getFlashdata('message')['type'] === 'success') : ?>
                // Redirect ke Gmail hanya jika pesan sukses
                window.location.href = 'https://mail.google.com/';
            <?php endif; ?>
        });
    </script>
<?php endif; ?>


<?= $this->endSection(); ?>