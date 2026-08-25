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

    // 🏠 Halaman utama pembaruan
    public function index()
    {
        return redirect()->to('/dtsen-se');
    }

    public function detail($id_kk)
    {
        try {
            $db = \Config\Database::connect();
            $genModel = new \App\Models\GenModel();
            $kkModel  = new \App\Models\Dtsen\DtsenKkModel();

            $kkData = $db->table('dtsen_kk')
                ->where('id_kk', $id_kk)
                ->groupStart()
                ->where('deleted_at', null)
                ->orWhere('deleted_at', '0000-00-00 00:00:00')
                ->groupEnd()
                ->get()
                ->getRowArray() ?? [];

            if (empty($kkData)) {
                throw new \Exception("Data KK tidak ditemukan untuk id_kk={$id_kk}");
            }

            $seData = $db->table('dtsen_se')->select('kategori_desil')->where('id_kk', $id_kk)->orderBy('id_se', 'DESC')->get()->getRowArray();
            $kategoriDesil = $seData['kategori_desil'] ?? null;

            $rtData = [];
            if (!empty($kkData['id_rt'])) {
                $rtData = $db->table('dtsen_rt')->where('id_rt', $kkData['id_rt'])->get()->getRowArray() ?? [];
            }

            $usulan = $db->table('dtsen_usulan')
                ->where('dtsen_kk_id', $id_kk)
                ->whereIn('status', ['draft', 'submitted', 'verified', 'diverifikasi'])
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();

            if (!is_array($usulan)) $usulan = [];
            if (!array_key_exists('status', $usulan)) $usulan['status'] = null;

            $payload = [];
            $payloadPerumahan = [];
            if (!empty($usulan['payload'])) {
                $decoded = json_decode($usulan['payload'], true);
                if (is_array($decoded)) {
                    $payload = $decoded;
                    $payloadPerumahan = $payload['perumahan'] ?? [];
                }
            }

            $is_submitted_ready = 0;
            if (($usulan['status'] ?? '') === 'draft' && !empty($payload)) {
                if ($kkModel->isPayloadLengkap($payload)) {
                    $is_submitted_ready = 1;
                }
            }

            if (!is_array($payloadPerumahan)) $payloadPerumahan = [];

            $wilayahKode = $payloadPerumahan['wilayah'] ?? [];
            if (!is_array($wilayahKode)) $wilayahKode = [];

            $wilayahNama = ['provinsi' => '', 'kabupaten' => '', 'kecamatan' => '', 'desa' => ''];

            $kodeLookup = $wilayahKode['desa'] ?? $wilayahKode['kecamatan'] ?? $wilayahKode['kabupaten'] ?? $wilayahKode['provinsi'] ?? null;
            if ($kodeLookup) {
                try {
                    $hasil = $genModel->getNamaWilayah($kodeLookup);
                    if (is_array($hasil)) $wilayahNama = array_merge($wilayahNama, $hasil);
                } catch (\Throwable $e) {
                }
            }

            $statusKepemilikan =
                $payloadPerumahan['status_kepemilikan'] ??
                ($payloadPerumahan['kondisi']['status_kepemilikan'] ?? null) ??
                ($payloadPerumahan['kepemilikan_rumah'] ?? null) ??
                ($rtData['kepemilikan_rumah'] ?? null) ??
                ($kkData['status_kepemilikan_rumah'] ?? null) ??
                '';

            // 7) MODE MASTER
            if (empty($usulan['id'])) {
                $perumahan = [
                    'kategori_desil'      => $kkData['kategori_desil'] ?? '',
                    'no_kk'               => $kkData['no_kk'] ?? '',
                    'kepala_keluarga'     => $kkData['kepala_keluarga'] ?? '',
                    'nik_kepala_keluarga' => $kkData['nik_kepala_keluarga'] ?? '',
                    'jumlah_anggota'      => $kkData['jumlah_anggota'] ?? '',
                    'nama_jalan'          => $kkData['nama_jalan'] ?? '',
                    'nomor_rumah'         => $kkData['nomor_rumah'] ?? '',
                    'dusun'               => $kkData['dusun'] ?? '',
                    'kode_pos'            => $kkData['kode_pos'] ?? '',
                    'is_alamat_sesuai_kk' => $kkData['is_alamat_sesuai_kk'] ?? 'Ya',
                    'alamat'              => $kkData['alamat'] ?? '',
                    'rw'                  => $rtData['rw'] ?? ($kkData['rw'] ?? ''),
                    'rt'                  => $rtData['rt'] ?? ($kkData['rt'] ?? ''),
                    'status_kepemilikan'  => $statusKepemilikan,
                    'kategori_adat'       => $kkData['kategori_adat'] ?? 'Tidak',
                    'nama_suku'           => $kkData['nama_suku'] ?? '',
                    'wilayah_nama'        => $wilayahNama,
                    'kondisi'             => $rtData ? [
                        'luas_lantai'              => $rtData['luas_lantai'] ?? null,
                        'jenis_lantai'             => $rtData['jenis_lantai'] ?? null,
                        'kondisi_lantai'           => $rtData['kondisi_lantai'] ?? null,
                        'jenis_dinding'            => $rtData['jenis_dinding'] ?? null,
                        'kondisi_dinding'          => $rtData['kondisi_dinding'] ?? null,
                        'jenis_atap'               => $rtData['kondisi_atap'] ?? null,
                        'kondisi_atap'             => $rtData['kondisi_atap'] ?? null,
                        'bahan_bakar'              => $rtData['bahan_bakar'] ?? null,
                        'sumber_air'               => $rtData['sumber_air'] ?? null,
                        'sumber_listrik'           => $rtData['sumber_listrik'] ?? null,
                        'jumlah_meteran_listrik'   => $rtData['jumlah_meteran_listrik'] ?? null,
                        'nomor_pelanggan'          => $rtData['nomor_pelanggan'] ?? null,
                        'nomor_meter'              => $rtData['nomor_meter'] ?? null,
                        'daya_listrik'             => $rtData['daya_listrik'] ?? null,
                        'jenis_bangunan'           => $rtData['jenis_bangunan'] ?? null,
                        'is_tinggal_bersama'       => $rtData['is_tinggal_bersama'] ?? null,
                        'jumlah_kk_dalam_rumah'    => $rtData['jumlah_kk_dalam_rumah'] ?? null,
                        'no_kk_lainnya'            => $rtData['no_kk_lainnya'] ?? null,
                        'jumlah_orang_dalam_rumah' => $rtData['jumlah_orang_dalam_rumah'] ?? null,
                        'perkiraan_harga_sewa'     => $rtData['perkiraan_harga_sewa'] ?? null,
                        'bukti_kepemilikan'        => $rtData['bukti_kepemilikan'] ?? null,
                    ] : ($payloadPerumahan['kondisi'] ?? []),
                    'sanitasi' => $payloadPerumahan['sanitasi'] ?? []
                ];

                $payload['perumahan'] = $perumahan;

                $anggota = $db->table('dtsen_art')->where('id_kk', $id_kk)->where('deleted_at', null)->get()->getResultArray();

                // 🚀 PREFILL GEO (Dari dtsen_rt)
                $payload['geo'] = ['lat' => $rtData['latitude'] ?? '', 'lng' => $rtData['longitude'] ?? ''];

                return view('dtsen/pembaruan/detail', [
                    'title' => 'Detail Pembaruan Keluarga',
                    'namaApp' => nameApp(),
                    'user' => session()->get(),
                    'kkData' => $kkData,
                    'rtData' => $rtData,
                    'perumahan' => $perumahan,
                    'anggota' => $anggota,
                    'payload' => $payload,
                    'usulan' => $usulan,
                    'id_kk' => $kkData['id_kk'],
                    'sumber' => 'utama',
                    'kategori_desil' => $kategoriDesil,
                    'is_submitted_ready' => $is_submitted_ready
                ]);
            }

            // 8) MODE USULAN
            $payload['perumahan'] = $payload['perumahan'] ?? [];
            if (!isset($payload['perumahan']['kondisi']) || !is_array($payload['perumahan']['kondisi'])) $payload['perumahan']['kondisi'] = $payloadPerumahan['kondisi'] ?? [];
            if (!isset($payload['perumahan']['sanitasi']) || !is_array($payload['perumahan']['sanitasi'])) $payload['perumahan']['sanitasi'] = $payloadPerumahan['sanitasi'] ?? [];
            $payload['perumahan']['wilayah_nama'] = $wilayahNama;

            $perumahan = [
                'no_kk'               => $payloadPerumahan['no_kk'] ?? $kkData['no_kk'] ?? '',
                'kepala_keluarga'     => $payloadPerumahan['kepala_keluarga'] ?? $kkData['kepala_keluarga'] ?? '',
                'nik_kepala_keluarga' => $payloadPerumahan['nik_kepala_keluarga'] ?? $kkData['nik_kepala_keluarga'] ?? '',
                'jumlah_anggota'      => $payloadPerumahan['jumlah_anggota'] ?? $kkData['jumlah_anggota'] ?? '',
                'nama_jalan'          => $payloadPerumahan['nama_jalan'] ?? $kkData['nama_jalan'] ?? '',
                'nomor_rumah'         => $payloadPerumahan['nomor_rumah'] ?? $kkData['nomor_rumah'] ?? '',
                'dusun'               => $payloadPerumahan['dusun'] ?? $kkData['dusun'] ?? '',
                'kode_pos'            => $payloadPerumahan['kode_pos'] ?? $kkData['kode_pos'] ?? '',
                'is_alamat_sesuai_kk' => $payloadPerumahan['is_alamat_sesuai_kk'] ?? $kkData['is_alamat_sesuai_kk'] ?? 'Ya',
                'alamat'              => $payloadPerumahan['alamat'] ?? $kkData['alamat'] ?? '',
                'rw'                  => $payloadPerumahan['rw'] ?? $rtData['rw'] ?? '',
                'rt'                  => $payloadPerumahan['rt'] ?? $rtData['rt'] ?? '',
                'status_kepemilikan'  => $payloadPerumahan['status_kepemilikan'] ?? ($payloadPerumahan['kondisi']['status_kepemilikan'] ?? null) ?? $statusKepemilikan,
                'kategori_adat'       => $payloadPerumahan['kategori_adat'] ?? '',
                'nama_suku'           => $payloadPerumahan['nama_suku'] ?? '',
                'wilayah_nama'        => $wilayahNama,
                'kondisi'             => $payload['perumahan']['kondisi'],
                'sanitasi'            => $payload['perumahan']['sanitasi'],
            ];

            $anggota = $db->table('dtsen_usulan_art')->where('dtsen_usulan_id', $usulan['id'])->where('deleted_at', null)->get()->getResultArray();

            return view('dtsen/pembaruan/detail', [
                'title' => 'Detail Pembaruan Keluarga',
                'namaApp' => nameApp(),
                'user' => session()->get(),
                'kkData' => $kkData,
                'rtData' => $rtData,
                'perumahan' => $perumahan,
                'anggota' => $anggota,
                'payload' => $payload,
                'usulan' => $usulan,
                'id_kk' => $usulan['dtsen_kk_id'] ?? $kkData['id_kk'],
                'sumber' => 'usulan',
                'kategori_desil' => $kategoriDesil,
                'is_submitted_ready' => $is_submitted_ready
            ]);
        } catch (\Throwable $e) {
            return view('errors/html/error_general', ['message' => 'Gagal memuat detail keluarga: ' . $e->getMessage()]);
        }
    }

    /**
     * 💾 Simpan data keluarga (tab Perumahan) 
     * - Menangkap Wilayah, GeoTag, dan Data BPS
     */
    public function saveKeluarga()
    {
        $post = $this->request->getPost();
        $session = session();
        $userId = $session->get('id_user') ?? $session->get('user_id') ?? $session->get('id') ?? 0;
        $mode = $post['sumber'] ?? 'utama';

        try {
            $idKk = $post['id_kk'] ?? null;

            if (empty($idKk) && $mode === 'baru') {
                $kodeDesa = $post['village'] ?? $session->get('kode_desa') ?? null;
                $rw = trim($post['rw'] ?? '');
                $rt = trim($post['rt'] ?? '');

                $dataRT = [
                    'kode_desa'         => $kodeDesa,
                    'rw'                => $rw,
                    'rt'                => $rt,
                    'alamat'            => trim($post['alamat'] ?? ''),
                    'latitude'          => trim($post['latitude'] ?? ''),
                    'longitude'         => trim($post['longitude'] ?? ''),
                    'source_name'       => 'saveKeluarga_baru',
                    'created_by'        => $userId,
                    'created_at'        => date('Y-m-d H:i:s')
                ];
                $this->db->table('dtsen_rt')->insert($dataRT);
                $idRt = $this->db->insertID();

                $dataKK = [
                    'id_rt'                    => $idRt,
                    'no_kk'                    => trim($post['no_kk'] ?? ''),
                    'kepala_keluarga'          => trim($post['kepala_keluarga'] ?? ''),
                    'nik_kepala_keluarga'      => trim($post['nik_kepala_keluarga'] ?? ''),
                    'jumlah_anggota'           => trim($post['jumlah_anggota'] ?? ''),
                    'nama_jalan'               => trim($post['nama_jalan'] ?? ''),
                    'nomor_rumah'              => trim($post['nomor_rumah'] ?? ''),
                    'dusun'                    => trim($post['dusun'] ?? ''),
                    'kode_pos'                 => trim($post['kode_pos'] ?? ''),
                    'is_alamat_sesuai_kk'      => trim($post['is_alamat_sesuai_kk'] ?? 'Ya'),
                    'alamat'                   => trim($post['alamat'] ?? ''),
                    'created_by'               => $userId,
                    'created_at'               => date('Y-m-d H:i:s'),
                ];
                $this->db->table('dtsen_kk')->insert($dataKK);
                $idKk = $this->db->insertID();

                $payloadBaru = [
                    'perumahan' => array_merge($dataKK, [
                        'wilayah' => [
                            'provinsi'  => $post['provinsi'] ?? '',
                            'kabupaten' => $post['regency'] ?? '',
                            'kecamatan' => $post['district'] ?? '',
                            'desa'      => $post['village'] ?? ''
                        ]
                    ]),
                    'geo' => ['lat' => $post['latitude'] ?? '', 'lng' => $post['longitude'] ?? '']
                ];

                $this->db->table('dtsen_usulan')->insert([
                    'usulan_no'    => 'PDK-' . date('ymdHis'),
                    'jenis'        => 'keluarga_baru',
                    'status'       => 'draft',
                    'dtsen_kk_id'  => $idKk,
                    'no_kk_target' => $dataKK['no_kk'],
                    'created_by'   => $userId,
                    'payload'      => json_encode($payloadBaru, JSON_UNESCAPED_UNICODE),
                    'created_at'   => date('Y-m-d H:i:s')
                ]);

                return $this->response->setJSON(['status' => 'success', 'message' => 'Keluarga baru berhasil dibuat.', 'id_kk' => $idKk]);
            }

            // 🟢 MODE PEMBARUAN / DRAFT
            $kkData = $this->db->table('dtsen_kk')->where('id_kk', $idKk)->get()->getRowArray();
            if (!$kkData) throw new \Exception('Data KK tidak ditemukan.');

            $rtData = $this->db->table('dtsen_rt')->where('id_rt', $kkData['id_rt'])->get()->getRowArray() ?? [];

            $perumahanBaru = [
                'no_kk'               => $post['no_kk'] ?? $kkData['no_kk'],
                'kepala_keluarga'     => $post['kepala_keluarga'] ?? $kkData['kepala_keluarga'],
                'nik_kepala_keluarga' => $post['nik_kepala_keluarga'] ?? $kkData['nik_kepala_keluarga'] ?? '',
                'jumlah_anggota'      => $post['jumlah_anggota'] ?? $kkData['jumlah_anggota'] ?? '',
                'nama_jalan'          => $post['nama_jalan'] ?? $kkData['nama_jalan'] ?? '',
                'nomor_rumah'         => $post['nomor_rumah'] ?? $kkData['nomor_rumah'] ?? '',
                'dusun'               => $post['dusun'] ?? $kkData['dusun'] ?? '',
                'kode_pos'            => $post['kode_pos'] ?? $kkData['kode_pos'] ?? '',
                'is_alamat_sesuai_kk' => $post['is_alamat_sesuai_kk'] ?? $kkData['is_alamat_sesuai_kk'] ?? 'Ya',
                'alamat'              => $post['alamat'] ?? $rtData['alamat'] ?? $kkData['alamat'] ?? '',
                'rw'                  => $post['rw'] ?? $rtData['rw'] ?? '',
                'rt'                  => $post['rt'] ?? $rtData['rt'] ?? ''
            ];

            $wilayahBaru = [
                'provinsi'  => $post['provinsi'] ?? '',
                'kabupaten' => $post['regency'] ?? '',
                'kecamatan' => $post['district'] ?? '',
                'desa'      => $post['village'] ?? ''
            ];

            $usulan = $this->db->table('dtsen_usulan')->where('dtsen_kk_id', $idKk)->whereIn('status', ['draft', 'submitted'])->orderBy('id', 'DESC')->get()->getRowArray();

            if ($usulan) {
                $payloadLama = json_decode($usulan['payload'] ?? '{}', true);
                if (!is_array($payloadLama)) $payloadLama = [];
                $payloadLama['perumahan'] = $payloadLama['perumahan'] ?? [];

                $payloadGabungan = array_merge($payloadLama['perumahan'], $perumahanBaru);
                $payloadGabungan['wilayah']  = $wilayahBaru;
                $payloadGabungan['kondisi']  = $payloadLama['perumahan']['kondisi'] ?? [];
                $payloadGabungan['sanitasi'] = $payloadLama['perumahan']['sanitasi'] ?? [];

                $payloadLama['perumahan'] = $payloadGabungan;
                $payloadLama['geo'] = ['lat' => $post['latitude'] ?? $payloadLama['geo']['lat'] ?? '', 'lng' => $post['longitude'] ?? $payloadLama['geo']['lng'] ?? ''];

                $this->db->table('dtsen_usulan')->where('id', $usulan['id'])->update([
                    'payload'    => json_encode($payloadLama, JSON_UNESCAPED_UNICODE),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $userId
                ]);
            } else {
                $payloadBaru = [
                    'perumahan' => array_merge($perumahanBaru, [
                        'wilayah' => $wilayahBaru,
                        'kondisi' => [
                            'luas_lantai'    => $rtData['luas_lantai'] ?? '',
                            'jenis_lantai'   => $rtData['jenis_lantai'] ?? '',
                            'kondisi_lantai' => $rtData['kondisi_lantai'] ?? '',
                            'jenis_dinding'  => $rtData['jenis_dinding'] ?? '',
                            'kondisi_dinding' => $rtData['kondisi_dinding'] ?? '',
                            'jenis_atap'     => $rtData['kondisi_atap'] ?? '',
                            'kondisi_atap'   => $rtData['kondisi_atap'] ?? '',
                            'bahan_bakar'    => $rtData['bahan_bakar'] ?? '',
                            'sumber_air'     => $rtData['sumber_air'] ?? '',
                            'sumber_listrik' => $rtData['sumber_listrik'] ?? ''
                        ],
                        'sanitasi' => ['pembuangan_tinja' => $rtData['sanitasi'] ?? '']
                    ]),
                    'geo' => ['lat' => $post['latitude'] ?? $rtData['latitude'] ?? '', 'lng' => $post['longitude'] ?? $rtData['longitude'] ?? '']
                ];

                $this->db->table('dtsen_usulan')->insert([
                    'usulan_no'    => 'PDK-' . date('ymdHis'),
                    'jenis'        => 'pembaruan',
                    'status'       => 'draft',
                    'dtsen_kk_id'  => $idKk,
                    'no_kk_target' => $kkData['no_kk'],
                    'created_by'   => $userId,
                    'payload'      => json_encode($payloadBaru, JSON_UNESCAPED_UNICODE),
                    'created_at'   => date('Y-m-d H:i:s')
                ]);
            }

            // 🚀 SINKRONISASI PECAH ALAMAT (DIPINDAHKAN KE SINI KARENA TAB_KELUARGA YANG MENGIRIM RT/RW)
            if ($kkData['id_rt'] && ($usulan['status'] ?? '') !== 'applied') {
                $rtBaru     = trim($post['rt'] ?? '');
                $rwBaru     = trim($post['rw'] ?? '');
                $desaBaru   = trim($post['village'] ?? '');
                $alamatBaru = trim($post['alamat'] ?? '');

                if ($rtBaru !== $rtData['rt'] || $rwBaru !== $rtData['rw'] || ($desaBaru !== '' && $desaBaru !== $rtData['kode_desa']) || $alamatBaru !== ($rtData['alamat'] ?? '')) {
                    $jumlahPenghuni = $this->db->table('dtsen_kk')->where('id_rt', $kkData['id_rt'])->countAllResults();
                    $isPindahWilayah = ($rtBaru !== $rtData['rt'] || $rwBaru !== $rtData['rw'] || ($desaBaru !== '' && $desaBaru !== $rtData['kode_desa']));

                    if ($jumlahPenghuni > 1 && $isPindahWilayah) {
                        $this->db->table('dtsen_rt')->insert([
                            'rw'          => $rwBaru,
                            'rt' => $rtBaru,
                            'kode_desa'   => !empty($post['village']) ? $post['village'] : ($rtData['kode_desa'] ?? null),
                            'alamat'      => $alamatBaru,
                            'created_at'  => date('Y-m-d H:i:s'),
                            'created_by'  => $userId,
                            'source_name' => 'pecah_alamat_draft'
                        ]);
                        $idRtBaru = $this->db->insertID();
                        $this->db->table('dtsen_kk')->where('id_kk', $idKk)->update(['id_rt' => $idRtBaru]);
                    } else {
                        $this->db->table('dtsen_rt')->where('id_rt', $kkData['id_rt'])->update([
                            'rw'         => $rwBaru,
                            'rt' => $rtBaru,
                            'kode_desa'  => !empty($post['village']) ? $post['village'] : ($rtData['kode_desa'] ?? null),
                            'alamat'     => $alamatBaru,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data keluarga berhasil disimpan.', 'id_kk' => $idKk]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 🏠 Simpan data Tab “Keterangan Perumahan”
     */
    public function saveRumah()
    {
        try {
            $post = $this->request->getPost();
            $user = session()->get();

            $usulanId = $post['dtsen_usulan_id'] ?? null;
            if (!$usulanId) return $this->response->setJSON(['status' => 'error', 'message' => 'ID usulan tidak ditemukan.']);

            $this->db->transBegin();

            $usulanRow = $this->db->table('dtsen_usulan')->select('id, payload, status, dtsen_kk_id')->where('id', $usulanId)->get()->getRowArray();
            if (!$usulanRow) throw new \Exception('Data usulan tidak ditemukan.');

            $payloadLama = json_decode($usulanRow['payload'] ?? '{}', true);
            if (!is_array($payloadLama)) $payloadLama = [];
            $gabungan = $payloadLama['perumahan'] ?? [];

            // 🚀 FUNGSI HELPER UNTUK ARRAY (No KK, Pelanggan, Meter, Daya)
            $getArray = function ($key) use ($post) {
                if (!isset($post[$key])) return '';
                return is_array($post[$key]) ? implode(',', array_filter($post[$key], fn($v) => trim($v) !== '')) : trim($post[$key]);
            };

            // 🚀 UPDATE NODE KONDISI FISIK
            $gabungan['kondisi'] = array_merge($gabungan['kondisi'] ?? [], [
                'jenis_bangunan'           => $post['jenis_bangunan'] ?? '',
                'is_tinggal_bersama'       => $post['is_tinggal_bersama'] ?? '',
                'jumlah_kk_dalam_rumah'    => $post['jumlah_kk_dalam_rumah'] ?? '',
                'no_kk_lainnya'            => $getArray('no_kk_lainnya'),
                'jumlah_orang_dalam_rumah' => $post['jumlah_orang_dalam_rumah'] ?? '',
                'perkiraan_harga_sewa'     => $post['perkiraan_harga_sewa'] ?? '',
                'status_kepemilikan'       => $post['status_kepemilikan'] ?? '',
                'bukti_kepemilikan'        => $post['bukti_kepemilikan'] ?? '',
                'luas_lantai'              => (float)($post['luas_lantai'] ?? 0),
                'jenis_lantai'             => $post['jenis_lantai'] ?? '',
                'kondisi_lantai'           => $post['kondisi_lantai'] ?? '',
                'jenis_dinding'            => $post['jenis_dinding'] ?? '',
                'kondisi_dinding'          => $post['kondisi_dinding'] ?? '',
                'jenis_atap'               => $post['jenis_atap'] ?? '',
                'kondisi_atap'             => $post['kondisi_atap'] ?? '',
                'bahan_bakar'              => $post['bahan_bakar'] ?? '',
                'sumber_air'               => $post['sumber_air'] ?? '',
                'sumber_listrik'           => $post['sumber_listrik'] ?? '',
                'jumlah_meteran_listrik'   => $post['jumlah_meteran_listrik'] ?? '',
                'nomor_pelanggan'          => $getArray('nomor_pelanggan'),
                'nomor_meter'              => $getArray('nomor_meter'),
                'daya_listrik'             => $getArray('daya_listrik')
            ]);

            // 🚀 UPDATE NODE SANITASI
            $gabungan['sanitasi'] = array_merge($gabungan['sanitasi'] ?? [], [
                'fasilitas_bab'       => $post['fasilitas_bab'] ?? '',
                'jenis_kloset'        => $post['jenis_kloset'] ?? '',
                'jarak_air_ke_limbah' => $post['jarak_air_ke_limbah'] ?? '',
                'pembuangan_tinja'    => $post['pembuangan_tinja'] ?? ''
            ]);

            // 🚀 UPDATE NODE SOSIAL EKONOMI (Finansial)
            $payloadLama['sosial_ekonomi'] = [
                'pengeluaran_listrik'           => $post['pengeluaran_listrik'] ?? '',
                'pengeluaran_pulsa'             => $post['pengeluaran_pulsa'] ?? '',
                'pengeluaran_internet'          => $post['pengeluaran_internet'] ?? '',
                'pengeluaran_makan_mingguan'    => $post['pengeluaran_makan_mingguan'] ?? '',
                'pengeluaran_non_makan_bulanan' => $post['pengeluaran_non_makan_bulanan'] ?? '',
                'pengeluaran_non_makan_tahunan' => $post['pengeluaran_non_makan_tahunan'] ?? '',
                'pendapatan_gaji'               => $post['pendapatan_gaji'] ?? '',
                'pendapatan_usaha'              => $post['pendapatan_usaha'] ?? '',
                'pendapatan_lainnya'            => $post['pendapatan_lainnya'] ?? ''
            ];

            $payloadLama['perumahan'] = $gabungan;

            $this->db->table('dtsen_usulan')->where('id', $usulanId)->update([
                'payload'    => json_encode($payloadLama, JSON_UNESCAPED_UNICODE),
                'updated_at' => date('Y-m-d H:i:s'),
                'summary'    => 'Data rumah diperbarui oleh ' . ($user['nama'] ?? 'Sistem')
            ]);

            $this->db->table('dtsen_kk')->where('id_kk', $usulanRow['dtsen_kk_id'])->update(['is_recovery_needed' => 0]);
            $this->db->transCommit();

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data rumah & finansial berhasil disimpan.']);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    /**
     * 🧱 Simpan Data Kepemilikan Aset
     */
    public function saveAset()
    {
        try {
            $request  = service('request');
            $usulanId = $request->getPost('dtsen_usulan_id');
            $userId   = session()->get('id_user') ?? session()->get('id') ?? 0;

            if (empty($usulanId)) return $this->response->setJSON(['status' => 'error', 'message' => 'ID usulan tidak ditemukan.']);

            $usulan = $this->db->table('dtsen_usulan')->select('id, payload')->where('id', $usulanId)->get()->getRowArray();
            if (!$usulan) return $this->response->setJSON(['status' => 'error', 'message' => 'Data usulan tidak ditemukan.']);

            $payloadLama = json_decode($usulan['payload'] ?? '{}', true);
            if (!is_array($payloadLama)) $payloadLama = [];
            $payloadLama['aset'] = $payloadLama['aset'] ?? [];

            // 🚀 ASET BERGERAK DAN TERNAK
            $asetBaru = [
                'tabung_gas_3kg'     => $request->getPost('tabung_gas_3kg') ?? 0,
                'tabung_gas'         => $request->getPost('tabung_gas') ?? 0,
                'kulkas'             => $request->getPost('kulkas') ?? 0,
                'ac'                 => $request->getPost('ac') ?? 0,
                'emas'               => $request->getPost('emas') ?? 0,
                'laptop'             => $request->getPost('laptop') ?? 0,
                'sepeda_motor'       => $request->getPost('sepeda_motor') ?? 0,
                'nilai_sepeda_motor' => str_replace('.', '', $request->getPost('nilai_sepeda_motor') ?? ''), // 🚀 TANGKAP NILAI MOTOR
                'mobil'              => $request->getPost('mobil') ?? 0,
                'nilai_mobil'        => str_replace('.', '', $request->getPost('nilai_mobil') ?? ''), // 🚀 TANGKAP NILAI MOBIL
                'water_heater'       => $request->getPost('water_heater') ?? 0,
                'telepon_rumah'      => $request->getPost('telepon_rumah') ?? 0,
                'tv_lcd'             => $request->getPost('tv_lcd') ?? 0,
                'sepeda'             => $request->getPost('sepeda') ?? 0,
                'perahu'             => $request->getPost('perahu') ?? 0,
                'smartphone'         => $request->getPost('smartphone') ?? 0,
                'sapi'               => $request->getPost('sapi') ?? 0,
                'kerbau'             => $request->getPost('kerbau') ?? 0,
                'kuda'               => $request->getPost('kuda') ?? 0,
                'kambing'            => $request->getPost('kambing') ?? 0,
                'babi'               => $request->getPost('babi') ?? 0,
                // === ASET TIDAK BERGERAK ===
                'luas_sawah'         => $request->getPost('luas_sawah') ?? '',
                'nilai_sawah'        => str_replace('.', '', $request->getPost('nilai_sawah') ?? ''), // Simpan integer bersih
                'memiliki_lahan'     => $request->getPost('memiliki_lahan') ?? '',
                'rumah_lain'         => $request->getPost('rumah_lain') ?? '',
                'nilai_rumah_lain'   => str_replace('.', '', $request->getPost('nilai_rumah_lain') ?? '') // Simpan integer bersih
            ];

            $payloadLama['aset'] = array_merge($payloadLama['aset'], $asetBaru);

            $this->db->table('dtsen_usulan')->where('id', $usulanId)->update([
                'payload'    => json_encode($payloadLama, JSON_UNESCAPED_UNICODE),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $userId
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data aset berhasil disimpan.']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 📸 Simpan Foto
     */
    public function saveFoto()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        try {
            $session = session();
            $userId  = $session->get('id_user') ?? $session->get('id') ?? 0;
            $usulanId = $this->request->getPost('dtsen_usulan_id');

            if (empty($usulanId)) return $this->response->setJSON(['status' => 'error', 'message' => 'ID usulan tidak ditemukan.']);

            // 🔍 Ambil data usulan
            $usulan = $this->db->table('dtsen_usulan')->select('id, payload, dtsen_kk_id')->where('id', $usulanId)->get()->getRowArray();
            $payloadLama = json_decode($usulan['payload'] ?? '{}', true);
            $payloadLama['foto'] = $payloadLama['foto'] ?? [];
            $geoGabungan = $payloadLama['geo'] ?? [];

            // 📁 Siapkan direktori upload
            $uploadBase = FCPATH . 'data/usulan/';
            $dirs = [
                'foto_identitas'   => $uploadBase . 'foto_identitas/',
                'foto_rumah'       => $uploadBase . 'foto_rumah/',
                'foto_rumah_dalam' => $uploadBase . 'foto_rumah_dalam/',
                'foto_kamar_mandi' => $uploadBase . 'foto_kamar_mandi/' // 🚀 NEW FOLDER
            ];
            foreach ($dirs as $dir) if (!is_dir($dir)) mkdir($dir, 0777, true);

            // ==========================================
            // SIAPKAN DATA UNTUK WATERMARK
            // ==========================================
            $noKKManual = trim((string)($this->request->getPost('no_kk') ?? ''));
            if ($noKKManual === '') {
                $noKK = $this->db->table('dtsen_kk')->select('no_kk')->where('id_kk', $usulan['dtsen_kk_id'])->get()->getRow('no_kk') ?? 'unknown';
            } else {
                $noKK = $noKKManual;
            }

            $kepalaKeluarga = trim((string)($this->request->getPost('kepala_keluarga') ?? ''));
            if ($kepalaKeluarga === '') {
                $kepalaKeluarga = $payloadLama['perumahan']['kepala_keluarga'] ?? 'Tidak diketahui';
            }

            $lat = trim((string)($this->request->getPost('latitude') ?? ''));
            $lng = trim((string)($this->request->getPost('longitude') ?? ''));

            // Normalisasi lat/lng untuk nama file
            $latForFile = str_replace([' ', ','], ['_', '_'], $lat !== '' ? $lat : '0');
            $lngForFile = str_replace([' ', ','], ['_', '_'], $lng !== '' ? $lng : '0');

            // Nama Wilayah untuk Watermark
            $kodeDesaFull = $session->get('kode_desa');
            $WilayahModel = new \App\Models\WilayahModel();
            $desaRow = $WilayahModel
                ->select("tb_villages.name AS desa, tb_districts.name AS kecamatan, tb_regencies.name AS kabupaten, tb_provinces.name AS provinsi")
                ->join('tb_districts', 'tb_districts.id = tb_villages.district_id', 'left')
                ->join('tb_regencies', 'tb_regencies.id = tb_villages.regency_id', 'left')
                ->join('tb_provinces', 'tb_provinces.id = tb_villages.province_id', 'left')
                ->where('tb_villages.id', $kodeDesaFull)
                ->get()
                ->getRowArray();

            $namaDesa      = strtoupper($desaRow['desa'] ?? '-');
            $namaKecamatan = strtoupper($desaRow['kecamatan'] ?? '-');
            $namaKabupaten = strtoupper($desaRow['kabupaten'] ?? '-');
            $namaProvinsi  = strtoupper($desaRow['provinsi'] ?? '-');
            $wilayahFull   = "Desa {$namaDesa}, Kec. {$namaKecamatan}, Kab. {$namaKabupaten}, Prov. {$namaProvinsi}";

            // ==========================================
            // 🚀 MAPPING FOTO
            // ==========================================
            $fotoFields = [
                'foto_ktp'         => ['path' => 'foto_identitas/',   'key' => 'ktp_kk'],
                'foto_depan'       => ['path' => 'foto_rumah/',       'key' => 'depan'],
                'foto_dalam'       => ['path' => 'foto_rumah_dalam/', 'key' => 'dalam'],
                'foto_kamar_mandi' => ['path' => 'foto_kamar_mandi/', 'key' => 'kamar_mandi'] // 🚀 NEW
            ];

            $fotoGabungan = $payloadLama['foto'];

            foreach ($fotoFields as $field => $opt) {
                $file = $this->request->getFile($field);
                if ($file && $file->isValid() && !$file->hasMoved()) {

                    $timestamp = time();
                    $newName = 'sinden_' . preg_replace('/\s+/', '', $noKK) . '_' . $field . '_' . $latForFile . '_' . $lngForFile . '_' . $timestamp . '.' . $file->getExtension();
                    $finalPath = $uploadBase . $opt['path'] . $newName;

                    // Pindah file ke folder tujuan
                    $file->move($uploadBase . $opt['path'], $newName, true);

                    // 🚀 WATERMARK & KOMPRESI (Berlaku untuk semua KECUALI foto KTP)
                    if ($field !== 'foto_ktp') {
                        $latText = $lat !== '' ? $lat : ($geoGabungan['lat'] ?? '-');
                        $lngText = $lng !== '' ? $lng : ($geoGabungan['lng'] ?? '-');

                        // Panggil helper watermark
                        applyWatermarkPremium($finalPath, [
                            'no_kk'     => (string) $noKK,
                            'kepala'    => (string) $kepalaKeluarga,
                            'petugas'   => (string) ($session->get('fullname') ?? 'Petugas'),
                            'tanggal'   => date('d F Y'),
                            'latitude'  => (string) $latText,
                            'longitude' => (string) $lngText,
                            'wilayah'   => (string) $wilayahFull
                        ]);

                        // Kompresi ukuran file max 500kb
                        recompressImageToTarget($finalPath, 500);
                    }

                    // Simpan path ke payload JSON
                    $fotoGabungan[$opt['key']] = 'data/usulan/' . $opt['path'] . $newName;
                }
            }

            // ==========================================
            // SIMPAN GEOTAG & UPDATE DATABASE
            // ==========================================
            $geoGabungan['lat'] = $lat !== '' ? $lat : ($geoGabungan['lat'] ?? null);
            $geoGabungan['lng'] = $lng !== '' ? $lng : ($geoGabungan['lng'] ?? null);

            $payloadLama['foto'] = $fotoGabungan;
            $payloadLama['geo']  = $geoGabungan;

            $this->db->table('dtsen_usulan')->where('id', $usulanId)->update([
                'payload'    => json_encode($payloadLama, JSON_UNESCAPED_UNICODE),
                'updated_by' => $userId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data foto berhasil disimpan dan diberi watermark!']);
        } catch (\Throwable $e) {
            log_message('error', '❌ saveFoto() error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * 💾 Simpan data anggota individu
     */
    public function saveAnggota()
    {
        $request = $this->request;
        $db = \Config\Database::connect();
        $session = session();

        try {
            $post = $request->getPost();
            $userId = $session->get('id_user') ?? $session->get('id') ?? 'system';

            if (empty($post['nik']) || empty($post['nama'])) return $this->response->setJSON(['status' => 'error', 'message' => 'NIK dan Nama wajib diisi.']);

            $idKk = $post['id_kk'] ?? null;
            $usulan = $db->table('dtsen_usulan')->where('dtsen_kk_id', $idKk)->whereIn('status', ['draft', 'submitted'])->orderBy('id', 'DESC')->get()->getRowArray();

            if (!$usulan) {
                $db->table('dtsen_usulan')->insert([
                    'usulan_no'   => 'ART-' . date('ymdHis'),
                    'jenis' => 'pembaruan',
                    'status' => 'draft',
                    'dtsen_kk_id' => $idKk,
                    'created_by' => $userId,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $usulan_id = $db->insertID();
            } else {
                $usulan_id = $usulan['id'];
            }

            // 🔹 Payload Individu (Usaha Dihapus, no_hp dan rekening ditambahkan)
            $payloadIndividu = [
                'identitas' => [
                    'status_keberadaan' => $post['status_keberadaan'] ?? null,
                    'individu_no_kk' => $post['individu_no_kk'] ?? null,
                    'nik' => $post['nik'] ?? null,
                    'nama' => $post['nama'] ?? null,
                    'no_hp' => $post['no_hp'] ?? null, // 🚀 NEW
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
                    'rekening_aktif' => $post['rekening_aktif'] ?? null // 🚀 NEW
                ],
                'kesehatan' => [
                    'status_hamil' => $post['status_hamil'] ?? null,
                    'disabilitas' => $post['disabilitas'] ?? [],
                    'penyakit_kronis' => $post['penyakit_kronis'] ?? null,
                ],
            ];

            $existingArt = $db->table('dtsen_usulan_art ua')
                ->select('ua.id')->join('dtsen_usulan u', 'u.id = ua.dtsen_usulan_id', 'left')
                ->where('ua.nik', $post['nik'])->where('u.dtsen_kk_id', $idKk)->get()->getRowArray();

            $dataArt = [
                'dtsen_usulan_id' => $usulan_id,
                'nik' => $post['nik'],
                'nama' => $post['nama'],
                'hubungan' => $post['hubungan'] ?? null,
                'payload_member' => json_encode($payloadIndividu, JSON_UNESCAPED_UNICODE),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $userId
            ];

            if ($existingArt) {
                $db->table('dtsen_usulan_art')->where('id', $existingArt['id'])->update($dataArt);
            } else {
                $dataArt['created_at'] = date('Y-m-d H:i:s');
                $dataArt['created_by'] = $userId;
                $db->table('dtsen_usulan_art')->insert($dataArt);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data individu berhasil disimpan.']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getAnggotaDetail($id = null)
    {
        try {
            $db = \Config\Database::connect();
            $genModel = new \App\Models\GenModel();

            if (empty($id) || !is_numeric($id)) {
                return $this->response->setJSON([
                    'status' => 'empty',
                    'data' => [
                        'usulan_id' => null,
                        'anggota_prefill' => [],
                        'dropdowns' => [
                            'status_kawin' => $genModel->getDataStatusKawin(),
                            'hubungan' => $genModel->getDataShdk(),
                            'pekerjaan' => $genModel->getPendudukPekerjaan(),
                            'pendidikan' => $genModel->getPendidikan(),
                        ]
                    ]
                ]);
            }

            $usulanArt = $db->table('dtsen_usulan_art')->where('id', $id)->get()->getRowArray();
            $usulan_id = null;
            $anggota_prefill = [];

            if ($usulanArt) {
                $payload = json_decode($usulanArt['payload_member'] ?? '{}', true);
                $anggota_prefill = array_merge(
                    [
                        'id' => $usulanArt['id'],
                        'dtsen_usulan_id' => $usulanArt['dtsen_usulan_id'],
                        'nik' => $usulanArt['nik'],
                        'nama' => $usulanArt['nama'],
                        'hubungan' => $usulanArt['hubungan'],
                    ],
                    [
                        'individu_no_kk' => $payload['identitas']['individu_no_kk'] ?? '',
                        'no_hp' => $payload['identitas']['no_hp'] ?? '', // 🚀 NEW
                        'tempat_lahir' => $payload['identitas']['tempat_lahir'] ?? '',
                        'tanggal_lahir' => $payload['identitas']['tanggal_lahir'] ?? '',
                        'jenis_kelamin' => $payload['identitas']['jenis_kelamin'] ?? '',
                        'status_kawin' => $payload['identitas']['status_kawin'] ?? '',
                        'hubungan_keluarga' => $payload['identitas']['hubungan'] ?? '',
                        'pekerjaan' => $payload['identitas']['pekerjaan'] ?? '',
                        'pendidikan_terakhir' => $payload['identitas']['pendidikan_terakhir'] ?? '',
                        'ibu_kandung' => $payload['identitas']['ibu_kandung'] ?? '',
                        'provinsi' => $payload['identitas']['provinsi'] ?? '',
                        'kabupaten' => $payload['identitas']['kabupaten'] ?? '',
                        'kecamatan' => $payload['identitas']['kecamatan'] ?? '',
                        'desa' => $payload['identitas']['desa'] ?? '',
                        'status_keberadaan' => $payload['identitas']['status_keberadaan'] ?? 'Belum Ditentukan',
                        'partisipasi_sekolah' => $payload['pendidikan']['partisipasi_sekolah'] ?? '',
                        'jenjang_pendidikan'  => $payload['pendidikan']['jenjang_pendidikan'] ?? '',
                        'kelas_tertinggi'     => $payload['pendidikan']['kelas_tertinggi'] ?? '',
                        'ijazah_tertinggi'   => $payload['pendidikan']['ijazah_tertinggi'] ?? '',
                        'bekerja_seminggu' => $payload['tenaga_kerja']['bekerja_seminggu'] ?? '',
                        'lapangan_usaha'     => $payload['tenaga_kerja']['lapangan_usaha'] ?? '',
                        'status_pekerjaan'   => $payload['tenaga_kerja']['status_pekerjaan'] ?? '',
                        'pendapatan'         => $payload['tenaga_kerja']['pendapatan'] ?? '',
                        'rekening_aktif'     => $payload['tenaga_kerja']['rekening_aktif'] ?? '', // 🚀 NEW
                        'keterampilan'       => $payload['tenaga_kerja']['keterampilan'] ?? [],
                        'status_hamil'       => $payload['kesehatan']['status_hamil'] ?? '',
                        'penyakit_kronis'    => $payload['kesehatan']['penyakit_kronis'] ?? '',
                        'disabilitas'        => $payload['kesehatan']['disabilitas'] ?? [],
                    ]
                );
                $usulan_id = $usulanArt['dtsen_usulan_id'];
            } else {
                $art = $db->table('dtsen_art a')->select('a.*, kk.no_kk as individu_no_kk, kk.kepala_keluarga')->join('dtsen_kk kk', 'kk.id_kk = a.id_kk', 'left')->where('a.id_art', $id)->get()->getRowArray();
                if (!$art) return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
                $anggota_prefill = $art;
            }

            $refStatusKawin = $genModel->getDataStatusKawin();
            $refShdk        = $genModel->getDataShdk();
            $refPekerjaan   = $genModel->getPendudukPekerjaan();
            $refPendidikan  = $genModel->getPendidikan();

            return $this->response->setJSON([
                'status'  => 'success',
                'data'    => [
                    'usulan_id' => $usulan_id,
                    'anggota_prefill' => $anggota_prefill,
                    'dropdowns' => ['status_kawin' => $refStatusKawin, 'hubungan' => $refShdk, 'pekerjaan' => $refPekerjaan, 'pendidikan' => $refPendidikan]
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * ♻️ Simpan Seluruh Data ke Tabel Utama (Apply)
     */
    public function apply()
    {
        $this->db->transBegin();
        try {
            $usulan_id = $this->request->getPost('usulan_id');
            $userId    = session()->get('id') ?? 'system';

            $usulan = $this->db->table('dtsen_usulan')->where('id', $usulan_id)->get()->getRowArray();
            if (!$usulan) throw new \Exception('Data usulan tidak ditemukan.');

            $payload = json_decode($usulan['payload'] ?? '{}', true);
            $idKk = $usulan['dtsen_kk_id'] ?? null;
            if (!$idKk) throw new \Exception('ID KK tidak ditemukan.');

            // 🏠 1. Update dtsen_rt
            $geo      = $payload['geo'] ?? [];
            $foto     = $payload['foto'] ?? [];
            $rumah    = $payload['perumahan'] ?? [];
            $kondisi  = $rumah['kondisi'] ?? [];
            $sanitasi = $rumah['sanitasi'] ?? [];
            $wilayah  = $rumah['wilayah'] ?? [];

            $idRtSekarang = $this->db->table('dtsen_kk')->select('id_rt')->where('id_kk', $idKk)->get()->getRow('id_rt');
            if ($idRtSekarang) {
                $rtLama = $this->db->table('dtsen_rt')->where('id_rt', $idRtSekarang)->get()->getRowArray();
                $rtUpdate = [
                    'kode_desa'                => $wilayah['desa'] ?? $rtLama['kode_desa'],
                    'rt'                       => $rumah['rt'] ?? $rtLama['rt'],
                    'rw'                       => $rumah['rw'] ?? $rtLama['rw'],
                    'alamat'                   => $rumah['alamat'] ?? $rtLama['alamat'],
                    'kepemilikan_rumah'        => $kondisi['status_kepemilikan'] ?? $rtLama['kepemilikan_rumah'],
                    'bukti_kepemilikan'        => $kondisi['bukti_kepemilikan'] ?? $rtLama['bukti_kepemilikan'] ?? null,
                    'jenis_bangunan'           => $kondisi['jenis_bangunan'] ?? $rtLama['jenis_bangunan'] ?? null,
                    'is_tinggal_bersama'       => $kondisi['is_tinggal_bersama'] ?? $rtLama['is_tinggal_bersama'] ?? null,
                    'jumlah_kk_dalam_rumah'    => $kondisi['jumlah_kk_dalam_rumah'] ?? $rtLama['jumlah_kk_dalam_rumah'] ?? null,
                    'no_kk_lainnya'            => $kondisi['no_kk_lainnya'] ?? $rtLama['no_kk_lainnya'] ?? null,
                    'jumlah_orang_dalam_rumah' => $kondisi['jumlah_orang_dalam_rumah'] ?? $rtLama['jumlah_orang_dalam_rumah'] ?? null,
                    'perkiraan_harga_sewa'     => $kondisi['perkiraan_harga_sewa'] ?? $rtLama['perkiraan_harga_sewa'] ?? null,
                    'luas_lantai'              => $kondisi['luas_lantai'] ?? $rtLama['luas_lantai'],
                    'jenis_lantai'             => $kondisi['jenis_lantai'] ?? $rtLama['jenis_lantai'],
                    'kondisi_lantai'           => $kondisi['kondisi_lantai'] ?? $rtLama['kondisi_lantai'] ?? null,
                    'jenis_dinding'            => $kondisi['jenis_dinding'] ?? $rtLama['jenis_dinding'],
                    'kondisi_dinding'          => $kondisi['kondisi_dinding'] ?? $rtLama['kondisi_dinding'] ?? null,
                    'jenis_atap'               => $kondisi['jenis_atap'] ?? $rtLama['jenis_atap'] ?? null,
                    'kondisi_atap'             => $kondisi['kondisi_atap'] ?? $rtLama['kondisi_atap'] ?? null,
                    'bahan_bakar'              => $kondisi['bahan_bakar'] ?? $rtLama['bahan_bakar'],
                    'sumber_air'               => $kondisi['sumber_air'] ?? $rtLama['sumber_air'],
                    'sumber_listrik'           => $kondisi['sumber_listrik'] ?? $rtLama['sumber_listrik'],
                    'jumlah_meteran_listrik'   => $kondisi['jumlah_meteran_listrik'] ?? $rtLama['jumlah_meteran_listrik'] ?? null,
                    'nomor_pelanggan'          => $kondisi['nomor_pelanggan'] ?? $rtLama['nomor_pelanggan'] ?? null,
                    'nomor_meter'              => $kondisi['nomor_meter'] ?? $rtLama['nomor_meter'] ?? null,
                    'daya_listrik'             => $kondisi['daya_listrik'] ?? $rtLama['daya_listrik'] ?? null,
                    'sanitasi'                 => $sanitasi['pembuangan_tinja'] ?? $rtLama['sanitasi'],
                    'foto_rumah'               => $foto['depan'] ?? $rtLama['foto_rumah'],
                    'foto_rumah_dalam'         => $foto['dalam'] ?? $rtLama['foto_rumah_dalam'],
                    'foto_kamar_mandi'         => $foto['kamar_mandi'] ?? $rtLama['foto_kamar_mandi'] ?? null,
                    'latitude'                 => $geo['lat'] ?? $rtLama['latitude'],
                    'longitude'                => $geo['lng'] ?? $rtLama['longitude'],
                    'updated_at'               => date('Y-m-d H:i:s')
                ];
                $this->db->table('dtsen_rt')->where('id_rt', $idRtSekarang)->update($rtUpdate);
            }

            // 👪 2. Update dtsen_kk
            $kkLama = $this->db->table('dtsen_kk')->where('id_kk', $idKk)->get()->getRowArray();
            $kkUpdate = [
                'is_recovery_needed'       => 0,
                'no_kk'                    => $rumah['no_kk'] ?? $kkLama['no_kk'],
                'kepala_keluarga'          => $rumah['kepala_keluarga'] ?? $kkLama['kepala_keluarga'],
                'nik_kepala_keluarga'      => $rumah['nik_kepala_keluarga'] ?? null,
                'jumlah_anggota'           => $rumah['jumlah_anggota'] ?? null,
                'alamat'                   => $rumah['alamat'] ?? $kkLama['alamat'],
                'nama_jalan'               => $rumah['nama_jalan'] ?? null,
                'nomor_rumah'              => $rumah['nomor_rumah'] ?? null,
                'dusun'                    => $rumah['dusun'] ?? null,
                'kode_pos'                 => $rumah['kode_pos'] ?? null,
                'is_alamat_sesuai_kk'      => $rumah['is_alamat_sesuai_kk'] ?? null,
                'status_kepemilikan_rumah' => $rumah['status_kepemilikan'] ?? $kkLama['status_kepemilikan_rumah'],
                'kategori_adat'            => $rumah['kategori_adat'] ?? $kkLama['kategori_adat'],
                'nama_suku'                => $rumah['nama_suku'] ?? $kkLama['nama_suku'],
                'foto_kk'                  => $foto['ktp_kk'] ?? $kkLama['foto_kk'],
                'foto_rumah'               => $foto['depan'] ?? $kkLama['foto_rumah'],
                'foto_rumah_dalam'         => $foto['dalam'] ?? $kkLama['foto_rumah_dalam'],
                'updated_at'               => date('Y-m-d H:i:s')
            ];
            $this->db->table('dtsen_kk')->where('id_kk', $idKk)->update($kkUpdate);

            // 👤 3. Sinkronisasi dtsen_art
            $anggotaUsulan = $this->db->table('dtsen_usulan_art')->where('dtsen_usulan_id', $usulan_id)->get()->getResultArray();
            if (!empty($anggotaUsulan)) {
                $this->db->table('dtsen_art')->where('id_kk', $idKk)->where('deleted_at', null)->update(['deleted_at' => date('Y-m-d H:i:s'), 'delete_reason' => 'Ditimpa usulan pembaruan ID ' . $usulan_id]);
                foreach ($anggotaUsulan as $art) {
                    $payloadMember = json_decode($art['payload_member'] ?? '{}', true);
                    $identitas     = $payloadMember['identitas'] ?? [];
                    $tenagaKerja   = $payloadMember['tenaga_kerja'] ?? [];

                    $this->db->table('dtsen_art')->insert([
                        'id_kk'               => $idKk,
                        'nik'                 => $identitas['nik'] ?? $art['nik'] ?? null,
                        'nama'                => $identitas['nama'] ?? $art['nama'] ?? null,
                        'no_hp'               => $identitas['no_hp'] ?? null,
                        'rekening_aktif'      => $tenagaKerja['rekening_aktif'] ?? null,
                        'hubungan_keluarga'   => $identitas['hubungan'] ?? null,
                        'jenis_kelamin'       => $identitas['jenis_kelamin'] ?? null,
                        'tanggal_lahir'       => $identitas['tanggal_lahir'] ?? null,
                        'tempat_lahir'        => $identitas['tempat_lahir'] ?? null,
                        'pendidikan_terakhir' => $identitas['pendidikan'] ?? null,
                        'pekerjaan'           => $identitas['pekerjaan'] ?? null,
                        'status_kawin'        => $identitas['status_kawin'] ?? null,
                        'foto_identitas'      => $payloadMember['foto'] ?? null,
                        'source_name'         => 'apply_usulan_' . $usulan_id,
                        'created_at'          => date('Y-m-d H:i:s')
                    ]);
                }
            }

            // 💰 4. Upsert dtsen_se (Finansial & Aset)
            $seData = $payload['sosial_ekonomi'] ?? [];
            $updateSE = [
                'kepemilikan_aset'              => json_encode($payload['aset'] ?? [], JSON_UNESCAPED_UNICODE),
                'pengeluaran_listrik'           => $seData['pengeluaran_listrik'] ?? null,
                'pengeluaran_pulsa'             => $seData['pengeluaran_pulsa'] ?? null,
                'pengeluaran_internet'          => $seData['pengeluaran_internet'] ?? null,
                'pengeluaran_makan_mingguan'    => $seData['pengeluaran_makan_mingguan'] ?? null,
                'pengeluaran_non_makan_bulanan' => $seData['pengeluaran_non_makan_bulanan'] ?? null,
                'pengeluaran_non_makan_tahunan' => $seData['pengeluaran_non_makan_tahunan'] ?? null,
                'pendapatan_gaji'               => $seData['pendapatan_gaji'] ?? null,
                'pendapatan_usaha'              => $seData['pendapatan_usaha'] ?? null,
                'pendapatan_lainnya'            => $seData['pendapatan_lainnya'] ?? null,
                'latitude'                      => $geo['lat'] ?? null,
                'longitude'                     => $geo['lng'] ?? null,
                'updated_at'                    => date('Y-m-d H:i:s')
            ];

            if ($this->db->table('dtsen_se')->where('id_kk', $idKk)->get()->getRowArray()) {
                $this->db->table('dtsen_se')->where('id_kk', $idKk)->update($updateSE);
            } else {
                $updateSE['id_rt'] = $idRtSekarang;
                $updateSE['id_kk'] = $idKk;
                $updateSE['created_at'] = date('Y-m-d H:i:s');
                $this->db->table('dtsen_se')->insert($updateSE);
            }

            // 🟦 5. Update Status Usulan
            $this->db->table('dtsen_usulan')->where('id', $usulan_id)->update(['status' => 'diverifikasi', 'verified_at' => date('Y-m-d H:i:s')]);

            $this->db->transCommit();
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data usulan berhasil diterapkan.', 'redirect' => base_url('dtsen-se')]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menerapkan data: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // BAGIAN FUNGSI-FUNGSI LAINNYA DI BAWAH INI (SAMA DENGAN SEBELUMNYA)
    // deleteAnggota(), deleteKeluarga(), lanjutkan(), tambah(), store(), getAnggotaList(), getDataDraft(), syncDesil dll...
    // ==========================================

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
                se.kategori_desil,
                r.rw, r.rt,
                COALESCE(u.fullname, us.created_by) AS created_by_name,
                COALESCE(u.id, NULL) AS created_by_id,
                COALESCE(us.payload, '{}') AS payload,
                (SELECT COUNT(1) FROM dtsen_usulan_art aua WHERE aua.dtsen_usulan_id = us.id) AS jumlah_art_usulan
            ")
                ->join('dtsen_kk kk', 'kk.id_kk = us.dtsen_kk_id', 'left')
                ->join('dtsen_se se', 'se.id_kk = us.dtsen_kk_id', 'left') // 🚀 JOIN TABEL SE
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
                r.rw, r.rt,
                se.kategori_desil
            ")
                ->join('dtks_users petugas', 'petugas.id = u.created_by', 'left')
                ->join('dtsen_kk kk', 'kk.id_kk = u.dtsen_kk_id', 'left')
                ->join('dtsen_se se', 'se.id_kk = u.dtsen_kk_id', 'left')
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
