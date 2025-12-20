<div class="modal fade" id="modalInputDesil" tabindex="-1" aria-labelledby="modalInputDesilLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-balance-scale"></i> Input Kategori Desil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formInputDesil" method="POST" action="<?= site_url('dtsen-se/update-desil'); ?>">
                <div class="modal-body">
                    <input type="hidden" name="id_kk" id="id_kk">

                    <div class="mb-2">
                        <label class="form-label fw-bold">No. KK</label>
                        <input type="text" class="form-control" id="no_kk" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Kepala Keluarga</label>
                        <input type="text" class="form-control" id="nama_kepala" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Alamat</label>
                        <input type="text" class="form-control" id="alamat" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori Desil</label>
                        <select class="form-select" name="kategori_desil" id="kategori_desil">
                            <option value="">[ Pilih Desil ]</option>
                            <?php for ($i = 1; $i <= 10; $i++) : ?>
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