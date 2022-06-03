<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Vv06Model;
use App\Models\DtksStatusModel;
use App\Models\DtksKetModel;
use App\Models\DesaModel;
use App\Models\RwModel;
use App\Models\RtModel;

class Vv06 extends BaseController
{
    public function __construct()
    {
        helper(['form']);
        $this->vv06Model = new Vv06Model();
        $this->desa = new DesaModel();
        $this->rw = new RwModel();
        $this->rt = new RtModel();
        $this->statusdtks = new DtksStatusModel();
        $this->KetVv = new DtksKetModel();
    }

    public function index()
    {

        if (session()->get('level') == 1) {
            $model = new Vv06Model();
            $dtks = $model->getDtks()->getResultArray();

            $data = [
                'title' => 'Daftar VeriVali DTKS berdasarkan Alamat',
                'dtks' => $dtks,
                // 'status' => $row['status'],
                'datastatus' => $this->statusdtks->joinStatus()->getRowArray(),
                // 'ket_vv' => $row['ket_verivali'],
                'dataketvv' => $this->KetVv->findAll(),
            ];
            // dd($data);
            return view('dtks/vv06/tables', $data);
        } else if (session()->get('level') == 2) {
            $model = new Vv06Model();
            $data = [
                'title' => 'Daftar VeriVali DTKS berdasarkan Alamat',
                'dtks' => $model->getData(),
            ];

            // var_dump($data);
            return view('dtks/vv06/tables', $data);
        }
        $data = [
            'title' => 'Opr NewDTKS | Lockscreen',
        ];
        return view('lockscreen', $data);
    }

    public function table_dtks()
    {
        $desa               = $this->request->getVar('desa');
        $rw                 = $this->request->getVar('rw');
        $keterangan         = $this->request->getVar('keterangan');
        $keyword            = $this->request->getVar('keyword');

        $data['desa'] = $desa;
        $data['rw']   = $rw;
        $data['keterangan']   = $keterangan;
        $data['keyword']      = $keyword;

        $desas         = $this->desa->findAll();
        $rws           = $this->rw->findAll();
        $categories   = $this->KetVv->findAll();
        // $data = [
        //     'desa' => ['' => 'Pilih Desa'] + array_column($desas, 'nama_desa', 'kode_desa'),
        //     'rw' => ['' => 'Pilih RW'] + array_column($rws, 'nama_rw', 'id'),
        //     'keterangan' => ['' => 'Pilih Keterangan'] + array_column($categories, 'jenis_keterangan', 'id_ketvv'),
        // ];
        $data['desas'] = ['' => 'Pilih Desa'] + array_column($desas, 'nama_desa', 'nama_desa');
        $data['rws'] = ['' => 'Pilih RW'] + array_column($rws, 'nama_rw', 'id');
        $data['categories'] = ['' => 'Pilih Keterangan'] + array_column($categories, 'jenis_keterangan', 'id_ketvv');

        // filter
        $where      = [];
        $like       = [];
        $or_like    = [];

        if (!empty($keterangan)) {
            $where = [
                'dtks_vv06.desa' => $desa,
                'dtks_vv06.rw' => $rw,
                'dtks_vv06.ket_verivali' => $keterangan,
            ];
        }

        if (!empty($keyword)) {
            $like   = ['dtks_vv06.nama' => $keyword];
            $or_like   = ['dtks_vv06.nik' => $keyword, 'dtks_vv06.alamat' => $keyword];
        }
        // end filter

        // paginate
        $paginate = 10;
        $data['dtks']   = $this->vv06Model->join('ket_verivali', 'ket_verivali.id_ketvv = dtks_vv06.ket_verivali')
            ->join('tbl_desa', 'tbl_desa.nama_desa = dtks_vv06.desa ')
            ->join('tbl_rw', 'tbl_rw.no_rw = dtks_vv06.rw ')
            ->join('dtks_status', 'dtks_status.id_status = dtks_vv06.status ')
            ->where($where)->like($like)->orLike($or_like)->paginate($paginate, 'paging_data');
        $data['pager']  = $this->vv06Model->pager;

        echo view('dtks/vv06/admintab', $data);
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

    public function redaktirovat($idv)
    {
        $row = $this->vv06Model->find($idv);
        $datart = $this->rt->noRt()->findAll();
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
                'pilihrt' => $row['rt'],
                'datart' => $datart,
                'pilihrw' => $row['rw'],
                'datarw' => $this->rw->findAll(),
                'rw' => $row['rw'],
                'nik_perbaikan' => $row['nik_perbaikan'],
                'pekerjaan' => $row['pekerjaan'],
                'status' => $row['status'],
                'datastatus' => $this->statusdtks->findAll(),
                'ket_vv' => $row['ket_verivali'],
                'dataketvv' => $this->KetVv->findAll(),
                'ket_gambar' => $row['nik'],
            ];
            return view('dtks/vv06/formredaktirovat', $data);
            // var_dump($data);

        } else {
            exit('Data tidak ditemukan');
        }
        $data = [
            'title' => 'Opr NewDTKS | Lockscreen',
        ];
        return view('lockscreen', $data);
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
}
