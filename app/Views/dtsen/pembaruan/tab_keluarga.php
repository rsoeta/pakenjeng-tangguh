<?php
$roleId = session()->get('role_id') ?? ($user['role_id'] ?? 99);
$editable = ($roleId <= 4);
$disabled = $editable ? '' : 'disabled';
$readonly = $editable ? '' : 'readonly';

$geo = $payload['geo'] ?? [];
$wil = $perumahan['wilayah'] ?? []; // 🚀 Penampung data wilayah domisili
?>

<style>
    .cursor-pointer {
        cursor: pointer;
    }

    .btn-check:checked+.card {
        background-color: #0d6efd10;
        border-width: 2px;
    }

    #map {
        border: 2px solid #ddd;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
</style>

<!-- 🌍 PANGGIL LEAFLET CSS & SELECT2 CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="p-3">

    <!-- ============================= -->
    <!-- 🏠 CARD DATA KELUARGA & BPS -->
    <!-- ============================= -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <h6 class="fw-bold mb-3 border-bottom pb-2">Identitas Keluarga</h6>

            <form id="formDataKeluarga" class="needs-validation" novalidate>
                <input type="hidden" id="id_kk" name="id_kk" value="<?= $id_kk ?>">
                <input type="hidden" id="sumber" name="sumber" value="<?= $sumber ?>">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="keluarga_no_kk" class="form-label fw-semibold">Nomor KK <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control onlynum16" id="keluarga_no_kk" name="no_kk" value="<?= esc($perumahan['no_kk'] ?? '') ?>" <?= $disabled ?> maxlength="16" minlength="16" required>
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#keluarga_no_kk" title="Salin Nomor KK"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="kepala_keluarga" class="form-label fw-semibold">Kepala Keluarga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control upper" id="kepala_keluarga" name="kepala_keluarga" value="<?= esc($perumahan['kepala_keluarga'] ?? '') ?>" <?= $disabled ?> required>
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#kepala_keluarga" title="Salin Kepala Keluarga"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary">NIK Kepala Keluarga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control onlynum16 border-primary" id="nik_kepala_keluarga" name="nik_kepala_keluarga" value="<?= esc($perumahan['nik_kepala_keluarga'] ?? '') ?>" <?= $disabled ?> maxlength="16" minlength="16" placeholder="Ketik NIK 16 digit..." required>
                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#nik_kepala_keluarga" title="Salin NIK"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary">Jumlah Anggota Keluarga</label>
                        <div class="input-group">
                            <input type="number" class="form-control border-primary bg-light" name="jumlah_anggota" id="auto_jumlah_anggota" value="<?= esc($perumahan['jumlah_anggota'] ?? '') ?>" readonly placeholder="Otomatis">
                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#auto_jumlah_anggota" title="Salin Jumlah Anggota"><i class="fas fa-copy"></i></button>
                        </div>
                        <small class="text-muted fst-italic" style="font-size: 0.7rem;">*Terisi otomatis dari Tab Anggota</small>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 🚀 PINDAHAN DARI TAB RUMAH: WILAYAH DOMISILI -->
                <!-- ========================================== -->
                <div class="card shadow-sm mb-4 mt-4 border-primary border-opacity-25">
                    <div class="card-header bg-light"><strong>Wilayah Domisili & Alamat</strong></div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label text-primary">Provinsi</label>
                                <select id="rumah_provinsi" name="provinsi" class="form-select border-primary required" <?= $disabled ?>>
                                    <option value="">[Pilih Provinsi]</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-primary">Kabupaten / Kota</label>
                                <select id="rumah_regency" name="regency" class="form-select border-primary required" <?= $disabled ?>>
                                    <option value="">[Pilih Kabupaten]</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-primary">Kecamatan</label>
                                <select id="rumah_district" name="district" class="form-select border-primary required" <?= $disabled ?>>
                                    <option value="">[Pilih Kecamatan]</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-primary">Desa / Kelurahan</label>
                                <select id="rumah_village" name="village" class="form-select border-primary required" <?= $disabled ?>>
                                    <option value="">[Pilih Desa]</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label text-primary">Alamat Lengkap</label>
                                <input name="alamat" class="form-control upper border-primary" <?= $readonly ?> value="<?= esc($perumahan['alamat'] ?? '') ?>" placeholder="Masukkan alamat lengkap sesuai KTP">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-primary">RT</label>
                                <input type="text" name="rt" class="form-control border-primary" value="<?= esc($perumahan['rt'] ?? '') ?>" <?= $readonly ?>>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-primary">RW</label>
                                <input type="text" name="rw" class="form-control border-primary" value="<?= esc($perumahan['rw'] ?? '') ?>" <?= $readonly ?>>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🚀 RINCIAN ALAMAT JALAN & NOMOR RUMAH -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary">Nama Jalan / Gang</label>
                        <div class="input-group">
                            <input type="text" class="form-control upper border-primary" id="nama_jalan" name="nama_jalan" value="<?= esc($perumahan['nama_jalan'] ?? '') ?>" <?= $disabled ?> placeholder="Contoh: JL. BUNGBULANG" required>
                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#nama_jalan" title="Salin Nama Jalan"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-primary">Nomor Rumah</label>
                        <div class="input-group">
                            <input type="text" class="form-control upper border-primary" id="nomor_rumah" name="nomor_rumah" value="<?= esc($perumahan['nomor_rumah'] ?? '') ?>" <?= $disabled ?> placeholder="Contoh: 12A" required>
                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#nomor_rumah" title="Salin Nomor Rumah"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary">Dusun / SLS</label>
                        <div class="input-group">
                            <input type="text" class="form-control upper border-primary" id="dusun" name="dusun" value="<?= esc($perumahan['dusun'] ?? '') ?>" <?= $disabled ?> placeholder="Contoh: NAGRAK" required>
                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#dusun" title="Salin Dusun"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary">Kode Pos</label>
                        <div class="input-group">
                            <input type="text" class="form-control onlynum border-primary" id="kode_pos" name="kode_pos" value="<?= esc($perumahan['kode_pos'] ?? '') ?>" <?= $disabled ?> maxlength="5" placeholder="5 Digit Kode Pos" required>
                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#kode_pos" title="Salin Kode Pos"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>

                    <!-- Ini select "Sesuai dgn KK?" biarkan seperti semula jika ada di file Jenderal -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-primary">Sesuai dgn KK?</label>
                        <select name="is_alamat_sesuai_kk" class="form-select border-primary" <?= $disabled ?>>
                            <option value="Ya" <?= ($perumahan['is_alamat_sesuai_kk'] ?? 'Ya') == 'Ya' ? 'selected' : '' ?>>Ya, Sesuai</option>
                            <option value="Tidak" <?= ($perumahan['is_alamat_sesuai_kk'] ?? '') == 'Tidak' ? 'selected' : '' ?>>Tidak Sesuai</option>
                        </select>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- 🌍 PINDAHAN GEOTAG DARI TAB FOTO -->
                <!-- ============================================== -->
                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2">🌍 Titik Lokasi Rumah (Geotag)</h6>
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Latitude</label>
                        <div class="input-group">
                            <input type="text" id="latitude" name="latitude" class="form-control" value="<?= esc($geo['lat'] ?? '') ?>" readonly required>
                            <button class="btn btn-outline-secondary" type="button" id="btnCopyLat" title="Salin Latitude"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Longitude</label>
                        <div class="input-group">
                            <input type="text" id="longitude" name="longitude" class="form-control" value="<?= esc($geo['lng'] ?? '') ?>" readonly required>
                            <button class="btn btn-outline-secondary" type="button" id="btnCopyLng" title="Salin Longitude"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                </div>
                <div class="mb-3 d-flex gap-2">
                    <?php if ($editable): ?>
                        <button type="button" class="btn btn-sm btn-primary shadow-sm" id="btnGetLocation">
                            <i class="fas fa-map-marker-alt"></i> Ambil Lokasi Saat Ini
                        </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-info shadow-sm" id="btnCopyFull" title="Salin Latitude & Longitude">
                        <i class="fas fa-copy"></i> Salin Koordinat
                    </button>
                </div>
                <div class="mb-4">
                    <div id="map" style="height: 300px; border-radius: 10px;"></div>
                </div>

                <!-- TOMBOL SUBMIT KELUARGA -->
                <?php if ($editable): ?>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan Data & Lokasi
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning small mt-3">
                        <i class="fas fa-lock"></i> Anda tidak memiliki hak untuk mengubah data ini.
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- ============================= -->
    <!-- 📊 MODAL UTAMA: GRAFIK DESIL -->
    <!-- ============================= -->
    <div class="modal fade" id="modalChartDesil" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- Pakai modal-xl agar grafiknya luas & mantap -->
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-chart-line me-2"></i> Grafik Riwayat Desil Keluarga</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-end align-items-center gap-2 mb-3 pb-3 border-bottom">
                        <?php if ($editable && ($user['role_id'] ?? 99) <= 3): ?>
                            <button class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalHistoricalDesil"><i class="fas fa-history me-1"></i> Tambah Snapshot</button>
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalInputDesil" data-id="<?= $id_kk ?>" data-nokk="<?= esc($kkData['no_kk'] ?? '') ?>" data-nama="<?= esc($kkData['kepala_keluarga'] ?? '') ?>" data-alamat="<?= esc($kkData['alamat'] ?? '') ?>" data-desil="<?= esc($kategori_desil ?? '') ?>"><i class="fas fa-hand-holding-heart me-1"></i> Update Desil</button>
                            <button type="button" id="btnSyncDesil" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold"><i class="fas fa-sync-alt me-1"></i> Sync</button>
                        <?php endif; ?>
                    </div>
                    <!-- Wadah Grafik -->
                    <div id="desilChart" style="min-height:350px;"></div>
                    <div id="desilTrendInfo" class="mt-3 small text-muted"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistoricalDesil" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Snapshot Historis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formHistoricalDesil">

                    <input type="hidden" name="id_kk" value="<?= $id_kk ?>">

                    <!-- Tahun -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <select name="tahun" class="form-select" required>
                            <?php
                            $startYear = 2025; // bisa kamu sesuaikan
                            $currentYear = date('Y');
                            for ($year = $currentYear; $year >= $startYear; $year--):
                            ?>
                                <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>>
                                    <?= $year ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Triwulan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Periode Triwulan</label>

                        <div class="row g-2 text-center">

                            <!-- TW1 -->
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="triwulan" id="tw1" value="1" required>

                                <label class="card border-primary h-100 cursor-pointer p-2"
                                    for="tw1">

                                    <div class="fw-bold text-primary">TW 1</div>
                                    <small class="text-muted">Jan – Mar</small>

                                </label>
                            </div>

                            <!-- TW2 -->
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="triwulan" id="tw2" value="2">

                                <label class="card border-success h-100 cursor-pointer p-2"
                                    for="tw2">

                                    <div class="fw-bold text-success">TW 2</div>
                                    <small class="text-muted">Apr – Jun</small>

                                </label>
                            </div>

                            <!-- TW3 -->
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="triwulan" id="tw3" value="3">

                                <label class="card border-warning h-100 cursor-pointer p-2"
                                    for="tw3">

                                    <div class="fw-bold text-warning">TW 3</div>
                                    <small class="text-muted">Jul – Sep</small>

                                </label>
                            </div>

                            <!-- TW4 -->
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="triwulan" id="tw4" value="4">

                                <label class="card border-danger h-100 cursor-pointer p-2"
                                    for="tw4">

                                    <div class="fw-bold text-danger">TW 4</div>
                                    <small class="text-muted">Okt – Des</small>

                                </label>
                            </div>

                        </div>
                    </div>

                    <!-- Desil -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Desil</label>
                        <div class="d-flex flex-wrap gap-2">

                            <?php for ($i = 0; $i <= 10; $i++): ?>

                                <?php
                                // 🚀 PERBAIKAN: Penentuan warna tombol
                                if ($i == 0) {
                                    $warna = 'secondary'; // abu-abu untuk belum diketahui / non-desil
                                } elseif ($i <= 3) {
                                    $warna = 'success'; // hijau
                                } elseif ($i <= 5) {
                                    $warna = 'warning'; // kuning
                                } else {
                                    $warna = 'danger'; // merah
                                }
                                ?>

                                <input
                                    type="radio"
                                    class="btn-check"
                                    name="desil"
                                    id="desil<?= $i ?>"
                                    value="<?= $i ?>"
                                    required>

                                <label
                                    class="btn btn-outline-<?= $warna ?> rounded-pill px-3"
                                    for="desil<?= $i ?>">
                                    Desil <?= $i ?>
                                </label>

                            <?php endfor; ?>

                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" id="btnSaveHistorical" class="btn btn-primary">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->include('dtsen/se/modal_input_desil'); ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // =============================
    // 🌍 SELECT2 WILAYAH DOMISILI (Pindahan dari Tab Rumah)
    // =============================
    document.addEventListener("DOMContentLoaded", function() {
        async function fetchJSON(url) {
            const res = await fetch(url, {
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('Network error');
            return res.json();
        }

        $('#rumah_provinsi, #rumah_regency, #rumah_district, #rumah_village').select2({
            width: '100%'
        });

        const pre = {
            province: "<?= esc($wil['province'] ?? '') ?>",
            regency: "<?= esc($wil['regency'] ?? '') ?>",
            district: "<?= esc($wil['district'] ?? '') ?>",
            village: "<?= esc($wil['village'] ?? '') ?>"
        };

        fetchJSON('/api/villages/provinces').then(data => {
            for (const p of data) {
                $('#rumah_provinsi').append(`<option value="${p.id}" ${(p.id == pre.province) ? 'selected' : ''}>${p.name}</option>`);
            }
            if (pre.province) $('#rumah_provinsi').trigger('change');
        });

        $('#rumah_provinsi').on('change', function() {
            const id = $(this).val();
            $('#rumah_regency, #rumah_district, #rumah_village').html('<option value="">[Pilih]</option>');
            if (!id) return;
            fetchJSON(`/api/villages/regencies/${id}`).then(data => {
                for (const r of data) $('#rumah_regency').append(`<option value="${r.id}" ${(r.id == pre.regency) ? 'selected' : ''}>${r.name}</option>`);
                if (pre.regency) $('#rumah_regency').trigger('change');
            });
        });

        $('#rumah_regency').on('change', function() {
            const id = $(this).val();
            $('#rumah_district, #rumah_village').html('<option value="">[Pilih]</option>');
            if (!id) return;
            fetchJSON(`/api/villages/districts/${id}`).then(data => {
                for (const d of data) $('#rumah_district').append(`<option value="${d.id}" ${(d.id == pre.district) ? 'selected' : ''}>${d.name}</option>`);
                if (pre.district) $('#rumah_district').trigger('change');
            });
        });

        $('#rumah_district').on('change', function() {
            const id = $(this).val();
            $('#rumah_village').html('<option value="">[Pilih]</option>');
            if (!id) return;
            fetchJSON(`/api/villages/villages/${id}`).then(data => {
                for (const v of data) $('#rumah_village').append(`<option value="${v.id}" ${(v.id == pre.village) ? 'selected' : ''}>${v.name}</option>`);
            });
        });
    });

    // =============================
    // 🌍 INISIALISASI PETA LEAFLET (MODE SATELIT ESRI)
    // =============================
    document.addEventListener("DOMContentLoaded", function() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        let lat = latInput.value ? parseFloat(latInput.value) : -6.895;
        let lng = lngInput.value ? parseFloat(lngInput.value) : 107.634;

        const map = L.map('map').setView([lat, lng], 17); // Default kita turunkan sedikit ke 17 agar aman

        // 🚀 MENGGUNAKAN SATELIT ESRI WORLD IMAGERY DENGAN ANTI-BLANK
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 20, // Peta boleh di-zoom sedekat mungkin oleh jari operator
            maxNativeZoom: 17, // 🚀 TAPI server hanya akan dipanggil maksimal sampai zoom 17 (Mencegah Error)
            attribution: 'Tiles &copy; Esri'
        }).addTo(map);

        // Lapisan label nama jalan (Opsional)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
            maxZoom: 20,
            maxNativeZoom: 18,
            attribution: '&copy; CARTO'
        }).addTo(map);

        let marker = null;
        if (latInput.value && lngInput.value) {
            marker = L.marker([lat, lng], {
                draggable: <?= $editable ? 'true' : 'false' ?>
            }).addTo(map);
        }

        // ... (Sisa kode drag marker, get location, dan copy clipboard di bawahnya TETAP SAMA) ...

        if (marker && <?= $editable ? 'true' : 'false' ?>) {
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                latInput.value = pos.lat.toFixed(6);
                lngInput.value = pos.lng.toFixed(6);
            });
        }

        const btnGetLocation = document.getElementById('btnGetLocation');
        if (btnGetLocation) {
            btnGetLocation.addEventListener('click', function() {
                if (!navigator.geolocation) return Swal.fire("Error", "GPS Tidak Didukung", "error");

                Swal.fire({
                    title: "Mengunci Satelit GPS...",
                    text: "Berdiri di luar ruangan agar lebih akurat.",
                    didOpen: () => Swal.showLoading()
                });

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        Swal.close();
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        const accuracy = position.coords.accuracy; // 🚀 Mendapatkan radius akurasi dalam satuan meter

                        latInput.value = userLat.toFixed(6);
                        lngInput.value = userLng.toFixed(6);

                        if (!marker) {
                            marker = L.marker([userLat, userLng], {
                                draggable: true
                            }).addTo(map);
                            marker.on('dragend', function(e) {
                                latInput.value = e.target.getLatLng().lat.toFixed(6);
                                lngInput.value = e.target.getLatLng().lng.toFixed(6);
                            });
                        } else {
                            marker.setLatLng([userLat, userLng]);
                        }

                        // Cari baris ini di dalam getCurrentPosition
                        map.setView([userLat, userLng], 18); // 🚀 Ubah dari 19 menjadi 18

                        // 🚀 Berikan informasi akurasi kepada operator
                        let iconType = accuracy <= 20 ? 'success' : 'warning';
                        let accMsg = accuracy <= 20 ? 'Sangat Akurat' : 'Cek Peta (Geser pin jika kurang pas)';

                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: iconType,
                            title: `Akurasi: ${accuracy.toFixed(0)} Meter`,
                            text: accMsg,
                            timer: 4500,
                            showConfirmButton: false
                        });
                    },
                    function(error) {
                        Swal.close();
                        Swal.fire("Gagal", "Pastikan GPS/Lokasi HP menyala dan izinkan browser mengaksesnya.", "warning");
                    }, {
                        enableHighAccuracy: true,
                        timeout: 15000, // 🚀 Beri waktu lebih lama agar HP sempat menangkap sinyal GPS asli
                        maximumAge: 0 // 🚀 PAKSA sistem mengambil data real-time, JANGAN gunakan cache lokasi
                    }
                );
            });
        }

        document.getElementById('btnCopyLat').addEventListener('click', () => {
            navigator.clipboard.writeText(latInput.value);
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
            navigator.clipboard.writeText(lngInput.value);
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'success',
                title: 'Longitude disalin',
                showConfirmButton: false,
                timer: 1500
            });
        });
        document.getElementById("btnCopyFull").addEventListener("click", function() {
            const full = latInput.value + ", " + lngInput.value;
            navigator.clipboard.writeText(full);
            Swal.fire({
                icon: "success",
                title: "Koordinat Disalin!",
                text: full,
                timer: 1800,
                showConfirmButton: false
            });
        });
    });
