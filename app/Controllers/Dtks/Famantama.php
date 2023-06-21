<?php

namespace App\Controllers\Dtks;


use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\DtksModel;
use App\Models\Dtks\Famantama\FamantamaModel;
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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Number;

class Famantama extends BaseController
{
    public function __construct()
    {
        $this->AuthModel = new AuthModel();
        $this->FamantamaModel = new FamantamaModel();
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
        // var_dump(deadline_ppks());
        // die;

        // Mengambil data dari view
        $db = \Config\Database::connect();
        $query = $db->query('SELECT rw_rt, fd_rw, fd_rt, jml_rkp FROM vw_famantama_rkp');
        $chartData = $query->getResultArray();

        if (session()->get('role_id') == 1) {
            $this->FamantamaModel = new FamantamaModel();
            $this->WilayahModel = new WilayahModel();
            $this->RwModel = new RwModel();
            $this->BansosModel = new BansosModel();
            $this->PekerjaanModel = new PekerjaanModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->ShdkModel = new ShdkModel();

            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Data Fakir Miskin dan Orang Tidak Mampu',
                'user_login' => $this->AuthModel->getUserId(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'bansos' => $this->BansosModel->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('JenisPekerjaan', 'asc')->findAll(),
                'statusKawin' => $this->StatusKawinModel->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $this->ShdkModel->findAll(),
                'percentages' => $this->VervalPbiModel->jml_persentase(),
                'statusRole' => $this->GenModel->getStatusRole(),
                'kerja_famantama' => $this->GenModel->get_jenis_pekerjaan(),
                'chartData' => $chartData,
            ];

            return view('dtks/data/dtks/famantama/tables', $data);
        } else if (session()->get('role_id') >= 1) {
            $this->FamantamaModel = new FamantamaModel();
            $this->WilayahModel = new WilayahModel();
            $this->RwModel = new RwModel();
            $this->BansosModel = new BansosModel();
            $this->ShdkModel = new ShdkModel();

            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Data Fakir Miskin dan Orang Tidak Mampu',
                'user_login' => $this->AuthModel->getUserId(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('pk_nama', 'asc')->findAll(),
                'statusKawin' => $this->StatusKawinModel->orderBy('StatusKawin', 'asc')->findAll(),
                'shdk' => $this->ShdkModel->findAll(),
                'percentages' => $this->VervalPbiModel->jml_persentase(),
                'statusRole' => $this->GenModel->getStatusRole(),
                'kerja_famantama' => $this->GenModel->get_jenis_pekerjaan(),
                'chartData' => $chartData,

            ];

            // dd($data['chartData']);

            return view('dtks/data/dtks/famantama/tables', $data);
        } else {
            return redirect()->to('lockscreen');
        }
    }


