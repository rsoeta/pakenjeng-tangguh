<!DOCTYPE html>
<html lang="id" prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb#">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>SINDEN — <?= titleApp(); ?></title>
  <meta name="description" content="<?= titleApp(); ?>. Sistem Informasi Terpadu Pengelolaan dan Monitoring Kesejahteraan Sosial Desa Pasirlangu.">
  <meta name="author" content="Rian Sutarsa">
  <meta name="keywords" content="SINDEN, DTKS, Bansos, Desa Pasirlangu, Kesejahteraan Sosial, Kemiskinan Ekstrem">

  <!-- Open Graph / Facebook / WhatsApp -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= base_url('/'); ?>">
  <meta property="og:title" content="SINDEN — <?= titleApp(); ?>">
  <meta property="og:description" content="<?= titleApp(); ?>. Sistem Informasi Terpadu Pengelolaan dan Monitoring Kesejahteraan Sosial Desa Pasirlangu.">
  <meta property="og:image" content="<?= base_url('assets/logo/sinden-og.png'); ?>">
  <meta property="og:image:secure_url" content="<?= base_url('assets/logo/sinden-og.png'); ?>">
  <meta property="og:image:type" content="image/png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="Logo SINDEN">
  <meta property="og:site_name" content="SINDEN">
  <meta property="og:locale" content="id_ID">

  <!-- WhatsApp Image Fallback -->
  <link rel="image_src" href="<?= base_url('assets/logo/sinden-og.png'); ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@sutarsarian">
  <meta name="twitter:creator" content="@sutarsarian">
  <meta name="twitter:title" content="SINDEN — <?= titleApp(); ?>">
  <meta name="twitter:description" content="<?= titleApp(); ?>. Sistem Informasi Terpadu Pengelolaan dan Monitoring Kesejahteraan Sosial Desa Pasirlangu.">
  <meta name="twitter:image" content="<?= base_url('assets/logo/sinden-og.png'); ?>">

  <!-- PWA & Mobile Web App Meta Tags -->
  <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
  <meta name="theme-color" content="#2ecc71">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="apple-mobile-web-app-title" content="SINDEN">
  <link rel="apple-touch-icon" href="<?= base_url('assets/logo/apple-touch-icon.png'); ?>">

  <!-- Favicon -->
  <link rel="icon" href="<?= base_url('assets/logo/icon-192.png'); ?>" type="image/png">
  <link rel="shortcut icon" href="<?= base_url('assets/logo/icon-192.png'); ?>" type="image/png">

  <link rel="stylesheet" href="<?= base_url('assets/css/sinden-landing.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/dist/css/authstyle.css'); ?>">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
  <header>
    <div class="container">
      <div class="logo">
        <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="Logo SINDEN">
        <span>SINDEN</span>
      </div>
    </div>
  </header>

  <section id="home" class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
      <h1>SINDEN</h1>
      <p><?php echo titleApp() ?></p>
      <a href="<?= base_url('login'); ?>" class="btn-primary">Login</a>
    </div>
  </section>

  <section id="articles" class="cards">
    <h2>Berita & Artikel</h2>
    <div class="card-grid">

      <?php if (count($articles) > 0): ?>
        <?php foreach ($articles as $article): ?>
          <div class="card">

            <img src="<?= base_url($article['image']); ?>" alt="<?= esc($article['title']); ?>">

            <h3><?= esc($article['title']); ?></h3>

            <p><?= esc($article['excerpt']); ?></p>

            <a href="<?= base_url('article/' . $article['slug']); ?>" class="read-more">Selengkapnya →</a>

          </div>
        <?php endforeach; ?>

      <?php else: ?>
        <p class="no-articles">Belum ada artikel yang dipublikasikan.</p>
      <?php endif; ?>

    </div>
  </section>

  <footer>
    <p>Versi <?= esc($version); ?></p>
    <p><?= esc($footerText); ?></p>
  </footer>

  <script>
    // dark-mode toggle
    const body = document.body;
    const toggleBtn = document.getElementById("toggleTheme");
    if (localStorage.getItem("theme") === "dark") {
      body.classList.add("dark-mode");
      toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
    }
    toggleBtn.addEventListener("click", () => {
      body.classList.toggle("dark-mode");
      if (body.classList.contains("dark-mode")) {
        localStorage.setItem("theme", "dark");
        toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
      } else {
        localStorage.setItem("theme", "light");
        toggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
      }
    });
  </script>

  <!-- PWA Service Worker Registration -->
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('<?= base_url('sw.js'); ?>')
          .catch(function(err) {
            console.warn('[PWA] SW registration warning:', err);
          });
      });
    }
  </script>
</body>

</html>