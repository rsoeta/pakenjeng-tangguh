<?php

namespace App\Controllers\Dtks;


use App\Controllers\BaseController;
use App\Models\Dtks\DtksModel;
use App\Models\Dtks\Usulan21Model;
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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Usulan21 extends BaseController
{
    protected $usulan21Model;
    public function __construct()
    {
        helper(['form']);
        $this->usulan21Model = new Usulan21Model();
        $this->VeriVali09Model = new VeriVali09Model();
        $this->VervalPbiModel = new VervalPbiModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->GenModel = new GenModel();
    }

    public function index()
    {
        if (session()->get('role_id') == 1) {
            $model = new Usulan21Model();
            $desa = new WilayahModel();
            $rw = new RwModel();
            $bansos = new BansosModel();
            $pekerjaan = new PekerjaanModel();
            $statusKawin = new StatusKawinModel();
            $shdk = new ShdkModel();

            $data = [
                'title' => 'Data Usulan DTKS',
                'dtks' => $model->getDtks(),
                'desa' => $desa->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'bansos' => $bansos->findAll(),
                'pekerjaan' => $pekerjaan->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $statusKawin->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $shdk->findAll(),
                'percentages' => $this->VervalPbiModel->jml_persentase(),
                'statusRole' => $this->GenModel->getStatusRole(),
                'updated_at' => $this->usulan21Model->getBulan()->getResultArray(),
            ];
            return view('dtks/data/dtks/usulan/tables', $data);
        } else if (session()->get('role_id') >= 1) {
            $model = new Usulan21Model();
            $desa = new WilayahModel();
            $datarw = new RwModel();
            $bansos = new BansosModel();
            $pekerjaan = new PekerjaanModel();
            $statusKawin = new StatusKawinModel();
            $shdk = new ShdkModel();

            $data = [
                'title' => 'Data Usulan DTKS',
                'dtks' => $model->getDtks(),
                'desa' => $desa->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'bansos' => $bansos->findAll(),
                'pekerjaan' => $pekerjaan->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $statusKawin->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $shdk->findAll(),
                'percentages' => $this->VervalPbiModel->jml_persentase(),
                'statusRole' => $this->GenModel->getStatusRole(),
                'updated_at' => $this->usulan21Model->getBulan()->getResultArray(),
            ];
            return view('dtks/data/dtks/usulan/tables', $data);
        } else {
            $data = [
                'title' => 'Access denied',
            ];
            return view('lockscreen', $data);
        }
    }


    public function tabel_data()
    {

        $model = new Usulan21Model();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('bansos');
        $filter5 = $this->request->getPost('updated_at');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->nama;
            $row[] = $key->nokk;
            $row[] = $key->nik;
            if ($key->jenis_kelamin == 1) {
                $row[] = 'LAKI-LAKI';
            } else {
                $row[] = 'PEREMPUAN';
            }
            $row[] = $key->tempat_lahir;
            $row[] = $key->tanggal_lahir;
            $row[] = $key->ibu_kandung;
            $row[] = $key->alamat;
            $row[] = $key->rt;
            $row[] = $key->rw;
            $row[] = $key->jenis_shdk;
            $row[] = $key->updated_at;
            $row[] = '<a class="btn btn-sm" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->idUsulan . "'" . ')"><i class="fa fa-pencil-alt"></i> detail</a> | 
			<button class="btn btn-sm" data-id="' . $key->idUsulan . '" data-nama="' . $key->nama . '" id="deleteBtn"><i class="fa fa-trash-alt"></i> hapus</button>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $jumlah_semua->jml,
            "recordsFiltered" => $jumlah_filter->jml,
            "data" => $data,
        );
        // var_dump($data);
        $output[$csrfName] = $csrfHash;

        echo json_encode($output);
    }


    public function formtambah()
    {
        if ($this->request->isAJAX()) {

            $model = new Usulan21Model();
            $desa = new WilayahModel();
            $rw = new RwModel();
            $bansos = new BansosModel();
            $pekerjaan = new PekerjaanModel();
            $statusKawin = new StatusKawinModel();
            $shdk = new ShdkModel();
            $users = new UsersModel();

            $data = [
                'title' => 'Data Usulan DTKS',

                'dtks' => $model->getDtks(),
                'desa' => $desa->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'bansos' => $bansos->findAll(),
                'pekerjaan' => $pekerjaan->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $statusKawin->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $shdk->findAll(),
                'users' => $users->findAll(),
            ];

            $msg = [
                'data' => view('dtks/data/dtks/usulan/modaltambah', $data),
            ];
            echo json_encode($msg);
        } else {
            return view('lockscreen');
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
                    'rules' => 'required|numeric|is_unique[dtks_usulan21.nik]|min_length[16]|max_length[16]',
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
                    'rules' => 'required|alpha_numeric_punct',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'tempat_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required|alpha_numeric_punct',
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
                //     //     // 'foto_rumah' => [
                //     //     //     'rules' => 'uploaded[rmh_depan]|max_size[rmh_depan,10000]|is_image[rmh_depan]|mime_in[rmh_depan,image/jpg,image/jpeg,image/png]',
                //     //     //     'errors' => [
                //     //     //         'uploaded' => '{field} harus diisi.',
                //     //     //         'max_size' => 'Ukuran foto terlalu besar',
                //     //     //         'is_image' => 'Yang anda pilih bukan gambar',
                //     //     //         'mime_in' => 'Yang anda pilih bukan gambar'

                //     //     //     ]
                //     //     // ]
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
                        'created_by' => $validation->getError('created_by'),
                    ]
                ];
            } else {
                $data = [
                    'provinsi' => '32',
                    'kabupaten' => '32.05',
                    'kecamatan' => '32.05.33',
                    'created_by' => $this->request->getVar('created_by'),
                    'shdk' => $this->request->getVar('shdk'),
                    'kelurahan' => $this->request->getVar('kelurahan'),
                    'rw' => $this->request->getVar("datarw"),
                    'rt' => $this->request->getVar("datart"),
                    'alamat' => strtoupper($this->request->getVar('alamat')),
                    'status_kawin' => $this->request->getVar("status_kawin"),
                    'jenis_pekerjaan' => $this->request->getVar("jenis_pekerjaan"),
                    'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
                    'ibu_kandung' => $this->request->getVar("ibu_kandung"),
                    'tanggal_lahir' => $this->request->getVar("tanggal_lahir"),
                    'tempat_lahir' => strtoupper($this->request->getVar("tempat_lahir")),
                    'nama' => strtoupper($this->request->getVar('nama')),
                    'nokk' => $this->request->getVar('nokk'),
                    'program_bansos' => $this->request->getVar('databansos'),
                    'nik' => $this->request->getVar('nik'),
                    'created_at' => date('Y-m-d h:m:s'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $this->usulan21Model->save($data);

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

    function delete()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $this->usulan21Model->delete($id);

            $msg = [
                'sukses' => 'Data berhasil dihapus'
            ];
            echo json_encode($msg);
        } else {
            $data = [
                'title' => 'Access denied',
            ];

            return redirect()->to('lockscreen', $data);
        }
    }

    public function formedit()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());

            $pekerjaan = new PekerjaanModel();
            $shdk = new ShdkModel();
            $statusKawin = new StatusKawinModel();
            $desa = new WilayahModel();
            $bansos = new BansosModel();
            $users = new UsersModel();

            $id = $this->request->getVar('id');
            $model = new Usulan21Model();
            $row = $model->find($id);

            $data = [
                'shdk' => $shdk->findAll(),
                'pekerjaan' => $pekerjaan->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $statusKawin->orderBy('StatusKawin', 'asc')->findAll(),
                'desa' => $desa->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'rw' => $this->RwModel->noRw(),
                'rt' => $this->RtModel->noRt(),
                'bansos' => $bansos->findAll(),
                'users' => $users->findAll(),
                'jenkel' => $this->GenModel->getDataJenkel(),

                'created_by' => session()->get('nik'),
                'stahub' => $row['shdk'],
                'kelurahan' => $row['kelurahan'],
                'datarw' => $row["rw"],
                'datart' => $row["rt"],
                'alamat' => $row['alamat'],
                'status_kawin' => $row["status_kawin"],
                'jenis_pekerjaan' => $row["jenis_pekerjaan"],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'ibu_kandung' => $row["ibu_kandung"],
                'tanggal_lahir' => $row["tanggal_lahir"],
                'tempat_lahir' => $row["tempat_lahir"],
                'nama' => $row['nama'],
                'nokk' => $row['nokk'],
                'databansos' => $row['program_bansos'],
                'nik' => $row['nik'],
                'id' => $row['id'],
                // 'foto_rumah' => $nama_foto_rumah,
            ];
            $msg = [
                'sukses' => view('dtks/data/dtks/usulan/modaledit', $data)
            ];
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }

    public function update()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());
            //cek nik
            $id = $this->request->getVar('id');
            $validation = \Config\Services::validation();

            $nikLama = $this->usulan21Model->find($id);
            if ($nikLama['nik'] == $this->request->getVar('nik')) {
                $rule_nik = 'required|numeric|is_unique[dtks_usulan21.nik]|min_length[16]|max_length[16]';
            } else {
                $rule_nik = 'required|numeric|min_length[16]|max_length[16]';
            }
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
                    'rules' => 'required|alpha_numeric_punct',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'tempat_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required|alpha_numeric_punct',
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
                        'created_by' => $validation->getError('created_by'),
                    ]
                ];
            } else {
                $data = [
                    'provinsi' => '32',
                    'kabupaten' => '32.05',
                    'kecamatan' => '32.05.33',
                    'updated_by' => $this->request->getVar('created_by'),
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
                    'tempat_lahir' => $this->request->getVar("tempat_lahir"),
                    'nama' => strtoupper($this->request->getVar('nama')),
                    'nokk' => $this->request->getVar('nokk'),
                    'program_bansos' => $this->request->getVar('databansos'),
                    'nik' => $this->request->getVar('nik'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $this->usulan21Model->update($id, $data);

                $msg = [
                    'sukses' => 'Data berhasil diubah',
                ];
            }
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    function export()
    {
        // $model = new Usulan21Model();
        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('bansos');

        $db      = \Config\Database::connect();
        if (session()->get('role_id') == 1) {
            $builder = $db->table('dtks_usulan21');
            $builder->select('nik, NamaBansos, nokk, nama, tempat_lahir, tanggal_lahir, ibu_kandung, NamaJenKel, JenisPekerjaan, StatusKawin, alamat, rt, rw, tb_provinces.name as prov, tb_regencies.name as kab, tb_districts.name as kec, tb_villages.name as desa');

            $builder->join('tbl_pekerjaan',   'tbl_pekerjaan.idPekerjaan=dtks_usulan21.jenis_pekerjaan');
            $builder->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_usulan21.status_kawin');
            $builder->join('dtks_bansos',     'dtks_bansos.Id=dtks_usulan21.program_bansos');
            $builder->join('tb_shdk',         'tb_shdk.id=dtks_usulan21.shdk');
            $builder->join('tbl_jenkel',      'tbl_jenkel.IdJenKel=dtks_usulan21.jenis_kelamin');
            $builder->join('tb_villages',     'tb_villages.id=dtks_usulan21.kelurahan');
            $builder->join('tb_districts',    'tb_districts.id=dtks_usulan21.kecamatan');
            $builder->join('tb_regencies',    'tb_regencies.id=dtks_usulan21.kabupaten');
            $builder->join('tb_provinces',    'tb_provinces.id=dtks_usulan21.provinsi');
            $query = $builder->get();
            $data = $query->getResultArray();
        } else {
            $builder = $db->table('dtks_usulan21');
            $builder->select('nik, NamaBansos, nokk, nama, tempat_lahir, tanggal_lahir, ibu_kandung, NamaJenKel, JenisPekerjaan, StatusKawin, alamat, rt, rw, tb_provinces.name as prov, tb_regencies.name as kab, tb_districts.name as kec, tb_villages.name as desa');

            $builder->join('tbl_pekerjaan',   'tbl_pekerjaan.idPekerjaan=dtks_usulan21.jenis_pekerjaan');
            $builder->join('tb_status_kawin', 'tb_status_kawin.idStatus=dtks_usulan21.status_kawin');
            $builder->join('dtks_bansos',     'dtks_bansos.Id=dtks_usulan21.program_bansos');
            $builder->join('tb_shdk',         'tb_shdk.id=dtks_usulan21.shdk');
            $builder->join('tbl_jenkel',      'tbl_jenkel.IdJenKel=dtks_usulan21.jenis_kelamin');
            $builder->join('tb_villages',     'tb_villages.id=dtks_usulan21.kelurahan');
            $builder->join('tb_districts',    'tb_districts.id=dtks_usulan21.kecamatan');
            $builder->join('tb_regencies',    'tb_regencies.id=dtks_usulan21.kabupaten');
            $builder->join('tb_provinces',    'tb_provinces.id=dtks_usulan21.provinsi');
            $builder->where('kelurahan', $filter1);
            $query = $builder->get();
            $data = $query->getResultArray();
        }

        // dd($data);

        $file_name = 'USULAN_PAKENJENG.xlsx';

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NIK');
        $sheet->setCellValue('B1', 'PROGRAM BANSOS');
        $sheet->setCellValue('C1', 'NOKK');
        $sheet->setCellValue('D1', 'NAMA');
        $sheet->setCellValue('E1', 'TEMPAT LAHIR');
        $sheet->setCellValue('F1', 'TANGGAL LAHIR (31/01/2000)');
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

        $count = 2;

        foreach ($data as $row) {

            $newFormat = date('d/m/Y', strtotime($row['tanggal_lahir']));

            $sheet->setCellValueExplicit('A' . $count, $row['nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $count, $row['NamaBansos']);
            $sheet->setCellValueExplicit('C' . $count, $row['nokk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $count, strtoupper($row['nama']));
            $sheet->setCellValue('E' . $count, strtoupper($row['tempat_lahir']));
            $sheet->setCellValue('F' . $count, $newFormat);
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

            $count++;
        }

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
}
