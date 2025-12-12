<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDefaultLanguageToSettings extends Migration
{
    public function up(): void
    {
        if (!$this->db->tableExists('site_global_settings')) {
            return;
        }

        $fields = array_map('strtolower', $this->db->getFieldNames('site_global_settings'));
        if (!in_array('default_language_id', $fields, true)) {
            $this->forge->addColumn('site_global_settings', [
                'default_language_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'site_base_url',
                ],
            ]);

            $this->db->query('ALTER TABLE `site_global_settings` ADD CONSTRAINT `sgs_default_language_fk` FOREIGN KEY (`default_language_id`) REFERENCES `languages`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('site_global_settings')) {
            $fields = array_map('strtolower', $this->db->getFieldNames('site_global_settings'));
            if (in_array('default_language_id', $fields, true)) {
                $this->db->query('ALTER TABLE `site_global_settings` DROP FOREIGN KEY `sgs_default_language_fk`');
                $this->forge->dropColumn('site_global_settings', 'default_language_id');
            }
        }
    }
}
