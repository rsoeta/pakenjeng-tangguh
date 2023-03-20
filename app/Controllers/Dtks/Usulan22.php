<?php

namespace App\Controllers\Dtks;


use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\DtksModel;
use App\Models\Dtks\Usulan22Model;
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

class Usulan22 extends BaseController
{
    public function __construct()
    {
        $this->AuthModel = new AuthModel();
        $this->Usulan22Model = new Usulan22Model();
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
        // var_dump(deadline_usulan());
        // die;
        if (session()->get('role_id') == 1) {
            $this->Usulan22Model = new Usulan22Model();
            $this->WilayahModel = new WilayahModel();
            $this->RwModel = new RwModel();
            $this->BansosModel = new BansosModel();
            $this->PekerjaanModel = new PekerjaanModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->ShdkModel = new ShdkModel();

            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Daftar Usulan DTKS',
                'user_login' => $this->AuthModel->getUserId(),
                'dtks' => $this->Usulan22Model->getDtks(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'bansos' => $this->BansosModel->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $this->StatusKawinModel->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $this->ShdkModel->findAll(),
                'percentages' => $this->VervalPbiModel->jml_persentase(),
                'statusRole' => $this->GenModel->getStatusRole(),
            ];

            return view('dtks/data/dtks/usulan/tables', $data);
        } else if (session()->get('role_id') >= 1) {
            $this->Usulan22Model = new Usulan22Model();
            $this->WilayahModel = new WilayahModel();
            $this->RwModel = new RwModel();
            $this->BansosModel = new BansosModel();
            $this->PekerjaanModel = new PekerjaanModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->ShdkModel = new ShdkModel();

            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Daftar Usulan DTKS',
                'user_login' => $this->AuthModel->getUserId(),
                'dtks' => $this->Usulan22Model->getDtks(),
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

            return view('dtks/data/dtks/usulan/tables', $data);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function tabel_data()
    {
        // var_dump(deadline_usulan());

        $this->Usulan22Model = new Usulan22Model();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $role = session()->get('role_id');

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('bansos');
        $filter5 = $this->request->getPost('data_tahun');
        $filter6 = $this->request->getPost('data_bulan');
        $filter7 = $this->request->getPost('data_reg');

        $listing = $this->Usulan22Model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7);
        $jumlah_semua = $this->Usulan22Model->jumlah_semua();
        $jumlah_filter = $this->Usulan22Model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->du_nik;
            $row[] = '<a href=' . usulan_foto($key->foto_identitas, 'foto_identitas') . ' data-lightbox="dataUsulan' . $key->du_nik . '"' . ' data-title="Foto Identitas" style="text-decoration:none;">' . $key->nama . '</a>
            <a href=' . usulan_foto($key->foto_rumah, 'foto_rumah') . ' data-lightbox="dataUsulan' . $key->du_nik . '"' . ' data-title="Foto Rumah Depan"></a>
            ';
            // $row[] = $key->nama;
            $row[] = $key->nokk;
            $row[] = $key->ibu_kandung;
            if ($key->tanggal_lahir == '0000-00-00') {
                $row[] = '-';
            } elseif ($key->tanggal_lahir == null) {
                $row[] = '-';
            } else {
                // date_format
                $row[] = date('d/m/Y', strtotime($key->tanggal_lahir));
            }
            $row[] = $key->JenisPekerjaan;
            $row[] = $key->StatusKawin;
            $row[] = $key->dbj_nama_bansos;
            $row[] = '<a href="https://wa.me/' . nope($key->nope) . '" target="_blank" style="text-decoration:none;">' . strtoupper($key->fullname) . '</a>';
            $row[] = $key->updated_at;
            $row[] = '<a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->idUsulan . "'" . ')"><i class="far fa-edit"></i></a> | 
                <button class="btn btn-sm btn-secondary" data-id="' . $key->idUsulan . '" data-nama="' . $key->nama . '" id="deleteBtn"><i class="far fa-trash-alt"></i></button>';
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

        $this->Usulan22Model = new Usulan22Model();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $role = session()->get('role_id');

        $filter1 = $this->request->getPost('desa01');
        $filter2 = $this->request->getPost('rw01');
        $filter3 = $this->request->getPost('rt01');
        $filter4 = $this->request->getPost('bansos01');
        $filter5 = $this->request->getPost('data_tahun01');
        $filter6 = $this->request->getPost('data_bulan01');
        $filter7 = $this->request->getPost('data_reg01');

        $listing = $this->Usulan22Model->get_datatables01($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7);
        $jumlah_semua = $this->Usulan22Model->jumlah_semua01();
        $jumlah_filter = $this->Usulan22Model->jumlah_filter01($filter1, $filter2, $filter3, $filter4, $filter5, $filter6, $filter7);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->du_nik;
            $row[] = '<a href=' . usulan_foto($key->foto_identitas, 'foto_identitas') . ' data-lightbox="dataUsulan' . $key->du_nik . '"' . ' data-title="Foto Identitas" style="text-decoration:none;">' . $key->nama . '</a>
            <a href=' . usulan_foto($key->foto_rumah, 'foto_rumah') . ' data-lightbox="dataUsulan' . $key->du_nik . '"' . ' data-title="Foto Rumah Depan"></a>
            ';
            $row[] = $key->nokk;
            $row[] = $key->ibu_kandung;
            if ($key->tanggal_lahir == '0000-00-00') {
                $row[] = '-';
            } elseif ($key->tanggal_lahir == null) {
                $row[] = '-';
            } else {
                // date_format
                $row[] = date('d/m/Y', strtotime($key->tanggal_lahir));
            }
            $row[] = $key->JenisPekerjaan;
            $row[] = $key->dbj_nama_bansos;
            $row[] = '<a href="https://wa.me/' . nope($key->nope) . '" target="_blank" style="text-decoration:none;">' . strtoupper($key->fullname) . '</a>';
            $row[] = $key->updated_at;
            $row[] = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="View" onclick="view_person(' . "'" . $key->idUsulan . "'" . ')"><i class="fas fa-eye"></i></a>';
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

            $this->Usulan22Model = new Usulan22Model();
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
                'dtks' => $this->Usulan22Model->getDtks(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $this->StatusKawinModel->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $this->ShdkModel->findAll(),
                'users' => $users->findAll(),
                'DisabilitasJenisModel' => $DisabilitasJenisModel->findAll(),
                'sta_ortu' => $GenModel->get_staortu(),
            ];
            if (deadline_usulan() === 1) {

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
                    'data' => view('dtks/data/dtks/usulan/modaltambah', $data),
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
                'nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[dtks_usulan_view.du_nik,du_id,{du_id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'databansos' => [
                    'label' => 'Program Bansos',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                    ]
                ],
                'nokk' => [
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
                'nama' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'tempat_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'tanggal_lahir' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'ibu_kandung' => [
                    'label' => 'Ibu Kandung',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'jenis_kelamin' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'jenis_pekerjaan' => [
                    'label' => 'Jenis Pekerjaan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'status_kawin' => [
                    'label' => 'Status Perkawinan',
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
                'shdk' => [
                    'label' => 'SHDK',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'du_foto_identitas' => [
                    'label' => 'Foto Identitas',
                    'rules' => 'uploaded[du_foto_identitas]|is_image[du_foto_identitas]|mime_in[du_foto_identitas,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => '{field} harus ada.',
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
                'du_foto_rumah' => [
                    'label' => 'Foto Depan Rumah',
                    'rules' => 'uploaded[du_foto_rumah]|is_image[du_foto_rumah]|mime_in[du_foto_rumah,image/jpg,image/jpeg,image/png]',
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
                        'nik' => $validation->getError('nik'),
                        'databansos' => $validation->getError('databansos'),
                        'nokk' => $validation->getError('nokk'),
                        'nama' => $validation->getError('nama'),
                        'tempat_lahir' => $validation->getError('tempat_lahir'),
                        'tanggal_lahir' => $validation->getError('tanggal_lahir'),
                        'ibu_kandung' => $validation->getError('ibu_kandung'),
                        'jenis_kelamin' => $validation->getError('jenis_kelamin'),
                        'jenis_pekerjaan' => $validation->getError('jenis_pekerjaan'),
                        'status_kawin' => $validation->getError('status_kawin'),
                        'alamat' => $validation->getError('alamat'),
                        'datart' => $validation->getError('datart'),
                        'datarw' => $validation->getError('datarw'),
                        'kelurahan' => $validation->getError('kelurahan'),
                        'shdk' => $validation->getError('shdk'),
                        'du_foto_identitas' => $validation->getError('du_foto_identitas'),
                        'du_foto_rumah' => $validation->getError('du_foto_rumah'),
                        'du_latitude' => $validation->getError('du_latitude'),
                        'du_longitude' => $validation->getError('du_longitude'),
                        'created_by' => $validation->getError('created_by'),
                    ]
                ];
            } else {

                $kode_desa = session()->get('kode_desa');
                $namaDesa = $this->WilayahModel->getVillage($kode_desa);
                $desaNama = $namaDesa['name'];

                $du_foto_rumah = $this->request->getFile('du_foto_rumah');
                $du_foto_identitas = $this->request->getFile('du_foto_identitas');

                // var_dump($dd_foto_cpm);
                // die;
                $buat_tanggal = date_create($this->request->getVar('updated_at'));
                $filename_dua = 'DUDFH_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
                $filename_empat = 'DUDID_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
                // var_dump($filename_dua);
                // die;

                $img_dua = imagecreatefromjpeg($du_foto_rumah);
                $img_empat = imagecreatefromjpeg($du_foto_identitas);

                // get width and height of image

                $width_dua = imagesx($img_dua);
                $height_dua = imagesy($img_dua);

                $width_empat = imagesx($img_empat);
                $height_empat = imagesy($img_empat);

                // reorient image if width is greater than height
                if ($width_dua > $height_dua) {
                    $img_dua = imagerotate($img_dua, -90, 0);
                }
                if ($width_empat > $height_empat) {
                    $img_empat = imagerotate($img_empat, -90, 0);
                }
                // resize image
                $img_dua = imagescale($img_dua, 480, 640);
                $img_empat = imagescale($img_empat, 480, 640);

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

                imagejpeg($img_dua, 'data/usulan/foto_rumah/' . $filename_dua, $quality);
                imagejpeg($img_empat, 'data/usulan/foto_identitas/' . $filename_empat, $quality);
                // var_dump($img_satu);
                // die;

                $data = [
                    'provinsi' => '32',
                    'kabupaten' => '32.05',
                    'kecamatan' => '32.05.33',
                    'shdk' => $this->request->getVar('shdk'),
                    'kelurahan' => $this->request->getVar('kelurahan'),
                    'rw' => $this->request->getVar("datarw"),
                    'rt' => $this->request->getVar("datart"),
                    'alamat' => strtoupper($this->request->getVar('alamat')),
                    'status_kawin' => $this->request->getVar("status_kawin"),
                    'jenis_pekerjaan' => $this->request->getVar("jenis_pekerjaan"),
                    'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
                    'ibu_kandung' => strtoupper($this->request->getVar("ibu_kandung")),
                    'tanggal_lahir' => $this->request->getVar("tanggal_lahir"),
                    'tempat_lahir' => strtoupper($this->request->getVar("tempat_lahir")),
                    'nama' => strtoupper($this->request->getVar('nama')),
                    'nokk' => $this->request->getVar('nokk'),
                    'program_bansos' => $this->request->getVar('databansos'),
                    'du_nik' => $this->request->getVar('nik'),
                    'disabil_status' => $this->request->getVar('disabil_status'),
                    'disabil_kode' => $this->request->getVar('disabil_jenis'),
                    'hamil_status' => $this->request->getVar('status_hamil'),
                    'hamil_tgl' => $this->request->getVar('tgl_hamil'),
                    'foto_identitas' => $filename_empat,
                    'foto_rumah' => $filename_dua,
                    'du_latitude' => $this->request->getVar('du_latitude'),
                    'du_longitude' => $this->request->getVar('du_longitude'),
                    'sk0' => $this->request->getVar('sk0'),
                    'sk1' => $this->request->getVar('sk1'),
                    'sk2' => $this->request->getVar('sk2'),
                    'sk3' => $this->request->getVar('sk3'),
                    'sk4' => $this->request->getVar('sk4'),
                    'sk5' => $this->request->getVar('sk5'),
                    'sk6' => $this->request->getVar('sk6'),
                    'sk7' => $this->request->getVar('sk7'),
                    'sk8' => $this->request->getVar('sk8'),
                    'sk9' => $this->request->getVar('sk9'),
                    'du_so_id' => $this->request->getVar('du_so_id'),
                    'created_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                    'updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                    'created_at_year' => date('Y'),
                    'created_at_month' => date('n'),
                    'created_by' => session()->get('nik'),
                    'updated_by' => session()->get('nik'),

                    // 'foto_rumah' => $nama_foto_rumah,
                ];
                // dd($data);
                $this->Usulan22Model->save($data);

                $msg = [
                    'sukses' => 'Data berhasil ditambahkan',
                ];
            }
            echo json_encode($msg);


            // session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');


            // echo json_encode(array("status" => true));
            // return redirect()->to('/dtks/usulan/tables');
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function get_data_penduduk()
    {
        $db = \Config\Database::connect();
        $role = session()->get('role_id');
        $kode_desa = session()->get('kode_desa');
        $kode_rw = session()->get('level');

        $request = service('request');
        $postData = $request->getPost();

        $response = array();
        $data = array();
        $builder = $db->table('dtks_usulan_caridata');
        $penduduk = [];
        if (isset($postData['search'])) {
            $search = $postData['search'];
            if ($role === '1') {
                $builder->select('*');
                $builder->like('du_nik', $search);
                $query = $builder->get();
                $data = $query->getResult();
            } elseif ($role === '2') {
                $builder->select('*');
                $builder->like('du_nik', $search);
                $query = $builder->get();
                $data = $query->getResult();
            } elseif ($role === '3') {
                $builder->select('*');
                $builder->where('kelurahan', $kode_desa);
                $builder->like('du_nik', $search);
                $query = $builder->get();
                $data = $query->getResult();
            } elseif ($role === '4') {
                $builder->select('*');
                $builder->where('kelurahan', $kode_desa);
                $builder->where('rw', $kode_rw);
                $builder->like('du_nik', $search);
                $query = $builder->get();
                $data = $query->getResult();
            } else {
                $data = [];
            }
            // } else {
            //     if ($role === '1') {
            //         $builder->select('*');
            //         $query = $builder->get();
            //         $data = $query->getResult();
            //     } elseif ($role === '2') {
            //         $builder->select('*');
            //         $builder->distinct('du_nik');
            //         $query = $builder->get();
            //         $data = $query->getResult();
            //     } elseif ($role === '3') {
            //         $builder->select('*');
            //         $builder->where('kelurahan', $kode_desa);
            //         $builder->distinct('du_nik');
            //         $query = $builder->get();
            //         $data = $query->getResult();
            //     } elseif ($role === '4') {
            //         $builder->select('*');
            //         $builder->where('kelurahan', $kode_desa);
            //         $builder->where('rw', $kode_rw);
            //         $builder->distinct('du_nik');
            //         $query = $builder->get();
            //         $data = $query->getResult();
            //     } else {
            //         $data = [];
            //     }
        }
        foreach ($data as $pdk) {
            $penduduk[] = array(
                'id' => $pdk->du_id,
                'text' => ' - NIK: ' . $pdk->du_nik . ', NAMA: ' . $pdk->nama,
            );
        }
        $response['data'] = $penduduk;

        return $this->response->setJSON($response);
    }

    public function delete()
    {
        if ($this->request->isAJAX()) {


            // if (deadline_usulan() == 1) {
            //     $msg = [
            //         'informasi' => 'Mohon Maaf, Batas waktu untuk Perubahan Data, Telah Habis!!'
            //     ];
            // } else {
            $id = $this->request->getVar('id');

            $usulan = $this->Usulan22Model->find($id);
            unlink('data/usulan/foto_identitas/' . $usulan['foto_identitas']);
            unlink('data/usulan/foto_rumah/' . $usulan['foto_rumah']);

            $this->Usulan22Model->delete($id);
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

            // if (deadline_usulan() == 1) {
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
            $model = new Usulan22Model();
            $row = $model->find($id);

            $data = [
                'title' => 'Form. Edit Data',
                'shdk' => $this->ShdkModel->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $this->StatusKawinModel->orderBy('StatusKawin', 'asc')->findAll(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'rw' => $this->RwModel->noRw(),
                'rt' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'users' => $users->findAll(),
                'jenkel' => $this->GenModel->getDataJenkel(),
                'DisabilitasJenisModel' => $DisabilitasJenisModel->findAll(),
                'sta_ortu' => $GenModel->get_staortu(),


                'created_by' => session()->get('nik'),
                'stahub' => $row['shdk'],
                'kelurahan' => $row['kelurahan'],
                'datarw' => $row["rw"],
                'datart' => $row["rt"],
                'alamat' => $row['alamat'],
                'status_kawin' => $row["status_kawin"],
                'jenis_pekerjaan' => $row["jenis_pekerjaan"],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'ibu_kandung' => strtoupper($row["ibu_kandung"]),
                'tanggal_lahir' => $row["tanggal_lahir"],
                'tempat_lahir' => $row["tempat_lahir"],
                'nama' => $row['nama'],
                'nokk' => $row['nokk'],
                'databansos' => $row['program_bansos'],
                'du_nik' => $row['du_nik'],
                'id' => $row['du_id'],
                'disabil_status' => $row['disabil_status'],
                'disabil_jenis' => $row['disabil_kode'],
                'status_hamil' => $row['hamil_status'],
                'tgl_hamil' => $row['hamil_tgl'],
                'du_foto_identitas' => $row['foto_identitas'],
                'du_foto_rumah' => $row['foto_rumah'],
                'du_latitude' => $row['du_latitude'],
                'du_longitude' => $row['du_longitude'],
                'sk0' => $row['sk0'],
                'sk1' => $row['sk1'],
                'sk2' => $row['sk2'],
                'sk3' => $row['sk3'],
                'sk4' => $row['sk4'],
                'sk5' => $row['sk5'],
                'sk6' => $row['sk6'],
                'sk7' => $row['sk7'],
                'sk8' => $row['sk8'],
                'sk9' => $row['sk9'],
                'du_so_id' => $row['du_so_id'],
                'du_proses' => $row['du_proses'],
                'updated_at' => $row['updated_at'],
                // 'foto_rumah' => $nama_foto_rumah,
            ];

            $msg = [
                'sukses' => view('dtks/data/dtks/usulan/modaledit', $data)
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

            // if (deadline_usulan() == 1) {
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
            $model = new Usulan22Model();
            $row = $model->find($id);

            $data = [
                'title' => 'Form. View Data',
                'shdk' => $this->ShdkModel->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $this->StatusKawinModel->orderBy('StatusKawin', 'asc')->findAll(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'rw' => $this->RwModel->noRw(),
                'rt' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'users' => $users->findAll(),
                'jenkel' => $this->GenModel->getDataJenkel(),
                'DisabilitasJenisModel' => $DisabilitasJenisModel->findAll(),
                'sta_ortu' => $GenModel->get_staortu(),


                'created_by' => session()->get('nik'),
                'stahub' => $row['shdk'],
                'kelurahan' => $row['kelurahan'],
                'datarw' => $row["rw"],
                'datart' => $row["rt"],
                'alamat' => $row['alamat'],
                'status_kawin' => $row["status_kawin"],
                'jenis_pekerjaan' => $row["jenis_pekerjaan"],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'ibu_kandung' => strtoupper($row["ibu_kandung"]),
                'tanggal_lahir' => $row["tanggal_lahir"],
                'tempat_lahir' => $row["tempat_lahir"],
                'nama' => $row['nama'],
                'nokk' => $row['nokk'],
                'databansos' => $row['program_bansos'],
                'du_nik' => $row['du_nik'],
                'id' => $row['du_id'],
                'disabil_status' => $row['disabil_status'],
                'disabil_jenis' => $row['disabil_kode'],
                'status_hamil' => $row['hamil_status'],
                'tgl_hamil' => $row['hamil_tgl'],
                'du_foto_identitas' => $row['foto_identitas'],
                'du_foto_rumah' => $row['foto_rumah'],
                'du_latitude' => $row['du_latitude'],
                'du_longitude' => $row['du_longitude'],
                'sk0' => $row['sk0'],
                'sk1' => $row['sk1'],
                'sk2' => $row['sk2'],
                'sk3' => $row['sk3'],
                'sk4' => $row['sk4'],
                'sk5' => $row['sk5'],
                'sk6' => $row['sk6'],
                'sk7' => $row['sk7'],
                'sk8' => $row['sk8'],
                'sk9' => $row['sk9'],
                'du_so_id' => $row['du_so_id'],
                'du_proses' => $row['du_proses'],
                // 'foto_rumah' => $nama_foto_rumah,
            ];

            $msg = [
                'sukses' => view('dtks/data/dtks/usulan/modalview', $data)
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
            $id = $this->request->getVar('id');
            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[dtks_usulan_view.du_nik,du_id,{id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'databansos' => [
                    'label' => 'Program Bansos',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                    ]
                ],
                'nokk' => [
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
                'nama' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'tempat_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'tanggal_lahir' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'ibu_kandung' => [
                    'label' => 'Ibu Kandung',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'jenis_kelamin' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'jenis_pekerjaan' => [
                    'label' => 'Jenis Pekerjaan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'status_kawin' => [
                    'label' => 'Status Perkawinan',
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
                'shdk' => [
                    'label' => 'SHDK',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'du_foto_identitas' => [
                    'label' => 'Foto Identitas',
                    'rules' => 'is_image[du_foto_identitas]|mime_in[du_foto_identitas,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berekstensi gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
                'du_foto_rumah' => [
                    'label' => 'Foto Depan Rumah',
                    'rules' => 'is_image[du_foto_rumah]|mime_in[du_foto_rumah,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berekstensi gambar.',
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
                        'nik' => $validation->getError('nik'),
                        'databansos' => $validation->getError('databansos'),
                        'nokk' => $validation->getError('nokk'),
                        'nama' => $validation->getError('nama'),
                        'tempat_lahir' => $validation->getError('tempat_lahir'),
                        'tanggal_lahir' => $validation->getError('tanggal_lahir'),
                        'ibu_kandung' => $validation->getError('ibu_kandung'),
                        'jenis_kelamin' => $validation->getError('jenis_kelamin'),
                        'jenis_pekerjaan' => $validation->getError('jenis_pekerjaan'),
                        'status_kawin' => $validation->getError('status_kawin'),
                        'alamat' => $validation->getError('alamat'),
                        'datart' => $validation->getError('datart'),
                        'datarw' => $validation->getError('datarw'),
                        'kelurahan' => $validation->getError('kelurahan'),
                        'shdk' => $validation->getError('shdk'),
                        'du_foto_identitas' => $validation->getError('du_foto_identitas'),
                        'du_foto_rumah' => $validation->getError('du_foto_rumah'),
                        'du_latitude' => $validation->getError('du_latitude'),
                        'du_longitude' => $validation->getError('du_longitude'),
                        'created_by' => $validation->getError('created_by'),
                    ]
                ];
            } else {

                $duFotoIdentitas = $_FILES['du_foto_identitas']['name'];
                $duFotoRumah = $_FILES['du_foto_rumah']['name'];

                if (($duFotoRumah || $duFotoIdentitas) != NULL) {

                    $usulan = $this->Usulan22Model->find($id);

                    $du_fotoid_old = $usulan['foto_identitas'];
                    $du_fotorumah_old = $usulan['foto_rumah'];

                    $dir_identintas = 'data/usulan/foto_identitas/' . $du_fotoid_old;
                    $dir_rumah = 'data/usulan/foto_rumah/' . $du_fotorumah_old;

                    unlink($dir_identintas);
                    unlink($dir_rumah);

                    $kode_desa = session()->get('kode_desa');
                    $namaDesa = $this->WilayahModel->getVillage($kode_desa);
                    $desaNama = $namaDesa['name'];

                    $du_foto_rumah = $this->request->getFile('du_foto_rumah');
                    $du_foto_identitas = $this->request->getFile('du_foto_identitas');

                    // var_dump($dd_foto_cpm);
                    // die;
                    $buat_tanggal = date_create($this->request->getVar('updated_at'));
                    $filename_dua = 'DUDFH_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
                    $filename_empat = 'DUDID_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';

                    $img_dua = imagecreatefromjpeg($du_foto_rumah);
                    $img_empat = imagecreatefromjpeg($du_foto_identitas);

                    // get width and height of image

                    $width_dua = imagesx($img_dua);
                    $height_dua = imagesy($img_dua);

                    $width_empat = imagesx($img_empat);
                    $height_empat = imagesy($img_empat);

                    // reorient image if width is greater than height
                    if ($width_dua > $height_dua) {
                        $img_dua = imagerotate($img_dua, -90, 0);
                    }
                    if ($width_empat > $height_empat) {
                        $img_empat = imagerotate($img_empat, -90, 0);
                    }
                    // resize image
                    $img_dua = imagescale($img_dua, 480, 640);
                    $img_empat = imagescale($img_empat, 480, 640);

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

                    imagejpeg($img_dua, 'data/usulan/foto_rumah/' . $filename_dua, $quality);
                    imagejpeg($img_empat, 'data/usulan/foto_identitas/' . $filename_empat, $quality);
                    // var_dump($img_satu);
                    // die;

                    $data = [
                        'provinsi' => '32',
                        'kabupaten' => '32.05',
                        'kecamatan' => '32.05.33',
                        'shdk' => $this->request->getVar('shdk'),
                        'kelurahan' => $this->request->getVar('kelurahan'),
                        'rw' => $this->request->getVar("datarw"),
                        'rt' => $this->request->getVar("datart"),
                        'alamat' => strtoupper($this->request->getVar('alamat')),
                        'status_kawin' => $this->request->getVar("status_kawin"),
                        'jenis_pekerjaan' => $this->request->getVar("jenis_pekerjaan"),
                        'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
                        'ibu_kandung' => strtoupper($this->request->getVar("ibu_kandung")),
                        'tanggal_lahir' => $this->request->getVar("tanggal_lahir"),
                        'tempat_lahir' => strtoupper($this->request->getVar("tempat_lahir")),
                        'nama' => strtoupper($this->request->getVar('nama')),
                        'nokk' => $this->request->getVar('nokk'),
                        'program_bansos' => $this->request->getVar('databansos'),
                        'du_nik' => $this->request->getVar('nik'),
                        'disabil_status' => $this->request->getVar('disabil_status'),
                        'disabil_kode' => $this->request->getVar('disabil_jenis'),
                        'hamil_status' => $this->request->getVar('status_hamil'),
                        'hamil_tgl' => $this->request->getVar('tgl_hamil'),
                        'foto_identitas' => $filename_empat,
                        'foto_rumah' => $filename_dua,
                        'du_latitude' => $this->request->getVar('du_latitude'),
                        'du_longitude' => $this->request->getVar('du_longitude'),
                        'sk0' => $this->request->getVar('sk0'),
                        'sk1' => $this->request->getVar('sk1'),
                        'sk2' => $this->request->getVar('sk2'),
                        'sk3' => $this->request->getVar('sk3'),
                        'sk4' => $this->request->getVar('sk4'),
                        'sk5' => $this->request->getVar('sk5'),
                        'sk6' => $this->request->getVar('sk6'),
                        'sk7' => $this->request->getVar('sk7'),
                        'sk8' => $this->request->getVar('sk8'),
                        'sk9' => $this->request->getVar('sk9'),
                        'du_so_id' => $this->request->getVar('du_so_id'),
                        'du_proses' => $this->request->getVar('du_proses'),
                        'updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                        'updated_by' => session()->get('nik'),

                        // 'foto_rumah' => $nama_foto_rumah,
                    ];

                    $this->Usulan22Model->update($id, $data);

                    $msg = [
                        'sukses' => 'Data berhasil diubah',
                    ];
                } else {

                    $buat_tanggal = date_create($this->request->getVar('updated_at'));
                    // $filename_dua = 'DUDFH_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
                    // $filename_empat = 'DUDID_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';

                    $data = [
                        'provinsi' => '32',
                        'kabupaten' => '32.05',
                        'kecamatan' => '32.05.33',
                        'shdk' => $this->request->getVar('shdk'),
                        'kelurahan' => $this->request->getVar('kelurahan'),
                        'rw' => $this->request->getVar("datarw"),
                        'rt' => $this->request->getVar("datart"),
                        'alamat' => strtoupper($this->request->getVar('alamat')),
                        'status_kawin' => $this->request->getVar("status_kawin"),
                        'jenis_pekerjaan' => $this->request->getVar("jenis_pekerjaan"),
                        'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
                        'ibu_kandung' => strtoupper($this->request->getVar("ibu_kandung")),
                        'tanggal_lahir' => $this->request->getVar("tanggal_lahir"),
                        'tempat_lahir' => strtoupper($this->request->getVar("tempat_lahir")),
                        'nama' => strtoupper($this->request->getVar('nama')),
                        'nokk' => $this->request->getVar('nokk'),
                        'program_bansos' => $this->request->getVar('databansos'),
                        'du_nik' => $this->request->getVar('nik'),
                        'disabil_status' => $this->request->getVar('disabil_status'),
                        'disabil_kode' => $this->request->getVar('disabil_jenis'),
                        'hamil_status' => $this->request->getVar('status_hamil'),
                        'hamil_tgl' => $this->request->getVar('tgl_hamil'),
                        // 'foto_rumah' => $filename_dua,
                        // 'foto_identitas' => $filename_empat,
                        'du_latitude' => $this->request->getVar('du_latitude'),
                        'du_longitude' => $this->request->getVar('du_longitude'),
                        'sk0' => $this->request->getVar('sk0'),
                        'sk1' => $this->request->getVar('sk1'),
                        'sk2' => $this->request->getVar('sk2'),
                        'sk3' => $this->request->getVar('sk3'),
                        'sk4' => $this->request->getVar('sk4'),
                        'sk5' => $this->request->getVar('sk5'),
                        'sk6' => $this->request->getVar('sk6'),
                        'sk7' => $this->request->getVar('sk7'),
                        'sk8' => $this->request->getVar('sk8'),
                        'sk9' => $this->request->getVar('sk9'),
                        'du_so_id' => $this->request->getVar('du_so_id'),
                        'du_proses' => $this->request->getVar('du_proses'),
                        'updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                        'updated_by' => session()->get('nik'),

                        // 'foto_rumah' => $nama_foto_rumah,
                    ];

                    $this->Usulan22Model->update($id, $data);

                    $msg = [
                        'sukses' => 'Data berhasil diubah',
                    ];
                }
                echo json_encode($msg);
            }
        } else {
            return redirect()->to('lockscreen');
        }
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
        $filter1 = $this->request->getVar('desa');
        $filter4 = $this->request->getVar('bansos');
        $filter5 = $this->request->getVar('data_tahun');
        $filter6 = $this->request->getVar('data_bulan');

        // dd($this->WilayahModel);
        // if (isset($tmbExpData)) {
        // if ($filter4 == null || $filter5 == null || $filter6 == null) {

        //     session()->setFlashdata('message', '<strong>Syarat Export</strong>: [-NAMA DESA, -JENIS PROGRAM, -TAHUN dan -BULAN] TIDAK BOLEH KOSONG!!');
        //     return redirect()->to('/dtks/usulan22');
        // } else {
        // dd($filter1, $filter4, $filter5, $filter6);

        $data = $this->Usulan22Model->dataExport($filter1, $filter4, $filter5, $filter6)->getResultArray();
        // dd($data);

        $this->WilayahModel = $wilayahModel->getVillage($filter1);
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
        // $file_name = 'TEMPLATE_PENGUSULAN_PAKENJENG - ' . $this->WilayahModel['name'] . ' - ' . $filter4 . '.xlsx';
        $file_name = 'TEMPLATE_EXCEL_USULAN - PAKENJENG - ' .  $this->WilayahModel['name'] . ' - ' . strtoupper($bulan[$filter6]) . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NIK');
        $sheet->setCellValue('B1', 'PROGRAM BANSOS');
        $sheet->setCellValue('C1', 'NOKK');
        $sheet->setCellValue('D1', 'NAMA');
        $sheet->setCellValue('E1', 'TEMPAT LAHIR');
        $sheet->setCellValue('F1', "TANGGAL LAHIR\n(31/01/2000)");
        $sheet->setCellValue('G1', 'IBU KANDUNG');
        $sheet->setCellValue('H1', 'JENIS KELAMIN');
        $sheet->setCellValue('I1', 'JENIS PEKERJAAN');
        $sheet->setCellValue('J1', 'STATUS KAWIN');
        $sheet->setCellValue('K1', 'ALAMAT');
        $sheet->setCellValue('L1', 'RT');
        $sheet->setCellValue('M1', 'RW');
        $sheet->setCellValue('N1', 'PROVINSI');
        $sheet->setCellValue('O1', 'KABUPATEN');
        $sheet->setCellValue('P1', 'KECAMATAN');
        $sheet->setCellValue('Q1', 'KELURAHAN');
        $sheet->setCellValue('R1', 'STATUS DISABILITAS');
        $sheet->setCellValue('S1', 'KODE JENIS DISABILITAS');
        $sheet->setCellValue('T1', 'STATUS HAMIL');
        $sheet->setCellValue('U1', "TGL MULAI HAMIL\n(31/12/2021)");

        $styleArray = [
            'font' => [
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'wrapText'     => TRUE,
            ],
            'borders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                // 'rotation' => 90,
                'startColor' => [
                    'rgb' => '4472C4',
                ],
                'endColor' => [
                    'rgb' => '4472C4',
                ],
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);

        $count = 2;

        foreach ($data as $row) {

            $tglLahir = date('d/m/Y', strtotime($row['tanggal_lahir']));
            if ($row['hamil_status'] == 1) {
                $status_hamil = 'YA';
            } elseif ($row['hamil_status'] == 2) {
                $status_hamil = 'TIDAK';
            } else {
                $status_hamil = '';
            }

            if ($row['hamil_tgl'] > 1) {
                $hamil_tgl = date('d/m/Y', strtotime($row['hamil_tgl']));
            } else {
                $hamil_tgl = '';
            }

            $TglBuat = date('m/Y', strtotime($row['created_at']));

            $sheet->setCellValueExplicit('A' . $count, $row['du_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $count, $row['dbj_nama_bansos']);
            $sheet->setCellValueExplicit('C' . $count, $row['nokk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $count, strtoupper($row['nama']));
            $sheet->setCellValue('E' . $count, strtoupper($row['tempat_lahir']));
            $sheet->setCellValue('F' . $count, $tglLahir);
            $sheet->setCellValue('G' . $count, strtoupper($row['ibu_kandung']));
            $sheet->setCellValue('H' . $count, $row['NamaJenKel']);
            $sheet->setCellValue('I' . $count, $row['JenisPekerjaan']);
            $sheet->setCellValue('J' . $count, $row['StatusKawin']);
            $sheet->setCellValue('K' . $count, strtoupper($row['alamat']));
            $sheet->setCellValue('L' . $count, $row['rt']);
            $sheet->setCellValue('M' . $count, $row['rw']);
            $sheet->setCellValue('N' . $count, $row['prov']);
            $sheet->setCellValue('O' . $count, $row['kab']);
            $sheet->setCellValue('P' . $count, $row['kec']);
            $sheet->setCellValue('Q' . $count, $row['desa']);
            $sheet->setCellValue('R' . $count, $row['dc_status']);
            $sheet->setCellValue('S' . $count, $row['dj_kode']);
            $sheet->setCellValue('T' . $count, $status_hamil);
            $sheet->setCellValue('U' . $count, $hamil_tgl);

            $count++;
        }

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('DATA');

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
        return view('dtks/data/dtks/usulan/impor_csv', $data);
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
            return view('dtks/data/dtks/usulan/impor_csv', $data);
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
