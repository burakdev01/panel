<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteSettingTranslationModel extends Model
{
    protected $table = 'site_setting_translations';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'setting_id',
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
