<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteSettingTranslationModel extends Model
{
    protected $table = 'site_settings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'lang_id',
        'site_title',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'meta_author',
        'footer_description',
    ];

    protected $useTimestamps = false;
}
