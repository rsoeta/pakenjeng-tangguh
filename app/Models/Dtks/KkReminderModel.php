<?php

namespace App\Models\Dtks;

use CodeIgniter\Model;

class KkReminderModel extends Model
{
    protected $table      = 'dtsen_kk_reminder_log';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kk_id',
        'admin_id',
        'due_date',
        'status',
        'sent_at'
    ];
}
