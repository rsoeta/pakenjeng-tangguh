<?php
// =============================================
// Tab: Kepemilikan Aset (Pemutakhiran Keluarga)
// =============================================

// Hak akses
$roleId = session()->get('role_id') ?? ($user['role_id'] ?? 99);
$editable = ($roleId <= 4);
$disabled = $editable ? '' : 'disabled';

// Prefill otomatis
$aset = $aset ?? [];
if (!empty($payload['aset'])) {
    $aset = array_merge($aset, $payload['aset']);
}

// Badge indikator kelengkapan
$isComplete = !empty($aset) && !in_array(null, $aset, true);
?>

<style>
    /* CSS ini sudah sangat baik untuk membuat select readonly secara visual */
    select[readonly],
    select[data-auto="true"] {
        pointer-events: none;
        background-color: #f8f9fa;
        opacity: 0.95;
    }
</style>
<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            💰 Kepemilikan Aset
        </h5>
    </div>

    <form id="formAset" class="needs-validation" novalidate>
        <input type="hidden" name="dtsen_usulan_id" value="<?= esc($usulan['id'] ?? '') ?>">
        <input type="hidden" name="no_kk" value="<?= esc($perumahan['no_kk'] ?? $perumahan['no_kk'] ?? '') ?>">
        <input type="hidden" name="sumber" value="<?= esc($sumber ?? 'master') ?>">

        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        Jumlah Aset Bergerak yang Dimiliki:
                    </div>
                    <div class="card-body">
                        <?php
                        // 🚀 PENYESUAIAN ARRAY ASET SESUAI BPS
                        $asetBergerak = [
                            'Tabung Gas 3 KG' => 'tabung_gas_3kg',          // Di urutan pertama
                            'Tabung Gas 5,5 kg atau lebih' => 'tabung_gas', // Menyusul di urutan kedua
                            'Lemari es / Kulkas' => 'kulkas',
                            'Air Conditioner (AC)' => 'ac',
                            'Emas / Perhiasan (gram)' => 'emas',
                            'Komputer / Laptop / Tablet' => 'laptop',
                            'Sepeda Motor' => 'sepeda_motor',
                            'Mobil' => 'mobil',
                            'Pemanas Air (Water Heater)' => 'water_heater',
                            'Telepon Rumah (PSTN)' => 'telepon_rumah',
                            'Televisi Layar Datar (min. 30 inci)' => 'tv_lcd',
                            'Sepeda' => 'sepeda',
                            'Perahu' => 'perahu',
                            'Smartphone' => 'smartphone'
                        ];
                        ?>

                        <div class="row">
                            <?php foreach ($asetBergerak as $label => $name): ?>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-primary fw-semibold"><?= esc($label) ?></label>

                                    <!-- Tambahkan ID di sini -->
                                    <input type="number" min="0" class="form-control form-control-sm border-primary"
                                        name="<?= $name ?>" id="<?= $name ?>" value="<?= esc($aset[$name] ?? 0) ?>" <?= $disabled ?>>

                                    <?php if ($name === 'sepeda_motor'): ?>
                                        <!-- 🚀 ELEMEN DINAMIS: Nilai Sepeda Motor -->
                                        <div id="div_nilai_sepeda_motor" class="mt-2 p-2 bg-light border border-primary rounded" style="display: none;">
                                            <label class="form-label text-primary mb-1">Total Nilai Aset Motor (Rp) <span class="text-danger">*</span></label>
                                            <input type="text" name="nilai_sepeda_motor" id="nilai_sepeda_motor" class="form-control form-control-sm rupiah border-primary" value="<?= esc($aset['nilai_sepeda_motor'] ?? '') ?>" <?= $disabled ?> placeholder="Rp...">
                                        </div>
                                    <?php elseif ($name === 'mobil'): ?>
                                        <!-- 🚀 ELEMEN DINAMIS: Nilai Mobil -->
                                        <div id="div_nilai_mobil" class="mt-2 p-2 bg-light border border-primary rounded" style="display: none;">
                                            <label class="form-label text-primary mb-1">Total Nilai Aset Mobil (Rp) <span class="text-danger">*</span></label>
                                            <input type="text" name="nilai_mobil" id="nilai_mobil" class="form-control form-control-sm rupiah border-primary" value="<?= esc($aset['nilai_mobil'] ?? '') ?>" <?= $disabled ?> placeholder="Rp...">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        Jumlah Ternak yang Dimiliki:
                    </div>
                    <div class="card-body">
                        <?php
                        $ternak = [
                            'Sapi' => 'sapi',
                            'Kuda' => 'kuda',
                            'Babi' => 'babi',
                            'Kerbau' => 'kerbau',
                            'Kambing / Domba' => 'kambing'
                        ];
                        ?>
                        <div class="row">
                            <?php foreach ($ternak as $label => $name): ?>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label"><?= esc("Jumlah $label") ?></label>
                                    <input type="number" min="0" class="form-control form-control-sm"
                                        name="<?= $name ?>" value="<?= esc($aset[$name] ?? 0) ?>" <?= $disabled ?>>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        Jumlah Aset Tidak Bergerak yang Dimiliki:
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- 🚀 ELEMEN BARU (BPS): Jumlah Titik Lahan Sawah/Kebun & Nilai -->
                            <div class="col-md-4">
                                <label class="form-label" title="Dihitung per titik lokasi/RT, bukan meter persegi">
                                    Jumlah Titik Lahan Sawah/Kebun <span class="text-danger">*</span>
                                </label>
                                <!-- step diubah jadi 1 karena hitungannya unit/titik, bukan desimal -->
                                <input type="number" min="0" step="1" name="luas_sawah" id="luas_sawah"
                                    class="form-control form-control-sm <?= $disabled ? '' : 'required-field' ?>"
                                    value="<?= esc($aset['luas_sawah'] ?? '') ?>" <?= $disabled ?> <?= $disabled ? '' : 'required' ?> placeholder="Ketik 0 jika tidak punya">

                                <?php if (!$disabled): ?>
                                    <div class="invalid-feedback">
                                        Wajib mengisi jumlah lokasi (isi 0 jika tidak memiliki).
                                    </div>
                                <?php endif; ?>

                                <!-- 🚀 ELEMEN DINAMIS: Nilai Sawah -->
                                <div id="div_nilai_sawah" class="mt-2 p-2 bg-light border border-primary rounded" style="display: none;">
                                    <label class="form-label text-primary mb-1">Total Nilai Aset Lahan (Rp) <span class="text-danger">*</span></label>
                                    <input type="text" name="nilai_sawah" id="nilai_sawah" class="form-control form-control-sm rupiah border-primary" value="<?= esc($aset['nilai_sawah'] ?? '') ?>" <?= $disabled ?> placeholder="Rp...">
                                    <div class="invalid-feedback">Wajib diisi karena jumlah lahan > 0</div>
                                </div>
                            </div>

                            <!-- Memiliki Lahan (Tetap Ada) -->
                            <div class="col-md-4">
                                <label class="form-label">Memiliki Lahan (selain yang ditempati)</label>
                                <select name="memiliki_lahan"
                                    id="memiliki_lahan"
                                    class="form-select form-select-sm bg-light"
                                    readonly tabindex="-1" data-auto="true">
                                    <option value="TIDAK" <?= ($aset['memiliki_lahan'] ?? '') === 'TIDAK' ? 'selected' : '' ?>>TIDAK</option>
                                    <option value="YA" <?= ($aset['memiliki_lahan'] ?? '') === 'YA' ? 'selected' : '' ?>>YA</option>
                                </select>
                            </div>

                            <!-- 🚀 ELEMEN BARU: Rumah/Bangunan Lain (Unit) & Nilai -->
                            <div class="col-md-4">
                                <label class="form-label">Rumah / Bangunan Lain (Unit) <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="rumah_lain" id="rumah_lain"
                                    class="form-control form-control-sm <?= $disabled ? '' : 'required-field' ?>"
                                    value="<?= esc($aset['rumah_lain'] ?? '') ?>" <?= $disabled ?> <?= $disabled ? '' : 'required' ?> placeholder="Ketik 0 jika tidak punya">
                                <?php if (!$disabled): ?>
                                    <div class="invalid-feedback">
                                        Wajib mengisi jumlah unit (isi 0 jika tidak memiliki).
                                    </div>
                                <?php endif; ?>

                                <!-- 🚀 ELEMEN DINAMIS: Nilai Rumah Lain -->
                                <div id="div_nilai_rumah" class="mt-2 p-2 bg-light border border-primary rounded" style="display: none;">
                                    <label class="form-label text-primary mb-1">Total Nilai Aset Rumah (Rp) <span class="text-danger">*</span></label>
                                    <input type="text" name="nilai_rumah_lain" id="nilai_rumah_lain" class="form-control form-control-sm rupiah border-primary" value="<?= esc($aset['nilai_rumah_lain'] ?? '') ?>" <?= $disabled ?> placeholder="Rp...">
                                    <div class="invalid-feedback">Wajib diisi karena unit > 0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <?php if ($editable): ?>
        <div class="text-end mt-4">
            <button type="button" id="btnSimpanAset" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    <?php else: ?>
        <div class="alert alert-warning small mt-3">
            <i class="fas fa-lock"></i> Anda tidak memiliki hak untuk mengubah data keluarga ini.
        </div>
    <?php endif; ?>
