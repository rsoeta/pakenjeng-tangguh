<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Formulir Pendaftaran Anak Yatim</title>
  <!-- Bootstrap CSS -->
  <!-- CSS only -->
  <link rel="stylesheet" href="<?= base_url(); ?>/assets/css/bootstrap.min.css">
  <!-- Style CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/form'); ?>/style.css">


</head>

<body>

  <div class="container-fluid">
    <img src="<?= base_url('/img/dtks_usulan/Keutamaan-Menyantuni-Anak-Yatim.jpg'); ?>"></img>
  </div>
  <br><br>
  <div class="container well">

    <?= form_open_multipart('', ['class' => 'formsimpandata']) ?>
    <?= csrf_field(); ?>

    <fieldset class="border p-2">
      <legend class="w-auto">Biodata Yatim</legend>
      <label for="" class="col-sm-3 control-label">Nomor Induk Kependudukan
        <span class="req">*</span>
      </label>
      <div class="form-group row">
        <div class="col-sm-9">
          <div class="form-group row">
            <label class="col-3 col-sm-2 col-form-label" for="nik_anak">NIK Anak</label>
            <div class="col-9 col-sm-7">
              <input class="form-control input-sm" type="text" name="nik_anak" id="nik_anak">
            </div>
          </div>

          <label for="" class="col-sm-3 control-label">Nama Lengkap
            <span class="req">*</span>
          </label>
          <div class="form-group row">
            <div class="col-sm-9">
              <div class="form-group row">
                <label class="col-2 col-sm-2 col-form-label" for="nama_lengkap_anak">Anak</label>
                <div class="col-10 col-sm-10">
                  <input class="form-control input-sm" type="text" name="nama_lengkap_anak" id="nama_lengkap_anak">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-2 col-sm-2 col-form-label" for="nama_lengkap_bapak">Bapak</label>
                <div class="col-10 col-sm-10">
                  <input class="form-control input-sm" type="text" name="nama_lengkap_bapak" id="nama_lengkap_bapak">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-2 col-sm-2 col-form-label" for="nama_lengkap_kakek">Kakek</label>
                <div class="col-10 col-sm-10">
                  <input class="form-control input-sm" type="text" name="nama_lengkap_kakek" id="nama_lengkap_kakek">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-2 control-label" for="kelamin">Jenis Kelamin <span class="req">*</span></label>
                <div class="col-sm-9">
                  <div class="radio">
                    <label for="kelamin_1"><input type="radio" name="kelamin" id="kelamin_1" value="Laki-laki"> Laki-laki</label>
                    &nbsp;&nbsp;
                    <label for="kelamin_2"><input type="radio" name="kelamin" id="kelamin_2" value="Perempuan"> Perempuan</label>
                  </div>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-4 col-sm-2 control-label" for="tmp_lahir">Tempat Lahir
                  <span class="req" data-field="tmp_lahir">*</span>
                </label>
                <div class="col-8 col-sm-10">
                  <input class="form-control input-sm" type="text" name="tmp_lahir" id="tmp_lahir">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-4 col-sm-2 control-label" for="tgl_lahir">Tanggal Lahir
                  <span class="req" data-field="tgl_lahir">*</span>
                </label>
                <div class="col-8 col-sm-10">
                  <input class="form-control input-sm" type="date" name="tgl_lahir" id="tgl_lahir">
                </div>
              </div>
            </div>
          </div>

          <hr>

          <div class="form-group row">
            <label class="col-5 col-sm-2 control-label" for="ortu_tgl_wafat">Tanggal Wafat Ortu
              <span class="req">*</span>
            </label>
            <div class="col-7 col-sm-7">
              <input class="form-control input-sm" type="date" name="ortu_tgl_wafat" id="ortu_tgl_wafat">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-5 col-sm-2 control-label" for="ortu_pekerjaan_sblm_wafat">Pekerjaan Sblm Wafat <span class="req">*</span></label>
            <div class="col-7 col-sm-7">
              <input class="form-control input-sm" type="text" name="ortu_pekerjaan_sblm_wafat" id="ortu_pekerjaan_sblm_wafat">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-4 col-sm-2 control-label" for="ibu_nama">Nama Ibu
              <span class="req" data-field="ibu_nama">*</span>
            </label>
            <div class="col-8 col-sm-3">
              <input class="form-control input-sm" type="text" name="ibu_nama" id="ibu_nama">
            </div>
            <label class="col-sm-1 control-label" for="ibu_status">Status</label>
            <div class="col-sm-5">
              <div class="radio">
                <label for="janda"><input type="radio" name="ibu_status" id="janda" value="Janda"> Janda</label>
                &nbsp;&nbsp;
                <label for="wafat"><input type="radio" name="ibu_status" id="wafat" value="Wafat"> Wafat</label>
                &nbsp;&nbsp;
                <label for="nikah_lagi"><input type="radio" name="ibu_status" id="nikah_lagi" value="Menikah Lagi"> Menikah Lagi</label>
                <div class="js-conditional" data-cond-option="ibu_status" data-cond-value="Janda" data-cond-value-alt="Menikah Lagi"></div>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-4 col-sm-2 control-label" for="ibu_ttl">Tgl Lahir Ibu
              <span class="req">*</span>
            </label>
            <div class="col-8 col-sm-7">
              <input class="form-control input-sm" type="date" name="ibu_ttl" id="ibu_ttl">
            </div>
          </div>


          <div class="form-group row">
            <label class="col-4 col-sm-2 control-label" for="ibu_pekerjaan">Pekerjaan Ibu
              <span class="req" data-field="ibu_pekerjaan">*</span>
            </label>
            <div class="col-8 col-sm-7">
              <input class="form-control input-sm js-conditional" type="text" name="ibu_pekerjaan" id="ibu_pekerjaan" data-cond-option="ibu_status" data-cond-value="Janda" data-cond-value-alt="Menikah Lagi" data-cond-action="enable|require">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-4 col-sm-2 control-label" for="ibu_penghasilan">Penghasilan Bulanan <span class="req">*</span></label>
            <div class="col-8 col-sm-7">
              <div class="input-group">
                <span class="input-group-addon">Rp</span>
                <input type="number" name="ibu_penghasilan" id="ibu_penghasilan" class="form-control input-sm js-conditional" data-cond-option="ibu_status" data-cond-value="Janda" data-cond-value-alt="Menikah Lagi" data-cond-action="enable|require">
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-4 col-sm-2 control-label" for="ibu_telp">No. Telp atau HP
              <span class="req" data-field="ibu_telp">*</span>
            </label>
            <div class="col-8 col-sm-7">
              <input class="form-control input-sm js-conditional" type="text" name="ibu_telp" id="ibu_telp" data-cond-option="ibu_status" data-cond-value="Janda" data-cond-value-alt="Menikah Lagi" data-cond-action="enable|require">
            </div>
          </div>

          <hr>

          <div class="form-group row">
            <label class="col-sm-2 control-label" for="alamat">Alamat Anak Yatim
              <span class="req" data-field="alamat">*</span>
            </label>
            <div class="col-sm-7">
              <input class="form-control input-sm" type="text" name="alamat" id="alamat">
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-2 control-label" for="tempat_tinggal">Tempat Tinggal Yatim
              <span class="req">*</span>
            </label>
            <div class="col-sm-9">
              <div class="radio">
                <label for="tt_sewa"><input type="radio" name="tempat_tinggal" value="Sewa" id="tt_sewa"> Sewa</label>
                &nbsp;
                <label for="tt_kelaurga"><input type="radio" name="tempat_tinggal" value="Dengan Keluarga" id="tt_kelaurga"> Dengan Keluarga</label>
                &nbsp;
                <label for="tt_asrama"><input type="radio" name="tempat_tinggal" value="Asrama" id="tt_asrama"> Asrama</label>
                &nbsp;
                <label for="tt_dinas"><input type="radio" name="tempat_tinggal" value="Perumahan Dinas" id="tt_dinas"> Perumahan Dinas</label>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-2 control-label" for="keadaan_tempat_tinggal">Keadaan Tempat Tinggal
              <span class="req">*</span>
            </label>
            <div class="col-sm-9">
              <div class="radio">
                <label for="keadaan_tt_permanen"><input type="radio" name="keadaan_tempat_tinggal" value="Permanen" id="keadaan_tt_permanen"> Permanen</label>
                &nbsp;
                <label for="keadaan_tt_semi"><input type="radio" name="keadaan_tempat_tinggal" value="Semi Permanen" id="keadaan_tt_semi"> Semi Permanen</label>
                &nbsp;
                <label for="keadaan_tt_kayu"><input type="radio" name="keadaan_tempat_tinggal" value="Kayu" id="keadaan_tt_kayu"> Kayu</label>
                &nbsp;
                <label for="keadaan_tt_tenda"><input type="radio" name="keadaan_tempat_tinggal" value="Tenda" id="keadaan_tt_tenda"> Tenda</label>
              </div>
            </div>
          </div>


          <div class="form-group row">
            <label class="col-sm-2 control-label" for="kesehatan">Kesehatan
              <span class="req">*</span>
            </label>
            <div class="col-9 col-sm-9">
              <div class="radio">
                <label for="kesehatan_sehat"><input type="radio" name="kesehatan" id="kesehatan_sehat" value="Sehat"> Sehat</label>
                &nbsp;
                <label for="kesehatan_sakit"><input type="radio" name="kesehatan" id="kesehatan_sakit" value="Sakit" style="margin-right:4px"> Sakit</label>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-2 control-label" for="pendidikan">Pendidikan
              <span class="req">*</span>
            </label>
            <div class="col-sm-9">
              <div class="radio">
                <label for="pendidikan_biasa"><input type="radio" name="pendidikan" id="pendidikan_biasa" value="Biasa"> Biasa</label>
              </div>
              <div class="radio">
                <label for="pendidikan_khusus"><input type="radio" name="pendidikan" id="pendidikan_khusus" value="Khusus"> Khusus</label>
              </div>
              <div class="radio">
                <label for="pendidikan_tidak_sekolah"><input type="radio" name="pendidikan" id="pendidikan_tidak_sekolah" value="Tidak Sekolah" style="margin-right:4px"> Tidak Sekolah, sebab</label>
                &nbsp;
                <input type="text" name="pendidikan_sebab" id="pendidikan_sebab" class="form-control input-sm js-conditional" data-cond-option="pendidikan" data-cond-value="Tidak Sekolah" data-cond-action="enable|require">
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-7 col-sm-2 control-label" for="keluarga_jumlah">Jumlah Saudara Kandung
              <span class="req">*</span>
            </label>
            <div class="col-5 col-sm-2">
              <input class="form-control input-sm" type="number" name="keluarga_jumlah" id="keluarga_jumlah">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-7 col-sm-2 control-label" for="keluarga_urutan">Urutan dalam Keluarga <span class="req">*</span></label>
            <div class="col-5 col-sm-2">
              <input type="number" name="keluarga_urutan" id="keluarga_urutan" class="form-control input-sm">
            </div>
          </div>

          <hr>


          <div class="form-group row">
            <label class="col-4 col-sm-2 control-label" for="wali_nama">Nama Wali
              <span class="req">*</span>
            </label>
            <div class="col-8 col-sm-9">
              <input class="form-control input-sm" type="text" name="wali_nama" id="wali_nama">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-4 col-sm-2 control-label" for="wali_hubungan">Hubungan dg Yatim
              <span class="req">*</span>
            </label>
            <div class="col-8 col-sm-9">
              <input class="form-control input-sm" type="text" name="wali_hubungan" id="wali_hubungan">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-4 col-sm-2 control-label" for="wali_pekerjaan">Pekerjaan Wali
              <span class="req">*</span>
            </label>
            <div class="col-8 col-sm-4">
              <input class="form-control input-sm" type="text" name="wali_pekerjaan" id="wali_pekerjaan">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-4 col-sm-2 control-label" for="wali_telp">No. Telp / HP
              <span class="req">*</span>
            </label>
            <div class="col-8 col-sm-3">
              <input type="text" name="wali_telp" id="wali_telp" class="form-control input-sm">
            </div>
          </div>

          <hr>

          <div class="form-group row">
            <label class="col-sm-2 control-label" for="pj_nama">Nama Penanggung Jawab
              <span class="req">*</span>
            </label>
            <div class="col-sm-9">
              <input class="form-control input-sm" type="text" name="pj_nama" id="pj_nama">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-5 col-sm-2 control-label" for="pj_telp">No. Telp / HP <span class="req">*</span></label>
            <div class="col-7 col-sm-3">
              <input type="text" name="pj_telp" id="pj_telp" class="form-control input-sm">
            </div>
          </div>

    </fieldset>
  </div>

  <div class="container ">
    <fieldset class="border p-2">
      <legend class="w-auto">Nama-nama Keluarga</legend>

      <div class="form-group row" data-num="{num}">
        <label class="col-4 col-sm-2 control-label" for="kel_satu_nama">Nama</label>
        <div class="col-8 col-sm-7">
          <input class="form-control input-sm" type="text" name="kel_satu_nama">
        </div>
      </div>
      <div class="form-group row" data-num="{num}">
        <label class="col-4 col-sm-2 control-label" for="kel_satu_kelamin">Jenis Kelamin</label>
        <div class="col-8 radio">
          <label for=""><input type="radio" name="kel_satu_kelamin" value="Laki-laki"> L</label>
          &nbsp; / &nbsp;
          <label for=""><input type="radio" name="kel_satu_kelamin" value="Perempuan"> P</label>
        </div>

      </div>
      <div class="form-group row">
        <label class="col-4 col-sm-2 control-label" for="kel_satu_tmplahir">Tempat Lahir</label>
        <div class="col-8 col-sm-3">
          <input type="text" name="kel_satu_tmplahir" class="form-control input-sm">
        </div>
      </div>
      <div class="form-group row">
        <label class="col-4 col-sm-2 control-label" for="kel_satu_tgllahir">Tanggal Lahir</label>
        <div class="col-8 col-sm-3">
          <input type="date" name="kel_satu_tgllahir" class="form-control input-sm">
        </div>
      </div>

      <hr>

      <div class="form-group row" data-num="{num}">
        <label class="col-4 col-sm-2 control-label" for="kel_dua_nama">Nama</label>
        <div class="col-8 col-sm-7">
          <input class="form-control input-sm" type="text" name="kel_dua_nama">
        </div>
      </div>
      <div class="form-group row" data-num="{num}">
        <label class="col-4 col-sm-2 control-label" for="kel_dua_kelamin">Jenis Kelamin</label>
        <div class="col-8 radio">
          <label for=""><input type="radio" name="kel_dua_kelamin" value="Laki-laki"> L</label>
          &nbsp; / &nbsp;
          <label for=""><input type="radio" name="kel_dua_kelamin" value="Perempuan"> P</label>
        </div>

      </div>
      <div class="form-group row">
        <label class="col-4 col-sm-2 control-label" for="kel_dua_tmplahir">Tempat Lahir</label>
        <div class="col-8 col-sm-3">
          <input type="text" name="kel_dua_tmplahir" class="form-control input-sm">
        </div>
      </div>
      <div class="form-group row">
        <label class="col-4 col-sm-2 control-label" for="kel_dua_tgllahir">Tanggal Lahir</label>
        <div class="col-8 col-sm-3">
          <input type="date" name="kel_dua_tgllahir" class="form-control input-sm">
        </div>
      </div>
    </fieldset>

  </div>

  <div class="container ">
    <fieldset class="border p-2">
      <legend class="w-auto">Lampiran</legend>

      <div class="form-group">
        <label class="col-sm-3 control-label" for="lampiran_pas_foto">Pas Foto (4&times;6)
          <span class="req" data-field="lampiran_pas_foto">*</span>
        </label>
        <div class="col-sm-5">
          <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_pas_foto" id="lampiran_pas_foto" accept="image/*">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label" for="lampiran_pas_foto_full">Pas Foto Badan Penuh
          <span class="req" data-field="lampiran_pas_foto_full">*</span>
        </label>
        <div class="col-sm-5">
          <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_pas_foto_full" id="lampiran_pas_foto_full" accept="image/*">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label" for="lampiran_akte_lahir">Scan Akte Kelahiran
          <span class="req" data-field="lampiran_akte_lahir">*</span>
        </label>
        <div class="col-sm-5">
          <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_akte_lahir" id="lampiran_akte_lahir" accept="image/*">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label" for="lampiran_kk">Scan Kartu Keluarga
          <span class="req" data-field="lampiran_kk">*</span>
        </label>
        <div class="col-sm-5">
          <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_kk" id="lampiran_kk" accept="image/*">
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label" for="lampiran_rapor_terakhir">Scan Rapor Terakhir
          <span class="req" data-field="lampiran_rapor_terakhir">*</span>
        </label>
        <div class="col-sm-5">
          <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_rapor_terakhir" id="lampiran_rapor_terakhir" accept="image/*">
        </div>
      </div>

    </fieldset>
  </div>

  <div class="container ">
    <fieldset class="border p-2">
      <legend class="w-auto">Syarat dan Ketentuan</legend>
      <ol>
        <li>Usia anak di bawah 15 tahun.</li>
        <li>Formulir diisi dengan sebenarnya.</li>
        <li>Setiap data yang masuk akan melalui proses seleksi.</li>
      </ol>
    </fieldset>
  </div>

  <div class="container mt-3">
    <div class="checkbox">
      <label for="persetujuan"><input type="checkbox" name="persetujuan" id="persetujuan"> Saya setuju dengan syarat dan ketentuan di atas.</label>

    </div>
    <button type="submit" class="btn btn-primary float-right mt-3 tombolSimpan">Simpan</button>
  </div>
  <?= form_close(); ?>
  <!-- partial:index.partial.html -->
  <br><br><br>

  <div class="container">
    <!-- /.content -->
    <footer class="main-footer">
      <strong>Copyright &copy; 2021 - <?= date('Y'); ?> <a href="/dtks" target="">Opr NewDTKS</a>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 2.1.0
      </div>
    </footer>
    <br>
  </div>
  <!-- jQuery -->
  <!-- <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script> -->

  <script src="<?= base_url(); ?>/assets/plugins/autoNumeric/autoNumeric.js"></script>

  <script>

    $(document).ready(function() {

      $('.tombolSimpan').click(function(e) {
        e.preventDefault();

        let form = $('.formsimpandata')[0];
        let data = new FormData(form);

        $.ajax({
          type: "post",
          url: "<?= site_url('dtks/yatim/simpandata'); ?>",
          data: data,
          dataType: "json",
          enctype: 'multipart/form-data',
          processData: false,
          contentType: false,
          cache: false,
          beforeSend: function() {
            $('.tombolSimpan').html('<i class="fa fa-spin fa-spinner"></i>');
            $('.tombolSimpan').prop('disabled', true);
          },
          complete: function() {
            $('.tombolSimpan').html('Simpan');
            $('.tombolSimpan').prop('disabled', false);
          },
          success: function(response) {

          },
          error: function(xhr, thrownError) {
            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
          }
        });
      });

      $('#keluarga_jumlah').autoNumeric('init', {
        aSep: ',',
        aDec: '.',
        mDec: '2'
      });

    });
  </script>


</body>


</html>