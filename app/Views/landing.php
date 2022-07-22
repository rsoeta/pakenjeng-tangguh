<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="author" content="Rian Sutarsa">
  <meta name="keywords" content="Aplikasi Opr. NewDTKS Kecamatan Pakenjeng Kabupaten Garut Provinsi Jawa Barat">
  <meta name="description" content="Aplikasi Opr. NewDTKS Kecamatan Pakenjeng Kabupaten Garut Provinsi Jawa Barat">

  <meta property="og:title" content="Opr NewDTKS" />
  <meta property="og:description" content="Aplikasi Opr. NewDTKS Kecamatan Pakenjeng Kabupaten Garut Provinsi Jawa Barat" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?= base_url(); ?>" />
  <meta property="og:image" content="<?= base_url('icon-dtks.png'); ?>" />

  <meta name="twitter:card" content="summary" />
  <meta name="twitter:site" content="@sutarsarian" />
  <meta name="twitter:creator" content="@sutarsarian" />
  <meta name="twitter:title" content="Opr NewDTKS" />
  <meta name="twitter:description" content="Aplikasi Opr. NewDTKS Kecamatan Pakenjeng Kabupaten Garut Provinsi Jawa Barat" />
  <meta name="twitter:image" content="<?= base_url('icon-dtks.png'); ?>" />

  <title><?= nameApp() . ' Kec. ' . ucwords(strtolower(Profil_Admin()['namaKec'])); ?></title>

  <!-- CSS FILES -->
  <link rel="preconnect" href="https://fonts.googleapis.com">

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

  <link href="<?= base_url('landing-page/css/bootstrap.min.css'); ?>" rel="stylesheet">

  <link href="<?= base_url('landing-page/css/bootstrap-icons.css'); ?>" rel="stylesheet">

  <link href="<?= base_url('landing-page/css/templatemo-leadership-event.css'); ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('landing-page/css/style-landing.css'); ?>">

  <!-- select2 -->
  <link href="<?= base_url('assets/plugins/select2/css/select2.min.css'); ?>" rel="stylesheet" />



  <!-- <link rel="shortcut icon" type="image/x-icon/png" href="<?= base_url('landing-page/images/logo-garut.png'); ?>"> -->
  <link rel="shortcut icon" type="image/x-icon/png" href="<?= base_url('icon-dtks.png'); ?>" />
  <link rel="stylesheet" href="<?= base_url(); ?>/assets/plugins/sweetalert2/sweetalert2.min.css">
  <script src="<?= base_url(); ?>/assets/plugins/sweetalert2/sweetalert2.all.min.js"></script>


  <!--

TemplateMo 575 Leadership Event

https://templatemo.com/tm-575-leadership-event

-->
</head>

