<!-- app/Views/dtsen/pembaruan/tab_keluarga.php -->
<?php
$roleId = $user['role_id'] ?? 99;
$editable = ($roleId <= 4); // Petugas Pendata (4) & Operator Desa (<=3) bisa edit
$disabled = $editable ? '' : 'disabled';
// $perumahan = $payload['perumahan'] ?? [];
?>
<?= $this->include('dtsen/pembaruan/_prefill_helper.php') ?>

<div class="p-3">
    <form id="formDataKeluarga" class="needs-validation" novalidate>
        <input type="hidden" id="id_kk" name="id_kk" value="<?= $id_kk ?>">
        <input type="hidden" id="sumber" name="sumber" value="<?= $sumber ?>">

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="keluarga_no_kk" class="form-label fw-bold">Nomor Kartu Keluarga</label>
                <input type="text" class="form-control" id="keluarga_no_kk" name="no_kk"
                    value="<?= esc($perumahan['no_kk'] ?? '') ?>" <?= $disabled ?>>
            </div>
            <div class="col-md-6">
                <label for="kepala_keluarga" class="form-label fw-bold">Kepala Keluarga</label>
                <input type="text" class="form-control" id="kepala_keluarga" name="kepala_keluarga"
                    value="<?= esc($perumahan['kepala_keluarga'] ?? '') ?>" <?= $disabled ?>>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-8">
                <label for="alamat" class="form-label fw-bold">Alamat Lengkap</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="2" <?= $disabled ?>><?= esc($perumahan['alamat'] ?? '') ?></textarea>
            </div>
            <div class="col-md-2">
                <label for="rw" class="form-label fw-bold">RW</label>
                <input type="text" class="form-control" id="rw" name="rw"
                    value="<?= esc($perumahan['rw'] ?? '') ?>" <?= $disabled ?>>
            </div>
            <div class="col-md-2">
                <label for="rt" class="form-label fw-bold">RT</label>
                <input type="text" class="form-control" id="rt" name="rt"
                    value="<?= esc($perumahan['rt'] ?? '') ?>" <?= $disabled ?>>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="kategori_adat" class="form-label fw-bold">Keluarga Adat?</label>
                <select class="form-select" id="kategori_adat" name="kategori_adat" <?= $disabled ?>>
                    <option value="Tidak" <?= ($perumahan['kategori_adat'] ?? 'Tidak') == 'Tidak' ? 'selected' : '' ?>>Tidak</option>
                    <option value="Ya" <?= ($perumahan['kategori_adat'] ?? 'Tidak') == 'Ya' ? 'selected' : '' ?>>Ya</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="nama_suku" class="form-label fw-bold">Nama Suku</label>
                <input type="text" class="form-control" id="nama_suku" name="nama_suku"
                    value="<?= esc($perumahan['nama_suku'] ?? '') ?>" <?= $disabled ?>>
            </div>
        </div>

        <?php if ($editable): ?>
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-warning small mt-3">
                <i class="fas fa-lock"></i> Anda tidak memiliki hak untuk mengubah data keluarga ini.
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- ============================== -->
<script>
    $(document).ready(function() {

    });
</script>