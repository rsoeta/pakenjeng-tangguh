<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>


<div class="content-wrapper mt-1">
    <style>
        #tabelUser_wrapper .dt-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            /* supaya tetap responsif */
            margin-bottom: 10px;
        }

        #tabelUser_wrapper .dt-left,
        #tabelUser_wrapper .dt-middle,
        #tabelUser_wrapper .dt-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        #customFilter select {
            height: 30px !important;
            padding: 2px 6px !important;
        }

        #customFilter label {
            font-size: 12px;
            margin-bottom: -2px;
            display: block;
        }

        .gap-2>* {
            margin-right: 8px;
        }

        .dt-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 10px;
            gap: 20px;
        }

        .dt-filters {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .dt-filters label {
            font-size: 12px;
            margin-bottom: -2px;
            display: block;
        }

        .dt-filters select {
            height: 30px !important;
            padding: 2px 6px !important;
        }
    </style>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/pages'); ?>">Home</a></li>
                        <li class="breadcrumb-item active"><?= $title; ?></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- DataTales Example -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="row">
                        <div class="col">
                            <!-- Button trigger modal -->
                            <h4 class="text-center"><?= $title; ?></h4>
                            <div class="card-tools d-flex gap-2 justify-content-end">
                                <button class="btn btn-info btn-sm shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRole" aria-controls="offcanvasRole">
                                    <i class="fas fa-user-tag"></i> Kelola Role
                                </button>

                                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalAdd">
                                    <i class="fa fa-plus fa-sm"></i> Tambah User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <br>
                    <div class="tengah">
                        <!-- <div id="customFilter"></div> -->
                        <div class="dt-toolbar">

                            <!-- kiri: show entries -->
                            <div class="dt-left"></div>

                            <!-- tengah: filter custom -->
                            <div class="dt-filters">

                                <div>
                                    <label>Level User</label>
                                    <select id="filterRole" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        <?php foreach ($roles as $r): ?>
                                            <option value="<?= $r['nm_role']; ?>"><?= $r['nm_role']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label>Status</label>
                                    <select id="filterStatus" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <div>
                                    <label>No. RW</label>
                                    <select id="filterRW" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        <?php
                                        $rwList = array_unique(array_column($users, 'level'));
                                        sort($rwList);
                                        foreach ($rwList as $rw) {
                                            echo "<option value='$rw'>$rw</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>

                            <!-- kanan: search -->
                            <div class="dt-right"></div>

                        </div>


                        <table id="tabelUser" class="table table-sm table-hover table-head-fixed compact" style="width: 100%;">
                            <thead class="text-primary">
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA LENGKAP</th>
                                    <th>WILAYAH AKTIF</th>
                                    <th>NO. RW</th>
                                    <th>NIK</th>
                                    <th>EMAIL</th>
                                    <th>NO. HP</th>
                                    <th>LEVEL</th>
                                    <th>USER IMAGE</th>
                                    <th>STATUS</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($users as $row) : ?>
                                    <tr>
                                        <td scope="row"><?= $i; ?></td>
                                        <td><?= $row['fullname']; ?></td>
                                        <td><?= tampilWilayahHumanis($row['wilayah_tugas']); ?></td>
                                        <td><?= $row['level']; ?></td>
                                        <td><?= $row['nik']; ?></td>
                                        <td><?= $row['email']; ?></td>
                                        <td><?= $row['nope']; ?></td>
                                        <td>
                                            <?php if ($row['role_id'] == 1) {
                                                $badges = 'bg-danger';
                                            } elseif ($row['role_id'] == 2) {
                                                $badges = 'bg-primary';
                                            } elseif ($row['role_id'] == 3) {
                                                $badges = 'bg-success';
                                            } elseif ($row['role_id'] == 4) {
                                                $badges = 'bg-warning';
                                            } elseif ($row['role_id'] == 6) {
                                                $badges = 'bg-info';
                                            } else {
                                                $badges = 'bg-secondary';
                                            }
                                            ?>
                                            <?php foreach ($roles as $role) { ?>
                                                <?php if ($role['id_role'] == $row['role_id']) {
                                                    echo '<span class="badge ' . $badges . '">' . $role['nm_role'] . '</span>';
                                                } ?>
                                            <?php } ?>
                                        </td>
                                        <td><a href="<?= Foto_Profil($row['user_image'], 'profil'); ?>" data-lightbox="<?= $row['fullname']; ?>" data-title="<?= $row['fullname']; ?>"><img src="<?= Foto_Profil($row['user_image'], 'profil'); ?>" alt="" style="border: 2px solid #ddd; border-radius: 5px; padding: 1px; width: 30px;"></a></td>
                                        <td>
                                            <!-- 🚀 TOMBOL STATUS AJAX -->
                                            <button type="button"
                                                class="btn <?= ($row['status'] == 1) ? 'btn-warning' : 'btn-dark' ?> btn-sm rounded-pill btnToggleStatus shadow-sm"
                                                data-id="<?= $row['id'] ?>"
                                                data-status="<?= $row['status'] ?>">
                                                <?= ($row['status'] == 1) ? 'Active' : 'Inactive' ?>
                                            </button>

                                            <!-- tampilkan tombol reset -->
                                            <button class="btn btn-info btn-sm rounded-pill shadow-sm" onclick="requestReset('<?= $row['id']; ?>')">
                                                Reset Password
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="view('<?= $row['id']; ?>')">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapus('<?= $row['id']; ?>','<?= $row['fullname']; ?>', this)">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="viewmodal" style="display: none;"></div>

<div class="offcanvas offcanvas-end shadow" tabindex="-1" id="offcanvasRole" aria-labelledby="offcanvasRoleLabel" style="width: 400px;">
    <div class="offcanvas-header bg-dark text-white">
        <h5 class="offcanvas-title fw-bold" id="offcanvasRoleLabel"><i class="fas fa-user-tag me-2"></i> Kelola Role Pengguna</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body bg-light">

        <div class="card shadow-sm mb-4 border-0">
            <div class="card-body bg-white rounded">
                <form id="formRole">
                    <input type="hidden" id="old_id_role" name="old_id_role">

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label fw-bold text-primary small">ID Role</label>
                            <input type="number" class="form-control" id="id_role" name="id_role" placeholder="Auto">
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-bold text-primary small">Nama Role / Level</label>
                            <input type="text" class="form-control" id="nm_role" name="nm_role" placeholder="Contoh: Petugas SE 2026" required autocomplete="off">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-secondary d-none" id="btnBatalRole"><i class="fas fa-times"></i> Batal</button>
                        <button type="submit" class="btn btn-sm btn-success shadow-sm" id="btnSimpanRole"><i class="fas fa-save"></i> Simpan Role</button>
                    </div>
                </form>
            </div>
        </div>

        <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-list me-1"></i> Daftar Role Saat Ini</h6>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-sm table-striped table-hover mb-0" id="tabelRoleOffcanvas">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="15%" class="text-center">ID</th>
                            <th>Nama Role</th>
                            <th width="25%" class="text-center"><i class="fas fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $r): ?>
                            <tr>
                                <td class="text-center fw-bold align-middle"><?= $r['id_role'] ?></td>
                                <td class="align-middle"><?= esc($r['nm_role']) ?></td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-xs btn-outline-primary btnEditRole" data-id="<?= $r['id_role'] ?>" data-nama="<?= esc($r['nm_role']) ?>"><i class="fas fa-edit"></i></button>
                                    <?php if (!in_array($r['id_role'], [1, 2, 99])): // Lindungi role inti agar tidak terhapus 
                                    ?>
                                        <button class="btn btn-xs btn-outline-danger btnHapusRole" data-id="<?= $r['id_role'] ?>"><i class="fas fa-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form. <?= $title1; ?></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form action="/user_tambah" method="POST" id="mainform">
                        <?= csrf_field(); ?>
                        <div class="form-group my-1">
                            <input type="text" class="form-control form-control form-control-user" name="fullname" aria-describedby="emailHelp" placeholder="Masukan Nama Lengkap" value="<?= set_value('fullname'); ?>">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group my-1">
                                    <input type="numeric" class="form-control form-control form-control-user" name="nik" aria-describedby="emailHelp" placeholder="Masukan No. KTP/NIK" value="<?= set_value('nik'); ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group my-1">
                                    <input type="numeric" class="form-control form-control form-control-user" name="nope" aria-describedby="emailHelp" placeholder="Masukan No. Handphone" value="<?= set_value('nope'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group my-1">
                            <input type="email" class="form-control form-control form-control-user" name="email" aria-describedby="emailHelp" placeholder="Masukan Email" value="<?= set_value('email'); ?>">
                        </div>
                        <div class="form-group my-1">
                            <select id="kecamatan" name="kecamatan" class="form-control form-control form-control-user" disabled="true">
                                <option value="">-- Pilih Kecamatan --</option>
                                <?php foreach ($kecamatan as $row) { ?>
                                    <option <?= $kode_kec == $row['id'] ? 'selected' : ''; ?> value="<?= $row['id'] ?>" <?= set_select('kecamatan', $row['id']); ?>> <?php echo $row['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group my-1">
                            <select id="kelurahan" name="kelurahan" class="form-control form-control form-control-user">
                                <option value="">-- Pilih Desa / Kelurahan --</option>
                                <?php foreach ($desa as $row) { ?>
                                    <option value="<?= $row['id'] ?>" <?= set_select('kelurahan', $row['id']); ?>> <?php echo $row['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group my-1">
                            <select id="no_rw" name="no_rw" class="form-control form-control form-control-user">
                                <option value="">-- Pilih RW --</option>
                                <?php foreach ($datarw as $row) { ?>
                                    <option value="<?= $row['no_rw'] ?>" <?= set_select('no_rw', $row['no_rw']); ?>> <?php echo $row['no_rw']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <!-- tambah input wilayah tugas -->
                        <div class="form-group my-1">
                            <input type="text" name="wilayah_tugas" id="wilayah_tugas" class="form-control form-control form-control-user" placeholder="Wilayah Tugas" value="<?= set_value('wilayah_tugas'); ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group my-1">
                                    <input type="password" class="form-control form-control form-control-user" name="password" placeholder="Password" id="password1" value="<?= set_value('password'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group my-1">
                                    <input type="password" class="form-control form-control form-control-user" name="password_confirm" placeholder="Password confirm" id="password2" value="<?= set_value('password_confirm'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group my-1">
                            <div class="custom-control custom-checkbox small">
                                <input type="checkbox" class="custom-control-input" id="checkbox">
                                <label class="custom-control-label" for="checkbox"> Tampilkan kata sandi</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="formTambahUser" type="submit" class="btn btn-primary btn-block">
                                <?= $title1; ?>
                            </button>
                        </div>
                        <hr>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 🚀 Inisialisasi variabel tabel secara global agar bisa diakses semua fungsi
    var tableUserDt;

    // ==========================================
    // 🚀 FUNGSI SAKTI: SOFT RELOAD (DOM REPLACEMENT)
    // ==========================================
    window.refreshTableSPA = function() {
        let currentPage = tableUserDt.page(); // Ingat halaman saat ini

        // Ambil data HTML terbaru dari server secara diam-diam
        $.get(location.href, function(data) {
            let newDoc = new DOMParser().parseFromString(data, 'text/html');
            let newTbody = $(newDoc).find('#tabelUser tbody').html();

            // Hancurkan tabel lama, pasang data baru, lalu bangun kembali!
            tableUserDt.clear().destroy();
            $('#tabelUser tbody').html(newTbody);
            tableUserDt = $('#tabelUser').DataTable({
                responsive: true
            });

            // Kembalikan ke halaman sebelumnya dan jalankan ulang filter Custom
            tableUserDt.page(currentPage).draw(false);
        });
    };

    $(document).ready(function() {
        // 1. Inisialisasi DataTable Awal
        tableUserDt = $('#tabelUser').DataTable({
            responsive: true
        });
        $('#tabelUser_wrapper .dataTables_length').appendTo('.dt-left');
        $('#tabelUser_wrapper .dataTables_filter').appendTo('.dt-right');

        // 2. Custom Filter (Anti-Badai Version)
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            // Pastikan filter ini hanya jalan untuk tabel user
            if (settings.nTable.id !== 'tabelUser') {
                return true;
            }

            let filterRole = $('#filterRole').val().toLowerCase();
            let filterStatus = $('#filterStatus').val().toLowerCase();
            let filterRW = $('#filterRW').val().toLowerCase();

            let rwText = data[3].toLowerCase();
            let roleText = data[7].toLowerCase();
            // Selector aman melalui DOM Node bawaan datatable
            let statusText = $(settings.aoData[dataIndex].nTr).find('.btnToggleStatus').text().trim().toLowerCase();

            if (filterStatus && filterStatus !== statusText) return false;
            if (filterRW && !rwText.includes(filterRW)) return false;
            if (filterRole && !roleText.includes(filterRole)) return false;
            return true;
        });

        $('#filterStatus, #filterRW, #filterRole').on('change', function() {
            tableUserDt.draw();
        });

        // 3. 🚀 TAMBAH USER (Mode AJAX tanpa reload)
        $('#mainform').on('submit', function(e) {
            e.preventDefault();
            $('#kecamatan').prop('disabled', false); // Aktifkan select agar terbaca serialize

            let btn = $('#formTambahUser');
            let ori = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        $('#modalAdd').modal('hide');
                        $('#mainform')[0].reset();
                        Swal.fire('Berhasil!', res.message, 'success');
                        refreshTableSPA(); // Panggil Soft Reload!
                    } else if (res.status === 'error') {
                        let errorMsg = Object.values(res.errors).join('<br>');
                        Swal.fire('Validasi Gagal!', errorMsg, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                },
                complete: function() {
                    btn.html(ori).prop('disabled', false);
                }
            });
        });

        // 4. 🚀 KELOLA ROLE: SIMPAN (Tanpa Reload)
        $('#formRole').submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.post('<?= base_url('users/saveRole') ?>', $(this).serialize(), function(res) {
                if (res.status === 'success') {
                    Swal.fire('Berhasil!', res.message, 'success');
                    $('#btnBatalRole').click(); // Reset form role

                    // Update spesifik untuk tabel role di offcanvas
                    $.get(location.href, function(data) {
                        let newTable = $(data).find('#tabelRoleOffcanvas tbody').html();
                        $('#tabelRoleOffcanvas tbody').html(newTable);
                    });
                    refreshTableSPA(); // Soft reload tabel utama agar dropdown terupdate
                } else {
                    Swal.fire('Gagal!', res.message || 'Terjadi kesalahan.', 'error');
                }
            }, 'json');
        });

        // 5. 🚀 KELOLA ROLE: HAPUS (Tanpa Reload)
        $(document).on('click', '.btnHapusRole', function() {
            let btn = $(this);
            let id = btn.data('id');
            Swal.fire({
                title: 'Hapus Role Ini?',
                text: "Pastikan tidak ada User yang menggunakan Role ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('<?= base_url('users/deleteRole') ?>', {
                        id_role: id
                    }, function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Terhapus!', res.message, 'success');
                            btn.closest('tr').remove(); // Buang baris tabel offcanvas
                            refreshTableSPA(); // Soft reload tabel utama
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    }, 'json');
                }
            });
        });

        // 6. TOGGLE STATUS AKTIF/INAKTIF
        $(document).on('click', '.btnToggleStatus', function(e) {
            e.preventDefault();
            let btn = $(this);
            let uid = btn.data('id');
            let ustatus = btn.data('status');
            let originalText = btn.text();

            btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            $.ajax({
                url: "<?= base_url('update_status') ?>/" + uid + "/" + ustatus,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        if (res.new_status == 1) {
                            btn.removeClass('btn-dark').addClass('btn-warning').text('Active').data('status', 1);
                        } else {
                            btn.removeClass('btn-warning').addClass('btn-dark').text('Inactive').data('status', 0);
                        }
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: res.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                        tableUserDt.draw(false);
                    } else {
                        btn.html(originalText);
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false);
                }
            });
        });

    });

    // ==========================================
    // 7. 🚀 HAPUS USER (DOM Removal Tanpa Reload)
    // ==========================================
    function hapus(id, fullname, el) {
        Swal.fire({
            title: `Hapus ${fullname}?`,
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "<?= base_url('hapus'); ?>",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Terhapus!', res.message, 'success');
                            // Lempar baris ke luar arena datatable
                            tableUserDt.row($(el).parents('tr')).remove().draw(false);
                        }
                    }
                });
            }
        });
    }

    // Modal Edit (Existing functionality)
    function view(id) {
        $.ajax({
            type: "post",
            url: "<?= base_url('formview'); ?>",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {
                if (response.sukses) {
                    $('.viewmodal').html(response.sukses).show();
                    $('#modalview').modal('show');
                }
            }
        });
    }
