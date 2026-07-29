<?php

namespace App\Controllers\Dtks\Pbi;

use App\Controllers\BaseController;
use App\Models\PbiMasterDataModel;

class Inactive extends BaseController
{
    protected $pbiModel;

    public function __construct()
    {
        $this->pbiModel = new PbiMasterDataModel();
    }

    public function pbi_nonaktif()
    {
        $kodeDesa = session()->get('kode_desa');

        // 🚀 Tarik daftar alasan_nonaktif yang unik (distinct) dari database
        $builderAlasan = $this->pbiModel->builder()
            ->select('alasan_nonaktif')
            ->where('status_kepesertaan', 'Non-Aktif')
            ->where('alasan_nonaktif IS NOT NULL')
            ->where('alasan_nonaktif !=', '')
            ->groupBy('alasan_nonaktif')
            ->orderBy('alasan_nonaktif', 'ASC');

        // Gembok desa jika ada
        if (!empty($kodeDesa)) {
            $builderAlasan->where('kode_desa', $kodeDesa);
        }

        $listAlasan = $builderAlasan->get()->getResultArray();

        $data = [
            'title'      => 'Data PBI-JKN (Non-Aktif)',
            'listAlasan' => $listAlasan // 🚀 Lempar datanya ke View
        ];

        return view('dtks/pbi/v_pbi_nonaktif', $data);
    }

    // 🚀 Server-Side DataTables Khusus Non-Aktif
    public function tb_pbi_nonaktif()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $post = $this->request->getPost();
        $kodeDesa = session()->get('kode_desa');

        $draw   = (int) ($post['draw'] ?? 1);
        $start  = (int) ($post['start'] ?? 0);
        $length = (int) ($post['length'] ?? 10);
        $search = $post['search']['value'] ?? '';

        // 🔥 Kunci utamanya di sini: status = Non-Aktif
        $builder = $this->pbiModel->builder()
            ->select('id, nik, no_kk, nama, alasan_nonaktif, tanggal_nonaktif, kampung, rt, rw')
            ->where('status_kepesertaan', 'Non-Aktif');

        if (!empty($kodeDesa)) {
            $builder->where('kode_desa', $kodeDesa);
        }

        // 🎛️ Filter dari UI
        if (!empty($post['rw'])) {
            $builder->where('rw', str_pad($post['rw'], 3, '0', STR_PAD_LEFT));
        }
        if (!empty($post['rt'])) {
            $builder->where('rt', str_pad($post['rt'], 3, '0', STR_PAD_LEFT));
        }

        // 🔥 TAMBAHAN: Filter Alasan Non-Aktif
        if (!empty($post['alasan'])) {
            $builder->where('alasan_nonaktif', $post['alasan']);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama', $search)
                ->orLike('nik', $search)
                ->orLike('no_kk', $search)
                ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);

        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $results = $builder->orderBy('tanggal_nonaktif', 'DESC')->get()->getResultArray();

        $totalBuilder = $this->pbiModel->builder()->where('status_kepesertaan', 'Non-Aktif');
        if (!empty($kodeDesa)) {
            $totalBuilder->where('kode_desa', $kodeDesa);
        }
        $recordsTotal = $totalBuilder->countAllResults();

        $data = [];
        $no = $start + 1;

        foreach ($results as $row) {
            // Tombol Rollback (Mengembalikan ke status Aktif)
            $btnAksi = '
                <button type="button" class="btn btn-sm btn-outline-success shadow-sm" onclick="kembalikanAktif(\'' . $row['id'] . '\', \'' . esc($row['nama']) . '\')" title="Kembalikan jadi Aktif">
                    <i class="fas fa-undo"></i> Rollback
                </button>
            ';

            $alamat = ($row['kampung'] ?? '-') . ' RT ' . str_pad($row['rt'] ?? '0', 3, '0', STR_PAD_LEFT) . '/' . str_pad($row['rw'] ?? '0', 3, '0', STR_PAD_LEFT);
            $tglNonAktif = $row['tanggal_nonaktif'] ? date('d-m-Y', strtotime($row['tanggal_nonaktif'])) : '-';

            $data[] = [
                $no++,
                '<b>' . esc($row['nama']) . '</b><br><small class="text-muted">NIK: ' . $row['nik'] . '</small>',
                esc($row['alasan_nonaktif'] ?? '-'),
                '<span class="badge bg-danger"><i class="fas fa-calendar-times"></i> ' . $tglNonAktif . '</span>',
                $alamat,
                $btnAksi
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
    }

    // 🚀 Endpoint untuk Rollback (Membatalkan Non-Aktif)
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
