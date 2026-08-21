<?= $this->extend('templates/index'); ?>

<?= $this->section('content') ?>

<!-- 🎨 CSS KHUSUS ANTI-LEAK & WATERMARK -->
<style>
    /* 1. Efek Blur Default */
    .data-rahasia {
        filter: blur(5px);
        transition: filter 0.3s ease-in-out;
        user-select: none;
        /* Mencegah teks di-copy secara cepat sebelum unblur */
    }

    /* Ubah kursor jadi pointer di seluruh baris tabel untuk penanda bisa diklik */
    #tableExclude tbody tr {
        cursor: pointer;
    }

    /* A. BUKA SEMENTARA: Saat baris di-hover, seluruh kolom rahasia di baris itu otomatis jelas */
    #tableExclude tbody tr:hover .data-rahasia {
        filter: blur(0);
    }

    /* B. BUKA PERMANEN: Saat baris memiliki class 'terbuka-permanen', blur hilang selamanya */
    #tableExclude tbody tr.terbuka-permanen .data-rahasia {
        filter: blur(0);
        user-select: auto;
        /* Izinkan teks di-copy */
    }

    /* 2. Container Watermark (Menutupi seluruh area tabel) */
    .watermark-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10;
        pointer-events: none;
        /* Agar klik tetap tembus ke tabel di bawahnya */
        overflow: hidden;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-content: center;
        opacity: 0.05;
        /* Sangat transparan, tidak mengganggu baca, tapi tertangkap kamera */
    }

    /* 3. Teks Watermark Berulang */
    .watermark-text {
        transform: rotate(-30deg);
        font-size: 1.5rem;
        font-weight: bold;
        color: #000;
        margin: 30px;
        white-space: nowrap;
        text-transform: uppercase;
    }

    /* Mengamankan Card agar posisinya relative untuk landasan watermark */
    .card-watermark {
        position: relative;
    }
</style>

<?php
// Ambil data user untuk Watermark
$namaUser = session()->get('fullname') ?? 'PENGGUNA';
// $nikUser  = session()->get('nik') ?? 'N/A';
$watermarkStr = $namaUser . ' - ' . date('d/m/Y');
?>

<div class="content-wrapper mt-1">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">

                <!-- 🚫 AREA TERKUNCI: Disembunyikan sebelum menyetujui NDA -->
                <div id="areaRahasia" class="d-none">
                    <div class="card shadow-sm border-danger card-watermark">

                        <!-- 🌊 CETAK WATERMARK BERULANG -->
                        <div class="watermark-overlay">
                            <?php for ($i = 0; $i < 30; $i++): ?>
                                <div class="watermark-text"><?= esc($watermarkStr) ?></div>
                            <?php endfor; ?>
                        </div>

                        <!-- 1️⃣ HEADER: Hanya untuk Judul -->
                        <div class="card-header bg-danger">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="fas fa-user-slash me-2"></i>Daftar KPM Exclude (Blacklist)
                            </h5>
                        </div>

                        <!-- 2️⃣ BODY: Action Bar & Tabel -->
                        <div class="card-body">

                            <!-- Alert Peringatan -->
                            <div class="alert alert-warning border-warning shadow-sm mb-4" role="alert" style="font-size: 0.9rem;">
                                <i class="fas fa-info-circle me-2"></i> <strong>Instruksi Keamanan:</strong> Arahkan kursor untuk melihat data secara otomatis. <b>Klik pada baris data</b> jika ingin membuka kunci blur secara permanen.
                            </div>

                            <!-- 🎯 ACTION BAR: Filter & Tombol (Responsive) -->
                            <div class="row mb-3 align-items-end" style="position: relative; z-index: 2;">

                                <!-- Bagian Kiri: Filter Wilayah -->
                                <div class="col-md-5 col-12 mb-3 mb-md-0 d-flex gap-2">
                                    <div class="w-50">
                                        <label class="form-label text-muted small fw-bold mb-1">Filter RW</label>
                                        <select id="filter_rw" class="form-select border-danger shadow-sm">
                                            <option value="">-- Semua RW --</option>
                                            <?php foreach ($rwList as $rw): ?>
                                                <option value="<?= esc($rw['rw']) ?>"><?= str_pad($rw['rw'], 3, '0', STR_PAD_LEFT) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="w-50">
                                        <label class="form-label text-muted small fw-bold mb-1">Filter RT</label>
                                        <select id="filter_rt" class="form-select border-danger shadow-sm" disabled>
                                            <option value="">-- Semua RT --</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Bagian Kanan: Deretan Tombol -->
                                <div class="col-md-7 col-12 d-flex gap-2 justify-content-md-end flex-wrap">
                                    <button id="btnResetFilter" class="btn btn-secondary shadow-sm fw-bold flex-grow-1 flex-md-grow-0">
                                        <i class="fas fa-sync-alt me-1"></i> Reset
                                    </button>

                                    <?php if (session()->get('role_id') < 4): ?>
                                        <button type="button" class="btn btn-primary shadow-sm fw-bold flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#modalTambahExclude">
                                            <i class="fas fa-plus me-1"></i> Tambah
                                        </button>
                                        <button type="button" class="btn btn-danger shadow-sm fw-bold flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#modalImportExclude">
                                            <i class="fas fa-file-excel me-1"></i> Import
                                        </button>
                                    <?php endif; ?>
                                </div>

                            </div>
                            <hr> <!-- Garis pemisah antara aksi dan tabel -->

                            <!-- 3️⃣ TABEL DATA -->
                            <div class="table-responsive" style="position: relative; z-index: 2;">
                                <table id="tableExclude" class="table table-bordered table-hover w-100 text-sm">
                                    <thead class="table-light">
                                        <!-- ... (Thead tabel tetap sama) ... -->
                                        <tr>
                                            <th class="text-center" width="5%">No</th>
                                            <th width="25%">Nama KPM & Wilayah</th>
                                            <th width="15%">NIK & KK</th>
                                            <th width="25%">Keterangan Blacklist</th>
                                            <th width="15%">Data Bank</th>
                                            <th width="15%">Tanggal Nonaktif</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan dimuat oleh DataTables -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Import Data Exclude -->
