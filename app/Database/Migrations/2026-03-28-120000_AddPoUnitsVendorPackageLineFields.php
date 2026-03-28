<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * PO unit line: vendor PI text, factory code, package flags, accessories, delivery grouping.
 */
class AddPoUnitsVendorPackageLineFields extends Migration
{
    public function up()
    {
        $fields = [
            'vendor_model_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'comment'    => 'Factory / vendor model code from PI',
                'after'      => 'model_unit_id',
            ],
            'vendor_spec_text' => [
                'type'    => 'MEDIUMTEXT',
                'null'    => true,
                'comment' => 'Verbatim vendor line specification (PI paste)',
                'after'   => 'vendor_model_code',
            ],
            'package_flags' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'JSON: fork_std, battery, charger, attachment, acc flags',
                'after'      => 'vendor_spec_text',
            ],
            'unit_accessories' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Expected accessories keys (quotation parity), comma or JSON',
                'after'   => 'package_flags',
            ],
            'po_line_group_id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'null'       => true,
                'comment'    => 'UUID grouping one PI line -> multiple po_units rows',
                'after'      => 'unit_accessories',
            ],
        ];

        foreach ($fields as $name => $def) {
            if ($this->db->fieldExists($name, 'po_units')) {
                continue;
            }
            $this->forge->addColumn('po_units', [$name => $def]);
        }
    }

    public function down()
    {
        foreach (['po_line_group_id', 'unit_accessories', 'package_flags', 'vendor_spec_text', 'vendor_model_code'] as $col) {
            if ($this->db->fieldExists($col, 'po_units')) {
                $this->forge->dropColumn('po_units', $col);
            }
        }
    }
}
