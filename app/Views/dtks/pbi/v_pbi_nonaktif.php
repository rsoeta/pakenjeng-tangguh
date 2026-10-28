<?= $this->extend('templates/index'); ?>

<?= $this->section('content') ?>

<?php
$userRole = (int) ($roleId ?? session()->get('role_id') ?? 99);
$canRollback = ($userRole < 4);
?>

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
                            <?php if ($canRollback): ?>
                                <!-- Form Hidden untuk Upload Excel Non-Aktif -->
                                <input type="file" id="fileExcelNonAktif" class="d-none" accept=".xls,.xlsx" onchange="uploadExcelNonAktif()">

                                <!-- Tombol Rollback Bulk di Action Bar -->
                                <button type="button" id="btnBulkRollbackTop" class="btn btn-sm btn-outline-danger shadow-sm d-none" onclick="bulkRollback()" title="Rollback Data Terpilih">
                                    <i class="fas fa-undo me-1"></i> Rollback (<span class="selectedCountBadge">0</span>)
                                </button>

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
                    <?php if ($canRollback): ?>
                        <!-- ⚡ FLOATING / INLINE BULK ACTION BAR -->
                        <div id="bulkActionBar" class="alert alert-light border border-danger py-2 px-3 mb-3 d-none align-items-center justify-content-between shadow-sm rounded-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger p-2 me-2 rounded-circle">
                                    <i class="fas fa-check"></i>
                                </span>
                                <div>
                                    <span class="fw-bold text-dark"><span class="selectedCountText">0</span> data dipilih</span>
                                    <small class="text-muted d-block d-sm-inline ms-sm-1">(pilih data untuk rollback sekaligus)</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary shadow-sm" onclick="clearSelection()">
                                    <i class="fas fa-times me-1"></i> Batal Pilih
                                </button>
                                <button type="button" class="btn btn-sm btn-success shadow-sm fw-bold" onclick="bulkRollback()">
                                    <i class="fas fa-undo me-1"></i> Rollback Terpilih (<span class="selectedCountBadge">0</span>)
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <table id="tablePbiNonAktif" class="table table-striped table-hover table-bordered w-100" style="font-size: 0.9rem;">
                        <thead class="table-danger text-center align-middle">
                            <tr>
                                <?php if ($canRollback): ?>
                                    <th width="3%" class="text-center align-middle" style="vertical-align: middle;">
                                        <input type="checkbox" id="checkAll" class="form-check-input m-0" style="cursor: pointer; width: 1.15rem; height: 1.15rem;" title="Pilih Semua di Halaman Ini">
                                    </th>
                                <?php endif; ?>
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
    const canRollback = <?= json_encode($canRollback) ?>;
    let selectedIds = new Set();

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
            "order": [], // Pertahankan urutan dari server (tanggal_nonaktif DESC)
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
                    columns: canRollback ? [1, 2, 3, 4, 5] : [0, 1, 2, 3, 4], // Tanpa checkbox dan tanpa kolom aksi
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
            "columnDefs": canRollback ? [{
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center align-middle",
                    "targets": [0, 6] // Checkbox dan Aksi
                },
                {
                    "className": "text-center align-middle",
                    "targets": [1, 4] // No dan Tanggal
                },
                {
                    "className": "align-middle",
                    "targets": [2, 3, 5] // Nama & NIK, Alasan, Alamat
                }
            ] : [{
                    "orderable": false,
                    "targets": [5] // Aksi
                },
                {
                    "className": "align-middle",
                    "targets": [1, 2, 4] // Nama & NIK, Alasan, Alamat
                }
            ],
            "drawCallback": function(settings) {
                if (canRollback) {
                    $('.row-checkbox').each(function() {
                        let val = String($(this).val());
                        if (selectedIds.has(val)) {
                            $(this).prop('checked', true);
                        }
                    });
                    syncCheckAllState();
                }
            }
        });

        // 🔥 Gabungkan semua filter ke dalam satu listener
        $('#filter_rw, #filter_rt, #filter_alasan').on('change', function() {
            clearSelection();
            table.ajax.reload(null, false);
        });

        if (canRollback) {
            // Event listener Pilih Semua di halaman aktif
            $('#checkAll').on('change', function() {
                let isChecked = $(this).is(':checked');
                $('.row-checkbox').each(function() {
                    $(this).prop('checked', isChecked);
                    let val = String($(this).val());
                    if (isChecked) {
                        selectedIds.add(val);
                    } else {
                        selectedIds.delete(val);
                    }
                });
                updateSelectionUI();
            });

            // Event listener checkbox baris
            $(document).on('change', '.row-checkbox', function() {
                let val = String($(this).val());
                if ($(this).is(':checked')) {
                    selectedIds.add(val);
                } else {
                    selectedIds.delete(val);
                }
                syncCheckAllState();
                updateSelectionUI();
            });

            // Klik pada sel checkbox kolom 0 untuk toggle checkbox
            $('#tablePbiNonAktif tbody').on('click', 'td:first-child', function(e) {
                if (!$(e.target).is(':checkbox')) {
                    let chk = $(this).find('.row-checkbox');
                    if (chk.length) {
                        chk.prop('checked', !chk.prop('checked')).trigger('change');
                    }
                }
            });
        }
    });

    // 🔄 Sinkronisasi Status Checkbox 'Pilih Semua'
    function syncCheckAllState() {
        if (!canRollback) return;
        let totalVisible = $('.row-checkbox').length;
        let totalChecked = $('.row-checkbox:checked').length;
        if (totalVisible > 0 && totalVisible === totalChecked) {
            $('#checkAll').prop('checked', true).prop('indeterminate', false);
        } else if (totalChecked > 0) {
            $('#checkAll').prop('checked', false).prop('indeterminate', true);
        } else {
            $('#checkAll').prop('checked', false).prop('indeterminate', false);
        }
    }

    // 📊 Perbarui Tampilan Counter & Bar Rollback Bulk
    function updateSelectionUI() {
        let count = selectedIds.size;
        $('.selectedCountBadge').text(count);
        $('.selectedCountText').text(count);

        if (count > 0) {
            $('#bulkActionBar').removeClass('d-none').addClass('d-flex');
            $('#btnBulkRollbackTop').removeClass('d-none');
        } else {
            $('#bulkActionBar').addClass('d-none').removeClass('d-flex');
            $('#btnBulkRollbackTop').addClass('d-none');
        }
    }

    // ❌ Batalkan Semua Pilihan
    function clearSelection() {
        selectedIds.clear();
        $('.row-checkbox').prop('checked', false);
        $('#checkAll').prop('checked', false).prop('indeterminate', false);
        updateSelectionUI();
    }

    function reloadTable() {
        clearSelection();
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

    // 🔄 Fungsi Membatalkan Penonaktifan Tunggal (Rollback)
    function kembalikanAktif(id, nama) {
        Swal.fire({
            title: 'Batalkan Non-Aktif?',
            html: `Data <b>${nama}</b> akan dikembalikan ke daftar PBI Aktif.<br>Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-undo me-1"></i> Ya, Kembalikan',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-sm rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('pbi/nonaktif/restore') ?>',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            if (typeof selectedIds !== 'undefined' && selectedIds.has(String(id))) {
                                selectedIds.delete(String(id));
                                updateSelectionUI();
                            }
                            table.ajax.reload(null, false);
                            // 🚀 Panggil fungsi modular auto-close
                            showSuccess(res.message);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan saat memulihkan data.',
                                customClass: {
                                    popup: 'swal-sm rounded-4'
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Server',
                            text: 'Terjadi kesalahan jaringan atau server.',
                            customClass: {
                                popup: 'swal-sm rounded-4'
                            }
                        });
                    }
                });
            }
        });
    }

    // ⚡ Fungsi Membatalkan Penonaktifan Massal (Bulk Rollback)
    function bulkRollback() {
        if (!canRollback) return;

        let ids = Array.from(selectedIds);
        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Silakan pilih setidaknya satu data yang ingin dikembalikan.',
                customClass: {
                    popup: 'swal-sm rounded-4'
                }
            });
            return;
        }

        Swal.fire({
            title: 'Rollback ' + ids.length + ' Data Terpilih?',
            html: `Sebanyak <b>${ids.length}</b> data PBI Non-Aktif akan dikembalikan ke status <b>Aktif</b>.<br><small class="text-muted">Apakah Anda yakin ingin melanjutkan?</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-undo me-1"></i> Ya, Rollback Semua',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-sm rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Rollback...',
                    text: 'Mohon tunggu, sistem sedang memperbarui status kepesertaan...',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'swal-sm rounded-4'
                    },
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= site_url('pbi/nonaktif/restore_bulk') ?>',
                    type: 'POST',
                    data: {
                        ids: ids
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            clearSelection();
                            table.ajax.reload(null, false);
                            showSuccess(res.message, 'Rollback Berhasil!', false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Rollback',
                                text: res.message,
                                customClass: {
                                    popup: 'swal-sm rounded-4'
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Server',
                            text: 'Terjadi kesalahan sistem saat memproses rollback massal.',
                            customClass: {
                                popup: 'swal-sm rounded-4'
                            }
                        });
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