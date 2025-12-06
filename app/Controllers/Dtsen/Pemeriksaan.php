<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use Config\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Pemeriksaan extends BaseController
{
    protected $db;
    protected $request;
    protected $AuthModel;
    protected $perPageDefault = 10;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->request = Services::request();
        $this->AuthModel = model('App\Models\AuthModel');
    }

    public function index()
    {
        // keamanan: hanya role tertentu
        $user = $this->AuthModel->getUserId();
        if (!$user) return redirect()->to('/login');

        $data = [
            'title' => 'Pemeriksaan Data ART & KK',
            'namaApp' => nameApp()
        ];

        return view('dtsen/pemeriksaan/index', $data);
    }

    /**
     * Server-side list KK (DataTables)
     */
    public function listKK()
    {
        $post   = $this->request->getPost();
        $start  = (int)($post['start'] ?? 0);
        $length = (int)($post['length'] ?? 10);
        $search = trim($post['search']['value'] ?? '');

        // --- USER & WILAYAH ---
        $user = $this->AuthModel->getUserId();
        $wil  = $user['wilayah_tugas'] ?? '';
        [$rw, $rtList] = $this->parseWilayah($wil);

        // --- BASE QUERY ---
        $builder = $this->db->table('dtsen_kk kk')
            ->select("
            kk.id_kk,
            kk.no_kk,
            kk.kepala_keluarga,
            kk.alamat,
            kk.jumlah_anggota,
            kk.kategori_adat,
            kk.created_at,

            kk.foto_kk,
            kk.foto_rumah,
            kk.foto_rumah_dalam,

            b.dbj_nama_bansos AS nama_program
        ")
            ->join('dtks_bansos_jenis b', 'b.dbj_id = kk.program_bansos', 'left')
            ->where('kk.deleted_at', null);

        // --- FILTER WILAYAH ---
        if ($rw !== '') {
            $builder->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
                ->where('rt.rw', $rw)
                ->where('kk.deleted_at', null);
            if (!empty($rtList)) {
                $builder->whereIn('rt.rt', $rtList);
            }
        }

        // --- SEARCH ---
        if ($search !== '') {
            $builder->groupStart()
                ->like('kk.no_kk', $search)
                ->orLike('kk.kepala_keluarga', $search)
                ->orLike('kk.alamat', $search)
                ->where('kk.deleted_at', null)
                ->groupEnd();
        }

        // --- TOTAL RECORDS ---
        $totalRecords = $builder->countAllResults(false);

        // --- ORDER ---
        if (!empty($post['order'][0])) {
            $columns = ['no_kk', 'kepala_keluarga', 'alamat', 'jumlah_anggota', 'created_at'];
            $colIdx = (int)$post['order'][0]['column'];
            $colName = $columns[$colIdx] ?? 'created_at';
            $builder->orderBy("kk.$colName", $post['order'][0]['dir']);
        } else {
            $builder->orderBy('kk.created_at', 'DESC');
        }

        // --- FETCH ---
        $data = $builder->limit($length, $start)->get()->getResultArray();

        // --- ENRICH ROWS ---
        foreach ($data as &$r) {

            // COUNT ART
            $r['art_count'] = (int)$this->db->table('dtsen_art')
                ->where('id_kk', $r['id_kk'])
                ->where('deleted_at', null)
                ->countAllResults();

            // FOTO FLAGS
            $r['has_foto_kk']         = !empty($r['foto_kk']) ? 1 : 0;
            $r['has_foto_rumah']      = !empty($r['foto_rumah']) ? 1 : 0;
            $r['has_foto_rumah_dalam'] = !empty($r['foto_rumah_dalam']) ? 1 : 0;
        }

        return $this->response->setJSON([
            'draw' => (int)($post['draw'] ?? 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Server-side list ART (DataTables)
     */
    public function listART()
    {
        $post   = $this->request->getPost();
        $start  = (int)($post['start'] ?? 0);
        $length = (int)($post['length'] ?? 10);
        $search = trim($post['search']['value'] ?? '');

        // --- USER & WILAYAH ---
        $user = $this->AuthModel->getUserId();
        $wil  = $user['wilayah_tugas'] ?? '';
        [$rw, $rtList] = $this->parseWilayah($wil);

        // --- BASE QUERY ART + JOIN2 ---
        $builder = $this->db->table('dtsen_art a')
            ->select("
            a.id_art,
            a.id_kk,
            a.nik,
            a.nama,
            a.shdk,
            a.jenis_kelamin,
            a.tanggal_lahir,
            a.status_hamil,
            a.ibu_kandung,
            a.created_at,

            kk.no_kk,

            s.jenis_shdk AS shdk_nama,
            b.dbj_nama_bansos AS nama_program,
            p.pk_nama AS pendidikan,
            k.pk_nama AS pekerjaan,
            d.dj_keterangan AS disabilitas
        ")
            ->where('a.deleted_at', null)
            ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')
            ->join('tb_shdk s', 's.id = a.shdk', 'left')
            ->join('dtks_bansos_jenis b', 'b.dbj_id = a.program_bansos', 'left')
            ->join('pendidikan_kk p', 'p.pk_id = a.pendidikan_terakhir', 'left')
            ->join('tb_penduduk_pekerjaan k', 'k.pk_id = a.pekerjaan', 'left')
            ->join('tb_disabil_jenis d', 'd.dj_id = a.disabilitas', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left');


        // --- FILTER WILAYAH ---
        if ($rw !== '') {
            $builder->where('rt.rw', $rw);
            if (!empty($rtList)) {
                $builder->whereIn('rt.rt', $rtList);
            }
        }

        // --- SEARCH ---
        if ($search !== '') {
            $builder->groupStart()
                ->like('a.nik', $search)
                ->orLike('a.nama', $search)
                ->orLike('kk.no_kk', $search)
                ->groupEnd();
        }

        // --- TOTAL ---
        $totalRecords = $builder->countAllResults(false);

        // --- ORDER ---
        if (!empty($post['order'][0])) {
            $columns = [
                'nik',
                'nama',
                'shdk',
                'jenis_kelamin',
                'tanggal_lahir',
                'pendidikan',
                'pekerjaan',
                'disabilitas',
                'status_hamil',
                'ibu_kandung',
                'nama_program',
                'no_kk',
                'created_at'
            ];
            $colIdx = (int)$post['order'][0]['column'];
            $colName = $columns[$colIdx] ?? 'created_at';

            // a.xxx atau alias lain
            if (in_array($colName, ['nama_program', 'pendidikan', 'pekerjaan', 'disabilitas', 'no_kk'])) {
                $builder->orderBy($colName, $post['order'][0]['dir']);
            } else {
                $builder->orderBy("a.$colName", $post['order'][0]['dir']);
            }
        } else {
            $builder->orderBy('a.created_at', 'DESC');
        }

        // --- FETCH ---
        $data = $builder->limit($length, $start)->get()->getResultArray();

        // --- ENRICH ROWS ---
        foreach ($data as &$r) {

            // AGE
            if (!empty($r['tanggal_lahir'])) {
                $r['age'] = date_diff(date_create($r['tanggal_lahir']), date_create())->y;
            } else {
                $r['age'] = null;
            }
        }

        return $this->response->setJSON([
            'draw' => (int)($post['draw'] ?? 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    /**
     * Export current filtered KK / ART as Excel
     * example: /dtsen/pemeriksaan/export?type=kk&search=xxx
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'kk';
        $search = $this->request->getGet('search') ?? '';

        // apply same wilayah restriction
        $user = $this->AuthModel->getUserId();
        $wil = $user['wilayah_tugas'] ?? '';
        [$rw, $rtList] = $this->parseWilayah($wil);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($type === 'art') {
            $sheet->setCellValue('A1', 'NIK');
            $sheet->setCellValue('B1', 'Nama');
            $sheet->setCellValue('C1', 'SHDK');
            $sheet->setCellValue('D1', 'Jenis Kelamin');
            $sheet->setCellValue('E1', 'Tanggal Lahir');
            $sheet->setCellValue('F1', 'Umur');
            $sheet->setCellValue('G1', 'Pendidikan');
            $sheet->setCellValue('H1', 'Pekerjaan');
            $sheet->setCellValue('I1', 'Disabilitas');
            $sheet->setCellValue('J1', 'Status Hamil');
            $sheet->setCellValue('K1', 'Ibu Kandung');
            $sheet->setCellValue('L1', 'Program Bansos');
            $sheet->setCellValue('M1', 'No KK');
            $sheet->setCellValue('N1', 'Created At');

            $builder = $this->db->table('dtsen_art as a')->select('a.nik,a.nama,a.shdk,a.jenis_kelamin,a.tanggal_lahir,a.pendidikan_terakhir,a.pekerjaan,a.disabilitas,a.status_hamil,a.ibu_kandung,a.program_bansos,kk.no_kk,a.created_at')
                ->join('dtsen_kk kk', 'kk.id_kk=a.id_kk', 'left')
                ->join('dtsen_rt rt', 'rt.id_rt=kk.id_rt', 'left');

            if ($rw !== '') {
                $builder->where('rt.rw', $rw);
                if (!empty($rtList)) $builder->whereIn('rt.rt', $rtList);
            }
            if (!empty($search)) $builder->groupStart()->like('a.nik', $search)->orLike('a.nama', $search)->groupEnd();

            $rows = $builder->get()->getResultArray();
            $i = 2;
            foreach ($rows as $r) {
                $age = !empty($r['tanggal_lahir']) ? date_diff(date_create($r['tanggal_lahir']), date_create(date('Y-m-d')))->y : '';
                $sheet->fromArray([
                    $r['nik'],
                    $r['nama'],
                    $r['shdk'],
                    $r['jenis_kelamin'],
                    $r['tanggal_lahir'],
                    $age,
                    $r['pendidikan_terakhir'],
                    $r['pekerjaan'],
                    $r['disabilitas'],
                    $r['status_hamil'],
                    $r['ibu_kandung'],
                    $r['program_bansos'],
                    $r['no_kk'],
                    $r['created_at']
                ], null, "A{$i}");
                $i++;
            }
        } else {
            // default KK export
            $sheet->setCellValue('A1', 'No KK');
            $sheet->setCellValue('B1', 'Kepala Keluarga');
            $sheet->setCellValue('C1', 'Alamat');
            $sheet->setCellValue('D1', 'Jumlah Anggota (field)');
            $sheet->setCellValue('E1', 'Jumlah ART (actual)');
            $sheet->setCellValue('F1', 'Foto KK');
            $sheet->setCellValue('G1', 'Foto Rumah');
            $sheet->setCellValue('H1', 'Foto Rumah Dalam');
            $sheet->setCellValue('I1', 'Program Bansos');
            $sheet->setCellValue('J1', 'Kategori Adat');
            $sheet->setCellValue('K1', 'Created At');

            $builder = $this->db->table('dtsen_kk as kk')->select('kk.no_kk,kk.kepala_keluarga,kk.alamat,kk.jumlah_anggota,kk.foto_kk,kk.foto_rumah,kk.foto_rumah_dalam,kk.program_bansos,kk.kategori_adat,kk.created_at')
                ->join('dtsen_rt rt', 'rt.id_rt=kk.id_rt', 'left');

            if ($rw !== '') {
                $builder->where('rt.rw', $rw);
                if (!empty($rtList)) $builder->whereIn('rt.rt', $rtList);
            }
            if (!empty($search)) $builder->groupStart()->like('kk.no_kk', $search)->orLike('kk.kepala_keluarga', $search)->groupEnd();

            $rows = $builder->get()->getResultArray();
            $i = 2;
            foreach ($rows as $r) {
                $cnt = $this->db->table('dtsen_art')->where('id_kk', $r['id_kk'] ?? 0)->countAllResults();
                $sheet->fromArray([
                    $r['no_kk'],
                    $r['kepala_keluarga'],
                    $r['alamat'],
                    $r['jumlah_anggota'],
                    $cnt,
                    $r['foto_kk'],
                    $r['foto_rumah'],
                    $r['foto_rumah_dalam'],
                    $r['program_bansos'],
                    $r['kategori_adat'],
                    $r['created_at']
                ], null, "A{$i}");
                $i++;
            }
        }

        // writer
        $filename = "export_{$type}_" . date('Ymd_His') . ".xlsx";
        $writer = new Xlsx($spreadsheet);

        // send to browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    /**
     * helper parse wilayah_tugas: "RW:RT,RT" => [rw, [rt,...]]
     */
    protected function parseWilayah($wil)
    {
        $rw = '';
        $rts = [];
        if (!empty($wil)) {
            [$rwPart, $rtPart] = array_pad(explode(':', $wil), 2, '');
            $rw = trim($rwPart);
            $rts = !empty($rtPart) ? array_map('trim', explode(',', $rtPart)) : [];
        }
        return [$rw, $rts];
    }

    public function detailKK($id_kk)
    {
        // keamanan
        $user = $this->AuthModel->getUserId();
        if (!$user) return "Akses ditolak";

        // ============================
        // 1) Ambil DATA KK + JOIN BANSOS + RT
        // ============================
        $kk = $this->db->table('dtsen_kk as kk')
            ->select('kk.*, rt.rw, rt.rt, dbj.dbj_nama_bansos')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->join('dtks_bansos_jenis dbj', 'dbj.dbj_id = kk.program_bansos', 'left')
            ->where('kk.id_kk', $id_kk)
            ->get()->getRowArray();

        if (!$kk) return "Data KK tidak ditemukan.";

        // ============================
        // 2) Ambil DATA ART + JOIN SHDK + Pendidikan + Pekerjaan + Bansos
        // ============================
        $arts = $this->db->table('dtsen_art as a')
            ->select("
            a.*,
            sh.jenis_shdk,
            pk.pk_nama as pendidikan_nama,
            pj.pk_nama as pekerjaan_nama,
            dbj.dbj_nama_bansos as bantuan_nama
        ")
            ->join('tb_shdk sh', 'sh.id = a.shdk', 'left')
            ->join('pendidikan_kk pk', 'pk.pk_id = a.pendidikan_terakhir', 'left')
            ->join('tb_penduduk_pekerjaan pj', 'pj.pk_id = a.pekerjaan', 'left')
            ->join('dtks_bansos_jenis dbj', 'dbj.dbj_id = a.program_bansos', 'left')
            ->where('a.id_kk', $id_kk)
            ->orderBy('a.shdk', 'ASC')
            ->get()->getResultArray();

        // hitung anggota secara dinamis
        $jumlahAnggota = $this->db->table('dtsen_art')
            ->where('id_kk', $id_kk)
            ->countAllResults();

        // ============================
        // 3) Kirim ke VIEW
        // ============================
        return view('dtsen/pemeriksaan/detail_kk', [
            'kk'   => $kk,
            'arts' => $arts,
            'jumlahAnggota' => $jumlahAnggota
        ]);
    }

    public function detailART($id_art)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) return "Akses ditolak";

        // Ambil data ART dan KK
        $art = $this->db->table('dtsen_art as a')
            ->select('a.*, kk.no_kk, kk.kepala_keluarga, rt.rw, rt.rt')
            ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->where('a.id_art', $id_art)
            ->get()->getRowArray();

        if (!$art) return "Data ART tidak ditemukan.";

        // ============================================
        // 🔍 Parse JSON Program Bansos
        // ============================================

        $rawJson = trim((string) $art['program_bansos']);
        $json = json_decode($rawJson, true);

        if (!is_array($json)) $json = [];

        // Normalisasi key lowercase
        $jsonNorm = [];
        foreach ($json as $k => $v) {
            $jsonNorm[strtolower(trim($k))] = (int) $v;
        }

        // Mapping
        $map = [
            'pkh'  => 'PKH',
            'bpnt' => 'BPNT',
            'bst'  => 'BST',
            'pbi'  => 'PBI'
        ];

        $aktif = [];
        foreach ($map as $kode => $nama) {
            if (isset($jsonNorm[$kode]) && $jsonNorm[$kode] == 1) {
                $aktif[] = $nama;
            }
        }

        $art['bantuan_nama'] = $aktif ? implode(', ', $aktif) : '-';

        // ============================================
        // 🔍 Join SHDK, Pendidikan, Pekerjaan
        // ============================================

        // SHDK
        $shdk = $this->db->table('tb_shdk')->where('id', $art['shdk'])->get()->getRowArray();
        $art['jenis_shdk'] = $shdk['jenis_shdk'] ?? '-';

        // Pendidikan
        $pk = $this->db->table('pendidikan_kk')->where('pk_id', $art['pendidikan_terakhir'])->get()->getRowArray();
        $art['pendidikan_nama'] = $pk['pk_nama'] ?? '-';

        // Pekerjaan
        $job = $this->db->table('tb_penduduk_pekerjaan')->where('pk_id', $art['pekerjaan'])->get()->getRowArray();
        $art['pekerjaan_nama'] = $job['pk_nama'] ?? '-';

        // ============================================
        return view('dtsen/pemeriksaan/detail_art', [
            'art' => $art
        ]);
    }

    /**
     * AJAX: render form edit KK (modal)
     */
    public function ajaxEditKK($id_kk)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) return $this->response->setStatusCode(403)->setBody('Akses ditolak');

        // ambil KK dengan RT/RW untuk validasi wilayah
        $kk = $this->db->table('dtsen_kk kk')
            ->select('kk.*, rt.rw, rt.rt')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->where('kk.id_kk', (int)$id_kk)
            ->get()->getRowArray();

        if (!$kk) return $this->response->setStatusCode(404)->setBody('Data KK tidak ditemukan');

        // cek wilayah user (boleh jika admin atau wilayah sesuai)
        [$rwUser, $rtUser] = $this->parseWilayah($user['wilayah_tugas'] ?? '');
        if (!empty($rwUser) && $rwUser !== $kk['rw']) {
            // jika user punya wilayah spesifik dan tidak cocok => tolak
            return $this->response->setStatusCode(403)->setBody('Akses wilayah tidak diperbolehkan');
        }

        // ambil count actual anggota
        $arts = $this->db->table('dtsen_art')->where('id_kk', $id_kk)->get()->getResultArray();

        return view('dtsen/pemeriksaan/edit_kk_modal', [
            'kk' => $kk,
            'arts_count' => count($arts)
        ]);
    }

    /**
     * AJAX: proses update KK (dari modal)
     */
    public function ajaxUpdateKK($id_kk)
    {
        log_message('error', "===> MASUK ajaxUpdateKK dengan ID: $id_kk");

        try {
            $user = $this->AuthModel->getUserId();
            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
            }

            $post = $this->request->getPost();
            $no_kk = trim((string)($post['no_kk'] ?? ''));
            $kepala = trim((string)($post['kepala_keluarga'] ?? ''));
            $alamat = trim((string)($post['alamat'] ?? ''));
            $program = trim((string)($post['program_bansos'] ?? ''));
            $kategori_adat = trim((string)($post['kategori_adat'] ?? 'Tidak'));

            // ambil KK untuk verifikasi wilayah & existing data
            $kk = $this->db->table('dtsen_kk kk')
                ->select('kk.*, rt.rw, rt.rt')
                ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
                ->where('kk.id_kk', (int)$id_kk)
                ->get()->getRowArray();

            if (!$kk) return $this->response->setJSON(['success' => false, 'message' => 'Data KK tidak ditemukan.'])->setStatusCode(404);

            // cek wilayah user
            [$rwUser, $rtUser] = $this->parseWilayah($user['wilayah_tugas'] ?? '');
            if (!empty($rwUser) && $rwUser !== $kk['rw']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses wilayah tidak diperbolehkan.'])->setStatusCode(403);
            }

            // Basic validation
            if ($no_kk === '' || $kepala === '') {
                return $this->response->setJSON(['success' => false, 'message' => 'No KK dan Kepala keluarga wajib diisi.']);
            }

            // update
            $updateData = [
                'no_kk' => $no_kk,
                'kepala_keluarga' => $kepala,
                'alamat' => $alamat,
                'program_bansos' => $program,
                'kategori_adat' => $kategori_adat,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user['nik'] ?? ($user['username'] ?? 'system')
            ];

            $this->db->table('dtsen_kk')->where('id_kk', (int)$id_kk)->update($updateData);

            return $this->response->setJSON(['success' => true, 'message' => 'Data KK berhasil diperbarui.']);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: render form edit ART (modal)
     */
    /**
     * AJAX: render form edit ART (modal)
     */
    public function ajaxEditART($id_art)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        }

        // --- Ambil data ART + KK + RT untuk validasi wilayah ---
        $art = $this->db->table('dtsen_art a')
            ->select('a.*, kk.no_kk, kk.kepala_keluarga, rt.rw, rt.rt')
            ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')
            ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
            ->where('a.id_art', (int)$id_art)
            ->get()->getRowArray();

        if (!$art) {
            return $this->response->setStatusCode(404)->setBody('Data ART tidak ditemukan');
        }

        // --- Validasi Wilayah User ---
        [$rwUser, $rtUser] = $this->parseWilayah($user['wilayah_tugas'] ?? '');
        if (!empty($rwUser) && $rwUser !== $art['rw']) {
            return $this->response->setStatusCode(403)->setBody('Akses wilayah tidak diperbolehkan');
        }

        // ======================================================
        // LOOKUP OPTIONS (SEMUA DIAMBIL DARI DATABASE)
        // ======================================================

        // SHDK dari tabel `tb_shdk`
        $shdk_list = $this->db->table('tb_shdk')
            ->select('id, jenis_shdk')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        // Pendidikan dari `pendidikan_kk`
        $pendidikan_list = $this->db->table('pendidikan_kk')
            ->select('pk_id, pk_nama')
            ->orderBy('pk_id', 'ASC')
            ->get()->getResultArray();

        // Pekerjaan dari `tb_penduduk_pekerjaan`
        $pekerjaan_list = $this->db->table('tb_penduduk_pekerjaan')
            ->select('pk_id, pk_nama')
            ->orderBy('pk_id', 'ASC')
            ->get()->getResultArray();

        // Disabilitas dari `tb_disabil_jenis`
        $disabilitas_list = $this->db->table('tb_disabil_jenis')
            ->select('dj_id, dj_keterangan')
            ->orderBy('dj_id', 'ASC')
            ->get()->getResultArray();

        // ======================================================
        // KIRIM VIEW MODAL
        // ======================================================

        return view('dtsen/pemeriksaan/edit_art_modal', [
            'art' => $art,
            'shdk_list' => $shdk_list,
            'pendidikan_list' => $pendidikan_list,
            'pekerjaan_list' => $pekerjaan_list,
            'disabilitas_list' => $disabilitas_list
        ]);
    }

    /**
     * AJAX: proses update ART (dari modal)
     */
    public function ajaxUpdateART($id_art)
    {
        try {
            $user = $this->AuthModel->getUserId();
            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
            }

            $post = $this->request->getPost();
            $newNik = trim((string)($post['nik'] ?? ''));
            $newNama = trim((string)($post['nama'] ?? ''));
            $newShdk = $post['shdk'] ?? null;
            $newJk = trim((string)($post['jenis_kelamin'] ?? ''));
            $newTgl = trim((string)($post['tanggal_lahir'] ?? ''));
            $newPendidikan = trim((string)($post['pendidikan_terakhir'] ?? ''));
            $newPekerjaan = trim((string)($post['pekerjaan'] ?? ''));
            $newDisabilitas = trim((string)($post['disabilitas'] ?? ''));
            $newStatusHamil = trim((string)($post['status_hamil'] ?? 'Tidak'));
            $newIbu = trim((string)($post['ibu_kandung'] ?? ''));
            $reasonNik = trim((string)($post['reason_nik'] ?? ''));

            // ambil existing ART
            $art = $this->db->table('dtsen_art a')
                ->select('a.*, kk.no_kk, rt.rw, rt.rt')
                ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')
                ->join('dtsen_rt rt', 'rt.id_rt = kk.id_rt', 'left')
                ->where('a.id_art', (int)$id_art)
                ->get()->getRowArray();

            if (!$art) return $this->response->setJSON(['success' => false, 'message' => 'Data ART tidak ditemukan.'])->setStatusCode(404);

            // cek wilayah user
            [$rwUser, $rtUser] = $this->parseWilayah($user['wilayah_tugas'] ?? '');
            if (!empty($rwUser) && $rwUser !== $art['rw']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses wilayah tidak diperbolehkan.'])->setStatusCode(403);
            }

            // validasi minimal
            if ($newNama === '') {
                return $this->response->setJSON(['success' => false, 'message' => 'Nama wajib diisi.']);
            }

            // jika NIK diubah, wajib alasan
            if ($newNik !== '' && $newNik !== ($art['nik'] ?? '')) {
                if ($reasonNik === '') {
                    return $this->response->setJSON(['success' => false, 'message' => 'Perubahan NIK harus disertai alasan.']);
                }
                // cek unik NIK
                $exists = $this->db->table('dtsen_art')->where('nik', $newNik)->where('id_art !=', $id_art)->countAllResults();
                if ($exists > 0) {
                    return $this->response->setJSON(['success' => false, 'message' => 'NIK sudah terdaftar pada ART lain.']);
                }
                // simpan log perubahan NIK ke tabel audit (opsional) -> contoh sederhana
                $this->db->table('dtsen_audit')->insert([
                    'entity' => 'dtsen_art',
                    'entity_id' => $id_art,
                    'field' => 'nik',
                    'old_value' => $art['nik'],
                    'new_value' => $newNik,
                    'reason' => $reasonNik,
                    'changed_by' => $user['nik'] ?? ($user['username'] ?? 'system'),
                    'changed_at' => date('Y-m-d H:i:s')
                ]);
            }

            // disallow edit foto via modal (keputusan)
            // jika client mengirim field foto => abaikan

            // lakukan update
            $update = [
                'nik' => $newNik !== '' ? $newNik : $art['nik'],
                'nama' => $newNama,
                'shdk' => !empty($newShdk) ? $newShdk : $art['shdk'],
                'jenis_kelamin' => $newJk,
                'tanggal_lahir' => $newTgl ?: null,
                'pendidikan_terakhir' => $newPendidikan,
                'pekerjaan' => $newPekerjaan,
                'disabilitas' => $newDisabilitas,
                'status_hamil' => $newStatusHamil,
                'ibu_kandung' => $newIbu,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user['nik'] ?? ($user['username'] ?? 'system'),
            ];

            $this->db->table('dtsen_art')->where('id_art', (int)$id_art)->update($update);

            return $this->response->setJSON(['success' => true, 'message' => 'Data ART berhasil diperbarui.']);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function ajaxDeleteKK($id_kk)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);

        $id = (int)$id_kk;
        $exists = $this->db->table('dtsen_kk')->where('id_kk', $id)->countAllResults();
        if (!$exists) return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan'])->setStatusCode(404);

        $this->db->table('dtsen_kk')->where('id_kk', $id)->update(['deleted_at' => date('Y-m-d H:i:s'), 'delete_reason' => 'Dihapus via pemeriksaan']);
        return $this->response->setJSON(['success' => true, 'message' => 'KK berhasil dihapus (soft)']);
    }

    public function ajaxDeleteART($id_art)
    {
        $user = $this->AuthModel->getUserId();
        if (!$user) return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);

        $id = (int)$id_art;
        $exists = $this->db->table('dtsen_art')->where('id_art', $id)->countAllResults();
        if (!$exists) return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan'])->setStatusCode(404);

        $this->db->table('dtsen_art')->where('id_art', $id)->update(['deleted_at' => date('Y-m-d H:i:s'), 'delete_reason' => 'Dihapus via pemeriksaan']);
        return $this->response->setJSON(['success' => true, 'message' => 'ART berhasil dihapus (soft)']);
    }
}
