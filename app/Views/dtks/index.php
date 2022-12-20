<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Rian Sutarsa">
    <meta name="keywords" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Desa Pasirlangu">
    <meta name="description" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Desa Pasirlangu">
    <meta property="og:title" content="Opr New DTKS | <?= $title; ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= base_url(); ?>/dtks" />
    <meta property="og:image" content="<?= base_url(); ?>/pages/home/images/home-bg.jpg" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@admindtks" />
    <meta name="twitter:creator" content="@admindtks" />
    <meta name="twitter:title" content="Opr New DTKS | <?= $title; ?>" />
    <meta name="twitter:description" content="Aplikasi Pembantu Pemutakhiran Data Terpadu Kesejahteraan Sosial (DTKS) Desa Pasirlangu" />
    <title>Opr NewDTKS | <?= $title; ?></title>
    <link rel="icon" type="image/x-icon" href="<?= base_url(); ?>/pages/dtks/assets/favicon.ico" />
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="<?= base_url('pages/dtks/css/styles.css'); ?>" rel="stylesheet" />
</head>

<body id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="#page-top">Home</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dtks/yatim">Form. Isi Anak Yatim</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dtks/login">Sign In</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Masthead-->
    <header class="masthead">
        <div class="container px-4 px-lg-5 d-flex h-100 align-items-center justify-content-center">
            <div class="d-flex justify-content-center">
                <div class="text-center">
                    <h1 class="mx-auto my-0 text-uppercase">Opr NewDTKS</h1>
                    <h2 class="text-white-50 mx-auto mt-2 mb-5">A Template, responsive, one page Bootstrap theme created by
                        Start Bootstrap.</h2>
                    <a class="btn btn-primary" href="/dtks/login">Get Started</a>
                </div>
            </div>
        </div>
    </header>
    <!-- About-->
    <section class="about-section" id="about">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 justify-content-center">
                <div class="col-lg-8">
                    <h2 class="text-white mb-4 text-center">Pentingnya Perbaikan DTKS untuk Efektivitas Program Bantuan Sosial</h2>
                    <blockquote class="blockquote">
                        <p class="text-white-50 text-left">
                            Di tengah situasi pandemi Covid-19 yang kasus penularaanya masih belum mereda, pemerintah terus berupaya memperbaiki sistem bantuan sosial yang ada. Salah satu strategi pemerintah dalam memperbaiki sistem ini adalah dengan meningkatkan akurasi penerima bantuan sosial. Masyarakat yang berhak menerima bantuan dari pemerintah harus merupakan orang yang paling membutuhkan. Peningkatan akurasi dilakukan dengan cara memperbaiki kualitas data administratif yang digunakan oleh pemerintah, yaitu Data Terpadu Kesejahteraan Sosial (DTKS).
                        </p>
                        <footer class="blockquote-footer"><a href="http://www.tnp2k.go.id/" target="blank">TNP2K</a></footer>
                    </blockquote>
                    <img class="img-fluid" src="<?= base_url(); ?>/pages/dtks/assets/img/TNP2K_thumbnail_08.jpg" alt="Pentingnya Perbaikan DTKS untuk Efektivitas Program Bantuan Sosial" />
                </div>
            </div>
        </div>
    </section>
    <!-- Projects-->
    <section class="projects-section bg-light" id="projects">
        <div class="container px-4 px-lg-5">
            <!-- Featured Project Row-->
            <div class="row gx-0 mb-4 mb-lg-5 align-items-center">
                <div class="col-xl-8 col-lg-7"><img class="img-fluid mb-3 mb-lg-0" src="<?= base_url(); ?>/pages/home/images/20200813_154459.jpg" alt="..." /></div>
                <div class="col-xl-4 col-lg-5">
                    <div class="featured-text text-left text-lg-left">
                        <h4>TKSK & Fasilitator Desa</h4>
                        <p class="text-black-50 mb-0">Bimbingan Teknis Fasilitator LAPAD RUHAMA (Layanan Terpadu Rumah Harapan Masyarakat) untuk 442 desa/kelurahan Gelombang I (05/08) bertempat di Hotel Agusta Garut. Bimbingan Teknis Fasilitator LAPAD RUHAMA dalam mendukung visi Kabupaten Garut Bertaqwa, Maju dan Sejahtera yang merupakan bagian Gerakan Besar penanggulangan kemiskinan.
                            Fasilitator direkomendasi dari Desa dan MUI desa, yang diberi tugas untuk mendata serta menangani keluhan masyarakat berkenaan layanan akses program pemerintah, provinsi dan kabupaten/kota berdasarkan Basis Data Terpadu (BDT).
                            Dibuka oleh Bupati Garut H. Rudy Gunawan, SH., MH., MP, di hadiri oleh Plt. Kepala Bappeda, Ketua BAZNAS, para Camat, OPD terkait penanggulangan kemiskinan. Turut hadir pula Wakil Bupati Garut dr. H. Helmi Budiman memantau serta memberi arahan dalam bimtek fasilitator LAPAD Ruhama.</p>
                    </div>
                </div>
            </div>
            <!-- Project One Row-->
            <div class="row gx-0 mb-5 mb-lg-0 justify-content-center">
                <div class="col-lg-6"><img class="img-fluid" src="<?= base_url(); ?>/pages/dtks/assets/img/demo-image-01.jpg" alt="..." /></div>
                <div class="col-lg-6">
                    <div class="bg-black text-center h-100 project">
                        <div class="d-flex h-100">
                            <div class="project-text w-100 my-auto text-center text-lg-left">
                                <h4 class="text-white">Misty</h4>
                                <p class="mb-0 text-white-50">An example of where you can put an image of a project, or
                                    anything else, along with a description.</p>
                                <hr class="d-none d-lg-block mb-0 ms-0" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Project Two Row-->
            <div class="row gx-0 justify-content-center">
                <div class="col-lg-6"><img class="img-fluid" src="<?= base_url(); ?>/pages/dtks/assets/img/demo-image-02.jpg" alt="..." /></div>
                <div class="col-lg-6 order-lg-first">
                    <div class="bg-black text-center h-100 project">
                        <div class="d-flex h-100">
                            <div class="project-text w-100 my-auto text-center text-lg-right">
                                <h4 class="text-white">Mountains</h4>
                                <p class="mb-0 text-white-50">Another example of a project with its respective
                                    description. These sections work well responsively as well, try this theme on a
                                    small screen!</p>
                                <hr class="d-none d-lg-block mb-0 me-0" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Signup-->
    <!-- <section class="signup-section" id="signup">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5">
                <div class="col-md-10 col-lg-8 mx-auto text-center">
                    <i class="far fa-paper-plane fa-2x mb-2 text-white"></i>
                    <h2 class="text-white mb-5">Subscribe to receive updates!</h2>
                    <form class="form-signup d-flex flex-column flex-sm-row">
                        <input class="form-control flex-fill me-0 me-sm-2 mb-3 mb-sm-0" id="inputEmail" type="email" placeholder="Enter email address..." />
                        <button class="btn btn-primary" type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </section> -->
    <!-- Contact-->
    <section class="contact-section bg-black">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="card py-4 h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-map-marked-alt text-primary mb-2"></i>
                            <h4 class="text-uppercase m-0">Address</h4>
                            <hr class="my-4 mx-auto" />
                            <div class="small text-black-50">Jl. Desa Km. 200 Kp. Rahayu Desa Pasirlangu Kec. Pakenjeng Kab. Garut - 44164</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="card py-4 h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-envelope text-primary mb-2"></i>
                            <h4 class="text-uppercase m-0">Email</h4>
                            <hr class="my-4 mx-auto" />
                            <div class="small text-black-50"><a href="#!">riansutarsa@outlook.com</a></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="card py-4 h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-mobile-alt text-primary mb-2"></i>
                            <h4 class="text-uppercase m-0">Phone</h4>
                            <hr class="my-4 mx-auto" />
                            <div class="small text-black-50">---</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="social d-flex justify-content-center">
                <a class="mx-2" href="https://twitter.com/riansutarsa" target="blank"><i class="fab fa-twitter"></i></a>
                <a class="mx-2" href="https://web.facebook.com/sutarsarian" target="blank"><i class="fab fa-facebook-f"></i></a>
                <a class="mx-2" href="https://github.com/rsoetarsa" target="blank"><i class="fab fa-github"></i></a>
            </div>
        </div>
    </section>
    <!-- Footer-->
    <footer class="footer bg-black small text-center text-white-50">
        <div class="container px-4 px-lg-5">Copyright &copy; Operator NewDTKS 2021</div>
    </footer>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="<?= base_url(); ?>/pages/dtks/js/scripts.js"></script>
</body>

</html>