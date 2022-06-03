<?php

namespace App\Controllers\Dtks;

use App\Controllers\BaseController;
use App\Models\Dtks\VervalPbiModel;
use App\Models\Dtks\DtksStatusModel;
use App\Models\Dtks\DtksKetModel;
use App\Models\GenModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;

class VervalPbi extends BaseController
{
    public function __construct()
    {
        helper(['form']);
        $this->VervalPbiModel = new VervalPbiModel();
        $this->WilayahModel = new WilayahModel();
        $this->GenModel = new GenModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->statusdtks = new DtksStatusModel();
        $this->keterangan = new DtksKetModel();
    }

    public function index()
    {

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Verifikasi dan ValiVasi data PBI JK NON-DTKS 2021',
            'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            // 'operator' => $this->operator->orderBy('NamaLengkap', 'asc')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'datart' => $this->RtModel->noRt(),
            'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
            'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
            'verivali_pbi' => $this->GenModel->getDataVerivaliPbi(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'percentages' => $this->VervalPbiModel->jml_persentase(),


        ];
        // dd($data['masuk']);
        return view('dtks/data/pbi/verval/index', $data);
    }

    public function tabel_data()
    {
        $model = new VervalPbiModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('datadesa');
        $filter2 = $this->request->getPost('datarw');
        $filter3 = $this->request->getPost('datart');
        $filter4 = $this->request->getPost('datastatus');
        $filter5 = $this->request->getPost('dataVvPbi');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="javascript:void(0)" title="View" onclick="edit_person(' . "'" . $key->id . "'" . ')">' . $key->nama . '</a>';
            $row[] = $key->noka;
            $row[] = $key->ps_noka;
            $row[] = $key->alamat;
            $row[] = $key->kkno;
            $row[] = $key->nik;
            $row[] = $key->tmplhr;
            $row[] = $key->tgllhr;
            $row[] = $key->StatusKawin;
            $row[] = $key->nmibu;

            $badges = $key->status;
            if ($badges == 1) {
                $row[] = '<span class="badge bg-success" selected>Aktif</span>';
            } elseif ($badges == 2) {
                $row[] = '<span class="badge bg-secondary" selected>Meninggal Dunia</span>';
            } elseif ($badges == 3) {
                $row[] = '<span class="badge bg-warning" selected>Ganda</span>';
            } elseif ($badges == 4) {
                $row[] = '<span class="badge bg-warning" selected>Pindah</span>';
            } elseif ($badges == 5) {
                $row[] = '<span class="badge bg-secondary" selected>Tidak Ditemukan</span>';
            } elseif ($badges == 7) {
                $row[] = '<span class="badge bg-warning" selected>Menolak</span>';
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
            $row[] = '<a class="btn btn-sm" href="javascript:void(0)" title="View" onclick="edit_person(' . "'" . $key->id . "'" . ')"><i class="fa fa-pencil-alt"></i></a>';
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

    public function tabel_pbi_verivali()
    {
        $model = new VervalPbiModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('datadesaverivali');
        $filter2 = $this->request->getPost('datarwverivali');
        $filter3 = $this->request->getPost('datartverivali');
        $filter4 = $this->request->getPost('datastatusverivali');
        $filter5 = $this->request->getPost('dataVvPbiverivali');

        $listing = $model->get_datatables_verivali($filter1, $filter2, $filter3, $filter4, $filter5);
        $jumlah_semua = $model->jumlah_semua_verivali();
        $jumlah_filter = $model->jumlah_filter_verivali($filter1, $filter2, $filter3, $filter4, $filter5);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="javascript:void(0)" title="View" onclick="edit_person(' . "'" . $key->id . "'" . ')">' . $key->nama . '</a>';
            $row[] = $key->noka;
            $row[] = $key->ps_noka;
            $row[] = $key->alamat;
            $row[] = $key->kkno;
            $row[] = $key->nik;
            $row[] = $key->nik_siks;
            $row[] = $key->tmplhr;
            $row[] = $key->tgllhr;
            $row[] = $key->StatusKawin;
            $row[] = $key->nmibu;

            $badges = $key->status;
            if ($badges == 1) {
                $row[] = '<span class="badge bg-success" selected>Aktif</span>';
            } elseif ($badges == 2) {
                $row[] = '<span class="badge bg-secondary" selected>Meninggal Dunia</span>';
            } elseif ($badges == 3) {
                $row[] = '<span class="badge bg-warning" selected>Ganda</span>';
            } elseif ($badges == 4) {
                $row[] = '<span class="badge bg-warning" selected>Pindah</span>';
            } elseif ($badges == 5) {
                $row[] = '<span class="badge bg-secondary" selected>Tidak Ditemukan</span>';
            } elseif ($badges == 7) {
                $row[] = '<span class="badge bg-warning" selected>Menolak</span>';
            } else {
                $row[] = '<span class="badge bg-warning" selected>Belum Cek</span>';
            }
            $row[] = $key->vp_keterangan;
            // $row[] = $key->NamaPendidikan;
            // $row[] = "<button class='btn btn-lg' onclick='delet('" . $key->ID . "')'>
            //                                             <i class='fa fa-trash-alt'></i>
            //                                         </button>";
            // $row[] = "<button type=\"button\" class=\"btn btn-outline-info btn-sm\" onclick=\"window.location='/verivali09/redaktirovat/" . $key->idv . "'\"><i class=\"fas fa-align-left\"></i></button>";
            //add html for action
            $row[] = '<a class="btn btn-sm" href="javascript:void(0)" title="View" onclick="edit_person(' . "'" . $key->id . "'" . ')"><i class="fa fa-pencil-alt"></i></a>';
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
            $id = $this->request->getVar('id');

            $model = new VervalPbiModel();
            $row = $model->find($id);

            $data = [
                'title' => 'Form. Edit',
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
                'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
                'datapisat' => $this->VervalPbiModel->getDataPisat(),
                'jenisKelamin' => $this->GenModel->getDataJenkel(),
                'statusKawin' => $this->GenModel->getDataStatusKawin(),
                'verivali_pbi' => $this->GenModel->getDataVerivaliPbi(),


                'id' => $id,
                'noka' => $row['noka'],
                'ps_noka' => $row['ps_noka'],
                'nama' => $row['nama'],
                'jenkel' => $row['jenkel'],
                'tgllhr' => $row['tgllhr'],
                'tmplhr' => $row['tmplhr'],
                'nik' => $row['nik'],
                'nik_siks' => $row['nik_siks'],
                'pisat' => $row['pisat'],
                'kdstawin' => $row['kdstawin'],
                'kelas_rawat' => $row['kelas_rawat'],
                'kkno' => $row['kkno'],
                'alamat' => $row['alamat'],
                'rt' => $row['rt'],
                'rw' => $row['rw'],
                'kodepos' => $row['kodepos'],
                'keterangan_bayi' => $row['keterangan_bayi'],
                'nikayah' => $row['nikayah'],
                'nmayah' => $row['nmayah'],
                'nikibu' => $row['nikibu'],
                'nmibu' => $row['nmibu'],
                'ket_aktivasi' => $row['ket_aktivasi'],
                'kdkepwil' => $row['kdkepwil'],
                'kdkc' => $row['kdkc'],
                'nmkc' => $row['nmkc'],
                'kdprov' => $row['kdprov'],
                'nmprov' => $row['nmprov'],
                'stat' => $row['status'],
                'vv_pbi' => $row['verivali_pbi'],
            ];
            $msg = [
                'sukses' => view('dtks/data/pbi/verval/modaledit', $data)
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
            $id = $this->request->getVar('id');
            //cek nik
            $nikLama = $this->VervalPbiModel->find($id);
            if ($nikLama['nik'] == $this->request->getVar('nik')) {
                $rule_nik = 'required|numeric|min_length[16]|max_length[16]';
            } else {
                $rule_nik = 'required|numeric|is_unique[dtks_pbi_jkn.nik]|min_length[16]|max_length[16]';
            }

            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'nama' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'jenkel' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'tgllhr' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'tmplhr' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'nik' => [
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
                'datapisat' => [
                    'label' => 'PISAT',
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
                'kelas_rawat' => [
                    'label' => 'Kelas Rawat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'kkno' => [
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
                'kodepos' => [
                    'label' => 'Kode Pos',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'nikayah' => [
                    'label' => 'NIK Ayah',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'nmayah' => [
                    'label' => 'Nama Ayah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'nikibu' => [
                    'label' => 'NIK Ibu',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'nmibu' => [
                    'label' => 'Nama Ibu',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
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
                        'id' => $id,
                        'nama' => $validation->getError('nama'),
                        'jenkel' => $validation->getError('jenkel'),
                        'tgllhr' => $validation->getError('tgllhr'),
                        'tmplhr' => $validation->getError('tmplhr'),
                        'nik' => $validation->getError('nik'),
                        'datapisat' => $validation->getError('datapisat'),
                        'status_kawin' => $validation->getError('status_kawin'),
                        'kelas_rawat' => $validation->getError('kelas_rawat'),
                        'kkno' => $validation->getError('kkno'),
                        'alamat' => $validation->getError('alamat'),
                        'rt' => $validation->getError('rt'),
                        'rw' => $validation->getError('rw'),
                        'kodepos' => $validation->getError('kodepos'),
                        'nikayah' => $validation->getError('nikayah'),
                        'nmayah' => $validation->getError('nmayah'),
                        'nikibu' => $validation->getError('nikibu'),
                        'nmibu' => $validation->getError('nmibu'),
                        'status' => $validation->getError('status'),
                    ]
                ];
            } else {
                $dataUpdate = [
                    'id' => $id,
                    'noka' => $this->request->getVar('noka'),
                    'ps_noka' => $this->request->getVar('ps_noka'),
                    'nama' => strtoupper($this->request->getVar('nama')),
                    'jenkel' => $this->request->getVar('jenkel'),
                    'tgllhr' => $this->request->getVar("tgllhr"),
                    'tmplhr' => strtoupper($this->request->getVar("tmplhr")),
                    'nik' => $this->request->getVar('nik'),
                    'nik_siks' => $this->request->getVar('nik_siks'),
                    'pisat' => $this->request->getVar('datapisat'),
                    'kdstawin' => $this->request->getVar('status_kawin'),
                    'kelas_rawat' => $this->request->getVar('kelas_rawat'),
                    'kkno' => $this->request->getVar('kkno'),
                    'alamat' => strtoupper($this->request->getVar('alamat')),
                    'rt' => $this->request->getVar("rt"),
                    'rw' => $this->request->getVar("rw"),
                    'kodepos' => $this->request->getVar('kodepos'),
                    'nikayah' => $this->request->getVar('nikayah'),
                    'nmayah' => strtoupper($this->request->getVar('nmayah')),
                    'nikibu' => $this->request->getVar('nikibu'),
                    'nmibu' => strtoupper($this->request->getVar('nmibu')),
                    'ket_aktivasi' => $this->request->getVar('ket_aktivasi'),
                    'kdkepwil' => $this->request->getVar('kdkepwil'),
                    'kdkc' => $this->request->getVar('kdkc'),
                    'nmkc' => $this->request->getVar('nmkc'),
                    'kdprov' => $this->request->getVar('kdprov'),
                    'nmprov' => $this->request->getVar('nmprov'),
                    'status' => $this->request->getVar('status'),
                    'verivali_pbi' => $this->request->getVar('verivali_pbi'),
                    'updated_by' => session()->get('nik'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                // $id = $this->VervalPbiModel->find($this->request->getVar('id'));
                $this->VervalPbiModel->update($id, $dataUpdate);

                $msg = [
                    'sukses' => 'Data berhasil diupdate',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }

    public function ketVervalPbi()
    {
        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'Keterangan Verval PBI',
            'verivali_pbi' => $this->GenModel->getDataVerivaliPbi(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'percentages' => $this->VervalPbiModel->jml_persentase(),

        ];

        return view('dtks/data/pbi/general/ket-verval-pbi', $data);
    }

    public function formTambahKetVvPbi()
    {
        if ($this->request->isAJAX()) {

            $GenModel = new GenModel();

            $data = [
                'title' => 'Tambah Keterangan Verval PBI',
            ];

            $msg = [
                'data' => view('dtks/data/pbi/general/modTambahKetPbi', $data),
            ];
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function tmbKetVvPbi()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());
            // validasi input
            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'vp_keterangan' => [
                    'label' => 'Keterangan Verivali',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
            ]);
            if (!$valid) {

                $msg = [
                    'error' => [
                        'vp_keterangan' => $validation->getError('vp_keterangan'),
                    ]
                ];
            } else {
                $data = [
                    'vp_keterangan' => $this->request->getVar('vp_keterangan'),
                ];
                $this->db = \Config\Database::connect();
                $this->db->table('dtks_verivali_pbi')->insert($data);

                $msg = [
                    'sukses' => 'Data berhasil ditambahkan',
                ];
            }
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function hapusKetVvPbi()
    {
        if ($this->request->isAJAX()) {
            $vp_id = $this->request->getVar('vp_id');

            $this->db = \Config\Database::connect();
            $this->db->table('dtks_verivali_pbi')->delete(['vp_id' => $vp_id]);

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

    public function viewKetVvPbi()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());

            $vp_id = $this->request->getVar('vp_id');

            $this->db = \Config\Database::connect();
            // $sql = 'SELECT vp_keterangan FROM dtks_verivali_pbi WHERE vp_id =' . $vp_id;
            $builder = $this->db->table('dtks_verivali_pbi');
            $builder->select('vp_id, vp_keterangan');
            $builder->where('vp_id', $vp_id);

            $query = $builder->get();

            $keterangan = $query->getResultArray();
            foreach ($keterangan as $row) {
                $vp_keterangan = $row['vp_keterangan'];
            }

            // var_dump($row);

            $data = [
                'title' => 'Form. Edit',
                'vp_id' => $vp_id,
                'vp_keterangan' => $vp_keterangan,
            ];
            $msg = [
                'sukses' => view('dtks/data/pbi/general/modEditKetPbi', $data)
            ];

            // var_dump(session()->get('kode_desa'));
            echo json_encode($msg);
        }
    }

    public function updKetVvPbi()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());
            // validasi input
            $this->db = \Config\Database::connect();

            $vp_id = $this->request->getVar('vp_id');

            $dataUpdate = [
                'vp_keterangan' => $this->request->getVar('vp_keterangan'),
            ];

            // var_dump($dataUpdate);

            $this->db->table('dtks_verivali_pbi')->update($vp_id, $dataUpdate);

            $msg = [
                'sukses' => 'Data berhasil diupdate',
            ];
        }
        echo json_encode($msg);
    }

    public function excelpage()
    {

        // echo 'test';

        if (session()->get('role_id') <= 3) {

            $data = [
                'title' => 'NIK BERMASALAH PBI JK NON DTKS 2021',
                'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
                'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
                'verivali_pbi' => $this->GenModel->getDataVerivaliPbi(),
                'statusRole' => $this->GenModel->getStatusRole(),
                'percentages' => $this->VervalPbiModel->jml_persentase(),
                // 'dataStatus' => $this->GenModel->getDataStatusVaksin(),
                // 'datajenkel' => $this->GenModel->getDataJenkel(),
            ];
            // dd($data['datadesa']);

            return view('dtks/data/pbi/verval/excel', $data);
        } else {

            return redirect()->to('lockscreen');
        }
    }


    public function tabexport()
    {
        $model = new VervalPbiModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('datadesaverivali');
        $filter2 = $this->request->getPost('datarwverivali');
        $filter3 = $this->request->getPost('datartverivali');
        $filter4 = $this->request->getPost('datastatusverivali');
        $filter5 = $this->request->getPost('dataVvPbiverivali');

        $listing = $model->get_datatables_verivali($filter1, $filter2, $filter3, $filter4, $filter5);
        $jumlah_semua = $model->jumlah_semua_verivali();
        $jumlah_filter = $model->jumlah_filter_verivali($filter1, $filter2, $filter3, $filter4, $filter5);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->nama;
            $row[] = $key->alamat;
            $row[] = $key->kkno;
            $row[] = $key->nik;
            $row[] = $key->nik_siks;
            $row[] = $key->tgllhr;
            $row[] = $key->nmibu;
            $row[] = $key->vp_keterangan;

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
                'data' => view('dtks/data/pbi/verval/modaltambah', $data),
            ];
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }


    public function save()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());
            // validasi input
            //cek nik
            $rule_nik = 'required|numeric|is_unique[dtks_pbi_jkn.nik]|min_length[16]|max_length[16]';

            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'nama' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'jenkel' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'tgllhr' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'tmplhr' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'nik' => [
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
                'datapisat' => [
                    'label' => 'PISAT',
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
                'kelas_rawat' => [
                    'label' => 'Kelas Rawat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'kkno' => [
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
                'kodepos' => [
                    'label' => 'Kode Pos',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'nikayah' => [
                    'label' => 'NIK Ayah',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'nmayah' => [
                    'label' => 'Nama Ayah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
                    ]
                ],
                'nikibu' => [
                    'label' => 'NIK Ibu',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah digunakan',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'nmibu' => [
                    'label' => 'Nama Ibu',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_dash' => '{field} harus berisi alphabet.'
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
                        'nama' => $validation->getError('nama'),
                        'jenkel' => $validation->getError('jenkel'),
                        'tgllhr' => $validation->getError('tgllhr'),
                        'tmplhr' => $validation->getError('tmplhr'),
                        'nik' => $validation->getError('nik'),
                        'datapisat' => $validation->getError('datapisat'),
                        'status_kawin' => $validation->getError('status_kawin'),
                        'kelas_rawat' => $validation->getError('kelas_rawat'),
                        'kkno' => $validation->getError('kkno'),
                        'alamat' => $validation->getError('alamat'),
                        'rt' => $validation->getError('rt'),
                        'rw' => $validation->getError('rw'),
                        'kodepos' => $validation->getError('kodepos'),
                        'nikayah' => $validation->getError('nikayah'),
                        'nmayah' => $validation->getError('nmayah'),
                        'nikibu' => $validation->getError('nikibu'),
                        'nmibu' => $validation->getError('nmibu'),
                        'status' => $validation->getError('status'),
                    ]
                ];
            } else {
                $dataSave = [
                    'noka' => $this->request->getVar('noka'),
                    'ps_noka' => $this->request->getVar('ps_noka'),
                    'nama' => strtoupper($this->request->getVar('nama')),
                    'jenkel' => $this->request->getVar('jenkel'),
                    'tgllhr' => $this->request->getVar("tgllhr"),
                    'tmplhr' => strtoupper($this->request->getVar("tmplhr")),
                    'nik' => $this->request->getVar('nik'),
                    'nik_siks' => $this->request->getVar('nik_siks'),
                    'pisat' => $this->request->getVar('datapisat'),
                    'kdstawin' => $this->request->getVar('status_kawin'),
                    'kelas_rawat' => $this->request->getVar('kelas_rawat'),
                    'kkno' => $this->request->getVar('kkno'),
                    'alamat' => strtoupper($this->request->getVar('alamat')),
                    'rt' => $this->request->getVar("rt"),
                    'rw' => $this->request->getVar("rw"),
                    'kodepos' => $this->request->getVar('kodepos'),
                    'nikayah' => $this->request->getVar('nikayah'),
                    'nmayah' => strtoupper($this->request->getVar('nmayah')),
                    'nikibu' => $this->request->getVar('nikibu'),
                    'nmibu' => strtoupper($this->request->getVar('nmibu')),
                    'ket_aktivasi' => $this->request->getVar('ket_aktivasi'),
                    'kdkepwil' => $this->request->getVar('kdkepwil'),
                    'kdkc' => $this->request->getVar('kdkc'),
                    'nmkc' => $this->request->getVar('nmkc'),
                    'kdprov' => $this->request->getVar('kdprov'),
                    'nmprov' => $this->request->getVar('nmprov'),
                    'status' => $this->request->getVar('status'),
                    'verivali_pbi' => $this->request->getVar('verivali_pbi'),
                    'desa_kode' => $this->request->getVar('desa_kode'),
                    'created_by' => session()->get('nik'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                // $id = $this->VervalPbiModel->find($this->request->getVar('id'));
                $this->VervalPbiModel->save($dataSave);

                $msg = [
                    'sukses' => 'Data berhasil disimpan',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }
}
