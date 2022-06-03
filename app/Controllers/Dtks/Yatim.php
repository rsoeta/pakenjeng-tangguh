<?php

namespace App\Controllers\Dtks;


use CodeIgniter\Controller;
use App\Controllers\BaseController;
use App\Models\Dtks\YatimModel;
use App\Models\DesaModel;
use App\Models\RwModel;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\BansosModel;

class Yatim extends BaseController
{
    public $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['form']);
    }

    public function listYatim()
    {
        $desKels = new DesaModel();
        $Rw = new RwModel();
        $operator = new AuthModel();
        $bansos = new BansosModel();

        $data = [
            'title' => 'Daftar Anak Yatim',
            'desKels' => $desKels->orderBy('nama_desa', 'asc')->findAll(),
            'Rw' => $Rw->orderBy('no_rw', 'asc')->findAll(),
            'operator' => $operator->orderBy('fullname', 'asc')->findAll(),
            'bansos' => $bansos->findAll(),
        ];

        return view('dtks/data/yatim/index', $data);
    }

    public function yatim_data()
    {
        $model = new YatimModel();

        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $desa = $this->request->getPost('desa');
        $rw = $this->request->getPost('rw');
        $operator = $this->request->getPost('operator');
        $keterangan = $this->request->getPost('keterangan');

        $listing = $model->get_datatables($desa, $rw, $operator, $keterangan);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($desa, $rw, $operator, $keterangan);
        // $KetMasalah = $KetMasalah->orderBy('IDKeterangan', 'asc')->findAll();
        // // dd($listing);
        // foreach ($KetMasalah as $row) {
        // 	$IDKeterangan = $row['IDKeterangan'];
        // 	$NamaKeterangan = $row['NamaKeterangan'];
        // }
        # code...


        // <span class="badge bg-secondary">Secondary</span>
        // <span class="badge bg-success">Success</span>
        // <span class="badge bg-danger">Danger</span>
        // <span class="badge bg-warning text-dark">Warning</span>
        // <span class="badge bg-info text-dark">Info</span>
        // <span class="badge bg-light text-dark">Light</span>
        // <span class="badge bg-dark">Dark</span>';

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            // $row[] = $key->NoKK;
            $row[] = $key->NamaAnak;
            // $row[] = $key->NoKK;
            $row[] = $key->TmpLahirAnak;
            $row[] = $key->TglLahirAnak;
            $row[] = $key->NamaJenKel;
            $row[] = $key->NamaWali;
            $row[] = $key->NoTlpWali;
            $row[] = $key->AlamatAnak;
            $row[] = $key->Rt;
            $row[] = $key->Rw;
            $row[] = $key->nama_desa;

            // $badges = $key->Keterangan;
            // if ($badges == 1) {
            //     $badges = '<span class="badge bg-success" selected>Clear</span>';
            // } elseif ($badges == 2) {
            //     $badges = '<span class="badge bg-danger" selected>Duplikat NIK</span>';
            // } elseif ($badges == 3) {
            //     $badges = '<span class="badge bg-danger" selected>Invalid NIK</span>';
            // } elseif ($badges == 4) {
            //     $badges = '<span class="badge bg-warning" selected>Kond. Pekerjaan NULL</span>';
            // } elseif ($badges == 5) {
            //     $badges = '<span class="badge bg-warning" selected>Pend. Tertinggi NULL</span>';
            // } elseif ($badges == 6) {
            //     $badges = '<span class="badge bg-warning" selected>Invalid RT/RW</span>';
            // }

            // $row[] = $badges;
            // $row[] = $key->NamaPendidikan;
            // $row[] = "<button class='btn btn-lg' onclick='delet('" . $key->ID . "')'>
            //                                             <i class='fa fa-trash-alt'></i>
            //                                         </button>";
            $row[] = "<button type=\"button\" class=\"btn btn-sm\" onclick=\"window.location='formview/" . $key->ID . "'\"><i class='fa fa-pencil-alt'></i></button> <button class='btn btn-sm' data-id='" . $key->ID . "' id='deleteBtn'><i class='fa fa-trash-alt'></i></button>";
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

    public function index()
    {
        $status = session()->get('status');
        $level = session()->get('level');
        $denied = $status == 0 && $level > 2;
        $user = $status == 1 && $level > 1;
        $admin = $status == 1 && $level >= 1;

        
        if ($denied) {
            $data = [
                'title' => 'Access denied',
            ];
            
            return view('lockscreen', $data);
        } else if ($user || $admin) {
            
            $YatimModel = new YatimModel;
            $data = [
                'title2' => 'Daftar Anak Yatim',
                'yatim' => $YatimModel->orderBy('NamaAnak', 'asc')->findAll(),
            ];
            // return view('dtks/data/yatim/index');
            return view('dtks/data/yatim/index', $data);
        }
    }
}
