<div class="modal fade" id="modalUsulanBansos" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Usulan Bansos Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formUsulanBansos">
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <?php if (!empty($wilayah_rw) && !empty($wilayah_rts)) : ?>
                            <span class="badge bg-primary px-4 py-2 fs-6 shadow-sm">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Wilayah Aktif: RW <strong><?= esc($wilayah_rw) ?></strong>,
                                RT <?= implode(', ', array_map('trim', $wilayah_rts)) ?>
                            </span>
                        <?php else : ?>
                            <span class="badge bg-secondary px-4 py-2 fs-6">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Wilayah belum ditentukan
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- 1️⃣ Pilih individu dari ART -->
                    <div class="form-group">
                        <label for="nik_peserta">Pilih Individu (Kepala Keluarga / Istri)</label>
                        <select id="nik_peserta" name="nik_peserta" class="form-control" style="width: 100%;"></select>
                        <input type="hidden" id="hidden_shdk" name="shdk">
                        <input type="hidden" id="hidden_nik" name="nik">
                    </div>

                    <!-- 2️⃣ Cek otomatis kategori desil -->
                    <div class="form-group">
                        <label for="kategori_desil">Kategori Desil</label>
                        <input type="text" id="kategori_desil" name="kategori_desil" class="form-control" readonly placeholder="Belum dicek">
                    </div>

                    <!-- 3️⃣ Pilih Program Bansos -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Program Bansos</label>
                        <select id="program_bansos" name="program_bansos" class="form-select" required>
                            <option value="">[ Pilih Program ]</option>
                            <?php foreach ($bansos as $row) { ?>
                                <option value="<?= $row['dbj_id'] ?>"> <?= $row['dbj_nama_bansos']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Catatan tambahan -->
                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan (Opsional)</label>
                        <textarea id="catatan" name="catatan" class="form-control" rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Usulan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- <script src="<?= base_url('assets/js/usulan_bansos.js'); ?>"></script> -->