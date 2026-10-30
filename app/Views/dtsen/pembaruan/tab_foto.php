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
                        class="img-fluid rounded border mb-2 img-download" id="previewKtp" data-saved-src="<?= esc($foto['ktp_kk'] ?? '') ?>" style="aspect-ratio: 3/4; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_identitas/noimage.png') ?>'">

                    <button type="button" class="btn btn-sm btn-dark position-absolute shadow-sm btn-download-foto" data-target="previewKtp" style="bottom: 15px; right: 5px; opacity: 0.85;" title="Unduh Foto KTP/KK">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <?php if ($editable): ?>
                    <input type="file" name="foto_ktp" id="fotoKtp" class="form-control form-control-sm" accept="image/*" capture="environment">
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
                    <input type="file" name="foto_depan" id="fotoDepan" class="form-control form-control-sm" accept="image/*" capture="environment">
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
                    <input type="file" name="foto_dalam" id="fotoDalam" class="form-control form-control-sm" accept="image/*" capture="environment">
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
                    <input type="file" name="foto_kamar_mandi" id="fotoKamarMandi" class="form-control form-control-sm" accept="image/*" capture="environment">
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
                        if (res.data.foto.ktp_kk) $('#previewKtp').attr('data-saved-src', res.data.foto.ktp_kk);
                        if (res.data.foto.depan) $('#previewDepan').attr('data-saved-src', res.data.foto.depan);
                        if (res.data.foto.dalam) $('#previewDalam').attr('data-saved-src', res.data.foto.dalam);
                        if (res.data.foto.kamar_mandi) $('#previewKamarMandi').attr('data-saved-src', res.data.foto.kamar_mandi);
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

    function previewImage(input, targetId) {
        if (input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById(targetId).src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    }

    ['Ktp', 'Depan', 'Dalam', 'KamarMandi'].forEach(suffix => {
        const input = document.getElementById('foto' + suffix);
        if (input) input.addEventListener('change', e => previewImage(e.target, 'preview' + suffix));
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

    imageInputs.forEach(item => {
        const fileInput = document.getElementById(item.input);
        if (!fileInput) return;
        fileInput.addEventListener('change', async function() {
            const file = this.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                Swal.fire('File tidak valid', 'Gunakan JPG/PNG.', 'warning');
                this.value = '';
                return;
            }
            try {
                const compressedResult = await compressWithLibrary(file);
                const compressedFile = compressedResult instanceof File ? compressedResult : new File([compressedResult], file.name, {
                    type: 'image/jpeg'
                });
                document.getElementById(item.preview).src = URL.createObjectURL(compressedFile);
                const dt = new DataTransfer();
                dt.items.add(compressedFile);
                fileInput.files = dt.files;
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
</script>