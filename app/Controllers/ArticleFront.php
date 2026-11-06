<?php

namespace App\Controllers;

use App\Models\ArticleModel;

class ArticleFront extends BaseController
{
    protected $articleModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }

    public function index()
    {
        return redirect()->to('/');
    }

    public function show($slug)
    {
        helper('text');

        $query = $this->articleModel->where('slug', $slug);

        // Pengunjung umum hanya bisa melihat artikel publish, admin yang login dapat mempratinjau draft
        $isLoggedIn = session()->get('id') || session()->get('id_user') || session()->get('role_id') || session()->get('logged_in');
        if (!$isLoggedIn) {
            $query->where('status', 'publish');
        }

        $article = $query->first();

        if (!$article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Artikel tidak ditemukan.");
        }

        $article['image_url'] = $article['image']
            ? base_url('uploads/articles/' . $article['image'])
            : base_url('assets/images/image_not_available.jpg');

        $article['published'] = date('d M Y H:i', strtotime($article['created_at']));
        $article['excerpt']   = character_limiter(strip_tags($article['description']), 160);

        return view('article_detail', [
            'article'    => $article,
            'titleApp'   => titleApp(),
            'version'    => versionApp(),
            'footerText' => footerText(),
        ]);
    }
}
