<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Models\GenModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\API\ResponseTrait;

class PembaruanKeluarga extends BaseController
{
    use ResponseTrait;

    protected $genModel;
    protected $db;

    public function __construct()
    {
        $this->genModel = new GenModel();
        $this->db = db_connect();
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

            log_message('debug', "🚀 [detail] Memulai load detail untuk id_kk={$id_kk}");

            // 1) Ambil data KK utama (pastikan array)
            $kkData = $db->table('dtsen_kk')
                ->where('id_kk', $id_kk)
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
                    'alamat'            => trim($post['alamat']),
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
                    'alamat'                   => trim($post['alamat']),
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

            // 💾 Siapkan data baru dari form
            $perumahanBaru = [
                'no_kk'              => $post['no_kk'] ?? $kkData['no_kk'],
                'kepala_keluarga'    => $post['kepala_keluarga'] ?? $kkData['kepala_keluarga'],
                'alamat'             => trim($post['alamat'] ?? $kkData['alamat']),
                'rw'                 => $post['rw'] ?? '',
                'rt'                 => $post['rt'] ?? '',
                'status_kepemilikan' => $post['status_rumah'] ?? $kkData['status_kepemilikan_rumah'] ?? '',
                'kategori_adat'      => $post['kategori_adat'] ?? 'Tidak',
                'nama_suku'          => $post['nama_suku'] ?? ''
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
                $payloadBaru = ['perumahan' => $perumahanBaru];
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
        $id = $this->request->getJSON()->id ?? null;

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
     * - Jika status = 'applied', juga update ke dtsen_rt
     */
    public function saveRumah()
    {
        try {
            $post = $this->request->getPost();
            $user = session()->get();

            // === VALIDASI INPUT ===
            $sumberListrik   = trim($post['sumber_listrik'] ?? '');
            $nomorPelanggan  = trim($post['nomor_pelanggan'] ?? '');
            $nomorMeter      = trim($post['nomor_meter'] ?? '');

            // Jika pilihannya adalah "Listrik PLN dengan meteran" → WAJIB isi nomor pelanggan & meter
            if ($sumberListrik === 'Listrik PLN dengan meteran') {

                // wajib isi nomor pelanggan
                if ($nomorPelanggan === '') {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Nomor Pelanggan wajib diisi untuk sumber listrik PLN dengan meteran.'
                    ]);
                }

                // wajib isi nomor meter
                if ($nomorMeter === '') {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Nomor Meter wajib diisi untuk sumber listrik PLN dengan meteran.'
                    ]);
                }

                // Validasi nomor pelanggan (11–13 digit)
                if (!preg_match('/^[0-9]{11,13}$/', $nomorPelanggan)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Nomor Pelanggan harus terdiri dari 11 sampai 13 digit angka.'
                    ]);
                }

                // Validasi nomor meter (8–13 digit)
                if (!preg_match('/^[0-9]{8,13}$/', $nomorMeter)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Nomor Meter harus terdiri dari 8 sampai 13 digit angka.'
                    ]);
                }
            }

            // Jika bukan PLN dengan meteran → validasi hanya jika diisi saja
            else {

                // Validasi nomor pelanggan opsional
                if ($nomorPelanggan !== '' && !preg_match('/^[0-9]{11,13}$/', $nomorPelanggan)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Nomor Pelanggan harus terdiri dari 11 sampai 13 digit angka.'
                    ]);
                }

                // Validasi nomor meter opsional
                if ($nomorMeter !== '' && !preg_match('/^[0-9]{8,13}$/', $nomorMeter)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Nomor Meter harus terdiri dari 8 sampai 13 digit angka.'
                    ]);
                }
            }

            // === PROSES SIMPAN DATA ===
            $usulanId = $post['dtsen_usulan_id'] ?? null;
            if (!$usulanId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID usulan tidak ditemukan.'
                ]);
            }

            $this->db->transBegin();

            // 🔍 Ambil data usulan lama
            $usulanRow = $this->db->table('dtsen_usulan')
                ->select('id, payload, status, dtsen_kk_id')
                ->where('id', $usulanId)
                ->get()
                ->getRowArray();

            if (!$usulanRow) {
                throw new \Exception('Data usulan tidak ditemukan.');
            }

            $payloadLama = json_decode($usulanRow['payload'] ?? '{}', true);
            if (!is_array($payloadLama)) $payloadLama = [];

            // Pastikan struktur minimum
            $payloadLama['perumahan'] = $payloadLama['perumahan'] ?? [];

            // 💾 Siapkan struktur baru
            $perumahanBaru = [
                'status_kepemilikan' => $post['status_kepemilikan'] ?? null, // simpan di level utama
                'wilayah' => [
                    'provinsi'   => $post['provinsi'] ?? null,
                    'kabupaten'  => $post['regency'] ?? null,
                    'kecamatan'  => $post['district'] ?? null,
                    'desa'       => $post['village'] ?? null
                ],
                'kondisi' => [
                    'luas_lantai'     => (float)($post['luas_lantai'] ?? 0),
                    'jenis_lantai'    => $post['jenis_lantai'] ?? null,
                    'jenis_dinding'    => $post['jenis_dinding'] ?? null,
                    'jenis_atap'      => $post['jenis_atap'] ?? null,
                    'bahan_bakar'     => $post['bahan_bakar'] ?? null,
                    'sumber_air'      => $post['sumber_air'] ?? null,
                    'sumber_listrik'  => $post['sumber_listrik'] ?? null,
                    'nomor_pelanggan' => $post['nomor_pelanggan'] ?? null,
                    'nomor_meter'     => $post['nomor_meter'] ?? null,
                    'daya_listrik'    => $post['daya_listrik'] ?? null,
                ],
                'sanitasi' => [
                    'fasilitas_bab'       => $post['fasilitas_bab'] ?? null,
                    'jenis_kloset'        => $post['jenis_kloset'] ?? null,
                    'jarak_air_ke_limbah' => $post['jarak_air_ke_limbah'] ?? null,
                    'pembuangan_tinja'    => $post['pembuangan_tinja'] ?? null,
                ]
            ];

            // ⚙️ Gabungkan data lama & baru
            $gabungan = $payloadLama['perumahan'];
            $gabungan['status_kepemilikan'] = $perumahanBaru['status_kepemilikan']; // overwrite langsung di level utama

            $gabungan['wilayah']  = array_merge($gabungan['wilayah'] ?? [], $perumahanBaru['wilayah']);
            $gabungan['kondisi']  = array_merge($gabungan['kondisi'] ?? [], $perumahanBaru['kondisi']);
            $gabungan['sanitasi'] = array_merge($gabungan['sanitasi'] ?? [], $perumahanBaru['sanitasi']);

            // 🚫 Hapus kunci lama yang bisa konflik
            unset($gabungan['kondisi']['kepemilikan_rumah']);
            unset($gabungan['kondisi']['status_kepemilikan']);

            // 🚫 Hapus alamat di wilayah karena ditangani di tab keluarga
            unset($gabungan['wilayah']['alamat']);

            // 🔄 Simpan gabungan ke payload utama
            $payloadLama['perumahan'] = $gabungan;

            // 💾 Update payload di tabel dtsen_usulan
            $this->db->table('dtsen_usulan')
                ->where('id', $usulanId)
                ->update([
                    'payload' => json_encode($payloadLama, JSON_UNESCAPED_UNICODE),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'summary' => 'Data rumah diperbarui oleh ' . ($user['nama'] ?? 'Sistem')
                ]);

            // ⚡ Jika status sudah "applied", update juga dtsen_rt
            if ($usulanRow['status'] === 'applied') {
                $kkRow = $this->db->table('dtsen_kk')
                    ->select('id_rt')
                    ->where('id_kk', $usulanRow['dtsen_kk_id'])
                    ->get()
                    ->getRowArray();

                if ($kkRow) {
                    $updateRT = [
                        'kepemilikan_rumah' => $post['status_kepemilikan'] ?? null,
                        'luas_lantai'    => (float)($post['luas_lantai'] ?? 0),
                        'jenis_lantai'    => $post['jenis_lantai'] ?? null,
                        'jenis_dinding'    => $post['jenis_dinding'] ?? null,
                        'bahan_bakar'    => $post['bahan_bakar'] ?? null,
                        'sumber_air'     => $post['sumber_air'] ?? null,
                        'sumber_listrik' => $post['sumber_listrik'] ?? null,
                        'updated_at'     => date('Y-m-d H:i:s'),
                        'updated_by'     => $user['nama'] ?? 'system'
                    ];
                    $this->db->table('dtsen_rt')
                        ->where('id_rt', $kkRow['id_rt'])
                        ->update($updateRT);
                }
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data rumah berhasil disimpan.'
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
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
            // 🏠 1️⃣ Update Tabel dtsen_rt
            // =======================================================
            $geo      = $payload['geo'] ?? [];
            $foto     = $payload['foto'] ?? [];
            $rumah    = $payload['perumahan'] ?? [];
            $kondisi  = $rumah['kondisi'] ?? [];
            $sanitasi = $rumah['sanitasi'] ?? [];

            $rtUpdate = [
                'kepemilikan_rumah' => $rumah['status_kepemilikan'] ?? 'Milik Sendiri',
                'luas_lantai'        => $kondisi['luas_lantai'] ?? null,
                'jenis_lantai'       => $kondisi['jenis_lantai'] ?? null,
                'jenis_dinding'      => $kondisi['jenis_dinding'] ?? null,
                'bahan_bakar'        => $kondisi['bahan_bakar'] ?? null,
                'sumber_air'         => $kondisi['sumber_air'] ?? null,
                'sumber_listrik'     => $kondisi['sumber_listrik'] ?? null,
                'sanitasi'           => $sanitasi['pembuangan_tinja'] ?? null,
                'foto_rumah'         => $foto['depan'] ?? null,
                'foto_rumah_dalam'   => $foto['dalam'] ?? null,
                'latitude'           => $geo['lat'] ?? null,
                'longitude'          => $geo['lng'] ?? null,
                'updated_at'         => date('Y-m-d H:i:s'),
                'updated_by'         => $userId
            ];

            // ambil id_rt untuk update
            $idRt = $this->db->table('dtsen_kk')->select('id_rt')->where('id_kk', $idKk)->get()->getRow('id_rt');
            if ($idRt) {
                $this->db->table('dtsen_rt')->where('id_rt', $idRt)->update($rtUpdate);
            }

            // =======================================================
            // 👪 2️⃣ Update Tabel dtsen_kk (DATA RUMAH)
            // =======================================================
            $kkUpdate = [
                'no_kk'                    => $rumah['no_kk'] ?? null,
                'kepala_keluarga'          => $rumah['kepala_keluarga'] ?? null,
                'alamat'                   => $rumah['alamat'] ?? null,
                'status_kepemilikan_rumah' => $rumah['status_kepemilikan'] ?? null,
                'kategori_adat'            => $rumah['kategori_adat'] ?? 'Tidak',
                'nama_suku'                => $rumah['nama_suku'] ?? null,
                'foto_kk'                  => $foto['ktp_kk'] ?? $foto['ktp'] ?? null,
                'foto_rumah'               => $foto['depan'] ?? null,
                'foto_rumah_dalam'         => $foto['dalam'] ?? null,
                'updated_at'               => date('Y-m-d H:i:s'),
                'updated_by'               => $userId
            ];
            $this->db->table('dtsen_kk')->where('id_kk', $idKk)->update($kkUpdate);

            // =======================================================
            // 👤 3️⃣ Sinkronisasi dtsen_art
            // =======================================================
            $anggotaUsulan = $this->db->table('dtsen_usulan_art')
                ->where('dtsen_usulan_id', $usulan_id)
                ->get()
                ->getResultArray();

            if (!empty($anggotaUsulan)) {
                // hapus dulu anggota lama, lalu insert yang baru
                $this->db->table('dtsen_art')->where('id_kk', $idKk)->delete();

                foreach ($anggotaUsulan as $art) {
                    $payloadMember = json_decode($art['payload_member'] ?? '{}', true);
                    $identitas     = $payloadMember['identitas'] ?? [];

                    $dataArt = [
                        'id_kk'             => $idKk,
                        'nik'               => $identitas['nik'] ?? $art['nik'] ?? null,
                        'nama'              => $identitas['nama'] ?? $art['nama'] ?? null,
                        'jenis_kelamin'     => $identitas['jenis_kelamin'] ?? null,
                        'tanggal_lahir'     => $identitas['tanggal_lahir'] ?? null,
                        'tempat_lahir'      => $identitas['tempat_lahir'] ?? null,
                        'pendidikan_terakhir' => $identitas['pendidikan'] ?? null,
                        'pekerjaan'         => $identitas['pekerjaan'] ?? null,
                        'status_kawin'      => $identitas['status_kawin'] ?? null,
                        'hubungan_keluarga' => $identitas['hubungan'] ?? null,
                        'foto_identitas'    => $payloadMember['foto'] ?? null,
                        'source_name'       => 'apply_usulan_' . $usulan_id,
                        'created_by'        => $userId,
                        'created_at'        => date('Y-m-d H:i:s')
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

            $existingSE = $this->db->table('dtsen_se')
                ->where('id_kk', $idKk)
                ->get()
                ->getRowArray();

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
                    'id_rt'        => $idRt,
                    'id_kk'        => $idKk,
                    'kepemilikan_aset'       => $kepemilikan_aset,
                    'kepemilikan_bantuan'    => $kepemilikan_bantuan,
                    'rata_penghasilan_bulanan' => $payload['penghasilan'] ?? null,
                    'rata_pengeluaran_bulanan' => $payload['pengeluaran'] ?? null,
                    'latitude'     => $geo['lat'] ?? null,
                    'longitude'    => $geo['lng'] ?? null,
                    'source_name'  => 'apply_usulan_' . $usulan_id,
                    'created_by'   => $userId,
                    'created_at'   => date('Y-m-d H:i:s')
                ]);
            }

            // =======================================================
            // 🟦 UPDATE STATUS USULAN
            // =======================================================
            $this->db->table('dtsen_usulan')
                ->where('id', $usulan_id)
                ->update([
                    'status'       => 'diverifikasi',
                    'verified_at'  => date('Y-m-d H:i:s'),
                    'verified_by'  => $userId
                ]);

            // =======================================================
            // 🟩 WA INTEGRATION — Generate Reminder Log
            // =======================================================
            $waConfig = $this->db->table('dtsen_wa_config')
                ->where('user_id', $userId)
                ->get()
                ->getRowArray();

            $interval = $waConfig['reminder_default_months'] ?? 3;
            $dueDate  = date('Y-m-d H:i:s', strtotime("+$interval months"));

            // Insert reminder log
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
            log_message('info', "✅ Usulan ID {$usulan_id} diterapkan oleh {$userId}. Reminder dibuat.");

            // =======================================================
            // 🔔 Kirim WhatsApp ke Petugas Entri (dtks_users.nope)
            // Hanya kirim jika status sebelumnya bukan 'diverifikasi'
            // =======================================================
            try {
                if (($statusSebelumnya ?? '') !== 'diverifikasi') {

                    $creatorNik = $usulan['created_by'] ?? null;

                    if (!empty($creatorNik)) {

                        // 1) Coba cari berdasarkan NIK
                        $petugas = $this->db->table('dtks_users')
                            ->select('id, fullname, nope, nik')
                            ->where('nik', $creatorNik)
                            ->get()
                            ->getRowArray();

                        // 2) Jika tidak ketemu → fallback cari berdasarkan user_id
                        if (!$petugas) {
                            $petugas = $this->db->table('dtks_users')
                                ->select('id, fullname, nope, nik')
                                ->where('id', $creatorNik)
                                ->get()
                                ->getRowArray();

                            if ($petugas) {
                                log_message('info', "[WA APPLY] Petugas ditemukan via fallback user_id={$creatorNik}");
                            }
                        }

                        if (!$petugas) {
                            log_message('warning', "[WA APPLY] Petugas tidak ditemukan. created_by={$creatorNik}");
                        }

                        if ($petugas && !empty($petugas['nope'])) {

                            // Normalisasi nomor HP → 0812 menjadi 62812 dst
                            $waService = new \App\Libraries\WaService();
                            // $nomorWA  = $waService->normalizeNumber($petugas['nope']);
                            $nomorWA = preg_replace('/[^0-9]/', '', $petugas['nope']); // hapus semua non-digit

                            if (str_starts_with($nomorWA, '0')) {
                                $nomorWA = '62' . substr($nomorWA, 1);
                            }

                            if (str_starts_with($nomorWA, '620')) {
                                $nomorWA = '62' . substr($nomorWA, 3);
                            }

                            // Ambil info KK untuk isi pesan
                            $kkInfo = $this->db->table('dtsen_kk')
                                ->select('no_kk, kepala_keluarga, alamat, id_rt')
                                ->where('id_kk', $idKk)
                                ->get()
                                ->getRowArray();

                            // Ambil RT/RW
                            $rtText = '-';
                            $rwText = '-';
                            if (!empty($kkInfo['id_rt'])) {
                                $rtRow = $this->db->table('dtsen_rt')
                                    ->select('rt,rw')
                                    ->where('id_rt', $kkInfo['id_rt'])
                                    ->get()
                                    ->getRowArray();

                                if ($rtRow) {
                                    $rtText = $rtRow['rt'] ?? '-';
                                    $rwText = $rtRow['rw'] ?? '-';
                                }
                            }

                            // Format tanggal Indonesia lengkap
                            $hari = [
                                'Sunday' => 'Minggu',
                                'Monday' => 'Senin',
                                'Tuesday' => 'Selasa',
                                'Wednesday' => 'Rabu',
                                'Thursday' => 'Kamis',
                                'Friday' => 'Jumat',
                                'Saturday' => 'Sabtu'
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

                            $now = date('Y-m-d H:i:s');
                            $hariIndo = $hari[date('l', strtotime($now))];
                            $tgl = date('d', strtotime($now));
                            $bln = $bulan[intval(date('m', strtotime($now)))];
                            $thn = date('Y', strtotime($now));
                            $jam = date('H:i', strtotime($now)) . " WIB";

                            $tanggalLengkap = "{$hariIndo}, {$tgl} {$bln} {$thn}, {$jam}";

                            // ===============================
                            // 📌 Format Pesan WA (Final)
                            // ===============================
                            $msg  = "*== SINDEN System ==*\n";
                            $msg .= "📌 *Pemberitahuan Validasi Groundcheck*\n";
                            $msg .= "Usulan No. {$usulan_id} telah selesai divalidasi.\n\n";
                            $msg .= "👤 Kepala Keluarga: *" . ($kkInfo['kepala_keluarga'] ?? '-') . "*\n";
                            $msg .= "🏠 No. KK: *" . ($kkInfo['no_kk'] ?? '-') . "*\n";
                            $msg .= "📍 Alamat: " . ($kkInfo['alamat'] ?? '-') . " RT {$rtText} RW {$rwText}\n";
                            $msg .= "🗓 Waktu: {$tanggalLengkap}\n\n";
                            $msg .= "Terima kasih atas kerja baiknya.";

                            // ===============================
                            // 📤 Kirim WA via WaService
                            // ===============================
                            try {
                                // $send = $waService->sendText($nomorWA, $msg);
                                $send = $waService->sendText($nomorWA, $msg);

                                if (!is_array($send) || empty($send['status']) || $send['status'] != 'success') {
                                    log_message('error', '[WA APPLY] Provider WA error: ' . json_encode($send));
                                }

                                log_message('info', '[WA APPLY] Pesan terkirim ke ' . $nomorWA . ' | ' . json_encode($send));
                            } catch (\Throwable $e) {
                                log_message('error', '[WA APPLY] ERROR mengirim WA: ' . $e->getMessage());
                            }
                        } else {
                            log_message('warning', "[WA APPLY] Nomor WA tidak ditemukan di dtks_users untuk NIK: {$creatorNik}");
                        }
                    } else {
                        log_message('warning', "[WA APPLY] created_by kosong, WA tidak dapat dikirim.");
                    }
                } else {
                    log_message('info', "[WA APPLY] Status sebelumnya sudah diverifikasi — WA tidak dikirim ulang.");
                }
            } catch (\Throwable $e) {
                log_message('error', "[WA APPLY OUTER] {$e->getMessage()}");
            }

            return $this->response->setJSON([
                'status'   => 'success',
                'message'  => 'Data usulan berhasil diterapkan ke database utama.',
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

    /**
     * 👨‍👩‍👧 Ambil daftar anggota keluarga (gabungan usulan_art + tabel utama)
     * - Jika ada usulan draft → tampilkan gabungan unik berdasarkan NIK
     * - Jika tidak ada draft → tampilkan dari tabel utama saja
     */
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

            // 🔍 Cek apakah KK ini punya usulan aktif
            $usulan = $db->table('dtsen_usulan')
                ->select('id, status')
                ->where('dtsen_kk_id', $id_kk)
                ->whereIn('status', ['draft', 'submitted', 'verified', 'diverifikasi'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            // 📦 Data hasil akhir
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
                    // orderBy berdasarkan hubungan keluarga
                    ->orderBy('s.id', 'ASC')
                    ->get()
                    ->getResultArray();
            }

            // Simpan semua NIK dari usulan untuk mencegah duplikat
            $nikUsulan = array_filter(array_column($anggotaUsulan, 'nik'));

            // =====================================================
            // 2️⃣ Ambil dari tabel UTAMA (dtsen_art)
            // =====================================================
            $anggotaUtama = $db->table('dtsen_art a')
                ->select('a.*, s.jenis_shdk, a.nik')
                ->join('tb_shdk s', 's.id = a.shdk', 'left')
                ->where('a.id_kk', $id_kk)
                ->where('a.deleted_at', null)
                ->orderBy('s.id', 'ASC')
                ->get()
                ->getResultArray();

            // =====================================================
            // 3️⃣ Gabungkan data unik berdasarkan NIK
            // =====================================================
            // Gunakan NIK sebagai kunci unik
            $gabungan = [];

            foreach ($anggotaUtama as $row) {
                if (!empty($row['nik'])) {
                    $gabungan[$row['nik']] = $row;
                }
            }

            foreach ($anggotaUsulan as $row) {
                if (!empty($row['nik'])) {
                    // Jika ada di usulan, timpa data lama
                    $gabungan[$row['nik']] = $row;
                }
            }

            // Konversi ke array numerik biasa
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
     * Ambil data usulan (status = submitted) yang sudah lengkap untuk DataTables (Submitted Pembaruan)
     * Route recommended: GET /pembaruan-keluarga/data?submitted=1
     */    private function getSubmittedData()
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
}
