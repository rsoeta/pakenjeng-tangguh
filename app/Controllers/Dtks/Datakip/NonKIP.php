<?php

namespace App\Controllers\Dtks\Datakip;

use App\Controllers\BaseController;
use App\Models\Dtks\AuthModel;
use App\Models\Dtks\DtksModel;
use App\Models\Dtks\Vv06Model;
use App\Models\Dtks\DtksStatusModel;
use App\Models\Dtks\DtksKetModel;
use App\Models\WilayahModel;
use App\Models\RwModel;
use App\Models\RtModel;
use App\Models\GenModel;
use App\Models\Dtks\VeriVali09Model;
use App\Models\Dtks\Usulan21Model;
use App\Models\Dtks\VervalPbiModel;
use App\Models\Dtks\KipModel;
use App\Models\Dtks\UsersModel;
use App\Models\Dtks\PekerjaanModel;
use App\Models\Dtks\ShdkModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class NonKIP extends BaseController
{
    public function __construct()
    {
        helper(['form']);
        $this->AuthModel = new AuthModel();
        $this->vv06Model = new Vv06Model;
        $this->Status = new DtksStatusModel;
        $this->keterangan = new DtksKetModel;
        $this->RwModel = new RwModel();
        $this->RtModel = new RtModel();
        $this->GenModel = new GenModel();
        $this->verivali09 = new VeriVali09Model();
        $this->usulan21 = new Usulan21Model();
        $this->VervalPbiModel = new VervalPbiModel();
        $this->WilayahModel = new WilayahModel();
        $this->KipModel = new KipModel();
        $this->UsersModel = new UsersModel();
        $this->PekerjaanModel = new PekerjaanModel();
        $this->ShdkModel = new ShdkModel();
    }

    public function index()
    {

        $jbt = (session()->get('level'));

        $desa = $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll();
        foreach ($desa as $row) {
        }
        $kode_kab = Profil_Admin()['kode_kab'];


        $data = [
            'namaApp' => 'OprNew DTKS',
            'title' => 'Data Siswa/Siswi Non-KIP',
            'user_login' => $this->AuthModel->getUserId(),
            'percentages' => $this->VervalPbiModel->jml_persentase(),
            'statusRole' => $this->GenModel->getStatusRole(),
            'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
            'datarw' => $this->RwModel->noRw(),
            'kecamatan' => $this->WilayahModel->getKec($kode_kab),
            'nama_sekolah' => $this->UsersModel->getSchool()->getResultArray(),
            'jenjang_sekolah' => $this->GenModel->getSekolahJenjang()->getResultArray(),
            'kelas_sekolah' => $this->KipModel->getKelas()->getResultArray(),
        ];
        // dd($data['percentages']);
        // dd($data['jml_persentase']);
        return view('dtks/data/kip/non-kip', $data);
    }

    public function tabel_data()
    {
        // var_dump(deadline_ppks());

        $this->KipModel = new KipModel();
        $csrfName = csrf_token();
        $csrfHash = csrf_hash();

        // $role = session()->get('role_id');

        $filter1 = $this->request->getPost('desa');
        $filter2 = $this->request->getPost('no_rw');
        $filter3 = $this->request->getPost('no_rt');
        $filter4 = $this->request->getPost('nama_sekolah');
        $filter5 = $this->request->getPost('jenjang');
        $filter6 = $this->request->getPost('kelas');
        // $filter7 = '0';
        // var_dump([$filter1, $filter2, $filter3, $filter4, $filter5, $filter6]);
        // die;

        $listing = $this->KipModel->get_datatables($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);
        $jumlah_semua = $this->KipModel->jumlah_semua();
        $jumlah_filter = $this->KipModel->jumlah_filter($filter1, $filter2, $filter3, $filter4, $filter5, $filter6);


        $data = array();
        $no = $_POST['start'];
        foreach ($listing as $key) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $key->dk_nama_siswa;
            $row[] = $key->dk_nisn;
            $row[] = $key->dk_nkk;
            $row[] = $key->dk_alamat;
            $row[] = str_pad($key->dk_rt, 3, '0', STR_PAD_LEFT);
            $row[] = str_pad($key->dk_rw, 3, '0', STR_PAD_LEFT);
            $row[] = $key->dk_kelas;
            $row[] = '<a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit" onclick="edit_person(' . "'" . $key->dk_id . "'" . ')"><i class="far fa-edit"></i></a> | 
                <button class="btn btn-sm btn-secondary" data-id="' . $key->dk_id . '" data-nama="' . $key->dk_nama_siswa . '" id="deleteBtn"><i class="far fa-trash-alt"></i></button>';

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

            $this->WilayahModel = new WilayahModel();
            $users = new UsersModel();

            $data = [
                'title' => 'Form. Tambah Data',
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'datarw' => $this->RwModel->noRw(),
                'datart' => $this->RtModel->noRt(),
                'users' => $users->findAll(),
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
                // Skrip JavaScript untuk menghapus modal
                $removeModalScript = '<script>removeModal();</script>';

                // Gabungkan data dan skrip JavaScript menjadi satu array
                $responseData = array_merge($data, ['remove_modal_script' => $removeModalScript]);

                // Kembalikan respons dalam format JSON
                // return $this->response->setJSON($responseData);
                $msg = [
                    'data' => view('dtks/data/kip/modaltambah', $data),
                    // 'removeModalScript' => $removeModalScript,
                    // 'responseData' => $responseData,
                    // 'data' => view('dtks/data/famantama/modaltambah'),
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
                'dk_nkk' => [
                    'label' => 'No. KK',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
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
                    'label' => 'Nama Siswa',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dk_jenkel' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'dk_tmp_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
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
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_rt' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dk_rw' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dk_nama_ibu' => [
                    'label' => 'Nama Ibu Kandung',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_ayah' => [
                    'label' => 'Nama Ayah Kandung',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_sekolah' => [
                    'label' => 'Nama Sekolah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_kelas' => [
                    'label' => 'Kelas',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'dk_foto_identitas' => [
                    'label' => 'Foto KK',
                    'rules' => 'uploaded[dk_foto_identitas]|is_image[dk_foto_identitas]|mime_in[dk_foto_identitas,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'uploaded' => '{field} harus ada.',
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
            ]);
            if (!$valid) {
                $msg = [
                    'error' => [
                        'dk_nisn' => $validation->getError('dk_nisn'),
                        'dk_nkk' => $validation->getError('dk_nkk'),
                        'dk_kip' => $validation->getError('dk_kip'),
                        'dk_nik' => $validation->getError('dk_nik'),
                        'dk_nama_siswa' => $validation->getError('dk_nama_siswa'),
                        'dk_jenkel' => $validation->getError('dk_jenkel'),
                        'dk_tmp_lahir' => $validation->getError('dk_tmp_lahir'),
                        'dk_tgl_lahir' => $validation->getError('dk_tgl_lahir'),
                        'dk_alamat' => $validation->getError('dk_alamat'),
                        'jenis_pekerjaan' => $validation->getError('jenis_pekerjaan'),
                        'status_kawin' => $validation->getError('status_kawin'),
                        'alamat' => $validation->getError('alamat'),
                        'dk_rt' => $validation->getError('dk_rt'),
                        'dk_rw' => $validation->getError('dk_rw'),
                        'dk_nama_ibu' => $validation->getError('dk_nama_ibu'),
                        'dk_nama_ayah' => $validation->getError('dk_nama_ayah'),
                        'dk_nama_sekolah' => $validation->getError('dk_nama_sekolah'),
                        'dk_jenjang' => $validation->getError('dk_jenjang'),
                        'dk_kelas' => $validation->getError('dk_kelas'),
                        'dk_foto_identitas' => $validation->getError('dk_foto_identitas'),
                    ]
                ];
            } else {

                $dk_foto_identitas = $this->request->getFile('dk_foto_identitas');

                // var_dump($dd_foto_cpm);
                // die;
                $buat_tanggal = date_create($this->request->getVar('updated_at'));
                $filename_empat = 'NONKIP_' . $this->request->getPost('dk_nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
                // var_dump($filename_dua);
                // die;

                $img_empat = imagecreatefromjpeg($dk_foto_identitas);

                // get width and height of image
                $width_empat = imagesx($img_empat);
                $height_empat = imagesy($img_empat);

                // reorient image if width is greater than height
                if ($width_empat < $height_empat) {
                    $img_empat = imagerotate($img_empat, -90, 0);
                }
                // resize image
                // $img_empat = imagescale($img_empat, 480, 640);

                $angle = 0;

                header("Content-type: image/jpg");
                $quality = 100; // 0 to 100

                // var_dump($img_satu);
                // die;

                imagejpeg($img_empat, 'data/kip_foto/nonkip_foto_kk/' . $filename_empat, $quality);
                // var_dump($img_satu);
                // die;

                $data = [
                    'dk_nkk' => $this->request->getVar("dk_nkk"),
                    'dk_nisn' => $this->request->getVar("dk_nisn"),
                    'dk_kip' => $this->request->getVar("dk_kip"),
                    'dk_nik' => $this->request->getVar('dk_nik'),
                    'dk_nama_siswa' => strtoupper(trim($this->request->getVar('dk_nama_siswa'))),
                    'dk_jenkel' => $this->request->getVar('dk_jenkel'),
                    'dk_tmp_lahir' => strtoupper(trim($this->request->getVar("dk_tmp_lahir"))),
                    'dk_tgl_lahir' => $this->request->getVar("dk_tgl_lahir"),
                    'dk_alamat' => strtoupper(trim($this->request->getVar('dk_alamat'))),
                    'dk_rt' => $this->request->getVar("dk_rt"),
                    'dk_rw' => $this->request->getVar("dk_rw"),
                    'dk_desa' => $this->request->getVar('dk_desa'),
                    'dk_kecamatan' => '32.05.33',
                    'provinsi' => '32',
                    'kabupaten' => '32.05',
                    'dk_nama_ibu' => strtoupper(trim($this->request->getVar("dk_nama_ibu"))),
                    'dk_nama_ayah' => strtoupper(trim($this->request->getVar("dk_nama_ayah"))),
                    'dk_nama_sekolah' => $this->request->getVar('dk_nama_sekolah'),
                    'dk_kelas' => $this->request->getVar('dk_kelas'),
                    'dk_partisipasi' => $this->request->getVar('dk_partisipasi'),
                    'dk_foto_identitas' => $filename_empat,
                    'dk_created_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                    'dk_created_by' => session()->get('nik'),
                    'dk_updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                    'dk_updated_by' => session()->get('nik'),
                    'created_at_year' => date('Y'),
                    'created_at_month' => date('n'),

                    // 'foto_rumah' => $nama_foto_rumah,
                ];
                // dd($data);
                $this->KipModel->save($data);

                $msg = [
                    'sukses' => 'Data berhasil ditambahkan',
                ];
            }
            echo json_encode($msg);


            // session()->setFlashdata('pesan', 'Data berhasil ditambahkan.');


            // echo json_encode(array("status" => true));
            // return redirect()->to('/dtks/usulan/tables');
        } else {
            return redirect()->to('lockscreen');
        }
    }

    function formedit()
    {
        if ($this->request->isAJAX()) {
            $this->KipModel = new KipModel();
            $this->WilayahModel = new WilayahModel();
            $users = new UsersModel();

            $dk_id =  $this->request->getVar('dk_id');

            $row = $this->KipModel->find($dk_id);
            $data = [
                'title' => 'Form. Edit Data',
                'desa' => $this->WilayahModel->orderBy('name', 'asc')->where('district_id', '32.05.33')->findAll(),
                'rw' => $this->RwModel->noRw(),
                'rt' => $this->RtModel->noRt(),

                'dk_id' => $row['dk_id'],
                'dk_nisn' => $row['dk_nisn'],
                'dk_nkk' => $row['dk_nkk'],
                'dk_kip' => $row['dk_kip'],
                'dk_nik' => $row['dk_nik'],
                'dk_nama_siswa' => $row['dk_nama_siswa'],
                'dk_jenkel' => $row['dk_jenkel'],
                'dk_tmp_lahir' => $row['dk_tmp_lahir'],
                'dk_tgl_lahir' => $row['dk_tgl_lahir'],
                'dk_alamat' => $row['dk_alamat'],
                'dk_rt' => $row['dk_rt'],
                'dk_rw' => $row['dk_rw'],
                'dk_desa' => $row['dk_desa'],
                'dk_nama_ibu' => $row['dk_nama_ibu'],
                'dk_nama_ayah' => $row['dk_nama_ayah'],
                'dk_nama_sekolah' => $row['dk_nama_sekolah'],
                'dk_kelas' => $row['dk_kelas'],
                'dk_foto_identitas' => $row['dk_foto_identitas'],
            ];
            // var_dump($data);
            // die;

            $msg = [
                'sukses' => view('dtks/data/kip/modaledit', $data)
            ];
            echo json_encode($msg);
        } else {
            return redirect()->to('lockscreen');
        }
    }

    function update()
    {
        if ($this->request->isAJAX()) {
            // var_dump($this->request->getVar());
            // die;

            $id = $this->request->getVar('dk_id');
            // dd($id);

            $validation = \Config\Services::validation();

            $valid = $this->validate([
                'dk_nkk' => [
                    'label' => 'No. KK',
                    'rules' => 'required|numeric|min_length[16]|max_length[16]',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'numeric' => '{field} harus berisi angka.',
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
                    'label' => 'Nama Siswa',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
                    ]
                ],
                'dk_jenkel' => [
                    'label' => 'Jenis Kelamin',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'dk_tmp_lahir' => [
                    'label' => 'Tempat Lahir',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                        'alpha_numeric_punct' => '{field} harus berisi alphabet.'
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
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_rt' => [
                    'label' => 'No. RT',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dk_rw' => [
                    'label' => 'No. RW',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => '{field} harus dipilih.',
                        'numeric' => '{field} harus berisi angka.'
                    ]
                ],
                'dk_nama_ibu' => [
                    'label' => 'Nama Ibu Kandung',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_ayah' => [
                    'label' => 'Nama Ayah Kandung',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.'
                    ]
                ],
                'dk_nama_sekolah' => [
                    'label' => 'Nama Sekolah',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus diisi.',
                    ]
                ],
                'dk_kelas' => [
                    'label' => 'Kelas',
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus dipilih.'
                    ]
                ],
                'dk_foto_identitas' => [
                    'label' => 'Foto KK',
                    'rules' => 'is_image[dk_foto_identitas]|mime_in[dk_foto_identitas,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'is_image' => '{field} harus berupa gambar.',
                        'mime_in' => '{field} harus berupa gambar.',
                        'max_size' => '{field} harus berukuran tidak lebih dari 2MB.'
                    ]
                ],
            ]);
            // dd($valid);

            if (!$valid) {
                $msg = [
                    'error' => [
                        'dk_nisn' => $validation->getError('dk_nisn'),
                        'dk_nkk' => $validation->getError('dk_nkk'),
                        'dk_kip' => $validation->getError('dk_kip'),
                        'dk_nik' => $validation->getError('dk_nik'),
                        'dk_nama_siswa' => $validation->getError('dk_nama_siswa'),
                        'dk_jenkel' => $validation->getError('dk_jenkel'),
                        'dk_tmp_lahir' => $validation->getError('dk_tmp_lahir'),
                        'dk_tgl_lahir' => $validation->getError('dk_tgl_lahir'),
                        'dk_alamat' => $validation->getError('dk_alamat'),
                        'jenis_pekerjaan' => $validation->getError('jenis_pekerjaan'),
                        'status_kawin' => $validation->getError('status_kawin'),
                        'alamat' => $validation->getError('alamat'),
                        'dk_rt' => $validation->getError('dk_rt'),
                        'dk_rw' => $validation->getError('dk_rw'),
                        'dk_nama_ibu' => $validation->getError('dk_nama_ibu'),
                        'dk_nama_ayah' => $validation->getError('dk_nama_ayah'),
                        'dk_nama_sekolah' => $validation->getError('dk_nama_sekolah'),
                        'dk_jenjang' => $validation->getError('dk_jenjang'),
                        'dk_kelas' => $validation->getError('dk_kelas'),
                        'dk_foto_identitas' => $validation->getError('dk_foto_identitas'),
                    ]
                ];
                echo json_encode($msg);
            } else {
                $dkFotoIdentitas = $_FILES['dk_foto_identitas']['name'];
                if ($dkFotoIdentitas != NULL) {

                    $nonkip = $this->KipModel->find($id);

                    $dk_foto_identitas_old = $nonkip['dk_foto_identitas'];

                    $dir_identintas = 'data/kip_foto/nonkip_foto_kk/' . $dk_foto_identitas_old;
                    unlink($dir_identintas);

                    $dk_foto_identitas = $this->request->getFile('dk_foto_identitas');

                    // var_dump($dd_foto_cpm);
                    // die;
                    $buat_tanggal = date_create($this->request->getVar('updated_at'));
                    $filename_empat = 'NONKIP_' . $this->request->getPost('dk_nik') . '_' . date_format($buat_tanggal, 'Ymd_His') . '.jpg';
                    // var_dump($filename_dua);
                    // die;

                    $img_empat = imagecreatefromjpeg($dk_foto_identitas);

                    // get width and height of image
                    $width_empat = imagesx($img_empat);
                    $height_empat = imagesy($img_empat);

                    // reorient image if width is greater than height
                    if ($width_empat < $height_empat) {
                        $img_empat = imagerotate($img_empat, -90, 0);
                    }
                    // resize image
                    // $img_empat = imagescale($img_empat, 480, 640);

                    $angle = 0;

                    header("Content-type: image/jpg");
                    $quality = 100; // 0 to 100

                    // var_dump($img_satu);
                    // die;

                    imagejpeg($img_empat, 'data/kip_foto/nonkip_foto_kk/' . $filename_empat, $quality);
                    // var_dump($img_satu);
                    // die;

                    $data = [
                        'dk_nkk' => $this->request->getVar("dk_nkk"),
                        'dk_nisn' => $this->request->getVar("dk_nisn"),
                        'dk_kip' => $this->request->getVar("dk_kip"),
                        'dk_nik' => $this->request->getVar('dk_nik'),
                        'dk_nama_siswa' => strtoupper(trim($this->request->getVar('dk_nama_siswa'))),
                        'dk_jenkel' => $this->request->getVar('dk_jenkel'),
                        'dk_tmp_lahir' => strtoupper(trim($this->request->getVar("dk_tmp_lahir"))),
                        'dk_tgl_lahir' => $this->request->getVar("dk_tgl_lahir"),
                        'dk_alamat' => strtoupper(trim($this->request->getVar('dk_alamat'))),
                        'dk_rt' => $this->request->getVar("dk_rt"),
                        'dk_rw' => $this->request->getVar("dk_rw"),
                        'dk_desa' => $this->request->getVar('dk_desa'),
                        'dk_kecamatan' => '32.05.33',
                        'provinsi' => '32',
                        'kabupaten' => '32.05',
                        'dk_nama_ibu' => strtoupper(trim($this->request->getVar("dk_nama_ibu"))),
                        'dk_nama_ayah' => strtoupper(trim($this->request->getVar("dk_nama_ayah"))),
                        'dk_nama_sekolah' => $this->request->getVar('dk_nama_sekolah'),
                        'dk_kelas' => $this->request->getVar('dk_kelas'),
                        'dk_partisipasi' => $this->request->getVar('dk_partisipasi'),
                        'dk_foto_identitas' => $filename_empat,
                        'dk_created_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                        'dk_created_by' => session()->get('nik'),
                        'dk_updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                        'dk_updated_by' => session()->get('nik'),
                        'created_at_year' => date('Y'),
                        'created_at_month' => date('n')
                    ];
                    // dd($data);
                    $this->KipModel->update($id, $data);

                    $msg = [
                        'sukses' => 'Data berhasil diubah',
                    ];
                    echo json_encode($msg);
                } else {
                    $buat_tanggal = date_create($this->request->getVar('updated_at'));

                    $data = [
                        'dk_nkk' => $this->request->getVar("dk_nkk"),
                        'dk_nisn' => $this->request->getVar("dk_nisn"),
                        'dk_kip' => $this->request->getVar("dk_kip"),
                        'dk_nik' => $this->request->getVar('dk_nik'),
                        'dk_nama_siswa' => strtoupper(trim($this->request->getVar('dk_nama_siswa'))),
                        'dk_jenkel' => $this->request->getVar('dk_jenkel'),
                        'dk_tmp_lahir' => strtoupper(trim($this->request->getVar("dk_tmp_lahir"))),
                        'dk_tgl_lahir' => $this->request->getVar("dk_tgl_lahir"),
                        'dk_alamat' => strtoupper(trim($this->request->getVar('dk_alamat'))),
                        'dk_rt' => $this->request->getVar("dk_rt"),
                        'dk_rw' => $this->request->getVar("dk_rw"),
                        'dk_desa' => $this->request->getVar('dk_desa'),
                        'dk_kecamatan' => '32.05.33',
                        'provinsi' => '32',
                        'kabupaten' => '32.05',
                        'dk_nama_ibu' => strtoupper(trim($this->request->getVar("dk_nama_ibu"))),
                        'dk_nama_ayah' => strtoupper(trim($this->request->getVar("dk_nama_ayah"))),
                        'dk_nama_sekolah' => $this->request->getVar('dk_nama_sekolah'),
                        'dk_kelas' => $this->request->getVar('dk_kelas'),
                        'dk_partisipasi' => $this->request->getVar('dk_partisipasi'),
                        'dk_created_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                        'dk_created_by' => session()->get('nik'),
                        'dk_updated_at' => date_format($buat_tanggal, 'Y-m-d H:i:s'),
                        'dk_updated_by' => session()->get('nik'),
                        'created_at_year' => date('Y'),
                        'created_at_month' => date('n')
                    ];
                    // dd($data);
                    $this->KipModel->update($id, $data);

                    $msg = [
                        'sukses' => 'Data berhasil diubah',
                    ];
                    echo json_encode($msg);
                }
            }
        } else {
            return redirect()->to('lockscreen');
        }
    }

    function delete()
    {
        if ($this->request->isAJAX()) {
            $dk_id = $this->request->getVar('id');

            $nonKip = $this->KipModel->find($dk_id);
            unlink('data/kip_foto/nonkip_foto_kk/' . $nonKip['dk_foto_identitas']);

            $this->KipModel->delete($dk_id);

            $msg = [
                'sukses' => 'Data berhasil dihapus'
            ];
            echo json_encode($msg);
        }
    }

    function export()
    {

        $wilayahModel = new WilayahModel();
        // $model = new Usulan22Model();
        // $tmbExpData = $this->request->getVar('btnExpData');
        // $tmbExpAll = $this->request->getVar('btnExpAll');
        $filter1 = $this->request->getVar('desa');
        $filter2 = $this->request->getVar('rw');
        $filter3 = $this->request->getVar('kelas');

        $data = $this->KipModel->dataExport($filter1, $filter2, $filter3)->getResultArray();
        // dd($data);

        $this->WilayahModel = $wilayahModel->getVillage($filter1);

        // $file_name = 'TEMPLATE_PENGUSULAN_PAKENJENG - ' . $this->WilayahModel['name'] . ' - ' . $filter4 . '.xlsx';
        $file_name = 'TEMPLATE_NONKIP - PAKENJENG - ' .  $this->WilayahModel['name'] . '.xlsx';

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        // Mengatur ukuran kertas ke F4
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

        // Mengatur properti margin
        $sheet->getPageMargins()->setTop(0.3); // Margin atas dalam inci
        $sheet->getPageMargins()->setBottom(0.3); // Margin bawah dalam inci
        $sheet->getPageMargins()->setLeft(0.3); // Margin kiri dalam inci
        $sheet->getPageMargins()->setRight(0.3); // Margin kanan dalam inci

        // Mengatur skala "Fit to 1 Page"
        $sheet->getPageSetup()->setFitToPage(true);

        // Menyesuaikan ke halaman lebar atau tinggi (bisa juga mengatur keduanya)
        $sheet->getPageSetup()->setFitToWidth(1); // Atur ke 1 halaman lebar
        // $sheet->getPageSetup()->setFitToHeight(1); // Atur ke 1 halaman tinggi

        $styleArray = [
            'font' => [
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'wrapText'     => TRUE,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                // 'rotation' => 90,
                'startColor' => [
                    'rgb' => '9400d3',
                ],
                'endColor' => [
                    'rgb' => '9400d3',
                ],
            ],
        ];

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:S1')->applyFromArray($styleArray);

        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'NO. KIP');
        $sheet->setCellValue('C1', 'NISN');
        $sheet->setCellValue('D1', 'NOKK');
        $sheet->setCellValue('E1', 'NAMA SISWA');
        $sheet->setCellValue('F1', 'NIK');
        $sheet->setCellValue('G1', 'JENIS KELAMIN');
        $sheet->setCellValue('H1', 'TEMPAT LAHIR');
        $sheet->setCellValue('I1', "TANGGAL LAHIR\n(31/01/2000)");
        $sheet->setCellValue('J1', 'ALAMAT');
        $sheet->setCellValue('K1', 'RT');
        $sheet->setCellValue('L1', 'RW');
        $sheet->setCellValue('M1', 'DESA');
        $sheet->setCellValue('N1', 'KEC');
        $sheet->setCellValue('O1', 'NAMA IBU');
        $sheet->setCellValue('P1', 'NAMA AYAH');
        $sheet->setCellValue('Q1', 'NAMA SEKOLAH');
        $sheet->setCellValue('R1', 'KLS');
        $sheet->setCellValue('S1', 'DOKUMEN');

        $count = 2;

        foreach ($data as $row) {

            $tglLahir = date('Y-m-d', strtotime($row['dk_tgl_lahir']));

            $sheet->setCellValue('A' . $count, $count - 1);
            $sheet->setCellValueExplicit('B' . $count, $row['dk_kip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $count, $row['dk_nisn'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $count, $row['dk_nkk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $count, strtoupper($row['dk_nama_siswa']));
            $sheet->setCellValueExplicit('F' . $count, $row['dk_nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('G' . $count, $row['NamaJenKel']);
            $sheet->setCellValue('H' . $count, strtoupper($row['dk_tmp_lahir']));
            $sheet->setCellValue('I' . $count, $tglLahir);
            $sheet->setCellValue('J' . $count, strtoupper($row['dk_alamat']));
            $sheet->setCellValue('K' . $count, str_pad($row['dk_rt'], 3, '0', STR_PAD_LEFT));
            $sheet->setCellValue('L' . $count, str_pad($row['dk_rw'], 3, '0', STR_PAD_LEFT));
            $sheet->setCellValue('M' . $count, strtoupper($row['namaDesa']));
            $sheet->setCellValue('N' . $count, strtoupper($row['namaKec']));
            $sheet->setCellValue('O' . $count, strtoupper($row['dk_nama_ibu']));
            $sheet->setCellValue('P' . $count, strtoupper($row['dk_nama_ayah']));
            $sheet->setCellValue('Q' . $count, strtoupper($row['dk_nama_sekolah']));
            $sheet->setCellValue('R' . $count, $row['dk_kelas']);
            $sheet->setCellValue('S' . $count, $row['dk_foto_identitas']);

            $count++;
        }
        $sheet->getStyle('A2:S' . ($count - 1))->applyFromArray($border);

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
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
