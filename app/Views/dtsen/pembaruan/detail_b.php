<?= $this->extend('templates/index') ?>
<?= $this->section('content') ?>

<div class="container-fluid p-3">
    <h4 class="fw-bold mb-3">
        <i class="fas fa-users-cog me-1"></i> Detail Pembaruan Keluarga
    </h4>

    <div class="alert alert-info small">
        ID KK: <strong><?= esc($id_kk) ?></strong><br>
        Status: <strong><?= esc($usulan['status'] ?? 'utama') ?></strong>
    </div>

    <!-- Tab navigasi -->
    <ul class="nav nav-tabs" id="tabsPembaruan" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="keluarga-tab" data-bs-toggle="tab" href="#keluarga" role="tab">Data Keluarga</a></li>
        <li class="nav-item"><a class="nav-link" id="rumah-tab" data-bs-toggle="tab" href="#rumah" role="tab">Kondisi Rumah</a></li>
        <li class="nav-item"><a class="nav-link" id="anggota-tab" data-bs-toggle="tab" href="#anggota" role="tab">Daftar Anggota</a></li>
    </ul>

    <div class="tab-content mt-3" id="tabsContent">
        <div class="tab-pane fade show active" id="keluarga" role="tabpanel">
            <?= $this->include('dtsen/pembaruan/tab_keluarga') ?>
        </div>
        <div class="tab-pane fade" id="rumah" role="tabpanel">
            <?= $this->include('dtsen/pembaruan/tab_rumah') ?>
        </div>
        <div class="tab-pane fade" id="anggota" role="tabpanel">
            <?= $this->include('dtsen/pembaruan/tab_anggota') ?>
        </div>
    </div>
</div>

<!-- ============================== -->
<!-- 🔗 Script Dependencies -->
<!-- ============================== -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.full.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JS konfigurasi base & payload -->
<script>
    const baseUrl = "<?= base_url() ?>";
    const payload = <?= json_encode($payload ?? []) ?>;
    console.log("🚀 Payload dari PHP:", payload);
</script>

