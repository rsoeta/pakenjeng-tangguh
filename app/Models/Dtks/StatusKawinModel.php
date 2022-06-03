<?php

//CountryModel.php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class StatusKawinModel extends Model
{

	protected $table = 'tb_status_kawin';

	protected $primaryKey = 'idStatus';

	protected $allowedFields = ['StatusKawin'];
}
