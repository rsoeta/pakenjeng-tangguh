<?php

namespace App\Controllers\Dtks;

use App\Controllers\BaseController;

use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;
use CodeIgniter\Config\Services;
// use Config\Services;

class Wil extends BaseController
{
    public function __construct()
    {
        $this->db = db_connect();
        $this->rw = new RwModel();
        $this->rt = new RtModel();
    }

    public function index()
    {
        $wilayah = new WilayahModel();
        $data = [
            'title' => 'Data Wilayah',
            // 'wilayah' => $wilayah->findAll(),
            'provinsi' => $wilayah->getProv(),
        ];

        return view('wil/index', $data);
    }

    public function tabel_data()
    {

        $model = new WilayahModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('namaProv');
        $filter2 = $this->request->getPost('namaKab');
        $filter3 = $this->request->getPost('namaKec');
        $filter4 = $this->request->getPost('namaDesa');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->namaProv;
            $row[] = $key->namaKab;
            $row[] = $key->namaKec;
            $row[] = $key->namaDesa;
            $row[] = "<button type=\"button\" class=\"btn btn-sm\" onclick=\"window.location='formview/" . $key->idDesa . "'\"><i class='fa fa-pencil-alt'></i></button> | 
			<button class='btn btn-sm' data-id='" . $key->idDesa . "' id='deleteBtn'><i class='fa fa-trash-alt'></i></button>";
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

    public function getKab()
    {
        $province_id = $this->request->getVar("province_id");

        $datakab = $this->db->query("SELECT id, name from tb_regencies WHERE province_id = " . $province_id . " ORDER BY name")->getResultArray();

        return json_encode($datakab);
    }

    public function getKec()
    {
        $regency_id = $this->request->getVar("regency_id");

        $datakec = $this->db->query("SELECT id, name from tb_districts WHERE regency_id = " . $regency_id . " ORDER BY name")->getResultArray();

        return json_encode($datakec);
    }

    public function getDesa()
    {
        $model = new WilayahModel();
        $district_id = $this->request->getVar("district_id");
        // $desadata = $this->db->query("SELECT id, name from tb_villages WHERE district_id = " . $district_id)->getResultArray();
        $desadata = $model->getDesa($district_id);


        return json_encode($desadata);
    }

    function action()
    {
        if ($this->request->getVar('action')) {
            $action = $this->request->getVar('action');
            $desa = $this->request->getVar('desa');
            $no_rw = $this->request->getVar('no_rw');

            if ($action == 'get_rt') {
                $RtModel = new RtModel();

                $Rtdata = $RtModel->getDataRT($desa, $no_rw);

                echo json_encode($Rtdata);
            }
        }
    }

    public function dsn()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        $tombolCari = $this->request->getpost('tomboldusun');
        if (isset($tombolCari)) {
            $cari = $this->request->getpost('caridusun');
            session()->set('caridusun', $cari);
            redirect()->to('dtks/wil/dsn');
        } else {
            $cari = session()->get('caridusun');
        }
        $datadusun = $cari ? $this->dusun->cariData($cari) : $this->dusun;
        $noHalaman = $this->request->getVar('page_paging') ? $this->request->getVar('page_paging') : 1;
        $data = [
            'title' => 'Wilayah Dusun',
            // 'dusun' => $this->dusunModel->getDusun()
            'datadusun' => $datadusun->paginate(10, 'paging'),
            'pager' => $this->dusun->pager,
            'noHalaman' => $noHalaman,
            'cari' => $cari
        ];
        return view('dtks/wil/dsn', $data);
    }

    public function rw()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        $tombolCari = $this->request->getpost('tombolrw');
        if (isset($tombolCari)) {
            $cari = $this->request->getpost('carirw');
            session()->set('carirw', $cari);
            redirect()->to('dtks/wil/rw');
        } else {
            $cari = session()->get('carirw');
        }
        $datarw = $cari ? $this->rw->cariData($cari) : $this->rw;

