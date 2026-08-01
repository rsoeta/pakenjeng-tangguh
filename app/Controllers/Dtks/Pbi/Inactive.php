<?php

namespace App\Controllers\Dtks\Pbi;

use App\Controllers\BaseController;
use App\Models\PbiMasterDataModel;
use App\Models\Dtks\AuthModel;

class Inactive extends BaseController
{
    protected $pbiModel;
    protected $AuthModel;

    public function __construct()
    {
        $this->pbiModel = new PbiMasterDataModel();
        $this->AuthModel = new AuthModel();
    }

    public function pbi_nonaktif()
    {
        $userInfo = $this->AuthModel->getUserId();
        $kodeDesa = $userInfo['kode_desa'] ?? session()->get('kode_desa');
        $wilayahTugas = trim($userInfo['wilayah_tugas'] ?? '');

        $builderAlasan = $this->pbiModel->builder()
            ->select('alasan_nonaktif')
            ->where('status_kepesertaan', 'Non-Aktif')
            ->where('alasan_nonaktif IS NOT NULL')
            ->where('alasan_nonaktif !=', '')
            ->groupBy('alasan_nonaktif')
            ->orderBy('alasan_nonaktif', 'ASC');

        if (!empty($kodeDesa)) $builderAlasan->where('kode_desa', $kodeDesa);

        // 🔐 Gembok Alasan Dropdown dengan Wilayah Tugas
        if (!empty($wilayahTugas)) {
            $blocks = explode('|', $wilayahTugas);
            $builderAlasan->groupStart();
            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);
                if ($rw !== '') {
                    $builderAlasan->orGroupStart()->where('rw', str_pad($rw, 3, '0', STR_PAD_LEFT));
                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    if (!empty($rts)) {
                        $rtVariants = [];
                        foreach ($rts as $rt) $rtVariants[] = str_pad($rt, 3, '0', STR_PAD_LEFT);
                        $builderAlasan->whereIn('rt', $rtVariants);
                    }
                    $builderAlasan->groupEnd();
                }
            }
            $builderAlasan->groupEnd();
        }

        $data = [
            'title'      => 'Data PBI-JKN (Non-Aktif)',
            'listAlasan' => $builderAlasan->get()->getResultArray()
        ];
        return view('dtks/pbi/v_pbi_nonaktif', $data);
    }

    public function tb_pbi_nonaktif()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $post = $this->request->getPost();

        $userInfo = $this->AuthModel->getUserId();
        $kodeDesa = $userInfo['kode_desa'] ?? session()->get('kode_desa');
        $wilayahTugas = trim($userInfo['wilayah_tugas'] ?? '');

        $draw   = (int) ($post['draw'] ?? 1);
        $start  = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? 10);
        $search = $post['search']['value'] ?? '';

        $builder = $this->pbiModel->builder()->where('status_kepesertaan', 'Non-Aktif');
        if (!empty($kodeDesa)) $builder->where('kode_desa', $kodeDesa);

        // 🔐 Gembok DataTables Wilayah Tugas
        if (!empty($wilayahTugas)) {
            $blocks = explode('|', $wilayahTugas);
            $builder->groupStart();
            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);
                if ($rw !== '') {
                    $builder->orGroupStart()->where('rw', str_pad($rw, 3, '0', STR_PAD_LEFT));
                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    if (!empty($rts)) {
                        $rtVariants = [];
                        foreach ($rts as $rt) $rtVariants[] = str_pad($rt, 3, '0', STR_PAD_LEFT);
                        $builder->whereIn('rt', $rtVariants);
                    }
                    $builder->groupEnd();
                }
            }
            $builder->groupEnd();
        }

        if (!empty($post['rw'])) $builder->where('rw', str_pad($post['rw'], 3, '0', STR_PAD_LEFT));
        if (!empty($post['rt'])) $builder->where('rt', str_pad($post['rt'], 3, '0', STR_PAD_LEFT));
        if (!empty($post['alasan'])) $builder->where('alasan_nonaktif', $post['alasan']);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama', $search)->orLike('nik', $search)->orLike('no_kk', $search)
                ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);
        if ($length != -1) $builder->limit($length, $start);
        $results = $builder->orderBy('tanggal_nonaktif', 'DESC')->get()->getResultArray();

        // 4️⃣ Hitung Total Data Murni
        $totalBuilder = $this->pbiModel->builder()->where('status_kepesertaan', 'Non-Aktif');
        if (!empty($kodeDesa)) $totalBuilder->where('kode_desa', $kodeDesa);

        // 🔐 Gembok Total Wilayah Tugas
        if (!empty($wilayahTugas)) {
            $blocks = explode('|', $wilayahTugas);
            $totalBuilder->groupStart();
            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);
                if ($rw !== '') {
                    $totalBuilder->orGroupStart()->where('rw', str_pad($rw, 3, '0', STR_PAD_LEFT));
                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    if (!empty($rts)) {
                        $rtVariants = [];
                        foreach ($rts as $rt) $rtVariants[] = str_pad($rt, 3, '0', STR_PAD_LEFT);
                        $totalBuilder->whereIn('rt', $rtVariants);
                    }
                    $totalBuilder->groupEnd();
                }
            }
            $totalBuilder->groupEnd();
        }
        $recordsTotal = $totalBuilder->countAllResults();

        // 🚀 Tangkap role_id user yang sedang login
        $roleId = (int) ($userInfo['role_id'] ?? session()->get('role_id') ?? 99);

        $data = [];
        $no = $start + 1;

        foreach ($results as $row) {
            $btnAksi = '-'; // Default kosong untuk role >= 4

            // 🔐 Hanya role 1, 2, 3 yang boleh melihat tombol Rollback
            if ($roleId < 4) {
                $btnAksi = '
                    <button type="button" class="btn btn-sm btn-outline-success shadow-sm" onclick="kembalikanAktif(\'' . $row['id'] . '\', \'' . esc($row['nama']) . '\')" title="Kembalikan jadi Aktif">
                        <i class="fas fa-undo"></i> Rollback
                    </button>
                ';
            }

            $alamat = ($row['kampung'] ?? '-') . ' RT ' . str_pad($row['rt'] ?? '0', 3, '0', STR_PAD_LEFT) . '/' . str_pad($row['rw'] ?? '0', 3, '0', STR_PAD_LEFT);
            // ... (lanjutan array $data[] biarkan sama seperti sebelumnya) ...
            $tglNonAktif = $row['tanggal_nonaktif'] ? date('d-m-Y', strtotime($row['tanggal_nonaktif'])) : '-';

            $data[] = [$no++, '<b>' . esc($row['nama']) . '</b><br><small class="text-muted">NIK: ' . $row['nik'] . '</small>', esc($row['alasan_nonaktif'] ?? '-'), '<span class="badge bg-danger"><i class="fas fa-calendar-times"></i> ' . $tglNonAktif . '</span>', $alamat, $btnAksi];
        }

        return $this->response->setJSON(['draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered, 'data' => $data]);
    }

    // ... (Fungsi restore_aktif biarkan utuh) ...

    // 🚀 Endpoint untuk Rollback (Membatalkan Non-Aktif)[cite: 3]
    public function restore_aktif()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $id = $this->request->getPost('id');

        $this->pbiModel->update($id, [
            'status_kepesertaan' => 'Aktif',
            'alasan_nonaktif'    => null,
            'tanggal_nonaktif'   => null,
            'updated_by'         => session()->get('id')
        ]);

        return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dikembalikan ke daftar PBI Aktif.']);
    }
}
