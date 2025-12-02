<?php

namespace App\Controllers\Dtsen;

use App\Models\Dtsen\DtsenUsulanModel;
use CodeIgniter\HTTP\ResponseInterface;

class DtsenUsulan extends BaseController
{
    protected $usulanModel;

    public function __construct()
    {
        $this->usulanModel = new DtsenUsulanModel();
    }

    /**
     * Memulai usulan baru (dipanggil saat form multi-step dibuka)
     * atau melanjutkan usulan draft yang belum selesai.
     */

    public function cariKK()
    {
        $input = $this->request->getJSON(true);
        $noKK = $input['no_kk'] ?? '';
        $nik = $input['nik'] ?? '';

        $db = db_connect();
        $builder = $db->table('dtsen_kk');

        if ($noKK) $builder->where('no_kk', $noKK);
        if ($nik) $builder->where('nik_kepala', $nik);

        $data = $builder->get()->getRowArray();

        if ($data) {
            $jumlahArt = $db->table('dtsen_art')
                ->where('dtsen_kk_id', $data['id'])
                ->countAllResults();
            $data['jumlah_art'] = $jumlahArt;
            return $this->response->setJSON(['success' => true, 'data' => $data]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
    }

    public function start()
    {
        $nik = session()->get('nik');
        if (!$nik) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesi tidak valid. Silakan login ulang.'
            ]);
        }

        // Cari usulan draft terbaru user
        $draft = $this->usulanModel
            ->where('created_by', $nik)
            ->where('status', 'draft')
            ->orderBy('id', 'DESC')
            ->first();

        // Kalau belum ada draft, buat baru
        if (!$draft) {
            $usulanNo = 'USL-' . date('YmdHis') . '-' . substr($nik, -4);
            $id = $this->usulanModel->insert([
                'usulan_no' => $usulanNo,
                'jenis' => 'pembaruan',
                'status' => 'draft',
                'created_by' => $nik,
                'payload' => json_encode([]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $draft = $this->usulanModel->find($id);
        }

        return $this->response->setJSON([
            'success' => true,
            'usulan_id' => $draft['id'],
            'usulan_no' => $draft['usulan_no']
        ]);
    }

    /**
     * Simpan data per-step (AJAX)
     */
    public function saveStep()
    {
        $id = $this->request->getVar('usulan_id');
        $step = $this->request->getVar('step');
        $data = $this->request->getVar('data');

        if (!$id || !$step) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap.']);
        }

        $usulan = $this->usulanModel->find($id);
        if (!$usulan) {
            return $this->response->setJSON(['success' => false, 'message' => 'Usulan tidak ditemukan.']);
        }

        $payload = json_decode($usulan['payload'], true) ?? [];
        $payload[$step] = $data;

        $this->usulanModel->update($id, [
            'payload' => json_encode($payload),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Muat payload (untuk ringkasan di Step 7)
     */
    public function getPayload($id)
    {
        $usulan = $this->usulanModel->find($id);
        if (!$usulan) {
            return $this->response->setJSON(['success' => false]);
        }
        return $this->response->setJSON([
            'success' => true,
            'payload' => json_decode($usulan['payload'], true)
        ]);
    }

    /**
     * Submit akhir (dari Step 7)
     */
    public function submitFinal()
    {
        $id = $this->request->getVar('usulan_id');
        $catatan = $this->request->getVar('catatan');
        $signature = $this->request->getVar('signature');

        $path = FCPATH . 'uploads/signature/';
        if (!is_dir($path)) mkdir($path, 0777, true);
        $filePath = $path . 'sig_' . $id . '_' . time() . '.png';
        $signature = str_replace('data:image/png;base64,', '', $signature);
        file_put_contents($filePath, base64_decode($signature));

        $this->usulanModel->update($id, [
            'status' => 'submitted',
            'summary' => $catatan,
            'updated_at' => date('Y-m-d H:i:s'),
            'verified_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Usulan berhasil dikirim.',
            'signature_path' => str_replace(FCPATH, base_url('/'), $filePath)
        ]);
    }
}
