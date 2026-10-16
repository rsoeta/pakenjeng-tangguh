/* ========================================================================
   📄 usulan_bansos.table.js
   Modul DataTable untuk Usulan Bansos
   ======================================================================== */

$(document).ready(function () {

    const userRole = window.userRole || 99;
    console.log("User Role:", userRole);

    let tableDraft;
    let tableVerified;

    // const now = new Date();
    // $('#filterBulan').val(String(now.getMonth() + 1).padStart(2, '0'));
    // $('#filterTahun').val(now.getFullYear());

    /* ============================================================
       🔧 UTILITIES
       ============================================================ */

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatWaNumber(raw) {
        if (!raw) return null;
        const num = String(raw).replace(/\D/g, '');
        if (!num) return null;

        if (num.startsWith('0')) return '62' + num.slice(1);
        if (num.startsWith('62')) return num;
        if (num.startsWith('8')) return '62' + num;

        return num;
    }

    function renderCreatorWithWA(row) {
        const name = row.created_by_name || '-';
        const raw = row.created_by_nope || row.nope || '';
        const wa = formatWaNumber(raw);

        if (wa) {
            const text = encodeURIComponent(
                `Assalamualaikum...`
            );
            const waUrl = `https://wa.me/${wa}?text=${text}`;
            return `<a href="${waUrl}" target="_blank">${escapeHtml(name)}</a>`;
        }

        return escapeHtml(name);
    }


    function loadFilterOptions() {
        return $.ajax({
            url: '/usulan-bansos/filter-options',
            type: 'GET',
            dataType: 'json'
        }).done(function(res) {

            if (!res || res.error) {
                console.error("Filter options error:", res?.error);
                return;
            }

            // PROGRAM
            if (res.program) {
                res.program.forEach(p => {
                    $('#filterProgram').append(
                        `<option value="${p.dbj_id}">${p.dbj_nama_bansos}</option>`
                    );
                });
            }

            // CREATED BY
            if (res.created_by) {
                res.created_by.forEach(u => {
                    $('#filterCreatedBy').append(
                        `<option value="${u.created_by}">${u.fullname}</option>`
                    );
                });
            }

            // RW
            if (res.rw) {
                res.rw.forEach(r => {
                    $('#filterRW').append(
                        `<option value="${r.rw}">RW ${r.rw}</option>`
                    );
                });
            }

            // RT
            if (res.rt) {
                res.rt.forEach(r => {
                    $('#filterRT').append(
                        `<option value="${r.rt}">RT ${r.rt}</option>`
                    );
                });
            }

            // 👉 TAMBAHAN BULAN
            const namaBulan = [
                "Januari", "Februari", "Maret", "April",
                "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];

            for (let i = 1; i <= 12; i++) {
                const val = i.toString().padStart(2, '0');
                const label = namaBulan[i - 1];

                $('#filterBulan').append(
                    `<option value="${val}">${label}</option>`
                );
            }

            // 👉 TAMBAHAN TAHUN
            const now = new Date();
            const currentYear = now.getFullYear();

            for (let y = currentYear - 2; y <= currentYear + 1; y++) {
                $('#filterTahun').append(`<option value="${y}">${y}</option>`);
            }

        }).fail(function(xhr) {
            console.error("AJAX error:", xhr.responseText);
        });
    }
    
    function setDefaultPeriode() {
        const now = new Date();

        const bulan = String(now.getMonth() + 1).padStart(2, '0');
        const tahun = now.getFullYear();

        $('#filterBulan').val(bulan);
        $('#filterTahun').val(tahun);
    }

     loadFilterOptions().then(() => {

        // 1. Set default dulu
        setDefaultPeriode();

        // 2. Baru init DataTable
        initDataTable();

    });

    // loadFilterOptions();

    function getFilterParams(status) {
        return {
            status: status,
            program: $('#filterProgram').val() || null,
            created_by: $('#filterCreatedBy').val() || null,
            rw: $('#filterRW').val() || null,
            rt: $('#filterRT').val() || null,
            bulan: $('#filterBulan').val() || null,
            tahun: $('#filterTahun').val() || null
        };
    }

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
                    ontouchstart="this.innerText='${numStr}'" 
                    ontouchend="this.innerText='${masked}'" 
                    title="Tahan/Arahkan kursor untuk melihat utuh">${masked}</span>`;
    }

    function initDataTable() {

        // Daftar label untuk tampilan Card di Mobile
        const labelsDraft = ['No', 'NIK / No. KK', 'Nama', 'Program', 'Status', 'Tanggal Dibuat', 'Dibuat Oleh', 'Aksi'];
        const labelsVerified = ['No', 'NIK / No. KK', 'Nama', 'Program', 'Status', 'Tanggal Diverifikasi', 'Verifikator'];

        /* ============================================================
        📊 DATATABLE — DRAFT
        ============================================================ */

        tableDraft = $('#tableUsulanBansosDraft').DataTable({
            ajax: {
                url: '/usulan-bansos/data',
                type: 'GET',
                dataType: 'json',
                data: function (d) {
                    return Object.assign(d, getFilterParams('draft'));
                },
                dataSrc: json => json.data || []
            },

            pageLength: 10,
            responsive: false, // 🚀 Matikan collapse agar CSS Card bekerja di HP
            autoWidth: false,
            columnDefs: [], // 🚀 Kunci mencegah error targets parameter 3

            columns: [
                {
                    data: null,
                    title: 'No',
                    width: '40px',
                    className: 'text-center align-middle',
                    render: (d, t, r, m) => `<span class="badge bg-light text-dark border">${m.row + 1}</span>`
                },
                {
                    data: null,
                    title: 'NIK / No. KK',
                    className: 'align-middle',
                    render: function (data, type, row) {
                        let nik = row.nik ? row.nik : '-';
                        let noKk = row.no_kk ? row.no_kk : '-';

                        if (type === 'filter' || type === 'sort') return nik + ' ' + noKk;

                        let maskedNik = maskNumberJS(nik);
                        let maskedKk = maskNumberJS(noKk);

                        // 🚀 PERBAIKAN: d-flex align-items-center agar tombol berdampingan
                        // gap-3 untuk jarak antar NIK & KK dalam 1 baris
                        return `
                        <div class="d-flex flex-wrap align-items-center gap-3 w-100" style="font-size: 0.85rem;">
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-1">NIK:</small>
                                <span class="fw-bold nik-text me-1">${maskedNik}</span>
                                <button type="button" class="btn btn-outline-primary btn-sm btnCopyNIK py-0 px-1" data-nik="${nik}" title="Salin NIK">
                                    <i class="fas fa-copy" style="font-size: 0.7rem;"></i>
                                </button>
                            </div>
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-1">KK:</small>
                                <span class="fw-bold text-info nik-text me-1">${maskedKk}</span>
                                <button type="button" class="btn btn-outline-info btn-sm btnCopyNIK py-0 px-1" data-nik="${noKk}" title="Salin No. KK">
                                    <i class="fas fa-copy" style="font-size: 0.7rem;"></i>
                                </button>
                            </div>
                        </div>
                        `;
                    }
                },
                {
                    data: 'nama',
                    title: 'Nama',
                    className: 'text-md-nowrap align-middle',
                    render: nama => `
                        <!-- 🚀 PERBAIKAN: Tombol copy menempel di samping nama -->
                        <div class="d-flex align-items-center gap-2 w-100">
                            <span class="fw-bold text-dark nama-text text-start">${nama || '-'}</span>
                            <button type="button" class="btn btn-outline-primary btn-sm btnCopyNama py-0 px-1 flex-shrink-0" data-nama="${nama || ''}" title="Salin Nama">
                                <i class="fas fa-copy" style="font-size: 0.7rem;"></i>
                            </button>
                        </div>
                    `
                },
                {
                    data: 'dbj_nama_bansos',
                    title: 'Program',
                    className: 'align-middle',
                    defaultContent: '-',
                    render: prog => `<span class="fw-semibold text-primary">${prog || '-'}</span>`
                },
                {
                    data: 'status',
                    title: 'Status',
                    className: 'align-middle',
                    render: s => {
                        const cls = { draft: 'secondary', diverifikasi: 'success' }[s] || 'secondary';
                        return `<span class="badge bg-${cls}">${(s || 'draft').toUpperCase()}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    title: 'Tanggal Dibuat',
                    className: 'align-middle',
                    render: d => d ? `<small class="text-muted">${new Date(d).toLocaleString('id-ID')}</small>` : '-'
                },
                {
                    data: null,
                    title: 'Dibuat Oleh',
                    className: 'align-middle',
                    render: (d, t, row) => renderCreatorWithWA(row)
                },
                {
                    data: 'id',
                    title: 'Aksi',
                    orderable: false,
                    className: 'align-middle',
                    render: function (id) {
                        let btn = `
                            <div class="d-flex flex-wrap gap-1 justify-content-md-center justify-content-end">
                                <button type="button" class="btn btn-danger btn-sm btnHapusUsulan" data-id="${id}">
                                    <i class="fas fa-trash-alt"></i> <span class="d-none d-md-inline">Hapus</span>
                                </button>
                        `;

                        if (userRole <= 3) {
                            btn += `
                                <button type="button" class="btn btn-success btn-sm btnVerifikasiUsulan" data-id="${id}">
                                    <i class="fas fa-check-circle"></i> <span class="d-none d-md-inline">Verify</span>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm btnTolakUsulan" data-id="${id}">
                                    <i class="fas fa-times-circle"></i> <span class="d-none d-md-inline">Tolak</span>
                                </button>
                            `;
                        }
                        btn += `</div>`;
                        return btn;
                    }
                }
            ],
            createdRow: function (row) {
                $(row).find('td').each(function (idx) {$(this).attr('data-label', labelsDraft[idx] || '');
                });
            },
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
        });


        /* ============================================================
        📊 DATATABLE — VERIFIED
        ============================================================ */

        tableVerified = $('#tableUsulanBansosVerified').DataTable({
            ajax: {
                url: '/usulan-bansos/data',
                type: 'GET',
                dataType: 'json',
                data: function (d) {
                    return Object.assign(d, getFilterParams('diverifikasi'));
                },
                dataSrc: json => json.data || []
            },

            pageLength: 10,
            responsive: false, // 🚀 Matikan collapse agar CSS Card bekerja di HP
            autoWidth: false,
            columnDefs: [], // 🚀 Kunci mencegah error targets parameter 3

            columns: [
                {
                    data: null,
                    title: 'No',
                    width: '40px',
                    className: 'text-center align-middle',
                    render: (d, t, r, m) => `<span class="badge bg-light text-dark border">${m.row + 1}</span>`
                },
                {
                    data: null,
                    title: 'NIK / No. KK',
                    className: 'align-middle',
                    render: function (data, type, row) {
                        let nik = row.nik ? row.nik : '-';
                        let noKk = row.no_kk ? row.no_kk : '-';

                        if (type === 'filter' || type === 'sort') return nik + ' ' + noKk;

                        let maskedNik = maskNumberJS(nik);
                        let maskedKk = maskNumberJS(noKk);

                        // 🚀 PERBAIKAN: d-flex align-items-center agar tombol berdampingan
                        // gap-3 untuk jarak antar NIK & KK dalam 1 baris
                        return `
                        <div class="d-flex flex-wrap align-items-center gap-3 w-100" style="font-size: 0.85rem;">
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-1">NIK:</small>
                                <span class="fw-bold nik-text me-1">${maskedNik}</span>
                                <button type="button" class="btn btn-outline-primary btn-sm btnCopyNIK py-0 px-1" data-nik="${nik}" title="Salin NIK">
                                    <i class="fas fa-copy" style="font-size: 0.7rem;"></i>
                                </button>
                            </div>
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-1">KK:</small>
                                <span class="fw-bold text-info nik-text me-1">${maskedKk}</span>
                                <button type="button" class="btn btn-outline-info btn-sm btnCopyNIK py-0 px-1" data-nik="${noKk}" title="Salin No. KK">
                                    <i class="fas fa-copy" style="font-size: 0.7rem;"></i>
                                </button>
                            </div>
                        </div>
                        `;
                    }
                },
                {
                    data: 'nama',
                    title: 'Nama',
                    className: 'text-md-nowrap align-middle',
                    render: nama => `
                        <!-- 🚀 PERBAIKAN: Tombol copy menempel di samping nama -->
                        <div class="d-flex align-items-center gap-2 w-100">
                            <span class="fw-bold text-dark nama-text text-start">${nama || '-'}</span>
                            <button type="button" class="btn btn-outline-primary btn-sm btnCopyNama py-0 px-1 flex-shrink-0" data-nama="${nama || ''}" title="Salin Nama">
                                <i class="fas fa-copy" style="font-size: 0.7rem;"></i>
                            </button>
                        </div>
                    `
                },
                {
                    data: 'dbj_nama_bansos',
                    title: 'Program',
                    className: 'align-middle',
                    defaultContent: '-',
                    render: prog => `<span class="fw-semibold text-success">${prog || '-'}</span>`
                },
                {
                    data: 'status',
                    title: 'Status',
                    className: 'align-middle',
                    render: s => {
                        const cls = { diverifikasi: 'success', ditolak: 'danger' }[s] || 'secondary';
                        return `<span class="badge bg-${cls}">${(s || '').toUpperCase()}</span>`;
                    }
                },
                {
                    data: 'updated_at',
                    title: 'Tanggal Diverifikasi',
                    className: 'align-middle',
                    render: d => d ? `<small class="text-muted">${new Date(d).toLocaleString('id-ID')}</small>` : '-'
                },
                {
                    data: 'updated_by_name',
                    title: 'Verifikator',
                    className: 'align-middle',
                    defaultContent: '-'
                }
            ],
            createdRow: function (row) {
                $(row).find('td').each(function (idx) {$(this).attr('data-label', labelsVerified[idx] || '');
                });
            },
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
        });

    }

    // Tombol Salin NIK
    $(document).on('click', '.btnCopyNIK', function () {
        const nik = $(this).data('nik');

        navigator.clipboard.writeText(nik)
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'NIK disalin',
                    text: `NIK ${nik} berhasil disalin ke clipboard`,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top'
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menyalin',
                    text: 'Clipboard tidak didukung oleh browser.',
                });
            });
    });

    // Tombol Salin Nama
    $(document).on('click', '.btnCopyNama', function () {
        const nama = $(this).data('nama');

        navigator.clipboard.writeText(nama)
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Nama disalin',
                    text: `Nama "${nama}" berhasil disalin ke clipboard`,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top'
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menyalin',
                    text: 'Clipboard tidak didukung oleh browser.'
                });
            });
    });

    /* ============================================================
    📩 BUILD WHATSAPP MESSAGE — FORMAL PEMERINTAHAN
    ============================================================ */

    function buildWaResponseMessage(row, alasan) {
        if (!row) return '';

        // Format tanggal created_at → "Desember 2025"
        const created = row.created_at ? new Date(row.created_at) : null;
        const periode = created
            ? created.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })
            : '-';
        
        const bansos = row.dbj_nama_bansos || '-';
        const nama = row.nama || '-';
        const nik = row.nik || '-';

        const message =
                `Assalamualaikum..
                Dengan ini kami informasikan bahwa Usulan Program *${bansos}* Periode *${periode}* atas nama *${nama}* (NIK ${nik}) tidak dapat diproses ke tahap verifikasi.

                Alasan: *${alasan}*.

                Demikian kami sampaikan. Atas perhatian dan pemahamannya, kami ucapkan terima kasih.

                > _SINDEN System_`;

        return encodeURIComponent(message);
    }

    function openWaResponse(row, alasan) {
        const rawNumber = row.nope || row.created_by_nope || ''; 
        const waNumber = formatWaNumber(rawNumber);
        if (!waNumber) {
            Swal.fire({
                icon: 'error',
                title: 'Nomor WA tidak valid',
                text: 'Nomor WhatsApp pemohon tidak ditemukan atau tidak valid.'
            });
            return;
        }

        const encoded = buildWaResponseMessage(row, alasan);
        const url = `https://wa.me/${waNumber}?text=${encoded}`;
        window.open(url, '_blank');
    }


    // ============================================================
    // === Tombol Tolak Usulan (Kirim WA + Delete Data + CSRF) ===
    // ============================================================

    $(document).on('click', '.btnTolakUsulan', function () {
        const id = $(this).data('id');
        const row = tableDraft.row($(this).closest('tr')).data();

        Swal.fire({
            title: 'Masukkan Alasan Penolakan',
            input: 'textarea',
            inputPlaceholder: 'Tuliskan alasan tidak dapat diverifikasi...',
            showCancelButton: true,
            confirmButtonText: 'Kirim WA',
            cancelButtonText: 'Batal',
            inputValidator: value => {
                if (!value) return 'Alasan wajib diisi.';
            }
        }).then(result => {
            if (!result.isConfirmed) return;

            const alasan = result.value;

            // 1. Kirim pesan WA
            openWaResponse(row, alasan);

            // 2. Ambil token CSRF dari input hidden
            const csrfName = $('#csrfToken').attr('name');
            const csrfHash = $('#csrfToken').val();

            // 3. Hapus usulan menggunakan AJAX + sertakan token
            $.ajax({
                url: `/usulan-bansos/delete/${id}`,
                type: 'POST',
                data: {
                    [csrfName]: csrfHash
                },
                success: function (res) {

                    // 4. Regenerasi token jika server mengirim token baru
                    if (res.csrfToken) {
                        $('#csrfToken').val(res.csrfToken);
                    }

                    tableDraft.ajax.reload(null, false);

                    // 🚀 Hilangkan tombol OK, gunakan timer otomatis
                    Swal.fire({
                        icon: 'success',
                        title: 'Usulan ditolak dan dihapus',
                        text: 'Pesan WA telah disiapkan, dan data usulan telah dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menghapus',
                        text: `Status ${xhr.status}: ${xhr.statusText}`
                    });
                }
            });
        });
    });

    /* ============================================================
       🗑 DELETE HANDLER
       ============================================================ */

    $(document).on('click', '.btnHapusUsulan', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data ini akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then(result => {

            if (!result.isConfirmed) return;

            $.ajax({
                url: '/usulan-bansos/delete/' + id,
                type: 'DELETE',
                dataType: 'json',
                success: res => {
                    if (res.success) {
                        // 🚀 Hilangkan tombol OK, gunakan timer otomatis
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        tableDraft.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: () => Swal.fire('Error', 'Gagal menghapus data.', 'error')
            });

        });
    });

    /* ============================================================
       ✔ VERIFIKASI HANDLER
       ============================================================ */

    $(document).on('click', '.btnVerifikasiUsulan', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Verifikasi Usulan',
            text: 'Apakah usulan ini ingin ditandai diverifikasi?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Verifikasi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745'
        }).then(result => {

            if (!result.isConfirmed) return;

            $.ajax({
                url: '/usulan-bansos/verifikasi/' + id,
                type: 'POST',
                dataType: 'json',
                success: res => {
                    if (res.success) {
                        // 🚀 Hilangkan tombol OK, gunakan timer otomatis
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        tableDraft.ajax.reload(null, false);
                        tableVerified.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: () => Swal.fire('Error', 'Tidak dapat memproses verifikasi.', 'error')
            });

        });
    });

    /* ============================================================
    🔄 RELOAD BUTTON HANDLER
    ============================================================ */

    $('#btnReloadDraft').on('click', function () {
        if ($.fn.DataTable.isDataTable('#tableUsulanBansosDraft')) {
            $('#tableUsulanBansosDraft').DataTable().ajax.reload(null, false);
            Swal.fire({
                icon: 'success',
                title: 'Data Draft Diperbarui',
                timer: 900,
                showConfirmButton: false
            });
        }
    });

    $('#btnReloadVerified').on('click', function () {
        if ($.fn.DataTable.isDataTable('#tableUsulanBansosVerified')) {
            $('#tableUsulanBansosVerified').DataTable().ajax.reload(null, false);
            Swal.fire({
                icon: 'success',
                title: 'Data Verified Diperbarui',
                timer: 900,
                showConfirmButton: false
            });
        }
    });

    /* ============================================================
    🔍 FILTER HANDLER (AUTO-RELOAD)
    ============================================================ */
    // 🚀 EVENT LISTENER: Tabel otomatis reload setiap kali ada dropdown yang diganti
    $('#filterProgram, #filterCreatedBy, #filterRW, #filterRT, #filterBulan, #filterTahun').on('change', function () {
        if (tableDraft) tableDraft.ajax.reload(null, false);
        if (tableVerified) tableVerified.ajax.reload(null, false);
    });

    // Tombol Apply (Opsional, dibiarkan jika masih ingin digunakan)
    $('#btnApplyFilter').on('click', function () {
        if (tableDraft) tableDraft.ajax.reload(null, false);
        if (tableVerified) tableVerified.ajax.reload(null, false);
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterProgram, #filterCreatedBy, #filterRW, #filterRT').val('');
        
        // 🚀 Kunci: Kembalikan ke bulan dan tahun saat ini, bukan dikosongkan
        setDefaultPeriode();

        if (tableDraft) tableDraft.ajax.reload(null, false);
        if (tableVerified) tableVerified.ajax.reload(null, false);
    });

    $('#filterRW').on('change', function () {
        const rw = $(this).val();

        // Reset RT dulu
        $('#filterRT').html('<option value="">-- RT --</option>');

        if (!rw) return;

        $.get(`/usulan-bansos/rt-by-rw/${rw}`, function(res) {
            if (!res || res.error) return;

            res.rt.forEach(item => {
                $('#filterRT').append(
                    `<option value="${item.rt}">RT ${item.rt}</option>`
                );
            });
        });
    });
    

});
