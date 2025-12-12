<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPostVariants extends Migration
{
    public function up(): void
    {
        if (!$this->db->tableExists('post_variants')) {
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
                'lang_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                ],
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'content' => [
                    'type' => 'TEXT',
                ],
                'seo_title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'seo_desc' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'seo_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('post_id');
            $this->forge->addKey('lang_id');
            $this->forge->addUniqueKey(['lang_id', 'seo_url'], 'lang_seo_unique');
            $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('lang_id', 'languages', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('post_variants');
        }

        if (!$this->db->tableExists('posts')) {
            return;
        }

        $postsTable = $this->db->table('posts');
        $postFields = array_map('strtolower', $this->db->getFieldNames('posts'));

        $requiredColumns = ['title', 'content', 'lang_id', 'seo_title', 'seo_desc', 'seo_url'];
        $hasLegacyColumns = !array_diff($requiredColumns, $postFields);

        if ($hasLegacyColumns) {
            $postData = $postsTable->select('id, title, content, lang_id, seo_title, seo_desc, seo_url')->get()->getResultArray();
            $variantTable = $this->db->table('post_variants');

            foreach ($postData as $post) {
                if (empty($post['lang_id'])) {
                    continue;
                }

                $variantTable->insert([
                    'post_id'  => $post['id'],
                    'lang_id'  => $post['lang_id'],
                    'title'    => $post['title'] ?? '',
                    'content'  => $post['content'] ?? '',
                    'seo_title'=> $post['seo_title'] ?? null,
                    'seo_desc' => $post['seo_desc'] ?? null,
                    'seo_url'  => $post['seo_url'] ?? null,
                ]);
            }
        }

        $columnsToDrop = ['title', 'content', 'lang_id', 'seo_title', 'seo_desc', 'seo_url'];
        foreach ($columnsToDrop as $column) {
            if (in_array($column, $postFields, true)) {
                $this->forge->dropColumn('posts', $column);
            }
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('posts')) {
            $postsFields = array_map('strtolower', $this->db->getFieldNames('posts'));

            $columnsToAdd = [];
            if (!in_array('title', $postsFields, true)) {
                $columnsToAdd['title'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ];
            }
            if (!in_array('content', $postsFields, true)) {
                $columnsToAdd['content'] = [
                    'type' => 'TEXT',
                    'null' => true,
                ];
            }
            if (!in_array('lang_id', $postsFields, true)) {
                $columnsToAdd['lang_id'] = [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                ];
            }
            if (!in_array('seo_title', $postsFields, true)) {
                $columnsToAdd['seo_title'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ];
            }
            if (!in_array('seo_desc', $postsFields, true)) {
                $columnsToAdd['seo_desc'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ];
            }
            if (!in_array('seo_url', $postsFields, true)) {
                $columnsToAdd['seo_url'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ];
            }

            if (!empty($columnsToAdd)) {
                $this->forge->addColumn('posts', $columnsToAdd);
            }

            if ($this->db->tableExists('post_variants')) {
                $variants = $this->db->table('post_variants')
                    ->select('post_id, lang_id, title, content, seo_title, seo_desc, seo_url')
                    ->orderBy('lang_id', 'ASC')
                    ->get()
                    ->getResultArray();

                $grouped = [];
                foreach ($variants as $variant) {
                    $postId = (int) $variant['post_id'];
                    if (!isset($grouped[$postId])) {
                        $grouped[$postId] = $variant;
                    }
                }

                $postsTable = $this->db->table('posts');
                foreach ($grouped as $postId => $variant) {
                    $postsTable->where('id', $postId)->update([
                        'title'    => $variant['title'],
                        'content'  => $variant['content'],
                        'lang_id'  => $variant['lang_id'],
                        'seo_title'=> $variant['seo_title'],
                        'seo_desc' => $variant['seo_desc'],
                        'seo_url'  => $variant['seo_url'],
                    ]);
                }
            }
        }

        if ($this->db->tableExists('post_variants')) {
            $this->forge->dropTable('post_variants');
        }
    }
}
