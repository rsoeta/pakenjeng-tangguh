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
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override(function () {
	return view('maintenance2');
});
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
// $routes->get('/', 'Pages::home');

// AUTH
// $routes->match(['get', 'post'], 'lockscreen', 'Lockscreen::index', ["filter" => "authfilterdtks"]);
$routes->match(['get', 'post'], 'login', 'Dtks\Auth::login', ["filter" => "noauthfilterdtks"]);
// $routes->match(['get', 'post'], 'register', 'Dtks\Auth::register', ["filter" => "noauthfilterdtks"]);
$routes->match(['get', 'post'], 'register', 'Dtks\Auth::regOpSek', ["filter" => "noauthfilterdtks"]);
$routes->get('/', 'Dtks\Pages::home', ["filter" => "noauthfilterdtks"]);
// $routes->get('/', 'Landing::index', ["filter" => "noauthfilterdtks"]);
$routes->get('dashboard', 'Dtks\Pages::home', ["filter" => "noauthfilterdtks"]);
$routes->get('pages', 'Dtks\Pages::index', ["filter" => "authfilterdtks"]);

// BNBA
$routes->get('bnba', 'Dtks\Bnba::index', ['filter' => 'authfilterdtks']);
$routes->post('tabel_bnba', 'Dtks\Bnba::tabel_data', ['filter' => 'authfilterdtks']);
$routes->post('detailBnba', 'Dtks\Bnba::formedit', ['filter' => 'authfilterdtks']);
// $routes->post('editBnba', 'Dtks\Bnba::formedit', ['filter' => 'authfilterdtks']);
$routes->post('updatebnba', 'Dtks\Bnba::ajax_update', ['filter' => 'authfilterdtks']);

// VERVAL PBI
$routes->get('verval', 'Dtks\VeriVali09::index', ["filter" => "authfilterdtks"]);
$routes->get('verivalipbi', 'Dtks\VervalPbi::index', ["filter" => "authfilterdtks"]);
$routes->post('tabel_pbi', 'Dtks\VervalPbi::tabel_data', ["filter" => "authfilterdtks"]);
$routes->get('exportExcel', 'Dtks\VervalPbi::excelpage', ["filter" => "authfilterdtks"]);
$routes->get('tmbData', 'Dtks\VervalPbi::formtambah', ["filter" => "authfilterdtks"]);
$routes->post('tabexport', 'Dtks\VervalPbi::tabexport', ['filter' => 'authfilterdtks']);
$routes->post('tabel_pbi_verivali', 'Dtks\VervalPbi::tabel_pbi_verivali', ["filter" => "authfilterdtks"]);
$routes->post('editpbi', 'Dtks\VervalPbi::formedit', ["filter" => "authfilterdtks"]);
$routes->post('updatepbi', 'Dtks\VervalPbi::ajax_update', ["filter" => "authfilterdtks"]);
$routes->post('addpbi', 'Dtks\VervalPbi::save', ["filter" => "authfilterdtks"]);
// $routes->get('usulan', 'Lockscreen::maintenance', ["filter" => "authfilterdtks"]);
// $routes->post('tmbUsul', 'Lockscreen::maintenance', ["filter" => "authfilterdtks"]);
// $routes->get('tambah', 'Lockscreen::maintenance', ["filter" => "authfilterdtks"]);
// $routes->post('editUsulan', 'Lockscreen::maintenance', ["filter" => "authfilterdtks"]);
// $routes->post('updateUsulan', 'Lockscreen::maintenance', ["filter" => "authfilterdtks"]);
// $routes->post('dltUsul', 'Lockscreen::maintenance', ["filter" => "authfilterdtks"]);
// $routes->post('tabel_data', 'Lockscreen::maintenance', ["filter" => "authfilterdtks"]);
// $routes->get('expUsulan', 'Lockscreen::maintenance', ["filter" => "authfilterdtks"]);

// USULAN
$routes->get('usulan', 'Dtks\Usulan22::index', ["filter" => "authfilterdtks"]);
$routes->post('tmbUsul', 'Dtks\Usulan22::save', ["filter" => "authfilterdtks"]);
$routes->get('tambah', 'Dtks\Usulan22::formtambah', ["filter" => "authfilterdtks"]);
$routes->post('editUsulan', 'Dtks\Usulan22::formedit', ["filter" => "authfilterdtks"]);
$routes->post('updateUsulan', 'Dtks\Usulan22::update', ["filter" => "authfilterdtks"]);
$routes->post('dltUsul', 'Dtks\Usulan22::delete', ["filter" => "authfilterdtks"]);
$routes->post('tabel_data', 'Dtks\Usulan22::tabel_data', ["filter" => "authfilterdtks"]);
$routes->post('expUsulan', 'Dtks\Usulan22::export', ["filter" => "authfilterdtks"]);
$routes->match(['get', 'post'], 'exportBa', 'Dtks\Usulan22::exportBa', ["filter" => "authfilterdtks"]);
$routes->get('import_csv', 'Dtks\Usulan22::import_csv', ["filter" => "authfilterdtks"]);
$routes->post('importCsvToDb', 'Dtks\Usulan22::importCsvToDb', ["filter" => "authfilterdtks"]);
$routes->post('tb_csv', 'Dtks\Usulan22::tbCsv', ["filter" => "authfilterdtks"]);


// WILAYAH
$routes->post('action', 'Dtks\Wil::action', ["filter" => "authfilterdtks"]);

