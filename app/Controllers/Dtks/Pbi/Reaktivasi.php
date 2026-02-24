<?php

namespace App\Controllers\Dtks\Pbi;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\PbiVerivaliReferenceModel;
use App\Controllers\BaseController;
use App\Models\PbiReaktivasiModel;

class Reaktivasi extends BaseController
{
    protected $model;
    protected $db;

    private const ROLE_KECAMATAN = 2;
    private const ROLE_DESA = 3;
    private const ROLE_PENTRI = 4;

    private const MAX_UPLOAD_MB = 5;
    private const MAX_FINAL_KB = 450;
    private const PDF_MODES = ['/screen', '/ebook'];

    public function __construct()
    {
        $this->model = new PbiReaktivasiModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {

        return $this->render('dtks/pbi/reaktivasi/index', [
            'title' => 'Daftar Pengajuan Reaktivasi PBI'
        ]);
    }

    public function create()
    {
        return $this->render('dtks/pbi/reaktivasi/create', [
            'title' => 'Ajukan Reaktivasi PBI'
        ]);
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

    private function parseWilayahTugas(string $wilayah): array
    {
        $result = [];

        $pairs = explode('|', $wilayah);

        foreach ($pairs as $pair) {
            [$rw, $rts] = explode(':', $pair);
            $rtList = explode(',', $rts);

            $result[$rw] = $rtList;
        }

        return $result;
    }

    public function tabel_data()
    {
        log_message('debug', json_encode($this->request->getVar()));

        $draw   = (int) ($this->request->getVar('draw') ?? 0);
        $start  = (int) ($this->request->getVar('start') ?? 0);
        $length = (int) ($this->request->getVar('length') ?? 10);
        $search = trim((string) ($this->request->getVar('search')['value'] ?? ''));

        $filterRw     = $this->request->getVar('rw');
        $filterRt     = $this->request->getVar('rt');
        $statusFilter = $this->request->getVar('status_filter');

        $roleId        = (int) session('role_id');
        $wilayahTugas  = session('wilayah_tugas');

        $builder = $this->db->table('pbi_verivali_reference ref')
            ->select('
            ref.*,
            r.id AS reaktivasi_id,
            r.status_pengajuan
        ')
            ->join('pbi_reaktivasi r', 'r.nik = ref.nik', 'left');

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ FILTER STATUS KEMENSOS (DARI DROPDOWN)
        |--------------------------------------------------------------------------
        */
        $kemensosStatus = $this->request->getPost('kemensos_status');

        if (!empty($kemensosStatus)) {
            $builder->where('ref.status', $kemensosStatus);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ FILTER WORKFLOW STATUS (DARI SUMMARY CARD)
        |--------------------------------------------------------------------------
        */
        $workflowStatus = $this->request->getPost('workflow_status');

        if (!empty($workflowStatus)) {

            $map = [
                'draft' => null,
                'diajukan' => 1,
                'diverifikasi' => 2,
                'disetujui' => 3,
                'ditolak' => 4,
                'diajukan_siks' => 5
            ];

            if ($workflowStatus === 'draft') {
                $builder->where('r.id IS NULL');
            } else if (isset($map[$workflowStatus])) {
                $builder->where('r.status_pengajuan', $map[$workflowStatus]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ FILTER WILAYAH TUGAS (ROLE_PENTRI)
        |--------------------------------------------------------------------------
        */
        if ($roleId == self::ROLE_PENTRI && !empty($wilayahTugas)) {

            $parsed = $this->parseWilayahTugas($wilayahTugas);

            $builder->groupStart();

            foreach ($parsed as $rw => $rts) {

                $builder->orGroupStart()
                    ->where('ref.rw', $rw)
                    ->whereIn('ref.rt', $rts)
                    ->groupEnd();
            }

            $builder->groupEnd();
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ FILTER MANUAL RW / RT (UI)
        |--------------------------------------------------------------------------
        */
        $filterRw     = $this->request->getVar('rw');
        $filterRt     = $this->request->getVar('rt');
        $statusFilter = $this->request->getVar('status_filter');

        if (!empty($filterRw)) {
            $builder->where('ref.rw', $filterRw);
        }

        if (!empty($filterRt)) {
            $builder->where('ref.rt', $filterRt);
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ SEARCH GLOBAL DATATABLE
        |--------------------------------------------------------------------------
        */
        if ($search !== '') {
            $builder->groupStart()
                ->like('ref.nik', $search)
                ->orLike('ref.nama', $search)
                ->groupEnd();
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ COUNT FILTERED
        |--------------------------------------------------------------------------
        */
        $filteredBuilder = clone $builder;
        $recordsFiltered = $filteredBuilder->countAllResults(false);

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ COUNT TOTAL (TANPA SEARCH, TAPI TETAP WILAYAH FILTER)
        |--------------------------------------------------------------------------
        */
        $totalBuilder = $this->db->table('pbi_verivali_reference ref');

        if ($roleId == self::ROLE_PENTRI && !empty($wilayahTugas)) {

            $parsed = $this->parseWilayahTugas($wilayahTugas);

            $totalBuilder->groupStart();

            foreach ($parsed as $rw => $rts) {

                $totalBuilder->orGroupStart()
                    ->where('ref.rw', $rw)
                    ->whereIn('ref.rt', $rts)
                    ->groupEnd();
            }

            $totalBuilder->groupEnd();
        }

        $recordsTotal = $totalBuilder->countAllResults();

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ ORDERING
        |--------------------------------------------------------------------------
        */
        $orderColumnIndex = $this->request->getVar('order')[0]['column'] ?? null;
        $orderDir = $this->request->getVar('order')[0]['dir'] ?? 'asc';

        $columns = [
            0 => 'ref.nama',
            1 => 'ref.nik',
            2 => 'ref.no_kk',
            3 => 'ref.desil_nasional',
            4 => 'ref.status',
            5 => 'ref.rw',
            6 => 'ref.rt',
            7 => 'ref.alamat',
            8 => 'r.status_pengajuan'
        ];

        if ($orderColumnIndex !== null && isset($columns[$orderColumnIndex])) {
            $builder->orderBy($columns[$orderColumnIndex], $orderDir);
        }

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ PAGINATION
        |--------------------------------------------------------------------------
        */
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }


    private function resolveStatus(?int $status): int
    {
        return $status ?? -1;
    }

    public function ajukan()
    {
        if ((int) session('role_id') !== self::ROLE_PENTRI) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $nik    = trim((string) ($this->request->getPost('nik') ?? ''));
        $alasan = trim((string) ($this->request->getPost('alasan') ?? ''));

        if ($nik === '' || $alasan === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        // Ambil RW RT dari reference
        $ref = $this->db->table('pbi_verivali_reference')
            ->where('nik', $nik)
            ->get()
            ->getRowArray();

        if (!$ref) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data referensi tidak ditemukan'
            ]);
        }

        $rw = trim((string) ($ref['rw'] ?? ''));
        $rt = trim((string) ($ref['rt'] ?? ''));

        if ($rw === '' || $rt === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data wilayah tidak valid'
            ]);
        }

        $file = $this->request->getFile('surat_faskes');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid'
            ]);
        }

        // 🔒 Ambil metadata sebelum move
        $mime      = $file->getMimeType();
        $size      = $file->getSize();
        $extension = $file->getExtension();

        $allowedMime = [
            'application/pdf',
            'image/jpeg',
            'image/png'
        ];

        if (!in_array($mime, $allowedMime, true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format file tidak diperbolehkan'
            ]);
        }

        if ($size > (self::MAX_UPLOAD_MB * 1024 * 1024)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File terlalu besar (maks 5MB)'
            ]);
        }

        $uploadDir = FCPATH . 'uploads/pbi/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $tempName = $file->getRandomName();
        $file->move($uploadDir, $tempName);

        $fullPath = $uploadDir . $tempName;

        // 🔄 Penamaan final (sama untuk PDF & Image)
        $finalName = sprintf(
            '%s_RW%s_RT%s_%s.%s',
            $nik,
            str_pad($rw, 3, '0', STR_PAD_LEFT),
            str_pad($rt, 3, '0', STR_PAD_LEFT),
            date('YmdHis'),
            $extension
        );

        // === PDF COMPRESSION ===
        if ($mime === 'application/pdf') {

            $compressed = $this->processPdfUpload($fullPath, $nik, $rw, $rt);

            if (!$compressed) {
                unlink($fullPath);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'PDF tetap lebih dari 450KB setelah kompresi'
                ]);
            }

