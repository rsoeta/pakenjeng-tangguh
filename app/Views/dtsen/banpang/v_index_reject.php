<?= $this->extend('templates/index') ?> <?= $this->section('content') ?>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-id-card text-primary mr-2"></i> <?= $title; ?>
                    </h1>
                    <small class="text-muted">Dokumentasi Ulang KTP & Swafoto KPM (Data Reject BULOG)</small>
                </div>
                <div class="col-sm-6 text-right mt-3 mt-sm-0">
                    <?php if (session()->get('role_id') <= 3): ?>
                        <button class="btn btn-sm btn-success shadow-sm rounded-pill font-weight-bold" data-toggle="modal" data-target="#modalImport">
                            <i class="fas fa-file-excel mr-1"></i> Import Excel
                        </button>
                        <button type="button" class="btn btn-primary btn-sm shadow-sm rounded-pill px-3" id="btnTambahReject">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah KPM
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid mb-5">
            <?php $roleId = session()->get('role_id'); ?>
            <div class="row mb-3">
                <div class="col-12 col-md-4">
                    <select id="filterStatus" class="form-control form-control-sm border-info shadow-sm font-weight-bold text-info">
                        <option value="all">Tampilkan Semua Status</option>
                        <option value="0" <?= $roleId == 4 ? 'selected' : '' ?>>❌ Belum Difoto (Tugas Pentri)</option>
                        <option value="1" <?= $roleId < 4 ? 'selected' : '' ?>>⏳ Selesai Difoto (Menunggu Verifikasi Admin)</option>
                        <option value="2">✅ Diverifikasi & Layak</option>
                    </select>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body p-2 p-md-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered w-100 text-sm" id="tableReject">
                            <thead class="bg-light text-secondary text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">No. PBP</th>
                                    <th width="15%">NIK</th>
                                    <th>Nama KPM</th>
                                    <th width="20%">Wilayah</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Aksi</th>
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

