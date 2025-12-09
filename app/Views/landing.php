<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SINDEN — Sistem Informasi Data Ekonomi dan Sosial Desa</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/sinden-landing.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/dist/css/authstyle.css'); ?>">
  <link rel="icon" href="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" type="image/png">
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
</body>

</html>