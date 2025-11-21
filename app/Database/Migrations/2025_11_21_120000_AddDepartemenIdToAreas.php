<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDepartemenIdToAreas extends Migration
{
    public function up()
    {
        // Check if column already exists
        $fields = $this->db->getFieldData('areas');
        $columnExists = false;
        foreach ($fields as $field) {
            if ($field->name === 'departemen_id') {
                $columnExists = true;
                break;
            }
        }
        
        if (!$columnExists) {
            $this->forge->addColumn('areas', [
                'departemen_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'comment' => 'FK to departemen table for filtering areas by department'
                ]
            ]);
            
            // Add foreign key constraint
            $this->forge->addForeignKey('departemen_id', 'departemen', 'id_departemen', 'CASCADE', 'SET NULL', 'fk_areas_departemen');
            
            // Add index for better query performance
            $this->forge->addKey('departemen_id', false, false, 'idx_areas_departemen');
        }
    }

    public function down()
    {
        // Remove foreign key first
        $this->forge->dropForeignKey('areas', 'fk_areas_departemen');
        
        // Remove column
        $this->forge->dropColumn('areas', 'departemen_id');
    }
}

