<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 14mm;
        }

        body {
            font-family: "Times New Roman", Georgia, serif;
            font-size: 10.5pt;
            color: #000;
            margin: 0;
            padding: 0;
            background: #f4f6f9;
        }

        .no-print {
            background: #1e293b;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            position: sticky;
            top: 0;
            z-index: 9999;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 9.5pt;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        }

        .btn-toolbar {
            border: none;
            border-radius: 5px;
            padding: 6px 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 9pt;
        }

        .btn-cetak { background: #16a34a; color: #fff; }
        .btn-toggle { background: #0284c7; color: #fff; }
        .btn-tutup { background: #64748b; color: #fff; }

        .paper-sheet {
            max-width: 210mm;
            margin: 16px auto;
            background: #fff;
            padding: 14mm 16mm;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
        }

        /* KOP SURAT RESMI */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 4px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .kop-logo {
            width: 72px;
            height: auto;
            margin-right: 14px;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-text h4 {
            margin: 0;
            font-size: 11.5pt;
            text-transform: uppercase;
            font-weight: normal;
            letter-spacing: 0.5px;
        }

        .kop-text h3 {
            margin: 1px 0;
            font-size: 13pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .kop-text h2 {
            margin: 1px 0;
            font-size: 15.5pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .kop-text p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            font-style: italic;
        }

        .judul-dokumen {
            text-align: center;
            margin: 10px 0 14px 0;
        }

        .judul-dokumen h1 {
            margin: 0;
            font-size: 12.5pt;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .judul-dokumen .sub {
            font-size: 10pt;
            margin-top: 3px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }

        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 14px 0 6px 0;
            text-transform: uppercase;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.2pt;
            margin-bottom: 10px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        table.data-table th {
            background-color: #eceff4;
            font-weight: bold;
            text-align: center;
        }

        table.data-table tfoot td {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .ttd-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 26px;
            page-break-inside: avoid;
            font-size: 10pt;
        }

        .ttd-col {
            width: 44%;
            text-align: center;
        }

        .ttd-space {
            height: 64px;
        }

        .page-break {
            page-break-before: always;
            break-before: page;
        }

        @media print {
            body {
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            .paper-sheet {
                margin: 0;
                padding: 0;
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php
    $queryBase = [
        'filter_desa'        => $filter['kode_desa'],
        'filter_rw'          => $filter['rw'],
        'filter_rt'          => $filter['rt'],
        'filter_sumber_mode' => $filter['sumber_mode'],
        'filter_kategori'    => $filter['kategori'],
        'filter_daya'        => $filter['daya'],
        'filter_desil'       => $filter['desil'],
        'filter_bansos'      => $filter['bansos'],
        'search'             => $search,
    ];
    $urlToggleLampiran = base_url('dtsen/laporan-penerangan/cetak') . '?' . http_build_query(array_merge($queryBase, [
        'lampiran' => $include_lampiran ? '0' : '1',
        'mask'     => $mask_kk ? '1' : '0',
    ]));
    $urlToggleMask = base_url('dtsen/laporan-penerangan/cetak') . '?' . http_build_query(array_merge($queryBase, [
        'lampiran' => $include_lampiran ? '1' : '0',
        'mask'     => $mask_kk ? '0' : '1',
    ]));

    $maskKkStr = static function (string $noKk) use ($mask_kk): string {
        if (!$mask_kk || strlen($noKk) <= 8) {
            return $noKk;
        }
        return substr($noKk, 0, 8) . str_repeat('*', strlen($noKk) - 8);
    };
    $sum = $analytics['summary'];
    ?>

    <div class="no-print">
        <div>
            <strong><i class="fas fa-bolt"></i> Pratinjau Cetak Laporan Sumber Penerangan</strong>
            &bull; Total: <?= number_format($total_bnba, 0, ',', '.') ?> KK
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="<?= esc($urlToggleLampiran) ?>" class="btn-toolbar btn-toggle">
                <i class="fas <?= $include_lampiran ? 'fa-eye-slash' : 'fa-list-ol' ?>"></i>
                <?= $include_lampiran ? 'Sembunyikan Lampiran BNBA' : 'Sertakan Lampiran BNBA (' . number_format($total_bnba, 0, ',', '.') . ' KK)' ?>
            </a>
            <?php if ($include_lampiran): ?>
                <a href="<?= esc($urlToggleMask) ?>" class="btn-toolbar btn-toggle" style="background:#7c3aed;">
                    <i class="fas <?= $mask_kk ? 'fa-unlock' : 'fa-user-shield' ?>"></i>
                    <?= $mask_kk ? 'Tampilkan No. KK Utuh' : 'Sensor No. KK' ?>
                </a>
            <?php endif; ?>
            <button type="button" class="btn-toolbar btn-cetak" onclick="window.print();">
                <i class="fas fa-print"></i> Cetak / Simpan PDF
            </button>
            <button type="button" class="btn-toolbar btn-tutup" onclick="window.close();">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
    </div>

    <div class="paper-sheet">
        <!-- KOP SURAT RESMI -->
        <div class="kop-surat">
            <img src="<?= base_url('landing-page/images/logo-garut.png') ?>" alt="Logo Kabupaten" class="kop-logo" onerror="this.style.display='none'">
            <div class="kop-text">
                <h4>PEMERINTAH KABUPATEN <?= esc(strtoupper($meta['nama_kab'])) ?></h4>
                <h3>KECAMATAN <?= esc(strtoupper($meta['nama_kec'])) ?></h3>
                <?php if (!$meta['is_semua_desa']): ?>
                    <h2>DESA <?= esc(strtoupper($meta['nama_desa'])) ?></h2>
                    <p><?= esc($meta['sekretariat']) ?><br>
                        Email: <?= esc($meta['email_desa']) ?> &bull; Kode Pos: <?= esc($meta['kode_pos']) ?></p>
                <?php else: ?>
                    <h2>REKAPITULASI LINTAS DESA (SINDEN - DTSEN)</h2>
                    <p>Sistem Informasi Data Ekonomi dan Sosial Desa &bull; Kabupaten <?= esc($meta['nama_kab']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="judul-dokumen">
            <h1>LAPORAN KLASIFIKASI KELUARGA BERDASARKAN SUMBER PENERANGAN UTAMA</h1>
            <div class="sub">Basis Data Tunggal Sosial dan Ekonomi Nasional (DTSEN)</div>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 18%;"><b>Cakupan Wilayah</b></td>
                <td style="width: 38%;">: <?= esc($meta['wilayah_label']) ?></td>
                <td style="width: 18%;"><b>Filter Kategori</b></td>
                <td style="width: 26%;">: <?= esc($meta['kategori_label']) ?></td>
            </tr>
            <tr>
                <td><b>Sumber Data</b></td>
                <td>: <?= esc($meta['sumber_label']) ?></td>
                <td><b>Filter Daya / Desil</b></td>
                <td>: <?= esc($meta['daya_label']) ?> / <?= esc($meta['desil_label']) ?></td>
            </tr>
            <tr>
                <td><b>Tanggal Cetak</b></td>
                <td>: <?= esc($meta['printed_at']) ?></td>
                <td><b>Filter Bansos</b></td>
                <td>: <?= esc($meta['bansos_label']) ?></td>
            </tr>
        </table>

        <!-- BAGIAN I: RINGKASAN KATEGORI -->
        <div class="section-title">I. Ringkasan Klasifikasi Sumber Penerangan Utama</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th>Kategori Sumber Penerangan Utama</th>
                    <th style="width: 20%;">Jumlah Keluarga (KK)</th>
                    <th style="width: 18%;">Persentase (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $katRows = [
                    $sum['pln_meteran'],
                    $sum['pln_tanpa'],
                    $sum['non_pln'],
                    $sum['bukan_listrik'],
                    $sum['belum_terdata'],
                ];
                foreach ($katRows as $idx => $kr):
                ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td><?= esc($kr['label']) ?></td>
                        <td class="text-right"><?= number_format($kr['count'], 0, ',', '.') ?></td>
                        <td class="text-right"><?= number_format($kr['percent'], 1, ',', '.') ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-center">TOTAL KELUARGA</td>
                    <td class="text-right"><?= number_format($sum['total'], 0, ',', '.') ?></td>
                    <td class="text-right"><?= $sum['total'] > 0 ? '100,0%' : '0,0%' ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- BAGIAN II: BREAKDOWN DAYA PLN -->
        <div class="section-title">II. Rincian Golongan Daya Listrik (Khusus PLN dengan Meteran)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th>Golongan Daya Listrik Terpasang</th>
                    <th style="width: 20%;">Jumlah Keluarga (KK)</th>
                    <th style="width: 18%;">Porsi dari PLN Meteran (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($analytics['daya_breakdown'] as $idx => $dbItem): ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td><?= esc($dbItem['label']) ?></td>
                        <td class="text-right"><?= number_format($dbItem['count'], 0, ',', '.') ?></td>
                        <td class="text-right"><?= number_format($dbItem['percent'], 1, ',', '.') ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-center">TOTAL PENGGUNA PLN DENGAN METERAN</td>
                    <td class="text-right"><?= number_format($analytics['daya_total'], 0, ',', '.') ?></td>
                    <td class="text-right"><?= $analytics['daya_total'] > 0 ? '100,0%' : '0,0%' ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- BAGIAN III: REKAPITULASI WILAYAH -->
        <div class="section-title">III. Rekapitulasi Klasifikasi per Wilayah (<?= esc($analytics['wilayah_level']) ?>)</div>
        <table class="data-table" style="font-size: 8.6pt;">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th>Wilayah</th>
                    <th>Total</th>
                    <th>PLN Meteran</th>
                    <th>PLN Tanpa Meteran</th>
                    <th>Non-PLN</th>
                    <th>Bukan Listrik</th>
                    <th>Belum Terdata</th>
                    <th>450W</th>
                    <th>900W</th>
                    <th>&ge;1300W</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totW = array_fill(0, 9, 0);
                foreach ($analytics['wilayah_rekap'] as $idx => $w):
                    $vals = [
                        (int) $w['total'],
                        (int) $w['pln_meteran'],
                        (int) $w['pln_tanpa'],
                        (int) $w['non_pln'],
                        (int) $w['bukan_listrik'],
                        (int) $w['belum_terdata'],
                        (int) $w['daya_450'],
                        (int) $w['daya_900'],
                        (int) $w['daya_1300_plus'],
                    ];
                    foreach ($vals as $vi => $vv) {
                        $totW[$vi] += $vv;
                    }
                ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td><?= esc($w['label']) ?></td>
                        <?php foreach ($vals as $vv): ?>
                            <td class="text-right"><?= number_format($vv, 0, ',', '.') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-center">TOTAL</td>
                    <?php foreach ($totW as $tv): ?>
                        <td class="text-right"><?= number_format($tv, 0, ',', '.') ?></td>
                    <?php endforeach; ?>
                </tr>
            </tfoot>
        </table>

        <!-- BAGIAN IV: KORELASI DESIL & BANSOS -->
        <div class="section-title">IV. Korelasi Sumber Penerangan dengan Kelompok Desil &amp; Bansos Aktif</div>
        <table class="data-table" style="font-size: 8.6pt;">
            <thead>
                <tr>
                    <th rowspan="2">Sumber Penerangan Utama</th>
                    <th colspan="3">Kelompok Desil</th>
                    <th colspan="4">Status Penerima Bansos</th>
                    <th rowspan="2">Total KK</th>
                </tr>
                <tr>
                    <th>Desil 1–4</th>
                    <th>Desil 5–10</th>
                    <th>Belum/0</th>
                    <th>PKH</th>
                    <th>SEMBAKO</th>
                    <th>PKH+SMB</th>
                    <th>Non-Bansos</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tCorr = array_fill(0, 8, 0);
                foreach ($analytics['korelasi_desil'] as $katLabel => $dRow):
                    $bRow = $analytics['korelasi_bansos'][$katLabel] ?? [];
                    $cVals = [
                        (int) ($dRow['Desil 1–4'] ?? 0),
                        (int) ($dRow['Desil 5–10'] ?? 0),
                        (int) ($dRow['Belum Ada / Desil 0'] ?? 0),
                        (int) ($bRow['PKH'] ?? 0),
                        (int) ($bRow['SEMBAKO'] ?? 0),
                        (int) ($bRow['PKH + SEMBAKO'] ?? 0),
                        (int) ($bRow['Non-Bansos'] ?? 0),
                        (int) ($dRow['Total'] ?? 0),
                    ];
                    foreach ($cVals as $ci => $cv) {
                        $tCorr[$ci] += $cv;
                    }
                ?>
                    <tr>
                        <td><?= esc($katLabel) ?></td>
                        <?php foreach ($cVals as $cv): ?>
                            <td class="text-right"><?= number_format($cv, 0, ',', '.') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-center">TOTAL</td>
                    <?php foreach ($tCorr as $tc): ?>
                        <td class="text-right"><?= number_format($tc, 0, ',', '.') ?></td>
                    <?php endforeach; ?>
                </tr>
            </tfoot>
        </table>

        <!-- BLOK TANDA TANGAN RESMI -->
        <div class="ttd-wrapper">
            <div class="ttd-col">
                <p style="margin:0;">Mengetahui,<br>Kepala Desa <?= esc($meta['nama_desa']) ?></p>
                <div class="ttd-space"></div>
                <p style="margin:0; font-weight:bold; text-decoration:underline;"><?= esc(strtoupper($meta['kepala_desa'])) ?></p>
            </div>
            <div class="ttd-col">
                <p style="margin:0;"><?= esc($meta['nama_desa']) ?>, <?= esc($meta['tanggal_indo']) ?><br>Operator DTSEN / SINDEN</p>
                <div class="ttd-space"></div>
                <p style="margin:0; font-weight:bold; text-decoration:underline;"><?= esc(strtoupper($meta['operator_nama'])) ?></p>
            </div>
        </div>

        <!-- BAGIAN V: LAMPIRAN BNBA OPSIONAL -->
        <?php if ($include_lampiran): ?>
            <div class="page-break"></div>
            <div class="section-title" style="margin-top:0;">Lampiran: Daftar Keluarga (BNBA) Berdasarkan Sumber Penerangan Utama</div>
            <table class="data-table" style="font-size: 8.2pt;">
                <thead>
                    <tr>
                        <th style="width:4%;">No</th>
                        <th style="width:14%;">No. KK</th>
                        <th>Kepala Keluarga</th>
                        <th>Alamat &amp; Wilayah</th>
                        <th>Sumber Penerangan</th>
                        <th>Daya / Meteran</th>
                        <th>Desil</th>
                        <th>Bansos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dataset)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data keluarga untuk filter yang dipilih.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dataset as $idx => $row): ?>
                            <tr>
                                <td class="text-center"><?= $idx + 1 ?></td>
                                <td><?= esc($maskKkStr($row['no_kk'])) ?></td>
                                <td><?= esc($row['kepala_keluarga']) ?></td>
                                <td><?= esc($row['alamat']) ?> (RW <?= esc($row['rw']) ?>/RT <?= esc($row['rt']) ?>)</td>
                                <td><?= esc($row['sumber_penerangan']) ?></td>
                                <td><?= esc($row['daya_raw'] !== '' ? $row['daya_raw'] : '-') ?></td>
                                <td class="text-center"><?= $row['desil'] !== null ? 'D' . $row['desil'] : '-' ?></td>
                                <td class="text-center"><?= esc($row['bansos']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>
