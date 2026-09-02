/* ======================================================
 🏠 public/assets/js/pembaruan_keluarga.js
 Versi Sinkronisasi 2025-11-09
 - Prefill lengkap Tab Keluarga & Rumah
 - AJAX Select2 Wilayah (prov, kab, kec, desa)
 - Merge data foto, aset, rumah, keluarga tanpa overwrite
 - SweetAlert feedback
====================================================== */

$(document).ready(function () {
    const baseUrl = window.baseUrl || $('meta[name="base-url"]').attr('content') || '';

    /* ======================================================
     🧩 Fungsi Bantuan
    ======================================================= */

    const showError = (title, text) => Swal.fire(title, text, 'error');
    const showSuccess = (title, text) => Swal.fire(title, text, 'success');

    const readPreview = (input, target) => {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => $(target).attr('src', e.target.result);
            reader.readAsDataURL(input.files[0]);
        }
    };

    // ------------------------
    // ASET TIDAK BERGERAK
    // ------------------------
    function syncMemilikiLahan() {
        const luas = $('select[name="luas_sawah"]').val();
        const $lahan = $('#memiliki_lahan');

        if (!luas) {
            $lahan.val('');
            return;
        }

        if (luas === 'TIDAK MEMILIKI') {
            $lahan.val('TIDAK');
        } else {
            $lahan.val('YA');
        }
    }

    $(document).on('mousedown', '#memiliki_lahan', function (e) {
        e.preventDefault();
    });

    // Trigger saat user ganti luas sawah
    $(document).on('change', 'select[name="luas_sawah"]', syncMemilikiLahan);

    // Trigger saat halaman load (prefill edit)
    $(document).ready(syncMemilikiLahan);

    // ------------------------
    // FASILITAS BAB
    // ------------------------
    function toggleFasilitasBab() {
        const val = $('#fasilitas_bab').val();

        if (val === "Tidak ada fasilitas") {

            // Sembunyikan elemen
            $('.fasilitas-extra').hide();

            // Kosongkan value
            $('#jenis_kloset').val("");
            $('#pembuangan_tinja').val("");

            // Disable agar tidak terkirim
            $('#jenis_kloset, #pembuangan_tinja').prop('disabled', true);

        } else {

            // Tampilkan elemen
            $('.fasilitas-extra').show();

            // Aktifkan kembali
            $('#jenis_kloset, #pembuangan_tinja').prop('disabled', false);
        }
    }

    // Event perubahan
    $('#fasilitas_bab').on('change', toggleFasilitasBab);

    // Trigger saat halaman load
    toggleFasilitasBab();

    // ===================================================================
    // 🎓 SMART EDUCATION MODULE — FINAL FIX (KELAS 8 + VALIDASI USIA)
    // ===================================================================

    // ------------------------
    // 1. LEVEL PENDIDIKAN
    // ------------------------
    const jenjangLevel = {
        "Belum Ditentukan": 0,
        "Tidak Punya Ijazah SD": 0,
        "Paket A": 0, "SDLB": 0, "SD": 0, "MI": 0, "SPM/PDF Ula": 0,

        "Paket B": 1, "SMP LB": 1, "SMP": 1, "MTS": 1, "SPM/PDF Wustha": 1,

        "Paket C": 2, "SMLB": 2, "SMA": 2, "MA": 2,
        "SMK": 2, "MAK": 2, "SPM/PDF Ulya": 2,

        "DI/D2/D3": 3,
        "D4/S1": 4, "Profesi": 4,
        "S2": 5,
        "S3": 6
    };

    // ------------------------
    // 2. VALIDASI KELAS
    // ------------------------
    const kelasValid = {
        level0: [1,2,3,4,5,6],     // SD
        level1: [1,2,3],          // SMP
        level2: [1,2,3,4],        // SMA/SMK
        levelPT: [1,2,3,4,5,6,7,8]
    };

    // ------------------------
    // 3. Hitung Usia dari Tanggal Lahir
    // ------------------------
    function getUsia() {
        const tgl = $('#tanggal_lahir').val();
        if (!tgl) return null;

        const dob = new Date(tgl);
        const now = new Date();
        
        let usia = now.getFullYear() - dob.getFullYear();
        const m = now.getMonth() - dob.getMonth();

        if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) usia--;

        return usia;
    }

    // ===================================================================
    // 4. MASTER SWITCH — PARTISIPASI SEKOLAH
    // ===================================================================
    function handlePartisipasiSekolah() {
        const ps = $('#partisipasi_sekolah').val();
        const fJenjang = $('#jenjang_pendidikan');
        const fKelas = $('#kelas_tertinggi');
        const fIjazah = $('#ijazah_tertinggi');

        if (ps === "") {
            fJenjang.prop('disabled', true).val("");
            fKelas.prop('disabled', true).val("");
            fIjazah.prop('disabled', true).val("");
            return;
        }

        if (ps === "Belum Pernah Sekolah") {
            fJenjang.val("Belum Ditentukan").prop('disabled', true);
            fKelas.val("").prop('disabled', true);
            fIjazah.val("Tidak Punya Ijazah SD").prop('disabled', true);
            return;
        }

        // Selain itu aktifkan
        fJenjang.prop('disabled', false);
        fKelas.prop('disabled', false);
        fIjazah.prop('disabled', false);
    }

    $('#partisipasi_sekolah').on('change', handlePartisipasiSekolah);



    // ===================================================================
    // 5. VALIDASI USIA MINIMAL BERDASARKAN JENJANG
    // ===================================================================

    function validateUsiaJenjang() {
        const usia = getUsia();
        const jenjang = $('#jenjang_pendidikan').val();

        // Reset error state
        $('#jenjang_pendidikan').removeClass('is-invalid');

        if (usia === null || !jenjang) return;

        let minUsia = 0;

        if (["SD", "MI", "Paket A", "SDLB", "SPM/PDF Ula"].includes(jenjang)) minUsia = 6;
        if (["SMP", "MTS", "Paket B", "SMP LB", "SPM/PDF Wustha"].includes(jenjang)) minUsia = 12;
        if (["SMA", "MA", "SMK", "MAK", "Paket C", "SMLB", "SPM/PDF Ulya"].includes(jenjang)) minUsia = 15;
        if (["DI/D2/D3"].includes(jenjang)) minUsia = 18;
        if (["D4/S1", "Profesi"].includes(jenjang)) minUsia = 18;
        if (jenjang === "S2") minUsia = 22;
        if (jenjang === "S3") minUsia = 25;

        if (usia < minUsia) {
            $('#jenjang_pendidikan').addClass('is-invalid');
            $('#fb_jenjang').html(`<i class="fas fa-exclamation-circle"></i> Usia terlalu muda (Minimal ${minUsia} thn).`);
            // Value tidak di-reset agar user tahu apa yang salah
        }
    }

    $('#tanggal_lahir, #jenjang_pendidikan').on('change', validateUsiaJenjang);


    // ===================================================================
    // 6. VALIDASI IJAZAH BERDASARKAN JENJANG
    // ===================================================================
    function validateJenjangIjazah() {
        const ps = $('#partisipasi_sekolah').val();
        const jenjang = $('#jenjang_pendidikan').val();
        const ijazah = $('#ijazah_tertinggi').val();

        // Reset error state
        $('#ijazah_tertinggi').removeClass('is-invalid');

        if (!jenjang || !ijazah) return;

        // ✔ Abaikan validasi jika ijazah = Belum Ditentukan
        if (ijazah === "Belum Ditentukan") return;

        const levelJenjang = jenjangLevel[jenjang] ?? 0;
        const levelIjazah = jenjangLevel[ijazah] ?? 0;

        // Masih sekolah → ijazah harus lebih rendah
        if (ps === "Masih Sekolah") {
            if (levelIjazah >= levelJenjang) {
                $('#ijazah_tertinggi').addClass('is-invalid');
                $('#fb_ijazah').html('<i class="fas fa-exclamation-circle"></i> Ijazah tidak boleh sama/lebih tinggi dari jenjang yang sedang ditempuh.');
            }
        }

        // Tidak sekolah lagi → ijazah ≤ jenjang
        if (ps === "Tidak Bersekolah Lagi" && levelIjazah > levelJenjang) {
            $('#ijazah_tertinggi').addClass('is-invalid');
            $('#fb_ijazah').html('<i class="fas fa-exclamation-circle"></i> Ijazah tidak boleh lebih tinggi dari jenjang pendidikan terakhir.');
        }
    }

    $('#jenjang_pendidikan, #ijazah_tertinggi, #partisipasi_sekolah').on('change', validateJenjangIjazah);


    // ===================================================================
    // 7. VALIDASI KELAS — FIX TAMAT & LULUS (KELAS 8 ALWAYS VALID)
    // ===================================================================
    function validateKelas() {
        const jenjang = $('#jenjang_pendidikan').val();
        const kelas = parseInt($('#kelas_tertinggi').val());

        // Reset error state
        $('#kelas_tertinggi').removeClass('is-invalid');

        if (!kelas || !jenjang) return;

        // FIX: Jika kelas = 8 → anggap "Tamat & Lulus", SELALU VALID
        if (kelas === 8) return;

        let allowed = [];
        const lv = jenjangLevel[jenjang] ?? 0;

        if (lv === 0) allowed = kelasValid.level0;
        else if (lv === 1) allowed = kelasValid.level1;
        else if (lv === 2) allowed = kelasValid.level2;
        else if (lv >= 3) allowed = kelasValid.levelPT;

        if (!allowed.includes(kelas)) {
            $('#kelas_tertinggi').addClass('is-invalid');
            $('#fb_kelas').html(`<i class="fas fa-exclamation-circle"></i> Kelas ${kelas} tidak sesuai untuk jenjang ini.`);
        }
    }

    $('#kelas_tertinggi, #jenjang_pendidikan').on('change', validateKelas);


    /* ======================================================
   ✅ Listener Global untuk Event Usia <5 & bekerja_seminggu = "Tidak" (semua umur): Semua field tenaga kerja lain → kosong + readonly
   ====================================================== */
    // ---------------------------
    // Apply rules & helper funcs
    // ---------------------------

    // Helper: hitung usia dari tanggal 'YYYY-MM-DD'
    function hitungUsiaFromDateString(tgl) {
        if (!tgl) return 0;
        const today = new Date();
        const birth = new Date(tgl);
        let usia = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) usia--;
        return usia < 0 ? 0 : usia;
    }

    // Ambil elemen sekali (jika elemen belum ada saat ready, selector tetap bekerja karena dipanggil saat modal show)
    const $tanggalLahir = $('#tanggal_lahir');
    const $jenisKelamin = $('#jenis_kelamin');

    // Pendidikan fields
    const $partisipasi = $('#partisipasi_sekolah');
    const $jenjang = $('#jenjang_pendidikan');
    const $kelasTertinggi = $('#kelas_tertinggi');
    const $ijazah = $('#ijazah_tertinggi');

    // Tenaga Kerja fields
    const $bekerja = $('#bekerja_seminggu');
    const $lapanganUsaha = $('#lapangan_usaha');
    const $statusPekerjaan = $('#status_pekerjaan');
    const $pendapatan = $('#pendapatan');
    const $skillChecks = $('.skill-check');

    // Usaha
    const $memilikiUsaha = $('#memiliki_usaha');
    const $formUsahaDetail = $('#form_usaha_detail');
    const $usahaDetailInputs = $('#form_usaha_detail')
        .find('input, select');

    // Kesehatan
    const $statusHamil = $('#status_hamil');

    // Fungsi-fungsi kecil
    function lockPendidikan() {
        $partisipasi.val('Belum Pernah Sekolah').prop('disabled', true);
        $jenjang.val('').prop('disabled', true);
        $kelasTertinggi.val('').prop('disabled', true);
        $ijazah.val('').prop('disabled', true);
    }
    function unlockPendidikan() {
        $partisipasi.prop('disabled', false);
        $jenjang.prop('disabled', false);
        $kelasTertinggi.prop('disabled', false);
        $ijazah.prop('disabled', false);
    }

    function clearTenagaKerja() {
        $lapanganUsaha.val('');
        $statusPekerjaan.val('');
        $pendapatan.val('');
        // $skillChecks.prop('checked', false);
    }
    function lockTenagaKerjaForUnder5() {
        $bekerja.val('Tidak').prop('disabled', true);
        clearTenagaKerja();
        $lapanganUsaha.prop('disabled', true);
        $statusPekerjaan.prop('disabled', true);
        $pendapatan.prop('disabled', true);
        // $skillChecks.prop('disabled', true);
    }
    function unlockTenagaKerja() {
        $bekerja.prop('disabled', false);
        $lapanganUsaha.prop('disabled', false);
        $statusPekerjaan.prop('disabled', false);
        $pendapatan.prop('disabled', false);
        // $skillChecks.prop('disabled', false);
    }

    function updateStatusHamilByGender() {
        const gender = $jenisKelamin.val();
        if (gender && gender.toLowerCase().startsWith('l')) { // "Laki-laki"
            $statusHamil.val('Tidak').prop('disabled', true);
        } else {
            $statusHamil.prop('disabled', false);
        }
    }

    function clearUsaha() {
        $memilikiUsaha.val('Tidak');
        $usahaDetailInputs.val('');
    }

    function lockUsaha() {
        clearUsaha();
        $memilikiUsaha.prop('disabled', true);
        $formUsahaDetail.hide();
        $usahaDetailInputs.prop('disabled', true);
    }

    function unlockUsaha() {
        $memilikiUsaha.prop('disabled', false);
        $usahaDetailInputs.prop('disabled', false);

        if ($memilikiUsaha.val() === 'Ya') {
            $formUsahaDetail.show();
        } else {
            $formUsahaDetail.hide();
            $usahaDetailInputs.val('');
        }
    }

    // ------------- MAIN applyRules -------------
    function applyRules() {
        const tgl = $tanggalLahir.val();
        const usia = hitungUsiaFromDateString(tgl);
        const bekerjaVal = $bekerja.val();

        /* ===============================
        PENDIDIKAN
        =============================== */
        if (usia < 5) {
            lockPendidikan();
        } else {
            unlockPendidikan();
        }

        /* ===============================
        TENAGA KERJA & USAHA
        =============================== */
        if (usia < 5) {
            // ⛔ Usia balita
            lockTenagaKerjaForUnder5();
            lockUsaha();

        } else {
            // usia >= 5
            unlockTenagaKerja();

            if (bekerjaVal === 'Tidak') {
                // ⛔ Tidak bekerja
                clearTenagaKerja();
                $lapanganUsaha.prop('disabled', true);
                $statusPekerjaan.prop('disabled', true);
                $pendapatan.prop('disabled', true);
                $skillChecks.prop('checked', false).prop('disabled', true);

                lockUsaha();
            } else {
                // ✅ Bekerja = Ya / Belum Ditentukan
                $skillChecks.prop('disabled', false);
                unlockUsaha();
            }
        }

        /* ===============================
        KESEHATAN
        =============================== */
        updateStatusHamilByGender();

        /* ===============================
        VALIDASI LAIN
        =============================== */
        handlePartisipasiSekolah();
        validateJenjangIjazah();
        validateKelas();
    }


    // expose globally so AJAX success and other handlers can call it
    window.applyRules = applyRules;

    // ------------- Event bindings -------------
    // Trigger saat modal benar-benar tampil (Bootstrap shown event)
    $(document).on('shown.bs.modal', '#modalAnggota', function () {
        // small timeout not strictly necessary with shown.bs.modal, but keep minimal delay to ensure prefill finished
        setTimeout(function () {
            applyRules();
            console.log('🧾 Modal Anggota terbuka, applyRules() dipanggil.');
        }, 15);
    });

    // Jika user mengubah tanggal lahir manual -> update langsung
    $tanggalLahir.on('change input', function () {
        applyRules();
    });

    // Jika user mengubah jenis kelamin -> update langsung (untuk status hamil)
    $jenisKelamin.on('change', function () {
        applyRules();
    });

    // Jika user mengubah pilihan bekerja_seminggu -> jika 'Tidak' kunci sisanya
    $bekerja.on('change', function () {
        if ($(this).val() === 'Tidak') {
            clearTenagaKerja();
            $lapanganUsaha.prop('disabled', true);
            $statusPekerjaan.prop('disabled', true);
            $pendapatan.prop('disabled', true);
            $skillChecks.prop('disabled', true);
        } else {
            // hanya buka kalau usia >=5
            const usiaNow = hitungUsiaFromDateString($tanggalLahir.val());
            if (usiaNow >= 5) {
                $lapanganUsaha.prop('disabled', false);
                $statusPekerjaan.prop('disabled', false);
                $pendapatan.prop('disabled', false);
                $skillChecks.prop('disabled', false);
            }
        }
    }); // 👈 PASTIKAN PENUTUP INI ADA DI SINI SEBELUM KODE DI BAWAHNYA

    // ===================================================================
    // 🚀 LOGIKA DINAMIS: SEMBUNYIKAN STATUS JIKA "TIDAK BEKERJA"
    // ===================================================================
    function toggleStatusPekerjaan() {
        const profesi = $('#lapangan_usaha').val();
        const $divStatus = $('#div_status_pekerjaan');
        const $inputStatus = $('#status_pekerjaan');

        if (profesi === 'Tidak Bekerja') {
            $divStatus.slideUp(200); // Sembunyikan dengan animasi halus
            $inputStatus.val('').removeClass('is-invalid'); // Kosongkan & hapus error (jika ada)
        } else {
            $divStatus.slideDown(200); // Tampilkan kembali
        }
    }

    // Panggil saat dropdown profesi berubah (mendukung Select2)
    $('#lapangan_usaha').on('change', toggleStatusPekerjaan);

    // Sisipkan juga ke dalam fungsi applyRules() bawaan Jenderal agar tereksekusi saat modal pertama dibuka
    const originalApplyRules = window.applyRules;
    window.applyRules = function() {
        originalApplyRules();
        toggleStatusPekerjaan(); // Panggil paksa setiap kali modal/aturan dirender ulang
    };

    $(document).on('change', '#memiliki_usaha', function () {
        if ($(this).val() === 'Ya') {
            $('#form_usaha_detail').slideDown(150);
        } else {
            $('#form_usaha_detail').slideUp(150);
            $('#form_usaha_detail').find('input, select').val('');
        }
    });

    // OPTIONAL: panggil applyRules() saat halaman ready jika modal sudah berisi nilai (edit inline)
    // (tidak memaksa modal terbuka)
    // applyRules(); // Uncomment kalau perlu pada page load

    /* ======================================================
     🏡 Prefill Wilayah Select2 (AJAX)
    ======================================================= */

    function prefillWilayah(wilayah, wilayahNama) {
        if (!wilayah || !wilayahNama) {
            console.warn("⚠️ Data wilayah tidak lengkap di payload.");
            return;
        }

        // console.log("✅ Prefill Wilayah:", wilayah, wilayahNama);

        const setSelect2Value = (selector, id, text) => {
            if (!id || !text) return;
            const $select = $(selector);
            if ($select.length) {
                const option = new Option(text, id, true, true);
                $select.append(option).trigger('change');
            }
        };

        // delay agar select2 siap
        setTimeout(() => {
            setSelect2Value("#rumah_provinsi, #provinsi", wilayah.provinsi, wilayahNama.provinsi);
            setSelect2Value("#rumah_regency, #kabupaten", wilayah.kabupaten, wilayahNama.kabupaten);
            setSelect2Value("#rumah_district, #kecamatan", wilayah.kecamatan, wilayahNama.kecamatan);
            setSelect2Value("#rumah_village, #desa", wilayah.desa, wilayahNama.desa);
        }, 400);
    }

    /* ======================================================
     🏠 Prefill Data Rumah (Kondisi + Sanitasi)
    ======================================================= */

    function prefillRumah(perumahan) {
        if (!perumahan || typeof perumahan !== 'object') {
            console.warn("⚠️ Data perumahan tidak ditemukan atau tidak valid.");
            return;
        }

        // console.log("✅ Prefill Rumah:", perumahan);

        // Kondisi rumah
        if (perumahan.kondisi) {
            $('#jenis_atap').val(perumahan.kondisi.jenis_atap || '');
            $('#jenis_lantai').val(perumahan.kondisi.jenis_lantai || '');
            $('#sumber_air').val(perumahan.kondisi.sumber_air || '');
            $('#bahan_bakar').val(perumahan.kondisi.bahan_bakar || '');
            $('#daya_listrik').val(perumahan.kondisi.daya_listrik || '');
            $('#sumber_listrik').val(perumahan.kondisi.sumber_listrik || '');
            $('#luas_lantai').val(perumahan.kondisi.luas_lantai || '');
            $('#nomor_meter').val(perumahan.kondisi.nomor_meter || '');
            $('#nomor_pelanggan').val(perumahan.kondisi.nomor_pelanggan || '');
        }

        // Status kepemilikan (ambil dari perumahan utama)
        $('#status_kepemilikan')
            .val(perumahan.status_kepemilikan)
            .trigger('change');

        // Sanitasi
        if (perumahan.sanitasi) {
            $('#jenis_kloset').val(perumahan.sanitasi.jenis_kloset || '');
            $('#fasilitas_bab').val(perumahan.sanitasi.fasilitas_bab || '');
            $('#pembuangan_tinja').val(perumahan.sanitasi.pembuangan_tinja || '');
            $('#jarak_air_ke_limbah').val(perumahan.sanitasi.jarak_air_ke_limbah || '');
        }

        // Wilayah (kode + nama)
        if (perumahan.wilayah && perumahan.wilayah_nama) {
            prefillWilayah(perumahan.wilayah, perumahan.wilayah_nama);
        } else {
            console.warn("⚠️ Data wilayah tidak ditemukan di payload.");
        }
    }

    /* ======================================================
     🌍 Inisialisasi Select2 Wilayah AJAX Berantai
    ======================================================= */
    function initSelect2Wilayah() {
        const select2Base = {
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih...',
            allowClear: true,
            ajax: {
                delay: 250,
                dataType: 'json',
                processResults: data => ({ results: data.map(item => ({ id: item.id, text: item.name })) })
            }
        };

        // Helper: buat select2 dengan opsi, optional dropdownParent
        function attachSelect2(selector, ajaxUrl, dropdownParentEl = null, transportFn = null) {
            const cfg = $.extend(true, {}, select2Base);
            if (ajaxUrl) cfg.ajax = $.extend({}, cfg.ajax, { url: ajaxUrl });
            if (transportFn) cfg.ajax.transport = transportFn;
            if (dropdownParentEl && dropdownParentEl.length) cfg.dropdownParent = dropdownParentEl;
            $(selector).select2(cfg);
        }

        // elemen modal (jika ada)
        const $modal = $('#modalAnggota');

        // Provinsi
        attachSelect2('#rumah_provinsi, #provinsi', baseUrl + '/api/villages/provinces', null);

        // Kabupaten (bergantung prov)
        attachSelect2('#rumah_regency, #kabupaten', null, null, function(params, success, failure) {
            const provID = $('#rumah_provinsi').val() || $('#provinsi').val();
            if (!provID) return success([]);
            $.ajax({ url: baseUrl + '/api/villages/regencies/' + provID, dataType: 'json', success, error: failure });
        });

        // Kecamatan
        attachSelect2('#rumah_district, #kecamatan', null, null, function(params, success, failure) {
            const kabID = $('#rumah_regency').val() || $('#kabupaten').val();
            if (!kabID) return success([]);
            $.ajax({ url: baseUrl + '/api/villages/districts/' + kabID, dataType: 'json', success, error: failure });
        });

        // Desa
        attachSelect2('#rumah_village, #desa', null, null, function(params, success, failure) {
            const kecID = $('#rumah_district').val() || $('#kecamatan').val();
            if (!kecID) return success([]);
            $.ajax({ url: baseUrl + '/api/villages/villages/' + kecID, dataType: 'json', success, error: failure });
        });

        // ---- IMPORTANT: re-initialize selects that are *inside modal* with dropdownParent to ensure dropdown appears above modal ----
        if ($modal.length) {
            ['#provinsi', '#kabupaten', '#kecamatan', '#desa'].forEach(sel => {
                // destroy existing select2 (if initialized), then re-init with dropdownParent
                if ($(sel).data('select2')) $(sel).select2('destroy');
            });

            // attach with dropdownParent = modal
            attachSelect2('#provinsi', baseUrl + '/api/villages/provinces', $modal);
            attachSelect2('#kabupaten', null, $modal, function(params, success, failure) {
                const provID = $('#provinsi').val();
                if (!provID) return success([]);
                $.ajax({ url: baseUrl + '/api/villages/regencies/' + provID, dataType: 'json', success, error: failure });
            });
            attachSelect2('#kecamatan', null, $modal, function(params, success, failure) {
                const kabID = $('#kabupaten').val();
                if (!kabID) return success([]);
                $.ajax({ url: baseUrl + '/api/villages/districts/' + kabID, dataType: 'json', success, error: failure });
            });
            attachSelect2('#desa', null, $modal, function(params, success, failure) {
                const kecID = $('#kecamatan').val();
                if (!kecID) return success([]);
                $.ajax({ url: baseUrl + '/api/villages/villages/' + kecID, dataType: 'json', success, error: failure });
            });
        }

        // Reset dependensi saat ganti
        $('#rumah_provinsi, #provinsi').on('change', function () {
            $('#rumah_regency, #kabupaten, #rumah_district, #kecamatan, #rumah_village, #desa').val(null).trigger('change');
        });
        $('#rumah_regency, #kabupaten').on('change', function () {
            $('#rumah_district, #kecamatan, #rumah_village, #desa').val(null).trigger('change');
        });
        $('#rumah_district, #kecamatan').on('change', function () {
            $('#rumah_village, #desa').val(null).trigger('change');
        });
    }


    /* ======================================================
    📤 Submit Data Form
    ======================================================= */

    // 🚀 VALIDASI REAL-TIME TAB KELUARGA
    $('#formDataKeluarga').on('input change', '.required, [required]', function() {
        if (!$(this).val() || String($(this).val()).trim() === '') {
            $(this).addClass('is-invalid');
            // Khusus Select2 Bootstrap 5: Beri border merah pada wadahnya
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).next('.select2-container').find('.select2-selection').addClass('border-danger');
            }
        } else {
            $(this).removeClass('is-invalid');
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).next('.select2-container').find('.select2-selection').removeClass('border-danger');
            }
        }
    });

    // ============================= 
    // 🛡️ SUBMIT FORM KELUARGA
    // =============================
    $('#formDataKeluarga').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        let isValid = true;

        // 1) Sikat bersih huruf/simbol pada field Nomor (Hanya sisakan angka)
        ['#keluarga_no_kk', '#nik_kepala_keluarga', '#kode_pos'].forEach(selector => {
            const el = form.find(selector);
            if (el.length) {
                el.val(el.val().replace(/\D/g, ''));
            }
        });

        // 2) Periksa SEMUA field wajib (.required atau ber-atribut required)
        form.find('.required, [required]').each(function() {
            if (!$(this).val() || String($(this).val()).trim() === '') {
                $(this).addClass('is-invalid');
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).next('.select2-container').find('.select2-selection').addClass('border-danger');
                }
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('border-danger');
                }
            }
        });

        // 3) Validasi khusus Nomor KK
        const noKK = $('#keluarga_no_kk').val();
        if (noKK && noKK.length !== 16) {
            $('#keluarga_no_kk').addClass('is-invalid');
            Swal.fire({ icon: 'error', title: 'Nomor KK Tidak Valid', text: 'Nomor KK harus berisi 16 digit angka.' });
            return;
        }

        if (noKK && noKK.slice(-3) === "000") {
            $('#keluarga_no_kk').addClass('is-invalid');
            Swal.fire({ icon: 'error', title: 'Nomor KK Tidak Valid', text: 'Tiga digit terakhir Nomor KK tidak boleh 000.' });
            return;
        }

        // 4) 🚀 GATEKEEPER: Hentikan eksekusi jika ada form yang kosong / merah
        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Isian Belum Lengkap',
                text: 'Terdapat isian wajib (seperti Wilayah Capil atau Identitas) yang masih kosong. Silakan lengkapi kotak yang bergaris merah.',
                confirmButtonText: 'Periksa Kembali'
            });
            return;
        }

        // =============================
        // 🟢 5) KONFIRMASI SEBELUM SIMPAN
        // =============================
        const sumber = $('#sumber').val();

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            html: 'Dengan menekan tombol <b>Ya</b>, data yang telah terinput sebelumnya akan ditimpa/hilang.<br><br><small class="text-danger"><i>*Perhatian khusus bagi data yang telah berstatus Verified.</i></small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fas fa-save"></i> Ya, Simpan Data!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                
                Swal.fire({
                    title: 'Menyimpan data...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: baseUrl + '/pembaruan-keluarga/save-keluarga',
                    method: 'POST',
                    data: $('#formDataKeluarga').serialize(),
                    dataType: 'json',
                    success: res => {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            if (res.id_kk) $('#id_kk').val(res.id_kk);

                            if (sumber === 'baru' && res.id_kk) {
                                setTimeout(() => {
                                    window.location.href = `${baseUrl}/pembaruan-keluarga/detail/${res.id_kk}`;
                                }, 1200);
                                return;
                            }

                            $('#sumber').val('utama');
                            setTimeout(() => location.reload(), 1000);

                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Tidak dapat menyimpan data.' });
                        }
                    },
                    error: () => {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat menyimpan data.' });
                    }
                });
            }
        });
    });

    // 🔹 Simpan Data Rumah
    $('#formRumah').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: baseUrl + '/pembaruan-keluarga/save-rumah',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: res => Swal.fire({
                icon: res.status === 'success' ? 'success' : 'error',
                title: res.message,
                timer: 1800,
                showConfirmButton: false
            }),
            error: () => showError('Error', 'Gagal mengirim data ke server.')
        });
    });

    // 🔹 Simpan Data Foto & Geo
    $('#formFotoGeotag').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        Swal.fire({
            title: 'Simpan Foto & Lokasi?',
            text: 'Data foto dan koordinat akan disimpan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: baseUrl + '/pembaruan-keluarga/save-foto',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: res => {
                        if (res.status === 'success') showSuccess('Berhasil!', res.message);
                        else showError('Gagal', res.message);
                    },
                    error: () => showError('Error', 'Gagal menyimpan data foto.')
                });
            }
        });
    });

    // Validasi form tambah/edit anggota (pastikan semua field required di form memiliki class .required-field)
    document.addEventListener("DOMContentLoaded", function () {

        const formAset = document.querySelector("#formAset"); // pastikan ID sesuai

        if (!formAset) return;

        formAset.addEventListener("submit", function (e) {

            const requiredFields = formAset.querySelectorAll(".required-field");

            let valid = true;

            requiredFields.forEach(field => {
                if (!field.value) {
                    field.classList.add("is-invalid");
                    valid = false;
                } else {
                    field.classList.remove("is-invalid");
                }
            });

            if (!valid) {
                e.preventDefault();
                e.stopPropagation();
            }

        });

    });
    
    // Simpan Data Historis Desil
    $('#btnSaveHistorical').on('click', function() {

        const form = $('#formHistoricalDesil');

        $.ajax({
            url: '/pembaruan-keluarga/add-historical-desil',
            type: 'POST',
            data: form.serialize(),
            success: function(res) {

                if (res.status === 'success') {

                    Swal.fire('Berhasil', res.message, 'success');

                    $('#modalHistoricalDesil').modal('hide');

                    // reload grafik
                    // loadDesilHistory($('#id_kk').val());
                    if (res.status === 'success') {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });

                        // setTimeout(() => {
                        //     window.location.reload(true);
                        // }, 1200);
                        setTimeout(() => {
                            window.location.href = window.location.pathname + '?v=' + new Date().getTime();
                        }, 1200);

                    }

                } else {

                    Swal.fire('Gagal', res.message, 'error');

                }
            }
        });

    });

    /* ======================================================
     🚀 Inisialisasi Saat Halaman Siap
    ======================================================= */
    initSelect2Wilayah();

    // Prefill payload dari PHP
    if (typeof payload !== 'undefined' && payload.perumahan) {
        prefillRumah(payload.perumahan);
    }

    // Preview Foto
    $('#foto_ktp').on('change', function () { readPreview(this, '#previewKtp'); });
    $('#foto_depan').on('change', function () { readPreview(this, '#previewDepan'); });
    $('#foto_dalam').on('change', function () { readPreview(this, '#previewDalam'); });


    $('#btnApply').on('click', function() {
        const usulanId = $(this).data('usulan-id');
    
        if (!usulanId) {
            Swal.fire('Gagal', 'ID usulan tidak ditemukan.', 'error');
            return;
        }
    
        Swal.fire({
            title: 'Terapkan Data?',
            text: 'Data ini akan dipindahkan ke database utama dan tidak bisa diedit kembali.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Terapkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: window.baseUrl + '/pembaruan-keluarga/apply',
                    type: 'POST',
                    data: { usulan_id: usulanId },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => {
                                if (res.redirect) {
                                    window.location.href = res.redirect;
                                } else {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire('Gagal', res.message || 'Terjadi kesalahan tak terduga.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan koneksi ke server.', 'error');
                    }
                });
            }
        });
    });

    if (isTambahMode === 'true' && userRoleId > 3) {
        $('#btnApply').hide(); // sembunyikan untuk petugas lapangan
    }

     if (window.location.hash === "#tab-anggota") {
        // Tandai baris terakhir (baris baru disimpan)
        const lastRow = $('#tableAnggota tbody tr:first');
        if (lastRow.length) {
            lastRow.css('background-color', '#fff3cd');
            setTimeout(() => lastRow.css('background-color', ''), 2000);
        }
    }

    // Fungsi Format Rupiah Khusus Kalkulator
    function formatRupiahCalc(angka) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }
    
    // 1. EVENT: Buka Modal Kalkulator
    $('.btn-calc').click(function() {
        let target = $(this).data('target');
        let title = $(this).data('title');
    
        $('#kalkulator_target_input').val(target);
        $('#modalKalkulatorTitle').html('<i class="fas fa-calculator mr-1"></i> ' + title);
    
        // Bersihkan modal
        $('#kalkulator_inputs').html('');
        $('#kalkulator_total').text('Rp 0').data('raw', 0);
    
        // Siapkan baris input default sesuai target
        if (target === '#pengeluaran_non_makan_bulanan') {
            addCalcRow('Listrik');
            addCalcRow('Pulsa / Kuota');
            addCalcRow('Air / PDAM');
        } else {
            addCalcRow('Pakaian / Sandang');
            addCalcRow('Pendidikan');
            addCalcRow('Kesehatan / Obat');
        }
    
        $('#modalKalkulator').modal('show');
    });
    
    // 2. EVENT: Tambah Baris Baru
    $('#btn_add_calc_row').click(function() {
        addCalcRow('Item Lainnya...');
    });
    
    // Fungsi Render HTML Baris Input
    function addCalcRow(placeholder) {
        let html = `
            <div class="input-group input-group-sm mb-2 calc-row shadow-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white" style="width: 120px; font-size:0.75rem;">${placeholder}</span>
                </div>
                <input type="text" class="form-control calc-input rupiah-calc" placeholder="0">
                <div class="input-group-append">
                    <button class="btn btn-danger btn-remove-calc-row" type="button" title="Hapus"><i class="fas fa-times"></i></button>
                </div>
            </div>
        `;
        $('#kalkulator_inputs').append(html);
    }
    
    // 3. EVENT: Hapus Baris
    $(document).on('click', '.btn-remove-calc-row', function() {
        $(this).closest('.calc-row').remove();
        hitungTotalKalkulator();
    });
    
    // 4. EVENT: Ketik Nominal & Hitung Otomatis
    $(document).on('keyup', '.rupiah-calc', function() {
        $(this).val(formatRupiahCalc($(this).val()));
        hitungTotalKalkulator();
    });
    
    // Fungsi Menghitung Total Akumulasi
    function hitungTotalKalkulator() {
        let total = 0;
        $('.calc-input').each(function() {
            let val = $(this).val().replace(/\./g, '');
            if (val) total += parseInt(val);
        });
        $('#kalkulator_total').text('Rp ' + formatRupiahCalc(total.toString()));
        $('#kalkulator_total').data('raw', total);
    }
    
    // 5. EVENT: Tombol Terapkan (Kirim hasil ke Input Utama)
    $('#btn_apply_kalkulator').click(function() {
        let target = $('#kalkulator_target_input').val();
        let totalRaw = $('#kalkulator_total').data('raw') || 0;
    
        if (totalRaw > 0) {
            $(target).val(formatRupiahCalc(totalRaw.toString()));
            // Trigger event change jika ada validasi lain yang memantau form utama
            $(target).trigger('change'); 
        }
        
        $('#modalKalkulator').modal('hide');
    });

    
});

