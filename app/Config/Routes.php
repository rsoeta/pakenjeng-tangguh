<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Landing');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
// ========================================================
// 🔒 CUSTOM 404 OVERRIDE (Aktif Hanya di Production)
// ========================================================
if (ENVIRONMENT === 'production') {
	$routes->set404Override(function () {
		return view('lockscreen');
	});
}

$routes->setAutoRoute(true);
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
// $routes->get('/', 'Pages::home');

// ========================================================
// 🚀 WEBHOOK AUTO-DEPLOY GITHUB TO CPANEL
// ========================================================
$routes->match(['get', 'post'], 'deploy-sinden-rahasia', 'Webhook::deploy');

// AUTH
// $routes->match(['GET', 'POST'], 'lockscreen', 'Lockscreen::index', ['filter' => 'authfilterdtks']);
$routes->match(['GET', 'POST'], 'login', 'Auth\Auth::login', ['filter' => 'noauthfilterdtks']);
$routes->match(['GET', 'POST'], 'register', 'Auth\Auth::register', ['filter' => 'noauthfilterdtks']);
// $routes->match(['GET', 'POST'], 'register', 'Auth\Auth::regOpSek', ['filter' => 'noauthfilterdtks']);
$routes->match(['GET', 'POST'], 'lupa-password', 'Auth\Auth::lupaPassword', ['filter' => 'noauthfilterdtks']);
$routes->match(['GET', 'POST'], 'requestReset', 'Auth\Auth::requestReset', ['filter' => 'noauthfilterdtks']);
$routes->get('reset-password', 'Auth\Auth::resetPassword', ['filter' => 'noauthfilterdtks']);
$routes->post('reset-password', 'Auth\Auth::processResetPassword', ['filter' => 'noauthfilterdtks']);

// Admin Reset Password
$routes->post('admin-reset-password', 'Auth\Auth::adminResetPassword', ['filter' => 'authfilterdtks']);


// $routes->get('/', 'Auth\Pages::home', ['filter' => 'noauthfilterdtks']);
$routes->get('/', 'Landing::index');
$routes->get('article/(:segment)', 'Landing::articleDetail/$1');

$routes->post('cek_usulan', 'Landing::cek_usulan');

// ✅ Cara 1 (lebih direkomendasikan)
$routes->get('dashboard', 'Auth\Pages::index', [
	'filter' => ['authfilterdtks', 'menufilterdtks']
]);

$routes->get('pages', 'Auth\Pages::index', [
	'filter' => ['authfilterdtks', 'menufilterdtks']
]);

$routes->get('getNilaiJumlah', 'Auth\Pages::getNilaiJumlah');

$routes->get('logout', 'Auth\Auth::logout');

$routes->get('redirect', 'Auth\Auth::redirectToExternalLink');

// ====================================================================
// === SINDEN (Sistem Informasi Data Ekonomi dan Sosial Desa) === //
// ====================================================================

$routes->get('dropdown-rwrt', 'Dtks\Wil::dropdownRwRt');

// USULAN DTSEN
$routes->group('dtsen-usulan', ['namespace' => 'App\Controllers', 'filter' => ['authfilterdtks', 'menufilterdtks']], function ($routes) {
	$routes->post('start', 'DtsenUsulan::start');
	$routes->post('saveStep', 'DtsenUsulan::saveStep');
	$routes->get('getPayload/(:num)', 'DtsenUsulan::getPayload/$1');
	$routes->post('submitFinal', 'DtsenUsulan::submitFinal');
	$routes->post('dtsen-usulan/cariKK', 'DtsenUsulan::cariKK');

	$routes->get('/', 'Dtsen\DtsenUsulan::index');
	$routes->post('save', 'Dtsen\DtsenUsulan::save');
	$routes->get('data', 'Dtsen\DtsenUsulan::getData');
});

// === DTSEN - Sosial Ekonomi (Data Keluarga / Input Desil) ===
$routes->group('dtsen-se', ['filter' => ['authfilterdtks', 'globalview', 'menufilterdtks']], function ($routes) {
	$routes->get('/', 'Dtsen\DtsenSe::index');
	$routes->post('update-desil', 'Dtsen\DtsenSe::updateDesil');
	$routes->post('tabel_data', 'Dtsen\DtsenSe::tabel_data');
	$routes->post('delete', 'Dtsen\DtsenSe::deleteKeluarga');
	$routes->post('restore', 'Dtsen\DtsenSe::restoreKeluarga');
	$routes->post('tabel_arsip', 'Dtsen\DtsenSe::tabel_arsip');
	$routes->get('arsip-anggota', 'Dtsen\DtsenSe::arsipAnggota');
	$routes->post('restore-art', 'Dtsen\DtsenSe::restoreArt');
	$routes->get('list-rw', 'Dtsen\DtsenSe::listRW');
	$routes->get('list-rt/(:segment)', 'Dtsen\DtsenSe::listRT/$1');
	// Gunakan rute POST khusus untuk data tabel (AJAX)
	// Sesuaikan namanya agar konsisten dengan JS di View
	$routes->post('tabel_pemulihan', 'Dtsen\DtsenSe::tabel_pemulihan');
	$routes->post('autofix_rt_rw', 'Dtsen\DtsenSe::autofix_rt_rw');
	$routes->post('search_kk_select2', 'Dtsen\DtsenSe::search_kk_select2');
	$routes->post('tarik_ke_pemulihan', 'Dtsen\DtsenSe::tarik_ke_pemulihan');
});

