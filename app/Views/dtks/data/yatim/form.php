<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Formulir Pendaftaran Anak Yatim</title>

    <!-- CSS only -->
    <link href="<?= base_url('/assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('/assets/css/form.css'); ?>">

    <script type="text/javascript" src="<?= base_url('/assets/jquery/2.1.4/jquery-2.1.4.min.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('/assets/js/bootstrap-filestyle.min.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('/assets/js/conditionize.jquery.js?12345'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('/assets/js/jquery.remember-state-mod.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('/assets/js/autosize.js'); ?>"></script>
    <script type="text/javascript" src="<?= base_url('/assets/js/form.js?12345'); ?>"></script>
</head>

<body>

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-2">Jumbotron Heading Text Goes Here</h1>
        </div>

    </div>
        <div class="container">

            <nav>
                <ul class="pager">
                    <li class="previous"><a href="/"><span aria-hidden="true">&larr;</span> Kembali ke Beranda</a></li>
                </ul>
            </nav>

            <div class="page-header">
                <h1>Formulir Pendaftaran Anak Yatim</h1>
            </div>

        <form id="js-yatim-form" action="" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="well">
                <fieldset>
                    <legend>Biodata Yatim</legend>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Nama Lengkap
                            <span class="req">*</span>
                        </label>

                        <div class="col-sm-9">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="nama_lengkap_anak">Anak</label>
                                <div class="col-sm-10">
                                    <input class="form-control input-sm" type="text" name="nama_lengkap_anak" id="nama_lengkap_anak" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="nama_lengkap_bapak">Bapak</label>
                                <div class="col-sm-10">
                                    <input class="form-control input-sm" type="text" name="nama_lengkap_bapak" id="nama_lengkap_bapak" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="nama_lengkap_kakek">Kakek</label>
                                <div class="col-sm-10">
                                    <input class="form-control input-sm" type="text" name="nama_lengkap_kakek" id="nama_lengkap_kakek" required>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kelamin">Jenis Kelamin <span class="req">*</span></label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label for="kelamin_1"><input type="radio" name="kelamin" id="kelamin_1" value="Laki-laki" required> Laki-laki</label>
                                &nbsp;&nbsp;
                                <label for="kelamin_2"><input type="radio" name="kelamin" id="kelamin_2" value="Perempuan" required> Perempuan</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="ttl">Tempat Tanggal Lahir
                            <span class="req" data-field="ttl">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control input-sm" type="text" name="ttl" id="ttl" required>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="ortu_tgl_wafat">Tanggal Wafat Ortu
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input class="form-control input-sm" type="text" name="ortu_tgl_wafat" id="ortu_tgl_wafat" required>
                        </div>
                        <label class="col-sm-3 control-label" for="ortu_pekerjaan_sblm_wafat">Pekerjaan Sblm Wafat <span class="req">*</span></label>
                        <div class="col-sm-3">
                            <input class="form-control input-sm" type="text" name="ortu_pekerjaan_sblm_wafat" id="ortu_pekerjaan_sblm_wafat" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="ibu_nama">Nama Ibu
                            <span class="req" data-field="ibu_nama">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input class="form-control input-sm" type="text" name="ibu_nama" id="ibu_nama" required>
                        </div>
                        <label class="col-sm-1 control-label" for="ibu_status">Status</label>
                        <div class="col-sm-5">
                            <div class="radio">
                                <label for="janda"><input type="radio" name="ibu_status" id="janda" value="Janda" required> Janda</label>
                                &nbsp;&nbsp;
                                <label for="wafat"><input type="radio" name="ibu_status" id="wafat" value="Wafat" required> Wafat</label>
                                &nbsp;&nbsp;
                                <label for="nikah_lagi"><input type="radio" name="ibu_status" id="nikah_lagi" value="Menikah Lagi" required> Menikah Lagi</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="ibu_ttl">TTL Ibu
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control input-sm" type="text" name="ibu_ttl" id="ibu_ttl" required>
                        </div>
                    </div>

                    <div class="js-conditional" data-cond-option="ibu_status" data-cond-value="Janda" data-cond-value-alt="Menikah Lagi">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="ibu_pekerjaan">Pekerjaan Ibu
                                <span class="req" data-field="ibu_pekerjaan">*</span>
                            </label>
                            <div class="col-sm-3">
                                <input class="form-control input-sm js-conditional" type="text" name="ibu_pekerjaan" id="ibu_pekerjaan" disabled="disabled" data-cond-option="ibu_status" data-cond-value="Janda" data-cond-value-alt="Menikah Lagi" data-cond-action="enable|require">
                            </div>
                            <label class="col-sm-3 control-label" for="ibu_penghasilan">Penghasilan Bulanan <span class="req">*</span></label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp</span>
                                    <input type="number" name="ibu_penghasilan" id="ibu_penghasilan" class="form-control input-sm js-conditional" disabled="disabled" data-cond-option="ibu_status" data-cond-value="Janda" data-cond-value-alt="Menikah Lagi" data-cond-action="enable|require">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="ibu_telp">No. Telp atau HP
                                <span class="req" data-field="ibu_telp">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control input-sm js-conditional" type="text" name="ibu_telp" id="ibu_telp" disabled="disabled" data-cond-option="ibu_status" data-cond-value="Janda" data-cond-value-alt="Menikah Lagi" data-cond-action="enable|require">
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="alamat">Alamat Anak Yatim
                            <span class="req" data-field="alamat">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control input-sm" type="text" name="alamat" id="alamat" required>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="tempat_tinggal">Tempat Tinggal Yatim
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label for="tt_sewa"><input type="radio" name="tempat_tinggal" value="Sewa" id="tt_sewa" required> Sewa</label>
                                &nbsp;
                                <label for="tt_kelaurga"><input type="radio" name="tempat_tinggal" value="Dengan Keluarga" id="tt_kelaurga" required> Dengan Keluarga</label>
                                &nbsp;
                                <label for="tt_asrama"><input type="radio" name="tempat_tinggal" value="Asrama" id="tt_asrama" required> Asrama</label>
                                &nbsp;
                                <label for="tt_dinas"><input type="radio" name="tempat_tinggal" value="Perumahan Dinas" id="tt_dinas" required> Perumahan Dinas</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="keadaan_tempat_tinggal">Keadaan Tempat Tinggal
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label for="keadaan_tt_permanen"><input type="radio" name="keadaan_tempat_tinggal" value="Permanen" id="keadaan_tt_permanen" required> Permanen</label>
                                &nbsp;
                                <label for="keadaan_tt_semi"><input type="radio" name="keadaan_tempat_tinggal" value="Semi Permanen" id="keadaan_tt_semi" required> Semi Permanen</label>
                                &nbsp;
                                <label for="keadaan_tt_kayu"><input type="radio" name="keadaan_tempat_tinggal" value="Kayu" id="keadaan_tt_kayu" required> Kayu</label>
                                &nbsp;
                                <label for="keadaan_tt_tenda"><input type="radio" name="keadaan_tempat_tinggal" value="Tenda" id="keadaan_tt_tenda" required> Tenda</label>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kesehatan">Kesehatan
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label for="kesehatan_sehat"><input type="radio" name="kesehatan" id="kesehatan_sehat" value="Sehat" required> Sehat</label>
                            </div>
                            <div class="form-inline" style="margin-top: -3px">
                                <div class="radio">
                                    <label for="kesehatan_sakit"><input type="radio" name="kesehatan" id="kesehatan_sakit" value="Sakit" style="margin-right:4px" required> Sakit</label>
                                    &nbsp;
                                    <input type="text" name="kesehatan_sakit_teks" id="kesehatan_sakit_teks" class="form-control input-sm js-conditional" disabled="disabled" data-cond-option="kesehatan" data-cond-value="Sakit" data-cond-action="enable|require">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="pendidikan">Pendidikan
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label for="pendidikan_biasa"><input type="radio" name="pendidikan" id="pendidikan_biasa" value="Biasa" required> Biasa</label>
                            </div>
                            <div class="radio">
                                <label for="pendidikan_khusus"><input type="radio" name="pendidikan" id="pendidikan_khusus" value="Khusus" required> Khusus</label>
                            </div>
                            <div class="form-inline" style="margin-top: -3px">
                                <div class="radio">
                                    <label for="pendidikan_tidak_sekolah"><input type="radio" name="pendidikan" id="pendidikan_tidak_sekolah" value="Tidak Sekolah" style="margin-right:4px" required> Tidak Sekolah, sebab</label>
                                    &nbsp;
                                    <input type="text" name="pendidikan_sebab" id="pendidikan_sebab" class="form-control input-sm js-conditional" disabled="disabled" data-cond-option="pendidikan" data-cond-value="Tidak Sekolah" data-cond-action="enable|require">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group js-conditional" data-cond-option="pendidikan" data-cond-value="Biasa" data-cond-value-alt="Khusus">
                        <label class="col-sm-3 control-label" for="sekolah_nama">Nama Sekolah
                            <span class="req" data-field="sekolah_nama">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input class="form-control input-sm js-conditional" type="text" name="sekolah_nama" id="sekolah_nama" disabled="disabled" data-cond-option="pendidikan" data-cond-value="Biasa" data-cond-value-alt="Khusus" data-cond-action="enable|require">
                        </div>
                        <label class="col-sm-1 control-label" for="sekolah_kelas">Kelas</label>
                        <div class="col-sm-2">
                            <input type="text" name="sekolah_kelas" id="sekolah_kelas" class="form-control input-sm">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="keluarga_jumlah">Jumlah Saudara Kandung
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-4">
                            <input class="form-control input-sm" type="text" name="keluarga_jumlah" id="keluarga_jumlah" required>
                        </div>
                        <label class="col-sm-3 control-label" for="keluarga_urutan">Urutan dalam Keluarga <span class="req">*</span></label>
                        <div class="col-sm-2">
                            <input type="text" name="keluarga_urutan" id="keluarga_urutan" class="form-control input-sm" required>
                        </div>
                    </div>



                    <hr>


                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="wali_nama">Nama Wali
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control input-sm" type="text" name="wali_nama" id="wali_nama" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="wali_hubungan">Hubungan dg Yatim
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control input-sm" type="text" name="wali_hubungan" id="wali_hubungan" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="wali_pekerjaan">Pekerjaan Wali
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-4">
                            <input class="form-control input-sm" type="text" name="wali_pekerjaan" id="wali_pekerjaan" required>
                        </div>
                        <label class="col-sm-2 control-label" for="wali_telp">No. Telp / HP
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="text" name="wali_telp" id="wali_telp" class="form-control input-sm" required>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="pj_nama">Nama Penanggung Jawab
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-4">
                            <input class="form-control input-sm" type="text" name="pj_nama" id="pj_nama" required>
                        </div>
                        <label class="col-sm-2 control-label" for="pj_telp">No. Telp / HP <span class="req">*</span></label>
                        <div class="col-sm-3">
                            <input type="text" name="pj_telp" id="pj_telp" class="form-control input-sm" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="lembaga_nama">Nama Instansi / Lembaga
                            <span class="req">*</span>
                        </label>
                        <div class="col-sm-4">
                            <input class="form-control input-sm" type="text" name="lembaga_nama" id="lembaga_nama" required>
                        </div>
                        <label class="col-sm-2 control-label" for="lembaga_telp">No. Telp / HP <span class="req">*</span></label>
                        <div class="col-sm-3">
                            <input type="text" name="lembaga_telp" id="lembaga_telp" class="form-control input-sm" required>
                        </div>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>Nama-nama Keluarga</legend>

                    <script id="js-keluarga-template" type="text/template">
                        <div class="form-group js-keluarga-item" data-num="{num}">
  <label class="col-sm-1 control-label">{num}.</label>
  <label class="col-sm-1 control-label" for="keluarga[{num}][nama]">Nama
  </label>
  <div class="col-sm-4">
    <input class="form-control input-sm" type="text" name="keluarga[{num}][nama]">
  </div>
  <div class="col-sm-2">
    <div class="radio">
      <label for=""><input type="radio" name="keluarga[{num}][kelamin]" value="Laki-laki"> L</label>
      &nbsp; / &nbsp;
      <label for=""><input type="radio" name="keluarga[{num}][kelamin]" value="Perempuan"> P</label>
    </div>
  </div>
  <label class="col-sm-1 control-label" for="keluarga[{num}][ttl]">TTL</label>
  <div class="col-sm-3">
    <input type="text" name="keluarga[{num}][ttl]" class="form-control input-sm">
  </div>
