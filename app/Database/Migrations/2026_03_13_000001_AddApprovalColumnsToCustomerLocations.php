<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddApprovalColumnsToCustomerLocations extends Migration
{
    public function up()
    {
        // Add approval-related columns to customer_locations for Service Add Location requests
        $fields = [
            'approval_status' => [
                'type'       => 'ENUM',
                'constraint' => ['PENDING', 'APPROVED', 'REJECTED'],
                'null'       => true,
                'default'    => null,
                'after'      => 'notes',
            ],
            'requested_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'approval_status',
            ],
            'approved_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'requested_by',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after'=> 'approved_by',
            ],
            'approval_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after'=> 'approved_at',
            ],
        ];

        $this->forge->addColumn('customer_locations', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('customer_locations', ['approval_status', 'requested_by', 'approved_by', 'approved_at', 'approval_notes']);
    }
}
