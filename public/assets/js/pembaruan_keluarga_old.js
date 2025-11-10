// ============================================================
// 🌍 Prefill Wilayah Select2 (Prov, Kab, Kec, Desa)
// ============================================================
function prefillWilayah(perumahan) {
    if (!perumahan || !perumahan.wilayah || !perumahan.wilayah_nama) {
        console.warn("⚠️ Data wilayah tidak ditemukan di payload.");
        return;
    }

    const wilayah = perumahan.wilayah;
    const wilayahNama = perumahan.wilayah_nama;

    console.log("✅ Prefill Wilayah:", wilayah, wilayahNama);

    const setSelect2Value = (selector, id, text) => {
        if (!id || !text) return;
        const $select = $(selector);
        if ($select.length) {
            const option = new Option(text, id, true, true);
            $select.append(option).trigger("change");
        }
    };

    setSelect2Value("#rumah_provinsi, #provinsi", wilayah.provinsi, wilayahNama.provinsi);
    setSelect2Value("#rumah_regency, #kabupaten", wilayah.kabupaten, wilayahNama.kabupaten);
    setSelect2Value("#rumah_district, #kecamatan", wilayah.kecamatan, wilayahNama.kecamatan);
    setSelect2Value("#rumah_village, #desa", wilayah.desa, wilayahNama.desa);
}

// ============================================================
// ⚙️ Inisialisasi Select2 AJAX Wilayah Berantai
// ============================================================
function initSelect2Wilayah() {
    const select2Config = {
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih...',
        allowClear: true,
        ajax: {
            delay: 250,
            dataType: 'json',
            processResults: data => ({
                results: data.map(item => ({ id: item.id, text: item.name }))
            })
        }
    };

    $('#rumah_provinsi, #provinsi').select2($.extend(true, {}, select2Config, {
        ajax: { ...select2Config.ajax, url: baseUrl + '/api/villages/provinces' }
    }));

    $('#rumah_regency, #kabupaten').select2($.extend(true, {}, select2Config, {
        ajax: {
            ...select2Config.ajax,
            transport: function (_, success, failure) {
                const provID = $('#rumah_provinsi').val() || $('#provinsi').val();
                if (!provID) return success([]);
                $.ajax({
                    url: baseUrl + '/api/villages/regencies/' + provID,
                    dataType: 'json',
                    success, error: failure
                });
            }
        }
    }));

    $('#rumah_district, #kecamatan').select2($.extend(true, {}, select2Config, {
        ajax: {
            ...select2Config.ajax,
            transport: function (_, success, failure) {
                const kabID = $('#rumah_regency').val() || $('#kabupaten').val();
                if (!kabID) return success([]);
                $.ajax({
                    url: baseUrl + '/api/villages/districts/' + kabID,
                    dataType: 'json',
                    success, error: failure
                });
            }
        }
    }));

    $('#rumah_village, #desa').select2($.extend(true, {}, select2Config, {
        ajax: {
            ...select2Config.ajax,
            transport: function (_, success, failure) {
                const kecID = $('#rumah_district').val() || $('#kecamatan').val();
                if (!kecID) return success([]);
                $.ajax({
                    url: baseUrl + '/api/villages/villages/' + kecID,
                    dataType: 'json',
                    success, error: failure
                });
            }
        }
    }));

    // Reset dropdown jika parent berubah
    $('#rumah_provinsi, #provinsi').on('change', function () {
        $('#rumah_regency, #kabupaten').val(null).trigger('change');
        $('#rumah_district, #kecamatan').val(null).trigger('change');
        $('#rumah_village, #desa').val(null).trigger('change');
    });
    $('#rumah_regency, #kabupaten').on('change', function () {
        $('#rumah_district, #kecamatan').val(null).trigger('change');
        $('#rumah_village, #desa').val(null).trigger('change');
    });
    $('#rumah_district, #kecamatan').on('change', function () {
        $('#rumah_village, #desa').val(null).trigger('change');
    });
}

