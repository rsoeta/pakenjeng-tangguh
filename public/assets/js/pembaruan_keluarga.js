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
        $bekerja.val('Belum Ditentukan').prop('disabled', true);
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

    // ------------- MAIN applyRules -------------
    function applyRules() {
        // ambil tanggal lahir terkini dari DOM
        const tgl = $tanggalLahir.val();
        const usia = hitungUsiaFromDateString(tgl);

        // Pendidikan
        if (usia < 5) {
            lockPendidikan();
        } else {
            unlockPendidikan();
        }

        // Tenaga Kerja
        if (usia < 5) {
            lockTenagaKerjaForUnder5();
        } else {
            // jika sebelumnya terkunci oleh usia <5, kita buka;
            // tetapi jika user sudah memilih 'Tidak' pada bekerja_seminggu, tetap kunci sisanya
            unlockTenagaKerja();
            if ($bekerja.val() === 'Tidak') {
                clearTenagaKerja();
                $lapanganUsaha.prop('disabled', true);
                $statusPekerjaan.prop('disabled', true);
                $pendapatan.prop('disabled', true);
                $skillChecks.prop('disabled', true);
            }
        }

        // Kesehatan (status hamil)
        updateStatusHamilByGender();
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

    // // =============================
    // // 🛡️ VALIDASI WAJIB FORM KELUARGA
    // // =============================
    // $('#formDataKeluarga').on('submit', function (e) {
    //     e.preventDefault();

    //     const noKK           = $('#keluarga_no_kk').val().trim();
    //     const kepala         = $('#kepala_keluarga').val().trim();
    //     const alamat         = $('#alamat').val().trim();
    //     const rw             = $('#rw').val().trim();
    //     const rt             = $('#rt').val().trim();
    //     const kategoriAdat   = $('#kategori_adat').val().trim();
    //     const namaSuku       = $('#nama_suku').val().trim();

    //     // ========== VALIDASI DASAR ==========
    //     if (!noKK || !kepala || !alamat || !rw || !rt || !kategoriAdat) {
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Gagal',
    //             text: 'Semua field wajib diisi sebelum menyimpan.'
    //         });
    //         return;
    //     }

    //     // ========== VALIDASI SUKU TAMBAHAN ==========
    //     if (kategoriAdat === 'Ya' && !namaSuku) {
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Gagal',
    //             text: 'Nama Suku wajib diisi karena Keluarga Adat = Ya.'
    //         });
    //         return;
    //     }

    //     // =============================
    //     // 🟢 Jika valid → lanjutkan konfirmasi simpan
    //     // =============================
    //     Swal.fire({
    //         title: 'Simpan Perubahan?',
    //         text: 'Data keluarga akan disimpan sebagai draft pembaruan.',
    //         icon: 'question',
    //         showCancelButton: true,
    //         confirmButtonText: 'Ya, Simpan',
    //         cancelButtonText: 'Batal'
    //     }).then(result => {
    //         if (!result.isConfirmed) return;

    //         const sumber = $('#sumber').val();

    //         $.ajax({
    //             url: baseUrl + '/pembaruan-keluarga/save-keluarga',
    //             method: 'POST',
    //             data: $('#formDataKeluarga').serialize(),
    //             dataType: 'json',
    //             success: res => {
    //                 if (res.status === 'success') {
    //                     Swal.fire({
    //                         icon: 'success',
    //                         title: 'Berhasil!',
    //                         text: res.message,
    //                         timer: 1500,
    //                         showConfirmButton: false
    //                     });

    //                     if (res.id_kk) {
    //                         $('#id_kk').val(res.id_kk);
    //                     }

    //                     if (sumber === 'baru' && res.id_kk) {
    //                         setTimeout(() => {
    //                             window.location.href = `${baseUrl}/pembaruan-keluarga/detail/${res.id_kk}`;
    //                         }, 1200);
    //                         return;
    //                     }

    //                     $('#sumber').val('utama');
    //                     setTimeout(() => location.reload(), 1000);

    //                 } else {
    //                     Swal.fire({
    //                         icon: 'error',
    //                         title: 'Gagal',
    //                         text: res.message || 'Tidak dapat menyimpan data.'
    //                     });
    //                 }
    //             },
    //             error: xhr => {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Error',
    //                     text: 'Terjadi kesalahan saat menyimpan data.'
    //                 });
    //             }
    //         });

    //     });

    // });

    // =============================
    // 🛡️ VALIDASI WAJIB FORM KELUARGA
    // =============================
    $('#formDataKeluarga').on('submit', function (e) {
        e.preventDefault();

        let noKK           = $('#keluarga_no_kk').val().trim();
        const kepala       = $('#kepala_keluarga').val().trim();
        const alamat       = $('#alamat').val().trim();
        const rw           = $('#rw').val().trim();
        const rt           = $('#rt').val().trim();
        const kategoriAdat = $('#kategori_adat').val().trim();
        const namaSuku     = $('#nama_suku').val().trim();

        // ===========================================
        // 🔒 1) Bersihkan input: hanya angka
        // ===========================================
        noKK = noKK.replace(/\D/g, '');
        $('#keluarga_no_kk').val(noKK);

        // ===========================================
        // 🔒 2) VALIDASI KHUSUS No KK (16 digit + tidak boleh 00)
        // ===========================================
        if (noKK.length !== 16) {
            Swal.fire({
                icon: 'error',
                title: 'Nomor KK Tidak Valid',
                text: 'Nomor KK harus berisi 16 digit angka.'
            });
            return;
        }

        if (noKK.slice(-2) === "00") {
            Swal.fire({
                icon: 'error',
                title: 'Nomor KK Tidak Valid',
                text: 'Dua digit terakhir Nomor KK tidak boleh 00.'
            });
            return;
        }

        // ===========================================
        // 🔒 3) VALIDASI DASAR FIELD WAJIB
        // ===========================================
        if (!noKK || !kepala || !alamat || !rw || !rt || !kategoriAdat) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Semua field wajib diisi sebelum menyimpan.'
            });
            return;
        }

        // ===========================================
        // 🔒 4) VALIDASI SUKU TAMBAHAN
        // ===========================================
        if (kategoriAdat === 'Ya' && !namaSuku) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Nama Suku wajib diisi karena Keluarga Adat = Ya.'
            });
            return;
        }

        // =============================
        // 🟢 5) KONFIRMASI SIMPAN
        // =============================
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Data keluarga akan disimpan sebagai draft pembaruan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (!result.isConfirmed) return;

            const sumber = $('#sumber').val();

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

                        if (res.id_kk) {
                            $('#id_kk').val(res.id_kk);
                        }

                        if (sumber === 'baru' && res.id_kk) {
                            setTimeout(() => {
                                window.location.href = `${baseUrl}/pembaruan-keluarga/detail/${res.id_kk}`;
                            }, 1200);
                            return;
                        }

                        $('#sumber').val('utama');
                        setTimeout(() => location.reload(), 1000);

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Tidak dapat menyimpan data.'
                        });
                    }
                },
                error: xhr => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan data.'
                    });
                }
            });

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

