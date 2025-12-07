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

    public function delete($id)
    {
        $this->articleModel->delete($id);
        return $this->response->setJSON(['success' => true, 'message' => 'Artikel dihapus.']);
    }

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
