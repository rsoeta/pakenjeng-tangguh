<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= esc($article['title']); ?> — SINDEN</title>

    <!-- SEO -->
    <meta name="description" content="<?= esc($article['excerpt']); ?>">
    <meta property="og:title" content="<?= esc($article['title']); ?>">
    <meta property="og:description" content="<?= esc($article['excerpt']); ?>">
    <meta property="og:image" content="<?= $article['image_url']; ?>">
    <meta property="og:type" content="article">

    <link rel="stylesheet" href="<?= base_url('assets/css/sinden-landing.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/authstyle.css'); ?>">
    <link rel="icon" href="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" type="image/png">

</head>

<body>

    <!-- HEADER -->
    <header>
        <div class="container article-header">

            <div class="logo">
                <img src="<?= base_url('assets/logo/SINDEN-logo.png'); ?>" alt="Logo <?= nameApp(); ?>">
                <span><?= nameApp(); ?></span>
            </div>

            <a href="<?= base_url('/'); ?>" class="btn-back">← Kembali</a>

        </div>
    </header>

    <!-- MAIN ARTICLE -->
    <section class="article-detail">

        <div class="breadcrumb" style="font-size:14px;color:#999;margin-bottom:10px;">
            <a href="<?= base_url('/'); ?>" style="color:#777;text-decoration:none;">Beranda</a> ›
            <span>Artikel</span>
        </div>

        <h1 class="article-title"><?= esc($article['title']); ?></h1>

        <p class="article-meta">
            Dipublikasikan pada <?= esc($article['published']); ?>
        </p>

        <img src="<?= $article['image_url']; ?>" class="article-main-image">

        <article class="article-content">
            <?= $article['description']; ?>
        </article>

        <a href="<?= base_url('/'); ?>" class="back-home">← Kembali ke Beranda</a>

    </section>

    <footer>
        <p>Versi <?= esc($version); ?></p>
        <p><?= esc($footerText); ?></p>
    </footer>

</body>

</html>