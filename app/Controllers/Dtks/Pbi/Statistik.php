<?php

namespace App\Controllers\Dtks\Pbi;

use App\Controllers\BaseController;
use App\Models\PbiMasterDataModel;
use App\Models\Dtks\AuthModel;

class Statistik extends BaseController
{
    protected $pbiModel;
    protected $AuthModel;

    public function __construct()
    {
        $this->pbiModel = new PbiMasterDataModel();
        $this->AuthModel = new AuthModel();
    }

    public function index()
    {
        $data = ['title' => 'Statistik Kepesertaan PBI-JKN'];
        return view('dtks/pbi/v_statistik', $data);
    }

    public function get_data()
    {
        if (!$this->request->isAJAX()) return exit('Tidak diizinkan');

        $userInfo = $this->AuthModel->getUserId();
        $kodeDesa = $userInfo['kode_desa'] ?? session()->get('kode_desa');
        $wilayahTugas = trim($userInfo['wilayah_tugas'] ?? '');

        // 1. Ambil Total Aktif vs Non-Aktif
        $builderStatus = $this->pbiModel->builder()->select('status_kepesertaan, COUNT(id) as total')->groupBy('status_kepesertaan');
        if (!empty($kodeDesa)) $builderStatus->where('kode_desa', $kodeDesa);

        // 🔐 Gembok Chart Status
        if (!empty($wilayahTugas)) {
            $blocks = explode('|', $wilayahTugas);
            $builderStatus->groupStart();
            foreach ($blocks as $block) {
                [$rw, $rtList] = array_pad(explode(':', $block), 2, '');
                $rw = trim($rw);
                if ($rw !== '') {
                    $builderStatus->orGroupStart()->where('rw', str_pad($rw, 3, '0', STR_PAD_LEFT));
                    $rts = array_filter(array_map('trim', explode(',', $rtList)));
                    if (!empty($rts)) {
                        $rtVariants = [];
                        foreach ($rts as $rt) $rtVariants[] = str_pad($rt, 3, '0', STR_PAD_LEFT);
                        $builderStatus->whereIn('rt', $rtVariants);
                    }
                    $builderStatus->groupEnd();
                }
            }
            $builderStatus->groupEnd();
        }

        $dataStatus = $builderStatus->get()->getResultArray();
        $chartStatus = ['labels' => [], 'data' => [], 'colors' => []];

        foreach ($dataStatus as $row) {
            $chartStatus['labels'][] = $row['status_kepesertaan'];
            $chartStatus['data'][] = $row['total'];
            $chartStatus['colors'][] = ($row['status_kepesertaan'] == 'Aktif') ? '#198754' : '#dc3545';
        }

        // 2. Ambil Proporsi Alasan Non-Aktif
        $builderAlasan = $this->pbiModel->builder()
            ->select('alasan_nonaktif, COUNT(id) as total')
            ->where('status_kepesertaan', 'Non-Aktif')
            ->groupBy('alasan_nonaktif')
            ->orderBy('total', 'DESC');

        if (!empty($kodeDesa)) $builderAlasan->where('kode_desa', $kodeDesa);

        // 🔐 Gembok Chart Alasan
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

        $dataAlasan = $builderAlasan->get()->getResultArray();
        $chartAlasan = ['labels' => [], 'data' => []];

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
