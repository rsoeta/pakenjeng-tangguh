<div class="card card-success card-outline">
    <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" id="wa-tab" role="tablist">

            <li class="nav-item">
                <a class="nav-link active" id="wa-api-tab" data-toggle="pill"
                    href="#wa-api" role="tab">
                    <i class="fas fa-key"></i> API Settings
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" id="wa-fonnte-tab" data-toggle="tab" href="#wa-fonnte" role="tab">
                    <i class="fas fa-random"></i> Fallback Fonnte
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" id="wa-template-tab" data-toggle="pill"
                    href="#wa-template" role="tab">
                    <i class="fas fa-edit"></i> Template Pesan
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" id="wa-preview-tab" data-toggle="pill"
                    href="#wa-preview" role="tab">
                    <i class="fas fa-eye"></i> Preview
                </a>
            </li>

        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="wa-tabContent">

            <!-- ======================================================
                 1) API SETTINGS
                 ====================================================== -->
            <div class="tab-pane fade show active" id="wa-api" role="tabpanel">

                <form id="form_api">
                    <div class="form-group">
                        <label>API Key Alatwa.com</label>
                        <input type="text" name="api_key" id="api_key"
                            class="form-control"
                            value="<?= esc($wa_setting['api_key'] ?? '') ?>"
                            placeholder="Masukkan API Key">
                    </div>

                    <div class="form-group">
                        <label>Device ID</label>
                        <input type="text" name="device" id="device"
                            class="form-control"
                            value="<?= esc($wa_setting['device'] ?? '') ?>"
                            placeholder="Masukkan device ID">
                    </div>

                    <!-- tambah sender -->
                    <div class="form-group">
                        <label>Sender (Nomor Pengirim)</label>
                        <input type="text" name="sender" id="sender"
                            class="form-control"
                            value="<?= esc($wa_setting['sender'] ?? '') ?>"
                            placeholder="Masukkan nomor pengirim (sender)">
                    </div>

                    <div class="text-right mt-3">
                        <button type="button" id="save_api" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan API
                        </button>

                        <button type="button" id="test_api" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Test Kirim
                        </button>
                    </div>
                </form>
            </div>

            <!-- ======================================================
                 2) KONFIGURASI FONNTE
                 ====================================================== -->
            <div class="tab-pane fade" id="wa-fonnte" role="tabpanel">

                <form id="form_fonnte">

                    <div class="form-group">
                        <label>Fonnte Token</label>
                        <input type="text" name="fonnte_token" class="form-control"
                            value="<?= esc($wa_setting['fonnte_token'] ?? '') ?>"
                            placeholder="Masukkan token FONNTE">
                    </div>

                    <div class="form-group">
                        <label>Fonnte Sender (optional)</label>
                        <input type="text" name="fonnte_sender" class="form-control"
                            value="<?= esc($wa_setting['fonnte_sender'] ?? '') ?>"
                            placeholder="Nomor / Sender Device FONNTE">
                    </div>

                    <div class="form-group">
                        <label>Aktifkan Fallback</label><br>
                        <input type="checkbox" name="fallback_enabled"
                            <?= !empty($wa_setting['fallback_enabled']) ? 'checked' : '' ?>>
                        <span class="ml-1">Gunakan Fonnte jika Alatwa gagal</span>
                    </div>

                    <button type="button" id="save_fonnte" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>

                    <button type="button" id="test_fonnte" class="btn btn-success ml-2">
                        <i class="fas fa-paper-plane"></i> Test Kirim via Fonnte
                    </button>

                </form>
            </div>

            <!-- ======================================================
                 2) TEMPLATE PESAN
                 ====================================================== -->
            <div class="tab-pane fade" id="wa-template" role="tabpanel">

                <form id="form_template">

                    <div class="form-group">
                        <label>Isi Template</label>

                        <textarea name="template" id="template"
                            class="form-control" rows="8"
                            placeholder="Isi template pesan..."><?= esc($wa_setting['template_groundcheck'] ??
                                                                    "{{titleApp()}} - Notifications 

                    Assalamu’alaikum.
                    Mohon memeriksa kembali *Pembaruan Desil Hasil Groundcheck.*

                    * No KK: {{no_kk}}
                    * Nama KK: {{nama_kk}}

                    _*Pesan Otomatis {{titleApp()}}.*_") ?></textarea>
                    </div>

                    <h6><strong>Placeholder yang dapat digunakan:</strong></h6>
                    <ul>
                        <li><code>{{no_kk}}</code></li>
                        <li><code>{{nama_kk}}</code></li>
                        <li><code>{{nik_kk}}</code></li>
                        <li><code>{{kode_desa}}</code></li>
                        <li><code>{{titleApp()}}</code></li>
                    </ul>

                    <div class="text-right mt-3">
                        <button type="button" id="save_template" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Template
                        </button>
                    </div>

                </form>

            </div>

            <!-- ======================================================
                 3) PREVIEW TEMPLATE
                 ====================================================== -->
            <div class="tab-pane fade" id="wa-preview" role="tabpanel">

                <div id="preview_box" class="border rounded p-3 bg-light"
                    style="min-height:150px; white-space: pre-line;">
                    <em>Klik tombol “Preview Pesan” untuk melihat hasil…</em>
                </div>

                <div class="text-right mt-3">
                    <button type="button" id="show_preview" class="btn btn-info">
                        <i class="fas fa-sync"></i> Preview Pesan
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- ==========================================================
     SCRIPT AJAX UNTUK SAVE / PREVIEW / TEST SEND
     ========================================================== -->
