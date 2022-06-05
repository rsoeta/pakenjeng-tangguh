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
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Days;

class Filters extends BaseConfig
{
	/**
	 * Configures aliases for Filter classes to
	 * make reading things nicer and simpler.
	 *
	 * @var array
	 */
	public $aliases = [
		'csrf'     => CSRF::class,
		'toolbar'  => DebugToolbar::class,
		'honeypot' => Honeypot::class,
		"authfilterdtks" => AuthFilterDtks::class,
		"noauthfilterdtks" => NoauthFilterDtks::class,
		'adminFilter' => AdminFilter::class,
		'schfilterkip' => SchFilterKip::class,
		'timeFilter' => TimeFilter::class,
	];

	/**
	 * List of filter aliases that are always
	 * applied before and after every request.
	 *
	 * @var array
	 */
	public $globals = [
		'before' => [
			'honeypot',
			// 'csrf',
		],
		'after'  => [
			'toolbar',
			// 'honeypot',
		],
	];

	/**
	 * List of filter aliases that works on a
	 * particular HTTP method (GET, POST, etc.).
	 *
	 * Example:
	 * 'post' => ['csrf', 'throttle']
	 *
	 * @var array
	 */
	public $methods = [];

	/**
	 * List of filter aliases that should run on any
	 * before or after URI patterns.
	 *
	 * Example:
	 * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
	 *
	 * @var array
	 */

	public $filters = [
		'adminFilter' => [
			'before' => [
				// 'usulan', 'usulan/*',
				// 'dtks', 'dtks/*',
				// 'tmbUsul',
				'tambah',
				'verivali', 'verivali/*',
				// 'verivalipbi', 'verivalipbi/*',
				'users', 'users/*',
				// 'expUsulan',
				'exportExcel', 'tabexport', 'update_status/*', 'hapus', 'formview', 'updateDataUser', 'expKip'
			]
		],
		// 'schfilterkip' => [
		// 	'before' => [
		// 		'usulan', 'usulan/*',
		// 		'dtks', 'dtks/*',
		// 		'verivali', 'verivali/*', 'verivalipbi', 'verivalipbi/*',
		// 		'users', 'users/*',
		// 		'expUsulan',
		// 		'exportExcel', 'tabexport', 'update_status/*', 'hapus', 'formview', 'updateDataUser', 'expKip'
		// 	]
		// ],
	];
}