</div>

<script>
    $(function() {
        // ====================================================
        // 🚀 LOGIKA DINAMIS: NILAI ASET MUNCUL JIKA ANGKA > 0
        // ====================================================
        function toggleNilaiSawah() {
            const val = parseFloat($('#luas_sawah').val()) || 0;
            if (val > 0) {
                $('#div_nilai_sawah').slideDown();
                $('#nilai_sawah').prop('required', true);

                // 🚀 AUTO-SET MEMILIKI LAHAN = YA
                $('#memiliki_lahan').val('YA');
            } else {
                $('#div_nilai_sawah').slideUp();
                $('#nilai_sawah').prop('required', false).val('');

                // 🚀 AUTO-SET MEMILIKI LAHAN = TIDAK
                $('#memiliki_lahan').val('TIDAK');
            }
        }

        function toggleNilaiRumah() {
            const val = parseInt($('#rumah_lain').val()) || 0;
            if (val > 0) {
                $('#div_nilai_rumah').slideDown();
                $('#nilai_rumah_lain').prop('required', true);
            } else {
                $('#div_nilai_rumah').slideUp();
                $('#nilai_rumah_lain').prop('required', false).val('');
            }
        }

        // Jalankan saat diketik
        $('#luas_sawah').on('input change', toggleNilaiSawah);
        $('#rumah_lain').on('input change', toggleNilaiRumah);

        // Panggil saat halaman pertama kali diload (untuk prefill dari database)
        toggleNilaiSawah();
        toggleNilaiRumah();

        // ====================================================
        // 🚀 LOGIKA DINAMIS: NILAI KENDARAAN (MOTOR & MOBIL)
        // ====================================================
        function toggleNilaiMotor() {
            const val = parseInt($('#sepeda_motor').val()) || 0;
            if (val > 0) {
                $('#div_nilai_sepeda_motor').slideDown();
                $('#nilai_sepeda_motor').prop('required', true);
            } else {
                $('#div_nilai_sepeda_motor').slideUp();
                $('#nilai_sepeda_motor').prop('required', false).val('');
            }
        }

        function toggleNilaiMobil() {
            const val = parseInt($('#mobil').val()) || 0;
            if (val > 0) {
                $('#div_nilai_mobil').slideDown();
                $('#nilai_mobil').prop('required', true);
            } else {
                $('#div_nilai_mobil').slideUp();
                $('#nilai_mobil').prop('required', false).val('');
            }
        }

        // Jalankan saat diketik
        $('#sepeda_motor').on('input change', toggleNilaiMotor);
        $('#mobil').on('input change', toggleNilaiMobil);

        // Panggil saat halaman pertama kali diload
        toggleNilaiMotor();
        toggleNilaiMobil();

        $('#btnSimpanAset').on('click', function(e) {
            e.preventDefault();

            const form = $('#formAset')[0];

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                Swal.fire({
                    icon: 'warning',
                    title: 'Isian Belum Lengkap',
                    text: 'Silakan periksa kembali field yang bertanda bintang (*).',
                    width: '320px',
                    customClass: {
                        title: 'fs-5',
                        content: 'fs-6'
                    }
                });
                return;
            }

            const formData = $('#formAset').serialize();

            $.post('<?= base_url('pembaruan-keluarga/save-aset') ?>', formData, function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data aset berhasil disimpan.',
                        width: '320px',
                        customClass: {
                            title: 'fs-5',
                            content: 'fs-6'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message || 'Terjadi kesalahan.',
                        width: '320px',
                        customClass: {
                            title: 'fs-5',
                            content: 'fs-6'
                        }
                    });
                }
            }, 'json').fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Tidak dapat terhubung ke server.',
                    width: '320px',
                    customClass: {
                        title: 'fs-5',
                        content: 'fs-6'
                    }
                });
            });
        });
    });
</script>