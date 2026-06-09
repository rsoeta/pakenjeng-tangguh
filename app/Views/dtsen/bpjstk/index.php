<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    /* Styling khusus Signature Pad agar responsif dan tidak scroll saat disentuh di HP */
    #signature-pad {
        border: 2px dashed #007bff;
        border-radius: 8px;
        background-color: #f8f9fa;
        width: 100%;
        height: 250px;
        touch-action: none;
    }

    .filter-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
    }
</style>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h4 class="m-0 fw-bold"><i class="fas fa-id-badge text-primary mr-2"></i> <?= $title; ?></h4>
                </div>
                <div class="col-sm-6 text-right">
                    <?php if (session()->get('role_id') <= 3): ?>
                        <button class="btn btn-success btn-sm shadow-sm rounded-pill px-3" data-toggle="modal" data-target="#modalImport">
                            <i class="fas fa-file-excel mr-1"></i> Impor Data (Excel)
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card filter-box mb-4 shadow-none border">
                <div class="card-body p-3">
                    <div class="row align-items-end g-2">
                        <div class="col-6 col-md-3">
                            <label class="filter-label">Wilayah RW</label>
                            <input type="number" id="filter_rw" class="form-control form-control-sm border bg-light rounded-pill px-3" placeholder="Contoh: 3">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="filter-label">Wilayah RT</label>
                            <input type="number" id="filter_rt" class="form-control form-control-sm border bg-light rounded-pill px-3" placeholder="Contoh: 1">
                        </div>
                        <div class="col-8 col-md-4">
                            <label class="filter-label">Status Serah Terima</label>
                            <select id="filter_status" class="form-control form-control-sm border bg-light rounded-pill px-3">
                                <option value="">Semua Status</option>
                                <option value="0">Belum Diserahkan</option>
                                <option value="1">Sudah Diserahkan</option>
                            </select>
                        </div>
                        <div class="col-4 col-md-2">
                            <button id="btn_filter" class="btn btn-sm btn-dark w-100 rounded-pill shadow-sm">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <table class="table table-hover align-middle w-100" id="tableBpjstk">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Peserta</th>
                                <th>KPJ / NIK</th>
                                <th>Wilayah</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-upload mr-2"></i> Impor Data BPJSTK</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <form action="<?= base_url('bpjstk/import'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <p class="small text-muted">Pastikan format kolom sesuai: <br><b>NO, KP BPJSTK, NAMA, NIK, ALAMAT, RT, RW</b>.</p>
                    <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">Mulai Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProses" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalProsesTitle"><i class="fas fa-handshake mr-2"></i> Proses Serah Terima</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <form id="formSerahTerima">
                <?= csrf_field(); ?>
                <input type="hidden" id="id_bpjstk" name="id_bpjstk">

                <div class="modal-body bg-light">
                    <div class="card mb-3 border-primary shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-primary mb-1" id="info_nama">Memuat...</h6>
                            <p class="mb-0 small text-muted">NIK: <span id="info_nik"></span> | KPJ: <span id="info_kpj"></span></p>
                            <p class="mb-0 small text-muted">Alamat: <span id="info_alamat"></span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <div class="form-group">
                                <label class="small fw-bold">Nama Ibu Kandung <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" required placeholder="Sesuai KK">
                            </div>
                            <div class="form-group">
                                <label class="small fw-bold">No. Handphone / WA</label>
                                <input type="number" class="form-control" id="no_hp" name="no_hp" placeholder="Opsional (0812...)">
                            </div>
                            <div class="form-group">
                                <div class="mt-2 text-center" id="box_preview_foto" style="display:none;">
                                    <img id="preview_foto" src="" class="img-fluid rounded border shadow-sm" style="max-height: 150px;">
                                </div>
                                <label class="small fw-bold">Foto Penerima & Kartu <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="foto_bukti" id="foto_bukti" accept="image/*" capture="environment" required>
                                    <label class="custom-file-label small" for="foto_bukti">Pilih File/Kamera...</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7 mb-3">
                            <label class="small fw-bold">Tanda Tangan Penerima <span class="text-danger">*</span></label>
                            <canvas id="signature-pad"></canvas>
                            <input type="hidden" name="ttdDataUrl" id="ttdDataUrl">
                            <div class="text-right mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger shadow-sm rounded-pill px-3" id="btnClearSignature">
                                    <i class="fas fa-eraser mr-1"></i> Hapus TTD
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow" id="btnSimpan">
                        <i class="fas fa-save mr-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLihat" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-archive mr-2"></i> Arsip Serah Terima</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <h6 class="fw-bold" id="arsip_nama">Nama</h6>
                <p class="small text-muted mb-3" id="arsip_waktu">Diserahkan: -</p>

                <div class="row">
                    <div class="col-6">
                        <p class="small fw-bold mb-1">Foto Bukti</p>
                        <img id="arsip_foto" src="" class="img-fluid rounded border shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                    </div>
                    <div class="col-6">
                        <p class="small fw-bold mb-1">Tanda Tangan</p>
                        <img id="arsip_ttd" src="" class="img-fluid rounded border shadow-sm" style="height: 150px; object-fit: contain; width: 100%; background: #fff;">
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex <?= (session()->get('role_id') < 4) ? 'justify-content-between' : 'justify-content-end' ?>">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                <?php if (session()->get('role_id') < 4): ?>
                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" id="btnRollback" data-id="">
                        <i class="fas fa-undo mr-1"></i> Rollback Data
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    $(document).ready(function() {
        // --- 1. INISIALISASI DATATABLES ---
        var tableBpjstk = $('#tableBpjstk').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('bpjstk/datatable') ?>",
                type: "POST",
                data: function(d) {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                    d.filter_rw = $('#filter_rw').val();
                    d.filter_rt = $('#filter_rt').val();
                    d.filter_status = $('#filter_status').val();
                }
            },
            columnDefs: [{
                    className: "text-center align-middle",
                    targets: [0, 3, 4, 5]
                },
                {
                    orderable: false,
                    targets: [5]
                }
            ]
        });

        $('#btn_filter').click(function() {
            tableBpjstk.ajax.reload();
        });

        // --- 2. INISIALISASI SIGNATURE PAD ---
        var canvas = document.getElementById('signature-pad');
        var signaturePad;

        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            if (signaturePad) signaturePad.clear(); // Bersihkan jika di-resize
        }

        $('#modalProses').on('shown.bs.modal', function() {
            resizeCanvas(); // Ukur ulang canvas saat modal terbuka (PENTING!)
            if (!signaturePad) {
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)'
                });
            } else {
                signaturePad.clear();
            }
        });

        $('#btnClearSignature').click(function() {
            if (signaturePad) signaturePad.clear();
        });

        // --- 3. PREVIEW FOTO UPLOAD ---
        $('#foto_bukti').change(function() {
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_foto').attr('src', e.target.result);
                    $('#box_preview_foto').slideDown();
                }
                reader.readAsDataURL(input.files[0]);
                $(this).next('.custom-file-label').html(input.files[0].name);
            }
        });

        // --- 4. KLIK TOMBOL PROSES (BUKA MODAL) ---
        $('#tableBpjstk').on('click', '.btn-proses', function() {
            var id = $(this).data('id');
            $('#formSerahTerima')[0].reset();
            $('#id_bpjstk').val(id);
            $('#box_preview_foto').hide();
            $('.custom-file-label').html('Pilih File/Kamera...');

            // Ambil data ke server
            $.ajax({
                url: "<?= base_url('bpjstk/get-data') ?>/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(res) {
                    if (res.status === 'success') {
                        $('#info_nama').text(res.data.nama);
                        $('#info_nik').text(res.data.nik);
                        $('#info_kpj').text(res.data.kpj);
                        $('#info_alamat').text(res.data.alamat + ' RT ' + res.data.rt + '/RW ' + res.data.rw);
                        $('#modalProses').modal('show');
                    }
                }
            });
        });

        // --- 5. KLIK TOMBOL LIHAT ARSIP ---
        $('#tableBpjstk').on('click', '.btn-lihat', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "<?= base_url('bpjstk/get-data') ?>/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(res) {
                    if (res.status === 'success') {
                        var d = res.data;
                        $('#arsip_nama').text(d.nama);
                        $('#arsip_waktu').text('Diserahkan: ' + d.waktu_serah_terima);
                        $('#arsip_foto').attr('src', '<?= base_url('uploads/bpjstk/') ?>' + d.foto_bukti);
                        $('#arsip_ttd').attr('src', '<?= base_url('uploads/bpjstk/') ?>' + d.ttd_penerima);
                        $('#modalLihat').modal('show');
                    }
                }
            });
        });

        // --- 6. SUBMIT FORM SERAH TERIMA ---
        $('#formSerahTerima').on('submit', function(e) {
            e.preventDefault();

            if (signaturePad.isEmpty()) {
                Swal.fire('Peringatan', 'Tanda tangan wajib diisi!', 'warning');
                return false;
            }

            // Masukkan data Base64 ke input hidden
            $('#ttdDataUrl').val(signaturePad.toDataURL('image/png'));

            var formData = new FormData(this);

            $.ajax({
                url: '<?= base_url('bpjstk/simpan-serah-terima') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Berhasil!', res.message, 'success');
                        $('#modalProses').modal('hide');
                        tableBpjstk.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                }
            });
        });

        // ========================================================
        // 📋 FITUR SALIN NIK & KPJ (CLIPBOARD)
        // ========================================================
        $(document).on('click', '.btnCopyNik', function() {
            const value = $(this).data('value');
            if (!value) return;
            navigator.clipboard.writeText(value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'NIK Disalin!',
                    text: `NIK ${value} berhasil disalin ke clipboard`,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        });

        $(document).on('click', '.btnCopyNoKK', function() {
            const value = $(this).data('value');
            if (!value) return;
            navigator.clipboard.writeText(value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Nomor BPJSTK Disalin!',
                    text: `KPJ ${value} berhasil disalin ke clipboard`,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        });

        // ========================================================
        // 🔄 LOGIKA ROLLBACK DATA (EKSKLUSIF ROLE < 4)
        // ========================================================
        // Masukkan ID ke dalam atribut tombol rollback saat modal Arsip dibuka
        $('#tableBpjstk').on('click', '.btn-lihat', function() {
            var id = $(this).data('id');
            $('#btnRollback').data('id', id);
        });

        $('#btnRollback').click(function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Rollback Data?',
                html: 'Status akan dikembalikan ke <b>Belum Diserahkan</b>.<br><small class="text-danger">File foto bukti dan tanda tangan lama akan dihapus permanen.</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-undo"></i> Ya, Rollback!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                width: '340px', // 📱 Ukuran mobile friendly nyaman di genggaman
                customClass: {
                    title: 'fs-5',
                    htmlContainer: 'fs-6'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "<?= base_url('bpjstk/rollback') ?>",
                        type: "POST",
                        data: {
                            id: id,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: "JSON",
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('#modalLihat').modal('hide');
                                tableBpjstk.ajax.reload(null, false); // Reload senyap tanpa reset halaman pagination
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Terjadi kesalahan sistem saat menghubungi server.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>