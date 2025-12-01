    <div class="card-body">
        <div class="tab-content" id="wa-tabContent">

            <button id="btnMigration" class="btn btn-primary">
                Jalankan Migrasi DB
            </button>

            <button id="btnDownloadDb" class="btn btn-success">
                Unduh Database
            </button>

        </div>
    </div>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('btnMigration').addEventListener('click', function() {
            Swal.fire({
                title: 'Jalankan Migrasi Database?',
                text: "Pastikan tidak ada proses yang berjalan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, jalankan!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('admin/migration/run') ?>";
                }
            });
        });

        document.getElementById('btnDownloadDb').addEventListener('click', function() {
            Swal.fire({
                title: 'Unduh Backup Database?',
                text: "File backup akan diunduh dalam format .sql",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Unduh Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('admin/download-db') ?>";
                }
            });
        });
    </script>

    <?php if (session()->getFlashdata('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= session()->getFlashdata('success'); ?>'
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= session()->getFlashdata('error'); ?>'
            });
        </script>
    <?php endif; ?>