// === DTSEN - Pembaruan Data Keluarga ===
$routes->group('pembaruan-keluarga', ['filter' => ['authfilterdtks', 'globalview', 'menufilterdtks']], function ($routes) {
	$routes->get('draft', 'Dtsen\DtsenSe::draft'); // Halaman Draft
	$routes->get('submitted', 'Dtsen\DtsenSe::submitted'); // Halaman Submitted
	$routes->get('pemulihan', 'Dtsen\DtsenSe::pemulihan'); // Halaman Pemulihan
	$routes->get('arsip', 'Dtsen\DtsenSe::arsip'); // Halaman Arsip
	// 📡 Routing Data AJAX
	$routes->post('tabel_data', 'Dtsen\DtsenSe::tabel_data');
	// $routes->post('tabel_pemulihan', 'Dtsen\DtsenSe::tabel_pemulihan');
	$routes->post('autofix_rt_rw', 'Dtsen\DtsenSe::autofix_rt_rw');

	$routes->get('detail/(:num)', 'Dtsen\PembaruanKeluarga::detail/$1');
	$routes->get('tambah', 'Dtsen\PembaruanKeluarga::tambah');  // 🆕 Form tambah keluarga baru
	$routes->post('tambah', 'Dtsen\PembaruanKeluarga::store');  // 🆕 Simpan draft baru hasil input
	$routes->post('save-keluarga', 'Dtsen\PembaruanKeluarga::saveKeluarga');
	$routes->post('save-anggota', 'Dtsen\PembaruanKeluarga::saveAnggota');
	$routes->post('delete-anggota', 'Dtsen\PembaruanKeluarga::deleteAnggota');
	$routes->post('delete-keluarga', 'Dtsen\PembaruanKeluarga::deleteKeluarga');
	$routes->post('save-rumah', 'Dtsen\PembaruanKeluarga::saveRumah');
	$routes->post('save-aset', 'Dtsen\PembaruanKeluarga::saveAset');
	$routes->post('save-foto', 'Dtsen\PembaruanKeluarga::saveFoto');
	$routes->post('apply', 'Dtsen\PembaruanKeluarga::apply');

	$routes->get('get-anggota-detail', 'Dtsen\PembaruanKeluarga::getAnggotaDetail');
	// 🧍‍♂️ Prefill Data Individu
	$routes->get('get-anggota-detail/(:num)', 'Dtsen\PembaruanKeluarga::getAnggotaDetail/$1');

	// $routes->get('data', 'Dtsen\PembaruanKeluarga::getDataDraft');
	$routes->get('data', 'Dtsen\PembaruanKeluarga::data');
	$routes->get('lanjutkan/(:num)', 'Dtsen\PembaruanKeluarga::lanjutkan/$1');
	$routes->get('get-anggota-list/(:num)', 'Dtsen\PembaruanKeluarga::getAnggotaList/$1');

	// 🔥 API untuk sinkronisasi desil (manual trigger dari UI)
	$routes->post('sync-desil/(:num)', 'Dtsen\PembaruanKeluarga::syncDesilPerKK/$1');
	$routes->post('sync-desil-global', 'Dtsen\PembaruanKeluarga::syncDesilGlobal');

	$routes->get('desil-history/(:num)', 'Dtsen\PembaruanKeluarga::desilHistory/$1');

	$routes->post('add-historical-desil', 'Dtsen\PembaruanKeluarga::addHistoricalDesil');
});

// USULAN BANSOS
$routes->group('usulan-bansos', ['filter' => ['authfilterdtks', 'globalview', 'menufilterdtks']], function ($routes) {
	$routes->get('/', 'Dtsen\UsulanBansos::index');
	$routes->get('data', 'Dtsen\UsulanBansos::getDataBulanIni');
	$routes->post('verifikasi/(:num)', 'Dtsen\UsulanBansos::verifikasi/$1');
	$routes->post('delete/(:num)', 'Dtsen\UsulanBansos::delete/$1');
	$routes->delete('delete/(:num)', 'Dtsen\UsulanBansos::delete/$1');
	$routes->get('check-desil', 'Dtsen\UsulanBansos::checkDesil');
	$routes->get('search-art', 'Dtsen\UsulanBansos::searchArt');
	$routes->post('save', 'Dtsen\UsulanBansos::save');
	$routes->post('verifikasi/(:num)', 'Dtsen\UsulanBansos::verifikasi/$1');

	// NEW: check deadline API untuk modal + countdown
	$routes->get('check-deadline', 'Dtsen\UsulanBansos::checkDeadline');

	$routes->get('filter-options', 'Dtsen\UsulanBansos::getFilterOptions');

	$routes->get('rt-by-rw/(:any)', 'Dtsen\UsulanBansos::getRTByRW/$1');

	$routes->get('data-filter', 'Dtsen\UsulanBansos::getDataByFilter');
});

// MASTER KKS
$routes->group('master-kks', ['filter' => 'authfilterdtks', 'menufilterdtks'], function ($routes) {
	$routes->get('/', 'Dtsen\MasterKKS::index');
	$routes->post('datatable', 'Dtsen\MasterKKS::datatable');
	$routes->get('get-rw', 'Dtsen\MasterKKS::get_rw_ajax');
	$routes->post('get-rt', 'Dtsen\MasterKKS::get_rt_ajax');
	$routes->post('import', 'Dtsen\MasterKKS::import_excel');
	$routes->post('get-kpm', 'Dtsen\MasterKKS::get_kpm');
	$routes->post('save', 'Dtsen\MasterKKS::save');
	$routes->post('search-penduduk', 'Dtsen\MasterKKS::search_penduduk');
	$routes->post('delete', 'Dtsen\MasterKKS::delete');
});

