<?php
$userRole = session()->get('role_id') ?? 99;
$userImage = session()->get('user_image') ?? 'default.png';
$userName = session()->get('fullname') ?? (session()->get('username') ?? 'Pengguna SINDEN');
$avatarUrl = function_exists('Foto_Profil') ? Foto_Profil($userImage, 'profil') : base_url('assets/dist/img/' . $userImage);
$activeTab = function_exists('mobile_active_tab') ? mobile_active_tab() : '';

// Ambil semua menu dari database
$allMenus = menu();

// Helper untuk filter akses dan status menu
$filterAccess = function ($items) use ($userRole) {
    return array_values(array_filter($items, function ($item) use ($userRole) {
        return ((int)$item['tm_status'] === 1 && (int)$item['tm_grup_akses'] >= (int)$userRole && $item['tm_url'] !== 'pembaruan-keluarga/pemulihan');
    }));
};

// Helper mencari parent_id secara dinamis (fleksibel lintas database lokal / server online)
$findParentId = function ($keywords) use ($allMenus) {
    if (!is_array($keywords)) $keywords = [$keywords];
    foreach ($allMenus as $m) {
        if ((int)$m['tm_parent_id'] === 0) {
            foreach ($keywords as $kw) {
                if (stripos($m['tm_nama'], $kw) !== false || stripos($m['tm_url'], $kw) !== false) {
                    return (int)$m['tm_id'];
                }
            }
        }
    }
    return null;
};

// Helper mencari menu individual secara dinamis
$findMenuItem = function ($keywords) use ($allMenus, $userRole) {
    if (!is_array($keywords)) $keywords = [$keywords];
    foreach ($allMenus as $m) {
        if ((int)$m['tm_status'] !== 1 || (int)$m['tm_grup_akses'] < (int)$userRole) continue;
        foreach ($keywords as $kw) {
            if (stripos($m['tm_url'], $kw) !== false || stripos($m['tm_nama'], $kw) !== false) {
                return $m;
            }
        }
    }
    return null;
};

// Dapatkan ID parent secara dinamis (dengan fallback angka lokal)
$dtsenId       = $findParentId(['dtsen']) ?? 3;
$kemiskinanId  = $findParentId(['kemiskinan']) ?? 41;
$bansosId      = $findParentId(['bansos', 'bantuan']) ?? 82;
$vervalId      = $findParentId(['verval', 'verivali']) ?? 11;
$pbiId         = $findParentId(['pbi']) ?? 7;
$settingsId    = $findParentId(['setting', 'pengaturan']) ?? 22;

// 1. DATA SUBMENU DTSEN & KEMISKINAN
$dtsenDirect = $filterAccess(menu_child($dtsenId));
$dtsenUtama = [];
$banpangItems = [];
$monitoringItems = [];

foreach ($dtsenDirect as $dItem) {
    if ($dItem['tm_url'] === '#' || empty($dItem['tm_url'])) {
        $sub = $filterAccess(menu_child($dItem['tm_id']));
        if (stripos($dItem['tm_nama'], 'banpang') !== false) {
            $banpangItems = $sub;
        } elseif (stripos($dItem['tm_nama'], 'monitoring') !== false) {
            $monitoringItems = $sub;
        }
    } else {
        $dtsenUtama[] = $dItem;
    }
}
$kemiskinanItems = $filterAccess(menu_child($kemiskinanId));

// 2. DATA SUBMENU BANSOS, VERVAL, & ASSESSMENT
$bansosItems = $filterAccess(menu_child($bansosId));

// Assessment
$assessmentItem = $findMenuItem(['assessment']);
if ($assessmentItem && !in_array($assessmentItem['tm_id'], array_column($bansosItems, 'tm_id'))) {
    $bansosItems[] = $assessmentItem;
}

// VERVAL Items
$vervalDirect = $filterAccess(menu_child($vervalId));
$vervalItems = [];
foreach ($vervalDirect as $vItem) {
    if ($vItem['tm_url'] === '#' || empty($vItem['tm_url'])) {
        $subVerval = $filterAccess(menu_child($vItem['tm_id']));
        foreach ($subVerval as $sv) {
            if ($sv['tm_url'] === '#' || empty($sv['tm_url'])) {
                $subSub = $filterAccess(menu_child($sv['tm_id']));
                foreach ($subSub as $ssv) {
                    $vervalItems[] = $ssv;
                }
            } else {
                $vervalItems[] = $sv;
            }
        }
    } else {
        $vervalItems[] = $vItem;
    }
}

