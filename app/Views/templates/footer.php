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

<div class="modal fade" id="modalDokumentasi" data-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-camera retro mr-2"></i> Laporan Dokumentasi Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formUploadDokumentasi" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> Sistem akan otomatis menambahkan Watermark (Nama, Waktu, & Lokasi) pada foto Anda.
                    </div>

                    <div class="form-group">
                        <label>Jenis Kegiatan <span class="text-danger">*</span></label>
                        <select name="jenis_kegiatan" id="selectKegiatan" class="form-control" required>
                            <option value="">-- Memuat Data... --</option>
                        </select>
                    </div>

                    <div id="previewContainer" class="text-center mt-3" style="display: none;">
                        <img id="previewFoto" src="" class="img-fluid rounded shadow-sm" style="max-height: 250px; border: 2px solid #ddd;" alt="Preview Foto">
                    </div>

                    <div class="form-group">
                        <label>Foto Dokumentasi <span class="text-danger">*</span></label>
                        <input type="file" name="foto" id="inputFotoDoc" class="form-control-file" accept="image/*" capture="environment" required>
                    </div>

                    <input type="hidden" name="latitude" id="doc_lat">
                    <input type="hidden" name="longitude" id="doc_lng">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Nanti Saja</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveDoc">
                        <i class="fas fa-upload mr-1"></i> Upload & Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // 1. PENGATURAN WAKTU
        // Ubah angka di bawah ini (30 menit = 30 * 60 detik * 1000 milidetik = 1800000)
        const REMINDER_TIME = 30 * 60 * 1000;

        // (Opsional) Mbah juga bisa menyesuaikan masa tenggang/snooze jika tombol "Nanti Saja" ditekan
        // Misalnya dibuat 15 Menit agar tidak terlalu cepat mengganggu lagi
        const SNOOZE_DURATION = 15 * 60 * 1000;

        setTimeout(function() {
            let lastUpload = localStorage.getItem('last_doc_upload');
            let snoozeTime = localStorage.getItem('snooze_doc_upload');
            let today = new Date().toISOString().split('T')[0];
            let now = Date.now();

            // 🛡️ CEK 1: Apakah hari ini sudah berhasil upload? Jika sudah, diam.
            if (lastUpload === today) return;

            // 🛡️ CEK 2: Apakah sedang dalam masa tenggang (Snooze)? Jika ya, diam.
            if (snoozeTime && (now - parseInt(snoozeTime)) < SNOOZE_DURATION) {
                console.log("Sedang masa tenggang/snooze. Alert ditahan.");
                return;
            }

            // Jika lolos kedua cek di atas, tampilkan Alert!
            Swal.fire({
                title: 'Waktunya Dokumentasi!',
                text: "Jangan lupa untuk mengupload foto kegiatan Anda sebagai laporan hari ini ya.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Upload Sekarang',
                cancelButtonText: 'Nanti Saja'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#modalDokumentasi').modal('show');
                    getLocation(); // Ambil koordinat GPS
                } else {
                    // 🚀 FITUR BARU: Catat waktu Snooze jika klik "Nanti Saja" / ditutup
                    localStorage.setItem('snooze_doc_upload', Date.now());

                    Swal.fire({
                        title: 'Ditunda',
                        text: 'Saya akan mengingatkan Anda lagi beberapa saat ke depan.',
                        icon: 'warning',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }, REMINDER_TIME);

        // 2. FUNGSI AMBIL GPS HP/LAPTOP
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    $('#doc_lat').val(position.coords.latitude);
                    $('#doc_lng').val(position.coords.longitude);
                }, function(error) {
                    console.log("GPS Ditolak/Error: ", error);
                });
            }
        }

        // 3. FUNGSI SUBMIT FORM VIA AJAX
        $('#formUploadDokumentasi').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $('#btnSaveDoc').html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);

            $.ajax({
                url: '/dokumentasi/upload', // Endpoint backend Mbah nanti
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#btnSaveDoc').html('<i class="fas fa-upload mr-1"></i> Upload & Simpan').prop('disabled', false);
                    if (response.success) {
                        $('#modalDokumentasi').modal('hide');
                        // Simpan jejak agar hari ini tidak ditanya lagi
                        localStorage.setItem('last_doc_upload', new Date().toISOString().split('T')[0]);

                        Swal.fire('Berhasil!', 'Dokumentasi berhasil disimpan dengan Watermark.', 'success');
                        $('#formUploadDokumentasi')[0].reset();
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function() {
                    $('#btnSaveDoc').html('<i class="fas fa-upload mr-1"></i> Upload & Simpan').prop('disabled', false);
                    Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                }
            });
        });

        // 1. 🚀 AMBIL DATA DROPDOWN KEGIATAN DARI DATABASE
        $.ajax({
            url: '/dokumentasi/kegiatan',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                let options = '<option value="">-- Pilih Kegiatan --</option>';
                data.forEach(function(item) {
                    options += `<option value="${item.nama_kegiatan}">${item.nama_kegiatan}</option>`;
                });
                $('#selectKegiatan').html(options);
            }
        });

        // 2. 🚀 FITUR PREVIEW FOTO SEBELUM UPLOAD
        $('#inputFotoDoc').on('change', function(e) {
            let file = e.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewFoto').attr('src', e.target.result);
                    $('#previewContainer').fadeIn('fast');
                }
                reader.readAsDataURL(file);
            } else {
                $('#previewContainer').fadeOut('fast');
                $('#previewFoto').attr('src', '');
            }
        });

        // 3. Reset form dan preview setelah sukses upload (tambahkan di dalam success ajax form Mbah)
        // $('#previewContainer').hide(); 
        // $('#previewFoto').attr('src', '');

    });

    // Load Bootstrap 5 secara DINAMIS agar tidak diambil alih AdminLTE
    let bs5Script = document.createElement("script");
    bs5Script.src = "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js";
    bs5Script.onload = () => {
        window.BS5 = bootstrap;
        console.log("🔥 Bootstrap 5 loaded in isolated mode!");
    };
    document.body.appendChild(bs5Script);

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

    function startWIBClock() {
        const timeEl = document.getElementById('wibTime');
        const dateEl = document.getElementById('wibDate');

        if (!timeEl || !dateEl) return;

        const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        function updateClock() {
            const now = new Date(
                new Date().toLocaleString('en-US', {
                    timeZone: 'Asia/Jakarta'
                })
            );

            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');

            const dayName = hari[now.getDay()];
            const date = String(now.getDate()).padStart(2, '0');
            const monthName = bulan[now.getMonth()];
            const year = now.getFullYear();

            timeEl.textContent = `${h}:${m}:${s}`;
            dateEl.textContent = `${dayName}, ${date} ${monthName} ${year}`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    }

    // jalankan setelah DOM siap
    document.addEventListener('DOMContentLoaded', startWIBClock);
</script>

</body>

</html>