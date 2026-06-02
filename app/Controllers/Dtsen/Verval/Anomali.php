<?php

namespace App\Controllers\Dtsen\Verval;

use App\Controllers\BaseController;
use App\Models\Dtsen\AnomaliModel;

class Anomali extends BaseController
{
    protected $AnomaliModel;

    public function __construct()
    {
        // 🚀 Inisialisasi Model dengan namespace DTSEN
        $this->AnomaliModel = new AnomaliModel();
    }

    /**
     * Menampilkan Halaman Utama Verval Anomali
     */
    public function index()
    {
        // 🚀 Ambil role_id dari sesi login
        $roleId = session()->get('role_id');

        // 🚀 Logika Default Filter
        // Role 4 = default 'open', Role < 4 = default 'draft'
        $defaultFilter = ($roleId == 4) ? 'open' : (($roleId < 4) ? 'draft' : '');

        $data = [
            'title'          => 'Verval Anomali SIKS-NG',
            'role_id'        => $roleId,
            'default_filter' => $defaultFilter
        ];

        return view('verval/anomali/index', $data);
    }

    /**
     * AJAX Endpoint untuk mencari data berdasarkan NIK (Super Join)
     */
    public function search_nik_ajax()
    {
        if ($this->request->isAJAX()) {
            $nik = $this->request->getPost('nik');
            $data = $this->AnomaliModel->searchPendudukByNik($nik);

            if ($data) {
                // 🚀 Lacak ID Petugas Entri
                $petugasId = $this->AnomaliModel->findPetugasByWilayah($data['rw'], $data['rt']);
                $data['petugas_entri_id'] = $petugasId;

                return $this->response->setJSON([
                    'status' => 'success',
                    'data'   => $data
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data NIK tidak ditemukan di database DTSEN.'
                ]);
            }
        }
    }

