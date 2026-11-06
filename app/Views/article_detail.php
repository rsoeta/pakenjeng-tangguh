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

    <!-- Stylesheets & Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome-free/css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/dist/css/authstyle.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/sinden-landing.css'); ?>">
    <link rel="icon" href="<?= base_url('assets/logo/icon-192.png'); ?>" type="image/png">
    <link rel="shortcut icon" href="<?= base_url('assets/logo/icon-192.png'); ?>" type="image/png">

    <style>
        .article-detail {
            max-width: 820px;
            width: calc(100% - 32px);
            margin: 30px auto;
            background: #fff;
            padding: clamp(20px, 4vw, 36px);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
        }

        .article-title {
            font-size: clamp(20px, 2.4vw, 25px);
            font-weight: 700;
            color: #1e293b;
            margin-top: 8px;
            margin-bottom: 12px;
            line-height: 1.38;
        }

        .article-meta {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .article-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .article-main-image {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .article-content {
            font-size: 15px;
            color: #334155;
            line-height: 1.8;
            word-wrap: break-word;
        }

        .article-content p {
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 1.15rem;
        }

        .article-content h1 { font-size: 22px; font-weight: 700; margin: 1.5rem 0 0.8rem; color: #1e293b; }
        .article-content h2 { font-size: 19px; font-weight: 700; margin: 1.3rem 0 0.7rem; color: #1e293b; }
        .article-content h3 { font-size: 17px; font-weight: 600; margin: 1.1rem 0 0.6rem; color: #1e293b; }
        .article-content h4 { font-size: 15px; font-weight: 600; margin: 1rem 0 0.5rem; color: #1e293b; }

        .article-content ul, .article-content ol {
            padding-left: 1.4rem;
            margin-bottom: 1.15rem;
        }

        .article-content li {
            margin-bottom: 0.35rem;
        }

        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 16px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .article-content blockquote {
            border-left: 4px solid #2ecc71;
            padding: 10px 16px;
            margin: 16px 0;
            background: #f8fafc;
            color: #475569;
            font-style: italic;
            border-radius: 0 8px 8px 0;
        }

        /* TOMBOL BERBAGI */
        .article-share-bar {
            margin: 32px 0 20px;
            padding: 14px 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .share-label {
            font-size: 13.5px;
            font-weight: 600;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .share-actions {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-share {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 7px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease-in-out;
            user-select: none;
        }

        .btn-share-wa {
            background-color: #25D366;
            color: #ffffff !important;
            border-color: #1ebd5b;
        }

        .btn-share-wa:hover {
            background-color: #1da851;
            color: #ffffff !important;
            box-shadow: 0 3px 10px rgba(37, 211, 102, 0.35);
        }

        .btn-share-copy {
            background-color: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .btn-share-copy:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .btn-share-copy.copied {
            background-color: #10b981;
            color: #ffffff;
            border-color: #059669;
        }

        /* TOAST NOTIFIKASI */
        .toast-copy {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #1e293b;
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0;
            transform: translateY(14px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: none;
            z-index: 9999;
        }

        .toast-copy.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
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

        <div class="breadcrumb" style="font-size: 13px; color: #94a3b8; margin-bottom: 12px;">
            <a href="<?= base_url('/'); ?>" style="color: #64748b; text-decoration: none;">Beranda</a>
            <span style="margin: 0 4px;">›</span>
            <span style="color: #0f172a;">Artikel</span>
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
        <i class="fas fa-check-circle" style="color: #10b981;"></i>
        <span>Judul dan link artikel berhasil disalin ke clipboard!</span>
    </div>

    <footer>
        <p>Versi <?= esc(!empty($version) ? $version : versionApp()); ?></p>
        <p><?= esc(!empty($footerText) ? $footerText : footerText()); ?></p>
    </footer>

    <!-- Copy Link & PWA Script -->
    <script>
        // Fitur Salin Link (Menyalin Judul + Newline + URL)
        const btnCopy = document.getElementById('btnCopyLink');
        const toast = document.getElementById('toastCopy');
        let toastTimeout = null;

        if (btnCopy) {
            btnCopy.addEventListener('click', async function () {
                const title = btnCopy.getAttribute('data-title') || document.querySelector('.article-title')?.innerText?.trim() || '';
                const url = btnCopy.getAttribute('data-url') || window.location.href;
                const textToCopy = title ? `${title}\n\n${url}` : url;
                let success = false;

                if (navigator.clipboard && window.isSecureContext) {
                    try {
                        await navigator.clipboard.writeText(textToCopy);
                        success = true;
                    } catch (err) {
                        success = false;
                    }
                }

                if (!success) {
                    const tempInput = document.createElement('textarea');
                    tempInput.value = textToCopy;
                    tempInput.style.position = 'fixed';
                    tempInput.style.left = '-9999px';
                    tempInput.style.top = '-9999px';
                    document.body.appendChild(tempInput);
                    tempInput.focus();
                    tempInput.select();
                    try {
                        success = document.execCommand('copy');
                    } catch (err) {
                        success = false;
                    }
                    document.body.removeChild(tempInput);
                }

                const copyIcon = document.getElementById('copyIcon');
                const copyText = document.getElementById('copyText');
                if (copyIcon && copyText) {
                    copyIcon.className = 'fas fa-check';
                    copyText.textContent = 'Link Disalin!';
                    btnCopy.classList.add('copied');

                    if (toast) {
                        toast.classList.add('show');
                        if (toastTimeout) clearTimeout(toastTimeout);
                        toastTimeout = setTimeout(() => {
                            toast.classList.remove('show');
                        }, 2800);
                    }

                    setTimeout(() => {
                        copyIcon.className = 'fas fa-link';
                        copyText.textContent = 'Salin Link';
                        btnCopy.classList.remove('copied');
                    }, 2500);
                }
            });
        }

        // Service Worker
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
