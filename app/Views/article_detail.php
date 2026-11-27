<!DOCTYPE html>
<html lang="id" prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb#">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    // ============================================================
    // 🖼 DETEKSI GAMBAR UTAMA & METADATA OPEN GRAPH (MEDSOS)
    // ============================================================
    $ogImg    = base_url('assets/logo/sinden-og.png');
    $ogWidth  = 1200;
    $ogHeight = 630;
    $ogMime   = 'image/png';

    if (!empty($article['image'])) {
        $imgName = $article['image'];
        $relPath = str_starts_with($imgName, 'uploads/') ? $imgName : 'uploads/articles/' . $imgName;
        $absFile = FCPATH . $relPath;

        if (file_exists($absFile)) {
            $ogImg = base_url($relPath);
            $sizeInfo = @getimagesize($absFile);
            if ($sizeInfo) {
                $ogWidth  = $sizeInfo[0];
                $ogHeight = $sizeInfo[1];
                $ogMime   = $sizeInfo['mime'] ?? 'image/jpeg';
            }
        } elseif (str_starts_with($imgName, 'http')) {
            $ogImg = $imgName;
        } else {
            $ogImg = base_url($relPath);
        }
    } elseif (!empty($article['image_url'])) {
        $ogImg = $article['image_url'];
    }

    // Bersihkan excerpt untuk meta deskripsi
    $rawExcerpt = !empty($article['excerpt']) 
        ? $article['excerpt'] 
        : character_limiter(strip_tags($article['description'] ?? ''), 160);
    $cleanExcerpt = trim(preg_replace('/\s+/', ' ', strip_tags($rawExcerpt)));

    $currentUrl = current_url();
    $pageTitle  = esc($article['title']) . ' — ' . nameApp();
    $publishedTime = !empty($article['created_at']) ? date('c', strtotime($article['created_at'])) : '';
    $modifiedTime  = !empty($article['updated_at']) ? date('c', strtotime($article['updated_at'])) : $publishedTime;
    ?>

    <title><?= $pageTitle; ?></title>

    <!-- Standard SEO -->
    <meta name="description" content="<?= esc($cleanExcerpt); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="<?= $currentUrl; ?>">
    <meta name="author" content="<?= esc(nameApp()); ?>">

    <!-- Open Graph (WhatsApp, Facebook, LinkedIn, Telegram) -->
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="<?= esc(nameApp()); ?>">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="<?= $currentUrl; ?>">
    <meta property="og:title" content="<?= esc($article['title']); ?>">
    <meta property="og:description" content="<?= esc($cleanExcerpt); ?>">
    <meta property="og:image" content="<?= $ogImg; ?>">
    <meta property="og:image:secure_url" content="<?= $ogImg; ?>">
    <meta property="og:image:type" content="<?= $ogMime; ?>">
    <meta property="og:image:width" content="<?= $ogWidth; ?>">
    <meta property="og:image:height" content="<?= $ogHeight; ?>">
    <meta property="og:image:alt" content="<?= esc($article['title']); ?>">
    <?php if ($publishedTime): ?>
    <meta property="article:published_time" content="<?= $publishedTime; ?>">
    <?php endif; ?>
    <?php if ($modifiedTime): ?>
    <meta property="article:modified_time" content="<?= $modifiedTime; ?>">
    <?php endif; ?>
    <meta property="article:section" content="Artikel">

    <!-- Twitter / X Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= $currentUrl; ?>">
    <meta name="twitter:title" content="<?= esc($article['title']); ?>">
    <meta name="twitter:description" content="<?= esc($cleanExcerpt); ?>">
    <meta name="twitter:image" content="<?= $ogImg; ?>">
    <meta name="twitter:image:alt" content="<?= esc($article['title']); ?>">

    <!-- Link Image Source untuk Web Crawlers -->
    <link rel="image_src" href="<?= $ogImg; ?>">

    <!-- PWA -->
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">
    <meta name="theme-color" content="#2ecc71">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?= nameApp(); ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/logo/apple-touch-icon.png'); ?>">

    <!-- Google Fonts: Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Stylesheets & Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome-free/css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/authstyle.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/sinden-landing.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/sinden-landing.css') ?: '1'; ?>">
    <link rel="icon" href="<?= base_url('assets/logo/icon-192.png'); ?>" type="image/png">
    <link rel="shortcut icon" href="<?= base_url('assets/logo/icon-192.png'); ?>" type="image/png">
</head>

<body data-sw-url="<?= esc(base_url('sw.js'), 'attr'); ?>">

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

        <div class="breadcrumb article-breadcrumb">
            <a href="<?= base_url('/'); ?>" class="article-breadcrumb-link">Beranda</a>
            <span class="article-breadcrumb-sep">›</span>
            <span class="article-breadcrumb-current">Artikel</span>
        </div>

        <h1 class="article-title"><?= esc($article['title']); ?></h1>

        <p class="article-meta">
            <span>
                <i class="far fa-calendar-alt text-primary"></i>
                <?= esc($article['published']); ?>
            </span>
            <span>•</span>
            <span>
                <i class="far fa-user text-muted"></i>
                <?= esc(nameApp()); ?>
            </span>
        </p>

        <img src="<?= $article['image_url']; ?>" alt="<?= esc($article['title']); ?>" class="article-main-image">

        <article class="article-content">
            <?= $article['description']; ?>
        </article>

        <!-- BAGIKAN ARTIKEL -->
        <div class="article-share-bar">
            <div class="share-label">
                <i class="fas fa-share-alt text-primary"></i>
                Bagikan Artikel:
            </div>
            <div class="share-actions">
                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send?text=<?= rawurlencode($article['title'] . "\n\n" . $currentUrl); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-share btn-share-wa"
                    title="Bagikan ke WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>

                <!-- Salin Link -->
                <button type="button" class="btn-share btn-share-copy" id="btnCopyLink" 
                    data-title="<?= esc($article['title']); ?>" 
                    data-url="<?= $currentUrl; ?>" 
                    title="Salin Judul dan Link Artikel">
                    <i class="fas fa-link" id="copyIcon"></i>
                    <span id="copyText">Salin Link</span>
                </button>
            </div>
        </div>

        <a href="<?= base_url('/'); ?>" class="back-home">← Kembali ke Beranda</a>

    </section>

    <!-- TOAST NOTIFICATION -->
    <div id="toastCopy" class="toast-copy">
        <i class="fas fa-check-circle toast-copy-icon"></i>
        <span>Judul dan link artikel berhasil disalin ke clipboard!</span>
    </div>

    <footer>
        <p>Versi <?= esc(!empty($version) ? $version : versionApp()); ?></p>
        <p><?= esc(!empty($footerText) ? $footerText : footerText()); ?></p>
    </footer>

    <!-- Copy Link & PWA Script -->
    <script src="<?= base_url('assets/js/landing-public.js'); ?>?v=<?= @filemtime(FCPATH . 'assets/js/landing-public.js') ?: '1'; ?>" defer></script>
</body>

</html>
