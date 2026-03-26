<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;

class PenentuanKemiskinan extends BaseController
{
    protected $db;
    protected $kkModel;
    protected $model;


    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->kkModel = new \App\Models\Dtsen\DtsenKkModel();
        $this->model = new \App\Models\Dtsen\PenentuanKemiskinanModel();
    }

    /*
    =================================
    HALAMAN PENENTUAN
    =================================
    */

    public function penentuan()
    {
        $filter = [
            'kode_desa'     => session()->kode_desa,
            'wilayah_tugas' => session()->wilayah_tugas,
            'rw'            => $this->request->getGet('rw'),
            'rt'            => $this->request->getGet('rt'),
            'desil'         => $this->request->getGet('desil'),
        ];

        $userRole = session()->get('role_id');

        $data = [
            'title' => 'Penentuan Kemiskinan',
            // 'keluarga' => $this->model->getPenentuanKemiskinan($filter)
        ];

        return view('dtsen/penentuan_kemiskinan/penentuan', $data);
    }

    public function datatable()
    {
        $filter = [
            'rw'    => $this->request->getGet('rw'),
            'rt'    => $this->request->getGet('rt'),
            'desil' => $this->request->getGet('desil'),
            'kode_desa' => session()->kode_desa,
            'wilayah_tugas' => session()->wilayah_tugas
        ];

        $data = $this->model->getPenentuanKemiskinan($filter);

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    /*
    =================================
    HALAMAN VERIFIKASI
    =================================
    */

    public function verifikasi()
    {
        $filter = [
            'kode_desa'     => session()->kode_desa,
            'wilayah_tugas' => session()->wilayah_tugas,
            'rw'            => $this->request->getGet('rw'),
            'rt'            => $this->request->getGet('rt'),
            'desil'         => $this->request->getGet('desil')
        ];

        $data = [
            'title' => 'Verifikasi Kemiskinan',
            'verifikasi' => $this->model->getVerifikasiKemiskinan($filter)
        ];

        return view('dtsen/penentuan_kemiskinan/verifikasi', $data);
    }

    public function verifikasiData()
    {
        $filter = [
            'kode_desa'     => session()->kode_desa,
            'wilayah_tugas' => session()->wilayah_tugas,
            'rw'            => $this->request->getGet('rw'),
            'rt'            => $this->request->getGet('rt'),
            'desil'         => $this->request->getGet('desil')
        ];

        $data = $this->model->getVerifikasiKemiskinan($filter);

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    /*
    =================================
    HALAMAN DATA FINAL
    =================================
    */

    public function final()
    {
        $filter = [
            'kode_desa'     => session()->kode_desa,
            'wilayah_tugas' => session()->wilayah_tugas,
            'rw'            => $this->request->getGet('rw'),
            'rt'            => $this->request->getGet('rt'),
            'desil'         => $this->request->getGet('desil')
        ];

        $data = [
            'title' => 'Data Final Kemiskinan',
            'final' => $this->model->getDataKemiskinanFinal($filter)
        ];

        return view('dtsen/penentuan_kemiskinan/final', $data);
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
            ->table('dtsen_kemiskinan_alasan_master')
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
        // var_dump($_POST);
        // die;
        $kkId = (int) trim($this->request->getPost('kk_id'));
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

        /**
         * 🔍 CEK SUDAH ADA BELUM
         */
        $existing = $this->db->table('dtsen_penentuan_kemiskinan')
            ->where('dtsen_kk_id', $kkId)
            ->get()
            ->getRowArray();

        /**
         * 🧪 DEBUG WAJIB (TARUH DI SINI)
         */
        log_message('error', 'KKID: ' . $kkId);
        log_message('error', 'EXISTING: ' . json_encode($existing));

        // var_dump($existing);
        // die;
        if ($existing) {

            // 🔄 UPDATE (kasus rollback)
            $penentuanId = $existing['id'];

            $this->db->table('dtsen_penentuan_kemiskinan')
                ->where('id', $penentuanId)
                ->update([
                    'status_kemiskinan' => $status,
                    'catatan'           => $catatan,
                    'status_verifikasi' => 'pending', // 🔥 penting
                    'updated_by'        => session()->get('id'),
                    'updated_at'        => date('Y-m-d H:i:s')
                ]);

            // 🧹 HAPUS alasan lama
            $this->db->table('dtsen_penentuan_kemiskinan_alasan')
                ->where('penentuan_id', $penentuanId)
                ->delete();
        } else {

            // ➕ INSERT BARU
            $this->db->table('dtsen_penentuan_kemiskinan')->insert([
                'dtsen_kk_id'       => $kkId,
                'status_kemiskinan' => $status,
                'catatan'           => $catatan,
                'status_verifikasi' => 'pending',
                'created_by'        => session()->get('id'),
            ]);

            $penentuanId = $this->db->insertID();
        }

        /**
         * 💾 INSERT ALASAN BARU
         */
        foreach ($alasan as $id) {
            $this->db->table('dtsen_penentuan_kemiskinan_alasan')->insert([
                'penentuan_id' => $penentuanId,
                'alasan_id'    => $id
            ]);
        }

        /**
         * 🧾 LOG
         */
        $this->db->table('dtsen_penentuan_kemiskinan_log')->insert([
            'penentuan_id'      => $penentuanId,
            'aksi'              => $existing ? 'update' : 'create',
            'status_kemiskinan' => $status,
            'user_id'           => session()->get('id'),
            'catatan'           => $catatan
        ]);

        $this->db->transComplete();

        return $this->response->setJSON([
            'success' => true
        ]);
    }

    public function detail()
    {
        $id = $this->request->getGet('id');

        $db = \Config\Database::connect();

        $row = $db->table('dtsen_penentuan_kemiskinan')
            ->select('status_kemiskinan, catatan')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON([
                'status' => '',
                'alasan' => [],
                'catatan' => ''
            ]);
        }

        $alasanRows = $db->table('dtsen_penentuan_kemiskinan_alasan pa')
            ->select('m.kategori, m.label')
            ->join('dtsen_kemiskinan_alasan_master m', 'm.id = pa.alasan_id')
            ->where('pa.penentuan_id', $id)
            ->orderBy('m.kategori', 'ASC')
            ->orderBy('m.urutan', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];

        foreach ($alasanRows as $a) {

            $kategori = ucfirst($a['kategori']);

            $grouped[$kategori][] = $a['label'];
        }

        return $this->response->setJSON([
            'status' => $row['status_kemiskinan'],
            'alasan' => $grouped,
            'catatan' => $row['catatan']
        ]);
    }

    public function validasi()
    {
        $id = $this->request->getPost('id');

        $this->db->table('dtsen_penentuan_kemiskinan')
            ->where('id', $id)
            ->update([
                'status_verifikasi' => 'approved',
                'verified_by' => session()->get('id'),
                'verified_at' => date('Y-m-d H:i:s')
            ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function tolak()
    {
        $id = $this->request->getPost('id');

        $this->db->table('dtsen_penentuan_kemiskinan')
            ->where('id', $id)
            ->update([
                'status_verifikasi' => 'rejected'
            ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function rollback()
    {
        $id = $this->request->getPost('id');
        $userId = session()->get('user_id');

        $data = $this->model->find($id);

        // update status
        $this->model->update($id, [
            'status_verifikasi' => 'rollback',
            'verified_by' => null,
            'verified_at' => null
        ]);

        // log aktivitas
        $this->db->table('dtsen_penentuan_kemiskinan_log')->insert([
            'penentuan_id' => $id,
            'aksi' => 'rollback',
            'status_kemiskinan' => $data['status_kemiskinan'], // WAJIB
            'user_id' => $userId,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true
        ]);
    }
}
