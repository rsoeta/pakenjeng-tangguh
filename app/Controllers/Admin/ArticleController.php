<?php // app/Controllers/Admin/ArticleController.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArticleModel;

class ArticleController extends BaseController
{
    protected $session;
    protected $helpers = ['url', 'form'];
    protected $request;
    protected $response;
    protected $articleModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->response = \Config\Services::response();
    }

    public function index()
    {
        $title = 'Manajemen Artikel';
        $session = session();
        $kodeDesa = $session->get('kode_desa');
        $roleId   = $session->get('role_id');
        $articles = $this->articleModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'kode_desa' => $kodeDesa,
            'role_id'   => $roleId,
            'articles'  => $articles,
            'title'     => $title,
        ];
        return view('admin/articles/index', $data);
    }

    /**
     * JSON endpoint untuk DataTable
     */
    public function data()
    {
        // Jika ingin batasi akses: hanya role <= 3 (admin)
        $role = session()->get('role_id') ?? 99;
        if ($role > 3) {
            return $this->response->setStatusCode(403)->setJSON(['data' => []]);
        }

        $articles = $this->articleModel->orderBy('created_at', 'DESC')->findAll();

        $data = [];
        $no = 0;
        foreach ($articles as $row) {
            $no++;
            $statusBadge = $row['status'] === 'publish'
                ? '<span class="badge bg-success">PUBLISH</span>'
                : '<span class="badge bg-secondary">DRAFT</span>';

            $thumbUrl = $row['image']
                ? base_url('uploads/articles/' . $row['image'])
                : base_url('assets/images/image_not_available.jpg');

            $imgTag = '<img src="' . $thumbUrl . '" class="article-thumb">';

            // action buttons (Edit opens edit tab and loads data; Delete calls AJAX)
            $editBtn = '<button class="btn btn-sm btn-primary btnEditArticle" data-id="' . $row['id'] . '"><i class="fas fa-edit"></i></button>';
            $delBtn  = '<button class="btn btn-sm btn-danger btnDeleteArticle ms-1" data-id="' . $row['id'] . '"><i class="fas fa-trash"></i></button>';

            $data[] = [
                'no' => $no,
                'image' => $imgTag,
                'title' => esc($row['title']),
                'status' => $statusBadge,
                'created_at' => $row['created_at'] ? date('d M Y H:i', strtotime($row['created_at'])) : '-',
                'actions' => $editBtn . $delBtn,
                'raw' => $row, // optional, for debugging client-side if needed
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    /**
     * Ambil single artikel (untuk populasi form edit)
     */
    public function get($id = null)
    {
        if (!$id) return $this->response->setJSON(['success' => false, 'message' => 'ID tidak diberikan']);

        $article = $this->articleModel->find($id);
        if (!$article) return $this->response->setJSON(['success' => false, 'message' => 'Artikel tidak ditemukan']);

        return $this->response->setJSON(['success' => true, 'data' => $article]);
    }

    /**
     * Hapus artikel (POST)
     */
    public function delete($id = null)
    {
        $role = session()->get('role_id') ?? 99;
        if ($role > 3) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        if (!$id) return $this->response->setJSON(['success' => false, 'message' => 'ID tidak diberikan']);

        try {
            $this->articleModel->delete($id);
            return $this->response->setJSON(['success' => true, 'message' => 'Artikel dihapus.']);
        } catch (\Throwable $e) {
            log_message('error', '[ArticleController::delete] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus artikel.']);
        }
    }

    public function create()
    {
        return view('admin/articles/create'); // form dengan TinyMCE
    }

    public function store()
    {
        $post = $this->request->getPost();
        // generate slug sederhana
        $slug = url_title($post['title'], '-', true);
        $this->articleModel->save([
            'title' => $post['title'],
            'description' => $post['description'],
            'image' => $post['image'] ?? null,
            'slug' => $slug,
            'status' => $post['status'] ?? 'draft',
            'author_id' => $this->session->get('user_id') ?? $this->session->get('nik'),
            'meta_description' => $post['meta_description'] ?? null,
            'category_id' => $post['category_id'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->to('/admin/articles')->with('success', 'Artikel disimpan.');
    }

    public function edit($id)
    {
        $data['article'] = $this->articleModel->find($id);
        if (!$data['article']) return redirect()->back()->with('error', 'Artikel tidak ditemukan');
        return view('admin/articles/edit', $data);
    }

    public function update($id)
    {
        $post = $this->request->getPost();
        $slug = url_title($post['title'], '-', true);
        $this->articleModel->update($id, [
            'title' => $post['title'],
            'description' => $post['description'],
            'image' => $post['image'] ?? null,
            'slug' => $slug,
            'status' => $post['status'] ?? 'draft',
            'meta_description' => $post['meta_description'] ?? null,
            'category_id' => $post['category_id'] ?? null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->to('/admin/articles')->with('success', 'Artikel diperbarui.');
    }

    // public function delete($id)
    // {
    //     $this->articleModel->delete($id);
    //     return $this->response->setJSON(['success' => true, 'message' => 'Artikel dihapus.']);
    // }

    /** TinyMCE image upload */
    public function uploadImage()
    {
        $file = $this->request->getFile('file'); // TinyMCE default field name "file"
        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON(['location' => '', 'error' => 'File invalid']);
        }

        // buat folder jika belum ada
        $folder = WRITEPATH . '../public/uploads/articles';
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        $newName = $file->getRandomName();
        $file->move($folder, $newName);

        $url = base_url("uploads/articles/{$newName}");
        return $this->response->setJSON(['location' => $url]); // TinyMCE expects { location : "url" }
    }
}
