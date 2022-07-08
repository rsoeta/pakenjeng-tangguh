<?php

namespace App\Controllers\Dtks;

use App\Models\Dtks\AuthModel;
use App\Models\WilayahModel;
use App\Models\GenModel;
use App\Models\RwModel;
use App\Models\Dtks\VerivaliGeoModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



use App\Controllers\BaseController;

class Geotagging extends BaseController
{
    // add this in the class
    protected $_rels;
    protected $_types;
    public function __construct()
    {
        $this->WilayahModel = new WilayahModel();
        $this->AuthModel = new AuthModel();
        $this->GenModel = new GenModel();
        $this->RwModel = new RwModel();
        $this->VerivaliGeoModel = new VerivaliGeoModel();
    }


    public function index()
    {
        $db = \Config\Database::connect();

        $capaianPdtt = $this->VerivaliGeoModel->jml_persentase();
        // dd($capaianPdtt);
        if (!empty($capaianPdtt)) {
            foreach ($capaianPdtt as $row) {
                $dataCapaian['label'][] = $row['namaDesa'];
                $dataCapaian['dataCapaian'][] = $row['dataCapaian'];
            }
        } else {
            $dataCapaian['label'] = [];
            $dataCapaian['dataCapaian'] = [];
        }

        $data = [
            'title' => 'Verifikasi dan Validasi PDTT',
            'user_login' => $this->AuthModel->getUserId(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'dataStatus2' => $db->table('dtks_status')->get()->getResultArray(),
            'Bansos' => $db->table('dtks_bansos_jenis')->get()->getResultArray(),
            'dataCapaian' => json_encode($dataCapaian),
            'indikasiTemuan' => $db->table('tb_ket_temuan')->get()->getResultArray()

        ];
        // dd($data['capaianPdtt']);

        return view('dtks/data/dtks/file_bpk/index', $data);
    }

    public function tabel_data()
    {
        $db = \Config\Database::connect();

        $model = new VerivaliGeoModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();


        $filter1 = $this->request->getPost('datadesa');
        $filter2 = $this->request->getPost('datarw');
        $filter3 = $this->request->getPost('datart');
        $filter4 = $this->request->getPost('dataBansos');
        $filter5 = '0';
        $filter6 = $this->request->getPost('dataIndikasi');
        // var_dump($filter6);
        // die;

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->vg_nama_lengkap;
            $row[] = $key->vg_nik;
            $row[] = $key->vg_nkk;
            $row[] = $key->vg_alamat;
            $row[] = $key->namaDesa;

            // get name of 
            $bansosSatu = $key->vg_dbj_id1;
            $bansosSatuNama = $db->table('dtks_bansos_jenis')->where('dbj_id', $bansosSatu)->get()->getRowArray();
            if ($bansosSatuNama != null || $bansosSatuNama != 0 || $bansosSatuNama != '') {
                $jenisBansosSatu = '<span class="badge bg-success">' . $bansosSatuNama['dbj_nama_bansos'] . '</span>';
            } else {
                $jenisBansosSatu = '';
            }

            $bansosDua = $key->vg_dbj_id2;
            $bansosDuaNama = $db->table('dtks_bansos_jenis')->where('dbj_id', $bansosDua)->get()->getRowArray();
            if ($bansosDuaNama != null || $bansosDuaNama != 0 || $bansosDuaNama != '') {
                $jenisBansosDua = '<span class="badge bg-success">' . $bansosDuaNama['dbj_nama_bansos'] . '</span>';
            } else {
                $jenisBansosDua = '';
            }

            // $jenisBansosSatu = $bansosSatu;
            // $jenisBansosDua = $bansosDua;

            // $row[] = '<span class="badge bg-success">' . $bansosSatuNama['dbj_nama_bansos'] . '</span>' . ' ' . '<span class="badge bg-success">' . $bansosDuaNama['dbj_nama_bansos'] . '</span>';
            $row[] = $jenisBansosSatu . ' ' . $jenisBansosDua;
            $row[] = $key->sta_nama;
            $row[] = '<a class="btn btn-sm" href="javascript:void(0)" title="Ambil Foto" onclick="edit_person(' . "'" . $key->vg_id . "'" . ')"><i class="fa fa-camera-retro fa-2x"></i></a>';
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

    public function tabel_data2()
    {
        $db = \Config\Database::connect();

        $model = new VerivaliGeoModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();


        $filter1 = $this->request->getPost('datadesa2');
        $filter2 = $this->request->getPost('datarw2');
        $filter3 = $this->request->getPost('dataStatusPm');
        $filter4 = $this->request->getPost('dataBansos2');
        $filter5 = $this->request->getPost('dataStatus2');
        $filter6 = $this->request->getPost('dataIndikasi2');

        $listing = $model->get_datatables2($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);
        $jumlah_semua = $model->jumlah_semua2();
        $jumlah_filter = $model->jumlah_filter2($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->vg_nama_lengkap;
            $row[] = $key->vg_nik;
            $row[] = $key->vg_nkk;
            $row[] = $key->vg_alamat;
            $row[] = $key->namaDesa;

            // get name of 
            $bansosSatu = $key->vg_dbj_id1;
            $bansosSatuNama = $db->table('dtks_bansos_jenis')->where('dbj_id', $bansosSatu)->get()->getRowArray();
            if ($bansosSatuNama != null || $bansosSatuNama != 0 || $bansosSatuNama != '') {
                $jenisBansosSatu = '<span class="badge bg-success">' . $bansosSatuNama['dbj_nama_bansos'] . '</span>';
            } else {
                $jenisBansosSatu = '';
            }

            $bansosDua = $key->vg_dbj_id2;
            $bansosDuaNama = $db->table('dtks_bansos_jenis')->where('dbj_id', $bansosDua)->get()->getRowArray();
            if ($bansosDuaNama != null || $bansosDuaNama != 0 || $bansosDuaNama != '') {
                $jenisBansosDua = '<span class="badge bg-success">' . $bansosDuaNama['dbj_nama_bansos'] . '</span>';
            } else {
                $jenisBansosDua = '';
            }

            // $jenisBansosSatu = $bansosSatu;
            // $jenisBansosDua = $bansosDua;
            // $row[] = '<span class="badge bg-success">' . $bansosSatuNama['dbj_nama_bansos'] . '</span>' . ' ' . '<span class="badge bg-success">' . $bansosDuaNama['dbj_nama_bansos'] . '</span>';
            $row[] = $jenisBansosSatu . ' ' . $jenisBansosDua;
            $row[] = $key->sta_nama;
            // $row[] = '<img id="myImg" src="' . base_url('data/foto_pm/PDTT_FP' . $key->vg_nik . '.jpg') . '" alt="' . $key->vg_nama_lengkap . '" style="width:10%;max-width:100px"> 
            //          <img id="myImg2" src="' . base_url('data/foto_rumah/PDTT_FR' . $key->vg_nik . '.jpg') . '" alt="' . $key->vg_nama_lengkap . '" style="width:10%;max-width:100px">';
            $row[] = '<a href="' . base_url('data/foto_pm/PDTT_FP' . $key->vg_nik . '.jpg') . '" data-lightbox="roadtrip" data-title="' . $key->vg_nama_lengkap . '"><img src="' . base_url('data/foto_pm/PDTT_FP' . $key->vg_nik . '.jpg') . '" alt="' . $key->vg_nama_lengkap . '" style="width:10%;max-width:100px"></a>
                      <a href="' . base_url('data/foto_rumah/PDTT_FR' . $key->vg_nik . '.jpg') . '" data-lightbox="roadtrip" data-title="' . $key->vg_nama_lengkap . '"><img src="' . base_url('data/foto_rumah/PDTT_FR' . $key->vg_nik . '.jpg') . '" alt="' . $key->vg_nama_lengkap . '" style="width:10%;max-width:100px"></a>';
            $row[] = '<a class="btn btn-sm" href="javascript:void(0)" title="Detail" onclick="edit_person(' . "'" . $key->vg_id . "'" . ')"><i class="fa fa-info-circle"></i></a>';
            $data[] = $row;
        }

        // dd($data[$row]);
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $jumlah_semua->jml,
            "recordsFiltered" => $jumlah_filter->jml,
            "data" => $data,
        );
        $output[$csrfName] = $csrfHash;
        echo json_encode($output);
    }

    public function simpanExcel()
    {
        // dd($this->request->getPost());
        if ($this->request->getPost(['submit'])) {
            // truncate table dtks_verivali_geo
            $this->VerivaliGeoModel->table('dtks_verivali_geo')->truncate();

            // insert data from excel
            $file_excel = $this->request->getFile('file');
            $ext = $file_excel->getClientExtension();
            if ($ext == 'xls') {
                $render = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else {
                $render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet = $render->load($file_excel);

            $data = $spreadsheet->getActiveSheet()->toArray();

            // dd($data);
            foreach ($data as $x => $row) {
                if ($x == 0) {
                    continue;
                }
                $vg_no_data = $row[1];
                $vg_nik = $row[2];
                $vg_nik_ktp = $row[3];
                $vg_nama_lengkap = $row[4];
                $vg_nama_ktp = $row[5];
                $vg_nkk = $row[6];
                $vg_alamat = $row[7];
                $vg_rt = $row[8];
                $vg_rw = $row[9];
                $vg_desa = $row[10];
                $vg_kec = $row[11];
                $vg_kab = $row[12];
                $vg_prov = $row[13];
                $vg_dbj_id1 = $row[14];
                $vg_dbj_id2 = $row[15];
                $vg_norek = $row[16];
                $vg_source = $row[17];
                $vg_fp = $row[18];
                $vg_fr = $row[19];
                $vg_fktp = $row[20];
                $vg_fkk = $row[21];
                $vg_lat = $row[22];
                $vg_lang = $row[23];
                $vg_created_by = session()->get('nik');
                $vg_created_at = date('Y-m-d H:i:s');

                $db = \Config\Database::connect();

                $cekId = $db->table('dtks_verivali_geo')->getWhere(['vg_nik' => $vg_nik])->getResult();

                if (count($cekId) > 0) {
                    session()->setFlashdata('message', '<b style="color:red">Data Gagal di Import, ada ID yang sama</b>');
                } else {

                    $simpandata = [
                        'vg_no_data' => $vg_no_data, 'vg_nik' => $vg_nik, 'vg_nik_ktp' => $vg_nik_ktp, 'vg_nama_lengkap' => $vg_nama_lengkap, 'vg_nama_ktp' => $vg_nama_ktp, 'vg_nkk' => $vg_nkk, 'vg_alamat' => $vg_alamat, 'vg_rt' => $vg_rt, 'vg_rw' => $vg_rw, 'vg_desa' => $vg_desa, 'vg_kec' => $vg_kec, 'vg_kab' => $vg_kab, 'vg_prov' => $vg_prov, 'vg_norek' => $vg_norek, 'vg_dbj_id1' => $vg_dbj_id1, 'vg_dbj_id2' => $vg_dbj_id2, 'vg_source' => $vg_source, 'vg_fp' => $vg_fp, 'vg_fr' => $vg_fr, 'vg_fktp' => $vg_fktp, 'vg_fkk' => $vg_fkk, 'vg_lat' => $vg_lat, 'vg_lang' => $vg_lang, 'vg_created_by' => $vg_created_by, 'vg_created_at' => $vg_created_at
                    ];

                    $db->table('dtks_verivali_geo')->insert($simpandata);
                    session()->setFlashdata('message', '<b>Import file, Berhasil!</b>');
                }
            }
            return redirect()->to('geotagging');
        }
        // return to maintenance
        return redirect()->to('maintenance');
    }


    public function formedit()
    {
        if ($this->request->isAJAX()) {
            $db = \Config\Database::connect();
            $vg_id = $this->request->getVar('vg_id');

            $model = $db->table('dtks_verivali_geo')->where('vg_id', $vg_id)->get()->getRowArray();
            // var_dump($model);
            // $row = $model->find($vg_id);
            foreach ($model as $key => $value) {
                $data[$key] = $value;
            }
            // get name of 
            $bansosSatu = $data['vg_dbj_id1'];
            $bansosSatuNama = $db->table('dtks_bansos_jenis')->where('dbj_id', $bansosSatu)->get()->getRowArray();
            if ($bansosSatuNama != null || $bansosSatuNama != 0 || $bansosSatuNama != '') {
                $jenisBansosSatu = $bansosSatuNama['dbj_nama_bansos'];
            } else {
                $jenisBansosSatu = '';
            }

            $bansosDua = $data['vg_dbj_id2'];
            $bansosDuaNama = $db->table('dtks_bansos_jenis')->where('dbj_id', $bansosDua)->get()->getRowArray();
            if ($bansosDuaNama != null || $bansosDuaNama != 0 || $bansosDuaNama != '') {
                $jenisBansosDua = $bansosDuaNama['dbj_nama_bansos'];
            } else {
                $jenisBansosDua = '';
            }

            $indikasiTemuan = $db->table('tb_ket_temuan')->get()->getResultArray();



            $data = [
                'title' => 'Form. Upload Foto',
                'keterangan' => $db->table('tb_ket_anomali')->orderBy('ano_nama', 'asc')->get()->getResultArray(),
                'status' => $this->GenModel->getStatusLimit()->getResultArray(),
                'jenisPekerjaan' => $this->GenModel->getPendudukPekerjaan()->getResultArray(),
                'jenisKelamin' => $this->GenModel->getDataJenkel(),
                'statusDtks' => $this->GenModel->getStatusDtks()->getResultArray(),
                'Bansos' => $db->table('dtks_bansos_jenis')->get()->getResultArray(),

                'vg_id' => $vg_id,
                'vg_nik' => $model['vg_nik'],
                'vg_nik_ktp' => $model['vg_nik_ktp'],
                'vg_nama_lengkap' => $model['vg_nama_lengkap'],
                'vg_nama_ktp' => $model['vg_nama_ktp'],
                'vg_nkk' => $model['vg_nkk'],
                'vg_alamat' => $model['vg_alamat'],
                'vg_rw' => $model['vg_rw'],
                'vg_rt' => $model['vg_rt'],
                'vg_desa' => $model['vg_desa'],
                'vg_kec' => $model['vg_kec'],
                'vg_kab' => $model['vg_kab'],
                'vg_prov' => $model['vg_prov'],
                'vg_norek' => $model['vg_norek'],
                'vg_source' => $model['vg_source'],
                'vg_fp' => $model['vg_fp'],
                'vg_fr' => $model['vg_fr'],
                'vg_fktp' => $model['vg_fktp'],
                'vg_fkk' => $model['vg_fkk'],
                'vg_lat' => $model['vg_lat'],
                'vg_lang' => $model['vg_lang'],
                'vg_dbj_id1' => $model['vg_dbj_id1'],
                'vg_dbj_id2' => $model['vg_dbj_id2'],
                'vg_sta_id' => $model['vg_sta_id'],
                'vg_ds_id' => $model['vg_ds_id'],
                'vg_terbukti' => $model['vg_terbukti'],
                'vg_alasan' => $model['vg_alasan'],
                'jenisBansosSatu' => $jenisBansosSatu,
                'jenisBansosDua' => $jenisBansosDua,
                'indikasiTemuan' => $indikasiTemuan,

            ];
            // dd($data['status']);

            $msg = [
                'sukses' => view('dtks/data/dtks/file_bpk/modaledit', $data)
            ];

            // var_dump(session()->get('kode_desa'));
            echo json_encode($msg);
        }
    }

    public function ajax_update()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());


            // validasi input
            $user = session()->get('role_id');
            $vg_id = $this->request->getPost('vg_id');
            //cek nik
            // move file base64_decode

            $validation = \Config\Services::validation();
            if ($user <= 2) {
                $data = [
                    'vg_nik' => $this->request->getPost('vg_nik'),
                    'vg_nik_ktp' => $this->request->getPost('vg_nik_ktp'),
                    'vg_nama_lengkap' => $this->request->getPost('vg_nama_lengkap'),
                    'vg_nama_ktp' => $this->request->getPost('vg_nama_ktp'),
                    'vg_alamat' => $this->request->getPost('vg_alamat'),
                    'vg_rt' => $this->request->getPost('vg_rt'),
                    'vg_rw' => $this->request->getPost('vg_rw'),
                    'vg_ds_id' => $this->request->getPost('vg_ds_id'),
                    'vg_lat' => $this->request->getPost('vg_lat'),
                    'vg_lang' => $this->request->getPost('vg_lang'),
                    'vg_sta_id' => $this->request->getPost('vg_status'),
                    'vg_terbukti' => $this->request->getPost('vg_terbukti'),
                    'vg_alasan' => $this->request->getPost('vg_alasan'),
                ];
                // ];
                // var_dump($dataUpdate);
                // die;
                $this->VerivaliGeoModel->update($vg_id, $data);

                $msg = [
                    'sukses' => 'Data Berhasil di update!',
                ];
            } else {
                $valid = $this->validate([

                    'image_fp' => [
                        'label' => 'Foto PM',
                        'rules' => 'uploaded[image_fp]|is_image[image_fp]|mime_in[image_fp,image/jpg,image/jpeg,image/png]',
                        'errors' => [
                            'uploaded' => '{field} harus ada.',
                            'is_image' => '{field} harus berupa gambar.',
                            'mime_in' => '{field} harus berupa gambar.',
                            'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                        ]
                    ],
                    'image_fr' => [
                        'label' => 'Foto Rumah',
                        'rules' => 'uploaded[image_fr]|is_image[image_fr]|mime_in[image_fr,image/jpg,image/jpeg,image/png]',
                        'errors' => [
                            'uploaded' => '{field} harus ada.',
                            'is_image' => '{field} harus berupa gambar.',
                            'mime_in' => '{field} harus berupa gambar.',
                            'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                        ]
                    ],
                    'vg_fkk' => [
                        'label' => 'Foto KK',
                        'rules' => 'uploaded[vg_fkk]|is_image[vg_fkk]|mime_in[vg_fkk,image/jpg,image/jpeg,image/png]',
                        'errors' => [
                            'uploaded' => '{field} harus ada.',
                            'is_image' => '{field} harus berupa gambar.',
                            'mime_in' => '{field} harus berupa gambar.',
                            'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                        ]
                    ],
                    'vg_nik_ktp' => [
                        'label' => 'NIK',
                        'rules' => 'required|numeric|min_length[16]|max_length[16]',
                        'errors' => [
                            'required' => '{field} harus diisi.',
                            'numeric' => '{field} harus berupa angka.',
                            'min_length' => '{field} harus berupa {param} karakter.',
                            'max_length' => '{field} harus berupa {param} karakter.'
                        ]
                    ],
                    'vg_nama_ktp' => [
                        'label' => 'Nama',
                        'rules' => 'required|min_length[3]|max_length[150]',
                        'errors' => [
                            'required' => '{field} harus diisi.',
                            'min_length' => '{field} harus berupa {param} karakter.',
                            'max_length' => '{field} harus berupa {param} karakter.'
                        ]
                    ],
                    'vg_alamat' => [
                        'label' => 'Alamat',
                        'rules' => 'required',
                        'errors' => [
                            'required' => '{field} harus diisi.'
                        ]
                    ],
                    'vg_rt' => [
                        'label' => 'No. RT',
                        'rules' => 'required|numeric',
                        'errors' => [
                            'required' => '{field} harus diisi.',
                            'numeric' => '{field} harus berisi angka.'
                        ]
                    ],
                    'vg_rw' => [
                        'label' => 'No. RW',
                        'rules' => 'required|numeric',
                        'errors' => [
                            'required' => '{field} harus diisi.',
                            'numeric' => '{field} harus berisi angka.'
                        ]
                    ],
                    'vg_lat' => [
                        'label' => 'Latitude',
                        'rules' => 'required',
                        'errors' => [
                            'required' => '{field} harus harus ada.',
                        ]
                    ],
                    'vg_lang' => [
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
                            'vg_fkk' => $this->validator->getError('vg_fkk'),
                            'vg_nik_ktp' => $this->validator->getError('vg_nik_ktp'),
                            'vg_nama_ktp' => $this->validator->getError('vg_nama_ktp'),
                            'vg_alamat' => $validation->getError('vg_alamat'),
                            'vg_rt' => $validation->getError('vg_rt'),
                            'vg_rw' => $validation->getError('vg_rw'),
                            'image_fp' => $validation->getError('image_fp'),
                            'image_fr' => $validation->getError('image_fr'),
                            'vg_lat' => $validation->getError('vg_lat'),
                            'vg_lang' => $validation->getError('vg_lang'),
                            // 'vg_terbukti' => $validation->getError('vg_terbukti'),
                        ]
                    ];
                } else {
                    // unlink file when same id

                    $cekdata = $this->VerivaliGeoModel->getRowId($vg_id);

                    $filename_fp = 'PDTT_FP' . $cekdata['vg_nik'] . '.jpg';
                    $path_fp = 'data/foto_pm/' . $filename_fp;
                    if (file_exists($path_fp)) {
                        unlink($path_fp);
                    }

                    $filename_fr = 'PDTT_FR' . $cekdata['vg_nik'] . '.jpg';
                    $path_fr = 'data/foto_rumah/' . $filename_fr;
                    if (file_exists($path_fr)) {
                        unlink($path_fr);
                    }

                    $filename_ktp = 'PDTT_KTP' . $cekdata['vg_nik'] . '.jpg';
                    $path_ktp = 'data/foto_ktp/' . $filename_ktp;
                    if (file_exists($path_ktp)) {
                        unlink($path_ktp);
                        //else if not exist, do nothing
                    }

                    $filename_kk = 'PDTT_KK' . $cekdata['vg_nik'] . '.jpg';
                    $path_kk = 'data/foto_kk/' . $filename_kk;
                    if (file_exists($path_kk)) {
                        unlink($path_kk);
                    }



                    $image_fp = $this->request->getFile('image_fp');
                    $image_fr = $this->request->getFile('image_fr');
                    $image_ktp = $this->request->getFile('vg_fktp');
                    $image_kk = $this->request->getFile('vg_fkk');

                    // get filename by vg_nik
                    $filename_fp = 'PDTT_FP' . $cekdata['vg_nik'] . '.jpg';
                    $filename_fr = 'PDTT_FR' . $cekdata['vg_nik'] . '.jpg';
                    $filename_ktp = 'PDTT_KTP' . $cekdata['vg_nik'] . '.jpg';
                    $filename_kk = 'PDTT_KK' . $cekdata['vg_nik'] . '.jpg';

                    //image manipulation codeigniter 4 compress and resize image
                    // $image = \Config\Services::image()
                    //     ->withFile($imgPath)
                    //     ->resize(200, 100, true, 'height')
                    //     ->save(FCPATH . '/images/' . $imgPath->getRandomName());

                    // $imgPath->move(WRITEPATH . 'uploads');


                    // $filename_fr = 'PDTT_FR' . $cekdata['vg_nik'] . '.jpg';
                    // move file to folder
                    // $image_fp->move('data/foto_pm/', $filename_fp);
                    $img_fp = imagecreatefromjpeg($image_fp);
                    $img_fr = imagecreatefromjpeg($image_fr);
                    $img_ktp = imagecreatefromjpeg($image_ktp);
                    $img_kk = imagecreatefromjpeg($image_kk);

                    $txtNik = $this->request->getPost('vg_nik_ktp');
                    $txtNama = $this->request->getPost('vg_nama_ktp');
                    $txtAlamat = $this->request->getPost('vg_alamat');
                    $txtKelurahan = $cekdata['namaDesa'];
                    $txtKecamatan = $cekdata['namaKec'];
                    $txtKabupaten = $cekdata['namaKab'];
                    $txtProvinsi = $cekdata['namaProv'];
                    $txtLat = $this->request->getPost('vg_lat');
                    $txtLang = $this->request->getPost('vg_lang');

                    $txt = "NIK : " . $txtNik . "\nNama : " . $txtNama . "\nAlamat : " . $txtAlamat . "\nDesa/Kelurahan : " . $txtKelurahan . "\nKecamatan : " . $txtKecamatan . "\nKabupaten : " . $txtKabupaten . "\nProvinsi : " . $txtProvinsi . "\nLokasi : " . $txtLat . ", " . $txtLang . "\n\n@ " . nameApp() . " Kec. " . ucwords(strtolower(Profil_Admin()['namaKec']));
                    $fontFile = FCPATH . 'assets/fonts/Futura Bk BT Book.ttf';

                    $fontSize = 0.020 * imagesx($img_fp);
                    $black = imagecolorallocate($img_fp, 0, 0, 0);
                    $white = imagecolorallocate($img_fp, 255, 255, 255);
                    $stroke_color = imagecolorallocate($img_fp, 0, 0, 0);
                    $grey = imagecolorallocate($img_fp, 128, 128, 128);

                    $fontSize = 0.020 * imagesx($img_fr);
                    $black = imagecolorallocate($img_fr, 0, 0, 0);
                    $white = imagecolorallocate($img_fr, 255, 255, 255);
                    $stroke_color = imagecolorallocate($img_fr, 0, 0, 0);
                    $grey = imagecolorallocate($img_fr, 128, 128, 128);

                    // pos x from left, pos y from bottom
                    $posX = 0.02 * imagesx($img_fp);
                    $posY = 0.50 * imagesy($img_fp);

                    // $posX = 10;
                    // $posY = 830;
                    $angle = 0;

                    // resize image to smaller size
                    // $img_fp = imagescale($img_fp, imagesx($img_fp) / 2, imagesy($img_fp) / 2);
                    // $img_fr = imagescale($img_fr, imagesx($img_fr) / 2, imagesy($img_fr) / 2);
                    // $img_ktp = imagescale($img_ktp, imagesx($img_ktp) / 2, imagesy($img_ktp) / 2);

                    imagettfstroketext($img_fp, $fontSize, $angle, $posX, $posY, $white, $stroke_color, $fontFile, $txt, 3);
                    imagettfstroketext($img_fr, $fontSize, $angle, $posX, $posY, $white, $stroke_color, $fontFile, $txt, 3);
                    // imagettftext($img_fp, $fontSize, $angle, $posX, $posY, $white, $stroke_color, $fontFile, $txt, 2);


                    header("Content-type: image/jpg");
                    $quality = 90; // 0 to 100
                    imagejpeg($img_fp, 'data/foto_pm/' . $filename_fp, $quality);
                    imagejpeg($img_fr, 'data/foto_rumah/' . $filename_fr, $quality);
                    imagejpeg($img_ktp, 'data/foto_ktp/' . $filename_ktp, $quality);
                    imagejpeg($img_kk, 'data/foto_kk/' . $filename_kk, $quality);

                    // $img_ktp = \Config\Services::image();
                    // $img_ktp->withFile('data/foto_ktp/' . $filename_ktp);
                    // $img_ktp->resize(200, 100, true, 'height');
                    // $img_ktp->move('data/foto_ktp/' . $filename_ktp);

                    // $img_kk = \Config\Services::image();
                    // $img_kk->withFile('data/foto_kk/' . $filename_kk);
                    // $img_kk->resize(200, 100, true, 'height');
                    // $img_kk->move('data/foto_kk/' . $filename_kk);

                    // $img = \Config\Services::image()
                    //     ->withFile($img)
                    //     // fit image
                    //     ->resize(800, 600, true, 'height')
                    //     // ->imagecolorallocate($img, $text_colour)
                    //     // how to add watermark text to image
                    //     // ->withText($img, $txt, $fontFile, $fontSize, $posX, $posY, $angle, $text_colour)
                    //     ->save(FCPATH . 'data/foto_pm/' . $filename_fp);
                    $data = [
                        'vg_nik_ktp' => $this->request->getPost('vg_nik_ktp'),
                        'vg_nama_ktp' => $this->request->getPost('vg_nama_ktp'),
                        'vg_alamat' => $this->request->getPost('vg_alamat'),
                        'vg_rt' => $this->request->getPost('vg_rt'),
                        'vg_rw' => $this->request->getPost('vg_rw'),
                        'vg_ds_id' => $this->request->getPost('vg_ds_id'),
                        'vg_lat' => $this->request->getPost('vg_lat'),
                        'vg_lang' => $this->request->getPost('vg_lang'),
                        'vg_fp' => $filename_fp,
                        'vg_fr' => $filename_fr,
                        'vg_fktp' => $filename_ktp,
                        'vg_fkk' => $filename_kk,
                        'vg_sta_id' => $this->request->getPost('vg_status'),
                        'vg_terbukti' => $this->request->getPost('vg_terbukti'),
                        'vg_alasan' => $this->request->getPost('vg_alasan'),
                        'vg_updated_by' => $user,

                    ];
                    // ];
                    // var_dump($data);
                    // die;
                    $this->VerivaliGeoModel->update($vg_id, $data);

                    $msg = [
                        'sukses' => 'Upload Foto, Berhasil!',
                    ];
                }
            }
            echo json_encode($msg);
        }
    }

    public function setImageValue($search, $replace)
    {
        // Sanity check
        if (!file_exists($replace)) {
            return;
        }

        // Delete current image
        $this->zipClass->deleteName('word/media/' . $search);

        // Add a new one
        $this->zipClass->addFile($replace, 'word/media/' . $search);
    }

    public function exportBA()
    {
        $user_login = $this->AuthModel->getUserId();
        // dd($user_login);
        if (!isset($user_login['lp_sekretariat']) && !isset($user_login['user_lembaga_id'])) {
            $str = '  <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
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
        } else {

            // dd($user_login);
            $this->WilayahModel = new WilayahModel();
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(FCPATH . 'data/templates/ba_pdtt.docx');

            $filter1 = session()->get('kode_desa');
            // not equal to
            $filter4 = '1';
            $filter5 = '1';


            $kode_tanggal = date('d');
            $kode_bulan = date('n');
            $kode_tahun = date('Y');

            $this->WilayahModel = $this->WilayahModel->getVillage($filter1);
            // dd($this->WilayahModel);
            if (is_array($this->WilayahModel)) {
                $this->WilayahModelUpper = strtoupper($this->WilayahModel['name']);
                $this->WilayahModelPropper = ucwords(strtolower($this->WilayahModel['name']));
            } else {
                $this->WilayahModelUpper = strtoupper($this->WilayahModel);
                $this->WilayahModelPropper = ucwords(strtolower($this->WilayahModel));
            }


            // dd($this->WilayahModelUpper);
            $bulan_ini = bulan_ini();

            $hari_ini = hari_ini();

            // dd($filter1);
            $jmlVerval = $this->VerivaliGeoModel->getJmlVerval($filter1, $filter4);
            $jmlVerval = $jmlVerval['jml'];
            // dd($jmlVerval);

            // get $jmlVervalFix()
            $jmlVervalFix = $this->VerivaliGeoModel->getJmlVervalFix($filter1, $filter4, $filter5);
            $jmlVervalFix = $jmlVervalFix['jml'];
            // dd($jmlVervalFix);

            $jmlNonVerval = $jmlVerval - $jmlVervalFix;
            // dd($jmlVervalFix);
            // get $jmlVervalFix()

            $templateProcessor->setValues([
                'desaUpper' => $this->WilayahModelUpper,
                'desaPropper' => $this->WilayahModelPropper,
                'sekretariat' => $user_login['lp_sekretariat'],
                'email' => $user_login['lp_email'],
                'kode_pos' => $user_login['lp_kode_pos'],
                'hari' => $hari_ini,
                'tanggal' => $kode_tanggal,
                'bulan' => $bulan_ini,
                'tahun' => $kode_tahun,
                'nama_petugas' => $user_login['fullname'],
                'nik_petugas' => $user_login['nik'],
                'nama_apdes' => $user_login['lp_kepala'],
                'jmlVerval' => $jmlVerval,
                'jmlVervalFix' => $jmlVervalFix,
                'jmlNonVerval' => $jmlNonVerval,
            ]);

            $vervalPdtt = $this->VerivaliGeoModel->getVerivaliFix($filter1, $filter4, $filter5);
            // dd($vervalPdtt);


            $coba = [];
            foreach ($vervalPdtt as $i => $value) {

                $coba[] = [
                    'vg_no' => $i + 1,
                    'vg_nik' => $value['vg_nik'],
                    'vg_nama_lengkap' => $value['vg_nama_lengkap'],
                    'vg_alamat' => $value['vg_alamat'],
                    'vg_rt' => $value['vg_rt'],
                    'vg_rw' => $value['vg_rw'],
                    'vg_desa' => $value['namaDesa'],
                    'dbj_nama_bansos' => $value['dbj_nama_bansos'],
                    'vg_lat' => $value['vg_lat'],
                    'vg_lang' => $value['vg_lang'],
                    'vg_fp_name' => $value['vg_fp'],
                    'vg_fp' => $value['vg_fp'],
                    'vg_fr_name' => $value['vg_fr'],
                    'vg_fr' => $value['vg_fr'],
                ];
                $i++;
            }
            // dd($coba);

            $templateProcessor->cloneRowAndSetValues('vg_no', $coba);
            foreach ($coba as $i => $item) {
                // path php to folder
                // $path = base_url('data/foto_pm/' . $item['vg_fp']);
                // $templateProcessor->setImageValue(sprintf('gmb_fp#%d', $i + 1), array('path' => $path, 'width' => 10, 'height' => 10, 'ratio' => false));
                // dd($path);
            }

            /* Note: any element you append to a document must reside inside of a Section. */


            $filename = 'BA VERVAL PDTT.docx';

            header("Content-Description: File Transfer");
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');

            // Saving the document as OOXML file...
            $templateProcessor->saveAs('php://output');

            /* Note: we skip RTF, because it's not XML-based and requires a different example. */
            /* Note: we skip PDF, because "HTML-to-PDF" approach is used to create PDF documents. */
        }
    }

    function exportExcel()
    {
        // dd($this->request->getPost());

        $filter1 = $this->request->getPost('datadesa2');
        $filter5 = '1';
        $filter6 = $this->request->getPost('dataIndikasi2');
        // dd($filter6);
        if (empty($filter6)) {
            // send alert message then redirect to page
            $str = '<script>
                    alert("Silahkan pilih \"Jenis Kondisi\" terlebih dahulu!");
                    window.location.href = "' . base_url('geotagging') . '";
                </script>';
            echo $str;
        } else {

            $data = $this->VerivaliGeoModel->dataExport($filter1, $filter5, $filter6);

            $styleArray = [
                'font' => [
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    // setsize 12
                    'size' => 11,
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    // 'wrapText'     => TRUE,
                ],
            ];

            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $lampStyle = [
                // font italic
                'font' => [
                    'italic' => true,
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    // 'wrapText'     => TRUE,
                ],
            ];

            $footerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => array('rgb' => '000000'),
                    // setsize 12
                    'size' => 11,
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ];


            if ($filter6 == 81) {

                $nama_data = 'KPM tidak terdaftar di DTKS tahun 2020 dan usulan Pemda';
                $nama_kondisi = 'KONDISI 1';
                $lampiran = 'Lampiran 7';
                $file_name = 'LAMPIRAN BA. BPK_RI ' . $nama_kondisi . ' - BPNT_BST - PAKENJENG.xlsx';
                $rangeHead1 = 'A1:G1';
                $rangeHead2 = 'A2:G2';
                $rangeHead3 = 'A3:G3';
                $rangeHead4 = 'A4:G4';

                $spreadsheet = new Spreadsheet();

                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'Instrumen Pengujian KPM TP. 3.2.8');
                $sheet->setCellValue('A2', $nama_data);
                $sheet->setCellValue('A3', ucwords(strtolower($nama_kondisi)));
                $sheet->setCellValue('A4', ucwords(strtolower($lampiran)));
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'NIK_BNBA');
                $sheet->setCellValue('B6', '(Lampiran LHP BPK RI)');
                $sheet->setCellValue('C5', 'NIK_KTP');
                $sheet->setCellValue('C6', '(Dicek di Lokasi)');
                $sheet->setCellValue('D5', 'NAMA KPM');
                $sheet->setCellValue('E5', 'KPM Ditemui');
                $sheet->setCellValue('F5', 'KPM Tidak Ditemui');
                $sheet->setCellValue('G5', 'Keterangan *)');

                $sheet->mergeCells($rangeHead1);
                $sheet->getStyle($rangeHead1)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead2);
                $sheet->getStyle($rangeHead2)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead3);
                $sheet->getStyle($rangeHead3)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead4);
                $sheet->getStyle($rangeHead4)->applyFromArray($lampStyle);
                $sheet->mergeCells('A5:A6');
                $sheet->getStyle('A5:A6')->applyFromArray($styleArray);
                $sheet->mergeCells('D5:D6');
                $sheet->getStyle('D5:D6')->applyFromArray($styleArray);
                $sheet->mergeCells('E5:E6');
                $sheet->getStyle('E5:E6')->applyFromArray($styleArray);
                $sheet->mergeCells('F5:F6');
                $sheet->getStyle('F5:F6')->applyFromArray($styleArray);
                $sheet->mergeCells('G5:G6');
                $sheet->getStyle('G5:G6')->applyFromArray($styleArray);
                // ];

