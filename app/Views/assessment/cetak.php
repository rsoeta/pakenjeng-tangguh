<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?></title>
    <style>
        /* ============================================================
           CSS PRINT STYLING PRESISI LAPAD RUHAMA KABUPATEN GARUT
           ============================================================ */
        @page {
            size: F4 portrait; /* Ukuran Folio / F4 standar birokrasi Pemkab Garut */
            margin: 12mm 15mm 12mm 15mm;
        }

        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
        }

        .no-print {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-print {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 8px 18px;
            font-size: 11pt;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-print:hover {
            background-color: #219150;
        }

        /* TABEL INSTRUMEN */
        .table-instrumen {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 8px;
        }

        .table-instrumen th,
        .table-instrumen td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 9.5pt;
            vertical-align: middle;
        }

        .table-instrumen th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .section-header {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        /* KOP SURAT DESA */
        .kop-surat-desa {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .kop-logo {
            width: 75px;
            height: auto;
            margin-right: 15px;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-text h4 {
            margin: 0;
            font-size: 11pt;
            letter-spacing: 1px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text h3 {
            margin: 0;
            font-size: 13pt;
            letter-spacing: 1px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 15pt;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .kop-text p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            font-style: italic;
        }

        /* GRID FOTO DOKUMENTASI */
        .grid-foto {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .grid-foto td {
            width: 50%;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: top;
        }

        .foto-box {
            width: 100%;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #fafafa;
        }

        .foto-box img {
            max-width: 100%;
            max-height: 220px;
            object-fit: contain;
        }

        /* KARTU SIKS-NG WATERMARK BACKGROUND */
        .siks-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            position: relative;
            overflow: hidden;
            background-color: #fff;
        }

        .siks-watermark {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-content: center;
            opacity: 0.08;
            transform: rotate(-25deg);
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            user-select: none;
            line-height: 2.2;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Bar Navigasi Cetak (Disembunyikan saat di-print) -->
    <div class="no-print">
        <div>
            <strong>Berkas Asesmen Lapad Ruhama</strong> &bull; No: <?= esc($nomor_asesmen); ?>
        </div>
        <div>
            <button class="btn-print" onclick="window.print();"><i class="fas fa-print"></i> Cetak / Simpan PDF</button>
        </div>
    </div>

    <?php
    $namaDesa      = strtoupper($nama_desa ?: 'PASIRLANGU');
    $namaKecamatan = strtoupper($nama_kecamatan ?: 'PAKENJENG');
    $namaKabupaten = strtoupper($nama_kabupaten ?: 'GARUT');
    $alamatLengkap = ($kk_alamat ?: '-') . ' RT ' . str_pad($rt ?? '0', 3, '0', STR_PAD_LEFT) . ' RW ' . str_pad($rw ?? '0', 3, '0', STR_PAD_LEFT);

    // Format Tanggal Indonesia
    $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $tglTimestamp = strtotime($tanggal_asesmen);
    $tanggalIndo = date('d', $tglTimestamp) . ' ' . $bulanIndo[(int)date('m', $tglTimestamp)] . ' ' . date('Y', $tglTimestamp);
    ?>

    <!-- ==============================================================
         LEMBAR 1: INSTRUMEN ASESMEN PMKS LAPAD RUHAMA KABUPATEN GARUT
         ============================================================== -->
    <div class="lembar-asesmen">
        <table style="width: 100%; margin-bottom: 8px;">
            <tr>
                <td style="width: 25%; text-align: left; vertical-align: middle;">
                    <img src="<?= base_url('assets/logo/logo-slrt.svg'); ?>" alt="Logo SLRT" style="width: 150px; height: auto;">
                </td>
                <td style="width: 75%; text-align: center; vertical-align: middle;">
                    <div style="font-weight: 900; font-size: 11pt; line-height: 1.25; text-transform: uppercase;">
                        INSTRUMEN ASSESMENT<br>
                        PENYANDANG MASALAH KESEJAHTERAAN SOSIAL<br>
                        LAYANAN TERPADU RUMAH HARAPAN MASYARAKAT<br>
                        KABUPATEN GARUT
                    </div>
                </td>
            </tr>
        </table>

        <!-- TABEL FORMULIR UTAMA -->
        <table class="table-instrumen">
            <!-- I. LOKASI -->
            <tr class="section-header">
                <td style="width: 5%; text-align: center; font-weight: bold;">I.</td>
                <td colspan="5" style="font-weight: bold;">LOKASI</td>
            </tr>
            <tr>
                <td></td>
                <td style="width: 3%; text-align: center;">1.</td>
                <td style="width: 32%;">Alamat</td>
                <td style="width: 2%; text-align: center;">:</td>
                <td colspan="2"><?= esc(toTitleCase($alamatLengkap)); ?></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">2.</td>
                <td>Desa</td>
                <td style="text-align: center;">:</td>
                <td colspan="2"><?= esc(toTitleCase($namaDesa)); ?></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">3.</td>
                <td>Kecamatan</td>
                <td style="text-align: center;">:</td>
                <td colspan="2"><?= esc(toTitleCase($namaKecamatan)); ?></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">4.</td>
                <td>Kabupaten</td>
                <td style="text-align: center;">:</td>
                <td colspan="2"><?= esc(toTitleCase($namaKabupaten)); ?></td>
            </tr>

            <!-- II. IDENTITAS -->
            <tr class="section-header">
                <td style="text-align: center; font-weight: bold;">II.</td>
                <td colspan="5" style="font-weight: bold;">IDENTITAS</td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">1.</td>
                <td>Nama Lengkap</td>
                <td style="text-align: center;">:</td>
                <td colspan="2" style="font-weight: bold;"><?= esc(toTitleCase($nama_sasaran)); ?></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">2.</td>
                <td>N I K</td>
                <td style="text-align: center;">:</td>
                <td colspan="2" style="letter-spacing: 0.5px;"><?= esc($nik_sasaran); ?></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">3.</td>
                <td>Tempat/ Tgl. Lahir</td>
                <td style="text-align: center;">:</td>
                <td colspan="2">
                    <?php
                    $tglLahirSasaran = !empty($sasaran['tanggal_lahir']) ? date('d', strtotime($sasaran['tanggal_lahir'])) . ' ' . $bulanIndo[(int)date('m', strtotime($sasaran['tanggal_lahir']))] . ' ' . date('Y', strtotime($sasaran['tanggal_lahir'])) : '-';
                    echo esc(toTitleCase($sasaran['tempat_lahir'] ?? 'Garut')) . ', ' . $tglLahirSasaran;
                    ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">4.</td>
                <td>Status Perkawinan</td>
                <td style="text-align: center;">:</td>
                <td colspan="2"><?= esc(toTitleCase($sasaran['nama_status_kawin'] ?? 'Belum Kawin')); ?></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">5.</td>
                <td>Pekerjaan/ Penghasilan/Bulan</td>
                <td style="text-align: center;">:</td>
                <td colspan="2"><?= esc(toTitleCase($sasaran['nama_pekerjaan'] ?? 'Belum/Tidak Bekerja')); ?></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">6.</td>
                <td>Jumlah Anggota Keluarga</td>
                <td style="text-align: center;">:</td>
                <td colspan="2"><?= count($anggota_keluarga); ?> Jiwa</td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: center;">7.</td>
                <td>Program yang dimiliki</td>
                <td style="text-align: center;">:</td>
                <td colspan="2"><?= esc(toTitleCase(!empty($sasaran['program_bansos']) ? $sasaran['program_bansos'] : ($status_kks === 'Ya' ? 'PKH, Sembako' : '-'))); ?></td>
            </tr>

            <!-- III. SUSUNAN KELUARGA -->
            <tr class="section-header">
                <td style="text-align: center; font-weight: bold;">III.</td>
                <td colspan="5" style="font-weight: bold;">KELUARGA</td>
            </tr>
        </table>

        <!-- TABEL DAFTAR ART -->
        <table class="table-instrumen" style="margin-top: -9px;">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Nama</th>
                    <th style="width: 18%;">Hub. Keluarga</th>
                    <th style="width: 12%;">Umur</th>
                    <th style="width: 20%;">Pekerjaan</th>
                    <th style="width: 15%;">Penghasilan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $noArt = 1;
                foreach ($anggota_keluarga as $art) :
                    $umurText = '-';
                    if (!empty($art['tanggal_lahir']) && $art['tanggal_lahir'] !== '0000-00-00') {
                        $birth = new DateTime($art['tanggal_lahir']);
                        $now   = new DateTime('today');
                        $umurText = $birth->diff($now)->y . ' Thn';
                    }

                    $penghasilanText = '-';
                    if ($art['hubungan_keluarga'] == 1) {
                        $penghasilanText = !empty($rata_penghasilan_bulanan) ? 'Rp ' . number_format($rata_penghasilan_bulanan, 0, ',', '.') : '< 1 Jt';
                    }
                ?>
                    <tr>
                        <td style="text-align: center;"><?= $noArt++; ?></td>
                        <td style="font-weight: bold;"><?= esc(toTitleCase($art['nama'])); ?></td>
                        <td style="text-align: center;"><?= esc(toTitleCase($art['nama_shdk'] ?? '-')); ?></td>
                        <td style="text-align: center;"><?= esc($umurText); ?></td>
                        <td><?= esc(toTitleCase($art['nama_pekerjaan'] ?? 'Belum Bekerja')); ?></td>
                        <td style="text-align: center;"><?= esc($penghasilanText); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- IV. PERMASALAHAN -->
        <table class="table-instrumen" style="margin-top: -9px;">
            <tr class="section-header">
                <td style="width: 5%; text-align: center; font-weight: bold;">IV.</td>
                <td style="font-weight: bold;">PERMASALAHAN</td>
            </tr>
            <tr>
                <td></td>
                <td style="padding: 6px 8px; line-height: 1.4;">
                    <?php if (!empty($riwayat_penyakit)) : ?>
                        <div>&bull; <?= esc($riwayat_penyakit); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($permasalahan_tambahan)) : ?>
                        <div>&bull; <?= nl2br(esc($permasalahan_tambahan)); ?></div>
                    <?php else : ?>
                        <div>&bull; Keluarga Miskin/Tidak Mampu</div>
                        <div>&bull; Tidak memiliki BPJS APBN KIS</div>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- V. PERMOHONAN BANTUAN -->
            <tr class="section-header">
                <td style="text-align: center; font-weight: bold;">V.</td>
                <td style="font-weight: bold;">PERMOHONAN BANTUAN / KELUHAN</td>
            </tr>
            <tr>
                <td></td>
                <td style="padding: 6px 8px; line-height: 1.4; font-weight: bold;">
                    &bull; <?= nl2br(esc($permohonan_bantuan ?: 'Mendapatkan Pelayanan BPJS KIS PBI')); ?>
                </td>
            </tr>
        </table>

        <!-- TANDA TANGAN 5 PIHAK -->
        <table style="width: 100%; margin-top: 10px; font-size: 9.5pt; text-align: center;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: right; padding-right: 30px;">
                    Garut, <?= $tanggalIndo; ?>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 5px;">
                    Fasilitator<br><br><br><br>
                    <strong><u><?= esc(strtoupper($fasilitator_nama)); ?></u></strong>
                </td>
                <td style="padding-top: 5px;">
                    Ketua Puskesos<br><br><br><br>
                    <strong><u><?= esc(strtoupper($ketua_puskesos_nama ?: 'AJAT SUDRAJAT')); ?></u></strong>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 10px; font-weight: bold;">
                    Mengetahui,
                </td>
            </tr>
            <tr>
                <td style="padding-top: 5px;">
                    Kepala Desa <?= esc(ucwords(strtolower($namaDesa))); ?><br><br><br><br>
                    <strong><u><?= esc(strtoupper($kepala_desa_nama ?: 'TATA RUSTANDI')); ?></u></strong>
                </td>
                <td style="padding-top: 5px;">
                    Supervisor<br><br><br><br>
                    <strong><u><?= esc(strtoupper($supervisor_nama ?: 'ABDURRAHMAN LAKA, S.Sos')); ?></u></strong><br>
                    <small><?= esc($supervisor_niat ?: 'NIAT. 32.05.33-0183'); ?></small>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 10px;">
                    Camat <?= esc(ucwords(strtolower($namaKecamatan))); ?><br><br><br><br>
                    <strong><u><?= esc(strtoupper($camat_nama ?: '...................................')); ?></u></strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- ==============================================================
         LEMBAR 2: SURAT PERNYATAAN KEASLIAN DOKUMEN
         ============================================================== -->
    <div class="lembar-pernyataan" style="padding: 10px 15px;">
        <!-- KOP SURAT DESA -->
        <div class="kop-surat-desa">
            <img src="<?= base_url('landing-page/images/logo-garut.png'); ?>" alt="Logo Garut" class="kop-logo">
            <div class="kop-text">
                <h4>PEMERINTAH KABUPATEN <?= $namaKabupaten; ?></h4>
                <h3>KECAMATAN <?= $namaKecamatan; ?></h3>
                <h2>DESA <?= $namaDesa; ?></h2>
                <p><?= esc($profil_desa['lp_sekretariat'] ?? "Jl. Desa Km. 200 Kp. Rahayu Desa {$namaDesa} Kec. {$namaKecamatan} Kab. {$namaKabupaten}"); ?><br>
                   Email: <?= esc($profil_desa['lp_email'] ?? "info@" . strtolower($namaDesa) . ".desa.id"); ?> Kode Pos: <?= esc($profil_desa['lp_kode_pos'] ?? '44164'); ?>
                </p>
            </div>
        </div>

        <div style="text-align: center; margin: 15px 0 20px 0;">
            <div style="font-weight: 900; font-size: 13pt; text-decoration: underline; letter-spacing: 0.5px;">
                SURAT PERNYATAAN KEASLIAN DOKUMEN
            </div>
        </div>

        <p style="margin-bottom: 12px;">Saya yang bertandatangan di bawah ini :</p>

        <?php
        $pembuatTtl = !empty($pembuat_pernyataan['tanggal_lahir']) ? date('d', strtotime($pembuat_pernyataan['tanggal_lahir'])) . ' ' . $bulanIndo[(int)date('m', strtotime($pembuat_pernyataan['tanggal_lahir']))] . ' ' . date('Y', strtotime($pembuat_pernyataan['tanggal_lahir'])) : '-';
        ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt;">
            <tr>
                <td style="width: 25%; padding: 3px 0;">Nama</td>
                <td style="width: 3%;">:</td>
                <td style="font-weight: bold;"><?= esc(toTitleCase($nama_pembuat_pernyataan)); ?></td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">Tempat/Tanggal Lahir</td>
                <td>:</td>
                <td><?= esc(toTitleCase($pembuat_pernyataan['tempat_lahir'] ?? 'Garut')) . ', ' . $pembuatTtl; ?></td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">NIK</td>
                <td>:</td>
                <td style="letter-spacing: 0.5px;"><?= esc($nik_pembuat_pernyataan); ?></td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">No KK</td>
                <td>:</td>
                <td style="letter-spacing: 0.5px;"><?= esc($no_kk); ?></td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">Jenis Kelamin</td>
                <td>:</td>
                <td><?= ($pembuat_pernyataan['jenis_kelamin'] == 2 || $pembuat_pernyataan['jenis_kelamin'] === 'P') ? 'Perempuan' : 'Laki-laki'; ?></td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">Agama</td>
                <td>:</td>
                <td>Islam</td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">Alamat</td>
                <td>:</td>
                <td><?= esc(toTitleCase($alamatLengkap)); ?></td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">No. HP</td>
                <td>:</td>
                <td><?= esc($pembuat_pernyataan_hp ?: ($pembuat_pernyataan['no_hp'] ?? '0831-6612-8082')); ?></td>
            </tr>
        </table>

        <p style="text-align: justify; line-height: 1.6; margin-bottom: 15px; text-indent: 30px;">
            Dengan ini saya menyatakan bahwa apabila dikemudian hari saya melakukan pelanggaran pemalsuan dokumen dalam <strong>Proses Pengajuan Administrasi Permohonan Keringanan Biaya Kesehatan Lapad Ruhama Dinas Sosial Kabupaten Garut</strong>. Maka dengan ini, saya siap menerima segala sanksi dan bersedia dituntut serta diproses sesuai dengan hukum dan ketentuan yang berlaku.
        </p>

        <p style="text-align: justify; line-height: 1.6; margin-bottom: 40px; text-indent: 30px;">
            Demikian pernyataan ini saya buat dengan sesungguhnya dan penuh kesadaran tanpa adanya paksaan dari siapa pun.
        </p>

        <table style="width: 100%; text-align: center; font-size: 10pt;">
            <tr>
                <td style="width: 50%;">
                    Mengetahui,<br>
                    Kepala Desa <?= esc(ucwords(strtolower($namaDesa))); ?><br><br><br><br><br>
                    <strong><u><?= esc(strtoupper($kepala_desa_nama ?: 'TATA RUSTANDI')); ?></u></strong>
                </td>
                <td style="width: 50%;">
                    Garut, <?= $tanggalIndo; ?><br>
                    Yang Membuat Pernyataan,<br>
                    Kepala Keluarga/Pasien<br><br><br><br>
                    <small style="color: #666; font-size: 8pt;">(Materai 10.000)</small><br>
                    <strong><u><?= esc(strtoupper($nama_pembuat_pernyataan)); ?></u></strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- ==============================================================
         LEMBAR 3: LAMPIRAN 4 GRID FOTO DOKUMENTASI
         ============================================================== -->
    <div class="lembar-foto">
        <div style="text-align: center; margin-bottom: 12px;">
            <div style="font-weight: 900; font-size: 11pt; text-transform: uppercase;">
                DOKUMENTASI FOTO VERIFIKASI LAPANGAN<br>
                LAYANAN TERPADU RUMAH HARAPAN MASYARAKAT (LAPAD RUHAMA)<br>
                KABUPATEN GARUT
            </div>
            <small style="color: #444;">No. KK: <strong><?= esc($no_kk); ?></strong> &bull; Pasien: <strong><?= esc($nama_sasaran); ?></strong></small>
        </div>

        <table class="grid-foto">
            <tr>
                <td>
                    <div style="font-weight: bold; font-size: 9.5pt; margin-bottom: 5px;">FOTO PASIEN</div>
                    <div class="foto-box">
                        <?php if (!empty($foto_pasien) && file_exists(FCPATH . $foto_pasien)) : ?>
                            <img src="<?= base_url($foto_pasien); ?>" alt="Foto Pasien">
                        <?php else : ?>
                            <span style="color: #999; font-style: italic;">Foto Pasien Belum Diunggah</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <div style="font-weight: bold; font-size: 9.5pt; margin-bottom: 5px;">FOTO RUMAH TAMPAK DEPAN</div>
                    <div class="foto-box">
                        <?php if (!empty($foto_depan) && file_exists(FCPATH . $foto_depan)) : ?>
                            <img src="<?= base_url($foto_depan); ?>" alt="Foto Rumah Depan">
                        <?php elseif (!empty($kk_foto_depan) && file_exists(FCPATH . $kk_foto_depan)) : ?>
                            <img src="<?= base_url($kk_foto_depan); ?>" alt="Foto Rumah Depan">
                        <?php else : ?>
                            <span style="color: #999; font-style: italic;">Foto Rumah Depan (DTSEN)</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="font-weight: bold; font-size: 9.5pt; margin-bottom: 5px;">FOTO RUMAH TAMPAK DALAM</div>
                    <div class="foto-box">
                        <?php if (!empty($foto_dalam) && file_exists(FCPATH . $foto_dalam)) : ?>
                            <img src="<?= base_url($foto_dalam); ?>" alt="Foto Rumah Dalam">
                        <?php elseif (!empty($kk_foto_dalam) && file_exists(FCPATH . $kk_foto_dalam)) : ?>
                            <img src="<?= base_url($kk_foto_dalam); ?>" alt="Foto Rumah Dalam">
                        <?php else : ?>
                            <span style="color: #999; font-style: italic;">Foto Rumah Dalam (DTSEN)</span>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <div style="font-weight: bold; font-size: 9.5pt; margin-bottom: 5px;">FOTO WC / KAMAR MANDI</div>
                    <div class="foto-box">
                        <?php if (!empty($foto_kamar_mandi) && file_exists(FCPATH . $foto_kamar_mandi)) : ?>
                            <img src="<?= base_url($foto_kamar_mandi); ?>" alt="Foto Kamar Mandi">
                        <?php elseif (!empty($rt_foto_mck) && file_exists(FCPATH . $rt_foto_mck)) : ?>
                            <img src="<?= base_url($rt_foto_mck); ?>" alt="Foto Kamar Mandi">
                        <?php else : ?>
                            <span style="color: #999; font-style: italic;">Foto Kamar Mandi / MCK</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>

        <div style="width: 100%; margin-top: 25px; text-align: right; padding-right: 40px; font-size: 9.5pt;">
            Mengetahui,<br>
            Kepala Desa <?= esc(ucwords(strtolower($namaDesa))); ?><br><br><br><br>
            <strong><u><?= esc(strtoupper($kepala_desa_nama ?: 'TATA RUSTANDI')); ?></u></strong>
        </div>
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- ==============================================================
         LEMBAR 4: LAMPIRAN KONTROL DESIL & DATA DUKUNG SIKS-NG
         ============================================================== -->
    <div class="lembar-siks" style="padding: 10px;">
        <div style="text-align: center; margin-bottom: 15px;">
            <div style="font-weight: 900; font-size: 12pt; text-transform: uppercase;">
                PROFIL KELAYAKAN BANSOS & PERINGKAT KESEJAHTERAAN KELUARGA<br>
                SISTEM INFORMASI DATA EKONOMI DAN SOSIAL (SINDEN)
            </div>
            <small style="color: #555;">Sinkronisasi Data Terpadu Sosial Ekonomi Nasional (DTSEN / SIKS-NG)</small>
        </div>

        <div class="siks-card shadow-sm">
            <!-- Watermark Berulang Petugas & Waktu -->
            <div class="siks-watermark">
                <?php
                $watermarkStamp = strtoupper($fasilitator_nama) . " | " . $fasilitator_nik . " | " . date('d-m-Y H:i:s');
                for ($w = 0; $w < 20; $w++) {
                    echo $watermarkStamp . " &bull; ";
                }
                ?>
            </div>

            <div style="position: relative; z-index: 2;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px;">
                    <div>
                        <h4 style="margin: 0; color: #1a5276; font-size: 13pt;">Informasi Keluarga</h4>
                        <small style="color: #7f8c8d;">Detail data kependudukan dan tempat tinggal</small>
                        <div style="margin-top: 8px; font-size: 10pt;">
                            <div><strong>KEPALA KELUARGA:</strong> <?= esc(strtoupper($anggota_keluarga[0]['nama'] ?? $nama_sasaran)); ?></div>
                            <div><strong>NO. KARTU KELUARGA:</strong> <?= esc(substr($no_kk, 0, 11) . '*****'); ?></div>
                            <div><strong>WILAYAH:</strong> Prov. Jawa Barat, Kab. Garut, Kec. <?= esc($namaKecamatan); ?>, Desa <?= esc($namaDesa); ?></div>
                            <div><strong>ALAMAT:</strong> <?= esc($alamatLengkap); ?></div>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <div style="background: #1b4f72; color: white; padding: 10px 15px; border-radius: 6px; display: inline-block; text-align: center;">
                            <small style="text-transform: uppercase; font-size: 8pt; letter-spacing: 0.5px;">Peringkat Kesejahteraan Keluarga</small>
                            <div style="font-size: 26pt; font-weight: 900; line-height: 1.1; margin: 4px 0;">
                                <?= !empty($kategori_desil) ? $kategori_desil : '1'; ?>
                            </div>
                            <span style="background: #e74c3c; color: white; padding: 2px 8px; border-radius: 3px; font-size: 8pt; font-weight: bold;">
                                <?= !empty($kategori_desil) ? 'DESIL ' . $kategori_desil : 'DESIL 1'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <h5 style="margin: 0 0 8px 0; color: #1a5276; font-size: 11pt;">Status Program Bantuan Sosial Aktif:</h5>
                    <table class="table-instrumen" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Program Bansos</th>
                                <th>Status Kepesertaan</th>
                                <th>Keterangan Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;">1</td>
                                <td><strong>PBI-JKN (BPJS Kesehatan)</strong></td>
                                <td style="text-align: center;">
                                    <span style="color: <?= ($status_bpjs === 'Ya' ? 'green' : '#c0392b'); ?>; font-weight: bold;">
                                        <?= ($status_bpjs === 'Ya') ? 'Aktif' : 'Belum Terdaftar / Diusulkan PBI'; ?>
                                    </span>
                                </td>
                                <td>Diusulkan melalui Jalur Lapad Ruhama Dinas Sosial Kabupaten Garut</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">2</td>
                                <td><strong>PKH (Program Keluarga Harapan)</strong></td>
                                <td style="text-align: center;">
                                    <span style="font-weight: bold; color: <?= ($status_kks === 'Ya' ? 'green' : '#7f8c8d'); ?>;">
                                        <?= ($status_kks === 'Ya') ? 'Terdaftar (KPM)' : 'Tidak Terdaftar'; ?>
                                    </span>
                                </td>
                                <td>Basis Data DTSEN / SIKS-NG</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">3</td>
                                <td><strong>SEMBAKO / BPNT</strong></td>
                                <td style="text-align: center;">
                                    <span style="font-weight: bold; color: <?= ($status_kks === 'Ya' ? 'green' : '#7f8c8d'); ?>;">
                                        <?= ($status_kks === 'Ya') ? 'Terdaftar (KPM)' : 'Tidak Terdaftar'; ?>
                                    </span>
                                </td>
                                <td>Basis Data DTSEN / SIKS-NG</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 15px; font-size: 8.5pt; color: #555; border-top: 1px dashed #ccc; padding-top: 8px;">
                    <i class="fas fa-info-circle"></i> Berkas cetak ini merupakan salinan sah data elektronik yang terintegrasi pada sistem SINDEN Kabupaten Garut. Digunakan sebagai berkas kelengkapan pengajuan keringanan biaya kesehatan / jaminan sosial Lapad Ruhama.
                </div>
            </div>
        </div>
    </div>

</body>

</html>

