<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper mt-1">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="fw-bold"><i class="fas fa-list-alt text-primary"></i> Manajemen Menu SINDEN</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item">Aplikasi</li>
                        <li class="breadcrumb-item active">Menu</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title fw-bold text-secondary mb-0 mt-1">Struktur Navigasi SINDEN</h5>

                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambahMenu">
                            <i class="fas fa-plus-circle"></i> Tambah Menu Baru
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" style="border-radius: 0 0 8px 8px; overflow: hidden;">
                        <table class="table table-sm table-hover table-striped mb-0 align-middle" id="tableManagementMenu" style="width:100%;">
                            <thead>
                                <tr class="bg-dark text-white text-center" style="font-size: 0.9rem;">
                                    <th style="width: 40px;">No</th>
                                    <th style="width: 50px;">ID</th>
                                    <th class="text-left">Nama Menu / Submenu</th>
                                    <th>Class</th>
                                    <th>Route URL</th>
                                    <th>Icon</th>
                                    <th style="width: 60px;">Parent</th>
                                    <th style="width: 60px;">Akses</th>
                                    <th style="width: 100px;">Dashboard</th>
                                    <th style="width: 60px;">Urutan</th>
                                    <th style="width: 80px;">Status</th>
                                    <th style="width: 60px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.875rem;"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalTambahMenu" role="dialog" aria-labelledby="modalTambahMenuLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                        <div class="modal-header bg-primary text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                            <h5 class="modal-title fw-bold" id="modalTambahMenuLabel"><i class="fas fa-sliders-h"></i> Form Menu Baru</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="form_tambah_menu" method="POST">
                            <div class="modal-body p-4">
                                <div class="row">
                                    <div class="col-12 col-md-6 form-group mb-3">
                                        <label class="fw-bold small text-secondary">Nama Menu <span class="text-danger">*</span></label>
                                        <input type="text" name="tm_nama" id="new_tm_nama" class="form-control form-control-sm" placeholder="Contoh: PDTT 2025" required spellcheck="false">
                                    </div>
                                    <div class="col-12 col-md-6 form-group mb-3">
                                        <label class="fw-bold small text-secondary">Route URL Link <span class="text-danger">*</span></label>
                                        <input type="text" name="tm_url" id="new_tm_url" class="form-control form-control-sm" placeholder="Contoh: pdtt/2025" required spellcheck="false">
                                    </div>
                                    <div class="col-12 col-md-6 form-group mb-3">
                                        <label class="fw-bold small text-secondary">Icon Class (FontAwesome)</label>
                                        <input type="text" name="tm_icon" id="new_tm_icon" class="form-control form-control-sm" placeholder="Contoh: fas fa-shield-alt" spellcheck="false">
                                    </div>
                                    <div class="col-12 col-md-6 form-group mb-3">
                                        <label class="fw-bold small text-secondary">CSS Class Code</label>
                                        <input type="text" name="tm_class" id="new_tm_class" class="form-control form-control-sm" placeholder="Kosongkan jika tidak ada" spellcheck="false">
                                    </div>

                                    <div class="col-12 col-md-6 form-group mb-3">
                                        <label class="fw-bold small text-secondary">Induk Parent Menu</label>
                                        <select name="tm_parent_id" id="new_tm_parent_id" class="form-control form-control-sm">
                                            <option value="0" class="font-weight-bold text-primary">--- Menu Utama (No Parent) ---</option>
                                            <?php
                                            $buildMenuDropdown = function ($menus, $parentId = 0, $level = 0) use (&$buildMenuDropdown) {
                                                foreach ($menus as $m) {
                                                    if ($m['tm_parent_id'] == $parentId) {
                                                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                                        $icon = $level > 0 ? '&#x21B3; ' : '';
                                                        echo '<option value="' . $m['tm_id'] . '">' . $indent . $icon . esc($m['tm_nama']) . ' [ID: ' . $m['tm_id'] . ']</option>';
                                                        $buildMenuDropdown($menus, $m['tm_id'], $level + 1);
                                                    }
                                                }
                                            };
                                            $buildMenuDropdown($menu);
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 form-group mb-3">
                                        <label class="fw-bold small text-secondary">Batas Grup Akses</label>
                                        <select name="tm_grup_akses" id="new_tm_grup_akses" class="form-control form-control-sm">
                                            <?php foreach ($statusRole as $role): ?>
                                                <option value="<?= $role['id_role'] ?>" <?= $role['id_role'] == 4 ? 'selected' : '' ?>><?= esc($role['nm_role']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4 form-group mb-3">
                                        <label class="fw-bold small text-secondary">Nomor Urutan</label>
                                        <input type="number" name="tm_urutan" id="new_tm_urutan" class="form-control form-control-sm text-center" value="0" min="0">
                                    </div>
                                    <div class="col-12 col-md-4 form-group mb-3">
                                        <label class="fw-bold small text-secondary">Dashboard Shortcut</label>
                                        <select name="tm_is_dashboard" id="new_tm_is_dashboard" class="form-control form-control-sm">
                                            <option value="0">Sembunyi</option>
                                            <option value="1">Tampilkan</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-4 form-group mb-3">
                                        <label class="fw-bold small text-secondary">Status Sistem</label>
                                        <select name="tm_status" id="new_tm_status" class="form-control form-control-sm">
                                            <option value="1">Aktif</option>
                                            <option value="0">Non-Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                <button type="button" class="btn btn-secondary btn-sm shadow-sm" data-dismiss="modal">Batal</button>
                                <button type="submit" id="btnSubmitMenu" class="btn btn-success btn-sm shadow-sm px-3"><i class="fas fa-save"></i> Simpan Menu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        function load_data_menu() {
            $.ajax({
                type: "GET",
                url: "<?= base_url('settings/load_data_menu') ?>",
                dataType: "json",
                success: function(response) {
                    function buildTree(data, parentId = 0, level = 0) {
                        let html = '';
                        // 🚀 BUG FIX: Menangani nilai NULL dari database agar dibaca 0
                        let items = data.filter(m => parseInt(m.tm_parent_id || 0) === parentId);

                        items.forEach((item, index) => {
                            html += renderRow(item, level);
                            html += buildTree(data, parseInt(item.tm_id), level + 1);
                        });
                        return html;
                    }
                    let tableBody = buildTree(response, 0, 0);
                    $('#tableManagementMenu tbody').html(tableBody);
                    if ($.fn.bootstrapToggle) {
                        $('.toggle_checkbox_auto').bootstrapToggle();
                    }
                },
                error: function(xhr, status, error) {
                    // 🚀 BUG FIX: Menangkap error diam-diam
                    console.error("AJAX Error (Load Menu):", xhr.responseText);
                    $('#tableManagementMenu tbody').html('<tr><td colspan="12" class="text-center text-danger"><b>Gagal memuat data. Silakan cek Inspect Element -> Console!</b></td></tr>');
                }
            });
        }

        function renderRow(item, level) {
            let paddingLeft = level * 30;
            let isSubmenu = level > 0;
            let style = isSubmenu ? 'background-color:#ffffff;' : 'font-weight:bold; background-color:#f8f9fa;';

            var row = '<tr class="text-center" style="' + style + '">';
            row += '<td class="align-middle text-muted small">#</td>';
            row += '<td class="table_data_menu align-middle small" data-column-name="tm_id" id="' + item.tm_id + '">' + item.tm_id + '</td>';

            var icon = item.tm_icon ? '<i class="' + item.tm_icon + ' text-primary mr-2"></i>' : '<i class="fas fa-circle text-muted mr-2" style="font-size:0.5rem;"></i>';
            row += '<td class="table_data_menu align-middle text-left" data-column-name="tm_nama" id="' + item.tm_id + '" contenteditable style="padding-left: ' + (paddingLeft + 15) + 'px !important;">' + icon + item.tm_nama + '</td>';

            row += '<td class="table_data_menu align-middle" data-column-name="tm_class" id="' + item.tm_id + '" contenteditable>' + (item.tm_class || '-') + '</td>';
            row += '<td class="table_data_menu align-middle text-left" data-column-name="tm_url" id="' + item.tm_id + '" contenteditable><code class="text-danger">' + item.tm_url + '</code></td>';
            row += '<td class="table_data_menu align-middle text-left small" data-column-name="tm_icon" id="' + item.tm_id + '" contenteditable>' + (item.tm_icon || '') + '</td>';
            row += '<td class="table_data_menu align-middle font-weight-bold text-info" data-column-name="tm_parent_id" id="' + item.tm_id + '" contenteditable>' + item.tm_parent_id + '</td>';
            row += '<td class="table_data_menu align-middle" data-column-name="tm_grup_akses" id="' + item.tm_id + '" contenteditable>' + item.tm_grup_akses + '</td>';

            var chkDash = (item.tm_is_dashboard == '1') ? 'checked' : '';
            row += '<td class="align-middle" data-column-name="tm_is_dashboard" id="' + item.tm_id + '"><input type="checkbox" class="toggle_checkbox_auto" data-toggle="toggle" data-on="<i class=\'fas fa-bolt\'></i>" data-off="-" data-onstyle="info" data-offstyle="secondary" data-size="sm" ' + chkDash + ' value="1"></td>';

            row += '<td class="table_data_menu align-middle font-weight-bold text-primary" data-column-name="tm_urutan" id="' + item.tm_id + '" contenteditable>' + item.tm_urutan + '</td>';
            var chkStat = (item.tm_status == '1') ? 'checked' : '';
            row += '<td class="align-middle" data-column-name="tm_status" id="' + item.tm_id + '"><input type="checkbox" class="toggle_checkbox_auto" data-toggle="toggle" data-on="Y" data-off="N" data-onstyle="success" data-offstyle="danger" data-size="sm" ' + chkStat + ' value="1"></td>';

            row += '<td class="align-middle"><button type="button" class="btn btn-xs btn-outline-danger btn_delete"><i class="fa fa-trash-alt"></i></button></td>';
            row += '</tr>';
            return row;
        }

        load_data_menu();

        $('#form_tambah_menu').on('submit', function(e) {
            e.preventDefault();
            var form_data = $(this).serialize();

            // 🚀 SUNTIKKAN CSRF TOKEN AGAR TIDAK FORBIDDEN
            form_data += '&<?= csrf_token() ?>=<?= csrf_hash() ?>';

            var btn = $('#btnSubmitMenu');
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

            $.ajax({
                type: "POST",
                url: "<?= base_url('settings/insert_data_menu') ?>",
                data: form_data,
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html(originalText);
                    if (res.status === 'success') {
                        $('#modalTambahMenu').modal('hide');
                        $('#form_tambah_menu')[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses! 🎉',
                            text: res.message,
                            width: '320px',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        load_data_menu();
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    console.error(xhr.responseText);
                    alert("Terjadi kesalahan sistem! Cek Console.");
                }
            });
        });

        $(document).on('change', '.toggle_checkbox_auto', function() {
            var td = $(this).closest('td');
            var id = td.attr('id');
            var table_column = td.attr('data-column-name');
            var value = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                type: "post",
                url: "<?= base_url('settings/update_data_menu') ?>",
                data: {
                    id: id,
                    table_column: table_column,
                    value: value,
                    // 🚀 SUNTIKKAN CSRF TOKEN
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(data) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Perubahan disimpan!'
                    });
                }
            });
        });

        // PASTE CSRF JUGA UNTUK EVENT DELETE & BLUR BILA PERLU (formatnya sama seperti di atas)

        $(document).on('blur', '.table_data_menu[contenteditable]', function() {
            var id = $(this).attr('id');
            var table_column = $(this).attr('data-column-name');
            var value = $(this).text().trim();
            if (table_column === 'tm_url') {
                value = $(this).find('code').text().trim() || value;
            }

            $.ajax({
                type: "post",
                url: "<?= base_url('settings/update_data_menu') ?>", // 🚀 FIX PATH URL
                data: {
                    id: id,
                    table_column: table_column,
                    value: value
                },
                success: function(data) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Perubahan disimpan!'
                    });
                }
            });
        });

        $(document).on('click', '.btn_delete', function() {
            var tr = $(this).parents("tr");
            var id = tr.find("td:eq(1)").text();

            Swal.fire({
                title: 'Hapus Data?',
                text: "Menu ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                width: '320px',
                customClass: {
                    title: 'fs-5',
                    content: 'fs-6'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "post",
                        url: "<?= base_url('settings/delete_data_menu') ?>", // 🚀 FIX PATH URL
                        data: {
                            id: id
                        },
                        success: function(data) {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Data berhasil dihapus'
                            });
                            load_data_menu();
                        }
                    });
                }
            });
        });

    });
</script>
<?= $this->endSection(); ?>