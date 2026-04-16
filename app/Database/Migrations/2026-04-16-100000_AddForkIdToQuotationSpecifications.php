<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForkIdToQuotationSpecifications extends Migration
{
    public function up()
    {
        // Add fork_id column if it doesn't exist
        if (!$this->db->fieldExists('fork_id', 'quotation_specifications')) {
            $this->forge->addColumn('quotation_specifications', [
                'fork_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'attachment_id',
                ],
            ]);
        }

        // Also ensure specification_type enum includes 'FORK' value
        // Check current column type first to avoid duplicate alteration
        $fields = $this->db->getFieldData('quotation_specifications');
        foreach ($fields as $field) {
            if ($field->name === 'specification_type') {
                // Only alter if FORK is not yet in the type definition
                if (strpos((string)($field->type ?? ''), 'FORK') === false) {
                    $this->forge->modifyColumn('quotation_specifications', [
                        'specification_type' => [
                            'type'       => "ENUM('UNIT','ATTACHMENT','FORK')",
                            'null'       => false,
                            'default'    => 'UNIT',
                        ],
                    ]);
                }
                break;
            }
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('fork_id', 'quotation_specifications')) {
            $this->forge->dropColumn('quotation_specifications', 'fork_id');
        }
    }
}
