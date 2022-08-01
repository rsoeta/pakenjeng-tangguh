<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ChattModel;
use App\Models\Dtks\UsersLoginModel;
use CodeIgniter\API\ResponseTrait;



class Chat extends BaseController
{
    public function __construct()
    {
        $this->ChattModel = new ChattModel();
    }

    use ResponseTrait;

    public function index()
    {
        if ($this->request->isAJAX()) {

            $rules = [
                'message' => 'required',
            ];

            if (!$this->validate($rules)) {
                return $this->fail($this->validator->getErrors());
            } else {
                $data = [
                    'title' => 'DISKS Chat',
                    'tc_user_id' => session()->get('id'),
                    'tc_fullname' => ucwords(strtolower(session()->get('fullname'))),
                    'tc_message' => $this->request->getVar('message'),
                    'tc_date' => date('Y-m-d H:i:s'),
                    'tc_image' => session()->get('user_image'),
                    'tc_status' => '1',
                    'sukses' => 'Data berhasil diupdate',
                ];

                $this->ChattModel->insert($data);
                return $this->respondCreated($data);
            }
        }
    }

    public function getMsg()
    {
        $model = new ChattModel();
        $data = $model->orderBy('tc_id', 'desc')->findAll();
        return $this->respond($data);
    }

    public function getUserLogged()
    {
        $model = new UsersLoginModel();
        $data = $model->getUserLogged();
        // dd($data);
        return $this->respond($data);
    }

    public function updateLastActivity()
    {
        $model = new UsersLoginModel();
        $id = $this->request->getPost('id');
        $newData = [
            'dul_last_activity' => date('Y-m-d H:i:s'),
        ];
        $data = $model->update($id, $newData);

        return $this->respond($data);
    }
}