                $spreadsheet->getActiveSheet()->getStyle('A5:G6')->applyFromArray($styleArray);



                $count = 7;

                foreach ($data as $row) {

                    $sheet->setCellValue('A' . $count, $count - 6);
                    $sheet->setCellValueExplicit('B' . $count, $row['vg_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('C' . $count, $row['vg_nik_ktp'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValue('D' . $count, strtoupper($row['vg_nama_lengkap']));
                    if ($row['vg_terbukti'] == '1') {
                        $sheet->setCellValue('E' . $count, 'v');
                    } else {
                        $sheet->setCellValue('F' . $count, 'v');
                    }
                    $sheet->setCellValue('G' . $count, ucwords(strtolower($row['vg_alasan'])));

                    $count++;
                }
                $lastRow = $spreadsheet->getActiveSheet(0)->getHighestRow() + 5;
                $spreadsheet->getActiveSheet()->getStyle('A5:G' . $lastRow)->applyFromArray($borderStyle);

                $sheet->mergeCells('A' . ($lastRow + 2) . ':G' . ($lastRow + 2));
                $sheet->mergeCells('A' . ($lastRow + 3) . ':G' . ($lastRow + 3));


                $sheet->setCellValue('A' . ($lastRow + 2), 'Petunjuk Pengisian :')->getStyle('A' . ($lastRow + 2))->applyFromArray($footerStyle);
                $sheet->setCellValue('A' . ($lastRow + 3), '*) Pada kolom keterangan diisi secara singkat terkait hal yang ditemukan pada saat Verifikasi dan Validasi maupun Pengujian')->getStyle('A' . ($lastRow + 3));

                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->setTitle($nama_kondisi);

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
            } elseif ($filter6 == 102 || $filter6 == 103 || $filter6 == 104) {

                $nama_data = 'KPM Sembako/BPNT ganda BST';
                $nama_kondisi = 'KONDISI 3';
                $lampiran = 'Lampiran 9';
                $file_name = 'LAMPIRAN BA. BPK_RI ' . $nama_kondisi . ' - BPNT_BST - PAKENJENG.xlsx';
                $rangeHead1 = 'A1:I1';
                $rangeHead2 = 'A2:I2';
                $rangeHead3 = 'A3:I3';
                $rangeHead4 = 'A4:I4';

                $spreadsheet = new Spreadsheet();

                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'Instrumen Pengujian KPM TP. 3.2.8');
                $sheet->setCellValue('A2', $nama_data);
                $sheet->setCellValue('A3', ucwords(strtolower($nama_kondisi)));
                $sheet->setCellValue('A4', ucwords(strtolower($lampiran)));
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'NIK_BNBA');
                $sheet->setCellValue('B6', '(Lampiran LHP BPK RI)');
                $sheet->setCellValue('C5', 'NIK_KTP');
                $sheet->setCellValue('C6', '(Dicek di Lokasi)');
                $sheet->setCellValue('D5', 'NAMA KPM');
                $sheet->setCellValue('E5', 'KPM Ditemui');
                $sheet->setCellValue('E6', 'BST');
                $sheet->setCellValue('F6', 'PKH/BPNT');
                $sheet->setCellValue('G5', 'KPM Tidak Ditemui');
                $sheet->setCellValue('G6', 'BST');
                $sheet->setCellValue('H6', 'PKH/BPNT');
                $sheet->setCellValue('I5', 'Keterangan *)');

