<!-- 🚀 KOREKSI: Hapus atribut tabindex="-1" dan tambahkan data-bs-focus="false" -->
<div class="modal fade" id="modalInputDesil" aria-labelledby="modalInputDesilLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-balance-scale"></i> Update Kategori Desil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formInputDesil" method="POST" action="<?= site_url('dtsen-se/update-desil'); ?>">
                <div class="modal-body bg-light">
                    <input type="hidden" name="id_kk" id="modal_id_kk">

                    <!-- Kartu Informasi Dasar -->
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body py-2 px-3">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-0">No. KK</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-0 fw-bold" id="modal_no_kk" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold mb-0">Kepala Keluarga</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-0 fw-bold" id="modal_kepala_keluarga" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold mb-0">Alamat</label>
                                    <input type="text" class="form-control form-control-sm bg-white border-0" id="modal_alamat" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tahun -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun Berlaku</label>
                        <select name="tahun_berlaku" id="tahun_berlaku" class="form-select" required>
                            <?php
                            $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 2025; $year--):
                            ?>
                                <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>><?= $year ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Triwulan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Periode Triwulan</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold text-primary">TW</span>
                            <input type="number" class="form-control" name="triwulan_berlaku" id="triwulan_berlaku" step="0.1" min="1" max="4.9" value="<?= ceil(date('n') / 3) ?>" placeholder="Contoh: 3 atau 3.1" required>
                        </div>
                        <small class="text-muted fst-italic">Digunakan untuk penandaan pada grafik (misal: 1, 2, atau 3.1).</small>
                    </div>

                    <!-- 🚀 Desil (Berubah menjadi Tombol Pills) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Desil</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                <?php $warna = ($i == 0) ? 'secondary' : (($i <= 3) ? 'success' : (($i <= 5) ? 'warning' : 'danger')); ?>
                                <!-- 🚀 Name harus tetap "kategori_desil" agar backend tidak error -->
                                <input type="radio" class="btn-check" name="kategori_desil" id="update_desil<?= $i ?>" value="<?= $i ?>" required>
                                <label class="btn btn-outline-<?= $warna ?> rounded-pill px-3 fw-bold" for="update_desil<?= $i ?>">Desil <?= $i ?></label>
                            <?php endfor; ?>
                        </div>
                    </div>

                </div>

                <!-- 🚀 TAMBAHKAN d-flex justify-content-between -->
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-3"><i class="fas fa-save me-1"></i> Simpan Update</button>
                </div>
            </form>
        </div>
    </div>
</div>