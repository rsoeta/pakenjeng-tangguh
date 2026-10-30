<?php
$init_kategori = $init_kategori ?? (request()->getGet('kategori') ?? '');
$init_rw       = $init_rw ?? (request()->getGet('rw') ?? '');
$init_rt       = $init_rt ?? (request()->getGet('rt') ?? '');
?>
<?= $this->extend('templates/index'); ?>
<?= $this->section('content'); ?>

<style>
    .card-stat {
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
    }

    .card-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08);
    }

    .card-stat .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .badge-permanen {
        background-color: #169B88 !important;
        color: #fff !important;
    }

    .badge-semi {
        background-color: #3D82A9 !important;
        color: #fff !important;
    }

    .badge-panggung {
        background-color: #9EAF22 !important;
        color: #fff !important;
    }

    .badge-numpang {
        background-color: #D84F2A !important;
        color: #fff !important;
    }

    .badge-belum {
        background-color: #F59100 !important;
        color: #fff !important;
    }

    .border-permanen {
        border-left: 4px solid #169B88 !important;
    }

    .border-semi {
        border-left: 4px solid #3D82A9 !important;
    }

    .border-panggung {
        border-left: 4px solid #9EAF22 !important;
    }

    .border-numpang {
        border-left: 4px solid #D84F2A !important;
    }

    .border-belum {
        border-left: 4px solid #F59100 !important;
    }

    /* 🔧 Hilangkan jarak jomplang antara navbar dan content-header */
    .content-wrapper {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .content-header {
        padding-top: 0.25rem !important;
        padding-bottom: 0.25rem !important;
    }
</style>

<div class="content-wrapper mt-0 pt-0 px-2 px-md-3 pb-3">
    <?= view('dtsen/keadaan_rumah/layout_nav'); ?>

    <!-- ========================================== -->
    <!-- 1. KARTU STATISTIK RINGKASAN              -->
    <!-- ========================================== -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card card-stat shadow-sm border-start border-4 border-primary bg-white h-100 p-2">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total KK</div>
                        <div class="fs-5 fw-bold text-dark" id="statTotal"><?= number_format($summary['total'], 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
            <div class="card card-stat shadow-sm border-permanen bg-white h-100 p-2 btn-stat-filter" data-kategori="Permanen" style="cursor: pointer;" title="Filter Permanen">
                <div class="d-flex align-items-center">
                    <div class="stat-icon text-white me-2" style="background-color: #169B88;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Permanen</div>
                        <div class="fs-5 fw-bold" style="color: #169B88;" id="statPermanen"><?= number_format($summary['permanen']['count'], 0, ',', '.') ?> <span class="small fw-normal text-muted">(<?= $summary['permanen']['percent'] ?>%)</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
            <div class="card card-stat shadow-sm border-semi bg-white h-100 p-2 btn-stat-filter" data-kategori="Semi Permanen" style="cursor: pointer;" title="Filter Semi Permanen">
                <div class="d-flex align-items-center">
                    <div class="stat-icon text-white me-2" style="background-color: #3D82A9;">
                        <i class="fas fa-home"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Semi Perm.</div>
                        <div class="fs-5 fw-bold" style="color: #3D82A9;" id="statSemi"><?= number_format($summary['semi_permanen']['count'], 0, ',', '.') ?> <span class="small fw-normal text-muted">(<?= $summary['semi_permanen']['percent'] ?>%)</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
            <div class="card card-stat shadow-sm border-panggung bg-white h-100 p-2 btn-stat-filter" data-kategori="Panggung" style="cursor: pointer;" title="Filter Panggung">
                <div class="d-flex align-items-center">
                    <div class="stat-icon text-white me-2" style="background-color: #9EAF22;">
                        <i class="fas fa-campground"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Panggung</div>
                        <div class="fs-5 fw-bold" style="color: #9EAF22;" id="statPanggung"><?= number_format($summary['panggung']['count'], 0, ',', '.') ?> <span class="small fw-normal text-muted">(<?= $summary['panggung']['percent'] ?>%)</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
            <div class="card card-stat shadow-sm border-numpang bg-white h-100 p-2 btn-stat-filter" data-kategori="Numpang" style="cursor: pointer;" title="Filter Numpang">
                <div class="d-flex align-items-center">
                    <div class="stat-icon text-white me-2" style="background-color: #D84F2A;">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Numpang</div>
                        <div class="fs-5 fw-bold" style="color: #D84F2A;" id="statNumpang"><?= number_format($summary['numpang']['count'], 0, ',', '.') ?> <span class="small fw-normal text-muted">(<?= $summary['numpang']['percent'] ?>%)</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
            <div class="card card-stat shadow-sm border-belum bg-white h-100 p-2 btn-stat-filter" data-kategori="belum_dinilai" style="cursor: pointer;" title="Filter Belum Dinilai">
                <div class="d-flex align-items-center">
                    <div class="stat-icon text-white me-2" style="background-color: #F59100;">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Belum Dinilai</div>
                        <div class="fs-5 fw-bold" style="color: #F59100;" id="statBelum"><?= number_format($summary['belum_dinilai']['count'], 0, ',', '.') ?> <span class="small fw-normal text-muted">(<?= $summary['belum_dinilai']['percent'] ?>%)</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. TOOLBAR FILTER & TOMBOL AKSI           -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <!-- Filter RW -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold mb-1">Filter RW</label>
                    <select class="form-select form-select-sm" id="filter_rw">
                        <option value="all"><?= ($user_login['role_id'] ?? 0) >= 4 ? 'Semua RW Tugas' : 'Semua RW' ?></option>
                        <?php foreach ($list_rw as $rw): ?>
                            <option value="<?= esc($rw) ?>" <?= ($init_rw == $rw) ? 'selected' : '' ?>>RW <?= esc($rw) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter RT -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold mb-1">Filter RT</label>
                    <select class="form-select form-select-sm" id="filter_rt" <?= empty($init_rw) ? 'disabled' : '' ?>>
                        <option value="all"><?= ($user_login['role_id'] ?? 0) >= 4 ? 'Semua RT Tugas' : 'Semua RT' ?></option>
                    </select>
                </div>

                <!-- Filter Kategori Rumah -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold mb-1">Kategori Keadaan Rumah</label>
                    <select class="form-select form-select-sm" id="filter_kategori">
                        <option value="all" <?= (empty($init_kategori) || $init_kategori === 'all') ? 'selected' : '' ?>>Semua Kategori</option>
                        <option value="Permanen" <?= ($init_kategori === 'Permanen') ? 'selected' : '' ?>>Permanen</option>
                        <option value="Semi Permanen" <?= ($init_kategori === 'Semi Permanen') ? 'selected' : '' ?>>Semi Permanen</option>
                        <option value="Panggung" <?= ($init_kategori === 'Panggung') ? 'selected' : '' ?>>Panggung</option>
                        <option value="Numpang" <?= ($init_kategori === 'Numpang') ? 'selected' : '' ?>>Numpang</option>
                        <option value="belum_dinilai" <?= ($init_kategori === 'belum_dinilai') ? 'selected' : '' ?>>Belum Dinilai</option>
                    </select>
                </div>

                <!-- Filter Sumber Penilaian -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold mb-1">Sumber Penilaian</label>
                    <select class="form-select form-select-sm" id="filter_sumber">
                        <option value="all">Semua Sumber</option>
                        <option value="otomatis">Otomatis (Sistem)</option>
                        <option value="manual">Manual (Petugas)</option>
                    </select>
                </div>

                <!-- Tombol Aksi: Export & Reset -->
                <div class="col-md-3 col-sm-12 text-md-end text-start mt-md-4 mt-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetFilter" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAutoDetectAll" title="Jalankan Auto-Klasifikasi Masal dari Tab Rumah">
                            <i class="fas fa-robot me-1"></i> Auto-Detect
                        </button>
                        <button type="button" class="btn btn-sm btn-success" id="btnExportExcel" title="Unduh Data Hasil Filter ke File Excel (.xlsx)">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. TABEL DATA UTAMA                       -->
    <!-- ========================================== -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="tableKeadaanRumah" class="table table-striped table-hover align-middle w-100">
                    <thead class="table-light text-center text-uppercase small">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Kepala Keluarga</th>
                            <th>No. KK</th>
                            <th>Alamat</th>
                            <th>Wilayah</th>
                            <th>Kepemilikan</th>
                            <th>Fisik (L / D / A)</th>
                            <th>Keadaan Rumah</th>
                            <th>Sumber</th>
                            <th style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. MODAL UBAH / SESUAIKAN PENILAIAN        -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditKeadaan" tabindex="-1" aria-labelledby="modalEditKeadaanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold" id="modalEditKeadaanLabel"><i class="fas fa-home me-2"></i>Sesuaikan Keadaan Rumah</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditKeadaan">
                <input type="hidden" id="edit_id_kk" name="id_kk">
                <div class="modal-body p-3">
                    <div class="bg-light p-2 rounded mb-3 border">
                        <div class="small text-muted">Kepala Keluarga: <span class="fw-bold text-dark" id="txtNamaKK">-</span></div>
                        <div class="small text-muted">No. KK: <span class="fw-bold text-dark" id="txtNoKK">-</span></div>
                    </div>

                    <!-- Kepemilikan -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Status Kepemilikan Rumah</label>
                        <select class="form-select form-select-sm calc-trigger" name="status_kepemilikan" id="edit_status_kepemilikan">
                            <option value="">[Pilih Kepemilikan]</option>
                            <option value="Milik Sendiri">Milik Sendiri</option>
                            <option value="Sewa">Sewa</option>
                            <option value="Kontrak">Kontrak</option>
                            <option value="Bebas Sewa">Bebas Sewa (Numpang)</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Lantai</label>
                            <select class="form-select form-select-sm calc-trigger" name="jenis_lantai" id="edit_jenis_lantai">
                                <option value="">[Pilih]</option>
                                <option value="Marmer/granit">Marmer/granit</option>
                                <option value="Keramik">Keramik</option>
                                <option value="Parket/vinil/karpet">Parket/vinil</option>
                                <option value="Ubin/tegal/teraso">Ubin/tegel</option>
                                <option value="Kayu">Kayu</option>
                                <option value="Semen">Semen</option>
                                <option value="Bambu">Bambu</option>
                                <option value="Tanah">Tanah</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Dinding</label>
                            <select class="form-select form-select-sm calc-trigger" name="jenis_dinding" id="edit_jenis_dinding">
                                <option value="">[Pilih]</option>
                                <option value="Tembok">Tembok</option>
                                <option value="Plesteran anyaman bambu/kawat">Plesteran bambu</option>
                                <option value="Kayu/papan/gypsum/GRC/calciboard">Kayu/papan/GRC</option>
                                <option value="Anyaman bambu">Anyaman bambu</option>
                                <option value="Batang kayu">Batang kayu</option>
                                <option value="Bambu">Bambu</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Atap</label>
                            <select class="form-select form-select-sm calc-trigger" name="jenis_atap" id="edit_jenis_atap">
                                <option value="">[Pilih]</option>
                                <option value="Beton">Beton</option>
                                <option value="Genteng">Genteng</option>
                                <option value="Seng">Seng</option>
                                <option value="Asbes">Asbes</option>
                                <option value="Bambu">Bambu</option>
                                <option value="Kayu/sirap">Kayu/sirap</option>
                                <option value="Rumbia">Rumbia</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Rekomendasi Sistem -->
                    <div class="alert alert-info py-2 px-3 small d-flex justify-content-between align-items-center mb-2">
                        <span><i class="fas fa-lightbulb text-warning me-1"></i> Rekomendasi Sistem:</span>
                        <strong id="badgeRekomendasi">-</strong>
                    </div>

                    <!-- Kategori Final -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-primary">Kategori Keadaan Rumah (Final) <span class="text-danger">*</span></label>
                        <select class="form-select fw-bold" name="kategori_rumah" id="edit_kategori_rumah" required>
                            <option value="">[Pilih Kategori Final]</option>
                            <option value="Permanen">Permanen</option>
                            <option value="Semi Permanen">Semi Permanen</option>
                            <option value="Panggung">Panggung</option>
                            <option value="Numpang">Numpang</option>
                        </select>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Catatan Penilaian</label>
                        <textarea class="form-control form-control-sm" name="catatan" id="edit_catatan" rows="2" placeholder="Catatan opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSimpanEdit">
                        <i class="fas fa-save me-1"></i> Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
        // ==========================================
    // 🛡️ SENSOR DATA SENSITIF (NO. KK)
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

    // ==========================================
    // 📋 SALIN NO KK KE CLIPBOARD
    // ==========================================
    $(document).on('click', '.btnCopyNoKK', function(e) {
        e.preventDefault();
        const value = $(this).data('value');
        if (!value) return;

        navigator.clipboard.writeText(value).then(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin',
                    text: 'No. KK ' + value + ' berhasil disalin ke clipboard',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top'
                });
            } else {
                alert('No. KK ' + value + ' berhasil disalin ke clipboard');
            }
        }).catch(() => {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Gagal', 'Tidak dapat menyalin No. KK', 'error');
            } else {
                alert('Gagal menyalin No. KK');
            }
        });
    });

        $(document).ready(function() {
        // Klik card summary langsung filter tabel
        $(document).on('click', '.btn-stat-filter', function() {
            const kat = $(this).data('kategori');
            $('#filter_kategori').val(kat).trigger('change');
        });

        // Tangkap parameter query dari URL jika ada
        const urlParams = new URLSearchParams(window.location.search);
        const urlKat = urlParams.get('kategori') || '<?= esc($init_kategori ?? '') ?>';
        const urlRw  = urlParams.get('rw') || '<?= esc($init_rw ?? '') ?>';
        const urlRt  = urlParams.get('rt') || '<?= esc($init_rt ?? '') ?>';

        if (urlKat) {
            $('#filter_kategori').val(urlKat);
        }

        if (urlRw && urlRw !== 'all') {
            $('#filter_rw').val(urlRw);
            const $rt = $('#filter_rt');
            const defaultRtText = '<?= ($user_login['role_id'] ?? 0) >= 4 ? 'Semua RT Tugas' : 'Semua RT' ?>';
            $.getJSON('<?= base_url('dtsen/keadaan-rumah/rt-by-rw') ?>/' + encodeURIComponent(urlRw), function(res) {
                $rt.empty().append('<option value="all">' + defaultRtText + '</option>');
                if (res && res.length > 0) {
                    res.forEach(item => {
                        const isSel = (item.rt == urlRt) ? 'selected' : '';
                        $rt.append(`<option value="${item.rt}" ${isSel}>RT ${item.rt}</option>`);
                    });
                    $rt.prop('disabled', false);
                }
            });
        }
        // ==========================================
        // 1. INISIALISASI DATATABLES
        // ==========================================
        const table = $('#tableKeadaanRumah').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            ajax: {
                url: '<?= base_url('dtsen/keadaan-rumah/data') ?>',
                type: 'POST',
                data: function(d) {
                    d.filter_rw = $('#filter_rw').val();
                    d.filter_rt = $('#filter_rt').val();
                    d.filter_kategori = $('#filter_kategori').val();
                    d.filter_sumber = $('#filter_sumber').val();
                }
            },
            columns: [{
                    data: 'no',
                    orderable: false,
                    className: 'text-center',
                    render: (d, type) => (type === 'display' ? formatRibuanID(d) : d)
                },
                {
                    data: 'kepala_keluarga'
                },
                                {
                    data: 'no_kk',
                    className: 'text-center font-monospace text-nowrap',
                    render: function(data, type, row) {
                        if (type === 'filter' || type === 'sort') {
                            return data;
                        }

                        let maskedData = maskNumberJS(data);

                        return `
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="fw-semibold">${maskedData}</span>
                                <button 
                                    type="button" 
                                    class="btn btn-outline-secondary btn-xs btnCopyNoKK py-0 px-1" 
                                    data-value="${data}" 
                                    title="Salin No KK">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        `;
                    }
                },
                {
                    data: 'alamat'
                },
                {
                    data: 'wilayah',
                    className: 'text-center'
                },
                {
                    data: 'status_kepemilikan',
                    className: 'text-center'
                },
                {
                    data: 'fisik',
                    orderable: false
                },
                {
                    data: 'kategori_rumah',
                    className: 'text-center'
                },
                {
                    data: 'sumber_penilaian',
                    className: 'text-center'
                },
                {
                    data: 'aksi',
                    orderable: false,
                    className: 'text-center'
                }
            ],
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary"></div> Memuat data...',
                search: "Cari KK / Nama:",
                lengthMenu: "Tampil _MENU_",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ KK",
                infoEmpty: "Data tidak ditemukan",
                zeroRecords: "Tidak ada data yang sesuai",
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                }
            }
        });

        // ==========================================
        // 2. FILTER DEPENDENT RT DARI RW
        // ==========================================
        $('#filter_rw').on('change', function() {
            const rw = $(this).val();
            const $rt = $('#filter_rt');
            const defaultRtText = '<?= ($user_login['role_id'] ?? 0) >= 4 ? 'Semua RT Tugas' : 'Semua RT' ?>';
            $rt.empty().append('<option value="all">' + defaultRtText + '</option>');

            if (rw && rw !== 'all') {
                $.getJSON('<?= base_url('dtsen/keadaan-rumah/rt-by-rw') ?>/' + encodeURIComponent(rw), function(res) {
                    if (res && res.length > 0) {
                        res.forEach(item => {
                            $rt.append(`<option value="${item.rt}">RT ${item.rt}</option>`);
                        });
                        $rt.prop('disabled', false);
                    } else {
                        $rt.prop('disabled', true);
                    }
                    table.ajax.reload();
                    refreshSummary();
                });
            } else {
                $rt.prop('disabled', true);
                table.ajax.reload();
                refreshSummary();
            }
        });

        $('#filter_rt, #filter_kategori, #filter_sumber').on('change', function() {
            table.ajax.reload();
            refreshSummary();
        });

        // Reset Filter
        $('#btnResetFilter').on('click', function() {
            const defaultRtText = '<?= ($user_login['role_id'] ?? 0) >= 4 ? 'Semua RT Tugas' : 'Semua RT' ?>';
            $('#filter_rw').val('all');
            $('#filter_rt').empty().append('<option value="all">' + defaultRtText + '</option>').prop('disabled', true);
            $('#filter_kategori').val('all');
            $('#filter_sumber').val('all');
            table.search('').ajax.reload();
            refreshSummary();
        });

        // ==========================================
        // 3. SEGARKAN RINGKASAN STATISTIK
        // ==========================================
        function refreshSummary() {
            const rw = $('#filter_rw').val();
            const rt = $('#filter_rt').val();
            $.getJSON('<?= base_url('dtsen/keadaan-rumah/grafik-data') ?>', {
                rw: rw,
                rt: rt
            }, function(data) {
                if (data) {
                    $('#statTotal').text(Number(data.total).toLocaleString('id-ID'));
                    $('#statPermanen').html(`${Number(data.permanen.count).toLocaleString('id-ID')} <span class="small fw-normal text-muted">(${data.permanen.percent}%)</span>`);
                    $('#statSemi').html(`${Number(data.semi_permanen.count).toLocaleString('id-ID')} <span class="small fw-normal text-muted">(${data.semi_permanen.percent}%)</span>`);
                    $('#statPanggung').html(`${Number(data.panggung.count).toLocaleString('id-ID')} <span class="small fw-normal text-muted">(${data.panggung.percent}%)</span>`);
                    $('#statNumpang').html(`${Number(data.numpang.count).toLocaleString('id-ID')} <span class="small fw-normal text-muted">(${data.numpang.percent}%)</span>`);
                    $('#statBelum').html(`${Number(data.belum_dinilai.count).toLocaleString('id-ID')} <span class="small fw-normal text-muted">(${data.belum_dinilai.percent}%)</span>`);
                }
            });
        }

        // ==========================================
        // 4. EKSPOR EXCEL
        // ==========================================
        $('#btnExportExcel').on('click', function() {
            const params = new URLSearchParams({
                rw: $('#filter_rw').val() !== 'all' ? $('#filter_rw').val() : '',
                rt: $('#filter_rt').val() !== 'all' ? $('#filter_rt').val() : '',
                kategori: $('#filter_kategori').val() !== 'all' ? $('#filter_kategori').val() : '',
                sumber: $('#filter_sumber').val() !== 'all' ? $('#filter_sumber').val() : '',
                search: table.search()
            });

            window.location.href = '<?= base_url('dtsen/keadaan-rumah/export-excel') ?>?' + params.toString();
        });

        // ==========================================
        // 5. AUTO-DETECT MASAL
        // ==========================================
        $('#btnAutoDetectAll').on('click', function() {
            Swal.fire({
                title: 'Auto-Detect Keadaan Rumah?',
                text: 'Sistem akan memindai seluruh data Tab Rumah terbaru dan menentukan kriteria secara otomatis.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-robot me-1"></i> Ya, Jalankan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#169B88'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Data...',
                        text: 'Mohon tunggu beberapa saat',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.post('<?= base_url('dtsen/keadaan-rumah/auto-detect-all') ?>', function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil!', res.message, 'success');
                            table.ajax.reload();
                            refreshSummary();
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    }).fail(function() {
                        Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                    });
                }
            });
        });

        // ==========================================
        // 6. MODAL EDIT & HITUNG LOGIKA DI MODAL
        // ==========================================
        function hitungRekomendasiModal() {
            const pemilikan = $('#edit_status_kepemilikan').val();
            const lantai = $('#edit_jenis_lantai').val();
            const dinding = $('#edit_jenis_dinding').val();
            const atap = $('#edit_jenis_atap').val();

            if (pemilikan === 'Bebas Sewa' || pemilikan === 'Menumpang' || pemilikan === 'Numpang') {
                return 'Numpang';
            }

            if (!lantai && !dinding && !atap) {
                return 'Belum Bisa Ditentukan';
            }

            const lantaiPanggung = ['Kayu', 'Bambu'];
            if (lantaiPanggung.includes(lantai) && dinding !== 'Tembok') {
                return 'Panggung';
            }

            const lantaiPerm = ['Marmer/granit', 'Keramik', 'Parket/vinil/karpet', 'Ubin/tegal/teraso', 'Semen'];
            const atapPerm = ['Beton', 'Genteng', 'Seng', 'Asbes'];

            if (dinding === 'Tembok' && (lantaiPerm.includes(lantai) || !lantai) && (atapPerm.includes(atap) || !atap)) {
                return 'Permanen';
            }

            return 'Semi Permanen';
        }

        $('.calc-trigger').on('change', function() {
            const rek = hitungRekomendasiModal();
            $('#badgeRekomendasi').text(rek);
            if (rek !== 'Belum Bisa Ditentukan') {
                $('#edit_kategori_rumah').val(rek);
            }
        });

        $(document).on('click', '.btn-edit-keadaan', function() {
            const btn = $(this);
            $('#edit_id_kk').val(btn.data('id'));
            $('#txtNamaKK').text(btn.data('nama'));
            const kkVal = btn.data('kk') || '';
            $('#txtNoKK').html(maskNumberJS(kkVal) + ` <button type="button" class="btn btn-outline-secondary btn-xs btnCopyNoKK py-0 px-1 ms-1" data-value="${kkVal}" title="Salin No KK"><i class="fas fa-copy"></i></button>`);
            $('#edit_status_kepemilikan').val(btn.data('pemilikan'));
            $('#edit_jenis_lantai').val(btn.data('lantai'));
            $('#edit_jenis_dinding').val(btn.data('dinding'));
            $('#edit_jenis_atap').val(btn.data('atap'));
            $('#edit_kategori_rumah').val(btn.data('kategori'));
            $('#edit_catatan').val(btn.data('catatan'));

            const rek = hitungRekomendasiModal();
            $('#badgeRekomendasi').text(rek);

            $('#modalEditKeadaan').modal('show');
        });

        $('#formEditKeadaan').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btnSimpanEdit');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.post('<?= base_url('dtsen/keadaan-rumah/save') ?>', $(this).serialize(), function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Penilaian');
                if (res.status === 'success') {
                    $('#modalEditKeadaan').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    table.ajax.reload(null, false);
                    refreshSummary();
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            }).fail(function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Penilaian');
                Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
            });
        });
    });
</script>

<?= $this->endSection(); ?>