    public function tabel_data()
    {
        // var_dump(deadline_ppks());

        $this->FamantamaModel = new FamantamaModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        // $role = session()->get('role_id');

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('shdk');
        // $filter5 = $this->request->getPost('data_tahun');
        // $filter6 = $this->request->getPost('data_bulan');
        // $filter7 = '0';

        $listing = $this->FamantamaModel->get_datatables($filter1, $filter2, $filter3, $filter4);
        $jumlah_semua = $this->FamantamaModel->jumlah_semua();
        $jumlah_filter = $this->FamantamaModel->jumlah_filter($filter1, $filter2, $filter3, $filter4);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->fd_nama_lengkap;
            $row[] = $key->fd_nik;
            $row[] = $key->fd_nkk;
            $row[] = $key->fd_alamat;
            $row[] = $key->fd_rt;
            $row[] = $key->fd_rw;
            $row[] = $key->jenis_shdk;
            $row[] = $key->pk_nama;
            $row[] = '<a href="https://wa.me/' . nope($key->nope) . '" target="_blank" style="text-decoration:none;">' . strtoupper($key->fullname) . '</a>';
            $row[] = $key->fd_created_at;
            $row[] = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->fd_id . "'" . ')"><i class="far fa-edit"></i></a> | 
                <button class="btn btn-sm btn-secondary" data-id="' . $key->fd_id . '" data-nama="' . $key->fd_nama_lengkap . '" id="deleteBtn"><i class="far fa-trash-alt"></i></button>';

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

            $this->FamantamaModel = new FamantamaModel();
            $this->WilayahModel = new WilayahModel();
            $this->BansosModel = new BansosModel();
            $this->PekerjaanModel = new PekerjaanModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->ShdkModel = new ShdkModel();
            $users = new UsersModel();

            $data = [
                'title' => 'Form. Tambah Data',
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'users' => $users->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('pk_nama')->findAll(),
                'shdk' => $this->ShdkModel->findAll(),
                'sta_bangteti' => $this->GenModel->get_sta_bangteti(),
                'sta_lahteti' => $this->GenModel->get_sta_lahteti(),
                'jenlai' => $this->GenModel->get_jenlai(),
                'jenlai' => $this->GenModel->get_jenlai(),
                'jendin' => $this->GenModel->get_jendin(),
                'jentap' => $this->GenModel->get_jentap(),
                'kondisi' => $this->GenModel->get_kondisi(),
                'penghasilan' => $this->GenModel->get_penghasilan(),
                'pengeluaran' => $this->GenModel->get_pengeluaran(),
                'jml_tanggungan' => $this->GenModel->get_jml_tanggungan(),
                'roda_dua' => $this->GenModel->get_roda_dua(),
                'sumber_minum' => $this->GenModel->get_sumber_minum(),
                'cara_minum' => $this->GenModel->get_cara_minum(),
                'penerangan_utama' => $this->GenModel->get_penerangan_utama(),
                'daya_listrik' => $this->GenModel->get_daya_listrik(),
                'bahan_masak' => $this->GenModel->get_bahan_masak(),
                'tempat_bab' => $this->GenModel->get_tempat_bab(),
                'jenis_kloset' => $this->GenModel->get_jenis_kloset(),
                'tempat_tinja' => $this->GenModel->get_tempat_tinja(),
                'jenis_pekerjaan' => $this->GenModel->get_jenis_pekerjaan(),
            ];
            if (deadline_ppks() === 1) {
                $msg = [
                    'data' =>
                    '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Ops...",
                                text: "Akses Tidak Sesuai!",
                                })
                        </script>'
                ];
                echo json_encode($msg);
            } else {
                $msg = [
                    'data' => view('dtks/data/dtks/famantama/modaltambah', $data),
                ];
                echo json_encode($msg);
            }
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function save()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getPost());
            // validasi input
            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'fd_nama_lengkap' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'fd_nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[famantama_data.fd_nik,fd_id,{fd_id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'fd_nkk' => [
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
                'fd_alamat' => [
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
                'fd_shdk' => [
                    'label' => 'Status Hubungan dalam Keluarga',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jenkel' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_sta_bangteti' => [
                    'label' => 'Status Bangunan Tempat Tinggal',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_sta_lahteti' => [
                    'label' => 'Status Lahan Tempat Tinggal',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jenlai' => [
                    'label' => 'Jenis Lantai',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jendin' => [
                    'label' => 'Jenis Dinding',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'kondisi_dinding' => [
                    'label' => 'Kondisi Dinding',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jentap' => [
                    'label' => 'Jenis Atap',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'kondisi_atap' => [
                    'label' => 'Kondisi Atap',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_penghasilan' => [
                    'label' => 'Penghasilan Rata-Rata /Bulan',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_pengeluaran' => [
                    'label' => 'Pengeluaran Rata-Rata /Bulan',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jml_tanggungan' => [
                    'label' => 'Jumlah Tanggungan Keluarga',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_roda_dua' => [
                    'label' => 'Kepemilikan Kendaraan Roda 2',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_sumber_minum' => [
                    'label' => 'Sumber Air Minum',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_cara_minum' => [
                    'label' => 'Cara Memperoleh Air Minum',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_penerangan_utama' => [
                    'label' => 'Sumber Penerangan Utama',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_daya_listrik' => [
                    'label' => 'Daya Listrik Terpasang',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_bahan_masak' => [
                    'label' => 'Bahan Bakar/Energi Utama Untuk Memasak',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_tempat_bab' => [
                    'label' => 'Penggunaan Fasilitas Tempat Buang Air Besar',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jenis_kloset' => [
                    'label' => 'Jenis Kloset',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_tempat_tinja' => [
                    'label' => 'Tempat Pembuangan Akhir Tinja',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_pekerjaan_kk' => [
                    'label' => 'Pekerjaan Kepala Keluarga',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
            ]);
            if (!$valid) {

                $msg = [
                    'error' => [
                        'fd_nama_lengkap' => $validation->getError('fd_nama_lengkap'),
                        'fd_nik' => $validation->getError('fd_nik'),
                        'fd_nkk' => $validation->getError('fd_nkk'),
                        'fd_alamat' => $validation->getError('fd_alamat'),
                        'fd_rt' => $validation->getError('fd_rt'),
                        'fd_rw' => $validation->getError('fd_rw'),
                        'fd_desa' => $validation->getError('fd_desa'),
                        'fd_kecamatan' => $validation->getError('fd_kecamatan'),
                        'fd_shdk' => $validation->getError('fd_shdk'),
                        'fd_jenkel' => $validation->getError('fd_jenkel'),
                        'fd_sta_bangteti' => $validation->getError('fd_sta_bangteti'),
                        'fd_sta_lahteti' => $validation->getError('fd_sta_lahteti'),
                        'fd_jenlai' => $validation->getError('fd_jenlai'),
                        'fd_jendin' => $validation->getError('fd_jendin'),
                        'kondisi_dinding' => $validation->getError('kondisi_dinding'),
                        'fd_jentap' => $validation->getError('fd_jentap'),
                        'kondisi_atap' => $validation->getError('kondisi_atap'),
                        'fd_penghasilan' => $validation->getError('fd_penghasilan'),
                        'fd_pengeluaran' => $validation->getError('fd_pengeluaran'),
                        'fd_jml_tanggungan' => $validation->getError('fd_jml_tanggungan'),
                        'fd_roda_dua' => $validation->getError('fd_roda_dua'),
                        'fd_sumber_minum' => $validation->getError('fd_sumber_minum'),
                        'fd_cara_minum' => $validation->getError('fd_cara_minum'),
                        'fd_penerangan_utama' => $validation->getError('fd_penerangan_utama'),
                        'fd_daya_listrik' => $validation->getError('fd_daya_listrik'),
                        'fd_bahan_masak' => $validation->getError('fd_bahan_masak'),
                        'fd_tempat_bab' => $validation->getError('fd_tempat_bab'),
                        'fd_jenis_kloset' => $validation->getError('fd_jenis_kloset'),
                        'fd_tempat_tinja' => $validation->getError('fd_tempat_tinja'),
                        'fd_pekerjaan_kk' => $validation->getError('fd_pekerjaan_kk'),
                    ]
                ];
            } else {

                $kode_desa = session()->get('kode_desa');
                $namaDesa = $this->WilayahModel->getVillage($kode_desa);
                $desaNama = $namaDesa['name'];
                $buat_tanggal = date_create($this->request->getVar('fd_updated_at'));

                $data = [
                    'fd_nama_lengkap' => strtoupper($this->request->getVar('fd_nama_lengkap')),
                    'fd_nik' => $this->request->getVar('fd_nik'),
                    'fd_nkk' => $this->request->getVar('fd_nkk'),
                    'fd_alamat' => strtoupper($this->request->getVar('fd_alamat')),
                    'fd_rt' => $this->request->getVar("datart"),
                    'fd_rw' => $this->request->getVar("datarw"),
                    'fd_desa' => $this->request->getVar('fd_desa'),
                    'fd_kec' => '32.05.33',
                    'fd_kab' => '32.05',
                    'fd_prov' => '32',
                    'fd_shdk' => $this->request->getVar('fd_shdk'),
                    'fd_jenkel' => $this->request->getVar('fd_jenkel'),
                    'fd_sta_bangteti' => $this->request->getVar("fd_sta_bangteti"),
                    'fd_sta_lahteti' => $this->request->getVar("fd_sta_lahteti"),
                    'fd_jenlai' => $this->request->getVar("fd_jenlai"),
                    'fd_jendin' => $this->request->getVar("fd_jendin"),
                    'fd_kondin' => $this->request->getVar('kondisi_dinding'),
                    'fd_jentap' => strtoupper($this->request->getVar("fd_jentap")),
                    'fd_kontap' => $this->request->getVar("kondisi_atap"),
                    'fd_penghasilan' => strtoupper($this->request->getVar("fd_penghasilan")),
                    'fd_pengeluaran' => $this->request->getVar('fd_pengeluaran'),
                    'fd_jml_tanggungan' => $this->request->getVar('fd_jml_tanggungan'),
                    'fd_roda_dua' => $this->request->getVar('fd_roda_dua'),
                    'fd_sumber_minum' => $this->request->getVar('fd_sumber_minum'),
                    'fd_cara_minum' => $this->request->getVar('fd_cara_minum'),
                    'fd_penerangan_utama' => $this->request->getVar('fd_penerangan_utama'),
                    'fd_daya_listrik' => $this->request->getVar('fd_daya_listrik'),
                    'fd_bahan_masak' => $this->request->getVar('fd_bahan_masak'),
                    'fd_tempat_bab' => $this->request->getVar('fd_tempat_bab'),
                    'fd_jenis_kloset' => $this->request->getVar('fd_jenis_kloset'),
                    'fd_tempat_tinja' => $this->request->getVar('fd_tempat_tinja'),
                    'fd_pekerjaan_kk' => $this->request->getVar('fd_pekerjaan_kk'),
                    'fd_created_at_year' => date('Y'),
                    'fd_created_at_month' => date('n'),
                    'fd_created_by' => session()->get('nik'),
                    'fd_created_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                    'fd_updated_by' => session()->get('nik'),
                    'fd_updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                ];
                // dd($data);
                $this->FamantamaModel->save($data);

                $msg = [
                    'sukses' => 'Data berhasil ditambahkan',
                ];
            }
            echo json_encode($msg);


            // session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');


            // echo json_encode(array("status" => true));
            // return redirect()->to('/dtks/famantama/tables');
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function delete()
    {
        if ($this->request->isAJAX()) {


            // if (deadline_ppks() == 1) {
            //     $msg = [
            //         'informasi' => 'Mohon Maaf, Batas waktu untuk Perubahan Data, Telah Habis!!'
            //     ];
            // } else {
            $id = $this->request->getVar('id');

            $this->FamantamaModel->delete($id);

            if (deadline_ppks() === 1) {
                $msg = [
                    'data' =>
                    '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Ops...",
                                text: "Akses Tidak Sesuai!",
                                })
                        </script>'
                ];
                echo json_encode($msg);
            } else {
                $msg = [
                    'sukses' => 'Data berhasil dihapus'
                ];
                // }
                echo json_encode($msg);
            }
            // } else {
            //     $data = [
            //         'title' => 'Access denied',
            //     ];

            //     return redirect()->to('lockscreen');
        }
    }

    public function formedit()
    {
        if ($this->request->isAJAX()) {

            $this->FamantamaModel = new FamantamaModel();
            $this->WilayahModel = new WilayahModel();
            $this->BansosModel = new BansosModel();
            $this->PekerjaanModel = new PekerjaanModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->ShdkModel = new ShdkModel();
            $users = new UsersModel();

            $id = $this->request->getVar('id');
            $model = new FamantamaModel();
            $row = $model->find($id);
            // dd($id);

            $data = [
                'title' => 'Form. Edit Data',
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'data2rw' => $this->RwModel->noRw(),
                'data2rt' => $this->RtModel->noRt(),
                'users' => $users->findAll(),
                'pekerjaan' => $this->PekerjaanModel->orderBy('pk_nama')->findAll(),
                'shdk' => $this->ShdkModel->findAll(),
                'sta_bangteti' => $this->GenModel->get_sta_bangteti(),
                'sta_lahteti' => $this->GenModel->get_sta_lahteti(),
                'jenlai' => $this->GenModel->get_jenlai(),
                'jenlai' => $this->GenModel->get_jenlai(),
                'jendin' => $this->GenModel->get_jendin(),
                'jentap' => $this->GenModel->get_jentap(),
                'kondisi' => $this->GenModel->get_kondisi(),
                'penghasilan' => $this->GenModel->get_penghasilan(),
                'pengeluaran' => $this->GenModel->get_pengeluaran(),
                'jml_tanggungan' => $this->GenModel->get_jml_tanggungan(),
                'roda_dua' => $this->GenModel->get_roda_dua(),
                'sumber_minum' => $this->GenModel->get_sumber_minum(),
                'cara_minum' => $this->GenModel->get_cara_minum(),
                'penerangan_utama' => $this->GenModel->get_penerangan_utama(),
                'daya_listrik' => $this->GenModel->get_daya_listrik(),
                'bahan_masak' => $this->GenModel->get_bahan_masak(),
                'tempat_bab' => $this->GenModel->get_tempat_bab(),
                'jenis_kloset' => $this->GenModel->get_jenis_kloset(),
                'tempat_tinja' => $this->GenModel->get_tempat_tinja(),
                'jenis_pekerjaan' => $this->GenModel->get_jenis_pekerjaan(),

                'fd_id' => $row['fd_id'],
                'fd_nama_lengkap' => $row['fd_nama_lengkap'],
                'fd_nik' => $row['fd_nik'],
                'fd_nkk' => $row['fd_nkk'],
                'fd_alamat' => $row['fd_alamat'],
                'datart' => $row['fd_rt'],
                'datarw' => $row['fd_rw'],
                'fd_desa' => $row['fd_desa'],
                'fd_shdk' => $row['fd_shdk'],
                'fd_jenkel' => $row['fd_jenkel'],
                'fd_sta_bangteti' => $row['fd_sta_bangteti'],
                'fd_sta_lahteti' => $row['fd_sta_lahteti'],
                'fd_jenlai' => $row['fd_jenlai'],
                'fd_jendin' => $row['fd_jendin'],
                'kondisi_dinding' => $row['fd_kondin'],
                'fd_jentap' => $row['fd_jentap'],
                'kondisi_atap' => $row['fd_kontap'],
                'fd_penghasilan' => $row['fd_penghasilan'],
                'fd_pengeluaran' => $row['fd_pengeluaran'],
                'fd_jml_tanggungan' => $row['fd_jml_tanggungan'],
                'fd_roda_dua' => $row['fd_roda_dua'],
                'fd_sumber_minum' => $row['fd_sumber_minum'],
                'fd_cara_minum' => $row['fd_cara_minum'],
                'fd_penerangan_utama' => $row['fd_penerangan_utama'],
                'fd_daya_listrik' => $row['fd_daya_listrik'],
                'fd_bahan_masak' => $row['fd_bahan_masak'],
                'fd_tempat_bab' => $row['fd_tempat_bab'],
                'fd_jenis_kloset' => $row['fd_jenis_kloset'],
                'fd_tempat_tinja' => $row['fd_tempat_tinja'],
                'fd_pekerjaan_kk' => $row['fd_pekerjaan_kk'],
                'fd_updated_at' => $row['fd_updated_at'],
                'fd_created_by' => session()->get('nik'),
                // 'foto_rumah' => $nama_foto_rumah,
            ];
            // var_dump($data['datarw2']);
            if (deadline_ppks() === 1) {
                $msg = [
                    'data' =>
                    '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Ops...",
                                text: "Akses Tidak Sesuai!",
                                })
                        </script>'
                ];
                echo json_encode($msg);
            } else {
                $msg = [
                    'sukses' => view('dtks/data/dtks/famantama/modaledit', $data)
                ];
                echo json_encode($msg);
            }
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function formview()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());

            // if (deadline_ppks() == 1) {
            //     $msg = [
            //         'informasi' => 'Mohon Maaf, Batas waktu untuk Perubahan Data Telah Habis!!'
            //     ];
            //     echo json_encode($msg);
            // } else {
            $this->PekerjaanModel = new PekerjaanModel();
            $this->ShdkModel = new ShdkModel();
            $this->StatusKawinModel = new StatusKawinModel();
            $this->WilayahModel = new WilayahModel();
            $this->BansosModel = new BansosModel();
            $DisabilitasJenisModel = new DisabilitasJenisModel();
            $users = new UsersModel();
            $GenModel = new GenModel();

            $id = $this->request->getVar('id');
            $model = new FamantamaModel();
            $row = $model->find($id);
            // dd($id);

            $data = [
                'title' => 'Form. View Data',
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'rw' => $this->RwModel->noRw(),
                'rt' => $this->RtModel->noRt(),
                'bansos' => $this->BansosModel->findAll(),
                'users' => $users->findAll(),
                'jenkel' => $this->GenModel->getDataJenkel(),

                'id' => $row['fd_id'],
                'fd_kategori_id' => $row['fd_kategori_id'],
                'kelurahan' => $row['fd_kelurahan'],
                'datarw' => $row['fd_rw'],
                'datart' => $row['fd_rt'],
                'alamat' => $row['fd_alamat'],
                'fd_jenis_kelamin' => $row['fd_jenis_kelamin'],
                'fd_no_telp' => $row['fd_no_telp'],
                'fd_tgl_lahir' => $row['fd_tgl_lahir'],
                'fd_tempat_lahir' => $row['fd_tempat_lahir'],
                'fd_nama' => $row['fd_nama'],
                'fd_nkk' => $row['fd_nkk'],
                'fd_status_keberadaan' => $row['fd_status_keberadaan'],
                'fd_status_panti' => $row['fd_status_panti'],
                'databansos' => $row['fd_status_bantuan'],
                'fd_nik' => $row['fd_nik'],
                'fd_id' => $row['fd_id'],
                'fd_foto' => $row['fd_foto'],
                'fd_proses' => $row['fd_proses'],
                'fd_updated_at' => $row['fd_updated_at'],
                'fd_created_by' => session()->get('fd_nik'),
                // 'foto_rumah' => $nama_foto_rumah,
            ];

            $msg = [
                'sukses' => view('dtks/data/dtks/famantama/modalview', $data)
            ];
            echo json_encode($msg);
            // }
        } else {
            return redirect()->to('lockscreen');
        }
    }

    public function update()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());
            //cek nik
            $id = $this->request->getVar('fd_id');

            $buat_tanggal = date_create($this->request->getVar('fd_updated_at'));
            // $filename_dua = 'DUDFH_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
            // $filename_empat = 'DUDID_' . $this->request->getPost('nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'fd_nama_lengkap' => [
                    'label' => 'Nama Lengkap',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'fd_nik' => [
                    'label' => 'NIK',
                    'rules' => 'required|numeric|is_unique[famantama_data.fd_nik,fd_id,{fd_id}]|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
                        'is_unique' => '{field} sudah terdaftar.',
                        'min_length' => '{field} terlalu pendek',
                        'max_length' => '{field} terlalu panjang',
                    ]
                ],
                'fd_nkk' => [
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
                'fd_alamat' => [
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
                'fd_shdk' => [
                    'label' => 'Status Hubungan dalam Keluarga',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jenkel' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],                'fd_sta_bangteti' => [
                    'label' => 'Status Bangunan Tempat Tinggal',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_sta_lahteti' => [
                    'label' => 'Status Lahan Tempat Tinggal',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jenlai' => [
                    'label' => 'Jenis Lantai',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jendin' => [
                    'label' => 'Jenis Dinding',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'kondisi_dinding' => [
                    'label' => 'Kondisi Dinding',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jentap' => [
                    'label' => 'Jenis Atap',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'kondisi_atap' => [
                    'label' => 'Kondisi Atap',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_penghasilan' => [
                    'label' => 'Penghasilan Rata-Rata /Bulan',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_pengeluaran' => [
                    'label' => 'Pengeluaran Rata-Rata /Bulan',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jml_tanggungan' => [
                    'label' => 'Jumlah Tanggungan Keluarga',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_roda_dua' => [
                    'label' => 'Kepemilikan Kendaraan Roda 2',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_sumber_minum' => [
                    'label' => 'Sumber Air Minum',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_cara_minum' => [
                    'label' => 'Cara Memperoleh Air Minum',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_penerangan_utama' => [
                    'label' => 'Sumber Penerangan Utama',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_daya_listrik' => [
                    'label' => 'Daya Listrik Terpasang',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_bahan_masak' => [
                    'label' => 'Bahan Bakar/Energi Utama Untuk Memasak',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_tempat_bab' => [
                    'label' => 'Penggunaan Fasilitas Tempat Buang Air Besar',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_jenis_kloset' => [
                    'label' => 'Jenis Kloset',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_tempat_tinja' => [
                    'label' => 'Tempat Pembuangan Akhir Tinja',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'fd_pekerjaan_kk' => [
                    'label' => 'Pekerjaan Kepala Keluarga',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
            ]);
            if (!$valid) {

                $msg = [
                    'error' => [
                        'fd_nama_lengkap' => $validation->getError('fd_nama_lengkap'),
                        'fd_nik' => $validation->getError('fd_nik'),
                        'fd_nkk' => $validation->getError('fd_nkk'),
                        'fd_alamat' => $validation->getError('fd_alamat'),
                        'fd_rt' => $validation->getError('fd_rt'),
                        'fd_rw' => $validation->getError('fd_rw'),
                        'fd_desa' => $validation->getError('fd_desa'),
                        'fd_kecamatan' => $validation->getError('fd_kecamatan'),
                        'fd_shdk' => $validation->getError('fd_shdk'),
                        'fd_jenkel' => $validation->getError('fd_jenkel'),
                        'fd_sta_bangteti' => $validation->getError('fd_sta_bangteti'),
                        'fd_sta_lahteti' => $validation->getError('fd_sta_lahteti'),
                        'fd_jenlai' => $validation->getError('fd_jenlai'),
                        'fd_jendin' => $validation->getError('fd_jendin'),
                        'kondisi_dinding' => $validation->getError('kondisi_dinding'),
                        'fd_jentap' => $validation->getError('fd_jentap'),
                        'kondisi_atap' => $validation->getError('kondisi_atap'),
                        'fd_penghasilan' => $validation->getError('fd_penghasilan'),
                        'fd_pengeluaran' => $validation->getError('fd_pengeluaran'),
                        'fd_jml_tanggungan' => $validation->getError('fd_jml_tanggungan'),
                        'fd_roda_dua' => $validation->getError('fd_roda_dua'),
                        'fd_sumber_minum' => $validation->getError('fd_sumber_minum'),
                        'fd_cara_minum' => $validation->getError('fd_cara_minum'),
                        'fd_penerangan_utama' => $validation->getError('fd_penerangan_utama'),
                        'fd_daya_listrik' => $validation->getError('fd_daya_listrik'),
                        'fd_bahan_masak' => $validation->getError('fd_bahan_masak'),
                        'fd_tempat_bab' => $validation->getError('fd_tempat_bab'),
                        'fd_jenis_kloset' => $validation->getError('fd_jenis_kloset'),
                        'fd_tempat_tinja' => $validation->getError('fd_tempat_tinja'),
                        'fd_pekerjaan_kk' => $validation->getError('fd_pekerjaan_kk'),
                    ]
                ];
            } else {

                $kode_desa = session()->get('kode_desa');
                $namaDesa = $this->WilayahModel->getVillage($kode_desa);
                $desaNama = $namaDesa['name'];
                $buat_tanggal = date_create($this->request->getVar('fd_updated_at'));

                $data = [
                    'fd_nama_lengkap' => strtoupper($this->request->getVar('fd_nama_lengkap')),
                    'fd_nik' => $this->request->getVar('fd_nik'),
                    'fd_nkk' => $this->request->getVar('fd_nkk'),
                    'fd_alamat' => strtoupper($this->request->getVar('fd_alamat')),
                    'fd_rt' => $this->request->getVar('datart'),
                    'fd_rw' => $this->request->getVar('datarw'),
                    'fd_desa' => $this->request->getVar('fd_desa'),
                    'fd_kec' => '32.05.33',
                    'fd_kab' => '32.05',
                    'fd_prov' => '32',
                    'fd_shdk' => $this->request->getVar('fd_shdk'),
                    'fd_jenkel' => $this->request->getVar('fd_jenkel'),
                    'fd_sta_bangteti' => $this->request->getVar("fd_sta_bangteti"),
                    'fd_sta_lahteti' => $this->request->getVar("fd_sta_lahteti"),
                    'fd_jenlai' => $this->request->getVar("fd_jenlai"),
                    'fd_jendin' => $this->request->getVar("fd_jendin"),
                    'fd_kondin' => $this->request->getVar('kondisi_dinding'),
                    'fd_jentap' => strtoupper($this->request->getVar("fd_jentap")),
                    'fd_kontap' => $this->request->getVar("kondisi_atap"),
                    'fd_penghasilan' => strtoupper($this->request->getVar("fd_penghasilan")),
                    'fd_pengeluaran' => $this->request->getVar('fd_pengeluaran'),
                    'fd_jml_tanggungan' => $this->request->getVar('fd_jml_tanggungan'),
                    'fd_roda_dua' => $this->request->getVar('fd_roda_dua'),
                    'fd_sumber_minum' => $this->request->getVar('fd_sumber_minum'),
                    'fd_cara_minum' => $this->request->getVar('fd_cara_minum'),
                    'fd_penerangan_utama' => $this->request->getVar('fd_penerangan_utama'),
                    'fd_daya_listrik' => $this->request->getVar('fd_daya_listrik'),
                    'fd_bahan_masak' => $this->request->getVar('fd_bahan_masak'),
                    'fd_tempat_bab' => $this->request->getVar('fd_tempat_bab'),
                    'fd_jenis_kloset' => $this->request->getVar('fd_jenis_kloset'),
                    'fd_tempat_tinja' => $this->request->getVar('fd_tempat_tinja'),
                    'fd_pekerjaan_kk' => $this->request->getVar('fd_pekerjaan_kk'),
                    'fd_updated_by' => session()->get('nik'),
                    'fd_updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                ];

                $this->FamantamaModel->update($id, $data);

                $msg = [
                    'sukses' => 'Data berhasil diubah',
                ];
            }
            echo json_encode($msg);
        }
        // } else {
        //     return redirect()->to('lockscreen');
        // }
    }

    function downIden($id)
    {
        $url = 'data/usulan/foto_identitas/' . $id;

        // Use basename() function to return the base name of file
        $file_name = basename($url);

        // Use file_get_contents() function to get the file
        // from url and use file_put_contents() function to
        // save the file by using base name
        file_put_contents($file_name, file_get_contents($url));

        // $usulan = new Usulan22Model();
        // $data = $usulan->find($id);
        // return $this->response->download('data/usulan/foto_identitas/' . $data->usulan, null);
    }

    function export()
    {

        $wilayahModel = new WilayahModel();
        // $model = new Usulan22Model();
        // $tmbExpData = $this->request->getVar('btnExpData');
        // $tmbExpAll = $this->request->getVar('btnExpAll');
        $filter1 = $this->request->getPost('desa');
        // $filter4 = $this->request->getPost('bansos');
        $filter5 = $this->request->getPost('data_tahun');
        $filter6 = $this->request->getPost('data_bulan');
        // $filter7 = 0;


        $data = $this->FamantamaModel->dataExport($filter1, $filter5, $filter6)->getResultArray();
        // dd($data);

        $wilayahModel = $wilayahModel->getVillage($filter1);
        // $file_name = 'TEMPLATE_PENGUSULAN_PAKENJENG - ' . $wilayahModel['name'] . ' - ' . $filter4 . '.xlsx';
        $file_name = 'Lampiran 1 - Format Kolom Pendataan.xlsx';
        // $file_name = 'Template-PPKS-Kec.xlsx';
        require '../vendor/autoload.php';
        $spreadsheet = new Spreadsheet();

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Lengkap');
        $sheet->setCellValue('C1', 'NIK');
        $sheet->setCellValue('D1', 'KK');
        $sheet->setCellValue('E1', 'Jln/Kampung/Dusun');
        $sheet->setCellValue('F1', 'RT');
        $sheet->setCellValue('G1', 'RW');
        $sheet->setCellValue('H1', 'Desa/Kel');
        $sheet->setCellValue('I1', 'Kecamatan');
        $sheet->setCellValue('J1', 'Status Hubungan');
        $sheet->setCellValue('K1', 'Status Bangunan
Tempat Tinggal
Yang Ditempati');
        $sheet->setCellValue('L1', 'Status Lahan
Tempat Tinggal
Yang Ditempati');
        $sheet->setCellValue('M1', 'Jenis
Lantai');
        $sheet->setCellValue('N1', 'Jenis
Dinding');
        $sheet->setCellValue('O1', 'Kondisi
Dinding');
        $sheet->setCellValue('P1', 'Jenis
Atap');
        $sheet->setCellValue('Q1', 'Kondisi
Atap');
        $sheet->setCellValue('R1', 'Penghasilan
Rata-Rata/Bulan');
        $sheet->setCellValue('S1', 'Pengeluaran
Rata-Rata/bulan');
        $sheet->setCellValue('T1', 'Jumlah
Tanggungan
Keluarga');
        $sheet->setCellValue('U1', 'Kepemilikan
Kendaraan
Roda 2');
        $sheet->setCellValue('V1', 'Sumber
Air Minum');
        $sheet->setCellValue('W1', 'Cara
Memperoleh
Air Minum');
        $sheet->setCellValue('X1', 'Sumber
Penerangan
Utama');
        $sheet->setCellValue('Y1', 'Daya
Listrik
Terpasang');
        $sheet->setCellValue('Z1', 'Bahan
Bakar/Energi
Utama Untuk
Memasak');
        $sheet->setCellValue('AA1', 'Penggunaan
Fasilitas
Tempat
Buang Air
Besar');
        $sheet->setCellValue('AB1', 'Jenis
Kloset');
        $sheet->setCellValue('AC1', 'Tempat
Pembuangan
Akhir
Tinja');
        $sheet->setCellValue('AD1', 'Pekerjaan
Kepala
Keluarga');

        $styleArray = [
            // 'font' => [
            //     'bold' => true,
            //     'color' => array('rgb' => 'FFFFFF'),
            // ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'wrapText'     => TRUE,
            ],
            'borders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            // 'fill' => [
            //     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            //     // 'rotation' => 90,
            //     'startColor' => [
            //         'rgb' => '4472C4',
            //     ],
            //     'endColor' => [
            //         'rgb' => '4472C4',
            //     ],
            // ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:AD1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A1:AD1')->getAlignment()->setWrapText(true);

        // // menetapkan format tanggal pada sel H
        // $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

        $count = 2;

        foreach ($data as $row) {

            $sheet->setCellValue('A' . $count, $count - 1);
            $sheet->setCellValue('B' . $count, strtoupper($row['fd_nama_lengkap']));
            $sheet->setCellValueExplicit('C' . $count, $row['fd_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $count, $row['fd_nkk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $count, strtoupper($row['fd_alamat']));
            $sheet->setCellValue('F' . $count, strtoupper($row['fd_rt']));
            $sheet->setCellValue('G' . $count, strtoupper($row['fd_rw']));
            $sheet->setCellValue('H' . $count, strtoupper($row['namaDesa']));
            $sheet->setCellValue('I' . $count, 'PAKENJENG');
            $sheet->setCellValue('J' . $count, $row['tsf_id']);
            $sheet->setCellValue('K' . $count, $row['fd_sta_bangteti']);
            $sheet->setCellValue('L' . $count, $row['fd_sta_lahteti']);
            $sheet->setCellValue('M' . $count, $row['fd_jenlai']);
            $sheet->setCellValue('N' . $count, $row['fd_jendin']);
            $sheet->setCellValue('O' . $count, $row['fd_kondin']);
            $sheet->setCellValue('P' . $count, $row['fd_jentap']);
            $sheet->setCellValue('Q' . $count, $row['fd_kontap']);
            $sheet->setCellValue('R' . $count, $row['fd_penghasilan']);
            $sheet->setCellValue('S' . $count, $row['fd_pengeluaran']);
            $sheet->setCellValue('T' . $count, $row['fd_jml_tanggungan']);
            $sheet->setCellValue('U' . $count, $row['fd_roda_dua']);
            $sheet->setCellValue('V' . $count, $row['fd_sumber_minum']);
            $sheet->setCellValue('W' . $count, $row['fd_cara_minum']);
            $sheet->setCellValue('X' . $count, $row['fd_penerangan_utama']);
            $sheet->setCellValue('Y' . $count, $row['fd_daya_listrik']);
            $sheet->setCellValue('Z' . $count, $row['fd_bahan_masak']);
            $sheet->setCellValue('AA' . $count, $row['fd_tempat_bab']);
            $sheet->setCellValue('AB' . $count, $row['fd_jenis_kloset']);
            $sheet->setCellValue('AC' . $count, $row['fd_tempat_tinja']);
            $sheet->setCellValue('AD' . $count, $row['fpp_id']);

            $count++;
        }

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Sheet1');

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

        $wilayahModel = new WilayahModel();
        $user_login = $this->AuthModel->getUserId();
        // dd($user_login);
        if (!isset($user_login['lp_sekretariat']) && !isset($user_login['user_lembaga_id'])) {
            $str = '<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
               <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
               <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
               <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.1/js/bootstrap-dialog.min.js"></script>
               <script type="text/javascript">
                   setTimeout(function() { 
                      BootstrapDialog.alert(\'Silahkan isi profil Anda dan Lembaga terlebih dahulu!!\') 
                      window.location.href = \'/profil_user\';
                    },10000);
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
            $bulanUpper = strtoupper($bulan[$kode_bulan]);

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
                'bulanUpper' => strtoupper($bulan[$kode_bulan]),
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
            'csv_ket' => $this->CsvReportModel->getCsvKet(),
        ];
        // dd($data['csv_ket']);
        return view('dtks/data/dtks/famantama/impor_csv', $data);
    }

    public function tbCsv()
    {
        $this->CsvReportModel = new CsvReportModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('namaFile');
        // $filter5 = '';
        // $filter6 = '';
        $filter5 = $this->request->getPost('data_tahun');
        $filter6 = $this->request->getPost('data_bulan');

        $listing = $this->CsvReportModel->getDataTabel($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);
        $jumlah_semua = $this->CsvReportModel->semua();
        $jumlah_filter = $this->CsvReportModel->filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->du_nik;
            $row[] = $key->nama;
            $row[] = $key->cr_nama_lgkp;
            $row[] = $key->nokk;
            $row[] = $key->alamat;
            $row[] = $key->rt;
            $row[] = $key->rw;
            $row[] = $key->cr_nama_desa;
            $row[] = $key->kelurahan;
            $row[] = $key->cr_nama_kec;
            $row[] = $key->kecamatan;
            // $row[] = $key->program_bansos;
            $row[] = $key->cr_program_bansos;
            $row[] = $key->cr_hasil;
            $row[] = $key->cr_padan;
            $row[] = $key->cr_ket_vali;
            $row[] = $key->cr_ck_id;
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

    public function importCsvToDb()
    {
        $input = $this->validate([
            'file' => 'uploaded[file]|max_size[file,2048]|ext_in[file,csv]'
        ]);
        $namaFile = $this->request->getPost('ck_id');

        // dd($namaFile);

        if (!$input && ($namaFile = '' || $namaFile == null)) {
            $data = [
                'namaApp' => 'Opr NewDTKS',
                'title' => 'Import CSV Report',
                'user_login' => $this->AuthModel->getUserId(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'bansos' => $this->BansosModel->findAll(),
                'statusRole' => $this->GenModel->getStatusRole(),
                'session' => session()->get(),
                'csv_ket' => $this->CsvReportModel->getCsvKet(),
                'validation' => $this->validator,
            ];
            // dd($data['validation']);
            // $data['validation'] = $this->validator;
            return view('dtks/data/dtks/famantama/impor_csv', $data);
        } else {
            if ($file = $this->request->getFile('file')) {
                if ($file->isValid() && !$file->hasMoved()) {

                    // Get random file name
                    $newName = $file->getRandomName();

                    // Store file in public/csvfile/ folder
                    $file->move('../public/data/csvfile', $newName);

                    // Reading file
                    $file = fopen("../public/data/csvfile/" . $newName, "r");
                    $i = 0;
                    $numberOfFields = 8; // Total number of fields

                    $csvArr = array();

                    // Initialize $importData_arr Array
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);
                        // Skip first row & check number of fields
                        if ($i > 0 && $num == $numberOfFields) {

                            // $namaFile = $this->request->getPost('ck_id');
                            // Key names are the insert table field names - name, email, city, and status
                            $csvArr[$i]['cr_nama_kec'] = $filedata[0];
                            $csvArr[$i]['cr_nama_desa'] = $filedata[1];
                            $csvArr[$i]['cr_nik_usulan'] = $filedata[2];
                            $csvArr[$i]['cr_program_bansos'] = $filedata[3];
                            $csvArr[$i]['cr_hasil'] = $filedata[4];
                            $csvArr[$i]['cr_padan'] = $filedata[5];
                            $csvArr[$i]['cr_nama_lgkp'] = $filedata[6];
                            $csvArr[$i]['cr_ket_vali'] = $filedata[7];
                            $csvArr[$i]['cr_ck_id'] = $namaFile;
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
}
