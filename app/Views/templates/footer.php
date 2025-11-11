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

<script src="<?= base_url('assets/plugins/select2/js/select2.full.min.js'); ?>"></script>

<!-- ./wrapper -->

<!-- Bootstrap -->
<script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/lightbox/dist/js/lightbox.min.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/js/all.min.js"></script>

<!-- overlayScrollbars -->
<script src="<?= base_url('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'); ?>"></script>
<!-- AdminLTE App -->
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

</body>

</html>