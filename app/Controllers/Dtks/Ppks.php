<?php

namespace App\Controllers\Dtks;


use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\DtksModel;
use App\Models\Dtks\Ppks\PpksModel;
use App\Models\Dtks\Ppks\PpksKatModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;
use App\Models\GenModel;
use App\Models\Dtks\BansosModel;
use App\Models\Dtks\PekerjaanModel;
use App\Models\Dtks\StatusKawinModel;
use App\Models\Dtks\ShdkModel;
use App\Models\Dtks\UsersModel;
use App\Models\Dtks\VeriVali09Model;
use App\Models\Dtks\VervalPbiModel;
use App\Models\Dtks\DisabilitasJenisModel;
use App\Models\Dtks\LembagaModel;
use App\Models\Dtks\CsvReportModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Number;

class Ppks extends BaseController
{
    public function __construct()
    {
        $this->AuthModel = new AuthModel();
        $this->PpksModel = new PpksModel();
        $this->PpksKatModel = new PpksKatModel();
        $this->VeriVali09Model = new VeriVali09Model();
        $this->VervalPbiModel = new VervalPbiModel();
        $this->DisabilitasJenisModel = new DisabilitasJenisModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->GenModel = new GenModel();
        $this->WilayahModel = new WilayahModel();
        $this->BansosModel = new BansosModel();
        $this->PekerjaanModel = new PekerjaanModel();
        $this->StatusKawinModel = new StatusKawinModel();
        $this->CsvReportModel = new CsvReportModel();
    }

