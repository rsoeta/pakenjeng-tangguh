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

        // Daftar RT & Pemetaan RW -> RT (mengikuti karantina wilayah tugas jika role_id >= 4)
        $rwRtMap = [];
        $rtSet   = [];
        if ($roleId >= 4) {
            $parsedMap = self::parseWilayahTugasMap($wilayahTugas);
            if (!empty($parsedMap)) {
                foreach ($parsedMap as $rwKey => $rts) {
                    if (!empty($rts)) {
                        $rwRtMap[$rwKey] = $rts;
                        foreach ($rts as $r) {
                            $rtSet[$r] = true;
                        }
                    } else {
                        $rwVars = self::getWilayahVariants($rwKey);
                        $rtRows = $db->table('dtsen_rt')
                            ->select('rt')
                            ->distinct()
                            ->whereIn('rw', $rwVars)
                            ->where('rt !=', '')
                            ->where('rt !=', '000')
                            ->where('kode_desa', $kodeDesa)
                            ->orderBy('rt', 'asc')
                            ->get()->getResultArray();
                        $rwRtMap[$rwKey] = array_column($rtRows, 'rt');
                        foreach ($rwRtMap[$rwKey] as $r) {
                            $rtSet[$r] = true;
                        }
                    }
                }
            } else {
                foreach ($rwList as $rRow) {
                    $rwKey  = $rRow['no_rw'];
                    $rwVars = self::getWilayahVariants($rwKey);
                    $rtRows = $db->table('dtsen_rt')
                        ->select('rt')
                        ->distinct()
                        ->whereIn('rw', $rwVars)
                        ->where('rt !=', '')
                        ->where('rt !=', '000')
                        ->where('kode_desa', $kodeDesa)
                        ->orderBy('rt', 'asc')
                        ->get()->getResultArray();
                    $rwRtMap[$rwKey] = array_column($rtRows, 'rt');
                    foreach ($rwRtMap[$rwKey] as $r) {
                        $rtSet[$r] = true;
                    }
                }
            }
        } else {
            $rtBuilder = $db->table('dtsen_rt')
                ->select('rw, rt')
                ->distinct()
                ->where('rt !=', '')
                ->where('rt !=', '000');

            if (!empty($kodeDesa)) {
                $rtBuilder->where('kode_desa', $kodeDesa);
            }

            $rtRows = $rtBuilder->orderBy('rw', 'asc')->orderBy('rt', 'asc')->get()->getResultArray();
            foreach ($rtRows as $rRow) {
                $rwKey = (string)$rRow['rw'];
                $rtVal = (string)$rRow['rt'];
                if ($rwKey !== '' && $rwKey !== '000') {
                    $rwRtMap[$rwKey][] = $rtVal;
                }
                $rtSet[$rtVal] = true;
            }
        }

        $rtKeys = array_keys($rtSet);
        sort($rtKeys);
        $rtList = array_map(function ($rt) {
            return ['no_rt' => $rt];
        }, $rtKeys);

        $data = [
            'title'     => 'Data Lansia / Jompo',
            'title2'    => 'Data Lansia & Jompo DTSEN',
            'desKels'   => $desKels->orderBy('nama_desa', 'asc')->findAll(),
            'Rw'        => $rwList,
            'Rt'        => $rtList,
            'rwRtMap'   => $rwRtMap,
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
                'kode_desa'      => $kodeDesa,
                'rw'             => $this->request->getPost('rw'),
                'rt'             => $this->request->getPost('rt'),
                'jk'             => $this->request->getPost('jk'),
                'tempat_tinggal' => $this->request->getPost('tempat_tinggal'),
                'keadaan_rumah'  => $this->request->getPost('keadaan_rumah'),
                'kategori'       => $this->request->getPost('kategori'),
                'search'         => trim((string)$searchVal),
                'wilayah_tugas'  => $user['wilayah_tugas'],
                'level'          => $user['level'],
                'role_id'        => $roleId
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
                if ($statusRumah === '1') {
                    $statusRumah = 'Milik Sendiri';
                }
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

            // 1. Jalankan mesin Smart Indonesian Copyediting (EYD/KBBI, koreksi typo & struktur kalimat efektif)
            $localResult = $this->smartCopyeditIndonesia($rawText);

            // 2. Opsional: Jika GEMINI_API_KEY diset di .env, sempurnakan via Gemini API lalu pastikan tetap baku EYD
            $aiResult = $this->tryExternalAiCopyedit($localResult);
            $result   = !empty($aiResult) ? $this->smartCopyeditIndonesia($aiResult) : $localResult;

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
     * Mesin Copyediting Bahasa Indonesia Cerdas (EYD/KBBI Berimbuhan + Kamus Typo + Fuzzy Matching Levenshtein + Kalimat Efektif)
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

        // Perbaiki angka 0 yang tertukar dengan huruf o di awal kata (contoh: 0rang -> orang)
        $s = preg_replace('/\b0([a-z]{3,})\b/', 'o$1', $s);

        // Normalisasi singkatan satuan umur/waktu (contoh: "7 th" / "7 thn" -> "7 tahun", "3 bln" -> "3 bulan")
        $s = preg_replace('/\b(\d+)\s*(?:thn|th)\b/', '$1 tahun', $s);
        $s = preg_replace('/\b(\d+)\s*bln\b/', '$1 bulan', $s);
        $s = preg_replace('/\b(\d+)\s*hr\b/', '$1 hari', $s);

        // 2. Normalisasi kata ulang dengan angka "2"
        // Untuk kata berawalan se-/ber-/ter- (contoh: sehari2 -> sehari-hari, berkali2 -> berkali-kali, bertahun2 -> bertahun-tahun)
        $s = preg_replace('/\b(se|ber|ter)(hari|kali|tahun|bulan|minggu|jam|jalan|turut|angsur|ulang)2(an|nya)?\b/', '$1$2-$2$3', $s);
        // Untuk kata dasar ulang (contoh: sakit2an -> sakit-sakitan, paru2 -> paru-paru, rata2 -> rata-rata)
        $s = preg_replace('/\b([a-z]{3,})2(an|nya)?\b/', '$1-$1$2', $s);

        // Normalisasi kata ulang yang ditulis terpisah tanpa tanda hubung
        $s = preg_replace('/\b(se|ber|ter)(hari|kali|tahun|bulan|minggu|turut|ulang)\s+\2(nya|an)?\b/', '$1$2-$2$3', $s);
        $reduplications = [
            'paru', 'anak', 'orang', 'rata', 'kadang', 'hati',
            'masing', 'sendiri', 'laki', 'tiba', 'pelan', 'jalan', 'ragu'
        ];
        foreach ($reduplications as $rw) {
            $s = preg_replace('/\b' . $rw . '\s+' . $rw . '(nya|an)?\b/', $rw . '-' . $rw . '$1', $s);
        }

        // 3. Perbaikan gabungan imbuhan "di ", "se ", dan akhiran " nya" yang terpisah spasi (sebelum tokenisasi)
        $passiveVerbs = [
            'bantu', 'rawat', 'urus', 'dampingi', 'papah', 'gendong', 'bawa', 'periksa',
            'obati', 'tangani', 'pantau', 'awasi', 'operasi', 'pakai', 'gunakan', 'antar',
            'jemput', 'suapi', 'mandikan', 'tuntun', 'dorong', 'angkat', 'pijat', 'terapi',
            'beri', 'berikan', 'tempatkan', 'tempati', 'tinggali', 'biayai', 'tanggung'
        ];
        foreach ($passiveVerbs as $v) {
            $s = preg_replace('/\bdi\s+' . $v . '\b/', 'di' . $v, $s);
        }
        $s = preg_replace('/\bdi\s*karna\s*kan\b|\bdi\s*karena\s*kan\b/', 'dikarenakan', $s);
        $s = preg_replace('/\bse\s+orang\b/', 'seorang', $s);
        $s = preg_replace('/\borangtua(nya)?\b/', 'orang tua$1', $s);
        $s = preg_replace('/\brumahtangga(nya)?\b/', 'rumah tangga$1', $s);
        $s = preg_replace('/\byangtak\b/', 'yang tidak', $s);
        $s = preg_replace('/\b(?<!di|ke|dari)([a-z]{4,})\s+nya\b/', '$1nya', $s);

        // 4. Normalisasi fonetik EYD/KBBI berimbuhan (p/f/v dan bentuk tidak baku umum)
        $phoneticPatterns = [
            '/\b(ber|ke)?akti[pf]itas(nya)?\b/' => '$1aktivitas$2',
            '/\b(ber|ke)?akti[pf](an|nya)?\b/' => '$1aktif$2',
            '/\bprodukti[pf]itas(nya)?\b/' => 'produktivitas$1',
            '/\befekti[pf]itas(nya)?\b/' => 'efektivitas$1',
            '/\bkreati[pf]itas(nya)?\b/' => 'kreativitas$1',
            '/\b(produkti|efekti|intensi|pasi|sensiti|kreati|operati|kogniti|degenerati|rehabilitati|alternati|inisiati|positi|negati|kolekti|objekti|subjekti|preventi|kurati|paliati|komunikati|kooperati)p(nya)?\b/' => '$1f$2',
            '/\b(ber|per)?nafas(an|nya)?\b/' => '$1napas$2',
            '/\b(ber|mem|pe|pem)?fikir(an|kan|nya)?\b/' => '$1pikir$2',
            '/\b(mem|pe|pem)?faham(i|an|nya)?\b/' => '$1paham$2',
            '/\b(?:syaraf|sarap)(nya)?\b/' => 'saraf$1',
            '/\bmerubah\b/' => 'mengubah',
            '/\b(ber|di|ter)?(?:robah|obah|rubah)(nya|kan)?\b/' => '$1ubah$2',
            '/\b(ber|me|di)?praktek(kan|nya)?\b/' => '$1praktik$2',
            '/\b(be)?resiko(nya)?\b/' => '$1risiko$2',
            '/\b(?:kwatir|kuatir|kawatir)(nya)?\b/' => 'khawatir$1',
            '/\bapotik(nya)?\b/' => 'apotek$1',
            '/\b(meng)?antri(an)?\b/' => '$1antre$2',
            '/\bmemperihatinkan\b/' => 'memprihatinkan',
            '/\b(di)?diagnosa(nya)?\b/' => '$1diagnosis$2',
            '/\b(di)?inpus(nya)?\b/' => '$1infus$2',
            '/\binpeksi(nya)?\b/' => 'infeksi$1',
            '/\b(ber)?hutang(nya)?\b/' => '$1utang$2',
            '/\b(meng|di)?ijin(kan|nya)?\b/' => '$1izin$2',
            '/\bijasah(nya)?\b/' => 'ijazah$1',
            '/\bjenasah(nya)?\b/' => 'jenazah$1',
            '/\bnasehat(i|nya)?\b/' => 'nasihat$1',
            '/\b(ke)?trampil(an|nya)?\b/' => '$1terampil$2',
            '/\bsilahkan\b/' => 'silakan',
            '/\bsekedar(nya)?\b/' => 'sekadar$1',
            '/\btuna\s+(netra|rungu|wicara|daksa|grahita)\b/' => 'tuna$1',
        ];
        foreach ($phoneticPatterns as $pat => $rep) {
            $s = preg_replace($pat, $rep, $s);
        }

        // 5. Kamus koreksi typo langsung & kata tidak baku -> baku EYD/KBBI
        $directMap = [
            // Kata keterangan & derajat
            'sagat' => 'sangat',
            'sngat' => 'sangat',
            'sangt' => 'sangat',
            'sgt' => 'sangat',
            'snagt' => 'sangat',
            'sangaat' => 'sangat',
            'bgt' => 'sekali',
            'banget' => 'sekali',
            'bget' => 'sekali',
            'aga' => 'agak',
            'agk' => 'agak',
            'rada' => 'agak',
            'ckup' => 'cukup',
            'ckp' => 'cukup',
            'cuku' => 'cukup',
            'kurag' => 'kurang',
            'kurng' => 'kurang',
            'krg' => 'kurang',
            'lbih' => 'lebih',
            'lbh' => 'lebih',
            'palig' => 'paling',
            'plg' => 'paling',
            'msh' => 'masih',
            'msih' => 'masih',
            'masi' => 'masih',
            'masihh' => 'masih',
            'sdh' => 'sudah',
            'udh' => 'sudah',
            'udah' => 'sudah',
            'sdah' => 'sudah',
            'suadh' => 'sudah',
            'blm' => 'belum',
            'blom' => 'belum',
            'blum' => 'belum',
            'belom' => 'belum',
            'slalu' => 'selalu',
            'sllu' => 'selalu',
            'serig' => 'sering',
            'sring' => 'sering',
            'srng' => 'sering',
            'jarag' => 'jarang',
            'jrang' => 'jarang',
            'jrg' => 'jarang',
            'kadag' => 'kadang',
            'kdang' => 'kadang',
            'kdg' => 'kadang',
            'hnya' => 'hanya',
            'cuma' => 'hanya',
            'cuman' => 'hanya',
            'cm' => 'hanya',
            'cma' => 'hanya',
            'hmpir' => 'hampir',
            'trus' => 'terus',
            'trs' => 'terus',
            'tetep' => 'tetap',
            'ttp' => 'tetap',
            'lgsg' => 'langsung',
            'lagsung' => 'langsung',
            'memag' => 'memang',
            'mmg' => 'memang',
            'skrg' => 'sekarang',
            'skrang' => 'sekarang',
            'sekarag' => 'sekarang',
            'dlu' => 'dahulu',

            // Kata hubung, depan, & negasi
            'tdk' => 'tidak',
            'tak' => 'tidak',
            'gak' => 'tidak',
            'ga' => 'tidak',
            'nggak' => 'tidak',
            'enggak' => 'tidak',
            'kagak' => 'tidak',
            'tidk' => 'tidak',
            'tdak' => 'tidak',
            'bkan' => 'bukan',
            'bkn' => 'bukan',
            'yg' => 'yang',
            'yag' => 'yang',
            'yng' => 'yang',
            'dn' => 'dan',
            'ato' => 'atau',
            'atw' => 'atau',
            'tp' => 'tetapi',
            'tapi' => 'tetapi',
            'ttpi' => 'tetapi',
            'namaun' => 'namun',
            'nmun' => 'namun',
            'krn' => 'karena',
            'karna' => 'karena',
            'kerna' => 'karena',
            'krna' => 'karena',
            'shg' => 'sehingga',
            'sehinga' => 'sehingga',
            'shgga' => 'sehingga',
            'untik' => 'untuk',
            'untk' => 'untuk',
            'utk' => 'untuk',
            'tuk' => 'untuk',
            'bueat' => 'buat',
            'bwt' => 'buat',
            'dgn' => 'dengan',
            'dg' => 'dengan',
            'dngn' => 'dengan',
            'dgan' => 'dengan',
            'sm' => 'sama',
            'ama' => 'sama',
            'tampa' => 'tanpa',
            'tnpa' => 'tanpa',
            'dlm' => 'dalam',
            'dlam' => 'dalam',
            'dalem' => 'dalam',
            'pd' => 'pada',
            'pda' => 'pada',
            'dr' => 'dari',
            'dri' => 'dari',
            'kpd' => 'kepada',
            'kpda' => 'kepada',
            'olh' => 'oleh',
            'sjak' => 'sejak',
            'ktika' => 'ketika',
            'wkt' => 'waktu',
            'wktu' => 'waktu',
            'kl' => 'kalau',
            'kalo' => 'kalau',
            'klu' => 'kalau',
            'jka' => 'jika',
            'agr' => 'agar',
            'biar' => 'agar',
            'sbg' => 'sebagai',
            'sbgai' => 'sebagai',
            'spt' => 'seperti',
            'sprti' => 'seperti',
            'kyk' => 'seperti',
            'kayak' => 'seperti',
            'jga' => 'juga',
            'jg' => 'juga',
            'jd' => 'jadi',
            'jdi' => 'jadi',

            // Kata kerja bantu & modal
            'bs' => 'bisa',
            'bsa' => 'bisa',
            'dpt' => 'dapat',
            'dpat' => 'dapat',
            'hrs' => 'harus',
            'hrus' => 'harus',
            'prlu' => 'perlu',
            'btuh' => 'butuh',
            'puya' => 'punya',
            'piya' => 'punya',
            'pnya' => 'punya',
            'pny' => 'punya',
            'mpunyai' => 'mempunyai',
            'mendaptakan' => 'mendapatkan',
            'mndapatkan' => 'mendapatkan',
            'layal' => 'layak',
            'layk' => 'layak',

            // Kondisi kesehatan, tubuh & lansia (termasuk EYD baku)
            'kkdisi' => 'kondisi',
            'kndisi' => 'kondisi',
            'kondsi' => 'kondisi',
            'kndsi' => 'kondisi',
            'kodisi' => 'kondisi',
            'knodisi' => 'kondisi',
            'keadan' => 'keadaan',
            'kadaan' => 'keadaan',
            'pisik' => 'fisik',
            'psik' => 'fisik',
            'fisikna' => 'fisiknya',
            'pisikna' => 'fisiknya',
            'pisiknya' => 'fisiknya',
            'tbuh' => 'tubuh',
            'bada' => 'badan',
            'bdn' => 'badan',
            'shat' => 'sehat',
            'seht' => 'sehat',
            'bgar' => 'bugar',
            'bugr' => 'bugar',
            'lmah' => 'lemah',
            'lemes' => 'lemas',
            'lmas' => 'lemas',
            'rnta' => 'renta',
            'ujur' => 'uzur',
            'jmpo' => 'jompo',
            'mndiri' => 'mandiri',
            'sndiri' => 'sendiri',
            'sndirian' => 'sendirian',
            'peyakit' => 'penyakit',
            'pnyakit' => 'penyakit',
            'penyaket' => 'penyakit',
            'pnykt' => 'penyakit',
            'skit' => 'sakit',
            'skt' => 'sakit',
            'struk' => 'stroke',
            'strok' => 'stroke',
            'setruk' => 'stroke',
            'lmpuh' => 'lumpuh',
            'rematik' => 'reumatik',
            'rmatik' => 'reumatik',
            'asamurat' => 'asam urat',
            'darahtinggi' => 'hipertensi',
            'diabet' => 'diabetes',
            'kolestrol' => 'kolesterol',
            'kolesterl' => 'kolesterol',
            'jantug' => 'jantung',
            'jntung' => 'jantung',
            'lambug' => 'lambung',
            'mag' => 'maag',
            'magh' => 'maag',
            'tulag' => 'tulang',
            'katark' => 'katarak',
            'rabn' => 'rabun',
            'budeg' => 'tuli',
            'budek' => 'tuli',
            'pkun' => 'pikun',
            'stress' => 'stres',
            'migren' => 'migrain',
            'oprasi' => 'operasi',
            'pengelihatan' => 'penglihatan',
            'pelihatan' => 'penglihatan',
            'pendegaran' => 'pendengaran',
            'tergaggu' => 'terganggu',
            'gagguan' => 'gangguan',
            'pinggag' => 'pinggang',
            'pggang' => 'pinggang',
            'puggung' => 'punggung',
            'pungug' => 'punggung',
            'tagan' => 'tangan',
            'tngan' => 'tangan',
            'sbelah' => 'sebelah',
            'pegel' => 'pegal',
            'sesek' => 'sesak',
            'bengek' => 'asma',
            'ambeyen' => 'ambeien',
            'ambein' => 'ambeien',

            // Aktivitas & penanganan
            'aktipitas' => 'aktivitas',
            'aktifitas' => 'aktivitas',
            'aktvts' => 'aktivitas',
            'beraktipitas' => 'beraktivitas',
            'beraktifitas' => 'beraktivitas',
            'braktivitas' => 'beraktivitas',
            'berjln' => 'berjalan',
            'brjalan' => 'berjalan',
            'berjlan' => 'berjalan',
            'berjala' => 'berjalan',
            'jln' => 'jalan',
            'jala' => 'jalan',
            'brgerak' => 'bergerak',
            'brdiri' => 'berdiri',
            'dduk' => 'duduk',
            'tdur' => 'tidur',
            'mkan' => 'makan',
            'mndi' => 'mandi',
            'bcara' => 'bicara',
            'ngomong' => 'berbicara',
            'bkerja' => 'bekerja',
            'krja' => 'kerja',
            'srabutan' => 'serabutan',
            'ngurus' => 'mengurus',
            'ngurusin' => 'mengurus',
            'mgurus' => 'mengurus',
            'mrawat' => 'merawat',
            'ngebantu' => 'membantu',
            'penagan' => 'penanganan',
            'penaganan' => 'penanganan',
            'pnanganan' => 'penanganan',
            'penangan' => 'penanganan',
            'prawatan' => 'perawatan',
            'pgobatan' => 'pengobatan',
            'brobat' => 'berobat',
            'husus' => 'khusus',
            'kusus' => 'khusus',
            'khusu' => 'khusus',
            'khuss' => 'khusus',
            'bntuan' => 'bantuan',
            'bantuaan' => 'bantuan',
            'batuan' => 'bantuan',
            'penghasila' => 'penghasilan',
            'pemerintanh' => 'pemerintah',
            'mesjid' => 'masjid',
            'tgkat' => 'tongkat',
            'tngkat' => 'tongkat',
            'kursiroda' => 'kursi roda',
            'kluarga' => 'keluarga',
            'klg' => 'keluarga',
            'ank' => 'anak',
            'ttangga' => 'tetangga',
            'tggal' => 'tinggal',
            'tnggal' => 'tinggal',
            'rmh' => 'rumah',
            'rmah' => 'rumah',
            'tmpat' => 'tempat',
            'tmp' => 'tempat',
            'susah' => 'sulit',
            'slit' => 'sulit',
        ];

        // Kosakata baku referensi untuk fuzzy matching Levenshtein
        $standardVocab = [
            'masih', 'sangat', 'cukup', 'agak', 'kurang', 'lebih', 'paling', 'sudah', 'belum',
            'selalu', 'sering', 'jarang', 'kadang', 'hanya', 'hampir', 'terus', 'tetap', 'langsung',
            'memang', 'sekarang', 'dahulu', 'tidak', 'bukan', 'yang', 'atau', 'tetapi', 'namun',
            'karena', 'sehingga', 'untuk', 'dengan', 'tanpa', 'dalam', 'pada', 'dari', 'kepada',
            'oleh', 'sejak', 'ketika', 'waktu', 'kalau', 'jika', 'apabila', 'agar', 'supaya',
            'walaupun', 'meskipun', 'sebagai', 'seperti', 'juga', 'jadi', 'bisa', 'dapat', 'mampu',
            'harus', 'perlu', 'butuh', 'punya', 'memiliki', 'mempunyai', 'menderita', 'mengalami',
            'penyakit', 'asam', 'urat', 'kondisi', 'keadaan', 'fisik', 'tubuh', 'badan', 'sehat',
            'bugar', 'kuat', 'lemah', 'lemas', 'renta', 'uzur', 'lansia', 'jompo', 'mandiri',
            'sendiri', 'sendirian', 'sakit', 'gejala', 'keluhan', 'kronis', 'menahun', 'kambuh',
            'parah', 'ringan', 'berat', 'sembuh', 'pulih', 'membaik', 'memburuk', 'menurun',
            'lumpuh', 'stroke', 'diabetes', 'hipertensi', 'kolesterol', 'jantung', 'lambung',
            'ginjal', 'saraf', 'tulang', 'sendi', 'nyeri', 'linu', 'pegal', 'kaku', 'bengkak',
            'katarak', 'rabun', 'buram', 'kabur', 'buta', 'tuli', 'bisu', 'pikun', 'demensia',
            'stres', 'depresi', 'operasi', 'vertigo', 'pusing', 'batuk', 'demam', 'sesak', 'napas',
            'bernapas', 'pernapasan', 'kepala', 'mata', 'penglihatan', 'telinga', 'pendengaran',
            'mulut', 'bicara', 'berbicara', 'tangan', 'kaki', 'lutut', 'pinggang', 'punggung',
            'dada', 'perut', 'leher', 'sebelah', 'kanan', 'kiri', 'terganggu', 'gangguan', 'jelas',
            'tinggal', 'keluarga', 'anak', 'cucu', 'suami', 'istri', 'tetangga', 'bantu', 'bantuan',
            'membantu', 'dibantu', 'rawat', 'merawat', 'dirawat', 'perawatan', 'urus', 'mengurus',
            'diurus', 'aktivitas', 'beraktivitas', 'kegiatan', 'jalan', 'berjalan', 'gerak',
            'bergerak', 'berdiri', 'duduk', 'tidur', 'berbaring', 'terbaring', 'makan', 'minum',
            'mandi', 'kerja', 'bekerja', 'kebun', 'berkebun', 'tani', 'bertani', 'serabutan',
            'menjaga', 'mendampingi', 'didampingi', 'tempat', 'rumah', 'kamar', 'tongkat', 'kursi',
            'roda', 'membutuhkan', 'memerlukan', 'penanganan', 'pengobatan', 'berobat', 'medis',
            'rutin', 'intensif', 'khusus', 'kontrol', 'dokter', 'puskesmas', 'pendamping',
            'kesulitan', 'fungsi', 'usia', 'lanjut', 'faktor', 'sulit', 'aktif', 'produktif',
            'normal', 'stabil', 'kesehatan', 'ingatan', 'pikiran', 'tenaga', 'pekerjaan',
            'penghasilan', 'pendapatan', 'kebutuhan', 'tanggungan', 'pemerintah', 'layak',
            'menerima', 'mendapatkan', 'kehidupan', 'keseharian', 'beliau', 'orang',
            'daya', 'berdaya', 'upaya', 'berupaya', 'kemampuan', 'ketidakberdayaan',
            'keterbatasan', 'ketergantungan', 'keseimbangan', 'kemandirian',
            'tepat', 'tanggal', 'tunggal', 'tingkat', 'tinggali', 'ditinggali', 'panggung'
        ];

        $resolveWord = function (string $word) use ($directMap, $standardVocab): ?string {
            if (isset($directMap[$word])) {
                return $directMap[$word];
            }
            if (in_array($word, $standardVocab, true)) {
                return $word;
            }
            if (strlen($word) < 5) {
                return null;
            }

            // 1. Jika kata benda berimbuhan pe-/ke-...-a kehilangan huruf 'n' di akhir (contoh: penghasila -> penghasilan, kesehata -> kesehatan)
            if (preg_match('/^(?:pe|ke)[a-z]{4,}a$/', $word) && in_array($word . 'n', $standardVocab, true)) {
                return $word . 'n';
            }

            // 2. Deteksi apakah kata memiliki indikasi kuat typo struktural:
            //    - Huruf kembar berurutan selain 'ngg' yang valid (contoh: kkdisi, bantuaan, sangaat, tergaggu)
            //    - Gugus konsonan tidak lazim (contoh: kndisi, pnyakit, brjalan, pemerintanh)
            $hasDoubleLetter = (bool)preg_match('/([a-fh-z])\1|(?<!n)gg/', $word);
            $hasWeirdCluster = (bool)preg_match('/^(?:[^aiueo]{2,})|(?:[^aiueo]{4,})|(?:nh|(?<!n)gk|knd|pny|brj|mnd|snd|klg|tngg|bnt|tngk)/', $word);

            $bestWord = null;
            $bestDist = 99;
            foreach ($standardVocab as $vocab) {
                if (strlen($vocab) < 5) {
                    continue;
                }
                $lenDiff = abs(strlen($word) - strlen($vocab));
                if ($lenDiff > 1 || $word[0] !== $vocab[0]) {
                    continue;
                }
                $dist = levenshtein($word, $vocab);

                // A. Transposisi 2 huruf bersebelahan (contoh: knodisi -> kondisi, mendaptakan -> mendapatkan)
                if ($lenDiff === 0 && $dist === 2 && count_chars($word, 1) === count_chars($vocab, 1)) {
                    return $vocab;
                }

                // B. Kurang/lebih 1 huruf ($lenDiff === 1) atau huruf kembar typo ($dist === 1), HANYA jika ada indikasi typo struktural
                if ($dist === 1 && ($hasDoubleLetter || ($lenDiff === 1 && $hasWeirdCluster))) {
                    if (substr($word, -1) === 'i' && substr($vocab, -1) !== 'i') {
                        continue;
                    }
                    if ($dist < $bestDist) {
                        $bestDist = $dist;
                        $bestWord = $vocab;
                    }
                }
            }
            return $bestWord;
        };

        $tokens = preg_split('/(\s+|[,.!?;:\/()-])/', $s, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        foreach ($tokens as &$tok) {
            if (preg_match('/^[a-z0-9]+$/', $tok)) {
                $resolved = $resolveWord($tok);
                if ($resolved !== null) {
                    $tok = $resolved;
                } else {
                    // Cek jika berakhiran "-nya" (contoh: kkdisinya -> kondisinya, peyakitnya -> penyakitnya)
                    if (strlen($tok) > 6 && substr($tok, -3) === 'nya') {
                        $base = substr($tok, 0, -3);
                        $resBase = $resolveWord($base);
                        if ($resBase !== null) {
                            $tok = $resBase . 'nya';
                        }
                    } elseif (strlen($tok) > 6 && substr($tok, -2) === 'ya' && substr($tok, -3, 1) === 'n') {
                        // Contoh: kesehatanya -> kesehatan + nya = kesehatannya
                        $baseWithN = substr($tok, 0, -2);
                        $resBase = $resolveWord($baseWithN);
                        if ($resBase !== null && substr($resBase, -1) === 'n') {
                            $tok = $resBase . 'nya';
                        }
                    }
                    // Cek jika berawalan "ber-", "ter-", "se-", "di-" HANYA untuk kata dasar >= 5 huruf atau yang ada di $directMap
                    if (preg_match('/^(ber|ter|se|di)([a-z]{5,})$/', $tok, $m)) {
                        $resBase = $resolveWord($m[2]);
                        if ($resBase !== null) {
                            $tok = $m[1] . $resBase;
                        }
                    }
                }
            }
        }
        unset($tok);

        $s = implode('', $tokens);

        // 6. Transformasi Frasa & Struktur Kalimat Efektif (Copyediting Sintaksis)
        $phraseRules = [
            '/^(?:punya|mempunyai|memiliki|ada)\s+penyakit\b/' => 'menderita penyakit',
            '/\b(?:punya|mempunyai)\s+penyakit\b/' => 'menderita penyakit',
            '/^(?:kena|terkena)\s+penyakit\b/' => 'menderita penyakit',
            '/^(?:kena|terkena)\s+(stroke|diabetes|hipertensi|katarak|asam urat|reumatik|lumpuh)\b/' => 'menderita $1',
            '/^sakit\s+(reumatik|lambung|pinggang|jantung|ginjal|asam urat|stroke|diabetes|hipertensi|katarak|paru-paru|asma|dalam)\b/' => 'menderita sakit $1',

            // Struktur rancu "jadi untuk kondisi fisik buat berjalan agak sulit"
            '/\bjadi\s+untuk\s+kondisi\s+fisik\s+(?:buat|untuk)\s+berjalan\s+(?:agak|cukup)?\s*sulit\b/' => 'sehingga kondisi fisik cukup sulit untuk berjalan',
            '/\buntuk\s+kondisi\s+fisik\s+(?:buat|untuk)\s+berjalan\s+(?:agak|cukup)?\s*sulit\b/' => 'kondisi fisik cukup sulit untuk berjalan',
            '/\bkondisi\s+fisik\s+(?:buat|untuk)\s+berjalan\s+(?:agak|cukup)?\s*sulit\b/' => 'kondisi fisik cukup sulit untuk berjalan',
            '/\b(?:buat|untuk)\s+berjalan\s+(?:agak|cukup)\s+sulit\b/' => 'cukup sulit untuk berjalan',
            '/\b(?:buat|untuk)\s+berjalan\s+sulit\b/' => 'sulit untuk berjalan',
            '/\b(?:agak|cukup)\s+sulit\s+(?:buat|untuk)\s+berjalan\b/' => 'cukup sulit untuk berjalan',

            // Kata "buat" non-baku sebelum kata kerja -> "untuk"
            '/\bbuat\s+(berjalan|bergerak|beraktivitas|berdiri|duduk|melihat|mendengar|bicara|berbicara|makan|bekerja|berobat|kebutuhan|hidup)\b/' => 'untuk $1',

            // Pengulangan klausa "masih ... masih bisa/dapat ..." tanpa konjungsi (contoh: "Masih sangat bugar masih bisa beraktivitas")
            '/\b(masih\s+(?:sangat\s+|cukup\s+)?[a-z]+(?:\s+dan\s+[a-z]+)?)\s+masih\s+(?:bisa|dapat)\b/' => '$1 serta masih dapat',

            // Pengulangan kata penghubung "jadi ... jadi ..." & klausa penanganan khusus
            '/\bjadi\s+harus\s+ada\s+penanganan\s+khusus\s+juga\b/' => 'dan memerlukan penanganan khusus',
            '/\bjadi\s+harus\s+ada\s+penanganan\s+khusus\b/' => 'serta memerlukan penanganan khusus',
            '/\bharus\s+ada\s+penanganan\s+khusus\s+juga\b/' => 'memerlukan penanganan khusus',
            '/\bharus\s+ada\s+penanganan\s+khusus\b/' => 'memerlukan penanganan khusus',
            '/\bbutuh\s+penanganan\s+khusus\b/' => 'memerlukan penanganan khusus',

            // Frasa umum lainnya pada keterangan lansia/jompo
            '/\bsudah\s+tua\s+renta\s+tidak\b/' => 'sudah tua renta, tidak',
            '/\btidak\s+(?:bisa|dapat)\s+(?:jalan|berjalan)\s+sendiri\b/' => 'tidak dapat berjalan secara mandiri',
            '/\btidak\s+(?:bisa|dapat)\s+beraktivitas\s+sendiri\b/' => 'tidak dapat beraktivitas secara mandiri',
            '/\b(masih|tidak|kurang|belum|sudah|hanya)\s+bisa\b/' => '$1 dapat',
            '/\b(tidak|belum)\s+punya\b/' => '$1 memiliki',
            '/\bmandiri\s+harus\s+dibantu\b/' => 'mandiri, sehingga perlu dibantu',
            '/\bharus\s+dibantu\b/' => 'sehingga perlu dibantu',
            '/\bmasih\s+dapat\s+kerja\b/' => 'masih dapat bekerja',
            '/\bsulit\s+bicara\b/' => 'sulit berbicara',
            '/\btidak\s+ada\s+yang\s+mengurus\b/' => 'tanpa pendamping yang mengurus',
            '/\b(?:sering\s+)?sakit[\s-]+sakitan\b/' => 'sering sakit-sakitan',
            '/\baktivitas\s+di\s+tempat\s+tidur\s+saja\b/' => 'hanya dapat beraktivitas di tempat tidur',
        ];

        foreach ($phraseRules as $pattern => $replacement) {
            $s = preg_replace($pattern, $replacement, $s);
        }

        // Jika masih ada pengulangan "masih ... masih ..." tanpa kata hubung di antaranya
        $s = preg_replace('/\b(masih\s+[^,.!?;]+?)\s+(?<!dan\s|serta\s|tetapi\s|namun\s)masih\s+/', '$1 dan masih ', $s);

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
        $acronyms = [
            'tbc' => 'TBC', 'hiv' => 'HIV', 'bpjs' => 'BPJS', 'rt' => 'RT', 'rw' => 'RW',
            'odgj' => 'ODGJ', 'bab' => 'BAB', 'bak' => 'BAK', 'kpm' => 'KPM', 'kk' => 'KK'
        ];
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
                'kode_desa'      => $kodeDesa,
                'rw'             => $this->request->getGet('rw'),
                'rt'             => $this->request->getGet('rt'),
                'jk'             => $this->request->getGet('jk'),
                'tempat_tinggal' => $this->request->getGet('tempat_tinggal'),
                'keadaan_rumah'  => $this->request->getGet('keadaan_rumah'),
                'kategori'       => $this->request->getGet('kategori'),
                'search'         => trim((string)($this->request->getGet('search') ?? '')),
                'wilayah_tugas'  => $user['wilayah_tugas'],
                'level'          => $user['level'],
                'role_id'        => $roleId
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
                if ($statusRumah === '1') {
                    $statusRumah = 'Milik Sendiri';
                }
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

