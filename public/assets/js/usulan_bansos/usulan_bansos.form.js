/* ========================================================================
   📄 usulan_bansos.form.js
   Handler Form Usulan Bansos (Modal, Select2, Filter Desil/SHDK/PBI, Submit)
   ======================================================================== */

$(document).ready(function () {

    // 🚀 KONFIGURASI GLOBAL SWEETALERT2 AUTO-CLOSE (2000ms)
    const SwalSuccessAuto = Swal.mixin({
        icon: 'success',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        width: '22rem',
        padding: '1.25rem',
        customClass: {
            popup: 'swal-sm rounded-4 shadow',
            title: 'fs-5 fw-bold',
            htmlContainer: 'fs-6'
        }
    });

    function showSuccess(message, title = 'Berhasil!') {
        SwalSuccessAuto.fire({
            title: title,
            text: message
        });
    }

    /* ============================================================
       🔧 UTILITIES
       ============================================================ */

    function resetProgramBansos() {
        const select = $('#program_bansos');
        select.val('').trigger('change');
        select.find('option').prop('disabled', false).show();
        $('#formUsulanBansos button[type="submit"]').prop('disabled', false);
    }

    /* ---------------------------------------------
       RULE GABUNGAN (SHDK + DESIL + STATUS PBI AKTIF)
    --------------------------------------------- */
    function applyProgramFilters(shdk, desil, isPbiAktif = false, pbiInfo = null) {

        resetProgramBansos();

        const select = $('#program_bansos');
        const options = select.find('option');
        const btnSubmit = $('#formUsulanBansos button[type="submit"]');

        // 🔴 1. DESIL tidak valid atau > 5 (Batas toleransi Kemensos)
        if (desil === null || desil === 0 || isNaN(desil) || desil > 5) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Layak',
                text: 'Kategori desil ' + (desil || 'Tidak Valid') + ' tidak memenuhi syarat (Maksimal Desil 5).',
                width: '22rem',
                customClass: { popup: 'swal-sm rounded-4' }
            });
            options.not('[value=""]').prop('disabled', true).hide();
            btnSubmit.prop('disabled', true);
            return;
        }

        const isWajibPbiSaja = (desil === 5) || (shdk !== 1 && shdk !== 3);
        const jenisPbiTeks = (pbiInfo && pbiInfo.jenis_kepesertaan) ? pbiInfo.jenis_kepesertaan : 'PBI-JKN';

        // 🔴 2. CEK BLOKIR MUTLAK: Jika warga HANYA BOLEH PBI (Desil 5 atau Anak/Famili), TAPI SUDAH PBI AKTIF!
        if (isPbiAktif && isWajibPbiSaja) {
            options.not('[value=""]').prop('disabled', true).hide();
            btnSubmit.prop('disabled', true);

            const alasanKhusus = (desil === 5)
                ? 'Berada di <b>Desil 5</b> (hanya layak PBI-JK)'
                : 'Berstatus <b>Selain Kepala Keluarga / Istri</b> (hanya layak PBI-JK)';

            Swal.fire({
                icon: 'error',
                title: 'Sudah Peserta PBI Aktif!',
                html: `Warga ini tidak dapat diusulkan karena:<br>
                       <div class="text-start small bg-light p-2 rounded border mt-2 mb-3">
                           1. ${alasanKhusus}.<br>
                           2. Tercatat sudah berstatus <b>${jenisPbiTeks} AKTIF</b> di Master Data PBI.
                       </div>
                       <button type="button" id="btnBukaModalSanggah" class="btn btn-warning btn-sm fw-bold text-dark w-100 shadow-sm">
                           <i class="fas fa-exclamation-triangle me-1"></i> Sanggah Status PBI (Ajukan Non-Aktif)
                       </button>`,
                width: '24rem',
                showConfirmButton: false, // Sembunyikan tombol OK bawaan, ganti tombol Sanggah interaktif
                showCloseButton: true,
                customClass: { popup: 'swal-sm rounded-4' },
                didOpen: () => {
                    // Ketika tombol Sanggah di dalam SweetAlert diklik
                    $('#btnBukaModalSanggah').on('click', function () {
                        Swal.close(); // Tutup alert peringatan
                        bukaModalSanggahPbi(pbiInfo ? pbiInfo.nik_warga : $('#hidden_nik').val(), pbiInfo ? pbiInfo.nama_warga : '');
                    });
                }
            });
            return;
        }

        // 🟡 3. JIKA SUDAH PBI AKTIF (Tetapi Kepala Keluarga/Istri di Desil 1-4 -> Masih bisa PKH / Sembako, tapi boleh disanggah)
        if (isPbiAktif && !isWajibPbiSaja) {
            options.each(function () {
                const text = $(this).text().toUpperCase();
                if (text.includes('PBI')) {
                    $(this).prop('disabled', true).hide();
                }
            });

            Swal.fire({
                icon: 'info',
                title: 'Layak (Non-PBI)',
                html: `Kategori <b>Desil ${desil}</b> memenuhi syarat usulan PKH/Sembako.<br><br>
                       <small class="text-danger fw-bold"><i class="fas fa-info-circle"></i> Opsi PBI-JK disembunyikan karena warga ini tercatat sebagai peserta ${jenisPbiTeks} Aktif.</small><br><br>
                       <button type="button" id="btnBukaModalSanggah2" class="btn btn-warning btn-sm fw-bold text-dark w-100 shadow-sm mt-1">
                           <i class="fas fa-exclamation-triangle me-1"></i> Sanggah Status PBI (Jika Sudah Tidak Aktif)
                       </button>`,
                width: '24rem',
                showConfirmButton: true,
                confirmButtonText: 'Lanjutkan Tanpa PBI',
                customClass: { popup: 'swal-sm rounded-4' },
                didOpen: () => {
                    $('#btnBukaModalSanggah2').on('click', function () {
                        Swal.close();
                        bukaModalSanggahPbi(pbiInfo ? pbiInfo.nik_warga : $('#hidden_nik').val(), '');
                    });
                }
            });
            return;
        }

        // 🔵 4. KEMENSOS RULE: DESIL 5 (Belum PBI Aktif) -> HANYA PBI-JK
        if (desil === 5) {
            options.each(function () {
                const text = $(this).text().toUpperCase();
                if (!text.includes('PBI') && $(this).val() !== '') {$(this).prop('disabled', true).hide();
                }
            });

            const pbi = options.filter((i, o) => $(o).text().toUpperCase().includes('PBI')).first();
            if (pbi.length) select.val(pbi.val()).trigger('change');

            Swal.fire({
                title: 'Layak Bersyarat',
                text: 'Desil 5 HANYA diperbolehkan untuk usulan PBI-JK.',
                icon: 'info',
                width: '22rem',
                customClass: { popup: 'swal-sm rounded-4' }
            });
            return;
        }

        // 🔵 5. SHDK RULE: Bukan Kepala Keluarga & Bukan Istri (Desil 1-4 & Belum PBI Aktif) -> PAKSA HANYA PBI
        if (shdk !== 1 && shdk !== 3) {
            options.each(function () {
                const text = $(this).text().toUpperCase();
                if (!text.includes('PBI') && $(this).val() !== '') {$(this).prop('disabled', true).hide();
                }
            });

            const pbi = options.filter((i, o) => $(o).text().toUpperCase().includes('PBI')).first();
            if (pbi.length) select.val(pbi.val()).trigger('change');

            Swal.fire({
                title: 'Layak Khusus PBI-JK',
                text: `Desil ${desil} (Anggota Keluarga selain KK/Istri hanya diperbolehkan untuk usulan PBI-JK).`,
                icon: 'info',
                width: '22rem',
                customClass: { popup: 'swal-sm rounded-4' }
            });
            return;
        }

        // 🟢 6. KEMENSOS RULE: DESIL 1-4 (KK/Istri & Belum PBI Aktif) -> BISA PKH, SEMBAKO, PBI
        SwalSuccessAuto.fire({
            title: 'Layak Diusulkan',
            text: `Kategori Desil ${desil} memenuhi syarat untuk usulan PKH, Sembako/BPNT, dan PBI.`
        });
    }

    // 🛡️ MODAL SANGGAH PBI AKTIF (Form Pengajuan Non-Aktif Darurat oleh PENTRI)
    function bukaModalSanggahPbi(nikWarga, namaWarga) {
        nikWarga = nikWarga || $('#hidden_nik').val();

        // Ambil pilihan alasan non-aktif yang sudah ada di database untuk menghindari typo
        $.getJSON('/usulan-bansos/get-list-alasan-pbi', function (res) {
            let opsiAlasanHtml = '<option value="">-- Pilih Sumber/Alasan Sanggahan --</option>';
            if (res.status && res.data) {
                res.data.forEach(item => {
                    opsiAlasanHtml += `<option value="${item.alasan_nonaktif}">${item.alasan_nonaktif}</option>`;
                });
            }
            opsiAlasanHtml += '<option value="LAINNYA">LAINNYA (Tulis Manual)</option>';

            Swal.fire({
                title: 'Form Sanggah Status PBI',
                html: `
                    <div class="text-start small mb-3">
                        <p class="text-muted mb-2">Gunakan formulir ini apabila NIK <b>${nikWarga}</b> berdasarkan hasil cek CekBansos/Pandawa terbukti sudah <b>tidak aktif</b>, namun masih terkunci di sistem desa.</p>
                        
                        <label class="fw-bold text-secondary mb-1">Pilih Alasan Sanggahan <span class="text-danger">*</span></label>
                        <select id="sanggah_alasan_select" class="form-select form-select-sm mb-2" onchange="toggleSanggahAlasan(this.value)">
                            ${opsiAlasanHtml}
                        </select>
                        <input type="text" id="sanggah_alasan_manual" class="form-control form-control-sm d-none mb-2" placeholder="Tuliskan alasan spesifik...">

                        <label class="fw-bold text-secondary mb-1 mt-2">Unggah Bukti Screenshot Pemeriksaan <span class="text-danger">*</span></label>
                        <input type="file" id="sanggah_bukti_file" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted" style="font-size: 0.7rem;">Format gambar (JPG/PNG), maks 2MB.</small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-paper-plane text-dark me-1"></i> Kirim Sanggahan',
                cancelButtonText: 'Batal',
                width: '26rem',
                customClass: { popup: 'swal-sm rounded-4' },
                preConfirm: () => {
                    let selectVal = document.getElementById('sanggah_alasan_select').value;
                    let manualVal = document.getElementById('sanggah_alasan_manual').value.trim();
                    let fileInput = document.getElementById('sanggah_bukti_file').files[0];

                    let alasanFinal = (selectVal === 'LAINNYA') ? manualVal : selectVal;

                    if (!alasanFinal) {
                        Swal.showValidationMessage('Alasan sanggahan wajib dipilih atau diisi!');
                        return false;
                    }
                    if (!fileInput) {
                        Swal.showValidationMessage('Bukti screenshot wajib dilampirkan!');
                        return false;
                    }

                    return {
                        nik: nikWarga,
                        alasan: alasanFinal,
                        file: fileInput
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let dataForm = new FormData();
                    dataForm.append('nik', result.value.nik);
                    dataForm.append('alasan', result.value.alasan);
                    dataForm.append('bukti_foto', result.value.file);

                    Swal.fire({
                        title: 'Mengirim Sanggahan...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        customClass: { popup: 'swal-sm rounded-4' },
                        didOpen: () => Swal.showLoading()
                    });

                    // Kirim ke Backend Controller UsulanBansos
                    $.ajax({
                        url: '/usulan-bansos/proses-sanggah-pbi',
                        type: 'POST',
                        data: dataForm,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function (r) {
                            if (r.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sanggahan Terkirim!',
                                    text: r.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    customClass: { popup: 'swal-sm rounded-4' }
                                });
                                // Reset form / refresh select2 jika perlu
                                $('#nik_peserta').val(null).trigger('change');
                                resetProgramBansos();
                            } else {
                                Swal.fire({ icon: 'error', title: 'Gagal', text: r.message, customClass: { popup: 'swal-sm rounded-4' } });
                            }
                        },
                        error: function () {
                            Swal.fire({ icon: 'error', title: 'Error Server', text: 'Gagal mengirim data sanggahan.', customClass: { popup: 'swal-sm rounded-4' } });
                        }
                    });
                }
            });
        }).fail(() => {
            Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Gagal mengambil daftar referensi alasan.', customClass: { popup: 'swal-sm rounded-4' } });
        });
    }

    // Fungsi bantu tampilkan input teks jika pilih LAINNYA
    window.toggleSanggahAlasan = function(val) {
        let inputManual = document.getElementById('sanggah_alasan_manual');
        if (val === 'LAINNYA') {
            inputManual.classList.remove('d-none');
            inputManual.focus();
        } else {
            inputManual.classList.add('d-none');
            inputManual.value = '';
        }
    };

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
                Swal.fire({
                    icon: 'info',
                    title: 'Belum Dibuka',
                    text: 'Pengajuan belum dibuka.',
                    width: '22rem',
                    customClass: { popup: 'swal-sm rounded-4' }
                });
                if (typeof updateButtonState === 'function') updateButtonState();
                return;
            }

            if (end && now > end) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Masa Berakhir',
                    text: 'Pengajuan telah berakhir.',
                    width: '22rem',
                    customClass: { popup: 'swal-sm rounded-4' }
                });
                if (typeof updateButtonState === 'function') updateButtonState();
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
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan',
                text: 'Gagal memeriksa batas waktu.',
                width: '22rem',
                customClass: { popup: 'swal-sm rounded-4' }
            });
        }
    });


    /* ========================================================================
       🟦 SELECT2 — Pencarian Individu Berdasarkan NIK
       ======================================================================== */
    $('#nik_peserta').select2({
        dropdownParent: $('#modalUsulanBansos'),
        theme: 'bootstrap-5',
        placeholder: 'Ketik NIK (min 3 karakter)...',
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

            const badgePbi = item.is_pbi_aktif
                ? `<span class="badge bg-success ms-1" style="font-size: 0.7rem;"><i class="fas fa-check-circle"></i> ${item.pbi_jenis || 'PBI'} Aktif</span>`
                : '';

            return $(`
                <div>
                    ${item.id} — <strong>${item.nama.toUpperCase()}</strong> (${item.shdk_nama}) ${badgePbi}
                    <br><small class="text-muted">${wilayah}</small>
                </div>
            `);
        },

        templateSelection: function (item) {
            if (!item.id) return item.text;
            const statusTeks = item.is_pbi_aktif ? ' [PBI AKTIF]' : '';
            return `${item.id} — ${item.nama.toUpperCase()}${statusTeks}`;
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

        // Cek DESIL & Status PBI Aktif via AJAX
        $.getJSON('/usulan-bansos/check-desil', { nik })
            .done(res => {

                if (!res.success) {
                    Swal.fire({
                        title: 'Gagal Memeriksa Data',
                        text: res.message,
                        icon: 'error',
                        width: '22rem',
                        customClass: { popup: 'swal-sm rounded-4' }
                    });
                    $('#formUsulanBansos button[type="submit"]').prop('disabled', true);
                    return;
                }

                const desil = parseInt(res.kategori_desil);
                const isPbiAktif = Boolean(res.is_pbi_aktif);
                const pbiInfo = res.pbi_info || null;

                $('#kategori_desil').val(desil + (isPbiAktif ? ' (Peserta PBI Aktif)' : ''));

                // 🚀 Apply rule gabungan (SHDK + Desil + Status PBI Aktif)
                applyProgramFilters(shdk, desil, isPbiAktif, pbiInfo);

            })
            .fail(() => Swal.fire({
                title: 'Kesalahan',
                text: 'Tidak dapat memeriksa desil dan status PBI.',
                icon: 'error',
                width: '22rem',
                customClass: { popup: 'swal-sm rounded-4' }
            }));
    });


    /* ========================================================================
       💾 SUBMIT FORM USULAN
       ======================================================================== */

    $('#formUsulanBansos').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const btn = form.find('button[type="submit"]');

        if (!$('#program_bansos').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Program',
                text: 'Silakan pilih program bantuan sosial yang tersedia.',
                width: '22rem',
                customClass: { popup: 'swal-sm rounded-4' }
            });
            return;
        }

        btn.prop('disabled', true);

        $.ajax({
            url: '/usulan-bansos/save',
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',

            success: function (res) {
                btn.prop('disabled', false);

                if (res.success) {
                    // 🚀 Panggil fungsi global auto-close (2000ms + progress bar)
                    showSuccess(res.message);

                    $('#modalUsulanBansos').modal('hide');
                    $('#formUsulanBansos')[0].reset();

                    $(document).trigger('usulanBansosSaved');

                    if ($.fn.DataTable.isDataTable('#tableUsulanBansosDraft')) {
                        $('#tableUsulanBansosDraft').DataTable().ajax.reload(null, false);
                    }
                    if ($.fn.DataTable.isDataTable('#tableUsulanBansosVerified')) {
                        $('#tableUsulanBansosVerified').DataTable().ajax.reload(null, false);
                    }

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message,
                        width: '22rem',
                        customClass: { popup: 'swal-sm rounded-4' }
                    });
                }
            },

            error: function () {
                btn.prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Tidak dapat menyimpan data usulan.',
                    width: '22rem',
                    customClass: { popup: 'swal-sm rounded-4' }
                });
            }
        });
    });

});