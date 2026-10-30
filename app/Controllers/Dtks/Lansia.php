<?php

namespace App\Controllers\Dtks;

use App\Controllers\BaseController;
use App\Models\DesaModel;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\LansiaModel;
use App\Traits\WilayahFilterTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Lansia extends BaseController
{
    use WilayahFilterTrait;

    protected $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        helper(['form', 'url']);
    }

    private function formatTitleCase(?string $str): string
    {
        $str = trim((string)$str);
        if ($str === '' || $str === '-') {
            return '-';
        }
        return mb_convert_case(mb_strtolower($str, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    private function formatAlamat(?string $str): string
    {
        $str = trim((string)$str);
        if ($str === '' || $str === '-') {
            return '-';
        }

        return mb_strtoupper($str, 'UTF-8');
    }

    private function formatPendidikan(?string $str): string
    {
        $str = trim((string)$str);
        if ($str === '' || $str === '-') {
            return '-';
        }

        return mb_strtoupper($str, 'UTF-8');
    }

    private function getUserContext(): array
    {
        $session  = session();
        $userInfo = $this->authModel->getUserId();

        return [
            'role_id'       => (int)($session->get('role_id') ?? ($userInfo['role_id'] ?? 4)),
            'kode_desa'     => $session->get('kode_desa') ?? ($userInfo['kode_desa'] ?? '32.05.33.2006'),
            'level'         => $session->get('level') ?? ($userInfo['level'] ?? ''),
            'wilayah_tugas' => $session->get('wilayah_tugas') ?? ($userInfo['wilayah_tugas'] ?? ''),
            'fullname'      => $session->get('fullname') ?? ($userInfo['fullname'] ?? 'Petugas'),
        ];
    }

    public function index()
    {
        $status = session()->get('status');
        if ($status !== null && (int)$status === 0) {
            return view('lockscreen', ['title' => 'Access denied']);
        }

        $user         = $this->getUserContext();
        $roleId       = $user['role_id'];
        $kodeDesa     = $user['kode_desa'];
        $wilayahTugas = $user['wilayah_tugas'];
        $levelUser    = $user['level'];

        $desKels = new DesaModel();
        $db      = \Config\Database::connect();

        // 🔐 Karantina RW untuk Petugas (role_id >= 4) via WilayahFilterTrait
        if ($roleId >= 4) {
            $allowedRw = self::getAllowedRwList($wilayahTugas, $levelUser);
            $rwList = array_map(function ($rw) {
                return ['no_rw' => $rw];
            }, $allowedRw);
        } else {
            $rwBuilder = $db->table('dtsen_rt')
                ->select('rw as no_rw')
                ->distinct()
                ->where('rw !=', '')
                ->where('rw !=', '000');

            if (!empty($kodeDesa)) {
                $rwBuilder->where('kode_desa', $kodeDesa);
            }

            $rwList = $rwBuilder->orderBy('rw', 'asc')->get()->getResultArray();
        }

        $data = [
            'title'     => 'Data Lansia / Jompo',
            'title2'    => 'Data Lansia & Jompo DTSEN',
            'desKels'   => $desKels->orderBy('nama_desa', 'asc')->findAll(),
            'Rw'        => $rwList,
            'userDesa'  => $kodeDesa,
            'userRole'  => $roleId
        ];

        return view('dtks/data/lansia/index', $data);
    }

    public function data()
    {
        try {
            $user        = $this->getUserContext();
            $roleId      = $user['role_id'];
            $sessionDesa = $user['kode_desa'];

            // Role 1 & 2 (Super Admin / Admin) boleh memilih desa lain lewat filter jika diisi
            $postDesa = $this->request->getPost('desa');
            if ($roleId < 3 && !empty($postDesa)) {
                $kodeDesa = $postDesa;
            } else {
                $kodeDesa = !empty($sessionDesa) ? $sessionDesa : '32.05.33.2006';
            }

            $rawSearch = $this->request->getPost('search');
            $searchVal = is_array($rawSearch) ? ($rawSearch['value'] ?? '') : (is_string($rawSearch) ? $rawSearch : '');

            $filter = [
                'kode_desa'     => $kodeDesa,
                'rw'            => $this->request->getPost('rw'),
                'kategori'      => $this->request->getPost('kategori'),
                'search'        => trim((string)$searchVal),
                'wilayah_tugas' => $user['wilayah_tugas'],
                'level'         => $user['level'],
                'role_id'       => $roleId
            ];

            $model = new LansiaModel();

            $csrfName = csrf_token();
            $csrfHash = csrf_hash();

            $listing       = $model->get_datatables($filter, $roleId);
            $jumlah_semua  = $model->jumlah_semua($filter, $roleId);
            $jumlah_filter = $model->jumlah_filter($filter, $roleId);

            $totalRecords    = is_object($jumlah_semua) ? ($jumlah_semua->jml ?? 0) : (int)$jumlah_semua;
            $filteredRecords = is_object($jumlah_filter) ? ($jumlah_filter->jml ?? 0) : (int)$jumlah_filter;

            $data = [];
            $no   = (int)($this->request->getPost('start') ?? 0);
            foreach ($listing as $key) {
                $no++;
                $row = [];
                $row[] = $no;

                // NIK (Sensor & Tombol Salin)
                $nikAsli = trim((string)($key->nik ?? ''));
                if (empty($nikAsli) || $nikAsli === '-') {
                    $nikCol = '<span class="text-muted">-</span>';
                } else {
                    $len = strlen($nikAsli);
                    $nikMasked = ($len === 16)
                        ? substr($nikAsli, 0, 6) . '******' . substr($nikAsli, 12, 4)
                        : (($len > 8) ? substr($nikAsli, 0, 8) . str_repeat('*', $len - 8) : $nikAsli);

                    $nikCol = '<div class="d-flex align-items-center justify-content-between px-1">' .
                        '<span class="text-primary font-weight-bold" style="cursor: pointer; border-bottom: 1px dashed #007bff;" ' .
                        'data-masked="' . esc($nikMasked, 'attr') . '" data-full="' . esc($nikAsli, 'attr') . '" ' .
                        'onmouseenter="this.innerText=this.getAttribute(\'data-full\')" ' .
                        'onmouseleave="this.innerText=this.getAttribute(\'data-masked\')" ' .
                        'onclick="this.innerText=this.getAttribute(\'data-full\')" ' .
                        'title="Sorot atau klik untuk melihat NIK lengkap">' . esc($nikMasked) . '</span>' .
                        '<button type="button" class="btn btn-xs btn-outline-secondary border-0 p-0 ml-1 btnCopyNik" ' .
                        'data-value="' . esc($nikAsli, 'attr') . '" title="Salin NIK">' .
                        '<i class="fas fa-copy text-secondary"></i></button>' .
                        '</div>';
                }
                $row[] = $nikCol;

                // Nama
                $row[] = '<strong>' . esc(mb_strtoupper($key->nama, 'UTF-8')) . '</strong>';

                // Alamat (Capitalize Each Word)
                $row[] = esc($this->formatAlamat($key->alamat));

                // RT / RW
                $rtRw = esc($key->rt ?? '000') . ' / ' . esc($key->rw ?? '000');
                $row[] = '<span class="badge badge-light bg-light text-dark border">' . $rtRw . '</span>';

                // Usia
                $row[] = '<span class="badge badge-info bg-info text-white">' . esc($key->usia) . ' THN</span>';

                // Jenis Kelamin
                if ($key->jenis_kelamin === 'L') {
                    $row[] = '<span class="badge badge-primary bg-primary text-white" title="LAKI-LAKI">L</span>';
                } elseif ($key->jenis_kelamin === 'P') {
                    $row[] = '<span class="badge badge-danger bg-danger text-white" title="PEREMPUAN">P</span>';
                } else {
                    $row[] = '-';
                }

                // Tempat Tinggal / Keadaan Rumah
                $statusRumah   = (!empty($key->status_kepemilikan) && $key->status_kepemilikan !== '-') ? $key->status_kepemilikan : '';
                $kategoriRumah = (!empty($key->kategori_rumah) && $key->kategori_rumah !== '-') ? $key->kategori_rumah : '';
                if ($statusRumah && $kategoriRumah) {
                    $rumahText = $statusRumah . ' / ' . $kategoriRumah;
                } elseif ($statusRumah) {
                    $rumahText = $statusRumah;
                } elseif ($kategoriRumah) {
                    $rumahText = $kategoriRumah;
                } else {
                    $rumahText = '-';
                }
                $row[] = esc(mb_strtoupper($rumahText, 'UTF-8'));

                // Pendidikan Terakhir (Capitalize Each Word dengan SD/SMP/SMA kapital)
                $row[] = esc($this->formatPendidikan($key->pendidikan));

                // Kategori & Keterangan
                $kat = (strtoupper($key->kategori_lansia ?? '') === 'JOMPO') ? 'JOMPO' : 'LANSIA';
                if ($kat === 'JOMPO') {
                    $katBadge = '<span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-wheelchair mr-1"></i>JOMPO</span>';
                } else {
                    $katBadge = '<span class="badge badge-success px-2 py-1"><i class="fas fa-user-check mr-1"></i>LANSIA</span>';
                }
                if (!empty($key->keterangan_lansia)) {
                    $katBadge .= '<div class="small text-muted font-italic mt-1"><i class="fas fa-info-circle mr-1 text-xs"></i>' . esc(mb_strtoupper($key->keterangan_lansia, 'UTF-8')) . '</div>';
                }
                $row[] = $katBadge;

                // Aksi
                $btnAksi = '<button type="button" class="btn btn-xs btn-outline-primary btnPilihKategori" ' .
                    'data-id="' . $key->id_art . '" ' .
                    'data-nama="' . esc($key->nama, 'attr') . '" ' .
                    'data-nik="' . esc($key->nik ?? '', 'attr') . '" ' .
                    'data-usia="' . esc($key->usia, 'attr') . '" ' .
                    'data-kategori="' . esc($kat, 'attr') . '" ' .
                    'data-keterangan="' . esc($key->keterangan_lansia ?? '', 'attr') . '" ' .
                    'title="PILIH STATUS">' .
                    '<i class="fas fa-user-edit mr-1"></i>PILIH STATUS</button>';
                $row[] = $btnAksi;

                $data[] = $row;
            }

            $output = [
                "draw"            => intval($this->request->getPost('draw') ?? 1),
                "recordsTotal"    => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data"            => $data,
            ];
            $output[$csrfName] = $csrfHash;

            return $this->response->setJSON($output);
        } catch (\Throwable $e) {
            log_message('error', '[Lansia::data] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                "draw"            => intval($this->request->getPost('draw') ?? 1),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => [],
                "error"           => "Error di server: " . $e->getMessage() . " (" . basename($e->getFile()) . ":" . $e->getLine() . ")",
                csrf_token()      => csrf_hash()
            ]);
        }
    }

    public function save_status()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        try {
            $id_art     = (int)$this->request->getPost('id_art');
            $kategori   = $this->request->getPost('kategori');
            $keterangan = trim((string)$this->request->getPost('keterangan'));

            if (!$id_art || !in_array($kategori, ['Lansia', 'Jompo'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data tidak valid. Kategori harus Lansia atau Jompo.'
                ]);
            }

            $user     = $this->getUserContext();
            $username = $user['fullname'];

            $model = new LansiaModel();
            $saved = $model->save_status_lansia($id_art, $kategori, $keterangan, $username);

            if ($saved) {
                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => "Status berhasil diperbarui menjadi {$kategori}.",
                    'csrf_name' => csrf_token(),
                    'csrf_hash' => csrf_hash()
                ]);
            }

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan status kategori.'
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[Lansia::save_status] ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Endpoint AJAX Copyediting untuk memperbaiki ejaan (typo), tanda baca,
     * kapitalisasi, pemilihan kata baku, dan struktur kalimat efektif.
     */
    public function copyedit()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        try {
            $rawText = trim((string)$this->request->getPost('text'));
            if ($rawText === '' || $rawText === '-') {
                return $this->response->setJSON([
                    'status'    => 'error',
                    'message'   => 'Teks keterangan masih kosong.',
                    'csrf_name' => csrf_token(),
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // 1. Coba gunakan AI API (jika GEMINI_API_KEY diset di .env)
            $result = $this->tryExternalAiCopyedit($rawText);

            // 2. Jika tidak ada API key atau jaringan gagal, gunakan mesin Smart Indonesian Copyediting lokal
            if (empty($result)) {
                $result = $this->smartCopyeditIndonesia($rawText);
            }

            return $this->response->setJSON([
                'status'    => 'success',
                'original'  => $rawText,
                'result'    => $result,
                'csrf_name' => csrf_token(),
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[Lansia::copyedit] ' . $e->getMessage());
            return $this->response->setJSON([
                'status'    => 'error',
                'message'   => 'Gagal memperbaiki teks: ' . $e->getMessage(),
                'csrf_name' => csrf_token(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    /**
     * Opsional: Panggil Gemini API jika GEMINI_API_KEY tersedia di .env
     */
    private function tryExternalAiCopyedit(string $text): ?string
    {
        $apiKey = trim((string)(getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? '')));
        if ($apiKey === '' || !function_exists('curl_init')) {
            return null;
        }

        $prompt = "Lakukan copyediting bahasa Indonesia yang baik, benar (EYD/KBBI), ringkas, dan efektif untuk catatan kondisi lansia/jompo berikut. "
            . "Perbaiki semua kesalahan ejaan (typo), tanda baca, kapitalisasi (gunakan Sentence case, bukan huruf kapital semua), pemilihan kata yang salah, dan struktur kalimat yang rancu tanpa mengubah makna aslinya. "
            . "Berikan HANYA kalimat hasil perbaikannya saja tanpa tanda kutip dan tanpa penjelasan tambahan:\n\n"
            . $text;

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . urlencode($apiKey);
        $payload = json_encode([
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 250,
            ]
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200 && $resp) {
            $json = json_decode($resp, true);
            $out  = trim((string)($json['candidates'][0]['content']['parts'][0]['text'] ?? ''));
            $out  = trim($out, " \t\n\r\0\x0B\"'");
            if ($out !== '') {
                return $out;
            }
        }

        return null;
    }

    /**
     * Mesin Copyediting Bahasa Indonesia Cerdas (Kamus Typo + Fuzzy Matching Levenshtein + Restrukturisasi Kalimat Efektif)
     */
    private function smartCopyeditIndonesia(string $text): string
    {
        $text = trim($text);
        if ($text === '' || $text === '-') {
            return $text;
        }

        // 1. Normalisasi awal: huruf kecil semua, rapikan spasi berlebih
        $s = mb_strtolower($text, 'UTF-8');
        $s = preg_replace('/\s+/', ' ', $s);

        // 2. Kamus koreksi typo langsung & kata tidak baku (spesifik konteks sosial/kesehatan lansia)
        $directMap = [
            'puya' => 'punya',
            'pnya' => 'punya',
            'pny' => 'punya',
            'mpunyai' => 'mempunyai',
            'peyakit' => 'penyakit',
            'pnyakit' => 'penyakit',
            'penyaket' => 'penyakit',
            'pnykt' => 'penyakit',
            'untik' => 'untuk',
            'untk' => 'untuk',
            'utk' => 'untuk',
            'tuk' => 'untuk',
            'kkdisi' => 'kondisi',
            'kndisi' => 'kondisi',
            'kondsi' => 'kondisi',
            'kndsi' => 'kondisi',
            'kodisi' => 'kondisi',
            'pisik' => 'fisik',
            'psik' => 'fisik',
            'fisikna' => 'fisiknya',
            'pisikna' => 'fisiknya',
            'bueat' => 'buat',
            'bwt' => 'buat',
            'berjln' => 'berjalan',
            'brjalan' => 'berjalan',
            'berjlan' => 'berjalan',
            'jln' => 'jalan',
            'aga' => 'agak',
            'agk' => 'agak',
            'rada' => 'agak',
            'penagan' => 'penanganan',
            'penaganan' => 'penanganan',
            'pnanganan' => 'penanganan',
            'penangan' => 'penanganan',
            'husus' => 'khusus',
            'kusus' => 'khusus',
            'khusu' => 'khusus',
            'khuss' => 'khusus',
            'jga' => 'juga',
            'jg' => 'juga',
            'jd' => 'jadi',
            'jdi' => 'jadi',
            'hrs' => 'harus',
            'hrus' => 'harus',
            'krn' => 'karena',
            'karna' => 'karena',
            'kerna' => 'karena',
            'yg' => 'yang',
            'dgn' => 'dengan',
            'dg' => 'dengan',
            'sm' => 'sama',
            'sdh' => 'sudah',
            'udh' => 'sudah',
            'udah' => 'sudah',
            'blm' => 'belum',
            'blom' => 'belum',
            'tdk' => 'tidak',
            'tak' => 'tidak',
            'gak' => 'tidak',
            'ga' => 'tidak',
            'nggak' => 'tidak',
            'enggak' => 'tidak',
            'bs' => 'bisa',
            'bsa' => 'bisa',
            'msh' => 'masih',
            'msih' => 'masih',
            'sgt' => 'sangat',
            'bgt' => 'sekali',
            'banget' => 'sekali',
            'dlm' => 'dalam',
            'pd' => 'pada',
            'dr' => 'dari',
            'kpd' => 'kepada',
            'tp' => 'tetapi',
            'tapi' => 'tetapi',
            'kl' => 'kalau',
            'kalo' => 'kalau',
            'klu' => 'kalau',
            'sbg' => 'sebagai',
            'spt' => 'seperti',
            'aktipitas' => 'aktivitas',
            'aktifitas' => 'aktivitas',
            'aktvts' => 'aktivitas',
            'struk' => 'stroke',
            'strok' => 'stroke',
            'setruk' => 'stroke',
            'syaraf' => 'saraf',
            'sarap' => 'saraf',
            'rematik' => 'reumatik',
            'reumatik' => 'reumatik',
            'asamurat' => 'asam urat',
            'darahtinggi' => 'hipertensi',
            'diabet' => 'diabetes',
            'pikun' => 'demensia (pikun)',
            'ngurus' => 'mengurus',
            'ngurusin' => 'mengurus',
            'ngebantu' => 'membantu',
            'susah' => 'sulit',
        ];

        // Kosakata baku referensi untuk fuzzy matching Levenshtein (typo 1-2 huruf)
        $standardVocab = [
            'menderita', 'penyakit', 'asam', 'urat', 'sehingga', 'untuk', 'kondisi', 'fisik',
            'berjalan', 'agak', 'sulit', 'harus', 'penanganan', 'khusus', 'juga', 'lansia',
            'jompo', 'sehat', 'bugar', 'lemah', 'renta', 'lumpuh', 'stroke', 'diabetes',
            'hipertensi', 'katarak', 'rabun', 'tuli', 'pendengaran', 'penglihatan', 'terganggu',
            'kurang', 'jelas', 'tinggal', 'sendiri', 'sendirian', 'keluarga', 'bantuan',
            'aktivitas', 'mandiri', 'tempat', 'tidur', 'tongkat', 'kursi', 'roda', 'sakit',
            'menahun', 'kronis', 'parah', 'butuh', 'membutuhkan', 'memerlukan', 'perawatan',
            'medis', 'rutin', 'kontrol', 'dokter', 'puskesmas', 'bekerja', 'serabutan',
            'ringan', 'berat', 'bicara', 'mengurus', 'merawat', 'pendamping', 'kesulitan',
            'gangguan', 'fungsi', 'tubuh', 'menurun', 'usia', 'lanjut', 'faktor'
        ];

        $tokens = preg_split('/(\s+|[,.!?;:\/()-])/', $s, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($tokens as &$tok) {
            if (preg_match('/^[a-z0-9]+$/', $tok)) {
                if (isset($directMap[$tok])) {
                    $tok = $directMap[$tok];
                } elseif (strlen($tok) >= 5 && !in_array($tok, $standardVocab, true)) {
                    $bestWord = null;
                    $bestDist = 99;
                    foreach ($standardVocab as $vocab) {
                        if (abs(strlen($tok) - strlen($vocab)) > 2) {
                            continue;
                        }
                        if ($tok[0] !== $vocab[0] && substr($tok, 0, 2) !== substr($vocab, 0, 2)) {
                            continue;
                        }
                        $dist = levenshtein($tok, $vocab);
                        $maxAllowed = (strlen($vocab) >= 7) ? 2 : 1;
                        if ($dist <= $maxAllowed && $dist < $bestDist) {
                            $bestDist = $dist;
                            $bestWord = $vocab;
                        }
                    }
                    if ($bestWord !== null) {
                        $tok = $bestWord;
                    }
                }
            }
        }
        unset($tok);

        $s = implode('', $tokens);

        // 3. Perbaikan gabungan imbuhan "di " yang seharusnya serangkai pada kata kerja pasif
        $passiveVerbs = ['bantu', 'rawat', 'urus', 'dampingi', 'papah', 'gendong', 'bawa', 'periksa', 'obati', 'tangani', 'pantau', 'awasi'];
        foreach ($passiveVerbs as $v) {
            $s = preg_replace('/\bdi\s+' . $v . '\b/', 'di' . $v, $s);
        }

        // 4. Transformasi Frasa & Struktur Kalimat Efektif (Copyediting Sintaksis)
        $phraseRules = [
            '/^(?:punya|mempunyai|memiliki|ada)\s+penyakit\b/' => 'menderita penyakit',
            '/\b(?:punya|mempunyai)\s+penyakit\b/' => 'menderita penyakit',
            '/^(?:kena|terkena)\s+penyakit\b/' => 'menderita penyakit',
            '/^(?:kena|terkena)\s+(stroke|diabetes|hipertensi|katarak|asam urat|reumatik|lumpuh)\b/' => 'menderita $1',

            // Struktur rancu "jadi untuk kondisi fisik buat berjalan agak sulit"
            '/\bjadi\s+untuk\s+kondisi\s+fisik\s+(?:buat|untuk)\s+berjalan\s+(?:agak|cukup)?\s*sulit\b/' => 'sehingga kondisi fisik cukup sulit untuk berjalan',
            '/\buntuk\s+kondisi\s+fisik\s+(?:buat|untuk)\s+berjalan\s+(?:agak|cukup)?\s*sulit\b/' => 'kondisi fisik cukup sulit untuk berjalan',
            '/\bkondisi\s+fisik\s+(?:buat|untuk)\s+berjalan\s+(?:agak|cukup)?\s*sulit\b/' => 'kondisi fisik cukup sulit untuk berjalan',
            '/\b(?:buat|untuk)\s+berjalan\s+(?:agak|cukup)\s+sulit\b/' => 'cukup sulit untuk berjalan',
            '/\b(?:buat|untuk)\s+berjalan\s+sulit\b/' => 'sulit untuk berjalan',
            '/\b(?:agak|cukup)\s+sulit\s+(?:buat|untuk)\s+berjalan\b/' => 'cukup sulit untuk berjalan',

            // Kata "buat" non-baku sebelum kata kerja -> "untuk"
            '/\bbuat\s+(berjalan|bergerak|beraktivitas|berdiri|duduk|melihat|mendengar|bicara|berbicara|makan|bekerja)\b/' => 'untuk $1',

            // Pengulangan kata penghubung "jadi ... jadi ..." & klausa penanganan khusus
            '/\bjadi\s+harus\s+ada\s+penanganan\s+khusus\s+juga\b/' => 'dan memerlukan penanganan khusus',
            '/\bjadi\s+harus\s+ada\s+penanganan\s+khusus\b/' => 'serta memerlukan penanganan khusus',
            '/\bharus\s+ada\s+penanganan\s+khusus\s+juga\b/' => 'memerlukan penanganan khusus',
            '/\bharus\s+ada\s+penanganan\s+khusus\b/' => 'memerlukan penanganan khusus',
            '/\bbutuh\s+penanganan\s+khusus\b/' => 'memerlukan penanganan khusus',

            // Frasa umum lainnya pada keterangan lansia/jompo
            '/\btidak\s+bisa\s+(?:jalan|berjalan)\s+sendiri\b/' => 'tidak dapat berjalan secara mandiri',
            '/\btidak\s+bisa\s+beraktivitas\s+sendiri\b/' => 'tidak dapat beraktivitas secara mandiri',
            '/\bharus\s+dibantu\b/' => 'sehingga perlu dibantu',
            '/\bmasih\s+bisa\s+kerja\b/' => 'masih dapat bekerja',
            '/\btidak\s+ada\s+yang\s+mengurus\b/' => 'tanpa pendamping yang mengurus',
            '/\b(?:sering\s+)?sakit[\s-]+sakitan\b/' => 'sering sakit-sakitan',
            '/\baktivitas\s+di\s+tempat\s+tidur\s+saja\b/' => 'hanya dapat beraktivitas di tempat tidur',
        ];

        foreach ($phraseRules as $pattern => $replacement) {
            $s = preg_replace($pattern, $replacement, $s);
        }

        // Ubah kata penghubung "jadi" di tengah kalimat menjadi "sehingga" / "dan"
        $jadiCount = 0;
        $s = preg_replace_callback('/\bjadi\b/', function () use (&$jadiCount) {
            $jadiCount++;
            return ($jadiCount === 1) ? 'sehingga' : 'dan';
        }, $s);

        // Hapus kata "juga" yang menggantung di akhir kalimat jika sudah efektif
        $s = preg_replace('/\b(memerlukan|membutuhkan)\s+([^,.]+?)\s+juga$/', '$1 $2', $s);

        // Rapikan spasi di sekitar tanda baca
        $s = preg_replace('/\s+([,.!?;:])/', '$1', $s);
        $s = preg_replace('/([,.!?;:])(?=[a-zA-Z])/', '$1 ', $s);
        $s = preg_replace('/\s+/', ' ', trim($s));

        // Kapitalisasi huruf pertama pada setiap kalimat (Sentence case)
        $s = preg_replace_callback('/(?:^|[.!?]\s+)([a-z])/', function ($m) {
            return mb_strtoupper($m[0], 'UTF-8');
        }, $s);

        // Pastikan singkatan medis/umum tetap kapital bila ada
        $acronyms = ['tbc' => 'TBC', 'hiv' => 'HIV', 'bpjs' => 'BPJS', 'rt' => 'RT', 'rw' => 'RW', 'odgj' => 'ODGJ'];
        foreach ($acronyms as $low => $up) {
            $s = preg_replace('/\b' . $low . '\b/i', $up, $s);
        }

        // Tambahkan titik di akhir kalimat jika belum ada tanda baca akhir
        if (!preg_match('/[.!?]$/', $s)) {
            $s .= '.';
        }

        return $s;
    }

    public function export_excel()
    {
        try {
            $user        = $this->getUserContext();
            $roleId      = $user['role_id'];
            $sessionDesa = $user['kode_desa'];

            $getDesa = $this->request->getGet('desa');
            if ($roleId < 3 && !empty($getDesa)) {
                $kodeDesa = $getDesa;
            } else {
                $kodeDesa = !empty($sessionDesa) ? $sessionDesa : '32.05.33.2006';
            }

            $filter = [
                'kode_desa'     => $kodeDesa,
                'rw'            => $this->request->getGet('rw'),
                'kategori'      => $this->request->getGet('kategori'),
                'search'        => trim((string)($this->request->getGet('search') ?? '')),
                'wilayah_tugas' => $user['wilayah_tugas'],
                'level'         => $user['level'],
                'role_id'       => $roleId
            ];

            $model = new LansiaModel();
            $data  = $model->get_export_data($filter, $roleId);

            // Inisialisasi PhpSpreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Lansia Jompo');

            // KOP Sesuai Permintaan
            $sheet->setCellValue('A1', 'DATA LANSIA/JOMPO');
            $sheet->setCellValue('A2', 'DESA PASIRLANGU KECAMATAN PAKENJENG KABUPATEN GARUT');
            $sheet->setCellValue('A3', 'TAHUN 2026');

            $sheet->mergeCells('A1:J1');
            $sheet->mergeCells('A2:J2');
            $sheet->mergeCells('A3:J3');

            $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);

            // Header Tabel Kolom (Baris 5) Sesuai Permintaan User:
            // No | NIK | Nama | Alamat | Rt/Rw | Usia (Tahun) | Jenis Kelamin | Tempat Tinggal/Keadaan Rumah | Pendidikan Terakhir | Keterangan
            $headers = [
                'A5' => 'NO',
                'B5' => 'NIK',
                'C5' => 'NAMA',
                'D5' => 'ALAMAT',
                'E5' => 'RT/RW',
                'F5' => 'USIA (TAHUN)',
                'G5' => 'JENIS KELAMIN',
                'H5' => 'TEMPAT TINGGAL / KEADAAN RUMAH',
                'I5' => 'PENDIDIKAN TERAKHIR',
                'J5' => 'KETERANGAN'
            ];

            foreach ($headers as $cell => $val) {
                $sheet->setCellValue($cell, $val);
            }

            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E3E5']
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ];
            $sheet->getStyle('A5:J5')->applyFromArray($headerStyle);
            $sheet->getRowDimension(5)->setRowHeight(26);

            // Isi Data (Mulai Baris 6)
            $rowNum = 6;
            $no = 1;
            foreach ($data as $item) {
                // Tempat tinggal / keadaan rumah
                $statusRumah   = (!empty($item['status_kepemilikan']) && $item['status_kepemilikan'] !== '-') ? $item['status_kepemilikan'] : '';
                $kategoriRumah = (!empty($item['kategori_rumah']) && $item['kategori_rumah'] !== '-') ? $item['kategori_rumah'] : '';
                if ($statusRumah && $kategoriRumah) {
                    $rumahText = $statusRumah . ' / ' . $kategoriRumah;
                } elseif ($statusRumah) {
                    $rumahText = $statusRumah;
                } elseif ($kategoriRumah) {
                    $rumahText = $kategoriRumah;
                } else {
                    $rumahText = '-';
                }

                // Jenis Kelamin
                $jk = ($item['jenis_kelamin'] === 'L') ? 'LAKI-LAKI' : (($item['jenis_kelamin'] === 'P') ? 'PEREMPUAN' : '-');

                // Keterangan
                $kat = (strtoupper($item['kategori_lansia'] ?? '') === 'JOMPO') ? 'JOMPO' : 'LANSIA';
                $ket = $kat;
                if (!empty($item['keterangan_lansia'])) {
                    $ket .= ' (' . $item['keterangan_lansia'] . ')';
                }

                $rtRw = ($item['rt'] ?? '000') . '/' . ($item['rw'] ?? '000');

                $sheet->setCellValue('A' . $rowNum, $no);
                $sheet->setCellValueExplicit('B' . $rowNum, trim((string)($item['nik'] ?? '')), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowNum, mb_strtoupper((string)$item['nama'], 'UTF-8'));
                $sheet->setCellValue('D' . $rowNum, $this->formatAlamat($item['alamat']));
                $sheet->setCellValueExplicit('E' . $rowNum, $rtRw, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('F' . $rowNum, (int)$item['usia']);
                $sheet->setCellValue('G' . $rowNum, $jk);
                $sheet->setCellValue('H' . $rowNum, mb_strtoupper($rumahText, 'UTF-8'));
                $sheet->setCellValue('I' . $rowNum, $this->formatPendidikan($item['pendidikan']));
                $sheet->setCellValue('J' . $rowNum, mb_strtoupper($ket, 'UTF-8'));

                $rowNum++;
                $no++;
            }

            // Terapkan border dan format pada isi tabel jika ada data
            $lastRow = max(5, $rowNum - 1);
            if ($lastRow >= 6) {
                $dataStyle = [
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ];
                $sheet->getStyle('A6:J' . $lastRow)->applyFromArray($dataStyle);

                // Alignment spesifik
                $sheet->getStyle('A6:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E6:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Auto size columns A to J
            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $fileName = 'DATA_LANSIA_JOMPO_PASIRLANGU_2026.xlsx';

            // Bersihkan output buffer sebelum kirim file
            if (ob_get_length()) {
                ob_end_clean();
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Throwable $e) {
            log_message('error', '[Lansia::export_excel] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}

