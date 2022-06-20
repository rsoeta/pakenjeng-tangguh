<?php

namespace App\Controllers\Dtks;

use App\Controllers\BaseController;
use App\Models\Dtks\VerivaliAnomaliModel;
use App\Models\Dtks\DtksStatusModel;
use App\Models\Dtks\DtksKetModel;
use App\Models\GenModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;
use App\Models\Dtks\AuthModel;

class VerivaliAnomali extends BaseController
{
    public function __construct()
    {
        $this->VerivaliAnomaliModel = new VerivaliAnomaliModel();
        $this->WilayahModel = new WilayahModel();
        $this->GenModel = new GenModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->statusdtks = new DtksStatusModel();
        $this->keterangan = new DtksKetModel();
        $this->AuthModel = new AuthModel();
    }

    public function index()
    {
        // connect to db
        $db = \Config\Database::connect();

        // get data from table tb_ket_anomali

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Verifikasi dan Validasi Anomali DTKS',
            'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            // 'operator' => $this->operator->orderBy('NamaLengkap', 'asc')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'datart' => $this->RtModel->noRt(),
            'dataStatus' => $db->table('tb_status')->get()->getResultArray(),
            'dataStatus2' => $db->table('dtks_status2')->get()->getResultArray(),
            'verivaliAnomali' => $db->table('tb_ket_anomali')->orderBy('ano_nama', 'desc')->get()->getResultArray(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_login' => $this->AuthModel->getUserId(),

        ];
        // dd($data['masuk']);
        return view('dtks/data/dtks/anomali/index', $data);
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

            $va_id_dtks = $row[0];
            $va_nik = $row[1];
            $va_pekerjaan = $row[13];
            $va_rw = $row[14];
            $va_nama_anomali = $row[15];
            $va_status = '0';
            $va_creator = session()->get('nik');

            $db = \Config\Database::connect();

            $cekId = $db->table('dtks_verivali_anomali')->getWhere(['va_id_dtks' => $va_id_dtks])->getResult();

            if (count($cekId) > 0) {
                session()->setFlashdata('message', '<b style="color:red">Data Gagal di Import, ada ID yang sama</b>');
            } else {

                $simpandata = [
                    'va_id_dtks' => $va_id_dtks, 'va_nik' => $va_nik,
                    // 'va_nama' => $va_nama, 'va_nkk' => $va_nkk, 'va_tmp_lhr' => $va_tmp_lhr, 'va_tgl_lhr' => $va_tgl_lhr, 'va_alamat' => $va_alamat, 'va_prov' => $va_prov, 'va_kab' => $va_kab, 'va_kec' => $va_kec, 'va_desa' => $va_desa, 'va_jk' => $va_jk, 'va_ibu' => $va_ibu, 
                    'va_pekerjaan' => $va_pekerjaan, 'va_rw' => $va_rw, 'va_nama_anomali' => $va_nama_anomali, 'va_status' => $va_status, 'va_creator' => $va_creator
                ];

                $db->table('dtks_verivali_anomali')->insert($simpandata);
                session()->setFlashdata('message', '<b>Import file, Berhasil!</b>');
            }
        }

