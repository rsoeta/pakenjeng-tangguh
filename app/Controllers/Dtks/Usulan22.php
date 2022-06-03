<?php

namespace App\Controllers\Dtks;


use App\Controllers\BaseController;
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
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Usulan22 extends BaseController
{
    protected $usulan22Model;
    public function __construct()
    {
        helper(['form']);
        $this->usulan22Model = new Usulan22Model();
        $this->VeriVali09Model = new VeriVali09Model();
        $this->VervalPbiModel = new VervalPbiModel();
        $this->DisabilitasJenisModel = new DisabilitasJenisModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->GenModel = new GenModel();
    }

    public function index()
    {

        if (session()->get('role_id') == 1) {
            $model = new Usulan22Model();
            $desa = new WilayahModel();
            $rw = new RwModel();
            $bansos = new BansosModel();
            $pekerjaan = new PekerjaanModel();
            $statusKawin = new StatusKawinModel();
            $shdk = new ShdkModel();

            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Daftar Usulan DTKS',
                'dtks' => $model->getDtks(),
                'desa' => $desa->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'bansos' => $bansos->findAll(),
                'pekerjaan' => $pekerjaan->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $statusKawin->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $shdk->findAll(),
                'percentages' => $this->VervalPbiModel->jml_persentase(),
                'statusRole' => $this->GenModel->getStatusRole(),
            ];

            return view('dtks/data/dtks/usulan/tables', $data);
        } else if (session()->get('role_id') >= 1) {
            $model = new Usulan22Model();
            $desa = new WilayahModel();
            $datarw = new RwModel();
            $bansos = new BansosModel();
            $pekerjaan = new PekerjaanModel();
            $statusKawin = new StatusKawinModel();
            $shdk = new ShdkModel();

            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Daftar Usulan DTKS',
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

        $model = new Usulan22Model();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('bansos');
        $filter5 = $this->request->getPost('data_tahun');
        $filter6 = $this->request->getPost('data_bulan');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->nama;
            $row[] = $key->nokk;
            $row[] = $key->du_nik;
            if ($key->jenis_kelamin == 1) {
                $row[] = 'LAKI-LAKI';
            } else {
                $row[] = 'PEREMPUAN';
            }
            $row[] = $key->alamat;
            $row[] = $key->jenis_shdk;
            $row[] = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->idUsulan . "'" . ')"><i class="far fa-edit"></i> Edit</a> | 
			<button class="btn btn-sm btn-danger" data-id="' . $key->idUsulan . '" data-nama="' . $key->nama . '" id="deleteBtn"><i class="far fa-trash-alt"></i> Hapus</button>';
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

            $model = new Usulan22Model();
            $desa = new WilayahModel();
            $rw = new RwModel();
            $bansos = new BansosModel();
            $pekerjaan = new PekerjaanModel();
            $statusKawin = new StatusKawinModel();
            $shdk = new ShdkModel();
            $users = new UsersModel();
            $DisabilitasJenisModel = new DisabilitasJenisModel();

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
                'DisabilitasJenisModel' => $DisabilitasJenisModel->findAll(),
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
                    'created_at' => date("Y-m-d H:i:s"),
                    'created_at_year' => date('Y'),
                    'created_at_month' => date('n'),
                    'created_by' => session()->get('nik'),

                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $this->usulan22Model->save($data);

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

            $this->usulan22Model->delete($id);

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
            $DisabilitasJenisModel = new DisabilitasJenisModel();
            $users = new UsersModel();

            $id = $this->request->getVar('id');
            $model = new Usulan22Model();
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
                'DisabilitasJenisModel' => $DisabilitasJenisModel->findAll(),

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
                    'du_nik' => $this->request->getVar('nik'),
                    'disabil_status' => $this->request->getVar('disabil_status'),
                    'disabil_kode' => $this->request->getVar('disabil_jenis'),
                    'hamil_status' => $this->request->getVar('status_hamil'),
                    'hamil_tgl' => $this->request->getVar('tgl_hamil'),
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => session()->get('nik'),

                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $this->usulan22Model->update($id, $data);

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
        // $model = new Usulan22Model();
        // $tmbExpData = $this->request->getVar('btnExpData');
        // $tmbExpAll = $this->request->getVar('btnExpAll');
        $filter1 = $this->request->getVar('desa');
        $filter4 = $this->request->getVar('bansos');
        $filter5 = $this->request->getVar('data_tahun');
        $filter6 = $this->request->getVar('data_bulan');

        // if (isset($tmbExpData)) {
        // if ($filter4 == null || $filter5 == null || $filter6 == null) {

        //     session()->setFlashdata('message', '<strong>Syarat Export</strong>: [-NAMA DESA, -JENIS PROGRAM, -TAHUN dan -BULAN] TIDAK BOLEH KOSONG!!');
        //     return redirect()->to('/dtks/usulan22');
        // } else {
        // dd($filter1, $filter4, $filter5, $filter6);

        $data = $this->usulan22Model->dataExport($filter1, $filter4, $filter5, $filter6)->getResultArray();

        dd($data);

        $file_name = 'USULAN - PAKENJENG - ' . $filter1 . ' - ' . $filter4 . '.xlsx';

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
        // }
        // }

        // if (isset($tmbExpAll)) {
        //     // dd($filter1, $filter4, $filter5, $filter6);

        //     $data = $this->usulan22Model->allExport($filter4, $filter5, $filter6)->getResultArray();

        //     // dd($data);

        //     $file_name = 'USULAN - PAKENJENG - ' . $filter4 . '.xlsx';

        //     $spreadsheet = new Spreadsheet();

        //     $sheet = $spreadsheet->getActiveSheet();

        //     $sheet->setCellValue('A1', 'NIK');
        //     $sheet->setCellValue('B1', 'PROGRAM BANSOS');
        //     $sheet->setCellValue('C1', 'NOKK');
        //     $sheet->setCellValue('D1', 'NAMA');
        //     $sheet->setCellValue('E1', 'TEMPAT LAHIR');
        //     $sheet->setCellValue('F1', "TANGGAL LAHIR\n(31/01/2000)");
        //     $sheet->setCellValue('G1', 'IBU KANDUNG');
        //     $sheet->setCellValue('H1', 'JENIS KELAMIN');
        //     $sheet->setCellValue('I1', 'JENIS PEKERJAAN');
        //     $sheet->setCellValue('J1', 'STATUS KAWIN');
        //     $sheet->setCellValue('K1', 'ALAMAT');
        //     $sheet->setCellValue('L1', 'RT');
        //     $sheet->setCellValue('M1', 'RW');
        //     $sheet->setCellValue('N1', 'PROVINSI');
        //     $sheet->setCellValue('O1', 'KABUPATEN');
        //     $sheet->setCellValue('P1', 'KECAMATAN');
        //     $sheet->setCellValue('Q1', 'KELURAHAN');
        //     $sheet->setCellValue('R1', 'STATUS DISABILITAS');
        //     $sheet->setCellValue('S1', 'KODE JENIS DISABILITAS');
        //     $sheet->setCellValue('T1', 'STATUS HAMIL');
        //     $sheet->setCellValue('U1', "TGL MULAI HAMIL\n(31/12/2021)");
        //     // $sheet->getStyle('A1:U1')->getFont()->setBold(true);
        //     // $sheet->getStyle('A1:U1')->getAlignment()->setWrapText(true);
        //     // $sheet->getStyle('A1:U1')->getAlignment()->setHorizontal('center');

        //     $styleArray = [
        //         'font' => [
        //             'bold' => true,
        //             'color' => array('rgb' => 'FFFFFF'),
        //         ],
        //         'alignment' => [
        //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
        //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        //             'wrapText'     => TRUE,
        //         ],
        //         'borders' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //         ],
        //         'fill' => [
        //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //             // 'rotation' => 90,
        //             'startColor' => [
        //                 'rgb' => '4472C4',
        //             ],
        //             'endColor' => [
        //                 'rgb' => '4472C4',
        //             ],
        //         ],
        //     ];

        //     $spreadsheet->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);

        //     $count = 2;

        //     foreach ($data as $row) {

        //         $tglLahir = date('d/m/Y', strtotime($row['tanggal_lahir']));
        //         if ($row['hamil_status'] == 1) {
        //             $status_hamil = 'YA';
        //         } elseif ($row['hamil_status'] == 2) {
        //             $status_hamil = 'TIDAK';
        //         } else {
        //             $status_hamil = '';
        //         }

        //         if ($row['hamil_tgl'] > 1) {
        //             $hamil_tgl = date('d/m/Y', strtotime($row['hamil_tgl']));
        //         } else {
        //             $hamil_tgl = '';
        //         }

        //         $TglBuat = date('m/Y', strtotime($row['created_at']));

        //         $sheet->setCellValueExplicit('A' . $count, $row['du_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        //         $sheet->setCellValue('B' . $count, $row['dbj_nama_bansos']);
        //         $sheet->setCellValueExplicit('C' . $count, $row['nokk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        //         $sheet->setCellValue('D' . $count, strtoupper($row['nama']));
        //         $sheet->setCellValue('E' . $count, strtoupper($row['tempat_lahir']));
        //         $sheet->setCellValue('F' . $count, $tglLahir);
        //         $sheet->setCellValue('G' . $count, strtoupper($row['ibu_kandung']));
        //         $sheet->setCellValue('H' . $count, $row['NamaJenKel']);
        //         $sheet->setCellValue('I' . $count, $row['JenisPekerjaan']);
        //         $sheet->setCellValue('J' . $count, $row['StatusKawin']);
        //         $sheet->setCellValue('K' . $count, strtoupper($row['alamat']));
        //         $sheet->setCellValue('L' . $count, $row['rt']);
        //         $sheet->setCellValue('M' . $count, $row['rw']);
        //         $sheet->setCellValue('N' . $count, $row['prov']);
        //         $sheet->setCellValue('O' . $count, $row['kab']);
        //         $sheet->setCellValue('P' . $count, $row['kec']);
        //         $sheet->setCellValue('Q' . $count, $row['desa']);
        //         $sheet->setCellValue('R' . $count, $row['dc_status']);
        //         $sheet->setCellValue('S' . $count, $row['dj_kode']);
        //         $sheet->setCellValue('T' . $count, $status_hamil);
        //         $sheet->setCellValue('U' . $count, $hamil_tgl);

        //         $count++;
        //     }
        //     foreach ($sheet->getColumnIterator() as $column) {
        //         $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        //     }
        //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        //     $sheet->setTitle('DATA');

        //     $writer = new Xlsx($spreadsheet);
        //     $writer->save($file_name);
        //     header("Content-Type: application/vnd.ms-excel");
        //     header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        //     header('Expires: 0');
        //     header('Cache-Control: must-revalidate');
        //     header('Pragma: public');
        //     header('Content-Length:' . filesize($file_name));
        //     flush();

        //     readfile($file_name);

        //     exit;
        // }
    }
}
