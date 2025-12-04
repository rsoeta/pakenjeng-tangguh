<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Pemeriksaan Data ART & KK</h4>
    </div>

    <section class="content">
        <div class="card shadow-sm">
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="tabPemeriksaan" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-kk" data-bs-toggle="tab" href="#pane-kk" role="tab">Pemeriksaan KK</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-art" data-bs-toggle="tab" href="#pane-art" role="tab">Pemeriksaan ART</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- KK -->
                    <div class="tab-pane fade show active" id="pane-kk" role="tabpanel">
                        <div class="mb-2 d-flex gap-2">
                            <input id="filter_kk_search" class="form-control form-control-sm w-25" placeholder="Cari No KK / Kepala keluarga">

                            <button id="btnReloadKK" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-sync"></i> Reload
                            </button>

                            <button id="btnExportKK" class="btn btn-sm btn-outline-success">Export Excel</button>
                        </div>

                        <table id="tableKK" class="table table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th>No KK</th>
                                    <th>Kepala Keluarga</th>
                                    <th>Alamat</th>
                                    <th>Jml ART</th>
                                    <th>Foto KK</th>
                                    <th>Foto Rumah</th>
                                    <th>Foto Dalam</th>
                                    <th>Program</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- ART -->
                    <div class="tab-pane fade" id="pane-art" role="tabpanel">
                        <div class="mb-2 d-flex gap-2">
                            <input id="filter_art_search" class="form-control form-control-sm w-25" placeholder="Cari NIK / Nama / No KK">

                            <button id="btnReloadART" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-sync"></i> Reload
                            </button>

                            <button id="btnExportART" class="btn btn-sm btn-outline-success">Export Excel</button>
                        </div>

                        <table id="tableART" class="table table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>SHDK</th>
                                    <th>JK</th>
                                    <th>Umur</th>
                                    <th>Pendidikan</th>
                                    <th>Pekerjaan</th>
                                    <th>Disabilitas</th>
                                    <th>Status Hamil</th>
                                    <th>Ibu Kandung</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- OFFCANVAS MASTER -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMaster">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasTitle">Detail</h5>
        <button class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="offcanvasBody">
        Memuat...
    </div>
</div>

<script>
    window.baseUrl = "<?= rtrim(base_url(), '/') ?>";
    window.csrfName = "<?= csrf_token() ?>";
    window.csrfHash = "<?= csrf_hash() ?>";
</script>

<!-- load js -->
<script src="<?= base_url('assets/js/pemeriksaan.js') ?>"></script>

<?= $this->endSection() ?>