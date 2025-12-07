<?php

/**
 * Auto-generate URL sesuai modul yang benar.
 * Tidak breaking project lama karena:
 * - Jika URL sudah absolute → langsung dikembalikan
 * - Jika URL cocok modul tertentu → ditambahkan prefix automatically
 */
function menu_url(string $url)
{
    // 1. Jika URL sudah absolute (mengandung http:// atau /), langsung return
    if (strpos($url, 'http') === 0 || strpos($url, '/') === 0) {
        return $url;
    }

    // 2. Mapping prefix berdasarkan modul group
    $prefixMap = [
        // Modul Admin
        'migrate'             => 'admin/',
        'download-db'         => 'admin/',
        'articles'            => 'admin/',
        // 'article-categories'  => 'admin/',
        // 'users'               => 'admin/',
        'settings'            => 'admin/',

        // Modul DTSEN
        'pemeriksaan'        => 'dtsen/',
        'reminder-monitor'   => 'dtsen/',
        'laporan'            => 'dtsen/',
        'monitoring'         => 'dtsen/',
        'pengaturan_wa'      => 'dtsen/',
        // 'dtsen-se'           => 'dtsen/',

        // Modul Verivali
        'verivali'           => '',
        'verivalipbi'        => '',
        'verivalianomali'    => '',

        // Modul Aplikasi lain sesuai kebutuhan
        'usulan-bansos'      => '',
        'users'              => '',
        'settings'           => '',
    ];

    // 3. Jika ditemukan prefix yg cocok → gunakan prefix tersebut
    foreach ($prefixMap as $key => $prefix) {
        if (stripos($url, $key) === 0) {
            return base_url($prefix . $url);
        }
    }

    // 4. Default (compatibility project lama)
    return base_url($url);
}

function menu()
{
    static $menuCache;

    if ($menuCache === null) {
        $db = \Config\Database::connect();
        $menuCache = $db->table('tb_menu')
            ->orderBy('tm_parent_id', 'asc')
            ->orderBy('tm_id', 'asc')
            ->get()
            ->getResultArray();
    }

    return $menuCache;
}

function menu_child($parent_id)
{
    return array_values(array_filter(menu(), function ($m) use ($parent_id) {
        return $m['tm_parent_id'] == $parent_id;
    }));
}

function menu_child_child($parent_id)
{
    return menu_child($parent_id);
}

function menu_child_child_child($parent_id)
{
    return menu_child($parent_id);
}

function menu_is_active(string $menuUrl)
{
    $request = \Config\Services::request();

    $seg1 = strtolower($request->getUri()->getSegment(1));
    $seg2 = strtolower($request->getUri()->getSegment(2));

    // Auto detect modul DTSEN (menggunakan prefix /dtsen/)
    if ($seg1 === 'dtsen') {
        return $seg2 === strtolower($menuUrl);
    }

    // Default untuk project lama
    return $seg1 === strtolower($menuUrl);
}

function menu_is_open(array $children)
{
    foreach ($children as $child) {
        if (menu_is_active($child['tm_url'])) {
            return true;
        }
    }
    return false;
}