        $noHalaman = $this->request->getVar('page_paging') ? $this->request->getVar('page_paging') : 1;
        $data = [
            'title' => 'Wilayah RW',
            // 'rw' => $this->rwModel->getrw()
            'datarw' => $datarw->paginate(10, 'paging'),
            'pager' => $this->rw->pager,
            'noHalaman' => $noHalaman,
            'cari' => $cari
        ];
        return view('dtks/wil/rw', $data);
    }
    public function rt()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        $tombolCari = $this->request->getpost('tombolrt');
        if (isset($tombolCari)) {
            $cari = $this->request->getpost('carirt');
            session()->set('carirt', $cari);
            redirect()->to('dtks/wil/rt');
        } else {
            $cari = session()->get('carirt');
        }
        $datart = $cari ? $this->rt->cariData($cari) : $this->rt;

        $noHalaman = $this->request->getVar('page_paging') ? $this->request->getVar('page_paging') : 1;
        $data = [
            'title' => 'Wilayah RT',
            // 'rt' => $this->rtModel->getrt()
            'datart' => $datart->paginate(10, 'paging'),
            'pager' => $this->rt->pager,
            'noHalaman' => $noHalaman,
            'cari' => $cari
        ];
        return view('dtks/wil/rt', $data);
    }

    function formtambahdusun()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if ($this->request->isAJAX()) {
            $msg = [
                'data' => view('wilayah/modalformtambah')
            ];
            echo json_encode($msg);
        } else {
            exit('Maaf tidak ada halaman yang bisa ditampilkan');
        }
    }

    function formTambahRw()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if ($this->request->isAJAX()) {
            $msg = [
                'data' => view('wilayah/modalformtambahrw')
            ];
            echo json_encode($msg);
        } else {
            exit('Maaf tidak ada halaman yang bisa ditampilkan');
        }
    }

    function formTambahrt()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if ($this->request->isAJAX()) {
            $msg = [
                'data' => view('wilayah/modalformtambahrt')
            ];
            echo json_encode($msg);
        } else {
            exit('Maaf tidak ada halaman yang bisa ditampilkan');
        }
    }

    public function simpandataDusun()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if ($this->request->isAJAX()) {
            $this->dusun->insert([
                'no_dusun' => $this->request->getVar('no_dusun'),
                'nama_dusun' => $this->request->getVar('nama_dusun')
            ]);
            $msg = [
                'success' => 'Data Dusun baru berhasil ditambahkan'
            ];
            echo json_encode($msg);
        }
    }

    public function simpandatarw()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if ($this->request->isAJAX()) {
            $this->rw->insert([
                'no_dusun' => $this->request->getVar('no_dusun'),
                'no_rw' => $this->request->getVar('no_rw'),
                'nama_rw' => $this->request->getVar('nama_rw')
            ]);
            $msg = [
                'success' => 'Data RW baru berhasil ditambahkan'
            ];
            echo json_encode($msg);
        }
    }

    public function simpandatart()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if ($this->request->isAJAX()) {
            $this->rt->insert([
                'no_dusun' => $this->request->getVar('no_dusun'),
                'no_rw' => $this->request->getVar('no_rw'),
                'no_rt' => $this->request->getVar('no_rt'),
                'nama_rt' => $this->request->getVar('nama_rt')
            ]);
            $msg = [
                'success' => 'Data RT baru berhasil ditambahkan'
            ];
            echo json_encode($msg);
        }
    }

    function hapusdusun()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $this->dusun->delete($id);

            $msg = [
                'success' => 'Data Dusun berhasil dihapus'
            ];
            echo json_encode($msg);
        }
    }

    function hapusrw()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $this->rw->delete($id);

            $msg = [
                'success' => 'Data RW berhasil dihapus'
            ];
            echo json_encode($msg);
        }
    }

    function hapusrt()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if ($this->request->isAJAX()) {
            $id = $this->request->getVar('id');

            $this->rt->delete($id);

            $msg = [
                'success' => 'Data RT berhasil dihapus'
            ];
            echo json_encode($msg);
        }
    }

    public function detail($id_user)
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        $data = [
            'title' => 'Detail User',
            'user' => $this->userModel->getdusun($id_user)
        ];

        // jika user tidak ada di tabel
        if (empty($data['user'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('ID User ' . $id_user . ' tidak ditemukan.');
        }

        return view('dusun/detail', $data);
    }

    public function detailDhkp($id_sppt)
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        $data = [
            'title' => 'Detail SPPT',
            'Dhkp' => $this->dhkpModel->getDhkp($id_sppt)
        ];
        return view('dhkp/detailDhkp', $data);
    }


    public function create()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        // session();
        $data = [
            'title' => 'Form Tambah Data',
            'validation' => \Config\Services::validation()
        ];
        return view('admin/create', $data);
    }
    public function save()
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        if (!$this->validate([
            'nama_user' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'nama user harus diisi.'
                ]
            ],
            'hp' => [
                'rules' => 'required|numeric|min_length[11]',
                'errors' => [
                    'required' => 'no. hp harus diisi.',
                    'numeric' => 'no. hp harus berisi angka',
                    'min_length' => 'no. hp tidak benar'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'password harus diisi.'
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'status harus diisi.'
                ]
            ],
            'foto' => [
                'rules' => 'max_size[foto,1024]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'ukuran foto terlalu besar',
                    'is_image' => 'yang anda pilih bukan gambar',
                    'mime_in' => 'yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            // $validation = \Config\Services::validation();
            // return redirect()->to('/admin/create')->withInput()->with('validation', $validation);
            return redirect()->to('/admin/create')->withInput();
        }


        // ambil foto
        $fileFoto = $this->request->getFile('foto');
        // apakah tidak ada gambar yang di upload
        if ($fileFoto->getError() == 4) {
            $namaFoto = 'default.png';
        } else {
            // gnerate nama foto random
            $namaFoto = $fileFoto->getRandomName();

            // pindahkan file ke folder img
            $fileFoto->move('img', $namaFoto);
        }

        $this->userModel->save([
            'nama_user' => $this->request->getVar('nama_user'),
            'hp' => $this->request->getVar('hp'),
            'password' => $this->request->getVar('password'),
            'status' => $this->request->getVar('status'),
            'foto' => $namaFoto
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');

        return redirect()->to('/admin');
    }
    public function delete($id_user)
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        // cari gambar berdasarkan id
        $user = $this->userModel->find($id_user);

        //cek jika file gambarnya default.png
        if ($user['foto'] != 'default.png') {
            // hapus gambar
            unlink('img/' . $user['foto']);
        }

        $this->userModel->delete($id_user);

        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/admin');
    }

    public function edit($id_user)
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        $data = [
            'title' => 'Form Ubah Data',
            'validation' => \Config\Services::validation(),
            'user' => $this->userModel->getUser($id_user)
        ];
        return view('admin/edit', $data);
    }
    public function update($id_user)
    {
        if (session()->get('level') > 1) {
            return redirect()->to(base_url());
        }
        // cek duplikat
        //  $userlama = $this->userModel->getUser($this->request->getVar('id_user'));
        //  if($userlama['nama_user']==$this->request->getVar('nama_user')){
        //      $rule_namauser = 'required';
        //  } else {
        //      $rule_namauser='required|is_unique[admin.nama]';
        //  }

        if (!$this->validate([
            'nama_user' => [
                // 'rules' => $rule_namauser,
                'rules' => 'required',
                'errors' => [
                    'required' => 'nama user harus diisi.'
                ]
            ],
            'hp' => [
                'rules' => 'required|numeric|min_length[11]',
                'errors' => [
                    'required' => 'no. hp harus diisi.',
                    'numeric' => 'no. hp harus berisi angka',
                    'min_length' => 'no. hp tidak benar'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'password harus diisi.'
                ]
            ],
            'status' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'status harus diisi.'
                ]
            ],
            'foto' => [
                'rules' => 'max_size[foto,1024]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'ukuran foto terlalu besar',
                    'is_image' => 'yang anda pilih bukan gambar',
                    'mime_in' => 'yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            // $validation = \Config\Services::validation();
            return redirect()->to('/admin/edit/' . $this->request->getVar('id_user'))->withInput();
        }

        $fileFoto = $this->request->getFile('foto');

        //cek gambar, apakah tetap gambar lama
        if ($fileFoto->getError() == 4) {
            $namaFoto = $this->request->getVar('fotoLama');
        } else {
            // generate nama file random
            $namaFoto = $fileFoto->getRandomName();
            // pindahkan gambar
            $fileFoto->move('img', $namaFoto);
            //hapus file yang lama
            unlink('img/' . $this->request->getVar('fotoLama'));
        }

        $this->userModel->save([
            'id_user' => $id_user,
            'nama_user' => $this->request->getVar('nama_user'),
            'hp' => $this->request->getVar('hp'),
            'password' => $this->request->getVar('password'),
            'status' => $this->request->getVar('status'),
            'foto' => $namaFoto
        ]);

        session()->setFlashdata('pesan', 'Data berhasil diubah.');

        return redirect()->to('/admin');
    }
}
