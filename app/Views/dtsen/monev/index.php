<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="mb-0"><i class="fas fa-camera-retro"></i> <?= esc($title); ?></h1>
            <ol class="breadcrumb float-right mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/pages'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Monev PKH</li>
            </ol>
        </div>
    </section>

    <section class="content">
        <div class="card mt-2 shadow-sm border-top-primary">
            <div class="card-header bg-white d-flex justify-content-between align-items-center w-100">
                <h5 class="mb-0 fw-bold text-primary">Daftar Target Monev</h5>
                <button type="button" class="btn btn-sm btn-success shadow-sm ms-auto" data-bs-toggle="modal" data-bs-target="#modalImportMonev">
                    <i class="fas fa-file-excel me-1"></i> Import Data Excel
                </button>
            </div>

            <div class="card-body">
                <!-- 🚀 Tabel ini sementara kosong, nanti kita isi dengan Server-Side DataTables -->
                <div class="alert alert-info border-0 shadow-sm">
                    <i class="fas fa-info-circle me-2"></i> Silakan import data Excel dari Pendamping PKH terlebih dahulu.
                </div>

                <div class="table-responsive mt-3">
                    <table id="tableMonev" class="table table-striped table-bordered w-100">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Target</th>
                                <th>Alamat</th>
                                <th>Status Monev</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- 🪟 MODAL IMPORT EXCEL -->
<div class="modal fade" id="modalImportMonev" tabindex="-1" aria-labelledby="modalImportMonevLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalImportMonevLabel"><i class="fas fa-file-upload me-2"></i>Import Data Target Monev</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- 🚀 Tambahkan method="POST" -->
            <form id="formImportMonev" method="POST" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Periode Monev</label>
                        <input type="text" name="periode" class="form-control" value="Triwulan 2 <?= date('Y') ?>" required>
                        <small class="text-muted">Contoh: Triwulan 2 2026</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">File Excel Data Bayar/Monev</label>
                        <input type="file" name="file_excel" id="file_excel" class="form-control" accept=".xls, .xlsx" required>
                        <small class="text-danger">* Pastikan format kolom sesuai dengan template asli (No, Nama Target, dst sampai NIK di kolom L).</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <!-- 🚀 Ubah type="submit" menjadi type="button" -->
                    <button type="button" class="btn btn-success" id="btnProsesImport">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 🪟 MODAL EKSEKUSI MONEV -->