    public function index()
    {
        // var_dump(deadline_ppks());
        // die;
        if (session()->get('role_id') == 1) {
            $this->PpksModel = new PpksModel();
            $this->WilayahModel = new WilayahModel();
            $this->RwModel = new RwModel();
            $this->BansosModel = new BansosModel();
            $this->PekerjaanModel = new PekerjaanModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->ShdkModel = new ShdkModel();

            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Data PPKS',
                'user_login' => $this->AuthModel->getUserId(),
                'dtks' => $this->PpksModel->getDtks(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'bansos' => $this->BansosModel->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $this->StatusKawinModel->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $this->ShdkModel->findAll(),
                'percentages' => $this->VervalPbiModel->jml_persentase(),
                'statusRole' => $this->GenModel->getStatusRole(),
            ];

            return view('dtks/data/ppks/usulan/tables', $data);
        } else if (session()->get('role_id') >= 1) {
            $this->PpksModel = new PpksModel();
            $this->WilayahModel = new WilayahModel();
            $this->RwModel = new RwModel();
            $this->BansosModel = new BansosModel();
            $this->ShdkModel = new ShdkModel();

            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Data PPKS',
                'user_login' => $this->AuthModel->getUserId(),
                'dtks' => $this->PpksModel->getDtks(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $this->StatusKawinModel->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $this->ShdkModel->findAll(),
                'percentages' => $this->VervalPbiModel->jml_persentase(),
                'statusRole' => $this->GenModel->getStatusRole(),
            ];

            return view('dtks/data/ppks/usulan/tables', $data);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function tabel_data()
    {
        // var_dump(deadline_ppks());

        $this->PpksModel = new PpksModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        // $role = session()->get('role_id');

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('bansos');
        $filter5 = $this->request->getPost('data_tahun');
        $filter6 = $this->request->getPost('data_bulan');
        $filter7 = '0';

        $listing = $this->PpksModel->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7);
        $jumlah_semua = $this->PpksModel->jumlah_semua();
        $jumlah_filter = $this->PpksModel->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->ppks_nik;
            $row[] = '<a href=' . ppks_foto($key->ppks_foto, '') . ' data-lightbox="dataUsulan' . $key->ppks_nik . '"' . ' data-title="Foto Identitas" style="text-decoration:none;">' . $key->ppks_nama . '</a>
            ';
            $row[] = $key->ppks_nokk;
            if ($key->ppks_tgl_lahir == '0000-00-00') {
                $row[] = '-';
            } elseif ($key->ppks_tgl_lahir == null) {
                $row[] = '-';
            } else {
                // date_format
                $row[] = date('d/m/Y', strtotime($key->ppks_tgl_lahir));
            }
            $row[] = $key->dbj_nama_bansos;
            $row[] = '<a href="https://wa.me/' . nope($key->nope) . '" target="_blank" style="text-decoration:none;">' . strtoupper($key->fullname) . '</a>';
            $row[] = $key->ppks_updated_at;
            $row[] = '<a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->id_ppks . "'" . ')"><i class="far fa-edit"></i></a> | 
                <button class="btn btn-sm btn-secondary" data-id="' . $key->id_ppks . '" data-nama="' . $key->ppks_nama . '" id="deleteBtn"><i class="far fa-trash-alt"></i></button>';

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

    public function tabel_padan()
    {
        // var_dump(deadline_usulan());

        $this->PpksModel = new PpksModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa01');
        $filter2 = $this->request->getPost('rw01');
        $filter3 = $this->request->getPost('rt01');
        $filter4 = $this->request->getPost('bansos01');
        $filter5 = $this->request->getPost('data_tahun01');
        $filter6 = $this->request->getPost('data_bulan01');
        $filter7 = '1';

        $listing = $this->PpksModel->get_datatables01($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7);
        $jumlah_semua = $this->PpksModel->jumlah_semua01();
        $jumlah_filter = $this->PpksModel->jumlah_filter01($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->ppks_nik;
            $row[] = '<a href=' . ppks_foto($key->ppks_foto, '') . ' data-lightbox="dataUsulan' . $key->ppks_nik . '"' . ' data-title="Foto Identitas" style="text-decoration:none;">' . $key->ppks_nama . '</a>
            ';
            $row[] = $key->ppks_nokk;
            if ($key->ppks_tgl_lahir == '0000-00-00') {
                $row[] = '-';
            } elseif ($key->ppks_tgl_lahir == null) {
                $row[] = '-';
            } else {
                // date_format
                $row[] = date('d/m/Y', strtotime($key->ppks_tgl_lahir));
            }
            $row[] = $key->dbj_nama_bansos;
            $row[] = '<a href="https://wa.me/' . nope($key->nope) . '" target="_blank" style="text-decoration:none;">' . strtoupper($key->fullname) . '</a>';
            $row[] = $key->ppks_updated_at;
            $row[] = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="View" onclick="view_person(' . "'" . $key->id_ppks . "'" . ')"><i class="fas fa-eye"></i></a>';

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

    public function formtambah()
    {
        if ($this->request->isAJAX()) {

            $this->PpksModel = new PpksModel();
            $this->WilayahModel = new WilayahModel();
            $rw = new RwModel();
            $this->BansosModel = new BansosModel();
            $this->PekerjaanModel = new PekerjaanModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->ShdkModel = new ShdkModel();
            $users = new UsersModel();
            $DisabilitasJenisModel = new DisabilitasJenisModel();
            $GenModel = new GenModel();

            $data = [
                'title' => 'Form. Tambah Data',
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'ppks_kategori' => $this->PpksKatModel->findAll(),
                'users' => $users->findAll(),
            ];
            if (deadline_ppks() === 1) {

                // alert(\'Mohon Maaf, Batas waktu untuk Tambah Data Telah Habis!!\');
                // Swal.fire({
                //     icon: "error",
                //     title: "Oops...",
                //     text: "Something went wrong!",
                //     footer: "<a href="">Why do I have this issue?</a>"
                // })
                $msg = [
                    'data' =>
                    '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Ops...",
                                text: "Akses Tidak Sesuai!",
                                })
                        </script>'
                ];
                echo json_encode($msg);
            } else {
                $msg = [
                    'data' => view('dtks/data/ppks/usulan/modaltambah', $data),
                ];
                echo json_encode($msg);
            }
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function save()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());
            // validasi input
            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'ppks_nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[ppks_data.ppks_nik,ppks_id,{ppks_id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'ppks_kategori_id' => [
                    'label' => 'Kategori PPKS',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                    ]
                ],
                'databansos' => [
                    'label' => 'Program Bansos',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                    ]
                ],
                'ppks_nokk' => [
                    'label' => 'No. KK',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang'
                    ]
                ],
                'ppks_nama' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'ppks_tempat_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'ppks_tgl_lahir' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'ppks_no_telp' => [
                    'label' => 'No. Telp/HP',
                    'rules' => 'numeric',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'ppks_status_panti' => [
                    'label' => 'Status Panti',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'ppks_jenis_kelamin' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'ppks_status_keberadaan' => [
                    'label' => 'Status Keberadaan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'alamat' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'datart' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'datarw' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'ppks_foto' => [
                    'label' => 'Foto Depan Rumah',
                    'rules' => 'uploaded[ppks_foto]|is_image[ppks_foto]|mime_in[ppks_foto,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => '{field} harus ada.',
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
                'du_latitude' => [
                    'label' => 'Garis Lintang',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus terisi.'
                    ]
                ],
                'du_longitude' => [
                    'label' => 'Garis Bujur',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus terisi.'
                    ]
                ],
            ]);
            if (!$valid) {

                $msg = [
                    'error' => [
                        'ppks_kategori_id' => $validation->getError('ppks_kategori_id'),
                        'ppks_nik' => $validation->getError('ppks_nik'),
                        'databansos' => $validation->getError('databansos'),
                        'ppks_nokk' => $validation->getError('ppks_nokk'),
                        'ppks_nama' => $validation->getError('ppks_nama'),
                        'ppks_tempat_lahir' => $validation->getError('ppks_tempat_lahir'),
                        'ppks_tgl_lahir' => $validation->getError('ppks_tgl_lahir'),
                        'ppks_status_panti' => $validation->getError('ppks_status_panti'),
                        'ppks_jenis_kelamin' => $validation->getError('ppks_jenis_kelamin'),
                        'ppks_status_keberadaan' => $validation->getError('ppks_status_keberadaan'),
                        'ppks_no_telp' => $validation->getError('ppks_no_telp'),
                        'alamat' => $validation->getError('alamat'),
                        'datart' => $validation->getError('datart'),
                        'datarw' => $validation->getError('datarw'),
                        'kelurahan' => $validation->getError('kelurahan'),
                        // 'shdk' => $validation->getError('shdk'),
                        // 'du_foto_identitas' => $validation->getError('du_foto_identitas'),
                        'ppks_foto' => $validation->getError('ppks_foto'),
                        'du_latitude' => $validation->getError('du_latitude'),
                        'du_longitude' => $validation->getError('du_longitude'),
                        'ppks_created_by' => $validation->getError('ppks_created_by'),
                    ]
                ];
            } else {

                $kode_desa = session()->get('kode_desa');
                $namaDesa = $this->WilayahModel->getVillage($kode_desa);
                $desaNama = $namaDesa['name'];

                $ppks_foto = $this->request->getFile('ppks_foto');
                // $du_foto_identitas = $this->request->getFile('du_foto_identitas');

                // var_dump($dd_foto_cpm);
                // die;
                $buat_tanggal = date_create($this->request->getVar('ppks_updated_at'));
                $filename_dua = 'PPKS_' . $this->request->getPost('ppks_nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
                // $filename_empat = 'PPKS_' . $this->request->getPost('ppks_nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
                // var_dump($filename_dua);
                // die;

                $img_dua = imagecreatefromjpeg($ppks_foto);
                // $img_empat = imagecreatefromjpeg($du_foto_identitas);

                // get width and height of image

                $width_dua = imagesx($img_dua);
                $height_dua = imagesy($img_dua);

                // $width_empat = imagesx($img_empat);
                // $height_empat = imagesy($img_empat);

                // reorient image if width is greater than height
                if ($width_dua > $height_dua) {
                    $img_dua = imagerotate($img_dua, -90, 0);
                }
                // if ($width_empat > $height_empat) {
                //     $img_empat = imagerotate($img_empat, -90, 0);
                // }
                // resize image
                $img_dua = imagescale($img_dua, 480, 640);
                // $img_empat = imagescale($img_empat, 480, 640);

                $txtNik = $this->request->getPost('ppks_nik');
                $txtNama = strtoupper($this->request->getPost('ppks_nama'));
                $txtAlamat = strtoupper($this->request->getPost('alamat') . ' RT/RW ' . $this->request->getPost('datart') . "/" . $this->request->getPost('datarw'));
                $txtKelurahan = $desaNama;
                $txtKecamatan = 'PAKENJENG';
                $txtKabupaten = 'GARUT';
                $txtProvinsi = 'JAWA BARAT';
                $txtLat = $this->request->getPost('du_latitude');
                $txtLang = $this->request->getPost('du_longitude');
                date_default_timezone_set('Asia/Jakarta');
                $txtTimestap = date("d M Y H:i:s");

                $txt = "NIK : " . $txtNik . "\nNama : " . $txtNama . "\nAlamat : " . $txtAlamat . "\n"  . $txtKelurahan . ", " . $txtKecamatan . ", " . $txtKabupaten . ", " . $txtProvinsi . "\nLokasi : " . $txtLat . ", " . $txtLang . "\nDibuat pada : " . $txtTimestap . "
                \n@" . nameApp() . " Kec. " . ucwords(strtolower(Profil_Admin()['namaKec']));
                $fontFile = FCPATH . 'assets/fonts/Futura Bk BT Book.ttf';

                $fontSizeDua = 0.020 * imagesx($img_dua);
                $whiteDua = imagecolorallocate($img_dua, 255, 255, 255);
                // $strokeColorDua = imagecolorallocate($img_dua, 0, 0, 0);
                $strokeColorDua = imagecolorallocate($img_dua, 26, 36, 33);

                // pos x from left, pos y from bottom
                $posXdua = 0.02 * imagesx($img_dua);
                $posYdua = 0.80 * imagesy($img_dua);

                // $posX = 10;
                // $posY = 830;
                $angle = 0;

                // stroke watermark image
                imagettfstroketext($img_dua, $fontSizeDua, $angle, $posXdua, $posYdua, $whiteDua, $strokeColorDua, $fontFile, $txt, 1);


                header("Content-type: image/jpg");
                $quality = 90; // 0 to 100

                // var_dump($img_satu);
                // die;

                imagejpeg($img_dua, 'data/ppks_kpm/' . $filename_dua, $quality);
                // imagejpeg($img_empat, 'data/usulan/foto_identitas/' . $filename_empat, $quality);
                // var_dump($img_satu);
                // die;

                $data = [
                    'provinsi' => '32',
                    'kabupaten' => '32.05',
                    'ppks_kecamatan' => '32.05.33',
                    // 'shdk' => $this->request->getVar('shdk'),
                    'ppks_kelurahan' => $this->request->getVar('kelurahan'),
                    'ppks_rw' => $this->request->getVar("datarw"),
                    'ppks_rt' => $this->request->getVar("datart"),
                    'ppks_alamat' => strtoupper($this->request->getVar('alamat')),
                    // 'status_kawin' => $this->request->getVar("status_kawin"),
                    'ppks_kategori_id' => $this->request->getVar("ppks_kategori_id"),
                    'ppks_status_keberadaan' => $this->request->getVar("ppks_status_keberadaan"),
                    'ppks_jenis_kelamin' => $this->request->getVar('ppks_jenis_kelamin'),
                    'ppks_status_panti' => strtoupper($this->request->getVar("ppks_status_panti")),
                    'ppks_tgl_lahir' => $this->request->getVar("ppks_tgl_lahir"),
                    'ppks_tempat_lahir' => strtoupper($this->request->getVar("ppks_tempat_lahir")),
                    'ppks_nama' => strtoupper($this->request->getVar('ppks_nama')),
                    'ppks_nokk' => $this->request->getVar('ppks_nokk'),
                    'ppks_status_bantuan' => $this->request->getVar('databansos'),
                    'ppks_nik' => $this->request->getVar('ppks_nik'),
                    'ppks_no_telp' => $this->request->getVar('ppks_no_telp'),
                    // 'disabil_status' => $this->request->getVar('disabil_status'),
                    // 'disabil_kode' => $this->request->getVar('disabil_jenis'),
                    // 'hamil_status' => $this->request->getVar('status_hamil'),
                    // 'hamil_tgl' => $this->request->getVar('tgl_hamil'),
                    // 'foto_identitas' => $filename_empat,
                    'ppks_foto' => $filename_dua,
                    'ppks_proses' => 0,
                    'ppks_lat' => $this->request->getVar('du_latitude'),
                    'ppks_long' => $this->request->getVar('du_longitude'),
                    // 'sk0' => $this->request->getVar('sk0'),
                    // 'sk1' => $this->request->getVar('sk1'),
                    // 'sk2' => $this->request->getVar('sk2'),
                    // 'sk3' => $this->request->getVar('sk3'),
                    // 'sk4' => $this->request->getVar('sk4'),
                    // 'sk5' => $this->request->getVar('sk5'),
                    // 'sk6' => $this->request->getVar('sk6'),
                    // 'sk7' => $this->request->getVar('sk7'),
                    // 'sk8' => $this->request->getVar('sk8'),
                    // 'sk9' => $this->request->getVar('sk9'),
                    // 'du_so_id' => $this->request->getVar('du_so_id'),
                    'ppks_created_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                    'ppks_updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                    'ppks_created_at_year' => date('Y'),
                    'ppks_created_at_month' => date('n'),
                    'ppks_created_by' => session()->get('nik'),
                    'ppks_updated_by' => session()->get('nik'),

                    // 'foto_rumah' => $nama_foto_rumah,
                ];
                // dd($data);
                $this->PpksModel->save($data);

                $msg = [
                    'sukses' => 'Data berhasil ditambahkan',
                ];
            }
            echo json_encode($msg);


            // session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');


            // echo json_encode(array("status" => true));
            // return redirect()->to('/ppks/usulan/tables');
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function delete()
    {
        if ($this->request->isAJAX()) {


            // if (deadline_ppks() == 1) {
            //     $msg = [
            //         'informasi' => 'Mohon Maaf, Batas waktu untuk Perubahan Data, Telah Habis!!'
            //     ];
            // } else {
            $id = $this->request->getVar('id');

            $ppks = $this->PpksModel->find($id);
            unlink('data/ppks_kpm/' . $ppks['ppks_foto']);

            $this->PpksModel->delete($id);
            $msg = [
                'sukses' => 'Data berhasil dihapus'
            ];
            // }
            echo json_encode($msg);
            // } else {
            //     $data = [
            //         'title' => 'Access denied',
            //     ];

            //     return redirect()->to('lockscreen');
        }
    }

    public function formedit()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());

            // if (deadline_ppks() == 1) {
            //     $msg = [
            //         'informasi' => 'Mohon Maaf, Batas waktu untuk Perubahan Data Telah Habis!!'
            //     ];
            //     echo json_encode($msg);
            // } else {
            $this->PekerjaanModel = new PekerjaanModel();
            $this->ShdkModel = new ShdkModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->WilayahModel = new WilayahModel();
            $this->BansosModel = new BansosModel();
            $DisabilitasJenisModel = new DisabilitasJenisModel();
            $users = new UsersModel();
            $GenModel = new GenModel();

            $id = $this->request->getVar('id');
            $model = new PpksModel();
            $row = $model->find($id);
            // dd($id);

            $data = [
                'title' => 'Form. Edit Data',
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'rw' => $this->RwModel->noRw(),
                'rt' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'users' => $users->findAll(),
                'jenkel' => $this->GenModel->getDataJenkel(),
                'kategori_ppks' => $this->PpksKatModel->findAll(),

                'id' => $row['ppks_id'],
                'ppks_kategori_id' => $row['ppks_kategori_id'],
                'kelurahan' => $row['ppks_kelurahan'],
                'datarw' => $row['ppks_rw'],
                'datart' => $row['ppks_rt'],
                'alamat' => $row['ppks_alamat'],
                'ppks_jenis_kelamin' => $row['ppks_jenis_kelamin'],
                'ppks_no_telp' => $row['ppks_no_telp'],
                'ppks_tgl_lahir' => $row['ppks_tgl_lahir'],
                'ppks_tempat_lahir' => $row['ppks_tempat_lahir'],
                'ppks_nama' => $row['ppks_nama'],
                'ppks_nokk' => $row['ppks_nokk'],
                'ppks_status_keberadaan' => $row['ppks_status_keberadaan'],
                'ppks_status_panti' => $row['ppks_status_panti'],
                'databansos' => $row['ppks_status_bantuan'],
                'ppks_nik' => $row['ppks_nik'],
                'ppks_id' => $row['ppks_id'],
                'ppks_foto' => $row['ppks_foto'],
                'ppks_proses' => $row['ppks_proses'],
                'ppks_updated_at' => $row['ppks_updated_at'],
                'ppks_created_by' => session()->get('ppks_nik'),
                // 'foto_rumah' => $nama_foto_rumah,
            ];

            $msg = [
                'sukses' => view('dtks/data/ppks/usulan/modaledit', $data)
            ];
            echo json_encode($msg);
            // }
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function formview()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());

            // if (deadline_ppks() == 1) {
            //     $msg = [
            //         'informasi' => 'Mohon Maaf, Batas waktu untuk Perubahan Data Telah Habis!!'
            //     ];
            //     echo json_encode($msg);
            // } else {
            $this->PekerjaanModel = new PekerjaanModel();
            $this->ShdkModel = new ShdkModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->WilayahModel = new WilayahModel();
            $this->BansosModel = new BansosModel();
            $DisabilitasJenisModel = new DisabilitasJenisModel();
            $users = new UsersModel();
            $GenModel = new GenModel();

            $id = $this->request->getVar('id');
            $model = new PpksModel();
            $row = $model->find($id);
            // dd($id);

            $data = [
                'title' => 'Form. View Data',
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'rw' => $this->RwModel->noRw(),
                'rt' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'users' => $users->findAll(),
                'jenkel' => $this->GenModel->getDataJenkel(),
                'kategori_ppks' => $this->PpksKatModel->findAll(),

                'id' => $row['ppks_id'],
                'ppks_kategori_id' => $row['ppks_kategori_id'],
                'kelurahan' => $row['ppks_kelurahan'],
                'datarw' => $row['ppks_rw'],
                'datart' => $row['ppks_rt'],
                'alamat' => $row['ppks_alamat'],
                'ppks_jenis_kelamin' => $row['ppks_jenis_kelamin'],
                'ppks_no_telp' => $row['ppks_no_telp'],
                'ppks_tgl_lahir' => $row['ppks_tgl_lahir'],
                'ppks_tempat_lahir' => $row['ppks_tempat_lahir'],
                'ppks_nama' => $row['ppks_nama'],
                'ppks_nokk' => $row['ppks_nokk'],
                'ppks_status_keberadaan' => $row['ppks_status_keberadaan'],
                'ppks_status_panti' => $row['ppks_status_panti'],
                'databansos' => $row['ppks_status_bantuan'],
                'ppks_nik' => $row['ppks_nik'],
                'ppks_id' => $row['ppks_id'],
                'ppks_foto' => $row['ppks_foto'],
                'ppks_proses' => $row['ppks_proses'],
                'ppks_updated_at' => $row['ppks_updated_at'],
                'ppks_created_by' => session()->get('ppks_nik'),
                // 'foto_rumah' => $nama_foto_rumah,
            ];

            $msg = [
                'sukses' => view('dtks/data/ppks/usulan/modalview', $data)
            ];
            echo json_encode($msg);
            // }
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function update()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());
            //cek nik
            $id = $this->request->getVar('ppks_id');
            // $validation = \Config\Services::validation();
            // $valid = $this->validate([
            //     'nik' => [
            //         'label' => 'NIK',
            //         'rules' => 'required|numeric|is_unique[dtks_usulan_view.du_nik,du_id,{id}]|min_length[16]|max_length[16]',
            //         'errors' => [
            //             'required' => '{field} harus diisi.',
            //             'numeric' => '{field} harus berisi angka.',
            //             'is_unique' => '{field} sudah terdaftar.',
            //             'min_length' => '{field} terlalu pendek',
            //             'max_length' => '{field} terlalu panjang',
            //         ]
            //     ],
            //     'databansos' => [
            //         'label' => 'Program Bansos',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus dipilih.',
            //         ]
            //     ],
            //     'nokk' => [
            //         'label' => 'No. KK',
            //         'rules' => 'required|numeric|min_length[16]|max_length[16]',
            //         'errors' => [
            //             'required' => '{field} harus diisi.',
            //             'numeric' => '{field} harus berisi angka.',
            //             'is_unique' => '{field} sudah terdaftar.',
            //             'min_length' => '{field} terlalu pendek',
            //             'max_length' => '{field} terlalu panjang'
            //         ]
            //     ],
            //     'nama' => [
            //         'label' => 'Nama Lengkap',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus diisi.',
            //             'alpha_numeric_punct' => '{field} harus berisi alphabet.'
            //         ]
            //     ],
            //     'tempat_lahir' => [
            //         'label' => 'Tempat Lahir',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus diisi.',
            //             'alpha_numeric_punct' => '{field} harus berisi alphabet.'
            //         ]
            //     ],
            //     'tanggal_lahir' => [
            //         'label' => 'Tanggal Lahir',
            //         'rules' => 'required|valid_date',
            //         'errors' => [
            //             'required' => '{field} harus diisi.',
            //             'valid_date' => '{field} tidak valid.'
            //         ]
            //     ],
            //     'ibu_kandung' => [
            //         'label' => 'Ibu Kandung',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus diisi.'
            //         ]
            //     ],
            //     'jenis_kelamin' => [
            //         'label' => 'Jenis Kelamin',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus dipilih.'
            //         ]
            //     ],
            //     'jenis_pekerjaan' => [
            //         'label' => 'Jenis Pekerjaan',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus dipilih.'
            //         ]
            //     ],
            //     'status_kawin' => [
            //         'label' => 'Status Perkawinan',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus dipilih.'
            //         ]
            //     ],
            //     'alamat' => [
            //         'label' => 'Alamat',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus diisi.'
            //         ]
            //     ],
            //     'datart' => [
            //         'label' => 'No. RT',
            //         'rules' => 'required|numeric',
            //         'errors' => [
            //             'required' => '{field} harus dipilih.',
            //             'numeric' => '{field} harus berisi angka.'
            //         ]
            //     ],
            //     'datarw' => [
            //         'label' => 'No. RW',
            //         'rules' => 'required|numeric',
            //         'errors' => [
            //             'required' => '{field} harus dipilih.',
            //             'numeric' => '{field} harus berisi angka.'
            //         ]
            //     ],
            //     'shdk' => [
            //         'label' => 'SHDK',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus dipilih.'
            //         ]
            //     ],
            //     'du_foto_identitas' => [
            //         'label' => 'Foto Identitas',
            //         'rules' => 'is_image[du_foto_identitas]|mime_in[du_foto_identitas,image/jpg,image/jpeg,image/png]',
            //         'errors' => [
            //             'is_image' => '{field} harus berupa gambar.',
            //             'mime_in' => '{field} harus berekstensi gambar.',
            //             'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
            //         ]
            //     ],
            //     'du_foto_rumah' => [
            //         'label' => 'Foto Depan Rumah',
            //         'rules' => 'is_image[du_foto_rumah]|mime_in[du_foto_rumah,image/jpg,image/jpeg,image/png]',
            //         'errors' => [
            //             'is_image' => '{field} harus berupa gambar.',
            //             'mime_in' => '{field} harus berekstensi gambar.',
            //             'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
            //         ]
            //     ],
            //     'du_latitude' => [
            //         'label' => 'Garis Lintang',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus terisi.'
            //         ]
            //     ],
            //     'du_longitude' => [
            //         'label' => 'Garis Bujur',
            //         'rules' => 'required',
            //         'errors' => [
            //             'required' => '{field} harus terisi.'
            //         ]
            //     ],
            // ]);
            // if (!$valid) {
            //     $msg = [
            //         'error' => [
            //             'nik' => $validation->getError('nik'),
            //             'databansos' => $validation->getError('databansos'),
            //             'nokk' => $validation->getError('nokk'),
            //             'nama' => $validation->getError('nama'),
            //             'tempat_lahir' => $validation->getError('tempat_lahir'),
            //             'tanggal_lahir' => $validation->getError('tanggal_lahir'),
            //             'ibu_kandung' => $validation->getError('ibu_kandung'),
            //             'jenis_kelamin' => $validation->getError('jenis_kelamin'),
            //             'jenis_pekerjaan' => $validation->getError('jenis_pekerjaan'),
            //             'status_kawin' => $validation->getError('status_kawin'),
            //             'alamat' => $validation->getError('alamat'),
            //             'datart' => $validation->getError('datart'),
            //             'datarw' => $validation->getError('datarw'),
            //             'kelurahan' => $validation->getError('kelurahan'),
            //             'shdk' => $validation->getError('shdk'),
            //             'du_foto_identitas' => $validation->getError('du_foto_identitas'),
            //             'du_foto_rumah' => $validation->getError('du_foto_rumah'),
            //             'du_latitude' => $validation->getError('du_latitude'),
            //             'du_longitude' => $validation->getError('du_longitude'),
            //             'created_by' => $validation->getError('created_by'),
            //         ]
            //     ];
            // } else {

            $duFotoRumah = $_FILES['ppks_foto']['name'];

            if ($duFotoRumah != NULL) {

                $usulan = $this->PpksModel->find($id);

                $du_fotorumah_old = $usulan['ppks_foto'];

                $dir_rumah = 'data/ppks_kpm/' . $du_fotorumah_old;

                unlink($dir_rumah);

                $kode_desa = session()->get('kode_desa');
                $namaDesa = $this->WilayahModel->getVillage($kode_desa);
                $desaNama = $namaDesa['name'];

                $ppks_foto = $this->request->getFile('ppks_foto');

                // var_dump($dd_foto_cpm);
                // die;
                $buat_tanggal = date_create($this->request->getVar('ppks_updated_at'));
                $filename_dua = 'PPKS_' . $this->request->getPost('ppks_nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';

                $img_dua = imagecreatefromjpeg($ppks_foto);

                // get width and height of image

                $width_dua = imagesx($img_dua);
                $height_dua = imagesy($img_dua);

                // reorient image if width is greater than height
                if ($width_dua > $height_dua) {
                    $img_dua = imagerotate($img_dua, -90, 0);
                }

                // resize image
                $img_dua = imagescale($img_dua, 480, 640);

                $txtNik = $this->request->getPost('nik');
                $txtNama = strtoupper($this->request->getPost('nama'));
                $txtAlamat = strtoupper($this->request->getPost('alamat') . ' RT/RW ' . $this->request->getPost('datart') . "/" . $this->request->getPost('datarw'));
                $txtKelurahan = $desaNama;
                $txtKecamatan = 'PAKENJENG';
                $txtKabupaten = 'GARUT';
                $txtProvinsi = 'JAWA BARAT';
                $txtLat = $this->request->getPost('du_latitude');
                $txtLang = $this->request->getPost('du_longitude');
                date_default_timezone_set('Asia/Jakarta');
                $txtTimestap = date("d M Y H:i:s");

                $txt = "NIK : " . $txtNik . "\nNama : " . $txtNama . "\nAlamat : " . $txtAlamat . "\n"  . $txtKelurahan . ", " . $txtKecamatan . ", " . $txtKabupaten . ", " . $txtProvinsi . "\nLokasi : " . $txtLat . ", " . $txtLang . "\nDibuat pada : " . $txtTimestap . "\n@" . nameApp() . " Kec. " . ucwords(strtolower(Profil_Admin()['namaKec']));
                $fontFile = FCPATH . 'assets/fonts/Futura Bk BT Book.ttf';

                $fontSizeDua = 0.020 * imagesx($img_dua);
                $whiteDua = imagecolorallocate($img_dua, 255, 255, 255);
                // $strokeColorDua = imagecolorallocate($img_dua, 0, 0, 0);
                $strokeColorDua = imagecolorallocate($img_dua, 26, 36, 33);

                // pos x from left, pos y from bottom
                $posXdua = 0.02 * imagesx($img_dua);
                $posYdua = 0.80 * imagesy($img_dua);

                // $posX = 10;
                // $posY = 830;
                $angle = 0;

                // stroke watermark image
                imagettfstroketext($img_dua, $fontSizeDua, $angle, $posXdua, $posYdua, $whiteDua, $strokeColorDua, $fontFile, $txt, 1);


                header("Content-type: image/jpg");
                $quality = 90; // 0 to 100

                // var_dump($img_satu);
                // die;

                imagejpeg($img_dua, 'data/ppks_kpm/' . $filename_dua, $quality);
                // var_dump($img_satu);
                // die;

                $data = [
                    'provinsi' => '32',
                    'kabupaten' => '32.05',
                    'kecamatan' => '32.05.33',
                    'ppks_kelurahan' => $this->request->getVar('kelurahan'),
                    'ppks_rw' => $this->request->getVar("datarw"),
                    'ppks_rt' => $this->request->getVar("datart"),
                    'ppks_alamat' => strtoupper($this->request->getVar('alamat')),
                    'ppks_status_panti' => $this->request->getVar("ppks_status_panti"),
                    'ppks_status_keberadaan' => $this->request->getVar("ppks_status_keberadaan"),
                    'ppks_jenis_kelamin' => $this->request->getVar('ppks_jenis_kelamin'),
                    'ppks_no_telp' => strtoupper($this->request->getVar("ppks_no_telp")),
                    'ppks_tgl_lahir' => $this->request->getVar("ppks_tgl_lahir"),
                    'ppks_tempat_lahir' => strtoupper($this->request->getVar("ppks_tempat_lahir")),
                    'ppks_nama' => strtoupper($this->request->getVar('ppks_nama')),
                    'ppks_nokk' => $this->request->getVar('ppks_nokk'),
                    'ppks_status_bantuan' => $this->request->getVar('databansos'),
                    'ppks_nik' => $this->request->getVar('ppks_nik'),
                    'ppks_foto' => $filename_dua,
                    'ppks_lat' => $this->request->getVar('du_latitude'),
                    'ppks_long' => $this->request->getVar('du_longitude'),
                    'ppks_updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                    'ppks_updated_by' => session()->get('nik'),
                    'ppks_proses' => $this->request->getVar('ppks_proses'),

                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $this->PpksModel->update($id, $data);

                $msg = [
                    'sukses' => 'Data berhasil diubah',
                ];
            } else {

                $buat_tanggal = date_create($this->request->getVar('ppks_updated_at'));
                // $filename_dua = 'DUDFH_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
                // $filename_empat = 'DUDID_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';

                $data = [
                    'provinsi' => '32',
                    'kabupaten' => '32.05',
                    'kecamatan' => '32.05.33',
                    'ppks_kelurahan' => $this->request->getVar('kelurahan'),
                    'ppks_rw' => $this->request->getVar("datarw"),
                    'ppks_rt' => $this->request->getVar("datart"),
                    'ppks_alamat' => strtoupper($this->request->getVar('alamat')),
                    'ppks_status_panti' => $this->request->getVar("ppks_status_panti"),
                    'ppks_status_keberadaan' => $this->request->getVar("ppks_status_keberadaan"),
                    'ppks_jenis_kelamin' => $this->request->getVar('ppks_jenis_kelamin'),
                    'ppks_no_telp' => strtoupper($this->request->getVar("ppks_no_telp")),
                    'ppks_tgl_lahir' => $this->request->getVar("ppks_tgl_lahir"),
                    'ppks_tempat_lahir' => strtoupper($this->request->getVar("ppks_tempat_lahir")),
                    'ppks_nama' => strtoupper($this->request->getVar('ppks_nama')),
                    'ppks_nokk' => $this->request->getVar('ppks_nokk'),
                    'ppks_status_bantuan' => $this->request->getVar('databansos'),
                    'ppks_nik' => $this->request->getVar('ppks_nik'),
                    'ppks_lat' => $this->request->getVar('du_latitude'),
                    'ppks_long' => $this->request->getVar('du_longitude'),
                    'ppks_updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                    'ppks_updated_by' => session()->get('nik'),
                    'ppks_proses' => $this->request->getVar('ppks_proses'),

                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $this->PpksModel->update($id, $data);

                $msg = [
                    'sukses' => 'Data berhasil diubah',
                ];
            }
            echo json_encode($msg);
        }
        // } else {
        //     return redirect()->to('lockscreen');
        // }
    }

    function downIden($id)
    {
        $url = 'data/usulan/foto_identitas/' . $id;

        // Use basename() function to return the base name of file
        $file_name = basename($url);

        // Use file_get_contents() function to get the file
        // from url and use file_put_contents() function to
        // save the file by using base name
        file_put_contents($file_name, file_get_contents($url));

        // $usulan = new Usulan22Model();
        // $data = $usulan->find($id);
        // return $this->response->download('data/usulan/foto_identitas/' . $data->usulan, null);
    }

    function export()
    {

        $wilayahModel = new WilayahModel();
        // $model = new Usulan22Model();
        // $tmbExpData = $this->request->getVar('btnExpData');
        // $tmbExpAll = $this->request->getVar('btnExpAll');
        $filter1 = $this->request->getPost('desa');
        $filter4 = $this->request->getPost('bansos');
        $filter5 = $this->request->getPost('data_tahun');
        $filter6 = $this->request->getPost('data_bulan');
        $filter7 = 0;

        // dd(array($filter1, $filter4, $filter5, $filter6));
        // if (isset($tmbExpData)) {
        // if ($filter4 == null || $filter5 == null || $filter6 == null) {

        //     session()->setFlashdata('message', '<strong>Syarat Export</strong>: [-NAMA DESA, -JENIS PROGRAM, -TAHUN dan -BULAN] TIDAK BOLEH KOSONG!!');
        //     return redirect()->to('/ppks/usulan22');
        // } else {
        // dd($filter1, $filter4, $filter5, $filter6);

        $data = $this->PpksModel->dataExport($filter1, $filter4, $filter5, $filter6, $filter7)->getResultArray();
        // dd($data);

        $wilayahModel = $wilayahModel->getVillage($filter1);
        // $bulan = array(
        //     1 =>   'Januari',
        //     'Februari',
        //     'Maret',
        //     'April',
        //     'Mei',
        //     'Juni',
        //     'Juli',
        //     'Agustus',
        //     'September',
        //     'Oktober',
        //     'November',
        //     'Desember'
        // );
        // $file_name = 'TEMPLATE_PENGUSULAN_PAKENJENG - ' . $wilayahModel['name'] . ' - ' . $filter4 . '.xlsx';
        $file_name = 'Template-PPKS-Kec-Pakenjeng-' .  ucwords(strtolower($wilayahModel['name'])) . '.xlsx';
        // $file_name = 'Template-PPKS-Kec.xlsx';
        require '../vendor/autoload.php';
        $spreadsheet = new Spreadsheet();

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ppkskategori_id');
        $sheet->setCellValue('B1', 'nama');
        $sheet->setCellValue('C1', 'alamat');
        $sheet->setCellValue('D1', 'nik');
        $sheet->setCellValue('E1', 'nokk');
        $sheet->setCellValue('F1', 'jenis_kelamin');
        $sheet->setCellValue('G1', 'tempat_lahir');
        $sheet->setCellValue('H1', 'tgl_lahir');
        $sheet->setCellValue('I1', 'no_telp');
        $sheet->setCellValue('J1', 'status_keberadaan');
        $sheet->setCellValue('K1', 'status_bantuan');
        $sheet->setCellValue('L1', 'status_panti');
        // $sheet->setCellValue('M1', 'tgl_out');
        $sheet->setCellValue('M1', 'foto');
        // $sheet->setCellValue('O1', 'KABUPATEN');
        // $sheet->setCellValue('P1', 'KECAMATAN');
        // $sheet->setCellValue('Q1', 'KELURAHAN');
        // $sheet->setCellValue('R1', 'STATUS DISABILITAS');
        // $sheet->setCellValue('S1', 'KODE JENIS DISABILITAS');
        // $sheet->setCellValue('T1', 'STATUS HAMIL');
        // $sheet->setCellValue('U1', "TGL MULAI HAMIL\n(31/12/2021)");

        $styleArray = [
            // 'font' => [
            //     'bold' => true,
            //     'color' => array('rgb' => 'FFFFFF'),
            // ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'wrapText'     => TRUE,
            ],
            'borders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            // 'fill' => [
            //     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            //     // 'rotation' => 90,
            //     'startColor' => [
            //         'rgb' => '4472C4',
            //     ],
            //     'endColor' => [
            //         'rgb' => '4472C4',
            //     ],
            // ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleArray);

        // // menetapkan format tanggal pada sel H
        // $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

        $count = 2;

        foreach ($data as $row) {

            $formatTgl = date_create($row['ppks_tgl_lahir']);
            // $tglLahir = date_format($formatTgl, 'Y/m/d');
            $excelDateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($formatTgl);
            // $tglLahir = date('Y/m/d', strtotime($row['ppks_tgl_lahir']));
            // if ($row['hamil_status'] == 1) {
            //     $status_hamil = 'YA';
            // } elseif ($row['hamil_status'] == 2) {
            //     $status_hamil = 'TIDAK';
            // } else {
            //     $status_hamil = '';
            // }

            // if ($row['hamil_tgl'] > 1) {
            //     $hamil_tgl = date('d/m/Y', strtotime($row['hamil_tgl']));
            // } else {
            //     $hamil_tgl = '';
            // }

            // $TglBuat = date('m/Y', strtotime($row['created_at']));

            $sheet->setCellValue('A' . $count, $row['ppks_kategori_id']);
            $sheet->setCellValue('B' . $count, strtoupper($row['ppks_nama']));
            $sheet->setCellValue('C' . $count, strtoupper($row['ppks_alamat'] . " RT " . $row['ppks_rt'] . " RW " . $row['ppks_rw']));
            // $sheet->setCellValue('C' . $count, strtoupper($row['ppks_alamat']));
            $sheet->setCellValueExplicit('D' . $count, $row['ppks_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $count, $row['ppks_nokk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $count, $row['NamaJenKel']);
            $sheet->setCellValue('G' . $count, strtoupper($row['ppks_tempat_lahir']));
            $sheet->setCellValue('H' . $count, $excelDateValue);

            // Apply date format to cell A1
            $sheet->getStyle('H' . $count)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

            $sheet->setCellValue('I' . $count, $row['ppks_no_telp']);
            $sheet->setCellValue('J' . $count, $row['psk_nama_status']);
            if ($row['ppks_status_bantuan'] != 4) {
                $sheet->setCellValue('K' . $count, 'YA');
            } else {
                $sheet->setCellValue('K' . $count, 'TIDAK');
            }
            $sheet->setCellValue('L' . $count, $row['pp_status_panti']);

            // Set the image data
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName($row['ppks_foto']);
            $drawing->setDescription($row['ppks_foto']);
            $drawing->setPath('data/ppks_kpm/' . $row['ppks_foto']);
            // $drawing->setHeight(50);
            // $drawing->setWidth(50);

            // Get the height and width of the image
            // [$width, $height] = getimagesize('data/ppks_kpm/' . $key->ppks_foto);

            // Get the height and width of the image
            list($width, $height) = getimagesize('data/ppks_kpm/' . $row['ppks_foto']);

            // Set the desired width (5cm in pixels)
            $desiredWidth = 189;

            // Calculate the scale factor
            $scaleFactor = $desiredWidth / $width;

            // Set the height and width of the image using the scale factor
            $drawing->setHeight($height * $scaleFactor);
            $drawing->setWidth($width * $scaleFactor);
            $drawing->setCoordinates('M' . $count);

            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // Set the height of the row to match the height of the image
            $spreadsheet->getActiveSheet()->getRowDimension($count)->setRowHeight($height * $scaleFactor);

            // Mengubah ukuran kolom pada file spreadsheet
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth($width * $scaleFactor);

            // // Set the height of the row to match the height of the image
            // $spreadsheet->getActiveSheet()->getRowDimension($count)->setRowHeight($height);

            // $drawing->setHeight($height);
            // $drawing->setWidth($width);

            // $drawing->setCoordinates('M' . $count);
            // $drawing->setWorksheet($spreadsheet->getActiveSheet());
            // $sheet->setCellValue('T' . $count, $status_hamil);
            // $sheet->setCellValue('U' . $count, $hamil_tgl);

            $count++;
        }

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Form Isian');

        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length:' . filesize($file_name));
        flush();

        readfile($file_name);

        exit;
    }

    function export1()
    {

        $wilayahModel = new WilayahModel();
        // $model = new Usulan22Model();
        // $tmbExpData = $this->request->getVar('btnExpData');
        // $tmbExpAll = $this->request->getVar('btnExpAll');
        $filter1 = $this->request->getPost('desa01');
        $filter4 = $this->request->getPost('bansos01');
        $filter5 = $this->request->getPost('data_tahun01');
        $filter6 = $this->request->getPost('data_bulan01');
        $filter7 = 1;

        // dd(array($filter1, $filter4, $filter5, $filter6));
        // if (isset($tmbExpData)) {
        // if ($filter4 == null || $filter5 == null || $filter6 == null) {

        //     session()->setFlashdata('message', '<strong>Syarat Export</strong>: [-NAMA DESA, -JENIS PROGRAM, -TAHUN dan -BULAN] TIDAK BOLEH KOSONG!!');
        //     return redirect()->to('/ppks/usulan22');
        // } else {
        // dd($filter1, $filter4, $filter5, $filter6);

        $data = $this->PpksModel->dataExport($filter1, $filter4, $filter5, $filter6, $filter7)->getResultArray();
        // dd($data);

        $wilayahModel = $wilayahModel->getVillage($filter1);
        // $bulan = array(
        //     1 =>   'Januari',
        //     'Februari',
        //     'Maret',
        //     'April',
        //     'Mei',
        //     'Juni',
        //     'Juli',
        //     'Agustus',
        //     'September',
        //     'Oktober',
        //     'November',
        //     'Desember'
        // );
        // $file_name = 'TEMPLATE_PENGUSULAN_PAKENJENG - ' . $wilayahModel['name'] . ' - ' . $filter4 . '.xlsx';
        $file_name = 'Template-PPKS-Kec-Pakenjeng-' .  ucwords(strtolower($wilayahModel['name'])) . '.xlsx';
        // $file_name = 'Template-PPKS-Kec.xlsx';
        require '../vendor/autoload.php';
        $spreadsheet = new Spreadsheet();

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ppkskategori_id');
        $sheet->setCellValue('B1', 'nama');
        $sheet->setCellValue('C1', 'alamat');
        $sheet->setCellValue('D1', 'nik');
        $sheet->setCellValue('E1', 'nokk');
        $sheet->setCellValue('F1', 'jenis_kelamin');
        $sheet->setCellValue('G1', 'tempat_lahir');
        $sheet->setCellValue('H1', 'tgl_lahir');
        $sheet->setCellValue('I1', 'no_telp');
        $sheet->setCellValue('J1', 'status_keberadaan');
        $sheet->setCellValue('K1', 'status_bantuan');
        $sheet->setCellValue('L1', 'status_panti');
        // $sheet->setCellValue('M1', 'tgl_out');
        $sheet->setCellValue('M1', 'foto');
        // $sheet->setCellValue('O1', 'KABUPATEN');
        // $sheet->setCellValue('P1', 'KECAMATAN');
        // $sheet->setCellValue('Q1', 'KELURAHAN');
        // $sheet->setCellValue('R1', 'STATUS DISABILITAS');
        // $sheet->setCellValue('S1', 'KODE JENIS DISABILITAS');
        // $sheet->setCellValue('T1', 'STATUS HAMIL');
        // $sheet->setCellValue('U1', "TGL MULAI HAMIL\n(31/12/2021)");

        $styleArray = [
            // 'font' => [
            //     'bold' => true,
            //     'color' => array('rgb' => 'FFFFFF'),
            // ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'wrapText'     => TRUE,
            ],
            'borders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            // 'fill' => [
            //     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            //     // 'rotation' => 90,
            //     'startColor' => [
            //         'rgb' => '4472C4',
            //     ],
            //     'endColor' => [
            //         'rgb' => '4472C4',
            //     ],
            // ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleArray);

        $count = 2;

        foreach ($data as $row) {

            $formatTgl = date_create($row['ppks_tgl_lahir']);
            $tglLahir = date_format($formatTgl, 'Y/m/d');
            // $tglLahir = date('Y/m/d', strtotime($row['ppks_tgl_lahir']));
            // if ($row['hamil_status'] == 1) {
            //     $status_hamil = 'YA';
            // } elseif ($row['hamil_status'] == 2) {
            //     $status_hamil = 'TIDAK';
            // } else {
            //     $status_hamil = '';
            // }

            // if ($row['hamil_tgl'] > 1) {
            //     $hamil_tgl = date('d/m/Y', strtotime($row['hamil_tgl']));
            // } else {
            //     $hamil_tgl = '';
            // }

            // $TglBuat = date('m/Y', strtotime($row['created_at']));

            $sheet->setCellValue('A' . $count, $row['ppks_kategori_id']);
            $sheet->setCellValue('B' . $count, strtoupper($row['ppks_nama']));
            $sheet->setCellValue('C' . $count, strtoupper($row['ppks_alamat'] . " RT " . $row['ppks_rt'] . " RW " . $row['ppks_rw']));
            // $sheet->setCellValue('C' . $count, strtoupper($row['ppks_alamat']));
            $sheet->setCellValueExplicit('D' . $count, $row['ppks_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $count, $row['ppks_nokk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $count, $row['NamaJenKel']);
            $sheet->setCellValue('G' . $count, strtoupper($row['ppks_tempat_lahir']));
            $sheet->setCellValue('H' . $count, $tglLahir);
            $sheet->setCellValue('I' . $count, $row['ppks_no_telp']);
            $sheet->setCellValue('J' . $count, $row['psk_nama_status']);
            if ($row['ppks_status_bantuan'] != 4) {
                $sheet->setCellValue('K' . $count, 'YA');
            } else {
                $sheet->setCellValue('K' . $count, 'TIDAK');
            }
            $sheet->setCellValue('L' . $count, $row['pp_status_panti']);

            // Set the image data
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName($row['ppks_foto']);
            $drawing->setDescription($row['ppks_foto']);
            $drawing->setPath('data/ppks_kpm/' . $row['ppks_foto']);
            // $drawing->setHeight(50);
            // $drawing->setWidth(50);

            // Get the height and width of the image
            // [$width, $height] = getimagesize('data/ppks_kpm/' . $key->ppks_foto);

            // Get the height and width of the image
            list($width, $height) = getimagesize('data/ppks_kpm/' . $row['ppks_foto']);

            // Set the desired width (5cm in pixels)
            $desiredWidth = 189;

            // Calculate the scale factor
            $scaleFactor = $desiredWidth / $width;

            // Set the height and width of the image using the scale factor
            $drawing->setHeight($height * $scaleFactor);
            $drawing->setWidth($width * $scaleFactor);
            $drawing->setCoordinates('M' . $count);

            $drawing->setWorksheet($spreadsheet->getActiveSheet());

            // Set the height of the row to match the height of the image
            $spreadsheet->getActiveSheet()->getRowDimension($count)->setRowHeight($height * $scaleFactor);

            // Mengubah ukuran kolom pada file spreadsheet
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth($width * $scaleFactor);

            // // Set the height of the row to match the height of the image
            // $spreadsheet->getActiveSheet()->getRowDimension($count)->setRowHeight($height);

            // $drawing->setHeight($height);
            // $drawing->setWidth($width);

            // $drawing->setCoordinates('M' . $count);
            // $drawing->setWorksheet($spreadsheet->getActiveSheet());
            // $sheet->setCellValue('T' . $count, $status_hamil);
            // $sheet->setCellValue('U' . $count, $hamil_tgl);

            $count++;
        }

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Form Isian');

        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length:' . filesize($file_name));
        flush();

        readfile($file_name);

        exit;
    }

    public function exportBa()
    {

        $wilayahModel = new WilayahModel();
        $user_login = $this->AuthModel->getUserId();
        // dd($user_login);
        if (!isset($user_login['lp_sekretariat']) && !isset($user_login['user_lembaga_id'])) {
            $str = '<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
               <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
               <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
               <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.1/js/bootstrap-dialog.min.js"></script>
               <script type="text/javascript">
                   setTimeout(function() { 
                      BootstrapDialog.alert(\'Silahkan isi profil Anda dan Lembaga terlebih dahulu!!\') 
                      window.location.href = \'/profil_user\';
                    },10000);
               </script>';
            echo $str;
            // echo "<script>
            //     alert('Some text');
            //     window.location.href = '/usulan';// your redirect path here
            //     </script>";
        } else {

            $rekapUsulan = $this->Usulan22Model->rekapUsulanBa();
            foreach ($rekapUsulan as $row) {
                $nonbansos = $row['nonbansos'];
                $bpnt = $row['bpnt'];
                $pkh = $row['pkh'];
                $pbi = $row['pbi'];
                $bst = $row['bst'];
                $total_usulan = $row['total_usulan'];
            }

            // dd($rekapUsulan);
            // dd(session()->get('kode_desa'));

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('data/templates/template_usulan.docx');

            $kode_desa = session()->get('kode_desa');
            $kode_tanggal = date('d');
            $kode_bulan = date('n');
            $kode_tahun = date('Y');

            $this->WilayahModel = $wilayahModel->getVillage($kode_desa);
            // dd($this->WilayahModel);
            if (is_array($this->WilayahModel)) {
                $this->WilayahModelUpper = strtoupper($this->WilayahModel['name']);
                $this->WilayahModelPropper = ucwords(strtolower($this->WilayahModel['name']));
            } else {
                $this->WilayahModelUpper = strtoupper($this->WilayahModel);
                $this->WilayahModelPropper = ucwords(strtolower($this->WilayahModel));
            }


            // dd($this->WilayahModelUpper);
            $bulan = array(
                1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
            $bulanUpper = strtoupper($bulan[$kode_bulan]);

            $hari = date("D");
            switch ($hari) {
                case 'Sun':
                    $hari_ini = "Minggu";
                    break;

                case 'Mon':
                    $hari_ini = "Senin";
                    break;

                case 'Tue':
                    $hari_ini = "Selasa";
                    break;

                case 'Wed':
                    $hari_ini = "Rabu";
                    break;

                case 'Thu':
                    $hari_ini = "Kamis";
                    break;

                case 'Fri':
                    $hari_ini = "Jumat";
                    break;

                case 'Sat':
                    $hari_ini = "Sabtu";
                    break;

                default:
                    $hari_ini = "Tidak di ketahui";
                    break;
            }

            $templateProcessor->setValues([
                'desaUpper' => $this->WilayahModelUpper,
                'desaPropper' => $this->WilayahModelPropper,
                'sekretariat' => $user_login['lp_sekretariat'],
                'email' => $user_login['lp_email'],
                'kode_pos' => $user_login['lp_kode_pos'],
                'hari' => $hari_ini,
                'tanggal' => $kode_tanggal,
                'bulan' => $bulan[$kode_bulan],
                'bulanUpper' => strtoupper($bulan[$kode_bulan]),
                'tahun' => $kode_tahun,
                'nonbansos' => $nonbansos,
                'bpnt' => $bpnt,
                'pkh' => $pkh,
                'pbi' => $pbi,
                'bst' => $bst,
                // 'disabilitas' => $rekapUsulan['disabilitas'],
                'total_usulan' => $total_usulan,
                'nama_petugas' => strtoupper($user_login['fullname']),
                'nama_pimpinan' => strtoupper($user_login['lp_kepala']),
            ]);

            $filename = 'BA_PENGUSULAN – PAKENJENG – ' . $this->WilayahModel['name'] . ' – ' . strtoupper($bulan[$kode_bulan]) . '.docx';

            header("Content-Description: File Transfer");
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');

            $templateProcessor->saveAs('php://output');
        }
    }

    public function import_csv()
    {
        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Import CSV Report',
            'user_login' => $this->AuthModel->getUserId(),
            'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'bansos' => $this->BansosModel->findAll(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'csv_ket' => $this->CsvReportModel->getCsvKet(),
        ];
        // dd($data['csv_ket']);
        return view('dtks/data/ppks/usulan/impor_csv', $data);
    }

    public function tbCsv()
    {
        $this->CsvReportModel = new CsvReportModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('namaFile');
        // $filter5 = '';
        // $filter6 = '';
        $filter5 = $this->request->getPost('data_tahun');
        $filter6 = $this->request->getPost('data_bulan');

        $listing = $this->CsvReportModel->getDataTabel($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);
        $jumlah_semua = $this->CsvReportModel->semua();
        $jumlah_filter = $this->CsvReportModel->filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->du_nik;
            $row[] = $key->nama;
            $row[] = $key->cr_nama_lgkp;
            $row[] = $key->nokk;
            $row[] = $key->alamat;
            $row[] = $key->rt;
            $row[] = $key->rw;
            $row[] = $key->cr_nama_desa;
            $row[] = $key->kelurahan;
            $row[] = $key->cr_nama_kec;
            $row[] = $key->kecamatan;
            // $row[] = $key->program_bansos;
            $row[] = $key->cr_program_bansos;
            $row[] = $key->cr_hasil;
            $row[] = $key->cr_padan;
            $row[] = $key->cr_ket_vali;
            $row[] = $key->cr_ck_id;
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

    public function importCsvToDb()
    {
        $input = $this->validate([
            'file' => 'uploaded[file]|max_size[file,2048]|ext_in[file,csv]'
        ]);
        $namaFile = $this->request->getPost('ck_id');

        // dd($namaFile);

        if (!$input && ($namaFile = '' || $namaFile == null)) {
            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Import CSV Report',
                'user_login' => $this->AuthModel->getUserId(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'bansos' => $this->BansosModel->findAll(),
                'statusRole' => $this->GenModel->getStatusRole(),
                'session' => session()->get(),
                'csv_ket' => $this->CsvReportModel->getCsvKet(),
                'validation' => $this->validator,
            ];
            // dd($data['validation']);
            // $data['validation'] = $this->validator;
            return view('dtks/data/ppks/usulan/impor_csv', $data);
        } else {
            if ($file = $this->request->getFile('file')) {
                if ($file->isValid() && !$file->hasMoved()) {

                    // Get random file name
                    $newName = $file->getRandomName();

                    // Store file in public/csvfile/ folder
                    $file->move('../public/data/csvfile', $newName);

                    // Reading file
                    $file = fopen("../public/data/csvfile/" . $newName, "r");
                    $i = 0;
                    $numberOfFields = 8; // Total number of fields

                    $csvArr = array();

                    // Initialize $importData_arr Array
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);
                        // Skip first row & check number of fields
                        if ($i > 0 && $num == $numberOfFields) {

                            // $namaFile = $this->request->getPost('ck_id');
                            // Key names are the insert table field names - name, email, city, and status
                            $csvArr[$i]['cr_nama_kec'] = $filedata[0];
                            $csvArr[$i]['cr_nama_desa'] = $filedata[1];
                            $csvArr[$i]['cr_nik_usulan'] = $filedata[2];
                            $csvArr[$i]['cr_program_bansos'] = $filedata[3];
                            $csvArr[$i]['cr_hasil'] = $filedata[4];
                            $csvArr[$i]['cr_padan'] = $filedata[5];
                            $csvArr[$i]['cr_nama_lgkp'] = $filedata[6];
                            $csvArr[$i]['cr_ket_vali'] = $filedata[7];
                            $csvArr[$i]['cr_ck_id'] = $namaFile;
                            $csvArr[$i]['cr_created_by'] = session()->get('nik');
                        }
                        $i++;
                    }
                    fclose($file);

                    // Insert data
                    $count = 0;
                    foreach ($csvArr as $userdata) {
                        $this->CsvReportModel = new CsvReportModel();

                        // Check record
                        $findRecord = $this->CsvReportModel->where('cr_nik_usulan', $userdata['cr_nik_usulan'])->countAllResults();

                        if ($findRecord == 0) {

                            ## Insert Record
                            if ($this->CsvReportModel->insert($userdata)) {
                                $count++;
                            }
                        }
                    }

                    // Set Session
                    session()->setFlashdata('message', $count . ' rows successfully added.');
                    session()->setFlashdata('alert-class', 'alert-success');
                } else {
                    // Set Session
                    session()->setFlashdata('message', 'CSV file coud not be imported.');
                    session()->setFlashdata('alert-class', 'alert-danger');
                }
            } else {

                // Set Session
                session()->setFlashdata('message', 'CSV file coud not be imported.');
                session()->setFlashdata('alert-class', 'alert-danger');
            }
        }
        return redirect()->route('import_csv');
    }
}
