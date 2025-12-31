/* ========================================================================
   📄 usulan_bansos.form.js
   Handler Form Usulan Bansos (Modal, Select2, Filter, Submit)
   ======================================================================== */

$(document).ready(function () {

    /* ============================================================
       🔧 UTILITIES
       ============================================================ */

    function resetProgramBansos() {
        const select = $('#program_bansos');
        select.val('').trigger('change');
        select.find('option').show();
    }

    /* ---------------------------------------------
       RULE 1 — SHDK Filter
       --------------------------------------------- 
       Jika SHDK ≠ 1 (Kepala Keluarga) dan ≠ 3 (Istri)
       => hanya boleh mengajukan PBI
    ------------------------------------------------ */
    function filterBySHDK(shdk) {
        if (shdk !== 1 && shdk !== 3) {
            console.log("🔒 SHDK bukan KK/Istri → hanya PBI");

            $('#program_bansos option').each(function () {
                const text = $(this).text().toUpperCase();
                if (!text.includes('PBI') && $(this).val() !== '') {
                    $(this).hide();
                }
            });

            // Auto-select PBI
            const pbi = $('#program_bansos option').filter((i, o) =>
                $(o).text().toUpperCase().includes('PBI')
            ).first();

            if (pbi.length) $('#program_bansos').val(pbi.val()).trigger('change');

            return false; // stop processing
        }
        return true;
    }

    /* ---------------------------------------------
       RULE 2 — DESIL FILTER
       Desil 5 → hanya SEMBAKO & PBI
    --------------------------------------------- */
    function filterByDesil(desil) {
        if (desil === 5) {
            console.log("🔒 DESIL 5 → hanya SEMBAKO+PBI");
            $('#program_bansos option').each(function () {
                const text = $(this).text().toUpperCase();
                if (
                    !text.includes('BPNT') &&
                    !text.includes('SEMBAKO') &&   //<-- tambahan penting
                    !text.includes('PBI') &&
                    $(this).val() !== ''
                )
    {
                    $(this).hide();
                }
            });
        }
    }

    /* ---------------------------------------------
       RULE GABUNGAN (SHDK + DESIL + VALIDASI)
    --------------------------------------------- */
    function applyProgramFilters(shdk, desil) {

        resetProgramBansos();

        const select = $('#program_bansos');
        const options = select.find('option');

        // 🔴 DESIL tidak valid → tidak layak
        if (desil === null || desil === 0 || isNaN(desil) || desil > 5) {
            Swal.fire(
                'Tidak Layak',
                'Kategori desil tidak valid atau di atas 5.',
                'warning'
            );
            options.not('[value=""]').hide();
            return;
        }

        // 🔵 DESIL 5
        if (desil === 5) {
            options.each(function () {
                const text = $(this).text().toUpperCase();
                if (
                    !text.includes('PBI') &&
                    !text.includes('BPNT') &&
                    !text.includes('SEMBAKO') &&
                    $(this).val() !== ''
                ) {
                    $(this).hide();
                }
            });
        }

        // 🔵 SHDK LAIN / NULL / 0 → hanya PBI
        if (shdk !== 1 && shdk !== 3) {
            options.each(function () {
                const text = $(this).text().toUpperCase();
                if (!text.includes('PBI') && $(this).val() !== '') {
                    $(this).hide();
                }
            });
        }
    }

    /* ========================================================================
       🟢 EVENT: Tombol Tambah Usulan (cek deadline dulu)
       ======================================================================== */

    $('#btnTambahUsulan').on('click', async function (e) {
        e.preventDefault();

        try {
            const res = await fetch('/usulan-bansos/check-deadline');
            const json = await res.json();

            const now = new Date(json.now || new Date());
            const start = json.start ? new Date(json.start) : null;
            const end = json.end ? new Date(json.end) : null;

            if (start && now < start) {
                Swal.fire('Belum Dibuka', 'Pengajuan belum dibuka.', 'info');
                updateButtonState();
                return;
            }

            if (end && now > end) {
                Swal.fire('Masa Berakhir', 'Pengajuan telah berakhir.', 'warning');
                updateButtonState();
                return;
            }

            if (json.allowed) {
                $('#modalUsulanBansos').off('show.bs.modal');
                $('#modalUsulanBansos').on('show.bs.modal', function () {
                    $('#formUsulanBansos')[0].reset();
                    $('#nik_peserta').val(null).trigger('change');
                    resetProgramBansos();
                    $('#kategori_desil').val('');
                });

                $('#modalUsulanBansos').modal('show');
            }

        } catch (err) {
            console.error("btnTambahUsulan error:", err);
            Swal.fire('Kesalahan', 'Gagal memeriksa batas waktu.', 'error');
        }
    });


    /* ========================================================================
       🟦 SELECT2 — Pencarian Individu
       ======================================================================== */
    $('#nik_peserta').select2({
        dropdownParent: $('#modalUsulanBansos'),
        theme: 'bootstrap-5',
        placeholder: 'Ketik NIK (min 3 digit)...',
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
                <br><small class="text-muted">${wilayah}</small></div>
            `);
        },

        templateSelection: function (item) {
            if (!item.id) return item.text;
            return `${item.id} — ${item.nama.toUpperCase()}`;
        }
    });

    /* ========================================================================
       🔄 APPLY FILTER SAAT INDIVIDU DIPILIH
       ======================================================================== */

    $('#nik_peserta').on('select2:select', function (e) {
        const data = e.params.data;

        const nik = data.id;
        const shdk = parseInt(data.shdk || 0);

        $('#hidden_nik').val(nik);
        $('#hidden_shdk').val(shdk);

        resetProgramBansos();

        // Cek DESIL via AJAX
        $.getJSON('/usulan-bansos/check-desil', { nik })
            .done(res => {

                if (!res.success) {
                    Swal.fire('Gagal Memeriksa Desil', res.message, 'error');
                    return;
                }

                const desil = parseInt(res.kategori_desil);
                $('#kategori_desil').val(desil);

                // Apply rule gabungan
                applyProgramFilters(shdk, desil);

                if (desil <= 5) {
                    Swal.fire('Layak Diusulkan', `Kategori desil ${desil} memenuhi syarat.`, 'success');
                } else {
                    Swal.fire('Tidak Layak', `Kategori desil ${desil} di atas batas kelayakan.`, 'warning');
                }

            })
            .fail(() => Swal.fire('Kesalahan', 'Tidak dapat memeriksa desil.', 'error'));
    });


    /* ========================================================================
       💾 SUBMIT FORM USAULAN
       ======================================================================== */

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
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $('#modalUsulanBansos').modal('hide');
                    $('#formUsulanBansos')[0].reset();

                    // Trigger event global
                    $(document).trigger('usulanBansosSaved');

                    // Reload table jika ada
                    if ($.fn.DataTable.isDataTable('#tableUsulanBansosDraft')) {
                        $('#tableUsulanBansosDraft').DataTable().ajax.reload(null, false);
                    }
                    if ($.fn.DataTable.isDataTable('#tableUsulanBansosVerified')) {
                        $('#tableUsulanBansosVerified').DataTable().ajax.reload(null, false);
                    }

                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },

            error: function () {
                btn.prop('disabled', false);
                Swal.fire('Kesalahan', 'Tidak dapat menyimpan data usulan.', 'error');
            }
        });
    });

});
