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

            $articleUrl = base_url('article/' . $a['slug']);

            $rows[] = [
                'no'         => $no,
                'image'      => '<img src="' . $thumb . '" class="article-thumb">',
                'title'      => esc($a['title']),
                'status'     => $a['status'] == 'publish'
                    ? '<span class="badge bg-success">Publish</span>'
                    : '<span class="badge bg-secondary">Draft</span>',
                'created_at' => date('d M Y H:i', strtotime($a['created_at'])),
                'actions'    => '
                    <div class="d-inline-flex text-nowrap">
                        <a href="' . $articleUrl . '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-info text-white" title="Lihat Artikel">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <button class="btn btn-sm btn-primary btnEditArticle ms-1" data-id="' . $a['id'] . '" title="Ubah Artikel">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btnDeleteArticle ms-1" data-id="' . $a['id'] . '" title="Hapus Artikel">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
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

        if ($this->request->isAJAX()) {
            return $this->response
                ->setHeader('X-CSRF-TOKEN', csrf_hash())
                ->setJSON([
                    'success'   => true,
                    'message'   => 'Artikel berhasil dibuat.',
                    'csrf_name' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                    'csrf'      => [
                        'name'  => csrf_token(),
                        'value' => csrf_hash()
                    ]
                ]);
        }

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

        $newHash = csrf_hash();

        return $this->response
            ->setHeader('X-CSRF-TOKEN', $newHash)
            ->setJSON([
                'success'   => true,
                'message'   => 'Artikel berhasil diperbarui.',
                'csrf_name' => csrf_token(),
                'csrf_hash' => $newHash,
            ]);
    }

    public function delete($id)
    {
        $this->articleModel->delete($id);

        $newHash = csrf_hash();

        return $this->response
            ->setHeader('X-CSRF-TOKEN', $newHash)
            ->setJSON([
                'success'   => true,
                'message'   => 'Artikel berhasil dihapus.',
                'csrf_name' => csrf_token(),
                'csrf_hash' => $newHash,
            ]);
    }

    /**
     * Upload image khusus TinyMCE dengan kompresi otomatis & fallback aman
     */
    public function uploadImage()
    {
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            $errorMsg = $file ? $file->getErrorString() : 'File gambar tidak ditemukan.';
            return $this->response
                ->setStatusCode(400)
                ->setHeader('X-CSRF-TOKEN', csrf_hash())
                ->setJSON([
                    'error'     => 'Upload gambar gagal: ' . $errorMsg,
                    'message'   => 'Upload gambar gagal: ' . $errorMsg,
                    'csrf_name' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                ]);
        }

        // Validasi ekstensi & mime type gambar yang aman
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        $mime = $file->getMimeType();
        if (!in_array($mime, $allowedMimes, true)) {
            return $this->response
                ->setStatusCode(400)
                ->setHeader('X-CSRF-TOKEN', csrf_hash())
                ->setJSON([
                    'error'     => 'Format file tidak didukung. Harap unggah JPG, PNG, GIF, WebP, atau SVG.',
                    'message'   => 'Format file tidak didukung. Harap unggah JPG, PNG, GIF, WebP, atau SVG.',
                    'csrf_name' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                ]);
        }

        $uploadDir = FCPATH . 'uploads/articles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = $file->guessExtension() ?: $file->getClientExtension() ?: 'jpg';
        $newName = $file->getRandomName();

        if (!pathinfo($newName, PATHINFO_EXTENSION)) {
            $newName .= '.' . $ext;
        }

        $target = $uploadDir . $newName;

        if ($mime === 'image/svg+xml') {
            $file->move($uploadDir, $newName);
        } else {
            $resized = false;
            try {
                // Resize + kompresi (maksimal 1280px, quality 75%)
                \Config\Services::image()
                    ->withFile($file->getTempName())
                    ->resize(1280, 1280, true, 'auto')
                    ->save($target, 75);
                $resized = true;
            } catch (\Throwable $e) {
                log_message('warning', '[TinyMCE Upload] Resize GD gagal (' . $e->getMessage() . '), fallback memindahkan file asli.');
            }

            if (!$resized || !file_exists($target)) {
                if (!$file->hasMoved()) {
                    $file->move($uploadDir, $newName);
                }
            }
        }

        $newHash = csrf_hash();

        return $this->response
            ->setHeader('X-CSRF-TOKEN', $newHash)
            ->setJSON([
                'location'  => base_url('uploads/articles/' . $newName),
                'csrf_name' => csrf_token(),
                'csrf_hash' => $newHash,
            ]);
    }

    public function create()
    {
        return redirect()->to('/admin/articles');
    }

    public function edit($id)
    {
        $article = $this->articleModel->find($id);
        if (!$article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Artikel tidak ditemukan.");
        }

        return view('admin/articles/edit', [
            'title'   => 'Ubah Artikel',
            'article' => $article
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
