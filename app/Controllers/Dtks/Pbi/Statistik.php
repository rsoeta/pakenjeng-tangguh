<?php

namespace App\Controllers\Dtks\Pbi;

use App\Controllers\BaseController;
use App\Models\PbiMasterDataModel;

class Statistik extends BaseController
{
    protected $pbiModel;

    public function __construct()
    {
        $this->pbiModel = new PbiMasterDataModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Statistik Kepesertaan PBI-JKN'
        ];
        return view('dtks/pbi/v_statistik', $data);
    }

    // 🚀 Endpoint untuk menyuplai data JSON ke Chart.js
    public function get_data()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $kodeDesa = session()->get('kode_desa');

        // 1. Ambil Total Aktif vs Non-Aktif
        $builderStatus = $this->pbiModel->builder()->select('status_kepesertaan, COUNT(id) as total')->groupBy('status_kepesertaan');
        if (!empty($kodeDesa)) $builderStatus->where('kode_desa', $kodeDesa);

        $dataStatus = $builderStatus->get()->getResultArray();

        $chartStatus = [
            'labels' => [],
            'data'   => [],
            'colors' => []
        ];

        foreach ($dataStatus as $row) {
            $chartStatus['labels'][] = $row['status_kepesertaan'];
            $chartStatus['data'][] = $row['total'];
            // Beri warna Hijau untuk Aktif, Merah untuk Non-Aktif
            $chartStatus['colors'][] = ($row['status_kepesertaan'] == 'Aktif') ? '#198754' : '#dc3545';
        }

        // 2. Ambil Proporsi Alasan Non-Aktif
        $builderAlasan = $this->pbiModel->builder()
            ->select('alasan_nonaktif, COUNT(id) as total')
            ->where('status_kepesertaan', 'Non-Aktif')
            ->groupBy('alasan_nonaktif')
            ->orderBy('total', 'DESC'); // Urutkan dari yang terbanyak

        if (!empty($kodeDesa)) $builderAlasan->where('kode_desa', $kodeDesa);

        $dataAlasan = $builderAlasan->get()->getResultArray();

        $chartAlasan = [
            'labels' => [],
            'data'   => []
        ];

        foreach ($dataAlasan as $row) {
            $alasan = empty($row['alasan_nonaktif']) ? 'Tanpa Keterangan' : $row['alasan_nonaktif'];
            $chartAlasan['labels'][] = $alasan;
            $chartAlasan['data'][] = $row['total'];
        }

        return $this->response->setJSON([
            'status' => true,
            'chartStatus' => $chartStatus,
            'chartAlasan' => $chartAlasan
        ]);
    }
}
