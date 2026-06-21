<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class BanpangRejectModel extends Model
{
    protected $table            = 'dtsen_banpang_reject';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Semua 26 kolom Bulog + 4 kolom Sinden diizinkan untuk diisi
    protected $allowedFields    = [
        'nik',
        'no_kk',
        'nama',
        'foto_ktp',
        'foto_pbp',
        'transporter_name',
        'no_pbp',
        'alamat_pbp',
        'lat_penyaluran',
        'long_penyaluran',
        'status_pbp',
        'nik_pengganti',
        'no_kk_pengganti',
        'nama_pengganti',
        'notes',
        'alamat_pengganti',
        'verification_status',
        'status_serah',
        'no_bast',
        'alokasi_bulan',
        'alokasi_tahun',
        'entitas',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'foto_ktp_sinden',
        'foto_pbp_sinden',
        'is_redocumented',
        'updated_by_pentri'
    ];

    // Aktifkan timestamp otomatis untuk created_at dan updated_at
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
