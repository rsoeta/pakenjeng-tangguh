<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Traits\WilayahFilterTrait;

class SensusEkonomi extends BaseController
{
    use WilayahFilterTrait;

    protected $db;
    protected $authModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        // Pastikan AuthModel sudah di-load sesuai standar Sinden
        $this->authModel = new \App\Models\Dtks\AuthModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Pencarian Data Keluarga - Sensus Ekonomi 2026'
        ];
        return view('dtsen/sensus_ekonomi/v_cari', $data);
    }

    public function cariKk()
    {
        $request = \Config\Services::request();

        if (!$request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        }

        $no_kk = trim($request->getPost('no_kk'));

        if (empty($no_kk)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Silakan masukkan Nomor KK terlebih dahulu.'
            ]);
        }

        // Ambil data User yang sedang login
        $user     = $this->authModel->getUserId();
        $roleId   = session()->get('role_id') ?? $user['role_id'] ?? 6;
        $kodeDesa = session()->get('kode_desa') ?? ($user['kode_desa'] ?? '');

        // 🚀 RACIKAN KEAMANAN: Query hanya KK yang aktif dengan tambahan JOIN ke dtsen_se
        $builder = $this->db->table('dtsen_kk k')
            // 🚀 PERBAIKAN: Masukkan se.kategori_desil ke dalam select
            ->select('k.id_kk, k.no_kk, k.kepala_keluarga, k.alamat, rt.rt, rt.rw, se.kategori_desil')
            ->join('dtsen_rt rt', 'rt.id_rt = k.id_rt', 'left')
            // 🚀 PERBAIKAN: Hubungkan relasi ke tabel dtsen_se berdasarkan id_kk
            ->join('dtsen_se se', 'se.id_kk = k.id_kk', 'left')
            ->where('k.no_kk', $no_kk)
            ->where('k.deleted_at IS NULL');

        // 🔐 KARANTINA WILAYAH: Jika beda RW/RT dari wilayah kerja, data tidak akan bocor!
        $filterData = [
            'kode_desa'     => $kodeDesa,
            'wilayah_tugas' => trim($user['wilayah_tugas'] ?? '')
        ];
        $this->applyWilayahFilter($builder, $filterData, $roleId);

        $keluarga = $builder->get()->getRowArray();

        if ($keluarga) {
            // 🚀 PERBAIKAN: Format angka INT dari database menjadi format teks "Desil X"
            $teksDesil = !empty($keluarga['kategori_desil']) ? 'Desil ' . $keluarga['kategori_desil'] : 'Belum Ditentukan';

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => [
                    'id_kk'           => $keluarga['id_kk'],
                    'no_kk'           => esc($keluarga['no_kk']),
                    'kepala_keluarga' => esc($keluarga['kepala_keluarga']),
                    'alamat'          => esc($keluarga['alamat']) . ' - RT ' . esc($keluarga['rt']) . ' / RW ' . esc($keluarga['rw']),

                    // 🚀 Gunakan hasil format teks desil yang sudah aman
                    'kategori_desil'  => $teksDesil,

                    'link_detail'     => base_url('sensus-ekonomi/detail/' . $keluarga['id_kk'])
                ]
            ]);
        } else {
            // Pesan dibuat ambigu agar peretas tidak tahu apakah KK-nya yang salah atau Wilayahnya yang dikunci
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data Keluarga tidak ditemukan atau Nomor KK berada di luar wilayah kerja Anda!'
            ]);
        }
    }
}