// DOKUMENTASI BANSOS KKS
$routes->group('bansos-kks', ['filter' => 'authfilterdtks', 'menufilterdtks'], function ($routes) {
	$routes->get('/', 'Dtsen\BansosKKS::index');
	$routes->post('datatable', 'Dtsen\BansosKKS::datatable');
	$routes->post('cari-nik', 'Dtsen\BansosKKS::cari_nik_ajax');
	$routes->post('simpan', 'Dtsen\BansosKKS::simpan');
	$routes->get('edit-ajax/(:num)', 'Dtsen\BansosKKS::edit_ajax/$1');
	$routes->get('get-rw', 'Dtsen\BansosKKS::get_rw_ajax');
	$routes->get('get-rt', 'Dtsen\BansosKKS::get_rt_ajax');
	$routes->post('hapus', 'Dtsen\BansosKKS::hapus');
	$routes->post('toggle-lock', 'Dtsen\BansosKKS::toggleLock');
	// 🚀 ROUTE IMPORT TUGAS DOKUMENTASI KKS DARI SIKS-NG
	$routes->post('importTugasExcel', 'Dtsen\BansosKKS::importTugasExcel');
});

// ==========================================
// 🪪 MODUL ARSIP BPJSTK
// ==========================================
$routes->group('bpjstk', ['filter' => 'authfilterdtks', 'menufilterdtks'], function ($routes) {
	$routes->get('/', 'Dtsen\Bpjstk::index');
	$routes->post('datatable', 'Dtsen\Bpjstk::datatable');
	$routes->post('import', 'Dtsen\Bpjstk::importExcel');
	$routes->get('get-data/(:num)', 'Dtsen\Bpjstk::getData/$1'); // Untuk menarik data ke modal
	$routes->post('simpan-serah-terima', 'Dtsen\Bpjstk::simpanSerahTerima'); // Untuk save foto & TTD
	$routes->post('rollback', 'Dtsen\Bpjstk::rollback'); // 🚀 Tambahkan rute rollback ini
});

// ========================================================
// 📊 SENSUS EKONOMI 2026 (PENCARIAN KELUARGA STRICT)
// ========================================================
$routes->group('sensus-ekonomi', ['filter' => ['authfilterdtks', 'menufilterdtks']], static function ($routes) {
	$routes->get('/', 'Dtsen\SensusEkonomi::index');
	$routes->post('cariKk', 'Dtsen\SensusEkonomi::cariKk');

	// 🚀 BYPASS ROUTE: Menembak fungsi detail tanpa harus masuk menu pembaruan-keluarga
	$routes->get('detail/(:num)', 'Dtsen\PembaruanKeluarga::detail/$1');
	// 🚀 BYPASS AJAX: Mengizinkan Role 6 (Petugas SE) mengambil data tanpa terblokir Menu Filter
	$routes->get('get-anggota-list/(:num)', 'Dtsen\PembaruanKeluarga::getAnggotaList/$1');
	$routes->get('get-anggota-detail/(:num)', 'Dtsen\PembaruanKeluarga::getAnggotaDetail/$1');
	$routes->get('get-anggota-detail', 'Dtsen\PembaruanKeluarga::getAnggotaDetail');
	$routes->get('desil-history/(:num)', 'Dtsen\PembaruanKeluarga::desilHistory/$1');
});

// 🌍 API Wilayah Lokal (Dropdown berantai untuk DTSEN)
$routes->group('api/villages', ['namespace' => 'App\Controllers\Api'], function ($routes) {
	$routes->get('provinces', 'Villages::provinces');
	$routes->get('regencies/(:any)', 'Villages::regencies/$1');
	$routes->get('districts/(:any)', 'Villages::districts/$1');
	$routes->get('villages/(:any)', 'Villages::villages/$1');
	$routes->get('lookup/(:any)', 'Villages::lookup/$1'); // 👈 untuk prefill
});


// WhatsApp Settings (Admin Desa)
$routes->group('pengaturan_wa', ['filter' => ['authfilterdtks', 'menufilterdtks']], function ($routes) {
	$routes->get('/', 'Profil\WaSettings::index');
	$routes->post('save_api', 'Profil\WaSettings::saveApi');
	$routes->post('save_template', 'Profil\WaSettings::saveTemplate');
	$routes->post('preview', 'Profil\WaSettings::preview');

	// Fonnte Settings
	$routes->post('save_fonnte', 'Profil\WaSettings::saveFonnte');
	$routes->post('test_fonnte', 'Profil\WaSettings::testFonnte');
});

// Cron Jobs
$routes->cli('cron/wa-reminder', 'Cron\WaReminder::index');
$routes->get('cron/reminder', 'Cron\Reminder::index');

