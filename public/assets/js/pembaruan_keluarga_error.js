/* ============================================================
 * 📦 Pembaruan Data Keluarga - Versi Final (Stable)
 * Diperbarui oleh Katie & Rian — memastikan kompatibilitas penuh
 * dengan modal Select2, prefill wilayah, dan mode tambah/detail.
 * ============================================================ */

// 🌍 Variabel global
const baseUrl = window.baseUrl || '';
const isTambahMode = (window.isTambahMode === 'true');

// ============================================================
// 🔹 Helper Umum
// ============================================================

// ✅ Helper universal untuk dropdown dinamis (status kawin, pekerjaan, dll)
function updateSelectOptions(selector, list, selectedId) {
    const el = $(selector);
    el.empty().append(`<option value="">-- Pilih --</option>`);
    if (!Array.isArray(list)) return;
    list.forEach(opt => {
        const id = opt.id ?? opt.pk_id ?? opt.value ?? '';
        const name = opt.nama ?? opt.jenis_shdk ?? opt.name ?? opt.label ?? '';
        const selected = (id == selectedId) ? 'selected' : '';
        el.append(`<option value="${id}" ${selected}>${name}</option>`);
    });
}

// ============================================================
// 🔄 Select2 Wilayah (Main & Modal)
// ============================================================

