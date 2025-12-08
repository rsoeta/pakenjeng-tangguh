<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<script src="<?= base_url('assets/js/tinymce/tinymce.min.js'); ?>"></script>

<div class="content-wrapper mt-0">
    <div class="content-header">
        <h3 class="mb-2">📝 Buat Artikel Baru</h3>
    </div>

    <section class="content">

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="post" action="<?= base_url('/admin/articles/store') ?>" enctype="multipart/form-data">

                    <!-- Judul -->
                    <div class="mb-3">
                        <label class="form-label">Judul Artikel</label>
                        <input type="text" class="form-control" name="title" placeholder="Judul artikel..." required>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="draft">Draft</option>
                            <option value="publish">Publish</option>
                        </select>
                    </div>

                    <!-- Gambar -->
                    <div class="mb-3">
                        <label class="form-label">Gambar Utama Artikel</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Opsional. Jika disertakan akan muncul sebagai thumbnail.</small>
                    </div>

                    <!-- Konten -->
                    <div class="mb-3">
                        <label class="form-label">Isi Artikel</label>
                        <textarea id="createEditor" name="description"></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Artikel
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </section>
</div>

<script>
    tinymce.init({
        selector: '#createEditor',
        height: 450,
        plugins: 'image link lists table code media',
        toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | image link media | code',
        images_upload_url: '<?= base_url("admin/articles/upload-image") ?>',
        automatic_uploads: true,
        relative_urls: false,
        remove_script_host: false,
        images_upload_handler: function(blobInfo, success, failure) {
            let formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            fetch('<?= base_url("admin/articles/upload-image") ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(json => json.location ? success(json.location) : failure("Upload gagal"))
                .catch(err => failure("Upload error: " + err));
        }
    });
</script>

<?= $this->endSection(); ?>