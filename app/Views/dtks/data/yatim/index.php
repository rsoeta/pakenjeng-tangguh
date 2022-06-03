<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1" style="min-height: 1325.2px;">
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="row text-center mb-2">
              <h5 class="card-title"><strong><?= $title; ?></strong></h5>
            </div>
            <?php if (session()->get('level') < 3) { ?>
                <div class="row mb-2">
                  <div class="col-12 col-sm-3">
                    <a href="" class="btn float-right btn-primary btn-block"><i class="fa fa-plus"> Tambah Data</i></a>
                  </div>
                </div>
            <?php } ?>
            <?php
            $user = session()->get('level');
            $nik = session()->get('nik');
            $jabatan = session()->get('jabatan');
            $desa_id = '2006';
            ?>
              <div class="row">
                <div class="col-sm-1 col-3 mb-1">
                  <label for="desa" class="form-label">
                    Desa
                  </label>
                </div>
                <div class="col-sm-2 col-9">
                  <select <?php if ($user >= 2) {
                            echo 'disabled="disabled"';
                          } ?> class="form-control form-control-sm" name="desa" id="desa">
                    <option value="">-Pilih-</option>
                    <?php foreach ($desKels as $row) { ?>
                      <option <?php if ($desa_id == $row['KodeDesa']) {
                                echo 'selected';
                              } ?> value="<?= $row['KodeDesa']; ?>"><?= $row['nama_desa']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-1 col-3 mb-1">
                  <label for="operator" class="form-label">
                    Operator
                  </label>
                </div>
                <div class="col-sm-2 col-9">
                  <select <?php if ($user >= 2) {
                            echo 'disabled="disabled"';
                          } ?> class="form-control form-control-sm" name="operator" id="operator">
                    <option value="">-Pilih-</option>
                    <?php foreach ($operator as $row) { ?>
                      <option <?php if ($nik == $row['nik']) {
                                echo 'selected';
                              } ?> value="<?php echo $row['nik']; ?>"><?php echo strtoupper($row['fullname']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-1 col-3 mb-1">
                  <label for="rw" class="form-label">
                    No. RW
                  </label>
                </div>
                <div class="col-sm-2 col-9">
                  <select <?php if ($user >= 2) {
                            echo 'disabled="disabled"';
                          } ?> class="form-control form-control-sm" name="rw" id="rw">
                    <option value="">-Pilih-</option>
                    <?php foreach ($Rw as $row) { ?>
                      <option <?php if ($jabatan == $row['no_rw']) {
                                echo 'selected';
                              } ?> value="<?php echo $row['no_rw']; ?>"><?php echo $row['no_rw']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-sm-1 col-3 mb-1">
                  <label for="keterangan" class="form-label">
                    Keterangan
                  </label>
                </div>
                <div class="col-sm-2 col-9">
                  <select <?php if ($user >= 2) {
                            echo 'disabled="disabled"';
                          } ?> class="form-control form-control-sm" name="keterangan" id="keterangan">
                    <option value="">-Pilih-</option>
                    <?php foreach ($bansos as $row) { ?>
                      <option value="<?= $row['Id']; ?>"><?= $row['NamaBansos']; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <table id="tbl_yatim_data" class="table table-bordered compact">
          <thead class="bg-success">
            <tr role="row">
              <th>No</th>
              <th>Nama Anak</th>
              <th>Tempat Lahir</th>
              <th>Tanggal Lahir</th>
              <th>Jenis Kelamin</th>
              <th>Nama Wali</th>
              <th>No. Tlp Wali</th>
              <th>Alamat</th>
              <th>RT</th>
              <th>RW</th>
              <th>Desa</th>
              <th>#</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <div class="accordion" id="accordionExample">
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
            <strong></strong>
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
          <div class="accordion-body">
            <section class="content">
              <!-- Default box -->
              <div class="card card-solid">
                <div class="card-body pb-0">
                  <div class="row d-flex align-items-stretch">
                    <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
                      <div class="card bg-light">
                        <div class="card-header text-muted border-bottom-0">
                          Digital Strategist
                        </div>
                        <div class="card-body pt-0">
                          <div class="row">
                            <div class="col-7">
                              <h2 class="lead"><b>Nicole Pearson</b></h2>
                              <p class="text-muted text-sm"><b>About: </b> Web Designer / UX / Graphic Artist / Coffee Lover </p>
                              <ul class="ml-4 mb-0 fa-ul text-muted">
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Address: Demo Street 123, Demo City 04312, NJ</li>
                                <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Phone #: + 800 - 12 12 23 52</li>
                              </ul>
                            </div>
                            <div class="col-5 text-center">
                              <img src="<?= base_url(); ?>/assets/img/new_logo.png" alt="user-avatar" class="img-circle img-fluid">
                            </div>
                          </div>
                        </div>
                        <div class="card-footer">
                          <div class="text-right">
                            <a href="#" class="btn btn-sm bg-teal">
                              <i class="fas fa-comments"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-primary">
                              <i class="fas fa-user"></i> View Profile
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <nav aria-label="Contacts Page Navigation">
                    <ul class="pagination justify-content-center m-0">
                      <li class="page-item active"><a class="page-link" href="#">1</a></li>
                      <li class="page-item"><a class="page-link" href="#">2</a></li>
                      <li class="page-item"><a class="page-link" href="#">3</a></li>
                      <li class="page-item"><a class="page-link" href="#">4</a></li>
                      <li class="page-item"><a class="page-link" href="#">5</a></li>
                      <li class="page-item"><a class="page-link" href="#">6</a></li>
                      <li class="page-item"><a class="page-link" href="#">7</a></li>
                      <li class="page-item"><a class="page-link" href="#">8</a></li>
                    </ul>
                  </nav>
                </div>
                <!-- /.card-footer -->
              </div>
              <!-- /.card -->

            </section>
            <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingThree">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
            Accordion Item #3
          </button>
        </h2>
        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
          <div class="accordion-body">
            <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  table = $('#tbl_yatim_data').DataTable({
    'order': [],
    'fixedHeader': true,
    'searching': true,
    'paging': true,
    'responsive': true,
    'processing': true,
    'serverSide': true,
    "ajax": {
      "url": "<?= site_url('dtks/yatim_data'); ?>",
      "type": "POST",
      "data": {
        "csrf_test_name": $('input[name=csrf_test_name]').val()
      },
      "data": function(data) {
        data.csrf_test_name = $('input[name=csrf_test_name]').val();
        data.desa = $('#desa').val();
        data.rw = $('#rw').val();
        // data.operator = $('#operator').val();
        data.keterangan = $('#keterangan').val();
      },
      "dataSrc": function(response) {
        $('input[name=csrf_test_name]').val(response.csrf_test_name);
        return response.data;
      }
    },

    "columnDefs": [{
      "targets": [0],
      "orderable": false
    }]
  });

  $('#desa').change(function() {
    table.draw();
  });
  $('#rw').change(function() {
    table.draw();
  });
  // $('#operator').change(function() {
  //   table.draw();
  // });
  $('#keterangan').change(function() {
    table.draw();
  });
</script>

<?= $this->endSection(); ?>