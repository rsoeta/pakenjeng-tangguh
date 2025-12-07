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
                `Assalamualaikum, saya ingin menanyakan mengenai usulan bansos (ID: ${row.id}).`
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

            { data: 'nik', title: 'NIK' },
            { data: 'nama', title: 'Nama' },

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
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;

                    if (userRole <= 3) {
                        btn += `
                            <button class="btn btn-success btn-sm btnVerifikasiUsulan ms-1" data-id="${id}">
                                <i class="fas fa-check-circle"></i> Verifikasi
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

            { data: 'nik', title: 'NIK' },
            { data: 'nama', title: 'Nama' },

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