/* ======================================================
   ✅ Listener Global untuk Event Sukses Simpan Anggota
   ====================================================== */
$(document).on('anggota:saved', function() {
    console.log('♻️ Event anggota:saved diterima, reload ke tab #tab-anggota...');

    // Tutup modal dulu jika masih terbuka
    const modalEl = $('#modalAnggota');
    if (modalEl.is(':visible')) {
        modalEl.modal('hide');
    }

    // Pastikan Bootstrap benar-benar menutup modal dulu
    setTimeout(() => {
        try {
            const base = window.location.origin + window.location.pathname;
            console.log('🔁 Reload ke:', base + '#tab-anggota');
            window.location.assign(base + '#tab-anggota');
        } catch (e) {
            console.error('⚠️ Gagal redirect:', e);
        }
    }, 800); // beri jeda 0,8 detik supaya modal benar-benar tertutup
});


$(document).on('submit', '#formInputDesil', function(e) {

    e.preventDefault(); // ⛔ cegah reload default

    const form = $(this);
    const actionUrl = form.attr('action');
    const formData = form.serialize();

    $.ajax({
        url: actionUrl,
        type: 'POST',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {

            if (response.status === 'success') {

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Kategori desil berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // location.reload(); // 🔄 reload setelah sukses
                    location.href = location.href; // alternatif reload dengan cache-busting
                });

            } else if (response.status === 'forbidden') {

                Swal.fire({
                    icon: 'warning',
                    title: 'Akses Ditolak',
                    text: response.message
                });

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message || 'Terjadi kesalahan.'
                });

            }
        },
        error: function() {

            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Tidak dapat menghubungi server.'
            });
        }
    });
});