<div class="modal fade" id="modalImportExclude" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel me-2"></i>Import Data Exclude</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formImportExclude" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="alert alert-warning py-2 mb-3 shadow-sm" style="font-size: 0.85rem;">
                        <i class="fas fa-exclamation-triangle me-1"></i> Pastikan struktur kolom Excel Anda sama persis (mulai dari kolom A):<br>
                        <b>NIK | Nama | NO KK | Tanggal Nonaktif | Keterangan | Bank | No Rek | Desil</b>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-danger fw-bold">Unggah File SIKS-NG (.xls / .xlsx)</label>
                        <input class="form-control" type="file" name="file_excel" id="file_excel" accept=".xls,.xlsx" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between w-100">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger shadow-sm" id="btnProsesImport">
                    <i class="fas fa-upload me-1"></i> Unggah & Proses
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Data Exclude -->
<div class="modal fade" id="modalTambahExclude" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah KPM Exclude</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahExclude">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label text-primary fw-bold">Cari KPM (NIK / Nama) <span class="text-danger">*</span></label>
                        <select class="form-select" id="select_nik_exclude" name="nik" style="width: 100%;" required></select>
                    </div>
                    <!-- Input hidden untuk menampung nama dan KK asli dari database -->
                    <input type="hidden" id="nama_exclude" name="nama">
                    <input type="hidden" id="kk_exclude" name="no_kk">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan Blacklist <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="keterangan" rows="2" placeholder="Contoh: Terindikasi Transaksi Judi Online" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Bank</label>
                            <input type="text" class="form-control" name="bank" placeholder="BCA; DANA">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">No. Rekening</label>
                            <input type="text" class="form-control" name="no_rek" placeholder="024xxx">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Tgl Nonaktif <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_nonaktif" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Desil</label>
                            <input type="number" class="form-control" name="desil" id="desil_exclude" placeholder="1">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between w-100">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary shadow-sm" id="btnSimpanExclude">Simpan Data</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // 🛡️ MODAL SUMPAH RAHASIA (NDA) SAAT HALAMAN DIMUAT
        Swal.fire({
            title: '⚠️ PERINGATAN KERAHASIAAN DATA',
            html: `
            <div class="text-start" style="font-size: 0.9rem; line-height: 1.5;">
                Anda akan mengakses data sensitif terkait <b>Aib Warga (Indikasi Judi Online, dll)</b>.<br><br>
                <ul class="text-danger fw-bold">
                    <li>DILARANG KERAS menyebarkan informasi ini ke publik.</li>
                    <li>DILARANG KERAS memfoto/menangkap layar (screenshot) halaman ini.</li>
                </ul>
                Segala bentuk kebocoran data akan terekam oleh sistem dan dapat memicu konflik horizontal di desa.
            </div>
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check-circle"></i> Saya Mengerti & Berjanji',
            cancelButtonText: 'Batal / Kembali',
            allowOutsideClick: false, // Tidak bisa ditutup dengan klik luar
            allowEscapeKey: false, // Tidak bisa ditutup dengan tombol ESC
            width: '32em',
            customClass: {
                title: 'text-danger fw-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // ✅ Jika setuju, buka gembok area rahasia dan inisialisasi tabel
                $('#areaRahasia').removeClass('d-none');

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Akses Diberikan. Jaga kerahasiaan data.',
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: {
                        popup: 'swal2-small'
                    }
                });

                // Panggil fungsi inisialisasi DataTables
                initDataTableExclude();
            } else {
                // ❌ Jika menolak, tendang kembali ke halaman sebelumnya atau dashboard
                window.history.back();
            }
        });

        // 🚀 FUNGSI INIT DATATABLES
        function initDataTableExclude() {
            $('#tableExclude').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '<?= site_url('exclude/datatable') ?>',
                    type: 'POST',
                    data: function(d) {
                        d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                        // Sisipkan nilai filter ke dalam request DataTables
                        d.filter_rw = $('#filter_rw').val();
                        d.filter_rt = $('#filter_rt').val();
                    }
                },
                columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        targets: [2, 3, 4],
                        orderable: false
                    }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        }

        // 🎯 LOGIKA DROPDOWN FILTER WILAYAH
        $('#filter_rw').on('change', function() {
            let rw = $(this).val();
            let rtSelect = $('#filter_rt');

            // Reset RT
            rtSelect.html('<option value="">-- Semua RT --</option>').prop('disabled', true);

            if (rw !== "") {
                // Ambil daftar RT berdasarkan RW via AJAX
                $.post('<?= site_url('exclude/get_rt_by_rw') ?>', {
                    rw: rw,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                }, function(data) {
                    if (data.length > 0) {
                        $.each(data, function(i, item) {
                            rtSelect.append('<option value="' + item.rt + '">' + String(item.rt).padStart(3, '0') + '</option>');
                        });
                        rtSelect.prop('disabled', false);
                    }
                }, 'json');
            }

            // Reload Tabel
            $('#tableExclude').DataTable().ajax.reload();
        });

        $('#filter_rt').on('change', function() {
            $('#tableExclude').DataTable().ajax.reload();
        });

        $('#btnResetFilter').on('click', function() {
            $('#filter_rw').val('').trigger('change');
            $('#tableExclude').DataTable().search('').ajax.reload(); // Reset search box & reload
        });

        // 🚀 PROSES IMPORT EXCEL
        $('#btnProsesImport').on('click', function() {
            let fileInput = $('#file_excel').val();
            if (!fileInput) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Pilih file Excel terlebih dahulu!',
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: {
                        popup: 'swal2-small'
                    }
                });
                return;
            }

            let formData = new FormData($('#formImportExclude')[0]);
            let btn = $(this);
            let htmlLama = btn.html();

            // Ubah tombol jadi loading
            btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...').prop('disabled', true);

            $.ajax({
                url: '<?= site_url('exclude/import_excel') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    btn.html(htmlLama).prop('disabled', false);

                    if (response.status) {
                        $('#modalImportExclude').modal('hide');
                        $('#formImportExclude')[0].reset();
                        $('#tableExclude').DataTable().ajax.reload(null, false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 4000,
                            customClass: {
                                popup: 'swal2-small'
                            }
                        });
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 4000,
                            customClass: {
                                popup: 'swal2-small'
                            }
                        });
                    }
                },
                error: function() {
                    btn.html(htmlLama).prop('disabled', false);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Koneksi server gagal!',
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            popup: 'swal2-small'
                        }
                    });
                }
            });
        });

        // 🚀 BUKA BLUR PERMANEN SAAT BARIS DIKLIK
        $('#tableExclude tbody').on('click', 'tr', function() {
            // Tambahkan class agar baris ini tidak buram lagi walau kursor pergi
            $(this).addClass('terbuka-permanen');
            // Kembalikan kursor ke normal sebagai penanda sudah terbuka
            $(this).css('cursor', 'default');
        });

    });

    // 🚀 FUNGSI SALIN TEKS (Clipboard API + SweetAlert2 Mini)
    function salinTeksRahasia(teks, jenis) {
        navigator.clipboard.writeText(teks).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: jenis + ' berhasil disalin!',
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                    popup: 'swal2-small'
                }
            });
        }).catch(err => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Gagal menyalin teks!',
                showConfirmButton: false,
                timer: 1500,
                customClass: {
                    popup: 'swal2-small'
                }
            });
        });
    }

    // Inisialisasi Select2 di Modal Tambah Data
    $('#select_nik_exclude').select2({
        dropdownParent: $('#modalTambahExclude'),
        placeholder: 'Ketik NIK atau Nama warga...',
        minimumInputLength: 3,
        ajax: {
            url: '<?= site_url("exclude/search_nik_art") ?>',
            dataType: 'json',
            delay: 500,
            data: function(params) {
                return {
                    q: params.term
                };
            }
            // (processResults tidak perlu diubah jika sudah jalan)
        }
    });

    // 🚀 AUTO-FILL NAMA, KK, DAN DESIL SAAT KPM DIPILIH
    $('#select_nik_exclude').on('select2:select', function(e) {
        var data = e.params.data;

        // Isi input hidden nama dan KK
        $('#nama_exclude').val(data.nama);
        $('#kk_exclude').val(data.no_kk);

        // Isi otomatis form Desil (jika desil kosong dari master, biarkan kosong agar bisa diisi manual)
        if (data.desil) {
            $('#desil_exclude').val(data.desil);
        } else {
            $('#desil_exclude').val(''); // Kosongkan jika tidak ada data desil
        }
    });

    $('#modalTambahExclude').on('hidden.bs.modal', function() {
        $('#formTambahExclude')[0].reset();
        $('#select_nik_exclude').val(null).trigger('change');
    });

    // 🚀 EKSEKUSI SIMPAN TAMBAH DATA EXCLUDE
    $('#btnSimpanExclude').on('click', function() {
        let nik = $('#select_nik_exclude').val();
        if (!nik) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Pilih KPM terlebih dahulu!',
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    popup: 'swal2-small'
                }
            });
            return;
        }

        let formData = $('#formTambahExclude').serialize();
        let btn = $(this);
        let htmlLama = btn.html();

        btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...').prop('disabled', true);

        $.ajax({
            url: '<?= site_url('exclude/tambah_exclude') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                btn.html(htmlLama).prop('disabled', false);
                if (response.status) {
                    $('#modalTambahExclude').modal('hide');
                    $('#tableExclude').DataTable().ajax.reload(null, false);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            popup: 'swal2-small'
                        }
                    });
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            popup: 'swal2-small'
                        }
                    });
                }
            }
        });
    });

    // 🚀 FUNGSI PROSES SURAT & UPLOAD BUKTI (One-Stop-Process + UI Bersih)
    function cetakSuratJudol(id, nik, nama) {
        Swal.fire({
            title: 'Proses Surat Klarifikasi',
            text: `Pilih jenis dokumen untuk KPM: ${nama}`,
            icon: 'question',

            // 🚀 PERUBAHAN UI: Tombol X dan Klik Luar
            showCancelButton: false, // Matikan tombol batal di bawah
            showCloseButton: true, // Nyalakan tombol X di kanan atas
            allowOutsideClick: true, // Izinkan klik di luar area untuk menutup

            showDenyButton: true,
            confirmButtonText: '<i class="fas fa-file-word"></i> BA (Membantah)',
            denyButtonText: '<i class="fas fa-file-signature"></i> Pernyataan (Mengaku)',
            buttonsStyling: false,
            customClass: {
                popup: 'swal2-small',
                confirmButton: 'btn btn-warning text-dark mx-1 shadow-sm',
                denyButton: 'btn btn-success mx-1 shadow-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // 🛑 OPSI 1: BA (Tanpa Upload File)
                Swal.fire({
                    title: 'Memproses BA...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.post('<?= site_url('exclude/proses_surat') ?>', {
                    id: id,
                    jenis: 'ba',
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                }, function(res) {
                    if (res.status) {
                        $('#tableExclude').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            customClass: {
                                popup: 'swal2-small'
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message,
                            customClass: {
                                popup: 'swal2-small'
                            }
                        });
                    }
                }).fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: xhr.responseText.substring(0, 100) + '...',
                        customClass: {
                            popup: 'swal2-small'
                        }
                    });
                });

            } else if (result.isDenied) {
                // 🛑 OPSI 2: PERNYATAAN (Select2 + Upload Bukti)
                Swal.fire({
                    title: 'Lengkapi Pernyataan',
                    html: `
                        <div class="text-start small text-muted mb-3">Pilih identitas pelaku dan lampirkan foto/scan penutupan rekening.</div>
                        
                        <label class="float-start fw-bold small text-primary">Identitas Pelaku (NIK / Nama) <span class="text-danger">*</span></label>
                        <select id="select_pelaku" class="form-control form-control-sm mb-3" style="width: 100%;">
                            <option value="${nik}|${nama}" selected>${nik} - ${nama}</option>
                        </select>
                        
                        <br><br>
                        
                        <label class="float-start fw-bold small text-primary mt-1">Upload Bukti Penutupan <span class="text-danger">*</span></label>
                        <input type="file" id="file_bukti" class="form-control form-control-sm shadow-sm" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="float-start text-muted" style="font-size:0.75rem;">Maksimal 5MB (Format: JPG/PNG/PDF)</small>
                    `,

                    // 🚀 PERUBAHAN UI: Konsisten dengan Popup Pertama
                    showCancelButton: false,
                    showCloseButton: true,
                    allowOutsideClick: () => !Swal.isLoading(), // Bisa klik luar, KECUALI sedang proses loading upload

                    confirmButtonText: '<i class="fas fa-save"></i> Simpan ke Server',
                    customClass: {
                        popup: 'swal2-small'
                    },
                    didOpen: () => {
                        $('#select_pelaku').select2({
                            dropdownParent: $('.swal2-popup'),
                            placeholder: 'Ketik NIK atau Nama warga...',
                            minimumInputLength: 3,
                            ajax: {
                                url: '<?= site_url("exclude/search_nik_art") ?>',
                                dataType: 'json',
                                delay: 500,
                                data: function(params) {
                                    return {
                                        q: params.term
                                    };
                                },
                                processResults: function(data) {
                                    return {
                                        results: data.results.map(function(item) {
                                            return {
                                                id: item.id + '|' + item.nama,
                                                text: item.text
                                            }
                                        })
                                    };
                                },
                                cache: true
                            }
                        });
                    },
                    preConfirm: () => {
                        let val = document.getElementById('select_pelaku').value;
                        let file = document.getElementById('file_bukti').files[0];

                        if (!val) {
                            Swal.showValidationMessage('Identitas Pelaku wajib dipilih!');
                            return false;
                        }
                        if (!file) {
                            Swal.showValidationMessage('Bukti penutupan wajib diunggah!');
                            return false;
                        }

                        let splitData = val.split('|');
                        let formData = new FormData();
                        formData.append('id', id);
                        formData.append('jenis', 'pernyataan');
                        formData.append('nik_pelaku', splitData[0]);
                        formData.append('nama_pelaku', splitData[1]);
                        formData.append('file_bukti', file);
                        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                        return $.ajax({
                            url: '<?= site_url('exclude/proses_surat') ?>',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false
                        }).then(response => {
                            if (!response.status) throw new Error(response.message);
                            return response;
                        }).catch(error => {
                            Swal.showValidationMessage(error.message || 'Gagal menghubungi server');
                        });
                    }
                }).then((res2) => {
                    if (res2.isConfirmed) {
                        $('#tableExclude').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan!',
                            text: res2.value.message,
                            customClass: {
                                popup: 'swal2-small'
                            }
                        });
                    }
                });
            }
        });
    }

    // 🗑️ FUNGSI HAPUS DATA KPM EXCLUDE
    function hapusExclude(id, nama) {
        Swal.fire({
            title: 'Hapus Data?',
            html: `Apakah Anda yakin ingin menghapus <b>${nama}</b> dari daftar KPM Exclude? <br><br><small class="text-danger">File surat Word dan foto bukti (jika sudah ada) juga akan ikut terhapus dari server.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            customClass: {
                popup: 'swal2-small',
                confirmButton: 'btn btn-danger mx-1 shadow-sm',
                cancelButton: 'btn btn-secondary mx-1 shadow-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus Data...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.post('<?= site_url('exclude/delete') ?>', {
                    id: id,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                }, function(res) {
                    if (res.status) {
                        $('#tableExclude').DataTable().ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message,
                            customClass: {
                                popup: 'swal2-small'
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message,
                            customClass: {
                                popup: 'swal2-small'
                            }
                        });
                    }
                }).fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: xhr.responseText.substring(0, 100) + '...',
                        customClass: {
                            popup: 'swal2-small'
                        }
                    });
                });
            }
        });
    }
</script>

<?= $this->endSection() ?>