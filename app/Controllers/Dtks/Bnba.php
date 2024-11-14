<?php

namespace App\Controllers\Dtks;

use App\Controllers\BaseController;
use App\Models\Dtks\DtksStatusModel;
use App\Models\Dtks\VervalPbiModel;
use App\Models\Dtks\DtksKetModel;
use App\Models\Dtks\ShdkModel;
use App\Models\Dtks\BnbaModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;
use App\Models\GenModel;
use App\Models\Dtks\AuthModel;

use function PHPUnit\Framework\isEmpty;

class Bnba extends BaseController
{
    public function __construct()
    {
        $this->BnbaModel = new BnbaModel();
        $this->WilayahModel = new WilayahModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->GenModel = new GenModel();
        $this->statusdtks = new DtksStatusModel();
        $this->keterangan = new DtksKetModel();
        $this->datashdk = new ShdkModel();
        $this->VervalPbiModel = new VervalPbiModel();
        $this->AuthModel = new AuthModel();
    }

    public function index()
    {

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'BNBA DTKS',
            'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', Profil_Admin()['kode_kec'])->findAll(),
            // 'operator' => $this->operator->orderBy('NamaLengkap', 'asc')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'datart' => $this->RtModel->noRt(),

            'datart' => $this->BnbaModel->getDataRT()->getResultArray(),
            'datashdk' => $this->datashdk->findAll(),
            'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
            'percentages' => $this->VervalPbiModel->jml_persentase(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_login' => $this->AuthModel->getUserId(),



        ];
        // dd($data['user_login']);
        return view('dtks/data/dtks/clean/index', $data);
    }

    // public function tabel_data()
    // {
    //     $model = new BnbaModel();
    //     // $KetMasalah = new KetModel();

    //     $csrfName = csrf_token();
    //     $csrfHash = csrf_hash();
    //     $user = session()->get('role_id');

    //     $filter0 = '1';
    //     $filter1 = $this->request->getPost('datadesa');
    //     // $operator = $this->request->getPost('operator');
    //     $filter2 = $this->request->getPost('datarw');
    //     $filter3 = $this->request->getPost('datart');
    //     $filter4 = $this->request->getPost('datashdk');

    //     $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter0);
    //     $jumlah_semua = $model->jumlah_semua();
    //     $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter0);

    //     $data = array();
    //     $no = $_POST['start'];
    //     foreach ($listing as $key) {

    //         $no++;
    //         $row = array();
    //         $row[] = $no;

    //         // cari-foto
    //         $foundFile = ''; // variabel untuk menyimpan nama file dengan 16 digit angka
    //         $namaFile = '';
    //         $ekstensiFile = '';

    //         $dirPath = FCPATH . 'data/bnba/foto-kpm'; // Ganti dengan path direktori foto Anda && FCPATH . 'data/dkm/foto-cpm'
    //         $files = scandir($dirPath);

    //         $filenames = [];
    //         foreach ($files as $file) {
    //             if (!in_array($file, array(".", ".."))) {
    //                 $filenames[] = $file;
    //             }
    //         }
    //         // var_dump($filenames);
    //         // die;

    //         // Sekarang $filenames berisi daftar file dalam direktori tersebut
    //         $nik_kpm = $key->db_nik;
    //         foreach ($filenames as $filename) {
    //             // preg_match('/\d{16}/', $filename, $matches);
    //             preg_match('/' . $nik_kpm . '/', $filename, $matches);
    //             if (!empty($matches)) {
    //                 $sixteenDigitNumber = $matches[0];
    //                 $filenameParts = explode($sixteenDigitNumber, $filename);

    //                 // echo "Nama File: {$filenameParts[0]} <br>";
    //                 // echo "16 digit angka: $sixteenDigitNumber <br>";
    //                 // echo "Ekstensi File: {$filenameParts[1]} <br>";

    //                 // Mencoba mendapatkan URL foto
    //                 $fotoURL = FOTO_KPM($filename, 'direktori_pertama');
    //                 if ($fotoURL === base_url('assets/images/image_not_available.jpg')) {
    //                     // Jika tidak ditemukan di direktori pertama, coba di direktori kedua
    //                     $fotoURL = FOTO_KPM($filename, 'direktori_kedua');
    //                 }

    //                 // echo "URL Foto: $fotoURL <br><br>";

    //                 // Simpan nama file dengan 16 digit angka yang ditemukan
    //                 $namaFile = $filenameParts[0];
    //                 $ekstensiFile = $filenameParts[1];

    //                 $foundFile = $filename;
    //                 var_dump($foundFile);
    //                 die;
    //                 break; // Hentikan loop setelah menemukan file
    //             } else {
    //                 // echo "Tidak ditemukan 16 digit angka pada $filename<br>";
    //             }
    //         }

    //         // echo "Nama File dengan 16 digit angka: $foundFile"; // Menampilkan nama file yang ditemukan
    //         // akhir cari-foto

    //         $row[] = '
    //         <a href=' . FOTO_KPM($namaFile . $foundFile . $ekstensiFile, 'foto-kpm') . ' data-lightbox="BNT' . $foundFile . '" data-title="Foto Identitas">
    //         <img src="' . FOTO_KPM($namaFile . $foundFile . $ekstensiFile, 'foto-kpm') . '" alt="" style="width: 30px; height: 40px; border-radius: 2px;">
    //         </a>
    //         ';
    //         $row[] = $key->db_id_dtks;
    //         $row[] = $key->db_nama;
    //         $row[] = $key->db_nkk;
    //         $row[] = $key->db_nik;
    //         $row[] = $key->db_tmp_lahir;
    //         $row[] = $key->db_tgl_lahir;
    //         $row[] = $key->jenis_shdk;

    //         if ($user <= 4) :
    //             $row[] = '<a href="javascript:void(0)" title="more info" onclick="detail_person(' . "'" . $key->db_id . "'" . ')"></a>';
    //         elseif ($user <= 3) :
    //             $row[] = '<a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->db_id . "'" . ')"><i class="far fa-edit"></i></a> <button class="btn btn-sm btn-secondary" data-id="' . $key->db_id . '" data-nama="' . $key->db_nama . '" id="deleteBtn"><i class="far fa-trash-alt"></i></button>';
    //         else :
    //             $row[] = '';
    //         endif;

    //         $data[] = $row;
    //     }

    //     $output = array(
    //         "draw" => $_POST['draw'],
    //         "recordsTotal" => $jumlah_semua->jml,
    //         "recordsFiltered" => $jumlah_filter->jml,
    //         "data" => $data,
    //     );
    //     $output[$csrfName] = $csrfHash;
    //     echo json_encode($output);
    // }

    // public function tabel_data()
    // {
    //     $model = new BnbaModel();
    //     $csrfName = csrf_token();
    //     $csrfHash = csrf_hash();
    //     $user = session()->get('role_id');

    //     $filter0 = '1';
    //     $filter1 = $this->request->getPost('datadesa');
    //     $filter2 = $this->request->getPost('datarw');
    //     $filter3 = $this->request->getPost('datart');
    //     $filter4 = $this->request->getPost('datashdk');

    //     $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter0);
    //     $jumlah_semua = $model->jumlah_semua();
    //     $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter0);

    //     $data = array();
    //     $no = $_POST['start'];

    //     foreach ($listing as $key) {
    //         // cari foto-kpm
    //         $dirPathKpm1 = FCPATH . 'data/bnba/foto-kpm'; // Ubah sesuai dengan path direktori foto-kpm Anda
    //         $dirPathKpm2 = FCPATH . 'data/dkm/foto-cpm'; // Ubah sesuai dengan path direktori foto-cpm Anda

    //         $filesKpm1 = scandir($dirPathKpm1);
    //         $filesKpm2 = scandir($dirPathKpm2);

    //         $filenames = array_merge(
    //             array_diff($filesKpm1, array(".", "..")),
    //             array_diff($filesKpm2, array(".", ".."))
    //         );

    //         $nik_kpm = $key->db_nik;
    //         $foundFileKpm = '';

    //         foreach ($filenames as $filename) {
    //             preg_match('/' . $nik_kpm . '/', $filename, $matches);
    //             if (!empty($matches)) {
    //                 $foundFileKpm = $filename;
    //                 break;
    //             }
    //         }

    //         if ($foundFileKpm !== '') {
    //             $fotoKpmURL = FOTO_KPM($foundFileKpm, 'foto-kpm');
    //         } elseif (isEmpty(FOTO_KPM($foundFileKpm, 'foto-kpm'))) {
    //             $fotoKpmURL = FOTO_KPM($foundFileKpm, 'foto-cpm');
    //         } else {
    //             $fotoKpmURL = base_url('assets/images/image_not_available.jpg');
    //         }
    //         $foto_kpm_URL = $fotoKpmURL;

    //         // Mendapatkan base_url dari CodeIgniter
    //         $baseURL = base_url();

    //         // URL statis yang ingin diganti
    //         $staticURL = 'http://localhost:8080/C:%5Claragon%5Cwww%5Cdtks.pakenjeng-tangg%5Cpublic%5C';

    //         // Mengganti bagian awal URL dengan base_url dari CodeIgniter
    //         $foto_kpm_URL = str_replace($staticURL, $baseURL . '/', $foto_kpm_URL);
    //         // selesai cari foto-kpm

    //         // cari foto-rumah
    //         $dirPathRmh1 = FCPATH . 'data/bnba/foto-rumah'; // Ubah sesuai dengan path direktori foto-kpm Anda
    //         $dirPathRmh2 = FCPATH . 'data/usulan/foto_rumah'; // Ubah sesuai dengan path direktori foto-cpm Anda

    //         $filesRmh1 = scandir($dirPathRmh1);
    //         $filesRmh2 = scandir($dirPathRmh2);

    //         $filenames = array_merge(
    //             array_diff($filesRmh1, array(".", "..")),
    //             array_diff($filesRmh2, array(".", ".."))
    //         );

    //         $nik_kpm = $key->db_nik;
    //         $foundFileRmh = '';

    //         foreach ($filenames as $filename) {
    //             preg_match('/' . $nik_kpm . '/', $filename, $matches);
    //             if (!empty($matches)) {
    //                 $foundFileRmh = $filename;
    //                 break;
    //             }
    //         }

    //         if ($foundFileRmh !== '') {
    //             $fotoRmhURL = FOTO_RUMAH($foundFileRmh, 'foto-rumah');
    //         } elseif (isEmpty(FOTO_RUMAH($foundFileRmh, 'foto-rumah'))) {
    //             $fotoRmhURL = FOTO_RUMAH($foundFileRmh, 'foto_rumah');
    //         } else {
    //             $fotoRmhURL = base_url('assets/images/image_not_available.jpg');
    //         }
    //         $foto_rmh_URL = $fotoRmhURL;

    //         // Mengganti bagian awal URL dengan base_url dari CodeIgniter
    //         $foto_rmh_URL = str_replace($staticURL, $baseURL . '/', $foto_rmh_URL);
    //         // selesai cari foto-rumah

    //         $no++;
    //         $row = array();
    //         $row[] = $no;

    //         $row[] = '
    //                     <a href=' . $foto_kpm_URL . ' data-lightbox="' . $foundFileKpm . '" data-title="' . $key->db_nama . '(' . $key->db_nik . ')' . '">
    //                     <img src="' . $foto_kpm_URL . '" alt="" style="width: 30px; height: 40px; border-radius: 2px;">
    //                     </a>
    //                     <a href=' . $foto_rmh_URL . ' data-lightbox="' . $foundFileRmh . '" data-title="' . $key->db_nama . '(' . $key->db_nik . ')' . '"></a>
    //                     ';

    //         // Menambahkan data lain ke dalam tabel sesuai kebutuhan
    //         $row[] = $key->db_nama;
    //         $row[] = $key->db_nkk;
    //         $row[] = $key->db_nik;
    //         $row[] = $key->NamaJenKel;
    //         $row[] = $key->db_tmp_lahir;
    //         $row[] = $key->db_tgl_lahir;
    //         $row[] = $key->jenis_shdk;

    //         if ($user <= 3) :
    //             $row[] = '
    //             <a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->db_id . "'" . ')"><i class="far fa-edit"></i></a>
    //             <button class="btn btn-sm btn-secondary" data-id="' . $key->db_id . '" data-nama="' . $key->db_nama . '" id="deleteBtn"><i class="far fa-trash-alt"></i></button>
    //             ';
    //         elseif ($user <= 4) :
    //             $row[] = '
    //             <a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->db_id . "'" . ')"><i class="far fa-edit"></i></a> 
    //             ';
    //         else :
    //             $row[] = '';
    //         endif;

    //         $data[] = $row;
    //     }

    //     $output = array(
    //         "draw" => $_POST['draw'],
    //         "recordsTotal" => $jumlah_semua->jml,
    //         "recordsFiltered" => $jumlah_filter->jml,
    //         "data" => $data,
    //     );
    //     $output[$csrfName] = $csrfHash;
    //     echo json_encode($output);
    // }

    public function tabel_data()
    {
        $model = new BnbaModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();
        $user = session()->get('role_id');

        $filter0 = '1';
        $filter1 = $this->request->getPost('datadesa');
        $filter2 = $this->request->getPost('datarw');
        $filter3 = $this->request->getPost('datart');
        $filter4 = $this->request->getPost('datashdk');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter0);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter0);

        $data = array();
        $no = $_POST['start'];

        foreach ($listing as $key) {
            // cari foto-kpm
            $dirPathKpm1 = FCPATH . 'data/bnba/foto-kpm'; // Ubah sesuai dengan path direktori foto-kpm Anda
            $dirPathKpm2 = FCPATH . 'data/dkm/foto-cpm'; // Ubah sesuai dengan path direktori foto-cpm Anda

            $filesKpm1 = scandir($dirPathKpm1);
            $filesKpm2 = scandir($dirPathKpm2);

            $filenamesKpm = array_merge(
                array_diff($filesKpm1, array(".", "..")),
                array_diff($filesKpm2, array(".", ".."))
            );

            // Urutkan array berdasarkan waktu modifikasi
            usort($filenamesKpm, function ($a, $b) use ($dirPathKpm1, $dirPathKpm2) {
                $pathA1 = $dirPathKpm1 . DIRECTORY_SEPARATOR . $a;
                $pathA2 = $dirPathKpm2 . DIRECTORY_SEPARATOR . $a;
                $pathA = file_exists($pathA1) ? $pathA1 : $pathA2;

                $pathB1 = $dirPathKpm1 . DIRECTORY_SEPARATOR . $b;
                $pathB2 = $dirPathKpm2 . DIRECTORY_SEPARATOR . $b;
                $pathB = file_exists($pathB1) ? $pathB1 : $pathB2;

                $timeA = file_exists($pathA) ? filemtime($pathA) : 0;
                $timeB = file_exists($pathB) ? filemtime($pathB) : 0;

                return $timeB - $timeA;
            });

            $nik_kpm = $key->db_nik;
            $foundFileKpm = '';

            foreach ($filenamesKpm as $filename) {
                preg_match('/' . $nik_kpm . '/', $filename, $matches);
                if (!empty($matches)) {
                    $foundFileKpm = $filename;
                    break;
                }
            }

            if (!empty($foundFileKpm)) {
                $fotoKpmURL = FOTO_KPM($foundFileKpm, 'foto-kpm');
            } else {
                $fotoKpmURL = base_url('assets/images/image_not_available.jpg');
            }

            // selesai cari foto-kpm

            // cari foto-rumah
            $dirPathRmh1 = FCPATH . 'data/bnba/foto-rumah'; // Ubah sesuai dengan path direktori foto-rumah Anda
            $dirPathRmh2 = FCPATH . 'data/usulan/foto_rumah'; // Ubah sesuai dengan path direktori foto-rumah Anda

            $filesRmh1 = scandir($dirPathRmh1);
            $filesRmh2 = scandir($dirPathRmh2);

            $filenamesRmh = array_merge(
                array_diff($filesRmh1, array(".", "..")),
                array_diff($filesRmh2, array(".", ".."))
            );

            // Urutkan array berdasarkan waktu modifikasi
            usort($filenamesRmh, function ($a, $b) use ($dirPathRmh1, $dirPathRmh2) {
                $pathA1 = $dirPathRmh1 . DIRECTORY_SEPARATOR . $a;
                $pathA2 = $dirPathRmh2 . DIRECTORY_SEPARATOR . $a;
                $pathA = file_exists($pathA1) ? $pathA1 : $pathA2;

                $pathB1 = $dirPathRmh1 . DIRECTORY_SEPARATOR . $b;
                $pathB2 = $dirPathRmh2 . DIRECTORY_SEPARATOR . $b;
                $pathB = file_exists($pathB1) ? $pathB1 : $pathB2;

                $timeA = file_exists($pathA) ? filemtime($pathA) : 0;
                $timeB = file_exists($pathB) ? filemtime($pathB) : 0;

                return $timeB - $timeA;
            });

            $nik_rumah = $key->db_nik;
            $foundFileRmh = '';

            foreach ($filenamesRmh as $filename) {
                preg_match('/' . $nik_rumah . '/', $filename, $matches);
                if (!empty($matches)) {
                    $foundFileRmh = $filename;
                    break;
                }
            }

            if (!empty($foundFileRmh)) {
                $fotoRmhURL = FOTO_RUMAH($foundFileRmh, 'foto-rumah');
            } else {
                $fotoRmhURL = base_url('assets/images/image_not_available.jpg');
            }

            // selesai cari foto-rumah


            // Mengganti bagian awal URL dengan base_url dari CodeIgniter
            // Mendapatkan base_url dari CodeIgniter
            $baseURL = base_url();

            // URL statis yang ingin diganti
            $staticURLLocal = 'http://localhost:8080/C:%5Claragon%5Cwww%5Cdtks.pakenjeng-tangg%5Cpublic%5C';
            $staticURLHosting = 'https://dtks.pakenjeng-tangguh.id/home/pakenjen/repositories/pakenjeng-tangguh/public';

            // Pilih URL statis berdasarkan lingkungan
            $staticURL = ENVIRONMENT === 'production' ? $staticURLHosting : $staticURLLocal;

            // Mengganti bagian awal URL dengan base_url dari CodeIgniter
            $fotoKpmURL = str_replace($staticURL, $baseURL . '/', $fotoKpmURL);
            $fotoRmhURL = str_replace($staticURL, $baseURL . '/', $fotoRmhURL);


            // if (!$staticURLLocal || !$staticURLHosting) {
            //     // Mengganti bagian awal URL dengan base_url dari CodeIgniter
            //     $fotoKpmURL = str_replace(base_url(), '', $fotoKpmURL);
            //     $fotoRmhURL = str_replace(base_url(), '', $fotoRmhURL);
            // } else {
            //     // Mengganti bagian awal URL dengan base_url dari CodeIgniter
            //     $fotoKpmURL = str_replace($staticURL, $baseURL . '/', $fotoKpmURL);
            //     $fotoRmhURL = str_replace($staticURL, $baseURL . '/', $fotoRmhURL);
            // }


            $no++;
            $row = array();
            $row[] = $no;

            $row[] = '
            <a href="' . $fotoKpmURL . '" data-lightbox="' . $foundFileKpm . '" data-title="' . $key->db_nama . ' (' . $key->db_nik . ')' . '">
                <img src="' . $fotoKpmURL . '" alt="" style="width: 30px; height: 40px; border-radius: 2px;">
            </a>
            <a href="' . $fotoRmhURL . '" data-lightbox="' . $foundFileKpm . '" data-title="' . $key->db_nama . ' (' . $key->db_nik . ')' . '"></a>
        ';

            // Menambahkan data lain ke dalam tabel sesuai kebutuhan
            $row[] = $key->db_nama;
            $row[] = $key->db_nkk;
            $row[] = $key->db_nik;
            $row[] = $key->NamaJenKel;
            $row[] = $key->db_tmp_lahir;
            $row[] = $key->db_tgl_lahir;
            $row[] = $key->jenis_shdk;

            if ($user <= 3) :
                $row[] = '
                <a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->db_id . "'" . ')"><i class="far fa-edit"></i></a>
                <button class="btn btn-sm btn-secondary" data-id="' . $key->db_id . '" data-nama="' . $key->db_nama . '" id="deleteBtn"><i class="far fa-trash-alt"></i></button>
            ';
            elseif ($user <= 4) :
                $row[] = '
                <a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->db_id . "'" . ')"><i class="far fa-edit"></i></a> 
            ';
            else :
                $row[] = '';
            endif;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $jumlah_semua->jml,
            "recordsFiltered" => $jumlah_filter->jml,
            "data" => $data,
        );
        $output[$csrfName] = $csrfHash;
        echo json_encode($output);
    }

    // function show detail data by id from BnbaModel
    public function detail($id)
    {
        if ($this->request->isAJAX()) {
            $kode_kec = Profil_Admin()['kode_kec'];
            $BnbaModel = new BnbaModel();

            $id = $this->request->getVar('db_id');
            // dd($id);
            $row = $BnbaModel->find($id);

            $data = [
                'title' => 'View User',
                'modTtl' => 'Form. View User',
                'id' => $row['id'],
                'nik' => $row['nik'],
                'kode_desa' => $row['kode_desa'],
                'username' => $row['username'],
                'fullname' => $row['fullname'],
                'email' => $row['email'],
                'status' => $row['status'],
                'level' => $row['level'],
                'role_id' => $row['role_id'],
                'datarw' => $this->RwModel->noRw(),
                'user_image' => $row['user_image'],
                'roles' => $this->Role->getRole()->getResultArray(),
                'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', $kode_kec)->findAll(),

            ];
            // dd($data);
            $msg = [
                'sukses' => view('dtks/users/formview', $data),
            ];

            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
            exit;
        }
    }

    public function formview()
    {
        if ($this->request->isAJAX()) {

            // var_dump($this->request->getPost());

            $id = $this->request->getVar('id');

            $model = new BnbaModel();
            $row = $model->find($id);

            // var_dump($row);
            $kode_kab = $row['db_regency'];

            $data = [
                'title' => 'Detail Penerima Manfaat',
                'dataprov' => $this->WilayahModel->getProv()->getResultArray(),
                'datakab' => $this->WilayahModel->getKab()->getResultArray(),
                'datakec' => $this->WilayahModel->getKec($kode_kab)->getResultArray(),
                'datadesa' => $this->WilayahModel->getDataDesa()->getResultArray(),
                'datadusun' => $this->WilayahModel->getDusun()->getResultArray(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
                'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
                'jenisKelamin' => $this->BnbaModel->getDataJenkel(),
                'datashdk' => $this->BnbaModel->getDataShdk(),

                'db_id' => $row['db_id'],
                'db_id_dtks' => $row['db_id_dtks'],
                'province_id' => $row['db_province'],
                'regency_id' => $row['db_regency'],
                'district_id' => $row['db_district'],
                'village_id' => $row['db_village'],
                'alamat' => $row['db_alamat'],
                'dusun' => $row['db_dusun'],
                'no_rw' => $row['db_rw'],
                'no_rt' => $row['db_rt'],
                'nomor_kk' => $row['db_nkk'],
                'nomor_nik' => $row['db_nik'],
                'nama' => $row['db_nama'],
                'tempat_lahir' => $row['db_tmp_lahir'],
                'tanggal_lahir' => $row['db_tgl_lahir'],
                'jenis_kelamin' => $row['db_jenkel_id'],
                'nama_ibu_kandung' => $row['db_ibu_kandung'],
                'hubungan_keluarga' => $row['db_shdk_id'],
                'created_by' => $row['db_creator'],
                'status' => $row['db_status'],
            ];


            // dd($data);
            $msg = [
                'sukses' => view('dtks/data/dtks/clean/modaledit', $data)

            ];

            echo json_encode($msg);
        }
    }

    public function formedit()
    {

        if ($this->request->isAJAX()) {

            // var_dump($this->request->getPost());

            $id = $this->request->getPost('id');

            $model = new BnbaModel();
            $row = $model->find($id);

            // var_dump($row);
            $kode_kab = $row['db_regency'];

            $data = [
                'title' => 'Status Penerima Manfaat',
                'dataprov' => $this->WilayahModel->getProv()->getResultArray(),
                'datakab' => $this->WilayahModel->getKab()->getResultArray(),
                'datakec' => $this->WilayahModel->getKec($kode_kab)->getResultArray(),
                'datadesa' => $this->WilayahModel->getDataDesa()->getResultArray(),
                'datadusun' => $this->WilayahModel->getDusun()->getResultArray(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
                'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
                'jenisKelamin' => $this->BnbaModel->getDataJenkel(),
                'datashdk' => $this->BnbaModel->getDataShdk(),

                'db_id' => $row['db_id'],
                'db_id_dtks' => $row['db_id_dtks'],
                'nomor_nik' => $row['db_nik'],
                'nama' => $row['db_nama'],
                'tempat_lahir' => $row['db_tmp_lahir'],
                'tanggal_lahir' => $row['db_tgl_lahir'],
                'jenis_kelamin' => $row['db_jenkel_id'],
                'nama_ibu_kandung' => $row['db_ibu_kandung'],
                'hubungan_keluarga' => $row['db_shdk_id'],
                'nomor_kk' => $row['db_nkk'],
                'no_rt' => $row['db_rt'],
                'no_rw' => $row['db_rw'],
                'dusun' => $row['db_dusun'],
                'alamat' => $row['db_alamat'],
                'village_id' => $row['db_village'],
                'district_id' => $row['db_district'],
                'regency_id' => $row['db_regency'],
                'province_id' => $row['db_province'],
                'created_by' => $row['db_creator'],
                'db_status' => $row['db_status'],
            ];


            // dd($data);
            $msg = [
                'sukses' => view('dtks/data/dtks/clean/modaledit', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function ajax_update()
    {
        // var_dump($this->request->getPost());
        if ($this->request->isAJAX()) {
            // validasi input
            $id_data = $this->request->getVar('id_data');
            $validation = \Config\Services::validation();

            //cek nik
            $nikLama = $this->BnbaModel->find($id_data);
            // var_dump($nikLama);
            // die;
            if ($nikLama['d_nik'] == $this->request->getVar('nomor_nik')) {
                $rule_nik = 'required|numeric|is_unique[dtks_data_clean.d_nik]|min_length[16]|max_length[16]';
            } else {
                $rule_nik = 'required|numeric|min_length[16]|max_length[16]';
            }


            $valid = $this->validate([
                'status' => [
                    'label' => 'Status',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'tanggal_meninggal' => [
                    'label' => 'Tanggal Meninggal',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'no_registrasi_meninggal' => [
                    'label' => 'No. Registrasi Meninggal',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
            ]);


            if (!$valid) {

                $msg = [
                    'error' => [
                        'status' => $validation->getError('status'),
                        'tanggal_meninggal' => $validation->getError('tanggal_meninggal'),
                        'no_registrasi_meninggal' => $validation->getError('no_registrasi_meninggal'),
                    ]
                ];
            } else {
                $dataBnba = [
                    'province_id' => $this->request->getVar('province_id'),
                    'regency_id' => $this->request->getVar('regency_id'),
                    'district_id' => $this->request->getVar('district_id'),
                    'village_id' => $this->request->getVar('village_id'),
                    'd_alamat' => $this->request->getVar('alamat'),
                    'd_dusun' => $this->request->getVar('dusun'),
                    'd_rw' => $this->request->getVar("no_rw"),
                    'd_rt' => $this->request->getVar("no_rt"),
                    'd_nkk' => $this->request->getVar('nomor_kk'),
                    'd_nik' => $this->request->getVar('nomor_nik'),
                    'd_nama' => $this->request->getVar('nama'),
                    'd_tmp_lahir' => $this->request->getVar("tempat_lahir"),
                    'd_tgl_lahir' => $this->request->getVar("tanggal_lahir"),
                    'd_jenkel_id' => $this->request->getVar("jenis_kelamin"),
                    'd_ibu_kandung' => $this->request->getVar("nama_ibu_kandung"),
                    'd_shdk_id' => $this->request->getVar("hubungan_keluarga"),
                    'd_status' => $this->request->getVar('status'),
                    'updated_by' => session()->get('nik'),
                    'updated_at' => date('Y-m-d h:m:s'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];


                $this->BnbaModel->update($id_data, $dataBnba);

                $msg = [
                    'sukses' => 'Data berhasil diupdate',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }
}