            $finalName = $compressed;
        } else {

            if (!rename($fullPath, $uploadDir . $finalName)) {
                unlink($fullPath);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses file'
                ]);
            }
        }

        // =============================
        // TRANSACTION (Race-safe)
        // =============================
        $this->db->transStart();

        // Re-check existing inside transaction
        $existing = $this->model
            ->where('nik', $nik)
            ->whereIn('status_pengajuan', [
                PbiReaktivasiModel::STATUS_DIAJUKAN,
                PbiReaktivasiModel::STATUS_DIVERIFIKASI,
                PbiReaktivasiModel::STATUS_DISETUJUI
            ])
            ->first();

        if ($existing) {
            $this->db->transRollback();
            unlink($uploadDir . $finalName);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'NIK sudah dalam proses pengajuan'
            ]);
        }

        $this->model->insert([
            'nik' => $nik,
            'nama_snapshot' => $ref['nama'] ?? '',
            'alasan' => $alasan,
            'surat_faskes' => $finalName,
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DIAJUKAN,
            'tanggal_diajukan' => date('Y-m-d H:i:s'),
            'created_by' => session('id'),
            'desa_id' => session('kode_desa')
        ]);

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            unlink($uploadDir . $finalName);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pengajuan berhasil dikirim'
        ]);
    }

    private function compressPdf(string $sourcePath, string $outputPath, string $mode): bool
    {
        $gsPath = 'gswin64c'; // Sesuaikan jika perlu full path

        $command = sprintf(
            '"%s" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=%s -dNOPAUSE -dQUIET -dBATCH -sOutputFile="%s" "%s"',
            $gsPath,
            $mode,
            $outputPath,
            $sourcePath
        );

        exec($command, $output, $resultCode);

        return $resultCode === 0 && file_exists($outputPath);
    }

    private function processPdfUpload(string $originalPath, string $nik, string $rw, string $rt): ?string
    {
        clearstatcache();
        $initialSize = filesize($originalPath);

        log_message('info', "PDF BEFORE COMPRESSION: {$initialSize} bytes");

        $currentPath = $originalPath;

        foreach (self::PDF_MODES as $mode) {

            $compressedPath = dirname($originalPath) . '/tmp_' . uniqid() . '.pdf';

            if ($this->compressPdf($currentPath, $compressedPath, $mode)) {

                unlink($currentPath);
                $currentPath = $compressedPath;

                clearstatcache();
                $sizeAfter = filesize($currentPath);

                log_message('info', "PDF AFTER {$mode}: {$sizeAfter} bytes");

                if ($sizeAfter <= self::MAX_FINAL_KB * 1024) {
                    break;
                }
            }
        }

        clearstatcache();
        if (filesize($currentPath) > self::MAX_FINAL_KB * 1024) {
            unlink($currentPath);
            return null;
        }

        $finalName = sprintf(
            '%s_RW%s_RT%s_%s.pdf',
            $nik,
            str_pad($rw, 3, '0', STR_PAD_LEFT),
            str_pad($rt, 3, '0', STR_PAD_LEFT),
            date('YmdHis')
        );

        $finalPath = FCPATH . 'uploads/pbi/' . $finalName;
        rename($currentPath, $finalPath);

        return $finalName;
    }

    public function startVerifikasi($nik)
    {
        if (session('role_id') !== self::ROLE_PENTRI) {
            return $this->response->setJSON(['success' => false]);
        }

        $existing = $this->model->where('nik', $nik)->first();

        if (!$existing) {
            $this->model->insert([
                'nik' => $nik,
                'status_pengajuan' => PbiReaktivasiModel::STATUS_DRAFT,
                'tanggal_draft' => date('Y-m-d H:i:s'),
                'created_by' => session('user_id'),
                'desa_id' => session('desa_id'),
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Draft verifikasi dibuat.'
        ]);
    }

    public function summary()
    {
        $roleId       = (int) session('role_id');
        $wilayahTugas = session('wilayah_tugas');
        $kodeDesa     = session('kode_desa');

        $builder = $this->db->table('pbi_verivali_reference ref')
            ->select("
            SUM(CASE WHEN r.id IS NULL THEN 1 ELSE 0 END) AS draft,
            SUM(CASE WHEN r.status_pengajuan = 1 THEN 1 ELSE 0 END) AS diajukan,
            SUM(CASE WHEN r.status_pengajuan = 2 THEN 1 ELSE 0 END) AS diverifikasi,
            SUM(CASE WHEN r.status_pengajuan = 3 THEN 1 ELSE 0 END) AS disetujui,
            SUM(CASE WHEN r.status_pengajuan = 4 THEN 1 ELSE 0 END) AS ditolak,
            SUM(CASE WHEN r.status_pengajuan = 5 THEN 1 ELSE 0 END) AS diajukan_siks
        ", false)
            ->join(
                'pbi_reaktivasi r',
                'TRIM(r.nik) = TRIM(ref.nik)',
                'left'
            );

        /*
        |--------------------------------------------------------------------------
        | FILTER DESA (ROLE_DESA & ROLE_PENTRI)
        |--------------------------------------------------------------------------
        */
        if (!empty($kodeDesa)) {
            $builder->where('ref.kode_desa', $kodeDesa);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER KHUSUS ROLE_PENTRI (WILAYAH TUGAS)
        |--------------------------------------------------------------------------
        */
        if ($roleId === self::ROLE_PENTRI && !empty($wilayahTugas)) {

            $parsed = $this->parseWilayahTugas($wilayahTugas);

            $builder->groupStart();

            foreach ($parsed as $rw => $rts) {

                $builder->orGroupStart()
                    ->where('ref.rw', $rw)
                    ->whereIn('ref.rt', $rts)
                    ->groupEnd();
            }

            $builder->groupEnd();
        }

        $result = $builder->get()->getRowArray();

        return $this->response->setJSON([
            'draft'         => (int) ($result['draft'] ?? 0),
            'diajukan'      => (int) ($result['diajukan'] ?? 0),
            'diverifikasi'  => (int) ($result['diverifikasi'] ?? 0),
            'disetujui'     => (int) ($result['disetujui'] ?? 0),
            'ditolak'       => (int) ($result['ditolak'] ?? 0),
            'diajukan_siks' => (int) ($result['diajukan_siks'] ?? 0),
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

    // public function kirimSiks($id)
    // {
    //     if (session('role_id') !== self::ROLE_DESA) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Unauthorized',
    //         ]);
    //     }

    //     $record = $this->model->find($id);

    //     if (! $record) {
    //         return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
    //     }

    //     if ((int) $record['desa_id'] !== (int) session('desa_id')) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Unauthorized',
    //         ]);
    //     }

    //     if ((int) $record['status_pengajuan'] !== PbiReaktivasiModel::STATUS_DISETUJUI) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Status tidak valid untuk dikirim ke SIKS.',
    //         ]);
    //     }

    //     $this->model->update($id, [
    //         'status_pengajuan' => PbiReaktivasiModel::STATUS_DIAJUKAN_SIKS,
    //         'tanggal_kirim_siks' => date('Y-m-d H:i:s'),
    //     ]);

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'message' => 'Data berhasil dikirim ke SIKS.',
    //     ]);
    // }

    public function uploadExcel()
    {
        if (
            (int) session('role_id') !== self::ROLE_DESA
            && (int) session('role_id') !== self::ROLE_KECAMATAN
        ) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid'
            ]);
        }

        $ext = $file->getClientExtension();

        if (!in_array($ext, ['xls', 'xlsx'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format harus XLS/XLSX'
            ]);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File kosong'
            ]);
        }

        unset($rows[0]); // skip header

        $builder = $this->db->table('pbi_verivali_reference');

        $insertData = [];
        $nikExist = [];

        foreach ($rows as $row) {

            $nik = trim((string) ($row[3] ?? ''));

            if (!$nik) {
                continue;
            }

            // Skip duplicate in file
            if (in_array($nik, $nikExist)) {
                continue;
            }

            $nikExist[] = $nik;

            $insertData[] = [
                'nama'            => trim((string) ($row[1] ?? '')),
                'noka_jkn'        => trim((string) ($row[2] ?? '')),
                'nik'             => $nik,
                'no_kk'           => trim((string) ($row[4] ?? '')),
                'desil_nasional'  => is_numeric($row[5]) ? (int) $row[5] : null,
                'kepesertaan'     => trim((string) ($row[6] ?? '')),
                'status'          => trim((string) ($row[7] ?? '')),
                'kode_desa'       => trim((string) ($row[8] ?? '')),
                'rw'              => str_pad(trim((string) ($row[9] ?? '')), 3, '0', STR_PAD_LEFT),
                'rt'              => str_pad(trim((string) ($row[10] ?? '')), 3, '0', STR_PAD_LEFT),
                'alamat'          => trim((string) ($row[11] ?? '')),
                'created_at'      => date('Y-m-d H:i:s'),
            ];
        }

        if (empty($insertData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data valid'
            ]);
        }

        $this->db->transStart();

        foreach (array_chunk($insertData, 500) as $chunk) {
            $builder->ignore(true)->insertBatch($chunk);
        }

        $this->db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil diimport',
            'total'   => count($insertData)
        ]);
    }

    public function detail($id)
    {
        $roleId = (int) session('role_id');

        // Hanya petugas desa boleh akses
        if ($roleId !== self::ROLE_DESA) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = $this->db->table('pbi_reaktivasi r')
            ->select('
            r.*,
            ref.nama,
            ref.nik,
            ref.noka_jkn,
            ref.alamat,
            ref.rw,
            ref.rt,
            ref.kode_desa,
            d.name as nama_desa,
            k.name as nama_kecamatan
        ')
            ->join('pbi_verivali_reference ref', 'ref.nik = r.nik')
            ->join('tb_villages d', 'd.id = ref.kode_desa')
            ->join('tb_districts k', 'k.id = LEFT(ref.kode_desa, 8)')
            ->where('r.id', $id)
            ->get()
            ->getRowArray();

        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Tambahkan informasi petugas login (role 3)
    |--------------------------------------------------------------------------
    */

        $desa = $this->db->table('tb_villages')
            ->select('name')
            ->where('id', session('kode_desa'))
            ->get()
            ->getRowArray();

        $kecamatan = $this->db->table('tb_districts')
            ->select('name')
            ->where('id', session('kode_kec'))
            ->get()
            ->getRowArray();

        $data['nama_petugas_login']   = session('fullname');
        $data['nama_desa_login']      = $desa['name'] ?? '-';
        $data['nama_kecamatan_login'] = $kecamatan['name'] ?? '-';

        return $this->response->setJSON($data);
    }

    public function dropdownStatus()
    {
        $builder = $this->db->table('pbi_verivali_reference')
            ->select('status')
            ->distinct()
            ->where('status IS NOT NULL');

        $roleId = (int) session('role_id');
        $wilayah = session('wilayah_tugas');

        if ($roleId == self::ROLE_PENTRI && !empty($wilayah)) {

            $parsed = $this->parseWilayahTugas($wilayah);

            $builder->groupStart();

            foreach ($parsed as $rw => $rts) {
                $builder->orGroupStart()
                    ->where('rw', $rw)
                    ->whereIn('rt', $rts)
                    ->groupEnd();
            }

            $builder->groupEnd();
        }

        $data = $builder->get()->getResultArray();

        $result = array_map(fn($row) => $row['status'], $data);

        return $this->response->setJSON($result);
    }

    public function verifikasi($id)
    {
        if ((int) session('role_id') !== self::ROLE_DESA) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = $this->model->find($id);

        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        if ((int)$data['status_pengajuan'] !== PbiReaktivasiModel::STATUS_DIAJUKAN) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya data Diajukan yang bisa diverifikasi'
            ]);
        }

        $this->model->update($id, [
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DIVERIFIKASI,
            'tanggal_diverifikasi' => date('Y-m-d H:i:s'),
            'verified_by' => session('id')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function setujui($id)
    {
        if ((int) session('role_id') !== self::ROLE_DESA) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = $this->model->find($id);

        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        if ((int)$data['status_pengajuan'] !== PbiReaktivasiModel::STATUS_DIVERIFIKASI) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid untuk disetujui'
            ]);
        }

        $this->model->update($id, [
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DISETUJUI,
            'tanggal_disetujui' => date('Y-m-d H:i:s'),
            'verified_by' => session('id')
        ]);

        return $this->response->setJSON([
            'success' => true
        ]);
    }

    public function tolak($id)
    {
        if ((int) session('role_id') !== self::ROLE_DESA) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $data = $this->model->find($id);

        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        $this->model->update($id, [
            'status_pengajuan' => PbiReaktivasiModel::STATUS_DITOLAK,
            'tanggal_ditolak' => date('Y-m-d H:i:s'),
            'verified_by' => session('id')
        ]);

        return $this->response->setJSON([
            'success' => true
        ]);
    }

    public function kirimSiks($id)
    {
        $reak = $this->model->find($id);

        if (!$reak) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        if ($reak['status_pengajuan'] != 3) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya status Disetujui yang bisa dikirim ke SIKS'
            ]);
        }

        $this->model->update($id, [
            'status_pengajuan' => 5,
            'tanggal_diajukan_siks' => date('Y-m-d H:i:s'),
            'diajukan_siks_by' => session('id')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Berhasil dikirim ke SIKS'
        ]);
    }
}
