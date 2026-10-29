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
     * Hitung total draft dan submitted yang konsisten 100% dengan tabel Pembaruan Keluarga
     *
     * @param int $roleId
     * @param array $filter ['kode_desa', 'wilayah_tugas', 'rw', 'level']
     * @return array ['draft' => int, 'submitted' => int]
     */
    public function getCountsByRole(int $roleId, array $filter = []): array
    {
        $kodeDesa     = $filter['kode_desa'] ?? null;
        $wilayahTugas = $filter['wilayah_tugas'] ?? null;
        $rwUser       = $filter['rw'] ?? $filter['level'] ?? null;

        $kkModel = new \App\Models\Dtsen\DtsenKkModel();

        $builder = $this->db->table('dtsen_usulan us')
            ->select("us.id, us.status, us.payload")
            ->join('dtsen_kk kk', 'kk.id_kk = us.dtsen_kk_id', 'left')
            ->join('dtsen_rt r', 'r.id_rt = kk.id_rt', 'left')
            ->whereIn('us.status', ['draft', 'submitted'])
            // 🛡️ Abaikan KK yang telah di-soft-delete
            ->groupStart()
                ->where('kk.deleted_at', null)
                ->orWhere('kk.deleted_at', '0000-00-00 00:00:00')
                ->orWhere('us.dtsen_kk_id', null)
            ->groupEnd();

        if (!empty($kodeDesa)) {
            $builder->where('r.kode_desa', $kodeDesa);
        }

        if ($roleId >= 4) {
            $map = !empty($wilayahTugas) ? self::parseWilayahTugasMap($wilayahTugas) : [];
            if (!empty($map)) {
                $builder->groupStart();
                foreach ($map as $rw => $rtList) {
                    $rwNorm = is_numeric($rw) ? str_pad($rw, 3, '0', STR_PAD_LEFT) : $rw;
                    $builder->orGroupStart()->where('r.rw', $rwNorm);
                    if (!empty($rtList)) {
                        $rtNorms = array_map(fn($rt) => is_numeric($rt) ? str_pad($rt, 3, '0', STR_PAD_LEFT) : $rt, $rtList);
                        $builder->whereIn('r.rt', $rtNorms);
                    }
                    $builder->groupEnd();
                }
                $builder->groupEnd();
            } elseif (!empty($rwUser)) {
                $rwNorm = is_numeric($rwUser) ? str_pad($rwUser, 3, '0', STR_PAD_LEFT) : $rwUser;
                $builder->where('r.rw', $rwNorm);
            }
        }

        $rows = $builder->get()->getResultArray();

        $totalDraft = 0;
        $totalSubmitted = 0;

        foreach ($rows as $r) {
            $payload = json_decode($r['payload'] ?? '{}', true) ?? [];
            if ($kkModel->isPayloadLengkap($payload)) {
                $totalSubmitted++;
            } else {
                $totalDraft++;
            }
        }

        return [
            'draft'     => $totalDraft,
            'submitted' => $totalSubmitted,
        ];
    }
}
