<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use App\Filters\AuthFilterDtks;
use App\Filters\NoauthFilterDtks;
use App\Filters\AdminFilter;
use App\Filters\SchFilterKip;
use App\Filters\TimeFilter;
use App\Filters\MenuFilterDtks;

class Filters extends BaseConfig
{
	public $aliases = [
		'csrf'     => CSRF::class,
		'toolbar'  => DebugToolbar::class,
		'honeypot' => Honeypot::class,
		'cors'     => \App\Filters\Cors::class,
		"authfilterdtks" => AuthFilterDtks::class,
		"noauthfilterdtks" => NoauthFilterDtks::class,
		'adminFilter' => AdminFilter::class,
		'schfilterkip' => SchFilterKip::class,
		'timeFilter' => TimeFilter::class,
		'menufilterdtks' => MenuFilterDtks::class,
		'globalview' => \App\Filters\GlobalViewDataFilter::class,
	];

	public $globals = [
		'before' => [
			'cors',
		],
		'after'  => [
			// empty
		],
	];

	public $methods = [];

	public $filters = [

		/**
		 * ⚠ ADMIN FILTER – hanya untuk modul yang memang harus admin
		 * Tidak menimpa seluruh DTSEN, dan tidak memblok pemeriksaan dtsen.
		 */
		'adminFilter' => [
			'before' => [

				// ==============================
				// Tetap dipertahankan seperti asli
				// ==============================
				'verivali',
				'verivali/*',
				'users',
				'users/*',
				'exportExcel',
				'tabexport',
				'update_status/*',
				'hapus',
				'formview',
				'updateDataUser',
				'expKip',

				// ==============================
				// FIXED:
				// Hapus 'dtsen/pemeriksaan' dari adminFilter
				// karena modul ini dikendalikan MenuFilterDtks,
				// supaya operator (role 3) tetap bisa masuk
				// sementara viewer (role >3) ditolak.
				// ==============================
				// 'dtsen/pemeriksaan',  <-- DIHAPUS (fix)

				// ==============================
				// Modul DTSEN yang memang admin only
				// ==============================
				'dtsen-monitoring',
				'dtsen-monitoring/*',
				'admin/articles',
				'admin/articles/*',
				// 'dtsen-reminder',
				// 'dtsen-reminder/*',
				// 'pengaturan_wa',
				// 'laporan-dtsen',
				// 'laporan-dtsen/*',
				// 'dtsen/reminder-monitor',
				// 'dtsen/reminder-monitor/*',
				// 'admin/migrate',
				// 'admin/download-db',
			]
		],

		// (schfilterkip tetap dibiarkan disabled sesuai file aslinya)
	];
}
