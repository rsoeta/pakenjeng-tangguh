<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticlesModel extends Model
{
    protected $table = 'articles';
    protected $allowedFields = ['title', 'description', 'image', 'slug', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
