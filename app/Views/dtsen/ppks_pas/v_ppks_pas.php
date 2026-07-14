<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-1">
    <div class="content-header">
        <div class="container-fluid">
            <h4 class="m-0 fw-bold"><i class="fas fa-hands-helping text-primary mr-2"></i> <?= $title; ?></h4>
            <p class="text-muted">Pendataan PPKS 5 PAS untuk diunggah ke GForm Pemkab Garut.</p>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="alert alert-warning shadow-sm border-warning text-center mb-4" id="countdown-container" style="display: none;">
                <h6 class="m-0 fw-bold text-dark">
                    <i class="fas fa-stopwatch fa-spin text-danger mr-2"></i> BATAS WAKTU PENDATAAN PPKS:
                    <span id="countdown-timer" class="text-danger font-monospace fs-5 ml-2">Memuat Waktu...</span>
                </h6>
            </div>
            <!-- Form Input (Tampil untuk semua role) -->
            <div class="card shadow-sm mb-4 border-top-primary">
                <div class="card-body p-3">
                    <form id="formPPKS">
                        <?= csrf_field(); ?>
                        <!-- g-2 untuk jarak antar kolom yang pas di mobile -->
                        <div class="row align-items-end g-2">

                            <!-- Kiri: col-6 memakan 50% layar HP -->
                            <div class="col-6 col-md-5 mb-2">
                                <label class="small fw-bold text-truncate d-block">Cari NIK / Nama <span class="text-danger">*</span></label>
                                <select class="form-control" id="nik_search" name="nik" style="width: 100%;" required></select>
                            </div>

                            <!-- Kanan: col-6 memakan 50% layar HP -->
                            <div class="col-6 col-md-5 mb-2">
                                <label class="small fw-bold text-truncate d-block">Jenis PPKS <span class="text-danger">*</span></label>
                                <select name="jenis_ppks" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <?php foreach ($kategori_5 as $kat) : ?>
                                        <option value="<?= $kat['id'] ?>|<?= $kat['nama_gform'] ?>">
                                            <?= $kat['nama_gform'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Bawah: Tombol memanjang penuh di HP (col-12), tapi sejajar di PC (col-md-2) -->
                            <div class="col-12 col-md-2 mb-2">
                                <button type="submit" id="btnSimpan" class="btn btn-primary w-100 shadow-sm">
                                    <i class="fas fa-save mr-1"></i> Usulkan
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="card shadow-sm border-top-success">
                <!-- 🚀 HEADER TABEL & TOMBOL EXPORT STANDAR ADMINLTE -->
                <div class="card-header bg-white">
                    <h3 class="card-title fw-bold mt-1" style="font-size: 1rem;">
                        <i class="fas fa-list text-success mr-1"></i> Data Usulan PPKS
                    </h3>
                    <div class="card-tools">
                        <!-- Tambahkan text-white agar ikon dan teks mutlak berwarna putih -->
                        <a href="<?= base_url('ppks-pas/export-excel') ?>" class="btn btn-sm btn-success shadow-sm rounded-pill px-3 text-white">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel
                        </a>
                    </div>
                </div>

                <div class="card-body p-3">
                    <table class="table table-hover align-middle w-100" id="tablePPKS">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Identitas & Kategori</th>
                                <th>Alamat</th>
                                <th>Status GForm</th>
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

<!-- Textarea rahasia untuk alat bantu Copy ke Clipboard -->
<textarea id="clipboard-helper" style="position: absolute; left: -9999px;"></textarea>

<script>
    $(document).ready(function() {

        // Inisialisasi DataTable
        var table = $('#tablePPKS').DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            "ajax": {
                "url": "<?= base_url('ppks-pas/datatable') ?>",
                "type": "POST",
                "data": function(d) {
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                }
            },
            "columnDefs": [{
                "className": "text-center",
                "targets": [0, 3, 4]
            }]
        });

        // ========================================================
        // ⏳ LOGIKA COUNTDOWN TIMER & SMART LOCK
        // ========================================================
        function initCountdown() {
            $.ajax({
                url: '<?= base_url('ppks-pas/check-deadline') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    // 🚀 PASTIKAN DATA ADA
                    if (res.status === 'success' && res.deadline != null) {

                        // 🚀 AMBIL SPESIFIK KOLOM dd_waktu_end
                        var deadlineStr = res.deadline.dd_waktu_end;
                        if (!deadlineStr) return; // Hentikan jika kosong

                        // 🚀 PELINDUNG BROWSER HP (Ubah - jadi /)
                        var safeDateStr = deadlineStr.replace(/-/g, '/');

                        $('#countdown-container').fadeIn();
                        var countDownDate = new Date(safeDateStr).getTime();

                        var x = setInterval(function() {
                            var now = new Date().getTime();
                            var distance = countDownDate - now;

                            // 🔴 JIKA WAKTU HABIS (PORTAL DITUTUP)
                            if (distance < 0) {
                                clearInterval(x);
                                $('#countdown-timer').html("WAKTU HABIS! PORTAL DITUTUP.");
                                $('#countdown-container').removeClass('alert-warning border-warning').addClass('alert-danger border-danger');

                                // 🔒 Kunci Form
                                $('#nik_search').prop('disabled', true);
                                $('select[name="jenis_ppks"]').prop('disabled', true);
                                $('#btnSimpan').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary').html('<i class="fas fa-lock mr-1"></i> Waktu Habis');

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Waktu Habis!',
                                    text: 'Batas waktu pendataan PPKS telah berakhir.',
                                    confirmButtonText: 'Mengerti'
                                });
                            }
                            // 🟢 JIKA WAKTU MASIH ADA
                            else {
                                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                hours = (hours < 10) ? "0" + hours : hours;
                                minutes = (minutes < 10) ? "0" + minutes : minutes;
                                seconds = (seconds < 10) ? "0" + seconds : seconds;

                                $('#countdown-timer').text(days + " Hari " + hours + ":" + minutes + ":" + seconds);
                            }
                        }, 1000);
                    }
                }
            });
        }

        initCountdown();

        // Inisialisasi Select2
        $('#nik_search').select2({
            ajax: {
                url: '<?= base_url('ppks-pas/search-nik') ?>',
                type: 'GET',
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
            },
            placeholder: 'Ketik NIK atau Nama...'
        });

        // Simpan Data
        $('#formPPKS').submit(function(e) {
            e.preventDefault();

            if ($('#nik_search').val() == null) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih NIK warga terlebih dahulu!',
                    customClass: {
                        popup: 'swal-sm'
                    }
                });
                return;
            }

            $.ajax({
                url: '<?= base_url('ppks-pas/simpan') ?>',
                type: 'POST',
                data: $(this).serialize(),
                beforeSend: function() {
                    $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'swal-sm'
                            }
                        });
                        $('#formPPKS')[0].reset();
                        $('#nik_search').val(null).trigger('change');
                        table.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message,
                            customClass: {
                                popup: 'swal-sm'
                            }
                        });
                    }
                    $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Usulkan');
                }
            });
        });

        // 🚀 FITUR SAKTI KANG RIAN: MODAL COPY PRESISI GOOGLE FORM
        $('#tablePPKS').on('click', '.btn-copy', function() {
            var rawData = $(this).data('clipboard');
            var idUsulan = rawData.id; // Tangkap ID

            // Meracik UI Modal dengan Tombol Selesai di Bawah Tabel
            var htmlContent = `
            <div class="text-left small">
                <div class="alert alert-info py-1 mb-2"><i class="fas fa-info-circle"></i> Klik tombol biru untuk menyalin data satu per satu.</div>
                <table class="table table-sm table-bordered align-middle mb-3">
                    <tbody>
                        <tr><td width="35%"><b>Email</b></td><td>riansoetarsa@gmail.com</td><td width="15%" class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="riansoetarsa@gmail.com"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>Nama PPKS</b></td><td>${rawData.nama}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="${rawData.nama}"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>No. KK</b></td><td>'${rawData.no_kk}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="'${rawData.no_kk}"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>N I K</b></td><td>'${rawData.nik}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="'${rawData.nik}"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>Jenis Kelamin</b></td><td>${rawData.jk}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="${rawData.jk}"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>Tempat Lahir</b></td><td>${rawData.tempat_lahir}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="${rawData.tempat_lahir}"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>Tanggal Lahir</b></td><td>${rawData.tgl_lahir}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="${rawData.tgl_lahir}"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>Jenis PPKS</b></td><td>${rawData.jenis_ppks}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="${rawData.jenis_ppks}"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>Alamat</b></td><td>${rawData.alamat}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="${rawData.alamat}"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>Desa</b></td><td>${rawData.desa}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="${rawData.desa}"><i class="fas fa-copy"></i></button></td></tr>
                        <tr><td><b>Kecamatan</b></td><td>${rawData.kecamatan}</td><td class="text-center"><button class="btn btn-xs btn-primary copy-item" data-val="${rawData.kecamatan}"><i class="fas fa-copy"></i></button></td></tr>
                    </tbody>
                </table>
                
                <button class="btn btn-success w-100 btn-done-modal shadow-sm font-weight-bold py-2" data-id="${idUsulan}">
                    <i class="fas fa-check-double mr-1"></i> Data Selesai Diinput ke GForm
                </button>
            </div>
        `;

            Swal.fire({
                title: 'Salin Data GForm',
                html: htmlContent,
                showConfirmButton: false,
                showCloseButton: true,
                width: '500px'
            });
        });

        // Aksi saat tombol biru "Copy" ditekan
        $(document).on('click', '.copy-item', function() {
            var valToCopy = $(this).data('val');

            var $temp = $("#clipboard-helper");
            $temp.val(valToCopy).select();
            document.execCommand("copy");

            var btn = $(this);
            var originalHtml = btn.html();
            btn.removeClass('btn-primary').addClass('btn-success').html('<i class="fas fa-check"></i>');

            setTimeout(function() {
                btn.removeClass('btn-success').addClass('btn-primary').html(originalHtml);
            }, 1000);
        });

        // 🚀 Aksi saat tombol "Tandai Selesai" DI DALAM MODAL ditekan
        $(document).on('click', '.btn-done-modal', function() {
            var id = $(this).data('id');
            var btn = $(this);

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

            $.ajax({
                url: '<?= base_url('ppks-pas/tandai-selesai') ?>',
                type: 'POST',
                data: {
                    id: id,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(res) {
                    if (res.status == 'success') {
                        Swal.close(); // Tutup modal salin

                        Swal.fire({
                            icon: 'success',
                            title: 'Selesai!',
                            text: 'Data berhasil ditandai selesai.',
                            toast: true,
                            position: 'top-end',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $('#tablePPKS').DataTable().ajax.reload(null, false);
                    }
                }
            });
        });

        // Hapus Usulan (Bagi Pentri yang salah klik)
        $('#tablePPKS').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Batalkan Usulan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                customClass: {
                    popup: 'swal-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('ppks-pas/hapus') ?>',
                        type: 'POST',
                        data: {
                            id: id,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        success: function(res) {
                            if (res.status == 'success') table.ajax.reload(null, false);
                        }
                    });
                }
            });
        });

    });
</script>

<?= $this->endSection(); ?>