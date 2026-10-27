<!DOCTYPE html>
<html lang="id" prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb#">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= esc($article['title']); ?> — <?= nameApp(); ?></title>

    <!-- SEO & Open Graph -->
    <meta name="description" content="<?= esc($article['excerpt']); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?= current_url(); ?>">
    <meta property="og:title" content="<?= esc($article['title']); ?> — <?= nameApp(); ?>">
    <meta property="og:description" content="<?= esc($article['excerpt']); ?>">
    <?php
    $ogImg = !empty($article['image']) ? base_url($article['image']) : base_url('assets/logo/sinden-og.png');
    ?>
    <meta property="og:image" content="<?= $ogImg; ?>">
    <meta property="og:image:secure_url" content="<?= $ogImg; ?>">
    <meta property="og:image:alt" content="<?= esc($article['title']); ?>">
    <meta property="og:site_name" content="<?= nameApp(); ?>">
    <meta property="og:locale" content="id_ID">
    <link rel="image_src" href="<?= $ogImg; ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($article['title']); ?> — <?= nameApp(); ?>">
    <meta name="twitter:description" content="<?= esc($article['excerpt']); ?>">
    <meta name="twitter:image" content="<?= $ogImg; ?>">

    <!-- PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
    <meta name="theme-color" content="#2ecc71">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?= nameApp(); ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/logo/apple-touch-icon.png'); ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/sinden-landing.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/authstyle.css'); ?>">
    <link rel="icon" href="<?= base_url('assets/logo/icon-192.png'); ?>" type="image/png">
    <link rel="shortcut icon" href="<?= base_url('assets/logo/icon-192.png'); ?>" type="image/png">
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