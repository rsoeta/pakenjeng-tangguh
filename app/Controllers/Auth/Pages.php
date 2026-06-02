<?php

namespace App\Controllers\Auth;

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
use App\Models\Dtsen\DtsenKkModel;
use App\Models\Dtsen\DtsenDraftModel;
use App\Models\Dtsen\DtsenSeModel;
use App\Models\Dtsen\DtsenUsulanBansosModel;



class Pages extends BaseController
{
    protected $BansosModel;
    protected $GenModel;
    protected $KipModel;
    protected $Rw;
    protected $UsersModel;
    protected $usulan21;
    protected $Usulan22Model;
    protected $verivali09;
    protected $VervalPbiModel;
    protected $WilayahModel;
    protected $AuthModel;
    protected $BnbaModel;
    protected $DtsenKkModel;
    protected $DtsenDraftModel;
    protected $DtsenSeModel;
    protected $DtsenUsulanBansosModel;

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
        $this->DtsenKkModel = new DtsenKkModel();
        $this->DtsenDraftModel = new DtsenDraftModel();
        $this->DtsenSeModel = new DtsenSeModel();
        $this->DtsenUsulanBansosModel = new DtsenUsulanBansosModel();
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
        $session = session();
        $kodeDesa     = $session->get('kode_desa');
        $roleId       = $session->get('role_id');
        $rwUser       = $session->get('level');
        $filterRW     = $this->request->getPost('filterRW') ?? null;
        $wilayahTugas = $session->get('wilayah_tugas');
        $userId       = $session->get('id'); // Diperlukan untuk pelacakan tugas spesifik user

        $db = \Config\Database::connect();

        $filter = [
            'kode_desa'     => $kodeDesa,
            'rw'            => ($roleId >= 4 ? $rwUser : null),
            'wilayah_tugas' => $wilayahTugas,
        ];

        // 🔹 Total Keluarga (BNBA)
        $totalKK = $this->DtsenKkModel->countVerifiedByUser($roleId, $filter);

        // 🔹 Total Draft (status = 'draft')
        $totalDraft = $this->DtsenDraftModel->countDraftByUser($roleId, $filter);

        // 🔹 Total Submitted (menggunakan query submitted builder)
        $totalSubmitted = $this->DtsenDraftModel->countSubmittedByUser($roleId, $filter);

        // 🔹 Data Desil (kategori kesejahteraan)
        $dataDesil = $this->DtsenSeModel->getDesilByRole($roleId, $filter);

        // 🔹 Total Usulan Bansos Bulan Ini
        $totalUsulan = $this->DtsenUsulanBansosModel->countUsulanBansosBulanIni($roleId, $filter);

        // =======================================================
        // 🚀 CEK PEMULIHAN DATA WILAYAH RT/RW
        // =======================================================
        $menuPemulihan = $db->table('tb_menu')
            ->where('tm_url', 'pembaruan-keluarga/pemulihan')
            ->where('tm_status', 1)
            ->get()
            ->getRowArray();

        $jumlahBermasalah = 0;

        if ($menuPemulihan) {
            $jumlahBermasalah = $db->table('dtsen_kk k')
                ->join('dtsen_rt r', 'k.id_rt = r.id_rt', 'left')
                ->where('k.deleted_at IS NULL')
                ->groupStart()
                ->where('r.rt', null)
                ->orWhere('r.rt', '')
                ->orWhere('r.rw', null)
                ->orWhere('r.rw', '')
                ->orWhere('LENGTH(r.rt) <', 3)
                ->orWhere('LENGTH(r.rw) <', 3)
                ->groupEnd()
                ->countAllResults();
        }

        // =======================================================
        // 🚀 CEK TUGAS ANOMALI (KHUSUS PETUGAS ENTRI / ROLE 4)
        // =======================================================
        $total_anomali_tugas = 0;
        if ($roleId == 4) {
            $total_anomali_tugas = $db->table('dtsen_anomali')
                ->where('petugas_entri_id', $userId)
                ->groupStart()
                ->where('status_anomali', 'open')
                ->orWhere('status_anomali', 'rejected') // Termasuk data yang ditolak Operator
                ->groupEnd()
                ->countAllResults();
        }

        // 🔹 Deadline (optional)
        $deadline = $this->GenModel->getDeadline();
        $dd_waktu_start = null;
        $dd_waktu_end = null;
        if (!empty($deadline)) {
            foreach ($deadline as $d) {
                $dd_waktu_start = date_create($d['dd_waktu_start']);
                $dd_waktu_end = date_create($d['dd_waktu_end']);
            }
        }

        // =======================================================
        // 🚀 AMBIL MENU PRIORITAS UNTUK DASHBOARD (DENGAN PARENT)
        // =======================================================
        $menuPrioritas = $db->table('tb_menu m')
            ->select('m.*, p.tm_nama as parent_nama')
            ->join('tb_menu p', 'm.tm_parent_id = p.tm_id', 'left')
            ->where('m.tm_status', 1)
            ->where('m.tm_is_dashboard', 1)
            ->orderBy('m.tm_urutan', 'ASC')
            ->get()
            ->getResultArray();

        // 🔹 Siapkan Data ke View
        $data = [
            'title'               => 'Dashboard',
            'totalKK'             => $totalKK,
            'totalUsulan'         => $totalUsulan,
            'totalDraft'          => $totalDraft,
            'totalSubmitted'      => $totalSubmitted,
            'dataDesil'           => $dataDesil,
            'dd_waktu_start'      => $dd_waktu_start,
            'dd_waktu_end'        => $dd_waktu_end,
            'user_login'          => $this->AuthModel->getUserId(),
            'menu_pemulihan'      => $menuPemulihan,
            'total_masalah'       => $jumlahBermasalah,
            'total_anomali_tugas' => $total_anomali_tugas, // 🚀 Variabel Anomali dimasukkan ke View
            'menu_prioritas'      => $menuPrioritas
        ];

        // ✅ Set Flashdata untuk notifikasi login
        if (!session()->getFlashdata('login_success')) {
            session()->setFlashdata('login_success', true);
        }

        // 🚀 BUG FIX: Hapus limitasi lama, izinkan Role <= 5 mengakses Dashboard Utama
        if (session()->get('status') == 1 && $roleId <= 5) {
            return view('dashboard', $data);
        }

        // 🛡️ Fallback pengaman jika sesi aneh atau role > 5 mencoba akses
        return redirect()->to(base_url('home'))->with('error', 'Akses ditolak atau sesi tidak valid.');
    }

    public function getNilaiJumlah()
    {
        $rekapUsulan = $this->Usulan22Model->rekapUsulan();
        $rekapUsulanArray = json_decode(json_encode($rekapUsulan), true);

        foreach ($rekapUsulanArray as &$item) {
            // Tambahkan 'id' ke setiap elemen array
            $item['id'] = $item['namaDesa'];
        }

        return $this->response->setJSON($rekapUsulanArray);
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