// 3. DATA SUBMENU PBI-JKN
$pbiItems = $filterAccess(menu_child($pbiId));

// 4. DATA MENU & AKUN
$settingsChildren = $filterAccess(menu_child($settingsId));
$adminItems = [];
$profilItem = null;

foreach ($settingsChildren as $sItem) {
    if ($sItem['tm_url'] === 'profil_user') {
        $profilItem = $sItem;
    } else {
        if ($sItem['tm_url'] !== '#' && !empty($sItem['tm_url'])) {
            $adminItems[] = $sItem;
        }
        $subSettings = $filterAccess(menu_child($sItem['tm_id']));
        foreach ($subSettings as $subSet) {
            $adminItems[] = $subSet;
        }
    }
}

// Helper render kartu grid
$renderCard = function ($item) {
    $nama = esc($item['tm_nama'] ?? '');
    if ($nama === '2025') $nama = 'PDTT 2025';
    $icon = !empty($item['tm_icon']) ? $item['tm_icon'] : 'fas fa-th-large';
    $url = menu_url($item['tm_url'] ?? '#');
    $isActive = menu_is_active($item['tm_url'] ?? '');
    $activeClass = $isActive ? 'active' : '';

    return '<a href="' . $url . '" class="mobile-grid-card ' . $activeClass . '">
        <div class="mobile-grid-icon-wrap">
            <i class="' . esc($icon) . '"></i>
        </div>
        <span class="mobile-grid-text">' . $nama . '</span>
    </a>';
};

// Tambahan: Timeline & Artikel
$infoItems = [];
if ($profilItem) {
    $infoItems[] = $profilItem;
} else {
    $infoItems[] = [
        'tm_nama' => 'Profil Saya',
        'tm_url'  => 'profil_user',
        'tm_icon' => 'fa fa-user'
    ];
}

$timelineItem = $findMenuItem(['timeline', 'dokumentasi/timeline']);
if (!empty($timelineItem)) {
    $infoItems[] = $timelineItem;
}

$artikelItem = $findMenuItem(['articles', 'artikel']);
if (!empty($artikelItem)) {
    $infoItems[] = $artikelItem;
}
if (!empty($artikelItem)) {
    $infoItems[] = $artikelItem[0];
}
?>

<!-- ========================================== -->
<!-- 📱 SINDEN MOBILE EXPANDING TAB BAR & SHEETS -->
<!-- ========================================== -->

<!-- BACKDROP OVERLAY -->
<div class="mobile-sheet-backdrop" id="mobileSheetBackdrop"></div>

<!-- 1. BOTTOM SHEET: DTSEN & KEMISKINAN -->
<div class="mobile-bottom-sheet" id="sheet-dtsen" role="dialog" aria-labelledby="title-dtsen" aria-modal="true">
    <div class="mobile-sheet-handle-area" data-close-sheet="sheet-dtsen">
        <div class="mobile-sheet-handle"></div>
    </div>
    <div class="mobile-sheet-header">
        <div class="mobile-sheet-title-box">
            <span class="mobile-sheet-icon-pill bg-dtsen"><i class="fa fa-users"></i></span>
            <div class="mobile-sheet-title-text">
                <h6 id="title-dtsen" class="mb-0 fw-bold">DTSEN & Kesejahteraan</h6>
                <small class="text-muted">Data keluarga, banpang & kemiskinan</small>
            </div>
        </div>
        <button type="button" class="mobile-sheet-close" data-close-sheet="sheet-dtsen" aria-label="Tutup">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="mobile-sheet-body">
        <?php if (!empty($dtsenUtama)) : ?>
            <div class="mobile-section-badge">DTSEN UTAMA</div>
            <div class="mobile-menu-grid">
                <?php foreach ($dtsenUtama as $item) echo $renderCard($item); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($banpangItems)) : ?>
            <div class="mobile-section-badge">BANTUAN PANGAN (BANPANG)</div>
            <div class="mobile-menu-grid">
                <?php foreach ($banpangItems as $item) echo $renderCard($item); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($monitoringItems)) : ?>
            <div class="mobile-section-badge">MONITORING & KONTROL</div>
            <div class="mobile-menu-grid">
                <?php foreach ($monitoringItems as $item) echo $renderCard($item); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($kemiskinanItems)) : ?>
            <div class="mobile-section-badge">PENANGANAN KEMISKINAN</div>
            <div class="mobile-menu-grid">
                <?php foreach ($kemiskinanItems as $item) echo $renderCard($item); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 2. BOTTOM SHEET: BANSOS & VERVAL -->
