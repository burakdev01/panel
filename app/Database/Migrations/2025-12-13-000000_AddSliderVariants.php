<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSliderVariants extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('sliders')) {
            return;
        }

        if (! $this->db->tableExists('slider_variants')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'slider_id' => [
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
                'details' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'links' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('slider_id');
            $this->forge->addKey('lang_id');
            $this->forge->addForeignKey('slider_id', 'sliders', 'id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('lang_id', 'languages', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('slider_variants');
        }

        $sliderFields = array_map('strtolower', $this->db->getFieldNames('sliders'));
        $legacyColumns = ['title', 'details', 'links', 'lang_id'];
        $hasLegacyColumns = ! array_diff($legacyColumns, $sliderFields);

        if ($hasLegacyColumns) {
            $sliderData = $this->db->table('sliders')
                ->select('id, title, details, links, lang_id')
                ->get()
                ->getResultArray();

            $variantTable = $this->db->table('slider_variants');

            foreach ($sliderData as $slider) {
                if (empty($slider['lang_id'])) {
                    continue;
                }

                $variantTable->insert([
                    'slider_id' => $slider['id'],
                    'lang_id'   => $slider['lang_id'],
                    'title'     => $slider['title'] ?? '',
                    'details'   => $slider['details'] ?? '',
                    'links'     => $slider['links'] ?? null,
                ]);
            }
        }

        foreach ($legacyColumns as $column) {
            if (in_array($column, $sliderFields, true)) {
                $this->forge->dropColumn('sliders', $column);
            }
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('sliders')) {
            if ($this->db->tableExists('slider_variants')) {
                $this->forge->dropTable('slider_variants');
            }

            return;
        }

        $sliderFields = array_map('strtolower', $this->db->getFieldNames('sliders'));

        $columnsToAdd = [];

        if (! in_array('title', $sliderFields, true)) {
            $columnsToAdd['title'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ];
        }

        if (! in_array('details', $sliderFields, true)) {
            $columnsToAdd['details'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (! in_array('links', $sliderFields, true)) {
            $columnsToAdd['links'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ];
        }

        if (! in_array('lang_id', $sliderFields, true)) {
            $columnsToAdd['lang_id'] = [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ];
        }

        if (! empty($columnsToAdd)) {
            $this->forge->addColumn('sliders', $columnsToAdd);
        }

        if ($this->db->tableExists('slider_variants')) {
            $variants = $this->db->table('slider_variants')
                ->select('slider_id, lang_id, title, details, links')
                ->orderBy('lang_id', 'ASC')
                ->get()
                ->getResultArray();

            $grouped = [];
            foreach ($variants as $variant) {
                $sliderId = (int) $variant['slider_id'];
                if (! isset($grouped[$sliderId])) {
                    $grouped[$sliderId] = $variant;
                }
            }

            $sliderTable = $this->db->table('sliders');
            foreach ($grouped as $sliderId => $variant) {
                $sliderTable->where('id', $sliderId)->update([
                    'title'  => $variant['title'],
                    'details'=> $variant['details'],
                    'links'  => $variant['links'],
                    'lang_id'=> $variant['lang_id'],
                ]);
            }

            $this->forge->dropTable('slider_variants');
        }
    }
}
