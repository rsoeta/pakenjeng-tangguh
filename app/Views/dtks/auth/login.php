<?= $this->extend('dtks/auth/templates/index'); ?>

<?= $this->section('content'); ?>

<!-- captha -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>

<script src="https://www.google.com/recaptcha/api.js?render=reCAPTCHA_site_key"></script>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

<style>
    .error {
        color: red;
    }
</style>
<!-- captha -->


<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="<?= base_url(); ?>">
                <img src="<?= base_url('icon-dtks.png'); ?>" alt="Logo" style="height: 50%; width: 50%;">
            </a>
        </div>
        <div class="card-body">
            <!-- <p class="login-box-msg"> -->
            <?php if (session()->get('success')) : ?>
                <div class="alert alert-success text-center" role="alert">
                    <?= session()->get('success'); ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('message') && is_array(session()->getFlashdata('message'))) : ?>
                <div class="alert alert-warning text-center" role="alert">
                    <?= session()->getFlashdata('message')['text']; ?>
                </div>
            <?php endif; ?>
            <!-- </p> -->
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
                            <i class="fas fa-eye" id="checkbox"></i>
                        </div>
                    </div>
                </div>
                <div class="g-recaptcha" data-sitekey="6LctvBomAAAAAGjg0x7rNMuW9c5BOZfP-ev4E6b5"></div>
                <div class="row mt-3">
                    <div class="col-8">
                        <!-- <div class="icheck-primary">
                        <input type="checkbox" id="remember">
                        <label for="remember">
                            Remember Me
                        </label>
                    </div> -->
                    </div>
                    <!-- /.col -->
                    <!-- /.col -->
                </div>
                <hr>
                <div class="social-auth-links mt-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                    <a href="lupa-password" class="btn btn-sm btn-block btn-danger">Lupa Password</a>
                </div>
            </form>

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
    // $(document).ready(function() {
    //     $('#checkbox').click(function() {
    //         if ($(this).is(':checked')) {
    //             $('#password').attr('type', 'text');
    //         } else {
    //             $('#password').attr('type', 'password');
    //         }
    //     });
    // });
    // Mendapatkan referensi ke elemen-elemen yang diperlukan
    var passwordInput = document.getElementById("password");
    var checkbox = document.getElementById("checkbox");

    // Menambahkan event listener untuk mengubah tipe input saat kotak centang diklik
    checkbox.addEventListener("click", function() {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            checkbox.classList.remove("fa-eye");
            checkbox.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            checkbox.classList.remove("fa-eye-slash");
            checkbox.classList.add("fa-eye");
        }
    });
</script>
<?php if (session()->getFlashdata('message')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const message = <?= json_encode(session()->getFlashdata('message')) ?>;

            // Tampilkan SweetAlert2 untuk pesan
            Swal.fire({
                icon: message.type,
                title: message.type === 'success' ? 'Berhasil' : 'Gagal',
                text: message.text,
                timer: 3000,
                showConfirmButton: false
            });

            // Redirect ke Gmail hanya jika context adalah 'requestReset'
            if (message.type === 'success' && message.context === 'requestReset') {
                setTimeout(function() {
                    window.location.href = 'https://mail.google.com/';
                }, 3000); // Redirect setelah 3 detik
            }
        });
    </script>
<?php endif; ?>

<?= $this->endSection(); ?>