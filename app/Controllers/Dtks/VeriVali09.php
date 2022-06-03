<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VeriVali09Model;
use App\Models\DtksStatusModel;
use App\Models\DtksKetModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;

class VeriVali09 extends BaseController
{
    public function __construct()
    {
        // helper(['form']);
        $this->VeriVali09Model = new VeriVali09Model();
        $this->DesaModel = new WilayahModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->statusdtks = new DtksStatusModel();
        $this->keterangan = new DtksKetModel();
    }

    public function index()
    {

        $data = [
            'title' => 'VeriVali DTKS 2021',
            'desKels' => $this->DesaModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            // 'operator' => $this->operator->orderBy('NamaLengkap', 'asc')->findAll(),
            'datarw' => $this->VeriVali09Model->getDataRW()->getResultArray(),
            'datart' => $this->VeriVali09Model->getDataRT()->getResultArray(),
            'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
            'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),

        ];
        // dd($data['masuk']);
        return view('data/dtks/verivali09/index', $data);
    }

    public function tabel_data()
    {
        $model = new VeriVali09Model();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa');
        // $operator = $this->request->getPost('operator');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('keterangan');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->nama;
            $row[] = $key->alamat;
            $row[] = $key->nik;
            $row[] = $key->nkk;
            // $row[] = $key->NoKK;
            $row[] = $key->tmp_lahir;
            $row[] = $key->tgl_lahir;
            // $row[] = $key->Desa;
            $row[] = $key->indikasi_masalah;
            // $row[] = $key->TglLahir;

            $badges = $key->ket_verivali;
            if ($badges == 1) {
                $row[] = '<span class="badge bg-danger" selected>Invalid</span>';
            } elseif ($badges == 2) {
                $row[] = '<span class="badge bg-danger" selected>NIK Padan Beda Nama</span>';
            } elseif ($badges == 3) {
                $row[] = '<span class="badge bg-success" selected>Valid</span>';
            } elseif ($badges == 4) {
                $row[] = '<span class="badge bg-warning" selected>Di Hapus - Meninggal</span>';
            } elseif ($badges == 5) {
                $row[] = '<span class="badge bg-warning" selected>Di Hapus - NIK Sudah Terdaftar</span>';
            } elseif ($badges == 6) {
                $row[] = '<span class="badge bg-secondary" selected>Tidak Memiliki E-KTP</span>';
            } elseif ($badges == 7) {
                $row[] = '<span class="badge bg-warning" selected>Di Hapus - Tidak Ditemukan</span>';
            } else {
                $row[] = '<span class="badge bg-warning" selected>Belum Cek</span>';
            }
            // $row[] = $badges;
            // $row[] = $key->NamaPendidikan;
            // $row[] = "<button class='btn btn-lg' onclick='delet('" . $key->ID . "')'>
            //                                             <i class='fa fa-trash-alt'></i>
            //                                         </button>";
            // $row[] = "<button type=\"button\" class=\"btn btn-outline-info btn-sm\" onclick=\"window.location='/verivali09/redaktirovat/" . $key->idv . "'\"><i class=\"fas fa-align-left\"></i></button>";
            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->idv . "'" . ')"><i class="fa fa-pencil-alt"></i> detail</a>';
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

    public function invalid()
    {

        if (session()->get('level') == 1) {
            $model = new Vv06Model();
            $dtks = $model->getDtks()->getResultArray();

            $data = [
                'title' => 'Daftar VeriVali DTKS berdasarkan Invalid',
                'dtks' => $dtks,
                // 'status' => $row['status'],
                'datastatus' => $this->statusdtks->joinStatus()->getRowArray(),
                // 'ket_vv' => $row['ket_verivali'],
                'dataketvv' => $this->KetVv->findAll(),
            ];
            // dd($data);
            return view('dtks/vv06/invalid', $data);
        } else if (session()->get('level') == 2) {
            $model = new Vv06Model();
            $data = [
                'title' => 'Daftar VeriVali DTKS berdasarkan Invalid',
                'dtks' => $model->getDataInvalid(),
            ];
            // var_dump($data);
            return view('dtks/vv06/invalid', $data);
        }
        $data = [
            'title' => 'Opr NewDTKS | Lockscreen',
        ];
        return view('lockscreen', $data);
    }

    public function noaddress()
    {
        if (session()->get('level') == 1) {
            $model = new Vv06Model();
            $data = [
                'title' => 'Daftar VeriVali DTKS berdasarkan Non-Alamat',
                'dtks' => $model->getDtks()
            ];
            return view('dtks/vv06/noaddress', $data);
        } else if (session()->get('level') == 2) {
            $model = new Vv06Model();
            $data = [
                'title' => 'Daftar VeriVali DTKS berdasarkan Non-Alamat',
                'dtks' => $model->getDataNoAddress()
            ];
            return view('dtks/vv06/noaddress', $data);
        }
        $data = [
            'title' => 'Opr NewDTKS | Lockscreen',
        ];
        return view('lockscreen', $data);
    }

    public function process()
    {
        $users = new Vv06Model();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $dataUser = $users->where([
            'email' => $email,
        ])->first();
        if ($dataUser) {
            if (password_verify($password, $dataUser->password)) {
                session()->set([
                    'email' => $dataUser->email,
                    'name' => $dataUser->name,
                    'logged_in' => TRUE
                ]);
                return redirect()->to(base_url('verivali/dtks/home'));
            } else {
                session()->setFlashdata('error', 'Email & Password Salah');
                return redirect()->back();
            }
        } else {
            session()->setFlashdata('error', 'Email & Password Salah');
            return redirect()->back();
        }
    }

    public function formedit()
    {
        if ($this->request->isAJAX()) {
            $idv = $this->request->getVar('idv');

            $model = new VeriVali09Model();
            $row = $model->find($idv);

            $data = [
                'title' => 'Form. Edit',
                'datarw' => $this->VeriVali09Model->getDataRW()->getResultArray(),
                'datart' => $this->VeriVali09Model->getDataRT()->getResultArray(),
                'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
                'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
                'idv' => $idv,
                'nik' => $row['nik'],
                'nkk' => $row['nkk'],
                'nama' => $row['nama'],
                'tmp_lahir' => $row['tmp_lahir'],
                'tgl_lahir' => $row['tgl_lahir'],
                'alamat' => $row['alamat'],
                'rw' => $row['rw'],
                'rt' => $row['rt'],
                'stat' => $row['status'],
                'ket' => $row['ket_verivali'],
            ];
            $msg = [
                'sukses' => view('data/dtks/verivali09/modaledit', $data)
            ];
            echo json_encode($msg);
        }
    }

    public function ajax_update()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());
            // validasi input
            $idv = $this->request->getVar('idv');
            //cek nik
            $nikLama = $this->VeriVali09Model->find($idv);
            if ($nikLama['nik'] == $this->request->getVar('nik')) {
                $rule_nik = 'required|numeric|min_length[16]|max_length[16]';
            } else {
                $rule_nik = 'required|numeric|is_unique[dtks_verivali09.nik]|min_length[16]|max_length[16]';
            }

            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'nik' => [
                    'label' => 'NIK',
                    'rules' => $rule_nik,
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'nkk' => [
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
                    'rules' => 'required|alpha_numeric_punct',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'tmp_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required|alpha_numeric_punct',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'tgl_lahir' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'alamat' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'rt' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'rw' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'status' => [
                    'label' => 'Status',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
            ]);
            if (!$valid) {

                $msg = [
                    'error' => [
                        'idv' => $idv,
                        'nik' => $validation->getError('nik'),
                        'nkk' => $validation->getError('nkk'),
                        'nama' => $validation->getError('nama'),
                        'tmp_lahir' => $validation->getError('tmp_lahir'),
                        'tgl_lahir' => $validation->getError('tgl_lahir'),
                        'ibu_kandung' => $validation->getError('ibu_kandung'),
                        'alamat' => $validation->getError('alamat'),
                        'rt' => $validation->getError('rt'),
                        'rw' => $validation->getError('rw'),
                        'status' => $validation->getError('status'),
                    ]
                ];
            } else {
                $dataUser = [
                    'idv' => $idv,
                    'nik' => $this->request->getVar('nik'),
                    'nkk' => $this->request->getVar('nkk'),
                    'nama' => $this->request->getVar('nama'),
                    'tmp_lahir' => $this->request->getVar("tmp_lahir"),
                    'tgl_lahir' => $this->request->getVar("tgl_lahir"),
                    'alamat' => $this->request->getVar('alamat'),
                    'rt' => $this->request->getVar("rt"),
                    'rw' => $this->request->getVar("rw"),
                    'status' => $this->request->getVar('status'),
                    'ket_verivali' => 0,
                    'created_by' => session()->get('nik'),
                    'cek_update' => 1,
                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $dataAdmin = [
                    'idv' => $idv,
                    'nik' => $this->request->getVar('nik'),
                    'nkk' => $this->request->getVar('nkk'),
                    'nama' => $this->request->getVar('nama'),
                    'tmp_lahir' => $this->request->getVar("tmp_lahir"),
                    'tgl_lahir' => $this->request->getVar("tgl_lahir"),
                    'alamat' => $this->request->getVar('alamat'),
                    'rt' => $this->request->getVar("rt"),
                    'rw' => $this->request->getVar("rw"),
                    'status' => $this->request->getVar('status'),
                    'ket_verivali' => $this->request->getVar('ket'),
                    'created_by' => session()->get('nik'),
                    'cek_update' => 1,
                    // 'foto_rumah' => $nama_foto_rumah,
                ];
                if (session()->get('role_id') <= 2) {
                    // $id = $this->VeriVali09Model->find($this->request->getVar('idv'));
                    // var_dump($id);
                    $this->VeriVali09Model->update($idv, $dataAdmin);
                } else {
                    // $id = $this->VeriVali09Model->find($this->request->getVar('idv'));
                    $this->VeriVali09Model->update($idv, $dataUser);
                }

                $msg = [
                    'sukses' => 'Data berhasil diupdate',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }

    public function redaktirovat($idv)
    {
        $VeriVali09Model = new VeriVali09Model();
        $status = new DtksStatusModel();
        $keterangan = new DtksKetModel();

        $row = $VeriVali09Model->find($idv);
        // var_dump($row);
        // $datart = $this->rt->noRt()->findAll();
        // $ket_gambar = 
        if ($row) {
            $data = [
                'title' => 'Edit Data',
                'idv' => $row['idv'],
                'ids' => $row['ids'],
                'nik' => $row['nik'],
                'nkk' => $row['nkk'],
                'nama' => $row['nama'],
                'tgl_lahir' => $row['tgl_lahir'],
                'tmp_lahir' => $row['tmp_lahir'],
                'alamat' => $row['alamat'],
                'rt' => $row['rt'],
                'pilihrt' => $row['rt'],
                'datarw' => $this->VeriVali09Model->getDataRW()->getResultArray(),
                'datart' => $this->VeriVali09Model->getDataRT()->getResultArray(),
                // 'datart' => $datart,
                'pilihrw' => $row['rw'],
                // 'datarw' => $this->rw->findAll(),
                'rw' => $row['rw'],
                'nik_perbaikan' => $row['nik_perbaikan'],
                'pekerjaan' => $row['pekerjaan'],
                'status' => $row['status'],
                'datastatus' => $status->orderBy('jenis_status', 'asc')->findAll(),
                'keterangan' => $row['ket_verivali'],
                'dataketerangan' => $keterangan->findAll(),
                // 'ket_gambar' => $row['nik'],
            ];

            return view('verivali09/formredaktirovat', $data);
        }
    }

    public function updatedata()
    {
        if ($this->request->isAJAX()) {
            $idv = $this->request->getVar('idv');
            $nik = $this->request->getVar('nik');
            $nkk = $this->request->getVar('nkk');
            $nama = $this->request->getVar('nama');
            $tgl_lahir = $this->request->getVar('tgl_lahir');
            $alamat = $this->request->getVar('alamat');
            $rw = $this->request->getVar('no_rw');
            $rt = $this->request->getVar('id_rt');
            $status = $this->request->getVar('status');
            $ket_vv = $this->request->getVar('ket_vv');

            $validation = \Config\Services::validation();

            $doValid = $this->validate([

                'nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|min_length[16]|max_length[16]|numeric',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                        'min_length' => '{field} yang Anda masukan terlalu pendek',
                        'max_length' => '{field} yang Anda masukan terlalu panjang',
                        'numeric' => '{field} hanya boleh berisi angka',
                    ]
                ],
                'nkk' => [
                    'label' => 'No. KK',
                    'rules' => 'required|min_length[16]|max_length[16]|numeric',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong',
                        'min_length' => '{field} yang Anda masukan terlalu pendek',
                        'max_length' => '{field} yang Anda masukan terlalu panjang',
                        'numeric' => '{field} hanya boleh berisi angka',
                    ]
                ],
                'nama' => [
                    'label' => 'Nama',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong'
                    ]
                ],
                'tgl_lahir' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong'
                    ]
                ],
                'alamat' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong'
                    ]
                ],
                'status' => [
                    'label' => 'Status',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} tidak boleh kosong'
                    ]
                ]
            ]);

            if (!$doValid) {
                $msg = [
                    'error' => [
                        'errorNik' => $validation->getError('nik'),
                        'errorNkk' => $validation->getError('nkk'),
                        'errorNama' => $validation->getError('nama'),
                        'errorTgl_lahir' => $validation->getError('tgl_lahir'),
                        'errorAlamat' => $validation->getError('alamat'),
                        'errorStatus' => $validation->getError('status'),
                        'errorRw' => $validation->getError('no_rw'),
                        'errorRt' => $validation->getError('id_rt')
                    ]
                ];
            } else if (session()->get('level') == 1) {
                $this->vv06Model->update($idv, [
                    'nik' => $nik,
                    'nkk' => $nkk,
                    'nama' => $nama,
                    'tgl_lahir' => $tgl_lahir,
                    'alamat' => $alamat,
                    'status' => $status,
                    'rw' => $rw,
                    'rt' => $rt,
                    'cek_update' => 1,
                    'ket_verivali' => $ket_vv,
                ]);
                // var_dump($no);

                $msg = [
                    'sukses' => 'Data PM berhasil di update!'
                ];
            } else {
                $this->vv06Model->update($idv, [
                    'nik' => $nik,
                    'nkk' => $nkk,
                    'nama' => $nama,
                    'tgl_lahir' => $tgl_lahir,
                    'alamat' => $alamat,
                    'status' => $status,
                    'rw' => $rw,
                    'rt' => $rt,
                    'cek_update' => 1,
                    'ket_verivali' => 1,
                ]);
                // var_dump($no);

                $msg = [
                    'sukses' => 'Data PM berhasil di update!'
                ];
            }
            echo json_encode($msg);
        } else {
            $data = [
                'title' => 'Opr NewDTKS | Lockscreen',
            ];
            return view('lockscreen', $data);
        }
    }

    public function chartDesa()
    {
        $data = [
            'title' => 'Tabel Chart Desa',
        ];
        return view('verivali09/chart-desa', $data);
    }

    public function load_data()
    {
        $model = new DesaModel();

        $data =  $model->orderBy('nama_desa', 'asc')->findAll();

        echo json_encode($data);
    }

    public function update_data()
    {
        $model = new DesaModel();

        $id_desa = $this->request->getPost('id_desa');

        $data = [
            $this->request->getPost('table_column') => $this->request->getPost('value')
        ];

        $model->update($id_desa, $data);
    }
}