// DTSEN - Pemeriksaan Data Sosial Ekonomi
$routes->group('dtsen', ['namespace' => 'App\Controllers\Dtsen', 'filter' => ['authfilterdtks', 'menufilterdtks']], function ($routes) {

	// Reminder Monitoring (UI + API)
	$routes->get('laporan', 'ReminderMonitor::index');
	$routes->get('reminder-monitor', 'ReminderMonitor::index');
	$routes->get('reminder-monitor/list', 'ReminderMonitor::listAjax');
	$routes->post('reminder-monitor/resend', 'ReminderMonitor::resend');

	// SUMMARY
	$routes->get('reminder-monitor/summary', 'ReminderMonitor::summary');

	// Pemeriksaan Data
	$routes->get('pemeriksaan', 'Pemeriksaan::index');
	$routes->post('pemeriksaan/listKK', 'Pemeriksaan::listKK');
	$routes->post('pemeriksaan/listART', 'Pemeriksaan::listART');
	$routes->get('pemeriksaan/export', 'Pemeriksaan::export');

	// DETAIL
	$routes->get('kk/detail/(:num)', 'Pemeriksaan::detailKK/$1');
	$routes->get('art/detail/(:num)', 'Pemeriksaan::detailART/$1');

	// EDIT
	$routes->get('kk/edit/(:num)', 'Pemeriksaan::ajaxEditKK/$1');
	$routes->get('art/edit/(:num)', 'Pemeriksaan::ajaxEditART/$1');

	// UPDATE
	$routes->post('kk/update/(:num)', 'Pemeriksaan::ajaxUpdateKK/$1');
	$routes->post('art/update/(:num)', 'Pemeriksaan::ajaxUpdateART/$1');

	// DELETE
	$routes->post('kk/delete/(:num)', 'Pemeriksaan::ajaxDeleteKK/$1');
	$routes->post('art/delete/(:num)', 'Pemeriksaan::ajaxDeleteART/$1');
});

// PENENTUAN KEMISKINAN
$routes->group('dtsen/kemiskinan', ['filter' => ['authfilterdtks', 'menufilterdtks']], function ($routes) {

	$routes->get('penentuan', 'Dtsen\PenentuanKemiskinan::penentuan');
	$routes->get('datatable', 'Dtsen\PenentuanKemiskinan::datatable');
	$routes->get('verifikasi', 'Dtsen\PenentuanKemiskinan::verifikasi');
	$routes->get('verifikasi-data', 'Dtsen\PenentuanKemiskinan::verifikasiData');
	$routes->get('final', 'Dtsen\PenentuanKemiskinan::final');
	$routes->get('final-data', 'Dtsen\PenentuanKemiskinan::finalData');

	$routes->get('alasan', 'Dtsen\PenentuanKemiskinan::getAlasan');

	$routes->post('simpan', 'Dtsen\PenentuanKemiskinan::simpan');

	$routes->post('tolak', 'Dtsen\PenentuanKemiskinan::tolak');
	$routes->post('validasi', 'Dtsen\PenentuanKemiskinan::validasi');

	$routes->get('detail', 'Dtsen\PenentuanKemiskinan::detail');

	$routes->post('rollback', 'Dtsen\PenentuanKemiskinan::rollback');
});

/**
 * =========================================================================
 * 🛡️ MODUL VERVAL (Verifikasi & Validasi) DTSEN
 * =========================================================================
 */

// === PDTT 2025 ===
$routes->group('pdtt', ['filter' => ['authfilterdtks', 'globalview', 'menufilterdtks']], function ($routes) {
	$routes->get('2025', 'Dtsen\Verval\Pdtt2025::index');
	$routes->post('2025/datatable', 'Dtsen\Verval\Pdtt2025::datatable');
	$routes->post('2025/import-excel', 'Dtsen\Verval\Pdtt2025::importExcel');
	$routes->get('2025/export-excel', 'Dtsen\Verval\Pdtt2025::exportExcel');
	$routes->get('2025/export-images', 'Dtsen\Verval\Pdtt2025::exportImages');
	$routes->get('2025/download-images/(:num)', 'Dtsen\Verval\Pdtt2025::downloadImagesPerKpm/$1');
	$routes->get('2025/statistik', 'Dtsen\Verval\Pdtt2025::statistik'); // Sesuaikan nama controllernya

	// API untuk Form Verifikasi
	$routes->get('2025/get-detail/(:num)', 'Dtsen\Verval\Pdtt2025::getDetail/$1');
	$routes->post('2025/save-verifikasi', 'Dtsen\Verval\Pdtt2025::saveVerifikasi');

	// 🚀 API Filter Bertingkat (Desa -> RW -> RT)
	$routes->get('2025/get-rw', 'Dtsen\Verval\Pdtt2025::getFilterRw');
	$routes->get('2025/get-rt/(:any)', 'Dtsen\Verval\Pdtt2025::getFilterRt/$1');
});


// Filter grup dasar: Wajib LOGIN (authfilterdtks)
$routes->group('verval', [
	'namespace' => 'App\Controllers\Dtsen\Verval',
	'filter'    => ['authfilterdtks', 'menufilterdtks'] // Pastikan menufilterdtks juga aktif untuk akses menu,
], function ($routes) {

	// ---------------------------------------------------------------------
	// 📌 SUBMENU: ANOMALI SIKS-NG
	// ---------------------------------------------------------------------
	// Halaman Utama: Ekstra perlindungan HAK AKSES MENU (menufilterdtks)
	$routes->get('anomali', 'Anomali::index', ['filter' => 'menufilterdtks']);

	// 🔓 AJAX Endpoint: Cukup login saja, bebas dari blokir hak akses menu
	$routes->post('anomali/search_nik_ajax', 'Anomali::search_nik_ajax');
	$routes->post('anomali/simpan', 'Anomali::simpan');
	// Tambahkan di bawah rute anomali/simpan
	$routes->get('anomali/get_data_ajax', 'Anomali::get_data_ajax');

	// 🔓 AJAX Endpoint untuk mengambil detail 1 baris anomali
	$routes->get('anomali/get_detail_ajax/(:num)', 'Anomali::get_detail_ajax/$1');

	// 🔓 AJAX Endpoint untuk menyimpan perbaikan KK dari Petugas
	$routes->post('anomali/update_petugas', 'Anomali::update_petugas');

	// 🔓 AJAX Endpoint untuk Dropdown Bertingkat Wilayah (Prov, Kab, Kec, Desa)
	$routes->get('anomali/get_wilayah/(:any)', 'Anomali::get_wilayah/$1');
	$routes->get('anomali/get_wilayah/(:any)/(:any)', 'Anomali::get_wilayah/$1/$2');

	// 🔓 AJAX Endpoint untuk Dropdown SHDK, Kawin, Pekerjaan
	$routes->get('anomali/get_referensi/(:any)', 'Anomali::get_referensi/$1');

	// 🔓 AJAX Endpoint untuk Proses Verifikasi Operator (Setuju / Tolak)
	$routes->post('anomali/proses_verifikasi', 'Anomali::proses_verifikasi');
});

