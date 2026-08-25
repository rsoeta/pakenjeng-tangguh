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
    <h5 class="fw-bold mb-3">📸 Upload Foto Rumah & KTP</h5>

    <form id="formFoto" enctype="multipart/form-data">
        <input type="hidden" name="dtsen_usulan_id" value="<?= esc($payload['id'] ?? $usulan['id'] ?? '') ?>">
        <input type="hidden" name="no_kk" value="<?= esc($payload['no_kk'] ?? $perumahan['no_kk'] ?? '') ?>">
        <input type="hidden" name="kepala_keluarga" value="<?= esc($payload['kepala_keluarga'] ?? $perumahan['kepala_keluarga'] ?? '') ?>">

        <!-- 🚀 4 KOLOM FOTO SEJAJAR -->
        <div class="row g-2">
            <!-- Foto KTP/KK -->
            <div class="col-md-3 mb-3 text-center">
                <label class="fw-semibold d-block small">Foto KTP / KK</label>
                <img src="<?= base_url($foto['ktp_kk'] ?? 'data/usulan/foto_identitas/noimage.png') ?>"
                    class="img-fluid rounded border mb-2 img-download" id="previewKtp" style="max-height: 180px; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_identitas/noimage.png') ?>'">
                <?php if ($editable): ?>
                    <input type="file" name="foto_ktp" id="fotoKtp" class="form-control form-control-sm" accept="image/*" capture="environment">
                <?php endif; ?>
            </div>

            <!-- Foto Rumah Depan -->
            <div class="col-md-3 mb-3 text-center">
                <label class="fw-semibold d-block small">Tampak Depan</label>
                <img src="<?= base_url($foto['depan'] ?? 'data/usulan/foto_rumah/noimage.png') ?>"
                    class="img-fluid rounded border mb-2 img-download" id="previewDepan" style="max-height: 180px; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_rumah/noimage.png') ?>'">
                <?php if ($editable): ?>
                    <input type="file" name="foto_depan" id="fotoDepan" class="form-control form-control-sm" accept="image/*" capture="environment">
                <?php endif; ?>
            </div>

            <!-- Foto Rumah Dalam -->
            <div class="col-md-3 mb-3 text-center">
                <label class="fw-semibold d-block small">Ruang Tamu / Dalam</label>
                <img src="<?= base_url($foto['dalam'] ?? 'data/usulan/foto_rumah_dalam/noimage.png') ?>"
                    class="img-fluid rounded border mb-2 img-download" id="previewDalam" style="max-height: 180px; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_rumah_dalam/noimage.png') ?>'">
                <?php if ($editable): ?>
                    <input type="file" name="foto_dalam" id="fotoDalam" class="form-control form-control-sm" accept="image/*" capture="environment">
                <?php endif; ?>
            </div>

            <!-- 🚀 TAMBAHAN: Foto Kamar Mandi -->
            <div class="col-md-3 mb-3 text-center">
                <label class="fw-semibold d-block small">Kamar Mandi / WC</label>
                <img src="<?= base_url($foto['kamar_mandi'] ?? 'data/usulan/foto_kamar_mandi/noimage.png') ?>"
                    class="img-fluid rounded border mb-2 img-download" id="previewKamarMandi" style="max-height: 180px; cursor: pointer; object-fit: cover; width: 100%;" onerror="this.src='<?= base_url('data/usulan/foto_kamar_mandi/noimage.png') ?>'">
                <?php if ($editable): ?>
                    <input type="file" name="foto_kamar_mandi" id="fotoKamarMandi" class="form-control form-control-sm" accept="image/*" capture="environment">
                <?php endif; ?>
            </div>
        </div>

        <?php if ($editable): ?>
            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                    <i class="fas fa-upload me-1"></i> Upload Semua Foto
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- ============================== -->
<!-- 📡 JS SAVE FOTO & PREVIEW -->
<!-- ============================== -->
<script>
    // $('#formFoto').on('submit', function(e) {
    //     e.preventDefault();

    //     if (typeof window.cekKelengkapanAnggota === 'function') {
    //         if (!window.cekKelengkapanAnggota()) {
    //             const tabTrigger = document.querySelector('[href="#tab-anggota"], [data-bs-target="#tab-anggota"]');
    //             if (tabTrigger) new bootstrap.Tab(tabTrigger).show();
    //             return;
    //         }
    //     }

    //     const formData = new FormData(this);
    //     Swal.fire({
    //         title: 'Menyimpan Foto...',
    //         text: 'Mohon tunggu sebentar.',
    //         allowOutsideClick: false,
    //         didOpen: () => Swal.showLoading()
    //     });

    //     $.ajax({
    //         url: '<?= base_url("pembaruan-keluarga/save-foto") ?>',
    //         type: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function(res) {
    //             if (res.status === 'success') {
    //                 Swal.fire({
    //                     icon: 'success',
    //                     title: 'Berhasil!',
    //                     text: res.message,
    //                     timer: 1500,
    //                     showConfirmButton: false
    //                 }).then(() => {
    //                     window.history.replaceState(null, null, window.location.pathname);
    //                     window.location.reload();
    //                 });
    //             } else {
    //                 Swal.fire('Gagal!', res.message, 'error');
    //             }
    //         },
    //         error: function() {
    //             Swal.fire('Error!', 'Tidak dapat mengirim data ke server.', 'error');
    //         }
    //     });
    // });

    // ==========================================
    // 📸 SUBMIT FORM FOTO & GEOTAG
    // ==========================================
    $('#formFoto, #formFotoGeotag').on('submit', function(e) {
        e.preventDefault();

        // 🚀 Mbah sudah membuang 'window.cekKelengkapanAnggota' dari sini!
        // Operator kini bebas menyimpan foto kapan pun tanpa dicegat Tab Anggota.

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

                    // 🚀 Mbah juga sudah membuang 'window.location.reload()'
                    // Halaman tidak akan berkedip/reload, operator bisa langsung lanjut klik Tab Aset!
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Tidak dapat mengirim data ke server.', 'error');
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('img-download')) {
            const a = document.createElement('a');
            a.href = e.target.src;
            a.download = e.target.src.split('/').pop() || 'gambar.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    });

    function previewImage(input, targetId) {
        if (input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById(targetId).src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    }

    // 🚀 DITAMBAHKAN KamarMandi KE DALAM ARRAY PREVIEW
    ['Ktp', 'Depan', 'Dalam', 'KamarMandi'].forEach(suffix => {
        const input = document.getElementById('foto' + suffix);
        if (input) input.addEventListener('change', e => previewImage(e.target, 'preview' + suffix));
    });

    // 🚀 DITAMBAHKAN KamarMandi KE DALAM ARRAY KOMPRESI IMAGE
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