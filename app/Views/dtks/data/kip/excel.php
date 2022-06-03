<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data Usulan.xlsx");
?>
<html>

<body>
    <table border="1">
        <thead class="text-black">
            <tr>
                <th>NO</th>
                <th>NAMA</th>
                <th>NO. KK</th>
                <th>NIK</th>
                <th>JENIS KELAMIN</th>
                <th>TEMPAT LAHIR</th>
                <th>TANGGAL LAHIR</th>
                <th>IBU KANDUNG</th>
                <th>JENIS PEKERJAAN</th>
                <th>STATUS PERKAWINAN</th>
                <th>ALAMAT</th>
                <th>RT</th>
                <th>RW</th>
                <th>DESA</th>
                <th>KECAMATAN</th>
                <th>SHDK</th>
                <th>FOTO RUMAH</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($usulaDtks as $row) : ?>
                <tr>
                    <td><?= $i; ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['nokk']; ?></td>
                    <td><?= $row['nik']; ?></td>
                    <td><?= $row['jenis_kelamin']; ?></td>
                    <td><?= $row['tempat_lahir']; ?></td>
                    <td><?= $row['tanggal_lahir']; ?></td>
                    <td><?= $row['ibu_kandung']; ?></td>
                    <td><?= $row['jenis_pekerjaan']; ?></td>
                    <td><?= $row['status_perkawinan']; ?></td>
                    <td><?= $row['alamat']; ?></td>
                    <td><?= $row['rt']; ?></td>
                    <td><?= $row['rw']; ?></td>
                    <td><?= $row['kelurahan']; ?></td>
                    <td><?= $row['kecamatan']; ?></td>
                </tr>
                <?php $i++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>