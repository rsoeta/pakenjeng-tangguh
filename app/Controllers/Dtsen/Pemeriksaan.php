<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Traits\WilayahFilterTrait;
use Config\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Pemeriksaan extends BaseController
{
    use WilayahFilterTrait;

    protected $db;
    protected $request;
    protected $AuthModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->request = Services::request();
        $this->AuthModel = model('App\Models\AuthModel');
    }

    public function index()
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return redirect()->to('/login');
        }

        return view('dtsen/pemeriksaan/index', [
            'title' => 'Unified Archive Workspace – Pemeriksaan Data ART & KK',
            'namaApp' => nameApp(),
            'userRole' => $user['role_id'] ?? 99,
            'userWilayah' => $user['wilayah_tugas'] ?? '',
        ]);
    }

    public function workspaceStats()
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->jsonError('Akses ditolak.', 403);
        }

        $kkActive = $this->kkQuery($user)->where('kk.deleted_at', null)->countAllResults();
        $kkArchivedQuery = $this->kkQuery($user);
        $this->applyKkStatus($kkArchivedQuery, 'archive');
        $kkArchived = $kkArchivedQuery->countAllResults();

        $artActive = $this->artQuery($user)
            ->where('a.deleted_at', null)
            ->where('kk.deleted_at', null)
            ->countAllResults();
        $artArchived = $this->artQuery($user)
            ->groupStart()
            ->where('a.deleted_at IS NOT NULL', null, false)
            ->orWhere('kk.deleted_at IS NOT NULL', null, false)
            ->groupEnd()
            ->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'csrfHash' => csrf_hash(),
            'data' => [
                'total_kk' => (int) $kkActive,
                'total_art' => (int) $artActive,
                'archived_kk' => (int) $kkArchived,
                'archived_art' => (int) $artArchived,
            ],
        ]);
    }

    public function listKK()
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->dataTableError('Akses ditolak.', 403);
        }

        $post = $this->request->is('get') ? $this->request->getGet() : $this->request->getPost();
        $status = ($post['status'] ?? 'active') === 'archive' ? 'archive' : 'active';
        $start = max(0, (int) ($post['start'] ?? 0));
        $length = min(100, max(1, (int) ($post['length'] ?? 20)));
        $search = trim((string) ($post['search']['value'] ?? ''));

        $base = $this->kkQuery($user);
        $this->applyKkStatus($base, $status);
        $recordsTotal = (int) $base->countAllResults();

        $query = $this->kkQuery($user);
        $this->applyKkStatus($query, $status);
        if ($search !== '') {
            $query->groupStart()
                ->like('kk.no_kk', $search)
                ->orLike('kk.kepala_keluarga', $search)
                ->orLike('kk.alamat', $search)
                ->groupEnd();
        }
        if (($post['missing_photo'] ?? '') === '1' && $status === 'active') {
            $query->groupStart()
                ->where('kk.foto_kk', null)
                ->orWhere('kk.foto_kk', '')
                ->groupEnd();
        }

        $recordsFiltered = (int) $query->countAllResults(false);
        $sortColumns = [
            0 => 'kk.no_kk',
            1 => 'kk.kepala_keluarga',
            2 => 'kk.alamat',
            3 => 'art_count',
        ];
        $order = $post['order'][0] ?? [];
        $column = (int) ($order['column'] ?? 0);
        $direction = strtolower((string) ($order['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';

        $rows = $query
            ->select(
                'kk.id_kk, kk.no_kk, kk.kepala_keluarga, kk.alamat, kk.foto_kk, kk.deleted_at, kk.delete_reason, ' .
                    '(SELECT COUNT(*) FROM dtsen_art a WHERE a.id_kk = kk.id_kk AND a.deleted_at IS NULL) AS active_art_count, ' .
                    '(SELECT COUNT(*) FROM dtsen_art all_art WHERE all_art.id_kk = kk.id_kk) AS art_count, ' .
                    '(SELECT COUNT(*) FROM dtsen_art aa WHERE aa.id_kk = kk.id_kk AND aa.deleted_at IS NOT NULL) AS archived_art_count',
                false
            )
            ->orderBy($sortColumns[$column] ?? 'kk.kepala_keluarga', $direction)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'draw' => (int) ($post['draw'] ?? 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function listART()
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->dataTableError('Akses ditolak.', 403);
        }

        $post = $this->request->is('get') ? $this->request->getGet() : $this->request->getPost();
        $status = ($post['status'] ?? 'active') === 'archive' ? 'archive' : 'active';
        $idKk = (int) ($post['id_kk'] ?? 0);
        $start = max(0, (int) ($post['start'] ?? 0));
        $length = min(100, max(1, (int) ($post['length'] ?? 100)));
        $search = trim((string) ($post['search']['value'] ?? ''));

        $base = $this->artQuery($user);
        $this->applyArtStatus($base, $status);
        if ($idKk > 0) {
            $base->where('a.id_kk', $idKk);
        }
        $recordsTotal = (int) $base->countAllResults();

        $query = $this->artQuery($user);
        $this->applyArtStatus($query, $status);
        if ($idKk > 0) {
            $query->where('a.id_kk', $idKk);
        }
        if ($search !== '') {
            $query->groupStart()
                ->like('a.nik', $search)
                ->orLike('a.nama', $search)
                ->orLike('kk.no_kk', $search)
                ->groupEnd();
        }

        $recordsFiltered = (int) $query->countAllResults(false);
        $relationColumn = $this->artRelationColumn();
        $rows = $query
            ->select(
                "a.id_art, a.id_kk, a.nik, a.nama, a.{$relationColumn} AS shdk_value, " .
                    'a.jenis_kelamin, a.tanggal_lahir, a.pekerjaan, a.deleted_at, a.delete_reason, ' .
                    'kk.no_kk, kk.deleted_at AS kk_deleted_at, sh.jenis_shdk AS shdk_nama, pekerjaan.pk_nama AS pekerjaan_nama',
                false
            )
            ->join('tb_shdk sh', "sh.id = a.{$relationColumn}", 'left')
            ->join('tb_penduduk_pekerjaan pekerjaan', 'pekerjaan.pk_id = a.pekerjaan', 'left')
            ->orderBy('a.nama', 'ASC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['age'] = null;
            if (!empty($row['tanggal_lahir'])) {
                try {
                    $row['age'] = (new \DateTime($row['tanggal_lahir']))->diff(new \DateTime())->y;
                } catch (\Exception $e) {
                    $row['age'] = null;
                }
            }
        }
        unset($row);

        return $this->response->setJSON([
            'draw' => (int) ($post['draw'] ?? 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function quickSearch()
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->jsonError('Akses ditolak.', 403);
        }

        $search = trim((string) $this->request->getGet('q'));
        if ($search === '') {
            return $this->response->setJSON(['success' => true, 'data' => ['kk' => [], 'art' => []]]);
        }

        $kk = $this->kkQuery($user)
            ->where('kk.deleted_at', null)
            ->groupStart()
            ->like('kk.no_kk', $search)
            ->orLike('kk.kepala_keluarga', $search)
            ->orLike('kk.alamat', $search)
            ->groupEnd()
            ->select('kk.id_kk, kk.no_kk, kk.kepala_keluarga, kk.alamat')
            ->limit(8)
            ->get()
            ->getResultArray();

        $art = $this->artQuery($user)
            ->where('a.deleted_at', null)
            ->where('kk.deleted_at', null)
            ->groupStart()
            ->like('a.nik', $search)
            ->orLike('a.nama', $search)
            ->orLike('kk.no_kk', $search)
            ->groupEnd()
            ->select('a.id_art, a.id_kk, a.nik, a.nama, kk.no_kk, kk.kepala_keluarga')
            ->limit(8)
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => ['kk' => $kk, 'art' => $art]]);
    }

    public function archiveKK($id_kk)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->jsonError('Akses ditolak.', 403);
        }

        $kk = $this->findKkInScope((int) $id_kk, $user);
        if (!$kk) {
            return $this->jsonError('Data KK tidak ditemukan atau di luar wilayah tugas.', 404);
        }

        $action = $this->archiveAction();
        if (!$action) {
            return $this->jsonError('Aksi arsip tidak valid.', 400);
        }
        $now = date('Y-m-d H:i:s');
        $update = $action === 'archive'
            ? ['deleted_at' => $now, 'delete_reason' => $this->archiveReason('KK')]
            : ['deleted_at' => null, 'delete_reason' => null];

        $this->db->table('dtsen_kk')->where('id_kk', (int) $id_kk)->update($update);

        return $this->response->setJSON([
            'success' => true,
            'message' => $action === 'archive' ? 'KK berhasil diarsipkan.' : 'KK berhasil dipulihkan.',
        ]);
    }

    public function archiveART($id_art)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->jsonError('Akses ditolak.', 403);
        }

        $art = $this->findArtInScope((int) $id_art, $user);
        if (!$art) {
            return $this->jsonError('Data ART tidak ditemukan atau di luar wilayah tugas.', 404);
        }
        if (!empty($art['kk_deleted_at'])) {
            return $this->jsonError('Pulihkan KK terlebih dahulu untuk mengelola arsip ART.', 409);
        }

        $action = $this->archiveAction();
        if (!$action) {
            return $this->jsonError('Aksi arsip tidak valid.', 400);
        }
        $update = $action === 'archive'
            ? ['deleted_at' => date('Y-m-d H:i:s'), 'delete_reason' => $this->archiveReason('ART')]
            : ['deleted_at' => null, 'delete_reason' => null];

        $this->db->table('dtsen_art')->where('id_art', (int) $id_art)->update($update);

        return $this->response->setJSON([
            'success' => true,
            'message' => $action === 'archive' ? 'ART berhasil diarsipkan.' : 'ART berhasil dipulihkan.',
        ]);
    }

    public function bulkArchiveKK()
    {
        return $this->bulkArchive('kk');
    }

    public function bulkArchiveART()
    {
        return $this->bulkArchive('art');
    }

    public function ajaxEditKK($id_kk)
    {
        $user = $this->AuthModel->getUserId();
        $kk = $user ? $this->findKkInScope((int) $id_kk, $user) : null;
        if (!$user) {
            return $this->jsonError('Akses ditolak.', 403);
        }
        if (!$kk || $kk['deleted_at'] !== null) {
            return $this->jsonError('Data KK aktif tidak ditemukan.', 404);
        }

        return $this->response->setJSON(['success' => true, 'data' => $kk]);
    }

    public function ajaxUpdateKK($id_kk)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->jsonError('Akses ditolak.', 403);
        }
        $kk = $this->findKkInScope((int) $id_kk, $user);
        if (!$kk || $kk['deleted_at'] !== null) {
            return $this->jsonError('Data KK aktif tidak ditemukan.', 404);
        }

        $post = $this->request->getPost();
        $noKk = trim((string) ($post['no_kk'] ?? ''));
        $head = trim((string) ($post['kepala_keluarga'] ?? ''));
        if ($noKk === '' || $head === '') {
            return $this->jsonError('Nomor KK dan kepala keluarga wajib diisi.', 422);
        }

        $update = [
            'no_kk' => $noKk,
            'kepala_keluarga' => $head,
            'alamat' => trim((string) ($post['alamat'] ?? '')),
            'program_bansos' => trim((string) ($post['program_bansos'] ?? '')) ?: null,
            'kategori_adat' => trim((string) ($post['kategori_adat'] ?? 'Tidak')),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $user['nik'] ?? ($user['username'] ?? 'system'),
        ];
        $this->updateExistingFields('dtsen_kk', $update, 'id_kk', (int) $id_kk);

        return $this->response->setJSON(['success' => true, 'message' => 'Data KK berhasil diperbarui.']);
    }

    public function ajaxEditART($id_art)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->jsonError('Akses ditolak.', 403);
        }
        $art = $this->findArtInScope((int) $id_art, $user);
        if (!$art || $art['deleted_at'] !== null || $art['kk_deleted_at'] !== null) {
            return $this->jsonError('Data ART aktif tidak ditemukan.', 404);
        }

        $art['shdk'] = $art[$this->artRelationColumn()] ?? '';
        return $this->response->setJSON(['success' => true, 'data' => $art]);
    }

    public function ajaxUpdateART($id_art)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->jsonError('Akses ditolak.', 403);
        }
        $art = $this->findArtInScope((int) $id_art, $user);
        if (!$art || $art['deleted_at'] !== null || $art['kk_deleted_at'] !== null) {
            return $this->jsonError('Data ART aktif tidak ditemukan.', 404);
        }

        $post = $this->request->getPost();
        $name = trim((string) ($post['nama'] ?? ''));
        $nik = trim((string) ($post['nik'] ?? ''));
        if ($name === '') {
            return $this->jsonError('Nama ART wajib diisi.', 422);
        }
        if ($nik !== '' && !preg_match('/^\d{16}$/', $nik)) {
            return $this->jsonError('NIK harus berisi 16 digit atau dikosongkan.', 422);
        }
        $nikChanged = $nik !== '' && $nik !== (string) ($art['nik'] ?? '');
        if ($nikChanged) {
            if (trim((string) ($post['reason_nik'] ?? '')) === '') {
                return $this->jsonError('Perubahan NIK harus disertai alasan.', 422);
            }
            $auditFields = ['entity', 'entity_id', 'field', 'old_value', 'new_value', 'reason', 'changed_by', 'changed_at'];
            if (!$this->db->tableExists('dtsen_audit')) {
                return $this->jsonError('Perubahan NIK dibatalkan karena tabel audit belum tersedia.', 409);
            }
            foreach ($auditFields as $field) {
                if (!$this->db->fieldExists($field, 'dtsen_audit')) {
                    return $this->jsonError('Perubahan NIK dibatalkan karena struktur audit belum lengkap.', 409);
                }
            }
            $duplicate = $this->db->table('dtsen_art')
                ->where('nik', $nik)
                ->where('id_art !=', (int) $id_art)
                ->where('deleted_at', null)
                ->countAllResults();
            if ($duplicate > 0) {
                return $this->jsonError('NIK sudah digunakan ART lain.', 409);
            }
        }

        $relationColumn = $this->artRelationColumn();
        $update = [
            'nik' => $nik !== '' ? $nik : null,
            'nama' => $name,
            $relationColumn => trim((string) ($post['shdk'] ?? '')) ?: null,
            'jenis_kelamin' => trim((string) ($post['jenis_kelamin'] ?? '')) ?: null,
            'tanggal_lahir' => trim((string) ($post['tanggal_lahir'] ?? '')) ?: null,
            'pendidikan_terakhir' => trim((string) ($post['pendidikan_terakhir'] ?? '')) ?: null,
            'pekerjaan' => trim((string) ($post['pekerjaan'] ?? '')) ?: null,
            'disabilitas' => trim((string) ($post['disabilitas'] ?? '')) ?: null,
            'status_hamil' => ($post['status_hamil'] ?? 'Tidak') === 'Ya' ? 'Ya' : 'Tidak',
            'ibu_kandung' => trim((string) ($post['ibu_kandung'] ?? '')) ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $user['nik'] ?? ($user['username'] ?? 'system'),
        ];
        $this->db->transBegin();
        if ($nikChanged) {
            $this->db->table('dtsen_audit')->insert([
                'entity' => 'dtsen_art',
                'entity_id' => (int) $id_art,
                'field' => 'nik',
                'old_value' => $art['nik'] ?? null,
                'new_value' => $nik,
                'reason' => trim((string) $post['reason_nik']),
                'changed_by' => $user['nik'] ?? ($user['username'] ?? 'system'),
                'changed_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->updateExistingFields('dtsen_art', $update, 'id_art', (int) $id_art);
        if (!$this->db->transStatus()) {
            $this->db->transRollback();
            return $this->jsonError('Perubahan ART gagal disimpan; tidak ada perubahan yang diterapkan.', 500);
        }
        $this->db->transCommit();

        return $this->response->setJSON(['success' => true, 'message' => 'Data ART berhasil diperbarui.']);
    }

    public function lookupShdk()
    {
        return $this->lookup('tb_shdk', 'id', 'jenis_shdk');
    }

    public function lookupPendidikan()
    {
        return $this->lookup('pendidikan_kk', 'pk_id', 'pk_nama');
    }

    public function lookupPekerjaan()
    {
        return $this->lookup('tb_penduduk_pekerjaan', 'pk_id', 'pk_nama');
    }

    public function lookupDisabilitas()
    {
        return $this->lookup('tb_disabil_jenis', 'dj_id', 'dj_keterangan');
    }

    public function export()
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return redirect()->to('/login');
        }

        $type = $this->request->getGet('type') === 'art' ? 'art' : 'kk';
        $status = $this->request->getGet('status') === 'archive' ? 'archive' : 'active';
        $search = trim((string) $this->request->getGet('q'));
        $idKk = (int) $this->request->getGet('id_kk');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($type === 'art') {
            $query = $this->artQuery($user);
            $this->applyArtStatus($query, $status);
            if ($idKk > 0) {
                $query->where('a.id_kk', $idKk);
            }
            if ($search !== '') {
                $query->groupStart()->like('a.nik', $search)->orLike('a.nama', $search)->groupEnd();
            }
            $rows = $query
                ->select('a.nik, a.nama, a.jenis_kelamin, a.tanggal_lahir, a.pekerjaan, a.pendidikan_terakhir, a.disabilitas, kk.no_kk')
                ->orderBy('kk.no_kk', 'ASC')
                ->orderBy('a.nama', 'ASC')
                ->get()
                ->getResultArray();
            $sheet->fromArray(['NIK', 'Nama', 'Jenis Kelamin', 'Tanggal Lahir', 'Pekerjaan', 'Pendidikan', 'Disabilitas', 'No. KK'], null, 'A1');
            if ($rows) {
                $sheet->fromArray(array_values($rows), null, 'A2');
            }
        } else {
            $query = $this->kkQuery($user);
            $this->applyKkStatus($query, $status);
            if ($search !== '') {
                $query->groupStart()->like('kk.no_kk', $search)->orLike('kk.kepala_keluarga', $search)->orLike('kk.alamat', $search)->groupEnd();
            }
            $rows = $query
                ->select('kk.no_kk, kk.kepala_keluarga, kk.alamat, kk.jumlah_anggota, kk.foto_kk, kk.program_bansos')
                ->orderBy('kk.no_kk', 'ASC')
                ->get()
                ->getResultArray();
            $sheet->fromArray(['No. KK', 'Kepala Keluarga', 'Alamat', 'Jumlah Anggota', 'Foto KK', 'Program Bansos'], null, 'A1');
            if ($rows) {
                $sheet->fromArray(array_values($rows), null, 'A2');
            }
        }

        $filename = "pemeriksaan_{$type}_{$status}_" . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    public function detailKK($id_kk)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        }
        $kk = $this->kkQuery($user)
            ->select('kk.*, rt.rw, rt.rt, dbj.dbj_nama_bansos')
            ->join('dtks_bansos_jenis dbj', 'dbj.dbj_id = kk.program_bansos', 'left')
            ->where('kk.id_kk', (int) $id_kk)
            ->get()
            ->getRowArray();
        if (!$kk) {
            return $this->response->setStatusCode(404)->setBody('Data KK tidak ditemukan.');
        }

        $arts = $this->artQuery($user)
            ->where('a.id_kk', (int) $id_kk)
            ->where('a.deleted_at', null)
            ->select('a.*, sh.jenis_shdk, pendidikan.pk_nama AS pendidikan_nama, pekerjaan.pk_nama AS pekerjaan_nama')
            ->join('tb_shdk sh', 'sh.id = a.' . $this->artRelationColumn(), 'left')
            ->join('pendidikan_kk pendidikan', 'pendidikan.pk_id = a.pendidikan_terakhir', 'left')
            ->join('tb_penduduk_pekerjaan pekerjaan', 'pekerjaan.pk_id = a.pekerjaan', 'left')
            ->orderBy('a.nama', 'ASC')
            ->get()
            ->getResultArray();

        return view('dtsen/pemeriksaan/detail_kk', ['kk' => $kk, 'arts' => $arts, 'jumlahAnggota' => count($arts)]);
    }

    public function detailART($id_art)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        }
        $art = $this->artQuery($user)
            ->select('a.*, kk.no_kk, kk.kepala_keluarga, rt.rw, rt.rt, sh.jenis_shdk, pendidikan.pk_nama AS pendidikan_nama, pekerjaan.pk_nama AS pekerjaan_nama, bansos.dbj_nama_bansos AS bantuan_nama')
            ->join('tb_shdk sh', 'sh.id = a.' . $this->artRelationColumn(), 'left')
            ->join('pendidikan_kk pendidikan', 'pendidikan.pk_id = a.pendidikan_terakhir', 'left')
            ->join('tb_penduduk_pekerjaan pekerjaan', 'pekerjaan.pk_id = a.pekerjaan', 'left')
            ->join('dtks_bansos_jenis bansos', 'bansos.dbj_id = a.program_bansos', 'left')
            ->where('a.id_art', (int) $id_art)
            ->get()
            ->getRowArray();
        if (!$art) {
            return $this->response->setStatusCode(404)->setBody('Data ART tidak ditemukan.');
        }

        return view('dtsen/pemeriksaan/detail_art', ['art' => $art]);
    }

    protected function kkQuery(array $user)
    {
        $builder = $this->db->table('dtsen_kk kk')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');
        return $this->applyWilayahScope($builder, $user);
    }

    protected function artQuery(array $user)
    {
        $builder = $this->db->table('dtsen_art a')
            ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');
        return $this->applyWilayahScope($builder, $user);
    }

    protected function applyWilayahScope($builder, array $user)
    {
        $roleId = (int) ($user['role_id'] ?? 99);
        if ($roleId === 1) {
            return $builder;
        }

        if ($roleId === 2) {
            $kodeKecamatan = trim((string) ($user['kode_kec'] ?? ''));
            if ($kodeKecamatan === '') {
                return $builder->where('1 = 0', null, false);
            }

            return $builder
                ->join('tb_villages pemeriksaan_village', 'pemeriksaan_village.id = rt.kode_desa', 'left')
                ->where('pemeriksaan_village.district_id', $kodeKecamatan);
        }

        if ($roleId === 3 || $roleId >= 4) {
            $kodeDesa = trim((string) ($user['kode_desa'] ?? ''));
            if ($kodeDesa === '') {
                return $builder->where('1 = 0', null, false);
            }

            if ($roleId === 3) {
                return $builder->where('rt.kode_desa', $kodeDesa);
            }

            $wilayahTugas = trim((string) ($user['wilayah_tugas'] ?? ''));
            $level = trim((string) ($user['level'] ?? ''));
            if ($wilayahTugas === '' && $level === '') {
                return $builder->where('1 = 0', null, false);
            }

            return $this->applyWilayahFilter($builder, [
                'kode_desa' => $kodeDesa,
                'wilayah_tugas' => $wilayahTugas,
                'level' => $level,
            ], $roleId);
        }

        return $builder->where('1 = 0', null, false);
    }

    protected function applyKkStatus($builder, string $status): void
    {
        if ($status === 'archive') {
            $builder->groupStart()
                ->where('kk.deleted_at IS NOT NULL', null, false)
                ->orWhere(
                    '(SELECT COUNT(*) FROM dtsen_art aa WHERE aa.id_kk = kk.id_kk AND aa.deleted_at IS NOT NULL) >',
                    0,
                    false
                )
                ->groupEnd();
        } else {
            $builder->where('kk.deleted_at', null);
        }
    }

    protected function applyArtStatus($builder, string $status): void
    {
        if ($status === 'archive') {
            $builder->groupStart()
                ->where('a.deleted_at IS NOT NULL', null, false)
                ->orWhere('kk.deleted_at IS NOT NULL', null, false)
                ->groupEnd();
        } else {
            $builder->where('a.deleted_at', null)->where('kk.deleted_at', null);
        }
    }

    protected function findKkInScope(int $id, array $user): ?array
    {
        $row = $this->kkQuery($user)
            ->where('kk.id_kk', $id)
            ->select('kk.*')
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    protected function findArtInScope(int $id, array $user): ?array
    {
        $row = $this->artQuery($user)
            ->where('a.id_art', $id)
            ->select('a.*, kk.deleted_at AS kk_deleted_at, kk.no_kk, kk.kepala_keluarga')
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    protected function artRelationColumn(): string
    {
        return $this->db->fieldExists('shdk', 'dtsen_art') ? 'shdk' : 'hubungan_keluarga';
    }

    protected function archiveAction(): ?string
    {
        $action = (string) ($this->request->getPost('action') ?? 'archive');
        return in_array($action, ['archive', 'restore'], true) ? $action : null;
    }

    protected function archiveReason(string $entity): string
    {
        $reason = trim((string) ($this->request->getPost('reason') ?? ''));
        return $reason !== '' ? mb_substr($reason, 0, 1000) : "Diarsipkan melalui Workspace Pemeriksaan {$entity}.";
    }

    protected function bulkArchive(string $entity)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->jsonError('Akses ditolak.', 403);
        }
        $action = $this->archiveAction();
        $ids = $this->request->getPost('ids') ?? [];
        if (!$action || !is_array($ids) || !$ids || count($ids) > 100) {
            return $this->jsonError('Pilihan aksi atau jumlah data tidak valid.', 422);
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn($id) => $id > 0)));
        if (!$ids) {
            return $this->jsonError('Pilih setidaknya satu data.', 422);
        }

        if ($entity === 'kk') {
            $builder = $this->kkQuery($user)->whereIn('kk.id_kk', $ids)->select('kk.id_kk');
            if ($action === 'archive') {
                $builder->where('kk.deleted_at', null);
            } else {
                $builder->where('kk.deleted_at IS NOT NULL', null, false);
            }
            $table = 'dtsen_kk';
            $key = 'id_kk';
        } else {
            $builder = $this->artQuery($user)->whereIn('a.id_art', $ids)->select('a.id_art');
            $builder->where('kk.deleted_at', null);
            if ($action === 'archive') {
                $builder->where('a.deleted_at', null);
            } else {
                $builder->where('a.deleted_at IS NOT NULL', null, false);
            }
            $table = 'dtsen_art';
            $key = 'id_art';
        }
        $allowedIds = array_map('intval', array_column($builder->get()->getResultArray(), $key));
        if (!$allowedIds) {
            return $this->jsonError('Tidak ada data yang dapat diproses dalam wilayah tugas.', 403);
        }

        $update = $action === 'archive'
            ? ['deleted_at' => date('Y-m-d H:i:s'), 'delete_reason' => $this->archiveReason(strtoupper($entity))]
            : ['deleted_at' => null, 'delete_reason' => null];
        $this->db->table($table)->whereIn($key, $allowedIds)->update($update);

        return $this->response->setJSON([
            'success' => true,
            'message' => count($allowedIds) . ' data berhasil ' . ($action === 'archive' ? 'diarsipkan.' : 'dipulihkan.'),
            'data' => ['success' => count($allowedIds), 'failed' => count($ids) - count($allowedIds)],
        ]);
    }

    protected function updateExistingFields(string $table, array $data, string $key, int $id): void
    {
        $available = array_filter(
            $data,
            fn($value, $field) => $this->db->fieldExists($field, $table),
            ARRAY_FILTER_USE_BOTH
        );
        $this->db->table($table)->where($key, $id)->update($available);
    }

    protected function lookup(string $table, string $idColumn, string $labelColumn)
    {
        if (!$this->AuthModel->getUserId()) {
            return $this->jsonError('Akses ditolak.', 403);
        }
        $rows = $this->db->table($table)
            ->select("{$idColumn} AS id, {$labelColumn} AS label")
            ->orderBy($idColumn, 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    protected function jsonError(string $message, int $status)
    {
        return $this->response->setJSON(['success' => false, 'message' => $message])->setStatusCode($status);
    }

    protected function dataTableError(string $message, int $status)
    {
        return $this->response->setJSON([
            'draw' => (int) ($this->request->getPost('draw') ?? 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'success' => false,
            'message' => $message,
        ])->setStatusCode($status);
    }
}
