<?php
$roleId = $user['role_id'] ?? 99;
$editable = ($roleId <= 4); // Petugas & Operator bisa edit
$disabled = $editable ? '' : 'disabled';
$foto = $payload['foto'] ?? [];
$sanitasi = $perumahan['sanitasi'] ?? ($payload['perumahan']['sanitasi'] ?? []);
$fasilitasBab = $sanitasi['fasilitas_bab'] ?? '';
?>

<style>
    #map {
        border: 2px solid #ddd;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .camera-guide-overlay {
        position: fixed;
        inset: 0;
        z-index: 1085;
        background: #070a0f;
        color: #fff;
        display: flex;
        flex-direction: column;
        user-select: none;
        touch-action: manipulation;
    }

    .camera-guide-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 10px 14px;
        background: rgba(12, 17, 24, 0.94);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 3;
    }

    .camera-stage {
        flex: 1;
        min-height: 0;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
        overflow: hidden;
    }

    .camera-viewport {
        position: relative;
        overflow: hidden;
        background: #000;
        width: 100%;
        height: 100%;
    }

    #cameraVideoStream {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center center;
    }

    .camera-framing-layer {
        position: absolute;
        inset: 0;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .camera-guide-box {
        position: relative;
        border-radius: 10px;
        box-shadow: 0 0 0 9999px var(--guide-mask, rgba(0, 0, 0, 0.45));
        transition: width 0.16s ease, height 0.16s ease;
    }

    .camera-guide-box-inner {
        position: absolute;
        inset: 0;
        border: 1.5px dashed rgba(255, 255, 255, 0.48);
        border-radius: 10px;
        overflow: hidden;
    }

    .camera-guide-box-inner svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .guide-zone-hint {
        position: absolute;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: rgba(255, 255, 255, 0.85);
        background: rgba(10, 14, 20, 0.55);
        padding: 2px 8px;
        border-radius: 4px;
        pointer-events: none;
        white-space: nowrap;
    }

    .camera-guide-label {
        position: absolute;
        top: -11px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(10, 14, 20, 0.92);
        border: 1px solid var(--guide-accent, #00e5ff);
        color: var(--guide-accent, #00e5ff);
        font-size: 0.66rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 1px 10px;
        border-radius: 999px;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        z-index: 2;
    }

    .guide-corner {
        position: absolute;
        width: 26px;
        height: 26px;
        border-color: var(--guide-accent, #00e5ff);
        border-style: solid;
        pointer-events: none;
        z-index: 1;
    }

    .guide-corner.tl {
        top: -2px;
        left: -2px;
        border-width: 4px 0 0 4px;
        border-top-left-radius: 10px;
    }

    .guide-corner.tr {
        top: -2px;
        right: -2px;
        border-width: 4px 4px 0 0;
        border-top-right-radius: 10px;
    }

    .guide-corner.bl {
        bottom: -2px;
        left: -2px;
        border-width: 0 0 4px 4px;
        border-bottom-left-radius: 10px;
    }

    .guide-corner.br {
        bottom: -2px;
        right: -2px;
        border-width: 0 4px 4px 0;
        border-bottom-right-radius: 10px;
    }

    .camera-watermark-safezone {
        position: absolute;
        left: 14px;
        bottom: 18px;
        background: rgba(0, 0, 0, 0.62);
        border: 1px dashed rgba(255, 255, 255, 0.55);
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 0.64rem;
        color: rgba(255, 255, 255, 0.88);
        pointer-events: none;
        z-index: 3;
    }

    .camera-zoom-bar {
        position: absolute;
        right: 12px;
        bottom: 18px;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px;
        border-radius: 999px;
        background: rgba(10, 14, 20, 0.68);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        z-index: 4;
        pointer-events: auto;
    }

    .btn-cam-zoom {
        border: 0;
        border-radius: 999px;
        padding: 2px 9px;
        font-size: 0.72rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.88);
        background: transparent;
        line-height: 1.4;
    }

    .btn-cam-zoom.active {
        background: var(--guide-accent, #00e5ff);
        color: #071018;
    }

    .camera-guidance-bar {
        background: rgba(10, 15, 22, 0.94);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        color: #e2e8f0;
        padding: 5px 12px;
        font-size: 0.74rem;
        line-height: 1.3;
        text-align: center;
        z-index: 3;
    }

    .camera-shutter-flash {
        position: absolute;
        inset: 0;
        background: #fff;
        opacity: 0;
        pointer-events: none;
        z-index: 6;
        transition: opacity 0.14s ease-out;
    }

    .camera-shutter-flash.flash {
        opacity: 0.85;
    }

    .camera-preview-layer {
        position: absolute;
        inset: 0;
        background: #05080d;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        z-index: 5;
    }

    .camera-preview-layer img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 8px 28px rgba(0, 0, 0, 0.6);
    }

    .camera-controls-bar {
        padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
        background: rgba(12, 17, 24, 0.96);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 3;
    }

    .btn-shutter {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        border: 4px solid #fff;
        background: rgba(255, 255, 255, 0.2);
        padding: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.12s ease;
    }

    .btn-shutter:active {
        transform: scale(0.92);
    }

    .btn-shutter-inner {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #fff;
        display: block;
    }

    .camera-loading-state {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(7, 10, 15, 0.9);
        z-index: 4;
    }

    .swal2-container {
        z-index: 1100 !important;
    }
</style>

<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">📸 Upload Foto Rumah & KTP</h5>

        <!-- 🚀 TOMBOL BULK DOWNLOAD (UNDUH SEMUA FOTO) -->
        <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="btnBulkDownload">
            <i class="fas fa-file-archive me-1"></i> Unduh Semua Foto
        </button>
    </div>

    <form id="formFoto" enctype="multipart/form-data">
        <input type="hidden" name="dtsen_usulan_id" value="<?= esc($payload['id'] ?? $usulan['id'] ?? '') ?>">
        <input type="hidden" name="no_kk" value="<?= esc($payload['no_kk'] ?? $perumahan['no_kk'] ?? '') ?>">
        <input type="hidden" name="kepala_keluarga" value="<?= esc($payload['kepala_keluarga'] ?? $perumahan['kepala_keluarga'] ?? '') ?>">
        <input type="hidden" name="fasilitas_bab" id="foto_fasilitas_bab" value="<?= esc($fasilitasBab) ?>">

        <!-- 🚀 4 KOLOM FOTO (2 BARIS DI MOBILE, 1 BARIS DI DESKTOP) -->
        <div class="row g-2">
            <!-- Foto KTP/KK -->
            <div class="col-6 col-md-3 mb-3 text-center position-relative">
                <label class="fw-semibold d-block small">Foto KTP / KK <span class="text-danger">*</span></label>
                <div class="position-relative d-inline-block w-100">
                    <img src="<?= base_url($foto['ktp_kk'] ?? 'data/usulan/foto_identitas/noimage.png') ?>"
                        class="img-fluid rounded border mb-2 img-download bg-light" id="previewKtp" data-saved-src="<?= esc($foto['ktp_kk'] ?? '') ?>" style="aspect-ratio: 3/4; cursor: pointer; object-fit: contain; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_identitas/noimage.png') ?>'">

                    <button type="button" class="btn btn-sm btn-dark position-absolute shadow-sm btn-download-foto" data-target="previewKtp" style="bottom: 15px; right: 5px; opacity: 0.85;" title="Unduh Foto KTP/KK">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <?php if ($editable): ?>
                    <input type="file" name="foto_ktp" id="fotoKtp" class="d-none" accept="image/*" capture="environment">
                    <button type="button" class="btn btn-sm btn-primary w-100 shadow-sm btn-open-camera" data-camera-field="ktp" title="Buka Kamera dengan Bingkai Panduan KTP/KK">
                        <i class="fas fa-camera me-1"></i> Ambil Foto
                    </button>
                <?php endif; ?>
            </div>

            <!-- Foto Rumah Depan -->
            <div class="col-6 col-md-3 mb-3 text-center position-relative">
                <label class="fw-semibold d-block small">Tampak Depan <span class="text-danger">*</span></label>
                <div class="position-relative d-inline-block w-100">
                    <img src="<?= base_url($foto['depan'] ?? 'data/usulan/foto_rumah/noimage.png') ?>"
                        class="img-fluid rounded border mb-2 img-download" id="previewDepan" data-saved-src="<?= esc($foto['depan'] ?? '') ?>" style="aspect-ratio: 3/4; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_rumah/noimage.png') ?>'">

                    <button type="button" class="btn btn-sm btn-dark position-absolute shadow-sm btn-download-foto" data-target="previewDepan" style="bottom: 15px; right: 5px; opacity: 0.85;" title="Unduh Foto Depan">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <?php if ($editable): ?>
                    <input type="file" name="foto_depan" id="fotoDepan" class="d-none" accept="image/*" capture="environment">
                    <button type="button" class="btn btn-sm btn-primary w-100 shadow-sm btn-open-camera" data-camera-field="depan" title="Buka Kamera dengan Bingkai Panduan Rumah Depan">
                        <i class="fas fa-camera me-1"></i> Ambil Foto
                    </button>
                <?php endif; ?>
            </div>

            <!-- Foto Rumah Dalam -->
            <div class="col-6 col-md-3 mb-3 text-center position-relative">
                <label class="fw-semibold d-block small">Ruang Tamu / Dalam <span class="text-danger">*</span></label>
                <div class="position-relative d-inline-block w-100">
                    <img src="<?= base_url($foto['dalam'] ?? 'data/usulan/foto_rumah_dalam/noimage.png') ?>"
                        class="img-fluid rounded border mb-2 img-download" id="previewDalam" data-saved-src="<?= esc($foto['dalam'] ?? '') ?>" style="aspect-ratio: 3/4; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_rumah_dalam/noimage.png') ?>'">

                    <button type="button" class="btn btn-sm btn-dark position-absolute shadow-sm btn-download-foto" data-target="previewDalam" style="bottom: 15px; right: 5px; opacity: 0.85;" title="Unduh Foto Ruang Tamu">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <?php if ($editable): ?>
                    <input type="file" name="foto_dalam" id="fotoDalam" class="d-none" accept="image/*" capture="environment">
                    <button type="button" class="btn btn-sm btn-primary w-100 shadow-sm btn-open-camera" data-camera-field="dalam" title="Buka Kamera dengan Bingkai Panduan Ruang Dalam">
                        <i class="fas fa-camera me-1"></i> Ambil Foto
                    </button>
                <?php endif; ?>
            </div>

            <!-- Foto Kamar Mandi -->
            <div class="col-6 col-md-3 mb-3 text-center position-relative">
                <label class="fw-semibold d-block small">Kamar Mandi / WC <span class="text-danger">*</span></label>
                <div class="position-relative d-inline-block w-100">
                    <?php
                    $savedWc = $foto['kamar_mandi'] ?? ($foto['mck'] ?? '');
                    $isTidakAdaBabInit = ($fasilitasBab === 'Tidak ada fasilitas');
                    $wcInitImg = 'data/usulan/foto_kamar_mandi/noimage.png';
                    if (!empty($savedWc) && !str_contains($savedWc, 'noimage.png')) {
                        $wcInitImg = $savedWc;
                    } elseif ($isTidakAdaBabInit) {
                        $wcInitImg = 'assets/images/image_not_available.jpg';
                    }
                    ?>
                    <img src="<?= base_url($wcInitImg) ?>"
                        class="img-fluid rounded border mb-2 img-download" id="previewKamarMandi" data-saved-src="<?= esc($savedWc) ?>" style="aspect-ratio: 3/4; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_kamar_mandi/noimage.png') ?>'">

                    <button type="button" class="btn btn-sm btn-dark position-absolute shadow-sm btn-download-foto" data-target="previewKamarMandi" style="bottom: 15px; right: 5px; opacity: 0.85;" title="Unduh Foto Kamar Mandi">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <?php if ($editable): ?>
                    <input type="file" name="foto_kamar_mandi" id="fotoKamarMandi" class="d-none" accept="image/*" capture="environment">
                    <button type="button" class="btn btn-sm btn-primary w-100 shadow-sm btn-open-camera" data-camera-field="wc" title="Buka Kamera dengan Bingkai Panduan Kamar Mandi / WC">
                        <i class="fas fa-camera me-1"></i> Ambil Foto
                    </button>
                <?php endif; ?>
                <div id="wcInfoBadge" class="mt-1 text-center"></div>
            </div>
        </div>

        <?php if ($editable): ?>
            <?php $isUsulanEditable = $isUsulanEditable ?? ($editable && in_array(strtolower(trim($usulan['status'] ?? '')), ['draft', 'submitted'])); ?>
            <div class="mt-3 text-end">
                <button type="submit" id="btnUploadFoto" class="btn btn-success rounded-pill px-4 shadow-sm <?= $isUsulanEditable ? '' : 'd-none' ?>">
                    <i class="fas fa-upload me-1"></i> Upload Semua Foto
                </button>
                <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm btn-terkunci <?= $isUsulanEditable ? 'd-none' : '' ?>">
                    <i class="fas fa-lock me-1"></i> Terkunci
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php if ($editable): ?>
    <!-- 📸 OVERLAY KAMERA TERPADU DENGAN BINGKAI PANDUAN PER FIELD -->
    <div id="cameraGuideOverlay" class="camera-guide-overlay d-none" role="dialog" aria-modal="true" aria-labelledby="cameraOverlayTitle">
        <div class="camera-guide-header">
            <div class="d-flex align-items-center gap-2 text-truncate">
                <span id="cameraOverlayBadge" class="badge bg-info text-dark">KTP / KK</span>
                <div class="text-truncate">
                    <div id="cameraOverlayTitle" class="fw-bold small lh-sm text-truncate">Foto KTP / KK</div>
                    <div id="cameraOverlaySub" class="text-white-50" style="font-size: 0.68rem;">Panduan Bingkai Aktif</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                <button type="button" class="btn btn-sm btn-outline-light" id="btnCamToggleGuide" title="Tampilkan / Sembunyikan Panduan">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-light" id="btnCamRotateFrame" title="Putar Orientasi Bingkai">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning d-none" id="btnCamTorch" title="Nyalakan / Matikan Lampu Flash">
                    <i class="fas fa-bolt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger ms-1" id="btnCamClose" title="Tutup Kamera">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div id="cameraGuidanceBanner" class="camera-guidance-bar"></div>

        <div id="cameraStage" class="camera-stage">
            <div id="cameraLoadingState" class="camera-loading-state">
                <div class="spinner-border text-info mb-2" role="status"></div>
                <small class="text-white-50">Membuka kamera...</small>
            </div>

            <div id="cameraViewport" class="camera-viewport">
                <video id="cameraVideoStream" autoplay playsinline muted></video>

                <div id="cameraFramingLayer" class="camera-framing-layer">
                    <div id="cameraGuideBox" class="camera-guide-box">
                        <span id="cameraGuideLabel" class="camera-guide-label">BINGKAI PANDUAN</span>
                        <span class="guide-corner tl"></span>
                        <span class="guide-corner tr"></span>
                        <span class="guide-corner bl"></span>
                        <span class="guide-corner br"></span>
                        <div id="cameraGuideBoxInner" class="camera-guide-box-inner"></div>
                    </div>
                    <div id="cameraWatermarkHint" class="camera-watermark-safezone d-none">
                        <i class="fas fa-shield-alt me-1"></i>Area Watermark SINDEN
                    </div>
                </div>

                <div id="cameraZoomBar" class="camera-zoom-bar d-none" role="group" aria-label="Pilihan Zoom Kamera"></div>

                <div id="cameraShutterFlash" class="camera-shutter-flash"></div>
            </div>

            <div id="cameraPreviewLayer" class="camera-preview-layer d-none">
                <img id="cameraCapturedPreview" src="" alt="Pratinjau Hasil Foto">
            </div>

            <canvas id="cameraCaptureCanvas" class="d-none"></canvas>
        </div>

        <div class="camera-controls-bar">
            <div id="camControlsLive" class="position-relative d-flex align-items-center justify-content-center">
                <button type="button" class="btn-shutter" id="btnCamCapture" title="Ambil Foto" disabled>
                    <span class="btn-shutter-inner"></span>
                </button>

                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 position-absolute end-0" id="btnCamSwitch" title="Ganti Kamera Depan / Belakang">
                    <i class="fas fa-sync-alt me-1"></i> Balik
                </button>
            </div>

            <div id="camControlsReview" class="d-none d-flex align-items-center justify-content-center gap-3">
                <button type="button" class="btn btn-outline-light rounded-pill px-4" id="btnCamRetake">
                    <i class="fas fa-redo me-1"></i> Foto Ulang
                </button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" id="btnCamUsePhoto">
                    <i class="fas fa-check me-1"></i> Gunakan Foto
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ============================== -->
<!-- 📡 JS SAVE FOTO & PREVIEW -->
<!-- ============================== -->
<script>
    // ==========================================
    // 🔄 SINKRONISASI STATUS FOTO WC & FASILITAS BAB
    // ==========================================
    function syncWcPlaceholder() {
        const fasilitasBab = $('input[name="fasilitas_bab"]:checked').val() || $('#foto_fasilitas_bab').val() || '';
        const isTidakAdaBab = (fasilitasBab === 'Tidak ada fasilitas');
        const fileWc = document.getElementById('fotoKamarMandi')?.files?.[0];
        const $prevWc = $('#previewKamarMandi');
        const savedWc = ($prevWc.attr('data-saved-src') || '').toString();
        const hasSavedRealWc = (savedWc !== '' && !savedWc.includes('noimage.png') && !savedWc.includes('image_not_available.jpg'));

        if (isTidakAdaBab) {
            if (!fileWc && !hasSavedRealWc) {
                $prevWc.attr('src', window.baseUrl + '/assets/images/image_not_available.jpg');
                $('#wcInfoBadge').html('<span class="badge bg-secondary text-wrap shadow-sm" style="font-size: 0.7rem;"><i class="fas fa-info-circle me-1"></i>Tidak ada fasilitas BAB (Placeholder)</span>').show();
            } else {
                $('#wcInfoBadge').html('<span class="badge bg-success text-wrap shadow-sm" style="font-size: 0.7rem;"><i class="fas fa-check-circle me-1"></i>Foto WC terisi</span>').show();
            }
        } else {
            if (!fileWc && !hasSavedRealWc) {
                const curSrc = $prevWc.attr('src') || '';
                if (curSrc.includes('image_not_available.jpg')) {
                    $prevWc.attr('src', window.baseUrl + '/data/usulan/foto_kamar_mandi/noimage.png');
                }
                $('#wcInfoBadge').html('<span class="badge bg-danger text-wrap shadow-sm" style="font-size: 0.7rem;"><i class="fas fa-exclamation-triangle me-1"></i>Wajib upload foto WC</span>').show();
            } else {
                $('#wcInfoBadge').html('<span class="badge bg-success text-wrap shadow-sm" style="font-size: 0.7rem;"><i class="fas fa-check-circle me-1"></i>Foto WC terisi</span>').show();
            }
        }
    }

    $(document).on('change', 'input[name="fasilitas_bab"]', function() {
        $('#foto_fasilitas_bab').val($(this).val());
        syncWcPlaceholder();
    });

    $('a[data-bs-toggle="tab"][href="#tabFoto"], a[href="#tabFoto"]').on('shown.bs.tab', syncWcPlaceholder);
    $('#fotoKamarMandi').on('change', syncWcPlaceholder);

    $(document).ready(function() {
        syncWcPlaceholder();
    });

    // ==========================================
    // 📸 SUBMIT FORM FOTO & GEOTAG
    // ==========================================
    $('#formFoto, #formFotoGeotag').on('submit', function(e) {
        e.preventDefault();

        // 1. Ambil status Kepemilikan Fasilitas BAB dari Tab Rumah
        const fasilitasBab = $('input[name="fasilitas_bab"]:checked').val() || $('#foto_fasilitas_bab').val() || '';
        const isTidakAdaBab = (fasilitasBab === 'Tidak ada fasilitas');

        // 2. Cek kelengkapan 4 foto
        const fileKtp = document.getElementById('fotoKtp')?.files?.[0];
        const srcKtp = $('#previewKtp').attr('src') || '';
        const hasKtp = Boolean(fileKtp) || (srcKtp !== '' && !srcKtp.includes('noimage.png'));

        const fileDepan = document.getElementById('fotoDepan')?.files?.[0];
        const srcDepan = $('#previewDepan').attr('src') || '';
        const hasDepan = Boolean(fileDepan) || (srcDepan !== '' && !srcDepan.includes('noimage.png'));

        const fileDalam = document.getElementById('fotoDalam')?.files?.[0];
        const srcDalam = $('#previewDalam').attr('src') || '';
        const hasDalam = Boolean(fileDalam) || (srcDalam !== '' && !srcDalam.includes('noimage.png'));

        const fileWc = document.getElementById('fotoKamarMandi')?.files?.[0];
        const srcWc = $('#previewKamarMandi').attr('src') || '';
        const hasRealWc = Boolean(fileWc) || (srcWc !== '' && !srcWc.includes('noimage.png') && !srcWc.includes('image_not_available.jpg'));
        const hasPlaceholderWc = (srcWc !== '' && srcWc.includes('image_not_available.jpg'));

        let missingPhotos = [];
        if (!hasKtp) missingPhotos.push('Foto KTP / KK');
        if (!hasDepan) missingPhotos.push('Foto Rumah Tampak Depan');
        if (!hasDalam) missingPhotos.push('Foto Ruang Tamu / Dalam');

        if (!hasRealWc) {
            if (!isTidakAdaBab) {
                missingPhotos.push('Foto Kamar Mandi / WC');
            } else {
                // Khusus "Tidak ada fasilitas": boleh diganti image_not_available.jpg
                if (!hasPlaceholderWc) {
                    $('#previewKamarMandi').attr('src', window.baseUrl + '/assets/images/image_not_available.jpg');
                }
            }
        }

        if (missingPhotos.length > 0) {
            let note = '';
            if (!isTidakAdaBab && missingPhotos.includes('Foto Kamar Mandi / WC')) {
                note = `<div class="alert alert-warning py-2 px-3 mt-3 text-start small mb-0 border-warning">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <strong>Catatan:</strong> Foto Kamar Mandi / WC wajib diunggah karena Kepemilikan Fasilitas BAB di <b>Tab Rumah</b> bernilai <i>"${fasilitasBab || 'tersedia'}"</i> (bukan "Tidak ada fasilitas").
                        </div>`;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Foto Belum Lengkap!',
                html: `<div class="text-start mb-2">Foto berikut masih kosong dan wajib diisi sebelum diunggah:</div>
                       <ul class="text-start fw-bold text-danger mb-0">
                           <li>` + missingPhotos.join('</li><li>') + `</li>
                       </ul>` + note,
                confirmButtonText: 'Mengerti',
                customClass: {
                    title: 'fs-5',
                    content: 'fs-6'
                }
            });
            return false;
        }

        const formData = new FormData(this);
        formData.set('fasilitas_bab', fasilitasBab);

        Swal.fire({
            title: 'Menyimpan Foto...',
            text: 'Proses kompresi dan pemberian watermark sedang berjalan.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: window.baseUrl + '/pembaruan-keluarga/save-foto',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 'success') {
                    if (res.data && res.data.foto) {
                        const resolveFotoUrl = (path) => {
                            if (!path) return '';
                            if (/^https?:\/\//i.test(path)) return path;
                            return window.baseUrl.replace(/\/$/, '') + '/' + String(path).replace(/^\//, '');
                        };
                        const mapping = [
                            { key: 'ktp_kk', preview: 'previewKtp', input: 'fotoKtp' },
                            { key: 'depan', preview: 'previewDepan', input: 'fotoDepan' },
                            { key: 'dalam', preview: 'previewDalam', input: 'fotoDalam' },
                            { key: 'kamar_mandi', preview: 'previewKamarMandi', input: 'fotoKamarMandi' }
                        ];
                        mapping.forEach(item => {
                            const savedPath = res.data.foto[item.key];
                            if (savedPath) {
                                $('#' + item.preview)
                                    .attr('data-saved-src', savedPath)
                                    .attr('src', resolveFotoUrl(savedPath));
                            }
                            const inputEl = document.getElementById(item.input);
                            if (inputEl) inputEl.value = '';
                        });
                    }
                    syncWcPlaceholder();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // 🚀 Otomatis berpindah ke Tab Selanjutnya (Tab Aset) setelah alert sukses
                    setTimeout(() => {
                        if (typeof window.pindahTabSelanjutnya === 'function') {
                            window.pindahTabSelanjutnya('#tabFoto');
                        }
                    }, 1200);
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            },
            error: function(xhr) {
                let msg = 'Tidak dapat mengirim data ke server.';
                try {
                    const parsed = JSON.parse(xhr.responseText);
                    if (parsed && parsed.message) msg = parsed.message;
                } catch (err) {}
                Swal.fire('Error!', msg, 'error');
            }
        });
    });

    // 🚀 LOGIKA UNDUH SATUAN (Mempertahankan nama asli dari URL server)
    document.addEventListener('click', function(e) {
        let targetImg = null;

        if (e.target.classList.contains('img-download')) {
            targetImg = e.target;
        } else {
            const btnDownload = e.target.closest('.btn-download-foto');
            if (btnDownload) {
                const targetId = btnDownload.getAttribute('data-target');
                targetImg = document.getElementById(targetId);
            }
        }

        if (targetImg) {
            if (targetImg.src.includes('noimage.png')) {
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'warning',
                    title: 'Belum ada foto untuk diunduh!',
                    showConfirmButton: false,
                    timer: 2000
                });
                return;
            }

            const a = document.createElement('a');
            a.href = targetImg.src;
            // 🚀 Ambil nama file asli murni dari ujung URL server (misal: sinden_320533....jpg)
            a.download = targetImg.src.split('/').pop() || 'gambar.jpg';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    });

    // 🚀 LOGIKA UNDUH BULK (UNDUH SEMUA FOTO BERURUTAN)
    document.getElementById('btnBulkDownload').addEventListener('click', function() {
        const previewIds = ['previewKtp', 'previewDepan', 'previewDalam', 'previewKamarMandi'];
        let downloadedCount = 0;

        previewIds.forEach((id, index) => {
            const imgEl = document.getElementById(id);
            if (imgEl && !imgEl.src.includes('noimage.png')) {
                // Beri jeda waktu (delay) 400ms tiap file agar browser tidak memblokir multi-download (popup blocker)
                setTimeout(() => {
                    const a = document.createElement('a');
                    a.href = imgEl.src;
                    a.download = imgEl.src.split('/').pop() || `foto_${index+1}.jpg`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }, index * 400);
                downloadedCount++;
            }
        });

        if (downloadedCount === 0) {
            Swal.fire('Info', 'Tidak ada foto yang tersedia untuk diunduh.', 'info');
        } else {
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'success',
                title: `Memulai unduh ${downloadedCount} foto...`,
                showConfirmButton: false,
                timer: 2000
            });
        }
    });

    const imageInputs = [{
            input: 'fotoKtp',
            preview: 'previewKtp'
        },
        {
            input: 'fotoDepan',
            preview: 'previewDepan'
        },
        {
            input: 'fotoDalam',
            preview: 'previewDalam'
        },
        {
            input: 'fotoKamarMandi',
            preview: 'previewKamarMandi'
        }
    ];

    const previewObjectUrls = {};

    async function applyFileToField(fileInput, previewId, file) {
        if (!file || !file.type.startsWith('image/')) {
            Swal.fire('File tidak valid', 'Gunakan foto langsung dari kamera HP.', 'warning');
            fileInput.value = '';
            return false;
        }

        const compressedResult = await compressWithLibrary(file);
        const fileName = (file.name && file.name.trim() !== '') ? file.name : (fileInput.id + '_' + Date.now() + '.jpg');
        const compressedFile = compressedResult instanceof File
            ? compressedResult
            : new File([compressedResult], fileName.replace(/\.[^.]+$/, '') + '.jpg', {
                type: 'image/jpeg'
            });

        if (previewObjectUrls[previewId]) {
            URL.revokeObjectURL(previewObjectUrls[previewId]);
        }
        const objUrl = URL.createObjectURL(compressedFile);
        previewObjectUrls[previewId] = objUrl;

        const previewEl = document.getElementById(previewId);
        if (previewEl) {
            previewEl.src = objUrl;
        }

        const dt = new DataTransfer();
        dt.items.add(compressedFile);
        fileInput.files = dt.files;

        if (fileInput.id === 'fotoKamarMandi') {
            syncWcPlaceholder();
        }
        return true;
    }

    imageInputs.forEach(item => {
        const fileInput = document.getElementById(item.input);
        if (!fileInput) return;
        fileInput.addEventListener('change', async function() {
            const file = this.files[0];
            if (!file) return;
            try {
                await applyFileToField(fileInput, item.preview, file);
            } catch (err) {
                Swal.fire('Gagal Kompres', err.message, 'error');
                this.value = '';
            }
        });
    });

    async function compressWithLibrary(file) {
        if (typeof imageCompression !== 'function') throw new Error('Library imageCompression belum dimuat');
        return await imageCompression(file, {
            maxSizeMB: 0.45,
            maxWidthOrHeight: 1280,
            useWebWorker: true,
            fileType: 'image/jpeg',
            initialQuality: 0.75
        });
    }

    // ==========================================
    // 📸 MESIN KAMERA & BINGKAI PANDUAN PER FIELD
    // ==========================================
    (function initGuidedCamera() {
        const overlay = document.getElementById('cameraGuideOverlay');
        if (!overlay) return;

        // Teleportasi ke document.body agar terbebas dari stacking context tab-pane.fade
        if (overlay.parentElement !== document.body) {
            document.body.appendChild(overlay);
        }

        const stageEl = document.getElementById('cameraStage');
        const viewportEl = document.getElementById('cameraViewport');
        const videoEl = document.getElementById('cameraVideoStream');
        const canvasEl = document.getElementById('cameraCaptureCanvas');
        const loadingEl = document.getElementById('cameraLoadingState');
        const framingLayerEl = document.getElementById('cameraFramingLayer');
        const guideBoxEl = document.getElementById('cameraGuideBox');
        const guideLabelEl = document.getElementById('cameraGuideLabel');
        const guideInnerEl = document.getElementById('cameraGuideBoxInner');
        const watermarkHintEl = document.getElementById('cameraWatermarkHint');
        const guidanceBannerEl = document.getElementById('cameraGuidanceBanner');
        const zoomBarEl = document.getElementById('cameraZoomBar');
        const shutterFlashEl = document.getElementById('cameraShutterFlash');
        const previewLayerEl = document.getElementById('cameraPreviewLayer');
        const capturedPreviewEl = document.getElementById('cameraCapturedPreview');

        const badgeEl = document.getElementById('cameraOverlayBadge');
        const titleEl = document.getElementById('cameraOverlayTitle');
        const subEl = document.getElementById('cameraOverlaySub');

        const controlsLiveEl = document.getElementById('camControlsLive');
        const controlsReviewEl = document.getElementById('camControlsReview');
        const btnToggleGuide = document.getElementById('btnCamToggleGuide');
        const btnRotateFrame = document.getElementById('btnCamRotateFrame');
        const btnTorch = document.getElementById('btnCamTorch');
        const btnSwitch = document.getElementById('btnCamSwitch');
        const btnClose = document.getElementById('btnCamClose');
        const btnCapture = document.getElementById('btnCamCapture');
        const btnRetake = document.getElementById('btnCamRetake');
        const btnUsePhoto = document.getElementById('btnCamUsePhoto');

        const FIELD_GUIDES = {
            ktp: {
                inputId: 'fotoKtp',
                previewId: 'previewKtp',
                badge: 'IDENTITAS',
                badgeClass: 'bg-info text-dark',
                title: 'Foto KTP / Kartu Keluarga',
                subtitle: 'Sesuaikan dokumen pada bingkai tanpa pantulan cahaya',
                frameLabel: 'BINGKAI KTP / KK',
                accent: '#00e5ff',
                mask: 'rgba(0, 0, 0, 0.56)',
                fullFrame: false,
                preferWide1x: false,
                allowRotate: true,
                defaultOrientation: 'landscape',
                landscapeRatio: 1.586,
                portraitRatio: 3 / 4,
                cropToGuide: false,
                showWatermarkHint: false,
                guidance: 'Posisikan KTP / KK di dalam bingkai panduan (hasil pemotretan rasio 3:4).',
                svgLandscape: `
                    <svg viewBox="0 0 158 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="6" y="7" width="146" height="12" rx="2" fill="rgba(0,229,255,0.12)" stroke="rgba(0,229,255,0.5)" stroke-width="0.6" stroke-dasharray="3 2"/>
                        <rect x="8" y="25" width="76" height="8" rx="1.5" fill="rgba(255,255,255,0.12)"/>
                        <rect x="8" y="38" width="64" height="5" rx="1" fill="rgba(255,255,255,0.10)"/>
                        <rect x="8" y="47" width="70" height="5" rx="1" fill="rgba(255,255,255,0.10)"/>
                        <rect x="8" y="56" width="58" height="5" rx="1" fill="rgba(255,255,255,0.10)"/>
                        <rect x="8" y="65" width="66" height="5" rx="1" fill="rgba(255,255,255,0.10)"/>
                        <rect x="110" y="25" width="38" height="48" rx="3" fill="rgba(0,229,255,0.08)" stroke="rgba(0,229,255,0.65)" stroke-width="0.9" stroke-dasharray="4 2"/>
                        <circle cx="129" cy="41" r="7" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="0.8"/>
                        <path d="M 118 66 C 118 57, 140 57, 140 66" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="0.8"/>
                        <text x="129" y="79" text-anchor="middle" fill="rgba(255,255,255,0.72)" font-size="4.6" font-weight="600">PAS FOTO (KTP)</text>
                    </svg>`,
                svgPortrait: `
                    <svg viewBox="0 0 100 141" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="8" y="8" width="84" height="14" rx="2" fill="rgba(0,229,255,0.12)" stroke="rgba(0,229,255,0.5)" stroke-width="0.7" stroke-dasharray="3 2"/>
                        <line x1="8" y1="30" x2="92" y2="30" stroke="rgba(255,255,255,0.3)" stroke-width="0.6" stroke-dasharray="3 2"/>
                        <line x1="8" y1="45" x2="92" y2="45" stroke="rgba(255,255,255,0.22)" stroke-width="0.5" stroke-dasharray="2 2"/>
                        <line x1="8" y1="60" x2="92" y2="60" stroke="rgba(255,255,255,0.22)" stroke-width="0.5" stroke-dasharray="2 2"/>
                        <line x1="8" y1="75" x2="92" y2="75" stroke="rgba(255,255,255,0.22)" stroke-width="0.5" stroke-dasharray="2 2"/>
                        <line x1="8" y1="90" x2="92" y2="90" stroke="rgba(255,255,255,0.22)" stroke-width="0.5" stroke-dasharray="2 2"/>
                        <rect x="8" y="102" width="84" height="28" rx="2" fill="rgba(255,255,255,0.06)" stroke="rgba(255,255,255,0.28)" stroke-width="0.6" stroke-dasharray="3 2"/>
                        <text x="50" y="17" text-anchor="middle" fill="rgba(255,255,255,0.75)" font-size="4.8" font-weight="600">DOKUMEN KTP / KK</text>
                    </svg>`
            },
            depan: {
                inputId: 'fotoDepan',
                previewId: 'previewDepan',
                badge: 'RUMAH DEPAN',
                badgeClass: 'bg-success text-white',
                title: 'Foto Rumah Tampak Depan',
                subtitle: 'Atap, dinding depan, dan teras rumah terlihat utuh',
                frameLabel: 'BINGKAI TAMPAK DEPAN',
                accent: '#2ec4b6',
                mask: 'rgba(0, 0, 0, 0.18)',
                fullFrame: true,
                preferWide1x: true,
                allowRotate: false,
                cropToGuide: false,
                showWatermarkHint: true,
                guidance: 'Tampilkan tampak depan rumah secara utuh (atap hingga lantai/teras) dan sejajar.',
                svgTemplate: `
                    <svg viewBox="0 0 120 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="40" y1="2" x2="40" y2="118" vector-effect="non-scaling-stroke" stroke="rgba(255,255,255,0.14)" stroke-width="1"/>
                        <line x1="80" y1="2" x2="80" y2="118" vector-effect="non-scaling-stroke" stroke="rgba(255,255,255,0.14)" stroke-width="1"/>
                        <line x1="2" y1="40" x2="118" y2="40" vector-effect="non-scaling-stroke" stroke="rgba(255,255,255,0.14)" stroke-width="1"/>
                        <line x1="2" y1="80" x2="118" y2="80" vector-effect="non-scaling-stroke" stroke="rgba(255,255,255,0.14)" stroke-width="1"/>
                        <polygon points="5,38 60,12 115,38" vector-effect="non-scaling-stroke" fill="rgba(46,196,182,0.05)" stroke="rgba(46,196,182,0.78)" stroke-width="1.8" stroke-dasharray="6 4"/>
                        <rect x="10" y="38" width="100" height="62" vector-effect="non-scaling-stroke" fill="none" stroke="rgba(46,196,182,0.68)" stroke-width="1.6" stroke-dasharray="6 4"/>
                        <rect x="50" y="58" width="20" height="42" rx="1" vector-effect="non-scaling-stroke" fill="none" stroke="rgba(255,255,255,0.55)" stroke-width="1.4" stroke-dasharray="4 3"/>
                        <rect x="19" y="52" width="20" height="18" rx="1" vector-effect="non-scaling-stroke" fill="none" stroke="rgba(255,255,255,0.48)" stroke-width="1.3" stroke-dasharray="4 3"/>
                        <rect x="81" y="52" width="20" height="18" rx="1" vector-effect="non-scaling-stroke" fill="none" stroke="rgba(255,255,255,0.48)" stroke-width="1.3" stroke-dasharray="4 3"/>
                        <line x1="4" y1="100" x2="116" y2="100" vector-effect="non-scaling-stroke" stroke="rgba(255,255,255,0.52)" stroke-width="1.5" stroke-dasharray="7 4"/>
                    </svg>
                    <div class="guide-zone-hint" style="top: 22px; left: 50%; transform: translateX(-50%);">AREA ATAP RUMAH</div>
                    <div class="guide-zone-hint" style="bottom: 48px; left: 50%; transform: translateX(-50%);">BATAS TERAS / HALAMAN</div>`
            },
            dalam: {
                inputId: 'fotoDalam',
                previewId: 'previewDalam',
                badge: 'RUANG DALAM',
                badgeClass: 'bg-warning text-dark',
                title: 'Foto Ruang Tamu / Dalam',
                subtitle: 'Perlihatkan kondisi atap/plafon, dinding, dan lantai',
                frameLabel: 'BINGKAI RUANG DALAM',
                accent: '#ffd166',
                mask: 'rgba(0, 0, 0, 0.18)',
                fullFrame: true,
                preferWide1x: true,
                allowRotate: false,
                cropToGuide: false,
                showWatermarkHint: true,
                guidance: 'Arahkan ke sudut ruangan agar Plafon/Atap, Dinding, dan Lantai terlihat jelas.',
                svgTemplate: `
                    <svg viewBox="0 0 120 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="60" y1="24" x2="60" y2="88" vector-effect="non-scaling-stroke" stroke="rgba(255,209,102,0.8)" stroke-width="1.7" stroke-dasharray="6 4"/>
                        <line x1="60" y1="24" x2="4" y2="4" vector-effect="non-scaling-stroke" stroke="rgba(255,209,102,0.75)" stroke-width="1.7" stroke-dasharray="6 4"/>
                        <line x1="60" y1="24" x2="116" y2="4" vector-effect="non-scaling-stroke" stroke="rgba(255,209,102,0.75)" stroke-width="1.7" stroke-dasharray="6 4"/>
                        <line x1="60" y1="88" x2="4" y2="114" vector-effect="non-scaling-stroke" stroke="rgba(255,209,102,0.8)" stroke-width="1.7" stroke-dasharray="6 4"/>
                        <line x1="60" y1="88" x2="116" y2="114" vector-effect="non-scaling-stroke" stroke="rgba(255,209,102,0.8)" stroke-width="1.7" stroke-dasharray="6 4"/>
                        <rect x="20" y="32" width="80" height="48" vector-effect="non-scaling-stroke" fill="none" stroke="rgba(255,255,255,0.24)" stroke-width="1.2" stroke-dasharray="5 5"/>
                    </svg>
                    <div class="guide-zone-hint" style="top: 22px; left: 50%; transform: translateX(-50%);">ATAP / PLAFON</div>
                    <div class="guide-zone-hint" style="top: 50%; left: 10px; transform: translateY(-50%);">DINDING</div>
                    <div class="guide-zone-hint" style="top: 50%; right: 10px; transform: translateY(-50%);">DINDING</div>
                    <div class="guide-zone-hint" style="bottom: 48px; left: 50%; transform: translateX(-50%);">AREA LANTAI</div>`
            },
            wc: {
                inputId: 'fotoKamarMandi',
                previewId: 'previewKamarMandi',
                badge: 'SANITASI / WC',
                badgeClass: 'bg-primary text-white',
                title: 'Foto Kamar Mandi / WC',
                subtitle: 'Fokus pada kloset serta lantai & dinding kamar mandi',
                frameLabel: 'BINGKAI KAMAR MANDI / WC',
                accent: '#38bdf8',
                mask: 'rgba(0, 0, 0, 0.22)',
                fullFrame: true,
                preferWide1x: true,
                allowRotate: false,
                cropToGuide: false,
                showWatermarkHint: true,
                guidance: 'Fokuskan kloset/jamban di dalam area target beserta kondisi lantai & dinding WC.',
                svgTemplate: `
                    <svg viewBox="0 0 120 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <line x1="40" y1="2" x2="40" y2="118" vector-effect="non-scaling-stroke" stroke="rgba(255,255,255,0.12)" stroke-width="1"/>
                        <line x1="80" y1="2" x2="80" y2="118" vector-effect="non-scaling-stroke" stroke="rgba(255,255,255,0.12)" stroke-width="1"/>
                        <line x1="4" y1="82" x2="116" y2="82" vector-effect="non-scaling-stroke" stroke="rgba(56,189,248,0.52)" stroke-width="1.4" stroke-dasharray="6 4"/>
                        <rect x="10" y="18" width="100" height="82" rx="6" vector-effect="non-scaling-stroke" fill="rgba(56,189,248,0.05)" stroke="rgba(56,189,248,0.78)" stroke-width="1.8" stroke-dasharray="6 4"/>
                        <rect x="44" y="34" width="32" height="18" rx="3" vector-effect="non-scaling-stroke" fill="none" stroke="rgba(255,255,255,0.55)" stroke-width="1.5" stroke-dasharray="4 3"/>
                        <ellipse cx="60" cy="70" rx="18" ry="12" vector-effect="non-scaling-stroke" fill="none" stroke="rgba(255,255,255,0.65)" stroke-width="1.6" stroke-dasharray="5 3"/>
                        <path d="M 48 80 L 51 93 L 69 93 L 72 80" vector-effect="non-scaling-stroke" fill="none" stroke="rgba(255,255,255,0.55)" stroke-width="1.5" stroke-dasharray="4 3"/>
                    </svg>
                    <div class="guide-zone-hint" style="top: 22px; left: 50%; transform: translateX(-50%);">DINDING KAMAR MANDI</div>
                    <div class="guide-zone-hint" style="bottom: 48px; left: 50%; transform: translateX(-50%);">TARGET KLOSET &amp; LANTAI</div>`
            }
        };

        let activeFieldKey = 'ktp';
        let currentStream = null;
        let currentFacingMode = 'environment';
        let currentDeviceIndex = -1;
        let availableVideoDevices = [];
        let attemptedWideLensSwitch = false;
        let guideVisible = true;
        let frameOrientation = 'landscape';
        let torchEnabled = false;
        let currentZoomValue = 1;
        let capturedRawFile = null;
        let capturedPreviewUrl = null;
        let previousBodyOverflow = '';

        function getActiveConfig() {
            return FIELD_GUIDES[activeFieldKey] || FIELD_GUIDES.ktp;
        }

        function stopMediaStream() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => {
                    try {
                        track.stop();
                    } catch (e) {}
                });
                currentStream = null;
            }
            videoEl.srcObject = null;
            torchEnabled = false;
            currentZoomValue = 1;
            btnTorch.classList.add('d-none');
            btnTorch.classList.remove('btn-warning', 'text-dark');
            btnTorch.classList.add('btn-outline-warning');
            if (zoomBarEl) {
                zoomBarEl.classList.add('d-none');
                zoomBarEl.innerHTML = '';
            }
        }

        function revokeCapturedPreview() {
            if (capturedPreviewUrl) {
                URL.revokeObjectURL(capturedPreviewUrl);
                capturedPreviewUrl = null;
            }
            capturedPreviewEl.src = '';
            capturedRawFile = null;
        }

        function setReviewMode(isReview) {
            if (isReview) {
                previewLayerEl.classList.remove('d-none');
                controlsLiveEl.classList.add('d-none');
                controlsReviewEl.classList.remove('d-none');
                guidanceBannerEl.classList.add('d-none');
                if (zoomBarEl) zoomBarEl.classList.add('d-none');
            } else {
                previewLayerEl.classList.add('d-none');
                controlsReviewEl.classList.add('d-none');
                controlsLiveEl.classList.remove('d-none');
                guidanceBannerEl.classList.toggle('d-none', !guideVisible);
                if (zoomBarEl && zoomBarEl.childElementCount > 0) {
                    zoomBarEl.classList.remove('d-none');
                }
                revokeCapturedPreview();
                if (videoEl.srcObject && videoEl.paused) {
                    videoEl.play().catch(() => {});
                }
            }
        }

        function isMobileHandset() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
                (navigator.maxTouchPoints > 1 && /Macintosh/i.test(navigator.userAgent));
        }

        function triggerNativePhoneCamera(targetInput) {
            if (!targetInput) return;
            targetInput.setAttribute('accept', 'image/*');
            targetInput.setAttribute('capture', 'environment');
            targetInput.click();
        }

        function updateGuideAppearance() {
            const cfg = getActiveConfig();
            const CAMERA_RATIO_3_4 = 3 / 4; // Rasio standar kamera & hasil foto 3:4

            if (guideVisible) {
                framingLayerEl.classList.remove('d-none');
                btnToggleGuide.innerHTML = '<i class="fas fa-eye"></i>';
            } else {
                framingLayerEl.classList.add('d-none');
                btnToggleGuide.innerHTML = '<i class="fas fa-eye-slash"></i>';
            }
            guidanceBannerEl.textContent = cfg.guidance;
            guidanceBannerEl.classList.toggle('d-none', !guideVisible || !previewLayerEl.classList.contains('d-none'));

            const stageW = stageEl.clientWidth || window.innerWidth;
            const stageH = stageEl.clientHeight || (window.innerHeight * 0.7);
            const vw = videoEl.videoWidth || 1200;
            const vh = videoEl.videoHeight || 1600;

            let effectiveOrientation = frameOrientation;
            if (effectiveOrientation === 'auto') {
                effectiveOrientation = (vw >= vh) ? 'landscape' : 'portrait';
            }

            const ratio = (effectiveOrientation === 'landscape') ? cfg.landscapeRatio : cfg.portraitRatio;

            overlay.style.setProperty('--guide-accent', cfg.accent);
            overlay.style.setProperty('--guide-mask', cfg.mask);

            badgeEl.className = 'badge ' + cfg.badgeClass;
            badgeEl.textContent = cfg.badge;
            titleEl.textContent = cfg.title;
            subEl.textContent = cfg.subtitle;
            btnRotateFrame.classList.toggle('d-none', !cfg.allowRotate);
            guideLabelEl.textContent = cfg.allowRotate
                ? (cfg.frameLabel + (effectiveOrientation === 'landscape' ? ' (LANDSCAPE)' : ' (PORTRAIT)'))
                : cfg.frameLabel;

            if (cfg.showWatermarkHint && guideVisible) {
                watermarkHintEl.classList.remove('d-none');
            } else {
                watermarkHintEl.classList.add('d-none');
            }

            if (activeFieldKey === 'ktp') {
                guideInnerEl.innerHTML = (effectiveOrientation === 'landscape') ? cfg.svgLandscape : cfg.svgPortrait;
            } else {
                guideInnerEl.innerHTML = cfg.svgTemplate;
            }

            // Kunci viewport kamera ke rasio 3:4 terbesar di dalam stage (penuh kiri-kanan pada layar HP portrait)
            let vpW = stageW;
            let vpH = Math.round(vpW / CAMERA_RATIO_3_4);
            if (vpH > stageH) {
                vpH = stageH;
                vpW = Math.round(vpH * CAMERA_RATIO_3_4);
            }

            viewportEl.style.width = vpW + 'px';
            viewportEl.style.height = vpH + 'px';

            const maxGuideW = Math.max(140, vpW - 8); // Sisakan 4px kiri + 4px kanan agar sudut bingkai tidak terpotong
            const maxGuideH = Math.max(140, vpH - 24); // Sisakan ruang aman atas untuk label bingkai

            let boxW = maxGuideW;
            let boxH = maxGuideH;
            if (!cfg.fullFrame) {
                boxH = Math.round(boxW / ratio);
                if (boxH > maxGuideH) {
                    boxH = maxGuideH;
                    boxW = Math.round(boxH * ratio);
                }
            }

            guideBoxEl.style.width = Math.round(boxW) + 'px';
            guideBoxEl.style.height = Math.round(boxH) + 'px';
        }

        async function refreshCameraDevices(cfg, useSpecificDevice) {
            if (!navigator.mediaDevices || typeof navigator.mediaDevices.enumerateDevices !== 'function') return false;
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                availableVideoDevices = devices.filter(d => d.kind === 'videoinput');
                btnSwitch.classList.toggle('d-none', availableVideoDevices.length < 2);

                // Jika mode kamera lebar 1x aktif & browser tanpa sengaja membuka lensa tele/macro/bokeh, alihkan ke kamera belakang utama
                if (!useSpecificDevice && currentFacingMode === 'environment' && cfg && cfg.preferWide1x && !attemptedWideLensSwitch && availableVideoDevices.length >= 2) {
                    const activeTrack = currentStream?.getVideoTracks?.()[0];
                    const activeId = activeTrack?.getSettings?.()?.deviceId;
                    const activeLabel = (activeTrack?.label || '').toLowerCase();
                    const isNarrowActive = /(tele|macro|depth|bokeh)/i.test(activeLabel);

                    if (isNarrowActive) {
                        const mainIdx = availableVideoDevices.findIndex(d => {
                            const lbl = (d.label || '').toLowerCase();
                            return d.deviceId !== activeId &&
                                /(back|rear|environment|camera2 0)/i.test(lbl) &&
                                !/(tele|macro|depth|bokeh|front|user)/i.test(lbl);
                        });
                        if (mainIdx >= 0) {
                            attemptedWideLensSwitch = true;
                            currentDeviceIndex = mainIdx;
                            await startCameraStream(true);
                            return true;
                        }
                    }
                }
            } catch (e) {
                availableVideoDevices = [];
            }
            return false;
        }

        function renderZoomControls(track, caps) {
            if (!zoomBarEl || !caps || !caps.zoom || typeof caps.zoom.min !== 'number') return;
            const minZ = caps.zoom.min;
            const maxZ = (typeof caps.zoom.max === 'number') ? caps.zoom.max : minZ;
            if (maxZ <= minZ + 0.05) return;

            const presets = [];
            if (minZ < 0.95) {
                presets.push(Number(minZ.toFixed(1)));
            }
            if (minZ <= 1.05 && maxZ >= 0.95) {
                presets.push(1);
            } else {
                presets.push(Number(minZ.toFixed(1)));
            }
            if (maxZ >= 2) {
                presets.push(2);
            }

            const uniquePresets = [...new Set(presets)];
            zoomBarEl.innerHTML = '';
            uniquePresets.forEach(zVal => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn-cam-zoom' + (Math.abs(zVal - currentZoomValue) < 0.12 ? ' active' : '');
                btn.textContent = (zVal === 1 ? '1x' : zVal + 'x');
                btn.addEventListener('click', async function() {
                    try {
                        await track.applyConstraints({
                            advanced: [{
                                zoom: zVal
                            }]
                        });
                        currentZoomValue = zVal;
                        zoomBarEl.querySelectorAll('.btn-cam-zoom').forEach(el => el.classList.remove('active'));
                        btn.classList.add('active');
                    } catch (e) {}
                });
                zoomBarEl.appendChild(btn);
            });

            if (previewLayerEl.classList.contains('d-none')) {
                zoomBarEl.classList.remove('d-none');
            }
        }

        async function configureTrackCapabilities(stream, cfg) {
            const track = stream.getVideoTracks()[0];
            if (!track) return;

            const caps = (typeof track.getCapabilities === 'function') ? (track.getCapabilities() || {}) : {};
            if (caps.torch) {
                btnTorch.classList.remove('d-none');
            }

            if (cfg && cfg.preferWide1x) {
                // 1. Jika HAL kamera masih memilih mode crop 16:9, turunkan ke sensor penuh 4:3 / 3:4 agar tidak terlihat seperti zoom x2
                const vw = videoEl.videoWidth || 0;
                const vh = videoEl.videoHeight || 0;
                const longRatio = (vw > 0 && vh > 0) ? (Math.max(vw, vh) / Math.min(vw, vh)) : 0;
                const targetAspect = (vh >= vw) ? (3 / 4) : (4 / 3);

                if (longRatio > 1.45) {
                    try {
                        await track.applyConstraints({
                            aspectRatio: {
                                exact: targetAspect
                            },
                            resizeMode: 'none'
                        });
                    } catch (e1) {
                        try {
                            await track.applyConstraints({
                                aspectRatio: {
                                    exact: targetAspect
                                }
                            });
                        } catch (e2) {}
                    }
                } else if (vw > 0 && vw < 720 && caps.width && caps.width.max >= 960) {
                    // Coba tingkatkan ke 960x1280 tetap di dalam aspek 3:4 tanpa memicu crop 16:9
                    try {
                        const beforeRatio = Math.max(vw, vh) / Math.min(vw, vh);
                        await track.applyConstraints({
                            aspectRatio: {
                                exact: targetAspect
                            },
                            width: {
                                ideal: (vh >= vw) ? 960 : 1280
                            },
                            height: {
                                ideal: (vh >= vw) ? 1280 : 960
                            },
                            resizeMode: 'none'
                        });
                        const afterW = videoEl.videoWidth || vw;
                        const afterH = videoEl.videoHeight || vh;
                        const afterRatio = (afterW > 0 && afterH > 0) ? (Math.max(afterW, afterH) / Math.min(afterW, afterH)) : beforeRatio;
                        if (afterRatio > 1.45) {
                            await track.applyConstraints({
                                aspectRatio: {
                                    exact: targetAspect
                                }
                            });
                        }
                    } catch (e) {}
                }

                // 2. Pastikan tingkat zoom dimulai tepat pada 1x (atau minimum sensor jika min > 1)
                if (caps.zoom && typeof caps.zoom.min === 'number') {
                    const minZ = caps.zoom.min;
                    const maxZ = (typeof caps.zoom.max === 'number') ? caps.zoom.max : minZ;
                    const defaultZoom = (minZ <= 1 && maxZ >= 1) ? 1 : minZ;
                    currentZoomValue = defaultZoom;
                    try {
                        await track.applyConstraints({
                            advanced: [{
                                zoom: defaultZoom
                            }]
                        });
                    } catch (e) {}
                    renderZoomControls(track, caps);
                }
            }
        }

        async function requestMediaStreamSafe(videoConstraints) {
            try {
                return await navigator.mediaDevices.getUserMedia({
                    video: videoConstraints,
                    audio: false
                });
            } catch (err) {
                if (videoConstraints.resizeMode) {
                    const fallbackConstraints = Object.assign({}, videoConstraints);
                    delete fallbackConstraints.resizeMode;
                    return await navigator.mediaDevices.getUserMedia({
                        video: fallbackConstraints,
                        audio: false
                    });
                }
                throw err;
            }
        }

        async function startCameraStream(useSpecificDevice = false) {
            loadingEl.classList.remove('d-none');
            btnCapture.disabled = true;
            stopMediaStream();

            const cfg = getActiveConfig();
            const isScreenPortrait = (window.innerHeight || 0) >= (window.innerWidth || 0);

            const videoConstraints = cfg.preferWide1x
                ? {
                    aspectRatio: {
                        ideal: isScreenPortrait ? (3 / 4) : (4 / 3)
                    },
                    resizeMode: {
                        ideal: 'none'
                    }
                }
                : {
                    aspectRatio: {
                        ideal: 3 / 4
                    },
                    width: {
                        ideal: 1200
                    },
                    height: {
                        ideal: 1600
                    }
                };

            if (useSpecificDevice && currentDeviceIndex >= 0 && availableVideoDevices[currentDeviceIndex]) {
                videoConstraints.deviceId = {
                    exact: availableVideoDevices[currentDeviceIndex].deviceId
                };
            } else {
                videoConstraints.facingMode = {
                    ideal: currentFacingMode
                };
            }

            try {
                const stream = await requestMediaStreamSafe(videoConstraints);
                currentStream = stream;
                videoEl.srcObject = stream;

                await new Promise((resolve) => {
                    if (videoEl.readyState >= 1) {
                        resolve();
                    } else {
                        videoEl.onloadedmetadata = () => resolve();
                    }
                });

                await videoEl.play();

                const switchedToMain = await refreshCameraDevices(cfg, useSpecificDevice);
                if (switchedToMain) return;

                await configureTrackCapabilities(stream, cfg);
                loadingEl.classList.add('d-none');
                btnCapture.disabled = false;

                updateGuideAppearance();
                requestAnimationFrame(() => updateGuideAppearance());
            } catch (err) {
                loadingEl.classList.add('d-none');
                closeGuidedCamera();

                const cfg = getActiveConfig();
                const targetInput = document.getElementById(cfg.inputId);
                const mobileDevice = isMobileHandset();

                let reason = 'Kamera tidak dapat diakses pada perangkat ini.';
                if (err && (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError')) {
                    reason = mobileDevice
                        ? 'Izin kamera ditolak oleh browser. Aktifkan izin kamera pada pengaturan situs atau gunakan kamera bawaan HP.'
                        : 'Izin kamera ditolak oleh browser. Aktifkan izin kamera browser atau akses melalui kamera HP.';
                } else if (err && (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError')) {
                    reason = mobileDevice
                        ? 'Kamera web tidak terdeteksi. Gunakan kamera bawaan HP untuk mengambil foto.'
                        : 'Kamera tidak ditemukan pada perangkat ini. Pengambilan foto wajib menggunakan kamera perangkat / HP (tanpa galeri).';
                } else if (err && err.name === 'NotReadableError') {
                    reason = 'Kamera sedang digunakan oleh aplikasi lain.';
                }

                if (mobileDevice && targetInput) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kamera Browser Tidak Tersedia',
                        text: reason,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-camera me-1"></i> Buka Kamera HP',
                        cancelButtonText: 'Tutup'
                    }).then(result => {
                        if (result.isConfirmed) {
                            triggerNativePhoneCamera(targetInput);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kamera Tidak Tersedia',
                        text: reason,
                        confirmButtonText: 'Tutup'
                    });
                }
            }
        }

        function openGuidedCamera(fieldKey) {
            activeFieldKey = FIELD_GUIDES[fieldKey] ? fieldKey : 'ktp';
            const cfg = getActiveConfig();

            if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                const targetInput = document.getElementById(cfg.inputId);
                if (isMobileHandset() && targetInput) {
                    triggerNativePhoneCamera(targetInput);
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Kamera Membutuhkan HTTPS / HP',
                        text: 'Browser membatasi kamera langsung pada koneksi non-HTTPS. Buka halaman ini melalui HP atau gunakan HTTPS / localhost.',
                        confirmButtonText: 'Tutup'
                    });
                }
                return;
            }

            guideVisible = true;
            frameOrientation = cfg.defaultOrientation || 'landscape';
            currentFacingMode = 'environment';
            currentDeviceIndex = -1;
            attemptedWideLensSwitch = false;

            previousBodyOverflow = document.body.style.overflow || '';
            document.body.style.overflow = 'hidden';

            setReviewMode(false);
            overlay.classList.remove('d-none');
            updateGuideAppearance();
            startCameraStream(false);
        }

        function closeGuidedCamera() {
            stopMediaStream();
            setReviewMode(false);
            overlay.classList.add('d-none');
            document.body.style.overflow = previousBodyOverflow;
        }

        function captureCurrentFrame() {
            if (!videoEl.videoWidth || !videoEl.videoHeight) return;

            const TARGET_RATIO_3_4 = 3 / 4; // Rasio hasil pemotretan wajib 3:4 portrait
            const vw = videoEl.videoWidth;
            const vh = videoEl.videoHeight;
            const vpRect = viewportEl.getBoundingClientRect();

            let sx = 0;
            let sy = 0;
            let sw = vw;
            let sh = vh;

            if (vpRect.width > 0 && vpRect.height > 0) {
                const renderScale = Math.max(vpRect.width / vw, vpRect.height / vh);
                const drawnW = vw * renderScale;
                const drawnH = vh * renderScale;
                const cropLeft = Math.max(0, (drawnW - vpRect.width) / 2);
                const cropTop = Math.max(0, (drawnH - vpRect.height) / 2);

                sx = Math.max(0, Math.round(cropLeft / renderScale));
                sy = Math.max(0, Math.round(cropTop / renderScale));
                sw = Math.min(vw - sx, Math.round(vpRect.width / renderScale));
                sh = Math.min(vh - sy, Math.round(vpRect.height / renderScale));
            }

            // Pastikan proporsi sumber tepat 3:4
            let targetSw = sw;
            let targetSh = Math.round(targetSw / TARGET_RATIO_3_4);
            if (targetSh > sh) {
                targetSh = sh;
                targetSw = Math.round(targetSh * TARGET_RATIO_3_4);
            }

            sx = Math.max(0, sx + Math.floor((sw - targetSw) / 2));
            sy = Math.max(0, sy + Math.floor((sh - targetSh) / 2));
            sw = Math.max(3, Math.min(vw - sx, targetSw));
            sh = Math.max(4, Math.min(vh - sy, targetSh));

            // Jaga resolusi minimal 960x1280 (3:4) agar watermark SINDEN bottom-left pas dan tidak terpotong
            const outW = Math.max(960, sw);
            const outH = Math.round(outW / TARGET_RATIO_3_4);

            canvasEl.width = outW;
            canvasEl.height = outH;
            const ctx = canvasEl.getContext('2d');
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(videoEl, sx, sy, sw, sh, 0, 0, outW, outH);

            shutterFlashEl.classList.add('flash');
            setTimeout(() => shutterFlashEl.classList.remove('flash'), 160);

            canvasEl.toBlob(blob => {
                if (!blob) return;
                revokeCapturedPreview();
                capturedRawFile = new File([blob], `cam_${activeFieldKey}_${Date.now()}.jpg`, {
                    type: 'image/jpeg'
                });
                capturedPreviewUrl = URL.createObjectURL(blob);
                capturedPreviewEl.src = capturedPreviewUrl;
                videoEl.pause();
                setReviewMode(true);
            }, 'image/jpeg', 0.92);
        }

        // Event: Buka Kamera Panduan
        $(document).on('click', '.btn-open-camera', function() {
            const fieldKey = $(this).data('camera-field');
            openGuidedCamera(fieldKey);
        });

        btnToggleGuide.addEventListener('click', function() {
            guideVisible = !guideVisible;
            updateGuideAppearance();
        });

        btnRotateFrame.addEventListener('click', function() {
            const vw = videoEl.videoWidth || 1280;
            const vh = videoEl.videoHeight || 720;
            const currentEffective = (frameOrientation === 'auto') ? ((vw >= vh) ? 'landscape' : 'portrait') : frameOrientation;
            frameOrientation = (currentEffective === 'landscape') ? 'portrait' : 'landscape';
            updateGuideAppearance();
        });

        btnTorch.addEventListener('click', async function() {
            if (!currentStream) return;
            const track = currentStream.getVideoTracks()[0];
            if (!track) return;
            try {
                torchEnabled = !torchEnabled;
                await track.applyConstraints({
                    advanced: [{
                        torch: torchEnabled
                    }]
                });
                btnTorch.classList.toggle('btn-warning', torchEnabled);
                btnTorch.classList.toggle('text-dark', torchEnabled);
                btnTorch.classList.toggle('btn-outline-warning', !torchEnabled);
            } catch (e) {
                torchEnabled = false;
            }
        });

        btnSwitch.addEventListener('click', async function() {
            const prevDeviceId = currentStream?.getVideoTracks?.()[0]?.getSettings?.()?.deviceId;
            currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
            currentDeviceIndex = -1;
            if (currentFacingMode === 'environment') {
                attemptedWideLensSwitch = false;
            }
            await startCameraStream(false);

            const newDeviceId = currentStream?.getVideoTracks?.()[0]?.getSettings?.()?.deviceId;
            if (prevDeviceId && newDeviceId === prevDeviceId && availableVideoDevices.length >= 2) {
                const activeIdx = availableVideoDevices.findIndex(d => d.deviceId === prevDeviceId);
                currentDeviceIndex = ((activeIdx >= 0 ? activeIdx : 0) + 1) % availableVideoDevices.length;
                await startCameraStream(true);
            }
        });

        videoEl.addEventListener('resize', function() {
            if (!overlay.classList.contains('d-none')) {
                updateGuideAppearance();
            }
        });

        btnCapture.addEventListener('click', captureCurrentFrame);
        btnRetake.addEventListener('click', () => setReviewMode(false));

        btnUsePhoto.addEventListener('click', async function() {
            if (!capturedRawFile) return;
            const cfg = getActiveConfig();
            const fileInput = document.getElementById(cfg.inputId);
            if (!fileInput) {
                closeGuidedCamera();
                return;
            }

            btnUsePhoto.disabled = true;
            btnUsePhoto.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

            try {
                await applyFileToField(fileInput, cfg.previewId, capturedRawFile);
                closeGuidedCamera();
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: cfg.title + ' siap diunggah',
                    showConfirmButton: false,
                    timer: 1800
                });
            } catch (err) {
                Swal.fire('Gagal Memproses Foto', err.message || 'Terjadi kesalahan saat memproses gambar.', 'error');
            } finally {
                btnUsePhoto.disabled = false;
                btnUsePhoto.innerHTML = '<i class="fas fa-check me-1"></i> Gunakan Foto';
            }
        });

        btnClose.addEventListener('click', closeGuidedCamera);

        window.addEventListener('resize', function() {
            if (!overlay.classList.contains('d-none')) {
                updateGuideAppearance();
            }
        });

        window.addEventListener('orientationchange', function() {
            setTimeout(function() {
                if (!overlay.classList.contains('d-none')) {
                    updateGuideAppearance();
                }
            }, 250);
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !overlay.classList.contains('d-none')) {
                closeGuidedCamera();
            }
        });
    })();
</script>