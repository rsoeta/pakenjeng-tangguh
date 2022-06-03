<?= $this->extend('dtks/templates/index'); ?>

<?= $this->section('content'); ?>

<div class="content-wrapper mt-1" style="min-height: 1325.2px;">
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <div class="row text-center">
              <h5 class="card-title"><strong><?= $title; ?></strong></h5>
            </div>
          </div>
          <?php if (session()->get('level') < 3) { ?>
            <div class="container">
              <div class="row mb-2">
                <div class="col-12 col-sm-6">
                  <button type="button" class="btn float-right btn-primary btn-block tombolTambah"><i class="fa fa-plus"> Tambah Data</i></button>
                </div>
              </div>
            </div>
          <?php } ?>
          <?php
          $user = session()->get('level');
          $nik = session()->get('nik');
          $jabatan = session()->get('jabatan');
          $desa_id = '2006';
          ?>
          <div class="container">
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
                  <option value="">-Pilih Desa-</option>
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
                  <option value="">-Pilih Operator-</option>
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
                  <option value="">-Pilih Rw-</option>
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
                  <option value="">-Pilih Keterangan-</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="card-body">
    <div class="row">
      <div class="col-12">
        <table id="tbl_janda" class="table table-bordered table-striped compact">
          <thead class="bg-warning">
            <tr role="row">
              <th>No</th>
              <th>Nama</th>
              <th>NIK</th>
              <th>No. KK</th>
              <th>Tempat Lahir</th>
              <th>Tanggal Lahir</th>
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
  </div>
</div>

<div class="viewmodal" style="display: none;"></div>
<script>
  table = $('#tbl_janda').DataTable({
    'order': [],
    'fixedHeader': true,
    'searching': true,
    'paging': true,
    'responsive': true,
    'processing': true,
    'serverSide': true,
    "ajax": {
      "url": "<?= site_url('dtks/janda_data'); ?>",
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

  $(document).ready(function() {
    $('.tombolTambah').click(function(e) {
      e.preventDefault();
      $.ajax({
        type: "GET",
        url: "<?= site_url('dtks/tambahjanda'); ?>",
        dataType: "json",
        success: function(response) {
          $('.viewmodal').html(response.data).show();

          $('#modaltambah').modal('show');
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
        }
      });
    });
  });
</script>
<?= $this->endSection(); ?>