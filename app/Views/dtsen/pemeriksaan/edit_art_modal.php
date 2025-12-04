<!-- <div class="container-fluid"> -->

<form id="formEditART">

    <!-- hidden untuk fetch() -->
    <input type="hidden" name="id_art" value="<?= esc($art['id_art']) ?>">
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div class="row g-3">

        <div class="col-md-6">
            <label class="form-label fw-bold">NIK</label>
            <input type="text" name="nik" class="form-control" value="<?= esc($art['nik']) ?>">
            <small class="text-muted">Jika diubah, wajib mengisi alasan perubahan.</small>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="<?= esc($art['nama']) ?>" required>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">SHDK</label>
            <select name="shdk" class="form-select">
                <?php foreach ($shdk_list as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $s['id'] == $art['shdk'] ? 'selected' : '' ?>>
                        <?= $s['jenis_shdk'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select">
                <option value="L" <?= $art['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= $art['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control"
                value="<?= esc($art['tanggal_lahir']) ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Pendidikan Terakhir</label>
            <select name="pendidikan_terakhir" class="form-select">
                <?php foreach ($pendidikan_list as $p): ?>
                    <option value="<?= $p['pk_id'] ?>" <?= $p['pk_id'] == $art['pendidikan_terakhir'] ? 'selected' : '' ?>>
                        <?= $p['pk_nama'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Pekerjaan</label>
            <select name="pekerjaan" class="form-select">
                <?php foreach ($pekerjaan_list as $p): ?>
                    <option value="<?= $p['pk_id'] ?>" <?= $p['pk_id'] == $art['pekerjaan'] ? 'selected' : '' ?>>
                        <?= $p['pk_nama'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Disabilitas</label>
            <select name="disabilitas" class="form-select">
                <option value="">Tidak Ada</option>
                <?php foreach ($disabilitas_list as $d): ?>
                    <option value="<?= $d['dj_id'] ?>" <?= $d['dj_id'] == $art['disabilitas'] ? 'selected' : '' ?>>
                        <?= $d['dj_keterangan'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Status Hamil</label>
            <select name="status_hamil" class="form-select">
                <option value="Tidak" <?= $art['status_hamil'] == 'Tidak' ? 'selected' : '' ?>>Tidak</option>
                <option value="Ya" <?= $art['status_hamil'] == 'Ya' ? 'selected' : '' ?>>Ya</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Ibu Kandung</label>
            <input type="text" name="ibu_kandung" class="form-control"
                value="<?= esc($art['ibu_kandung']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">No. KK</label>
            <input type="text" name="no_kk" class="form-control"
                value="<?= esc($art['no_kk']) ?>" readonly>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-bold">Alasan Perubahan NIK (jika NIK diganti)</label>
            <textarea name="reason_nik" class="form-control" rows="2"></textarea>
        </div>

    </div>

    <div class="mt-4 text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary px-4">
            Simpan Perubahan
        </button>
    </div>

</form>

</div><!-- </div> -->