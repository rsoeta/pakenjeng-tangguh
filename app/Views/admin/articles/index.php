<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>
<!-- <script src="https://cdn.tiny.cloud/1/NO-API-KEY/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> -->
<script src="<?= base_url('assets/js/tinymce/tinymce.min.js'); ?>"></script>

<div class="content-wrapper mt-0">
    <div class="content-header">
        <h3 class="mb-2">📰 <?= $title; ?></h3>
    </div>

    <section class="content">

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i>
                    Artikel Baru
                </h3>
            </div>
            <div class="card-body">
                <h4>Left Sided</h4>
                <div class="row">
                    <div class="col-5 col-sm-3">
                        <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-home" role="tab" aria-controls="vert-tabs-home" aria-selected="true">Daftar Artikel</a>
                            <a class="nav-link" id="vert-tabs-profile-tab" data-toggle="pill" href="#vert-tabs-profile" role="tab" aria-controls="vert-tabs-profile" aria-selected="false">Tulis Artikel</a>
                            <a class="nav-link" id="vert-tabs-messages-tab" data-toggle="pill" href="#vert-tabs-messages" role="tab" aria-controls="vert-tabs-messages" aria-selected="false">Ubah Artikel</a>
                            <a class="nav-link" id="vert-tabs-settings-tab" data-toggle="pill" href="#vert-tabs-settings" role="tab" aria-controls="vert-tabs-settings" aria-selected="false">Settings</a>
                        </div>
                    </div>
                    <div class="col-7 col-sm-9">
                        <div class="tab-content" id="vert-tabs-tabContent">
                            <div class="tab-pane text-left fade show active" id="vert-tabs-home" role="tabpanel" aria-labelledby="vert-tabs-home-tab">
                                <!-- table lorem ipsum -->
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Bordered Table</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10px">#</th>
                                                    <th>Task</th>
                                                    <th>Progress</th>
                                                    <th style="width: 40px">Label</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1.</td>
                                                    <td>Update software</td>
                                                    <td>
                                                        <div class="progress progress-xs">
                                                            <div class="progress-bar progress-bar-danger" style="width: 55%"></div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-danger">55%</span></td>
                                                </tr>
                                                <tr>
                                                    <td>2.</td>
                                                    <td>Clean database</td>
                                                    <td>
                                                        <div class="progress progress-xs">
                                                            <div class="progress-bar bg-warning" style="width: 70%"></div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-warning">70%</span></td>
                                                </tr>
                                                <tr>
                                                    <td>3.</td>
                                                    <td>Cron job running</td>
                                                    <td>
                                                        <div class="progress progress-xs progress-striped active">
                                                            <div class="progress-bar bg-primary" style="width: 30%"></div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-primary">30%</span></td>
                                                </tr>
                                                <tr>
                                                    <td>4.</td>
                                                    <td>Fix and squish bugs</td>
                                                    <td>
                                                        <div class="progress progress-xs progress-striped active">
                                                            <div class="progress-bar bg-success" style="width: 90%"></div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-success">90%</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer clearfix">
                                        <ul class="pagination pagination-sm m-0 float-right">
                                            <li class="page-item"><a class="page-link" href="#">«</a></li>
                                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                            <li class="page-item"><a class="page-link" href="#">»</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="vert-tabs-profile" role="tabpanel" aria-labelledby="vert-tabs-profile-tab">
                                <form method="post" action="<?= base_url('/admin/articles/store') ?>">
                                    <input class="form-control" name="title" placeholder="Judul" />
                                    <select name="status" class="custom-select form-control-borderded mt-2 mb-2">
                                        <option value="draft">Draft</option>
                                        <option value="publish">Publish</option>
                                    </select>
                                    <textarea id="tinyDesc" name="description"></textarea>
                                    <!-- buat tombol submit disebelah kanan -->
                                    <div class="modal-footer">
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="vert-tabs-messages" role="tabpanel" aria-labelledby="vert-tabs-messages-tab">
                                Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
                            </div>
                            <div class="tab-pane fade" id="vert-tabs-settings" role="tabpanel" aria-labelledby="vert-tabs-settings-tab">
                                Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>

    </section>
</div>

<script>
    tinymce.init({
        selector: '#tinyDesc',
        plugins: 'image link lists table code',
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
        images_upload_url: '<?= base_url("admin/articles/upload-image") ?>',
        images_upload_credentials: true, // sertakan cookie & headers (jika perlu sesi)
        automatic_uploads: true,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        // fallback handler jika TinyMCE mengirim field "file" atau "image"
        images_upload_handler: function(blobInfo, success, failure) {
            var formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            fetch('<?= base_url("admin/articles/upload-image") ?>', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(r => r.json()).then(json => {
                if (json.location) success(json.location);
                else failure('Upload gagal');
            }).catch(err => failure('Upload error: ' + err));
        }
    });
</script>

<?= $this->endSection(); ?>