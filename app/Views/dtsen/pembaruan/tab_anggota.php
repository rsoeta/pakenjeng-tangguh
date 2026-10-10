<?php
// 🚀 PERBAIKAN: Tangkap langsung dari session agar lebih akurat
$roleId = session()->get('role_id') ?? ($user['role_id'] ?? 99);
$editable = ($roleId <= 4); // Operator & Pendata bisa edit
?>

<style>
    /* Pastikan child row DataTables rata kiri */
    table.dataTable>tbody>tr.child td {
        text-align: left !important;
        vertical-align: top;
    }

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

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }

        70% {
            box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">👨‍👩‍👧 Daftar Anggota Keluarga</h5>
    <?php if ($editable): ?>
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

<table class="table table-bordered table-sm table-striped w-100" id="tableAnggota">
    <thead class="table-light text-start">
        <tr>
            <th></th>
            <th>No</th>
            <th>Nama</th>
            <th>NIK</th>
            <th>Tanggal Lahir</th>
            <th>Hubungan Keluarga</th>
            <th>Pekerjaan</th>
            <th>Aksi</th>
        </tr>
    </thead>
</table>

<!-- 🚀 KOTAK TOMBOL SELESAI / CEK STATUS -->
<?php if ($editable): ?>
    <div class="mt-4 p-4 bg-light border border-primary border-opacity-25 rounded-3 shadow-sm text-end">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="text-start mb-3 mb-md-0">
                <h6 class="fw-bold text-primary mb-1"><i class="fas fa-info-circle me-1"></i> Pengisian Selesai?</h6>
                <span class="text-muted small">Jika seluruh tab (Keluarga, Rumah, Foto, Aset, Anggota) telah diisi dengan lengkap, klik tombol di samping untuk mengecek status akhir usulan.</span>
            </div>
            <!-- 🚀 BUG FIX & UPGRADE: Hapus onclick inline, ganti dengan ID pemicu -->
            <?php if (in_array(strtolower($usulan['status'] ?? ''), ['draft', 'submitted'])): ?>
                <button type="button" id="btnTriggerSimpanGlobal" class="btn btn-primary px-4 rounded-pill shadow-sm">
                    <i class="fas fa-clipboard-check me-2"></i> Cek Status Kelengkapan Usulan
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm btn-terkunci">
                    <i class="fas fa-lock me-2"></i> Terkunci
                </button>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.baseUrl = "<?= rtrim(base_url(), '/') ?>";

    // 🚀 PERBAIKAN: Lempar status editable ke JS agar DataTables tahu
    const isEditable = <?= $editable ? 'true' : 'false' ?>;

    $(document).ready(function() {

        // ==============================================================
        // 🚀 SMART UI: LOGIKA TOGGLE STATUS KEBERADAAN
        // ==============================================================
        $(document).on('change', '#status_keberadaan', function() {
            let val = $(this).val();
            let form = $('#formAnggota');

            // Definisikan kategori status
            let isMeninggalAtauHilang = ['2', '5', '6'].includes(val);
            let isPindahDN = (val === '3');
            let isPindahLN = (val === '4');

            // 1. LOGIKA KUNCI ELEMEN (READONLY/DISABLED)
            if (isMeninggalAtauHilang) {
                // Kunci semua input, select, textarea di dalam form
                form.find('input, select, textarea').prop('disabled', true);

                // KECUALI: Dropdown status ini sendiri, tombol close, dan input hidden (ID, NIK)
                $('#status_keberadaan').prop('disabled', false);
                form.find('input[type="hidden"]').prop('disabled', false);

                // Munculkan notifikasi
                $('#alert-keberadaan').html('<div class="alert alert-danger py-2 mb-0 shadow-sm border-danger"><i class="fas fa-lock me-2"></i> <strong>Terkunci:</strong> Seluruh isian data diabaikan karena status keberadaan anggota keluarga.</div>').slideDown();

            } else {
                // Buka kembali semua kuncian jika statusnya selain 2, 5, 6
                form.find('input, select, textarea').prop('disabled', false);

                if (val !== '1' && val !== '') {
                    $('#alert-keberadaan').html('<div class="alert alert-warning py-2 mb-0 shadow-sm border-warning"><i class="fas fa-info-circle me-2"></i> <strong>Perhatian:</strong> Karena pindah, kelengkapan isian Pendidikan, Pekerjaan, dll akan disesuaikan.</div>').slideDown();
                } else {
                    $('#alert-keberadaan').slideUp();
                }
            }

            // 2. LOGIKA TOGGLE ALAMAT
            if (isPindahDN) {
                $('#blok_alamat_tetap, #blok_alamat_pindah_ln').hide();
                $('#blok_alamat_pindah_dn').fadeIn();
                loadProvinsiTujuan(); // 🚀 PANGGIL API PROVINSI DI SINI!
            } else if (isPindahLN) {
                $('#blok_alamat_tetap, #blok_alamat_pindah_dn').hide();
                $('#blok_alamat_pindah_ln').fadeIn();
            } else {
                $('#blok_alamat_tetap').fadeIn();
                $('#blok_alamat_pindah_dn, #blok_alamat_pindah_ln').hide();
                $('.alamat-wajib-dn, .alamat-wajib-ln').val('');
            }
        });

        // ==============================================================
        // 🌍 API WILAYAH INDONESIA (CASCADING DROPDOWN)
        // Menggunakan public API EMSIFA (Cepat & Stabil)
        // ==============================================================
        const apiWilayah = 'https://www.emsifa.com/api-wilayah-indonesia/api';
        let provLoaded = false;

        function loadProvinsiTujuan() {
            if (provLoaded) return;

            $('#provinsi_tujuan_select').html('<option value="">Loading...</option>');

            $.getJSON(`${apiWilayah}/provinces.json`, function(data) {
                let options = '<option value="">-- Pilih Provinsi --</option>';
                data.forEach(p => {
                    options += `<option value="${p.id}" data-name="${p.name}">${p.name}</option>`;
                });
                $('#provinsi_tujuan_select').html(options);
                provLoaded = true;
            }).fail(function() {
                $('#provinsi_tujuan_select').html('<option value="">Gagal memuat data</option>');
            });
        }

        // 1. Aksi saat Provinsi dipilih -> Cari Kabupaten
        $('#provinsi_tujuan_select').on('change', function() {
            let id = $(this).val();
            // 🚀 Tangkap NAMA Provinsi, bukan ID-nya
            let name = $(this).find(':selected').attr('data-name') || '';
            $('#provinsi_tujuan_nama').val(name);

            $('#kabupaten_tujuan_select').html('<option value="">Loading...</option>').prop('disabled', true);
            $('#kecamatan_tujuan_select').html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
            $('#desa_tujuan_select').html('<option value="">-- Pilih Desa/Kelurahan --</option>').prop('disabled', true);
            $('#kabupaten_tujuan_nama, #kecamatan_tujuan_nama, #desa_tujuan_nama').val('');

            if (id) {
                $.getJSON(`${apiWilayah}/regencies/${id}.json`, function(data) {
                    let options = '<option value="">-- Pilih Kab/Kota --</option>';
                    data.forEach(k => {
                        options += `<option value="${k.id}" data-name="${k.name}">${k.name}</option>`;
                    });
                    $('#kabupaten_tujuan_select').html(options).prop('disabled', false);
                });
            }
        });

        // 2. Aksi saat Kabupaten dipilih -> Cari Kecamatan
        $('#kabupaten_tujuan_select').on('change', function() {
            let id = $(this).val();
            let name = $(this).find(':selected').attr('data-name') || '';
            $('#kabupaten_tujuan_nama').val(name);

            $('#kecamatan_tujuan_select').html('<option value="">Loading...</option>').prop('disabled', true);
            $('#desa_tujuan_select').html('<option value="">-- Pilih Desa/Kelurahan --</option>').prop('disabled', true);
            $('#kecamatan_tujuan_nama, #desa_tujuan_nama').val('');

            if (id) {
                $.getJSON(`${apiWilayah}/districts/${id}.json`, function(data) {
                    let options = '<option value="">-- Pilih Kecamatan --</option>';
                    data.forEach(k => {
                        options += `<option value="${k.id}" data-name="${k.name}">${k.name}</option>`;
                    });
                    $('#kecamatan_tujuan_select').html(options).prop('disabled', false);
                });
            }
        });

        // 3. Aksi saat Kecamatan dipilih -> Cari Desa
        $('#kecamatan_tujuan_select').on('change', function() {
            let id = $(this).val();
            let name = $(this).find(':selected').attr('data-name') || '';
            $('#kecamatan_tujuan_nama').val(name);

            $('#desa_tujuan_select').html('<option value="">Loading...</option>').prop('disabled', true);
            $('#desa_tujuan_nama').val('');

            if (id) {
                $.getJSON(`${apiWilayah}/villages/${id}.json`, function(data) {
                    let options = '<option value="">-- Pilih Desa/Kelurahan --</option>';
                    data.forEach(d => {
                        options += `<option value="${d.id}" data-name="${d.name}">${d.name}</option>`;
                    });
                    $('#desa_tujuan_select').html(options).prop('disabled', false);
                });
            }
        });

        // 4. Aksi saat Desa dipilih
        $('#desa_tujuan_select').on('change', function() {
            let name = $(this).find(':selected').attr('data-name') || '';
            $('#desa_tujuan_nama').val(name);
        });

        /* ============================================================
         * 🧩 Fungsi: Tampilkan form detail usaha bila memilih "Ya"
         * ============================================================ */
        const selectUsaha = document.getElementById('memiliki_usaha');
        const formDetail = document.getElementById('form_usaha_detail');

        function toggleUsahaDetail() {
            const val = $('#memiliki_usaha').val(); // ambil langsung dari form, bukan dari `d`
            if (val === 'Ya') {
                $('#form_usaha_detail').slideDown();
                $('.required-if-ya').attr('required', true);
            } else {
                $('#form_usaha_detail').slideUp();
                $('.required-if-ya').removeAttr('required').removeClass('is-invalid');
                $('#jumlah_usaha, #pekerja_dibayar, #pekerja_tidak_dibayar, #omzet_bulanan').val('');
            }
            $('#badgeUsaha').text(val ? '🟢' : '⚠️');
        }

        $('#memiliki_usaha').on('change', toggleUsahaDetail);

        if (selectUsaha) {
            selectUsaha.addEventListener('change', toggleUsahaDetail);
            toggleUsahaDetail();
        }

        /* ============================================================
         * 🧠 SMART TAB INDICATOR & BUTTON LOCKER
         * ============================================================ */
        window.validateAllTabs = function() {
            const tabs = ["#tab-identitas", "#tab-pendidikan", "#tab-kerja", "#tab-kesehatan"];

            // 🚀 Bantuan Ekstra: Kunci paksa status pekerjaan jika "Tidak Bekerja"
            if ($('#lapangan_usaha').val() === 'Tidak Bekerja') {
                $('#status_pekerjaan').val('').prop('disabled', true).removeClass('is-invalid');
            }

            $('#jenjang_pendidikan, #kelas_tertinggi, #ijazah_tertinggi, #lapangan_usaha, #status_pekerjaan').addClass('required');

            let isAllTabsValid = true; // 🚀 Master Kunci Tombol Simpan

            tabs.forEach(tabId => {
                let valid = true;
                const $tab = $(tabId);

                // 1. Cek elemen .required normal
                $tab.find('.required:not(:disabled)').each(function() {
                    let val = $(this).val();
                    if (!val || String(val).trim() === '') {
                        $(this).addClass('is-invalid');
                        valid = false;
                    } else {
                        // 🚀 TAMBAHKAN 'lapangan_usaha' KE DALAM DAFTAR PENGECUALIAN INI!
                        // 🚀 HAPUS partisipasi_sekolah dari daftar ini:
                        if (!['jenjang_pendidikan', 'kelas_tertinggi', 'ijazah_tertinggi', 'lapangan_usaha'].includes($(this).attr('id'))) {
                            $(this).removeClass('is-invalid');
                        }
                    }
                });

                // 2. 🚀 CEK SPESIFIK RADIO BUTTON REKENING (Khusus Tab Kerja)
                if (tabId === "#tab-kerja") {
                    let isRekDisabled = $('input[name="rekening_aktif"]').prop('disabled');
                    let $rekContainer = $('input[name="rekening_aktif"]').closest('.border');

                    if (!isRekDisabled) {
                        // Jika aktif tapi belum ada yang dipilih -> MERAH
                        if (!$('input[name="rekening_aktif"]:checked').val()) {
                            $('#rek_usaha').addClass('is-invalid'); // Flag virtual
                            $rekContainer.removeClass('border-primary').addClass('border-danger');
                            valid = false;
                        } else {
                            // Sudah dipilih -> HIJAU
                            $('#rek_usaha').removeClass('is-invalid');
                            $rekContainer.removeClass('border-danger').addClass('border-primary');
                        }
                    } else {
                        // Jika anak Balita (disabled) -> AMAN
                        $('#rek_usaha').removeClass('is-invalid');
                        $rekContainer.removeClass('border-danger').addClass('border-primary');
                    }
                }

                // 3. Cek Error Pintar (Hanya hitung elemen yang aktif / tidak dikunci)
                if ($tab.find('.is-invalid:not(:disabled)').length > 0) {
                    valid = false;
                }

                // 4. Update Lencana 🟢 / ⚠️
                const badgeMap = {
                    "#tab-identitas": "#badgeIdentitas",
                    "#tab-pendidikan": "#badgePendidikan",
                    "#tab-kerja": "#badgeKerja",
                    "#tab-kesehatan": "#badgeKesehatan"
                };

                if (badgeMap[tabId]) {
                    $(badgeMap[tabId]).text(valid ? "🟢" : "⚠️");
                }

                // Jika ada 1 saja tab yang ⚠️, maka tombol simpan tetap TERKUNCI
                if (!valid) {
                    isAllTabsValid = false;
                }
            });

            // 🚀 EKSEKUSI GEMBOK TOMBOL SIMPAN
            $('#btnSimpanAnggota').prop('disabled', !isAllTabsValid);
        };

        // Delegasi Event Tetap Sama
        $('#formAnggota').on('change input', 'input, select, textarea', function() {
            setTimeout(validateAllTabs, 100);
        });

        // 🚀 Beri jeda sedikit lebih lama (400ms) saat modal terbuka 
        // agar data prefill AJAX selesai merender sebelum Indikator mengeceknya
        $('#modalAnggota').on('shown.bs.modal', function() {
            setTimeout(validateAllTabs, 400);
        });

        document.querySelectorAll(".required").forEach(el => {
            el.addEventListener("change", () => {
                const parentTab = el.closest(".tab-pane");
                if (parentTab) validateTab(`#${parentTab.id}`);
            });
        });

        // ==============================================================
        // 🚀 DYNAMIC LOCK: TAB TENAGA KERJA (Berdasarkan Usia Real-Time)
        // ==============================================================
        function toggleKunciTenagaKerja() {
            let tglLahir = $('#tanggal_lahir').val();
            let usia = 0;
            let isBalitaOrEmpty = true; // Default: KUNCI!

            // Hitung umur secara presisi
            if (tglLahir && tglLahir.length === 10) {
                let dob = new Date(tglLahir);
                let today = new Date();
                usia = today.getFullYear() - dob.getFullYear();
                if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) {
                    usia--;
                }

                // Jika usianya di atas 5 tahun, BUKA KUNCI
                if (usia > 5) {
                    isBalitaOrEmpty = false;
                }
            }

            // Eksekusi Kunci / Buka Kunci HTML
            $('#status_pekerjaan').prop('disabled', isBalitaOrEmpty);
            $('input[name="rekening_aktif"]').prop('disabled', isBalitaOrEmpty);
            $('#lapangan_usaha').prop('disabled', isBalitaOrEmpty);

            // Jika terkunci, SIKAT BERSIH isiannya agar data kotor tidak masuk ke database
            if (isBalitaOrEmpty) {
                $('#status_pekerjaan').val('');
                $('input[name="rekening_aktif"]').prop('checked', false);

                // Khusus Select2, butuh trigger change agar UI-nya kembali ke placeholder
                $('#lapangan_usaha').val('').trigger('change.select2');

                // Matikan paksa pesan error merah jika ada
                $('#lapangan_usaha, #status_pekerjaan, #rek_usaha').removeClass('is-invalid');
                $('#lapangan_usaha').next('.select2-container').find('.select2-selection').removeClass('border-danger');
                $('input[name="rekening_aktif"]').closest('.border').removeClass('border-danger').addClass('border-primary');
            }
        }

        // 1. Pemicu saat operator mengetik/memperbaiki Tanggal Lahir
        // Gunakan timeout kecil agar hidden input #tanggal_lahir sempat diisi oleh script sebelumnya
        $('#tanggal_lahir_display').on('keyup blur change', function() {
            setTimeout(toggleKunciTenagaKerja, 150);
        });

        // 2. Pemicu saat Modal pertama kali terbuka (Edit / Tambah Baru)
        $('#modalAnggota').on('shown.bs.modal', function() {
            setTimeout(toggleKunciTenagaKerja, 150);
        });

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

        /* ======================================================
        💾 EVENT SUBMIT FORM ANGGOTA (Tambah / Edit)
        ====================================================== */
        $(document).on('submit', '#formAnggota', function(e) {
            e.preventDefault();

            const form = $(this);
            let errorMessage = 'Pastikan kolom wajib terisi dan NIK/No KK terdiri dari 16 digit.';

            // 🚀 CEGAH DUPLIKASI KEPALA KELUARGA
            let hubunganDipilih = $('#hubungan option:selected').text().toLowerCase();
            let nikDiketik = $('#nik').val();

            if (hubunganDipilih === 'kepala keluarga') {
                if (window.nikKepalaKeluargaAktif && window.nikKepalaKeluargaAktif !== nikDiketik) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Dilarang Duplikat!',
                        text: 'Dalam 1 rumah tangga tidak boleh ada lebih dari 1 Kepala Keluarga. Silakan pilih status hubungan lain, atau ubah/hapus Kepala Keluarga sebelumnya terlebih dahulu.'
                    });
                    return;
                }
            }

            // ==============================================================
            // 🚀 SMART LOGIC: BACA STATUS KEBERADAAN (PENENTU VALIDASI)
            // ==============================================================
            let statusKeberadaan = $('#status_keberadaan').val();
            let isTinggalBersama = (statusKeberadaan === '1'); // 1 = Tinggal bersama keluarga

            // Jika statusnya belum dipilih sama sekali, blokir!
            if (!statusKeberadaan) {
                $('#status_keberadaan').addClass('is-invalid');
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Status Keberadaan wajib dipilih!'
                });
                return;
            } else {
                $('#status_keberadaan').removeClass('is-invalid');
            }

            // 1. Cek semua input yang wajib diisi (Required)
            form.find('.required:not(:disabled)').each(function() {

                // 🚀 KUNCI SAKTI: Jika ART meninggal/pindah, abaikan error di luar Tab Identitas!
                let isDiLuarIdentitas = ($(this).closest('#tab-identitas').length === 0);
                if (!isTinggalBersama && isDiLuarIdentitas) {
                    $(this).removeClass('is-invalid');
                    return true; // Lanjutkan loop (skip validasi ini)
                }

                if (!$(this).val().trim()) {
                    $(this).addClass('is-invalid');
                } else {
                    if (!['jenjang_pendidikan', 'kelas_tertinggi', 'ijazah_tertinggi', 'lapangan_usaha'].includes($(this).attr('id'))) {
                        $(this).removeClass('is-invalid');
                    }
                }
            });

            // 🚀 TAMBAHAN: Validasi Khusus Alamat Pindah
            if (statusKeberadaan === '3') { // Pindah Dalam Negeri
                form.find('.alamat-wajib-dn').each(function() {
                    if (!$(this).val().trim()) {
                        $(this).addClass('is-invalid');
                        errorMessage = 'Mohon lengkapi seluruh kolom Alamat Tujuan (Provinsi hingga Desa)!';
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
            } else if (statusKeberadaan === '4') { // Pindah Luar Negeri
                if (!form.find('input[name="negara_tujuan"]').val().trim()) {
                    form.find('input[name="negara_tujuan"]').addClass('is-invalid');
                    errorMessage = 'Sebutkan Nama Negara Tujuan!';
                } else {
                    form.find('input[name="negara_tujuan"]').removeClass('is-invalid');
                }
            }

            // 🚀 Buka paksa field yang di-disabled tepat sebelum AJAX agar diserialize dengan benar!
            form.find(':disabled').prop('disabled', false);

            // 2. Format dan cek presisi digit NIK / No KK
            ['#nik', '#keluarga_no_kk', '#individu_no_kk'].forEach(selector => {
                const el = form.find(selector);
                if (el.length) {
                    const value = el.val().replace(/\D/g, '');
                    el.val(value);

                    if (value.length > 0 && value.length !== 16) {
                        el.addClass('is-invalid');
                    } else if (value.length === 16) {
                        el.removeClass('is-invalid');
                    }
                }
            });

            // ==============================================================
            // 2.5 🚀 SMART VALIDATION: TAB TENAGA KERJA (HANYA DIEKSEKUSI JIKA TINGGAL BERSAMA)
            // ==============================================================
            let tglLahir = $('#tanggal_lahir').val();
            let usia = 0;

            if (tglLahir) {
                let dob = new Date(tglLahir);
                let today = new Date();
                usia = today.getFullYear() - dob.getFullYear();
                if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) {
                    usia--;
                }
            }

            // Bersihkan sisa error visual sebelumnya
            $('#lapangan_usaha, #status_pekerjaan, #rek_usaha').removeClass('is-invalid');
            $('#lapangan_usaha').next('.select2-container').find('.select2-selection').removeClass('border-danger');
            let $rekContainer = $('input[name="rekening_aktif"]').closest('.border');
            $rekContainer.removeClass('border-danger').addClass('border-primary');

            // 🚀 HANYA CEK TENAGA KERJA JIKA ART MASIH TINGGAL BERSAMA & USIA > 5 TAHUN
            if (isTinggalBersama && usia > 5) {
                let kerjaError = false;
                let lapanganUsahaVal = $('#lapangan_usaha').val();

                if (!lapanganUsahaVal) {
                    $('#lapangan_usaha').addClass('is-invalid');
                    $('#lapangan_usaha').next('.select2-container').find('.select2-selection').addClass('border-danger');
                    kerjaError = true;
                }

                if (lapanganUsahaVal === 'Lainnya') {
                    if (!$('#lapangan_usaha_lainnya').val().trim()) {
                        $('#lapangan_usaha_lainnya').addClass('is-invalid');
                        kerjaError = true;
                    }
                }

                if (lapanganUsahaVal !== 'Tidak Bekerja') {
                    if (!$('#status_pekerjaan').val()) {
                        $('#status_pekerjaan').addClass('is-invalid');
                        kerjaError = true;
                    }
                }

                if (!$('input[name="rekening_aktif"]:checked').val()) {
                    $('#rek_usaha').addClass('is-invalid');
                    $rekContainer.removeClass('border-primary').addClass('border-danger');
                    kerjaError = true;
                }

                if (kerjaError) {
                    errorMessage = 'Anggota keluarga usia di atas 5 tahun WAJIB mengisi data Pekerjaan dan Kepemilikan Rekening pada Tab Tenaga Kerja!';
                }
            }

            // 3. 🚀 FINAL GATEKEEPER: Abaikan elemen yang sedang dikunci (:not(:disabled))
            const invalidElements = form.find('.is-invalid:not(:disabled)');

            if (invalidElements.length > 0) {
                // Cek apakah errornya bersumber dari kecerdasan Tab Pendidikan
                const hasPendidikanError = invalidElements.filter('#jenjang_pendidikan, #kelas_tertinggi, #ijazah_tertinggi').length > 0;

                if (hasPendidikanError) {
                    errorMessage = 'Terdapat isian pendidikan/usia yang tidak logis (bergaris merah). Silakan periksa kembali Tab Pendidikan!';
                }

                // Tembakkan SweetAlert yang elegan
                Swal.fire({
                    icon: 'warning',
                    title: 'Isian Tidak Valid',
                    text: errorMessage,
                    customClass: {
                        title: 'fs-5',
                        content: 'fs-6'
                    },
                    width: '350px'
                });
                return; // Stop pengiriman data ke server!
            }

            // ==========================================
            // 🚀 JIKA LOLOS SEMUA GATEKEEPER -> SIMPAN!
            // ==========================================
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
                            title: 'Tersimpan',
                            text: res.message,
                            timer: 900,
                            showConfirmButton: false,
                            willClose: () => {
                                initTableAnggota();
                                $(document).trigger('anggota:saved');
                            }
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal menyimpan data.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    // Ambil teks error dari server (bisa jadi HTML error page dari CodeIgniter)
                    let errorMsg = xhr.responseText;

                    // Potong teks jika terlalu panjang agar popup tidak terlalu penuh
                    if (errorMsg.length > 500) {
                        errorMsg = errorMsg.substring(0, 500) + '...';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server (' + xhr.status + ')',
                        html: '<div class="text-start small text-danger" style="max-height: 200px; overflow-y: auto; text-align: left;">' + errorMsg + '</div>',
                        confirmButtonText: 'Tutup'
                    });

                    console.error("DETAIL ERROR AJAX:", xhr.responseText);
                }
            });
        });

        $('#modalAnggota').on('shown.bs.modal', function() {
            console.log('🧾 Modal Anggota terbuka, event submit aktif');
        });

        // Pemicu animasi peringatan saat dropdown Status Keberadaan diubah
        $(document).on('change', '#status_keberadaan', function() {
            if ($(this).val() !== '1' && $(this).val() !== '') {
                $('#alert-keberadaan').slideDown();
                // Opsional: Beri efek visual pada tab lain agar terlihat "tidak perlu diisi"
                $('.nav-tabs a[href="#tab-pendidikan"], .nav-tabs a[href="#tab-tenaga-kerja"], .nav-tabs a[href="#tab-kesehatan"]').css('opacity', '0.5');
            } else {
                $('#alert-keberadaan').slideUp();
                $('.nav-tabs a').css('opacity', '1');
            }
        });

        // ==============================================================
        // 🚀 UX CERDAS: Logika Lapangan Usaha (Tidak Bekerja & Lainnya)
        // ==============================================================
        $(document).on('change', '#lapangan_usaha', function() {
            let val = $(this).val();
            let $statusPekerjaan = $('#status_pekerjaan');
            let $divLainnya = $('#div_lapangan_usaha_lainnya');
            let $inputLainnya = $('#lapangan_usaha_lainnya');

            // 1. Logika Toggle Input "Lainnya"
            if (val === 'Lainnya') {
                $divLainnya.slideDown();
            } else {
                $divLainnya.slideUp();
                $inputLainnya.val('').removeClass('is-invalid');
            }

            // 2. Logika Kunci Status Pekerjaan
            if (val === 'Tidak Bekerja') {
                $statusPekerjaan.val('').prop('disabled', true).removeClass('is-invalid');
            } else {
                if (!$(this).prop('disabled')) {
                    $statusPekerjaan.prop('disabled', false);
                }
            }
        });

        /* ============================================================
         * ✏️ EVENT: Tombol Edit / Lihat Anggota
         * ============================================================ */
        // $(document).on('click', '.btnEditAnggota', function() {

        //     const id = $(this).data('id');
        //     if (!id) return Swal.fire("Info", "ID anggota tidak ditemukan.", "info");

        //     const idKkGlobal = $('#id_kk').val();
        //     $('#formAnggota')[0].reset(); // 🚀 Bersihkan sisa input sebelumnya
        //     $('#formAnggota #id_kk').val(idKkGlobal);

        //     Swal.fire({
        //         title: 'Memuat data...',
        //         allowOutsideClick: false,
        //         didOpen: () => Swal.showLoading()
        //     });

        //     $.getJSON(`${window.baseUrl}/<?= ($roleId == 6) ? 'sensus-ekonomi' : 'pembaruan-keluarga' ?>/get-anggota-detail/${id}`, function(res) {
        //         Swal.close();
        //         if (res.status !== 'success') return Swal.fire("Gagal", res.message, "error");

        //         const d = res.data.anggota_prefill;
        //         const drop = res.data.dropdowns;

        //         // Prefill semua input dasar
        //         $('#nik').val(d.nik ?? '');
        //         $('#nama').val(d.nama ?? '');
        //         $('#tempat_lahir').val(d.tempat_lahir ?? '');

        //         // 🚀 PREFILL: Konversi balik YYYY-MM-DD (DB) ke DD-MM-YYYY (Layar)
        //         const tglDB = d.tanggal_lahir ?? '';
        //         $('#tanggal_lahir').val(tglDB);

        //         if (tglDB && tglDB.includes('-')) {
        //             const parts = tglDB.split('-');
        //             if (parts.length === 3) {
        //                 $('#tanggal_lahir_display').val(`${parts[2]}-${parts[1]}-${parts[0]}`);
        //             }
        //         } else {
        //             $('#tanggal_lahir_display').val('');
        //         }

        //         // 🚀 PREFILL: Elemen Baru BPS (No HP)
        //         $('#no_hp').val(d.no_hp ?? '');

        //         // Prefill Jenis Kelamin
        //         $('input[name="jenis_kelamin"]').prop('checked', false);
        //         if (d.jenis_kelamin === 'L' || d.jenis_kelamin === 'P') {
        //             $(`input[name="jenis_kelamin"][value="${d.jenis_kelamin}"]`).prop('checked', true);
        //         }

        //         updateSelectOptions('#status_kawin', drop.status_kawin, d.status_kawin ?? d.status_kawin_label);
        //         updateSelectOptions('#hubungan', drop.hubungan, d.hubungan ?? d.hubungan_label);
        //         updateSelectOptions('#pekerjaan', drop.pekerjaan, d.pekerjaan ?? d.pekerjaan_label);
        //         updateSelectOptions('#pendidikan_terakhir', drop.pendidikan, d.pendidikan_terakhir ?? d.pendidikan_label);
        //         $('#ibu_kandung').val(d.ibu_kandung ?? '');
        //         $('#individu_no_kk').val($('#keluarga_no_kk').val()); // 🚀 Pastikan sinkron

        //         // ==============================================================
        //         // 🚀 TRANSLATOR STATUS KEBERADAAN (Dari Teks Lama BPS -> Kode Angka)
        //         // ==============================================================
        //         const statusMap = {
        //             'Tinggal Bersama Keluarga': '1',
        //             'Meninggal': '2',
        //             'Tidak Tinggal Bersama Keluarga/Pindah Ke Wilayah Lain': '3',
        //             'Tidak Tinggal Bersama Keluarga/Pindah Ke Luar Negeri': '4',
        //             'Sudah pisah kartu keluarga': '5',
        //             'Tidak Ditemukan': '6',
        //             'Tidak Ditemukan atau Tidak Dikenal': '6',
        //             'Belum Ditentukan': ''
        //         };
        //         let statusAsli = d.status_keberadaan ?? '';
        //         let statusFinal = statusMap[statusAsli] || statusAsli;

        //         // Set value dan trigger change agar form langsung Bereaksi (Kunci/Buka)
        //         $('#status_keberadaan').val(statusFinal).trigger('change');

        //         // ==============================================================
        //         // 🚀 PERBAIKAN WILAYAH (API Cascading Dropdown Pindah)
        //         // ==============================================================
        //         if (statusFinal === '3') {
        //             // Jika Pindah Dalam Negeri, ambil riwayat provinsi tujuan dari database
        //             const provTujuan = d.provinsi_tujuan ?? '';
        //             const kabTujuan = d.kabupaten_tujuan ?? '';
        //             const kecTujuan = d.kecamatan_tujuan ?? '';
        //             const desaTujuan = d.desa_tujuan ?? '';

        //             // Panggil API secara berantai agar dropdown tujuan terisi otomatis
        //             loadProvinces(provTujuan, () => loadRegencies(provTujuan, kabTujuan, () => loadDistricts(kabTujuan, kecTujuan, () => loadVillages(kecTujuan, desaTujuan))));

        //             // Isi input teks manual lainnya
        //             $('input[name="alamat_tujuan"]').val(d.alamat_tujuan ?? '');
        //             $('input[name="rt_tujuan"]').val(d.rt_tujuan ?? '');
        //             $('input[name="rw_tujuan"]').val(d.rw_tujuan ?? '');
        //             $('input[name="dusun_tujuan"]').val(d.dusun_tujuan ?? '');
        //         } else if (statusFinal === '4') {
        //             // Jika Pindah Luar Negeri
        //             $('input[name="negara_tujuan"]').val(d.negara_tujuan ?? '');
        //         }

        //         // 🚀 AMANKAN DOMISILI ASAL (Ke input hidden)
        //         const provAsal = d.provinsi ?? $('#rumah_provinsi').val() ?? '';
        //         const kabAsal = d.kabupaten ?? $('#rumah_regency').val() ?? '';
        //         const kecAsal = d.kecamatan ?? $('#rumah_district').val() ?? '';
        //         const desaAsal = d.desa ?? $('#rumah_village').val() ?? '';

        //         $('#ind_provinsi_hidden').val(provAsal);
        //         $('#ind_kabupaten_hidden').val(kabAsal);
        //         $('#ind_kecamatan_hidden').val(kecAsal);
        //         $('#ind_desa_hidden').val(desaAsal);

        //         // 🚀 TAMPILKAN TEKS DOMISILI KE DROPDOWN TERKUNCI
        //         $('#ind_provinsi').html($('#rumah_provinsi').html()).val(provAsal);
        //         $('#ind_kabupaten').html($('#rumah_regency').html()).val(kabAsal);
        //         $('#ind_kecamatan').html($('#rumah_district').html()).val(kecAsal);
        //         $('#ind_desa').html($('#rumah_village').html()).val(desaAsal);

        //         // ==============================================================
        //         // 🚀 PREFILL TAB PENDIDIKAN & TENAGA KERJA
        //         // ==============================================================
        //         let psVal = d.partisipasi_sekolah ?? '';
        //         let usiaBalita = 0;
        //         if (tglDB) {
        //             let dob = new Date(tglDB);
        //             let today = new Date();
        //             usiaBalita = today.getFullYear() - dob.getFullYear();
        //             if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) usiaBalita--;
        //         }

        //         if (usiaBalita < 5 && (!psVal || psVal === '')) psVal = 'Belum Pernah Sekolah';

        //         $('#partisipasi_sekolah').val(psVal).removeClass('is-invalid').trigger('change');
        //         $('#jenjang_pendidikan').val(d.jenjang_pendidikan ?? '');
        //         $('#kelas_tertinggi').val(d.kelas_tertinggi ?? '');
        //         $('#ijazah_tertinggi').val(d.ijazah_tertinggi ?? '');

        //         $('#bekerja_seminggu').val(d.bekerja_seminggu ?? '');
        //         $('#lapangan_usaha').val(d.lapangan_usaha ?? '').trigger('change');
        //         $('#lapangan_usaha_lainnya').val(d.lapangan_usaha_lainnya ?? '');
        //         $('#status_pekerjaan').val(d.status_pekerjaan ?? '');
        //         $('#pendapatan').val(d.pendapatan ?? '');

        //         $('input[name="rekening_aktif"]').prop('checked', false);
        //         if (d.rekening_aktif) {
        //             $(`input[name="rekening_aktif"][value="${d.rekening_aktif}"]`).prop('checked', true);
        //         }

        //         $('#memiliki_usaha').val(d.memiliki_usaha || '');
        //         $('#jumlah_usaha').val(d.jumlah_usaha || '');
        //         $('#pekerja_dibayar').val(d.pekerja_dibayar || '');
        //         $('#pekerja_tidak_dibayar').val(d.pekerja_tidak_dibayar || '');
        //         $('#omzet_bulanan').val(d.omzet_bulanan || '');
        //         toggleUsahaDetail();

        //         // ==============================================================
        //         // 🚀 PREFILL TAB KESEHATAN
        //         // ==============================================================
        //         $('#status_hamil').val(d.status_hamil ?? '');

        //         $('.kronis-check').prop('checked', false);
        //         if (Array.isArray(d.penyakit_kronis)) {
        //             d.penyakit_kronis.forEach(val => $(`.kronis-check[value="${val}"]`).prop('checked', true));
        //         } else if (typeof d.penyakit_kronis === 'string' && d.penyakit_kronis.trim() !== '') {
        //             try {
        //                 const parsed = JSON.parse(d.penyakit_kronis);
        //                 if (Array.isArray(parsed)) parsed.forEach(val => $(`.kronis-check[value="${val}"]`).prop('checked', true));
        //             } catch (e) {
        //                 $(`.kronis-check[value="${d.penyakit_kronis}"]`).prop('checked', true);
        //             }
        //         }

        //         if (Array.isArray(d.disabilitas)) {
        //             $('.disab-check').prop('checked', false);
        //             d.disabilitas.forEach(val => $(`.disab-check[value="${val}"]`).prop('checked', true));
        //         } else if (typeof d.disabilitas === 'string' && d.disabilitas.trim() !== '') {
        //             try {
        //                 const parsed = JSON.parse(d.disabilitas);
        //                 if (Array.isArray(parsed)) {
        //                     $('.disab-check').prop('checked', false);
        //                     parsed.forEach(val => $(`.disab-check[value="${val}"]`).prop('checked', true));
        //                 }
        //             } catch (e) {}
        //         }

        //         // ==============================================================
        //         // 🚀 UI FINISHING
        //         // ==============================================================
        //         let modeTeks = isEditable ? 'Edit Anggota:' : 'Detail Anggota:';
        //         $('#mode_teks_header').text(modeTeks);
        //         $('#nama_anggota_header').text(d.nama ? d.nama.toUpperCase() : 'TANPA NAMA');
        //         $('#formAnggota #id_kk').val($('#id_kk').val() || $('[name="id_kk"]').val());
        //         $('#tabAnggotaTabs a:first').tab('show');

        //         $('#modalAnggota').modal('show');

        //         // Pastikan status_pekerjaan dll disesuaikan berdasarkan usia saat modal terbuka
        //         setTimeout(toggleKunciTenagaKerja, 150);

        //     }).fail(() => Swal.fire("Error", "Gagal memuat data anggota.", "error"));
        // });

        /* ======================================================
        ➕ EVENT: Tambah Anggota Baru (Gunakan Modal yang Sama)
        ====================================================== */
        // $(document).on('click', '#btnTambahAnggota', function() {
        //     console.log('🆕 Tambah Anggota Baru diklik');

        //     Swal.fire({
        //         title: 'Memuat form...',
        //         allowOutsideClick: false,
        //         didOpen: () => Swal.showLoading()
        //     });

        //     $.getJSON(`${window.baseUrl}/<?= ($roleId == 6) ? 'sensus-ekonomi' : 'pembaruan-keluarga' ?>/get-anggota-detail`, function(res) {
        //         Swal.close();

        //         // 🚀 ANTI-BOCOR: Hapus isi form secara paksa
        //         $('#formAnggota')[0].reset();
        //         $('#id_anggota').val('');
        //         $('#no_hp').val('');
        //         $('input[name="rekening_aktif"]').prop('checked', false);

        //         const idKk = $('#id_kk').val() || $('[name="id_kk"]').val();

        //         $('#formAnggota #id_kk').val(idKk);

        //         // 🚀 Kosongkan nama di header karena ini mode tambah baru
        //         $('#mode_teks_header').text('Tambah Anggota Baru');
        //         $('#nama_anggota_header').text('');

        //         $('#ind_provinsi, #ind_kabupaten, #ind_kecamatan, #ind_desa').html('<option value="">Pilih...</option>').val('').trigger('change');

        //         $('.skill-check, .disab-check').prop('checked', false);

        //         const drop = res.data?.dropdowns ?? {};
        //         updateSelectOptions('#status_kawin', drop.status_kawin);
        //         updateSelectOptions('#hubungan', drop.hubungan);
        //         updateSelectOptions('#pekerjaan', drop.pekerjaan);
        //         updateSelectOptions('#pendidikan_terakhir', drop.pendidikan);

        //         $('#tabAnggotaTabs a:first').tab('show');

        //         // 🚀 PERBAIKAN WILAYAH (TAMBAH BARU): Tembak langsung dari Tab Rumah
        //         const prov = $('#rumah_provinsi').val() ?? '';
        //         const kab = $('#rumah_regency').val() ?? '';
        //         const kec = $('#rumah_district').val() ?? '';
        //         const desa = $('#rumah_village').val() ?? '';

        //         // Amankan Nomor KK & Hidden Input Domisili
        //         $('#individu_no_kk').val($('#keluarga_no_kk').val());
        //         $('#ind_provinsi_hidden').val(prov);
        //         $('#ind_kabupaten_hidden').val(kab);
        //         $('#ind_kecamatan_hidden').val(kec);
        //         $('#ind_desa_hidden').val(desa);

        //         // Tampilkan Teks Domisili ke Dropdown Terkunci
        //         $('#ind_provinsi').html($('#rumah_provinsi').html()).val(prov);
        //         $('#ind_kabupaten').html($('#rumah_regency').html()).val(kab);
        //         $('#ind_kecamatan').html($('#rumah_district').html()).val(kec);
        //         $('#ind_desa').html($('#rumah_village').html()).val(desa);

        //         $('#modalAnggota').modal('show');
        //     }).fail(() => {
        //         Swal.close();
        //         Swal.fire('Error', 'Gagal memuat form tambah anggota.', 'error');
        //     });
        // });

        /* ============================================================
         * ✏️ EVENT: Tombol Edit / Lihat Anggota
         * ============================================================ */
        $(document).on('click', '.btnEditAnggota', function() {

            const id = $(this).data('id');
            if (!id) return Swal.fire("Info", "ID anggota tidak ditemukan.", "info");

            const idKkGlobal = $('#id_kk').val();
            $('#formAnggota')[0].reset();

            // 🚀 SAPU BERSIH ERROR DARI INTERAKSI SEBELUMNYA (ANTI-NYANGKUT)
            $('#formAnggota').find('.is-invalid').removeClass('is-invalid');
            $('#formAnggota').find('.border-danger').removeClass('border-danger');
            $('#formAnggota').find('.invalid-feedback').html('').removeClass('d-block');

            $('#formAnggota #id_kk').val(idKkGlobal);

            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.getJSON(`${window.baseUrl}/<?= ($roleId == 6) ? 'sensus-ekonomi' : 'pembaruan-keluarga' ?>/get-anggota-detail/${id}`, function(res) {
                Swal.close();
                if (res.status !== 'success') return Swal.fire("Gagal", res.message, "error");

                const d = res.data.anggota_prefill;
                const drop = res.data.dropdowns;

                // Prefill semua input dasar
                $('#nik').val(d.nik ?? '');
                $('#nama').val(d.nama ?? '');
                $('#tempat_lahir').val(d.tempat_lahir ?? '');

                const tglDB = d.tanggal_lahir ?? '';
                $('#tanggal_lahir').val(tglDB);

                if (tglDB && tglDB.includes('-')) {
                    const parts = tglDB.split('-');
                    if (parts.length === 3) {
                        $('#tanggal_lahir_display').val(`${parts[2]}-${parts[1]}-${parts[0]}`);
                    }
                } else {
                    $('#tanggal_lahir_display').val('');
                }

                $('#no_hp').val(d.no_hp ?? '');

                $('input[name="jenis_kelamin"]').prop('checked', false);
                if (d.jenis_kelamin === 'L' || d.jenis_kelamin === 'P') {
                    $(`input[name="jenis_kelamin"][value="${d.jenis_kelamin}"]`).prop('checked', true);
                }

                updateSelectOptions('#status_kawin', drop.status_kawin, d.status_kawin ?? d.status_kawin_label);
                updateSelectOptions('#hubungan', drop.hubungan, d.hubungan ?? d.hubungan_label);
                updateSelectOptions('#pekerjaan', drop.pekerjaan, d.pekerjaan ?? d.pekerjaan_label);
                updateSelectOptions('#pendidikan_terakhir', drop.pendidikan, d.pendidikan_terakhir ?? d.pendidikan_label);
                $('#ibu_kandung').val(d.ibu_kandung ?? '');
                $('#individu_no_kk').val($('#keluarga_no_kk').val());

                const statusMap = {
                    'Tinggal Bersama Keluarga': '1',
                    'Meninggal': '2',
                    'Tidak Tinggal Bersama Keluarga/Pindah Ke Wilayah Lain': '3',
                    'Tidak Tinggal Bersama Keluarga/Pindah Ke Luar Negeri': '4',
                    'Sudah pisah kartu keluarga': '5',
                    'Tidak Ditemukan': '6',
                    'Tidak Ditemukan atau Tidak Dikenal': '6',
                    'Belum Ditentukan': ''
                };
                let statusAsli = d.status_keberadaan ?? '';
                let statusFinal = statusMap[statusAsli] || statusAsli;

                $('#status_keberadaan').val(statusFinal).trigger('change');

                if (statusFinal === '3') {
                    const provTujuan = d.provinsi_tujuan ?? '';
                    const kabTujuan = d.kabupaten_tujuan ?? '';
                    const kecTujuan = d.kecamatan_tujuan ?? '';
                    const desaTujuan = d.desa_tujuan ?? '';

                    loadProvinces(provTujuan, () => loadRegencies(provTujuan, kabTujuan, () => loadDistricts(kabTujuan, kecTujuan, () => loadVillages(kecTujuan, desaTujuan))));

                    $('input[name="alamat_tujuan"]').val(d.alamat_tujuan ?? '');
                    $('input[name="rt_tujuan"]').val(d.rt_tujuan ?? '');
                    $('input[name="rw_tujuan"]').val(d.rw_tujuan ?? '');
                    $('input[name="dusun_tujuan"]').val(d.dusun_tujuan ?? '');
                } else if (statusFinal === '4') {
                    $('input[name="negara_tujuan"]').val(d.negara_tujuan ?? '');
                }

                // ==============================================================
                // 🚀 AMANKAN DOMISILI ASAL (Penyelamat Data Kosong dari Tab Rumah)
                // ==============================================================
                // Ambil alamat dari Database Anggota, JIKA KOSONG, Kloning paksa dari Tab Keluarga!
                const provAsal = (d.provinsi && d.provinsi !== '') ? d.provinsi : ($('#rumah_provinsi').val() ?? '');
                const kabAsal = (d.kabupaten && d.kabupaten !== '') ? d.kabupaten : ($('#rumah_regency').val() ?? '');
                const kecAsal = (d.kecamatan && d.kecamatan !== '') ? d.kecamatan : ($('#rumah_district').val() ?? '');
                const desaAsal = (d.desa && d.desa !== '') ? d.desa : ($('#rumah_village').val() ?? '');

                // 1. Amankan di Hidden Input agar tersimpan ke Database saat disubmit
                $('#ind_provinsi_hidden').val(provAsal);
                $('#ind_kabupaten_hidden').val(kabAsal);
                $('#ind_kecamatan_hidden').val(kecAsal);
                $('#ind_desa_hidden').val(desaAsal);

                // 2. Tampilkan langsung di Dropdown Terkunci Modal Anggota (Kloning Teks)
                $('#ind_provinsi').html($('#rumah_provinsi').html()).val(provAsal);
                $('#ind_kabupaten').html($('#rumah_regency').html()).val(kabAsal);
                $('#ind_kecamatan').html($('#rumah_district').html()).val(kecAsal);
                $('#ind_desa').html($('#rumah_village').html()).val(desaAsal);

                // ==============================================================
                // 🚀 PREFILL TAB PENDIDIKAN & TENAGA KERJA
                // ==============================================================
                let psVal = d.partisipasi_sekolah ?? '';
                let usiaBalita = 0;
                if (tglDB) {
                    let dob = new Date(tglDB);
                    let today = new Date();
                    usiaBalita = today.getFullYear() - dob.getFullYear();
                    if (today.getMonth() < dob.getMonth() || (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())) usiaBalita--;
                }

                if (usiaBalita < 5 && (!psVal || psVal === '')) psVal = 'Belum Pernah Sekolah';

                $('#partisipasi_sekolah').val(psVal).removeClass('is-invalid').trigger('change');

                // 🚀 COPOT PAKSA CLASS MERAH SAAT PREFILL (ANTI-NYANGKUT)
                $('#jenjang_pendidikan').val(d.jenjang_pendidikan ?? '').removeClass('is-invalid');
                $('#kelas_tertinggi').val(d.kelas_tertinggi ?? '').removeClass('is-invalid');
                $('#ijazah_tertinggi').val(d.ijazah_tertinggi ?? '').removeClass('is-invalid');

                // 🚀 TRIGGER ULANG VALIDASI BPS SECARA HALUS AGAR MENGHITUNG ULANG
                setTimeout(() => {
                    $('#jenjang_pendidikan, #kelas_tertinggi, #ijazah_tertinggi').trigger('change');
                }, 50);

                $('#bekerja_seminggu').val(d.bekerja_seminggu ?? '');
                $('#lapangan_usaha').val(d.lapangan_usaha ?? '').trigger('change');
                $('#lapangan_usaha_lainnya').val(d.lapangan_usaha_lainnya ?? '');
                $('#status_pekerjaan').val(d.status_pekerjaan ?? '');
                $('#pendapatan').val(d.pendapatan ?? '');

                $('input[name="rekening_aktif"]').prop('checked', false);
                if (d.rekening_aktif) {
                    $(`input[name="rekening_aktif"][value="${d.rekening_aktif}"]`).prop('checked', true);
                }

                $('#memiliki_usaha').val(d.memiliki_usaha || '');
                $('#jumlah_usaha').val(d.jumlah_usaha || '');
                $('#pekerja_dibayar').val(d.pekerja_dibayar || '');
                $('#pekerja_tidak_dibayar').val(d.pekerja_tidak_dibayar || '');
                $('#omzet_bulanan').val(d.omzet_bulanan || '');
                toggleUsahaDetail();

                // ==============================================================
                // 🚀 PREFILL TAB KESEHATAN
                // ==============================================================
                $('#status_hamil').val(d.status_hamil ?? '');

                $('.kronis-check').prop('checked', false);
                if (Array.isArray(d.penyakit_kronis)) {
                    d.penyakit_kronis.forEach(val => $(`.kronis-check[value="${val}"]`).prop('checked', true));
                } else if (typeof d.penyakit_kronis === 'string' && d.penyakit_kronis.trim() !== '') {
                    try {
                        const parsed = JSON.parse(d.penyakit_kronis);
                        if (Array.isArray(parsed)) parsed.forEach(val => $(`.kronis-check[value="${val}"]`).prop('checked', true));
                    } catch (e) {
                        $(`.kronis-check[value="${d.penyakit_kronis}"]`).prop('checked', true);
                    }
                }

                if (Array.isArray(d.disabilitas)) {
                    $('.disab-check').prop('checked', false);
                    d.disabilitas.forEach(val => $(`.disab-check[value="${val}"]`).prop('checked', true));
                } else if (typeof d.disabilitas === 'string' && d.disabilitas.trim() !== '') {
                    try {
                        const parsed = JSON.parse(d.disabilitas);
                        if (Array.isArray(parsed)) {
                            $('.disab-check').prop('checked', false);
                            parsed.forEach(val => $(`.disab-check[value="${val}"]`).prop('checked', true));
                        }
                    } catch (e) {}
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
                    } catch (e) {}
                }

                let modeTeks = isEditable ? 'Edit Anggota:' : 'Detail Anggota:';
                $('#mode_teks_header').text(modeTeks);
                $('#nama_anggota_header').text(d.nama ? d.nama.toUpperCase() : 'TANPA NAMA');
                $('#formAnggota #id_kk').val($('#id_kk').val() || $('[name="id_kk"]').val());
                $('#tabAnggotaTabs a:first').tab('show');

                $('#modalAnggota').modal('show');

                setTimeout(toggleKunciTenagaKerja, 150);

            }).fail(() => Swal.fire("Error", "Gagal memuat data anggota.", "error"));
        });

        /* ======================================================
        ➕ EVENT: Tambah Anggota Baru (Gunakan Modal yang Sama)
        ====================================================== */
        $(document).on('click', '#btnTambahAnggota', function() {
            Swal.fire({
                title: 'Memuat form...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.getJSON(`${window.baseUrl}/<?= ($roleId == 6) ? 'sensus-ekonomi' : 'pembaruan-keluarga' ?>/get-anggota-detail`, function(res) {
                Swal.close();

                // 🚀 ANTI-BOCOR: Hapus isi form secara paksa
                $('#formAnggota')[0].reset();

                // 🚀 SAPU BERSIH ERROR DARI INTERAKSI SEBELUMNYA (ANTI-NYANGKUT)
                $('#formAnggota').find('.is-invalid').removeClass('is-invalid');
                $('#formAnggota').find('.border-danger').removeClass('border-danger');
                $('#formAnggota').find('.invalid-feedback').html('').removeClass('d-block');

                $('#id_anggota').val('');
                $('#no_hp').val('');
                $('input[name="rekening_aktif"]').prop('checked', false);

                const idKk = $('#id_kk').val() || $('[name="id_kk"]').val();
                $('#formAnggota #id_kk').val(idKk);

                // 🚀 Kosongkan nama di header karena ini mode tambah baru
                $('#mode_teks_header').text('Tambah Anggota Baru');
                $('#nama_anggota_header').text('');

                $('.skill-check, .disab-check').prop('checked', false);

                const drop = res.data?.dropdowns ?? {};
                updateSelectOptions('#status_kawin', drop.status_kawin);
                updateSelectOptions('#hubungan', drop.hubungan);
                updateSelectOptions('#pekerjaan', drop.pekerjaan);
                updateSelectOptions('#pendidikan_terakhir', drop.pendidikan);

                $('#tabAnggotaTabs a:first').tab('show');

                // 🚀 PERBAIKAN WILAYAH (TAMBAH BARU): Tembak langsung dari Tab Rumah
                const prov = $('#rumah_provinsi').val() ?? '';
                const kab = $('#rumah_regency').val() ?? '';
                const kec = $('#rumah_district').val() ?? '';
                const desa = $('#rumah_village').val() ?? '';

                // 1. Amankan Nomor KK & Hidden Input Domisili
                $('#individu_no_kk').val($('#keluarga_no_kk').val());
                $('#ind_provinsi_hidden').val(prov);
                $('#ind_kabupaten_hidden').val(kab);
                $('#ind_kecamatan_hidden').val(kec);
                $('#ind_desa_hidden').val(desa);

                // 2. Tampilkan Teks Domisili ke Dropdown Terkunci (Bukan dikosongkan!)
                $('#ind_provinsi').html($('#rumah_provinsi').html()).val(prov);
                $('#ind_kabupaten').html($('#rumah_regency').html()).val(kab);
                $('#ind_kecamatan').html($('#rumah_district').html()).val(kec);
                $('#ind_desa').html($('#rumah_village').html()).val(desa);

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
            const val = $(this).val();

            // 🚀 Bersihkan error pada diri sendiri jika sudah ada pilihan
            if (val && val !== '') {
                $(this).removeClass('is-invalid');
            }

            const disable = (val === 'Belum Pernah Sekolah');
            ['#jenjang_pendidikan', '#kelas_tertinggi', '#ijazah_tertinggi'].forEach(id => {
                $(id).prop('disabled', disable);
                if (disable) {
                    // 🚀 PAKSA KOSONG & HAPUS MERAH SAAT TERKUNCI
                    $(id).val('').removeClass('is-invalid');
                }
            });

            if (disable) {
                $('#fb_jenjang, #fb_kelas, #fb_ijazah').html('');
            }
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

        // ==========================================
        // 🛡️ FUNGSI BANTUAN: SENSOR DATA SENSITIF (JS)
        // ==========================================
        function maskNumberJS(number) {
            if (!number || number === '-' || number === 'NOKKS') return number || '-';
            let numStr = number.toString().trim();
            if (numStr.length <= 8) return numStr;
            let masked = numStr.substring(0, 8) + '*'.repeat(numStr.length - 8);
            return `<span class="fw-bold text-primary" style="cursor:pointer;" 
                      onmouseenter="this.innerText='${numStr}'" 
                      onmouseleave="this.innerText='${masked}'" 
                      title="Tahan/Arahkan kursor untuk melihat utuh">${masked}</span>`;
        }

        let tableAnggota;

        function initTableAnggota() {
            const idKk = $('#id_kk').val();
            if (!idKk) return;

            if ($.fn.DataTable.isDataTable('#tableAnggota')) {
                tableAnggota.ajax.reload(null, false);
                return;
            }

            tableAnggota = $('#tableAnggota').DataTable({
                destroy: true,
                processing: true,
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                autoWidth: false,
                pageLength: 10,
                ajax: {
                    url: `${window.baseUrl}/<?= ($roleId == 6) ? 'sensus-ekonomi' : 'pembaruan-keluarga' ?>/get-anggota-list/${idKk}`,
                    type: 'GET',
                    dataSrc: function(json) {
                        if (!json || json.status !== 'success') {
                            Swal.fire('Error', json?.message || 'Gagal memuat anggota', 'error');
                            return [];
                        }
                        return json.data;
                    }
                },
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                drawCallback: function(settings) {
                    let totalAnggota = this.api().rows().count();
                    if (typeof window.updateJumlahAnggotaOtomatis === 'function') {
                        window.updateJumlahAnggotaOtomatis(totalAnggota);
                    }

                    // 🚀 SMART SYNC KEPALA KELUARGA: Cari NIK & Nama Kepala Keluarga lalu tembak ke Tab Keluarga
                    let dtApi = this.api();
                    let kepalaDitemukan = false;

                    dtApi.rows().every(function() {
                        let data = this.data();
                        let hubungan = (data.hubungan_keluarga_label || data.jenis_shdk || data.hubungan_keluarga || '').toLowerCase();

                        if (hubungan === 'kepala keluarga') {
                            kepalaDitemukan = true;
                            $('#kepala_keluarga').val(data.nama ? data.nama.toUpperCase() : '');
                            $('#nik_kepala_keluarga').val(data.nik || '');
                            $('#kepala_keluarga, #nik_kepala_keluarga').removeClass('is-invalid');
                            window.nikKepalaKeluargaAktif = data.nik;
                        }
                    });

                    if (!kepalaDitemukan) {
                        $('#kepala_keluarga').val('');
                        $('#nik_kepala_keluarga').val('');
                        window.nikKepalaKeluargaAktif = null;
                    }
                },
                columns: [{
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control text-start',
                        orderable: false,
                        width: '20px'
                    },
                    {
                        data: null,
                        className: 'text-start',
                        width: '40px',
                        render: (d, t, r, m) => m.row + 1
                    },
                    {
                        data: 'nama',
                        className: 'text-nowrap text-start fw-bold',
                        render: function(data, type, row) {
                            let nama = data || '-';

                            // 🚀 SMART CHECK: Abaikan peringatan jika statusnya Meninggal (2), Pisah KK (5), atau Hilang (6)
                            let status = (row.status_keberadaan !== null && row.status_keberadaan !== undefined) ? String(row.status_keberadaan).trim() : '';
                            let isMeninggalAtauHilang = ['2', '5', '6'].includes(status);

                            // Lengkap jika: Wilayah terisi, ATAU statusnya memang membebaskan pengisian wilayah
                            let isLengkap = isMeninggalAtauHilang || (row.provinsi && row.kabupaten && row.kecamatan && row.desa);

                            if (!isLengkap && type === 'display') {
                                return `${nama} <span class="badge bg-danger rounded-circle ms-2 shadow-sm" title="Data belum lengkap!" style="width: 14px; height: 14px; padding: 0; display: inline-flex; align-items: center; justify-content: center; animation: pulse 2s infinite; vertical-align: middle;"><i class="fas fa-exclamation" style="font-size: 8px;"></i></span>`;
                            }
                            return nama;
                        }
                    },
                    {
                        data: 'nik',
                        className: 'text-nowrap text-start',
                        render: function(data, type, row) {
                            if (!data) return '-';
                            if (type === 'filter' || type === 'sort') return data;
                            let maskedData = maskNumberJS(data);
                            return `<div class="d-flex align-items-center gap-2"><span class="fw-semibold">${maskedData}</span><button type="button" class="btn btn-outline-secondary btn-xs btnCopyNik" data-value="${data}" title="Salin NIK"><i class="fas fa-copy"></i></button></div>`;
                        }
                    },
                    {
                        data: 'tanggal_lahir',
                        className: 'text-nowrap text-start',
                        render: d => d ? new Date(d).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) : '-'
                    },
                    {
                        data: null,
                        className: 'text-nowrap text-start',
                        render: row => row.hubungan_keluarga_label ?? row.jenis_shdk ?? row.hubungan_keluarga ?? '-'
                    },
                    {
                        data: 'pekerjaan_label',
                        className: 'text-nowrap text-start',
                        defaultContent: '-'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-start',
                        width: '140px',
                        render: function(r) {
                            if (!isEditable) {
                                return `
                                    <a href="javascript:void(0)" class="btn btn-info btn-sm btnEditAnggota shadow-sm" data-id="${r.id_art ?? r.id}" title="Lihat Detail Data">
                                        <i class="fas fa-eye me-1"></i> Lihat
                                    </a>
                                `;
                            }
                            return `
                                <div class="btn-group btn-group-sm shadow-sm">
                                    <button class="btn btn-primary btnEditAnggota" data-id="${r.id_art ?? r.id}" title="Edit Data Anggota">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btnHapusAnggota" data-id="${r.id_art ?? r.id}" title="Hapus Data Anggota">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                columnDefs: [{
                    targets: '_all',
                    className: 'text-start align-middle'
                }],
                order: []
            });
        }

        initTableAnggota();
        $(document).on('anggota:saved', function() {
            if (tableAnggota) tableAnggota.ajax.reload(null, false);
        });

        /* ======================================================
           🔁 TOMBOL RELOAD DAFTAR ANGGOTA (FIXED)
           ====================================================== */
        $(document).on('click', '#btnReloadAnggota', function() {

            Swal.fire({
                title: 'Memuat ulang data...',
                text: 'Harap tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            if ($.fn.DataTable.isDataTable('#tableAnggota')) {
                tableAnggota.ajax.reload(function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Daftar anggota berhasil dimuat ulang.',
                        timer: 800,
                        showConfirmButton: false
                    });
                }, false);
            } else {
                initTableAnggota();
                Swal.close();
            }
        });

        /* ============================================================
         * 🗑️ EVENT: Hapus Anggota
         * ============================================================ */
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
                        initTableAnggota();
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                }, 'json');
            });
        });

        // ========================================================
        // 📋 FUNGSI SALIN KE CLIPBOARD (NIK & NO KK)
        // ========================================================
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        $(document).on('click', '.btnCopyNoKK', function() {
            const value = $(this).attr('data-value');

            navigator.clipboard.writeText(value).then(() => {
                Toast.fire({
                    icon: 'success',
                    title: 'No. KK berhasil disalin!'
                });
            }).catch(err => {
                console.error('Gagal menyalin teks: ', err);
                Toast.fire({
                    icon: 'error',
                    title: 'Gagal menyalin No. KK'
                });
            });
        });

        $(document).on('click', '.btnCopyNik', function() {
            const value = $(this).attr('data-value');

            navigator.clipboard.writeText(value).then(() => {
                Toast.fire({
                    icon: 'success',
                    title: 'NIK berhasil disalin!'
                });
            }).catch(err => {
                console.error('Gagal menyalin teks: ', err);
                Toast.fire({
                    icon: 'error',
                    title: 'Gagal menyalin NIK'
                });
            });
        });

        // ========================================================
        // 🚀 TRIGGER SIMPAN GLOBAL DARI TAB ANGGOTA
        // ========================================================
        $(document).on('click', '#btnTriggerSimpanGlobal', function() {

            // 1. Cek dulu apakah seluruh data anggota sudah bebas dari tanda seru merah
            if (typeof window.cekKelengkapanAnggota === 'function' && window.cekKelengkapanAnggota()) {

                // 2. Lacak keberadaan tombol "Simpan Data & Lokasi" di Tab Keluarga
                let btnTarget = $('#btnSimpanDataLokasi');

                // (Fallback: Jika Jenderal lupa pasang ID di langkah 1, sistem akan mencari berdasarkan Teks-nya)
                if (btnTarget.length === 0) {
                    btnTarget = $('button[type="submit"]:contains("Simpan Data & Lokasi")');
                }

                // 3. Eksekusi klik otomatis seperti layaknya hantu! 👻
                if (btnTarget.length > 0) {
                    btnTarget.trigger('click');
                } else {
                    // Jika tombol tidak ditemukan di layar, kembalikan ke fungsi lawas (reload paksa)
                    window.location.reload();
                }
            }
        });

        // ========================================================
        // 🛡️ FUNGSI GLOBAL: CEK KELENGKAPAN ANGGOTA SEBELUM APPLY
        // ========================================================
        window.cekKelengkapanAnggota = function() {
            let adaYangBelumLengkap = false;

            let table = $('#tableAnggota').DataTable();

            table.rows().every(function() {
                let data = this.data();

                // 🚀 SMART CHECK: Sama seperti kolom nama, konversi aman.
                let status = (data.status_keberadaan !== null && data.status_keberadaan !== undefined) ? String(data.status_keberadaan).trim() : '';
                let isMeninggalAtauHilang = ['2', '5', '6'].includes(status);

                let isLengkap = isMeninggalAtauHilang || (
                    data.provinsi && String(data.provinsi).trim() !== '' &&
                    data.kabupaten && String(data.kabupaten).trim() !== '' &&
                    data.kecamatan && String(data.kecamatan).trim() !== '' &&
                    data.desa && String(data.desa).trim() !== ''
                );

                if (!isLengkap) {
                    adaYangBelumLengkap = true;
                }
            });

            if (adaYangBelumLengkap) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Anggota Belum Lengkap!',
                    html: 'Masih ada anggota keluarga dengan tanda <span class="badge bg-danger rounded-circle p-1 mx-1"><i class="fas fa-exclamation" style="font-size: 10px;"></i></span><br><br>Silakan klik tombol <b><i class="fas fa-edit text-primary"></i> Edit</b> pada anggota tersebut dan lengkapi data yang masih kosong.',
                    confirmButtonText: 'Mengerti',
                    width: '350px',
                    customClass: {
                        title: 'fs-5',
                        content: 'fs-6'
                    }
                });
                return false;
            }

            return true;
        };

    }); // <-- Akhir dari document.ready
</script>