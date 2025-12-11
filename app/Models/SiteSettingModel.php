<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteSettingModel extends Model
{
    protected $table = 'site_settings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'site_base_url',
        'google_analytics',
        'google_search_console',
        'smtp_host',
        'smtp_user',
        'smtp_password',
        'smtp_port',
    ];

    protected $useTimestamps = true;
}