// public CMS / admin
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => ['authfilterdtks', 'menufilterdtks']], function ($routes) {
	$routes->get('articles/data', 'ArticleController::data');
	$routes->get('articles/get/(:num)', 'ArticleController::get/$1');
	$routes->post('articles/store', 'ArticleController::store');
	$routes->post('articles/update/(:num)', 'ArticleController::update/$1');
	$routes->post('articles/delete/(:num)', 'ArticleController::delete/$1');
});


$routes->group('admin', ['filter' => ['authfilterdtks', 'globalview', 'menufilterdtks']], function ($routes) {

	// Migration Tool (Admin Only)
	$routes->get('migrate', 'Admin\MigrationTool::index');
	$routes->get('download-db', 'Admin\MigrationTool::downloadDb');

	// Article Management
	$routes->get('articles', 'Admin\ArticleController::index');
	// di group admin (sudah ada filter auth)

	$routes->get('articles/create', 'Admin\ArticleController::create');
	$routes->get('articles/edit/(:num)', 'Admin\ArticleController::edit/$1');

	// TinyMCE image upload endpoint
	$routes->post('articles/upload-image', 'Admin\ArticleController::uploadImage');
});


// ========================================================
// 📦 MODUL BANTUAN PANGAN (BANPANG)
// ========================================================
$routes->group('banpang', ['filter' => ['authfilterdtks', 'menufilterdtks']], static function ($routes) {
	// 📊 Halaman Rekap & Datatable
	$routes->get('/', 'Dtsen\Banpang::index');
	$routes->post('datatable', 'Dtsen\Banpang::datatable');

	// 📷 Fitur Scanner & AJAX
	$routes->get('scanner', 'Dtsen\Banpang::scanner');
	$routes->post('simpanScan', 'Dtsen\Banpang::simpanScan');
	$routes->get('getLatestScans', 'Dtsen\Banpang::getLatestScans');

	// 🖨️ Ekspor Laporan
	$routes->get('exportExcel', 'Dtsen\Banpang::exportExcel');
	$routes->get('exportPdf', 'Dtsen\Banpang::exportPdf');

	// ========================================================
	// 📥 FITUR BARU: PERBAIKAN DATA BANPANG (REJECT BULOG)
	// ========================================================

	// Halaman Utama Admin (Daftar Data Reject)
	$routes->get('reject', 'Dtsen\Banpang::indexReject');
	$routes->post('reject/datatable', 'Dtsen\Banpang::datatableReject');

	// Upload Data Awal (Excel & Manual)
	$routes->post('reject/importExcel', 'Dtsen\Banpang::importExcelReject');
	$routes->post('reject/tambahManual', 'Dtsen\Banpang::tambahManualReject');

	// Antarmuka Kamera PENTRI & Proses Simpan + Trigger WA
	$routes->get('reject/kamera/(:segment)', 'Dtsen\Banpang::kameraReject/$1');
	$routes->post('reject/simpanDokumentasi', 'Dtsen\Banpang::simpanDokumentasiReject');

	// Antarmuka Kamera PENTRI & Proses Simpan + Trigger WA
	$routes->get('reject/kamera/(:segment)', 'Dtsen\Banpang::kameraReject/$1');
	$routes->post('reject/simpanDokumentasi', 'Dtsen\Banpang::simpanDokumentasiReject');

	// 🚀 PERBAIKAN 2: Rute Eksekusi Verifikasi Admin
	$routes->post('reject/aksiVerifikasi', 'Dtsen\Banpang::aksiVerifikasiReject');

	// 🚀 Tambahan rute baru:
	$routes->get('searchKpmAjax', 'Dtsen\Banpang::searchKpmAjax');
	$routes->post('simpanRejectManual', 'Dtsen\Banpang::simpanRejectManual');
});

// Frontend article view
$routes->get('artikel', 'ArticleFront::index', ['filter' => ['globalview', 'menufilterdtks', 'authfilterdtks']]);
$routes->get('artikel/(:segment)', 'ArticleFront::show/$1');


// ====================================================================
// === DTKS - Data Terpadu Keserjahteraan Sosial === //
// ====================================================================
// CHATTING
$routes->match(['GET', 'POST'], 'chatt', 'Chat::index', ['filter' => 'authfilterdtks']);
$routes->get('getMsg', 'Chat::getMsg', ['filter' => 'authfilterdtks']);
$routes->get('getUserLogged', 'Chat::getUserLogged', ['filter' => 'authfilterdtks']);
$routes->post('updateLastActivity', 'Chat::updateLastActivity', ['filter' => 'authfilterdtks']);

