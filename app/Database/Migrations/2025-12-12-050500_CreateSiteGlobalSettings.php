<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiteGlobalSettings extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'site_base_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'google_analytics' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'google_search_console' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'smtp_host' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'smtp_user' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'smtp_password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'smtp_port' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('site_global_settings');
    }

    public function down(): void
    {
        $this->forge->dropTable('site_global_settings');
    }
}