<!-- ============================== -->
<!-- ⚙️ Script Prefill & Interaksi -->
<!-- ============================== -->
<script>
    $(document).ready(function() {

        // ================================
        // 🌍 Inisialisasi Select2 AJAX Wilayah
        // ================================
        function initSelect2Wilayah() {
            const select2Config = {
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih...',
                allowClear: true,
                ajax: {
                    delay: 200,
                    dataType: 'json',
                    processResults: function(data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.name
                            }))
                        };
                    }
                }
            };

            // 1️⃣ Provinsi
            $('#rumah_provinsi, #provinsi').select2($.extend(true, {}, select2Config, {
                ajax: {
                    ...select2Config.ajax,
                    url: baseUrl + '/api/villages/provinces'
                }
            }));

            // 2️⃣ Kabupaten
            $('#rumah_regency, #kabupaten').select2($.extend(true, {}, select2Config, {
                ajax: {
                    ...select2Config.ajax,
                    transport: function(params, success, failure) {
                        const provID = $('#rumah_provinsi').val() || $('#provinsi').val();
                        if (!provID) return success([]);
                        $.ajax({
                            url: baseUrl + '/api/villages/regencies/' + provID,
                            dataType: 'json',
                            success,
                            error: failure
                        });
                    }
                }
            }));

            // 3️⃣ Kecamatan
            $('#rumah_district, #kecamatan').select2($.extend(true, {}, select2Config, {
                ajax: {
                    ...select2Config.ajax,
                    transport: function(params, success, failure) {
                        const kabID = $('#rumah_regency').val() || $('#kabupaten').val();
                        if (!kabID) return success([]);
                        $.ajax({
                            url: baseUrl + '/api/villages/districts/' + kabID,
                            dataType: 'json',
                            success,
                            error: failure
                        });
                    }
                }
            }));

            // 4️⃣ Desa
            $('#rumah_village, #desa').select2($.extend(true, {}, select2Config, {
                ajax: {
                    ...select2Config.ajax,
                    transport: function(params, success, failure) {
                        const kecID = $('#rumah_district').val() || $('#kecamatan').val();
                        if (!kecID) return success([]);
                        $.ajax({
                            url: baseUrl + '/api/villages/villages/' + kecID,
                            dataType: 'json',
                            success,
                            error: failure
                        });
                    }
                }
            }));

            // reset chain dropdown saat ubah atasannya
            $('#rumah_provinsi, #provinsi').on('change', () => {
                $('#rumah_regency, #kabupaten, #rumah_district, #kecamatan, #rumah_village, #desa')
                    .val(null).trigger('change');
            });
            $('#rumah_regency, #kabupaten').on('change', () => {
                $('#rumah_district, #kecamatan, #rumah_village, #desa')
                    .val(null).trigger('change');
            });
            $('#rumah_district, #kecamatan').on('change', () => {
                $('#rumah_village, #desa').val(null).trigger('change');
            });
        }


        // ================================
        // 🏠 Prefill Tab Rumah
        // ================================
        function prefillRumah(perumahan) {
            if (!perumahan || typeof perumahan !== 'object') {
                console.warn('⚠️ Data perumahan kosong, tidak bisa prefill rumah');
                return;
            }
            if (perumahan.kondisi) {
                $('#jenis_atap').val(perumahan.kondisi.jenis_atap || '');
                $('#jenis_lantai').val(perumahan.kondisi.jenis_lantai || '');
                $('#sumber_air').val(perumahan.kondisi.sumber_air || '');
                $('#status_kepemilikan').val(perumahan.kondisi.status_kepemilikan || '');
                $('#bahan_bakar').val(perumahan.kondisi.bahan_bakar || '');
                $('#daya_listrik').val(perumahan.kondisi.daya_listrik || '');
            }
            if (perumahan.sanitasi) {
                $('#jenis_kloset').val(perumahan.sanitasi.jenis_kloset || '');
                $('#fasilitas_bab').val(perumahan.sanitasi.fasilitas_bab || '');
                $('#pembuangan_tinja').val(perumahan.sanitasi.pembuangan_tinja || '');
                $('#jarak_air_ke_limbah').val(perumahan.sanitasi.jarak_air_ke_limbah || '');
            }
        }

        // ================================
        // 🌍 Prefill Wilayah (Select2 AJAX)
        // ================================
        function prefillWilayah(payloadWilayah, payloadNama) {
            if (!payloadWilayah || !payloadNama) {
                console.warn('⚠️ Data wilayah tidak ditemukan di payload.');
                return;
            }
            const setSelect2Ajax = (selector, id, text) => {
                if (!id || !text) return;
                const $select = $(selector);
                const newOption = new Option(text, id, true, true);
                $select.append(newOption).trigger('change');
            };
            setSelect2Ajax('#rumah_provinsi', payloadWilayah.provinsi, payloadNama.provinsi);
            setSelect2Ajax('#rumah_regency', payloadWilayah.kabupaten, payloadNama.kabupaten);
            setSelect2Ajax('#rumah_district', payloadWilayah.kecamatan, payloadNama.kecamatan);
            setSelect2Ajax('#rumah_village', payloadWilayah.desa, payloadNama.desa);
        }

        // ============================================================
        // 🚀 Jalankan Saat Halaman Siap
        // ============================================================
        initSelect2Wilayah();

        if (payload && payload.perumahan) {
            const perumahan = payload.perumahan;
            prefillKeluarga(perumahan);
            prefillRumah(perumahan);
            if (perumahan.wilayah && perumahan.wilayah_nama) {
                prefillWilayah(perumahan.wilayah, perumahan.wilayah_nama);
            }
        } else {
            console.warn('⚠️ Payload kosong atau tidak mengandung data perumahan.');
        }
    });
</script>

<?= $this->endSection() ?>