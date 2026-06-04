<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\GenModel;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\API\ResponseTrait;

class PembaruanKeluarga extends BaseController
{
    use ResponseTrait;

    protected $db;
    protected $genModel;
    protected $kkModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->genModel = new GenModel();
    }

    // 🏠 Halaman utama pembaruan (opsional, nanti bisa jadi list usulan)
    public function index()
    {
        return redirect()->to('/dtsen-se'); // fallback ke halaman utama data keluarga
    }

    public function detail($id_kk)
    {
        try {
            $db = \Config\Database::connect();
            $genModel = new \App\Models\GenModel();
            $kkModel  = new \App\Models\Dtsen\DtsenKkModel(); // 🚀 TAMBAHKAN INI

            log_message('debug', "🚀 [detail] Memulai load detail untuk id_kk={$id_kk}");

            // 1) Ambil data KK utama (pastikan array)
            $kkData = $db->table('dtsen_kk')
                ->where('id_kk', $id_kk)
                // 🚀 PENTING: Penulisan baku CI4
                ->groupStart()
                ->where('deleted_at', null)
                ->orWhere('deleted_at', '0000-00-00 00:00:00')
                ->groupEnd()
                ->get()
                ->getRowArray() ?? [];

            if (empty($kkData)) {
                throw new \Exception("Data KK tidak ditemukan untuk id_kk={$id_kk}");
            }

            // Ambil kategori desil dari dtsen_se
            $seData = $db->table('dtsen_se')
                ->select('kategori_desil')
                ->where('id_kk', $id_kk)
                ->orderBy('id_se', 'DESC') // ambil SE terbaru jika lebih dari satu
                ->get()
                ->getRowArray();

            $kategoriDesil = $seData['kategori_desil'] ?? null;

            // 2) Ambil data RT terkait (jika ada)
            $rtData = [];
            if (!empty($kkData['id_rt'])) {
                $rtData = $db->table('dtsen_rt')
                    ->where('id_rt', $kkData['id_rt'])
                    ->get()
                    ->getRowArray() ?? [];
            }

            // 3) Ambil usulan terbaru (draft / submitted / verified / diverifikasi)
            $usulan = $db->table('dtsen_usulan')
                ->where('dtsen_kk_id', $id_kk)
                ->whereIn('status', ['draft', 'submitted', 'verified', 'diverifikasi'])
                // ->where('deleted_at IS NULL') // 🚀 FILTER DRAFT/USULAN HANTU DI DETAIL
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            // safety: pastikan $usulan adalah array dan punya key yang diperlukan
            if (!is_array($usulan)) $usulan = [];
            if (!array_key_exists('status', $usulan)) $usulan['status'] = null;

            log_message('debug', 'ℹ️ [detail] status usulan = ' . ($usulan['status'] ?? 'null'));

            // 4) Decode payload jika ada
            $payload = [];
            $payloadPerumahan = [];
            if (!empty($usulan['payload'])) {
                $decoded = json_decode($usulan['payload'], true);
                if (is_array($decoded)) {
                    $payload = $decoded;
                    $payloadPerumahan = $payload['perumahan'] ?? [];
                } else {
                    log_message('warning', '[detail] payload usulan tidak dapat didecode sebagai array untuk usulan id=' . ($usulan['id'] ?? 'null'));
                }
            }

            // 🚀 TAMBAHKAN INI: Cek apakah Draft sudah lengkap (Siap Submitted)
            $is_submitted_ready = 0;
            if (($usulan['status'] ?? '') === 'draft' && !empty($payload)) {
                if ($kkModel->isPayloadLengkap($payload)) {
                    $is_submitted_ready = 1;
                }
            }

            // pastikan payloadPerumahan adalah array
            if (!is_array($payloadPerumahan)) $payloadPerumahan = [];

            // 5) Ambil kode wilayah yang tersedia di payload (jika ada)
            $wilayahKode = $payloadPerumahan['wilayah'] ?? $payloadPerumahan['wilayah'] ?? [];
            if (!is_array($wilayahKode)) $wilayahKode = [];

            // default nama wilayah kosong
            $wilayahNama = [
                'provinsi'  => '',
                'kabupaten' => '',
                'kecamatan' => '',
                'desa'      => ''
            ];

            // jika ada kode desa/kecamatan/kabupaten/provinsi di payload, lookup nama
            $kodeLookup = $wilayahKode['desa'] ?? $wilayahKode['kecamatan'] ?? $wilayahKode['kabupaten'] ?? $wilayahKode['provinsi'] ?? null;
            if ($kodeLookup) {
                try {
                    $hasil = $genModel->getNamaWilayah($kodeLookup);
                    if (is_array($hasil)) {
                        $wilayahNama = array_merge($wilayahNama, $hasil);
                    }
                } catch (\Throwable $e) {
                    log_message('error', "⚠️ [detail] Gagal ambil nama wilayah: " . $e->getMessage());
                }
            }

            // 6) Normalisasi status_kepemilikan:
            // periksa beberapa lokasi yang mungkin menyimpan info ini
            $statusKepemilikan =
                $payloadPerumahan['status_kepemilikan'] ??             // payload root (new format)
                ($payloadPerumahan['kondisi']['status_kepemilikan'] ?? null) ?? // payload.kondisi
                ($payloadPerumahan['kepemilikan_rumah'] ?? null) ??   // alternatif naming
                ($rtData['kepemilikan_rumah'] ?? null) ??             // rt table
                ($kkData['status_kepemilikan_rumah'] ?? null) ??      // kk table field legacy
                ($kkData['status_kepemilikan'] ?? null) ??            // other legacy
                '';

            // 7) MODE: belum ada usulan -> gunakan data utama (dtsen_kk + dtsen_rt)
            if (empty($usulan['id'])) {
                $perumahan = [
                    'kategori_desil'     => $kkData['kategori_desil'] ?? '',
                    'no_kk'              => $kkData['no_kk'] ?? '',
                    'kepala_keluarga'    => $kkData['kepala_keluarga'] ?? '',
                    'alamat'             => $kkData['alamat'] ?? '',
                    'rw'                 => $rtData['rw'] ?? ($kkData['rw'] ?? ''),
                    'rt'                 => $rtData['rt'] ?? ($kkData['rt'] ?? ''),
                    'status_kepemilikan' => $statusKepemilikan,
                    'kategori_adat'      => $kkData['kategori_adat'] ?? 'Tidak',
                    'nama_suku'          => $kkData['nama_suku'] ?? '',
                    'wilayah_nama'       => $wilayahNama,
                    // agar prefill JS punya struktur perumahan.kondisi/sanitasi jika diperlukan
                    'kondisi'            => $rtData ? [
                        'luas_lantai' => $rtData['luas_lantai'] ?? null,
                        'jenis_lantai' => $rtData['jenis_lantai'] ?? null,
                        'jenis_dinding' => $rtData['jenis_dinding'] ?? null,
                        'bahan_bakar' => $rtData['bahan_bakar'] ?? null,
                        'sumber_air'  => $rtData['sumber_air'] ?? null,
                        'sumber_listrik' => $rtData['sumber_listrik'] ?? null,
                    ] : ($payloadPerumahan['kondisi'] ?? []),
                    'sanitasi' => $payloadPerumahan['sanitasi'] ?? []
                ];

                // gabungkan ke payload supaya JS prefill menerima data konsisten
                $payload['perumahan'] = $perumahan;

                // ambil anggota dari tabel utama
                $anggota = $db->table('dtsen_art')
                    ->where('id_kk', $id_kk)
                    ->where('deleted_at', null)
                    ->get()
                    ->getResultArray();

                $data = [
                    'title'     => 'Detail Pembaruan Keluarga',
                    'namaApp'   => nameApp(),
                    'user'      => session()->get(),
                    'kkData'    => $kkData,
                    'rtData'    => $rtData,
                    'perumahan' => $perumahan,
                    'anggota'   => $anggota,
                    'payload'   => $payload,
                    'usulan'    => $usulan,
                    'id_kk'     => $kkData['id_kk'],
                    'sumber'    => 'utama',
                    'kategori_desil' => $kategoriDesil,
                    // 🚀 PASTIKAN BARIS INI ADA DI SINI JUGA MBAH!
                    'is_submitted_ready' => $is_submitted_ready

                ];

                log_message('debug', '✅ [detail] Memuat data dari tabel utama (tidak ada usulan)');
                return view('dtsen/pembaruan/detail', $data);
            }

            // 8) MODE: ada usulan -> gunakan payload (tetap aman jika field tidak ada)
            // pastikan payload['perumahan'] punya struktur dan kita masukkan status/wilayah_nama
            $payload['perumahan'] = $payload['perumahan'] ?? [];
            // pastikan 'kondisi' & 'sanitasi' adalah array agar merge aman di JS
            if (!isset($payload['perumahan']['kondisi']) || !is_array($payload['perumahan']['kondisi'])) {
                $payload['perumahan']['kondisi'] = $payloadPerumahan['kondisi'] ?? [];
            }
            if (!isset($payload['perumahan']['sanitasi']) || !is_array($payload['perumahan']['sanitasi'])) {
                $payload['perumahan']['sanitasi'] = $payloadPerumahan['sanitasi'] ?? [];
            }

            // masukkan nama wilayah hasil lookup agar select2/ajax bisa prefill
            $payload['perumahan']['wilayah_nama'] = $wilayahNama;

            // buat objek perumahan untuk view (mengambil prioritas dari payload)
            $perumahan = [
                'no_kk'              => $payloadPerumahan['no_kk'] ?? $kkData['no_kk'] ?? '',
                'kepala_keluarga'    => $payloadPerumahan['kepala_keluarga'] ?? $kkData['kepala_keluarga'] ?? '',
                'alamat'             => $payloadPerumahan['alamat'] ?? $kkData['alamat'] ?? '',
                'rw'                 => $payloadPerumahan['rw'] ?? $rtData['rw'] ?? '',
                'rt'                 => $payloadPerumahan['rt'] ?? $rtData['rt'] ?? '',
                'status_kepemilikan' => $payloadPerumahan['status_kepemilikan']
                    ?? ($payloadPerumahan['kondisi']['status_kepemilikan'] ?? null)
                    ?? ($payloadPerumahan['kepemilikan_rumah'] ?? null)
                    ?? $statusKepemilikan,
                'kategori_adat'      => $payloadPerumahan['kategori_adat'] ?? '',
                'nama_suku'          => $payloadPerumahan['nama_suku'] ?? '',
                'wilayah_nama'       => $wilayahNama,
                'kondisi'            => $payload['perumahan']['kondisi'],
                'sanitasi'           => $payload['perumahan']['sanitasi'],
            ];

            // ambil anggota dari usulan_art
            $anggota = $db->table('dtsen_usulan_art')
                ->where('dtsen_usulan_id', $usulan['id'])
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();

            $data = [
                'title'     => 'Detail Pembaruan Keluarga',
                'namaApp'   => nameApp(),
                'user'      => session()->get(),
                'kkData'    => $kkData,
                'rtData'    => $rtData,
                'perumahan' => $perumahan,
                'anggota'   => $anggota,
                'payload'   => $payload,
                'usulan'    => $usulan,
                'id_kk'     => $usulan['dtsen_kk_id'] ?? $kkData['id_kk'],
                'sumber'    => 'usulan',
                'kategori_desil' => $kategoriDesil,
                // 🚀 PASTIKAN BARIS INI ADA DI SINI JUGA MBAH!
                'is_submitted_ready' => $is_submitted_ready

            ];

            log_message('debug', '✅ [detail] Memuat data dari dtsen_usulan (draft/usulan)');

            return view('dtsen/pembaruan/detail', $data);
        } catch (\Throwable $e) {
            log_message('error', '❌ [detail] ' . $e->getMessage());
            return view('errors/html/error_general', [
                'message' => 'Gagal memuat detail keluarga: ' . $e->getMessage(),
            ]);
        }
    }

    // 🧩 Fungsi bantu: konversi data KK ke struktur payload kosong
    private function ambilPayloadDariMaster($kk)
    {
        if (!$kk) return [];

        // ambil data RT jika tersedia
        $rt = null;
        if (!empty($kk->id_rt)) {
            $rt = $this->db->table('dtsen_rt')->where('id_rt', $kk->id_rt)->get()->getRow();
        }

        return [
            'perumahan' => [
                'no_kk' => $kk->no_kk ?? '',
                'kepala_keluarga' => $kk->kepala_keluarga ?? '',
                'alamat' => $kk->alamat ?? '',
                'rw' => $rt->rw ?? ($kk->rw ?? ''),   // prefer rt table, fallback kk if ada
                'rt' => $rt->rt ?? ($kk->rt ?? ''),
                'status_kepemilikan' => $kk->status_kepemilikan_rumah ?? '',
                'kategori_adat' => $kk->kategori_adat ?? 'Tidak',
                'nama_suku' => $kk->nama_suku ?? '',
                'konstruksi_rumah' => [
                    'lantai' => $rt->kondisi_lantai ?? '',
                    'dinding' => $rt->kondisi_dinding ?? '',
                    'atap' => $rt->kondisi_atap ?? ''
                ],
                'sumber_air' => $rt->sumber_air ?? '',
                'sanitasi' => $rt->sanitasi ?? '',
                'listrik' => $rt->sumber_listrik ?? '',
            ],
            'foto_geotag' => [
                'foto' => [
                    'ktp_kk' => $kk->foto_kk ?? '',
                    'depan' => $kk->foto_rumah ?? '',
                    'dalam' => $kk->foto_rumah_dalam ?? ''
                ],
                'geotag' => [
                    'lat' => $rt->latitude ?? null,
                    'lng' => $rt->longitude ?? null
                ]
            ]
        ];
    }

    /**
     * 💾 Simpan data keluarga (tab Perumahan)
     * - Data disimpan ke dtsen_usulan.payload (JSON)
     * - Merge dengan payload lama agar data rumah & wilayah tidak hilang
     */
    public function saveKeluarga()
    {
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('id_user') ?? $session->get('user_id') ?? $session->get('id') ?? 0;
        $mode = $post['sumber'] ?? 'utama'; // 'utama' | 'baru' | 'draft'

        try {
            $idKk = $post['id_kk'] ?? null;

            // ==========================================================
            // 🟡 MODE TAMBAH — Buat entri RT + KK baru
            // ==========================================================
            if (empty($idKk) && $mode === 'baru') {
                $kodeDesa = $session->get('kode_desa') ?? null;
                $rw = trim($post['rw'] ?? '');
                $rt = trim($post['rt'] ?? '');

                // 🔹 1️⃣ Buat entri RT baru terlebih dahulu
                $dataRT = [
                    'kode_desa'         => $kodeDesa,
                    'rw'                => $rw,
                    'rt'                => $rt,
                    // 'alamat'            => trim($post['alamat']),
                    'kepemilikan_rumah' => $post['status_rumah'] ?? 'Lainnya',
                    'source_name'       => 'saveKeluarga_baru',
                    'created_by'        => $userId,
                    'created_at'        => date('Y-m-d H:i:s')
                ];

                $this->db->table('dtsen_rt')->insert($dataRT);
                $idRt = $this->db->insertID();

                // 🔹 2️⃣ Buat entri KK baru yang terhubung ke RT baru
                $dataKK = [
                    'id_rt'                    => $idRt,
                    'no_kk'                    => trim($post['no_kk']),
                    'kepala_keluarga'          => trim($post['kepala_keluarga']),
                    // 'alamat'                   => trim($post['alamat']),
                    'status_kepemilikan_rumah' => $post['status_rumah'] ?? 'Lainnya',
                    'kategori_adat'            => $post['kategori_adat'] ?? 'Tidak',
                    'nama_suku'                => $post['nama_suku'] ?? '',
                    'created_by'               => $userId,
                    'created_at'               => date('Y-m-d H:i:s'),
                ];

                $this->db->table('dtsen_kk')->insert($dataKK);
                $idKk = $this->db->insertID();

                // 🔹 3️⃣ Buat dtsen_usulan (draft baru)
                $payloadBaru = ['perumahan' => $dataKK];
                $this->db->table('dtsen_usulan')->insert([
                    'usulan_no'    => 'PDK-' . date('ymdHis'),
                    'jenis'        => 'keluarga_baru',
                    'status'       => 'draft',
                    'dtsen_kk_id'  => $idKk,
                    'no_kk_target' => $dataKK['no_kk'],
                    'created_by'   => $userId,
                    'payload'      => json_encode($payloadBaru, JSON_UNESCAPED_UNICODE),
                    'created_at'   => date('Y-m-d H:i:s'),
                    'summary'      => 'Keluarga baru dibuat oleh ' . ($session->get('nama') ?? 'Sistem')
                ]);

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Keluarga baru berhasil dibuat.',
                    'id_kk'   => $idKk,
                    'id_rt'   => $idRt
                ]);
            }

            // ==========================================================
            // 🟢 MODE PEMBARUAN / DRAFT
            // ==========================================================
            $kkData = $this->db->table('dtsen_kk')
                ->where('id_kk', $idKk)
                ->get()
                ->getRowArray();

            if (!$kkData) {
                throw new \Exception('Data KK tidak ditemukan atau tidak valid.');
            }

            // 🔍 AMBIL DATA RUMAH (RT) LAMA SEBAGAI BACKUP
            $rtData = $this->db->table('dtsen_rt')
                ->where('id_rt', $kkData['id_rt'])
                ->get()
                ->getRowArray() ?? [];

            // 💾 Siapkan data baru dari form (Hanya menimpa yang dikirim dari tab_keluarga)
            $perumahanBaru = [
                'no_kk'              => $post['no_kk'] ?? $kkData['no_kk'],
                'kepala_keluarga'    => $post['kepala_keluarga'] ?? $kkData['kepala_keluarga'],
                // Tetap bawa data wilayah lama agar tidak hilang di draft
                'alamat'             => $rtData['alamat'] ?? $kkData['alamat'] ?? '',
                'rw'                 => $rtData['rw'] ?? '',
                'rt'                 => $rtData['rt'] ?? '',
                'status_kepemilikan' => $rtData['kepemilikan_rumah'] ?? '',
                'kategori_adat'      => $kkData['kategori_adat'] ?? 'Tidak',
                'nama_suku'          => $kkData['nama_suku'] ?? ''
            ];

            // 🔍 Cek apakah ada usulan aktif
            $usulan = $this->db->table('dtsen_usulan')
                ->where('dtsen_kk_id', $idKk)
                ->whereIn('status', ['draft', 'submitted'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if ($usulan) {
                // ✅ Merge dengan payload lama TANPA kehilangan data rumah/wilayah
                $payloadLama = json_decode($usulan['payload'] ?? '{}', true);
                if (!is_array($payloadLama)) $payloadLama = [];
                $payloadLama['perumahan'] = $payloadLama['perumahan'] ?? [];

                // pertahankan sub-bagian lama
                $payloadLama['perumahan']['kondisi']  = $payloadLama['perumahan']['kondisi']  ?? [];
                $payloadLama['perumahan']['sanitasi'] = $payloadLama['perumahan']['sanitasi'] ?? [];
                $payloadLama['perumahan']['wilayah']  = $payloadLama['perumahan']['wilayah']  ?? [];

                // merge di tingkat root perumahan
                $payloadGabungan = array_merge($payloadLama['perumahan'], $perumahanBaru);

                // pertahankan subarray kondisi/sanitasi/wilayah
                $payloadGabungan['kondisi']  = $payloadLama['perumahan']['kondisi'];
                $payloadGabungan['sanitasi'] = $payloadLama['perumahan']['sanitasi'];
                $payloadGabungan['wilayah']  = $payloadLama['perumahan']['wilayah'];

                $payloadLama['perumahan'] = $payloadGabungan;

                $this->db->table('dtsen_usulan')
                    ->where('id', $usulan['id'])
                    ->update([
                        'payload'    => json_encode($payloadLama, JSON_UNESCAPED_UNICODE),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => $userId,
                        'summary'    => 'Data keluarga diperbarui oleh ' . ($session->get('nama') ?? 'Sistem')
                    ]);
            } else {
                // 🆕 Buat draft baru kalau belum ada
                // SUNTIKAN DATA RUMAH LAMA KE DALAM PAYLOAD BARU
                $payloadBaru = [
                    'perumahan' => array_merge($perumahanBaru, [
                        'kondisi' => [
                            'luas_lantai'    => $rtData['luas_lantai'] ?? '',
                            'jenis_lantai'   => $rtData['jenis_lantai'] ?? '',
                            'jenis_dinding'  => $rtData['jenis_dinding'] ?? '',
                            'jenis_atap'     => $rtData['kondisi_atap'] ?? '',
                            'bahan_bakar'    => $rtData['bahan_bakar'] ?? '',
                            'sumber_air'     => $rtData['sumber_air'] ?? '',
                            'sumber_listrik' => $rtData['sumber_listrik'] ?? ''
                        ],
                        'sanitasi' => [
                            'pembuangan_tinja' => $rtData['sanitasi'] ?? ''
                        ]
                    ]),
                    'geo' => [
                        'lat' => $rtData['latitude'] ?? '',
                        'lng' => $rtData['longitude'] ?? ''
                    ]
                ];

                $this->db->table('dtsen_usulan')->insert([
                    'usulan_no'    => 'PDK-' . date('ymdHis'),
                    'jenis'        => 'pembaruan',
                    'status'       => 'draft',
                    'dtsen_kk_id'  => $idKk,
                    'no_kk_target' => $kkData['no_kk'],
                    'created_by'   => $userId,
                    'payload'      => json_encode($payloadBaru, JSON_UNESCAPED_UNICODE),
                    'created_at'   => date('Y-m-d H:i:s'),
                    'summary'      => 'Data keluarga baru dibuat oleh ' . ($session->get('nama') ?? 'Sistem')
                ]);
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data keluarga berhasil disimpan.',
                'id_kk'   => $idKk
            ]);
        } catch (\Throwable $e) {
            log_message('error', '❌ saveKeluarga() error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // 🗑️ Hapus anggota
    public function deleteAnggota()
    {
        try {
            $idArt = $this->request->getPost('id_art');
            $reason = trim($this->request->getPost('reason'));

            if (!$idArt) {
                return $this->response->setJSON(['status' => false, 'message' => 'ID anggota tidak valid']);
            }

            if ($reason === '') {
                return $this->response->setJSON(['status' => false, 'message' => 'Alasan wajib diisi']);
            }

            $db = \Config\Database::connect();

            // 1️⃣ Periksa apakah sedang dalam usulan aktif
            $usulan = $db->table('dtsen_usulan_art')
                ->where('id', $idArt)
                ->get()->getRowArray();

            // ============================
            // CASE 1 → Hapus dari USULAN
            // ============================
            if ($usulan) {

                $db->table('dtsen_usulan_art')
                    ->where('id', $idArt)
                    ->update([
                        'deleted_at'    => date('Y-m-d H:i:s'),
                        'delete_reason' => $reason
                    ]);

                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Anggota berhasil dihapus dari draf.'
                ]);
            }

            // ============================
            // CASE 2 → Hapus dari DATA UTAMA (dtsen_art)
            // ============================
            $utama = $db->table('dtsen_art')->where('id_art', $idArt)->get()->getRowArray();

            if ($utama) {
                $db->table('dtsen_art')
                    ->where('id_art', $idArt)
                    ->update([
                        'deleted_at'    => date('Y-m-d H:i:s'),
                        'delete_reason' => $reason
                    ]);

                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Anggota berhasil dihapus dari data utama.'
                ]);
            }

            return $this->response->setJSON(['status' => false, 'message' => 'Data anggota tidak ditemukan']);
        } catch (\Throwable $e) {
            log_message('error', '[deleteAnggota] ' . $e->getMessage());
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteKeluarga()
    {
        // 🚀 PERBAIKAN: Tangkap dari form POST biasa, dengan fallback JSON
        $id = $this->request->getPost('id') ?? ($this->request->getJSON()->id ?? null);

        if (!$id) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'ID tidak diberikan.'
            ]);
        }

        try {
            // Hard delete
            $this->db->table('dtsen_usulan_art')->where('dtsen_usulan_id', $id)->delete();
            $this->db->table('dtsen_usulan')->where('id', $id)->delete();

            // ... (sisanya biarkan sama)

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Usulan berhasil dihapus permanen.'
            ]);
        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🏠 Simpan data Tab “Keterangan Perumahan”
     * - Data disimpan ke dtsen_usulan.payload (JSON)
     * - Mengamankan data dari null-overwrite (Efek Domino)
     * - Update dtsen_rt secara bertahap sesuai status
     */
    public function saveRumah()
    {
        try {
            $post = $this->request->getPost();
            $user = session()->get();

            // ==========================================
            // 1️⃣ VALIDASI INPUT 
            // ==========================================
            $sumberListrik  = trim($post['sumber_listrik'] ?? '');
            $nomorPelanggan = trim($post['nomor_pelanggan'] ?? '');
            $nomorMeter     = trim($post['nomor_meter'] ?? '');

            if ($sumberListrik === 'Listrik PLN dengan meteran') {
                if ($nomorPelanggan === '') {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor Pelanggan wajib diisi.']);
                }
                if ($nomorMeter === '') {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor Meter wajib diisi.']);
                }
                if (!preg_match('/^[0-9]{11,13}$/', $nomorPelanggan)) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor Pelanggan harus 11-13 digit angka.']);
                }
                if (!preg_match('/^[0-9]{8,13}$/', $nomorMeter)) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor Meter harus 8-13 digit angka.']);
                }
            }

            if (empty($post['alamat']) || empty($post['rt']) || empty($post['rw'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Alamat, RT, dan RW wajib diisi agar tidak hilang dari dashboard.'
                ]);
            } else {
                if ($nomorPelanggan !== '' && !preg_match('/^[0-9]{11,13}$/', $nomorPelanggan)) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor Pelanggan harus 11-13 digit.']);
                }
                if ($nomorMeter !== '' && !preg_match('/^[0-9]{8,13}$/', $nomorMeter)) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor Meter harus 8-13 digit.']);
                }
            }

            // ==========================================
            // 2️⃣ PERSIAPAN DATA & AMBIL BACKUP LAMA
            // ==========================================
            $usulanId = $post['dtsen_usulan_id'] ?? null;
            if (!$usulanId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ID usulan tidak ditemukan.']);
            }

            $this->db->transBegin();

            // Ambil usulan aktif
            $usulanRow = $this->db->table('dtsen_usulan')
                ->select('id, payload, status, dtsen_kk_id')
                ->where('id', $usulanId)
                ->get()
                ->getRowArray();

            if (!$usulanRow) {
                throw new \Exception('Data usulan tidak ditemukan.');
            }

            // Ambil id_rt dari dtsen_kk untuk sinkronisasi
            $idRt = $this->db->table('dtsen_kk')->select('id_rt')->where('id_kk', $usulanRow['dtsen_kk_id'])->get()->getRow('id_rt');

            // Ambil data dtsen_rt lama sebagai "Sabuk Pengaman" (Anti-Null)
            $rtLama = [];
            if ($idRt) {
                $rtLama = $this->db->table('dtsen_rt')->where('id_rt', $idRt)->get()->getRowArray() ?? [];
            }

            $payloadLama = json_decode($usulanRow['payload'] ?? '{}', true);
            if (!is_array($payloadLama)) $payloadLama = [];
            $gabungan = $payloadLama['perumahan'] ?? [];

            // ==========================================
            // 3️⃣ MERGE KE PAYLOAD JSON
            // ==========================================
            // Overwrite level root
            $gabungan['alamat']             = trim($post['alamat']);
            $gabungan['rw']                 = trim($post['rw']);
            $gabungan['rt']                 = trim($post['rt']);
            $gabungan['status_kepemilikan'] = $post['status_kepemilikan'] ?? $gabungan['status_kepemilikan'] ?? '';

            // Overwrite level array (wilayah, kondisi, sanitasi)
            $gabungan['wilayah'] = array_merge($gabungan['wilayah'] ?? [], [
                'provinsi'  => $post['provinsi'] ?? '',
                'kabupaten' => $post['regency'] ?? '',
                'kecamatan' => $post['district'] ?? '',
                'desa'      => $post['village'] ?? ''
            ]);

            $gabungan['kondisi'] = array_merge($gabungan['kondisi'] ?? [], [
                'luas_lantai'     => (float)($post['luas_lantai'] ?? 0),
                'jenis_lantai'    => $post['jenis_lantai'] ?? '',
                'jenis_dinding'   => $post['jenis_dinding'] ?? '',
                'jenis_atap'      => $post['jenis_atap'] ?? '',
                'bahan_bakar'     => $post['bahan_bakar'] ?? '',
                'sumber_air'      => $post['sumber_air'] ?? '',
                'sumber_listrik'  => $post['sumber_listrik'] ?? '',
                'nomor_pelanggan' => $post['nomor_pelanggan'] ?? '',
                'nomor_meter'     => $post['nomor_meter'] ?? '',
                'daya_listrik'    => $post['daya_listrik'] ?? ''
            ]);

            $gabungan['sanitasi'] = array_merge($gabungan['sanitasi'] ?? [], [
                'fasilitas_bab'       => $post['fasilitas_bab'] ?? '',
                'jenis_kloset'        => $post['jenis_kloset'] ?? '',
                'jarak_air_ke_limbah' => $post['jarak_air_ke_limbah'] ?? '',
                'pembuangan_tinja'    => $post['pembuangan_tinja'] ?? ''
            ]);

            // Bersihkan properti konflik/redundant
            unset($gabungan['kondisi']['kepemilikan_rumah'], $gabungan['kondisi']['status_kepemilikan'], $gabungan['wilayah']['alamat']);

            // Simpan JSON ke database usulan
            $payloadLama['perumahan'] = $gabungan;
            $this->db->table('dtsen_usulan')->where('id', $usulanId)->update([
                'payload'    => json_encode($payloadLama, JSON_UNESCAPED_UNICODE),
                'updated_at' => date('Y-m-d H:i:s'),
                'summary'    => 'Data rumah diperbarui oleh ' . ($user['nama'] ?? 'Sistem')
            ]);

            // ==========================================================
            // 4️⃣ SINKRONISASI KE TABEL dtsen_rt (Di dalam saveRumah)
            // ==========================================================
            if ($idRt) {
                // Skenario A: Masih Draft, tapi butuh sinkronisasi wilayah (RT/RW/Desa/Alamat)
                if ($usulanRow['status'] !== 'applied') {

                    $rtBaru     = trim($post['rt'] ?? '');
                    $rwBaru     = trim($post['rw'] ?? '');
                    $desaBaru   = trim($post['village'] ?? '');
                    $alamatBaru = trim($post['alamat'] ?? ''); // 🚀 Ambil data alamat baru

                    // 🚀 Cek apakah ada perubahan KODE DESA, RT, RW, atau ALAMAT LENGKAP
                    if ($rtBaru !== $rtLama['rt'] || $rwBaru !== $rtLama['rw'] || ($desaBaru !== '' && $desaBaru !== $rtLama['kode_desa']) || $alamatBaru !== ($rtLama['alamat'] ?? '')) {

                        // Hitung apakah ada KK lain di id_rt yang sama
                        $jumlahPenghuni = $this->db->table('dtsen_kk')->where('id_rt', $idRt)->countAllResults();

                        // Deteksi apakah ini murni pindah wilayah atau cuma revisi typo alamat
                        $isPindahWilayah = ($rtBaru !== $rtLama['rt'] || $rwBaru !== $rtLama['rw'] || ($desaBaru !== '' && $desaBaru !== $rtLama['kode_desa']));

                        if ($jumlahPenghuni > 1 && $isPindahWilayah) {
                            // 🚨 PECAH ALAMAT DINI: 
                            // Buat "Rumah Kosong" baru di wilayah yang baru agar tidak menyeret KK lain
                            $this->db->table('dtsen_rt')->insert([
                                'rw'          => $rwBaru,
                                'rt'          => $rtBaru,
                                'kode_desa'   => !empty($post['village']) ? $post['village'] : ($rtLama['kode_desa'] ?? null),
                                'alamat'      => $alamatBaru, // 🚀 Simpan alamat lengkap
                                'created_at'  => date('Y-m-d H:i:s'),
                                'created_by'  => $user['id'] ?? 'system',
                                'source_name' => 'pecah_alamat_draft_' . $usulanId
                            ]);

                            $idRtBaru = $this->db->insertID();

                            // Pindahkan HANYA KK ini ke id_rt yang baru
                            $this->db->table('dtsen_kk')->where('id_kk', $usulanRow['dtsen_kk_id'])->update([
                                'id_rt' => $idRtBaru
                            ]);
                        } else {
                            // Cuma sendirian di id_rt ini, ATAU hanya sekadar revisi tulisan alamat (typo)
                            $this->db->table('dtsen_rt')->where('id_rt', $idRt)->update([
                                'rw'          => $rwBaru,
                                'rt'          => $rtBaru,
                                'kode_desa'   => !empty($post['village']) ? $post['village'] : ($rtLama['kode_desa'] ?? null),
                                'alamat'      => $alamatBaru, // 🚀 Update alamat lengkap
                                'updated_at'  => date('Y-m-d H:i:s'),
                                'updated_by'  => $user['nama'] ?? 'system'
                            ]);
                        }
                    }
                }

                // Skenario B: Jika status sudah "applied" (Update lengkap)
                if ($usulanRow['status'] === 'applied') {

                    $updateRT = [];

                    $updateRT['kode_desa']         = (!empty($post['village'])) ? $post['village'] : ($rtLama['kode_desa'] ?? null);
                    $updateRT['rt']                = trim($post['rt'] ?? $rtLama['rt'] ?? '');
                    $updateRT['rw']                = trim($post['rw'] ?? $rtLama['rw'] ?? '');

                    // 🚀 Alamat Lengkap sudah ada di sini, aman!
                    $updateRT['alamat']            = trim($post['alamat']);
                    $updateRT['kepemilikan_rumah'] = (!empty($post['status_kepemilikan'])) ? $post['status_kepemilikan'] : ($rtLama['kepemilikan_rumah'] ?? '');

                    // Gunakan !== '' untuk angka agar input 0 tidak ditolak
                    $updateRT['luas_lantai']       = (isset($post['luas_lantai']) && $post['luas_lantai'] !== '') ? (float)$post['luas_lantai'] : ($rtLama['luas_lantai'] ?? 0);

                    $updateRT['jenis_lantai']      = (!empty($post['jenis_lantai'])) ? $post['jenis_lantai'] : ($rtLama['jenis_lantai'] ?? '');
                    $updateRT['jenis_dinding']     = (!empty($post['jenis_dinding'])) ? $post['jenis_dinding'] : ($rtLama['kondisi_dinding'] ?? '');
                    $updateRT['bahan_bakar']       = (!empty($post['bahan_bakar'])) ? $post['bahan_bakar'] : ($rtLama['bahan_bakar'] ?? '');
                    $updateRT['sumber_air']        = (!empty($post['sumber_air'])) ? $post['sumber_air'] : ($rtLama['sumber_air'] ?? '');
                    $updateRT['sumber_listrik']    = (!empty($post['sumber_listrik'])) ? $post['sumber_listrik'] : ($rtLama['sumber_listrik'] ?? '');

                    $this->db->table('dtsen_rt')->where('id_rt', $idRt)->update($updateRT);
                }
            }

            // ==========================================================
            // ✅ 5️⃣ HAPUS BENDERA PEMULIHAN (WISUDA DATA)
            // ==========================================================
            $this->db->table('dtsen_kk')
                ->where('id_kk', $usulanRow['dtsen_kk_id'])
                ->update(['is_recovery_needed' => 0]);

            $this->db->transCommit();

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data rumah dan sinkronisasi wilayah berhasil disimpan.'
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', '❌ saveRumah() error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data rumah: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🧱 Simpan Data Kepemilikan Aset
     * - Merge payload lama + baru (tidak overwrite penuh)
     * - Struktur JSON tetap utuh seperti tab lain (geo, foto, perumahan, dll)
     */
    public function saveAset()
    {
        try {
            log_message('error', 'POST DATA: ' . json_encode($this->request->getPost()));

            $request  = service('request');
            $usulanId = $request->getPost('dtsen_usulan_id');
            $userId   = session()->get('id_user') ?? session()->get('user_id') ?? session()->get('id') ?? 0;

            // 🔒 Validasi ID usulan
            if (empty($usulanId)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'ID usulan tidak ditemukan.'
                ]);
            }

            $luasSawah  = trim($request->getPost('luas_sawah') ?? '');
            $rumahLain  = trim($request->getPost('rumah_lain') ?? '');

            if ($luasSawah === '') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Luas sawah / kebun wajib dipilih.'
                ]);
            }

            if ($rumahLain === '') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Kepemilikan rumah lain wajib dipilih.'
                ]);
            }

            // 🔍 Ambil data usulan
            $usulan = $this->db->table('dtsen_usulan')
                ->select('id, payload, status')
                ->where('id', $usulanId)
                ->get()
                ->getRowArray();

            if (!$usulan) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data usulan tidak ditemukan di database.'
                ]);
            }

            // 🔄 Decode payload lama
            $payloadLama = json_decode($usulan['payload'] ?? '{}', true);
            if (!is_array($payloadLama)) $payloadLama = [];
            log_message('error', 'PAYLOAD BARU: ' . json_encode($payloadLama));

            // Siapkan struktur minimum
            $payloadLama['aset'] = $payloadLama['aset'] ?? [];

            // 🗂️ Ambil data aset baru dari POST
            $asetBaru = [
                // === ASET BERGERAK ===
                'tabung_gas'    => $request->getPost('tabung_gas') ?? 0,
                'kulkas'        => $request->getPost('kulkas') ?? 0,
                'ac'            => $request->getPost('ac') ?? 0,
                'water_heater'  => $request->getPost('water_heater') ?? 0,
                'telepon_rumah' => $request->getPost('telepon_rumah') ?? 0,
                'tv_lcd'        => $request->getPost('tv_lcd') ?? 0,
                'emas'          => $request->getPost('emas') ?? 0,
                'laptop'        => $request->getPost('laptop') ?? 0,
                'sepeda_motor'  => $request->getPost('sepeda_motor') ?? 0,
                'sepeda'        => $request->getPost('sepeda') ?? 0,
                'mobil'         => $request->getPost('mobil') ?? 0,
                'perahu'        => $request->getPost('perahu') ?? 0,
                'kapal_motor'   => $request->getPost('kapal_motor') ?? 0,
                'smartphone'    => $request->getPost('smartphone') ?? 0,

                // === TERNAK ===
                'sapi'          => $request->getPost('sapi') ?? 0,
                'kerbau'        => $request->getPost('kerbau') ?? 0,
                'kuda'          => $request->getPost('kuda') ?? 0,
                'kambing'       => $request->getPost('kambing') ?? 0,
                'babi'          => $request->getPost('babi') ?? 0,

                // === ASET TIDAK BERGERAK ===
                'luas_sawah'     => $request->getPost('luas_sawah') ?? '',
                'memiliki_lahan' => $request->getPost('memiliki_lahan') ?? '',
                'rumah_lain'     => $request->getPost('rumah_lain') ?? ''
            ];

            // ⚙️ Merge data aset lama + baru
            $asetGabungan = array_merge($payloadLama['aset'], $asetBaru);
            $payloadLama['aset'] = $asetGabungan;

            // 💾 Simpan hasil gabungan ke database
            $this->db->table('dtsen_usulan')
                ->where('id', $usulanId)
                ->update([
                    'payload'    => json_encode($payloadLama, JSON_UNESCAPED_UNICODE),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $userId,
                    'summary'    => 'Data aset diperbarui oleh ' . (session()->get('nama') ?? 'Sistem')
                ]);

            return $this->response->setJSON([
                'debug_payload' => $payloadLama,
                'status'  => 'success',
                'message' => 'Data aset berhasil disimpan.'
            ]);
        } catch (\Throwable $e) {
            log_message('error', '❌ saveAset() error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 📸 Simpan Foto & GeoTag
     * - Merge payload lama + baru (tidak overwrite penuh)
     * - Menyimpan file foto (ktp_kk, depan, dalam)
     * - Menyimpan koordinat geo (lat, lng)
     * - Kirim WhatsApp ke Admin
     */
    public function saveFoto()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        try {
            $session = session();
            $userId  = $session->get('id_user') ?? $session->get('user_id') ?? $session->get('id') ?? 0;

            $usulanId = $this->request->getPost('dtsen_usulan_id');
            if (empty($usulanId)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal! ID usulan tidak ditemukan.'
                ]);
            }

            // 🔍 Ambil data usulan
            $usulan = $this->db->table('dtsen_usulan')
                ->select('id, payload')
                ->where('id', $usulanId)
                ->get()
                ->getRowArray();

            if (!$usulan) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data usulan tidak ditemukan di database.'
                ]);
            }

            // 🔄 Decode payload lama
            $payloadLama = json_decode($usulan['payload'] ?? '{}', true);
            if (!is_array($payloadLama)) $payloadLama = [];

            // Siapkan struktur minimal
            $payloadLama['foto'] = $payloadLama['foto'] ?? [];
            $payloadLama['geo']  = $payloadLama['geo']  ?? [];

            // ==========================================
            // 🔒 VALIDASI WAJIB: FOTO + GEOTAG HARUS ADA
            // ==========================================
            $fotoLama = $payloadLama['foto'] ?? [];
            $geoLama  = $payloadLama['geo'] ?? [];

            $ternyataBelumAdaFotoKTP   = empty($fotoLama['ktp_kk'])   && !$this->request->getFile('foto_ktp')->isValid();
            $ternyataBelumAdaFotoDepan = empty($fotoLama['depan'])    && !$this->request->getFile('foto_depan')->isValid();
            $ternyataBelumAdaFotoDalam = empty($fotoLama['dalam'])    && !$this->request->getFile('foto_dalam')->isValid();

            $latBaru = $this->request->getPost('latitude');
            $lngBaru = $this->request->getPost('longitude');

            $geoKosong = (empty($geoLama['lat']) && empty($geoLama['lng']) && (empty($latBaru) || empty($lngBaru)));

            if ($ternyataBelumAdaFotoKTP || $ternyataBelumAdaFotoDepan || $ternyataBelumAdaFotoDalam || $geoKosong) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan! Semua foto dan titik lokasi (Geotag) wajib diisi terlebih dahulu.'
                ]);
            }

            // 📁 Direktori upload
            $uploadBase = FCPATH . 'data/usulan/';
            $dirs = [
                'foto_identitas'   => $uploadBase . 'foto_identitas/',
                'foto_rumah'       => $uploadBase . 'foto_rumah/',
                'foto_rumah_dalam' => $uploadBase . 'foto_rumah_dalam/',
            ];
            foreach ($dirs as $dir) {
                if (!is_dir($dir)) mkdir($dir, 0777, true);
            }

            // ==========================================
            // Ambil No KK dari POST (lebih akurat daripada database)
            // ==========================================

            // Ambil data dari POST dengan fallback aman (hindari trim(null) deprecated)
            $noKKManualRaw    = $this->request->getPost('no_kk');
            $kepalaKeluargaRaw = $this->request->getPost('kepala_keluarga');
            $latRaw           = $this->request->getPost('latitude');
            $lngRaw           = $this->request->getPost('longitude');

            // Cast ke string / fallback supaya trim tidak menerima null
            $noKKManual     = trim((string) ($noKKManualRaw ?? ''));
            $kepalaKeluarga = trim((string) ($kepalaKeluargaRaw ?? ''));
            $lat            = trim((string) ($latRaw ?? ''));
            $lng            = trim((string) ($lngRaw ?? ''));

            // fallback jika kosong
            if ($noKKManual === '') {
                // coba ambil dari DB (seperti rencana fallback sebelumnya)
                $kkId = $this->db->table('dtsen_usulan')
                    ->select('dtsen_kk_id')
                    ->where('id', $usulanId)
                    ->get()
                    ->getRow('dtsen_kk_id');

                $noKKdb = $this->db->table('dtsen_kk')
                    ->select('no_kk')
                    ->where('id_kk', $kkId)
                    ->get()
                    ->getRow('no_kk');

                $noKK = $noKKdb ?? 'unknown';
            } else {
                $noKK = $noKKManual;
            }

            // nama kepala keluarga fallback
            if ($kepalaKeluarga === '') {
                $kepalaKeluarga = $payloadLama['kepala_keluarga'] ?? ($kk['kepala_keluarga'] ?? 'Tidak ditemukan');
            }

            // normalisasi lat/lng untuk nama file (tetap simpan original untuk watermark text)
            $latForFile = str_replace([' ', ','], ['_', '_'], $lat !== '' ? $lat : '0');
            $lngForFile = str_replace([' ', ','], ['_', '_'], $lng !== '' ? $lng : '0');


            // ===========================================
            $kodeDesaFull = session()->get('kode_desa'); // contoh: 32.05.33.2006

            $WilayahModel = new \App\Models\WilayahModel();

            $desaRow = $WilayahModel
                ->select("
                        tb_villages.name      AS desa,
                        tb_districts.name     AS kecamatan,
                        tb_regencies.name     AS kabupaten,
                        tb_provinces.name     AS provinsi
                    ")
                ->join('tb_districts', 'tb_districts.id = tb_villages.district_id', 'left')
                ->join('tb_regencies', 'tb_regencies.id = tb_villages.regency_id', 'left')
                ->join('tb_provinces', 'tb_provinces.id = tb_villages.province_id', 'left')
                ->where('tb_villages.id', $kodeDesaFull)
                ->get()
                ->getRowArray();

            // Fallback aman
            $namaDesa      = strtoupper($desaRow['desa']      ?? '-');
            $namaKecamatan = strtoupper($desaRow['kecamatan'] ?? '-');
            $namaKabupaten = strtoupper($desaRow['kabupaten'] ?? '-');
            $namaProvinsi  = strtoupper($desaRow['provinsi']  ?? '-');

            $wilayahFull = "Desa {$namaDesa}, Kec. {$namaKecamatan}, Kab. {$namaKabupaten}, Prov. {$namaProvinsi}";

            // dd($wilayahFull);


            // Latitude & longitude untuk nama file
            $lat  = $this->request->getPost('latitude')  ?? '0';
            $lng  = $this->request->getPost('longitude') ?? '0';

            // Normalisasi karakter untuk nama file
            $lat = str_replace([' ', ','], ['_', '_'], $lat);
            $lng = str_replace([' ', ','], ['_', '_'], $lng);

            // 🧩 Mapping field foto dari form
            $fotoFields = [
                'foto_ktp'   => ['path' => 'foto_identitas/',   'key' => 'ktp_kk'],
                'foto_depan' => ['path' => 'foto_rumah/',       'key' => 'depan'],
                'foto_dalam' => ['path' => 'foto_rumah_dalam/', 'key' => 'dalam'],
            ];

            // 🖼️ Proses upload foto dengan nama file format baru
            $fotoGabungan = $payloadLama['foto'];

            foreach ($fotoFields as $field => $opt) {
                $file = $this->request->getFile($field);

                if ($file && $file->isValid() && !$file->hasMoved()) {

                    $timestamp = time();

                    // nama file (normalisasi)
                    $newName = 'sinden_'
                        . preg_replace('/\s+/', '', $noKK) . '_'
                        . $field . '_'
                        . $latForFile . '_'
                        . $lngForFile . '_'
                        . $timestamp . '.'
                        . $file->getExtension();

                    $finalPath = $uploadBase . $opt['path'] . $newName;

                    // pindah file
                    $file->move($uploadBase . $opt['path'], $newName, true);

                    // watermark only for foto_depan & foto_dalam (jangan watermark foto_ktp)
                    if ($field !== 'foto_ktp') {
                        // gunakan nilai lat/lng asli (bukan yang dinormalisasi untuk filename)
                        $latText = $lat !== '' ? $lat : ($geoGabungan['lat'] ?? '-');
                        $lngText = $lng !== '' ? $lng : ($geoGabungan['lng'] ?? '-');

                        // call helper watermark premium
                        applyWatermarkPremium($finalPath, [
                            'no_kk'     => (string) $noKK,
                            'kepala'    => (string) $kepalaKeluarga,
                            'petugas'   => (string) ($session->get('fullname') ?? 'Petugas'),
                            'tanggal'   => date('d F Y'),
                            'latitude'  => (string) ($latText ?? ($geoGabungan['lat'] ?? '-')),
                            'longitude' => (string) ($lngText ?? ($geoGabungan['lng'] ?? '-')),
                            'wilayah'   => (string) ($wilayahFull ?? '')
                        ]);
                        // 🔧 FINAL HARD COMPRESSION (WAJIB)
                        recompressImageToTarget($finalPath, 500);
                    }

                    // simpan ke payload
                    $fotoGabungan[$opt['key']] = 'data/usulan/' . $opt['path'] . $newName;
                }
            }

            // 📍 GeoTag (merge data baru dengan lama)
            $geoGabungan = $payloadLama['geo'];
            $geoGabungan['lat'] = $this->request->getPost('latitude')  ?? $geoGabungan['lat'] ?? null;
            $geoGabungan['lng'] = $this->request->getPost('longitude') ?? $geoGabungan['lng'] ?? null;

            // 🧩 Gabungkan hasil ke payload utama
            $payloadLama['foto'] = $fotoGabungan;
            $payloadLama['geo']  = $geoGabungan;

            // 💾 Simpan ke database
            $this->db->table('dtsen_usulan')
                ->where('id', $usulanId)
                ->update([
                    'payload'     => json_encode($payloadLama, JSON_UNESCAPED_UNICODE),
                    'updated_by'  => $userId,
                    'updated_at'  => date('Y-m-d H:i:s'),
                    'summary'     => 'Foto & GeoTag diperbarui oleh ' . ($session->get('nama') ?? 'Sistem')
                ]);

            // ===============================================
            // 📲 Kirim Notifikasi WhatsApp ke Admin
            // ===============================================

            // 1. Ambil dtsen_kk_id dari dtsen_usulan
            $usulanRow = $this->db->table('dtsen_usulan')
                ->select('dtsen_kk_id, created_by')
                ->where('id', $usulanId)
                ->get()
                ->getRowArray();

            $kkId = $usulanRow['dtsen_kk_id'] ?? null;
            $creatorUserId = $usulanRow['created_by'] ?? null;

            // 2. Ambil data KK
            $kk = $this->db->table('dtsen_kk')
                ->select('no_kk, kepala_keluarga, alamat')
                ->where('id_kk', $kkId)
                ->get()
                ->getRowArray();

            $namaKK   = $kk['kepala_keluarga'] ?? '-';
            $noKK     = $kk['no_kk'] ?? '-';
            $alamatKK = $kk['alamat'] ?? '-';

            // 3. Ambil kode_desa petugas (login)
            $kodeDesaPetugas = session()->get('kode_desa');

            // 4. Ambil semua admin (role_id=3) dalam desa yang sama
            $admins = $this->db->table('dtks_users')
                ->select('fullname, nope')
                ->where('role_id', 3)
                ->where('kode_desa', $kodeDesaPetugas)
                ->get()
                ->getResultArray();

            // Jika tidak ada admin → log dan skip
            if (empty($admins)) {
                log_message('warning', "[WA Foto] Tidak ditemukan admin role_id=3 pada desa {$kodeDesaPetugas}");
            } else {

                // === Format tanggal Indonesia ===
                $hari = [
                    'Sunday'    => 'Minggu',
                    'Monday'    => 'Senin',
                    'Tuesday'   => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday'  => 'Kamis',
                    'Friday'    => 'Jumat',
                    'Saturday'  => 'Sabtu'
                ];

                $bulan = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];

                $now  = date('Y-m-d H:i:s');
                $hariIndo = $hari[date('l', strtotime($now))];
                $tgl      = date('d', strtotime($now));
                $bln      = $bulan[intval(date('m', strtotime($now)))];
                $thn      = date('Y', strtotime($now));
                $jam      = date('H:i', strtotime($now)) . " WIB";

                $tanggalLengkap = "{$hariIndo}, {$tgl} {$bln} {$thn}, {$jam}";

                // === Format Pesan WA Final ===
                $pesan =
                    "*== SINDEN System ==*\n"
                    . "*📷 Update Foto Rumah Berhasil*\n\n"
                    . "Nama: *{$namaKK}*\n"
                    . "No. KK: *{$noKK}*\n"
                    . "Alamat: {$alamatKK}\n"
                    . "Waktu: {$tanggalLengkap}\n\n"
                    . "✔ Semua foto + Geotag berhasil dikirim oleh petugas.";

                // === Kirim WA ke setiap admin (role_id = 3) ===
                $wa = new \App\Libraries\WaService();

                foreach ($admins as $admin) {

                    if (empty($admin['nope'])) {
                        log_message('warning', "[WA Foto] Admin {$admin['fullname']} tidak memiliki nomor WhatsApp");
                        continue;
                    }

                    // Normalisasi nomor WA
                    $nomorWA = preg_replace('/[^0-9]/', '', $admin['nope']);
                    if (str_starts_with($nomorWA, '0')) {
                        $nomorWA = '62' . substr($nomorWA, 1);
                    }

                    try {
                        $send = $wa->sendText($nomorWA, $pesan);
                        log_message('info', "[WA Foto] Pesan dikirim ke admin {$admin['fullname']} ({$nomorWA}) | " . json_encode($send));
                    } catch (\Throwable $e) {
                        log_message('error', "[WA Foto] ERROR kirim WA ke {$nomorWA}: " . $e->getMessage());
                    }
                }
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data foto & geotag berhasil disimpan!',
                'payload' => $payloadLama
            ]);
        } catch (\Throwable $e) {
            log_message('error', '❌ saveFoto() error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    // /**
    //  * ♻️ Simpan Data Seluruh ke Database Utama (dtsen_kk, dtsen_art, dtsen_se)
    //  * - fitur baru, kirim pesan ke petugas entri (users.nope) sesuai data hasil pekerjaannya
    //  */
    // public function apply()
    // {
    //     $this->db->transBegin();
    //     try {
    //         $usulan_id = $this->request->getPost('usulan_id');
    //         $userId    = session()->get('id') ?? 'system';

    //         // 🔍 Ambil data usulan utama (simpan status lama untuk pengecekan)
    //         $usulan = $this->db->table('dtsen_usulan')
    //             ->where('id', $usulan_id)
    //             ->get()
    //             ->getRowArray();

    //         if (!$usulan) {
    //             throw new \Exception('Data usulan tidak ditemukan.');
    //         }

    //         $statusSebelumnya = $usulan['status'] ?? null;

    //         $payload = json_decode($usulan['payload'] ?? '{}', true);
    //         if (json_last_error() !== JSON_ERROR_NONE) {
    //             throw new \Exception('Payload tidak valid: ' . json_last_error_msg());
    //         }

    //         $idKk = $usulan['dtsen_kk_id'] ?? null;
    //         if (!$idKk) {
    //             throw new \Exception('ID KK tidak ditemukan dalam usulan.');
    //         }

    //         // =======================================================
    //         // 🏠 1️⃣ Update Tabel dtsen_rt (Logika Pecah Alamat)
    //         // =======================================================
    //         $geo      = $payload['geo'] ?? [];
    //         $foto     = $payload['foto'] ?? [];
    //         $rumah    = $payload['perumahan'] ?? [];
    //         $kondisi  = $rumah['kondisi'] ?? [];
    //         $sanitasi = $rumah['sanitasi'] ?? [];
    //         $wilayah  = $rumah['wilayah'] ?? []; // 🚀 Ambil node wilayah

    //         // Ambil id_rt yang dipakai KK saat ini
    //         $idRtSekarang = $this->db->table('dtsen_kk')->select('id_rt')->where('id_kk', $idKk)->get()->getRow('id_rt');

    //         if ($idRtSekarang) {
    //             // Ambil data asli sebelum ditimpa
    //             $rtLama = $this->db->table('dtsen_rt')->where('id_rt', $idRtSekarang)->get()->getRowArray();

    //             // Tentukan nilai RT, RW, dan KODE DESA yang baru
    //             $rtBaru   = (isset($rumah['rt']) && $rumah['rt'] !== '') ? $rumah['rt'] : $rtLama['rt'];
    //             $rwBaru   = (isset($rumah['rw']) && $rumah['rw'] !== '') ? $rumah['rw'] : $rtLama['rw'];
    //             $desaBaru = (!empty($wilayah['desa'])) ? $wilayah['desa'] : ($rtLama['kode_desa'] ?? null); // 🚀 BUG FIX KODE DESA

    //             // Deteksi apakah terjadi perpindahan wilayah
    //             $isPindahWilayah = ($rtBaru !== $rtLama['rt'] || $rwBaru !== $rtLama['rw'] || ($desaBaru !== '' && $desaBaru !== $rtLama['kode_desa']));

    //             $rtUpdate = [
    //                 'kode_desa'         => $desaBaru, // 🚀 Pastikan desa terupdate!
    //                 'rt'                => $rtBaru,
    //                 'rw'                => $rwBaru,
    //                 'alamat'            => !empty($rumah['alamat']) ? $rumah['alamat'] : $rtLama['alamat'],
    //                 'kepemilikan_rumah' => !empty($rumah['status_kepemilikan']) ? $rumah['status_kepemilikan'] : $rtLama['kepemilikan_rumah'],
    //                 'luas_lantai'       => (isset($kondisi['luas_lantai']) && $kondisi['luas_lantai'] !== '') ? $kondisi['luas_lantai'] : $rtLama['luas_lantai'],
    //                 'jenis_lantai'      => !empty($kondisi['jenis_lantai']) ? $kondisi['jenis_lantai'] : $rtLama['jenis_lantai'],
    //                 'jenis_dinding'     => !empty($kondisi['jenis_dinding']) ? $kondisi['jenis_dinding'] : $rtLama['kondisi_dinding'],
    //                 'bahan_bakar'       => !empty($kondisi['bahan_bakar']) ? $kondisi['bahan_bakar'] : $rtLama['bahan_bakar'],
    //                 'sumber_air'        => !empty($kondisi['sumber_air']) ? $kondisi['sumber_air'] : $rtLama['sumber_air'],
    //                 'sumber_listrik'    => !empty($kondisi['sumber_listrik']) ? $kondisi['sumber_listrik'] : $rtLama['sumber_listrik'],
    //                 'sanitasi'          => !empty($sanitasi['pembuangan_tinja']) ? $sanitasi['pembuangan_tinja'] : $rtLama['sanitasi'],
    //                 'foto_rumah'        => !empty($foto['depan']) ? $foto['depan'] : $rtLama['foto_rumah'],
    //                 'foto_rumah_dalam'  => !empty($foto['dalam']) ? $foto['dalam'] : $rtLama['foto_rumah_dalam'],
    //                 'latitude'          => !empty($geo['lat']) ? $geo['lat'] : $rtLama['latitude'],
    //                 'longitude'         => !empty($geo['lng']) ? $geo['lng'] : $rtLama['longitude'],
    //                 'updated_at'        => date('Y-m-d H:i:s'),
    //                 'updated_by'        => $userId
    //             ];

    //             if ($isPindahWilayah) {
    //                 $jumlahPenghuni = $this->db->table('dtsen_kk')->where('id_rt', $idRtSekarang)->countAllResults();

    //                 if ($jumlahPenghuni > 1) {
    //                     // 🚨 PECAH ALAMAT: Buat baris id_rt baru
    //                     $rtUpdate['created_at']  = date('Y-m-d H:i:s');
    //                     $rtUpdate['created_by']  = $userId;
    //                     $rtUpdate['source_name'] = 'pecah_alamat_usulan_' . $usulan_id;

    //                     $this->db->table('dtsen_rt')->insert($rtUpdate);
    //                     $idRtSekarang = $this->db->insertID();
    //                 } else {
    //                     // Update baris lama
    //                     $this->db->table('dtsen_rt')->where('id_rt', $idRtSekarang)->update($rtUpdate);
    //                 }
    //             } else {
    //                 // Update normal
    //                 $this->db->table('dtsen_rt')->where('id_rt', $idRtSekarang)->update($rtUpdate);
    //             }
    //         }

    //         // =======================================================
    //         // 👪 2️⃣ Update Tabel dtsen_kk (DATA KELUARGA)
    //         // =======================================================
    //         $kkLama = $this->db->table('dtsen_kk')->where('id_kk', $idKk)->get()->getRowArray();

    //         $kkUpdate = [
    //             'id_rt'                    => $idRtSekarang,
    //             'is_recovery_needed'       => 0,
    //             'no_kk'                    => !empty($rumah['no_kk']) ? $rumah['no_kk'] : $kkLama['no_kk'],
    //             'kepala_keluarga'          => !empty($rumah['kepala_keluarga']) ? $rumah['kepala_keluarga'] : $kkLama['kepala_keluarga'],
    //             'alamat'                   => !empty($rumah['alamat']) ? $rumah['alamat'] : $kkLama['alamat'],
    //             'status_kepemilikan_rumah' => !empty($rumah['status_kepemilikan']) ? $rumah['status_kepemilikan'] : $kkLama['status_kepemilikan_rumah'],
    //             'kategori_adat'            => !empty($rumah['kategori_adat']) ? $rumah['kategori_adat'] : $kkLama['kategori_adat'],
    //             'nama_suku'                => !empty($rumah['nama_suku']) ? $rumah['nama_suku'] : $kkLama['nama_suku'],
    //             'foto_kk'                  => !empty($foto['ktp_kk']) ? $foto['ktp_kk'] : (!empty($foto['ktp']) ? $foto['ktp'] : $kkLama['foto_kk']),
    //             'foto_rumah'               => !empty($foto['depan']) ? $foto['depan'] : $kkLama['foto_rumah'],
    //             'foto_rumah_dalam'         => !empty($foto['dalam']) ? $foto['dalam'] : $kkLama['foto_rumah_dalam'],
    //             'updated_at'               => date('Y-m-d H:i:s'),
    //             'updated_by'               => $userId
    //         ];
    //         $this->db->table('dtsen_kk')->where('id_kk', $idKk)->update($kkUpdate);

    //         // =======================================================
    //         // 👤 3️⃣ Sinkronisasi dtsen_art (Memakai SOFT DELETE)
    //         // =======================================================
    //         $anggotaUsulan = $this->db->table('dtsen_usulan_art')
    //             ->where('dtsen_usulan_id', $usulan_id)
    //             ->get()
    //             ->getResultArray();

    //         if (!empty($anggotaUsulan)) {
    //             // 🚀 BUG FIX: Gunakan Soft Delete agar riwayat tidak musnah
    //             $this->db->table('dtsen_art')->where('id_kk', $idKk)->where('deleted_at', null)->update([
    //                 'deleted_at'    => date('Y-m-d H:i:s'),
    //                 'delete_reason' => 'Ditimpa oleh usulan pembaruan ID ' . $usulan_id
    //             ]);

    //             foreach ($anggotaUsulan as $art) {
    //                 $payloadMember = json_decode($art['payload_member'] ?? '{}', true);
    //                 $identitas     = $payloadMember['identitas'] ?? [];

    //                 $dataArt = [
    //                     'id_kk'               => $idKk,
    //                     'nik'                 => $identitas['nik'] ?? $art['nik'] ?? null,
    //                     'nama'                => $identitas['nama'] ?? $art['nama'] ?? null,
    //                     'hubungan_keluarga'   => $identitas['hubungan'] ?? null,
    //                     'jenis_kelamin'       => $identitas['jenis_kelamin'] ?? null,
    //                     'tanggal_lahir'       => $identitas['tanggal_lahir'] ?? null,
    //                     'tempat_lahir'        => $identitas['tempat_lahir'] ?? null,
    //                     'pendidikan_terakhir' => $identitas['pendidikan'] ?? null,
    //                     'pekerjaan'           => $identitas['pekerjaan'] ?? null,
    //                     'status_kawin'        => $identitas['status_kawin'] ?? null,
    //                     'foto_identitas'      => $payloadMember['foto'] ?? null,
    //                     'source_name'         => 'apply_usulan_' . $usulan_id,
    //                     'created_by'          => $userId,
    //                     'created_at'          => date('Y-m-d H:i:s')
    //                 ];

    //                 $this->db->table('dtsen_art')->insert($dataArt);
    //             }
    //         }

    //         // =======================================================
    //         // 💰 4️⃣ Upsert Sosial Ekonomi dtsen_se
    //         // =======================================================
    //         $aset  = $payload['aset'] ?? [];
    //         $geo   = $payload['geo'] ?? [];

    //         $kepemilikan_aset     = json_encode($aset, JSON_UNESCAPED_UNICODE);
    //         $kepemilikan_bantuan  = json_encode($payload['bantuan'] ?? [], JSON_UNESCAPED_UNICODE);

    //         $existingSE = $this->db->table('dtsen_se')
    //             ->where('id_kk', $idKk)
    //             ->get()
    //             ->getRowArray();

    //         if ($existingSE) {
    //             $this->db->table('dtsen_se')
    //                 ->where('id_kk', $idKk)
    //                 ->update([
    //                     'kepemilikan_aset'         => $kepemilikan_aset,
    //                     'kepemilikan_bantuan'      => $kepemilikan_bantuan,
    //                     'rata_penghasilan_bulanan' => $payload['penghasilan'] ?? null,
    //                     'rata_pengeluaran_bulanan' => $payload['pengeluaran'] ?? null,
    //                     'latitude'                 => $geo['lat'] ?? null,
    //                     'longitude'                => $geo['lng'] ?? null,
    //                     'updated_at'               => date('Y-m-d H:i:s'),
    //                     'updated_by'               => $userId
    //                 ]);
    //         } else {
    //             $this->db->table('dtsen_se')->insert([
    //                 'id_rt'                    => $idRtSekarang,
    //                 'id_kk'                    => $idKk,
    //                 'kepemilikan_aset'         => $kepemilikan_aset,
    //                 'kepemilikan_bantuan'      => $kepemilikan_bantuan,
    //                 'rata_penghasilan_bulanan' => $payload['penghasilan'] ?? null,
    //                 'rata_pengeluaran_bulanan' => $payload['pengeluaran'] ?? null,
    //                 'latitude'                 => $geo['lat'] ?? null,
    //                 'longitude'                => $geo['lng'] ?? null,
    //                 'source_name'              => 'apply_usulan_' . $usulan_id,
    //                 'created_by'               => $userId,
    //                 'created_at'               => date('Y-m-d H:i:s')
    //             ]);
    //         }

    //         // =======================================================
    //         // 🟦 UPDATE STATUS USULAN
    //         // =======================================================
    //         $this->db->table('dtsen_usulan')
    //             ->where('id', $usulan_id)
    //             ->update([
    //                 'status'       => 'diverifikasi',
    //                 'verified_at'  => date('Y-m-d H:i:s'),
    //                 'verified_by'  => $userId
    //             ]);

    //         // =======================================================
    //         // 🟩 WA INTEGRATION — Generate Reminder Log
    //         // =======================================================
    //         $waConfig = $this->db->table('dtsen_wa_config')
    //             ->where('user_id', $userId)
    //             ->get()
    //             ->getRowArray();

    //         $interval = $waConfig['reminder_default_months'] ?? 3;
    //         $dueDate  = date('Y-m-d H:i:s', strtotime("+$interval months"));

    //         // Insert reminder log
    //         $this->db->table('dtsen_kk_reminder_log')->insert([
    //             'kk_id'    => $idKk,
    //             'admin_id' => $userId,
    //             'due_date' => $dueDate,
    //             'status'   => 'pending'
    //         ]);

    //         // =======================================================
    //         // 🚀 SINKRONISASI PDTT 2025 (Set Selesai saat Apply)
    //         // =======================================================
    //         // Kita update berdasarkan No KK yang sedang di-apply
    //         // $this->db->table('dtsen_pdtt_2025')
    //         // ->where('no_kk', $kkUpdate['no_kk'])
    //         // ->update([
    //         //     'status_verifikasi' => 'Selesai',
    //         //     'verified_at'       => date('Y-m-d H:i:s'),
    //         //     'verified_by'       => $userId
    //         // ]);

    //         // =======================================================
    //         // Commit transaction dulu — setelah commit, kirim WhatsApp
    //         // =======================================================
    //         $this->db->transCommit();
    //         log_message('info', "✅ Usulan ID {$usulan_id} diterapkan oleh {$userId}. Reminder dibuat.");

    //         // =======================================================
    //         // 🔔 Kirim WhatsApp ke Petugas Entri (dtks_users.nope)
    //         // =======================================================
    //         try {
    //             if (($statusSebelumnya ?? '') !== 'diverifikasi') {

    //                 $creatorNik = $usulan['created_by'] ?? null;

    //                 if (!empty($creatorNik)) {

    //                     $petugas = $this->db->table('dtks_users')
    //                         ->select('id, fullname, nope, nik')
    //                         ->where('nik', $creatorNik)
    //                         ->get()
    //                         ->getRowArray();

    //                     if (!$petugas) {
    //                         $petugas = $this->db->table('dtks_users')
    //                             ->select('id, fullname, nope, nik')
    //                             ->where('id', $creatorNik)
    //                             ->get()
    //                             ->getRowArray();
    //                     }

    //                     if ($petugas && !empty($petugas['nope'])) {

    //                         $nomorWA = preg_replace('/[^0-9]/', '', $petugas['nope']);

    //                         if (str_starts_with($nomorWA, '0')) {
    //                             $nomorWA = '62' . substr($nomorWA, 1);
    //                         }

    //                         if (str_starts_with($nomorWA, '620')) {
    //                             $nomorWA = '62' . substr($nomorWA, 3);
    //                         }

    //                         $kkInfo = $this->db->table('dtsen_kk')
    //                             ->select('no_kk, kepala_keluarga, alamat, id_rt')
    //                             ->where('id_kk', $idKk)
    //                             ->get()
    //                             ->getRowArray();

    //                         $rtText = '-';
    //                         $rwText = '-';
    //                         if (!empty($kkInfo['id_rt'])) {
    //                             $rtRow = $this->db->table('dtsen_rt')
    //                                 ->select('rt,rw')
    //                                 ->where('id_rt', $kkInfo['id_rt'])
    //                                 ->get()
    //                                 ->getRowArray();

    //                             if ($rtRow) {
    //                                 $rtText = $rtRow['rt'] ?? '-';
    //                                 $rwText = $rtRow['rw'] ?? '-';
    //                             }
    //                         }

    //                         $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    //                         $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

    //                         $now = date('Y-m-d H:i:s');
    //                         $hariIndo = $hari[date('l', strtotime($now))];
    //                         $tgl = date('d', strtotime($now));
    //                         $bln = $bulan[intval(date('m', strtotime($now)))];
    //                         $thn = date('Y', strtotime($now));
    //                         $jam = date('H:i', strtotime($now)) . " WIB";

    //                         $tanggalLengkap = "{$hariIndo}, {$tgl} {$bln} {$thn}, {$jam}";

    //                         $msg  = "*== SINDEN System ==*\n";
    //                         $msg .= "📌 *Pemberitahuan Validasi Groundcheck*\n";
    //                         $msg .= "Usulan No. {$usulan_id} telah selesai divalidasi.\n\n";
    //                         $msg .= "👤 Kepala Keluarga: *" . ($kkInfo['kepala_keluarga'] ?? '-') . "*\n";
    //                         $msg .= "🏠 No. KK: *" . ($kkInfo['no_kk'] ?? '-') . "*\n";
    //                         $msg .= "📍 Alamat: " . ($kkInfo['alamat'] ?? '-') . " RT {$rtText} RW {$rwText}\n";
    //                         $msg .= "🗓 Waktu: {$tanggalLengkap}\n\n";
    //                         $msg .= "Terima kasih atas kerja baiknya.";

    //                         $waService = new \App\Libraries\WaService();
    //                         $send = $waService->sendText($nomorWA, $msg);

    //                         if (!is_array($send) || empty($send['status']) || $send['status'] != 'success') {
    //                             log_message('error', '[WA APPLY] Provider WA error: ' . json_encode($send));
    //                         }
    //                     }
    //                 }
    //             }
    //         } catch (\Throwable $e) {
    //             log_message('error', "[WA APPLY OUTER] {$e->getMessage()}");
    //         }

    //         return $this->response->setJSON([
    //             'status'   => 'success',
    //             'message'  => 'Data usulan berhasil diterapkan ke database utama.',
    //             'redirect' => base_url('dtsen-se')
    //         ]);
    //     } catch (\Throwable $e) {
    //         $this->db->transRollback();
    //         log_message('error', '[apply] ' . $e->getMessage());

    //         return $this->response->setJSON([
    //             'status'  => 'error',
    //             'message' => 'Gagal menerapkan data: ' . $e->getMessage()
    //         ]);
    //     }
    // }
    /**
     * ♻️ Simpan Data Seluruh ke Database Utama (dtsen_kk, dtsen_art, dtsen_se)
     * - fitur baru, kirim pesan ke petugas entri (users.nope) sesuai data hasil pekerjaannya
     */
    public function apply()
    {
        $this->db->transBegin();
        try {
            $usulan_id = $this->request->getPost('usulan_id');
            $userId    = session()->get('id') ?? 'system';

            // 🔍 Ambil data usulan utama (simpan status lama untuk pengecekan)
            $usulan = $this->db->table('dtsen_usulan')
                ->where('id', $usulan_id)
                ->get()
                ->getRowArray();

            if (!$usulan) {
                throw new \Exception('Data usulan tidak ditemukan.');
            }

            $statusSebelumnya = $usulan['status'] ?? null;

            $payload = json_decode($usulan['payload'] ?? '{}', true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Payload tidak valid: ' . json_last_error_msg());
            }

            $idKk = $usulan['dtsen_kk_id'] ?? null;
            if (!$idKk) {
                throw new \Exception('ID KK tidak ditemukan dalam usulan.');
            }

            // =======================================================
            // 🏠 1️⃣ Update Tabel dtsen_rt (Logika Pecah Alamat)
            // =======================================================
            $geo      = $payload['geo'] ?? [];
            $foto     = $payload['foto'] ?? [];
            $rumah    = $payload['perumahan'] ?? [];
            $kondisi  = $rumah['kondisi'] ?? [];
            $sanitasi = $rumah['sanitasi'] ?? [];
            $wilayah  = $rumah['wilayah'] ?? [];

            $idRtSekarang = $this->db->table('dtsen_kk')->select('id_rt')->where('id_kk', $idKk)->get()->getRow('id_rt');

            if ($idRtSekarang) {
                $rtLama = $this->db->table('dtsen_rt')->where('id_rt', $idRtSekarang)->get()->getRowArray();

                $rtBaru   = (isset($rumah['rt']) && $rumah['rt'] !== '') ? $rumah['rt'] : $rtLama['rt'];
                $rwBaru   = (isset($rumah['rw']) && $rumah['rw'] !== '') ? $rumah['rw'] : $rtLama['rw'];
                $desaBaru = (!empty($wilayah['desa'])) ? $wilayah['desa'] : ($rtLama['kode_desa'] ?? null);

                $isPindahWilayah = ($rtBaru !== $rtLama['rt'] || $rwBaru !== $rtLama['rw'] || ($desaBaru !== '' && $desaBaru !== $rtLama['kode_desa']));

                $rtUpdate = [
                    'kode_desa'         => $desaBaru,
                    'rt'                => $rtBaru,
                    'rw'                => $rwBaru,
                    'alamat'            => !empty($rumah['alamat']) ? $rumah['alamat'] : $rtLama['alamat'],
                    'kepemilikan_rumah' => !empty($rumah['status_kepemilikan']) ? $rumah['status_kepemilikan'] : $rtLama['kepemilikan_rumah'],
                    'luas_lantai'       => (isset($kondisi['luas_lantai']) && $kondisi['luas_lantai'] !== '') ? $kondisi['luas_lantai'] : $rtLama['luas_lantai'],
                    'jenis_lantai'      => !empty($kondisi['jenis_lantai']) ? $kondisi['jenis_lantai'] : $rtLama['jenis_lantai'],
                    'jenis_dinding'     => !empty($kondisi['jenis_dinding']) ? $kondisi['jenis_dinding'] : $rtLama['kondisi_dinding'],
                    'bahan_bakar'       => !empty($kondisi['bahan_bakar']) ? $kondisi['bahan_bakar'] : $rtLama['bahan_bakar'],
                    'sumber_air'        => !empty($kondisi['sumber_air']) ? $kondisi['sumber_air'] : $rtLama['sumber_air'],
                    'sumber_listrik'    => !empty($kondisi['sumber_listrik']) ? $kondisi['sumber_listrik'] : $rtLama['sumber_listrik'],
                    'sanitasi'          => !empty($sanitasi['pembuangan_tinja']) ? $sanitasi['pembuangan_tinja'] : $rtLama['sanitasi'],
                    'foto_rumah'        => !empty($foto['depan']) ? $foto['depan'] : $rtLama['foto_rumah'],
                    'foto_rumah_dalam'  => !empty($foto['dalam']) ? $foto['dalam'] : $rtLama['foto_rumah_dalam'],
                    'latitude'          => !empty($geo['lat']) ? $geo['lat'] : $rtLama['latitude'],
                    'longitude'         => !empty($geo['lng']) ? $geo['lng'] : $rtLama['longitude'],
                    'updated_at'        => date('Y-m-d H:i:s'),
                    'updated_by'        => $userId
                ];

                if ($isPindahWilayah) {
                    $jumlahPenghuni = $this->db->table('dtsen_kk')->where('id_rt', $idRtSekarang)->countAllResults();

                    if ($jumlahPenghuni > 1) {
                        $rtUpdate['created_at']  = date('Y-m-d H:i:s');
                        $rtUpdate['created_by']  = $userId;
                        $rtUpdate['source_name'] = 'pecah_alamat_usulan_' . $usulan_id;

                        $this->db->table('dtsen_rt')->insert($rtUpdate);
                        $idRtSekarang = $this->db->insertID();
                    } else {
                        $this->db->table('dtsen_rt')->where('id_rt', $idRtSekarang)->update($rtUpdate);
                    }
                } else {
                    $this->db->table('dtsen_rt')->where('id_rt', $idRtSekarang)->update($rtUpdate);
                }
            }

            // =======================================================
            // 👪 2️⃣ Update Tabel dtsen_kk (DATA KELUARGA)
            // =======================================================
            $kkLama = $this->db->table('dtsen_kk')->where('id_kk', $idKk)->get()->getRowArray();

            $kkUpdate = [
                'id_rt'                    => $idRtSekarang,
                'is_recovery_needed'       => 0,
                'no_kk'                    => !empty($rumah['no_kk']) ? $rumah['no_kk'] : $kkLama['no_kk'],
                'kepala_keluarga'          => !empty($rumah['kepala_keluarga']) ? $rumah['kepala_keluarga'] : $kkLama['kepala_keluarga'],
                'alamat'                   => !empty($rumah['alamat']) ? $rumah['alamat'] : $kkLama['alamat'],
                'status_kepemilikan_rumah' => !empty($rumah['status_kepemilikan']) ? $rumah['status_kepemilikan'] : $kkLama['status_kepemilikan_rumah'],
                'kategori_adat'            => !empty($rumah['kategori_adat']) ? $rumah['kategori_adat'] : $kkLama['kategori_adat'],
                'nama_suku'                => !empty($rumah['nama_suku']) ? $rumah['nama_suku'] : $kkLama['nama_suku'],
                'foto_kk'                  => !empty($foto['ktp_kk']) ? $foto['ktp_kk'] : (!empty($foto['ktp']) ? $foto['ktp'] : $kkLama['foto_kk']),
                'foto_rumah'               => !empty($foto['depan']) ? $foto['depan'] : $kkLama['foto_rumah'],
                'foto_rumah_dalam'         => !empty($foto['dalam']) ? $foto['dalam'] : $kkLama['foto_rumah_dalam'],
                'updated_at'               => date('Y-m-d H:i:s'),
                'updated_by'               => $userId
            ];
            $this->db->table('dtsen_kk')->where('id_kk', $idKk)->update($kkUpdate);

            // =======================================================
            // 👤 3️⃣ Sinkronisasi dtsen_art (Memakai SOFT DELETE)
            // =======================================================
            $anggotaUsulan = $this->db->table('dtsen_usulan_art')
                ->where('dtsen_usulan_id', $usulan_id)
                ->get()
                ->getResultArray();

            if (!empty($anggotaUsulan)) {
                $this->db->table('dtsen_art')->where('id_kk', $idKk)->where('deleted_at', null)->update([
                    'deleted_at'    => date('Y-m-d H:i:s'),
                    'delete_reason' => 'Ditimpa oleh usulan pembaruan ID ' . $usulan_id
                ]);

                foreach ($anggotaUsulan as $art) {
                    $payloadMember = json_decode($art['payload_member'] ?? '{}', true);
                    $identitas     = $payloadMember['identitas'] ?? [];

                    $dataArt = [
                        'id_kk'               => $idKk,
                        'nik'                 => $identitas['nik'] ?? $art['nik'] ?? null,
                        'nama'                => $identitas['nama'] ?? $art['nama'] ?? null,
                        'hubungan_keluarga'   => $identitas['hubungan'] ?? null,
                        'jenis_kelamin'       => $identitas['jenis_kelamin'] ?? null,
                        'tanggal_lahir'       => $identitas['tanggal_lahir'] ?? null,
                        'tempat_lahir'        => $identitas['tempat_lahir'] ?? null,
                        'pendidikan_terakhir' => $identitas['pendidikan'] ?? null,
                        'pekerjaan'           => $identitas['pekerjaan'] ?? null,
                        'status_kawin'        => $identitas['status_kawin'] ?? null,
                        'foto_identitas'      => $payloadMember['foto'] ?? null,
                        'source_name'         => 'apply_usulan_' . $usulan_id,
                        'created_by'          => $userId,
                        'created_at'          => date('Y-m-d H:i:s')
                    ];

                    $this->db->table('dtsen_art')->insert($dataArt);
                }
            }

            // =======================================================
            // 💰 4️⃣ Upsert Sosial Ekonomi dtsen_se
            // =======================================================
            $aset  = $payload['aset'] ?? [];
            $geo   = $payload['geo'] ?? [];

            $kepemilikan_aset     = json_encode($aset, JSON_UNESCAPED_UNICODE);
            $kepemilikan_bantuan  = json_encode($payload['bantuan'] ?? [], JSON_UNESCAPED_UNICODE);

            $existingSE = $this->db->table('dtsen_se')->where('id_kk', $idKk)->get()->getRowArray();

            if ($existingSE) {
                $this->db->table('dtsen_se')
                    ->where('id_kk', $idKk)
                    ->update([
                        'kepemilikan_aset'         => $kepemilikan_aset,
                        'kepemilikan_bantuan'      => $kepemilikan_bantuan,
                        'rata_penghasilan_bulanan' => $payload['penghasilan'] ?? null,
                        'rata_pengeluaran_bulanan' => $payload['pengeluaran'] ?? null,
                        'latitude'                 => $geo['lat'] ?? null,
                        'longitude'                => $geo['lng'] ?? null,
                        'updated_at'               => date('Y-m-d H:i:s'),
                        'updated_by'               => $userId
                    ]);
            } else {
                $this->db->table('dtsen_se')->insert([
                    'id_rt'                    => $idRtSekarang,
                    'id_kk'                    => $idKk,
                    'kepemilikan_aset'         => $kepemilikan_aset,
                    'kepemilikan_bantuan'      => $kepemilikan_bantuan,
                    'rata_penghasilan_bulanan' => $payload['penghasilan'] ?? null,
                    'rata_pengeluaran_bulanan' => $payload['pengeluaran'] ?? null,
                    'latitude'                 => $geo['lat'] ?? null,
                    'longitude'                => $geo['lng'] ?? null,
                    'source_name'              => 'apply_usulan_' . $usulan_id,
                    'created_by'               => $userId,
                    'created_at'               => date('Y-m-d H:i:s')
                ]);
            }

            // =======================================================
            // 🟦 5️⃣ UPDATE STATUS USULAN
            // =======================================================
            $this->db->table('dtsen_usulan')
                ->where('id', $usulan_id)
                ->update([
                    'status'       => 'diverifikasi',
                    'verified_at'  => date('Y-m-d H:i:s'),
                    'verified_by'  => $userId
                ]);

            // =======================================================
            // 🟪 6️⃣ AUTO-ROLLBACK KE MENU PENENTUAN KEMISKINAN
            // =======================================================
            // Mengecek apakah KK ini sebelumnya pernah ada di data Penentuan
            $cekPenentuan = $this->db->table('dtsen_penentuan_kemiskinan')
                ->where('dtsen_kk_id', $idKk)
                ->get()
                ->getRowArray();

            if ($cekPenentuan) {
                // Update status menjadi rollback, jangan insert baru (hindari duplikat)
                $this->db->table('dtsen_penentuan_kemiskinan')
                    ->where('id', $cekPenentuan['id'])
                    ->update([
                        'status_verifikasi' => 'rollback',
                        'catatan'           => 'Auto-Rollback by System: Data Groundcheck terbaru diterapkan, mohon evaluasi ulang.',
                        'verified_by'       => null,
                        'verified_at'       => null,
                        'updated_by'        => $userId,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ]);

                // Wajib masuk Log Aktivitas agar jejak rekamnya tercatat
                $this->db->table('dtsen_penentuan_kemiskinan_log')->insert([
                    'penentuan_id'      => $cekPenentuan['id'],
                    'aksi'              => 'rollback',
                    'status_kemiskinan' => $cekPenentuan['status_kemiskinan'],
                    'catatan'           => 'Auto-Rollback by System dari Apply Usulan ' . $usulan_id,
                    'user_id'           => $userId,
                    'created_at'        => date('Y-m-d H:i:s')
                ]);
            }

            // =======================================================
            // 🟩 7️⃣ WA INTEGRATION — Generate Reminder Log
            // =======================================================
            $waConfig = $this->db->table('dtsen_wa_config')
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $interval = $waConfig['reminder_default_months'] ?? 3;
            $dueDate  = date('Y-m-d H:i:s', strtotime("+$interval months"));

            $this->db->table('dtsen_kk_reminder_log')->insert([
                'kk_id'    => $idKk,
                'admin_id' => $userId,
                'due_date' => $dueDate,
                'status'   => 'pending'
            ]);

            // =======================================================
            // Commit transaction dulu — setelah commit, kirim WhatsApp
            // =======================================================
            $this->db->transCommit();
            log_message('info', "✅ Usulan ID {$usulan_id} diterapkan oleh {$userId}. Reminder dibuat & dikembalikan ke Penentuan.");

            // =======================================================
            // 🔔 Kirim WhatsApp ke Petugas Entri (dtks_users.nope)
            // =======================================================
            try {
                if (($statusSebelumnya ?? '') !== 'diverifikasi') {
                    $creatorNik = $usulan['created_by'] ?? null;
                    if (!empty($creatorNik)) {
                        $petugas = $this->db->table('dtks_users')
                            ->select('id, fullname, nope, nik')
                            ->where('nik', $creatorNik)
                            ->get()
                            ->getRowArray();

                        if (!$petugas) {
                            $petugas = $this->db->table('dtks_users')
                                ->select('id, fullname, nope, nik')
                                ->where('id', $creatorNik)
                                ->get()
                                ->getRowArray();
                        }

                        if ($petugas && !empty($petugas['nope'])) {
                            $nomorWA = preg_replace('/[^0-9]/', '', $petugas['nope']);
                            if (str_starts_with($nomorWA, '0')) $nomorWA = '62' . substr($nomorWA, 1);
                            if (str_starts_with($nomorWA, '620')) $nomorWA = '62' . substr($nomorWA, 3);

                            $kkInfo = $this->db->table('dtsen_kk')
                                ->select('no_kk, kepala_keluarga, alamat, id_rt')
                                ->where('id_kk', $idKk)
                                ->get()
                                ->getRowArray();

                            $rtText = '-';
                            $rwText = '-';
                            if (!empty($kkInfo['id_rt'])) {
                                $rtRow = $this->db->table('dtsen_rt')->select('rt,rw')->where('id_rt', $kkInfo['id_rt'])->get()->getRowArray();
                                if ($rtRow) {
                                    $rtText = $rtRow['rt'] ?? '-';
                                    $rwText = $rtRow['rw'] ?? '-';
                                }
                            }

                            $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                            $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

                            $now = date('Y-m-d H:i:s');
                            $tanggalLengkap = $hari[date('l', strtotime($now))] . ", " . date('d', strtotime($now)) . " " . $bulan[intval(date('m', strtotime($now)))] . " " . date('Y', strtotime($now)) . ", " . date('H:i', strtotime($now)) . " WIB";

                            $msg  = "*== SINDEN System ==*\n";
                            $msg .= "📌 *Pemberitahuan Validasi Groundcheck*\n";
                            $msg .= "Usulan No. {$usulan_id} telah selesai diterapkan.\n\n";
                            $msg .= "👤 Kepala Keluarga: *" . ($kkInfo['kepala_keluarga'] ?? '-') . "*\n";
                            $msg .= "🏠 No. KK: *" . ($kkInfo['no_kk'] ?? '-') . "*\n";
                            $msg .= "📍 Alamat: " . ($kkInfo['alamat'] ?? '-') . " RT {$rtText} RW {$rwText}\n";
                            $msg .= "🗓 Waktu: {$tanggalLengkap}\n\n";
                            $msg .= "Terima kasih atas kerja baiknya.";

                            $waService = new \App\Libraries\WaService();
                            $send = $waService->sendText($nomorWA, $msg);

                            if (!is_array($send) || empty($send['status']) || $send['status'] != 'success') {
                                log_message('error', '[WA APPLY] Provider WA error: ' . json_encode($send));
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', "[WA APPLY OUTER] {$e->getMessage()}");
            }

            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => 'Data usulan berhasil diterapkan. Data telah dikembalikan ke tabel Penentuan Kemiskinan untuk dievaluasi ulang.',
                'redirect' => base_url('dtsen-se')
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', '[apply] ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menerapkan data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * =========================================================================
     * 🔙 FUNGSI MANUAL ROLLBACK: Tombol "Tolak / Kembalikan Data"
     * =========================================================================
     */
    public function rollback()
    {
        try {
            $usulan_id = $this->request->getPost('usulan_id');
            $catatan   = $this->request->getPost('catatan') ?? 'Ditolak oleh Admin SINDEN.';
            $userId    = session()->get('id') ?? 'system';

            $usulan = $this->db->table('dtsen_usulan')->select('dtsen_kk_id')->where('id', $usulan_id)->get()->getRowArray();

            if (!$usulan) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data usulan tidak ditemukan.']);
            }

            // 1. Kembalikan data ke getPenentuanKemiskinan
            $this->db->table('dtsen_penentuan_kemiskinan')
                ->where('dtsen_kk_id', $usulan['dtsen_kk_id'])
                ->update([
                    'status_verifikasi' => 'rollback',
                    'catatan'           => $catatan,
                    'verified_by'       => $userId,
                    'verified_at'       => date('Y-m-d H:i:s')
                ]);

            // 2. Ubah status usulan
            $this->db->table('dtsen_usulan')
                ->where('id', $usulan_id)
                ->update([
                    'status' => 'dikembalikan'
                ]);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data berhasil ditolak dan dikembalikan ke petugas lapangan.'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal melakukan rollback: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 📋 Ambil Detail Data Anggota Usulan
     * - Jika ID diberikan, ambil data anggota usulan dari dtsen_usulan_art
     * - Jika tidak ada ID, kembalikan struktur kosong untuk mode tambah
     */
    public function getAnggotaDetail($id = null)
    {
        try {
            $db = \Config\Database::connect();
            $genModel = new \App\Models\GenModel();

            // 🟢 0️⃣ Mode Tambah (tanpa ID)
            if (empty($id) || !is_numeric($id)) {
                return $this->response->setJSON([
                    'status'  => 'empty',
                    'message' => 'Belum ada data anggota (mode tambah).',
                    'data'    => [
                        'usulan_id'       => null,
                        'anggota_prefill' => [],
                        'dropdowns'       => [
                            'status_kawin' => $genModel->getDataStatusKawin(),
                            'hubungan'     => $genModel->getDataShdk(),
                            'pekerjaan'    => $genModel->getPendudukPekerjaan(),
                            'pendidikan'   => $genModel->getPendidikan(),
                        ]
                    ]
                ]);
            }

            $usulanArt = $db->table('dtsen_usulan_art')->where('id', $id)->get()->getRowArray();
            $usulan_id = null;
            $anggota_prefill = [];

            if ($usulanArt) {
                $payload = json_decode($usulanArt['payload_member'] ?? '{}', true);

                // 🧩 Normalisasi key agar sesuai form
                $anggota_prefill = array_merge(
                    [
                        'id'                => $usulanArt['id'],
                        'dtsen_usulan_id'   => $usulanArt['dtsen_usulan_id'],
                        'nik'               => $usulanArt['nik'],
                        'nama'              => $usulanArt['nama'],
                        'hubungan'          => $usulanArt['hubungan'],
                    ],
                    [
                        // Identitas
                        'individu_no_kk'    => $payload['identitas']['individu_no_kk'] ?? '',
                        'tempat_lahir'      => $payload['identitas']['tempat_lahir'] ?? '',
                        'tanggal_lahir'     => $payload['identitas']['tanggal_lahir'] ?? '',
                        'jenis_kelamin'     => $payload['identitas']['jenis_kelamin'] ?? '',
                        'status_kawin'      => $payload['identitas']['status_kawin'] ?? '',
                        'hubungan_keluarga' => $payload['identitas']['hubungan'] ?? '',
                        'pekerjaan'         => $payload['identitas']['pekerjaan'] ?? '',
                        'pendidikan_terakhir' => $payload['identitas']['pendidikan_terakhir'] ?? '',
                        'ibu_kandung'       => $payload['identitas']['ibu_kandung'] ?? '',
                        'provinsi'          => $payload['identitas']['provinsi'] ?? '',
                        'kabupaten'         => $payload['identitas']['kabupaten'] ?? '',
                        'kecamatan'         => $payload['identitas']['kecamatan'] ?? '',
                        'desa'              => $payload['identitas']['desa'] ?? '',
                        'status_keberadaan' => $payload['identitas']['status_keberadaan'] ?? 'Belum Ditentukan',

                        // Pendidikan
                        'partisipasi_sekolah' => $payload['pendidikan']['partisipasi_sekolah'] ?? '',
                        'jenjang_pendidikan'  => $payload['pendidikan']['jenjang_pendidikan'] ?? '',
                        'kelas_tertinggi'     => $payload['pendidikan']['kelas_tertinggi'] ?? '',
                        'ijazah_tertinggi'   => $payload['pendidikan']['ijazah_tertinggi'] ?? '',

                        // Tenaga kerja
                        'bekerja_seminggu' => $payload['tenaga_kerja']['bekerja_seminggu'] ?? '',
                        'lapangan_usaha'     => $payload['tenaga_kerja']['lapangan_usaha'] ?? '',
                        'status_pekerjaan'   => $payload['tenaga_kerja']['status_pekerjaan'] ?? '',
                        'pendapatan'         => $payload['tenaga_kerja']['pendapatan'] ?? '',
                        'keterampilan'       => $payload['tenaga_kerja']['keterampilan'] ?? [],

                        // Usaha
                        'memiliki_usaha'     => $payload['usaha']['memiliki_usaha'] ?? '',
                        'jumlah_usaha'       => $payload['usaha']['jumlah_usaha'] ?? '',
                        'pekerja_dibayar'    => $payload['usaha']['pekerja_dibayar'] ?? '',
                        'pekerja_tidak_dibayar' => $payload['usaha']['pekerja_tidak_dibayar'] ?? '',
                        'omzet_bulanan'      => $payload['usaha']['omzet_bulanan'] ?? '',

                        // Kesehatan
                        'status_hamil'       => $payload['kesehatan']['status_hamil'] ?? '',
                        'penyakit_kronis'    => $payload['kesehatan']['penyakit_kronis'] ?? '',
                        'disabilitas'        => $payload['kesehatan']['disabilitas'] ?? [],
                    ]
                );

                $usulan_id = $usulanArt['dtsen_usulan_id'];
            } else {
                // 🔁 fallback dtsen_art
                $art = $db->table('dtsen_art a')
                    ->select('a.*, kk.no_kk as individu_no_kk, kk.kepala_keluarga')
                    ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')
                    ->where('a.id_art', $id)
                    ->get()->getRowArray();

                if (!$art) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Data anggota tidak ditemukan.']);
                }
                $anggota_prefill = $art;
            }

            // 🔍 lookup label (status_kawin, hubungan, pekerjaan, pendidikan)
            $refStatusKawin = $genModel->getDataStatusKawin();
            $refShdk        = $genModel->getDataShdk();
            $refPekerjaan   = $genModel->getPendudukPekerjaan();
            $refPendidikan  = $genModel->getPendidikan();

            // Helper lookup tanpa collect()
            $lookup = function ($list, $idField, $labelField, $value) {
                if (!$value) return null;
                foreach ($list as $item) {
                    if ($item[$idField] == $value) return $item[$labelField];
                }
                return null;
            };

            $anggota_prefill['status_kawin_label']    = $lookup($refStatusKawin, 'id', 'nama', $anggota_prefill['status_kawin']);
            $anggota_prefill['hubungan_label']        = $lookup($refShdk, 'id', 'nama', $anggota_prefill['hubungan_keluarga']);
            $anggota_prefill['pekerjaan_label']       = $lookup($refPekerjaan, 'id', 'nama', $anggota_prefill['pekerjaan']);
            $anggota_prefill['pendidikan_label']      = $lookup($refPendidikan, 'id', 'nama', $anggota_prefill['pendidikan_terakhir']);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data individu berhasil dimuat.',
                'data'    => [
                    'usulan_id'       => $usulan_id,
                    'anggota_prefill' => $anggota_prefill,
                    'dropdowns'       => [
                        'status_kawin' => $refStatusKawin,
                        'hubungan'     => $refShdk,
                        'pekerjaan'    => $refPekerjaan,
                        'pendidikan'   => $refPendidikan,
                    ]
                ]
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[getAnggotaDetail] ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 💾 Simpan data anggota individu (tambah/edit) ke dtsen_usulan_art
     */
    public function saveAnggota()
    {
        $request = $this->request;
        $db = \Config\Database::connect();
        $session = session();

        try {
            $post = $request->getPost();
            $userId = $session->get('id_user') ?? $session->get('id') ?? 'system';

            // ✅ Validasi dasar
            if (empty($post['nik']) || empty($post['nama'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'NIK dan Nama wajib diisi.'
                ]);
            }

            // 🔍 Ambil usulan aktif berdasarkan id_kk (bukan sekadar status draft)
            $idKk = $post['id_kk'] ?? null;

            // ✅ Validasi dulu apakah id_kk valid di dtsen_kk
            $cekKK = $db->table('dtsen_kk')->where('id_kk', $idKk)->countAllResults();
            if ($cekKK == 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "ID KK ($idKk) tidak ditemukan di tabel dtsen_kk. Simpan data keluarga dulu sebelum tambah anggota."
                ]);
            }

            $usulan = $db->table('dtsen_usulan')
                ->where('dtsen_kk_id', $idKk)
                ->whereIn('status', ['draft', 'submitted'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if (!$usulan) {
                // 🆕 Buat usulan draft otomatis jika belum ada
                $draftData = [
                    'usulan_no'   => 'ART-' . date('ymdHis'),
                    'jenis'       => 'pembaruan',
                    'status'      => 'draft',
                    'dtsen_kk_id' => $idKk,
                    'created_by'  => $userId,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'summary'     => 'Usulan otomatis dibuat oleh sistem saat tambah anggota.'
                ];
                $db->table('dtsen_usulan')->insert($draftData);
                $usulan_id = $db->insertID();
            } else {
                $usulan_id = $usulan['id'];
            }

            $usulan_id = $usulan['id'];

            // 🔹 Siapkan payload individu (gabungan 5 tab)
            $payloadIndividu = [
                'identitas' => [
                    'status_keberadaan' => $post['status_keberadaan'] ?? null,
                    'individu_no_kk' => $post['individu_no_kk'] ?? null,
                    'nik' => $post['nik'] ?? null,
                    'nama' => $post['nama'] ?? null,
                    'tempat_lahir' => $post['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $post['tanggal_lahir'] ?? null,
                    'jenis_kelamin' => $post['jenis_kelamin'] ?? null,
                    'status_kawin' => $post['status_kawin'] ?? null,
                    'hubungan' => $post['hubungan'] ?? null,
                    'pekerjaan' => $post['pekerjaan'] ?? null,
                    'pendidikan_terakhir' => $post['pendidikan_terakhir'] ?? null,
                    'ibu_kandung' => $post['ibu_kandung'] ?? null,
                    'provinsi' => $post['provinsi'] ?? null,
                    'kabupaten' => $post['kabupaten'] ?? null,
                    'kecamatan' => $post['kecamatan'] ?? null,
                    'desa' => $post['desa'] ?? null,
                ],
                'pendidikan' => [
                    'partisipasi_sekolah' => $post['partisipasi_sekolah'] ?? null,
                    'jenjang_pendidikan' => $post['jenjang_pendidikan'] ?? null,
                    'kelas_tertinggi' => $post['kelas_tertinggi'] ?? null,
                    'ijazah_tertinggi' => $post['ijazah_tertinggi'] ?? null,
                ],
                'tenaga_kerja' => [
                    'bekerja_seminggu' => $post['bekerja_seminggu'] ?? null,
                    'lapangan_usaha' => $post['lapangan_usaha'] ?? null,
                    'status_pekerjaan' => $post['status_pekerjaan'] ?? null,
                    'pendapatan' => $post['pendapatan'] ?? null,
                    'keterampilan' => $post['keterampilan'] ?? [],
                ],
                'usaha' => [
                    'memiliki_usaha' => $post['memiliki_usaha'] ?? 'Tidak',
                    'jumlah_usaha' => $post['jumlah_usaha'] ?? null,
                    'pekerja_dibayar' => $post['pekerja_dibayar'] ?? null,
                    'pekerja_tidak_dibayar' => $post['pekerja_tidak_dibayar'] ?? null,
                    'omzet_bulanan' => $post['omzet_bulanan'] ?? null,
                ],
                'kesehatan' => [
                    'status_hamil' => $post['status_hamil'] ?? null,
                    'disabilitas' => $post['disabilitas'] ?? [],
                    'penyakit_kronis' => $post['penyakit_kronis'] ?? null,
                ],
            ];

            // 🔍 Cek apakah NIK sudah ada pada KK yang sama
            $existingArt = $db->table('dtsen_usulan_art ua')
                ->select('ua.id')
                ->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'left')
                ->where('ua.nik', $post['nik'])
                ->where('u.dtsen_kk_id', $idKk)
                ->get()
                ->getRowArray();

            // 🔹 Siapkan data utama
            $dataArt = [
                'dtsen_usulan_id' => $usulan_id,
                'nik' => $post['nik'],
                'nama' => $post['nama'],
                'hubungan' => $post['hubungan'] ?? null,
                'payload_member' => json_encode($payloadIndividu, JSON_UNESCAPED_UNICODE),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $userId
            ];

            // 🟦 1. Jika sudah ada → UPDATE
            if ($existingArt) {
                $db->table('dtsen_usulan_art')
                    ->where('id', $existingArt['id'])
                    ->update($dataArt);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data individu berhasil diperbarui.',
                    'mode' => 'update'
                ]);
            }

            // 🟩 2. Jika belum ada → INSERT baru
            $dataArt['created_at'] = date('Y-m-d H:i:s');
            $dataArt['created_by'] = $userId;

            $inserted = $db->table('dtsen_usulan_art')->insert($dataArt);

            if (!$inserted) {
                throw new \Exception('Gagal menyimpan ke tabel dtsen_usulan_art.');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data individu berhasil ditambahkan.',
                'mode' => 'insert'
            ]);

            // 🔹 Siapkan data utama untuk tabel dtsen_usulan_art
            $dataArt = [
                'dtsen_usulan_id' => $usulan_id,
                'nik' => $post['nik'],
                'nama' => $post['nama'],
                'hubungan' => $post['hubungan'] ?? null,
                'payload_member' => json_encode($payloadIndividu, JSON_UNESCAPED_UNICODE),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => $userId,
                'updated_by' => $userId
            ];

            // 🔸 Insert ke tabel usulan_art
            $inserted = $db->table('dtsen_usulan_art')->insert($dataArt);

            if (!$inserted) {
                throw new \Exception('Gagal menyimpan ke tabel dtsen_usulan_art.');
            }

            // Pastikan array untuk frontend (bukan string)
            if (!is_array($anggota_prefill['disabilitas'] ?? null)) {
                $anggota_prefill['disabilitas'] = [];
            }
            if (!is_array($anggota_prefill['keterampilan'] ?? null)) {
                $anggota_prefill['keterampilan'] = [];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data individu berhasil disimpan ke dtsen_usulan_art.',
                'payload_sample' => $payloadIndividu // hanya untuk debug
            ]);
        } catch (\Throwable $e) {
            log_message('error', '❌ saveAnggota() error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function lanjutkan($id_usulan)
    {
        try {
            // Ambil data usulan berdasarkan id_usulan
            $usulan = $this->db->table('dtsen_usulan')
                ->where('id', $id_usulan)
                ->get()
                ->getRow();

            if (!$usulan) {
                throw new \Exception("Usulan tidak ditemukan.");
            }

            // Ambil id_kk (bisa null jika keluarga baru)
            $id_kk = $usulan->dtsen_kk_id ?? null;

            // Jika id_kk tidak ada (keluarga baru)
            if (!$id_kk) {
                // tetap buka halaman detail tapi dengan konteks keluarga baru
                return redirect()->to('/pembaruan-keluarga/detail-baru/' . $id_usulan);
            }

            // Jika id_kk ada, arahkan ke detail yang sama seperti tombol pembaruan keluarga
            return redirect()->to('/pembaruan-keluarga/detail/' . $id_kk);
        } catch (\Throwable $e) {
            log_message('error', '❌ lanjutkan() error: ' . $e->getMessage());
            return redirect()->back()->with('message', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function tambah()
    {
        try {
            $session = session();

            $data = [
                'title'       => 'Tambah Pembaruan Keluarga Baru',
                'namaApp'     => nameApp(),
                'user'        => $session->get(),
                'kkData'      => [], // kosong karena belum ada keluarga
                'rtData'      => [],
                'perumahan'   => [
                    'no_kk'              => '',
                    'kepala_keluarga'    => '',
                    'alamat'             => '',
                    'rw'                 => '',
                    'rt'                 => '',
                    'status_kepemilikan' => '',
                    'kategori_adat'      => 'Tidak',
                    'nama_suku'          => '',
                    'wilayah_nama'       => [
                        'provinsi'  => '',
                        'kabupaten' => '',
                        'kecamatan' => '',
                        'desa'      => ''
                    ],
                ],
                'anggota'     => [],
                'payload'     => [],
                'usulan'      => [],
                'id_kk'       => null,
                'sumber'      => 'baru' // 🔥 penanda mode tambah
            ];

            return view('dtsen/pembaruan/detail', $data);
        } catch (\Throwable $e) {
            log_message('error', '❌ [tambah] ' . $e->getMessage());
            return view('errors/html/error_general', [
                'message' => 'Gagal membuka halaman tambah: ' . $e->getMessage(),
            ]);
        }
    }

    public function store()
    {
        try {
            $post    = $this->request->getPost();
            $session = session();
            $userId  = $session->get('id_user') ?? $session->get('user_id') ?? $session->get('id') ?? 0;
            $userNama = $session->get('nama') ?? 'Petugas';

            // 🧾 Siapkan payload awal (struktur sama seperti mode pembaruan)
            $payload = [
                'perumahan' => [
                    'no_kk'              => $post['no_kk'] ?? '',
                    'kepala_keluarga'    => $post['kepala_keluarga'] ?? '',
                    'alamat'             => $post['alamat'] ?? '',
                    'rw'                 => $post['rw'] ?? '',
                    'rt'                 => $post['rt'] ?? '',
                    'status_kepemilikan' => $post['status_rumah'] ?? '',
                    'kategori_adat'      => $post['kategori_adat'] ?? 'Tidak',
                    'nama_suku'          => $post['nama_suku'] ?? '',
                    'wilayah' => [
                        'provinsi'   => $post['provinsi'] ?? '',
                        'kabupaten'  => $post['kabupaten'] ?? '',
                        'kecamatan'  => $post['kecamatan'] ?? '',
                        'desa'       => $post['desa'] ?? '',
                        'alamat'     => $post['alamat'] ?? '',
                    ],
                    'wilayah_nama' => [
                        'provinsi'  => '',
                        'kabupaten' => '',
                        'kecamatan' => '',
                        'desa'      => ''
                    ],
                    'kondisi'  => [],
                    'sanitasi' => [],
                ],
                'aset'  => [],
                'foto'  => [],
                'geo'   => [],
            ];

            // 💾 Simpan ke tabel dtsen_usulan sebagai draft baru
            $this->db->table('dtsen_usulan')->insert([
                'usulan_no'   => 'TBH-' . date('ymdHis'),
                'jenis'       => 'keluarga_baru',
                'status'      => 'draft',
                'created_by'  => $userId,
                'payload'     => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'summary'     => 'Draft keluarga baru dibuat oleh ' . $userNama,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            $insertId = $this->db->insertID();

            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => 'Draft keluarga baru berhasil dibuat.',
                'redirect' => base_url("pembaruan-keluarga/detail/{$insertId}")
            ]);
        } catch (\Throwable $e) {
            log_message('error', '❌ [store] ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan keluarga baru: ' . $e->getMessage()
            ]);
        }
    }

    public function getAnggotaList($id_kk = null)
    {
        try {
            $db = \Config\Database::connect();

            if (empty($id_kk) || !is_numeric($id_kk)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'ID KK tidak valid.'
                ]);
            }

            // 🔍 Ambil master data pekerjaan untuk dynamic lookup label
            $masterPekerjaan = $db->table('tb_penduduk_pekerjaan')->get()->getResultArray();
            $pekerjaanMap = [];
            foreach ($masterPekerjaan as $kp) {
                $pekerjaanMap[$kp['pk_id']] = $kp['pk_nama'];
            }

            // 🔍 Cek apakah KK ini punya usulan aktif
            $usulan = $db->table('dtsen_usulan')
                ->select('id, status')
                ->where('dtsen_kk_id', $id_kk)
                ->whereIn('status', ['draft', 'submitted', 'verified', 'diverifikasi'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            $anggotaFinal = [];

            // =====================================================
            // 1️⃣ Ambil dari USULAN_ART (jika ada)
            // =====================================================
            $anggotaUsulan = [];
            if ($usulan) {
                $anggotaUsulan = $db->table('dtsen_usulan_art ua')
                    ->select('ua.*, s.jenis_shdk, ua.nik')
                    ->join('tb_shdk s', 's.id = ua.hubungan', 'left')
                    ->where('ua.dtsen_usulan_id', $usulan['id'])
                    ->where('ua.deleted_at', null)
                    ->orderBy('s.id', 'ASC')
                    ->get()
                    ->getResultArray();
            }

            // =====================================================
            // 2️⃣ Ambil dari tabel UTAMA (dtsen_art)
            // =====================================================
            $anggotaUtama = $db->table('dtsen_art a')
                ->select('a.*, kk.no_kk, s.jenis_shdk')
                ->join('tb_shdk s', 's.id = a.shdk', 'left')
                ->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')
                ->where('a.id_kk', $id_kk)
                ->where('a.deleted_at', null)
                ->orderBy('s.id', 'ASC')
                ->get()
                ->getResultArray();

            // Atur pekerjaan_label untuk data tabel utama
            foreach ($anggotaUtama as &$row) {
                $pkKey = $row['pekerjaan'] ?? '';
                $row['pekerjaan_label'] = $pekerjaanMap[$pkKey] ?? $row['pekerjaan'] ?? '-';
            }
            unset($row);

            // =====================================================
            // 3️⃣ Gabungkan data unik berdasarkan NIK
            // =====================================================
            $gabungan = [];

            foreach ($anggotaUtama as $row) {
                if (!empty($row['nik'])) {
                    $gabungan[$row['nik']] = $row;
                }
            }

            foreach ($anggotaUsulan as $row) {
                if (empty($row['nik'])) {
                    continue;
                }

                // ... (kode ekstraksi awal)
                $payload = [];
                if (!empty($row['payload_member'])) {
                    $payload = json_decode($row['payload_member'], true) ?? [];
                }

                $identitas = $payload['identitas'] ?? [];

                $row['no_kk'] = $identitas['individu_no_kk'] ?? null;
                $row['tanggal_lahir'] = $identitas['tanggal_lahir'] ?? null;
                $row['hubungan_keluarga_label'] = $row['jenis_shdk'] ?? $row['hubungan'] ?? '-';

                // 🚀 AMBIL DATA PEKERJAAN
                $pkInput = $payload['pekerjaan'] ?? $identitas['pekerjaan'] ?? null;
                $row['pekerjaan_label'] = $pekerjaanMap[$pkInput] ?? $payload['pekerjaan_nama'] ?? $pkInput ?? '-';

                // 🚀 AMBIL DATA WILAYAH CAPIL (Dari payload -> identitas)
                $row['provinsi']  = $identitas['provinsi'] ?? null;
                $row['kabupaten'] = $identitas['kabupaten'] ?? null;
                $row['kecamatan'] = $identitas['kecamatan'] ?? null;
                $row['desa']      = $identitas['desa'] ?? null;

                // Timpa data utama bila NIK sama (jalur usulan draft terbaru)
                $gabungan[$row['nik']] = $row;
            }

            $anggotaFinal = array_values($gabungan);

            // =====================================================
            // 4️⃣ Kembalikan respons JSON
            // =====================================================
            if (empty($anggotaFinal)) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Belum ada anggota keluarga.',
                    'data'    => [],
                ]);
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data anggota berhasil digabungkan.',
                'data'    => $anggotaFinal
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[getAnggotaList] ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    private function parseWilayahTugas(?string $wilayahTugas): array
    {
        $result = [];

        if (empty($wilayahTugas)) {
            return $result;
        }

        $rwGroups = explode('|', $wilayahTugas); // contoh: 002:003,004|005:001,002

        foreach ($rwGroups as $group) {
            [$rw, $rtList] = array_pad(explode(':', trim($group)), 2, '');
            if ($rw === '') continue;

            $rts = $rtList ? array_map('trim', explode(',', $rtList)) : [];

            $result[] = [
                'rw' => $rw,
                'rt' => $rts
            ];
        }

        return $result;
    }

    public function data()
    {
        $submitted = $this->request->getGet('submitted');

        if ($submitted) {
            return $this->getSubmittedData();
        }

        // existing: ?status=draft
        $status = $this->request->getGet('status');
        if ($status === 'draft') {
            return $this->getDataDraft();
        }

        // fallback
        return $this->respond(['data' => []]);
    }

    /**
     * Ambil data usulan (status = draft) untuk DataTables (Draft Pembaruan)
     * Route recommended: GET /pembaruan-keluarga/data
     */
    public function getDataDraft()
    {
        try {
            $session        = session();
            $kodeDesa       = $session->get('kode_desa');
            $wilayahTugas   = $session->get('wilayah_tugas');
            $roleId         = (int) ($session->get('role_id') ?? 99);
            $status         = $this->request->getGet('status') ?? 'draft';

            $db = $this->db;

            $builder = $db->table('dtsen_usulan us')
                ->select("
                us.id, us.usulan_no, us.jenis, us.status, us.dtsen_kk_id, 
                us.no_kk_target, us.created_at, us.updated_at,
                kk.no_kk, kk.kepala_keluarga,
                r.rw, r.rt,
                COALESCE(u.fullname, us.created_by) AS created_by_name,
                COALESCE(u.id, NULL) AS created_by_id,
                COALESCE(us.payload, '{}') AS payload,
                (SELECT COUNT(1) FROM dtsen_usulan_art aua WHERE aua.dtsen_usulan_id = us.id) AS jumlah_art_usulan
            ")
                ->join('dtsen_kk kk', 'kk.id_kk = us.dtsen_kk_id', 'left')
                ->join('dtsen_rt r', 'r.id_rt = kk.id_rt', 'left')
                ->join('dtks_users u', ' (u.id = us.created_by OR u.nik = us.created_by) ', 'left', false)
                ->where('us.status', $status);

            // Filter desa
            if (!empty($kodeDesa)) {
                $builder->where('r.kode_desa', $kodeDesa);
            }

            // Filter wilayah tugas (role >= 4)
            if (!empty($wilayahTugas) && $roleId >= 4) {
                $parsed = $this->parseWilayahTugas($wilayahTugas);

                $builder->groupStart(); // where group RW/RT

                foreach ($parsed as $group) {
                    $builder->orGroupStart()
                        ->where('r.rw', $group['rw']);

                    if (!empty($group['rt'])) {
                        $builder->whereIn('r.rt', $group['rt']);
                    }

                    $builder->groupEnd();
                }

                $builder->groupEnd();
            }

            $builder->orderBy('us.updated_at', 'ASC');

            $rows = $builder->get()->getResultArray();

            // 🔄 Format output baris
            foreach ($rows as &$r) {

                $payload = json_decode($r['payload'], true) ?? [];

                $r['no_kk'] = $r['no_kk'] ?? $r['no_kk_target'] ?? '';
                $r['nama_kepala'] =
                    $r['kepala_keluarga'] ??
                    ($payload['kepala_keluarga'] ?? '') ??
                    '';

                $r['rw_rt'] = "RW {$r['rw']} / RT {$r['rt']}";
                $r['created_by_name'] = $r['created_by_name'] ?? '-';
                $r['jumlah_art_usulan'] = (int) ($r['jumlah_art_usulan'] ?? 0);
            }
            unset($r);

            return $this->response->setJSON(['data' => $rows]);
        } catch (\Throwable $e) {
            log_message('error', '❌ getDataDraft() error: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'error' => true,
                    'message' => 'Gagal mengambil data draft: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Ambil data usulan (status = draft) yang sudah lengkap untuk DataTables (Submitted Pembaruan)
     * Route recommended: GET /pembaruan-keluarga/data?submitted=1
     */
    private function getSubmittedData()
    {
        try {
            $session        = session();
            $kodeDesa       = $session->get('kode_desa');
            $wilayahTugas   = $session->get('wilayah_tugas');
            $roleId         = (int) ($session->get('role_id') ?? 99);

            $db = \Config\Database::connect();

            $builder = $db->table('dtsen_usulan u')
                ->select("
                u.id, u.no_kk_target, u.status,
                u.created_at, u.updated_at,
                petugas.fullname AS created_by_name,
                petugas.nope AS created_by_nope,
                JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.perumahan.kepala_keluarga')) AS nama_kepala,
                r.rw, r.rt
            ")
                ->join('dtks_users petugas', 'petugas.id = u.created_by', 'left')
                ->join('dtsen_kk kk', 'kk.id_kk = u.dtsen_kk_id', 'left')
                ->join('dtsen_rt r', 'r.id_rt = kk.id_rt', 'left')

                ->where('u.status', 'draft')
                ->where('JSON_LENGTH(u.payload) >', 0)

                // Wajib field perumahan
                ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.perumahan.no_kk')) <> ''")
                ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.perumahan.kepala_keluarga')) <> ''")
                ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.perumahan.alamat')) <> ''")

                ->where("
                JSON_EXTRACT(u.payload, '$.perumahan.kondisi') IS NOT NULL
                AND JSON_LENGTH(JSON_EXTRACT(u.payload, '$.perumahan.kondisi')) > 0
            ")

                ->where("
                JSON_EXTRACT(u.payload, '$.perumahan.wilayah') IS NOT NULL
                AND JSON_LENGTH(JSON_EXTRACT(u.payload, '$.perumahan.wilayah')) > 0
            ")

                ->where("
                JSON_EXTRACT(u.payload, '$.perumahan.sanitasi') IS NOT NULL
                AND JSON_LENGTH(JSON_EXTRACT(u.payload, '$.perumahan.sanitasi')) > 0
            ")

                // Validasi foto wajib
                ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.foto.ktp_kk')) <> ''")
                ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.foto.dalam')) <> ''")
                ->where("JSON_UNQUOTE(JSON_EXTRACT(u.payload, '$.foto.depan')) <> ''")

                // Validasi ART wajib lengkap
                ->where('EXISTS (
                SELECT 1
                FROM dtsen_usulan_art a
                WHERE a.dtsen_usulan_id = u.id
                AND JSON_LENGTH(a.payload_member) > 0

                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, "$.identitas.nik")) <> ""
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, "$.identitas.nama")) <> ""
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, "$.identitas.jenis_kelamin")) <> ""
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, "$.pendidikan.jenjang_pendidikan")) <> ""
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, "$.kesehatan.penyakit_kronis")) <> ""
                AND JSON_UNQUOTE(JSON_EXTRACT(a.payload_member, "$.tenaga_kerja.pendapatan")) <> ""
            )');

            // Filter desa
            if (!empty($kodeDesa)) {
                $builder->where('r.kode_desa', $kodeDesa);
            }

            // Filter wilayah tugas (role >= 4)
            if (!empty($wilayahTugas) && $roleId >= 4) {
                $parsed = $this->parseWilayahTugas($wilayahTugas);

                $builder->groupStart();
                foreach ($parsed as $group) {
                    $builder->orGroupStart()
                        ->where('r.rw', $group['rw']);

                    if (!empty($group['rt'])) {
                        $builder->whereIn('r.rt', $group['rt']);
                    }

                    $builder->groupEnd();
                }
                $builder->groupEnd();
            }

            $builder->orderBy('u.updated_at', 'ASC');

            $result = $builder->get()->getResultArray();

            return $this->respond(['data' => $result]);
        } catch (\Throwable $e) {
            log_message('error', '❌ getSubmittedData error: ' . $e->getMessage());
            return $this->respond([
                'error' => true,
                'message' => 'Gagal mengambil data submitted: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncDesilPerKK($id_kk)
    {
        helper('dtsen');

        $db = \Config\Database::connect();

        try {

            // Ambil desil nasional terbaru
            $seData = $db->table('dtsen_se')
                ->select('kategori_desil')
                ->where('id_kk', $id_kk)
                ->orderBy('id_se', 'DESC')
                ->get()
                ->getRowArray();

            if (!$seData) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data desil nasional tidak ditemukan.'
                ]);
            }

            $desilNasional = (int) $seData['kategori_desil'];

            // Ambil histori terakhir
            $lastHistory = $db->table('dtsen_desil_history')
                ->select('desil')
                ->where('id_kk', $id_kk)
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getRowArray();

            $lastDesil = $lastHistory['desil'] ?? null;

            // Jika belum pernah ada histori atau berubah
            if ($lastDesil === null || $lastDesil !== $desilNasional) {

                $periode = getPeriodeDesil();

                $exists = $db->table('dtsen_desil_history')
                    ->where([
                        'id_kk'    => $id_kk,
                        'tahun'    => $periode['tahun'],
                        'triwulan' => $periode['triwulan']
                    ])
                    ->get()
                    ->getRow();

                if ($exists) {

                    $db->table('dtsen_desil_history')
                        ->where('id', $exists->id)
                        ->update([
                            'desil' => $desilNasional,
                            'source' => 'sync'
                        ]);
                } else {

                    $db->table('dtsen_desil_history')
                        ->insert([
                            'id_kk'        => $id_kk,
                            'desil'        => $desilNasional,
                            'tahun'        => $periode['tahun'],
                            'triwulan'     => $periode['triwulan'],
                            'periode_label' => $periode['label'],
                            'created_by'   => session()->get('id') ?? null
                        ]);
                }


                return $this->response->setJSON([
                    'status' => 'changed',
                    'from'   => $lastDesil,
                    'to'     => $desilNasional,
                    'periode' => $periode['label']
                ]);
            }

            return $this->response->setJSON([
                'status' => 'unchanged',
                'message' => 'Tidak ada perubahan desil.'
            ]);
        } catch (\Throwable $e) {

            log_message('error', '❌ [syncDesilPerKK] ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat sinkronisasi.'
            ]);
        }
    }

    public function syncDesilGlobal()
    {
        helper('dtsen');
        $db = \Config\Database::connect();

        try {

            // ==============================
            // 1️⃣ COOLDOWN CHECK (1 menit)
            // ==============================

            $lastSync = $db->table('dtsen_desil_sync_log')
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getRowArray();

            if ($lastSync) {
                $lastTime = strtotime($lastSync['created_at']);
                if ((time() - $lastTime) < 60) {
                    return $this->response->setJSON([
                        'status' => 'blocked',
                        'message' => 'Sync baru saja dilakukan. Tunggu 1 menit.'
                    ]);
                }
            }

            $periode = getPeriodeDesil();

            // ==============================
            // 2️⃣ TOTAL KK
            // ==============================

            $totalKK = $db->table('dtsen_kk')->countAllResults();

            // ==============================
            // 3️⃣ SUBQUERY LAST HISTORY
            // ==============================

            $subQuery = "
            SELECT h1.id_kk, h1.desil
            FROM dtsen_desil_history h1
            INNER JOIN (
                SELECT id_kk, MAX(created_at) as max_date
                FROM dtsen_desil_history
                GROUP BY id_kk
            ) h2
            ON h1.id_kk = h2.id_kk
            AND h1.created_at = h2.max_date
        ";

            // ==============================
            // 4️⃣ AMBIL YANG BERUBAH
            // ==============================

            $changedData = $db->query("
            SELECT se.id_kk, se.kategori_desil
            FROM dtsen_se se
            LEFT JOIN ($subQuery) last
            ON se.id_kk = last.id_kk
            WHERE last.desil IS NULL
               OR se.kategori_desil != last.desil
        ")->getResultArray();

            $totalBerubah = count($changedData);
            $totalTidakBerubah = $totalKK - $totalBerubah;

            // ==============================
            // 5️⃣ TRANSACTION INSERT
            // ==============================

            $db->transStart();

            foreach ($changedData as $row) {
                $db->table('dtsen_desil_history')->insert([
                    'id_kk'        => $row['id_kk'],
                    'desil'        => (int) $row['kategori_desil'],
                    'tahun'        => $periode['tahun'],
                    'triwulan'     => $periode['triwulan'],
                    'periode_label' => $periode['label'],
                    'created_by'   => session()->get('id')
                ]);
            }

            // Insert log aktivitas
            $db->table('dtsen_desil_sync_log')->insert([
                'total_keluarga'       => $totalKK,
                'total_berubah'        => $totalBerubah,
                'total_tidak_berubah'  => $totalTidakBerubah,
                'tahun'                => $periode['tahun'],
                'triwulan'             => $periode['triwulan'],
                'created_by'           => session()->get('id')
            ]);

            $db->transComplete();

            // 🔒 COOLDOWN CHECK
            $lastSync = cache('desil_global_last_sync');

            if ($lastSync) {

                $elapsed = time() - $lastSync;
                $cooldown = 60; // 1 menit

                if ($elapsed < $cooldown) {

                    $remaining = $cooldown - $elapsed;

                    return $this->response->setJSON([
                        'status' => 'cooldown',
                        'message' => 'Sinkronisasi global masih dalam masa cooldown.',
                        'remaining_seconds' => $remaining
                    ]);
                }
            }

            // ===== PROSES SYNC DI SINI =====

            // setelah selesai
            cache()->save('desil_global_last_sync', time(), 60);

            return $this->response->setJSON([
                'status' => 'success',
                'total'  => $totalKK,
                'changed' => $totalBerubah,
                'unchanged' => $totalTidakBerubah,
                'periode' => $periode['label']
            ]);
        } catch (\Throwable $e) {

            log_message('error', '❌ [syncDesilGlobal] ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat sync global.'
            ]);
        }
    }

    public function desilHistory($id_kk)
    {
        $db = \Config\Database::connect();

        try {

            // Subquery ambil snapshot terakhir per TW
            $subQuery = "
            SELECT id_kk, tahun, triwulan, MAX(created_at) as max_date
            FROM dtsen_desil_history
            WHERE id_kk = ?
            GROUP BY tahun, triwulan
        ";

            $query = $db->query("
            SELECT h.id_kk, h.desil, h.tahun, h.triwulan, h.periode_label, h.created_at
            FROM dtsen_desil_history h
            INNER JOIN ($subQuery) last
                ON h.id_kk = last.id_kk
                AND h.tahun = last.tahun
                AND h.triwulan = last.triwulan
                AND h.created_at = last.max_date
            ORDER BY h.tahun ASC, h.triwulan ASC
        ", [$id_kk]);

            $results = $query->getResultArray();

            if (empty($results)) {
                return $this->response->setJSON([
                    'status' => 'empty',
                    'data' => []
                ]);
            }

            // Hitung delta change
            $formatted = [];
            $previousDesil = null;

            foreach ($results as $row) {

                $currentDesil = (int) $row['desil'];

                $delta = null;
                $trend = 'stabil';

                if ($previousDesil !== null) {
                    $delta = $currentDesil - $previousDesil;

                    if ($delta < 0) {
                        $trend = 'naik';      // kesejahteraan naik
                    } elseif ($delta > 0) {
                        $trend = 'turun';     // kesejahteraan turun
                    }
                }

                $formatted[] = [
                    'periode' => $row['periode_label'],
                    'tahun'   => (int) $row['tahun'],
                    'triwulan' => (int) $row['triwulan'],
                    'desil'   => $currentDesil,
                    'delta'   => $delta,
                    'trend'   => $trend,
                    'created_at' => $row['created_at']
                ];

                $previousDesil = $currentDesil;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => $formatted
            ]);
        } catch (\Throwable $e) {

            log_message('error', '❌ [desilHistory] ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengambil histori desil.'
            ]);
        }
    }

    public function addHistoricalDesil()
    {
        try {
            $session = session();
            $roleId = $session->get('role_id') ?? 99;

            // 🔒 Hanya role <= 3 yang boleh
            if ($roleId > 3) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menambahkan snapshot historis.'
                ]);
            }

            $post = $this->request->getPost();
            $userId = $session->get('id_user') ?? 0;

            $idKk     = $post['id_kk'] ?? null;
            $tahun    = (int) ($post['tahun'] ?? 0);
            $triwulan = (int) ($post['triwulan'] ?? 0);

            // 🚀 PERBAIKAN: Tangkap desil secara mentah dulu
            $desilRaw = $post['desil'] ?? null;

            // 🚀 PERBAIKAN: Validasi harus mengecek jika desil benar-benar tidak dikirim atau string kosong
            if (!$idKk || $desilRaw === null || $desilRaw === '' || !$tahun || !$triwulan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak lengkap.'
                ]);
            }

            // Pastikan desil menjadi integer (0 akan tetap 0)
            $desil = (int) $desilRaw;

            // 🔍 Cek apakah sudah ada periode ini
            $existing = $this->db->table('dtsen_desil_history')
                ->where([
                    'id_kk'    => $idKk,
                    'tahun'    => $tahun,
                    'triwulan' => $triwulan
                ])
                ->get()
                ->getRowArray();

            if ($existing) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Snapshot periode ini sudah ada.'
                ]);
            }

            $label = 'TW' . $triwulan . ' ' . $tahun;

            $this->db->table('dtsen_desil_history')->insert([
                'id_kk'         => $idKk,
                'desil'         => $desil,
                'tahun'         => $tahun,
                'triwulan'      => $triwulan,
                'periode_label' => $label,
                'source'        => 'historical_manual',
                'created_by'    => $userId
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Snapshot historis berhasil ditambahkan.'
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
