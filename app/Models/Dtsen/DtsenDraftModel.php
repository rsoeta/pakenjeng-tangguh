<?php

namespace App\Models\Dtsen;

use CodeIgniter\Model;
use App\Traits\WilayahFilterTrait;

class DtsenDraftModel extends Model
{
    use WilayahFilterTrait;

    protected $table      = 'dtsen_usulan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
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
    protected $useTimestamps = true;

    /**
     * Hitung jumlah draft sesuai wilayah user.
     */
    public function countDraftByUser(int $userRole, array $filter = [])
    {
        $builder = $this->db->table('dtsen_usulan u')
            ->select('COUNT(DISTINCT u.id) AS total')
            ->join('dtsen_kk kk', 'kk.id_kk = u.dtsen_kk_id', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->where('u.status', 'draft');

        $this->applyWilayahFilter($builder, $filter, $userRole);

        $row = $builder->get()->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Hitung jumlah data SUBMITTED (draft lengkap).
     */
    public function countSubmittedByUser(int $userRole, array $filter = [])
    {
        $builder = $this->db->table('dtsen_usulan u')
            ->select('COUNT(DISTINCT u.id) AS total')
            ->join('dtsen_kk kk', 'kk.id_kk = u.dtsen_kk_id', 'left')
            ->join('dtsen_rt r', 'r.id_rt = kk.id_rt', 'left')

            // status masih draft (submitted = draft yang lengkap)
            ->where('u.status', 'draft')

            // Perumahan wajib lengkap
            ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.perumahan.no_kk')) <> ''")
            ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.perumahan.kepala_keluarga')) <> ''")
            ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.perumahan.alamat')) <> ''")

            ->where("JSON_LENGTH(JSON_EXTRACT(u.payload, '$.perumahan.kondisi')) >", 0)
            ->where("JSON_LENGTH(JSON_EXTRACT(u.payload, '$.perumahan.wilayah')) >", 0)
            ->where("JSON_LENGTH(JSON_EXTRACT(u.payload, '$.perumahan.sanitasi')) >", 0)

            // Foto wajib lengkap
            ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.foto.ktp_kk')) <> ''")
            ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.foto.dalam')) <> ''")
            ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.foto.depan')) <> ''")

            // ART wajib lengkap
            ->where("EXISTS (
                SELECT 1 FROM dtsen_usulan_art a
                WHERE a.dtsen_usulan_id = u.id
                AND JSON_LENGTH(a.payload_member) > 0
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, '$.identitas.nik')) <> ''
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, '$.identitas.nama')) <> ''
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, '$.identitas.jenis_kelamin')) <> ''
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, '$.pendidikan.jenjang_pendidikan')) <> ''
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, '$.kesehatan.penyakit_kronis')) <> ''
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, '$.tenaga_kerja.pendapatan')) <> ''
            )", null, false);

        // APPLY FILTER WILAYAH
        $this->applyWilayahFilter($builder, $filter, $userRole);

        $row = $builder->get()->getRowArray();
        return (int)($row['total'] ?? 0);
    }
}
