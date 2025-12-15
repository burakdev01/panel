<?php

namespace App\Models;

use CodeIgniter\Model;

class SliderVariantModel extends Model
{
    protected $table = 'slider_variants';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'slider_id',
        'lang_id',
        'title',
        'details',
        'links',
    ];

    protected $useTimestamps = false;
}