// ========================================================
// 🚀 FITUR KALKULATOR SWEETALERT2 (MOBILE COMPACT)
// ========================================================
window.bukaKalkulator = function(tipe, targetId) {
    let title = '';
    let htmlContent = '';

    if (tipe === 'bulanan') {
        title = 'Rincian Bulanan';
        htmlContent = `
            <div class="text-left" style="font-size: 0.85rem;">
                <label class="mb-1 font-weight-bold">Listrik / Token</label>
                <input type="text" class="form-control form-control-sm mb-2 calc-input" placeholder="Rp 0">
                <label class="mb-1 font-weight-bold">Pulsa / Paket Data</label>
                <input type="text" class="form-control form-control-sm mb-2 calc-input" placeholder="Rp 0">
                <label class="mb-1 font-weight-bold">Air / PAM / Gas</label>
                <input type="text" class="form-control form-control-sm mb-2 calc-input" placeholder="Rp 0">
                <label class="mb-1 font-weight-bold">Lain-lain</label>
                <input type="text" class="form-control form-control-sm mb-3 calc-input" placeholder="Rp 0">
                <hr class="my-2">
                <label class="mb-1 font-weight-bold text-primary">Total Akumulasi</label>
                <input type="text" id="calc_total" class="form-control form-control-sm font-weight-bold text-success bg-light" readonly value="0">
            </div>
        `;
    } else if (tipe === 'tahunan') {
        title = 'Rincian Tahunan';
        htmlContent = `
            <div class="text-left" style="font-size: 0.85rem;">
                <label class="mb-1 font-weight-bold">Pendidikan (SPP, dll)</label>
                <input type="text" class="form-control form-control-sm mb-2 calc-input" placeholder="Rp 0">
                <label class="mb-1 font-weight-bold">Kesehatan (Berobat)</label>
                <input type="text" class="form-control form-control-sm mb-2 calc-input" placeholder="Rp 0">
                <label class="mb-1 font-weight-bold">Pakaian / Sepatu</label>
                <input type="text" class="form-control form-control-sm mb-2 calc-input" placeholder="Rp 0">
                <label class="mb-1 font-weight-bold">Pajak (PBB, Kendaraan)</label>
                <input type="text" class="form-control form-control-sm mb-2 calc-input" placeholder="Rp 0">
                <label class="mb-1 font-weight-bold">Lain-lain</label>
                <input type="text" class="form-control form-control-sm mb-3 calc-input" placeholder="Rp 0">
                <hr class="my-2">
                <label class="mb-1 font-weight-bold text-primary">Total Akumulasi</label>
                <input type="text" id="calc_total" class="form-control form-control-sm font-weight-bold text-success bg-light" readonly value="0">
            </div>
        `;
    } else if (tipe === 'mingguan_makan') {
        title = 'Kalkulator Makan';
        htmlContent = `
            <div class="text-left" style="font-size: 0.85rem;">
                <label class="mb-1 font-weight-bold">Biaya Makan per Orang / Hari</label>
                <input type="text" id="calc_makan_harian" class="form-control form-control-sm mb-2" placeholder="Rp 0">
                
                <label class="mb-1 font-weight-bold">Jumlah Anggota Keluarga (Jiwa)</label>
                <input type="number" id="calc_jumlah_anggota" class="form-control form-control-sm mb-3" min="1" placeholder="Misal: 4">
                
                <hr class="my-2">
                <label class="mb-1 font-weight-bold text-primary">Rumus: Harian × Jiwa × 7 Hari</label>
                <input type="text" id="calc_total" class="form-control form-control-sm font-weight-bold text-success bg-light" readonly value="0">
            </div>
        `;
    }

    Swal.fire({
        title: `<div style="font-size: 1.1rem; border-bottom: 2px solid #17a2b8; padding-bottom: 5px;">${title}</div>`,
        html: htmlContent,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Terapkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745',
        width: '320px',
        padding: '0.8em',
        customClass: {
            title: 'mb-0',
            actions: 'mt-2'
        },
        didOpen: () => {
            const totalInput = Swal.getHtmlContainer().querySelector('#calc_total');

            const formatRp = (angka) => {
                let val = angka.replace(/[^,\d]/g, '').toString();
                let split = val.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
                return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            };

            if (tipe === 'mingguan_makan') {
                const inputHarian = Swal.getHtmlContainer().querySelector('#calc_makan_harian');
                const inputAnggota = Swal.getHtmlContainer().querySelector('#calc_jumlah_anggota');

                const hitungMakan = () => {
                    let harian = parseInt(inputHarian.value.replace(/\./g, '')) || 0;
                    let anggota = parseInt(inputAnggota.value) || 0;
                    let total = harian * anggota * 7;
                    totalInput.value = formatRp(total.toString());
                };

                inputHarian.addEventListener('input', function() {
                    this.value = formatRp(this.value);
                    hitungMakan();
                });
                inputAnggota.addEventListener('input', hitungMakan);

            } else {
                const inputs = Swal.getHtmlContainer().querySelectorAll('.calc-input');
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        this.value = formatRp(this.value);
                        
                        let total = 0;
                        inputs.forEach(inp => {
                            let angka = inp.value.replace(/\./g, '');
                            if (angka) total += parseInt(angka);
                        });
                        totalInput.value = formatRp(total.toString());
                    });
                });
            }
        },
        preConfirm: () => {
            return Swal.getHtmlContainer().querySelector('#calc_total').value;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value !== "0" && result.value !== "") {
            let targetInput = $(targetId);
            targetInput.val(result.value);
            targetInput.trigger('input').trigger('change');
            
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                title: 'Total disalin!',
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
};