<?= $this->extend('templates/index') ?>

<?= $this->section('content') ?>

<div class="content-wrapper mt-1">
    <div class="content-header">

        <div class="container-fluid">
            <div class="row align-items-center mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-primary"><i class="fas fa-clipboard-list mr-2"></i> Rekap Scan Banpang</h1>
                    <div class="mt-2 mt-md-0">
                        <a href="<?= base_url('banpang/scanner') ?>" class="btn btn-sm btn-success shadow-sm mr-1"><i class="fas fa-qrcode mr-1"></i> Buka Scanner</a>
                        <button type="button" class="btn btn-sm btn-danger shadow-sm" id="btnCetakPdf"><i class="fas fa-file-pdf mr-1"></i> Cetak PDF</button>
                    </div>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item active">Rekap Banpang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card filter-box mb-4 shadow-none border">

                <div class="card-body p-3 bg-light border-bottom">
                    <div class="card shadow-sm">

                        <div class="row align-items-end">
                            <div class="col-4 col-md-4 mb-2">
                                <label class="small font-weight-bold">Filter RW</label>
                                <select id="filter_rw" class="form-control form-control-sm">
                                    <option value="">-- Semua RW --</option>
                                    <?php for ($i = 1; $i <= 20; $i++): ?>
                                        <option value="<?= str_pad($i, 3, '0', STR_PAD_LEFT) ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-4 col-md-4 mb-2">
                                <label class="small font-weight-bold">Filter RT</label>
                                <select id="filter_rt" class="form-control form-control-sm">
                                    <option value="">-- Semua RT --</option>
                                    <?php for ($i = 1; $i <= 15; $i++): ?>
                                        <option value="<?= str_pad($i, 3, '0', STR_PAD_LEFT) ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-4 col-md-4 mb-2">
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
                                        <th class="text-center">RT/RW</th>
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
            ajax: {
                url: "<?= base_url('banpang/datatable') ?>",
                type: "POST",
                data: function(d) {
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
                processing: '<i class="fas fa-spinner fa-spin fa-2x text-primary"></i>'
            }
        });

        // Trigger Filter Datatable
        $('#btnFilter').click(function() {
            tableBanpang.ajax.reload();
        });

        // Trigger Tombol PDF (Tahap 4 Nanti)
        $('#btnCetakPdf').click(function() {
            // Mbah berikan SweetAlert2 compact sesuai selera Kang Rian
            Swal.fire({
                title: 'Segera Hadir!',
                text: 'Modul Cetak PDF sedang diracik oleh Mbah.',
                icon: 'info',
                confirmButtonText: 'Tutup',
                customClass: {
                    confirmButton: 'btn btn-sm btn-primary px-4'
                },
                buttonsStyling: false
            });
        });

    });
</script>

<?= $this->endSection() ?>