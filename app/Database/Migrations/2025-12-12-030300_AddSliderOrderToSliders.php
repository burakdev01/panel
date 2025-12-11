<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSliderOrderToSliders extends Migration
{
    public function up(): void
    {
        $fields = $this->db->getFieldNames('sliders');

        if (!in_array('slider_order', $fields, true)) {
            $this->forge->addColumn('sliders', [
                'slider_order' => [
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
        $fields = $this->db->getFieldNames('sliders');
        if (in_array('slider_order', $fields, true)) {
            $this->forge->dropColumn('sliders', 'slider_order');
        }
    }
}