</script>

<script>
    // ==========================================
    // 🚀 FUNGSI RESET PASSWORD (VIA AJAX & WA/EMAIL)
    // ==========================================
    function requestReset(userId) {
        Swal.fire({
            title: "Kirim Reset Password?",
            text: "Link reset password akan dikirim ke WhatsApp dan Email pengguna.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, kirim!"
        }).then((result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: "Memproses...",
                    text: "Mohon tunggu sebentar, sistem sedang mengirim pesan...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch("<?= base_url('admin-reset-password') ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        body: JSON.stringify({
                            id: userId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {

                        Swal.close();

                        Swal.fire({
                            title: data.status === "success" ? "Berhasil!" : "Gagal!",
                            text: data.message,
                            icon: data.status === "success" ? "success" : "error",
                            confirmButtonText: "OK"
                        });

                        // 🚀 SINKRONISASI SPA: Refresh tabel secara diam-diam tanpa reload layar!
                        if (data.status === "success") {
                            if (typeof refreshTableSPA === "function") {
                                refreshTableSPA();
                            }
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire({
                            title: "Error",
                            text: "Terjadi kesalahan koneksi atau server.",
                            icon: "error"
                        });
                        console.error(err);
                    });
            }
        });
    }
</script>

<!-- ========================================== -->
<!-- 🚀 PENANGKAP FLASHDATA & NOTIFIKASI -->
<!-- ========================================== -->
<?php if (session()->getFlashdata('success')) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '<?= session()->getFlashdata('success') ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('danger')) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '<?= session()->getFlashdata('danger') ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
<?php endif; ?>

<?= $this->endSection(); ?>