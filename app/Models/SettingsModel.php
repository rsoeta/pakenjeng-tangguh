<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = 'settings';
    protected $allowedFields = ['key_name', 'value'];
    public $timestamps = false;

    public function getSetting($key)
    {
        $row = $this->where('key_name', $key)->first();
        return $row ? $row['value'] : null;
    }
}
