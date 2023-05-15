<?php

namespace App\Controllers\Dtks;

use App\Controllers\BaseController;
use App\Models\Dtks\DtksModel;
use App\Models\Dtks\UsulanModel;
use App\Models\DesaModel;
use App\Models\RwModel;
use App\Models\GenModel;
use App\Models\Dtks\BansosModel;

class Usulan extends BaseController
{
    protected $usulanModel;
    public function __construct()
    {
        helper(['form']);
        $this->usulanModel = new UsulanModel();
    }

    public function index()
    {
        $model = new UsulanModel();
        $desa = new DesaModel();
        $rw = new RwModel();
        $bansos = new BansosModel();

        $data = [
            'title' => 'Data Usulan DTKS',
            'dtks' => $model->getDtks(),
            'desa' => $desa->orderBy('nama_desa', 'asc')->findAll(),
            'rw' => $rw->orderBy('no_rw', 'asc')->findAll(),
            'bansos' => $bansos->findAll(),
        ];
        return view('dtks/data/dtks/usulan/index', $data);
    }

    public function tabel_data()
    {
        $model = new UsulanModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('rw');
        $filter3 = $this->request->getPost('rt');
        $filter4 = $this->request->getPost('bansos');

        $listing = $model->get_datatables($filter1, $filter2, $filter3, $filter4);
        $jumlah_semua = $model->jumlah_semua();
        $jumlah_filter = $model->jumlah_filter($filter1, $filter2, $filter3, $filter4);

        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->nokk;
            $row[] = $key->nik;
            $row[] = $key->nama;
            $row[] = $key->jenis_kelamin;
            $row[] = $key->tempat_lahir;
            $row[] = $key->tanggal_lahir;
            $row[] = $key->ibu_kandung;
            $row[] = $key->jenis_pekerjaan;
            $row[] = $key->status_kawin;
            $row[] = $key->alamat;
            $row[] = $key->rt;
            $row[] = $key->rw;
            $row[] = $key->kelurahan;
            $row[] = $key->kecamatan;
            $row[] = $key->shdk;
            $row[] = $key->foto_rumah;
            // if ($no == 1) {
            //     $row[] = number_format($key->debit);
            //     $row[] = number_format($key->kredit);
            //     $debit = $key->debit;
            //     $saldo = $key->debit;
            //     $row[] = number_format($saldo);
            // } else {
            //     if ($key->debit != 0) {
            //         $row[] = number_format($key->debit);
            //         $row[] = number_format($key->kredit);
            //         $debit = $debit + $key->debit;
            //         $saldo = $saldo + $key->debit;
            //         $row[] = number_format($saldo);
            //     } else {
            //         $row[] = number_format($key->debit);
            //         $row[] = number_format($key->kredit);
            //         $kredit = $key->kredit;
            //         $kredit = $kredit + $key->kredit;
            //         $saldo = $saldo - $key->kredit;
            //         $row[] = number_format($saldo);
            //     }
            // }
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



    public function home()
    {
        if (session()->get('level') == 1) {
            $model = new UsulanModel();
            $data = [
                'title' => 'Data Terpadu Kesejahteraan Sosial',
                'dtks' => $model->getDtks()
            ];
            return view('dtks/data/dtks/usulan/tables', $data);
        } else if (session()->get('level') == 2) {
            $model = new UsulanModel();
            $data = [
                'title' => 'Data Terpadu Kesejahteraan Sosial',
                'dtks' => $model->getData()
            ];
            return view('dtks/data/dtks/usulan/tables', $data);
        } else {
            $data = [
                'title' => 'Access denied',
            ];
            return view('lockscreen', $data);
        }
    }

    public function create()
    {
        // session();
        $data = [
            'title' => 'Form Tambah Data',
            'validation' => \Config\Services::validation()
        ];
        return view('dtks/data/dtks/usulan/create', $data);
    }

    public function save()
    {
        // validasi input
        if (!$this->validate([
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} harus diisi.'
                ]
            ],
            'nik' => [
                'rules' => 'required|numeric|is_unique[dtks_data.nik]|is_unique[dtks_usulan.nik]|min_length[16]|max_length[16]',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.',
                    'is_unique' => '{field} sudah terdaftar.',
                    'min_length' => '{field} min. 16 digit.',
                    'max_length' => '{field} max. 16 digit.'
                ]
            ],
            'nkk' => [
                'rules' => 'required|numeric|is_unique[dtks_data.nkk]|is_unique[dtks_usulan.nkk]|min_length[16]|max_length[16]',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.',
                    'is_unique' => '{field} sudah terdaftar.',
                    'min_length' => '{field} min. 16 digit.',
                    'max_length' => '{field} max. 16 digit.'
                ]
            ],
            'nama_krt' => [
                'rules' => 'required|alpha_space',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'alpha_space' => '{field} harus berisi alphabet.'
                ]
            ],
            'tgl_lahir' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'valid_date' => '{field} tidak valid.'
                ]
            ],
            'jenis_kelamin' => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} harus dipilih'
                ]
            ],
            'rt' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.'
                ]
            ],
            'rw' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.'
                ]
            ],
            'rmh_depan' => [
                'rules' => 'uploaded[rmh_depan]|max_size[rmh_depan,10000]|is_image[rmh_depan]|mime_in[rmh_depan,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => '{field} harus diisi.',
                    'max_size' => 'Ukuran foto terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'

                ]
            ],
            'rmh_belakang' => [
                'rules' => 'uploaded[rmh_belakang]|max_size[rmh_belakang,10000]|is_image[rmh_belakang]|mime_in[rmh_belakang,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => '{field} harus diisi.',
                    'max_size' => 'Ukuran foto terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'

                ]
            ],
            'rmh_kiri' => [
                'rules' => 'uploaded[rmh_kiri]|max_size[rmh_kiri,10000]|is_image[rmh_kiri]|mime_in[rmh_kiri,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => '{field} harus diisi.',
                    'max_size' => 'Ukuran foto terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'

                ]
            ],
            'rmh_kanan' => [
                'rules' => 'uploaded[rmh_kanan]|max_size[rmh_kanan,10000]|is_image[rmh_kanan]|mime_in[rmh_kanan,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => '{field} harus diisi.',
                    'max_size' => 'Ukuran foto terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'

                ]
            ],
            'jml_kel' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.'
                ]
            ],
            'jml_art' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.'
                ]
            ],
            'peristiwa' => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} harus diisi.',

                ]
            ],
            'tgl_peristiwa' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'valid_date' => '{field} tidak valid.'
                ]
            ]
        ])) {
            // $validation = \Config\Services::validation();
            // return redirect()->to('/dtks/data/dtks/usulan/create')->withInput()->with('validation', $validation);


            // $validation = \Config\Services::validation();
            // dd($validation);

            return redirect()->to('/dtks/data/dtks/usulan/create')->withInput();
        }

        // ambil foto
        $rmh_depan = $this->request->getFile('rmh_depan');
        $rmh_belakang = $this->request->getFile('rmh_belakang');
        $rmh_kiri = $this->request->getFile('rmh_kiri');
        $rmh_kanan = $this->request->getFile('rmh_kanan');

        // generate nama foto
        $nama_rmh_depan = $rmh_depan->getRandomName();
        $nama_rmh_belakang = $rmh_belakang->getRandomName();
        $nama_rmh_kiri = $rmh_kiri->getRandomName();
        $nama_rmh_kanan = $rmh_kanan->getRandomName();

        // pindahkan file ke folder img
        $rmh_depan->move('img/dtks_usulan', $nama_rmh_depan);
        $rmh_belakang->move('img/dtks_usulan', $nama_rmh_belakang);
        $rmh_kiri->move('img/dtks_usulan', $nama_rmh_kiri);
        $rmh_kanan->move('img/dtks_usulan', $nama_rmh_kanan);

        // ambil nama file


        $data = [
            'kec' => $this->request->getVar('kec'),
            'desa' => $this->request->getVar('desa'),
            'alamat' => $this->request->getVar('alamat'),
            'nik' => $this->request->getVar('nik'),
            'nkk' => $this->request->getVar('nkk'),
            'nama_krt' => $this->request->getVar('nama_krt'),
            'tgl_lahir' => $this->request->getVar("tgl_lahir"),
            'rt' => $this->request->getVar("rt"),
            'rw' => $this->request->getVar("rw"),
            'rmh_depan' => $nama_rmh_depan,
            'rmh_belakang' => $nama_rmh_belakang,
            'rmh_kiri' => $nama_rmh_kiri,
            'rmh_kanan' => $nama_rmh_kanan,
            'jml_kel' => $this->request->getVar("jml_kel"),
            'jml_art' => $this->request->getVar("jml_art"),
            'peristiwa' => $this->request->getVar('peristiwa'),
            'tgl_peristiwa' => $this->request->getVar("tgl_peristiwa"),
            'status' => 1
        ];

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');

        $this->usulanModel->save($data);

        return redirect()->to('/dtks/data/dtks/usulan/tables');
    }

    public function tables()
    {
        if (session()->get('level') == 1) {
            $model = new UsulanModel();
            $data = [
                'title' => 'Data Terpadu Kesejahteraan Sosial',
                'dtks' => $model->getDtks()
            ];
            return view('dtks/data/dtks/usulan/tables', $data);
        } else if (session()->get('level') == 2) {
            $model = new UsulanModel();
            $data = [
                'title' => 'Data Terpadu Kesejahteraan Sosial',
                'dtks' => $model->getData()
            ];
            return view('dtks/data/dtks/usulan/tables', $data);
        }
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
        $model = new UsulanModel();

        $data = [
            'title' => 'Detail Keluarga Penerima Manfaat',
            'dtks' => $model->getIdDtks($id)
        ];
        return view('dtks/data/dtks/usulan/detail', $data);
    }

    public function delete($id)
    {
        $this->usulanModel->delete($id);

        session()->setFlashdata('pesan', 'Data berhasil dihapus.');
        return redirect()->to('/dtks/data/dtks/usulan/tables');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Form Edit Data',
            'validation' => \Config\Services::validation(),
            'dtks' => $this->usulanModel->getIdDtks($id)
        ];

        // dd($data);
        return view('dtks/data/dtks/usulan/edit', $data);
    }

    public function update($id)
    {
        // validasi input
        if (!$this->validate([
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => '{field} harus diisi.'
                ]
            ],
            'nik' => [
                'rules' => 'required|numeric|is_unique[dtks_data.nik,id,' . $id . ']|is_unique[dtks_usulan.nik,id,' . $id . ']|min_length[16]|max_length[16]',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.',
                    'is_unique' => '{field} sudah terdaftar.',
                    'min_length' => '{field} min. 16 digit.',
                    'max_length' => '{field} max. 16 digit.'
                ]
            ],
            'nkk' => [
                'rules' => 'required|numeric|is_unique[dtks_data.nkk,id,' . $id . ']|is_unique[dtks_usulan.nkk,id,' . $id . ']|min_length[16]|max_length[16]',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.',
                    'is_unique' => '{field} sudah terdaftar.',
                    'min_length' => '{field} min. 16 digit.',
                    'max_length' => '{field} max. 16 digit.'
                ]
            ],
            'nama_krt' => [
                'rules' => 'required|alpha_space',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'alpha_space' => '{field} harus berisi alphabet.'
                ]
            ],
            'tgl_lahir' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'valid_date' => '{field} tidak valid.'
                ]
            ],
            'rt' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.'
                ]
            ],
            'rw' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.'
                ]
            ],

            'jml_kel' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.'
                ]
            ],
            'jml_art' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'numeric' => '{field} harus berisi angka.'
                ]
            ],
            'tgl_peristiwa' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => '{field} harus diisi.',
                    'valid_date' => '{field} tidak valid.'
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            // dd($validation);

            return redirect()->to('/dtks/data/dtks/usulan/edit/' . $this->request->getVar('id'))->withInput()->with('validation', $validation);
        }
        $data = [
            'id' => $id,
            'kec' => $this->request->getVar('kec'),
            'desa' => $this->request->getVar('desa'),
            'alamat' => $this->request->getVar('alamat'),
            'nik' => $this->request->getVar('nik'),
            'nkk' => $this->request->getVar('nkk'),
            'nama_krt' => $this->request->getVar('nama_krt'),
            'tgl_lahir' => $this->request->getVar("tgl_lahir"),
            'rt' => $this->request->getVar("rt"),
            'rw' => $this->request->getVar("rw"),
            'rmh_depan' => $this->request->getVar('rmh_depan'),
            'rmh_belakang' => $this->request->getVar('rmh_belakang'),
            'rmh_kiri' => $this->request->getVar('rmh_kiri'),
            'rmh_kanan' => $this->request->getVar('rmh_kanan'),
            'jml_kel' => $this->request->getVar("jml_kel"),
            'jml_art' => $this->request->getVar("jml_art"),
            'peristiwa' => $this->request->getVar('peristiwa'),
            'tgl_peristiwa' => $this->request->getVar("tgl_peristiwa"),
            'status' => 1
        ];

        session()->setFlashdata('pesan', 'Data berhasil diubah.');

        $this->usulanModel->save($data);

        return redirect()->to('/dtks/data/dtks/usulan/detail/' . $id);
    }
}