        return redirect()->to('/verivaliAnomali');
    }

    public function tabel_data()
    {
        $db = \Config\Database::connect();

        $model = new VerivaliAnomaliModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('datadesa');
        $filter2 = $this->request->getPost('datarw');
        $filter3 = $this->request->getPost('datart');
        $filter4 = $this->request->getPost('dataVerivaliAnomali');
        $filter5 = $this->request->getPost('dataStatus');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            // $row[] = '<a href="javascript:void(0)" title="View" onclick="edit_person(' . "'" . $key->va_id . "'" . ')">' . $key->db_nama . '</a>';
            $row[] = $key->db_nik;
            $row[] = $key->db_nama;
            $row[] = $key->db_nkk;
            $row[] = $key->db_tmp_lahir;
            $row[] = $key->db_tgl_lahir;
            $row[] = $key->db_alamat;
            $row[] = $key->db_rt;
            $row[] = $key->db_rw;
            $row[] = $key->db_village;
            $row[] = $key->db_district;
            $row[] = $key->db_regency;
            $row[] = $key->db_province;

            $badges = $key->va_nama_anomali;
            foreach ($db->table('tb_ket_anomali')->get()->getResultArray() as $key2) {
                if ($key2['ano_id'] == $badges) {
                    $keterangan = $key2['ano_nama'];
                }
            }
            if ($badges == 1) {
                $row[] = '<span class="badge bg-success" selected>' . $keterangan . '</span>';
            } elseif ($badges == 2) {
                $row[] = '<span class="badge bg-secondary" selected>' . $keterangan . '</span>';
            } elseif ($badges == 3) {
                $row[] = '<span class="badge bg-warning" selected>' . $keterangan . '</span>';
            } elseif ($badges == 4) {
                $row[] = '<span class="badge bg-warning" selected>' . $keterangan . '</span>';
            } elseif ($badges == 5) {
                $row[] = '<span class="badge bg-secondary" selected>' . $keterangan . '</span>';
            } elseif ($badges == 6) {
                $row[] = '<span class="badge bg-warning" selected>' . $keterangan . '</span>';
            } else {
                $row[] = '<span class="badge bg-warning" selected>' . $keterangan . '</span>';
            }
            $row[] = $key->va_status;

            $row[] = '<a class="btn btn-sm" href="javascript:void(0)" title="View" onclick="edit_person(' . "'" . $key->va_id . "'" . ')"><i class="fa fa-pencil-alt"></i></a>';
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

        $model = new VerivaliAnomaliModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('datadesa2');
        $filter2 = $this->request->getPost('datarw2');
        $filter4 = $this->request->getPost('dataVerivaliAnomali2');
        $filter5 = $this->request->getPost('dataStatus2');
        $filter6 = $this->request->getPost('dataStatusPm');

        $listing = $model->get_datatables2($filter1, $filter2, $filter4, $filter5, $filter6);
        $jumlah_semua = $model->jumlah_semua2();
        $jumlah_filter = $model->jumlah_filter2($filter1, $filter2, $filter4, $filter5, $filter6);

        // dd($listing);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            // $row[] = '<a href="javascript:void(0)" title="View" onclick="edit_person(' . "'" . $key->va_id . "'" . ')">' . $key->db_nama . '</a>';
            $row[] = $key->va_nik;
            $row[] = $key->va_nama;
            $row[] = $key->va_nkk;
            $row[] = $key->va_tgl_lhr;
            $row[] = $key->va_tmp_lhr;
            $row[] = $key->va_alamat;
            $row[] = $key->va_rw;
            $row[] = $key->NamaJenKel;
            $row[] = $key->pk_nama;
            $row[] = $key->va_ibu;
            $row[] = $key->va_desa;
            $row[] = $key->va_kec;
            $row[] = $key->va_kab;
            $row[] = $key->va_prov;
            $row[] = strtoupper($key->jenis_status);

            $badges = $key->va_nama_anomali;
            foreach ($db->table('tb_ket_anomali')->get()->getResultArray() as $key2) {
                if ($key2['ano_id'] == $badges) {
                    $keterangan = $key2['ano_nama'];
                }
            }
            if ($badges == 1) {
                $row[] = '<span class="badge bg-success" selected>' . $keterangan . '</span>';
            } elseif ($badges == 2) {
                $row[] = '<span class="badge bg-secondary" selected>' . $keterangan . '</span>';
            } elseif ($badges == 3) {
                $row[] = '<span class="badge bg-warning" selected>' . $keterangan . '</span>';
            } elseif ($badges == 4) {
                $row[] = '<span class="badge bg-warning" selected>' . $keterangan . '</span>';
            } elseif ($badges == 5) {
                $row[] = '<span class="badge bg-secondary" selected>' . $keterangan . '</span>';
            } elseif ($badges == 6) {
                $row[] = '<span class="badge bg-warning" selected>' . $keterangan . '</span>';
            } else {
                $row[] = '<span class="badge bg-warning" selected>' . $keterangan . '</span>';
            }

            $row[] = '<a class="btn btn-sm" href="javascript:void(0)" title="View" onclick="edit_person2(' . "'" . $key->va_id . "'" . ')"><i class="fa fa-pencil-alt"></i></a>';
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

    public function formedit()
    {

        if ($this->request->isAJAX()) {
            $db = \Config\Database::connect();
            $va_id = $this->request->getVar('va_id');

            $model = $db->table('vw_verivali_anomali')->where('va_id', $va_id)->get()->getRowArray();
            // $row = $model->find($va_id);

            $data = [
                'title' => 'Form. Edit',
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'keterangan' => $db->table('tb_ket_anomali')->orderBy('ano_nama', 'asc')->get()->getResultArray(),
                'status' => $db->table('tb_status')->get()->getResultArray(),
                'jenisPekerjaan' => $this->GenModel->getPendudukPekerjaan()->getResultArray(),
                'jenisKelamin' => $this->GenModel->getDataJenkel(),
                'statusDtks' => $this->GenModel->getStatusDtks()->getResultArray(),


                'va_id' => $va_id,
                'db_nik' => $model['db_nik'],
                'db_nama' => $model['db_nama'],
                'db_nkk' => $model['db_nkk'],
                'db_tgl_lahir' => $model['db_tgl_lahir'],
                'db_tmp_lahir' => $model['db_tmp_lahir'],
                'db_alamat' => $model['db_alamat'],
                'db_rt' => $model['db_rt'],
                'db_rw' => $model['db_rw'],
                'db_jenkel_id' => $model['db_jenkel_id'],
                'va_pekerjaan' => $model['va_pekerjaan'],
                'db_ibu_kandung' => $model['db_ibu_kandung'],
                'db_district' => $model['db_district'],
                'db_regency' => $model['db_regency'],
                'db_province' => $model['db_province'],
                'va_nama_anomali' => $model['va_nama_anomali'],
                'va_ds_id' => $model['va_ds_id'],
                'va_status' => $model['va_status'],
            ];

            // dd($data['statusDtks']);
            $msg = [
                'sukses' => view('dtks/data/dtks/anomali/modaledit', $data)
            ];

            // var_dump(session()->get('kode_desa'));
            echo json_encode($msg);
        }
    }

    public function formedit2()
    {

        if ($this->request->isAJAX()) {
            $db = \Config\Database::connect();
            $va_id = $this->request->getVar('va_id');

            $model = $db->table('dtks_verivali_anomali')->where('va_id', $va_id)->get()->getRowArray();
            // $row = $model->find($va_id);

            $data = [
                'title' => 'Form. Edit',
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'keterangan' => $db->table('tb_ket_anomali')->orderBy('ano_nama', 'asc')->get()->getResultArray(),
                'status' => $db->table('tb_status')->get()->getResultArray(),
                'jenisPekerjaan' => $this->GenModel->getPendudukPekerjaan()->getResultArray(),
                'jenisKelamin' => $this->GenModel->getDataJenkel(),
                'statusDtks' => $this->GenModel->getStatusDtks()->getResultArray(),

                'va_id' => $va_id,
                'va_nik' => $model['va_nik'],
                'va_nama' => $model['va_nama'],
                'va_nkk' => $model['va_nkk'],
                'va_tgl_lhr' => $model['va_tgl_lhr'],
                'va_tmp_lhr' => $model['va_tmp_lhr'],
                'va_alamat' => $model['va_alamat'],
                'va_rw' => $model['va_rw'],
                'va_jk' => $model['va_jk'],
                'va_pekerjaan' => $model['va_pekerjaan'],
                'va_ibu' => $model['va_ibu'],
                'va_desa' => $model['va_desa'],
                'va_kec' => $model['va_kec'],
                'va_kab' => $model['va_kab'],
                'va_prov' => $model['va_prov'],
                'va_nama_anomali' => $model['va_nama_anomali'],
                'va_status' => $model['va_status'],
                'va_ds_id' => $model['va_ds_id'],

            ];

            $msg = [
                'sukses' => view('dtks/data/dtks/anomali/modaledit2', $data)
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
            $va_id = $this->request->getVar('va_id');
            //cek nik
            $nikLama = $this->VerivaliAnomaliModel->find($va_id);
            if ($nikLama['va_nik'] == $this->request->getVar('db_nik')) {
                $rule_nik = 'required|numeric|min_length[16]|max_length[16]';
            } else {
                $rule_nik = 'required|numeric|is_unique[vw_verivali_anomali.va_nik]|min_length[16]|max_length[16]';
            }

            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'db_nama' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'db_jenkel_id' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'db_tgl_lahir' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'db_tmp_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'db_nik' => [
                    'label' => 'NIK',
                    'rules' => $rule_nik,
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'va_pekerjaan' => [
                    'label' => 'Jenis Pekerjaan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'db_nkk' => [
                    'label' => 'No. KK',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang'
                    ]
                ],
                'db_alamat' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'db_rt' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'db_rw' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'db_ibu_kandung' => [
                    'label' => 'Nama Ibu',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'va_ds_id' => [
                    'label' => 'Status PM',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
            ]);
            if (!$valid) {

                $msg = [
                    'error' => [
                        'va_id' => $va_id,
                        'db_nik' => $validation->getError('db_nik'),
                        'db_nama' => $validation->getError('db_nama'),
                        'db_jenkel_id' => $validation->getError('db_jenkel_id'),
                        'db_tgl_lahir' => $validation->getError('db_tgl_lahir'),
                        'db_tmp_lahir' => $validation->getError('db_tmp_lahir'),
                        'va_pekerjaan' => $validation->getError('va_pekerjaan'),
                        'kelas_rawat' => $validation->getError('kelas_rawat'),
                        'db_nkk' => $validation->getError('db_nkk'),
                        'db_alamat' => $validation->getError('db_alamat'),
                        'db_rt' => $validation->getError('db_rt'),
                        'db_rw' => $validation->getError('db_rw'),
                        'db_ibu_kandung' => $validation->getError('db_ibu_kandung'),
                        'va_ds_id' => $validation->getError('va_ds_id'),
                    ]
                ];
            } else {
                $dataUpdate = [
                    'va_id' => $va_id,
                    'va_nik' => $this->request->getVar('db_nik'),
                    'va_nama' => strtoupper($this->request->getVar('db_nama')),
                    'va_jk' => $this->request->getVar('db_jenkel_id'),
                    'va_tgl_lhr' => $this->request->getVar("db_tgl_lahir"),
                    'va_tmp_lhr' => strtoupper($this->request->getVar("db_tmp_lahir")),
                    'va_pekerjaan' => $this->request->getVar('va_pekerjaan'),
                    'va_nkk' => $this->request->getVar('db_nkk'),
                    'va_alamat' => strtoupper($this->request->getVar('db_alamat')),
                    'va_rw' => $this->request->getVar("db_rw"),
                    'va_ibu' => strtoupper($this->request->getVar('db_ibu_kandung')),
                    'va_ds_id' => $this->request->getVar('va_ds_id'),
                    'va_prov' => $this->request->getVar('db_province'),
                    'va_kab' => $this->request->getVar('db_regency'),
                    'va_kec' => $this->request->getVar('db_district'),
                    'va_desa' => $this->request->getVar('db_village'),
                    'va_status' => $this->request->getVar('va_status'),
                    'va_creator' => session()->get('nik'),
                ];
                // dd($dataUpdate);

                // $id = $this->VerivaliAnomaliModel->find($this->request->getVar('id'));
                $this->VerivaliAnomaliModel->update($va_id, $dataUpdate);

                $msg = [
                    'sukses' => 'Data berhasil diupdate',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }

    public function ajax_update2()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());
            // validasi input
            $va_id = $this->request->getVar('va_id');
            //cek nik
            $nikLama = $this->VerivaliAnomaliModel->find($va_id);
            if ($nikLama['va_nik'] == $this->request->getVar('va_nik')) {
                $rule_nik = 'required|numeric|min_length[16]|max_length[16]';
            } else {
                $rule_nik = 'required|numeric|is_unique[vw_verivali_anomali.va_nik]|min_length[16]|max_length[16]';
            }

            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'va_nik' => [
                    'label' => 'NIK',
                    'rules' => $rule_nik,
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
            ]);
            if (!$valid) {

                $msg = [
                    'error' => [
                        'va_id' => $va_id,
                        'va_nik' => $validation->getError('va_nik'),
                    ]
                ];
            } else {
                $dataUpdate = [
                    'va_id' => $va_id,
                    'va_nik' => $this->request->getVar('va_nik'),
                    'va_nama' => strtoupper($this->request->getVar('va_nama')),
                    'va_jk' => $this->request->getVar('va_jk'),
                    'va_tgl_lhr' => $this->request->getVar("va_tgl_lhr"),
                    'va_tmp_lhr' => strtoupper($this->request->getVar("va_tmp_lhr")),
                    'va_pekerjaan' => $this->request->getVar('va_pekerjaan'),
                    'va_nkk' => $this->request->getVar('va_nkk'),
                    'va_alamat' => strtoupper($this->request->getVar('va_alamat')),
                    'va_rw' => $this->request->getVar("va_rw"),
                    'va_ibu' => strtoupper($this->request->getVar('va_ibu')),
                    'va_prov' => $this->request->getVar('va_prov'),
                    'va_kab' => $this->request->getVar('va_kab'),
                    'va_kec' => $this->request->getVar('va_kec'),
                    'va_desa' => $this->request->getVar('va_desa'),
                    'va_status' => $this->request->getVar('va_status'),
                    'va_ds_id' => $this->request->getVar('va_ds_id'),
                    'va_creator' => session()->get('nik'),
                ];

                // $id = $this->VerivaliAnomaliModel->find($this->request->getVar('id'));
                $this->VerivaliAnomaliModel->update($va_id, $dataUpdate);

                $msg = [
                    'sukses' => 'Data berhasil dikembalikan',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }
}
