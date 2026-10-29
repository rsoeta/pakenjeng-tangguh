<?= $this->extend('templates/index'); ?>

<?= $this->section('content') ?>

<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><i class="fas fa-file-signature text-primary mr-2"></i>Form Asesmen PMKS Lapad Ruhama</h1>
                    <small class="text-muted">Pemberian Rekomendasi & Pelayanan Kesejahteraan Sosial Kabupaten Garut</small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('assessment'); ?>">Assessment</a></li>
                        <li class="breadcrumb-item active">Buat Baru</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">

            <form id="formAssessment" enctype="multipart/form-data">
                <?= csrf_field(); ?>

                <!-- CARD 1: PENCARIAN KELUARGA (SMART AUTOCOMPLETE) -->
                <div class="card card-primary card-outline shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title font-weight-bold text-primary m-0">
                            <i class="fas fa-search mr-1"></i> Langkah 1: Temukan Keluarga Sasaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row align-items-center">
                            <label class="col-md-3 col-form-label font-weight-bold">
                                Cari Nomor KK / Nama / NIK:
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <select id="searchKkSelect" class="form-control select2bs4" style="width: 100%;">
                                    <option value="">-- Ketik minimal 3 digit No KK, NIK, atau Nama Kepala Keluarga --</option>
                                </select>
                                <small class="text-muted font-italic">
                                    <i class="fas fa-info-circle mr-1"></i> Data otomatis ditarik dari basis data DTSEN SINDEN. Begitu KK dipilih, biodata, desil, dan susunan keluarga langsung terisi otomatis.
                                </small>
                            </div>
                        </div>

                        <!-- Panel Detail Singkat Keluarga Terpilih -->
                        <div id="panelKeluarga" class="d-none mt-3">
                            <div class="alert alert-light border p-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Nomor Kartu Keluarga (KK)</small>
                                        <strong id="prevNoKk" class="text-primary h6">-</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Nama Kepala Keluarga</small>
                                        <strong id="prevKepalaKeluarga" class="h6">-</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Peringkat Desil Kesejahteraan</small>
                                        <span id="prevDesil" class="badge badge-danger text-sm">Desil -</span>
                                    </div>
                                    <div class="col-md-8 mt-2">
                                        <small class="text-muted d-block">Alamat Lengkap</small>
                                        <span id="prevAlamat">-</span>
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <small class="text-muted d-block">Program Bansos Aktif</small>
                                        <span id="prevBansos" class="badge badge-info">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="id_kk" id="id_kk">
                    </div>
                </div>

                <!-- CARD 2: INSTRUMEN ASESMEN & SASARAN -->
                <div id="panelInstrumen" class="d-none">

                    <!-- BAGIAN I & II: SASARAN & PERNYATAAN -->
                    <div class="card card-outline card-info shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title font-weight-bold text-info m-0">
                                <i class="fas fa-user-injured mr-1"></i> Langkah 2: Identitas Sasaran & Pembuat Pernyataan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            1. Pilih Sasaran Asesmen (Pasien / PMKS): <span class="text-danger">*</span>
                                        </label>
                                        <select name="nik_sasaran" id="nik_sasaran" class="form-control form-control-sm select2" required style="width: 100%;">
                                            <option value="">-- Pilih Anggota Keluarga yang Mengalami Masalah --</option>
                                        </select>
                                        <small class="text-muted">Orang yang sakit atau membutuhkan pelayanan/rekomendasi Lapad Ruhama.</small>
                                    </div>

                                    <!-- Biodata Sasaran Terpilih -->
                                    <div id="bioSasaran" class="card bg-light border p-2 mb-3 d-none">
                                        <table class="table table-sm table-borderless m-0 text-sm">
                                            <tr>
                                                <td width="35%">Nama Lengkap</td>
                                                <td width="5%">:</td>
                                                <td id="s_nama" class="font-weight-bold">-</td>
                                            </tr>
                                            <tr>
                                                <td>NIK</td>
                                                <td>:</td>
                                                <td id="s_nik">-</td>
                                            </tr>
                                            <tr>
                                                <td>Tempat, Tgl Lahir</td>
                                                <td>:</td>
                                                <td id="s_ttl">-</td>
                                            </tr>
                                            <tr>
                                                <td>Status Kawin</td>
                                                <td>:</td>
                                                <td id="s_kawin">-</td>
                                            </tr>
                                            <tr>
                                                <td>Pekerjaan</td>
                                                <td>:</td>
                                                <td id="s_pekerjaan">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">
                                            2. Yang Membuat Surat Pernyataan Keaslian: <span class="text-danger">*</span>
                                        </label>
                                        <select name="nik_pembuat_pernyataan" id="nik_pembuat_pernyataan" class="form-control form-control-sm select2" required style="width: 100%;">
                                            <option value="">-- Pilih Kepala Keluarga / Pasien / Pasangan --</option>
                                        </select>
                                        <small class="text-muted">Orang yang menandatangani Surat Pernyataan Keaslian Dokumen Lapad Ruhama.</small>
                                    </div>

                                    <!-- Biodata Pembuat Pernyataan -->
                                    <div id="bioPernyataan" class="card bg-light border p-2 mb-3 d-none">
                                        <table class="table table-sm table-borderless m-0 text-sm">
                                            <tr>
                                                <td width="35%">Nama</td>
                                                <td width="5%">:</td>
                                                <td id="p_nama" class="font-weight-bold">-</td>
                                            </tr>
                                            <tr>
                                                <td>NIK</td>
                                                <td>:</td>
                                                <td id="p_nik">-</td>
                                            </tr>
                                            <tr>
                                                <td>Hubungan di KK</td>
                                                <td>:</td>
                                                <td id="p_shdk">-</td>
                                            </tr>
                                        </table>
                                        <div class="form-group mt-2 mb-1">
                                            <label class="small font-weight-bold text-dark">No. HP / WA Pembuat Pernyataan:</label>
                                            <input type="text" name="pembuat_pernyataan_hp" id="pembuat_pernyataan_hp" class="form-control form-control-sm" placeholder="Contoh: 0831-6612-8082" value="0831-6612-8082">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tanggal Asesmen Lapangan:</label>
                                        <input type="date" name="tanggal_asesmen" id="tanggal_asesmen" class="form-control form-control-sm" value="<?= date('Y-m-d'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Kategori PPKS Kemensos RI:</label>
                                        <select name="kategori_ppks_id" id="kategori_ppks_id" class="form-control form-control-sm select2" style="width: 100%;">
                                            <?php foreach ($kategoriPpks as $kp) : ?>
                                                <option value="<?= $kp['pk_id']; ?>" <?= ($kp['pk_id'] == 24) ? 'selected' : ''; ?>>
                                                    <?= $kp['pk_id'] . '. ' . $kp['pk_nama_kategori']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN III: TABEL ANGGOTA KELUARGA (OTOMATIS) -->
                    <div class="card card-outline card-secondary shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title font-weight-bold text-secondary m-0">
                                <i class="fas fa-users mr-1"></i> Susunan Anggota Keluarga (Tabel Bagian III Instrumen)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped text-sm m-0">
                                    <thead class="bg-light text-center">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="25%">Nama Lengkap</th>
                                            <th width="15%">Hub. Keluarga</th>
                                            <th width="12%">Umur</th>
                                            <th width="20%">Pekerjaan</th>
                                            <th width="15%">Penghasilan / Bln</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyArt">
                                        <tr>
                                            <td colspan="6" class="text-center py-3 text-muted">Belum ada keluarga yang dipilih</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN IV & V: PERMASALAHAN & PERMOHONAN BANTUAN -->
                    <div class="card card-outline card-warning shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title font-weight-bold text-dark m-0">
                                <i class="fas fa-exclamation-triangle text-warning mr-1"></i> Langkah 3: Permasalahan & Permohonan Bantuan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="font-weight-bold m-0">
                                                IV. Riwayat Penyakit / Diagnosa Medis Pasien:
                                            </label>
                                            <span class="badge badge-light border text-primary" title="Riwayat dari basis data asesmen">
                                                <i class="fas fa-history mr-1"></i>Pilihan Riwayat
                                            </span>
                                        </div>
                                        <select id="pilih_riwayat_penyakit" class="form-control form-control-sm select2 mb-2" style="width: 100%;">
                                            <option value="">-- Pilih Riwayat Penyakit Tersimpan (atau ketik langsung di bawah) --</option>
                                            <?php if (!empty($listRiwayatPenyakit)) : ?>
                                                <?php foreach ($listRiwayatPenyakit as $rp) : ?>
                                                    <option value="<?= esc($rp); ?>"><?= esc($rp); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <textarea name="riwayat_penyakit" id="riwayat_penyakit" class="form-control form-control-sm" rows="3" placeholder="Contoh: Memiliki Riwayat Penyakit Muntaber / Gejala Dehidrasi Akut"></textarea>
                                        <small class="text-muted">Ketik diagnosa penyakit atau pilih dari riwayat yang pernah tersimpan di atas.</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Permasalahan Sosial Ekonomi Lainnya:</label>
                                        <textarea name="permasalahan_tambahan" id="permasalahan_tambahan" class="form-control form-control-sm" rows="3" placeholder="Contoh: Keluarga Miskin/Tidak Mampu, Penghasilan buruh tidak mencukupi untuk rawat inap"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="font-weight-bold m-0">
                                                V. Permohonan Bantuan / Keluhan: <span class="text-danger">*</span>
                                            </label>
                                            <span class="badge badge-light border text-success" title="Riwayat dari basis data asesmen">
                                                <i class="fas fa-history mr-1"></i>Pilihan Riwayat
                                            </span>
                                        </div>
                                        <select id="pilih_permohonan_bantuan" class="form-control form-control-sm select2 mb-2" style="width: 100%;">
                                            <option value="">-- Pilih Permohonan Bantuan Tersimpan (atau ketik langsung di bawah) --</option>
                                            <?php if (!empty($listPermohonanBantuan)) : ?>
                                                <?php foreach ($listPermohonanBantuan as $pb) : ?>
                                                    <option value="<?= esc($pb); ?>"><?= esc($pb); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <textarea name="permohonan_bantuan" id="permohonan_bantuan" class="form-control form-control-sm" rows="3" required placeholder="Contoh: Mendapatkan Pelayanan BPJS KIS PBI / Permohonan Keringanan Biaya Perawatan Pasien Lapad Ruhama"></textarea>
                                        <small class="text-muted">Rekomendasi jenis intervensi layanan yang diajukan ke Dinsos Garut.</small>
                                    </div>

                                    <!-- Quick Checkbox Masalah Umum -->
                                    <div class="p-2 border rounded bg-light">
                                        <label class="small font-weight-bold text-muted mb-2 d-block">Pilihan Cepat Masalah & Usulan Tambahan:</label>
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input chk-preset" id="preset1" data-target="permasalahan_tambahan" value="Keluarga Miskin/Tidak Mampu">
                                            <label class="custom-control-label small" for="preset1">Keluarga Miskin/Tidak Mampu</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input chk-preset" id="preset2" data-target="permasalahan_tambahan" value="Tidak memiliki BPJS APBN KIS">
                                            <label class="custom-control-label small" for="preset2">Tidak memiliki BPJS APBN KIS</label>
                                        </div>
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox" class="custom-control-input chk-preset" id="preset3" data-target="permohonan_bantuan" value="Mendapatkan Pelayanan BPJS KIS PBI">
                                            <label class="custom-control-label small" for="preset3">Mendapatkan Pelayanan BPJS KIS PBI</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input chk-preset" id="preset4" data-target="permohonan_bantuan" value="Keringanan Biaya Kesehatan Lapad Ruhama Dinas Sosial Kabupaten Garut">
                                            <label class="custom-control-label small" for="preset4">Keringanan Biaya Kesehatan Lapad Ruhama Dinsos Garut</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN FOTO DOKUMENTASI (4 PILAR BUKTI) -->
                    <div class="card card-outline card-success shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title font-weight-bold text-success m-0">
                                <i class="fas fa-camera mr-1"></i> Langkah 4: Dokumentasi Lapangan (4 Pilar Foto)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- 1. FOTO PASIEN (UPLOAD BARU DARI GALERI + WATERMARK) -->
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="border rounded p-2 text-center h-100 bg-light">
                                        <label class="font-weight-bold text-primary small d-block mb-1">1. FOTO PASIEN / SASARAN <span class="text-danger">*</span></label>
                                        <div class="img-preview-box mb-2" style="height: 140px; background: #e9ecef; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 4px;">
                                            <img id="prevFotoPasien" src="" class="img-fluid d-none" style="max-height: 140px;">
                                            <span id="placeholderPasien" class="text-muted small"><i class="fas fa-user-injured fa-2x d-block mb-1"></i>Unggah dari Galeri</span>
                                        </div>
                                        <div class="custom-file custom-file-sm text-left">
                                            <input type="file" name="foto_pasien" id="foto_pasien" class="custom-file-input form-control-sm" accept="image/*" required>
                                            <label class="custom-file-label small text-truncate" for="foto_pasien">Pilih foto pasien...</label>
                                        </div>
                                        <span class="badge badge-warning text-xs mt-2 d-block"><i class="fas fa-stamp mr-1"></i>Auto Watermark SINDEN</span>
                                    </div>
                                </div>

                                <!-- 2. FOTO RUMAH TAMPAK DEPAN (DTSEN / UPLOAD GALERI) -->
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="border rounded p-2 text-center h-100 bg-light">
                                        <label class="font-weight-bold text-dark small d-block mb-1">2. RUMAH TAMPAK DEPAN</label>
                                        <div class="img-preview-box mb-2" style="height: 140px; background: #e9ecef; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 4px;">
                                            <img id="imgFotoDepan" src="" class="img-fluid d-none" style="max-height: 140px;">
                                            <span id="noFotoDepan" class="text-muted small"><i class="fas fa-home fa-2x d-block mb-1"></i>Tarik dari DTSEN</span>
                                        </div>
                                        <div class="custom-file custom-file-sm text-left">
                                            <input type="file" name="foto_depan" id="foto_depan" class="custom-file-input form-control-sm" accept="image/*">
                                            <label class="custom-file-label small text-truncate" for="foto_depan">Pilih foto depan...</label>
                                        </div>
                                        <span id="badgeFotoDepan" class="badge badge-light border text-xs mt-2 d-block text-truncate">Menunggu data KK</span>
                                        <span class="badge badge-warning text-xs mt-1 d-block"><i class="fas fa-stamp mr-1"></i>Auto Watermark SINDEN</span>
                                    </div>
                                </div>

                                <!-- 3. FOTO RUMAH TAMPAK DALAM (DTSEN / UPLOAD GALERI) -->
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="border rounded p-2 text-center h-100 bg-light">
                                        <label class="font-weight-bold text-dark small d-block mb-1">3. RUMAH TAMPAK DALAM</label>
                                        <div class="img-preview-box mb-2" style="height: 140px; background: #e9ecef; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 4px;">
                                            <img id="imgFotoDalam" src="" class="img-fluid d-none" style="max-height: 140px;">
                                            <span id="noFotoDalam" class="text-muted small"><i class="fas fa-couch fa-2x d-block mb-1"></i>Tarik dari DTSEN</span>
                                        </div>
                                        <div class="custom-file custom-file-sm text-left">
                                            <input type="file" name="foto_dalam" id="foto_dalam" class="custom-file-input form-control-sm" accept="image/*">
                                            <label class="custom-file-label small text-truncate" for="foto_dalam">Pilih foto dalam...</label>
                                        </div>
                                        <span id="badgeFotoDalam" class="badge badge-light border text-xs mt-2 d-block text-truncate">Menunggu data KK</span>
                                        <span class="badge badge-warning text-xs mt-1 d-block"><i class="fas fa-stamp mr-1"></i>Auto Watermark SINDEN</span>
                                    </div>
                                </div>

                                <!-- 4. FOTO MCK / KAMAR MANDI (DTSEN / UPLOAD GALERI) -->
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="border rounded p-2 text-center h-100 bg-light">
                                        <label class="font-weight-bold text-dark small d-block mb-1">4. FOTO MCK / WC</label>
                                        <div class="img-preview-box mb-2" style="height: 140px; background: #e9ecef; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 4px;">
                                            <img id="imgFotoMck" src="" class="img-fluid d-none" style="max-height: 140px;">
                                            <span id="noFotoMck" class="text-muted small"><i class="fas fa-bath fa-2x d-block mb-1"></i>Tarik dari DTSEN</span>
                                        </div>
                                        <div class="custom-file custom-file-sm text-left">
                                            <input type="file" name="foto_kamar_mandi" id="foto_kamar_mandi" class="custom-file-input form-control-sm" accept="image/*">
                                            <label class="custom-file-label small text-truncate" for="foto_kamar_mandi">Pilih foto MCK...</label>
                                        </div>
                                        <span id="badgeFotoMck" class="badge badge-light border text-xs mt-2 d-block text-truncate">Menunggu data KK</span>
                                        <span class="badge badge-warning text-xs mt-1 d-block"><i class="fas fa-stamp mr-1"></i>Auto Watermark SINDEN</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN PEJABAT PENANDATANGAN (AUTO DARI PROFIL LEMBAGA) -->
                    <div class="card card-outline card-dark shadow-sm mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title font-weight-bold text-dark m-0">
                                <i class="fas fa-stamp mr-1"></i> Pejabat Penandatangan Dokumen (Tersimpan Terpusat)
                            </h5>
                            <a href="<?= base_url('settings'); ?>" target="_blank" class="btn btn-xs btn-outline-info ml-auto">
                                <i class="fas fa-cog mr-1"></i> Kelola Profil Lembaga
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="small font-weight-bold">Fasilitator Lapangan:</label>
                                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($user_login['fullname']); ?> (NIK: <?= esc($user_login['nik']); ?>)" readonly>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small font-weight-bold">Ketua Puskesos Desa:</label>
                                    <input type="text" name="ketua_puskesos_nama" class="form-control form-control-sm" value="<?= esc($profilLembaga['lp_puskesos_ketua'] ?? 'AJAT SUDRAJAT'); ?>">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small font-weight-bold">Kepala Desa Pasirlangu:</label>
                                    <input type="text" name="kepala_desa_nama" class="form-control form-control-sm" value="<?= esc($profilLembaga['lp_kepala'] ?? 'TATA RUSTANDI'); ?>">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small font-weight-bold">Supervisor SLRT Kecamatan:</label>
                                    <input type="text" name="supervisor_nama" class="form-control form-control-sm" value="<?= esc($profilLembaga['lp_supervisor_slrt'] ?? 'ABDURRAHMAN LAKA, S.Sos'); ?>">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small font-weight-bold">NIAT Supervisor SLRT:</label>
                                    <input type="text" name="supervisor_niat" class="form-control form-control-sm" value="<?= esc($profilLembaga['lp_supervisor_niat'] ?? 'NIAT. 32.05.33-0183'); ?>">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="small font-weight-bold">Camat Pakenjeng:</label>
                                    <input type="text" name="camat_nama" class="form-control form-control-sm" value="<?= esc($profilLembaga['lp_camat'] ?? 'Camat Pakenjeng'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="card shadow-sm mb-5">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('assessment'); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            <button type="submit" id="btnSubmit" class="btn btn-primary px-4 font-weight-bold">
                                <i class="fas fa-save mr-1"></i> Simpan & Siapkan Dokumen Cetak
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        var rawArtList = [];

        // Select2 Autocomplete untuk Pencarian KK
        $('#searchKkSelect').select2({
            theme: 'bootstrap4',
            placeholder: 'Ketik minimal 3 digit No KK, NIK, atau Nama Kepala Keluarga...',
            minimumInputLength: 3,
            ajax: {
                url: '<?= base_url('assessment/search-kk'); ?>',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        // Inisialisasi Select2 untuk Dropdown Riwayat Penyakit & Permohonan Bantuan
        $('#pilih_riwayat_penyakit, #pilih_permohonan_bantuan').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Otomatis isi textarea saat Riwayat Penyakit dipilih dari dropdown
        $('#pilih_riwayat_penyakit').on('change', function() {
            var val = $(this).val();
            if (val) {
                $('#riwayat_penyakit').val(val);
            }
        });

        // Otomatis isi textarea saat Permohonan Bantuan dipilih dari dropdown
        $('#pilih_permohonan_bantuan').on('change', function() {
            var val = $(this).val();
            if (val) {
                $('#permohonan_bantuan').val(val);
            }
        });

        // Event ketika KK Dipilih
        $('#searchKkSelect').on('select2:select', function(e) {
            var id_kk = e.params.data.id;
            $('#id_kk').val(id_kk);

            // Load detail KK via AJAX
            Swal.fire({
                title: 'Memuat data keluarga...',
                text: 'Menyinkronkan data DTSEN, susunan ART, foto, dan desil',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= base_url('assessment/get-kk-detail'); ?>/' + id_kk,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    Swal.close();
                    if (res.status && res.kk) {
                        var kk = res.kk;
                        rawArtList = res.art;

                        // 1. Tampilkan Panel Preview Keluarga
                        $('#prevNoKk').text(kk.no_kk);
                        $('#prevKepalaKeluarga').text(kk.kepala_keluarga);
                        $('#prevDesil').text(kk.kategori_desil ? 'Desil ' + kk.kategori_desil : 'Non-Desil');

                        var alamatLengkap = (kk.alamat || '-') + ' RT ' + (kk.rt || '000') + ' / RW ' + (kk.rw || '000') + ', Desa ' + (kk.nama_desa || '-') + ', Kec. ' + (kk.nama_kecamatan || '-') + ', Kab. ' + (kk.nama_kabupaten || 'Garut');
                        $('#prevAlamat').text(alamatLengkap);
                        $('#prevBansos').text(kk.program_bansos || 'Belum Ada Bansos');
                        $('#panelKeluarga').removeClass('d-none');
                        $('#panelInstrumen').removeClass('d-none');

                        // 2. Populate Dropdown Sasaran & Pembuat Pernyataan
                        var optSasaran = '<option value="">-- Pilih Anggota Keluarga yang Mengalami Masalah --</option>';
                        var optPernyataan = '<option value="">-- Pilih Kepala Keluarga / Pasien / Pasangan --</option>';

                        var tbodyHtml = '';
                        var defaultPembuat = '';

                        res.art.forEach(function(a, idx) {
                            var label = a.nama + ' (' + (a.nama_shdk || 'ART') + ' | ' + a.umur + ' | NIK: ' + a.nik + ')';
                            optSasaran += '<option value="' + a.nik + '">' + label + '</option>';
                            optPernyataan += '<option value="' + a.nik + '">' + label + '</option>';

                            // Pilih Kepala Keluarga atau Istri sebagai default pembuat pernyataan
                            if (!defaultPembuat && (a.hubungan_keluarga == 1 || a.hubungan_keluarga == 3)) {
                                defaultPembuat = a.nik;
                            }

                            // Render Tabel Bagian III (Susunan Keluarga)
                            tbodyHtml += '<tr>';
                            tbodyHtml += '<td class="text-center">' + (idx + 1) + '</td>';
                            tbodyHtml += '<td><strong>' + a.nama + '</strong><br><small class="text-muted">NIK: ' + a.nik + '</small></td>';
                            tbodyHtml += '<td class="text-center">' + (a.nama_shdk || '-') + '</td>';
                            tbodyHtml += '<td class="text-center font-weight-bold">' + a.umur + '</td>';
                            tbodyHtml += '<td>' + (a.nama_pekerjaan || 'Belum/Tidak Bekerja') + '</td>';
                            tbodyHtml += '<td class="text-center">' + (a.hubungan_keluarga == 1 ? '< 1 Juta' : '-') + '</td>';
                            tbodyHtml += '</tr>';
                        });

                        $('#nik_sasaran').html(optSasaran);
                        $('#nik_pembuat_pernyataan').html(optPernyataan);
                        if (defaultPembuat) {
                            $('#nik_pembuat_pernyataan').val(defaultPembuat).trigger('change');
                        }

                        $('#tbodyArt').html(tbodyHtml);

                        // 3. Tampilkan Foto Rumah & MCK dari DTSEN
                        if (kk.foto_rumah) {
                            $('#imgFotoDepan').attr('src', '<?= base_url(); ?>/' + kk.foto_rumah).removeClass('d-none');
                            $('#noFotoDepan').addClass('d-none');
                            $('#badgeFotoDepan').removeClass('badge-light border badge-warning badge-primary').addClass('badge-success').html('<i class="fas fa-check"></i> Siap Cetak (DTSEN)');
                            $('#foto_depan').next('.custom-file-label').html('Ganti foto dari galeri...');
                        } else {
                            $('#imgFotoDepan').addClass('d-none');
                            $('#noFotoDepan').removeClass('d-none');
                            $('#badgeFotoDepan').removeClass('badge-success badge-primary').addClass('badge-warning').html('<i class="fas fa-exclamation-triangle"></i> Belum ada di DTSEN - Silakan Unggah');
                            $('#foto_depan').next('.custom-file-label').html('Pilih foto dari galeri...');
                        }

                        if (kk.foto_rumah_dalam) {
                            $('#imgFotoDalam').attr('src', '<?= base_url(); ?>/' + kk.foto_rumah_dalam).removeClass('d-none');
                            $('#noFotoDalam').addClass('d-none');
                            $('#badgeFotoDalam').removeClass('badge-light border badge-warning badge-primary').addClass('badge-success').html('<i class="fas fa-check"></i> Siap Cetak (DTSEN)');
                            $('#foto_dalam').next('.custom-file-label').html('Ganti foto dari galeri...');
                        } else {
                            $('#imgFotoDalam').addClass('d-none');
                            $('#noFotoDalam').removeClass('d-none');
                            $('#badgeFotoDalam').removeClass('badge-success badge-primary').addClass('badge-warning').html('<i class="fas fa-exclamation-triangle"></i> Belum ada di DTSEN - Silakan Unggah');
                            $('#foto_dalam').next('.custom-file-label').html('Pilih foto dari galeri...');
                        }

                        if (kk.foto_kamar_mandi) {
                            $('#imgFotoMck').attr('src', '<?= base_url(); ?>/' + kk.foto_kamar_mandi).removeClass('d-none');
                            $('#noFotoMck').addClass('d-none');
                            $('#badgeFotoMck').removeClass('badge-light border badge-warning badge-primary').addClass('badge-success').html('<i class="fas fa-check"></i> Siap Cetak (DTSEN)');
                            $('#foto_kamar_mandi').next('.custom-file-label').html('Ganti foto dari galeri...');
                        } else {
                            $('#imgFotoMck').addClass('d-none');
                            $('#noFotoMck').removeClass('d-none');
                            $('#badgeFotoMck').removeClass('badge-success badge-primary').addClass('badge-warning').html('<i class="fas fa-exclamation-triangle"></i> Belum ada di DTSEN - Silakan Unggah');
                            $('#foto_kamar_mandi').next('.custom-file-label').html('Pilih foto dari galeri...');
                        }
                    } else {
                        Swal.fire('Gagal!', 'Tidak dapat memuat detail keluarga.', 'error');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                }
            });
        });

        // Ketika Sasaran Pasien Dipilih
        $('#nik_sasaran').on('change', function() {
            var nik = $(this).val();
            if (!nik) {
                $('#bioSasaran').addClass('d-none');
                return;
            }

            var found = rawArtList.find(function(a) {
                return a.nik == nik;
            });
            if (found) {
                $('#s_nama').text(found.nama);
                $('#s_nik').text(found.nik);
                $('#s_ttl').text((found.tempat_lahir || 'Garut') + ', ' + (found.tanggal_lahir || '-'));
                $('#s_kawin').text(found.nama_status_kawin || 'Belum Kawin');
                $('#s_pekerjaan').text(found.nama_pekerjaan || 'Belum/Tidak Bekerja');
                $('#bioSasaran').removeClass('d-none');
            }
        });

        // Ketika Pembuat Pernyataan Dipilih
        $('#nik_pembuat_pernyataan').on('change', function() {
            var nik = $(this).val();
            if (!nik) {
                $('#bioPernyataan').addClass('d-none');
                return;
            }

            var found = rawArtList.find(function(a) {
                return a.nik == nik;
            });
            if (found) {
                $('#p_nama').text(found.nama);
                $('#p_nik').text(found.nik);
                $('#p_shdk').text(found.nama_shdk || 'Kepala Keluarga');
                if (found.no_hp) {
                    $('#pembuat_pernyataan_hp').val(found.no_hp);
                }
                $('#bioPernyataan').removeClass('d-none');
            }
        });

        // Helper Handler Preview Gambar untuk 4 Pilar Foto
        function bindImagePreview(inputId, imgId, placeholderId, badgeId) {
            $('#' + inputId).on('change', function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(evt) {
                        $('#' + imgId).attr('src', evt.target.result).removeClass('d-none');
                        $('#' + placeholderId).addClass('d-none');
                        if (badgeId) {
                            $('#' + badgeId).removeClass('badge-light border badge-success badge-warning').addClass('badge-primary').html('<i class="fas fa-upload mr-1"></i>Unggahan Baru (Galeri)');
                        }
                    };
                    reader.readAsDataURL(file);
                    $(this).next('.custom-file-label').html(file.name);
                }
            });
        }

        bindImagePreview('foto_pasien', 'prevFotoPasien', 'placeholderPasien', null);
        bindImagePreview('foto_depan', 'imgFotoDepan', 'noFotoDepan', 'badgeFotoDepan');
        bindImagePreview('foto_dalam', 'imgFotoDalam', 'noFotoDalam', 'badgeFotoDalam');
        bindImagePreview('foto_kamar_mandi', 'imgFotoMck', 'noFotoMck', 'badgeFotoMck');

        // Checkbox Preset Masalah & Permohonan
        $('.chk-preset').on('change', function() {
            var targetId = $(this).data('target');
            var val = $(this).val();
            var currentText = $('#' + targetId).val().trim();

            if ($(this).is(':checked')) {
                if (currentText.length > 0) {
                    $('#' + targetId).val(currentText + '\n' + val);
                } else {
                    $('#' + targetId).val(val);
                }
            }
        });

        // Submit Form via AJAX
        $('#formAssessment').on('submit', function(e) {
            e.preventDefault();

            if (!$('#id_kk').val()) {
                Swal.fire('Perhatian', 'Silakan cari dan pilih keluarga sasaran terlebih dahulu!', 'warning');
                return;
            }

            if (!$('#nik_sasaran').val()) {
                Swal.fire('Perhatian', 'Silakan pilih sasaran pasien / PMKS!', 'warning');
                return;
            }

            var formData = new FormData(this);

            Swal.fire({
                title: 'Menyimpan Berkas Asesmen...',
                text: 'Memproses kompresi dan watermark GPS foto pasien',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $('#btnSubmit').prop('disabled', true);

            $.ajax({
                url: '<?= base_url('assessment/simpan'); ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    $('#btnSubmit').prop('disabled', false);
                    if (res.status) {
                        Swal.fire({
                            title: 'Berhasil Disimpan!',
                            text: res.message,
                            icon: 'success',
                            showDenyButton: true,
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-file-word mr-1"></i> Unduh Word (.docx)',
                            denyButtonText: '<i class="fas fa-print mr-1"></i> Cetak Web',
                            cancelButtonText: '<i class="fas fa-list mr-1"></i> Daftar Asesmen',
                            confirmButtonColor: '#28a745',
                            denyButtonColor: '#17a2b8',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = res.word_url;
                                setTimeout(function() {
                                    window.location.href = res.redirect_url;
                                }, 1200);
                            } else if (result.isDenied) {
                                window.open(res.cetak_url, '_blank');
                                window.location.href = res.redirect_url;
                            } else {
                                window.location.href = res.redirect_url;
                            }
                        });
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                },
                error: function() {
                    $('#btnSubmit').prop('disabled', false);
                    Swal.fire('Error!', 'Terjadi kesalahan sistem saat menyimpan data.', 'error');
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>