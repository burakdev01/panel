<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPostOrderToPosts extends Migration
{
    public function up(): void
    {
        $fields = $this->db->getFieldNames('posts');

        if (in_array('order', $fields, true) && !in_array('post_order', $fields, true)) {
            $this->db->query('ALTER TABLE `posts` CHANGE `order` `post_order` INT UNSIGNED NOT NULL DEFAULT 0');
            return;
        }

        if (!in_array('post_order', $fields, true)) {
            $this->forge->addColumn('posts', [
                'post_order' => [
                    'type'       => 'INT',
                    'unsigned'   => true,
                    'default'    => 0,
                    'after'      => 'active',
                ],
            ]);
        }
    }

    public function down(): void
    {
        $fields = $this->db->getFieldNames('posts');

        if (in_array('post_order', $fields, true) && !in_array('order', $fields, true)) {
            $this->db->query('ALTER TABLE `posts` CHANGE `post_order` `order` INT UNSIGNED NOT NULL DEFAULT 0');
            return;
        }

        if (in_array('post_order', $fields, true)) {
            $this->forge->dropColumn('posts', 'post_order');
        }
    }
}
