<?php

namespace App\Models;

use CodeIgniter\Model;

class SliderModel extends Model
{
    protected $table = 'sliders';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'details',
        'links',
        'image',
        'lang_id',
        'active',
        'slider_order',
    ];

    protected $useTimestamps = false;
}