function initSelect2Wilayah() {
    const select2Base = {
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

    const $modal = $('#modalAnggota');

    // Helper untuk attach Select2 dengan dropdownParent opsional
    function attachSelect2(selector, ajaxUrl, dropdownParentEl = null, transportFn = null) {
        const cfg = $.extend(true, {}, select2Base);
        if (ajaxUrl) cfg.ajax = $.extend({}, cfg.ajax, { url: ajaxUrl });
        if (transportFn) cfg.ajax.transport = transportFn;
        if (dropdownParentEl && dropdownParentEl.length) cfg.dropdownParent = dropdownParentEl;
        $(selector).select2(cfg);
    }

    // --- Halaman utama (tab_keluarga / tab_rumah)
    attachSelect2('#rumah_provinsi', baseUrl + '/api/villages/provinces');
    attachSelect2('#rumah_regency', null, null, function(params, success, failure) {
        const provID = $('#rumah_provinsi').val();
        if (!provID) return success([]);
        $.ajax({ url: baseUrl + '/api/villages/regencies/' + provID, dataType: 'json', success, error: failure });
    });
    attachSelect2('#rumah_district', null, null, function(params, success, failure) {
        const kabID = $('#rumah_regency').val();
        if (!kabID) return success([]);
        $.ajax({ url: baseUrl + '/api/villages/districts/' + kabID, dataType: 'json', success, error: failure });
    });
    attachSelect2('#rumah_village', null, null, function(params, success, failure) {
        const kecID = $('#rumah_district').val();
        if (!kecID) return success([]);
        $.ajax({ url: baseUrl + '/api/villages/villages/' + kecID, dataType: 'json', success, error: failure });
    });

    // --- Dalam Modal (alamat anggota)
    if ($modal.length) {
        ['#provinsi', '#kabupaten', '#kecamatan', '#desa'].forEach(sel => {
            if ($(sel).data('select2')) $(sel).select2('destroy');
        });

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

    // Reset cascading
    $('#rumah_provinsi, #provinsi').on('change', function() {
        $('#rumah_regency, #kabupaten, #rumah_district, #kecamatan, #rumah_village, #desa').val(null).trigger('change');
    });
    $('#rumah_regency, #kabupaten').on('change', function() {
        $('#rumah_district, #kecamatan, #rumah_village, #desa').val(null).trigger('change');
    });
    $('#rumah_district, #kecamatan').on('change', function() {
        $('#rumah_village, #desa').val(null).trigger('change');
    });
}

// ============================================================
// 📥 Prefill Data Rumah dari Payload PHP
// ============================================================

function prefillRumah(payload) {
    try {
        if (!payload || !payload.perumahan) {
            console.warn("⚠️ Tidak ada data perumahan di payload.");
            return;
        }

        const perum = payload.perumahan;
        const wilayah = perum.wilayah ?? {};
        const kondisi = perum.kondisi ?? {};
        const sanitasi = perum.sanitasi ?? {};

        $('#rw').val(perum.rw ?? '');
        $('#rt').val(perum.rt ?? '');
        $('#alamat').val(perum.alamat ?? '');
        $('#kepala_keluarga').val(perum.kepala_keluarga ?? '');
        $('#status_kepemilikan').val(perum.status_kepemilikan ?? '');
        $('#kategori_adat').val(perum.kategori_adat ?? 'Tidak');
        $('#nama_suku').val(perum.nama_suku ?? '');

        // Prefill kondisi
        $('#luas_lantai').val(kondisi.luas_lantai ?? '');
        $('#jenis_lantai').val(kondisi.jenis_lantai ?? '');
        $('#jenis_atap').val(kondisi.jenis_atap ?? '');
        $('#bahan_bakar').val(kondisi.bahan_bakar ?? '');
        $('#sumber_air').val(kondisi.sumber_air ?? '');
        $('#sumber_listrik').val(kondisi.sumber_listrik ?? '');

        // Prefill sanitasi
        $('#fasilitas_bab').val(sanitasi.fasilitas_bab ?? '');
        $('#jenis_kloset').val(sanitasi.jenis_kloset ?? '');
        $('#jarak_air_ke_limbah').val(sanitasi.jarak_air_ke_limbah ?? '');
        $('#pembuangan_tinja').val(sanitasi.pembuangan_tinja ?? '');

        // Prefill wilayah
        if (wilayah && Object.keys(wilayah).length > 0) {
            console.log("🧭 Prefill wilayah:", wilayah);
            loadProvinces(wilayah.provinsi, () => {
                loadRegencies(wilayah.provinsi, wilayah.kabupaten, () => {
                    loadDistricts(wilayah.kabupaten, wilayah.kecamatan, () => {
                        loadVillages(wilayah.kecamatan, wilayah.desa);
                    });
                });
            });
        } else {
            console.warn("⚠️ Data wilayah tidak ditemukan di payload.");
        }
    } catch (e) {
        console.error("❌ prefillRumah() error:", e);
    }
}

// ============================================================
// 🧩 Chained Loading Wilayah (Callback Safe)
// ============================================================

function loadProvinces(selected = '', cb = null) {
    $.getJSON(baseUrl + '/api/villages/provinces', res => {
        const $p = $('#provinsi');
        $p.html('<option value="">Pilih Provinsi</option>');
        res.forEach(r => $p.append(`<option value="${r.id}" ${r.id==selected ? 'selected' : ''}>${r.name}</option>`));
        if (cb) cb();
    });
}

function loadRegencies(provId, selected = '', cb = null) {
    if (!provId) return $('#kabupaten').html('<option value="">Pilih Kabupaten</option>');
    $.getJSON(baseUrl + '/api/villages/regencies/' + provId, res => {
        const $k = $('#kabupaten');
        $k.html('<option value="">Pilih Kabupaten</option>');
        res.forEach(r => $k.append(`<option value="${r.id}" ${r.id==selected ? 'selected' : ''}>${r.name}</option>`));
        if (cb) cb();
    });
}

function loadDistricts(kabId, selected = '', cb = null) {
    if (!kabId) return $('#kecamatan').html('<option value="">Pilih Kecamatan</option>');
    $.getJSON(baseUrl + '/api/villages/districts/' + kabId, res => {
        const $k = $('#kecamatan');
        $k.html('<option value="">Pilih Kecamatan</option>');
        res.forEach(r => $k.append(`<option value="${r.id}" ${r.id==selected ? 'selected' : ''}>${r.name}</option>`));
        if (cb) cb();
    });
}

function loadVillages(kecId, selected = '', cb = null) {
    if (!kecId) return $('#desa').html('<option value="">Pilih Desa</option>');
    $.getJSON(baseUrl + '/api/villages/villages/' + kecId, res => {
        const $v = $('#desa');
        $v.html('<option value="">Pilih Desa</option>');
        res.forEach(r => $v.append(`<option value="${r.id}" ${r.id==selected ? 'selected' : ''}>${r.name}</option>`));
        if (cb) cb();
    });
}

// ============================================================
// 🚀 Inisialisasi
// ============================================================

$(document).ready(function() {
    console.log("🚀 pembaruan_keluarga.js aktif");
    initSelect2Wilayah();

    // Prefill dari PHP
    if (typeof payload !== 'undefined') {
        console.log('🚀 Payload dari PHP:', payload);
        prefillRumah(payload);
    }

    // Tombol Apply handler
    $('#btnApply').on('click', function() {
        const usulanId = $(this).data('usulan-id');
        if (!usulanId) return Swal.fire('Gagal', 'ID usulan tidak ditemukan.', 'error');

        Swal.fire({
            title: 'Terapkan Usulan?',
            text: 'Data ini akan dikirim ke database utama dan diverifikasi.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (!result.isConfirmed) return;
            $.post(baseUrl + '/pembaruan-keluarga/apply', { usulan_id: usulanId }, res => {
                if (res.status === 'success') {
                    Swal.fire('Berhasil', res.message, 'success');
                    setTimeout(() => location.href = res.redirect, 1500);
                } else Swal.fire('Gagal', res.message, 'error');
            }, 'json').fail(() => Swal.fire('Error', 'Gagal menghubungi server.', 'error'));
        });
    });

    // Form keluarga (simpan)
    $('#formDataKeluarga').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Data keluarga akan disimpan sebagai draft pembaruan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: baseUrl + '/pembaruan-keluarga/save-keluarga',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: res => {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                        if (res.id_kk) {
                            $('#id_kk').val(res.id_kk);
                            setTimeout(() => location.href = `${baseUrl}/pembaruan-keluarga/detail/${res.id_kk}`, 1000);
                        } else {
                            setTimeout(() => location.reload(), 1000);
                        }
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: () => Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error')
            });
        });
    });
});
