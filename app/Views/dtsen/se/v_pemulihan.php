<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<div class="content-wrapper mt-0">
    <?= $this->include('dtsen/se/layout_nav') ?>

    <section class="content">
        <div class="card shadow-sm border-danger">
            <div class="card-body">
                <div class="alert alert-danger shadow-sm border-0 mb-3 d-flex justify-content-between align-items-center">
                    <div d-flex justify-content-between align-items-center mb-3>
                        <i class="fas fa-tools me-2"></i>
                        <strong>Mode Pemulihan Aktif.</strong> Data di bawah memiliki anomali RT/RW (Kosong atau < 3 Digit).</div>
                            <div class="d-flex gap-2">
                                <button id="btnReloadPemulihan" class="btn btn-outline-danger btn-sm bg-white">
                                    <i class="fas fa-sync-alt"></i> Muat Ulang
                                </button>
                                <button type="button" class="btn btn-warning btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCariKK">
                                    <i class="fas fa-search-plus me-1"></i> Tarik Data Hilang
                                </button>
                            </div>

                    </div>

                    <table id="tablePemulihan" class="table table-striped table-hover nowrap w-100">
                        <thead class="text-center bg-danger text-white">
                            <tr>
                                <th>No.</th>
                                <th>Kepala Keluarga</th>
                                <th>No KK</th>
                                <th>Alamat</th>
                                <th>RT Lama</th>
                                <th>RW Lama</th>
                                <th>Aksi Pemulihan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
    </section>
</div>


<div class="modal fade" id="modalAutoFix" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning">
                <h6 class="modal-title fw-bold"><i class="fas fa-magic"></i> Auto-Fix Format (3 Digit)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAutoFix">
                    <input type="hidden" id="fix_id_rt" name="id_rt">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor RT <span class="text-muted fw-normal" id="label_rt_lama"></span></label>
                        <input type="text" class="form-control text-center fw-bold text-success" id="fix_rt" name="rt_baru" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor RW <span class="text-muted fw-normal" id="label_rw_lama"></span></label>
                        <input type="text" class="form-control text-center fw-bold text-success" id="fix_rw" name="rw_baru" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnSaveAutoFix" class="btn btn-primary btn-sm">
                    <i class="fas fa-save"></i> Simpan Perbaikan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCariKK" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title"><i class="fas fa-search"></i> Cari & Tarik Data Keluarga</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small fw-bold">Cari No. KK atau Nama Kepala Keluarga</label>
                <select id="selectKKGlobal" class="form-control" style="width: 100%"></select>
                <div class="alert alert-info mt-3 small">
                    <i class="fas fa-info-circle"></i> Gunakan fitur ini jika data tidak muncul di dashboard petugas namun Anda yakin data tersebut sudah ada di sistem.
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnProsesTarik" class="btn btn-primary btn-sm">
                    <i class="fas fa-file-import"></i> Tarik ke Pemulihan
                </button>
            </div>
        </div>
    </div>