<div class="mobile-bottom-sheet" id="sheet-bansos" role="dialog" aria-labelledby="title-bansos" aria-modal="true">
    <div class="mobile-sheet-handle-area" data-close-sheet="sheet-bansos">
        <div class="mobile-sheet-handle"></div>
    </div>
    <div class="mobile-sheet-header">
        <div class="mobile-sheet-title-box">
            <span class="mobile-sheet-icon-pill bg-bansos"><i class="fas fa-hands-helping"></i></span>
            <div class="mobile-sheet-title-text">
                <h6 id="title-bansos" class="mb-0 fw-bold">Bantuan Sosial & Verval</h6>
                <small class="text-muted">Usulan bansos, survei & verivali data</small>
            </div>
        </div>
        <button type="button" class="mobile-sheet-close" data-close-sheet="sheet-bansos" aria-label="Tutup">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="mobile-sheet-body">
        <?php if (!empty($bansosItems)) : ?>
            <div class="mobile-section-badge">MODUL BANSOS & USULAN</div>
            <div class="mobile-menu-grid">
                <?php foreach ($bansosItems as $item) echo $renderCard($item); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($vervalItems)) : ?>
            <div class="mobile-section-badge">VERVAL LAPANGAN & DOKUMEN</div>
            <div class="mobile-menu-grid">
                <?php foreach ($vervalItems as $item) echo $renderCard($item); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 3. BOTTOM SHEET: PBI-JKN -->
<div class="mobile-bottom-sheet" id="sheet-pbi" role="dialog" aria-labelledby="title-pbi" aria-modal="true">
    <div class="mobile-sheet-handle-area" data-close-sheet="sheet-pbi">
        <div class="mobile-sheet-handle"></div>
    </div>
    <div class="mobile-sheet-header">
        <div class="mobile-sheet-title-box">
            <span class="mobile-sheet-icon-pill bg-pbi"><i class="fa fa-credit-card"></i></span>
            <div class="mobile-sheet-title-text">
                <h6 id="title-pbi" class="mb-0 fw-bold">PBI-JKN Kesehatan</h6>
                <small class="text-muted">Data penerima jaminan kesehatan</small>
            </div>
        </div>
        <button type="button" class="mobile-sheet-close" data-close-sheet="sheet-pbi" aria-label="Tutup">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="mobile-sheet-body">
        <?php if (!empty($pbiItems)) : ?>
            <div class="mobile-section-badge">SUBMENU PBI-JKN</div>
            <div class="mobile-menu-grid">
                <?php foreach ($pbiItems as $item) echo $renderCard($item); ?>
            </div>
        <?php else : ?>
            <div class="p-4 text-center text-muted">
                <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                Tidak ada menu PBI-JKN yang tersedia untuk akun Anda.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 4. BOTTOM SHEET: MENU & AKUN -->
