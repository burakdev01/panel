<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostComments extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'post_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'author_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'author_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'content' => [
                'type' => 'TEXT',
            ],
            'is_approved' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '0: pending, 1: approved',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('post_id');
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('post_comments');
    }

    public function down(): void
    {
        $this->forge->dropTable('post_comments');
    }
}