</script>

<script>
    let desilChartInstance = null;

    function loadDesilChart() {

        console.log("LOAD DESIL CHART DIPANGGIL");

        const chartEl = document.querySelector("#desilChart");
        if (!chartEl) {
            console.log("desilChart element tidak ditemukan");
            return;
        }

        if (desilChartInstance !== null) return;

        const idKK = <?= (int)$id_kk ?>;

        // 🚀 UBAH BARIS INI (URL Dinamis):
        fetch("<?= base_url($roleId == 6 ? 'sensus-ekonomi/desil-history' : 'pembaruan-keluarga/desil-history') ?>/" + idKK)
            .then(res => res.json())
            .then(res => {

                console.log("Response:", res);

                if (res.status !== 'success' || res.data.length === 0) {
                    chartEl.innerHTML = '<div class="text-center text-muted py-5">Belum ada histori desil.</div>';
                    return;
                }

                const data = res.data;
                const categories = data.map(d => d.periode);
                const values = data.map(d => d.desil);

                const options = {
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    series: [{
                        name: 'Desil',
                        data: values
                    }],
                    xaxis: {
                        categories: categories
                    },
                    yaxis: {
                        // 🚀 PERBAIKAN: Ubah min jadi 0 dan tickAmount jadi 10
                        min: 0,
                        max: 10,
                        tickAmount: 10
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    markers: {
                        size: 6,
                        hover: {
                            size: 8
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "Desil " + val;
                            }
                        }
                    },
                    colors: ['#0d6efd'],
                    grid: {
                        borderColor: '#e9ecef'
                    }
                };

                desilChartInstance = new ApexCharts(chartEl, options);
                desilChartInstance.render();
            });
    }

    // Panggil langsung tanpa tunggu tab event
    // document.addEventListener("DOMContentLoaded", function() {
    //     setTimeout(loadDesilChart, 300);
    // });

    // Render saat tab keluarga aktif
    // document.addEventListener('shown.bs.tab', function(event) {
    //     const targetId = event.target.getAttribute('data-bs-target');
    //     if (targetId === '#tabKeluarga') {
    //         loadDesilChart();
    //     }
    // });

    // Jika tab keluarga sudah aktif saat load
    // document.addEventListener("DOMContentLoaded", function() {
    //     const activeTab = document.querySelector('.nav-link.active');
    //     if (activeTab && activeTab.getAttribute('data-bs-target') === '#tabKeluarga') {
    //         loadDesilChart();
    //     }
    // });

    // 🚀 Render grafik HANYA saat Modal Grafik Desil terbuka (Mencegah bug width 0px ApexCharts)
    document.getElementById('modalChartDesil').addEventListener('shown.bs.modal', function() {
        loadDesilChart();
    });

    // 🚀 Sabuk Pengaman: Perbaiki scrolling halaman jika modal bertumpuk ditutup
    document.addEventListener('hidden.bs.modal', function(event) {
        if (document.querySelectorAll('.modal.show').length > 0) {
            document.body.classList.add('modal-open');
        }
    });

    document.addEventListener('shown.bs.tab', function(event) {
        if (event.target.getAttribute('data-bs-target') === '#tabRumah') {

            $('#rumah_provinsi, #rumah_regency, #rumah_district, #rumah_village').select2({
                width: '100%'
            });

        }
    });

    $(document).on('click', '[data-bs-target="#modalInputDesil"]', function() {

        const id = $(this).attr('data-id');
        const nama = $(this).attr('data-nama');
        const nokk = $(this).attr('data-nokk');
        const alamat = $(this).attr('data-alamat');
        const desil = $(this).attr('data-desil');

        const modal = $('#modalInputDesil');

        modal.find('#modal_id_kk').val(id);
        modal.find('#modal_no_kk').val(nokk);
        modal.find('#modal_kepala_keluarga').val(nama);
        modal.find('#modal_alamat').val(alamat);
        modal.find('#kategori_desil').val(desil);
    });

    document.getElementById('btnSyncDesil')?.addEventListener('click', function() {

        const btn = this;
        const idKK = <?= (int)$id_kk ?>;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sync...';

        fetch("<?= base_url('pembaruan-keluarga/sync-desil') ?>/" + idKK, {
                method: "POST",
                credentials: "same-origin"
            })
            .then(res => res.json())
            .then(res => {

                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync';

                // ✅ SUCCESS - ADA PERUBAHAN
                if (res.status === 'changed') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Desil Berubah',
                        html: `Dari <b>${res.from ?? '-'}</b> menjadi <b>${res.to}</b><br><small>${res.periode}</small>`,
                        showConfirmButton: false,
                        timer: 1800,
                        timerProgressBar: true
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 1800);
                }

                // ℹ️ TIDAK BERUBAH
                else if (res.status === 'unchanged') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Perubahan',
                        text: 'Desil tetap sama.',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                }

                // ❌ ERROR DARI SERVER
                else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync';

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal melakukan sinkronisasi.',
                    showConfirmButton: false,
                    timer: 2000
                });
            });

    });