<div class="mobile-bottom-sheet" id="sheet-akun" role="dialog" aria-labelledby="title-akun" aria-modal="true">
    <div class="mobile-sheet-handle-area" data-close-sheet="sheet-akun">
        <div class="mobile-sheet-handle"></div>
    </div>
    <div class="mobile-sheet-header">
        <div class="mobile-sheet-title-box">
            <span class="mobile-sheet-icon-pill bg-akun"><i class="fas fa-th-large"></i></span>
            <div class="mobile-sheet-title-text">
                <h6 id="title-akun" class="mb-0 fw-bold">Menu & Pengaturan</h6>
                <small class="text-muted"><?= esc($userName); ?></small>
            </div>
        </div>
        <button type="button" class="mobile-sheet-close" data-close-sheet="sheet-akun" aria-label="Tutup">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="mobile-sheet-body">
        <!-- USER PROFILE QUICK CARD -->
        <div class="mobile-user-card mb-3">
            <img src="<?= $avatarUrl; ?>" class="mobile-user-avatar" alt="Avatar">
            <div class="mobile-user-info">
                <h6 class="mb-0 fw-bold text-truncate"><?= esc(ucwords(strtolower($userName))); ?></h6>
                <span class="badge bg-sinden-subtle text-sinden mt-1">
                    Role ID: <?= esc($userRole); ?>
                </span>
            </div>
            <a href="<?= base_url('profil_user'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                Profil
            </a>
        </div>

        <div class="mobile-section-badge">INFORMASI & DOKUMENTASI</div>
        <div class="mobile-menu-grid">
            <?php foreach ($infoItems as $item) echo $renderCard($item); ?>
        </div>

        <?php if (!empty($adminItems)) : ?>
            <div class="mobile-section-badge">ADMINISTRASI & SISTEM</div>
            <div class="mobile-menu-grid">
                <?php foreach ($adminItems as $item) echo $renderCard($item); ?>
            </div>
        <?php endif; ?>

        <!-- LOGOUT BUTTON -->
        <div class="pt-2 pb-1">
            <a href="<?= base_url('logout'); ?>" class="mobile-logout-button">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar dari Aplikasi
            </a>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 🚀 FIXED BOTTOM TAB BAR (EXPANDING LABEL)  -->
<!-- ========================================== -->
<nav class="mobile-tab-bar" aria-label="Navigasi Utama Mobile">
    <!-- 1. BERANDA (Direct Link) -->
    <a href="<?= base_url('dashboard'); ?>" class="mobile-tab-item <?= ($activeTab === 'beranda') ? 'active' : ''; ?>" data-tab-name="beranda">
        <i class="mobile-tab-icon fas fa-home"></i>
        <span class="mobile-tab-label">Beranda</span>
    </a>

    <!-- 2. DTSEN (Sheet Trigger) -->
    <button type="button" class="mobile-tab-item <?= ($activeTab === 'dtsen') ? 'active' : ''; ?>" data-tab-name="dtsen" data-sheet="sheet-dtsen" aria-expanded="false" aria-controls="sheet-dtsen">
        <i class="mobile-tab-icon fa fa-users"></i>
        <span class="mobile-tab-label">DTSEN</span>
    </button>

    <!-- 3. BANSOS (Sheet Trigger) -->
    <button type="button" class="mobile-tab-item <?= ($activeTab === 'bansos') ? 'active' : ''; ?>" data-tab-name="bansos" data-sheet="sheet-bansos" aria-expanded="false" aria-controls="sheet-bansos">
        <i class="mobile-tab-icon fas fa-hands-helping"></i>
        <span class="mobile-tab-label">Bansos</span>
    </button>

    <!-- 4. PBI-JKN (Sheet Trigger) -->
    <button type="button" class="mobile-tab-item <?= ($activeTab === 'pbi') ? 'active' : ''; ?>" data-tab-name="pbi" data-sheet="sheet-pbi" aria-expanded="false" aria-controls="sheet-pbi">
        <i class="mobile-tab-icon fa fa-credit-card"></i>
        <span class="mobile-tab-label">PBI-JKN</span>
    </button>

    <!-- 5. MENU & AKUN (Sheet Trigger) -->
    <button type="button" class="mobile-tab-item <?= ($activeTab === 'akun') ? 'active' : ''; ?>" data-tab-name="akun" data-sheet="sheet-akun" aria-expanded="false" aria-controls="sheet-akun">
        <i class="mobile-tab-icon fas fa-bars"></i>
        <span class="mobile-tab-label">Menu</span>
    </button>
</nav>

<!-- ========================================== -->
<!-- 🎨 CSS STYLING & SOLID TEAL EXPANDING PILL -->
<!-- ========================================== -->
<style>
/* Sembunyikan elemen mobile di Desktop / Layar lebar */
@media (min-width: 768px) {
    .mobile-tab-bar,
    .mobile-bottom-sheet,
    .mobile-sheet-backdrop {
        display: none !important;
    }
}

