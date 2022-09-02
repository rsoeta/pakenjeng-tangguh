<?php

namespace App\Controllers\Dtks;

use App\Controllers\BaseController;
use App\Models\Dtks\BansosModel;
use App\Models\Dtks\DtksModel;
use App\Models\Dtks\Usulan22Model;
use App\Models\Dtks\UsersModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\GenModel;
use App\Models\Dtks\YatimModel;
use App\Models\Dtks\JandaModel;
use App\Models\Dtks\VeriVali09Model;
use App\Models\Dtks\Usulan21Model;
use App\Models\Dtks\VervalPbiModel;
use App\Models\Dtks\KipModel;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\BnbaModel;



class Pages extends BaseController
{
    public function __construct()
    {
        $this->BansosModel = new BansosModel();
        $this->GenModel = new GenModel();
        $this->KipModel = new KipModel();
        $this->Rw = new RwModel();
        $this->UsersModel = new UsersModel();
        $this->usulan21 = new Usulan21Model();
        $this->Usulan22Model = new Usulan22Model();
        $this->verivali09 = new VeriVali09Model();
        $this->VervalPbiModel = new VervalPbiModel();
        $this->WilayahModel = new WilayahModel();
        $this->AuthModel = new AuthModel();
        $this->BnbaModel = new BnbaModel();
    }

    public function home()
    {
        $data = [
            'title' => 'Login',
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_login' => $this->AuthModel->getUserId(),

        ];

        return view('dtks/auth/login', $data);
    }

