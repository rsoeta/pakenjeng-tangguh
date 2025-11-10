<?php

namespace App\Models;

use CodeIgniter\Model;

class DtsenUsulanModel extends Model
{
    protected $table            = 'dtsen_usulan';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'usulan_no',
        'jenis',
        'status',
        'dtsen_kk_id',
        'no_kk_target',
        'created_by',
        'assigned_to',
        'payload',
        'summary',
        'created_at',
        'updated_at',
        'verified_at',
        'applied_at'
    ];
    protected $useTimestamps    = false;
    protected $returnType       = 'array';

    /**
     * Simpan payload baru / update existing
     */
    public function updatePayload(int $id, array $dataPart)
    {
        $usulan = $this->find($id);
        if (!$usulan) return false;

        $payload = json_decode($usulan['payload'] ?? '{}', true);
        $payload = array_merge($payload, $dataPart);

        return $this->update($id, [
            'payload' => json_encode($payload),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Buat usulan baru (draft)
     */
    public function createDraft(int $created_by, string $jenis = 'pembaruan')
    {
        helper('dtsen');
        $no = generateUsulanNo();

        $data = [
            'usulan_no'   => $no,
            'jenis'       => $jenis,
            'status'      => 'draft',
            'payload'     => json_encode([]),
            'created_by'  => $created_by,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $this->insert($data);
        return $this->getInsertID();
    }
}
