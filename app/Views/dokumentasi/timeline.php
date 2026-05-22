<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<!-- /* ======================================================== -->
<!-- ✨ EXPERT SNAKE ROADMAP TIMELINE (INFOGRAPHIC) ✨ -->
<!-- ======================================================== */ -->

<style>
    .infographic-container {
        max-width: 850px;
        /* Dipersempit agar roadmap tampak padat dan rapi */
        margin: 0 auto;
        padding: 30px 20px;
        background: #fdfdfd;
        border-radius: 15px;
    }

    .title-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .title-header h2 {
        font-size: 14px;
        letter-spacing: 3px;
        color: #888;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .title-header h1 {
        font-size: 32px;
        color: #444;
        font-weight: 800;
        margin-bottom: 10px;
    }

    /* 🐍 SNAKE ROADMAP MAIN TRACK */
    .snake-timeline {
        max-width: 700px;
        margin: 0 auto;
        padding: 20px 0;
        position: relative;
    }

    .snake-group {
        width: 50%;
        position: relative;
        padding: 25px 40px;
        /* Jarak antara garis dan kotak konten */
    }

    /* ➡️ LOOP KANAN (Baris Ganjil) */
    .snake-group:nth-child(odd) {
        margin-left: 50%;
        border-top: 4px solid #cbd5e1;
        border-right: 4px solid #cbd5e1;
        border-bottom: 4px solid #cbd5e1;
        border-radius: 0 80px 80px 0;
        /* Lekukan Ular Kanan */
    }

    /* ⬅️ LOOP KIRI (Baris Genap) */
    .snake-group:nth-child(even) {
        margin-left: 0;
        border-top: 4px solid #cbd5e1;
        border-left: 4px solid #cbd5e1;
        border-bottom: 4px solid #cbd5e1;
        border-radius: 80px 0 0 80px;
        /* Lekukan Ular Kiri */
        margin-top: -4px;
        /* Menggabungkan ujung garis agar nyambung sempurna */
    }

    /* Hapus garis atas elemen pertama agar terlihat seperti titik mulai */
    .snake-group:first-child {
        border-top-color: transparent;
        border-top-right-radius: 0;
    }

    /* 🎯 PIN MARKER (Teardrop Kiri & Kanan) */
    .pin-marker {
        position: absolute;
        top: 50%;
        width: 36px;
        height: 36px;
        border-radius: 50% 50% 50% 0;
        transform: translateY(-50%) rotate(-45deg);
        /* Jarum menghadap ke bawah */
        border: 3px solid #fff;
        box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.15);
        z-index: 2;
    }

    /* Taruh Pin di lekukan terluar masing-masing sisi */
    .snake-group:nth-child(odd) .pin-marker {
        right: -20px;
    }

    .snake-group:nth-child(even) .pin-marker {
        left: -20px;
    }

    /* Lubang Putih di dalam Pin */
    .pin-marker::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 14px;
        height: 14px;
        background: #fff;
        border-radius: 50%;
        transform: translate(-50%, -50%);
    }

    /* 📦 WADAH KONTEN (Mini & Padat) */
    .snake-content {
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        border: 1px solid #f4f4f4;
        transition: transform 0.2s;
    }

    .snake-content:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    /* Aksen Warna di sisi wadah */
    .snake-group:nth-child(odd) .snake-content {
        border-left-width: 4px;
        border-left-style: solid;
    }

    .snake-group:nth-child(even) .snake-content {
        border-right-width: 4px;
        border-right-style: solid;
        text-align: right;
    }

    .date-title {
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #eee;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* 🖼️ GALERI MINI GRID */
    .gallery-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    /* Balik posisi grid jika wadahnya di kiri agar estetik */
    .snake-group:nth-child(even) .gallery-grid {
        justify-content: flex-end;
    }

    .gallery-item {
        position: relative;
        width: 55px;
        /* Ukuran sangat mini & rapi */
        height: 55px;
        border-radius: 6px;
        overflow: hidden;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
        cursor: pointer;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .gallery-item:hover {
        transform: scale(1.2);
        z-index: 10;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* 📥 TOMBOL DOWNLOAD MINI GRID */
    .btn-download-mini {
        position: absolute;
        top: 3px;
        right: 3px;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        font-size: 10px;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        z-index: 15;
        text-decoration: none;
        opacity: 0;
        /* Sembunyikan awalnya */
        transition: opacity 0.2s, background 0.2s;
    }

    /* Munculkan tombol saat kotak foto disentuh mouse */
    .gallery-item:hover .btn-download-mini {
        opacity: 1;
    }

    .btn-download-mini:hover {
        background: #0d6efd;
        /* Biru terang saat di-hover */
        color: #fff;
    }

    .btn-floating-camera {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1050;
        width: 55px;
        /* 🤏 Diperkecil agar lebih proporsional */
        height: 55px;
        /* 🤏 Diperkecil agar lebih proporsional */
        border-radius: 50%;
        font-size: 22px;
        /* 🤏 Ikon disesuaikan */
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;

        /* ✨ Sentuhan Estetika: Warna gradien biru ke cyan */
        background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4), inset 0 -2px 5px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;

        /* 💓 Animasi denyut halus menarik perhatian tanpa norak */
        animation: pulse-glow 2.5s infinite;
    }

    /* ✨ Efek saat kursor di atas tombol (untuk pengguna laptop/desktop) */
    .btn-floating-camera:hover {
        background: linear-gradient(135deg, #0b5ed7 0%, #0bacce 100%);
        transform: translateX(-50%) translateY(-3px);
        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.6);
        color: #ffffff;
    }

    /* ✨ Efek membal saat tombol ditekan (Mobile/Touch) */
    .btn-floating-camera:active {
        transform: translateX(-50%) scale(0.92);
    }

    /* ✨ Keyframes untuk animasi denyut bayangan */
    @keyframes pulse-glow {
        0% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.5);
        }

        70% {
            box-shadow: 0 0 0 12px rgba(13, 110, 253, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
        }
    }

    /* 📱 RESPONSIVE: Luruskan jadi vertikal biasa jika di HP */
    @media screen and (max-width: 768px) {
        .snake-group {
            width: 100%;
            margin-left: 0 !important;
            border-left: 4px solid #cbd5e1 !important;
            border-right: none !important;
            border-top: none !important;
            border-bottom: none !important;
            border-radius: 0 !important;
            padding: 15px 0 15px 35px;
            margin-top: 0 !important;
        }

        .snake-group .pin-marker {
            left: -20px !important;
            right: auto !important;
        }

        .snake-group:nth-child(even) .snake-content {
            text-align: left;
            border-right: none;
            border-left-width: 4px;
            border-left-style: solid;
        }

        .snake-group:nth-child(even) .gallery-grid {
            justify-content: flex-start;
        }
    }
</style>

<div class="container-fluid mb-5">
    <div class="infographic-container shadow-sm">

        <div class="title-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">📸 Timeline Dokumentasi Petugas</h4>
                    <small class="text-muted">Jejak historis kegiatan lapangan</small>
                </div>

                <div>
                    <button type="button" class="btn-floating-camera" data-bs-toggle="modal" data-bs-target="#modalDokumentasi" title="Tambah Dokumentasi">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
            </div>
        </div>

        <?php if (empty($timelineData)): ?>
            <div class="text-center p-5">
                <h4 class="text-muted">Belum ada dokumentasi yang terekam.</h4>
            </div>
        <?php else: ?>

            <div class="snake-timeline">
                <?php
                function tgl_indo($tanggal)
                {
                    $bulan = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des');
                    $pecahkan = explode('-', $tanggal);
                    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
                }

                // Palet Warna: Orange, Tosca, Ungu, Biru (Seperti Gambar Referensi Mbah)
                $colors = ['#f39c12', '#00aba9', '#8e44ad', '#2980b9', '#27ae60', '#e74c3c'];

                $i = 0;
                foreach ($timelineData as $tanggal => $items):
                    $color = $colors[$i % count($colors)];
                ?>

                    <div class="snake-group">
                        <div class="pin-marker" style="background: <?= $color; ?>;"></div>

                        <div class="snake-content" style="border-color: <?= $color; ?>;">

                            <div class="date-title d-flex justify-content-between align-items-center" style="color: <?= $color; ?>;">
                                <div>
                                    <?= tgl_indo($tanggal); ?>
                                    <span class="badge badge-light ml-2 text-muted" style="font-size: 11px;">
                                        <?= count($items); ?> Foto
                                    </span>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="unduhSemuaFoto('<?= $tanggal; ?>')" style="font-size: 10px; border-radius: 4px;">
                                    <i class="fas fa-cloud-download-alt"></i> Unduh Semua
                                </button>
                            </div>

                            <div class="gallery-grid">
                                <?php foreach ($items as $item):
                                    $fotoPath = base_url('uploads/dokumentasi/' . $item['foto_path']);
                                    $jam = date('H:i', strtotime($item['created_at']));
                                    $tooltipText = "Kegiatan: " . esc($item['jenis_kegiatan']) . "\nPetugas: " . esc($item['nama_petugas']) . "\nJam: " . $jam . " WIB";

                                    // 🚀 KEMBALIKAN TOMBOL DOWNLOAD DI DALAM LIGHTBOX
                                    $lightboxTitle = esc($item['jenis_kegiatan']) . " (" . esc($item['nama_petugas']) . ") &nbsp; <a href='" . $fotoPath . "' download='" . $item['foto_path'] . "' class='btn btn-sm btn-primary py-0 px-2 text-white' style='margin-left: 10px;'><i class='fas fa-download'></i> Download</a>";
                                ?>
                                    <div class="gallery-item" style="border-color: <?= $color; ?>;">
                                        <a href="<?= $fotoPath; ?>"
                                            class="foto-tgl-<?= $tanggal; ?>"
                                            download="<?= $item['foto_path']; ?>"
                                            data-lightbox="road-<?= $tanggal; ?>"
                                            data-title="<?= $lightboxTitle; ?>">
                                            <img src="<?= $fotoPath; ?>" alt="Doc" title="<?= $tooltipText; ?>">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    </div>

                <?php
                    $i++;
                endforeach;
                ?>
            </div>

        <?php endif; ?>

    </div>
</div>

<script>
    // ==========================================
    // 📥 FUNGSI UNDUH SEMUA FOTO PER TANGGAL
    // ==========================================
    function unduhSemuaFoto(tanggal) {
        // Cari semua link foto yang memiliki class sesuai tanggal
        const links = document.querySelectorAll('.foto-tgl-' + tanggal);

        if (links.length === 0) return;

        // Munculkan notifikasi SweetAlert
        Swal.fire({
            icon: 'info',
            title: 'Mengunduh ' + links.length + ' Foto...',
            text: 'Mohon tunggu sebentar. Jika browser meminta izin download ganda (Multiple Files), silakan klik "Allow/Izinkan".',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        // Loop dan download dengan jeda 500ms (0.5 detik) agar tidak diblokir browser
        links.forEach((link, index) => {
            setTimeout(() => {
                // Buat elemen anchor bayangan
                const a = document.createElement('a');
                a.href = link.href;
                a.download = link.getAttribute('download'); // Gunakan nama asli dari database

                // Tempel ke body, klik, lalu cabut lagi
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }, index * 500);
        });
    }
</script>

<?= $this->endSection(); ?>