<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'image',
        'slug',
        'status',
        'author_id',
        'meta_description',
        'category_id',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = false;
}
