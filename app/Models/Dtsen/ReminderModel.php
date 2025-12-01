<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class ReminderModel extends Model
{
    protected $table = 'dtsen_kk_reminder_log';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kk_id',
        'admin_id',
        'due_date',
        'status',
        'sent_at'
    ];

    // Ambil semua reminder yang sudah jatuh tempo
    public function getDueReminders()
    {
        return $this->where('status', 'pending')
            ->where('due_date <=', date('Y-m-d H:i:s'))
            ->findAll();
    }

    public function markSent($id)
    {
        return $this->update($id, [
            'status'  => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ]);
    }
}