    /**
     * AJAX Endpoint untuk menyimpan data Anomali dari Modal
     */
    public function simpan()
    {
        if ($this->request->isAJAX()) {

            // 1. Tangkap File Upload (Screenshot Bukti SIKS-NG)
            $fileBukti = $this->request->getFile('bukti_siksng');
            $namaFileBukti = null;

            if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
                $namaFileBukti = $fileBukti->getRandomName();
                $fileBukti->move('uploads/anomali', $namaFileBukti);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Screenshot bukti ketidakpadanan SIKS-NG wajib diunggah!'
                ]);
            }

            // 2. Logika Penugasan Petugas Entri
            $petugasEntriId = $this->request->getPost('petugas_entri_id');

            // 3. Susun Array Data untuk Disimpan
            $dataSimpan = [
                'nik'               => $this->request->getPost('nik'),
                'no_kk'             => $this->request->getPost('no_kk'),
                'nama_lengkap'      => $this->request->getPost('nama_lengkap'),
                'ibu_kandung'       => $this->request->getPost('ibu_kandung'),
                'tempat_lahir'      => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir'     => $this->request->getPost('tanggal_lahir'),
                'provinsi'          => $this->request->getPost('provinsi'),
                'kabupaten'         => $this->request->getPost('kabupaten'),
                'kecamatan'         => $this->request->getPost('kecamatan'),
                'desa'              => $this->request->getPost('desa'),
                'alamat'            => $this->request->getPost('alamat'),
                'rt'                => $this->request->getPost('rt'),
                'rw'                => $this->request->getPost('rw'),
                'shdk'              => $this->request->getPost('shdk'),
                'jenis_kelamin'     => $this->request->getPost('jenis_kelamin'),
                'status_kawin'      => $this->request->getPost('status_kawin'),
                'pekerjaan'         => $this->request->getPost('pekerjaan'),
                'bukti_siksng'      => $namaFileBukti,
                'petugas_entri_id'  => $petugasEntriId,
                'status_anomali'    => 'open',
                'created_by'        => session()->get('id'),
            ];

            // 4. Eksekusi Simpan
            $simpan = $this->AnomaliModel->insert($dataSimpan);

            if ($simpan) {
                // =======================================================
                // 🔔 Kirim WhatsApp ke Petugas Entri (dtks_users.nope)
                // =======================================================
                try {
                    if (!empty($petugasEntriId)) {
                        $db = \Config\Database::connect();
                        $petugas = $db->table('dtks_users')
                            ->select('id, fullname, nope')
                            ->where('id', $petugasEntriId)
                            ->get()
                            ->getRowArray();

                        if ($petugas && !empty($petugas['nope'])) {
                            // Format Nomor WA
                            $nomorWA = preg_replace('/[^0-9]/', '', $petugas['nope']);
                            if (str_starts_with($nomorWA, '0')) $nomorWA = '62' . substr($nomorWA, 1);
                            if (str_starts_with($nomorWA, '620')) $nomorWA = '62' . substr($nomorWA, 3);

                            // Format Tanggal
                            $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                            $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

                            $now = date('Y-m-d H:i:s');
                            $tanggalLengkap = $hari[date('l', strtotime($now))] . ", " . date('d', strtotime($now)) . " " . $bulan[intval(date('m', strtotime($now)))] . " " . date('Y', strtotime($now)) . ", " . date('H:i', strtotime($now)) . " WIB";

                            // Susun Pesan yang Informatif & Rapi
                            $namaPetugas = $petugas['fullname'] ?? 'Petugas';
                            $linkVerval  = base_url('verval/anomali'); // Dinamis menyesuaikan domain

                            $msg  = "*== SINDEN System ==*\n";
                            $msg .= "📌 *Tugas Baru: Perbaikan Data Anomali*\n\n";
                            $msg .= "Halo {$namaPetugas}, terdapat temuan data KPM Anomali (Tidak Padan Dukcapil) di wilayah RT {$dataSimpan['rt']} / RW {$dataSimpan['rw']} yang memerlukan tindak lanjut Anda.\n\n";

                            $msg .= "👤 *Target KPM:*\n";
                            $msg .= "NIK: {$dataSimpan['nik']}\n";
                            $msg .= "Nama: {$dataSimpan['nama_lengkap']}\n\n";

                            $msg .= "Mohon segera koordinasi dengan KPM terkait, minta Foto Kartu Keluarga (KK) terbaru yang sudah valid, dan perbarui datanya melalui tautan di bawah ini 👇\n\n";
                            $msg .= "🔗 {$linkVerval}\n\n";

                            $msg .= "🗓 Dilaporkan pada: {$tanggalLengkap}\n";
                            $msg .= "Terima kasih atas kerja sama dan gerak cepatnya! 🙏";

                            // Eksekusi Kirim WA
                            $waService = new \App\Libraries\WaService();
                            $send = $waService->sendText($nomorWA, $msg);

                            // Log jika gagal terkirim dari sisi Provider
                            if (!is_array($send) || empty($send['status']) || $send['status'] != 'success') {
                                log_message('error', '[WA ANOMALI] Provider WA error: ' . json_encode($send));
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Log error agar tidak mengganggu respons JSON ke Frontend jika WA gagal
                    log_message('error', '[WA ANOMALI EXCEPTION] ' . $e->getMessage());
                }
                // =======================================================

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Data anomali berhasil disimpan dan pemberitahuan WA telah dikirim ke Petugas Entri!'
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan saat menyimpan ke database.'
                ]);
            }
        }
    }

    /**
     * 🚀 AJAX Endpoint untuk menyuplai data ke DataTables
     */
    public function get_data_ajax()
    {
        if ($this->request->isAJAX()) {
            $status = $this->request->getGet('status');

            // Inisialisasi Query Builder
            $builder = $this->AnomaliModel->orderBy('created_at', 'DESC');

            // Terapkan filter jika ada
            if (!empty($status)) {
                $builder->where('status_anomali', $status);
            }

            $list = $builder->findAll();
            $data = [];
            $no   = 1;

            foreach ($list as $row) {
                // 1. Format Badge Status (Tetap sama)
                $badge = '';
                if ($row['status_anomali'] == 'open') {
                    $badge = '<span class="badge badge-warning px-2 py-1"><i class="fas fa-clock"></i> Open</span>';
                } elseif ($row['status_anomali'] == 'draft') {
                    $badge = '<span class="badge badge-primary px-2 py-1"><i class="fas fa-edit"></i> Draft</span>';
                } elseif ($row['status_anomali'] == 'verified') {
                    $badge = '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-double"></i> Verified</span>';
                } elseif ($row['status_anomali'] == 'rejected') {
                    $badge = '<span class="badge badge-danger px-2 py-1"><i class="fas fa-times"></i> Rejected</span>';
                }

                // 🚀 2. MASKING NIK DENGAN FITUR REVEAL (Hover/Klik)
                $nikAsli = $row['nik'];
                $nikMasked = (strlen($nikAsli) > 8) ? substr($nikAsli, 0, 8) . str_repeat('*', strlen($nikAsli) - 8) : $nikAsli;

                // Bungkus dengan span khusus class 'sensitive-data'
                $nikHtml = '<span class="fw-bold text-dark sensitive-data" data-original="' . $nikAsli . '" data-masked="' . $nikMasked . '" style="cursor: pointer;" title="Klik/Tahan untuk melihat">' . $nikMasked . '</span>';

                // 🚀 3. KOLOM LAMPIRAN: Dinamis menampilkan SIKS-NG dan/atau Foto KK
                $btnLihat = '<div class="d-flex flex-column">';
                $btnLihat .= '<button type="button" class="btn btn-xs btn-outline-warning mb-1 shadow-sm btn-lightbox" data-img="' . base_url('uploads/anomali/' . $row['bukti_siksng']) . '"><i class="fas fa-desktop"></i> SIKS-NG</button>';
                if (!empty($row['foto_kk_baru'])) {
                    $btnLihat .= '<button type="button" class="btn btn-xs btn-outline-info shadow-sm btn-lightbox" data-img="' . base_url('uploads/anomali/' . $row['foto_kk_baru']) . '"><i class="fas fa-id-card"></i> Foto KK</button>';
                }
                $btnLihat .= '</div>';

                // 🚀 4. LOGIKA TOMBOL AKSI PINTAR BERDASARKAN ROLE ID
                $roleId = session()->get('role_id');
                if ($roleId == 4 && ($row['status_anomali'] == 'open' || $row['status_anomali'] == 'rejected')) {
                    // Petugas Entri: Bisa memperbaiki data
                    $btnAksi = '<button type="button" class="btn btn-sm btn-info shadow-sm btn-tindak-lanjut" data-id="' . $row['id_anomali'] . '" title="Perbaiki Data"><i class="fas fa-edit"></i> Perbaiki</button>';
                } elseif ($roleId < 4 && $row['status_anomali'] == 'draft') {
                    // Operator Desa (Role 1, 2, 3): Bisa memverifikasi data yang berstatus draft
                    $btnAksi = '<button type="button" class="btn btn-sm btn-success shadow-sm btn-verifikasi" data-id="' . $row['id_anomali'] . '" title="Verifikasi Data"><i class="fas fa-check-double"></i> Verifikasi</button>';
                } else {
                    // Jika tidak memenuhi syarat, gembok tombolnya
                    $btnAksi = '<button type="button" class="btn btn-sm btn-secondary shadow-sm" disabled><i class="fas fa-lock"></i> Terkunci</button>';
                }

                // 5. Masukkan ke Array DataTables (Ganti variabel $nikMasked menjadi $nikHtml)
                $data[] = [
                    '<div class="text-center">' . $no++ . '</div>',
                    '<div class="text-center text-nowrap">' . $btnAksi . '</div>',
                    '<div class="text-center text-nowrap">' . $badge . '</div>',
                    '<div class="text-nowrap">' . $nikHtml . '<br><span class="text-muted">' . $row['nama_lengkap'] . '</span></div>',
                    '<div class="text-nowrap">' . $row['alamat'] . '<br><small class="text-info fw-bold">RT ' . $row['rt'] . ' / RW ' . $row['rw'] . '</small></div>',
                    '<div class="text-center text-nowrap">' . $btnLihat . '</div>',
                    '<div class="text-center text-nowrap">' . date('d-m-Y H:i', strtotime($row['created_at'])) . '</div>'
                ];
            }

            return $this->response->setJSON(['data' => $data]);
        }
    }

    /**
     * 🚀 AJAX Endpoint: Mengambil detail anomali untuk diisi ke Modal Perbaikan
     */
    public function get_detail_ajax($id_anomali)
    {
        if ($this->request->isAJAX()) {
            $data = $this->AnomaliModel->find($id_anomali);
            if ($data) {
                return $this->response->setJSON(['status' => 'success', 'data' => $data]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
    }

    /**
     * 🚀 AJAX Endpoint: Petugas Entri menyimpan Perbaikan Data & Foto KK
     */
    public function update_petugas()
    {
        if ($this->request->isAJAX()) {
            $id_anomali = $this->request->getPost('id_anomali');
            $fileKk = $this->request->getFile('foto_kk_baru');

            // Susun data yang diubah dari Form Petugas
            $dataUpdate = [
                'nik'            => $this->request->getPost('nik'),
                'no_kk'          => $this->request->getPost('no_kk'),
                'nama_lengkap'   => $this->request->getPost('nama_lengkap'),
                'ibu_kandung'    => $this->request->getPost('ibu_kandung'),
                'tempat_lahir'   => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir'  => $this->request->getPost('tanggal_lahir'),
                'jenis_kelamin'  => $this->request->getPost('jenis_kelamin'),
                'status_kawin'   => $this->request->getPost('status_kawin'),
                'pekerjaan'      => $this->request->getPost('pekerjaan'),
                'shdk'           => $this->request->getPost('shdk'),
                'alamat'         => $this->request->getPost('alamat'),
                'rt'             => $this->request->getPost('rt'),
                'rw'             => $this->request->getPost('rw'),
                'provinsi'       => $this->request->getPost('provinsi'),
                'kabupaten'      => $this->request->getPost('kabupaten'),
                'kecamatan'      => $this->request->getPost('kecamatan'),
                'desa'           => $this->request->getPost('desa'),
                'status_anomali' => 'draft', // Kembalikan ke draft untuk diverifikasi Operator
                'updated_by'     => session()->get('id')
            ];

            // Jika petugas mengunggah KK baru, ganti filenya
            if ($fileKk && $fileKk->isValid() && !$fileKk->hasMoved()) {
                $namaFileKk = $fileKk->getRandomName();
                $fileKk->move('uploads/anomali', $namaFileKk);
                $dataUpdate['foto_kk_baru'] = $namaFileKk;
            }

            $update = $this->AnomaliModel->update($id_anomali, $dataUpdate);

            if ($update) {
                // =======================================================
                // 🔔 Kirim WhatsApp ke Operator Desa (created_by)
                // =======================================================
                try {
                    $anomali = $this->AnomaliModel->find($id_anomali);
                    if ($anomali && !empty($anomali['created_by'])) {
                        $db = \Config\Database::connect();
                        // Cari data Operator yang membuat anomali
                        $operator = $db->table('dtks_users')->select('fullname, nope')->where('id', $anomali['created_by'])->get()->getRowArray();
                        // Cari data Petugas Entri yang sedang login
                        $petugasLogin = $db->table('dtks_users')->select('fullname')->where('id', session()->get('id'))->get()->getRowArray();

                        if ($operator && !empty($operator['nope'])) {
                            $nomorWA = preg_replace('/[^0-9]/', '', $operator['nope']);
                            if (str_starts_with($nomorWA, '0')) $nomorWA = '62' . substr($nomorWA, 1);
                            if (str_starts_with($nomorWA, '620')) $nomorWA = '62' . substr($nomorWA, 3);

                            $namaPetugas  = $petugasLogin['fullname'] ?? 'Petugas Entri';
                            $namaOperator = $operator['fullname'] ?? 'Operator';
                            $linkVerval   = base_url('verval/anomali');

                            $msg  = "*== SINDEN System ==*\n";
                            $msg .= "📌 *Pemberitahuan Perbaikan Data*\n\n";
                            $msg .= "Halo {$namaOperator}, Petugas Entri (*{$namaPetugas}*) telah melakukan perbaikan data dan mengunggah Foto KK terbaru untuk:\n\n";

                            $msg .= "👤 NIK: {$dataUpdate['nik']}\n";
                            $msg .= "👤 Nama: {$dataUpdate['nama_lengkap']}\n\n";

                            $msg .= "Status saat ini menjadi *DRAFT*. Mohon segera lakukan Verifikasi melalui tautan berikut 👇\n";
                            $msg .= "🔗 {$linkVerval}\n\n";
                            $msg .= "Terima kasih.";

                            $waService = new \App\Libraries\WaService();
                            $waService->sendText($nomorWA, $msg);
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', '[WA UPDATE PETUGAS EXCEPTION] ' . $e->getMessage());
                }
                // =======================================================

                return $this->response->setJSON(['status' => 'success', 'message' => 'Data KPM berhasil diperbarui dan diajukan ke Operator!']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan pembaruan.']);
            }
        }
    }

    /**
     * 🚀 AJAX Endpoint: Sumber Data Dropdown Bertingkat
     */
    public function get_wilayah($jenis, $parent_id = null)
    {
        if ($this->request->isAJAX()) {
            $db = \Config\Database::connect();
            $data = [];

            if ($jenis == 'provinsi') {
                $data = $db->table('tb_provinces')->get()->getResultArray();
            } elseif ($jenis == 'kabupaten' && $parent_id) {
                $data = $db->table('tb_regencies')->where('province_id', $parent_id)->get()->getResultArray();
            } elseif ($jenis == 'kecamatan' && $parent_id) {
                $data = $db->table('tb_districts')->where('regency_id', $parent_id)->get()->getResultArray();
            } elseif ($jenis == 'desa' && $parent_id) {
                $data = $db->table('tb_villages')->where('district_id', $parent_id)->get()->getResultArray();
            }

            return $this->response->setJSON($data);
        }
    }

    /**
     * 🚀 AJAX Endpoint: Sumber Data Dropdown Referensi (SHDK, Kawin, Pekerjaan)
     */
    public function get_referensi($jenis)
    {
        if ($this->request->isAJAX()) {
            $db = \Config\Database::connect();
            $data = [];

            // Format output diseragamkan menjadi 'id' dan 'name' agar bisa dibaca satu fungsi JS
            if ($jenis == 'shdk') {
                $data = $db->table('tb_shdk')->select('id, jenis_shdk as name')->get()->getResultArray();
            } elseif ($jenis == 'status_kawin') {
                $data = $db->table('tb_status_kawin')->select('idStatus as id, StatusKawin as name')->get()->getResultArray();
            } elseif ($jenis == 'pekerjaan') {
                $data = $db->table('tb_penduduk_pekerjaan')->select('pk_id as id, pk_nama as name')->get()->getResultArray();
            }

            return $this->response->setJSON($data);
        }
    }

    /**
     * 🚀 AJAX Endpoint: Eksekusi Verifikasi oleh Operator Desa + Kirim Notif WA ke Petugas
     */
    public function proses_verifikasi()
    {
        if ($this->request->isAJAX()) {
            $id_anomali = $this->request->getPost('id_anomali');
            $status     = $this->request->getPost('status'); // 'verified' atau 'rejected'
            $catatan    = $this->request->getPost('catatan_penolakan');

            $dataUpdate = [
                'status_anomali'    => $status,
                'catatan_penolakan' => $catatan,
                'updated_by'        => session()->get('id')
            ];

            $update = $this->AnomaliModel->update($id_anomali, $dataUpdate);

            if ($update) {
                // =======================================================
                // 🔔 Kirim WhatsApp ke Petugas Entri
                // =======================================================
                try {
                    $anomali = $this->AnomaliModel->find($id_anomali);
                    if ($anomali && !empty($anomali['petugas_entri_id'])) {
                        $db = \Config\Database::connect();
                        // Cari data Petugas Entri
                        $petugas = $db->table('dtks_users')->select('fullname, nope')->where('id', $anomali['petugas_entri_id'])->get()->getRowArray();
                        // Cari data Operator yang memverifikasi
                        $operatorLogin = $db->table('dtks_users')->select('fullname')->where('id', session()->get('id'))->get()->getRowArray();

                        if ($petugas && !empty($petugas['nope'])) {
                            $nomorWA = preg_replace('/[^0-9]/', '', $petugas['nope']);
                            if (str_starts_with($nomorWA, '0')) $nomorWA = '62' . substr($nomorWA, 1);
                            if (str_starts_with($nomorWA, '620')) $nomorWA = '62' . substr($nomorWA, 3);

                            $namaPetugas  = $petugas['fullname'] ?? 'Petugas';
                            $namaOperator = $operatorLogin['fullname'] ?? 'Operator Desa';
                            $linkVerval   = base_url('verval/anomali');

                            $msg  = "*== SINDEN System ==*\n";
                            $msg .= "📌 *Hasil Verifikasi Data Anomali*\n\n";
                            $msg .= "Halo {$namaPetugas}, tim Operator (*{$namaOperator}*) telah melakukan pengecekan terhadap perbaikan data yang Anda ajukan untuk:\n\n";

                            $msg .= "👤 NIK: {$anomali['nik']}\n";
                            $msg .= "👤 Nama: {$anomali['nama_lengkap']}\n\n";

                            if ($status == 'verified') {
                                $msg .= "✅ *STATUS: DISETUJUI (VALID)*\n";
                                $msg .= "Data sudah padan. Terima kasih atas kerja keras Anda!\n";
                            } else {
                                $msg .= "❌ *STATUS: DITOLAK*\n";
                                $msg .= "📝 *Catatan Koreksi:* _{$catatan}_\n\n";
                                $msg .= "Mohon segera lakukan perbaikan ulang melalui tautan berikut 👇\n";
                                $msg .= "🔗 {$linkVerval}\n";
                            }

                            $waService = new \App\Libraries\WaService();
                            $waService->sendText($nomorWA, $msg);
                        }
                    }
                } catch (\Exception $e) {
                    log_message('error', '[WA VERIFIKASI EXCEPTION] ' . $e->getMessage());
                }
                // =======================================================

                return $this->response->setJSON(['status' => 'success', 'message' => 'Status anomali berhasil diperbarui!']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.']);
            }
        }
    }
}
