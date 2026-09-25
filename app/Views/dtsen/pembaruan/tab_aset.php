<?php
// =============================================
// Tab: Kepemilikan Aset (Pemutakhiran Keluarga)
// =============================================

// Hak akses
$roleId = session()->get('role_id') ?? ($user['role_id'] ?? 99);
$editable = ($roleId <= 4);
$disabled = $editable ? '' : 'disabled';

// Prefill otomatis
$aset = $aset ?? [];
if (!empty($payload['aset'])) {
    $aset = array_merge($aset, $payload['aset']);
}

// Badge indikator kelengkapan
$isComplete = !empty($aset) && !in_array(null, $aset, true);
?>

<style>
    /* CSS ini sudah sangat baik untuk membuat select readonly secara visual */
    select[readonly],
    select[data-auto="true"] {
        pointer-events: none;
        background-color: #f8f9fa;
        opacity: 0.95;
    }
</style>
<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            💰 Kepemilikan Aset
        </h5>
    </div>

    <form id="formAset" class="needs-validation" novalidate>
        <input type="hidden" name="dtsen_usulan_id" value="<?= esc($usulan['id'] ?? '') ?>">
        <input type="hidden" name="no_kk" value="<?= esc($perumahan['no_kk'] ?? $perumahan['no_kk'] ?? '') ?>">
        <input type="hidden" name="sumber" value="<?= esc($sumber ?? 'master') ?>">

        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        Jumlah Aset Bergerak yang Dimiliki:
                    </div>
                    <div class="card-body">
                        <?php
                        // 🚀 PENYESUAIAN ARRAY ASET SESUAI BPS
                        $asetBergerak = [
                            'Tabung Gas 3 KG' => 'tabung_gas_3kg',          // Di urutan pertama
                            'Tabung Gas 5,5 kg atau lebih' => 'tabung_gas', // Menyusul di urutan kedua
                            'Lemari es / Kulkas' => 'kulkas',
                            'Air Conditioner (AC)' => 'ac',
                            'Emas / Perhiasan (gram)' => 'emas',
                            'Komputer / Laptop / Tablet' => 'laptop',
                            'Sepeda Motor' => 'sepeda_motor',
                            'Mobil' => 'mobil',
                            'Pemanas Air (Water Heater)' => 'water_heater',
                            'Telepon Rumah (PSTN)' => 'telepon_rumah',
                            'Televisi Layar Datar (min. 30 inci)' => 'tv_lcd',
                            'Sepeda' => 'sepeda',
                            'Perahu' => 'perahu',
                            'Smartphone' => 'smartphone'
                        ];
                        ?>

                        <div class="row">
                            <?php foreach ($asetBergerak as $label => $name): ?>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-primary fw-semibold"><?= esc($label) ?></label>

                                    <!-- Tambahkan ID di sini -->
                                    <input type="number" min="0" class="form-control form-control-sm border-primary"
                                        name="<?= $name ?>" id="<?= $name ?>" value="<?= esc($aset[$name] ?? 0) ?>" <?= $disabled ?>>

                                    <?php if ($name === 'sepeda_motor'): ?>
                                        <!-- 🚀 ELEMEN DINAMIS: Nilai Sepeda Motor -->
                                        <div id="div_nilai_sepeda_motor" class="mt-2 p-2 bg-light border border-primary rounded" style="display: none;">
                                            <label class="form-label text-primary mb-1">Total Nilai Aset Motor (Rp) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm has-validation">
                                                <!-- 🚀 KOREKSI: data-qty-target diubah menjadi #sepeda_motor -->
                                                <input type="text" name="nilai_sepeda_motor" id="nilai_sepeda_motor" class="form-control rupiah border-primary val-min-aset" data-min="500000" data-qty-target="#sepeda_motor" value="<?= esc($aset['nilai_sepeda_motor'] ?? '') ?>" <?= $disabled ?> placeholder="Rp...">
                                                <?php if (!$disabled): ?>
                                                    <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#nilai_sepeda_motor" title="Salin Nilai Motor"><i class="fas fa-copy"></i></button>
                                                <?php endif; ?>
                                                <div class="invalid-feedback small fw-bold w-100">Nilai Aset minimal bernilai Rp 500.000</div>
                                            </div>
                                        </div>
                                    <?php elseif ($name === 'mobil'): ?>
                                        <!-- 🚀 ELEMEN DINAMIS: Nilai Mobil -->
                                        <div id="div_nilai_mobil" class="mt-2 p-2 bg-light border border-primary rounded" style="display: none;">
                                            <label class="form-label text-primary mb-1">Total Nilai Aset Mobil (Rp) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm has-validation">
                                                <!-- 🚀 KOREKSI: data-qty-target diubah menjadi #mobil -->
                                                <input type="text" name="nilai_mobil" id="nilai_mobil" class="form-control rupiah border-primary val-min-aset" data-min="10000000" data-qty-target="#mobil" value="<?= esc($aset['nilai_mobil'] ?? '') ?>" <?= $disabled ?> placeholder="Rp...">
                                                <?php if (!$disabled): ?>
                                                    <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#nilai_mobil" title="Salin Nilai Mobil"><i class="fas fa-copy"></i></button>
                                                <?php endif; ?>
                                                <div class="invalid-feedback small fw-bold w-100">Nilai Aset minimal bernilai Rp 10.000.000</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        Jumlah Ternak yang Dimiliki:
                    </div>
                    <div class="card-body">
                        <?php
                        $ternak = [
                            'Sapi' => 'sapi',
                            'Kuda' => 'kuda',
                            'Babi' => 'babi',
                            'Kerbau' => 'kerbau',
                            'Kambing / Domba' => 'kambing'
                        ];
                        ?>
                        <div class="row">
                            <?php foreach ($ternak as $label => $name): ?>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label"><?= esc("Jumlah $label") ?></label>
                                    <input type="number" min="0" class="form-control form-control-sm"
                                        name="<?= $name ?>" value="<?= esc($aset[$name] ?? 0) ?>" <?= $disabled ?>>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        Jumlah Aset Tidak Bergerak yang Dimiliki:
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- 🚀 ELEMEN BARU: Rumah/Bangunan Lain (Unit) & Nilai -->
                            <div class="col-md-4">
                                <label class="form-label">Rumah / Bangunan Lain (Unit) <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="rumah_lain" id="rumah_lain"
                                    class="form-control form-control-sm <?= $disabled ? '' : 'required-field' ?>"
                                    value="<?= esc($aset['rumah_lain'] ?? '') ?>" <?= $disabled ?> <?= $disabled ? '' : 'required' ?> placeholder="Ketik 0 jika tidak punya">
                                <?php if (!$disabled): ?>
                                    <div class="invalid-feedback">
                                        Wajib mengisi jumlah unit (isi 0 jika tidak memiliki).
                                    </div>
                                <?php endif; ?>

                                <!-- 🚀 ELEMEN DINAMIS: Nilai Rumah Lain -->
                                <div id="div_nilai_rumah" class="mt-2 p-2 bg-light border border-primary rounded" style="display: none;">
                                    <label class="form-label text-primary mb-1">Total Nilai Aset Rumah (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm has-validation">
                                        <input type="text" name="nilai_rumah_lain" id="nilai_rumah_lain" class="form-control rupiah border-primary" value="<?= esc($aset['nilai_rumah_lain'] ?? '') ?>" <?= $disabled ?> placeholder="Rp...">
                                        <?php if (!$disabled): ?>
                                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#nilai_rumah_lain" title="Salin Nilai Rumah"><i class="fas fa-copy"></i></button>
                                        <?php endif; ?>
                                        <div class="invalid-feedback">Wajib diisi karena unit > 0</div>
                                    </div>
                                </div>
                            </div>

                            <!-- 🚀 ELEMEN BARU (BPS): Jumlah Titik Lahan Sawah/Kebun & Nilai -->
                            <div class="col-md-4">
                                <label class="form-label" title="Dihitung per titik lokasi/RT, bukan meter persegi">
                                    Jumlah Titik Lahan Sawah/Kebun <span class="text-danger">*</span>
                                </label>
                                <!-- step diubah jadi 1 karena hitungannya unit/titik, bukan desimal -->
                                <input type="number" min="0" step="1" name="luas_sawah" id="luas_sawah"
                                    class="form-control form-control-sm <?= $disabled ? '' : 'required-field' ?>"
                                    value="<?= esc($aset['luas_sawah'] ?? '') ?>" <?= $disabled ?> <?= $disabled ? '' : 'required' ?> placeholder="Ketik 0 jika tidak punya">

                                <?php if (!$disabled): ?>
                                    <div class="invalid-feedback">
                                        Wajib mengisi jumlah lokasi (isi 0 jika tidak memiliki).
                                    </div>
                                <?php endif; ?>

                                <!-- 🚀 ELEMEN DINAMIS: Nilai Sawah -->
                                <div id="div_nilai_sawah" class="mt-2 p-2 bg-light border border-primary rounded" style="display: none;">
                                    <label class="form-label text-primary mb-1">Total Nilai Aset Lahan (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm has-validation">
                                        <input type="text" name="nilai_sawah" id="nilai_sawah" class="form-control rupiah border-primary" value="<?= esc($aset['nilai_sawah'] ?? '') ?>" <?= $disabled ?> placeholder="Rp...">
                                        <?php if (!$disabled): ?>
                                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#nilai_sawah" title="Salin Nilai Lahan"><i class="fas fa-copy"></i></button>
                                        <?php endif; ?>

                                        <!-- 🚀 TAMBAHKAN ID err_nilai_sawah DI SINI -->
                                        <div id="err_nilai_sawah" class="invalid-feedback">Wajib diisi karena jumlah lahan > 0</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Memiliki Lahan (Tetap Ada) -->
                            <div class="col-md-4">
                                <label class="form-label">Memiliki Lahan (selain yang ditempati)</label>
                                <select name="memiliki_lahan"
                                    id="memiliki_lahan"
                                    class="form-select form-select-sm bg-light"
                                    readonly tabindex="-1" data-auto="true">
                                    <option value="TIDAK" <?= ($aset['memiliki_lahan'] ?? '') === 'TIDAK' ? 'selected' : '' ?>>TIDAK</option>
                                    <option value="YA" <?= ($aset['memiliki_lahan'] ?? '') === 'YA' ? 'selected' : '' ?>>YA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <?php if ($editable): ?>
        <div class="text-end mt-4">
            <button type="button" id="btnSimpanAset" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>
    <?php else: ?>
        <div class="alert alert-warning small mt-3">
            <i class="fas fa-lock"></i> Anda tidak memiliki hak untuk mengubah data keluarga ini.
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {

        // ==============================================================
        // 🚀 VALIDASI BATAS MINIMAL ASET (DINAMIS DENGAN KELIPATAN JUMLAH)
        // ==============================================================
        function validateMinAset(el) {
            const $el = $(el);
            const minLimitBase = parseInt($el.data('min'), 10);
            const qtyTarget = $el.data('qty-target'); // Ambil ID input jumlah

            // Cari nilai jumlah unitnya, jika kosong atau tidak valid, anggap 1
            let qty = 1;
            if (qtyTarget && $(qtyTarget).length) {
                qty = parseInt($(qtyTarget).val(), 10) || 1;
            }

            // Hitung batas minimal sesungguhnya (Kelipatan)
            const actualMinLimit = minLimitBase * qty;

            // Bersihkan format rupiah dari input nilai
            let rawValue = $el.val().replace(/\./g, '');
            if (rawValue === '') {
                $el.removeClass('is-invalid');
                return;
            }

            const numValue = parseInt(rawValue, 10);
            const $feedback = $el.siblings('.invalid-feedback'); // Target pesan error

            // Cek apakah di bawah standar
            if (numValue < actualMinLimit) {
                $el.addClass('is-invalid');
                // Ubah teks peringatan secara real-time dengan format Rupiah
                const formatRupiah = new Intl.NumberFormat('id-ID').format(actualMinLimit);
                $feedback.text(`Total nilai minimal untuk ${qty} unit adalah Rp ${formatRupiah}`);
            } else {
                $el.removeClass('is-invalid');
            }
        }

        // 1. Trigger saat input nilai aset itu sendiri diketik/berubah
        $('.val-min-aset').on('input change', function() {
            validateMinAset(this);
        });

        // 2. 🚀 Trigger JUGA saat input JUMLAH (qty) diubah
        $('.val-min-aset').each(function() {
            const qtyTarget = $(this).data('qty-target');
            if (qtyTarget) {
                // Gunakan delegasi event agar bisa mendeteksi perubahan dari elemen lain
                $(document).on('input change', qtyTarget, () => {
                    validateMinAset(this);
                });
            }
        });

    });

    $(function() {
        // ====================================================
        // 🚀 LOGIKA DINAMIS & VALIDASI BPS: ASET LAHAN
        // 1 Titik = Minimal Rp 6.000.000
        // ====================================================
        function cekStandarSawahBPS() {
            let jmlSawah = parseInt($('#luas_sawah').val()) || 0;
            let rawNilai = $('#nilai_sawah').val().replace(/[^0-9]/g, '');
            let nilaiSawah = parseInt(rawNilai) || 0;

            if (jmlSawah > 0) {
                $('#div_nilai_sawah').slideDown();
                $('#nilai_sawah').prop('required', true);

                // 🚀 AUTO-SET MEMILIKI LAHAN = YA
                $('#memiliki_lahan').val('YA');

                // Hitung nilai minimal (Jumlah Titik x Rp 6.000.000)
                let minNilaiBPS = jmlSawah * 6000000;

                if (nilaiSawah < minNilaiBPS) {
                    let formatMin = new Intl.NumberFormat('id-ID').format(minNilaiBPS);
                    $('#nilai_sawah').addClass('is-invalid').removeClass('border-primary');
                    $('#err_nilai_sawah').text('Total Nilai Aset minimal bernilai Rp ' + formatMin);
                } else {
                    $('#nilai_sawah').removeClass('is-invalid').addClass('border-primary');
                }
            } else {
                $('#div_nilai_sawah').slideUp();
                $('#nilai_sawah').prop('required', false).removeClass('is-invalid');

                // 🚀 AUTO-SET MEMILIKI LAHAN = TIDAK
                $('#memiliki_lahan').val('TIDAK');
            }
        }

        // ====================================================
        // 🚀 LOGIKA DINAMIS: NILAI RUMAH, MOTOR, & MOBIL
        // ====================================================
        function toggleNilaiRumah() {
            const val = parseInt($('#rumah_lain').val()) || 0;
            if (val > 0) {
                $('#div_nilai_rumah').slideDown();
                $('#nilai_rumah_lain').prop('required', true);
            } else {
                $('#div_nilai_rumah').slideUp();
                $('#nilai_rumah_lain').prop('required', false).val('');
            }
        }

        function toggleNilaiMotor() {
            const val = parseInt($('#sepeda_motor').val()) || 0;
            if (val > 0) {
                $('#div_nilai_sepeda_motor').slideDown();
                $('#nilai_sepeda_motor').prop('required', true);
            } else {
                $('#div_nilai_sepeda_motor').slideUp();
                $('#nilai_sepeda_motor').prop('required', false).val('');
            }
        }

        function toggleNilaiMobil() {
            const val = parseInt($('#mobil').val()) || 0;
            if (val > 0) {
                $('#div_nilai_mobil').slideDown();
                $('#nilai_mobil').prop('required', true);
            } else {
                $('#div_nilai_mobil').slideUp();
                $('#nilai_mobil').prop('required', false).val('');
            }
        }

        // 🎯 EVENT LISTENER (Jalankan saat diketik)
        $('#luas_sawah, #nilai_sawah').on('input change', cekStandarSawahBPS);
        $('#rumah_lain').on('input change', toggleNilaiRumah);
        $('#sepeda_motor').on('input change', toggleNilaiMotor);
        $('#mobil').on('input change', toggleNilaiMobil);

        // 🎯 INISIALISASI SAAT HALAMAN DIBUKA (Untuk prefill data)
        cekStandarSawahBPS();
        toggleNilaiRumah();
        toggleNilaiMotor();
        toggleNilaiMobil();

        // ====================================================
        // 💾 EVENT SIMPAN ASET (Dengan Gatekeeper BPS)
        // ====================================================
        $('#btnSimpanAset').on('click', function(e) {
            e.preventDefault();

            // 1️⃣ GATEKEEPER 1: Cek Standar Nilai Lahan BPS
            let jmlSawah = parseInt($('#luas_sawah').val()) || 0;
            let rawNilai = $('#nilai_sawah').val().replace(/[^0-9]/g, '');
            let nilaiSawah = parseInt(rawNilai) || 0;
            let minNilaiBPS = jmlSawah * 6000000;

            if (jmlSawah > 0 && nilaiSawah < minNilaiBPS) {
                let formatMin = new Intl.NumberFormat('id-ID').format(minNilaiBPS);
                $('#nilai_sawah').focus();
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Aset Tidak Logis',
                    text: 'Sesuai standar BPS, untuk ' + jmlSawah + ' lokasi lahan, Total Nilai Aset minimal bernilai Rp ' + formatMin,
                    width: '320px',
                    customClass: {
                        title: 'fs-5',
                        content: 'fs-6'
                    }
                });
                return; // 🛑 Hentikan proses simpan!
            }

            // 🚀 TAMBAHAN: GATEKEEPER 1.5 - Cek Garis Merah pada Motor & Mobil
            if ($('.val-min-aset.is-invalid').length > 0) {
                $('.val-min-aset.is-invalid').first().focus(); // Fokus ke elemen pertama yang salah
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Aset Tidak Logis',
                    text: 'Silakan perbaiki total nilai aset (Motor/Mobil) yang ditandai dengan warna merah.',
                    width: '320px',
                    customClass: {
                        title: 'fs-5',
                        content: 'fs-6'
                    }
                });
                return; // 🛑 Hentikan proses simpan!
            }

            // 2️⃣ GATEKEEPER 2: Cek Validitas Form HTML5 (Wajib Isi)
            const form = $('#formAset')[0];
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                Swal.fire({
                    icon: 'warning',
                    title: 'Isian Belum Lengkap',
                    text: 'Silakan periksa kembali field yang bertanda bintang (*).',
                    width: '320px',
                    customClass: {
                        title: 'fs-5',
                        content: 'fs-6'
                    }
                });
                return; // 🛑 Hentikan proses simpan!
            }

            // 3️⃣ JIKA LOLOS SEMUA -> PROSES AJAX SIMPAN
            const formData = $('#formAset').serialize();

            $.post('<?= base_url('pembaruan-keluarga/save-aset') ?>', formData, function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data aset berhasil disimpan.',
                        width: '320px',
                        customClass: {
                            title: 'fs-5',
                            content: 'fs-6'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message || 'Terjadi kesalahan.',
                        width: '320px',
                        customClass: {
                            title: 'fs-5',
                            content: 'fs-6'
                        }
                    });
                }
            }, 'json').fail(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Tidak dapat terhubung ke server.',
                    width: '320px',
                    customClass: {
                        title: 'fs-5',
                        content: 'fs-6'
                    }
                });
            });
        });
    });
    // =======================================================
    // 📋 FUNGSI SALIN KE CLIPBOARD (DINAMIS UNTUK SEMUA INPUT)
    // =======================================================
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.btn-copy-input').forEach(btn => {
            btn.addEventListener('click', function() {
                // Ambil ID target dari atribut data-target
                const targetSelector = this.getAttribute('data-target');
                const inputEl = document.querySelector(targetSelector);

                if (inputEl && inputEl.value.trim() !== '') {
                    // Salin ke clipboard
                    navigator.clipboard.writeText(inputEl.value.trim()).then(() => {
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'success',
                            title: 'Teks disalin!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });
                } else {
                    // Jika inputan masih kosong
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'warning',
                        title: 'Kolom masih kosong!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });
    });
</script>