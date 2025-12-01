<?php

namespace App\Controllers\Dtsen;

use App\Controllers\BaseController;
use App\Libraries\WaService;

class ReminderMonitor extends BaseController
{
    protected $db;
    protected $validation;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Render monitoring page
     */
    public function index()
    {
        // optional: hak akses (hanya role tertentu)
        // if (session()->get('role_id') > 3) {
        //     return redirect()->to('/login');
        // }

        $data = [
            'title' => 'Monitoring Reminder WA',
        ];

        return view('dtsen/pembaruan/reminder_monitor', $data);
    }

    /**
     * DataTables AJAX list (GET)
     */
    public function listAjax()
    {
        $request = $this->request;
        $params = $request->getGet();

        // basic query
        $builder = $this->db->table('dtsen_kk_reminder_log r')
            ->select('r.id, r.kk_id, r.admin_id, r.due_date, r.status, r.sent_at, kk.no_kk, kk.kepala_keluarga, u.fullname as admin_name, u.nope')
            ->join('dtsen_kk kk', 'kk.id_kk = r.kk_id', 'left')
            ->join('dtks_users u', 'u.id = r.admin_id', 'left');

        // filters
        if (!empty($params['status'])) {
            $builder->where('r.status', $params['status']);
        }

        if (!empty($params['q'])) {
            $q = trim($params['q']);
            $builder->groupStart()
                ->like('kk.no_kk', $q)
                ->orLike('kk.kepala_keluarga', $q)
                ->orLike('u.fullname', $q)
                ->groupEnd();
        }

        // simple pagination for DataTables client-side processing
        $data = $builder->orderBy('r.due_date', 'DESC')->get()->getResultArray();

        // format response for DataTables (client-side)
        $rows = [];
        foreach ($data as $r) {
            $rows[] = [
                'id' => $r['id'],
                'no_kk' => $r['no_kk'],
                'nama_kk' => $r['kepala_keluarga'],
                'admin' => $r['admin_name'],
                'nope' => $r['nope'],
                'due_date' => $r['due_date'],
                'status' => $r['status'],
                'sent_at' => $r['sent_at']
            ];
        }

        return $this->response->setJSON([
            'data' => $rows
        ]);
    }

    /**
     * Resend reminder manual (POST)
     * body: id (reminder_log.id)
     */
    public function resend()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => false, 'message' => 'Bad request']);
        }

        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak dikirim']);
        }

        // ambil row reminder
        $row = $this->db->table('dtsen_kk_reminder_log r')
            ->select('r.*, kk.no_kk, kk.kepala_keluarga, u.fullname, u.nope, cfg.api_key, cfg.device, cfg.sender, cfg.fonnte_token, cfg.fonnte_sender, cfg.fallback_enabled, cfg.template_groundcheck')
            ->join('dtsen_kk kk', 'kk.id_kk = r.kk_id', 'left')
            ->join('dtks_users u', 'u.id = r.admin_id', 'left')
            ->join('dtsen_wa_config cfg', 'cfg.user_id = r.admin_id', 'left')
            ->where('r.id', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $this->response->setJSON(['status' => false, 'message' => 'Reminder tidak ditemukan']);
        }

        // render pesan
        $message = $this->renderTemplate($row['template_groundcheck'] ?? "{{nameApp()}} - Reminder\nNo KK: {{no_kk}}\nNama: {{nama_kk}}", [
            'no_kk' => $row['no_kk'],
            'nama_kk' => $row['kepala_keluarga'],
            'admin' => $row['fullname'],
            'nameApp()' => nameApp()
        ]);

        // Setup WaService with configs (use fonnte token if present)
        $wa = new WaService([
            'alatwa_api_key' => $row['api_key'] ?? null,
            'alatwa_device'  => $row['device'] ?? null,
            'alatwa_sender'  => $row['sender'] ?? null,
            'fonnte_token'   => $row['fonnte_token'] ?? getenv('fonnte.token'),
            'fonnte_sender'  => $row['fonnte_sender'] ?? null
        ]);

        $send = $wa->sendText($row['nope'], $message);

        if ($send['status'] === true) {
            // update log
            $this->db->table('dtsen_kk_reminder_log')->where('id', $id)->update([
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Pesan berhasil dikirim',
                'provider' => $send['provider'] ?? null
            ]);
        }

        // gagal
        return $this->response->setJSON([
            'status' => false,
            'message' => 'Gagal mengirim: ' . ($send['fonnte_error'] ?? $send['alatwa_error'] ?? ($send['detail']['message'] ?? json_encode($send)))
        ]);
    }

    private function renderTemplate($template, $vars = [])
    {
        if (!$template) return '';
        foreach ($vars as $k => $v) {
            $template = str_replace('{{' . $k . '}}', $v, $template);
        }
        return $template;
    }
}
