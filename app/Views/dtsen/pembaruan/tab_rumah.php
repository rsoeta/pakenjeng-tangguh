<?php
$roleId = $user['role_id'] ?? 99;
$editable = ($roleId <= 4); // Operator & Pendata bisa edit
$perumahan = $payload['perumahan'] ?? [];
$wil = $perumahan['wilayah'] ?? [];
$kond = $perumahan['kondisi'] ?? [];
$san = $perumahan['sanitasi'] ?? [];

?>

<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">🏡 Keterangan Perumahan</h5>
        <div>
            <span id="badgeRumah" class="badge <?= empty($perumahan) ? 'bg-secondary' : (/*frontend akan update*/'bg-danger') ?>">
                <?= empty($perumahan) ? 'Kosong' : 'Belum Lengkap' ?>
            </span>
        </div>
    </div>

    <form id="formRumahFull" enctype="multipart/form-data">
        <input type="hidden" name="dtsen_usulan_id" value="<?= esc($usulan['id'] ?? '') ?>">
        <input type="hidden" name="no_kk" value="<?= esc($perumahan['no_kk'] ?? $perumahan['no_kk'] ?? '') ?>">
        <input type="hidden" name="sumber" value="<?= esc($sumber ?? 'master') ?>">

        <!-- 1. Wilayah Domisili -->
        <div class="card mb-3">
            <div class="card-header">
                <strong>1. Wilayah Domisili</strong>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Provinsi</label>
                        <select id="rumah_provinsi" name="provinsi" class="form-select required" <?= $editable ? '' : 'disabled' ?>>
                            <option value="">[Pilih Provinsi]</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kabupaten / Kota</label>
                        <select id="rumah_regency" name="regency" class="form-select required" <?= $editable ? '' : 'disabled' ?>>
                            <option value="">[Pilih Kabupaten]</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kecamatan</label>
                        <select id="rumah_district" name="district" class="form-select required" <?= $editable ? '' : 'disabled' ?>>
                            <option value="">[Pilih Kecamatan]</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Desa / Kelurahan</label>
                        <select id="rumah_village" name="village" class="form-select required" <?= $editable ? '' : 'disabled' ?>>
                            <option value="">[Pilih Desa]</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Kondisi Bangunan -->
        <div class="card mb-3">
            <div class="card-header">
                <strong>2. Kondisi Bangunan & Fasilitas</strong>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Status Kepemilikan Rumah</label>
                        <select name="status_kepemilikan" id="status_kepemilikan" class="form-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiPemilik = ['Milik Sendiri', 'Sewa', 'Kontrak', 'Bebas Sewa', 'Lainnya'];
                            $selpem = $kond['status_kepemilikan'] ?? $perumahan['status_kepemilikan'] ?? '';
                            foreach ($opsiPemilik as $o) {
                                $s = ($o === $selpem) ? 'selected' : '';
                                echo "<option value=\"{$o}\" {$s}>{$o}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Luas Lantai (m²)</label>
                        <input type="number" step="0.01" min="0" name="luas_lantai" id="luas_lantai" class="form-control" value="<?= esc($kond['luas_lantai'] ?? '') ?>" <?= $editable ? '' : 'readonly' ?>>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Jenis Lantai</label>
                        <select name="jenis_lantai" id="jenis_lantai" class="form-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php $opsiLantai = ['Marmer/granit', 'Keramik', 'Parket/vinil/karpet', 'Ubin/tegal/teraso', 'Kayu', 'Semen', 'Bambu', 'Tanah', 'Lainnya'];
                            $sel = $kond['jenis_lantai'] ?? '';
                            foreach ($opsiLantai as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Jenis Dinding</label>
                        <select name="jenis_dinding" id="jenis_dinding" class="form-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiDinding = [
                                'Tembok',
                                'Plesteran anyaman bambu/kawat',
                                'Kayu/papan/gypsum/GRC/calciboard',
                                'Anyaman bambu',
                                'Batang kayu',
                                'Bambu',
                                'Lainnya'
                            ];
                            $sel = $kond['jenis_dinding'] ?? '';
                            foreach ($opsiDinding as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Jenis Atap</label>
                        <select name="jenis_atap" id="jenis_atap" class="form-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php $opsiAtap = ['Beton', 'Genteng',  'Seng', 'Asbes', 'Bambu', 'Kayu/sirap', 'Rumbia', 'Lainnya'];
                            $sel = $kond['jenis_atap'] ?? '';
                            foreach ($opsiAtap as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            } ?>
                        </select>
                    </div>
                    <!-- </div> -->

                    <!-- <div class="row g-2 mt-2"> -->
                    <div class="col-md-2">
                        <label class="form-label">Bahan Bakar Utama Memasak</label>
                        <select name="bahan_bakar" id="bahan_bakar" class="form-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiBakar = ['Tidak memasak di rumah', 'Listrik', 'Gas elpiji5,5kg/blue', 'Gas elpiji 12 kg', 'Gas elpiji 3 kg', 'Gas kota/meteran PGN', 'Biogas', 'Minyak tanah', 'Briket', 'Arang', 'Kayu bakar', 'Lainnya'];
                            $sel = $kond['bahan_bakar'] ?? '';
                            foreach ($opsiBakar as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Sumber Air Minum</label>
                        <select name="sumber_air" id="sumber_air" class="form-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiAir = ['Air kemasan bermerk', 'Air isi ulang', 'Leding', 'Sumur bor/pompa', 'Sumur terlindung', 'Sumur tak terlindung', 'Mata air terlindung', 'Mata air tak terlindung', 'Air permukaan (sungai/danau/waduk/kolam/irigasi)', 'Air hujan', 'Lainnya'];
                            $sel = $kond['sumber_air'] ?? '';
                            foreach ($opsiAir as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jarak Sumber Air Minum ke Pembuangan Limbah</label>
                        <select name="jarak_air_ke_limbah" id="jarak_air_ke_limbah" class="form-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiJarak = ['< 10 meter', '>= 10 meter', 'Tidak tahu'];
                            $sel = $san['jarak_air_ke_limbah'] ?? '';
                            foreach ($opsiJarak as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Sumber Penerangan Utama</label>
                        <select name="sumber_listrik" id="sumber_listrik" class="form-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiListrik = ['Listrik PLN dengan meteran', 'Listrik PLN tanpa meteran', 'Listrik non-PLN', 'Bukan listrik'];
                            $sel = $kond['sumber_listrik'] ?? '';
                            foreach ($opsiListrik as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- tambahan daya listrik (muncul hanya bila PLN dengan meteran) -->
                <div id="div_info_listrik" class="row g-2 mt-2" style="display: none;">
                    <div class="col-md-4">
                        <label class="form-label">Nomor Pelanggan</label>
                        <input type="text" name="nomor_pelanggan" id="nomor_pelanggan" class="form-control onlynum" value="<?= esc($kond['nomor_pelanggan'] ?? '') ?>" <?= $editable ? '' : 'readonly' ?>>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nomor Meter</label>
                        <input type="text" name="nomor_meter" id="nomor_meter" class="form-control onlynum" value="<?= esc($kond['nomor_meter'] ?? '') ?>" <?= $editable ? '' : 'readonly' ?>>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Besar Daya</label>
                        <select name="daya_listrik" id="daya_listrik" class="form-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiDaya = ['TANPA METERAN', '450 WATT', '900 WATT', '1.300 WATT', '2.200 WATT', '> 2.200 WATT', '< = 900 WATT', '> 900 WATT'];
                            $sel = $kond['daya_listrik'] ?? '';
                            foreach ($opsiDaya as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Sanitasi -->
        <div class="card mb-3">
            <div class="card-header">
                <strong>3. Sarana Sanitasi</strong>
            </div>
            <div class="card-body">

                <div class="row g-2 align-items-end">

                    <!-- Fasilitas BAB -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Kepemilikan Fasilitas BAB</label>
                        <select name="fasilitas_bab" id="fasilitas_bab"
                            class="form-select uniform-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiBab = [
                                'Ada, digunakan hanya Anggota Keluarga sendiri',
                                'Ada, digunakan bersama Anggota Keluarga dari Keluarga tertentu',
                                'Ada, di MCK komunal',
                                'Ada, di MCK umum/siapapun menggunakan',
                                'Ada, Anggota Keluarga tidak menggunakan',
                                'Tidak ada fasilitas'
                            ];
                            $sel = $san['fasilitas_bab'] ?? '';
                            foreach ($opsiBab as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Jenis Kloset -->
                    <!-- <div class="fasilitas-extra"> -->
                    <div class="col-md-4 fasilitas-extra">
                        <label class="form-label fw-bold">Jenis Kloset</label>
                        <select name="jenis_kloset" id="jenis_kloset"
                            class="form-select uniform-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiKloset = ['Leher angsa', 'Plengsengan dengan tutup', 'Plengsengan tanpa tutup', 'Cemplung'];
                            $sel = $san['jenis_kloset'] ?? '';
                            foreach ($opsiKloset as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Pembuangan Tinja -->
                    <div class="col-md-4 fasilitas-extra">
                        <label class="form-label fw-bold">Tempat Pembuangan Akhir Tinja</label>
                        <select name="pembuangan_tinja" id="pembuangan_tinja"
                            class="form-select uniform-select" <?= $editable ? '' : 'disabled' ?>>
                            <?php
                            $opsiTinja = [
                                'Tangki septik',
                                'IPAL',
                                'Kolam/sawah/sungai/danau/laut',
                                'Lubang tanah',
                                'Pantai/tanah lapang/kebun',
                                'Lainnya'
                            ];
                            $sel = $san['pembuangan_tinja'] ?? '';
                            foreach ($opsiTinja as $o) {
                                $s = ($o == $sel) ? 'selected' : '';
                                echo "<option value=\"$o\" $s>$o</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- </div> -->
                </div>
            </div>
        </div>

        <!-- Tombol simpan -->
        <?php if ($editable): ?>
            <div class="text-end">
                <button type="button" id="btnSimpanRumah" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data Rumah</button>
            </div>
        <?php endif; ?>

    </form>
</div>

<!-- ============================== -->
<!-- Dependencies: Select2 (cascading dropdown), SweetAlert2 -->
<!-- (Pastikan Select2 dan SweetAlert sudah muncul di global layout AdminLTE; jika belum, URL di bawah bisa dipakai) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editable = <?= $editable ? 'true' : 'false' ?>;

        // ============ helper: fetch data from villages table endpoints ============
        async function fetchJSON(url) {
            const res = await fetch(url, {
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        }

        // initialize Select2
        $('#rumah_provinsi, #rumah_regency, #rumah_district, #rumah_village').select2({
            width: '100%'
        });

        const pre = {
            province: "<?= esc($wil['province'] ?? '') ?>",
            regency: "<?= esc($wil['regency'] ?? '') ?>",
            district: "<?= esc($wil['district'] ?? '') ?>",
            village: "<?= esc($wil['village'] ?? '') ?>"
        };

        // 🔹 Load provinces
        fetchJSON('/api/villages/provinces').then(data => {
            for (const p of data) {
                const sel = (p.id == pre.province) ? 'selected' : '';
                $('#rumah_provinsi').append(`<option value="${p.id}" ${sel}>${p.name}</option>`);
            }
            if (pre.province) $('#rumah_provinsi').trigger('change');
        });

        // 🔹 on province change → load regencies
        $('#rumah_provinsi').on('change', function() {
            const id = $(this).val();
            $('#rumah_regency').html('<option value="">[Pilih Kabupaten]</option>');
            $('#rumah_district').html('<option value="">[Pilih Kecamatan]</option>');
            $('#rumah_village').html('<option value="">[Pilih Desa]</option>');
            if (!id) return;

            fetchJSON(`/api/villages/regencies/${id}`).then(data => {
                for (const r of data) {
                    const sel = (r.id == pre.regency) ? 'selected' : '';
                    $('#rumah_regency').append(`<option value="${r.id}" ${sel}>${r.name}</option>`);
                }
                if (pre.regency) $('#rumah_regency').trigger('change');
            });
        });

        // 🔹 on regency change → load districts
        $('#rumah_regency').on('change', function() {
            const id = $(this).val();
            $('#rumah_district').html('<option value="">[Pilih Kecamatan]</option>');
            $('#rumah_village').html('<option value="">[Pilih Desa]</option>');
            if (!id) return;

            fetchJSON(`/api/villages/districts/${id}`).then(data => {
                for (const d of data) {
                    const sel = (d.id == pre.district) ? 'selected' : '';
                    $('#rumah_district').append(`<option value="${d.id}" ${sel}>${d.name}</option>`);
                }
                if (pre.district) $('#rumah_district').trigger('change');
            });
        });

        // 🔹 on district change → load villages
        $('#rumah_district').on('change', function() {
            const id = $(this).val();
            $('#rumah_village').html('<option value="">[Pilih Desa]</option>');
            if (!id) return;

            fetchJSON(`/api/villages/villages/${id}`).then(data => {
                for (const v of data) {
                    const sel = (v.id == pre.village) ? 'selected' : '';
                    $('#rumah_village').append(`<option value="${v.id}" ${sel}>${v.name}</option>`);
                }
            });
        });

        // if preselected province exists, trigger change sequence
        if (pre.province) {
            $('#rumah_provinsi').trigger('change');
        }

        // ============ dynamic PLN fields ============
        function toggleListrikFields() {
            const val = $('#sumber_listrik').val();
            if (val === 'Listrik PLN dengan meteran') {
                $('#div_info_listrik').slideDown();
            } else {
                $('#div_info_listrik').slideUp();
            }
        }
        $('#sumber_listrik').on('change', toggleListrikFields);
        toggleListrikFields(); // initial

        // ============ kelengkapan check =============
        const requiredFields = [
            '#status_kepemilikan',
            '#luas_lantai',
            '#jenis_lantai',
            '#jenis_atap',
            '#bahan_bakar',
            '#sumber_air',
            '#sumber_listrik',
            '#fasilitas_bab',
            '#jenis_kloset',
            '#pembuangan_tinja',
            '#jarak_air_ke_limbah'
        ];

        function checkKelengkapanRumah() {
            let missing = [];
            requiredFields.forEach(sel => {
                const el = document.querySelector(sel);
                if (!el) return missing.push(sel);
                const val = el.value;
                if (val === null || val === '' || val === '0') missing.push(sel);
            });

            // special: if sumber_listrik == PLN meteran, require nomor_pelanggan & daya_listrik
            const sumber = $('#sumber_listrik').val();
            if (sumber === 'Listrik PLN dengan meteran') {
                if (!$('#nomor_pelanggan').val()) missing.push('#nomor_pelanggan');
                if (!$('#daya_listrik').val()) missing.push('#daya_listrik');
            }

            // update badge
            const badge = $('#badgeRumah');
            if (missing.length === 0) {
                badge.removeClass('bg-danger bg-secondary').addClass('bg-success').text('Lengkap');
            } else {
                badge.removeClass('bg-success bg-secondary').addClass('bg-danger').text('Belum Lengkap');
            }
            return missing;
        }

        // run check on change for fields
        requiredFields.concat(['#nomor_pelanggan', '#daya_listrik']).forEach(s => {
            $(document).on('change input', s, function() {
                checkKelengkapanRumah();
            });
        });
        // initial check
        setTimeout(checkKelengkapanRumah, 250);

        // ============ submit/save handler ============
        $('#btnSimpanRumah').on('click', function() {
            const missing = checkKelengkapanRumah();
            if (missing.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Isian belum lengkap',
                    html: 'Beberapa field wajib belum diisi. Silakan lengkapi sebelum menyimpan.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const form = document.getElementById('formRumahFull');
            const formData = new FormData(form);

            // convert luas_lantai to numeric string if empty => null
            if (!formData.get('luas_lantai')) {
                formData.set('luas_lantai', '');
            }

            // optional: loading indicator
            Swal.fire({
                title: 'Menyimpan data...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/pembaruan-keluarga/save-rumah', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                })
                .then(resp => {
                    if (!resp.ok) throw new Error('Gagal menyimpan');
                    return resp.json();
                })
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire('Berhasil', res.message, 'success');
                        // reload or update UI if necessary
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', err.message || 'Terjadi kesalahan jaringan', 'error');
                });
        });

    });
</script>

<style>
    /* small spacing tweaks to match AdminLTE aesthetics */
    .card .card-header {
        font-size: .95rem;
    }

    .form-label {
        font-size: .9rem;
    }

    .badge {
        font-size: .85rem;
        padding: .45em .6em;
    }
</style>