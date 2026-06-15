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
                        $asetBergerak = [
                            'Tabung Gas 5,5 kg atau lebih' => 'tabung_gas',
                            'Lemari es / Kulkas' => 'kulkas',
                            'Air Conditioner (AC)' => 'ac',
                            'Pemanas Air (Water Heater)' => 'water_heater',
                            'Telepon Rumah (PSTN)' => 'telepon_rumah',
                            'Televisi Layar Datar (min. 30 inci)' => 'tv_lcd',
                            'Emas / Perhiasan (min. 10 gram)' => 'emas',
                            'Komputer / Laptop / Tablet' => 'laptop',
                            'Sepeda Motor' => 'sepeda_motor',
                            'Sepeda' => 'sepeda',
                            'Mobil' => 'mobil',
                            'Perahu' => 'perahu',
                            'Kapal / Perahu Motor' => 'kapal_motor',
                            'Smartphone' => 'smartphone'
                        ];
                        ?>

                        <div class="row">
                            <?php foreach ($asetBergerak as $label => $name): ?>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label"><?= esc($label) ?></label>
                                    <input type="number" min="0" class="form-control form-control-sm"
                                        name="<?= $name ?>" value="<?= esc($aset[$name] ?? 0) ?>" <?= $disabled ?>>
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
                            <div class="col-md-4">
                                <label class="form-label">Luas Sawah / Kebun <span class="text-danger">*</span></label>
                                <select name="luas_sawah"
                                    class="form-select form-select-sm <?= $disabled ? '' : 'required-field' ?>"
                                    <?= $disabled ?> <?= $disabled ? '' : 'required' ?>>
                                    <option value="">Pilih</option>
                                    <option value="TIDAK MEMILIKI" <?= ($aset['luas_sawah'] ?? '') === 'TIDAK MEMILIKI' ? 'selected' : '' ?>>TIDAK MEMILIKI</option>
                                    <option value="KURANG DARI 1000 M2" <?= ($aset['luas_sawah'] ?? '') === 'KURANG DARI 1000 M2' ? 'selected' : '' ?>>KURANG DARI 1000 M2</option>
                                    <option value="1000-5000 M2" <?= ($aset['luas_sawah'] ?? '') === '1000-5000 M2' ? 'selected' : '' ?>>1000-5000 M2</option>
                                    <option value="5000-10000 M2" <?= ($aset['luas_sawah'] ?? '') === '5000-10000 M2' ? 'selected' : '' ?>>5000-10000 M2</option>
                                    <option value="LEBIH DARI 10000 M2" <?= ($aset['luas_sawah'] ?? '') === 'LEBIH DARI 10000 M2' ? 'selected' : '' ?>>LEBIH DARI 10000 M2</option>
                                </select>
                                <?php if (!$disabled): ?>
                                    <div class="invalid-feedback">
                                        Wajib memilih luas sawah / kebun.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Memiliki Lahan (selain yang ditempati)</label>
                                <select name="memiliki_lahan"
                                    id="memiliki_lahan"
                                    class="form-select form-select-sm bg-light"
                                    readonly
                                    tabindex="-1"
                                    data-auto="true">
                                    <option value="">Pilih</option>
                                    <option value="YA" <?= ($aset['memiliki_lahan'] ?? '') === 'YA' ? 'selected' : '' ?>>YA</option>
                                    <option value="TIDAK" <?= ($aset['memiliki_lahan'] ?? '') === 'TIDAK' ? 'selected' : '' ?>>TIDAK</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Rumah / Bangunan Ditempati Lain <span class="text-danger">*</span></label>
                                <select name="rumah_lain"
                                    class="form-select form-select-sm <?= $disabled ? '' : 'required-field' ?>"
                                    <?= $disabled ?> <?= $disabled ? '' : 'required' ?>>
                                    <option value="">Pilih</option>
                                    <option value="YA" <?= ($aset['rumah_lain'] ?? '') === 'YA' ? 'selected' : '' ?>>YA</option>
                                    <option value="TIDAK" <?= ($aset['rumah_lain'] ?? '') === 'TIDAK' ? 'selected' : '' ?>>TIDAK</option>
                                </select>
                                <?php if (!$disabled): ?>
                                    <div class="invalid-feedback">
                                        Wajib memilih kepemilikan rumah lain.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <?php if ($editable): ?>
        <div class="text-end mt-4">
            <button type="button" id="btnSimpanAset" class="btn btn-success">
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
        $('#btnSimpanAset').on('click', function(e) {
            e.preventDefault(); // Mencegah form reload

            const form = $('#formAset')[0];

            // 🚀 BUG FIX: Validasi kelengkapan form sebelum mengirim data ke server
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                Swal.fire({
                    icon: 'warning',
                    title: 'Isian Belum Lengkap',
                    text: 'Silakan periksa kembali field yang bertanda bintang (*).',
                    width: '320px', // Perkecil untuk kenyamanan mobile
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
                        width: '320px', // Perkecil untuk kenyamanan mobile
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
                        width: '320px', // Perkecil untuk kenyamanan mobile
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
                    width: '320px', // Perkecil untuk kenyamanan mobile
                    customClass: {
                        title: 'fs-5',
                        content: 'fs-6'
                    }
                });
            });
        });
    });
</script>