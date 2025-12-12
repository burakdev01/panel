<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPublishedAtToPosts extends Migration
{
    public function up(): void
    {
        if (!$this->db->tableExists('posts')) {
            return;
        }

        $fields = array_map('strtolower', $this->db->getFieldNames('posts'));
        if (!in_array('published_at', $fields, true)) {
            $this->forge->addColumn('posts', [
                'published_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'post_order',
                ],
            ]);
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('posts')) {
            $fields = array_map('strtolower', $this->db->getFieldNames('posts'));
            if (in_array('published_at', $fields, true)) {
                $this->forge->dropColumn('posts', 'published_at');
            }
        }
    }
}
