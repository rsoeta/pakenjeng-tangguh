<!-- <div class="container-fluid"> -->

<form id="formEditKK">

    <!-- wajib untuk fetch() -->
    <input type="hidden" name="id_kk" value="<?= esc($kk['id_kk']) ?>">
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div class="row g-3">

        <div class="col-md-6">
            <label class="form-label fw-bold">Nomor KK</label>
            <input type="text" name="no_kk" class="form-control" value="<?= esc($kk['no_kk']) ?>" required>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Kepala Keluarga</label>
            <input type="text" name="kepala_keluarga" class="form-control" value="<?= esc($kk['kepala_keluarga']) ?>" required>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-bold">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2"><?= esc($kk['alamat']) ?></textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Program Bansos</label>
            <select name="program_bansos" class="form-select">
                <option value="">Tidak Ada</option>
                <option value="2" <?= $kk['program_bansos'] == '2' ? 'selected' : '' ?>>BPNT</option>
                <option value="1" <?= $kk['program_bansos'] == '1' ? 'selected' : '' ?>>PKH</option>
                <option value="5" <?= $kk['program_bansos'] == '5' ? 'selected' : '' ?>>PBI</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Kategori Adat</label>
            <select name="kategori_adat" class="form-select">
                <option value="Tidak" <?= $kk['kategori_adat'] == 'Tidak' ? 'selected' : '' ?>>Tidak</option>
                <option value="Ya" <?= $kk['kategori_adat'] == 'Ya' ? 'selected' : '' ?>>Ya</option>
            </select>
        </div>

        <div class="col-md-12">
            <p class="mt-2 text-muted small">
                Jumlah ART terdata: <b><?= $arts_count ?></b>
            </p>
        </div>

    </div>

    <div class="mt-4 text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary px-4">
            Simpan Perubahan
        </button>
    </div>

</form>

<!-- </div> -->