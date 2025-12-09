/* ========================================================================
   📄 usulan_bansos.table.js
   Modul DataTable untuk Usulan Bansos
   ======================================================================== */

$(document).ready(function () {

    const userRole = window.userRole || 99;
    console.log("User Role:", userRole);

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

    /* ============================================================
       📊 DATATABLE — DRAFT
       ============================================================ */

    const tableDraft = $('#tableUsulanBansosDraft').DataTable({
        ajax: {
            url: '/usulan-bansos/data?status=draft',
            type: 'GET',
            dataType: 'json',
            dataSrc: json => json.data || []
        },
        columns: [
            { data: null, defaultContent: '' },

            {
                data: null,
                title: 'No',
                render: (d, t, r, m) => m.row + 1
            },

            {
                data: 'nik',
                title: 'NIK',
                render: nik => `
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="me-2 nik-text">${nik}</span>
                        <button class="btn btn-outline-primary btn-sm btnCopyNIK" 
                                data-nik="${nik}"
                                title="Salin NIK">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `
            },
            
            {
                data: 'nama',
                title: 'Nama',
                render: nama => `
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="nama-text">${nama}</span>
                        <button class="btn btn-outline-primary btn-sm btnCopyNama" 
                                data-nama="${nama}" 
                                title="Salin Nama">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `
            },

            {
                data: 'dbj_nama_bansos',
                title: 'Program',
                defaultContent: '-'
            },

            {
                data: 'status',
                title: 'Status',
                render: s => {
                    const cls = { draft: 'secondary', diverifikasi: 'success' }[s] || 'secondary';
                    return `<span class="badge bg-${cls}">${(s || 'draft').toUpperCase()}</span>`;
                }
            },

            {
                data: 'created_at',
                title: 'Tanggal Dibuat',
                render: d => d ? new Date(d).toLocaleString('id-ID') : '-'
            },

            {
                data: null,
                title: 'Dibuat Oleh',
                render: (d, t, row) => renderCreatorWithWA(row)
            },

            {
                data: 'id',
                title: 'Aksi',
                orderable: false,
                render: function (id, type, row) {
                        let btn = `
                            <button class="btn btn-danger btn-sm btnHapusUsulan" data-id="${id}">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                        `;

                        if (userRole <= 3) {
                            btn += `
                                <button class="btn btn-success btn-sm btnVerifikasiUsulan ms-1" data-id="${id}">
                                    <i class="fas fa-check-circle"></i> Verify
                                </button>

                                <button class="btn btn-warning btn-sm btnTolakUsulan ms-1" data-id="${id}">
                                    <i class="fas fa-times-circle"></i> Tolak
                                </button>
                            `;
                        }

                        return btn;
                    }

            }
        ],
        createdRow: row => $(row).find('td').css('text-align', 'left'),
        headerCallback: thead => $(thead).find('th').css('text-align', 'center')
    });

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
                    showConfirmButton: false
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
                    showConfirmButton: false
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

        const nama = row.nama || '-';
        const nik = row.nik || '-';

        const message =
`Assalamualaikum..
Dengan ini kami informasikan bahwa Usulan Bansos Periode *${periode}* atas nama *${nama}* (NIK ${nik}) tidak dapat diproses ke tahap verifikasi.

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


    /* ============================================================
       📊 DATATABLE — VERIFIED
       ============================================================ */

    const tableVerified = $('#tableUsulanBansosVerified').DataTable({
        ajax: {
            url: '/usulan-bansos/data?status=diverifikasi',
            type: 'GET',
            dataType: 'json',
            dataSrc: json => json.data || []
        },
        columns: [
            { data: null, defaultContent: '' },

            {
                data: null,
                title: 'No',
                render: (d, t, r, m) => m.row + 1
            },

             {
                data: 'nik',
                title: 'NIK',
                render: nik => `
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="me-2 nik-text">${nik}</span>
                        <button class="btn btn-outline-primary btn-sm btnCopyNIK"
                                data-nik="${nik}"
                                title="Salin NIK">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `
            },
            
            {
                data: 'nama',
                title: 'Nama',
                render: nama => `
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="nama-text">${nama}</span>
                        <button class="btn btn-outline-primary btn-sm btnCopyNama" 
                                data-nama="${nama}" 
                                title="Salin Nama">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `
            },

            {
                data: 'dbj_nama_bansos',
                title: 'Program',
                defaultContent: '-'
            },

            {
                data: 'status',
                title: 'Status',
                render: s => {
                    const cls = { diverifikasi: 'success', ditolak: 'danger' }[s] || 'secondary';
                    return `<span class="badge bg-${cls}">${(s || '').toUpperCase()}</span>`;
                }
            },

            {
                data: 'updated_at',
                title: 'Tanggal Diverifikasi',
                render: d => d ? new Date(d).toLocaleString('id-ID') : '-'
            },

            {
                data: 'updated_by_name',
                title: 'Verifikator',
                defaultContent: '-'
            }
        ],
        createdRow: row => $(row).find('td').css('text-align', 'left'),
        headerCallback: thead => $(thead).find('th').css('text-align', 'center')
    });

    // // ============================================================
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

                    Swal.fire({
                        icon: 'success',
                        title: 'Usulan ditolak dan dihapus',
                        text: 'Pesan WA telah disiapkan, dan data usulan telah dihapus.'
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
                        Swal.fire('Berhasil', res.message, 'success');
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
                        Swal.fire('Berhasil', res.message, 'success');
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

});