</div>
      </script>


                    <div class="form-group js-keluarga-item" data-num="1">
                        <label class="col-sm-1 control-label">1.</label>
                        <label class="col-sm-1 control-label" for="keluarga[1][nama]">Nama
                        </label>
                        <div class="col-sm-4">
                            <input class="form-control input-sm" type="text" name="keluarga[1][nama]">
                        </div>
                        <div class="col-sm-2">
                            <div class="radio">
                                <label for=""><input type="radio" name="keluarga[1][kelamin]" value="Laki-laki"> L</label>
                                &nbsp; / &nbsp;
                                <label for=""><input type="radio" name="keluarga[1][kelamin]" value="Perempuan"> P</label>
                            </div>
                        </div>
                        <label class="col-sm-1 control-label" for="keluarga[1][ttl]">TTL</label>
                        <div class="col-sm-3">
                            <input type="text" name="keluarga[1][ttl]" class="form-control input-sm">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-push-1 col-sm-2">
                            <button type="button" id="add-keluarga" class="btn btn-default">Tambah Keluarga</button>
                        </div>
                    </div>

                    <script type="text/javascript">
                        var addNewKeluarga = function() {
                            var num = $('.js-keluarga-item').length + 1;
                            var tmpl = $('#js-keluarga-template').html().replace(/{num}/g, num);

                            $(tmpl).insertAfter($('.js-keluarga-item').last());

                            createCookie('keluarga_count', $('.js-keluarga-item').length);

                            $(tmpl).find('input').first().focus();
                        }

                        // restore last total keluarga count
                        if (readCookie('keluarga_count')) {
                            var max = parseInt(readCookie('keluarga_count')) - 1;

                            for (var i = 0; i < max; i++) addNewKeluarga();
                        }

                        $(document).ready(function() {
                            $('#add-keluarga').on('click', function(e) {
                                addNewKeluarga();
                            })
                        });
                    </script>
                </fieldset>

            </div>


            <div class="well">
                <fieldset>
                    <legend>Lampiran</legend>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="lampiran_pas_foto">Pas Foto (4&times;6)
                            <span class="req" data-field="lampiran_pas_foto">*</span>
                        </label>
                        <div class="col-sm-5">
                            <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_pas_foto" id="lampiran_pas_foto" accept="image/*" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="lampiran_pas_foto_full">Pas Foto Badan Penuh
                            <span class="req" data-field="lampiran_pas_foto_full">*</span>
                        </label>
                        <div class="col-sm-5">
                            <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_pas_foto_full" id="lampiran_pas_foto_full" accept="image/*" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="lampiran_akte_lahir">Scan Akte Kelahiran
                            <span class="req" data-field="lampiran_akte_lahir">*</span>
                        </label>
                        <div class="col-sm-5">
                            <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_akte_lahir" id="lampiran_akte_lahir" accept="image/*" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="lampiran_surat_kematian">Scan Surat Kematian
                            <span class="req" data-field="lampiran_surat_kematian">*</span>
                        </label>
                        <div class="col-sm-5">
                            <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_surat_kematian" id="lampiran_surat_kematian" accept="image/*" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="lampiran_kk">Scan Kartu Keluarga
                            <span class="req" data-field="lampiran_kk">*</span>
                        </label>
                        <div class="col-sm-5">
                            <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_kk" id="lampiran_kk" accept="image/*" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="lampiran_ktp">Scan KTP
                            <span class="req" data-field="lampiran_ktp">*</span>
                        </label>
                        <div class="col-sm-5">
                            <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_ktp" id="lampiran_ktp" accept="image/*" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="lampiran_rapor_terakhir">Scan Rapor Terakhir
                            <span class="req" data-field="lampiran_rapor_terakhir">*</span>
                        </label>
                        <div class="col-sm-5">
                            <input class="filestyle" data-icon="false" data-buttonText="Pilih berkas" type="file" name="lampiran_rapor_terakhir" id="lampiran_rapor_terakhir" accept="image/*" required>
                        </div>
                    </div>

                </fieldset>
            </div>

            <div class="well">
                <fieldset>
                    <legend>Syarat dan Ketentuan</legend>
                    <ol>
                        <li>Usia anak di bawah 10 tahun.</li>
                        <li>Formulir diisi dengan sebenarnya.</li>
                        <li>Setiap data yang masuk akan melalui proses seleksi.</li>
                    </ol>
                </fieldset>
            </div>

            <div class="checkbox">
                <label for="persetujuan"><input type="checkbox" name="persetujuan" id="persetujuan" required> Saya setuju dengan syarat dan ketentuan di atas.</label>
            </div>

            <button type="submit" class="btn btn-primary" style="margin: 2em 0">Submit</button>

        </form>

    </div>
</body>

</html>