<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>
<style>
    /* CSS Kustom untuk Timeline Vertical */
    .sinden-timeline {
        position: relative;
        padding-left: 3rem;
        margin-top: 1rem;
        margin-bottom: 2rem;
    }

    .sinden-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #dee2e6;
        /* Warna garis abu-abu */
        border-radius: 3px;
    }

    .timeline-date-label {
        font-weight: 700;
        background: #0d6efd;
        /* Biru Primary */
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 1.5rem;
        margin-left: -3rem;
        /* Tarik ke kiri menimpa garis */
        position: relative;
        z-index: 2;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }

    .timeline-dot {
        position: absolute;
        left: -3.15rem;
        /* Posisikan pas di tengah garis */
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #ffc107;
        /* Kuning Warning */
        top: 10px;
        border: 4px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
        z-index: 2;
    }

    .timeline-card {
        border-left: 4px solid #0d6efd;
        transition: transform 0.2s ease-in-out;
    }

    .timeline-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
</style>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="m-0 fw-bold"><i class="fas fa-camera-retro text-primary mr-2"></i> <?= $title; ?></h4>
                    <p class="text-muted">Daftar dokumentasi petugas secara kronologis</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?= $title; ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow mb-4 border-0">
                <div class="card-body bg-light">

                    <?php if (empty($timelineData)): ?>
                        <div class="text-center p-5">
                            <img src="<?= base_url('assets/img/undraw_empty.svg'); ?>" alt="Kosong" width="150" class="mb-3 opacity-50">
                            <h5 class="text-muted">Belum ada dokumentasi yang masuk.</h5>
                        </div>
                    <?php else: ?>

                        <div class="sinden-timeline">
                            <?php
                            // Fungsi bantuan untuk mengubah Y-m-d menjadi format Indo (misal: 17 Mei 2026)
                            function tgl_indo($tanggal)
                            {
                                $bulan = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                                $pecahkan = explode('-', $tanggal);
                                return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
                            }

                            foreach ($timelineData as $tanggal => $items):
                            ?>
                                <div class="timeline-date-label">
                                    <i class="far fa-calendar-alt mr-1"></i> <?= tgl_indo($tanggal); ?>
                                </div>

                                <?php foreach ($items as $item): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <div class="card shadow-sm timeline-card border-0">
                                            <div class="card-body p-3">
                                                <div class="row align-items-center">

                                                    <div class="col-md-3 col-sm-4 text-center text-sm-left mb-3 mb-sm-0">
                                                        <?php
                                                        $fotoPath = base_url('uploads/dokumentasi/' . $item['foto_path']);
                                                        // Ambil Jam saja
                                                        $jam = date('H:i', strtotime($item['created_at']));
                                                        ?>
                                                        <a href="<?= $fotoPath; ?>" data-lightbox="timeline-doc" data-title="<?= esc($item['nama_petugas']) . ' - ' . esc($item['jenis_kegiatan']); ?>">
                                                            <img src="<?= $fotoPath; ?>" class="img-fluid rounded shadow-sm" style="max-height: 120px; object-fit: cover; border: 2px solid #fff;" alt="Dokumentasi">
                                                        </a>
                                                    </div>

                                                    <div class="col-md-9 col-sm-8">
                                                        <h5 class="text-dark font-weight-bold mb-1">
                                                            <?= esc($item['jenis_kegiatan']); ?>
                                                        </h5>
                                                        <div class="text-muted small mb-2">
                                                            <i class="far fa-clock text-warning"></i> Jam <?= $jam; ?> WIB
                                                        </div>

                                                        <div class="d-flex align-items-center mb-1">
                                                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center mr-2" style="width: 30px; height: 30px;">
                                                                <i class="fas fa-user-tie"></i>
                                                            </div>
                                                            <span class="font-weight-bold text-gray-800"><?= esc($item['nama_petugas']); ?></span>
                                                        </div>

                                                        <p class="small text-muted mt-2 mb-0 border-top pt-2">
                                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                                            GPS: <?= esc($item['latitude']); ?>, <?= esc($item['longitude']); ?>
                                                        </p>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            <?php endforeach; ?>
                        </div> <?php endif; ?>

                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection(); ?>