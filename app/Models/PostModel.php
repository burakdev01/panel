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
        'lang_id',
        'seo_title',
        'seo_desc',
        'seo_url',
        'active',
        'post_order',
    ];

    protected $useTimestamps = false;
}
