<?= $this->extend('templates/index'); ?>

<?= $this->section('content'); ?>

<style>
  /* 🏷️ Warna latar Usia konsisten di mode Desktop & Mobile (termasuk saat responsif) */
  #tbl_lansia .badge-info,
  #tbl_lansia .badge.bg-info,
  table.dtr-details .badge-info,
  table.dtr-details .badge.bg-info,
  #modal_usia {
    background-color: #17a2b8 !important;
    color: #ffffff !important;
  }

  /* Setarakan baris rincian child row DataTables di mobile agar tidak belang */
  table.dataTable > tbody > tr.child {
    background-color: inherit !important;
  }
  table.dataTable > tbody > tr.child > td.child {
    background-color: inherit !important;
  }
  table.dataTable > tbody > tr.child table.dtr-details {
    background-color: transparent !important;
  }
</style>

<div class="content-wrapper mt-1">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark font-weight-bold">
            <i class="fas fa-blind text-primary mr-2"></i><?= $title; ?>
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-right">
            <li class="breadcrumb-item"><a href="<?= base_url('/pages'); ?>">Home</a></li>
            <li class="breadcrumb-item active"><?= $title; ?></li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">

      <!-- 💡 Kotak Informasi: Perbedaan Lansia & Jompo -->
      <div class="card card-outline card-info collapsed-card mb-3 shadow-sm">
        <div class="card-header bg-light py-2" data-card-widget="collapse" style="cursor: pointer;">
          <h3 class="card-title text-info font-weight-bold">
            <i class="fas fa-info-circle mr-2"></i>Panduan Perbedaan Kategori: Lansia vs Jompo
          </h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </div>
        <div class="card-body py-3" style="display: none;">
          <p class="text-secondary mb-3">
            <strong>Perbedaan utama terletak pada fokusnya:</strong> <em>Lansia (lanjut usia)</em> adalah istilah formal untuk menunjukkan kelompok umur seseorang yang sudah tua, sedangkan <em>Jompo</em> merujuk pada kondisi fisik yang sudah renta, lemah, atau tidak berdaya.
          </p>
          <div class="row">
            <div class="col-md-6 mb-2">
              <div class="p-3 bg-white rounded border border-success h-100 shadow-sm">
                <h6 class="font-weight-bold text-success mb-2">
                  <i class="fas fa-user-check mr-1"></i> Pengertian Lansia
                </h6>
                <ul class="pl-3 mb-0 small text-muted" style="line-height: 1.6;">
                  <li><strong>Definisi:</strong> Merujuk <em>strictly</em> pada batasan umur seseorang yang telah memasuki usia lanjut (biasanya di atas 60 tahun).</li>
                  <li><strong>Kondisi:</strong> Tidak terikat pada kondisi fisik; seorang lansia bisa saja masih sangat bugar, aktif, dan sehat secara fisik maupun mental.</li>
                  <li><strong>Sifat:</strong> Netral dan formal.</li>
                </ul>
              </div>
            </div>
            <div class="col-md-6 mb-2">
              <div class="p-3 bg-white rounded border border-warning h-100 shadow-sm">
                <h6 class="font-weight-bold text-warning mb-2">
                  <i class="fas fa-wheelchair mr-1"></i> Pengertian Jompo
                </h6>
                <ul class="pl-3 mb-0 small text-muted" style="line-height: 1.6;">
                  <li><strong>Definisi:</strong> Merujuk pada kondisi fisik atau kemampuan tubuh yang sudah sangat menurun, renta, dan kesulitan beraktivitas mandiri.</li>
                  <li><strong>Kondisi:</strong> Tidak selalu terikat pada usia tua. Dalam bahasa sehari-hari, istilah ini bahkan bisa dipakai untuk anak muda yang tubuhnya cepat lelah, pegal-pegal, atau tidak bertenaga.</li>
                  <li><strong>Sifat:</strong> Cenderung menggambarkan kelemahan fungsi tubuh.</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Data Table -->
      <div class="card card-primary card-outline shadow-sm">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="card-title font-weight-bold text-dark m-0">
              <i class="fas fa-table mr-1 text-primary"></i> Data Penduduk Lansia (Usia > 60 Tahun dari DTSEN)
            </h5>
            <div>
              <button type="button" id="btnExportExcel" class="btn btn-success btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-file-excel mr-1"></i> Download Excel
              </button>
            </div>
          </div>
        </div>

        <div class="card-body">
          <?php
          $roleId   = $userRole ?? session()->get('role_id') ?? 4;
          $desa_id  = $userDesa ?? session()->get('kode_desa') ?? '32.05.33.2006';
          ?>

          <!-- Filter Bar (1 Baris di Mode Mobile & Desktop) -->
          <div class="card card-outline card-secondary bg-light p-2 p-md-3 mb-2 mb-md-3">
            <div class="form-row align-items-end flex-nowrap">
              <div class="col-3 col-md-3">
                <label for="filter_desa" class="small font-weight-bold text-muted mb-1 text-truncate d-block" style="font-size: 0.75rem;">Desa</label>
                <select <?php if ($roleId >= 3) {
                          echo 'disabled="disabled"';
                        } ?> class="form-control form-control-sm px-1 px-sm-2" name="filter_desa" id="filter_desa" style="font-size: 0.78rem;">
                  <?php if ($roleId < 3): ?>
                    <option value="">Semua</option>
                  <?php endif; ?>
                  <?php foreach ($desKels as $row) {
                    $rawKd = (string)($row['kode_desa'] ?? $row['KodeDesa']);
                    $cleanKd = str_replace('.', '', $rawKd);
                    $dottedKd = '32.05.33.' . substr($cleanKd, -4);
                    $isSelected = ($desa_id === $dottedKd || str_replace('.', '', $desa_id) === $cleanKd);
                  ?>
                    <option <?= $isSelected ? 'selected' : ''; ?> value="<?= $dottedKd; ?>"><?= $row['nama_desa']; ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="col-3 col-md-3">
                <label for="filter_rw" class="small font-weight-bold text-muted mb-1 text-truncate d-block" style="font-size: 0.75rem;">No. RW</label>
                <select class="form-control form-control-sm px-1 px-sm-2" name="filter_rw" id="filter_rw" style="font-size: 0.78rem;">
                  <option value="">Semua RW</option>
                  <?php foreach ($Rw as $row) { ?>
                    <option value="<?= $row['no_rw']; ?>">RW <?= $row['no_rw']; ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="col-4 col-md-3">
                <label for="filter_kategori" class="small font-weight-bold text-muted mb-1 text-truncate d-block" style="font-size: 0.75rem;">Status</label>
                <select class="form-control form-control-sm px-1 px-sm-2" name="filter_kategori" id="filter_kategori" style="font-size: 0.78rem;">
                  <option value="">Semua Status</option>
                  <option value="Lansia">Lansia</option>
                  <option value="Jompo">Jompo</option>
                </select>
              </div>

              <div class="col-2 col-md-3">
                <label class="small font-weight-bold text-muted mb-1 d-block text-truncate" style="font-size: 0.75rem;">&nbsp;</label>
                <button type="button" id="btnResetFilter" class="btn btn-default btn-sm btn-block px-1" title="Reset Filter">
                  <i class="fas fa-undo"></i><span class="d-none d-md-inline ml-1">Reset Filter</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Tabel Data -->
          <div class="table-responsive">
            <table id="tbl_lansia" class="table table-bordered table-striped table-hover compact w-100" style="font-size: 0.9rem;">
              <thead class="bg-light text-center">
                <tr role="row">
                  <th style="width: 3%;">No</th>
                  <th style="width: 14%;">NIK</th>
                  <th style="width: 15%;">Nama</th>
                  <th style="width: 14%;">Alamat</th>
                  <th style="width: 7%;">Rt/Rw</th>
                  <th style="width: 6%;">Usia</th>
                  <th style="width: 5%;">JK</th>
                  <th style="width: 14%;">Tempat Tinggal / Keadaan Rumah</th>
                  <th style="width: 10%;">Pendidikan Terakhir</th>
                  <th style="width: 8%;">Keterangan</th>
                  <th style="width: 4%;">Aksi</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

        </div>
      </div>

    </div>
  </section>
</div>

<!-- Modal Pilih Status Kategori (Lansia / Jompo) -->
<div class="modal fade" id="modalStatus" tabindex="-1" role="dialog" aria-labelledby="modalStatusLabel">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white py-2">
        <h5 class="modal-title font-weight-bold" id="modalStatusLabel" style="font-size: 1.1rem;">
          <i class="fas fa-user-tag mr-2"></i>Pilih Status Kategori
        </h5>
        <button type="button" id="btnCloseStatus" class="close btn-close btn-close-white text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formStatus">
        <input type="hidden" name="id_art" id="modal_id_art">
        <div class="modal-body">

          <!-- Info Person -->
          <div class="alert alert-light border mb-3 p-2">
            <div class="row align-items-center">
              <div class="col-8">
                <span class="d-block font-weight-bold text-primary" id="modal_nama" style="font-size: 1.05rem;">-</span>
                <div class="text-muted small mt-1 d-flex align-items-center">
                  <i class="far fa-id-card mr-1 text-primary"></i>
                  <span>NIK: </span>
                  <span id="modal_nik" class="font-weight-bold text-dark ml-1" style="cursor: pointer; border-bottom: 1px dashed #6c757d;" title="Sorot atau klik untuk melihat NIK lengkap">-</span>
                  <button type="button" class="btn btn-xs btn-light border ml-1 py-0 px-1" id="btnCopyModalNik" title="Salin NIK">
                    <i class="fas fa-copy text-secondary"></i>
                  </button>
                </div>
              </div>
              <div class="col-4 text-right">
                <span class="badge badge-info bg-info text-white p-2" id="modal_usia" style="font-size: 0.9rem;">- Thn</span>
              </div>
            </div>
          </div>

          <!-- Panduan Singkat -->
          <div class="callout callout-info py-2 px-3 mb-3 small bg-light">
            <strong><i class="fas fa-lightbulb text-warning mr-1"></i> Perbedaan Fokus:</strong>
            <div class="mt-1 text-muted">
              • <strong>Lansia:</strong> Kelompok umur (>60 tahun), fisik bisa tetap bugar & sehat.<br>
              • <strong>Jompo:</strong> Kondisi fisik yang sangat menurun, renta, lemah, atau tidak berdaya.
            </div>
          </div>

          <!-- Pilihan Kategori -->
          <div class="form-group mb-3">
            <label class="font-weight-bold small text-dark d-block">Kategori:</label>
            <div class="row">
              <div class="col-6">
                <div class="custom-control custom-radio p-2 border rounded text-center">
                  <input type="radio" id="kat_lansia" name="kategori" value="Lansia" class="custom-control-input" checked>
                  <label class="custom-control-label text-success font-weight-bold" for="kat_lansia" style="cursor: pointer;">
                    <i class="fas fa-user-check d-block mb-1" style="font-size: 1.3rem;"></i> Lansia
                  </label>
                </div>
              </div>
              <div class="col-6">
                <div class="custom-control custom-radio p-2 border rounded text-center">
                  <input type="radio" id="kat_jompo" name="kategori" value="Jompo" class="custom-control-input">
                  <label class="custom-control-label text-warning font-weight-bold" for="kat_jompo" style="cursor: pointer;">
                    <i class="fas fa-wheelchair d-block mb-1" style="font-size: 1.3rem;"></i> Jompo
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Keterangan Tambahan -->
          <div class="form-group mb-1">
            <label for="modal_keterangan" class="font-weight-bold small text-dark">Keterangan Tambahan (Opsional):</label>
            <textarea class="form-control form-control-sm" name="keterangan" id="modal_keterangan" rows="2" placeholder="Contoh: Kondisi bugar / Sulit beraktivitas mandiri / Stroke..."></textarea>
          </div>

        </div>
        <div class="modal-footer py-2 d-flex justify-content-between align-items-center bg-light">
          <button type="button" id="btnBatalStatus" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="btnSimpanStatus" class="btn btn-primary btn-sm font-weight-bold">
            <i class="fas fa-save mr-1"></i> Simpan Status
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    var defaultDesa = '<?= $desa_id; ?>';

    var table = $('#tbl_lansia').DataTable({
      'order': [],
      'fixedHeader': true,
      'searching': true,
      'paging': true,
      'responsive': true,
      'processing': true,
      'serverSide': true,
      "ajax": {
        "url": "<?= site_url('lansia-jompo/data'); ?>",
        "type": "POST",
        "data": function(data) {
          data.csrf_test_name = $('input[name=csrf_test_name]').val() || (window.CSRF ? window.CSRF.value : '');
          data.desa = $('#filter_desa').val() || defaultDesa;
          data.rw = $('#filter_rw').val();
          data.kategori = $('#filter_kategori').val();
        },
        "dataSrc": function(response) {
          if (response.csrf_test_name) {
            $('input[name=csrf_test_name]').val(response.csrf_test_name);
            if (window.CSRF) {
              window.CSRF.value = response.csrf_test_name;
            }
          }
          return response.data || [];
        }
      },
      "columnDefs": [{
        "targets": [0, 10],
        "orderable": false
      }]
    });

    // 📋 FUNGSI SALIN KE CLIPBOARD
    function copyToClipboard(text, label) {
      label = label || 'NIK';
      if (!text || text === '-' || text === 'null') {
        return;
      }
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
          showCopyToast(label + ' berhasil disalin!');
        }).catch(function() {
          fallbackCopyText(text, label);
        });
      } else {
        fallbackCopyText(text, label);
      }
    }

    function fallbackCopyText(text, label) {
      var temp = document.createElement("input");
      temp.value = text;
      document.body.appendChild(temp);
      temp.select();
      try {
        document.execCommand("copy");
        showCopyToast(label + ' berhasil disalin!');
      } catch (e) {
        alert('Gagal menyalin ' + label);
      }
      document.body.removeChild(temp);
    }

    function showCopyToast(msg) {
      if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true
        });
        Toast.fire({
          icon: 'success',
          title: msg
        });
      } else {
        alert(msg);
      }
    }

    // Event Salin NIK dari Tabel
    $(document).on('click', '.btnCopyNik', function(e) {
      e.stopPropagation();
      var val = $(this).attr('data-value');
      copyToClipboard(val, 'NIK');
    });

    // Event Salin NIK dari Modal
    $('#btnCopyModalNik').click(function(e) {
      e.preventDefault();
      var val = $(this).attr('data-value');
      copyToClipboard(val, 'NIK');
    });

    // Event Trigger Filter
    $('#filter_desa, #filter_rw, #filter_kategori').change(function() {
      table.draw();
    });

    $('#btnResetFilter').click(function() {
      $('#filter_rw').val('');
      $('#filter_kategori').val('');
      table.search('').draw();
    });

    // Modal Ubah Status Kategori
    $(document).on('click', '.btnPilihKategori', function() {
      var id = $(this).data('id');
      var nama = $(this).data('nama');
      var nik = String($(this).data('nik') || '');
      var usia = $(this).data('usia');
      var kategori = $(this).data('kategori');
      var keterangan = $(this).data('keterangan');

      $('#modal_id_art').val(id);
      $('#modal_nama').text(nama);

      // Masking NIK di Modal
      var nikMasked = '-';
      if (nik && nik !== '-') {
        nikMasked = (nik.length === 16) ?
          nik.substring(0, 6) + '******' + nik.substring(12, 16) :
          ((nik.length > 8) ? nik.substring(0, 8) + '********' : nik);
      }

      $('#modal_nik').text(nikMasked)
        .attr('data-masked', nikMasked)
        .attr('data-full', (nik && nik !== '-') ? nik : '-')
        .off('mouseenter mouseleave')
        .on('mouseenter', function() {
          $(this).text($(this).attr('data-full'));
        })
        .on('mouseleave', function() {
          $(this).text($(this).attr('data-masked'));
        });

      $('#btnCopyModalNik').attr('data-value', (nik && nik !== '-') ? nik : '');

      $('#modal_usia').text(usia + ' Thn');
      $('#modal_keterangan').val(keterangan || '');

      if (kategori === 'Jompo') {
        $('#kat_jompo').prop('checked', true);
      } else {
        $('#kat_lansia').prop('checked', true);
      }

      showModalStatus();
    });

    function showModalStatus() {
      try {
        if (window.BS5 && window.BS5.Modal) {
          var modalEl = document.getElementById('modalStatus');
          var bsModal = window.BS5.Modal.getOrCreateInstance(modalEl);
          bsModal.show();
          return;
        }
      } catch (err) {}
      $('#modalStatus').modal('show');
    }

    function closeModalStatus() {
      if (document.activeElement) {
        document.activeElement.blur();
      }
      try {
        if (window.BS5 && window.BS5.Modal) {
          var modalEl = document.getElementById('modalStatus');
          var bsModal = window.BS5.Modal.getInstance(modalEl);
          if (bsModal) {
            bsModal.hide();
          }
        }
      } catch (err) {}
      try {
        $('#modalStatus').modal('hide');
      } catch (err) {}

      // Sabuk pengaman: bersihkan backdrop jika tersisa
      setTimeout(function() {
        if (!$('#modalStatus').hasClass('show')) {
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open').css('padding-right', '');
        }
      }, 250);
    }

    // Event Klik Tombol Close (X) dan Batal
    $(document).on('click', '#btnCloseStatus, #btnBatalStatus, #modalStatus [data-dismiss="modal"], #modalStatus [data-bs-dismiss="modal"]', function(e) {
      e.preventDefault();
      closeModalStatus();
    });

    $('#modalStatus').on('hidden.bs.modal', function() {
      if (document.activeElement) {
        document.activeElement.blur();
      }
    });

    // Submit Form Status via AJAX
    $('#formStatus').submit(function(e) {
      e.preventDefault();
      var btn = $('#btnSimpanStatus');
      btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

      var postData = $(this).serialize();
      var csrfName = window.CSRF ? window.CSRF.name : 'csrf_test_name';
      var csrfVal = window.CSRF ? window.CSRF.value : $('input[name=csrf_test_name]').val();
      postData += '&' + csrfName + '=' + csrfVal;

      $.ajax({
        url: "<?= site_url('lansia-jompo/save_status'); ?>",
        type: "POST",
        data: postData,
        dataType: "json",
        success: function(res) {
          btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Status');
          if (res.status === 'success') {
            closeModalStatus();
            table.draw(false);
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
              });
            } else {
              alert(res.message);
            }
          } else {
            alert(res.message || 'Terjadi kesalahan saat menyimpan.');
          }
        },
        error: function(xhr, status, error) {
          btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Status');
          alert('Error: ' + error);
        }
      });
    });

    // Tombol Download Excel dengan Parameter Filter Terkini
    $('#btnExportExcel').click(function(e) {
      e.preventDefault();
      var desa = encodeURIComponent($('#filter_desa').val() || defaultDesa);
      var rw = encodeURIComponent($('#filter_rw').val() || '');
      var kategori = encodeURIComponent($('#filter_kategori').val() || '');
      var search = encodeURIComponent(table.search() || '');

      var exportUrl = "<?= site_url('lansia-jompo/export_excel'); ?>?" +
        "desa=" + desa +
        "&rw=" + rw +
        "&kategori=" + kategori +
        "&search=" + search;

      window.location.href = exportUrl;
    });

  });
</script>
<?= $this->endSection(); ?>