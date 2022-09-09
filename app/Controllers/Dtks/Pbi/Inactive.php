<?php

namespace App\Controllers\Dtks\Pbi;

use App\Controllers\BaseController;
use App\Models\Dtks\VervalPbiModel;
use App\Models\Dtks\DtksStatusModel;
use App\Models\Dtks\DtksKetModel;
use App\Models\GenModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\Pbi\NonaktifModel;

class Inactive extends BaseController
{
    public function __construct()
    {
        $this->VervalPbiModel = new VervalPbiModel();
        $this->WilayahModel = new WilayahModel();
        $this->GenModel = new GenModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->statusdtks = new DtksStatusModel();
        $this->keterangan = new DtksKetModel();
        $this->AuthModel = new AuthModel();
        $this->NonaktifModel = new NonaktifModel();
    }

    public function pbi_nonaktif()
    {
        $data = [
            'title' => 'Rekapitulasi PBI Non-Aktif 2022',
            'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            // 'operator' => $this->operator->orderBy('NamaLengkap', 'asc')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'datart' => $this->RtModel->noRt(),
            'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
            'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_login' => $this->AuthModel->getUserId(),
        ];

        return view('dtks/data/pbi/nonaktif/index', $data);
    }

    public function tb_pbi_nonaktif()
    {
        $model = new NonaktifModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('datadesa');
        $filter2 = $this->request->getPost('datarw');
        $filter3 = $this->request->getPost('datart');

        $listing = $model->get_datatables($filter1, $filter2, $filter3);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->dpn_id . "'" . ')">' . $key->dpn_nama_kis . '</a>';
            $row[] = $key->dpn_noka_kis;
            $row[] = $key->dpn_nik_kis;
            if ($key->dpn_tgl_lhr_kis == null) {
                $row[] = '-';
            } else if ($key->dpn_tgl_lhr_kis == '0000-00-00') {
                $row[] = '-';
            } else {
                $row[] = $key->dpn_tmp_lhr_kis . ', ' . date_format(date_create($key->dpn_tgl_lhr_kis), 'd-m-Y');
            }
            $row[] = $key->dpn_alamat_kis;
            $row[] = $key->dpn_nik_pm;
            $row[] = $key->dpn_nama_pm;
            $row[] = $key->dpn_nkk_pm;
            if ($key->dpn_tgl_lhr_pm == null) {
                $row[] = '-';
            } else if ($key->dpn_tgl_lhr_pm == '0000-00-00') {
                $row[] = '-';
            } else {
                $row[] = $key->dpn_tmp_lhr_pm . ', ' . date_format(date_create($key->dpn_tgl_lhr_pm), 'd-m-Y');
            }
            $row[] = $key->dpn_alamat_pm;
            if (session()->get('role_id') < 4) {
                $row[] = '<a class="btn btn-sm" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->dpn_id . "'" . ')"><i class="fa fa-edit"></i></a> <button class="btn btn-sm" data-id="' . $key->dpn_id . '" data-nama="' . $key->dpn_nama_kis . '" id="deleteBtn"><i class="far fa-trash-alt"></i></button>';
            } else {
                $row[] = '<a class="btn btn-sm" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->dpn_id . "'" . ')"><i class="fa fa-edit"></i></a>';
            }
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

    public function formTmbNA()
    {
        if ($this->request->isAJAX()) {

            $data = [
                'title' => 'Form. Tambah Data',
                'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
                'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
                'datapisat' => $this->VervalPbiModel->getDataPisat(),
                'jenisKelamin' => $this->GenModel->getDataJenkel(),
                'statusKawin' => $this->GenModel->getDataStatusKawin(),
                'verivali_pbi' => $this->GenModel->getDataVerivaliPbi(),
            ];

            $msg = [
                'data' => view('dtks/data/pbi/nonaktif/modaltambah', $data),
            ];
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    function get_data_pbi()
    {
        $db = \Config\Database::connect();
        $role = session()->get('role_id');
        $kode_desa = session()->get('kode_desa');
        $kode_rw = session()->get('level');

        $request = service('request');
        $postData = $request->getPost();

        $response = array();
        $data = array();
        $builder = $db->table('dtks_pbi_jkn');
        $pbiList = [];
        if (isset($postData['search'])) {
            $search = $postData['search'];
            if ($role === '1') {
                $builder->select('*');
                $builder->like('noka', $search);
                $builder->orLike('nama', $search);
                $builder->distinct('noka');
                $query = $builder->get();
                $data = $query->getResult();
            } elseif ($role === '2') {
                $builder->select('*');
                $builder->like('noka', $search);
                $builder->orLike('nama', $search);
                $builder->distinct('noka');
                $query = $builder->get();
                $data = $query->getResult();
            } elseif ($role === '3') {
                $builder->select('*');
                $builder->where('desa_kode', $kode_desa);
                $builder->like('noka', $search);
                $builder->orLike('nama', $search);
                $builder->distinct('noka');
                $query = $builder->get();
                $data = $query->getResult();
            } elseif ($role === '4') {
                $builder->select('*');
                $builder->where('desa_kode', $kode_desa);
                $builder->where('rw', $kode_rw);
                $builder->like('noka', $search);
                $builder->orLike('nama', $search);
                $builder->distinct('noka');
                $query = $builder->get();
                $data = $query->getResult();
            } else {
                $data = [];
            }
        } else {
            if ($role === '1') {
                $builder->select('*');
                $query = $builder->get();
                $data = $query->getResult();
            } elseif ($role === '2') {
                $builder->select('*');
                $builder->distinct('noka');
                $query = $builder->get();
                $data = $query->getResult();
            } elseif ($role === '3') {
                $builder->select('*');
                $builder->where('desa_kode', $kode_desa);
                $builder->distinct('noka');
                $query = $builder->get();
                $data = $query->getResult();
            } elseif ($role === '4') {
                $builder->select('*');
                $builder->where('desa_kode', $kode_desa);
                $builder->where('rw', $kode_rw);
                $builder->distinct('noka');
                $query = $builder->get();
                $data = $query->getResult();
            } else {
                $data = [];
            }
        }
        foreach ($data as $pdk) {
            $pbiList[] = array(
                'id' => $pdk->id,
                'text' => ' - NAMA: ' . $pdk->nama . ', NOKA: ' . $pdk->noka,
            );
        }
        $response['data'] = $pbiList;

        return $this->response->setJSON($response);
    }

    public function saveInactive()
    {
        if ($this->request->isAJAX()) {

            // var_dump($this->request->getPost());
            // validasi input
            //cek nik
            // $rule_nik = 'required|numeric|is_unique[dtks_pbi_nonaktif.dpn_nik_pm]|min_length[16]|max_length[16]';
            $creator = session()->get('nik');

            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'dpn_noka_kis' => [
                    'label' => 'NOKA',
                    'rules' => 'required|is_unique[dtks_pbi_nonaktif.dpn_noka_kis,dpn_id,{id}]|numeric|min_length[13]|max_length[13]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang'
                    ]
                ],
                'dpn_nik_kis' => [
                    'label' => 'NIK',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dpn_nama_kis' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dpn_tmp_lhr_kis' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dpn_tgl_lhr_kis' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'dpn_alamat_kis' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dpn_rt_kis' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dpn_rw_kis' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dpn_nik_pm' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[dtks_pbi_nonaktif.dpn_nik_pm,dpn_id,{id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dpn_nama_pm' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dpn_nkk_pm' => [
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
                'dpn_alamat_pm' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dpn_rt_pm' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dpn_rw_pm' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dpn_tmp_lhr_pm' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dpn_tgl_lhr_pm' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'dpn_noka_kis' => $validation->getError('dpn_noka_kis'),
                        'dpn_nik_kis' => $validation->getError('dpn_nik_kis'),
                        'dpn_nama_kis' => $validation->getError('dpn_nama_kis'),
                        'dpn_tmp_lhr_kis' => $validation->getError('dpn_tmp_lhr_kis'),
                        'dpn_tgl_lhr_kis' => $validation->getError('dpn_tgl_lhr_kis'),
                        'dpn_alamat_kis' => $validation->getError('dpn_alamat_kis'),
                        'dpn_rt_kis' => $validation->getError('dpn_rt_kis'),
                        'dpn_rw_kis' => $validation->getError('dpn_rw_kis'),
                        'dpn_nik_pm' => $validation->getError('dpn_nik_pm'),
                        'dpn_nama_pm' => $validation->getError('dpn_nama_pm'),
                        'dpn_nkk_pm' => $validation->getError('dpn_nkk_pm'),
                        'dpn_alamat_pm' => $validation->getError('dpn_alamat_pm'),
                        'dpn_rt_pm' => $validation->getError('dpn_rt_pm'),
                        'dpn_rw_pm' => $validation->getError('dpn_rw_pm'),
                        'dpn_tmp_lhr_pm' => $validation->getError('dpn_tmp_lhr_pm'),
                        'dpn_tgl_lhr_pm' => $validation->getError('dpn_tgl_lhr_pm'),
                    ]
                ];
            } else {
                $dataSave = [
                    'dpn_noka_kis' => $this->request->getVar('dpn_noka_kis'),
                    'dpn_nik_kis' => $this->request->getVar('dpn_nik_kis'),
                    'dpn_nama_kis' => strtoupper($this->request->getVar('dpn_nama_kis')),
                    'dpn_tmp_lhr_kis' => $this->request->getVar('dpn_tmp_lhr_kis'),
                    'dpn_tgl_lhr_kis' => $this->request->getVar("dpn_tgl_lhr_kis"),
                    'dpn_alamat_kis' => strtoupper($this->request->getVar("dpn_alamat_kis")),
                    'dpn_rt_kis' => $this->request->getVar('dpn_rt_kis'),
                    'dpn_rw_kis' => $this->request->getVar('dpn_rw_kis'),
                    'dpn_faskes_kis' => $this->request->getVar('dpn_faskes_kis'),
                    'dpn_nik_pm' => $this->request->getVar('dpn_nik_pm'),
                    'dpn_nama_pm' => $this->request->getVar('dpn_nama_pm'),
                    'dpn_nkk_pm' => $this->request->getVar('dpn_nkk_pm'),
                    'dpn_alamat_pm' => strtoupper($this->request->getVar('dpn_alamat_pm')),
                    'dpn_rt_pm' => $this->request->getVar('dpn_rt_pm'),
                    'dpn_rw_pm' => $this->request->getVar('dpn_rw_pm'),
                    'dpn_tmp_lhr_pm' => $this->request->getVar('dpn_tmp_lhr_pm'),
                    'dpn_tgl_lhr_pm' => $this->request->getVar('dpn_tgl_lhr_pm'),
                    'dpn_kode_desa' => $this->request->getVar('dpn_kode_desa'),
                    'dpn_created_by' => $creator,
                    'dpn_updated_by' => $creator,
                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $db = \Config\Database::connect();
                $builder = $db->table('dtks_pbi_nonaktif');
                $builder->insert($dataSave);

                $msg = [
                    'sukses' => 'Data berhasil disimpan',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }

    public function formEditInactive()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $db = \Config\Database::connect();
            $model = $db->table('dtks_pbi_nonaktif');
            // $builder = $model->select('*');
            $row = $model->where('dpn_id', $id)->get()->getRowArray();

            $data = [
                'title' => 'Form. Edit',
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),

                'dpn_id' => $id,
                'dpn_noka_kis' => $row['dpn_noka_kis'],
                'dpn_nik_kis' => $row['dpn_nik_kis'],
                'dpn_nama_kis' => $row['dpn_nama_kis'],
                'dpn_tmp_lhr_kis' => $row['dpn_tmp_lhr_kis'],
                'dpn_tgl_lhr_kis' => $row['dpn_tgl_lhr_kis'],
                'dpn_alamat_kis' => $row['dpn_alamat_kis'],
                'dpn_rt_kis' => $row['dpn_rt_kis'],
                'dpn_rw_kis' => $row['dpn_rw_kis'],
                'dpn_faskes_kis' => $row['dpn_faskes_kis'],
                'dpn_nik_pm' => $row['dpn_nik_pm'],
                'dpn_nama_pm' => $row['dpn_nama_pm'],
                'dpn_nkk_pm' => $row['dpn_nkk_pm'],
                'dpn_alamat_pm' => $row['dpn_alamat_pm'],
                'dpn_rt_pm' => $row['dpn_rt_pm'],
                'dpn_rw_pm' => $row['dpn_rw_pm'],
                'dpn_tmp_lhr_pm' => $row['dpn_tmp_lhr_pm'],
                'dpn_tgl_lhr_pm' => $row['dpn_tgl_lhr_pm'],
                'dpn_kode_desa' => $row['dpn_kode_desa'],
            ];
            $msg = [
                'sukses' => view('dtks/data/pbi/nonaktif/modaledit', $data)
            ];

            // var_dump(session()->get('kode_desa'));
            echo json_encode($msg);
        }
    }

    public function updateInactive()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());
            // die;

            $creator = session()->get('nik');
            // validasi input
            $id = $this->request->getVar('dpn_id');
            //cek nik
            $validation = \Config\Services::validation();
            $valid = $this->validate([
                'dpn_noka_kis' => [
                    'label' => 'NOKA',
                    'rules' => 'required|is_unique[dtks_pbi_nonaktif.dpn_noka_kis,dpn_id,{dpn_id}]|numeric|min_length[13]|max_length[13]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang'
                    ]
                ],
                'dpn_nik_kis' => [
                    'label' => 'NIK',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dpn_nama_kis' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dpn_tmp_lhr_kis' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dpn_tgl_lhr_kis' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'dpn_alamat_kis' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dpn_rt_kis' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dpn_rw_kis' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dpn_nik_pm' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[dtks_pbi_nonaktif.dpn_nik_pm,dpn_id,{dpn_id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dpn_nama_pm' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dpn_nkk_pm' => [
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
                'dpn_alamat_pm' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dpn_rt_pm' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dpn_rw_pm' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dpn_tmp_lhr_pm' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dpn_tgl_lhr_pm' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'dpn_noka_kis' => $validation->getError('dpn_noka_kis'),
                        'dpn_nik_kis' => $validation->getError('dpn_nik_kis'),
                        'dpn_nama_kis' => $validation->getError('dpn_nama_kis'),
                        'dpn_tmp_lhr_kis' => $validation->getError('dpn_tmp_lhr_kis'),
                        'dpn_tgl_lhr_kis' => $validation->getError('dpn_tgl_lhr_kis'),
                        'dpn_alamat_kis' => $validation->getError('dpn_alamat_kis'),
                        'dpn_rt_kis' => $validation->getError('dpn_rt_kis'),
                        'dpn_rw_kis' => $validation->getError('dpn_rw_kis'),
                        'dpn_nik_pm' => $validation->getError('dpn_nik_pm'),
                        'dpn_nama_pm' => $validation->getError('dpn_nama_pm'),
                        'dpn_nkk_pm' => $validation->getError('dpn_nkk_pm'),
                        'dpn_alamat_pm' => $validation->getError('dpn_alamat_pm'),
                        'dpn_rt_pm' => $validation->getError('dpn_rt_pm'),
                        'dpn_rw_pm' => $validation->getError('dpn_rw_pm'),
                        'dpn_tmp_lhr_pm' => $validation->getError('dpn_tmp_lhr_pm'),
                        'dpn_tgl_lhr_pm' => $validation->getError('dpn_tgl_lhr_pm'),
                    ]
                ];
            } else {
                $dataUpdate = [
                    'dpn_noka_kis' => $this->request->getVar('dpn_noka_kis'),
                    'dpn_nik_kis' => $this->request->getVar('dpn_nik_kis'),
                    'dpn_nama_kis' => strtoupper($this->request->getVar('dpn_nama_kis')),
                    'dpn_tmp_lhr_kis' => $this->request->getVar('dpn_tmp_lhr_kis'),
                    'dpn_tgl_lhr_kis' => $this->request->getVar("dpn_tgl_lhr_kis"),
                    'dpn_alamat_kis' => strtoupper($this->request->getVar("dpn_alamat_kis")),
                    'dpn_rt_kis' => $this->request->getVar('dpn_rt_kis'),
                    'dpn_rw_kis' => $this->request->getVar('dpn_rw_kis'),
                    'dpn_faskes_kis' => $this->request->getVar('dpn_faskes_kis'),
                    'dpn_nik_pm' => $this->request->getVar('dpn_nik_pm'),
                    'dpn_nama_pm' => $this->request->getVar('dpn_nama_pm'),
                    'dpn_nkk_pm' => $this->request->getVar('dpn_nkk_pm'),
                    'dpn_alamat_pm' => strtoupper($this->request->getVar('dpn_alamat_pm')),
                    'dpn_rt_pm' => $this->request->getVar('dpn_rt_pm'),
                    'dpn_rw_pm' => $this->request->getVar('dpn_rw_pm'),
                    'dpn_tmp_lhr_pm' => $this->request->getVar('dpn_tmp_lhr_pm'),
                    'dpn_tgl_lhr_pm' => $this->request->getVar('dpn_tgl_lhr_pm'),
                    'dpn_kode_desa' => $this->request->getVar('dpn_kode_desa'),
                    'dpn_created_by' => $creator,
                    'dpn_updated_by' => $creator,
                ];

                // $id = $this->VervalPbiModel->find($this->request->getVar('id'));
                $db = \Config\Database::connect();
                $builder = $db->table('dtks_pbi_nonaktif');
                $builder->where('dpn_id', $id);
                $builder->update($dataUpdate);

                $msg = [
                    'sukses' => 'Data berhasil diupdate',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }

    function hapus()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getVar('id');
            $this->NonaktifModel->delete($id);
            $msg = [
                'sukses' => 'Data berhasil dihapus'
            ];
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }
}