/* Khusus Mobile (< 768px) */
@media (max-width: 767.98px) {
    /* Kompensasi padding bawah halaman agar konten tidak tertutup Tab Bar */
    body {
        padding-bottom: calc(72px + env(safe-area-inset-bottom, 0px)) !important;
    }
    .layout-navbar-fixed .wrapper .content-wrapper,
    .content-wrapper {
        padding-bottom: calc(80px + env(safe-area-inset-bottom, 0px)) !important;
    }
    .main-footer {
        margin-bottom: calc(64px + env(safe-area-inset-bottom, 0px)) !important;
    }

    /* Kunci scroll saat Bottom Sheet terbuka */
    body.mobile-sheet-locked {
        overflow: hidden !important;
    }

    /* Angkat floating button agar tidak menimpa Tab Bar di mobile */
    .btn-floating,
    .btn-floating-camera {
        bottom: calc(78px + env(safe-area-inset-bottom, 0px)) !important;
    }
}

/* 🌿 FIXED BOTTOM TAB BAR */
.mobile-tab-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: calc(64px + env(safe-area-inset-bottom, 0px));
    padding: 6px 12px calc(6px + env(safe-area-inset-bottom, 0px)) 12px;
    background: #ffffff;
    border-top: 1px solid #e5e7eb;
    box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    justify-content: space-around;
    z-index: 1040;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

/* 💊 EXPANDING LABEL (PILL) ITEM STYLES */
.mobile-tab-item {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 44px;
    padding: 0 12px;
    border-radius: 50px;
    background: transparent;
    border: none;
    outline: none;
    cursor: pointer;
    text-decoration: none !important;
    -webkit-tap-highlight-color: transparent;
    user-select: none;
    transition: all 0.32s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-tab-item:focus {
    outline: none;
}

.mobile-tab-item:active {
    transform: scale(0.94);
}

/* Ikon Tab */
.mobile-tab-icon {
    font-size: 1.15rem;
    color: #64748b;
    display: inline-block;
    transition: color 0.25s ease, transform 0.25s ease;
}

/* Label Tab (Default: Hidden / Inactive) */
.mobile-tab-label {
    max-width: 0;
    opacity: 0;
    overflow: hidden;
    white-space: nowrap;
    margin-left: 0;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.2px;
    color: #ffffff;
    transition: max-width 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.22s ease,
                margin-left 0.3s ease;
}

/* 🌟 ACTIVE PILL: STYLE SOLID TEAL SINDEN */
.mobile-tab-item.active {
    background: #2EC4B6 !important;
    box-shadow: 0 4px 14px rgba(46, 196, 182, 0.42);
    padding: 0 16px;
}

.mobile-tab-item.active .mobile-tab-icon {
    color: #ffffff !important;
    transform: scale(1.05);
}

.mobile-tab-item.active .mobile-tab-label {
    max-width: 85px;
    opacity: 1;
    margin-left: 7px;
    color: #ffffff !important;
}

/* 🌙 DARK MODE TAB BAR */
body.dark-mode .mobile-tab-bar {
    background: #142320;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: none;
}

body.dark-mode .mobile-tab-item .mobile-tab-icon {
    color: #94a3b8;
}

body.dark-mode .mobile-tab-item.active {
    background: #2EC4B6 !important;
    box-shadow: 0 4px 14px rgba(46, 196, 182, 0.3);
}

body.dark-mode .mobile-tab-item.active .mobile-tab-icon,
body.dark-mode .mobile-tab-item.active .mobile-tab-label {
    color: #ffffff !important;
}

/* ========================================== */
/* 📑 BOTTOM SHEET STYLES (HALF-SCREEN)       */
/* ========================================== */

/* Backdrop */
.mobile-sheet-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 1055;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.25s ease, visibility 0.25s ease;
}

.mobile-sheet-backdrop.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

/* Sheet Box */
.mobile-bottom-sheet {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    max-height: 82vh;
    background: #ffffff;
    border-radius: 24px 24px 0 0;
    z-index: 1060;
    transform: translateY(105%);
    visibility: hidden;
    pointer-events: none;
    box-shadow: none;
    display: flex;
    flex-direction: column;
    transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease, visibility 0.32s;
    overflow: hidden;
}

.mobile-bottom-sheet.open {
    transform: translateY(0);
    visibility: visible;
    pointer-events: auto;
    box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.15);
}

/* Handle Bar Area */
.mobile-sheet-handle-area {
    padding: 10px 0 4px 0;
    cursor: grab;
    display: flex;
    justify-content: center;
    align-items: center;
    touch-action: none;
}

