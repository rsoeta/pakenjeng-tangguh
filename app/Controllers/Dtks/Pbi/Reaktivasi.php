<?php

namespace App\Controllers\Dtks\Pbi;

use App\Controllers\BaseController;
use App\Models\PbiReaktivasiModel;

class Reaktivasi extends BaseController
{
    protected $model;

    private const ROLE_KECAMATAN = 2;
    private const ROLE_DESA = 3;
    private const ROLE_PENTRI = 4;

    public function __construct()
    {
        $this->model = new PbiReaktivasiModel();
    }

    public function index()
    {
        return view('dtks/pbi/reaktivasi/index');
    }

    public function create()
    {
        return view('dtks/pbi/reaktivasi/create');
    }

    public function store()
    {
        if (session('role_id') !== self::ROLE_PENTRI) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $rules = [
            'nik' => 'required|min_length[16]',
            'nama_snapshot' => 'required',
            'alasan' => 'required',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $file = $this->request->getFile('surat_faskes');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid (hanya PDF/JPG/PNG, max 2MB)',
            ]);
        }

        $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];

        if ($file->getSize() > 2048 * 1024 || ! in_array($file->getMimeType(), $allowedMimeTypes, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid (hanya PDF/JPG/PNG, max 2MB)',
            ]);
        }

        $uploadDir = WRITEPATH . '../public/uploads/pbi/';
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/pbi/', $newName);

        $data = [
            'nik' => $this->request->getPost('nik'),
            'nama_snapshot' => $this->request->getPost('nama_snapshot'),
            'status_pbi_awal' => $this->request->getPost('status_pbi_awal'),
            'desil_snapshot' => $this->request->getPost('desil_snapshot'),
            'alasan' => $this->request->getPost('alasan'),
            'kondisi_mendesak' => $this->request->getPost('kondisi_mendesak') ?? 0,
            'surat_faskes' => $newName,
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DRAFT,
            'tanggal_draft' => date('Y-m-d H:i:s'),
            'created_by' => session('user_id'),
            'desa_id' => session('desa_id'),
        ];

        $this->model->insert($data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Draft berhasil disimpan.',
            'id' => $this->model->getInsertID(),
        ]);
    }


    public function tabel_data()
    {
        $draw = (int) ($this->request->getVar('draw') ?? 0);
        $start = (int) ($this->request->getVar('start') ?? 0);
        $length = (int) ($this->request->getVar('length') ?? 10);
        $search = $this->request->getVar('search');
        $order = $this->request->getVar('order');
        $columns = $this->request->getVar('columns');

        $roleId = (int) session('role_id');
        if (! in_array($roleId, [self::ROLE_PENTRI, self::ROLE_DESA, self::ROLE_KECAMATAN], true)) {
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $baseBuilder = $this->model->builder();
        $this->applyRoleFilter($baseBuilder, $roleId);
        $recordsTotal = (clone $baseBuilder)->countAllResults();

        $searchValue = trim((string) ($search['value'] ?? ''));
        if ($searchValue !== '') {
            $baseBuilder->groupStart()
                ->like('nik', $searchValue)
                ->orLike('nama_snapshot', $searchValue)
                ->orLike('alasan', $searchValue)
                ->groupEnd();
        }

        $recordsFiltered = (clone $baseBuilder)->countAllResults();

        $allowedOrderColumns = ['id', 'nik', 'nama_snapshot', 'status_pengajuan', 'created_at'];
        $orderColumn = 'id';
        $orderDir = 'DESC';

        if (is_array($order) && isset($order[0]['column'], $order[0]['dir']) && is_array($columns)) {
            $columnIndex = (int) $order[0]['column'];
            $requestedColumn = $columns[$columnIndex]['data'] ?? '';
            $requestedDir = strtolower((string) $order[0]['dir']);

            if (in_array($requestedColumn, $allowedOrderColumns, true)) {
                $orderColumn = $requestedColumn;
            }

            if (in_array($requestedDir, ['asc', 'desc'], true)) {
                $orderDir = strtoupper($requestedDir);
            }
        }

        $baseBuilder->select('*')->orderBy($orderColumn, $orderDir);

        if ($length !== -1) {
            $baseBuilder->limit($length, $start);
        }

        $result = $baseBuilder->get()->getResultArray();

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result,
        ]);
    }


    public function summary()
    {
        $roleId = (int) session('role_id');

        if (! in_array($roleId, [self::ROLE_PENTRI, self::ROLE_DESA, self::ROLE_KECAMATAN], true)) {
            return $this->response->setJSON([
                'draft' => 0,
                'diajukan' => 0,
                'diverifikasi' => 0,
                'disetujui' => 0,
                'ditolak' => 0,
                'diajukan_siks' => 0,
            ]);
        }

        $draft = $this->countByStatus($roleId, PbiReaktivasiModel::STATUS_DRAFT);
        $diajukan = $this->countByStatus($roleId, PbiReaktivasiModel::STATUS_DIAJUKAN);
        $diverifikasi = $this->countByStatus($roleId, PbiReaktivasiModel::STATUS_DIVERIFIKASI);
        $disetujui = $this->countByStatus($roleId, PbiReaktivasiModel::STATUS_DISETUJUI);
        $ditolak = $this->countByStatus($roleId, PbiReaktivasiModel::STATUS_DITOLAK);
        $diajukanSiks = $this->countByStatus($roleId, PbiReaktivasiModel::STATUS_DIAJUKAN_SIKS);

        return $this->response->setJSON([
            'draft' => $draft,
            'diajukan' => $diajukan,
            'diverifikasi' => $diverifikasi,
            'disetujui' => $disetujui,
            'ditolak' => $ditolak,
            'diajukan_siks' => $diajukanSiks,
        ]);
    }

    private function countByStatus(int $roleId, int $status): int
    {
        $builder = $this->model->builder();
        $this->applyRoleFilter($builder, $roleId);
        $builder->where('status_pengajuan', $status);

        return (int) $builder->countAllResults();
    }

    private function applyRoleFilter($builder, int $roleId): void
    {
        if ($roleId === self::ROLE_PENTRI) {
            $builder->where('created_by', (int) session('user_id'));
            return;
        }

        if ($roleId === self::ROLE_DESA) {
            $builder->where('desa_id', (int) session('desa_id'));
        }
    }

    public function submit($id)
    {
        if (session('role_id') !== self::ROLE_PENTRI) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $record = $this->model->find($id);

        if (! $record) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if ((int) $record['created_by'] !== (int) session('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        if ((int) $record['status_pengajuan'] !== PbiReaktivasiModel::STATUS_DRAFT) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid untuk diajukan.',
            ]);
        }

        $this->model->update($id, [
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DIAJUKAN,
            'tanggal_diajukan' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pengajuan berhasil dikirim.',
        ]);
    }

    public function verify($id)
    {
        if (session('role_id') !== self::ROLE_DESA) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $record = $this->model->find($id);

        if (! $record) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if ((int) $record['desa_id'] !== (int) session('desa_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        if ((int) $record['status_pengajuan'] !== PbiReaktivasiModel::STATUS_DIAJUKAN) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid untuk diverifikasi.',
            ]);
        }

        $this->model->update($id, [
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DIVERIFIKASI,
            'tanggal_verifikasi' => date('Y-m-d H:i:s'),
            'verified_by' => session('user_id'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil diverifikasi.',
        ]);
    }

    public function approve($id)
    {
        if (session('role_id') !== self::ROLE_DESA) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $record = $this->model->find($id);

        if (! $record) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if ((int) $record['desa_id'] !== (int) session('desa_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        if ((int) $record['status_pengajuan'] !== PbiReaktivasiModel::STATUS_DIVERIFIKASI) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid untuk disetujui.',
            ]);
        }

        $this->model->update($id, [
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DISETUJUI,
            'tanggal_keputusan' => date('Y-m-d H:i:s'),
            'keputusan_by' => session('user_id'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pengajuan disetujui.',
        ]);
    }

    public function reject($id)
    {
        if (session('role_id') !== self::ROLE_DESA) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $record = $this->model->find($id);

        if (! $record) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if ((int) $record['desa_id'] !== (int) session('desa_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        if ((int) $record['status_pengajuan'] !== PbiReaktivasiModel::STATUS_DIVERIFIKASI) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid untuk ditolak.',
            ]);
        }

        $this->model->update($id, [
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DITOLAK,
            'tanggal_keputusan' => date('Y-m-d H:i:s'),
            'keputusan_by' => session('user_id'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pengajuan ditolak.',
        ]);
    }

    public function kirimSiks($id)
    {
        if (session('role_id') !== self::ROLE_DESA) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $record = $this->model->find($id);

        if (! $record) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        if ((int) $record['desa_id'] !== (int) session('desa_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        if ((int) $record['status_pengajuan'] !== PbiReaktivasiModel::STATUS_DISETUJUI) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid untuk dikirim ke SIKS.',
            ]);
        }

        $this->model->update($id, [
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DIAJUKAN_SIKS,
            'tanggal_kirim_siks' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil dikirim ke SIKS.',
        ]);
    }
}