<?php if (session()->get('role_id') == 3) : ?>
    <div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white p-3">
                    <h6 class="modal-title font-weight-bold"><i class="fas fa-upload mr-2"></i> Import Data Reject</h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formImport" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="form-group mb-0">
                            <label class="text-muted text-sm font-weight-bold">Pilih File Excel (.xls / .xlsx)</label>
                            <input type="file" name="file_excel" class="form-control-file" accept=".xls,.xlsx" required>
                            <small class="text-danger d-block mt-2">* Pastikan format kolom sesuai dengan template dari BULOG.</small>
                        </div>
                    </div>
                    <div class="modal-footer p-2 bg-light">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-cloud-upload-alt mr-1"></i> Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle mr-1"></i> Tambah KPM Reject (Manual)</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahReject">
                        <div class="form-group border-bottom pb-3">
                            <label class="font-weight-bold">Cari KPM (dari data Scan SINDEN)</label>
                            <select class="form-control" id="selectCariKpm" style="width: 100%;"></select>
                            <small class="text-muted">Ketik No. PBP, NIK, atau Nama KPM untuk mencari otomatis.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>No. PBP</label>
                                <input type="text" class="form-control" id="add_no_pbp" name="no_pbp" readonly required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nama KPM</label>
                                <input type="text" class="form-control" id="add_nama" name="nama" readonly required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>No. KK</label>
                                <input type="text" class="form-control" id="add_no_kk" name="no_kk" readonly required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>NIK</label>
                                <input type="text" class="form-control" id="add_nik" name="nik" readonly required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="row mt-2">
                                <!-- Form No. BAST (Otomatis dari DB) -->
                                <div class="col-md-4 form-group">
                                    <label>No. BAST</label>
                                    <input type="text" class="form-control" id="add_no_bast" name="no_bast" readonly required>
                                </div>

                                <!-- Form Alokasi Bulan (Manual) -->
                                <div class="col-md-12 form-group">
                                    <label class="d-block font-weight-bold mb-2">Alokasi Bulan <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap">
                                        <?php
                                        $bulan_list = [
                                            1 => 'Januari',
                                            2 => 'Februari',
                                            3 => 'Maret',
                                            4 => 'April',
                                            5 => 'Mei',
                                            6 => 'Juni',
                                            7 => 'Juli',
                                            8 => 'Agustus',
                                            9 => 'September',
                                            10 => 'Oktober',
                                            11 => 'November',
                                            12 => 'Desember'
                                        ];
                                        foreach ($bulan_list as $val => $nama) :
                                        ?>
                                            <div class="custom-control custom-radio custom-control-inline mb-2 mr-3">
                                                <input type="radio" id="bulan<?= $val ?>" name="alokasi_bulan" class="custom-control-input" value="<?= $val ?>" required>
                                                <label class="custom-control-label cursor-pointer" style="cursor: pointer;" for="bulan<?= $val ?>">
                                                    <?= $nama ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Form Alokasi Tahun (Manual) -->
                                <div class="col-md-4 form-group">
                                    <label>Alokasi Tahun <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="add_alokasi_tahun" name="alokasi_tahun" value="<?= date('Y') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-8 form-group">
                                <label>Alamat (Opsional)</label>
                                <input type="text" class="form-control" id="add_alamat" name="alamat_pbp" placeholder="Nama Jalan / Kp.">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-danger">Catatan Reject / Alasan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="add_catatan" name="catatan" rows="3" placeholder="Contoh: Foto kurang jelas, KTP tidak terbaca..." required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" id="btnSimpanReject">
                        <i class="fas fa-save mr-1"></i> Simpan ke Daftar Reject
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // ========================================================
    // 📸 FUNGSI GLOBAL: HARUS DI LUAR $(document).ready
    // ========================================================
    // 🚀 PERBAIKAN: Tambahkan parameter 'status' di akhir fungsi
    function lihatFoto(id, fotoKtp, fotoPbp, status) {
        let baseUrl = '<?= base_url() ?>';
        let urlKtp = baseUrl + '/' + fotoKtp;
        let urlPbp = baseUrl + '/' + fotoPbp;
        let roleId = <?= session()->get('role_id') ?>;

        let filenameKtp = fotoKtp.split('/').pop();
        let filenamePbp = fotoPbp.split('/').pop();

        let htmlContent = `
        <div class="mt-2">
            <div class="text-left mb-2">
                <span class="badge bg-info text-white"><i class="fas fa-id-card mr-1"></i> Foto KTP</span>
            </div>
            <div class="position-relative mb-4">
                <img src="${urlKtp}" class="img-fluid rounded border shadow-sm w-100" style="max-height: 350px; object-fit: contain; background: #2d2d2d;">
                <a href="${urlKtp}" download="${filenameKtp}" class="btn btn-sm btn-info position-absolute shadow" style="bottom: 10px; right: 10px; border-radius: 50%;">
                    <i class="fas fa-download"></i>
                </a>
            </div>

            <div class="text-left mb-2">
                <span class="badge bg-success text-white"><i class="fas fa-user-check mr-1"></i> Swafoto KPM</span>
            </div>
            <div class="position-relative mb-2">
                <img src="${urlPbp}" class="img-fluid rounded border shadow-sm w-100" style="max-height: 350px; object-fit: contain; background: #2d2d2d;">
                <a href="${urlPbp}" download="${filenamePbp}" class="btn btn-sm btn-success position-absolute shadow" style="bottom: 10px; right: 10px; border-radius: 50%;">
                    <i class="fas fa-download"></i>
                </a>
            </div>
        </div>
    `;

        // 🚀 PERBAIKAN 3: Kondisional Tombol Validasi Berdasarkan Status Data
        if (roleId === 3) {
            htmlContent += `<hr class="mt-4 mb-3">`;

            if (status === 1) {
                // Tampilkan tombol eksekusi hanya jika statusnya masih 1 (Menunggu Verifikasi)
                htmlContent += `
                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-user-shield mr-1"></i> Aksi Admin (Validasi)</h6>
                <div class="row">
                    <div class="col-6 pr-1">
                        <button class="btn btn-success btn-block font-weight-bold shadow-sm" onclick="aksiVerifikasi(${id}, 'verify')">
                            <i class="fas fa-check-circle mr-1"></i> Teruskan
                        </button>
                    </div>
                    <div class="col-6 pl-1">
                        <button class="btn btn-danger btn-block font-weight-bold shadow-sm" onclick="aksiVerifikasi(${id}, 'reject')">
                            <i class="fas fa-times-circle mr-1"></i> Tolak (Ulangi)
                        </button>
                    </div>
                </div>
            `;
            } else if (status === 2) {
                // Jika statusnya sudah 2 (Diverifikasi), kunci tombol dan tampilkan alert hijau penanda aman
                htmlContent += `
                <div class="alert alert-success p-2 text-center text-sm mb-0 font-weight-bold shadow-sm border-0">
                    <i class="fas fa-check-double mr-1"></i> Data Ini Telah Diverifikasi & Valid SINDEN
                </div>
            `;
            }
        }

        Swal.fire({
            title: '<h6 class="font-weight-bold text-primary mb-0">Hasil Dokumentasi</h6>',
            html: htmlContent,
            showCloseButton: true,
            showConfirmButton: false,
        });
    }

    // 🚀 FUNGSI AJAX EKSEKUSI VERIFIKASI ADMIN
    function aksiVerifikasi(id, aksi) {
        if (aksi === 'reject') {
            Swal.fire({
                title: 'Tolak Foto?',
                input: 'text',
                inputPlaceholder: 'Tuliskan alasan penolakan (misal: Foto KTP buram)',
                showCancelButton: true,
                confirmButtonText: 'Tolak & Minta Ulang',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) return 'Alasan harus diisi!'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    kirimAksiBackend(id, aksi, result.value);
                }
            });
        } else {
            Swal.fire({
                title: 'Verifikasi & Teruskan?',
                text: "KPM ini akan dianggap selesai dan valid.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Valid!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    kirimAksiBackend(id, aksi, '-');
                }
            });
        }
    }

    function kirimAksiBackend(id, aksi, alasan) {
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.post('<?= base_url('banpang/reject/aksiVerifikasi') ?>', {
            id: id,
            aksi: aksi,
            alasan: alasan,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }, function(res) {
            if (res.status === 'success') {
                Swal.fire('Berhasil!', res.message, 'success').then(() => {
                    $('#tableReject').DataTable().ajax.reload(null, false);
                });
            } else {
                Swal.fire('Gagal!', res.message, 'error');
            }
        }, 'json');
    }

    // 📋 FUNGSI COPY TO CLIPBOARD
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Gunakan Toast SweetAlert2 agar tidak menutupi layar
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: 'Berhasil disalin ke clipboard'
            });
        }, function(err) {
            console.error('Gagal menyalin: ', err);
        });
    }

    // ========================================================
    // ⚙️ INISIALISASI DATATABLES DLL
    // ========================================================
    $(document).ready(function() {
        // ⚙️ Inisialisasi DataTables (Server-Side)
        let tableReject = $('#tableReject').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '<?= base_url('banpang/reject/datatable') ?>',
                type: 'POST',
                data: function(d) {
                    d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                    d.filter_status = $('#filterStatus').val(); // 🚀 Kirim status terpilih
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    searchable: false,
                    className: 'text-center align-middle'
                },
                {
                    data: 'no_pbp',
                    className: 'align-middle font-weight-bold'
                },
                {
                    data: 'nik',
                    className: 'align-middle'
                },
                {
                    data: 'nama',
                    className: 'align-middle text-primary font-weight-bold'
                },
                {
                    data: 'wilayah',
                    className: 'align-middle text-sm'
                },
                {
                    data: 'status_badge',
                    className: 'text-center align-middle',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'aksi',
                    className: 'text-center align-middle',
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: "Cari NIK/Nama:",
                lengthMenu: "Tampil _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ KPM",
                paginate: {
                    previous: "<",
                    next: ">"
                }
            }
        });

        // 🚀 Trigger Reload Tabel otomatis saat opsi Filter diubah
        $('#filterStatus').change(function() {
            tableReject.ajax.reload();
        });

        // 📥 Eksekusi AJAX Import Excel
        $('#formImport').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            // SweetAlert2 Loading (Ukuran kecil untuk Mobile)
            Swal.fire({
                title: 'Mengimpor Data...',
                text: 'Mohon tunggu, jangan tutup halaman ini.',
                allowOutsideClick: false,
                customClass: {
                    popup: 'swal2-sm'
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('banpang/reject/importExcel') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        // 1. Tutup modal secara paksa (Trik bulletproof untuk Bootstrap 4 & 5)
                        $('#modalImport').modal('hide');
                        $('#modalImport').find('[data-dismiss="modal"], [data-bs-dismiss="modal"]').click();
                        $('.modal-backdrop').remove(); // Bersihkan sisa bayangan hitam jika ada

                        // 2. Reset isi file input
                        $('#formImport')[0].reset();

                        // 3. Refresh Datatable secara halus (tanpa mereset halaman paginasi)
                        tableReject.ajax.reload(null, false);

                        // 4. Baru munculkan notifikasi sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            customClass: {
                                popup: 'swal2-sm'
                            },
                            confirmButtonText: 'Oke Mantap'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message,
                            customClass: {
                                popup: 'swal2-sm'
                            }
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gangguan Jaringan',
                        text: 'Gagal terhubung ke server SINDEN.',
                        customClass: {
                            popup: 'swal2-sm'
                        }
                    });
                }
            });
        });

        // 1. Tampilkan Modal Saat Tombol Diklik
        $('#btnTambahReject').click(function() {
            $('#formTambahReject')[0].reset();
            $('#selectCariKpm').empty().trigger('change'); // Kosongkan pilihan
            $('#modalTambahReject').modal('show');
        });

        // 2. Inisialisasi Select2 untuk Pencarian AJAX
        if ($.fn.select2) {
            $('#selectCariKpm').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#modalTambahReject'),
                placeholder: 'Pilih / Ketik KPM...',
                ajax: {
                    url: '<?= base_url('banpang/searchKpmAjax') ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            // Otomatis isi kolom saat KPM dipilih
            $('#selectCariKpm').on('select2:select', function(e) {
                var data = e.params.data;
                $('#add_no_pbp').val(data.no_pbp);
                $('#add_no_bast').val(data.no_bast); // 🚀 Otomatis lempar data No. BAST
                $('#add_no_kk').val(data.no_kk);
                $('#add_nik').val(data.nik);
                $('#add_nama').val(data.nama);
            });
        }

        // 3. Aksi Simpan menggunakan AJAX + SweetAlert2
        $('#btnSimpanReject').click(function() {
            let no_pbp = $('#add_no_pbp').val();
            let catatan = $('#add_catatan').val();

            if (!no_pbp || !catatan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Belum Lengkap!',
                    text: 'Pastikan KPM sudah dipilih dan Catatan Reject wajib diisi.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Ubah tombol jadi loading
            let btn = $(this);
            let originalText = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...').prop('disabled', true);

            $.ajax({
                url: '<?= base_url('banpang/simpanRejectManual') ?>',
                type: 'POST',
                data: $('#formTambahReject').serialize(),
                dataType: 'json',
                success: function(res) {
                    btn.html(originalText).prop('disabled', false);
                    if (res.status === 'success') {
                        $('#modalTambahReject').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Reload DataTables (sesuaikan dengan nama variabel datatable Kang Rian)
                        $('#tableReject').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                },
                error: function() {
                    btn.html(originalText).prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Gagal menghubungi server.'
                    });
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>