</script>

<script>
    // ==========================================
    // 🚀 SINKRONISASI JUMLAH ANGGOTA OTOMATIS (SMART MODE)
    // ==========================================
    window.updateJumlahAnggotaOtomatis = function(totalAnggota) {
        // Tembak angkanya ke Tab Keluarga (Selalu update)
        const inputKeluarga = document.getElementById('auto_jumlah_anggota');
        if (inputKeluarga) {
            inputKeluarga.value = totalAnggota;
        }

        // Tembak angkanya ke Tab Rumah (Secara Cerdas)
        const inputRumah = document.getElementById('jumlah_orang_dalam_rumah');
        if (inputRumah) {
            let orangSaatIni = parseInt(inputRumah.value) || 0;

            // 🚀 SMART UPDATE: 
            // Hanya timpa jika kosong, atau jika jumlah orang saat ini 
            // ternyata lebih sedikit dari jumlah anggota keluarga inti.
            if (orangSaatIni < parseInt(totalAnggota)) {
                inputRumah.value = totalAnggota;
            }
        }
    };

    // =======================================================
    // 📋 FUNGSI SALIN KE CLIPBOARD (DINAMIS UNTUK SEMUA INPUT)
    // =======================================================
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.btn-copy-input').forEach(btn => {
            btn.addEventListener('click', function() {
                // Ambil ID target dari atribut data-target
                const targetSelector = this.getAttribute('data-target');
                const inputEl = document.querySelector(targetSelector);

                if (inputEl && inputEl.value.trim() !== '') {
                    // Salin ke clipboard
                    navigator.clipboard.writeText(inputEl.value.trim()).then(() => {
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'success',
                            title: 'Teks disalin!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });
                } else {
                    // Jika inputan masih kosong
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'warning',
                        title: 'Kolom masih kosong!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });
    });
</script>