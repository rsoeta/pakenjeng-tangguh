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



class Bnba extends BaseController
{
    public function __construct()
    {
        helper(['form']);
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

        $data = [
            'namaApp' => 'Opr NewDTKS',
            'title' => 'BNBA DTKS',
            'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            // 'operator' => $this->operator->orderBy('NamaLengkap', 'asc')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'datart' => $this->RtModel->noRt(),

            'datart' => $this->BnbaModel->getDataRT()->getResultArray(),
            'datashdk' => $this->datashdk->findAll(),
            'status' => $this->statusdtks->orderBy('jenis_status', 'asc')->findAll(),
            'percentages' => $this->VervalPbiModel->jml_persentase(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_login' => $this->AuthModel->getUserId(),



        ];
        // dd($data['user_login']);
        return view('dtks/data/dtks/clean/index', $data);
    }

    public function tabel_data()
    {
        $model = new BnbaModel();
        // $KetMasalah = new KetModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter0 = '2';
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
            $row[] = $key->db_nama;
            $row[] = $key->db_nkk;
            $row[] = $key->db_nik;
            $row[] = $key->db_tmp_lahir;
            $row[] = $key->db_tgl_lahir;
            $row[] = $key->jenis_shdk;
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

    // function show detail data by id from BnbaModel
    public function detail($id)
    {
        if ($this->request->isAJAX()) {
            $kode_kec = Profil_Admin()['kode_kec'];
            $BnbaModel = new BnbaModel();

            $id = $this->request->getVar('db_id');
            // dd($id);
            $row = $BnbaModel->find($id);

            $data = [
                'title' => 'View User',
                'modTtl' => 'Form. View User',
                'id' => $row['id'],
                'nik' => $row['nik'],
                'kode_desa' => $row['kode_desa'],
                'username' => $row['username'],
                'fullname' => $row['fullname'],
                'email' => $row['email'],
                'status' => $row['status'],
                'level' => $row['level'],
                'role_id' => $row['role_id'],
                'datarw' => $this->RwModel->noRw(),
                'user_image' => $row['user_image'],
                'roles' => $this->Role->getRole()->getResultArray(),
                'desKels' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', $kode_kec)->findAll(),

            ];
            // dd($data);
            $msg = [
                'sukses' => view('dtks/users/formview', $data),
            ];

            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
            exit;
        }
    }

    public function formview()
    {
        if ($this->request->isAJAX()) {

            // var_dump($this->request->getPost());

            $id = $this->request->getVar('id');

            $model = new BnbaModel();
            $row = $model->find($id);

            // var_dump($row);
            $kode_kab = $row['db_regency'];

            $data = [
                'title' => 'Detail Penerima Manfaat',
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
                'status' => $row['db_status'],
            ];


            // dd($data);
            $msg = [
                'sukses' => view('dtks/data/dtks/clean/modaledit', $data)

            ];

            echo json_encode($msg);
        }
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
            ];


            // dd($data);
            $msg = [
                'sukses' => view('dtks/data/dtks/clean/modalMati', $data)

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
            $nikLama = $this->BnbaModel->find($id_data);
            // var_dump($nikLama);
            // die;
            if ($nikLama['d_nik'] == $this->request->getVar('nomor_nik')) {
                $rule_nik = 'required|numeric|is_unique[dtks_data_clean.d_nik]|min_length[16]|max_length[16]';
            } else {
                $rule_nik = 'required|numeric|min_length[16]|max_length[16]';
            }


            $valid = $this->validate([
                'status' => [
                    'label' => 'Status',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'tanggal_meninggal' => [
                    'label' => 'Tanggal Meninggal',
                    'rules' => 'required|valid_date',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'valid_date' => '{field} tidak valid.'
                    ]
                ],
                'no_registrasi_meninggal' => [
                    'label' => 'No. Registrasi Meninggal',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
            ]);


            if (!$valid) {

                $msg = [
                    'error' => [
                        'status' => $validation->getError('status'),
                        'tanggal_meninggal' => $validation->getError('tanggal_meninggal'),
                        'no_registrasi_meninggal' => $validation->getError('no_registrasi_meninggal'),
                    ]
                ];
            } else {
                $dataBnba = [
                    'province_id' => $this->request->getVar('province_id'),
                    'regency_id' => $this->request->getVar('regency_id'),
                    'district_id' => $this->request->getVar('district_id'),
                    'village_id' => $this->request->getVar('village_id'),
                    'd_alamat' => $this->request->getVar('alamat'),
                    'd_dusun' => $this->request->getVar('dusun'),
                    'd_rw' => $this->request->getVar("no_rw"),
                    'd_rt' => $this->request->getVar("no_rt"),
                    'd_nkk' => $this->request->getVar('nomor_kk'),
                    'd_nik' => $this->request->getVar('nomor_nik'),
                    'd_nama' => $this->request->getVar('nama'),
                    'd_tmp_lahir' => $this->request->getVar("tempat_lahir"),
                    'd_tgl_lahir' => $this->request->getVar("tanggal_lahir"),
                    'd_jenkel_id' => $this->request->getVar("jenis_kelamin"),
                    'd_ibu_kandung' => $this->request->getVar("nama_ibu_kandung"),
                    'd_shdk_id' => $this->request->getVar("hubungan_keluarga"),
                    'd_status' => $this->request->getVar('status'),
                    'updated_by' => session()->get('nik'),
                    'updated_at' => date('Y-m-d h:m:s'),
                    // 'foto_rumah' => $nama_foto_rumah,
                ];


                $this->BnbaModel->update($id_data, $dataBnba);

                $msg = [
                    'sukses' => 'Data berhasil diupdate',
                ];
            }
            echo json_encode($msg);
        } else {
            return view('lockscreen');
        }
    }
}
