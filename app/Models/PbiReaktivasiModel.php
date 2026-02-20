<?php

namespace App\Models;

use CodeIgniter\Model;

class PbiReaktivasiModel extends Model
{
    public const STATUS_DRAFT = 0;
    public const STATUS_DIAJUKAN = 1;
    public const STATUS_DIVERIFIKASI = 2;
    public const STATUS_DISETUJUI = 3;
    public const STATUS_DITOLAK = 4;
    public const STATUS_DIAJUKAN_SIKS = 5;
    public const STATUS_DISETUJUI_KAB = 6;
    public const STATUS_DITOLAK_KAB = 7;

    protected $table = 'pbi_reaktivasi';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'nik',
        'nama_snapshot',
        'status_pbi_awal',
        'desil_snapshot',
        'alasan',
        'kondisi_mendesak',
        'surat_faskes',
        'status_pengajuan',
        'catatan_pentri',
        'catatan_desa',
        'tanggal_draft',
        'tanggal_diajukan',
        'tanggal_verifikasi',
        'tanggal_keputusan',
        'tanggal_kirim_siks',
        'tanggal_respon_kab',
        'created_by',
        'verified_by',
        'keputusan_by',
        'desa_id',
        'created_at',
        'updated_at',
    ];

    public function getStatusLabel(int $status): string
    {
        $labels = [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_DIAJUKAN => 'Diajukan',
            self::STATUS_DIVERIFIKASI => 'Diverifikasi',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_DIAJUKAN_SIKS => 'Diajukan SIKS',
            self::STATUS_DISETUJUI_KAB => 'Disetujui Kab',
            self::STATUS_DITOLAK_KAB => 'Ditolak Kab',
        ];

        return $labels[$status] ?? 'Status tidak dikenal';
    }
}