// KETERANGAN VERVAL PBI
$routes->get('ketVervalPbi', 'Dtks\VervalPbi::ketVervalPbi', ["filter" => "authfilterdtks"]);
$routes->post('formTambahKetVvPbi', 'Dtks\VervalPbi::formTambahKetVvPbi', ["filter" => "authfilterdtks"]);
$routes->post('tmbKetVvPbi', 'Dtks\VervalPbi::tmbKetVvPbi', ["filter" => "authfilterdtks"]);
$routes->post('hapusKetVvPbi', 'Dtks\VervalPbi::hapusKetVvPbi', ["filter" => "authfilterdtks"]);
$routes->post('viewKetVvPbi', 'Dtks\VervalPbi::viewKetVvPbi', ["filter" => "authfilterdtks"]);
$routes->post('updKetVvPbi', 'Dtks\VervalPbi::updKetVvPbi', ["filter" => "authfilterdtks"]);

// OPERATOR KIP
$routes->get('/operatorsch', 'Dtks\DataKip::index', ["filter" => "authfilterdtks"]);
$routes->get('datakip', 'Dtks\DataKip::index', ["filter" => "authfilterdtks"]);
$routes->post('tabel_kip', 'Dtks\DataKip::tabel_data', ["filter" => "authfilterdtks"]);
$routes->get('formTmbKip', 'Dtks\DataKip::formtambah', ["filter" => "authfilterdtks"]);
$routes->post('TmbKip', 'Dtks\DataKip::save', ["filter" => "authfilterdtks"]);
$routes->post('dltKip', 'Dtks\DataKip::delete', ["filter" => "authfilterdtks"]);
$routes->post('editKip', 'Dtks\DataKip::formedit', ["filter" => "authfilterdtks"]);
$routes->post('updateKip', 'Dtks\DataKip::update', ["filter" => "authfilterdtks"]);

// OPERATOR BPNT
$routes->get('bpnt', 'Dtks\BpntGanti::index', ["filter" => "authfilterdtks"]);
$routes->post('tabel_bpnt', 'Dtks\BpntGanti::tabel_data', ["filter" => "authfilterdtks"]);
$routes->get('formTmbBpnt', 'Dtks\BpntGanti::formtambah', ["filter" => "authfilterdtks"]);
$routes->post('TmbBpnt', 'Dtks\BpntGanti::save', ["filter" => "authfilterdtks"]);
$routes->post('dltBpnt', 'Dtks\BpntGanti::delete', ["filter" => "authfilterdtks"]);
$routes->post('editBpnt', 'Dtks\BpntGanti::formedit', ["filter" => "authfilterdtks"]);
$routes->post('updateBpnt', 'Dtks\BpntGanti::update', ["filter" => "authfilterdtks"]);

// Setting General
$routes->get('chart_desa', 'Dtks\VeriVali09::chartDesa', ["filter" => "authfilterdtks"]);
$routes->get('wilayah', 'Dtks\Wil::index', ["filter" => "authfilterdtks"]);
$routes->post('wil/listData', 'Dtks\Wil::listData', ["filter" => "authfilterdtks"]);
$routes->post('getKab', 'Dtks\Wil::getKab', ["filter" => "authfilterdtks"]);
$routes->post('getKec', 'Dtks\Wil::getKec', ["filter" => "authfilterdtks"]);
$routes->post('getDesa', 'Dtks\Wil::getDesa', ["filter" => "authfilterdtks"]);
$routes->get('load_data', 'Dtks\VeriVali09::load_data', ["filter" => "authfilterdtks"]);
$routes->post('editVerivali', 'Dtks\VeriVali09::formedit', ["filter" => "authfilterdtks"]);
$routes->post('ajax_update', 'Dtks\VeriVali09::ajax_update', ["filter" => "authfilterdtks"]);
$routes->post('update_data', 'Dtks\VeriVali09::update_data', ["filter" => "authfilterdtks"]);
$routes->get('/verivali09/redaktirovat/(:num)', 'Dtks\VeriVali09::redaktirovat/$1', ["filter" => "authfilterdtks"]);

// Setting Users / Hak Akses
$routes->get('users', 'Dtks\Users::index', ["filter" => "authfilterdtks"]);
$routes->match(['get', 'post'], 'user_tambah', 'Dtks\Users::tambah', ["filter" => "authfilterdtks"]);
$routes->match(['get', 'post'], 'update_status/(:num)/(:num)', 'Dtks\Users::update_status/$1/$2', ['filter' => 'authfilterdtks']);
$routes->post('hapus', 'Dtks\Users::hapus', ['filter' => 'authfilterdtks']);
$routes->post('formview', 'Dtks\Users::formview', ['filter' => 'authfilterdtks']);
$routes->post('updateDataUser', 'Dtks\Users::updatedata', ['filter' => 'authfilterdtks']);

// profil
$routes->match(['get', 'post'], 'profil_user', 'Profil\Profil_User::index', ['filter' => 'authfilterdtks']);
$routes->post('update_user', 'Profil\Profil_User::update_user', ['filter' => 'authfilterdtks']);
$routes->post('submit_lembaga', 'Profil\Profil_User::submit_lembaga', ['filter' => 'authfilterdtks']);
$routes->post('update_lembaga', 'Profil\Profil_User::update_lembaga', ['filter' => 'authfilterdtks']);

$routes->get('logout', 'Dtks\Auth::logout');
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
