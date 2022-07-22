<?php

namespace App\Controllers\Dtks;

use App\Controllers\BaseController;
use App\Models\Dtks\DtksStatusModel;
use App\Models\Dtks\VervalPbiModel;
use App\Models\Dtks\DtksKetModel;
use App\Models\Dtks\ShdkModel;
use App\Models\Dtks\BnbaModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;
use App\Models\GenModel;
use App\Models\Dtks\AuthModel;



class VervalBnba extends BaseController
{
    public function __construct()
    {
        $this->BnbaModel = new BnbaModel();
        $this->WilayahModel = new WilayahModel();
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->GenModel = new GenModel();
        $this->statusdtks = new DtksStatusModel();
        $this->keterangan = new DtksKetModel();
        $this->datashdk = new ShdkModel();
        $this->VervalPbiModel = new VervalPbiModel();
        $this->AuthModel = new AuthModel();
    }

    public function index()
    {
        $kode_kec = Profil_Admin()['kode_kec'];
        $data = [
            'title' => 'Verivali BNBA DTKS',
            'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', $kode_kec)->findAll(),
            // 'operator' => $this->operator->orderBy('NamaLengkap', 'asc')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'datart' => $this->RtModel->noRt(),

            'datart' => $this->BnbaModel->getDataRT()->getResultArray(),
            'datashdk' => $this->datashdk->findAll(),
            'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_login' => $this->AuthModel->getUserId(),



        ];
        // dd($data['user_login']);
        return view('dtks/data/dtks/verivali/bnba/index', $data);
    }

    public function tabel_data()
    {
        $model = new BnbaModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter0 = 1;
        $filter1 = $this->request->getPost('datadesa');
        // $operator = $this->request->getPost('operator');
        $filter2 = $this->request->getPost('datarw');
        $filter3 = $this->request->getPost('datart');
        $filter4 = $this->request->getPost('datashdk');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4, $filter0);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter0);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <a href="javascript:void(0)" title="more info" onclick="detail_person(' . "'" . $key->db_id . "'" . ')">
            <img src=' . FOTO_DOKUMEN('KPM_BNT' . $key->db_nik . 'A.jpg', 'foto-kpm') . ' alt="' . $key->db_nama . '" style="width: 30px; height: 40px; border-radius: 2px;">
            </a>
            ';
            $row[] = $key->db_id_dtks;
            $row[] = $key->db_nik;
            $row[] = $key->db_nama;
            $row[] = $key->db_nkk;
            $row[] = $key->db_alamat;
            $row[] = $key->db_rt;
            $row[] = $key->db_rw;
            $row[] = '<a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Status" onclick="edit_person(' . "'" . $key->db_id . "'" . ')"><i class="fa fa-stream fa-2xs mr-1"></i></a>';
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

            // var_dump($this->request->getPost());

            $id = $this->request->getVar('id_data');

            $model = new BnbaModel();
            $row = $model->find($id);

            // var_dump($row);
            $kode_kab = $row['db_regency'];

            $data = [
                'title' => 'Status Penerima Manfaat',
                'dataprov' => $this->WilayahModel->getProv()->getResultArray(),
                'datakab' => $this->WilayahModel->getKab()->getResultArray(),
                'datakec' => $this->WilayahModel->getKec($kode_kab)->getResultArray(),
                'datadesa' => $this->WilayahModel->getDataDesa()->getResultArray(),
                'datadusun' => $this->WilayahModel->getDusun()->getResultArray(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
                'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
                'jenisKelamin' => $this->BnbaModel->getDataJenkel(),
                'datashdk' => $this->BnbaModel->getDataShdk(),

                'db_id' => $row['db_id'],
                'db_id_dtks' => $row['db_id_dtks'],
                'province_id' => $row['db_province'],
                'regency_id' => $row['db_regency'],
                'district_id' => $row['db_district'],
                'village_id' => $row['db_village'],
                'alamat' => $row['db_alamat'],
                'dusun' => $row['db_dusun'],
                'no_rw' => $row['db_rw'],
                'no_rt' => $row['db_rt'],
                'nomor_kk' => $row['db_nkk'],
                'nomor_nik' => $row['db_nik'],
                'nama' => $row['db_nama'],
                'tempat_lahir' => $row['db_tmp_lahir'],
                'tanggal_lahir' => $row['db_tgl_lahir'],
                'jenis_kelamin' => $row['db_jenkel_id'],
                'nama_ibu_kandung' => $row['db_ibu_kandung'],
                'hubungan_keluarga' => $row['db_shdk_id'],
                'created_by' => $row['db_creator'],
                'db_status' => $row['db_status'],
                'tanggal_kejadian' => $row['db_tgl_kejadian'],
                'no_registrasi_kejadian' => $row['db_noreg_kejadian']
            ];


            // dd($data);
            $msg = [
                'sukses' => view('dtks/data/dtks/verivali/bnba/modaledit', $data)

            ];

            echo json_encode($msg);
        }
    }

    public function ajax_update()
    {
        // var_dump($this->request->getPost());
        if ($this->request->isAJAX()) {
            // validasi input
            $id_data = $this->request->getVar('id_data');
            $validation = \Config\Services::validation();

            //cek nik
            // var_dump($nikLama);
            // die;


            $valid = $this->validate([
                'status' => [
                    'label' => 'Status',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'tanggal_kejadian' => [
                    'label' => 'Tanggal Meninggal',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ]
            ]);


            if (!$valid) {

                $msg = [
                    'error' => [
                        'status' => $validation->getError('status'),
                        'tanggal_kejadian' => $validation->getError('tanggal_kejadian'),
                        'no_registrasi_kejadian' => $validation->getError('no_registrasi_kejadian'),
                    ]
                ];
            } else {
                $data = [
                    'db_status' => $this->request->getVar('status'),
                    'db_tgl_kejadian' => $this->request->getVar('tanggal_kejadian'),
                    'db_noreg_kejadian' => $this->request->getVar('no_registrasi_kejadian'),
                    'db_tb_status' => '1',
                    'db_modifier' => session()->get('nik'),
                    'db_modified' => date('Y-m-d H:i:s'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];


                $this->BnbaModel->update($id_data, $data);

                $msg = [
                    'sukses' => 'Data berhasil diupdate',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }

    public function tabel_data1()
    {
        $role = session()->get('role_id');
        $model = new BnbaModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter0 = $this->request->getPost('data_status1');
        $filter1 = $this->request->getPost('datadesa1');
        // $operator = $this->request->getPost('operator');
        $filter2 = $this->request->getPost('datarw1');
        $filter3 = $this->request->getPost('datart1');
        $filter4 = $this->request->getPost('datashdk1');
        $filter5 = 2;

        $listing = $model->get_datatables1($filter1, $filter2, $filter3, $filter4, $filter0, $filter5);
        $jumlah_semua = $model->jumlah_semua1();
        $jumlah_filter = $model->jumlah_filter1($filter1, $filter2, $filter3, $filter4, $filter0, $filter5);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $tombolEdit = '<a class="btn btn-sm btn-outline-success" href="javascript:void(0)" title="Edit" onclick="edit_person1(' . "'" . $key->db_id . "'" . ')"><i class="fa fa-stream fa-2xs mr-1"></i></a>';
            $tombolProses = '<button type="button" class="btn btn-outline-primary btn-sm" title="Sukses" id="ChangeNext" data-id=' . $key->db_id . '><i class="fa fa-check mr-1"></i> </button>';
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <a href="javascript:void(0)" title="more info" onclick="detail_person(' . "'" . $key->db_id . "'" . ')">
            <img src=' . FOTO_DOKUMEN('KPM_BNT' . $key->db_nik . 'A.jpg', 'foto-kpm') . ' alt="' . $key->db_nama . '" style="width: 30px; height: 40px; border-radius: 2px;">
            </a>
            ';
            $row[] = $key->db_id_dtks;
            $row[] = $key->db_nama;
            $row[] = $key->db_nik;
            $row[] = $key->db_nkk;
            $row[] = $key->name;
            $row[] = $key->jenis_status;
            $row[] = $key->db_tgl_kejadian;
            $row[] = $key->db_noreg_kejadian;
            $row[] =  ($role <= 2) ? $tombolEdit . ' ' . $tombolProses : $tombolEdit;

            # code...
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

    public function formedit1()
    {
        if ($this->request->isAJAX()) {

            // var_dump($this->request->getPost());

            $id = $this->request->getVar('id_data');

            $model = new BnbaModel();
            $row = $model->find($id);

            // var_dump($row);
            $kode_kab = $row['db_regency'];

            $data = [
                'title' => 'Status Penerima Manfaat',
                'dataprov' => $this->WilayahModel->getProv()->getResultArray(),
                'datakab' => $this->WilayahModel->getKab()->getResultArray(),
                'datakec' => $this->WilayahModel->getKec($kode_kab)->getResultArray(),
                'datadesa' => $this->WilayahModel->getDataDesa()->getResultArray(),
                'datadusun' => $this->WilayahModel->getDusun()->getResultArray(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'keterangan' => $this->keterangan->orderBy('jenis_keterangan', 'asc')->findAll(),
                'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
                'jenisKelamin' => $this->BnbaModel->getDataJenkel(),
                'datashdk' => $this->BnbaModel->getDataShdk(),

                'db_id' => $row['db_id'],
                'db_id_dtks' => $row['db_id_dtks'],
                'province_id' => $row['db_province'],
                'regency_id' => $row['db_regency'],
                'district_id' => $row['db_district'],
                'village_id' => $row['db_village'],
                'alamat' => $row['db_alamat'],
                'dusun' => $row['db_dusun'],
                'no_rw' => $row['db_rw'],
                'no_rt' => $row['db_rt'],
                'nomor_kk' => $row['db_nkk'],
                'nomor_nik' => $row['db_nik'],
                'nama' => $row['db_nama'],
                'tempat_lahir' => $row['db_tmp_lahir'],
                'tanggal_lahir' => $row['db_tgl_lahir'],
                'jenis_kelamin' => $row['db_jenkel_id'],
                'nama_ibu_kandung' => $row['db_ibu_kandung'],
                'hubungan_keluarga' => $row['db_shdk_id'],
                'created_by' => $row['db_creator'],
                'db_status' => $row['db_status'],
                'tanggal_kejadian' => $row['db_tgl_kejadian'],
                'no_registrasi_kejadian' => $row['db_noreg_kejadian'],
            ];


            // dd($data);
            $msg = [
                'sukses' => view('dtks/data/dtks/verivali/bnba/modalupdate', $data)

            ];

            echo json_encode($msg);
        }
    }

    public function ajax_update1()
    {
        // var_dump($this->request->getPost());
        if ($this->request->isAJAX()) {
            // validasi input
            $id_data = $this->request->getVar('id_data');
            $validation = \Config\Services::validation();

            //cek nik
            // var_dump($nikLama);
            // die;


            $valid = $this->validate([
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
                        'status' => $validation->getError('status'),
                        'tanggal_kejadian' => $validation->getError('tanggal_kejadian'),
                        'no_registrasi_kejadian' => $validation->getError('no_registrasi_kejadian'),
                    ]
                ];
            } else {
                $data = [
                    'db_status' => $this->request->getVar('status'),
                    'db_tgl_kejadian' => $this->request->getVar('tanggal_kejadian'),
                    'db_noreg_kejadian' => $this->request->getVar('no_registrasi_kejadian'),
                    'db_modifier' => session()->get('nik'),
                    'db_modified' => date('Y-m-d H:i:s'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];


                $this->BnbaModel->update($id_data, $data);

                $msg = [
                    'sukses' => 'Data berhasil diupdate',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }

    public function lockBnba()
    {
        // var_dump($this->request->getPost());
        if ($this->request->isAJAX()) {
            // validasi input
            $id_data = $this->request->getPost('statusid');
            $db_tb_status = 2;

            $data = [
                'db_tb_status' => $db_tb_status,
                'db_modifier' => session()->get('nik'),
                'db_modified' => date('Y-m-d H:i:s'),
                // 'foto_rumah' => $nama_foto_rumah,
            ];


            $this->BnbaModel->update($id_data, $data);

            $msg = [
                'sukses' => 'Data berhasil diupdate',
            ];

            echo json_encode($msg);
        } else {
            return '<script>
                        alert(\'Mohon Maaf, Batas waktu untuk Tambah Data Telah Habis!!\');
                    </script>';
        }
    }


    public function tabel_data2()
    {
        $role = session()->get('role_id');
        $model = new BnbaModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter0 = $this->request->getPost('data_status1');
        $filter1 = $this->request->getPost('datadesa1');
        // $operator = $this->request->getPost('operator');
        $filter2 = $this->request->getPost('datarw1');
        $filter3 = $this->request->getPost('datart1');
        $filter4 = $this->request->getPost('datashdk1');
        $filter5 = 2;

        $listing = $model->get_datatables2($filter1, $filter2, $filter3, $filter4, $filter0, $filter5);
        $jumlah_semua = $model->jumlah_semua2();
        $jumlah_filter = $model->jumlah_filter2($filter1, $filter2, $filter3, $filter4, $filter0, $filter5);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $tombolEdit = '<a class="btn btn-outline-success btn-sm" href="javascript:void(0)" title="Edit" onclick="edit_person1(' . "'" . $key->db_id . "'" . ')"><i class="fa fa-stream fa-2xs mr-1"></i></a>';
            $tombolProses = '<button type="button" class="btn btn-outline-danger btn-sm" title="Gagal" id="ChangePrev" data-id=' . $key->db_id . '><i class="fa fa-times mr-1"></i> Gagal</button>';
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '
            <a href="javascript:void(0)" title="more info" onclick="detail_person(' . "'" . $key->db_id . "'" . ')">
            <img src=' . FOTO_DOKUMEN('KPM_BNT' . $key->db_nik . 'A.jpg', 'foto-kpm') . ' alt="' . $key->db_nama . '" style="width: 30px; height: 40px; border-radius: 2px;">
            </a>
            ';
            $row[] = $key->db_id_dtks;
            $row[] = $key->db_nama;
            $row[] = $key->db_nik;
            $row[] = $key->db_nkk;
            $row[] = $key->name;
            $row[] = $key->jenis_status;
            $row[] = $key->db_tgl_kejadian;
            $row[] = $key->db_noreg_kejadian;
            $row[] = $tombolProses;

            # code...
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


    public function unlockBnba()
    {
        // var_dump($this->request->getPost());
        if ($this->request->isAJAX()) {
            // validasi input
            $id_data = $this->request->getPost('statusid');
            $db_tb_status = 1;

            $data = [
                'db_tb_status' => $db_tb_status,
                'db_modifier' => session()->get('nik'),
                'db_modified' => date('Y-m-d H:i:s'),
                // 'foto_rumah' => $nama_foto_rumah,
            ];


            $this->BnbaModel->update($id_data, $data);

            $msg = [
                'sukses' => 'Data berhasil diupdate',
            ];

            echo json_encode($msg);
        } else {
            return '<script>
                        alert(\'Mohon Maaf, Akses tidak diizinkan!!\');
                    </script>';
        }
    }
}
