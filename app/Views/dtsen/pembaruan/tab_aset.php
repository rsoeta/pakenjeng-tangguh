<?php
// =============================================
// Tab: Kepemilikan Aset (Pemutakhiran Keluarga)
// =============================================

// Hak akses
$roleId   = $user['role_id'] ?? 99;
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
            <!-- ================= ASET BERGERAK ================= -->
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

            <!-- ================= TERNAK ================= -->
            <div class="col-12 col-lg-6">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        Jumlah Ternak yang Dimiliki:
                    </div>
                    <div class="card-body">
                        <?php
                        $ternak = [
                            'Sapi' => 'sapi',
                            'Kerbau' => 'kerbau',
                            'Kuda' => 'kuda',
                            'Kambing / Domba' => 'kambing',
                            'Babi' => 'babi'
                        ];
                        ?>
                        <div class="row">
                            <?php foreach ($ternak as $label => $name): ?>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label"><?= esc("Jumlah $label") ?></label>
                                    <input type="number" min="0" class="form-control form-control-sm"
                                        name="<?= $name ?>" value="<?= esc($aset[$name] ?? 0) ?>" <?= $disabled ?>>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= ASET TIDAK BERGERAK ================= -->
            <div class="col-12 mt-3">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        Jumlah Aset Tidak Bergerak yang Dimiliki:
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Luas Sawah / Kebun (m²)</label>
                                <select name="luas_sawah" class="form-select form-select-sm" <?= $disabled ?>>
                                    <option value="">Pilih</option>
                                    <option <?= ($aset['luas_sawah'] ?? '') === 'TIDAK MEMILIKI' ? 'selected' : '' ?>>TIDAK MEMILIKI</option>
                                    <option <?= ($aset['luas_sawah'] ?? '') === 'KURANG DARI 100 M2' ? 'selected' : '' ?>>KURANG DARI 100 M2</option>
                                    <option <?= ($aset['luas_sawah'] ?? '') === '100-500 M2' ? 'selected' : '' ?>>100-500 M2</option>
                                    <option <?= ($aset['luas_sawah'] ?? '') === 'LEBIH DARI 500 M2' ? 'selected' : '' ?>>LEBIH DARI 500 M2</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Memiliki Lahan (selain yang ditempati)</label>
                                <select name="memiliki_lahan" class="form-select form-select-sm" <?= $disabled ?>>
                                    <option value="">Pilih</option>
                                    <option value="YA" <?= ($aset['memiliki_lahan'] ?? '') === 'YA' ? 'selected' : '' ?>>YA</option>
                                    <option value="TIDAK" <?= ($aset['memiliki_lahan'] ?? '') === 'TIDAK' ? 'selected' : '' ?>>TIDAK</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Rumah / Bangunan Ditempati Lain</label>
                                <select name="rumah_lain" class="form-select form-select-sm" <?= $disabled ?>>
                                    <option value="">Pilih</option>
                                    <option value="YA" <?= ($aset['rumah_lain'] ?? '') === 'YA' ? 'selected' : '' ?>>YA</option>
                                    <option value="TIDAK" <?= ($aset['rumah_lain'] ?? '') === 'TIDAK' ? 'selected' : '' ?>>TIDAK</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php if ($editable): ?>
        <div class="text-end mt-4">
            <button id="btnSimpanAset" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    <?php else: ?>
        <div class="alert alert-warning small mt-3">
            <i class="fas fa-lock"></i> Anda tidak memiliki hak untuk mengubah data keluarga ini.
        </div>
    <?php endif; ?>
</div>

<!-- ================ SCRIPT ================= -->
<script>
    $(function() {
        $('#btnSimpanAset').on('click', function() {
            const formData = $('#formAset').serialize();
            $.post('<?= base_url('pembaruan-keluarga/save-aset') ?>', formData, function(res) {
                if (res.status === 'success') {
                    Swal.fire('Berhasil!', 'Data aset berhasil disimpan.', 'success');
                } else {
                    Swal.fire('Gagal!', res.message || 'Terjadi kesalahan.', 'error');
                }
            }, 'json').fail(() => {
                Swal.fire('Gagal!', 'Tidak dapat terhubung ke server.', 'error');
            });
        });
    });
</script>