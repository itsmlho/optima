<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUnitAuditLocationTables extends Migration
{
    public function up()
    {
        // ============ unit_audit_locations (Header) ============
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'audit_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'AUDLOC-YYYYMMDD-NNNN',
            ],
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'customer_location_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'The location being audited',
            ],
            'kontrak_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Contract covering this location',
            ],
            // Audit Scheduling
            'audit_date' => [
                'type' => 'DATE',
            ],
            'audit_completed_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'audited_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Mechanic who performed audit',
            ],
            // Status Workflow
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['DRAFT', 'PRINTED', 'IN_PROGRESS', 'RESULTS_ENTERED', 'PENDING_APPROVAL', 'APPROVED', 'REJECTED'],
                'default'    => 'DRAFT',
            ],
            // Contract Comparison Data (what Marketing sees)
            'kontrak_total_units' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'kontrak_spare_units' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'kontrak_has_operator' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            // Audit Findings Summary (from mechanic)
            'actual_total_units' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'actual_spare_units' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'actual_has_operator' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'has_discrepancy' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            // Pricing (filled by Marketing during approval)
            'unit_difference' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'price_per_unit' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'total_price_adjustment' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            // Notes
            'mechanic_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'service_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'marketing_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Review/Approval
            'submitted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'reviewed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            // Timestamps
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('audit_number');
        $this->forge->addKey(['customer_id', 'customer_location_id']);
        $this->forge->addKey('status');
        $this->forge->addKey('audit_date');
        $this->forge->createTable('unit_audit_locations', true);

        // ============ unit_audit_location_items (Detail) ============
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'audit_location_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kontrak_unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Link to kontrak_unit (if found in contract)',
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            // Expected Data (from kontrak)
            'expected_no_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'expected_serial' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'expected_merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'expected_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'expected_is_spare' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'expected_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            // Actual Data (from mechanic's findings)
            'actual_no_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'actual_serial' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'actual_merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'actual_model' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'actual_is_spare' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'actual_operator_present' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            // Comparison Result
            'result' => [
                'type'       => 'ENUM',
                'constraint' => ['MATCH', 'NO_UNIT_IN_KONTRAK', 'EXTRA_UNIT', 'MISMATCH_NO_UNIT', 'MISMATCH_SERIAL', 'MISMATCH_SPEC', 'MISMATCH_SPARE'],
                'default'    => 'MATCH',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Timestamps
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('audit_location_id');
        $this->forge->addKey('unit_id');
        $this->forge->addKey('result');

        // Add foreign key
        $this->forge->addForeignKey('audit_location_id', 'unit_audit_locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('unit_id', 'inventory_unit', 'id_inventory_unit', 'CASCADE', 'CASCADE');

        $this->forge->createTable('unit_audit_location_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('unit_audit_location_items', true);
        $this->forge->dropTable('unit_audit_locations', true);
    }
}
