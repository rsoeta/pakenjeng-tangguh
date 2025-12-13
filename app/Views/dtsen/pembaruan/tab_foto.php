<?php
$roleId = $user['role_id'] ?? 99;
$editable = ($roleId <= 4); // Petugas & Operator bisa edit
$disabled = $editable ? '' : 'disabled';

$foto = $payload['foto'] ?? [];
$geo  = $payload['geo'] ?? [];
?>

<div class="p-3">
    <h5 class="fw-bold mb-3">📸 Foto & Geotag Lokasi Rumah</h5>
    <!-- tampilkan session login -->
    <!-- <p>
        <?php foreach (session()->get() as $key => $value): ?>
            - <?= $key ?> : <?= $value ?><br>
        <?php endforeach; ?>

    </p> -->
    <form id="formFoto" enctype="multipart/form-data">
        <input type="hidden" name="dtsen_usulan_id" value="<?= esc($payload['id'] ?? $usulan['id'] ?? '') ?>">
        <input type="hidden" name="no_kk" value="<?= esc($payload['no_kk'] ?? $perumahan['no_kk'] ?? '') ?>">
        <input type="hidden" name="kepala_keluarga" value="<?= esc($payload['kepala_keluarga'] ?? $perumahan['kepala_keluarga'] ?? '') ?>">

        <div class="row">
            <!-- Foto KTP/KK -->
            <div class="col-md-4 mb-3 text-center">
                <label class="fw-semibold d-block">Foto KTP / KK</label>
                <img src="<?= base_url($foto['ktp_kk'] ?? 'data/usulan/foto_identitas/noimage.png') ?>"
                    class="img-fluid rounded border mb-2 img-download" id="previewKtp" style="max-height: 200px;">
                <?php if ($editable): ?>
                    <input type="file" name="foto_ktp" id="fotoKtp" class="form-control form-control-sm" accept="image/*" capture="environment">
                <?php endif; ?>
            </div>

            <!-- Foto Rumah Depan -->
            <div class="col-md-4 mb-3 text-center">
                <label class="fw-semibold d-block">Foto Rumah (Tampak Depan)</label>
                <img src="<?= base_url($foto['depan'] ?? 'data/usulan/foto_rumah/noimage.png') ?>"
                    class="img-fluid rounded border mb-2 img-download" id="previewDepan" style="max-height: 200px;">
                <?php if ($editable): ?>
                    <input type="file" name="foto_depan" id="fotoDepan" class="form-control form-control-sm" accept="image/*" capture="environment">
                <?php endif; ?>
            </div>

            <!-- Foto Rumah Dalam -->
            <div class="col-md-4 mb-3 text-center">
                <label class="fw-semibold d-block">Foto Rumah (Bagian Dalam)</label>
                <img src="<?= base_url($foto['dalam'] ?? 'data/usulan/foto_rumah_dalam/noimage.png') ?>"
                    class="img-fluid rounded border mb-2 img-download" id="previewDalam" style="max-height: 200px;">
                <?php if ($editable): ?>
                    <input type="file" name="foto_dalam" id="fotoDalam" class="form-control form-control-sm" accept="image/*" capture="environment">
                <?php endif; ?>
            </div>
        </div>

        <hr>

        <!-- 🌍 Koordinat Lokasi -->
        <h6 class="fw-bold mb-3 mt-3">🌍 Titik Lokasi Rumah (Geotag)</h6>

        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Latitude</label>
                <div class="input-group">
                    <input type="text" id="latitude" name="latitude" class="form-control"
                        value="<?= esc($geo['lat'] ?? '') ?>" readonly>
                    <button class="btn btn-outline-secondary" type="button" id="btnCopyLat" title="Salin Latitude">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Longitude</label>
                <div class="input-group">
                    <input type="text" id="longitude" name="longitude" class="form-control"
                        value="<?= esc($geo['lng'] ?? '') ?>" readonly>
                    <button class="btn btn-outline-secondary" type="button" id="btnCopyLng" title="Salin Longitude">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-3 mb-3 d-flex gap-2">
            <button type="button" class="btn btn-sm btn-primary" id="btnGetLocation">
                <i class="fas fa-map-marker-alt"></i> Ambil Lokasi Saat Ini
            </button>

            <button type="button" class="btn btn-sm btn-info" id="btnCopyFull" title="Salin Latitude & Longitude">
                <i class="fas fa-copy"></i> Salin Koordinat
            </button>
        </div>


        <div class="mt-3">
            <div id="map" style="height: 300px; border-radius: 10px;"></div>
        </div>

        <?php if ($editable): ?>
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- ============================== -->
<!-- 📡 JS SAVE FOTO DAN GEOTAG -->
<!-- ============================== -->
<script>
    $('#formFoto').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url("pembaruan-keluarga/save-foto") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Tidak dapat mengirim data ke server.', 'error');
            }
        });
    });
