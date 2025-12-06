<table class="table table-sm table-bordered">

    <tr>
        <th>NIK</th>
        <td><?= esc($art['nik']) ?></td>
    </tr>

    <tr>
        <th>Nama</th>
        <td><?= esc($art['nama']) ?></td>
    </tr>

    <tr>
        <th>SHDK</th>
        <td><?= esc($art['jenis_shdk'] ?? '-') ?></td>
    </tr>

    <tr>
        <th>Jenis Kelamin</th>
        <td><?= esc($art['jenis_kelamin']) ?></td>
    </tr>

    <tr>
        <th>Tanggal Lahir</th>
        <td><?= esc($art['tanggal_lahir']) ?></td>
    </tr>

    <tr>
        <th>Pendidikan</th>
        <td><?= esc($art['pendidikan_nama'] ?? '-') ?></td>
    </tr>

    <tr>
        <th>Pekerjaan</th>
        <td><?= esc($art['pekerjaan_nama'] ?? '-') ?></td>
    </tr>

    <tr>
        <th>Status Hamil</th>
        <td><?= esc($art['status_hamil']) ?></td>
    </tr>

    <tr>
        <th>Ibu Kandung</th>
        <td><?= esc($art['ibu_kandung']) ?></td>
    </tr>

    <tr>
        <th>Program Bansos</th>
        <td><?= esc($art['bantuan_nama'] ?? '-') ?></td>
    </tr>

    <tr>
        <th>No KK</th>
        <td><?= esc($art['no_kk']) ?></td>
    </tr>

    <tr>
        <th>Kepala Keluarga</th>
        <td><?= esc($art['kepala_keluarga']) ?></td>
    </tr>

    <tr>
        <th>RW / RT</th>
        <td><?= esc($art['rw']) ?> / <?= esc($art['rt']) ?></td>
    </tr>
</table>