.mobile-sheet-handle {
    width: 44px;
    height: 5px;
    background: #cbd5e1;
    border-radius: 10px;
}

/* Header */
.mobile-sheet-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 18px 12px 18px;
    border-bottom: 1px solid #f1f5f9;
}

.mobile-sheet-title-box {
    display: flex;
    align-items: center;
    gap: 12px;
}

.mobile-sheet-icon-pill {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1rem;
    flex-shrink: 0;
}

.bg-dtsen { background: linear-gradient(135deg, #2EC4B6 0%, #157367 100%); }
.bg-bansos { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.bg-pbi { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.bg-akun { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }

.mobile-sheet-title-text h6 {
    color: #1e293b;
    font-size: 1rem;
}

.mobile-sheet-title-text small {
    font-size: 0.75rem;
}

.mobile-sheet-close {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #f1f5f9;
    border: none;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.mobile-sheet-close:active {
    background: #e2e8f0;
    transform: scale(0.9);
}

/* Body */
.mobile-sheet-body {
    padding: 14px 16px calc(24px + env(safe-area-inset-bottom, 0px)) 16px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

/* Badge Section */
.mobile-section-badge {
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.6px;
    color: #94a3b8;
    text-transform: uppercase;
    margin: 12px 0 8px 4px;
}

.mobile-section-badge:first-child {
    margin-top: 0;
}

/* Menu Grid (4 Kolom Ala Gojek / Tokopedia) */
.mobile-menu-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px 8px;
    margin-bottom: 8px;
}

/* Menu Card */
.mobile-grid-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none !important;
    padding: 8px 4px;
    border-radius: 14px;
    transition: transform 0.18s ease, background 0.18s ease;
    -webkit-tap-highlight-color: transparent;
}

.mobile-grid-card:active {
    transform: scale(0.93);
    background: rgba(46, 196, 182, 0.08);
}

.mobile-grid-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    background: rgba(46, 196, 182, 0.12);
    color: #178a7f;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 6px;
    transition: all 0.2s ease;
}

.mobile-grid-text {
    font-size: 0.72rem;
    font-weight: 600;
    color: #334155;
    text-align: center;
    line-height: 1.2;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 28px;
}

/* State jika item halaman saat ini aktif di dalam sheet */
.mobile-grid-card.active .mobile-grid-icon-wrap {
    background: #2EC4B6 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(46, 196, 182, 0.35);
}

.mobile-grid-card.active .mobile-grid-text {
    color: #0d9488 !important;
    font-weight: 700;
}

/* User Card di Menu Sheet */
.mobile-user-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: #f8fafc;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
}

.mobile-user-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #2EC4B6;
}

.mobile-user-info {
    flex: 1;
    min-width: 0;
}

.bg-sinden-subtle {
    background: rgba(46, 196, 182, 0.15);
}

.text-sinden {
    color: #135d54 !important;
    font-weight: 600;
}

/* Logout Button */
.mobile-logout-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 11px;
    border-radius: 12px;
    background: rgba(239, 68, 68, 0.08);
    color: #dc2626 !important;
    font-weight: 700;
    font-size: 0.88rem;
    text-decoration: none !important;
    transition: all 0.2s ease;
    border: 1px solid rgba(239, 68, 68, 0.15);
    margin-top: 8px;
}

.mobile-logout-button:active {
    background: #dc2626;
    color: #ffffff !important;
}

/* 🌙 DARK MODE UNTUK BOTTOM SHEET */
body.dark-mode .mobile-bottom-sheet {
    background: #182824;
    color: #f8f9fa;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

body.dark-mode .mobile-bottom-sheet.open {
    box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.5);
}

body.dark-mode .mobile-sheet-handle {
    background: #475569;
}

body.dark-mode .mobile-sheet-header {
    border-bottom-color: rgba(255, 255, 255, 0.06);
}

body.dark-mode .mobile-sheet-title-text h6 {
    color: #f1f5f9;
}

body.dark-mode .mobile-sheet-title-text small {
    color: #94a3b8 !important;
}

body.dark-mode .mobile-sheet-close {
    background: #243630;
    color: #cbd5e1;
}

body.dark-mode .mobile-section-badge {
    color: #94a3b8;
}

body.dark-mode .mobile-grid-icon-wrap {
    background: rgba(46, 196, 182, 0.16);
    color: #5eead4;
}

