<?= $this->extend('templates/index'); ?>

<?= $this->section('content') ?>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid py-4">
            <div class="row mb-3">
                <div class="col-12">
                    <h4 class="mb-0 text-success fw-bold"><i class="fas fa-id-card-alt me-2"></i> Master Data PBI-JKN (Aktif)</h4>
                    <p class="text-muted">Data penerima bantuan iuran Jaminan Kesehatan Nasional yang berstatus Aktif.</p>
                </div>
            </div>

            <!-- 🎛️ FILTER PANEL DINAMIS (Mobile Friendly 2-Kolom) -->
            <div class="card shadow-sm border-top-success mb-4">
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <label class="small fw-bold text-muted">Filter RW</label>
                            <select id="filter_rw" class="form-control form-control-sm select2">
                                <option value="">-- Semua RW --</option>
                                <?php for ($i = 1; $i <= 15; $i++): ?>
                                    <option value="<?= $i ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="small fw-bold text-muted">Filter RT</label>
                            <select id="filter_rt" class="form-control form-control-sm select2">
                                <option value="">-- Semua RT --</option>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= str_pad($i, 3, '0', STR_PAD_LEFT) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- 🛠️ TEMPAT KUMPULAN TOMBOL AKSI -->
                        <div class="col-12 col-md-6 d-flex align-items-end justify-content-md-end mt-2 mt-md-0 flex-wrap gap-2">
                            <?php if (session()->get('role_id') < 4): ?>

                                <!-- Tombol Tambah Manual -->
                                <button class="btn btn-sm btn-primary shadow-sm" onclick="tambahPbiManual()">
                                    <i class="fas fa-plus me-1"></i> Tambah
                                </button>

                                <!-- Form Hidden untuk Upload -->
                                <input type="file" id="fileExcel" class="d-none" accept=".xls,.xlsx" onchange="uploadExcel()">

                                <!-- Tombol Import -->
                                <button class="btn btn-sm btn-info text-white shadow-sm" onclick="document.getElementById('fileExcel').click()">
                                    <i class="fas fa-file-import me-1"></i> Import
                                </button>

                                <!-- Tombol Export (Trigger External) -->
                                <button class="btn btn-sm btn-success shadow-sm" onclick="exportExcelLuar()">
                                    <i class="fas fa-file-excel me-1"></i> Export
                                </button>
                            <?php endif; ?>

                            <!-- Tombol Refresh (Pakai Ikon Saja agar Hemat Tempat) -->
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
                    <table id="tablePbiAktif" class="table table-striped table-hover table-bordered w-100" style="font-size: 0.9rem;">
                        <thead class="table-success text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Nama & NIK</th>
                                <th width="15%">No. KK</th>
                                <th width="15%">No. KIS</th>
                                <th width="15%">Faskes Tk. 1</th>
                                <th width="20%">Alamat</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data dimuat melalui AJAX -->
                        </tbody>
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
        // 🚀 Inisialisasi DataTables dengan "responsive": true
        table = $('#tablePbiAktif').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true, // MANTRA WAJIB MOBILE
            "ajax": {
                "url": "<?= site_url('pbi/data/datatable') ?>",
                "type": "POST",
                "data": function(d) {
                    d.rw = $('#filter_rw').val();
                    d.rt = $('#filter_rt').val();
                }
            },
            "dom": '<"row align-items-center"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            "buttons": [{
                extend: 'excelHtml5',
                className: 'd-none btn-excel-hidden',
                title: 'Master Data PBI-JKN Aktif',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5],
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
                    "targets": [0, 6]
                },
                {
                    "className": "align-middle",
                    "targets": [1, 2, 3, 4, 5]
                },
                {
                    "orderable": false,
                    "targets": [6]
                }
            ]
        });

        // Event listener untuk filter dropdown
        $('#filter_rw, #filter_rt').on('change', function() {
            table.ajax.reload(null, false);
        });
    });

    function reloadTable() {
        table.ajax.reload(null, false);
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

    // 🚷 Fungsi Konfirmasi Non-Aktif (Update dengan Textarea Dinamis)
    function nonAktifkan(id, nama) {
        Swal.fire({
            title: 'Non-Aktifkan Data?',
            html: `Anda akan menonaktifkan PBI-JKN atas nama <b>${nama}</b>.<br><br>
                   <select id="alasan_nonaktif" class="form-control form-control-sm mb-2" onchange="toggleAlasan(this.value)">
                       <option value="">-- Pilih Alasan --</option>
                       <option value="MENINGGAL DUNIA">MENINGGAL DUNIA</option>
                       <option value="PINDAH DOMISILI">PINDAH DOMISILI</option>
                       <option value="DINONAKTIFKAN PUSAT/MAMPU">DINONAKTIFKAN PUSAT / MAMPU</option>
                       <option value="Lainnya">LAINNYA...</option>
                   </select>
                   <textarea id="alasan_lainnya" class="form-control form-control-sm d-none mt-2" rows="3" placeholder="Tuliskan alasan spesifik di sini..."></textarea>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Non-Aktifkan',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-sm'
            },
            preConfirm: () => {
                let alasan = document.getElementById('alasan_nonaktif').value;
                let alasanLain = document.getElementById('alasan_lainnya').value.trim();

                if (!alasan) {
                    Swal.showValidationMessage('Alasan utama harus dipilih!');
                    return false;
                }

                if (alasan === 'Lainnya') {
                    if (!alasanLain) {
                        Swal.showValidationMessage('Detail alasan lainnya tidak boleh kosong!');
                        return false;
                    }
                    alasan = alasanLain;
                }

                return {
                    alasan: alasan
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('pbi/data/proses_nonaktif') ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        alasan: result.value.alasan
                    },
                    success: function(res) {
                        if (res.status) {
                            table.ajax.reload(null, false);
                            // 🚀 Panggil fungsi modular auto-close
                            showSuccess('Data dipindahkan ke menu Non-Aktif.');
                        }
                    }
                });
            }
        });
    }

    // 🔄 Trik Menampilkan Text Area secara Real-time
    function toggleAlasan(value) {
        let txtLainnya = document.getElementById('alasan_lainnya');
        if (value === 'Lainnya') {
            txtLainnya.classList.remove('d-none');
            txtLainnya.focus();
        } else {
            txtLainnya.classList.add('d-none');
            txtLainnya.value = '';
        }
    }

    // 🚀 Fungsi Upload Excel dengan SweetAlert2 Compact
    function uploadExcel() {
        let fileInput = document.getElementById('fileExcel');
        let file = fileInput.files[0];

        if (!file) return;

        let formData = new FormData();
        formData.append('file_excel', file);

        Swal.fire({
            title: 'Mengimpor Data...',
            text: 'Mohon tunggu, sistem sedang memetakan NIK dan alamat.',
            allowOutsideClick: false,
            customClass: {
                popup: 'swal-sm'
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= site_url('pbi/data/import_excel') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                fileInput.value = ""; // Reset input
                if (response.status) {
                    table.ajax.reload(null, false);
                    // 🚀 Panggil fungsi modular auto-close dengan parameter HTML = true
                    showSuccess(response.message, 'Import Selesai!', true);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message,
                        customClass: {
                            popup: 'swal-sm'
                        }
                    });
                }
            },
            error: function() {
                fileInput.value = "";
                Swal.fire({
                    icon: 'error',
                    title: 'Error Server',
                    text: 'Terjadi kesalahan jaringan.',
                    customClass: {
                        popup: 'swal-sm'
                    }
                });
            }
        });
    }

    // 🚀 Fungsi Pemicu Export Excel (Full Data Server-Side Workaround)
    function exportExcelLuar() {
        Swal.fire({
            title: 'Menyiapkan Data...',
            text: 'Membaca seluruh data dari server untuk diekspor.',
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

    // 🚀 Fungsi Tambah PBI Manual (Real AJAX dengan Jenis Kepesertaan)
    function tambahPbiManual() {
        Swal.fire({
            title: 'Cek Data Penduduk',
            text: 'Masukkan 16 Digit NIK Warga',
            input: 'text',
            inputAttributes: {
                maxlength: 16,
                pattern: '[0-9]*',
                autocomplete: 'off'
            },
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-search"></i> Cek NIK',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            customClass: {
                popup: 'swal-sm'
            },
            preConfirm: (nik) => {
                if (!nik || nik.length !== 16) {
                    Swal.showValidationMessage('NIK harus lengkap 16 digit!');
                    return false;
                }

                return $.ajax({
                    url: '<?= site_url('pbi/data/cek_nik') ?>',
                    type: 'POST',
                    data: {
                        nik: nik
                    }
                }).then(response => {
                    if (!response.status) {
                        throw new Error(response.message);
                    }
                    return response.data;
                }).catch(error => {
                    Swal.showValidationMessage(error.message || 'Gagal menghubungi server');
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                let w = result.value;

                Swal.fire({
                    title: 'Lengkapi Data BPJS',
                    html: `
                        <div class="text-start small mb-3 p-2 bg-light rounded border">
                            <b class="text-primary">${w.nama}</b><br>
                            NIK: ${w.nik} | KK: ${w.no_kk}<br>
                            Alamat: ${w.kampung} RT ${w.rt} / RW ${w.rw}
                        </div>
                        
                        <label class="small text-muted float-start fw-bold">Jenis Kepesertaan <span class="text-danger">*</span></label>
                        <select id="jenis_kepesertaan_manual" class="form-select form-select-sm mb-2 shadow-sm border-primary">
                            <option value="PBI APBN">PBI APBN (Pusat)</option>
                            <option value="PBI APBD">PBI APBD (Daerah)</option>
                            <option value="PBPU / MANDIRI">PBPU / MANDIRI</option>
                            <option value="LAINNYA">LAINNYA</option>
                        </select>

                        <label class="small text-muted float-start fw-bold">Nomor KIS (Opsional)</label>
                        <input type="text" id="kis_manual" class="form-control form-control-sm mb-2" placeholder="000xxxxxx">
                        
                        <label class="small text-muted float-start fw-bold">Faskes Tk. 1 (Opsional)</label>
                        <input type="text" id="faskes_manual" class="form-control form-control-sm mb-2" placeholder="Cth: Puskesmas Pakenjeng">
                    `,
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    confirmButtonText: '<i class="fas fa-save"></i> Simpan',
                    customClass: {
                        popup: 'swal-sm'
                    },
                    preConfirm: () => {
                        return {
                            nik: w.nik,
                            id_art: w.id_art,
                            no_kk: w.no_kk,
                            nama: w.nama,
                            kampung: w.kampung,
                            rt: w.rt,
                            rw: w.rw,
                            pbi_id: w.pbi_id,
                            jenis_kepesertaan: document.getElementById('jenis_kepesertaan_manual').value,
                            no_kis: document.getElementById('kis_manual').value,
                            faskes_tk1: document.getElementById('faskes_manual').value
                        }
                    }
                }).then((res) => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: '<?= site_url('pbi/data/simpan_manual') ?>',
                            type: 'POST',
                            data: res.value,
                            success: function(r) {
                                if (r.status) {
                                    table.ajax.reload(null, false);
                                    // 🚀 Panggil fungsi modular auto-close
                                    showSuccess(r.message);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Menyimpan!',
                                        text: r.message,
                                        customClass: {
                                            popup: 'swal-sm'
                                        }
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Koneksi Terputus!',
                                    text: 'Terjadi kesalahan pada server (Error 500/Network).',
                                    customClass: {
                                        popup: 'swal-sm'
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    }

    // 🚀 Fungsi Edit PBI (Memanfaatkan endpoint simpan_manual yang sudah ada)
    function editPbi(id, nik, id_art, no_kk, nama, kampung, rt, rw, no_kis, jenis_kepesertaan, faskes_tk1) {

        let isLainnya = (jenis_kepesertaan !== 'PBI APBN' && jenis_kepesertaan !== 'PBI APBD' && jenis_kepesertaan !== 'PBPU / MANDIRI' && jenis_kepesertaan !== '');

        Swal.fire({
            title: 'Edit Data BPJS',
            html: `
                <div class="text-start small mb-3 p-2 bg-light rounded border">
                    <b class="text-primary">${nama}</b><br>
                    NIK: ${nik} | KK: ${no_kk || '-'}<br>
                    Alamat: ${kampung || '-'} RT ${rt} / RW ${rw}
                    <hr class="my-1 border-secondary">
                    <span class="text-muted" style="font-size: 0.75rem;"><em>*Identitas pribadi otomatis disinkronkan dari database kependudukan.</em></span>
                </div>
                
                <label class="small text-muted float-start fw-bold">Jenis Kepesertaan <span class="text-danger">*</span></label>
                <select id="edit_jenis_kepesertaan" class="form-select form-select-sm mb-2 shadow-sm border-primary">
                    <option value="PBI APBN" ${jenis_kepesertaan === 'PBI APBN' ? 'selected' : ''}>PBI APBN (Pusat)</option>
                    <option value="PBI APBD" ${jenis_kepesertaan === 'PBI APBD' ? 'selected' : ''}>PBI APBD (Daerah)</option>
                    <option value="PBPU / MANDIRI" ${jenis_kepesertaan === 'PBPU / MANDIRI' ? 'selected' : ''}>PBPU / MANDIRI</option>
                    <option value="${isLainnya ? jenis_kepesertaan : 'LAINNYA'}" ${isLainnya ? 'selected' : ''}>${isLainnya ? jenis_kepesertaan : 'LAINNYA'}</option>
                </select>

                <label class="small text-muted float-start fw-bold">Nomor KIS (Opsional)</label>
                <input type="text" id="edit_kis" class="form-control form-control-sm mb-2" value="${no_kis}" placeholder="000xxxxxx">
                
                <label class="small text-muted float-start fw-bold">Faskes Tk. 1 (Opsional)</label>
                <input type="text" id="edit_faskes" class="form-control form-control-sm mb-2" value="${faskes_tk1}" placeholder="Cth: Puskesmas Pakenjeng">
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save"></i> Simpan Perubahan',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'swal-sm'
            },
            preConfirm: () => {
                return {
                    pbi_id: id,
                    nik: nik,
                    id_art: id_art,
                    no_kk: no_kk,
                    nama: nama,
                    kampung: kampung,
                    rt: rt,
                    rw: rw,
                    jenis_kepesertaan: document.getElementById('edit_jenis_kepesertaan').value,
                    no_kis: document.getElementById('edit_kis').value,
                    faskes_tk1: document.getElementById('edit_faskes').value
                }
            }
        }).then((res) => {
            if (res.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('pbi/data/simpan_manual') ?>',
                    type: 'POST',
                    data: res.value,
                    success: function(r) {
                        if (r.status) {
                            table.ajax.reload(null, false);
                            // 🚀 Panggil fungsi modular auto-close
                            showSuccess('Data kepesertaan berhasil diperbarui.');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan!',
                                text: r.message,
                                customClass: {
                                    popup: 'swal-sm'
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Koneksi Terputus!',
                            text: 'Terjadi kesalahan pada server (Error 500/Network).',
                            customClass: {
                                popup: 'swal-sm'
                            }
                        });
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>