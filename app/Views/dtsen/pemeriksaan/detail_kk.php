<div class="container-fluid">

    <table class="table table-sm table-bordered">
        <tr>
            <th>No KK</th>
            <td><?= esc($kk['no_kk']) ?></td>
        </tr>
        <tr>
            <th>Kepala Keluarga</th>
            <td><strong><?= esc($kk['kepala_keluarga']) ?></strong></td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td><?= esc($kk['alamat']) ?></td>
        </tr>
        <tr>
            <th>RW / RT</th>
            <td><?= esc($kk['rw']) ?> / <?= esc($kk['rt']) ?></td>
        </tr>
        <tr>
            <th>Jumlah Anggota</th>
            <td><?= esc($jumlahAnggota) ?></td>
        </tr>
        <tr>
            <th>Program Bansos</th>
            <td><?= esc($kk['dbj_nama_bansos'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Kategori Adat</th>
            <td><?= esc($kk['kategori_adat']) ?></td>
        </tr>
        <tr>
            <th>Foto KK</th>
            <td><?= $kk['foto_kk'] ? '<img src="' . base_url($kk['foto_kk']) . '" width="150">' : 'Tidak ada' ?></td>
        </tr>
        <tr>
            <th>Foto Rumah</th>
            <td><?= $kk['foto_rumah'] ? '<img src="' . base_url($kk['foto_rumah']) . '" width="150">' : 'Tidak ada' ?></td>
        </tr>
        <tr>
            <th>Foto Rumah Dalam</th>
            <td><?= $kk['foto_rumah_dalam'] ? '<img src="' . base_url($kk['foto_rumah_dalam']) . '" width="150">' : 'Tidak ada' ?></td>
        </tr>
    </table>

    <h6 class="mt-4">Daftar Anggota KK</h6>

    <table class="table table-sm table-striped" id="tableART">
        <thead>
            <tr>
                <th>SHDK</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Tgl Lahir</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arts as $a): ?>
                <tr>
                    <td><?= esc($a['jenis_shdk'] ?? '-') ?></td>
                    <td><?= esc($a['nik']) ?></td>
                    <td><?= esc($a['nama']) ?></td>
                    <td><?= esc($a['jenis_kelamin']) ?></td>
                    <td><?= esc($a['tanggal_lahir']) ?></td>
                    <td><?= esc($a['pendidikan_nama'] ?? '-') ?></td>
                    <td><?= esc($a['pekerjaan_nama'] ?? '-') ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>