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
use CodeIgniter\HTTP\Response;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\Word2007;
use CodeIgniter\I18n\Time;

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
            $data = [
                'title' => 'Access denied',
            ];
            return view('lockscreen', $data);
        }
    }


    public function tabel_data()
    {

        $this->Usulan22Model = new Usulan22Model();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('bansos');
        $filter5 = $this->request->getPost('data_tahun');
        $filter6 = $this->request->getPost('data_bulan');

        $listing = $this->Usulan22Model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);
        $jumlah_semua = $this->Usulan22Model->jumlah_semua();
        $jumlah_filter = $this->Usulan22Model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);

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
            $row[] = '<a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->idUsulan . "'" . ')"><i class="far fa-edit"></i> Edit</a> | 
			<button class="btn btn-sm btn-secondary" data-id="' . $key->idUsulan . '" data-nama="' . $key->nama . '" id="deleteBtn"><i class="far fa-trash-alt"></i> Hapus</button>';
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

            $data = [
                'title' => 'Data Usulan DTKS',

                'dtks' => $model->getDtks(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $this->StatusKawinModel->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $this->ShdkModel->findAll(),
                'users' => $users->findAll(),
                'DisabilitasJenisModel' => $DisabilitasJenisModel->findAll(),
            ];

            $times = new Time();
            $hari = $times->getDay();       // 12
            $jam = $times->hour;           // 16
            $menit = $times->minute;         // 15
            $hari_ini = $hari . $jam . $menit;
            $deadline = '141414';
            if ($hari_ini > $deadline) {
                $msg = [
                    'data' => '<script>
                        alert(\'Mohon Maaf, Batas waktu untuk Tambah Data Telah Habis!!\');
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

    function delete()
    {
        if ($this->request->isAJAX()) {

            $times = new Time();
            $hari = $times->getDay();       // 12
            $jam = $times->hour;           // 16
            $menit = $times->minute;         // 15
            $hari_ini = $hari . $jam . $menit;
            $deadline = '141414';
            if ($hari_ini > $deadline) {
                $msg = [
                    'informasi' => 'Mohon Maaf, Batas waktu untuk Perubahan Data, Telah Habis!!'
                ];
            } else {
                $id = $this->request->getVar('id');
                $this->Usulan22Model->delete($id);
                $msg = [
                    'sukses' => 'Data berhasil dihapus'
                ];
            }
            echo json_encode($msg);
        } else {
            $data = [
                'title' => 'Access denied',
            ];

            return redirect()->to('lockscreen');
        }
    }

    public function formedit()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());
            $times = new Time();
            $hari = $times->getDay();       // 12
            $jam = $times->hour;           // 16
            $menit = $times->minute;         // 15
            $hari_ini = $hari . $jam . $menit;
            $deadline = '141414';

            if ($hari_ini > $deadline) {
                $msg = [
                    'informasi' => 'Mohon Maaf, Batas waktu untuk Perubahan Data Telah Habis!!'
                ];
                echo json_encode($msg);
            } else {
                $this->PekerjaanModel = new PekerjaanModel();
                $this->ShdkModel = new ShdkModel();
                $this->StatusKawinModel = new StatusKawinModel();
                $this->WilayahModel = new WilayahModel();
                $this->BansosModel = new BansosModel();
                $DisabilitasJenisModel = new DisabilitasJenisModel();
                $users = new UsersModel();

                $id = $this->request->getVar('id');
                $model = new Usulan22Model();
                $row = $model->find($id);

                $data = [
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
            }
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

                $this->Usulan22Model->update($id, $data);

                $msg = [
                    'sukses' => 'Data berhasil diubah',
                ];
            }
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    function bulan()
    {
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
        $user_id = session()->get('id');

        $wilayahModel = new WilayahModel();
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
                    },100);
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
        ];
        // dd($data['session']);
        return view('dtks/data/dtks/usulan/impor_csv', $data);
    }
    public function importCsvToDb()
    {
        // $this->validate =  \Config\Services::validation();
        // $input = $this->validate([
        //     'file' => [
        //         'rules' => 'uploaded[file]|max_size[file,2048]|ext_in[file,csv]',
        //         'errors' => [
        //             'uploaded' => 'Belum ada File yang di Upload',
        //             'max_size' => 'Ukuran file terlalu besar',
        //             'ext_in' => 'File yang anda Upload bukan CSV',
        //         ]
        //     ]
        // ]);
        // $input = $validation->setRules([
        //     'file' => [
        //         'label'  => 'File CSV',
        //         'rules'  => 'uploaded[file]|max_size[file,2048]|ext_in[file,csv]',
        //         'errors' => [
        //             'uploaded' => 'Belum ada File yang di Upload',
        //             'max_size' => 'Ukuran file terlalu besar',
        //             'ext_in' => 'File yang anda Upload bukan CSV',
        //         ]
        //     ]
        // ]);
        $input = $this->validate([
            'file' => 'uploaded[file]|max_size[file,2048]|ext_in[file,csv]'
        ]);
        if (!$input) {
            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Import CSV Report',
                'user_login' => $this->AuthModel->getUserId(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'bansos' => $this->BansosModel->findAll(),
                'statusRole' => $this->GenModel->getStatusRole(),
                'session' => session()->get(),
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
                    $file->move('../public/csvfile', $newName);

                    // Reading file
                    $file = fopen("../public/csvfile/" . $newName, "r");
                    $i = 0;
                    $numberOfFields = 8; // Total number of fields

                    $csvArr = array();

                    // Initialize $importData_arr Array
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);

                        // Skip first row & check number of fields
                        if ($i > 0 && $num == $numberOfFields) {

                            // Key names are the insert table field names - name, email, city, and status
                            $csvArr[$i]['cr_nama_kec'] = $filedata[0];
                            $csvArr[$i]['cr_nama_desa'] = $filedata[1];
                            $csvArr[$i]['cr_nik_usulan'] = $filedata[2];
                            $csvArr[$i]['cr_program_bansos'] = $filedata[3];
                            $csvArr[$i]['cr_hasil'] = $filedata[4];
                            $csvArr[$i]['cr_padan'] = $filedata[5];
                            $csvArr[$i]['cr_nama_lgkp'] = $filedata[6];
                            $csvArr[$i]['cr_ket_vali'] = $filedata[7];
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

    public function tbCsv()
    {
        $this->CsvReportModel = new CsvReportModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('bansos');
        $filter5 = '';
        $filter6 = '';
        // $filter5 = $this->request->getPost('data_tahun');
        // $filter6 = $this->request->getPost('data_bulan');

        $listing = $this->CsvReportModel->getDataTabel($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);
        $jumlah_semua = $this->CsvReportModel->semua();
        $jumlah_filter = $this->CsvReportModel->filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            if (session()->get('role_id') < 3) {
                $row[] = $key->kecamatan;
                $row[] = $key->kelurahan;
            }
            $row[] = $key->cr_nama_kec;
            $row[] = $key->cr_nama_desa;
            $row[] = $key->du_nik;
            $row[] = $key->nama;
            $row[] = $key->cr_nama_lgkp;
            $row[] = $key->nokk;
            $row[] = $key->alamat;
            $row[] = $key->rt;
            $row[] = $key->rw;
            if (session()->get('role_id') < 3) {
                $row[] = $key->program_bansos;
            }
            $row[] = $key->cr_program_bansos;
            $row[] = $key->cr_hasil;
            $row[] = $key->cr_padan;
            $row[] = $key->cr_ket_vali;
            $row[] = $key->cr_created_by;
            $row[] = $key->cr_created_at;
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
}
