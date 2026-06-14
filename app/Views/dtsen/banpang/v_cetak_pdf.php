<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        /* 🚀 UPGRADE: Ganti font utama ke Bookman Old Style */
        body {
            font-family: "Bookman Old Style", Bookman, Georgia, serif;
            font-size: 11pt;
            color: #000;
            margin: 0;
            padding: 10mm;
        }

        /* 🚀 PERBAIKAN SPASI KOP: Jinakkan margin default agar merapat presisi */
        .kop-surat {
            text-align: center;
            border-bottom: 4px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .kop-surat h2,
        .kop-surat h3,
        .kop-surat h4,
        .kop-surat p {
            margin: 0;
            padding: 1px 0;
        }

        .kop-surat h4 {
            font-size: 12pt;
            text-transform: uppercase;
            font-weight: normal;
            letter-spacing: 0.5px;
        }

        .kop-surat h3 {
            font-size: 14pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .kop-surat h2 {
            font-size: 16pt;
            text-transform: uppercase;
            font-weight: bold;
            line-height: 1.2;
        }

        .kop-surat p {
            font-size: 9.5pt;
            font-style: italic;
            color: #333;
        }

        .judul-laporan {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12pt;
            margin-bottom: 15px;
        }

        .meta-info {
            font-size: 9.5pt;
            margin-bottom: 10px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 10pt;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #777;
        }

        .ttd-container {
            width: 100%;
            margin-top: 30px;
            font-size: 10.5pt;
        }

        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
        }

        .ttd-space {
            height: 70px;
        }

        @media print {
            body {
                padding: 0;
            }

            @page {
                size: portrait;
                margin: 15mm 10mm;
            }
        }
    </style>
</head>

<body>

    <div class="kop-surat">
        <h4>PEMERINTAH KABUPATEN GARUT</h4>
        <h3>KECAMATAN PAKENJENG</h3>
        <h2>DESA PASIRLANGU</h2>
        <p>Jl. Desa Km. 200 Kp. Rahayu Desa Pasirlangu Kec. Pakenjeng Kab. Garut</p>
        <p>Email: info@pasirlangu.desa.id Kode Pos: 44163</p>
    </div>

    <div class="judul-laporan">
        REKAPITULASI BANTUAN PANGAN HASIL VALIDASI
    </div>

    <table class="meta-info" style="border: none !important; margin-bottom: 15px;">
        <tr style="border: none !important;">
            <td style="border: none !important; padding: 2px 0; width: 15%;"><b>Filter Wilayah</b></td>
            <td style="border: none !important; padding: 2px 0;">:
                RW <?= !empty($filter_rw) ? esc($filter_rw) : 'Semua' ?> /
                RT <?= !empty($filter_rt) ? esc($filter_rt) : 'Semua' ?>
            </td>
            <td style="border: none !important; padding: 2px 0; text-align: right; color: #555;">
                Dicetak: <?= date('d-m-Y H:i') ?>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="20%">No. PBP (Undangan)</th>
                <th width="33%">Nama KPM / NIK</th>
                <th width="13%" class="text-center">RT/RW</th>
                <th width="20%">Waktu Kehadiran</th>
                <th width="10%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($query)): ?>
                <?php $no = 1;
                foreach ($query as $row): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= esc($row['no_pbp']) ?></td>
                        <td>
                            <b><?= esc($row['nama_kpm']) ?></b><br>
                            <small class="text-muted"><?= esc($row['nik_kpm']) ?></small>
                        </td>
                        <td>
                            <?php
                            $teksAlamat = !empty($row['alamat']) ? esc($row['alamat']) . '<br>' : '';
                            $rtrw = (!empty($row['rt']) && !empty($row['rw'])) ? 'RT ' . esc($row['rt']) . ' / RW ' . esc($row['rw']) : '-';
                            echo $teksAlamat . $rtrw;
                            ?>
                        </td>
                        <td><?= date('d/m/Y H:i:s', strtotime($row['waktu_scan'])) ?></td>
                        <td class="text-center">
                            <?= ($row['status_kelengkapan'] == 1) ? 'Selesai' : 'Ter-Scan' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data terekap untuk filter ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Petugas Operator Pencatatan,</p>
            <div class="ttd-space"></div>
            <p style="text-decoration: underline; font-weight: bold;"><?= esc($nama_petugas) ?></p>
            <p class="text-muted" style="font-size: 8.5pt; margin-top: -10px;">SINDEN Banpang Module</p>
        </div>
    </div>

    <script type="text/javascript">
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 500);
        }
    </script>
</body>

</html>