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
use App\Filters\CsrfAudit;
use App\Filters\AttachCsrfHeader;

class Filters extends BaseConfig
{
	public $aliases = [
		'csrf'            => CSRF::class,
		'toolbar'         => DebugToolbar::class,
		'honeypot'        => Honeypot::class,
		'cors'            => \App\Filters\Cors::class,
		'authfilterdtks'  => AuthFilterDtks::class,
		'noauthfilterdtks' => NoauthFilterDtks::class,
		'adminFilter'     => AdminFilter::class,
		'schfilterkip'    => SchFilterKip::class,
		'timeFilter'      => TimeFilter::class,
		'menufilterdtks'  => MenuFilterDtks::class,
		'globalview'      => \App\Filters\GlobalViewDataFilter::class,
		'csrf_audit'      => CsrfAudit::class,
		'attach_csrf_hdr' => AttachCsrfHeader::class,
	];

	public $globals = [
		'before' => [
			// NOTE:
			// CSRF intentionally not applied globally here.
			// Use 'csrf' in $filters below for specific routes if you prefer route-level protection.
		],
		'after'  => [
			// attach CSRF header to every response (if CSRF enabled)
			'attach_csrf_hdr'
		],
	];

	// don't force CSRF on all POST methods
	// public $methods = [ 'POST' => ['csrf'] ];

	public $filters = [

		// Keep adminFilter and others as you had them (partial excerpt)
		'adminFilter' => [
			'before' => [
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
				'dtsen-monitoring',
				'dtsen-monitoring/*',
				'admin/articles',
			]
		],

		// CSRF: route-level protection
		'csrf' => [
			'before' => [
				// ROUTE LIST: sensitive endpoints that should keep CSRF ON
				'login',
				'register',
				'lupa-password',
				'requestReset',
				'reset-password',
				'admin/articles/store',
				'admin/articles/update/*',
				'admin/articles/delete/*',
				'pengaturan_wa/save_api',
				'pengaturan_wa/save_template',
				'pengaturan_wa/save_fonnte',
				'settings',
				'update_web_admin',
				'update_web_lembaga',
				// tambahkan route lain yang benar-benar perlu proteksi form tradisional
			]
		],

		// Audit filter: catat POST tanpa token atau kegagalan
		'csrf_audit' => [
			'before' => [
				// opsional: aktifkan audit di seluruh POST untuk startup (bisa dihapus setelah stabil)
				// gunakan pola di bawah jika ingin aktifkan pencatatan untuk semua POST routes:
				// '*' atau specific groups
			]
		],
	];
}
