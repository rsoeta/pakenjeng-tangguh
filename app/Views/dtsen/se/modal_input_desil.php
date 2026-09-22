<div class="modal fade" id="modalInputDesil" tabindex="-1" aria-labelledby="modalInputDesilLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-balance-scale"></i> Update Kategori Desil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formInputDesil" method="POST" action="<?= site_url('dtsen-se/update-desil'); ?>">
                <div class="modal-body">
                    <input type="hidden" name="id_kk" id="modal_id_kk">

                    <div class="mb-2">
                        <label class="form-label fw-bold">No. KK</label>
                        <input type="text" class="form-control" id="modal_no_kk" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Kepala Keluarga</label>
                        <input type="text" class="form-control" id="modal_kepala_keluarga" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Alamat</label>
                        <input type="text" class="form-control" id="modal_alamat" readonly>
                    </div>

                    <!-- 🚀 TAMBAHAN: Opsi Periode Agar Tidak Tertimpa Otomatis -->
                    <div class="row g-2 bg-light p-2 rounded border">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Tahun Berlaku</label>
                            <select name="tahun_berlaku" id="tahun_berlaku" class="form-select form-select-sm" required>
                                <?php
                                $currentYear = date('Y');
                                for ($year = $currentYear; $year >= 2025; $year--):
                                ?>
                                    <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>><?= $year ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Periode (TW)</label>
                            <!-- Input number mendukung desimal (1 s/d 4.9) -->
                            <input type="number" step="0.1" min="1" max="4.9" name="triwulan_berlaku" id="triwulan_berlaku" class="form-control form-control-sm" value="<?= ceil(date('n') / 3) ?>" required>
                            <small class="text-muted" style="font-size: 10px;">Bisa desimal (misal 3.1)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Desil</label>
                        <select class="form-select" name="kategori_desil" id="kategori_desil" required>
                            <option value="">[ Pilih Desil ]</option>
                            <?php for ($i = 0; $i <= 10; $i++) : ?>
                                <option value="<?= $i ?>">Desil <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>