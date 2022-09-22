<?php

namespace App\Controllers\Dtks\Dkm;


use App\Controllers\BaseController;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;
use App\Models\Dtks\ShdkModel;
use App\Models\GenModel;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\Dkm\DkmModel;
use App\Models\Dtks\DtksStatusModel;



class Kemis extends BaseController
{
    // add this in the class
    protected $_rels;
    protected $_types;
    public function __construct()
    {

        $this->WilayahModel = new WilayahModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->datashdk = new ShdkModel();
        $this->GenModel = new GenModel();
        $this->AuthModel = new AuthModel();
        $this->DkmModel = new DkmModel();
        $this->statusdtks = new DtksStatusModel();
    }

    public function index()
    {
        $user_login = $this->AuthModel->getUserId();
        // dd($user_login);
        $data = [
            'title' => 'Daftar Keluarga Miskin',
            'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            // 'operator' => $this->operator->orderBy('NamaLengkap', 'asc')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'datart' => $this->RtModel->noRt(),

            'status' => $this->statusdtks->limit(2)->get()->getResultArray(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_login' => $user_login,
        ];

        return view('dtks/dkm/index', $data);
    }

    public function tabel_data()
    {
        $model = new DkmModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter0 = $this->request->getPost('datastatus');
        $filter1 = $this->request->getPost('datadesa');
        // $operator = $this->request->getPost('operator');
        $filter2 = $this->request->getPost('datarw');
        $filter3 = $this->request->getPost('datart');
        $filter4 = $this->request->getPost('dataDelete');

        $listing = $model->get_datatables($filter0, $filter1, $filter2, $filter3, $filter4);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter0, $filter1, $filter2, $filter3, $filter4);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <a href=' . dkm_foto_cpm('DKM_FP' . $key->dd_nik . '.jpg', 'foto-cpm') . ' data-lightbox="dataCpm' . $key->dd_nik . '"' . ' data-title="Foto Calon Penerima Manfaat">
            <img src=' . dkm_foto_cpm('DKM_FP' . $key->dd_nik . '.jpg', 'foto-cpm') . ' style="width: 30px; height: 40px; border-radius: 2px;">
            </a>
            <a href=' . dkm_foto_cpm('DKM_FH' . $key->dd_nik . '.jpg', 'foto-rumah-depan') . ' data-lightbox="dataCpm' . $key->dd_nik . '"' . ' data-title="Foto Rumah Depan"></a>
            <a href=' . dkm_foto_cpm('DKM_BH' . $key->dd_nik . '.jpg', 'foto-rumah-belakang') . ' data-lightbox="dataCpm' . $key->dd_nik . '"' . ' data-title="Foto Rumah Belakang"></a>
            <a href=' . dkm_foto_cpm('DKM_KK' . $key->dd_nik . '.jpg', 'foto-kk') . ' data-lightbox="dataCpm' . $key->dd_nik . '"' . ' data-title="Foto Kartu Keluarga"></a>
            ';
            $row[] = $key->dd_nama;
            $row[] = $key->dd_nik;
            $row[] = $key->dd_alamat;
            $row[] = $key->dd_rt;
            $row[] = $key->dd_rw;
            $row[] = '<a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="detail_person(' . "'" . $key->dd_id . "'" . ')"><i class="fas fa-user-edit mr-1"></i></i> Edit</a>
            <button class="btn btn-sm btn-danger" data-id="' . $key->dd_id . '" data-nama="' . $key->dd_nama . '" id="deleteBtn"><i class="far fa-trash-alt mr-1"></i> Hapus</button>
            ';
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

    public function formview()
    {
        if ($this->request->isAJAX()) {

            // var_dump($this->request->getPost());
            $kode_kec = Profil_Admin()['kode_kec'];
            $id = $this->request->getVar('id');

            $model = new DkmModel();
            $row = $model->find($id);

            // var_dump($row);
            // $kode_kab = $row['db_regency'];

            $data = [
                'title' => 'Detail',
                'role_id' => session()->get('role_id'),
                // 'dataprov' => $this->WilayahModel->getProv()->getResultArray(),
                // 'datakab' => $this->WilayahModel->getKab()->getResultArray(),
                // 'datakec' => $this->WilayahModel->getKec($kode_kab)->getResultArray(),
                'datadesa' => $this->WilayahModel->getDesa($kode_kec),
                'datadusun' => $this->WilayahModel->getDusun()->getResultArray(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                // 'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
                'status' => $this->statusdtks->limit(2)->get()->getResultArray(),
                // 'jenisKelamin' => $this->BnbaModel->getDataJenkel(),
                // 'datashdk' => $this->BnbaModel->getDataShdk(),

                'dd_id' => $row['dd_id'],
                'dd_nik' => $row['dd_nik'],
                'dd_nkk' => $row['dd_nkk'],
                'dd_nama' => $row['dd_nama'],
                'dd_alamat' => $row['dd_alamat'],
                'dd_rt' => $row['dd_rt'],
                'dd_rw' => $row['dd_rw'],
                'dd_desa' => $row['dd_desa'],
                'dd_kec' => $row['dd_kec'],
                'dd_kab' => $row['dd_kab'],
                'dd_adminduk' => $row['dd_adminduk'],
                'dd_adminduk_foto' => $row['dd_adminduk_foto'],
                'dd_bpjs' => $row['dd_bpjs'],
                'dd_bpjs_foto' => $row['dd_bpjs_foto'],
                'dd_blt' => $row['dd_blt'],
                'dd_blt_deskripsi' => $row['dd_blt_deskripsi'],
                'dd_blt_dd' => $row['dd_blt_dd'],
                'dd_blt_dd_deskripsi' => $row['dd_blt_dd_deskripsi'],
                'dd_bpnt' => $row['dd_bpnt'],
                'dd_bpnt_deskripsi' => $row['dd_bpnt_deskripsi'],
                'dd_pkh' => $row['dd_pkh'],
                'dd_pkh_deskripsi' => $row['dd_pkh_deskripsi'],
                'dd_foto_cpm' => $row['dd_foto_cpm'],
                'dd_foto_rumah_depan' => $row['dd_foto_rumah_depan'],
                'dd_foto_rumah_belakang' => $row['dd_foto_rumah_belakang'],
                'dd_foto_kk' => $row['dd_foto_kk'],
                'dd_latitude' => $row['dd_latitude'],
                'dd_longitude' => $row['dd_longitude'],
                'dd_status' => $row['dd_status'],
                'dd_created_by' => $row['dd_created_by'],
                'dd_created_at' => $row['dd_created_at'],
            ];


            // var_dump($data['datadesa']);
            // die;
            $msg = [
                'sukses' => view('dtks/dkm/modaledit', $data)

            ];

            echo json_encode($msg);
        }
    }

    public function update_data()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());
            // var_dump($this->request->getPost('dd_foto_cpm'));
            // var_dump($this->request->getFile('dd_foto_cpm'));
            // die;

            $id = $this->request->getPost('dd_id');
            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'dd_nkk' => [
                    'label' => 'No. KK',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berupa angka.',
                        'min_length' => '{field} harus berupa {param} karakter.',
                        'max_length' => '{field} harus berupa {param} karakter.'
                    ]
                ],
                'dd_nama' => [
                    'label' => 'Nama',
                    'rules' => 'required|min_length[3]|max_length[150]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'min_length' => '{field} harus berupa {param} karakter.',
                        'max_length' => '{field} harus berupa {param} karakter.'
                    ]
                ],
                'dd_nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[dtks_dkm.dd_nik,dd_id,{dd_id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berupa angka.',
                        'min_length' => '{field} harus berupa {param} karakter.',
                        'max_length' => '{field} harus berupa {param} karakter.',
                        'is_unique' => '{field} sudah terdaftar'
                    ]
                ],
                'dd_alamat' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dd_rt' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dd_rw' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dd_foto_cpm' => [
                    'label' => 'Foto CPM',
                    'rules' => 'uploaded[dd_foto_cpm]|is_image[dd_foto_cpm]|mime_in[dd_foto_cpm,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => '{field} harus ada.',
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
                'dd_foto_rumah_depan' => [
                    'label' => 'Foto Depan Rumah',
                    'rules' => 'uploaded[dd_foto_rumah_depan]|is_image[dd_foto_rumah_depan]|mime_in[dd_foto_rumah_depan,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => '{field} harus ada.',
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
                'dd_foto_rumah_belakang' => [
                    'label' => 'Foto Belakang Rumah',
                    'rules' => 'uploaded[dd_foto_rumah_belakang]|is_image[dd_foto_rumah_belakang]|mime_in[dd_foto_rumah_belakang,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => '{field} harus ada.',
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
                'dd_latitude' => [
                    'label' => 'Latitude',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus harus ada.',
                    ]
                ],
                'dd_longitude' => [
                    'label' => 'Longitude',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus harus ada.',
                    ]
                ],
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'dd_nkk' => $this->validator->getError('dd_nkk'),
                        'dd_nama' => $this->validator->getError('dd_nama'),
                        'dd_nik' => $this->validator->getError('dd_nik'),
                        'dd_alamat' => $validation->getError('dd_alamat'),
                        'dd_rt' => $validation->getError('dd_rt'),
                        'dd_rw' => $validation->getError('dd_rw'),
                        'dd_adminduk_foto' => $validation->getError('dd_adminduk_foto'),
                        'dd_bpjs_foto' => $validation->getError('dd_bpjs_foto'),
                        'dd_foto_cpm' => $validation->getError('dd_foto_cpm'),
                        'dd_foto_rumah_depan' => $validation->getError('dd_foto_rumah_depan'),
                        'dd_foto_rumah_belakang' => $validation->getError('dd_foto_rumah_belakang'),
                        'dd_latitude' => $validation->getError('dd_latitude'),
                        'dd_longitude' => $validation->getError('dd_longitude')
                    ]
                ];
            } else {

                $kode_desa = session()->get('kode_desa');
                $namaDesa = $this->WilayahModel->getVillage($kode_desa);
                $desaNama = $namaDesa['name'];

                $dd_foto_cpm = $this->request->getFile('dd_foto_cpm');
                $dd_foto_rumah_depan = $this->request->getFile('dd_foto_rumah_depan');
                $dd_foto_rumah_belakang = $this->request->getFile('dd_foto_rumah_belakang');
                $dd_foto_kk = $this->request->getFile('dd_foto_kk');

                // var_dump($dd_foto_cpm);
                // die;

                // get filename by vg_nik
                $filename_satu = 'DKM_FP' . $this->request->getPost('dd_nik') . '.jpg';
                $filename_dua = 'DKM_FH' . $this->request->getPost('dd_nik') . '.jpg';
                $filename_tiga = 'DKM_BH' . $this->request->getPost('dd_nik') . '.jpg';
                $filename_empat = 'DKM_KK' . $this->request->getPost('dd_nik') . '.jpg';

                $img_satu = imagecreatefromjpeg($dd_foto_cpm);
                $img_dua = imagecreatefromjpeg($dd_foto_rumah_depan);
                $img_tiga = imagecreatefromjpeg($dd_foto_rumah_belakang);
                $img_empat = imagecreatefromjpeg($dd_foto_kk);

                // get width and height of image
                $width_satu = imagesx($img_satu);
                $height_satu = imagesy($img_satu);
                $width_dua = imagesx($img_dua);
                $height_dua = imagesy($img_dua);
                $width_tiga = imagesx($img_tiga);
                $height_tiga = imagesy($img_tiga);
                $width_empat = imagesx($img_empat);
                $height_empat = imagesy($img_empat);

                // reorient image if width is greater than height
                if ($width_satu > $height_satu) {
                    $img_satu = imagerotate($img_satu, -90, 0);
                }
                if ($width_dua > $height_dua) {
                    $img_dua = imagerotate($img_dua, -90, 0);
                }
                if ($width_tiga > $height_tiga) {
                    $img_tiga = imagerotate($img_tiga, -90, 0);
                }
                if (
                    $width_empat > $height_empat
                ) {
                    $img_empat = imagerotate($img_empat, -90, 0);
                }
                // resize image
                $img_satu = imagescale($img_satu, 480, 640);
                $img_dua = imagescale($img_dua, 480, 640);
                $img_tiga = imagescale($img_tiga, 480, 640);
                $img_empat = imagescale($img_empat, 480, 640);

                $txtNik = $this->request->getPost('dd_nik');
                $txtNama = $this->request->getPost('dd_nama');
                $txtAlamat = $this->request->getPost('dd_alamat');
                $txtKelurahan = $desaNama;
                $txtKecamatan = 'PAKENJENG';
                $txtKabupaten = 'GARUT';
                $txtProvinsi = 'JAWA BARAT';
                $txtLat = $this->request->getPost('dd_latitude');
                $txtLang = $this->request->getPost('dd_longitude');

                $txt = "NIK : " . $txtNik . "\nNama : " . $txtNama . "\nAlamat : " . $txtAlamat . "\nDesa/Kelurahan : " . $txtKelurahan . "\nKecamatan : " . $txtKecamatan . "\nKabupaten : " . $txtKabupaten . "\nProvinsi : " . $txtProvinsi . "\nLokasi : " . $txtLat . ", " . $txtLang . "\n\n@" . nameApp() . " Kec. " . ucwords(strtolower(Profil_Admin()['namaKec']));
                $fontFile = FCPATH . 'assets/fonts/Futura Bk BT Book.ttf';

                $txtLain = "@" . nameApp() . " Kec. " . ucwords(strtolower(Profil_Admin()['namaKec']));
                $fontFile = FCPATH . 'assets/fonts/Futura Bk BT Book.ttf';

                $fontSizeSatu = 0.020 * imagesx($img_satu);
                $whiteSatu = imagecolorallocate($img_satu, 255, 255, 255);
                $strokeColorSatu = imagecolorallocate($img_satu, 0, 0, 0);

                $fontSizeDua = 0.020 * imagesx($img_dua);
                $whiteDua = imagecolorallocate($img_dua, 255, 255, 255);
                $strokeColorDua = imagecolorallocate($img_dua, 0, 0, 0);

                $fontSizeTiga = 0.020 * imagesx($img_tiga);
                $whiteTiga = imagecolorallocate($img_tiga, 255, 255, 255);
                $strokeColorTiga = imagecolorallocate($img_tiga, 0, 0, 0);

                // pos x from left, pos y from bottom
                $posXsatu = 0.02 * imagesx($img_satu);
                $posYsatu = 0.75 * imagesy($img_satu);

                $posXdua = 0.02 * imagesx($img_dua);
                $posYdua = 0.75 * imagesy($img_dua);

                $posXtiga = 0.02 * imagesx($img_tiga);
                $posYtiga = 0.75 * imagesy($img_tiga);

                // $posX = 10;
                // $posY = 830;
                $angle = 0;

                imagettfstroketext($img_satu, $fontSizeSatu, $angle, $posXsatu, $posYsatu, $whiteSatu, $strokeColorSatu, $fontFile, $txt, 1);
                imagettfstroketext($img_dua, $fontSizeDua, $angle, $posXdua, $posYdua, $whiteDua, $strokeColorDua, $fontFile, $txt, 1);
                imagettfstroketext($img_tiga, $fontSizeTiga, $angle, $posXtiga, $posYtiga, $whiteTiga, $strokeColorTiga, $fontFile, $txt, 1);


                header("Content-type: image/jpg");
                $quality = 90; // 0 to 100

                // var_dump($img_satu);
                // die;

                imagejpeg($img_satu, 'data/dkm/foto-cpm/' . $filename_satu, $quality);
                imagejpeg(
                    $img_dua,
                    'data/dkm/foto-rumah-depan/' . $filename_dua,
                    $quality
                );
                imagejpeg($img_tiga, 'data/dkm/foto-rumah-belakang/' . $filename_tiga, $quality);
                imagejpeg($img_empat, 'data/dkm/foto-kk/' . $filename_empat, $quality);
                // var_dump($img_satu);
                // die;

                $data = [
                    'dd_nkk' => $this->request->getPost('dd_nkk'),
                    'dd_nama' => $this->request->getPost('dd_nama'),
                    'dd_nik' => $this->request->getPost('dd_nik'),
                    'dd_alamat' => $this->request->getPost('dd_alamat'),
                    'dd_rt' => $this->request->getPost('dd_rt'),
                    'dd_rw' => $this->request->getPost('dd_rw'),
                    'dd_desa' => session()->get('kode_desa'),
                    'dd_kec' => Profil_Admin()['kode_kec'],
                    'dd_kab' => Profil_Admin()['kode_kab'],
                    'dd_adminduk' => $this->request->getPost('dd_adminduk'),
                    'dd_bpjs' => $this->request->getPost('dd_bpjs'),
                    'dd_blt' => $this->request->getPost('dd_blt'),
                    'dd_blt_dd' => $this->request->getPost('dd_blt_dd'),
                    'dd_bpnt' => $this->request->getPost('dd_bpnt'),
                    'dd_pkh' => $this->request->getPost('dd_pkh'),
                    'dd_foto_cpm' => $filename_satu,
                    'dd_foto_rumah_depan' => $filename_dua,
                    'dd_foto_rumah_belakang' => $filename_tiga,
                    'dd_foto_kk' => $filename_empat,
                    'dd_latitude' => $this->request->getPost('dd_latitude'),
                    'dd_longitude' => $this->request->getPost('dd_longitude'),
                    // 'dd_status' => $this->request->getPost('dd_status'),
                ];
                // ];
                // var_dump($data);
                // die;
                $this->DkmModel->update($id, $data);
                // imagedestroy($img_satu, $img_dua, $img_tiga, $img_empat);

                $msg = [
                    'sukses' => 'Update Data, Berhasil!',
                ];
            }
            echo json_encode($msg);
        }
    }

    public function formTmb()
    {
        if ($this->request->isAJAX()) {

            $district_id = Profil_Admin()['kode_kec'];
            $data = [
                'title' => 'Form. Tambah Data',
                'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', $district_id)->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
                'jenisKelamin' => $this->GenModel->getDataJenkel(),
                'statusKawin' => $this->GenModel->getDataStatusKawin(),
                'verivali_pbi' => $this->GenModel->getDataVerivaliPbi(),
            ];

            $msg = [
                'data' => view('dtks/dkm/modaltambah', $data),
            ];
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function simpan_data()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost('dd_foto_cpm'));
            // var_dump($this->request->getFile('dd_foto_cpm'));
            // die;

            // foreach ($namaDesa as $dn) {
            // }
            // var_dump($namaDesa['name']);
            // die;

            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'dd_nkk' => [
                    'label' => 'No. KK',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berupa angka.',
                        'min_length' => '{field} harus berupa {param} karakter.',
                        'max_length' => '{field} harus berupa {param} karakter.'
                    ]
                ],
                'dd_nama' => [
                    'label' => 'Nama',
                    'rules' => 'required|min_length[3]|max_length[150]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'min_length' => '{field} harus berupa {param} karakter.',
                        'max_length' => '{field} harus berupa {param} karakter.'
                    ]
                ],
                'dd_nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]|is_unique[dtks_dkm.dd_nik,dd_id,{dd_id}]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berupa angka.',
                        'min_length' => '{field} harus berupa {param} karakter.',
                        'max_length' => '{field} harus berupa {param} karakter.',
                        'is_unique' => '{field} sudah terdaftar'
                    ]
                ],
                'dd_alamat' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dd_rt' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dd_rw' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dd_foto_cpm' => [
                    'label' => 'Foto CPM',
                    'rules' => 'uploaded[dd_foto_cpm]|is_image[dd_foto_cpm]|mime_in[dd_foto_cpm,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => '{field} harus ada.',
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
                'dd_foto_rumah_depan' => [
                    'label' => 'Foto Depan Rumah',
                    'rules' => 'uploaded[dd_foto_rumah_depan]|is_image[dd_foto_rumah_depan]|mime_in[dd_foto_rumah_depan,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => '{field} harus ada.',
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
                'dd_foto_rumah_belakang' => [
                    'label' => 'Foto Belakang Rumah',
                    'rules' => 'uploaded[dd_foto_rumah_belakang]|is_image[dd_foto_rumah_belakang]|mime_in[dd_foto_rumah_belakang,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => '{field} harus ada.',
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
                'dd_latitude' => [
                    'label' => 'Latitude',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus harus ada.',
                    ]
                ],
                'dd_longitude' => [
                    'label' => 'Longitude',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus harus ada.',
                    ]
                ],
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'dd_nkk' => $this->validator->getError('dd_nkk'),
                        'dd_nama' => $this->validator->getError('dd_nama'),
                        'dd_nik' => $this->validator->getError('dd_nik'),
                        'dd_alamat' => $validation->getError('dd_alamat'),
                        'dd_rt' => $validation->getError('dd_rt'),
                        'dd_rw' => $validation->getError('dd_rw'),
                        'dd_adminduk_foto' => $validation->getError('dd_adminduk_foto'),
                        'dd_bpjs_foto' => $validation->getError('dd_bpjs_foto'),
                        'dd_foto_cpm' => $validation->getError('dd_foto_cpm'),
                        'dd_foto_rumah_depan' => $validation->getError('dd_foto_rumah_depan'),
                        'dd_foto_rumah_belakang' => $validation->getError('dd_foto_rumah_belakang'),
                        'dd_latitude' => $validation->getError('dd_latitude'),
                        'dd_longitude' => $validation->getError('dd_longitude')
                    ]
                ];
            } else {

                $kode_desa = session()->get('kode_desa');
                $namaDesa = $this->WilayahModel->getVillage($kode_desa);
                $desaNama = $namaDesa['name'];

                $dd_foto_cpm = $this->request->getFile('dd_foto_cpm');
                $dd_foto_rumah_depan = $this->request->getFile('dd_foto_rumah_depan');
                $dd_foto_rumah_belakang = $this->request->getFile('dd_foto_rumah_belakang');
                $dd_foto_kk = $this->request->getFile('dd_foto_kk');

                // var_dump($dd_foto_cpm);
                // die;


                // get filename by vg_nik
                // $filename_satu = 'DKM_FP' . $cekdata['dd_nik'] . '.jpg';
                // $filename_dua = 'DKM_FH' . $cekdata['dd_nik'] . '.jpg';
                // $filename_tiga = 'DKM_BH' . $cekdata['dd_nik'] . '.jpg';
                // $filename_empat = 'DKM_empat' . $cekdata['dd_nik'] . '.jpg';

                $filename_satu = 'DKM_FP' . $this->request->getPost('dd_nik') . '.jpg';
                $filename_dua = 'DKM_FH' . $this->request->getPost('dd_nik') . '.jpg';
                $filename_tiga = 'DKM_BH' . $this->request->getPost('dd_nik') . '.jpg';
                $filename_empat = 'DKM_KK' . $this->request->getPost('dd_nik') . '.jpg';

                $img_satu = imagecreatefromjpeg($dd_foto_cpm);
                $img_dua = imagecreatefromjpeg($dd_foto_rumah_depan);
                $img_tiga = imagecreatefromjpeg($dd_foto_rumah_belakang);
                $img_empat = imagecreatefromjpeg($dd_foto_kk);

                // get width and height of image
                $width_satu = imagesx($img_satu);
                $height_satu = imagesy($img_satu);

                $width_dua = imagesx($img_dua);
                $height_dua = imagesy($img_dua);

                $width_tiga = imagesx($img_tiga);
                $height_tiga = imagesy($img_tiga);

                $width_empat = imagesx($img_empat);
                $height_empat = imagesy($img_empat);

                // reorient image if width is greater than height
                if ($width_satu > $height_satu) {
                    $img_satu = imagerotate($img_satu, -90, 0);
                }
                if ($width_dua > $height_dua) {
                    $img_dua = imagerotate($img_dua, -90, 0);
                }
                if ($width_tiga > $height_tiga) {
                    $img_tiga = imagerotate($img_tiga, -90, 0);
                }
                if ($width_empat > $height_empat) {
                    $img_empat = imagerotate($img_empat, -90, 0);
                }
                // resize image
                $img_satu = imagescale($img_satu, 480, 640);
                $img_dua = imagescale($img_dua, 480, 640);
                $img_tiga = imagescale($img_tiga, 480, 640);
                $img_empat = imagescale($img_empat, 480, 640);

                $txtNik = $this->request->getPost('dd_nik');
                $txtNama = $this->request->getPost('dd_nama');
                $txtAlamat = $this->request->getPost('dd_alamat');
                $txtKelurahan = $desaNama;
                $txtKecamatan = 'PAKENJENG';
                $txtKabupaten = 'GARUT';
                $txtProvinsi = 'JAWA BARAT';
                $txtLat = $this->request->getPost('dd_latitude');
                $txtLang = $this->request->getPost('dd_longitude');

                $txt = "NIK : " . $txtNik . "\nNama : " . $txtNama . "\nAlamat : " . $txtAlamat . "\nDesa/Kelurahan : " . $txtKelurahan . "\nKecamatan : " . $txtKecamatan . "\nKabupaten : " . $txtKabupaten . "\nProvinsi : " . $txtProvinsi . "\nLokasi : " . $txtLat . ", " . $txtLang . "\n\n@" . nameApp() . " Kec. " . ucwords(strtolower(Profil_Admin()['namaKec']));
                $fontFile = FCPATH . 'assets/fonts/Futura Bk BT Book.ttf';

                $txtLain = "@" . nameApp() . " Kec. " . ucwords(strtolower(Profil_Admin()['namaKec']));
                $fontFile = FCPATH . 'assets/fonts/Futura Bk BT Book.ttf';

                $fontSizeSatu = 0.020 * imagesx($img_satu);
                $whiteSatu = imagecolorallocate($img_satu, 255, 255, 255);
                $strokeColorSatu = imagecolorallocate($img_satu, 0, 0, 0);

                $fontSizeDua = 0.020 * imagesx($img_dua);
                $whiteDua = imagecolorallocate($img_dua, 255, 255, 255);
                $strokeColorDua = imagecolorallocate($img_dua, 0, 0, 0);

                $fontSizeTiga = 0.020 * imagesx($img_tiga);
                $whiteTiga = imagecolorallocate($img_tiga, 255, 255, 255);
                $strokeColorTiga = imagecolorallocate($img_tiga, 0, 0, 0);

                // pos x from left, pos y from bottom
                $posXsatu = 0.02 * imagesx($img_satu);
                $posYsatu = 0.75 * imagesy($img_satu);

                $posXdua = 0.02 * imagesx($img_dua);
                $posYdua = 0.75 * imagesy($img_dua);

                $posXtiga = 0.02 * imagesx($img_tiga);
                $posYtiga = 0.75 * imagesy($img_tiga);

                // $posX = 10;
                // $posY = 830;
                $angle = 0;

                // stroke watermark image
                imagettfstroketext($img_satu, $fontSizeSatu, $angle, $posXsatu, $posYsatu, $whiteSatu, $strokeColorSatu, $fontFile, $txt, 1);
                imagettfstroketext($img_dua, $fontSizeDua, $angle, $posXdua, $posYdua, $whiteDua, $strokeColorDua, $fontFile, $txt, 1);
                imagettfstroketext($img_tiga, $fontSizeTiga, $angle, $posXtiga, $posYtiga, $whiteTiga, $strokeColorTiga, $fontFile, $txt, 1);


                header("Content-type: image/jpg");
                $quality = 90; // 0 to 100

                // var_dump($img_satu);
                // die;

                imagejpeg($img_satu, 'data/dkm/foto-cpm/' . $filename_satu, $quality);
                imagejpeg($img_dua, 'data/dkm/foto-rumah-depan/' . $filename_dua, $quality);
                imagejpeg($img_tiga, 'data/dkm/foto-rumah-belakang/' . $filename_tiga, $quality);
                imagejpeg($img_empat, 'data/dkm/foto-kk/' . $filename_empat, $quality);
                // var_dump($img_satu);
                // die;

                $data = [
                    'dd_nkk' => $this->request->getPost('dd_nkk'),
                    'dd_nama' => $this->request->getPost('dd_nama'),
                    'dd_nik' => $this->request->getPost('dd_nik'),
                    'dd_alamat' => $this->request->getPost('dd_alamat'),
                    'dd_rt' => $this->request->getPost('dd_rt'),
                    'dd_rw' => $this->request->getPost('dd_rw'),
                    'dd_desa' => session()->get('kode_desa'),
                    'dd_kec' => Profil_Admin()['kode_kec'],
                    'dd_kab' => Profil_Admin()['kode_kab'],
                    'dd_adminduk' => $this->request->getPost('dd_adminduk'),
                    'dd_bpjs' => $this->request->getPost('dd_bpjs'),
                    'dd_blt' => $this->request->getPost('dd_blt'),
                    'dd_blt_dd' => $this->request->getPost('dd_blt_dd'),
                    'dd_bpnt' => $this->request->getPost('dd_bpnt'),
                    'dd_pkh' => $this->request->getPost('dd_pkh'),
                    'dd_foto_cpm' => $filename_satu,
                    'dd_foto_rumah_depan' => $filename_dua,
                    'dd_foto_rumah_belakang' => $filename_tiga,
                    'dd_foto_kk' => $filename_empat,
                    'dd_latitude' => $this->request->getPost('dd_latitude'),
                    'dd_longitude' => $this->request->getPost('dd_longitude'),
                    'dd_status' => 1,
                ];
                // ];
                // var_dump($data);
                // die;
                $this->DkmModel->save($data);
                // imagedestroy($img_satu, $img_dua, $img_tiga, $img_empat);

                $msg = [
                    'sukses' => 'Simpan Data, Berhasil!',
                ];
            }
            echo json_encode($msg);
        }
    }

    function delete()
    {
        if ($this->request->isAJAX()) {


            if (deadline_usulan() == 1) {
                $msg = [
                    'informasi' => 'Mohon Maaf, Batas waktu untuk Perubahan Data, Telah Habis!!'
                ];
            } else {
                $id = $this->request->getVar('id');
                $this->DkmModel->delete($id);
                $msg = [
                    'sukses' => 'Data berhasil dihapus'
                ];
            }
            echo json_encode($msg);
        } else {

            return redirect()->to('lockscreen');
        }
    }
}
