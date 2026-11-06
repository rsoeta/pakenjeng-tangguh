<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<script src="<?= base_url('assets/js/tinymce/tinymce.min.js'); ?>"></script>

<div class="content-wrapper mt-0">
    <div class="content-header">
        <h3 class="mb-2">✏️ Ubah Artikel</h3>
    </div>

    <section class="content">

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="post" action="<?= base_url('/admin/articles/update/' . $article['id']) ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <input type="hidden" name="id" value="<?= $article['id'] ?>">

                    <!-- Judul -->
                    <div class="mb-3">
                        <label class="form-label">Judul Artikel</label>
                        <input type="text" class="form-control" name="title"
                            value="<?= esc($article['title']) ?>" required>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="draft" <?= $article['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="publish" <?= $article['status'] == 'publish' ? 'selected' : '' ?>>Publish</option>
                        </select>
                    </div>

                    <!-- Gambar -->
                    <div class="mb-3">
                        <label class="form-label">Gambar Utama</label><br>

                        <?php
                        $imageUrl = $article['image']
                            ? base_url('uploads/articles/' . $article['image'])
                            : base_url('assets/images/image_not_available.jpg');
                        ?>

                        <img src="<?= $imageUrl ?>" id="editPreview"
                            style="width:140px;height:140px;object-fit:cover;border-radius:8px;border:1px solid #ced4da;box-shadow:0 2px 6px rgba(0,0,0,0.08);">
                        <div id="standalone_preview_note" class="small text-muted mt-1"><i class="fas fa-image me-1"></i> Gambar saat ini</div>

                        <input type="file" class="form-control mt-2" id="standalone_edit_image" name="image" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika gambar tidak ingin diubah.</small>
                    </div>

                    <!-- Konten -->
                    <div class="mb-3">
                        <label class="form-label">Isi Artikel</label>
                        <textarea id="editEditor" name="description"><?= esc($article['description']) ?></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Artikel
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </section>
</div>

<script>
    tinymce.init({
        selector: '#editEditor',
        height: 450,
        plugins: 'image link lists table code media',
        toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | image link media | code',
        images_upload_url: '<?= base_url("admin/articles/upload-image") ?>',
        automatic_uploads: true,
        relative_urls: false,
        remove_script_host: false,
        image_title: true,
        file_picker_types: 'image',
        setup: function(editor) {
            editor.on('change keyup NodeChange', function() {
                editor.save();
            });
        },
        images_upload_handler: function(blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                const csrfName = (window.CSRF && window.CSRF.name)
                    ? window.CSRF.name
                    : (document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_test_name');

                const csrfValue = (window.CSRF && window.CSRF.value)
                    ? window.CSRF.value
                    : (document.querySelector('meta[name="csrf-token-value"]')?.content || '');

                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                if (csrfName && csrfValue) {
                    formData.set(csrfName, csrfValue);
                }

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= base_url("admin/articles/upload-image") ?>');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable && typeof progress === 'function') {
                        progress(Math.round((e.loaded / e.total) * 100));
                    }
                };

                xhr.onload = function() {
                    let json = null;
                    try {
                        json = JSON.parse(xhr.responseText);
                    } catch (err) {
                        console.error('TinyMCE upload parse error:', xhr.responseText);
                    }

                    // Perbarui CSRF token dari header atau JSON payload
                    const newToken = xhr.getResponseHeader('X-CSRF-TOKEN') || (json && (json.csrf_hash || json.csrfToken));
                    if (newToken && typeof window.updateCsrfToken === 'function') {
                        window.updateCsrfToken(newToken);
                    }

                    if (xhr.status >= 200 && xhr.status < 300 && json && json.location) {
                        resolve(json.location);
                    } else {
                        const errorMsg = (json && (json.error || json.message))
                            || ('Upload gambar gagal (HTTP ' + xhr.status + ').');
                        reject(errorMsg);
                    }
                };

                xhr.onerror = function() {
                    reject('Terjadi kesalahan jaringan saat mengunggah gambar.');
                };

                xhr.send(formData);
            });
        }
    });
    // Realtime preview gambar artikel
    const standaloneInput = document.getElementById('standalone_edit_image');
    const standalonePreview = document.getElementById('editPreview');
    const standaloneNote = document.getElementById('standalone_preview_note');
    const initialStandaloneSrc = standalonePreview ? standalonePreview.src : '';

    if (standaloneInput && standalonePreview) {
        standaloneInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    standalonePreview.src = e.target.result;
                    if (standaloneNote) {
                        standaloneNote.innerHTML = '<i class="fas fa-eye me-1 text-primary"></i> <span class="text-primary font-weight-bold">Preview gambar baru terpilih</span> (klik Update untuk menyimpan)';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                standalonePreview.src = initialStandaloneSrc;
                if (standaloneNote) {
                    standaloneNote.innerHTML = '<i class="fas fa-image me-1"></i> Gambar saat ini';
                }
            }
        });
    }
</script>

<?= $this->endSection(); ?>