</div>


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

        // 🚨 HANYA INISIALISASI DATATABLES PEMULIHAN DI FILE INI
        // ========================= 🚨 INITIALIZE TABLE PEMULIHAN =========================
        let tablePemulihan;
        if ($('#tablePemulihan').length) {
            tablePemulihan = $('#tablePemulihan').DataTable({
                ajax: {
                    url: '/dtsen-se/tabel_pemulihan', // ✅ UBAH KE SINI sesuai dengan Routes.php
                    type: 'POST',
                    dataSrc: 'data'
                },
                responsive: true,
                pageLength: 10,
                autoWidth: false,

                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },

                columns: [{
                        data: null,
                        render: (d, t, r, m) => m.row + 1,
                        className: 'text-center'
                    },
                    {
                        data: 'kepala_keluarga'
                    },
                    // 🔹 No KK
                    {
                        data: 'no_kk',
                        className: 'text-nowrap text-start',
                        render: function(noKK) {
                            if (!noKK) return '-';
                            // 🚀 Panggil fungsi penyensoran
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
                        data: 'alamat_rt',
                        render: (d, t, r) => d || r.alamat_kk || '-'
                    },
                    {
                        data: 'rt',
                        className: 'text-center fw-bold text-danger'
                    },
                    {
                        data: 'rw',
                        className: 'text-center fw-bold text-danger'
                    },
                    // ... kolom-kolom lainnya ...
                    {
                        data: null,
                        render: function(row) {
                            // 🛡️ Tarik session role_id langsung secara aman
                            let roleId = <?= (int) session()->get('role_id') ?>;

                            // Jika bukan Admin (role_id 4 atau 5), hanya tampilkan label
                            if (roleId > 3) {
                                return '<span class="badge bg-secondary opacity-75"><i class="fas fa-hourglass-half"></i> Menunggu Admin</span>';
                            }

                            // Logika tombol eksklusif untuk Admin (role_id 1, 2, 3)
                            let btnManual = `<a href="/pembaruan-keluarga/detail/${row.id_kk}" class="btn btn-outline-dark btn-sm me-1"><i class="fas fa-users-cog"></i> Manual</a>`;
                            let btnAuto = (row.rt && row.rw) ? `<button class="btn btn-warning btn-sm btnTriggerAutoFix" data-idrt="${row.id_rt}" data-rt="${row.rt}" data-rw="${row.rw}"><i class="fas fa-magic"></i> Auto-Fix</button>` : '';

                            return btnManual + btnAuto;
                        }
                    }
                ]
            });
        }

        // Inisialisasi Select2 Global
        $('#selectKKGlobal').select2({
            dropdownParent: $('#modalCariKK'),
            placeholder: 'Masukkan No. KK atau Nama...',
            minimumInputLength: 3,
            ajax: {
                url: '<?= base_url('dtsen-se/search_kk_select2') ?>',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        searchTerm: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        // Logika Tarik Data oleh Pentri
        $('#btnProsesTarik').on('click', function() {
            let id_kk = $('#selectKKGlobal').val();
            if (!id_kk) return Swal.fire('Pilih data!', '', 'warning');

            $.post('<?= base_url('dtsen-se/tarik_ke_pemulihan') ?>', {
                id_kk: id_kk
            }, function(res) {
                if (res.status) {
                    Swal.fire('Berhasil', 'Data sudah masuk ke daftar Pemulihan. Silakan lapor ke Admin untuk perbaikan wilayah.', 'success');
                    $('#modalCariKK').modal('hide');
                    tablePemulihan.ajax.reload(); // Refresh tabel agar muncul
                }
            });
        });

        // ========================= 🔄 TOMBOL MUAT ULANG =========================
        $('#btnReloadPemulihan').on('click', function() {
            if (typeof tablePemulihan !== 'undefined') {
                tablePemulihan.ajax.reload(null, false); // Reload tabel tanpa mereset pagination

                // Efek visual loading pada tombol
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
                setTimeout(() => {
                    btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Muat Ulang');
                }, 800);
            }
        });

        // ========================= 🪄 LOGIKA FRONTEND AUTO-FIX =========================
        $(document).on('click', '.btnTriggerAutoFix', function() {
            // ...
            let idRt = $(this).data('idrt');
            let rtLama = String($(this).data('rt'));
            let rwLama = String($(this).data('rw'));

            // Bersihkan spasi dulu dengan trim(), baru tambahkan 0 di depan
            let rtBaru = rtLama.trim().padStart(3, '0');
            let rwBaru = rwLama.trim().padStart(3, '0');

            // Isi ke Modal
            $('#fix_id_rt').val(idRt);
            $('#label_rt_lama').text(`(Lama: ${rtLama})`);
            $('#label_rw_lama').text(`(Lama: ${rwLama})`);

            $('#fix_rt').val(rtBaru);
            $('#fix_rw').val(rwBaru);

            // Tampilkan Modal
            $('#modalAutoFix').modal('show');
        });

        // Eksekusi Simpan
        $('#btnSaveAutoFix').on('click', function() {
            let formData = $('#formAutoFix').serialize();
            let btn = $(this);

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            // ... kode autofix lainnya ...
            $.post('/dtsen-se/autofix_rt_rw', formData, function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perbaikan');

                if (res.status) {
                    $('#modalAutoFix').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // ✅ Hanya reload tabel pemulihan saja
                    tablePemulihan.ajax.reload(null, false);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }, 'json');
        });

        // ========================= 📋 COPY NO KK =========================
        $(document).on('click', '.btnCopyNoKK', function() {
            const value = $(this).data('value');

            if (!value) return;

            navigator.clipboard.writeText(value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin',
                    text: 'No. KK berhasil disalin ke clipboard',
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
</script>

<?= $this->endSection(); ?>