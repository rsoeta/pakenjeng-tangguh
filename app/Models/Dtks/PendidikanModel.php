<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class PendidikanModel extends Model
{
	protected $table = 'pendidikan_kk';
	protected $primaryKey = 'pk_id';
	protected $allowedFields = ['pk_nama'];
}
