<?= $this->extend('templates/index') ?>

<?= $this->section('content') ?>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-list mr-2"></i> Rekap Scan Banpang</h1>
                </div>
                <div class="col-sm-6 text-sm-right mt-3 mt-sm-0">
                    <a href="<?= base_url('banpang/scanner') ?>" class="btn btn-sm btn-success shadow-sm mr-1"><i class="fas fa-qrcode mr-1"></i> Buka Scanner</a>
                    <button type="button" class="btn btn-sm btn-outline-success shadow-sm mr-1" id="btnExportExcel"><i class="fas fa-file-excel mr-1"></i> Ekspor Excel</button>
                    <button type="button" class="btn btn-sm btn-danger shadow-sm" id="btnCetakPdf"><i class="fas fa-file-pdf mr-1"></i> Cetak PDF</button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card filter-box mb-4 shadow-none border">

                <div class="card-body p-3 bg-light border-bottom">
                    <div class="card shadow-sm">

                        <!-- 🚀 GRID DIUBAH JADI 4 KOLOM (col-md-3) -->
                        <div class="row align-items-end">
                            <!-- 🚀 GANTI JADI DROPDOWN NO BAST -->
                            <div class="col-6 col-md-3 mb-2">
                                <label class="small font-weight-bold">Filter Tahap (No. BAST)</label>
                                <select id="filter_bast" class="form-control form-control-sm">
                                    <option value="">-- Semua Tahap --</option>
                                    <?php foreach ($list_bast as $bast): ?>
                                        <option value="<?= esc($bast['no_bast']) ?>"><?= esc($bast['no_bast']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <label class="small font-weight-bold">Filter RW</label>
                                <select id="filter_rw" class="form-control form-control-sm">
                                    <option value="">-- Semua RW --</option>
                                    <?php for ($i = 1; $i <= 20; $i++): ?>
                                        <option value="<?= str_pad($i, 3, '0', STR_PAD_LEFT) ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <label class="small font-weight-bold">Filter RT</label>
                                <select id="filter_rt" class="form-control form-control-sm">
                                    <option value="">-- Semua RT --</option>
                                    <?php for ($i = 1; $i <= 15; $i++): ?>
                                        <option value="<?= str_pad($i, 3, '0', STR_PAD_LEFT) ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 mb-2">
                                <button class="btn btn-sm btn-primary w-100 shadow-sm" id="btnFilter"><i class="fas fa-filter mr-1"></i> Terapkan</button>
                            </div>
                        </div>
                    </div>


                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="tableRekapBanpang" class="table table-hover table-striped mb-0 w-100">
                                <thead class="bg-white">
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th>No PBP</th>
                                        <th>Nama KPM / NIK</th>
                                        <th class="text-center">Alamat Lengkap</th>
                                        <th>Waktu Scan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center"><i class="fas fa-cog"></i></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {

        // Inisialisasi Datatable
        var tableBanpang = $('#tableRekapBanpang').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,

            // 🚀 TAMBAHAN SUPER PENTING:
            searchDelay: 800, // Tunggu 800ms setelah user berhenti mengetik baru kirim AJAX
            deferRender: true, // Mempercepat rendering DOM HTML di browser

            // 1. Di dalam inisialisasi DataTables (ajax.data)
            ajax: {
                url: "<?= base_url('banpang/datatable') ?>",
                type: "POST",
                data: function(d) {
                    d.filter_bast = $('#filter_bast').val(); // <-- Pastikan namanya filter_bast
                    d.filter_rw = $('#filter_rw').val();
                    d.filter_rt = $('#filter_rt').val();
                    d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
                }
            },
            columnDefs: [{
                    targets: [0, 3, 5, 6],
                    className: 'text-center'
                },
                {
                    targets: [6],
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari Nama / NIK / No PBP...",
                processing: '<i class="fas fa-spinner fa-spin fa-2x text-primary"></i> Memuat Data...'
            }
        });

        // Trigger Filter Datatable
        $('#btnFilter').click(function() {
            tableBanpang.ajax.reload();
        });

        // 2. Di dalam Event Trigger Tombol Ekspor Excel
        $('#btnExportExcel').click(function() {
            let bast = $('#filter_bast').val(); // 🚀 UBAH JADI BAST
            let rw = $('#filter_rw').val();
            let rt = $('#filter_rt').val();
            window.open("<?= base_url('banpang/exportExcel') ?>?filter_bast=" + bast + "&filter_rw=" + rw + "&filter_rt=" + rt, '_blank');
        });

        // 3. Di dalam Event Trigger Tombol Cetak PDF
        $('#btnCetakPdf').click(function() {
            let bast = $('#filter_bast').val(); // 🚀 UBAH JADI BAST
            let rw = $('#filter_rw').val();
            let rt = $('#filter_rt').val();
            window.open("<?= base_url('banpang/exportPdf') ?>?filter_bast=" + bast + "&filter_rw=" + rw + "&filter_rt=" + rt, '_blank');
        });

    });
</script>

<?= $this->endSection() ?>