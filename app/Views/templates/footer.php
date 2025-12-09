<footer class="main-footer">
    <strong>&copy; <?= date('Y'); ?> <a href="<?= base_url(); ?>"><?= nameApp(); ?></a>.</strong>
    Version <strong><?php echo versionApp(); ?>-<a href="https://twitter.com/riansutarsa" target="blank">rs</a></strong>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>

<!-- ===================== -->
<!-- JAVASCRIPT — FOOTER -->
<!-- ===================== -->

<script src="<?= base_url('assets/plugins/select2/js/select2.full.min.js'); ?>"></script>

<script src="<?= base_url('assets/lightbox/dist/js/lightbox.min.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/js/all.min.js"></script>

<!-- overlayScrollbars -->
<script src="<?= base_url('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'); ?>"></script>
<!-- AdminLTE -->
<script src="<?= base_url('assets/dist/js/adminlte.js'); ?>"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="<?= base_url('assets/plugins/jquery-mousewheel/jquery.mousewheel.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/raphael/raphael.min.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/jquery-mapael/jquery.mapael.min.js'); ?>"></script>
<script src="<?= base_url('assets/plugins/jquery-mapael/maps/usa_states.min.js'); ?>"></script>

<!-- AdminLTE for demo purposes -->
<script src="<?= base_url('assets/dist/js/demo.js'); ?>"></script>

<script>
    // Load Bootstrap 5 secara DINAMIS agar tidak diambil alih AdminLTE
    let bs5Script = document.createElement("script");
    bs5Script.src = "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js";
    bs5Script.onload = () => {
        window.BS5 = bootstrap;
        console.log("🔥 Bootstrap 5 loaded in isolated mode!");
    };
    document.body.appendChild(bs5Script);
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.main-header');
        const contentWrapper = document.querySelector('.content-wrapper');

        function setDynamicPadding() {
            if (!navbar || !contentWrapper) return;

            const navbarHeight = navbar.offsetHeight;
            const compactPadding = window.innerWidth <= 768 ? 4 : 8; // jarak minimal pixel

            // gunakan CSS variable supaya bisa diatur ulang lewat media query
            document.body.style.setProperty('--navbar-height', `${navbarHeight - compactPadding}px`);
        }

        // Jalankan awal & saat resize
        setDynamicPadding();
        window.addEventListener('resize', setDynamicPadding);

        // Jalankan juga setelah toggle sidebar
        const toggleMenu = document.querySelector('[data-widget="pushmenu"]');
        if (toggleMenu) {
            toggleMenu.addEventListener('click', () => setTimeout(setDynamicPadding, 300));
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleTheme');
        const body = document.body;

        // Cek preferensi sebelumnya dari localStorage
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme === 'dark') {
            body.classList.add('dark-mode');
            toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
            toggleBtn.classList.remove('btn-outline-light');
            toggleBtn.classList.add('btn-outline-warning');
        }

        toggleBtn.addEventListener('click', function() {
            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
                toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
                toggleBtn.classList.remove('btn-outline-light');
                toggleBtn.classList.add('btn-outline-warning');
            } else {
                localStorage.setItem('theme', 'light');
                toggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
                toggleBtn.classList.remove('btn-outline-warning');
                toggleBtn.classList.add('btn-outline-light');
            }
        });
    });
</script>

<!-- tambahkan script untuk SweetAlert2 -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleTheme');
        if (!toggleBtn) return; // prevent error

        toggleBtn.addEventListener('click', function() {
            const isDark = document.body.classList.contains('dark-mode');
            Swal.fire({
                toast: true,
                position: 'top-end',
                timer: 1200,
                showConfirmButton: false,
                icon: isDark ? 'info' : 'success',
                title: isDark ? '🌙 Mode Gelap Aktif' : '☀️ Mode Terang Aktif',
                background: isDark ? '#182c25' : '#fff',
                color: isDark ? '#f8f9fa' : '#333',
            });
        });
    });
</script>

<script>
    // ============================================================
    // GLOBAL CSRF HANDLER — AUTO REFRESH TOKEN UNTUK SEMUA AJAX
    // ============================================================

    // Ambil token awal dari meta tag
    let csrfName = document.querySelector('meta[name="csrf-token-name"]').getAttribute("content");
    let csrfValue = document.querySelector('meta[name="csrf-token-value"]').getAttribute("content");

    // Simpan ke window global
    window.CSRF = {
        name: csrfName,
        value: csrfValue
    };

    // ------------------------------
    // Inject token ke setiap request AJAX (jQuery)
    // ------------------------------
    if (typeof $ !== "undefined") {
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (settings.type === "POST") {
                    // Untuk form_params atau x-www-form-urlencoded
                    if (typeof settings.data === "string") {
                        settings.data += `&${window.CSRF.name}=${window.CSRF.value}`;
                    }
                }
            }
        });

        // Tangkap token baru dari semua response
        $(document).ajaxComplete(function(event, xhr) {
            try {
                let token = xhr.getResponseHeader("X-CSRF-TOKEN");
                if (token) {
                    window.CSRF.value = token;
                    document.querySelector('meta[name="csrf-token-value"]').setAttribute("content", token);
                }
            } catch (e) {
                console.warn("Tidak bisa update CSRF token:", e);
            }
        });
    }

    // ------------------------------
    // Untuk Fetch API (opsional tapi direkomendasikan)
    // ------------------------------
    window.secureFetch = async function(url, options = {}) {
        options.headers = options.headers || {};

        // Jika POST maka sertakan token
        if (options.method && options.method.toUpperCase() === "POST") {
            if (options.headers["Content-Type"] === "application/json") {
                let body = JSON.parse(options.body || "{}");
                body[window.CSRF.name] = window.CSRF.value;
                options.body = JSON.stringify(body);
            } else {
                // FormUrlEncoded
                let form = new URLSearchParams(options.body || "");
                form.append(window.CSRF.name, window.CSRF.value);
                options.body = form.toString();
            }
        }

        let response = await fetch(url, options);

        // Ambil token baru dari header response
        let newToken = response.headers.get("X-CSRF-TOKEN");
        if (newToken) {
            window.CSRF.value = newToken;
            document.querySelector('meta[name="csrf-token-value"]').setAttribute("content", newToken);
        }

        return response;
    };
</script>

</body>

</html>