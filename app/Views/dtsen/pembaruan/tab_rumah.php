<?php
$roleId = session()->get('role_id') ?? ($user['role_id'] ?? 99);
$editable = ($roleId <= 4); // Operator & Pendata bisa edit
$disabled = $editable ? '' : 'disabled';
$readonly = $editable ? '' : 'readonly';

$perumahan = $payload['perumahan'] ?? [];
$kond = $perumahan['kondisi'] ?? [];
$san  = $perumahan['sanitasi'] ?? [];
$se   = $payload['sosial_ekonomi'] ?? [];
?>

<style>
    .card .card-header {
        font-size: .95rem;
        background-color: #f8f9fa;
    }

    .form-label {
        font-size: .85rem;
        font-weight: 600;
        color: #495057;
    }

    .badge {
        font-size: .85rem;
        padding: .45em .6em;
    }

    .card {
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }

    /* Penyesuaian agar radio button rapi */
    .form-check-label {
        font-size: 0.85rem;
    }

    .radio-group-box {
        background: #fdfdfd;
        border: 1px solid #eee;
        border-radius: 6px;
        padding: 10px;
    }
</style>

<div class="p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">🏡 Keterangan Perumahan & Finansial</h5>
        <div>
            <span id="badgeRumah" class="badge <?= empty($perumahan) ? 'bg-secondary' : 'bg-danger' ?>">
                <?= empty($perumahan) ? 'Kosong' : 'Belum Lengkap' ?>
            </span>
        </div>
    </div>

    <form id="formRumahFull" enctype="multipart/form-data">
        <input type="hidden" name="dtsen_usulan_id" value="<?= esc($usulan['id'] ?? '') ?>">
        <input type="hidden" name="no_kk" value="<?= esc($perumahan['no_kk'] ?? $perumahan['no_kk'] ?? '') ?>">
        <input type="hidden" name="sumber" value="<?= esc($sumber ?? 'master') ?>">

        <!-- ========================================== -->
        <!-- 1. KONDISI BANGUNAN & FISIK -->
        <!-- ========================================== -->
        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>1. Kondisi Bangunan & Fasilitas Fisik</strong></div>
            <div class="card-body">

                <div class="row g-3 mb-3 border-bottom pb-3">
                    <!-- 🚀 RADIO (5): Jenis Bangunan -->
                    <div class="col-md-6">
                        <label class="form-label text-primary">Jenis Bangunan</label>
                        <div class="radio-group-box">
                            <?php $opsiBangunan = ['Rumah Tunggal', 'Apartemen', 'Rumah Susun', 'Rumah Deret', 'Lainnya'];
                            $sel = $kond['jenis_bangunan'] ?? '';
                            foreach ($opsiBangunan as $i => $o): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_bangunan" id="jb_<?= $i ?>" value="<?= $o ?>" <?= ($o == $sel ? 'checked' : '') ?> <?= $disabled ?>>
                                    <label class="form-check-label" for="jb_<?= $i ?>"><?= $o ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- 🚀 RADIO (2): Tinggal Bersama Keluarga Lain? -->
                    <div class="col-md-6">
                        <label class="form-label text-primary">Tinggal Bersama Keluarga Lain?</label>
                        <div class="radio-group-box">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_tinggal_bersama" id="tb_tidak" value="Tidak" <?= ($kond['is_tinggal_bersama'] ?? '') == 'Tidak' ? 'checked' : '' ?> <?= $disabled ?>>
                                <label class="form-check-label" for="tb_tidak">Tidak (Sendiri)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_tinggal_bersama" id="tb_ya" value="Ya" <?= ($kond['is_tinggal_bersama'] ?? '') == 'Ya' ? 'checked' : '' ?> <?= $disabled ?>>
                                <label class="form-check-label" for="tb_ya">Ya</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BLOK JML KK & LIST INPUT KK LAINNYA -->
                <!-- ========================================== -->
                <div class="row g-3 mb-3 pb-3 border-bottom">
                    <div class="col-md-3" id="div_jumlah_kk" style="display: none;">
                        <label class="form-label text-primary">Jml KK dlm Rumah</label>
                        <input type="number" name="jumlah_kk_dalam_rumah" id="jumlah_kk_dalam_rumah" class="form-control border-primary" value="<?= esc($kond['jumlah_kk_dalam_rumah'] ?? '') ?>" <?= $readonly ?> placeholder="Misal: 1" min="1">
                    </div>

                    <!-- 🚀 ELEMEN DINAMIS: List Input Nomor KK -->
                    <div class="col-md-12 mt-2" id="div_list_kk_lainnya" style="display: none;">
                        <div class="p-3 bg-light border border-primary border-opacity-50 rounded">
                            <label class="form-label text-primary mb-3">
                                <i class="fas fa-info-circle me-1"></i> Tuliskan Nomor KK (16 Digit) dari keluarga selain Anda yang tinggal di rumah ini.
                            </label>
                            <!-- Di dalam div ini inputannya akan beranak-pinak -->
                            <div id="container_kk_lainnya" class="row g-2"></div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BLOK ORANG, KEPEMILIKAN, SEWA, & LUAS LANTAI -->
                <!-- ========================================== -->
                <div class="row g-3 mb-3 pb-3 border-bottom">

                    <!-- 1. Jml Orang dlm Rumah -->
                    <div class="col-md-2">
                        <label class="form-label text-primary">Jml Orang dlm Rumah</label>
                        <div class="input-group">
                            <!-- 🚀 Hapus bg-light dan readonly hardcode, ganti placeholder -->
                            <input type="number" min="1" name="jumlah_orang_dalam_rumah" id="jumlah_orang_dalam_rumah" class="form-control border-primary" value="<?= esc($kond['jumlah_orang_dalam_rumah'] ?? '') ?>" <?= $readonly ?> placeholder="Misal: 4">
                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#jumlah_orang_dalam_rumah" title="Salin Jml Orang"><i class="fas fa-copy"></i></button>
                        </div>
                        <!-- 🚀 Ganti keterangan kecilnya agar petugas paham -->
                        <small class="text-muted" style="font-size: 0.7rem;">*Total seluruh penghuni atap</small>
                    </div>

                    <!-- 2. Status Kepemilikan & Bukti -->
                    <div class="col-md-12">
                        <label class="form-label">Status Kepemilikan Rumah</label>
                        <div class="radio-group-box">
                            <?php $opsiPemilik = ['Milik Sendiri', 'Sewa', 'Kontrak', 'Bebas Sewa', 'Lainnya'];
                            $selpem = $kond['status_kepemilikan'] ?? $perumahan['status_kepemilikan'] ?? '';
                            foreach ($opsiPemilik as $i => $o): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input input-status-kepemilikan" type="radio" name="status_kepemilikan" id="sk_<?= $i ?>" value="<?= $o ?>" <?= ($o === $selpem ? 'checked' : '') ?> <?= $disabled ?>>
                                    <label class="form-check-label" for="sk_<?= $i ?>"><?= $o ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 🚀 ELEMEN DINAMIS: Bukti Kepemilikan -->
                    <div class="col-md-12 mt-2" id="div_bukti_kepemilikan" style="display: none;">
                        <label class="form-label text-info">Apa bukti kepemilikan tanah bangunan tempat tinggal yang Anda tempati? <span class="text-danger">*</span></label>
                        <div class="radio-group-box p-2">
                            <?php $opsiBukti = ['SHM', 'Sertifikat selain SHM (SHGB, SHSRS)', 'Surat bukti lainnya (Girik, Letter C, dll)', 'Tidak Punya'];
                            $selBukti = $kond['bukti_kepemilikan'] ?? '';
                            foreach ($opsiBukti as $i => $o): ?>
                                <div class="form-check">
                                    <input class="form-check-input input-bukti-kepemilikan" type="radio" name="bukti_kepemilikan" id="bk_<?= $i ?>" value="<?= $o ?>" <?= ($o == $selBukti ? 'checked' : '') ?> <?= $disabled ?>>
                                    <label class="form-check-label" for="bk_<?= $i ?>"><?= $o ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 3. Perkiraan Sewa/Bulan -->
                    <div class="col-md-4">
                        <label class="form-label text-primary">Perkiraan Sewa/Bulan</label>
                        <div class="input-group has-validation">
                            <input type="text" name="perkiraan_harga_sewa" id="perkiraan_harga_sewa" class="form-control rupiah border-primary" value="<?= esc($kond['perkiraan_harga_sewa'] ?? '') ?>" <?= $readonly ?> placeholder="Rp...">
                            <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#perkiraan_harga_sewa" title="Salin Harga Sewa"><i class="fas fa-copy"></i></button>
                            <!-- 🚀 ELEMEN PESAN ERROR REAL-TIME -->
                            <div class="invalid-feedback small fw-bold">
                                <i class="fas fa-exclamation-circle"></i> Harga sewa minimal Rp 50.000
                            </div>
                        </div>
                    </div>

                    <!-- 4. Luas Lantai -->
                    <div class="col-md-4">
                        <label class="form-label">Luas Lantai (m²)</label>
                        <div class="input-group has-validation">
                            <input type="number" step="0.01" min="0" name="luas_lantai" id="luas_lantai" class="form-control" value="<?= esc($kond['luas_lantai'] ?? '') ?>" <?= $readonly ?>>
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#luas_lantai" title="Salin Luas Lantai"><i class="fas fa-copy"></i></button>
                            <!-- 🚀 ELEMEN PESAN ERROR REAL-TIME -->
                            <div class="invalid-feedback small fw-bold">
                                <i class="fas fa-exclamation-circle"></i> Luas lantai minimal 4 m²
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis Lantai</label>
                        <select name="jenis_lantai" id="jenis_lantai" class="form-select" <?= $disabled ?>>
                            <?php $opsiLantai = ['Marmer/granit', 'Keramik', 'Parket/vinil/karpet', 'Ubin/tegal/teraso', 'Kayu', 'Semen', 'Bambu', 'Tanah', 'Lainnya'];
                            $sel = $kond['jenis_lantai'] ?? '';
                            echo '<option value="">[Pilih]</option>';
                            foreach ($opsiLantai as $o) echo "<option value=\"$o\" " . ($o == $sel ? 'selected' : '') . ">$o</option>"; ?>
                        </select>

                        <!-- 🚀 ELEMEN DINAMIS: Kondisi Lantai -->
                        <div id="div_kondisi_lantai" class="mt-2" style="display: none;">
                            <label class="form-label text-info">Kondisi Lantai <span class="text-danger">*</span></label>
                            <div class="radio-group-box p-2">
                                <?php $opsiKondisiLantai = ['Baik', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat'];
                                $selKondisi = $kond['kondisi_lantai'] ?? '';
                                foreach ($opsiKondisiLantai as $i => $o): ?>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input input-kondisi-lantai" type="radio" name="kondisi_lantai" id="kl_<?= $i ?>" value="<?= $o ?>" <?= ($o == $selKondisi ? 'checked' : '') ?> <?= $disabled ?>>
                                        <label class="form-check-label" for="kl_<?= $i ?>"><?= $o ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <!-- JENIS & KONDISI DINDING -->
                    <div class="col-md-4">
                        <label class="form-label">Jenis Dinding</label>
                        <select name="jenis_dinding" id="jenis_dinding" class="form-select" <?= $disabled ?>>
                            <?php $opsiDinding = ['Tembok', 'Plesteran anyaman bambu/kawat', 'Kayu/papan/gypsum/GRC/calciboard', 'Anyaman bambu', 'Batang kayu', 'Bambu', 'Lainnya'];
                            $sel = $kond['jenis_dinding'] ?? '';
                            echo '<option value="">[Pilih]</option>';
                            foreach ($opsiDinding as $o) echo "<option value=\"$o\" " . ($o == $sel ? 'selected' : '') . ">$o</option>"; ?>
                        </select>

                        <!-- 🚀 ELEMEN DINAMIS: Kondisi Dinding -->
                        <div id="div_kondisi_dinding" class="mt-2" style="display: none;">
                            <label class="form-label text-info">Kondisi Dinding <span class="text-danger">*</span></label>
                            <div class="radio-group-box p-2">
                                <?php $opsiKondisiDinding = ['Baik', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat'];
                                $selKondisiDinding = $kond['kondisi_dinding'] ?? '';
                                foreach ($opsiKondisiDinding as $i => $o): ?>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input input-kondisi-dinding" type="radio" name="kondisi_dinding" id="kd_<?= $i ?>" value="<?= $o ?>" <?= ($o == $selKondisiDinding ? 'checked' : '') ?> <?= $disabled ?>>
                                        <label class="form-check-label" for="kd_<?= $i ?>"><?= $o ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- JENIS & KONDISI ATAP -->
                    <div class="col-md-4">
                        <label class="form-label">Jenis Atap</label>
                        <select name="jenis_atap" id="jenis_atap" class="form-select" <?= $disabled ?>>
                            <?php $opsiAtap = ['Beton', 'Genteng', 'Seng', 'Asbes', 'Bambu', 'Kayu/sirap', 'Rumbia', 'Lainnya'];
                            $sel = $kond['jenis_atap'] ?? '';
                            echo '<option value="">[Pilih]</option>';
                            foreach ($opsiAtap as $o) echo "<option value=\"$o\" " . ($o == $sel ? 'selected' : '') . ">$o</option>"; ?>
                        </select>

                        <!-- 🚀 ELEMEN DINAMIS: Kondisi Atap -->
                        <div id="div_kondisi_atap" class="mt-2" style="display: none;">
                            <label class="form-label text-info">Kondisi Atap <span class="text-danger">*</span></label>
                            <div class="radio-group-box p-2">
                                <?php $opsiKondisiAtap = ['Baik', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat'];
                                $selKondisiAtap = $kond['kondisi_atap'] ?? '';
                                foreach ($opsiKondisiAtap as $i => $o): ?>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input input-kondisi-atap" type="radio" name="kondisi_atap" id="ka_<?= $i ?>" value="<?= $o ?>" <?= ($o == $selKondisiAtap ? 'checked' : '') ?> <?= $disabled ?>>
                                        <label class="form-check-label" for="ka_<?= $i ?>"><?= $o ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. SARANA SANITASI -->
        <!-- ========================================== -->
        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>2. Sarana Sanitasi</strong></div>
            <div class="card-body">
                <div class="row g-3">

                    <!-- 🚀 RADIO (6): Fasilitas BAB -->
                    <div class="col-md-12">
                        <label class="form-label">Kepemilikan Fasilitas BAB</label>
                        <div class="radio-group-box">
                            <?php $opsiBab = ['Ada, digunakan hanya Anggota Keluarga sendiri', 'Ada, digunakan bersama Anggota Keluarga dari Keluarga tertentu', 'Ada, di MCK komunal', 'Ada, di MCK umum/siapapun menggunakan', 'Ada, Anggota Keluarga tidak menggunakan', 'Tidak ada fasilitas'];
                            $sel = $san['fasilitas_bab'] ?? '';
                            foreach ($opsiBab as $i => $o): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fasilitas_bab" id="fb_<?= $i ?>" value="<?= $o ?>" <?= ($o == $sel ? 'checked' : '') ?> <?= $disabled ?>>
                                    <label class="form-check-label" for="fb_<?= $i ?>"><?= $o ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 🚀 RADIO (4): Kloset -->
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kloset</label>
                        <div class="radio-group-box">
                            <?php $opsiKloset = ['Leher angsa', 'Plengsengan dengan tutup', 'Plengsengan tanpa tutup', 'Cemplung'];
                            $sel = $san['jenis_kloset'] ?? '';
                            foreach ($opsiKloset as $i => $o): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_kloset" id="jk_<?= $i ?>" value="<?= $o ?>" <?= ($o == $sel ? 'checked' : '') ?> <?= $disabled ?>>
                                    <label class="form-check-label" for="jk_<?= $i ?>"><?= $o ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 🚀 RADIO (6): Pembuangan Tinja -->
                    <div class="col-md-6">
                        <label class="form-label">Tempat Pembuangan Akhir Tinja</label>
                        <div class="radio-group-box">
                            <?php $opsiTinja = ['Tangki septik', 'IPAL', 'Kolam/sawah/sungai/danau/laut', 'Lubang tanah', 'Pantai/tanah lapang/kebun', 'Lainnya'];
                            $sel = $san['pembuangan_tinja'] ?? '';
                            foreach ($opsiTinja as $i => $o): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pembuangan_tinja" id="pt_<?= $i ?>" value="<?= $o ?>" <?= ($o == $sel ? 'checked' : '') ?> <?= $disabled ?>>
                                    <label class="form-check-label" for="pt_<?= $i ?>"><?= $o ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- 🚀 RADIO (3): Jarak Limbah -->
                    <div class="col-md-12 mt-2">
                        <label class="form-label">Jarak Sumber Air ke Pembuangan Limbah</label>
                        <div class="radio-group-box">
                            <?php $opsiJarak = ['< 10 meter', '>= 10 meter', 'Tidak tahu'];
                            $sel = $san['jarak_air_ke_limbah'] ?? '';
                            foreach ($opsiJarak as $i => $o): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jarak_air_ke_limbah" id="ja_<?= $i ?>" value="<?= $o ?>" <?= ($o == $sel ? 'checked' : '') ?> <?= $disabled ?>>
                                    <label class="form-check-label" for="ja_<?= $i ?>"><?= $o ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 3. SARANA ENERGI -->
        <!-- ========================================== -->
        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>3. Sarana Energi</strong></div>
            <div class="card-body">

                <div class="row g-3 mb-3 pb-3 border-bottom">

                    <div class="col-md-4">
                        <label class="form-label">Bahan Bakar Utama Memasak</label>
                        <select name="bahan_bakar" id="bahan_bakar" class="form-select" <?= $disabled ?>>
                            <?php $opsiBakar = ['Tidak memasak di rumah', 'Listrik', 'Gas elpiji 5,5kg/blue', 'Gas elpiji 12 kg', 'Gas elpiji 3 kg', 'Gas kota/meteran PGN', 'Biogas', 'Minyak tanah', 'Briket', 'Arang', 'Kayu bakar', 'Lainnya'];
                            $sel = $kond['bahan_bakar'] ?? '';
                            echo '<option value="">[Pilih]</option>';
                            foreach ($opsiBakar as $o) echo "<option value=\"$o\" " . ($o == $sel ? 'selected' : '') . ">$o</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sumber Air Minum</label>
                        <select name="sumber_air" id="sumber_air" class="form-select" <?= $disabled ?>>
                            <?php $opsiAir = ['Air kemasan bermerk', 'Air isi ulang', 'Leding', 'Sumur bor/pompa', 'Sumur terlindung', 'Sumur tak terlindung', 'Mata air terlindung', 'Mata air tak terlindung', 'Air permukaan (sungai/danau/waduk/kolam/irigasi)', 'Air hujan', 'Lainnya'];
                            $sel = $kond['sumber_air'] ?? '';
                            echo '<option value="">[Pilih]</option>';
                            foreach ($opsiAir as $o) echo "<option value=\"$o\" " . ($o == $sel ? 'selected' : '') . ">$o</option>"; ?>
                        </select>
                    </div>
                </div>

                <!-- 🚀 RADIO (4): Sumber Listrik -->
                <div class="row g-3 mt-2">
                    <div class="col-md-12">
                        <label class="form-label">Sumber Penerangan Utama</label>
                        <div class="radio-group-box">
                            <?php $opsiListrik = ['Listrik PLN dengan meteran', 'Listrik PLN tanpa meteran', 'Listrik non-PLN', 'Bukan listrik'];
                            $sel = $kond['sumber_listrik'] ?? '';
                            foreach ($opsiListrik as $i => $o): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input input-listrik" type="radio" name="sumber_listrik" id="sl_<?= $i ?>" value="<?= $o ?>" <?= ($o == $sel ? 'checked' : '') ?> <?= $disabled ?>>
                                    <label class="form-check-label" for="sl_<?= $i ?>"><?= $o ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Info Tambahan PLN -->
                <div id="div_info_listrik" class="mt-2 p-3 bg-light border rounded" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-primary">Jumlah Meteran Listrik</label>
                            <div class="input-group">
                                <input type="number" name="jumlah_meteran_listrik" id="jumlah_meteran_listrik" class="form-control border-primary" value="<?= esc($kond['jumlah_meteran_listrik'] ?? '1') ?>" <?= $readonly ?> placeholder="Misal: 1" min="1" max="10">
                                <button class="btn btn-outline-primary btn-copy-input" type="button" data-target="#jumlah_meteran_listrik" title="Salin Jumlah Meteran"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- 🚀 ELEMEN DINAMIS: List Input Meteran -->
                    <div id="container_meteran_listrik" class="mt-3"></div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 4. PENGELUARAN & PENDAPATAN -->
        <!-- ========================================== -->
        <div class="card shadow-sm mb-4">
            <div class="card-header"><strong>4. Pengeluaran & Pendapatan</strong></div>
            <div class="card-body">
                <h6 class="fw-bold text-secondary mb-3">A. Rincian Pengeluaran Rutin</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Listrik per Bulan (Rp)</label>
                        <div class="input-group has-validation">
                            <input type="text" name="pengeluaran_listrik" id="pengeluaran_listrik" class="form-control rupiah" value="<?= esc($se['pengeluaran_listrik'] ?? '') ?>" <?= $readonly ?> placeholder="Cth: 10000">
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#pengeluaran_listrik" title="Salin Listrik"><i class="fas fa-copy"></i></button>
                            <!-- <div class="invalid-feedback small fw-bold"><i class="fas fa-exclamation-circle"></i> Minimal Rp 1.000</div> -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pulsa per Bulan (Rp)</label>
                        <div class="input-group has-validation">
                            <input type="text" name="pengeluaran_pulsa" id="pengeluaran_pulsa" class="form-control rupiah" value="<?= esc($se['pengeluaran_pulsa'] ?? '') ?>" <?= $readonly ?> placeholder="Cth: 10000 atau 0">
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#pengeluaran_pulsa" title="Salin Pulsa"><i class="fas fa-copy"></i></button>
                            <!-- <div class="invalid-feedback small fw-bold"><i class="fas fa-exclamation-circle"></i> Minimal Rp 1.000</div> -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Internet per Bulan (Rp)</label>
                        <div class="input-group has-validation">
                            <input type="text" name="pengeluaran_internet" id="pengeluaran_internet" class="form-control rupiah" value="<?= esc($se['pengeluaran_internet'] ?? '') ?>" <?= $readonly ?> placeholder="Cth: 10000 atau 0">
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#pengeluaran_internet" title="Salin Internet"><i class="fas fa-copy"></i></button>
                            <!-- <div class="invalid-feedback small fw-bold"><i class="fas fa-exclamation-circle"></i> Minimal Rp 1.000</div> -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Makan per <u>Minggu</u> (Rp)</label>
                        <div class="input-group">
                            <input type="text" name="pengeluaran_makan_mingguan" id="pengeluaran_makan_mingguan" class="form-control rupiah" value="<?= esc($se['pengeluaran_makan_mingguan'] ?? '') ?>" <?= $readonly ?> placeholder="0">
                            <!-- 🚀 TOMBOL KALKULATOR MAKAN MINGGUAN -->
                            <button class="btn btn-outline-info" type="button" onclick="bukaKalkulator('mingguan_makan', '#pengeluaran_makan_mingguan')" <?= $readonly ?> title="Hitung Makan Mingguan"><i class="fas fa-calculator"></i></button>
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#pengeluaran_makan_mingguan" title="Salin Makan Mingguan"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-danger">Bukan Makanan Rutin <u>Bulanan</u> (Rp)</label>
                        <div class="input-group has-validation">
                            <input type="text" name="pengeluaran_non_makan_bulanan" id="pengeluaran_non_makan_bulanan" class="form-control rupiah border-danger" value="<?= esc($se['pengeluaran_non_makan_bulanan'] ?? '') ?>" <?= $readonly ?> placeholder="Listrik + Pulsa + Internet">

                            <!-- 🚀 TOMBOL KALKULATOR -->
                            <button class="btn btn-outline-info btn-calc" type="button" data-target="#pengeluaran_non_makan_bulanan" data-title="Kalkulator Rutin Bulanan" title="Hitung Akumulasi Bulanan" <?= $readonly ?>><i class="fas fa-calculator"></i></button>

                            <button class="btn btn-outline-danger btn-copy-input" type="button" data-target="#pengeluaran_non_makan_bulanan" title="Salin Bukan Makanan Bulanan"><i class="fas fa-copy"></i></button>
                            <!-- 🚀 PESAN ERROR DINAMIS -->
                            <div id="feedback_non_makan" class="invalid-feedback small fw-bold"></div>
                        </div>
                        <small class="text-muted" style="font-size: 0.7rem;">(Min. Akumulasi Listrik, Pulsa & Internet)</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bukan Makanan Rutin <u>Tahunan</u> (Rp)</label>
                        <div class="input-group">
                            <input type="text" name="pengeluaran_non_makan_tahunan" id="pengeluaran_non_makan_tahunan" class="form-control rupiah" value="<?= esc($se['pengeluaran_non_makan_tahunan'] ?? '') ?>" <?= $readonly ?> placeholder="0">

                            <!-- 🚀 TOMBOL KALKULATOR -->
                            <button class="btn btn-outline-info btn-calc" type="button" data-target="#pengeluaran_non_makan_tahunan" data-title="Kalkulator Rutin Tahunan" title="Hitung Akumulasi Tahunan" <?= $readonly ?>><i class="fas fa-calculator"></i></button>

                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#pengeluaran_non_makan_tahunan" title="Salin Bukan Makanan Tahunan"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-secondary mb-3 border-top pt-3">B. Rincian Pendapatan / Penghasilan Bulanan</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Gaji / Upah Utama (Rp)</label>
                        <div class="input-group">
                            <input type="text" name="pendapatan_gaji" id="pendapatan_gaji" class="form-control rupiah" value="<?= esc($se['pendapatan_gaji'] ?? '') ?>" <?= $readonly ?> placeholder="0">
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#pendapatan_gaji" title="Salin Gaji"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hasil Usaha / Bisnis (Rp)</label>
                        <div class="input-group">
                            <input type="text" name="pendapatan_usaha" id="pendapatan_usaha" class="form-control rupiah" value="<?= esc($se['pendapatan_usaha'] ?? '') ?>" <?= $readonly ?> placeholder="0">
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#pendapatan_usaha" title="Salin Hasil Usaha"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Lainnya (Pensiun/Kupon/Dll) (Rp)</label>
                        <div class="input-group">
                            <input type="text" name="pendapatan_lainnya" id="pendapatan_lainnya" class="form-control rupiah" value="<?= esc($se['pendapatan_lainnya'] ?? '') ?>" <?= $readonly ?> placeholder="0">
                            <button class="btn btn-outline-secondary btn-copy-input" type="button" data-target="#pendapatan_lainnya" title="Salin Lainnya"><i class="fas fa-copy"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($editable): ?>
            <div class="text-end mt-4">
                <button type="button" id="btnSimpanRumah" class="btn btn-success rounded-pill px-4 shadow-sm">
                    <i class="fas fa-save me-1"></i> Simpan Data Rumah & SosEk
                </button>
            </div>
        <?php endif; ?>

    </form>
</div>

<!-- 🚀 MODAL KALKULATOR AKUMULASI BPS -->
<div class="modal fade" id="modalKalkulator" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg border-info">
            <div class="modal-header bg-info text-white py-2">
                <h6 class="modal-title font-weight-bold" id="modalKalkulatorTitle"><i class="fas fa-calculator mr-1"></i> Kalkulator</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div id="kalkulator_inputs">
                    <!-- Baris input akan di-generate oleh JS -->
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-2 font-weight-bold border-dashed" id="btn_add_calc_row">
                    <i class="fas fa-plus mr-1"></i> Tambah Item Lainnya
                </button>
                <hr>
                <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border border-success">
                    <span class="font-weight-bold text-dark">Total:</span>
                    <span class="font-weight-bold text-success" id="kalkulator_total" style="font-size: 1.1rem;">Rp 0</span>
                </div>
            </div>
            <div class="modal-footer py-2">
                <input type="hidden" id="kalkulator_target_input">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-info font-weight-bold" id="btn_apply_kalkulator"><i class="fas fa-check mr-1"></i> Terapkan</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================== -->
<!-- DEPENDENCIES & SCRIPTS -->
<!-- ============================== -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editable = <?= $editable ? 'true' : 'false' ?>;

        // ============ TINGGAL BERSAMA DYNAMIC ============
        function toggleJumlahKK() {
            const val = $('input[name="is_tinggal_bersama"]:checked').val();
            if (val === 'Ya') {
                $('#div_jumlah_kk').slideDown();
                renderKKLainnya();
            } else {
                $('#div_jumlah_kk').slideUp();
                $('#div_list_kk_lainnya').slideUp();
                $('#jumlah_kk_dalam_rumah').val('');
                $('#container_kk_lainnya').empty();
            }
        }
        $('input[name="is_tinggal_bersama"]').on('change', toggleJumlahKK);

        // ============ RENDER NOMOR KK DINAMIS ============
        // Tangkap data lama jika sudah pernah tersimpan di database (dipisah koma)
        const savedKKLainnya = <?= json_encode(!empty($kond['no_kk_lainnya']) ? explode(',', $kond['no_kk_lainnya']) : []) ?>;

        function renderKKLainnya() {
            const container = $('#container_kk_lainnya');
            const count = parseInt($('#jumlah_kk_dalam_rumah').val()) || 0;

            // Amankan nilai yang sedang diketik user agar tidak hilang saat kotak ber-render ulang
            let currentValues = [];
            $('.input-kk-lainnya').each(function(idx) {
                currentValues[idx] = $(this).val();
            });

            container.empty();

            if (count > 0) {
                $('#div_list_kk_lainnya').slideDown();
                for (let i = 0; i < count; i++) {
                    // Prioritaskan ketikan user, jika kosong baru ambil dari database
                    let val = currentValues[i] || savedKKLainnya[i] || '';

                    // 🚀 TAMBAHKAN TOMBOL SALIN BIAR MAKIN PREMIUM
                    container.append(`
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">No. KK Keluarga ke-${i+1} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" id="no_kk_lainnya_${i}" name="no_kk_lainnya[]" class="form-control form-control-sm border-primary input-kk-lainnya" value="${val}" maxlength="16" placeholder="16 Digit Nomor KK">
                                <button class="btn btn-outline-primary btn-sm btn-copy-input" type="button" data-target="#no_kk_lainnya_${i}" title="Salin KK"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                    `);
                }
            } else {
                $('#div_list_kk_lainnya').slideUp();
            }
        }

        // 🚀 INI DIA KUNCI JAWABANNYA: Trigger fungsi render saat kotak angka diketik/diubah!
        $('#jumlah_kk_dalam_rumah').on('input change', renderKKLainnya);

        // Inisialisasi awal saat halaman diload
        toggleJumlahKK();

        // Panggil fungsi render tiap kali angka jumlah KK diubah
        $('#jumlah_kk_dalam_rumah').on('input change', renderKKLainnya);

        // Delegasi event khusus untuk field dinamis (Paksa 16 digit & hanya angka)
        $(document).on('input', '.input-kk-lainnya', function() {
            this.value = this.value.replace(/\\D/g, ''); // Sikat bersih selain angka
            if (this.value.length > 16) this.value = this.value.slice(0, 16);
            checkKelengkapanRumah(); // Lapor ke Satpam Kelengkapan
        });

        // ============ KONDISI LANTAI DYNAMIC ============
        function toggleKondisiLantai() {
            const val = $('#jenis_lantai').val();
            if (val && val !== '') {
                $('#div_kondisi_lantai').slideDown();
            } else {
                $('#div_kondisi_lantai').slideUp();
                $('input[name="kondisi_lantai"]').prop('checked', false); // Hapus pilihan jika Jenis Lantai dikosongkan
            }
        }
        $('#jenis_lantai').on('change', toggleKondisiLantai);
        toggleKondisiLantai(); // Panggil saat halaman diload

        // ============ KONDISI DINDING & ATAP DYNAMIC ============
        function toggleKondisiDinding() {
            if ($('#jenis_dinding').val() && $('#jenis_dinding').val() !== '') {
                $('#div_kondisi_dinding').slideDown();
            } else {
                $('#div_kondisi_dinding').slideUp();
                $('input[name="kondisi_dinding"]').prop('checked', false); // Hapus pilihan jika dikosongkan
            }
        }
        $('#jenis_dinding').on('change', toggleKondisiDinding);
        toggleKondisiDinding();

        function toggleKondisiAtap() {
            if ($('#jenis_atap').val() && $('#jenis_atap').val() !== '') {
                $('#div_kondisi_atap').slideDown();
            } else {
                $('#div_kondisi_atap').slideUp();
                $('input[name="kondisi_atap"]').prop('checked', false); // Hapus pilihan jika dikosongkan
            }
        }
        $('#jenis_atap').on('change', toggleKondisiAtap);
        toggleKondisiAtap();

        // ============ BUKTI KEPEMILIKAN DYNAMIC ============
        function toggleBuktiKepemilikan() {
            const val = $('input[name="status_kepemilikan"]:checked').val();
            if (val === 'Milik Sendiri') {
                $('#div_bukti_kepemilikan').slideDown();
            } else {
                $('#div_bukti_kepemilikan').slideUp();
                $('input[name="bukti_kepemilikan"]').prop('checked', false); // Bersihkan isian jika disembunyikan
            }
        }
        $('.input-status-kepemilikan').on('change', toggleBuktiKepemilikan);
        toggleBuktiKepemilikan(); // Panggil saat halaman diload

        // ============ RENDER METERAN LISTRIK DINAMIS ============
        // Tangkap data lama dari database jika ada (dipisah koma)
        const savedNoPelanggan = <?= json_encode(!empty($kond['nomor_pelanggan']) ? explode(',', $kond['nomor_pelanggan']) : []) ?>;
        const savedNoMeter = <?= json_encode(!empty($kond['nomor_meter']) ? explode(',', $kond['nomor_meter']) : []) ?>;
        const savedDaya = <?= json_encode(!empty($kond['daya_listrik']) ? explode(',', $kond['daya_listrik']) : []) ?>;

        function renderMeteranListrik() {
            const container = $('#container_meteran_listrik');
            let count = parseInt($('#jumlah_meteran_listrik').val()) || 0;

            // Batasi agar user tidak iseng mengisi angka terlalu besar (misal 100)
            if (count > 10) {
                count = 10;
                $('#jumlah_meteran_listrik').val(10);
            }

            // Amankan nilai yang sedang diketik user agar tidak hilang saat re-render
            let currPelanggan = [];
            let currMeter = [];
            let currDaya = [];
            $('.input-pelanggan').each(function(i) {
                currPelanggan[i] = $(this).val();
            });
            $('.input-meter').each(function(i) {
                currMeter[i] = $(this).val();
            });
            $('.input-daya').each(function(i) {
                currDaya[i] = $(this).val();
            });

            container.empty();

            if (count > 0) {
                for (let i = 0; i < count; i++) {
                    let valPel = currPelanggan[i] || savedNoPelanggan[i] || '';
                    let valMet = currMeter[i] || savedNoMeter[i] || '';
                    let valDay = currDaya[i] || savedDaya[i] || '';

                    // Buat Opsi Dropdown Daya
                    let opsiDaya = ['TANPA METERAN', '450 WATT', '900 WATT', '1.300 WATT', '2.200 WATT', '> 2.200 WATT', '< = 900 WATT', '> 900 WATT'];
                    let optHtml = '<option value="">[Pilih]</option>';
                    opsiDaya.forEach(o => {
                        optHtml += `<option value="${o}" ${o === valDay ? 'selected' : ''}>${o}</option>`;
                    });

                    container.append(`
                        <div class="row g-2 mb-2 pb-2 ${i < count - 1 ? 'border-bottom' : ''}">
                            <div class="col-12"><label class="form-label small fw-bold text-secondary mb-0">Meteran ke-${i+1}</label></div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" id="nomor_pelanggan_${i}" name="nomor_pelanggan[]" class="form-control form-control-sm onlynum input-pelanggan" value="${valPel}" placeholder="Nomor Pelanggan PLN">
                                    <button class="btn btn-outline-secondary btn-sm btn-copy-input" type="button" data-target="#nomor_pelanggan_${i}" title="Salin Nomor Pelanggan"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" id="nomor_meter_${i}" name="nomor_meter[]" class="form-control form-control-sm onlynum input-meter" value="${valMet}" placeholder="Nomor Meter PLN">
                                    <button class="btn btn-outline-secondary btn-sm btn-copy-input" type="button" data-target="#nomor_meter_${i}" title="Salin Nomor Meter"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select name="daya_listrik[]" class="form-select form-select-sm input-daya">
                                    ${optHtml}
                                </select>
                            </div>
                        </div>
                    `);
                }
            }
        }

        $('#jumlah_meteran_listrik').on('input change', renderMeteranListrik);

        // ============ LISTRIK DYNAMIC (RADIO VERSION) ============
        function toggleListrikFields() {
            const val = $('input[name="sumber_listrik"]:checked').val();
            if (val === 'Listrik PLN dengan meteran') {
                $('#div_info_listrik').slideDown();
                if (!$('#jumlah_meteran_listrik').val()) $('#jumlah_meteran_listrik').val(1);
                renderMeteranListrik(); // Panggil fungsi render
            } else {
                $('#div_info_listrik').slideUp();
                $('#jumlah_meteran_listrik').val('');
                $('#container_meteran_listrik').empty(); // Hapus anak-anaknya
            }
        }
        $('.input-listrik').on('change', toggleListrikFields);
        toggleListrikFields();

        // ============ HELPER ANGKA ============
        const getInt = (selector) => {
            let val = $(selector).val() || '0';
            return parseInt(val.replace(/\D/g, '')) || 0;
        };

        // ============ VALIDASI REAL-TIME: HARGA SEWA ============
        $('#perkiraan_harga_sewa').on('input change', function() {
            const sewa = getInt(this); // Mengambil nilai angka bersih

            if (sewa > 0 && sewa < 50000) {
                // Jika isian di bawah 50rb, ubah kotak jadi merah dan munculkan pesan
                $(this).addClass('is-invalid border-danger').removeClass('border-primary');
            } else {
                // Jika sudah 50rb atau kosong, hapus warna merah
                $(this).removeClass('is-invalid border-danger').addClass('border-primary');
            }
        });

        // Panggil fungsi ini sekali saat halaman baru dibuka (berjaga-jaga jika data lamanya salah)
        $('#perkiraan_harga_sewa').trigger('input');

        // ============ VALIDASI REAL-TIME: ATURAN NASIONAL ============
        $('#luas_lantai').on('input change', function() {
            const luas = parseFloat($(this).val()) || 0;
            if (luas > 0 && luas < 4) $(this).addClass('is-invalid border-danger');
            else $(this).removeClass('is-invalid border-danger');
        });

        // 🚀 SINKRONISASI PENGELUARAN DENGAN SISTEM DELTA (AKUMULASI PINTAR ALA EXCEL)
        // 1. Rekam nilai asli saat halaman pertama kali diload agar tidak merusak data lama
        $('#pengeluaran_listrik, #pengeluaran_pulsa, #pengeluaran_internet').each(function() {
            $(this).data('prev-val', getInt(this));
        });

        $('#pengeluaran_listrik, #pengeluaran_pulsa, #pengeluaran_internet').on('input', function(e) {
            // Validasi minimal Rp 1.000
            const currentVal = getInt(this);
            if (currentVal > 0 && currentVal < 1000) {
                $(this).addClass('is-invalid border-danger').removeClass('border-primary');
            } else {
                $(this).removeClass('is-invalid border-danger').addClass('border-primary');
            }

            // 2. Tambahkan SELISIH (Delta) hanya jika user benar-benar mengetik manual (bukan karena load sistem)
            if (e.originalEvent) {
                const prevVal = $(this).data('prev-val') || 0;
                const delta = currentVal - prevVal; // Hitung penambahan/pengurangannya saja

                if (delta !== 0) {
                    const currentNonMakan = getInt('#pengeluaran_non_makan_bulanan');
                    const newNonMakan = currentNonMakan + delta;

                    // Tembakkan hasil rumus ke kolom Bukan Makanan
                    if (newNonMakan > 0) {
                        $('#pengeluaran_non_makan_bulanan').val(new Intl.NumberFormat('id-ID').format(newNonMakan));
                    } else {
                        $('#pengeluaran_non_makan_bulanan').val('');
                    }

                    // Simpan angka terakhir sebagai patokan untuk ketikan selanjutnya
                    $(this).data('prev-val', currentVal);
                }
            }

            // Panggil validasi diri sendiri untuk kolom Bukan Makanan
            $('#pengeluaran_non_makan_bulanan').trigger('change');
        });

        $('#pengeluaran_non_makan_bulanan').on('input change', function() {
            const listrik = getInt('#pengeluaran_listrik');
            const pulsa = getInt('#pengeluaran_pulsa');
            const internet = getInt('#pengeluaran_internet');
            const minNonMakan = listrik + pulsa + internet;
            const nonMakanBln = getInt(this);

            if (nonMakanBln > 0 && nonMakanBln < minNonMakan) {
                $(this).addClass('is-invalid border-danger').removeClass('border-primary');
                $('#feedback_non_makan').html(`<i class="fas fa-exclamation-circle"></i> Tidak boleh lebih kecil dari Rp ${new Intl.NumberFormat('id-ID').format(minNonMakan)}`);
            } else {
                $(this).removeClass('is-invalid border-danger').addClass('border-primary');
            }
        });

        // ==========================================
        // 🚀 FUNGSI VALIDASI CERDAS (ATURAN NASIONAL)
        // ==========================================
        function validasiAturanNasional() {
            // Karena sekarang UI sudah real-time memberi class "is-invalid", 
            // Satpam di tombol Simpan hanya perlu mendeteksi keberadaan class tersebut.
            if ($('#formRumahFull .is-invalid').length > 0) {
                return 'Terdapat isian yang tidak memenuhi standar (kotak bergaris merah dan peringatan di bawahnya). Mohon perbaiki nominal tersebut sebelum menyimpan.';
            }
            return true;
        }

        // Panggil trigger awal untuk merender error jika ada data lama yang kurang tepat dari database
        setTimeout(() => {
            $('#luas_lantai, #pengeluaran_listrik, #pengeluaran_pulsa, #pengeluaran_internet, #pengeluaran_non_makan_bulanan').trigger('change');
        }, 500);

        // ============ KELENGKAPAN CHECK ============
        const requiredSelects = ['#luas_lantai', '#jenis_lantai', '#jenis_atap', '#bahan_bakar', '#sumber_air'];
        // 🚀 BUG FIX: Tambahkan jenis_bangunan dan is_tinggal_bersama ke radar wajib
        const requiredRadios = ['jenis_bangunan', 'is_tinggal_bersama', 'status_kepemilikan', 'sumber_listrik', 'fasilitas_bab'];

        function checkKelengkapanRumah() {
            let missing = [];

            // Cek Input / Select
            requiredSelects.forEach(sel => {
                const el = document.querySelector(sel);
                if (!el || el.value === null || el.value === '' || el.value === '0') missing.push(sel);
            });

            // Cek Radio Buttons
            requiredRadios.forEach(name => {
                if (!$(`input[name="${name}"]:checked`).val()) missing.push(name);
            });

            // Logika Khusus Kondisi Lantai, Dinding, Atap
            if ($('#jenis_lantai').val() !== '') {
                if (!$('input[name="kondisi_lantai"]:checked').val()) missing.push('kondisi_lantai');
            }
            if ($('#jenis_dinding').val() !== '') {
                if (!$('input[name="kondisi_dinding"]:checked').val()) missing.push('kondisi_dinding');
            }
            if ($('#jenis_atap').val() !== '') {
                if (!$('input[name="kondisi_atap"]:checked').val()) missing.push('kondisi_atap');
            }

            // Logika Khusus Bukti Kepemilikan
            if ($('input[name="status_kepemilikan"]:checked').val() === 'Milik Sendiri') {
                if (!$('input[name="bukti_kepemilikan"]:checked').val()) missing.push('bukti_kepemilikan');
            }

            // Logika Khusus Jumlah KK & Input KK Lainnya
            if ($('input[name="is_tinggal_bersama"]:checked').val() === 'Ya') {
                const jml = parseInt($('#jumlah_kk_dalam_rumah').val()) || 0;
                if (jml <= 0) missing.push('#jumlah_kk_dalam_rumah');

                // Cek apakah ada inputan KK yg digitnya kurang dari 16 atau kosong
                $('.input-kk-lainnya').each(function() {
                    if ($(this).val().length !== 16) {
                        missing.push('no_kk_lainnya_tidak_valid');
                    }
                });
            }

            // 🚀 BUG FIX: Logika Khusus PLN (Versi Dinamis)
            if ($('input[name="sumber_listrik"]:checked').val() === 'Listrik PLN dengan meteran') {
                if (!$('#jumlah_meteran_listrik').val()) missing.push('#jumlah_meteran_listrik');

                // Pastikan Daya Listrik tiap meteran sudah dipilih (Nomor pelanggan boleh opsional/kosong jika tidak tahu)
                $('.input-daya').each(function() {
                    if (!$(this).val()) missing.push('daya_listrik_kosong');
                });
            }

            const badge = $('#badgeRumah');
            if (missing.length === 0) {
                badge.removeClass('bg-danger bg-secondary').addClass('bg-success').text('Lengkap');
            } else {
                badge.removeClass('bg-success bg-secondary').addClass('bg-danger').text('Belum Lengkap');
            }
            return missing;
        }

        // Jalankan pengecekan real-time
        requiredSelects.concat(['#jumlah_meteran_listrik']).forEach(s => {
            $(document).on('change input', s, checkKelengkapanRumah);
        });

        requiredRadios.forEach(s => {
            $(document).on('change', `input[name="${s}"]`, checkKelengkapanRumah);
        });

        // 🚀 Trigger untuk semua elemen radio dan input dinamis
        $(document).on('change input', '.input-kk-lainnya, .input-daya', checkKelengkapanRumah);
        $('.input-kondisi-lantai, .input-kondisi-dinding, .input-kondisi-atap, .input-bukti-kepemilikan').on('change', checkKelengkapanRumah);

        setTimeout(checkKelengkapanRumah, 250);

        // ============ SUBMIT HANDLER ============
        $('#btnSimpanRumah').on('click', function() {
            const missing = checkKelengkapanRumah();
            if (missing.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Isian Belum Lengkap',
                    text: 'Silakan isi kolom-kolom yang masih kosong.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const cekAturan = validasiAturanNasional();
            if (cekAturan !== true) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Data Gagal',
                    html: cekAturan,
                    confirmButtonText: 'Perbaiki'
                });
                return;
            }

            const form = document.getElementById('formRumahFull');
            const formData = new FormData(form);

            document.querySelectorAll('#formRumahFull .rupiah').forEach(el => {
                let cleanVal = el.value.replace(/\D/g, '');
                formData.set(el.name, cleanVal);
            });

            if (!formData.get('luas_lantai')) formData.set('luas_lantai', '');

            Swal.fire({
                title: 'Menyimpan data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('/pembaruan-keluarga/save-rumah', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            }).then(resp => resp.json()).then(res => {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            }).catch(err => Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error'));
        });
    });

    // =======================================================
    // 📋 FUNGSI SALIN KE CLIPBOARD (VERSI DELEGASI JQUERY)
    // =======================================================
    $(document).on('click', '.btn-copy-input', function() {
        const targetSelector = $(this).attr('data-target');
        const inputEl = $(targetSelector);

        if (inputEl.length && inputEl.val().trim() !== '') {
            navigator.clipboard.writeText(inputEl.val().trim()).then(() => {
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
</script>