<!-- Modal -->
<?php
// $level = session()->get('role_id');
?>
<!-- Modal -->
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="modaleditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaleditLabel"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="form-group row">
                        <div class="col-12 col-sm-12">
                            <label class="col-4 col-sm-4 col-form-label" for="foto_kpm">Foto PM</label>
                            <img src="<?= FOTO_DOKUMEN('KPM_BNT' . $nomor_nik . 'A.jpg', 'foto-kpm') ?>" alt="" style="width: 200px; height: 255px; border-radius: 10px;">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 col-sm-12">
                            <label class="col-4 col-sm-4 col-form-label" for="foto_rumah">Foto Rumah</label>
                            <img src="<?= FOTO_DOKUMEN('BNT' . $nomor_nik . '1.jpg', 'foto-rumah') ?>" alt="" style="width: 200px; height: 255px; border-radius: 10px;">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>