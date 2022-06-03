<?php

namespace App\Controllers\Dtks;


use App\Controllers\BaseController;
use App\Models\Dtks\BpntGantiModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;
use App\Models\GenModel;
use App\Models\Dtks\UsersModel;
use App\Models\Dtks\VeriVali09Model;
use App\Models\Dtks\VervalPbiModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class BpntGanti extends BaseController
{
    public function __construct()
    {
        helper(['form']);
        $this->VeriVali09Model = new VeriVali09Model();
        $this->VervalPbiModel = new VervalPbiModel();
        $this->WilayahModel = new WilayahModel();
        $this->BpntGantiModel = new BpntGantiModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->GenModel = new GenModel();
        $this->UsersModel = new UsersModel();
    }

    public function index()
    {

        $jbt = (session()->get('level'));

        $desa = $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll();
        foreach ($desa as $row) {
        }


        $data = [
            'title' => 'Data Kartu Indonesia Pintar',
            'percentages' => $this->VervalPbiModel->jml_persentase(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            'kecamatan' => $this->WilayahModel->getKec()->getResultArray(),
            'nama_sekolah' => $this->UsersModel->getSchool()->getResultArray(),
            'jenjang_sekolah' => $this->GenModel->getSekolahJenjang()->getResultArray(),
            'kelas_sekolah' => $this->BpntGantiModel->getKelas()->getResultArray(),
        ];
        // dd($data['percentages']);
        // dd($data['jml_persentase']);
        return view('dtks/data/kip/home', $data);
    }


    public function tabel_data()
    {

        $model = new BpntGantiModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('nama_sekolah');
        $filter3 = $this->request->getPost('jenjang');
        $filter4 = $this->request->getPost('kelas');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->dk_kks;
            $row[] = $key->dk_kip;
            $row[] = $key->dk_nik;
            $row[] = $key->dk_nama_siswa;
            $row[] = $key->dk_alamat . ' ' . $key->dk_rt . '/' . $key->dk_rw;
            $row[] = $key->sj_nama;
            $row[] = $key->dk_kelas;
            $row[] = '<a class="btn btn-sm btn-outline-success" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->dk_id . "'" . ')"><i class="fa fa-pencil-alt"></i> Edit</a> | 
            <button class="btn btn-sm btn-outline-danger" data-id="' . $key->dk_id . '" data-nama="' . $key->dk_nama_siswa . '" id="deleteBtn"><i class="fa fa-trash-alt"></i> Hapus</button>';
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
                'kecamatan' => $this->WilayahModel->getKec()->getResultArray(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'partisipasi_sekolah' => $this->GenModel->getStatusPs()->getResultArray(),
                'jenjang_sekolah' => $this->GenModel->getSekolahJenjang()->getResultArray(),
                'nama_sekolah' => $this->UsersModel->getSchool()->getResultArray(),
                'users' => $this->UsersModel->findAll(),
            ];

            $msg = [
                'data' => view('dtks/data/kip/modaltambah', $data),
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
            // die;
            // validasi input
            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'dk_kks' => [
                    'label' => 'No.KKS',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dk_kip' => [
                    'label' => 'No.KIP',
                    'rules' => 'required|is_unique[dtks_kip.dk_kip,dk_kip,{dk_kip}]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dk_nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[dtks_kip.dk_nik,dk_id,{dk_id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dk_nama_siswa' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_jenkel' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_tmp_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_tgl_lahir' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'dk_alamat' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_rt' => [
                    'label' => 'No. RT',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_rw' => [
                    'label' => 'No. RW',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_desa' => [
                    'label' => 'Desa / Kelurahan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_kecamatan' => [
                    'label' => 'Kecamatan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_ibu' => [
                    'label' => 'Nama Ibu',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_ayah' => [
                    'label' => 'Nama Ayah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_sekolah' => [
                    'label' => 'Nama Sekolah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'dk_jenjang' => [
                    'label' => 'Jenjang Pendidikan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'dk_kelas' => [
                    'label' => 'Kelas',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_partisipasi' => [
                    'label' => 'Partisipasi Sekolah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                    ]
                ],
                'dk_created_by' => [
                    'label' => 'Editor',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                    ]
                ],
            ]);
            if (!$valid) {

                $msg = [
                    'error' => [
                        'dk_kks' => $validation->getError('dk_kks'),
                        'dk_kip' => $validation->getError('dk_kip'),
                        'dk_nik' => $validation->getError('dk_nik'),
                        'dk_nama_siswa' => $validation->getError('dk_nama_siswa'),
                        'dk_jenkel' => $validation->getError('dk_jenkel'),
                        'dk_tmp_lahir' => $validation->getError('dk_tmp_lahir'),
                        'dk_tgl_lahir' => $validation->getError('dk_tgl_lahir'),
                        'dk_alamat' => $validation->getError('dk_alamat'),
                        'dk_rt' => $validation->getError('dk_rt'),
                        'dk_rw' => $validation->getError('dk_rw'),
                        'dk_desa' => $validation->getError('dk_desa'),
                        'dk_kecamatan' => $validation->getError('dk_kecamatan'),
                        'dk_nama_ibu' => $validation->getError('dk_nama_ibu'),
                        'dk_nama_ayah' => $validation->getError('dk_nama_ayah'),
                        'dk_nama_sekolah' => $validation->getError('dk_nama_sekolah'),
                        'dk_jenjang' => $validation->getError('dk_jenjang'),
                        'dk_kelas' => $validation->getError('dk_kelas'),
                        'dk_partisipasi' => $validation->getError('dk_partisipasi'),
                        'dk_created_by' => $validation->getError('dk_created_by'),
                    ]
                ];
            } else {
                $data = [
                    'dk_kks' => strtoupper($this->request->getVar('dk_kks')),
                    'dk_kip' => strtoupper($this->request->getVar('dk_kip')),
                    'dk_nik' => $this->request->getVar('dk_nik'),
                    'dk_nama_siswa' => strtoupper($this->request->getVar("dk_nama_siswa")),
                    'dk_jenkel' => $this->request->getVar("dk_jenkel"),
                    'dk_tmp_lahir' => strtoupper($this->request->getVar('dk_tmp_lahir')),
                    'dk_tgl_lahir' => $this->request->getVar("dk_tgl_lahir"),
                    'dk_alamat' => strtoupper($this->request->getVar("dk_alamat")),
                    'dk_rt' => strtoupper($this->request->getVar("dk_rt")),
                    'dk_rw' => strtoupper($this->request->getVar("dk_rw")),
                    'dk_desa' => $this->request->getVar('dk_desa'),
                    'dk_kecamatan' => $this->request->getVar("dk_kecamatan"),
                    'dk_nama_ibu' => strtoupper($this->request->getVar("dk_nama_ibu")),
                    'dk_nama_ayah' => strtoupper($this->request->getVar("dk_nama_ayah")),
                    'dk_nama_sekolah' => strtoupper($this->request->getVar('dk_nama_sekolah')),
                    'dk_jenjang' => $this->request->getVar('dk_jenjang'),
                    'dk_kelas' => $this->request->getVar('dk_kelas'),
                    'dk_partisipasi' => $this->request->getVar('dk_partisipasi'),
                    'dk_created_by' => $this->request->getVar('dk_created_by'),
                    'dk_created_at' => date('Y-m-d h:m:s'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $this->BpntGantiModel->save($data);

                $msg = [
                    'sukses' => 'Data berhasil ditambahkan',
                ];
            }
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    function delete()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $this->BpntGantiModel->delete($id);

            $msg = [
                'sukses' => 'Data berhasil dihapus'
            ];
            echo json_encode($msg);
        } else {

            return redirect()->to('lockscreen');
        }
    }

    public function formedit()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());
            $dk_id = $this->request->getVar('dk_id');
            $model = new BpntGantiModel();
            $row = $model->find($dk_id);

            $data = [
                'kecamatan' => $this->WilayahModel->getKec()->getResultArray(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'partisipasi_sekolah' => $this->GenModel->getStatusPs()->getResultArray(),
                'jenjang_sekolah' => $this->GenModel->getSekolahJenjang()->getResultArray(),
                'nama_sekolah' => $this->UsersModel->getSchool()->getResultArray(),
                'users' => $this->UsersModel->findAll(),

                'dk_id' => $row['dk_id'],
                'dk_kks' => $row['dk_kks'],
                'dk_kip' => $row['dk_kip'],
                'dk_nik' => $row["dk_nik"],
                'dk_nama_siswa' => $row["dk_nama_siswa"],
                'dk_jenkel' => $row['dk_jenkel'],
                'dk_tmp_lahir' => $row["dk_tmp_lahir"],
                'dk_tgl_lahir' => $row["dk_tgl_lahir"],
                'dk_alamat' => $row['dk_alamat'],
                'dk_rt' => $row['dk_rt'],
                'dk_rw' => $row['dk_rw'],
                'dk_desa' => $row["dk_desa"],
                'dk_kecamatan' => $row["dk_kecamatan"],
                'dk_nama_ibu' => $row["dk_nama_ibu"],
                'dk_nama_ayah' => $row['dk_nama_ayah'],
                'dk_nama_sekolah' => $row['dk_nama_sekolah'],
                'dk_jenjang' => $row['dk_jenjang'],
                'dk_kelas' => $row['dk_kelas'],
                'dk_partisipasi' => $row['dk_partisipasi'],
                'dk_updated_by' => $row['dk_updated_by'],
                'dk_updated_at' => date('Y-m-d h:m:s'),

                // 'foto_rumah' => $nama_foto_rumah,
            ];

            // var_dump($data['nama_sekolah']);
            $msg = [
                'sukses' => view('dtks/data/kip/modaledit', $data)
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
            $id = $this->request->getVar('dk_id');
            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'dk_kks' => [
                    'label' => 'No.KKS',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dk_kip' => [
                    'label' => 'No.KIP',
                    'rules' => 'required|is_unique[dtks_kip.dk_kip,dk_kip,{dk_kip}]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dk_nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[dtks_kip.dk_nik,dk_id,{dk_id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'dk_nama_siswa' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_jenkel' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_tmp_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_tgl_lahir' => [
                    'label' => 'Tanggal Lahir',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'dk_alamat' => [
                    'label' => 'Alamat',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_desa' => [
                    'label' => 'Desa / Kelurahan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_kecamatan' => [
                    'label' => 'Kecamatan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_ibu' => [
                    'label' => 'Nama Ibu',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_ayah' => [
                    'label' => 'Nama Ayah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_sekolah' => [
                    'label' => 'Nama Sekolah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'dk_jenjang' => [
                    'label' => 'Jenjang Pendidikan',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'dk_kelas' => [
                    'label' => 'Kelas',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_partisipasi' => [
                    'label' => 'Partisipasi Sekolah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                    ]
                ],
                'dk_created_by' => [
                    'label' => 'Editor',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                    ]
                ],
            ]);
            if (!$valid) {

                $msg = [
                    'error' => [
                        'dk_kks' => $validation->getError('dk_kks'),
                        'dk_kip' => $validation->getError('dk_kip'),
                        'dk_nik' => $validation->getError('dk_nik'),
                        'dk_nama_siswa' => $validation->getError('dk_nama_siswa'),
                        'dk_jenkel' => $validation->getError('dk_jenkel'),
                        'dk_tmp_lahir' => $validation->getError('dk_tmp_lahir'),
                        'dk_tgl_lahir' => $validation->getError('dk_tgl_lahir'),
                        'dk_alamat' => $validation->getError('dk_alamat'),
                        'dk_rt' => $validation->getError('dk_rt'),
                        'dk_rw' => $validation->getError('dk_rw'),
                        'dk_desa' => $validation->getError('dk_desa'),
                        'dk_kecamatan' => $validation->getError('dk_kecamatan'),
                        'dk_nama_ibu' => $validation->getError('dk_nama_ibu'),
                        'dk_nama_ayah' => $validation->getError('dk_nama_ayah'),
                        'dk_nama_sekolah' => $validation->getError('dk_nama_sekolah'),
                        'dk_jenjang' => $validation->getError('dk_jenjang'),
                        'dk_kelas' => $validation->getError('dk_kelas'),
                        'dk_partisipasi' => $validation->getError('dk_partisipasi'),
                        'dk_created_by' => $validation->getError('dk_created_by'),
                    ]
                ];
            } else {
                $data = [
                    'dk_kks' => strtoupper($this->request->getVar('dk_kks')),
                    'dk_kip' => strtoupper($this->request->getVar('dk_kip')),
                    'dk_nik' => $this->request->getVar('dk_nik'),
                    'dk_nama_siswa' => strtoupper($this->request->getVar("dk_nama_siswa")),
                    'dk_jenkel' => $this->request->getVar("dk_jenkel"),
                    'dk_tmp_lahir' => strtoupper($this->request->getVar('dk_tmp_lahir')),
                    'dk_tgl_lahir' => $this->request->getVar("dk_tgl_lahir"),
                    'dk_alamat' => strtoupper($this->request->getVar("dk_alamat")),
                    'dk_rt' => strtoupper($this->request->getVar("dk_rt")),
                    'dk_rw' => strtoupper($this->request->getVar("dk_rw")),
                    'dk_desa' => $this->request->getVar('dk_desa'),
                    'dk_kecamatan' => $this->request->getVar("dk_kecamatan"),
                    'dk_nama_ibu' => strtoupper($this->request->getVar("dk_nama_ibu")),
                    'dk_nama_ayah' => strtoupper($this->request->getVar("dk_nama_ayah")),
                    'dk_nama_sekolah' => strtoupper($this->request->getVar('dk_nama_sekolah')),
                    'dk_jenjang' => $this->request->getVar('dk_jenjang'),
                    'dk_kelas' => $this->request->getVar('dk_kelas'),
                    'dk_partisipasi' => $this->request->getVar('dk_partisipasi'),
                    'dk_updated_by' => $this->request->getVar('dk_created_by'),
                    'dk_updated_at' => date('Y-m-d h:m:s'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];

                $this->BpntGantiModel->update($id, $data);

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
        $db      = \Config\Database::connect();
        // $model = new Usulan21Model();
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
