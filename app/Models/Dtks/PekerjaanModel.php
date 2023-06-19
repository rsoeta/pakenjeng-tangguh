<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class PekerjaanModel extends Model
{
	protected $table = 'tb_penduduk_pekerjaan';
	protected $primaryKey = 'pk_id';
	protected $allowedFields = ['pk_nama', 'fpp_id'];
}
