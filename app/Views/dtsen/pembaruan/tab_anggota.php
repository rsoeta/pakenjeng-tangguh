<!-- app/Views/dtsen/pembaruan/tab_anggota.php -->
<?php
$roleId = $user['role_id'] ?? 99;
$editable = ($roleId <= 4); // Operator & Pendata bisa edit
?>

<!-- <div class="p-1"> -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">👨‍👩‍👧 Daftar Anggota Keluarga</h5>
    <?php if ($editable): ?>
        <!-- gabungkan tombol -->
        <div class="btn-group">
            <button id="btnTambahAnggota" class="btn btn-success btn-sm">
                <i class="fas fa-user-plus"></i> Tambah Anggota
            </button>
            <button id="btnReloadAnggota" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sync-alt"></i> Muat Ulang
            </button>
        </div>
    <?php endif; ?>
</div>
<table class="table table-bordered table-sm table-striped" id="tableAnggota">
    <thead class="table-light">
        <tr>
            <th style="width:5%">No</th>
            <th style="width:20%">NIK</th>
            <th>Nama</th>
            <th style="width:20%">Hubungan Keluarga</th>
            <th style="width:15%" class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5" class="text-center text-muted">Memuat data...</td>
        </tr>
    </tbody>
</table>
<!-- </div> -->

<!-- Modal Anggota -->
<?= $this->include('dtsen/pembaruan/modal_anggota') ?>
<!-- ============================== -->
<!-- ✅ DataTables & Dependencies -->
<!-- ============================== -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url('assets/js/datatables.config.js'); ?>"></script>

