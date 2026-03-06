<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUnitAuditAndMovementTables extends Migration
{
    public function up()
    {
        // ============ unit_audit_requests ============
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'reported_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'approved_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'recorded_location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'recorded_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'recorded_customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'recorded_customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'recorded_kontrak_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'actual_location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'actual_customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'actual_customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'actual_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'request_type' => [
                'type'       => 'ENUM',
                'constraint' => ['LOCATION_MISMATCH', 'STATUS_MISMATCH', 'DAMAGE_REPORT', 'OTHER'],
                'default'    => 'LOCATION_MISMATCH',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['PENDING', 'APPROVED', 'REJECTED', 'CANCELLED'],
                'default'    => 'PENDING',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'approval_notes' => [
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
        $this->forge->addKey('unit_id');
        $this->forge->addKey('status');
        $this->forge->addKey('request_type');
        $this->forge->addKey('reported_by_user_id');
        $this->forge->createTable('unit_audit_requests', true);

        // ============ unit_movements ============
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'movement_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'component_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'component_type' => [
                'type'       => 'ENUM',
                'constraint' => ['FORKLIFT', 'ATTACHMENT', 'CHARGER', 'BATTERY'],
                'default'    => 'FORKLIFT',
                'null'       => true,
            ],
            'origin_location' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'destination_location' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'origin_type' => [
                'type'       => 'ENUM',
                'constraint' => ['POS_1', 'POS_2', 'POS_3', 'POS_4', 'POS_5', 'CUSTOMER_SITE', 'WAREHOUSE', 'OTHER'],
            ],
            'destination_type' => [
                'type'       => 'ENUM',
                'constraint' => ['POS_1', 'POS_2', 'POS_3', 'POS_4', 'POS_5', 'CUSTOMER_SITE', 'WAREHOUSE', 'OTHER'],
            ],
            'movement_date' => [
                'type' => 'DATETIME',
            ],
            'driver_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'vehicle_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'surat_jalan_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['DRAFT', 'IN_TRANSIT', 'ARRIVED', 'CANCELLED'],
                'default'    => 'DRAFT',
            ],
            'created_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'confirmed_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'confirmed_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey('movement_number');
        $this->forge->addKey('unit_id');
        $this->forge->addKey('surat_jalan_number');
        $this->forge->addKey('status');
        $this->forge->addKey('origin_type');
        $this->forge->addKey('destination_type');
        $this->forge->addKey('created_by_user_id');
        $this->forge->createTable('unit_movements', true);
    }

    public function down()
    {
        $this->forge->dropTable('unit_audit_requests', true);
        $this->forge->dropTable('unit_movements', true);
    }
}