    public function index()
    {

        $BansosModel = new BansosModel();
        $DesaModel = new WilayahModel();

        $YatimModel = new YatimModel();
        $yatim = $YatimModel->jmlYatim();

        $JandaModel = new JandaModel();
        $janda = $JandaModel->jmlJanda();

        $Usulan22Model = new Usulan22Model();
        $rekapUsulan = $Usulan22Model->rekapUsulan();


        // dd($yatim);
        // $yatims[];

        foreach ($yatim as $row) {
            $yatims[] = array(
                'Rw' => $row->Rw,
                'jml' => $row->jml,
            );
        }

        foreach ($janda as $row) {
            $jandas[] = array(
                'RW' => $row->RW,
                'jml' => $row->jml,
            );
        }

        $jbt = (session()->get('level'));

        $desa = $DesaModel->findAll();
        foreach ($desa as $row) {
        }

        $capaianAll = 0;
        foreach ($rekapUsulan as $row) {
            $capaianAll += $row->Capaian;
        }

        $data = [
            'title' => 'Dashboard',
            // 'desa' => $row['id'],
            'bansos' => $BansosModel->getBansos(),
            'dtks_status' => $this->GenModel->getStatusDtks(),
            'Rw' => $jbt,
            'jmlRecord' => $this->verivali09->jumlah_semua(),
            'getDataGrup' => $this->verivali09->getDataGroupBy(),
            'rekapUsulan' => $this->Usulan22Model->rekapUsulan(),
            'jumlah_semua_usulan' => $this->Usulan22Model->jumlah_semua(),
            'rekapPbi' => $this->VervalPbiModel->jumlah_semua(),
            'rincianHasil' => $this->VervalPbiModel->jml_grup(),
            'rincianDesa' => $this->VervalPbiModel->jml_perdesa(),
            'jml_persentase' => $this->VervalPbiModel->jml_persentase(),
            'jmlPerbaikan' => $this->VervalPbiModel->perbaikanAll(),
            'percentages' => $this->VervalPbiModel->jml_persentase(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'user_login' => $this->AuthModel->getUserId(),
            'countStatusBnba' => $this->BnbaModel->countStatusBnba(),
            'capaianAll' => $capaianAll,
        ];
        if (session()->get('status') == 1 && session()->get('role_id') <= 4) {
            // dd($data['capaianAll']);
            return view('dashboard', $data);
            // return view('dash', $data);
        } elseif (session()->get('status') == 1 && session()->get('role_id') == 5) {

            $data = [
                'title' => 'Data Kartu Indonesia Pintar',
                'statusRole' => $this->GenModel->getStatusRole(),
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'nama_sekolah' => $this->UsersModel->getSchool()->getResultArray(),
                'jenjang_sekolah' => $this->GenModel->getSekolahJenjang()->getResultArray(),
                'kelas_sekolah' => $this->KipModel->getKelas()->getResultArray(),
            ];
            return view('dtks/data/kip/home', $data);
        }
    }

    public function tables()
    {
        if (session()->get('level') == 1) {
            $model = new DtksModel();
            $data = [
                'title' => 'Data Terpadu Kesejahteraan Sosial',
                'dtks' => $model->getDtks()
            ];
            return view('dtks/data/tables', $data);
        } else if (session()->get('level') == 2) {
            $model = new DtksModel();
            $data = [
                'title' => 'Data Terpadu Kesejahteraan Sosial',
                'dtks' => $model->getData()
            ];
            return view('dtks/data/tables', $data);
        }
        return view('lockscreen');
    }

    public function process()
    {
        $users = new DtksModel();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $dataUser = $users->where([
            'email' => $email,
        ])->first();
        if ($dataUser) {
            if (password_verify($password, $dataUser->password)) {
                session()->set([
                    'email' => $dataUser->email,
                    'name' => $dataUser->name,
                    'logged_in' => TRUE
                ]);
                return redirect()->to(base_url('verivali/dtks/home'));
            } else {
                session()->setFlashdata('error', 'Email & Password Salah');
                return redirect()->back();
            }
        } else {
            session()->setFlashdata('error', 'Email & Password Salah');
            return redirect()->back();
        }
    }

    public function detail($id)
    {
        $model = new DtksModel();

        $data = [
            'title' => 'Detail Keluarga Penerima Manfaat',
            'dtks' => $model->getIdDtks($id)
        ];
        return view('dtks/data/detail', $data);
    }

    public function status()
    {
        $tombolCari = $this->request->getPost('tombolcari');

        if (isset($tombolCari)) {
            $cari = $this->request->getPost('caristatus');
            session()->set('caristatus', $cari);
            redirect()->to('/dtks/properti/status');
        } else {
            $cari = session()->get('caristatus');
        }

        $dataStatus = $cari ? $this->Status->cariData($cari) : $this->Status;

        $noHalaman = $this->request->getVar('page_paging_data') ?  $this->request->getVar('page_paging_data') : 1;
        $data = [
            'title' => 'Status Penerima Manfaat',
            'status' => $dataStatus->paginate(5, 'paging_data'),
            'pager' => $this->Status->pager,
            'nohalaman' => $noHalaman,
            'cari' => $cari
        ];
        return view('dtks/properti/status', $data);
    }

    public function keterangan()
    {
        $tombolCari = $this->request->getPost('tombolcari');

        if (isset($tombolCari)) {
            $cari = $this->request->getPost('cariketerangan');
            session()->set('cariketerangan', $cari);
            redirect()->to('/dtks/properti/keterangan');
        } else {
            $cari = session()->get('cariketerangan');
        }

        $dataKeterangan = $cari ? $this->keterangan->cariData($cari) : $this->keterangan;

        $noHalaman = $this->request->getVar('page_paging_data') ?  $this->request->getVar('page_paging_data') : 1;
        $data = [
            'title' => 'Keterangan Penerima Manfaat',
            'keterangan' => $dataKeterangan->paginate(5, 'paging_data'),
            'pager' => $this->keterangan->pager,
            'nohalaman' => $noHalaman,
            'cari' => $cari
        ];
        return view('dtks/properti/keterangan', $data);
    }

    function formTambahKeterangan()
    {
        if ($this->request->isAJAX()) {
            $msg = [
                'data' => view('dtks/properti/modalformtambahket')
            ];
            echo json_encode($msg);
        } else {
            exit('Maaf akses tidak diizinkan!');
        }
    }

    function formTambahStatus()
    {
        if ($this->request->isAJAX()) {
            $msg = [
                'data' => view('dtks/properti/modalformtambahstat')
            ];
            echo json_encode($msg);
        } else {
            exit('Maaf akses tidak diizinkan!');
        }
    }

    public function simpandata()
    {
        if ($this->request->isAJAX()) {
            $jenisketerangan = $this->request->getVar('jenisketerangan');

            $this->keterangan->insert([
                'jenis_keterangan' => $jenisketerangan
            ]);

            $msg = [
                'sukses' => 'Keterangan berhasil ditambahkan'
            ];
            echo json_encode($msg);
        }
    }

    public function simpandatastatus()
    {
        if ($this->request->isAJAX()) {
            $jenisstatus = $this->request->getVar('jenisstatus');

            $this->Status->insert([
                'jenis_status' => $jenisstatus
            ]);

            $msg = [
                'sukses' => 'Status berhasil ditambahkan'
            ];
            echo json_encode($msg);
        }
    }

    function hapus()
    {
        if ($this->request->isAJAX()) {
            $idket = $this->request->getVar('idket');

            $this->keterangan->delete($idket);

            $msg = [
                'sukses' => 'Keterangan berhasil dihapus'
            ];
            echo json_encode($msg);
        }
    }

    function hapusstatus()
    {
        if ($this->request->isAJAX()) {
            $idstatus = $this->request->getVar('idstatus');

            $this->Status->delete($idstatus);

            $msg = [
                'sukses' => 'Status berhasil dihapus'
            ];
            echo json_encode($msg);
        }
    }

    function formEdit()
    {
        if ($this->request->isAJAX()) {
            $idket =  $this->request->getVar('idket');

            $ambildataketerangan = $this->keterangan->find($idket);
            $data = [
                'idket' => $idket,
                'jenis_keterangan' => $ambildataketerangan['jenis_keterangan']
            ];

            $msg = [
                'data' => view('dtks/properti/modalformedit', $data)
            ];
            echo json_encode($msg);
        }
    }

    function formEditStatus()
    {
        if ($this->request->isAJAX()) {
            $idstatus =  $this->request->getVar('idstatus');

            $ambildatastatus = $this->Status->find($idstatus);
            $data = [
                'idstatus' => $idstatus,
                'jenis_status' => $ambildatastatus['jenis_status']
            ];

            $msg = [
                'data' => view('dtks/properti/modalformeditstat', $data)
            ];
            echo json_encode($msg);
        }
    }

    function updatedata()
    {
        if ($this->request->isAJAX()) {
            $idket = $this->request->getVar('idket');
            $jenis_keterangan = $this->request->getVar('jenis_keterangan');

            $this->keterangan->update($idket, [
                'jenis_keterangan' => $jenis_keterangan
            ]);

            $msg = [
                'sukses' =>  'Data Keterangan berhasil diupdate'
            ];
            echo json_encode($msg);
        }
    }

    function updatedatastat()
    {
        if ($this->request->isAJAX()) {
            $idstatus = $this->request->getVar('idstatus');
            $jenis_status = $this->request->getVar('jenis_status');

            $this->Status->update($idstatus, [
                'jenis_status' => $jenis_status
            ]);

            $msg = [
                'sukses' =>  'Data Status berhasil diupdate'
            ];
            echo json_encode($msg);
        }
    }
}