</script>

<!-- ================================================== -->
<!-- 🧩 SCRIPTS: PREVIEW FOTO, COPY KOORDINAT & PETA MAP -->
<!-- ================================================== -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // download gambar saat di klik
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('img-download')) {
            const img = e.target;
            const url = img.src;

            // ambil nama file dari url
            const filename = url.split('/').pop();

            const a = document.createElement('a');
            a.href = url;
            a.download = filename || 'gambar.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    });

    // 📸 preview gambar otomatis saat upload
    function previewImage(input, targetId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById(targetId).src = e.target.result;
            reader.readAsDataURL(file);
        }
    }

    ['Ktp', 'Depan', 'Dalam'].forEach(suffix => {
        const input = document.getElementById('foto' + suffix);
        if (input) input.addEventListener('change', e => previewImage(e.target, 'preview' + suffix));
    });

    // =============================
    // 🌍 INISIALISASI PETA LEAFLET
    // =============================
    document.addEventListener("DOMContentLoaded", function() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        // Jika payload kosong → map tetap tampil tapi input tidak diisi
        let lat = latInput.value ? parseFloat(latInput.value) : -6.895;
        let lng = lngInput.value ? parseFloat(lngInput.value) : 107.634;

        const map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marker hanya muncul jika ada koordinat
        let marker = null;
        if (latInput.value && lngInput.value) {
            marker = L.marker([lat, lng], {
                draggable: <?= $editable ? 'true' : 'false' ?>
            }).addTo(map);
        }

        // Update koordinat kalau marker digeser
        if (marker && <?= $editable ? 'true' : 'false' ?>) {
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                latInput.value = pos.lat.toFixed(6);
                lngInput.value = pos.lng.toFixed(6);
            });
        }

        // =============================
        // 📍 GET KOORDINAT MANUAL
        // =============================
        document.getElementById('btnGetLocation').addEventListener('click', function() {
            if (!navigator.geolocation) {
                return Swal.fire("Error", "Browser tidak mendukung GPS.", "error");
            }

            Swal.fire({
                icon: "info",
                title: "Mencari lokasi...",
                text: "Mohon tunggu sebentar",
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    Swal.close();

                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    // isi input
                    latInput.value = userLat.toFixed(6);
                    lngInput.value = userLng.toFixed(6);

                    // buat marker kalau belum ada
                    if (!marker) {
                        marker = L.marker([userLat, userLng], {
                            draggable: true
                        }).addTo(map);

                        marker.on('dragend', function(e) {
                            const pos = e.target.getLatLng();
                            latInput.value = pos.lat.toFixed(6);
                            lngInput.value = pos.lng.toFixed(6);
                        });
                    } else {
                        marker.setLatLng([userLat, userLng]);
                    }

                    map.setView([userLat, userLng], 17);

                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'success',
                        title: 'Lokasi berhasil diambil!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                function(error) {
                    Swal.close();
                    Swal.fire("Gagal", "Tidak dapat mengambil lokasi. Aktifkan GPS Anda.", "warning");
                }, {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 0
                }
            );
        });
    });

    // =============================
    // 🧭 Tombol copy koordinat
    document.getElementById('btnCopyLat').addEventListener('click', () => {
        const val = document.getElementById('latitude').value;
        navigator.clipboard.writeText(val);
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'success',
            title: 'Latitude disalin',
            showConfirmButton: false,
            timer: 1500
        });
    });

    document.getElementById('btnCopyLng').addEventListener('click', () => {
        const val = document.getElementById('longitude').value;
        navigator.clipboard.writeText(val);
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: 'success',
            title: 'Longitude disalin',
            showConfirmButton: false,
            timer: 1500
        });
    });

    // Copy LAT + LNG lengkap
    document.getElementById("btnCopyFull").addEventListener("click", function() {
        const lat = document.getElementById("latitude").value;
        const lng = document.getElementById("longitude").value;

        if (!lat || !lng) {
            Swal.fire("Gagal", "Latitude & Longitude belum lengkap!", "warning");
            return;
        }

        const full = `${lat}, ${lng}`;
        navigator.clipboard.writeText(full);

        Swal.fire({
            icon: "success",
            title: "Koordinat Disalin!",
            text: full,
            timer: 1800,
            showConfirmButton: false
        });
    });

    /* ============================================================
     * 📸 IMAGE UPLOAD + COMPRESSION HANDLER (FINAL)
     * ============================================================ */
    const imageInputs = [{
            input: 'fotoKtp',
            preview: 'previewKtp'
        },
        {
            input: 'fotoDepan',
            preview: 'previewDepan'
        },
        {
            input: 'fotoDalam',
            preview: 'previewDalam'
        }
    ];

    imageInputs.forEach(item => {
        const fileInput = document.getElementById(item.input);
        if (!fileInput) return;

        fileInput.addEventListener('change', async function() {
            const file = this.files[0];
            if (!file) return;

            // validasi file gambar
            if (!file.type.startsWith('image/')) {
                Swal.fire(
                    'File tidak valid',
                    'File harus berupa gambar (JPG / PNG).',
                    'warning'
                );
                this.value = '';
                return;
            }

            try {
                // 🔹 proses kompresi
                const compressedResult = await compressWithLibrary(file);

                // 🔒 PAKSA hasil selalu File (atasi Blob vs File)
                const compressedFile = compressedResult instanceof File ?
                    compressedResult :
                    new File([compressedResult], file.name, {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                // 🔹 preview
                const previewEl = document.getElementById(item.preview);
                if (previewEl) {
                    previewEl.src = URL.createObjectURL(compressedFile);
                }

                // 🔹 replace input file
                const dt = new DataTransfer();
                dt.items.add(compressedFile);
                fileInput.files = dt.files;

                console.log(
                    `[${item.input}] before: ${(file.size / 1024).toFixed(1)} KB | after: ${(compressedFile.size / 1024).toFixed(1)} KB`
                );

            } catch (err) {
                console.error('❌ Gagal kompres gambar:', err);

                Swal.fire(
                    'Gagal Kompres Gambar',
                    err.message || 'Terjadi kesalahan saat memproses gambar.',
                    'error'
                );

                // reset input jika gagal
                this.value = '';
            }
        });
    });

    /* ============================================================
     * 🗜️ IMAGE COMPRESSION HELPER (browser-image-compression)
     * ============================================================ */
    async function compressWithLibrary(file) {
        if (typeof imageCompression !== 'function') {
            throw new Error('Library imageCompression belum dimuat');
        }

        const options = {
            maxSizeMB: 0.5, // target ±500 KB
            maxWidthOrHeight: 1280, // aman untuk foto HP
            useWebWorker: true,
            fileType: 'image/jpeg',
            initialQuality: 0.8
        };

        return await imageCompression(file, options);
    }
</script>

<style>
    #map {
        border: 2px solid #ddd;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
</style>