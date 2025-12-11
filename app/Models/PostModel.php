<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'content',
        'image',
        'lang',
        'seo_title',
        'seo_desc',
        'seo_url',
        'active',
    ];

    protected $useTimestamps = false;
}