                $sheet->mergeCells($rangeHead1);
                $sheet->getStyle($rangeHead1)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead2);
                $sheet->getStyle($rangeHead2)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead3);
                $sheet->getStyle($rangeHead3)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead4);
                $sheet->getStyle($rangeHead4)->applyFromArray($lampStyle);
                $sheet->mergeCells('A5:A6');
                $sheet->getStyle('A5:A6')->applyFromArray($styleArray);
                $sheet->mergeCells('D5:D6');
                $sheet->getStyle('D5:D6')->applyFromArray($styleArray);
                $sheet->mergeCells('E5:F5');
                $sheet->getStyle('E5:F5')->applyFromArray($styleArray);
                $sheet->mergeCells('G5:H5');
                $sheet->getStyle('G5:H5')->applyFromArray($styleArray);
                $sheet->mergeCells('I5:I6');
                $sheet->getStyle('I5:I6')->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->getStyle('A5:I6')->applyFromArray($styleArray);

                $count = 7;

                foreach ($data as $row) {

                    $sheet->setCellValue('A' . $count, $count - 6);
                    $sheet->setCellValueExplicit('B' . $count, $row['vg_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('C' . $count, $row['vg_nik_ktp'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValue('D' . $count, strtoupper($row['vg_nama_lengkap']));
                    if ($row['vg_terbukti'] == '1' && $row['vg_dbj_id1'] == '3') {
                        $sheet->setCellValue('E' . $count, 'v');
                    } elseif ($row['vg_terbukti'] == '1' && ($row['vg_dbj_id1'] == '1' || $row['vg_dbj_id1'] == '2')) {
                        $sheet->setCellValue('F' . $count, 'v');
                    }
                    if ($row['vg_terbukti'] == '' && $row['vg_dbj_id1'] == '3') {
                        $sheet->setCellValue('G' . $count, 'v');
                    } elseif ($row['vg_terbukti'] == '' && ($row['vg_dbj_id1'] == '1' || $row['vg_dbj_id1'] == '2')) {
                        $sheet->setCellValue('H' . $count, 'v');
                    }
                    $sheet->setCellValue('I' . $count, ucwords(strtolower($row['vg_alasan'])));

                    $count++;
                }
                $lastRow = $spreadsheet->getActiveSheet(0)->getHighestRow() + 5;
                $spreadsheet->getActiveSheet()->getStyle('A5:I' . $lastRow)->applyFromArray($borderStyle);

                $sheet->mergeCells('A' . ($lastRow + 2) . ':I' . ($lastRow + 2));
                $sheet->mergeCells('A' . ($lastRow + 3) . ':I' . ($lastRow + 3));


                $sheet->setCellValue('A' . ($lastRow + 2), 'Petunjuk Pengisian :')->getStyle('A' . ($lastRow + 2))->applyFromArray($footerStyle);
                $sheet->setCellValue('A' . ($lastRow + 3), '*) Pada kolom keterangan diisi secara singkat terkait hal yang ditemukan pada saat Verifikasi dan Validasi maupun Pengujian')->getStyle('A' . ($lastRow + 3));

                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->setTitle($nama_kondisi);

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
            } elseif ($filter6 == 91) {

                $nama_data = 'KPM dengan NIK tidak ditemukan di Dukcapil';
                $nama_kondisi = 'KONDISI 9';
                $lampiran = 'Lampiran 15';
                $file_name = 'LAMPIRAN BA. BPK_RI ' . $nama_kondisi . ' - BPNT_BST - PAKENJENG.xlsx';
                $rangeHead1 = 'A1:G1';
                $rangeHead2 = 'A2:G2';
                $rangeHead3 = 'A3:G3';
                $rangeHead4 = 'A4:G4';

                $spreadsheet = new Spreadsheet();

                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'Instrumen Pengujian KPM TP. 3.2.8');
                $sheet->setCellValue('A2', $nama_data);
                $sheet->setCellValue('A3', ucwords(strtolower($nama_kondisi)));
                $sheet->setCellValue('A4', ucwords(strtolower($lampiran)));
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'NIK_BNBA');
                $sheet->setCellValue('B6', '(Lampiran LHP BPK RI)');
                $sheet->setCellValue('C5', 'NIK_KTP');
                $sheet->setCellValue('C6', '(Dicek di Lokasi)');
                $sheet->setCellValue('D5', 'NAMA KPM');
                $sheet->setCellValue('E5', 'Kondisi');
                $sheet->setCellValue('E6', 'Ditemui');
                $sheet->setCellValue('F6', 'Tidak Ditemui');
                $sheet->setCellValue('G5', 'Keterangan *)');

                $sheet->mergeCells($rangeHead1);
                $sheet->getStyle($rangeHead1)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead2);
                $sheet->getStyle($rangeHead2)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead3);
                $sheet->getStyle($rangeHead3)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead4);
                $sheet->getStyle($rangeHead4)->applyFromArray($lampStyle);
                $sheet->mergeCells('A5:A6');
                $sheet->getStyle('A5:A6')->applyFromArray($styleArray);
                $sheet->mergeCells('D5:D6');
                $sheet->getStyle('D5:D6')->applyFromArray($styleArray);
                $sheet->mergeCells('E5:F5');
                $sheet->getStyle('E5:F5')->applyFromArray($styleArray);
                $sheet->mergeCells('G5:G6');
                $sheet->getStyle('G5:G6')->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->getStyle('A5:G6')->applyFromArray($styleArray);

                $count = 7;

                foreach ($data as $row) {

                    $sheet->setCellValue('A' . $count, $count - 6);
                    $sheet->setCellValueExplicit('B' . $count, $row['vg_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('C' . $count, $row['vg_nik_ktp'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValue('D' . $count, strtoupper($row['vg_nama_lengkap']));
                    if ($row['vg_terbukti'] == '1') {
                        $sheet->setCellValue('E' . $count, 'v');
                    } else {
                        $sheet->setCellValue('F' . $count, 'v');
                    }
                    $sheet->setCellValue('G' . $count, ucwords(strtolower($row['vg_alasan'])));

                    $count++;
                }
                $lastRow = $spreadsheet->getActiveSheet(0)->getHighestRow() + 5;
                $spreadsheet->getActiveSheet()->getStyle('A5:G' . $lastRow)->applyFromArray($borderStyle);

                $sheet->mergeCells('A' . ($lastRow + 2) . ':G' . ($lastRow + 2));
                $sheet->mergeCells('A' . ($lastRow + 3) . ':G' . ($lastRow + 3));


                $sheet->setCellValue('A' . ($lastRow + 2), 'Petunjuk Pengisian :')->getStyle('A' . ($lastRow + 2))->applyFromArray($footerStyle);
                $sheet->setCellValue('A' . ($lastRow + 3), '*) Pada kolom keterangan diisi secara singkat terkait hal yang ditemukan pada saat Verifikasi dan Validasi maupun Pengujian')->getStyle('A' . ($lastRow + 3));

                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->setTitle($nama_kondisi);

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
            } elseif ($filter6 == 92 || $filter6 == 93 || $filter6 == 94) {

                $nama_data = 'KPM dengan NIK tidak aktif sesuai dengan database DUKCAPIL';
                $nama_kondisi = 'KONDISI 10';
                $lampiran = 'Lampiran 16';
                $file_name = 'LAMPIRAN BA. BPK_RI ' . $nama_kondisi . ' - BPNT_BST - PAKENJENG.xlsx';
                $rangeHead1 = 'A1:G1';
                $rangeHead2 = 'A2:G2';
                $rangeHead3 = 'A3:G3';
                $rangeHead4 = 'A4:G4';

                $spreadsheet = new Spreadsheet();

                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'Instrumen Pengujian KPM TP. 3.2.8');
                $sheet->setCellValue('A2', $nama_data);
                $sheet->setCellValue('A3', ucwords(strtolower($nama_kondisi)));
                $sheet->setCellValue('A4', ucwords(strtolower($lampiran)));
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'NIK_BNBA');
                $sheet->setCellValue('B6', '(Lampiran LHP BPK RI)');
                $sheet->setCellValue('C5', 'NIK_KTP');
                $sheet->setCellValue('C6', '(Dicek di Lokasi)');
                $sheet->setCellValue('D5', 'NAMA KPM');
                $sheet->setCellValue('E5', 'Kondisi');
                $sheet->setCellValue('E6', 'AKTIF*)');
                $sheet->setCellValue('F6', 'TIDAK AKTIF**)');
                $sheet->setCellValue('G5', 'Keterangan ***)');

                $sheet->mergeCells($rangeHead1);
                $sheet->getStyle($rangeHead1)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead2);
                $sheet->getStyle($rangeHead2)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead3);
                $sheet->getStyle($rangeHead3)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead4);
                $sheet->getStyle($rangeHead4)->applyFromArray($lampStyle);
                $sheet->mergeCells('A5:A6');
                $sheet->getStyle('A5:A6')->applyFromArray($styleArray);
                $sheet->mergeCells('D5:D6');
                $sheet->getStyle('D5:D6')->applyFromArray($styleArray);
                $sheet->mergeCells('E5:F5');
                $sheet->getStyle('E5:F5')->applyFromArray($styleArray);
                $sheet->mergeCells('G5:G6');
                $sheet->getStyle('G5:G6')->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->getStyle('A5:G6')->applyFromArray($styleArray);

                $count = 7;

                foreach ($data as $row) {

                    $sheet->setCellValue('A' . $count, $count - 6);
                    $sheet->setCellValueExplicit('B' . $count, $row['vg_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('C' . $count, $row['vg_nik_ktp'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValue('D' . $count, strtoupper($row['vg_nama_lengkap']));
                    if ($row['vg_terbukti'] == '1') {
                        $sheet->setCellValue('E' . $count, 'v');
                    } else {
                        $sheet->setCellValue('F' . $count, 'v');
                    }
                    $sheet->setCellValue('G' . $count, ucwords(strtolower($row['vg_alasan'])));

                    $count++;
                }
                $lastRow = $spreadsheet->getActiveSheet(0)->getHighestRow() + 5;
                $spreadsheet->getActiveSheet()->getStyle('A5:G' . $lastRow)->applyFromArray($borderStyle);

                $sheet->mergeCells('A' . ($lastRow + 2) . ':G' . ($lastRow + 2));
                $sheet->mergeCells('A' . ($lastRow + 3) . ':G' . ($lastRow + 3));
                $sheet->mergeCells('A' . ($lastRow + 4) . ':G' . ($lastRow + 4));
                $sheet->mergeCells('A' . ($lastRow + 5) . ':G' . ($lastRow + 5));


                $sheet->setCellValue('A' . ($lastRow + 2), 'Petunjuk Pengisian :')->getStyle('A' . ($lastRow + 2))->applyFromArray($footerStyle);
                $sheet->setCellValue('A' . ($lastRow + 3), '*) Pada kolom aktif, diberikan tanda ceklis (v) apabila NIK KPM padan DUKCAPIL')->getStyle('A' . ($lastRow + 3));
                $sheet->setCellValue('A' . ($lastRow + 4), '**) Pada kolom tidak aktif, diberikan tanda ceklis (v) apabila NIK KPM tidak padan DUKCAPIL')->getStyle('A' . ($lastRow + 4));
                $sheet->setCellValue('A' . ($lastRow + 5), '***) Pada kolom keterangan diisi secara singkat terkait hal yang ditemukan pada saat Verifikasi dan Validasi maupun Pengujian')->getStyle('A' . ($lastRow + 5));


                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }

                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->setTitle($nama_kondisi);

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
            } elseif ($filter6 == 105 || $filter6 == 107 || $filter6 == 114 || $filter6 == 119) {

                $nama_data = 'KPM Merupakan SDM PKH';
                $nama_kondisi = 'KONDISI 13';
                $lampiran = 'Lampiran 13';
                $file_name = 'LAMPIRAN BA. BPK_RI ' . $nama_kondisi . ' - BPNT_BST - PAKENJENG.xlsx';
                $rangeHead1 = 'A1:G1';
                $rangeHead2 = 'A2:G2';
                $rangeHead3 = 'A3:G3';
                $rangeHead4 = 'A4:G4';

                $spreadsheet = new Spreadsheet();

                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'Instrumen Pengujian KPM TP. 3.2.8');
                $sheet->setCellValue('A2', $nama_data);
                $sheet->setCellValue('A3', ucwords(strtolower($nama_kondisi)));
                $sheet->setCellValue('A4', ucwords(strtolower($lampiran)));
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'NIK_BNBA');
                $sheet->setCellValue('B6', '(Lampiran LHP BPK RI)');
                $sheet->setCellValue('C5', 'NIK_KTP');
                $sheet->setCellValue('C6', '(Dicek di Lokasi)');
                $sheet->setCellValue('D5', 'Nama KPM');
                $sheet->setCellValue('E5', 'Kondisi');
                $sheet->setCellValue('E6', 'Terbukti SDM PKH');
                $sheet->setCellValue('F6', 'Tidak Terbukti SDM PKH');
                $sheet->setCellValue('G5', 'Keterangan *)');

                $sheet->mergeCells($rangeHead1);
                $sheet->getStyle($rangeHead1)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead2);
                $sheet->getStyle($rangeHead2)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead3);
                $sheet->getStyle($rangeHead3)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead4);
                $sheet->getStyle($rangeHead4)->applyFromArray($lampStyle);
                $sheet->mergeCells('A5:A6');
                $sheet->getStyle('A5:A6')->applyFromArray($styleArray);
                $sheet->mergeCells('D5:D6');
                $sheet->getStyle('D5:D6')->applyFromArray($styleArray);
                $sheet->mergeCells('E5:F5');
                $sheet->getStyle('E5:F5')->applyFromArray($styleArray);
                $sheet->mergeCells('G5:G6');
                $sheet->getStyle('G5:G6')->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->getStyle('A5:G6')->applyFromArray($styleArray);

                $count = 7;

                foreach ($data as $row) {

                    $sheet->setCellValue('A' . $count, $count - 6);
                    $sheet->setCellValueExplicit('B' . $count, $row['vg_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('C' . $count, $row['vg_nik_ktp'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValue('D' . $count, strtoupper($row['vg_nama_lengkap']));
                    if ($row['vg_terbukti'] == '1') {
                        $sheet->setCellValue('E' . $count, 'v');
                    } else {
                        $sheet->setCellValue('F' . $count, 'v');
                    }
                    $sheet->setCellValue('G' . $count, ucwords(strtolower($row['vg_alasan'])));

                    $count++;
                }
                $lastRow = $spreadsheet->getActiveSheet(0)->getHighestRow() + 5;
                $spreadsheet->getActiveSheet()->getStyle('A5:G' . $lastRow)->applyFromArray($borderStyle);

                $sheet->mergeCells('A' . ($lastRow + 2) . ':G' . ($lastRow + 2));
                $sheet->mergeCells('A' . ($lastRow + 3) . ':G' . ($lastRow + 3));


                $sheet->setCellValue('A' . ($lastRow + 2), 'Petunjuk Pengisian :')->getStyle('A' . ($lastRow + 2))->applyFromArray($footerStyle);
                $sheet->setCellValue('A' . ($lastRow + 3), '*) Pada kolom keterangan diisi secara singkat terkait hal yang ditemukan pada saat Verifikasi dan Validasi maupun Pengujian')->getStyle('A' . ($lastRow + 3));

                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->setTitle($nama_kondisi);

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
            } elseif ($filter6 == 123) {

                $nama_data = 'KPM dibawah umur';
                $nama_kondisi = 'KONDISI 18';
                $lampiran = 'Lampiran 14';
                $file_name = 'LAMPIRAN BA. BPK_RI ' . $nama_kondisi . ' - BPNT_BST - PAKENJENG.xlsx';
                $rangeHead1 = 'A1:G1';
                $rangeHead2 = 'A2:G2';
                $rangeHead3 = 'A3:G3';
                $rangeHead4 = 'A4:G4';

                $spreadsheet = new Spreadsheet();

                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'Instrumen Pengujian KPM TP. 3.2.8');
                $sheet->setCellValue('A2', $nama_data);
                $sheet->setCellValue('A3', ucwords(strtolower($nama_kondisi)));
                $sheet->setCellValue('A4', ucwords(strtolower($lampiran)));
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'NIK_BNBA');
                $sheet->setCellValue('B6', '(Lampiran LHP BPK RI)');
                $sheet->setCellValue('C5', 'NIK_KTP');
                $sheet->setCellValue('C6', '(Dicek di Lokasi)');
                $sheet->setCellValue('D5', 'Nama KPM');
                $sheet->setCellValue('E5', 'Kondisi');
                $sheet->setCellValue('E6', 'Terbukti Dibawah Umur');
                $sheet->setCellValue('F6', 'Tidak Terbukti Dibawah Umur');
                $sheet->setCellValue('G5', 'Keterangan *)');

                $sheet->mergeCells($rangeHead1);
                $sheet->getStyle($rangeHead1)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead2);
                $sheet->getStyle($rangeHead2)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead3);
                $sheet->getStyle($rangeHead3)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead4);
                $sheet->getStyle($rangeHead4)->applyFromArray($lampStyle);
                $sheet->mergeCells('A5:A6');
                $sheet->getStyle('A5:A6')->applyFromArray($styleArray);
                $sheet->mergeCells('D5:D6');
                $sheet->getStyle('D5:D6')->applyFromArray($styleArray);
                $sheet->mergeCells('E5:F5');
                $sheet->getStyle('E5:F5')->applyFromArray($styleArray);
                $sheet->mergeCells('G5:G6');
                $sheet->getStyle('G5:G6')->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->getStyle('A5:G6')->applyFromArray($styleArray);

                $count = 7;

                foreach ($data as $row) {

                    $sheet->setCellValue('A' . $count, $count - 6);
                    $sheet->setCellValueExplicit('B' . $count, $row['vg_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('C' . $count, $row['vg_nik_ktp'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValue('D' . $count, strtoupper($row['vg_nama_lengkap']));
                    if ($row['vg_terbukti'] == '1') {
                        $sheet->setCellValue('E' . $count, 'v');
                    } else {
                        $sheet->setCellValue('F' . $count, 'v');
                    }
                    $sheet->setCellValue('G' . $count, ucwords(strtolower($row['vg_alasan'])));

                    $count++;
                }
                $lastRow = $spreadsheet->getActiveSheet(0)->getHighestRow() + 5;
                $spreadsheet->getActiveSheet()->getStyle('A5:G' . $lastRow)->applyFromArray($borderStyle);

                $sheet->mergeCells('A' . ($lastRow + 2) . ':G' . ($lastRow + 2));
                $sheet->mergeCells('A' . ($lastRow + 3) . ':G' . ($lastRow + 3));


                $sheet->setCellValue('A' . ($lastRow + 2), 'Petunjuk Pengisian :')->getStyle('A' . ($lastRow + 2))->applyFromArray($footerStyle);
                $sheet->setCellValue('A' . ($lastRow + 3), '*) Pada kolom keterangan diisi secara singkat terkait hal yang ditemukan pada saat Verifikasi dan Validasi maupun Pengujian')->getStyle('A' . ($lastRow + 3));

                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->setTitle($nama_kondisi);

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
            } elseif ($filter6 == 115 || $filter6 == 116 || $filter6 == 117) {

                $nama_data = 'KPM PKH Terdaftar di Data AHU';
                $nama_kondisi = 'KONDISI 12';
                $lampiran = 'Lampiran 18';
                $file_name = 'LAMPIRAN BA. BPK_RI ' . $nama_kondisi . ' - BPNT_BST - PAKENJENG.xlsx';
                $rangeHead1 = 'A1:G1';
                $rangeHead2 = 'A2:G2';
                $rangeHead3 = 'A3:G3';
                $rangeHead4 = 'A4:G4';

                $spreadsheet = new Spreadsheet();

                $sheet = $spreadsheet->getActiveSheet();

                $sheet->setCellValue('A1', 'Instrumen Pengujian KPM TP. 3.2.8');
                $sheet->setCellValue('A2', $nama_data);
                $sheet->setCellValue('A3', ucwords(strtolower($nama_kondisi)));
                $sheet->setCellValue('A4', ucwords(strtolower($lampiran)));
                $sheet->setCellValue('A5', 'NO');
                $sheet->setCellValue('B5', 'NIK_BNBA');
                $sheet->setCellValue('B6', '(Lampiran LHP BPK RI)');
                $sheet->setCellValue('C5', 'NIK_KTP');
                $sheet->setCellValue('C6', '(Dicek di Lokasi)');
                $sheet->setCellValue('D5', 'Nama KPM');
                $sheet->setCellValue('E5', 'Kondisi');
                $sheet->setCellValue('E6', 'Terbukti');
                $sheet->setCellValue('F6', 'Tidak Terbukti');
                $sheet->setCellValue('G5', 'Keterangan *)');

                $sheet->mergeCells($rangeHead1);
                $sheet->getStyle($rangeHead1)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead2);
                $sheet->getStyle($rangeHead2)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead3);
                $sheet->getStyle($rangeHead3)->applyFromArray($styleArray);
                $sheet->mergeCells($rangeHead4);
                $sheet->getStyle($rangeHead4)->applyFromArray($lampStyle);
                $sheet->mergeCells('A5:A6');
                $sheet->getStyle('A5:A6')->applyFromArray($styleArray);
                $sheet->mergeCells('D5:D6');
                $sheet->getStyle('D5:D6')->applyFromArray($styleArray);
                $sheet->mergeCells('E5:F5');
                $sheet->getStyle('E5:F5')->applyFromArray($styleArray);
                $sheet->mergeCells('G5:G6');
                $sheet->getStyle('G5:G6')->applyFromArray($styleArray);

                $spreadsheet->getActiveSheet()->getStyle('A5:G6')->applyFromArray($styleArray);

                $count = 7;

                foreach ($data as $row) {

                    $sheet->setCellValue('A' . $count, $count - 6);
                    $sheet->setCellValueExplicit('B' . $count, $row['vg_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('C' . $count, $row['vg_nik_ktp'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->setCellValue('D' . $count, strtoupper($row['vg_nama_lengkap']));
                    if ($row['vg_terbukti'] == '1') {
                        $sheet->setCellValue('E' . $count, 'v');
                    } else {
                        $sheet->setCellValue('F' . $count, 'v');
                    }
                    $sheet->setCellValue('G' . $count, ucwords(strtolower($row['vg_alasan'])));

                    $count++;
                }
                $lastRow = $spreadsheet->getActiveSheet(0)->getHighestRow() + 5;
                $spreadsheet->getActiveSheet()->getStyle('A5:G' . $lastRow)->applyFromArray($borderStyle);

                $sheet->mergeCells('A' . ($lastRow + 2) . ':G' . ($lastRow + 2));
                $sheet->mergeCells('A' . ($lastRow + 3) . ':G' . ($lastRow + 3));

                $sheet->setCellValue('A' . ($lastRow + 2), 'Petunjuk Pengisian :')->getStyle('A' . ($lastRow + 2))->applyFromArray($footerStyle);
                $sheet->setCellValue('A' . ($lastRow + 3), '*) Pada kolom keterangan diisi secara singkat terkait hal yang ditemukan pada saat Verifikasi dan Validasi maupun Pengujian')->getStyle('A' . ($lastRow + 3));

                foreach ($sheet->getColumnIterator() as $column) {
                    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
                }
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->setTitle($nama_kondisi);

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
        }
    }
}