body.dark-mode .mobile-grid-text {
    color: #e2e8f0;
}

body.dark-mode .mobile-grid-card.active .mobile-grid-text {
    color: #5eead4 !important;
}

body.dark-mode .mobile-user-card {
    background: #20332d;
    border-color: rgba(255, 255, 255, 0.06);
}

body.dark-mode .text-sinden {
    color: #5eead4 !important;
}
</style>

<!-- ========================================== -->
<!-- ⚙️ JAVASCRIPT LOGIC INTERACTIVE TAB BAR    -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const backdrop = document.getElementById('mobileSheetBackdrop');
    const sheets = document.querySelectorAll('.mobile-bottom-sheet');
    const tabTriggers = document.querySelectorAll('.mobile-tab-item[data-sheet]');
    const closeButtons = document.querySelectorAll('[data-close-sheet]');
    let currentOpenSheet = null;

    // Fungsi membuka bottom sheet
    function openSheet(sheetId, clickedTab) {
        const targetSheet = document.getElementById(sheetId);
        if (!targetSheet) return;

        // Tutup sheet lain jika ada yang terbuka
        if (currentOpenSheet && currentOpenSheet !== targetSheet) {
            currentOpenSheet.classList.remove('open');
        }

        // Buka sheet tujuan
        targetSheet.classList.add('open');
        backdrop.classList.add('active');
        document.body.classList.add('mobile-sheet-locked');
        currentOpenSheet = targetSheet;

        // Update ARIA
        tabTriggers.forEach(tab => tab.setAttribute('aria-expanded', 'false'));
        if (clickedTab) {
            clickedTab.setAttribute('aria-expanded', 'true');
        }
    }

    // Fungsi menutup semua bottom sheet
    function closeAllSheets() {
        sheets.forEach(sheet => sheet.classList.remove('open'));
        backdrop.classList.remove('active');
        document.body.classList.remove('mobile-sheet-locked');
        tabTriggers.forEach(tab => tab.setAttribute('aria-expanded', 'false'));
        currentOpenSheet = null;
    }

    // Event listener untuk tombol tab yang membuka sheet
    tabTriggers.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const sheetId = this.getAttribute('data-sheet');
            const targetSheet = document.getElementById(sheetId);

            if (targetSheet && targetSheet.classList.contains('open')) {
                // Jika sheet yang sama sudah terbuka, tutup sheet
                closeAllSheets();
            } else {
                // Buka sheet terkait
                openSheet(sheetId, this);
            }
        });
    });

    // Event listener tutup saat klik backdrop
    if (backdrop) {
        backdrop.addEventListener('click', closeAllSheets);
    }

    // Event listener tombol close
    closeButtons.forEach(btn => {
        btn.addEventListener('click', closeAllSheets);
    });

    // Tutup dengan tombol Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && currentOpenSheet) {
            closeAllSheets();
        }
    });

    // 👆 TOUCH SWIPE DOWN TO DISMISS (Ergonomis ala Native App)
    sheets.forEach(sheet => {
        let startY = 0;
        let currentY = 0;
        let isDragging = false;
        const handleArea = sheet.querySelector('.mobile-sheet-handle-area') || sheet.querySelector('.mobile-sheet-header');

        if (!handleArea) return;

        handleArea.addEventListener('touchstart', function (e) {
            startY = e.touches[0].clientY;
            isDragging = true;
            sheet.style.transition = 'none';
        }, { passive: true });

        handleArea.addEventListener('touchmove', function (e) {
            if (!isDragging) return;
            currentY = e.touches[0].clientY;
            let diff = currentY - startY;
            if (diff > 0) {
                // Geser ke bawah mengikuti jari
                sheet.style.transform = `translateY(${diff}px)`;
            }
        }, { passive: true });

        handleArea.addEventListener('touchend', function () {
            if (!isDragging) return;
            isDragging = false;
            sheet.style.transition = 'transform 0.32s cubic-bezier(0.16, 1, 0.3, 1)';
            let diff = currentY - startY;
            if (diff > 90) {
                // Jika ditarik lebih dari 90px, tutup
                closeAllSheets();
            } else {
                // Kembalikan ke posisi awal
                sheet.style.transform = '';
            }
            startY = 0;
            currentY = 0;
        }, { passive: true });
    });
});
</script>
