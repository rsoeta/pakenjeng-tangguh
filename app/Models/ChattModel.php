<?php

namespace App\Models;

use CodeIgniter\Model;

class ChattModel extends Model
{
	protected $table                = 'tb_chatt';
	protected $primaryKey           = 'tc_id';
	protected $protectFields        = true;
	protected $allowedFields        = [
		'tc_id',
		'tc_user_id',
		'tc_fullname',
		'tc_message',
		'tc_date',
		'tc_image',
		'tc_status',
	];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';

	// Validation
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
}