<!-- Dependencies: Select2 (cascading dropdown), SweetAlert2 -->
<!-- (Pastikan Select2 dan SweetAlert sudah muncul di global layout AdminLTE; jika belum, URL di bawah bisa dipakai) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.baseUrl = "<?= rtrim(base_url(), '/') ?>";

    $(document).ready(function() {

        /* ============================================================
         * 🧩 Fungsi: Tampilkan form detail usaha bila memilih "Ya"
         * ============================================================ */
        const selectUsaha = document.getElementById('memiliki_usaha');
        const formDetail = document.getElementById('form_usaha_detail');

        // ============================================================
        // 🧩 Tampilkan / sembunyikan form detail usaha
        // ============================================================
        function toggleUsahaDetail() {
            const val = $('#memiliki_usaha').val(); // ambil langsung dari form, bukan dari `d`
            if (val === 'Ya') {
                $('#form_usaha_detail').slideDown();
                $('.required-if-ya').attr('required', true);
            } else {
                $('#form_usaha_detail').slideUp();
                $('.required-if-ya').removeAttr('required').removeClass('is-invalid');
                // kosongkan field tambahan kalau "Tidak"
                $('#jumlah_usaha, #pekerja_dibayar, #pekerja_tidak_dibayar, #omzet_bulanan').val('');
            }
            // perbarui badge validasi tab usaha
            $('#badgeUsaha').text(val ? '🟢' : '⚠️');
        }


        // Event listener
        $('#memiliki_usaha').on('change', toggleUsahaDetail);


        if (selectUsaha) {
            selectUsaha.addEventListener('change', toggleUsahaDetail);
            toggleUsahaDetail();
        }

        /* ============================================================
         * 🧠 Validasi Tab dan Input Wajib
         * ============================================================ */
        function validateTab(tabId) {
            let valid = true;
            document.querySelectorAll(`${tabId} .required`).forEach(el => {
                if (!el.value.trim()) {
                    el.classList.add("is-invalid");
                    valid = false;
                } else el.classList.remove("is-invalid");
            });

            const badgeMap = {
                "#tab-identitas": "#badgeIdentitas",
                "#tab-pendidikan": "#badgePendidikan",
                "#tab-kerja": "#badgeKerja",
                "#tab-usaha": "#badgeUsaha",
                "#tab-kesehatan": "#badgeKesehatan"
            };

            const badge = badgeMap[tabId];
            if (badge) document.querySelector(badge).textContent = valid ? "🟢" : "⚠️";

            return valid;
        }

        document.querySelectorAll(".required").forEach(el => {
            el.addEventListener("change", () => {
                const parentTab = el.closest(".tab-pane");
                if (parentTab) validateTab(`#${parentTab.id}`);
            });
        });

        function validateTenagaKerja() {
            const bekerja = $('#bekerja_seminggu').val();
            const usaha = $('#lapangan_usaha').val();
            const status = $('#status_pekerjaan').val();
            const pendapatan = $('#pendapatan').val();
            const valid = (bekerja && usaha && status && pendapatan);
            $('#badgeKerja').text(valid ? '🟢' : '⚠️');
            return valid;
        }

        function validateUsaha() {
            const memilikiUsaha = $('#memiliki_usaha').val();
            let valid = true;

            if (memilikiUsaha === 'Ya') {
                const jumlahUsaha = $('#jumlah_usaha').val();
                const pekerjaDibayar = $('#pekerja_dibayar').val();
                const pekerjaTidakDibayar = $('#pekerja_tidak_dibayar').val();
                const omzetBulanan = $('#omzet_bulanan').val();
                valid = (jumlahUsaha && pekerjaDibayar && pekerjaTidakDibayar && omzetBulanan);
            }

            $('#badgeUsaha').text(valid ? '🟢' : '⚠️');
            return valid;
        }

        function validateKesehatan() {
            const penyakitKronis = $('#penyakit_kronis').val();
            const valid = (statusHamil && penyakitKronis);
            $('#badgeKesehatan').text(valid ? '🟢' : '⚠️');
            return valid;
        }

        /* ============================================================
         * 🌍 FUNGSI: Chained Loading Wilayah (tanpa setTimeout)
         * ============================================================ */
        const apiBase = "<?= base_url('api/villages') ?>";

        function loadProvinces(selected = '', next) {
            $.getJSON(`${apiBase}/provinces`, res => {
                const el = $('#ind_provinsi').html('<option value="">Pilih Provinsi</option>');
                res.forEach(r => el.append(`<option value="${r.id}" ${r.id == selected ? 'selected' : ''}>${r.name}</option>`));
                if (next) next();
            });
        }

        function loadRegencies(provId, selected = '', next) {
            const el = $('#ind_kabupaten').html('<option value="">Pilih Kabupaten</option>');
            if (!provId) return;
            $.getJSON(`${apiBase}/regencies/${provId}`, res => {
                res.forEach(r => el.append(`<option value="${r.id}" ${r.id == selected ? 'selected' : ''}>${r.name}</option>`));
                if (next) next();
            });
        }

        function loadDistricts(kabId, selected = '', next) {
            const el = $('#ind_kecamatan').html('<option value="">Pilih Kecamatan</option>');
            if (!kabId) return;
            $.getJSON(`${apiBase}/districts/${kabId}`, res => {
                res.forEach(r => el.append(`<option value="${r.id}" ${r.id == selected ? 'selected' : ''}>${r.name}</option>`));
                if (next) next();
            });
        }

        function loadVillages(kecId, selected = '') {
            const el = $('#ind_desa').html('<option value="">Pilih Desa</option>');
            if (!kecId) return;
            $.getJSON(`${apiBase}/villages/${kecId}`, res => {
                res.forEach(r => el.append(`<option value="${r.id}" ${r.id == selected ? 'selected' : ''}>${r.name}</option>`));
            });
        }

        // 🌐 Event cascading antar wilayah
        $('#ind_provinsi').on('change', function() {
            loadRegencies(this.value);
            $('#ind_kecamatan, #ind_desa').html('<option value="">Pilih Kecamatan/Desa</option>');
        });
        $('#ind_kabupaten').on('change', function() {
            loadDistricts(this.value);
            $('#ind_desa').html('<option value="">Pilih Desa</option>');
        });
        $('#ind_kecamatan').on('change', function() {
            loadVillages(this.value);
        });

        /* ============================================================
         * 🔧 Helper Dropdown Dinamis
         * ============================================================ */
        function updateSelectOptions(selector, list = [], selectedId = '') {
            const el = $(selector);
            el.empty().append(`<option value="">-- Pilih --</option>`);
            if (!Array.isArray(list)) return;
            list.forEach(opt => {
                const id = opt.id ?? opt.idStatus ?? opt.pk_id ?? '';
                const name = opt.nama ?? opt.jenis_shdk ?? opt.StatusKawin ?? opt.pk_nama ?? '';
                const selected = (id == selectedId) ? 'selected' : '';
                el.append(`<option value="${id}" ${selected}>${name}</option>`);
            });
        }

        $('#memiliki_usaha').on('change', toggleUsahaDetail);
        console.log("Usaha:", $('#memiliki_usaha').val());

        /* ============================================================
         * ✏️ EVENT: Tombol Edit Anggota
         * ============================================================ */
        $(document).on('click', '.btnEditAnggota', function() {

            const id = $(this).data('id');
            if (!id) return Swal.fire("Info", "ID anggota tidak ditemukan.", "info");

            const idKkGlobal = $('#id_kk').val();
            $('#formAnggota #id_kk').val(idKkGlobal);

            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.getJSON(`${window.baseUrl}/pembaruan-keluarga/get-anggota-detail/${id}`, function(res) {
                Swal.close();
                if (res.status !== 'success') return Swal.fire("Gagal", res.message, "error");

                const d = res.data.anggota_prefill;
                const drop = res.data.dropdowns;

                // Prefill semua input dasar
                $('#nik').val(d.nik ?? '');
                $('#nama').val(d.nama ?? '');
                $('#tempat_lahir').val(d.tempat_lahir ?? '');
                $('#tanggal_lahir').val(d.tanggal_lahir ?? '');
                // $('#jenis_kelamin').val(d.jenis_kelamin ?? '');
                // Prefill Jenis Kelamin (radio button)
                $('input[name="jenis_kelamin"]').prop('checked', false); // reset dulu
                if (d.jenis_kelamin === 'L' || d.jenis_kelamin === 'P') {
                    $(`input[name="jenis_kelamin"][value="${d.jenis_kelamin}"]`).prop('checked', true);
                }

                updateSelectOptions('#status_kawin', drop.status_kawin, d.status_kawin ?? d.status_kawin_label);
                updateSelectOptions('#hubungan', drop.hubungan, d.hubungan ?? d.hubungan_label);
                updateSelectOptions('#pekerjaan', drop.pekerjaan, d.pekerjaan ?? d.pekerjaan_label);
                updateSelectOptions('#pendidikan_terakhir', drop.pendidikan, d.pendidikan_terakhir ?? d.pendidikan_label);
                $('#ibu_kandung').val(d.ibu_kandung ?? '');
                $('#individu_no_kk').val(d.individu_no_kk);
                $('#status_keberadaan').val(d.status_keberadaan ?? '');

                // Prefill cascading wilayah
                const prov = d.provinsi ?? '',
                    kab = d.kabupaten ?? '',
                    kec = d.kecamatan ?? '',
                    desa = d.desa ?? '';
                loadProvinces(prov, () => loadRegencies(prov, kab, () => loadDistricts(kab, kec, () => loadVillages(kec, desa))));

                // Prefill Tab Pendidikan
                $('#partisipasi_sekolah').val(d.partisipasi_sekolah ?? '');
                $('#jenjang_pendidikan').val(d.jenjang_pendidikan ?? '');
                $('#kelas_tertinggi').val(d.kelas_tertinggi ?? '');
                $('#ijazah_tertinggi').val(d.ijazah_tertinggi ?? '');

                // Prefill Tab Kerja
                $('#bekerja_seminggu').val(d.bekerja_seminggu ?? '');
                $('#lapangan_usaha').val(d.lapangan_usaha ?? '');
                $('#status_pekerjaan').val(d.status_pekerjaan ?? '');
                $('#pendapatan').val(d.pendapatan ?? '');

                // Prefill Tab Usaha
                $('#memiliki_usaha').val(d.memiliki_usaha || '');
                $('#jumlah_usaha').val(d.jumlah_usaha || '');
                $('#pekerja_dibayar').val(d.pekerja_dibayar || '');
                $('#pekerja_tidak_dibayar').val(d.pekerja_tidak_dibayar || '');
                $('#omzet_bulanan').val(d.omzet_bulanan || '');
                toggleUsahaDetail(); // pastikan tampil sesuai nilai prefill

                // Prefill Tab Kesehatan
                $('#status_hamil').val(d.status_hamil ?? '');
                $('#penyakit_kronis').val(d.penyakit_kronis ?? '');

                // Multi-check Disabilitas dan Keterampilan
                // ♿ Disabilitas
                if (Array.isArray(d.disabilitas)) {
                    $('.disab-check').prop('checked', false);
                    d.disabilitas.forEach(val => {
                        $(`.disab-check[value="${val}"]`).prop('checked', true);
                    });
                } else if (typeof d.disabilitas === 'string' && d.disabilitas.trim() !== '') {
                    // Jika string tapi berisi JSON misalnya '["Fisik"]'
                    try {
                        const parsed = JSON.parse(d.disabilitas);
                        if (Array.isArray(parsed)) {
                            $('.disab-check').prop('checked', false);
                            parsed.forEach(val => {
                                $(`.disab-check[value="${val}"]`).prop('checked', true);
                            });
                        }
                    } catch (e) {
                        console.warn("⚠️ d.disabilitas bukan array valid:", d.disabilitas);
                    }
                }

                if (Array.isArray(d.keterampilan)) {
                    $('.skill-check').prop('checked', false);
                    d.keterampilan.forEach(val => $(`.skill-check[value="${val}"]`).prop('checked', true));
                } else if (typeof d.keterampilan === 'string' && d.keterampilan.trim() !== '') {
                    try {
                        const parsed = JSON.parse(d.keterampilan);
                        if (Array.isArray(parsed)) {
                            $('.skill-check').prop('checked', false);
                            parsed.forEach(val => $(`.skill-check[value="${val}"]`).prop('checked', true));
                        }
                    } catch (e) {
                        console.warn("⚠️ d.keterampilan bukan array valid:", d.keterampilan);
                    }
                }

                $('#modalAnggotaLabel').text('Edit Anggota');
                const idKk = $('#id_kk').val() || $('[name="id_kk"]').val();
                $('#formAnggota #id_kk').val(idKk);
                $('#modalAnggota').modal('show');
                setTimeout(applyRules, 30);

            }).fail(() => Swal.fire("Error", "Gagal memuat data anggota.", "error"));
        });

        $(document).on('shown.bs.modal', '#modalAnggota', function() {
            applyRules();
        });

        /* ======================================================
        ➕ EVENT: Tambah Anggota Baru (Gunakan Modal yang Sama)
        ====================================================== */
        $(document).on('click', '#btnTambahAnggota', function() {
            console.log('🆕 Tambah Anggota Baru diklik');

            Swal.fire({
                title: 'Memuat form...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // 🔗 Ambil dropdown master dari backend
            $.getJSON(`${window.baseUrl}/pembaruan-keluarga/get-anggota-detail`, function(res) {
                Swal.close();

                // 🧹 Reset seluruh form modal
                $('#formAnggota')[0].reset();
                $('#id_anggota').val('');

                // Ambil id_kk dari halaman utama
                const idKk = $('#id_kk').val() || $('[name="id_kk"]').val();

                // Pastikan diset ke form di modal
                $('#formAnggota #id_kk').val(idKk);

                $('#modalAnggotaLabel').text('Tambah Anggota Baru');

                // Kosongkan semua dropdown wilayah
                $('#ind_provinsi, #ind_kabupaten, #ind_kecamatan, #ind_desa').html('<option value="">Pilih...</option>').val('').trigger('change');

                // Kosongkan checklist & select lainnya
                $('.skill-check, .disab-check').prop('checked', false);

                // Prefill dropdown master
                const drop = res.data?.dropdowns ?? {};
                updateSelectOptions('#status_kawin', drop.status_kawin);
                updateSelectOptions('#hubungan', drop.hubungan);
                updateSelectOptions('#pekerjaan', drop.pekerjaan);
                updateSelectOptions('#pendidikan_terakhir', drop.pendidikan);

                // Pastikan tab pertama aktif
                $('#tabAnggotaTabs button:first').tab('show');

                // 🌏 Muat daftar provinsi awal
                loadProvinces('', function() {
                    console.log('✅ Daftar provinsi dimuat.');
                });

                // Buka modal
                $('#modalAnggota').modal('show');
            }).fail(() => {
                Swal.close();
                Swal.fire('Error', 'Gagal memuat form tambah anggota.', 'error');
            });
        });

        /* ============================================================
         * ♀️ Toggle field kehamilan berdasarkan gender (radio version)
         * ============================================================ */
        $('input[name="jenis_kelamin"]').on('change', function() {
            const gender = $('input[name="jenis_kelamin"]:checked').val();

            if (gender === 'L') {
                $('#status_hamil').val('Tidak'); // otomatis Non-Hamil
                $('#status_hamil').prop('disabled', true);
            } else {
                $('#status_hamil').prop('disabled', false);
            }
        });

        // Trigger saat modal dibuka (prefill)
        setTimeout(() => {
            const gender = $('input[name="jenis_kelamin"]:checked').val();
            if (gender === 'L') {
                $('#status_hamil').val('Tidak').prop('disabled', true);
            } else {
                $('#status_hamil').prop('disabled', false);
            }
        }, 50);

        /* ============================================================
         * 🎓 Disable jenjang bila belum pernah sekolah
         * ============================================================ */
        $('#partisipasi_sekolah').on('change', function() {
            const disable = ($(this).val() === 'Belum Pernah Sekolah');
            ['#jenjang_pendidikan', '#kelas_tertinggi', '#ijazah_tertinggi'].forEach(id => {
                $(id).prop('disabled', disable);
                if (disable) $(id).val('');
            });
        });

        /* ============================================================
         * 🧩 Inisialisasi Select2 Wilayah di Modal Anggota
         * ============================================================ */
        function initSelect2WilayahModal() {
            const parent = $('#modalAnggota');
            ['#ind_provinsi', '#ind_kabupaten', '#ind_kecamatan', '#ind_desa'].forEach(sel => {
                const $el = $(sel);
                if ($el.data('select2')) $el.select2('destroy');
                $el.select2({
                    dropdownParent: parent,
                    width: '100%',
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih...',
                    allowClear: true
                });
            });
        }

        $('#modalAnggota').on('shown.bs.modal', initSelect2WilayahModal);

        /* ======================================================
        💾 EVENT SUBMIT FORM ANGGOTA (Tambah / Edit)
        ====================================================== */
        $(document).on('submit', '#formAnggota', function(e) {
            e.preventDefault();

            console.log('🚀 Submitting formAnggota...');

            const form = $(this);
            let valid = true;

            /* ======================================================
               1) VALIDASI KOLOM REQUIRED
            ====================================================== */
            form.find('.required').each(function() {
                if (!$(this).val().trim()) {
                    $(this).addClass('is-invalid');
                    valid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            /* ======================================================
               2) VALIDASI 16 DIGIT UNTUK: NIK, No KK, Individu No KK
            ====================================================== */
            const digitFields = [
                '#nik',
                '#keluarga_no_kk', // jika tidak ada, otomatis dilewati
                '#individu_no_kk'
            ];

            digitFields.forEach(selector => {
                const el = form.find(selector);
                if (el.length > 0) {
                    let value = el.val().replace(/\D/g, ''); // hanya angka
                    el.val(value); // bersihkan otomatis

                    if (value.length !== 16) {
                        el.addClass('is-invalid');
                        valid = false;
                    } else {
                        el.removeClass('is-invalid');
                    }
                }
            });

            if (!valid) {
                Swal.fire(
                    "Isian Tidak Valid",
                    "Pastikan semua kolom wajib sudah diisi dan NIK/No KK terdiri dari 16 digit angka.",
                    "warning"
                );
                return;
            }

            /* ======================================================
               3) KONFIRMASI SIMPAN
            ====================================================== */
            Swal.fire({
                title: 'Simpan Data?',
                text: 'Data individu akan disimpan ke draf pembaruan keluarga.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: `${window.baseUrl}/pembaruan-keluarga/save-anggota`,
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        Swal.close();
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1200,
                                showConfirmButton: false,
                                willClose: () => {
                                    // 🔄 Reload tabel anggota
                                    loadTableAnggota();
                                    $(document).trigger('anggota:saved');
                                }
                            });
                        } else {
                            Swal.fire('Gagal', res.message || 'Tidak dapat menyimpan data.', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        console.error('❌ Error saat simpan:', xhr.responseText);
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data individu.', 'error');
                    }
                });
            });
        });

        $('#modalAnggota').on('shown.bs.modal', function() {
            console.log('🧾 Modal Anggota terbuka, event submit aktif');
        });

        /* ============================================================
         * 📋 LOAD TABLE ANGGOTA
         * ============================================================ */
        function loadTableAnggota(callback) {
            const idKk = $('#id_kk').val();
            if (!idKk) {
                console.warn('⚠️ ID KK belum ada, tidak bisa memuat anggota.');
                if (callback) callback();
                return;
            }

            $.getJSON(`${window.baseUrl}/pembaruan-keluarga/get-anggota-list/${idKk}`, function(res) {
                if (res.status !== 'success') {
                    $('#tableAnggota tbody').html(`<tr><td colspan="5" class="text-center text-danger">${res.message}</td></tr>`);
                    if (callback) callback();
                    return;
                }

                let html = '';
                res.data.forEach((row, i) => {
                    const hubungan = row.jenis_shdk ?
                        row.jenis_shdk.toUpperCase() :
                        (row.hubungan_keluarga ?? row.shdk ?? '-');

                    html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${row.nik ?? '-'}</td>
                    <td>${row.nama ?? '-'}</td>
                    <td>${hubungan}</td>
                    <td class="text-end">
                    <!-- gabungkan tombol -->
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary btnEditAnggota" data-id="${row.id_art ?? row.id}" title="Edit"><i class="fas fa-edit"></i>Edit</button>
                        <button class="btn btn-sm btn-danger btnHapusAnggota" data-id="${row.id_art ?? row.id}" title="Hapus"><i class="fas fa-trash-alt"></i>Hapus</button>
                    </div>
                    </td>
                </tr>`;
                });

                $('#tableAnggota tbody').html(
                    html || `<tr><td colspan="5" class="text-center text-muted">Belum ada anggota keluarga.</td></tr>`
                );

                if (callback) callback();
            }).fail(() => {
                $('#tableAnggota tbody').html(`<tr><td colspan="5" class="text-center text-danger">Gagal memuat data anggota.</td></tr>`);
                if (callback) callback();
            });
        }

        loadTableAnggota();

        /* ======================================================
        🔁 TOMBOL RELOAD DAFTAR ANGGOTA
        ====================================================== */
        $(document).on('click', '#btnReloadAnggota', function() {
            Swal.fire({
                title: 'Memuat ulang data...',
                text: 'Harap tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            loadTableAnggota(() => {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Daftar anggota berhasil dimuat ulang.',
                    timer: 800,
                    showConfirmButton: false
                });
            });
        });

        $(document).on('click', '.btnHapusAnggota', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Hapus Anggota?',
                html: `
                <p class="text-start">Silakan isi alasan penghapusan:</p>
                <textarea id="deleteReason" class="form-control" rows="3" placeholder="Wajib diisi..."></textarea>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                preConfirm: () => {
                    const reason = $('#deleteReason').val().trim();
                    if (!reason) {
                        Swal.showValidationMessage('Alasan wajib diisi!');
                    }
                    return reason;
                }
            }).then(result => {
                if (!result.isConfirmed) return;

                $.post(`${window.baseUrl}/pembaruan-keluarga/delete-anggota`, {
                    id_art: id,
                    reason: result.value
                }, function(res) {
                    if (res.status) {
                        Swal.fire('Berhasil!', res.message, 'success');
                        loadTableAnggota();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json');
            });
        });

    });
</script>

<style>
    #tableAnggota td,
    #tableAnggota th {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.2em 0.6em;
    }

    table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
        background-color: #198754 !important;
    }

    table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.dtr-control:before {
        background-color: #dc3545 !important;
    }
</style>