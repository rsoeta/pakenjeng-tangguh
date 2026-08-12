<?= $this->extend('templates/index'); ?>

<?= $this->section('content') ?>

<!-- 🎨 CSS KHUSUS ANTI-LEAK & WATERMARK -->
<style>
    /* 1. Efek Blur Interaktif */
    .data-rahasia {
        filter: blur(5px);
        transition: filter 0.3s ease-in-out;
        cursor: pointer;
        user-select: none;
        /* Mencegah teks diblok/di-copy secara cepat */
    }

    /* Sentuh atau Tahan (Mobile) untuk melihat jelas */
    .data-rahasia:hover,
    .data-rahasia:active {
        filter: blur(0);
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
$nikUser  = session()->get('nik') ?? 'N/A';
$watermarkStr = $namaUser . ' - ' . $nikUser . ' - ' . date('d/m/Y');
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

                        <div class="card-header bg-danger d-flex justify-content-between align-items-center w-100">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="fas fa-user-slash me-2"></i>Daftar KPM Exclude (Blacklist)
                            </h5>

                            <!-- Tombol Import (Hanya untuk Admin/Operator) -->
                            <?php if (session()->get('role_id') < 4): ?>
                                <div class="ms-auto d-flex gap-2">

                                    <button type="button" class="btn btn-sm btn-light shadow-sm text-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalImportExclude">
                                        <i class="fas fa-file-excel me-1"></i> Import SIKS-NG
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-warning border-warning shadow-sm" role="alert">
                                <i class="fas fa-info-circle me-2"></i> <strong>Instruksi Keamanan:</strong> Arahkan kursor atau tahan sentuhan (pada HP) pada teks yang buram untuk membaca data sensitif.
                            </div>

                            <div class="table-responsive" style="position: relative; z-index: 2;">
                                <table id="tableExclude" class="table table-bordered table-hover w-100 text-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="5%">No</th>
                                            <th width="25%">Nama KPM & Wilayah</th>
                                            <th width="15%">NIK & KK</th>
                                            <th width="25%">Keterangan Blacklist</th>
                                            <th width="15%">Data Bank</th>
                                            <th width="15%">Tanggal Nonaktif</th>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger shadow-sm" id="btnProsesImport">
                    <i class="fas fa-upload me-1"></i> Unggah & Proses
                </button>
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
                    } // Nonaktifkan sorting di kolom rahasia
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        }

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
</script>

<?= $this->endSection() ?>