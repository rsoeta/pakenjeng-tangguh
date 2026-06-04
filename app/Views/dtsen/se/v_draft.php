<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-0">
    <?= $this->include('dtsen/se/layout_nav') ?>

    <section class="content">
        <div class="card shadow-sm border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <button id="btnReloadDraft" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-sync-alt"></i> Muat Ulang
                    </button>
                </div>

                <table id="tableDraftKeluarga" class="table table-striped table-hover nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th></th>
                            <th>No.</th>
                            <th>Kepala Keluarga</th>
                            <th>No KK</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>

            </div>
    </section>
</div>

...

<script>
    $(document).ready(function() {

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

        // ========================= 🟡 TABLE DRAFT PEMBARUAN =========================
        const tableDraft = $('#tableDraftKeluarga').DataTable({
            ajax: {
                url: '/pembaruan-keluarga/data?status=draft',
                type: 'GET',
                dataType: 'json',
                dataSrc: json => json.data || []
            },
            columns: [{
                    data: null,
                    defaultContent: ''
                },
                {
                    data: null,
                    render: (d, t, r, m) => m.row + 1
                },
                {
                    data: 'nama_kepala',
                    title: 'Kepala Keluarga'
                },
                {
                    data: 'no_kk_target',
                    className: 'text-nowrap',
                    render: function(noKK, type, row) {
                        if (!noKK) return '-';

                        // 🚀 KUNCI SAKTINYA DITARUH DI SINI MBAH!
                        // Kembalikan No KK asli khusus untuk mesin pencari (filter) dan pengurut (sort) DataTables
                        if (type === 'filter' || type === 'sort') {
                            return noKK;
                        }

                        // 🚀 Panggil fungsi penyensoran untuk tampilan (display)
                        let maskedKK = maskNumberJS(noKK);

                        return `
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold">${maskedKK}</span>
                                <button 
                                    type="button"
                                    class="btn btn-outline-secondary btn-xs btnCopyNoKK"
                                    data-value="${noKK}"
                                    title="Salin No KK">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        `;
                    }
                },
                {
                    data: 'status',
                    title: 'Status',
                    render: s => `<span class="badge bg-secondary">${(s || 'draft').toUpperCase()}</span>`
                },
                {
                    data: 'updated_at',
                    title: 'Tanggal Dibuat',
                    render: d => d ? new Date(d).toLocaleString('id-ID') : '-'
                },
                {
                    data: 'created_by_name',
                    title: 'Petugas',
                    defaultContent: '-'
                },
                {
                    data: 'id',
                    title: 'Aksi',
                    orderable: false,
                    render: function(id, type, row, meta) {
                        return `
                        <a href="/pembaruan-keluarga/lanjutkan/${id}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Lanjutkan
                        </a>
                        <button class="btn btn-danger btn-sm btnDeleteUsulan" data-id="${row.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                `;
                    }
                }
            ],
            responsive: true,
            pageLength: 10,
            createdRow: row => $(row).find('td').css('text-align', 'left'),
            headerCallback: thead => $(thead).find('th').css('text-align', 'center')
        });

        $('#btnReloadDraft').on('click', function() {
            tableDraft.ajax.reload(null, false);
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
            setTimeout(() => {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Muat Ulang');
            }, 800);
        });

        // ========================= 📋 COPY NO KK =========================
        $(document).on('click', '.btnCopyNoKK', function() {
            const value = $(this).data('value');

            if (!value) return;

            navigator.clipboard.writeText(value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin',
                    text: 'No. KK ' + value + ' berhasil disalin ke clipboard',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top'
                });
            }).catch(() => {
                Swal.fire('Gagal', 'Tidak dapat menyalin No. KK', 'error');
            });
        });

        // ========================= 🔥 HAPUS DRAFT USULAN =========================
        $(document).on('click', '.btnDeleteUsulan', function() {
            let idUsulan = $(this).data('id');

            Swal.fire({
                title: 'Hapus Draft?',
                html: 'Tindakan ini akan menghapus data secara <b>permanen</b> dan tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true, // Tombol Ya di kanan
                width: '320px', // 📱 Mobile friendly: perkecil ukuran pop-up
                customClass: {
                    title: 'fs-5',
                    htmlContainer: 'fs-6 text-muted'
                }
            }).then((result) => {
                if (result.isConfirmed) {

                    // 🚀 Munculkan loading agar tidak diklik berkali-kali
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        width: '300px',
                        didOpen: () => Swal.showLoading()
                    });

                    // 🚀 Kirim request POST standar (bukan JSON murni)
                    $.ajax({
                        // 💡 PERBAIKAN: Hapus "baseUrl +" karena tidak terdefinisi di view ini
                        url: '/pembaruan-keluarga/delete-keluarga',
                        type: 'POST',
                        data: {
                            id: idUsulan
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false,
                                    width: '300px'
                                });
                                // 💡 Sesuaikan dengan nama variabel tabel di file masing-masing:
                                // Gunakan tableDraft.ajax.reload(null, false); untuk v_draft.php
                                // Gunakan tableSubmitted.ajax.reload(null, false); untuk v_submitted.php
                                if (typeof tableDraft !== 'undefined') tableDraft.ajax.reload(null, false);
                                if (typeof tableSubmitted !== 'undefined') tableSubmitted.ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message,
                                    width: '320px'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error Server',
                                text: 'Terjadi kesalahan saat menghubungi server.',
                                width: '320px'
                            });
                        }
                    });
                }
            });
        });

    });
</script>

<?= $this->endSection(); ?>