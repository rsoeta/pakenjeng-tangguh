<div class="container-fluid">
    <h5 class="mb-3">Detail KK</h5>

    <table class="table table-sm table-bordered">
        <tr>
            <th>No KK</th>
            <td><?= esc($kk['no_kk']) ?></td>
        </tr>
        <tr>
            <th>Kepala Keluarga</th>
            <td><?= esc($kk['kepala_keluarga']) ?></td>
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
            <th>Jumlah Anggota (field)</th>
            <td><?= esc($kk['jumlah_anggota']) ?></td>
        </tr>
        <tr>
            <th>Program Bansos</th>
            <td><?= esc($kk['program_bansos']) ?></td>
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
    <table class="table table-sm table-striped">
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
                    <td><?= esc($a['shdk']) ?></td>
                    <td><?= esc($a['nik']) ?></td>
                    <td><?= esc($a['nama']) ?></td>
                    <td><?= esc($a['jenis_kelamin']) ?></td>
                    <td><?= esc($a['tanggal_lahir']) ?></td>
                    <td><?= esc($a['pendidikan_terakhir']) ?></td>
                    <td><?= esc($a['pekerjaan']) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>