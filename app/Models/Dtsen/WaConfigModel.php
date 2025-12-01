<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class WaConfigModel extends Model
{
    protected $table = 'dtsen_wa_config';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'api_key',
        'device',
        'sender',
        'fonnte_token',
        'fonnte_sender',
        'fallback_enabled',
        'template_groundcheck',
        'reminder_default_months',
        'updated_at'
    ];



    public function getConfig($userId)
    {
        return $this->where('user_id', $userId)->first();
    }
}
