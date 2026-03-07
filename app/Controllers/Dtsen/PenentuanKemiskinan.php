<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;

class PenentuanKemiskinan extends BaseController
{
    protected $kkModel;
    protected $db;

    public function __construct()
    {
        $this->kkModel = new \App\Models\Dtsen\DtsenKkModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * ======================================================
     * HALAMAN INDEX
     * ======================================================
     */

    public function index()
    {
        $filter = [
            'kode_desa'     => session()->kode_desa,
            'wilayah_tugas' => session()->wilayah_tugas,
            'rw'            => $this->request->getGet('rw'),
            'rt'            => $this->request->getGet('rt'),
            'desil'         => $this->request->getGet('desil')
        ];

        $data['keluarga'] = $this->kkModel->getPenentuanKemiskinan($filter);

        return view('dtsen/penentuan_kemiskinan/index', $data);
    }

    /**
     * ======================================================
     * AJAX AMBIL ALASAN KEMISKINAN
     * ======================================================
     */

    public function getAlasan()
    {
        $status = $this->request->getGet('status');

        $rows = $this->db
            ->table('dtks_kemiskinan_alasan_master')
            ->where('status_kemiskinan', $status)
            ->where('is_active', 1)
            ->orderBy('kategori', 'ASC')
            ->orderBy('urutan', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$row['kategori']][] = $row;
        }

        return $this->response->setJSON($grouped);
    }

    /**
     * ======================================================
     * AJAX SIMPAN PENENTUAN KEMISKINAN
     * ======================================================
     */

    public function simpan()
    {
        $kkId    = $this->request->getPost('kk_id');
        $status  = $this->request->getPost('status');
        $alasan  = $this->request->getPost('alasan');
        $catatan = $this->request->getPost('catatan');

        if (empty($alasan)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Minimal satu alasan harus dipilih'
            ]);
        }

        $this->db->transStart();

        $this->db->table('dtks_penentuan_kemiskinan')->insert([
            'dtsen_kk_id'       => $kkId,
            'status_kemiskinan' => $status,
            'catatan'           => $catatan,
            'created_by'        => session()->user_id
        ]);

        $penentuanId = $this->db->insertID();

        foreach ($alasan as $id) {

            $this->db->table('dtks_penentuan_kemiskinan_alasan')->insert([
                'penentuan_id' => $penentuanId,
                'alasan_id'    => $id
            ]);
        }

        $this->db->table('dtks_penentuan_kemiskinan_log')->insert([
            'penentuan_id'       => $penentuanId,
            'aksi'               => 'create',
            'status_kemiskinan'  => $status,
            'user_id'            => session()->user_id
        ]);

        $this->db->transComplete();

        return $this->response->setJSON([
            'success' => true
        ]);
    }
}
