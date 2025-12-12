<?php

namespace App\Models;

use CodeIgniter\Model;

class PostVariantModel extends Model
{
    protected $table = 'post_variants';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'post_id',
        'lang_id',
        'title',
        'content',
        'seo_title',
        'seo_desc',
        'seo_url',
    ];

    protected $useTimestamps = false;
}
