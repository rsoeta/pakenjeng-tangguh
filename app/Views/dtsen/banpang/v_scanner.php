<?= $this->extend('templates/index') ?>

<?= $this->section('content') ?>
<div class="container-fluid pt-3">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card card-primary card-outline shadow">
                <div class="card-header text-center bg-primary text-white p-2">
                    <h5 class="card-title m-0 font-weight-bold"><i class="fas fa-qrcode mr-2"></i> Scanner Banpang</h5>
                </div>
                <div class="card-body p-3 text-center">

                    <div class="mb-3 p-2 border rounded bg-light">
                        <label for="qr-input-file" class="small font-weight-bold text-dark mb-1"><i class="fas fa-image text-success"></i> Sulit Scan? Pilih Gambar QR dari Galeri:</label>
                        <input type="file" id="qr-input-file" accept="image/*" class="form-control form-control-sm">
                    </div>

                    <hr>

                    <div id="reader" class="shadow-sm" style="width: 100%; border-radius: 10px; overflow: hidden; background: #000;"></div>

                    <div class="mt-2">
                        <h6 id="last_scan_result" class="font-weight-bold text-success mt-2">-- Menunggu Scan / Upload --</h6>
                    </div>

                    <div class="mt-4 text-left">
                        <h6 class="font-weight-bold text-secondary border-bottom pb-1 mb-2" style="font-size:0.9rem;"><i class="fas fa-history mr-1"></i> 5 Riwayat Scan Terakhir</h6>
                        <ul class="list-group list-group-flush shadow-sm" id="list_latest_scans" style="border-radius: 5px; overflow:hidden;">
                            <li class="list-group-item text-center text-muted small py-2 bg-light">Memuat riwayat data...</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<audio id="audio_beep" src="<?= base_url('assets/sounds/beep.mp3') ?>" preload="auto"></audio>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            width: '300px',
            customClass: {
                title: 'small'
            }
        });

        // ==========================================
        // 🔄 FUNGSI LOAD 5 DATA TERAKHIR
        // ==========================================
        function loadRiwayatScan() {
            $.ajax({
                url: "<?= base_url('banpang/getLatestScans') ?>",
                type: "GET",
                dataType: "JSON",
                success: function(res) {
                    if (res.status === 'success') {
                        let html = '';
                        if (res.data.length > 0) {
                            res.data.forEach(function(item) {
                                html += `<li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2 small">
                                            <span class="font-weight-bold text-dark text-truncate" style="max-width: 70%;"><i class="fas fa-check-circle text-success mr-1"></i> ${item.nama_kpm}</span>
                                            <span class="badge badge-light text-muted border"><i class="far fa-clock"></i> ${item.waktu}</span>
                                         </li>`;
                            });
                        } else {
                            html = '<li class="list-group-item text-center text-muted small py-2 bg-light">Belum ada data terekap.</li>';
                        }
                        $('#list_latest_scans').html(html);
                    }
                }
            });
        }

        // Panggil fungsi riwayat saat halaman pertama dibuka
        loadRiwayatScan();

        // Konfigurasi Kamera (Dengan Steroid)
        const html5QrCode = new Html5Qrcode("reader");
        const config = {
            fps: 15,
            qrbox: function(vw, vh) {
                return {
                    width: Math.floor(Math.min(vw, vh) * 0.7),
                    height: Math.floor(Math.min(vw, vh) * 0.7)
                };
            },
            formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            }
        };

        function startKamera() {
            html5QrCode.start({
                    facingMode: "environment"
                }, config,
                function(decodedText, decodedResult) {
                    html5QrCode.pause();
                    prosesDataQR(decodedText, 'kamera');
                }
            ).catch((err) => {
                console.log("Kamera standby / tidak tersedia.");
            });
        }

        function prosesDataQR(decodedText, mode) {
            try {
                let dataQR = JSON.parse(decodedText);

                if (dataQR.no_pbp && dataQR.nik) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Menyimpan data...',
                        timer: false
                    });

                    $.ajax({
                        url: "<?= base_url('banpang/simpanScan') ?>",
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            no_pbp: dataQR.no_pbp,
                            no_bast: dataQR.no_bast,
                            nik: dataQR.nik,
                            nama: dataQR.nama,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                document.getElementById('audio_beep').play().catch(e => console.log('Audio error'));
                                Toast.fire({
                                    icon: 'success',
                                    title: res.nama + '<br>Berhasil disave!'
                                });
                                $('#last_scan_result').html('<i class="fas fa-check-circle"></i> ' + res.nama);

                                // 🚀 REFRESH RIWAYAT SCAN OTOMATIS SAAT BERHASIL
                                loadRiwayatScan();

                            } else if (res.status === 'warning') {
                                Toast.fire({
                                    icon: 'warning',
                                    title: res.nama + '<br>' + res.message
                                });
                                $('#last_scan_result').html('<i class="fas fa-exclamation-triangle text-warning"></i> ' + res.nama + ' (Duplikat)');
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Gagal diproses!'
                                });
                            }
                        },
                        error: function() {
                            Toast.fire({
                                icon: 'error',
                                title: 'Gangguan Jaringan/Server!'
                            });
                        },
                        complete: function() {
                            setTimeout(() => {
                                if (mode === 'kamera') html5QrCode.resume();
                                else startKamera();
                            }, 2000);
                        }
                    });

                } else {
                    Toast.fire({
                        icon: 'error',
                        title: 'Bukan QR Banpang Resmi!'
                    });
                    setTimeout(() => {
                        if (mode === 'kamera') html5QrCode.resume();
                        else startKamera();
                    }, 2000);
                }

            } catch (e) {
                Toast.fire({
                    icon: 'error',
                    title: 'Format QR tidak valid / Bukan JSON!'
                });
                setTimeout(() => {
                    if (mode === 'kamera') html5QrCode.resume();
                    else startKamera();
                }, 2000);
            }
        }

        const fileinput = document.getElementById('qr-input-file');
        fileinput.addEventListener('change', e => {
            if (e.target.files.length == 0) return;
            const imageFile = e.target.files[0];
            Toast.fire({
                icon: 'info',
                title: 'Menganalisa gambar...',
                timer: false
            });

            function jalankanScanFile(file) {
                html5QrCode.scanFile(file, true)
                    .then(decodedText => {
                        fileinput.value = '';
                        prosesDataQR(decodedText, 'file');
                    }).catch(err => {
                        fileinput.value = '';
                        Toast.fire({
                            icon: 'error',
                            title: 'Gagal membaca QR dari gambar!'
                        });
                        setTimeout(() => {
                            startKamera();
                        }, 2000);
                    });
            }

            try {
                let state = html5QrCode.getState();
                if (state === Html5QrcodeScannerState.SCANNING || state === Html5QrcodeScannerState.PAUSED) {
                    html5QrCode.stop().then(() => {
                            html5QrCode.clear();
                            jalankanScanFile(imageFile);
                        })
                        .catch(err => {
                            jalankanScanFile(imageFile);
                        });
                } else {
                    jalankanScanFile(imageFile);
                }
            } catch (err) {
                jalankanScanFile(imageFile);
            }
        });

        startKamera();
    });
</script>


<?= $this->endSection() ?>