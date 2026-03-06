<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RedesignUnitAuditRequests extends Migration
{
    public function up()
    {
        // Drop old table
        $this->forge->dropTable('unit_audit_requests', true);

        // Create new table with redesigned schema
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
            ],
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kontrak_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'request_type' => [
                'type'       => 'ENUM',
                'constraint' => ['LOCATION_MISMATCH', 'UNIT_SWAP', 'ADD_UNIT', 'MARK_SPARE', 'UNIT_MISSING', 'OTHER'],
                'default'    => 'LOCATION_MISMATCH',
            ],
            'current_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'proposed_data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'evidence_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED'],
                'default'    => 'SUBMITTED',
            ],
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
            'review_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
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
        $this->forge->addKey('customer_id');
        $this->forge->addKey('unit_id');
        $this->forge->addKey('status');
        $this->forge->addKey('submitted_by');
        $this->forge->createTable('unit_audit_requests', true);
    }

    public function down()
    {
        $this->forge->dropTable('unit_audit_requests', true);
    }
}
