<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;

class DtsenRtModel extends Model
{
    protected $table            = 'dtsen_rt';
    protected $primaryKey       = 'id_rt';
    protected $allowedFields    = [
        'kode_desa',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'latitude',
        'longitude',
        'accuracy',
        'kepemilikan_rumah',
        'bukti_kepemilikan',
        'jenis_bangunan',             // 🚀 TAMBAHAN BARU
        'is_tinggal_bersama',         // 🚀 TAMBAHAN BARU
        'jumlah_kk_dalam_rumah',      // 🚀 TAMBAHAN BARU
        'jumlah_orang_dalam_rumah',   // 🚀 TAMBAHAN BARU
        'perkiraan_harga_sewa',       // 🚀 TAMBAHAN BARU
        'kondisi_atap',
        'kondisi_dinding',
        'kondisi_lantai',
        'sumber_air',
        'sanitasi',
        'sumber_listrik',
        'jumlah_meteran_listrik',     // 🚀 TAMBAHAN BARU
        // ... (kolom lainnya)
        'jumlah_meteran_listrik',
        'nomor_pelanggan',
        'nomor_meter',
        'daya_listrik',
        // ...
        'foto_rumah',
        'foto_rumah_dalam',
        'foto_kamar_mandi',           // 🚀 TAMBAHAN BARU
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps    = true;
}