// BNBA
$routes->get('bnba', 'Dtks\Bnba::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_bnba', 'Dtks\Bnba::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('detailBnba', 'Dtks\Bnba::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['GET', 'POST'], 'editBnba', 'Dtks\Bnba::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
// VERIVALI BNBA
$routes->get('verivalibnba', 'Dtks\VervalBnba::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabVerivaliBnba', 'Dtks\VervalBnba::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabVerivaliBnba1', 'Dtks\VervalBnba::tabel_data1', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabVerivaliBnba2', 'Dtks\VervalBnba::tabel_data2', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editBnba', 'Dtks\VervalBnba::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updatebnba', 'Dtks\VervalBnba::ajax_update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editBnba1', 'Dtks\VervalBnba::formedit1', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updatebnba1', 'Dtks\VervalBnba::ajax_update1', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('lockBnba', 'Dtks\VervalBnba::lockBnba', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('unlockBnba', 'Dtks\VervalBnba::unlockBnba', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// VERVAL PBI
$routes->get('verval', 'Dtks\VeriVali09::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('verivalipbi', 'Dtks\VervalPbi::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_pbi', 'Dtks\VervalPbi::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('exportExcel', 'Dtks\VervalPbi::excelpage', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('tmbData', 'Dtks\VervalPbi::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabexport', 'Dtks\VervalPbi::tabexport', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_pbi_verivali', 'Dtks\VervalPbi::tabel_pbi_verivali', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltPbi', 'Dtks\VervalPbi::hapus', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editpbi', 'Dtks\VervalPbi::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updatepbi', 'Dtks\VervalPbi::ajax_update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('addpbi', 'Dtks\VervalPbi::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('importPbi', 'Dtks\VervalPbi::importExcel', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// PBI INACTIVE
$routes->get('pbi_nonaktif', 'Dtks\Pbi\Inactive::pbi_nonaktif', ['filter' => ['authfilterdtks', 'menufilterdtks']]);
$routes->post('tb_pbi_nonaktif', 'Dtks\Pbi\Inactive::tb_pbi_nonaktif', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('tmbNA', 'Dtks\Pbi\Inactive::formTmbNA', ['filter' => 'authfilterdtks']);
$routes->post('get_data_pbi', 'Dtks\Pbi\Inactive::get_data_pbi', ['filter' => 'authfilterdtks']);
$routes->resource('api_pbi', ['controller' => 'Api\Dtks_Pbi', 'filter' => 'menufilterdtks']);
$routes->post('saveInactive', 'Dtks\Pbi\Inactive::saveInactive', ['filter' => 'authfilterdtks']);
$routes->post('editInactive', 'Dtks\Pbi\Inactive::formEditInactive', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateInactive', 'Dtks\Pbi\Inactive::updateInactive', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltInactive', 'Dtks\Pbi\Inactive::hapus', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// REAKTIVASI PBI
$routes->group('pbi', ['filter' => ['authfilterdtks', 'globalview', 'menufilterdtks']], function ($routes) {

	$routes->group('reaktivasi', function ($routes) {

		// Halaman utama (Daftar Pengajuan)
		$routes->GET('/', 'Dtks\Pbi\Reaktivasi::index');

		// 🔥 TAMBAHKAN INI
		$routes->match(['GET', 'POST'], 'tabel', 'Dtks\Pbi\Reaktivasi::tabel_data');

		// Form Ajukan
		$routes->POST('ajukan', 'Dtks\Pbi\Reaktivasi::ajukan');

		// Simpan draft
		$routes->post('store', 'Dtks\Pbi\Reaktivasi::store');

		// DataTables
		$routes->post('tabel', 'Dtks\Pbi\Reaktivasi::tabel_data');

		// Summary cards
		$routes->GET('summary', 'Dtks\Pbi\Reaktivasi::summary');

		// Lifecycle actions
		$routes->post('submit/(:num)', 'Dtks\Pbi\Reaktivasi::submit/$1');
		$routes->post('verify/(:num)', 'Dtks\Pbi\Reaktivasi::verify/$1');
		$routes->post('approve/(:num)', 'Dtks\Pbi\Reaktivasi::approve/$1');
		$routes->post('reject/(:num)', 'Dtks\Pbi\Reaktivasi::reject/$1');
		$routes->post('kirim-siks/(:num)', 'Dtks\Pbi\Reaktivasi::kirimSiks/$1');

		// Riwayat
		$routes->GET('riwayat', 'Dtks\Pbi\Reaktivasi::riwayat');

		// Upload Excel Verivali
		$routes->post('upload-excel', 'Dtks\Pbi\Reaktivasi::uploadExcel');

		// Detail view
		$routes->get('detail/(:num)', 'Dtks\Pbi\Reaktivasi::detail/$1');

		// Dropdown untuk filter status di DataTables
		$routes->get('dropdown-status', 'Dtks\Pbi\Reaktivasi::dropdownStatus');

		$routes->post('verifikasi/(:num)', 'Dtks\Pbi\Reaktivasi::verifikasi/$1');
		$routes->post('setujui/(:num)', 'Dtks\Pbi\Reaktivasi::setujui/$1');
		$routes->post('tolak/(:num)', 'Dtks\Pbi\Reaktivasi::tolak/$1');
	});
});

// USULAN
$routes->get('usulan', 'Dtks\Usulan22::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tmbUsul', 'Dtks\Usulan22::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->GET('tambah', 'Dtks\Usulan22::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->POST('get_data_penduduk', 'Dtks\Usulan22::get_data_penduduk', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->resource('api_usulan', ['controller' => 'Api\Dtks_Usulan', 'filter' => 'menufilterdtks']);
$routes->match(['GET', 'POST'], 'editUsulan', 'Dtks\Usulan22::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('viewUsulan', 'Dtks\Usulan22::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateUsulan', 'Dtks\Usulan22::update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltUsul', 'Dtks\Usulan22::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->POST('tabel_data', 'Dtks\Usulan22::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->POST('tabel_padan', 'Dtks\Usulan22::tabel_padan', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('expUsulan', 'Dtks\Usulan22::export', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['GET', 'POST'], 'exportBa', 'Dtks\Usulan22::exportBa', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('import_csv', 'Dtks\Usulan22::import_csv', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('importCsvToDb', 'Dtks\Usulan22::importCsvToDb', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tb_csv', 'Dtks\Usulan22::tbCsv', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('downIden', 'Dtks\Usulan22::downIden');

// PPKS
$routes->get('ppks', 'Dtks\Ppks::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tmbUsulPpks', 'Dtks\Ppks::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('tambahPpks', 'Dtks\Ppks::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['GET', 'POST'], 'editPpks', 'Dtks\Ppks::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('viewPpks', 'Dtks\Ppks::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updatePpks', 'Dtks\Ppks::update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltPpks', 'Dtks\Ppks::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_ppks', 'Dtks\Ppks::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_padan_ppks', 'Dtks\Ppks::tabel_padan', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('exportPpks', 'Dtks\Ppks::export', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('exportPpks1', 'Dtks\Ppks::export1', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// FAMANTAMA
$routes->get('famantama', 'Dtks\Famantama::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('getDiagram', 'Dtks\Famantama::getDiagram', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('tambahFamantama', 'Dtks\Famantama::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('getRespondenData', 'Dtks\Famantama::getRespondenData', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tmbFamantama', 'Dtks\Famantama::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['GET', 'POST'], 'editFamantama', 'Dtks\Famantama::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltFamantama', 'Dtks\Famantama::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updFamantama', 'Dtks\Famantama::update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('viewFamantama', 'Dtks\Famantama::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tbFamantama', 'Dtks\Famantama::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tbPadanFamantama', 'Dtks\Famantama::tabel_padan', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('exportFamantama', 'Dtks\Famantama::export', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('exportFamantama1', 'Dtks\Famantama::export1', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// WILAYAH
$routes->post('action', 'Dtks\Wil::action', ['filter' => 'authfilterdtks']);

// KETERANGAN VERVAL PBI
$routes->get('ketVervalPbi', 'Dtks\VervalPbi::ketVervalPbi', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('formTambahKetVvPbi', 'Dtks\VervalPbi::formTambahKetVvPbi', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tmbKetVvPbi', 'Dtks\VervalPbi::tmbKetVvPbi', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('hapusKetVvPbi', 'Dtks\VervalPbi::hapusKetVvPbi', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('viewKetVvPbi', 'Dtks\VervalPbi::viewKetVvPbi', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updKetVvPbi', 'Dtks\VervalPbi::updKetVvPbi', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// VERVAL DTKS-ANOMALI
$routes->get('verivaliAnomali', 'Dtks\VerivaliAnomali::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('imporAnomali', 'Dtks\VerivaliAnomali::simpanExcel', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabelAnomali', 'Dtks\VerivaliAnomali::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabelAnomali2', 'Dtks\VerivaliAnomali::tabel_data2', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabelAnomali3', 'Dtks\VerivaliAnomali::tabel_data3', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editAnomali', 'Dtks\VerivaliAnomali::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editAnomali2', 'Dtks\VerivaliAnomali::formedit2', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editAnomali3', 'Dtks\VerivaliAnomali::formedit3', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateAnomali', 'Dtks\VerivaliAnomali::ajax_update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateAnomali2', 'Dtks\VerivaliAnomali::ajax_update2', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateAnomali3', 'Dtks\VerivaliAnomali::ajax_update3', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// VERIVALI GEOTAGGING
$routes->get('geotagging', 'Dtks\Geotagging::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabelGeo', 'Dtks\Geotagging::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabelGeo2', 'Dtks\Geotagging::tabel_data2', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('imporVerivaliGeo', 'Dtks\Geotagging::simpanExcel', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editGeo', 'Dtks\Geotagging::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateGeo', 'Dtks\Geotagging::ajax_update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('modGambar', 'Dtks\Geotagging::modGambar', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['GET', 'POST'], 'exportBaPdtt', 'Dtks\Geotagging::exportBA', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('exportDataPdtt', 'Dtks\Geotagging::exportExcel', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// OPERATOR KIP
$routes->get('/operatorsch', 'Dtks\Datakip\DataKip::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('datakip\DataKip', 'Dtks\Datakip\DataKip::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_kip', 'Dtks\Datakip\DataKip::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('formTmbKip', 'Dtks\Datakip\DataKip::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('TmbKip', 'Dtks\Datakip\DataKip::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltKip', 'Dtks\Datakip\DataKip::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editKip', 'Dtks\Datakip\DataKip::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateKip', 'Dtks\Datakip\DataKip::update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('non-kip', 'Dtks\Datakip\NonKIP::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('formTmbNonKip', 'Dtks\Datakip\NonKIP::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_nonkip', 'Dtks\Datakip\NonKIP::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tmbNonKip', 'Dtks\Datakip\NonKIP::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editNonKip', 'Dtks\Datakip\NonKIP::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('upNonKip', 'Dtks\Datakip\NonKIP::update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltNonKip', 'Dtks\Datakip\NonKIP::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('exportNonKip', 'Dtks\Datakip\NonKIP::export', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// OPERATOR BPNT
$routes->get('bpnt', 'Dtks\BpntGanti::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_bpnt', 'Dtks\BpntGanti::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('formTmbBpnt', 'Dtks\BpntGanti::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('TmbBpnt', 'Dtks\BpntGanti::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltBpnt', 'Dtks\BpntGanti::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editBpnt', 'Dtks\BpntGanti::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateBpnt', 'Dtks\BpntGanti::update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// DAFTAR KELUARGA MISKIN
$routes->get('dkm', 'Dtks\Dkm\Kemis::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tb_dkm', 'Dtks\Dkm\Kemis::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('detailDkm', 'Dtks\Dkm\Kemis::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('tmbKemis', 'Dtks\Dkm\Kemis::formTmb', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('simpanDkm', 'Dtks\Dkm\Kemis::simpan_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateDkm', 'Dtks\Dkm\Kemis::update_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltDkm', 'Dtks\Dkm\Kemis::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['GET', 'POST'], 'exportDkm', 'Dtks\Dkm\Kemis::exportBA', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// Setting General
$routes->get('chart_desa', 'Dtks\VeriVali09::chartDesa', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('wilayah', 'Dtks\Wil::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('wil/listData', 'Dtks\Wil::listData', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('getKab', 'Dtks\Wil::getKab', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('getKec', 'Dtks\Wil::getKec', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('getDesa', 'Dtks\Wil::getDesa', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('load_data', 'Dtks\VeriVali09::load_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editVerivali', 'Dtks\VeriVali09::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('ajax_update', 'Dtks\VeriVali09::ajax_update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('update_data', 'Dtks\VeriVali09::update_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('/verivali09/redaktirovat/(:num)', 'Dtks\VeriVali09::redaktirovat/$1', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// Setting Users / Hak Akses
$routes->get('users', 'Dtks\Users::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['GET', 'POST'], 'user_tambah', 'Dtks\Users::tambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['GET', 'POST'], 'update_status/(:num)/(:num)', 'Dtks\Users::update_status/$1/$2', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('hapus', 'Dtks\Users::hapus', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('formview', 'Dtks\Users::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateDataUser', 'Dtks\Users::updatedata', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('users/saveRole', 'Dtks\Users::saveRole', ['filter' => 'authfilterdtks']);
$routes->post('users/deleteRole', 'Dtks\Users::deleteRole', ['filter' => 'authfilterdtks']);

// setting web hak akses Super Admin
$routes->match(['GET', 'POST'], 'settings', 'Profil\Profil_Web::index', ['filter' => 'authfilterdtks']);
$routes->post('update_web_admin', 'Profil\Profil_Web::update_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('submit_web_lembaga', 'Profil\Profil_Web::submit_lembaga', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('update_web_lembaga', 'Profil\Profil_Web::update_lembaga', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('load_data_menu', 'Profil\Profil_Web::load_data_menu', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('insert_data_menu', 'Profil\Profil_Web::insert_data_menu', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('update_data_menu', 'Profil\Profil_Web::update_data_menu', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('delete_data_menu', 'Profil\Profil_Web::delete_data_menu', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('get_nama_menu', 'Profil\Profil_Web::get_nama_menu', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('submit_web_general', 'Profil\Profil_Web::submit_general', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('update_web_general', 'Profil\Profil_Web::update_general', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateBatch', 'Profil\Profil_Web::updateBatch', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateBatchGen', 'Profil\Profil_Web::updateBatchGen', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// profil user petugas entri, dan Admin Desa
$routes->match(['GET', 'POST'], 'profil_user', 'Profil\Profil_User::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('update_user', 'Profil\Profil_User::update_user', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('submit_lembaga', 'Profil\Profil_User::submit_lembaga', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('update_lembaga', 'Profil\Profil_User::update_lembaga', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

/**
 * =========================================================================
 * ⚙️ SETTINGS & APLIKASI
 * =========================================================================
 */
// 🛡️ Filter grup hanya mewajibkan LOGIN (authfilterdtks)
$routes->group('settings', [
	'namespace' => 'App\Controllers\Settings',
	'filter'    => 'authfilterdtks'
], function ($routes) {

	// 🛡️ Halaman Utama: Ekstra perlindungan HAK AKSES MENU (menufilterdtks)
	$routes->get('menu', 'MenuApp::index', ['filter' => 'menufilterdtks']);

	// 🔓 Ubah ke GET agar bebas dari blokir CSRF saat memuat data
	$routes->get('load_data_menu', 'MenuApp::load_data_menu');

	$routes->post('insert_data_menu', 'MenuApp::insert_data_menu');
	$routes->post('update_data_menu', 'MenuApp::update_data_menu');
	$routes->post('delete_data_menu', 'MenuApp::delete_data_menu');
	$routes->post('get_nama_menu', 'MenuApp::get_nama_menu');
});

// DOKUMENTASI KEGIATAN PETUGAS ENTRI
$routes->get('dokumentasi/kegiatan', 'Dokumentasi::get_kegiatan', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dokumentasi/upload', 'Dokumentasi::upload', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('dokumentasi/timeline', 'Dokumentasi::timeline', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