// ============================================================
// 🏠 Prefill Data Rumah (umum)
// ============================================================
function prefillRumah(perumahan) {
    if (!perumahan || typeof perumahan !== 'object') {
        console.warn('⚠️ Data perumahan tidak valid.');
        return;
    }

    console.log("🧭 payload.perumahan.wilayah:", payload?.perumahan?.wilayah);
    console.log("🧭 payload.perumahan.wilayah_nama:", payload?.perumahan?.wilayah_nama);


    if (perumahan.kondisi) {
        $('#jenis_atap').val(perumahan.kondisi.jenis_atap || '');
        $('#jenis_lantai').val(perumahan.kondisi.jenis_lantai || '');
        $('#sumber_air').val(perumahan.kondisi.sumber_air || '');
        $('#status_kepemilikan').val(perumahan.kondisi.status_kepemilikan || '');
        $('#bahan_bakar').val(perumahan.kondisi.bahan_bakar || '');
        $('#daya_listrik').val(perumahan.kondisi.daya_listrik || '');
    }

    if (perumahan.sanitasi) {
        $('#jenis_kloset').val(perumahan.sanitasi.jenis_kloset || '');
        $('#fasilitas_bab').val(perumahan.sanitasi.fasilitas_bab || '');
        $('#pembuangan_tinja').val(perumahan.sanitasi.pembuangan_tinja || '');
        $('#jarak_air_ke_limbah').val(perumahan.sanitasi.jarak_air_ke_limbah || '');
    }
}

// ================================
// 👨‍👩‍👧 Prefill Tab Keluarga
// ================================
function prefillKeluarga(perumahan) {
    if (!perumahan || typeof perumahan !== 'object') {
        console.warn('⚠️ Data perumahan tidak valid untuk tab keluarga');
        return;
    }
    $('#keluarga_no_kk').val(perumahan.no_kk || '');
    $('#kepala_keluarga').val(perumahan.kepala_keluarga || '');
    $('#alamat').val(perumahan.alamat || '');
    $('#rw').val(perumahan.rw || '');
    $('#rt').val(perumahan.rt || '');
    $('#kategori_adat').val(perumahan.kategori_adat || 'Tidak').trigger('change');
    $('#nama_suku').val(perumahan.nama_suku || '');
}

// ============================================================
// 🚀 Jalankan Setelah Halaman Siap
// ============================================================
$(document).ready(function () {
    // Aktifkan Select2 AJAX wilayah
    initSelect2Wilayah();

    // Pastikan payload global tersedia
    if (typeof payload === 'undefined' || !payload || !payload.perumahan) {
        console.warn('⚠️ Payload kosong atau tidak berisi data perumahan.');
        return;
    }

    const perumahan = payload.perumahan || {};

    // Prefill tab keluarga
    prefillKeluarga(perumahan);

    // Prefill tab rumah
    prefillRumah(perumahan);

    // Prefill wilayah (jika tersedia)
    if (perumahan.wilayah && perumahan.wilayah_nama) {
        prefillWilayah(perumahan.wilayah, perumahan.wilayah_nama);
    } else {
        console.warn('⚠️ Data wilayah tidak ditemukan di payload.');
    }

    // ============================================================
    // Semua listener & AJAX form yang sudah kamu punya (dipertahankan)
    // ============================================================
    $('#formDataKeluarga').on('submit', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Data keluarga akan disimpan sebagai draft pembaruan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/pembaruan-keluarga/save-keluarga',
                    method: 'POST',
                    data: {
                        id_kk: $('#id_kk').val(),
                        no_kk: $('#keluarga_no_kk').val(),
                        kepala_keluarga: $('#kepala_keluarga').val(),
                        alamat: $('#alamat').val(),
                        rt: $('#rt').val(),
                        rw: $('#rw').val(),
                        kategori_desil: $('#kategori_desil').val(),
                        status_rumah: $('#status_rumah').val(),
                        kategori_adat: $('#kategori_adat').val(),
                        nama_suku: $('#nama_suku').val()
                    },
                    success: function (res) {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
                    }
                });
            }
        });
    });

    // ... (lanjutkan semua handler formAnggota, formRumah, formAset, formFoto, dll persis seperti sebelumnya)
});
