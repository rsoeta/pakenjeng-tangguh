<?php

namespace App\Controllers\Pdtt;

use App\Controllers\BaseController;
use App\Models\Dtsen\Pdtt2025Model;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Pdtt2025 extends BaseController
{
    protected $pdttModel;

    public function __construct()
    {
        $this->pdttModel = new Pdtt2025Model();
    }

    // 🌐 Halaman Utama PDTT 2025
    public function index()
    {
        $roleId = session()->get('role_id') ?? 99;

        $data = [
            'title'    => 'Verivali PDTT 2025',
            'roleId'   => $roleId,
            'editable' => ($roleId <= 4)
        ];

        // Pastikan Kang Rian membuat file views/pdtt/2025/index.php setelah ini
        return view('pdtt/2025/index', $data);
    }

    // 📥 Fitur Import Excel (Hanya untuk Admin / Role <= 4)
    public function importExcel()
    {
        $roleId = session()->get('role_id');
        if ($roleId > 4) return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);

        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) return $this->response->setJSON(['status' => 'error', 'message' => 'File tidak valid.']);

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($sheetData); // Hapus Header

            $insertData = [];
            foreach ($sheetData as $row) {
                // 🚀 SILVER BULLET: Hapus semua karakter aneh/invisible dari NIK & KK agar JOIN Sinden sukses!
                $nikBersih = preg_replace('/[^0-9]/', '', $row['A'] ?? '');
                $kkBersih  = preg_replace('/[^0-9]/', '', $row['B'] ?? '');

                if (empty($nikBersih) || empty($row['D'])) continue;

                $insertData[] = [
                    'nik'              => $nikBersih,
                    'no_kk'            => $kkBersih,
                    'no_rekening'      => trim($row['C'] ?? ''),
                    'nama_pengurus'    => trim($row['D'] ?? ''),
                    'lembaga_penyalur' => trim($row['E'] ?? ''),
                    'kode_wilayah'     => trim($row['F'] ?? ''),
                    'prov'             => trim($row['G'] ?? ''),
                    'kab'              => trim($row['H'] ?? ''),
                    'kec'              => trim($row['I'] ?? ''),
                    'kel'              => trim($row['J'] ?? ''),
                    'alamat'           => trim($row['K'] ?? ''),
                    'rt'               => str_pad(trim($row['L'] ?? ''), 3, '0', STR_PAD_LEFT),
                    'rw'               => str_pad(trim($row['M'] ?? ''), 3, '0', STR_PAD_LEFT),
                    'keterangan'       => trim($row['N'] ?? ''),
                ];
            }

            if (!empty($insertData)) {
                $this->pdttModel->insertBatch($insertData);
                return $this->response->setJSON(['status' => 'success', 'message' => count($insertData) . ' Data diimport!']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Excel kosong/tidak valid.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    // 📊 Fetch DataTables
    public function datatable()
    {
        $request = \Config\Services::request();
        $session = session();

        $filters = [
            'rw'                => $request->getPost('filter_rw'),
            'rt'                => $request->getPost('filter_rt'),
            'status_verifikasi' => $request->getPost('filter_status'),
            'search'            => $request->getPost('search')['value'] ?? '',
            'role_id'           => $session->get('role_id'),
            'wilayah_tugas'     => $session->get('wilayah_tugas'),
            'kode_desa'         => $session->get('kode_desa'),
        ];

        $builder = $this->pdttModel->getDatatablesQuery($filters);

        $start  = $request->getPost('start');
        $length = $request->getPost('length');
        $totalRecords = $builder->countAllResults(false);

        if ($length != -1) {
            $builder->limit($length, $start);
        }
        $query = $builder->get()->getResultArray();

        $data = [];
        $no = $start + 1;

        foreach ($query as $row) {
            $aset = json_decode($row['kepemilikan_aset'] ?? '{}', true);

            $badgeStatus = ($row['status_verifikasi'] === 'Selesai')
                ? '<span class="badge bg-success">Selesai</span>' : '<span class="badge bg-warning text-dark">Pending</span>';

            $btnAction = '<button class="btn btn-sm btn-primary btn-verifikasi text-nowrap" data-id="' . $row['id'] . '"><i class="fas fa-search"></i> Verifikasi</button>';

            // 🚀 BUG FIX FOTO KKS: Cek foto_kepemilikan sebagai prioritas utama
            $fotoKksVal = !empty($row['foto_kepemilikan']) ? $row['foto_kepemilikan'] : ($row['foto_kks'] ?? '');

            $fotoKks = (!empty($fotoKksVal) && $fotoKksVal !== '-' && strpos($fotoKksVal, 'noimage') === false)
                ? '<span class="badge bg-success"><i class="fas fa-check"></i> Ada</span>'
                : '<span class="badge bg-danger">Kosong</span>';

            $fotoRumahVal = $row['foto_rumah'] ?? '';
            $fotoRumah = (!empty($fotoRumahVal) && $fotoRumahVal !== '-' && strpos($fotoRumahVal, 'noimage') === false)
                ? '<span class="badge bg-success"><i class="fas fa-check"></i> Ada</span>' : '<span class="badge bg-danger">Kosong</span>';

            $data[] = [
                $no++,
                esc($row['nama_pengurus']),
                esc($row['nik']),
                esc($row['no_kk']),
                esc($row['alamat']) . ' RT ' . esc($row['rt']) . ' RW ' . esc($row['rw']),
                esc($row['keterangan']),
                $fotoKks,
                esc($row['kepemilikan_rumah'] ?? '-'),
                esc($row['kondisi_rumah'] ?? '-'),
                $fotoRumah,
                esc($aset['mobil'] ?? 0),
                esc($aset['sepeda_motor'] ?? 0),
                esc($row['disabilitas_keluarga'] ?? '-'),
                $badgeStatus,
                $btnAction
            ];
        }

        return $this->response->setJSON([
            'draw'            => $request->getPost('draw'),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data'            => $data
        ]);
    }

    // 🚀 API Ambil Filter RW Bertingkat & Parsing Wilayah Tugas
    public function getFilterRw()
    {
        $db = \Config\Database::connect();
        $kodeDesa = session()->get('kode_desa');
        $roleId   = session()->get('role_id');
        $wilayahTugas = session()->get('wilayah_tugas') ?? '';

        // 🚀 BUG FIX: isNotNull() diganti dengan where('rw IS NOT NULL')
        $builder = $db->table('dtsen_rt')
            ->select('rw')
            ->distinct()
            ->where('kode_desa', $kodeDesa)
            ->where('rw !=', '')
            ->where('rw IS NOT NULL');

        if ($roleId == 4 && !empty($wilayahTugas)) {
            $wilayah = trim(str_replace('RW:', '', $wilayahTugas));
            $blokRW = preg_split('/[|;]/', $wilayah);
            $allowedRWs = [];
            foreach ($blokRW as $blok) {
                $blok = trim($blok);
                if ($blok === '') continue;
                [$rw,] = array_pad(explode(':', $blok), 2, '');

                $baseRw = (string)(int)$rw;
                $allowedRWs[] = $baseRw;
                $allowedRWs[] = str_pad($baseRw, 2, '0', STR_PAD_LEFT);
                $allowedRWs[] = str_pad($baseRw, 3, '0', STR_PAD_LEFT);
            }
            if (!empty($allowedRWs)) $builder->whereIn('rw', $allowedRWs);
        }

        $rwList = $builder->orderBy('rw', 'ASC')->get()->getResultArray();

        // Bersihkan Duplikasi karena Spasi
        $cleanRw = [];
        $seen = [];
        foreach ($rwList as $r) {
            $val = trim($r['rw']);
            if (!in_array($val, $seen) && $val !== '') {
                $seen[] = $val;
                $cleanRw[] = ['rw' => str_pad($val, 3, '0', STR_PAD_LEFT)];
            }
        }
        return $this->response->setJSON(['rw' => $cleanRw]);
    }

    // 🚀 API Ambil Filter RT Bertingkat & Parsing Wilayah Tugas
    public function getFilterRt($rw = null)
    {
        if (!$rw) return $this->response->setJSON(['rt' => []]);

        $db = \Config\Database::connect();
        $kodeDesa = session()->get('kode_desa');
        $roleId   = session()->get('role_id');
        $wilayahTugas = session()->get('wilayah_tugas') ?? '';

        $baseRwSelect = (string)(int)$rw;

        // 🚀 BUG FIX: isNotNull() diganti dengan where('rt IS NOT NULL')
        $builder = $db->table('dtsen_rt')
            ->select('rt')
            ->distinct()
            ->where('kode_desa', $kodeDesa)
            ->where('rt !=', '')
            ->where('rt IS NOT NULL');

        $builder->groupStart()
            ->where('rw', $baseRwSelect)
            ->orWhere('rw', str_pad($baseRwSelect, 2, '0', STR_PAD_LEFT))
            ->orWhere('rw', str_pad($baseRwSelect, 3, '0', STR_PAD_LEFT))
            ->groupEnd();

        if ($roleId == 4 && !empty($wilayahTugas)) {
            $wilayah = trim(str_replace('RW:', '', $wilayahTugas));
            $blokRW = preg_split('/[|;]/', $wilayah);
            $allowedRTs = [];

            foreach ($blokRW as $blok) {
                $blok = trim($blok);
                if ($blok === '') continue;
                [$rwTugas, $rtCSV] = array_pad(explode(':', $blok), 2, '');

                if ((string)(int)$rwTugas === $baseRwSelect) {
                    if ($rtCSV) {
                        foreach (explode(',', $rtCSV) as $r) {
                            $baseRt = (string)(int)trim($r);
                            $allowedRTs[] = $baseRt;
                            $allowedRTs[] = str_pad($baseRt, 2, '0', STR_PAD_LEFT);
                            $allowedRTs[] = str_pad($baseRt, 3, '0', STR_PAD_LEFT);
                        }
                    }
                }
            }
            if (!empty($allowedRTs)) $builder->whereIn('rt', $allowedRTs);
        }

        $rtList = $builder->orderBy('rt', 'ASC')->get()->getResultArray();

        $cleanRt = [];
        $seen = [];
        foreach ($rtList as $r) {
            $val = trim($r['rt']);
            if (!in_array($val, $seen) && $val !== '') {
                $seen[] = $val;
                $cleanRt[] = ['rt' => str_pad($val, 3, '0', STR_PAD_LEFT)];
            }
        }
        return $this->response->setJSON(['rt' => $cleanRt]);
    }

    // 🔍 Ambil Detail untuk Modal Verifikasi
    public function getDetail($id)
    {
        $data = $this->pdttModel->find($id);
        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    }

    // 💾 Simpan Hasil Verifikasi PDTT
    public function saveVerifikasi()
    {
        try {
            $id = $this->request->getPost('id');
            if (!$id) throw new \Exception("ID PDTT tidak valid.");

            $penghasilan = $this->request->getPost('jumlah_penghasilan');
            $penghasilan = str_replace(['.', ','], '', $penghasilan ?? '0');

            $dataUpdate = [
                'kesesuaian'         => $this->request->getPost('kesesuaian'),
                'penjelasan'         => $this->request->getPost('penjelasan'),
                'pekerjaan'          => $this->request->getPost('pekerjaan'),
                'jenis_usaha'        => $this->request->getPost('jenis_usaha'),
                'jumlah_penghasilan' => (float) $penghasilan,
                'status_verifikasi'  => 'Selesai',
                'verified_by'        => session()->get('id') ?? session()->get('id_user'),
                'verified_at'        => date('Y-m-d H:i:s')
            ];

            // 📸 Handle Upload Foto Listrik
            $fotoListrik = $this->request->getFile('foto_listrik');
            if ($fotoListrik && $fotoListrik->isValid() && !$fotoListrik->hasMoved()) {
                $newName = $fotoListrik->getRandomName();
                $fotoListrik->move('data/pdtt/2025', $newName);
                $dataUpdate['foto_listrik'] = 'data/pdtt/2025/' . $newName;
            }

            // 📸 Handle Upload Foto Slip Gaji
            $fotoSlip = $this->request->getFile('foto_slip_gaji');
            if ($fotoSlip && $fotoSlip->isValid() && !$fotoSlip->hasMoved()) {
                $newName = $fotoSlip->getRandomName();
                $fotoSlip->move('data/pdtt/2025', $newName);
                $dataUpdate['foto_slip_gaji'] = 'data/pdtt/2025/' . $newName;
            }

            $this->pdttModel->update($id, $dataUpdate);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Hasil verifikasi berhasil disimpan!']);
        } catch (\Throwable $e) {
            log_message('error', '[PDTT Save] ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }
}
