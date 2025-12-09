<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArticleModel;

class ArticleController extends BaseController
{
    protected $articleModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }

    public function index()
    {
        return view('admin/articles/index', [
            'title' => 'Manajemen Artikel'
        ]);
    }

    /**
     * JSON for DataTables
     */
    public function data()
    {
        $articles = $this->articleModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $rows = [];
        $no = 0;

        foreach ($articles as $a) {
            $no++;

            $thumb = $a['image']
                ? base_url('uploads/articles/' . $a['image'])
                : base_url('assets/images/image_not_available.jpg');

            $rows[] = [
                'no'         => $no,
                'image'      => '<img src="' . $thumb . '" class="article-thumb">',
                'title'      => esc($a['title']),
                'status'     => $a['status'] == 'publish'
                    ? '<span class="badge bg-success">Publish</span>'
                    : '<span class="badge bg-secondary">Draft</span>',
                'created_at' => date('d M Y H:i', strtotime($a['created_at'])),
                'actions'    => '
                    <button class="btn btn-sm btn-primary btnEditArticle" data-id="' . $a['id'] . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btnDeleteArticle ms-1" data-id="' . $a['id'] . '">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                '
            ];
        }

        return $this->response->setJSON(['data' => $rows]);
    }

    public function get($id)
    {
        $row = $this->articleModel->find($id);

        if (! $row) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // Buat URL gambar full
        $row['image_url'] = $row['image']
            ? base_url('uploads/articles/' . $row['image'])
            : base_url('assets/images/image_not_available.jpg');

        return $this->response->setJSON([
            'success' => true,
            'data' => $row
        ]);
    }

    public function store()
    {
        helper('url'); // pastikan aktif

        $imageName = null;

        // upload image
        if ($file = $this->request->getFile('image')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $imageName = $file->getRandomName();
                $file->move('uploads/articles/', $imageName);
            }
        }

        $title = $this->request->getPost('title');
        $slug  = url_title($title, '-', true);

        // pastikan slug unik
        $exists = $this->articleModel->where('slug', $slug)->first();
        if ($exists) {
            $slug .= '-' . time(); // append timestamp supaya unik
        }

        $this->articleModel->insert([
            'title'       => $title,
            'slug'        => $slug,
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
            'image'       => $imageName,
            'author_id'   => session()->get('user_id'),
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/admin/articles')->with('message', 'Artikel berhasil dibuat.');
    }

    public function update($id)
    {
        helper('url');

        $article = $this->articleModel->find($id);
        if (!$article) {
            return $this->response->setJSON(['success' => false, 'message' => 'Artikel tidak ditemukan.']);
        }

        $title = $this->request->getPost('title');
        $slug  = $article['slug'];

        // jika judul berubah → update slug
        if ($title !== $article['title']) {
            $newSlug = url_title($title, '-', true);

            // jika slug baru bentrok dengan artikel lain → tambahkan sufiks
            $exists = $this->articleModel
                ->where('slug', $newSlug)
                ->where('id !=', $id)
                ->first();

            if ($exists) {
                $newSlug .= '-' . time();
            }

            $slug = $newSlug;
        }

        // ---------- HANDLE GAMBAR ----------
        $imageName = $article['image'];

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($imageName && file_exists(FCPATH . 'uploads/articles/' . $imageName)) {
                unlink(FCPATH . 'uploads/articles/' . $imageName);
            }
            $imageName = $file->getRandomName();
            $file->move('uploads/articles/', $imageName);
        }

        $this->articleModel->update($id, [
            'title'       => $title,
            'slug'        => $slug,
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status'),
            'image'       => $imageName,
            'updated_at'  => date('Y-m-d H:i:s')
        ]);

        return $this->response
            ->setHeader('X-CSRF-TOKEN', csrf_hash())
            ->setJSON(['success' => true, 'message' => 'Artikel berhasil diperbarui.']);
    }

    public function delete($id)
    {
        $this->articleModel->delete($id);

        return $this->response
            ->setHeader('X-CSRF-TOKEN', csrf_hash())
            ->setJSON([
                'success' => true,
                'message' => 'Artikel berhasil dihapus.'
            ]);
    }

    /**
     * Upload image khusus TinyMCE dengan kompresi otomatis
     */
    public function uploadImage()
    {
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => 'File tidak valid.']);
        }

        $newName = $file->getRandomName();
        $target = FCPATH . "uploads/articles/" . $newName;

        // Resize + compress (max width 1280px, quality 70%)
        \Config\Services::image()
            ->withFile($file)
            ->resize(1280, 1280, true, 'auto')
            ->save($target, 70);

        return $this->response->setJSON([
            'location' => base_url("uploads/articles/" . $newName)
        ]);
    }

    public function show($slug)
    {
        $article = $this->articleModel
            ->where('slug', $slug)
            ->where('status', 'publish')
            ->first();

        if (! $article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Artikel tidak ditemukan.");
        }

        $article['image_url'] = $article['image']
            ? base_url('uploads/articles/' . $article['image'])
            : base_url('assets/images/image_not_available.jpg');

        return view('public/articles/detail', ['article' => $article]);
    }
}
