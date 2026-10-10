<?php
$roleId = $user['role_id'] ?? 99;
$editable = ($roleId <= 4); // Petugas & Operator bisa edit
$disabled = $editable ? '' : 'disabled';
$foto = $payload['foto'] ?? [];
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

        <!-- 🚀 4 KOLOM FOTO (2 BARIS DI MOBILE, 1 BARIS DI DESKTOP) -->
        <div class="row g-2">
            <!-- Foto KTP/KK -->
            <div class="col-6 col-md-3 mb-3 text-center position-relative">
                <label class="fw-semibold d-block small">Foto KTP / KK</label>
                <div class="position-relative d-inline-block w-100">
                    <img src="<?= base_url($foto['ktp_kk'] ?? 'data/usulan/foto_identitas/noimage.png') ?>"
                        class="img-fluid rounded border mb-2 img-download" id="previewKtp" style="aspect-ratio: 3/4; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_identitas/noimage.png') ?>'">

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
                <label class="fw-semibold d-block small">Tampak Depan</label>
                <div class="position-relative d-inline-block w-100">
                    <img src="<?= base_url($foto['depan'] ?? 'data/usulan/foto_rumah/noimage.png') ?>"
                        class="img-fluid rounded border mb-2 img-download" id="previewDepan" style="aspect-ratio: 3/4; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_rumah/noimage.png') ?>'">

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
                <label class="fw-semibold d-block small">Ruang Tamu / Dalam</label>
                <div class="position-relative d-inline-block w-100">
                    <img src="<?= base_url($foto['dalam'] ?? 'data/usulan/foto_rumah_dalam/noimage.png') ?>"
                        class="img-fluid rounded border mb-2 img-download" id="previewDalam" style="aspect-ratio: 3/4; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_rumah_dalam/noimage.png') ?>'">

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
                <label class="fw-semibold d-block small">Kamar Mandi / WC</label>
                <div class="position-relative d-inline-block w-100">
                    <img src="<?= base_url($foto['kamar_mandi'] ?? 'data/usulan/foto_kamar_mandi/noimage.png') ?>"
                        class="img-fluid rounded border mb-2 img-download" id="previewKamarMandi" style="aspect-ratio: 3/4; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_kamar_mandi/noimage.png') ?>'">

                    <button type="button" class="btn btn-sm btn-dark position-absolute shadow-sm btn-download-foto" data-target="previewKamarMandi" style="bottom: 15px; right: 5px; opacity: 0.85;" title="Unduh Foto Kamar Mandi">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <?php if ($editable): ?>
                    <input type="file" name="foto_kamar_mandi" id="fotoKamarMandi" class="form-control form-control-sm" accept="image/*" capture="environment">
                <?php endif; ?>
            </div>
        </div>

        <?php if ($editable): ?>
            <div class="mt-3 text-end">
                <?php if (in_array(strtolower($usulan['status'] ?? ''), ['draft', 'submitted'])): ?>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="fas fa-upload me-1"></i> Upload Semua Foto
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm btn-terkunci">
                        <i class="fas fa-lock me-1"></i> Terkunci
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- ============================== -->
<!-- 📡 JS SAVE FOTO & PREVIEW -->
<!-- ============================== -->
<script>
    // ==========================================
    // 📸 SUBMIT FORM FOTO & GEOTAG
    // ==========================================
    $('#formFoto, #formFotoGeotag').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Tidak dapat mengirim data ke server.', 'error');
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