<div class="modal fade" id="modalEksekusiMonev" tabindex="-1" aria-labelledby="modalEksekusiMonevLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"> <!-- Pakai modal-xl agar grid fotonya luas -->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEksekusiMonevLabel">
                    <i class="fas fa-camera-retro me-2"></i>Eksekusi Monev PKH
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body bg-light">
                <!-- Info KPM -->
                <div class="alert alert-info shadow-sm">
                    <h5 class="mb-1 fw-bold" id="detail_nama_kpm">-</h5>
                    <p class="mb-0">NIK: <span id="detail_nik_kpm" class="fw-bold">-</span></p>
                </div>

                <!-- Grid 4 Foto -->
                <div class="row g-3">
                    <!-- 1. Foto KPM -->
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-white fw-bold"><i class="fas fa-user text-primary me-2"></i>Foto KPM</div>
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center" style="min-height: 250px; background: #f8f9fa;">
                                <img id="img_kpm" src="" alt="Foto KPM" class="img-fluid rounded shadow-sm mb-3 d-none" style="max-height: 200px; object-fit: contain;">
                                <span id="null_kpm" class="text-muted fst-italic"><i class="fas fa-image fa-3x mb-2 text-secondary opacity-50"></i><br>Foto belum tersedia</span>
                            </div>
                            <div class="card-footer bg-white text-center">
                                <a id="btn_dl_kpm" href="#" class="btn btn-sm btn-outline-primary d-none" download>
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Foto KKS (Hybrid: Lokal / G-Drive) -->
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-white fw-bold"><i class="fas fa-credit-card text-success me-2"></i>Foto KKS / Kepemilikan</div>
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center p-2" style="min-height: 250px; background: #f8f9fa;">

                                <!-- Iframe untuk G-Drive -->
                                <iframe id="iframe_kks" src="" style="width: 100%; height: 230px; border: none;" class="rounded shadow-sm d-none"></iframe>

                                <!-- Image untuk file lokal -->
                                <img id="img_kks" src="" alt="Foto KKS" class="img-fluid rounded shadow-sm d-none" style="max-height: 230px; object-fit: contain;">

                                <span id="null_kks" class="text-muted fst-italic mt-2"><i class="fas fa-image fa-3x mb-2 text-secondary opacity-50"></i><br>Foto belum tersedia</span>
                            </div>
                            <div class="card-footer bg-white text-center">
                                <a id="btn_dl_kks" href="#" class="btn btn-sm btn-outline-success d-none">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Foto Rumah Depan -->
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-white fw-bold"><i class="fas fa-home text-warning me-2"></i>Foto Rumah (Depan)</div>
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center" style="min-height: 250px; background: #f8f9fa;">
                                <img id="img_rumah_depan" src="" alt="Foto Rumah Depan" class="img-fluid rounded shadow-sm mb-3 d-none" style="max-height: 200px; object-fit: contain;">
                                <span id="null_rumah_depan" class="text-muted fst-italic"><i class="fas fa-image fa-3x mb-2 text-secondary opacity-50"></i><br>Foto belum tersedia</span>
                            </div>
                            <div class="card-footer bg-white text-center">
                                <a id="btn_dl_rumah_depan" href="#" class="btn btn-sm btn-outline-warning d-none" download>
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Foto Rumah Dalam -->
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header bg-white fw-bold"><i class="fas fa-door-open text-danger me-2"></i>Foto Rumah (Dalam)</div>
                            <div class="card-body text-center d-flex flex-column justify-content-center align-items-center" style="min-height: 250px; background: #f8f9fa;">
                                <img id="img_rumah_dalam" src="" alt="Foto Rumah Dalam" class="img-fluid rounded shadow-sm mb-3 d-none" style="max-height: 200px; object-fit: contain;">
                                <span id="null_rumah_dalam" class="text-muted fst-italic"><i class="fas fa-image fa-3x mb-2 text-secondary opacity-50"></i><br>Foto belum tersedia</span>
                            </div>
                            <div class="card-footer bg-white text-center">
                                <a id="btn_dl_rumah_dalam" href="#" class="btn btn-sm btn-outline-danger d-none" download>
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Tandai Selesai -->
                <div class="card mt-3 border-0 shadow-sm">
                    <div class="card-body">
                        <form id="formTandaiSelesai">
                            <input type="hidden" id="id_monev_eksekusi" name="id_monev">
                            <label class="form-label fw-bold">Catatan Pendamping (Opsional)</label>
                            <textarea name="catatan_pendamping" id="catatan_pendamping" class="form-control" rows="2" placeholder="Tuliskan catatan hasil Monev jika ada..."></textarea>
                        </form>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-white d-flex justify-content-between w-100">
                <!-- Tombol Tutup di Kiri -->
                <button type="button" class="btn btn-secondary px-4 shadow-sm" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left me-1"></i> Tutup
                </button>

                <!-- Tombol Tandai Selesai di Kanan -->
                <button type="button" class="btn btn-primary px-4 shadow-sm" id="btnTandaiSelesai">
                    <i class="fas fa-check me-1"></i> Tandai Selesai
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // 🚀 INIT DATATABLES SERVER-SIDE
        let tableMonev = $('#tableMonev').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '<?= site_url('monev/datatable') ?>',
                type: 'POST',
                data: function(d) {
                    // Sisipkan CSRF Token agar aman
                    d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                }
            },
            columns: [{
                    className: "text-center"
                }, // No
                null, // Nama Target
                null, // Alamat
                {
                    className: "text-center"
                }, // Status
                {
                    className: "text-center"
                } // Aksi
            ]
        });

        // 🚀 BIND KE EVENT CLICK TOMBOL, BUKAN FORM SUBMIT
        $('#btnProsesImport').on('click', function() {

            let form = $('#formImportMonev')[0];

            // 1. Cek validasi HTML5 (Required dll)
            if (!form.checkValidity()) {
                form.reportValidity();
                return false;
            }

            let fileInput = $('#file_excel').val();

            // 2. Cek apakah file sudah dipilih
            if (fileInput === '') {
                Swal.fire('Peringatan', 'Pilih file Excel terlebih dahulu!', 'warning');
                return false;
            }

            // 3. Cek Ekstensi
            let ext = fileInput.split('.').pop().toLowerCase();
            if ($.inArray(ext, ['xls', 'xlsx']) === -1) {
                Swal.fire('Format Salah', 'Harap unggah file berekstensi .xls atau .xlsx', 'error');
                return false;
            }

            let formData = new FormData(form);
            let btnSubmit = $(this);

            // Kunci tombol & tampilkan loading
            btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
            Swal.fire({
                title: 'Membaca Excel...',
                html: 'Sistem sedang mengekstrak NIK KPM.<br>Harap tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Eksekusi AJAX
            $.ajax({
                url: '<?= site_url('monev/import_excel') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    btnSubmit.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1"></i> Mulai Import');

                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Berhasil!',
                            html: res.message,
                            customClass: {
                                popup: 'swal-sm'
                            }
                        }).then(() => {
                            // ...
                            $('#modalImportMonev').modal('hide');
                            $('#formImportMonev')[0].reset();

                            // 🚀 Hancurkan // dan aktifkan kode ini!
                            tableMonev.ajax.reload(null, false);
                            // ...
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    btnSubmit.prop('disabled', false).html('<i class="fas fa-cloud-upload-alt me-1"></i> Mulai Import');
                    console.error(xhr.responseText); // Tampilkan detail error di console
                    Swal.fire('Kesalahan Sistem', 'Gagal memproses permintaan ke server. Silakan cek console browser.', 'error');
                }
            });
        });

        // 🚀 FUNGSI MENAMPILKAN DETAIL MONEV KE MODAL
        window.lihatDetailMonev = function(id) {
            Swal.fire({
                title: 'Memuat Data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= site_url('monev/get_detail/') ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    Swal.close();
                    if (res.status) {
                        let d = res.data;

                        // 🚀 ANTI-BUG: Pastikan baseUrl selalu memiliki garis miring TEPAT 1 di akhir
                        let baseUrl = '<?= rtrim(base_url(), '/') ?>/';

                        let namaFinal = d.nama_sinden ? d.nama_sinden : d.nama_target;
                        $('#detail_nama_kpm').text(namaFinal.toUpperCase());
                        $('#detail_nik_kpm').text(d.nik);
                        $('#id_monev_eksekusi').val(d.id_monev);
                        $('#catatan_pendamping').val('');

                        // 🚀 FUNGSI HELPER CERDAS UNTUK PATH GAMBAR LOKAL & G-DRIVE
                        function renderImage(imgVal, imgId, iframeId, nullId, dlBtnId, prefixName, defaultFolder) {
                            let elImg = $('#' + imgId);
                            let elIframe = iframeId ? $('#' + iframeId) : null;
                            let elNull = $('#' + nullId);
                            let elBtn = $('#' + dlBtnId);

                            elImg.addClass('d-none');
                            if (elIframe) elIframe.addClass('d-none');
                            elNull.addClass('d-none');
                            elBtn.addClass('d-none').removeAttr('download').removeAttr('target');

                            if (imgVal && imgVal.trim() !== '') {
                                // 🌐 CEK APAKAH INI LINK GOOGLE DRIVE
                                if (imgVal.includes('drive.google.com')) {
                                    let match = imgVal.match(/(?:id=|\/d\/)([a-zA-Z0-9_-]{25,})/);

                                    if (match && match[1]) {
                                        let fileId = match[1];
                                        let iframeUrl = 'https://drive.google.com/file/d/' + fileId + '/preview';

                                        // 🚀 URL SAKTI UNTUK DIRECT DOWNLOAD G-DRIVE
                                        let directDownloadUrl = 'https://drive.google.com/uc?export=download&id=' + fileId;

                                        if (elIframe) {
                                            elIframe.attr('src', iframeUrl).removeClass('d-none');
                                        }

                                        // 🚀 Ubah kembali tombol menjadi Download dan arahkan ke direct link
                                        elBtn.attr('href', directDownloadUrl)
                                            .attr('target', '_blank') // Buka tab baru sebentar sebelum otomatis terunduh
                                            .html('<i class="fas fa-download me-1"></i> Download')
                                            .removeClass('d-none');
                                    } else {
                                        elNull.removeClass('d-none').html('<span class="text-danger text-sm"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>URL Drive Tidak Valid</span>');
                                    }
                                } else {
                                    let fullPath = '';
                                    // Normalisasi backslash bawaan Windows ke slash normal
                                    let normalizedImg = imgVal.replace(/\\/g, '/');

                                    if (normalizedImg.indexOf('/') !== -1) {
                                        // Path sudah ada dari DB
                                        fullPath = baseUrl + normalizedImg.replace(/^\//, '');
                                    } else {
                                        // Path diracik dari folder default
                                        fullPath = baseUrl + defaultFolder + '/' + normalizedImg;
                                    }

                                    elImg.attr('src', fullPath).removeClass('d-none');
                                    let fileName = prefixName + '_' + d.nik + '.jpg';
                                    elBtn.attr('href', fullPath)
                                        .attr('download', fileName)
                                        .html('<i class="fas fa-download me-1"></i> Download')
                                        .removeClass('d-none');
                                }
                            } else {
                                elNull.removeClass('d-none').html('<i class="fas fa-image fa-3x mb-2 text-secondary opacity-50"></i><br>Foto belum tersedia');
                            }
                        }

                        // (Variabel folder dan eksekusi renderImage di bawahnya tetap sama)
                        // ...

                        // 🔧 DEKLARASI DIREKTORI FOTO SPESIFIK SINDEN
                        let folderKpm = 'uploads/bansos';
                        let folderKks = 'data/master_kks';
                        let folderRumahDepan = 'data/usulan/foto_rumah';
                        let folderRumahDalam = 'data/usulan/foto_rumah_dalam';

                        // 🎯 EKSEKUSI RENDER
                        // Parameter ke-3 adalah ID Iframe (khusus KKS, yang lain null)
                        renderImage(d.foto_kpm_kks, 'img_kpm', null, 'null_kpm', 'btn_dl_kpm', 'KPM', folderKpm);
                        renderImage(d.foto_kks_final, 'img_kks', 'iframe_kks', 'null_kks', 'btn_dl_kks', 'KKS', folderKks);
                        renderImage(d.foto_rumah, 'img_rumah_depan', null, 'null_rumah_depan', 'btn_dl_rumah_depan', 'Rumah_Depan', folderRumahDepan);
                        renderImage(d.foto_rumah_dalam, 'img_rumah_dalam', null, 'null_rumah_dalam', 'btn_dl_rumah_dalam', 'Rumah_Dalam', folderRumahDalam);

                        // Disable tombol "Tandai Selesai" jika status sudah selesai
                        if (d.status_monev === 'Selesai') {
                            $('#btnTandaiSelesai').prop('disabled', true).html('<i class="fas fa-check-double me-1"></i> Sudah Selesai');
                        } else {
                            $('#btnTandaiSelesai').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Tandai Selesai');
                        }

                        // Tampilkan Modal
                        $('#modalEksekusiMonev').modal('show');
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Kesalahan', 'Gagal memuat data dari server.', 'error');
                }
            });
        };

        // 🚀 HANDLER TOMBOL TANDAI SELESAI
        $('#btnTandaiSelesai').on('click', function() {
            let btn = $(this);
            let id_monev = $('#id_monev_eksekusi').val();
            let catatan = $('#catatan_pendamping').val();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: '<?= site_url('monev/tandai_selesai') ?>',
                type: 'POST',
                data: {
                    id_monev: id_monev,
                    catatan_pendamping: catatan,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modalEksekusiMonev').modal('hide');
                            tableMonev.ajax.reload(null, false); // Reload tabel tanpa memindahkan paginasi
                        });
                    } else {
                        btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Tandai Selesai');
                        Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                    }
                }
            });
        });

        // 🚀 FIX: Paksa DataTables hitung ulang lebar kolom saat Modal ditutup
        $('#modalEksekusiMonev, #modalImportMonev').on('hidden.bs.modal', function() {
            if (typeof tableMonev !== 'undefined') {
                tableMonev.columns.adjust().responsive.recalc();
            }
        });

    });
</script>
<?= $this->endSection(); ?>