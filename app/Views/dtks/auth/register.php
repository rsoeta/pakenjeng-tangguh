<?= $this->extend('dtks/auth/templates/index'); ?>
<?= $this->section('content'); ?>

<div class="min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-lg border-0 rounded-3" id="theme-card" style="max-width: 400px; width: 100%; background-color: var(--card-bg, #fff);">
        <div class="card-body text-center p-4">

            <!-- Toggle Theme -->
            <button id="toggleTheme" class="btn btn-outline-secondary btn-sm mb-3" style="border-radius: 10px;">
                🌙 Mode Gelap
            </button>

            <!-- Logo -->
            <div class="mb-3">
                <img id="appLogo" src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="Logo SINDEN" style="height: 80px; width: auto;">
            </div>

            <h3 class="text-teal font-weight-bold mb-0" id="titleText">SINDEN</h3>
            <p class="text-muted small mb-4">Sistem Informasi Data Ekonomi dan Sosial Desa</p>

            <!-- Alert -->
            <?php if (session()->get('success')) : ?>
                <div class="alert alert-success text-center small"><?= session()->get('success'); ?></div>
            <?php endif; ?>
            <?php if (session()->get('message')) : ?>
                <div class="alert alert-warning text-center small"><?= session()->get('message'); ?></div>
            <?php endif; ?>
            <?php if (isset($validation)) : ?>
                <div class="alert alert-danger text-center small"><?= $validation->listErrors(); ?></div>
            <?php endif; ?>

            <!-- Form -->
            <form action="/register" method="POST">
                <?= csrf_field(); ?>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="fullname" placeholder="Nama Lengkap" value="<?= set_value('fullname'); ?>">
                    <label>Nama Lengkap</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="number" class="form-control" name="nik" placeholder="Nomor KTP/NIK" value="<?= set_value('nik'); ?>">
                    <label>Nomor KTP/NIK</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="number" class="form-control" name="nope" placeholder="Nomor Handphone" value="<?= set_value('nope'); ?>">
                    <label>Nomor Handphone</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" name="email" placeholder="Alamat Email" value="<?= set_value('email'); ?>">
                    <label>Alamat Email</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-control" name="kelurahan">
                        <option value="">-- Pilih Desa / Kelurahan --</option>
                        <?php foreach ($desa as $row): ?>
                            <option value="<?= $row['id'] ?>" <?= set_select('kelurahan', $row['id']); ?>><?= $row['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Desa / Kelurahan</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-control" name="no_rw">
                        <option value="">-- Pilih RW --</option>
                        <?php foreach ($datarw as $row): ?>
                            <option value="<?= $row['no_rw'] ?>" <?= set_select('no_rw', $row['no_rw']); ?>><?= $row['no_rw']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Nomor RW</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    <label>Password</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Ulangi Password">
                    <label>Ulangi Password</label>
                </div>

                <div class="text-start mb-3">
                    <input type="checkbox" id="checkbox">
                    <label for="checkbox" class="small">Tampilkan kata sandi</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3" style="background-color: #2EC4B6; border:none;">Daftar</button>
            </form>

            <p class="small mb-0">
                <a href="<?= base_url('login'); ?>" id="linkLogin" class="text-decoration-none fw-bold" style="color:#FFBE0B;">Sudah punya akun?</a>
            </p>
        </div>
    </div>
</div>

<!-- Toggle Password -->
<script>
    document.getElementById('checkbox').addEventListener('click', function() {
        const p1 = document.getElementById('password');
        const p2 = document.getElementById('password_confirm');
        const type = p1.type === 'password' ? 'text' : 'password';
        p1.type = type;
        p2.type = type;
    });
</script>

<!-- Universal Dark Mode -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById("toggleTheme");
        const card = document.getElementById("theme-card");
        const body = document.body;
        const linkLogin = document.getElementById("linkLogin");

        const currentTheme = localStorage.getItem("themeMode") || "light";
        applyTheme(currentTheme);

        toggleBtn.addEventListener("click", function() {
            const newTheme = body.classList.contains("dark-mode") ? "light" : "dark";
            applyTheme(newTheme);
            localStorage.setItem("themeMode", newTheme);
        });

        function applyTheme(mode) {
            if (mode === "dark") {
                body.classList.add("dark-mode");
                card.style.backgroundColor = "#1e1e1e";
                card.classList.add("text-light");
                toggleBtn.innerHTML = "☀️ Mode Terang";
                linkLogin.style.color = "#FFBE0B";
            } else {
                body.classList.remove("dark-mode");
                card.style.backgroundColor = "#fff";
                card.classList.remove("text-light");
                toggleBtn.innerHTML = "🌙 Mode Gelap";
                linkLogin.style.color = "#FFBE0B";
            }
        }
    });
</script>

<?= $this->endSection(); ?>