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

                                        <!-- 🛡️ PERLINSOS -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Perlinsos</label>
                                            <select class="form-select form-select-sm" id="filterPerlinsos">
                                                <option value="">Semua</option>
                                                <option value="terverifikasi">Terverifikasi</option>
                                                <option value="belum_verifikasi">Belum Verifikasi</option>
                                                <option value="none">Belum Di-entri</option>
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

                                        <!-- 🎁 BANSOS -->
                                        <div class="col-auto">
                                            <label class="form-label small fw-bold mb-1">Bansos</label>
                                            <select class="form-select form-select-sm" id="filterBansos">
                                                <option value="">Semua</option>
                                                <option value="PKH">PKH</option>
                                                <option value="SEMBAKO">SEMBAKO</option>
                                                <option value="PKH + SEMBAKO">PKH + SEMBAKO</option>
                                                <option value="none">Tidak Ada</option>
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
                                    <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center">
                                        <!-- batasi akses hanya untuk role_id <4 -->
                                        <?php if ($role_id < 4): ?>
                                            <button id="btnSyncGlobal"
                                                class="btn btn-outline-success btn-sm shadow-sm"
                                                title="Sinkronisasi seluruh desil nasional dengan histori SINDEN berdasarkan periode rilis resmi">
                                                <i class="fas fa-cloud-download-alt me-1"></i> Sync Desil Nasional
                                            </button>
                                        <?php endif; ?>
                                        <div class="btn-group" role="group">
                                            <button id="btnReloadKeluarga" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-sync-alt"></i> Muat Ulang
                                            </button>
                                            <button id="btnTambahKeluarga" class="btn btn-primary btn-sm">
                                                <i class="fas fa-user-plus"></i> Tambah Keluarga
                                            </button>
                                        </div>
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
                                <th>Bansos</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- 🚀 KUNCI SAKTI: Panggil modal dari file terpisah agar dirender ke halaman ini -->
<?= $this->include('dtsen/se/modal_input_desil') ?>

