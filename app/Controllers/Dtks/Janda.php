<?php

namespace App\Controllers\Dtks;


use CodeIgniter\Controller;
use App\Controllers\BaseController;
use App\Models\DesaModel;
use App\Models\RwModel;
use App\Models\RtModel;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\JandaModel;

class Janda extends BaseController
{
    public function __construct()
    {
        helper(['form']);
    }

    public function index()
    {
        $status = session()->get('status');
        $level = session()->get('level');
        $denied = $status == 0 && $level > 2;
        $user = $status == 1 && $level > 1;
        $admin = $status == 1 && $level >= 1;

        $desKels = new DesaModel();
        $Rw = new RwModel();
        $operator = new AuthModel();

        if ($denied) {
            $data = [
                'title' => 'Access denied',
            ];

            return view('lockscreen', $data);
        } else if ($user || $admin) {

            $jandaModel = new JandaModel;
            $data = [
                'title' => 'Daftar Janda',
                'title2' => 'Daftar Anak Yatim',
                'desKels' => $desKels->orderBy('nama_desa', 'asc')->findAll(),
                'Rw' => $Rw->orderBy('no_rw', 'asc')->findAll(),
                'operator' => $operator->orderBy('fullname', 'asc')->findAll(),
                'janda' => $jandaModel->orderBy('NAMA', 'asc')->findAll(),
            ];
            // return view('dtks/data/yatim/index');
            return view('dtks/data/janda/index', $data);
        }
    }

    public function janda_data()
    {
        $model = new JandaModel();

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
            $row[] = $key->NAMA;
            $row[] = $key->NIK;
            $row[] = $key->NO_KK;
            // $row[] = $key->NoKK;
            $row[] = $key->TEMPAT_LAHIR;
            $row[] = $key->TANGGAL_LAHIR;
            $row[] = $key->ALAMAT;
            $row[] = $key->RT;
            $row[] = $key->RW;
            $row[] = $key->DESA;

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

    public function tambah()
    {
        if ($this->request->isAJAX()) {

            $rws = new RwModel();
            $operator = new AuthModel();
            // $rts = new RtModel();
            $rws = $rws->orderBy('no_rw', 'asc')->findAll();
            $operator = $operator->orderBy('fullname', 'asc')->findAll();

            $data = [
                'rws' => $rws,
                'operator' => $operator,
            ];

            $msg = [
                'data' => view('dtks/data/janda/modaltambah', $data),
            ];
            // dd($msg);

            echo json_encode($msg);
        } else {
            exit('lockscreen');
        }
    }

    function action()
    {
        if ($this->request->getVar('action')) {
            $action = $this->request->getVar('action');

            if ($action == 'get_rt') {
                $RtModel = new RtModel();

                $Rtdata = $RtModel->where('id_rw', $this->request->getVar('no_rw'))->findAll();

                echo json_encode($Rtdata);
            }
        }
    }
}
