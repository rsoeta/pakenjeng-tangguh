$(document).ready(function () {

    // ============================================================
    // 🛠️  UTILITIES & FILTER FUNCTIONS
    // ============================================================

    function resetProgramBansos() {
        const select = $('#program_bansos');
        select.val('').trigger('change');
        select.find('option').show();
    }

    function filterBySHDK(shdk) {
        // Jika bukan KK(1) atau Istri(3) → hanya PBI
        if (shdk !== 1 && shdk !== 3) {
            console.log("🔒 Filter SHDK aktif → hanya PBI");

            $('#program_bansos option').each(function () {
                const txt = $(this).text().toUpperCase();
                if (!txt.includes('PBI') && $(this).val() !== '') {
                    $(this).hide();
                }
            });

            // Auto-select PBI
            const pbi = $('#program_bansos option').filter((_, o) =>
                $(o).text().toUpperCase().includes('PBI')
            ).first();

            if (pbi.length) $('#program_bansos').val(pbi.val()).trigger('change');

            return false; // berhenti, karena SHDK ≠ 1/3 dominan
        }

        return true; // lanjut ke filter desil
    }

    function filterByDesil(desil) {
        if (desil === 5) {
            console.log("🔒 Filter Desil 5 aktif → hanya BPNT & PBI");

            $('#program_bansos option').each(function () {
                const txt = $(this).text().toUpperCase();
                if (
                    !txt.includes('BPNT') &&
                    !txt.includes('PBI') &&
                    $(this).val() !== ''
                ) {
                    $(this).hide();
                }
            });
        }
    }

    function applyProgramFilters(shdk, desil) {
        resetProgramBansos();

        // ======================================================
        // RULE 1 — Desil > 5 → otomatis TIDAK LAYAK
        // ======================================================
        if (desil > 5) {
            console.log("❌ Desil > 5 → Tidak layak mengajukan");
            resetProgramBansos();
            $('#program_bansos option').not('[value=""]').hide();
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Layak',
                text: 'Kategori desil di atas 5 tidak dapat mengajukan usulan.',
            });
            return;
        }

        // ======================================================
        // RULE 2 — Filter SHDK (jika bukan KK/Istri → hanya PBI)
        // ======================================================
        const allowContinue = filterBySHDK(shdk);
        if (!allowContinue) return; // Tidak perlu lanjut ke desil

        // ======================================================
        // RULE 3 — Filter DESIL (khusus desil 5 → BPNT + PBI)
        // ======================================================
        filterByDesil(desil);
    }

    // ============================================================
    // 🟢  EVENT: Buka Modal Tambah Usulan
    // ============================================================

    $('#btnTambahUsulan').on('click', function () {

        $('#modalUsulanBansos').on('show.bs.modal', function () {
            $('#formUsulanBansos')[0].reset();
            $('#nik_peserta').val(null).trigger('change');
            resetProgramBansos();
            $('#kategori_desil').val('');
        });

        $('#modalUsulanBansos').modal('show');
    });

    // ============================================================
    // 🟦  SELECT2 — Pilih Individu
    // ============================================================

    $('#nik_peserta').select2({
        dropdownParent: $('#modalUsulanBansos'),
        theme: 'bootstrap-5',
        placeholder: 'Ketik NIK atau Nama (min 3 huruf)',
        minimumInputLength: 3,
        width: '100%',
        ajax: {
            url: '/usulan-bansos/search-art',
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results || [] }),
            cache: true
        },
        templateResult: function (item) {
            if (!item.id) return item.text;
            const wilayah = (item.rw || item.rt)
                ? ` — RW ${item.rw} / RT ${item.rt}`
                : '';
            return $(`
                <div>${item.id} — <strong>${item.nama.toUpperCase()}</strong> (${item.shdk_nama})
                <br><small class="text-muted">${wilayah}</small>
                </div>
            `);
        },
        templateSelection: function (item) {
            if (!item.id) return item.text;
            return `${item.id} - ${item.nama.toUpperCase()}`;
        }
    });

    // ============================================================
    // 🔄  Saat individu dipilih → apply filter + cek desil
    // ============================================================

    $('#nik_peserta').on('select2:select', function (e) {
        const data = e.params.data;

        const nik = data.id;
        const shdk = parseInt(data.shdk || 0);

        $('#hidden_nik').val(nik);
        $('#hidden_shdk').val(shdk);

        resetProgramBansos();

        console.log("👤 Individu dipilih:", data);

        // ======================================================
        // AJAX cek kategori desil
        // ======================================================
        $.getJSON('/usulan-bansos/check-desil', { nik: nik })
            .done(function (res) {
                if (!res.success) {
                    $('#kategori_desil').val('');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memeriksa Desil',
                        text: res.message || 'Tidak dapat memuat data desil.'
                    });
                    return;
                }

                const desil = parseInt(res.kategori_desil);
                $('#kategori_desil').val(desil);

                // Terapkan filter gabungan rules:
                // - SHDK filter
                // - Desil filter
                // - Tidak layak (desil > 5)
                applyProgramFilters(shdk, desil);

                // Popup informasi
                if (desil <= 5) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Layak Diusulkan',
                        text: 'Kategori desil ' + desil + ' memenuhi syarat.'
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Layak',
                        text: 'Kategori desil ' + desil + ' di atas batas kelayakan.'
                    });
                }
            })
            .fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Server',
                    text: 'Gagal terhubung ke server untuk memeriksa desil.'
                });
            });
    });

    // ============================================================
    // 💾 SUBMIT FORM
    // ============================================================

    $('#formUsulanBansos').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const btn = form.find('button[type="submit"]');
        btn.prop('disabled', true);

        $.ajax({
            url: '/usulan-bansos/save',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function (res) {
                btn.prop('disabled', false);

                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Usulan bansos berhasil disimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    $('#modalUsulanBansos').modal('hide');
                    $('#formUsulanBansos')[0].reset();

                    $(document).trigger('usulanBansosSaved');

                    if ($.fn.DataTable.isDataTable('#tableUsulanBansosDraft')) {
                        $('#tableUsulanBansosDraft').DataTable().ajax.reload(null, false);
                    }
                    if ($$.fn.DataTable.isDataTable('#tableUsulanBansosVerified')) {
                        $('#tableUsulanBansosVerified').DataTable().ajax.reload(null, false);
                    }
                } else {
                    Swal.fire('Gagal', res.message || 'Gagal menyimpan usulan.', 'error');
                }
            },
            error: function () {
                btn.prop('disabled', false);
                Swal.fire('Kesalahan Server', 'Tidak dapat menyimpan data usulan.', 'error');
            }
        });
    });

});
