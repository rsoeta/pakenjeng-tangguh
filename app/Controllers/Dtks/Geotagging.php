<?php

namespace App\Controllers\Dtks;

use App\Models\Dtks\AuthModel;
use App\Models\WilayahModel;
use App\Models\GenModel;
use App\Models\RwModel;
use App\Models\Dtks\VerivaliGeoModel;


use App\Controllers\BaseController;

class Geotagging extends BaseController
{
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

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Verifikasi dan Validasi Geotagging DTKS',
            'user_login' => $this->AuthModel->getUserId(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'dataStatus2' => $db->table('dtks_status')->get()->getResultArray(),
            'Bansos' => $db->table('dtks_bansos_jenis')->get()->getResultArray(),

        ];

        return view('dtks/data/dtks/file_bpk/index', $data);
    }

    public function tabel_data()
    {
        $db = \Config\Database::connect();

        $model = new VerivaliGeoModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        // $filter1 = '';
        // $filter2 = '';
        // $filter3 = '';
        // $filter4 = '';
        // $filter5 = '';
        $filter1 = $this->request->getPost('datadesa');
        $filter2 = $this->request->getPost('datarw');
        $filter3 = $this->request->getPost('datart');
        $filter4 = $this->request->getPost('dataBansos');
        $filter5 = 0;

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5);

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

            // $badges = $key->vg_dbj_id1;
            // foreach ($db->table('dtks_bansos_jenis')->get()->getResultArray() as $key2) {
            //     if ($key2['dbj_id'] == $badges) {
            //         $keterangan = $key2['dbj_nama_bansos'];
            //     }
            // }
            $row[] = '<span class="badge bg-success">' . $key->dbj_nama_bansos . '</span>';
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

    public function simpanExcel()
    {
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

            $vg_nik = $row[2];
            $vg_nama_lengkap = $row[3];
            $vg_nkk = $row[4];
            $vg_alamat = $row[5];
            $vg_rt = $row[6];
            $vg_rw = $row[7];
            $vg_desa = $row[8];
            $vg_kec = $row[9];
            $vg_kab = $row[10];
            $vg_prov = $row[11];
            $vg_norek = $row[12];
            $vg_dbj_id1 = $row[13];
            $vg_dbj_id2 = $row[14];
            $vg_source = $row[15];
            $vg_created_by = session()->get('nik');
            $vg_created_at = date('Y-m-d H:i:s');

            $db = \Config\Database::connect();

            $cekId = $db->table('dtks_verivali_geo')->getWhere(['vg_nik' => $vg_nik])->getResult();

            if (count($cekId) > 0) {
                session()->setFlashdata('message', '<b style="color:red">Data Gagal di Import, ada ID yang sama</b>');
            } else {

                $simpandata = [
                    'vg_nik' => $vg_nik, 'vg_nama_lengkap' => $vg_nama_lengkap, 'vg_nkk' => $vg_nkk, 'vg_alamat' => $vg_alamat, 'vg_rt' => $vg_rt, 'vg_rw' => $vg_rw, 'vg_desa' => $vg_desa, 'vg_kec' => $vg_kec, 'vg_kab' => $vg_kab, 'vg_prov' => $vg_prov, 'vg_norek' => $vg_norek, 'vg_dbj_id1' => $vg_dbj_id1, 'vg_dbj_id2' => $vg_dbj_id2, 'vg_source' => $vg_source, 'vg_created_by' => $vg_created_by, 'vg_created_at' => $vg_created_at
                ];

                $db->table('dtks_verivali_geo')->insert($simpandata);
                session()->setFlashdata('message', '<b>Import file, Berhasil!</b>');
            }
        }

        return redirect()->to('geotagging');
    }

    public function formedit()
    {
        if ($this->request->isAJAX()) {
            $db = \Config\Database::connect();
            $vg_id = $this->request->getVar('vg_id');

            $model = $db->table('dtks_verivali_geo')->where('vg_id', $vg_id)->get()->getRowArray();
            // $row = $model->find($vg_id);

            $data = [
                'title' => 'Form. Upload Foto',
                'keterangan' => $db->table('tb_ket_anomali')->orderBy('ano_nama', 'asc')->get()->getResultArray(),
                'status' => $db->table('tb_status')->get()->getResultArray(),
                'jenisPekerjaan' => $this->GenModel->getPendudukPekerjaan()->getResultArray(),
                'jenisKelamin' => $this->GenModel->getDataJenkel(),
                'statusDtks' => $this->GenModel->getStatusDtks()->getResultArray(),
                'Bansos' => $db->table('dtks_bansos_jenis')->get()->getResultArray(),

                'vg_id' => $vg_id,
                'vg_nik' => $model['vg_nik'],
                'vg_nama_lengkap' => $model['vg_nama_lengkap'],
                'vg_nkk' => $model['vg_nkk'],
                'vg_alamat' => $model['vg_alamat'],
                'vg_rw' => $model['vg_rw'],
                'vg_rt' => $model['vg_rt'],
                'vg_desa' => $model['vg_desa'],
                'vg_kec' => $model['vg_kec'],
                'vg_kab' => $model['vg_kab'],
                'vg_prov' => $model['vg_prov'],
                'vg_dbj_id1' => $model['vg_dbj_id1'],
                'vg_dbj_id2' => $model['vg_dbj_id2'],
                'vg_sta_id' => $model['vg_sta_id'],
                'vg_ds_id' => $model['vg_ds_id'],

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
            $kode_desa = session()->get('kode_desa');
            $user = session()->get('nik');
            $vg_id = $this->request->getPost('vg_id');
            $vg_nik = $this->request->getPost('vg_nik');
            //cek nik
            // move file base64_decode

            $validation = \Config\Services::validation();
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
                        'vg_alamat' => $validation->getError('vg_alamat'),
                        'vg_rt' => $validation->getError('vg_rt'),
                        'vg_rw' => $validation->getError('vg_rw'),
                        'image_fp' => $validation->getError('image_fp'),
                        'image_fr' => $validation->getError('image_fr'),
                        'vg_lat' => $validation->getError('vg_lat'),
                        'vg_lang' => $validation->getError('vg_lang'),

                    ]
                ];
            } else {
                // unlink file when same id

                $cekdata = $this->VerivaliGeoModel->find($vg_id);

                $filename_fp = $cekdata['vg_nik'] . '.jpg';
                $path_fp = 'data/foto_pm/' . $filename_fp;
                if (file_exists($path_fp)) {
                    unlink($path_fp);
                }

                $filename_fr = $cekdata['vg_nik'] . '.jpg';
                $path_fr = 'data/foto_rumah/' . $filename_fr;
                if (file_exists($path_fr)) {
                    unlink($path_fr);
                }

                $image_fp = $this->request->getFile('image_fp');
                $image_fr = $this->request->getFile('image_fr');

                //image manipulation codeigniter 4 compress and resize image
                // $image = \Config\Services::image()
                //     ->withFile($imgPath)
                //     ->resize(200, 100, true, 'height')
                //     ->save(FCPATH . '/images/' . $imgPath->getRandomName());

                // $imgPath->move(WRITEPATH . 'uploads');

                // get filename by vg_nik
                $filename_fp = $cekdata['vg_nik'] . '.' . $image_fp->getExtension();
                $filename_fr = $cekdata['vg_nik'] . '.' . $image_fr->getExtension();
                // move file to folder
                // $image_fp->move('data/foto_pm/', $filename_fp);
                $image_fp = \Config\Services::image()
                    ->withFile($image_fp)
                    ->text(
                        $cekdata['vg_nama_lengkap'] . ' - ' . $cekdata['vg_lang'] . ', ' . $cekdata['vg_lat'],
                        [
                            'color'         => '#ffffff',
                            'opacity'       => 0,
                            'withShadow'    => false,
                            'shadowColor'   => '#000000',
                            'hAlign'        => 'center',
                            'vAlign'        => 'bottom',
                            'fontSize'      => 20,
                        ]
                    )
                    ->resize(100, 200, true, 'height')
                    // ->fit(100, 200, 'center')
                    ->save(FCPATH . 'data/foto_pm/' . $filename_fp);

                $image_fr = \Config\Services::image()
                    ->withFile($image_fr)
                    ->text(
                        $cekdata['vg_nama_lengkap'] . ' - ' . $cekdata['vg_lang'] . ', ' . $cekdata['vg_lat'],

                        [
                            'color'         => '#ffffff',
                            'shadowColor'   => '#000000',
                            'opacity'       => 0,
                            'withShadow'    => false,
                            'hAlign'        => 'right',
                            'vAlign'        => 'bottom',
                            'fontSize'      => 20,
                        ]
                    )
                    ->resize(100, 200, true, 'height')
                    // ->fit(100, 200, 'center')
                    ->save(FCPATH . 'data/foto_rumah/' . $filename_fr);

                // $image_fr->move('data/foto_rumah/', $filename_fr);
                // $image_fp->move('data/foto_pm');
                // $image_fr->move('data/foto_rumah');
                // update data
                $data = [
                    'vg_alamat' => $this->request->getPost('vg_alamat'),
                    'vg_rt' => $this->request->getPost('vg_rt'),
                    'vg_rw' => $this->request->getPost('vg_rw'),
                    'vg_ds_id' => $this->request->getPost('vg_ds_id'),
                    'vg_lat' => $this->request->getPost('vg_lat'),
                    'vg_lang' => $this->request->getPost('vg_lang'),
                    'vg_fp' => $filename_fp,
                    'vg_fr' => $filename_fr,
                    'vg_sta_id' => $this->request->getPost('vg_status'),
                    // 'vg_updated_by' => $user,

                ];
                // ];
                // var_dump($dataUpdate);
                // die;
                $this->VerivaliGeoModel->update($vg_id, $data);

                $msg = [
                    'sukses' => 'Upload Foto, Berhasil!',
                ];
            }
            echo json_encode($msg);
        }
    }
}
