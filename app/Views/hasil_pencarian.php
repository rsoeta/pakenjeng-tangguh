<div class="modal fade" id="hasil_pencarian" aria-hidden="true" aria-labelledby="hasil_pencarianLabel" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="card">
        <div class="card-header h4 text-center">
          <?= nameApp() . ' - ' . $title; ?>
          <br>
        </div>
        <div class="card-body">
          <h4 class="card-title">Yth:</h4>
          <h5 class="card-text">Menurut database kami sampai saat ini bahwa :
          </h5>
          <div class="table-responsive-sm">
            <table class="table table-striped table-hover">
              <thead class="table-dark">
                <tr>
                  <th scope="col">NO.</th>
                  <th scope="col">NIK</th>
                  <th scope="col">NAMA</th>
                  <th scope="col">ALAMAT</th>
                  <th scope="col">TERDAFTAR PADA</th>
                  <th scope="col">PROGRAM</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1 ?>
                <?php foreach ($tampilData as $row) : ?>
                  <tr>
                    <th scope="row"><?= $no++; ?></th>
                    <td><?= $row['du_nik']; ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['alamat']; ?></td>
                    <td><?= $row['tb_nama'] . ' - ' . $row['created_at_year']; ?></td>
                    <td><?= $row['dbj_nama_bansos']; ?></td>
                  <?php endforeach; ?>
                  <tr>
                  </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer text-muted">
          <p><small class="text-muted">Last updated : <strong><?= hari_ini() . ', ' . date('d') . ' ' . bulan_ini() . ' ' . date('Y'); ?></strong></small></p>
          <button class="btn btn-sm btn-primary d-inline-block w-100" data-bs-toggle="modal" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>