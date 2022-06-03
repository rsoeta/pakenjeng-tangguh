<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class ShdkModel extends Model
{

	protected $table = 'tb_shdk';

	protected $primaryKey = 'id';

	protected $allowedFields = ['jenis_shdk'];
}
