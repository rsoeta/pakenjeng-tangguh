<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .article-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
    }
</style>

<script src="<?= base_url('assets/js/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/admin/articles.table.js'); ?>"></script>


<div class="content-wrapper mt-0">

    <!-- HEADER -->
    <div class="content-header d-flex justify-content-between align-items-center">
        <h3 class="mb-2">📰 Artikel</h3>
        <span class="text-muted"><?= date('d M Y') ?></span>
    </div>

    <section class="content">

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-3 col-12 mb-3 border-right">
                        <!-- Sidebar menu -->
                        <div class="nav flex-column nav-pills" id="article-nav" role="tablist">

                            <a class="nav-link active" id="tab-list" data-bs-toggle="pill"
                                href="#pane-list" role="tab">
                                <i class="fas fa-list"></i> Daftar Artikel
                            </a>

                            <a class="nav-link" id="tab-create" data-bs-toggle="pill"
                                href="#pane-create" role="tab">
                                <i class="fas fa-plus-circle"></i> Tulis Artikel Baru
                            </a>

                            <a class="nav-link d-none" id="tab-edit" data-bs-toggle="pill"
                                href="#pane-edit" role="tab">
                                <i class="fas fa-edit"></i> Ubah Artikel
                            </a>

                            <a class="nav-link" id="tab-settings" data-bs-toggle="pill"
                                href="#pane-settings" role="tab">
                                <i class="fas fa-cog"></i> Pengaturan
                            </a>
                        </div>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="col-md-9 col-12">
                        <div class="tab-content">

                            <!-- =======================================================================
                                1. LIST ARTIKEL
                            ======================================================================== -->
                            <div class="tab-pane fade show active" id="pane-list" role="tabpanel">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="mb-0">Daftar Artikel</h5>
                                    <button id="btnReloadArticles" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-sync-alt"></i> Reload
                                    </button>
                                </div>

                                <table id="tableArticles" class="table table-striped table-bordered" width="100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Gambar</th> <!-- WAJIB DITAMBAHKAN -->
                                            <th>Judul</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <!-- =======================================================================
                                2. FORM BUAT ARTIKEL BARU
                            ======================================================================== -->
                            <div class="tab-pane fade" id="pane-create" role="tabpanel">

                                <h5>Tulis Artikel Baru</h5>
                                <hr>

                                <form method="post" action="<?= base_url('/admin/articles/store') ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Judul Artikel</label>
                                        <input class="form-control" name="title" required placeholder="Judul artikel...">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="draft">Draft</option>
                                            <option value="publish">Publish</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Isi Artikel</label>
                                        <textarea id="articleCreateEditor" name="description"></textarea>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Simpan Artikel
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- =======================================================================
                                3. FORM EDIT ARTIKEL
                            ======================================================================== -->
                            <div class="tab-pane fade" id="pane-edit" role="tabpanel">
                                <h5>Ubah Artikel</h5>
                                <hr>

                                <form id="editArticleForm" method="post" action="#">
                                    <input type="hidden" name="id" id="edit_id">

                                    <div class="mb-3">
                                        <label class="form-label">Judul Artikel</label>
                                        <input class="form-control" name="title" id="edit_title" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select" id="edit_status">
                                            <option value="draft">Draft</option>
                                            <option value="publish">Publish</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Isi Artikel</label>
                                        <textarea id="articleEditEditor" name="description"></textarea>
                                    </div>

                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Artikel
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- =======================================================================
                                4. SETTINGS
                            ======================================================================== -->
                            <div class="tab-pane fade" id="pane-settings" role="tabpanel">
                                <h5>Pengaturan Artikel</h5>
                                <p class="text-muted">Menu pengaturan artikel dapat ditambahkan di sini.</p>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>
</div>

<!-- TinyMCE INITIALIZER -->
<script>
    function initTiny(selector) {
        if (typeof tinymce === "undefined") {
            console.error("TinyMCE belum terload!");
            return;
        }
        tinymce.init({
            selector: selector,
            plugins: 'image link lists table code',
            height: 350,
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
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
    }

    initTiny('#articleCreateEditor');
    initTiny('#articleEditEditor');

    $('#editArticleForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        const payload = $(this).serialize(); // plus description from tinymce if needed
        payload.description = tinymce.get('articleEditEditor').getContent();

        $.post('/admin/articles/update/' + id, payload)
            .done(function(res) {
                if (res.success) {
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#tab-list').trigger('click');
                    $('#tab-edit').addClass('d-none'); // hide edit tab if desired
                    $('#tableArticles').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .fail(function() {
                Swal.fire('Error', 'Gagal memperbarui artikel.', 'error');
            });
    });

    render: img => img ?
        `<img src="${img}" class="article-thumb">` :
        '<span class="text-muted">Tidak ada</span>'
</script>

<?= $this->endSection(); ?>