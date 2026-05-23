<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <h4 class="fw-bold mb-0"><i class="fas fa-shield-alt text-primary"></i> Verivali PDTT 2025</h4>
        <div>
            <?php if ($roleId == 5 || $roleId <= 3): ?>
                <a href="<?= base_url('pdtt/2025/export-excel') ?>" class="btn btn-primary btn-sm shadow-sm me-1">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            <?php endif; ?>

            <?php if ($roleId <= 3): ?>
                <button class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                    <i class="fas fa-upload"></i> Import Data
                </button>
            <?php endif; ?>
        </div>
    </div>

    <section class="content px-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-3 pb-2">
                <div class="row g-2">
                    <div class="col-md-2 col-6">
                        <select id="filterRW" class="form-select form-select-sm">
                            <option value="">-- Semua RW --</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <select id="filterRT" class="form-select form-select-sm">
                            <option value="">-- Semua RT --</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-12">
                        <select id="filterStatus" class="form-select form-select-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="Pending" selected>🟡 Pending (Belum Verifikasi)</option>
                            <option value="Selesai">🟢 Selesai (Sudah Verifikasi)</option>
                        </select>
                    </div>
                    <div class="col-md-5 col-12 text-end">
                        <button id="btnFilter" class="btn btn-primary btn-sm w-100"><i class="fas fa-search"></i> Terapkan Filter</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="tablePDTT" class="table table-striped table-bordered table-sm w-100">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>No</th>
                            <th>Nama Pengurus</th>
                            <th>NIK</th>
                            <th>No KK</th>
                            <th>Alamat</th>
                            <th>Temuan (Keterangan)</th>
                            <th>Foto KKS</th>
                            <th>Kepemilikan Rumah</th>
                            <th>Kondisi Rumah</th>
                            <th>Foto Rumah</th>
                            <th>Mobil</th>
                            <th>Motor</th>
                            <th>Disabilitas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formImport" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-file-excel"></i> Import Data PDTT</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> Pastikan format kolom Excel sesuai juknis.
                    </div>
                    <label class="form-label fw-bold">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" name="file_excel" id="file_excel" class="form-control" accept=".xlsx, .xls" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="formVerifikasi" enctype="multipart/form-data">
            <input type="hidden" name="id" id="pdtt_id">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">🔍 Form Verifikasi PDTT 2025</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="bg-light p-3 rounded border mb-3">
                        <h6 class="fw-bold text-danger mb-1" id="v_keterangan">Temuan Pusat: -</h6>
                        <small class="text-muted">Nama: <span id="v_nama" class="fw-bold text-dark"></span> | NIK: <span id="v_nik" class="fw-bold text-dark"></span></small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kesesuaian Temuan <span class="text-danger">*</span></label>
                            <select name="kesesuaian" id="kesesuaian" class="form-select" required>
                                <option value="">-- Pilih Kesesuaian --</option>
                                <option value="Sesuai">Sesuai</option>
                                <option value="Tidak Sesuai">Tidak Sesuai</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pekerjaan</label>
                            <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" placeholder="Contoh: Buruh Harian">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jenis Usaha (Jika ada)</label>
                            <input type="text" name="jenis_usaha" id="jenis_usaha" class="form-control" placeholder="Contoh: Berdagang Nasi Uduk">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jumlah Penghasilan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="jumlah_penghasilan" id="jumlah_penghasilan" class="form-control rupiah" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Penjelasan Riil <span class="text-danger">*</span></label>
                            <textarea name="penjelasan" id="penjelasan" class="form-control" rows="2" placeholder="Wajib diisi sesuai instruksi surat..." required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Foto Meteran Listrik <small class="text-muted" id="hint_pln">(Hanya jika temuan PLN)</small></label>
                            <input type="file" name="foto_listrik" id="foto_listrik" class="form-control form-control-sm" accept="image/*" capture="environment">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Foto Slip Gaji <small class="text-muted">(Jika ASN/UMK)</small></label>
                            <input type="file" name="foto_slip_gaji" id="foto_slip_gaji" class="form-control form-control-sm" accept="image/*" capture="environment">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Verifikasi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {

        // 📌 Format Rupiah
        $(document).on('input', '.rupiah', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = new Intl.NumberFormat('id-ID').format(value);
        });

        // 🚀 INIT FILTER RW BERDASARKAN WILAYAH TUGAS
        $.getJSON("<?= base_url('pdtt/2025/get-rw') ?>", function(res) {
            let options = '<option value="">-- Semua RW --</option>';
            res.rw.forEach(item => {
                options += `<option value="${item.rw}">RW ${item.rw}</option>`;
            });
            $('#filterRW').html(options);
        });

        // 🚀 INIT FILTER RT BERTINGKAT (ON CHANGE RW)
        $('#filterRW').on('change', function() {
            let rw = $(this).val();
            let rtSelect = $('#filterRT');
            rtSelect.html('<option value="">-- Semua RT --</option>');

            if (!rw) return;

            $.getJSON("<?= base_url('pdtt/2025/get-rt/') ?>" + rw, function(res) {
                res.rt.forEach(item => {
                    rtSelect.append(`<option value="${item.rt}">RT ${item.rt}</option>`);
                });
            });
        });

        // 📌 Inisialisasi DataTables
        let tablePDTT = $('#tablePDTT').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "<?= base_url('pdtt/2025/datatable') ?>",
                type: "POST",
                data: function(d) {
                    d.filter_rw = $('#filterRW').val();
                    d.filter_rt = $('#filterRT').val();
                    d.filter_status = $('#filterStatus').val();
                }
            },
            columnDefs: [{
                    targets: '_all',
                    className: 'align-middle'
                },
                {
                    targets: 1,
                    className: 'text-nowrap fw-bold'
                }, // 🚀 Nama Pengurus: text-nowrap
                // {
                //     targets: [0, 6, 9, 10, 11, 13, 14],
                //     className: 'text-center'
                // }
            ]
        });

        // 🚀 EVENT: Klik Tombol Terkunci (Groundcheck Belum Lengkap)
        $(document).on('click', '.btn-locked', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Terkunci! 🔒',
                text: 'Selesaikan Groundcheck di menu Pembaruan Keluarga terlebih dahulu. Pastikan Foto KKS, Foto Rumah, Kepemilikan, dan Kondisi Rumah telah terisi!',
                width: '320px', // Perkecil untuk kenyamanan mobile user
                customClass: {
                    title: 'fs-5',
                    content: 'fs-6'
                }
            });
        });

        // 📌 Filter Ulang
        $('#btnFilter').on('click', function() {
            tablePDTT.ajax.reload();
        });

        // 📌 Form Import Excel
        $('#formImport').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            Swal.fire({
                title: 'Mengupload...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "<?= base_url('pdtt/2025/import-excel') ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Berhasil!', res.message, 'success');
                        $('#modalImport').modal('hide');
                        $('#formImport')[0].reset();
                        tablePDTT.ajax.reload();
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Terjadi kesalahan jaringan', 'error');
                }
            });
        });

        // 📌 Buka Modal Verifikasi
        $(document).on('click', '.btn-verifikasi', function() {
            let id = $(this).data('id');
            $('#formVerifikasi')[0].reset();

            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.getJSON(`<?= base_url('pdtt/2025/get-detail/') ?>${id}`, function(res) {
                Swal.close();
                if (res.status === 'success') {
                    let d = res.data;
                    $('#pdtt_id').val(d.id);
                    $('#v_keterangan').text('Temuan Pusat: ' + (d.keterangan || '-'));
                    $('#v_nama').text(d.nama_pengurus);
                    $('#v_nik').text(d.nik);

                    $('#kesesuaian').val(d.kesesuaian);
                    $('#pekerjaan').val(d.pekerjaan);
                    $('#jenis_usaha').val(d.jenis_usaha);
                    $('#penjelasan').val(d.penjelasan);

                    if (d.jumlah_penghasilan) {
                        $('#jumlah_penghasilan').val(new Intl.NumberFormat('id-ID').format(d.jumlah_penghasilan));
                    }

                    // 🚀 Disable Foto Listrik jika tidak ada keterangan 'PLN'
                    let temuan = (d.keterangan || '').toUpperCase();
                    if (temuan.includes('PLN')) {
                        $('#foto_listrik').prop('disabled', false);
                        $('#hint_pln').text('(Wajib diisi untuk temuan PLN)').addClass('text-danger').removeClass('text-muted');
                    } else {
                        $('#foto_listrik').prop('disabled', true);
                        $('#hint_pln').text('(Dinonaktifkan, bukan temuan PLN)').removeClass('text-danger').addClass('text-muted');
                    }

                    $('#modalVerifikasi').modal('show');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        });

        // 📌 Submit Verifikasi
        $('#formVerifikasi').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            Swal.fire({
                title: 'Menyimpan Verifikasi...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "<?= base_url('pdtt/2025/save-verifikasi') ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#modalVerifikasi').modal('hide');
                        tablePDTT.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
                }
            });
        });

        // 📋 FUNGSI TOAST SALIN KE CLIPBOARD
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        $(document).on('click', '.btnCopyNik, .btnCopyNoKK', function() {
            const value = $(this).attr('data-value');
            const title = $(this).hasClass('btnCopyNik') ? 'NIK' : 'No. KK';

            // 🚀 BUG FIX: Trik Fallback untuk akses via IP Lokal (HTTP Non-Secure)
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(value).then(() => {
                    Toast.fire({
                        icon: 'success',
                        title: `${title} berhasil disalin!`
                    });
                }).catch(err => {
                    Toast.fire({
                        icon: 'error',
                        title: `Gagal menyalin ${title}`
                    });
                });
            } else {
                // Trik DOM execCommand versi lama (Bypass batasan keamanan browser HTTP)
                let textArea = document.createElement("textarea");
                textArea.value = value;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    Toast.fire({
                        icon: 'success',
                        title: `${title} berhasil disalin!`
                    });
                } catch (err) {
                    Toast.fire({
                        icon: 'error',
                        title: `Gagal menyalin ${title}`
                    });
                }
                textArea.remove();
            }
        });
    });
</script>
<?= $this->endSection(); ?>