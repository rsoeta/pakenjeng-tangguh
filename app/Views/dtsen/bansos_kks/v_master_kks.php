<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-id-card text-primary mr-2"></i> <?= $title; ?>
                    </h1>
                </div>
                <div class="col-sm-6 text-right mt-3 mt-sm-0">
                    <?php if (session()->get('role_id') <= 3): ?>
                        <button type="button" class="btn btn-success btn-sm shadow-sm rounded-pill px-3 mr-1" data-toggle="modal" data-target="#modalImport">
                            <i class="fas fa-file-excel mr-1"></i> Impor Data (Excel)
                        </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm rounded-pill px-3" id="btnTambahKPM">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah KPM
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded border-0">
                    <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card card-outline card-primary shadow-sm border-0 rounded-lg">
                <div class="card-header bg-light border-bottom p-3">
                    <div class="row align-items-end">
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <label for="filter_rw" class="small font-weight-bold text-muted mb-1">Wilayah RW</label>
                            <select id="filter_rw" class="form-control form-control-sm border-0 shadow-sm rounded-pill px-3">
                                <option value="">-- Semua RW --</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <label for="filter_rt" class="small font-weight-bold text-muted mb-1">Wilayah RT</label>
                            <select id="filter_rt" class="form-control form-control-sm border-0 shadow-sm rounded-pill px-3">
                                <option value="">-- Semua RT --</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <label for="filter_status" class="small font-weight-bold text-muted mb-1">Status KKS</label>
                            <select id="filter_status" class="form-control form-control-sm border-0 shadow-sm rounded-pill px-3">
                                <option value="">-- Semua Status --</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Non Aktif">Non Aktif</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <button id="btn_filter" class="btn btn-sm btn-dark btn-block shadow-sm rounded-pill">
                                <i class="fas fa-filter mr-1"></i> Terapkan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tableMasterKKS" class="table table-hover table-striped mb-0" style="width:100%;">
                            <thead class="text-muted bg-white">
                                <tr>
                                    <th width="5%" class="text-center border-top-0">No</th>
                                    <th class="border-top-0">NIK</th>
                                    <th class="border-top-0">Nama KPM</th>
                                    <th class="border-top-0">No. KKS</th>
                                    <th class="border-top-0">Alamat Lengkap</th>
                                    <th class="border-top-0 text-center">Status</th>
                                    <th width="10%" class="text-center border-top-0"><i class="fas fa-cogs"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-upload mr-2"></i> Impor Data KKS</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="<?= base_url('master-kks/import'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="alert alert-warning small">
                        <i class="fas fa-info-circle mr-1"></i> <strong>Instruksi:</strong> Simpan file Google Sheets Anda sebagai format <strong>.xlsx</strong> terlebih dahulu. Pastikan urutan kolom sesuai dengan struktur aslinya.
                    </div>
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">Mulai Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKKS" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Tambah Data KPM</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKKS">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" id="kpm_id">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Cari Data Penduduk (NIK/Nama) <span class="text-danger">*</span></label>
                        <select class="form-control" name="nik" id="nik" style="width: 100%;" required></select>
                    </div>
                    <div class="form-group">
                        <label>Nama KPM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_penerima" id="nama_penerima" required readonly>
                    </div>
                    <div class="form-group">
                        <label>Alamat / Kampung</label>
                        <textarea class="form-control" name="alamat" id="alamat" rows="2" readonly></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>RW <span class="text-danger">*</span></label>
                                <input type="text" inputmode="numeric" class="form-control" name="rw" id="rw" required readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>RT <span class="text-danger">*</span></label>
                                <input type="text" inputmode="numeric" class="form-control" name="rt" id="rt" required readonly>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-2 mb-3">

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label style="font-size: 0.85rem;">Nomor KKS</label>
                                <input type="text" class="form-control form-control-sm" name="no_kks" id="no_kks" placeholder="Kosongkan jika tdk ada">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label style="font-size: 0.85rem;">No. WhatsApp</label>
                                <input type="number" class="form-control form-control-sm" name="no_wa" id="no_wa" placeholder="08xxxx">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label style="font-size: 0.85rem;">Status KKS <span class="text-danger">*</span></label>
                                <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                    <label class="btn btn-sm btn-outline-success flex-fill active" id="lbl_status_aktif">
                                        <input type="radio" name="status_kks" id="status_aktif" value="Aktif" required checked> Aktif
                                    </label>
                                    <label class="btn btn-sm btn-outline-danger flex-fill" id="lbl_status_nonaktif">
                                        <input type="radio" name="status_kks" id="status_nonaktif" value="Non Aktif"> Non Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label style="font-size: 0.85rem;">Upload Foto KKS</label>
                        <div class="text-center p-2 border rounded" style="background-color: #f8f9fa; height: 180px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            <img id="preview_foto" src="" alt="Preview Foto KKS" class="img-fluid rounded shadow-sm" style="max-height: 100%; display: none;">

                            <iframe id="preview_iframe" src="" style="width: 100%; height: 100%; border: none; display: none;"></iframe>

                            <div id="placeholder_foto" class="text-muted">
                                <i class="fas fa-camera fa-3x mb-2 d-block text-secondary"></i>
                                <span style="font-size: 0.8rem;">Belum ada foto</span>
                            </div>
                        </div>
                        <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" name="foto_kks" id="foto_kks" accept="image/jpeg, image/png">
                            <label class="custom-file-label col-form-label-sm" for="foto_kks">Pilih file foto...</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanKPM">
                        <i class="fas fa-save"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // Aksi Tombol Terapkan Filter
        $('#btn_filter').click(function() {
            tableKKS.ajax.reload(); // Refresh tabel bawa data filter baru
        });

        // 1. Load RW Dinamis saat halaman pertama kali dibuka
        $.ajax({
            url: '<?= base_url('master-kks/get-rw') ?>',
            type: 'GET',
            success: function(res) {
                $('#filter_rw').html(res);
            }
        });

        // 2. Load RT Dinamis saat RW dipilih
        $('#filter_rw').change(function() {
            var rw = $(this).val();
            if (rw !== '') {
                $.ajax({
                    url: '<?= base_url('master-kks/get-rt') ?>',
                    type: 'POST',
                    data: {
                        rw: rw,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(res) {
                        $('#filter_rt').html(res);
                    }
                });
            } else {
                // Reset RT jika milih -- Semua RW --
                $('#filter_rt').html('<option value="">-- Semua RT --</option>');
            }
        });

        // 3. Modifikasi inisialisasi DataTables (Tambahkan parameter d)
        var tableKKS = $('#tableMasterKKS').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            // ... (setting processing, serverSide seperti instruksi sebelumnya)
            ajax: {
                url: "<?= base_url('master-kks/datatable') ?>",
                type: "POST",
                data: function(d) {
                    d.filter_rw = $('#filter_rw').val();
                    d.filter_rt = $('#filter_rt').val();
                    d.filter_status = $('#filter_status').val();
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            columnDefs: [{
                    targets: [0, 6],
                    orderable: false,
                    className: 'text-center'
                },
                {
                    targets: [4, 5],
                    className: 'text-center'
                }
            ],
            language: {
                // Tambahkan https: secara eksplisit
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });

        // 4. Aksi Tombol Terapkan Filter
        $('#btn_filter').click(function() {
            tableKKS.ajax.reload(); // Tabel otomatis ambil data filter terbaru
        });

        // ==========================================
        // 📝 LOGIKA CRUD: TAMBAH, EDIT & SELECT2
        // ==========================================

        // Inisialisasi Select2
        $('#nik').select2({
            dropdownParent: $('#modalKKS'), // Agar dropdown tidak sembunyi di belakang modal
            placeholder: '-- Ketik NIK atau Nama --',
            ajax: {
                url: '<?= base_url('master-kks/search-penduduk') ?>',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchTerm: params.term,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    };
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });

        // Otomatis isi kolom saat data penduduk dipilih
        $('#nik').on('select2:select', function(e) {
            var data = e.params.data;
            $('#nama_penerima').val(data.nama);
            $('#alamat').val(data.alamat);

            // GANTI INI: Format jadi 3 digit!
            $('#rw').val(String(data.rw || 0).padStart(3, '0'));
            $('#rt').val(String(data.rt || 0).padStart(3, '0'));
        });

        // 1. Klik Tombol Tambah
        $('#btnTambahKPM').click(function() {
            $('#formKKS')[0].reset();
            $('#kpm_id').val('');
            $('#nik').empty().trigger('change'); // Kosongkan Select2
            $('#modalTitle').text('Tambah Data KPM');
            $('#modalKKS').modal('show');
        });

        // 2. Klik Tombol Edit
        $('#tableMasterKKS tbody').on('click', '.btn-edit', function() {
            var id = $(this).data('id');

            $.ajax({
                url: "<?= base_url('master-kks/get-kpm') ?>",
                type: "POST",
                data: {
                    id: id,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: "JSON",
                success: function(data) {
                    $('#formKKS')[0].reset();
                    $('#kpm_id').val(data.id);

                    // Set data Select2 secara manual untuk mode Edit
                    var option = new Option(data.nik + ' - ' + data.nama_penerima, data.nik, true, true);
                    $('#nik').append(option).trigger('change');

                    // (Di dalam bagian ajax success Edit Data)
                    $('#nama_penerima').val(data.nama_penerima);
                    $('#no_kks').val(data.no_kks === '-' ? '' : data.no_kks);
                    $('#no_wa').val(data.no_wa);
                    $('#alamat').val(data.alamat);

                    // GANTI INI: Format jadi 3 digit saat Edit diklik!
                    $('#rw').val(String(data.rw || 0).padStart(3, '0'));
                    $('#rt').val(String(data.rt || 0).padStart(3, '0'));
                    $('#status_kks').val(data.status_kks);

                    $('#modalTitle').text('Edit Data KPM');
                    $('#modalKKS').modal('show');
                }
            });
        });

        // 3. Submit Form (Pake FormData karena ada upload file!)
        $('#formKKS').submit(function(e) {
            e.preventDefault();
            $('#btnSimpanKPM').text('Menyimpan...').prop('disabled', true);

            var formData = new FormData(this); // Mengambil semua input termasuk File Foto

            $.ajax({
                url: "<?= base_url('master-kks/save') ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function(response) {
                    $('#btnSimpanKPM').text('Simpan Data').prop('disabled', false);

                    if (response.status === 'success') {
                        $('#modalKKS').modal('hide');
                        Swal.fire('Berhasil!', response.message, 'success');
                        tableKKS.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function() {
                    $('#btnSimpanKPM').text('Simpan Data').prop('disabled', false);
                    Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                }
            });
        });

        // ==========================================
        // 📸 FITUR PREVIEW FOTO
        // ==========================================
        $('#foto_kks').change(function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_foto').attr('src', e.target.result).show();
                    $('#placeholder_foto').hide();
                }
                reader.readAsDataURL(file);
                $(this).next('.custom-file-label').html(file.name); // Ubah teks label jadi nama file
            } else {
                resetPreviewFoto();
            }
        });

        function resetPreviewFoto() {
            $('#preview_foto').hide().attr('src', '');
            $('#placeholder_foto').show();
            $('#foto_kks').val('');
            $('.custom-file-label').html('Pilih file foto...');
        }

        // ==========================================
        // 📝 LOGIKA MODAL TAMBAH & EDIT (UPDATE)
        // ==========================================

        // Klik Tombol Tambah
        $('#btnTambahKPM').click(function() {
            $('#formKKS')[0].reset();
            $('#kpm_id').val('');
            $('#nik').empty().trigger('change');

            // Reset Status ke Aktif (Default)
            $('#status_aktif').prop('checked', true);
            $('#lbl_status_aktif').addClass('active');
            $('#lbl_status_nonaktif').removeClass('active');

            resetPreviewFoto(); // Bersihkan preview

            $('#modalTitle').text('Tambah Data KPM');
            $('#modalKKS').modal('show');
        });

        // Klik Tombol Edit
        $('#tableMasterKKS tbody').on('click', '.btn-edit', function() {
            var id = $(this).data('id');

            $.ajax({
                url: "<?= base_url('master-kks/get-kpm') ?>", // Pastikan URL sesuai route baru Anda!
                type: "POST",
                data: {
                    id: id,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: "JSON",
                success: function(data) {
                    $('#formKKS')[0].reset();
                    resetPreviewFoto(); // Bersihkan sisa form sebelumnya

                    $('#kpm_id').val(data.id);
                    var option = new Option(data.nik + ' - ' + data.nama_penerima, data.nik, true, true);
                    $('#nik').append(option).trigger('change');

                    $('#nama_penerima').val(data.nama_penerima);
                    $('#no_kks').val(data.no_kks === '-' ? '' : data.no_kks); // Hilangkan strip di form edit
                    $('#no_wa').val(data.no_wa);
                    $('#alamat').val(data.alamat);
                    $('#rw').val(parseInt(data.rw));
                    $('#rt').val(parseInt(data.rt));

                    // Logic Radio Button Status
                    if (data.status_kks.toLowerCase() === 'aktif') {
                        $('#status_aktif').prop('checked', true);
                        $('#lbl_status_aktif').addClass('active');
                        $('#lbl_status_nonaktif').removeClass('active');
                    } else {
                        $('#status_nonaktif').prop('checked', true);
                        $('#lbl_status_nonaktif').addClass('active');
                        $('#lbl_status_aktif').removeClass('active');
                    }

                    // ==========================================
                    // 📸 FITUR PREVIEW FOTO (IMG & IFRAME)
                    // ==========================================
                    function resetPreviewFoto() {
                        $('#preview_foto').hide().attr('src', '');
                        $('#preview_iframe').hide().attr('src', ''); // Matikan juga iframe
                        $('#placeholder_foto').show();
                        $('#foto_kks').val('');
                        $('.custom-file-label').html('Pilih file foto...');
                    }

                    // Saat milih file dari HP/Laptop (Pakai IMG)
                    $('#foto_kks').change(function() {
                        var file = this.files[0];
                        if (file) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                $('#preview_foto').attr('src', e.target.result).show();
                                $('#preview_iframe').hide(); // Sembunyikan iframe G-Drive
                                $('#placeholder_foto').hide();
                            }
                            reader.readAsDataURL(file);
                            $(this).next('.custom-file-label').html(file.name);
                        } else {
                            resetPreviewFoto();
                        }
                    });

                    // ... (di dalam fungsi .btn-edit AJAX Success) ...

                    // ==========================================
                    // 📸 LOGIKA TAMPILKAN FOTO (ANTI BADAI G-DRIVE)
                    // ==========================================
                    // 1. Prioritas: Cek foto lokal (hasil upload SINDEN)
                    if (data.foto_kks && data.foto_kks !== '') {
                        $('#preview_foto').attr('src', '<?= base_url() ?>/' + data.foto_kks).show();
                        $('#preview_iframe').hide();
                        $('#placeholder_foto').hide();
                    }
                    // 2. Alternatif: Cek data warisan Google Drive
                    else if (data.foto_kepemilikan && data.foto_kepemilikan !== '') {
                        var driveUrl = data.foto_kepemilikan;
                        var match = driveUrl.match(/(?:id=|\/d\/)([a-zA-Z0-9_-]{25,})/);

                        if (match && match[1]) {
                            var fileId = match[1];
                            // 🚀 KUNCI: Gunakan endpoint /preview resmi Google
                            var iframeUrl = 'https://drive.google.com/file/d/' + fileId + '/preview';

                            $('#preview_iframe').attr('src', iframeUrl).show();
                            $('#preview_foto').hide();
                            $('#placeholder_foto').hide();
                        } else {
                            // Jika URL sama sekali tidak dikenali
                            $('#placeholder_foto').show().html('<span class="text-danger text-sm">URL tidak valid</span>');
                        }
                    } else {
                        resetPreviewFoto(); // Jika keduanya kosong
                    }

                    $('#modalTitle').text('Edit Data KPM');
                    $('#modalKKS').modal('show');

                    $('#modalTitle').text('Edit Data KPM');
                    $('#modalKKS').modal('show');
                }
            });
        });

        // ==========================================
        // 🗑️ LOGIKA HAPUS DATA (SWEETALERT)
        // ==========================================
        $('#tableMasterKKS tbody').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');

            Swal.fire({
                title: 'Hapus Data KPM?',
                html: "Anda yakin ingin menghapus data <b>" + nama + "</b>?<br><small class='text-danger'>Data dan foto yang dihapus tidak dapat dikembalikan.</small>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                reverseButtons: true // Tukar posisi tombol (Batal di kiri, Hapus di kanan)
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "<?= base_url('master-kks/delete') ?>",
                        type: "POST",
                        data: {
                            id: id,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: "JSON",
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Terhapus!', response.message, 'success');
                                tableKKS.ajax.reload(null, false); // Refresh tabel tanpa mereset paginasi
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Terjadi kesalahan pada server saat menghapus data.', 'error');
                        }
                    });
                }
            });
        });

    });
</script>

<?= $this->endSection(); ?>