<script>
    $(document).ready(function() {

        // =========================
        // 🔎 FILTER STATE (GLOBAL)
        // =========================
        const filterKeluarga = {
            rw: '',
            rt: '',
            status: '',
            perlinsos: '',
            desil: '',
            bansos: ''
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
            stateSave: false,

            ajax: {
                url: '/dtsen-se/tabel_data',
                type: 'POST', // 🚀 KEMBALIKAN KE POST SESUAI ROUTE CI4 JENDERAL
                cache: false,
                data: function(d) {
                    // Bungkus kembali ke dalam variabel filter agar rapi
                    d.filter = filterKeluarga;

                    console.log('📤 Minta Data (POST) dengan Filter:', d.filter);
                },
                dataSrc: 'data'
            },

            responsive: true,
            pageLength: 10,
            autoWidth: false,
            order: [], // 🚀 Jangan paksa sort di kolom No

            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },

            // 🚀 KUNCI NOMOR URUT AGAR TIDAK TERPENGARUH SORTING KOLOM LAIN
            drawCallback: function(settings) {
                var api = this.api();
                var info = api.page.info();
                api.column(1, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = info.start + i + 1;
                });
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
                    defaultContent: '',
                    orderable: false,
                    searchable: false
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
                    className: 'text-start text-nowrap',
                    render: function(status, type, row) {

                        // 1. Status Pembaruan
                        let pembaruanBadge = '<span class="badge bg-secondary">Belum Ada Pembaruan</span>';
                        let pembaruanText = 'Belum Ada Pembaruan';

                        if (status === 'draft' || status === 'submitted') {
                            if (row.is_submitted_ready == 1) {
                                pembaruanBadge = '<span class="badge bg-info text-dark">Submitted</span>';
                                pembaruanText = 'Submitted';
                            } else {
                                pembaruanBadge = '<span class="badge bg-warning text-dark">Draft</span>';
                                pembaruanText = 'Draft';
                            }
                        } else if (status === 'verified' || status === 'diverifikasi') {
                            pembaruanBadge = '<span class="badge bg-primary">Verified</span>';
                            pembaruanText = 'Verified';
                        }

                        // 2. Status Perlinsos
                        let perlinsosBadge = '<span class="text-muted fw-bold">-</span>';
                        let perlinsosText = '-';

                        if (row.perlinsos_status) {
                            if (row.perlinsos_status === 'tersinkron') {
                                perlinsosBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Terverifikasi</span>';
                                perlinsosText = 'Terverifikasi';
                            } else {
                                perlinsosBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Belum Verifikasi</span>';
                                perlinsosText = 'Belum Verifikasi';
                            }
                        }

                        if (type === 'filter' || type === 'sort') {
                            return 'Pembaruan: ' + pembaruanText + ' Perlinsos: ' + perlinsosText;
                        }

                        return `
                            <div class="d-flex flex-column" style="gap: 3px;">
                                <div>
                                    <span class="small text-muted fw-bold me-1">Pembaruan:</span>${pembaruanBadge}
                                </div>
                                <div>
                                    <span class="small text-muted fw-bold me-1">Perlinsos:</span>${perlinsosBadge}
                                </div>
                            </div>
                        `;
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

                // 🔹 Bansos (Dokumentasi Bansos Tahap Terakhir)
                {
                    data: 'bansos',
                    render: function(data, type, row) {
                        if (!data || data === '-' || data === '') {
                            return '<span class="text-muted">-</span>';
                        }

                        if (type === 'filter') {
                            return data + (row.bansos_tahap ? ' ' + row.bansos_tahap : '');
                        }

                        if (type === 'sort') {
                            return data;
                        }

                        let tahapTooltip = row.bansos_tahap ? ` title="${row.bansos_tahap}"` : '';
                        let badgeClass = 'bg-primary';

                        if (data === 'PKH') {
                            badgeClass = 'bg-success';
                        } else if (data === 'SEMBAKO') {
                            badgeClass = 'bg-info text-dark';
                        } else if (data.indexOf('+') !== -1 || data.indexOf(',') !== -1) {
                            badgeClass = 'bg-primary';
                        }

                        return `<span class="badge ${badgeClass} shadow-sm"${tahapTooltip}>${data}</span>`;
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
                                    data-desil="${row.kategori_desil ?? ''}"
                                    title="Update Desil">
                                    <i class="fas fa-balance-scale"></i>
                                </button>
                            `;
                        }

                        const detailId = row.id_kk_encoded || row.id_kk;
                        return `
                            <a href="/pembaruan-keluarga/detail/${detailId}" 
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
                },
                // 🔹 Kolom Pencarian Tersembunyi (Anggota Keluarga)
                {
                    data: 'anggota_search',
                    visible: false, // 🚀 Sembunyikan dari tampilan tabel utama
                    searchable: true, // 🚀 Tetap dilacak oleh fitur Search DataTables
                    className: 'never', // 🚀 DataTables Responsive: jangan pernah tampilkan di child row
                    defaultContent: '',
                    render: function(data, type) {
                        // Hanya berikan data saat DataTables melakukan filtering/searching
                        if (type === 'filter') {
                            return data || '';
                        }
                        return '';
                    }
                }
            ]
        });

        // ==============================================================
        // 🎛️ SMART FILTER HANDLER (ANTI-GAGAL / EVENT DELEGATION)
        // ==============================================================

        // 1. Dengarkan perubahan pada semua filter dari akar dokumen
        $(document).on('change', '#filterRW, #filterRT, #filterStatus, #filterPerlinsos, #filterDesil, #filterBansos', function() {
            // Update parameter filter
            filterKeluarga.rw = $('#filterRW').val() || '';
            filterKeluarga.rt = $('#filterRT').val() || '';
            filterKeluarga.status = $('#filterStatus').val() || '';
            filterKeluarga.perlinsos = $('#filterPerlinsos').val() || '';
            filterKeluarga.desil = $('#filterDesil').val() || '';
            filterKeluarga.bansos = $('#filterBansos').val() || '';

            console.log('🎯 Minta Data:', filterKeluarga); // Pantau di Inspect Element -> Console

            // PAKSA reload langsung tembak ke instance tabel (Bypass masalah scope variabel)
            $('#tableKeluarga').DataTable().ajax.reload(null, false);
        });

        // 2. Logika khusus RW (Tarik data RT dari Server)
        $(document).on('change', '#filterRW', function() {
            const rw = $(this).val();
            const $rt = $('#filterRT');

            // Reset UI RT
            $rt.prop('disabled', !rw).html('<option value="">Semua RT</option>');

            if (rw) {
                $rt.append('<option value="" disabled>Memuat...</option>');
                $.getJSON('/dtsen-se/list-rt/' + rw, function(res) {
                    $rt.html('<option value="">Semua RT</option>');
                    if (res.data) {
                        res.data.forEach(rt => {
                            $rt.append(`<option value="${rt}">RT ${rt}</option>`);
                        });
                    }
                });
            }
        });

        // 3. Tombol Reset
        $(document).on('click', '#btnResetFilter', function() {
            // Reset UI
            $('#filterRW, #filterStatus, #filterPerlinsos, #filterDesil, #filterBansos').val('');
            $('#filterRT').html('<option value="">Semua RT</option>').prop('disabled', true);

            // Reset Parameter
            filterKeluarga.rw = '';
            filterKeluarga.rt = '';
            filterKeluarga.status = '';
            filterKeluarga.perlinsos = '';
            filterKeluarga.desil = '';
            filterKeluarga.bansos = '';

            // Tembak Reload
            $('#tableKeluarga').DataTable().ajax.reload(null, false);
        });

        // 4. Tombol Muat Ulang & Tambah
        $(document).on('click', '#btnReloadKeluarga', function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');

            $('#tableKeluarga').DataTable().ajax.reload(null, false);

            setTimeout(() => {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Muat Ulang');
            }, 800);
        });

        $(document).on('click', '#btnTambahKeluarga', function() {
            window.location.href = '/pembaruan-keluarga/tambah';
        });

        // ==============================================================
        // 🚀 EVENT: BUKA MODAL UPDATE DESIL DARI TABEL DAFTAR KELUARGA
        // ==============================================================
        $(document).on('click', '.btnInputDesil', function(e) {
            e.preventDefault(); // Mencegah aksi bawaan tombol

            // Ambil data dari atribut tombol
            const id = $(this).attr('data-id');
            const nama = $(this).attr('data-nama');
            const nokk = $(this).attr('data-nokk');
            const alamat = $(this).attr('data-alamat');
            const desil = $(this).attr('data-desil');

            // Temukan modal
            const modal = $('#modalInputDesil');

            // Suntikkan data ke dalam form modal
            modal.find('#modal_id_kk').val(id);
            modal.find('#modal_no_kk').val(nokk);
            modal.find('#modal_kepala_keluarga').val(nama);
            modal.find('#modal_alamat').val(alamat);

            // 🚀 PERBAIKAN: Logika Pengisian Radio Button (Pills)
            modal.find('input[name="kategori_desil"]').prop('checked', false); // Bersihkan semua pil
            if (desil && desil.trim() !== '') {
                modal.find('input[name="kategori_desil"][value="' + desil.trim() + '"]').prop('checked', true);
            }

            // 🚀 Otomatis set form Periode ke Triwulan Berjalan saat modal dibuka
            const d = new Date();
            const currentYear = d.getFullYear();
            const currentMonth = d.getMonth() + 1;
            const currentTw = Math.ceil(currentMonth / 3);

            modal.find('#tahun_berlaku').val(currentYear);
            modal.find('#triwulan_berlaku').val(currentTw);

            // Tampilkan modal ke layar menggunakan method Bootstrap 5 via jQuery
            modal.modal('show');
        });

        // ==============================================================
        // 🚀 EVENT: SUBMIT FORM UPDATE DESIL (VIA AJAX)
        // ==============================================================
        $('#formInputDesil').on('submit', function(e) {
            e.preventDefault(); // 🛑 CEGAH BROWSER PINDAH HALAMAN!

            const form = $(this);
            const btnSubmit = form.find('button[type="submit"]');
            const originalText = btnSubmit.html();

            // Ubah tombol jadi status loading agar tidak diklik dua kali
            btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(res) {
                    // Kembalikan kondisi tombol
                    btnSubmit.prop('disabled', false).html(originalText);

                    if (res.status === 'success') {
                        // Tampilkan notifikasi sukses elegan
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Kategori desil berhasil diperbarui.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Sembunyikan modal
                        $('#modalInputDesil').modal('hide');

                        // 🚀 REFRESH TABEL TANPA RELOAD HALAMAN
                        tableKeluarga.ajax.reload(null, false);
                    } else {
                        // Jika ada validasi error dari server
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal memperbarui desil.'
                        });
                    }
                },
                error: function(xhr) {
                    // Kembalikan kondisi tombol jika server down/error 500
                    btnSubmit.prop('disabled', false).html(originalText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Gagal terhubung ke server atau terjadi kesalahan sistem.'
                    });
                }
            });
        });

        // ... (Kode selanjutnya) ...
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
            const currentYear = new Date().getFullYear();
            let yearOptions = '';
            for (let y = currentYear; y >= 2024; y--) {
                yearOptions += `<option value="${y}" ${y === currentYear ? 'selected' : ''}>${y}</option>`;
            }

            Swal.fire({
                title: 'Sync Desil Nasional',
                html: `
                    <div class="text-start small">
                        <div class="alert alert-info py-2 px-3 mb-3 small">
                            <i class="fas fa-info-circle me-1"></i>
                            Pilih <b>Tahun</b> dan <b>Triwulan Rilis Resmi Kemensos</b> yang akan dicatat sebagai histori untuk setiap keluarga.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small mb-1">Tahun Rilis Kemensos:</label>
                            <select id="swalSyncTahun" class="form-select form-select-sm">
                                ${yearOptions}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small mb-1">Periode Triwulan:</label>
                            <select id="swalSyncTriwulan" class="form-select form-select-sm">
                                <option value="1">Triwulan 1 (TW1)</option>
                                <option value="2">Triwulan 2 (TW2)</option>
                                <option value="3" selected>Triwulan 3 (TW3)</option>
                                <option value="4">Triwulan 4 (TW4)</option>
                            </select>
                            <input type="text" id="swalSyncTriwulanCustom" class="form-control form-control-sm mt-2 d-none" placeholder="Contoh: 3.1" inputmode="decimal">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" id="swalCustomTwCheck">
                                <label class="form-check-label text-muted" for="swalCustomTwCheck" style="font-size: 0.78rem;">
                                    Gunakan triwulan desimal/revisi (misal: 3.1)
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-warning py-2 px-3 mb-0 small">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <b>Peringatan:</b> Sistem akan membandingkan desil nasional seluruh KK dan mencatat perubahannya ke periode yang Anda tentukan.
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-sync-alt me-1"></i> Mulai Sinkronisasi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,
                didOpen: () => {
                    const check = document.getElementById('swalCustomTwCheck');
                    const select = document.getElementById('swalSyncTriwulan');
                    const customInput = document.getElementById('swalSyncTriwulanCustom');
                    if (check && select && customInput) {
                        check.addEventListener('change', function() {
                            if (this.checked) {
                                select.classList.add('d-none');
                                customInput.classList.remove('d-none');
                                customInput.focus();
                            } else {
                                select.classList.remove('d-none');
                                customInput.classList.add('d-none');
                            }
                        });
                    }
                },
                preConfirm: () => {
                    const tahun = document.getElementById('swalSyncTahun').value;
                    const isCustom = document.getElementById('swalCustomTwCheck').checked;
                    let triwulan = isCustom ?
                        document.getElementById('swalSyncTriwulanCustom').value.trim() :
                        document.getElementById('swalSyncTriwulan').value;

                    if (!tahun) {
                        Swal.showValidationMessage('Tahun harus dipilih.');
                        return false;
                    }
                    if (!triwulan || isNaN(triwulan) || parseFloat(triwulan) <= 0 || parseFloat(triwulan) > 4.9) {
                        Swal.showValidationMessage('Periode triwulan tidak valid (harus angka 1 - 4.9).');
                        return false;
                    }

                    return {
                        tahun: tahun,
                        triwulan: triwulan
                    };
                }
            }).then((result) => {

                if (!result.isConfirmed || !result.value) return;

                const {
                    tahun,
                    triwulan
                } = result.value;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sync berjalan...';

                const formData = new FormData();
                formData.append('tahun', tahun);
                formData.append('triwulan', triwulan);

                fetch('/pembaruan-keluarga/sync-desil-global', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {

                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-cloud-download-alt me-1"></i> Sync Desil Nasional';

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
                                    <b>Periode Target:</b> ${res.periode}<br>
                                    <b>Total KK dicek:</b> ${res.total}<br>
                                    <b>Berubah & Dicatat:</b> <span class="text-danger fw-bold">${res.changed}</span><br>
                                    <b>Tidak berubah:</b> <span class="text-success">${res.unchanged}</span>
                                </div>
                                `
                            });

                            if ($.fn.DataTable.isDataTable('#tableKeluarga')) {
                                $('#tableKeluarga').DataTable().ajax.reload(null, false);
                            }

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
                        btn.innerHTML = '<i class="fas fa-cloud-download-alt me-1"></i> Sync Desil Nasional';

                        Swal.fire(
                            'Error',
                            'Gagal menghubungi server.',
                            'error'
                        );
                    });

            });

        });

    });

    // // ==============================================================
    // // 🎛️ SMART FILTER (API MURNI)
    // // ==============================================================

    // // Buat fungsi sapu jagat untuk membaca ulang semua nilai dropdown
    // function eksekusiFilter() {
    //     filterKeluarga.rw = $('#filterRW').val() || '';
    //     filterKeluarga.rt = $('#filterRT').val() || '';
    //     filterKeluarga.status = $('#filterStatus').val() || '';
    //     filterKeluarga.desil = $('#filterDesil').val() || '';

    //     console.log("🛠️ Memerintahkan Tabel Update...", filterKeluarga);

    //     // Perintah API Murni DataTables: reload dan kembalikan ke halaman 1
    //     tableKeluarga.ajax.reload(null, true);
    // }

    // // Tautkan fungsi di atas ke SEMUA dropdown filter
    // $('#filterRT, #filterStatus, #filterDesil').on('change', function() {
    //     eksekusiFilter();
    // });

    // // Khusus RW (Karena harus tarik data RT)
    // $('#filterRW').on('change', function() {
    //     const rw = $(this).val();
    //     const $rt = $('#filterRT');

    //     // Set state filter sementara dan reload
    //     filterKeluarga.rw = rw || '';
    //     filterKeluarga.rt = '';
    //     $rt.prop('disabled', !rw).html('<option value="">Semua RT</option>');

    //     eksekusiFilter();

    //     // Tarik data RT jika RW tidak kosong
    //     if (rw) {
    //         $rt.append('<option value="" disabled>Memuat...</option>');
    //         $.getJSON('/dtsen-se/list-rt/' + rw, function(res) {
    //             $rt.html('<option value="">Semua RT</option>');
    //             if (res.data) {
    //                 res.data.forEach(rt => {
    //                     $rt.append(`<option value="${rt}">RT ${rt}</option>`);
    //                 });
    //             }
    //         });
    //     }
    // });

    // // Tombol Reset
    // $('#btnResetFilter').on('click', function() {
    //     $('#filterRW, #filterStatus, #filterDesil').val('');
    //     $('#filterRT').html('<option value="">Semua RT</option>').prop('disabled', true);
    //     eksekusiFilter();
    // });
</script>

<?= $this->endSection(); ?>