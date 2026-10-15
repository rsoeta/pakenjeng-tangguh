<?= $this->extend('templates/index'); ?>

<?= $this->section('content') ?>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid py-4">
            <div class="row mb-3">
                <div class="col-12">
                    <h4 class="mb-0 text-danger fw-bold"><i class="fas fa-user-times me-2"></i> Arsip PBI-JKN (Non-Aktif)</h4>
                    <p class="text-muted">Daftar warga yang bantuan BPJS-nya telah dinonaktifkan.</p>
                </div>
            </div>

            <!-- 🎛️ FILTER PANEL DINAMIS -->
            <div class="card shadow-sm border-top-danger mb-4">
                <div class="card-body p-3">
                    <div class="row g-2">
                        <!-- Filter RW -->
                        <div class="col-6 col-md-2">
                            <label class="small fw-bold text-muted">Filter RW</label>
                            <select id="filter_rw" class="form-control form-control-sm select2">
                                <option value="">-- Semua --</option>
                                <?php for ($i = 1; $i <= 15; $i++): ?>
                                    <option value="<?= $i ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Filter RT -->
                        <div class="col-6 col-md-2">
                            <label class="small fw-bold text-muted">Filter RT</label>
                            <select id="filter_rt" class="form-control form-control-sm select2">
                                <option value="">-- Semua --</option>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- 🚀 TAMBAHAN: Filter Alasan (Dinamis dari Database) -->
                        <div class="col-12 col-md-4">
                            <label class="small fw-bold text-muted">Filter Alasan Non-Aktif</label>
                            <select id="filter_alasan" class="form-control form-control-sm select2">
                                <option value="">-- Semua Alasan --</option>
                                <?php foreach ($listAlasan as $row): ?>
                                    <!-- Looping data unik dari Controller -->
                                    <option value="<?= esc($row['alasan_nonaktif']) ?>">
                                        <?= esc($row['alasan_nonaktif']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="col-12 col-md-4 d-flex align-items-end justify-content-md-end mt-2 mt-md-0 gap-2 flex-wrap">
                            <?php if (session()->get('role_id') < 4): ?>
                                <!-- Form Hidden untuk Upload Excel Non-Aktif -->
                                <input type="file" id="fileExcelNonAktif" class="d-none" accept=".xls,.xlsx" onchange="uploadExcelNonAktif()">

                                <!-- Tombol Import Non-Aktif (Dilengkapi Panduan Format) -->
                                <button class="btn btn-sm btn-danger shadow-sm" onclick="konfirmasiImportNonAktif()" title="Import Data Non-Aktif dari SIKS-NG">
                                    <i class="fas fa-file-import me-1"></i> Import Non-Aktif
                                </button>
                            <?php endif; ?>

                            <button class="btn btn-sm btn-success shadow-sm" onclick="exportExcelLuar()">
                                <i class="fas fa-file-excel me-1"></i> Export Data
                            </button>
                            <button class="btn btn-sm btn-secondary shadow-sm" onclick="reloadTable()" title="Refresh Data">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🗄️ TABEL MASTER DATA -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="tablePbiNonAktif" class="table table-striped table-hover table-bordered w-100" style="font-size: 0.9rem;">
                        <thead class="table-danger text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Nama & NIK</th>
                                <th width="30%">Alasan Non-Aktif</th>
                                <th width="15%">Tanggal</th>
                                <th width="20%">Alamat</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var table;

    // =========================================================================
    // 🚀 KONFIGURASI MODULAR SWEETALERT2 (RESPONSIVE & AUTO-CLOSE)
    // =========================================================================
    const SwalSuccess = Swal.mixin({
        icon: 'success',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        width: '22rem',
        padding: '1.25rem',
        customClass: {
            popup: 'swal-sm rounded-4 shadow',
            title: 'fs-5 fw-bold',
            htmlContainer: 'fs-6'
        }
    });

    const SwalToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        icon: 'success',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        customClass: {
            popup: 'swal-sm rounded-3 shadow-sm'
        }
    });

    // Fungsi Global Pemanggil Alert Sukses (Mendukung Teks & HTML)
    function showSuccess(message, title = 'Berhasil!', isHtml = false) {
        let config = {
            title: title
        };
        if (isHtml) {
            config.html = message;
        } else {
            config.text = message;
        }
        SwalSuccess.fire(config);
    }

    $(document).ready(function() {
        table = $('#tablePbiNonAktif').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
                "url": "<?= site_url('pbi/nonaktif/tabel') ?>",
                "type": "POST",
                "data": function(d) {
                    d.rw = $('#filter_rw').val();
                    d.rt = $('#filter_rt').val();
                    d.alasan = $('#filter_alasan').val();
                }
            },
            "dom": '<"row align-items-center"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            "buttons": [{
                extend: 'excelHtml5',
                className: 'd-none btn-excel-hidden',
                title: 'Arsip PBI-JKN Non-Aktif',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4], // Tanpa aksi
                    format: {
                        body: function(data, row, column, node) {
                            if (typeof data === 'string') {
                                let cleanData = data.replace(/<br\s*[\/]?>/gi, "\n");
                                cleanData = cleanData.replace(/<[^>]*>?/gm, '');
                                let txt = document.createElement("textarea");
                                txt.innerHTML = cleanData;
                                return txt.value.trim();
                            }
                            return data;
                        }
                    }
                }
            }],
            "columnDefs": [{
                    "className": "text-center align-middle",
                    "targets": [0, 3, 5]
                },
                {
                    "className": "align-middle",
                    "targets": [1, 2, 4]
                },
                {
                    "orderable": false,
                    "targets": [5]
                }
            ]
        });

        // 🔥 Gabungkan semua filter ke dalam satu listener (menghapus duplikasi listener sebelumnya)
        $('#filter_rw, #filter_rt, #filter_alasan').on('change', function() {
            table.ajax.reload(null, false);
        });
    });

    function reloadTable() {
        table.ajax.reload(null, false);
    }

    // 🚀 FUNGSI EXPORT EXCEL (UPDATE FULL DATA SERVER-SIDE)
    function exportExcelLuar() {
        Swal.fire({
            title: 'Menyiapkan Data...',
            text: 'Membaca seluruh arsip Non-Aktif dari server...',
            allowOutsideClick: false,
            customClass: {
                popup: 'swal-sm'
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });

        let oldLength = table.page.len();
        table.page.len(-1).draw();

        table.one('draw', function() {
            table.button('.btn-excel-hidden').trigger();
            table.page.len(oldLength).draw();
            Swal.close();
        });
    }

    // 📋 Tampilkan Panduan Format Kolom SIKS-NG Sebelum Pilih File
    function konfirmasiImportNonAktif() {
        Swal.fire({
            title: 'Import PBI Non-Aktif (SIKS-NG)',
            html: `
                <div class="text-start small bg-light p-2 rounded border border-danger mb-2">
                    <b class="text-danger"><i class="fas fa-info-circle me-1"></i> Urutan Kolom Excel (Mulai Baris 2):</b>
                    <ol class="mb-0 ps-3 mt-1" style="line-height: 1.4;">
                        <li><b>A:</b> No</li>
                        <li><b>B:</b> NIK <span class="text-danger fw-bold">(Wajib 16 Digit)</span></li>
                        <li><b>C:</b> No KK</li>
                        <li><b>D:</b> Nama</li>
                        <li><b>E - H:</b> Provinsi, Kab/Kota, Kec, Desa</li>
                        <li><b>I:</b> Alamat</li>
                        <li><b>J:</b> Alasan Non-Aktif</li>
                    </ol>
                </div>
                <small class="text-muted">Data PBI Aktif yang NIK-nya tercantum di dalam file ini akan otomatis dipindahkan ke <b>Non-Aktif</b>.</small>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-folder-open me-1"></i> Pilih File Excel',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-sm rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('fileExcelNonAktif').click();
            }
        });
    }

    // 🚀 Eksekusi Upload Excel Non-Aktif via AJAX
    function uploadExcelNonAktif() {
        let fileInput = document.getElementById('fileExcelNonAktif');
        let file = fileInput.files[0];

        if (!file) return;

        let formData = new FormData();
        formData.append('file_excel', file);

        Swal.fire({
            title: 'Memproses Data Non-Aktif...',
            text: 'Mohon tunggu, sistem sedang mencocokkan NIK dan menonaktifkan kepesertaan.',
            allowOutsideClick: false,
            customClass: {
                popup: 'swal-sm rounded-4'
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= site_url('pbi/data/import_nonaktif') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                fileInput.value = ""; // Reset input file
                if (response.status) {
                    table.ajax.reload(null, false);
                    // 🚀 Panggil fungsi modular auto-close (2 detik, tanpa tombol OK)
                    showSuccess(response.message, 'Import Selesai!', true);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Import',
                        text: response.message,
                        customClass: {
                            popup: 'swal-sm rounded-4'
                        }
                    });
                }
            },
            error: function() {
                fileInput.value = "";
                Swal.fire({
                    icon: 'error',
                    title: 'Error Server',
                    text: 'Terjadi kesalahan jaringan atau format file tidak didukung.',
                    customClass: {
                        popup: 'swal-sm rounded-4'
                    }
                });
            }
        });
    }

    // 🔄 Fungsi Membatalkan Penonaktifan (Rollback)
    function kembalikanAktif(id, nama) {
        Swal.fire({
            title: 'Batalkan Non-Aktif?',
            html: `Data <b>${nama}</b> akan dikembalikan ke daftar PBI Aktif.<br>Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Kembalikan',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('pbi/nonaktif/restore') ?>',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(res) {
                        if (res.status) {
                            table.ajax.reload(null, false);
                            // 🚀 Panggil fungsi modular auto-close
                            showSuccess(res.message);
                        }
                    }
                });
            }
        });
    }

    // 📋 Fungsi Salin Teks (NIK / KK) dengan Toast SweetAlert2 Otomatis
    window.copyText = function(text) {
        navigator.clipboard.writeText(text).then(function() {
            SwalToast.fire({
                title: text + ' disalin!'
            });
        }).catch(function(err) {
            console.error('Gagal menyalin text: ', err);
        });
    };
</script>
<?= $this->endSection() ?>