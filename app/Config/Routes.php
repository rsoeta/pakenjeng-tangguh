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
// $routes->match(['get', 'post'], 'lockscreen', 'Lockscreen::index', ['filter' => 'authfilterdtks']);
$routes->match(['get', 'post'], 'login', 'Dtks\Auth::login', ['filter' => 'noauthfilterdtks']);
// $routes->match(['get', 'post'], 'register', 'Dtks\Auth::register', ['filter' => 'noauthfilterdtks']);
$routes->match(['get', 'post'], 'register', 'Dtks\Auth::regOpSek', ['filter' => 'noauthfilterdtks']);
// $routes->get('/', 'Dtks\Pages::home', ['filter' => 'noauthfilterdtks']);
$routes->get('/', 'Landing::index');
$routes->post('cek_usulan', 'Landing::cek_usulan');

$routes->get('dashboard', 'Dtks\Pages::home', ['filter' => 'noauthfilterdtks']);
$routes->get('pages', 'Dtks\Pages::index', ['filter' => 'authfilterdtks']);

// CHATTING
$routes->match(['get', 'post'], 'chatt', 'Chat::index', ['filter' => 'authfilterdtks']);
$routes->get('getMsg', 'Chat::getMsg', ['filter' => 'authfilterdtks']);
$routes->get('getUserLogged', 'Chat::getUserLogged', ['filter' => 'authfilterdtks']);
$routes->post('updateLastActivity', 'Chat::updateLastActivity', ['filter' => 'authfilterdtks']);

// BNBA
$routes->get('bnba', 'Dtks\Bnba::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_bnba', 'Dtks\Bnba::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('detailBnba', 'Dtks\Bnba::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

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
$routes->get('pbi_nonaktif', 'Dtks\Pbi\Inactive::pbi_nonaktif', ['filter' => 'authfilterdtks']);
$routes->post('tb_pbi_nonaktif', 'Dtks\Pbi\Inactive::tb_pbi_nonaktif', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('tmbNA', 'Dtks\Pbi\Inactive::formTmbNA', ['filter' => 'authfilterdtks']);
$routes->post('get_data_pbi', 'Dtks\Pbi\Inactive::get_data_pbi', ['filter' => 'authfilterdtks']);
$routes->resource('api_pbi', ['controller' => 'Api\Dtks_Pbi', 'filter' => 'menufilterdtks']);
$routes->post('saveInactive', 'Dtks\Pbi\Inactive::saveInactive', ['filter' => 'authfilterdtks']);
$routes->post('editInactive', 'Dtks\Pbi\Inactive::formEditInactive', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateInactive', 'Dtks\Pbi\Inactive::updateInactive', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltInactive', 'Dtks\Pbi\Inactive::hapus', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// USULAN
$routes->get('usulan', 'Dtks\Usulan22::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tmbUsul', 'Dtks\Usulan22::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('tambah', 'Dtks\Usulan22::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('get_data_penduduk', 'Dtks\Usulan22::get_data_penduduk', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->resource('api_usulan', ['controller' => 'Api\Dtks_Usulan', 'filter' => 'menufilterdtks']);
$routes->match(['get', 'post'], 'editUsulan', 'Dtks\Usulan22::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('viewUsulan', 'Dtks\Usulan22::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateUsulan', 'Dtks\Usulan22::update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltUsul', 'Dtks\Usulan22::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_data', 'Dtks\Usulan22::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_padan', 'Dtks\Usulan22::tabel_padan', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('expUsulan', 'Dtks\Usulan22::export', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['get', 'post'], 'exportBa', 'Dtks\Usulan22::exportBa', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('import_csv', 'Dtks\Usulan22::import_csv', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('importCsvToDb', 'Dtks\Usulan22::importCsvToDb', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tb_csv', 'Dtks\Usulan22::tbCsv', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('downIden', 'Dtks\Usulan22::downIden');

// PPKS
$routes->get('ppks', 'Dtks\Ppks::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tmbUsulPpks', 'Dtks\Ppks::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('tambahPpks', 'Dtks\Ppks::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
// $routes->post('get_data_penduduk', 'Dtks\Ppks::get_data_penduduk', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
// $routes->resource('api_usulan', ['controller' => 'Api\Dtks_Usulan', 'filter' => 'menufilterdtks']);
$routes->match(['get', 'post'], 'editPpks', 'Dtks\Ppks::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('viewPpks', 'Dtks\Ppks::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updatePpks', 'Dtks\Ppks::update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltPpks', 'Dtks\Ppks::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_ppks', 'Dtks\Ppks::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_padan_ppks', 'Dtks\Ppks::tabel_padan', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('exportPpks', 'Dtks\Ppks::export', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
// $routes->match(['get', 'post'], 'exportBa', 'Dtks\Ppks::exportBa', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
// $routes->get('import_csv', 'Dtks\Ppks::import_csv', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
// $routes->post('importCsvToDb', 'Dtks\Ppks::importCsvToDb', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
// $routes->post('tb_csv', 'Dtks\Ppks::tbCsv', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
// $routes->post('downIden', 'Dtks\Ppks::downIden');


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
$routes->match(['get', 'post'], 'exportBaPdtt', 'Dtks\Geotagging::exportBA', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('exportDataPdtt', 'Dtks\Geotagging::exportExcel', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

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

// OPERATOR KIP
$routes->get('/operatorsch', 'Dtks\DataKip::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('datakip', 'Dtks\DataKip::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('tabel_kip', 'Dtks\DataKip::tabel_data', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->get('formTmbKip', 'Dtks\DataKip::formtambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('TmbKip', 'Dtks\DataKip::save', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('dltKip', 'Dtks\DataKip::delete', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('editKip', 'Dtks\DataKip::formedit', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateKip', 'Dtks\DataKip::update', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

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
$routes->match(['get', 'post'], 'exportDkm', 'Dtks\Dkm\Kemis::exportBA', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);


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
$routes->match(['get', 'post'], 'user_tambah', 'Dtks\Users::tambah', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->match(['get', 'post'], 'update_status/(:num)/(:num)', 'Dtks\Users::update_status/$1/$2', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('hapus', 'Dtks\Users::hapus', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('formview', 'Dtks\Users::formview', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('updateDataUser', 'Dtks\Users::updatedata', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// profil
$routes->match(['get', 'post'], 'profil_user', 'Profil\Profil_User::index', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('update_user', 'Profil\Profil_User::update_user', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('submit_lembaga', 'Profil\Profil_User::submit_lembaga', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);
$routes->post('update_lembaga', 'Profil\Profil_User::update_lembaga', ['filter' => 'authfilterdtks', 'filter' => 'menufilterdtks']);

// setting web
$routes->match(['get', 'post'], 'settings', 'Profil\Profil_Web::index', ['filter' => 'authfilterdtks']);
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