<body>

  <nav class="navbar navbar-expand-lg">
    <div class="container">

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <a href="<?= base_url(); ?>" class="navbar-brand mx-auto mx-lg-0">
        <!-- <i class="bi-bullseye brand-logo"></i> -->
        <img src="<?= base_url('icon-dtks.png'); ?>" alt="Logo" class="img-circle" style="width: 50px; height: 50px;">
        <!-- <span class="brand-text" style="height:fit-content;"><?= nameApp(); ?></span> -->
      </a>


      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link click-scroll" href="#section_1">Home</a>
          </li>

          <li class="nav-item">
            <a class="nav-link click-scroll" href="#section_2">Cek Usulan</a>
          </li>

          <!-- <li class="nav-item">
            <a class="nav-link click-scroll" href="#section_3">SKPD</a>
          </li> -->

          <li class="nav-item">
            <a class="nav-link click-scroll" href="#section_4">Galeri Kegiatan</a>
          </li>

          <li class="nav-item">
            <a class="nav-link click-scroll" href="#section_5">Tentang Kami</a>
          </li>

          <li class="nav-item">
            <a class="nav-link click-scroll" href="#section_6">Peta Lokasi</a>
          </li>

          <li class="nav-item">
            <a class="nav-link click-scroll" href="#section_7">Contact</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="/login">Sign In</a>
          </li>

        </ul>
        <div>

        </div>
  </nav>

  <main>

    <section class="hero" id="section_1">
      <div class="container">
        <div class="row">

          <div class="col-lg-5 col-12 m-auto">
            <div class="hero-text">

              <h1 class="text-white"><?= nameApp(); ?></h1>
              <h2 class="text-white mb-4">KECAMATAN <?= Profil_Admin()['namaKec']; ?></h2>

              <div class="d-flex justify-content-center align-items-center">
                <span class="date-text"><?= hari_ini() . ', ' . date('d') . ' ' . bulan_ini() . ' ' . date('Y'); ?></span>

                <span class="location-text"><?= ucwords(strtolower(Profil_Admin()['namaKec'])); ?>, Garut</span>
              </div>

              <a href="#section_2" class="custom-link bi-arrow-down arrow-icon"></a>
            </div>
          </div>
        </div>
      </div>

      <div class="video-wrap">
        <video autoplay="true" loop="true" muted="true" class="custom-video" poster="">
          <source src="<?= base_url('landing-page/videos/20220628_092449.mp4'); ?>" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>
    </section>

    <section class="about section-padding" id="section_2">
      <div class="container">
        <div class="row">

          <div class="col-lg-6 col-12">
            <h3 class="mb-2">Sejarah </h3>
            <p>Awal Maret tahun 2020 pada saat merebaknya Covid-19 di Negara tercinta kita ini, dan mendapat lonjakan pada bulan Mei 2020 sebagaimana dikutip dari CNN Indonesia “Lonjakan Drastis Kasus Corona pada Mei 2020”.
            </p>

            <!-- <a class="custom-btn custom-border-btn btn custom-link mt-3 me-3" href="#section_3">Meet Speakers</a> -->

            <a class="custom-btn btn custom-link mt-3" href="">Read more...</a>
          </div>

          <div class="col-lg-2 col-0"></div>

          <div class="col-lg-4 col-12 mt-5 mt-lg-0">
            <h3 class="mb-2">Cek Status Usulan Anda dibawah ini</h3>

            <div class="card">

              <!-- form-group -->
              <div class="card card-info">
                <!-- /.card-header -->
                <div class="card-header">
                  <div class="card-title">
                    <h4 class="text-center">Form. Cek Status Usulan</h4>
                  </div>
                </div>
                <!-- form start -->
                <form class="form-horizontal">
                  <div class="card-body">
                    <div class="form-group row">
                      <label for="cek_desa" class="col-3 col-sm-3 col-form-label">Desa</label>
                      <div class="col-9 col-sm-9">
                        <select name="cek_desa" id="cek_desa" class="form-control select2">
                          <option value="">--Pilih Desa--</option>
                          <?php foreach ($getDesa as $row) { ?>
                            <option value="<?= $row['id']; ?>"><?= $row['name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <label for="cek_nik" class="col-3 col-sm-3 col-form-label">NIK</label>
                      <div class="col-9 col-sm-9">
                        <input type="number" class="form-control form-control-sm" name="cek_nik" id="cek_nik" placeholder="Masukan NIK Anda">
                      </div>
                      <!-- /.card-body -->
                      <div class="card-footer d-grid gap-2 col-12 mx-auto">
                        <button type="submit" class="btn btn-info btn-block" id="btnCek">Periksa</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>

      </div>
      </div>
    </section>

    <!-- 
    <section class="speakers section-padding" id="section_3">
      <div class="container">
        <div class="row">

          <div class="col-lg-6 col-12 d-flex flex-column justify-content-center align-items-center">
            <div class="speakers-text-info">
              <h2 class="mb-4">SKPD</h2>

              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut dolore</p>
            </div>
          </div>

          <div class="col-lg-6 col-12">
            <div class="speakers-thumb">
              <img src="<?= base_url('data/profil'); ?>/<?= Profil_Admin()['user_image']; ?>" class="img-fluid speakers-image" alt="">

              <small class="speakers-featured-text">Featured</small>

              <div class="speakers-info">

                <h5 class="speakers-title mb-0"><?= ucwords(strtolower(Profil_Admin()['fullname'])); ?></h5>

                <p class="speakers-text mb-0">Tikor</p>

                <ul class="social-icon">
                  <li><a href="#" class="social-icon-link bi-facebook"></a></li>

                  <li><a href="#" class="social-icon-link bi-instagram"></a></li>

                  <li><a href="#" class="social-icon-link bi-google"></a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-12 col-12">
            <div class="row">
              <div class="col-lg-3 col-md-6 col-12">
                <div class="speakers-thumb speakers-thumb-small">
                  <img src="<?= base_url('landing-page/images/avatar/portrait-good-looking-brunette-young-asian-woman.jpg'); ?>" class="img-fluid speakers-image" alt="">

                  <div class="speakers-info">
                    <p class="speakers-title mb-0">Benni Yandiana, S.Sos,.M.Si</p>

                    <p class="speakers-text mb-0">Sekretaris Kecamatan</p>

                    <ul class="social-icon">
                      <li><a href="#" class="social-icon-link bi-facebook"></a></li>

                      <li><a href="#" class="social-icon-link bi-instagram"></a></li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="col-lg-3 col-md-6 col-12">
                <div class="speakers-thumb speakers-thumb-small">
                  <img src="<?= base_url('landing-page'); ?>/images/avatar/senior-man-white-sweater-eyeglasses.jpg" class="img-fluid speakers-image" alt="">

                  <div class="speakers-info">
                    <h5 class="speakers-title mb-0">Indra Ginanjar</h5>

                    <p class="speakers-text mb-0">Kasubag Keuangan</p>

                    <ul class="social-icon">
                      <li><a href="#" class="social-icon-link bi-instagram"></a></li>

                      <li><a href="#" class="social-icon-link bi-whatsapp"></a></li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="col-lg-3 col-md-6 col-12">
                <div class="speakers-thumb speakers-thumb-small">
                  <img src="" class="img-fluid speakers-image" alt="">

                  <div class="speakers-info">
                    <h5 class="speakers-title mb-0">--</h5>

                    <p class="speakers-text mb-0">--</p>

                    <ul class="social-icon">
                      <li><a href="#" class="social-icon-link bi-facebook"></a></li>

                      <li><a href="#" class="social-icon-link bi-instagram"></a></li>

                      <li><a href="#" class="social-icon-link bi-whatsapp"></a></li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="col-lg-3 col-md-6 col-12">
                <div class="speakers-thumb speakers-thumb-small">
                  <img src="" class="img-fluid speakers-image" alt="">

                  <div class="speakers-info">
                    <h5 class="speakers-title mb-0">--</h5>

                    <p class="speakers-text mb-0">--</p>

                    <ul class="social-icon">
                      <li><a href="#" class="social-icon-link bi-instagram"></a></li>

                      <li><a href="#" class="social-icon-link bi-whatsapp"></a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section> -->


    <section class="schedule section-padding" id="section_4">
      <div class="container">
        <div class="row">

          <div class="col-lg-12 col-12">
            <h2 class="mb-5 text-center">Galeri Kegiatan</h2>


            <div class="tab-content mt-5" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-DayOne" role="tabpanel" aria-labelledby="nav-DayOne-tab">

                <div class="row border-bottom pb-5 mb-5">
                  <div class="col-lg-4 col-12">
                    <img src="<?= base_url('landing-page/images/schedule/20220531_081715.jpg'); ?>" class="schedule-image img-fluid" alt="">
                  </div>

                  <div class="col-lg-8 col-12 mt-3 mt-lg-0">
                    <h4 class="mb-2">BIMTEK Peningkatan Kapasitas Petugas DTKS</h4>
                    <p>
                      Menghadiri acara BIMTEK Peningkatan Kapasitas Petugas DTKS yang diadakan di Hotel Harmoni Garut<br>
                      Dengan dihadiri oleh : <br>
                      1. Kapusdatin Kesos, <br>
                      2. Bupati Garut, <br>
                      3. Kadinsos Kab. Garut, <br>
                      4. Kepala Bappeda Garut, <br>
                      5. Dukcapil Garut
                    </p>

                    <div class="d-flex align-items-center mt-4">
                      <div class="avatar-group d-flex">
                        <img src="<?= base_url('landing-page/images/avatar/portrait-good-looking-brunette-young-asian-woman.jpg'); ?>" class="img-fluid avatar-image" alt="">

                        <div class="ms-3">
                          <?= ucwords(strtolower(Profil_Admin()['fullname'])); ?>
                          <p class="speakers-text mb-0">Tikor Kecamatan</p>
                        </div>
                      </div>

                      <span class="mx-3 mx-lg-5">
                        <i class="bi-clock me-2"></i>
                        08:00 AM - 15:00 PM
                      </span>

                      <span class="mx-1 mx-lg-5">
                        <i class="bi-layout-sidebar me-2"></i>
                        Hotel Harmoni Garut
                      </span>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-4 col-12">
                    <img src="<?= base_url('landing-page/images/schedule/IMG-20220609-WA0014.jpg'); ?>" class="schedule-image img-fluid" alt="">
                  </div>

                  <div class="col-lg-8 col-12 mt-3 mt-lg-0">
                    <h4 class="mb-2">Meeting bersama Opr DTKS Tk. Desa</h4>

                    <p>
                      Menghadiri acara Meeting bersama Opr DTKS Tk. Desa yang diadakan di Aula Desa Pasirlangu<br>
                      Dengan dihadiri oleh : <br>
                      1. TKSK Kecamatan Pakenjeng,<br>
                      2. Seluruh Operator DTKS Tk. Desa<br>
                    </p>

                    <div class="d-flex align-items-center mt-4">
                      <div class="avatar-group d-flex">
                        <img src="<?= base_url('landing-page'); ?>/images/avatar/senior-man-white-sweater-eyeglasses.jpg" class="img-fluid avatar-image" alt="">

                        <div class="ms-3">
                          <?= ucwords(strtolower(Profil_Admin()['fullname'])); ?>
                          <p class="speakers-text mb-0">Tikor Kecamatan</p>
                        </div>
                      </div>

                      <span class="mx-3 mx-lg-5">
                        <i class="bi-clock me-2"></i>
                        08:00 AM - 12:30 PM
                      </span>

                      <span class="mx-1 mx-lg-5">
                        <i class="bi-layout-sidebar me-2"></i>
                        Aula Desa Pasirlangu
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <section class="venue section-padding" id="section_5">
      <div class="container">
        <div class="row">

          <div class="col-lg-12 col-12">
            <h2 class="mb-5 text-center">Tentang Kami</h2>


            <div class="tab-content mt-5" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-DayOne" role="tabpanel" aria-labelledby="nav-DayOne-tab">
                <div class="row border-bottom pb-5 mb-5">
                  <div class="col-lg-4 col-12">
                    <img src="<?= base_url('icon-dtks.png'); ?>" class="schedule-image img-fluid" alt="">
                  </div>

                  <div class="col-lg-8 col-12 mt-3 mt-lg-0">

                    <h4 class="mb-2">Sejarah</h4>

                    <p>Awal Maret tahun 2020 pada saat merebaknya Covid-19 di Negara tercinta kita ini, dan mendapat lonjakan pada bulan Mei 2020 sebagaimana dikutip dari CNN Indonesia “Lonjakan Drastis Kasus Corona pada Mei 2020”.

                    <p>Baca artikel CNN Indonesia "Lonjakan Drastis Kasus Corona pada Mei 2020" <a href="https://www.cnnindonesia.com/nasional/20200601103545-20-508637/lonjakan-drastis-kasus-corona-pada-mei-2020">selengkapnya di sini</a>.<br>
                      CNN Indonesia Jakarta, CNN Indonesia -- Kasus positif virus corona (Covid-19) di Indonesia masih terus meningkat. Hingga kemarin, Minggu (31/5), jumlah kumulatif kasus positif virus corona mencapai 26.473 orang.
                      Dari jumlah tersebut, 7.308 orang dinyatakan sembuh dan 1.613 orang lainnya meninggal dunia.</p>

                    <p>Hingga <a href="https://www.ombudsman.go.id/artikel/r/artikel--kebijakan-bekerja-dari-rumah-dan-pelayanan-publik">“Kebijakan Bekerja Dari Rumah dan Pelayanan Publik”</a> seperti dikutip dari Ombudsman Republik Indonesia - <a href="https://www.ombudsman.go.id/artikel/r/artikel--kebijakan-bekerja-dari-rumah-dan-pelayanan-publik">https://www.ombudsman.go.id/</a>
                      Sejak adanya pernyataan resmi dari World Health Organization (WHO) bahwa Corona Virus Disease (Covid-19) atau Virus Corona sebagai pandemi global dan pengumuman resmi yang disampaikan oleh Presiden Joko Widodo bersama Menteri Kesehatan, Terawan Agus Putranto pada Senin tanggal 2 Maret 2020 bahwa Covid-19 sudah masuk ke Indonesia, sehingga siap atau tidak Indonesia harus menghadapi, mencegah, dan melawan penyebaran Covid-19 tersebut. Untuk itu Pemerintah telah melakukan berbagai upaya dan kebijakan, salah satunya adalah bekerja dari rumah atau Work From Home (WFH) bagi Aparatur Sipil Negara (ASN), yaitu melaksanakan tugas kedinasan di rumah/tempat tinggalnya masing-masing untuk mencegah dan meminimalisir penyebaran virus corona di masyarakat.</p>

                    <p>Inilah awal mula sejarah lahirnya Aplikasi Opr NewDTKS dilandasi dari Pelayanan Publik yang terbatas dengan adanya wabah / virus corona menggerakan hati kami untuk tetap memaksimalkan Pelayanan Publik sebagai bukti melaksanakan Pasal 34 ayat (1) Undang-Undang Dasar Negara Republik Indonesia Tahun 1945 mengamanatkan kewajiban negara untuk memelihara fakir miskin dan anak terlantar.
                      Pula diperkuat dengan Dasar hukum pelaksanaan Verifikasi dan Validasi Data Terpadu Kesejahteraan Sosial (DTKS) yaitu sesuai dengan Undang-Undang Nomor 13 Tahun 2011 tentang Penanganan Fakir Miskin, Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintahan Daerah, Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik, Undang-Undang Nomor 11 Tahun 2008 tentang Informasi dan Transaksi Elektronik, Peraturan Pemerintah Nomor 82 Tahun 2012 tentang Sistem dan Transaksi Elektronik, serta Permensos Nomor 5 Tahun 2019 dan Permensos Nomor 28 Tahun 2017.
                      Aplikasi ini sampai saat ini dapat eksis sebagai jembatan penghubung dengan Aplikasi SIKS-NG Online milik Kementrian Sosial Republik Indonesia.
                    </p>

                    <div class="d-flex align-items-center mt-4">
                      <div class="avatar-group d-flex">
                        <img src="<?= base_url('/data/profil') . '/' . Profil_Admin()['user_image']; ?>" class="img-fluid avatar-image" alt="">

                        <div class="ms-3">
                          <?= Profil_Admin()['fullname'] ?>
                          <p class="speakers-text mb-0">Tikor</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
    <section class="venue section-padding" id="section_6">
      <div class="container">
        <div class="row" id="">
          <div class="col-lg-12 col-12">
            <h3 class="mb-5">Peta Lokasi</h3>
          </div>
          <div class="col-lg-6 col-sm-6 col-12">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d10113.465026073532!2d107.66838273174673!3d-7.466390778748057!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e661f7fa14fe949%3A0xe162ee3852ef74e!2sKantor%20Kecamatan%20Pakenjeng!5e0!3m2!1sid!2sid!4v1657476411931!5m2!1sid!2sid" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>

          <div class="col-lg-6 col-sm-6 col-12 mt-5 mt-lg-0">
            <div class="venue-thumb bg-white shadow-lg">

              <div class="venue-info-title">
                <h4 class="text-white mb-0">Sekretariat : Kantor Kecamatan <?= ucwords(strtolower(Profil_Admin()['namaKec'])); ?></h4>
              </div>

              <div class="venue-info-body">
                <h5 class="d-flex">
                  <i class="bi-geo-alt me-2"></i>
                  <span>Jl. Raya Bungbulang No. 467. Pakenjeng - Garut</span>
                </h5>

                <h5 class="mt-4 mb-3">
                  <a href="mailto:">
                    <i class="bi-envelope me-2"></i>
                    -
                  </a>
                </h5>

                <h5 class="mb-0">
                  <a href="tel: ">
                    <i class="bi-telephone me-2"></i>
                    -
                  </a>
                </h5>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <section class="contact section-padding" id="section_7">
      <div class="container">
        <div class="row">

          <div class="col-lg-8 col-12 mx-auto">
            <form class="custom-form contact-form bg-white shadow-lg" action="#" method="post" role="form">
              <h2>Please Say Hi</h2>

              <div class="row">
                <div class="col-lg-4 col-md-4 col-12">
                  <input type="text" name="name" id="name" class="form-control" placeholder="Name" required="">
                </div>

                <div class="col-lg-4 col-md-4 col-12">
                  <input type="email" name="email" id="email" pattern="[^ @]*@[^ @]*" class="form-control" placeholder="Email" required="">
                </div>

                <div class="col-lg-4 col-md-4 col-12">
                  <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject">
                </div>

                <div class="col-12">
                  <textarea class="form-control" rows="5" id="message" name="message" placeholder="Message"></textarea>

                  <button type="submit" class="form-control">Submit</button>
                </div>

              </div>
            </form>
          </div>

        </div>
      </div>
    </section>

  </main>

  <footer class="site-footer">
    <div class="container">
      <div class="row align-items-center">

        <div class="col-lg-12 col-12 border-bottom pb-5 mb-5">
          <div class="d-flex">
            <a href="<?= base_url(); ?>" class="navbar-brand mx-auto mx-lg-0">
              <!-- <i class="bi-bullseye brand-logo"></i> -->
              <img src="<?= base_url('icon-dtks.png'); ?>" alt="Logo" class="img-circle" style="width: 45px; height: 45px;">
              <span class="brand-text"><?= nameApp() . '<br> KEC. ' . Profil_Admin()['namaKec']; ?></span>
            </a>

            <ul class="social-icon ms-auto">
              <li><a href="#" class="social-icon-link bi-facebook"></a></li>

              <li><a href="#" class="social-icon-link bi-instagram"></a></li>

              <li><a href="#" class="social-icon-link bi-whatsapp"></a></li>

              <li><a href="#" class="social-icon-link bi-youtube"></a></li>

              <li><a href="/login" class="social-icon-link bi bi-box-arrow-right"></a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-12 ms-lg-auto mt-4 mt-lg-0">
        </div>

        <div class="col-lg-7 col-12">
          <ul class="footer-menu d-flex flex-wrap">

            <li class="footer-menu-item"><a href="#" class="footer-menu-link">Privacy and Terms</a></li>

            <li class="footer-menu-item"><a href="#" class="footer-menu-link">Contact</a></li>
          </ul>
        </div>


        <div class="col-lg-5 col-12 ms-lg-auto">
          <div class="copyright-text-wrap d-flex align-items-center">
            <p class="copyright-text ms-lg-auto mb-0">Copyright © <?= date('Y'); ?> <?= nameApp(); ?> Kec. <?= ucwords(strtolower(Profil_Admin()['namaKec'])); ?>.

              <br>All Rights Reserved.

              <br><br>Design: <a title="CSS Templates" rel="sponsored" href="https://templatemo.com" target="_blank">TemplateMo</a>
            </p>

            <a href="body" class="bi-arrow-up arrow-icon custom-link"></a>
          </div>
        </div>

      </div>
    </div>
  </footer>

  <!-- JAVASCRIPT FILES -->
  <script src="<?= base_url('landing-page/js/jquery.min.js'); ?>"></script>
  <script src="<?= base_url('landing-page/js/bootstrap.min.js'); ?>"></script>
  <script src="<?= base_url('landing-page/js/jquery.sticky.js'); ?>"></script>
  <script src="<?= base_url('landing-page/js/click-scroll.js'); ?>"></script>
  <script src="<?= base_url('landing-page/js/custom.js'); ?>"></script>
  <script src="<?= base_url('assets/plugins/select2/js/select2.min.js'); ?>"></script>

</body>
<!-- div viewmodal non -->

<div class="viewmodal" style="display: none;"></div>

<script>
  $(document).ready(function() {
    $('.select2').select2();

    $('#btnCek').click(function(e) {
      e.preventDefault();
      // alert('OK!');
      cek();
    });
  });

  function cek() {
    let cek_desa = $('#cek_desa').val();
    let cek_nik = $('#cek_nik').val();

    $.ajax({
      type: "POST",
      url: "<?= site_url('cek_usulan'); ?>",
      data: {
        cek_nik: cek_nik,
        cek_desa: $('#cek_desa').val(),
        cek_nik: $('#cek_nik').val(),
      },
      cache: false,
      dataType: "json",
      success: function(response) {
        if (response.error) {
          Swal.fire({
            icon: 'error',
            title: 'Perhatian!',
            text: response.error
          });
        }
        if (response.null) {
          Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: response.null
          });
        }
        if (response.data) {
          $('.viewmodal').html(response.data).show();
          // $('#hasil_pencarian').on('shown.bs.modal', function(event) {
          //   $('#cek_nik').focus();
          // });
          $('#hasil_pencarian').modal('show');
        }
      },
      error: function(xhr, thrownError) {
        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
      }
    });
  }
</script>

</html>