<script>
    const baseUrl = "<?= base_url() ?>";
    $(document).ready(function() {

        // SAVE API
        $('#save_api').click(function() {
            $.post('<?= base_url("pengaturan_wa/save_api") ?>', {
                    api_key: $('#api_key').val(),
                    device: $('#device').val(),
                    sender: $('#sender').val()
                },
                function(res) {
                    alert("API berhasil disimpan!");
                }, 'json');
        });

        // SAVE TEMPLATE
        $('#save_template').click(function() {
            $.post('<?= base_url("pengaturan_wa/save_template") ?>', {
                    template: $('#template').val()
                },
                function(res) {
                    alert("Template berhasil disimpan!");
                }, 'json');
        });

        // PREVIEW TEMPLATE
        $('#show_preview').click(function() {
            $.post('<?= base_url("pengaturan_wa/preview") ?>', {
                    template: $('#template').val()
                },
                function(res) {
                    $('#preview_box').html(res.preview);
                }, 'json');
        });

        // TEST SEND WA
        $('#test_api').click(function() {
            $.post('<?= base_url("pengaturan_wa/test") ?>', {}, function(res) {
                alert("Response dari alatwa.com:\n" + res.response);
            }, 'json');
        });

        // SAVE FONNTE
        $('#save_fonnte').on('click', function() {
            $.ajax({
                url: baseUrl + '/pengaturan_wa/save_fonnte',
                type: 'POST',
                data: $('#form_fonnte').serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        Swal.fire('Berhasil', 'Pengaturan Fonnte disimpan.', 'success');
                    } else {
                        Swal.fire('Gagal', 'Tidak dapat menyimpan.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Koneksi server gagal.', 'error');
                }
            });
        });

        // TEST KIRIM FONNTE
        $('#test_fonnte').on('click', function() {

            Swal.fire({
                title: 'Kirim Test?',
                text: "Pesan test Fonnte akan dikirim ke nomor Anda sendiri.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim!'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: baseUrl + '/pengaturan_wa/test_fonnte',
                        type: 'POST',
                        dataType: 'json',

                        success: function(res) {
                            if (res.status) {
                                Swal.fire('Berhasil', 'Pesan Test Fonnte terkirim.', 'success');
                                console.log(res.response);
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },

                        error: function() {
                            Swal.fire('Error', 'Koneksi server gagal.', 'error');
                        }

                    });
                }
            });

        });

    });
</script>