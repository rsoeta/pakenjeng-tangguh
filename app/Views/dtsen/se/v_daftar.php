<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-0">
    <?= $this->include('dtsen/se/layout_nav') ?>

    <section class="content">
        <div class="card shadow-sm border-danger">
            <div class="card-body tab-content">
                <!-- ===================== TAB 1: DAFTAR KELUARGA ===================== -->
                <div class="tab-pane fade show active" id="tabDaftar" role="tabpanel">
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body py-3">

                            <!-- ================= HEADER: FILTER + ACTION ================= -->
                            <div class="row g-2 align-items-end">

                                <!-- ================= FILTER BAR ================= -->
                                <div class="col-lg">
                                    <div class="row g-2 align-items-end" id="filterBarKeluarga">

                                        <!-- 🔐 AKSES -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Akses</label>
                                            <div class="form-control form-control-sm bg-light text-nowrap" id="filterAkses">
                                                Akses Desa Penuh
                                            </div>
                                        </div>

                                        <!-- RW -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Wilayah (RW)</label>
                                            <select class="form-select form-select-sm" id="filterRW">
                                                <option value="">Semua RW</option>
                                            </select>
                                        </div>

                                        <!-- RT -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">RT</label>
                                            <select class="form-select form-select-sm" id="filterRT" disabled>
                                                <option value="">Semua RT</option>
                                            </select>
                                        </div>

                                        <!-- 📌 STATUS -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Status</label>
                                            <select class="form-select form-select-sm" id="filterStatus">
                                                <option value="">Semua Status</option>
                                                <option value="none">Belum Ada Pembaruan</option>
                                                <option value="draft">Draft</option>
                                                <option value="submitted">Submitted</option>
                                                <option value="verified">Verified</option>
                                            </select>
                                        </div>

                                        <!-- 📊 DESIL -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Desil</label>
                                            <select class="form-select form-select-sm" id="filterDesil">
                                                <option value="">Semua</option>
                                                <option value="belum">Belum</option>
                                                <?php for ($i = 0; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>">Desil <?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>

                                        <!-- 🔄 RESET -->
                                        <div class="col-auto">
                                            <button class="btn btn-outline-secondary btn-sm" id="btnResetFilter">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                                <!-- ================= ACTION BUTTON ================= -->
                                <div class="col-lg-auto ms-lg-auto">
                                    <div class="btn-group w-100" role="group">
                                        <!-- batasi akses hanya untuk role_id <4 -->
                                        <?php if ($role_id < 4): ?>
                                            <button id="btnSyncGlobal"
                                                class="btn btn-success btn-sm shadow-sm">
                                                <i class="fas fa-sync-alt me-1"></i> Sync Desil Nasional
                                            </button>
                                        <?php endif; ?>
                                        <button id="btnReloadKeluarga" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-sync-alt"></i> Muat Ulang
                                        </button>
                                        <button id="btnTambahKeluarga" class="btn btn-primary btn-sm">
                                            <i class="fas fa-user-plus"></i> Tambah Keluarga
                                        </button>
                                    </div>
                                </div>

                            </div>
                            <!-- ================= END HEADER ================= -->

                        </div>
                    </div>

                    <table id="tableKeluarga" class="table table-striped table-hover nowrap w-100">
                        <thead class="text-light bg-primary text-center">
                            <tr>
                                <th></th>
                                <th>No.</th>
                                <th>Kepala Keluarga</th>
                                <th>No KK</th>
                                <th>Alamat</th>
                                <th>Wilayah</th>
                                <th>Status</th> <!-- 🆕 -->
                                <th>Desil</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

...

<script>
    $(document).ready(function() {

        // =========================
        // 🔎 FILTER STATE (GLOBAL)
        // =========================
        const filterKeluarga = {
            rw: '',
            rt: '',
            status: '',
            desil: ''
        };

        function loadRW() {
            $.getJSON('/dtsen-se/list-rw', function(res) {
                const $rw = $('#filterRW');
                $rw.empty().append('<option value="">Semua RW</option>');

                res.data.forEach(rw => {
                    $rw.append(`<option value="${rw}">RW ${rw}</option>`);
                });
            });
        }

        loadRW();

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

        // ========================= 🔵 TABLE DAFTAR KELUARGA =========================
        const tableKeluarga = $('#tableKeluarga').DataTable({
            ajax: {
                url: '/dtsen-se/tabel_data',
                type: 'POST',
                data: function(d) {
                    d.filter = filterKeluarga;
                    console.log('📤 FILTER DIKIRIM:', d.filter);
                },
                dataSrc: 'data'
            },

            responsive: true,
            pageLength: 10,
            autoWidth: false,

            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },

            columns: [
                // 🔹 Responsive control
                {
                    data: null,
                    defaultContent: '',
                    className: 'control text-center',
                    orderable: false
                },

                // 🔹 No
                {
                    data: null,
                    render: (d, t, r, m) => m.row + 1,
                    className: 'text-center'
                },

                // 🔹 Kepala Keluarga
                {
                    data: 'kepala_keluarga',
                    className: 'text-start text-capitalize'
                },

                // 🔹 No KK
                {
                    data: 'no_kk',
                    className: 'text-nowrap text-start',
                    // 🚀 TAMBAHKAN PARAMETER 'type' DI SINI
                    render: function(data, type, row) {

                        // 1. KUNCI SAKTINYA DI SINI MBAH! 
                        // Jika DataTables sedang mencari (filter) atau mengurutkan (sort), berikan angka aslinya.
                        if (type === 'filter' || type === 'sort') {
                            return data;
                        }

                        // 🚀 Panggil fungsi penyensoran
                        let maskedData = maskNumberJS(data);

                        return `
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-semibold">${maskedData}</span>
                        
                        <button 
                            type="button"
                            class="btn btn-outline-secondary btn-xs btnCopyNoKK"
                            data-value="${data}" 
                            title="Salin No KK">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                `;
                    }
                },

                // 🔹 Alamat
                {
                    data: 'alamat',
                    className: 'text-start',
                    render: d => d || '-'
                },

                // 🔹 Wilayah
                {
                    data: null,
                    className: 'text-start',
                    render: r => `
                <span class="badge bg-light text-dark border">RW ${r.rw}</span>
                <span class="mx-1">/</span>
                <span class="badge bg-info text-dark">RT ${r.rt}</span>
            `
                },

                // 🔹 Status
                {
                    data: 'usulan_status',
                    className: 'text-start',
                    render: function(status, type, row) {

                        if (!status) {
                            return `<span class="badge bg-secondary">Belum Ada Pembaruan</span>`;
                        }

                        if (status === 'draft') {
                            if (row.is_submitted_ready == 1) {
                                return `<span class="badge bg-info text-dark">Submitted</span>`;
                            }
                            return `<span class="badge bg-warning text-dark">Draft</span>`;
                        }

                        if (status === 'submitted') {
                            return `<span class="badge bg-info text-dark">Submitted</span>`;
                        }

                        if (status === 'verified' || status === 'diverifikasi') {
                            return `<span class="badge bg-primary">Verified</span>`;
                        }

                        return `<span class="badge bg-secondary">Belum Ada Pembaruan</span>`;
                    }
                },

                // 🔹 Desil
                {
                    data: 'kategori_desil',
                    className: 'text-start',
                    render: d => {
                        // 🚀 PERBAIKAN: Pastikan 0 tidak terdeteksi sebagai "Belum"
                        if (d === null || d === '' || d === undefined) {
                            return '<span class="badge bg-secondary">Belum</span>';
                        }

                        const n = parseInt(d);
                        // 🚀 PERBAIKAN: Beri warna khusus (abu-abu/biru) untuk desil 0
                        let warna = 'danger';
                        if (n === 0) warna = 'secondary';
                        else if (n <= 3) warna = 'success';
                        else if (n <= 5) warna = 'warning';

                        return `<span class="badge bg-${warna}">${n}</span>`;
                    }
                },

                // 🔹 Aksi
                {
                    data: null,
                    className: 'text-start text-nowrap',
                    orderable: false,
                    searchable: false,
                    render: row => {

                        let btnInputDesil = '';

                        if (row.can_input_desil) {
                            btnInputDesil = `
                                <button class="btn btn-outline-primary btn-sm btnInputDesil me-1"
                                    data-id="${row.id_kk}"
                                    data-nama="${row.kepala_keluarga}"
                                    data-nokk="${row.no_kk}"
                                    data-alamat="${row.alamat}"
                                    data-desil="${row.kategori_desil ?? ''}">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </button>
                            `;
                        }

                        return `
                            <a href="/pembaruan-keluarga/detail/${row.id_kk}" 
                            class="btn btn-outline-dark btn-sm me-1">
                                <i class="fas fa-users-cog"></i>
                            </a>

                            ${btnInputDesil}

                            <button class="btn btn-outline-danger btn-sm btnDeleteKeluarga"
                                data-id="${row.id_kk}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        `;
                    }
                }
            ]
        });

        // ========================= 🎛 FILTER HANDLER =========================
        function reloadTableKeluarga() {
            tableKeluarga.ajax.reload();
        }

        // Wilayah / Akses
        $('#filterWilayah').on('change', function() {
            filterKeluarga.wilayah = $(this).val() || null;
            reloadTableKeluarga();
        });

        // RW
        const $filterRW = $('#filterRW');
        const $filterRT = $('#filterRT');

        /**
         * ===============================
         * 🔁 FETCH RT BY RW
         * ===============================
         */
        $(document).on('change', '#filterRW', function() {
            const rw = $(this).val();

            filterKeluarga.rw = rw || '';
            filterKeluarga.rt = '';

            const $rt = $('#filterRT');
            $rt.prop('disabled', !rw)
                .empty()
                .append('<option value="">Semua RT</option>');

            if (!rw) {
                $('#tableKeluarga').DataTable().ajax.reload();
                return;
            }

            $.getJSON(`/dtsen-se/list-rt/${rw}`, function(res) {
                res.data.forEach(rt => {
                    $rt.append(`<option value="${rt}">RT ${rt}</option>`);
                });
            });

            $('#tableKeluarga').DataTable().ajax.reload();
        });

        $(document).on('change', '#filterRT', function() {
            filterKeluarga.rt = $(this).val() || '';
            $('#tableKeluarga').DataTable().ajax.reload();
        });

        // RT
        $('#filterRT').on('change', function() {
            filterKeluarga.rt = $(this).val() || null;
            reloadTableKeluarga();
        });

        // Status
        $('#filterStatus').on('change', function() {
            filterKeluarga.status = $(this).val() || null;
            reloadTableKeluarga();
        });

        // Desil
        $('#filterDesil').on('change', function() {
            filterKeluarga.desil = $(this).val() || null;
            reloadTableKeluarga();
        });

        $('.filter-keluarga').on('change', function() {
            const key = $(this).data('filter');
            filterKeluarga[key] = $(this).val();

            console.log('🧠 FILTER UPDATE:', filterKeluarga);

            $('#tableKeluarga').DataTable().ajax.reload();
        });


        // Reset
        $('#btnResetFilter').on('click', function() {

            filterKeluarga.rw = '';
            filterKeluarga.rt = '';
            filterKeluarga.status = '';
            filterKeluarga.desil = '';

            // reset UI
            $('.filter-keluarga').val('');

            $('#tableKeluarga').DataTable().ajax.reload();
        });

        function reloadTableKeluarga() {
            $('#tableKeluarga').DataTable().ajax.reload(null, false);
        }

        /* ======================================================
         * 🔎 FILTER HANDLER — TABLE KELUARGA
         * ====================================================== */

        // Helper: ambil nilai filter (null jika kosong)
        function getFilterValue(selector) {
            const v = $(selector).val();
            return (v === '' || v === null) ? null : v;
        }

        // Inject filter ke AJAX DataTables
        function applyTableKeluargaFilter() {
            tableKeluarga.ajax.reload();
        }

        // Bind auto-submit on change
        $(document).on('change', `
            #filterWilayah,
            #filterRW,
            #filterRT,
            #filterStatus,
            #filterDesil
        `, function() {
            applyTableKeluargaFilter();
        });

        // Reset filter
        $(document).on('click', '#btnResetFilter', function() {
            $('#filterWilayah').val('');
            $('#filterRW').val('');
            $('#filterRT').val('');
            $('#filterStatus').val('');
            $('#filterDesil').val('');

            applyTableKeluargaFilter();
        });

        $('#filterRW').on('change', () => tableKeluarga.ajax.reload());
        $('#btnTambahKeluarga').on('click', () => window.location.href = '/pembaruan-keluarga/tambah');
        $('#btnReloadKeluarga').on('click', () => {
            tableKeluarga.ajax.reload(null, false);
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
            setTimeout(() => {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Muat Ulang');
            }, 800);
        });

        // ========================= 🔥 HAPUS KELUARGA =========================
        $(document).on('click', '.btnDeleteKeluarga', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Hapus Keluarga?',
                html: `
            <p class="mb-2">Sebutkan alasan penghapusan:</p>
            <textarea id="alasanHapus" class="form-control" rows="3" placeholder="Wajib diisi"></textarea>
        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                preConfirm: () => {
                    const alasan = document.getElementById('alasanHapus').value.trim();
                    if (!alasan) {
                        Swal.showValidationMessage('Alasan wajib diisi!');
                    }
                    return alasan;
                }
            }).then(result => {
                if (!result.isConfirmed) return;

                let alasan = result.value;

                $.post('/dtsen-se/delete', {
                        id_kk: id,
                        alasan: alasan
                    },
                    function(res) {
                        if (res.status) {
                            Swal.fire('Berhasil', res.message, 'success');
                            tableKeluarga.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    }, 'json'
                );
            });
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

    });

    document.addEventListener("DOMContentLoaded", function() {

        const btn = document.getElementById("btnSyncGlobal");
        if (!btn) return;

        btn.addEventListener("click", function() {

            Swal.fire({
                title: 'Sinkronisasi Global?',
                text: 'Proses ini akan membandingkan seluruh desil nasional dengan histori SINDEN.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Sinkronkan',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (!result.isConfirmed) return;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sync berjalan...';

                fetch('/pembaruan-keluarga/sync-desil-global', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {

                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync Desil Nasional';

                        if (res.status === 'cooldown') {

                            let seconds = res.remaining_seconds || 60;

                            Swal.fire({
                                icon: 'warning',
                                title: 'Cooldown Aktif',
                                html: `
                                    <div>
                                        Sinkronisasi hanya dapat dilakukan setiap 1 menit.<br><br>
                                        Silakan tunggu <b><span id="cooldownTimer">${seconds}</span></b> detik lagi.
                                    </div>
                                `,
                                showConfirmButton: false
                            });

                            const interval = setInterval(() => {

                                seconds--;

                                const timerEl = document.getElementById('cooldownTimer');
                                if (timerEl) timerEl.textContent = seconds;

                                if (seconds <= 0) {
                                    clearInterval(interval);
                                    Swal.close();
                                }

                            }, 1000);

                            return;
                        }

                        if (res.status === 'success') {

                            Swal.fire({
                                icon: 'success',
                                title: 'Sinkronisasi Selesai',
                                html: `
                                <div class="text-start small">
                                    <b>Periode:</b> ${res.periode}<br>
                                    <b>Total KK dicek:</b> ${res.total}<br>
                                    <b>Berubah:</b> <span class="text-danger">${res.changed}</span><br>
                                    <b>Tidak berubah:</b> <span class="text-success">${res.unchanged}</span>
                                </div>
                                `
                            });

                        } else {

                            Swal.fire(
                                'Gagal',
                                res.message || 'Terjadi kesalahan.',
                                'error'
                            );
                        }

                    })
                    .catch(err => {

                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Sync Desil Nasional';

                        Swal.fire(
                            'Error',
                            'Gagal menghubungi server.',
                            'error'
                        );
                    });

            });

        });

    });
</script>

<?= $this->endSection(); ?>